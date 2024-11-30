document.addEventListener("DOMContentLoaded", () => {
  const tabButtons = document.querySelectorAll(".tab-button");
  const tabContents = document.querySelectorAll(".tab-content");

  // ページ読み込み時に localStorage からタブの情報を取得して復元
  const savedTab = localStorage.getItem("activeTab");
  if (savedTab) {
    const activeButton = document.querySelector(
      `.tab-button[data-tab="${savedTab}"]`
    );
    const activeContent = document.getElementById(savedTab);

    if (activeButton && activeContent) {
      tabButtons.forEach((btn) => btn.classList.remove("active"));
      tabContents.forEach((content) => content.classList.remove("active"));

      activeButton.classList.add("active");
      activeContent.classList.add("active");
    }
  }

  // タブの切り替えイベント
  tabButtons.forEach((button) => {
    button.addEventListener("click", () => {
      // 他のタブを非アクティブにする
      tabButtons.forEach((btn) => btn.classList.remove("active"));
      tabContents.forEach((content) => content.classList.remove("active"));

      // クリックされたタブをアクティブにする
      button.classList.add("active");
      const tabId = button.dataset.tab;
      const activeContent = document.getElementById(tabId);
      if (activeContent) {
        activeContent.classList.add("active");

        // 現在のタブを localStorage に保存
        localStorage.setItem("activeTab", tabId);
      }
    });
  });
});

document.addEventListener("DOMContentLoaded", () => {
  const activeTab = localStorage.getItem("activeTab") || "basic";
  const activeButton = document.querySelector(
    `.tab-button[data-tab="${activeTab}"]`
  );
  const activeContent = document.getElementById(activeTab);

  if (activeButton && activeContent) {
    document
      .querySelectorAll(".tab-button")
      .forEach((btn) => btn.classList.remove("active"));
    document
      .querySelectorAll(".tab-content")
      .forEach((content) => content.classList.remove("active"));

    activeButton.classList.add("active");
    activeContent.classList.add("active");
  }
});
