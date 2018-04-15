<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.user.php");
require_once("../../lib_rd4/class.rd4.gas.php");
$converter = new Encryption;

function do_user_custom_1($userid,$custom_1){
    global $db;
    if($userid>0){
            if(!(_USER_PERMISSIONS & perm::puo_gestire_utenti)){
                $res=array("result"=>"KO", "msg"=>"Non puoi" );
                echo json_encode($res);
                die();
            }
        }else{
            $userid=_USER_ID;
        }

        $U = new user($userid);



        if(($U->custom_3_proprieta==1) AND !(_USER_PERMISSIONS & perm::puo_gestire_utenti)){
            $res=array("result"=>"KO", "msg"=>"Il campo 1 non può essere modificato dall'utente" );
            echo json_encode($res);
            die();
        }

        if($U->custom_1_tipo==1){
            //NUMERO
            if(!is_numeric($custom_1)){
                $res=array("result"=>"KO", "msg"=>"Il campo 1 deve essere un numero" );
                echo json_encode($res);
                die();
            }
        }

        if($U->custom_1_tipo==2){
            //NUMERO
            if((CAST_TO_STRING($custom_1)=="SI") OR ((CAST_TO_STRING($custom_1)=="NO"))){

            }else{
                $res=array("result"=>"KO", "msg"=>"Il campo 1 deve essere SI o NO" );
                echo json_encode($res);
                die();
            }
        }

        if($U->custom_1_tipo==3){
            //DATA-ORA
            if(validateDateIta($custom_1)){

            }else{
                $res=array("result"=>"KO", "msg"=>"Il campo 1 deve essere una data nel formato GG/MM/AAAA" );
                echo json_encode($res);
                die();
            }
        }

        $sql="UPDATE maaking_users SET
                custom_1=:custom_1
              WHERE userid=:userid LIMIT 1;";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
        $stmt->bindParam(':custom_1',$custom_1, PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()<>1){
                $res=array("result"=>"KO", "msg"=>"Nessuna modifica." );
                echo json_encode($res);
                die();
            break;

        }

        $res=array("result"=>"OK", "msg"=>"Valore salvato" );
        echo json_encode($res);
        die();
}
function do_user_custom_2($userid,$custom_2){
    global $db;
    if($userid>0){
            if(!(_USER_PERMISSIONS & perm::puo_gestire_utenti)){
                $res=array("result"=>"KO", "msg"=>"Non puoi" );
                echo json_encode($res);
                die();
            }
        }else{
            $userid=_USER_ID;
        }

        $U = new user($userid);



        if(($U->custom_2_proprieta==1) AND !(_USER_PERMISSIONS & perm::puo_gestire_utenti)){
            $res=array("result"=>"KO", "msg"=>"Il campo 1 non può essere modificato dall'utente" );
            echo json_encode($res);
            die();
        }

        if($U->custom_2_tipo==1){
            //NUMERO
            if(!is_numeric($custom_2)){
                $res=array("result"=>"KO", "msg"=>"Il campo 1 deve essere un numero" );
                echo json_encode($res);
                die();
            }
        }

        if($U->custom_2_tipo==2){
            //NUMERO
            if((CAST_TO_STRING($custom_2)=="SI") OR ((CAST_TO_STRING($custom_2)=="NO"))){

            }else{
                $res=array("result"=>"KO", "msg"=>"Il campo 1 deve essere SI o NO" );
                echo json_encode($res);
                die();
            }
        }

        if($U->custom_2_tipo==3){
            //DATA-ORA
            if(validateDateIta($custom_2)){

            }else{
                $res=array("result"=>"KO", "msg"=>"Il campo 1 deve essere una data nel formato GG/MM/AAAA" );
                echo json_encode($res);
                die();
            }
        }

        $sql="UPDATE maaking_users SET
                custom_2=:custom_2
              WHERE userid=:userid LIMIT 1;";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
        $stmt->bindParam(':custom_2',$custom_2, PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()<>1){
                $res=array("result"=>"KO", "msg"=>"Nessuna modifica." );
                echo json_encode($res);
                die();
            break;

        }

        $res=array("result"=>"OK", "msg"=>"Valore salvato" );
        echo json_encode($res);
        die();
}
function do_user_custom_3($userid,$custom_3){
    global $db;
    if($userid>0){
            if(!(_USER_PERMISSIONS & perm::puo_gestire_utenti)){
                $res=array("result"=>"KO", "msg"=>"Non puoi" );
                echo json_encode($res);
                die();
            }
        }else{
            $userid=_USER_ID;
        }

        $U = new user($userid);



        if(($U->custom_3_proprieta==1) AND !(_USER_PERMISSIONS & perm::puo_gestire_utenti)){
            $res=array("result"=>"KO", "msg"=>"Il campo 1 non può essere modificato dall'utente" );
            echo json_encode($res);
            die();
        }

        if($U->custom_3_tipo==1){
            //NUMERO
            if(!is_numeric($custom_3)){
                $res=array("result"=>"KO", "msg"=>"Il campo 1 deve essere un numero" );
                echo json_encode($res);
                die();
            }
        }

        if($U->custom_3_tipo==2){
            //NUMERO
            if((CAST_TO_STRING($custom_3)=="SI") OR ((CAST_TO_STRING($custom_3)=="NO"))){

            }else{
                $res=array("result"=>"KO", "msg"=>"Il campo deve essere SI o NO" );
                echo json_encode($res);
                die();
            }
        }

        if($U->custom_3_tipo==3){
            //DATA-ORA
            if(validateDateIta($custom_3)){

            }else{
                $res=array("result"=>"KO", "msg"=>"Il campo deve essere una data nel formato GG/MM/AAAA" );
                echo json_encode($res);
                die();
            }
        }

        $sql="UPDATE maaking_users SET
                custom_3=:custom_3
              WHERE userid=:userid LIMIT 1;";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
        $stmt->bindParam(':custom_3',$custom_3, PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()<>1){
                $res=array("result"=>"KO", "msg"=>"Nessuna modifica." );
                echo json_encode($res);
                die();
            break;

        }

        $res=array("result"=>"OK", "msg"=>"Valore salvato" );
        echo json_encode($res);
        die();
}



