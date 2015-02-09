<?php
if($_POST["act"]=="do_login"){$skip_check=true;}
if($_POST["act"]=="do_forgotten"){$skip_check=true;}
require_once("inc/init.php");

switch ($_POST["act"]) {

    case "do_forgotten":
    $username = strip_tags(trim(CAST_TO_STRING($_POST["username"])));
    $email = strip_tags(trim(CAST_TO_STRING($_POST["email"])));
    if(($username=="") || ($email=="")){
            $res=array("result"=>"KO", "msg"=>"Username o email mancante" );
            echo json_encode($res);
            die();
    }
    $stmt = $db->prepare("SELECT email, fullname FROM maaking_users WHERE email=:email AND username=:username LIMIT 1;");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    if($stmt->rowCount()==1){
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $fullname = $row["fullname"];
        $password = rand(10000000,99999999);
        $md5password = md5($password);
        $stmt = $db->prepare("UPDATE maaking_users
                              SET password=:md5password
                              WHERE username=:username
                              AND email=:email
                              LIMIT 1;");
        $stmt->bindParam(':md5password', $md5password, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()==1){

            //mail
            $mailFROM = "retegas.ap@gmail.com";
            $fullnameFROM = "reteDES.it";

            $mailTO = $email;
            $fullnameTO = $fullname;

            $oggetto = "[reteDES.it] richiesta nuova password ";
            $profile = new Template('../email_rd4/nuova_password.html');

            $profile->set("newUSERNAME", $username );
            $profile->set("newPASSWORD", $password );


            $messaggio = $profile->output();


            if(SEmail($fullnameTO,$mailTO,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"password")){
                $res=array("result"=>"OK", "msg"=>"La nuova password ti è stata appena spedita.<br>Controlla la tua mail anche nella cartella della SPAM, se non la vedi in <i>posta arrivata</i>" );
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
            $res=array("result"=>"KO", "msg"=>"Errore." );
            echo json_encode($res);
            die();
        }
    }else{
        $res=array("result"=>"KO", "msg"=>"Nessun utente con questo username / email" );
        echo json_encode($res);
        die();
    }



    break;

    /*
    /* -------------------------LOGIN
    */

    case "do_login":

        $username = strip_tags(trim(CAST_TO_STRING($_POST["username"])));
        $password = strip_tags(trim(CAST_TO_STRING($_POST["password"])));

        if(($username=="") || ($password=="")){
                $res=array("result"=>"KO", "msg"=>"Username o password mancanti" );
                echo json_encode($res);
                die();
        }

        $md5password = md5($password);

        $stmt = $db->prepare("SELECT * FROM maaking_users WHERE (email=:email OR username=:username) AND password=:md5password LIMIT 1;");
        $stmt->bindParam(':email', $username, PDO::PARAM_STR);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':md5password', $md5password, PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()==1){
            $row = $stmt->fetch();
            if($row["isactive"]==1){

                $lastlogin = explode(" ", $row['lastlogin']);
                $lastlogin_date =  $lastlogin[0];
                $lastlogin_time = $lastlogin[1];
                $userid = $row['userid'];
                $username = $row['username'];
                $password = $row['password'];
                $ipaddress = $row['ipaddress'];
                $isactive = $row['isactive'];
                $user_permission = $row['user_permission'];
                $user_options = $row['user_site_option'];
                $info = base64_encode("$userid|$username|$password|$ipaddress|$lastlogin_date|$lastlogin_time|$user_permission|$user_options");
                if (CAST_TO_INT($_POST["remember"])>0){
                     setcookie("user","$info",time()+1209600,"/");
                }else{
                     setcookie("user","$info",0,"/");
                }

                $RA = $_SERVER['REMOTE_ADDR'];
                $stmt = $db->prepare("UPDATE maaking_users SET ipaddress='$RA', lastlogin=NOW() WHERE userid='$userid' LIMIT 1;");
                $stmt->execute();

                $res=array("result"=>"OK", "msg"=>"Bentornato !" );
                echo json_encode($res);
                die();
            }
            if($row["isactive"]==0){
                $res=array("result"=>"KO", "msg"=>"Utenza non ancora attivata !" );
                echo json_encode($res);
                die();
            }
            if($row["isactive"]==2){
                $res=array("result"=>"KO", "msg"=>"Utenza sospesa" );
                echo json_encode($res);
                die();
            }
            if($row["isactive"]==99){
                $res=array("result"=>"KO", "msg"=>"Utenza disattivata" );
                echo json_encode($res);
                die();
            }
        }else{
            $res=array("result"=>"KO", "msg"=>"Utente non riconosciuto." );
            echo json_encode($res);
            die();
        }

        $res=array("result"=>"KO", "msg"=>"Errore!" );
        echo json_encode($res);
        die();
    break;
    case "do_logout":
        setcookie('user','',time()-3600,'/');
        $res=array("result"=>"OK", "msg"=>"Logout" );
        echo json_encode($res);
        die();
    break;
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
    case "nonmostrarepiu":
        $stmt = $db->prepare("DELETE retegas_options WHERE id_user="._USER_ID." AND chiave = '_V4_NONMOSTRAREPIU' LIMIT 1;");
        $stmt->bindParam(':value', $_POST['value'], PDO::PARAM_STR);
        $stmt->execute();
        $res=array("result"=>"OK", "msg"=>"Ok, come non detto!" );

        if($_POST['value']=="SI"){
            $stmt = $db->prepare("INSERT INTO retegas_options (id_user,chiave,valore_text)
                                    VALUES ("._USER_ID.",'_V4_NONMOSTRAREPIU',:value)");
            $stmt->bindParam(':value', $_POST['value'], PDO::PARAM_STR);
            $stmt->execute();
            $res=array("result"=>"OK", "msg"=>"Ricarica la pagina per non vedere più il benvenuto" );
        }


        echo json_encode($res);
    break;
    case "insidecontainer":
        $stmt = $db->prepare("DELETE retegas_options WHERE id_user="._USER_ID." AND chiave = '_V4_INSIDECONTAINER' LIMIT 1;");
        $stmt->bindParam(':value', $_POST['value'], PDO::PARAM_STR);
        $stmt->execute();
        $res=array("result"=>"OK", "msg"=>"Ok" );

        if($_POST['value']=="SI"){
            $stmt = $db->prepare("INSERT INTO retegas_options (id_user,chiave,valore_text)
                                    VALUES ("._USER_ID.",'_V4_INSIDECONTAINER',:value)");
            $stmt->bindParam(':value', $_POST['value'], PDO::PARAM_STR);
            $stmt->execute();
            $res=array("result"=>"OK", "msg"=>"OK.." );
        }


        echo json_encode($res);
    break;
    default :
    $res=array("result"=>"KO", "msg"=>"Comando '".$_POST["act"]."' non riconosciuto" );
    echo json_encode($res);
    break;

}