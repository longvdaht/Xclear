<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Xclear_Applications_Widget extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'xclear_applications_widget';
    }

    public function get_title()
    {
        return esc_html__('Xclear Applications', 'xclear');
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
        return ['xclear-applications-widget-css'];
    }

    protected function register_controls()
    {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__('Content', 'xclear'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'badge_text',
            [
                'label' => esc_html__('Badge Text', 'xclear'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('Applications', 'xclear'),
            ]
        );

        $this->add_control(
            'title_highlight',
            [
                'label' => esc_html__('Title Highlight (Teal Color)', 'xclear'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('UV-C', 'xclear'),
            ]
        );

        $this->add_control(
            'title_text',
            [
                'label' => esc_html__('Title Normal Text', 'xclear'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => esc_html__('for every pond and aquaculture application.', 'xclear'),
            ]
        );

        $this->add_control(
            'description',
            [
                'label' => esc_html__('Description Content', 'xclear'),
                'type' => \Elementor\Controls_Manager::WYSIWYG,
                'default' => '<p>' . esc_html__('A healthy pond starts with perfectly balanced water. Whether you are a pond professional, koi hobbyist, natural swimming enthusiast, or a DIY pond builder, maintaining clear and healthy water is essential. Because clear pond water, thriving fish, and vibrant plant life don\'t happen by chance. They require the right technology.', 'xclear') . '</p><p>' . esc_html__('With Xclear UV-C products, you can effortlessly maintain clear pond water and reliable pond water treatment. Our solutions provide effective algae control and support a biologically healthy pond environment.', 'xclear') . '</p>',
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        $badge_text = $settings['badge_text'];
        $title_highlight = $settings['title_highlight'];
        $title_text = $settings['title_text'];
        $description = $settings['description'];
?>
        <div class="xclear-applications__badge">
            <span class="dot"></span>
            <?php echo esc_html($badge_text); ?>
        </div>
        <div class="xclear-applications-wrapper">
            <div class="xclear-applications__inner">
                <div class="xclear-applications__left">
                    <h2 class="xclear-applications__title">
                        <?php if (!empty($title_highlight)) : ?>
                            <span class="highlight"><?php echo esc_html($title_highlight); ?></span>
                        <?php endif; ?>
                        <?php echo nl2br(esc_html($title_text)); ?>
                    </h2>
                </div>

                <div class="xclear-applications__right">
                    <div class="xclear-applications__desc">
                        <?php echo wp_kses_post($description); ?>
                    </div>
                </div>

            </div>
        </div>
<?php
    }
}
