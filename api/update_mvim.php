<?php
include_once "db.php";

if(!empty($_FILES['img']['tmp_name'])){
    move_uploaded_file($_FILES['img']['tmp_name'],"../pic/".$_FILES['img']['name']);
    $_POST['img']=$_FILES['img']['name'];
}

$Mvim->save($_POST);

to("../back/mvim.php");