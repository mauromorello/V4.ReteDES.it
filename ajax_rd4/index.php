<?php
$skip_check=true;
require_once("inc/init.php");
if ($_GET["do"]=="c"){
    $code=CAST_TO_INT($_GET["c"],0);
    if($code>0){
        $stmt = $db->prepare("DELETE FROM maaking_users WHERE code=:code AND (lastlogin='0000-00-00 00:00:00') LIMIT 1;");
        $stmt->bindParam(':code', $code, PDO::PARAM_INT);
        $stmt->execute();
        if($stmt->rowCount()==1){
            echo "Account eliminato.";
        }else{
            echo "Hai già effettuato un accesso. Per eliminare il tuo account contatta gli amministratori.";
        }

    }else{
        echo "ko";
    }
}
if ($_GET["do"]=="r"){
    $code=CAST_TO_INT($_GET["c"],0);
    if($code>0){
        $stmt = $db->prepare("DELETE FROM retegas_options WHERE valore_int=:code AND chiave='_REFERENTE_EXTRA' LIMIT 1;");
        $stmt->bindParam(':code', $code, PDO::PARAM_INT);
        $stmt->execute();
        if($stmt->rowCount()==1){
            echo "Referenza eliminata.";
        }else{
            echo "Impossibile eliminare referenza.";
        }

    }else{
        echo "ko";
    }
}