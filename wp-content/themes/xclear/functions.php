<?php
if (! defined("ABSPATH")) {
    exit;
}

function xclearLoadTextdomain(): void
{
    $locale = apply_filters('theme_locale', determine_locale(), 'xclear');
    $file   = get_template_directory() . "/languages/xclear-{$locale}.mo";
    if (file_exists($file)) {
        load_textdomain('xclear', $file);
    }
}
add_action('after_setup_theme', 'xclearLoadTextdomain', 20);

function xclearEnqueueScripts()
{
    $style_path = get_stylesheet_directory() . "/assets/css/style.min.css";
    $version    = file_exists($style_path) ? filemtime($style_path) : wp_get_theme()->get("Version");

    wp_enqueue_style("xclear-style", get_stylesheet_directory_uri() . "/assets/css/style.min.css", array(), $version);
    wp_enqueue_style(
        "plus-jakarta-sans",
        "https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap",
        [],
        null
    );

    if (is_tax("project_category")) {
        wp_enqueue_style("xclear-project-category", get_stylesheet_directory_uri() . "/assets/css/widgets/project-category.css", array(), $version);
    }

    if (is_singular("project")) {
        wp_enqueue_style("xclear-single-project", get_stylesheet_directory_uri() . "/assets/css/widgets/single-project.css", array(), $version);
    }

    if (is_singular("post")) {
        wp_enqueue_style("xclear-single-post", get_stylesheet_directory_uri() . "/assets/css/widgets/single-post.css", array(), $version);
    }

    if (is_post_type_archive("project")) {
        wp_enqueue_style("xclear-archive-project", get_stylesheet_directory_uri() . "/assets/css/widgets/archive-project.css", array(), $version);
    }

    wp_enqueue_script("xclear-custom-tabs", get_stylesheet_directory_uri() . "/assets/js/custom-tabs.js", array("jquery"), $version, true);

    if (is_singular("product")) {
        wp_enqueue_style("xclear-single-product", get_stylesheet_directory_uri() . "/assets/css/widgets/single-product.css", array(), $version);
        wp_enqueue_script("xclear-product-detail", get_stylesheet_directory_uri() . "/assets/js/xclear-product-detail.js", array(), $version, true);
    }
}
add_action("wp_enqueue_scripts", "xclearEnqueueScripts");

require_once get_template_directory() . "/inc/elementor-assets.php"; // NOSONAR
require_once get_template_directory() . "/inc/elementor-widgets.php"; // NOSONAR
require_once get_template_directory() . "/inc/elementor-conditions.php"; // NOSONAR
require_once get_template_directory() . "/inc/projects.php";
require_once get_template_directory() . "/inc/article-ajax.php"; // NOSONAR
require_once get_template_directory() . "/inc/product-listing.php";
require_once get_template_directory() . "/inc/products.php"; // NOSONAR

add_theme_support('post-thumbnails');

/**
 * Elementor Theme Builder templates (header/footer/etc.) use the
 * "elementor_library" post type, which is not public — Polylang only
 * auto-detects public CPTs for translation management, so it never offers
 * to translate the header/footer otherwise. Registering it here lets each
 * Theme Builder template have a per-language version, same as any page.
 */
add_filter('pll_get_post_types', function (array $postTypes): array {
    $postTypes['elementor_library'] = 'elementor_library';
    return $postTypes;
});

/**
 * Ensure Polylang manages translations for the custom taxonomies, so each
 * category term can have a per-language name/description/slug (needed for
 * the DeepL/Claude translate tool's category section, and for menu links
 * that point to a category archive to resolve to the right language).
 */
add_filter('pll_get_taxonomies', function (array $taxonomies): array {
    foreach (['product_category', 'project_category'] as $tax) {
        $taxonomies[$tax] = $tax;
    }
    return $taxonomies;
});

/**
 * Elementor Pro's Archive Posts checks has_post_thumbnail() before calling
 * get_the_post_thumbnail(), so we need both filters to inject a placeholder.
 */
add_filter('has_post_thumbnail', '__return_true');

