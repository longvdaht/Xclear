<?php
if (! defined('ABSPATH')) {
    exit;
}

class XclearDocumentDownloadWidget extends \Elementor\Widget_Base
{
    private const COLOR_VALUE = 'color: {{VALUE}};';

    public function get_name()
    {
        return 'xclear_document_download_widget';
    }

    public function get_title()
    {
        return esc_html__('Xclear Document Download', 'xclear');
    }

    public function get_icon()
    {
        return 'eicon-download-button';
    }

    public function get_categories()
    {
        return ['xclear-widgets'];
    }

    public function get_style_depends()
    {
        return ['xclear-document-download-widget-css'];
    }

    private function registerDocumentsSection(): void
    {
        $this->start_controls_section(
            'documents_section',
            [
                'label' => esc_html__('Documents', 'xclear'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'doc_name',
            [
                'label' => esc_html__('File Name', 'xclear'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('File Name', 'xclear'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'doc_file',
            [
                'label' => esc_html__('File', 'xclear'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'media_type' => 'application',
                'description' => esc_html__('Upload file — URL and file size are detected automatically.', 'xclear'),
            ]
        );

        $this->add_control(
            'documents',
            [
                'label' => esc_html__('Document List', 'xclear'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'doc_name' => esc_html__('File Name', 'xclear'),
                    ],
                ],
                'title_field' => '{{{ doc_name }}}',
            ]
        );

        $this->end_controls_section();
    }

    private function registerButtonSection(): void
    {
        $this->start_controls_section(
            'button_section',
            [
                'label' => esc_html__('Button', 'xclear'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label' => esc_html__('Button Label', 'xclear'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('Download', 'xclear'),
            ]
        );

        $this->end_controls_section();
    }

    private function registerIconsSection(): void
    {
        $this->start_controls_section(
            'icons_section',
            [
                'label' => esc_html__('Icons', 'xclear'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'file_icon',
            [
                'label' => esc_html__('File Icon', 'xclear'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'description' => esc_html__('Upload SVG icon for the document. Leave empty to use default.', 'xclear'),
            ]
        );

        $this->add_control(
            'button_icon',
            [
                'label' => esc_html__('Button Icon', 'xclear'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'description' => esc_html__('Upload SVG icon for the download button. Leave empty to use default.', 'xclear'),
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();
    }

    private function registerStyleCardSection(): void
    {
        $this->start_controls_section(
            'style_card_section',
            [
                'label' => esc_html__('Card', 'xclear'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'card_bg_color',
            [
                'label' => esc_html__('Background Color', 'xclear'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .xclear-doc-download__item' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_bg_color',
            [
                'label' => esc_html__('Icon Background Color', 'xclear'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .xclear-doc-download__icon' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'card_gap',
            [
                'label' => esc_html__('Gap Between Cards', 'xclear'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => ['min' => 0, 'max' => 40],
                ],
                'selectors' => [
                    '{{WRAPPER}} .xclear-doc-download' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    private function registerStyleTextSection(): void
    {
        $this->start_controls_section(
            'style_text_section',
            [
                'label' => esc_html__('Text', 'xclear'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'style_name_heading',
            [
                'label' => esc_html__('File Name', 'xclear'),
                'type' => \Elementor\Controls_Manager::HEADING,
            ]
        );

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'name_typography',
            'selector' => '{{WRAPPER}} .xclear-doc-download__name',
        ]);

        $this->add_control(
            'name_color',
            [
                'label' => esc_html__('Color', 'xclear'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .xclear-doc-download__name' => self::COLOR_VALUE,
                ],
            ]
        );

        $this->add_control(
            'style_size_heading',
            [
                'label' => esc_html__('File Size', 'xclear'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'size_typography',
            'selector' => '{{WRAPPER}} .xclear-doc-download__size',
        ]);

        $this->add_control(
            'size_color',
            [
                'label' => esc_html__('Color', 'xclear'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .xclear-doc-download__size' => self::COLOR_VALUE,
                ],
            ]
        );

        $this->end_controls_section();
    }

    private function registerStyleButtonSection(): void
    {
        $this->start_controls_section(
            'style_button_section',
            [
                'label' => esc_html__('Button', 'xclear'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'btn_bg_color',
            [
                'label' => esc_html__('Background Color', 'xclear'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .xclear-doc-download__btn' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'btn_typography',
            'selector' => '{{WRAPPER}} .xclear-doc-download__btn-text',
        ]);

        $this->add_control(
            'btn_text_color',
            [
                'label' => esc_html__('Text Color', 'xclear'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .xclear-doc-download__btn' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function register_controls()
    {
        $this->registerDocumentsSection();
        $this->registerButtonSection();
        $this->registerIconsSection();
        $this->registerStyleCardSection();
        $this->registerStyleTextSection();
        $this->registerStyleButtonSection();
    }

    private static function renderFileIcon(): void
    {
?>
        <svg width="17" height="20" viewBox="0 0 17 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path
                d="M16.2806 5.46938L11.0306 0.219375C10.9609 0.149749 10.8782 0.094539 10.7871 0.0568979C10.6961 0.0192569 10.5985 -7.72394e-05 10.5 2.31899e-07H1.5C1.10218 2.31899e-07 0.720644 0.158035 0.43934 0.43934C0.158035 0.720645 0 1.10218 0 1.5V18C0 18.3978 0.158035 18.7794 0.43934 19.0607C0.720644 19.342 1.10218 19.5 1.5 19.5H15C15.3978 19.5 15.7794 19.342 16.0607 19.0607C16.342 18.7794 16.5 18.3978 16.5 18V6C16.5001 5.90148 16.4807 5.80391 16.4431 5.71286C16.4055 5.62182 16.3503 5.53908 16.2806 5.46938ZM11.25 14.25H5.25C5.05109 14.25 4.86032 14.171 4.71967 14.0303C4.57902 13.8897 4.5 13.6989 4.5 13.5C4.5 13.3011 4.57902 13.1103 4.71967 12.9697C4.86032 12.829 5.05109 12.75 5.25 12.75H11.25C11.4489 12.75 11.6397 12.829 11.7803 12.9697C11.921 13.1103 12 13.3011 12 13.5C12 13.6989 11.921 13.8897 11.7803 14.0303C11.6397 14.171 11.4489 14.25 11.25 14.25ZM11.25 11.25H5.25C5.05109 11.25 4.86032 11.171 4.71967 11.0303C4.57902 10.8897 4.5 10.6989 4.5 10.5C4.5 10.3011 4.57902 10.1103 4.71967 9.96967C4.86032 9.82902 5.05109 9.75 5.25 9.75H11.25C11.4489 9.75 11.6397 9.82902 11.7803 9.96967C11.921 10.1103 12 10.3011 12 10.5C12 10.6989 11.921 10.8897 11.7803 11.0303C11.6397 11.171 11.4489 11.25 11.25 11.25ZM10.5 6V1.875L14.625 6H10.5Z"
                fill="currentColor"
            />
        </svg>
    <?php
    }

    private static function renderDownloadIcon(): void
    {
    ?>
        <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path
                d="M4.55781 5.44219C4.44054 5.32491 4.37465 5.16585 4.37465 5C4.37465 4.83415 4.44054 4.67509 4.55781 4.55781C4.67509 4.44054 4.83415 4.37465 5 4.37465C5.16585 4.37465 5.32491 4.44054 5.44219 4.55781L8.125 7.24141V0.625C8.125 0.45924 8.19085 0.300268 8.30806 0.183058C8.42527 0.065848 8.58424 0 8.75 0C8.91576 0 9.07473 0.065848 9.19194 0.183058C9.30915 0.300268 9.375 0.45924 9.375 0.625V7.24141L12.0578 4.55781C12.1751 4.44054 12.3341 4.37465 12.5 4.37465C12.6659 4.37465 12.8249 4.44054 12.9422 4.55781C13.0595 4.67509 13.1253 4.83415 13.1253 5C13.1253 5.16585 13.0595 5.32491 12.9422 5.44219L9.19219 9.19219C9.13414 9.2503 9.06521 9.2964 8.98934 9.32785C8.91346 9.3593 8.83213 9.37549 8.75 9.37549C8.66787 9.37549 8.58654 9.3593 8.51066 9.32785C8.43479 9.2964 8.36586 9.2503 8.30781 9.19219L4.55781 5.44219ZM17.5 9.375V14.375C17.5 14.7065 17.3683 15.0245 17.1339 15.2589C16.8995 15.4933 16.5815 15.625 16.25 15.625H1.25C0.918479 15.625 0.600537 15.4933 0.366116 15.2589C0.131696 15.0245 0 14.7065 0 14.375V9.375C0 9.04348 0.131696 8.72554 0.366116 8.49112C0.600537 8.2567 0.918479 8.125 1.25 8.125H5.34375C5.3848 8.12497 5.42545 8.13302 5.46339 8.14871C5.50133 8.16439 5.5358 8.1874 5.56484 8.21641L7.42188 10.0781C7.59607 10.2529 7.80306 10.3916 8.03097 10.4863C8.25887 10.5809 8.50322 10.6296 8.75 10.6296C8.99678 10.6296 9.24113 10.5809 9.46903 10.4863C9.69694 10.3916 9.90393 10.2529 10.0781 10.0781L11.9375 8.21875C11.9952 8.15987 12.0738 8.12617 12.1563 8.125H16.25C16.5815 8.125 16.8995 8.2567 17.1339 8.49112C17.3683 8.72554 17.5 9.04348 17.5 9.375ZM14.375 11.875C14.375 11.6896 14.32 11.5083 14.217 11.3542C14.114 11.2 13.9676 11.0798 13.7963 11.0089C13.625 10.9379 13.4365 10.9193 13.2546 10.9555C13.0727 10.9917 12.9057 11.081 12.7746 11.2121C12.6435 11.3432 12.5542 11.5102 12.518 11.6921C12.4818 11.874 12.5004 12.0625 12.5714 12.2338C12.6423 12.4051 12.7625 12.5515 12.9167 12.6545C13.0708 12.7575 13.2521 12.8125 13.4375 12.8125C13.6861 12.8125 13.9246 12.7137 14.1004 12.5379C14.2762 12.3621 14.375 12.1236 14.375 11.875Z"
                fill="currentColor"
            />
        </svg>
    <?php
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $button_text = $settings['button_text'] ?? esc_html__('Download', 'xclear');
        $file_icon_url = $settings['file_icon']['url'] ?? '';
        $button_icon_url = $settings['button_icon']['url'] ?? '';

        if (empty($settings['documents'])) {
            return;
        }
    ?>
        <div class="xclear-doc-download">
            <?php foreach ($settings['documents'] as $item) : ?>
                <?php
                $attachment_id = ! empty($item['doc_file']['id']) ? absint($item['doc_file']['id']) : 0;
                $url = ! empty($item['doc_file']['url']) ? $item['doc_file']['url'] : '#';
                $file_size = '';
                if ($attachment_id) {
                    $file_path = get_attached_file($attachment_id);
                    if ($file_path && file_exists($file_path)) {
                        $file_size = size_format(filesize($file_path));
                    }
                }
                ?>
                <div class="xclear-doc-download__item">
                    <div class="xclear-doc-download__file">
                        <div class="xclear-doc-download__icon">
                            <?php if (! empty($file_icon_url)) : ?>
                                <img src="<?php echo esc_url($file_icon_url); ?>" alt="" class="xclear-doc-download__icon-img">
                            <?php else : ?>
                                <?php self::renderFileIcon(); ?>
                            <?php endif; ?>
                        </div>
                        <div class="xclear-doc-download__meta">
                            <?php if (! empty($item['doc_name'])) : ?>
                                <p class="xclear-doc-download__name"><?php echo esc_html($item['doc_name']); ?></p>
                            <?php endif; ?>
                            <?php if (! empty($file_size)) : ?>
                                <p class="xclear-doc-download__size"><?php echo esc_html($file_size); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <a
                        href="<?php echo esc_url($url); ?>"
                        class="xclear-doc-download__btn"
                        download>
                        <?php if (! empty($button_icon_url)) : ?>
                            <img src="<?php echo esc_url($button_icon_url); ?>" alt="" class="xclear-doc-download__btn-icon-img">
                        <?php else : ?>
                            <?php self::renderDownloadIcon(); ?>
                        <?php endif; ?>
                        <span class="xclear-doc-download__btn-text"><?php echo esc_html($button_text); ?></span>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
<?php
    }
}
