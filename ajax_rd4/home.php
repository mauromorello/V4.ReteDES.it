<?php
require_once("inc/init.php");
require_once("../lib_rd4/class.rd4.ordine.php");
require_once("../lib_rd4/class.rd4.cassa.php");
require_once("../lib_rd4/class.rd4.ditta.php");
$converter = new Encryption();
$ui = new SmartUI;

$page_title = "Cruscotto";
$page_id="cruscotto";

$h=file_get_contents("help/home.html");
$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>false,
                    "deletebutton"=>false,
                    "colorbutton"=>false);
$wg_help = $ui->create_widget($options);
$wg_help->id = "wg_help_home";
$wg_help->body = array("content" => $h,"class" => "");
$wg_help->header = array(
    "title" => '<h2>Aiuto</h2>',
    "icon" => 'fa fa-question-circle'
);


$saldo = _NF(VA_CASSA_SALDO_UTENTE_TOTALE(_USER_ID));
$saldo_non_conf = abs(_NF(VA_CASSA_SALDO_UTENTE_DA_REGISTRARE(_USER_ID)));

$cassa='<h1>Hai <b><span class="txt-color-blue font-xl">'.$saldo.' €</span></b> nel conto</h1>
        <h3><b><span class="txt-color-red font-lg">'.$saldo_non_conf.' €</span></b> non ancora contabilizzati
        <h6>Per visualizzare i movimenti della tua cassa e prenotare una ricarica clicca <a href="#ajax_rd4/user/miacassa.php">qua</a></h6>';


