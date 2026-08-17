<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class XclearImageWithTextWidget extends \Elementor\Widget_Base
{
    private const COLOR_VALUE = 'color: {{VALUE}};';

    public function get_name()
    {
        return 'xclear_image_with_text_widget';
    }

    public function get_title()
    {
        return esc_html__('Xclear Image with Text', 'xclear');
    }

    public function get_icon()
    {
        return 'eicon-image-rollover';
    }

    public function get_categories()
    {
        return ['xclear-widgets'];
    }

    public function get_style_depends()
    {
        return ['xclear-image-with-text-widget-css'];
    }

    public function get_script_depends()
    {
        return ['xclear-image-with-text-widget-js'];
    }

    private function registerContentSection(): void
    {
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__('Content', 'xclear'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'bg_mode',
            [
                'label'   => esc_html__('Background Mode', 'xclear'),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'content_position',
            [
                'label'   => esc_html__('Content Position', 'xclear'),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => 'Left',
                'label_off' => 'Right',
            ]
        );

        $this->add_control(
            'mobile_image_bottom',
            [
                'label'     => esc_html__('Image below content (mobile)', 'xclear'),
                'type'      => \Elementor\Controls_Manager::SWITCHER,
                'default'   => '',
                'label_on'  => esc_html__('Below', 'xclear'),
                'label_off' => esc_html__('Above', 'xclear'),
            ]
        );

        $this->add_control(
            'badge_text',
            [
                'label' => esc_html__('Badge Text', 'xclear'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('Badge Text', 'xclear'),
            ]
        );

        $this->add_control(
            'box_title',
            [
                'label'       => esc_html__('Title', 'xclear'),
                'type'        => \Elementor\Controls_Manager::TEXTAREA,
                'description' => esc_html__('Wrap highlighted words with [hl]...[/hl]. Example: [hl]Lorem ipsum[/hl] dolor sit amet.', 'xclear'),
                'default'     => esc_html__('Lorem ipsum dolor sit amet', 'xclear'),
                'rows'        => 3,
            ]
        );

        $this->add_control(
            'box_description',
            [
                'label' => esc_html__('Description', 'xclear'),
                'type' => \Elementor\Controls_Manager::WYSIWYG,
                'default' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec malesuada metus eu eros convallis, quis egestas nulla tristique. Nam mollis dictum feugiat. Vestibulum laoreet diam eu leo vulputate convallis. Vestibulum congue scelerisque dolor sed vehicula. Aenean efficitur quis nibh eget facilisis.', 'xclear'),
            ]
        );

        $this->add_control(
            'info_text',
            [
                'label'       => esc_html__('Information', 'xclear'),
                'description' => esc_html__('When filled: badge & title move to a full-width header row; information appears to the right of them. Image column is unaffected.', 'xclear'),
                'type'        => \Elementor\Controls_Manager::WYSIWYG,
                'default'     => '',
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'action_text',
            [
                'label' => esc_html__('Button Text', 'xclear'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('Button', 'xclear'),
            ]
        );

        $this->add_control(
            'button_link',
            [
                'label'         => esc_html__('Button Link', 'xclear'),
                'type'          => \Elementor\Controls_Manager::URL,
                'default'       => ['url' => ''],
                'placeholder'   => esc_html__('https://www.example.com', 'xclear'),
                'show_external' => true,
            ]
        );

        $this->add_control(
            'action_text_2',
            [
                'label' => esc_html__('Button Text 2', 'xclear'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('Button', 'xclear'),
            ]
        );

        $this->add_control(
            'button_link_2',
            [
                'label'         => esc_html__('Button Link 2', 'xclear'),
                'type'          => \Elementor\Controls_Manager::URL,
                'default'       => ['url' => ''],
                'placeholder'   => esc_html__('https://www.example2.com', 'xclear'),
                'show_external' => true,
            ]
        );

        $this->add_control(
            'image',
            [
                'label' => esc_html__('Image', 'xclear'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'description' => esc_html__('Upload an image.', 'xclear'),
            ]
        );

        $this->end_controls_section();
    }

    private function registerAccordionSection(): void {
        $this->start_controls_section(
            'accordion_section',
            [
                'label' => esc_html__('Accordion', 'xclear'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'acc_title',
            [
                'label'       => esc_html__('Title', 'xclear'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('', 'xclear'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'acc_content',
            [
                'label'       => esc_html__('Content', 'xclear'),
                'type'        => \Elementor\Controls_Manager::WYSIWYG,
                'default'     => esc_html__('', 'xclear'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'acc_list',
            [
                'label'       => esc_html__('Accordion Items', 'xclear'),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'title_field' => '{{{ acc_title }}}',
            ]
        );

        $this->end_controls_section();
    }

    protected function register_controls()
    {
        $this->registerContentSection();
        $this->registerAccordionSection();
        $this->registerStyleContentSection();
        $this->registerStyleAccordionSection();
        $this->registerStyleButtonSection();
    }

    private function registerStyleContentSection(): void
    {
        $this->start_controls_section('style_content_section', [
            'label' => esc_html__('Content', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('style_badge_heading', [
            'label' => esc_html__('Badge', 'xclear'),
            'type'  => \Elementor\Controls_Manager::HEADING,
        ]);

        $this->add_control('badge_bg_color', [
            'label'     => esc_html__('Background Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#e1f6f8',
            'selectors' => [
                '{{WRAPPER}} .xclear-img-with-text__badge' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('badge_color', [
            'label'     => esc_html__('Text Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#3B8F9A',
            'selectors' => [
                '{{WRAPPER}} .xclear-img-with-text__badge'        => self::COLOR_VALUE,
                '{{WRAPPER}} .xclear-img-with-text__badge:before' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'badge_typography',
            'selector' => '{{WRAPPER}} .xclear-img-with-text__badge',
        ]);

        $this->add_control('style_title_heading', [
            'label'     => esc_html__('Title', 'xclear'),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('title_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#263238',
            'selectors' => [
                '{{WRAPPER}} .xclear-img-with-text__title' => self::COLOR_VALUE,
            ],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'title_typography',
            'selector' => '{{WRAPPER}} .xclear-img-with-text__title',
        ]);

        $this->add_control('hl_color', [
            'label'     => esc_html__('Highlight Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#40A4B3',
            'separator' => 'before',
            'selectors' => [
                '{{WRAPPER}} .xclear-img-with-text__title .xclear-hl' => self::COLOR_VALUE,
            ],
        ]);

        $this->add_control('style_desc_heading', [
            'label'     => esc_html__('Description', 'xclear'),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('desc_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#546E7A',
            'selectors' => [
                '{{WRAPPER}} .xclear-img-with-text__desc'   => self::COLOR_VALUE,
                '{{WRAPPER}} .xclear-img-with-text__desc p' => self::COLOR_VALUE,
            ],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'desc_typography',
            'selector' => '{{WRAPPER}} .xclear-img-with-text__desc, {{WRAPPER}} .xclear-img-with-text__desc p',
        ]);

        $this->add_control('style_info_heading', [
            'label'     => esc_html__('Information', 'xclear'),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('info_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-img-with-text__info'   => self::COLOR_VALUE,
                '{{WRAPPER}} .xclear-img-with-text__info p' => self::COLOR_VALUE,
            ],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'info_typography',
            'selector' => '{{WRAPPER}} .xclear-img-with-text__info, {{WRAPPER}} .xclear-img-with-text__info p',
        ]);

        $this->end_controls_section();
    }

    private function registerStyleAccordionSection(): void
    {
        $this->start_controls_section('style_accordion_section', [
            'label' => esc_html__('Accordion', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('acc_title_heading', [
            'label' => esc_html__('Title', 'xclear'),
            'type'  => \Elementor\Controls_Manager::HEADING,
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'acc_title_typography',
            'selector' => '{{WRAPPER}} .xclear-img-with-text__acc__title',
        ]);

        $this->add_control('acc_title_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#263238',
            'selectors' => [
                '{{WRAPPER}} .xclear-img-with-text__acc__title' => self::COLOR_VALUE,
            ],
        ]);

        $this->add_control('acc_desc_heading', [
            'label'     => esc_html__('Description', 'xclear'),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'acc_desc_typography',
            'selector' => '{{WRAPPER}} .xclear-img-with-text__acc__desc, {{WRAPPER}} .xclear-img-with-text__acc__desc p',
        ]);

        $this->add_control('acc_desc_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#546E7A',
            'selectors' => [
                '{{WRAPPER}} .xclear-img-with-text__acc__desc'   => self::COLOR_VALUE,
                '{{WRAPPER}} .xclear-img-with-text__acc__desc p' => self::COLOR_VALUE,
            ],
        ]);

        $this->add_control('acc_active_bg', [
            'label'     => esc_html__('Active Background', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#F0F8FA',
            'selectors' => [
                '{{WRAPPER}} .xclear-img-with-text__acc__item.active' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();
    }

    private function registerStyleButtonSection(): void
    {
        $this->start_controls_section('style_button_section', [
            'label' => esc_html__('Buttons', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('btn_1_heading', [
            'label'     => esc_html__('Button 1', 'xclear'),
            'type'      => \Elementor\Controls_Manager::HEADING,
        ]);

        $this->add_control('btn_type_1', [
            'label'   => esc_html__('Type', 'xclear'),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'secondary',
            'options' => [
                'default'           => esc_html__('Default (Blue)', 'xclear'),
                'outline'           => esc_html__('Outline (Blue)', 'xclear'),
                'white'             => esc_html__('White', 'xclear'),
                'white_outline'     => esc_html__('White Outline', 'xclear'),
                'secondary'         => esc_html__('Secondary (Green)', 'xclear'),
                'secondary_outline' => esc_html__('Secondary Outline (Green)', 'xclear'),
                'light_green'       => esc_html__('Light Green', 'xclear'),
            ],
        ]);

        $this->add_control('btn_size_1', [
            'label'   => esc_html__('Size', 'xclear'),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'sm',
            'options' => [
                'md' => esc_html__('Default', 'xclear'),
                'sm' => esc_html__('Small', 'xclear'),
            ],
        ]);

        $this->add_control('btn_2_heading', [
            'label'     => esc_html__('Button 2', 'xclear'),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('btn_type_2', [
            'label'   => esc_html__('Type', 'xclear'),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'light_green',
            'options' => [
                'default'           => esc_html__('Default (Blue)', 'xclear'),
                'outline'           => esc_html__('Outline (Blue)', 'xclear'),
                'white'             => esc_html__('White', 'xclear'),
                'white_outline'     => esc_html__('White Outline', 'xclear'),
                'secondary'         => esc_html__('Secondary (Green)', 'xclear'),
                'secondary_outline' => esc_html__('Secondary Outline (Green)', 'xclear'),
                'light_green'       => esc_html__('Light Green', 'xclear'),
            ],
        ]);

        $this->add_control('btn_size_2', [
            'label'   => esc_html__('Size', 'xclear'),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'sm',
            'options' => [
                'md' => esc_html__('Default', 'xclear'),
                'sm' => esc_html__('Small', 'xclear'),
            ],
        ]);

        $this->end_controls_section();
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
            'light_green'       => 'btn btn-light-green',
        ];

        $classes = $map[$btnType] ?? 'btn btn-secondary';
        if ($size === 'sm') {
            $classes .= ' btn-small';
        }

        return $classes;
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $has_info = ! empty($settings['info_text']);
        $is_left  = $settings['content_position'] === 'yes';
        ?>
        <div class="xclear-img-with-text<?php if ($settings['bg_mode'] === 'yes') { echo ' has-bg'; } ?><?php echo $has_info ? ' has-info' : ''; ?>">

            <?php if ($has_info) : ?>
                <div class="xclear-img-with-text__header">
                    <?php if (!empty($settings['badge_text'])) : ?>
                        <div class="xclear-img-with-text__badge">
                            <?php echo esc_html($settings['badge_text']); ?>
                        </div>
                    <?php endif; ?>
                    <div class="xclear-img-with-text__header-row">
                        <?php if (!empty($settings['box_title'])) : ?>
                            <h2 class="xclear-img-with-text__title">
                                <?php echo nl2br(self::parseHighlight($settings['box_title'])); ?>
                            </h2>
                        <?php endif; ?>
                        <div class="xclear-img-with-text__info">
                            <?php echo wp_kses_post($settings['info_text']); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="xclear-img-with-text__content <?php echo $is_left ? 'content-left' : 'content-right'; ?>">
                <?php if (! $has_info) : ?>
                    <?php if (!empty($settings['badge_text'])) : ?>
                        <div class="xclear-img-with-text__badge">
                            <?php echo esc_html($settings['badge_text']); ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($settings['box_title'])) : ?>
                        <h2 class="xclear-img-with-text__title">
                            <?php echo nl2br(self::parseHighlight($settings['box_title'])); ?>
                        </h2>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if (!empty($settings['box_description'])) : ?>
                    <div class="xclear-img-with-text__desc">
                        <?php echo wp_kses_post($settings['box_description']); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($settings['acc_list'])) : ?>
                    <div class="xclear-img-with-text__acc">
                        <?php foreach ($settings['acc_list'] as $index => $item) : ?>
                            <div class="xclear-img-with-text__acc__item">
                                <h6 class="xclear-img-with-text__acc__title"><?php echo esc_html($item['acc_title']); ?></h6>
                                <div class="xclear-img-with-text__acc__desc"><?php echo wp_kses_post($item['acc_content']); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($settings['action_text']) || !empty($settings['action_text_2'])) : ?>
                    <div class="xclear-img-with-text__action">
                        <?php if (!empty($settings['action_text'])) : ?>
                            <a href="<?php echo esc_url($settings['button_link']['url']); ?>"
                               class="<?php echo esc_attr(self::getBtnClasses($settings['btn_type_1'] ?? 'secondary', $settings['btn_size_1'] ?? 'sm')); ?>"
                               <?php if (!empty($settings['button_link']['is_external'])) : ?>target="_blank" rel="noopener noreferrer"<?php endif; ?>>
                                <?php echo esc_html($settings['action_text']); ?>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($settings['action_text_2'])) : ?>
                            <a href="<?php echo esc_url($settings['button_link_2']['url']); ?>"
                               class="<?php echo esc_attr(self::getBtnClasses($settings['btn_type_2'] ?? 'light_green', $settings['btn_size_2'] ?? 'sm')); ?>"
                               <?php if (!empty($settings['button_link_2']['is_external'])) : ?>target="_blank" rel="noopener noreferrer"<?php endif; ?>>
                                <?php echo esc_html($settings['action_text_2']); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($settings['image'])) : ?>
                <div class="xclear-img-with-text__image <?php echo $is_left ? 'img-right' : 'img-left'; ?><?php echo $settings['mobile_image_bottom'] === 'yes' ? ' img-mobile-bottom' : ''; ?>">
                    <img src="<?php echo esc_url($settings['image']['url']); ?>" alt="<?php echo esc_attr($settings['image']['alt']); ?>">
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
}
