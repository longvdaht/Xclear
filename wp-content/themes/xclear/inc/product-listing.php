<?php
if (! defined('ABSPATH')) {
    exit;
}

// ---------------------------------------------------------------------------
// Query builder
// ---------------------------------------------------------------------------

function xclear_build_products_query(string $category, string $sort, int $page, int $per_page, array $customOrder = []): array
{
    $args = [
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => $per_page,
        'paged'          => $page,
    ];

    switch ($sort) {
        case 'default':
            // Signal to the posts_clauses filter to inject a LEFT JOIN + COALESCE
            // so that products with no saved position (NULL) are treated as 0
            // and sorted together with position=0 products by date DESC.
            $args['xclear_default_sort'] = true;
            break;
        case 'a-z':
            $args['orderby'] = 'title';
            $args['order']   = 'ASC';
            break;
        case 'z-a':
            $args['orderby'] = 'title';
            $args['order']   = 'DESC';
            break;
        case 'custom':
            if (! empty($customOrder)) {
                $args['post__in'] = array_map('intval', $customOrder);
                $args['orderby']  = 'post__in';
            } else {
                // fallback to newest when no custom order is configured
                $args['orderby'] = 'date';
                $args['order']   = 'DESC';
            }
            break;
        default:
            $args['orderby'] = 'date';
            $args['order']   = 'DESC';
    }

    if (! empty($category)) {
        $args['tax_query'] = [
            [
                'taxonomy' => 'product_category',
                'field'    => 'slug',
                'terms'    => $category,
            ],
        ];
    }

    return $args;
}

// ---------------------------------------------------------------------------
// posts_clauses filter — default sort (position ASC, date DESC)
// ---------------------------------------------------------------------------
add_filter('posts_clauses', function (array $clauses, WP_Query $query): array {
    if (! $query->get('xclear_default_sort')) {
        return $clauses;
    }

    global $wpdb;

    // LEFT JOIN so products with no saved meta row still appear.
    // Products with no position should be pushed to the end, not treated as 0.
    $clauses['join'] .= " LEFT JOIN {$wpdb->postmeta} AS xclear_pm_pos
        ON ( {$wpdb->posts}.ID = xclear_pm_pos.post_id
             AND xclear_pm_pos.meta_key = 'product_position' )";

    // Explicit positions are sorted first, products with no position are sent to the end.
    // Within the same position, newest product is shown first.
    $clauses['orderby'] = "CASE
            WHEN xclear_pm_pos.meta_value IS NULL OR xclear_pm_pos.meta_value = '' THEN 1
            ELSE 0
        END ASC,
        CAST(COALESCE(xclear_pm_pos.meta_value, 9999999) AS DECIMAL(10,2)) ASC,
        {$wpdb->posts}.post_date DESC";

    return $clauses;
}, 10, 2);

// ---------------------------------------------------------------------------
// Render helpers
// ---------------------------------------------------------------------------

function xclear_render_products_grid(WP_Query $query, bool $link_products = true): void
{
    if (! $query->have_posts()) {
        echo '<div class="xclear-product-listing__no-results">No products found.</div>';
        return;
    }

    while ($query->have_posts()) {
        $query->the_post();
        xclear_render_product_card(get_the_ID(), $link_products);
    }
}

function xclear_render_product_card(int $post_id, bool $link_products = true): void
{
    $title             = get_the_title($post_id);
    $short_description = get_field('product_short_description', $post_id);
    $thumbnail         = get_the_post_thumbnail_url($post_id, 'large');
    $permalink         = get_permalink($post_id);
    ?>
    <div class="xclear-product-listing__card">
        <?php if ($link_products) : ?>
        <a href="<?php echo esc_url($permalink); ?>" class="xclear-product-listing__card-link">
        <?php endif; ?>

        <div class="xclear-product-listing__card-image">
            <?php if ($thumbnail) : ?>
                <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy">
            <?php else : ?>
                <div class="xclear-product-listing__card-image-placeholder"></div>
            <?php endif; ?>
        </div>

        <div class="xclear-product-listing__card-body">
            <h3 class="xclear-product-listing__card-title"><?php echo esc_html($title); ?></h3>
            <?php if ($short_description) : ?>
                <div class="xclear-product-listing__card-desc"><?php echo wp_kses_post($short_description); ?></div>
            <?php endif; ?>
        </div>

        <?php if ($link_products) : ?>
        </a>
        <?php endif; ?>
    </div>
    <?php
}

