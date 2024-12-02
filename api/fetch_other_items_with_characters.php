<?php
require '../includes/db.php';

header("Content-Type: application/json");

$groupId = $_GET['group_id'] ?? null;

if (!$groupId) {
    echo json_encode(["success" => false, "message" => "グループIDが指定されていません"]);
    exit();
}

try {
    // キャラクター情報を取得
    $characterStmt = $pdo->prepare("SELECT id, name FROM characters WHERE group_id = ?");
    $characterStmt->execute([$groupId]);
    $characters = $characterStmt->fetchAll(PDO::FETCH_ASSOC);

    // 項目情報を取得
    $itemStmt = $pdo->prepare("SELECT id, item_name FROM other_items WHERE group_id = ?");
    $itemStmt->execute([$groupId]);
    $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "characters" => $characters,
        "items" => $items
    ]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>