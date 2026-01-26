<?php include_once "db.php"; 

$table=$_GET['table'];
$DB=${ucfirst($table)};

switch($_GET['table']){
    case "total":
        $row=$Total->find(1);
        $row['total']=$_POST['total'];
        $Total->save($row);
    break;
    case "bottom":
        $row=$Bottom->find(1);
        $row['bottom']=$_POST['bottom'];
        $Bottom->save($row);
    break;
}

to("../back.php?do=$table");