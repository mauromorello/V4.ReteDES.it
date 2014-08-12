<?php
require_once("inc/init.php");

switch ($_POST["act"]) {

    /*
    /* -------------------------SALVA HELP
    */
    case "salva_help":
        if (_USER_PUO_MODIFICARE_HELP){


            $stmt = $db->prepare("SELECT valore_int from retegas_options WHERE valore_text=:pagina AND chiave='_HELP_V4' ORDER BY id_option DESC LIMIT 1;");
            $stmt->bindParam(':pagina', $_POST['pagina'], PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch();

            $indice = $row[0] +1;

            $sHTML =$_POST["sHTML"];
            $sHTML = strip_tags($sHTML,"<a><table><tr><td><code><small><alt><b><strong><br><ul><li><ol><oi><hr><h1><h2><h3><h4><h5><h6><p><img><hr>");

            $msg = '<code class="note pull-right">Revisione <b>'.$indice.'</b> di '._USER_FULLNAME.", "._USER_GAS_NOME.", il ".date('d/m/Y H:i').'</code>';

            $stmt = $db->prepare("INSERT INTO retegas_options (valore_int,chiave,valore_text,id_user,note_1) VALUES ('".$indice."','_HELP_V4',:pagina,'"._USER_ID."',:sHTML)");
            $stmt->bindParam(':pagina', $_POST['pagina'], PDO::PARAM_STR);
            $stmt->bindParam(':sHTML', $sHTML, PDO::PARAM_STR);
            $stmt->execute();

            $res=array("result"=>"OK", "msg"=>$msg );

            echo json_encode($res);
            die();
        }else{
            $res=array("result"=>"KO", "msg"=>"Non hai i permessi per modificare gli help" );
            echo json_encode($res);
            die();
        }
    break;

    default :
    $res=array("result"=>"KO", "msg"=>"Comando '".$_POST["act"]."' non riconosciuto" );
    echo json_encode($res);
    break;

}