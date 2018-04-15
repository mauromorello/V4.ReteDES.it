<?php
require_once("inc/init.php");
require_once("../lib_rd4/class.rd4.ordine.php");
require_once("../lib_rd4/class.rd4.cassa.php");

$ui = new SmartUI;

$page_title = "Almanacco";
$page_id="almanacco";

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
                        <var class="atc_description">'.$colge.'  '.$ditta.', '.$row["descrizione_listini"].'</var>
                        <var class="atc_location">'._USER_GAS_NOME.'</var>
                        <var class="atc_organizer">'.$gestore.'</var>
                        
                    </var>
                </span>
                ';
            //$atc="";    
        }else{
            $atc="";
        }
        
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
                
                <span class="pull-right">
                    <a href="#ajax_rd4/ordini/compra.php?id='.$row["id_ordini"].'" class="btn btn-circle btn-xs btn-success"><i class="fa fa-shopping-cart"></i></a>
                </span>
                
            </span> 

        </li>';
}

if($oa<>""){
    $oa ='<ul class="notification-body">'.$oa.'</ul>';
}else{
    $oa ='<p class="alert alert-warning">Non ci sono ordini aperti: <a href="#ajax_rd4/ordini/nuovo.php">aprine</a> uno tu! :)</p>';
}




//ORDINI IO COINVOLTO

            $stmt = $db->prepare("SELECT    O.id_ordini,
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
                                  ORDER BY O.data_apertura DESC;");
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
                    $label='<span class="pull-right txt-color-blue note hidden-xs">PROGRAMMATO</span>';
                    $n_programmati++;
                }
                if($chiusura>$today AND $apertura<$today){
                    $color="text-success";
                    $tooltip="APERTO";
                    $label='<span class="pull-right txt-color-green note hidden-xs">APERTO</span>';
                    $n_aperti++;
                }
                if(($chiusura<$today) and $row["is_printable"]<1 ){
                    $color="text-danger";
                    $tooltip="CHIUSO";
                    $label='<span class="pull-right txt-color-red note hidden-xs">CHIUSO</span>';
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


                if($partecipante<>'' | $supervisore<>'' | $gestoreGAS<>'' | $gestore<>'' | $umile_aiutante<>''){
                    $tot_ordini_a_che_fare++;
                }

                $tot_ordini++;
                $r .= '<li style="border-bottom:1px dotted #ccc;">
                        '.$label.'
                        <i class="fa fa-circle visible-xs pull-left '.$color.'"></i>
                        <span>
                            #'.$row["id_ordini"].' <a href="#ajax_rd4/ordini/ordine.php?id='.$row["id_ordini"].'">'.$row["descrizione_ordini"].'</a><br>
                            <i class="note">'.$partecipante.' '.$euro_partecipante.' '.$gestore.' '.$gestoreGAS.' '.$supervisore.' '.$umile_aiutante.'</i>
                        </span>
                      </li>';

        }

                $r_pre = '  <div>
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
                            <i></i><strong>'.$n_programmati.'</strong> Programmati </label>
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



        $r=$r_pre.$r."</ul>";


        //ALERTS
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


        //NUOVI USERS
        if(_USER_PERMISSIONS & perm::puo_gestire_utenti){
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
                    if(($soglia_gas-$saldo_user)<10){
                    
                        $alert_sogliacassa='<p class="alert alert-warning margin-top-10"><a href="#ajax_rd4/user/miacassa.php" class="pull-left margin-top-5" style="margin-right:10px;" rel="tooltip" data-content="Ricarica credito"><i class="fa fa-2x fa-dollar"></i></a><span class="note">COME UTENTE</span><br>Il saldo della tua cassa (<strong>'._nf($saldo_user).' eu.</strong>) è molto vicino alla soglia minima del tuo GAS (<strong>'._nf(_GAS_CASSA_MIN_LEVEL).'</strong>). Se non ricarichi la cassa non puoi fare acquisti.</p>';
        
                    }
                }
            }
        }
        
        //CASSIERE
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

        //-------------------POST IN VETRINA------
        $post=' <div style="max-height:200px; overflow-y:auto;">
                <table class="table  table-forum" style="margin-bottom:0">
                            <tbody class="container_post">
                            </tbody>
         </table></div>';


?>
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
<?php echo $post;?>
<?php echo $alert_non_ho_cassa;?>
<?php echo $alert_help_from_last_login;?>
<?php echo $alert_users;?>
<?php echo $alert_movi;?>
<?php echo $alert_richi;?>
<?php echo $alert_geogas;?>
<?php echo $alert_sogliacassa;?>
<?php if(!_USER_NONMOSTRAREHELPHOME){echo $wg_help->print_html();} ?>

<div class="row padding-5 margin-top-10">
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        <div class="well well-sm"><h1>Puoi comprare:</h1><?php echo $oa; ?></div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        <div class="well well-sm"><h1>Tutti gli ordini <small>(<?php echo "<strong>".$tot_ordini."</strong> di cui <strong>".$tot_ordini_a_che_fare."</strong> che mi riguardano"?>)</small></h1><?php echo $r; ?></div>
        <!--<h1>Tutti gli ordini 2</h1><div id="container_ordini_home"><h1 class="text-center fa fa-spin fa-spinner"></h1></div>-->
    </div>



</div>



<script type="text/javascript">

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
                    filter="PROGRAMMATO";
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


     listFilter( $("#list"));

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


        carica_post(<? echo _USER_ID_GAS; ?>,0,0,0,1);
        //carica_ordini_home(1,1,1,1,1,1);
    };

    // end pagefunction


    loadScript("js_rd4/plugin/swipebox/jquery.swipebox.min.js", pagefunction);


</script>