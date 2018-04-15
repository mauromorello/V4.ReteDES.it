<?php
require_once("inc/init.php");

$ui = new SmartUI;
$page_title = "Amici";
$page_id = "amici";





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
            <td><span  class="editable_email" data-original-title ="Email"  data-type="text" data-pk="'.$row["id_amici"].'">'.$row["email"].'</span></td>
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
                                        <th>Email</th>
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

    //LISTA MAIL AMICI
        $stmt = $db->prepare("SELECT * FROM  retegas_amici WHERE id_referente='"._USER_ID."' AND status=1 AND email IS NOT NULL ");
        $stmt->execute();
        $rows = $stmt->fetchAll();
        foreach($rows as $row){
            $la .= $row["nome"].' '.htmlentities('<').$row["email"].htmlentities('>').'; ';
        }


?>
<div class="inbox-nav-bar no-content-padding">
    <h1 class="page-title txt-color-blueDark"><i class="fa fa-fw fa-group"></i> Amici &nbsp;</h1>
</div>

<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
            <?php echo $wg_ami->print_html(); ?>
        </article>
    </div><section id="widget-grid" class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <h1>Mail degli amici selezionati: <small class="pull-right"><button class="btn btn-link" id="copia_indirizzi" onClick="CopyClipboard();">Copia negli appunti</button></small></h1>
            <div class="panel panel-blue padding-10 font-md" id="box_mail_amici"><?echo $la?></div>
        </article>
    </div>
</section>
</section>


<script type="text/javascript">
    pageSetUp();

    function CopyClipboard(){
      // creating new textarea element and giveing it id 't'
      var t = document.createElement('textarea')
      t.id = 't'
      // Optional step to make less noise in the page, if any!
      t.style.height = 0
      // You have to append it to your page somewhere, I chose <body>
      document.body.appendChild(t)
      // Copy whatever is in your div to our new textarea
      t.value = document.getElementById('box_mail_amici').innerText
      // Now copy whatever inside the textarea to clipboard
      var selector = document.querySelector('#t')
      selector.select()
      document.execCommand('copy')
      // Remove the textarea
      document.body.removeChild(t)

    }

    var pagefunction = function(){

        //------------HELP WIDGET
        <?php echo help_render_js($page_id);?>
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
           $(".editable_email").editable(
            {
                url: 'ajax_rd4/user/_act.php',
                ajaxOptions: { dataType: 'json' },
                type: 'text',
                emptytext: 'Non definito',
                params:  {
                    'act': 'edit_amico_email'
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
                        ok(data.msg);
                        $('#box_mail_amici').html(data.mail_amici);

                }else{ko(data.msg);}
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

                            loadScript("js/plugin/x-editable/x-editable.min.js", pagefunction)

                    });
                });
            });
        });
    });
</script>
