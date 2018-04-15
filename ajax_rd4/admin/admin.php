<?php
require_once("inc/init.php");
require_once('../../lib_rd4/SEPA/SEPASDD.php');
if(!(_USER_PERMISSIONS & perm::puo_gestire_retegas)){die("KO");}

//sAmazon("Mauro","famiglia.morello@gmail.com","Fornara","famiglia.fornara@gmail.com","Oggetto","TEST <b>AMAZON</b>");

//MORRIS DATA
//}, {
//    period : '2010 Q2',
//    iphone : 2778,
//    ipad : 2294,
//    itouch : 2441
//}, {

//USER 2017
$SQL = "SELECT
            date_format(D.regdate,'%u') as settimana,
            COALESCE(COUNT(userid),0) as somma

        FROM maaking_users as D
            WHERE date_format(D.regdate,'%Y')=2017
        GROUP BY
            date_format(D.regdate,'%u');
        ";
$stmt = $db->prepare($SQL);
$stmt->execute();
$users2017 = $stmt->fetchAll();

//USER 2018
$SQL = "SELECT
            date_format(D.regdate,'%u') as settimana,
            COALESCE(COUNT(userid),0) as somma

        FROM maaking_users as D
            WHERE date_format(D.regdate,'%Y')=2018
        GROUP BY
            date_format(D.regdate,'%u');
        ";
$stmt = $db->prepare($SQL);
$stmt->execute();
$users2018 = $stmt->fetchAll();
$i=0;
foreach($users2018 as $row){
    $i++;
    $u .= "{period:'".$row["settimana"]."',
            '2017':".CAST_TO_INT($users2017[$i-1]["somma"],0).",
            '2018':".CAST_TO_INT($row["somma"])."
            },";

}
for($a=$i;$a<53;$a++){
    $u .= "{period:'$a',
            '2017':".CAST_TO_INT($users2017[$a-1]["somma"],0).",
            '2018':0
            },";
}

//EURO 2016
$SQL = "SELECT
            date_format(D.data_inserimento,'%u') as settimana,
            ROUND(SUM(qta_arr*prz_dett_arr)) as somma

        FROM retegas_dettaglio_ordini as D
            WHERE date_format(D.data_inserimento,'%Y')=2016
        GROUP BY
            date_format(D.data_inserimento,'%u');
        ";
$stmt = $db->prepare($SQL);
$stmt->execute();
$rows2016 = $stmt->fetchAll();

//EURO 2017
$SQL = "SELECT
            date_format(D.data_inserimento,'%u') as settimana,
            ROUND(SUM(qta_arr*prz_dett_arr)) as somma

        FROM retegas_dettaglio_ordini as D
            WHERE date_format(D.data_inserimento,'%Y')=2017
        GROUP BY
            date_format(D.data_inserimento,'%u');
        ";
$stmt = $db->prepare($SQL);
$stmt->execute();
$rows2017 = $stmt->fetchAll();

//EURO 2018
$SQL = "SELECT
            date_format(D.data_inserimento,'%u') as settimana,
            ROUND(SUM(qta_arr*prz_dett_arr)) as somma

        FROM retegas_dettaglio_ordini as D
            WHERE date_format(D.data_inserimento,'%Y')=2018
        GROUP BY
            date_format(D.data_inserimento,'%u');
        ";
$stmt = $db->prepare($SQL);
$stmt->execute();
$rows = $stmt->fetchAll();

$i=0;
foreach($rows as $row){
    $i++;
    $d .= "{period:'".$row["settimana"]."',
            '2016':".$rows2016[$i-1]["somma"].",
            '2017':".$rows2017[$i-1]["somma"].",
            '2018':".$row["somma"]."
            },";

}
for($a=$i;$a<54;$a++){
    $d .= "{period:'$a',
            '2016':".$rows2016[$a-1]["somma"].",
            '2017':".$rows2017[$a-1]["somma"].",
            '2018':0
            },";
}

//TEST REFERENTI GAS NON UNIVOCI
$SQL_TEST_REFERENTI_GAS_NON_UNIVOCI='SELECT id_referente_gas, COUNT(id_referente_gas) as conto FROM retegas_gas GROUP BY id_referente_gas HAVING COUNT(id_referente_gas)>1 ORDER BY `conto` DESC ';


