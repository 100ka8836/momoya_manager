<?php
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    file_put_contents('debug.log', var_export($data, true), FILE_APPEND);


    if (!isset($data['data']) || !is_array($data['data'])) {
        echo json_encode(['success' => false, 'message' => '無効なデータ形式']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        foreach ($data['data'] as $row) {
            $itemId = $row['itemId'];
            $values = $row['values'];

            foreach ($values as $characterId => $value) {
                // 既存のレコードがあるか確認
                $stmt = $pdo->prepare("
                    SELECT COUNT(*) 
                    FROM character_other_items 
                    WHERE item_id = ? AND character_id = ?
                ");
                $stmt->execute([$itemId, $characterId]);
                $exists = $stmt->fetchColumn();

                if ($exists) {
                    // 更新
                    $stmt = $pdo->prepare("
                        UPDATE character_other_items 
                        SET value = ? 
                        WHERE item_id = ? AND character_id = ?
                    ");
                    $stmt->execute([$value, $itemId, $characterId]);
                } else {
                    // 挿入
                    $stmt = $pdo->prepare("
                        INSERT INTO character_other_items (item_id, character_id, value) 
                        VALUES (?, ?, ?)
                    ");
                    $stmt->execute([$itemId, $characterId, $value]);
                }
            }
        }

        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}


foreach ($updates as $update) {
    $itemId = $update['itemId']; // 項目ID
    $valuesArray = $update['values']; // キャラクターIDをキーとした値

    foreach ($valuesArray as $characterId => $value) {
        // キャラクターIDが数値であることを確認
        if (!is_numeric($characterId)) {
            continue;
        }

        // データベースに存在するか確認
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM character_other_items 
            WHERE item_id = ? AND character_id = ?
        ");
        $stmt->execute([$itemId, $characterId]);
        $exists = $stmt->fetchColumn();

        if ($exists) {
            // 更新
            $stmt = $pdo->prepare("
                UPDATE character_other_items 
                SET value = ? 
                WHERE item_id = ? AND character_id = ?
            ");
            $stmt->execute([$value, $itemId, $characterId]);
        } else {
            // 挿入
            $stmt = $pdo->prepare("
                INSERT INTO character_other_items (item_id, character_id, value) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$itemId, $characterId, $value]);
        }
    }
}
