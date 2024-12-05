<?php
require 'includes/db.php';
require 'fetch_skills.php';

// グループIDを取得
$group_id = $_GET['group_id'] ?? null;
$activeTab = $_GET['activeTab'] ?? 'basic';

if (!$group_id) {
    die("グループが選択されていません。");
}

// グループに所属するキャラクターを取得
$stmt = $pdo->prepare("
    SELECT characters.id, characters.name, characters.occupation, characters.birthplace, characters.degree,
           characters.age, characters.sex,
           character_attributes.str, character_attributes.con, character_attributes.pow,
           character_attributes.dex, character_attributes.app, character_attributes.siz,
           character_attributes.int_value, character_attributes.edu,
           character_attributes.hp, character_attributes.mp, character_attributes.db,
           character_attributes.san_current, character_attributes.san_max
    FROM characters
    LEFT JOIN character_attributes ON characters.id = character_attributes.character_id
    WHERE characters.group_id = ?
");
$stmt->execute([$group_id]);
$characters = $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];

// JavaScriptにキャラクターデータを渡す
echo "<script>const characters = " . json_encode(array_map(function ($character) {
    return array_map(function ($value) {
        return $value !== null ? htmlspecialchars($value) : ''; // null の場合は空文字列を代入
    }, $character);
}, $characters)) . ";</script>";

// キャラクターが存在しない場合のエラーメッセージ
if (empty($characters)) {
    die("このグループにはキャラクターが登録されていません。");
}

// 技能を取得
$skillsData = fetchSkills($group_id, $pdo);
$skills = $skillsData['skills'];
$all_skills = $skillsData['all_skills'];

// 現在のグループに関連付けられたカテゴリを取得
$stmt = $pdo->prepare("SELECT * FROM Categories WHERE group_id = ?");
$stmt->execute([$group_id]);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// タブ保持用のアクティブタブを取得
$activeTab = $_GET['activeTab'] ?? 'basic'; // デフォルトタブは 'basic'
?>


<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>グループ詳細</title>
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- タブ切り替えスクリプト: タブの表示切り替えと現在のタブ状態を localStorage に保存 -->
    <script src="assets/js/tabs.js" defer></script>

    <!-- テーブルのソートスクリプト: 最初のセルをクリックして行を昇順または降順に並べ替え -->
    <script src="assets/js/sort_table.js" defer></script>

    <!-- 全タブ共通の検索スクリプト: テーブルの検索機能を提供し、検索条件に合致する行をトップに持ってくる -->
    <script src="assets/js/search_table.js" defer></script>

    <!-- その他タブ専用スクリプト -->
    <script src="assets/js/other_tab.js" defer></script>

    <!-- その他タブの編集について -->
    <script src="assets/js/edit_category.js" defer></script>

    <!-- その他タブの編集ボタン動作 -->
    <script src="assets/js/edit_value.js" defer></script>

</head>