$stmt = $db->prepare("SELECT retegas_ordini.id_ordini,
            retegas_ordini.descrizione_ordini,
            retegas_listini.descrizione_listini,
            retegas_listini.id_tipologie,
            retegas_ditte.descrizione_ditte,
            retegas_ordini.data_chiusura,
            retegas_ordini.data_apertura,
            retegas_gas.descrizione_gas,
            retegas_referenze.id_gas_referenze,
            retegas_referenze.id_utente_referenze,
            maaking_users.userid,
            maaking_users.fullname,
            retegas_ordini.id_utente,
            retegas_ordini.id_listini,
            retegas_ditte.id_ditte,
            retegas_ordini.data_apertura
            FROM (((((retegas_ordini INNER JOIN retegas_referenze ON retegas_ordini.id_ordini = retegas_referenze.id_ordine_referenze) LEFT JOIN maaking_users ON retegas_referenze.id_utente_referenze = maaking_users.userid) INNER JOIN retegas_listini ON retegas_ordini.id_listini = retegas_listini.id_listini) INNER JOIN retegas_ditte ON retegas_listini.id_ditte = retegas_ditte.id_ditte) INNER JOIN maaking_users AS maaking_users_1 ON retegas_ordini.id_utente = maaking_users_1.userid) INNER JOIN retegas_gas ON maaking_users_1.id_gas = retegas_gas.id_gas
            WHERE (((retegas_ordini.data_chiusura)>NOW())
            AND ((retegas_ordini.data_apertura)<NOW())
            AND ((retegas_referenze.id_gas_referenze)="._USER_ID_GAS."))
            ORDER BY retegas_ordini.data_chiusura ASC ;");
$stmt->execute();
$rows = $stmt->fetchAll();

$no=0;
foreach($rows as $row){
$no++;
//TEMPO ALLA CHIUSURA
        $inittime=time();
        $datexmas=strtotime($row["data_chiusura"]);
        $timediff = $datexmas - $inittime;

        $days=intval($timediff/86400);
        $remaining=$timediff%86400;
        if($days>0){$dd="<b>$days</b> gg. e ";}else{$dd="";}


        $hours=intval($remaining/3600);
        $remaining=$remaining%3600;

        $mins=intval($remaining/60);
        $secs=$remaining%60;

        if ($days<2){
            $color = "<span class=\"label label-danger animated swing\">SCADE</span>";
        }else{
            $color = "";
        }

        if($row["id_utente_referenze"]<1){
            $gestore='MANCA il referente per il tuo GAS';
            $colge =' text-danger ';
        }else{
            $gestore= $row["fullname"].', '.$row["descrizione_gas"];
            $colge ='';
        }
        $vo = _NF(VA_ORDINE_USER($row["id_ordini"],_USER_ID));
        if($vo>0){
            $vo='<span class="pull-left margin-top-10">Stai comprando per <strong>'.$vo.'</strong> Eu.</span>';
        }else{
            $vo='<span class="note pull-left margin-top-10">Non hai ancora partecipato.</span>';
        }
        if (DO_CHECK_USER_PRENOTAZIONE_ORDINE($row["id_ordini"], _USER_ID)=="SI"){
            $prenotazione = '<span class="label label-danger pull-right" rel="tooltip" title="prenotazione attiva" data-placement="left"><b>P</b></span>';
        }else{
            $prenotazione = '';
        }

        //NOTA PERSONALE
        $sql = "SELECT
                note_1
                FROM
                retegas_options
                WHERE
                chiave = '_NOTE_ORDINE' AND
                id_ordine =:id_ordine AND
                id_user = :id_user
                LIMIT 1;";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_ordine', $row["id_ordini"] , PDO::PARAM_INT);
        $stmt->bindParam(':id_user', CAST_TO_INT(_USER_ID) , PDO::PARAM_INT);
        $stmt->execute();
        $rowN = $stmt->fetch();
        if($rowN["note_1"]<>""){
            $nota = str_replace('"'," ",$rowN["note_1"]);
            $nota_personale='<span class="pull-right margin-top-10" rel="tooltip" data-original-title="'.$nota.'" data-placement="left"><i class="fa fa-pencil text-info" ></i><span/>';
        }else{
            $nota_personale='';
        }

        if($row["id_ditte"]>0){
            $ditta=$row["descrizione_ditte"];
        }else{
            $ditta="Multiditta";
        }

        if(_USER_ADDTOCALENDAR){
            $atc='
                    <span class="addtocalendar" >
                    <a class="atcb-link"><i class="fa fa-calendar"></i></a>
                    <var class="atc_event">
                        <var class="atc_date_start">'.$row["data_apertura"].'</var>
                        <var class="atc_date_end">'.$row["data_chiusura"].'</var>
                        <var class="atc_timezone">Europe/Rome</var>
                        <var class="atc_title">Ord #'.$row["id_ordini"].': '.$row["descrizione_ordini"].'</var>
                        <var class="atc_description">'.$gestore.'  '.$ditta.', '.$row["descrizione_listini"].'</var>
                        <var class="atc_location">'._USER_GAS_NOME.'</var>
                        <var class="atc_organizer">'.$gestore.'</var>
                        
                    </var>
                </span>
                ';
            //$atc="";    
        }else{
            $atc="";
        }

//ORDINE NASCOSTO PER IL GAS
        $show_this = true;
        $icon_is_nascosto="";

    $sqln = "SELECT valore_text FROM retegas_options WHERE
            chiave='_ORDINE_NASCOSTO_GAS' AND id_ordine= :id_ordine AND id_gas='"._USER_ID_GAS."' LIMIT 1;";
    $stmt = $db->prepare($sqln);
    $stmt->bindParam(':id_ordine', $row["id_ordini"] , PDO::PARAM_INT);
    $stmt->execute();
    $rowna = $stmt->fetch();
    if($rowna["valore_text"]=="SI"){
        $is_nascosto = true;
        $icon_is_nascosto='<a href="#ajax_rd4/ordini/edit_gas.php?id='.$row["id_ordini"].'" class="btn btn-circle btn-xs btn-danger"><i class="fa fa-eye-slash"></i></a>'; 
    }else{
        $is_nascosto = false;     
    }
if($is_nascosto){
    if(posso_gestire_ordine_come_gas($row["id_ordini"])){
        $show_this=true;    
    }else{
        $show_this=false;    
    }    
}else{
    $show_this=true;    
}
//ORDINE NASCOSTO PER IL GAS

       
if($show_this){        
    $oa .='<li> 
               <span class="">'.$atc.'
                    <a href="#ajax_rd4/ordini/ordine.php?id='.$row["id_ordini"].'" class="msg">
                    <img src="img_rd4/t_'.$row["id_tipologie"].'_240.png" alt="" class="air air-top-left margin-top-5 animated tada" width="40" height="40">
                    <span class="from">'.$row["descrizione_ordini"].' <i class="icon-paperclip">'.$color.'</i></span>
                    <time>'.$dd .'<b>'.$hours.'</b> h.</time>
                    <span class=" '.$colge.'">'.$gestore.'</span>
                    <span class="msg-body">'.$ditta.', '.$row["descrizione_listini"].'</span>
                </a>
                '.$vo.'
                '.$prenotazione.'
                '.$nota_personale.'
                
                <span class="pull-right btn-group">   
                    <a href="#ajax_rd4/ordini/compra.php?id='.$row["id_ordini"].'" class="btn btn-circle btn-xs btn-success"><i class="fa fa-shopping-cart"></i></a>
                    '.$icon_is_nascosto.'
                </span>
                
            </span> 

        </li>';
    }
        
}

if($oa<>""){
    $oa ='<ul class="notification-body">'.$oa.'</ul>';
}else{
    $oa ='<p class="alert alert-warning">Non ci sono ordini aperti: <a href="#ajax_rd4/ordini/nuovo.php">aprine</a> uno tu! :)</p>';
}







        //ALERTS
        
        //DOPPIA MAIL
        $email =_USER_MAIL;
        $sql = "SELECT count(*) as conto FROM maaking_users WHERE email=:email;";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':email', $email , PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch();
        if($row["conto"]>1){
            $alert_email_multipla='<p id="doppia_mail_alert" class="alert alert-danger margin-top-10"><a href="javascript:void(0);" class="pull-left margin-top-5" style="margin-right:10px;" rel="tooltip"><i class="fa fa-2x fa-warining"></i></a><span class="text-white"><strong>ATTENZIONE!</strong></span><br> la mail '._USER_MAIL.' è usata per più di un utente: devi cambiarla, o a breve verrà disattivata. <a href="https://retegas.altervista.org/gas4/index.php#ajax_rd4/user/anagrafiche.php"  class="btn btn-default">LINK</a> alla pagina per modificare.</p>';            
        }
        
        
        //LE MIE DITTE HANNO POCHI DATI
        $stmt = $db->prepare("SELECT * FROM retegas_ditte WHERE id_proponente='"._USER_ID."' ORDER BY id_ditte DESC;");
        $stmt->execute();
        $rows = $stmt->fetchAll();
        foreach($rows as $row){
            $D = new ditta($row["id_ditte"]);
            $rate = $D->n_info_disponibili();
            if($rate<6){
                $ditta_ko++;
                $elenco_ditte .='<li><a href="#ajax_rd4/fornitori/scheda.php?id='.$D->id_ditte.'">'.$D->descrizione_ditte.'</a>, '.$rate.' informazioni su '.$D->n_info_totali().'</li>';
            }
            unset($D);
        }
        if($ditta_ko>0){
                $alert_ditta_pochi_dati='<div id="ditta_pochi_dati" class="alert alert-warning margin-top-10"><a href="javascript:void(0);" class="pull-left margin-top-5" style="margin-right:10px;" rel="tooltip"><i class="fa fa-2x fa-truck"></i></a><span class="text-white"><strong>ATTENZIONE!</strong></span><br> Hai inserito '.$ditta_ko.' '.plurale($ditta_ko,"ditta che ha i", "ditte che hanno ").' dati incompleti o mancanti.<br><ul>'.$elenco_ditte.'</ul></div>'; 
        }else{
            $alert_ditta_pochi_dati='';    
        }
        
        
        //SONO REFERENTE GAS MA NON HO SUPERPOTERI
        $userid = _USER_ID;
        $stmt = $db->prepare("SELECT U.user_permission, U.fullname, U.userid, U.tel, U.id_gas, U.email, G.descrizione_gas, G.id_referente_gas FROM  maaking_users U INNER JOIN retegas_gas G ON G.id_gas=U.id_gas  WHERE userid = :userid LIMIT 1;");
        $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row["id_referente_gas"]==_USER_ID){
            $show_alert_responsabile_gas= false;
            if(!($row["user_permission"] & perm::puo_gestire_utenti)){
                $show_alert_responsabile_gas= true;
            }
           
            //SE CI FOSSE BISOGNO DI ALTRO           
            if($show_alert_responsabile_gas){
                $alert_responsabile_gas='<p id="gas_respo_alert" class="alert alert-danger margin-top-10"><a href="javascript:void(0);" class="pull-left margin-top-5" style="margin-right:10px;" rel="tooltip"><i class="fa fa-2x fa-check"></i></a><span class="text-white"><strong>ATTENZIONE!</strong></span><br> sei il responsabile del tuo GAS ma per qualche motivo non puoi controllare gli utenti: clicca <a href="#ajax_rd4/user/gestisci.php?id='.$converter->encode(_USER_ID).'"  class="btn btn-default">QUA</a> per poter attivare i permessi necessari.</p>'; 
            }
        }else{
            $alert_responsabile_gas='';
        }
        
        
        
        //NON HO LA CASSA MA IL MIO GAS SI
        if(!_USER_USA_CASSA AND _USER_GAS_USA_CASSA){
            $alert_non_ho_cassa='<p id="cassa_alert" class="alert alert-danger margin-top-10"><a href="#ajax_rd4/user/impostazioni.php" class="pull-left margin-top-5" style="margin-right:10px;" rel="tooltip" data-content="Vai alle impostazioni"><i class="fa fa-2x fa-bank"></i></a><span class="text-white"><strong>ATTENZIONE!</strong></span><br> Il tuo gas ha la cassa attiva ma tu no. Clicca <a href="javascript:void(0);" id="cb_user_usa_cassa_home" class="btn btn-default">QUA</a> per attivarla.</p>';
        }else{
            $alert_non_ho_cassa='';
        }

        //NOTIFICHE HELP

        $sql="SELECT COUNT(O.id_option) as conto FROM retegas_options O WHERE chiave='_HELP_V4' AND O.timbro>COALESCE((SELECT O2.timbro from retegas_options O2 WHERE O2.chiave='_LAST_HELP_VIEWED' AND O2.id_user="._USER_ID." LIMIT 1),0)";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch();
        $help_vecchi=$row["conto"];

        if($help_vecchi>0){
            if($help_vecchi==1){
                $help_vecchi_testo='C\'è <a href="#ajax_rd4/help/ultimi_help.php"><strong> un aggiornamento </strong></a> su reteDES.it dalla tua ultima visita alla pagina delle novità...';
            }
            if($help_vecchi>1){
                $help_vecchi_testo='Ci sono <a href="#ajax_rd4/help/ultimi_help.php"><strong> '.$help_vecchi.' aggiornamenti </strong></a> su reteDES.it dalla tua ultima visita alla pagina delle novità...';
            }
            if($help_vecchi>10){
                $help_vecchi_testo='Ci sono <a href="#ajax_rd4/help/ultimi_help.php"><strong> tantissimi aggiornamenti ('.$help_vecchi.')</strong></a> su reteDES.it dalla tua ultima visita alla pagina delle novità...';
            }

            $alert_help_from_last_login ='<p class="alert alert-success margin-top-10"><a href="#ajax_rd4/help/ultimi_help.php" class="pull-left margin-top-5" style="margin-right:10px;" rel="tooltip" data-content="Guarda le cose nuove"><i class="fa fa-2x fa-question-circle"></i></a><span class="text-white"><strong>NOVITA\'!</strong></span><br> '.$help_vecchi_testo.'</p>';
        }else{
            $alert_help_from_last_login ='';
        }

        //SELF BOUNCED
            $bo=0;
            $bounced_list ='';
            $stmt = $db->prepare("select B.*, U.* from retegas_bounced B inner join maaking_users U on U.userid=B.userid where B.userid='"._USER_ID."' AND B.bounce_class<>51 ");
            $stmt->execute();
            $rows = $stmt->fetchAll();
            foreach($rows as $row){
                $bo++;    
                $bounced_fullname = $row["fullname"];
                $bounced_email = $row["raw_rcpt_to"];
                $bounced_reason = $row["raw_reason"];
                if((strpos($bounced_reason, 'quota exceeded') !== false)){
                    $bounced_reason="CASELLA DI POSTA PIENA";   
                }

                $bounced_list .= '<b>'.$bounced_email.':</b> '.$bounced_reason.'<br>';
            }
            if($bo>0){
                $alert_bounced_myself='<p class="alert alert-warning margin-top-10"><a href="javascript:void(0)" class="pull-left margin-top-5" style="margin-right:10px;"><i class="fa fa-2x fa-envelope"></i></a><span class="note">LA TUA MAIL NON FUNZIONA</span><br>Una delle tue mail non è attiva:<br> '.$bounced_list.'</p>';                    
            }else{
                $alert_bounced_myself='';    
            }
        
        
        if(_USER_PERMISSIONS & perm::puo_gestire_utenti){
            //HO UTENTI CON MAIL BOUNCED
            $bo=0;
            $bounced_list ='';
            //PRENDE LA EMAIL SOLO SE ESISTE ANCORA NELLA TABELLA DEGLI USER
            //NON CONSIDERA LE EMAIL SECONDARIE - DA VERIFICARE NELLA PAGINA GESTISCI UTENTE
            //BOUNCE CLASS <>51 = NOT BLACKLISTED dal provider primario
            $stmt = $db->prepare("select B.*, U.* from retegas_bounced B inner join maaking_users U on (U.userid=B.userid AND U.email=B.raw_rcpt_to AND B.bounce_class<>51 AND B.provider=1) where B.id_gas='"._USER_ID_GAS."' AND U.isactive=1 ");
            $stmt->execute();
            $rows = $stmt->fetchAll();
            foreach($rows as $row){
                $bo++;    
                $bounced_fullname = $row["fullname"];
                $bounced_email = $row["raw_rcpt_to"];
                $bounced_class= $row["bounce_class"];
                
                switch ($bounced_class) {
                    case 22:
                        $bounced_reason = "CASELLA TROPPO PIENA";
                        break;
                    case 24:
                        $bounced_reason = "DESTINATARIO INESISTENTE"; 
                        break;
                    default:
                        $bounced_reason = $row["raw_reason"];
                        break;
                }

                $res = json_decode(sparkpostAPIget("suppression-list/".$bounced_email),TRUE);
                $bounced_reason.=" <strong>".$res["results"][0]["source"]."</strong>";
                
                $bounced_list .= '<strong>'.$bounced_fullname.'</strong> ('.$bounced_email.'): <span class="text-danger">'.$bounced_reason.'</span>, avvisalo allo <b><a href="tel:'.$row["tel"].'">'.$row["tel"].'</a></b><br>';
            }
            if($bo>0){
                $alert_bounced='<p class="alert alert-warning margin-top-10"><a href="javascript:void(0)" class="pull-left margin-top-5" style="margin-right:10px;" rel="tooltip" data-content="Vai alla gestione mail"><i class="fa fa-2x fa-envelope"></i></a><span class="note">UTENTI CON MAIL PROBLEMATICA</span><br>  Questi utenti non possono ricevere le mail di reteDES: <br>'.$bounced_list.'</p>';                    
            }else{
                $alert_bounced='';    
            }
            
            
            //NUOVI USERS
            $stmt = $db->prepare("select count(*) as conto from maaking_users where isactive=0 AND id_gas='"._USER_ID_GAS."' ");
            $stmt->execute();
            $row = $stmt->fetch();
            if($row["conto"]>0){
                if($row["conto"]==1){
                    $uten = 'C\'è <b>un</b> nuovo utente da attivare;';
                }else{
                    $uten = 'Ci sono <b>'.$row["conto"].'</b> nuovi utenti da attivare;';
                }
                $alert_users='<p class="alert alert-warning margin-top-10"><a href="#ajax_rd4/gas/gas_attivazioni.php" class="pull-left margin-top-5" style="margin-right:10px;" rel="tooltip" data-content="Vai alla tabella movimenti"><i class="fa fa-2x fa-user-plus"></i></a><span class="note">COME GESTORE UTENTI</span><br>  '.$uten.'</p>';
            }else{
                $alert_users='';
            }

        }

        //gas senza GEO
        if(_USER_PERMISSIONS & perm::puo_creare_gas){
            $stmt = $db->prepare("select * from retegas_gas where id_gas='"._USER_ID_GAS."' ");
            $stmt->execute();
            $row = $stmt->fetch();
            //if(true){
            if(CAST_TO_INT($row["gas_gc_lat"],0)==0){
                $alert_geogas='<p class="alert alert-danger margin-top-10"><a href="#ajax_rd4/gas/gas_opzioni.php" class="pull-left margin-top-5" style="margin-right:10px;" rel="tooltip" data-content="Vai alla scheda GAS"><i class="fa fa-2x fa-home"></i></a><span class="note">COME GESTORE GAS</span><br>Il tuo GAS non ha un indirizzo geografico riconosciuto. Vai alla scheda GAS per inserirlo.</p>';
            }else{
                $alert_geogas='';
            }

        }

        //SOGLIA MINIMA VICINA
        if(_USER_USA_CASSA){
            if(_GAS_CASSA_CHECK_MIN_LEVEL){
                
                $soglia_gas = _GAS_CASSA_MIN_LEVEL;
                $saldo_user = VA_CASSA_SALDO_UTENTE_TOTALE(_USER_ID);
                if($soglia_gas>=0){
                    if(($saldo_user-$soglia_gas)<10){
                    
                        $alert_sogliacassa='<p class="alert alert-warning margin-top-10"><a href="#ajax_rd4/user/miacassa.php" class="pull-left margin-top-5" style="margin-right:10px;" rel="tooltip" data-content="Ricarica credito"><i class="fa fa-2x fa-dollar"></i></a><span class="note">COME UTENTE</span><br>Il saldo della tua cassa (<strong>'._nf($saldo_user).' eu.</strong>) è molto vicino alla soglia minima del tuo GAS (<strong>'._nf(_GAS_CASSA_MIN_LEVEL).'</strong>). Se non ricarichi la cassa non puoi fare acquisti.</p>';
        
                    }
                }
            }
        }
        
        //CASSIERE
        if(_USER_GAS_USA_CASSA){
            if(_USER_PERMISSIONS & perm::puo_gestire_la_cassa){
                $C = new cassa(_USER_ID_GAS);

                $id_gas=_USER_ID_GAS;

                //MOVIMENTI NON REGISTRATI

                $mnr = $C->get_movimenti_da_registrare();
                if($mnr>0){
                    if($mnr==1){
                        $uten = 'C\'è <b>un</b> movimento non registrato, ';
                    }else{
                        $uten = 'Ci sono <b>'.$mnr.'</b> movimenti non registrati, ';
                    }
                    $n_ordini = $C->get_ordini_movimenti_da_registrare();
                    if($n_ordini==1){
                        $uten .= ' appartenenti a <b>un</b> ordine convalidato;';
                    }else{
                        $uten .= ' appartenenti a <b>'.$n_ordini.'</b> ordini convalidati';
                    }
                    $alert_movi='<p class="alert alert-info margin-top-10">
                                    <a title="vai alla tabella movimenti" rel="tooltip"  href="#ajax_rd4/cassa/da_registrare.php" class="pull-left margin-top-5" style="margin-right:10px;"><i class="fa fa-2x fa-bank"></i></a>
                                    <a title="vai alla tabella allineamenti" rel="tooltip" href="#ajax_rd4/cassa/allineamento_ordini.php" class="pull-left margin-top-5" style="margin-right:10px;"><i class="fa fa-2x fa-arrows-h"></i></a>
                    <span class="note">COME CASSIERE</span><br>  '.$uten.'</p>';
                }else{
                    $alert_movi='';
                }

                //RICARICHE
                $sql = "select count(*) as conto from retegas_options O where O.id_gas=:id_gas and O.chiave='PREN_MOV_CASSA' ";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
                $stmt->execute();
                $rowRIC = $stmt->fetch(PDO::FETCH_ASSOC);

                if($rowRIC["conto"]>0){
                    if($rowRIC["conto"]==1){
                        $uten = 'C\'è <b>una</b> richiesta di ricarica;';
                    }else{
                        $uten = 'Ci sono <b>'.$rowRIC["conto"].'</b> richieste di ricarica;';
                    }
                    $alert_richi='<p class="alert alert-info margin-top-10"><a href="#ajax_rd4/cassa/richieste.php" class="pull-left margin-top-5" style="margin-right:10px;"><i class="fa fa-2x fa-euro"></i></a><span class="note">COME CASSIERE</span><br>  '.$uten.'</p>';
                }else{
                    $alert_richi='';
                }


            }
        }
        //-------------------POST IN VETRINA------
        $post=' <div style="max-height:200px; overflow-y:auto;">
                <table class="table  table-forum" style="margin-bottom:0">
                            <tbody class="container_post">
                            </tbody>
         </table></div>';


