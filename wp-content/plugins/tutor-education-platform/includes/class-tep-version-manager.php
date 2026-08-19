<?php

if (! defined('ABSPATH')) {
    exit;
}

final class TEP_Version_Manager
{
    private const OPTION_PLATFORM_VERSION = 'tep_platform_version';

    public static function maybe_upgrade(string $current_version): void
    {
        $saved_version = (string) get_option(self::OPTION_PLATFORM_VERSION, '0.0.0');

        if (version_compare($saved_version, $current_version, '>=')) {
            return;
        }

        TEP_Migrations::run_pending_migrations();
        update_option(self::OPTION_PLATFORM_VERSION, $current_version, false);
    }

    public static function set_version(string $current_version): void
    {
        update_option(self::OPTION_PLATFORM_VERSION, $current_version, false);
    }
}
