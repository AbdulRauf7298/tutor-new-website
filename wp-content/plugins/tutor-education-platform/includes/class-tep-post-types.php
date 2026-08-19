<?php

if (! defined('ABSPATH')) {
    exit;
}

final class TEP_Post_Types
{
    public static function register(): void
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
}