?>
<!--
<div class="panel panel-blueLight segreti margin-top-10">
    <div class="panel-heading">Scopri i segreti di reteDES!<a class="pull-right close_segreti" href="javascript:$('div.segreti:first').clone().insertAfter('div.segreti:last');"><i class="fa fa-times"></i></a></div>
    <div class="panel-body">Ciao a tutti;<br>
                            <strong>Sabato 2 dicembre</strong>, a Fontaneto (NO) si terrà un incontro TECNICO-CONVIVIALE a tema reteDES, che è poi una cena con una piacevole chiacchierata a seguire.<br>
                            L'idea è quella di fare incontrare chi sta usando il sito, per condividere opinioni, suggerimenti, idee e -perchè no- anche tecniche di gestione del proprio GAS.<br>
                            <br>
                            Per i gas limitrofi è aperto l'ordine "I SEGRETI DI RETEDES" condiviso dal GAS BORGOMANERO, ma se qualcuno volesse venire "da lontano" per partecipare alla serata sarà più che benvenuto.<br>
                            <br>
                            Tutti i dettagli tecnici saranno pubblicati nell'ordine stesso e forse anche <a href="https://www.facebook.com/events/370442436740690/" target="_BLANK">sulla pagina fb</a> di retedes.
                            <span class="pull-right"><i>Mauro.</i></span>
    </div>
