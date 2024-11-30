document.addEventListener("DOMContentLoaded", () => {
  const tableHeadRow = document.getElementById("table-head-row");
  const tableBody = document.getElementById("table-body");

  // グループIDをURLから取得
  const groupId = new URLSearchParams(window.location.search).get("group_id");
  if (!groupId) {
    console.error("グループIDが指定されていません");
    return;
  }

  // 初期化: テーブルのヘッダーと既存のデータを取得して表示
  async function initializeTable() {
    try {
      const response = await fetch(
        `fetch_other_items_with_characters.php?group_id=${groupId}`
      );
      const data = await response.json();

      // ヘッダーを生成
      generateTableHeader(data.characters);

      // ボディを生成
      data.items.forEach((item) => {
        addRowForItem(item.item_name, item.id, data.characters.length);
      });
    } catch (error) {
      console.error("テーブルデータ取得エラー:", error);
    }
  }

  // テーブルヘッダーを生成
  function generateTableHeader(characters) {
    const itemHeader = document.createElement("th");
    itemHeader.textContent = "項目";
    tableHeadRow.appendChild(itemHeader);

    characters.forEach((character) => {
      const charHeader = document.createElement("th");
      charHeader.textContent = character.name;
      tableHeadRow.appendChild(charHeader);
    });
  }

  // 行として項目を追加する
  function addRowForItem(itemName, itemId, characterCount) {
    const row = document.createElement("tr");
    row.dataset.itemId = itemId;

    // 項目名セル
    const itemCell = document.createElement("td");
    itemCell.textContent = itemName;
    row.appendChild(itemCell);

    // キャラクター列のセル
    for (let i = 0; i < characterCount; i++) {
      const charCell = document.createElement("td");
      charCell.textContent = "-";
      row.appendChild(charCell);
    }

    // 削除ボタンのセル
    const deleteCell = document.createElement("td");
    const deleteButton = document.createElement("button");
    deleteButton.textContent = "×";
    deleteButton.classList.add("delete-row-btn");

    deleteCell.appendChild(deleteButton);
    row.appendChild(deleteCell);

    tableBody.appendChild(row);
  }

  // 初期化処理を実行
  initializeTable();
});
