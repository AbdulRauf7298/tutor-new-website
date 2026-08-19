<?php

if (! defined('ABSPATH')) {
    exit;
}

final class TEP_People_Enrollments_Admin
{
    private const CAPABILITY = 'manage_tep_platform';

    public static function register_submenus(): void
    {
        add_submenu_page('tep-platform-settings', 'Students', 'Students', self::CAPABILITY, 'tep-platform-students', [self::class, 'render_students_page']);
        add_submenu_page('tep-platform-settings', 'Teachers', 'Teachers', self::CAPABILITY, 'tep-platform-teachers', [self::class, 'render_teachers_page']);
        add_submenu_page('tep-platform-settings', 'Parents', 'Parents', self::CAPABILITY, 'tep-platform-parents', [self::class, 'render_parents_page']);
        add_submenu_page('tep-platform-settings', 'Enrollments', 'Enrollments', self::CAPABILITY, 'tep-platform-enrollments', [self::class, 'render_enrollments_page']);
    }

    public static function render_students_page(): void
    {
        self::assert_access();
        self::handle_students_actions();

        global $wpdb;
        $table = $wpdb->prefix . 'edu_students';

        $edit_id = isset($_GET['edit_id']) ? absint($_GET['edit_id']) : 0;
        $defaults = ['id' => 0, 'user_id' => '', 'external_student_code' => '', 'timezone' => 'UTC', 'status' => 'active'];
        $editing = $defaults;
        if ($edit_id > 0) {
            $record = $wpdb->get_row($wpdb->prepare("SELECT id, user_id, external_student_code, timezone, status FROM {$table} WHERE id = %d", $edit_id), ARRAY_A);
            if (is_array($record)) {
                $editing = array_merge($editing, $record);
            }
        }

        $items = $wpdb->get_results("SELECT id, user_id, external_student_code, timezone, status, created_at, updated_at FROM {$table} ORDER BY id DESC LIMIT 100", ARRAY_A);
        ?>
        <div class="wrap">
            <h1>Students</h1>
            <?php settings_errors('tep_phase2'); ?>
            <form method="post">
                <?php wp_nonce_field('tep_students_save', 'tep_students_nonce'); ?>
                <input type="hidden" name="tep_phase2_entity" value="students">
                <input type="hidden" name="tep_record_id" value="<?php echo esc_attr((string) $editing['id']); ?>">
                <table class="form-table" role="presentation">
                    <tr><th><label for="tep_user_id">User ID</label></th><td><input id="tep_user_id" name="tep_user_id" type="number" min="1" value="<?php echo esc_attr((string) $editing['user_id']); ?>" required></td></tr>
                    <tr><th><label for="tep_external_student_code">External Student Code</label></th><td><input id="tep_external_student_code" name="tep_external_student_code" type="text" value="<?php echo esc_attr((string) $editing['external_student_code']); ?>"></td></tr>
                    <tr><th><label for="tep_timezone">Timezone</label></th><td><input id="tep_timezone" name="tep_timezone" type="text" value="<?php echo esc_attr((string) $editing['timezone']); ?>"></td></tr>
                    <tr><th><label for="tep_status">Status</label></th><td><select id="tep_status" name="tep_status"><option value="active" <?php selected($editing['status'], 'active'); ?>>Active</option><option value="inactive" <?php selected($editing['status'], 'inactive'); ?>>Inactive</option></select></td></tr>
                </table>
                <?php submit_button($editing['id'] ? 'Update Student' : 'Add Student'); ?>
            </form>

            <h2>Student Profiles</h2>
            <table class="widefat striped">
                <thead><tr><th>ID</th><th>User ID</th><th>Code</th><th>Timezone</th><th>Status</th><th>Updated</th><th>Actions</th></tr></thead>
                <tbody>
                <?php if (is_array($items) && $items !== []): ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo esc_html((string) $item['id']); ?></td>
                            <td><?php echo esc_html((string) $item['user_id']); ?></td>
                            <td><?php echo esc_html((string) $item['external_student_code']); ?></td>
                            <td><?php echo esc_html((string) $item['timezone']); ?></td>
                            <td><?php echo esc_html((string) $item['status']); ?></td>
                            <td><?php echo esc_html((string) $item['updated_at']); ?></td>
                            <td>
                                <a href="<?php echo esc_url(add_query_arg(['page' => 'tep-platform-students', 'edit_id' => (int) $item['id']], admin_url('admin.php'))); ?>">Edit</a> |
                                <a href="<?php echo esc_url(wp_nonce_url(add_query_arg(['page' => 'tep-platform-students', 'tep_action' => 'delete', 'id' => (int) $item['id']], admin_url('admin.php')), 'tep_students_delete_' . (int) $item['id'])); ?>">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7">No student profiles found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    public static function render_teachers_page(): void
    {
        self::assert_access();
        self::handle_teachers_actions();

        global $wpdb;
        $table = $wpdb->prefix . 'edu_teachers';

        $edit_id = isset($_GET['edit_id']) ? absint($_GET['edit_id']) : 0;
        $defaults = ['id' => 0, 'user_id' => '', 'bio' => '', 'timezone' => 'UTC', 'status' => 'active'];
        $editing = $defaults;
        if ($edit_id > 0) {
            $record = $wpdb->get_row($wpdb->prepare("SELECT id, user_id, bio, timezone, status FROM {$table} WHERE id = %d", $edit_id), ARRAY_A);
            if (is_array($record)) {
                $editing = array_merge($editing, $record);
            }
        }

        $items = $wpdb->get_results("SELECT id, user_id, bio, timezone, status, created_at, updated_at FROM {$table} ORDER BY id DESC LIMIT 100", ARRAY_A);
        ?>
        <div class="wrap">
            <h1>Teachers</h1>
            <?php settings_errors('tep_phase2'); ?>
            <form method="post">
                <?php wp_nonce_field('tep_teachers_save', 'tep_teachers_nonce'); ?>
                <input type="hidden" name="tep_phase2_entity" value="teachers">
                <input type="hidden" name="tep_record_id" value="<?php echo esc_attr((string) $editing['id']); ?>">
                <table class="form-table" role="presentation">
                    <tr><th><label for="tep_teacher_user_id">User ID</label></th><td><input id="tep_teacher_user_id" name="tep_user_id" type="number" min="1" value="<?php echo esc_attr((string) $editing['user_id']); ?>" required></td></tr>
                    <tr><th><label for="tep_teacher_bio">Bio</label></th><td><textarea id="tep_teacher_bio" name="tep_bio" rows="4" cols="50"><?php echo esc_textarea((string) $editing['bio']); ?></textarea></td></tr>
                    <tr><th><label for="tep_teacher_timezone">Timezone</label></th><td><input id="tep_teacher_timezone" name="tep_timezone" type="text" value="<?php echo esc_attr((string) $editing['timezone']); ?>"></td></tr>
                    <tr><th><label for="tep_teacher_status">Status</label></th><td><select id="tep_teacher_status" name="tep_status"><option value="active" <?php selected($editing['status'], 'active'); ?>>Active</option><option value="inactive" <?php selected($editing['status'], 'inactive'); ?>>Inactive</option></select></td></tr>
                </table>
                <?php submit_button($editing['id'] ? 'Update Teacher' : 'Add Teacher'); ?>
            </form>

            <h2>Teacher Profiles</h2>
            <table class="widefat striped">
                <thead><tr><th>ID</th><th>User ID</th><th>Timezone</th><th>Status</th><th>Updated</th><th>Actions</th></tr></thead>
                <tbody>
                <?php if (is_array($items) && $items !== []): ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo esc_html((string) $item['id']); ?></td>
                            <td><?php echo esc_html((string) $item['user_id']); ?></td>
                            <td><?php echo esc_html((string) $item['timezone']); ?></td>
                            <td><?php echo esc_html((string) $item['status']); ?></td>
                            <td><?php echo esc_html((string) $item['updated_at']); ?></td>
                            <td>
                                <a href="<?php echo esc_url(add_query_arg(['page' => 'tep-platform-teachers', 'edit_id' => (int) $item['id']], admin_url('admin.php'))); ?>">Edit</a> |
                                <a href="<?php echo esc_url(wp_nonce_url(add_query_arg(['page' => 'tep-platform-teachers', 'tep_action' => 'delete', 'id' => (int) $item['id']], admin_url('admin.php')), 'tep_teachers_delete_' . (int) $item['id'])); ?>">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6">No teacher profiles found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    public static function render_parents_page(): void
    {
        self::assert_access();
        self::handle_parents_actions();

        global $wpdb;
        $table = $wpdb->prefix . 'edu_parents';

        $edit_id = isset($_GET['edit_id']) ? absint($_GET['edit_id']) : 0;
        $defaults = ['id' => 0, 'user_id' => '', 'phone' => '', 'status' => 'active'];
        $editing = $defaults;
        if ($edit_id > 0) {
            $record = $wpdb->get_row($wpdb->prepare("SELECT id, user_id, phone, status FROM {$table} WHERE id = %d", $edit_id), ARRAY_A);
            if (is_array($record)) {
                $editing = array_merge($editing, $record);
            }
        }

        $items = $wpdb->get_results("SELECT id, user_id, phone, status, created_at, updated_at FROM {$table} ORDER BY id DESC LIMIT 100", ARRAY_A);
        ?>
        <div class="wrap">
            <h1>Parents</h1>
            <?php settings_errors('tep_phase2'); ?>
            <form method="post">
                <?php wp_nonce_field('tep_parents_save', 'tep_parents_nonce'); ?>
                <input type="hidden" name="tep_phase2_entity" value="parents">
                <input type="hidden" name="tep_record_id" value="<?php echo esc_attr((string) $editing['id']); ?>">
                <table class="form-table" role="presentation">
                    <tr><th><label for="tep_parent_user_id">User ID</label></th><td><input id="tep_parent_user_id" name="tep_user_id" type="number" min="1" value="<?php echo esc_attr((string) $editing['user_id']); ?>" required></td></tr>
                    <tr><th><label for="tep_parent_phone">Phone</label></th><td><input id="tep_parent_phone" name="tep_phone" type="text" value="<?php echo esc_attr((string) $editing['phone']); ?>"></td></tr>
                    <tr><th><label for="tep_parent_status">Status</label></th><td><select id="tep_parent_status" name="tep_status"><option value="active" <?php selected($editing['status'], 'active'); ?>>Active</option><option value="inactive" <?php selected($editing['status'], 'inactive'); ?>>Inactive</option></select></td></tr>
                </table>
                <?php submit_button($editing['id'] ? 'Update Parent' : 'Add Parent'); ?>
            </form>

            <h2>Parent Profiles</h2>
            <table class="widefat striped">
                <thead><tr><th>ID</th><th>User ID</th><th>Phone</th><th>Status</th><th>Updated</th><th>Actions</th></tr></thead>
                <tbody>
                <?php if (is_array($items) && $items !== []): ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo esc_html((string) $item['id']); ?></td>
                            <td><?php echo esc_html((string) $item['user_id']); ?></td>
                            <td><?php echo esc_html((string) $item['phone']); ?></td>
                            <td><?php echo esc_html((string) $item['status']); ?></td>
                            <td><?php echo esc_html((string) $item['updated_at']); ?></td>
                            <td>
                                <a href="<?php echo esc_url(add_query_arg(['page' => 'tep-platform-parents', 'edit_id' => (int) $item['id']], admin_url('admin.php'))); ?>">Edit</a> |
                                <a href="<?php echo esc_url(wp_nonce_url(add_query_arg(['page' => 'tep-platform-parents', 'tep_action' => 'delete', 'id' => (int) $item['id']], admin_url('admin.php')), 'tep_parents_delete_' . (int) $item['id'])); ?>">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6">No parent profiles found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    public static function render_enrollments_page(): void
    {
        self::assert_access();
        self::handle_enrollments_actions();

        global $wpdb;
        $table = $wpdb->prefix . 'edu_enrollments';

        $edit_id = isset($_GET['edit_id']) ? absint($_GET['edit_id']) : 0;
        $defaults = [
            'id' => 0,
            'student_id' => '',
            'course_id' => '',
            'program_id' => '0',
            'enrollment_status' => 'active',
            'starts_on' => '',
            'ends_on' => '',
        ];
        $editing = $defaults;
        if ($edit_id > 0) {
            $record = $wpdb->get_row($wpdb->prepare("SELECT id, student_id, course_id, program_id, enrollment_status, starts_on, ends_on FROM {$table} WHERE id = %d", $edit_id), ARRAY_A);
            if (is_array($record)) {
                $editing = array_merge($editing, $record);
            }
        }

        $items = $wpdb->get_results("SELECT id, student_id, course_id, program_id, enrollment_status, starts_on, ends_on, updated_at FROM {$table} ORDER BY id DESC LIMIT 100", ARRAY_A);
        ?>
        <div class="wrap">
            <h1>Enrollments</h1>
            <?php settings_errors('tep_phase2'); ?>
            <form method="post">
                <?php wp_nonce_field('tep_enrollments_save', 'tep_enrollments_nonce'); ?>
                <input type="hidden" name="tep_phase2_entity" value="enrollments">
                <input type="hidden" name="tep_record_id" value="<?php echo esc_attr((string) $editing['id']); ?>">
                <table class="form-table" role="presentation">
                    <tr><th><label for="tep_student_id">Student ID</label></th><td><input id="tep_student_id" name="tep_student_id" type="number" min="1" value="<?php echo esc_attr((string) $editing['student_id']); ?>" required></td></tr>
                    <tr><th><label for="tep_course_id">Course ID</label></th><td><input id="tep_course_id" name="tep_course_id" type="number" min="1" value="<?php echo esc_attr((string) $editing['course_id']); ?>" required></td></tr>
                    <tr><th><label for="tep_program_id">Program ID</label></th><td><input id="tep_program_id" name="tep_program_id" type="number" min="0" value="<?php echo esc_attr((string) $editing['program_id']); ?>"></td></tr>
                    <tr><th><label for="tep_enrollment_status">Status</label></th><td><select id="tep_enrollment_status" name="tep_enrollment_status"><option value="active" <?php selected($editing['enrollment_status'], 'active'); ?>>Active</option><option value="paused" <?php selected($editing['enrollment_status'], 'paused'); ?>>Paused</option><option value="completed" <?php selected($editing['enrollment_status'], 'completed'); ?>>Completed</option><option value="cancelled" <?php selected($editing['enrollment_status'], 'cancelled'); ?>>Cancelled</option></select></td></tr>
                    <tr><th><label for="tep_starts_on">Starts On</label></th><td><input id="tep_starts_on" name="tep_starts_on" type="date" value="<?php echo esc_attr((string) $editing['starts_on']); ?>"></td></tr>
                    <tr><th><label for="tep_ends_on">Ends On</label></th><td><input id="tep_ends_on" name="tep_ends_on" type="date" value="<?php echo esc_attr((string) $editing['ends_on']); ?>"></td></tr>
                </table>
                <?php submit_button($editing['id'] ? 'Update Enrollment' : 'Add Enrollment'); ?>
            </form>

            <h2>Enrollment Records</h2>
            <table class="widefat striped">
                <thead><tr><th>ID</th><th>Student</th><th>Course</th><th>Program</th><th>Status</th><th>Starts</th><th>Ends</th><th>Updated</th><th>Actions</th></tr></thead>
                <tbody>
                <?php if (is_array($items) && $items !== []): ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo esc_html((string) $item['id']); ?></td>
                            <td><?php echo esc_html((string) $item['student_id']); ?></td>
                            <td><?php echo esc_html((string) $item['course_id']); ?></td>
                            <td><?php echo esc_html((string) $item['program_id']); ?></td>
                            <td><?php echo esc_html((string) $item['enrollment_status']); ?></td>
                            <td><?php echo esc_html((string) $item['starts_on']); ?></td>
                            <td><?php echo esc_html((string) $item['ends_on']); ?></td>
                            <td><?php echo esc_html((string) $item['updated_at']); ?></td>
                            <td>
                                <a href="<?php echo esc_url(add_query_arg(['page' => 'tep-platform-enrollments', 'edit_id' => (int) $item['id']], admin_url('admin.php'))); ?>">Edit</a> |
                                <a href="<?php echo esc_url(wp_nonce_url(add_query_arg(['page' => 'tep-platform-enrollments', 'tep_action' => 'delete', 'id' => (int) $item['id']], admin_url('admin.php')), 'tep_enrollments_delete_' . (int) $item['id'])); ?>">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="9">No enrollment records found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    private static function handle_students_actions(): void
    {
        global $wpdb;
        $table = $wpdb->prefix . 'edu_students';

        if (isset($_GET['tep_action'], $_GET['id']) && $_GET['tep_action'] === 'delete' && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'] ?? '')), 'tep_students_delete_' . absint($_GET['id']))) {
            $wpdb->delete($table, ['id' => absint($_GET['id'])], ['%d']);
            self::redirect_with_notice('tep-platform-students', 'Student profile deleted.');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ! isset($_POST['tep_phase2_entity']) || $_POST['tep_phase2_entity'] !== 'students') {
            return;
        }

        check_admin_referer('tep_students_save', 'tep_students_nonce');

        $record_id = absint($_POST['tep_record_id'] ?? 0);
        $data = [
            'user_id' => absint($_POST['tep_user_id'] ?? 0),
            'external_student_code' => sanitize_text_field(wp_unslash($_POST['tep_external_student_code'] ?? '')),
            'timezone' => sanitize_text_field(wp_unslash($_POST['tep_timezone'] ?? 'UTC')),
            'status' => self::sanitize_choice((string) ($_POST['tep_status'] ?? 'active'), ['active', 'inactive'], 'active'),
            'updated_at' => current_time('mysql', true),
        ];

        if ($data['user_id'] < 1) {
            add_settings_error('tep_phase2', 'tep_students_invalid_user', 'A valid User ID is required.', 'error');
            return;
        }

        if ($record_id > 0) {
            $updated = $wpdb->update($table, $data, ['id' => $record_id], ['%d', '%s', '%s', '%s', '%s'], ['%d']);
            if ($updated === false) {
                add_settings_error('tep_phase2', 'tep_students_update_failed', 'Could not update student profile.', 'error');
                return;
            }

            self::redirect_with_notice('tep-platform-students', 'Student profile updated.');
        }

        $data['created_at'] = current_time('mysql', true);
        $inserted = $wpdb->insert($table, $data, ['%d', '%s', '%s', '%s', '%s', '%s']);
        if (! $inserted) {
            add_settings_error('tep_phase2', 'tep_students_insert_failed', 'Could not create student profile (user may already be linked).', 'error');
            return;
        }

        self::redirect_with_notice('tep-platform-students', 'Student profile created.');
    }

    private static function handle_teachers_actions(): void
    {
        global $wpdb;
        $table = $wpdb->prefix . 'edu_teachers';

        if (isset($_GET['tep_action'], $_GET['id']) && $_GET['tep_action'] === 'delete' && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'] ?? '')), 'tep_teachers_delete_' . absint($_GET['id']))) {
            $wpdb->delete($table, ['id' => absint($_GET['id'])], ['%d']);
            self::redirect_with_notice('tep-platform-teachers', 'Teacher profile deleted.');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ! isset($_POST['tep_phase2_entity']) || $_POST['tep_phase2_entity'] !== 'teachers') {
            return;
        }

        check_admin_referer('tep_teachers_save', 'tep_teachers_nonce');

        $record_id = absint($_POST['tep_record_id'] ?? 0);
        $data = [
            'user_id' => absint($_POST['tep_user_id'] ?? 0),
            'bio' => wp_kses_post(wp_unslash($_POST['tep_bio'] ?? '')),
            'timezone' => sanitize_text_field(wp_unslash($_POST['tep_timezone'] ?? 'UTC')),
            'status' => self::sanitize_choice((string) ($_POST['tep_status'] ?? 'active'), ['active', 'inactive'], 'active'),
            'updated_at' => current_time('mysql', true),
        ];

        if ($data['user_id'] < 1) {
            add_settings_error('tep_phase2', 'tep_teachers_invalid_user', 'A valid User ID is required.', 'error');
            return;
        }

        if ($record_id > 0) {
            $updated = $wpdb->update($table, $data, ['id' => $record_id], ['%d', '%s', '%s', '%s', '%s'], ['%d']);
            if ($updated === false) {
                add_settings_error('tep_phase2', 'tep_teachers_update_failed', 'Could not update teacher profile.', 'error');
                return;
            }

            self::redirect_with_notice('tep-platform-teachers', 'Teacher profile updated.');
        }

        $data['created_at'] = current_time('mysql', true);
        $inserted = $wpdb->insert($table, $data, ['%d', '%s', '%s', '%s', '%s', '%s']);
        if (! $inserted) {
            add_settings_error('tep_phase2', 'tep_teachers_insert_failed', 'Could not create teacher profile (user may already be linked).', 'error');
            return;
        }

        self::redirect_with_notice('tep-platform-teachers', 'Teacher profile created.');
    }

    private static function handle_parents_actions(): void
    {
        global $wpdb;
        $table = $wpdb->prefix . 'edu_parents';

        if (isset($_GET['tep_action'], $_GET['id']) && $_GET['tep_action'] === 'delete' && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'] ?? '')), 'tep_parents_delete_' . absint($_GET['id']))) {
            $wpdb->delete($table, ['id' => absint($_GET['id'])], ['%d']);
            self::redirect_with_notice('tep-platform-parents', 'Parent profile deleted.');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ! isset($_POST['tep_phase2_entity']) || $_POST['tep_phase2_entity'] !== 'parents') {
            return;
        }

        check_admin_referer('tep_parents_save', 'tep_parents_nonce');

        $record_id = absint($_POST['tep_record_id'] ?? 0);
        $data = [
            'user_id' => absint($_POST['tep_user_id'] ?? 0),
            'phone' => sanitize_text_field(wp_unslash($_POST['tep_phone'] ?? '')),
            'status' => self::sanitize_choice((string) ($_POST['tep_status'] ?? 'active'), ['active', 'inactive'], 'active'),
            'updated_at' => current_time('mysql', true),
        ];

        if ($data['user_id'] < 1) {
            add_settings_error('tep_phase2', 'tep_parents_invalid_user', 'A valid User ID is required.', 'error');
            return;
        }

        if ($record_id > 0) {
            $updated = $wpdb->update($table, $data, ['id' => $record_id], ['%d', '%s', '%s', '%s'], ['%d']);
            if ($updated === false) {
                add_settings_error('tep_phase2', 'tep_parents_update_failed', 'Could not update parent profile.', 'error');
                return;
            }

            self::redirect_with_notice('tep-platform-parents', 'Parent profile updated.');
        }

        $data['created_at'] = current_time('mysql', true);
        $inserted = $wpdb->insert($table, $data, ['%d', '%s', '%s', '%s', '%s']);
        if (! $inserted) {
            add_settings_error('tep_phase2', 'tep_parents_insert_failed', 'Could not create parent profile (user may already be linked).', 'error');
            return;
        }

        self::redirect_with_notice('tep-platform-parents', 'Parent profile created.');
    }

    private static function handle_enrollments_actions(): void
    {
        global $wpdb;
        $table = $wpdb->prefix . 'edu_enrollments';

        if (isset($_GET['tep_action'], $_GET['id']) && $_GET['tep_action'] === 'delete' && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'] ?? '')), 'tep_enrollments_delete_' . absint($_GET['id']))) {
            $wpdb->delete($table, ['id' => absint($_GET['id'])], ['%d']);
            self::redirect_with_notice('tep-platform-enrollments', 'Enrollment deleted.');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ! isset($_POST['tep_phase2_entity']) || $_POST['tep_phase2_entity'] !== 'enrollments') {
            return;
        }

        check_admin_referer('tep_enrollments_save', 'tep_enrollments_nonce');

        $record_id = absint($_POST['tep_record_id'] ?? 0);
        $starts_on = self::sanitize_date($_POST['tep_starts_on'] ?? '');
        $ends_on = self::sanitize_date($_POST['tep_ends_on'] ?? '');

        $data = [
            'student_id' => absint($_POST['tep_student_id'] ?? 0),
            'course_id' => absint($_POST['tep_course_id'] ?? 0),
            'program_id' => absint($_POST['tep_program_id'] ?? 0),
            'enrollment_status' => self::sanitize_choice((string) ($_POST['tep_enrollment_status'] ?? 'active'), ['active', 'paused', 'completed', 'cancelled'], 'active'),
            'starts_on' => $starts_on,
            'ends_on' => $ends_on,
            'updated_at' => current_time('mysql', true),
        ];

        if ($data['student_id'] < 1 || $data['course_id'] < 1) {
            add_settings_error('tep_phase2', 'tep_enrollments_required', 'Student ID and Course ID are required.', 'error');
            return;
        }

        if ($starts_on !== null && $ends_on !== null && $starts_on > $ends_on) {
            add_settings_error('tep_phase2', 'tep_enrollments_dates', 'Start date cannot be after end date.', 'error');
            return;
        }

        if ($record_id > 0) {
            $updated = $wpdb->update($table, $data, ['id' => $record_id], ['%d', '%d', '%d', '%s', '%s', '%s', '%s'], ['%d']);
            if ($updated === false) {
                add_settings_error('tep_phase2', 'tep_enrollments_update_failed', 'Could not update enrollment.', 'error');
                return;
            }

            self::redirect_with_notice('tep-platform-enrollments', 'Enrollment updated.');
        }

        $data['created_at'] = current_time('mysql', true);
        $inserted = $wpdb->insert($table, $data, ['%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s']);
        if (! $inserted) {
            add_settings_error('tep_phase2', 'tep_enrollments_insert_failed', 'Could not create enrollment.', 'error');
            return;
        }

        self::redirect_with_notice('tep-platform-enrollments', 'Enrollment created.');
    }


    private static function sanitize_choice(string $value, array $allowed, string $default): string
    {
        $normalized = sanitize_text_field(wp_unslash($value));

        return in_array($normalized, $allowed, true) ? $normalized : $default;
    }

    private static function sanitize_date(string $value): ?string
    {
        $trimmed = sanitize_text_field(wp_unslash($value));
        if ($trimmed === '') {
            return null;
        }

        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $trimmed) ? $trimmed : null;
    }

    private static function redirect_with_notice(string $page, string $notice): void
    {
        $url = add_query_arg([
            'page' => $page,
            'tep_notice' => $notice,
        ], admin_url('admin.php'));

        wp_safe_redirect($url);
        exit;
    }

    private static function assert_access(): void
    {
        if (! current_user_can(self::CAPABILITY)) {
            wp_die(esc_html__('You do not have permission to access this page.', 'tep'));
        }

        if (isset($_GET['tep_notice'])) {
            add_settings_error('tep_phase2', 'tep_phase2_notice', sanitize_text_field(wp_unslash($_GET['tep_notice'])), 'updated');
        }
    }
}
