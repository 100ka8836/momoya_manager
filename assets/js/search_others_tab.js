document.addEventListener("DOMContentLoaded", () => {
  const tableBody = document.getElementById("table-body");

  // 検索機能のセットアップ
  const setupSearch = () => {
    const searchInput = document.querySelector("#others .column-search"); // その他タブの検索ボックスを取得

    if (!searchInput) {
      console.error("検索ボックスが見つかりません");
      return;
    }

    const getRows = () => Array.from(tableBody.querySelectorAll("tr")); // 最新の行を取得

    searchInput.addEventListener("input", () => {
      const query = searchInput.value.trim().toLowerCase();

      const rows = getRows(); // 動的に生成された行を含めて取得

      if (!query) {
        // 検索が空の場合は元の順序に戻す
        rows.forEach((row) => tableBody.appendChild(row));
        return;
      }

      // 条件に合う行と合わない行に分ける
      const matchingRows = rows.filter((row) =>
        row
          .querySelector("td:first-child") // 最初の列を検索対象にする
          ?.textContent.toLowerCase()
          .includes(query)
      );
      const nonMatchingRows = rows.filter((row) => !matchingRows.includes(row));

      // tbody をクリアして、検索に合致する行を先頭に並べ替える
      tableBody.innerHTML = "";
      matchingRows.forEach((row) => tableBody.appendChild(row)); // 該当行を先頭に追加
      nonMatchingRows.forEach((row) => tableBody.appendChild(row)); // それ以外を後ろに追加
    });
  };

  // 検索機能を初期化
  setupSearch();
});
