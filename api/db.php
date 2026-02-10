<?php
/**
 * =====================================================
 * 資料庫核心類別 / Database Core Class
 * =====================================================
 * 功能：PDO ORM 封裝，提供 CRUD 操作
 * Purpose: PDO ORM wrapper providing CRUD operations
 * 
 * 修改說明 / Modification Notes:
 * - 新增 categories, products, cart_items, orders, order_items 模型
 * - 新增購物車相關函式
 * - 保留原有表的支援
 * =====================================================
 */

session_start();

/**
 * DB 類別 / DB Class
 * 簡易 ORM 封裝 / Simple ORM wrapper
 */
class DB {
    // 資料庫連線設定 / Database connection settings
    // 請根據您的環境修改 / Modify according to your environment
    private $dsn = "mysql:host=localhost;dbname=aidb;charset=utf8mb4";
    private $username = 'root';  // 資料庫帳號 / DB username
    private $password = '';      // 資料庫密碼 / DB password
    private $table;
    private $pdo;
    
    /**
     * 建構函式 / Constructor
     * @param string $table 資料表名稱 / Table name
     */
    public function __construct($table) {
        $this->table = $table;
        try {
            $this->pdo = new PDO($this->dsn, $this->username, $this->password);
            // 設定錯誤模式 / Set error mode
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("資料庫連線失敗 / Database connection failed: " . $e->getMessage());
        }
    }

