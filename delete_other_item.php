<?php
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemId = $_POST['item_id'] ?? null;

    if (!$itemId) {
        echo json_encode(['success' => false, 'message' => '項目IDが指定されていません']);
        exit;
    }

    try {
        // データベースから項目を削除
        $stmt = $pdo->prepare("DELETE FROM other_items WHERE id = ?");
        $stmt->execute([$itemId]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => '項目が削除されました']);
        } else {
            echo json_encode(['success' => false, 'message' => '項目が見つかりませんでした']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => '削除中にエラーが発生しました: ' . $e->getMessage()]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => '無効なリクエスト']);