if(!empty($_POST["act"])){
    switch ($_POST["act"]) {

        case "do_user_custom_1":

            $userid = CAST_TO_INT($_POST["userid"],0);
            $custom_1 = CAST_TO_STRING($_POST["custom_1"]);

            do_user_custom_1($userid,$custom_1);


        break;
        case "do_user_custom_2":

            $userid = CAST_TO_INT($_POST["userid"],0);
            $custom_2 = CAST_TO_STRING($_POST["custom_2"]);

            do_user_custom_2($userid,$custom_2);


        break;
        case "do_user_custom_3":

            $userid = CAST_TO_INT($_POST["userid"],0);
            $custom_3 = CAST_TO_STRING($_POST["custom_3"]);

            do_user_custom_3($userid,$custom_3);


        break;

        case "disattiva_telegram":

            $stmt = $db->prepare("UPDATE maaking_users SET te_connected='' WHERE userid="._USER_ID." LIMIT 1");
            $stmt->execute();

            $res=array("result"=>"OK", "msg"=>"Telegram disattivato" );
            echo json_encode($res);
            die();
        break;

    case "save_new_username":

    $id = CAST_TO_INT($_POST["id"]);
    $username = trim(clean($_POST["username"]));
    if($username==''){
        $res=array("result"=>"KO", "msg"=>"Username vuoto" );
        echo json_encode($res);
        die();
        break;
    }

    //username esistente
    $stmt = $db->prepare("SELECT * from maaking_users WHERE username=:username;");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    if($stmt->rowCount()>0){
        $res=array("result"=>"KO", "msg"=>"Username già usata" );
        echo json_encode($res);
        die();
        break;

    }

    $U = new user($id);
    if(
        (_USER_PERMISSIONS & perm::puo_gestire_utenti)
         AND ($U->id_gas==_USER_ID_GAS)
         OR
         (_USER_PERMISSIONS & perm::puo_vedere_retegas)
         ){

         $U->set_username($username);

        //MANDARE MAIL
        //mail
            $mailFROM = _USER_MAIL;
            $fullnameFROM = _USER_FULLNAME;
            $mailTO = $U->email;
            $fullnameTO = $U->fullname;
            $oggetto = "[reteDES] $fullnameFROM ha modificato la tua username. ";
            $profile = new Template('../../email_rd4/modificato_username.html');
            $profile->set("newUSERNAME", $username );
            $profile->set("fullnameFROM", $fullnameFROM);
            $messaggio = $profile->output();

            if(SEmail($fullnameTO,$mailTO,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"username")){
                $res=array("result"=>"OK", "msg"=>"Username cambiata. E' stata spedita una mail all'utente per avvisarlo." );
                echo json_encode($res);
                die();
                break;
            }else{
                $res=array("result"=>"KO", "msg"=>"Errore mail." );
                echo json_encode($res);
                die();
                break;
            }

    }else{
        $res= array("result"=>"KO", "msg"=>"KO, errore");
        echo json_encode($res);die();
    }

    case "save_new_password":

    $converter = new Encryption;

    $id = CAST_TO_INT($converter->decode($_POST["id"]),0);
    if($id<1){
        $res=array("result"=>"KO", "msg"=>"Errore." );
        echo json_encode($res);
        die();
        break;
    }

    $password = trim(clean($_POST["password"]));
    if($password==''){
        $res=array("result"=>"KO", "msg"=>"Password vuota" );
        echo json_encode($res);
        die();
        break;
    }

    $U = new user($id);
    if(
        (_USER_PERMISSIONS & perm::puo_gestire_utenti)
         AND ($U->id_gas==_USER_ID_GAS)
         OR
         (_USER_PERMISSIONS & perm::puo_vedere_retegas)
         ){

         $U->set_password($password);

        //MANDARE MAIL
        //mail
            $mailFROM = _USER_MAIL;
            $fullnameFROM = _USER_FULLNAME;
            $mailTO = $U->email;
            $fullnameTO = $U->fullname;
            $oggetto = "[reteDES] $fullnameFROM ha modificato la tua password. ";
            $profile = new Template('../../email_rd4/modificato_password.html');
            $profile->set("newPASSWORD", $password );
            $profile->set("fullnameFROM", $fullnameFROM);
            $messaggio = $profile->output();

            if(SEmail($fullnameTO,$mailTO,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"password")){
                $res=array("result"=>"OK", "msg"=>"Password cambiata. E' stata spedita una mail all'utente per avvisarlo." );
                echo json_encode($res);
                die();
                break;
            }else{
                $res=array("result"=>"KO", "msg"=>"Errore invio mail. L\'utente potrebbe non avere una email valida o raggiungibile." );
                echo json_encode($res);
                die();
                break;
            }

    }else{
        $res= array("result"=>"KO", "msg"=>"KO, errore");
        echo json_encode($res);die();
    }

    break;

    case "save_new_gas":

    $converter = new Encryption;

    $id = CAST_TO_INT($converter->decode($_POST["id"]),0);
    if($id<1){
        $res=array("result"=>"KO", "msg"=>"Errore." );
        echo json_encode($res);
        die();
        break;
    }

    $nuovo_gas = CAST_TO_INT($_POST["nuovo_gas"]);

    $U = new user($id);

    if(_USER_PERMISSIONS & perm::puo_vedere_retegas){

         $U->set_id_gas($nuovo_gas);

         $res=array("result"=>"OK", "msg"=>"Utente $id GAS $nuovo_gas aggiornato." );
         echo json_encode($res);
         die();
         break;


    }else{
        $res= array("result"=>"KO", "msg"=>"KO, errore");
        echo json_encode($res);die();
    }

    break;

    case "save_stato_utente":

    $converter = new Encryption;

    $id = CAST_TO_INT($converter->decode($_POST["id"]),0);
    if($id<1){
        $res=array("result"=>"KO", "msg"=>"Errore." );
        echo json_encode($res);
        die();
        break;
    }

    $nuovo_stato = CAST_TO_INT($_POST["nuovo_stato"],0,3);

    $U = new user($id);
    if(
        (_USER_PERMISSIONS & perm::puo_gestire_utenti)
         AND ($U->id_gas==_USER_ID_GAS)
         OR
         (_USER_PERMISSIONS & perm::puo_vedere_retegas)
         ){

         $U->set_isactive($nuovo_stato);

         if($nuovo_stato==2){
             if(clean(CAST_TO_STRING($_POST["motivo_sospensione"]))<>""){
                $U->set_motivo_sospensione(clean(CAST_TO_STRING($_POST["motivo_sospensione"])));
             }else{
                $G = new gas($U->id_gas);
                $motivo = $G->get_motivo_sospensione();
                $U->set_motivo_sospensione($motivo);
                unset($G);
             }
         }else{
              $U->delete_motivo_sospensione();
         }





         $res=array("result"=>"OK", "msg"=>"'Stato' aggiornato." );
         echo json_encode($res);
         die();
         break;


    }else{
        $res= array("result"=>"KO", "msg"=>"KO, errore");
        echo json_encode($res);die();
    }

    break;

    case "save_contatti_utente":

    $converter = new Encryption;

    $id = CAST_TO_INT($converter->decode($_POST["id"]),0);
    if($id<1){
        $res=array("result"=>"KO", "msg"=>"Errore." );
        echo json_encode($res);
        die();
        break;
    }

    

    $U = new user($id);
    if(
        (_USER_PERMISSIONS & perm::puo_gestire_utenti)
         AND ($U->id_gas==_USER_ID_GAS)
         OR
         (_USER_PERMISSIONS & perm::puo_vedere_retegas)
         ){
         $email_utente = trim(clean($_POST["email_utente"]));
         $U->set_email($email_utente);
         
         $email_utente_2 = trim(clean($_POST["email_utente_2"]));
         $U->set_email_2($email_utente_2);
         
         $email_utente_3 = trim(clean($_POST["email_utente_3"]));
         $U->set_email_3($email_utente_3);
         
         $tel = trim(clean($_POST["tel"]));
         $U->set_tel($tel);

         $res=array("result"=>"OK", "msg"=>"Contatti aggiornati;" );
         echo json_encode($res);
         die();
         break;


    }else{
        $res= array("result"=>"KO", "msg"=>"KO, errore");
        echo json_encode($res);die();
    }

    break;
    
    case "save_user_profile":

    $converter = new Encryption;

    $id = CAST_TO_INT($converter->decode($_POST["id"]),0);
    if($id<1){
        $res=array("result"=>"KO", "msg"=>"Errore." );
        echo json_encode($res);
        die();
        break;
    }

    $profile = trim(clean($_POST["profile"]));

    $U = new user($id);
    if(
        (_USER_PERMISSIONS & perm::puo_gestire_utenti)
         AND ($U->id_gas==_USER_ID_GAS)
         OR
         (_USER_PERMISSIONS & perm::puo_vedere_retegas)
         ){

         $U->set_profile($profile);

         $res=array("result"=>"OK", "msg"=>"Note aggiornate;" );
         echo json_encode($res);
         die();
         break;


    }else{
        $res= array("result"=>"KO", "msg"=>"KO, errore");
        echo json_encode($res);die();
    }

    break;

    case "save_user_address":
        if(_USER_PERMISSIONS & perm::puo_gestire_utenti){
            $id_utente = CAST_TO_INT($converter->decode($_POST['id']),0);
            if($id_utente==0){
                $res= array("result"=>"KO", "msg"=>"KO"); echo json_encode($res);die();
            }else{

                $u_gc_lat=CAST_TO_FLOAT($_POST["u_gc_lat"]);
                $u_gc_lng=CAST_TO_FLOAT($_POST["u_gc_lng"]);
                $country = clean($_POST["country"]);
                $city = clean($_POST["city"]);

                $stmt = $db->prepare("UPDATE maaking_users
                                            SET
                                         user_gc_lat=:u_gc_lat,
                                         user_gc_lng=:u_gc_lng,
                                         city=:city,
                                         country=:country
                                         WHERE userid=:id_utente LIMIT 1;");
                $stmt->bindParam(':u_gc_lat', $u_gc_lat, PDO::PARAM_STR);
                $stmt->bindParam(':u_gc_lng', $u_gc_lng, PDO::PARAM_STR);
                $stmt->bindParam(':city', $city, PDO::PARAM_STR);
                $stmt->bindParam(':country', $country, PDO::PARAM_STR);
                $stmt->bindParam(':id_utente', $id_utente, PDO::PARAM_INT);
                $stmt->execute();

                if($stmt->rowCount()==1){
                    $res=array("result"=>"OK", "msg"=>"Indirizzo aggiornato." );
                    echo json_encode($res);
                    die();
                }else{
                    $res=array("result"=>"OK", "msg"=>"Indirizzo non aggiornato" );
                    echo json_encode($res);
                    die();
                }

            }
        }else{
            $res= array("result"=>"KO", "msg"=>"KO, non hai i permessi"); echo json_encode($res);die();
        }


    break;


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

    case "save_email_extra":

    $seconda_email = clean($_POST["seconda_email"]);
    $terza_email = clean($_POST["terza_email"]);
    
    if($seconda_email==_USER_MAIL){
        $res=array("result"=>"KO", "msg"=>"Non puoi inserire due email uguali" );
        echo json_encode($res);
        die();    
    }
    if($terza_email==_USER_MAIL){
        $res=array("result"=>"KO", "msg"=>"Non puoi inserire due email uguali" );
        echo json_encode($res);
        die();    
    }
   
    
    
    $seconda_email_code = md5(md5($seconda_email));

    $mailFROM = _USER_MAIL;
    $fullnameFROM = _USER_FULLNAME;
    $fullnameTO = _USER_FULLNAME;
    
    


    $stmt = $db->prepare("DELETE FROM retegas_options WHERE id_user="._USER_ID." AND chiave='_USER_SECONDA_EMAIL' LIMIT 1;");
    $stmt->execute();
    if(CAST_TO_STRING($seconda_email)<>""){
        $stmt = $db->prepare("INSERT INTO retegas_options (id_user,chiave,valore_text, valore_int, note_1) VALUES ("._USER_ID.",'_USER_SECONDA_EMAIL',:seconda_email,0,:seconda_email_code);");
        $stmt->bindParam(':seconda_email', $seconda_email, PDO::PARAM_STR);
        $stmt->bindParam(':seconda_email_code', $seconda_email_code, PDO::PARAM_STR);
        $stmt->execute();

        $mailTO = $seconda_email;

        $oggetto = "[reteDES] Aggiunta indirizzo EMAIL";
        $profile = new Template('../../email_rd4/conferma_mail_aggiuntiva.html');
        $profile->set("fullname", _USER_FULLNAME );
        $profile->set("gas", _USER_GAS_NOME );
        $profile->set("email", $seconda_email );
        $profile->set("activation_code", $seconda_email_code );
        $messaggio = $profile->output();

        SEmail($fullnameTO,$mailTO,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"EmailEXTRA");

    }

    
    $terza_email_code = md5(md5($terza_email));

    $stmt = $db->prepare("DELETE FROM retegas_options WHERE id_user="._USER_ID." AND chiave='_USER_TERZA_EMAIL' LIMIT 1;");
    $stmt->execute();
    if(CAST_TO_STRING($terza_email)<>""){
        $stmt = $db->prepare("INSERT INTO retegas_options (id_user,chiave,valore_text, valore_int, note_1) VALUES ("._USER_ID.",'_USER_TERZA_EMAIL',:terza_email,0,:terza_email_code);");
        $stmt->bindParam(':terza_email', $terza_email, PDO::PARAM_STR);
        $stmt->bindParam(':terza_email_code', $terza_email_code, PDO::PARAM_STR);
        $stmt->execute();

        $mailTO = $terza_email;

        $oggetto = "[reteDES] Aggiunta indirizzo EMAIL";
        $profile = new Template('../../email_rd4/conferma_mail_aggiuntiva.html');
        $profile->set("fullname", _USER_FULLNAME );
        $profile->set("gas", _USER_GAS_NOME );
        $profile->set("email", $terza_email );
        $profile->set("activation_code", $terza_email_code );
        $messaggio = $profile->output();

        SEmail($fullnameTO,$mailTO,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"EmailEXTRA");



    }



    $res=array("result"=>"OK", "msg"=>"Email secondarie aggiornate." );
    echo json_encode($res);
    die();

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
    $stmt->execute();
    if($stmt->rowCount()>1){
        $res=array("result"=>"KO", "msg"=>"Username già usata" );
        echo json_encode($res);
        die();
        break;

    }

    $stmt = $db->prepare("UPDATE maaking_users SET username = :username
                         WHERE userid='"._USER_ID."' AND "._USER_ID.">0");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();

    $res=array("result"=>"OK", "msg"=>"Nome utente aggiornato. E' necessario fare un nuovo login per applicare la modifica." );
    echo json_encode($res);
    die();
    break;


    break;

    case "abilitazioni":

    $tipo = $_POST['tipo'];
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
        case "gestione_des":
            if($v>0){
                if(_USER_PERMISSIONS & perm::puo_vedere_retegas){
                    $new_perm = $act_perm |  perm::puo_vedere_retegas;
                    $r = "Gestire il proprio DES: PERMESSO";
                }
            }else{
                $new_perm = $act_perm &  (~perm::puo_vedere_retegas);
                $r = "Gestire il proprio DES: NEGATO";
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
        case "gestione_help":
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
            
        case "supervisore_listini":
            if(_USER_PERMISSIONS & perm::puo_creare_gas){
                $stmt = $db->prepare("DELETE FROM retegas_options WHERE id_user=:userid AND chiave='_USER_SUPERVISORE_LISTINI' LIMIT 1;");
                $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
                $stmt->execute();
                $r = "Supervisionare listini : NEGATO;";
                if($v>0){
                    $stmt = $db->prepare("INSERT INTO retegas_options (id_user,chiave,valore_text) VALUES (:userid,'_USER_SUPERVISORE_LISTINI','SI');");
                    $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
                    $stmt->execute();
                    $r = "Supervisionare listini : PERMESSO;";
                }
                $res=array("result"=>"OK", "msg"=>$r );
            }else{
               $r = "Non hai i permessi necessari";
               $res= array("result"=>"KO", "msg"=>$r);
            }

            echo json_encode($res);
            die();
            break;    
        case "supervisore_anagrafiche":
            if(_USER_PERMISSIONS & perm::puo_gestire_retegas){
                $stmt = $db->prepare("DELETE FROM retegas_options WHERE id_user=:userid AND chiave='_USER_SUPERVISORE_ANAGRAFICHE' LIMIT 1;");
                $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
                $stmt->execute();
                $r = "Supervisionare anagrafiche : NEGATO;";
                if($v>0){
                    $stmt = $db->prepare("INSERT INTO retegas_options (id_user,chiave,valore_text) VALUES (:userid,'_USER_SUPERVISORE_ANAGRAFICHE','SI');");
                    $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
                    $stmt->execute();
                    $r = "Supervisionare anagrafiche : PERMESSO;";
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
    /* -------------------------notifiche telegram
    */
    case "notifiche_telegram":
    $stmt = $db->prepare("SELECT COUNT(*) from retegas_options WHERE id_user='"._USER_ID."' AND chiave='_USER_NOTIFICHE_TELEGRAM'");
    $stmt->execute();
    if ($stmt->fetchColumn()){
        //esiste
        $stmt = $db->prepare("UPDATE retegas_options SET valore_text = :value
        WHERE id_user='"._USER_ID."' AND chiave='_USER_NOTIFICHE_TELEGRAM'");

    }else{
        //non esiste
        $stmt = $db->prepare("INSERT INTO retegas_options (id_user,chiave,valore_text)
        VALUES ("._USER_ID.",'_USER_NOTIFICHE_TELEGRAM',:value)");

    }
    $stmt->bindParam(':value', $_POST['value'], PDO::PARAM_STR);
    $stmt->execute();

    $res=array("result"=>"OK", "msg"=>"Notifiche telegram : <b>".$_POST['value']."</b>" );
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
    /* -------------------------GESTORE CONVALIDA
    */
    case "convalida_gestore":
    $stmt = $db->prepare("SELECT COUNT(*) from retegas_options WHERE id_user='"._USER_ID."' AND chiave='_USER_CONVALIDA_ORDINI_GESTORI'");
    $stmt->execute();
    if ($stmt->fetchColumn()){
        //esiste
        $stmt = $db->prepare("UPDATE retegas_options SET valore_text = :value
        WHERE id_user='"._USER_ID."' AND chiave='_USER_CONVALIDA_ORDINI_GESTORI'");

    }else{
        //non esiste
        $stmt = $db->prepare("INSERT INTO retegas_options (id_user,chiave,valore_text)
        VALUES ("._USER_ID.",'_USER_CONVALIDA_ORDINI_GESTORI',:value)");

    }
    $stmt->bindParam(':value', $_POST['value'], PDO::PARAM_STR);
    $stmt->execute();

    $res=array("result"=>"OK", "msg"=>"Convalida ordini se gestore: <b>".$_POST['value']."</b>" );
    echo json_encode($res);

    break;
    //-------------------------------------------------------------------------------------


    /*
    /* -------------------------RICHIESTA RICARICA
    */
    case "richiesta_ricarica":
    $stmt = $db->prepare("SELECT COUNT(*) from retegas_options WHERE id_user='"._USER_ID."' AND chiave='_USER_MAIL_RICHIESTA_RICARICA'");
    $stmt->execute();
    if ($stmt->fetchColumn()){
        //esiste
        $stmt = $db->prepare("UPDATE retegas_options SET valore_text = :value
        WHERE id_user='"._USER_ID."' AND chiave='_USER_MAIL_RICHIESTA_RICARICA'");

    }else{
        //non esiste
        $stmt = $db->prepare("INSERT INTO retegas_options (id_user,chiave,valore_text)
        VALUES ("._USER_ID.",'_USER_MAIL_RICHIESTA_RICARICA',:value)");

    }
    $stmt->bindParam(':value', $_POST['value'], PDO::PARAM_STR);
    $stmt->execute();

    $res=array("result"=>"OK", "msg"=>"MAIL richiesta ricarica se cassiere: <b>".$_POST['value']."</b>" );
    echo json_encode($res);

    break;
    //-------------------------------------------------------------------------------------




    /*
    /* -------------------------GESTORE CHIUSURE
    */
    case "chiusure_gestore":
    $stmt = $db->prepare("SELECT COUNT(*) from retegas_options WHERE id_user='"._USER_ID."' AND chiave='_USER_CHIUSURE_ORDINI_GESTORI'");
    $stmt->execute();
    if ($stmt->fetchColumn()){
        //esiste
        $stmt = $db->prepare("UPDATE retegas_options SET valore_text = :value
        WHERE id_user='"._USER_ID."' AND chiave='_USER_CHIUSURE_ORDINI_GESTORI'");

    }else{
        //non esiste
        $stmt = $db->prepare("INSERT INTO retegas_options (id_user,chiave,valore_text)
        VALUES ("._USER_ID.",'_USER_CHIUSURE_ORDINI_GESTORI',:value)");

    }
    $stmt->bindParam(':value', $_POST['value'], PDO::PARAM_STR);
    $stmt->execute();

    $res=array("result"=>"OK", "msg"=>"Chiusure ordini se gestore: <b>".$_POST['value']."</b>" );
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

    case "_USER_MANDATE_DATE":
    $stmt = $db->prepare("SELECT COUNT(*) from retegas_options WHERE id_user='"._USER_ID."' AND chiave='_USER_MANDATE_DATE'");
    $stmt->execute();
    if ($stmt->fetchColumn()){
        //esiste
        $stmt = $db->prepare("UPDATE retegas_options SET valore_data = :value
        WHERE id_user='"._USER_ID."' AND chiave='_USER_MANDATE_DATE'");

    }else{
        //non esiste
        $stmt = $db->prepare("INSERT INTO retegas_options (id_user,chiave,valore_data)
        VALUES ("._USER_ID.",'_USER_MANDATE_DATE',:value)");

    }
    $stmt->bindParam(':value', conv_date_to_db($_POST['mandate_date']), PDO::PARAM_STR);
    $stmt->execute();

    $msg="Salvato: <b>".CAST_TO_STRING($_POST['mandate_date'])."</b>.";

    $res=array("result"=>"OK", "msg"=>$msg );
    echo json_encode($res);

    break;
    
    case "_USER_MANDATE_ID":
    $stmt = $db->prepare("SELECT COUNT(*) from retegas_options WHERE id_user='"._USER_ID."' AND chiave='_USER_MANDATE_ID'");
    $stmt->execute();
    if ($stmt->fetchColumn()){
        //esiste
        $stmt = $db->prepare("UPDATE retegas_options SET valore_text = :value
        WHERE id_user='"._USER_ID."' AND chiave='_USER_MANDATE_ID'");

    }else{
        //non esiste
        $stmt = $db->prepare("INSERT INTO retegas_options (id_user,chiave,valore_text)
        VALUES ("._USER_ID.",'_USER_MANDATE_ID',:value)");

    }
    $stmt->bindParam(':value', CAST_TO_STRING($_POST['mandate_id']), PDO::PARAM_STR);
    $stmt->execute();

    $msg="Salvato: <b>".CAST_TO_STRING($_POST['mandate_id'])."</b>.";

    $res=array("result"=>"OK", "msg"=>$msg );
    echo json_encode($res);

    break;
    
    case "_USER_IBAN":
    $stmt = $db->prepare("SELECT COUNT(*) from retegas_options WHERE id_user='"._USER_ID."' AND chiave='_USER_IBAN'");
    $stmt->execute();
    if ($stmt->fetchColumn()){
        //esiste
        $stmt = $db->prepare("UPDATE retegas_options SET valore_text = :value
        WHERE id_user='"._USER_ID."' AND chiave='_USER_IBAN'");

    }else{
        //non esiste
        $stmt = $db->prepare("INSERT INTO retegas_options (id_user,chiave,valore_text)
        VALUES ("._USER_ID.",'_USER_IBAN',:value)");

    }
    $stmt->bindParam(':value', CAST_TO_STRING($_POST['iban']), PDO::PARAM_STR);
    $stmt->execute();

    $msg="Salvato: <b>".CAST_TO_STRING($_POST['iban'])."</b>.";

    $res=array("result"=>"OK", "msg"=>$msg );
    echo json_encode($res);

    break;
    
    
    /*
    /* -------------------------USER CSV SEPARATOR
    */
    case "csv_separator":
    $stmt = $db->prepare("SELECT COUNT(*) from retegas_options WHERE id_user='"._USER_ID."' AND chiave='_USER_CSV_SEPARATOR'");
    $stmt->execute();
    if ($stmt->fetchColumn()){
        //esiste
        $stmt = $db->prepare("UPDATE retegas_options SET valore_text = :value
        WHERE id_user='"._USER_ID."' AND chiave='_USER_CSV_SEPARATOR'");

    }else{
        //non esiste
        $stmt = $db->prepare("INSERT INTO retegas_options (id_user,chiave,valore_text)
        VALUES ("._USER_ID.",'_USER_CSV_SEPARATOR',:value)");

    }
    $stmt->bindParam(':value', CAST_TO_STRING($_POST['chara'],1), PDO::PARAM_STR);
    $stmt->execute();

    $msg="Nuovo carattere: <b>".CAST_TO_STRING($_POST['chara'],1)."</b>.";

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
    /* -------------------------SCELTA METODO INSERIMENTO
    */
    case "scelta_metodo_inserimento":
        $scelta = CAST_TO_STRING($_POST["scelta"]);
        if($scelta=="textbox"){
            $value="SI";
        }else{
            $value="NO";
        }

        $stmt = $db->prepare("SELECT COUNT(*) from retegas_options WHERE id_user='"._USER_ID."' AND chiave='_USER_INSERIMENTO_TEXTBOX'");
        $stmt->execute();
        if ($stmt->fetchColumn()){
            //esiste
            $stmt = $db->prepare("UPDATE retegas_options SET valore_text = :value
            WHERE id_user='"._USER_ID."' AND chiave='_USER_INSERIMENTO_TEXTBOX'");

        }else{
            //non esiste
            $stmt = $db->prepare("INSERT INTO retegas_options (id_user,chiave,valore_text)
            VALUES ("._USER_ID.",'_USER_INSERIMENTO_TEXTBOX',:value)");

        }
        $stmt->bindParam(':value', $value , PDO::PARAM_STR);
        $stmt->execute();


        $res=array("result"=>"OK", "msg"=>"Impostato come $scelta" );
        echo json_encode($res);

    break;
    //-------------------------------------------------------------------------------------

    /*
    /* -------------------------USER_HAS_TRIGGERS
    */
    case "abilita_triggers":
        $scelta = CAST_TO_STRING($_POST["value"]);
        if($scelta=="SI"){
            $value="SI";
        }else{
            $value="NO";
        }

        $stmt = $db->prepare("SELECT COUNT(*) from retegas_options WHERE id_user='"._USER_ID."' AND chiave='_USER_HAS_TRIGGERS'");
        $stmt->execute();
        if ($stmt->fetchColumn()){
            //esiste
            $stmt = $db->prepare("UPDATE retegas_options SET valore_text = :value
            WHERE id_user='"._USER_ID."' AND chiave='_USER_HAS_TRIGGERS'");

        }else{
            //non esiste
            $stmt = $db->prepare("INSERT INTO retegas_options (id_user,chiave,valore_text)
            VALUES ("._USER_ID.",'_USER_HAS_TRIGGERS',:value)");

        }
        $stmt->bindParam(':value', $value , PDO::PARAM_STR);
        $stmt->execute();


        $res=array("result"=>"OK", "msg"=>"Impostato come $scelta" );
        echo json_encode($res);

    break;
    //-------------------------------------------------------------------------------------
    
        /*
    /* -------------------------SCELTA ATC
    */
    case "scelta_atc":
        $scelta = CAST_TO_STRING($_POST["scelta"]);
        if($scelta=="SI"){
            $value="SI";
        }else{
            $value="NO";
        }

        $stmt = $db->prepare("SELECT COUNT(*) from retegas_options WHERE id_user='"._USER_ID."' AND chiave='_USER_ADDTOCALENDAR'");
        $stmt->execute();
        if ($stmt->fetchColumn()){
            //esiste
            $stmt = $db->prepare("UPDATE retegas_options SET valore_text = :value
            WHERE id_user='"._USER_ID."' AND chiave='_USER_ADDTOCALENDAR'");

        }else{
            //non esiste
            $stmt = $db->prepare("INSERT INTO retegas_options (id_user,chiave,valore_text)
            VALUES ("._USER_ID.",'_USER_ADDTOCALENDAR',:value)");

        }
        $stmt->bindParam(':value', $value , PDO::PARAM_STR);
        $stmt->execute();


        $res=array("result"=>"OK", "msg"=>"Impostato come $scelta" );
        echo json_encode($res);

    break;
    //-------------------------------------------------------------------------------------
    

    /*
    /* -------------------------SCELTA DECIMALE
    */
    case "scelta_decimale":
        $scelta = CAST_TO_STRING($_POST["scelta"]);
        if($scelta=="virgola"){
            $value=",";
        }else{
            $value=".";
        }

        $stmt = $db->prepare("SELECT COUNT(*) from retegas_options WHERE id_user='"._USER_ID."' AND chiave='_USER_CARATTERE_DECIMALE'");
        $stmt->execute();
        if ($stmt->fetchColumn()){
            //esiste
            $stmt = $db->prepare("UPDATE retegas_options SET valore_text = :value
            WHERE id_user='"._USER_ID."' AND chiave='_USER_CARATTERE_DECIMALE'");

        }else{
            //non esiste
            $stmt = $db->prepare("INSERT INTO retegas_options (id_user,chiave,valore_text)
            VALUES ("._USER_ID.",'_USER_CARATTERE_DECIMALE',:value)");

        }
        $stmt->bindParam(':value', $value , PDO::PARAM_STR);
        $stmt->execute();


        $res=array("result"=>"OK", "msg"=>"Impostato come $scelta" );
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

    if(_USER_ID>0){
    //esiste
    
        
        //MAIL
        
        $email = CAST_TO_STRING($_POST["email"]);
        if($email<>_USER_MAIL){
            $sql = "SELECT count(*) as conto FROM maaking_users WHERE email=:email;";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':email', $email , PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch();
            if($row["conto"]>0){
                $res=array("result"=>"KO", "msg"=>"Mail già usata da un altro utente." );
                echo json_encode($res);
                die();    
            }
        }
        $stmt = $db->prepare("UPDATE maaking_users SET fullname = :fullname,
                                                       email = :email,
                                                       tel = :tel,
                                                       tessera = :tessera
                             WHERE userid="._USER_ID." LIMIT 1;");
        $stmt->bindParam(':fullname', $_POST['fullname'], PDO::PARAM_STR);
        $stmt->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
        $stmt->bindParam(':tel', $_POST['tel'], PDO::PARAM_STR);
        $stmt->bindParam(':tessera', clean($_POST['tessera']), PDO::PARAM_STR);
        $stmt->execute();

        $res=array("result"=>"OK", "msg"=>"Nuove anagrafiche salvate" );
        }else{
            SEmail("Mauro","famiglia.morello@gmail.com","retedes.it","info@retedes.it","ANAGRAFICA","Trovato user id=0 con ".$_POST['email']);
            $res=array("result"=>"KO", "msg"=>"Errore generico. Provare a ricaricare la pagina." );
        }


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
    /* -------------------------EDIT AMICO email
    */
    case "edit_amico_email":


    //esiste
    $stmt = $db->prepare("UPDATE retegas_amici SET email = :email
                         WHERE id_referente='"._USER_ID."' AND id_amici=:id_amici LIMIT 1;");
    $stmt->bindParam(':email', $_POST['value'], PDO::PARAM_STR);
    $stmt->bindParam(':id_amici', $_POST['pk'], PDO::PARAM_INT);
    $stmt->execute();
    if($stmt->rowCount()==1){
        $res=array("result"=>"OK", "msg"=>"" );
    }else{
        $res=array("result"=>"KO", "msg"=>"Errore." );
    }

    echo json_encode($res);

    break;

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


        //LISTA MAIL AMICI
        $stmt = $db->prepare("SELECT * FROM  retegas_amici WHERE id_referente='"._USER_ID."' AND status=1 AND email IS NOT NULL ");
        $stmt->execute();
        $rows = $stmt->fetchAll();
        foreach($rows as $row){
            $la .= $row["nome"].' '.htmlentities('<').$row["email"].htmlentities('>').'; ';
        }

        $res=array("result"=>"OK", "msg"=>"Status modificato", "mail_amici"=>$la );


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

    if(CAST_TO_STRING($_POST["value"]=="")){
        echo json_encode(array("result"=>"KO", "msg"=>"Non puoi lasciare questo campo vuoto"));
        die();
    }

    //esiste
    $stmt = $db->prepare("INSERT INTO retegas_amici (id_referente,nome, status) VALUES ('"._USER_ID."',:nome ,1) ;");

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



    if(CAST_TO_STRING($_POST["euro"])==""){
        echo json_encode(array("result"=>"KO", "msg"=>"Non puoi lasciare questo campo vuoto"));
        die();
    }

    $importo = str_replace(",", ".", $_POST["euro"]);
    $importo = CAST_TO_FLOAT($importo,0);

    if(CAST_TO_STRING($_POST["note"])==""){
        echo json_encode(array("result"=>"KO", "msg"=>"Serve un commento"));
        die();
    }

    if(round($importo,3)==0){
        echo json_encode(array("result"=>"KO", "msg"=>"Non puoi non caricare nulla :)"));
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

    $stmt->bindParam(':note', CAST_TO_STRING($_POST['note']), PDO::PARAM_STR);
    $stmt->bindParam(':documento', CAST_TO_STRING($_POST['documento']), PDO::PARAM_STR);
    $stmt->bindParam(':euro', $importo, PDO::PARAM_STR);
    $stmt->execute();

    if($stmt->rowCount()==1){

        $last_id = $db->lastInsertId();

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
                        $stmt->bindParam(':importo', $importo, PDO::PARAM_STR);
                        $stmt->execute();

                        $carico_automatico = "Il tuo gas ha attivata l'opzione di CARICO AUTOMATICO, significa che l'importo è già stato accreditato sul suo conto.";
        }else{
                        $carico_automatico = "Il tuo gas NON ha attivata l'opzione di CARICO AUTOMATICO, significa che c'è bisogno dell'intervento di un cassiere per caricare il credito sul suo conto.";
        }


        //MAIL AI CASSIERI
        $query_users="SELECT
                maaking_users.fullname,
                maaking_users.email,
                maaking_users.id_gas,
                maaking_users.userid,
                maaking_users.user_permission
                FROM
                maaking_users
                WHERE
                maaking_users.isactive=1 AND
                maaking_users.id_gas = '"._USER_ID_GAS."'
                ";
        $stmt = $db->prepare($query_users);
        $stmt->execute();
        $rows_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($rows_users as $row_u){
            if($row_u["user_permission"] & perm::puo_gestire_la_cassa){

                //SE VUOLE LA MAIL DELLA RICHIESTA
                    $sqlO="SELECT O.valore_text FROM retegas_options O inner join maaking_users U on U.userid=O.id_user WHERE U.email='".$row_u["email"]."' AND  O.chiave='_USER_MAIL_RICHIESTA_RICARICA' LIMIT 1";
                    $stmt = $db->prepare($sqlO);
                    $stmt->execute();
                    $rowO = $stmt->fetch();

                    if(CAST_TO_STRING($rowO[0])<>"NO"){
                        $lista_destinatari .= $row_u["fullname"]."<br>";
                        $r[]=array( 'email' => $row_u["email"],
                            'name' => $row_u["fullname"],
                            'type' => 'bcc');
                    }
                    //SE VUOLE LA MAIL DELLA RICHIESTA
            }
        }

        $fullnameFROM = "ReteDES.it";
            $mailFROM = "info@retedes.it";
        $oggetto = "[reteDES] "._USER_FULLNAME." chiede di ricaricare "._NF($importo)." eu. sul suo conto";
        $profile = new Template('../../email_rd4/ricarica_credito.html');
        $profile->set("fullname_utente", _USER_FULLNAME );
        $profile->set("credito", _NF($importo));
        $profile->set("note_ricarica", CAST_TO_STRING($_POST["note"]) );
        $profile->set("documento", CAST_TO_STRING($_POST['documento']) );
        $profile->set("carico_automatico", $carico_automatico );
        $profile->set("lista_destinatari", $lista_destinatari );
        $messaggio = $profile->output();
        SEmailMulti($r,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"V4RicaricaCredito");
        unset ($profile);
        //MAIL AI CASSIERI

        $res=array("result"=>"OK", "msg"=>"Prenotazione aggiunta. Una mail è stata inviata ai cassieri del tuo GAS:<br> $lista_destinatari", "html"=>"<div class=\"well well-sm margin-top-5 pren_box\">Pochi istanti fa,  <span class=\"badge bg-color-greenLight font-md\">&euro; "._NF($importo)."</span> ".$_POST["documento"]." - ".$_POST["note"]."; <button class=\"btn btn-danger btn-xs elimina_mov pull-right\" rel=\"".$last_id."\"><i class=\"fa fa-trash-o\"></i> <span class=\"hidden-xs\">Elimina</span></button><div class=\"clearfix\"></div></div>" );

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
}
//NAME


if(!empty($_POST["name"])){
    switch ($_POST["name"]) {
    case "do_user_tessera":
        $userid = CAST_TO_INT($_POST["pk"],0);
        $tessera = CAST_TO_STRING($_POST["value"]);
        $stmt = $db->prepare("UPDATE maaking_users SET tessera=:tessera
                              WHERE userid=:userid LIMIT 1;");

        $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
        $stmt->bindParam(':tessera', $tessera, PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()==1){
            $res=array("result"=>"OK", "msg"=>"OK" );
        }else{
            $res=array("result"=>"KO", "msg"=>"Errore." );
        }
        echo json_encode($res);
        die();

    break;
    case "do_user_tel":
        $userid = CAST_TO_INT($_POST["pk"],0);
        $tel = CAST_TO_STRING($_POST["value"]);
        $stmt = $db->prepare("UPDATE maaking_users SET tel=:tel
                              WHERE userid=:userid LIMIT 1;");

        $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
        $stmt->bindParam(':tel', $tel, PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()==1){
            $res=array("result"=>"OK", "msg"=>"OK" );
        }else{
            $res=array("result"=>"KO", "msg"=>"Errore." );
        }
        echo json_encode($res);
        die();

    break;    
    case "do_user_mail":
        $userid = CAST_TO_INT($_POST["pk"],0);
        $email = CAST_TO_STRING($_POST["value"]);
        $stmt = $db->prepare("UPDATE maaking_users SET email=:email
                              WHERE userid=:userid LIMIT 1;");

        $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()==1){
            $res=array("result"=>"OK", "msg"=>"OK" );
        }else{
            $res=array("result"=>"KO", "msg"=>"Errore." );
        }
        echo json_encode($res);
        die();

     break;
     
     case "do_user_fullname":
        $userid = CAST_TO_INT($_POST["pk"],0);
        $fullname = CAST_TO_STRING($_POST["value"]);
        $stmt = $db->prepare("UPDATE maaking_users SET fullname=:fullname
                              WHERE userid=:userid LIMIT 1;");

        $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
        $stmt->bindParam(':fullname', $fullname, PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()==1){
            $res=array("result"=>"OK", "msg"=>"OK" );
        }else{
            $res=array("result"=>"KO", "msg"=>"Errore." );
        }
        echo json_encode($res);
        die();

     break;

     case "do_user_custom_1":
        $userid = CAST_TO_INT($_POST["pk"],0);
        $custom_1 = CAST_TO_STRING($_POST["value"]);
        do_user_custom_1($userid,$custom_1);
     break;
     case "do_user_custom_2":
        $userid = CAST_TO_INT($_POST["pk"],0);
        $custom_2 = CAST_TO_STRING($_POST["value"]);
        do_user_custom_2($userid,$custom_2);
     break;
     case "do_user_custom_3":
        $userid = CAST_TO_INT($_POST["pk"],0);
        $custom_3 = CAST_TO_STRING($_POST["value"]);
        do_user_custom_3($userid,$custom_3);
     break;

     default:
        $res=array("result"=>"KO", "msg"=>"Comando non riconosciuto" );
        echo json_encode($res);
     break;
    }
}