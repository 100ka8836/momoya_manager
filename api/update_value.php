<?php
require '../includes/db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$updates = $data['updates'] ?? [];

if (empty($updates)) {
    echo json_encode(['success' => false, 'message' => '更新データがありません']);
    exit;
}

try {
    foreach ($updates as $update) {
        // まず、対象のアイテムが存在するか確認
        $stmt = $pdo->prepare("
            SELECT * FROM other_items
            WHERE item_id = :item_id AND group_id = :group_id
        ");
        $stmt->execute([
            'item_id' => $update['item_id'],
            'group_id' => $update['group_id'],
        ]);

        if ($stmt->rowCount() === 0) {
            continue; // 存在しない場合スキップ
        }

        // 値を更新
        $stmt = $pdo->prepare("
            UPDATE other_items
            SET value = :value
            WHERE item_id = :item_id AND character_id = :character_id
        ");
        $stmt->execute([
            'value' => $update['value'],
            'item_id' => $update['item_id'],
            'character_id' => $update['character_id'],
        ]);
    }
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    error_log('Database Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'データベースエラーが発生しました']);
}
