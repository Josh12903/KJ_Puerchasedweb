-- =====================================================
-- 資料庫遷移腳本 / Database Migration Script
-- 專案：現代化電商網站 / Modern E-commerce Website
-- 版本：v1.0
-- 日期：2026-01-26
-- =====================================================

-- -----------------------------------------------------
-- 1. 建立商品分類表 / Create categories table
-- 用途：儲存三個主要商品類別
-- Purpose: Store three main product categories
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL COMMENT '類別名稱 / Category name',
    `slug` VARCHAR(50) NOT NULL COMMENT '網址辨識碼 / URL slug',
    `description` TEXT COMMENT '類別描述 / Category description',
    `icon` VARCHAR(50) COMMENT '圖示類別 / Icon class (emoji or icon)',
    `sort_order` INT(11) DEFAULT 0 COMMENT '排序順序 / Sort order',
    `sh` TINYINT(1) DEFAULT 1 COMMENT '是否顯示 / Show status (1=show, 0=hide)',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品分類表 / Product categories';

-- -----------------------------------------------------
-- 2. 建立商品表 / Create products table (取代原本的 image 表)
-- 用途：儲存各類別的商品資料與圖片
-- Purpose: Store product data and images for each category
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `products` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `category_id` INT(11) NOT NULL COMMENT '所屬類別ID / Category ID',
    `title` VARCHAR(200) NOT NULL COMMENT '商品標題 / Product title',
    `description` TEXT COMMENT '商品描述 / Product description',
    `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '商品價格 / Product price',
    `img` VARCHAR(255) NOT NULL COMMENT '圖片檔名 / Image filename',
    `stock` INT(11) DEFAULT 99 COMMENT '庫存數量 / Stock quantity',
    `sh` TINYINT(1) DEFAULT 1 COMMENT '是否顯示 / Show status',
    `sort_order` INT(11) DEFAULT 0 COMMENT '排序順序 / Sort order',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `category_id` (`category_id`),
    CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) 
        REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品表 / Products';

-- -----------------------------------------------------
-- 3. 建立購物車項目表 / Create cart_items table
-- 用途：儲存使用者的購物車資料
-- Purpose: Store user shopping cart items
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `cart_items` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `session_id` VARCHAR(100) COMMENT '訪客Session ID / Guest session ID',
    `user_id` INT(11) COMMENT '登入用戶ID / Logged-in user ID (admin.id)',
    `product_id` INT(11) NOT NULL COMMENT '商品ID / Product ID',
    `quantity` INT(11) NOT NULL DEFAULT 1 COMMENT '數量 / Quantity',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `product_id` (`product_id`),
    KEY `session_id` (`session_id`),
    KEY `user_id` (`user_id`),
    CONSTRAINT `fk_cart_products` FOREIGN KEY (`product_id`) 
        REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='購物車項目表 / Shopping cart items';

-- -----------------------------------------------------
-- 4. 建立訂單表 / Create orders table
-- 用途：儲存結帳後的訂單資料
-- Purpose: Store checkout orders
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `orders` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `order_number` VARCHAR(50) NOT NULL COMMENT '訂單編號 / Order number',
    `user_id` INT(11) COMMENT '用戶ID / User ID',
    `session_id` VARCHAR(100) COMMENT '訪客Session / Guest session',
    `total_amount` DECIMAL(10,2) NOT NULL COMMENT '訂單總金額 / Total amount',
    `status` ENUM('pending','paid','shipped','completed','cancelled') 
        DEFAULT 'pending' COMMENT '訂單狀態 / Order status',
    `customer_name` VARCHAR(100) COMMENT '顧客姓名 / Customer name',
    `customer_email` VARCHAR(150) COMMENT '顧客Email / Customer email',
    `customer_phone` VARCHAR(20) COMMENT '顧客電話 / Customer phone',
    `shipping_address` TEXT COMMENT '配送地址 / Shipping address',
    `notes` TEXT COMMENT '備註 / Notes',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `order_number` (`order_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='訂單表 / Orders';

-- -----------------------------------------------------
-- 5. 建立訂單明細表 / Create order_items table
-- 用途：儲存訂單中的商品明細
-- Purpose: Store order item details
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `order_items` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `order_id` INT(11) NOT NULL COMMENT '訂單ID / Order ID',
    `product_id` INT(11) NOT NULL COMMENT '商品ID / Product ID',
    `product_title` VARCHAR(200) NOT NULL COMMENT '商品名稱快照 / Product title snapshot',
    `price` DECIMAL(10,2) NOT NULL COMMENT '購買時價格 / Price at purchase',
    `quantity` INT(11) NOT NULL COMMENT '數量 / Quantity',
    `subtotal` DECIMAL(10,2) NOT NULL COMMENT '小計 / Subtotal',
    PRIMARY KEY (`id`),
    KEY `order_id` (`order_id`),
    CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) 
        REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='訂單明細表 / Order items';

