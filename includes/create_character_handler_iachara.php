<?php
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // 基本情報を登録
        $stmt = $pdo->prepare("
            INSERT INTO characters (source_url, name, age, occupation, birthplace, sex)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $_POST['source_url'] ?? null,
            $_POST['name'],
            $_POST['age'] ?? null,
            $_POST['occupation'] ?? null,
            $_POST['birthplace'] ?? null,
            $_POST['sex'] ?? null
        ]);
        $characterId = $pdo->lastInsertId();

        // 能力値を登録
        $attributes = $_POST['attributes'] ?? [];
        $stmt = $pdo->prepare("
            INSERT INTO character_attributes (character_id, str, con, pow, dex, app, siz, int_value, edu, hp, mp, san_current, san_max, db)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $characterId,
            $attributes['str'] ?? null,
            $attributes['con'] ?? null,
            $attributes['pow'] ?? null,
            $attributes['dex'] ?? null,
            $attributes['app'] ?? null,
            $attributes['siz'] ?? null,
            $attributes['int_value'] ?? null,
            $attributes['edu'] ?? null,
            $attributes['hp'] ?? null,
            $attributes['mp'] ?? null,
            $attributes['san_current'] ?? null,
            $attributes['san_max'] ?? null,
            $attributes['db'] ?? null
        ]);

        // 技能値を登録
        $skills = $_POST['skills'] ?? [];
        foreach ($skills as $skillName => $skillValue) {
            $stmt = $pdo->prepare("
                INSERT INTO character_skills (character_id, skill_name, skill_value)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$characterId, $skillName, $skillValue]);
        }

        // 追加技能を登録
        $customSkills = $_POST['custom_skills'] ?? [];
        foreach ($customSkills as $customSkill) {
            $stmt = $pdo->prepare("
                INSERT INTO character_skills (character_id, skill_name, skill_value)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$characterId, $customSkill['name'], $customSkill['value']]);
        }

        $pdo->commit();

        // 成功メッセージでリダイレクト
        header('Location: http://localhost/momoya_character_manager/create_character_iachara.php?message=登録しました！&success=1');
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("エラー: " . $e->getMessage());

        // エラーメッセージでリダイレクト
        header('Location: http://localhost/momoya_character_manager/create_character_iachara.php?message=エラーが発生しました&success=0');
        exit;

    }
}
