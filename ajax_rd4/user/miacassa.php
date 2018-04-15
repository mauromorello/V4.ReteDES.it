<?php
require_once("inc/init.php");
$ui = new SmartUI;

$page_title = "Mia Cassa";
$help_id ="mia_cassa";


//-------SALDO
$saldo = _NF(VA_CASSA_SALDO_UTENTE_TOTALE(_USER_ID));
$saldo_non_conf = abs(_NF(VA_CASSA_SALDO_UTENTE_DA_REGISTRARE(_USER_ID)));


$cassa='<h1>Hai <b><span class="txt-color-blue font-xl">'.$saldo.' €</span></b> disponibili</h1>
        <h3>e <span class="txt-color-red font-lg">'.$saldo_non_conf.' €</span> non ancora contabilizzati</h3';

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
                            <div class="panel panel-default" id="anticipa-form-parent">
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
                                                <input id="invia_richiesta_ricarica" class="btn btn-lg btn-primary pull-right" type="submit" value="Invia la richiesta">
                                            </fieldset>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>






           ';






//MOVIMENTI CASSA
$stmt = $db->prepare("SELECT C.*, O.descrizione_ordini,U.fullname FROM retegas_cassa_utenti C left join retegas_ordini O on O.id_ordini=C.id_ordine left join maaking_users U on U.userid=C.id_cassiere WHERE C.id_utente='"._USER_ID."'  ORDER BY C.id_cassa_utenti DESC;");
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$h ='<table id="dt_miacassa" class="table table-striped" width="100%">
        <thead>
         <tr>

        <th data-class="expand">#</th>
        <th data-hide="phone,tablet">Data</th>
        <th data-hide="phone,tablet">Tipo</th>
        <th>Credito</th>
        <th>Debito</th>
        <th >Descrizione</th>
        <th data-hide="phone">Ordine</th>
        <th data-hide="phone">Cassiere</th>
        <th data-hide="phone">REG</th>
        <!--<th data-hide="phone">CON</th>-->
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
         //if($row["contabilizzato"]=="si"){
        //     $CON = conv_datetime_from_db($row["data_contabilizzato"]);
         //
         //}else{
         //    $CON = "";
         //}

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

    

    <div class="row margin-top-10">
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <div class="well well-sm">
            <?php echo $preno; ?>
            </div>
        </article>
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <div class="well well-sm text-align-right">
            <?php echo $cassa; ?>
            </div>
        </article>
    </div>

    <div class="row margin-top-10">
     <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            Clicca <a class="btn btn-danger" href="\#ajax_rd4\user\mia_cassa.php">qua</a> per una versione più completa di questo report.
     
     </article>
    </div>
    
    <div class="row margin-top-10">
     <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo $h ?>
        </article>
    </div>
      <hr>
        <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($help_id); ?>
        </article>
    </div>
</section>


<script type="text/javascript">

    pageSetUp();

    var pagefunction = function(){

         //-----------HELP
         <?php echo help_render_js($help_id); ?>
         //-----------HELP

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
                $('#invia_richiesta_ricarica').prop('disable', true).addClass('disabled');
                $('#invia_richiesta_ricarica').val('Attendi...');

                $(form).ajaxSubmit({
                    type:"POST",
                    dataType: 'json',
                    success : function(data) {
                                $("#anticipa-form-parent").hide();

                                if(data.result=="OK"){
                                    okWait(data.msg);

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

        $.fn.dataTable.moment( 'DD/MM/YYYY HH:mm' );

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
                        "sPdfMessage": "reteDES.it PDF Export",
                        "sPdfSize": "letter"
                    },
                     {
                        "sExtends": "print",
                        "sMessage": "Generato con reteDES.it <i>(premiESC per uscire)</i>"
                    }
                 ],
                "sSwfPath": "js/plugin/datatables/swf/copy_csv_xls_pdf.swf"
            },
            "columnDefs": [
                //{ "type": "date", "targets": 1 },
                //{ "type": "date", "targets": 8 }
            ],
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
                    loadScript("//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js", function(){
                        loadScript("//cdn.datatables.net/plug-ins/1.10.12/sorting/datetime-moment.js", function(){
                            loadScript("js/plugin/datatable-responsive/datatables.responsive.min.js", pagefunction)
                        });
                    });
                });
            });
        });
    });
</script>