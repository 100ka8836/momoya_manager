<?php
header("Content-Type: application/json");

// データベース接続
require_once __DIR__ . '/../includes/db.php';


try {
    $groupId = $_GET['group_id'] ?? null;

    if (!$groupId) {
        echo json_encode(["success" => false, "message" => "グループIDが指定されていません"]);
        exit();
    }

    // キャラクター情報を取得
    $characterStmt = $pdo->prepare("
        SELECT id, name
        FROM characters
        WHERE group_id = :group_id
    ");
    $characterStmt->execute([':group_id' => $groupId]);
    $characters = $characterStmt->fetchAll(PDO::FETCH_ASSOC);

    // 項目と値を取得
    $itemsStmt = $pdo->prepare("
        SELECT 
            o.id AS item_id, 
            o.item_name, 
            coi.character_id, 
            coi.value
        FROM other_items o
        LEFT JOIN character_other_items coi ON o.id = coi.item_id
        WHERE o.group_id = :group_id
    ");
    $itemsStmt->execute([':group_id' => $groupId]);

    $items = [];
    while ($row = $itemsStmt->fetch(PDO::FETCH_ASSOC)) {
        $itemId = $row['item_id'];

        if (!isset($items[$itemId])) {
            $items[$itemId] = [
                'item_id' => $itemId,
                'item_name' => $row['item_name'],
                'values' => [],
            ];
        }

        if ($row['character_id']) {
            $items[$itemId]['values'][$row['character_id']] = $row['value'];
        }
    }

    echo json_encode([
        "success" => true,
        "characters" => $characters,
        "items" => array_values($items),
    ]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>