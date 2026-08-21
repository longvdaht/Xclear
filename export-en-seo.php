<?php
/**
 * Standalone CSV export: all EN content (page, post, product, project,
 * product_category, project_category) with Yoast SEO focus keyphrase,
 * SEO title and meta description.
 *
 * Run on the server:
 *   php export-en-seo.php
 *
 * Output: xclear_en_seo.csv in the same directory as this script.
 */

$wpConfigPath = __DIR__ . '/wp-config.php';
$configSource = file_get_contents($wpConfigPath);

function extractDefine(string $source, string $name): string
{
    if (! preg_match('/define\(\s*[\'"]' . preg_quote($name, '/') . '[\'"]\s*,\s*[\'"](.*?)[\'"]\s*\)/', $source, $m)) {
        fwrite(STDERR, "Could not find $name in wp-config.php\n");
        exit(1);
    }

    return $m[1];
}

$dbName     = extractDefine($configSource, 'DB_NAME');
$dbUser     = extractDefine($configSource, 'DB_USER');
$dbPassword = extractDefine($configSource, 'DB_PASSWORD');
$dbHost     = extractDefine($configSource, 'DB_HOST');

// Table prefix (default 'wp_' if not found).
$tablePrefix = 'wp_';
if (preg_match('/\$table_prefix\s*=\s*[\'"](.*?)[\'"]/', $configSource, $m)) {
    $tablePrefix = $m[1];
}

// Polylang language term_id to export. Change this if your "en" language
// term_id differs (check wp_terms where slug = 'en' and a matching
// wp_term_taxonomy row with taxonomy = 'language').
$langTermId = 30;

$mysqli = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);
if ($mysqli->connect_error) {
    fwrite(STDERR, 'DB connection failed: ' . $mysqli->connect_error . "\n");
    exit(1);
}
$mysqli->set_charset('utf8mb4');

$p = $tablePrefix;

$postTypeSql = function (string $postType) use ($p, $langTermId): string {
    return "
        SELECT
            '{$postType}' AS type,
            p.post_title AS title,
            yi.permalink AS url,
            COALESCE(yi.primary_focus_keyword, '') AS focus_keyphrase,
            COALESCE(yi.title, '') AS seo_title,
            COALESCE(yi.description, '') AS meta_description
        FROM {$p}posts p
        JOIN {$p}term_relationships tr ON tr.object_id = p.ID
        JOIN {$p}term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
            AND tt.taxonomy = 'language' AND tt.term_id = {$langTermId}
        LEFT JOIN {$p}yoast_indexable yi ON yi.object_id = p.ID AND yi.object_type = 'post'
        WHERE p.post_type = '{$postType}' AND p.post_status = 'publish'
    ";
};

$taxonomySql = function (string $taxonomy) use ($p, $langTermId): string {
    return "
        SELECT
            '{$taxonomy}' AS type,
            t.name AS title,
            yi.permalink AS url,
            COALESCE(yi.primary_focus_keyword, '') AS focus_keyphrase,
            COALESCE(yi.title, '') AS seo_title,
            COALESCE(yi.description, '') AS meta_description
        FROM {$p}term_taxonomy tt
        JOIN {$p}terms t ON t.term_id = tt.term_id
        JOIN {$p}term_relationships tr ON tr.object_id = tt.term_taxonomy_id
        JOIN {$p}term_taxonomy lang ON lang.term_taxonomy_id = tr.term_taxonomy_id
            AND lang.taxonomy = 'language' AND lang.term_id = {$langTermId}
        LEFT JOIN {$p}yoast_indexable yi ON yi.object_id = t.term_id AND yi.object_type = 'term'
        WHERE tt.taxonomy = '{$taxonomy}'
    ";
};

$sql = implode(' UNION ALL ', [
    $postTypeSql('page'),
    $postTypeSql('post'),
    $postTypeSql('product'),
    $postTypeSql('project'),
    $taxonomySql('product_category'),
    $taxonomySql('project_category'),
]) . ' ORDER BY type, title';

$result = $mysqli->query($sql);
if ($result === false) {
    fwrite(STDERR, 'Query failed: ' . $mysqli->error . "\n");
    exit(1);
}

$outPath = __DIR__ . '/xclear_en_seo.csv';
$fp = fopen($outPath, 'w');

fputcsv($fp, ['Type', 'Title', 'URL', 'Focus Keyphrase', 'SEO Title', 'Meta Description']);

$rowCount = 0;
while ($row = $result->fetch_assoc()) {
    fputcsv($fp, [
        $row['type'],
        $row['title'],
        $row['url'],
        $row['focus_keyphrase'],
        $row['seo_title'],
        $row['meta_description'],
    ]);
    $rowCount++;
}

fclose($fp);
$mysqli->close();

echo "Done. Wrote {$rowCount} rows to {$outPath}\n";
