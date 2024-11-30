<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Composerのオートローダーを読み込み

use Dotenv\Dotenv;

// .env ファイルを読み込む
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

try {
    // 環境変数を利用してPDO接続を設定
    $dsn = 'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';charset=utf8';
    $username = $_ENV['DB_USER'];
    $password = $_ENV['DB_PASSWORD'];

    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // デバッグ用: 接続確認
    // echo "データベースに接続成功！";

} catch (PDOException $e) {
    error_log('データベース接続エラー: ' . $e->getMessage());
    die('データベース接続エラーが発生しました。管理者に連絡してください。');
}
