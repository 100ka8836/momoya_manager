document.addEventListener("DOMContentLoaded", () => {
  const groupNames = document.querySelectorAll(".group-name"); // グループ名
  const modal = document.getElementById("password-modal"); // モーダル
  const modalClose = document.getElementById("modal-close"); // モーダルを閉じるボタン
  const groupIdInput = document.getElementById("group-id"); // 隠しフィールドのグループID
  const passwordForm = document.getElementById("password-form"); // パスワードフォーム
  const errorMessage = document.getElementById("error-message"); // エラーメッセージ

  // グループ名をクリックするとモーダルを表示
  groupNames.forEach((groupName) => {
    groupName.addEventListener("click", () => {
      const groupId = groupName.dataset.groupId; // グループIDを取得
      groupIdInput.value = groupId; // 隠しフィールドにセット
      modal.style.display = "flex"; // モーダルを表示
    });
  });

  // モーダルを閉じる
  modalClose.addEventListener("click", () => {
    modal.style.display = "none";
    errorMessage.style.display = "none"; // エラーメッセージを非表示
  });

  // モーダル外をクリックして閉じる
  window.addEventListener("click", (e) => {
    if (e.target === modal) {
      modal.style.display = "none";
      errorMessage.style.display = "none";
    }
  });

  // パスワードフォーム送信時の処理
  passwordForm.addEventListener("submit", async (event) => {
    event.preventDefault(); // デフォルトの送信を防ぐ
    const formData = new FormData(passwordForm);

    try {
      const response = await fetch("group_password_handler.php", {
        method: "POST",
        body: formData
      });

      const result = await response.json();
      if (result.success) {
        // 成功時: グループページへ移動
        window.location.href = `group_page.php?group_id=${groupIdInput.value}`;
      } else {
        // 失敗時: エラーメッセージを表示
        errorMessage.textContent = result.message;
        errorMessage.style.display = "block";
      }
    } catch (error) {
      console.error("通信エラー:", error);
    }
  });
});
