import {
  fetchOtherItems,
  saveEditedRow,
  deleteOtherItem
} from "./apiHelpers.js";
import { isValidValue, sanitizeInput } from "./validationHelpers.js";

document.addEventListener("DOMContentLoaded", () => {
  const tableHeadRow = document.getElementById("table-head-row");
  const tableBody = document.getElementById("table-body");
  const editButton = document.getElementById("edit-table-btn");
  const saveButton = document.getElementById("save-table-btn");
  const groupId = new URLSearchParams(window.location.search).get("group_id");
  let isEditing = false;

  if (!groupId) {
    console.error("グループIDが指定されていません");
    return;
  }

  async function initializeTable() {
    try {
      const data = await fetchOtherItems(groupId);
      console.log("APIレスポンス:", data);

      if (data.success) {
        generateTableHeader(data.characters);
        populateTable(data.items, data.characters);
      } else {
        console.error("データ取得エラー:", data.message);
      }
    } catch (error) {
      console.error("通信エラー:", error);
    }
  }

  function generateTableHeader(characters) {
    tableHeadRow.innerHTML = "";

    const itemHeader = document.createElement("th");
    itemHeader.textContent = "項目";
    tableHeadRow.appendChild(itemHeader);

    characters.forEach((character) => {
      const charHeader = document.createElement("th");
      charHeader.textContent = character.name;
      charHeader.dataset.characterId = character.id;
      tableHeadRow.appendChild(charHeader);
    });

    const actionHeader = document.createElement("th");
    actionHeader.textContent = "操作";
    tableHeadRow.appendChild(actionHeader);
  }

  function populateTable(items, characters) {
    // 既存の行をクリア
    tableBody.innerHTML = "";

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
        valueCell.textContent =
          (item.values && item.values[character.id]) || ""; // 安全にアクセス
        row.appendChild(valueCell);
      });

      // 削除ボタンセル
      const deleteCell = document.createElement("td");
      const deleteButton = document.createElement("button");
      deleteButton.textContent = "×";
      deleteButton.classList.add("delete-row-btn");
      deleteButton.addEventListener("click", async () => {
        if (confirm("この行を削除しますか？")) {
          const result = await deleteOtherItem(row.dataset.itemId);
          if (result.success) row.remove();
        }
      });
      deleteCell.appendChild(deleteButton);
      row.appendChild(deleteCell);

      tableBody.appendChild(row);
    });
  }

  function enableEditMode() {
    isEditing = true;
    editButton.style.display = "none";
    saveButton.style.display = "inline-block";

    Array.from(tableBody.querySelectorAll("tr")).forEach((row) => {
      Array.from(row.cells).forEach((cell, index) => {
        // 先頭と末尾のセルをスキップ
        if (index === 0 || index === row.cells.length - 1) return;
        const originalText = cell.textContent.trim();
        const input = document.createElement("input");
        input.type = "text";
        // "フォームに表示される値が '-' の場合は空欄にする"
        input.value = originalText === "-" ? "" : originalText;
        cell.textContent = "";
        cell.appendChild(input);
      });
    });
  }

  async function disableEditMode() {
    isEditing = false;
    editButton.style.display = "inline-block";
    saveButton.style.display = "none";

    const updates = [];

    Array.from(tableBody.querySelectorAll("tr")).forEach((row) => {
      const itemId = row.dataset.itemId;
      const rowValues = [];

      Array.from(row.cells).forEach((cell, index) => {
        // 先頭と末尾のセルをスキップ
        if (index === 0 || index === row.cells.length - 1) return;

        const input = cell.querySelector("input");
        if (input) {
          const value = input.value.trim() || "-"; // 空欄の場合は "-" を代入
          rowValues.push(value);
          cell.textContent = value; // セルに値を戻す
        } else {
          rowValues.push(cell.textContent.trim());
        }
      });

      updates.push({ item_id: itemId, values: rowValues });
    });

    try {
      const response = await saveEditedRow({ updates });
      if (!response.success) {
        alert("保存中にエラーが発生しました: " + response.message);
      } else {
        alert("保存が完了しました");
      }
    } catch (error) {
      console.error("通信エラー:", error);
    }
  }

  async function saveData() {
    const updates = [];

    document.querySelectorAll("tr").forEach((row) => {
      const itemId = row.dataset.itemId;
      Array.from(row.cells).forEach((cell, index) => {
        if (index === 0 || index === row.cells.length - 1) return; // 項目名と削除ボタンをスキップ

        const input = cell.querySelector("input");
        if (input) {
          const characterId = cell.dataset.characterId; // 各セルにキャラクターIDを設定
          const value = input.value.trim();
          updates.push({ item_id: itemId, character_id: characterId, value });
        }
      });
    });

    // 送信するデータの確認
    console.log("送信するデータ:", updates);

    // データをサーバーに送信
    try {
      const response = await fetch(
        "/momoya_character_manager/api/saveEditedRow.php",
        {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ updates })
        }
      );
      const result = await response.json();

      if (result.success) {
        alert("保存が完了しました");
      } else {
        console.error("保存エラー:", result.messages);
      }
    } catch (error) {
      console.error("通信エラー:", error);
    }
  }

  editButton.addEventListener("click", () => {
    if (!isEditing) enableEditMode();
  });

  saveButton.addEventListener("click", () => {
    if (isEditing) disableEditMode();
  });

  initializeTable();
});
