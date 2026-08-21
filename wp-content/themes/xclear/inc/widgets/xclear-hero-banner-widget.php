<?php
if (! defined('ABSPATH')) {
    exit;
}

// ── Trait: Controls ───────────────────────────────────────────────────────────

trait XclearHeroBannerControlsTrait
{
    private const COLOR_VALUE = 'color: {{VALUE}};';

    private function registerContentSection(): void
    {
        $this->start_controls_section('section_content', [
            'label' => esc_html__('Content', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('badge', [
            'label'   => esc_html__('Badge Text', 'xclear'),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => esc_html__('Xclear', 'xclear'),
        ]);

        $this->add_control('heading_source', [
            'label'   => esc_html__('Heading Source', 'xclear'),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'manual',
            'options' => [
                'manual'                => esc_html__('Manual', 'xclear'),
                'auto_product_category' => esc_html__('Auto — Product Category Name', 'xclear'),
            ],
        ]);

        $this->add_control('heading', [
            'label'       => esc_html__('Heading', 'xclear'),
            'description' => esc_html__('Wrap words with [hl]...[/hl] to apply the highlight color. Example: Xclear [hl]UV-C[/hl]', 'xclear'),
            'type'        => \Elementor\Controls_Manager::TEXTAREA,
            'rows'        => 3,
            'default'     => "Xclear UV-C\nThe Difference is Clear",
            'condition'   => ['heading_source' => 'manual'],
        ]);

        $this->add_control('heading_tag', [
            'label'   => esc_html__('Heading HTML Tag', 'xclear'),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'h2',
            'options' => [
                'h1' => 'H1',
                'h2' => 'H2',
            ],
        ]);

        $this->add_control('description_1_source', [
            'label'     => esc_html__('Description 1 Source', 'xclear'),
            'type'      => \Elementor\Controls_Manager::SELECT,
            'default'   => 'manual',
            'options'   => [
                'manual'                => esc_html__('Manual', 'xclear'),
                'auto_product_category' => esc_html__('Auto — Product Category Description', 'xclear'),
            ],
            'separator' => 'before',
        ]);

        $this->add_control('description_1', [
            'label'     => esc_html__('Description 1', 'xclear'),
            'type'      => \Elementor\Controls_Manager::TEXTAREA,
            'rows'      => 4,
            'default'   => esc_html__('Enjoy a pond that looks beautiful every day, without constant maintenance or worry. Whether you own a high-end koi pond or a DIY garden project, Xclear offers reliable solutions for every type of pond.', 'xclear'),
            'condition' => ['description_1_source' => 'manual'],
        ]);

        $this->add_control('description_2', [
            'label'       => esc_html__('Description 2', 'xclear'),
            'description' => esc_html__('When filled, both descriptions appear side by side in two columns.', 'xclear'),
            'type'        => \Elementor\Controls_Manager::TEXTAREA,
            'rows'        => 4,
            'default'     => '',
            'separator'   => 'before',
        ]);

        $this->end_controls_section();
    }

    private function registerButtonsSection(): void
    {
        $btn_options = [
            'default'           => esc_html__('Default (Blue)', 'xclear'),
            'outline'           => esc_html__('Outline (Blue)', 'xclear'),
            'white'             => esc_html__('White', 'xclear'),
            'white_outline'     => esc_html__('White Outline', 'xclear'),
            'secondary'         => esc_html__('Secondary (Green)', 'xclear'),
            'secondary_outline' => esc_html__('Secondary Outline (Green)', 'xclear'),
        ];

        $this->start_controls_section('section_buttons', [
            'label' => esc_html__('Buttons', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('btn1_text', [
            'label'   => esc_html__('Button 1 — Text', 'xclear'),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => esc_html__('Explore Products', 'xclear'),
        ]);

        $this->add_control('btn1_url', [
            'label'     => esc_html__('Button 1 — URL', 'xclear'),
            'type'      => \Elementor\Controls_Manager::URL,
            'default'   => ['url' => '#'],
            'condition' => ['btn1_text!' => ''],
        ]);

        $this->add_control('btn1_type', [
            'label'     => esc_html__('Button 1 — Type', 'xclear'),
            'type'      => \Elementor\Controls_Manager::SELECT,
            'default'   => 'white',
            'options'   => $btn_options,
            'condition' => ['btn1_text!' => ''],
        ]);

        $this->add_control('btn1_size', [
            'label'     => esc_html__('Button 1 — Size', 'xclear'),
            'type'      => \Elementor\Controls_Manager::SELECT,
            'default'   => 'md',
            'options'   => [
                'md' => esc_html__('Default', 'xclear'),
                'sm' => esc_html__('Small', 'xclear'),
            ],
            'condition' => ['btn1_text!' => ''],
        ]);

        $this->add_control('btn2_text', [
            'label'     => esc_html__('Button 2 — Text', 'xclear'),
            'type'      => \Elementor\Controls_Manager::TEXT,
            'default'   => esc_html__('Contact Us', 'xclear'),
            'separator' => 'before',
        ]);

        $this->add_control('btn2_url', [
            'label'     => esc_html__('Button 2 — URL', 'xclear'),
            'type'      => \Elementor\Controls_Manager::URL,
            'default'   => ['url' => '#'],
            'condition' => ['btn2_text!' => ''],
        ]);

        $this->add_control('btn2_type', [
            'label'     => esc_html__('Button 2 — Type', 'xclear'),
            'type'      => \Elementor\Controls_Manager::SELECT,
            'default'   => 'white_outline',
            'options'   => $btn_options,
            'condition' => ['btn2_text!' => ''],
        ]);

        $this->add_control('btn2_size', [
            'label'     => esc_html__('Button 2 — Size', 'xclear'),
            'type'      => \Elementor\Controls_Manager::SELECT,
            'default'   => 'md',
            'options'   => [
                'md' => esc_html__('Default', 'xclear'),
                'sm' => esc_html__('Small', 'xclear'),
            ],
            'condition' => ['btn2_text!' => ''],
        ]);

        $this->end_controls_section();
    }

    private function getImageSizeOptions(): array
    {
        $sizes = ['full' => esc_html__('Full', 'xclear')];
        foreach (get_intermediate_image_sizes() as $size) {
            $sizes[$size] = esc_html__(ucwords(str_replace(['-', '_'], ' ', $size)), 'xclear');
        }
        return $sizes;
    }

    private function registerBackgroundSection(): void
    {
        $this->start_controls_section('section_background', [
            'label' => esc_html__('Background', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('bg_color', [
            'label' => esc_html__('Background Color', 'xclear'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-hero-banner' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('bg_type', [
            'label'   => esc_html__('Background Type', 'xclear'),
            'type'    => \Elementor\Controls_Manager::CHOOSE,
            'options' => [
                'image' => ['title' => esc_html__('Image', 'xclear'), 'icon' => 'eicon-image'],
                'video' => ['title' => esc_html__('Video', 'xclear'), 'icon' => 'eicon-video-camera'],
            ],
            'default' => 'image',
            'toggle'  => false,
        ]);

        // ─── Image ─────────────────────────────────────────────────────────────

        $this->add_responsive_control('bg_image', [
            'label'     => esc_html__('Image', 'xclear'),
            'type'      => \Elementor\Controls_Manager::MEDIA,
            'condition' => ['bg_type' => 'image'],
        ]);

        $this->add_control('bg_image_resolution', [
            'label'     => esc_html__('Image Resolution', 'xclear'),
            'type'      => \Elementor\Controls_Manager::SELECT,
            'default'   => 'full',
            'options'   => $this->getImageSizeOptions(),
            'condition' => ['bg_type' => 'image'],
        ]);

        $this->add_responsive_control('bg_position', [
            'label'     => esc_html__('Position', 'xclear'),
            'type'      => \Elementor\Controls_Manager::SELECT,
            'default'   => '',
            'options'   => [
                ''              => esc_html__('Default', 'xclear'),
                'center center' => esc_html__('Center Center', 'xclear'),
                'center left'   => esc_html__('Center Left', 'xclear'),
                'center right'  => esc_html__('Center Right', 'xclear'),
                'top center'    => esc_html__('Top Center', 'xclear'),
                'top left'      => esc_html__('Top Left', 'xclear'),
                'top right'     => esc_html__('Top Right', 'xclear'),
                'bottom center' => esc_html__('Bottom Center', 'xclear'),
                'bottom left'   => esc_html__('Bottom Left', 'xclear'),
                'bottom right'  => esc_html__('Bottom Right', 'xclear'),
            ],
            'selectors' => [
                '{{WRAPPER}} .xclear-hero-banner' => 'background-position: {{VALUE}};',
            ],
            'condition' => ['bg_type' => 'image'],
        ]);

        $this->add_responsive_control('bg_attachment', [
            'label'     => esc_html__('Attachment', 'xclear'),
            'type'      => \Elementor\Controls_Manager::SELECT,
            'default'   => '',
            'options'   => [
                ''       => esc_html__('Default', 'xclear'),
                'scroll' => esc_html__('Scroll', 'xclear'),
                'fixed'  => esc_html__('Fixed', 'xclear'),
            ],
            'selectors' => [
                '{{WRAPPER}} .xclear-hero-banner' => 'background-attachment: {{VALUE}};',
            ],
            'condition' => ['bg_type' => 'image'],
        ]);

        $this->add_responsive_control('bg_repeat', [
            'label'     => esc_html__('Repeat', 'xclear'),
            'type'      => \Elementor\Controls_Manager::SELECT,
            'default'   => '',
            'options'   => [
                ''          => esc_html__('Default', 'xclear'),
                'no-repeat' => esc_html__('No-repeat', 'xclear'),
                'repeat'    => esc_html__('Repeat', 'xclear'),
                'repeat-x'  => esc_html__('Repeat X', 'xclear'),
                'repeat-y'  => esc_html__('Repeat Y', 'xclear'),
            ],
            'selectors' => [
                '{{WRAPPER}} .xclear-hero-banner' => 'background-repeat: {{VALUE}};',
            ],
            'condition' => ['bg_type' => 'image'],
        ]);

        $this->add_responsive_control('bg_size', [
            'label'     => esc_html__('Display Size', 'xclear'),
            'type'      => \Elementor\Controls_Manager::SELECT,
            'default'   => '',
            'options'   => [
                ''        => esc_html__('Default', 'xclear'),
                'auto'    => esc_html__('Auto', 'xclear'),
                'cover'   => esc_html__('Cover', 'xclear'),
                'contain' => esc_html__('Contain', 'xclear'),
            ],
            'selectors' => [
                '{{WRAPPER}} .xclear-hero-banner' => 'background-size: {{VALUE}};',
            ],
            'condition' => ['bg_type' => 'image'],
        ]);

        // ─── Video ─────────────────────────────────────────────────────────────

        $this->add_control('bg_video', [
            'label'       => esc_html__('Background Video', 'xclear'),
            'type'        => \Elementor\Controls_Manager::MEDIA,
            'media_types' => ['video'],
            'condition'   => ['bg_type' => 'video'],
        ]);

        $this->add_control('bg_video_fallback', [
            'label'       => esc_html__('Fallback Image (Preload / Mobile)', 'xclear'),
            'description' => esc_html__('Shown while the video loads or on devices that block autoplay.', 'xclear'),
            'type'        => \Elementor\Controls_Manager::MEDIA,
            'condition'   => ['bg_type' => 'video'],
        ]);

        // ─── Overlay / Height ──────────────────────────────────────────────────

        $this->add_control('overlay_gradient_1', [
            'label'       => esc_html__('Overlay Gradient 1', 'xclear'),
            'description' => esc_html__('CSS linear-gradient value. Leave empty to hide this layer.', 'xclear'),
            'type'        => \Elementor\Controls_Manager::TEXTAREA,
            'rows'        => 3,
            'default'     => '',
            'selectors'   => [
                '{{WRAPPER}} .xclear-hero-banner__overlay-1' => 'background-image: {{VALUE}};',
            ],
        ]);

        $this->add_control('overlay_gradient_2', [
            'label'       => esc_html__('Overlay Gradient 2', 'xclear'),
            'description' => esc_html__('CSS linear-gradient value. Leave empty to hide this layer.', 'xclear'),
            'type'        => \Elementor\Controls_Manager::TEXTAREA,
            'rows'        => 3,
            'default'     => '',
            'separator'   => 'before',
            'selectors'   => [
                '{{WRAPPER}} .xclear-hero-banner__overlay-2' => 'background-image: {{VALUE}};',
            ],
        ]);

        $this->add_control('show_other_overlay', [
            'label'     => esc_html__('Show Other Overlay', 'xclear'),
            'type'      => \Elementor\Controls_Manager::SWITCHER,
            'label_on'  => esc_html__('yes', 'xclear'),
            'label_off' => esc_html__('no', 'xclear'),
            'default'   => 'yes',
        ]);

        $this->add_responsive_control('min_height', [
            'label'          => esc_html__('Min Height', 'xclear'),
            'type'           => \Elementor\Controls_Manager::SLIDER,
            'size_units'     => ['px', 'vh'],
            'range'          => [
                'px' => ['min' => 200, 'max' => 1200, 'step' => 10],
                'vh' => ['min' => 20,  'max' => 100,  'step' => 5],
            ],
            'default'        => ['unit' => 'px', 'size' => 600],
            'tablet_default' => ['unit' => 'px', 'size' => 600],
            'mobile_default' => ['unit' => 'px', 'size' => 640],
            'selectors'      => [
                '{{WRAPPER}} .xclear-hero-banner' => 'min-height: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();
    }

    private function registerStyleContentSection(): void
    {
        $this->start_controls_section('style_content', [
            'label' => esc_html__('Content', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('split_layout', [
            'label'        => esc_html__('Split Layout', 'xclear'),
            'description'  => esc_html__('Heading & buttons on the left, description on the right.', 'xclear'),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'label_on'     => esc_html__('On', 'xclear'),
            'label_off'    => esc_html__('Off', 'xclear'),
            'return_value' => 'yes',
            'default'      => '',
        ]);

        $this->add_responsive_control('content_align', [
            'label'     => esc_html__('Align Content', 'xclear'),
            'type'      => \Elementor\Controls_Manager::CHOOSE,
            'options'   => [
                'left'   => ['title' => esc_html__('Left',   'xclear'), 'icon' => 'eicon-text-align-left'],
                'center' => ['title' => esc_html__('Center', 'xclear'), 'icon' => 'eicon-text-align-center'],
                'right'  => ['title' => esc_html__('Right',  'xclear'), 'icon' => 'eicon-text-align-right'],
            ],
            'default'   => 'left',
            'toggle'    => false,
            'selectors' => [
                '{{WRAPPER}} .xclear-hero-banner__content' => 'align-items: {{VALUE}}; text-align: {{VALUE}};',
                '{{WRAPPER}} .xclear-hero-banner__buttons'  => 'justify-content: {{VALUE}};',
            ],
            'condition' => ['split_layout!' => 'yes'],
        ]);

        $this->add_responsive_control('content_valign', [
            'label'     => esc_html__('Position', 'xclear'),
            'type'      => \Elementor\Controls_Manager::CHOOSE,
            'options'   => [
                'flex-start' => ['title' => esc_html__('Top',    'xclear'), 'icon' => 'eicon-v-align-top'],
                'center'     => ['title' => esc_html__('Center', 'xclear'), 'icon' => 'eicon-v-align-middle'],
                'flex-end'   => ['title' => esc_html__('Bottom', 'xclear'), 'icon' => 'eicon-v-align-bottom'],
            ],
            'default'   => 'center',
            'mobile_default'   => 'flex-end',
            'toggle'    => false,
            'selectors' => [
                '{{WRAPPER}} .xclear-hero-banner' => 'align-items: {{VALUE}};',
            ],
        ]);

        $this->add_responsive_control('content_gap', [
            'label'      => esc_html__('Gap', 'xclear'),
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range'      => ['px' => ['min' => 0, 'max' => 80, 'step' => 2]],
            'default'    => ['unit' => 'px', 'size' => 24],
            'selectors'  => [
                '{{WRAPPER}} .xclear-hero-banner__content' => '--content-gap: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('content_padding', [
            'label'      => esc_html__('Padding', 'xclear'),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'default'    => [
                'top'    => 80,
                'right'  => 64,
                'bottom' => 80,
                'left'   => 64,
                'unit'   => 'px',
            ],
            'mobile_default'      => [
                'top'    => 40,
                'right'  => 24,
                'bottom' => 40,
                'left'   => 24,
                'unit'   => 'px',
            ],
            'selectors'  => [
                '{{WRAPPER}} .xclear-hero-banner__inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('banner_section_spacing', [
            'label'               => esc_html__('Section Spacing', 'xclear'),
            'type'                => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units'          => ['px', 'em'],
            'allowed_dimensions'  => 'vertical',
            'default'             => [
                'top'    => 64,
                'bottom' => 64,
                'unit'   => 'px',
            ],
            'mobile_default'      => [
                'top'    => 16,
                'bottom' => 16,
                'unit'   => 'px',
            ],
            'separator'           => 'before',
            'selectors'           => [
                '{{WRAPPER}}' => 'margin-top: {{TOP}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('banner_radius', [
            'label'      => esc_html__('Border Radius', 'xclear'),
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range'      => ['px' => ['min' => 0, 'max' => 64, 'step' => 1]],
            'default'    => ['unit' => 'px', 'size' => 0],
            'separator'  => 'before',
            'selectors'  => [
                '{{WRAPPER}} .xclear-hero-banner' => 'border-radius: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();
    }

    private function registerStyleBadgeSection(): void
    {
        $this->start_controls_section('style_badge', [
            'label' => esc_html__('Badge', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('badge_bg_color', [
            'label'     => esc_html__('Background Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#e1f6f8',
            'selectors' => [
                '{{WRAPPER}} .xclear-hero-banner__badge' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('badge_color', [
            'label'     => esc_html__('Text Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#45b5b5',
            'selectors' => [
                '{{WRAPPER}} .xclear-hero-banner__badge'     => self::COLOR_VALUE,
                '{{WRAPPER}} .xclear-hero-banner__badge-dot' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();
    }

    private function registerStyleHeadingSection(): void
    {
        $this->start_controls_section('style_heading', [
            'label' => esc_html__('Heading', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'heading_typography',
            'selector' => '{{WRAPPER}} .xclear-hero-banner__heading',
        ]);

        $this->add_control('heading_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .xclear-hero-banner__heading' => self::COLOR_VALUE,
            ],
        ]);

        $this->add_control('hl_color', [
            'label'     => esc_html__('Highlight Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#45b5b5',
            'separator' => 'before',
            'selectors' => [
                '{{WRAPPER}} .xclear-hl' => self::COLOR_VALUE,
            ],
        ]);

        $this->end_controls_section();
    }

    private function registerStyleDescSection(): void
    {
        $this->start_controls_section('style_description', [
            'label' => esc_html__('Description', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'desc_typography',
            'selector' => '{{WRAPPER}} .xclear-hero-banner__desc',
        ]);

        $this->add_control('desc_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-hero-banner__desc' => self::COLOR_VALUE,
            ],
        ]);

        $this->add_control('desc_1_max_width', [
            'label'      => esc_html__('Description 1 — Max Width', 'xclear'),
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'range'      => [
                'px' => ['min' => 90,  'max' => 800, 'step' => 10],
                '%'  => ['min' => 10,  'max' => 100, 'step' => 1],
            ],
            'separator'  => 'before',
            'selectors'  => [
                '{{WRAPPER}} .xclear-hero-banner__desc-wrap .xclear-hero-banner__desc:first-child' => 'max-width: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .xclear-hero-banner__split-right .xclear-hero-banner__desc:first-child' => 'max-width: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('desc_2_max_width', [
            'label'      => esc_html__('Description 2 — Max Width', 'xclear'),
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'range'      => [
                'px' => ['min' => 90,  'max' => 800, 'step' => 10],
                '%'  => ['min' => 10,  'max' => 100, 'step' => 1],
            ],
            'selectors'  => [
                '{{WRAPPER}} .xclear-hero-banner__desc-wrap .xclear-hero-banner__desc:last-child' => 'max-width: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .xclear-hero-banner__split-right .xclear-hero-banner__desc:last-child' => 'max-width: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();
    }
}

// ── Trait: Render ─────────────────────────────────────────────────────────────

trait XclearHeroBannerRenderTrait
{
    private static function parseHighlight(string $text): string
    {
        return preg_replace(
            '/\[hl\](.*?)\[\/hl\]/s',
            '<mark class="xclear-hl">$1</mark>',
            esc_html($text)
        );
    }

    private static function getBtnClasses(string $type, string $size = 'md'): string
    {
        $map = [
            'default'           => 'btn',
            'outline'           => 'btn btn-outline',
            'white'             => 'btn btn-white',
            'white_outline'     => 'btn btn-white btn-outline',
            'secondary'         => 'btn btn-secondary',
            'secondary_outline' => 'btn btn-secondary btn-outline',
        ];

        $classes = $map[$type] ?? 'btn';
        if ($size === 'sm') {
            $classes .= ' btn-small';
        }

        return $classes;
    }

    private function getContentClass(string $align): string
    {
        $base = 'xclear-hero-banner__content';
        return 'left' === $align ? $base : "{$base} {$base}--align-{$align}";
    }

    private function getImageUrl(array $image, string $size = 'full'): string
    {
        if (empty($image['url'])) {
            return '';
        }
        if (! empty($image['id']) && 'full' !== $size) {
            $src = wp_get_attachment_image_src((int) $image['id'], $size);
            if ($src) {
                return esc_url($src[0]);
            }
        }
        return esc_url($image['url']);
    }

    private function getBreakpoints(): array
    {
        $defaults = ['tablet' => 1024, 'mobile' => 768];
        if (! class_exists('\Elementor\Plugin') || ! isset(\Elementor\Plugin::$instance->breakpoints)) {
            return $defaults;
        }
        try {
            $bps = \Elementor\Plugin::$instance->breakpoints->get_breakpoints();
            return [
                'tablet' => isset($bps['tablet']) ? (int) $bps['tablet']->get_value() : $defaults['tablet'],
                'mobile' => isset($bps['mobile']) ? (int) $bps['mobile']->get_value() : $defaults['mobile'],
            ];
        } catch (\Throwable $e) {
            return $defaults;
        }
    }

    private function renderBackgroundImageCSS(array $settings): void
    {
        if ('image' !== ($settings['bg_type'] ?? 'image')) {
            return;
        }

        $resolution  = $settings['bg_image_resolution'] ?? 'full';
        $desktop_url = $this->getImageUrl($settings['bg_image'] ?? [], $resolution);
        $tablet_url  = $this->getImageUrl($settings['bg_image_tablet'] ?? [], $resolution);
        $mobile_url  = $this->getImageUrl($settings['bg_image_mobile'] ?? [], $resolution);

        if (! $desktop_url && ! $tablet_url && ! $mobile_url) {
            return;
        }

        $uid = 'elementor-element-' . $this->get_id();
        $bps = $this->getBreakpoints();

        echo '<style>';
        if ($desktop_url) {
            printf('.%s .xclear-hero-banner{background-image:url("%s")}', esc_attr($uid), $desktop_url);
        }
        if ($tablet_url) {
            printf(
                '@media(max-width:%dpx){.%s .xclear-hero-banner{background-image:url("%s")}}',
                $bps['tablet'],
                esc_attr($uid),
                $tablet_url
            );
        }
        if ($mobile_url) {
            printf(
                '@media(max-width:%dpx){.%s .xclear-hero-banner{background-image:url("%s")}}',
                $bps['mobile'],
                esc_attr($uid),
                $mobile_url
            );
        }
        echo '</style>';
    }

    private function buildBannerStyle(array $settings, bool $has_video): string
    {
        // For video: show fallback image inline (visible while video loads or on mobile)
        if ($has_video) {
            $fallback_url = esc_url($settings['bg_video_fallback']['url'] ?? '');
            return $fallback_url ? "background-image:url({$fallback_url});" : '';
        }
        // For image: URL is output via renderBackgroundImageCSS() to support responsive
        return '';
    }

    private function prepareButtonAttributes(array $settings): void
    {
        if (! empty($settings['btn1_url']['url'])) {
            $classes = self::getBtnClasses($settings['btn1_type'] ?? 'white', $settings['btn1_size'] ?? 'md') . ' xclear-hero-banner__btn';
            $this->add_link_attributes('btn1', $settings['btn1_url']);
            $this->add_render_attribute('btn1', 'class', $classes);
        }
        if (! empty($settings['btn2_url']['url'])) {
            $classes = self::getBtnClasses($settings['btn2_type'] ?? 'white_outline', $settings['btn2_size'] ?? 'md') . ' xclear-hero-banner__btn';
            $this->add_link_attributes('btn2', $settings['btn2_url']);
            $this->add_render_attribute('btn2', 'class', $classes);
        }
    }

    private function renderVideoBackground(string $video_url): void
    {
?>
        <video class="xclear-hero-banner__video" autoplay muted loop playsinline preload="auto" aria-hidden="true">
            <source src="<?php echo $video_url; ?>" type="video/mp4">
            <track kind="descriptions" src="" default>
        </video>
    <?php
    }

    private function renderButtons(array $settings, string $btn1_text, string $btn2_text): void
    {
    ?>
        <div class="xclear-hero-banner__buttons">
            <?php if ($btn1_text && ! empty($settings['btn1_url']['url'])) : ?>
                <a <?php echo $this->get_render_attribute_string('btn1'); ?>>
                    <?php echo $btn1_text; ?>
                </a>
            <?php endif; ?>
            <?php if ($btn2_text && ! empty($settings['btn2_url']['url'])) : ?>
                <a <?php echo $this->get_render_attribute_string('btn2'); ?>>
                    <?php echo $btn2_text; ?>
                </a>
            <?php endif; ?>
        </div>
    <?php
    }

    private function renderContent(array $settings, string $content_class): void
    {
        $badge = esc_html($settings['badge'] ?? '');

        if (($settings['heading_source'] ?? 'manual') === 'auto_product_category' && is_tax('product_category')) {
            $term    = get_queried_object();
            $heading = $term->name ?? '';
        } else {
            $heading = $settings['heading'] ?? '';
        }

        if (($settings['description_1_source'] ?? 'manual') === 'auto_product_category' && is_tax('product_category')) {
            $term          = $term ?? get_queried_object();
            $description_1 = esc_html($term->description ?? '');
        } else {
            $description_1 = esc_html($settings['description_1'] ?? '');
        }
        $description_2 = esc_html($settings['description_2'] ?? '');
        $btn1_text = esc_html($settings['btn1_text'] ?? '');
        $btn2_text = esc_html($settings['btn2_text'] ?? '');
        $has_two_desc = !empty($description_1) && !empty($description_2);
        $is_split = ($settings['split_layout'] ?? '') === 'yes';
        $heading_tag = $settings['heading_tag'] ?? 'h2';
    ?>
        <div class="<?php echo esc_attr($content_class); ?><?php echo $is_split ? ' xclear-hero-banner__content--split' : ''; ?>">

            <?php if ($is_split) : ?>

                <?php if ($badge) : ?>
                    <div class="xclear-hero-banner__badge">
                        <span class="xclear-hero-banner__badge-dot"></span>
                        <?php echo $badge; ?>
                    </div>
                <?php endif; ?>

                <div class="xclear-hero-banner__heading-desc">
                    <?php if ($heading) : ?>
                        <<?php echo $heading_tag; ?> class="xclear-hero-banner__heading">
                            <?php echo nl2br(self::parseHighlight($heading)); ?>
                        </<?php echo $heading_tag; ?>>
                    <?php endif; ?>
                    <?php if ($description_1 || $description_2) : ?>
                        <div class="xclear-hero-banner__split-right">
                            <?php if ($description_1) : ?>
                                <p class="xclear-hero-banner__desc"><?php echo $description_1; ?></p>
                            <?php endif; ?>
                            <?php if ($description_2) : ?>
                                <p class="xclear-hero-banner__desc"><?php echo $description_2; ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($btn1_text || $btn2_text) : ?>
                    <?php $this->renderButtons($settings, $btn1_text, $btn2_text); ?>
                <?php endif; ?>

            <?php else : ?>

                <?php if ($badge) : ?>
                    <div class="xclear-hero-banner__badge">
                        <span class="xclear-hero-banner__badge-dot"></span>
                        <?php echo $badge; ?>
                    </div>
                <?php endif; ?>

                <?php if ($heading) : ?>
                    <<?php echo $heading_tag; ?> class="xclear-hero-banner__heading">
                        <?php echo nl2br(self::parseHighlight($heading)); ?>
                    </<?php echo $heading_tag; ?>>
                <?php endif; ?>

                <?php if ($description_1 || $description_2) : ?>
                    <div class="xclear-hero-banner__desc-wrap<?php echo $has_two_desc ? ' has-two-desc' : ''; ?>">
                        <?php if ($description_1) : ?>
                            <p class="xclear-hero-banner__desc"><?php echo $description_1; ?></p>
                        <?php endif; ?>
                        <?php if ($description_2) : ?>
                            <p class="xclear-hero-banner__desc"><?php echo $description_2; ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($btn1_text || $btn2_text) : ?>
                    <?php $this->renderButtons($settings, $btn1_text, $btn2_text); ?>
                <?php endif; ?>

            <?php endif; ?>

        </div>
    <?php
    }
}

// ── Widget ────────────────────────────────────────────────────────────────────

class XclearHeroBannerWidget extends \Elementor\Widget_Base
{
    use XclearHeroBannerControlsTrait;
    use XclearHeroBannerRenderTrait;

    public function get_name()
    {
        return 'xclear_hero_banner_widget';
    }

    public function get_title()
    {
        return esc_html__('Xclear Hero Banner', 'xclear');
    }

    public function get_icon()
    {
        return 'eicon-banner';
    }

    public function get_categories()
    {
        return ['xclear-widgets'];
    }

    public function get_keywords()
    {
        return ['hero', 'banner', 'xclear'];
    }

    public function get_style_depends()
    {
        return ['xclear-hero-banner-widget-css'];
    }

    protected function register_controls()
    {
        $this->registerContentSection();
        $this->registerButtonsSection();
        $this->registerBackgroundSection();
        $this->registerStyleContentSection();
        $this->registerStyleBadgeSection();
        $this->registerStyleHeadingSection();
        $this->registerStyleDescSection();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        $has_video     = ('video' === ($settings['bg_type'] ?? 'image')) && ! empty($settings['bg_video']['url']);
        $video_url     = $has_video ? esc_url($settings['bg_video']['url']) : '';
        $content_class = $this->getContentClass($settings['content_align'] ?? 'left');
        $banner_style  = $this->buildBannerStyle($settings, $has_video);
        $show_other_overlay = ($settings['show_other_overlay'] ?? '') === 'yes';

        $this->prepareButtonAttributes($settings);
        $this->renderBackgroundImageCSS($settings);
    ?>
        <section class="xclear-hero-banner<?php echo $has_video ? ' xclear-hero-banner--video' : ''; ?><?php echo $show_other_overlay ? ' show-other-overlay' : ''; ?>"
            style="<?php echo $banner_style; ?>">

            <div class="xclear-hero-banner__overlay-1"></div>
            <div class="xclear-hero-banner__overlay-2"></div>

            <?php if ($has_video) : ?>
                <?php $this->renderVideoBackground($video_url); ?>
            <?php endif; ?>

            <div class="xclear-hero-banner__inner">
                <?php $this->renderContent($settings, $content_class); ?>
            </div>

        </section>
<?php
    }
}
