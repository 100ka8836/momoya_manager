<?php
require 'includes/db.php';
require 'includes/header.php';

// データベースからグループ一覧を取得
$stmt = $pdo->query("SELECT id, name FROM `groups`");
$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>キャラクター登録</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <main>
        <nav>
            <a href="create_character_charaeno.php">キャラエノ</a><br><br>
            <a href="create_character_charasheet.php">キャラクター保管所</a><br><br>
            <a href="create_character_iachara.php">いあきゃら</a>
        </nav>

    </main>
</body>

</html>