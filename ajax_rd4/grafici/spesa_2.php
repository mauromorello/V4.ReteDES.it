<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.gas.php");


$ui = new SmartUI;
$converter = new Encryption;

$page_title = "Spesa giornaliera";
$page_id ="spesa_2";


$SQL = "select
        date_format(timestamp_ord, '%Y,%m,%d') as ndate,
        CONCAT_WS(',',CAST(extract(year from timestamp_ord) AS CHAR)
                     ,CAST(extract(month from timestamp_ord)-1 AS CHAR) 
                     ,CAST(extract(day from timestamp_ord) AS CHAR)
                 ) as jdate,
        round(sum(qta_arr*prz_dett_arr)) as euro
        from retegas_dettaglio_ordini
        group by ndate
        order by ndate asc
        ";
$stmt = $db->prepare($SQL);
$stmt->execute();
$rows = $stmt->fetchAll();

$i=0;
foreach($rows as $row){
    //$euro += $row["euro"];
    $RdesData .="[Date.UTC(".$row["jdate"]."),".$row["euro"]."],";    
}
$SQL = "select
        date_format(timestamp_ord, '%Y,%m,%d') as ndate,
        CONCAT_WS(',',CAST(extract(year from timestamp_ord) AS CHAR)
                     ,CAST(extract(month from timestamp_ord)-1 AS CHAR) 
                     ,CAST(extract(day from timestamp_ord) AS CHAR)
                 ) as jdate,
        round(sum(qta_arr*prz_dett_arr)) as euro
        FROM
                        retegas_dettaglio_ordini
                        
                        Inner Join maaking_users ON retegas_dettaglio_ordini.id_utenti = maaking_users.userid
        WHERE
                        maaking_users.id_gas =  '"._USER_ID_GAS."'
        
        group by ndate
        order by ndate asc
        ";
        
$stmt = $db->prepare($SQL);
$stmt->execute();
$rows = $stmt->fetchAll();

$i=0;
foreach($rows as $row){
    //$euroG += $row["euro"];
    $gasData .="[Date.UTC(".$row["jdate"]."),".$row["euro"]."],";    
}
$SQL = "select
        date_format(timestamp_ord, '%Y,%m,%d') as ndate,
        CONCAT_WS(',',CAST(extract(year from timestamp_ord) AS CHAR)
                     ,CAST(extract(month from timestamp_ord)-1 AS CHAR) 
                     ,CAST(extract(day from timestamp_ord) AS CHAR)
                 ) as jdate,
        round(sum(qta_arr*prz_dett_arr)) as euro
        FROM
                        retegas_dettaglio_ordini
                        
                        Inner Join maaking_users ON retegas_dettaglio_ordini.id_utenti = maaking_users.userid
                        Inner Join retegas_gas ON retegas_gas.id_gas = maaking_users.id_gas
        WHERE
                        retegas_gas.id_des =  '"._USER_ID_DES."'
        
        group by ndate
        order by ndate asc
        ";
        
$stmt = $db->prepare($SQL);
$stmt->execute();
$rows = $stmt->fetchAll();

$i=0;
foreach($rows as $row){
    //$euroD += $row["euro"];
    $desData .="[Date.UTC(".$row["jdate"]."),".$row["euro"]."],";    
}


?>


<div id="container" style="width:100%; height:400px;"></div><section id="widget-grid" class="margin-top-10">

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
        
        var myChart = Highcharts.stockChart('container', {
                title: {
                text: 'Spesa Giornaliera'
            },
            chart: {
                
                type: 'column'
            },
            xAxis: {
                type: 'datetime'
            },
            yAxis: {
                title: {
                    text: 'Euro'
                }
            },
            legend: {
                enabled: true,
                align: 'right',
                layout: 'vertical',
                verticalAlign: 'top',
                y: 100
            },
            rangeSelector: {
                selected: 4
            },
            
            dataGrouping: {
                            units: [[
                                'week', // unit name
                                [1] // allowed multiples
                            ], [
                                'month',
                                [1, 2, 3, 4, 6]
                            ]]
                        },

            series: [{
                        name: 'www.ReteDES.it',
                        data: [<?php echo $RdesData; ?>],
                        visible: false,
                        dataGrouping: {
                            groupPixelWidth: 50
                        }
                    },
                    
                    {
                        name: '<?php echo _USER_DES_NOME?>',
                        data: [<?php echo $desData; ?>],
                        visible: false,
                    },
                    {
                        name: '<?php echo _USER_GAS_NOME?>',
                        data: [<?php echo $gasData; ?>],
                        
                    }
            ]            
            })

    } // end pagefunction

    
    function loadHS(){
        loadScript("https://code.highcharts.com/modules/exporting.js", pagefunction);    
    }
    
    loadScript("https://code.highcharts.com/stock/highstock.js",loadHS);

</script>
