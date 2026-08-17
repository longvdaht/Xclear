<?php
if (! defined('ABSPATH')) {
    exit;
}

class XclearLegalWidget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'xclear_legal_widget';
    }

    public function get_title()
    {
        return esc_html__('Xclear Legal', 'xclear');
    }

    public function get_icon()
    {
        return 'eicon-document-file';
    }

    public function get_categories()
    {
        return ['xclear-widgets'];
    }

    public function get_style_depends()
    {
        return ['xclear-legal-widget-css'];
    }

    protected function register_controls()
    {
        $this->registerContentSection();
        $this->registerStyleSection();
    }

    /* ─── Content ───────────────────────────────────────────────────────── */

    private function registerContentSection(): void
    {
        $this->start_controls_section('content_section', [
            'label' => esc_html__('Content', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $repeater = new \Elementor\Repeater();

        $repeater->add_control('item_type', [
            'label'   => esc_html__('Type', 'xclear'),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'paragraph',
            'options' => [
                'main_heading' => esc_html__('Main Heading (h5)', 'xclear'),
                'heading'      => esc_html__('Heading (h6)', 'xclear'),
                'paragraph'    => esc_html__('Paragraph', 'xclear'),
                'table'        => esc_html__('Table', 'xclear'),
            ],
        ]);

        // ── Text types ──────────────────────────────────────────────────
        $repeater->add_control('item_text', [
            'label'      => esc_html__('Text', 'xclear'),
            'type'       => \Elementor\Controls_Manager::WYSIWYG,
            'default'    => '',
            'show_label' => false,
            'condition'  => [
                'item_type!' => 'table',
            ],
        ]);

        // ── Table fields ─────────────────────────────────────────────────
        $repeater->add_control('table_title', [
            'label'       => esc_html__('Table Title', 'xclear'),
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => '',
            'placeholder' => esc_html__('e.g. Necessary (4)', 'xclear'),
            'label_block' => true,
            'condition'   => [
                'item_type' => 'table',
            ],
        ]);

        $repeater->add_control('table_description', [
            'label'     => esc_html__('Description', 'xclear'),
            'type'      => \Elementor\Controls_Manager::TEXTAREA,
            'default'   => '',
            'rows'      => 3,
            'condition' => [
                'item_type' => 'table',
            ],
        ]);

        $repeater->add_control('table_columns', [
            'label'       => esc_html__('Column Headers', 'xclear'),
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => 'Name|Provider|Purpose|Maximum Storage Duration|Type',
            'description' => esc_html__('Separate columns with | (pipe)', 'xclear'),
            'label_block' => true,
            'condition'   => [
                'item_type' => 'table',
            ],
        ]);

        $repeater->add_control('table_rows', [
            'label'       => esc_html__('Rows', 'xclear'),
            'type'        => \Elementor\Controls_Manager::TEXTAREA,
            'default'     => '',
            'rows'        => 8,
            'description' => esc_html__('Each line = 1 row. Separate cells with | (pipe). Example: CookieConsent|Cookiebot|Stores consent state|1 year|HTTP Cookie', 'xclear'),
            'condition'   => [
                'item_type' => 'table',
            ],
        ]);

        $this->add_control('legal_items', [
            'label'       => esc_html__('Content Blocks', 'xclear'),
            'type'        => \Elementor\Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'default'     => [
                [
                    'item_type' => 'main_heading',
                    'item_text' => 'Privacy policy VGE B.V.',
                ],
                [
                    'item_type' => 'paragraph',
                    'item_text' => 'VGE B.V., located at Nieuwe Eerdsebaan 26, is responsible for the processing of personal data as shown in this privacy statement.',
                ],
                [
                    'item_type' => 'heading',
                    'item_text' => 'Contact details',
                ],
                [
                    'item_type' => 'paragraph',
                    'item_text' => "Nieuwe Eerdsebaan 26\n5482 VS\nSchijndel\nThe Netherlands\n+31 (0) 88 222 1999",
                ],
                [
                    'item_type' => 'heading',
                    'item_text' => 'Personal data that we process',
                ],
                [
                    'item_type' => 'paragraph',
                    'item_text' => 'VGE B.V. processes your personal data because you use our services and / or because you provide them yourself.',
                ],
                [
                    'item_type' => 'heading',
                    'item_text' => 'Overview of personal data for processing',
                ],
                [
                    'item_type' => 'paragraph',
                    'item_text' => "- First and last name\n- E-mail address\n- IP address\n- Other personal data that you actively provide, for example by creating a profile on this website, in correspondence and by telephone\n- Location data\n- Information about your activities on our website\n- Information about your surfing behavior across different websites (for example because this company is part of an advertising network)\n- Internet browser and device type",
                ],
                [
                    'item_type' => 'heading',
                    'item_text' => 'Special and / or sensitive personal data that we process',
                ],
                [
                    'item_type' => 'paragraph',
                    'item_text' => 'Our website and / or service does not intend to collect data about website visitors under the age of 16. Unless they have permission from their parents or guardian. However, we cannot check whether a visitor is older than 16. We therefore advise parents to be involved in the online activities of their children, in order to prevent data about children from being collected without parental consent. If you are convinced that we have collected personal information about a minor without this permission, please contact us via info@vgebv.nl and we will delete this information.',
                ],
                [
                    'item_type' => 'heading',
                    'item_text' => 'For what purpose and on what basis we process personal data',
                ],
                [
                    'item_type' => 'paragraph',
                    'item_text' => "VGE B.V. processes your personal data for the following purposes:\n- Sending our newsletter and / or advertising brochure\n- To be able to call or e-mail you if necessary to carry out our services\n- To inform you about changes to our services and products\n- VGE B.V. analyzes your behavior on the website in order to improve the website and to tailor the range of products and services to your preferences.",
                ],
            ],
            'title_field' => '{{{ item_type === "main_heading" ? "H5: " + item_text.replace(/(<([^>]+)>)/gi,"").substring(0,40) : item_type === "heading" ? "H6: " + item_text.replace(/(<([^>]+)>)/gi,"").substring(0,40) : item_type === "table" ? "Table: " + (table_title || "(no title)") : "¶ " + item_text.replace(/(<([^>]+)>)/gi,"").substring(0,40) }}}',
        ]);

        $this->end_controls_section();
    }

    /* ─── Style ─────────────────────────────────────────────────────────── */

    private function registerStyleSection(): void
    {
        $this->start_controls_section('style_main_heading_section', [
            'label' => esc_html__('Main Heading (h5)', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'main_heading_typography',
            'selector' => '{{WRAPPER}} .xclear-legal__main-heading',
        ]);

        $this->add_control('main_heading_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-legal__main-heading' => 'color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();

        $this->start_controls_section('style_heading_section', [
            'label' => esc_html__('Heading (h6)', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'heading_typography',
            'selector' => '{{WRAPPER}} .xclear-legal__heading',
        ]);

        $this->add_control('heading_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-legal__heading' => 'color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();

        $this->start_controls_section('style_paragraph_section', [
            'label' => esc_html__('Paragraph', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'paragraph_typography',
            'selector' => '{{WRAPPER}} .xclear-legal__paragraph',
        ]);

        $this->add_control('paragraph_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-legal__paragraph' => 'color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();

        $this->start_controls_section('style_table_section', [
            'label' => esc_html__('Table', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('table_border_color', [
            'label'     => esc_html__('Border Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-legal__table-section' => 'border-color: {{VALUE}};',
                '{{WRAPPER}} .xclear-legal__table td'      => 'border-color: {{VALUE}};',
                '{{WRAPPER}} .xclear-legal__table th'      => 'border-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('table_header_color', [
            'label'     => esc_html__('Header Text Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-legal__table th' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('table_cell_color', [
            'label'     => esc_html__('Cell Text Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-legal__table td' => 'color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();
    }

    /* ─── Render ────────────────────────────────────────────────────────── */

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $items    = $settings['legal_items'] ?? [];

        if (empty($items)) {
            return;
        }
        ?>
        <div class="xclear-legal">
            <div class="xclear-legal__content">
                <?php foreach ($items as $item) :
                    $type = $item['item_type'] ?? 'paragraph';

                    switch ($type) {
                        case 'main_heading':
                            $text = $item['item_text'] ?? '';
                            if (empty(trim(strip_tags($text)))) break;
                            ?>
                            <h5 class="xclear-legal__main-heading"><?php echo wp_kses_post($text); ?></h5>
                            <?php
                            break;

                        case 'heading':
                            $text = $item['item_text'] ?? '';
                            if (empty(trim(strip_tags($text)))) break;
                            ?>
                            <h6 class="xclear-legal__heading"><?php echo wp_kses_post($text); ?></h6>
                            <?php
                            break;

                        case 'paragraph':
                            $text = $item['item_text'] ?? '';
                            if (empty(trim(strip_tags($text)))) break;
                            ?>
                            <div class="xclear-legal__paragraph"><?php echo wp_kses_post(wpautop($text)); ?></div>
                            <?php
                            break;

                        case 'table':
                            $this->renderTable($item);
                            break;
                    }
                endforeach; ?>
            </div>
        </div>
        <?php
    }

    private function renderTable(array $item): void
    {
        $title       = $item['table_title'] ?? '';
        $description = $item['table_description'] ?? '';
        $columns_raw = $item['table_columns'] ?? '';
        $rows_raw    = $item['table_rows'] ?? '';

        $columns = array_filter(array_map('trim', explode('|', $columns_raw)));
        $lines   = array_filter(array_map('trim', explode("\n", $rows_raw)));

        if (empty($columns) && empty($lines)) {
            return;
        }
        ?>
        <div class="xclear-legal__table-section">
            <?php if (! empty($title)) : ?>
                <p class="xclear-legal__table-title"><?php echo esc_html($title); ?></p>
            <?php endif; ?>

            <?php if (! empty($description)) : ?>
                <p class="xclear-legal__table-desc"><?php echo esc_html($description); ?></p>
            <?php endif; ?>

            <div class="xclear-legal__table-wrap">
                <table class="xclear-legal__table">
                    <?php if (! empty($columns)) : ?>
                        <thead>
                            <tr>
                                <?php foreach ($columns as $col) : ?>
                                    <th><?php echo esc_html($col); ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                    <?php endif; ?>
                    <tbody>
                        <?php foreach ($lines as $line) :
                            $cells = array_map('trim', explode('|', $line));
                        ?>
                            <tr>
                                <?php foreach ($cells as $cell) : ?>
                                    <td><?php echo esc_html($cell); ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }
}
