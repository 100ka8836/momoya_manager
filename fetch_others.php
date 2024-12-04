<?php
require 'includes/db.php';

$group_id = $_GET['group_id'] ?? null;

if (!$group_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'グループIDが指定されていません。']);
    exit;
}

// キャラクター情報を取得
$stmt = $pdo->prepare("
    SELECT id, name
    FROM characters
    WHERE group_id = ?
");
$stmt->execute([$group_id]);
$characters = $stmt->fetchAll(PDO::FETCH_ASSOC);

// その他タブのデータを取得
$stmt = $pdo->prepare("
    SELECT item_id, item_name, character_id, value
    FROM other_items
    WHERE group_id = ?
");
$stmt->execute([$group_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// データを整形してレスポンス
$response = [
    'success' => true,
    'characters' => $characters,
    'items' => [],
];

// アイテムの値をキャラクターごとに整理
$itemValues = [];

// アイテムごとにキャラクターの値を整理
foreach ($items as $item) {
    if (!isset($itemValues[$item['item_id']])) {
        $itemValues[$item['item_id']] = [
            'item_id' => $item['item_id'],
            'item_name' => $item['item_name'],
            'values' => [],
        ];
    }
    $itemValues[$item['item_id']]['values'][$item['character_id']] = $item['value'];
}

// アイテムとキャラクターの値を取得
$stmt = $pdo->prepare("
    SELECT item_id, character_id, value
    FROM character_item_values
    WHERE item_id IN (SELECT item_id FROM other_items WHERE group_id = ?)
");
$stmt->execute([$group_id]);
$characterItemValues = $stmt->fetchAll(PDO::FETCH_ASSOC);

// アイテムの値をキャラクターごとに整理
foreach ($characterItemValues as $value) {
    if (!isset($itemValues[$value['item_id']]['values'])) {
        $itemValues[$value['item_id']]['values'] = [];
    }
    $itemValues[$value['item_id']]['values'][$value['character_id']] = $value['value'];
}

// アイテムをレスポンスに追加
$response['items'] = array_values($itemValues);

// JSONとして返す
echo json_encode($response);
