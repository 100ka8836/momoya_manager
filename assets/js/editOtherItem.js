async function disableEditMode() {
  isEditing = false;
  editButton.style.display = "inline-block";
  saveButton.style.display = "none";

  const updatedData = [];

  // 各行を処理
  Array.from(characterTableBody.querySelectorAll("tr")).forEach((row) => {
    const itemId = row.dataset.itemId; // 行のアイテムIDを取得

    const values = Array.from(row.querySelectorAll("td"))
      .filter((cell) => !cell.classList.contains("delete-cell")) // 削除セルを無視
      .map((cell, index) => {
        const input = cell.querySelector("input");
        const value = input ? input.value.trim() : cell.textContent.trim();

        // ヘッダーからキャラクターIDを取得
        const characterId = document.querySelector(
          `#table-head-row th:nth-child(${index + 2})`
        )?.dataset.characterId;

        if (input) cell.textContent = value || "-"; // 入力欄を元の表示に戻す

        return {
          value: value || "-",
          character_id: characterId
        };
      });

    values.forEach((value) => {
      updatedData.push({ item_id: itemId, ...value });
    });
  });

  // サーバーにデータを送信
  try {
    const response = await fetch("saveEditedRow.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({ updates: updatedData })
    });

    const result = await response.json();
    if (!result.success) {
      console.error("保存エラー:", result.message);
      alert(`保存中にエラーが発生しました: ${result.message}`);
    } else {
      alert("変更が保存されました");
    }
  } catch (error) {
    console.error("通信エラー:", error);
    alert("通信エラーが発生しました");
  }
}
