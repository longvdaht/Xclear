<?php
if (! defined('ABSPATH')) {
    exit;
}

/**
 * AJAX handler for the Article widget.
 * This file must be loaded unconditionally (from functions.php)
 * so the wp_ajax_* actions are always registered.
 */

add_action('wp_ajax_xclear_article_query', 'xclearArticleQueryAjax');
add_action('wp_ajax_nopriv_xclear_article_query', 'xclearArticleQueryAjax');

function xclearArticleQueryAjax(): void
{
    check_ajax_referer('xclear_article_nonce', 'nonce');

    $category       = sanitize_text_field($_POST['category']      ?? '');
    $search         = sanitize_text_field($_POST['search']        ?? '');
    $page           = max(1, (int)($_POST['page']                ?? 1));
    $posts_per_page = max(1, (int)($_POST['posts_per_page']      ?? 6));
    $post_type      = sanitize_text_field($_POST['post_type']    ?? 'post');
    $taxonomy       = sanitize_text_field($_POST['taxonomy']     ?? 'category');

    $args = [
        'post_type'      => $post_type,
        'post_status'    => 'publish',
        'posts_per_page' => $posts_per_page,
        'paged'          => $page,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];

    if ($search !== '') {
        $args['s'] = $search;
    }

    if ($category !== '') {
        $args['tax_query'] = [[
            'taxonomy' => $taxonomy,
            'field'    => 'slug',
            'terms'    => $category,
        ]];
    }

    $query = new WP_Query($args);
    $posts = [];

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $thumb_id  = get_post_thumbnail_id();
            $thumb_url = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'large') : '';

            $posts[] = [
                'id'        => get_the_ID(),
                'title'     => get_the_title(),
                'permalink' => get_permalink(),
                'excerpt'   => wp_trim_words(get_the_excerpt() ?: strip_tags(get_the_content()), 22),
                'thumbnail' => $thumb_url,
            ];
        }
        wp_reset_postdata();
    }

    wp_send_json_success([
        'posts'        => $posts,
        'total'        => (int)$query->found_posts,
        'max_pages'    => (int)$query->max_num_pages,
        'current_page' => $page,
    ]);
}
