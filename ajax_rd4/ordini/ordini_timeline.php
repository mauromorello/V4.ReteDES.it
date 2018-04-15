<?php
require_once("inc/init.php");
$ui = new SmartUI;
$converter = new Encryption;

$page_title = "Scadenziario ordine";
$page_id = "scadenziario_ordine";

//CONTROLLI
$id_ordine = (int)$_GET["id"];
$O = new ordine($id_ordine);

$data_apertura = strtotime($O->data_apertura);
$data_chiusura = strtotime($O->data_chiusura);
$data_creazione = strtotime($O->data_creazione);
$data_oggi = strtotime('now');

if($O->data_distribuzione_start(_USER_ID_GAS)<>"00-00-0000 00:00"){
    $distribuzione='La merce verrà distribuita il '.$O->data_distribuzione_start;
}else{
    $distribuzione='Non vi sono indicazioni sulla data della distribuzione';

}

if($O->data_creazione<>"00-00-0000 00:00"){
    $show_creazione=true;
    if($data_oggi>$data_creazione){
        $creazione_bck =' bg-danger ';
    }else{
        $creazione_bck ='';
    }
}else{
   $show_creazione=false;
}

if($data_oggi>$data_apertura){
    $apertura_bck =' bg-success ';
}else{
    $apertura_bck ='';
}

if(($data_oggi>$data_apertura) AND ($data_oggi<$data_chiusura)){
    $partecipa_bck =' bg-success ';
    $messaggio_ordine_aperto="L'ordine è aperto, vuoi partecipare?";
    $bottone_ordine_aperto='<a href="<?php echo APP_URL; ?>/#ajax_rd4/ordini/compra.php?id='.$O->id_ordini.'" class="btn btn-xs btn-default">Compra qualcosa...</a>';
    $show_partecipa_ordine = true;
}else{
    $partecipa_bck ='';
}

if(($data_oggi>$data_chiusura)){
    $partecipa_bck =' bg-success ';
    $chiusura_bck = ' bg-success ';
    $messaggio_ordine_aperto="L'ordine è chiuso, non puoi più parteciparvi.";
    $bottone_ordine_aperto='';
    $show_partecipa_ordine = false;
}else{
    $partecipa_bck ='';
    $chiusura_bck ='';
}


//DATI GRAFICO VALORI
$sql="SELECT DATE_FORMAT(data_inserimento, '%Y-%m-%d') as period, SUM(prz_dett*qta_arr) as valore FROM retegas_dettaglio_ordini WHERE id_ordine='".$id_ordine."' GROUP BY DATE_FORMAT(data_inserimento, '%Y%m%d%')";
$stmt = $db->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll();
foreach($rows as $row){
    $valore += $row["valore"];
    //{"period": "2012-10-01", "valore": 100},
    $data.='{"period": "'.$row["period"].'", "valore": '.$valore.'},';
}

?>

<?php echo $O->navbar_ordine();?>

