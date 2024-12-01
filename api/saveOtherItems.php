<?php
header("Content-Type: application/json");

// データベース接続設定
require_once __DIR__ . '/../includes/db.php';


try {
    $inputData = json_decode(file_get_contents("php://input"), true);

    if (!$inputData || !isset($inputData['updates'])) {
        echo json_encode(["success" => false, "message" => "不正なリクエストです"]);
        exit();
    }

    $updates = $inputData['updates'];
    foreach ($updates as $update) {
        $itemId = $update['item_id'];
        $values = $update['values'];

        // `other_items` または関連テーブルを更新
        foreach ($values as $characterId => $value) {
            $stmt = $pdo->prepare("
                UPDATE character_other_items
                SET value = :value
                WHERE item_id = :item_id AND character_id = :character_id
            ");
            $stmt->execute([
                ':value' => $value,
                ':item_id' => $itemId,
                ':character_id' => $characterId,
            ]);
        }
    }

    echo json_encode(["success" => true]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>