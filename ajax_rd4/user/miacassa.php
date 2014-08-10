<?php
require_once("inc/init.php");
$ui = new SmartUI;

$page_title = "Mia Cassa";

$h=file_get_contents("help/cassa.html");

$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>false,
                    "deletebutton"=>true,
                    "colorbutton"=>false);
$wg_help = $ui->create_widget($options);
$wg_help->id = "wg_help_miacassa";
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
        <h3>ma <span class="txt-color-red font-lg">'.$saldo_non_conf.' €</span> non ancora contabilizzati</h3';

$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>false,
                    "deletebutton"=>false,
                    "colorbutton"=>true);
$wg_cassa = $ui->create_widget($options);
$wg_cassa->id = "wg_cassa_mia_cassa";
$wg_cassa->body = array("content" => $cassa,"class" => "");
$wg_cassa->header = array(
    "title" => '<h2>Saldo</h2>',
    "icon" => 'fa fa-euro'
);




$stmt = $db->prepare("SELECT * FROM retegas_options WHERE chiave='PREN_MOV_CASSA' AND id_user='"._USER_ID."'");
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$m="";
foreach ($rows as $row) {
    $m .="<div class=\"well well-sm margin-top-5 pren_box\">".conv_datetime_from_db($row["timbro"])." <span class=\"badge bg-color-greenLight font-md\">&euro; ".round($row["valore_real"],2)."</span> ".$row["valore_text"]." - ".$row["note_1"]."; <button class=\"btn btn-danger btn-xs elimina_mov pull-right\" rel=\"".$row["id_option"]."\"><i class=\"fa fa-trash-o\"></i> <span class=\"hidden-xs\">Elimina</span></button><div class=\"clearfix\"></div></div>";
}
if($m<>""){
    $m = '<h3>Hai in attesa queste prenotazioni:</h3>'.$m;

}else{
    $m = '<h3>Non hai nessuna prenotazione in attesa;</h3>';
}

$preno= '<div id="lista_prenotazioni">'.$m.'</div>
            <div class="panel-group smart-accordion-default margin-top-10" id="accordion-2">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion-2" href="#collapseOne-1" class=""> <i class="fa fa-fw fa-minus-circle txt-color-red"></i> <i class="fa fa-fw fa-plus-circle txt-color-green"></i> Clicca qua per inserire una nuova prenotazione</a></h4>
                                </div>
                                <div id="collapseOne-1" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <form action="ajax_rd4/user/_act.php" class="smart-form" id="anticipa-form" novalidate="novalidate">
                                            <fieldset>
                                                <section>
                                                                <label class="label">Messaggio ai cassieri</label>
                                                                <label class="input">
                                                                    <input type="text" maxlength="100" name="note">
                                                                </label>
                                                                <div class="note">
                                                                    Ad esempio : "Ti ho fatto un bonifico in data odierna"
                                                                </div>
                                                </section>
                                                <section>
                                                                <label class="label">Documento</label>
                                                                <label class="input">
                                                                    <input type="text" maxlength="30" name="documento">
                                                                </label>
                                                                <div class="note">
                                                                    Ad esempio il numero del bonifico
                                                                </div>
                                                </section>
                                                <section>
                                                                <label class="label">Quanti Euro ?</label>
                                                                <label class="input">
                                                                    <input type="numeric" class="input-lg font-xl" name="euro">
                                                                </label>
                                                </section>
                                                <input  type="hidden" name="act" value="ricarica">
                                                <input class="btn btn-lg btn-primary pull-right" type="submit" value="Invia la richiesta">
                                            </fieldset>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>






           ';

$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>false,
                    "deletebutton"=>false,
                    "colorbutton"=>true);
$wg_preno = $ui->create_widget($options);
$wg_preno->id = "wg_cassa_prenotazioni_attive";
$wg_preno->body = array("content" => $preno,"class" => "");
$wg_preno->header = array(
    "title" => '<h2>Prenotazione carichi</h2>',
    "icon" => 'fa fa-pencil-square-o'
);




//MOVIMENTI CASSA
$stmt = $db->prepare("SELECT C.*, O.descrizione_ordini,U.fullname FROM retegas_cassa_utenti C left join retegas_ordini O on O.id_ordini=C.id_ordine left join maaking_users U on U.userid=C.id_cassiere WHERE C.id_utente='"._USER_ID."'  ORDER BY C.id_cassa_utenti DESC;");
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$h ='<table id="dt_miacassa" class="table table-striped" width="100%">
        <thead>
         <tr>

        <th >#</th>
        <th data-hide="phone,tablet">Data</th>
        <th data-hide="phone,tablet">Tipo</th>
        <th>Credito</th>
        <th>Debito</th>
        <th data-class="expand">Descrizione</th>
        <th data-hide="phone">Ordine</th>
        <th data-hide="phone">Cassiere</th>
        <th data-hide="phone">REG</th>
        <th data-hide="phone">CON</th>
        </tr>
        </thead>';