//STATISTICHE ECC ECC
$sql="select count(*) as conto from retegas_options WHERE chiave='_SUGGERIMENTO_V4' and valore_int<>1 ;";
$stmt = $db->prepare($sql);
$stmt->execute();
$row = $stmt->fetch();
$suggerimenti_nuovi= $row["conto"];

$sql="select count(*) as conto from retegas_bounced;";
$stmt = $db->prepare($sql);
$stmt->execute();
$row = $stmt->fetch();
$email_bounced= $row["conto"];

$sql="select count(*) as conto from retegas_postino;";
$stmt = $db->prepare($sql);
$stmt->execute();
$row = $stmt->fetch();
$queued_postino= $row["conto"];

$sql="select count(*) as conto from retegas_telegram;";
$stmt = $db->prepare($sql);
$stmt->execute();
$row = $stmt->fetch();
$queued_telegram= $row["conto"];


$sql="select count(*) as conto from maaking_users;";
$stmt = $db->prepare($sql);
$stmt->execute();
$row = $stmt->fetch();
$user_tutti= $row["conto"];

$sql="select count(*) as conto from maaking_users where isactive=1;";
$stmt = $db->prepare($sql);
$stmt->execute();
$row = $stmt->fetch();
$user_attivi= $row["conto"];

$sql="select count(*) as conto from maaking_users where isactive=0;";
$stmt = $db->prepare($sql);
$stmt->execute();
$row = $stmt->fetch();
$user_attesa= $row["conto"];

$sql="select count(*) as conto from maaking_users where isactive=0 AND id_gas=147;";
$stmt = $db->prepare($sql);
$stmt->execute();
$row = $stmt->fetch();
$user_attesa_test= $row["conto"];

$sql="select count(*) as conto from maaking_users where isactive=2;";
$stmt = $db->prepare($sql);
$stmt->execute();
$row = $stmt->fetch();
$user_sospesi= $row["conto"];

$sql="select count(*) as conto from maaking_users where isactive=3;";
$stmt = $db->prepare($sql);
$stmt->execute();
$row = $stmt->fetch();
$user_cancellati= $row["conto"];

$sql="select count(*) as conto from maaking_users where isactive=4;";
$stmt = $db->prepare($sql);
$stmt->execute();
$row = $stmt->fetch();
$user_trasferiti= $row["conto"];

$sql="SELECT COUNT(*) as conto FROM (select count(*) as conto1 from maaking_users group by email having count(email)>1) as pio";
$stmt = $db->prepare($sql);
$stmt->execute();
$row = $stmt->fetch();
$user_mail_doppia= $row["conto"];


//TRIGGERS
$sql="select count(*) as conto from retegas_triggers;";
$stmt = $db->prepare($sql);
$stmt->execute();
$row = $stmt->fetch();
$triggers_attivi= $row["conto"];

// GOOGLED
$sql="select count(*) as conto from maaking_users where google_id IS NOT NULL;";
$stmt = $db->prepare($sql);
$stmt->execute();
$row = $stmt->fetch();
$user_googled= $row["conto"];


//COSE DI TEST


?>
<div class="inbox-nav-bar no-content-padding">

    <h1 class="page-title txt-color-blueDark"><i class="fa fa-fw fa-unlock"></i> Admin &nbsp;
    <span class="pull-right btn-group btn-group-xs btn-toolbar">
        <a class="btn btn-default" href="#ajax_rd4/admin/db_admin.php">DB</a>
        <a class="btn btn-default" href="#ajax_rd4/admin/des_admin.php">DES</a>
        <a class="btn btn-default" href="#ajax_rd4/admin/gas_admin.php">GAS</a>
        <a class="btn btn-default" href="#ajax_rd4/admin/logs.php">LOGS</a>
        <a class="btn btn-default" href="#ajax_rd4/admin/bounces.php">BOUNCES</a>
    </span>
    
    </h1>
        
</div>

