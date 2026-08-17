<?php
/**
 * DeepL Auto-Translate for Polylang + Elementor
 *
 * Run from the WordPress root directory:
 *
 *   # Translate all English pages/posts to all configured languages
 *   wp eval-file wp-content/themes/xclear/inc/translate-tool/deepl-translate.php -- --api_key=YOUR_KEY
 *
 *   # Translate a single post
 *   wp eval-file wp-content/themes/xclear/inc/translate-tool/deepl-translate.php -- --api_key=YOUR_KEY --post_id=123
 *
 *   # Preview what would be translated (no changes saved)
 *   wp eval-file wp-content/themes/xclear/inc/translate-tool/deepl-translate.php -- --api_key=YOUR_KEY --dry_run
 *
 *   # Translate to specific languages only
 *   wp eval-file wp-content/themes/xclear/inc/translate-tool/deepl-translate.php -- --api_key=YOUR_KEY --langs=nl,de
 *
 *   # Also translate post titles
 *   wp eval-file wp-content/themes/xclear/inc/translate-tool/deepl-translate.php -- --api_key=YOUR_KEY --translate_titles
 */

if (! defined('ABSPATH') || ! class_exists('WP_CLI')) {
    die("Run via WP-CLI: wp eval-file <path-to-this-file> -- --api_key=YOUR_KEY\n");
}

// ── Parse args ────────────────────────────────────────────────────────────────
// WP-CLI makes $assoc_args available when using eval-file
$opt = array_merge([
    'api_key'          => '',
    'post_id'          => 0,
    'langs'            => 'nl,de,fr,es',
    'dry_run'          => false,
    'translate_titles' => false,
], $assoc_args ?? []);

$opt['post_id']          = (int) $opt['post_id'];
$opt['dry_run']          = isset($assoc_args['dry_run']);
$opt['translate_titles'] = isset($assoc_args['translate_titles']);

if (empty($opt['api_key'])) {
    WP_CLI::error('Missing required flag: --api_key=YOUR_DEEPL_KEY');
    return;
}

// DeepL language codes keyed by Polylang locale code
$LANG_MAP = [
    'nl' => 'NL',
    'de' => 'DE',
    'fr' => 'FR',
    'es' => 'ES',
    'it' => 'IT',
    'pt' => 'PT-PT',
    'pl' => 'PL',
];

$targetLangs = [];
foreach (explode(',', $opt['langs']) as $code) {
    $code = trim(strtolower($code));
    if (isset($LANG_MAP[$code])) {
        $targetLangs[$code] = $LANG_MAP[$code];
    } else {
        WP_CLI::warning("Unknown lang code '{$code}' — skipping. Supported: " . implode(', ', array_keys($LANG_MAP)));
    }
}

if (empty($targetLangs)) {
    WP_CLI::error('No valid target languages specified.');
    return;
}

// ── Translator ────────────────────────────────────────────────────────────────

class XclearDeepLTranslator
{
    private string $apiKey;
    private string $apiUrl;
    private array  $targetLangs;
    private bool   $dryRun;
    private bool   $translateTitles;

    /**
     * Elementor settings keys whose string values should be translated.
     * Non-listed keys (colors, sizes, IDs, URLs, selectors) are left untouched.
     */
    private array $textKeys = [
        // ── Standard Elementor ──────────────────────────────────────────
        'title', 'text', 'description', 'editor', 'content', 'caption',
        'placeholder', 'button_text', 'label', 'heading', 'prefix', 'suffix',
        'inner_text', 'html', 'tab_title', 'tab_content',
        'accordion_title', 'accordion_content',
        // ── Custom xclear widgets ────────────────────────────────────────
        'badge_text', 'sub_heading', 'paragraph',
        'table_title', 'table_description', 'table_columns', 'table_rows',
        'custom_description', 'main_heading_text',
    ];

    public function __construct(string $apiKey, array $targetLangs, bool $dryRun, bool $translateTitles)
    {
        $this->apiKey          = $apiKey;
        // Free keys end with ':fx'; paid keys use the main API host
        $this->apiUrl          = (substr($apiKey, -3) === ':fx')
            ? 'https://api-free.deepl.com/v2/translate'
            : 'https://api.deepl.com/v2/translate';
        $this->targetLangs     = $targetLangs;
        $this->dryRun          = $dryRun;
        $this->translateTitles = $translateTitles;
    }

    // ── Public entry point ───────────────────────────────────────────────────

