<?php
require_once("inc/init.php");

switch ($_POST["act"]) {

    /*
    /* -------------------------PERMETTI MODIFICA ORDINI
    */
    case "permetti_modifica":
    $stmt = $db->prepare("SELECT COUNT(*) from retegas_options WHERE id_user='"._USER_ID."' AND chiave='_USER_PERMETTI_MODIFICA'");
    $stmt->execute();
    if ($stmt->fetchColumn()){
        //esiste
        $stmt = $db->prepare("UPDATE retegas_options SET valore_text = :value
        WHERE id_user='"._USER_ID."' AND chiave='_USER_PERMETTI_MODIFICA'");

    }else{
        //non esiste
        $stmt = $db->prepare("INSERT INTO retegas_options (id_user,chiave,valore_text)
        VALUES ("._USER_ID.",'_USER_PERMETTI_MODIFICA',:value)");

    }
    $stmt->bindParam(':value', $_POST['value'], PDO::PARAM_STR);
    $stmt->execute();

    $res=array("result"=>"OK", "msg"=>"Permetti modifiche : <b>".$_POST['value']."</b>" );
    echo json_encode($res);

    break;
    //-------------------------------------------------------------------------------------

    /*
    /* -------------------------USER USA LA CASSA
    */
    case "user_usa_cassa":
    $stmt = $db->prepare("SELECT COUNT(*) from retegas_options WHERE id_user='"._USER_ID."' AND chiave='_USER_USA_CASSA'");
    $stmt->execute();
    if ($stmt->fetchColumn()){
        //esiste
        $stmt = $db->prepare("UPDATE retegas_options SET valore_text = :value
        WHERE id_user='"._USER_ID."' AND chiave='_USER_USA_CASSA'");

    }else{
        //non esiste
        $stmt = $db->prepare("INSERT INTO retegas_options (id_user,chiave,valore_text)
        VALUES ("._USER_ID.",'_USER_USA_CASSA',:value)");

    }
    $stmt->bindParam(':value', $_POST['value'], PDO::PARAM_STR);
    $stmt->execute();

    $res=array("result"=>"OK", "msg"=>"Usa la cassa : <b>".$_POST['value']."</b>" );
    echo json_encode($res);

    break;
    //-------------------------------------------------------------------------------------

    /*
    /* -------------------------USER ALERT DAYS
    */
    case "user_alert_days":
    $stmt = $db->prepare("SELECT COUNT(*) from retegas_options WHERE id_user='"._USER_ID."' AND chiave='_USER_ALERT_DAYS'");
    $stmt->execute();
    if ($stmt->fetchColumn()){
        //esiste
        $stmt = $db->prepare("UPDATE retegas_options SET valore_int = :value
        WHERE id_user='"._USER_ID."' AND chiave='_USER_ALERT_DAYS'");

    }else{
        //non esiste
        $stmt = $db->prepare("INSERT INTO retegas_options (id_user,chiave,valore_int)
        VALUES ("._USER_ID.",'_USER_ALERT_DAYS',:value)");

    }
    $stmt->bindParam(':value', $_POST['value'], PDO::PARAM_INT);
    $stmt->execute();

    if($_POST['value']<1){$msg="Avviso disattivato.";}else{$msg="Avviso <b>".$_POST['value']."</b> giorni prima."; }

    $res=array("result"=>"OK", "msg"=>$msg );
    echo json_encode($res);

    break;
    //-------------------------------------------------------------------------------------

    /*
    /* -------------------------SAVE GEOLOCALIZZAZIONE
    */
    case "save_geolocalizzazione":

    //esiste
    $stmt = $db->prepare("UPDATE maaking_users SET user_gc_lat = :lat,
                                                   user_gc_lng = :lng,
                                                   city = :citta,
                                                   country = :indirizzo
                         WHERE userid='"._USER_ID."'");
    $stmt->bindParam(':lat', $_POST['lat'], PDO::PARAM_STR);
    $stmt->bindParam(':lng', $_POST['lng'], PDO::PARAM_STR);
    $stmt->bindParam(':citta', $_POST['address1'], PDO::PARAM_STR);
    $stmt->bindParam(':indirizzo', $_POST['address2'], PDO::PARAM_STR);
    $stmt->execute();

    $res=array("result"=>"OK", "msg"=>"Nuovo indirizzo salvato" );
    echo json_encode($res);

    break;
    //-------------------------------------------------------------------------------------

    /*
    /* -------------------------SAVE ANAGRAFICHE
    */
    case "save_anagrafica":

    //esiste
    $stmt = $db->prepare("UPDATE maaking_users SET fullname = :fullname,
                                                   email = :email,
                                                   tel = :tel
                         WHERE userid='"._USER_ID."'");
    $stmt->bindParam(':fullname', $_POST['fullname'], PDO::PARAM_STR);
    $stmt->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
    $stmt->bindParam(':tel', $_POST['tel'], PDO::PARAM_STR);
    $stmt->execute();

    $res=array("result"=>"OK", "msg"=>"Nuove anagrafiche salvate" );
    echo json_encode($res);

    break;
    //-------------------------------------------------------------------------------------

    /*
    /* -------------------------EDIT AMICO NOME
    */
    case "edit_amico_nome":

    if($_POST["value"]==""){
        echo json_encode(array("result"=>"KO", "msg"=>"Non puoi lasciare questo campo vuoto"));
        die();
    }
    //esiste
    $stmt = $db->prepare("UPDATE retegas_amici SET nome = :nome
                         WHERE id_referente='"._USER_ID."' AND id_amici=:id_amici LIMIT 1;");
    $stmt->bindParam(':nome', $_POST['value'], PDO::PARAM_STR);
    $stmt->bindParam(':id_amici', $_POST['pk'], PDO::PARAM_INT);
    $stmt->execute();
    if($stmt->rowCount()==1){
        $res=array("result"=>"OK", "msg"=>"" );
    }else{
        $res=array("result"=>"KO", "msg"=>"Errore." );
    }

    echo json_encode($res);

    break;
    //-------------------------------------------------------------------------------------

    /*
    /* -------------------------EDIT AMICO INDIRIZZO
    */
    case "edit_amico_indirizzo":


    //esiste
    $stmt = $db->prepare("UPDATE retegas_amici SET indirizzo = :indirizzo
                         WHERE id_referente='"._USER_ID."' AND id_amici=:id_amici LIMIT 1;");
    $stmt->bindParam(':indirizzo', $_POST['value'], PDO::PARAM_STR);
    $stmt->bindParam(':id_amici', $_POST['pk'], PDO::PARAM_INT);
    $stmt->execute();
    if($stmt->rowCount()==1){
        $res=array("result"=>"OK", "msg"=>"" );
    }else{
        $res=array("result"=>"KO", "msg"=>"Errore." );
    }

    echo json_encode($res);

    break;
    //-------------------------------------------------------------------------------------

    /*
    /* -------------------------EDIT AMICO TELEFONO
    */
    case "edit_amico_telefono":


    //esiste
    $stmt = $db->prepare("UPDATE retegas_amici SET telefono = :telefono
                         WHERE id_referente='"._USER_ID."' AND id_amici=:id_amici LIMIT 1;");
    $stmt->bindParam(':telefono', $_POST['value'], PDO::PARAM_STR);
    $stmt->bindParam(':id_amici', $_POST['pk'], PDO::PARAM_INT);
    $stmt->execute();
    if($stmt->rowCount()==1){
        $res=array("result"=>"OK", "msg"=>"" );
    }else{
        $res=array("result"=>"KO", "msg"=>"Errore." );
    }

    echo json_encode($res);

    break;
    //-------------------------------------------------------------------------------------

    /*
    /* -------------------------EDIT AMICO ATTIVO
    */
    case "edit_amico_attivo":

    if($_POST["value"]=="true"){
        $status = 1;
    }else{
        $status = 0;
    }

    //esiste
    $stmt = $db->prepare("UPDATE retegas_amici SET status = :status
                         WHERE id_referente='"._USER_ID."' AND id_amici=:id_amici LIMIT 1;");
    $stmt->bindParam(':status', $status, PDO::PARAM_INT);
    $stmt->bindParam(':id_amici', $_POST['id'], PDO::PARAM_INT);
    $stmt->execute();
    if($stmt->rowCount()==1){
        $res=array("result"=>"OK", "msg"=>"Status modificato" );
    }else{
        $res=array("result"=>"KO", "msg"=>"Errore." );
    }

    echo json_encode($res);

    break;
    //-------------------------------------------------------------------------------------

    /*
    /* -------------------------AGGIUNGI
    */
    case "aggiungi_amico":

    if($_POST["value"]==""){
        echo json_encode(array("result"=>"KO", "msg"=>"Non puoi lasciare questo campo vuoto"));
        die();
    }

    //esiste
    $stmt = $db->prepare("INSERT INTO retegas_amici (id_referente,nome) VALUES ('"._USER_ID."',:nome) ;");

    $stmt->bindParam(':nome', $_POST['value'], PDO::PARAM_STR);
    $stmt->execute();
    if($stmt->rowCount()==1){
        $res=array("result"=>"OK", "msg"=>"Amico aggiunto" );
    }else{
        $res=array("result"=>"KO", "msg"=>"Errore." );
    }

    echo json_encode($res);

    break;
    //-------------------------------------------------------------------------------------

    /*
    /* -------------------------DELETE AMICO
    */
    case "delete_amico":

    if($_POST["value"]==""){
        echo json_encode(array("result"=>"KO", "msg"=>"Non puoi lasciare questo campo vuoto"));
        die();
    }

    //esiste
    $stmt = $db->prepare("UPDATE retegas_amici SET status = 0, is_visible=0
                         WHERE id_referente='"._USER_ID."' AND id_amici=:id_amici LIMIT 1;");

    $stmt->bindParam(':id_amici', $_POST['value'], PDO::PARAM_STR);
    $stmt->execute();
    if($stmt->rowCount()==1){
        $res=array("result"=>"OK", "msg"=>"Amico tolto" );
    }else{
        $res=array("result"=>"KO", "msg"=>"Errore." );
    }

    echo json_encode($res);

    break;
    //-------------------------------------------------------------------------------------


    //-------------------------------------------------------------------------------------

    /*
    /* -------------------------AGGIUNGI RICARICA
    */
    case "ricarica":

    if($_POST["euro"]==""){
        echo json_encode(array("result"=>"KO", "msg"=>"Non puoi lasciare questo campo vuoto"));
        die();
    }

    if($_POST["note"]==""){
        echo json_encode(array("result"=>"KO", "msg"=>"Serve un commento"));
        die();
    }

    //esiste
    $stmt = $db->prepare("INSERT INTO retegas_options (id_user,
                                        chiave,
                                        valore_text,
                                        note_1,
                                        id_gas,
                                        valore_real)
                                        VALUES (
                                        "._USER_ID.",
                                        'PREN_MOV_CASSA',
                                        :note,
                                        :documento,
                                        "._USER_ID_GAS.",
                                        :euro);");

    $stmt->bindParam(':note', $_POST['note'], PDO::PARAM_STR);
    $stmt->bindParam(':documento', $_POST['documento'], PDO::PARAM_STR);
    $stmt->bindParam(':euro', $_POST['euro'], PDO::PARAM_STR);
    $stmt->execute();

    if($stmt->rowCount()==1){
        $last_id = $db->lastInsertId();
        $res=array("result"=>"OK", "msg"=>"Prenotazione aggiunta", "html"=>"<div class=\"well well-sm margin-top-5 pren_box\">Pochi istanti fa,  <span class=\"badge bg-color-greenLight font-md\">&euro; ".round($_POST["euro"],2)."</span> ".$_POST["documento"]." - ".$_POST["note"]."; <button class=\"btn btn-danger btn-xs elimina_mov pull-right\" rel=\"".$last_id."\"><i class=\"fa fa-trash-o\"></i> <span class=\"hidden-xs\">Elimina</span></button><div class=\"clearfix\"></div></div>" );

    }else{
        $res=array("result"=>"KO", "msg"=>"Errore." );
    }

    echo json_encode($res);

    break;
    //-------------------------------------------------------------------------------------

    /*
    /* -------------------------DELETE PRENOTAZIONE
    */
    case "delete_prenotazione":

        $stmt = $db->prepare("SELECT * FROM retegas_options WHERE id_option=:id_prenotazione AND chiave='PREN_MOV_CASSA' AND id_user='"._USER_ID."'");
        $stmt->bindParam(':id_prenotazione', $_POST["id_prenotazione"], PDO::PARAM_INT);
        $stmt->execute();

       if($stmt->rowCount()==1){
            $stmt = $db->prepare("DELETE FROM retegas_options WHERE id_option=:id_prenotazione");
            $stmt->bindParam(':id_prenotazione', $_POST["id_prenotazione"], PDO::PARAM_INT);
            $stmt->execute();
            $result="OK";
            $msg="Prenotazione eliminata";
       }else{
            $result="KO";
            $msg="Non possibile;";
       }

       $res=array("result"=>$result, "msg"=>$msg );
       echo json_encode($res);

   break;
   //-------------------------------------------------------------------------------------


    default :
    $res=array("result"=>"KO", "msg"=>"Comando '".$_POST["act"]."' non riconosciuto" );
    echo json_encode($res);
    break;

}