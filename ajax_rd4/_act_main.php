<?php
if($_POST["act"]=="do_login"){$skip_check=true;}
if($_POST["act"]=="do_forgotten"){$skip_check=true;}
if(file_exists("../../lib_rd4/class.rd4.ordine.php")){require_once("../../lib_rd4/class.rd4.ordine.php");}
if(file_exists("../lib_rd4/class.rd4.ordine.php")){require_once("../lib_rd4/class.rd4.ordine.php");}

if(file_exists("../../lib_rd4/htmlpurifier-4.7.0/library/HTMLPurifier.auto.php")){require_once("../../lib_rd4/htmlpurifier-4.7.0/library/HTMLPurifier.auto.php");}
if(file_exists("../lib_rd4/htmlpurifier-4.7.0/library/HTMLPurifier.auto.php")){require_once("../lib_rd4/htmlpurifier-4.7.0/library/HTMLPurifier.auto.php");}

require_once("inc/init.php");

switch ($_POST["act"]) {

    case "update_cose":


    //USER ONLINE
    $stmt = $db->prepare("SELECT COUNT(maaking_users.fullname) FROM maaking_users WHERE (time_to_sec(timediff(now(),maaking_users.last_activity))/60)<2;");
    $stmt->execute();
    $row = $stmt->fetch();
    $row[0]==1 ? $user_online= "Ci sei solo tu, ma..." : $user_online= $row[0]." utenti, e intanto...";

    //SOLDI!
    $stmt = $db->prepare("SELECT Sum(retegas_dettaglio_ordini.qta_arr*retegas_dettaglio_ordini.prz_dett_arr) FROM retegas_dettaglio_ordini");
    $stmt->execute();
    $row = $stmt->fetch();
    $totale_netto = round($row[0]);

    $res=array("result"=>"OK", "msg"=>"Updated", "user_online"=> $user_online, "totale_euro"=> " € ".$totale_netto);
    echo json_encode($res);

    die();
    break;

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
            $mailFROM = "info@retedes.it";
            $fullnameFROM = "reteDES.it";

            $mailTO = $email;
            $fullnameTO = $fullname;

            $oggetto = "[reteDES] richiesta nuova password ";
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
                $res=array("result"=>"KO", "msg"=>"Errore invio mail. L\'utente potrebbe non avere una email valida o raggiungibile." );
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
                $stmt = $db->prepare("SELECT valore_text FROM retegas_options WHERE id_user=:id_user AND chiave='_NOTE_SUSPENDED' LIMIT 1;");
                $stmt->bindParam(':id_user', $row["userid"], PDO::PARAM_INT);
                $stmt->execute();
                $rowSUS=$stmt->fetch();
                $msg=CAST_TO_STRING($rowSUS["valore_text"]);

                if($msg<>""){
                    $msg=$msg;
                }else{
                    $msg="Account sospeso";
                }

                $res=array("result"=>"KO", "msg"=>$msg, "sospeso"=>"SI" );
                echo json_encode($res);
                die();
            }
            if($row["isactive"]>2){
                $res=array("result"=>"KO", "msg"=>"Utente non riconosciuto." );
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
    case "delete_suggerimento":
            if (_USER_PUO_MODIFICARE_HELP){
                $stmt = $db->prepare("UPDATE retegas_options set valore_int=1 where chiave='_SUGGERIMENTO_V4' and id_option=:id_option LIMIT 1;");
                $stmt->bindParam(':id_option', $_POST["id_option"], PDO::PARAM_INT);
                $stmt->execute();
                    $res=array("result"=>"OK", "html"=>"" );
                }else{
                    $res=array("result"=>"KO", "msg"=>"KO." );
                }

            echo json_encode($res);
    break;
    case "delete_suggerimento_totale":
            if (_USER_PUO_MODIFICARE_HELP){
                $stmt = $db->prepare("DELETE FROM retegas_options  where chiave='_SUGGERIMENTO_V4' and id_option=:id_option LIMIT 1;");
                $stmt->bindParam(':id_option', $_POST["id_option"], PDO::PARAM_INT);
                $stmt->execute();
                    $res=array("result"=>"OK", "html"=>"" );
                }else{
                    $res=array("result"=>"KO", "msg"=>"KO." );
                }

            echo json_encode($res);
    break;
    case "update_suggerimenti_vecchi":

            $stmt = $db->prepare("SELECT * from  retegas_options where chiave='_SUGGERIMENTO_V4' and valore_int=1 order by id_option DESC;");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach($rows as $row){
                if(_USER_PUO_MODIFICARE_HELP){
                    $b='<i style="cursor:pointer" class="fa fa-times text-danger delete_suggerimento_totale pull-right" data-id="'.$row["id_option"].'"></i>';
                }else{
                    $b='';
                }
                $html.='<p class="well well-sm well-light text-muted">'.$b.$row["note_1"].'</p>';
            }

            $html = '<h1>Presi in carico</h1>'.$html;

            $res=array("result"=>"OK", "html"=>$html );
            echo json_encode($res);
    break;
    case "update_suggerimenti":

            $stmt = $db->prepare("SELECT * from  retegas_options where chiave='_SUGGERIMENTO_V4' and valore_int<>1 order by id_option DESC;");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach($rows as $row){
                if(_USER_PUO_MODIFICARE_HELP){
                    $b='<i style="cursor:pointer" class="fa fa-eye text-info delete_suggerimento pull-right" data-id="'.$row["id_option"].'"></i>';
                }else{
                    $b='';
                }
                $html.='<p class="well well-sm well-light">'.$b.$row["note_1"].'</p>';
            }

            $html = '<h1>Da visionare</h1>'.$html;

            $res=array("result"=>"OK", "html"=>$html );
            echo json_encode($res);
    break;

    case "salva_suggerimento":

            $link = new Encryption();
            $sHTML =$_POST["sHTML"];
            $sHTML = strip_tags($sHTML);

            $msg = '<i class="fa fa-2x fa-user pull-left"></i><small class="font-sm text-info"><a href="'.APP_URL.'/#ajax_rd4/user/scheda.php?id='.$link->encode(_USER_ID).'">'._USER_FULLNAME."</a>, "._USER_GAS_NOME.", il ".date('d/m/Y H:i').'</small><br>';
            $sHTML = $msg.$sHTML;
            $stmt = $db->prepare("INSERT INTO retegas_options (chiave,valore_text,id_user,note_1,id_gas, valore_int) VALUES ('_SUGGERIMENTO_V4',0,'"._USER_ID."',:sHTML,'"._USER_ID_GAS."', 0)");
            $stmt->bindParam(':sHTML', $sHTML, PDO::PARAM_STR);
            $stmt->execute();

            $res=array("result"=>"OK", "msg"=>$msg );

            echo json_encode($res);
    break;
    case "salva_help":
        if (_USER_PUO_MODIFICARE_HELP){


            $stmt = $db->prepare("SELECT valore_int from retegas_options WHERE valore_text=:pagina AND chiave='_HELP_V4' ORDER BY id_option DESC LIMIT 1;");
            $stmt->bindParam(':pagina', $_POST['pagina'], PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch();

            $indice = $row[0] +1;

            //UNPUPLISH OLDER PAGES
            $stmt = $db->prepare("UPDATE retegas_options SET valore_real=0 WHERE valore_text=:pagina AND chiave='_HELP_V4';");
            $stmt->bindParam(':pagina', $_POST['pagina'], PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch();


            $sHTML =$_POST["sHTML"];
            //$sHTML = strip_tags($sHTML,"<br><a><table><tr><td><code><small><alt><b><strong><ul><li><ol><oi><hr><h1><h2><h3><h4><h5><h6><p><img><hr>");
            $config = HTMLPurifier_Config::createDefault();
            $config->set('CSS.MaxImgLength', null);
            $config->set('HTML.MaxImgLength', null);
            $config->set('HTML.SafeIframe', true);
            $config->set('URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%'); //allow YouTube and Vimeo
            $config->set('Attr.AllowedFrameTargets', array('_blank','_self'));
            $config->set('URI.AllowedSchemes', array('http' => true, 'https' => true, 'mailto' => true, 'ftp'=> true, 'nntp' => true, 'news' => true, 'data' => true));

            $purifier = new HTMLPurifier($config);
            $sHTML = $purifier->purify($sHTML);
            
            
            
            $msg = '<code class="note pull-right">Revisione <b>'.$indice.'</b> di '._USER_FULLNAME.", "._USER_GAS_NOME.", il ".date('d/m/Y H:i').'</code>';


            //VALORE_REAL=1 -> PUBLISHED
            $stmt = $db->prepare("INSERT INTO retegas_options (valore_int,chiave,valore_text,id_user,note_1,valore_real) VALUES ('".$indice."','_HELP_V4',:pagina,'"._USER_ID."',:sHTML,1)");
            $stmt->bindParam(':pagina', $_POST['pagina'], PDO::PARAM_STR);
            $stmt->bindParam(':sHTML', $sHTML, PDO::PARAM_STR);
            $stmt->execute();

            $res=array("result"=>"OK", "msg"=>$msg );

            //TELEGRAM
            //PASSO TUTTI GLI UTENTI CHE HANNO TELEGRAM
            STelegramALL('C\'è un aggiornamento dell\'help <a href="'.APP_URL.'/#ajax_rd4/help/ultimi_help.php">'.$_POST['pagina'].'</a> ad opera di '._USER_FULLNAME." ("._USER_GAS_NOME.")");



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
        $stmt = $db->prepare("DELETE FROM retegas_options WHERE id_user="._USER_ID." AND chiave = '_V4_INSIDECONTAINER' ");
        $stmt->bindParam(':value', $_POST['value'], PDO::PARAM_STR);
        $stmt->execute();
        $res=array("result"=>"OK", "msg"=>"Largo: SI" );

        if($_POST['value']=="SI"){
            $stmt = $db->prepare("INSERT INTO retegas_options (id_user,chiave,valore_text)
                                    VALUES ("._USER_ID.",'_V4_INSIDECONTAINER',:value)");
            $stmt->bindParam(':value', $_POST['value'], PDO::PARAM_STR);
            $stmt->execute();
            $res=array("result"=>"OK", "msg"=>"Largo: NO" );
        }


        echo json_encode($res);
    break;
    case "show_ordini_home":

        $sql = "SELECT    O.id_ordini,
                                            O.data_apertura,
                                            O.data_chiusura,
                                            O.is_printable,
                                            O.descrizione_ordini,
                                            O.id_utente as id_referente,
                                            R.id_utente_referenze as id_referente_gas,
                                            R.convalida_referenze
                                    FROM retegas_referenze R
                                    INNER JOIN retegas_ordini O on O.id_ordini=R.id_ordine_referenze
                                  WHERE R.id_gas_referenze=:id_gas and id_utente_referenze>0
                                  ORDER BY O.data_apertura DESC;";
    $stmt = $db->prepare($sql);
            $id_gas = _USER_ID_GAS;
            $stmt->bindParam(':id_gas', $id_gas , PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll();

        

        foreach($rows as $row){

                $gestore = "";
                $gestoreGAS = "";
                $supervisore = "";
                $partecipante ="";
                $umile_aiutante ='';

                $apertura = strtotime($row["data_apertura"]);
                $chiusura = strtotime($row["data_chiusura"]);
                $today = strtotime(date("Y-m-d H:i"));
                if($apertura>$today){
                    $color="text-info";
                    $tooltip="PROGRAMMATO";
                    $label='<span class="pull-right txt-color-blue note hidden-xs text-right">APRIRA\' <br> il <b>'.conv_date_from_db($row["data_apertura"]).'</b></span>';
                    $n_programmati++;
                }
                if($chiusura>$today AND $apertura<$today){
                    $color="text-success";
                    $tooltip="APERTO";
                    $label='<span class="pull-right txt-color-green note hidden-xs text-right">APERTO <br> fino al '.conv_date_from_db($row["data_chiusura"]).'</span>';
                    $n_aperti++;
                }
                if($chiusura<$today and $row["is_printable"]<1){
                    $color="text-danger";
                    $tooltip="CHIUSO";
                    $label='<span class="pull-right txt-color-red note hidden-xs text-right">CHIUSO <br> il '.conv_date_from_db($row["data_chiusura"]).'</span>';
                    $n_chiusi++;
                }
                if($row["is_printable"]>0){
                    if($row["convalida_referenze"]>0){
                        $color="text-muted";
                        $tooltip="CONVALIDATO";
                        $label='<span class="pull-right txt-muted note hidden-xs">CONVALIDATO</span>';
                        $n_convalidati++;
                    }else{
                        $color="text-muted";
                        $tooltip="MANCA LA CONVALIDA DEL TUO GAS";
                        $label='<span class="pull-right txt-color-red note hidden-xs"><i class="fa fa-warning"></i> CONVALIDATO</span>';
                    }
                }



                $stmt = $db->prepare("select * from retegas_dettaglio_ordini where id_utenti='"._USER_ID."' AND id_ordine=:id_ordine");
                $stmt->bindParam(':id_ordine', $row["id_ordini"] , PDO::PARAM_INT);
                $stmt->execute();

                if($stmt->rowCount()>0){
                    $partecipante ='<a href="#ajax_rd4/reports/la_mia_spesa.php?id='.$row["id_ordini"].'""><i class="fa fa-shopping-cart"></i></a> Partecipante';
                    $euro_partecipante='<i class="fa fa-euro text-primary"></i> '._NF(VA_ORDINE_USER($row["id_ordini"],_USER_ID)).' ';
                    $n_acquistato++;
                }else{
                    $partecipante ='';
                    $euro_partecipante='';
                }




                if($row["id_referente"]==_USER_ID){
                    $gestore = '<a href="#ajax_rd4/ordini/edit.php?id='.$row["id_ordini"].'""><i class="fa fa-gears"></i></a> Gestore';
                }else{
                    if($row["id_referente_gas"]==_USER_ID){
                        $gestoreGAS ='<a href="#ajax_rd4/ordini/edit.php?id='.$row["id_ordini"].'""><i class="fa fa-home"></i></a> Gestore GAS';
                    }else{
                        if(_USER_PERMISSIONS & perm::puo_vedere_tutti_ordini){
                            $supervisore='<a href="#ajax_rd4/ordini/edit.php?id='.$row["id_ordini"].'""><i class="fa fa-star"></i></a> Supervisore';
                        }
                    }

                }


                $stmt = $db->prepare("select * from retegas_options where id_user='"._USER_ID."' AND id_ordine=:id_ordine AND chiave='AIUTO_ORDINI' and valore_int=1");
                $stmt->bindParam(':id_ordine', $row["id_ordini"] , PDO::PARAM_INT);
                $stmt->execute();
                if($stmt->rowCount()>0){
                    $umile_aiutante ='<a href="#ajax_rd4/ordini/ordine.php?id='.$row["id_ordini"].'""><i class="fa fa-hand-o-up "></i></a> Umile aiutante';
                }else{
                    $umile_aiutante ='';
                }

                $tot_ordini++;
                if($partecipante<>'' | $supervisore<>'' | $gestoreGAS<>'' | $gestore<>'' | $umile_aiutante<>''){
                    $tot_ordini_a_che_fare++;
                }
                
                $r .= '<li style="border-bottom:1px dotted #ccc;">
                        '.$label.'
                        <i class="fa fa-circle visible-xs pull-left '.$color.'"></i>
                        <span>
                            #'.$row["id_ordini"].' <a href="#ajax_rd4/ordini/ordine.php?id='.$row["id_ordini"].'">'.utf8_decode($row["descrizione_ordini"]).'</a><br>
                            <i class="note">'.$partecipante.' '.$euro_partecipante.' '.$gestore.' '.$gestoreGAS.' '.$supervisore.' '.$umile_aiutante.'</i>
                        </span>
                      </li>';
                          
                
        }
        $start = '  
                <div class="">
                <form class="smart-form">
                <section>
                    <label class="input"> <i class="icon-append fa fa-filter"></i>
                        <input type="text" placeholder="filtra tra gli ordini..." id="listfilter">
                    </label>
                </section>
                <section class="font-xs">
                    <div class="inline-group">
                        <label class="checkbox">
                            <input type="checkbox" name="checkbox-inline" id="show_programmati">
                            <i></i><strong>'.$n_programmati.'</strong> Programmati</label>
                        <label class="checkbox">
                            <input type="checkbox" name="checkbox-inline"  id="show_aperti">
                            <i></i><strong>'.$n_aperti.'</strong> Aperti</label>
                        <label class="checkbox">
                            <input type="checkbox" name="checkbox-inline"  id="show_chiusi">
                            <i></i><strong>'.$n_chiusi.'</strong> Chiusi</label>
                        <label class="checkbox">
                            <input type="checkbox" name="checkbox-inline"  id="show_convalidati">
                            <i></i><strong>'.$n_convalidati.'</strong> Convalidati</label>
                        <label class="checkbox">
                            <input type="checkbox" name="checkbox-inline"  id="show_partecipo">
                            <i></i><strong>'.$n_acquistato.'</strong> Ho acquistato</label>
                    </div>
                </section>
                </form>
                </div>
                <ul id="list" style="height:400px;overflow-y:auto" class="list-unstyled">';
        $end="</ul>";
        $resa = utf8_encode($start.$r.$end);
        

    $res=array("result"=>"OK", "msg"=>"test2", "html"=>$resa);
    echo json_encode($res);
    break;









    default :
    $res=array("result"=>"KO", "msg"=>"Comando '".$_POST["act"]."' non riconosciuto" );
    echo json_encode($res);
    break;

}