function editBasicValue(characterId, fieldName) {
  // セルの取得
  const cell = document.getElementById(
    `basic-cell-${characterId}-${fieldName}`
  );
  const currentValue = cell.querySelector(".value-display").innerText.trim();

  // フォームを作成
  const form = document.createElement("form");
  form.method = "POST";
  form.action = "edit_basic_value.php";

  // 入力フィールド
  const input = document.createElement("input");
  input.type = "text";
  input.name = "new_value";
  input.value = currentValue;
  input.required = true;

  // 隠しフィールド（キャラクターIDとフィールド名）
  const hiddenCharId = document.createElement("input");
  hiddenCharId.type = "hidden";
  hiddenCharId.name = "character_id";
  hiddenCharId.value = characterId;

  const hiddenFieldName = document.createElement("input");
  hiddenFieldName.type = "hidden";
  hiddenFieldName.name = "field_name";
  hiddenFieldName.value = fieldName;

  // 隠しフィールド（グループID）
  const hiddenGroupId = document.createElement("input");
  hiddenGroupId.type = "hidden";
  hiddenGroupId.name = "group_id";
  hiddenGroupId.value = document.body.getAttribute("data-group-id");

  // 保存ボタン
  const saveButton = document.createElement("button");
  saveButton.type = "submit";
  saveButton.innerText = "保存";

  // フォームに要素を追加
  form.appendChild(input);
  form.appendChild(hiddenCharId);
  form.appendChild(hiddenFieldName);
  form.appendChild(hiddenGroupId);
  form.appendChild(saveButton);

  // 現在の値をフォームに置き換え
  cell.innerHTML = "";
  cell.appendChild(form);
}
