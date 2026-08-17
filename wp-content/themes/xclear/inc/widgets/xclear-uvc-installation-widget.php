<?php
if (! defined('ABSPATH')) {
    exit;
}

class XclearUvcInstallationWidget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'xclear_uvc_installation_widget';
    }

    public function get_title()
    {
        return esc_html__('UVC Installation & Maintenance', 'xclear');
    }

    public function get_icon()
    {
        return 'eicon-icon-box';
    }

    public function get_categories()
    {
        return ['xclear-widgets'];
    }

    public function get_style_depends()
    {
        return ['xclear-uvc-installation-widget-css'];
    }

    protected function register_controls()
    {
        $this->registerHeaderSection();
        $this->registerCardsSection();
        $this->registerStyleHeaderSection();
        $this->registerStyleCardsSection();
    }

    // ── Content: Header ───────────────────────────────────────────────────────

    private function registerHeaderSection()
    {
        $this->start_controls_section('section_header', [
            'label' => esc_html__('Header', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('badge_text', [
            'label'   => esc_html__('Badge Text', 'xclear'),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => esc_html__('LOREM IPSUM', 'xclear'),
        ]);

        $this->add_control('heading', [
            'label'       => esc_html__('Heading', 'xclear'),
            'type'        => \Elementor\Controls_Manager::TEXTAREA,
            'default'     => 'Installation & maintenance',
            'description' => esc_html__('Use [hl]...[/hl] for highlighted text.', 'xclear'),
        ]);

        $this->end_controls_section();
    }

    // ── Content: Cards ────────────────────────────────────────────────────────

    private function registerCardsSection()
    {
        $this->start_controls_section('section_cards', [
            'label' => esc_html__('Cards', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $repeater = new \Elementor\Repeater();

        $repeater->add_control('card_icon', [
            'label'   => esc_html__('Icon', 'xclear'),
            'type'    => \Elementor\Controls_Manager::ICONS,
            'default' => [
                'value'   => 'fas fa-lightbulb',
                'library' => 'fa-solid',
            ],
        ]);

        $repeater->add_control('card_title', [
            'label'   => esc_html__('Title', 'xclear'),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => esc_html__('Card title', 'xclear'),
        ]);

        $repeater->add_control('card_text', [
            'label'   => esc_html__('Description', 'xclear'),
            'type'    => \Elementor\Controls_Manager::TEXTAREA,
            'default' => esc_html__('Card description goes here.', 'xclear'),
        ]);

        $this->add_control('cards', [
            'label'       => esc_html__('Cards', 'xclear'),
            'type'        => \Elementor\Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'default'     => [
                [
                    'card_icon'  => ['value' => 'fas fa-wrench', 'library' => 'fa-solid'],
                    'card_title' => 'Installation',
                    'card_text'  => 'UV-C units are typically placed after the pump and main filter to ensure all water is treated before returning to the pond.',
                ],
                [
                    'card_icon'  => ['value' => 'fas fa-cog', 'library' => 'fa-solid'],
                    'card_title' => 'Maintenance',
                    'card_text'  => 'To maintain peak performance, annual lamp replacement is essential. Standard lamps last ~9,000 hours, while Amalgam lamps offer up to 16,000 hours.',
                ],
                [
                    'card_icon'  => ['value' => 'fas fa-lightbulb', 'library' => 'fa-solid'],
                    'card_title' => 'Usage Tip',
                    'card_text'  => 'Keep your system running 24/7 during the pond season to prevent algae from gaining a foothold.',
                ],
            ],
            'title_field' => '{{{ card_title }}}',
        ]);

        $this->add_control('columns', [
            'label'   => esc_html__('Columns (desktop)', 'xclear'),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => '3',
            'options' => [
                '2' => '2',
                '3' => '3',
                '4' => '4',
            ],
            'selectors' => [
                '{{WRAPPER}} .xclear-uvc-installation__grid' => '--grid-cols: {{VALUE}}',
            ],
        ]);

        $this->end_controls_section();
    }

    // ── Style: Header ─────────────────────────────────────────────────────────

    private function registerStyleHeaderSection()
    {
        $this->start_controls_section('style_header', [
            'label' => esc_html__('Header', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('style_badge_heading', [
            'label' => esc_html__('Badge', 'xclear'),
            'type'  => \Elementor\Controls_Manager::HEADING,
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'badge_typography',
            'selector' => '{{WRAPPER}} .xclear-uvc-installation__badge',
        ]);

        $this->add_control('badge_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .xclear-uvc-installation__badge' => 'color: {{VALUE}}'],
        ]);

        $this->add_control('badge_bg', [
            'label'     => esc_html__('Background', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .xclear-uvc-installation__badge' => 'background-color: {{VALUE}}; border-color: {{VALUE}}'],
        ]);

        $this->add_control('style_heading_heading', [
            'label'     => esc_html__('Heading', 'xclear'),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'heading_typography',
            'selector' => '{{WRAPPER}} .xclear-uvc-installation__heading',
        ]);

        $this->add_control('heading_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .xclear-uvc-installation__heading' => 'color: {{VALUE}}'],
        ]);

        $this->add_control('hl_color', [
            'label'     => esc_html__('Highlight Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'separator' => 'before',
            'selectors' => ['{{WRAPPER}} .xclear-uvc-installation__heading .xclear-hl' => 'color: {{VALUE}}'],
        ]);

        $this->end_controls_section();
    }

    // ── Style: Cards ──────────────────────────────────────────────────────────

    private function registerStyleCardsSection()
    {
        $this->start_controls_section('style_cards', [
            'label' => esc_html__('Cards', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('card_bg', [
            'label'     => esc_html__('Card Background', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .xclear-uvc-installation__card' => 'background-color: {{VALUE}}'],
        ]);

        $this->add_control('card_border_radius', [
            'label'      => esc_html__('Border Radius', 'xclear'),
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range'      => ['px' => ['min' => 0, 'max' => 40]],
            'selectors'  => ['{{WRAPPER}} .xclear-uvc-installation__card' => 'border-radius: {{SIZE}}{{UNIT}}'],
        ]);

        $this->add_control('icon_color', [
            'label'     => esc_html__('Icon Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'separator' => 'before',
            'selectors' => [
                '{{WRAPPER}} .xclear-uvc-installation__card-icon'     => 'color: {{VALUE}}',
                '{{WRAPPER}} .xclear-uvc-installation__card-icon svg' => 'fill: {{VALUE}}',
            ],
        ]);

        $this->add_control('style_card_title_heading', [
            'label'     => esc_html__('Card Title', 'xclear'),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'card_title_typography',
            'selector' => '{{WRAPPER}} .xclear-uvc-installation__card-title',
        ]);

        $this->add_control('card_title_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .xclear-uvc-installation__card-title' => 'color: {{VALUE}}'],
        ]);

        $this->add_control('style_card_text_heading', [
            'label'     => esc_html__('Card Description', 'xclear'),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'card_text_typography',
            'selector' => '{{WRAPPER}} .xclear-uvc-installation__card-text',
        ]);

        $this->add_control('card_text_color', [
            'label'     => esc_html__('Description Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .xclear-uvc-installation__card-text' => 'color: {{VALUE}}'],
        ]);

        $this->end_controls_section();
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private static function parseHighlight(string $text): string
    {
        return preg_replace(
            '/\[hl\](.*?)\[\/hl\]/s',
            '<mark class="xclear-hl">$1</mark>',
            esc_html($text)
        );
    }

    // ── Render ────────────────────────────────────────────────────────────────

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $cards    = $settings['cards'] ?? [];
        ?>
        <div class="xclear-uvc-installation">

            <?php if ($settings['badge_text'] || $settings['heading']) : ?>
            <div class="xclear-uvc-installation__header">
                <?php if ($settings['badge_text']) : ?>
                <span class="xclear-uvc-installation__badge">
                    <span class="xclear-uvc-installation__badge-dot"></span>
                    <?php echo esc_html($settings['badge_text']); ?>
                </span>
                <?php endif; ?>
                <?php if ($settings['heading']) : ?>
                <h2 class="xclear-uvc-installation__heading"><?php echo self::parseHighlight($settings['heading']); ?></h2>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if (! empty($cards)) : ?>
            <div class="xclear-uvc-installation__grid">
                <?php foreach ($cards as $card) : ?>
                <div class="xclear-uvc-installation__card">
                    <?php if (! empty($card['card_icon']['value'])) : ?>
                    <div class="xclear-uvc-installation__card-icon">
                        <?php \Elementor\Icons_Manager::render_icon($card['card_icon'], ['aria-hidden' => 'true']); ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($card['card_title']) : ?>
                    <h5 class="xclear-uvc-installation__card-title"><?php echo esc_html($card['card_title']); ?></h5>
                    <?php endif; ?>
                    <?php if ($card['card_text']) : ?>
                    <p class="xclear-uvc-installation__card-text"><?php echo esc_html($card['card_text']); ?></p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

        </div>
        <?php
    }
}