    public function run(int $specificPostId = 0): void
    {
        if (! function_exists('pll_get_post_translations')) {
            WP_CLI::error('Polylang is not active or not loaded.');
            return;
        }

        if ($specificPostId > 0) {
            $post = get_post($specificPostId);
            $posts = $post ? [$post] : [];
        } else {
            $posts = get_posts([
                'post_type'   => ['page', 'post'],
                'post_status' => 'publish',
                'numberposts' => -1,
                'lang'        => 'en',
            ]);
        }

        if (empty($posts)) {
            WP_CLI::warning('No published English posts/pages found.');
            return;
        }

        WP_CLI::log(sprintf(
            'Processing %d post(s) → languages: %s%s',
            count($posts),
            implode(', ', array_keys($this->targetLangs)),
            $this->dryRun ? ' [DRY RUN]' : ''
        ));
        WP_CLI::log('');

        foreach ($posts as $post) {
            if ($post) {
                $this->translatePost($post);
            }
        }

        WP_CLI::success('All done.');
    }

    // ── Per-post processing ───────────────────────────────────────────────────

    private function translatePost(\WP_Post $post): void
    {
        WP_CLI::log("▸ [{$post->ID}] {$post->post_title}");

        $elementorData = get_post_meta($post->ID, '_elementor_data', true);
        if (empty($elementorData)) {
            WP_CLI::log("  (no Elementor data — skipping Elementor content)");
        }

        $sourceElements = ! empty($elementorData)
            ? json_decode($elementorData, true)
            : null;

        if (! empty($elementorData) && json_last_error() !== JSON_ERROR_NONE) {
            WP_CLI::warning("  Invalid Elementor JSON — skipping this post entirely.");
            return;
        }

        $translations = pll_get_post_translations($post->ID);

        foreach ($this->targetLangs as $polylangCode => $deeplCode) {
            $translatedPostId = $translations[$polylangCode] ?? 0;
            if (! $translatedPostId || $translatedPostId === $post->ID) {
                WP_CLI::log("  [{$polylangCode}] No translated post found — create it in Polylang first.");
                continue;
            }

            WP_CLI::log("  [{$polylangCode}] → post #{$translatedPostId}");

            // 1. Translate Elementor content
            if ($sourceElements !== null) {
                $strings = $this->collectStrings($sourceElements);
                WP_CLI::log(sprintf("    %d translatable string(s) in Elementor data.", count($strings)));

                if (! empty($strings)) {
                    $translatedElements = $this->applyTranslations($sourceElements, $strings, $deeplCode);
                    if ($translatedElements === null) {
                        WP_CLI::warning("    Translation failed — skipping this language.");
                        continue;
                    }

                    if (! $this->dryRun) {
                        $json = wp_slash(json_encode($translatedElements, JSON_UNESCAPED_UNICODE));
                        update_post_meta($translatedPostId, '_elementor_data', $json);
                        delete_post_meta($translatedPostId, '_elementor_css');
                        WP_CLI::log("    ✓ Elementor data saved.");
                    } else {
                        WP_CLI::log("    [DRY RUN] Would save Elementor data to post #{$translatedPostId}.");
                    }
                }
            }

            // 2. Optionally translate post title
            if ($this->translateTitles) {
                $translatedTitle = $this->callDeepL([$post->post_title], $deeplCode, false);
                if ($translatedTitle !== null && ! empty($translatedTitle[0])) {
                    if (! $this->dryRun) {
                        wp_update_post(['ID' => $translatedPostId, 'post_title' => $translatedTitle[0]]);
                        WP_CLI::log("    ✓ Title translated: \"{$translatedTitle[0]}\"");
                    } else {
                        WP_CLI::log("    [DRY RUN] Title would become: \"{$translatedTitle[0]}\"");
                    }
                }
            }
        }

        WP_CLI::log('');
    }

    // ── String collection ─────────────────────────────────────────────────────

