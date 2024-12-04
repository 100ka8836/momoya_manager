<?php
require '../includes/db.php';

// エラーメッセージをJSONとして処理する
header('Content-Type: application/json');

// リクエストをJSONとして解析
$data = json_decode(file_get_contents('php://input'), true);

// 必要なデータが存在するか確認
$group_id = $data['group_id'] ?? null;
$item_name = $data['item_name'] ?? null;

if (!$group_id || !$item_name) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'グループIDまたは項目名が指定されていません。']);
    exit;
}

try {
    // データベースに新しい項目を追加
    $stmt = $pdo->prepare("
        INSERT INTO other_items (group_id, item_name, character_id, value) 
        VALUES (:group_id, :item_name, NULL, NULL)
    ");
    $stmt->execute(['group_id' => $group_id, 'item_name' => $item_name]);

    // 新しく追加された項目の情報を取得
    $new_item_id = $pdo->lastInsertId();
    $new_item = [
        'item_id' => $new_item_id,
        'item_name' => $item_name,
    ];

    echo json_encode(['success' => true, 'new_item' => $new_item]);

} catch (PDOException $e) {
    // エラーログを記録し、クライアントにエラーメッセージを返す
    error_log('Database Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'データベースエラーが発生しました。']);
}
