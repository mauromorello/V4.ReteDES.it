<?php
require_once("inc/init.php");

$ui = new SmartUI;
$page_title = "Amici";






$stmt = $db->prepare("SELECT * FROM  retegas_amici WHERE id_referente = '"._USER_ID."' AND retegas_amici.is_visible = '1'");
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $row) {
    if($row["status"]==1){
        $checked=" CHECKED ";
    }else{
        $checked="";
    }

    $z .= '<tr rel="'.$row["id_amici"].'">
            <td><input type="checkbox" name="checkbox-inline" id="'.$row["id_amici"].'" '.$checked.' class="attiva_amici"><i class="hidden">'.$checked.'</i></td>
            <td><span  class="editable_nome" data-original-title ="Nome e Cognome"  data-pk="'.$row["id_amici"].'">'.$row["nome"].'</span></td>
            <td><span  class="editable_indirizzo" data-original-title ="Indirizzo"  data-pk="'.$row["id_amici"].'">'.$row["indirizzo"].'</span></td>
            <td><span  class="editable_telefono" data-original-title ="Telefono"  data-type="text" data-pk="'.$row["id_amici"].'">'.$row["telefono"].'</span></td>

          </tr>';
}


$a='<div class="table-responsive">

                            <table id="dt_amici" class="table table-striped has-tickbox">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Nome</th>
                                        <th>Indirizzo</th>
                                        <th>Telefono</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    '.$z.'
                                </tbody>
                            </table>

                        </div>';
$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>true,
                    "deletebutton"=>false,
                    "colorbutton"=>true);
$wg_ami = $ui->create_widget($options);
$wg_ami->id = "wg_rubrica_amici";
$wg_ami->body = array("content" => $a,"class" => "no-padding");
$wg_ami->header = array(
    "title" => '<h2>Rubrica</h2>',
    "icon" => 'fa fa-table',
    "toolbar" => array( '<button class="btn btn-danger" id="delete_amico">
                            <i class="fa fa-trash-o" disabled="disabled"></i> Elimina</button>',
                        '<button class="btn btn-warning" id="enable">
                            <i class="fa fa-edit"></i> Modifica</button>',
                        '<button class="btn btn-success" id="aggiungi">
                            <i class="fa fa-plus"></i> Aggiungi</button>'
                            )
    );


?>
<div class="inbox-nav-bar no-content-padding">
    <h1 class="page-title txt-color-blueDark"><i class="fa fa-fw fa-group"></i> Amici &nbsp;</h1>
</div>

<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_title); ?>
            <?php echo $wg_ami->print_html(); ?>
        </article>
    </div>
</section>


