// assets/js/edit_toggle.js
document.addEventListener("DOMContentLoaded", () => {
  // 基本タブの編集ボタン表示切り替え
  document
    .getElementById("toggle-basic-edit-mode")
    .addEventListener("click", () => {
      document.querySelectorAll("#basic .edit-button").forEach((btn) => {
        btn.style.display =
          btn.style.display === "none" ? "inline-block" : "none";
      });
    });

  // 能力タブの編集ボタン表示切り替え
  document
    .getElementById("toggle-abilities-edit-mode")
    .addEventListener("click", () => {
      document.querySelectorAll("#abilities .edit-button").forEach((btn) => {
        btn.style.display =
          btn.style.display === "none" ? "inline-block" : "none";
      });
    });

  // 技能タブの編集ボタン表示切り替え
  document
    .getElementById("toggle-skills-edit-mode")
    .addEventListener("click", () => {
      document.querySelectorAll("#skills .edit-button").forEach((btn) => {
        btn.style.display =
          btn.style.display === "none" ? "inline-block" : "none";
      });
    });
});
