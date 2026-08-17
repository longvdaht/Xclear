<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Xclear_About_Box_Widget extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'xclear_about_box_widget';
    }

    public function get_title()
    {
        return esc_html__('Xclear About Box', 'xclear');
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
        return ['xclear-about-box-widget-css'];
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
                'default' => esc_html__('About Xclear UV-C', 'xclear'),
            ]
        );

        $this->add_control(
            'box_title',
            [
                'label' => esc_html__('Title', 'xclear'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => esc_html__('Crystal clear water with Xclear UV-C', 'xclear'),
                'rows' => 3,
            ]
        );

        $this->add_control(
            'box_description',
            [
                'label' => esc_html__('Description', 'xclear'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => esc_html__("UV-C disinfection is the most effective, natural solution to prevent green pond water caused by single-celled algae. By passing water through a powerful UV-C filter, the DNA of microorganisms like bacteria and viruses is neutralized, stopping them from multiplying without the use of chemicals.\n\nBy integrating UV-C technology into your pond system, you benefit from improved water clarity, reduced maintenance, and a healthier pond ecosystem.", 'xclear'),
                'rows' => 8,
            ]
        );

        $this->add_control(
            'background_image',
            [
                'label' => esc_html__('Custom Background Image (Optional)', 'xclear'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'description' => esc_html__('Upload a background texture. If empty, the box uses CSS gradients.', 'xclear'),
            ]
        );

        $this->add_control(
            'background_image_mobile',
            [
                'label'       => esc_html__('Background Image (Mobile)', 'xclear'),
                'type'        => \Elementor\Controls_Manager::MEDIA,
                'description' => esc_html__('Optional. If empty, the desktop image is used on mobile.', 'xclear'),
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        $bg_style = '';
        if (!empty($settings['background_image']['url'])) {
            $bg_style .= '--bg-desktop: url(' . esc_url($settings['background_image']['url']) . ');';
        }
        if (!empty($settings['background_image_mobile']['url'])) {
            $bg_style .= '--bg-mobile: url(' . esc_url($settings['background_image_mobile']['url']) . ');';
        }

?>
        <div class="xclear-about-box" style="<?php echo esc_attr($bg_style); ?>">
            <div class="xclear-about-box__content">
                <?php if (!empty($settings['badge_text'])) : ?>
                    <div class="xclear-about-box__badge">
                        <span class="xclear-about-box__badge-dot"></span>
                        <?php echo esc_html($settings['badge_text']); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($settings['box_title'])) : ?>
                    <h2 class="xclear-about-box__title">
                        <?php echo wp_kses_post($settings['box_title']); ?>
                    </h2>
                <?php endif; ?>

                <?php if (!empty($settings['box_description'])) : ?>
                    <div class="xclear-about-box__description">
                        <?php echo nl2br(esc_html($settings['box_description'])); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
<?php
    }
}
