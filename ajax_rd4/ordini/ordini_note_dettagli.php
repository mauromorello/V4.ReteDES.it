<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.ordine.php");
$ui = new SmartUI;
$converter = new Encryption;

$page_title = "Ordini note dettagli";
$page_id = "ordini_note_dettagli";

$id_ordine = CAST_TO_INT($_POST["id"],0);
if ($id_ordine==0){
    $id_ordine = CAST_TO_INT($_GET["id"],0);
}
$O = new ordine($id_ordine);

//ARTICOLI
      $sql = "SELECT U.userid, U.fullname, G.descrizione_gas , D.art_codice, D.art_desc, D.art_um, D.qta_ord, D.qta_arr, D.prz_dett_arr, D.prz_dett, D.id_articoli, D.art_ingombro FROM retegas_dettaglio_ordini D INNER JOIN maaking_users U ON U.userid=D.id_utenti INNER JOIN retegas_gas G ON G.id_gas=U.id_gas WHERE D.id_ordine=:id_ordine AND LEFT(art_codice , 2)<>'@@' AND LEFT(art_codice , 2)<>'##' ORDER BY U.userid ASC";
      
      $stmt = $db->prepare($sql);
      $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
      $stmt->execute();
      $rows = $stmt->fetchAll();


    $i=0;

    foreach($rows as $row){
        
        $h.= '<tr>';
        $h.= '<td><strong>'.$row["fullname"].'</strong> ('.$row["descrizione_gas"].')</td>';
        $h.= '<td>'.$row["art_codice"].' '.$row["art_desc"].'</td>';
        $h.= '<td>'._nf($row["qta_ord"]).'/'._nf($row["qta_arr"]).'</td>';
        $h.= '<td>'._nf($row["prz_dett"]).'/'._nf($row["prz_dett_arr"]).'</td>';
        $h.= '</tr>';
        
        //NOTA ARTICOLO
            $sql = "SELECT * FROM retegas_options
                    WHERE
                    id_articolo=:id_articolo AND
                    id_user=:id_user AND
                    chiave='_NOTE_DETTAGLIO' AND
                    id_ordine=:id_ordine";
            $stmtN = $db->prepare($sql);
            $stmtN->bindParam(':id_articolo', $row["id_articoli"], PDO::PARAM_INT);
            $stmtN->bindParam(':id_ordine', $O->id_ordini, PDO::PARAM_INT);
            $stmtN->bindParam(':id_user', $row["userid"], PDO::PARAM_INT);
            $stmtN->execute();
            $rowN = $stmtN->fetch();
            $nota = trim(CAST_TO_STRING($rowN["valore_text"]));

            $h.= '<tr class="tablesorter-childRow">';
            $h.= '<td COLSPAN=4><span class="text-muted">Note</span> : <span class="nota" data-pk="'.$rowN["id_option"].'" data-id_articolo="'.$row["id_articoli"].'" data-id_utente="'.$row["userid"].'">'.$nota.'</span></td>';
            $h.= '</tr>';
            
            //NOTA ARTICOLO
        
        
        
        $h.= '<tr class="tablesorter-childRow">';
        $h.= '<td COLSPAN=4></td>';
        $h.= '</tr>';    

    }

?>
<?php echo $O->navbar_ordine(); ?>
<h1>Note dettaglio ordini</h1>
            <table id="tabella_note">
                <thead>
                    <tr>
                        <th>Nome - GAS</th>
                        <th>Articolo - Descrizione</th>
                        <th>Qta</th>
                        <th>Prezzo</th>
                    </tr>
                </thead>
                <tbody>
                <?php echo $h; ?>
                </tbody>
            </table>
<section  class="margin-top-10">
    <div class="row">
       
        <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
        </article>

        </div>
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

        loadScript("js_rd4/plugin/tablesorter/js/jquery.tablesorter.widgets.js",loadMath);
        
        function loadMath(){
            loadScript("js/plugin/x-editable/x-editable.min.js", startTable);
        }     

        function startTable(){
                // clears memory even if nothing is in the function
                 $("#tabella_note")
                    .bind("updateComplete",function(e, table) {
                        console.log("updated");
                    });


                $.extend($.tablesorter.themes.bootstrap, {
                    table      : 'table table-bordered',
                    caption    : 'caption',
                    sortNone   : 'bootstrap-icon-unsorted',
                    sortAsc    : 'fa fa-arrow-up',
                    sortDesc   : 'fa fa-arrow-down'

                  });

                
                t = $('#tabella_note').tablesorter({
                    theme: 'bootstrap',
                        //debug:true,
                        widgets: ["uitheme","filter","zebra"],
                     widgetOptions : {
                         zebra : ["even", "odd"],
                         group_collapsible : false,
                         group_collapsed   : false,
                         group_count       : false,
                         filter_childRows  : false
                        }
                });

                $xeditable = $('.nota').editable({
                            url: 'ajax_rd4/ordini/_act.php',
                            type: 'textarea',
                            name: 'edit_nota_dettaglio',
                            title: 'Inserisci nuova nota',
                            params: function (params) {
                                params.id_ordine = <?php echo $O->id_ordini?>;
                                params.id_articolo = $(this).attr('data-id_articolo');
                                params.id_utente = $(this).attr('data-id_utente');            
                                params.pk = $(this).attr('data-pk');
                                return params;
                            }, 
                            //params:  {
                            //    'id_ordine': '',
                            //    'id_articolo' :  
                            //},
                            ajaxOptions: {
                                dataType: 'json'
                            },
                            success: function(data, newValue) {
                                
                                if(data.result=="OK") {
                                    console.log(data.pk);
                                    if(data.pk !== null){
                                        $(this).attr("data-pk", data.pk);
                                    } 
                                    return;
                                }else{
                                     return data.msg;
                                }
                            }
                });
                
                
        }//END STARTTABLE

    } // end pagefunction



    loadScript("js_rd4/plugin/tablesorter/js/jquery.tablesorter.min.js", pagefunction);
</script>
