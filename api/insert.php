<?php

include_once "db.php";

$table=$_GET['table'];
// echo $table;
$DB = ${ucfirst($table)};

if(!empty($_FILES['img']['tmp_name'])){
    move_uploaded_file($_FILES['img']['tmp_name'],"../pic/".$_FILES['img']['name']);
    $_POST['img']=$_FILES['img']['name'];
}



switch($table){
    case "title":
        $_POST['sh']=($DB->count(['sh'=>1])==0)?1:0;
    break;
    default:
        if($table!='admin'){
            $_POST['sh']=1;
        }
    
}



$DB->save($_POST);

to("../back.php?do=$table");


?>