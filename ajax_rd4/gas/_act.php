<?php
require_once("inc/init.php");
$converter = new Encryption;

if(!empty($_POST["act"])){
    switch ($_POST["act"]) {

    case "gas_cond_ord_est":
        if(_USER_PERMISSIONS & perm::puo_creare_gas){
            $id_gas = _USER_ID_GAS;
            $value = CAST_TO_INT($_POST["value"],0,1);
            if($value==0){
                $value="NO";
            }else{
                $value="SI";
            }
            //Aggiorna l'opzione dal GAS.
            $stmt = $db->prepare("DELETE FROM retegas_options WHERE id_gas=:id_gas AND chiave='_GAS_PUO_COND_ORD_EST' LIMIT 1;");
            $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
            $stmt->execute();
            $r = "Il tuo GAS è isolato.";
            if($value=="SI"){
                $stmt = $db->prepare("INSERT INTO retegas_options (id_gas,chiave,valore_text) VALUES (:id_gas,'_GAS_PUO_COND_ORD_EST','SI');");
                $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
                $stmt->execute();
                $r = "Il tuo GAS condividere ordini con altri GAS";
            }
            $res=array("result"=>"OK", "msg"=>$r );
        }else{
           $r = "Non hai i permessi necessari";
           $res= array("result"=>"KO", "msg"=>$r);
        }
        echo json_encode($res);
        die();
    break;

    case "gas_part_ord_est":
        if(_USER_PERMISSIONS & perm::puo_creare_gas){
            $id_gas = _USER_ID_GAS;
            $value = CAST_TO_INT($_POST["value"],0,1);
            if($value==0){
                $value="NO";
            }else{
                $value="SI";
            }
            //Aggiorna l'opzione dal GAS.
            $stmt = $db->prepare("DELETE FROM retegas_options WHERE id_gas=:id_gas AND chiave='_GAS_PUO_PART_ORD_EST' LIMIT 1;");
            $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
            $stmt->execute();
            $r = "Il tuo GAS è isolato.";
            if($value=="SI"){
                $stmt = $db->prepare("INSERT INTO retegas_options (id_gas,chiave,valore_text) VALUES (:id_gas,'_GAS_PUO_PART_ORD_EST','SI');");
                $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
                $stmt->execute();
                $r = "Il tuo GAS può partecipare ad ordini aperti da altri GAS";
            }
            $res=array("result"=>"OK", "msg"=>$r );
        }else{
           $r = "Non hai i permessi necessari";
           $res= array("result"=>"KO", "msg"=>$r);
        }
        echo json_encode($res);
        die();
    break;

    case "gas_option_visione_condivisa":

        if(_USER_PERMISSIONS & perm::puo_creare_gas){
            $id_gas = _USER_ID_GAS;
            $value = CAST_TO_INT($_POST["value"],0,1);
            if($value==0){
                $value="NO";
            }else{
                $value="SI";
            }
            //Aggiorna l'opzione dal GAS.
            $stmt = $db->prepare("DELETE FROM retegas_options WHERE id_gas=:id_gas AND chiave='_GAS_VISIONE_CONDIVISA' LIMIT 1;");
            $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
            $stmt->execute();
            $r = "Visione condivisa DISABILITATA";
            if($value=="SI"){
                $stmt = $db->prepare("INSERT INTO retegas_options (id_gas,chiave,valore_text) VALUES (:id_gas,'_GAS_VISIONE_CONDIVISA','SI');");
                $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
                $stmt->execute();
                $r = "Visione condivisa ABILITATA;";
            }
            $res=array("result"=>"OK", "msg"=>$r );
        }else{
           $r = "Non hai i permessi necessari";
           $res= array("result"=>"KO", "msg"=>$r);
        }
        echo json_encode($res);
        die();

    break;

    case "gas_option_cassa":

        if(_USER_PERMISSIONS & perm::puo_creare_gas){
            $id_gas = _USER_ID_GAS;
            $value = CAST_TO_INT($_POST["value"],0,1);
            if($value==0){
                $value="NO";
            }else{
                $value="SI";
            }
            //Aggiorna l'opzione dal GAS.
            $stmt = $db->prepare("DELETE FROM retegas_options WHERE id_gas=:id_gas AND chiave='_GAS_USA_CASSA' LIMIT 1;");
            $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
            $stmt->execute();
            $r = "Il gas NON usa la cassa;";
            if($value=="SI"){
                $stmt = $db->prepare("INSERT INTO retegas_options (id_gas,chiave,valore_text) VALUES (:id_gas,'_GAS_USA_CASSA','SI');");
                $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
                $stmt->execute();
                $r = "Il tuo GAS usa la cassa;";
            }else{
                //Toglie la cassa a tutti i gasisti
                $stmt = $db->prepare("DELETE O.* FROM retegas_options O inner join maaking_users U on U.userid=O.id_user where chiave='_USER_USA_CASSA' AND U.id_gas=:id_gas");
                $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
                $stmt->execute();
            }
            $res=array("result"=>"OK", "msg"=>$r );


        }else{
           $r = "Non hai i permessi necessari";
           $res= array("result"=>"KO", "msg"=>$r);
        }

        echo json_encode($res);
        die();

    break;

    case "register_new":

    $fullname = clean($_POST["fullname"]);
    $username = clean($_POST["username"]);
    $password = clean($_POST["password"]);
    $password2 = clean($_POST["password2"]);
    $tel = clean($_POST["tel"]);
    $email = clean($_POST["email"]);
    $permessi = 0;
    $puo_partecipare = CAST_TO_INT($_POST["puo_partecipare"],0,1);
    $puo_gestire = CAST_TO_INT($_POST["puo_gestire"],0,1);

    //vuoti
    if($fullname=="" ||
       $username=="" ||
       $password=="" ||
       $password2=="" ||
       $tel=="" ||
       $email==""){
        $res=array("result"=>"KO", "msg"=>"Non puoi lasciare i campi vuoti" );
        echo json_encode($res);
        die();
        break;
    }
    //password
    if($password<>$password2){
        $res=array("result"=>"KO", "msg"=>"Le due password non coincidono" );
        echo json_encode($res);
        die();
        break;
    }
    //username esistente
    $stmt = $db->prepare("SELECT * from maaking_users WHERE email=:email OR username=:username;");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    if($stmt->rowCount()>0){
        $res=array("result"=>"KO", "msg"=>"Username o Email già esistenti" );
        echo json_encode($res);
        die();
        break;

    }

    $md5password=md5($password);
    $id_gas = _USER_ID_GAS;
    $code = rand(10000000,99999999);
    $codeLINK = 'http://retegas.altervista.org/gas4/ajax_rd4/?do=c&c='.$code;

    $stmt = $db->prepare("INSERT INTO maaking_users
                                        (username,
                                         password,
                                         email,
                                         fullname,
                                         regdate,
                                         isactive,
                                         code,
                                         id_gas,
                                         consenso,
                                         tel,
                                         user_permission,
                                         user_site_option)
                                         VALUES
                                        (:username,
                                         :md5password,
                                         :email,
                                         :fullname,
                                         NOW(),
                                         '1',
                                         :code,
                                         :id_gas,
                                         '1',
                                         :tel,
                                         :permessi,
                                         '31');");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':md5password', $md5password, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':fullname', $fullname, PDO::PARAM_STR);
    $stmt->bindParam(':code', $code, PDO::PARAM_INT);
    $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
    $stmt->bindParam(':tel', $tel, PDO::PARAM_STR);
    $stmt->bindParam(':permessi', $permessi, PDO::PARAM_STR);

    $stmt->execute();
    if($stmt->rowCount()==1){

        //mail
        $mailFROM = _USER_MAIL;
        $fullnameFROM = _USER_FULLNAME;

        $mailTO = $email;
        $fullnameTO = $fullname;

        $oggetto = "[reteDES.it] nuovo account creato da ".$fullnameFROM;
        $profile = new Template('../../email_rd4/nuovo_utente_da_gas.html');

        $profile->set("fullnameFROM", $fullnameFROM );
        $profile->set("gasNOME", _USER_GAS_NOME );
        $profile->set("newUSERNAME", $username );
        $profile->set("newPASSWORD", $password );
        $profile->set("newFULLNAME", $fullname );
        $profile->set("newTEL", $tel );
        $profile->set("newCODE", $codeLINK );

        $messaggio = $profile->output();

        if(SEmail($fullnameTO,$mailTO,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"CreatoAccount")){
            $res=array("result"=>"OK", "msg"=>"$fullname inserito in reteDES.<br>Mail inviata." );
            echo json_encode($res);
            die();
            break;
        }else{
            $res=array("result"=>"KO", "msg"=>"Utente inserito, ma mail non inviata." );
            echo json_encode($res);
            die();
            break;
        }




    }


    $res=array("result"=>"KO", "msg"=>"username: ".$email );
    echo json_encode($res);

    break;

    case "attiva_utente":
        $userid = $_POST["value"];
        $userid = $converter->decode($userid);

        if(_USER_PERMISSIONS & perm::puo_gestire_utenti){

            $stmt = $db->prepare("UPDATE maaking_users SET isactive=1 WHERE userid = :userid and id_gas='"._USER_ID_GAS."'");
            $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
            $stmt->execute();
            if($stmt->rowCount()==1){
                //TODO: Toglie eventuale sospensione

                $stmt = $db->prepare("SELECT * FROM  maaking_users WHERE userid = :userid");
                $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                $mailTO = $row["email"];
                $fullnameTO = $row["fullname"];

                $mailFROM = _USER_MAIL;
                $fullnameFROM = _USER_FULLNAME;

                $oggetto = "[reteDES.it] Avvenuta attivazione.";
                $profile = new Template('../../email_rd4/attiva_utente.html');

                $profile->set("fullnameFROM", $fullnameFROM );
                $profile->set("gasNOME", _USER_GAS_NOME );

                $messaggio = $profile->output();

                if(SEmail($fullnameTO,$mailTO,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"AttivazioneUtente")){
                    $res=array("result"=>"OK", "msg"=>"$fullnameTO attivato." );
                }else{
                    $res=array("result"=>"KO", "msg"=>"Operazione non riuscita" );
                }
            }else{
                $res=array("result"=>"KO", "msg"=>"Utente di un altro gas" );
            }
        }else{
            $res=array("result"=>"KO", "msg"=>"Non hai i permessi necessari" );
        }
        echo json_encode($res);

    break;

    case "sospendi_utente":
        $userid = $_POST["value"];
        $userid = $converter->decode($userid);

        if(_USER_PERMISSIONS & perm::puo_gestire_utenti){

            $stmt = $db->prepare("UPDATE maaking_users SET isactive=2 WHERE userid = :userid and id_gas='"._USER_ID_GAS."'");
            $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
            $stmt->execute();
            if($stmt->rowCount()==1){
                //TODO: Toglie eventuale sospensione
                $stmt = $db->prepare("DELETE retegas_options WHERE id_user = :userid and chiave='_NOTE_SUSPENDED' LIMIT 1;");
                $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
                $stmt->execute();


                //METTE NUOVA SOSPENSIONE
                $stmt = $db->prepare("INSERT retegas_options (id_user,chiave,valore_text) VALUES (:userid,'_NOTE_SUSPENDED','Utenza sospesa da "._USER_FULLNAME."');");
                $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
                $stmt->execute();


                $res=array("result"=>"OK", "msg"=>"Utente sospeso." );
            }else{
                $res=array("result"=>"KO", "msg"=>"Utente non del tuo gas." );
            }
        }else{
            $res=array("result"=>"KO", "msg"=>"Permessi non abilitati." );
        }

        echo json_encode($res);

    break;

    case "messaggia_utenti":
        $utenti = $_POST["values"];
        $messaggio = clean($_POST["messaggio"]);

        foreach ($utenti as $id_utente){
            $stmt = $db->prepare("SELECT fullname, email FROM maaking_users WHERE userid=:userid");
            $stmt->bindParam(':userid', $id_utente, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch();
            $n++;
            $r[]=array( 'email' => $row["email"],
                        'name' => $row["fullname"],
                        'type' => 'bcc');

        }

        //MAIL------------------------------------------------
            $fullnameFROM = _USER_FULLNAME;
            $mailFROM = _USER_MAIL;


            //manda mail di carico credito
            $oggetto = "[reteDES.it] Messaggio da "._USER_FULLNAME;
            $profile = new Template('../../email_rd4/basic_2.html');

            $profile->set("fullnameFROM", _USER_FULLNAME );
            $profile->set("messaggio", $messaggio );


            $messaggio = $profile->output();

            SEmailMulti($r,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"MessaggioMultiplo");
            //MAIL------------------------------------------------


        $res=array("result"=>"OK", "msg"=>"Mail inviate a $n utenti" );
        echo json_encode($res);

    break;

    case "elimina_utente":
        $userid = $_POST["value"];
        $userid = $converter->decode($userid);

        if(_USER_PERMISSIONS & perm::puo_gestire_utenti){

            $stmt = $db->prepare("UPDATE maaking_users SET isactive=3 WHERE userid = :userid and id_gas='"._USER_ID_GAS."'");
            $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
            $stmt->execute();
            if($stmt->rowCount()==1){
                $res=array("result"=>"OK", "msg"=>"Utente eliminato." );
            }else{
                $res=array("result"=>"KO", "msg"=>"Utente non del tuo gas." );
            }
        }else{
            $res=array("result"=>"KO", "msg"=>"Permessi non abilitati." );
        }

        echo json_encode($res);

    break;


    case "messaggia":
        $sHTML =$_POST["messaggio"];
        $sHTML = strip_tags($sHTML);


        $stmt = $db->prepare("SELECT * FROM  maaking_users WHERE userid = :userid");
        $stmt->bindParam(':userid', $_POST['id'], PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $mailTO = $row["email"];
        $fullnameTO = $row["fullname"];

        $mailFROM = _USER_MAIL;
        $fullnameFROM = _USER_FULLNAME;

        $oggetto = "[reteDES.it] Messaggio da ".$fullnameFROM;

        $profile = new Template('../../email_rd4/basic_2.html');
        $profile->set("fullnameFROM", $fullnameFROM );
        $profile->set("messaggio", $sHTML );
        $messaggio = $profile->output();


        //if(SEmailMulti($ArrayTO,$fullnameFROM,$mailFROM,$oggetto,$messaggio)){
        if(SEmail($fullnameTO,$mailTO,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"MessaggioPrivato")){
            $res=array("result"=>"OK", "msg"=>"Messaggio a $fullnameTO inviato." );
        }else{
            $res=array("result"=>"KO", "msg"=>"Messaggio NON recapitato" );
        }

        //$res=array("result"=>"KO", "msg"=>$messaggio );
        echo json_encode($res);


    break;
     default:
        $res=array("result"=>"KO", "msg"=>"Comando non riconosciuto" );
        echo json_encode($res);
     break;
    }
};

if(!empty($_POST["name"])){
    switch ($_POST["name"]) {



     default:
        $res=array("result"=>"KO", "msg"=>"Comando non riconosciuto" );
        echo json_encode($res);
     break;
    }
}