function xclear_render_products_pagination(int $current_page, int $total_pages): void
{
    if ($total_pages <= 1) {
        return;
    }

    $pages = xclear_get_pagination_pages($current_page, $total_pages);

    $prev_disabled = $current_page <= 1 ? ' disabled' : '';
    $next_disabled = $current_page >= $total_pages ? ' disabled' : '';
    ?>
    <button class="xclear-product-listing__page-btn xclear-product-listing__page-prev"
            data-page="<?php echo max(1, $current_page - 1); ?>"
            aria-label="Previous page"
            <?php echo $current_page <= 1 ? 'disabled' : ''; ?>>
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>

    <?php foreach ($pages as $page) : ?>
        <?php if ($page === '...') : ?>
            <span class="xclear-product-listing__page-ellipsis">…</span>
        <?php else : ?>
            <button class="xclear-product-listing__page-btn<?php echo $page === $current_page ? ' active' : ''; ?>"
                    data-page="<?php echo intval($page); ?>">
                <?php echo intval($page); ?>
            </button>
        <?php endif; ?>
    <?php endforeach; ?>

    <button class="xclear-product-listing__page-btn xclear-product-listing__page-next"
            data-page="<?php echo min($total_pages, $current_page + 1); ?>"
            aria-label="Next page"
            <?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>>
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M6 4L10 8L6 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>
    <?php
}

function xclear_get_pagination_pages(int $current, int $total): array
{
    if ($total <= 5) {
        return range(1, $total);
    }

    $pages = [1];

    if ($current > 3) {
        $pages[] = '...';
    }

    $start = max(2, $current - 1);
    $end   = min($total - 1, $current + 1);

    // Ensure at least 2 pages appear in the middle window so that near-edge
    // states render as "1 2 3 … last" rather than "1 2 … last".
    if ($end - $start < 1) {
        if ($start <= 2) {
            $end = min($total - 1, $start + 1);
        } else {
            $start = max(2, $end - 1);
        }
    }

    for ($i = $start; $i <= $end; $i++) {
        $pages[] = $i;
    }

    if ($current < $total - 2) {
        $pages[] = '...';
    }

    $pages[] = $total;

    return $pages;
}

// ---------------------------------------------------------------------------
// AJAX handler
// ---------------------------------------------------------------------------

function xclear_get_products_ajax(): void
{
    check_ajax_referer('xclear_products_nonce', 'nonce');

    $allowed_sorts    = ['default', 'newest', 'a-z', 'z-a', 'custom'];
    $allowed_per_page = [10, 20, 50];

    $category      = sanitize_text_field(wp_unslash($_POST['category'] ?? ''));
    $sort          = sanitize_text_field(wp_unslash($_POST['sort'] ?? 'default'));
    $page          = max(1, intval($_POST['page'] ?? 1));
    $per_page      = intval($_POST['per_page'] ?? 10);
    $link_products = ! empty($_POST['link_products']) && $_POST['link_products'] === '1';

    if (! in_array($sort, $allowed_sorts, true)) {
        $sort = 'default';
    }

    if (! in_array($per_page, $allowed_per_page, true)) {
        $per_page = 10;
    }

    $customOrder = [];
    if ($sort === 'custom' && ! empty($category)) {
        $term = get_term_by('slug', $category, 'product_category');
        if ($term) {
            $customOrder = (array) (get_field('product_category_custom_order', $term) ?: []);
        }
    }

    $query_args  = xclear_build_products_query($category, $sort, $page, $per_page, $customOrder);
    $query       = new WP_Query($query_args);
    $total       = $query->found_posts;
    $total_pages = (int) $query->max_num_pages;
    $start       = $total > 0 ? ($page - 1) * $per_page + 1 : 0;
    $end         = min($page * $per_page, $total);

    ob_start();
    xclear_render_products_grid($query, $link_products);
    $grid_html = ob_get_clean();

    ob_start();
    xclear_render_products_pagination($page, $total_pages);
    $pagination_html = ob_get_clean();

    wp_reset_postdata();

    wp_send_json_success([
        'grid_html'       => $grid_html,
        'pagination_html' => $pagination_html,
        'total'           => $total,
        'total_pages'     => $total_pages,
        'current_page'    => $page,
        'per_page'        => $per_page,
        'start'           => $start,
        'end'             => $end,
    ]);
}
add_action('wp_ajax_xclear_get_products', 'xclear_get_products_ajax');
add_action('wp_ajax_nopriv_xclear_get_products', 'xclear_get_products_ajax');
