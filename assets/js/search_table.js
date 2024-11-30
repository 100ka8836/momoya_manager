document.addEventListener("DOMContentLoaded", () => {
  const tables = document.querySelectorAll("table[id='sortable-table']");

  tables.forEach((table) => {
    const searchInput = table
      .closest(".tab-content")
      .querySelector(".column-search");
    const tbody = table.querySelector("tbody");
    const rows = Array.from(tbody.querySelectorAll("tr"));

    if (searchInput) {
      searchInput.addEventListener("input", () => {
        const query = searchInput.value.trim().toLowerCase();

        if (!query) {
          // 検索が空の場合は元の順序に戻す
          rows.forEach((row) => tbody.appendChild(row));
          return;
        }

        // 検索条件に合致する行を一時的に先頭に移動
        const matchingRows = rows.filter((row) =>
          row
            .querySelector("td:first-child")
            .textContent.toLowerCase()
            .includes(query)
        );
        const nonMatchingRows = rows.filter(
          (row) => !matchingRows.includes(row)
        );

        tbody.innerHTML = ""; // テーブルを一旦クリア
        matchingRows.forEach((row) => tbody.appendChild(row)); // 該当行を先頭に追加
        nonMatchingRows.forEach((row) => tbody.appendChild(row)); // それ以外を追加
      });
    }
  });
});
