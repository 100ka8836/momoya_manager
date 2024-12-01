document.addEventListener("DOMContentLoaded", () => {
  // すべてのテーブルを対象にする
  const tables = document.querySelectorAll("table");

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

        // 条件に合う行と合わない行に分ける
        const matchingRows = rows.filter((row) =>
          row
            .querySelector("td:first-child") // 最初の列を検索対象にする
            ?.textContent.toLowerCase()
            .includes(query)
        );
        const nonMatchingRows = rows.filter(
          (row) => !matchingRows.includes(row)
        );

        // tbody をクリアして、検索に合致する行を先頭に並べ替える
        tbody.innerHTML = "";
        matchingRows.forEach((row) => tbody.appendChild(row)); // 該当行を先頭に追加
        nonMatchingRows.forEach((row) => tbody.appendChild(row)); // それ以外を後ろに追加
      });
    }
  });
});
