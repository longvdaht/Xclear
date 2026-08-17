<?php

/**
 * Template Name: Product - Extended Advantages
 * Template Post Type: product
 *
 * Same as the default single-product.php, plus an extra "Advantages 2" block
 * for products whose Advantages content is too long for a single field.
 * Assign via the Template field on the product's edit screen.
 */
define('XCLEAR_CSS_ACTIVE', ' active');

/**
 * Extract a thumbnail URL from a YouTube or Vimeo URL.
 * Returns empty string if the URL is not recognised or the request fails.
 */
function xclear_video_auto_thumbnail(string $url): string
{
    // YouTube: youtube.com/watch?v=ID  or  youtu.be/ID
    if (preg_match('/(?:youtube\.com\/(?:watch\?.*v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $m)) {
        return 'https://img.youtube.com/vi/' . $m[1] . '/hqdefault.jpg';
    }

    // Vimeo: vimeo.com/ID
    if (preg_match('/vimeo\.com\/(\d+)/', $url, $m)) {
        $cache_key = 'xclear_vimeo_thumb_' . $m[1];
        $cached    = get_transient($cache_key);
        if ($cached !== false) {
            return $cached;
        }
        $response = wp_remote_get('https://vimeo.com/api/oembed.json?url=' . rawurlencode($url) . '&width=640');
        if (! is_wp_error($response)) {
            $data  = json_decode(wp_remote_retrieve_body($response), true);
            $thumb = $data['thumbnail_url'] ?? '';
            set_transient($cache_key, $thumb, WEEK_IN_SECONDS);
            return $thumb;
        }
    }

    return '';
}

get_header();

if (! have_posts()) {
    get_footer();
    return;
}

the_post();
$post_id = get_the_ID();

// ACF fields
$short_description   = get_field('product_short_description', $post_id);
$gallery             = get_field('product_gallery', $post_id);
$advantages          = get_field('product_advantages', $post_id);
$advantages_2        = get_field('product_advantages_2', $post_id);
$manuals_file        = get_field('product_manuals_file', $post_id);
$leaflet_file        = get_field('product_leaflet_file', $post_id);
$request_info_url    = xclear_resolve_global_page_link('global_request_info_url');
$become_dealer_url   = xclear_resolve_global_page_link('global_become_dealer_url');
$buy_parts_url       = get_field('product_buy_parts_url', $post_id);
$specs_image                = get_field('product_specs_image', $post_id);
$specs_image_mobile         = get_field('product_specs_image_mobile', $post_id);
$specs_html                 = get_field('product_specs_html', $post_id);
$spare_parts_image          = get_field('product_spare_parts_image', $post_id);
$spare_parts_image_mobile   = get_field('product_spare_parts_image_mobile', $post_id);
$spare_parts_html           = get_field('product_spare_parts_html', $post_id);
$data_table_image           = get_field('product_data_table_image', $post_id);
$data_table_image_mobile    = get_field('product_data_table_image_mobile', $post_id);
$data_table_html            = get_field('product_data_table_html', $post_id);
$installation_videos = get_field('product_installation_videos', $post_id);

// Main image always comes from Featured Image
$main_img_url = has_post_thumbnail() ? get_the_post_thumbnail_url($post_id, 'full') : '';
$main_img_alt = get_the_title();

// Breadcrumb: get first product category
$terms    = get_the_terms($post_id, 'product_category');
$category = (! empty($terms) && ! is_wp_error($terms)) ? $terms[0] : null;

// Build tabs array (only show tab if it has content)
$tabs = [];
if ($specs_image || $specs_html) {
    $tabs[] = [
        'id'           => 'specs',
        'label'        => __('Specs', 'xclear'),
        'image'        => $specs_image,
        'image_mobile' => $specs_image_mobile,
        'html'         => $specs_html,
    ];
}
if ($spare_parts_image || $spare_parts_html) {
    $tabs[] = [
        'id'           => 'spare-parts',
        'label'        => __('Spare parts', 'xclear'),
        'image'        => $spare_parts_image,
        'image_mobile' => $spare_parts_image_mobile,
        'html'         => $spare_parts_html,
    ];
}
if ($data_table_image || $data_table_html) {
    $tabs[] = [
        'id'           => 'data-table',
        'label'        => __('Data Table', 'xclear'),
        'image'        => $data_table_image,
        'image_mobile' => $data_table_image_mobile,
        'html'         => $data_table_html,
    ];
}
if (! empty($installation_videos)) {
    $tabs[] = [
        'id'     => 'installation-video',
        'label'  => __('Installation Video', 'xclear'),
        'videos' => $installation_videos,
    ];
}
?>

<div class="product-detail container">

    <?php /* ── Breadcrumbs ──────────────────────────────────────────── */ ?>
    <nav class="product-detail__breadcrumbs" aria-label="<?php esc_attr_e('Breadcrumb', 'xclear'); ?>">
        <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'xclear'); ?></a>
        <span class="product-detail__breadcrumbs-sep" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                <path d="M4.69955 2.05045L8.44955 5.80045C8.50222 5.85319 8.5318 5.92467 8.5318 5.9992C8.5318 6.07374 8.50222 6.14522 8.44955 6.19795L4.69955 9.94795C4.64623 9.99763 4.57571 10.0247 4.50285 10.0234C4.42999 10.0221 4.36047 9.99259 4.30894 9.94106C4.25741 9.88953 4.22789 9.82001 4.22661 9.74715C4.22532 9.67429 4.25237 9.60377 4.30205 9.55045L7.85283 5.9992L4.30205 2.44795C4.25237 2.39464 4.22532 2.32412 4.22661 2.25126C4.22789 2.1784 4.25741 2.10888 4.30894 2.05735C4.36047 2.00582 4.42999 1.9763 4.50285 1.97501C4.57571 1.97373 4.64623 2.00077 4.69955 2.05045Z" fill="#263238" />
            </svg>
        </span>
        <?php if ($category) : ?>
            <a href="<?php echo esc_url(get_term_link($category)); ?>"><?php echo esc_html($category->name); ?></a>
            <span class="product-detail__breadcrumbs-sep" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                    <path d="M4.69955 2.05045L8.44955 5.80045C8.50222 5.85319 8.5318 5.92467 8.5318 5.9992C8.5318 6.07374 8.50222 6.14522 8.44955 6.19795L4.69955 9.94795C4.64623 9.99763 4.57571 10.0247 4.50285 10.0234C4.42999 10.0221 4.36047 9.99259 4.30894 9.94106C4.25741 9.88953 4.22789 9.82001 4.22661 9.74715C4.22532 9.67429 4.25237 9.60377 4.30205 9.55045L7.85283 5.9992L4.30205 2.44795C4.25237 2.39464 4.22532 2.32412 4.22661 2.25126C4.22789 2.1784 4.25741 2.10888 4.30894 2.05735C4.36047 2.00582 4.42999 1.9763 4.50285 1.97501C4.57571 1.97373 4.64623 2.00077 4.69955 2.05045Z" fill="#263238" />
                </svg>
            </span>
        <?php endif; ?>
        <span class="product-detail__breadcrumbs-current"><?php the_title(); ?></span>
    </nav>

    <?php /* ── Top section ─────────────────────────────────────────── */ ?>
    <div class="product-detail__top">

        <?php /* ── Featured image ───────────────────────────────────── */ ?>
        <div class="product-detail__gallery-main">
            <?php if ($main_img_url) : ?>
                <img src="<?php echo esc_url($main_img_url); ?>"
                    alt="<?php echo esc_attr($main_img_alt); ?>"
                    loading="lazy">
            <?php endif; ?>
        </div>

        <?php /* ── Product info ─────────────────────────────────────── */ ?>
        <div class="product-detail__info">

            <h1 class="product-detail__title"><?php the_title(); ?></h1>

            <?php if ($short_description) : ?>
                <div class="product-detail__desc"><?php echo wp_kses_post($short_description); ?></div>
            <?php endif; ?>

            <?php if ($advantages) : ?>
                <div class="product-detail__advantages">
                    <h3 class="product-detail__advantages-title"><?php esc_html_e('Advantages', 'xclear'); ?></h3>
                    <div class="product-detail__advantages-content">
                        <?php echo wp_kses_post($advantages); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($manuals_file || $leaflet_file) : ?>
                <div class="product-detail__downloads">
                    <?php if ($manuals_file) :
                        $manuals_url  = $manuals_file['url'] ?? '';
                        $manuals_name = $manuals_file['filename'] ?? 'manuals';
                    ?>
                        <a href="<?php echo esc_url($manuals_url); ?>"
                            class="product-detail__download-btn"
                            target="_blank"
                            rel="noopener noreferrer">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                <path d="M8 2v7M5.5 6.5L8 9l2.5-2.5M3 12h10" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <?php esc_html_e('Manuals', 'xclear'); ?>
                        </a>
                    <?php endif; ?>
                    <?php if ($leaflet_file) :
                        $leaflet_url  = $leaflet_file['url'] ?? '';
                        $leaflet_name = $leaflet_file['filename'] ?? 'leaflet';
                    ?>
                        <a href="<?php echo esc_url($leaflet_url); ?>"
                            class="product-detail__download-btn"
                            target="_blank"
                            rel="noopener noreferrer">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                <path d="M8 2v7M5.5 6.5L8 9l2.5-2.5M3 12h10" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <?php esc_html_e('Leaflet', 'xclear'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="product-detail__ctas">
                <a href="<?php echo esc_url($request_info_url); ?>" class="btn btn-secondary product-detail__cta-full">
                    <?php esc_html_e('Request More Information', 'xclear'); ?>
                </a>
                <div class="product-detail__cta-row">
                    <a href="<?php echo esc_url($buy_parts_url); ?>" class="btn btn-light-green">
                        <?php esc_html_e('Buy spare parts', 'xclear'); ?>
                    </a>
                    <a href="<?php echo esc_url($become_dealer_url); ?>" class="btn btn-light-green">
                        <?php esc_html_e('Become a Dealer', 'xclear'); ?>
                    </a>
                </div>
            </div>

        </div>
    </div>

    <?php /* ── Advantages 2 (extended content, own row) ───────────────── */ ?>
    <?php if ($advantages_2) : ?>
        <div class="product-detail__advantages-wrapper">
            <div class="product-detail__advantages-content">
                <?php echo wp_kses_post($advantages_2); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php /* ── Gallery slider ──────────────────────────────────────── */ ?>
    <?php if (! empty($gallery)) : ?>
        <div class="product-detail__slider">
            <button class="product-detail__slider-arrow product-detail__slider-prev" aria-label="<?php esc_attr_e('Previous', 'xclear'); ?>">
                <svg width="32" height="32" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <path d="M13 4L7 10L13 16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>

            <div class="product-detail__slider-track-wrap">
                <div class="product-detail__slider-track">
                    <?php foreach ($gallery as $img) :
                        if (is_array($img)) {
                            $src = $img['sizes']['full'] ?? $img['url'] ?? '';
                            $alt = $img['alt'] ?: get_the_title();
                        } else {
                            $src = $img;
                            $alt = get_the_title();
                        }
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
    <?php endif; ?>

    <?php /* ── Image lightbox ──────────────────────────────────────── */ ?>
    <?php if (! empty($gallery)) : ?>
        <div class="product-detail__image-lightbox" id="product-image-lightbox" aria-hidden="true">
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
    <?php endif; ?>

    <?php /* ── Tabs ─────────────────────────────────────────────────── */ ?>
    <?php if (! empty($tabs)) : ?>
        <div class="product-detail__tabs">

            <div class="product-detail__tabs-nav" role="tablist">
                <?php foreach ($tabs as $i => $tab) : ?>
                    <button class="product-detail__tab-btn<?php echo $i === 0 ? XCLEAR_CSS_ACTIVE : ''; ?>"
                        role="tab"
                        aria-controls="tab-panel-<?php echo esc_attr($tab['id']); ?>"
                        aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>"
                        data-tab="<?php echo esc_attr($tab['id']); ?>">
                        <?php echo esc_html($tab['label']); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="product-detail__tabs-content">
                <?php foreach ($tabs as $i => $tab) : ?>
                    <div class="product-detail__tab-panel<?php echo $i === 0 ? XCLEAR_CSS_ACTIVE : ''; ?>"
                        id="tab-panel-<?php echo esc_attr($tab['id']); ?>"
                        role="tabpanel"
                        aria-hidden="<?php echo $i === 0 ? 'false' : 'true'; ?>">

                        <?php if (! empty($tab['html'])) : ?>
                            <div class="product-detail__tab-html"><?php echo wp_kses_post($tab['html']); ?></div>

                        <?php elseif (! empty($tab['image'])) :
                            $img        = $tab['image'];
                            $img_url    = is_array($img) ? ($img['url'] ?? '') : (string) $img;
                            $img_alt    = is_array($img) ? ($img['alt'] ?: get_the_title()) : get_the_title();
                            $mob        = $tab['image_mobile'] ?? null;
                            $mob_url    = (is_array($mob) && ! empty($mob['url'])) ? $mob['url'] : '';
                        ?>
                            <picture>
                                <?php if ($mob_url) : ?>
                                    <source media="(max-width: 767px)" srcset="<?php echo esc_url($mob_url); ?>">
                                <?php endif; ?>
                                <img src="<?php echo esc_url($img_url); ?>"
                                    alt="<?php echo esc_attr($img_alt); ?>"
                                    class="product-detail__tab-image"
                                    loading="lazy">
                            </picture>

                        <?php elseif (! empty($tab['videos'])) : ?>
                            <div class="product-detail__video-grid">
                                <?php foreach ($tab['videos'] as $vid) :
                                    $thumb   = $vid['video_thumbnail'];
                                    $url     = trim($vid['video_url'] ?? '');
                                    $caption = $vid['video_caption'] ?? '';
                                    $thumb_url = is_array($thumb) ? ($thumb['sizes']['medium_large'] ?? $thumb['url'] ?? '') : '';
                                    $thumb_alt = is_array($thumb) ? ($thumb['alt'] ?? '') : '';
                                    if (! $thumb_url && $url) {
                                        $thumb_url = xclear_video_auto_thumbnail($url);
                                    }
                                ?>
                                    <button class="product-detail__video-card"
                                        data-video="<?php echo esc_attr($url); ?>"
                                        aria-label="<?php echo esc_attr($caption ?: __('Play video', 'xclear')); ?>">
                                        <div class="product-detail__video-thumb">
                                            <?php if ($thumb_url) : ?>
                                                <img src="<?php echo esc_url($thumb_url); ?>"
                                                    alt="<?php echo esc_attr($thumb_alt); ?>"
                                                    loading="lazy">
                                            <?php endif; ?>
                                            <span class="product-detail__video-play" aria-hidden="true">
                                                <svg width="20" height="22" viewBox="0 0 20 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M18.7811 8.87702C20.1708 9.63506 20.1708 11.6306 18.781 12.3886L2.9577 21.0195C1.62495 21.7465 8.56395e-08 20.7818 1.55879e-07 19.2637L9.5454e-07 2.0019C1.02478e-06 0.483779 1.62496 -0.480848 2.95771 0.246106L18.7811 8.87702Z" fill="white" />
                                                </svg>
                                            </span>
                                        </div>
                                        <?php if ($caption) : ?>
                                            <p class="product-detail__video-caption"><?php echo esc_html($caption); ?></p>
                                        <?php endif; ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>

                            <?php /* ── Video lightbox ── */ ?>
                            <div class="product-detail__lightbox" id="product-video-lightbox" aria-hidden="true">
                                <div class="product-detail__lightbox-inner">
                                    <button class="product-detail__lightbox-close" aria-label="Close">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                        </svg>
                                    </button>
                                    <div class="product-detail__lightbox-content"></div>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    <?php endif; ?>

</div>

<?php get_footer(); ?>