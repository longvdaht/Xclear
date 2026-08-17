<?php
if (! defined('ABSPATH')) {
    exit;
}

class XclearUvcHowItWorksWidget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'xclear_uvc_how_it_works_widget';
    }

    public function get_title()
    {
        return esc_html__('UVC How It Works', 'xclear');
    }

    public function get_icon()
    {
        return 'eicon-carousel';
    }

    public function get_categories()
    {
        return ['xclear-widgets'];
    }

    public function get_style_depends()
    {
        return ['xclear-uvc-how-it-works-widget-css'];
    }

    public function get_script_depends()
    {
        return ['xclear-uvc-how-it-works-widget-js'];
    }

    protected function register_controls()
    {
        $this->registerHeaderSection();
        $this->registerStepsSection();
        $this->registerTipsSection();
        $this->registerStyleHeaderSection();
        $this->registerStyleStepsSection();
        $this->registerStyleTipsSection();
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
            'default' => esc_html__('HOW IT WORKS', 'xclear'),
        ]);

        $this->add_control('heading', [
            'label'       => esc_html__('Heading', 'xclear'),
            'type'        => \Elementor\Controls_Manager::TEXTAREA,
            'default'     => 'How does [hl]UV-C[/hl] work?',
            'description' => esc_html__('Use [hl]...[/hl] for highlighted text.', 'xclear'),
        ]);

        $this->add_control('description', [
            'label'   => esc_html__('Description', 'xclear'),
            'type'    => \Elementor\Controls_Manager::TEXTAREA,
            'default' => esc_html__('The process of UV-C disinfection is a highly efficient method of water treatment that helps maintain clear and healthy pond water.', 'xclear'),
        ]);

        $this->end_controls_section();
    }

    // ── Content: Steps ────────────────────────────────────────────────────────

    private function registerStepsSection()
    {
        $this->start_controls_section('section_steps', [
            'label' => esc_html__('Steps', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $repeater = new \Elementor\Repeater();

        $repeater->add_control('step_title', [
            'label'   => esc_html__('Title', 'xclear'),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => esc_html__('Step title', 'xclear'),
        ]);

        $repeater->add_control('step_desc', [
            'label'   => esc_html__('Description', 'xclear'),
            'type'    => \Elementor\Controls_Manager::TEXTAREA,
            'default' => esc_html__('Step description goes here.', 'xclear'),
        ]);

        $this->add_control('steps', [
            'label'       => esc_html__('Steps', 'xclear'),
            'type'        => \Elementor\Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'default'     => [
                [
                    'step_title' => 'Water circulation',
                    'step_desc'  => 'Your pond pump continuously circulates water through the filtration system and into the Xclear UV-C unit.',
                ],
                [
                    'step_title' => 'Mechanical pre-filtration',
                    'step_desc'  => 'Before entering the UV-C unit, it is recommended that water passes through a mechanical filter. This removes debris and particles, allowing the UV-C light to work more effectively.',
                ],
                [
                    'step_title' => 'UV-C exposure',
                    'step_desc'  => 'Inside the unit, the water flows past a UV-C lamp. The ultraviolet radiation disrupts the DNA of algae, bacteria, and other microorganisms, preventing them from reproducing.',
                ],
                [
                    'step_title' => 'Targeted treatment',
                    'step_desc'  => 'UV-C only affects organisms that pass through the unit, making it a safe and environmentally friendly solution without impacting the overall biological balance of the pond.',
                ],
                [
                    'step_title' => 'Return to the pond',
                    'step_desc'  => 'The treated water flows back into the pond.',
                ],
                [
                    'step_title' => 'Continuous process',
                    'step_desc'  => 'As the water is constantly circulated, more and more microorganisms are neutralized over time, leading to long-term water clarity and stability.',
                ],
            ],
            'title_field' => '{{{ step_title }}}',
        ]);

        $this->end_controls_section();
    }

    // ── Content: Performance Tips ─────────────────────────────────────────────

    private function registerTipsSection()
    {
        $this->start_controls_section('section_tips', [
            'label' => esc_html__('Performance Tips', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('tips_title', [
            'label'   => esc_html__('Section Title', 'xclear'),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => esc_html__('Important for optimal performance:', 'xclear'),
        ]);

        $repeater = new \Elementor\Repeater();

        $repeater->add_control('tip_icon', [
            'label'   => esc_html__('Icon', 'xclear'),
            'type'    => \Elementor\Controls_Manager::ICONS,
            'default' => [
                'value'   => 'fas fa-lightbulb',
                'library' => 'fa-solid',
            ],
        ]);

        $repeater->add_control('tip_text', [
            'label'   => esc_html__('Text', 'xclear'),
            'type'    => \Elementor\Controls_Manager::TEXTAREA,
            'default' => esc_html__('Tip text goes here.', 'xclear'),
        ]);

        $this->add_control('tips', [
            'label'       => esc_html__('Tips', 'xclear'),
            'type'        => \Elementor\Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'default'     => [
                [
                    'tip_icon' => ['value' => 'fas fa-lightbulb', 'library' => 'fa-solid'],
                    'tip_text' => 'Proper flow rate is essential: water should not pass too quickly or too slowly through the unit.',
                ],
                [
                    'tip_icon' => ['value' => 'fas fa-lightbulb', 'library' => 'fa-solid'],
                    'tip_text' => 'Correct sizing of the UV-C unit based on pond volume ensures maximum effectiveness.',
                ],
                [
                    'tip_icon' => ['value' => 'fas fa-lightbulb', 'library' => 'fa-solid'],
                    'tip_text' => 'Regular maintenance, such as cleaning the quartz sleeve and replacing the UV lamp annually, is necessary to maintain performance.',
                ],
            ],
            'title_field' => '{{{ tip_text }}}',
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

        $this->add_control('badge_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .xclear-uvc-how-it-works__badge' => 'color: {{VALUE}}'],
        ]);

        $this->add_control('badge_bg', [
            'label'     => esc_html__('Background', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .xclear-uvc-how-it-works__badge' => 'background-color: {{VALUE}}; border-color: {{VALUE}}'],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'badge_typography',
            'selector' => '{{WRAPPER}} .xclear-uvc-how-it-works__badge',
        ]);

        $this->add_control('style_heading_heading', [
            'label'     => esc_html__('Heading', 'xclear'),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('heading_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .xclear-uvc-how-it-works__heading' => 'color: {{VALUE}}'],
        ]);

        $this->add_control('hl_color', [
            'label'     => esc_html__('Highlight Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .xclear-uvc-how-it-works__heading .xclear-hl' => 'color: {{VALUE}}'],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'heading_typography',
            'selector' => '{{WRAPPER}} .xclear-uvc-how-it-works__heading',
        ]);

        $this->add_control('style_desc_heading', [
            'label'     => esc_html__('Description', 'xclear'),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('desc_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .xclear-uvc-how-it-works__desc' => 'color: {{VALUE}}'],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'desc_typography',
            'selector' => '{{WRAPPER}} .xclear-uvc-how-it-works__desc',
        ]);

        $this->end_controls_section();
    }

    // ── Style: Steps ──────────────────────────────────────────────────────────

    private function registerStyleStepsSection()
    {
        $this->start_controls_section('style_steps', [
            'label' => esc_html__('Steps', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('style_step_number_heading', [
            'label' => esc_html__('Number', 'xclear'),
            'type'  => \Elementor\Controls_Manager::HEADING,
        ]);

        $this->add_control('step_number_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .xclear-uvc-how-it-works__step-number' => 'color: {{VALUE}}'],
        ]);

        $this->add_control('step_number_bg', [
            'label'     => esc_html__('Background', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .xclear-uvc-how-it-works__step-number' => 'background-color: {{VALUE}}'],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'step_number_typography',
            'selector' => '{{WRAPPER}} .xclear-uvc-how-it-works__step-number',
        ]);

        $this->add_control('style_step_connector_heading', [
            'label'     => esc_html__('Connector', 'xclear'),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('step_line_color', [
            'label'     => esc_html__('Line Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .xclear-uvc-how-it-works__step-connector' => 'background-color: {{VALUE}}'],
        ]);

        $this->add_control('style_step_title_heading', [
            'label'     => esc_html__('Step Title', 'xclear'),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('step_title_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .xclear-uvc-how-it-works__step-title' => 'color: {{VALUE}}'],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'step_title_typography',
            'selector' => '{{WRAPPER}} .xclear-uvc-how-it-works__step-title',
        ]);

        $this->add_control('style_step_desc_heading', [
            'label'     => esc_html__('Step Description', 'xclear'),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('step_desc_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .xclear-uvc-how-it-works__step-desc' => 'color: {{VALUE}}'],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'step_desc_typography',
            'selector' => '{{WRAPPER}} .xclear-uvc-how-it-works__step-desc',
        ]);

        $this->end_controls_section();
    }

    // ── Style: Performance Tips ───────────────────────────────────────────────

    private function registerStyleTipsSection()
    {
        $this->start_controls_section('style_tips', [
            'label' => esc_html__('Performance Tips', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('style_tips_title_heading', [
            'label' => esc_html__('Title', 'xclear'),
            'type'  => \Elementor\Controls_Manager::HEADING,
        ]);

        $this->add_control('tips_title_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .xclear-uvc-how-it-works__tips-title' => 'color: {{VALUE}}'],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'tips_title_typography',
            'selector' => '{{WRAPPER}} .xclear-uvc-how-it-works__tips-title',
        ]);

        $this->add_control('style_tip_card_heading', [
            'label'     => esc_html__('Card', 'xclear'),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('tip_card_bg', [
            'label'     => esc_html__('Background', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .xclear-uvc-how-it-works__tip-card' => 'background-color: {{VALUE}}'],
        ]);

        $this->add_control('tip_icon_color', [
            'label'     => esc_html__('Icon Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-uvc-how-it-works__tip-icon'     => 'color: {{VALUE}}',
                '{{WRAPPER}} .xclear-uvc-how-it-works__tip-icon svg' => 'fill: {{VALUE}}',
            ],
        ]);

        $this->add_control('tip_text_color', [
            'label'     => esc_html__('Text Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .xclear-uvc-how-it-works__tip-text' => 'color: {{VALUE}}'],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'tip_text_typography',
            'selector' => '{{WRAPPER}} .xclear-uvc-how-it-works__tip-text',
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
        $settings    = $this->get_settings_for_display();
        $steps       = $settings['steps'] ?? [];
        $steps_count = count($steps);
        $tips        = $settings['tips'] ?? [];
        ?>
        <div class="xclear-uvc-how-it-works">

            <?php if ($settings['badge_text'] || $settings['heading'] || $settings['description']) : ?>
            <div class="xclear-uvc-how-it-works__header">
                <?php if ($settings['badge_text']) : ?>
                <span class="xclear-uvc-how-it-works__badge">
                    <span class="xclear-uvc-how-it-works__badge-dot"></span>
                    <?php echo esc_html($settings['badge_text']); ?>
                </span>
                <?php endif; ?>
                <?php if ($settings['heading']) : ?>
                <h2 class="xclear-uvc-how-it-works__heading"><?php echo self::parseHighlight($settings['heading']); ?></h2>
                <?php endif; ?>
                <?php if ($settings['description']) : ?>
                <p class="xclear-uvc-how-it-works__desc"><?php echo esc_html($settings['description']); ?></p>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if (! empty($steps)) : ?>
            <div class="xclear-uvc-how-it-works__steps-wrap">
                <div class="xclear-uvc-how-it-works__steps">
                    <?php foreach ($steps as $index => $step) : ?>
                    <div class="xclear-uvc-how-it-works__step">
                        <div class="xclear-uvc-how-it-works__step-header">
                            <div class="xclear-uvc-how-it-works__step-number"><?php echo esc_html($index + 1); ?></div>
                            <?php if ($index < $steps_count - 1) : ?>
                            <div class="xclear-uvc-how-it-works__step-connector" aria-hidden="true"></div>
                            <?php endif; ?>
                        </div>
                        <?php if ($step['step_title']) : ?>
                        <h5 class="xclear-uvc-how-it-works__step-title"><?php echo esc_html($step['step_title']); ?></h5>
                        <?php endif; ?>
                        <?php if ($step['step_desc']) : ?>
                        <p class="xclear-uvc-how-it-works__step-desc"><?php echo esc_html($step['step_desc']); ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($settings['tips_title'] || ! empty($tips)) : ?>
            <div class="xclear-uvc-how-it-works__tips-section">
                <?php if ($settings['tips_title']) : ?>
                <h5 class="xclear-uvc-how-it-works__tips-title"><?php echo esc_html($settings['tips_title']); ?></h5>
                <?php endif; ?>
                <?php if (! empty($tips)) : ?>
                <div class="xclear-uvc-how-it-works__tips-grid">
                    <?php foreach ($tips as $tip) : ?>
                    <div class="xclear-uvc-how-it-works__tip-card">
                        <?php if (! empty($tip['tip_icon']['value'])) : ?>
                        <div class="xclear-uvc-how-it-works__tip-icon">
                            <?php \Elementor\Icons_Manager::render_icon($tip['tip_icon'], ['aria-hidden' => 'true']); ?>
                        </div>
                        <?php endif; ?>
                        <?php if ($tip['tip_text']) : ?>
                        <p class="xclear-uvc-how-it-works__tip-text"><?php echo esc_html($tip['tip_text']); ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

        </div>
        <?php
    }
}