foreach ($rows as $row) {
         $id_op = $row["id_cassa_utenti"];
         $data_op = conv_datetime_from_db($row["data_movimento"]);
         $tipo_op = $__movcas[$row["tipo_movimento"]];
         if($row["segno"]=="+"){
             $credito_op = round($row["importo"],2);
             $debito_op = "&nbsp";

         }else{
             $debito_op = round($row["importo"],2);
             $credito_op = "&nbsp";
         }
         $descrizione_op = $row["descrizione_movimento"];
         if($row["id_ordine"]<>0){
            $ordine_op = $row["id_ordine"] . " ".$row["descrizione_ordini"];
         }else{
            $ordine_op = null;
         }

         if($row["registrato"]=="si"){
             $REG = conv_datetime_from_db($row["data_registrato"]);

         }else{
             $REG = "";

         }
         if($row["contabilizzato"]=="si"){
             $CON = conv_datetime_from_db($row["data_contabilizzato"]);

         }else{
             $CON = "";
         }

         $h.= "
            <tr>

            <td>$id_op</td>
            <td>$data_op</td>
            <td>$tipo_op</td>
            <td>$credito_op</td>
            <td>$debito_op</td>
            <td>$descrizione_op</td>
            <td>$ordine_op</td>
            <td>".$row["fullname"]."</td>
            <td>$REG</td>
            <td>$CON</td>
            </tr>";

}
$h.= "
         </tbody>
         </table>";
$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>true,
                    "deletebutton"=>false,
                    "colorbutton"=>true);
$wg_mov = $ui->create_widget($options);
$wg_mov->id = "wg_movimenti_cassa";
$wg_mov->body = array("content" => $h,"class" => "no-padding");
$wg_mov->header = array(
    "title" => '<h2>Movimenti</h2>',
    "icon" => 'fa fa-exchange'
);

?>
<div class="inbox-nav-bar no-content-padding">
    <h1 class="page-title txt-color-blueDark"><i class="fa fa-fw fa-euro"></i> La mia Cassa &nbsp;</h1>
</div>

<section id="widget-grid" class="margin-top-10">

    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <?php echo $wg_help->print_html(); ?>
            <?php echo $wg_cassa->print_html(); ?>
        </article>
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <?php echo $wg_preno->print_html(); ?>
        </article>

    </div>
    <hr>
    <div class="row">
     <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo $wg_mov->print_html(); ?>
        </article>
    </div>
</section>


<script type="text/javascript">

    pageSetUp();

    var pagefunction = function(){

         $(document.body).on('click', '.elimina_mov', function(){
             var id_prenotazione = $(this).attr('rel');
             var box = $(this).closest('.pren_box');

             console.log("Prenotazione " + id_prenotazione);
             $.SmartMessageBox({
                    title : "Vuoi cancellare questa prenotazione ?",
                    content : "",
                    buttons : "[NO][SI]"

                        }, function(ButtonPress, Value) {

                            if(ButtonPress=="SI"){


                                $.ajax({
                                      type: "POST",
                                      url: "ajax_rd4/user/_act.php",
                                      dataType: 'json',
                                      data: {act: "delete_prenotazione", id_prenotazione : id_prenotazione},
                                      context: document.body
                                    }).done(function(data) {
                                        if(data.result=="OK"){
                                                ok(data.msg);
                                                box.remove();

                                        }else{ko(data.msg);}

                                    });
                            }
                        });


         });

         var $anticipaForm = $('#anticipa-form').validate({
        // Rules for form validation
            rules : {
                note : {
                    required : true
                },
                documento : {
                    required : false
                },
                euro : {
                    required : true
                }
            },

            // Messages for form validation
            messages : {

                note : {
                    required : 'Al cassiere serve una indicazione su questa ricarica.'
                },
                documento : {

                },
                euro : {
                    required : 'Devi dire a quanti euro ammonta la ricarica.'
                }
            },

            // Ajax form submition
            submitHandler : function(form) {

                $(form).ajaxSubmit({
                    type:"POST",
                    dataType: 'json',
                    success : function(data) {
                                //$("#checkout-form").addClass('submited');
                                if(data.result=="OK"){
                                    ok(data.msg);

                                    $("#lista_prenotazioni").append(data.html);
                                }else{
                                    ko(data.msg);
                                }
                            }
                });
            },

            // Do not change code below
            errorPlacement : function(error, element) {
                error.insertAfter(element.parent());
            }
        });



        var responsiveHelper_dt_basic = undefined;
        var responsiveHelper_datatable_fixed_column = undefined;
        var responsiveHelper_datatable_col_reorder = undefined;
        var responsiveHelper_datatable_tabletools = undefined;

        var breakpointDefinition = {
            tablet : 1024,
            phone : 480
        };
        var table= $('#dt_miacassa').dataTable({

            "sDom": "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-6 hidden-xs'T C>r>"+
                    "t"+
                    "<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-sm-6 col-xs-12'p>>",


            "oTableTools": {
                 "aButtons": [
                 "copy",
                 "csv",
                 "xls",
                    {
                        "sExtends": "pdf",
                        "sTitle": "SmartAdmin_PDF",
                        "sPdfMessage": "SmartAdmin PDF Export",
                        "sPdfSize": "letter"
                    },
                     {
                        "sExtends": "print",
                        "sMessage": "Generated by SmartAdmin <i>(press Esc to close)</i>"
                    }
                 ],
                "sSwfPath": "js/plugin/datatables/swf/copy_csv_xls_pdf.swf"
            },
            "autoWidth" : true,
            "preDrawCallback" : function() {
                // Initialize the responsive datatables helper once.
                if (!responsiveHelper_datatable_col_reorder) {
                    responsiveHelper_datatable_col_reorder = new ResponsiveDatatablesHelper($('#dt_miacassa'), breakpointDefinition);
                }
            },
            "rowCallback" : function(nRow) {
                responsiveHelper_datatable_col_reorder.createExpandIcon(nRow);
            },
            "drawCallback" : function(oSettings) {
                responsiveHelper_datatable_col_reorder.respond();
            },
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
    }
    loadScript("js/plugin/jquery-form/jquery-form.min.js");
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