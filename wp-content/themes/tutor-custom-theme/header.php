<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<header class="tep-container" style="padding:1rem;">
    <nav>
        <?php wp_nav_menu(['theme_location' => 'primary', 'fallback_cb' => false]); ?>
    </nav>
</header>