</div>
-->
<div class="inbox-nav-bar no-content-padding">
    <h1 class="page-title txt-color-blueDark"><i class="fa fa-fw fa-dashboard"></i> Cruscotto &nbsp;
            <div class="btn-group margin-bottom-10 pull-right">
                <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);">Operazioni comuni&nbsp;&nbsp;&nbsp;<span class="caret"></span></a>
            <ul class="dropdown-menu">
                <li>
                    <a href="#ajax_rd4/user/miacassa.php">Ricarica Credito</a>
                </li>
                <li>
                    <a href="#ajax_rd4/ordini/nuovo.php">Nuovo ordine</a>
                </li>
                <li class="divider"></li>
                <li>
                    <a href="#ajax_rd4/ordini/ordini_preferiti.php">Ordini Preferiti</a>
                </li>
                <li class="divider"></li>
                <li>
                    <a href="#ajax_rd4/gas/gas_bacheca.php">Bacheca del GAS</a>
                </li>
            </ul>
        </div>
    </h1>
</div>
<!--
<div class="well well-lg margin-top-10">
    <h1>Attenzione:</h1>
    <p>reteDES nelle ultime ore ha avuto dei problemi; la maggior parte delle cose sono state risolte, ma soprattutto nella giornata del 3/2/2018 alcuni movimenti di cassa potrebbero non essere stati registrati.</p>
    <p>Avviso tutti gli utenti, ma soprattutto i referenti ordine e i cassieri a prestare particolare attenzione, in modo da minimizzare il disagio che potrebbe esserci.</p>
    <p></p>
    <p>Grazie per la pazienza, Mauro.</p>


