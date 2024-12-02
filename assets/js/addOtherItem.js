document.addEventListener("DOMContentLoaded", () => {
  const characterTableBody = document.querySelector("#character-table tbody");
  const addItemBtn = document.getElementById("add-item-btn");

  // 新しい行を追加する関数
  function addRowForItem(itemName, itemId, characterCount) {
    const tr = document.createElement("tr");
    tr.dataset.itemId = itemId;

    // 項目名セル
    const itemCell = document.createElement("td");
    itemCell.textContent = itemName;
    tr.appendChild(itemCell);

    // キャラクター列のセル
    for (let i = 0; i < characterCount; i++) {
      const charCell = document.createElement("td");
      charCell.textContent = "-";
      tr.appendChild(charCell);
    }

    // 操作セル（削除ボタン）
    const deleteCell = document.createElement("td");
    const deleteButton = document.createElement("button");
    deleteButton.textContent = "×";
    deleteButton.classList.add("delete-row-btn");
    deleteButton.addEventListener("click", () => {
      const confirmDelete = confirm("この行を削除しますか？");
      if (confirmDelete) {
        deleteRow(tr);
      }
    });

    deleteCell.appendChild(deleteButton);
    tr.appendChild(deleteCell);

    characterTableBody.appendChild(tr);
  }

  // サーバーに新しいデータを保存する関数
  async function addNewItem(groupId, itemName) {
    try {
      const response = await fetch("save_added_row.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ group_id: groupId, item_name: itemName })
      });

      const result = await response.json();

      if (result.success) {
        const itemId = result.item_id;
        const characterCount =
          document.querySelector("#character-table thead tr").children.length -
          2; // 「項目」列と「操作」列を除外
        addRowForItem(itemName, itemId, characterCount);
      } else {
        console.error(result.message);
        alert("データの保存に失敗しました");
      }
    } catch (error) {
      console.error("通信エラー:", error);
      alert("通信エラーが発生しました");
    }
  }

  // 削除処理
  async function deleteRow(row) {
    const itemId = row.dataset.itemId;
    if (!itemId) {
      console.error("項目IDが見つかりません");
      return;
    }

    try {
      const response = await fetch(
        "/momoya_character_manager/api/delete_other_item.php",
        {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: new URLSearchParams({ item_id: itemId }) // POSTデータを送信
        }
      );

      const result = await response.json(); // レスポンスをJSON形式で解析
      if (result.success) {
        row.remove(); // 成功時に行を削除
      } else {
        console.error("削除エラー:", result.message); // サーバーからのエラーメッセージを表示
      }
    } catch (error) {
      console.error("削除リクエストエラー:", error); // ネットワークやサーバーエラーを表示
    }
  }

  // + ボタンのクリックイベント
  addItemBtn.addEventListener("click", async () => {
    const itemName = prompt("追加する項目名を入力してください:");
    if (!itemName) return;

    const groupId = new URLSearchParams(window.location.search).get("group_id");
    await addNewItem(groupId, itemName);
  });
});
