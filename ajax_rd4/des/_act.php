<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.user.php");
require_once("../../lib_rd4/class.rd4.gas.php");
require_once("../../lib_rd4/class.rd4.des.php");
$converter = new Encryption;



if(!empty($_POST["act"])){
    switch ($_POST["act"]) {
    
    case "edit_descrizione_des":
    if(_USER_PERMISSIONS & perm::puo_vedere_retegas){
        
    }else{
        $res=array("result"=>"KO", "msg"=>"Non puoi" );
        echo json_encode($res);
        die();    
    }
    
    $id_des = _USER_ID_DES;
    $D = new des($id_des);
    
    $D->set_des_descrizione(CAST_TO_STRING($_POST["value"]));
                                                 
    $res=array("result"=>"OK", "msg"=>"Nome salvato.");
    echo json_encode($res);
    die();
    break;
    
    case "set_gc_des":
    if(_USER_PERMISSIONS & perm::puo_vedere_retegas){
        
    }else{
        $res=array("result"=>"KO", "msg"=>"Non puoi" );
        echo json_encode($res);
        die();    
    }
    
    $id_des = _USER_ID_DES;
    $D = new des($id_des);
    
    $D->set_des_GC_LAT(CAST_TO_STRING($_POST["lat"]));
    $D->set_des_GC_LNG(CAST_TO_STRING($_POST["lng"]));
    $D->set_des_zoom(CAST_TO_INT($_POST["zoom"]));
    
                                                          
    $res=array("result"=>"OK", "msg"=>"Salvato." );
    echo json_encode($res);
    die();
    break;
        
    case "edit_targa_gas":
        if(!(_USER_PERMISSIONS && perm::puo_vedere_retegas)){
            $res=array("result"=>"KO", "msg"=>"Non puoi" );
            echo json_encode($res);
            die();
        }
        $id_gas = CAST_TO_INT($_POST["pk"],0);
        $targa_gas = CAST_TO_STRING($_POST["value"]);
        $stmt = $db->prepare("UPDATE retegas_gas SET targa_gas=:targa_gas
                              WHERE id_gas=:id_gas LIMIT 1;");

        $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
        $stmt->bindParam(':targa_gas', $targa_gas, PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()==1){
            $res=array("result"=>"OK", "msg"=>"OK" );
        }else{
            $res=array("result"=>"KO", "msg"=>"Errore." );
        }
        echo json_encode($res);
        die();
    case "edit_descrizione_gas":
        if(!(_USER_PERMISSIONS && perm::puo_vedere_retegas)){
            $res=array("result"=>"KO", "msg"=>"Non puoi" );
            echo json_encode($res);
            die();
        }
        $id_gas = CAST_TO_INT($_POST["pk"],0);
        $descrizione_gas = CAST_TO_STRING($_POST["value"]);
        $stmt = $db->prepare("UPDATE retegas_gas SET descrizione_gas=:descrizione_gas
                              WHERE id_gas=:id_gas LIMIT 1;");

        $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
        $stmt->bindParam(':descrizione_gas', $descrizione_gas, PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()==1){
            $res=array("result"=>"OK", "msg"=>"OK" );
        }else{
            $res=array("result"=>"KO", "msg"=>"Errore." );
        }
        echo json_encode($res);
        die();
    break;    
   //-------------------------------------------------------------------------------------


    default :
    $res=array("result"=>"KO", "msg"=>"Comando '".$_POST["act"]."' non riconosciuto" );
    echo json_encode($res);
    break;

}
}
//NAME


if(!empty($_POST["name"])){
    switch ($_POST["name"]) {
    
    case "edit_targa_gas":
        $id_gas = CAST_TO_INT($_POST["pk"],0);
        $targa_gas = CAST_TO_STRING($_POST["value"]);
        $stmt = $db->prepare("UPDATE retegas_gas SET targa_gas=:targa_gas
                              WHERE id_gas=:id_gas LIMIT 1;");

        $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
        $stmt->bindParam(':targa_gas', $targa_gas, PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()==1){
            $res=array("result"=>"OK", "msg"=>"OK" );
        }else{
            $res=array("result"=>"KO", "msg"=>"Errore." );
        }
        echo json_encode($res);
        die();

    break;    
    

     default:
        $res=array("result"=>"KO", "msg"=>"Comando non riconosciuto" );
        echo json_encode($res);
     break;
    }
}