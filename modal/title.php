<div class="cent">新增標題區圖片</div>
    <hr>
<form action="./api/insert.php?table=<?=$_GET['table'];?>" method="post" enctype="multipart/form-data">
    <table style="width:80%;margin:auto">
        <tr>
            <td>新增網站標題圖片</td>
            <td><input type="file" name="img" id=""></td>
        </tr>
        <tr>
            <td>新增替代文字</td>
            <td><input type="text" name="text" id=""></td>
        </tr>
        <tr>
            <td><input type="submit" value="新增"></td>
            <td><input type="reset" value="重置"></td>
        </tr>
    </table>

</form>
