<?php
$skip_check=true;
require_once("inc/init.php");

$code=CAST_TO_STRING($_GET["mail"]);

if($code<>""){

    $sql="UPDATE retegas_options SET
            valore_int=1,
            note_1=''
            WHERE
            note_1=:code
            LIMIT 1;";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':code', $code, PDO::PARAM_STR);
    $stmt->execute();

    if($stmt->rowCount()==1){
        echo "OK";
    }else{
        echo "KO";
    }

}