<section class="margin-top-10">

    <div class="row">
        <div class="col col-md-12 text-align-center">
            <div style="display: inline-block;">
                <div class="easy-pie-chart txt-color-green" data-percent="<?php echo round(($user_attivi/$user_tutti)*100);?>" data-size="100" data-pie-size="40">ATTIVI <?php echo $user_attivi?></div>
                <div class="easy-pie-chart txt-color-yellow" data-percent="<?php echo round(($user_attesa/$user_tutti)*100);?>" data-size="100" data-pie-size="40">ATTESA <?php echo $user_attesa?></div>
                <div class="easy-pie-chart txt-color-yellow" data-percent="<?php echo round(($user_attesa_test/$user_tutti)*100);?>" data-size="100" data-pie-size="40">ATTESA TEST <?php echo $user_attesa_test?></div>
                <div class="easy-pie-chart txt-color-red" data-percent="<?php echo round(($user_sospesi/$user_tutti)*100);?>" data-size="100" data-pie-size="40">SOSPESI <?php echo $user_sospesi?></div>
                <div class="easy-pie-chart txt-color-red" data-percent="<?php echo round(($user_cancellati/$user_tutti)*100);?>" data-size="100" data-pie-size="40">CANC. <?php echo $user_cancellati?></div>
                <div class="easy-pie-chart txt-color-blue" data-percent="<?php echo round(($user_trasferiti/$user_tutti)*100);?>" data-size="100" data-pie-size="40">TRASF. <?php echo $user_trasferiti?></div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col col-md-12 text-align-center margin-top-10">
            <div style="display: inline-block;" class="margin-top-10">
                <span class="panel panel-green padding-10">SUGGERIMENTI: <strong class="font-lg"><?php echo $suggerimenti_nuovi; ?></strong></span>
                <span class="panel panel-blueLight padding-10">POSTINO: <strong class="font-lg"><?php echo $queued_postino; ?></strong></span>
                <span class="panel panel-blueLight padding-10">TELEGRAM: <strong class="font-lg"><?php echo $queued_telegram; ?></strong></span>
                <span class="panel panel-red padding-10">BOUNCED: <strong class="font-lg txt-color-red"><?php echo $email_bounced; ?></strong></span>
                <span class="panel panel-green padding-10">TRIGGERS: <strong class="font-lg"><?php echo $triggers_attivi; ?></strong></span>
                <span class="panel panel-red padding-10">MAIL 2x: <strong class="font-lg txt-color-red"><?php echo $user_mail_doppia; ?></strong></span>
                <span class="panel panel-green padding-10">GOOGLED: <strong class="font-lg"><?php echo $user_googled; ?></strong></span>
            </div>
        </div>
        
        <!--
        <div class="col col-md-6 margin-top-10">
            <ul>
                <li>APP_URL : <?php echo APP_URL ?></li>
                <li>ASSETS_URL : <?php echo ASSETS_URL ?></li>
                <li>USER_IMG_URL : <?php echo USER_IMG_URL ?></li>  
            </ul>
        </div>
        -->
    </div>
    <div class="row padding-10">
            <h1>Trend settimananle UTENTI</h1>
            <div id="user-graph" class="chart no-padding"></div>
    </div>
    <div class="row padding-10">
            <h1>Trend settimananle EURO</h1>
            <div id="sales-graph" class="chart no-padding"></div>
    </div>
</section>

<div class="well well-sm">
<h1>Test:</h1>
<?php
    echo "_GAS_CASSA_REGISTRA_AUTOMATICO: "._GAS_CASSA_REGISTRA_AUTOMATICO."<hr>";
    //echo SAmazonSMS("+393665750975","Ciao 4");
    //echo $_SERVER['DOCUMENT_ROOT']; 
    //echo $_SERVER['DOCUMENT_ROOT'].'/lib_rd4/class.rd4.user.php';
    //require_once $_SERVER['DOCUMENT_ROOT'].'/gas4/lib_rd4/class.rd4.user.php';
?>

</div>

<script type="text/javascript">

    pageSetUp();

    var pagefunction = function() {
        
        //$('#user_selection').select2();
       // $('#user_selection').on("select2-selected", function(e){$('#userid').html('<h1>'+e.val+'</h1>')});

        // sales graph


            Morris.Bar({
                element : 'sales-graph',
                data : [<?php echo $d;?>],
                xkey : 'period',
                ykeys : ['2016','2017','2018'],
                labels : ['2016','2017','2018'],
                pointSize : 2,
                hideHover : 'auto'
            });
            Morris.Bar({
                element : 'user-graph',
                data : [<?php echo $u;?>],
                xkey : 'period',
                ykeys : ['2017','2018'],
                labels : ['2017','2018'],
                pointSize : 2,
                hideHover : 'auto'
            });
    };



    // end pagefunction


    // Load morris dependencies and run pagefunction
    loadScript("js/plugin/morris/raphael.min.js", function(){
        loadScript("js/plugin/morris/morris.min.js", pagefunction);
    });
</script>