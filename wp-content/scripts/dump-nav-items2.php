<?php
$mysqli = new mysqli('localhost', 'admin', '1', 'xclear2307');
function fetchMeta(mysqli $db, int $postId, string $key): ?string {
    $stmt = $db->prepare("SELECT meta_value FROM wp_postmeta WHERE post_id=? AND meta_key=? LIMIT 1");
    $stmt->bind_param('is', $postId, $key);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    return $res ? $res['meta_value'] : null;
}
function findWidget(array $els, string $type, array &$out) {
    foreach ($els as $el) {
        if (($el['widgetType'] ?? '') === $type) $out[] = $el;
        if (!empty($el['elements'])) findWidget($el['elements'], $type, $out);
    }
}
foreach (['en'=>303, 'nl'=>2544] as $lang => $id) {
    $json = fetchMeta($mysqli, $id, '_elementor_data');
    $data = json_decode($json, true);
    $found = [];
    findWidget($data, 'xclear_nav_dropdown_widget', $found);
    echo "=== $lang (post #$id) ===\n";
    foreach ($found as $w) {
        $items = $w['settings']['items'] ?? [];
        foreach ($items as $item) {
            echo "  label=\"" . ($item['item_title'] ?? '?') . "\" link_type=" . ($item['link_type'] ?? '(default:page)') . " link_page=" . ($item['link_page'] ?? '') . " link_custom=" . ($item['link_custom'] ?? '') . "\n";
        }
    }
}
