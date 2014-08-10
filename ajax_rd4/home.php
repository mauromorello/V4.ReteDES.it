<?php
require_once("inc/init.php");
$ui = new SmartUI;

$page_title = "Cruscotto";

$h=file_get_contents("help/home.html");

$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>false,
                    "deletebutton"=>true,
                    "colorbutton"=>false);
$wg_help = $ui->create_widget($options);
$wg_help->id = "wg_help_home";
$wg_help->body = array("content" => $h,"class" => "");
$wg_help->header = array(
    "title" => '<h2>Aiuto</h2>',
    "icon" => 'fa fa-question-circle'
);


//-------SALDO
$stmt = $db->prepare("SELECT  (
                    COALESCE((SELECT SUM(importo) FROM retegas_cassa_utenti WHERE id_utente='"._USER_ID."' AND segno='+'),0)
                    -
                    COALESCE((SELECT SUM(importo) FROM retegas_cassa_utenti WHERE id_utente='"._USER_ID."' AND segno='-'),0)
                    )  As risultato");
$stmt->execute();
$row = $stmt->fetch();
$saldo =  (float)round($row["risultato"],2);

$stmt = $db->prepare("SELECT  (
            COALESCE((SELECT SUM(importo) FROM retegas_cassa_utenti WHERE id_utente='"._USER_ID."' AND segno='+' AND registrato='no'),0)
            -
            COALESCE((SELECT SUM(importo) FROM retegas_cassa_utenti WHERE id_utente='"._USER_ID."' AND segno='-' AND registrato='no'),0)
            )  As risultato");
$stmt->execute();
$row = $stmt->fetch();
$saldo_non_conf =  abs((float)round($row["risultato"],2));

if(_GAS_CASSA_VISUALIZZAZIONE_SALDO){
    $saldo +=  $saldo_non_conf;
}





$cassa='<h1>Hai <b><span class="txt-color-blue font-xl">'.$saldo.' €</span></b> disponibili</h1>
        <h3>ma <b><span class="txt-color-red font-lg">'.$saldo_non_conf.' €</span></b> non ancora contabilizzati
        <h6>Per visualizzare i movimenti della tua cassa e prenotare una ricarica clicca <a href="#ajax_rd4/user/miacassa.php">qua</a></h6>';

$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>false,
                    "deletebutton"=>false,
                    "colorbutton"=>true);
$wg_cassa = $ui->create_widget($options);
$wg_cassa->id = "wg_cassa_home";
$wg_cassa->body = array("content" => $cassa,"class" => "");
$wg_cassa->header = array(
    "title" => '<h2>Cassa</h2>',
    "icon" => 'fa fa-euro'
);

$stmt = $db->prepare("SELECT retegas_ordini.id_ordini,
            retegas_ordini.descrizione_ordini,
            retegas_listini.descrizione_listini,
            retegas_listini.id_tipologie,
            retegas_ditte.descrizione_ditte,
            retegas_ordini.data_chiusura,
            retegas_gas.descrizione_gas,
            retegas_referenze.id_gas_referenze,
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

            $color = "<span class=\"label label-danger\">SCADE</span>";
        }else{
            $color = "";
        }

$oa .=' <li>
        <span class="">
            <a href="javascript:void(0);" class="msg">
                <img src="img_rd4/t_'.$row["id_tipologie"].'_240.png" alt="" class="air air-top-left margin-top-5" width="40" height="40">
                <span class="from">'.$row["descrizione_ordini"].' <i class="icon-paperclip">'.$color.'</i></span>
                <time>'.$dd .'<b>'.$hours.'</b> h.</time>
                <span class="subject">'.$row["fullname"].', '.$row["descrizione_gas"].'</span>
                <span class="msg-body">'.$row["descrizione_ditte"].', '.$row["descrizione_listini"].'</span>

            </a>
            <span class="note pull-left margin-top-10">Non hai ancora partecipato.</span>
            <span class="pull-right"><a href="javascript:void(0);" class="btn btn-default btn-xs" rel="popover" data-placement="top" data-original-title="Di questo ordine puoi:" data-content="<div class=\'btn-group-vertical btn-xs\'><button type=\'button\' class=\'btn btn-default\'>Eliminare la tua spesa</button><button type=\'button\' class=\'btn btn-default\'>Contattare il referente</button><button type=\'button\' class=\'btn btn-default\'>Altro</button></div>" data-html="true" aria-describedby="popover'.$row["id_ordini"].'"><i class="fa fa-cog"></i> Opzioni</a></span>

        </span>
        </li>';
}

$oa ='<ul class="notification-body">'.$oa.'</ul>';

$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>false,
                    "deletebutton"=>false,
                    "colorbutton"=>true);
$wg_oa = $ui->create_widget($options);
$wg_oa->id = "wg_ordini_aperti_home";
$wg_oa->body = array("content" => $oa,"class" => "no-padding");
$wg_oa->header = array(
    "title" => '<h2>Ci sono <b>'.$no.'</b> ordini aperti</h2>',
    "icon" => 'fa fa-shopping-cart'
);

?>
<div class="inbox-nav-bar no-content-padding">
    <h1 class="page-title txt-color-blueDark"><i class="fa fa-fw fa-dashboard"></i> Cruscotto &nbsp;</h1>
</div>

<section id="widget-grid" class="margin-top-10">

    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <?php echo $wg_help->print_html(); ?>
        </article>
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <?php echo $wg_cassa->print_html(); ?>
            <?php echo $wg_oa->print_html(); ?>
        </article>

    </div>

</section>


<script type="text/javascript">

    pageSetUp();

    var pagefunction = function() {
        // clears memory even if nothing is in the function
    };

    // end pagefunction

    // run pagefunction on load
    pagefunction();

</script>