</div>-->
<?php echo $post;?>
<?php echo $alert_responsabile_gas;?>
<?php echo $alert_email_multipla;?>
<?php echo $alert_non_ho_cassa;?>
<?php echo $alert_help_from_last_login;?>
<?php echo $alert_users;?>
<?php echo $alert_bounced;?>
<?php echo $alert_bounced_myself;?>
<?php echo $alert_movi;?>
<?php echo $alert_richi;?>
<?php echo $alert_geogas;?>
<?php echo $alert_sogliacassa;?>
<?php echo $alert_ditta_pochi_dati;?>
<?php if(!_USER_NONMOSTRAREHELPHOME){echo $wg_help->print_html();} ?>

<div class="row padding-5 margin-top-10">
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        <div class="well well-sm"><h1>Puoi comprare:</h1><?php echo $oa; ?></div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        <!--<div class="well well-sm"><h1>Tutti gli ordini <small>(<?php echo "<strong>".$tot_ordini."</strong> di cui <strong>".$tot_ordini_a_che_fare."</strong> che mi riguardano"?>)</small></h1><?php echo $r; ?></div>-->
        <div class="well well-sm">
            <h1>Tutti gli ordini</h1><div id="container_ordini_home"><div class="text-align-center"><i class="fa fa-spin fa-spinner fa-2x" style="margin:auto"></i></div></div>
        </div>
    </div>



