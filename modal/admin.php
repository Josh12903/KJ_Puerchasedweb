<div class="cent">新增管理者帳號</div>
    <hr>
<form action="./api/insert.php?table=<?=$_GET['table'];?>" method="post" enctype="multipart/form-data">
    <table style="width:70%;margin:auto">
        <tr>
            <td>帳號</td>
            <td style="padding-left:12%"><input type="text" name="acc" id=""></td>
        </tr>
        <tr>
            <td>密碼</td>
            <td style="padding-left:12%"><input type="password" name="pw" id=""></td>
        </tr>
        <tr>
            <td>確認密碼</td>
            <td style="padding-left:12%"><input type="password" id=""></td>
        </tr>    
        <tr> 
        <td>
            <input type="submit" value="新增">
            <input type="reset" value="重置">
        </td>
        </tr>
    </table>

</form>