-- =====================================================
-- 初始資料 / Initial Data
-- =====================================================

-- -----------------------------------------------------
-- 插入三個商品類別 / Insert three product categories
-- 🎮 熱門ACG周邊 / Popular ACG Merchandise
-- 💄 日用與時尚美妝 / Daily & Fashion Beauty
-- 💊 健康保健食品 / Health Supplements
-- -----------------------------------------------------
INSERT INTO `categories` (`name`, `slug`, `description`, `icon`, `sort_order`, `sh`) VALUES
('熱門ACG周邊', 'acg', '動畫、漫畫、遊戲相關周邊商品 / Anime, Comics, Games merchandise', '🎮', 1, 1),
('日用與時尚美妝', 'beauty', '日常用品與化妝品 / Daily products and cosmetics', '💄', 2, 1),
('健康保健食品', 'health', '營養補充與保健食品 / Health supplements and nutrition', '💊', 3, 1);

-- -----------------------------------------------------
-- 插入範例商品 / Insert sample products
-- 您可以自行修改這些資料 / You can customize these data
-- 圖片請放在 ./pic/ 資料夾 / Put images in ./pic/ folder
-- -----------------------------------------------------

-- ACG 類別商品 / ACG category products
INSERT INTO `products` (`category_id`, `title`, `description`, `price`, `img`, `stock`, `sh`, `sort_order`) VALUES
(1, '初音未來公仔', '限定版初音未來模型，高度約15cm / Limited edition Hatsune Miku figure, 15cm', 1280.00, 'acg_miku.jpg', 50, 1, 1),
(1, '鬼滅之刃海報組', '精美印刷海報5張組 / Demon Slayer poster set, 5 pieces', 350.00, 'acg_kimetsu.jpg', 100, 1, 2),
(1, '寶可夢卡牌禮盒', '稀有卡牌收藏組 / Pokemon rare card collection box', 890.00, 'acg_pokemon.jpg', 30, 1, 3),
(1, '進擊的巨人T恤', '純棉印花T恤 / Attack on Titan cotton T-shirt', 580.00, 'acg_aot.jpg', 80, 1, 4);

-- 美妝類別商品 / Beauty category products
INSERT INTO `products` (`category_id`, `title`, `description`, `price`, `img`, `stock`, `sh`, `sort_order`) VALUES
(2, '保濕精華液', '深層保濕配方 / Deep moisturizing serum', 680.00, 'beauty_serum.jpg', 60, 1, 1),
(2, '唇膏組合包', '多色唇膏4入 / Lipstick set, 4 colors', 420.00, 'beauty_lipstick.jpg', 100, 1, 2),
(2, '面膜禮盒', '10入面膜組 / Face mask gift set, 10 pieces', 550.00, 'beauty_mask.jpg', 80, 1, 3),
(2, '香氛沐浴乳', '天然植物萃取 / Natural botanical body wash', 320.00, 'beauty_bodywash.jpg', 120, 1, 4);

-- 健康保健類別商品 / Health category products
INSERT INTO `products` (`category_id`, `title`, `description`, `price`, `img`, `stock`, `sh`, `sort_order`) VALUES
(3, '綜合維他命', '30日份膠囊 / Multivitamins 30-day supply', 450.00, 'health_vitamin.jpg', 200, 1, 1),
(3, '魚油膠囊', 'Omega-3高濃度配方 / Omega-3 fish oil capsules', 720.00, 'health_fishoil.jpg', 150, 1, 2),
(3, '益生菌粉', '腸道保健配方 / Probiotics powder for gut health', 580.00, 'health_probiotic.jpg', 100, 1, 3),
(3, '膠原蛋白飲', '美容養顏配方 / Collagen drink for beauty', 890.00, 'health_collagen.jpg', 80, 1, 4);

-- =====================================================
-- 注意事項 / Notes
-- =====================================================
-- 1. 執行前請備份原有資料庫 / Backup your database before running
-- 2. 圖片檔案需自行準備並放入 ./pic/ 資料夾
--    Prepare image files and put them in ./pic/ folder
-- 3. 如需修改原有 image 表資料，可使用以下 SQL:
--    To migrate existing image data, use:
--    INSERT INTO products (category_id, title, img, price, sh)
--    SELECT 1, CONCAT('商品', id), img, 100.00, sh FROM image;
-- =====================================================
