<?php
include_once "db.php";
foreach($_POST['id'] as $id){
    if(!empty($_POST['del']) && in_array($id,$_POST['del'])){
        $Mvim->del($id);
    }else{
        $row=$Mvim->find($id);
        
        $row['sh']=(isset($_POST['sh']) && in_array($id,$_POST['sh']))?1:0;
        // $Title->save(['id'=>$id,'sh'=>(123),'text'])
        $Mvim->save($row);
    }
}


to("../back.php?=mvim");

