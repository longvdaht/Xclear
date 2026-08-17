<?php

if (! defined('ABSPATH')) {
    exit;
}

class Pond_Card_Grid extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'pond_card_grid';
    }

    public function get_title()
    {
        return esc_html__('Pond Card Grid', 'practice-theme');
    }

    public function get_icon()
    {
        return 'eicon-gallery-grid';
    }

    public function get_categories()
    {
        return ['custom-widget'];
    }

    public function get_style_depends()
    {
        return ['pond-card-grid-widget'];
    }


    protected function register_controls() {
        $this->start_controls_section( 'section_cards', [
            'label' => esc_html__( 'Cards', 'practice-theme' ),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'cards', [
            'label'       => esc_html__( 'Cards', 'practice-theme' ),
            'type'        => \Elementor\Controls_Manager::REPEATER,
            'fields'      => $this->_get_repeater_fields(),
            'default'     => [
                [ 'card_title' => 'Koi Pond', 'card_btn_text' => 'Details' ],
                [ 'card_title' => 'Natural Pond', 'card_btn_text' => 'Details' ],
            ],
            'title_field' => '{{{ card_title }}}',
        ] );

        $this->end_controls_section();
    }

    private function _get_repeater_fields() {
        $repeater = new \Elementor\Repeater();

        $repeater->add_control( 'card_icon', [ 'label' => esc_html__( 'Icon', 'practice-theme' ), 'type' => \Elementor\Controls_Manager::ICONS, 'default' => [ 'value' => 'fas fa-fish', 'library' => 'fa-default' ] ] );
        $repeater->add_control( 'card_image_desktop', [ 'label' => esc_html__( 'Background (Desktop)', 'practice-theme' ), 'type' => \Elementor\Controls_Manager::MEDIA ] );
        $repeater->add_control( 'card_image_mobile', [ 'label' => esc_html__( 'Background (Mobile)', 'practice-theme' ), 'type' => \Elementor\Controls_Manager::MEDIA ] );
        $repeater->add_control( 'card_title', [ 'label' => esc_html__( 'Title', 'practice-theme' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Koi Pond' ] );
        $repeater->add_control( 'card_title_color', ['label' => esc_html__( 'Title Color', 'practice-theme' ), 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#FFFFFF',] );
        $repeater->add_control( 'card_description', [ 'label' => esc_html__( 'Description', 'practice-theme' ), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'rows' => 4 ] );
        $repeater->add_control( 'card_description_color', ['label' => esc_html__( 'Description Color', 'practice-theme' ), 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#FFFFFF',] );
        
        $repeater->add_control( 'card_btn_divider', [ 'type' => \Elementor\Controls_Manager::DIVIDER ] );

        $repeater->add_control( 'card_btn_text', [ 'label' => esc_html__( 'Button Text', 'practice-theme' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Details' ] );
        $repeater->add_control( 'card_btn_url', [ 'label' => esc_html__( 'Button URL', 'practice-theme' ), 'type' => \Elementor\Controls_Manager::URL, 'show_external' => true ] );
        $repeater->add_control( 'card_btn_color', [ 'label' => esc_html__( 'Button Color', 'practice-theme' ), 'type' => \Elementor\Controls_Manager::SELECT, 'default' => 'btn-primary', 'options' => [ 'btn-primary' => 'Primary (Blue)', 'btn-secondary' => 'Secondary (Green)', 'btn-white' => 'White' ] ] );
        $repeater->add_control( 'card_btn_style', [ 'label' => esc_html__( 'Button Style', 'practice-theme' ), 'type' => \Elementor\Controls_Manager::SELECT, 'default' => 'btn-default', 'options' => [ 'btn-default' => 'Default', 'btn-outline' => 'Outline', 'btn-text' => 'Text' ] ] );
        $repeater->add_control( 'card_btn_size', [ 'label' => esc_html__( 'Button Size', 'practice-theme' ), 'type' => \Elementor\Controls_Manager::SELECT, 'default' => 'btn-small', 'options' => [ 'btn-large' => 'Large', 'btn-small' => 'Small' ] ] );

        return $repeater->get_controls();
    }

    protected function render() {
        $s = $this->get_settings_for_display();
        $card_title_color = ! empty( $slide['title_color'] ) ? $slide['title_color'] : '#FFFFFF';
        $card_desc_color  = ! empty( $slide['description_color'] ) ? $slide['description_color'] : '#FFFFFF';
        $cards = $s['cards'] ?? [];
        $overlay_color = 'rgba(10,40,30,0.72)'; 
        ?>
        <div class="card-grid-widget" style="--card-grid-columns: 3; --card-grid-overlay: <?php echo esc_attr( $overlay_color ); ?>;">
            <?php foreach ( $cards as $card ) :

                $img_d = ! empty( $card['card_image_desktop']['url'] ) ? esc_url( $card['card_image_desktop']['url'] ) : '';
                $img_m = ! empty( $card['card_image_mobile']['url'] ) ? esc_url( $card['card_image_mobile']['url'] ) : $img_d;
                $card_style = "--card-img: url('{$img_d}'); --card-img-mobile: url('{$img_m}');";
                $btn_url = ! empty( $card['card_btn_url']['url'] ) ? esc_url( $card['card_btn_url']['url'] ) : '#';
                $btn_class = implode( ' ', [
                    'btn',
                    esc_attr( $card['card_btn_size'] ?? 'btn-small' ),
                    esc_attr( $card['card_btn_color'] ?? 'btn-primary' ),
                    esc_attr( $card['card_btn_style'] ?? 'btn-default' ),
                ] );
            ?>
                <div class="card-grid-card" style="<?php echo esc_attr( $card_style ); ?>">
                    <div class="card-grid-overlay"></div>
                    <div class="card-grid-content">

                        <?php if ( ! empty( $card['card_icon']['value'] ) ) : ?>
                            <div class="card-grid-icon">
                                <?php \Elementor\Icons_Manager::render_icon( $card['card_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ( ! empty( $card['card_title'] ) ) : ?>
                            <?php $title_color_style = ! empty( $card['card_title_color'] ) ? "color: {$card['card_title_color']};" : ''; ?>
                            <h4 class="card-grid-title h4 fw-medium" style="<?php echo esc_attr( $title_color_style ); ?>">
                                <?php echo esc_html__( $card['card_title'], 'practice-theme' ); ?>
                            </h4>
                        <?php endif; ?>

                        <?php if ( ! empty( $card['card_description'] ) ) : ?>
                            <?php $desc_color_style = ! empty( $card['card_description_color'] ) ? "color: {$card['card_description_color']};" : ''; ?>
                            <p class="card-grid-desc text-normal fw-regular" style="<?php echo esc_attr( $desc_color_style ); ?>">
                                <?php echo nl2br( esc_html__( $card['card_description'], 'practice-theme' ) ); ?>
                            </p>
                        <?php endif; ?>

                        <?php if ( ! empty( $card['card_btn_text'] ) ) : ?>
                            <a href="<?php echo esc_url( $btn_url ); ?>" class="<?php echo esc_attr( $btn_class ); ?>">
                                <?php echo esc_html__( $card['card_btn_text'], 'practice-theme' ); ?>
                            </a>
                        <?php endif; ?>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }
}