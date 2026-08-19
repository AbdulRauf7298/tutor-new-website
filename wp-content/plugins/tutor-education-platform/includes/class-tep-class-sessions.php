<?php

if (! defined('ABSPATH')) {
    exit;
}

final class TEP_Class_Sessions
{
    /**
     * @return array<string, string>
     */
    private static function get_defaults(): array
    {
        return [
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
    }

    public static function register_meta_boxes(): void
    {
        add_meta_box(
            'tep_class_session_details',
            'Class Session Details',
            [self::class, 'render_meta_box'],
            'tep_class_session',
            'normal',
            'high'
        );
    }

    public static function render_meta_box(WP_Post $post): void
    {
        wp_nonce_field('tep_save_session_meta', 'tep_session_meta_nonce');

        $fields = self::get_defaults();

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

    public static function save_meta(int $post_id): void
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
}
