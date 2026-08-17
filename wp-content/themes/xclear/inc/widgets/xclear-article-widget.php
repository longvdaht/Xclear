<?php
if (! defined('ABSPATH')) {
    exit;
}

class XclearArticleWidget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'xclear_article_widget';
    }

    public function get_title()
    {
        return esc_html__('Xclear Articles', 'xclear');
    }

    public function get_icon()
    {
        return 'eicon-posts-grid';
    }

    public function get_categories()
    {
        return ['xclear-widgets'];
    }

    public function get_style_depends()
    {
        return ['xclear-article-widget-css'];
    }

    public function get_script_depends()
    {
        return ['xclear-article-widget-js'];
    }

    protected function register_controls()
    {
        /* ── Content ── */
        $this->start_controls_section('content_section', [
            'label' => esc_html__('Settings', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('post_type', [
            'label'   => esc_html__('Post Type', 'xclear'),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'post',
            'options' => array_merge(
                ['post' => esc_html__('Posts', 'xclear')],
                array_map(fn($pt) => $pt->label, array_filter(
                    get_post_types(['public' => true, '_builtin' => false], 'objects'),
                    fn($pt) => $pt->name !== 'elementor_library'
                ))
            ),
        ]);

        $this->add_control('category_taxonomy', [
            'label'       => esc_html__('Category Taxonomy', 'xclear'),
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => 'category',
            'description' => esc_html__('Taxonomy slug to display in sidebar (e.g., "category" for posts).', 'xclear'),
        ]);

        $this->add_control('posts_per_page', [
            'label'   => esc_html__('Posts Per Page', 'xclear'),
            'type'    => \Elementor\Controls_Manager::NUMBER,
            'default' => 6,
            'min'     => 1,
            'max'     => 24,
        ]);

        $this->add_control('all_posts_label', [
            'label'   => esc_html__('All Posts Label', 'xclear'),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => esc_html__('All Posts', 'xclear'),
        ]);

        $this->add_control('search_placeholder', [
            'label'   => esc_html__('Search Placeholder', 'xclear'),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => esc_html__('Search for articles', 'xclear'),
        ]);

        $this->add_control('load_more_text', [
            'label'   => esc_html__('Load More Button Text', 'xclear'),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => esc_html__('Load More', 'xclear'),
        ]);

        $this->end_controls_section();

        /* ── Style: Card ── */
        $this->start_controls_section('style_card_section', [
            'label' => esc_html__('Card', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('card_title_color', [
            'label'     => esc_html__('Title Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-article__card-title' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('card_excerpt_color', [
            'label'     => esc_html__('Excerpt Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-article__card-excerpt' => 'color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();

        /* ── Style: Button ── */
        $this->start_controls_section('style_button_section', [
            'label' => esc_html__('Load More Button', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('btn_bg_color', [
            'label'     => esc_html__('Background Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-article__load-more' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('btn_text_color', [
            'label'     => esc_html__('Text Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-article__load-more' => 'color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();

        $post_type       = $settings['post_type']         ?? 'post';
        $taxonomy        = $settings['category_taxonomy'] ?? 'category';
        $posts_per_page  = (int)($settings['posts_per_page'] ?? 6);
        $all_label       = $settings['all_posts_label']   ?? esc_html__('All Posts', 'xclear');
        $search_ph       = $settings['search_placeholder'] ?? esc_html__('Search for articles', 'xclear');
        $load_more_text  = $settings['load_more_text']    ?? esc_html__('Load More', 'xclear');

        // Get categories for sidebar
        $terms = get_terms([
            'taxonomy'   => $taxonomy,
            'hide_empty' => true,
        ]);

        $widget_id = $this->get_id();

        // Pass config to JS
        wp_localize_script('xclear-article-widget-js', 'xclearArticleConfig_' . $widget_id, [
            'ajaxUrl'       => admin_url('admin-ajax.php'),
            'nonce'         => wp_create_nonce('xclear_article_nonce'),
            'postType'      => $post_type,
            'taxonomy'      => $taxonomy,
            'postsPerPage'  => $posts_per_page,
            'allLabel'      => $all_label,
            'searchResults' => esc_html__('Search results for', 'xclear'),
            'noResults'     => esc_html__('No posts found.', 'xclear'),
            'widgetId'      => $widget_id,
        ]);
        ?>
        <div class="xclear-article" id="xclear-article-<?php echo esc_attr($widget_id); ?>"
             data-widget-id="<?php echo esc_attr($widget_id); ?>">

            <!-- Header: title + search -->
            <div class="xclear-article__header">
                <h2 class="xclear-article__heading"><?php echo esc_html($all_label); ?></h2>
                <div class="xclear-article__search-wrap">
                    <input type="text"
                           class="xclear-article__search-input"
                           placeholder="<?php echo esc_attr($search_ph); ?>"
                           autocomplete="off" />
                    <button class="xclear-article__search-clear" aria-label="Clear search" hidden>
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <path d="M12 4L4 12M4 4l8 8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </button>
                    <button class="xclear-article__search-btn" aria-label="Search">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.5 18C14.6421 18 18 14.6421 18 10.5C18 6.35786 14.6421 3 10.5 3C6.35786 3 3 6.35786 3 10.5C3 14.6421 6.35786 18 10.5 18Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M15.8047 15.8027L21.0012 20.9993" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Body: sidebar + grid -->
            <div class="xclear-article__body">
                <aside class="xclear-article__sidebar">
                    <p class="xclear-article__sidebar-title"><?php esc_html_e('Categories', 'xclear'); ?></p>
                    <ul class="xclear-article__categories">
                        <li>
                            <a class="xclear-article__cat-link is-active" data-category="">
                                <?php echo esc_html($all_label); ?>
                            </a>
                        </li>
                        <?php if (! is_wp_error($terms) && ! empty($terms)) : ?>
                            <?php foreach ($terms as $term) : ?>
                                <li>
                                    <a class="xclear-article__cat-link" data-category="<?php echo esc_attr($term->slug); ?>">
                                        <?php echo esc_html($term->name); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </aside>

                <!-- Article grid (populated via AJAX) -->
                <div class="xclear-article__grid">
                    <div class="xclear-article__posts"></div>
                    <div class="xclear-article__loading" hidden>
                        <div class="xclear-article__spinner"></div>
                    </div>
                    <div class="xclear-article__empty" hidden>
                        <?php esc_html_e('No articles found.', 'xclear'); ?>
                    </div>
                </div>
            </div>

            <!-- Footer: load more -->
            <div class="xclear-article__footer">
                <button class="xclear-article__load-more btn btn-secondary" hidden>
                    <?php echo esc_html($load_more_text); ?>
                </button>
            </div>
        </div>
        <?php
    }
}

// AJAX handler is defined in inc/article-ajax.php (always loaded via functions.php)
