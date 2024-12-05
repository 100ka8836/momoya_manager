document.addEventListener("DOMContentLoaded", () => {
  // ボタンの存在を確認してからイベントリスナーを登録
  const toggleButton = document.getElementById("toggle-skills-edit-mode");
  if (toggleButton) {
    toggleButton.addEventListener("click", () => {
      document.querySelectorAll("#skills .edit-button").forEach((btn) => {
        btn.style.display =
          btn.style.display === "none" ? "inline-block" : "none";
      });
    });
  } else {
    console.error("Button with ID 'toggle-skills-edit-mode' not found.");
  }
});

function editSkillValue(characterId, skillName) {
  // セルの取得
  const cell = document.getElementById(
    `skills-cell-${characterId}-${skillName}`
  );
  const currentValue = cell.querySelector(".value-display").innerText.trim();

  // フォームを作成
  const form = document.createElement("form");
  form.method = "POST";
  form.action = "edit_skill_value.php";

  // 入力フィールド
  const input = document.createElement("input");
  input.type = "text";
  input.name = "new_value";
  input.value = currentValue;
  input.required = true;

  // 隠しフィールド（キャラクターIDとスキル名）
  const hiddenCharId = document.createElement("input");
  hiddenCharId.type = "hidden";
  hiddenCharId.name = "character_id";
  hiddenCharId.value = characterId;

  const hiddenSkillName = document.createElement("input");
  hiddenSkillName.type = "hidden";
  hiddenSkillName.name = "skill_name";
  hiddenSkillName.value = skillName;

  // 隠しフィールド（グループID）
  const hiddenGroupId = document.createElement("input");
  hiddenGroupId.type = "hidden";
  hiddenGroupId.name = "group_id";
  hiddenGroupId.value = document.body.getAttribute("data-group-id"); // bodyからgroup_idを取得
  form.appendChild(hiddenGroupId);

  // 保存ボタン
  const saveButton = document.createElement("button");
  saveButton.type = "submit";
  saveButton.innerText = "保存";

  // フォームに要素を追加
  form.appendChild(input);
  form.appendChild(hiddenCharId);
  form.appendChild(hiddenSkillName);
  form.appendChild(saveButton);

  // 現在の値をフォームに置き換え
  cell.innerHTML = "";
  cell.appendChild(form);
}
