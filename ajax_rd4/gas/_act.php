<?php
require_once("inc/init.php");
$converter = new Encryption;

if(!empty($_POST["act"])){
    switch ($_POST["act"]) {

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

                if(SEmail($fullnameTO,$mailTO,$fullnameFROM,$mailFROM,$oggetto,$messaggio)){
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

        if(SEmail($fullnameTO,$mailTO,$fullnameFROM,$mailFROM,$oggetto,$messaggio)){
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

