<?php
include_once "db.php";
$table=$_GET['table'];
$DB=${ucfirst($table)};

$ids=[];
switch($table){
    case "mvim":
    case "image":
        $ids=$_POST['id'];
        break;
        default:
            $ids=array_keys($_POST['text']);
    }


            
            

foreach($ids as $id){
    if(!empty($_POST['del']) && in_array($id,$_POST['del'])){
        $DB->del($id);
    }else{
        $row=$DB->find($id);
            switch($table){
                case "admin":
                    $row['acc']=$_POST['acc'][$id];
                    $row['pw']=$_POST['pw'][$id];
                break;
                case "menu":
                    $row['text']=$_POST['text'][$id];
                    $row['href']=$_POST['href'][$id];
                break;
                case "admin":
                    $row['acc']=$_POST['acc'][$id];
                    $row['pw']=$_POST['pw'][$id];
                break;
                case "mvim":
                case "image":
                    $row['sh']=(isset($_POST['sh']) && in_array($id,$_POST['sh']))?1:0;
                break;
                case "title":
                    $row['text']=$_POST['text'][$id];
                    $row['sh']=(isset($_POST['sh']) && $_POST['sh']==$id)?1:0;
                break;
                default:
                    $row['text']=$_POST['text'][$id];
                $row['sh']=(isset($_POST['sh']) && in_array($id,$_POST['sh']))?1:0;
            }
        // $row['text']=$text;
        // $row['sh']=(isset($_POST['sh']) && $_POST['sh']==$id)?1:0;

        $DB->save($row);
    }
}

to("../back.php?do=$table");

?>