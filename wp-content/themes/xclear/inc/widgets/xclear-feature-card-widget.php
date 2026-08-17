<?php
if (! defined('ABSPATH')) {
    exit;
}

class Xclear_Feature_Card_Widget extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'xclear_feature_card_widget';
    }

    public function get_title()
    {
        return esc_html__('Xclear Feature Card', 'xclear');
    }

    public function get_icon()
    {
        return 'eicon-image-box';
    }

    public function get_categories()
    {
        return ['xclear-widgets'];
    }

    public function get_style_depends()
    {
        return ['xclear-feature-card-widget-css'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__('Cards', 'xclear'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'card_icon',
            [
                'label'   => esc_html__('Icon', 'xclear'),
                'type'    => \Elementor\Controls_Manager::ICONS,
                'default' => [
                    'value'   => 'fas fa-fish',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $repeater->add_control(
            'card_title',
            [
                'label'       => esc_html__('Title', 'xclear'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Koi Pond', 'xclear'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'card_description',
            [
                'label'   => esc_html__('Description', 'xclear'),
                'type'    => \Elementor\Controls_Manager::TEXTAREA,
                'default' => esc_html__('Keep your koi pond water crystal clear and protect your fish from harmful bacteria and parasites.', 'xclear'),
                'rows'    => 5,
            ]
        );

        $repeater->add_control(
            'hr_content_button',
            [
                'type' => \Elementor\Controls_Manager::DIVIDER,
            ]
        );

        $repeater->add_control(
            'button_text',
            [
                'label'   => esc_html__('Button Text', 'xclear'),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('Details', 'xclear'),
            ]
        );

        $repeater->add_control(
            'button_link',
            [
                'label'       => esc_html__('Button Link', 'xclear'),
                'type'        => \Elementor\Controls_Manager::URL,
                'placeholder' => esc_html__('https://your-link.com', 'xclear'),
                'default'     => ['url' => '#'],
            ]
        );

        $repeater->add_control(
            'hr_button_images',
            [
                'type' => \Elementor\Controls_Manager::DIVIDER,
            ]
        );

        $repeater->add_control(
            'background_image',
            [
                'label'   => esc_html__('Background Image', 'xclear'),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()],
            ]
        );

        $this->add_control(
            'cards',
            [
                'label'       => esc_html__('Cards', 'xclear'),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'default'     => [
                    [
                        'card_title'       => esc_html__('Koi Pond', 'xclear'),
                        'card_description' => esc_html__('Keep your koi pond water crystal clear and protect your fish from harmful bacteria and parasites. Xclear UV-C filters improve water quality and support strong fish health, vibrant colours, and optimal growth.', 'xclear'),
                        'button_text'      => esc_html__('Details', 'xclear'),
                        'button_link'      => ['url' => '#'],
                    ],
                ],
                'title_field' => '{{{ card_title }}}',
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $cards    = $settings['cards'] ?? [];

        if (empty($cards)) {
            return;
        }
?>
        <div class="xclear-feature-cards">
            <?php foreach ($cards as $index => $card) :
                $btn_key = 'button_' . $index;
                if (! empty($card['button_link']['url'])) {
                    $this->add_link_attributes($btn_key, $card['button_link']);
                    $this->add_render_attribute($btn_key, 'class', 'xclear-btn');
                }
            ?>
                <div class="xclear-feature-card" style="<?php echo ! empty($card['background_image']['url']) ? 'background-image: url(' . esc_url($card['background_image']['url']) . ');' : ''; ?>">
                    <div class="xclear-feature-card__overlay"></div>
                    <div class="xclear-feature-card__content">
                        <?php if (! empty($card['card_icon']['value'])) : ?>
                            <div class="xclear-feature-card__icon">
                                <?php \Elementor\Icons_Manager::render_icon($card['card_icon'], ['aria-hidden' => 'true']); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (! empty($card['card_title'])) : ?>
                            <h3 class="xclear-feature-card__title"><?php echo esc_html($card['card_title']); ?></h3>
                        <?php endif; ?>

                        <?php if (! empty($card['card_description'])) : ?>
                            <div class="xclear-feature-card__description">
                                <?php echo wp_kses_post($card['card_description']); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (! empty($card['button_text']) && ! empty($card['button_link']['url'])) : ?>
                            <div class="xclear-feature-card__action">
                                <a <?php echo $this->get_render_attribute_string($btn_key); ?>>
                                    <?php echo esc_html($card['button_text']); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
<?php
    }
}
