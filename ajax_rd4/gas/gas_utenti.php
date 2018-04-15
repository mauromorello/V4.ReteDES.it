<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.gas.php");

$ui = new SmartUI;
$converter = new Encryption;

$page_title = "Utenti del mio GAS";
$page_id="gas_utenti";

$G = new gas(_USER_ID_GAS);

if(!_USER_GAS_VISIONE_DATI_UTENTI){
    if(!((_USER_PERMISSIONS & perm::puo_gestire_utenti) OR (_USER_PERMISSIONS & perm::puo_creare_gas))){
        echo rd4_go_back("Il tuo GAS non permette la visione dei dati utenti");die;
    }
}

$stmt = $db->prepare("SELECT * FROM  maaking_users WHERE id_gas = '"._USER_ID_GAS."' AND isactive=1");
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $row) {

    $useridEnc = $converter->encode($row["userid"]);

    //ha la cassa;


    $p1 = '<img class="" src="'.src_user($row["userid"],64).'" style="width:32px;height:32px;">';
    $p2 = '<a class="btn btn-link" data-id="'.$row["userid"].'" href="ajax_rd4/gas/inc/messaggio.php?id='.$row["userid"].'" data-toggle="modal" data-target="#remoteModal"><i class="fa fa-envelope"></i></a>';


    $indirizzo=$row["country"].'<br><span class="note">'.$row["city"].'</span>';

    if($G->custom_1_privato==0){
        $custom_1='<td>'.$row["custom_1"].'</td>';
    }else{
        $custom_1='';
    }
    if($G->custom_2_privato==0){
        $custom_2='<td>'.$row["custom_2"].'</td>';
    }else{
        $custom_2='';
    }
    if($G->custom_3_privato==0){
        $custom_3='<td>'.$row["custom_3"].'</td>';
    }else{
        $custom_3='';
    }

    if((_USER_PERMISSIONS & perm::puo_gestire_utenti) OR $row["isactive"]==1){

        $z .= '<tr rel="'.$useridEnc.'" class="'.$status.'">
                <td><label class="checkbox"><input type="checkbox" class="utente" value="'.$row["userid"].'"><i></i></label></td>
                <td>'.$p1.'</td>
                <td><a href="#ajax_rd4/user/scheda.php?id='.$useridEnc.'">'.$row["fullname"].'</a></td>
                <td>'.$p2.'  '.$row["email"].'</td>
                <td>'.$indirizzo.'</td>
                <td>'.$row["tel"].'</td>
                <td>'.$status_n.'</td>
                '.$custom_1.'
                '.$custom_2.'
                '.$custom_3.'

              </tr>';
    }
}


if($G->custom_1_privato==0){
        $custom_1_header='<th>'.$G->custom_1_nome.'</th>';
    }else{
        $custom_1_header='';
    }
if($G->custom_2_privato==0){
        $custom_2_header='<th>'.$G->custom_2_nome.'</th>';
    }else{
        $custom_2_header='';
    }
if($G->custom_3_privato==0){
        $custom_3_header='<th>'.$G->custom_3_nome.'</th>';
    }else{
        $custom_3_header='';
    }

$a='    <div class="" style="max-height:600px;overflow-y:auto;">
        <table id="dt_utenti_gas" class="table table-striped margin-top-10 has-tickbox smart-form">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>Nome</th>
                                        <th><i class="fa fa-envelope"></i>&nbsp;Mail</th>
                                        <th>Indirizzo</th>
                                        <th>Tel</th>
                                        <th>Status</th>
                                        '.$custom_1_header.'
                                        '.$custom_2_header.'
                                        '.$custom_3_header.'
                                    </tr>
                                </thead>
                                <tbody>
                                    '.$z.'
                                </tbody>
                            </table>
        </div> ';


?>
<?php echo $G->render_toolbar("UTENTI del "._USER_GAS_NOME); ?>

<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
        </article>
    </div>
</section>

<?php echo '<hr>'.$a; ?>
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
                                            
                                            "sDom": "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-6 hidden-xs'T C>r>"+
                                                    "t"+
                                                    "<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-sm-6 col-xs-12'p>>",
                                            "oTableTools": {
                                                 "aButtons": [
                                                 "copy",
                                                 "csv",
                                                 {
                                                        "sExtends": "xls",
                                                        "sFileName": "*.xls"
                                                    },
                                                 //"xls",
                                                    {
                                                        "sExtends": "pdf",
                                                        "sTitle": "Utenti <?php echo _USER_GAS_NOME;?>",
                                                        "sPdfMessage": "reteDES.it PDF Export",
                                                        "sPdfSize": "letter"
                                                    },
                                                     {
                                                        "sExtends": "print",
                                                        "sMessage": "Generato con reteDES.it <i>(premiESC per uscire)</i>"
                                                    }
                                                 ],
                                                "sSwfPath": "../gas4/js/plugin/datatables/swf/copy_csv_xls_pdf.swf"
                                            },
                                            "bPaginate": false
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

        $('body').on('click', '#usermessage_go', function () {
            //invio il messaggio ciccio

            $.ajax({
              type: "POST",
              url: "ajax_rd4/gas/_act.php",
              dataType: 'json',
              data: {act: "messaggia", messaggio : messaggio, id:id},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                    ok(data.msg);
                    messaggio='';
                }else{
                    ko(data.msg);
                }
            });


            //chiudo il modal
            $('#remoteModal').modal('hide');
        });

        //$('.show_Attesa').click(function(){oTable.fnFilter( 'in Attesa',5 );});
        //$('.show_Attivi').click(function(){oTable.fnFilter( 'Attivo',5 );});
        //$('.show_Sospesi').click(function(){oTable.fnFilter( 'Sospeso',5 );});
        //$('.show_Eliminati').click(function(){oTable.fnFilter( 'Eliminato',5 );});
        //$('.show_Tutti').click(function(){  oTable.fnFilter('',5);
        //                                    oTable.fnFilter('');});
        //MOstro solo gli attivi
        //oTable.fnFilter( 'Attivo',5 );

        //se clicco
        //$('#dt_utenti_gas tbody').on( 'click', 'tr', function () {
        //    var value = $(this).attr('rel');
         //       $.ajax({
         //         type: "POST",
        //          url: "ajax_rd4/gas/inc/info_utente.php",
        //          data: {userid : value},
        //          context: document.body
        //       }).done(function(data) {
        //
        //           $('#schedina_utente').html(data);
        //
        //
        //    });
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