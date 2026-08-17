<?php
if (! defined('ABSPATH')) {
    exit;
}

class XclearApplicationsCardsWidget extends \Elementor\Widget_Base
{
    private const COLOR_VALUE = 'color: {{VALUE}};';
    public function get_name()
    {
        return 'xclear_applications_cards_widget';
    }

    public function get_title()
    {
        return esc_html__('Xclear Application Cards', 'xclear');
    }

    public function get_icon()
    {
        return 'eicon-gallery-grid';
    }

    public function get_categories()
    {
        return ['xclear-widgets'];
    }

    public function get_style_depends()
    {
        return ['xclear-applications-cards-widget-css'];
    }

    protected function register_controls()
    {
        $this->registerHeaderSection();
        $this->registerCardsSection();
        $this->registerLayoutSection();
        $this->registerStyleHeaderSection();
        $this->registerStyleGridSection();
        $this->registerStyleCardContentSection();
    }

    private function registerHeaderSection(): void
    {
        $this->start_controls_section('section_header', [
            'label' => esc_html__('Header', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('badge', [
            'label'   => esc_html__('Badge Text', 'xclear'),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => esc_html__('Application', 'xclear'),
        ]);

        $this->add_control('heading', [
            'label'       => esc_html__('Heading', 'xclear'),
            'type'        => \Elementor\Controls_Manager::TEXTAREA,
            'rows'        => 3,
            'default'     => '[hl]UV-C[/hl] for every pond and aquaculture application.',
            'description' => esc_html__('Wrap highlighted words with [hl]...[/hl]. Example: [hl]UV-C[/hl] for every pond.', 'xclear'),
        ]);

        $this->add_control('description', [
            'label'     => esc_html__('Description', 'xclear'),
            'type'      => \Elementor\Controls_Manager::WYSIWYG,
            'separator' => 'before',
            'default'   => '<p>A healthy pond starts with perfectly balanced water.</p>',
        ]);

        $this->end_controls_section();
    }

    private function registerCardsSection(): void
    {
        $this->start_controls_section('cards_section', [
            'label' => esc_html__('Cards', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $repeater = new \Elementor\Repeater();

        $repeater->add_control('card_image', [
            'label' => esc_html__('Background Image', 'xclear'),
            'type'  => \Elementor\Controls_Manager::MEDIA,
        ]);

        $repeater->add_control('card_icon', [
            'label'   => esc_html__('Icon', 'xclear'),
            'type'    => \Elementor\Controls_Manager::ICONS,
            'default' => ['library' => 'fa-solid', 'value' => 'fas fa-fish'],
        ]);

        $repeater->add_control('card_title', [
            'label'       => esc_html__('Title', 'xclear'),
            'description' => esc_html__('Wrap words with [hl]...[/hl] to apply the highlight color. Example: Koi [hl]Pond[/hl]', 'xclear'),
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => esc_html__('Koi Pond', 'xclear'),
        ]);

        $repeater->add_control('card_desc', [
            'label' => esc_html__('Description', 'xclear'),
            'type'  => \Elementor\Controls_Manager::TEXTAREA,
            'rows'  => 4,
        ]);

        $repeater->add_control('card_btn_text', [
            'label'   => esc_html__('Button Text', 'xclear'),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => esc_html__('Details', 'xclear'),
        ]);

        $repeater->add_control('card_btn_url', [
            'label'   => esc_html__('Button URL', 'xclear'),
            'type'    => \Elementor\Controls_Manager::URL,
            'default' => ['url' => '#'],
        ]);

        $this->add_control('cards', [
            'label'       => esc_html__('Cards', 'xclear'),
            'type'        => \Elementor\Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'default'     => [
                [
                    'card_title'    => esc_html__('Koi Pond', 'xclear'),
                    'card_desc'     => esc_html__('Keep your koi pond water crystal clear and protect your fish from harmful bacteria and parasites. Xclear UV-C filters improve water quality and support strong fish health, vibrant colours, and optimal growth.', 'xclear'),
                    'card_btn_text' => esc_html__('Details', 'xclear'),
                ],
                [
                    'card_title'    => esc_html__('Natural Swimming Pond', 'xclear'),
                    'card_desc'     => esc_html__('Maintain a clean and chemical-free swimming pond with effective UV-C water treatment. Our systems control algae naturally and help create safe, clear water for both people and nature.', 'xclear'),
                    'card_btn_text' => esc_html__('Details', 'xclear'),
                ],
                [
                    'card_title'    => esc_html__('Plant Pond', 'xclear'),
                    'card_desc'     => esc_html__('Support flourishing aquatic flora by maintaining the ideal biological balance and preventing algae from clouding the water. Optimal clarity allows essential light to reach your plants.', 'xclear'),
                    'card_btn_text' => esc_html__('Details', 'xclear'),
                ],
                [
                    'card_title'    => esc_html__('Ornamental Pond', 'xclear'),
                    'card_desc'     => esc_html__('Bring tranquillity to your garden with an inviting water feature that stays free from green water and harmful microorganisms all year round.', 'xclear'),
                    'card_btn_text' => esc_html__('Details', 'xclear'),
                ],
                [
                    'card_title'    => esc_html__('Aquaculture', 'xclear'),
                    'card_desc'     => esc_html__('A healthy garden pond requires reliable pond filtration and water treatment. Xclear solutions help maintain biological balance, reduce maintenance and keep your pond clean and transparent.', 'xclear'),
                    'card_btn_text' => esc_html__('Details', 'xclear'),
                ],
                [
                    'card_title'    => esc_html__('Fish Pond', 'xclear'),
                    'card_desc'     => esc_html__('Create a safe and stable environment for all pond fish by significantly reducing the presence of germs and viruses. Clear water allows for better visibility.', 'xclear'),
                    'card_btn_text' => esc_html__('Details', 'xclear'),
                ],
            ],
            'title_field' => '{{{ card_title }}}',
        ]);

        $this->end_controls_section();
    }

    private function registerLayoutSection(): void
    {
        $this->start_controls_section('layout_section', [
            'label' => esc_html__('Layout', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('content_position', [
            'label'   => esc_html__('Content Position', 'xclear'),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'bottom',
            'options' => [
                'bottom' => esc_html__('Bottom', 'xclear'),
                'top'    => esc_html__('Top', 'xclear'),
            ],
        ]);

        $this->end_controls_section();
    }

    private function registerStyleHeaderSection(): void
    {
        $this->start_controls_section('style_header', [
            'label' => esc_html__('Header', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('style_header_badge_heading', [
            'label' => esc_html__('Badge', 'xclear'),
            'type'  => \Elementor\Controls_Manager::HEADING,
        ]);

        $this->add_control('badge_bg_color', [
            'label'     => esc_html__('Background Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#e1f6f8',
            'selectors' => [
                '{{WRAPPER}} .xclear-app-cards__badge' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('badge_color', [
            'label'     => esc_html__('Text Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#45b5b5',
            'selectors' => [
                '{{WRAPPER}} .xclear-app-cards__badge'     => self::COLOR_VALUE,
                '{{WRAPPER}} .xclear-app-cards__badge-dot' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('style_header_heading_heading', [
            'label'     => esc_html__('Heading', 'xclear'),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'heading_typography',
            'selector' => '{{WRAPPER}} .xclear-app-cards__heading',
        ]);

        $this->add_control('heading_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .xclear-app-cards__heading' => self::COLOR_VALUE,
            ],
        ]);

        $this->add_control('hl_color', [
            'label'     => esc_html__('Highlight Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#45b5b5',
            'separator' => 'before',
            'selectors' => [
                '{{WRAPPER}} .xclear-app-cards__heading .xclear-hl' => self::COLOR_VALUE,
            ],
        ]);

        $this->add_control('style_header_desc_heading', [
            'label'     => esc_html__('Description', 'xclear'),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'header_desc_typography',
            'selector' => '{{WRAPPER}} .xclear-app-cards__header-desc',
        ]);

        $this->add_control('header_desc_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => 'rgba(255,255,255,0.65)',
            'selectors' => [
                '{{WRAPPER}} .xclear-app-cards__header-desc'   => self::COLOR_VALUE,
                '{{WRAPPER}} .xclear-app-cards__header-desc p' => self::COLOR_VALUE,
            ],
        ]);

        $this->end_controls_section();
    }

    private function registerStyleGridSection(): void
    {
        $this->start_controls_section('style_grid_section', [
            'label' => esc_html__('Grid & Item', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_responsive_control('cards_gap', [
            'label'      => esc_html__('Cards Gap', 'xclear'),
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range'      => ['px' => ['min' => 0, 'max' => 64]],
            'default'    => ['unit' => 'px', 'size' => 24],
            'selectors'  => [
                '{{WRAPPER}} .xclear-app-cards' => 'gap: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('card_radius', [
            'label'      => esc_html__('Item Border Radius', 'xclear'),
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range'      => ['px' => ['min' => 0, 'max' => 48]],
            'default'    => ['unit' => 'px', 'size' => 12],
            'selectors'  => [
                '{{WRAPPER}} .xclear-app-cards__item' => 'border-radius: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('card_body_gap', [
            'label'      => esc_html__('Content Gap', 'xclear'),
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range'      => ['px' => ['min' => 0, 'max' => 48]],
            'default'    => ['unit' => 'px', 'size' => 12],
            'selectors'  => [
                '{{WRAPPER}} .xclear-app-cards__body' => 'gap: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('card_min_height', [
            'label'      => esc_html__('Card Min Height', 'xclear'),
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', 'vh'],
            'range'      => [
                'px' => ['min' => 100, 'max' => 800],
                'vh' => ['min' => 10,  'max' => 100],
            ],
            'default'   => ['unit' => 'px', 'size' => 400],
            'separator' => 'before',
            'selectors' => [
                '{{WRAPPER}} .xclear-app-cards__item' => 'min-height: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('card_padding', [
            'label'      => esc_html__('Card Content Padding', 'xclear'),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px'],
            'default'    => [
                'top'    => 32,
                'right'  => 32,
                'bottom' => 32,
                'left'   => 32,
                'unit'   => 'px',
            ],
            'selectors' => [
                '{{WRAPPER}} .xclear-app-cards__body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();
    }

    private function registerStyleCardContentSection(): void
    {
        $this->start_controls_section('style_card_content_section', [
            'label' => esc_html__('Card Content', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('card_btn_type_heading', [
            'label'     => esc_html__('Button', 'xclear'),
            'type'      => \Elementor\Controls_Manager::HEADING,
        ]);

        $this->add_control('btn_type', [
            'label'   => esc_html__('Button Type', 'xclear'),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'white',
            'options' => [
                'default'           => esc_html__('Default (Blue)', 'xclear'),
                'outline'           => esc_html__('Outline (Blue)', 'xclear'),
                'white'             => esc_html__('White', 'xclear'),
                'white_outline'     => esc_html__('White Outline', 'xclear'),
                'secondary'         => esc_html__('Secondary (Green)', 'xclear'),
                'secondary_outline' => esc_html__('Secondary Outline (Green)', 'xclear'),
            ],
        ]);

        $this->add_control('btn_size', [
            'label'   => esc_html__('Button Size', 'xclear'),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'md',
            'options' => [
                'md' => esc_html__('Default', 'xclear'),
                'sm' => esc_html__('Small', 'xclear'),
            ],
        ]);

        $this->add_control('card_title_heading', [
            'label'     => esc_html__('Title', 'xclear'),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('title_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .xclear-app-cards__title' => self::COLOR_VALUE,
            ],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'title_typography',
            'selector' => '{{WRAPPER}} .xclear-app-cards__title',
        ]);

        $this->add_control('card_hl_color', [
            'label'     => esc_html__('Highlight Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#45b5b5',
            'separator' => 'before',
            'selectors' => [
                '{{WRAPPER}} .xclear-app-cards__title .xclear-hl' => self::COLOR_VALUE,
            ],
        ]);

        $this->add_control('card_desc_heading', [
            'label'     => esc_html__('Description', 'xclear'),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('desc_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => 'rgba(255,255,255,0.75)',
            'selectors' => [
                '{{WRAPPER}} .xclear-app-cards__desc' => self::COLOR_VALUE,
            ],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'desc_typography',
            'selector' => '{{WRAPPER}} .xclear-app-cards__desc',
        ]);

        $this->end_controls_section();
    }

    private function getWrapperClasses(array $settings): string
    {
        $classes = 'xclear-app-cards';

        if (($settings['content_position'] ?? 'bottom') === 'top') {
            $classes .= ' xclear-app-cards--content-top';
        }

        return $classes;
    }

    private static function parseHighlight(string $text): string
    {
        return preg_replace(
            '/\[hl\](.*?)\[\/hl\]/s',
            '<mark class="xclear-hl">$1</mark>',
            esc_html($text)
        );
    }

    private static function getBtnClasses(string $btnType, string $size = 'md'): string
    {
        $map = [
            'default'           => 'btn',
            'outline'           => 'btn btn-outline',
            'white'             => 'btn btn-white',
            'white_outline'     => 'btn btn-white btn-outline',
            'secondary'         => 'btn btn-secondary',
            'secondary_outline' => 'btn btn-secondary btn-outline',
        ];

        $classes = $map[$btnType] ?? 'btn btn-white';
        if ($size === 'sm') {
            $classes .= ' btn-small';
        }

        return $classes;
    }

    private function renderCard(array $card, int $index, string $btnClasses): void
    {
        $img     = ! empty($card['card_image']['url']) ? esc_url($card['card_image']['url']) : '';
        $title   = $card['card_title'] ?? '';
        $desc    = esc_html($card['card_desc']     ?? '');
        $btnText = esc_html($card['card_btn_text'] ?? '');
        $btnKey  = "card_btn_{$index}";

        if (! empty($card['card_btn_url']['url'])) {
            $this->add_link_attributes($btnKey, $card['card_btn_url']);
            $this->add_render_attribute($btnKey, 'class', "{$btnClasses} xclear-app-cards__btn");
        }
?>
        <div class="xclear-app-cards__item" <?php echo $img ? "style=\"background-image:url({$img});\"" : ''; ?>>
            <div class="xclear-app-cards__body">
                <?php if ($title) : ?>
                    <h3 class="xclear-app-cards__title">
                        <?php if (! empty($card['card_icon']['value'])) : ?>
                            <span class="xclear-app-cards__icon">
                                <?php \Elementor\Icons_Manager::render_icon($card['card_icon'], ['aria-hidden' => 'true']); ?>
                            </span>
                        <?php endif; ?>
                        <?php echo self::parseHighlight($title); ?>
                    </h3>
                <?php endif; ?>

                <?php if ($desc) : ?>
                    <p class="xclear-app-cards__desc"><?php echo $desc; ?></p>
                <?php endif; ?>

                <?php if ($btnText && ! empty($card['card_btn_url']['url'])) : ?>
                    <a <?php echo $this->get_render_attribute_string($btnKey); ?>>
                        <?php echo $btnText; ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php
    }

    private function renderHeader(string $badge, string $heading, string $description): void
    {
        if (! $badge && ! $heading && ! $description) {
            return;
        }
    ?>
        <div class="xclear-app-cards__header">
            <?php if ($badge) : ?>
                <div class="xclear-app-cards__badge">
                    <span class="xclear-app-cards__badge-dot"></span>
                    <?php echo $badge; ?>
                </div>
            <?php endif; ?>
            <?php if ($heading || $description) : ?>
                <div class="xclear-app-cards__header-content">
                    <?php if ($heading) : ?>
                        <h2 class="xclear-app-cards__heading"><?php echo $heading; ?></h2>
                    <?php endif; ?>
                    <?php if ($description) : ?>
                        <div class="xclear-app-cards__header-desc"><?php echo $description; ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();

        $this->renderHeader(
            esc_html($settings['badge'] ?? ''),
            self::parseHighlight($settings['heading'] ?? ''),
            wp_kses_post($settings['description'] ?? '')
        );

        $cards = $settings['cards'] ?? [];
        if (empty($cards)) {
            return;
        }

        $classes    = $this->getWrapperClasses($settings);
        $btnClasses = self::getBtnClasses($settings['btn_type'] ?? 'white', $settings['btn_size'] ?? 'md');
    ?>
        <div class="<?php echo esc_attr($classes); ?>">
            <?php foreach ($cards as $index => $card) : ?>
                <?php $this->renderCard($card, $index, $btnClasses); ?>
            <?php endforeach; ?>
        </div>
<?php
    }
}
