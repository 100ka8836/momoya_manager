<?php
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $character_id = $_POST['character_id'] ?? null;
    $skill_name = $_POST['skill_name'] ?? null;
    $new_value = $_POST['new_value'] ?? null;
    $group_id = $_POST['group_id'] ?? null;

    if ($character_id && $skill_name && $new_value !== null && $group_id) {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM character_skills
            WHERE character_id = ? AND skill_name = ?
        ");
        $stmt->execute([$character_id, $skill_name]);
        $exists = $stmt->fetchColumn();

        try {
            $pdo->beginTransaction();
            if ($exists) {
                // 既存の行を更新
                $stmt = $pdo->prepare("
                    UPDATE character_skills
                    SET skill_value = ?
                    WHERE character_id = ? AND skill_name = ?
                ");
                $stmt->execute([$new_value, $character_id, $skill_name]);
            } else {
                // 新しい行を挿入
                $stmt = $pdo->prepare("
                    INSERT INTO character_skills (character_id, skill_name, skill_value)
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$character_id, $skill_name, $new_value]);
            }
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            die("エラーが発生しました: " . $e->getMessage());
        }

        // リダイレクト処理
        header("Location: group_page.php?group_id=" . urlencode($group_id));
        exit;
    } else {
        die("必要なデータが不足しています。");
    }
}
