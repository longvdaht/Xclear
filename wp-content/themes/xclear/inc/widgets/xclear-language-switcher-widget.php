<?php
if (! defined('ABSPATH')) {
    exit;
}

class XclearLanguageSwitcherWidget extends \Elementor\Widget_Base
{
    public function get_name(): string
    {
        return 'xclear_language_switcher_widget';
    }

    public function get_title(): string
    {
        return esc_html__('Xclear Language Switcher', 'xclear');
    }

    public function get_icon(): string
    {
        return 'eicon-globe';
    }

    public function get_categories(): array
    {
        return ['xclear-widgets'];
    }

    public function get_style_depends(): array
    {
        return ['xclear-language-switcher-widget-css'];
    }

    public function get_script_depends(): array
    {
        return ['xclear-language-switcher-widget-js'];
    }

    protected function register_controls(): void
    {
        $this->start_controls_section('section_settings', [
            'label' => esc_html__('Settings', 'xclear'),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('layout', [
            'label'   => esc_html__('Layout', 'xclear'),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'pill',
            'options' => [
                'pill'     => esc_html__('Pill with flags (current → dropdown)', 'xclear'),
                'list'     => esc_html__('Horizontal list', 'xclear'),
                'dropdown' => esc_html__('Native dropdown (select)', 'xclear'),
            ],
        ]);

        $this->add_control('display_format', [
            'label'   => esc_html__('Display Format', 'xclear'),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'name',
            'options' => [
                'name' => esc_html__('Language name (English)', 'xclear'),
                'slug' => esc_html__('Language code (EN, NL...)', 'xclear'),
            ],
        ]);

        $this->add_control('show_current', [
            'label'        => esc_html__('Show Current Language in List', 'xclear'),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'label_on'     => esc_html__('Yes', 'xclear'),
            'label_off'    => esc_html__('No', 'xclear'),
            'return_value' => 'yes',
            'default'      => 'yes',
        ]);

        $this->end_controls_section();
    }

    protected function render(): void
    {
        if (! function_exists('pll_the_languages')) {
            return;
        }

        $settings   = $this->get_settings_for_display();
        $layout     = $settings['layout'] ?? 'pill';
        $format     = $settings['display_format'] ?? 'name';
        $showCurrent = ($settings['show_current'] ?? 'yes') === 'yes';

        $languages = pll_the_languages([
            'raw'         => 1,
            'hide_if_empty' => 0,
        ]);

        if (empty($languages) || ! is_array($languages)) {
            return;
        }

        if ($layout !== 'pill' && ! $showCurrent) {
            $languages = array_filter($languages, fn($lang) => empty($lang['current_lang']));
        }

        switch ($layout) {
            case 'dropdown':
                $this->renderDropdown($languages, $format);
                break;
            case 'list':
                $this->renderList($languages, $format);
                break;
            default:
                $this->renderPill($languages, $format);
        }
    }

    private function flagUrl(string $slug): string
    {
        $file = 'flag_' . strtoupper($slug) . '.png';
        $path = get_template_directory() . '/assets/images/' . $file;
        return file_exists($path) ? get_template_directory_uri() . '/assets/images/' . $file : '';
    }

    private function renderFlag(string $slug): void
    {
        $url = $this->flagUrl($slug);
        if (! $url) {
            return;
        }
    ?>
        <img class="xclear-language-switcher__flag" src="<?php echo esc_url($url); ?>" alt="" aria-hidden="true">
    <?php
    }

    private function renderPill(array $languages, string $format): void
    {
        $current = null;
        foreach ($languages as $lang) {
            if (! empty($lang['current_lang'])) {
                $current = $lang;
                break;
            }
        }
        if (! $current) {
            $current = reset($languages);
        }
        if (! $current) {
            return;
        }

        $caretUrl = get_template_directory_uri() . '/assets/images/CaretDown.svg';
    ?>
        <div class="xclear-language-switcher xclear-language-switcher--pill">
            <button class="xclear-language-switcher__trigger" type="button" aria-haspopup="listbox" aria-expanded="false">
                <?php $this->renderFlag($current['slug']); ?>
                <span class="xclear-language-switcher__code"><?php echo esc_html(strtoupper($current['slug'])); ?></span>
                <img class="xclear-language-switcher__caret" src="<?php echo esc_url($caretUrl); ?>" alt="" aria-hidden="true">
            </button>
            <ul class="xclear-language-switcher__dropdown" role="listbox">
                <?php foreach ($languages as $lang) : ?>
                    <li class="xclear-language-switcher__option<?php echo ! empty($lang['current_lang']) ? ' is-current' : ''; ?>" role="option">
                        <a href="<?php echo esc_url($lang['url']); ?>" hreflang="<?php echo esc_attr($lang['locale']); ?>">
                            <?php $this->renderFlag($lang['slug']); ?>
                            <span><?php echo esc_html($this->getLabel($lang, $format)); ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php
    }

    /**
     * Render the language switcher standalone — usable from other widgets (e.g. mobile drawer).
     */
    public static function renderStandalone(string $layout = 'pill', string $format = 'slug'): void
    {
        $instance = new self();
        $instance->renderByLayout($layout, $format);
    }

    private function renderByLayout(string $layout, string $format): void
    {
        if (! function_exists('pll_the_languages')) {
            return;
        }

        $languages = pll_the_languages(['raw' => 1, 'hide_if_empty' => 0]);

        if (empty($languages) || ! is_array($languages)) {
            return;
        }

        switch ($layout) {
            case 'dropdown':
                $this->renderDropdown($languages, $format);
                break;
            case 'list':
                $this->renderList($languages, $format);
                break;
            default:
                $this->renderPill($languages, $format);
        }
    }

    private function getLabel(array $lang, string $format): string
    {
        if ($format === 'slug') {
            return strtoupper($lang['slug'] ?? '');
        }
        return $lang['name'] ?? strtoupper($lang['slug'] ?? '');
    }

    private function renderList(array $languages, string $format): void
    {
    ?>
        <ul class="xclear-language-switcher xclear-language-switcher--list">
            <?php foreach ($languages as $lang) : ?>
                <li class="xclear-language-switcher__item<?php echo ! empty($lang['current_lang']) ? ' is-current' : ''; ?>">
                    <a href="<?php echo esc_url($lang['url']); ?>"
                        class="xclear-language-switcher__link"
                        hreflang="<?php echo esc_attr($lang['locale']); ?>">
                        <?php echo esc_html($this->getLabel($lang, $format)); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php
    }

    private function renderDropdown(array $languages, string $format): void
    {
        $current = '';
        foreach ($languages as $lang) {
            if (! empty($lang['current_lang'])) {
                $current = $lang['url'];
            }
        }
    ?>
        <div class="xclear-language-switcher xclear-language-switcher--dropdown">
            <select class="xclear-language-switcher__select"
                onchange="if(this.value) window.location.href = this.value;">
                <?php foreach ($languages as $lang) : ?>
                    <option value="<?php echo esc_url($lang['url']); ?>"
                        <?php selected($lang['url'], $current); ?>>
                        <?php echo esc_html($this->getLabel($lang, $format)); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    <?php
    }
}
