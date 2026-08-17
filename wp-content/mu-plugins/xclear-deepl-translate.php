<?php
/**
 * Plugin Name: Xclear DeepL Translate
 * Description: Admin tool — auto-translate Elementor + Polylang pages via DeepL API.
 * Version:     1.0.0
 */

if (! defined('ABSPATH')) {
    exit;
}

class XclearDeepLAdmin
{
    private const OPTION_API_KEY        = 'xclear_deepl_api_key';
    private const OPTION_CLAUDE_API_KEY = 'xclear_claude_api_key';
    private const AJAX_ACTION           = 'xclear_deepl_translate_post';
    private const AJAX_ACTION_TERM      = 'xclear_deepl_translate_term';
    private const AJAX_ACTION_SYNC_CATS = 'xclear_deepl_sync_categories';
    private const NONCE_ACTION          = 'xclear_deepl_nonce';

    private array $termTaxonomies = ['product_category', 'project_category', 'category'];

    private string $lastApiError = '';

    private array $textKeys = [
        // ── Standard Elementor fallback ───────────────────────────────────
        'title', 'text', 'description', 'editor', 'content', 'caption',
        'placeholder', 'button_text', 'label', 'heading', 'prefix', 'suffix',
        'inner_text', 'html', 'tab_title', 'tab_content',
        'accordion_title', 'accordion_content',

        // ── Elementor Pro "Nav Menu" widget ────────────────────────────────
        'item_title',      // repeater: menu item label

        // ── xclear-hero-banner-widget ─────────────────────────────────────
        'badge',           // badge chip text
        'description_1',   // first subtitle block
        'description_2',   // second subtitle block
        'btn1_text',       // primary button
        'btn2_text',       // secondary button

        // ── xclear-applications-cards-widget / xclear-categories-cards-widget
        // (badge, heading, description already covered above)
        'card_title',      // repeater: card heading
        'card_desc',       // repeater: card short description
        'card_btn_text',   // repeater: card button label
        'card_text',       // repeater: card body text (uvc-installation)
        'card_description', // repeater: longer card description (lamp-replacement)

        // ── xclear-about-highlights-widget ────────────────────────────────
        'item_text',       // repeater: highlight text
        'item_label',      // repeater: item label (uvc-guide)

        // ── xclear-document-download-widget ───────────────────────────────
        'doc_name',        // repeater: document display name

        // ── xclear-faq-widget ─────────────────────────────────────────────
        'badge_text',      // badge chip text (many widgets)
        'faq_category',    // repeater: visible category tab label
        'faq_question',    // repeater: question text
        'faq_answer',      // repeater: answer (WYSIWYG)

        // ── xclear-image-with-text-widget ─────────────────────────────────
        'box_title',       // main title (supports [hl] highlight)
        'box_description', // body description (WYSIWYG)
        'info_text',       // information panel (WYSIWYG)
        'action_text',     // primary button label
        'action_text_2',   // secondary button label
        'acc_title',       // repeater: accordion item title
        'acc_content',     // repeater: accordion item body (WYSIWYG)

        // ── xclear-section-header-widget ──────────────────────────────────
        'title_text',      // section heading
        'description_text', // section sub-description (WYSIWYG)

        // ── xclear-uvc-guide-widget ───────────────────────────────────────
        'desc_intro',      // introductory paragraph
        'col1_header',     // table column 1 header
        'col2_header',     // table column 2 header
        'col3_header',     // table column 3 header
        'row_col1',        // repeater: table row cell 1
        'row_col2',        // repeater: table row cell 2
        'row_col3',        // repeater: table row cell 3
        'note_text',       // footnote / note block

        // ── xclear-uvc-how-it-works-widget ────────────────────────────────
        'step_title',      // repeater: step title
        'step_desc',       // repeater: step description
        'tips_title',      // tips section heading
        'tip_text',        // repeater: tip card body

        // ── xclear-video-gallery-widget ───────────────────────────────────
        'video_title',     // repeater: video card title

        // ── xclear-article-widget ─────────────────────────────────────────
        'all_posts_label',     // "Show all" button/link label
        'search_placeholder',  // search input placeholder text
        'load_more_text',      // "Load more" button label

        // ── xclear-legal-widget ───────────────────────────────────────────
        'item_text',           // repeater: paragraph/heading content (WYSIWYG) — duplicate ok
        'table_title',         // repeater: table section heading
        'table_description',   // repeater: text above table
        'table_columns',       // repeater: pipe-separated column headers
        'table_rows',          // repeater: newline-separated rows

        // ── xclear-list-category-widget ───────────────────────────────────
        'custom_description',  // repeater: per-category description override
    ];

    private array $langMap = [
        'nl' => ['label' => 'Dutch (NL)',    'deepl' => 'NL', 'name' => 'Dutch'],
        'de' => ['label' => 'German (DE)',   'deepl' => 'DE', 'name' => 'German'],
        'fr' => ['label' => 'French (FR)',   'deepl' => 'FR', 'name' => 'French'],
        'es' => ['label' => 'Spanish (ES)',  'deepl' => 'ES', 'name' => 'Spanish'],
    ];

    public function __construct()
    {
        add_action('admin_menu', [$this, 'registerMenu']);
        add_action('wp_ajax_' . self::AJAX_ACTION, [$this, 'ajaxHandler']);
        add_action('wp_ajax_' . self::AJAX_ACTION_TERM, [$this, 'ajaxTermHandler']);
        add_action('wp_ajax_' . self::AJAX_ACTION_SYNC_CATS, [$this, 'ajaxSyncCategoriesHandler']);
    }

    // ── Menu ──────────────────────────────────────────────────────────────────

    public function registerMenu(): void
    {
        add_management_page(
            'DeepL Translate',
            'DeepL Translate',
            'manage_options',
            'xclear-deepl-translate',
            [$this, 'renderPage']
        );
    }

    // ── Admin page ────────────────────────────────────────────────────────────

