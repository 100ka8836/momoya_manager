document.addEventListener("DOMContentLoaded", () => {
  const tableHeadRow = document.getElementById("table-head-row");
  const tableBody = document.getElementById("table-body");
  const groupId = new URLSearchParams(window.location.search).get("group_id");
  const addItemButton = document.getElementById("add-item-btn");
  const newItemInput = document.getElementById("new-item-name");
  const isOtherTab = !!document.querySelector("#others"); // その他タブかどうかを判定

  if (!groupId) {
    console.error("グループIDが指定されていません");
    return;
  }

  if (isOtherTab) {
    console.log("その他タブの処理を実行します...");
    initializeOtherTab();
    return;
  }

  console.log("その他タブ以外の処理を実行中...");
  initializeTable();

  // その他タブの初期化
  function initializeOtherTab() {
    let currentSortOrder = "asc"; // ソート順を管理

    // 項目名列（例:「好き」「身長」）でソートを実行
    function handleSort() {
      const rows = Array.from(tableBody.querySelectorAll("tr"));

      currentSortOrder = currentSortOrder === "asc" ? "desc" : "asc";

      rows.sort((rowA, rowB) => {
        const cellA = rowA.children[0]?.textContent.trim(); // 項目名列（1列目）
        const cellB = rowB.children[0]?.textContent.trim(); // 項目名列（1列目）

        return currentSortOrder === "asc"
          ? cellA.localeCompare(cellB)
          : cellB.localeCompare(cellA);
      });

      rows.forEach((row) => tableBody.appendChild(row));
    }

    async function fetchAndInitializeOtherTab() {
      try {
        const response = await fetch(
          `/momoya_character_manager/fetch_others.php?group_id=${groupId}`
        );
        const data = await response.json();

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
      itemHeader.style.cursor = "pointer"; // ソート可能を示すスタイル
      itemHeader.addEventListener("click", handleSort); // ソートをバインド
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
      tableBody.innerHTML = "";

      items.forEach((item) => {
        const row = document.createElement("tr");
        row.dataset.itemId = item.item_id;

        const itemCell = document.createElement("td");
        itemCell.textContent = item.item_name; // 項目名（例:「好き」「身長」）
        row.appendChild(itemCell);

        characters.forEach((character) => {
          const valueCell = document.createElement("td");
          valueCell.textContent = item.values[character.id] || ""; // キャラクター列の値
          row.appendChild(valueCell);
        });

        const actionCell = document.createElement("td");

        // 編集ボタン
        const editButton = document.createElement("button");
        editButton.textContent = "編集";
        editButton.addEventListener("click", (event) => {
          const row = event.target.closest("tr");
          const isEditing = editButton.textContent === "完了";

          Array.from(row.querySelectorAll("td")).forEach((cell, index) => {
            // 項目名セル（先頭のセル）と操作セル（末尾のセル）はスキップ
            if (index === 0 || index === row.cells.length - 1) return;

            const input = cell.querySelector("input");
            if (input) {
              if (isEditing) {
                input.style.display = "none";
                cell.textContent = input.value;
              } else {
                input.style.display = "inline-block";
                input.focus();
                cell.textContent = "";
                cell.appendChild(input);
              }
            } else if (!isEditing) {
              const originalText = cell.textContent.trim();
              const input = document.createElement("input");
              input.type = "text";
              input.value = originalText;
              cell.textContent = "";
              cell.appendChild(input);
            }
          });

          editButton.textContent = isEditing ? "編集" : "完了";

          if (isEditing) {
            saveRowValues(row);
          }
        });
        actionCell.appendChild(editButton);

        // 削除ボタン
        const deleteButton = document.createElement("button");
        deleteButton.textContent = "削除";
        deleteButton.addEventListener("click", async () => {
          if (confirm("この行を削除しますか？")) {
            const result = await deleteItem(item.item_id);
            if (result.success) row.remove();
          }
        });
        actionCell.appendChild(deleteButton);

        row.appendChild(actionCell);
        tableBody.appendChild(row);
      });
    }

    fetchAndInitializeOtherTab();
  }

  // その他タブ以外の初期化
  function initializeTable() {
    console.log("その他タブ以外のテーブル初期化処理");
    // 必要な初期化処理を記述
  }

  async function saveRowValues(row) {
    const updates = [];

    row.querySelectorAll("input").forEach((input, index) => {
      const characterId =
        tableHeadRow.children[index + 1]?.dataset?.characterId; // ヘッダーからキャラクターIDを取得
      const itemId = row.dataset.itemId;
      const value = input.value.trim();

      if (characterId && itemId) {
        updates.push({
          character_id: characterId,
          item_id: itemId,
          value: value
        });
      }
    });

    if (updates.length === 0) {
      alert("変更されたデータがありません。");
      return;
    }

    try {
      const response = await fetch(
        "/momoya_character_manager/api/update_value.php",
        {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ updates })
        }
      );

      const data = await response.json();

      if (!data.success) {
        alert("値の保存に失敗しました");
      }
    } catch (error) {
      alert("通信エラーが発生しました。");
    }
  }

  async function deleteItem(itemId) {
    try {
      const response = await fetch(
        `/momoya_character_manager/api/delete_item.php`,
        {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ item_id: itemId })
        }
      );
      return await response.json();
    } catch (error) {
      return { success: false };
    }
  }

  addItemButton.addEventListener("click", async () => {
    const itemName = newItemInput.value.trim();
    if (!itemName) {
      alert("項目名を入力してください");
      return;
    }
    try {
      const response = await fetch(
        `/momoya_character_manager/api/save_item.php`,
        {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ group_id: groupId, item_name: itemName })
        }
      );
      const result = await response.json();
      if (result.success) {
        initializeOtherTab();
      } else {
        alert("項目の追加に失敗しました");
      }
    } catch (error) {
      alert("通信エラーが発生しました");
    }
  });
});
