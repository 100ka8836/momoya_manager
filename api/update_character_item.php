<?php
// データベース接続
$pdo = new PDO("mysql:host=localhost;dbname=momoya_manager;charset=utf8mb4", "username", "password");

$data = json_decode(file_get_contents("php://input"), true);

foreach ($data['updates'] as $update) {
    $character_id = $update['character_id'];
    $item_id = $update['item_id'];
    $value = $update['value'];

    // 挿入または更新処理
    $stmt = $pdo->prepare("
        INSERT INTO character_item_values (character_id, item_id, value)
        VALUES (:character_id, :item_id, :value)
        ON DUPLICATE KEY UPDATE value = :value
    ");
    $stmt->execute([
        ':character_id' => $character_id,
        ':item_id' => $item_id,
        ':value' => $value
    ]);
}

echo json_encode(['success' => true]);
?>