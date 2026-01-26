<?php
/**
 * =====================================================
 * 儲存商品 API / Save Product API
 * =====================================================
 * 功能：新增或更新商品資料
 * Purpose: Insert or update product data
 * 
 * 接收參數 / Parameters:
 * - id: 商品ID（編輯時）/ Product ID (for edit)
 * - category_id: 類別ID / Category ID
 * - title: 商品名稱 / Product title
 * - description: 商品描述 / Product description
 * - price: 價格 / Price
 * - stock: 庫存 / Stock
 * - sh: 是否顯示 / Show status
 * - image: 圖片檔案 / Image file
 * =====================================================
 */

include_once "db.php";

// 驗證登入 / Verify login
if (!isset($_SESSION['admin'])) {
    to("../index.php?page=login");
}

// 只處理 POST 請求 / Only handle POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    to("../back.php?do=products");
}

// 取得表單資料 / Get form data
$id = intval($_POST['id'] ?? 0);
$category_id = intval($_POST['category_id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$price = floatval($_POST['price'] ?? 0);
$stock = intval($_POST['stock'] ?? 99);
$sh = isset($_POST['sh']) ? 1 : 0;

// 驗證必填欄位 / Validate required fields
if (empty($title) || $category_id <= 0 || $price < 0) {
    echo "<script>alert('請填寫必要欄位 / Please fill required fields'); history.back();</script>";
    exit;
}

// 處理圖片上傳 / Handle image upload
$img = '';
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = "../pic/";
    
    // 生成唯一檔名 / Generate unique filename
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $newFilename = 'product_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
    $uploadPath = $uploadDir . $newFilename;
    
    // 驗證檔案類型 / Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (in_array($_FILES['image']['type'], $allowedTypes)) {
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
            $img = $newFilename;
        }
    }
}

// 準備資料 / Prepare data
$data = [
    'category_id' => $category_id,
    'title' => $title,
    'description' => $description,
    'price' => $price,
    'stock' => $stock,
    'sh' => $sh
];

// 如果有上傳新圖片 / If new image uploaded
if (!empty($img)) {
    $data['img'] = $img;
}

// 新增或更新 / Insert or update
if ($id > 0) {
    // 更新 / Update
    $data['id'] = $id;
    
    // 如果沒有上傳新圖片，保留原圖 / Keep original image if no new upload
    if (empty($img)) {
        $existing = $Product->find($id);
        if ($existing) {
            $data['img'] = $existing['img'];
        }
    }
    
    $Product->save($data);
    $message = '商品已更新 / Product updated';
} else {
    // 新增 / Insert
    if (empty($img)) {
        $data['img'] = 'placeholder.jpg'; // 預設圖片 / Default image
    }
    
    $Product->insert($data);
    $message = '商品已新增 / Product added';
}

// 導回商品管理頁 / Redirect to products page
echo "<script>alert('{$message}'); location.href='../back.php?do=products';</script>";
?>
