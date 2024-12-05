<?php
require 'includes/db.php';

// POSTリクエストかつ必要なデータがある場合
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_id'], $_POST['group_id'])) {
    $categoryId = intval($_POST['category_id']);
    $groupId = intval($_POST['group_id']);

    try {
        // カテゴリを削除
        $stmt = $pdo->prepare("DELETE FROM Categories WHERE id = ? AND group_id = ?");
        $stmt->execute([$categoryId, $groupId]);

        // 成功時にリダイレクト
        header("Location: group_page.php?group_id=$groupId&success=1");
        exit;
    } catch (PDOException $e) {
        die('カテゴリ削除中にエラーが発生しました: ' . $e->getMessage());
    }
} else {
    die('無効なリクエストです。');
}
?>