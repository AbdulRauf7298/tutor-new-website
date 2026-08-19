<?php
/**
 * Plugin Name: Tutor Education Platform
 * Description: Core education platform plugin for live online classes, roles, scheduling metadata, and student dashboard widgets.
 * Version: 0.2.0
 * Author: Tutor New Website
 */

if (! defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'includes/class-tep-capability-map.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-tep-migrations.php';

final class TEP_Platform
{
    private const OPTION_GOOGLE_CONNECTED = 'tep_google_connected';

    public static function init(): void
    {
        register_activation_hook(__FILE__, [self::class, 'activate']);
        add_action('init', [self::class, 'register_post_types']);
        add_action('init', [self::class, 'register_roles']);
        add_action('add_meta_boxes', [self::class, 'register_meta_boxes']);
        add_action('save_post_tep_class_session', [self::class, 'save_class_session_meta']);
        add_action('admin_menu', [self::class, 'register_admin_menu']);
        add_action('admin_init', [self::class, 'register_settings']);
        add_shortcode('tep_student_dashboard_next_class', [self::class, 'render_next_class_shortcode']);
    }

    public static function activate(): void
    {
        self::register_roles();
        self::register_post_types();
        TEP_Migrations::run_pending_migrations();
        flush_rewrite_rules();
    }

    public static function register_roles(): void
    {
        TEP_Capability_Map::register_roles();
    }

    public static function register_post_types(): void
    {
        $shared_args = [
            'public' => true,
            'show_in_rest' => true,
            'supports' => ['title', 'editor', 'thumbnail'],
            'has_archive' => true,
        ];

        register_post_type('tep_subject', array_merge($shared_args, [
            'label' => 'Subjects',
            'menu_icon' => 'dashicons-book-alt',
        ]));

        register_post_type('tep_curriculum', array_merge($shared_args, [
            'label' => 'Curriculums',
            'menu_icon' => 'dashicons-welcome-learn-more',
        ]));

        register_post_type('tep_program', array_merge($shared_args, [
            'label' => 'Programs',
            'menu_icon' => 'dashicons-welcome-widgets-menus',
        ]));

        register_post_type('tep_comp_exam', array_merge($shared_args, [
            'label' => 'Competitive Exams',
            'menu_icon' => 'dashicons-clipboard',
        ]));

        register_post_type('tep_course', array_merge($shared_args, [
            'label' => 'Courses',
            'menu_icon' => 'dashicons-welcome-learn-more',
        ]));

        register_post_type('tep_class_session', [
            'label' => 'Class Sessions',
            'public' => false,
            'show_ui' => true,
            'show_in_rest' => true,
            'menu_icon' => 'dashicons-calendar-alt',
            'supports' => ['title', 'editor'],
            'capability_type' => 'post',
        ]);
    }

    public static function register_meta_boxes(): void
    {
        add_meta_box(
            'tep_class_session_details',
            'Class Session Details',
            [self::class, 'render_class_session_meta_box'],
            'tep_class_session',
            'normal',
            'high'
        );
    }

    public static function render_class_session_meta_box(WP_Post $post): void
    {
        wp_nonce_field('tep_save_session_meta', 'tep_session_meta_nonce');

        $fields = [
            'tep_course_id' => '',
            'tep_subject_id' => '',
            'tep_teacher_id' => '',
            'tep_student_ids' => '',
            'tep_session_start_utc' => '',
            'tep_session_end_utc' => '',
            'tep_session_timezone' => 'Asia/Karachi',
            'tep_google_calendar_event_id' => '',
            'tep_google_meet_space_id' => '',
            'tep_google_meet_url' => '',
            'tep_conceptboard_url' => '',
            'tep_session_status' => 'scheduled',
        ];

        foreach ($fields as $key => $default) {
            $fields[$key] = (string) get_post_meta($post->ID, $key, true);
            if ($fields[$key] === '') {
                $fields[$key] = $default;
            }
        }

        ?>
        <p><label for="tep_course_id"><strong>Course ID</strong></label><br><input type="number" id="tep_course_id" name="tep_course_id" value="<?php echo esc_attr($fields['tep_course_id']); ?>" class="widefat"></p>
        <p><label for="tep_subject_id"><strong>Subject ID</strong></label><br><input type="number" id="tep_subject_id" name="tep_subject_id" value="<?php echo esc_attr($fields['tep_subject_id']); ?>" class="widefat"></p>
        <p><label for="tep_teacher_id"><strong>Teacher User ID</strong></label><br><input type="number" id="tep_teacher_id" name="tep_teacher_id" value="<?php echo esc_attr($fields['tep_teacher_id']); ?>" class="widefat"></p>
        <p><label for="tep_student_ids"><strong>Student User IDs (comma separated)</strong></label><br><input type="text" id="tep_student_ids" name="tep_student_ids" value="<?php echo esc_attr($fields['tep_student_ids']); ?>" class="widefat"></p>
        <p><label for="tep_session_start_utc"><strong>Start (UTC, e.g. 2026-08-25 14:00:00)</strong></label><br><input type="text" id="tep_session_start_utc" name="tep_session_start_utc" value="<?php echo esc_attr($fields['tep_session_start_utc']); ?>" class="widefat"></p>
        <p><label for="tep_session_end_utc"><strong>End (UTC)</strong></label><br><input type="text" id="tep_session_end_utc" name="tep_session_end_utc" value="<?php echo esc_attr($fields['tep_session_end_utc']); ?>" class="widefat"></p>
        <p><label for="tep_session_timezone"><strong>Session Timezone</strong></label><br><input type="text" id="tep_session_timezone" name="tep_session_timezone" value="<?php echo esc_attr($fields['tep_session_timezone']); ?>" class="widefat"></p>
        <p><label for="tep_google_calendar_event_id"><strong>Google Calendar Event ID</strong></label><br><input type="text" id="tep_google_calendar_event_id" name="tep_google_calendar_event_id" value="<?php echo esc_attr($fields['tep_google_calendar_event_id']); ?>" class="widefat"></p>
        <p><label for="tep_google_meet_space_id"><strong>Google Meet Space ID</strong></label><br><input type="text" id="tep_google_meet_space_id" name="tep_google_meet_space_id" value="<?php echo esc_attr($fields['tep_google_meet_space_id']); ?>" class="widefat"></p>
        <p><label for="tep_google_meet_url"><strong>Google Meet URL</strong></label><br><input type="url" id="tep_google_meet_url" name="tep_google_meet_url" value="<?php echo esc_attr($fields['tep_google_meet_url']); ?>" class="widefat"></p>
        <p><label for="tep_conceptboard_url"><strong>Conceptboard URL</strong></label><br><input type="url" id="tep_conceptboard_url" name="tep_conceptboard_url" value="<?php echo esc_attr($fields['tep_conceptboard_url']); ?>" class="widefat"></p>
        <p><label for="tep_session_status"><strong>Status</strong></label><br>
            <select id="tep_session_status" name="tep_session_status" class="widefat">
                <?php foreach (['scheduled', 'meeting_creation_failed', 'cancelled', 'completed'] as $status): ?>
                    <option value="<?php echo esc_attr($status); ?>" <?php selected($fields['tep_session_status'], $status); ?>><?php echo esc_html(ucwords(str_replace('_', ' ', $status))); ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <?php
    }

    public static function save_class_session_meta(int $post_id): void
    {
        if (! isset($_POST['tep_session_meta_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['tep_session_meta_nonce'])), 'tep_save_session_meta')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (! current_user_can('edit_post', $post_id)) {
            return;
        }

        $text_fields = [
            'tep_course_id',
            'tep_subject_id',
            'tep_teacher_id',
            'tep_student_ids',
            'tep_session_start_utc',
            'tep_session_end_utc',
            'tep_session_timezone',
            'tep_google_calendar_event_id',
            'tep_google_meet_space_id',
            'tep_session_status',
        ];

        foreach ($text_fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $field, sanitize_text_field(wp_unslash($_POST[$field])));
            }
        }

        if (isset($_POST['tep_google_meet_url'])) {
            update_post_meta($post_id, 'tep_google_meet_url', esc_url_raw(wp_unslash($_POST['tep_google_meet_url'])));
        }

        if (isset($_POST['tep_conceptboard_url'])) {
            update_post_meta($post_id, 'tep_conceptboard_url', esc_url_raw(wp_unslash($_POST['tep_conceptboard_url'])));
        }

        self::validate_session_status($post_id);
    }

    private static function validate_session_status(int $post_id): void
    {
        $status = (string) get_post_meta($post_id, 'tep_session_status', true);
        $meet_url = (string) get_post_meta($post_id, 'tep_google_meet_url', true);

        if ($status === 'scheduled' && $meet_url === '') {
            update_post_meta($post_id, 'tep_session_status', 'meeting_creation_failed');
        }
    }

    public static function register_admin_menu(): void
    {
        add_menu_page(
            'Education Platform',
            'Education Platform',
            'manage_tep_platform',
            'tep-platform-settings',
            [self::class, 'render_platform_settings_page'],
            'dashicons-welcome-learn-more'
        );
    }

    public static function register_settings(): void
    {
        register_setting('tep_platform_settings', self::OPTION_GOOGLE_CONNECTED, [
            'type' => 'boolean',
            'sanitize_callback' => static fn ($value) => (bool) $value,
            'default' => false,
        ]);
    }

    public static function render_platform_settings_page(): void
    {
        if (! current_user_can('manage_tep_platform')) {
            wp_die(esc_html__('You do not have permission to access this page.', 'tep'));
        }

        $connected = (bool) get_option(self::OPTION_GOOGLE_CONNECTED, false);
        ?>
        <div class="wrap">
            <h1>Google Integration</h1>
            <p><strong>Status:</strong> <?php echo esc_html($connected ? 'Connected' : 'Not Connected'); ?></p>
            <form method="post" action="options.php">
                <?php settings_fields('tep_platform_settings'); ?>
                <input type="hidden" name="<?php echo esc_attr(self::OPTION_GOOGLE_CONNECTED); ?>" value="<?php echo esc_attr($connected ? '0' : '1'); ?>">
                <?php submit_button($connected ? 'Disconnect Google' : 'Connect Google', 'primary'); ?>
            </form>
            <p>Use this as a secure status toggle. Production OAuth credentials and tokens must be stored server-side only.</p>
        </div>
        <?php
    }

    public static function render_next_class_shortcode(): string
    {
        if (! is_user_logged_in()) {
            return '<p>Please log in to view your next live class.</p>';
        }

        $user_id = get_current_user_id();
        $query = new WP_Query([
            'post_type' => 'tep_class_session',
            'post_status' => 'publish',
            'posts_per_page' => 30,
            'meta_key' => 'tep_session_start_utc',
            'orderby' => 'meta_value',
            'order' => 'ASC',
            'meta_query' => [
                [
                    'key' => 'tep_session_start_utc',
                    'value' => gmdate('Y-m-d H:i:s'),
                    'compare' => '>=',
                    'type' => 'DATETIME',
                ],
            ],
        ]);

        $next_post = null;
        foreach ($query->posts as $post) {
            $student_ids = (string) get_post_meta($post->ID, 'tep_student_ids', true);
            $normalized = array_filter(array_map('absint', array_map('trim', explode(',', $student_ids))));
            if (in_array($user_id, $normalized, true)) {
                $next_post = $post;
                break;
            }
        }

        if (! $next_post instanceof WP_Post) {
            return '<p>No upcoming live class found.</p>';
        }

        $start_utc = (string) get_post_meta($next_post->ID, 'tep_session_start_utc', true);
        $end_utc = (string) get_post_meta($next_post->ID, 'tep_session_end_utc', true);
        $timezone = get_user_meta($user_id, 'timezone_string', true);
        if (! is_string($timezone) || $timezone === '') {
            $timezone = wp_timezone_string() ?: 'UTC';
        }

        $start_display = self::format_utc_for_timezone($start_utc, $timezone);
        $end_display = self::format_utc_for_timezone($end_utc, $timezone);

        $meet_url = esc_url((string) get_post_meta($next_post->ID, 'tep_google_meet_url', true));
        $board_url = esc_url((string) get_post_meta($next_post->ID, 'tep_conceptboard_url', true));

        ob_start();
        ?>
        <div class="tep-next-live-class" style="border:1px solid #ddd;padding:16px;border-radius:10px;max-width:520px;">
            <h3 style="margin-top:0;">Next Live Class</h3>
            <p><strong><?php echo esc_html(get_the_title($next_post)); ?></strong></p>
            <p><strong>Starts:</strong> <?php echo esc_html($start_display); ?></p>
            <p><strong>Ends:</strong> <?php echo esc_html($end_display); ?></p>
            <p><strong>Timezone:</strong> <?php echo esc_html($timezone); ?></p>
            <?php if ($meet_url !== ''): ?>
                <p><a href="<?php echo $meet_url; ?>" class="button button-primary" target="_blank" rel="noopener noreferrer">Join Live Class</a></p>
            <?php else: ?>
                <p><em>Google Meet could not be created. Please contact support.</em></p>
            <?php endif; ?>
            <?php if ($board_url !== ''): ?>
                <p><a href="<?php echo $board_url; ?>" class="button" target="_blank" rel="noopener noreferrer">Open Conceptboard</a></p>
            <?php endif; ?>
        </div>
        <?php
        return (string) ob_get_clean();
    }

    private static function format_utc_for_timezone(string $utc_datetime, string $timezone): string
    {
        if ($utc_datetime === '') {
            return 'N/A';
        }

        try {
            $utc = new DateTime($utc_datetime, new DateTimeZone('UTC'));
            $utc->setTimezone(new DateTimeZone($timezone));

            return $utc->format('d M Y, h:i A');
        } catch (Exception $exception) {
            return 'N/A';
        }
    }
}

TEP_Platform::init();
