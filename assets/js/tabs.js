(function () {
  document.addEventListener("DOMContentLoaded", () => {
    const tabButtons = document.querySelectorAll(".tab-button");
    const tabContents = document.querySelectorAll(".tab-content");

    tabButtons.forEach((button) => {
      button.addEventListener("click", () => {
        tabButtons.forEach((btn) => btn.classList.remove("active"));
        tabContents.forEach((content) => content.classList.remove("active"));

        button.classList.add("active");
        const tabId = button.dataset.tab;
        const activeContent = document.getElementById(tabId);
        if (activeContent) {
          activeContent.classList.add("active");
        }
      });
    });
  });
})();
