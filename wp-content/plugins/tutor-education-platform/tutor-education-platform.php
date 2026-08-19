<?php
/**
 * Plugin Name: Tutor Education Platform
 * Description: Core education platform plugin for live online classes, roles, scheduling metadata, and student dashboard widgets.
 * Version: 0.3.0
 * Author: Tutor New Website
 */

if (! defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'includes/class-tep-capability-map.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-tep-migrations.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-tep-post-types.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-tep-class-sessions.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-tep-dashboard-shortcodes.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-tep-version-manager.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-tep-people-enrollments-admin.php';

final class TEP_Platform
{
    private const OPTION_GOOGLE_CONNECTED = 'tep_google_connected';
    private const VERSION = '0.3.0';

    public static function init(): void
    {
        register_activation_hook(__FILE__, [self::class, 'activate']);
        add_action('plugins_loaded', [self::class, 'maybe_upgrade']);
        add_action('init', [self::class, 'register_roles']);
        add_action('init', [TEP_Post_Types::class, 'register']);
        add_action('add_meta_boxes', [TEP_Class_Sessions::class, 'register_meta_boxes']);
        add_action('save_post_tep_class_session', [TEP_Class_Sessions::class, 'save_meta']);
        add_action('admin_menu', [self::class, 'register_admin_menu']);
        add_action('admin_init', [self::class, 'register_settings']);
        add_shortcode('tep_student_dashboard_next_class', [TEP_Dashboard_Shortcodes::class, 'render_next_class']);
    }

    public static function activate(): void
    {
        self::register_roles();
        TEP_Post_Types::register();
        TEP_Migrations::run_pending_migrations();
        TEP_Version_Manager::set_version(self::VERSION);
        flush_rewrite_rules();
    }

    public static function maybe_upgrade(): void
    {
        TEP_Version_Manager::maybe_upgrade(self::VERSION);
    }

    public static function register_roles(): void
    {
        TEP_Capability_Map::register_roles();
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

        TEP_People_Enrollments_Admin::register_submenus();
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
}

TEP_Platform::init();
