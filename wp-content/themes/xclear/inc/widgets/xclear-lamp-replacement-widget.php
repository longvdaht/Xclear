<?php
if (! defined('ABSPATH')) {
    exit;
}

class XclearLampReplacementWidget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'xclear_lamp_replacement_widget';
    }

    public function get_title()
    {
        return esc_html__('Lamp Replacement', 'xclear');
    }

    public function get_icon()
    {
        return 'eicon-posts-grid';
    }

    public function get_categories()
    {
        return ['xclear-widgets'];
    }

    public function get_style_depends()
    {
        return ['xclear-lamp-replacement-widget-css'];
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
            'default' => esc_html__('CATEGORIES', 'xclear'),
        ]);

        $this->add_control('heading', [
            'label'       => esc_html__('Heading', 'xclear'),
            'type'        => \Elementor\Controls_Manager::TEXTAREA,
            'default'     => 'The importance of [hl]annual lamp replacement[/hl]',
            'description' => esc_html__('Use [hl]...[/hl] for highlighted text.', 'xclear'),
        ]);

        $this->add_control('description', [
            'label'       => esc_html__('Description', 'xclear'),
            'type'        => \Elementor\Controls_Manager::TEXTAREA,
            'default'     => esc_html__('To maintain optimal water quality, it is crucial to replace your UV-C lamp on time.', 'xclear'),
            'description' => esc_html__('Separate paragraphs with a blank line.', 'xclear'),
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

        $repeater->add_control('card_image', [
            'label' => esc_html__('Image', 'xclear'),
            'type'  => \Elementor\Controls_Manager::MEDIA,
        ]);

        $repeater->add_control('card_title', [
            'label'   => esc_html__('Title', 'xclear'),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => esc_html__('Card title', 'xclear'),
        ]);

        $repeater->add_control('card_description', [
            'label'   => esc_html__('Description', 'xclear'),
            'type'    => \Elementor\Controls_Manager::TEXTAREA,
            'default' => esc_html__('Card description goes here.', 'xclear'),
        ]);

        $repeater->add_control('card_link', [
            'label' => esc_html__('Link', 'xclear'),
            'type'  => \Elementor\Controls_Manager::URL,
        ]);

        $this->add_control('cards', [
            'label'       => esc_html__('Cards', 'xclear'),
            'type'        => \Elementor\Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'default'     => [
                [
                    'card_title'       => 'Standard UV-C Lamps',
                    'card_description' => 'Have an effective lifespan of approximately 9,000 hours.',
                ],
                [
                    'card_title'       => 'Amalgam Lamp',
                    'card_description' => 'Offer a significantly longer life of 12,000 to 16,000 hours.',
                ],
            ],
            'title_field' => '{{{ card_title }}}',
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
            'label'     => esc_html__('Badge', 'xclear'),
            'type'      => \Elementor\Controls_Manager::HEADING,
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'badge_typography',
            'selector' => '{{WRAPPER}} .xclear-lamp-replacement__badge',
        ]);

        $this->add_control('badge_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .xclear-lamp-replacement__badge' => 'color: {{VALUE}}'],
        ]);

        $this->add_control('badge_bg', [
            'label'     => esc_html__('Background', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .xclear-lamp-replacement__badge' => 'background-color: {{VALUE}}; border-color: {{VALUE}}'],
        ]);

        $this->add_control('style_heading_heading', [
            'label'     => esc_html__('Heading', 'xclear'),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'heading_typography',
            'selector' => '{{WRAPPER}} .xclear-lamp-replacement__heading',
        ]);

        $this->add_control('heading_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .xclear-lamp-replacement__heading' => 'color: {{VALUE}}'],
        ]);

        $this->add_control('hl_color', [
            'label'     => esc_html__('Highlight Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .xclear-lamp-replacement__heading .xclear-hl' => 'color: {{VALUE}}'],
        ]);

        $this->add_control('style_desc_heading', [
            'label'     => esc_html__('Description', 'xclear'),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'desc_typography',
            'selector' => '{{WRAPPER}} .xclear-lamp-replacement__desc, {{WRAPPER}} .xclear-lamp-replacement__desc p',
        ]);

        $this->add_control('desc_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .xclear-lamp-replacement__desc' => 'color: {{VALUE}}'],
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
            'selectors' => ['{{WRAPPER}} .xclear-lamp-replacement__card' => 'background-color: {{VALUE}}'],
        ]);

        $this->add_control('style_card_title_heading', [
            'label'     => esc_html__('Card Title', 'xclear'),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'card_title_typography',
            'selector' => '{{WRAPPER}} .xclear-lamp-replacement__card-title',
        ]);

        $this->add_control('card_title_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .xclear-lamp-replacement__card-title' => 'color: {{VALUE}}'],
        ]);

        $this->add_control('style_card_desc_heading', [
            'label'     => esc_html__('Card Description', 'xclear'),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'card_desc_typography',
            'selector' => '{{WRAPPER}} .xclear-lamp-replacement__card-desc',
        ]);

        $this->add_control('card_desc_color', [
            'label'     => esc_html__('Description Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .xclear-lamp-replacement__card-desc' => 'color: {{VALUE}}'],
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

    private static function renderLinkAttrs(array $link): string
    {
        if (empty($link['url'])) {
            return '';
        }
        $attrs = ' href="' . esc_url($link['url']) . '"';
        $rel   = [];
        if (! empty($link['is_external'])) {
            $attrs .= ' target="_blank"';
            $rel[] = 'noopener';
            $rel[] = 'noreferrer';
        }
        if (! empty($link['nofollow'])) {
            $rel[] = 'nofollow';
        }
        if ($rel) {
            $attrs .= ' rel="' . esc_attr(implode(' ', $rel)) . '"';
        }
        return $attrs;
    }

    // ── Render ────────────────────────────────────────────────────────────────

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $cards    = $settings['cards'] ?? [];
        ?>
        <div class="xclear-lamp-replacement">

            <?php if ($settings['badge_text'] || $settings['heading'] || $settings['description']) : ?>
            <div class="xclear-lamp-replacement__header">
                <?php if ($settings['badge_text']) : ?>
                <span class="xclear-lamp-replacement__badge">
                    <span class="xclear-lamp-replacement__badge-dot"></span>
                    <?php echo esc_html($settings['badge_text']); ?>
                </span>
                <?php endif; ?>
                <?php if ($settings['heading']) : ?>
                <h2 class="xclear-lamp-replacement__heading"><?php echo self::parseHighlight($settings['heading']); ?></h2>
                <?php endif; ?>
                <?php if ($settings['description']) : ?>
                <div class="xclear-lamp-replacement__desc"><?php echo wp_kses_post(wpautop($settings['description'])); ?></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if (! empty($cards)) : ?>
            <div class="xclear-lamp-replacement__grid">
                <?php foreach ($cards as $card) :
                    $link_attrs = self::renderLinkAttrs($card['card_link'] ?? []);
                    $tag        = $link_attrs ? 'a' : 'div';
                ?>
                <<?php echo esc_attr($tag); ?> class="xclear-lamp-replacement__card"<?php echo $link_attrs; ?>>
                    <div class="xclear-lamp-replacement__card-image">
                        <?php if (! empty($card['card_image']['url'])) : ?>
                        <img src="<?php echo esc_url($card['card_image']['url']); ?>"
                             alt="<?php echo esc_attr($card['card_title']); ?>">
                        <?php else : ?>
                        <div class="xclear-lamp-replacement__card-image-placeholder"></div>
                        <?php endif; ?>
                    </div>
                    <div class="xclear-lamp-replacement__card-body">
                        <?php if ($card['card_title']) : ?>
                        <h3 class="xclear-lamp-replacement__card-title"><?php echo esc_html($card['card_title']); ?></h3>
                        <?php endif; ?>
                        <?php if ($card['card_description']) : ?>
                        <p class="xclear-lamp-replacement__card-desc"><?php echo esc_html($card['card_description']); ?></p>
                        <?php endif; ?>
                    </div>
                </<?php echo esc_attr($tag); ?>>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

        </div>
        <?php
    }
}
