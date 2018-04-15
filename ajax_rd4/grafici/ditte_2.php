<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.gas.php");


$ui = new SmartUI;
$converter = new Encryption;

$page_title = "Spesa GAS per ditta";
$page_id ="ditte_2";

$min_euro=CAST_TO_INT($_GET["min_euro"],0);
if($min_euro>0){
    $w_min_euro=' HAVING soldi > :min_euro ';    
}

$id_gas=CAST_TO_INT($_GET["id_gas"],0);
if($id_gas>0){
    $w_id_gas=' AND U.id_gas = :id_gas ';    
}

$id_ditte=CAST_TO_INT($_GET["id_ditte"],0);
if($id_ditte>0){
    $w_id_ditte=' AND D.id_ditte = :id_ditte ';    
}

$from=CAST_TO_STRING($_GET["from"]);
if($from<>""){
    $w_date1 =' AND D1.data_inserimento > :from ';
    $w_date2 =' AND data_chiusura > :from ';    
}else{
    $from=date("d/m/Y", strtotime("-1 months"));
}

$to=CAST_TO_STRING($_GET["to"]);
if($to<>""){
    $w_date1 =' AND D1.data_inserimento < :to ';
    $w_date2 =' AND data_chiusura < :to ';    
}else{
    $to=date("d/m/Y",time());
}


if($from<>"" AND $to<>""){
    $w_date1 =' AND D1.data_inserimento BETWEEN :from AND :to ';
    $w_date2 =' AND data_chiusura BETWEEN :from AND :to ';    
}
        
$sql="SELECT 
            U.id_gas,
            D.id_ditte,
            D.descrizione_ditte,
            G.descrizione_gas,
            COUNT(D.id_ditte) as conto,
            
            (SELECT SUM(D1.prz_dett_arr*D1.qta_arr) as totale FROM retegas_dettaglio_ordini D1
                INNER JOIN maaking_users U1 on U1.userid=D1.id_utenti
                WHERE
                     U1.id_gas=U.id_gas AND
                    D1.id_ditta=D.id_ditte
             ".$w_date1."
             AND D1.qta_arr>0
             AND D1.prz_dett_arr>0
                ) as soldi
            
            
            
                FROM `retegas_ditte` D
        LEFT JOIN retegas_listini L on L.id_ditte=D.id_ditte
        LEFT JOIN retegas_ordini O on O.id_listini=L.id_listini
        LEFT JOIN maaking_users U on U.userid=O.id_utente
        INNER JOIN retegas_gas G on 
        U.id_gas = G.id_gas        
        WHERE 
        G.id_des='"._USER_ID_DES."'
        ".$w_id_gas."
        ".$w_id_ditte."
        ".$w_date2."
        GROUP BY D.id_ditte, U.id_gas
        ".$w_min_euro."
        ORDER BY soldi DESC";        
        
$stmt = $db->prepare($sql);
if($min_euro>0){
    $stmt->bindParam(':min_euro', $min_euro, PDO::PARAM_INT);        
}
if($id_gas>0){
    $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);    
}
if($id_ditte>0){
    $stmt->bindParam(':id_ditte', $id_ditte, PDO::PARAM_INT);    
}
if($from<>""){
    $stmt->bindParam(':from', conv_date_to_db($from), PDO::PARAM_STR);       
}
if($to<>""){
    $stmt->bindParam(':to', conv_date_to_db($to), PDO::PARAM_STR);       
}

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
    $rd_data.="['#".$row["id_gas"]." ".str_replace("'","\'",$row["descrizione_gas"])."','#".$row["id_ditte"]." ".str_replace("'","\'",$row["descrizione_ditte"])."',".CAST_TO_INT($row["soldi"],0)."],";  
}

$rd_data = rtrim($rd_data,",");
?>
<div class="well well-sm">
    <form class="smart_form">
        <h1>Relazioni tra GAS di uno stesso DES e DITTE</h1>
        <p></p>
        <div class="row padding-10">
            <div class="col-sm-6">
                <div class="form-group">
                    <div class="input-group">
                        <input class="form-control datepicker" data-dateformat="dd/mm/yy" name="from" placeholder="Data inizio" type="text" <?php  if($from>0){echo 'value="'.$from.'"';} ?>>
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <div class="input-group">
                        <input class="form-control datepicker" name="to" data-dateformat="dd/mm/yy" placeholder="Data fine" type="text" <?php  if($to>0){echo 'value="'.$to.'"';} ?>>
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>

            </div>
        </div>
         <div class="row padding-10">
            <div class="col-sm-6">
                <div class="form-group">
                    <div class="input-group">
                        <input class="form-control text"  name="id_gas" placeholder="ID Gas" type="text" <?php  if($id_gas>0){echo 'value="'.$id_gas.'"';} ?>>
                        <span class="input-group-addon"><i class="fa fa-home"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <div class="input-group">
                        <input class="form-control text"  name="id_ditte" placeholder="ID ditta" type="text" <?php  if($id_ditte>0){echo 'value="'.$id_ditte.'"';} ?>>
                        <span class="input-group-addon"><i class="fa fa-truck"></i></span>
                    </div>
                </div>
            </div>

        </div>
        <div class="row padding-10">
            <div class="col-sm-6">
                <div class="form-group">
                    <div class="input-group">
                        <input class="form-control text"  name="min_euro" id="min_euro" placeholder="Euro Minimi" type="text" <?php  if($min_euro>0){echo 'value="'.$min_euro.'"';} ?>>
                        <span class="input-group-addon"><i class="fa fa-euro"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">

            </div>
        </div>
        
                
        <div class="pull-right btn-group-sm">
            <input type="submit" class="btn btn-success" id="update_page" value="visualizza">
        </div>
        <div class="clearfix"></div>
    </form>
</div>
<p><?php if(_USER_PERMISSIONS & perm::puo_gestire_retegas){echo $sql;}?></p>
<div id="container" style="width:100%; height:1200px;"></div>
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

    title: {
        text: 'Relazione tra GAS e DITTE'
    },

    series: [{
        keys: ['from', 'to', 'weight'],
        data: [
            /*['Brazil', 'Portugal', 5 ],
            ['Brazil', 'France', 1 ],
            ['Brazil', 'Spain', 1 ],
            ['Brazil', 'England', 1 ],
            ['Canada', 'Portugal', 1 ],*/
            <?php echo $rd_data;?>
        ],
        type: 'sankey',
        name: 'Relazione tra GAS e DITTA'
    }]

});
    /*
    $(document).off("#update_page","click");
    $(document).on("#update_page","click",function(e){
        
        e.preventdefault();
        var min_euro=$("#min_euro").val();
        console.log("min_euro: "+ min_euro);
        loadURL("#ajax_rd4/grafici/ditte_2.php?min_euro="+min_euro);
        
    })
    */
    
    } // end pagefunction

    
    function loadHS(){
        loadScript("https://code.highcharts.com/modules/sankey.js", pagefunction);    
    }
    
    loadScript("https://code.highcharts.com/highcharts.js",loadHS);

</script>
