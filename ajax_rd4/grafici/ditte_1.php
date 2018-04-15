<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.gas.php");


$ui = new SmartUI;
$converter = new Encryption;

$page_title = "Spesa per ditta";
$page_id ="ditte_1";
/*
SELECT 
            U.id_gas,
            D.id_ditte,
            D.descrizione_ditte,
            COUNT(D.id_ditte) as conto,
            
            (SELECT SUM(D1.prz_dett_arr*D1.qta_arr) as totale FROM retegas_dettaglio_ordini D1
                INNER JOIN maaking_users U1 on U1.userid=D1.id_utenti
                WHERE
                     U1.id_gas=U.id_gas AND
                    D1.id_ditta=D.id_ditte
                ) as soldi
            
            
            
                FROM `retegas_ditte` D
        LEFT JOIN retegas_listini L on L.id_ditte=D.id_ditte
        LEFT JOIN retegas_ordini O on O.id_listini=L.id_listini
        LEFT JOIN maaking_users U on U.userid=O.id_utente
        WHERE U.id_gas=1
        GROUP BY D.id_ditte, U.id_gas
        HAVING soldi > 1000
        ORDER BY soldi DESC


*/

        
$sql="  SELECT 
            D.*, 
            COUNT(D.id_ditte) as conto 
        
        FROM `retegas_ditte` D
        LEFT JOIN retegas_listini L on L.id_ditte=D.id_ditte
        LEFT JOIN retegas_ordini O on O.id_listini=L.id_listini
        LEFT JOIN maaking_users U on U.userid=O.id_utente
        WHERE U.id_gas='"._USER_ID_GAS."'
        GROUP BY D.id_ditte
        ORDER BY COUNT(D.id_ditte) DESC
        LIMIT 10;";        
        
$stmt = $db->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll();


/*
SELECT 
            D.*, 
            COUNT(D.id_ditte) as conto,
            (SELECT SUM(DG.prz_dett_arr*DG.qta_arr) as totale FROM retegas_dettaglio_ordini DG
                INNER JOIN maaking_users UG on UG.userid=DG.id_utenti
                WHERE 
                    DG.id_ditta=D.id_ditte
                AND UG.id_gas='1') as totale_gas,
            (SELECT SUM(DD.prz_dett_arr*DD.qta_arr) as totale FROM retegas_dettaglio_ordini DD
                INNER JOIN maaking_users UD on UD.userid=DD.id_utenti
                 INNER JOIN retegas_gas GD on GD.id_gas=UD.id_gas
                WHERE 
                    DD.id_ditta=D.id_ditte
                AND GD.id_des='1') as totale_des,
            (SELECT SUM(DR.prz_dett_arr*DR.qta_arr) as totale FROM retegas_dettaglio_ordini DR
                
                WHERE 
                    DR.id_ditta=D.id_ditte) as totale_retedes
        FROM `retegas_ditte` D
        LEFT JOIN retegas_listini L on L.id_ditte=D.id_ditte
        LEFT JOIN retegas_ordini O on O.id_listini=L.id_listini
        LEFT JOIN maaking_users U on U.userid=O.id_utente
        WHERE U.id_gas='1'
        GROUP BY D.id_ditte
        ORDER BY COUNT(D.id_ditte) DESC;


*/

