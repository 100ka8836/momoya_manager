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

// キャラクターが存在しない場合のエラーメッセージ
if (empty($characters)) {
    die("このグループにはキャラクターが登録されていません。");
}

// 技能を取得
$skillsData = fetchSkills($group_id, $pdo);
$skills = $skillsData['skills'];
$all_skills = $skillsData['all_skills'];

// タブ保持用のアクティブタブを取得
$activeTab = $_GET['activeTab'] ?? 'basic'; // デフォルトタブは 'basic'
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>グループ詳細</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/tabs.js" defer></script>
    <script src="assets/js/sort_table.js" defer></script>
    <script src="assets/js/search_table.js" defer></script>
    <script src="assets/js/addOtherItem.js" defer></script>
    <script src="assets/js/othersTab.js" defer></script>
    <script src="assets/js/editOtherItem.js" defer></script>
    <script src="assets/js/deleteRow.js"></script>
    <script src="assets/js/deleteOtherItem.js" defer></script>


</head>

<body data-group-id="<?= htmlspecialchars($group_id) ?>">
    <?php include __DIR__ . '/includes/header.php'; ?>
    <main class="container">
        <div class="tabs">
            <button class="tab-button active" data-tab="basic">基本</button>
            <button class="tab-button" data-tab="abilities">能力</button>
            <button class="tab-button" data-tab="skills">技能</button>
            <button class="tab-button" data-tab="others">その他</button>
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



        <div id="others" class="tab-content">
            <table id="character-table" class="character-table">
                <thead>
                    <tr id="table-head-row">
                        <!-- 「項目」とキャラクター名が動的に追加される -->
                    </tr>
                </thead>
                <tbody id="table-body">
                    <!-- 各項目の行が動的に追加される -->
                </tbody>
            </table>
            <div class="table-controls">
                <button id="add-item-btn" class="control-btn">+</button>
                <button id="edit-table-btn" class="control-btn">編集</button>
                <button id="save-table-btn" class="control-btn" style="display: none;">完了</button>
            </div>
        </div>



    </main>
    <script>
        const groupId = <?= json_encode($group_id); ?>;
    </script>

</body>

</html>