<script type="text/javascript">
    pageSetUp();

    var pagefunction = function(){

        //------------HELP WIDGET
        <?php echo help_render_js($page_title);?>
        //------------END HELP WIDGET

        console.log("Start");
        var responsiveHelper_dt_basic = undefined;
            var responsiveHelper_datatable_fixed_column = undefined;
            var responsiveHelper_datatable_col_reorder = undefined;
            var responsiveHelper_datatable_tabletools = undefined;

            var breakpointDefinition = {
                tablet : 1024,
                phone : 480
            };

            var table= $('#dt_amici').dataTable({
                "paging":   false
            });
            $('#dt_amici tbody').on( 'click', 'tr', function () {
                if ( $(this).hasClass('danger') ) {
                    $(this).removeClass('danger');
                }
                else {
                    table.$('tr.danger').removeClass('danger');
                    $(this).addClass('danger');

                }

                if (table.$('tr.danger').length > 0){
                    $("#delete_amico").prop('disabled', false);
                }else{
                    $("#delete_amico").prop('disabled', 'disabled');
                }

            } );
            $('#delete_amico').click( function () {
                console.log("Delete");

                if (table.$('tr.danger').length > 0){
                    $.SmartMessageBox({
                    title : "Vuoi cancellare questa voce ?",
                    content : "Gli ordini vecchi condivisi con lui non verranno modificati.",
                    buttons : "[NO][SI]"

                        }, function(ButtonPress, Value) {

                            if(ButtonPress=="SI"){
                                var Value = table.$('tr.danger').attr('rel');
                                console.log(Value);
                                $.ajax({
                                      type: "POST",
                                      url: "ajax_rd4/user/_act.php",
                                      dataType: 'json',
                                      data: {act: "delete_amico", value : Value},
                                      context: document.body
                                    }).done(function(data) {
                                        if(data.result=="OK"){
                                                ok(data.msg);
                                                var row = $("tr.danger").closest("tr").get(0);
                                                table.fnDeleteRow(table.fnGetPosition(row));
                                                //table.row('.danger').remove().draw();
                                        }else{ko(data.msg);}

                                    });
                            }
                        });
                }else{

                    ko("Seleziona una riga della tabella che vuoi cancellare!")
                }




             } );


            $(".editable_nome").editable({
                url: 'ajax_rd4/user/_act.php',
                ajaxOptions: { dataType: 'json' },
                type: 'text',
                params:  {
                    'act': 'edit_amico_nome'
                },
                success: function(response, newValue) {
                    console.log(response);
                    if(response.result == 'KO'){
                        return response.msg;
                    }
                }
                });
           $(".editable_indirizzo").editable(
                {
                url: 'ajax_rd4/user/_act.php',
                ajaxOptions: { dataType: 'json' },
                type: 'textarea',
                emptytext: 'Non definito',
                params:  {
                    'act': 'edit_amico_indirizzo'
                },
                success: function(response, newValue) {
                    console.log(response);
                    if(response.result == 'KO'){
                        return response.msg;
                    }
                }
           });
           $(".editable_telefono").editable(
            {
                url: 'ajax_rd4/user/_act.php',
                ajaxOptions: { dataType: 'json' },
                type: 'text',
                emptytext: 'Non definito',
                params:  {
                    'act': 'edit_amico_telefono'
                },
                success: function(response, newValue) {
                    console.log(response);
                    if(response.result == 'KO'){
                        return response.msg;
                    }
                }
           });

          $('.attiva_amici').change(function () {
            var value = $(this).prop('checked');
            id = this.id;

            $.ajax({
              type: "POST",
              url: "ajax_rd4/user/_act.php",
              dataType: 'json',
              data: {act: "edit_amico_attivo", value : value, id:id},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                        ok(data.msg);}else{ko(data.msg);}
            });


        });

        $('.editable').editable('toggleDisabled');
        $('#enable').click(function () {
                $('.editable').editable('toggleDisabled');

        });

        $("#aggiungi").click(function(e) {

            $.SmartMessageBox({
                title : "Aggiungi un amico alla tua rubrica",
                content : "Inserisci il suo nome, potrai poi aggiungere gli altri dati",
                buttons : "[Esci][Salva]",
                input : "text",
                placeholder : "Nome",
                inputValue: '',
            }, function(ButtonPress, Value) {

                if(ButtonPress=="Salva"){
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/user/_act.php",
                          dataType: 'json',
                          data: {act: "aggiungi_amico", value : Value},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    ok(data.msg);}else{ko(data.msg);}
                                    location.reload();
                        });
                }
            });

            e.preventDefault();
        })


    };

    loadScript("js/plugin/datatables/jquery.dataTables.min.js", function(){
        loadScript("js/plugin/datatables/dataTables.colVis.min.js", function(){
            loadScript("js/plugin/datatables/dataTables.tableTools.min.js", function(){
                loadScript("js/plugin/datatables/dataTables.bootstrap.min.js", function(){
                    loadScript("js/plugin/datatable-responsive/datatables.responsive.min.js", function(){
                        loadScript("js/plugin/summernote/summernote.min.js", function(){
                            loadScript("js/plugin/x-editable/x-editable.min.js", pagefunction)
                        });
                    });
                });
            });
        });
    });
</script>
