document.addEventListener("DOMContentLoaded", () => {
  const characterTableBody = document.querySelector("#character-table tbody");

  // 行を削除する関数
  async function deleteRow(row) {
    const itemId = row.dataset.itemId;
    if (!itemId) {
      console.error("項目IDが見つかりません");
      return;
    }

    // サーバーに削除リクエストを送信
    try {
      const response = await fetch("delete_other_item.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded"
        },
        body: new URLSearchParams({ item_id: itemId })
      });

      const result = await response.json();
      if (result.success) {
        row.remove(); // テーブルから行を削除
      } else {
        console.error("削除エラー:", result.message);
      }
    } catch (error) {
      console.error("削除リクエストエラー:", error);
    }
  }

  // 行を追加する場合の削除ボタン自動追加
  function addRowForItem(itemName, itemId, characterCount) {
    const tr = document.createElement("tr");
    tr.dataset.itemId = itemId;

    const itemCell = document.createElement("td");
    itemCell.textContent = itemName;
    tr.appendChild(itemCell);

    // キャラクター列を追加
    for (let i = 0; i < characterCount; i++) {
      const charCell = document.createElement("td");
      charCell.textContent = "-";
      tr.appendChild(charCell);
    }

    // 削除ボタンを追加
    const deleteCell = document.createElement("td");
    const deleteButton = document.createElement("button");
    deleteButton.textContent = "×";
    deleteButton.classList.add("delete-row-btn");
    deleteCell.appendChild(deleteButton);
    tr.appendChild(deleteCell);

    characterTableBody.appendChild(tr);
  }

  // 初期化関数で既存の行に削除ボタンを追加
  async function fetchAndPopulateTable() {
    try {
      const response = await fetch("fetch_other_items_with_characters.php");
      const data = await response.json();

      // テーブルデータを生成
      data.items.forEach((item) => {
        addRowForItem(item.item_name, item.id, data.characters.length);
      });
    } catch (error) {
      console.error("データ取得エラー:", error);
    }
  }

  fetchAndPopulateTable(); // 初期化
});