    public function renderPage(): void
    {
        if (! current_user_can('manage_options')) {
            return;
        }

        if (isset($_POST['xclear_save_key']) && check_admin_referer('xclear_save_settings')) {
            update_option(self::OPTION_API_KEY, sanitize_text_field($_POST['xclear_api_key'] ?? ''));
            update_option(self::OPTION_CLAUDE_API_KEY, sanitize_text_field($_POST['xclear_claude_api_key'] ?? ''));
            echo '<div class="notice notice-success is-dismissible"><p>API keys saved.</p></div>';
        }

        $savedKey       = get_option(self::OPTION_API_KEY, '');
        $savedClaudeKey = get_option(self::OPTION_CLAUDE_API_KEY, '');
        $polylangOk     = function_exists('pll_get_post_translations');
        $nonce          = wp_create_nonce(self::NONCE_ACTION);

        $posts = [];
        if ($polylangOk) {
            $posts = get_posts([
                'post_type'   => ['page', 'post', 'product'],
                'post_status' => 'publish',
                'numberposts' => -1,
                'lang'        => 'en',
                'orderby'     => 'title',
                'order'       => 'ASC',
            ]);

            // Elementor Theme Builder templates (header/footer/archive) — same
            // Elementor content pipeline, just a different post type. Archive
            // covers both the product CPT archive ("list product" page) and
            // the product_category taxonomy archive.
            $templates = get_posts([
                'post_type'   => 'elementor_library',
                'post_status' => 'publish',
                'numberposts' => -1,
                'lang'        => 'en',
                'orderby'     => 'title',
                'order'       => 'ASC',
                'meta_query'  => [
                    [
                        'key'     => '_elementor_template_type',
                        'value'   => ['header', 'footer', 'archive'],
                        'compare' => 'IN',
                    ],
                ],
            ]);

            $posts = array_merge($posts, $templates);
        }

        $terms = [];
        if ($polylangOk) {
            foreach ($this->termTaxonomies as $tax) {
                if (! taxonomy_exists($tax)) {
                    continue;
                }
                $taxTerms = get_terms([
                    'taxonomy'   => $tax,
                    'hide_empty' => false,
                    'lang'       => 'en',
                    'orderby'    => 'name',
                    'order'      => 'ASC',
                ]);
                if (! is_wp_error($taxTerms)) {
                    $terms = array_merge($terms, $taxTerms);
                }
            }
        }

        ?>
        <div class="wrap" style="max-width:960px">
            <h1 style="display:flex;align-items:center;gap:10px">
                🌐 DeepL Auto-Translate
                <span style="font-size:13px;font-weight:400;color:#999">Elementor + Polylang</span>
            </h1>

            <?php if (! $polylangOk): ?>
                <div class="notice notice-error"><p><strong>Polylang is not active.</strong> This tool requires Polylang to map translation posts.</p></div>
            <?php endif; ?>

            <!-- API Key -->
            <div class="postbox" style="padding:0 20px 16px">
                <h2 class="hndle" style="padding:12px 0"><span>⚙️ Settings</span></h2>
                <form method="post">
                    <?php wp_nonce_field('xclear_save_settings'); ?>
                    <table class="form-table" style="margin:0">
                        <tr>
                            <th style="width:130px;padding-left:0">DeepL API Key</th>
                            <td style="padding-left:0">
                                <div style="display:flex;gap:8px;align-items:center">
                                    <input type="password" name="xclear_api_key"
                                           value="<?php echo esc_attr($savedKey); ?>"
                                           class="regular-text" style="width:380px"
                                           placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx:fx">
                                    <button type="submit" name="xclear_save_key" class="button">Save key</button>
                                    <?php if ($savedKey): ?>
                                        <span style="color:#46b450">✓ Key saved</span>
                                    <?php endif; ?>
                                </div>
                                <p class="description" style="margin-top:4px">
                                    Free keys end with <code>:fx</code> — uses <code>api-free.deepl.com</code>.
                                    Get your key at <a href="https://www.deepl.com/pro-api" target="_blank">deepl.com/pro-api</a>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th style="width:130px;padding-left:0">Claude API Key</th>
                            <td style="padding-left:0">
                                <div style="display:flex;gap:8px;align-items:center">
                                    <input type="password" name="xclear_claude_api_key"
                                           value="<?php echo esc_attr($savedClaudeKey); ?>"
                                           class="regular-text" style="width:380px"
                                           placeholder="sk-ant-xxxxxxxxxxxxxxxxxxxxxxxx">
                                    <button type="submit" name="xclear_save_key" class="button">Save key</button>
                                    <?php if ($savedClaudeKey): ?>
                                        <span style="color:#46b450">✓ Key saved</span>
                                    <?php endif; ?>
                                </div>
                                <p class="description" style="margin-top:4px">
                                    Uses <code>claude-haiku-4-5</code>. Get your key at <a href="https://console.anthropic.com/settings/keys" target="_blank">console.anthropic.com</a>
                                </p>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>

            <!-- Translate tool -->
            <div class="postbox" style="padding:0 20px 20px">
                <h2 class="hndle" style="padding:12px 0"><span>📄 Translate pages / posts / products</span></h2>

                <!-- Options bar -->
                <div style="display:flex;gap:32px;margin-bottom:16px;flex-wrap:wrap;align-items:flex-start">
                    <div>
                        <strong style="display:block;margin-bottom:6px">Translation Engine</strong>
                        <label style="display:block;cursor:pointer;margin-bottom:4px">
                            <input type="radio" name="engine" id="engine-deepl" value="deepl" checked>
                            DeepL
                        </label>
                        <label style="display:block;cursor:pointer">
                            <input type="radio" name="engine" id="engine-claude" value="claude">
                            Claude (Haiku 4.5)
                        </label>
                    </div>
                    <div>
                        <strong style="display:block;margin-bottom:6px">Target languages</strong>
                        <?php foreach ($this->langMap as $code => $info): ?>
                            <label style="display:inline-flex;align-items:center;gap:5px;margin-right:16px;cursor:pointer">
                                <input type="checkbox" class="lang-check" value="<?php echo esc_attr($code); ?>" checked>
                                <?php echo esc_html($info['label']); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <div>
                        <strong style="display:block;margin-bottom:6px">Options</strong>
                        <label style="display:block;cursor:pointer;margin-bottom:4px">
                            <input type="checkbox" id="opt-titles">
                            Also translate page titles
                        </label>
                        <label style="display:block;cursor:pointer;margin-bottom:4px">
                            <input type="checkbox" id="opt-autocreate" checked>
                            Auto-create missing translation posts
                            <span style="color:#999">(creates if not yet duplicated in Polylang)</span>
                        </label>
                        <label style="display:block;cursor:pointer;margin-bottom:4px">
                            <input type="checkbox" id="opt-linksonly">
                            Only update internal links
                            <span style="color:#999">(skips text translation, only rewrites internal links on already-translated posts)</span>
                        </label>
                        <label style="display:block;cursor:pointer">
                            <input type="checkbox" id="opt-dryrun">
                            Dry run <span style="color:#999">(preview only — no changes saved)</span>
                        </label>
                    </div>
                </div>

                <?php if (empty($posts)): ?>
                    <p><em>No published English pages found.<?php echo $polylangOk ? ' Make sure pages are assigned to the English language in Polylang.' : ''; ?></em></p>
                <?php else: ?>

                    <!-- Posts table -->
                    <table class="wp-list-table widefat fixed striped" style="margin-bottom:12px">
                        <thead>
                            <tr>
                                <th style="width:32px">
                                    <input type="checkbox" id="check-all" title="Select all" checked>
                                </th>
                                <th>Title</th>
                                <th style="width:48px;text-align:center">ID</th>
                                <?php foreach ($this->langMap as $code => $info): ?>
                                    <th style="width:52px;text-align:center" title="<?php echo esc_attr($info['label']); ?>">
                                        <?php echo strtoupper(esc_html($code)); ?>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($posts as $post):
                                $translations = pll_get_post_translations($post->ID);
                            ?>
                                <tr id="post-row-<?php echo $post->ID; ?>">
                                    <td>
                                        <input type="checkbox" class="post-check" value="<?php echo $post->ID; ?>" checked>
                                    </td>
                                    <td>
                                        <strong><?php echo esc_html($post->post_title ?: '(no title)'); ?></strong>
                                        <span style="color:#999;font-size:11px;margin-left:4px"><?php echo esc_html($post->post_type); ?></span>
                                    </td>
                                    <td style="text-align:center;color:#999"><?php echo $post->ID; ?></td>
                                    <?php foreach ($this->langMap as $code => $info):
                                        $tPostId  = $translations[$code] ?? 0;
                                        $hasContent = false;
                                        if ($tPostId) {
                                            if ($post->post_type === 'product') {
                                                $hasContent = (bool) get_post_meta($tPostId, 'product_short_description', true)
                                                           || (bool) get_post_meta($tPostId, 'product_advantages', true);
                                            } else {
                                                $hasContent = (bool) get_post_meta($tPostId, '_elementor_data', true);
                                            }
                                        }
                                    ?>
                                        <td style="text-align:center"
                                            class="lang-status" id="status-<?php echo $post->ID; ?>-<?php echo $code; ?>">
                                            <?php if (! $tPostId): ?>
                                                <span style="color:#ccc" title="No translation post in Polylang">—</span>
                                            <?php elseif ($hasContent): ?>
                                                <span style="color:#46b450" title="Has translated content">✓</span>
                                            <?php else: ?>
                                                <span style="color:#dba617" title="Translation post exists but no content yet">○</span>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- Action bar -->
                    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-bottom:16px">
                        <button class="button" id="btn-select-all">Select all</button>
                        <button class="button" id="btn-deselect-all">Deselect all</button>
                        <button class="button button-primary" id="btn-translate" <?php echo ($savedKey || $savedClaudeKey) ? '' : 'disabled title="Save an API key first"'; ?>>
                            ▶ Translate selected
                        </button>
                        <span id="translate-status" style="color:#666;font-size:13px"></span>
                    </div>

                <?php endif; ?>
            </div>

            <!-- Translate categories -->
            <div class="postbox" style="padding:0 20px 20px">
                <h2 class="hndle" style="padding:12px 0"><span>🏷️ Translate categories</span></h2>
                <p class="description" style="margin-top:0">Product &amp; Project categories (name + description). Uses the same Engine / Languages / Dry run options above.</p>

                <?php if (empty($terms)): ?>
                    <p><em>No categories found.<?php echo $polylangOk ? ' Make sure terms are assigned to the English language in Polylang.' : ''; ?></em></p>
                <?php else: ?>

                    <table class="wp-list-table widefat fixed striped" style="margin-bottom:12px">
                        <thead>
                            <tr>
                                <th style="width:32px">
                                    <input type="checkbox" id="check-all-terms" title="Select all" checked>
                                </th>
                                <th>Category</th>
                                <th style="width:48px;text-align:center">ID</th>
                                <?php foreach ($this->langMap as $code => $info): ?>
                                    <th style="width:52px;text-align:center" title="<?php echo esc_attr($info['label']); ?>">
                                        <?php echo strtoupper(esc_html($code)); ?>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($terms as $term):
                                $translations = function_exists('pll_get_term_translations') ? pll_get_term_translations($term->term_id) : [];
                            ?>
                                <tr id="term-row-<?php echo $term->term_id; ?>">
                                    <td>
                                        <input type="checkbox" class="term-check" value="<?php echo $term->term_id; ?>" checked>
                                    </td>
                                    <td>
                                        <strong><?php echo esc_html($term->name); ?></strong>
                                        <span style="color:#999;font-size:11px;margin-left:4px"><?php echo esc_html($term->taxonomy); ?></span>
                                    </td>
                                    <td style="text-align:center;color:#999"><?php echo $term->term_id; ?></td>
                                    <?php foreach ($this->langMap as $code => $info):
                                        $tTermId    = $translations[$code] ?? 0;
                                        $tTermObj   = $tTermId ? get_term($tTermId) : null;
                                        $hasName    = ($tTermObj instanceof \WP_Term) && $tTermObj->name !== '';
                                    ?>
                                        <td style="text-align:center"
                                            class="term-lang-status" id="term-status-<?php echo $term->term_id; ?>-<?php echo $code; ?>">
                                            <?php if (! $tTermId): ?>
                                                <span style="color:#ccc" title="No translation term in Polylang">—</span>
                                            <?php elseif ($hasName): ?>
                                                <span style="color:#46b450" title="Has translated name">✓</span>
                                            <?php else: ?>
                                                <span style="color:#dba617" title="Translation term exists but empty">○</span>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-bottom:16px">
                        <button class="button" id="btn-select-all-terms">Select all</button>
                        <button class="button" id="btn-deselect-all-terms">Deselect all</button>
                        <button class="button button-primary" id="btn-translate-terms" <?php echo ($savedKey || $savedClaudeKey) ? '' : 'disabled title="Save an API key first"'; ?>>
                            ▶ Translate selected categories
                        </button>
                        <span id="translate-terms-status" style="color:#666;font-size:13px"></span>
                    </div>

                <?php endif; ?>
            </div>

            <!-- Sync product categories -->
            <div class="postbox" style="padding:0 20px 20px">
                <h2 class="hndle" style="padding:12px 0"><span>🔗 Sync product categories</span></h2>
                <p class="description" style="margin-top:0">
                    For every translated product, re-assigns its category to the translated version of the English product's category (falls back to the English category if that one isn't translated yet). Fixes products that were auto-created before categories had translations. No API calls — instant, no language/engine selection needed.
                </p>
                <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-bottom:8px">
                    <button class="button button-primary" id="btn-sync-categories">▶ Sync now</button>
                    <span id="sync-categories-status" style="color:#666;font-size:13px"></span>
                </div>
            </div>

