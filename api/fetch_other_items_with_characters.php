<?php
header("Content-Type: application/json");

// グループIDを取得
$groupId = $_GET['group_id'] ?? null;

if (!$groupId) {
    echo json_encode(["success" => false, "message" => "グループIDが指定されていません"]);
    exit();
}

// グループIDを使ったデータ取得処理
try {
    // 仮のデータ（実際にはデータベースクエリを使用）
    $items = [
        ["id" => "1", "item_name" => "項目1"],
        ["id" => "2", "item_name" => "項目2"]
    ];
    $characters = [
        ["id" => "1", "name" => "キャラクター1"],
        ["id" => "2", "name" => "キャラクター2"]
    ];

    echo json_encode([
        "success" => true,
        "items" => $items,
        "characters" => $characters
    ]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>