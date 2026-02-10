<?php 
/**
 * =====================================================
 * 現代化一頁式電商網站 / Modern Single-Page E-commerce Website
 * =====================================================
 * 功能：RWD響應式設計、三類別商品展示、購物車功能
 * Purpose: Responsive design, 3-category product display, shopping cart
 * 
 * 技術棧 / Tech Stack:
 * - Tailwind CSS v4 (CDN)
 * - Google Fonts (Noto Sans TC)
 * - PHP + MySQL
 * - jQuery (for interactions)
 * =====================================================
 */

include_once "./api/db.php";

// =====================================================
// 處理 AJAX 請求 / Handle AJAX Requests
// =====================================================
if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];
    
    switch ($action) {
        // 加入購物車 / Add to cart
        case 'add_to_cart':
            $productId = intval($_POST['product_id'] ?? 0);
            $quantity = intval($_POST['quantity'] ?? 1);
            $result = addToCart($productId, $quantity);
            $cartTotal = getCartTotal();
            echo json_encode([
                'success' => $result,
                'cart_count' => $cartTotal['count'],
                'message' => $result ? '已加入購物車 / Added to cart' : '加入失敗 / Failed to add'
            ]);
            exit;
            
        // 更新購物車數量 / Update cart quantity
        case 'update_cart':
            $cartItemId = intval($_POST['cart_item_id'] ?? 0);
            $quantity = intval($_POST['quantity'] ?? 1);
            $result = updateCartQuantity($cartItemId, $quantity);
            $cartTotal = getCartTotal();
            echo json_encode([
                'success' => $result,
                'cart_count' => $cartTotal['count'],
                'cart_total' => $cartTotal['total']
            ]);
            exit;
            
        // 從購物車移除 / Remove from cart
        case 'remove_from_cart':
            $cartItemId = intval($_POST['cart_item_id'] ?? 0);
            $result = removeFromCart($cartItemId);
            $cartTotal = getCartTotal();
            echo json_encode([
                'success' => $result,
                'cart_count' => $cartTotal['count'],
                'cart_total' => $cartTotal['total']
            ]);
            exit;
            
        // 結帳 / Checkout
        case 'checkout':
            $customerInfo = [
                'name' => $_POST['name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'address' => $_POST['address'] ?? '',
                'notes' => $_POST['notes'] ?? ''
            ];
            $orderId = createOrder($customerInfo);
            if ($orderId) {
                $order = $Order->find($orderId);
                echo json_encode([
                    'success' => true,
                    'order_number' => $order['order_number'],
                    'message' => '訂單已建立 / Order created'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => '訂單建立失敗 / Order creation failed'
                ]);
            }
            exit;
    }
}

// 取得當前頁面 / Get current page
$page = $_GET['page'] ?? 'home';
// 取得選中的類別 / Get selected category
$selectedCategory = $_GET['category'] ?? '';

// 取得購物車數量 / Get cart count
$cartTotal = getCartTotal();

// 取得所有類別 / Get all categories
$categories = $Category->all(['sh' => 1], ' ORDER BY sort_order ASC');

// 取得標題資料 / Get title data
$title = $Title->find(['sh' => 1]);

// 取得廣告文字 / Get ad text
$ads = $Ad->all(['sh' => 1]);

