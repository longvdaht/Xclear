<?php
if (! defined('ABSPATH')) {
    exit;
}

class XclearIndustryCardsWidget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'xclear_industry_cards_widget';
    }

    public function get_title()
    {
        return esc_html__('Industry Cards', 'xclear');
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
        return ['xclear-industry-cards-widget-css'];
    }

    protected function register_controls()
    {
        $this->registerHeaderSection();
        $this->registerCardsSection();
        $this->registerStyleHeaderSection();
        $this->registerStyleImageCardsSection();
        $this->registerStyleCtaCardSection();
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
            'default' => esc_html__('INDUSTRY', 'xclear'),
        ]);

        $this->add_control('heading', [
            'label'       => esc_html__('Heading', 'xclear'),
            'type'        => \Elementor\Controls_Manager::TEXTAREA,
            'default'     => 'Serving pond professionals [hl]worldwide[/hl]',
            'description' => esc_html__('Use [hl]...[/hl] for highlighted text.', 'xclear'),
        ]);

        $this->add_control('description', [
            'label'   => esc_html__('Description', 'xclear'),
            'type'    => \Elementor\Controls_Manager::TEXTAREA,
            'default' => esc_html__('Xclear is a trusted partner for pond professionals in over 80 countries worldwide. Our products, possibilities and extensive expertise allow us to support various sectors within the pond and water treatment industry:', 'xclear'),
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

        $repeater->add_control('card_type', [
            'label'   => esc_html__('Card Type', 'xclear'),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'image',
            'options' => [
                'image' => esc_html__('Image Card', 'xclear'),
                'cta'   => esc_html__('CTA Card', 'xclear'),
            ],
        ]);

        $repeater->add_control('card_image', [
            'label'     => esc_html__('Background Image', 'xclear'),
            'type'      => \Elementor\Controls_Manager::MEDIA,
            'condition' => ['card_type' => 'image'],
        ]);

        $repeater->add_control('card_icon', [
            'label'     => esc_html__('Icon', 'xclear'),
            'type'      => \Elementor\Controls_Manager::ICONS,
            'default'   => ['value' => 'fas fa-store', 'library' => 'fa-solid'],
            'condition' => ['card_type' => 'image'],
        ]);

        $repeater->add_control('card_title', [
            'label'   => esc_html__('Title', 'xclear'),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => esc_html__('Card title', 'xclear'),
        ]);

        $repeater->add_control('card_link', [
            'label' => esc_html__('Link', 'xclear'),
            'type'  => \Elementor\Controls_Manager::URL,
        ]);

        $repeater->add_control('cta_arrow_icon', [
            'label'       => esc_html__('Arrow Icon', 'xclear'),
            'type'        => \Elementor\Controls_Manager::MEDIA,
            'description' => esc_html__('Upload custom SVG icon. Leave empty to use default.', 'xclear'),
            'condition'   => ['card_type' => 'cta'],
        ]);

        $this->add_control('cards', [
            'label'       => esc_html__('Cards', 'xclear'),
            'type'        => \Elementor\Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'default'     => [
                [
                    'card_type'  => 'image',
                    'card_icon'  => ['value' => 'fas fa-store', 'library' => 'fa-solid'],
                    'card_title' => 'Wholesale',
                ],
                [
                    'card_type'  => 'image',
                    'card_icon'  => ['value' => 'fas fa-tag', 'library' => 'fa-solid'],
                    'card_title' => 'Retail & dealers',
                ],
                [
                    'card_type'  => 'image',
                    'card_icon'  => ['value' => 'fas fa-hammer', 'library' => 'fa-solid'],
                    'card_title' => 'Pond builders & installers',
                ],
                [
                    'card_type'  => 'image',
                    'card_icon'  => ['value' => 'fas fa-seedling', 'library' => 'fa-solid'],
                    'card_title' => 'Garden & DIY centers',
                ],
                [
                    'card_type'  => 'image',
                    'card_icon'  => ['value' => 'fas fa-shopping-cart', 'library' => 'fa-solid'],
                    'card_title' => 'E-commerce',
                ],
                [
                    'card_type'  => 'cta',
                    'card_title' => 'Become a Xclear Dealer',
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

        $this->add_control('badge_color', [
            'label'     => esc_html__('Badge Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .xclear-industry-cards__badge' => 'color: {{VALUE}}'],
        ]);

        $this->add_control('badge_bg', [
            'label'     => esc_html__('Badge Background', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .xclear-industry-cards__badge' => 'background-color: {{VALUE}}; border-color: {{VALUE}}'],
        ]);

        $this->add_control('heading_color', [
            'label'     => esc_html__('Heading Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .xclear-industry-cards__heading' => 'color: {{VALUE}}'],
        ]);

        $this->add_control('hl_color', [
            'label'     => esc_html__('Highlight Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'separator' => 'before',
            'selectors' => ['{{WRAPPER}} .xclear-industry-cards__heading .xclear-hl' => 'color: {{VALUE}}'],
        ]);

        $this->add_control('desc_color', [
            'label'     => esc_html__('Description Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .xclear-industry-cards__desc' => 'color: {{VALUE}}'],
        ]);

        $this->end_controls_section();
    }

    // ── Style: Image Cards ────────────────────────────────────────────────────

    private function registerStyleImageCardsSection()
    {
        $this->start_controls_section('style_image_cards', [
            'label' => esc_html__('Image Cards', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('card_icon_color', [
            'label'     => esc_html__('Icon Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-industry-cards__card-icon'     => 'color: {{VALUE}}',
                '{{WRAPPER}} .xclear-industry-cards__card-icon svg' => 'fill: {{VALUE}}',
            ],
        ]);

        $this->add_control('card_title_color', [
            'label'     => esc_html__('Title Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .xclear-industry-cards__card--image .xclear-industry-cards__card-title' => 'color: {{VALUE}}'],
        ]);

        $this->end_controls_section();
    }

    // ── Style: CTA Card ───────────────────────────────────────────────────────

    private function registerStyleCtaCardSection()
    {
        $this->start_controls_section('style_cta_card', [
            'label' => esc_html__('CTA Card', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('cta_bg', [
            'label'     => esc_html__('Background', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .xclear-industry-cards__card--cta' => 'background-color: {{VALUE}}'],
        ]);

        $this->add_control('cta_title_color', [
            'label'     => esc_html__('Title Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .xclear-industry-cards__card--cta .xclear-industry-cards__card-title' => 'color: {{VALUE}}'],
        ]);

        $this->add_control('cta_arrow_color', [
            'label'     => esc_html__('Arrow Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .xclear-industry-cards__cta-arrow' => 'color: {{VALUE}}'],
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
        <div class="xclear-industry-cards">

            <?php if ($settings['badge_text'] || $settings['heading'] || $settings['description']) : ?>
            <div class="xclear-industry-cards__header">
                <?php if ($settings['badge_text']) : ?>
                <span class="xclear-industry-cards__badge">
                    <span class="xclear-industry-cards__badge-dot"></span>
                    <?php echo esc_html($settings['badge_text']); ?>
                </span>
                <?php endif; ?>
                <?php if ($settings['heading']) : ?>
                <h2 class="xclear-industry-cards__heading"><?php echo self::parseHighlight($settings['heading']); ?></h2>
                <?php endif; ?>
                <?php if ($settings['description']) : ?>
                <p class="xclear-industry-cards__desc"><?php echo esc_html($settings['description']); ?></p>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if (! empty($cards)) : ?>
            <div class="xclear-industry-cards__grid">
                <?php foreach ($cards as $card) :
                    $is_cta     = $card['card_type'] === 'cta';
                    $link_attrs = self::renderLinkAttrs($card['card_link'] ?? []);
                    $tag        = $link_attrs ? 'a' : 'div';
                    $mod_class  = $is_cta ? 'xclear-industry-cards__card--cta' : 'xclear-industry-cards__card--image';
                    $bg_url     = (! $is_cta && ! empty($card['card_image']['url']))
                        ? esc_url($card['card_image']['url'])
                        : '';
                ?>
                <<?php echo esc_attr($tag); ?> class="xclear-industry-cards__card <?php echo esc_attr($mod_class); ?>"<?php echo $link_attrs; ?>>
                    <?php if (! $is_cta) : ?>
                        <?php if ($bg_url) : ?>
                        <span class="xclear-industry-cards__card-bg" style="background-image: url('<?php echo $bg_url; ?>')"></span>
                        <?php endif; ?>
                        <div class="xclear-industry-cards__card-content">
                            <?php if (! empty($card['card_icon']['value'])) : ?>
                            <div class="xclear-industry-cards__card-icon">
                                <?php \Elementor\Icons_Manager::render_icon($card['card_icon'], ['aria-hidden' => 'true']); ?>
                            </div>
                            <?php endif; ?>
                            <?php if ($card['card_title']) : ?>
                            <h4 class="xclear-industry-cards__card-title"><?php echo esc_html($card['card_title']); ?></h4>
                            <?php endif; ?>
                        </div>
                    <?php else : ?>
                        <?php if ($card['card_title']) : ?>
                        <span class="xclear-industry-cards__card-title"><?php echo esc_html($card['card_title']); ?></span>
                        <?php endif; ?>
                        <span class="xclear-industry-cards__cta-arrow" aria-hidden="true">
                            <?php if (! empty($card['cta_arrow_icon']['url'])) : ?>
                                <img src="<?php echo esc_url($card['cta_arrow_icon']['url']); ?>" alt="">
                            <?php else : ?>
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7 17L17 7M17 7H7M17 7V17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            <?php endif; ?>
                        </span>
                    <?php endif; ?>
                </<?php echo esc_attr($tag); ?>>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

        </div>
        <?php
    }
}
