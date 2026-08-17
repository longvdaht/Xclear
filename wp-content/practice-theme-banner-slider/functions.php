<?php

function practice_theme_setup() 
{
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('html5', array('comment-list', 'comment-form', 'search-form', 'gallery', 'caption'));
    register_nav_menus(array(
        'primary' => ('Primary Menu'),
    ));
}
add_action('after_setup_theme', 'practice_theme_setup');

function practice_theme_enqueue_fonts() 
{
    wp_enqueue_style(
        'google-fonts',
        'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap',
        array(),
        null
    );
}
add_action('wp_enqueue_scripts', 'practice_theme_enqueue_fonts');

function practice_theme_enqueue_swiper()
{
    wp_enqueue_style(
        'swiper',
        'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
        array(),
        '11'
    );

    wp_enqueue_script(
        'swiper',
        'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
        array(),
        '11',
        true
    );

    $banner_js = get_template_directory() . '/js/banner-widget.js';
    wp_enqueue_script(
        'banner-widget',
        get_template_directory_uri() . '/js/banner-widget.js',
        array('swiper'),
        file_exists($banner_js) ? filemtime($banner_js) : null,
        true
    );
}
add_action('wp_enqueue_scripts', 'practice_theme_enqueue_swiper');

function practice_theme_enqueue_css() 
{
    $dependencies = array('google-fonts');

    $style_file = get_template_directory() . '/style.css';
    wp_enqueue_style(
        'practice-theme-style',
        get_stylesheet_uri(),
        $dependencies,
        file_exists($style_file) ? filemtime($style_file) : null
    );
    $dependencies[] = 'practice-theme-style';

    $widget_css_dir = get_template_directory() . '/assets/css';
    $widget_css_uri = get_template_directory_uri() . '/assets/css';

    foreach (glob($widget_css_dir . '/*.css') as $file) {
        $filename = basename($file, '.css');
        wp_enqueue_style(
            'practice-theme-' . $filename,
            $widget_css_uri . '/' . basename($file),
            $dependencies,
            filemtime($file)
        );
    }
}
add_action('wp_enqueue_scripts', 'practice_theme_enqueue_css');

require_once get_stylesheet_directory() . '/inc/widget-loader.php';


function add_custom_widget_categories( $elements_manager ) {
    $elements_manager->add_category(
        'custom-widget', 
        [
            'title' => 'Custom Widgets', 
            'icon'  => 'eicon-code',     
        ]
    );
}
add_action( 'elementor/elements/categories_registered', 'add_custom_widget_categories' );

?>

