<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.gas.php");


$ui = new SmartUI;
$converter = new Encryption;

$page_title = "Ordini GAS";
$page_id ="ordini_1";



$SQL = "select
        date_format(data_apertura, '%Y,%m,%d') as ndate,
        CONCAT_WS(',',CAST(extract(year from data_apertura) AS CHAR)
                     ,CAST(extract(month from data_apertura)-1 AS CHAR) 
                     ,CAST(extract(day from data_apertura) AS CHAR)
                 ) as jdate, 

        count(id_ordini) as ordini
        FROM
                        retegas_ordini
                        
                        Inner Join maaking_users ON retegas_ordini.id_utente = maaking_users.userid
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
    $gasData .="[Date.UTC(".$row["jdate"]."),".$row["ordini"]."],";    
}



?>


<div id="container" style="width:100%; height:400px;"></div>
<section id="widget-grid" class="margin-top-10">
<div id="report"></div>
    <div class="row">
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
                text: 'Ordini aperti nel periodo selezionato: '
            },
            chart: {
                type: 'column'
            },
            xAxis: {
                type: 'datetime',
                ordinal: false,
                events: {
                    afterSetExtremes: function(e) {
                        //console.log(this);
                    
                        var sum = 0,
                            chartOb = this;
                        
                        $.each(myChart.series[0].data,function(i,point){
                           if (typeof point != 'undefined'){
                               if(point.x >= chartOb.min && point.x <= chartOb.max){sum += point.y;}
                               
                           };
                        });
                        
                        myChart.title.update({ text: 'Ordini aperti nel periodo selezionato: ' + sum });
                        
                        
                    }
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
                                [1]
                            ]]
                        },
            series: [
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