<!-- TIMELINE -->
            <style>
            .dimmed {
              position: relative;
            }

            .dimmed:after {
              content: " ";
              z-index: 10;
              display: block;
              position: absolute;
              height: 100%;
              top: 0;
              left: 0;
              right: 0;
              background: rgba(255, 255, 255, 0.65);
            }
            </style>
            
            <!-- Timeline Content -->
            <h1>Sequenza temporale di questo ordine</h1>
            <div class="smart-timeline margin-top-10">
                <ul class="smart-timeline-list">
                    <?php if($show_creazione){ ?>
                    <li class="<?php echo $creazione_bck; ?>">
                        <div class="smart-timeline-icon">
                            <i class="fa fa-star"></i>
                        </div>
                        <div class="smart-timeline-time">
                            <small><?php echo $O->data_creazione; ?></small>
                        </div>
                        <div class="smart-timeline-content">
                            <p>
                                <a href="javascript:void(0);"><strong>Creazione ordine.</strong></a>
                            </p>
                            <p>
                                <?php echo $O->fullname_referente; ?> ha creato l'ordine il <?php echo $O->data_creazione_lunga; ?>
                            </p>
                            <p>
                                Occorre modificare qualcosa?
                            </p>
                            <a href="<?php echo APP_URL; ?>/#ajax_rd4/ordini/edit.php?id=<?php echo $O->id_ordini; ?>" class="btn btn-xs btn-default">Vai al pannello di gestione</a>
                        </div>
                    </li>
                    <?php } ?>

                    <li class="<?php echo $apertura_bck; ?>">
                        <div class="smart-timeline-icon">
                            <i class="fa fa-exclamation"></i>
                        </div>
                        <div class="smart-timeline-time">
                            <small><?php echo $O->data_apertura; ?></small>
                        </div>
                        <div class="smart-timeline-content">
                            <p>
                                <a href="javascript:void(0);"><strong>Apertura ordine.</strong></a>
                            </p>
                            <p>
                                L'ordine è programmato per aprirsi il <?php echo $O->data_apertura_lunga; ?>
                            </p>
                        </div>
                    </li>


                    <?php if($show_partecipa_ordine){ ?>
                        <li class="<?php echo $partecipa_bck; ?>">
                            <div class="smart-timeline-icon">
                                <i class="fa fa-shopping-cart"></i>
                            </div>
                            <div class="smart-timeline-time">

                            </div>
                            <div class="smart-timeline-content">
                                <p>
                                    <a href="javascript:void(0);"><strong>Ordine aperto.</strong></a>
                                </p>
                                <p>
                                    <?php echo $messaggio_ordine_aperto; ?>
                                </p>
                                <?php echo $bottone_ordine_aperto; ?>
                                <div id="year-graph" class="chart no-padding margin-top-10"></div>
                            </div>
                        </li>
                    <?php }?>


                    <li class="<?php echo $chiusura_bck; ?>">
                        <div class="smart-timeline-icon">
                            <i class="fa fa-close"></i>
                        </div>
                        <div class="smart-timeline-time">
                            <small><?php echo $O->data_chiusura;?></small>
                        </div>
                        <div class="smart-timeline-content">
                            <p>
                                <a href="javascript:void(0);"><strong>Chiusura ordine.</strong></a>
                            </p>
                            <p>
                                <?php if($show_partecipa_ordine){ ?>
                                    L'ordine è ancora aperto, stanno comprando <strong><?php echo _NF($O->n_articoli_ordinati($O->id_ordini))?></strong> articoli , per un valore di <strong><?php echo _NF(VA_ORDINE($O->id_ordini))?></strong> Eu.<br>
                                    Stanno partecipando in <strong><?php echo $O->n_utenti_partecipanti($O->id_ordini); ?></strong> famiglie.
                                <?} else {?>
                                    L'ordine si è chiuso, con un totale di <strong><?php echo _NF($O->n_articoli_ordinati($O->id_ordini))?></strong> articoli ordinati, del valore di <strong><?php echo _NF(VA_ORDINE($O->id_ordini))?></strong> Eu.<br>
                                    Hanno partecipato in <strong><?php echo $O->n_utenti_partecipanti($O->id_ordini); ?></strong> famiglie.
                                <? } ?>

                            </p>
                            <p>Cosa ha ordinato la gente del tuo GAS:<br>
                                <a href="<?php echo APP_URL; ?>/#ajax_rd4/reports/utenti_gas.php?id=<?php echo $O->id_ordini; ?>" class="btn btn-xs btn-default">Vedi i partecipanti</a>
                                <a href="<?php echo APP_URL; ?>/#ajax_rd4/reports/note_ordine.php?id=<?php echo $O->id_ordini; ?>" class="btn btn-xs btn-default">Vedi le note degli utenti</a>
                            </p>
                            <p>Se occorre rettificare o aggiungere qualcosa:<br>
                                <a href="<?php echo APP_URL; ?>/#ajax_rd4/rettifiche/start.php?id=<?php echo $O->id_ordini; ?>" class="btn btn-xs btn-default">Vai al pannello rettifiche</a>
                            </p>
                            <p>Contatta i fornitori e manda l'elenco degli articoli:<br>
                                <a href="<?php echo APP_URL; ?>/#ajax_rd4/reports/articoli_raggruppati_multigas.php?id=<?php echo $O->id_ordini; ?>" class="btn btn-xs btn-default">Lista articoli raggruppati ma divisi per gas</a>
                                <a href="<?php echo APP_URL; ?>/#ajax_rd4/reports/articoli_raggruppati.php?id=<?php echo $O->id_ordini; ?>" class="btn btn-xs btn-default">Lista articoli raggruppati</a>
                                

                            </p>
                        </div>
                    </li>

                    <li>
                        <div class="smart-timeline-icon">
                            <i class="fa fa-truck"></i>
                        </div>
                        <div class="smart-timeline-time">
                            <small></small>
                        </div>
                        <div class="smart-timeline-content">
                            <p>
                                <a href="javascript:void(0);"><strong>Finalmente la merce arriva.</strong></a>
                            </p>
                            <p>
                                Occorre rettificare i quantitativi?
                            </p>
                            <a href="<?php echo APP_URL; ?>/#ajax_rd4/rettifiche/start.php?id=<?php echo $O->id_ordini; ?>" class="btn btn-xs btn-default">Vai al pannello rettifiche</a>
                        </div>
                    </li>
                    <li>
                        <div class="smart-timeline-icon">
                            <i class="fa fa-check-circle"></i>
                        </div>
                        <div class="smart-timeline-time">
                            <small></small>
                        </div>
                        <div class="smart-timeline-content">
                            <p>
                                <a href="javascript:void(0);"><strong>Convalida ordine.</strong></a>
                            </p>
                            <p>
                                Se è tutto a posto adesso puoi convalidare l'ordine
                            </p>
                            <a href="<?php echo APP_URL; ?>/#ajax_rd4/ordini/ordini_convalida.php?id=<?php echo $O->id_ordini; ?>" class="btn btn-xs btn-default">Vai al pannello convalida.</a>
                        </div>
                    </li>

                    <li>
                        <div class="smart-timeline-icon">
                            <i class="fa fa-group"></i>
                        </div>
                        <div class="smart-timeline-time">
                            <small><?php echo $O->data_distribuzione_start(_USER_ID_GAS); ?></small>
                        </div>
                        <div class="smart-timeline-content">
                            <p>
                                <a href="javascript:void(0);"><strong>Distribuzione.</strong></a>
                            </p>
                            <p>
                                La merce di questo ordine viene distribuita il <?php echo $O->data_distribuzione_start(_USER_ID_GAS); ?> a <?php echo $O->luogo_distribuzione(_USER_ID_GAS); ?>
                            </p>
                            <a href="<?php echo APP_URL; ?>/#ajax_rd4/ordini/edit_gas.php?id=<?php echo $O->id_ordini; ?>" class="btn btn-xs btn-default">Vai al pannello di gestione distribuzione</a>
                            <a href="<?php echo APP_URL; ?>/#ajax_rd4/reports/distribuzione.php?id=<?php echo $O->id_ordini; ?>" class="btn btn-xs btn-default">Report dettaglio distribuzione</a>


                        </div>
                    </li>
                    <?php if(_USER_GAS_USA_CASSA){ ?>
                        <li>
                            <div class="smart-timeline-icon">
                                <i class="fa fa-arrows-h"></i>
                            </div>
                            <div class="smart-timeline-time">
                                <small></small>
                            </div>
                            <div class="smart-timeline-content">
                                <p>
                                    <a href="javascript:void(0);"><strong>Roba di cassieri...</strong></a>
                                </p>
                                <p>
                                    Se sei un cassiere, devi allineare l'ordine alla cassa.
                                </p>
                                <a href="<?php echo APP_URL; ?>/#ajax_rd4/ordini/cassa.php?id=<?php echo $O->id_ordini; ?>" class="btn btn-xs btn-default">Vai al pannello di gestione</a>
                            </div>
                        </li>
                    <?php }?>

                    <?php if(_USER_GAS_USA_CASSA){ ?>
                    <li>
                        <div class="smart-timeline-icon">
                            <i class="fa fa-bank"></i>
                        </div>
                        <div class="smart-timeline-time">
                            <small></small>
                        </div>
                        <div class="smart-timeline-content">
                            <p>
                                <a href="javascript:void(0);"><strong>Registra i movimenti</strong></a>
                            </p>
                            <p>
                                Se sei un cassiere, segna che i movimenti della cassa sono tutti a posto.
                            </p>
                            <a href="javascript:void(0);" class="btn btn-xs btn-default">Vai alla pagina della registrazione</a>
                        </div>
                    </li>
                    <?php }?>
                </ul>
            </div>
            <!-- END Timeline Content -->
