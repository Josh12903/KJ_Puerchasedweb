<div class="cent">新增動態文字廣告</div>
    <hr>
<form action="./api/insert.php?table=<?=$_GET['table'];?>" method="post" enctype="multipart/form-data">
    <table style="width:80%;margin:auto">
        <tr>
            <td>動態文字廣告</td>
            <td><input type="text" name="text" id=""></td>
        </tr>
        <tr>
            <td><input type="submit" value="新增"></td>
            <td><input type="reset" value="重置"></td>
        </tr>
    </table>

</form>
