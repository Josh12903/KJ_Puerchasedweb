<?php include_once "db.php";



//edit
if(!empty($_POST['text'])){
    foreach($_POST['text'] as $id => $text){
        if(!empty($_POST['del']) && in_array($id,$_POST['del'])){
            $Menu->del($id);    
        }else{
            // $href=$_POST['href'][$id];
            $row=$Menu->find($id);
            $row['text']=$text;
            $row['href']=$_POST['href'][$id];
            $Menu->save($row);
        }
}
}

//add
if(!empty($_POST['new_text'])){
    foreach($_POST['new_text'] as $key =>$text){
        if($text!==""){
            $href=$_POST['new_href'][$key];    
            $Menu->save(['main_id'=>$_GET['main_id'],
                            'text'=>$text,
                            'href'=>$href]);
        }
    }
}

to("../back.php?do=menu");
// $_POST['new_text'];
// $_POST['new_href'];