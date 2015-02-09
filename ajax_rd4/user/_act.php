<?php
require_once("inc/init.php");
$converter = new Encryption;

switch ($_POST["act"]) {
    /*
    /* -------------------------PERMESSi SUPERPOTERI
    */

    case "del_user":
        if(_USER_PERMISSIONS & perm::puo_gestire_utenti){
            $id_utente = CAST_TO_INT($converter->decode($_POST['id']),0);
            if($id_utente==0){
                $res= array("result"=>"KO", "msg"=>"KO"); echo json_encode($res);die();
            }else{
                $stmt = $db->prepare("DELETE from maaking_users WHERE userid=:id_utente LIMIT 1;");
                $stmt->bindParam(':id_utente', $id_utente, PDO::PARAM_INT);
                $stmt->execute();
                if($stmt->rowCount()==1){
                    $res= array("result"=>"OK", "msg"=>"Utente eliminato"); echo json_encode($res);die();
                    //Da chiamare CleanDB;
                }else{
                    $res= array("result"=>"KO", "msg"=>"KO, errore"); echo json_encode($res);die();
                }
            }
        }else{
            $res= array("result"=>"KO", "msg"=>"KO, non hai i permessi"); echo json_encode($res);die();
        }


    break;
    case "save_pwd_user":

    $old_password = clean($_POST["acc_oldpwd"]);
    $new_password = clean($_POST["acc_newpwd"]);
    $new_password2 = clean($_POST["acc_newpwd2"]);

    if($old_password=="" ||
       $new_password=="" ||
       $new_password2=="" ){
        $res=array("result"=>"KO", "msg"=>"Uno dei campi è vuoto" );
        echo json_encode($res);
        die();
        break;
    }

    if($new_password<>$new_password2){
        $res=array("result"=>"KO", "msg"=>"Le due password non coincidono" );
        echo json_encode($res);
        die();
        break;
    }

    $md5password = md5($new_password);

    $stmt = $db->prepare("UPDATE maaking_users SET password = :password
                         WHERE userid='"._USER_ID."' AND password=:old_password");
    $stmt->bindParam(':password', $md5password, PDO::PARAM_STR);
    $stmt->bindParam(':old_password', md5($old_password), PDO::PARAM_STR);
    $stmt->execute();
    if($stmt->rowCount()==1){
        $res=array("result"=>"OK", "msg"=>"Password aggiornata. E' necessario fare un nuovo login per applicare la modifica." );
        echo json_encode($res);
        die();
    }else{
        $res=array("result"=>"OK", "msg"=>"La vecchia password non è stata riconosciuta" );
        echo json_encode($res);
        die();
    }
    break;

    case "save_acc_user":
    $username = clean($_POST["username"]);
    if($username==""){
        $res=array("result"=>"KO", "msg"=>"Username vuoto" );
        echo json_encode($res);
        die();
        break;
    }
    //username esistente
    $stmt = $db->prepare("SELECT * from maaking_users WHERE username=:username;");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    if($stmt->rowCount()>1){
        $res=array("result"=>"KO", "msg"=>"Username già usata" );
        echo json_encode($res);
        die();
        break;

    }

    $stmt = $db->prepare("UPDATE maaking_users SET username = :username
                         WHERE userid='"._USER_ID."'");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();

    $res=array("result"=>"OK", "msg"=>"Nome utente aggiornato. E' necessario fare un nuovo login per applicare la modifica." );
    echo json_encode($res);
    die();
    break;


    break;

    case "abilitazioni":

    $tipo = CAST_TO_INT($_POST['tipo']);
    $v = CAST_TO_INT($_POST['value']);
    $userid = CAST_TO_INT($converter->decode($_POST['id']),0);
    if($userid==0){
        $res= array("result"=>"KO", "msg"=>"KO"); echo json_encode($res);die();
    }
    $stmt = $db->prepare("SELECT id_gas, user_permission FROM  maaking_users WHERE userid = :userid LIMIT 1;");
    $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $act_perm = $row["user_permission"];

    $bitwise=false;
    $gestionali=false;

    switch ($tipo) {
        case perm::puo_eliminare_messaggi:
            if($v>0){
                $new_perm = $act_perm |  perm::puo_eliminare_messaggi;
                $r = "Moderare i feedback: PERMESSO";
            }else{
                $new_perm = $act_perm &  (~perm::puo_eliminare_messaggi);
                $r = "Moderare i feedback: NEGATO";
            }
            $bitwise=true;
            $gestionali = true;
            break;
        case perm::puo_gestire_la_cassa:
            if($v>0){
                $new_perm = $act_perm |  perm::puo_gestire_la_cassa;
                $r = "Fare il cassiere: PERMESSO";
            }else{
                $new_perm = $act_perm &  (~perm::puo_gestire_la_cassa);
                $r = "Fare il cassiere: NEGATO";
            }
            $bitwise=true;
            $gestionali = true;
            break;
        case perm::puo_gestire_utenti:
            if($v>0){
                $new_perm = $act_perm |  perm::puo_mod_perm_user_gas;
                $new_perm = $new_perm |  perm::puo_gestire_utenti;
                $r = "Gestire gli utenti: PERMESSO";
            }else{
                $new_perm = $act_perm &  (~perm::puo_mod_perm_user_gas);
                $new_perm = $new_perm &  (~perm::puo_gestire_utenti);
                $r = "Gestire gli utenti: NEGATO";
            }
            $gestionali = true;
            $bitwise=true;
            break;
        case perm::puo_vedere_tutti_ordini:
            if($v>0){
                $new_perm = $act_perm |  perm::puo_vedere_tutti_ordini;
                $r = "Supervisionare gli ordini: PERMESSO";
            }else{
                $new_perm = $act_perm &  (~perm::puo_vedere_tutti_ordini);
                $r = "Supervisionare gli ordini: NEGATO";
            }
            $gestionali = true;
            $bitwise=true;
            break;
        case perm::puo_creare_gas:
            if($v>0){
                $new_perm = $act_perm |  perm::puo_creare_gas;
                $r = "Gestire il gas: PERMESSO";
            }else{
                $new_perm = $act_perm &  (~perm::puo_creare_gas);
                $r = "Gestire il gas: NEGATO";
            }
            $gestionali = true;
            $bitwise=true;
            break;
        case perm::puo_vedere_retegas:
            if($v>0){
                if(_USER_PERMISSIONS & perm::puo_gestire_retegas){
                    $new_perm = $act_perm |  perm::puo_vedere_retegas;
                    $r = "Gestire il gas: PERMESSO";
                }
            }else{
                $new_perm = $act_perm &  (~perm::puo_vedere_retegas);
                $r = "Gestire il gas: NEGATO";
            }
            $gestionali = true;
            $bitwise=true;
            break;
        case perm::puo_avere_amici:
            if($v>0){
                $new_perm = $act_perm |  perm::puo_avere_amici;
                $r = "Gestire la rubrica amici: PERMESSO";
            }else{
                $new_perm = $act_perm &  (~perm::puo_avere_amici);
                $r = "Gestire la rubrica amici: NEGATO";
            }
            $gestionali = false;
            $bitwise=true;
            break;
        case perm::puo_creare_ditte:
            if($v>0){
                $new_perm = $act_perm |  perm::puo_creare_ditte;
                $new_perm = $new_perm |  perm::puo_creare_listini;
                $r = "Creare e gestire i fornitori: PERMESSO";
            }else{
                $new_perm = $act_perm &  (~perm::puo_creare_ditte);
                $new_perm = $new_perm &  (~perm::puo_creare_listini);
                $r = "Creare e gestire i fornitori: NEGATO";
            }
            $gestionali = false;
            $bitwise=true;
            break;
        case perm::puo_creare_ordini:
            if($v>0){
                $new_perm = $act_perm |  perm::puo_creare_ordini;
                $r = "Creare e gestire gli ordini: PERMESSO";
            }else{
                $new_perm = $act_perm &  (~perm::puo_creare_ordini);
                $r = "Creare e gestire gli ordini: NEGATO";
            }
            $gestionali = false;
            $bitwise=true;
            break;
        case perm::puo_partecipare_ordini:
            if($v>0){
                $new_perm = $act_perm |  perm::puo_partecipare_ordini;
                $r = "Partecipare agli ordini: PERMESSO";
            }else{
                $new_perm = $act_perm &  (~perm::puo_partecipare_ordini);
                $r = "Partecipare agli ordini: NEGATO";
            }
            $gestionali = false;
            $bitwise=true;
            break;
        case perm::puo_postare_messaggi:
            if($v>0){
                $new_perm = $act_perm |  perm::puo_postare_messaggi;
                $r = "Postare i messaggi: PERMESSO";
            }else{
                $new_perm = $act_perm &  (~perm::puo_postare_messaggi);
                $r = "Postare i messaggi: NEGATO";
            }
            $gestionali = false;
            $bitwise=true;
            break;
        case perm::puo_operare_con_crediti:
            if($v>0){
                $new_perm = $act_perm |  perm::puo_operare_con_crediti;
                $r = "Operare con crediti altrui: PERMESSO";
            }else{
                $new_perm = $act_perm &  (~perm::puo_operare_con_crediti);
                $r = "Operare con crediti altrui: NEGATO";
            }
            $gestionali = false;
            $bitwise=true;
            break;
        case "gestire_help":
            if(_USER_PERMISSIONS & perm::puo_gestire_retegas){
                $stmt = $db->prepare("DELETE FROM retegas_options WHERE id_user=:userid AND chiave='_USER_PUO_MODIFICARE_HELP' LIMIT 1;");
                $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
                $stmt->execute();
                $r = "Modificare gli help : NEGATO;";
                if($v>0){
                    $stmt = $db->prepare("INSERT INTO retegas_options (id_user,chiave,valore_text) VALUES (:userid,'_USER_PUO_MODIFICARE_HELP','SI');");
                    $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
                    $stmt->execute();
                    $r = "Modificare gli help : PERMESSO;";
                }
                $res=array("result"=>"OK", "msg"=>$r );
            }else{
               $r = "Non hai i permessi necessari";
               $res= array("result"=>"KO", "msg"=>$r);
            }

            echo json_encode($res);
            die();
            break;
        default:
            $new_perm=0;
            break;

    }

    $res=array("result"=>"KO", "msg"=>"Errore generico..." );
    if($bitwise){
        if($gestionali){
            $stmt = $db->prepare("SELECT id_referente_gas FROM  retegas_gas WHERE id_gas = :id_gas LIMIT 1;");
            $stmt->bindParam(':id_gas', $row["id_gas"], PDO::PARAM_INT);
            $stmt->execute();
            $g = $stmt->fetch(PDO::FETCH_ASSOC);
            if(($g["id_referente_gas"]==_USER_ID) OR (_USER_PERMISSIONS & perm::puo_gestire_retegas)){
                if($g["id_referente_gas"]==_USER_ID){$chi="Sei un Referente GAS:<br>";}
                if(_USER_PERMISSIONS & perm::puo_vedere_retegas){$chi.="Sei un admin:<br>";}
                $stmt = $db->prepare("UPDATE maaking_users SET user_permission= :new_perm WHERE userid = :userid LIMIT 1;");
                $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
                $stmt->bindParam(':new_perm', $new_perm, PDO::PARAM_INT);
                $stmt->execute();
                $res=array("result"=>"OK", "msg"=>$chi.$r );
            }else{
                $res=array("result"=>"KO", "msg"=>"Non hai i permessi necessari" );
            }
        }else{
            if(((_USER_PERMISSIONS & perm::puo_gestire_utenti) AND ($row["id_gas"]==_USER_ID_GAS)) OR (_USER_PERMISSIONS & perm::puo_gestire_retegas)){
                if((_USER_PERMISSIONS & perm::puo_gestire_utenti) AND ($row["id_gas"]==_USER_ID_GAS)){$chi="Sei un Gestore Utenti :<br>";}
                if(_USER_PERMISSIONS & perm::puo_gestire_retegas){$chi.="Sei un admin:<br>";}
                $stmt = $db->prepare("UPDATE maaking_users SET user_permission= :new_perm WHERE userid = :userid LIMIT 1;");
                $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
                $stmt->bindParam(':new_perm', $new_perm, PDO::PARAM_INT);
                $stmt->execute();
                $res=array("result"=>"OK", "msg"=>$chi.$r );
            }else{
                $res=array("result"=>"KO", "msg"=>"Non hai i permessi necessari" );
            }
        }
    }



    echo json_encode($res);

    break;
    //-------------------------------------------------------------------------------------

     /*
    /* -------------------------AVVISIAPERTURE
    */
    case "avvisi_aperture":
    $stmt = $db->prepare("SELECT COUNT(*) from retegas_options WHERE id_user='"._USER_ID."' AND chiave='_V4_AVVISIAPERTURE'");
    $stmt->execute();
    if ($stmt->fetchColumn()){
        //esiste
        $stmt = $db->prepare("UPDATE retegas_options SET valore_text = :value
        WHERE id_user='"._USER_ID."' AND chiave='_V4_AVVISIAPERTURE'");

    }else{
        //non esiste
        $stmt = $db->prepare("INSERT INTO retegas_options (id_user,chiave,valore_text)
        VALUES ("._USER_ID.",'_V4_AVVISIAPERTURE',:value)");

    }
    $stmt->bindParam(':value', $_POST['value'], PDO::PARAM_STR);
    $stmt->execute();

    $res=array("result"=>"OK", "msg"=>"Avvisi aperture : <b>".$_POST['value']."</b>" );
    echo json_encode($res);

    break;
    //-------------------------------------------------------------------------------------

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
    /* -------------------------USER ELIMINA HELP
    */
    case "user_elimina_help":

    $stmt = $db->prepare("INSERT INTO retegas_options (
                            id_user,
                            chiave,
                            valore_int,
                            valore_text)
                            VALUES (
                            "._USER_ID.",
                            '_HELP_V4_HIDE',
                            1,
                            :value)");
    $stmt->bindParam(':value', $_POST['value'], PDO::PARAM_STR);
    $stmt->execute();

    $msg="Help nascosto. Potrai riattivarlo dalla pagina delle impostazioni oppure vederlo separatamente nella sezione ''AIUTO'' ";
    $res=array("result"=>"OK", "msg"=>$msg );
    echo json_encode($res);

    break;
    //-------------------------------------------------------------------------------------
    /*
    /* -------------------------USER RIPRISTINA HELP
    */
    case "ripristina_help":

    $stmt = $db->prepare("DELETE FROM retegas_options
                            WHERE
                            id_user = '"._USER_ID."' AND
                            chiave = '_HELP_V4_HIDE';");
    $stmt->execute();

    $msg="Aiuti ripristinati. Ricarica la pagina per rivederli.";
    $res=array("result"=>"OK", "msg"=>$msg );
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

        if(_GAS_CASSA_BONIFICO_AUTOMATICO){
            $sql = "INSERT INTO retegas_cassa_utenti (   id_utente ,
                                                                id_gas,
                                                                importo ,
                                                                segno ,
                                                                tipo_movimento ,
                                                                descrizione_movimento ,
                                                                data_movimento ,
                                                                id_cassiere ,
                                                                registrato ,
                                                                data_registrato ,
                                                                contabilizzato ,
                                                                data_contabilizzato,
                                                                numero_documento
                                                              )VALUES(
                                                              '"._USER_ID."',
                                                              '"._USER_ID_GAS."',
                                                              :importo,
                                                              '+',
                                                              '1',
                                                              :descrizione,
                                                              NOW(),
                                                              '"._USER_ID."',
                                                              'si',
                                                              NOW(),
                                                              'no',
                                                              NULL,
                                                              :documento
                                                              )";
                        $stmt = $db->prepare($sql);
                        $stmt->bindParam(':documento', $_POST['documento'], PDO::PARAM_STR);
                        $stmt->bindParam(':descrizione', $_POST['note'], PDO::PARAM_STR);
                        $stmt->bindParam(':importo', $_POST['euro'], PDO::PARAM_STR);
                        $stmt->execute();
        }


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