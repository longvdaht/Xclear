<?php
if (! defined('ABSPATH')) {
    exit;
}

class Xclear_Hero_Widget extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'xclear_hero_widget';
    }

    public function get_title()
    {
        return esc_html__('Xclear Hero', 'xclear');
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
        return ['swiper-css', 'xclear-hero-widget-css'];
    }

    public function get_script_depends()
    {
        return ['xclear-hero-widget-js'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'slides_section',
            [
                'label' => esc_html__('Hero', 'xclear'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'enable_slider',
            [
                'label'   => esc_html__('Slider Mode', 'xclear'),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        // --- Slider mode controls ---

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'hero_title',
            [
                'label'   => esc_html__('Title', 'xclear'),
                'type'    => \Elementor\Controls_Manager::TEXTAREA,
                'default' => esc_html__('UV-C – the difference is clear.', 'xclear'),
            ]
        );

        $repeater->add_control(
            'hero_desc',
            [
                'label'   => esc_html__('Description', 'xclear'),
                'type'    => \Elementor\Controls_Manager::TEXTAREA,
                'default' => esc_html__('Enjoy a pond that looks beautiful every day, without constant maintenance or worry.', 'xclear'),
            ]
        );

        $repeater->add_control(
            'hr_content_buttons',
            [
                'type' => \Elementor\Controls_Manager::DIVIDER,
            ]
        );

        $repeater->add_control(
            'btn_primary_text',
            [
                'label'   => esc_html__('Primary Button Text', 'xclear'),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('Explore Products', 'xclear'),
            ]
        );

        $repeater->add_control(
            'btn_primary_link',
            [
                'label'       => esc_html__('Primary Button Link', 'xclear'),
                'type'        => \Elementor\Controls_Manager::URL,
                'placeholder' => esc_html__('https://your-link.com', 'xclear'),
                'default'     => ['url' => '#'],
            ]
        );

        $repeater->add_control(
            'btn_secondary_text',
            [
                'label'   => esc_html__('Secondary Button Text', 'xclear'),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('Contact Us', 'xclear'),
            ]
        );

        $repeater->add_control(
            'btn_secondary_link',
            [
                'label'       => esc_html__('Secondary Button Link', 'xclear'),
                'type'        => \Elementor\Controls_Manager::URL,
                'placeholder' => esc_html__('https://your-link.com', 'xclear'),
                'default'     => ['url' => '#'],
            ]
        );

        $repeater->add_control(
            'hr_buttons_images',
            [
                'type' => \Elementor\Controls_Manager::DIVIDER,
            ]
        );

        $repeater->add_control(
            'bg_image',
            [
                'label'   => esc_html__('Background Image', 'xclear'),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()],
            ]
        );

        $repeater->add_control(
            'bg_image_mobile',
            [
                'label'       => esc_html__('Background Image (Mobile)', 'xclear'),
                'type'        => \Elementor\Controls_Manager::MEDIA,
                'description' => esc_html__('Optional. If empty, the desktop image is used on mobile.', 'xclear'),
            ]
        );

        $repeater->add_control(
            'right_image_back',
            [
                'label'   => esc_html__('Right Image (Back – Top Right)', 'xclear'),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()],
            ]
        );

        $repeater->add_control(
            'right_image_front',
            [
                'label'   => esc_html__('Right Image (Front – Bottom Left)', 'xclear'),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()],
            ]
        );

        $this->add_control(
            'slides',
            [
                'label'       => esc_html__('Slides', 'xclear'),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'default'     => [
                    [
                        'hero_title'         => esc_html__('UV-C – the difference is clear.', 'xclear'),
                        'hero_desc'          => esc_html__('Enjoy a pond that looks beautiful every day, without constant maintenance or worry. Whether you own a high-end koi pond or a DIY garden project, Xclear offers reliable solutions for every type of pond.', 'xclear'),
                        'btn_primary_text'   => esc_html__('Explore Products', 'xclear'),
                        'btn_primary_link'   => ['url' => '#'],
                        'btn_secondary_text' => esc_html__('Contact Us', 'xclear'),
                        'btn_secondary_link' => ['url' => '#'],
                    ],
                ],
                'title_field' => '{{{ hero_title }}}',
                'condition'   => ['enable_slider' => 'yes'],
            ]
        );

        // --- Static mode controls ---

        $this->add_control(
            'static_hero_title',
            [
                'label'     => esc_html__('Title', 'xclear'),
                'type'      => \Elementor\Controls_Manager::TEXTAREA,
                'default'   => esc_html__('UV-C – the difference is clear.', 'xclear'),
                'condition' => ['enable_slider' => ''],
            ]
        );

        $this->add_control(
            'static_hero_desc',
            [
                'label'     => esc_html__('Description', 'xclear'),
                'type'      => \Elementor\Controls_Manager::TEXTAREA,
                'default'   => esc_html__('Enjoy a pond that looks beautiful every day, without constant maintenance or worry. Whether you own a high-end koi pond or a DIY garden project, Xclear offers reliable solutions for every type of pond.', 'xclear'),
                'condition' => ['enable_slider' => ''],
            ]
        );

        $this->add_control(
            'static_hr_content_buttons',
            [
                'type'      => \Elementor\Controls_Manager::DIVIDER,
                'condition' => ['enable_slider' => ''],
            ]
        );

        $this->add_control(
            'static_btn_primary_text',
            [
                'label'     => esc_html__('Primary Button Text', 'xclear'),
                'type'      => \Elementor\Controls_Manager::TEXT,
                'default'   => esc_html__('Explore Products', 'xclear'),
                'condition' => ['enable_slider' => ''],
            ]
        );

        $this->add_control(
            'static_btn_primary_link',
            [
                'label'       => esc_html__('Primary Button Link', 'xclear'),
                'type'        => \Elementor\Controls_Manager::URL,
                'placeholder' => esc_html__('https://your-link.com', 'xclear'),
                'default'     => ['url' => '#'],
                'condition'   => ['enable_slider' => ''],
            ]
        );

        $this->add_control(
            'static_btn_secondary_text',
            [
                'label'     => esc_html__('Secondary Button Text', 'xclear'),
                'type'      => \Elementor\Controls_Manager::TEXT,
                'default'   => esc_html__('Contact Us', 'xclear'),
                'condition' => ['enable_slider' => ''],
            ]
        );

        $this->add_control(
            'static_btn_secondary_link',
            [
                'label'       => esc_html__('Secondary Button Link', 'xclear'),
                'type'        => \Elementor\Controls_Manager::URL,
                'placeholder' => esc_html__('https://your-link.com', 'xclear'),
                'default'     => ['url' => '#'],
                'condition'   => ['enable_slider' => ''],
            ]
        );

        $this->add_control(
            'static_hr_buttons_images',
            [
                'type'      => \Elementor\Controls_Manager::DIVIDER,
                'condition' => ['enable_slider' => ''],
            ]
        );

        $this->add_control(
            'static_bg_image',
            [
                'label'     => esc_html__('Background Image', 'xclear'),
                'type'      => \Elementor\Controls_Manager::MEDIA,
                'default'   => ['url' => \Elementor\Utils::get_placeholder_image_src()],
                'condition' => ['enable_slider' => ''],
            ]
        );

        $this->add_control(
            'static_bg_image_mobile',
            [
                'label'       => esc_html__('Background Image (Mobile)', 'xclear'),
                'type'        => \Elementor\Controls_Manager::MEDIA,
                'description' => esc_html__('Optional. If empty, the desktop image is used on mobile.', 'xclear'),
                'condition'   => ['enable_slider' => ''],
            ]
        );

        $this->add_control(
            'static_right_image_back',
            [
                'label'     => esc_html__('Right Image (Back – Top Right)', 'xclear'),
                'type'      => \Elementor\Controls_Manager::MEDIA,
                'default'   => ['url' => \Elementor\Utils::get_placeholder_image_src()],
                'condition' => ['enable_slider' => ''],
            ]
        );

        $this->add_control(
            'static_right_image_front',
            [
                'label'     => esc_html__('Right Image (Front – Bottom Left)', 'xclear'),
                'type'      => \Elementor\Controls_Manager::MEDIA,
                'default'   => ['url' => \Elementor\Utils::get_placeholder_image_src()],
                'condition' => ['enable_slider' => ''],
            ]
        );

        $this->end_controls_section();
    }

    private function render_hero_wrapper($data, $key_prefix)
    {
        $wrap_style = '';
        if (! empty($data['bg_image']['url'])) {
            $wrap_style .= '--bg-desktop: url(' . esc_url($data['bg_image']['url']) . ');';
        }
        if (! empty($data['bg_image_mobile']['url'])) {
            $wrap_style .= '--bg-mobile: url(' . esc_url($data['bg_image_mobile']['url']) . ');';
        }

        $img_back  = $data['right_image_back']['url']  ?? '';
        $img_front = $data['right_image_front']['url'] ?? '';
        $btn1_text = $data['btn_primary_text']         ?? '';
        $btn1_link = $data['btn_primary_link']         ?? [];
        $btn2_text = $data['btn_secondary_text']       ?? '';
        $btn2_link = $data['btn_secondary_link']       ?? [];

        $key1 = $key_prefix . '_btn1';
        $key2 = $key_prefix . '_btn2';

        if (! empty($btn1_link['url'])) {
            $this->add_link_attributes($key1, $btn1_link);
            $this->add_render_attribute($key1, 'class', 'btn-primary');
        }
        if (! empty($btn2_link['url'])) {
            $this->add_link_attributes($key2, $btn2_link);
            $this->add_render_attribute($key2, 'class', 'btn-secondary');
        }
?>
        <div class="xclear-hero-wrapper" style="<?php echo esc_attr($wrap_style); ?>">
            <div class="xclear-hero__inner">

                <div class="xclear-hero__content">
                    <?php if (! empty($data['hero_title'])) : ?>
                        <h2 class="xclear-hero__title"><?php echo wp_kses_post(nl2br($data['hero_title'])); ?></h2>
                    <?php endif; ?>

                    <?php if (! empty($data['hero_desc'])) : ?>
                        <p class="xclear-hero__desc"><?php echo wp_kses_post(nl2br($data['hero_desc'])); ?></p>
                    <?php endif; ?>

                    <div class="xclear-hero__actions">
                        <?php if (! empty($btn1_text) && ! empty($btn1_link['url'])) : ?>
                            <a <?php echo $this->get_render_attribute_string($key1); ?>>
                                <?php echo esc_html($btn1_text); ?>
                            </a>
                        <?php endif; ?>

                        <?php if (! empty($btn2_text) && ! empty($btn2_link['url'])) : ?>
                            <a <?php echo $this->get_render_attribute_string($key2); ?>>
                                <?php echo esc_html($btn2_text); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="xclear-hero__visuals">
                    <?php if (! empty($img_back)) : ?>
                        <img src="<?php echo esc_url($img_back); ?>" class="xclear-hero__img xclear-hero__img--back" alt="">
                    <?php endif; ?>
                    <?php if (! empty($img_front)) : ?>
                        <img src="<?php echo esc_url($img_front); ?>" class="xclear-hero__img xclear-hero__img--front" alt="">
                    <?php endif; ?>
                </div>

            </div>
        </div>
<?php
    }

    protected function render()
    {
        $settings  = $this->get_settings_for_display();
        $is_slider = $settings['enable_slider'] === 'yes';

        if ($is_slider) {
            $slides = $settings['slides'] ?? [];
            if (empty($slides)) {
                return;
            }
            $widget_id = $this->get_id();
?>
        <div class="swiper xclear-hero-slider" id="xclear-hs-<?php echo esc_attr($widget_id); ?>">
            <div class="swiper-wrapper">
                <?php foreach ($slides as $index => $slide) : ?>
                    <div class="swiper-slide">
                        <?php $this->render_hero_wrapper($slide, 'slide_' . $index); ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (count($slides) > 1) : ?>
                <div class="swiper-pagination"></div>
            <?php endif; ?>
        </div>
<?php
        } else {
            $data = [
                'hero_title'          => $settings['static_hero_title']          ?? '',
                'hero_desc'           => $settings['static_hero_desc']           ?? '',
                'btn_primary_text'    => $settings['static_btn_primary_text']    ?? '',
                'btn_primary_link'    => $settings['static_btn_primary_link']    ?? [],
                'btn_secondary_text'  => $settings['static_btn_secondary_text']  ?? '',
                'btn_secondary_link'  => $settings['static_btn_secondary_link']  ?? [],
                'bg_image'            => $settings['static_bg_image']            ?? [],
                'bg_image_mobile'     => $settings['static_bg_image_mobile']     ?? [],
                'right_image_back'    => $settings['static_right_image_back']    ?? [],
                'right_image_front'   => $settings['static_right_image_front']   ?? [],
            ];
            $this->render_hero_wrapper($data, 'static');
        }
    }
}
