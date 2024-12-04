document.addEventListener("DOMContentLoaded", () => {
  const tableHeadRow = document.getElementById("table-head-row");
  const tableBody = document.getElementById("table-body");
  const groupId = new URLSearchParams(window.location.search).get("group_id");
  const addItemButton = document.getElementById("add-item-btn");
  const newItemInput = document.getElementById("new-item-name");
  const isOtherTab = document.querySelector("#others"); // その他タブかどうかを判定

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

        const editButton = document.createElement("button");
        editButton.textContent = "編集";
        actionCell.appendChild(editButton);

        const deleteButton = document.createElement("button");
        deleteButton.textContent = "削除";
        actionCell.appendChild(deleteButton);

        row.appendChild(actionCell);
        tableBody.appendChild(row);
      });
    }

    fetchAndInitializeOtherTab();
  }

  // その他タブ以外の初期化
  async function initializeTable() {
    console.log("その他タブ以外のテーブル初期化処理");
    // その他タブ以外で行う処理を書く
  }
});
