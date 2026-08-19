<?php

if (! defined('ABSPATH')) {
    exit;
}

final class TEP_Dashboard_Shortcodes
{
    public static function render_next_class(): string
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
