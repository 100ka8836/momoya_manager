<?php
require '../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemId = $_POST['item_id'] ?? null;

    if (!$itemId) {
        echo json_encode(['success' => false, 'message' => '項目IDが指定されていません']);
        exit();
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM other_items WHERE id = :id");
        $stmt->execute([':id' => $itemId]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => '項目が削除されました']);
        } else {
            echo json_encode(['success' => false, 'message' => '指定された項目が見つかりませんでした']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => '削除中にエラーが発生しました: ' . $e->getMessage()]);
    }
    exit();
}
