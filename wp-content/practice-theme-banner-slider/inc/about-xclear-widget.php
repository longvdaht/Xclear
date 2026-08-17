<?php
if (! defined('ABSPATH')) {
    exit;
}

class About_Xclear extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'about_xclear';
    }

    public function get_title()
    {
        return esc_html__('About Xclear', 'practice-theme');
    }

    public function get_icon()
    {
        return 'eicon-columns';
    }

    public function get_categories()
    {
        return ['custom-widget'];
    }

    public function get_style_depends()
    {
        return ['about-xclear-widget'];
    }


    protected function register_controls() {
        $this->_register_badge_section();
        $this->_register_heading_section();
        $this->_register_content_section();
        $this->_register_bg_section();
        $this->_register_image_section();
        $this->_register_cta_section();
    }

    private function _register_badge_section() {
        $this->start_controls_section( 'section_badge', [ 'label' => esc_html__( 'Badge', 'practice-theme' ) ] );
        $this->add_control( 'badge_text', ['label' => esc_html__( 'Badge Text', 'practice-theme' ), 'type'  => \Elementor\Controls_Manager::TEXT, 'default' => 'About Xclear UV-C',] );
        $this->add_control( 'badge_show', ['label' => esc_html__( 'Show Badge', 'practice-theme' ), 'type'  => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes',] );
        $this->end_controls_section();
    }

    private function _register_heading_section() {
        $this->start_controls_section( 'section_heading', [ 'label' => esc_html__( 'Heading (Left)', 'practice-theme' ) ] );
        $this->add_control( 'heading', [ 'label' => esc_html__( 'Heading', 'practice-theme' ), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'default' => 'Crystal clear water with Xclear UV-C' ] );
        $this->add_control( 'heading_color', [ 'label' => esc_html__( 'Heading Color', 'practice-theme' ), 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#FFFFFF' ] );
        $this->end_controls_section();
    }

    private function _register_content_section() {
        $this->start_controls_section( 'section_content', [ 'label' => esc_html__( 'Content (Left)', 'practice-theme' ) ] );
        $this->add_control( 'paragraph_1', [ 'label' => esc_html__( 'Paragraph 1', 'practice-theme' ), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'default' => 'UV-C disinfection...' ] );
        $this->add_control( 'paragraph_2', [ 'label' => esc_html__( 'Paragraph 2', 'practice-theme' ), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'default' => 'By integrating...' ] );
        $this->add_control( 'content_color', [ 'label' => esc_html__( 'Text Color', 'practice-theme' ), 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#FFFFFF' ] );
        $this->end_controls_section();
    }

    private function _register_bg_section() {
        $this->start_controls_section( 'section_left_bg', [ 'label' => esc_html__( 'Left Background', 'practice-theme' ) ] );
        $this->add_control( 'left_bg_type', [ 'label' => esc_html__( 'Background Type', 'practice-theme' ), 'type' => \Elementor\Controls_Manager::SELECT, 'default' => 'gradient', 'options' => [ 'gradient' => 'Gradient', 'image' => 'Image' ] ] );
        $this->add_control( 'left_gradient_from', [ 'label' => 'From', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#1a6b6b', 'condition' => [ 'left_bg_type' => 'gradient' ] ] );
        $this->add_control( 'left_gradient_to', [ 'label' => 'To', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#4ab3c0', 'condition' => [ 'left_bg_type' => 'gradient' ] ] );
        $this->add_control( 'left_bg_image', [ 'label' => 'Image', 'type' => \Elementor\Controls_Manager::MEDIA, 'condition' => [ 'left_bg_type' => 'image' ] ] );
        $this->end_controls_section();
    }

    private function _register_image_section() {
        $this->start_controls_section( 'section_right_image', [ 'label' => esc_html__( 'Image (Right)', 'practice-theme' ) ] );
        $this->add_control( 'right_image', [ 'label' => esc_html__( 'Image', 'practice-theme' ), 'type' => \Elementor\Controls_Manager::MEDIA ] );
        $this->end_controls_section();
    }

    private function _register_cta_section() {
        $this->start_controls_section( 'section_cta', [ 'label' => esc_html__( 'CTA Bar (Right)', 'practice-theme' ) ] );
        $this->add_control( 'cta_text', [ 'label' => 'CTA Text', 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Contact Us' ] );
        $this->add_control( 'cta_url', [ 'label' => 'URL', 'type' => \Elementor\Controls_Manager::URL ] );
        $this->add_control( 'cta_bg_color', [ 'label' => 'Background Color', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#254D29' ] );
        $this->add_control( 'cta_text_color', [ 'label' => 'Text Color', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#ffffff' ] );
        $this->end_controls_section();
    }

    protected function render() {
        $s = $this->get_settings_for_display();

        $left_bg = '';
        if ( $s['left_bg_type'] === 'gradient' ) {
            $left_bg = "background: linear-gradient(135deg, {$s['left_gradient_from']} 0%, {$s['left_gradient_to']} 100%);";
        } elseif ( ! empty( $s['left_bg_image']['url'] ) ) {
            $left_bg = "background-image: url('{$s['left_bg_image']['url']}'); background-size: cover; background-position: center;";
        }

        $this->add_render_attribute( 'left_wrapper', [ 'class' => 'split-section-left', 'style' => $left_bg ] );
        $this->add_render_attribute( 'heading', [ 'style' => "color: {$s['heading_color']};" ] );
        $this->add_render_attribute( 'content', [ 'style' => "color: {$s['content_color']};" ] );
        $this->add_render_attribute( 'cta', [ 
            'href' => esc_url( $s['cta_url']['url'] ?? '#' ),
            'class' => 'split-section-cta',
            'style' => "background-color: {$s['cta_bg_color']}; color: {$s['cta_text_color']};"
        ] );

        ?>
        <div class="split-section-widget">
            <div <?php $this->print_render_attribute_string( 'left_wrapper' ); ?>>
                <?php if ( 'yes' === $s['badge_show'] && ! empty( $s['badge_text'] ) ) : ?>
                    <span class="split-section-badge text-small fw-medium">
                        <span class="split-section-badge-dot"></span>
                        <?php echo esc_html__( $s['badge_text'], 'practice-theme' ); ?>
                    </span>
                <?php endif; ?>

                <?php if ( ! empty( $s['heading'] ) ) : ?>
                    <h2 class="split-section-heading h2 fw-medium" <?php $this->print_render_attribute_string( 'heading' ); ?>>
                        <?php echo nl2br( esc_html__( $s['heading'], 'practice-theme' ) ); ?>
                    </h2>
                <?php endif; ?>

                <div class="split-section-content" <?php $this->print_render_attribute_string( 'content' ); ?>>
                    <?php if ( ! empty( $s['paragraph_1'] ) ) echo '<p class="text-regular">' . nl2br( esc_html__( $s['paragraph_1'], 'practice-theme' ) ) . '</p>'; ?>
                    <?php if ( ! empty( $s['paragraph_2'] ) ) echo '<p class="text-regular">' . nl2br( esc_html__( $s['paragraph_2'], 'practice-theme' ) ) . '</p>'; ?>
                </div>
            </div>

            <div class="split-section-right">
                <?php if ( ! empty( $s['right_image']['url'] ) ) : ?>
                    <div class="split-section-image">
                        <?php
                            $image = $s['right_image'];
                            if ( ! empty( $image['id'] ) ) {
                                echo wp_get_attachment_image( $image['id'], 'full' );
                            }
                        ?>
                    </div>
                <?php endif; ?>

                <a <?php $this->print_render_attribute_string( 'cta' ); ?>>
                    <span class="split-section-cta-text h4 fw-medium"><?php echo esc_html__( $s['cta_text'], 'practice-theme' ); ?></span>
                    <span class="split-section-cta-arrow">→</span>
                </a>
            </div>
        </div>
        <?php
    }
}