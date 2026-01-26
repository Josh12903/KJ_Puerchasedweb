<?php
/**
 * =====================================================
 * 登入處理 API / Login Handler API
 * =====================================================
 * 功能：驗證管理員登入並設定 Session
 * Purpose: Verify admin login and set session
 * 
 * 修改說明 / Modification Notes:
 * - 新增登入後購物車合併功能
 * - Added cart merge after login
 * =====================================================
 */

include_once "db.php";

// 驗證帳號密碼 / Verify credentials
$chk = $Admin->count($_POST);

if ($chk) {
    // 設定 Session / Set session
    $_SESSION['admin'] = $_POST['acc'];
    
    // 合併購物車 / Merge cart
    mergeCartOnLogin();
    
    // 導向後台 / Redirect to admin
    to("../back.php");
} else {
    // 登入失敗 / Login failed
    ?>
    <script>
        alert("帳號或密碼錯誤，請重新登入\nIncorrect account or password, please try again");
        location.href = "../index.php?page=login";
    </script>
    <?php
}
?>