</div>



<script type="text/javascript">

    //TEST
    //window.history.pushState("object or string", "Title", "/gas4/");

    pageSetUp();

    var pagefunction = function() {

        <?php if(_USER_ADDTOCALENDAR){ ?>
            addtocalendar.load();
        <?php } ?>
        var carica_ordini_home=function( futuri,aperti, chiusi,convalidati,gestisco,partecipo){
            $.ajax({
              type: "POST",
              url: "ajax_rd4/_act_main.php",
              dataType: 'json',
              data: {act: "show_ordini_home", futuri:futuri,aperti:aperti,chiusi:chiusi,convalidati:convalidati,gestisco:gestisco,partecipo:partecipo},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                    //ok(data.msg);

                    $('#container_ordini_home').html(data.html);
                    listFilter( $("#list"));
                }else{
                    ko(data.msg);
                }
            });

        }

        var carica_post=function( gas, id_ordine, utente, id_ditta, page){
            $.ajax({
              type: "POST",
              url: "ajax_rd4/bacheca/_act.php",
              dataType: 'json',
              data: {act: "show_vetrina", page:page, gas:gas, id_ordine:id_ordine, utente:utente, id_ditta:id_ditta, limit:1},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                    //ok(data.msg);
                    //$('.conversation_img[alt="example"]').attr('src')
                    $('.container_post').append(data.post);
                    $( ".messaggio img" ).wrap(function() {
                        return "<a class='swipebox' title='Immagine' href='" + $( this ).attr('src') + "'></a>";
                    });
                    $('.swipebox').swipebox();

                }else{
                    ko(data.msg);
                }
            });

        }


        $(document).on('change','#nonmostrarepiu',function(){
            if(this.checked) {value = "SI";}else{value = "NO";}
            $.ajax({
                      type: "POST",
                      url: "ajax_rd4/_act_main.php",
                      dataType: 'json',
                      data: {act: "nonmostrarepiu", value : value},
                      context: document.body
                    }).done(function(data) {
                        if(data.result=="OK"){
                            ok(data.msg);
                        }else{
                            ko(data.msg);
                        }

                    });
        });
         jQuery.expr[':'].Contains = function(a,i,m){
              return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase())>=0;
          };
        function listFilter(list) { // header is any element, list is an unordered list
            // create and add the filter form to the header
            $('#show_aperti')
              .change( function () {
                  $('#show_programmati').prop("checked", false);
                  $('#show_chiusi').prop("checked", false);
                  $('#show_convalidati').prop("checked", false);
                  $('#show_partecipo').prop("checked", false);
                  var filter;
                  if($("#show_aperti").is(':checked')){
                    filter="APERTO";
                  }else{
                    filter="";
                  }
                  if(filter) {
                    $(list).find("span:not(:Contains(" + filter + "))").parent().hide();
                    $(list).find("span:Contains(" + filter + ")").parent().show();
                } else {
                  $(list).find("li").show();
                }
                return false;
              });
            $('#show_programmati')
              .change( function () {
                  $('#show_aperti').prop("checked", false);
                  $('#show_chiusi').prop("checked", false);
                  $('#show_convalidati').prop("checked", false);
                  $('#show_partecipo').prop("checked", false);
                  var filter;
                  if($("#show_programmati").is(':checked')){
                    filter="APRIRA'";
                  }else{
                    filter="";
                  }
                  if(filter) {
                    $(list).find("span:not(:Contains(" + filter + "))").parent().hide();
                    $(list).find("span:Contains(" + filter + ")").parent().show();
                } else {
                  $(list).find("li").show();
                }
                return false;
              });
            $('#show_chiusi')
                .change( function () {
                  $('#show_programmati').prop("checked", false);
                  $('#show_aperti').prop("checked", false);
                  $('#show_convalidati').prop("checked", false);
                  $('#show_partecipo').prop("checked", false);
                  var filter;
                  if($("#show_chiusi").is(':checked')){
                    filter="CHIUSO";
                  }else{
                    filter="";
                  }
                  if(filter) {
                    $(list).find("span:not(:Contains(" + filter + "))").parent().hide();
                    $(list).find("span:Contains(" + filter + ")").parent().show();
                } else {
                  $(list).find("li").show();
                }
                return false;
              });
            $('#show_convalidati')
              .change( function () {
                  $('#show_programmati').prop("checked", false);
                  $('#show_chiusi').prop("checked", false);
                  $('#show_aperti').prop("checked", false);
                  $('#show_partecipo').prop("checked", false);
                  var filter;
                  if($("#show_convalidati").is(':checked')){
                    filter="CONVALIDATO";
                  }else{
                    filter="";
                  }
                  if(filter) {
                    $(list).find("span:not(:Contains(" + filter + "))").parent().hide();
                    $(list).find("span:Contains(" + filter + ")").parent().show();
                } else {
                  $(list).find("li").show();
                }
                return false;
              });
            $('#show_partecipo')
              .change( function () {
                  $('#show_programmati').prop("checked", false);
                  $('#show_chiusi').prop("checked", false);
                  $('#show_aperti').prop("checked", false);
                  $('#show_convalidati').prop("checked", false);
                  var filter;
                  if($("#show_partecipo").is(':checked')){
                    filter="Partecipante";
                  }else{
                    filter="";
                  }
                  if(filter) {
                    $(list).find("span:not(:Contains(" + filter + "))").parent().hide();
                    $(list).find("span:Contains(" + filter + ")").parent().show();
                } else {
                  $(list).find("li").show();
                }
                return false;
              });
            $('#listfilter')
              .change( function () {
                  $('#show_programmati').prop("checked", false);
                  $('#show_aperti').prop("checked", false);
                  $('#show_convalidati').prop("checked", false);
                  $('#show_chiusi').prop("checked", false);
                  $('#show_partecipo').prop("checked", false);
                  var filter = $(this).val();
                if(filter) {
                  // this finds all links in a list that contain the input,
                  // and hide the ones not containing the input while showing the ones that do
                  $(list).find("span:not(:Contains(" + filter + "))").parent().hide();
                  $(list).find("span:Contains(" + filter + ")").parent().show();
                } else {
                  $(list).find("li").show();
                }
                return false;
              })
            .keyup( function () {
                // fire the above change event after every letter
                $(this).change();
            });
          }


     //listFilter( $("#list"));

     $("#cb_user_usa_cassa_home").click(function(e) {
            e.preventDefault();
            value = "SI";
            $.SmartMessageBox({
                title : "Attenzione !",
                content : 'Se attivi la cassa, confermi di accettare quanto riportato <a href="#ajax_rd4/help/help_cassa.php" target="_BLANK">qui</a>',
                buttons : '[No][Si]'
            }, function(ButtonPressed) {
                if (ButtonPressed === "Si") {
                    console.log("Value: " + value);
                    $.ajax({
                      type: "POST",
                      url: "ajax_rd4/user/_act.php",
                      dataType: 'json',
                      data: {act: "user_usa_cassa", value : value},
                      context: document.body
                    }).done(function(data) {
                        if(data.result=="OK"){
                                ok(data.msg);
                                $('#cassa_alert').fadeOut();
                        }else{ko(data.msg);}
                    });
                }
                if (ButtonPressed === "No") {

                }

            });


            e.preventDefault();
        });

        $(document).on("click",".liked_post", function(e){
            var $t=$(this);
            var id_post=$(this).data("id_post");
            console.log("liked " + id_post);
            $.ajax({
              type: "POST",
              url: "ajax_rd4/bacheca/_act.php",
              dataType: 'json',
              data: {act: "liked_post", id_post:id_post},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                    if(data.preferito=="SI"){
                        $('.icona_liked[data-id_post="'+id_post+'"]').removeClass("fa-star-o").addClass("fa-star");
                    }else{
                        $('.icona_liked[data-id_post="'+id_post+'"]').removeClass("fa-star").addClass("fa-star-o");
                    }
                }else{
                    ko(data.msg);
                }
            });
        })

        console.log("loading post");
        carica_post(<? echo _USER_ID_GAS; ?>,0,0,0,1);
        console.log("loading ordini");
        carica_ordini_home(1,1,1,1,1,1);
    };

    // end pagefunction


    loadScript("js_rd4/plugin/swipebox/jquery.swipebox.min.js", pagefunction);

    

</script>