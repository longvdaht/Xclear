<?php
// One-off audit script — run via CLI: php check-translations.php
// Checks Polylang translation completeness for pages/products/product_category terms.

$mysqli = new mysqli('localhost', 'admin', '1', 'xclear2307');
if ($mysqli->connect_errno) {
    fwrite(STDERR, "DB connect failed: {$mysqli->connect_error}\n");
    exit(1);
}

$langs = ['en', 'nl', 'de', 'fr', 'es'];
$targetLangs = ['nl', 'de', 'fr', 'es'];

function fetchMeta(mysqli $db, int $postId, string $key): ?string
{
    $stmt = $db->prepare("SELECT meta_value FROM wp_postmeta WHERE post_id=? AND meta_key=? LIMIT 1");
    $stmt->bind_param('is', $postId, $key);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    return $res ? $res['meta_value'] : null;
}

function postInfo(mysqli $db, int $postId): ?array
{
    $stmt = $db->prepare("SELECT ID, post_type, post_status, post_title FROM wp_posts WHERE ID=?");
    $stmt->bind_param('i', $postId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc() ?: null;
}

// Recursively collect "meaningful" text leaf values from decoded Elementor JSON,
// regardless of key name — used as a rough untranslated-content detector.
function collectAllTexts($data, array &$out): void
{
    if (is_array($data)) {
        foreach ($data as $k => $v) {
            if (is_string($v) && is_string($k)) {
                $plain = trim(wp_strip_tags_lite($v));
                if (mb_strlen($plain) >= 12 && preg_match('/[a-zA-Z]{4,}/', $plain)) {
                    $out[] = $plain;
                }
            } elseif (is_array($v)) {
                collectAllTexts($v, $out);
            }
        }
    }
}

function wp_strip_tags_lite(string $s): string
{
    return trim(preg_replace('/<[^>]*>/', ' ', $s));
}

// Returns overlap ratio: fraction of $targetTexts strings that appear verbatim in $enTexts.
function overlapRatio(array $enTexts, array $targetTexts): float
{
    if (empty($targetTexts)) {
        return 0.0;
    }
    $enSet = array_flip($enTexts);
    $matches = 0;
    foreach ($targetTexts as $t) {
        if (isset($enSet[$t])) {
            $matches++;
        }
    }
    return $matches / count($targetTexts);
}

// ── Posts (page + product) ──────────────────────────────────────────────
$res = $mysqli->query("SELECT description FROM wp_term_taxonomy WHERE taxonomy='post_translations'");

$missingLang   = []; // group summary of missing target languages
$untranslated  = []; // target post has elementor data identical to EN source
$emptyContent  = []; // target post has NO elementor data at all
$checkedGroups = 0;

while ($row = $res->fetch_assoc()) {
    $map = @unserialize($row['description']);
    if (! is_array($map) || empty($map['en'])) {
        continue;
    }

    $enId = (int) $map['en'];
    $en   = postInfo($mysqli, $enId);
    if (! $en || ! in_array($en['post_type'], ['page', 'post', 'product', 'elementor_library'], true) || $en['post_status'] !== 'publish') {
        continue;
    }

    // elementor_library entries only matter if they're a header/footer/archive template
    if ($en['post_type'] === 'elementor_library') {
        $tplType = fetchMeta($mysqli, $enId, '_elementor_template_type');
        if (! in_array($tplType, ['header', 'footer', 'archive'], true)) {
            continue;
        }
    }

    $checkedGroups++;
    $missing = [];
    foreach ($targetLangs as $lc) {
        if (empty($map[$lc])) {
            $missing[] = $lc;
        }
    }
    if ($missing) {
        $missingLang[] = [
            'id'      => $enId,
            'type'    => $en['post_type'],
            'title'   => $en['post_title'],
            'missing' => $missing,
        ];
    }

    $enElementor = fetchMeta($mysqli, $enId, '_elementor_data');
    $enTexts = [];
    if ($enElementor) {
        $enData = json_decode($enElementor, true);
        if (is_array($enData)) {
            collectAllTexts($enData, $enTexts);
        }
    }

    foreach ($targetLangs as $lc) {
        if (empty($map[$lc])) {
            continue;
        }
        $tId = (int) $map[$lc];
        $t   = postInfo($mysqli, $tId);
        if (! $t) {
            continue;
        }
        $tElementor = fetchMeta($mysqli, $tId, '_elementor_data');

        if ($enElementor && ($tElementor === null || trim((string) $tElementor) === '')) {
            $emptyContent[] = ['id' => $enId, 'tid' => $tId, 'type' => $en['post_type'], 'title' => $en['post_title'], 'lang' => $lc];
            continue;
        }

        if (! $enElementor || $tElementor === null) {
            continue;
        }

        $tData = json_decode($tElementor, true);
        if (! is_array($tData)) {
            continue;
        }
        $tTexts = [];
        collectAllTexts($tData, $tTexts);

        if (empty($enTexts) && empty($tTexts)) {
            continue;
        }

        $ratio = overlapRatio($enTexts, $tTexts);
        if ($ratio >= 0.5 && count($tTexts) >= 3) {
            $untranslated[] = [
                'id' => $enId, 'tid' => $tId, 'type' => $en['post_type'], 'title' => $en['post_title'],
                'lang' => $lc, 'ratio' => round($ratio * 100), 'strings' => count($tTexts),
            ];
        }
    }
}

echo "=== POSTS/PAGES/PRODUCTS/HEADER-FOOTER-ARCHIVE: checked {$checkedGroups} EN groups ===\n\n";

echo "--- Missing translation entirely (no post created for that language) ---\n";
foreach ($missingLang as $m) {
    echo "[{$m['type']}] #{$m['id']} \"{$m['title']}\" missing: " . implode(', ', $m['missing']) . "\n";
}
echo "(" . count($missingLang) . " groups)\n\n";

echo "--- Elementor text mostly IDENTICAL to EN source (>=50% strings match, likely untranslated) ---\n";
foreach ($untranslated as $u) {
    echo "[{$u['type']}] EN #{$u['id']} \"{$u['title']}\" — {$u['lang']} post #{$u['tid']}: {$u['ratio']}% of {$u['strings']} strings match EN\n";
}
echo "(" . count($untranslated) . " posts)\n\n";

echo "--- No Elementor content at all on translated post ---\n";
foreach ($emptyContent as $e) {
    echo "[{$e['type']}] EN #{$e['id']} \"{$e['title']}\" — {$e['lang']} post #{$e['tid']} has NO _elementor_data\n";
}
echo "(" . count($emptyContent) . " posts)\n\n";

// ── Terms (product_category) ────────────────────────────────────────────
$res = $mysqli->query("SELECT description FROM wp_term_taxonomy WHERE taxonomy='term_translations'");

function termInfo(mysqli $db, int $termId): ?array
{
    $stmt = $db->prepare("
        SELECT t.term_id, t.name, tt.taxonomy, tt.description
        FROM wp_terms t JOIN wp_term_taxonomy tt ON tt.term_id = t.term_id
        WHERE t.term_id = ?
    ");
    $stmt->bind_param('i', $termId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc() ?: null;
}

$termMissing = [];
$termSameName = [];
$checkedTermGroups = 0;

while ($row = $res->fetch_assoc()) {
    $map = @unserialize($row['description']);
    if (! is_array($map) || empty($map['en'])) {
        continue;
    }

    $enId = (int) $map['en'];
    $en   = termInfo($mysqli, $enId);
    if (! $en || ! in_array($en['taxonomy'], ['product_category', 'project_category', 'category'], true)) {
        continue;
    }

    $checkedTermGroups++;
    $missing = [];
    foreach ($targetLangs as $lc) {
        if (empty($map[$lc])) {
            $missing[] = $lc;
        }
    }
    if ($missing) {
        $termMissing[] = ['id' => $enId, 'name' => $en['name'], 'tax' => $en['taxonomy'], 'missing' => $missing];
    }

    foreach ($targetLangs as $lc) {
        if (empty($map[$lc])) {
            continue;
        }
        $tId = (int) $map[$lc];
        $t   = termInfo($mysqli, $tId);
        if ($t && $t['name'] === $en['name']) {
            $termSameName[] = ['id' => $enId, 'name' => $en['name'], 'tax' => $en['taxonomy'], 'lang' => $lc, 'tid' => $tId];
        }
    }
}

echo "=== CATEGORIES (product_category / project_category / category): checked {$checkedTermGroups} EN groups ===\n\n";

echo "--- Missing translation entirely ---\n";
foreach ($termMissing as $m) {
    echo "[{$m['tax']}] #{$m['id']} \"{$m['name']}\" missing: " . implode(', ', $m['missing']) . "\n";
}
echo "(" . count($termMissing) . " groups)\n\n";

echo "--- Same name as EN (likely untranslated) ---\n";
foreach ($termSameName as $s) {
    echo "[{$s['tax']}] EN #{$s['id']} \"{$s['name']}\" — {$s['lang']} term #{$s['tid']} has identical name\n";
}
echo "(" . count($termSameName) . " terms)\n";
