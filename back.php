<?php 
/**
 * =====================================================
 * 後台管理介面 / Admin Panel
 * =====================================================
 * 功能：商品管理、類別管理、訂單查看
 * Purpose: Product management, category management, order viewing
 * 
 * 修改說明 / Modification Notes:
 * - 現代化 Tailwind CSS 樣式
 * - 新增商品 CRUD 功能
 * - 新增訂單管理
 * =====================================================
 */

include_once "./api/db.php";

// =====================================================
// 登入驗證 / Login Verification
// 取消下方註解以啟用登入保護
// Uncomment below to enable login protection
// =====================================================
if(!isset($_SESSION['admin'])){
    header("Location: index.php?page=login");
    exit();
}

// 合併購物車 / Merge cart on login
mergeCartOnLogin();

// 取得當前操作 / Get current action
$do = $_GET['do'] ?? 'products';

// 取得標題 / Get title
$title = $Title->find(['sh' => 1]);
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>後台管理 | <?= $title['text'] ?? '管理介面' ?></title>
    
    <!-- 字體 / Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Noto Sans TC', 'sans-serif'],
                    },
                    colors: {
                        'primary': '#6366f1',
                        'secondary': '#8b5cf6',
                        'accent': '#f59e0b',
                        'dark': '#1e1b4b',
                    }
                }
            }
        }
    </script>
    
    <style>
        body { font-family: 'Noto Sans TC', sans-serif; }
        .sidebar-link { transition: all 0.2s ease; }
        .sidebar-link:hover, .sidebar-link.active {
            background: linear-gradient(135deg, #c6d886, #8b5cf6);
            color: white;
        }
    </style>
    
    <script src="./js/jquery-1.9.1.min.js"></script>
</head>

<body class="bg-gray-100 min-h-screen">
    
    <div class="flex">
        <!-- =====================================================
        側邊選單 / Sidebar Menu
        ===================================================== -->
        <aside class="fixed left-0 top-0 h-full w-64 bg-dark text-white shadow-xl z-50" style="background:#ffe">
            <!-- Logo -->
            <div class="p-6 border-b border-gray-700">
                <a href="index.php" class="text-xl font-bold text-white hover:text-primary transition"style="color:#0AE">
                    首頁
                </a>
                <p class="text-gray-400 text-sm mt-1">Admin Panel</p>
            </div>
            
            <!-- 導航連結 / Navigation Links -->
            <nav class="p-4 space-y-2">
                <!-- 商品管理 / Product Management -->
                <a href="?do=products" class="sidebar-link block px-4 py-3 rounded-lg <?= $do === 'products' ? 'active' : '' ?>" style="color:black">
                    📦 商品管理
                </a>
                
                <!-- 類別管理 / Category Management -->
                <a href="?do=categories" class="sidebar-link block px-4 py-3 rounded-lg <?= $do === 'categories' ? 'active' : '' ?>" style="color:black">
                    📁 類別管理
                </a>
                
                <!-- 訂單管理 / Order Management -->
                <a href="?do=orders" class="sidebar-link block px-4 py-3 rounded-lg <?= $do === 'orders' ? 'active' : '' ?>" style="color:black">
                    🧾 訂單管理
                </a>
                
                <hr class="border-gray-700 my-4">
                
                <!-- 原有功能 / Legacy Features -->
                <p class="text-gray-500 text-xs uppercase tracking-wider px-4 mb-2">原有功能</p>
                
                <a href="?do=title" class="sidebar-link block px-4 py-3 rounded-lg <?= $do === 'title' ? 'active' : '' ?>" style="color:black">
                    🏷️ 網站標題管理
                </a>
                
                <a href="?do=ad" class="sidebar-link block px-4 py-3 rounded-lg <?= $do === 'ad' ? 'active' : '' ?>" style="color:black">
                    📢 動態文字廣告
                </a>
                
                <a href="?do=news" class="sidebar-link block px-4 py-3 rounded-lg <?= $do === 'news' ? 'active' : '' ?>" style="color:black">
                    📰 最新消息管理
                </a>
                
                <a href="?do=admin" class="sidebar-link block px-4 py-3 rounded-lg <?= $do === 'admin' ? 'active' : '' ?>" style="color:black">
                    👤 管理員帳號
                </a>
            </nav>
            
            <!-- 底部 / Bottom -->
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-700">
                <p class="text-gray-400 text-sm mb-2">👤 <?= $_SESSION['admin'] ?></p>
                <div class="flex gap-2">
                    <a href="index.php" class="flex-1 py-2 bg-gray-700 text-center rounded-lg text-sm hover:bg-gray-600 transition">
                        前往前台
                    </a>
                    <a href="./api/signout.php" class="flex-1 py-2 bg-red-600 text-center rounded-lg text-sm hover:bg-red-700 transition">
                        登出
                    </a>
                </div>
            </div>
        </aside>
        
        <!-- =====================================================
        主要內容區 / Main Content Area
        ===================================================== -->
        <main class="ml-64 flex-1 p-8">
            
            <?php switch ($do): 
                case 'products': ?>
            <!-- =====================================================
            商品管理頁面 / Products Management Page
            ===================================================== -->
            <div class="mb-8 flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-800">📦 商品管理</h1>
                <button onclick="openModal('add-product')" 
                        class="px-6 py-3 text-white rounded-xl font-medium hover:bg-secondary transition" style="background:orange">
                    + 新增商品
                </button>
            </div>
            
            <?php 
            // 取得所有類別 / Get all categories
            $allCategories = $Category->all(' ORDER BY sort_order ASC');
            
            // 按類別顯示商品 / Display products by category
            foreach ($allCategories as $cat):
                $catProducts = $Product->all(['category_id' => $cat['id']], ' ORDER BY sort_order ASC');
            ?>
            <div class="bg-white rounded-2xl shadow-md mb-8 overflow-hidden">
                <div class="bg-gradient-to-r from-primary to-secondary text-white px-6 py-4 flex items-center justify-between" style="background: #0e4dd3">
                    <h2 class="text-xl font-bold"><?= $cat['icon'] ?> <?= $cat['name'] ?></h2>
                    <span class="bg-white/20 px-3 py-1 rounded-full text-sm">
                        <?= count($catProducts) ?> 件商品
                    </span>
                </div>
                
                <div class="p-6">
                    <?php if (empty($catProducts)): ?>
                    <p class="text-gray-400 text-center py-8">此類別尚無商品</p>
                    <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left text-gray-500 text-sm border-b">
                                    <th class="pb-3 w-20">圖片</th>
                                    <th class="pb-3">商品名稱</th>
                                    <th class="pb-3 w-24">價格</th>
                                    <th class="pb-3 w-20">庫存</th>
                                    <th class="pb-3 w-20">顯示</th>
                                    <th class="pb-3 w-40 text-center">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($catProducts as $prod): ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-3">
                                        <img src="./pic/<?= $prod['img'] ?>" 
                                             alt="<?= htmlspecialchars($prod['title']) ?>"
                                             class="w-16 h-16 object-cover rounded-lg"
                                             onerror="this.src='./pic/placeholder.jpg'">
                                    </td>
                                    <td class="py-3">
                                        <p class="font-medium text-gray-800"><?= htmlspecialchars($prod['title']) ?></p>
                                        <p class="text-sm text-gray-400 line-clamp-1"><?= htmlspecialchars($prod['description']) ?></p>
                                    </td>
                                    <td class="py-3 font-bold text-accent">
                                        NT$ <?= number_format($prod['price']) ?>
                                    </td>
                                    <td class="py-3">
                                        <span class="<?= $prod['stock'] < 10 ? 'text-red-500' : 'text-gray-600' ?>">
                                            <?= $prod['stock'] ?>
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <span class="px-2 py-1 text-xs rounded-full <?= $prod['sh'] ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' ?>">
                                            <?= $prod['sh'] ? '顯示' : '隱藏' ?>
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <div class="flex justify-center gap-2">
                                            <button onclick="editProduct(<?= htmlspecialchars(json_encode($prod)) ?>)"
                                                    class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-sm hover:bg-blue-200 transition">
                                                編輯
                                            </button>
                                            <button onclick="deleteProduct(<?= $prod['id'] ?>)"
                                                    class="px-3 py-1 bg-red-100 text-red-700 rounded-lg text-sm hover:bg-red-200 transition">
                                                刪除
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
            
            <!-- 新增/編輯商品 Modal / Add/Edit Product Modal -->
            <div id="product-modal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto m-4">
                    <div class="p-6 border-b flex items-center justify-between">
                        <h3 id="modal-title" class="text-xl font-bold">新增商品</h3>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">✕</button>
                    </div>
                    
                    <form id="product-form" action="./api/save_product.php" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                        <input type="hidden" name="id" id="product-id">
                        
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">類別 *</label>
                            <select name="category_id" id="product-category" required
                                    class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary">
                                <?php foreach ($allCategories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= $cat['icon'] ?> <?= $cat['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">商品名稱 *</label>
                            <input type="text" name="title" id="product-title" required
                                   class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">商品描述</label>
                            <textarea name="description" id="product-description" rows="3"
                                      class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary"></textarea>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">價格 *</label>
                                <input type="number" name="price" id="product-price" required min="0" step="0.01"
                                       class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">庫存</label>
                                <input type="number" name="stock" id="product-stock" value="99" min="0"
                                       class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">商品圖片</label>
                            <input type="file" name="image" accept="image/*"
                                   class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary">
                            <p class="text-sm text-gray-400 mt-1">建議尺寸：400x400px，格式：JPG/PNG</p>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="sh" id="product-sh" value="1" checked
                                   class="w-5 h-5 rounded border-gray-300 text-primary focus:ring-primary">
                            <label for="product-sh" class="text-gray-700">顯示此商品</label>
                        </div>
                        
                        <div class="flex gap-4 pt-4">
                            <button type="button" onclick="closeModal()"
                                    class="flex-1 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-medium hover:border-gray-400 transition">
                                取消
                            </button>
                            <button type="submit"
                                    class="flex-1 py-3 text-white rounded-xl font-medium hover:bg-secondary transition" style="background: #53e9b0">
                                儲存
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <?php break; ?>
            
            <?php case 'categories': ?>
            <!-- =====================================================
            類別管理頁面 / Categories Management Page
            ===================================================== -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800">📁 類別管理</h1>
            </div>
            
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="p-6">
                    <form action="./api/save_categories.php" method="POST">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left text-gray-500 text-sm border-b">
                                    <th class="pb-3 w-20">圖示</th>
                                    <th class="pb-3">名稱</th>
                                    <th class="pb-3 w-32">Slug</th>
                                    <th class="pb-3">描述</th>
                                    <th class="pb-3 w-20">排序</th>
                                    <th class="pb-3 w-20">顯示</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $allCats = $Category->all(' ORDER BY sort_order ASC');
                                foreach ($allCats as $cat): 
                                ?>
                                <tr class="border-b">
                                    <td class="py-3">
                                        <input type="hidden" name="id[]" value="<?= $cat['id'] ?>">
                                        <input type="text" name="icon[]" value="<?= htmlspecialchars($cat['icon']) ?>"
                                               class="w-16 px-2 py-2 border rounded-lg text-center text-2xl">
                                    </td>
                                    <td class="py-3">
                                        <input type="text" name="name[]" value="<?= htmlspecialchars($cat['name']) ?>"
                                               class="w-full px-3 py-2 border rounded-lg">
                                    </td>
                                    <td class="py-3">
                                        <input type="text" name="slug[]" value="<?= htmlspecialchars($cat['slug']) ?>"
                                               class="w-full px-3 py-2 border rounded-lg text-sm text-gray-500" readonly>
                                    </td>
                                    <td class="py-3">
                                        <input type="text" name="description[]" value="<?= htmlspecialchars($cat['description']) ?>"
                                               class="w-full px-3 py-2 border rounded-lg">
                                    </td>
                                    <td class="py-3">
                                        <input type="number" name="sort_order[]" value="<?= $cat['sort_order'] ?>"
                                               class="w-full px-3 py-2 border rounded-lg text-center" min="0">
                                    </td>
                                    <td class="py-3 text-center">
                                        <input type="checkbox" name="sh[]" value="<?= $cat['id'] ?>"
                                               <?= $cat['sh'] ? 'checked' : '' ?>
                                               class="w-5 h-5 rounded border-gray-300 text-primary">
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        
                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="px-8 py-3 bg-primary text-white rounded-xl font-medium hover:bg-secondary transition" style="background: rgb(71 72 108)">
                                儲存變更
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <?php break; ?>
            
            <?php case 'orders': ?>
            <!-- =====================================================
            訂單管理頁面 / Orders Management Page
            ===================================================== -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800">🧾 訂單管理</h1>
            </div>
            
            <?php 
            $orders = $Order->all(' ORDER BY created_at DESC');
            ?>
            
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <?php if (empty($orders)): ?>
                <div class="p-12 text-center">
                    <div class="text-6xl mb-4">📋</div>
                    <p class="text-xl text-gray-500">目前沒有訂單</p>
                </div>
                <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-gray-500 text-sm bg-gray-50">
                                <th class="px-6 py-4">訂單編號</th>
                                <th class="px-6 py-4">顧客資訊</th>
                                <th class="px-6 py-4">金額</th>
                                <th class="px-6 py-4">狀態</th>
                                <th class="px-6 py-4">建立時間</th>
                                <th class="px-6 py-4">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-6 py-4 font-mono text-primary">
                                    <?= $order['order_number'] ?>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-medium"><?= htmlspecialchars($order['customer_name'] ?: '未填寫') ?></p>
                                    <p class="text-sm text-gray-400"><?= htmlspecialchars($order['customer_email']) ?></p>
                                </td>
                                <td class="px-6 py-4 font-bold text-accent">
                                    NT$ <?= number_format($order['total_amount']) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php 
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        'paid' => 'bg-blue-100 text-blue-700',
                                        'shipped' => 'bg-purple-100 text-purple-700',
                                        'completed' => 'bg-green-100 text-green-700',
                                        'cancelled' => 'bg-red-100 text-red-700'
                                    ];
                                    $statusLabels = [
                                        'pending' => '待處理',
                                        'paid' => '已付款',
                                        'shipped' => '已出貨',
                                        'completed' => '已完成',
                                        'cancelled' => '已取消'
                                    ];
                                    ?>
                                    <span class="px-3 py-1 rounded-full text-sm <?= $statusColors[$order['status']] ?>">
                                        <?= $statusLabels[$order['status']] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-sm">
                                    <?= $order['created_at'] ?>
                                </td>
                                <td class="px-6 py-4">
                                    <button onclick="viewOrder(<?= $order['id'] ?>)"
                                            class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200 transition">
                                        查看詳情
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            
            <?php break; ?>
            
            <?php default: ?>
            <!-- =====================================================
            原有後台功能 / Legacy Admin Features
            載入原有的 back/*.php 檔案
            Load existing back/*.php files
            ===================================================== -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800">
                    <?php
                    $titles = [
                        'title' => '🏷️ 網站標題管理',
                        'ad' => '📢 動態文字廣告',
                        'mvim' => '🎬 動畫圖片管理',
                        'image' => '🖼️ 校園映象管理',
                        'news' => '📰 最新消息管理',
                        'admin' => '👤 管理員帳號',
                        'menu' => '📋 選單管理',
                        'total' => '📊 進站人數',
                        'bottom' => '📝 頁尾版權'
                    ];
                    echo $titles[$do] ?? '管理功能';
                    ?>
                </h1>
            </div>
            
            <div class="bg-white rounded-2xl shadow-md overflow-hidden p-6">
                <?php
                $file = "./back/" . $do . ".php";
                if (file_exists($file)) {
                    include $file;
                } else {
                    echo '<p class="text-gray-500 text-center py-8">功能不存在</p>';
                }
                ?>
            </div>
            
            <?php endswitch; ?>
            
        </main>
    </div>
    
    <!-- =====================================================
    JavaScript 功能 / JavaScript Functions
    ===================================================== -->
    <script>
    // 開啟 Modal / Open modal
    function openModal(type) {
        if (type === 'add-product') {
            $('#modal-title').text('新增商品');
            $('#product-form')[0].reset();
            $('#product-id').val('');
            $('#product-modal').removeClass('hidden');
        }
    }
    
    // 關閉 Modal / Close modal
    function closeModal() {
        $('#product-modal').addClass('hidden');
    }
    
    // 編輯商品 / Edit product
    function editProduct(product) {
        $('#modal-title').text('編輯商品');
        $('#product-id').val(product.id);
        $('#product-category').val(product.category_id);
        $('#product-title').val(product.title);
        $('#product-description').val(product.description);
        $('#product-price').val(product.price);
        $('#product-stock').val(product.stock);
        $('#product-sh').prop('checked', product.sh == 1);
        $('#product-modal').removeClass('hidden');
    }
    
    // 刪除商品 / Delete product
    function deleteProduct(id) {
        if (confirm('確定要刪除此商品嗎？')) {
            window.location.href = './api/delete_product.php?id=' + id;
        }
    }
    
    // 查看訂單 / View order
    function viewOrder(id) {
        alert('訂單詳情功能開發中...\nOrder detail feature in development...');
    }
    
    // 點擊 Modal 外部關閉 / Close modal on outside click
    $('#product-modal').click(function(e) {
        if (e.target === this) closeModal();
    });
    </script>
    
</body>
</html>