<?php
require_once("inc/init.php");

if(!empty($_POST["act"])){
    switch ($_POST["act"]) {

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

