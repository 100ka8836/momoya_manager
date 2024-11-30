document.addEventListener("DOMContentLoaded", () => {
  const characterTableBody = document.querySelector("#character-table tbody");
  const addItemBtn = document.getElementById("add-item-btn");

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
          1;
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

  // テーブルに新しい行を追加する関数
  function addRowForItem(itemName, itemId, characterCount) {
    const tr = document.createElement("tr");
    tr.dataset.itemId = itemId;

    const itemCell = document.createElement("td");
    itemCell.textContent = itemName;
    tr.appendChild(itemCell);

    for (let i = 0; i < characterCount; i++) {
      const charCell = document.createElement("td");
      charCell.textContent = "-";
      tr.appendChild(charCell);
    }

    const deleteCell = createDeleteButton(tr);
    tr.appendChild(deleteCell);

    characterTableBody.appendChild(tr);
  }

  // 削除ボタンを生成する関数
  function createDeleteButton(row) {
    const deleteCell = document.createElement("td");
    deleteCell.classList.add("delete-cell");

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
    return deleteCell;
  }

  // 行を削除する関数
  async function deleteRow(row) {
    const itemId = row.dataset.itemId;
    if (!itemId) {
      console.error("項目IDが見つかりません");
      return;
    }

    try {
      const response = await fetch("delete_other_item.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({ item_id: itemId })
      });

      const result = await response.json();
      if (result.success) {
        row.remove();
      } else {
        console.error("削除エラー:", result.message);
      }
    } catch (error) {
      console.error("削除リクエストエラー:", error);
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
