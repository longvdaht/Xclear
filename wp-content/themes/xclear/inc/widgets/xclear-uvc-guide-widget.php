<?php
if (! defined('ABSPATH')) {
    exit;
}

class XclearUvcGuideWidget extends \Elementor\Widget_Base
{
    private const COLOR_VALUE = 'color: {{VALUE}};';

    public function get_name()
    {
        return 'xclear_uvc_guide_widget';
    }

    public function get_title()
    {
        return esc_html__('Xclear UV-C Guide', 'xclear');
    }

    public function get_icon()
    {
        return 'eicon-table';
    }

    public function get_categories()
    {
        return ['xclear-widgets'];
    }

    public function get_style_depends()
    {
        return ['xclear-uvc-guide-widget-css'];
    }

    protected function register_controls()
    {
        $this->registerHeaderSection();
        $this->registerTableSection();
        $this->registerNoteSection();
        $this->registerStyleHeaderSection();
        $this->registerStyleTableSection();
        $this->registerStyleNoteSection();
    }

    // ── Content: Header ───────────────────────────────────────────────────────

    private function registerHeaderSection(): void
    {
        $this->start_controls_section('section_header', [
            'label' => esc_html__('Header', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('badge', [
            'label'   => esc_html__('Badge Text', 'xclear'),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => esc_html__('Choosing UV-C', 'xclear'),
        ]);

        $this->add_control('heading', [
            'label'       => esc_html__('Heading', 'xclear'),
            'type'        => \Elementor\Controls_Manager::TEXTAREA,
            'rows'        => 4,
            'default'     => 'Choosing [hl]the right UV-C[/hl] for your swimming pond',
            'description' => esc_html__('Wrap highlighted words with [hl]...[/hl].', 'xclear'),
        ]);

        $this->add_control('desc_intro', [
            'label'     => esc_html__('Description Intro', 'xclear'),
            'type'      => \Elementor\Controls_Manager::TEXTAREA,
            'rows'      => 3,
            'default'   => esc_html__('To ensure optimal results, your UV-C unit must match your pond\'s specific needs:', 'xclear'),
            'separator' => 'before',
        ]);

        $repeater = new \Elementor\Repeater();

        $repeater->add_control('item_label', [
            'label'   => esc_html__('Label', 'xclear'),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => esc_html__('Volume (m³):', 'xclear'),
        ]);

        $repeater->add_control('item_text', [
            'label'   => esc_html__('Text', 'xclear'),
            'type'    => \Elementor\Controls_Manager::TEXTAREA,
            'rows'    => 3,
            'default' => esc_html__('Larger ponds require higher wattage. For swimming ponds, we recommend at least 3 watts per 1,000 liters.', 'xclear'),
        ]);

        $this->add_control('desc_items', [
            'label'       => esc_html__('Description Items', 'xclear'),
            'type'        => \Elementor\Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'default'     => [
                [
                    'item_label' => esc_html__('Volume (m³):', 'xclear'),
                    'item_text'  => esc_html__('Larger ponds require higher wattage. For swimming ponds, we recommend at least 3 watts per 1,000 liters.', 'xclear'),
                ],
                [
                    'item_label' => esc_html__('Sunlight:', 'xclear'),
                    'item_text'  => esc_html__('Ponds in full sun need more power to combat rapid algae growth.', 'xclear'),
                ],
                [
                    'item_label' => esc_html__('Usage:', 'xclear'),
                    'item_text'  => esc_html__('High swimmer frequency increases the demand for disinfection.', 'xclear'),
                ],
            ],
            'title_field' => '{{{ item_label }}}',
        ]);

        $this->end_controls_section();
    }

    // ── Content: Table ────────────────────────────────────────────────────────

    private function registerTableSection(): void
    {
        $this->start_controls_section('section_table', [
            'label' => esc_html__('Table', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('show_table', [
            'label'   => esc_html__('Show Table', 'xclear'),
            'type'    => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);

        $this->add_control('col1_header', [
            'label'   => esc_html__('Column 1 Header', 'xclear'),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => esc_html__('Pond volume', 'xclear'),
        ]);

        $this->add_control('col2_header', [
            'label'   => esc_html__('Column 2 Header', 'xclear'),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => esc_html__('Recommended Xclear unit', 'xclear'),
        ]);

        $this->add_control('col3_header', [
            'label'   => esc_html__('Column 3 Header', 'xclear'),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => esc_html__('Max. Flow Capacity', 'xclear'),
        ]);

        $repeater = new \Elementor\Repeater();

        $repeater->add_control('row_col1', [
            'label'   => esc_html__('Column 1', 'xclear'),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => esc_html__('Up to 25 m³', 'xclear'),
        ]);

        $repeater->add_control('row_col2', [
            'label'   => esc_html__('Column 2', 'xclear'),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => esc_html__('Jumbo Tech 75W', 'xclear'),
        ]);

        $repeater->add_control('row_col3', [
            'label'   => esc_html__('Column 3', 'xclear'),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => esc_html__('16 m³/h', 'xclear'),
        ]);

        $this->add_control('table_rows', [
            'label'       => esc_html__('Table Rows', 'xclear'),
            'type'        => \Elementor\Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'default'     => [
                [
                    'row_col1' => esc_html__('Up to 25 m³', 'xclear'),
                    'row_col2' => esc_html__('Jumbo Tech 75W', 'xclear'),
                    'row_col3' => esc_html__('16 m³/h', 'xclear'),
                ],
                [
                    'row_col1' => esc_html__('Up to 50 m³', 'xclear'),
                    'row_col2' => esc_html__('Xpert Inox 130W Amalgam', 'xclear'),
                    'row_col3' => esc_html__('22 m³/h', 'xclear'),
                ],
                [
                    'row_col1' => esc_html__('Up to 100+ m³', 'xclear'),
                    'row_col2' => esc_html__('Xpert Buster 420W', 'xclear'),
                    'row_col3' => esc_html__('49 m³/h', 'xclear'),
                ],
            ],
            'title_field' => '{{{ row_col1 }}}',
        ]);

        $this->end_controls_section();
    }

    // ── Content: Note ─────────────────────────────────────────────────────────

    private function registerNoteSection(): void
    {
        $this->start_controls_section('section_note', [
            'label' => esc_html__('Note', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('show_note', [
            'label'   => esc_html__('Show Note', 'xclear'),
            'type'    => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);

        $this->add_control('note_text', [
            'label'   => esc_html__('Note Text', 'xclear'),
            'type'    => \Elementor\Controls_Manager::WYSIWYG,
            'default' => esc_html__('Please note that these figures are indications based on technical data; we always advise choosing extra wattage and a larger system capacity to ensure optimal performance in all conditions.', 'xclear'),
        ]);

        $this->end_controls_section();
    }

    // ── Style: Header ─────────────────────────────────────────────────────────

    private function registerStyleHeaderSection(): void
    {
        $this->start_controls_section('style_header', [
            'label' => esc_html__('Header', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('style_badge_heading', [
            'label' => esc_html__('Badge', 'xclear'),
            'type'  => \Elementor\Controls_Manager::HEADING,
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'badge_typography',
            'selector' => '{{WRAPPER}} .xclear-uvc-guide__badge',
        ]);

        $this->add_control('badge_bg_color', [
            'label'     => esc_html__('Background', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-uvc-guide__badge' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('badge_text_color', [
            'label'     => esc_html__('Text Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-uvc-guide__badge'     => self::COLOR_VALUE,
                '{{WRAPPER}} .xclear-uvc-guide__badge-dot' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('style_heading_heading', [
            'label'     => esc_html__('Heading', 'xclear'),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'heading_typography',
            'selector' => '{{WRAPPER}} .xclear-uvc-guide__heading',
        ]);

        $this->add_control('heading_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-uvc-guide__heading' => self::COLOR_VALUE,
            ],
        ]);

        $this->add_control('hl_color', [
            'label'     => esc_html__('Highlight Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'separator' => 'before',
            'selectors' => [
                '{{WRAPPER}} .xclear-uvc-guide__heading .xclear-hl' => self::COLOR_VALUE,
            ],
        ]);

        $this->add_control('style_desc_intro_heading', [
            'label'     => esc_html__('Description Intro', 'xclear'),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'desc_intro_typography',
            'selector' => '{{WRAPPER}} .xclear-uvc-guide__desc-intro',
        ]);

        $this->add_control('desc_intro_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-uvc-guide__desc-intro' => self::COLOR_VALUE,
            ],
        ]);

        $this->add_control('style_item_label_heading', [
            'label'     => esc_html__('Item — Label', 'xclear'),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'item_label_typography',
            'selector' => '{{WRAPPER}} .xclear-uvc-guide__item-label',
        ]);

        $this->add_control('item_label_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-uvc-guide__item-label' => self::COLOR_VALUE,
            ],
        ]);

        $this->add_control('style_item_text_heading', [
            'label'     => esc_html__('Item — Text', 'xclear'),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'item_text_typography',
            'selector' => '{{WRAPPER}} .xclear-uvc-guide__item-text',
        ]);

        $this->add_control('item_text_color', [
            'label'     => esc_html__('Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-uvc-guide__item-text' => self::COLOR_VALUE,
            ],
        ]);

        $this->end_controls_section();
    }

    // ── Style: Table ──────────────────────────────────────────────────────────

    private function registerStyleTableSection(): void
    {
        $this->start_controls_section('style_table', [
            'label' => esc_html__('Table', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('style_table_header_heading', [
            'label' => esc_html__('Header Row', 'xclear'),
            'type'  => \Elementor\Controls_Manager::HEADING,
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'table_header_typography',
            'selector' => '{{WRAPPER}} .xclear-uvc-guide__table-head th',
        ]);

        $this->add_control('table_header_color', [
            'label'     => esc_html__('Text Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-uvc-guide__table-head th' => self::COLOR_VALUE,
            ],
        ]);

        $this->add_control('table_header_bg', [
            'label'     => esc_html__('Background', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-uvc-guide__table-head' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('table_divider_color', [
            'label'     => esc_html__('Divider Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'separator' => 'before',
            'selectors' => [
                '{{WRAPPER}} .xclear-uvc-guide__table-head th' => 'border-bottom-color: {{VALUE}};',
                '{{WRAPPER}} .xclear-uvc-guide__table td'      => 'border-bottom-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('style_table_row_heading', [
            'label'     => esc_html__('Data Rows', 'xclear'),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'table_cell_typography',
            'selector' => '{{WRAPPER}} .xclear-uvc-guide__table td',
        ]);

        $this->add_control('table_cell_color', [
            'label'     => esc_html__('Text Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-uvc-guide__table td' => self::COLOR_VALUE,
            ],
        ]);

        $this->add_control('table_row_bg', [
            'label'     => esc_html__('Row Background', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-uvc-guide__table tbody tr' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('table_row_stripe_bg', [
            'label'     => esc_html__('Stripe Background', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-uvc-guide__table tbody tr:nth-child(odd)' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();
    }

    // ── Style: Note ───────────────────────────────────────────────────────────

    private function registerStyleNoteSection(): void
    {
        $this->start_controls_section('style_note', [
            'label' => esc_html__('Note', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'note_typography',
            'selector' => '{{WRAPPER}} .xclear-uvc-guide__note',
        ]);

        $this->add_control('note_color', [
            'label'     => esc_html__('Text Color', 'xclear'),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .xclear-uvc-guide__note' => self::COLOR_VALUE,
            ],
        ]);

        $this->end_controls_section();
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private static function parseHighlight(string $text): string
    {
        return preg_replace(
            '/\[hl\](.*?)\[\/hl\]/s',
            '<mark class="xclear-hl">$1</mark>',
            esc_html($text)
        );
    }

    // ── Render ────────────────────────────────────────────────────────────────

    protected function render(): void
    {
        $settings   = $this->get_settings_for_display();
        $badge      = esc_html($settings['badge']      ?? '');
        $heading    = self::parseHighlight($settings['heading']    ?? '');
        $descIntro  = esc_html($settings['desc_intro'] ?? '');
        $descItems  = $settings['desc_items']  ?? [];
        $showTable  = ($settings['show_table'] ?? 'yes') === 'yes';
        $col1Header = esc_html($settings['col1_header'] ?? '');
        $col2Header = esc_html($settings['col2_header'] ?? '');
        $col3Header = esc_html($settings['col3_header'] ?? '');
        $tableRows  = $settings['table_rows']  ?? [];
        $showNote   = ($settings['show_note'] ?? 'yes') === 'yes';
        $noteText   = wp_kses_post($settings['note_text']  ?? '');
?>
        <div class="xclear-uvc-guide">

            <div class="xclear-uvc-guide__header">
                <?php if ($badge) : ?>
                    <div class="xclear-uvc-guide__badge">
                        <span class="xclear-uvc-guide__badge-dot"></span>
                        <?php echo $badge; ?>
                    </div>
                <?php endif; ?>
                <?php if ($heading) : ?>
                    <h2 class="xclear-uvc-guide__heading"><?php echo $heading; ?></h2>
                <?php endif; ?>

                <div class="xclear-uvc-guide__header-right">
                    <?php if ($descIntro) : ?>
                        <p class="xclear-uvc-guide__desc-intro"><?php echo $descIntro; ?></p>
                    <?php endif; ?>
                    <?php if ($descItems) : ?>
                        <ul class="xclear-uvc-guide__desc-list">
                            <?php foreach ($descItems as $item) : ?>
                                <li class="xclear-uvc-guide__desc-item">
                                    <?php if (! empty($item['item_label'])) : ?>
                                        <span class="xclear-uvc-guide__item-label"><?php echo esc_html($item['item_label']); ?></span>
                                    <?php endif; ?>
                                    <?php if (! empty($item['item_text'])) : ?>
                                        <span class="xclear-uvc-guide__item-text"><?php echo esc_html($item['item_text']); ?></span>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($showTable && $tableRows) : ?>
                <div class="xclear-uvc-guide__table-wrap">
                    <table class="xclear-uvc-guide__table">
                        <thead class="xclear-uvc-guide__table-head">
                            <tr>
                                <th><?php echo $col1Header; ?></th>
                                <th><?php echo $col2Header; ?></th>
                                <th><?php echo $col3Header; ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tableRows as $row) : ?>
                                <tr>
                                    <td><?php echo esc_html($row['row_col1'] ?? ''); ?></td>
                                    <td><?php echo esc_html($row['row_col2'] ?? ''); ?></td>
                                    <td><?php echo esc_html($row['row_col3'] ?? ''); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <?php if ($showNote && $noteText) : ?>
                <div class="xclear-uvc-guide__note"><?php echo $noteText; ?></div>
            <?php endif; ?>

        </div>
<?php
    }
}
