<?php
if (! defined('ABSPATH')) {
    exit;
}

add_action('elementor/widgets/register', function($widgets_manager) {
    
    $my_widgets = [
        'XClear_Banner' => ['file' => 'banner-widget'],
        'Section_Heading' => ['file' => 'application-heading-widget'],
        'Pond_Card_Grid' => ['file' => 'pond-card-grid-widget'],
        'About_Xclear' => ['file' => 'about-xclear-widget'],
    ];

    foreach ($my_widgets as $class_name => $config) {
        $base_name = $config['file'];
        
        $php_path = get_stylesheet_directory() . '/inc/' . $base_name . '.php';
        $css_path = get_stylesheet_directory() . '/assets/css/' . $base_name . '.css';
        $css_uri  = get_stylesheet_directory_uri() . '/assets/css/' . $base_name . '.css';
        $js_path  = get_stylesheet_directory() . '/js/' . $base_name . '.js';
        $js_uri   = get_stylesheet_directory_uri() . '/js/' . $base_name . '.js';

        if (file_exists($php_path)) {
            require_once $php_path; // NOSONAR

            if (class_exists($class_name)) {
                $widget_instance = new $class_name();
                $widgets_manager->register($widget_instance);

                $style_depends = $widget_instance->get_style_depends();
                if (!empty($style_depends) && isset($style_depends[0])) {
                    $handle = $style_depends[0];
                    if (file_exists($css_path)) {
                        wp_register_style(
                            $handle, 
                            $css_uri, 
                            [], 
                            filemtime($css_path) 
                        );
                    } else {
                        wp_deregister_style($handle);
                    }
                }

                $script_depends = $widget_instance->get_script_depends();
                if (!empty($script_depends) && isset($script_depends[0])) {
                    $js_handle = $script_depends[0];
                    if (file_exists($js_path)) {
                        wp_register_script(
                            $js_handle,
                            $js_uri,
                            ['swiper'],
                            filemtime($js_path),
                            true
                        );
                    }
                }
            }
        }
    }
});
