<?php
require_once("inc/init.php");
if(file_exists("../../lib_rd4/class.rd4.ordine.php")){require_once("../../lib_rd4/class.rd4.ordine.php");}
if(file_exists("../lib_rd4/class.rd4.ordine.php")){require_once("../lib_rd4/class.rd4.ordine.php");}
$ui = new SmartUI;
$converter = new Encryption;

$page_title = "Comunica a tutti i referenti";
$page_id = "comunica_referenti";


$id_ordine = CAST_TO_INT($_POST["id"],0);
if ($id_ordine==0){
    $id_ordine = CAST_TO_INT($_GET["id"],0);
}

if($id_ordine==0){echo rd4_go_back("KO!");die;}

if (!posso_gestire_ordine_come_gas($id_ordine)){
    echo rd4_go_back("Non ho i permessi necessari");die;
}
$filter='';
if (!posso_gestire_ordine($id_ordine)){
    $filter=" AND maaking_users.id_gas= "._USER_ID_GAS." ";
}


$O=new ordine($id_ordine);



$rows = $O->lista_referenti_gas_partecipanti();
$rowsE = $O->lista_referenti_extra();

$rows= array_merge($rows,$rowsE);


foreach ($rows as $row) {

    $useridEnc = $converter->encode($row["userid"]);

    $p1 = '<img class="" src="'.src_user($row["userid"],64).'" style="width:32px;height:32px;">';


    $z .= '<tr rel="'.$useridEnc.'">
            <td><label class="checkbox"><input type="checkbox" class="utente" value="'.$row["userid"].'"><i></i></label></td>
            <td>'.$p1.'</td>
            <td><a href="#ajax_rd4/user/scheda.php?id='.$useridEnc.'">'.$row["fullname"].'</a></td>
            <td>'.$row["descrizione_gas"].'</td>
            <td>'.$partecipa.'</td>
            <td>'.$valore_ordine.'</td>
          </tr>';

}


$a='    <div class="" style="max-height:600px;overflow-y:auto;">

        <table id="dt_utenti_gas" class="table table-striped margin-top-10 has-tickbox smart-form">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>Nome</th>
                                        <th><i class="fa fa-home"></i>&nbsp;GAS</th>
                                        <th>partecipa</th>
                                        <th>per Euro</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    '.$z.'
                                </tbody>
                            </table>
        </div>
        <div class="well margin-top-5">
            <label class="pull-right ">
            <input class="selectall" type="checkbox"> Seleziona / deseleziona tutti
            </label>
            <p><button class="btn btn-default" id="manda_messaggio_a_referenti"><i class="fa fa-envelope"></i>   manda agli utenti selezionati un messaggio.</button></p>

            <label>Messaggio:</label>
            <textarea  id="messaggio_a_utenti" style="width:100%;"></textarea>
            <p></p>
            <div class="alert alert-info"><strong>ATTENZIONE:</strong> Non abusare di questa funzione. A nessuno piace ricevere mail inutili.</div>
        </div>

        ';

        $mp ='
        <div class="row ">
            <div class=" col-xs-4">
                <h4>Filtra la tabella:</h4>
                <div class="btn-group-vertical btn-block">
                    <a class="show_Tutti btn btn-default">Tutti</a>
                    <a class="show_Partecipa btn btn-default">Che partecipano </a>
                    <a class="show_NonPartecipa btn btn-default">Che non partecipano</a>
                </div>
            </div>

            <div class="col-xs-4">
            </div>
            <div class="col-xs-4">
            </div>
        </div>
      ';

?>
<?php echo $O->navbar_ordine(); ?>

<h3>Comunica a qualche utente legato a questo ordine.</h3>
<?php echo $mp.'<hr>'.$a; ?>

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



        console.log("pagefunction");
        //------------HELP WIDGET
        document.title = '<?php echo "ReteDES.it :: $page_title";?>';
        <?php echo help_render_js($page_id);?>
        //------------END HELP WIDGET
        var oTable= $('#dt_utenti_gas').dataTable({
                                            "bPaginate": false
                                        });
        var id;
        var messaggio;

        $('#manda_messaggio_a_referenti').click(function(){
            console.log("Click");
            values = $('input:checkbox:checked.utente').map(function () {
              return this.value;
            }).get();
            messaggio = $('#messaggio_a_utenti').val();
            console.log("Messaggio " + messaggio);
            if(!messaggio){
                ko("Messaggio vuoto");
            }
            else if(values.length==0){
                ko("Nessun destinatario");
            }else{
            console.log(values);

            $.SmartMessageBox({
                title : "Messaggia",
                content : "Confermi? la mail sarà inviata a " + values.length + " utenti",
                buttons : "[Esci][INVIA]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="INVIA"){
                    $.blockUI();
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/gas/_act.php",
                          dataType: 'json',
                          data: {act: "messaggia_utenti", values : values, messaggio : messaggio},
                          context: document.body
                        }).done(function(data) {
                            $.unblockUI();
                            if(data.result=="OK"){
                                    ok(data.msg);
                                    //location.reload();
                            }else{
                                ko(data.msg);
                            }

                        });
                }
            });




        }//messaggio vuoto

        });


        $('.selectall').click(function(event) {  //on click
            console.log("Click select");
            if(this.checked) { // check select status
                $('.utente').each(function() { //loop through each checkbox
                    this.checked = true;  //select all checkboxes with class "checkbox1"
                });
            }else{
                $('.utente').each(function() { //loop through each checkbox
                    this.checked = false; //deselect all checkboxes with class "checkbox1"
                });
            }
        });





        $('body').on('hidden.bs.modal', '.modal', function () {
          $(this).removeData('bs.modal');
        });

        $('body').on('shown.bs.modal','.modal', function(e) {
            console.log("Modal opened");
            id = $(e.relatedTarget).attr('data-id');
        });


        $(document).on( 'change', '#usermessage', function() {
            messaggio = $(this).val();
            console.log("messaggio = " + messaggio);

        });

        $('.show_Partecipa').click(function(){oTable.fnFilter( 'SI',4 );});
        $('.show_NonPartecipa').click(function(){oTable.fnFilter( 'NO',4 );});
        $('.show_Tutti').click(function(){  oTable.fnFilter('',4);
                                            oTable.fnFilter('');}
        );

        //$('#dt_utenti_gas thead th').each( function () {
        //    var title = $(this).text();
        //    $(this).html( '<input type="text" placeholder="Filtra '+title+'" />' );
        //} );


    } // end pagefunction



    loadScript("js/plugin/datatables/jquery.dataTables.min.js", function(){
        loadScript("js/plugin/datatables/dataTables.colVis.min.js", function(){
            loadScript("js/plugin/datatables/dataTables.tableTools.min.js", function(){
                loadScript("js/plugin/datatables/dataTables.bootstrap.min.js", function(){
                    loadScript("js/plugin/datatable-responsive/datatables.responsive.min.js", function(){
                            loadScript("js/plugin/x-editable/x-editable.min.js", pagefunction())

                    });
                });
            });
        });
    });

</script>