<?php
/**
 * =====================================================
 * 刪除商品 API / Delete Product API
 * =====================================================
 * 功能：刪除指定商品
 * Purpose: Delete specified product
 * 
 * 接收參數 / Parameters:
 * - id: 商品ID / Product ID (GET)
 * =====================================================
 */

include_once "db.php";

// 驗證登入 / Verify login
if (!isset($_SESSION['admin'])) {
    to("../index.php?page=login");
}

// 取得商品ID / Get product ID
$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    // 取得商品資料（可選：刪除圖片）/ Get product data (optional: delete image)
    $product = $Product->find($id);
    
    // 刪除商品 / Delete product
    $Product->del($id);
    
    // 可選：刪除圖片檔案 / Optional: delete image file
    // if ($product && $product['img'] && $product['img'] !== 'placeholder.jpg') {
    //     $imgPath = "../pic/" . $product['img'];
    //     if (file_exists($imgPath)) {
    //         unlink($imgPath);
    //     }
    // }
    
    echo "<script>alert('商品已刪除 / Product deleted'); location.href='../back.php?do=products';</script>";
} else {
    echo "<script>alert('無效的商品ID / Invalid product ID'); history.back();</script>";
}
?>
