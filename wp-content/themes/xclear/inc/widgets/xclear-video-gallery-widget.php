<?php
if (! defined('ABSPATH')) {
    exit;
}

class XclearVideoGalleryWidget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'xclear_video_gallery_widget';
    }

    public function get_title()
    {
        return esc_html__('Xclear Video Gallery', 'xclear');
    }

    public function get_icon()
    {
        return 'eicon-gallery-grid';
    }

    public function get_categories()
    {
        return ['xclear-widgets'];
    }

    public function get_style_depends()
    {
        return ['xclear-video-gallery-widget-css'];
    }

    public function get_script_depends()
    {
        return ['xclear-video-gallery-widget-js'];
    }

    protected function register_controls()
    {
        /* ── Content: Videos ── */
        $this->start_controls_section('content_section', [
            'label' => esc_html__('Videos', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('columns', [
            'label'   => esc_html__('Columns', 'xclear'),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => '3',
            'options' => [
                '2' => '2',
                '3' => '3',
                '4' => '4',
            ],
        ]);

        $repeater = new \Elementor\Repeater();

        $repeater->add_control('video_title', [
            'label'       => esc_html__('Title', 'xclear'),
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => esc_html__('Lorem ipsum dolor sit amet consec tetur adipiscing elit', 'xclear'),
            'label_block' => true,
        ]);

        $repeater->add_control('video_url', [
            'label'         => esc_html__('Video URL', 'xclear'),
            'type'          => \Elementor\Controls_Manager::URL,
            'placeholder'   => 'https://www.youtube.com/watch?v=...',
            'description'   => esc_html__('Supports YouTube, Vimeo, or direct MP4 URL.', 'xclear'),
            'label_block'   => true,
            'options'       => false,
        ]);

        $repeater->add_control('video_thumbnail', [
            'label'   => esc_html__('Thumbnail Image', 'xclear'),
            'type'    => \Elementor\Controls_Manager::MEDIA,
            'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()],
        ]);

        $this->add_control('videos', [
            'label'       => esc_html__('Video Items', 'xclear'),
            'type'        => \Elementor\Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'default'     => [
                [
                    'video_title' => esc_html__('Lorem ipsum dolor sit amet consec tetur adipiscing elit', 'xclear'),
                    'video_url'   => ['url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
                ],
                [
                    'video_title' => esc_html__('Lorem ipsum dolor sit amet consec tetur adipiscing elit', 'xclear'),
                    'video_url'   => ['url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
                ],
                [
                    'video_title' => esc_html__('Lorem ipsum dolor sit amet consec tetur adipiscing elit', 'xclear'),
                    'video_url'   => ['url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
                ],
            ],
            'title_field' => '{{{ video_title }}}',
        ]);

        $this->end_controls_section();

        /* ── Style: Card ── */
        $this->start_controls_section('style_card_section', [
            'label' => esc_html__('Card', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('card_bg_color', [
            'label'     => esc_html__('Card Background', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-vg__card' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('title_color', [
            'label'     => esc_html__('Title Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-vg__card-title' => 'color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();

    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $videos   = $settings['videos'] ?? [];
        $columns  = (int)($settings['columns'] ?? 3);

        if (empty($videos)) {
            return;
        }
        ?>
        <div class="xclear-vg xclear-vg--cols-<?php echo esc_attr($columns); ?>">
            <div class="xclear-vg__grid">
                <?php foreach ($videos as $index => $video) :
                    $title     = $video['video_title'] ?? '';
                    $video_url = $video['video_url']['url'] ?? '';
                    $thumb_url = $video['video_thumbnail']['url'] ?? '';
                    $embed_url = self::getEmbedUrl($video_url);
                ?>
                    <div class="xclear-vg__card"
                         data-video-url="<?php echo esc_attr($embed_url ?: $video_url); ?>"
                         data-video-type="<?php echo esc_attr($embed_url ? 'iframe' : 'html5'); ?>"
                         data-video-title="<?php echo esc_attr($title); ?>"
                         role="button"
                         tabindex="0"
                         aria-label="<?php echo esc_attr(sprintf(__('Play video: %s', 'xclear'), $title)); ?>">

                        <div class="xclear-vg__thumb-wrap">
                            <?php if ($thumb_url) : ?>
                                <img class="xclear-vg__thumb"
                                     src="<?php echo esc_url($thumb_url); ?>"
                                     alt="<?php echo esc_attr($title); ?>"
                                     loading="lazy" />
                            <?php else : ?>
                                <div class="xclear-vg__thumb xclear-vg__thumb--placeholder"></div>
                            <?php endif; ?>

                            <div class="xclear-vg__overlay"></div>

                            <button class="xclear-vg__play-btn" tabindex="-1" aria-hidden="true">
                                <svg width="58" height="58" viewBox="0 0 58 58" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="58" height="58" rx="29" fill="white" fill-opacity="0.5"/>
                                    <path d="M39.7811 27.2442C41.1708 28.0022 41.1708 29.9978 39.781 30.7558L23.9577 39.3867C22.625 40.1137 21 39.149 21 37.6309L21 20.3691C21 18.851 22.625 17.8863 23.9577 18.6133L39.7811 27.2442Z" fill="white"/>
                                </svg>
                            </button>
                        </div>

                        <?php if ($title) : ?>
                            <div class="xclear-vg__card-body">
                                <h6 class="xclear-vg__card-title"><?php echo esc_html($title); ?></h6>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Lightbox (rendered once, shared across all widgets on page) -->
        <?php self::renderLightbox(); ?>
        <?php
    }

    private static function renderLightbox(): void
    {
        // Only render once per page
        static $rendered = false;
        if ($rendered) return;
        $rendered = true;
        ?>
        <div class="xclear-vg-lightbox" id="xclear-vg-lightbox" role="dialog" aria-modal="true" aria-label="Video player" hidden>
            <div class="xclear-vg-lightbox__backdrop"></div>
            <div class="xclear-vg-lightbox__wrap">
                <div class="xclear-vg-lightbox__close" aria-label="Close video">
                    <svg class="e-font-icon-svg e-eicon-close" viewBox="0 0 1000 1000" xmlns="http://www.w3.org/2000/svg"><path d="M742 167L500 408 258 167C246 154 233 150 217 150 196 150 179 158 167 167 154 179 150 196 150 212 150 229 154 242 171 254L408 500 167 742C138 771 138 800 167 829 196 858 225 858 254 829L496 587 738 829C750 842 767 846 783 846 800 846 817 842 829 829 842 817 846 804 846 783 846 767 842 750 829 737L588 500 833 258C863 229 863 200 833 171 804 137 775 137 742 167Z"></path></svg>
                </div>
                <div class="xclear-vg-lightbox__player"></div>
            </div>
        </div>
        <?php
    }

    /**
     * Convert YouTube / Vimeo watch URL to embed URL.
     */
    private static function getEmbedUrl(string $url): string
    {
        if (empty($url)) return '';

        // YouTube
        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1] . '?autoplay=1&rel=0';
        }

        // Vimeo
        if (preg_match('/vimeo\.com\/(?:video\/)?(\d+)/', $url, $m)) {
            return 'https://player.vimeo.com/video/' . $m[1] . '?autoplay=1';
        }

        return ''; // direct / mp4 — handled as html5 in JS
    }
}
