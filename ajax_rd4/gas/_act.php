<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.gas.php");
require_once("../../lib_rd4/class.rd4.user.php");
require_once("../../lib_rd4/htmlpurifier-4.7.0/library/HTMLPurifier.auto.php");
$converter = new Encryption;

if(!empty($_POST["act"])){
    switch ($_POST["act"]) {

    case "unban_user":
        if(_USER_PERMISSIONS & perm::puo_gestire_utenti){

            $userid=CAST_TO_INT($_POST["userid"],0);
            if($userid>0){
                $U = new user($userid);
                $res = json_decode(sparkpostAPIget("suppression-list/".$U->email),TRUE);
                if(CAST_TO_STRING($res["results"][0]["source"]=="")){
                    $sql="DELETE FROM `retegas_bounced` WHERE `raw_rcpt_to`=:email LIMIT 1;";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':email', $U->email, PDO::PARAM_STR);
                    $stmt->execute();
                }else{
                    $res=array("result"=>"KO", "msg"=>"Purtroppo questa mail non è riattivabile :( "); 
                    echo json_encode($res);
                    die();    
                }
                
                
            }
            $id=CAST_TO_INT($_POST["id"],0);
            if($id>0){
                
                $sql="DELETE FROM `retegas_bounced` WHERE id=:id LIMIT 1;";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            
            }
            if($stmt->rowCount()==1){
                $res=array("result"=>"OK", "msg"=>"Riattivato" );
            }else{
                $res=array("result"=>"KO", "msg"=>"Non trovato ");    
            }
        }else{
            $res=array("result"=>"KO", "msg"=>"Non puoi" );
        }
        echo json_encode($res);
        die();
    break;    
        
    case "sospensione_utenti":
        if(_USER_PERMISSIONS & perm::puo_gestire_utenti){

            $id_gas=_USER_ID_GAS;
            $frase_sospensione = clean(CAST_TO_STRING($_POST["frase_sospensione"]));
            $giorni_sospensione = CAST_TO_INT($_POST["giorni_sospensione"],0);


            $sql="DELETE FROM retegas_options WHERE id_gas=:id_gas AND chiave='_GAS_SOSPENSIONE_UTENTI' LIMIT 2;";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
            $stmt->execute();

            $sql="INSERT INTO retegas_options (id_gas,chiave,valore_int, valore_text) VALUES (:id_gas,'_GAS_SOSPENSIONE_UTENTI',:giorni_sospensione,:frase_sospensione);";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
            $stmt->bindParam(':giorni_sospensione', $giorni_sospensione, PDO::PARAM_INT);
            $stmt->bindParam(':frase_sospensione', $frase_sospensione, PDO::PARAM_STR);

            $stmt->execute();


            $res=array("result"=>"OK", "msg"=>"ok" );
        }else{
            $res=array("result"=>"KO", "msg"=>"Non puoi" );
        }
        echo json_encode($res);
        die();
    break;

    case "do_custom_1":
        if(_USER_PERMISSIONS & perm::puo_gestire_utenti){
            $id_gas=_USER_ID_GAS;
            $nome = CAST_TO_STRING($_POST["custom_1_nome"]);
            $tipo = CAST_TO_INT($_POST["custom_1_tipo"],0,3);
            $privato = CAST_TO_INT($_POST["custom_1_privato"],0,2);
            $proprieta = CAST_TO_INT($_POST["custom_1_proprieta"],0,2);

            if($proprieta==0){
                $proprieta='';
            }

            $sql="DELETE FROM retegas_options WHERE id_gas=:id_gas AND chiave='_GAS_CUSTOM_1' LIMIT 3;";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
            $stmt->execute();

            $sql="INSERT INTO retegas_options (id_gas,chiave,valore_int, valore_text, valore_real, note_1) VALUES (:id_gas,'_GAS_CUSTOM_1',:tipo,:nome,:privato,:proprieta);";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
            $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
            $stmt->bindParam(':tipo', $tipo, PDO::PARAM_INT);
            $stmt->bindParam(':privato', $privato, PDO::PARAM_STR);
            $stmt->bindParam(':proprieta', $proprieta, PDO::PARAM_STR);
            $stmt->execute();


            $res=array("result"=>"OK", "msg"=>"ok" );
        }else{
            $res=array("result"=>"KO", "msg"=>"Non puoi" );
        }
        echo json_encode($res);
        die();
    break;
    case "do_custom_2":
        if(_USER_PERMISSIONS & perm::puo_gestire_utenti){
            $id_gas=_USER_ID_GAS;
            $nome = CAST_TO_STRING($_POST["custom_2_nome"]);
            $tipo = CAST_TO_INT($_POST["custom_2_tipo"],0,3);
            $privato = CAST_TO_INT($_POST["custom_2_privato"],0,2);
            $proprieta = CAST_TO_INT($_POST["custom_2_proprieta"],0,2);

            if($proprieta==0){
                $proprieta='';
            }

            $sql="DELETE FROM retegas_options WHERE id_gas=:id_gas AND chiave='_GAS_CUSTOM_2' LIMIT 3;";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
            $stmt->execute();

            $sql="INSERT INTO retegas_options (id_gas,chiave,valore_int, valore_text, valore_real, note_1) VALUES (:id_gas,'_GAS_CUSTOM_2',:tipo,:nome,:privato,:proprieta);";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
            $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
            $stmt->bindParam(':tipo', $tipo, PDO::PARAM_INT);
            $stmt->bindParam(':privato', $privato, PDO::PARAM_STR);
            $stmt->bindParam(':proprieta', $proprieta, PDO::PARAM_STR);
            $stmt->execute();


            $res=array("result"=>"OK", "msg"=>"ok" );
        }else{
            $res=array("result"=>"KO", "msg"=>"Non puoi" );
        }
        echo json_encode($res);
        die();
    break;
    case "do_custom_3":
        if(_USER_PERMISSIONS & perm::puo_gestire_utenti){
            $id_gas=_USER_ID_GAS;
            $nome = CAST_TO_STRING($_POST["custom_3_nome"]);
            $tipo = CAST_TO_INT($_POST["custom_3_tipo"],0,3);
            $privato = CAST_TO_INT($_POST["custom_3_privato"],0,2);
            $proprieta = CAST_TO_INT($_POST["custom_3_proprieta"],0,2);

            if($proprieta==0){
                $proprieta='';
            }

            $sql="DELETE FROM retegas_options WHERE id_gas=:id_gas AND chiave='_GAS_CUSTOM_3' LIMIT 3;";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
            $stmt->execute();

            $sql="INSERT INTO retegas_options (id_gas,chiave,valore_int, valore_text, valore_real, note_1) VALUES (:id_gas,'_GAS_CUSTOM_3',:tipo,:nome,:privato,:proprieta);";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
            $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
            $stmt->bindParam(':tipo', $tipo, PDO::PARAM_INT);
            $stmt->bindParam(':privato', $privato, PDO::PARAM_STR);
            $stmt->bindParam(':proprieta', $proprieta, PDO::PARAM_STR);
            $stmt->execute();


            $res=array("result"=>"OK", "msg"=>"ok" );
        }else{
            $res=array("result"=>"KO", "msg"=>"Non puoi" );
        }
        echo json_encode($res);
        die();
    break;

    case "fullname_id":
        $id_gas = _USER_ID_GAS;
        $value =CAST_TO_INT($_POST["value"],0);
        if(_USER_PERMISSIONS & perm::puo_creare_gas){
            $G=new gas($id_gas);
            $act_perm = $G->default_permission;
            //Aggiorna l'opzione dal GAS.
            $stmt = $db->prepare("DELETE FROM retegas_options WHERE id_gas=:id_gas AND chiave='_GAS_REPORT_SHOW_ID' LIMIT 1;");
            $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
            $stmt->execute();

            if($value>0){
                $stmt = $db->prepare("INSERT INTO retegas_options (id_gas,chiave,valore_int) VALUES (:id_gas,'_GAS_REPORT_SHOW_ID',:value);");
                $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
                $stmt->bindParam(':value', $value, PDO::PARAM_INT);
                $stmt->execute();
            }
            $res=array("result"=>"OK", "msg"=>"ok" );
        }else{
            $res=array("result"=>"KO", "msg"=>"Non puoi" );
        }
        echo json_encode($res);
        die();
    break;

    case "report_show_tel":
        $id_gas = _USER_ID_GAS;
        $value =CAST_TO_INT($_POST["value"],0);
        if(_USER_PERMISSIONS & perm::puo_creare_gas){
            $G=new gas($id_gas);
            $act_perm = $G->default_permission;
            //Aggiorna l'opzione dal GAS.
            $stmt = $db->prepare("DELETE FROM retegas_options WHERE id_gas=:id_gas AND chiave='_GAS_REPORT_SHOW_TEL' LIMIT 1;");
            $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
            $stmt->execute();

            if($value>0){
                $stmt = $db->prepare("INSERT INTO retegas_options (id_gas,chiave,valore_int) VALUES (:id_gas,'_GAS_REPORT_SHOW_TEL',:value);");
                $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
                $stmt->bindParam(':value', $value, PDO::PARAM_INT);
                $stmt->execute();
            }
            $res=array("result"=>"OK", "msg"=>"ok" );
        }else{
            $res=array("result"=>"KO", "msg"=>"Non puoi" );
        }
        echo json_encode($res);
        die();
    break;

    case "report_show_cassa":
        $id_gas = _USER_ID_GAS;
        $value =CAST_TO_INT($_POST["value"],0);
        if(_USER_PERMISSIONS & perm::puo_creare_gas){
            $G=new gas($id_gas);
            $act_perm = $G->default_permission;
            //Aggiorna l'opzione dal GAS.
            $stmt = $db->prepare("DELETE FROM retegas_options WHERE id_gas=:id_gas AND chiave='_GAS_REPORT_SHOW_CASSA' LIMIT 1;");
            $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
            $stmt->execute();

            if($value>0){
                $stmt = $db->prepare("INSERT INTO retegas_options (id_gas,chiave,valore_int) VALUES (:id_gas,'_GAS_REPORT_SHOW_CASSA',:value);");
                $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
                $stmt->bindParam(':value', $value, PDO::PARAM_INT);
                $stmt->execute();
            }
            $res=array("result"=>"OK", "msg"=>"ok" );
        }else{
            $res=array("result"=>"KO", "msg"=>"Non puoi" );
        }
        echo json_encode($res);
        die();
    break;

    case "report_show_tessera":
        $id_gas = _USER_ID_GAS;
        $value =CAST_TO_INT($_POST["value"],0);
        if(_USER_PERMISSIONS & perm::puo_creare_gas){
            $G=new gas($id_gas);
            $act_perm = $G->default_permission;
            //Aggiorna l'opzione dal GAS.
            $stmt = $db->prepare("DELETE FROM retegas_options WHERE id_gas=:id_gas AND chiave='_GAS_REPORT_SHOW_TESSERA' LIMIT 1;");
            $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
            $stmt->execute();

            if($value>0){
                $stmt = $db->prepare("INSERT INTO retegas_options (id_gas,chiave,valore_int) VALUES (:id_gas,'_GAS_REPORT_SHOW_TESSERA',:value);");
                $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
                $stmt->bindParam(':value', $value, PDO::PARAM_INT);
                $stmt->execute();
            }
            $res=array("result"=>"OK", "msg"=>"ok" );
        }else{
            $res=array("result"=>"KO", "msg"=>"Non puoi" );
        }
        echo json_encode($res);
        die();
    break;

    case "report_show_indirizzo":
        $id_gas = _USER_ID_GAS;
        $value =CAST_TO_INT($_POST["value"],0);
        if(_USER_PERMISSIONS & perm::puo_creare_gas){
            $G=new gas($id_gas);
            $act_perm = $G->default_permission;
            //Aggiorna l'opzione dal GAS.
            $stmt = $db->prepare("DELETE FROM retegas_options WHERE id_gas=:id_gas AND chiave='_GAS_REPORT_SHOW_INDIRIZZO' LIMIT 1;");
            $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
            $stmt->execute();

            if($value>0){
                $stmt = $db->prepare("INSERT INTO retegas_options (id_gas,chiave,valore_int) VALUES (:id_gas,'_GAS_REPORT_SHOW_INDIRIZZO',:value);");
                $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
                $stmt->bindParam(':value', $value, PDO::PARAM_INT);
                $stmt->execute();
            }
            $res=array("result"=>"OK", "msg"=>"ok" );
        }else{
            $res=array("result"=>"KO", "msg"=>"Non puoi" );
        }
        echo json_encode($res);
        die();
    break;

    case "default_puo_creare_ditte":
        $id_gas = _USER_ID_GAS;
        $v =CAST_TO_INT($_POST["v"]);
        if(_USER_PERMISSIONS & perm::puo_gestire_utenti){
            $G=new gas($id_gas);
            $act_perm = $G->default_permission;
            if($v>0){
                $new_perm = $act_perm |  perm::puo_creare_ditte;
                $new_perm = $new_perm |  perm::puo_creare_listini;

                $msg = " si.";
            }else{
                $new_perm = $act_perm &  (~perm::puo_creare_ditte);
                $new_perm = $new_perm &  (~perm::puo_creare_listini);
                $msg = " no.";
            }
            $G->set_default_permission($new_perm);
            $res=array("result"=>"OK", "msg"=>"Gestire ditte e listini: ".$msg );
        }else{
            $res=array("result"=>"KO", "msg"=>"Non puoi" );
        }
        echo json_encode($res);
        die();
    break;
    case "default_puo_avere_amici":
        $id_gas = _USER_ID_GAS;
        $v =CAST_TO_INT($_POST["v"]);
        if(_USER_PERMISSIONS & perm::puo_gestire_utenti){
            $G=new gas($id_gas);
            $act_perm = $G->default_permission;
            if($v>0){
                $new_perm = $act_perm |  perm::puo_avere_amici;
                $msg = " si.";
            }else{
                $new_perm = $act_perm &  (~perm::puo_avere_amici);
                $msg = " no.";
            }
            $G->set_default_permission($new_perm);
            $res=array("result"=>"OK", "msg"=>"Amici: ".$msg );
        }else{
            $res=array("result"=>"KO", "msg"=>"Non puoi" );
        }
        echo json_encode($res);
        die();
    break;
    case "default_puo_postare_messaggi":
        $id_gas = _USER_ID_GAS;
        $v =CAST_TO_INT($_POST["v"]);
        if(_USER_PERMISSIONS & perm::puo_gestire_utenti){
            $G=new gas($id_gas);
            $act_perm = $G->default_permission;
            if($v>0){
                $new_perm = $act_perm |  perm::puo_postare_messaggi;
                $msg = " si.";
            }else{
                $new_perm = $act_perm &  (~perm::puo_postare_messaggi);
                $msg = " no.";
            }
            $G->set_default_permission($new_perm);
            $res=array("result"=>"OK", "msg"=>"Postare messaggi: ".$msg );
        }else{
            $res=array("result"=>"KO", "msg"=>"Non puoi" );
        }
        echo json_encode($res);
        die();
    break;
    case "default_puo_operare_con_crediti":
        $id_gas = _USER_ID_GAS;
        $v =CAST_TO_INT($_POST["v"]);
        if(_USER_PERMISSIONS & perm::puo_gestire_utenti){
            $G=new gas($id_gas);
            $act_perm = $G->default_permission;
            if($v>0){
                $new_perm = $act_perm |  perm::puo_operare_con_crediti;
                $msg = " si.";
            }else{
                $new_perm = $act_perm &  (~perm::puo_operare_con_crediti);
                $msg = " no.";
            }
            $G->set_default_permission($new_perm);
            $res=array("result"=>"OK", "msg"=>"Operare con crediti altrui: ".$msg );
        }else{
            $res=array("result"=>"KO", "msg"=>"Non puoi" );
        }
        echo json_encode($res);
        die();
    break;
    case "default_puo_gestire_ordini":
        $id_gas = _USER_ID_GAS;
        $v =CAST_TO_INT($_POST["v"]);
        if(_USER_PERMISSIONS & perm::puo_gestire_utenti){
            $G=new gas($id_gas);
            $act_perm = $G->default_permission;
            if($v>0){
                $new_perm = $act_perm |  perm::puo_creare_ordini;
                $msg = " si.";
            }else{
                $new_perm = $act_perm &  (~perm::puo_creare_ordini);
                $msg = " no.";
            }
            $G->set_default_permission($new_perm);
            $res=array("result"=>"OK", "msg"=>"Gestire ordini: ".$msg );
        }else{
            $res=array("result"=>"KO", "msg"=>"Non puoi" );
        }
        echo json_encode($res);
        die();
    break;
    case "default_puo_partecipare_ordini":
        $id_gas = _USER_ID_GAS;
        $v =CAST_TO_INT($_POST["v"]);
        if(_USER_PERMISSIONS & perm::puo_gestire_utenti){
            $G=new gas($id_gas);
            $act_perm = $G->default_permission;
            if($v>0){
                $new_perm = $act_perm |  perm::puo_partecipare_ordini;
                $msg = " si.";
            }else{
                $new_perm = $act_perm &  (~perm::puo_partecipare_ordini);
                $msg = " no.";
            }
            $G->set_default_permission($new_perm);
            $res=array("result"=>"OK", "msg"=>"Partecipare ordini: ".$msg );
        }else{
            $res=array("result"=>"KO", "msg"=>"Non puoi" );
        }
        echo json_encode($res);
        die();
    break;
    case "o1_go":

        $res=array("result"=>"OK", "msg"=>"Yeah" );
        echo json_encode($res);
        die();
    break;
    case "nuovo_gas":
    //CHECK PERMESSI
    if(_USER_PERMISSIONS & perm::puo_creare_gas){

    }else{
        $res=array("result"=>"KO", "msg"=>"Non disponi dei permessi necessari" );
        echo json_encode($res);
        die();
    }

    $nuovo_user = CAST_TO_INT($_POST["idutente"]);
    //CONTROLLO SE IL NUOVO UTENTE E' GIA' A CAPO DI UN GAS
    $stmt = $db->prepare("SELECT * from retegas_gas WHERE id_referente_gas=:id_referente_gas;");
    $stmt->bindParam(':id_referente_gas', $nuovo_user, PDO::PARAM_INT);
    $stmt->execute();
    if($stmt->rowCount()>0){
        $res=array("result"=>"KO", "msg"=>"L'utente selezionato è già a capo di un GAS." );
        echo json_encode($res);
        die();
    }

    //CONTROLLARE SE IL DES LASCIA CREARE NUOVI GAS

    $id_des=_USER_ID_DES;

    //INSERT NUOVO GAS
    $stmt = $db->prepare("INSERT INTO retegas_gas (
                                        descrizione_gas,
                                        id_referente_gas,
                                        default_permission,
                                        id_tipo_gas,
                                        gas_permission,
                                        id_des)
                                        VALUES (
                                        :descrizione_gas,
                                        :nuovo_user,
                                        2,
                                        1,
                                        7,
                                        :id_des
                                        );");

    $nome_gas= clean(CAST_TO_STRING($_POST['nomegas']));
    $stmt->bindParam(':descrizione_gas', $nome_gas, PDO::PARAM_STR);
    $stmt->bindParam(':nuovo_user', $nuovo_user, PDO::PARAM_INT);
    $stmt->bindParam(':id_des', $id_des, PDO::PARAM_INT);
    $stmt->execute();
    $newIdGas = $db->lastInsertId();

    //PERMESSI NUOVO GAS

    //CONDIVIDE ORDINI ESTERNI
    $stmt = $db->prepare("INSERT INTO retegas_options (id_gas,chiave,valore_text) VALUES (:id_gas,'_GAS_PUO_COND_ORD_EST','SI');");
    $stmt->bindParam(':id_gas', $newIdGas, PDO::PARAM_INT);
    $stmt->execute();

    //VISIONE CONDIVISA
    $stmt = $db->prepare("INSERT INTO retegas_options (id_gas,chiave,valore_text) VALUES (:id_gas,'_GAS_VISIONE_CONDIVISA','SI');");
    $stmt->bindParam(':id_gas', $newIdGas, PDO::PARAM_INT);
    $stmt->execute();

    //MAIL ORDINI
    $stmt = $db->prepare("INSERT INTO retegas_options (id_gas,chiave,valore_int) VALUES (:id_gas,'_GAS_VISUALIZZAZIONE_MAIL_ORDINE',4);");
    $stmt->bindParam(':id_gas', $newIdGas, PDO::PARAM_INT);
    $stmt->execute();

    //GAS PARTECIPA ORDINI ESTERNI
    $stmt = $db->prepare("INSERT INTO retegas_options (id_gas,chiave,valore_text) VALUES (:id_gas,'_GAS_PUO_PART_ORD_EST','SI');");
    $stmt->bindParam(':id_gas', $newIdGas, PDO::PARAM_INT);
    $stmt->execute();


    //PERMESSI USER
    $U = new user($nuovo_user);
    $act_perm = $U->user_permission;
    $new_perm = $act_perm |  perm::puo_eliminare_messaggi;
    $new_perm = $new_perm |  perm::puo_gestire_la_cassa;
    $new_perm = $new_perm |  perm::puo_mod_perm_user_gas;
    $new_perm = $new_perm |  perm::puo_gestire_utenti;
    $new_perm = $new_perm |  perm::puo_vedere_tutti_ordini;
    $new_perm = $new_perm |  perm::puo_creare_gas;

    $stmt = $db->prepare("UPDATE maaking_users SET id_gas=:id_gas, user_permission = '$new_perm' WHERE userid=:userid LIMIT 1;");
    $stmt->bindParam(':id_gas', $newIdGas, PDO::PARAM_INT);
    $stmt->bindParam(':userid', $nuovo_user, PDO::PARAM_INT);
    $stmt->execute();

    //GLI TOLGO LA CASSA
    $stmt = $db->prepare("DELETE FROM retegas_options WHERE id_user=:userid AND chiave='_USER_USA_CASSA' LIMIT 1;");
    $stmt->bindParam(':userid', $nuovo_user, PDO::PARAM_INT);
    $stmt->execute();

    //MAIL USER
    $sHTML =$_POST["messaggio"];
    $sHTML = strip_tags($sHTML);


    $stmt = $db->prepare("SELECT * FROM  maaking_users WHERE userid = :userid");
    $stmt->bindParam(':userid', $nuovo_user, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $mailTO = $row["email"];
    $fullnameTO = $row["fullname"];

    $mailFROM = _USER_MAIL;
    $fullnameFROM = _USER_FULLNAME;

    $oggetto = "[reteDES] Nuovo GAS: ".$nome_gas;

    $profile = new Template('../../email_rd4/nuovo_gas.html');
    $profile->set("fullnameFROM", $fullnameFROM );
    $profile->set("messaggio", $sHTML );
    $profile->set("nome_gas", $nome_gas );
    $messaggio = $profile->output();


    //if(SEmailMulti($ArrayTO,$fullnameFROM,$mailFROM,$oggetto,$messaggio)){
    if(SEmail($fullnameTO,$mailTO,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"NuovoGAS")){
        $res=array("result"=>"OK", "msg"=>"Messaggio a $fullnameTO inviato." );
    }else{
        $res=array("result"=>"KO", "msg"=>"Messaggio NON recapitato" );
    }


    echo json_encode($res);
    die();
    break;
    case "parametri_ordini_gas":
        $id_gas=_USER_ID_GAS;
        $G = new gas($id_gas);

        if((_USER_PERMISSIONS & perm::puo_creare_gas) OR ($G->id_referente_gas==_USER_ID)){
            $G->set_maggiorazione_ordini(CAST_TO_FLOAT($_POST["maggiorazione_ordini"]));
            $G->set_comunicazione_referenti(clean($_POST["comunicazione_referenti"]));
            $res=array("result"=>"OK", "msg"=>"Parametri salvati." );
        }else{
            $res=array("result"=>"KO", "msg"=>"Non disponi dei permessi necessari" );
        }
        echo json_encode($res);
        die();

    break;

    case "anagrafiche_gas":
        $id_gas=_USER_ID_GAS;
        $G = new gas($id_gas);

        if((_USER_PERMISSIONS & perm::puo_creare_gas) OR ($G->id_referente_gas==_USER_ID)){
            $G->set_descrizione_gas(clean($_POST["descrizione_gas"]));
            
            $config = HTMLPurifier_Config::createDefault();
            $config->set('CSS.MaxImgLength', null);
            $config->set('HTML.MaxImgLength', null);
            $config->set('HTML.SafeIframe', true);
            $config->set('URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%'); //allow YouTube and Vimeo
            $config->set('Attr.AllowedFrameTargets', array('_blank','_self'));
            $config->set('URI.AllowedSchemes', array('http' => true, 'https' => true, 'mailto' => true, 'ftp'=> true, 'nntp' => true, 'news' => true, 'data' => true));
            $purifier = new HTMLPurifier($config);
            $sHTML = $purifier->purify($_POST["nome_gas"]);
            
            
            
            $G->set_nome_gas($sHTML);
            $G->set_mail_gas($_POST["mail_gas"]);
            $G->set_website_gas($_POST["website_gas"]);
            $res=array("result"=>"OK", "msg"=>"Anagrafiche salvate." );
        }else{
            $res=array("result"=>"KO", "msg"=>"Non disponi dei permessi necessari" );
        }
        echo json_encode($res);
        die();

    break;
    case "geolocate_gas":
        $id_gas=_USER_ID_GAS;
        $G = new gas($id_gas);

        if((_USER_PERMISSIONS & perm::puo_creare_gas) OR ($G->id_referente_gas==_USER_ID)){
            $G->set_SEDE_GAS($_POST["indirizzo_gas"]);
            if(CAST_TO_FLOAT($_POST["lat"])>0){
                $G->set_GAS_CG_LAT(CAST_TO_FLOAT($_POST["lat"]));
                $G->set_GAS_CG_LNG(CAST_TO_FLOAT($_POST["lng"]));
                $res=array("result"=>"OK", "msg"=>"Indirizzo salvato." );
            }else{
                $res=array("result"=>"OK", "msg"=>"Indirizzo salvato, anche se non è un indirizzo valido." );
            }

        }else{
            $res=array("result"=>"KO", "msg"=>"Non disponi dei permessi necessari" );
        }
        echo json_encode($res);
        die();

    break;
    case "ordinamento_pagina_compra":
        if(_USER_PERMISSIONS & perm::puo_creare_gas){
            $id_gas = _USER_ID_GAS;
            $value = CAST_TO_INT($_POST["value"],0,4);
            //Aggiorna l'opzione dal GAS.
            $stmt = $db->prepare("DELETE FROM retegas_options WHERE id_gas=:id_gas AND chiave='_USER_GAS_ORDINAMENTO_COMPRA' LIMIT 1;");
            $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $db->prepare("INSERT INTO retegas_options (id_gas,chiave,valore_int) VALUES (:id_gas,'_USER_GAS_ORDINAMENTO_COMPRA',:value);");
            $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
            $stmt->bindParam(':value', $value, PDO::PARAM_INT);
            $stmt->execute();
            $r = "Opzione modificata";

            $res=array("result"=>"OK", "msg"=>$r );
        }else{
           $r = "Non hai i permessi necessari";
           $res= array("result"=>"KO", "msg"=>$r);
        }
        echo json_encode($res);
        die();
    break;
    case "visualizzazione_mail_ordine":
        if(_USER_PERMISSIONS & perm::puo_creare_gas){
            $id_gas = _USER_ID_GAS;
            $value = CAST_TO_INT($_POST["value"],0,4);
            //Aggiorna l'opzione dal GAS.
            $stmt = $db->prepare("DELETE FROM retegas_options WHERE id_gas=:id_gas AND chiave='_GAS_VISUALIZZAZIONE_MAIL_ORDINE' LIMIT 1;");
            $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $db->prepare("INSERT INTO retegas_options (id_gas,chiave,valore_int) VALUES (:id_gas,'_GAS_VISUALIZZAZIONE_MAIL_ORDINE',:value);");
            $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
            $stmt->bindParam(':value', $value, PDO::PARAM_INT);
            $stmt->execute();
            $r = "Opzione modificata";

            $res=array("result"=>"OK", "msg"=>$r );
        }else{
           $r = "Non hai i permessi necessari";
           $res= array("result"=>"KO", "msg"=>$r);
        }
        echo json_encode($res);
        die();
    break;
    
    case "gas_perm_gest_est":
        if(_USER_PERMISSIONS & perm::puo_creare_gas){
            $id_gas = _USER_ID_GAS;
            $value = CAST_TO_INT($_POST["value"],0,1);
            if($value==0){
                $value="NO";
            }else{
                $value="SI";
            }
            //Aggiorna l'opzione dal GAS.
            $stmt = $db->prepare("DELETE FROM retegas_options WHERE id_gas=:id_gas AND chiave='_USER_GAS_PERM_GEST_EST' LIMIT 1;");
            $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
            $stmt->execute();
            $r = "Il tuo GAS si sceglie da solo i suoi referenti.";
            if($value=="SI"){
                $stmt = $db->prepare("INSERT INTO retegas_options (id_gas,chiave,valore_text) VALUES (:id_gas,'_USER_GAS_PERM_GEST_EST','SI');");
                $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
                $stmt->execute();
                $r = "Il tuo GAS potrà far scegliere i suoi referenti da altri gas. Auguri.";
            }
            $res=array("result"=>"OK", "msg"=>$r );
        }else{
           $r = "Non hai i permessi necessari";
           $res= array("result"=>"KO", "msg"=>$r);
        }
        echo json_encode($res);
        die();
    break;
    
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

    case "gas_option_visione_dati_utenti":

        if(_USER_PERMISSIONS & perm::puo_gestire_utenti){
            $id_gas = _USER_ID_GAS;
            $value = CAST_TO_INT($_POST["value"],0,1);
            if($value==0){
                $value="NO";
            }else{
                $value="SI";
            }
            //Aggiorna l'opzione dal GAS.
            $stmt = $db->prepare("DELETE FROM retegas_options WHERE id_gas=:id_gas AND chiave='_USER_GAS_VISIONE_DATI_UTENTI' LIMIT 1;");
            $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
            $stmt->execute();
            $r = "Visione dati utenti ABILITATA";
            if($value=="NO"){
                $stmt = $db->prepare("INSERT INTO retegas_options (id_gas,chiave,valore_text) VALUES (:id_gas,'_USER_GAS_VISIONE_DATI_UTENTI','NO');");
                $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
                $stmt->execute();
                $r = "Visione dati utenti DISABILITATA;";
            }
            $res=array("result"=>"OK", "msg"=>$r );
        }else{
           $r = "Non hai i permessi necessari";
           $res= array("result"=>"KO", "msg"=>$r);
        }
        echo json_encode($res);
        die();

    break;

    case "gas_option_visione_dati_amici":

        if(_USER_PERMISSIONS & perm::puo_gestire_utenti){
            $id_gas = _USER_ID_GAS;
            $value = CAST_TO_INT($_POST["value"],0,1);
            if($value==0){
                $value="NO";
            }else{
                $value="SI";
            }
            //Aggiorna l'opzione dal GAS.
            $stmt = $db->prepare("DELETE FROM retegas_options WHERE id_gas=:id_gas AND chiave='_USER_GAS_VISIONE_DATI_AMICI' LIMIT 1;");
            $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
            $stmt->execute();
            $r = "Visione dati utenti ABILITATA";
            if($value=="NO"){
                $stmt = $db->prepare("INSERT INTO retegas_options (id_gas,chiave,valore_text) VALUES (:id_gas,'_USER_GAS_VISIONE_DATI_AMICI','NO');");
                $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
                $stmt->execute();
                $r = "Visione dati amici DISABILITATA;";
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

        if(_USER_PERMISSIONS & perm::puo_gestire_utenti){
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

    //$puo_partecipare = CAST_TO_INT($_POST["puo_partecipare"],0,1);
    //$puo_gestire = CAST_TO_INT($_POST["puo_gestire"],0,1);

    //if($puo_partecipare>0){
    //    $permessi = perm::puo_partecipare_ordini;
    //}
    //if($puo_gestire>0){
    //    $permessi = $permessi | perm::puo_creare_ordini;
    //}

    //PERMESSI DI DEFAULT
    $G = new gas(_USER_ID_GAS);
    $permessi = $G->default_permission;
    $gas_usa_cassa = $G->gas_usa_cassa;
    //$descrizione_gas = $G->descrizione_gas;
    unset($G);


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
    $codeLINK = APP_URL.'/ajax_rd4/?do=c&c='.$code;

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
    $stmt->bindParam(':permessi', $permessi, PDO::PARAM_INT);

    $stmt->execute();

    //PRENDO ID DEL NUOVO UTENTE
    $newid = $db->lastInsertId();

    if($stmt->rowCount()==1){

        //SE IL GAS HA LA CASSA INSERISCO L'OPZIONE NELL'UTENTE
        if($gas_usa_cassa){
            if($newid>0){
                $stmt = $db->prepare("INSERT INTO retegas_options (id_user,chiave,valore_text)
                VALUES (".$newid.",'_USER_USA_CASSA','SI')");
                $stmt->execute();
            }
        }


        //mail
        $mailFROM = _USER_MAIL;
        $fullnameFROM = _USER_FULLNAME;

        $mailTO = $email;
        $fullnameTO = $fullname;

        $oggetto = "[reteDES] nuovo account creato da ".$fullnameFROM;
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

    case "do_cancella_utenti":
        $utenti = $_POST["values"];
        $ok=$err=0;

        if(_USER_PERMISSIONS & perm::puo_gestire_utenti){
            foreach ($utenti as $userid){
                /*INIZIO LOOP */
                //NON POSSO CANCELLARE ME STESSO
                if($userid<>_USER_ID){
                    $stmt = $db->prepare("UPDATE maaking_users SET isactive=3 WHERE userid = :userid and id_gas="._USER_ID_GAS.";");
                    $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
                    $stmt->execute();
                    if($stmt->rowCount()==1){
                        $ok++;
                    }else{
                        $err++;
                    }
                }else{
                    $err++;    
                }
                /*FINE LOOP */
            }
        }else{
            $res=array("result"=>"KO", "msg"=>"Non hai i permessi necessari" );
        }

        if($err>0){
            $res=array("result"=>"KO", "msg"=>"$ok utenti cancellati; Non è stato possibile cancellare $err utenti." );
        }else{
            $res=array("result"=>"OK", "msg"=>"Sono stati cancellati $ok utenti;" );

        }

        echo json_encode($res);

    break;

    case "do_sospendi_utenti":

        $utenti = $_POST["values"];
        $ok=$err=0;
        $G = new gas(_USER_ID_GAS);
        $motivo_sospensione = $G->get_motivo_sospensione();

        if(_USER_PERMISSIONS & perm::puo_gestire_utenti){
            foreach ($utenti as $userid){
                /*INIZIO LOOP */
                
                //NON POSSO SOSPENDERE ME STESSO
                if($userid<>_USER_ID){
                    $stmt = $db->prepare("UPDATE maaking_users SET isactive=2 WHERE userid = :userid and id_gas="._USER_ID_GAS.";");
                    $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
                    $stmt->execute();
                    if($stmt->rowCount()==1){
                        $U = new user($userid);
                        $U->set_motivo_sospensione($motivo_sospensione);
                        unset($U);
                        $ok++;

                    }else{
                        $err++;
                    }
                }else{
                    $err++;    
                }
                /*FINE LOOP */



            }
        }else{
            $res=array("result"=>"KO", "msg"=>"Non hai i permessi necessari" );
        }

        if($err>0){
            $res=array("result"=>"KO", "msg"=>"$ok utenti sospesi; Non è stato possibile sospendere $err utenti." );
        }else{
            $res=array("result"=>"OK", "msg"=>"Sono stati sospesi $ok utenti;" );

        }

        echo json_encode($res);

    break;

    case "do_attiva_utenti":
        $utenti = $_POST["values"];
        $ok=$err=0;

        if(_USER_PERMISSIONS & perm::puo_gestire_utenti){
            foreach ($utenti as $userid){
                /*INIZIO LOOP */
                $stmt = $db->prepare("UPDATE maaking_users SET isactive=1 WHERE userid = :userid and id_gas="._USER_ID_GAS.";");
                $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
                $stmt->execute();
                if($stmt->rowCount()==1){


                    $U = new user($userid);
                    $U->delete_motivo_sospensione();
                    unset($U);

                    $stmt = $db->prepare("SELECT * FROM  maaking_users WHERE userid = :userid");
                    $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                    $mailTO = $row["email"];
                    $fullnameTO = $row["fullname"];

                    $mailFROM = _USER_MAIL;
                    $fullnameFROM = _USER_FULLNAME;

                    $oggetto = "[reteDES] Avvenuta attivazione.";
                    $profile = new Template('../../email_rd4/attiva_utente.html');

                    $profile->set("fullnameFROM", $fullnameFROM );
                    $profile->set("gasNOME", _USER_GAS_NOME );

                    $messaggio = $profile->output();
                    SPostino($fullnameTO,$mailTO,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"AttivazioneUtente");

                    $ok++;

                }else{
                    $err++;
                }

                /*FINE LOOP */
            }
        }else{
            $res=array("result"=>"KO", "msg"=>"Non hai i permessi necessari" );
        }

        if($err>0){
            $res=array("result"=>"KO", "msg"=>"$ok utenti attivati; Non è stato possibile attivare $err utenti." );
        }else{
            $res=array("result"=>"OK", "msg"=>"Sono stati attivati $ok utenti;" );

        }

        echo json_encode($res);

    break;

    case "attiva_utente":
        $userid = $_POST["value"];
        $userid = $converter->decode($userid);

        if(_USER_PERMISSIONS & perm::puo_gestire_utenti){

            $stmt = $db->prepare("UPDATE maaking_users SET isactive=1 WHERE userid = :userid and id_gas="._USER_ID_GAS.";");
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

                $oggetto = "[reteDES] Avvenuta attivazione.";
                $profile = new Template('../../email_rd4/attiva_utente.html');

                $profile->set("fullnameFROM", $fullnameFROM );
                $profile->set("gasNOME", _USER_GAS_NOME );

                $messaggio = $profile->output();

                if(SEmail($fullnameTO,$mailTO,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"AttivazioneUtente")){
                    $res=array("result"=>"OK", "msg"=>"$fullnameTO attivato." );
                }else{
                    $res=array("result"=>"KO", "msg"=>"Utente attivato, ma mail utente non raggiungibile" );
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

    case "sposta_utenti":
        $utenti = $_POST["values"];
        $id_gas = $_POST["id_gas"];
        $conserva_i_dati = CAST_TO_INT($_POST["crea_ghost"],0,1);
        
        if($conserva_i_dati==1){
            //$res=array("result"=>"OK", "msg"=>"Opzione \"conserva i dati\" non ancora attiva" );
            //echo json_encode($res);
            //die();    
        }

        $G=new gas($id_gas);

        foreach ($utenti as $id_utente){
            
            
            $stmt = $db->prepare("SELECT fullname, email, username FROM maaking_users WHERE userid=:userid");
            $stmt->bindParam(':userid', $id_utente, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch();
            
            $old_email=$row["email"];
            $old_username = $row["username"];
            $old_password = $row["password"];
            
            //PER MAIL
            $n++;
            $r[]=array( 'email' => $row["email"],
                        'name' => utf8_encode($row["fullname"]),
                        'type' => 'bcc');
            //PER MAIL
            
            if($conserva_i_dati==0){
                $stmt = $db->prepare("UPDATE maaking_users SET id_gas=:id_gas WHERE userid=:userid");
                $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
                $stmt->bindParam(':userid', $id_utente, PDO::PARAM_INT);
                $stmt->execute();
            }else{
            
               
                
                //CREO UN NUOVO UTENTE COPIANDO I DATI DI QUELLO VECCHIO
                //TRANNE: USER, MAIL e PWD CHE RIPRENDO DA QUELLI VECCHI.
                $stmt = $db->prepare("CREATE TEMPORARY TABLE tmp SELECT * FROM maaking_users WHERE userid=:userid; 
                                        UPDATE tmp set id_gas=:id_gas;
                                        ALTER TABLE tmp drop userid; 
                                        INSERT INTO maaking_users SELECT NULL,tmp.* FROM tmp;
                                        DROP TABLE tmp;");
                                        
                $stmt->bindParam(':userid', $id_utente, PDO::PARAM_INT);
                $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
                $stmt->execute();
                
                
                
                //CAMBIO USER, MAIL IN QUELLO VECCHIO, settando stato = 4 (Trasferito?)
                $old_username = $old_username."_TRASFERITO";
                $old_email = $old_email."_TRASFERITO";
                
                $stmt = $db->prepare("UPDATE maaking_users SET isactive=4, username=:username, email=:email WHERE userid = :userid;");
                $stmt->bindParam(':userid', $id_utente, PDO::PARAM_INT);
                $stmt->bindParam(':username', $old_username, PDO::PARAM_STR);
                $stmt->bindParam(':email', $old_email, PDO::PARAM_STR);
                $stmt->execute();
                
                //TROVO IL NUOVO USERID
                $email = $row["email"];
                $stmt = $db->prepare("SELECT userid FROM maaking_users WHERE email=:email LIMIT 1");
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->execute();
                $row = $stmt->fetch();
        
                $new_userid = $row["userid"];
                
                
                //TRASFERISCO TUTTE LE OPTION DA USER VECCHIO A NUOVO
                $stmt = $db->prepare("UPDATE retegas_options SET id_user=:new_userid WHERE id_user=:old_userid;");
                $stmt->bindParam(':old_userid', $id_utente, PDO::PARAM_INT);
                $stmt->bindParam(':new_userid', $new_userid, PDO::PARAM_INT);
                $stmt->execute();
                
                //LA CASSA NON SI TOCCA
                //GLI ORDINI NON SI TOCCANO
                //I DETTAGLI NON SI TOCCANO
                //GLI AMICI SI PERDONO.
                
                
            }

            
         }

        //MAIL------------------------------------------------
            $fullnameFROM = _USER_FULLNAME;
            $mailFROM = _USER_MAIL;

            $messaggio='<p>Ciao,<br>'._USER_FULLNAME.' ti ha spostato in <strong>'.$G->descrizione_gas.'</strong><br>
                        La prossima volta che accedi a reteDES.it ti troverai nel tuo nuovo GAS.</p>';

            //manda mail di carico credito
            $oggetto = "[reteDES] Spostamento GAS.";
            $profile = new Template('../../email_rd4/generica.html');

            $profile->set("messaggio", $messaggio );


            $messaggio = $profile->output();

            SEmailMulti($r,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"MessaggioMultiplo");
        //MAIL------------------------------------------------


        $res=array("result"=>"OK", "msg"=>"Spostamento fatto a $n utenti" );
        echo json_encode($res);

    break;

    case "cambia_stato_utenti":
        if(!(_USER_PERMISSIONS & perm::puo_vedere_retegas)){
            $res=array("result"=>"OK", "msg"=>"Non hai i permessi necessari" );
            echo json_encode($res);
            die();
        }

        $utenti = $_POST["values"];
        $op = $_POST["op"];

        if($op=="attiva"){
            $isactive=1;
        }
        if($op=="sospendi"){
            $isactive=2;
        }
        if($op=="cancella"){
            $isactive=3;
        }

        foreach ($utenti as $id_utente){
            $stmt = $db->prepare("UPDATE maaking_users set isactive=:isactive WHERE userid=:userid");
            $stmt->bindParam(':userid', $id_utente, PDO::PARAM_INT);
            $stmt->bindParam(':isactive', $isactive, PDO::PARAM_INT);
            $stmt->execute();
            $n++;
         }

        //MAIL------------------------------------------------
            //$fullnameFROM = _USER_FULLNAME;
            //$mailFROM = _USER_MAIL;

            //$oggetto = "[reteDES] Messaggio da "._USER_FULLNAME;
            //$profile = new Template('../../email_rd4/basic_2.html');

            //$profile->set("fullnameFROM", _USER_FULLNAME );
            //$profile->set("messaggio", $messaggio );


            //$messaggio = $profile->output();

            //SEmailMulti($r,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"MessaggioMultiplo");
            //MAIL------------------------------------------------


        $res=array("result"=>"OK", "msg"=>"Stato cambiato a $n utenti" );
        echo json_encode($res);

    break;

    case "messaggia_utenti":
        $utenti = $_POST["values"];
        //$messaggio = clean($_POST["messaggio"]);
        //data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAkGBwgHBgkIBwgKCgkLDRYPDQwMDRsUFRAWIB0iIiAdHx8kKDQsJCYxJx8fLT0tMTU3Ojo6Iys/RD84QzQ5OjcBCgoKDQwNGg8PGjclHyU3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3N//AABEIAGYAmgMBIgACEQEDEQH/xAAbAAEAAgMBAQAAAAAAAAAAAAAABAUBAwYCB//EADQQAAEDAgQCBwgBBQAAAAAAAAEAAgMEEQUhMUESUQYTFCJhcZEyQmKBobHB0SMVJENT4f/EABoBAQACAwEAAAAAAAAAAAAAAAADBAECBQb/xAAjEQACAgEFAAIDAQAAAAAAAAAAAQIDEQQFEiExE3EyQWEU/9oADAMBAAIRAxEAPwD7iiIgCIiAIiIAiIgCIiAIiIAiIgCLBWGuDtDdAekREAREQBERAEReJZGxRufI4Na0XJOyw2kssHtVON45BhXVRcD6isnuIKWEXfJ+h4rUcXmlu6mjaGbB4Nz+lDwOGKDFKuur23xGscG9acxHGPZjadhqfEkqjHcKLJcIy7JPja7ZcYLNiNRQRy4vSw0tU4kuhhl6wNF8u9YXNtVPWAsq+iMIiIAiIgCIiA8vAcLHMHVUfQ9gp6Kqo2+xS1UkTB4Aq7fIxntua2+lzZUXRlw7bjR4gWurnFufwi6hnJKyPZuvxZ0CLF1lTGgREQBERAFS9JZT1UNONJHXdnsP+2Vw9wa0ucbAZkrmKqp7ZX9b/jaeFt91zN1u+PTuK9fRJVFt5JdC0QxgBotbdRcXZxtuXEG+2SnwElu42zCj1rBJYDNefl1SkiZfkXdJIZaaKQ+8wE+i3KqwWqaYxTuPeb7PiFaXXrNNdG2pTTK0lhmVi6g4niLKNoaBxyu9lg+5VNI+pqjxTSusfcbkFV1e5Vad8fX/AA3jW5HT3RcyynDO9G57Xc2nRWNDiBaRFVPHg/8ABUWm3WFsuM1xEq8FsvLiALlZvkufxrFC9z6SmOQykePsFd1Ophp6+c2axi5PCITqrtte+SWzo790fCt7xGHOkhbwG1gd1Bp4DYkG2XNSRxNbcuFtF412zk3J/vsuqC8L3Cas1VP3z/JGeF3jyKnrmMLqDR1N5BaKQWdn7J5rpgbr1e26j5qFl9r0qWw4yMoiLoEYREQFP0ime2BsMZsZD3vJVVKGsLW8Om+itMaYXTtOwaqpwtd1jfkvI7rZN6l58Rbq6gWTJQRYZI57SMtfFV4kdsPmvXWkDPZUHqGbcTM5ML+ON1i03Hmr01rG4d2s+zwcXz5Lk6mpyc7O1s7rdS1ZrcLpILngALnfESTb5K5t2udEbPrr7NbIZwZj6yqqHzykuLs1atHdyz01WqKJrWbhJJiyzTY87bKFdNzn6zLeekbSQ2+iiyAHU7eiCbfe61yPsS4m1lFKafgSJX9V6nCpgXfzR2Y3PW+n59FRw94i++nmoeJzS9sh4R/bl1pD8Vjw/QPUqF3E0A52W2q1FlygpfpG1cUslqLBoFwB4Ly4AOBF/BRWvPAAStrJhoL+S0VkfCXB6flyurrAagzUrmOJJidw3PLZURN+dlb9HIiwTuPvELobTZL/AFJLxkN6XAukRF60pBERAQsSj4mB493VU8jM7g3V1XQyyxFsLrOXNzSYhRZVVDNM0aSQAO9R+lw9z0rnLnFE9cusG63MXWmUWtr45KFNj0TGktosRe8e4KR9/tb6qtnxXHay7MN6O1Lb5dZVEMA+Qv8AhciOhsn5El5JGvFpXSTRUkWssgDiNmnVdPRsZELcPdaLZDZcazop0ikqe3YhM1sgNwG6N8grYYs4VMDJ5oYQ55D2PNi61wbG/Oy3t0EqYxY5pnT9Y0DVR58zkQBdapaqGGLrJ5Y42DVz3gBUNR0wwRk3VNqXyO3MMD3t9QLKNUzmukYyi/LgPAD6LRUP4WEnl6Knb0iZPYUOH4hVE6WhLB6ut9l6dhOP44OCeEUVOdWB1y7zP4UtWhsm8YMOSLCZlG/Ap4+0MNXI4StzyBbo30v6qHSyHqgXDW1wrKPoLB2bgfM7jtrdVMmA47gzj2cCupf9RPeaPA/hdDWbc5Qi4LxYMQsSJ7Xd7PzW2NxGdlUHE2xkNqaCugJ1vTudb5tupDMUpy4Nihq5n7COnf8AcgD6rjPS2J44k/yItG3dYWXSYZD1NK0EWc7MqgwiKunka91CYIgb/wApBcfRdS24Ga7216N1N2S9K1089HpERdogCIiAJZEQGLBLBZRYwCo6TYmzC8O4yWiaZ4hgDtC8g/gE/JRIuimHTUkcdbAJXBveLjmTuSr2ppoKpgZUwxzMDg8NkaHAOGYOe4W1RupSnykZzhdHPR9C8BjcHdhY4jTizAVnT4Rh9OAIaOFtuTApyLKqgvEMs1tijbk1jR5Be7LKLfCMBERZBgtB2CwGtGgC9IsYAREWQEREAREQBERAEREAREQBERAEREAREQBERAEREAREQH//2Q==
        $config = HTMLPurifier_Config::createDefault();
        $config->set('CSS.MaxImgLength', null);
        $config->set('HTML.MaxImgLength', null);
        $config->set('HTML.AllowedAttributes', 'href, src, height, width, alt');
        $config->set('HTML.SafeIframe', true);
        $config->set('URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%'); //allow YouTube and Vimeo
        $config->set('Attr.AllowedFrameTargets', array('_blank','_self'));
        $config->set('URI.AllowedSchemes', array('http' => true, 'https' => true, 'mailto' => true, 'ftp'=> true, 'nntp' => true, 'news' => true, 'data' => true));
        
        $purifier = new HTMLPurifier($config);
        $messaggio = $purifier->purify($_POST["messaggio"]);
        

        foreach ($utenti as $id_utente){
            $stmt = $db->prepare("SELECT fullname, email FROM maaking_users WHERE userid=:userid");
            $stmt->bindParam(':userid', $id_utente, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch();
            $n++;
            $r[]=array( 'email' => $row["email"],
                        'name' => utf8_encode($row["fullname"]),
                        'type' => 'bcc');
         }

         //EVITO RIPETIZIONI
         $r= array_unique($r, SORT_REGULAR);
         
        //MAIL------------------------------------------------
            $fullnameFROM = _USER_FULLNAME;
            $mailFROM = _USER_MAIL;


            //manda mail di carico credito
            $oggetto = "[reteDES] Messaggio da "._USER_FULLNAME;
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
    case "elimina_utente_secco":
        $userid = $_POST["value"];
        $userid = $converter->decode($userid);

        if(_USER_PERMISSIONS & perm::puo_gestire_utenti){

            $stmt = $db->prepare("DELETE FROM maaking_users WHERE userid = :userid");
            $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
            $stmt->execute();
            if($stmt->rowCount()==1){
                $res=array("result"=>"OK", "msg"=>"Utente eliminato." );
            }else{
                $res=array("result"=>"KO", "msg"=>"Utente non eliminato. Sorry." );
            }
        }else{
            $res=array("result"=>"KO", "msg"=>"Permessi non abilitati." );
        }

        echo json_encode($res);

    break;

    case "messaggia":
        $sHTML =$_POST["messaggio"];
        $sHTML = strip_tags($sHTML);

        $U = new user(CAST_TO_INT($_POST['id']));
        
        $metodo = CAST_TO_STRING($_POST["metodo"]);

        $stmt = $db->prepare("SELECT * FROM  maaking_users WHERE userid = :userid");
        $stmt->bindParam(':userid', $_POST['id'], PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $mailTO = $row["email"];
        $fullnameTO = $row["fullname"];

        $mailFROM = _USER_MAIL;
        $fullnameFROM = _USER_FULLNAME;

        $oggetto = "[reteDES] Messaggio da ".$fullnameFROM;

        $profile = new Template('../../email_rd4/basic_2.html');
        $profile->set("fullnameFROM", $fullnameFROM );
        $profile->set("messaggio", $sHTML );
        $messaggio = $profile->output();


        //if(SEmailMulti($ArrayTO,$fullnameFROM,$mailFROM,$oggetto,$messaggio)){
        if($metodo=="SMS"){
            SAmazonSMS($U->tel,$sHTML);
            $res=array("result"=>"OK", "msg"=>"SMS a $fullnameTO (".$U->tel.") inviato." );    
        }else{
            if($metodo=="TELEGRAM"){
                STelegram($U->userid, $U->email, _USER_FULLNAME.' ('._USER_GAS_NOME.') ti dice: '.$sHTML );
                $res=array("result"=>"OK", "msg"=>"Telegram a $fullnameTO inviato." );    
            }else{
                if(SEmail($fullnameTO,$mailTO,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"MessaggioPrivato")){
                    $res=array("result"=>"OK", "msg"=>"Messaggio a $fullnameTO inviato." );
                }else{
                    $res=array("result"=>"KO", "msg"=>"Messaggio NON recapitato. Mail utente non raggiungibile." );
                }
            }
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