    /**
     * Walk Elementor elements array and collect translatable strings with paths.
     *
     * @return array<int, array{path: list<string|int>, text: string, is_html: bool}>
     */
    private function collectStrings(array $elements, array $basePath = []): array
    {
        $found = [];

        foreach ($elements as $idx => $element) {
            $elemPath = array_merge($basePath, [$idx]);

            if (! empty($element['settings']) && is_array($element['settings'])) {
                foreach ($element['settings'] as $key => $val) {
                    if (is_string($val) && in_array($key, $this->textKeys, true) && trim($val) !== '') {
                        $found[] = [
                            'path'    => array_merge($elemPath, ['settings', $key]),
                            'text'    => $val,
                            'is_html' => $this->looksLikeHtml($val),
                        ];
                    } elseif (is_array($val)) {
                        // Repeater field: array of sub-items
                        foreach ($val as $rIdx => $rItem) {
                            if (! is_array($rItem)) {
                                continue;
                            }
                            foreach ($rItem as $rKey => $rVal) {
                                if (is_string($rVal) && in_array($rKey, $this->textKeys, true) && trim($rVal) !== '') {
                                    $found[] = [
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
                $found = array_merge($found, $child);
            }
        }

        return $found;
    }

    // ── Translation application ───────────────────────────────────────────────

    /**
     * Translate all collected strings and write them back into a copy of $elements.
     */
    private function applyTranslations(array $elements, array $strings, string $deeplCode): ?array
    {
        // Split into plain-text and HTML batches
        $plainIdxs = [];
        $htmlIdxs  = [];
        foreach ($strings as $i => $s) {
            if ($s['is_html']) {
                $htmlIdxs[] = $i;
            } else {
                $plainIdxs[] = $i;
            }
        }

        $translatedMap = [];

        if (! empty($plainIdxs)) {
            $texts   = array_map(fn($i) => $strings[$i]['text'], $plainIdxs);
            $results = $this->callDeepL($texts, $deeplCode, false);
            if ($results === null) {
                return null;
            }
            foreach ($plainIdxs as $j => $origIdx) {
                $translatedMap[$origIdx] = $results[$j];
            }
        }

        if (! empty($htmlIdxs)) {
            $texts   = array_map(fn($i) => $strings[$i]['text'], $htmlIdxs);
            $results = $this->callDeepL($texts, $deeplCode, true);
            if ($results === null) {
                return null;
            }
            foreach ($htmlIdxs as $j => $origIdx) {
                $translatedMap[$origIdx] = $results[$j];
            }
        }

        // Write translations back using paths
        $result = $elements;
        foreach ($strings as $idx => $entry) {
            $this->setByPath($result, $entry['path'], $translatedMap[$idx] ?? $entry['text']);
        }

        return $result;
    }

    /**
     * Set a value deep inside a nested array using a list of keys.
     */
    private function setByPath(array &$data, array $path, string $value): void
    {
        $ref = &$data;
        foreach ($path as $key) {
            $ref = &$ref[$key];
        }
        $ref = $value;
    }

    // ── DeepL API ─────────────────────────────────────────────────────────────

    /**
     * Translate texts via DeepL API (batched to 50 strings per request).
     *
     * @param  string[] $texts
     * @return string[]|null  Translated strings in the same order, or null on error.
     */
    private function callDeepL(array $texts, string $targetLang, bool $isHtml): ?array
    {
        $results = [];
        $chunks  = array_chunk($texts, 50, true);

        foreach ($chunks as $chunk) {
            $bodyParts = [];
            foreach ($chunk as $text) {
                $bodyParts[] = 'text=' . rawurlencode($text);
            }
            $bodyParts[] = 'target_lang=' . rawurlencode($targetLang);
            $bodyParts[] = 'source_lang=EN';
            $bodyParts[] = 'preserve_formatting=1';
            if ($isHtml) {
                $bodyParts[] = 'tag_handling=html';
            }

            $response = wp_remote_post($this->apiUrl, [
                'headers' => [
                    'Authorization' => 'DeepL-Auth-Key ' . $this->apiKey,
                    'Content-Type'  => 'application/x-www-form-urlencoded',
                ],
                'body'    => implode('&', $bodyParts),
                'timeout' => 60,
            ]);

            if (is_wp_error($response)) {
                WP_CLI::warning('    DeepL request failed: ' . $response->get_error_message());
                return null;
            }

            $statusCode = (int) wp_remote_retrieve_response_code($response);
            $rawBody    = wp_remote_retrieve_body($response);

            if ($statusCode === 456) {
                WP_CLI::error('DeepL quota exceeded. Upgrade plan or wait for monthly reset.', false);
                return null;
            }

            if ($statusCode !== 200) {
                WP_CLI::warning("    DeepL HTTP {$statusCode}: {$rawBody}");
                return null;
            }

            $parsed = json_decode($rawBody, true);
            if (empty($parsed['translations'])) {
                WP_CLI::warning('    DeepL returned no translations.');
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

// ── Run ───────────────────────────────────────────────────────────────────────

$translator = new XclearDeepLTranslator(
    $opt['api_key'],
    $targetLangs,
    $opt['dry_run'],
    $opt['translate_titles']
);

$translator->run($opt['post_id']);