    /**
     * 取得所有資料 / Get all records
     * @param mixed $arg 查詢條件 / Query conditions
     * @return array 查詢結果 / Query results
     */
    public function all(...$arg) {
        $sql = "SELECT * FROM `{$this->table}` ";

        if (isset($arg[0])) {
            if (is_array($arg[0])) {
                $tmp = $this->arrayToSql($arg[0]);
                $sql .= " WHERE " . implode(" AND ", $tmp);
            } else {
                $sql .= $arg[0];
            }
        }
        if (isset($arg[1])) {
            $sql .= $arg[1];
        }

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 取得單筆資料 / Get single record
     * @param mixed $id 主鍵ID或條件陣列 / Primary key ID or condition array
     * @return array|false 查詢結果 / Query result
     */
    public function find($id) {
        $sql = "SELECT * FROM `{$this->table}` ";
        if (is_array($id)) {
            $tmp = $this->arrayToSql($id);
            $sql .= " WHERE " . implode(" AND ", $tmp);
        } else {
            $sql .= " WHERE `id`='$id' ";
        }
        return $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * 儲存資料（新增或更新）/ Save data (insert or update)
     * @param array $array 資料陣列 / Data array
     */
    public function save($array) {
        if (isset($array['id'])) {
            $this->update($array);
        } else {
            $this->insert($array);
        }
    }

    /**
     * 計算筆數 / Count records
     * @param mixed $arg 查詢條件 / Query conditions
     * @return int 資料筆數 / Record count
     */
    public function count(...$arg) {
        $sql = "SELECT COUNT(*) FROM `{$this->table}` ";
        if (isset($arg[0])) {
            if (is_array($arg[0])) {
                $tmp = $this->arrayToSql($arg[0]);
                $sql .= " WHERE " . implode(" AND ", $tmp);
            } else {
                $sql .= $arg[0];
            }
        }
        if (isset($arg[1])) {
            $sql .= $arg[1];
        }
        return $this->pdo->query($sql)->fetchColumn();
    }

    /**
     * 更新資料 / Update record
     * @param array $array 資料陣列（須包含id）/ Data array (must include id)
     * @return int 影響筆數 / Affected rows
     */
    public function update($array) {
        $sql = "UPDATE `{$this->table}`";
        $tmp = $this->arrayToSql($array);
        $sql .= " SET " . join(", ", $tmp);
        $sql .= " WHERE id='{$array['id']}'";
        
        return $this->pdo->exec($sql);
    }

    /**
     * 新增資料 / Insert record
     * @param array $array 資料陣列 / Data array
     * @return int 影響筆數 / Affected rows
     */
    public function insert($array) {
        $sql = "INSERT INTO `{$this->table}` ";
        $keys = array_keys($array);
        $sql .= "(`" . join("`,`", $keys) . "`)";
        $sql .= " VALUES ('" . join("','", $array) . "')";

        return $this->pdo->exec($sql);
    }

    /**
     * 取得最後插入的ID / Get last inserted ID
     * @return string 最後插入的ID / Last insert ID
     */
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }

    /**
     * 刪除資料 / Delete record
     * @param mixed $id 主鍵ID或條件陣列 / Primary key ID or condition array
     * @return int 影響筆數 / Affected rows
     */
    public function del($id) {
        $sql = "DELETE FROM `{$this->table}` ";
        if (is_array($id)) {
            $tmp = $this->arrayToSql($id);
            $sql .= " WHERE " . implode(" AND ", $tmp);
        } else {
            $sql .= " WHERE `id`='$id' ";
        }
        return $this->pdo->exec($sql);
    }

    /**
     * 陣列轉SQL條件 / Convert array to SQL conditions
     * @param array $array 條件陣列 / Condition array
     * @return array SQL條件陣列 / SQL condition array
     */
    private function arrayToSql($array) {
        $tmp = [];
        foreach ($array as $key => $value) {
            // 簡單跳過 id 用於 update SET / Skip id for update SET
            if ($key === 'id') continue;
            $tmp[] = "`$key`='$value'";
        }
        return $tmp;
    }

    /**
     * 執行原生SQL查詢 / Execute raw SQL query
     * @param string $sql SQL語句 / SQL statement
     * @return array 查詢結果 / Query results
     */
    public function query($sql) {
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 執行原生SQL（無回傳）/ Execute raw SQL (no return)
     * @param string $sql SQL語句 / SQL statement
     * @return int 影響筆數 / Affected rows
     */
    public function exec($sql) {
        return $this->pdo->exec($sql);
    }
}

/**
 * 全域查詢函式 / Global query function
 * @param string $sql SQL語句 / SQL statement
 * @return array 查詢結果 / Query results
 */
function q($sql) {
    $dsn = "mysql:host=localhost;dbname=aidb;charset=utf8mb4";
    $pdo = new PDO($dsn, 'root', '');
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * 頁面導向函式 / Page redirect function
 * @param string $url 目標網址 / Target URL
 */
function to($url) {
    header("location: " . $url);
    exit();
}

// =====================================================
// 資料表模型實例化 / Database Model Instances
// =====================================================

// 原有表 / Original tables
$Title = new DB('title');       // 網站標題 / Site title
$Ad = new DB('ad');             // 跑馬燈廣告 / Marquee ads
$Mvim = new DB('mvim');         // 動畫圖片（保留但可能不再使用）/ Animation images (legacy)
$News = new DB('news');         // 最新消息 / News
$Image = new DB('image');       // 圖片（保留但可能不再使用）/ Images (legacy)
$Admin = new DB('admin');       // 管理員 / Administrators
$Menu = new DB('menu');         // 選單 / Menu
$Total = new DB('total');       // 訪客計數 / Visitor count
$Bottom = new DB('bottom');     // 頁尾版權 / Footer

// 新增表 / New tables
$Category = new DB('categories');    // 商品分類 / Product categories
$Product = new DB('products');       // 商品 / Products
$CartItem = new DB('cart_items');    // 購物車項目 / Cart items
$Order = new DB('orders');           // 訂單 / Orders
$OrderItem = new DB('order_items');  // 訂單明細 / Order items

// =====================================================
// 訪客計數器 / Visitor Counter
// =====================================================
if (!isset($_SESSION['view'])) {
    $_SESSION['view'] = 1;
    $total = $Total->find(1);
    if ($total) {
        $total['total']++;
        $Total->save($total);
    }
}

// =====================================================
// 購物車輔助函式 / Shopping Cart Helper Functions
// =====================================================

/**
 * 取得當前購物車識別 / Get current cart identifier
 * 優先使用 user_id（登入者），否則使用 session_id
 * Prioritize user_id (logged-in), otherwise use session_id
 * 
 * @return array ['type' => 'user'|'session', 'id' => value]
 */
function getCartIdentifier() {
    if (isset($_SESSION['admin'])) {
        // 已登入，使用 admin 帳號作為識別 / Logged in, use admin account
        global $Admin;
        $admin = $Admin->find(['acc' => $_SESSION['admin']]);
        return ['type' => 'user', 'id' => $admin['id'] ?? 0];
    } else {
        // 未登入，使用 session_id / Not logged in, use session_id
        return ['type' => 'session', 'id' => session_id()];
    }
}

/**
 * 取得購物車項目 / Get cart items
 * @return array 購物車商品列表 / Cart product list
 */
function getCartItems() {
    global $CartItem, $Product;
    $identifier = getCartIdentifier();
    
    if ($identifier['type'] === 'user') {
        $items = $CartItem->all(['user_id' => $identifier['id']]);
    } else {
        $items = $CartItem->all(['session_id' => $identifier['id']]);
    }
    
    // 附加商品詳細資料 / Attach product details
    $result = [];
    foreach ($items as $item) {
        $product = $Product->find($item['product_id']);
        if ($product) {
            $item['product'] = $product;
            $item['subtotal'] = $product['price'] * $item['quantity'];
            $result[] = $item;
        }
    }
    
    return $result;
}

/**
 * 加入購物車 / Add to cart
 * @param int $productId 商品ID / Product ID
 * @param int $quantity 數量 / Quantity
 * @return bool 是否成功 / Success status
 */
function addToCart($productId, $quantity = 1) {
    global $CartItem, $Product;
    
    // 確認商品存在 / Verify product exists
    $product = $Product->find($productId);
    if (!$product) return false;
    
    $identifier = getCartIdentifier();
    
    // 檢查是否已在購物車 / Check if already in cart
    if ($identifier['type'] === 'user') {
        $existing = $CartItem->find([
            'user_id' => $identifier['id'],
            'product_id' => $productId
        ]);
    } else {
        $existing = $CartItem->find([
            'session_id' => $identifier['id'],
            'product_id' => $productId
        ]);
    }
    
    if ($existing) {
        // 更新數量 / Update quantity
        $existing['quantity'] += $quantity;
        $CartItem->save($existing);
    } else {
        // 新增項目 / Add new item
        $data = [
            'product_id' => $productId,
            'quantity' => $quantity
        ];
        
        if ($identifier['type'] === 'user') {
            $data['user_id'] = $identifier['id'];
        } else {
            $data['session_id'] = $identifier['id'];
        }
        
        $CartItem->insert($data);
    }
    
    return true;
}

/**
 * 更新購物車數量 / Update cart quantity
 * @param int $cartItemId 購物車項目ID / Cart item ID
 * @param int $quantity 新數量 / New quantity
 * @return bool 是否成功 / Success status
 */
function updateCartQuantity($cartItemId, $quantity) {
    global $CartItem;
    
    if ($quantity <= 0) {
        // 數量為0則刪除 / Delete if quantity is 0
        return $CartItem->del($cartItemId);
    }
    
    $item = $CartItem->find($cartItemId);
    if (!$item) return false;
    
    $item['quantity'] = $quantity;
    $CartItem->save($item);
    return true;
}

/**
 * 從購物車移除 / Remove from cart
 * @param int $cartItemId 購物車項目ID / Cart item ID
 * @return bool 是否成功 / Success status
 */
function removeFromCart($cartItemId) {
    global $CartItem;
    return $CartItem->del($cartItemId) > 0;
}

/**
 * 計算購物車總計 / Calculate cart total
 * @return array ['count' => 項目數, 'total' => 總金額]
 */
function getCartTotal() {
    $items = getCartItems();
    $count = 0;
    $total = 0;
    
    foreach ($items as $item) {
        $count += $item['quantity'];
        $total += $item['subtotal'];
    }
    
    return ['count' => $count, 'total' => $total];
}

/**
 * 清空購物車 / Clear cart
 * @return bool 是否成功 / Success status
 */
function clearCart() {
    global $CartItem;
    $identifier = getCartIdentifier();
    
    if ($identifier['type'] === 'user') {
        return $CartItem->del(['user_id' => $identifier['id']]);
    } else {
        return $CartItem->del(['session_id' => $identifier['id']]);
    }
}

/**
 * 登入後合併購物車 / Merge cart after login
 * 將 session 購物車合併到 user 購物車
 * Merge session cart to user cart
 */
function mergeCartOnLogin() {
    global $CartItem, $Admin;
    
    if (!isset($_SESSION['admin'])) return;
    
    $admin = $Admin->find(['acc' => $_SESSION['admin']]);
    if (!$admin) return;
    
    $userId = $admin['id'];
    $sessionId = session_id();
    
    // 取得 session 購物車項目 / Get session cart items
    $sessionItems = $CartItem->all(['session_id' => $sessionId]);
    
    foreach ($sessionItems as $item) {
        // 檢查 user 購物車是否已有該商品 / Check if product exists in user cart
        $existing = $CartItem->find([
            'user_id' => $userId,
            'product_id' => $item['product_id']
        ]);
        
        if ($existing) {
            // 合併數量 / Merge quantity
            $existing['quantity'] += $item['quantity'];
            $CartItem->save($existing);
            $CartItem->del($item['id']);
        } else {
            // 轉移給 user / Transfer to user
            $item['user_id'] = $userId;
            $item['session_id'] = null;
            $CartItem->save($item);
        }
    }
}

/**
 * 建立訂單 / Create order
 * @param array $customerInfo 顧客資訊 / Customer info
 * @return int|false 訂單ID或失敗 / Order ID or false
 */
function createOrder($customerInfo = []) {
    global $Order, $OrderItem, $CartItem;
    
    $cartItems = getCartItems();
    if (empty($cartItems)) return false;
    
    $identifier = getCartIdentifier();
    $cartTotal = getCartTotal();
    
    // 生成訂單編號 / Generate order number
    $orderNumber = 'ORD' . date('YmdHis') . rand(100, 999);
    
    // 建立訂單 / Create order
    $orderData = [
        'order_number' => $orderNumber,
        'total_amount' => $cartTotal['total'],
        'status' => 'pending',
        'customer_name' => $customerInfo['name'] ?? '',
        'customer_email' => $customerInfo['email'] ?? '',
        'customer_phone' => $customerInfo['phone'] ?? '',
        'shipping_address' => $customerInfo['address'] ?? '',
        'notes' => $customerInfo['notes'] ?? ''
    ];
    
    if ($identifier['type'] === 'user') {
        $orderData['user_id'] = $identifier['id'];
    } else {
        $orderData['session_id'] = $identifier['id'];
    }
    
    $Order->insert($orderData);
    $orderId = $Order->lastInsertId();
    
    // 建立訂單明細 / Create order items
    foreach ($cartItems as $item) {
        $OrderItem->insert([
            'order_id' => $orderId,
            'product_id' => $item['product_id'],
            'product_title' => $item['product']['title'],
            'price' => $item['product']['price'],
            'quantity' => $item['quantity'],
            'subtotal' => $item['subtotal']
        ]);
    }
    
    // 清空購物車 / Clear cart
    clearCart();
    
    return $orderId;
}
?>