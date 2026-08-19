<?php

if (! defined('ABSPATH')) {
    exit;
}

final class TEP_Capability_Map
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public static function get_role_capabilities(): array
    {
        $map = [
            'education_administrator' => [
                'label' => 'Education Administrator',
                'capabilities' => [
                    'read' => true,
                    'edit_posts' => true,
                    'manage_tep_platform' => true,
                    'manage_tep_sessions' => true,
                ],
            ],
            'teacher' => [
                'label' => 'Teacher',
                'capabilities' => [
                    'read' => true,
                    'manage_tep_sessions' => true,
                    'edit_tep_class_sessions' => true,
                ],
            ],
            'student' => [
                'label' => 'Student',
                'capabilities' => [
                    'read' => true,
                ],
            ],
            'parent_guardian' => [
                'label' => 'Parent/Guardian',
                'capabilities' => [
                    'read' => true,
                ],
            ],
            'accountant' => [
                'label' => 'Accountant',
                'capabilities' => [
                    'read' => true,
                    'manage_woocommerce' => true,
                ],
            ],
        ];

        return apply_filters('tep_role_capability_map', $map);
    }

    /**
     * @return array<string, bool>
     */
    public static function get_administrator_capabilities(): array
    {
        $capabilities = [
            'manage_tep_platform' => true,
            'manage_tep_sessions' => true,
            'edit_tep_class_sessions' => true,
        ];

        return apply_filters('tep_administrator_capabilities', $capabilities);
    }

    public static function register_roles(): void
    {
        foreach (self::get_role_capabilities() as $role => $definition) {
            $label = is_string($definition['label'] ?? null) ? $definition['label'] : '';
            $capabilities = is_array($definition['capabilities'] ?? null) ? $definition['capabilities'] : [];

            if ($label === '' || $capabilities === []) {
                continue;
            }

            add_role($role, $label, $capabilities);
        }

        $admin = get_role('administrator');
        if (! $admin instanceof WP_Role) {
            return;
        }

        foreach (self::get_administrator_capabilities() as $capability => $granted) {
            if ($granted) {
                $admin->add_cap($capability);
                continue;
            }

            $admin->remove_cap($capability);
        }
    }
}
