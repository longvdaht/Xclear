<?php

if (! defined('ABSPATH')) {
    exit;
}

class XClear_Banner extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'banner_widget';
    }

    public function get_title()
    {
        return esc_html__('Xclear Banner', 'practice-theme');
    }

    public function get_icon()
    {
        return 'eicon-banner';
    }

    public function get_categories()
    {
        return ['custom-widget'];
    }

    public function get_style_depends()
    {
        return ['banner-widget'];
    }

    public function get_script_depends()
    {
        return ['banner-widget'];
    }


    protected function register_controls() {
        $this->start_controls_section( 'section_slides', [
            'label' => esc_html__( 'Slides', 'practice-theme' ),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $repeater = new \Elementor\Repeater();
        $repeater->add_control( 'title', [ 'label'   => esc_html__( 'Title', 'practice-theme' ), 'type'    => \Elementor\Controls_Manager::TEXTAREA, 'default' => 'UV-C – the difference is clear.', 'rows'    => 3,] );
        $repeater->add_control( 'title_color', ['label' => esc_html__( 'Title Color', 'practice-theme' ), 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#FFFFFF',] );
        $repeater->add_control( 'description', [ 'label'   => esc_html__( 'Description', 'practice-theme' ), 'type'    => \Elementor\Controls_Manager::TEXTAREA, 'default' => 'Enjoy a pond that looks beautiful every day, without constant maintenance or worry.', 'rows'    => 4, ] );
        $repeater->add_control( 'description_color', [ 'label' => esc_html__( 'Description Color', 'practice-theme' ), 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#FFFFFF', ] );


        $repeater->add_control( 'btn_primary_text', [ 'label' => esc_html__( 'Primary Button Text', 'practice-theme' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Explore Products' ] );
        $repeater->add_control( 'btn_primary_url',  [ 'label' => esc_html__( 'Primary Button URL', 'practice-theme' ),  'type' => \Elementor\Controls_Manager::URL, 'show_external' => true ] );
        $repeater->add_control( 'btn_primary_color', [ 'label' => esc_html__( 'Primary Color', 'practice-theme' ), 'type' => \Elementor\Controls_Manager::SELECT, 'default' => 'btn-secondary', 'options' => [ 'btn-primary' => 'Blue', 'btn-secondary' => 'Green', 'btn-white' => 'White' ] ] );
        $repeater->add_control( 'btn_primary_style', [ 'label' => esc_html__( 'Primary Style', 'practice-theme' ), 'type' => \Elementor\Controls_Manager::SELECT, 'default' => 'btn-default',   'options' => [ 'btn-default' => 'Default', 'btn-outline' => 'Outline', 'btn-text' => 'Text' ] ] );
        $repeater->add_control( 'btn_primary_size',  [ 'label' => esc_html__( 'Primary Size', 'practice-theme' ),  'type' => \Elementor\Controls_Manager::SELECT, 'default' => 'btn-large',   'options' => [ 'btn-large' => 'Large', 'btn-small' => 'Small' ] ] );

        $repeater->add_control( 'btn_divider', [ 'type' => \Elementor\Controls_Manager::DIVIDER ] );

        $repeater->add_control( 'btn_secondary_text', [ 'label' => esc_html__( 'Secondary Button Text', 'practice-theme' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Contact Us' ] );
        $repeater->add_control( 'btn_secondary_url',  [ 'label' => esc_html__( 'Secondary Button URL', 'practice-theme' ),  'type' => \Elementor\Controls_Manager::URL, 'show_external' => true ] );
        $repeater->add_control( 'btn_secondary_color', [ 'label' => esc_html__( 'Secondary Color', 'practice-theme' ), 'type' => \Elementor\Controls_Manager::SELECT, 'default' => 'btn-white',     'options' => [ 'btn-primary' => 'Blue', 'btn-secondary' => 'Green', 'btn-white' => 'White' ] ] );
        $repeater->add_control( 'btn_secondary_style', [ 'label' => esc_html__( 'Secondary Style', 'practice-theme' ), 'type' => \Elementor\Controls_Manager::SELECT, 'default' => 'btn-outline',   'options' => [ 'btn-default' => 'Default', 'btn-outline' => 'Outline', 'btn-text' => 'Text' ] ] );
        $repeater->add_control( 'btn_secondary_size',  [ 'label' => esc_html__( 'Secondary Size', 'practice-theme' ),  'type' => \Elementor\Controls_Manager::SELECT, 'default' => 'btn-large',   'options' => [ 'btn-large' => 'Large', 'btn-small' => 'Small' ] ] );

        $repeater->add_control( 'bg_divider', [ 'type' => \Elementor\Controls_Manager::DIVIDER ] );

        $repeater->add_control( 'background_image_desktop', ['label' => esc_html__( 'Background Image (Desktop)', 'practice-theme' ), 'type'  => \Elementor\Controls_Manager::MEDIA,] );
        $repeater->add_control( 'background_image_mobile', [ 'label' => esc_html__( 'Background Image (Mobile)', 'practice-theme' ), 'type'  => \Elementor\Controls_Manager::MEDIA, ] );
        $repeater->add_control( 'background_color', [ 'label'   => esc_html__( 'Background Color (fallback)', 'practice-theme' ), 'type'    => \Elementor\Controls_Manager::COLOR, 'default' => '#1a7a7a', ] );

        $repeater->add_control( 'img_divider', [ 'type' => \Elementor\Controls_Manager::DIVIDER ] );

        $repeater->add_control( 'image_front', [ 'label' => esc_html__( 'Front Image', 'practice-theme' ), 'type' => \Elementor\Controls_Manager::MEDIA ] );
        $repeater->add_control( 'image_back',  [ 'label' => esc_html__( 'Back Image',  'practice-theme' ), 'type' => \Elementor\Controls_Manager::MEDIA ] );

        $this->add_control( 'slides', [
            'label'       => esc_html__( 'Slides', 'practice-theme' ),
            'type'        => \Elementor\Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'default'     => [
                [
                    'title'       => 'UV-C – the difference is clear.',
                    'description' => 'Enjoy a pond that looks beautiful every day, without constant maintenance or.',
                ],
            ],
            'title_field' => '{{{ title }}}',
        ] );
        $this->end_controls_section();


        $this->start_controls_section( 'section_slider_settings', [
            'label' => esc_html__( 'Slider Settings', 'practice-theme' ),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'autoplay', [
            'label'        => esc_html__( 'Autoplay', 'practice-theme' ),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'label_on'     => esc_html__( 'Yes', 'practice-theme' ),
            'label_off'    => esc_html__( 'No', 'practice-theme' ),
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );

        $this->add_control( 'autoplay_delay', [
            'label'     => esc_html__( 'Autoplay Delay (ms)', 'practice-theme' ),
            'type'      => \Elementor\Controls_Manager::NUMBER,
            'default'   => 5000,
            'min'       => 1000,
            'step'      => 500,
            'condition' => [ 'autoplay' => 'yes' ],
        ] );

        $this->add_control( 'loop', [
            'label'        => esc_html__( 'Loop', 'practice-theme' ),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'label_on'     => esc_html__( 'Yes', 'practice-theme' ),
            'label_off'    => esc_html__( 'No', 'practice-theme' ),
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );

        $this->add_control( 'show_pagination', [
            'label'        => esc_html__( 'Show Pagination Dots', 'practice-theme' ),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'label_on'     => esc_html__( 'Yes', 'practice-theme' ),
            'label_off'    => esc_html__( 'No', 'practice-theme' ),
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        $s      = $this->get_settings_for_display();
        $title_color = ! empty( $slide['title_color'] ) ? $slide['title_color'] : '#FFFFFF';
        $desc_color  = ! empty( $slide['description_color'] ) ? $slide['description_color'] : '#FFFFFF';
        $slides = ! empty( $s['slides'] ) ? $s['slides'] : [];

        if ( empty( $slides ) ) return;

        $autoplay       = $s['autoplay'] === 'yes';
        $loop           = $s['loop'] === 'yes';
        $show_pagination = $s['show_pagination'] === 'yes';
        $autoplay_delay = intval( $s['autoplay_delay'] ?? 5000 );

        $slider_config = json_encode( [
            'loop'       => $loop,
            'autoplay'   => $autoplay ? [ 'delay' => $autoplay_delay, 'disableOnInteraction' => false ] : false,
            'pagination' => $show_pagination ? [ 'el' => '.swiper-pagination', 'clickable' => true ] : false,
        ] );

        $get_btn_class = function( $slide, $prefix ) {
            return implode( ' ', [
                'btn',
                esc_attr( $slide[ $prefix . '_size' ]  ?? 'btn-large' ),
                esc_attr( $slide[ $prefix . '_color' ] ?? 'btn-secondary' ),
                esc_attr( $slide[ $prefix . '_style' ] ?? 'btn-default' ),
            ] );
        };
        ?>

        <div class="banner-widget banner-slider swiper" data-slider-config='<?php echo esc_attr( $slider_config ); ?>'>
            <div class="swiper-wrapper">
                <?php foreach ( $slides as $slide ) :
                    $desktop = $slide['background_image_desktop'];
                    $mobile  = $slide['background_image_mobile'];
                    $bg_desktop = '';
                    $bg_mobile  = '';
                    if ( ! empty( $desktop['id'] ) ) {
                        $bg_desktop = wp_get_attachment_image_url( $desktop['id'], 'full' );
                    }
                    if ( ! empty( $mobile['id'] ) ) {
                        $bg_mobile = wp_get_attachment_image_url( $mobile['id'], 'full' );
                    } else {
                        $bg_mobile = $bg_desktop;
                    }
                    $bg_color   = esc_attr( $slide['background_color'] ?? '#1a7a7a' );

                    $slide_style = implode( ' ', [
                        "--bg-desktop: url('{$bg_desktop}');",
                        "--bg-mobile: url('{$bg_mobile}');",
                        "background-color: {$bg_color};",
                    ] );
                ?>
                    <div class="swiper-slide banner-slide" style="<?php echo $slide_style; ?>">
                        <div class="banner-inner">
                            <div class="left-banner">
                                
                                <?php if ( ! empty( $slide['title'] ) ) : ?>
                                    <?php $title_color = ! empty( $slide['title_color'] ) ? $slide['title_color'] : '#FFFFFF'; ?>
                                    <h1 class="banner-title h1 fw-medium" style="color: <?php echo esc_attr( $title_color); ?>;">
                                        <?php echo nl2br( esc_html__( $slide['title'], 'practice-theme' ) ); ?>
                                    </h1>
                                <?php endif; ?>
                                <?php if ( ! empty( $slide['description'] ) ) : ?>
                                    <?php $desc_color = ! empty( $slide['description_color'] ) ? $slide['description_color'] : '#FFFFFF'; ?>
                                    <p class="text-regular banner-description" style="color: <?php echo esc_attr( $desc_color); ?>;">
                                        <?php echo nl2br( esc_html__( $slide['description'], 'practice-theme' ) ); ?>
                                    </p>
                                <?php endif; ?>

                                <div class="banner-buttons">
                                    <?php if ( ! empty( $slide['btn_primary_text'] ) ) : ?>
                                        <?php
                                            $url = $slide['btn_primary_url']['url'] ?? '';
                                        ?>
                                        <a href="<?php echo esc_url( $url ?: '#' ); ?>" class="<?php echo esc_attr( $get_btn_class( $slide, 'btn_primary' ) ); ?>">
                                            <?php echo esc_html__( $slide['btn_primary_text'], 'practice-theme' ); ?>
                                        </a>        
                                    <?php endif; ?>

                                    <?php if ( ! empty( $slide['btn_secondary_text'] ) ) : ?>
                                        <?php
                                            $url = $slide['btn_secondary_url']['url'] ?? '';
                                        ?>
                                        <a href="<?php echo esc_url( $url ?: '#' ); ?>" class="<?php echo esc_attr( $get_btn_class( $slide, 'btn_secondary' ) ); ?>">
                                            <?php echo esc_html__( $slide['btn_secondary_text'], 'practice-theme' ); ?>
                                        </a>  
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if ( ! empty( $slide['image_front']['url'] ) || ! empty( $slide['image_back']['url'] ) ) : ?>
                                <div class="banner-images">
                                    <?php if ( ! empty( $slide['image_back']['url'] ) ) : ?>
                                        <div class="banner-img banner-img--back">
                                            <?php
                                                $image = $slide['image_back'];
                                                if ( ! empty( $image['id'] ) ) {
                                                    echo wp_get_attachment_image( $image['id'], 'full', false, [
                                                        'alt' => esc_attr( $image['alt'] ?? '' )
                                                    ] );
                                                }
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ( ! empty( $slide['image_front']['url'] ) ) : ?>
                                        <div class="banner-img banner-img--front">
                                            <?php
                                                $image = $slide['image_front'];
                                                if ( ! empty( $image['id'] ) ) {
                                                    echo wp_get_attachment_image( $image['id'], 'full', false, [
                                                        'alt' => esc_attr( $image['alt'] ?? '' )
                                                    ] );
                                                }
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ( $show_pagination ) : ?>
                <div class="swiper-pagination"></div>
            <?php endif; ?>
        </div>
        <?php
    }
}
