<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.user.php");
require_once("../../lib_rd4/class.rd4.gas.php");
require_once("../../lib_rd4/htmlpurifier-4.7.0/library/HTMLPurifier.auto.php");

$converter = new Encryption;

switch ($_POST["act"]) {

    case "elimina_help":

    if(!_USER_PUO_MODIFICARE_HELP){
        $res=array("result"=>"KO", "msg"=>"Non puoi gestire gli help" );
        echo json_encode($res);
        die();
    }

    $id_option=CAST_TO_INT($_POST["id_option"]);

    $SQL = "delete from retegas_options WHERE id_option=:id_option LIMIT 1;";
    $stmt = $db->prepare($SQL);

    $stmt->bindParam(':id_option', $id_option, PDO::PARAM_INT);
    $stmt->execute();
    if($stmt->rowCount()==1){
        $res=array("result"=>"OK", "msg"=>"Eliminato" );
    }else{
        $res=array("result"=>"KO", "msg"=>"Errore" );
    }



    echo json_encode($res);
    break;
    //

    default :
    $res=array("result"=>"KO", "msg"=>"Comando '".$_POST["act"]."' non riconosciuto" );
    echo json_encode($res);
    break;

}