<body data-group-id="<?= htmlspecialchars($group_id) ?>">
    <?php include __DIR__ . '/includes/header.php'; ?>
    <main class="container">
        <div class="tabs">
            <button class="tab-button active" data-tab="basic">基本</button>
            <button class="tab-button" data-tab="abilities">能力</button>
            <button class="tab-button" data-tab="skills">技能</button>
            <button class="tab-button" data-tab="other">その他</button>
        </div>

        <!-- 基本情報タブ -->
        <div id="basic" class="tab-content active">
            <table id="sortable-table">
                <div>
                    <input type="text" class="column-search" placeholder="検索: 例 年齢, STR, 目星">
                </div>
                <thead>
                    <tr>
                        <th>カラム</th>
                        <?php foreach ($characters as $character): ?>
                            <th><?= htmlspecialchars($character['name']) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>職業</td>
                        <?php foreach ($characters as $c): ?>
                            <td><?= htmlspecialchars($c['occupation']) ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <td>住所</td>
                        <?php foreach ($characters as $c): ?>
                            <td><?= htmlspecialchars($c['birthplace']) ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <td>年齢</td>
                        <?php foreach ($characters as $c): ?>
                            <td><?= htmlspecialchars($c['age']) ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <td>性別</td>
                        <?php foreach ($characters as $c): ?>
                            <td><?= htmlspecialchars($c['sex']) ?></td>
                        <?php endforeach; ?>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- 能力値タブ -->
        <div id="abilities" class="tab-content">
            <table id="sortable-table">
                <div>
                    <input type="text" class="column-search" placeholder="検索: 例 年齢, STR, 目星">
                </div>
                <thead>
                    <tr>
                        <th>カラム</th>
                        <?php foreach ($characters as $character): ?>
                            <th><?= htmlspecialchars($character['name']) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>STR</td><?php foreach ($characters as $c)
                            echo "<td>{$c['str']}</td>"; ?>
                    </tr>
                    <tr>
                        <td>CON</td><?php foreach ($characters as $c)
                            echo "<td>{$c['con']}</td>"; ?>
                    </tr>
                    <tr>
                        <td>POW</td><?php foreach ($characters as $c)
                            echo "<td>{$c['pow']}</td>"; ?>
                    </tr>
                    <tr>
                        <td>DEX</td><?php foreach ($characters as $c)
                            echo "<td>{$c['dex']}</td>"; ?>
                    </tr>
                    <tr>
                        <td>APP</td><?php foreach ($characters as $c)
                            echo "<td>{$c['app']}</td>"; ?>
                    </tr>
                    <tr>
                        <td>SIZ</td><?php foreach ($characters as $c)
                            echo "<td>{$c['siz']}</td>"; ?>
                    </tr>
                    <tr>
                        <td>INT</td><?php foreach ($characters as $c)
                            echo "<td>{$c['int_value']}</td>"; ?>
                    </tr>
                    <tr>
                        <td>EDU</td><?php foreach ($characters as $c)
                            echo "<td>{$c['edu']}</td>"; ?>
                    </tr>
                    <tr>
                        <td>HP</td><?php foreach ($characters as $c)
                            echo "<td>{$c['hp']}</td>"; ?>
                    </tr>
                    <tr>
                        <td>MP</td><?php foreach ($characters as $c)
                            echo "<td>{$c['mp']}</td>"; ?>
                    </tr>
                    <tr>
                        <td>DB</td><?php foreach ($characters as $c)
                            echo "<td>{$c['db']}</td>"; ?>
                    </tr>
                    <tr>
                        <td>現在SAN</td><?php foreach ($characters as $c)
                            echo "<td>{$c['san_current']}</td>"; ?>
                    </tr>
                    <tr>
                        <td>最大SAN</td><?php foreach ($characters as $c)
                            echo "<td>{$c['san_max']}</td>"; ?>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- 技能タブ -->
        <div id="skills" class="tab-content">
            <table id="sortable-table">
                <div>
                    <input type="text" class="column-search" placeholder="検索: 例 年齢, STR, 目星">
                </div>
                <thead>
                    <tr>
                        <th>技能</th>
                        <?php foreach ($characters as $character): ?>
                            <th><?= htmlspecialchars($character['name']) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_skills as $skill_name): ?>
                        <tr>
                            <td><?= htmlspecialchars($skill_name) ?></td>
                            <?php foreach ($characters as $character): ?>
                                <td>
                                    <?php
                                    $value = array_filter(
                                        $skills[$character['id']] ?? [],
                                        fn($s) => $s['skill_name'] === $skill_name
                                    );
                                    echo $value ? reset($value)['skill_value'] : '-';
                                    ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- その他タブ -->
        <div id="other" class="tab-content">
            <h2>カテゴリと値</h2>

            <table id="sortable-table">
                <thead>
                    <tr>
                        <th>項目</th>
                        <?php foreach ($characters as $character): ?>
                            <th><?= htmlspecialchars($character['name']) ?></th>
                        <?php endforeach; ?>
                        <th>操作</th> <!-- 削除ボタン用の列 -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // カテゴリを取得
                    $stmt = $pdo->prepare("SELECT * FROM Categories WHERE group_id = ?");
                    $stmt->execute([$group_id]);
                    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($categories as $category): ?>
                        <tr>
                            <td><?= htmlspecialchars($category['name']); ?></td>
                            <?php foreach ($characters as $character): ?>
                                <td id="value-cell-<?= $character['id'] ?>-<?= $category['id'] ?>">
                                    <?php
                                    $stmtValue = $pdo->prepare("
                                SELECT value
                                FROM CharacterValues
                                WHERE character_id = ? AND category_id = ?
                            ");
                                    $stmtValue->execute([$character['id'], $category['id']]);
                                    $value = $stmtValue->fetchColumn();
                                    ?>
                                    <span class="value-display"><?= htmlspecialchars($value ?? '-') ?></span>
                                    <button class="edit-button" style="display: none;"
                                        onclick="editValue(<?= $character['id'] ?>, <?= $category['id'] ?>)">編集</button>
                                </td>
                            <?php endforeach; ?>
                            <td>
                                <form method="POST" action="delete_category.php" style="display: inline;">
                                    <input type="hidden" name="category_id"
                                        value="<?= htmlspecialchars($category['id']); ?>">
                                    <input type="hidden" name="group_id" value="<?= htmlspecialchars($group_id); ?>">
                                    <button type="submit" class="delete-button">削除</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- カテゴリ追加フォーム -->
            <form id="add-category-form" method="POST" action="add_category.php">
                <input type="hidden" name="group_id" value="<?= htmlspecialchars($group_id); ?>">
                <input type="text" id="category-name" name="category_name" required placeholder="追加する項目名">
                <button type="submit">＋</button>
            </form>

            <!-- 値の変更ボタン -->
            <button id="toggle-edit-mode">キャラクター情報の変更</button>
        </div>




    </main>
    <script>
        const groupId = <?= json_encode($group_id); ?>;
    </script>

</body>

</html>