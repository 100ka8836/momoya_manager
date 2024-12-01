document.addEventListener("DOMContentLoaded", () => {
  const editButton = document.getElementById("edit-table-btn");
  const saveButton = document.getElementById("save-table-btn");
  const tableBody = document.getElementById("table-body");
  let isEditing = false;

  // 編集モードを有効にする
  function enableEditMode() {
    isEditing = true;
    editButton.style.display = "none"; // 編集ボタンを非表示
    saveButton.style.display = "inline-block"; // 完了ボタンを表示

    Array.from(tableBody.querySelectorAll("td")).forEach((cell) => {
      if (!cell.classList.contains("delete-cell")) {
        const originalText = cell.textContent.trim();
        const input = document.createElement("input");
        input.type = "text";
        input.value = originalText === "-" ? "" : originalText; // "-" は空白に
        cell.textContent = "";
        cell.appendChild(input);
      }
    });
  }

  // 編集モードを無効にしてデータを保存
  async function disableEditMode() {
    isEditing = false;
    editButton.style.display = "inline-block"; // 編集ボタンを表示
    saveButton.style.display = "none"; // 完了ボタンを非表示

    const updatedData = [];

    Array.from(tableBody.querySelectorAll("tr")).forEach((row) => {
      const itemId = row.dataset.itemId;
      const rowValues = [];

      Array.from(row.querySelectorAll("td")).forEach((cell) => {
        const input = cell.querySelector("input");
        if (input) {
          const value = input.value.trim() || "-";
          rowValues.push(value);
          cell.textContent = value; // フォームをテキストに戻す
        }
      });

      updatedData.push({ item_id: itemId, values: rowValues });
    });

    try {
      const response = await fetch("api/saveOtherItems.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({ updates: updatedData })
      });
      const result = await response.json();
      if (!result.success) {
        console.error("保存エラー:", result.message);
        alert("保存中にエラーが発生しました: " + result.message);
      } else {
        alert("保存が完了しました");
      }
    } catch (error) {
      console.error("通信エラー:", error);
    }
  }

  // 編集ボタンのイベント
  editButton.addEventListener("click", () => {
    if (!isEditing) enableEditMode();
  });

  // 完了ボタンのイベント
  saveButton.addEventListener("click", () => {
    if (isEditing) disableEditMode();
  });
});
