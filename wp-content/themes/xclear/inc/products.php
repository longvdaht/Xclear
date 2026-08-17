<?php
if (! defined('ABSPATH')) {
    exit;
}

define('XCLEAR_ACF_INIT', 'acf/init');

// ---------------------------------------------------------------------------
// Field definitions — split into helpers to keep each function ≤ 150 lines
// ---------------------------------------------------------------------------

function xclear_product_fields_media() {
    return [
        [
            'key'           => 'field_product_gallery',
            'label'         => 'Gallery',
            'name'          => 'product_gallery',
            'type'          => 'gallery',
            'return_format' => 'array',
            'preview_size'  => 'thumbnail',
            'min'           => 0,
            'max'           => 0,
            'instructions'  => 'Additional product images shown in the thumbnail slider. The main display image comes from the Featured Image.',
        ],
    ];
}

function xclear_product_fields_info() {
    return [
        [
            'key'          => 'field_product_position',
            'label'        => 'Product Position',
            'name'         => 'product_position',
            'type'         => 'number',
            'default_value'=> '',
            'placeholder'  => '0',
            'min'          => '',
            'max'          => '',
            'step'         => 1,
            'instructions' => 'Display order for the product listing (lower number = displayed first). Products with the same position value are sorted from newest to oldest.',
        ],
        [
            'key'          => 'field_product_short_description',
            'label'        => 'Short Description',
            'name'         => 'product_short_description',
            'type'         => 'textarea',
            'rows'         => 3,
            'instructions' => 'Brief description shown below the product title.',
        ],
        [
            'key'          => 'field_product_advantages',
            'label'        => 'Advantages',
            'name'         => 'product_advantages',
            'type'         => 'wysiwyg',
            'tabs'         => 'all',
            'toolbar'      => 'full',
            'media_upload' => 1,
        ],
        [
            'key'          => 'field_product_advantages_2',
            'label'        => 'Advantages 2',
            'name'         => 'product_advantages_2',
            'type'         => 'wysiwyg',
            'tabs'         => 'all',
            'toolbar'      => 'full',
            'media_upload' => 1,
            'instructions' => 'Optional continuation of Advantages, for products where the content is too long for a single field.',
        ],
        [
            'key'           => 'field_product_manuals_file',
            'label'         => 'Manuals',
            'name'          => 'product_manuals_file',
            'type'          => 'file',
            'return_format' => 'array',
            'instructions'  => 'Upload the Manuals PDF file.',
        ],
        [
            'key'           => 'field_product_leaflet_file',
            'label'         => 'Leaflet',
            'name'          => 'product_leaflet_file',
            'type'          => 'file',
            'return_format' => 'array',
            'instructions'  => 'Upload the Leaflet PDF file.',
        ],
        [
            'key'   => 'field_product_buy_parts_url',
            'label' => 'Buy spare parts URL',
            'name'  => 'product_buy_parts_url',
            'type'  => 'url',
        ],
    ];
}

