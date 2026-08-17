<?php
if (! defined('ABSPATH')) {
    exit;
}

function xclearRegisterElementorWidgetAssets()
{
    $css_widgets = [
        'xclear-hero-banner-widget-css'        => '/assets/css/widgets/xclear-hero-banner-widget.css',
        'xclear-applications-cards-widget-css' => '/assets/css/widgets/xclear-applications-cards-widget.css',
        'xclear-image-with-text-widget-css'    => '/assets/css/widgets/xclear-image-with-text-widget.css',
        'xclear-categories-cards-widget-css'   => '/assets/css/widgets/xclear-categories-cards-widget.css',
        'xclear-about-highlights-widget-css'   => '/assets/css/widgets/xclear-about-highlights-widget.css',
        'xclear-uvc-guide-widget-css'          => '/assets/css/widgets/xclear-uvc-guide-widget.css',
        'xclear-uvc-how-it-works-widget-css'  => '/assets/css/widgets/xclear-uvc-how-it-works-widget.css',
        'xclear-uvc-installation-widget-css'  => '/assets/css/widgets/xclear-uvc-installation-widget.css',
        'xclear-industry-cards-widget-css'    => '/assets/css/widgets/xclear-industry-cards-widget.css',
        'xclear-lamp-replacement-widget-css'  => '/assets/css/widgets/xclear-lamp-replacement-widget.css',
        'xclear-document-download-widget-css' => '/assets/css/widgets/xclear-document-download-widget.css',
        'xclear-faq-widget-css'               => '/assets/css/widgets/xclear-faq-widget.css',
        'xclear-article-widget-css'           => '/assets/css/widgets/xclear-article-widget.css',
        'xclear-video-gallery-widget-css'     => '/assets/css/widgets/xclear-video-gallery-widget.css',
        'xclear-section-header-widget-css'     => '/assets/css/widgets/xclear-section-header-widget.css',
        'xclear-product-listing-widget-css'    => '/assets/css/widgets/xclear-product-listing-widget.css',
        'xclear-legal-widget-css'              => '/assets/css/widgets/xclear-legal-widget.css',
        'xclear-list-category-widget-css'      => '/assets/css/widgets/xclear-list-category-widget.css',
        'xclear-language-switcher-widget-css'  => '/assets/css/widgets/xclear-language-switcher-widget.css',
        'xclear-nav-dropdown-widget-css'       => '/assets/css/widgets/xclear-nav-dropdown-widget.css',
    ];

    foreach ($css_widgets as $handle => $path) {
        $file_path = get_stylesheet_directory() . $path;
        $version   = file_exists($file_path) ? filemtime($file_path) : wp_get_theme()->get('Version');

        wp_register_style(
            $handle,
            get_stylesheet_directory_uri() . $path,
            ['xclear-style'],
            $version
        );
    }
}
add_action('elementor/frontend/after_register_styles', 'xclearRegisterElementorWidgetAssets');

function xclearRegisterElementorWidgetScripts()
{
    $js_widgets = [
        'xclear-image-with-text-widget-js' => '/assets/js/xclear-image-with-text-widget.js',
        'xclear-uvc-how-it-works-widget-js' => '/assets/js/xclear-uvc-how-it-works-widget.js',
        'xclear-faq-widget-js'              => '/assets/js/xclear-faq-widget.js',
        'xclear-article-widget-js'          => '/assets/js/xclear-article-widget.js',
        'xclear-video-gallery-widget-js'    => '/assets/js/xclear-video-gallery-widget.js',
        'xclear-product-listing-widget-js'      => '/assets/js/xclear-product-listing-widget.js',
        'xclear-language-switcher-widget-js'    => '/assets/js/xclear-language-switcher-widget.js',
        'xclear-nav-dropdown-widget-js'        => '/assets/js/xclear-nav-dropdown-widget.js',
    ];

    foreach ($js_widgets as $handle => $path) {
        $file_path = get_stylesheet_directory() . $path;
        $version   = file_exists($file_path) ? filemtime($file_path) : wp_get_theme()->get('Version');

        wp_register_script(
            $handle,
            get_stylesheet_directory_uri() . $path,
            ['jquery'],
            $version,
            true
        );
    }
}
add_action('elementor/frontend/after_register_scripts', 'xclearRegisterElementorWidgetScripts');
