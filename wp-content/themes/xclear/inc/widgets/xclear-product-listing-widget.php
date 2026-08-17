<?php
if (! defined('ABSPATH')) {
    exit;
}

class XclearProductListingWidget extends \Elementor\Widget_Base
{
    public function get_name(): string
    {
        return 'xclear_product_listing_widget';
    }

    public function get_title(): string
    {
        return esc_html__('Xclear Product Listing', 'xclear');
    }

    public function get_icon(): string
    {
        return 'eicon-products';
    }

    public function get_categories(): array
    {
        return ['xclear-widgets'];
    }

    public function get_style_depends(): array
    {
        return ['xclear-product-listing-widget-css'];
    }

    public function get_script_depends(): array
    {
        return ['xclear-product-listing-widget-js'];
    }

    protected function register_controls(): void
    {
        $this->start_controls_section('section_settings', [
            'label' => esc_html__('Settings', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('default_sort', [
            'label'   => esc_html__('Default Sort', 'xclear'),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'default',
            'options' => [
                'default' => esc_html__('Default', 'xclear'),
                'newest'  => esc_html__('Newest', 'xclear'),
                'a-z'     => esc_html__('A → Z', 'xclear'),
                'z-a'     => esc_html__('Z → A', 'xclear'),
            ],
        ]);

        $this->add_control('per_page', [
            'label'   => esc_html__('Items per Page', 'xclear'),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => '10',
            'options' => [
                '10' => '10',
                '20' => '20',
                '50' => '50',
            ],
        ]);

        $this->add_control('link_products', [
            'label'     => esc_html__('Link to Product Page', 'xclear'),
            'type'      => \Elementor\Controls_Manager::SWITCHER,
            'label_on'  => esc_html__('Yes', 'xclear'),
            'label_off' => esc_html__('No', 'xclear'),
            'default'   => 'yes',
        ]);

        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings      = $this->get_settings_for_display();
        $sort          = $settings['default_sort'] ?? 'default';
        $per_page      = intval($settings['per_page'] ?? 10);
        $link_products = ($settings['link_products'] ?? 'yes') === 'yes';

        $categories = get_terms([
            'taxonomy'   => 'product_category',
            'hide_empty' => true,
            'orderby'    => 'name',
            'order'      => 'ASC',
        ]);

        $customOrder = []; // custom order does not apply to "All Products"
        $query_args  = xclear_build_products_query('', $sort, 1, $per_page, $customOrder);
        $query       = new WP_Query($query_args);
        $total       = $query->found_posts;
        $total_pages = (int) $query->max_num_pages;
        $end         = min($per_page, $total);

        $sort_labels = [
            'default' => __('Default', 'xclear'),
            'newest'  => __('Newest', 'xclear'),
            'a-z'     => 'A → Z',
            'z-a'     => 'Z → A',
        ];
        $current_sort_label = $sort_labels[$sort] ?? __('Default', 'xclear');
?>
        <div class="xclear-product-listing"
            data-nonce="<?php echo esc_attr(wp_create_nonce('xclear_products_nonce')); ?>"
            data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>"
            data-link-products="<?php echo $link_products ? '1' : '0'; ?>"
            data-sort="<?php echo esc_attr($sort); ?>"
            data-per-page="<?php echo esc_attr($per_page); ?>"
            data-category=""
            data-page="1">

            <?php $this->renderFilters($categories); ?>

            <div class="xclear-product-listing__toolbar">
                <span class="xclear-product-listing__count">
                    <?php echo esc_html(sprintf(
                        _n('%s Product', '%s Products', $total, 'xclear'),
                        number_format($total)
                    )); ?>
                </span>
                <?php $this->renderSortDropdown($sort, $sort_labels, $current_sort_label); ?>
            </div>

            <div class="xclear-product-listing__grid">
                <?php xclear_render_products_grid($query, $link_products); ?>
            </div>

            <div class="xclear-product-listing__bottom-bar">
                <span class="xclear-product-listing__items-info">
                    <?php if ($total > 0) : ?>
                        <?php echo esc_html(sprintf(
                            __('Items %1$d to %2$d of %3$d', 'xclear'),
                            1,
                            $end,
                            $total
                        )); ?>
                    <?php endif; ?>
                </span>

                <div class="xclear-product-listing__pagination">
                    <?php xclear_render_products_pagination(1, $total_pages); ?>
                </div>

                <div class="xclear-product-listing__per-page-wrapper">
                    <span><?php esc_html_e('Show', 'xclear'); ?></span>
                    <select class="xclear-product-listing__per-page" aria-label="<?php esc_attr_e('Items per page', 'xclear'); ?>">
                        <?php foreach ([10, 20, 50] as $opt) : ?>
                            <option value="<?php echo esc_attr($opt); ?>" <?php selected($per_page, $opt); ?>>
                                <?php echo esc_html($opt); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    <?php

        wp_reset_postdata();
    }

    private function renderFilters($categories): void
    {
        $settings    = $this->get_settings_for_display();
        $defaultSort = $settings['default_sort'] ?? 'default';
    ?>
        <div class="xclear-product-listing__filters" role="tablist" aria-label="<?php esc_attr_e('Filter by category', 'xclear'); ?>">
            <button class="xclear-product-listing__filter-btn btn btn-light-green active"
                data-category=""
                data-default-sort="<?php echo esc_attr($defaultSort); ?>"
                role="tab"
                aria-selected="true">
                <?php esc_html_e('All Products', 'xclear'); ?>
            </button>
            <?php if (! is_wp_error($categories) && ! empty($categories)) : ?>
                <?php foreach ($categories as $cat) :
                    $catSort = get_field('product_category_default_sort', $cat) ?: 'default';
                ?>
                    <button class="xclear-product-listing__filter-btn btn btn-light-green"
                        data-category="<?php echo esc_attr($cat->slug); ?>"
                        data-default-sort="<?php echo esc_attr($catSort); ?>"
                        role="tab"
                        aria-selected="false">
                        <?php echo esc_html($cat->name); ?>
                    </button>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php
    }

    private function renderSortDropdown(string $sort, array $sort_labels, string $current_sort_label): void
    {
    ?>
        <div class="xclear-product-listing__sort-wrapper">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4.5 12H11.25" stroke="#263238" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M4.5 6H9.75" stroke="#263238" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M4.5 18H17.25" stroke="#263238" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M13.5 8.25L17.25 4.5L21 8.25" stroke="#263238" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M17.25 4.5V13.5" stroke="#263238" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>

            <span class="xclear-product-listing__sort-by-label"><?php esc_html_e('Sort by:', 'xclear'); ?></span>
            <button class="xclear-product-listing__sort-trigger"
                type="button"
                aria-haspopup="listbox"
                aria-expanded="false">
                <span class="xclear-product-listing__sort-label"><?php echo esc_html($current_sort_label); ?></span>
                <svg class="xclear-product-listing__sort-arrow" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>

            <div class="xclear-product-listing__sort-dropdown" role="listbox">
                <?php foreach ($sort_labels as $value => $label) : ?>
                    <button class="xclear-product-listing__sort-option<?php echo $sort === $value ? ' active' : ''; ?>"
                        data-sort="<?php echo esc_attr($value); ?>"
                        role="option"
                        aria-selected="<?php echo $sort === $value ? 'true' : 'false'; ?>">
                        <?php echo esc_html($label); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
<?php
    }
}