// 取得頁尾資料 / Get footer data
$bottom = $Bottom->find(1);
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO 優化 / SEO Optimization -->
    <title>Lily's shop</title>
    <meta name="description" content="熱門ACG周邊、日用美妝、健康保健食品代購網站">
    
    <!-- =====================================================
    字體載入 / Font Loading
    使用 Google Fonts - Noto Sans TC (中文優化)
    Using Google Fonts - Noto Sans TC (Chinese optimized)
    可自訂字重 / Customizable font weights: 300, 400, 500, 700
    ===================================================== -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/css.css">
    
    <!-- =====================================================
    Tailwind CSS v4 CDN
    如遇相容問題可切換到 Bootstrap 5
    If compatibility issues, switch to Bootstrap 5
    ===================================================== -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Tailwind 自訂設定 / Tailwind Custom Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    // 自訂字體 / Custom fonts
                    fontFamily: {
                        'sans': ['Noto Sans TC', 'sans-serif'],
                    },
                    // 自訂顏色 / Custom colors - 可自行修改 / Customizable
                    colors: {
                        'primary': '#34b35a',      // 主色調 / Primary color
                        'secondary': '#c2d68c',    // 次要色 / Secondary color
                        'accent': '#ffbe4f',       // 強調色 / Accent color
                        'dark': '#3028a0',         // 深色背景 / Dark background
                    }
                }
            }
        }
    </script>
    
    <!-- 自訂樣式 / Custom Styles -->
    <style>
        /* =====================================================
        全域樣式 / Global Styles
        ===================================================== */
        body {
            font-family: 'Noto Sans TC', sans-serif;
        }
        
        /* 平滑滾動 / Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }
        
        /* 類別按鈕動畫 / Category button animation */
        .category-btn {
            transition: all 0.3s ease;
        }
        .category-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 40px rgba(99, 102, 241, 0.3);
        }
        .category-btn.active {
            background: linear-gradient(135deg, #3134d1, #5cf671);
            color: white;
            transform: scale(1.05);
        }
        
        /* 商品卡片動畫 / Product card animation */
        .product-card {
            transition: all 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        /* 漸層背景 / Gradient backgrounds */
        .gradient-bg {
            background: linear-gradient(135deg, #3551cf 0%, #5ca24b 100%);
        }
        
        /* 玻璃效果 / Glassmorphism effect */
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        /* 跑馬燈優化樣式 / Marquee optimized style */
        .marquee-text {
            animation: marquee 20s linear infinite;
        }
        @keyframes marquee {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
        
        /* 購物車徽章 / Cart badge */
        .cart-badge {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        /* 模態框動畫 / Modal animation */
        .modal-enter {
            animation: fadeIn 0.3s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
    
    <!-- jQuery -->
    <script src="./js/jquery-1.9.1.min.js"></script>
</head>

<body class="bg-gray-50 min-h-screen">
    
    <!-- =====================================================
    頂部導航列 / Top Navigation Bar
    包含：Logo、類別導航、購物車
    Contains: Logo, category nav, cart
    ===================================================== -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md shadow-sm">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                
                <!-- Logo 區域 / Logo Area - 可自訂圖片 / Customizable image -->
                <a href="index.php" class="flex items-center space-x-2">
                    <!-- 如果有 logo 圖片，取消註解以下行 / Uncomment below if you have logo image -->
                    <!-- <img src="./pic/logo.png" alt="Logo" class="h-10 w-auto"> -->
                    <span class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-primary to-secondary">
                        莉莉代購 | 
                    </span>
                    <span class="font-bold" style="color:orange"> Lily's shop</span>
                </a>
                
                <!-- 桌面版導航 / Desktop Navigation -->
                <nav class="hidden md:flex items-center space-x-6">
                    <a href="?page=home" class="text-gray-700 hover:text-primary transition font-medium">
                        首頁 / Home
                    </a>
                    <?php foreach ($categories as $cat): ?>
                    <a href="?page=home&category=<?= $cat['slug'] ?>" 
                       class="text-gray-700 hover:text-primary transition font-medium">
                        <?= $cat['icon'] ?> <?= $cat['name'] ?>
                    </a>
                    <?php endforeach; ?>
                </nav>
                
                <!-- 右側按鈕 / Right Side Buttons -->
                <div class="flex items-center space-x-4">
                    
                    <!-- 購物車按鈕 / Cart Button -->
                    <a href="?page=cart" class="relative p-2 text-gray-700 hover:text-primary transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <!-- 購物車數量徽章 / Cart count badge -->
                        <span id="cart-badge" class="cart-badge absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center <?= $cartTotal['count'] > 0 ? '' : 'hidden' ?>">
                            <?= $cartTotal['count'] ?>
                        </span>
                    </a>
                    
                    <!-- 登入/管理按鈕 / Login/Admin Button -->
                    <?php if (isset($_SESSION['admin'])): ?>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600">👤 <?= $_SESSION['admin'] ?></span>
                        <a href="back.php" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition text-sm">
                            管理後台
                        </a>
                        <a href="./api/signout.php" class="px-3 py-2 text-gray-600 hover:text-red-500 transition text-sm">
                            登出
                        </a>
                    </div>
                    <?php else: ?>
                    <a href="?page=login" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition text-sm font-medium">
                        管理登入
                    </a>
                    <?php endif; ?>
                    
                    <!-- 手機版選單按鈕 / Mobile Menu Button -->
                    <button id="mobile-menu-btn" class="md:hidden p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- 手機版選單 / Mobile Menu -->
            <div id="mobile-menu" class="md:hidden hidden pb-4">
                <a href="?page=home" class="block py-2 text-gray-700 hover:text-primary">首頁</a>
                <?php foreach ($categories as $cat): ?>
                <a href="?page=home&category=<?= $cat['slug'] ?>" class="block py-2 text-gray-700 hover:text-primary">
                    <?= $cat['icon'] ?> <?= $cat['name'] ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </header>
    
    <!-- 佔位空間（因為導航列固定）/ Spacer for fixed header -->
    <div class="h-16"></div>
    
    <?php
    // =====================================================
    // 頁面路由 / Page Routing
    // 根據 ?page= 參數顯示不同內容
    // Display different content based on ?page= parameter
    // =====================================================
    switch ($page):
        case 'home':
        default:
    ?>
    
    <!-- =====================================================
    HERO 區域 / HERO Section
    可自訂背景圖片和文字 / Customizable background image and text
    ===================================================== -->
    <section class="relative gradient-bg text-white py-20 md:py-32 overflow-hidden">
        <!-- 背景裝飾 / Background decoration -->
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-10 left-10 w-72 h-72 bg-white rounded-full blur-3xl"></div>
            <div class="absolute bottom-10 right-10 w-96 h-96 bg-purple-300 rounded-full blur-3xl"></div>
        </div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-3xl mx-auto text-center">
                <!-- 主標題 / Main Title - 可自訂 / Customizable -->
                <h1 class="text-4xl md:text-6xl font-bold mb-6">
                    精選代購商品
                </h1>
                <!-- 副標題 / Subtitle - 可自訂 / Customizable -->
                <p class="md:text-2xl mb-8 text-123" style="color:#0DF;font-family:'Inter', 'Noto Sans TC', sans-serif">
                    熱門ACG周邊 · 日用美妝 · 健康保健
                </p>
                
                <!-- 跑馬燈廣告 / Marquee Ads -->
                <?php if (!empty($ads)): ?>
                <div class="glass rounded-full py-3 px-6 overflow-hidden">
                    <div class="marquee-text whitespace-nowrap font-medium">
                        <?php foreach ($ads as $ad): ?>
                            <span class="mx-8">📢 <?= $ad['text'] ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <!-- =====================================================
    類別選擇區 / Category Selection Section
    三個主要類別的大按鈕
    Three main category buttons
    ===================================================== -->
    <section class="py-12 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-8 text-gray-800">
                選擇商品類別 / Select Category
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto">
                <?php foreach ($categories as $cat): ?>
                <!-- 類別按鈕卡片 / Category Button Card -->
                <a href="?page=home&category=<?= $cat['slug'] ?>" 
                   class="category-btn block p-8 rounded-2xl border-2 border-gray-100 bg-white shadow-lg text-center
                          <?= $selectedCategory === $cat['slug'] ? 'active border-primary' : '' ?>">
                    
                    <!-- 類別圖示 / Category Icon - 可自訂為圖片 / Can customize to image -->
                    <div class="text-5xl mb-4">
                        <?= $cat['icon'] ?>
                        <!-- 如果要用圖片，取消下方註解 / Uncomment below for image -->
                        <!-- <img src="./pic/category_<?= $cat['slug'] ?>.jpg" alt="<?= $cat['name'] ?>" 
                             class="w-24 h-24 mx-auto rounded-full object-cover" loading="lazy"> -->
                    </div>
                    
                    <h3 class="text-xl font-bold mb-2 <?= $selectedCategory === $cat['slug'] ? 'text-white' : 'text-gray-800' ?>">
                        <?= $cat['name'] ?>
                    </h3>
                    <p class="text-sm <?= $selectedCategory === $cat['slug'] ? 'text-white/80' : 'text-gray-500' ?>">
                        <?= $cat['description'] ?>
                    </p>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    
    <!-- =====================================================
    商品展示區 / Products Display Section
    根據選中的類別顯示商品 Grid
    Display products grid based on selected category
    ===================================================== -->
    <section class="py-12 bg-gray-50" id="products-section">
        <div class="container mx-auto px-4">
            
            <?php if ($selectedCategory): 
                // 取得選中類別的商品 / Get products of selected category
                $currentCat = $Category->find(['slug' => $selectedCategory]);
                if ($currentCat):
                    $products = $Product->all(['category_id' => $currentCat['id'], 'sh' => 1], ' ORDER BY sort_order ASC');
            ?>
            
            <!-- 類別標題 / Category Title -->
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold text-gray-800">
                    <?= $currentCat['icon'] ?> <?= $currentCat['name'] ?>
                </h2>
                <span class="text-gray-500">共 <?= count($products) ?> 件商品</span>
            </div>
            
            <!-- =====================================================
            商品 Grid / Products Grid
            響應式設計：手機1欄、平板2欄、桌面3-4欄
            Responsive: 1 col mobile, 2 cols tablet, 3-4 cols desktop
            ===================================================== -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($products as $prod): ?>
                <!-- 商品卡片 / Product Card -->
                <div class="product-card bg-white rounded-2xl shadow-md overflow-hidden">
                    
                    <!-- 商品圖片 / Product Image -->
                    <div class="relative aspect-square bg-gray-100">
                        <!-- 使用 lazy loading 和 object-fit / Using lazy loading and object-fit -->
                        <img src="./pic/<?= $prod['img'] ?>" 
                             alt="<?= htmlspecialchars($prod['title']) ?>"
                             loading="lazy"
                             width="400"
                             height="400"
                             class="w-full h-full object-cover"
                             onerror="this.src='./pic/placeholder.jpg'">
                        
                        <!-- 價格標籤 / Price Tag -->
                        <div class="absolute top-3 right-3 bg-accent text-white px-3 py-1 rounded-full text-sm font-bold">
                            NT$ <?= number_format($prod['price']) ?>
                        </div>
                    </div>
                    
                    <!-- 商品資訊 / Product Info -->
                    <div class="p-4">
                        <h3 class="font-bold text-lg mb-2 text-gray-800 line-clamp-2">
                            <?= htmlspecialchars($prod['title']) ?>
                        </h3>
                        <p class="text-gray-500 text-sm mb-4 line-clamp-2">
                            <?= htmlspecialchars($prod['description']) ?>
                        </p>
                        
                        <!-- 加入購物車按鈕 / Add to Cart Button -->
                        <button onclick="addToCart(<?= $prod['id'] ?>)" 
                                class="w-full py-3 bg-primary text-white rounded-xl font-medium
                                       hover:bg-secondary transition flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            <span>加入購物車</span>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?php if (empty($products)): ?>
            <!-- 無商品提示 / No products message -->
            <div class="text-center py-20">
                <div class="text-6xl mb-4">📦</div>
                <p class="text-xl text-gray-500">此類別暫無商品 / No products in this category</p>
                <p class="text-gray-400 mt-2">請至後台新增商品 / Please add products in admin panel</p>
            </div>
            <?php endif; ?>
            
            <?php else: ?>
            <!-- 類別不存在 / Category not found -->
            <div class="text-center py-20">
                <p class="text-xl text-gray-500">類別不存在 / Category not found</p>
            </div>
            <?php endif; ?>
            
            <?php else: ?>
            <!-- =====================================================
            未選擇類別時顯示所有類別預覽
            Show all categories preview when no category selected
            ===================================================== -->
            
            <?php foreach ($categories as $cat): 
                $catProducts = $Product->all(['category_id' => $cat['id'], 'sh' => 1], ' ORDER BY sort_order ASC LIMIT 4');
            ?>
            <div class="mb-16">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">
                        <?= $cat['icon'] ?> <?= $cat['name'] ?>
                    </h2>
                    <a href="?page=home&category=<?= $cat['slug'] ?>" 
                       class="text-primary hover:text-secondary font-medium flex items-center">
                        查看全部
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php foreach ($catProducts as $prod): ?>
                    <div class="product-card bg-white rounded-2xl shadow-md overflow-hidden">
                        <div class="relative aspect-square bg-gray-100">
                            <img src="./pic/<?= $prod['img'] ?>" 
                                 alt="<?= htmlspecialchars($prod['title']) ?>"
                                 loading="lazy"
                                 class="w-full h-full object-cover"
                                 onerror="this.src='./pic/placeholder.jpg'">
                            <div class="absolute top-3 right-3 bg-accent text-white px-3 py-1 rounded-full text-sm font-bold">
                                NT$ <?= number_format($prod['price']) ?>
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-lg mb-2 text-gray-800 line-clamp-1">
                                <?= htmlspecialchars($prod['title']) ?>
                            </h3>
                            <button onclick="addToCart(<?= $prod['id'] ?>)" 
                                    class="w-full py-2 bg-primary text-white rounded-lg text-sm
                                           hover:bg-secondary transition">
                                加入購物車
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <?php if (empty($catProducts)): ?>
                    <div class="col-span-full text-center py-10 text-gray-400">
                        暫無商品 / No products yet
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php endif; ?>
        </div>
    </section>
    
    <?php break; ?>
    
    <?php case 'cart': ?>
    <!-- =====================================================
    購物車頁面 / Shopping Cart Page
    ===================================================== -->
    <section class="py-12 min-h-screen">
        <div class="container mx-auto px-4 max-w-4xl">
            <h1 class="text-3xl font-bold mb-8 text-gray-800">
                🛒 購物車 / Shopping Cart
            </h1>
            
            <?php 
            $cartItems = getCartItems();
            $cartTotal = getCartTotal();
            ?>
            
            <?php if (empty($cartItems)): ?>
            <!-- 空購物車 / Empty Cart -->
            <div class="text-center py-20 bg-white rounded-2xl shadow-md">
                <div class="text-8xl mb-6">🛒</div>
                <h2 class="text-2xl font-bold text-gray-700 mb-4">購物車是空的</h2>
                <p class="text-gray-500 mb-8">快去選購喜歡的商品吧！</p>
                <a href="?page=home" class="inline-block px-8 py-3 bg-primary text-white rounded-xl font-medium hover:bg-secondary transition">
                    繼續購物
                </a>
            </div>
            
            <?php else: ?>
            <!-- 購物車列表 / Cart List -->
            <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-8">
                <div class="p-6">
                    <div id="cart-items">
                        <?php foreach ($cartItems as $item): ?>
                        <div class="cart-item flex items-center gap-4 py-4 border-b last:border-b-0" 
                             data-id="<?= $item['id'] ?>">
                            
                            <!-- 商品圖片 / Product Image -->
                            <img src="./pic/<?= $item['product']['img'] ?>" 
                                 alt="<?= htmlspecialchars($item['product']['title']) ?>"
                                 class="w-20 h-20 object-cover rounded-lg"
                                 loading="lazy">
                            
                            <!-- 商品資訊 / Product Info -->
                            <div class="flex-1">
                                <h3 class="font-bold text-gray-800">
                                    <?= htmlspecialchars($item['product']['title']) ?>
                                </h3>
                                <p class="text-primary font-medium">
                                    NT$ <?= number_format($item['product']['price']) ?>
                                </p>
                            </div>
                            
                            <!-- 數量控制 / Quantity Control -->
                            <div class="flex items-center gap-2">
                                <button onclick="updateQuantity(<?= $item['id'] ?>, <?= $item['quantity'] - 1 ?>)"
                                        class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center">
                                    −
                                </button>
                                <span class="w-12 text-center font-medium"><?= $item['quantity'] ?></span>
                                <button onclick="updateQuantity(<?= $item['id'] ?>, <?= $item['quantity'] + 1 ?>)"
                                        class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center">
                                    +
                                </button>
                            </div>
                            
                            <!-- 小計 / Subtotal -->
                            <div class="text-right w-24">
                                <p class="font-bold text-accent">
                                    NT$ <?= number_format($item['subtotal']) ?>
                                </p>
                            </div>
                            
                            <!-- 刪除按鈕 / Delete Button -->
                            <button onclick="removeItem(<?= $item['id'] ?>)"
                                    class="p-2 text-gray-400 hover:text-red-500 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- 總計區域 / Total Area -->
                <div class="bg-gray-50 p-6">
                    <div class="flex items-center justify-between text-lg mb-4">
                        <span class="text-gray-600">商品總計</span>
                        <span id="cart-total-amount" class="text-2xl font-bold text-accent">
                            NT$ <?= number_format($cartTotal['total']) ?>
                        </span>
                    </div>
                    
                    <div class="flex gap-4">
                        <a href="?page=home" class="flex-1 py-3 border-2 border-gray-300 text-gray-700 rounded-xl 
                                                    text-center font-medium hover:border-primary hover:text-primary transition">
                            繼續購物
                        </a>
                        <a href="?page=checkout" class="flex-1 py-3 bg-primary text-white rounded-xl 
                                                        text-center font-medium hover:bg-secondary transition">
                            前往結帳
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php break; ?>
    
    <?php case 'checkout': ?>
    <!-- =====================================================
    結帳頁面 / Checkout Page
    ===================================================== -->
    <section class="py-12 min-h-screen">
        <div class="container mx-auto px-4 max-w-2xl">
            <h1 class="text-3xl font-bold mb-8 text-gray-800">
                📝 結帳 / Checkout
            </h1>
            
            <?php 
            $cartItems = getCartItems();
            $cartTotal = getCartTotal();
            
            if (empty($cartItems)):
            ?>
            <div class="text-center py-20 bg-white rounded-2xl shadow-md">
                <p class="text-xl text-gray-500">購物車是空的，無法結帳</p>
                <a href="?page=home" class="inline-block mt-4 px-6 py-2 bg-primary text-white rounded-lg">
                    返回購物
                </a>
            </div>
            
            <?php else: ?>
            <div class="bg-white rounded-2xl shadow-md p-6 mb-6">
                <!-- 訂單摘要 / Order Summary -->
                <h2 class="text-xl font-bold mb-4 pb-4 border-b">訂單摘要</h2>
                
                <?php foreach ($cartItems as $item): ?>
                <div class="flex items-center gap-4 py-2">
                    <span class="flex-1 text-gray-600">
                        <?= htmlspecialchars($item['product']['title']) ?> × <?= $item['quantity'] ?>
                    </span>
                    <span class="font-medium">NT$ <?= number_format($item['subtotal']) ?></span>
                </div>
                <?php endforeach; ?>
                
                <div class="flex items-center justify-between pt-4 mt-4 border-t">
                    <span class="text-lg font-bold">總計</span>
                    <span class="text-2xl font-bold text-accent">NT$ <?= number_format($cartTotal['total']) ?></span>
                </div>
            </div>
            
            <!-- 收件資訊表單 / Shipping Info Form -->
            <form id="checkout-form" class="bg-white rounded-2xl shadow-md p-6">
                <h2 class="text-xl font-bold mb-4 pb-4 border-b">收件資訊</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">姓名 *</label>
                        <input type="text" name="name" required
                               class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Email *</label>
                        <input type="email" name="email" required
                               class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">電話 *</label>
                        <input type="tel" name="phone" required
                               class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">配送地址 *</label>
                        <textarea name="address" rows="2" required
                                  class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">備註（選填）</label>
                        <textarea name="notes" rows="2"
                                  class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
                    </div>
                </div>
                
                <div class="flex gap-4 mt-8">
                    <a href="?page=cart" class="flex-1 py-3 border-2 border-gray-300 text-gray-700 rounded-xl 
                                                text-center font-medium hover:border-primary transition">
                        返回購物車
                    </a>
                    <button type="submit" class="flex-1 py-3 bg-accent text-white rounded-xl 
                                                 font-medium hover:bg-orange-600 transition">
                        確認下單
                    </button>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </section>
    <?php break; ?>
    
    <?php case 'order-success': ?>
    <!-- =====================================================
    訂單成功頁面 / Order Success Page
    ===================================================== -->
    <section class="py-20 min-h-screen flex items-center">
        <div class="container mx-auto px-4 max-w-lg text-center">
            <div class="bg-white rounded-2xl shadow-xl p-12">
                <div class="text-8xl mb-6">🎉</div>
                <h1 class="text-3xl font-bold text-gray-800 mb-4">訂單已成立！</h1>
                <p class="text-gray-600 mb-2">訂單編號</p>
                <p class="text-2xl font-bold text-primary mb-8"><?= $_GET['order'] ?? 'N/A' ?></p>
                <p class="text-gray-500 mb-8">
                    感謝您的訂購！我們將盡快處理您的訂單。
                </p>
                <a href="?page=home" class="inline-block px-8 py-3 bg-primary text-white rounded-xl font-medium hover:bg-secondary transition">
                    繼續購物
                </a>
            </div>
        </div>
    </section>
    <?php break; ?>
    
    <?php case 'login': ?>
    <!-- =====================================================
    登入頁面 / Login Page
    ===================================================== -->
    <section class="py-20 min-h-screen flex items-center">
        <div class="container mx-auto px-4 max-w-md">
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <h1 class="text-2xl font-bold text-center mb-8 text-gray-800">
                    🔐 管理員登入
                </h1>
                
                <form method="post" action="./api/login.php" class="space-y-6">
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">帳號</label>
                        <input type="text" name="acc" required autofocus
                               class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">密碼</label>
                        <input type="password" name="pw" required
                               class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    
                    <button type="submit" class="w-full py-3 bg-primary text-white rounded-xl font-medium hover:bg-secondary transition">
                        登入
                    </button>
                </form>
                
                <p class="text-center mt-6">
                    <a href="?page=home" class="text-gray-500 hover:text-primary">← 返回首頁</a>
                </p>
            </div>
        </div>
    </section>
    <?php break; ?>
    
    <?php endswitch; ?>
    
    <!-- =====================================================
    頁尾 / Footer
    可自訂版權資訊 / Customizable copyright info
    ===================================================== -->
    <footer class="bg-dark text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- 關於我們 / About Us -->
                <div>
                    <h3 class="text-xl font-bold mb-4">關於我們</h3>
                    <p class="text-gray-400">
                        頻繁往返日韓，當季代購服務，提供客製窗口服務。
                    </p>
                </div>
                
                <!-- 商品類別 / Categories -->
                <div>
                    <h3 class="text-xl font-bold mb-4">商品類別</h3>
                    <ul class="space-y-2 text-gray-400">
                        <?php foreach ($categories as $cat): ?>
                        <li>
                            <a href="?page=home&category=<?= $cat['slug'] ?>" class="hover:text-white transition">
                                <?= $cat['icon'] ?> <?= $cat['name'] ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <!-- 聯絡資訊 / Contact Info -->
                <div>
                    <h3 class="text-xl font-bold mb-4">聯絡我們</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li>📧 contact@example.com</li>
                        <li>📞 (02) 1234-5678</li>
                        <li>📍 台灣台北市</li>
                    </ul>
                </div>
            </div>
            
            <!-- 版權資訊 / Copyright -->
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p><?= $bottom['bottom'] ?? '© 2026 代購專門店. All rights reserved.' ?></p>
            </div>
        </div>
    </footer>
    
    <!-- =====================================================
    Toast 通知元件 / Toast Notification Component
    ===================================================== -->
    <div id="toast" class="fixed bottom-4 right-4 z-50 hidden">
        <div class="bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span id="toast-message">已加入購物車</span>
        </div>
    </div>
    
    <!-- =====================================================
    JavaScript 功能 / JavaScript Functions
    ===================================================== -->
    <script>
    // 手機版選單切換 / Mobile menu toggle
    $('#mobile-menu-btn').click(function() {
        $('#mobile-menu').toggleClass('hidden');
    });
    
    /**
     * 顯示 Toast 通知 / Show toast notification
     * @param {string} message 訊息內容 / Message content
     * @param {string} type 類型 (success/error) / Type
     */
    function showToast(message, type = 'success') {
        const toast = $('#toast');
        const bg = type === 'success' ? 'bg-green-500' : 'bg-red-500';
        toast.find('div').removeClass('bg-green-500 bg-red-500').addClass(bg);
        $('#toast-message').text(message);
        toast.removeClass('hidden').addClass('modal-enter');
        setTimeout(() => toast.addClass('hidden'), 3000);
    }
    
    /**
     * 更新購物車徽章 / Update cart badge
     * @param {number} count 數量 / Count
     */
    function updateCartBadge(count) {
        const badge = $('#cart-badge');
        if (count > 0) {
            badge.text(count).removeClass('hidden');
        } else {
            badge.addClass('hidden');
        }
    }
    
    /**
     * 加入購物車 / Add to cart
     * @param {number} productId 商品ID / Product ID
     */
    function addToCart(productId) {
        $.post('index.php', {
            action: 'add_to_cart',
            product_id: productId,
            quantity: 1
        }, function(response) {
            if (response.success) {
                showToast(response.message);
                updateCartBadge(response.cart_count);
            } else {
                showToast(response.message, 'error');
            }
        }, 'json');
    }
    
    /**
     * 更新購物車數量 / Update cart quantity
     * @param {number} cartItemId 購物車項目ID / Cart item ID
     * @param {number} quantity 新數量 / New quantity
     */
    function updateQuantity(cartItemId, quantity) {
        if (quantity <= 0) {
            removeItem(cartItemId);
            return;
        }
        
        $.post('index.php', {
            action: 'update_cart',
            cart_item_id: cartItemId,
            quantity: quantity
        }, function(response) {
            if (response.success) {
                location.reload(); // 重新載入頁面更新顯示 / Reload to update display
            }
        }, 'json');
    }
    
    /**
     * 從購物車移除 / Remove from cart
     * @param {number} cartItemId 購物車項目ID / Cart item ID
     */
    function removeItem(cartItemId) {
        if (!confirm('確定要移除此商品？')) return;
        
        $.post('index.php', {
            action: 'remove_from_cart',
            cart_item_id: cartItemId
        }, function(response) {
            if (response.success) {
                showToast('已移除商品');
                location.reload();
            }
        }, 'json');
    }
    
    // 結帳表單處理 / Checkout form handling
    $('#checkout-form').submit(function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize() + '&action=checkout';
        
        $.post('index.php', formData, function(response) {
            if (response.success) {
                // 導向成功頁面 / Redirect to success page
                window.location.href = '?page=order-success&order=' + response.order_number;
            } else {
                showToast(response.message, 'error');
            }
        }, 'json');
    });
    </script>
    
</body>
</html>