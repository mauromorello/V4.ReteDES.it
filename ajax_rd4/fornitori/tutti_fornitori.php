<?php
require_once("inc/init.php");

$ui = new SmartUI;
$page_title = "Tutte le ditte";


$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>true,
                    "deletebutton"=>false,
                    "colorbutton"=>true);
$stmt = $db->prepare("SELECT * FROM retegas_ditte");
$stmt->execute();
$rows = $stmt->fetchAll();


$d = '  <div class="table-responsive">
        <table id="table_ditte" class="table table-striped table-bordered table-hover responsive" width="100%">
            <thead>
                <tr>
                    <th data-class="expand"><i class="fa fa-truck"></i>&nbsp;&nbsp;Ditta</th>
                    <th>Tel.</th>
                    <th>Mail</th>
                    <th data-hide="phone,tablet">indirizzo</th>
                    <th data-hide="phone,tablet">tags</th>
                    <th>listini</th>
               </tr>
            </thead>
            </tbody>';
foreach($rows as $row){

            $class_tr='';

            if($row["ditte_gc_lat"]>0){
                $geo='<i class="fa fa-map-marker txt-color-green"></i>';
            }else{
                $geo='<i class="fa fa-map-marker txt-color-red" rel="tooltip" data-original-title="Indirizzo non riconosciuto"></i>';
            }

            if($row["id_proponente"]==_USER_ID){
                $class_tr=' success ';
            }else{
                $class_tr='';
            }

            $stmt = $db->prepare("SELECT count(*) as conto FROM retegas_listini WHERE id_ditte='".$row["id_ditte"]."' AND is_privato=0 AND tipo_listino=0 AND data_valido<NOW();");
            $stmt->execute();
            $listini = $stmt->fetch();
            if($listini["conto"]>0){
                $conto = $listini["conto"];
            }else{
                $conto = '<span class="text-danger"><b>'.$listini["conto"].'</b></span>';
                $class_tr=' danger ';
            }




            $d.='<tr class="'.$class_tr.'">
                    <td><a href="#ajax_rd4/fornitori/scheda.php?id='.$row["id_ditte"].'">'.$row["descrizione_ditte"].'</a><br><span class="font-xs">'.$row["fullname"].'</span></td>
                    <td><a href="tel:'.$row["telefono"].'" class="visible-xs">'.$row["telefono"].'</a><span class="hidden-xs">'.$row["telefono"].'</span></td>
                    <td>'.$row["mail_ditte"].'</td>
                    <td>'.$geo.'&nbsp;&nbsp;'.$row["indirizzo"].'</td>
                    <td>'.$row["tag_ditte"].'</td>
                    <td>'.$conto.'</td>
            </tr>';
}

$d.= '</tbody></table></div>';

$wg_ditte= $ui->create_widget($options);
$wg_ditte->id = "wg_tutte_le_ditte";
$wg_ditte->body = array("content" => $d ,"class" => "no-padding");
$wg_ditte->header = array(
    "title" => '<h2>Tutte le ditte</h2>',
    "icon" => 'fa fa-bar-truck'
);

if(_USER_PERMISSIONS & perm::puo_creare_ditte){
    $button[] = '<form style="margin-right:10px;"><button  class="aggiungi_ditta btn btn-default btn-success navbar-btn"><i class="fa fa-plus"></i> Nuova ditta</button></form>';
}

?>
<?php echo navbar('<i class="fa fa-2x fa-truck pull-left"></i> Tutte le  ditte<br>',$button); ?>

<section id="widget-grid" class="margin-top-10">

    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html("tutte_le_ditte",$page_title); ?>
            <?php echo $wg_ditte->print_html(); ?>
        </article>

    </div>

</section>


<script type="text/javascript">

    pageSetUp();








    var pagefunction = function() {
        //-------------------------HELP
        <?php echo help_render_js("tutte_le_ditte"); ?>
        //-------------------------HELP

        var table= $('#table_ditte').dataTable({
            "autoWidth" : true,
            responsive : true,
            language: {
                "sEmptyTable":     "Nessun dato presente nella tabella",
                "sInfo":           "Vista da _START_ a _END_ di _TOTAL_ elementi",
                "sInfoEmpty":      "Vista da 0 a 0 di 0 elementi",
                "sInfoFiltered":   "(filtrati da _MAX_ elementi totali)",
                "sInfoPostFix":    "",
                "sInfoThousands":  ",",
                "sLengthMenu":     "Visualizza _MENU_ elementi",
                "sLoadingRecords": "Caricamento...",
                "sProcessing":     "Elaborazione...",
                "sHideColumn":     "Mostra / Nascondi colonna",
                "sZeroRecords":    "La ricerca non ha portato alcun risultato.",
                "oPaginate": {
                    "sFirst":      "Inizio",
                    "sPrevious":   "Precedente",
                    "sNext":       "Successivo",
                    "sLast":       "Fine"
                },
                "oAria": {
                    "sSortAscending":  ": attiva per ordinare la colonna in ordine crescente",
                    "sSortDescending": ": attiva per ordinare la colonna in ordine decrescente"
                }
            }
            });


    };

    // end pagefunction

    // run pagefunction on load

   loadScript("js/plugin/datatables/jquery.dataTables.min.js", function(){
        loadScript("js/plugin/datatables/dataTables.colVis.min.js", function(){
            loadScript("js/plugin/datatables/dataTables.tableTools.min.js", function(){
                loadScript("js/plugin/datatables/dataTables.bootstrap.min.js", function(){
                    loadScript("js/plugin/datatable-responsive/datatables.responsive.min.js", pagefunction)
                });
            });
        });
    });


</script>
