<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.gas.php");
$ui = new SmartUI;
$converter = new Encryption;

$page_title = "Storico GAS";
$page_id ="gas_storico";

$G = new gas(_USER_ID_GAS);

$sql1 = "SELECT
        Sum(D.qta_arr * D.prz_dett_arr) AS totale_giorno,
        DATE_FORMAT (D.data_inserimento,'%Y/%m/%d') as data
        FROM
        retegas_dettaglio_ordini D
        INNER JOIN maaking_users U on U.userid=D.id_utenti
        WHERE U.id_gas='"._USER_ID_GAS."'
        GROUP BY DATE_FORMAT(D.data_inserimento, '%Y%m%d')
        ORDER BY D.data_inserimento ASC";

$stmt = $db->prepare($sql1);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $row) {
    $somma =  CAST_TO_FLOAT($row["totale_giorno"]);
    $data_gas .= "[ new Date(\"".$row["data"]."\"), ".$somma."],";
}
$data_gas = rtrim($data_gas,",");


?>

<?php echo $G->render_toolbar("STORICO"); ?>

<div class="row padding-10 margin-top-10">
    <div class="well well-sm">
    <form class="" action="/gas4/ajax_rd4/gas/_csv.php" method="POST" id="o1_form">
        <h1>Dettaglio ordini utenti proprio GAS</h1>
        <p>Con questa esportazione avrai una tabella di tutti gli articoli arrivati agli utenti del tuo gas nel periodo selezionato. Puoi filtrarla per data, utente oppure ordine.<br><b>NB:</b> Per i filtri usare l'ID utente e l'ID ordine, che si possono ricavare dalle varie schermate di retedes.<br>In questa esportazione sono inclusi gli articoli appartenenti ad ordini di gas esterni ai quali i componenti del tuo gas hanno partecipato.</p>
        <div class="row padding-10">
            <div class="col-sm-6">
                <div class="form-group">
                    <div class="input-group">
                        <input class="form-control datepicker" data-dateformat="dd/mm/yy" name="from" placeholder="Data inizio" type="text">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <div class="input-group">
                        <input class="form-control datepicker" name="to" data-dateformat="dd/mm/yy" placeholder="Data fine" type="text">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>

            </div>
        </div>
         <div class="row padding-10">
            <div class="col-sm-6">
                <div class="form-group">
                    <div class="input-group">
                        <input class="form-control text"  name="idutente" placeholder="ID utente" type="text">
                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <div class="input-group">
                        <input class="form-control text"  name="idordine" placeholder="ID ordine" type="text">
                        <span class="input-group-addon"><i class="fa fa-shopping-cart"></i></span>
                    </div>
                </div>
            </div>

        </div>
        <div class="row padding-10">
            <div class="col-sm-6">
                <div class="form-group">
                    <div class="input-group">
                        <input class="form-control text"  name="idreferente" placeholder="ID referente" type="text">
                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <div class="input-group">
                        <input class="form-control text"  name="idditta" placeholder="ID ditta" type="text">
                        <span class="input-group-addon"><i class="fa fa-truck"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row padding-10">
            <div class="col-sm-12">
                <label>
                    <input class="checkbox" type="checkbox" name="includi_extra">
                <span>Includi partecipazioni extra-gas</span></label>
            </div>
        </div>

        <input type="hidden" name="do" value="o1_go">
        <div class="pull-right btn-group-sm">
            <input type="submit" class="btn btn-success" id="o1_go" value="visualizza">
            &nbsp;
            <input type="submit" class="btn btn-info" value="Esporta un CSV">
        </div>
        <div class="clearfix"></div>
    </form>
    </div>

    <div class="row margin-top-10">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
        </div>
    </div>

    <div id="box_o1" style="width: 100%;margin-bottom: 15px; overflow-y: hidden; overflow-x: scroll; -ms-overflow-style: -ms-autohiding-scrollbar; border: 1px solid #DDD; -webkit-overflow-scrolling: touch;" class="margin-top-10"></div>
</div>

<script type="text/javascript">

    pageSetUp();

    var pagefunction = function(){
    //------------HELP WIDGET
        document.title = '<?php echo "ReteDES.it :: $page_title";?>';
        <?php echo help_render_js($page_id);?>
        //------------END HELP WIDGET


        $('#o1_go').click(function(e){
            console.log("Click");
            $.blockUI();
            $('#box_o1').html('<div class="text-center">Sto recuperando i dati... <i class="fa fa-spin fa-spinner"></i></div>');
            e.preventDefault();
            $('#o1_form').ajaxSubmit({
                dataType: 'json',
                data: { output: 'vis' },
                success: function(data, status) {
                    $.unblockUI();
                    if(data.result=="OK"){
                        //okWait(data.query);
                        $('#box_o1').html(data.msg);
                        console.log(data.query);
                    }else{
                        ko(data.msg);
                    }
                }
            });

        })


    }
    loadScript("js/plugin/jquery-form/jquery-form.min.js", pagefunction  );
</script>