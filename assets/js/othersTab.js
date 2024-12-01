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
        `api/fetchOtherItems.php?group_id=${groupId}`
      );
      const data = await response.json();

      if (data.success) {
        // ヘッダーを生成
        generateTableHeader(data.characters);

        // ボディを生成
        populateTable(data.items, data.characters);
      } else {
        console.error("データ取得エラー:", data.message);
      }
    } catch (error) {
      console.error("通信エラー:", error);
    }
  }

  // テーブルヘッダーを生成
  function generateTableHeader(characters) {
    tableHeadRow.innerHTML = ""; // 既存のヘッダーをリセット

    const itemHeader = document.createElement("th");
    itemHeader.textContent = "項目";
    tableHeadRow.appendChild(itemHeader);

    characters.forEach((character) => {
      const charHeader = document.createElement("th");
      charHeader.textContent = character.name; // キャラクター名を表示
      tableHeadRow.appendChild(charHeader);
    });

    // 最後に削除ボタン用の列
    const actionHeader = document.createElement("th");
    actionHeader.textContent = "操作";
    tableHeadRow.appendChild(actionHeader);
  }

  // テーブルボディを生成
  function populateTable(items, characters) {
    tableBody.innerHTML = ""; // テーブルをリセット

    items.forEach((item) => {
      const row = document.createElement("tr");
      row.dataset.itemId = item.item_id;

      // 項目名セル
      const itemCell = document.createElement("td");
      itemCell.textContent = item.item_name;
      row.appendChild(itemCell);

      // 各キャラクターの値をセルに追加
      characters.forEach((character) => {
        const valueCell = document.createElement("td");
        valueCell.textContent = item.values[character.id] || "-"; // 値がなければ "-"
        row.appendChild(valueCell);
      });

      // 削除ボタンセル
      const deleteCell = document.createElement("td");
      const deleteButton = document.createElement("button");
      deleteButton.textContent = "×";
      deleteButton.classList.add("delete-row-btn");
      deleteButton.addEventListener("click", () => {
        const confirmDelete = confirm("この行を削除しますか？");
        if (confirmDelete) {
          deleteRow(row);
        }
      });
      deleteCell.appendChild(deleteButton);
      row.appendChild(deleteCell);

      tableBody.appendChild(row);
    });
  }

  // 行削除処理
  async function deleteRow(row) {
    const itemId = row.dataset.itemId;
    try {
      const response = await fetch(
        `api/deleteOtherItem.php?item_id=${itemId}`,
        {
          method: "DELETE"
        }
      );
      const result = await response.json();
      if (result.success) {
        row.remove();
        alert("行が削除されました");
      } else {
        alert("削除に失敗しました: " + result.message);
      }
    } catch (error) {
      console.error("削除エラー:", error);
    }
  }

  // 初期化処理を実行
  initializeTable();
});