<!-- TIMELINE -->


<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
        </article>

    </div>

</section>
<!-- Dynamic Modal -->
<div class="modal fade" id="remoteModal" tabindex="-1" role="dialog" aria-labelledby="remoteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- content will be filled here from "ajax/modal-content/model-content-1.html" -->
        </div>
    </div>
</div>
                        <!-- /.modal -->

<script type="text/javascript">

    pageSetUp();



    var pagefunction = function(){
        //-------------------------HELP
        <?php echo help_render_js($page_id); ?>
        //-------------------------HELP

        if ($('#year-graph').length){
            var day_data = [
            <?php echo $data ?>
            //  {"period": "2012-10-01", "valore": 100},
            //  {"period": "2012-10-01", "licensed": 3407, "sorned": 660},
            //  {"period": "2012-09-30", "licensed": 3351, "sorned": 629},
            //  {"period": "2012-09-29", "licensed": 3269, "sorned": 618},
            //  {"period": "2012-09-20", "licensed": 3246, "sorned": 661},
            //  {"period": "2012-09-19", "licensed": 3257, "sorned": 667},
            //  {"period": "2012-09-18", "licensed": 3248, "sorned": 627},
             // {"period": "2012-09-17", "licensed": 3171, "sorned": 660},
             // {"period": "2012-09-16", "licensed": 3171, "sorned": 676},
             // {"period": "2012-09-15", "licensed": 3201, "sorned": 656},
             // {"period": "2012-09-10", "licensed": 3215, "sorned": 622}
            ];
            var month = new Array();
            month[0] = "01";
            month[1] = "02";
            month[2] = "03";
            month[3] = "04";
            month[4] = "05";
            month[5] = "06";
            month[6] = "07";
            month[7] = "08";
            month[8] = "09";
            month[9] = "10";
            month[10] = "11";
            month[11] = "12";
            Morris.Line({
              element: 'year-graph',
              data: day_data,
              xkey: 'period',
              ykeys: ['valore'],
              labels: ['Euro'],
              xLabelFormat: function(d) {
                    return d.getDate()+'/'+month[d.getMonth()]+'/'+d.getFullYear();
                    },
              //hoverCallback: function(index, options, content,row) {
              //    var d = options.data[index];
              //    return d.getDate()+'/'+d.getMonth()+'/'+d.getFullYear();
              //}
            })
        }




    } // end pagefunction

    // Load morris dependencies and run pagefunction
    loadScript("js/plugin/morris/raphael.min.js", function(){
        loadScript("js/plugin/morris/morris.min.js", pagefunction);
    });

</script>
