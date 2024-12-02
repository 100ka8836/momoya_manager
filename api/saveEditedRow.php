<?php
header("Content-Type: application/json");
require_once '../includes/db.php';

try {
    // クライアントからのJSONデータを取得
    $inputData = json_decode(file_get_contents("php://input"), true);

    if (!$inputData || !isset($inputData['updates']) || !is_array($inputData['updates'])) {
        echo json_encode(["success" => false, "message" => "不正なリクエストデータです"]);
        exit();
    }

    $updates = $inputData['updates']; // 更新データを取得
    $responseMessages = [];          // 各更新の結果を記録

    // トランザクションを開始
    $pdo->beginTransaction();

    foreach ($updates as $update) {
        $itemId = $update['item_id'] ?? null;
        $characterId = $update['character_id'] ?? null;
        $values = $update['values'] ?? null;

        // 必須データが不足している場合はスキップ
        if (!$itemId || !$characterId || !isset($values) || empty($values)) {
            $responseMessages[] = "データが不足しています: " . json_encode($update);
            continue;
        }

        // アイテムの名前が変更されている場合の処理
        if (isset($update['item_name'])) {
            $itemName = $update['item_name'];

            // アイテムの保存または更新
            $stmt = $pdo->prepare("
                INSERT INTO other_items (id, item_name)
                VALUES (:item_id, :item_name)
                ON DUPLICATE KEY UPDATE item_name = :item_name
            ");
            $stmt->execute([
                ':item_id' => $itemId,
                ':item_name' => $itemName,
            ]);
        }

        // キャラクターとアイテムに関連する値の保存または更新
        foreach ($values as $value) {
            // 空文字や "-" は無視する
            if (trim($value) !== "" && $value !== "-") {
                $stmt = $pdo->prepare("
                    INSERT INTO character_other_items (item_id, character_id, value)
                    VALUES (:item_id, :character_id, :value)
                    ON DUPLICATE KEY UPDATE value = :value
                ");
                $stmt->execute([
                    ':item_id' => $itemId,
                    ':character_id' => $characterId,
                    ':value' => $value,
                ]);
            }
        }

        // 実行結果に基づくレスポンスメッセージ
        if ($stmt->rowCount() > 0) {
            $responseMessages[] = "更新成功: item_id={$itemId}, character_id={$characterId}";
        } else {
            $responseMessages[] = "更新失敗: item_id={$itemId}, character_id={$characterId}";
        }
    }




    // トランザクションをコミット
    $pdo->commit();

    // 成功レスポンス
    echo json_encode(["success" => true, "messages" => $responseMessages]);
} catch (Exception $e) {
    // トランザクションをロールバック
    $pdo->rollBack();

    // 例外処理
    echo json_encode(["success" => false, "message" => "サーバーエラー: " . $e->getMessage()]);
    error_log("トランザクションエラー: " . $e->getMessage());
}
?>