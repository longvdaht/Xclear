<?php
if (! defined('ABSPATH')) {
    exit;
}

class XclearNavDropdownWidget extends \Elementor\Widget_Base
{
    public function get_name(): string
    {
        return 'xclear_nav_dropdown_widget';
    }

    public function get_title(): string
    {
        return esc_html__('Xclear Nav Menu', 'xclear');
    }

    public function get_icon(): string
    {
        return 'eicon-nav-menu';
    }

    public function get_categories(): array
    {
        return ['xclear-widgets'];
    }

    public function get_style_depends(): array
    {
        return ['xclear-nav-dropdown-widget-css'];
    }

    public function get_script_depends(): array
    {
        return ['xclear-nav-dropdown-widget-js'];
    }

    protected function register_controls(): void
    {
        /* ── Content: Items ─────────────────────────────────────────── */
        $this->start_controls_section('section_items', [
            'label' => esc_html__('Menu Items', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $repeater = new \Elementor\Repeater();

        $repeater->add_control('item_level', [
            'label'   => esc_html__('Level', 'xclear'),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'top',
            'options' => [
                'top' => esc_html__('Top Level', 'xclear'),
                'sub' => esc_html__('Sub Item', 'xclear'),
            ],
        ]);

        $repeater->add_control('item_label', [
            'label'       => esc_html__('Label', 'xclear'),
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => esc_html__('Menu Item', 'xclear'),
            'placeholder' => esc_html__('Enter label', 'xclear'),
        ]);

        $repeater->add_control('link_type', [
            'label'   => esc_html__('Link Type', 'xclear'),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'page',
            'options' => [
                'page'   => esc_html__('Select Page', 'xclear'),
                'custom' => esc_html__('Custom URL', 'xclear'),
                'none'   => esc_html__('No Link', 'xclear'),
            ],
        ]);

        $repeater->add_control('link_page', [
            'label'     => esc_html__('Page', 'xclear'),
            'type'      => \Elementor\Controls_Manager::SELECT2,
            'options'   => $this->getPageOptions(),
            'default'   => '',
            'condition' => ['link_type' => 'page'],
        ]);

        $repeater->add_control('link_custom', [
            'label'       => esc_html__('Path', 'xclear'),
            'type'        => \Elementor\Controls_Manager::TEXT,
            'placeholder' => '/about-us',
            'description' => sprintf(
                esc_html__('Domain: %s', 'xclear'),
                esc_html(trailingslashit(home_url()))
            ),
            'default'     => '',
            'condition'   => ['link_type' => 'custom'],
        ]);

        $this->add_control('items', [
            'label'       => esc_html__('Items', 'xclear'),
            'type'        => \Elementor\Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'default'     => [
                ['item_level' => 'top', 'item_label' => 'Products',    'link_type' => 'none'],
                ['item_level' => 'sub', 'item_label' => 'Koi Pond',    'link_type' => 'page'],
                ['item_level' => 'sub', 'item_label' => 'Aquaculture', 'link_type' => 'page'],
                ['item_level' => 'top', 'item_label' => 'About us',    'link_type' => 'page'],
            ],
            'title_field' => '<# if (item_level === "sub") { #>↳ {{{ item_label }}}<# } else { #>{{{ item_label }}}<# } #>',
        ]);

        $this->end_controls_section();

        /* ── Settings ───────────────────────────────────────────────── */
        $this->start_controls_section('section_settings', [
            'label' => esc_html__('Settings', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('open_on', [
            'label'   => esc_html__('Open Dropdown on', 'xclear'),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'hover',
            'options' => [
                'hover' => esc_html__('Hover', 'xclear'),
                'click' => esc_html__('Click', 'xclear'),
            ],
        ]);

        $this->add_control('layout', [
            'label'   => esc_html__('Layout', 'xclear'),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'horizontal',
            'options' => [
                'horizontal' => esc_html__('Horizontal', 'xclear'),
                'vertical'   => esc_html__('Vertical', 'xclear'),
            ],
        ]);

        $this->add_control('mobile_lang_switcher', [
            'label'        => esc_html__('Show Language Switcher in Mobile Drawer', 'xclear'),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'label_on'     => esc_html__('Show', 'xclear'),
            'label_off'    => esc_html__('Hide', 'xclear'),
            'return_value' => 'yes',
            'default'      => 'no',
            'separator'    => 'before',
        ]);

        $this->add_control('mobile_lang_format', [
            'label'     => esc_html__('Language Display', 'xclear'),
            'type'      => \Elementor\Controls_Manager::SELECT,
            'default'   => 'slug',
            'options'   => [
                'slug' => esc_html__('Code (EN, NL)', 'xclear'),
                'name' => esc_html__('Full Name', 'xclear'),
            ],
            'condition' => ['mobile_lang_switcher' => 'yes'],
        ]);

        $this->end_controls_section();

        /* ── Content: Custom buttons (mobile drawer only) ───────────── */
        $this->start_controls_section('section_custom_buttons', [
            'label' => esc_html__('Custom Button', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('custom_buttons_notice', [
            'type'            => \Elementor\Controls_Manager::RAW_HTML,
            'raw'             => esc_html__('These buttons only show on mobile, below the language switcher.', 'xclear'),
            'content_classes' => 'elementor-descriptor',
        ]);

        $this->add_control('custom_buttons', [
            'label'        => esc_html__('Show Buttons', 'xclear'),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'label_on'     => esc_html__('Show', 'xclear'),
            'label_off'    => esc_html__('Hide', 'xclear'),
            'return_value' => 'yes',
            'default'      => 'no',
        ]);

        foreach ([1, 2] as $index) {
            $this->add_control("btn{$index}_heading", [
                'label'     => sprintf(esc_html__('Button %d', 'xclear'), $index),
                'type'      => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => ['custom_buttons' => 'yes'],
            ]);

            $this->add_control("btn{$index}_label", [
                'label'       => esc_html__('Text', 'xclear'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => 1 === $index
                    ? esc_html__('Become a Dealer', 'xclear')
                    : esc_html__('Contact Us', 'xclear'),
                'placeholder' => esc_html__('Enter text', 'xclear'),
                'condition'   => ['custom_buttons' => 'yes'],
            ]);

            $this->add_control("btn{$index}_link_type", [
                'label'     => esc_html__('Link Type', 'xclear'),
                'type'      => \Elementor\Controls_Manager::SELECT,
                'default'   => 'page',
                'options'   => [
                    'page'   => esc_html__('Select Page', 'xclear'),
                    'custom' => esc_html__('Custom URL', 'xclear'),
                ],
                'condition' => ['custom_buttons' => 'yes'],
            ]);

            $this->add_control("btn{$index}_link_page", [
                'label'     => esc_html__('Page', 'xclear'),
                'type'      => \Elementor\Controls_Manager::SELECT2,
                'options'   => $this->getPageOptions(),
                'default'   => '',
                'condition' => ['custom_buttons' => 'yes', "btn{$index}_link_type" => 'page'],
            ]);

            $this->add_control("btn{$index}_link_custom", [
                'label'       => esc_html__('Path', 'xclear'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'placeholder' => '/contact-us',
                'description' => sprintf(
                    esc_html__('Domain: %s', 'xclear'),
                    esc_html(trailingslashit(home_url()))
                ),
                'default'     => '',
                'condition'   => ['custom_buttons' => 'yes', "btn{$index}_link_type" => 'custom'],
            ]);
        }

        $this->end_controls_section();

        /* ── Style: Top-level items ─────────────────────────────────── */
        $this->start_controls_section('style_label', [
            'label' => esc_html__('Top-level Items', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('items_gap', [
            'label'      => esc_html__('Gap Between Items', 'xclear'),
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range'      => ['px' => ['min' => 0, 'max' => 80]],
            'default'    => ['size' => 28, 'unit' => 'px'],
            'selectors'  => [
                '{{WRAPPER}} .xclear-nav-menu' => 'gap: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('label_color', [
            'label'     => esc_html__('Text Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#263238',
            'selectors' => [
                '{{WRAPPER}} .xclear-nav-menu__label' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('label_color_hover', [
            'label'     => esc_html__('Text Color (Hover)', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#1f2124',
            'selectors' => [
                '{{WRAPPER}} .xclear-nav-menu__item:hover .xclear-nav-menu__label'   => 'color: {{VALUE}};',
                '{{WRAPPER}} .xclear-nav-menu__item.is-open .xclear-nav-menu__label' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'label_typography',
            'selector' => '{{WRAPPER}} .xclear-nav-menu__label',
        ]);

        $this->end_controls_section();

        /* ── Style: Dropdown ────────────────────────────────────────── */
        $this->start_controls_section('style_dropdown', [
            'label' => esc_html__('Dropdown', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('dropdown_bg', [
            'label'     => esc_html__('Background', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .xclear-nav-menu__submenu' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('dropdown_item_color', [
            'label'     => esc_html__('Item Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#263238',
            'selectors' => [
                '{{WRAPPER}} .xclear-nav-menu__sublink' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('dropdown_item_hover_color', [
            'label'     => esc_html__('Item Hover Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#1f2124',
            'selectors' => [
                '{{WRAPPER}} .xclear-nav-menu__sublink:hover' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('dropdown_item_hover_bg', [
            'label'     => esc_html__('Item Hover Background', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => 'rgba(28,74,75,0.06)',
            'selectors' => [
                '{{WRAPPER}} .xclear-nav-menu__sublink:hover' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'subitem_typography',
            'label'    => esc_html__('Item Typography', 'xclear'),
            'selector' => '{{WRAPPER}} .xclear-nav-menu__sublink',
        ]);

        $this->add_control('dropdown_min_width', [
            'label'      => esc_html__('Min Width', 'xclear'),
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range'      => ['px' => ['min' => 100, 'max' => 400]],
            'default'    => ['size' => 180, 'unit' => 'px'],
            'selectors'  => [
                '{{WRAPPER}} .xclear-nav-menu__submenu' => 'min-width: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('dropdown_border_radius', [
            'label'      => esc_html__('Border Radius', 'xclear'),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px'],
            'default'    => ['top' => '8', 'right' => '8', 'bottom' => '8', 'left' => '8', 'unit' => 'px'],
            'selectors'  => [
                '{{WRAPPER}} .xclear-nav-menu__submenu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Box_Shadow::get_type(), [
            'name'     => 'dropdown_shadow',
            'selector' => '{{WRAPPER}} .xclear-nav-menu__submenu',
        ]);

        $this->end_controls_section();
    }

    private function getPageOptions(): array
    {
        $options   = ['' => esc_html__('— Select —', 'xclear')];
        $hasPolylang = function_exists('pll_get_post_language');

        $postTypes = get_post_types(['public' => true, 'show_ui' => true], 'objects');
        foreach ($postTypes as $postType) {
            if (in_array($postType->name, ['attachment', 'elementor_library'], true)) {
                continue;
            }
            $posts = get_posts([
                'post_type'   => $postType->name,
                'post_status' => 'publish',
                'numberposts' => -1,
                'orderby'     => 'title',
                'order'       => 'ASC',
                'lang'        => '', // fetch all languages
            ]);
            if (empty($posts)) {
                continue;
            }
            foreach ($posts as $post) {
                $langSuffix = '';
                if ($hasPolylang) {
                    $lang = pll_get_post_language($post->ID, 'slug');
                    if ($lang) {
                        $langSuffix = ' [' . strtoupper($lang) . ']';
                    }
                }
                $options[$post->ID] = $postType->labels->singular_name . ': ' . $post->post_title . $langSuffix;
            }
        }

        return $options;
    }

    private function resolveItemUrl(array $item): array
    {
        $linkType = $item['link_type'] ?? 'page';

        if ($linkType === 'none') {
            return ['url' => '', 'target' => '_self', 'rel' => ''];
        }

        if ($linkType === 'custom') {
            $path = ltrim(trim($item['link_custom'] ?? ''), '/');
            return [
                'url'    => $path ? home_url('/' . $path) : '',
                'target' => '_self',
                'rel'    => '',
            ];
        }

        // link_type === 'page'
        $pageId = (int) ($item['link_page'] ?? 0);
        if (! $pageId) {
            return ['url' => '', 'target' => '_self', 'rel' => ''];
        }

        return ['url' => get_permalink($pageId) ?: '', 'target' => '_self', 'rel' => ''];
    }

    protected function render(): void
    {
        $settings    = $this->get_settings_for_display();
        $rawItems    = $settings['items'] ?? [];
        $openOn      = $settings['open_on'] ?? 'hover';
        $layout      = $settings['layout'] ?? 'horizontal';
        $showLangSw  = ($settings['mobile_lang_switcher'] ?? 'no') === 'yes';
        $langFormat  = $settings['mobile_lang_format'] ?? 'slug';
        $showButtons = ($settings['custom_buttons'] ?? 'no') === 'yes';

        if (empty($rawItems)) {
            return;
        }

        // Mobile drawer buttons — skip any with no label or unresolvable link
        $buttons = [];
        if ($showButtons) {
            foreach ([1, 2] as $index) {
                $label = trim($settings["btn{$index}_label"] ?? '');
                $link  = $this->resolveItemUrl([
                    'link_type'   => $settings["btn{$index}_link_type"] ?? 'page',
                    'link_page'   => $settings["btn{$index}_link_page"] ?? '',
                    'link_custom' => $settings["btn{$index}_link_custom"] ?? '',
                ]);

                if ($label && $link['url']) {
                    $buttons[] = ['label' => $label, 'url' => $link['url']];
                }
            }
        }

        // Group top-level items with their sub-items
        $grouped = [];
        foreach ($rawItems as $item) {
            if (($item['item_level'] ?? 'top') === 'sub' && ! empty($grouped)) {
                $grouped[count($grouped) - 1]['children'][] = $item;
            } else {
                $grouped[] = ['item' => $item, 'children' => []];
            }
        }

        $caretUrl = get_template_directory_uri() . '/assets/images/CaretDown.svg';
?>
        <div class="xclear-nav-wrapper">

            <button class="xclear-nav-hamburger"
                aria-label="<?php esc_attr_e('Open menu', 'xclear'); ?>"
                aria-expanded="false">
                <span class="xclear-nav-hamburger__bar"></span>
                <span class="xclear-nav-hamburger__bar"></span>
                <span class="xclear-nav-hamburger__bar"></span>
            </button>

            <div class="xclear-nav-backdrop"></div>

            <nav class="xclear-nav-menu xclear-nav-menu--<?php echo esc_attr($layout); ?>"
                data-open-on="<?php echo esc_attr($openOn); ?>">

                <button class="xclear-nav-drawer-close"
                    aria-label="<?php esc_attr_e('Close menu', 'xclear'); ?>">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </button>

                <?php foreach ($grouped as $group) :
                    $topItem     = $group['item'];
                    $children    = $group['children'];
                    $hasDropdown = ! empty($children);

                    $link     = $this->resolveItemUrl($topItem);
                    $url      = $link['url'];
                    $target   = $link['target'];
                    $rel      = $link['rel'];

                    $itemClass = 'xclear-nav-menu__item';
                    if ($hasDropdown) $itemClass .= ' xclear-nav-menu__item--dropdown';
                ?>
                    <div class="<?php echo esc_attr($itemClass); ?>">
                        <div class="xclear-nav-menu__trigger">
                            <?php if ($url) : ?>
                                <a class="xclear-nav-menu__label"
                                    href="<?php echo esc_url($url); ?>"
                                    target="<?php echo esc_attr($target); ?>"
                                    <?php echo $rel ? 'rel="' . esc_attr($rel) . '"' : ''; ?>>
                                    <?php echo esc_html($topItem['item_label'] ?? ''); ?>
                                </a>
                            <?php else : ?>
                                <span class="xclear-nav-menu__label">
                                    <?php echo esc_html($topItem['item_label'] ?? ''); ?>
                                </span>
                            <?php endif; ?>

                            <?php if ($hasDropdown) : ?>
                                <img class="xclear-nav-menu__caret"
                                    src="<?php echo esc_url($caretUrl); ?>"
                                    alt=""
                                    aria-hidden="true">
                            <?php endif; ?>
                        </div>

                        <?php if ($hasDropdown) : ?>
                            <ul class="xclear-nav-menu__submenu" role="list">
                                <?php foreach ($children as $child) :
                                    $subLink   = $this->resolveItemUrl($child);
                                    $subUrl    = $subLink['url'];
                                    $subTarget = $subLink['target'];
                                    $subRel    = $subLink['rel'];
                                ?>
                                    <li class="xclear-nav-menu__subitem">
                                        <a class="xclear-nav-menu__sublink"
                                            href="<?php echo esc_url($subUrl ?: '#'); ?>"
                                            target="<?php echo esc_attr($subTarget); ?>"
                                            <?php echo $subRel ? 'rel="' . esc_attr($subRel) . '"' : ''; ?>>
                                            <?php echo esc_html($child['item_label'] ?? ''); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <?php if ($showLangSw && class_exists('XclearLanguageSwitcherWidget')) : ?>
                    <div class="xclear-nav-drawer-lang">
                        <?php XclearLanguageSwitcherWidget::renderStandalone('pill', $langFormat); ?>
                    </div>
                <?php endif; ?>

                <?php if ($showButtons && ! empty($buttons)) : ?>
                    <div class="xclear-nav-drawer-buttons">
                        <?php foreach ($buttons as $i => $button) : ?>
                            <a class="btn btn-small <?php echo 0 === $i ? 'btn-light-green' : 'btn-secondary'; ?>"
                                href="<?php echo esc_url($button['url']); ?>">
                                <?php echo esc_html($button['label']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </nav>
        </div>
<?php
    }
}
