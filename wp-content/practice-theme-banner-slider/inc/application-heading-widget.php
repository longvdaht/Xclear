<?php

if (! defined('ABSPATH')) {
    exit;
}

class Section_Heading extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'section_heading';
    }

    public function get_title()
    {
        return esc_html__('Section Heading', 'practice-theme');
    }

    public function get_icon()
    {
        return 'eicon-heading';
    }

    public function get_categories()
    {
        return ['custom-widget'];
    }

    public function get_style_depends()
    {
        return ['application-heading-widget'];
    }


    protected function register_controls() {
        $this->_register_badge_section();
        $this->_register_heading_section();
        $this->_register_content_section();
    }

    private function _register_badge_section() {
        $this->start_controls_section( 'section_badge', [ 'label' => esc_html__( 'Badge', 'practice-theme' ) ] );
        $this->add_control( 'badge_text', ['label'   => esc_html__( 'Badge Text', 'practice-theme' ), 'type'    => \Elementor\Controls_Manager::TEXT, 'default' => 'Applications',] );
        $this->add_control( 'badge_show', [ 
            'label'        => esc_html__( 'Show Badge', 'practice-theme' ),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'label_on'     => esc_html__( 'Yes', 'practice-theme' ),
            'label_off'    => esc_html__( 'No', 'practice-theme' ),
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );

        $this->end_controls_section();
    }

    private function _register_heading_section() {
        $this->start_controls_section( 'section_heading', [ 'label' => esc_html__( 'Heading (Left)', 'practice-theme' ) ] );
        $this->add_control( 'heading_highlight', ['label'=> esc_html__( 'Highlighted Word(s)', 'practice-theme' ), 'type'=> \Elementor\Controls_Manager::TEXT, 'default'=> 'UV-C', 'description' => esc_html__( 'This part will be colored.', 'practice-theme' ),] );
        $this->add_control( 'heading_rest', [ 'label'   => esc_html__( 'Rest of Heading', 'practice-theme' ), 'type'    => \Elementor\Controls_Manager::TEXTAREA, 'default' => 'for every pond and aquaculture application.', 'rows'    => 3,] );
        $this->add_control( 'heading_highlight_color', [ 'label'   => esc_html__( 'Highlight Color', 'practice-theme' ), 'type'    => \Elementor\Controls_Manager::COLOR, 'default' => '#31828E', ] );
        $this->add_control( 'heading_color', [ 'label'   => esc_html__( 'Heading Color', 'practice-theme' ), 'type'    => \Elementor\Controls_Manager::COLOR, 'default' => '#263238',] );
        $this->end_controls_section();
    }

    private function _register_content_section() {
        $this->start_controls_section( 'section_content', [ 'label' => esc_html__( 'Content (Right)', 'practice-theme' ) ] );
        $this->add_control( 'paragraph_1', [ 'label'   => esc_html__( 'Paragraph 1', 'practice-theme' ), 'type'    => \Elementor\Controls_Manager::TEXTAREA, 'default' => 'A healthy pond starts with perfectly balanced water.', 'rows'    => 5, ] );
        $this->add_control( 'paragraph_2', [ 'label'   => esc_html__( 'Paragraph 2', 'practice-theme' ), 'type'    => \Elementor\Controls_Manager::TEXTAREA, 'default' => 'With Xclear UV-C products, you can effortlessly maintain clear pond water.', 'rows'    => 5,] );
        $this->add_control( 'content_color', ['label'   => esc_html__( 'Content Color', 'practice-theme' ), 'type'    => \Elementor\Controls_Manager::COLOR, 'default' => '#78909C', ] );
        $this->end_controls_section();
    }

    protected function render() {
        $s = $this->get_settings_for_display();

        $this->add_render_attribute( 'heading', 'class', ['sh-heading', 'h2', 'fw-medium'] );
        $this->add_render_attribute( 'heading', 'style', 'color: ' . esc_attr( $s['heading_color'] ) . ';' );
        $this->add_render_attribute( 'highlight', 'style', 'color: ' . esc_attr( $s['heading_highlight_color'] ) . ';' );
        $this->add_render_attribute( 'content', 'class', ['sh-right', 'sh-content'] );
        $this->add_render_attribute( 'content', 'style', 'color: ' . esc_attr( $s['content_color'] ) . ';' );

        ?>
        <div class="section-heading-widget">
            <?php if ( 'yes' === $s['badge_show'] && ! empty( $s['badge_text'] ) ) : ?>
                <span class="sh-badge text-small fw-medium">
                    <span class="sh-badge-dot"></span>
                    <?php echo esc_html__( $s['badge_text'], 'practice-theme' ); ?>
                </span>
            <?php endif; ?>

            <div class="sh-row">
                <div class="sh-left">
                    <h2 <?php $this->print_render_attribute_string( 'heading' ); ?>>
                        <?php if ( ! empty( $s['heading_highlight'] ) ) : ?>
                            <span <?php $this->print_render_attribute_string( 'highlight' ); ?>><?php echo esc_html__( $s['heading_highlight'], 'practice-theme' ); ?></span>&#32;
                        <?php endif; ?>
                        <?php echo esc_html__( $s['heading_rest'], 'practice-theme' ); ?>
                    </h2>
                </div>

                <div <?php $this->print_render_attribute_string( 'content' ); ?>>
                    <?php if ( ! empty( $s['paragraph_1'] ) ) : ?>
                        <p class="text-regular fw-regular"><?php echo nl2br( esc_html__( $s['paragraph_1'], 'practice-theme' ) ); ?></p>
                    <?php endif; ?>
                    <?php if ( ! empty( $s['paragraph_2'] ) ) : ?>
                        <p class="text-regular fw-regular"><?php echo nl2br( esc_html__( $s['paragraph_2'], 'practice-theme' ) ); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }
}