<?php
header("Content-Type: application/json");

// データベース接続設定を含むファイルを読み込む
require_once '../includes/db.php';

try {
    // フロントエンドから送信されたJSONデータを取得
    $inputData = json_decode(file_get_contents("php://input"), true);

    if (!$inputData || !isset($inputData['updates'])) {
        echo json_encode(["success" => false, "message" => "不正なリクエストデータです"]);
        exit();
    }

    $updates = $inputData['updates']; // 更新データを取得
    $responseMessages = [];

    // データベースで更新処理
    foreach ($updates as $update) {
        $itemId = $update['item_id'] ?? null;
        $characterId = $update['character_id'] ?? null;
        $value = $update['value'] ?? null;

        // 必要なデータが揃っていない場合はスキップ
        if (!$itemId || !$characterId || $value === null) {
            $responseMessages[] = "データが不足しています: " . json_encode($update);
            continue;
        }

        // データベースクエリ（例: character_items テーブルを更新）
        $stmt = $pdo->prepare("
            UPDATE character_items
            SET value = :value
            WHERE item_id = :item_id AND character_id = :character_id
        ");
        $stmt->execute([
            ':value' => $value,
            ':item_id' => $itemId,
            ':character_id' => $characterId,
        ]);

        // 更新結果を記録
        if ($stmt->rowCount() > 0) {
            $responseMessages[] = "更新成功: item_id={$itemId}, character_id={$characterId}";
        } else {
            $responseMessages[] = "更新失敗: item_id={$itemId}, character_id={$characterId}";
        }
    }

    // 更新結果を返す
    echo json_encode(["success" => true, "messages" => $responseMessages]);
} catch (Exception $e) {
    // 例外が発生した場合のエラーレスポンス
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>