function xclear_product_fields_tabs() {
    return [
        [
            'key'           => 'field_product_specs_image',
            'label'         => 'Specs — Image (Desktop)',
            'name'          => 'product_specs_image',
            'type'          => 'image',
            'return_format' => 'array',
            'preview_size'  => 'medium',
            'instructions'  => 'Upload an image of the specs table.',
        ],
        [
            'key'           => 'field_product_specs_image_mobile',
            'label'         => 'Specs — Image (Mobile)',
            'name'          => 'product_specs_image_mobile',
            'type'          => 'image',
            'return_format' => 'array',
            'preview_size'  => 'medium',
            'instructions'  => 'Optional. Upload a portrait/cropped version for mobile. If left empty, the desktop image will be used.',
        ],
        [
            'key'          => 'field_product_specs_html',
            'label'        => 'Specs — Content (overrides image if filled)',
            'name'         => 'product_specs_html',
            'type'         => 'wysiwyg',
            'tabs'         => 'all',
            'toolbar'      => 'full',
            'media_upload' => 1,
            'instructions' => 'Leave empty to use the image above.',
        ],
        [
            'key'           => 'field_product_spare_parts_image',
            'label'         => 'Spare Parts — Image (Desktop)',
            'name'          => 'product_spare_parts_image',
            'type'          => 'image',
            'return_format' => 'array',
            'preview_size'  => 'medium',
        ],
        [
            'key'           => 'field_product_spare_parts_image_mobile',
            'label'         => 'Spare Parts — Image (Mobile)',
            'name'          => 'product_spare_parts_image_mobile',
            'type'          => 'image',
            'return_format' => 'array',
            'preview_size'  => 'medium',
            'instructions'  => 'Optional. Upload a portrait/cropped version for mobile.',
        ],
        [
            'key'          => 'field_product_spare_parts_html',
            'label'        => 'Spare Parts — Content (overrides image if filled)',
            'name'         => 'product_spare_parts_html',
            'type'         => 'wysiwyg',
            'tabs'         => 'all',
            'toolbar'      => 'full',
            'media_upload' => 1,
        ],
        [
            'key'           => 'field_product_data_table_image',
            'label'         => 'Data Table — Image (Desktop)',
            'name'          => 'product_data_table_image',
            'type'          => 'image',
            'return_format' => 'array',
            'preview_size'  => 'medium',
        ],
        [
            'key'           => 'field_product_data_table_image_mobile',
            'label'         => 'Data Table — Image (Mobile)',
            'name'          => 'product_data_table_image_mobile',
            'type'          => 'image',
            'return_format' => 'array',
            'preview_size'  => 'medium',
            'instructions'  => 'Optional. Upload a portrait/cropped version for mobile.',
        ],
        [
            'key'          => 'field_product_data_table_html',
            'label'        => 'Data Table — Content (overrides image if filled)',
            'name'         => 'product_data_table_html',
            'type'         => 'wysiwyg',
            'tabs'         => 'all',
            'toolbar'      => 'full',
            'media_upload' => 1,
        ],
        [
            'key'        => 'field_product_installation_videos',
            'label'      => 'Installation Videos',
            'name'       => 'product_installation_videos',
            'type'       => 'repeater',
            'min'        => 0,
            'max'        => 0,
            'layout'     => 'block',
            'sub_fields' => [
                [
                    'key'           => 'field_product_video_thumbnail',
                    'label'         => 'Thumbnail',
                    'name'          => 'video_thumbnail',
                    'type'          => 'image',
                    'return_format' => 'array',
                    'preview_size'  => 'medium',
                    'instructions'  => 'Cover image shown on the card.',
                ],
                [
                    'key'          => 'field_product_video_url',
                    'label'        => 'Video URL or Embed Code',
                    'name'         => 'video_url',
                    'type'         => 'textarea',
                    'rows'         => 3,
                    'instructions' => 'Paste a YouTube/Vimeo URL or full iframe embed code.',
                ],
                [
                    'key'   => 'field_product_video_caption',
                    'label' => 'Caption',
                    'name'  => 'video_caption',
                    'type'  => 'text',
                ],
            ],
        ],
    ];
}

// ---------------------------------------------------------------------------
// Register ACF field group
// ---------------------------------------------------------------------------

function xclear_register_product_acf_fields() {
    if (! function_exists('acf_add_local_field_group')) {
        return;
    }

    $fields = array_merge(
        xclear_product_fields_media(),
        xclear_product_fields_info(),
        xclear_product_fields_tabs()
    );

    acf_add_local_field_group([
        'key'                   => 'group_product_details',
        'title'                 => 'Product Details',
        'fields'                => $fields,
        'location'              => [
            [
                [
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'product',
                ],
            ],
        ],
        'menu_order'            => 0,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
    ]);
}
add_action(XCLEAR_ACF_INIT, 'xclear_register_product_acf_fields');

/**
 * Keep the product position field stable in admin by always storing a clean
 * integer value instead of a string or any implicit default value.
 */
function xclear_sanitize_product_position_value($value, $post_id, $field) {
    if (($field['name'] ?? '') !== 'product_position') {
        return $value;
    }

    if ($value === '' || $value === null) {
        if ($post_id) {
            delete_post_meta((int) $post_id, 'product_position');
        }

        return null;
    }

    return absint($value);
}
add_filter('acf/update_value/name=product_position', 'xclear_sanitize_product_position_value', 10, 3);

// ---------------------------------------------------------------------------
// Options page — global product settings
// ---------------------------------------------------------------------------

function xclear_register_product_options_page() {
    if (! function_exists('acf_add_options_page')) {
        return;
    }

    acf_add_options_sub_page([
        'page_title'  => 'Product Settings',
        'menu_title'  => 'Product Settings',
        'parent_slug' => 'edit.php?post_type=product',
        'capability'  => 'edit_posts',
    ]);
}
add_action(XCLEAR_ACF_INIT, 'xclear_register_product_options_page');

