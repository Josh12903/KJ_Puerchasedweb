<?php
/**
 * =====================================================
 * 儲存類別 API / Save Categories API
 * =====================================================
 * 功能：批量更新類別資料
 * Purpose: Batch update category data
 * 
 * 接收參數 / Parameters:
 * - id[]: 類別ID陣列 / Category ID array
 * - name[]: 名稱陣列 / Name array
 * - icon[]: 圖示陣列 / Icon array
 * - description[]: 描述陣列 / Description array
 * - sort_order[]: 排序陣列 / Sort order array
 * - sh[]: 顯示的ID陣列 / Shown IDs array
 * =====================================================
 */

include_once "db.php";

// 驗證登入 / Verify login
if (!isset($_SESSION['admin'])) {
    to("../index.php?page=login");
}

// 只處理 POST 請求 / Only handle POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    to("../back.php?do=categories");
}

// 取得表單資料 / Get form data
$ids = $_POST['id'] ?? [];
$names = $_POST['name'] ?? [];
$icons = $_POST['icon'] ?? [];
$descriptions = $_POST['description'] ?? [];
$sortOrders = $_POST['sort_order'] ?? [];
$shownIds = $_POST['sh'] ?? [];

// 更新每個類別 / Update each category
foreach ($ids as $index => $id) {
    $id = intval($id);
    if ($id <= 0) continue;
    
    $data = [
        'id' => $id,
        'name' => trim($names[$index] ?? ''),
        'icon' => trim($icons[$index] ?? ''),
        'description' => trim($descriptions[$index] ?? ''),
        'sort_order' => intval($sortOrders[$index] ?? 0),
        'sh' => in_array($id, $shownIds) ? 1 : 0
    ];
    
    $Category->save($data);
}

// 導回類別管理頁 / Redirect to categories page
echo "<script>alert('類別已更新 / Categories updated'); location.href='../back.php?do=categories';</script>";
?>
