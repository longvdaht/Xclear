<?php
if (! defined('ABSPATH')) {
    exit;
}

class XclearAboutHighlightsWidget extends \Elementor\Widget_Base
{
    private const COLOR_VALUE = 'color: {{VALUE}};';
    public function get_name()
    {
        return 'xclear_about_highlights_widget';
    }

    public function get_title()
    {
        return esc_html__('Xclear About Highlights', 'xclear');
    }

    public function get_icon()
    {
        return 'eicon-info-box';
    }

    public function get_categories()
    {
        return ['xclear-widgets'];
    }

    public function get_style_depends()
    {
        return ['xclear-about-highlights-widget-css'];
    }

    protected function register_controls()
    {
        $this->registerItemsSection();
        $this->registerStyleSection();
    }

    private function registerItemsSection(): void
    {
        $this->start_controls_section('section_items', [
            'label' => esc_html__('Items', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $repeater = new \Elementor\Repeater();

        $repeater->add_control('item_icon', [
            'label'   => esc_html__('Icon', 'xclear'),
            'type'    => \Elementor\Controls_Manager::ICONS,
            'default' => [
                'value'   => 'fas fa-check-circle',
                'library' => 'fa-solid',
            ],
        ]);

        $repeater->add_control('item_text', [
            'label'   => esc_html__('Text', 'xclear'),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => esc_html__('Text here', 'xclear'),
        ]);

        $repeater->add_control('item_link', [
            'label'   => esc_html__('Link', 'xclear'),
            'type'    => \Elementor\Controls_Manager::URL,
            'default' => ['url' => ''],
        ]);

        $this->add_control('items', [
            'label'       => esc_html__('Items', 'xclear'),
            'type'        => \Elementor\Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'default'     => [
                [
                    'item_text' => esc_html__('Made in the Netherlands', 'xclear'),
                    'item_icon' => ['value' => 'fas fa-check-circle', 'library' => 'fa-solid'],
                ],
                [
                    'item_text' => esc_html__('40+ years of expertise', 'xclear'),
                    'item_icon' => ['value' => 'fas fa-check-circle', 'library' => 'fa-solid'],
                ],
                [
                    'item_text' => esc_html__('Exported worldwide', 'xclear'),
                    'item_icon' => ['value' => 'fas fa-check-circle', 'library' => 'fa-solid'],
                ],
            ],
            'title_field' => '{{{ item_text }}}',
        ]);

        $this->end_controls_section();
    }

    private function registerStyleSection(): void
    {
        $this->start_controls_section('section_style', [
            'label' => esc_html__('Style', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('heading_card', [
            'label'     => esc_html__('Card', 'xclear'),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('card_bg_color', [
            'label'     => esc_html__('Background Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-about-highlights__item' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('heading_icon', [
            'label'     => esc_html__('Icon', 'xclear'),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('icon_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-about-highlights__icon'     => 'color: {{VALUE}};',
                '{{WRAPPER}} .xclear-about-highlights__icon svg' => 'fill: {{VALUE}};',
            ],
        ]);

        $this->add_control('heading_text', [
            'label'     => esc_html__('Text', 'xclear'),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('text_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-about-highlights__text' => self::COLOR_VALUE,
            ],
        ]);

        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $items    = $settings['items'] ?? [];

        if (empty($items)) {
            return;
        }
        ?>
        <div class="xclear-about-highlights">
            <?php foreach ($items as $index => $item) :
                $hasLink = ! empty($item['item_link']['url']);
                $tag     = $hasLink ? 'a' : 'div';
                $classes = 'xclear-about-highlights__item' . ($hasLink ? ' xclear-about-highlights__item--link' : '');

                if ($hasLink) {
                    $this->add_link_attributes('item_link_' . $index, $item['item_link']);
                    $this->add_render_attribute('item_link_' . $index, 'class', $classes);
                }
            ?>
                <?php if ($hasLink) : ?>
                    <a <?php echo $this->get_render_attribute_string('item_link_' . $index); ?>>
                <?php else : ?>
                    <div class="<?php echo esc_attr($classes); ?>">
                <?php endif; ?>
                    <?php if (! empty($item['item_icon']['value'])) : ?>
                        <span class="xclear-about-highlights__icon">
                            <?php \Elementor\Icons_Manager::render_icon($item['item_icon'], ['aria-hidden' => 'true']); ?>
                        </span>
                    <?php endif; ?>
                    <?php if (! empty($item['item_text'])) : ?>
                        <span class="xclear-about-highlights__text">
                            <?php echo esc_html($item['item_text']); ?>
                        </span>
                    <?php endif; ?>
                </<?php echo $tag; ?>>
            <?php endforeach; ?>
        </div>
        <?php
    }
}
