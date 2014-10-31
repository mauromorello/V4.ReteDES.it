<?php
$skip_check=true;
require_once("inc/init.php");
if ($_GET["do"]=="c"){
    $code=CAST_TO_INT($_GET["c"],0);
    if($code>0){
    $stmt = $db->prepare("DELETE FROM maaking_users WHERE code=:code AND (lastlogin='0000-00-00 00:00:00');");
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