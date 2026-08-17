<?php
if (! defined('ABSPATH')) {
    exit;
}

class XclearSectionHeaderWidget extends \Elementor\Widget_Base
{
    private const COLOR_VALUE = 'color: {{VALUE}};';

    public function get_name()
    {
        return 'xclear_section_header_widget';
    }

    public function get_title()
    {
        return esc_html__('Xclear Section Header', 'xclear');
    }

    public function get_icon()
    {
        return 'eicon-t-letter';
    }

    public function get_categories()
    {
        return ['xclear-widgets'];
    }

    public function get_style_depends()
    {
        return ['xclear-section-header-widget-css'];
    }

    protected function register_controls()
    {
        $this->start_controls_section('section_content', [
            'label' => esc_html__('Content', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('badge_text', [
            'label'       => esc_html__('Badge Text', 'xclear'),
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => esc_html__('KNOWLEDGE CENTER', 'xclear'),
            'label_block' => true,
        ]);

        $this->add_control('show_badge_dot', [
            'label'        => esc_html__('Show Badge Dot', 'xclear'),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'label_on'     => esc_html__('Show', 'xclear'),
            'label_off'    => esc_html__('Hide', 'xclear'),
            'return_value' => 'yes',
            'default'      => 'yes',
            'condition'    => [
                'badge_text!' => '',
            ],
        ]);

        $this->add_control('title_text', [
            'label'       => esc_html__('Title', 'xclear'),
            'type'        => \Elementor\Controls_Manager::TEXTAREA,
            'description' => esc_html__('Wrap highlighted words with [hl]...[/hl]. Example: [hl]Lorem ipsum[/hl] dolor sit amet.', 'xclear'),
            'default'     => esc_html__('Everything you need to know about UV-C', 'xclear'),
            'label_block' => true,
        ]);

        $this->add_control('title_tag', [
            'label'   => esc_html__('Title HTML Tag', 'xclear'),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'h1'   => 'H1',
                'h2'   => 'H2',
            ],
            'default' => 'h1',
        ]);

        $this->add_control('description_text', [
            'label'       => esc_html__('Description', 'xclear'),
            'type'        => \Elementor\Controls_Manager::WYSIWYG,
            'default'     => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam pellentesque neque eget dui facilisis mattis. Aliquam a sollicitudin odio.', 'xclear'),
            'label_block' => true,
        ]);

        $this->add_responsive_control('align', [
            'label'   => esc_html__('Alignment', 'xclear'),
            'type'    => \Elementor\Controls_Manager::CHOOSE,
            'options' => [
                'left' => [
                    'title' => esc_html__('Left', 'xclear'),
                    'icon'  => 'eicon-text-align-left',
                ],
                'center' => [
                    'title' => esc_html__('Center', 'xclear'),
                    'icon'  => 'eicon-text-align-center',
                ],
                'right' => [
                    'title' => esc_html__('Right', 'xclear'),
                    'icon'  => 'eicon-text-align-right',
                ],
            ],
            'default' => 'center',
            'selectors' => [
                '{{WRAPPER}} .xclear-section-header' => 'text-align: {{VALUE}};',
                '{{WRAPPER}} .xclear-section-header__badge-wrap' => 'text-align: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();

        // Styles Tab - Badge
        $this->start_controls_section('section_style_badge', [
            'label' => esc_html__('Badge', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => [
                'badge_text!' => '',
            ],
        ]);

        $this->add_control('badge_color', [
            'label'     => esc_html__('Text Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-section-header__badge' => self::COLOR_VALUE,
                '{{WRAPPER}} .xclear-section-header__badge-dot' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('badge_bg_color', [
            'label'     => esc_html__('Background Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-section-header__badge' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();

        // Styles Tab - Title
        $this->start_controls_section('section_style_title', [
            'label' => esc_html__('Title', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('title_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-section-header__title' => self::COLOR_VALUE,
            ],
        ]);

        $this->end_controls_section();

        // Styles Tab - Description
        $this->start_controls_section('section_style_desc', [
            'label' => esc_html__('Description', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('desc_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-section-header__desc' => self::COLOR_VALUE,
            ],
        ]);

        $this->add_responsive_control('desc_max_width', [
            'label'      => esc_html__('Max Width', 'xclear'),
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'range'      => [
                'px' => [
                    'min' => 100,
                    'max' => 1200,
                ],
                '%' => [
                    'min' => 10,
                    'max' => 100,
                ],
            ],
            'selectors'  => [
                '{{WRAPPER}} .xclear-section-header__desc' => 'max-width: {{SIZE}}{{UNIT}}; display: inline-block; width: 100%;',
            ],
        ]);

        $this->end_controls_section();
    }

    private static function parseHighlight(string $text): string
    {
        return preg_replace(
            '/\[hl\](.*?)\[\/hl\]/s',
            '<span class="xclear-hl">$1</span>',
            $text
        );
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();

        $badge_text = $settings['badge_text'] ?? '';
        $show_dot   = $settings['show_badge_dot'] === 'yes';
        $title_text = $settings['title_text'] ?? '';
        $title_tag  = $settings['title_tag'] ?? 'h2';
        $desc_text  = $settings['description_text'] ?? '';
        ?>
        <div class="xclear-section-header">
            <?php if (! empty($badge_text)) : ?>
                <div class="xclear-section-header__badge-wrap">
                    <span class="xclear-section-header__badge">
                        <?php if ($show_dot) : ?>
                            <span class="xclear-section-header__badge-dot"></span>
                        <?php endif; ?>
                        <?php echo esc_html($badge_text); ?>
                    </span>
                </div>
            <?php endif; ?>

            <?php if (! empty($title_text)) : ?>
                <<?php echo esc_attr($title_tag); ?> class="xclear-section-header__title">
                    <?php echo nl2br(self::parseHighlight($title_text)); ?>
                </<?php echo esc_attr($title_tag); ?>>
            <?php endif; ?>

            <?php if (! empty($desc_text)) : ?>
                <div class="xclear-section-header__desc">
                    <?php echo do_shortcode($desc_text); ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
}
