<?php include_once "../api/db.php" ;?>
<div class="cent">編輯次選單</div>
    <hr>
<form action="./api/submenu.php?main_id=<?=$_GET['id'];?>" method="post" enctype="multipart/form-data">
    <table style="width:70%;margin:auto" id='subList'>
        <tr>
            <td>次主選單名稱</td>
            <td>次選單連結網址刪除</td>
            <td>刪除</td>
        </tr>
        <?php
        $subs=$Menu->all(['main_id'=>$_GET['id']]);
        // echo "目前的 ID 是: " . $subs;
        foreach($subs as $sub):
        ?>
        <tr>
            <td><input type="text" name="text[<?=$sub['id'];?>]" value="<?=$sub['text'];?>"></td>
            <td><input type="text" name="href[<?=$sub['id'];?>]" value="<?=$sub['href'];?>"></td>
            <td><input type="checkbox" name="del[]" value="<?=$sub['id'];?>"></td>
        </tr>
        <?php endforeach;?>
    </table>
    
    <tr>
        <div class="cent">
        <input type="submit" value="修改確定">
        <input type="reset" value="重置">
        <input type="button" value="更多次選單" onclick="more()">
        </div>
    </tr>
</form>

<script>
    function more(){
        let row=`<tr>
            <td><input type="text" name="new_text[]"></td>
            <td><input type="text" name="new_href[]"></td>
            <td></td>
        </tr>`
    $("#subList").append(row);
    }
</script>