            <!-- Progress log -->
            <div class="postbox" style="padding:0 20px 20px">
                <div id="deepl-log-wrap" style="display:none">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
                        <strong>Log</strong>
                        <button class="button button-small" id="btn-clear-log">Clear</button>
                    </div>
                    <div id="deepl-log"
                         style="background:#1d2327;color:#a7aaad;font-family:Consolas,monospace;font-size:12px;
                                line-height:1.7;padding:12px 16px;border-radius:4px;max-height:360px;
                                overflow-y:auto;white-space:pre-wrap;word-break:break-word"></div>
                </div>
            </div>
        </div>

        <script>
        (function ($) {
            const NONCE         = <?php echo json_encode($nonce); ?>;
            const AJAXURL       = <?php echo json_encode(admin_url('admin-ajax.php')); ?>;
            const AJAX_ACT      = <?php echo json_encode(self::AJAX_ACTION); ?>;
            const AJAX_ACT_TERM = <?php echo json_encode(self::AJAX_ACTION_TERM); ?>;
            const AJAX_ACT_SYNC = <?php echo json_encode(self::AJAX_ACTION_SYNC_CATS); ?>;

            // ── Helpers
            function log(msg, color) {
                const wrap = document.getElementById('deepl-log-wrap');
                const el   = document.getElementById('deepl-log');
                wrap.style.display = 'block';
                const line = document.createElement('span');
                if (color) line.style.color = color;
                line.textContent = msg + '\n';
                el.appendChild(line);
                el.scrollTop = el.scrollHeight;
            }

            // ── Checkbox controls
            $('#check-all').on('change', function () {
                $('.post-check').prop('checked', this.checked);
            });
            $('#btn-select-all').on('click', function (e) {
                e.preventDefault();
                $('.post-check, #check-all').prop('checked', true);
            });
            $('#btn-deselect-all').on('click', function (e) {
                e.preventDefault();
                $('.post-check, #check-all').prop('checked', false);
            });
            $('#check-all-terms').on('change', function () {
                $('.term-check').prop('checked', this.checked);
            });
            $('#btn-select-all-terms').on('click', function (e) {
                e.preventDefault();
                $('.term-check, #check-all-terms').prop('checked', true);
            });
            $('#btn-deselect-all-terms').on('click', function (e) {
                e.preventDefault();
                $('.term-check, #check-all-terms').prop('checked', false);
            });
            $('#btn-clear-log').on('click', function () {
                $('#deepl-log').empty();
            });

            // ── Main translate
            $('#btn-translate').on('click', function (e) {
                e.preventDefault();

                const postIds    = $('.post-check:checked').map(function () { return this.value; }).get();
                const langs      = $('.lang-check:checked').map(function () { return this.value; }).get();
                const engine     = $('input[name="engine"]:checked').val() || 'deepl';
                const titles     = $('#opt-titles').is(':checked') ? 1 : 0;
                const autoCreate = $('#opt-autocreate').is(':checked') ? 1 : 0;
                const linksOnly  = $('#opt-linksonly').is(':checked') ? 1 : 0;
                const dryRun     = $('#opt-dryrun').is(':checked') ? 1 : 0;

                if (! postIds.length) { alert('Select at least one page.'); return; }
                if (! langs.length)   { alert('Select at least one language.'); return; }

                $('#btn-translate').prop('disabled', true);
                $('#translate-status').text('');

                if (dryRun) log('⚠ DRY RUN — no changes will be saved\n', '#f0a500');

                // One AJAX call per (post × language) pair — keeps each request
                // short enough to avoid gateway timeouts (504) when the engine
                // (especially Claude) takes a while per language.
                const jobs = [];
                postIds.forEach(function (postId) {
                    langs.forEach(function (lang) {
                        jobs.push({ postId: postId, lang: lang });
                    });
                });

                let idx = 0;

                function processNext() {
                    if (idx >= jobs.length) {
                        $('#btn-translate').prop('disabled', false);
                        $('#translate-status').text('✓ Done');
                        log('\n✓ All done.', '#46b450');
                        return;
                    }

                    const job = jobs[idx++];
                    $('#translate-status').text('(' + idx + '/' + jobs.length + ') post #' + job.postId + ' [' + job.lang + ']…');

                    $.post(AJAXURL, {
                        action:           AJAX_ACT,
                        nonce:            NONCE,
                        post_id:          job.postId,
                        langs:            job.lang,
                        engine:           engine,
                        translate_titles: titles,
                        auto_create:      autoCreate,
                        links_only:       linksOnly,
                        dry_run:          dryRun,
                    })
                    .done(function (res) {
                        let quotaHit = false;
                        if (res.success && res.data && res.data.log) {
                            res.data.log.forEach(function (line) {
                                const color = line.includes('✘') || line.includes('⚠') ? '#f86368'
                                            : line.includes('✓') ? '#46b450'
                                            : null;
                                log(line, color);
                                if (line.indexOf('quota exceeded') !== -1) quotaHit = true;
                            });
                        } else {
                            log('  ✘ ' + (res.data && res.data.message ? res.data.message : 'Unknown error'), '#f86368');
                        }
                        if (quotaHit) {
                            $('#btn-translate').prop('disabled', false);
                            $('#translate-status').text('✘ Stopped (quota exceeded)');
                            log('\n✘ DeepL quota exceeded — stopped remaining ' + (jobs.length - idx) + ' job(s). Nothing further was translated.', '#f86368');
                            return;
                        }
                        processNext();
                    })
                    .fail(function (xhr) {
                        log('  ✘ HTTP ' + xhr.status + ' on post #' + job.postId + ' [' + job.lang + ']', '#f86368');
                        processNext();
                    });
                }

                processNext();
            });

            // ── Translate categories
            $('#btn-translate-terms').on('click', function (e) {
                e.preventDefault();

                const termIds    = $('.term-check:checked').map(function () { return this.value; }).get();
                const langs      = $('.lang-check:checked').map(function () { return this.value; }).get();
                const engine     = $('input[name="engine"]:checked').val() || 'deepl';
                const autoCreate = $('#opt-autocreate').is(':checked') ? 1 : 0;
                const dryRun     = $('#opt-dryrun').is(':checked') ? 1 : 0;

                if (! termIds.length) { alert('Select at least one category.'); return; }
                if (! langs.length)   { alert('Select at least one language.'); return; }

                $('#btn-translate-terms').prop('disabled', true);
                $('#translate-terms-status').text('');

                if (dryRun) log('⚠ DRY RUN — no changes will be saved\n', '#f0a500');

                const termJobs = [];
                termIds.forEach(function (termId) {
                    langs.forEach(function (lang) {
                        termJobs.push({ termId: termId, lang: lang });
                    });
                });

                let termIdx = 0;

                function processNextTerm() {
                    if (termIdx >= termJobs.length) {
                        $('#btn-translate-terms').prop('disabled', false);
                        $('#translate-terms-status').text('✓ Done');
                        log('\n✓ All categories done.', '#46b450');
                        return;
                    }

                    const job = termJobs[termIdx++];
                    $('#translate-terms-status').text('(' + termIdx + '/' + termJobs.length + ') category #' + job.termId + ' [' + job.lang + ']…');

                    $.post(AJAXURL, {
                        action:      AJAX_ACT_TERM,
                        nonce:       NONCE,
                        term_id:     job.termId,
                        langs:       job.lang,
                        engine:      engine,
                        auto_create: autoCreate,
                        dry_run:     dryRun,
                    })
                    .done(function (res) {
                        let quotaHit = false;
                        if (res.success && res.data && res.data.log) {
                            res.data.log.forEach(function (line) {
                                const color = line.includes('✘') || line.includes('⚠') ? '#f86368'
                                            : line.includes('✓') ? '#46b450'
                                            : null;
                                log(line, color);
                                if (line.indexOf('quota exceeded') !== -1) quotaHit = true;
                            });
                        } else {
                            log('  ✘ ' + (res.data && res.data.message ? res.data.message : 'Unknown error'), '#f86368');
                        }
                        if (quotaHit) {
                            $('#btn-translate-terms').prop('disabled', false);
                            $('#translate-terms-status').text('✘ Stopped (quota exceeded)');
                            log('\n✘ DeepL quota exceeded — stopped remaining ' + (termJobs.length - termIdx) + ' job(s). Nothing further was translated.', '#f86368');
                            return;
                        }
                        processNextTerm();
                    })
                    .fail(function (xhr) {
                        log('  ✘ HTTP ' + xhr.status + ' on category #' + job.termId + ' [' + job.lang + ']', '#f86368');
                        processNextTerm();
                    });
                }

                processNextTerm();
            });

            // ── Sync product categories
            $('#btn-sync-categories').on('click', function (e) {
                e.preventDefault();

                $('#btn-sync-categories').prop('disabled', true);
                $('#sync-categories-status').text('Running…');

                $.post(AJAXURL, {
                    action: AJAX_ACT_SYNC,
                    nonce:  NONCE,
                })
                .done(function (res) {
                    if (res.success && res.data && res.data.log) {
                        res.data.log.forEach(function (line) {
                            const color = line.includes('✘') ? '#f86368'
                                        : line.includes('✓') ? '#46b450'
                                        : null;
                            log(line, color);
                        });
                    } else {
                        log('  ✘ ' + (res.data && res.data.message ? res.data.message : 'Unknown error'), '#f86368');
                    }
                    $('#btn-sync-categories').prop('disabled', false);
                    $('#sync-categories-status').text('✓ Done');
                })
                .fail(function (xhr) {
                    log('  ✘ HTTP ' + xhr.status + ' while syncing categories', '#f86368');
                    $('#btn-sync-categories').prop('disabled', false);
                    $('#sync-categories-status').text('✘ Failed');
                });
            });
        }(jQuery));
        </script>
        <?php
    }

    // ── AJAX handler ──────────────────────────────────────────────────────────

    public function ajaxHandler(): void
    {
        check_ajax_referer(self::NONCE_ACTION, 'nonce');

        if (! current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized.']);
        }

        $postId          = (int) ($_POST['post_id'] ?? 0);
        $rawLangs        = array_filter(array_map('trim', explode(',', sanitize_text_field($_POST['langs'] ?? ''))));
        $engine          = in_array($_POST['engine'] ?? '', ['deepl', 'claude'], true) ? $_POST['engine'] : 'deepl';
        $translateTitles = ! empty($_POST['translate_titles']);
        $autoCreate      = ! empty($_POST['auto_create']);
        $dryRun          = ! empty($_POST['dry_run']);
        $linksOnly       = ! empty($_POST['links_only']);
        $apiKey          = $engine === 'claude'
            ? get_option(self::OPTION_CLAUDE_API_KEY, '')
            : get_option(self::OPTION_API_KEY, '');

        if (! $postId || ! $apiKey || empty($rawLangs)) {
            wp_send_json_error(['message' => 'Missing parameters (post_id, langs, or API key).']);
        }

        $post = get_post($postId);
        if (! $post) {
            wp_send_json_error(['message' => "Post #{$postId} not found."]);
        }

        $targetLangs = [];
        foreach ($rawLangs as $code) {
            if (isset($this->langMap[$code])) {
                $targetLangs[] = $code;
            }
        }

        @set_time_limit(120);

        $log = $this->doTranslatePost($post, $apiKey, $targetLangs, $translateTitles, $autoCreate, $dryRun, $linksOnly, $engine);
        wp_send_json_success(['log' => $log]);
    }

    public function ajaxTermHandler(): void
    {
        check_ajax_referer(self::NONCE_ACTION, 'nonce');

        if (! current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized.']);
        }

        $termId     = (int) ($_POST['term_id'] ?? 0);
        $rawLangs   = array_filter(array_map('trim', explode(',', sanitize_text_field($_POST['langs'] ?? ''))));
        $engine     = in_array($_POST['engine'] ?? '', ['deepl', 'claude'], true) ? $_POST['engine'] : 'deepl';
        $autoCreate = ! empty($_POST['auto_create']);
        $dryRun     = ! empty($_POST['dry_run']);
        $apiKey     = $engine === 'claude'
            ? get_option(self::OPTION_CLAUDE_API_KEY, '')
            : get_option(self::OPTION_API_KEY, '');

        if (! $termId || ! $apiKey || empty($rawLangs)) {
            wp_send_json_error(['message' => 'Missing parameters (term_id, langs, or API key).']);
        }

        $term = get_term($termId);
        if (! $term || is_wp_error($term)) {
            wp_send_json_error(['message' => "Term #{$termId} not found."]);
        }

        $langCode = reset($rawLangs);
        if (! isset($this->langMap[$langCode])) {
            wp_send_json_error(['message' => "Unknown language: {$langCode}."]);
        }

        @set_time_limit(60);

        $log = $this->doTranslateTerm($term, $apiKey, $langCode, $autoCreate, $dryRun, $engine);
        wp_send_json_success(['log' => $log]);
    }

    private function doTranslateTerm(\WP_Term $term, string $apiKey, string $langCode, bool $autoCreate, bool $dryRun, string $engine): array
    {
        $log   = [];
        $log[] = "▸ [{$term->term_id}] {$term->name} ({$term->taxonomy})";

        if (! function_exists('pll_get_term_translations')) {
            $log[] = '  ✘ Polylang not available.';
            return $log;
        }

        $translations = pll_get_term_translations($term->term_id);
        $tTermId      = $translations[$langCode] ?? 0;

        if (! $tTermId || $tTermId === $term->term_id) {
            if (! $autoCreate) {
                $log[] = "  [{$langCode}] No translation term — create it in Polylang first (or enable Auto-create).";
                return $log;
            }

            if ($dryRun) {
                $log[] = "  [{$langCode}] [DRY RUN] Would auto-create translation term.";
                return $log;
            }

            // wp_insert_term() rejects a duplicate NAME within the same
            // taxonomy+parent outright (unlike post titles, which can repeat) —
            // and this check runs before we have a term ID to assign a
            // language to. Insert with a temporary unique name, then rename
            // back to the real name once the language is set, by which point
            // wp_update_term() doesn't re-run that duplicate-name guard.
            $tempName = $term->name . ' [' . strtoupper($langCode) . ']';
            $newTerm  = wp_insert_term($tempName, $term->taxonomy, [
                'description' => $term->description,
            ]);

            if (is_wp_error($newTerm)) {
                $log[] = "  [{$langCode}] ✘ Auto-create failed: " . $newTerm->get_error_message();
                return $log;
            }

            $newTermId = (int) $newTerm['term_id'];

            // Set language BEFORE restoring the name/slug — same reasoning as
            // posts: Polylang only allows a duplicate slug across languages
            // once it knows this term's language.
            pll_set_term_language($newTermId, $langCode);
            wp_update_term($newTermId, $term->taxonomy, [
                'name' => $term->name,
                'slug' => $term->slug,
            ]);

            $this->copyTermAcfMedia($term->term_id, $newTermId);

            $allTranslations            = pll_get_term_translations($term->term_id);
            $allTranslations[$langCode] = $newTermId;
            pll_save_term_translations($allTranslations);

            $tTermId = $newTermId;
            $log[]   = "  [{$langCode}] ✚ Auto-created term #{$tTermId}";
        }

        $log[] = "  [{$langCode}] → term #{$tTermId}";

        $hasDesc = trim((string) $term->description) !== '';
        $texts   = [$term->name];
        if ($hasDesc) {
            $texts[] = $term->description;
        }

        $results = $this->translateTexts($texts, $langCode, false, $apiKey, $engine);
        if ($results === null) {
            $log[] = '    ✘ Translation failed: ' . ($this->lastApiError ?: 'unknown error');
            return $log;
        }

        if (! $dryRun) {
            $update = ['name' => $results[0]];
            if ($hasDesc) {
                $update['description'] = $results[1];
            }
            wp_update_term($tTermId, $term->taxonomy, $update);
            $log[] = "    ✓ Name → \"{$results[0]}\"";
        } else {
            $log[] = "    [DRY RUN] Name would be → \"{$results[0]}\"";
        }

        return $log;
    }

    private function copyTermAcfMedia(int $sourceTermId, int $targetTermId): void
    {
        $fields = ['product_category_image', 'product_category_banner_image'];

        foreach ($fields as $field) {
            $value = get_term_meta($sourceTermId, $field, true);
            if ($value !== '' && $value !== false) {
                update_term_meta($targetTermId, $field, $value);
            }
            $fieldKey = get_term_meta($sourceTermId, '_' . $field, true);
            if ($fieldKey) {
                update_term_meta($targetTermId, '_' . $field, $fieldKey);
            }
        }
    }

    public function ajaxSyncCategoriesHandler(): void
    {
        check_ajax_referer(self::NONCE_ACTION, 'nonce');

        if (! current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized.']);
        }

        @set_time_limit(60);

        $log = $this->syncProductCategories();
        wp_send_json_success(['log' => $log]);
    }

    /**
     * Retroactive fix: for every translated product, re-assign its
     * "product_category" terms to match the translated version of whatever
     * categories the English original belongs to (falls back to the English
     * term if that specific category has no translation yet). Needed
     * because wp_insert_post() never carries over term assignments, and
     * older auto-created translations predate the auto-create category fix.
     */
    private function syncProductCategories(): array
    {
        $log = [];

        if (! function_exists('pll_get_post_translations') || ! function_exists('pll_get_term')) {
            $log[] = '✘ Polylang not available.';
            return $log;
        }

        $englishProducts = get_posts([
            'post_type'   => 'product',
            'post_status' => 'publish',
            'numberposts' => -1,
            'lang'        => 'en',
        ]);

        $fixedCount = 0;

        foreach ($englishProducts as $product) {
            $sourceTermIds = wp_get_object_terms($product->ID, 'product_category', ['fields' => 'ids']);
            if (empty($sourceTermIds) || is_wp_error($sourceTermIds)) {
                continue;
            }

            $translations = pll_get_post_translations($product->ID);

            foreach ($translations as $langCode => $translatedId) {
                $translatedId = (int) $translatedId;
                if ($langCode === 'en' || $translatedId === $product->ID) {
                    continue;
                }

                $targetTermIds = [];
                foreach ($sourceTermIds as $termId) {
                    $translatedTermId = pll_get_term($termId, $langCode);
                    $targetTermIds[]  = $translatedTermId ?: $termId;
                }

                $current = wp_get_object_terms($translatedId, 'product_category', ['fields' => 'ids']);
                $current = is_array($current) ? $current : [];

                $targetSorted  = $targetTermIds;
                $currentSorted = $current;
                sort($targetSorted);
                sort($currentSorted);

                if ($targetSorted !== $currentSorted) {
                    wp_set_object_terms($translatedId, $targetTermIds, 'product_category');
                    $log[]      = "✓ [{$langCode}] #{$translatedId} ({$product->post_title}) → categories updated";
                    $fixedCount++;
                }
            }
        }

        if ($fixedCount === 0) {
            $log[] = 'Nothing to fix — all translated products already match.';
        } else {
            $log[] = "\n✓ Fixed {$fixedCount} product(s).";
        }

        return $log;
    }

    // ── Core logic ────────────────────────────────────────────────────────────

    private function doTranslatePost(\WP_Post $post, string $apiKey, array $targetLangs, bool $translateTitles, bool $autoCreate, bool $dryRun, bool $linksOnly = false, string $engine = 'deepl'): array
    {
        $log   = [];
        $label = $post->post_title ?: '(no title)';
        $log[] = "▸ [{$post->ID}] {$label}";

        if (! function_exists('pll_get_post_translations')) {
            $log[] = '  ✘ Polylang not available.';
            return $log;
        }

        $elementorJson = get_post_meta($post->ID, '_elementor_data', true);
        $sourceData    = null;

        if (! empty($elementorJson)) {
            $sourceData = json_decode($elementorJson, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $log[]     = '  ✘ Invalid Elementor JSON — content skipped.';
                $sourceData = null;
            }
        } else {
            $log[] = '  (no Elementor data on this post)';
        }

        $translations = pll_get_post_translations($post->ID);

        foreach ($targetLangs as $langCode) {
            $tPostId = $translations[$langCode] ?? 0;

            if (! $tPostId || $tPostId === $post->ID) {
                if ($linksOnly) {
                    $log[] = "  [{$langCode}] No translation post — nothing to update links on.";
                    continue;
                }

                if (! $autoCreate) {
                    $log[] = "  [{$langCode}] No translation post — create it in Polylang first (or enable Auto-create).";
                    continue;
                }

                if ($dryRun) {
                    $log[] = "  [{$langCode}] [DRY RUN] Would auto-create translation post.";
                    continue;
                }

                // Step 1: create post (WordPress may append -2 to slug here)
                $newId = wp_insert_post([
                    'post_type'    => $post->post_type,
                    'post_status'  => $post->post_status,
                    'post_title'   => $post->post_title,
                    'post_author'  => $post->post_author,
                    'post_content' => '',
                ]);

                if (is_wp_error($newId)) {
                    $log[] = "  [{$langCode}] ✘ Auto-create failed: " . $newId->get_error_message();
                    continue;
                }

                // Step 2: set Polylang language — must happen BEFORE slug fix so
                // Polylang's wp_unique_post_slug filter knows this post is in a
                // different language and allows the same slug as EN
                pll_set_post_language($newId, $langCode);

                // Step 3: fix slug — now Polylang's filter is active, so the same
                // post_name is allowed (/nl/disclaimer vs /disclaimer)
                wp_update_post(['ID' => $newId, 'post_name' => $post->post_name]);

                // Step 4: copy Elementor + page template meta so the page renders
                // correctly without needing to open/publish in the Elementor editor
                $metaToCopy = ['_elementor_edit_mode', '_wp_page_template', '_elementor_template_type'];
                foreach ($metaToCopy as $metaKey) {
                    $val = get_post_meta($post->ID, $metaKey, true);
                    if ($val !== '' && $val !== false) {
                        update_post_meta($newId, $metaKey, $val);
                    }
                }
                // Ensure Elementor edit mode is always set to builder
                update_post_meta($newId, '_elementor_edit_mode', 'builder');

                // Step 4b: for product CPT, copy media + URL meta that is
                // language-independent (images, files, external URLs), and
                // assign the translated version of each category the source
                // product belongs to (falls back to the English term if that
                // category hasn't been translated yet).
                if ($post->post_type === 'product') {
                    $this->copyProductMediaMeta($post->ID, $newId);
                    $this->copyTranslatedTerms($post->ID, $newId, $langCode, 'product_category');
                }

                // Step 5: link to source translations
                $allTranslations            = pll_get_post_translations($post->ID);
                $allTranslations[$langCode] = $newId;
                pll_save_post_translations($allTranslations);

                $tPostId = $newId;
                $log[]   = "  [{$langCode}] ✚ Auto-created post #{$tPostId}";
            }

            $log[] = "  [{$langCode}] → post #{$tPostId}";

            // ── Links-only mode: rewrite internal links on the EXISTING
            // translated post's own Elementor data, skip everything else ──
            if ($linksOnly) {
                $targetJson  = get_post_meta($tPostId, '_elementor_data', true);
                $targetData  = null;
                if (! empty($targetJson)) {
                    $targetData = json_decode($targetJson, true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $log[]      = '    ✘ Invalid Elementor JSON on translation post — skipped.';
                        $targetData = null;
                    }
                }

                if ($targetData !== null) {
                    $linkCount = $this->rewriteInternalLinks($targetData, $langCode);
                    if ($linkCount > 0) {
                        if (! $dryRun) {
                            update_post_meta($tPostId, '_elementor_data', wp_slash(json_encode($targetData, JSON_UNESCAPED_UNICODE)));
                            delete_post_meta($tPostId, '_elementor_css');
                            $log[] = "    ✓ Rewrote {$linkCount} internal link(s) to the {$langCode} version.";
                        } else {
                            $log[] = "    [DRY RUN] Would rewrite {$linkCount} internal link(s).";
                        }
                    } else {
                        $log[] = '    (no internal links to update)';
                    }
                } else {
                    $log[] = '    (no Elementor data on translation post)';
                }

                continue;
            }

            // Elementor content
            if ($sourceData !== null) {
                $strings   = $this->collectStrings($sourceData);
                $htmlCount = count(array_filter($strings['items'], fn($s) => $s['is_html']));
                $log[]     = "    {$strings['count']} string(s) to translate ({$htmlCount} HTML/WYSIWYG, " . ($strings['count'] - $htmlCount) . ' plain text).';

                $workingData = $sourceData;
                $hasChanges  = false;

                if (! empty($strings['items'])) {
                    [$translated, $errors] = $this->applyTranslations($workingData, $strings['items'], $langCode, $apiKey, $engine);
                    foreach ($errors as $err) {
                        $log[] = '    ✘ ' . $err;
                    }
                    if ($translated !== null) {
                        $workingData = $translated;
                        $hasChanges  = true;
                    } else {
                        $log[] = "    ⚠ [{$langCode}] Translation failed completely — page content was NOT updated (previous content, likely English, remains unchanged).";
                    }
                }

                $linkCount = $this->rewriteInternalLinks($workingData, $langCode);
                if ($linkCount > 0) {
                    $hasChanges = true;
                    $log[]      = "    ✓ Rewrote {$linkCount} internal link(s) to the {$langCode} version.";
                }

                if ($hasChanges) {
                    if (! $dryRun) {
                        update_post_meta($tPostId, '_elementor_data', wp_slash(json_encode($workingData, JSON_UNESCAPED_UNICODE)));
                        delete_post_meta($tPostId, '_elementor_css');
                        $log[] = '    ✓ Elementor content saved.';
                    } else {
                        $log[] = '    [DRY RUN] Would save Elementor content.';
                    }
                }
            }

            // ACF fields (product CPT only)
            if ($post->post_type === 'product') {
                $acfLog = $this->translateProductAcfFields($post, $tPostId, $langCode, $apiKey, $dryRun, $engine);
                $log    = array_merge($log, $acfLog);
            }

            // Title
            if ($translateTitles && ! empty($post->post_title)) {
                $result = $this->translateTexts([$post->post_title], $langCode, false, $apiKey, $engine);
                if ($result !== null && ! empty($result[0])) {
                    if (! $dryRun) {
                        wp_update_post(['ID' => $tPostId, 'post_title' => $result[0]]);
                        $log[] = "    ✓ Title → \"{$result[0]}\"";
                    } else {
                        $log[] = "    [DRY RUN] Title would be → \"{$result[0]}\"";
                    }
                }
            }
        }

        return $log;
    }

    /**
     * Assign the translated version of each term (in $taxonomy) that the
     * source post belongs to. Falls back to the original English term when
     * no translation exists yet for that specific category, so the product
     * is never left uncategorized.
     */
    private function copyTranslatedTerms(int $sourceId, int $targetId, string $langCode, string $taxonomy): void
    {
        if (! taxonomy_exists($taxonomy)) {
            return;
        }

        $sourceTermIds = wp_get_object_terms($sourceId, $taxonomy, ['fields' => 'ids']);
        if (empty($sourceTermIds) || is_wp_error($sourceTermIds)) {
            return;
        }

        $targetTermIds = [];
        foreach ($sourceTermIds as $termId) {
            $translatedTermId = function_exists('pll_get_term') ? pll_get_term($termId, $langCode) : 0;
            $targetTermIds[]  = $translatedTermId ?: $termId;
        }

        wp_set_object_terms($targetId, $targetTermIds, $taxonomy);
    }

    private function copyProductMediaMeta(int $sourceId, int $targetId): void
    {
        // Featured image
        $thumbId = get_post_meta($sourceId, '_thumbnail_id', true);
        if ($thumbId) {
            update_post_meta($targetId, '_thumbnail_id', $thumbId);
        }

        // ACF fields that are the same across all languages (images, files, URLs)
        // Each ACF field has two meta entries: the value and the field-key reference (_field_name)
        $fields = [
            'product_gallery',
            'product_specs_image',
            'product_specs_image_mobile',
            'product_spare_parts_image',
            'product_spare_parts_image_mobile',
            'product_data_table_image',
            'product_data_table_image_mobile',
            'product_buy_parts_url',
            'product_manuals_file',
            'product_leaflet_file',
            'product_installation_videos',
        ];

        foreach ($fields as $field) {
            $value = get_post_meta($sourceId, $field, true);
            if ($value !== '' && $value !== false) {
                update_post_meta($targetId, $field, $value);
            }
            // Copy ACF's internal field-key reference so ACF recognises the value
            $fieldKey = get_post_meta($sourceId, '_' . $field, true);
            if ($fieldKey) {
                update_post_meta($targetId, '_' . $field, $fieldKey);
            }
        }
    }

    private function translateProductAcfFields(\WP_Post $source, int $targetId, string $langCode, string $apiKey, bool $dryRun, string $engine = 'deepl'): array
    {
        $log = [];

        // Simple fields: field_name => is_html
        $fieldDefs = [
            'product_short_description' => false,
            'product_advantages'        => true,
            'product_specs_html'        => true,
            'product_spare_parts_html'  => true,
            'product_data_table_html'   => true,
        ];

        $plainKeys  = [];
        $plainTexts = [];
        $htmlKeys   = [];
        $htmlTexts  = [];

        foreach ($fieldDefs as $fieldName => $isHtml) {
            $value = get_post_meta($source->ID, $fieldName, true);
            if (empty(trim((string) $value))) {
                continue;
            }
            if ($isHtml) {
                $htmlKeys[]  = $fieldName;
                $htmlTexts[] = $value;
            } else {
                $plainKeys[]  = $fieldName;
                $plainTexts[] = $value;
            }
        }

        // Plain text batch
        if (! empty($plainTexts)) {
            $results = $this->translateTexts($plainTexts, $langCode, false, $apiKey, $engine);
            if ($results !== null) {
                foreach ($plainKeys as $i => $key) {
                    if (! $dryRun) {
                        update_post_meta($targetId, $key, $results[$i]);
                    }
                    $log[] = "    ✓ ACF {$key}" . ($dryRun ? ' [DRY RUN]' : '');
                }
            } else {
                $log[] = '    ✘ ACF plain text fields failed: ' . ($this->lastApiError ?: 'unknown error');
            }
        }

        // HTML batch — per-field fallback if batch fails
        if (! empty($htmlTexts)) {
            $results = $this->translateTexts($htmlTexts, $langCode, true, $apiKey, $engine);
            if ($results !== null) {
                foreach ($htmlKeys as $i => $key) {
                    if (! $dryRun) {
                        update_post_meta($targetId, $key, $results[$i]);
                    }
                    $log[] = "    ✓ ACF {$key}" . ($dryRun ? ' [DRY RUN]' : '');
                }
            } else {
                foreach ($htmlKeys as $i => $key) {
                    $single = $this->translateTexts([$htmlTexts[$i]], $langCode, true, $apiKey, $engine);
                    if ($single === null) {
                        $stripped = trim(wp_strip_all_tags($htmlTexts[$i]));
                        $plain    = $stripped !== '' ? $this->translateTexts([$stripped], $langCode, false, $apiKey, $engine) : null;
                        $single   = $plain !== null ? [$this->rewrapHtml($htmlTexts[$i], $plain[0])] : null;
                    }
                    if ($single !== null) {
                        if (! $dryRun) {
                            update_post_meta($targetId, $key, $single[0]);
                        }
                        $log[] = "    ✓ ACF {$key}" . ($dryRun ? ' [DRY RUN]' : '');
                    } else {
                        $log[] = "    ✘ ACF {$key} failed: " . ($this->lastApiError ?: 'unknown error');
                    }
                }
            }
        }

        // Repeater: product_installation_videos → video_caption
        if (function_exists('get_field') && function_exists('update_field')) {
            $videos = get_field('product_installation_videos', $source->ID);
            if (! empty($videos) && is_array($videos)) {
                $captionIdxs  = [];
                $captionTexts = [];
                foreach ($videos as $i => $video) {
                    if (! empty(trim((string) ($video['video_caption'] ?? '')))) {
                        $captionIdxs[]  = $i;
                        $captionTexts[] = $video['video_caption'];
                    }
                }
                if (! empty($captionTexts)) {
                    $results = $this->translateTexts($captionTexts, $langCode, false, $apiKey, $engine);
                    if ($results !== null) {
                        foreach ($captionIdxs as $j => $vidIdx) {
                            $videos[$vidIdx]['video_caption'] = $results[$j];
                        }
                        if (! $dryRun) {
                            update_field('product_installation_videos', $videos, $targetId);
                        }
                        $log[] = '    ✓ ACF product_installation_videos (captions)' . ($dryRun ? ' [DRY RUN]' : '');
                    } else {
                        $log[] = '    ✘ ACF product_installation_videos captions failed: ' . ($this->lastApiError ?: 'unknown error');
                    }
                }
            }
        }

        return $log;
    }

    /**
     * Walk an Elementor JSON tree and rewrite any internal link (Elementor
     * URL control: ['url' => '...', ...]) that points to a post which has a
     * translation in $langCode, so hardcoded buttons follow the translation.
     */
    private function rewriteInternalLinks(array &$elements, string $langCode): int
    {
        $count = 0;

        foreach ($elements as &$element) {
            if (! empty($element['settings']) && is_array($element['settings'])) {
                foreach ($element['settings'] as &$val) {
                    if (is_array($val) && array_key_exists('url', $val) && is_string($val['url']) && $val['url'] !== '') {
                        $newUrl = $this->translateInternalUrl($val['url'], $langCode);
                        if ($newUrl !== null && $newUrl !== $val['url']) {
                            $val['url'] = $newUrl;
                            $count++;
                        }
                    } elseif (is_array($val)) {
                        // Repeater sub-items
                        foreach ($val as &$rItem) {
                            if (! is_array($rItem)) {
                                continue;
                            }

                            // xclear-nav-dropdown-widget repeater pattern:
                            // link_type=page/custom/none + link_page (post ID) + link_custom (path).
                            if (array_key_exists('link_page', $rItem) || array_key_exists('link_custom', $rItem)) {
                                $count += $this->rewriteNavLinkFields($rItem, $langCode);
                            }

                            foreach ($rItem as &$rVal) {
                                if (is_array($rVal) && array_key_exists('url', $rVal) && is_string($rVal['url']) && $rVal['url'] !== '') {
                                    $newUrl = $this->translateInternalUrl($rVal['url'], $langCode);
                                    if ($newUrl !== null && $newUrl !== $rVal['url']) {
                                        $rVal['url'] = $newUrl;
                                        $count++;
                                    }
                                }
                            }
                        }
                    }
                }

                // xclear-nav-dropdown-widget flat "custom button" pattern:
                // btn{n}_link_type + btn{n}_link_page + btn{n}_link_custom as
                // sibling top-level settings keys (not nested in a repeater).
                $count += $this->rewriteFlatNavButtonLinks($element['settings'], $langCode);
            }

            if (! empty($element['elements']) && is_array($element['elements'])) {
                $count += $this->rewriteInternalLinks($element['elements'], $langCode);
            }
        }

        return $count;
    }

    /**
     * Remap a single {link_type, link_page, link_custom} field group to the
     * target language, whether it lives in a repeater item or flat settings.
     */
    private function rewriteNavLinkFields(array &$item, string $langCode): int
    {
        // Elementor only persists a repeater field when it differs from its
        // control default — the nav widget's link_type control defaults to
        // 'page' (see resolveItemUrl()'s own `?? 'page'`), so an absent key
        // here still means "page", not "no link".
        $linkType = $item['link_type'] ?? 'page';

        if ($linkType === 'page' && ! empty($item['link_page']) && function_exists('pll_get_post')) {
            $pageId = (int) $item['link_page'];
            $translatedId = pll_get_post($pageId, $langCode);
            if ($translatedId && (int) $translatedId !== $pageId) {
                $item['link_page'] = (string) $translatedId;
                return 1;
            }
            return 0;
        }

        if ($linkType === 'custom' && ! empty($item['link_custom'])) {
            $fakeUrl = home_url('/' . ltrim((string) $item['link_custom'], '/'));
            $newUrl  = $this->translateInternalUrl($fakeUrl, $langCode);
            if ($newUrl !== null) {
                $newPath = (string) wp_parse_url($newUrl, PHP_URL_PATH);
                $newPath = trim($newPath, '/');
                if ($newPath !== '' && $newPath !== trim((string) $item['link_custom'], '/')) {
                    $item['link_custom'] = '/' . $newPath . '/';
                    return 1;
                }
            }
            return 0;
        }

        return 0;
    }

    private function rewriteFlatNavButtonLinks(array &$settings, string $langCode): int
    {
        $count = 0;
        foreach ($settings as $key => $val) {
            if (! is_string($key) || ! str_ends_with($key, '_link_type')) {
                continue;
            }
            $prefix = substr($key, 0, -strlen('link_type'));
            $pageKey   = $prefix . 'link_page';
            $customKey = $prefix . 'link_custom';
            if (! array_key_exists($pageKey, $settings) && ! array_key_exists($customKey, $settings)) {
                continue;
            }

            $item = [
                'link_type'   => $settings[$key] ?? '',
                'link_page'   => $settings[$pageKey] ?? '',
                'link_custom' => $settings[$customKey] ?? '',
            ];
            $changed = $this->rewriteNavLinkFields($item, $langCode);
            if ($changed) {
                $settings[$pageKey]   = $item['link_page'];
                $settings[$customKey] = $item['link_custom'];
                $count += $changed;
            }
        }
        return $count;
    }

    private function translateInternalUrl(string $url, string $langCode): ?string
    {
        if (! function_exists('pll_get_post')) {
            return null;
        }

        // Compare by HOST, not the full home_url() string — this site is
        // reachable through more than one domain (e.g. a configured
        // siteurl plus a separate hosting alias used to browse it), so a
        // strict home_url() prefix match would wrongly treat same-site
        // links as external just because the domain differs.
        $urlHost = wp_parse_url($url, PHP_URL_HOST);
        if ($urlHost) {
            $knownHosts = array_filter(array_unique([
                wp_parse_url(home_url(), PHP_URL_HOST),
                wp_parse_url(site_url(), PHP_URL_HOST),
                isset($_SERVER['HTTP_HOST']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_HOST'])) : null,
            ]));
            if (! in_array($urlHost, $knownHosts, true)) {
                return null; // genuinely external domain
            }
        }

        $path = (string) wp_parse_url($url, PHP_URL_PATH);

        // Don't use url_to_postid() here: its WP_Query is filtered by
        // Polylang's pre_get_posts using whatever language the admin UI
        // currently happens to be in. Since slugs are shared across
        // languages, that silently resolves the slug to the WRONG
        // language's post (e.g. resolving "/contact-us/" straight to the
        // French post while browsing admin in French), making the link
        // look "already correct" and skipping it. Query by slug with an
        // explicit default-language constraint instead, since the source
        // Elementor data always belongs to the default-language post.
        $postId = $this->urlToPostIdInDefaultLang($url);

        if ($postId) {
            $translatedId = pll_get_post($postId, $langCode);
            if (! $translatedId || (int) $translatedId === $postId) {
                return null;
            }
            return get_permalink($translatedId) ?: null;
        }

        // Slug-based lookup only resolves posts/pages — fall back to
        // taxonomy term archive links (e.g. /project-category/projects/).
        $taxUrl = $this->translateTaxonomyUrl($path, $langCode);
        if ($taxUrl !== null) {
            return $taxUrl;
        }

        // Still nothing — try post type archive links (e.g. /product/,
        // the CPT archive itself rather than a single product).
        return $this->translatePostTypeArchiveUrl($path, $langCode);
    }

    private function urlToPostIdInDefaultLang(string $url): int
    {
        $path = trim((string) wp_parse_url($url, PHP_URL_PATH), '/');
        if ($path === '') {
            return 0;
        }

        $slashPos = strrpos($path, '/');
        $slug     = $slashPos === false ? $path : substr($path, $slashPos + 1);
        if ($slug === '') {
            return 0;
        }

        $args = [
            'name'        => $slug,
            'post_type'   => 'any',
            'post_status' => 'publish',
            'numberposts' => 1,
        ];
        if (function_exists('pll_default_language')) {
            $args['lang'] = pll_default_language();
        }

        $posts = get_posts($args);

        return $posts ? (int) $posts[0]->ID : 0;
    }

    private function translatePostTypeArchiveUrl(string $path, string $langCode): ?string
    {
        if (! function_exists('PLL') || ! function_exists('pll_is_translated_post_type')) {
            return null;
        }

        $cleanPath = $this->stripLangPrefix(trim((string) wp_parse_url($path, PHP_URL_PATH), '/'));
        if ($cleanPath === '') {
            return null;
        }

        foreach (get_post_types(['has_archive' => true]) as $postType) {
            if (! pll_is_translated_post_type($postType)) {
                continue;
            }

            $archiveLink = get_post_type_archive_link($postType);
            if (! $archiveLink) {
                continue;
            }

            // get_post_type_archive_link() is filtered by Polylang using
            // whatever language the CURRENT request/admin screen happens to
            // be in — unrelated to $langCode or to the link being rewritten
            // — so strip any language prefix it added before comparing.
            $archivePath = $this->stripLangPrefix(trim((string) wp_parse_url($archiveLink, PHP_URL_PATH), '/'));
            if ($archivePath === '' || $archivePath !== $cleanPath) {
                continue;
            }

            $lang = PLL()->model->get_language($langCode);
            if (! $lang) {
                return null;
            }

            return PLL()->links_model->switch_language_in_link($archiveLink, $lang);
        }

        return null;
    }

    private function stripLangPrefix(string $cleanPath): string
    {
        if ($cleanPath === '' || ! function_exists('pll_languages_list')) {
            return $cleanPath;
        }

        $slugs = pll_languages_list();
        $first = strtok($cleanPath, '/');
        if ($first !== false && in_array($first, $slugs, true)) {
            return (string) substr($cleanPath, strlen($first) + 1);
        }

        return $cleanPath;
    }

    private function translateTaxonomyUrl(string $path, string $langCode): ?string
    {
        if (! function_exists('pll_get_term')) {
            return null;
        }

        $cleanPath = trim((string) wp_parse_url($path, PHP_URL_PATH), '/');
        if ($cleanPath === '') {
            return null;
        }

        $slug = substr($cleanPath, strrpos($cleanPath, '/') + 1);
        if ($slug === '') {
            return null;
        }

        // Check our known taxonomies first — they may be registered by a
        // plugin (e.g. Custom Post Type UI) without the 'public' arg set the
        // way get_taxonomies(['public' => true]) expects, which would
        // otherwise silently skip them.
        $candidates = array_unique(array_merge($this->termTaxonomies, get_taxonomies(['public' => true])));

        foreach ($candidates as $taxonomy) {
            if (! taxonomy_exists($taxonomy)) {
                continue;
            }

            $term = get_term_by('slug', $slug, $taxonomy);
            if (! ($term instanceof \WP_Term)) {
                continue;
            }

            $translatedTermId = pll_get_term($term->term_id, $langCode);
            if (! $translatedTermId || (int) $translatedTermId === $term->term_id) {
                return null;
            }

            $link = get_term_link((int) $translatedTermId, $taxonomy);
            return is_wp_error($link) ? null : $link;
        }

        return null;
    }

    private function collectStrings(array $elements, array $basePath = []): array
    {
        $items = [];

        foreach ($elements as $idx => $element) {
            $elemPath = array_merge($basePath, [$idx]);

            if (! empty($element['settings']) && is_array($element['settings'])) {
                foreach ($element['settings'] as $key => $val) {
                    if (is_string($val) && in_array($key, $this->textKeys, true) && trim($val) !== '') {
                        $items[] = [
                            'path'    => array_merge($elemPath, ['settings', $key]),
                            'text'    => $val,
                            'is_html' => $this->looksLikeHtml($val),
                        ];
                    } elseif (is_array($val)) {
                        // Repeater sub-items
                        foreach ($val as $rIdx => $rItem) {
                            if (! is_array($rItem)) {
                                continue;
                            }
                            foreach ($rItem as $rKey => $rVal) {
                                if (is_string($rVal) && in_array($rKey, $this->textKeys, true) && trim($rVal) !== '') {
                                    $items[] = [
                                        'path'    => array_merge($elemPath, ['settings', $key, $rIdx, $rKey]),
                                        'text'    => $rVal,
                                        'is_html' => $this->looksLikeHtml($rVal),
                                    ];
                                }
                            }
                        }
                    }
                }
            }

            if (! empty($element['elements']) && is_array($element['elements'])) {
                $child = $this->collectStrings($element['elements'], array_merge($elemPath, ['elements']));
                $items = array_merge($items, $child['items']);
            }
        }

        return ['count' => count($items), 'items' => $items];
    }

    private function applyTranslations(array $elements, array $strings, string $langCode, string $apiKey, string $engine = 'deepl'): array
    {
        $plainIdxs = [];
        $htmlIdxs  = [];
        foreach ($strings as $i => $s) {
            if ($s['is_html']) {
                $htmlIdxs[] = $i;
            } else {
                $plainIdxs[] = $i;
            }
        }

        $errors        = [];
        $translatedMap = [];

        // ── Plain text batch ─────────────────────────────────────────────
        if (! empty($plainIdxs)) {
            $texts   = array_map(fn($i) => $strings[$i]['text'], $plainIdxs);
            $results = $this->translateTexts($texts, $langCode, false, $apiKey, $engine);
            if ($results === null) {
                $errors[] = 'Plain text batch failed: ' . ($this->lastApiError ?: 'unknown error');
            } else {
                foreach ($plainIdxs as $j => $origIdx) {
                    $translatedMap[$origIdx] = $results[$j];
                }
            }
        }

        // ── HTML batch (WYSIWYG) — batch first, per-string fallback ──────
        if (! empty($htmlIdxs)) {
            $htmlTexts = array_map(fn($i) => $strings[$i]['text'], $htmlIdxs);
            $results   = $this->translateTexts($htmlTexts, $langCode, true, $apiKey, $engine);

            if ($results !== null) {
                foreach ($htmlIdxs as $j => $origIdx) {
                    $translatedMap[$origIdx] = $results[$j];
                }
            } else {
                // Batch failed — retry each string individually
                foreach ($htmlIdxs as $j => $origIdx) {
                    $text   = $strings[$origIdx]['text'];
                    $single = $this->translateTexts([$text], $langCode, true, $apiKey, $engine);

                    if ($single === null) {
                        // Last resort: strip HTML, translate as plain text, re-wrap
                        $stripped = trim(wp_strip_all_tags($text));
                        if ($stripped !== '') {
                            $plain = $this->translateTexts([$stripped], $langCode, false, $apiKey, $engine);
                            if ($plain !== null) {
                                $single = [$this->rewrapHtml($text, $plain[0])];
                            }
                        }
                    }

                    if ($single !== null) {
                        $translatedMap[$origIdx] = $single[0];
                    } else {
                        $errors[] = 'HTML string failed (all retries) — ' . ($this->lastApiError ?: 'unknown error') . ': '
                            . mb_substr(wp_strip_all_tags($text), 0, 60) . '…';
                    }
                }
            }
        }

        if (! empty($errors) && empty($translatedMap)) {
            return [null, $errors];
        }

        $result = $elements;
        foreach ($strings as $idx => $entry) {
            $this->setByPath($result, $entry['path'], $translatedMap[$idx] ?? $entry['text']);
        }

        return [$result, $errors];
    }

    /**
     * Re-wrap translated plain text in the same block-level HTML as the original.
     * Handles single <p>, <div>, <li>; falls back to plain text for complex HTML.
     */
    private function rewrapHtml(string $original, string $translated): string
    {
        if (preg_match('/^\s*<(p|div|li|h[1-6])([^>]*)>(.*?)<\/\1>\s*$/is', $original, $m)) {
            return "<{$m[1]}{$m[2]}>{$translated}</{$m[1]}>";
        }
        return $translated;
    }

    private function setByPath(array &$data, array $path, string $value): void
    {
        $ref = &$data;
        foreach ($path as $key) {
            $ref = &$ref[$key];
        }
        $ref = $value;
    }

    /**
     * Dispatch to the configured translation engine. $langCode is the
     * Polylang language slug (e.g. 'nl') — resolved internally to whatever
     * format each engine needs (DeepL target code, or a human language name
     * for the Claude prompt).
     */
    private function translateTexts(array $texts, string $langCode, bool $isHtml, string $apiKey, string $engine): ?array
    {
        if ($engine === 'claude') {
            $langName = $this->langMap[$langCode]['name'] ?? $langCode;
            return $this->callClaude($texts, $langName, $isHtml, $apiKey);
        }

        $deeplCode = $this->langMap[$langCode]['deepl'] ?? strtoupper($langCode);
        return $this->callDeepL($texts, $deeplCode, $isHtml, $apiKey);
    }

    private function callClaude(array $texts, string $targetLangName, bool $isHtml, string $apiKey): ?array
    {
        $results = [];

        foreach (array_chunk($texts, 50) as $chunk) {
            $chunkResult = $this->callClaudeChunk($chunk, $targetLangName, $isHtml, $apiKey);
            if ($chunkResult === null) {
                return null;
            }
            $results = array_merge($results, $chunkResult);
        }

        return $results;
    }

    private function callClaudeChunk(array $texts, string $targetLangName, bool $isHtml, string $apiKey): ?array
    {
        if (empty($texts)) {
            return [];
        }

        $numbered = [];
        foreach ($texts as $i => $t) {
            $numbered[] = ($i + 1) . ". {$t}";
        }
        $joined  = implode("\n\n", $numbered);
        $count   = count($texts);

        $htmlNote = $isHtml
            ? 'Each item may contain HTML tags. Preserve all HTML tags, attributes, and structure exactly as-is — translate only the visible text content between tags. Do not translate or alter attribute values, class names, or tag names.'
            : 'These are plain text strings — do not add any HTML.';

        $prompt = "Translate each of the following {$count} numbered text items from English to {$targetLangName}. "
            . "{$htmlNote} Keep the same tone and meaning. Return exactly {$count} translations, "
            . "in the same order as the input, one per item, in the \"translations\" array.\n\n{$joined}";

        $body = [
            'model'      => 'claude-haiku-4-5',
            'max_tokens' => 8192,
            'messages'   => [
                ['role' => 'user', 'content' => $prompt],
            ],
            'output_config' => [
                'format' => [
                    'type'   => 'json_schema',
                    'schema' => [
                        'type'       => 'object',
                        'properties' => [
                            'translations' => [
                                'type'  => 'array',
                                'items' => ['type' => 'string'],
                            ],
                        ],
                        'required'             => ['translations'],
                        'additionalProperties' => false,
                    ],
                ],
            ],
        ];

        $response = wp_remote_post('https://api.anthropic.com/v1/messages', [
            'headers' => [
                'x-api-key'         => $apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ],
            'body'    => wp_json_encode($body),
            'timeout' => 90,
        ]);

        if (is_wp_error($response)) {
            $this->lastApiError = 'Claude network error: ' . $response->get_error_message();
            return null;
        }

        $statusCode = (int) wp_remote_retrieve_response_code($response);
        $rawBody    = wp_remote_retrieve_body($response);
        $parsed     = json_decode($rawBody, true);

        if ($statusCode !== 200) {
            $msg = $parsed['error']['message'] ?? $rawBody;
            $this->lastApiError = "Claude error (HTTP {$statusCode}): " . mb_substr((string) $msg, 0, 200);
            return null;
        }

        $textBlock = '';
        foreach ($parsed['content'] ?? [] as $block) {
            if (($block['type'] ?? '') === 'text') {
                $textBlock .= $block['text'];
            }
        }

        $decoded = json_decode($textBlock, true);
        if (! is_array($decoded) || empty($decoded['translations']) || ! is_array($decoded['translations'])) {
            $this->lastApiError = 'Claude returned unparseable response.';
            return null;
        }

        if (count($decoded['translations']) !== $count) {
            $this->lastApiError = 'Claude returned a mismatched number of translations ('
                . count($decoded['translations']) . " vs {$count} expected).";
            return null;
        }

        return $decoded['translations'];
    }

    private function callDeepL(array $texts, string $targetLang, bool $isHtml, string $apiKey): ?array
    {
        $apiUrl  = (substr($apiKey, -3) === ':fx')
            ? 'https://api-free.deepl.com/v2/translate'
            : 'https://api.deepl.com/v2/translate';
        $results = [];

        foreach (array_chunk($texts, 50) as $chunk) {
            $body = [];
            foreach ($chunk as $t) {
                $body[] = 'text=' . rawurlencode($t);
            }
            $body[] = 'target_lang=' . rawurlencode($targetLang);
            $body[] = 'source_lang=EN';
            $body[] = 'preserve_formatting=1';
            if ($isHtml) {
                $body[] = 'tag_handling=html';
            }

            $response = wp_remote_post($apiUrl, [
                'headers' => [
                    'Authorization' => 'DeepL-Auth-Key ' . $apiKey,
                    'Content-Type'  => 'application/x-www-form-urlencoded',
                ],
                'body'    => implode('&', $body),
                'timeout' => 60,
            ]);

            if (is_wp_error($response)) {
                $this->lastApiError = 'Network error: ' . $response->get_error_message();
                return null;
            }

            $statusCode = (int) wp_remote_retrieve_response_code($response);
            $rawBody    = wp_remote_retrieve_body($response);
            $parsed     = json_decode($rawBody, true);

            if ($statusCode === 456) {
                $this->lastApiError = 'DeepL quota exceeded (456) — check your plan usage at deepl.com.';
                return null;
            }

            if ($statusCode !== 200 || empty($parsed['translations'])) {
                $msg = $parsed['message'] ?? $rawBody;
                $this->lastApiError = "DeepL error (HTTP {$statusCode}): " . mb_substr((string) $msg, 0, 200);
                return null;
            }

            foreach ($parsed['translations'] as $t) {
                $results[] = $t['text'];
            }
        }

        return $results;
    }

    private function looksLikeHtml(string $text): bool
    {
        return (bool) preg_match('/<[a-z][^>]*>/i', $text);
    }
}

new XclearDeepLAdmin();
