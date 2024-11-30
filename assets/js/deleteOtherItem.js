document.addEventListener("DOMContentLoaded", () => {
  const characterTableBody = document.querySelector("#character-table tbody");

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
        headers: {
          "Content-Type": "application/x-www-form-urlencoded"
        },
        body: new URLSearchParams({ item_id: itemId })
      });

      const result = await response.json();
      if (result.success) {
        row.remove(); // テーブルから行を削除
        console.log(`行が削除されました: ${itemId}`);
      } else {
        console.error(`削除エラー: ${result.message}`);
      }
    } catch (error) {
      console.error("削除リクエストエラー:", error);
    }
  }

  // 削除ボタンのクリックイベントを設定
  characterTableBody.addEventListener("click", (event) => {
    if (event.target.classList.contains("delete-row-btn")) {
      const row = event.target.closest("tr");
      const confirmDelete = confirm("この行を削除しますか？");
      if (confirmDelete) {
        deleteRow(row);
      }
    }
  });
});
