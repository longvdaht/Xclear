<?php
if (! defined('ABSPATH')) {
    exit;
}

class XclearListCategoryWidget extends \Elementor\Widget_Base
{
    private const COLOR_VALUE = 'color: {{VALUE}};';

    public function get_name()
    {
        return 'xclear_list_category_widget';
    }

    public function get_title()
    {
        return esc_html__('Xclear List Category', 'xclear');
    }

    public function get_icon()
    {
        return 'eicon-bullet-list';
    }

    public function get_categories()
    {
        return ['xclear-widgets'];
    }

    public function get_style_depends()
    {
        return ['xclear-list-category-widget-css'];
    }

    protected function register_controls()
    {
        $this->registerHeaderSection();
        $this->registerBodySection();
        $this->registerStyleHeaderSection();
        $this->registerStyleBodySection();
    }

    /* ─── Content: Header ───────────────────────────────────────────────── */

    private function registerHeaderSection(): void
    {
        $this->start_controls_section('header_section', [
            'label' => esc_html__('Header', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('badge_text', [
            'label'   => esc_html__('Badge Text', 'xclear'),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => esc_html__('PRODUCTS', 'xclear'),
        ]);

        $this->add_control('heading', [
            'label'       => esc_html__('Heading', 'xclear'),
            'description' => esc_html__('Wrap words with [hl]...[/hl] to apply the highlight color.', 'xclear'),
            'type'        => \Elementor\Controls_Manager::TEXTAREA,
            'rows'        => 3,
            'default'     => 'Xclear products for [hl]natural swimming ponds[/hl]',
        ]);

        $this->add_control('heading_tag', [
            'label'   => esc_html__('Heading Tag', 'xclear'),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'h2',
            'options' => [
                'h1' => 'H1',
                'h2' => 'H2',
                'h3' => 'H3',
            ],
        ]);

        $this->add_control('button_text', [
            'label'     => esc_html__('Button Text', 'xclear'),
            'type'      => \Elementor\Controls_Manager::TEXT,
            'default'   => esc_html__('Explore Xclear product range', 'xclear'),
            'separator' => 'before',
        ]);

        $this->add_control('button_url', [
            'label'     => esc_html__('Button URL', 'xclear'),
            'type'      => \Elementor\Controls_Manager::URL,
            'default'   => ['url' => '#'],
            'condition' => ['button_text!' => ''],
        ]);

        $this->add_control('header_right_type', [
            'label'     => esc_html__('Right Side', 'xclear'),
            'type'      => \Elementor\Controls_Manager::SELECT,
            'default'   => 'button',
            'options'   => [
                'button' => esc_html__('Button', 'xclear'),
                'text'   => esc_html__('Description Text', 'xclear'),
            ],
            'separator' => 'before',
        ]);

        $this->add_control('header_description', [
            'label'     => esc_html__('Description', 'xclear'),
            'type'      => \Elementor\Controls_Manager::WYSIWYG,
            'default'   => '',
            'condition' => ['header_right_type' => 'text'],
        ]);

        $this->end_controls_section();
    }

    /* ─── Content: Body ─────────────────────────────────────────────────── */

    private function registerBodySection(): void
    {
        $this->start_controls_section('body_section', [
            'label' => esc_html__('Categories Grid', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('columns', [
            'label'   => esc_html__('Columns', 'xclear'),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => '3',
            'options' => [
                '3' => esc_html__('3 Columns', 'xclear'),
                '2' => esc_html__('2 Columns', 'xclear'),
            ],
            'separator' => 'after',
        ]);

        $this->add_control('show_description', [
            'label'        => esc_html__('Show Description', 'xclear'),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'label_on'     => esc_html__('Show', 'xclear'),
            'label_off'    => esc_html__('Hide', 'xclear'),
            'return_value' => 'yes',
            'default'      => 'yes',
        ]);

        $this->add_control('description_limit', [
            'label'       => esc_html__('Description Word Limit', 'xclear'),
            'description' => esc_html__('0 = show full description', 'xclear'),
            'type'        => \Elementor\Controls_Manager::NUMBER,
            'default'     => 0,
            'min'         => 0,
            'step'        => 1,
            'condition'   => ['show_description' => 'yes'],
        ]);

        $this->add_control('categories_notice', [
            'type'            => \Elementor\Controls_Manager::RAW_HTML,
            'raw'             => '<small style="color:#aaa">Drag rows to reorder. Leave Custom Description empty to use the category\'s original description.</small>',
            'separator'       => 'before',
            'content_classes' => 'elementor-descriptor',
        ]);

        // Build SELECT options from all product_category terms
        $term_options = ['' => '— Select Category —'];
        $all_terms    = get_terms(['taxonomy' => 'product_category', 'hide_empty' => false]);
        if (! is_wp_error($all_terms)) {
            foreach ($all_terms as $t) {
                $term_options[$t->slug] = $t->name;
            }
        }

        // Build SELECT options from all published products
        $product_options = ['' => '— Select Product —'];
        $all_products    = get_posts(['post_type' => 'product', 'post_status' => 'publish', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC']);
        foreach ($all_products as $p) {
            $product_options[$p->ID] = $p->post_title;
        }

        $repeater = new \Elementor\Repeater();

        $repeater->add_control('item_type', [
            'label'   => esc_html__('Type', 'xclear'),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'category',
            'options' => [
                'category' => esc_html__('Category', 'xclear'),
                'product'  => esc_html__('Product', 'xclear'),
            ],
        ]);

        $repeater->add_control('category_slug', [
            'label'     => esc_html__('Category', 'xclear'),
            'type'      => \Elementor\Controls_Manager::SELECT,
            'options'   => $term_options,
            'default'   => '',
            'condition' => ['item_type' => 'category'],
        ]);

        $repeater->add_control('product_id', [
            'label'     => esc_html__('Product', 'xclear'),
            'type'      => \Elementor\Controls_Manager::SELECT,
            'options'   => $product_options,
            'default'   => '',
            'condition' => ['item_type' => 'product'],
        ]);

        $repeater->add_control('custom_description', [
            'label'   => esc_html__('Custom Description', 'xclear'),
            'type'    => \Elementor\Controls_Manager::TEXTAREA,
            'rows'    => 3,
            'default' => '',
        ]);

        $this->add_control('card_button_text', [
            'label'     => esc_html__('Card Button Text', 'xclear'),
            'type'      => \Elementor\Controls_Manager::TEXT,
            'default'   => esc_html__('More information', 'xclear'),
            'condition' => ['columns' => '2'],
            'separator' => 'before',
        ]);

        $this->add_control('categories', [
            'label'       => esc_html__('Categories', 'xclear'),
            'type'        => \Elementor\Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'default'     => [],
            'title_field' => '{{{ category_slug || "— empty —" }}}',
        ]);

        $this->end_controls_section();
    }

    /* ─── Style: Header ─────────────────────────────────────────────────── */

    private function registerStyleHeaderSection(): void
    {
        $this->start_controls_section('style_badge_section', [
            'label' => esc_html__('Badge', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('badge_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-list-category__badge'     => self::COLOR_VALUE,
                '{{WRAPPER}} .xclear-list-category__badge-dot' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('badge_bg_color', [
            'label'     => esc_html__('Background Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-list-category__badge' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();

        $this->start_controls_section('style_heading_section', [
            'label' => esc_html__('Heading', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'heading_typography',
            'selector' => '{{WRAPPER}} .xclear-list-category__heading',
        ]);

        $this->add_control('heading_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-list-category__heading' => self::COLOR_VALUE,
            ],
        ]);

        $this->add_control('hl_color', [
            'label'     => esc_html__('Highlight Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'separator' => 'before',
            'selectors' => [
                '{{WRAPPER}} .xclear-list-category__heading .xclear-hl' => self::COLOR_VALUE,
            ],
        ]);

        $this->end_controls_section();

        $this->start_controls_section('style_button_section', [
            'label' => esc_html__('Button', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('button_bg_color', [
            'label'     => esc_html__('Background Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-list-category__btn' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('button_color', [
            'label'     => esc_html__('Text Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-list-category__btn' => self::COLOR_VALUE,
            ],
        ]);

        $this->end_controls_section();
    }

    /* ─── Style: Body ───────────────────────────────────────────────────── */

    private function registerStyleBodySection(): void
    {
        $this->start_controls_section('style_card_section', [
            'label' => esc_html__('Category Cards', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('card_bg_color', [
            'label'     => esc_html__('Card Background', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-list-category__card'      => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .xclear-list-category__card-body' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('card_image_bg_color', [
            'label'     => esc_html__('Image Area Background', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-list-category__card-image' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();

        $this->start_controls_section('style_card_name_section', [
            'label' => esc_html__('Category Name', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'card_name_typography',
            'selector' => '{{WRAPPER}} .xclear-list-category__card-name',
        ]);

        $this->add_control('card_name_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-list-category__card-name' => self::COLOR_VALUE,
            ],
        ]);

        $this->end_controls_section();

        $this->start_controls_section('style_card_desc_section', [
            'label' => esc_html__('Category Description', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'card_desc_typography',
            'selector' => '{{WRAPPER}} .xclear-list-category__card-desc',
        ]);

        $this->add_control('card_desc_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-list-category__card-desc' => self::COLOR_VALUE,
            ],
        ]);

        $this->end_controls_section();
    }

    /* ─── Render ────────────────────────────────────────────────────────── */

    protected function render(): void
    {
        $settings    = $this->get_settings_for_display();
        $badge       = $settings['badge_text'] ?? '';
        $heading     = $settings['heading'] ?? '';
        $heading_tag = $settings['heading_tag'] ?? 'h2';
        $btn_text    = $settings['button_text'] ?? '';
        $btn_url     = $settings['button_url']['url'] ?? '#';
        $btn_target  = ($settings['button_url']['is_external'] ?? '') === 'on' ? '_blank' : '_self';
        $columns          = in_array($settings['columns'] ?? '3', ['2', '3']) ? $settings['columns'] : '3';
        $show_desc        = ($settings['show_description'] ?? 'yes') === 'yes';
        $desc_limit       = (int) ($settings['description_limit'] ?? 0);
        $items            = $settings['categories'] ?? [];
        $header_right     = $settings['header_right_type'] ?? 'button';
        $header_desc      = $settings['header_description'] ?? '';
        $card_btn_txt     = trim($settings['card_button_text'] ?? 'More information');

        $heading_tag  = in_array($heading_tag, ['h1', 'h2', 'h3']) ? $heading_tag : 'h2';
        $heading_html = preg_replace(
            '/\[hl\](.*?)\[\/hl\]/s',
            '<mark class="xclear-hl">$1</mark>',
            esc_html($heading)
        );

        // Build the list of terms to render, in user-defined order.
        // Falls back to querying all terms if the repeater is empty.
        if (! empty($items)) {
            $terms_to_render = $this->buildTermListFromRepeater($items);
        } else {
            $terms_to_render = $this->buildTermListFromQuery();
        }
        ?>
        <div class="xclear-list-category">

            <div class="xclear-list-category__header">

                <?php if (! empty($badge)) : ?>
                    <div class="xclear-list-category__badge">
                        <span class="xclear-list-category__badge-dot"></span>
                        <?php echo esc_html($badge); ?>
                    </div>
                <?php endif; ?>

                <div class="xclear-list-category__header-row">
                    <?php if (! empty($heading)) : ?>
                        <<?php echo $heading_tag; ?> class="xclear-list-category__heading">
                            <?php echo nl2br($heading_html); ?>
                        </<?php echo $heading_tag; ?>>
                    <?php endif; ?>

                    <?php if ($header_right === 'button' && ! empty($btn_text)) : ?>
                        <a href="<?php echo esc_url($btn_url); ?>"
                           target="<?php echo esc_attr($btn_target); ?>"
                           class="xclear-list-category__btn">
                            <?php echo esc_html($btn_text); ?>
                        </a>
                    <?php elseif ($header_right === 'text' && ! empty($header_desc)) : ?>
                        <div class="xclear-list-category__header-desc">
                            <?php echo wp_kses_post($header_desc); ?>
                        </div>
                    <?php endif; ?>
                </div>

            </div>

            <?php if (! empty($terms_to_render)) : ?>
                <div class="xclear-list-category__grid xclear-list-category__grid--cols-<?php echo esc_attr($columns); ?>">
                    <?php foreach ($terms_to_render as $entry) :
                        $img_url = $entry['img'];
                        $img_alt = $entry['alt'];

                        $desc = $entry['desc'];
                        if ($desc_limit > 0 && ! empty($desc)) {
                            $words = explode(' ', $desc);
                            if (count($words) > $desc_limit) {
                                $desc = implode(' ', array_slice($words, 0, $desc_limit)) . '…';
                            }
                        }
                    ?>
                        <a href="<?php echo esc_url($entry['link']); ?>"
                           class="xclear-list-category__card">

                            <div class="xclear-list-category__card-image">
                                <?php if (! empty($img_url)) : ?>
                                    <img src="<?php echo esc_url($img_url); ?>"
                                         alt="<?php echo esc_attr($img_alt); ?>">
                                <?php endif; ?>
                            </div>

                            <div class="xclear-list-category__card-body">
                                <div class="xclear-list-category__card-content">
                                    <h3 class="xclear-list-category__card-name">
                                        <?php echo esc_html($entry['name']); ?>
                                    </h3>
                                    <?php if ($show_desc && ! empty($desc)) : ?>
                                        <p class="xclear-list-category__card-desc">
                                            <?php echo esc_html($desc); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <?php if ($columns === '2' && ! empty($card_btn_txt)) : ?>
                                    <span class="xclear-list-category__card-btn">
                                        <?php echo esc_html($card_btn_txt); ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
        <?php
    }

    /**
     * Build card list from repeater items (user-defined order + custom descriptions).
     * Returns a unified format for both categories and products.
     *
     * @param array $items Repeater field value.
     * @return array<array{name: string, link: string, img: string, alt: string, desc: string}>
     */
    private function buildTermListFromRepeater(array $items): array
    {
        $result = [];

        foreach ($items as $item) {
            $type   = $item['item_type'] ?? 'category';
            $custom = trim($item['custom_description'] ?? '');

            if ($type === 'product') {
                $post_id = (int) ($item['product_id'] ?? 0);
                if (! $post_id) {
                    continue;
                }
                $post = get_post($post_id);
                if (! $post || $post->post_status !== 'publish') {
                    continue;
                }

                $img_url = get_the_post_thumbnail_url($post_id, 'large') ?: '';
                $desc    = $custom !== '' ? wp_strip_all_tags($custom) : wp_strip_all_tags(get_the_excerpt($post));

                $result[] = [
                    'name' => $post->post_title,
                    'link' => get_permalink($post) ?: '#',
                    'img'  => $img_url,
                    'alt'  => esc_attr($post->post_title),
                    'desc' => $desc,
                ];
            } else {
                $slug = trim($item['category_slug'] ?? '');
                if (empty($slug)) {
                    continue;
                }
                $term = get_term_by('slug', $slug, 'product_category');
                if (! $term || is_wp_error($term)) {
                    continue;
                }

                $image   = function_exists('get_field') ? get_field('product_category_image', 'product_category_' . $term->term_id) : null;
                $img_url = $image['sizes']['large'] ?? $image['url'] ?? '';
                $desc    = $custom !== '' ? wp_strip_all_tags($custom) : wp_strip_all_tags($term->description);
                $link    = get_term_link($term);

                $result[] = [
                    'name' => $term->name,
                    'link' => is_wp_error($link) ? '#' : $link,
                    'img'  => $img_url,
                    'alt'  => esc_attr($image['alt'] ?? $term->name),
                    'desc' => $desc,
                ];
            }
        }

        return $result;
    }

    /**
     * Fall-back: return all product_category terms ordered by name.
     *
     * @return array<array{name: string, link: string, img: string, alt: string, desc: string}>
     */
    private function buildTermListFromQuery(): array
    {
        $terms = get_terms([
            'taxonomy'   => 'product_category',
            'hide_empty' => false,
            'orderby'    => 'name',
            'order'      => 'ASC',
        ]);

        if (is_wp_error($terms) || empty($terms)) {
            return [];
        }

        $result = [];
        foreach ($terms as $term) {
            $image   = function_exists('get_field') ? get_field('product_category_image', 'product_category_' . $term->term_id) : null;
            $img_url = $image['sizes']['large'] ?? $image['url'] ?? '';
            $link    = get_term_link($term);

            $result[] = [
                'name' => $term->name,
                'link' => is_wp_error($link) ? '#' : $link,
                'img'  => $img_url,
                'alt'  => esc_attr($image['alt'] ?? $term->name),
                'desc' => wp_strip_all_tags($term->description),
            ];
        }

        return $result;
    }
}