add_filter('post_thumbnail_html', function (string $html, int $post_id): string {
    if (! empty($html)) {
        return $html;
    }

    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="600" height="400" viewBox="0 0 600 400">'
         . '<rect width="600" height="400" fill="#f5f5f5"/>'
         . '<g transform="translate(300,200)" fill="none" stroke="#cccccc" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">'
         . '<rect x="-55" y="-42" width="110" height="84" rx="8"/>'
         . '<circle cx="0" cy="-4" r="22"/>'
         . '<path d="M-55 30 L-28 4 L-8 22 L18 -2 L55 30"/>'
         . '<rect x="-20" y="-42" width="14" height="10" rx="3" fill="#cccccc" stroke="none"/>'
         . '</g>'
         . '</svg>';

    $src = 'data:image/svg+xml;base64,' . base64_encode($svg);

    return '<img src="' . esc_attr($src) . '" alt="' . esc_attr(get_the_title($post_id)) . '" class="wp-post-image xclear-thumbnail-placeholder">';
}, 10, 2);

/**
 * Override default WordPress gallery shortcode to use the product detail slider style.
 */
add_filter('post_gallery', 'xclear_custom_post_gallery', 10, 2);
function xclear_custom_post_gallery($output, $attr) {
    global $post;

    if (isset($attr['orderby'])) {
        $attr['orderby'] = sanitize_sql_orderby($attr['orderby']);
        if (!$attr['orderby']) {
            unset($attr['orderby']);
        }
    }

    $atts = shortcode_atts([
        'order'      => 'ASC',
        'orderby'    => 'menu_order ID',
        'id'         => $post ? $post->ID : 0,
        'include'    => '',
        'exclude'    => '',
    ], $attr, 'gallery');

    $id = intval($atts['id']);

    if (!empty($atts['include'])) {
        $_attachments = get_posts([
            'include'        => $atts['include'],
            'post_status'    => 'inherit',
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'order'          => $atts['order'],
            'orderby'        => $atts['orderby'],
        ]);
        $attachments = [];
        foreach ($_attachments as $key => $val) {
            $attachments[$val->ID] = $_attachments[$key];
        }
    } elseif (!empty($atts['exclude'])) {
        $attachments = get_children([
            'post_parent'    => $id,
            'exclude'        => $atts['exclude'],
            'post_status'    => 'inherit',
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'order'          => $atts['order'],
            'orderby'        => $atts['orderby'],
        ]);
    } else {
        $attachments = get_children([
            'post_parent'    => $id,
            'post_status'    => 'inherit',
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'order'          => $atts['order'],
            'orderby'        => $atts['orderby'],
        ]);
    }

    if (empty($attachments)) {
        return '';
    }

    // Ensure assets are loaded
    $version = wp_get_theme()->get('Version');
    wp_enqueue_style('xclear-single-product', get_stylesheet_directory_uri() . '/assets/css/widgets/single-product.css', [], $version);
    wp_enqueue_script('xclear-product-detail', get_stylesheet_directory_uri() . '/assets/js/xclear-product-detail.js', [], $version, true);

    ob_start();
    ?>
    <div class="product-detail__slider product-detail__slider--two-item">
        <button class="product-detail__slider-arrow product-detail__slider-prev" aria-label="<?php esc_attr_e('Previous', 'xclear'); ?>">
            <svg width="32" height="32" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path d="M13 4L7 10L13 16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>

        <div class="product-detail__slider-track-wrap">
            <div class="product-detail__slider-track">
                <?php foreach ($attachments as $att_id => $attachment) : 
                    $src = wp_get_attachment_image_url($att_id, 'full');
                    $alt = get_post_meta($att_id, '_wp_attachment_image_alt', true) ?: $attachment->post_title;
                ?>
                    <div class="product-detail__slide">
                        <img src="<?php echo esc_url($src); ?>"
                            alt="<?php echo esc_attr($alt); ?>"
                            loading="lazy">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <button class="product-detail__slider-arrow product-detail__slider-next" aria-label="<?php esc_attr_e('Next', 'xclear'); ?>">
            <svg width="32" height="32" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path d="M7 4L13 10L7 16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>
    </div>

    <div class="product-detail__image-lightbox" id="product-image-lightbox-<?php echo esc_attr($id); ?>" aria-hidden="true">
        <button class="product-detail__image-lightbox-close" aria-label="<?php esc_attr_e('Close', 'xclear'); ?>">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
        </button>
        <button class="product-detail__image-lightbox-prev" aria-label="<?php esc_attr_e('Previous', 'xclear'); ?>">
            <svg width="28" height="28" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path d="M13 4L7 10L13 16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>
        <div class="product-detail__image-lightbox-content">
            <img src="" alt="" class="product-detail__image-lightbox-img">
        </div>
        <button class="product-detail__image-lightbox-next" aria-label="<?php esc_attr_e('Next', 'xclear'); ?>">
            <svg width="28" height="28" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path d="M7 4L13 10L7 16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>
    </div>
    <?php
    return ob_get_clean();
}
