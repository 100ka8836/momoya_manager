// assets/js/apiHelpers.js

export async function fetchOtherItems(groupId) {
  const response = await fetch(
    `/momoya_character_manager/api/fetchOtherItems.php?group_id=${groupId}`
  );
  return response.json();
}

export async function saveEditedRow(data) {
  const response = await fetch(
    "/momoya_character_manager/api/saveEditedRow.php",
    {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data)
    }
  );
  return response.json();
}

export async function deleteOtherItem(itemId) {
  const response = await fetch(
    "/momoya_character_manager/api/delete_other_item.php", // 修正されたパス
    {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `item_id=${itemId}` // POSTデータ
    }
  );
  return response.json();
}
