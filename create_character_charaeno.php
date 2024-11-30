<?php
require 'includes/db.php';
require 'includes/header.php';

$message = ''; // 登録完了メッセージを格納

// POSTリクエストの処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $formType = $_POST['form_type'] ?? null;
        $groupId = $_POST['group_id'] ?? null;
        $sourceUrl = $_POST['charaeno_url'] ?? null;

        if (!$formType || !$groupId || !$sourceUrl) {
            throw new Exception('必要なデータが不足しています。');
        }

        // データ送信をcreate_character_handler.phpに委託
        $response = @file_get_contents(
            'http://localhost/momoya_character_manager/create_character_handler.php',
            false,
            stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => 'Content-Type: application/x-www-form-urlencoded',
                    'content' => http_build_query($_POST)
                ]
            ])
        );

        if ($response === false) {
            throw new Exception('リクエストが失敗しました: ' . error_get_last()['message']);
        }

        $responseData = json_decode($response, true);

        if (!$responseData || !isset($responseData['success'])) {
            throw new Exception('ハンドラーのレスポンスが無効です。');
        }

        if ($responseData['success']) {
            $message = '登録できました！'; // 成功時メッセージ
        } else {
            $message = $responseData['message'] ?? '登録に失敗しました。';
        }
    } catch (Exception $e) {
        $message = 'エラー: ' . $e->getMessage();
    }
}

// データベースからグループ一覧を取得
$stmt = $pdo->query("SELECT id, name FROM groups");
$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>キャラエノ登録</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <main>
        <h2>キャラエノから登録</h2>
        <form method="post" action="">
            <input type="hidden" name="form_type" value="charaeno">
            <label>キャラエノURL: <input type="url" name="charaeno_url" required></label><br>
            <label>所属グループ:
                <select name="group_id" required>
                    <option value="">選択してください</option>
                    <?php foreach ($groups as $group): ?>
                        <option value="<?= htmlspecialchars($group['id']) ?>">
                            <?= htmlspecialchars($group['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label><br>
            <button type="submit">キャラエノ登録</button>
        </form>

        <!-- メッセージの表示 -->
        <?php if ($message): ?>
            <p><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
    </main>
</body>

</html>