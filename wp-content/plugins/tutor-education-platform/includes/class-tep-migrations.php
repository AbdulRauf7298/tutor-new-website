<?php

if (! defined('ABSPATH')) {
    exit;
}

final class TEP_Migrations
{
    private const MIGRATION_TABLE = 'edu_migrations';

    /**
     * @return array<string, string>
     */
    private static function get_migrations(): array
    {
        return [
            '001_initial' => 'migration_001_initial',
            '002_users' => 'migration_002_users',
            '003_enrollments' => 'migration_003_enrollments',
            '004_classes' => 'migration_004_classes',
            '005_google' => 'migration_005_google',
        ];
    }

    public static function run_pending_migrations(): void
    {
        self::ensure_migration_table();

        foreach (self::get_migrations() as $migration_key => $method) {
            if (self::is_migration_applied($migration_key) || ! method_exists(self::class, $method)) {
                continue;
            }

            self::{$method}();
            self::mark_migration_applied($migration_key);
        }
    }

    private static function ensure_migration_table(): void
    {
        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $table_name = $wpdb->prefix . self::MIGRATION_TABLE;
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE {$table_name} (
            migration_key varchar(64) NOT NULL,
            applied_at datetime NOT NULL,
            PRIMARY KEY  (migration_key)
        ) {$charset_collate};";

        dbDelta($sql);
    }

    private static function is_migration_applied(string $migration_key): bool
    {
        global $wpdb;

        $table_name = $wpdb->prefix . self::MIGRATION_TABLE;
        $query = $wpdb->prepare("SELECT migration_key FROM {$table_name} WHERE migration_key = %s LIMIT 1", $migration_key);
        $value = $wpdb->get_var($query);

        return is_string($value) && $value !== '';
    }

    private static function mark_migration_applied(string $migration_key): void
    {
        global $wpdb;

        $table_name = $wpdb->prefix . self::MIGRATION_TABLE;
        $wpdb->replace($table_name, [
            'migration_key' => $migration_key,
            'applied_at' => current_time('mysql', true),
        ], [
            '%s',
            '%s',
        ]);
    }

    private static function migration_001_initial(): void
    {
        self::create_tables([
            'edu_students' => "
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                user_id bigint(20) unsigned NOT NULL,
                external_student_code varchar(100) NOT NULL DEFAULT '',
                timezone varchar(100) NOT NULL DEFAULT 'UTC',
                status varchar(50) NOT NULL DEFAULT 'active',
                created_at datetime NOT NULL,
                updated_at datetime NOT NULL,
                PRIMARY KEY  (id),
                UNIQUE KEY user_id (user_id),
                KEY status (status)
            ",
            'edu_teachers' => "
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                user_id bigint(20) unsigned NOT NULL,
                bio longtext NULL,
                timezone varchar(100) NOT NULL DEFAULT 'UTC',
                status varchar(50) NOT NULL DEFAULT 'active',
                created_at datetime NOT NULL,
                updated_at datetime NOT NULL,
                PRIMARY KEY  (id),
                UNIQUE KEY user_id (user_id),
                KEY status (status)
            ",
            'edu_parents' => "
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                user_id bigint(20) unsigned NOT NULL,
                phone varchar(30) NOT NULL DEFAULT '',
                status varchar(50) NOT NULL DEFAULT 'active',
                created_at datetime NOT NULL,
                updated_at datetime NOT NULL,
                PRIMARY KEY  (id),
                UNIQUE KEY user_id (user_id),
                KEY status (status)
            ",
        ]);
    }

    private static function migration_002_users(): void
    {
        self::create_tables([
            'edu_parent_students' => "
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                parent_id bigint(20) unsigned NOT NULL,
                student_id bigint(20) unsigned NOT NULL,
                relationship_label varchar(100) NOT NULL DEFAULT '',
                created_at datetime NOT NULL,
                PRIMARY KEY  (id),
                UNIQUE KEY parent_student (parent_id, student_id),
                KEY student_id (student_id)
            ",
        ]);
    }