foreach($rows as $row){
    
    
    //PER OGNI DITTA TOTALE GAS
    $sql_gas ="SELECT SUM(D.prz_dett_arr*D.qta_arr) as totale FROM retegas_dettaglio_ordini D
                INNER JOIN maaking_users U on U.userid=D.id_utenti
                WHERE 
                    D.id_ditta='".$row["id_ditte"]."'
                AND U.id_gas='"._USER_ID_GAS."';";
                
                $stmt = $db->prepare($sql_gas);
                $stmt->execute();
                $row_gas = $stmt->fetch();
                $totale_gas .= CAST_TO_INT($row_gas["totale"]).", ";
                //$totale_gas .= "1, ";
    //TOTALE DES
    $sql_des ="SELECT SUM(D.prz_dett_arr*D.qta_arr) as totale FROM retegas_dettaglio_ordini D
                INNER JOIN maaking_users U on U.userid=D.id_utenti
                INNER JOIN retegas_gas G on G.id_gas=U.id_gas
                WHERE 
                    D.id_ditta='".$row["id_ditte"]."'
                AND G.id_des='"._USER_ID_DES."';";
                
                $stmt = $db->prepare($sql_des);
                $stmt->execute();
                $row_des = $stmt->fetch();
                $totale_des .= CAST_TO_INT($row_des["totale"]).", ";
    
    //TOTALE RETEDES 
    $sql_retedes ="SELECT SUM(D.prz_dett_arr*D.qta_arr) as totale FROM retegas_dettaglio_ordini D
                INNER JOIN maaking_users U on U.userid=D.id_utenti
                WHERE 
                    D.id_ditta='".$row["id_ditte"]."';";
                    
                $stmt = $db->prepare($sql_retedes);
                $stmt->execute();
                $row_retedes = $stmt->fetch();
                $totale_retedes .= CAST_TO_INT($row_retedes["totale"]).", ";
    
    //METODO 2
    $sql_retedes ="SELECT SUM(D.prz_dett_arr*D.qta_arr) as totale FROM retegas_dettaglio_ordini D
                INNER JOIN maaking_users U on U.userid=D.id_utenti
                WHERE 
                    D.id_ditta='".$row["id_ditte"]."';";
                    
                $stmt = $db->prepare($sql_retedes);
                $stmt->execute();
                $row_retedes = $stmt->fetch();
    
    
    $lista_ditte .= "'#".$row["id_ditte"]." ".str_replace("'","\'",$row["descrizione_ditte"])." (".$row["conto"].")', ";
        
    }


?>


<div id="container" style="width:100%; height:600px;"></div>
    <section id="widget-grid" class="margin-top-10">

    <div class="row">
        <!-- PRIMA COLONNA
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <div class="well well-sm"><?php if(_USER_PERMISSIONS & perm::puo_gestire_utenti){echo $nu;} ?></div>

        </article>-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
        </article>
    </div>

</section>
<script type="text/javascript">

    pageSetUp();



    var pagefunction = function(){

        //------------HELP WIDGET
        document.title = '<?php echo "ReteDES.it :: $page_title";?>';
        <?php echo help_render_js($page_id);?>
        //------------END HELP WIDGET

        Highcharts.setOptions({
            lang: {
                months: ['Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno',  'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre'],
                shortMonths: ['Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu',  'Lug', 'Ago', 'Set', 'Ott', 'Nov', 'Dic'],
                weekdays: ['Domenica', 'Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerd^', 'Sabato'],
                shortWeekdays: ['DO', 'LU', 'MA', 'ME', 'GI', 'VE', 'SA']
            }
        });
        
        Highcharts.chart('container', {
            chart: {
                type: 'bar'
            },
            title: {
                text: 'Somma degli ordini raggruppata per ditta'
            },
            subtitle: {
                text: 'NB: sono escluse le rettifiche.'
            },
            xAxis: {
                categories: [<?php echo $lista_ditte;?>],
                title: {
                    text: null
                },
                min: 0,
                max: 8
            },
            scrollbar :{
                enabled: true       
            },
            yAxis: {
                
                labels: {
                    overflow: 'justify'
                }
            },
            tooltip: {
                valueSuffix: ' Eu.'
            },
            plotOptions: {
                bar: {
                    dataLabels: {
                        enabled: true
                    }
                }
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'top',
                x: -40,
                y: 80,
                floating: true,
                borderWidth: 1,
            },
            credits: {
                enabled: false
            },
            series: [{
                name: '<?php echo _USER_GAS_NOME?>',
                data: [<?php echo $totale_gas?>]
            }, {
                name: '<?php echo _USER_DES_NOME?>',
                data: [<?php echo $totale_des?>],
                visible: false
            }, {
                name: 'Tutta retedes.it',
                data: [<?php echo $totale_retedes?>],
                visible: false
            }]
        });

    } // end pagefunction

    
    function loadHS(){
        loadScript("https://code.highcharts.com/modules/exporting.js", pagefunction);    
    }
    
    loadScript("https://code.highcharts.com/stock/highstock.js",loadHS);

</script>
