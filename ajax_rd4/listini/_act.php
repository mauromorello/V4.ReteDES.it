<?php
require_once("inc/init.php");
if(!empty($_POST["act"])){
    switch ($_POST["act"]) {

        default :
        $res=array("result"=>"KO", "msg"=>"Comando '".$_POST["act"]."' non riconosciuto" );
        echo json_encode($res);
        break;

    }
}
if(!empty($_POST["name"])){
    switch ($_POST["name"]) {

     case "descrizione_listini":
        //esiste
        $nuovo = trim(strip_tags($_POST['value']));
        if($nuovo<>""){}else{$res=array("result"=>"KO", "msg"=>"Non può essere vuoto" );echo json_encode($res);die();}

        $stmt = $db->prepare("UPDATE retegas_listini SET descrizione_listini= :descrizione_listini
                             WHERE id_listini=:id_listini AND id_utenti='"._USER_ID."'");
        $stmt->bindParam(':id_listini', $_POST['pk'], PDO::PARAM_INT);
        $stmt->bindParam(':descrizione_listini', $nuovo, PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()==1){;
            $res=array("result"=>"OK", "msg"=>"Nuovo nome salvato." );
        }else{
            $res=array("result"=>"KO", "msg"=>"Errore nel DB..." );
        }

        echo json_encode($res);
     break;

     default :
    $res=array("result"=>"KO", "msg"=>"Comando '".$_POST["act"]."' non riconosciuto" );
    echo json_encode($res);
    break;
    }
}