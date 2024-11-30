<?php
require 'includes/db.php';

$groupId = $_GET['group_id'] ?? null;

if (!$groupId) {
    echo json_encode(['success' => false, 'message' => 'グループIDが指定されていません']);
    exit;
}

try {
    // キャラクター名を取得
    $stmt = $pdo->prepare("SELECT id, name FROM characters WHERE group_id = ?");
    $stmt->execute([$groupId]);
    $characters = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // その他の項目を取得
    $stmt = $pdo->prepare("SELECT id, item_name FROM other_items WHERE group_id = ?");
    $stmt->execute([$groupId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 結果を返す
    echo json_encode(['success' => true, 'characters' => $characters, 'items' => $items]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'データ取得に失敗しました: ' . $e->getMessage()]);
}
