document.addEventListener("DOMContentLoaded", () => {
  const tables = document.querySelectorAll("table[id='sortable-table']");

  if (!tables) return;

  tables.forEach((table) => {
    const tbody = table.querySelector("tbody");
    const rows = Array.from(tbody.querySelectorAll("tr"));

    let sortOrder = "asc"; // 初期状態は昇順

    // 各行の最初のセルをクリック可能にする
    rows.forEach((row) => {
      const firstCell = row.querySelector("td:first-child");

      if (!firstCell) return;

      firstCell.addEventListener("click", () => {
        // クリックされた行のすべてのセルを取得（最後の列は操作列として除外）
        const cells = Array.from(row.querySelectorAll("td")).slice(1, -1);

        // 並び替え基準データを取得
        const columnData = cells.map((cell, index) => ({
          index: index + 1, // 列番号（1列目を除外し、最後の列も除外）
          value: isNaN(cell.textContent.trim())
            ? normalizeText(cell.textContent.trim()) // テキストを正規化
            : parseFloat(cell.textContent.trim()) // 数値の場合
        }));

        // 昇順・降順の切り替え
        columnData.sort((a, b) => {
          if (sortOrder === "asc") {
            return a.value > b.value ? 1 : -1;
          } else {
            return a.value < b.value ? 1 : -1;
          }
        });

        // ソート順序を反転
        sortOrder = sortOrder === "asc" ? "desc" : "asc";

        // ソートされた列順序に基づいてテーブルを再構築
        const newOrder = columnData.map((data) => data.index);
        const headerRow = table.querySelector("thead tr");
        const allRows = [headerRow, ...rows];

        allRows.forEach((tr) => {
          const cells = Array.from(tr.children);
          const reorderedCells = [
            cells[0], // 最初の列
            ...newOrder.map((i) => cells[i]), // ソート対象の列
            cells[cells.length - 1] // 最後の列（操作列）
          ];
          reorderedCells.forEach((cell, i) => tr.appendChild(cell));
        });
      });
    });
  });

  /**
   * テキストを正規化して比較可能な状態に変換
   * - 大文字小文字を区別しない
   * - 半角全角を統一
   * @param {string} text
   * @returns {string}
   */
  function normalizeText(text) {
    return text
      .toLocaleLowerCase() // 大文字小文字を統一
      .normalize("NFKC"); // 半角全角を統一
  }
});
