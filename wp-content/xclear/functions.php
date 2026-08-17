<?php
function xclear_enqueue_styles() {
    wp_enqueue_style(
        'xclear-style',
        get_stylesheet_uri()
    );

    wp_enqueue_style(
        'xclear-main',
        get_stylesheet_directory_uri() . '/assets/css/main.min.css',
        [],
        '1.0'
    );
}
add_action('wp_enqueue_scripts', 'xclear_enqueue_styles');