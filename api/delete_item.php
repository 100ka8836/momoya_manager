<?php
require '../includes/db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$item_id = $data['item_id'] ?? null;

if (!$item_id) {
    echo json_encode(['success' => false, 'message' => '項目IDが指定されていません']);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM other_items WHERE item_id = :item_id");
    $stmt->execute(['item_id' => $item_id]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    error_log('Database Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'データベースエラーが発生しました']);
}
