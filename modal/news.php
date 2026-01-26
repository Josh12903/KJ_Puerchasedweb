<div class="cent">新增最新消息</div>
    <hr>
<form action="./api/insert.php?table=<?=$_GET['table'];?>" method="post" enctype="multipart/form-data">
    <table style="width:80%;margin:auto">
        <tr>
            <td>最新消息</td>
            <td>
                <textarea name="text" style="width:75%;height:100px"></textarea>
            </td>
        </tr>
        <tr>
            <td><input type="submit" value="新增"></td>
            <td><input type="reset" value="重置"></td>
        </tr>
    </table>
</form>

