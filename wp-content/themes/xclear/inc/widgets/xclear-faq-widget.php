<?php
if (! defined('ABSPATH')) {
    exit;
}

class XclearFaqWidget extends \Elementor\Widget_Base
{
    private const COLOR_VALUE = 'color: {{VALUE}};';

    public function get_name()
    {
        return 'xclear_faq_widget';
    }

    public function get_title()
    {
        return esc_html__('Xclear FAQ', 'xclear');
    }

    public function get_icon()
    {
        return 'eicon-accordion';
    }

    public function get_categories()
    {
        return ['xclear-widgets'];
    }

    public function get_style_depends()
    {
        return ['xclear-faq-widget-css'];
    }

    public function get_script_depends()
    {
        return ['xclear-faq-widget-js'];
    }

    protected function register_controls()
    {
        $this->registerHeaderSection();
        $this->registerFaqItemsSection();
        $this->registerStyleBadgeSection();
        $this->registerStyleHeadingSection();
        $this->registerStyleCategorySection();
        $this->registerStyleQuestionSection();
        $this->registerStyleAnswerSection();
    }

    /* ─── Content: Header ───────────────────────────────────────────────── */

    private function registerHeaderSection(): void
    {
        $this->start_controls_section('header_section', [
            'label' => esc_html__('Header', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('badge_text', [
            'label'   => esc_html__('Badge Text', 'xclear'),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => esc_html__('FAQ', 'xclear'),
        ]);

        $this->add_control('show_badge_dot', [
            'label'        => esc_html__('Show Badge Dot', 'xclear'),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'label_on'     => esc_html__('Show', 'xclear'),
            'label_off'    => esc_html__('Hide', 'xclear'),
            'return_value' => 'yes',
            'default'      => 'yes',
            'condition'    => [
                'badge_text!' => '',
            ],
        ]);

        $this->add_control('heading', [
            'label'   => esc_html__('Heading', 'xclear'),
            'type'    => \Elementor\Controls_Manager::TEXTAREA,
            'default' => esc_html__('Lorem ipsum dolor sit amet adipiscing elit', 'xclear'),
        ]);

        $this->add_control('description', [
            'label'   => esc_html__('Description', 'xclear'),
            'type'    => \Elementor\Controls_Manager::TEXTAREA,
            'default' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam pellentesque neque eget dui facilisis mattis. Aliquam a sollicitudin odio.', 'xclear'),
        ]);

        $this->end_controls_section();
    }

    /* ─── Content: FAQ Items ────────────────────────────────────────────── */

    private function registerFaqItemsSection(): void
    {
        $this->start_controls_section('faq_items_section', [
            'label' => esc_html__('FAQ Items', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $repeater = new \Elementor\Repeater();

        $repeater->add_control('item_type', [
            'label'   => esc_html__('Item Type', 'xclear'),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'faq',
            'options' => [
                'category' => esc_html__('Category Header', 'xclear'),
                'faq'      => esc_html__('FAQ Item', 'xclear'),
            ],
        ]);

        $repeater->add_control('faq_category', [
            'label'       => esc_html__('Category Name', 'xclear'),
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => esc_html__('General questions', 'xclear'),
            'label_block' => true,
            'description' => esc_html__('Questions placed below this header will be grouped under this category.', 'xclear'),
            'condition'   => [
                'item_type' => 'category',
            ],
        ]);

        $repeater->add_control('faq_question', [
            'label'       => esc_html__('Question', 'xclear'),
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => esc_html__('Vivamus in tristique tellus, a ultricies turpis?', 'xclear'),
            'label_block' => true,
            'condition'   => [
                'item_type' => 'faq',
            ],
        ]);

        $repeater->add_control('faq_answer', [
            'label'   => esc_html__('Answer', 'xclear'),
            'type'    => \Elementor\Controls_Manager::WYSIWYG,
            'default' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam pellentesque neque eget dui facilisis mattis. Aliquam a sollicitudin odio. Donec sed lobortis elit. Praesent luctus odio nec nibh venenatis, ut pharetra metus pulvinar.', 'xclear'),
            'condition'   => [
                'item_type' => 'faq',
            ],
        ]);

        $this->add_control('faq_items', [
            'label'       => esc_html__('FAQ Items', 'xclear'),
            'type'        => \Elementor\Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'default'     => [
                [
                    'item_type'    => 'category',
                    'faq_category' => esc_html__('UV-C technology', 'xclear'),
                ],
                [
                    'item_type'    => 'faq',
                    'faq_question' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit?', 'xclear'),
                    'faq_answer'   => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam pellentesque neque eget dui facilisis mattis. Aliquam a sollicitudin odio. Donec sed lobortis elit. Praesent luctus odio nec nibh venenatis, ut pharetra metus pulvinar.', 'xclear'),
                ],
                [
                    'item_type'    => 'faq',
                    'faq_question' => esc_html__('Vivamus in tristique tellus, a ultricies turpis?', 'xclear'),
                    'faq_answer'   => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'xclear'),
                ],
                [
                    'item_type'    => 'faq',
                    'faq_question' => esc_html__('Vivamus in tristique tellus, a ultricies turpis?', 'xclear'),
                    'faq_answer'   => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'xclear'),
                ],
                [
                    'item_type'    => 'faq',
                    'faq_question' => esc_html__('Vivamus in tristique tellus, a ultricies turpis?', 'xclear'),
                    'faq_answer'   => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'xclear'),
                ],
                [
                    'item_type'    => 'category',
                    'faq_category' => esc_html__('Xclear UV-C systems', 'xclear'),
                ],
                [
                    'item_type'    => 'faq',
                    'faq_question' => esc_html__('Vivamus in tristique tellus, a ultricies turpis?', 'xclear'),
                    'faq_answer'   => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'xclear'),
                ],
                [
                    'item_type'    => 'faq',
                    'faq_question' => esc_html__('Vivamus in tristique tellus, a ultricies turpis?', 'xclear'),
                    'faq_answer'   => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'xclear'),
                ],
                [
                    'item_type'    => 'faq',
                    'faq_question' => esc_html__('Vivamus in tristique tellus, a ultricies turpis?', 'xclear'),
                    'faq_answer'   => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'xclear'),
                ],
                [
                    'item_type'    => 'category',
                    'faq_category' => esc_html__('General questions', 'xclear'),
                ],
                [
                    'item_type'    => 'faq',
                    'faq_question' => esc_html__('Vivamus in tristique tellus, a ultricies turpis?', 'xclear'),
                    'faq_answer'   => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'xclear'),
                ],
                [
                    'item_type'    => 'faq',
                    'faq_question' => esc_html__('Vivamus in tristique tellus, a ultricies turpis?', 'xclear'),
                    'faq_answer'   => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'xclear'),
                ],
                [
                    'item_type'    => 'faq',
                    'faq_question' => esc_html__('Vivamus in tristique tellus, a ultricies turpis?', 'xclear'),
                    'faq_answer'   => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'xclear'),
                ],
            ],
            'title_field' => '{{{ item_type == "category" ? "📂 " + faq_category : faq_question }}}',
        ]);

        $this->end_controls_section();
    }

    /* ─── Style: Badge ──────────────────────────────────────────────────── */

    private function registerStyleBadgeSection(): void
    {
        $this->start_controls_section('style_badge_section', [
            'label' => esc_html__('Badge', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('badge_text_color', [
            'label'     => esc_html__('Text Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-faq__badge' => self::COLOR_VALUE,
                '{{WRAPPER}} .xclear-faq__badge-dot' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('badge_bg_color', [
            'label'     => esc_html__('Background Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-faq__badge' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('badge_border_color', [
            'label'     => esc_html__('Border Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-faq__badge' => 'border-color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();
    }

    /* ─── Style: Heading ────────────────────────────────────────────────── */

    private function registerStyleHeadingSection(): void
    {
        $this->start_controls_section('style_heading_section', [
            'label' => esc_html__('Heading & Description', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('heading_heading', [
            'label' => esc_html__('Heading', 'xclear'),
            'type'  => \Elementor\Controls_Manager::HEADING,
        ]);

        $this->add_control('heading_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-faq__heading' => self::COLOR_VALUE,
            ],
        ]);

        $this->add_control('description_heading', [
            'label'     => esc_html__('Description', 'xclear'),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('description_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-faq__description' => self::COLOR_VALUE,
            ],
        ]);

        $this->end_controls_section();
    }

    /* ─── Style: Category Title ─────────────────────────────────────────── */

    private function registerStyleCategorySection(): void
    {
        $this->start_controls_section('style_category_section', [
            'label' => esc_html__('Category Title', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('category_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-faq__category-title' => self::COLOR_VALUE,
            ],
        ]);

        $this->end_controls_section();
    }

    /* ─── Style: Question ───────────────────────────────────────────────── */

    private function registerStyleQuestionSection(): void
    {
        $this->start_controls_section('style_question_section', [
            'label' => esc_html__('Question', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('question_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-faq__question' => self::COLOR_VALUE,
            ],
        ]);

        $this->add_control('question_active_heading', [
            'label'     => esc_html__('Active State', 'xclear'),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('question_active_bg', [
            'label'     => esc_html__('Background Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-faq__item.is-active .xclear-faq__question' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();
    }

    /* ─── Style: Answer ─────────────────────────────────────────────────── */

    private function registerStyleAnswerSection(): void
    {
        $this->start_controls_section('style_answer_section', [
            'label' => esc_html__('Answer', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('answer_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-faq__answer' => self::COLOR_VALUE,
            ],
        ]);

        $this->add_control('answer_bg_color', [
            'label'     => esc_html__('Background Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-faq__item.is-active .xclear-faq__answer' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();
    }

    /* ─── Render ────────────────────────────────────────────────────────── */

    protected function render(): void
    {
        $settings    = $this->get_settings_for_display();
        $badge       = $settings['badge_text'] ?? '';
        $heading     = $settings['heading'] ?? '';
        $description = $settings['description'] ?? '';
        $items       = $settings['faq_items'] ?? [];

        if (empty($items)) {
            return;
        }

        // Group items by category (preserve order)
        $grouped = [];
        $current_category = esc_html__('General', 'xclear');

        foreach ($items as $item) {
            $type = $item['item_type'] ?? 'faq';

            if ($type === 'category') {
                $current_category = !empty($item['faq_category']) ? $item['faq_category'] : esc_html__('Unnamed Category', 'xclear');
                if (!isset($grouped[$current_category])) {
                    $grouped[$current_category] = [];
                }
            } else {
                if (!isset($grouped[$current_category])) {
                    $grouped[$current_category] = [];
                }
                // Only add if there is an actual question
                if (!empty($item['faq_question'])) {
                    $grouped[$current_category][] = $item;
                }
            }
        }

        // Remove categories with no questions
        $grouped = array_filter($grouped, function ($cat_items) {
            return !empty($cat_items);
        });

        if (empty($grouped)) {
            return;
        }

        $widget_id = $this->get_id();
        ?>
        <div class="xclear-faq" id="xclear-faq-<?php echo esc_attr($widget_id); ?>">
            <!-- Sidebar -->
            <div class="xclear-faq__sidebar">
                <?php if (! empty($badge)) : ?>
                    <span class="xclear-faq__badge">
                        <?php if ($settings['show_badge_dot'] === 'yes') : ?>
                            <span class="xclear-faq__badge-dot"></span>
                        <?php endif; ?>
                        <?php echo esc_html($badge); ?>
                    </span>
                <?php endif; ?>

                <?php if (! empty($heading)) : ?>
                    <h2 class="xclear-faq__heading"><?php echo esc_html($heading); ?></h2>
                <?php endif; ?>

                <?php if (! empty($description)) : ?>
                    <p class="xclear-faq__description"><?php echo esc_html($description); ?></p>
                <?php endif; ?>

                <?php if (count($grouped) > 1) : ?>
                    <nav class="xclear-faq__nav">
                        <?php foreach ($grouped as $cat_name => $cat_items) :
                            $cat_slug = sanitize_title($cat_name);
                        ?>
                            <a href="#faq-<?php echo esc_attr($widget_id . '-' . $cat_slug); ?>"
                               class="xclear-faq__nav-link"
                               data-faq-target="faq-<?php echo esc_attr($widget_id . '-' . $cat_slug); ?>">
                                <span class="xclear-faq__nav-arrow"><?php echo self::renderChevronRightSvg(); ?></span>
                                <?php echo esc_html($cat_name); ?>
                            </a>
                        <?php endforeach; ?>
                    </nav>
                <?php endif; ?>
            </div>

            <!-- Content -->
            <div class="xclear-faq__content">
                <?php
                $is_first_item = true;
                foreach ($grouped as $cat_name => $cat_items) :
                    $cat_slug = sanitize_title($cat_name);
                ?>
                    <div class="xclear-faq__category"
                         id="faq-<?php echo esc_attr($widget_id . '-' . $cat_slug); ?>">
                        <h3 class="xclear-faq__category-title"><?php echo esc_html($cat_name); ?></h3>

                        <?php foreach ($cat_items as $faq) :
                            $active_class = $is_first_item ? ' is-active' : '';
                        ?>
                            <div class="xclear-faq__item<?php echo esc_attr($active_class); ?>">
                                <div class="xclear-faq__question" 
                                        aria-expanded="<?php echo $is_first_item ? 'true' : 'false'; ?>">
                                    <span class="xclear-faq__question-text">
                                        <?php echo esc_html($faq['faq_question']); ?>
                                    </span>
                                </div>
                                <div class="xclear-faq__answer">
                                    <div class="xclear-faq__answer-inner">
                                        <?php echo wp_kses_post($faq['faq_answer']); ?>
                                    </div>
                                </div>
                            </div>
                        <?php
                            $is_first_item = false;
                        endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /* ─── SVG Icons ─────────────────────────────────────────────────────── */

    private static function renderCheckSvg(): string
    {
        return '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M16.6668 5L7.50016 14.1667L3.3335 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>';
    }

    private static function renderChevronRightSvg(): string
    {
        return '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M7.5 15L12.5 10L7.5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>';
    }
}