function xclear_register_product_global_fields() {
    if (! function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group([
        'key'    => 'group_product_global_settings',
        'title'  => 'Global Product Button Settings',
        'fields' => [
            [
                'key'           => 'field_global_request_info_url',
                'label'         => 'Request More Information Page',
                'name'          => 'global_request_info_url',
                'type'          => 'post_object',
                'post_type'     => ['page'],
                'return_format' => 'id',
                'ui'            => 1,
                'instructions'  => 'Pick the contact page. The link will automatically resolve to the current language version.',
            ],
            [
                'key'           => 'field_global_become_dealer_url',
                'label'         => 'Become a Dealer Page',
                'name'          => 'global_become_dealer_url',
                'type'          => 'post_object',
                'post_type'     => ['page'],
                'return_format' => 'id',
                'ui'            => 1,
                'instructions'  => 'Pick the contact/dealer page. The link will automatically resolve to the current language version.',
            ],
        ],
        'location' => [
            [
                [
                    'param'    => 'options_page',
                    'operator' => '==',
                    'value'    => 'acf-options-product-settings',
                ],
            ],
        ],
        'menu_order'            => 0,
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
    ]);
}
add_action(XCLEAR_ACF_INIT, 'xclear_register_product_global_fields');

/**
 * Resolve a global options "page_link" field to the permalink of the page
 * matching the current frontend language, falling back to the page picked
 * in the options screen if no translation exists.
 */
function xclear_resolve_global_page_link(string $fieldName): string
{
    $pageId = get_field($fieldName, 'option');
    if (! $pageId) {
        return '#';
    }

    if (function_exists('pll_current_language') && function_exists('pll_get_post')) {
        $translatedId = pll_get_post((int) $pageId, pll_current_language());
        if ($translatedId) {
            $pageId = $translatedId;
        }
    }

    return get_permalink($pageId) ?: '#';
}

// ---------------------------------------------------------------------------
// Product category — featured image field
// ---------------------------------------------------------------------------

function xclear_register_product_category_acf_fields() {
    if (! function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group([
        'key'    => 'group_product_category_image',
        'title'  => 'Category Image',
        'fields' => [
            [
                'key'           => 'field_product_category_image',
                'label'         => 'Featured Image',
                'name'          => 'product_category_image',
                'type'          => 'image',
                'return_format' => 'array',
                'preview_size'  => 'medium',
                'instructions'  => 'Representative image for this product category.',
            ],
            [
                'key'           => 'field_product_category_default_sort',
                'label'         => 'Default Sort',
                'name'          => 'product_category_default_sort',
                'type'          => 'select',
                'choices'       => [
                    'default' => 'Default (Position)',
                    'newest'  => 'Newest',
                    'a-z'     => 'A → Z',
                    'z-a'     => 'Z → A',
                    'custom'  => 'Custom Order',
                ],
                'default_value' => 'default',
                'allow_null'    => 0,
                'return_format' => 'value',
                'instructions'  => 'Default sort order when this category is selected in the product listing.',
            ],
            [
                'key'               => 'field_product_category_custom_order',
                'label'             => 'Custom Product Order',
                'name'              => 'product_category_custom_order',
                'type'              => 'relationship',
                'post_type'         => ['product'],
                'post_status'       => 'publish',
                'filters'           => ['search'],
                'return_format'     => 'id',
                'min'               => 0,
                'max'               => 0,
                'instructions'      => 'Select and arrange the display order of products. Drag and drop to reorder.',
                'conditional_logic' => [
                    [
                        [
                            'field'    => 'field_product_category_default_sort',
                            'operator' => '==',
                            'value'    => 'custom',
                        ],
                    ],
                ],
            ],
        ],
        'location' => [
            [
                [
                    'param'    => 'taxonomy',
                    'operator' => '==',
                    'value'    => 'product_category',
                ],
            ],
        ],
        'menu_order'            => 0,
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
    ]);
}
add_action(XCLEAR_ACF_INIT, 'xclear_register_product_category_acf_fields');

// Restrict relationship field to only show products belonging to the category being edited
add_filter('acf/fields/relationship/query', function ($args, $field, $postId) {
    if (($field['name'] ?? '') !== 'product_category_custom_order') {
        return $args;
    }

    $termId = 0;

    // ACF passes taxonomy terms as "term_{id}"
    $raw = is_string($postId) ? $postId : (sanitize_text_field(wp_unslash($_POST['post_id'] ?? '')));
    if (strpos($raw, 'term_') === 0) {
        $termId = (int) str_replace('term_', '', $raw);
    }

    if ($termId) {
        $args['tax_query'] = [
            [
                'taxonomy' => 'product_category',
                'field'    => 'term_id',
                'terms'    => $termId,
            ],
        ];
    }

    return $args;
}, 10, 3);
