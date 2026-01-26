<?php
include_once "db.php";

// $table=$_GET['table'];
// $DB=${ucfirst($table)};



if(!empty($_FILES['img']['tmp_name'])){
    move_uploaded_file($_FILES['img']['tmp_name'],"../pic/".$_FILES['img']['name']);
    $_POST['img']=$_FILES['img']['name'];
}

$_POST['sh']=($Title->count(['sh'=>1])==0)?1:0;

$Title->save($_POST);

to("../back.php?do=title");