    private static function migration_003_enrollments(): void
    {
        self::create_tables([
            'edu_enrollments' => "
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                student_id bigint(20) unsigned NOT NULL,
                course_id bigint(20) unsigned NOT NULL,
                program_id bigint(20) unsigned NOT NULL DEFAULT 0,
                enrollment_status varchar(50) NOT NULL DEFAULT 'active',
                starts_on date NULL,
                ends_on date NULL,
                created_at datetime NOT NULL,
                updated_at datetime NOT NULL,
                PRIMARY KEY  (id),
                KEY student_id (student_id),
                KEY course_id (course_id),
                KEY enrollment_status (enrollment_status)
            ",
        ]);
    }

    private static function migration_004_classes(): void
    {
        self::create_tables([
            'edu_class_sessions' => "
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                session_post_id bigint(20) unsigned NOT NULL,
                teacher_id bigint(20) unsigned NOT NULL,
                start_utc datetime NOT NULL,
                end_utc datetime NOT NULL,
                timezone varchar(100) NOT NULL DEFAULT 'UTC',
                meet_url varchar(255) NOT NULL DEFAULT '',
                conceptboard_url varchar(255) NOT NULL DEFAULT '',
                session_status varchar(50) NOT NULL DEFAULT 'scheduled',
                created_at datetime NOT NULL,
                updated_at datetime NOT NULL,
                PRIMARY KEY  (id),
                UNIQUE KEY session_post_id (session_post_id),
                KEY teacher_id (teacher_id),
                KEY start_utc (start_utc),
                KEY session_status (session_status)
            ",
            'edu_class_students' => "
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                class_session_id bigint(20) unsigned NOT NULL,
                student_id bigint(20) unsigned NOT NULL,
                created_at datetime NOT NULL,
                PRIMARY KEY  (id),
                UNIQUE KEY class_student (class_session_id, student_id),
                KEY student_id (student_id)
            ",
            'edu_teacher_availability' => "
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                teacher_id bigint(20) unsigned NOT NULL,
                weekday tinyint(2) unsigned NOT NULL,
                start_time_utc time NOT NULL,
                end_time_utc time NOT NULL,
                timezone varchar(100) NOT NULL DEFAULT 'UTC',
                created_at datetime NOT NULL,
                updated_at datetime NOT NULL,
                PRIMARY KEY  (id),
                KEY teacher_id (teacher_id),
                KEY weekday (weekday)
            ",
        ]);
    }

    private static function migration_005_google(): void
    {
        self::create_tables([
            'edu_google_integrations' => "
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                user_id bigint(20) unsigned NOT NULL,
                provider_account_email varchar(190) NOT NULL DEFAULT '',
                refresh_token_hash varchar(255) NOT NULL DEFAULT '',
                token_expires_at datetime NULL,
                connected_at datetime NOT NULL,
                updated_at datetime NOT NULL,
                PRIMARY KEY  (id),
                UNIQUE KEY user_id (user_id)
            ",
            'edu_payment_links' => "
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                enrollment_id bigint(20) unsigned NOT NULL,
                order_id bigint(20) unsigned NOT NULL DEFAULT 0,
                payment_status varchar(50) NOT NULL DEFAULT 'pending',
                amount decimal(12,2) NOT NULL DEFAULT 0.00,
                currency varchar(10) NOT NULL DEFAULT 'USD',
                created_at datetime NOT NULL,
                updated_at datetime NOT NULL,
                PRIMARY KEY  (id),
                KEY enrollment_id (enrollment_id),
                KEY payment_status (payment_status)
            ",
        ]);
    }

    /**
     * @param array<string, string> $table_schemas
     */
    private static function create_tables(array $table_schemas): void
    {
        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset_collate = $wpdb->get_charset_collate();

        foreach ($table_schemas as $table_suffix => $schema) {
            $table_name = $wpdb->prefix . $table_suffix;
            $sql = "CREATE TABLE {$table_name} ({$schema}) {$charset_collate};";
            dbDelta($sql);
        }
    }
}
