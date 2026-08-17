<?php
if (! defined('ABSPATH')) {
    exit;
}

class XclearCategoriesCardsWidget extends \Elementor\Widget_Base
{
    private const COLOR_VALUE = 'color: {{VALUE}};';
    public function get_name()
    {
        return 'xclear_categories_cards_widget';
    }

    public function get_title()
    {
        return esc_html__('Xclear Categories Cards', 'xclear');
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
        return ['xclear-categories-cards-widget-css'];
    }

    protected function register_controls()
    {
        $this->registerHeaderSection();
        $this->registerCardsSection();
        $this->registerStyleHeaderSection();
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
            'default' => esc_html__('CATEGORIES', 'xclear'),
        ]);

        $this->add_control('heading', [
            'label'       => esc_html__('Heading', 'xclear'),
            'type'        => \Elementor\Controls_Manager::TEXTAREA,
            'rows'        => 3,
            'default'     => 'Xclear specialized [hl]product ranges[/hl] ',
            'description' => esc_html__('Wrap highlighted words with [hl]...[/hl]. Example: [hl]product ranges[/hl] for every pond.', 'xclear'),
        ]);

        $this->add_control('description', [
            'label'     => esc_html__('Description', 'xclear'),
            'type'      => \Elementor\Controls_Manager::WYSIWYG,
            'separator' => 'before',
            'default'   => '<p>The Xclear assortment is carefully developed to provide a solution for every pond situation:</p>',
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
            'label' => esc_html__('Image', 'xclear'),
            'type'  => \Elementor\Controls_Manager::MEDIA,
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
                    'card_title'    => esc_html__('Xclear Xpert-Series', 'xclear'),
                    'card_desc'     => esc_html__('Exclusively for the pond water professional who makes no compromises on performance and durability. These units feature a robust design, maximum flow capacity, and optimal radiation efficiency for top-tier', 'xclear'),
                    'card_btn_text' => esc_html__('More information ', 'xclear'),
                ],
                [
                    'card_title'    => esc_html__('Xclear Series', 'xclear'),
                    'card_desc'     => esc_html__('Specially developed for all types of ponds, from elegant decorative ponds to impressive koi ponds. These systems are easy to install and suitable for both enthusiastic pond owners and professional specialists. ', 'xclear'),
                    'card_btn_text' => esc_html__('More information ', 'xclear'),
                ],
                [
                    'card_title'    => esc_html__('Xclear Pond Products', 'xclear'),
                    'card_desc'     => esc_html__('A selection of practical accessories designed to keep your pond easy to maintain. This includes pre-filters, pond heaters, flow switches and static mixers for even water treatment distribution.', 'xclear'),
                    'card_btn_text' => esc_html__('More information ', 'xclear'),
                ],
                [
                    'card_title'    => esc_html__('UV-C replacement lamps', 'xclear'),
                    'card_desc'     => esc_html__('To ensure continuous protection, it is essential to replace your UV-C lamp on time. We offer the largest assortment of UV-C replacement lamps on the European market, ensuring the right lamp is available for your system.', 'xclear'),
                    'card_btn_text' => esc_html__('More information ', 'xclear'),
                ],
            ],
            'title_field' => '{{{ card_title }}}',
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
                '{{WRAPPER}} .xclear-categories-cards__badge' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('badge_color', [
            'label'     => esc_html__('Text Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#3B8F9A',
            'selectors' => [
                '{{WRAPPER}} .xclear-categories-cards__badge'     => self::COLOR_VALUE,
                '{{WRAPPER}} .xclear-categories-cards__badge-dot' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('style_header_heading_heading', [
            'label'     => esc_html__('Heading', 'xclear'),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('heading_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#263238',
            'selectors' => [
                '{{WRAPPER}} .xclear-categories-cards__heading' => self::COLOR_VALUE,
            ],
        ]);

        $this->add_control('hl_color', [
            'label'     => esc_html__('Highlight Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#40A4B3',
            'separator' => 'before',
            'selectors' => [
                '{{WRAPPER}} .xclear-categories-cards__heading .xclear-hl' => self::COLOR_VALUE,
            ],
        ]);

        $this->add_control('style_header_desc_heading', [
            'label'     => esc_html__('Description', 'xclear'),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('header_desc_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#546E7A',
            'selectors' => [
                '{{WRAPPER}} .xclear-categories-cards__header-desc'   => self::COLOR_VALUE,
                '{{WRAPPER}} .xclear-categories-cards__header-desc p' => self::COLOR_VALUE,
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
            'default' => 'secondary',
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
            'default'   => '#263238',
            'selectors' => [
                '{{WRAPPER}} .xclear-categories-cards__title' => self::COLOR_VALUE,
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
            'default'   => '#546E7A',
            'selectors' => [
                '{{WRAPPER}} .xclear-categories-cards__desc' => self::COLOR_VALUE,
            ],
        ]);

        $this->end_controls_section();
    }

    private function getWrapperClasses(array $settings): string
    {
        $classes = 'xclear-categories-cards';

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

        $classes = $map[$btnType] ?? 'btn btn-secondary';
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
            $this->add_render_attribute($btnKey, 'class', "{$btnClasses} xclear-categories-cards__btn");
        }
?>
        <div class="xclear-categories-cards__item">
            <div class="xclear-categories-cards__image">
                <?php if ($img) : ?>
                    <a href="<?php echo $card['card_btn_url']['url']; ?>">
                        <img src="<?php echo $img; ?>" alt="<?php echo $title; ?>">
                    </a>
                <?php else : ?>
                    <div class="xclear-categories-cards__image-placeholder"></div>
                <?php endif; ?>
            </div>
            <div class="xclear-categories-cards__body">
                <?php if ($title) : ?>
                    <h3 class="xclear-categories-cards__title">
                        <?php echo self::parseHighlight($title); ?>
                    </h3>
                <?php endif; ?>

                <?php if ($desc) : ?>
                    <p class="xclear-categories-cards__desc"><?php echo $desc; ?></p>
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
        <div class="xclear-categories-cards__header">
            <?php if ($badge) : ?>
                <div class="xclear-categories-cards__badge">
                    <span class="xclear-categories-cards__badge-dot"></span>
                    <?php echo $badge; ?>
                </div>
            <?php endif; ?>
            <?php if ($heading || $description) : ?>
                <div class="xclear-categories-cards__header-content">
                    <?php if ($heading) : ?>
                        <h2 class="xclear-categories-cards__heading">
                            <?php echo nl2br($heading); ?>
                        </h2>
                    <?php endif; ?>
                    <?php if ($description) : ?>
                        <div class="xclear-categories-cards__header-desc"><?php echo $description; ?></div>
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
        $btnClasses = self::getBtnClasses($settings['btn_type'] ?? 'secondary', $settings['btn_size'] ?? 'md');
    ?>
        <div class="<?php echo esc_attr($classes); ?>">
            <?php foreach ($cards as $index => $card) : ?>
                <?php $this->renderCard($card, $index, $btnClasses); ?>
            <?php endforeach; ?>
        </div>
<?php
    }
}
