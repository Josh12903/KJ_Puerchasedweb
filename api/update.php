<?php
include_once "db.php";
$table=$_GET['table'];
$db=${ucfirst($table)};

if(!empty($_FILES['img']['tmp_name'])){
    move_uploaded_file($_FILES['img']['tmp_name'],"../pic/".$_FILES['img']['name']);
    $_POST['img']=$_FILES['img']['name'];
}

$db->save($_POST);

to("../back.php?do=$table");

?>