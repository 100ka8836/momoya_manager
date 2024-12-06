<?php
require 'includes/db.php';
require 'fetch_skills.php';

// グループIDを取得
$group_id = $_GET['group_id'] ?? null;

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
$characters = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

// 技能データの構造を適切に取得
$skills = $skillsData['skills'] ?? [];
$all_skills = $skillsData['all_skills'] ?? [];

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

    <!-- 各タブの「編集」ボタンの表示を切り替える -->
    <script src="assets/js/edit_toggle.js" defer></script>

    <!-- 技能タブの「編集」ボタンの表示を切り替える -->
    <script src="assets/js/edit_skill_value.js" defer></script>

    <!-- 基本タブの編集ボタン機能 -->
    <script src="assets/js/edit_basic_value.js" defer></script>

    <!-- 能力タブの編集ボタン機能 -->
    <script src="assets/js/edit_ability_value.js" defer></script>

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
                        <th>以上</th> <!-- 操作列 -->
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>職業</td>
                        <?php foreach ($characters as $c): ?>
                            <td id="basic-cell-<?= $c['id'] ?>-occupation">
                                <span class="value-display"><?= htmlspecialchars($c['occupation']) ?></span>
                                <button class="edit-button" style="display: none;"
                                    onclick="editBasicValue(<?= $c['id'] ?>, 'occupation')">編集</button>
                            </td>
                        <?php endforeach; ?>
                        <td></td> <!-- 操作列の空白 -->
                    </tr>
                    <tr>
                        <td>住所</td>
                        <?php foreach ($characters as $c): ?>
                            <td id="basic-cell-<?= $c['id'] ?>-birthplace">
                                <span class="value-display"><?= htmlspecialchars($c['birthplace']) ?></span>
                                <button class="edit-button" style="display: none;"
                                    onclick="editBasicValue(<?= $c['id'] ?>, 'birthplace')">編集</button>
                            </td>
                        <?php endforeach; ?>
                        <td></td> <!-- 操作列の空白 -->
                    </tr>
                    <tr>
                        <td>年齢</td>
                        <?php foreach ($characters as $c): ?>
                            <td id="basic-cell-<?= $c['id'] ?>-age">
                                <span class="value-display"><?= htmlspecialchars($c['age']) ?></span>
                                <button class="edit-button" style="display: none;"
                                    onclick="editBasicValue(<?= $c['id'] ?>, 'age')">編集</button>
                            </td>
                        <?php endforeach; ?>
                        <td></td> <!-- 操作列の空白 -->
                    </tr>
                    <tr>
                        <td>性別</td>
                        <?php foreach ($characters as $c): ?>
                            <td id="basic-cell-<?= $c['id'] ?>-sex">
                                <span class="value-display"><?= htmlspecialchars($c['sex']) ?></span>
                                <button class="edit-button" style="display: none;"
                                    onclick="editBasicValue(<?= $c['id'] ?>, 'sex')">編集</button>
                            </td>
                        <?php endforeach; ?>
                        <td></td> <!-- 操作列の空白 -->
                    </tr>

                </tbody>
            </table>
            <button id="toggle-basic-edit-mode">基本情報の変更</button>
        </div>

        <!-- 能力値タブ -->
        <div id="abilities" class="tab-content">
            <table id="sortable-table">
                <div>
                    <input type="text" class="column-search" placeholder="検索: 例 STR, DEX, POW">
                </div>
                <thead>
                    <tr>
                        <th>カラム</th>
                        <?php foreach ($characters as $character): ?>
                            <th><?= htmlspecialchars($character['name']) ?></th>
                        <?php endforeach; ?>
                        <th>以上</th> <!-- 操作列 -->
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>STR</td>
                        <?php foreach ($characters as $c): ?>
                            <td id="abilities-cell-<?= $c['id'] ?>-str">
                                <span class="value-display"><?= htmlspecialchars($c['str']) ?></span>
                                <button class="edit-button" style="display: none;"
                                    onclick="editAbilityValue(<?= $c['id'] ?>, 'str')">編集</button>
                            </td>
                        <?php endforeach; ?>
                        <td></td> <!-- 操作列の空白 -->
                    </tr>
                    <tr>
                        <td>CON</td>
                        <?php foreach ($characters as $c): ?>
                            <td id="abilities-cell-<?= $c['id'] ?>-con">
                                <span class="value-display"><?= htmlspecialchars($c['con']) ?></span>
                                <button class="edit-button" style="display: none;"
                                    onclick="editAbilityValue(<?= $c['id'] ?>, 'con')">編集</button>
                            </td>
                        <?php endforeach; ?>
                        <td></td> <!-- 操作列の空白 -->
                    </tr>
                    <tr>
                        <td>POW</td>
                        <?php foreach ($characters as $c): ?>
                            <td id="abilities-cell-<?= $c['id'] ?>-pow">
                                <span class="value-display"><?= htmlspecialchars($c['pow']) ?></span>
                                <button class="edit-button" style="display: none;"
                                    onclick="editAbilityValue(<?= $c['id'] ?>, 'pow')">編集</button>
                            </td>
                        <?php endforeach; ?>
                        <td></td> <!-- 操作列の空白 -->
                    </tr>
                    <tr>
                        <td>DEX</td>
                        <?php foreach ($characters as $c): ?>
                            <td id="abilities-cell-<?= $c['id'] ?>-dex">
                                <span class="value-display"><?= htmlspecialchars($c['dex']) ?></span>
                                <button class="edit-button" style="display: none;"
                                    onclick="editAbilityValue(<?= $c['id'] ?>, 'dex')">編集</button>
                            </td>
                        <?php endforeach; ?>
                        <td></td> <!-- 操作列の空白 -->
                    </tr>
                    <tr>
                        <td>APP</td>
                        <?php foreach ($characters as $c): ?>
                            <td id="abilities-cell-<?= $c['id'] ?>-app">
                                <span class="value-display"><?= htmlspecialchars($c['app']) ?></span>
                                <button class="edit-button" style="display: none;"
                                    onclick="editAbilityValue(<?= $c['id'] ?>, 'app')">編集</button>
                            </td>
                        <?php endforeach; ?>
                        <td></td> <!-- 操作列の空白 -->
                    </tr>
                    <tr>
                        <td>SIZ</td>
                        <?php foreach ($characters as $c): ?>
                            <td id="abilities-cell-<?= $c['id'] ?>-siz">
                                <span class="value-display"><?= htmlspecialchars($c['siz']) ?></span>
                                <button class="edit-button" style="display: none;"
                                    onclick="editAbilityValue(<?= $c['id'] ?>, 'siz')">編集</button>
                            </td>
                        <?php endforeach; ?>
                        <td></td> <!-- 操作列の空白 -->
                    </tr>
                    <tr>
                        <td>INT</td>
                        <?php foreach ($characters as $c): ?>
                            <td id="abilities-cell-<?= $c['id'] ?>-int_value">
                                <span class="value-display"><?= htmlspecialchars($c['int_value']) ?></span>
                                <button class="edit-button" style="display: none;"
                                    onclick="editAbilityValue(<?= $c['id'] ?>, 'int_value')">編集</button>
                            </td>
                        <?php endforeach; ?>
                        <td></td> <!-- 操作列の空白 -->
                    </tr>
                    <tr>
                        <td>EDU</td>
                        <?php foreach ($characters as $c): ?>
                            <td id="abilities-cell-<?= $c['id'] ?>-edu">
                                <span class="value-display"><?= htmlspecialchars($c['edu']) ?></span>
                                <button class="edit-button" style="display: none;"
                                    onclick="editAbilityValue(<?= $c['id'] ?>, 'edu')">編集</button>
                            </td>
                        <?php endforeach; ?>
                        <td></td> <!-- 操作列の空白 -->
                    </tr>
                    <tr>
                        <td>HP</td>
                        <?php foreach ($characters as $c): ?>
                            <td id="abilities-cell-<?= $c['id'] ?>-hp">
                                <span class="value-display"><?= htmlspecialchars($c['hp']) ?></span>
                                <button class="edit-button" style="display: none;"
                                    onclick="editAbilityValue(<?= $c['id'] ?>, 'hp')">編集</button>
                            </td>
                        <?php endforeach; ?>
                        <td></td> <!-- 操作列の空白 -->
                    </tr>
                    <tr>
                        <td>MP</td>
                        <?php foreach ($characters as $c): ?>
                            <td id="abilities-cell-<?= $c['id'] ?>-mp">
                                <span class="value-display"><?= htmlspecialchars($c['mp']) ?></span>
                                <button class="edit-button" style="display: none;"
                                    onclick="editAbilityValue(<?= $c['id'] ?>, 'mp')">編集</button>
                            </td>
                        <?php endforeach; ?>
                        <td></td> <!-- 操作列の空白 -->
                    </tr>
                    <tr>
                        <td>DB</td>
                        <?php foreach ($characters as $c): ?>
                            <td id="abilities-cell-<?= $c['id'] ?>-db">
                                <span class="value-display"><?= htmlspecialchars($c['db']) ?></span>
                                <button class="edit-button" style="display: none;"
                                    onclick="editAbilityValue(<?= $c['id'] ?>, 'db')">編集</button>
                            </td>
                        <?php endforeach; ?>
                        <td></td> <!-- 操作列の空白 -->
                    </tr>
                    <tr>
                        <td>現在SAN</td>
                        <?php foreach ($characters as $c): ?>
                            <td id="abilities-cell-<?= $c['id'] ?>-san_current">
                                <span class="value-display"><?= htmlspecialchars($c['san_current']) ?></span>
                                <button class="edit-button" style="display: none;"
                                    onclick="editAbilityValue(<?= $c['id'] ?>, 'san_current')">編集</button>
                            </td>
                        <?php endforeach; ?>
                        <td></td> <!-- 操作列の空白 -->
                    </tr>
                    <tr>
                        <td>最大SAN</td>
                        <?php foreach ($characters as $c): ?>
                            <td id="abilities-cell-<?= $c['id'] ?>-san_max">
                                <span class="value-display"><?= htmlspecialchars($c['san_max']) ?></span>
                                <button class="edit-button" style="display: none;"
                                    onclick="editAbilityValue(<?= $c['id'] ?>, 'san_max')">編集</button>
                            </td>
                        <?php endforeach; ?>
                        <td></td> <!-- 操作列の空白 -->
                    </tr>

                </tbody>
            </table>
            <button id="toggle-abilities-edit-mode">能力値の変更</button>
        </div>




        <!-- 技能タブ -->
        <div id="skills" class="tab-content">
            <div>
                <input type="text" class="column-search" placeholder="検索: 例 年齢, STR, 目星">
            </div>
            <table id="sortable-table">
                <thead>
                    <tr>
                        <th>技能</th>
                        <?php foreach ($characters as $character): ?>
                            <th><?= htmlspecialchars($character['name']) ?></th>
                        <?php endforeach; ?>
                        <th>操作</th> <!-- 操作列 -->
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_skills as $skill_name): ?>
                        <tr>
                            <td><?= htmlspecialchars($skill_name) ?></td>
                            <?php foreach ($characters as $character): ?>
                                <td id="skills-cell-<?= $character['id'] ?>-<?= htmlspecialchars($skill_name) ?>">
                                    <?php
                                    // スキル値を取得
                                    $skill_value = $skills[$character['id']][$skill_name] ?? '-';
                                    ?>
                                    <span class="value-display"><?= htmlspecialchars($skill_value) ?></span>
                                    <button class="edit-button" style="display: none;"
                                        onclick="editSkillValue(<?= $character['id'] ?>, '<?= htmlspecialchars($skill_name) ?>')">編集</button>
                                </td>
                            <?php endforeach; ?>
                            <td></td> <!-- 操作列の空白 -->
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button id="toggle-skills-edit-mode">技能の変更</button>
        </div>




        <!-- その他タブ -->
        <div id="other" class="tab-content">
            <table id="sortable-table">
                <div>
                    <input type="text" class="column-search" placeholder="検索: 例 年齢, STR, 目星">
                </div>
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