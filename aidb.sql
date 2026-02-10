-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2026-02-10 08:22:26
-- 伺服器版本： 10.4.32-MariaDB
-- PHP 版本： 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `aidb`
--

-- --------------------------------------------------------

--
-- 資料表結構 `ad`
--

CREATE TABLE `ad` (
  `id` int(10) UNSIGNED NOT NULL,
  `text` text NOT NULL,
  `sh` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `admin`
--

CREATE TABLE `admin` (
  `id` int(10) UNSIGNED NOT NULL,
  `acc` text NOT NULL,
  `pw` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 傾印資料表的資料 `admin`
--

INSERT INTO `admin` (`id`, `acc`, `pw`) VALUES
(1, 'admin', '1234'),
(2, 'lily', 'lilili');

-- --------------------------------------------------------

--
-- 資料表結構 `bottom`
--

CREATE TABLE `bottom` (
  `id` int(10) UNSIGNED NOT NULL,
  `bottom` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `session_id` varchar(100) DEFAULT NULL COMMENT '訪客Session ID / Guest session ID',
  `user_id` int(11) DEFAULT NULL COMMENT '登入用戶ID / Logged-in user ID (admin.id)',
  `product_id` int(11) NOT NULL COMMENT '商品ID / Product ID',
  `quantity` int(11) NOT NULL DEFAULT 1 COMMENT '數量 / Quantity',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='購物車項目表 / Shopping cart items';

-- --------------------------------------------------------

--
-- 資料表結構 `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL COMMENT '類別名稱 / Category name',
  `slug` varchar(50) NOT NULL COMMENT '網址辨識碼 / URL slug',
  `description` text DEFAULT NULL COMMENT '類別描述 / Category description',
  `icon` varchar(50) DEFAULT NULL COMMENT '圖示類別 / Icon class (emoji or icon)',
  `sort_order` int(11) DEFAULT 0 COMMENT '排序順序 / Sort order',
  `sh` tinyint(1) DEFAULT 1 COMMENT '是否顯示 / Show status (1=show, 0=hide)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='商品分類表 / Product categories';

--
-- 傾印資料表的資料 `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `icon`, `sort_order`, `sh`, `created_at`) VALUES
(1, '熱門ACG周邊', 'acg', '動畫、漫畫、遊戲相關周邊商品 / Anime, Comics, Games merchandise', '🎮', 1, 1, '2026-02-10 06:21:28'),
(2, '日用與時尚美妝', 'beauty', '日常用品與化妝品 / Daily products and cosmetics', '💄', 2, 1, '2026-02-10 06:21:28'),
(3, '健康保健食品', 'health', '營養補充與保健食品 / Health supplements and nutrition', '💊', 3, 1, '2026-02-10 06:21:28');

-- --------------------------------------------------------

--
-- 資料表結構 `image`
--

CREATE TABLE `image` (
  `id` int(10) UNSIGNED NOT NULL,
  `img` text NOT NULL,
  `sh` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `menu`
--

CREATE TABLE `menu` (
  `id` int(10) UNSIGNED NOT NULL,
  `href` text NOT NULL,
  `text` text NOT NULL,
  `main_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `sh` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `mvim`
--

CREATE TABLE `mvim` (
  `id` int(10) UNSIGNED NOT NULL,
  `img` text NOT NULL,
  `sh` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `news`
--

CREATE TABLE `news` (
  `id` int(10) UNSIGNED NOT NULL,
  `text` text NOT NULL,
  `sh` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL COMMENT '訂單編號 / Order number',
  `user_id` int(11) DEFAULT NULL COMMENT '用戶ID / User ID',
  `session_id` varchar(100) DEFAULT NULL COMMENT '訪客Session / Guest session',
  `total_amount` decimal(10,2) NOT NULL COMMENT '訂單總金額 / Total amount',
  `status` enum('pending','paid','shipped','completed','cancelled') DEFAULT 'pending' COMMENT '訂單狀態 / Order status',
  `customer_name` varchar(100) DEFAULT NULL COMMENT '顧客姓名 / Customer name',
  `customer_email` varchar(150) DEFAULT NULL COMMENT '顧客Email / Customer email',
  `customer_phone` varchar(20) DEFAULT NULL COMMENT '顧客電話 / Customer phone',
  `shipping_address` text DEFAULT NULL COMMENT '配送地址 / Shipping address',
  `notes` text DEFAULT NULL COMMENT '備註 / Notes',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='訂單表 / Orders';

--
-- 傾印資料表的資料 `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `user_id`, `session_id`, `total_amount`, `status`, `customer_name`, `customer_email`, `customer_phone`, `shipping_address`, `notes`, `created_at`) VALUES
(1, 'ORD20260210080831824', 2, NULL, 890.00, 'pending', '123', '456@happy.com', '789', '123456789', '', '2026-02-10 07:08:31');

-- --------------------------------------------------------

--
-- 資料表結構 `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL COMMENT '訂單ID / Order ID',
  `product_id` int(11) NOT NULL COMMENT '商品ID / Product ID',
  `product_title` varchar(200) NOT NULL COMMENT '商品名稱快照 / Product title snapshot',
  `price` decimal(10,2) NOT NULL COMMENT '購買時價格 / Price at purchase',
  `quantity` int(11) NOT NULL COMMENT '數量 / Quantity',
  `subtotal` decimal(10,2) NOT NULL COMMENT '小計 / Subtotal'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='訂單明細表 / Order items';

--
-- 傾印資料表的資料 `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_title`, `price`, `quantity`, `subtotal`) VALUES
(1, 1, 3, '寶可夢卡牌禮盒', 890.00, 1, 890.00);

-- --------------------------------------------------------

--
-- 資料表結構 `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL COMMENT '所屬類別ID / Category ID',
  `title` varchar(200) NOT NULL COMMENT '商品標題 / Product title',
  `description` text DEFAULT NULL COMMENT '商品描述 / Product description',
  `price` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '商品價格 / Product price',
  `img` varchar(255) NOT NULL COMMENT '圖片檔名 / Image filename',
  `stock` int(11) DEFAULT 99 COMMENT '庫存數量 / Stock quantity',
  `sh` tinyint(1) DEFAULT 1 COMMENT '是否顯示 / Show status',
  `sort_order` int(11) DEFAULT 0 COMMENT '排序順序 / Sort order',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='商品表 / Products';

--
-- 傾印資料表的資料 `products`
--

INSERT INTO `products` (`id`, `category_id`, `title`, `description`, `price`, `img`, `stock`, `sh`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, '初音未來公仔', '限定版初音未來模型，高度約15cm / Limited edition Hatsune Miku figure, 15cm', 1280.00, 'product_1770705962_1133.jpg', 50, 1, 1, '2026-02-10 06:21:28', '2026-02-10 06:46:02'),
(2, 1, '鬼滅之刃海報組', '精美印刷海報5張組 / Demon Slayer poster set, 5 pieces', 490.00, 'product_1770706126_3820.jpg', 96, 1, 2, '2026-02-10 06:21:28', '2026-02-10 06:48:46'),
(3, 1, '寶可夢卡牌禮盒', '稀有卡牌收藏組 / Pokemon rare card collection box', 890.00, 'product_1770706198_2494.gif', 1, 1, 3, '2026-02-10 06:21:28', '2026-02-10 06:49:58'),
(4, 1, '進擊的巨人T恤', '純棉印花T恤 / Attack on Titan cotton T-shirt', 580.00, 'product_1770706290_2987.jpg', 80, 1, 4, '2026-02-10 06:21:28', '2026-02-10 06:51:30'),
(5, 2, '保濕精華液', '深層保濕配方 / Deep moisturizing serum', 680.00, 'product_1770706330_9311.jpg', 60, 1, 1, '2026-02-10 06:21:28', '2026-02-10 06:52:10'),
(6, 2, '唇膏組合包', '多色唇膏4入 / Lipstick set, 4 colors', 420.00, 'product_1770706384_2849.jpg', 100, 1, 2, '2026-02-10 06:21:28', '2026-02-10 06:53:04'),
(7, 2, '面膜禮盒', '10入面膜組 / Face mask gift set, 10 pieces', 550.00, 'product_1770706413_5078.jpg', 80, 1, 3, '2026-02-10 06:21:28', '2026-02-10 06:53:33'),
(8, 2, '香氛沐浴乳', '天然植物萃取 / Natural botanical body wash', 320.00, 'product_1770706479_6853.jpg', 120, 1, 4, '2026-02-10 06:21:28', '2026-02-10 06:54:39'),
(9, 3, '綜合維他命', '30日份膠囊 / Multivitamins 30-day supply', 450.00, 'product_1770706526_6596.jpg', 200, 1, 1, '2026-02-10 06:21:28', '2026-02-10 06:55:26'),
(10, 3, '魚油膠囊', 'Omega-3高濃度配方 / Omega-3 fish oil capsules', 720.00, 'product_1770706579_4918.jpg', 150, 1, 2, '2026-02-10 06:21:28', '2026-02-10 06:56:19'),
(11, 3, '益生菌粉', '腸道保健配方 / Probiotics powder for gut health', 580.00, 'product_1770706619_3028.jpg', 100, 1, 3, '2026-02-10 06:21:28', '2026-02-10 06:56:59'),
(12, 3, '膠原蛋白飲', '美容養顏配方 / Collagen drink for beauty', 890.00, 'product_1770706683_3335.jpg', 80, 1, 4, '2026-02-10 06:21:28', '2026-02-10 06:58:03');

-- --------------------------------------------------------

--
-- 資料表結構 `title`
--

CREATE TABLE `title` (
  `id` int(10) UNSIGNED NOT NULL,
  `img` text NOT NULL,
  `text` text NOT NULL,
  `sh` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `total`
--

CREATE TABLE `total` (
  `id` int(10) UNSIGNED NOT NULL,
  `total` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `ad`
--
ALTER TABLE `ad`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `bottom`
--
ALTER TABLE `bottom`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `user_id` (`user_id`);

--
-- 資料表索引 `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- 資料表索引 `image`
--
ALTER TABLE `image`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `mvim`
--
ALTER TABLE `mvim`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`);

--
-- 資料表索引 `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- 資料表索引 `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- 資料表索引 `title`
--
ALTER TABLE `title`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `total`
--
ALTER TABLE `total`
  ADD PRIMARY KEY (`id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `ad`
--
ALTER TABLE `ad`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `bottom`
--
ALTER TABLE `bottom`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `image`
--
ALTER TABLE `image`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `mvim`
--
ALTER TABLE `mvim`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `news`
--
ALTER TABLE `news`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `title`
--
ALTER TABLE `title`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `total`
--
ALTER TABLE `total`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `fk_cart_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- 資料表的限制式 `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- 資料表的限制式 `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
