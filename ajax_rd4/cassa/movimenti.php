<?php
    require_once("inc/init.php");
    require_once("../../lib_rd4/class.rd4.user.php");
    require_once("../../lib_rd4/class.rd4.ordine.php");

    $ui = new SmartUI;
    $page_title = "Movimenti Cassa";
    $page_id = "movimenti_cassa";
    $orientation = "landscape";

    $converter = new Encryption();
    $userid = CAST_TO_STRING($_POST["id"]);
    if($userid==""){
        $userid = CAST_TO_STRING($_GET["id"]);
    }
    $utente = CAST_TO_INT($converter->decode($userid));
    if($utente>0){
        $U = new user($utente);
        $fullname = " di " . $U->fullname.'<span class="pull-right"><a href="#ajax_rd4/cassa/movimenti.php?id=0"><strong>Mostrali tutti.</strong></a></span>';
    }else{
        $fullname = " di tutto il tuo GAS.";
    }


    if((!(_USER_PERMISSIONS & perm::puo_gestire_la_cassa))){
        echo "Non hai i permessi per gestire la cassa.";
    }
    $h = '<div id="table_container">
          <table id="movimenti_cassa_table"><tr><td></td></tr></table>
          <div id="pager"></div>
          </div>';




  //-------------------------ORDINO
  $css = css_report($orientation);
  if($_POST["o"]=="pdf"){

    include_once('../../lib_rd4/dompdf/dompdf_config.inc.php');
        $dompdf = new DOMPDF();

          $html ='<html>
      <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <title>ReteDES.it :: Movimenti CASSA</title>
        '.pdf_css().$css.'
      </head>
      <body>'.pdf_testata($O,$orientation).$h.'</body></html>';

          $dompdf->load_html($html);
          $dompdf->set_paper("letter", $orientation);
          $dompdf->render();
          $file_title = "Cassa_Utenti_".rand(1000,1000000).".pdf";
          $dompdf->stream($file_title, array("Attachment" => false));

          exit(0);
    }

    
    $id_gas = _USER_ID_GAS;
    $stmt = $db->prepare("SELECT * from retegas_options WHERE chiave='_CASSA_CONSOLIDAMENTO' AND id_gas=:id_gas ORDER BY valore_data DESC LIMIT 1;");
     $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();

    if($stmt->rowCount()==1){
        $ultimo_consolidamento = '<div class="alert alert-info"> Questo GAS ha già consolidato la cassa il <strong>'.conv_datetime_from_db($row["valore_data"]).'</strong> ad opera del cassiere '.user_fullname($row["id_user"])."</div>";
    }else{
        $ultimo_consolidamento = '<div class="alert alert-info"> La cassa di questo GAS non è MAI stata consolidata</div>';
    }
    
    
    
    
  //$buttons[]='<button  class="show_pdf btn btn-defalut btn-default"><i class="fa fa-file-pdf-o"></i>  PDF</button>';
  $buttons[]='<button  onclick="selectElementContents( document.getElementById(\'table_container\') ); " class="btn btn-default btn-default"><i class="fa fa-copy"></i>  COPIA</button>';
  $buttons[]='<button  class="btn btn-default btn-default show_csv"><i class="fa fa-download"></i>  CSV</button>';
  //$buttons[]='<button   class="btn btn-default btn-default" onclick=\'printDiv("table_container")\'><i class="fa fa-print"></i>  Stampa</button>';


?>

<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
        </article>
    </div>
</section>
<p></p>
<?php echo navbar_report($page_title, $buttons); ?>
<hr>
<div class="alert alert-info"><strong>N.B: </strong>Stai visualizzando i movimenti <?php echo $fullname; ?></div>
<?php echo $h; ?>
<hr>
<div class="panel panel-red padding-10">
    <h1>Eliminazione movimenti</h1>
    <p class="alert alert-danger">Questa operazione è irreversibile. Non esiste un log dei movimenti, e l'eliminazione anche di movimenti vecchi causerà la variazione dei totali degli utenti e del GAS.</p>
    <button class="btn btn-danger pull-right" id="do_elimina_movimento_multiplo"><i class="fa fa-warning"></i> ELIMINA</button>
    <h3>Stai selezionando <span id="n_selezione_multipla">0</span> movimenti:</h3>
    <br>
    <span id="selezione_multipla" class="txt-color-purple">Nessuno</span>
</div>
<hr>

<div class="panel panel-greenDark padding-10">
    <h1>Variazione importi</h1>
    <p class="alert alert-danger">La variazione degli importi avrà conseguenze sui totali utente e GAS.</p>

    <h3>Movimento selezionato: #<span id="id_movimento_singolo_edit" class="txt-color-greenDark">Nessuno</span></h3>
    <form class="smart-form">
        <section>
            <label class="label">Modifica la cifra</label>
            <label class="input">
                <input type="text" id="importo_edit" class="input-lg">
            </label>
        </section>
    </form>

    <button class="btn btn-success pull-right" id="do_edita_movimento_singolo"><i class="fa fa-check"></i> SALVA LE MODIFICHE</button>
    <div class="clearfix"></div>
</div>

<hr>
<div class="panel panel-red padding-10">
    <h1>Consolidamento movimenti CASSA</h1>
    <p>L'operazione di consolida serve a cancellare tutti i movimenti vecchi e a inserire solo un movimento per ogni utente che contiene la somma algebrica di tutti i suoi vecchi movimenti. Questa funzione è utile nel caso il GAS voglia tenere separate le gestioni della cassa a base annua oppure ad ogni cambio cassiere.</p>
    <p class="alert alert-info">Questa operazione è irreversibile. Se lo ritieni opportuno puoi salvare tutti i movimenti di cassa usando le apposite funzioni. NB: Verranno considerati TUTTI i movimenti presenti, anche quelli non registrati.</p>
    <?php echo $ultimo_consolidamento; ?>
    <button class="btn btn-primary pull-right " id="do_consolida_movimenti"><i class="fa fa-filter"></i> CONSOLIDA LA CASSA</button>
    <div class="clearfix"></div>
    
</div>


<hr>
<div class="panel panel-red padding-10">
    <h1>Eliminazione totale movimenti CASSA</h1>
    <p class="alert alert-danger">Questa operazione è irreversibile. La cassa del tuo GAS sarà azzerata. Se lo ritieni opportuno puoi salvare tutti i movimenti di cassa usando le apposite funzioni.</p>
    <button class="btn btn-danger pull-right btn-sm" id="do_elimina_tutti_movimenti"><i class="fa fa-trash-o"></i> ELIMINA TUTTA LA CASSA</button>
    <div class="clearfix"></div>
</div>
<hr>


<script type="text/javascript">

    pageSetUp();


    var pagefunction = function() {

        //-------------------------HELP
        document.title = '<?php echo "ReteDES.it :: $page_title";?>';
        <?php echo help_render_js($page_id); ?>
        //-------------------------HELP

        loadScript("js/plugin/jqgrid/jquery.jqGrid.min.js",startTable);
        //loadScript("js/plugin/jqgrid/jquery.jqGrid.min.js",startTable);
        //loadScript("js_rd4/plugin/tablesorter/js/jquery.tablesorter.widgets.js",loadMath);

        function startTable(){

                jQuery("#movimenti_cassa_table").jqGrid({
                    url: "ajax_rd4/cassa/movimenti_db.php",
                    datatype: "json",
                    mtype: "GET",
                    postData:{userid:"<?php echo $userid; ?>"},
                    colNames: ["id", "fullname", "data", "tipo","segno", "importo", "descrizione_movimento", "id_ordine", "ditta", "cassiere","data_registrato","registrato"],
                    colModel: [

                            {name:'id',index:'id', width:30},
                            {name:'Utente',index:'fullname', width:80},
                            {name:'data',index:'data', width:80},
                            {name:'tipo',index:'tipo', width:30},
                            {name:'segno',index:'segno', width:30, align:"left"},
                            {name:'importo',index:'importo', width:50, align:"left"},
                            {name:'descrizione_movimento',index:'descrizione_movimento', width:150, align:"left"},
                            {name:'N.Ordine',index:'id_ordine', width:40,align:"right"},
                            {name:'ditta',index:'ditta', width:80},
                            {name:'cassiere',index:'cassiere', width:70},
                            {name:'data_registrato',index:'data_registrato', width:70},
                            {name:'registrato',index:'registrato', width:50}
                    ],
                    pager: "#pager",
                    autowidth: true,
                    height : 600,
                    rowNum: 100,
                    rowList: [100, 1000, 5000, 10000],
                    sortname: "R.id_cassa_utenti",
                    sortorder: "desc",
                    viewrecords: true,
                    gridview: true,
                    autoencode: true,
                    multiselect: true,
                    caption: "",
                    onSelectRow: function(id){

                        var selectedIDs = jQuery("#movimenti_cassa_table").jqGrid().getGridParam("selarrrow");
                        var result = "";


                        $('#id_movimento_singolo').html(id);
                        $('#id_movimento_singolo_edit').html(id);
                        $('#importo_edit').val(jQuery("#movimenti_cassa_table").jqGrid ('getCell', id, 'importo'));



                        for (var i = 0; i < selectedIDs.length; i++) {
                            result += selectedIDs[i] + ", ";
                        }
                        $('#selezione_multipla').html(result);
                        $('#n_selezione_multipla').html(selectedIDs.length);



                    },
                    onSelectAll:function(id,status){
                        $('#id_movimento_singolo').html("");
                        $('#id_movimento_singolo_edit').html("");
                        $('#importo_edit').val("0");

                        var selectedIDs = jQuery("#movimenti_cassa_table").jqGrid().getGridParam("selarrrow");
                        var result = "";

                        for (var i = 0; i < selectedIDs.length; i++) {
                            result += selectedIDs[i] + ", ";
                        }
                        $('#selezione_multipla').html(result);
                        $('#n_selezione_multipla').html(selectedIDs.length);

                    }
                });
                jQuery("#movimenti_cassa_table").jqGrid('filterToolbar',{});

                //jQuery("#movimenti_cassa_table").jqGrid('navButtonAdd','#pager',{
                //       caption:"ESPORTA",
                //       onClickButton : function () {
                          // jQuery("#movimenti_cassa_table").jqGrid('excelExport',{"url":"ajax_rd4/cassa/movimenti_db.php"});
                //       }
                //});

                $(window).on('resize.jqGrid', function() {
                    console.log ("resizing ");

                        jQuery("#movimenti_cassa_table").jqGrid('setGridWidth', $("#table_container").width());
                        //jQuery("#movimenti_cassa_table").jqGrid('setGridHeight', $("#table_container").height() - ($("#gbox_jqgrid").height() - $('#gbox_jqgrid .ui-jqgrid-bdiv').height()));

                });
                // remove classes
                $(".ui-jqgrid").removeClass("ui-widget ui-widget-content");
                $(".ui-jqgrid-view").children().removeClass("ui-widget-header ui-state-default");
                $(".ui-jqgrid-labels, .ui-search-toolbar").children().removeClass("ui-state-default ui-th-column ui-th-ltr");
                $(".ui-jqgrid-pager").removeClass("ui-state-default");
                $(".ui-jqgrid").removeClass("ui-widget-content");

                // add classes
                $(".ui-jqgrid-htable").addClass("table table-bordered table-hover");
                $(".ui-jqgrid-btable").addClass("table table-bordered table-striped");




        }//END STARTTABLE

        $('.show_csv').click(function(){
            jQuery("#movimenti_cassa_table").jqGrid('excelExport',{"url":"ajax_rd4/cassa/movimenti_db.php"});
            return false;
        });

        $('.show_pdf').click(function(){
            var $this = $(this);
            var id = $this.data('id_ordine');
            open('POST', '<?php echo APP_URL; ?>/ajax_rd4/cassa/movimenti.php', {id:id, o:'pdf', dummy:<?php echo rand(1000,9999); ?> }, '_blank');
            return false;
        });

        /*CONSOLIDA*/
        $(document).off("click","#do_consolida_movimenti");
        $(document).on("click","#do_consolida_movimenti", function(e){


            $.SmartMessageBox({
                                title : "Stai per consolidare la tua cassa",
                                content : "Verrà inoltrata una mail ai cassieri, questa operazione è irreversibile.",
                                buttons : '[OK][ANNULLA]'
                            }, function(ButtonPressed) {
                                if (ButtonPressed === "OK") {

                                    $.ajax({
                                          type: "POST",
                                          url: "ajax_rd4/cassa/_act.php",
                                          dataType: 'json',
                                          data: {act: "consolida_movimenti"},
                                          context: document.body
                                        }).done(function(data) {
                                            if(data.result=="OK"){
                                                okReload("Cassa consolidata.");
                                            }else{
                                                ko(data.msg);
                                            }
                                    });

                                }
                            });

            /*ALLERTA*/
             e.preventDefault;
        });
        /*CONSOLIDA*/


        $(document).off("click","#do_elimina_movimento_multiplo");
        $(document).on("click","#do_elimina_movimento_multiplo", function(e){
            /*ALLERTA*/
            var ids = jQuery("#movimenti_cassa_table").jqGrid().getGridParam("selarrrow");

            $.SmartMessageBox({
                                title : "Stai eliminando n." + ids.length + " movimenti della cassa",
                                content : "Verrà inoltrata una mail ai cassieri, questa operazione è definitiva.",
                                buttons : '[OK][ANNULLA]'
                            }, function(ButtonPressed) {
                                if (ButtonPressed === "OK") {
                                    /*DELETE MULTIPLO*/
                                    console.log(ids);
                                    $.ajax({
                                          type: "POST",
                                          url: "ajax_rd4/cassa/_act.php",
                                          dataType: 'json',
                                          data: {act: "delete_movimento_multiplo", ids:ids},
                                          context: document.body
                                        }).done(function(data) {
                                            if(data.result=="OK"){
                                                ok("Movimenti eliminati.");
                                                for (var i = ids.length - 1; i >= 0; i--) {
                                                    $('#movimenti_cassa_table').jqGrid('delRowData', ids[i]);
                                                }
                                                $('#selezione_multipla').html("");
                                                $('#n_selezione_multipla').html("0");

                                                //$("#movimenti_cassa_table").jqGrid('delRowData',$("#movimenti_cassa_table").jqGrid ('getGridParam', 'selrow'));
                                            }else{
                                                ko(data.msg);
                                            }

                                    });
                                    /*DELETE MULTIPLO*/
                                }
                            });

            /*ALLERTA*/
             e.preventDefault;
        });

        $(document).off("click","#do_elimina_tutti_movimenti");
        $(document).on("click","#do_elimina_tutti_movimenti", function(e){
            /*ALLERTA*/

            $.SmartMessageBox({
                                title : "Stai eliminando TUTTI i movimenti della cassa del tuo GAS",
                                content : "Verrà inoltrata una mail ai cassieri, questa operazione è definitiva.",
                                buttons : '[OK][ANNULLA]'
                            }, function(ButtonPressed) {
                                if (ButtonPressed === "OK") {
                                    /*DELETE TOTALE*/

                                    $.ajax({
                                          type: "POST",
                                          url: "ajax_rd4/cassa/_act.php",
                                          dataType: 'json',
                                          data: {act: "delete_movimenti_tutti"},
                                          context: document.body
                                        }).done(function(data) {
                                            if(data.result=="OK"){
                                                okReload("Cassa azzerata.<br>Eliminati " + data.deleted + " movimenti." );
                                            }else{
                                                ko(data.msg);
                                            }
                                    });
                                    /*DELETE TOTALE*/
                                }
                            });

            /*ALLERTA*/
             e.preventDefault;
        });


        $(document).off("click","#do_edita_movimento_singolo");
        $(document).on("click","#do_edita_movimento_singolo", function(e){
        //$('#do_edita_movimento_singolo').click(function(){
            var id=$('#id_movimento_singolo_edit').html();
            var importo= $('#importo_edit').val();

            console.log(id);
            $.ajax({
                  type: "POST",
                  url: "ajax_rd4/cassa/_act.php",
                  dataType: 'json',
                  data: {act: "edit_movimento_singolo", id:id, importo:importo},
                  context: document.body
                }).done(function(data) {
                    if(data.result=="OK"){
                        ok("Movimento modificato.");
                        //$("#movimenti_cassa_table").jqGrid('delRowData',$("#movimenti_cassa_table").jqGrid ('getGridParam', 'selrow'));

                        $("#movimenti_cassa_table").jqGrid('setCell', data.id, 'importo', data.importo);
                        $("#movimenti_cassa_table").jqGrid('getLocalRow', data.id).importo = data.importo;

                        $("#movimenti_cassa_table").jqGrid('setCell', data.id, 'segno', data.segno);
                        $("#movimenti_cassa_table").jqGrid('getLocalRow', data.id).segno = data.segno;

                    }else{
                        ko(data.msg);
                    }

                });
             //e.preventDefault;

        });


        startTable();
    }
    // end pagefunction

    loadScript("js/plugin/jqgrid/grid.locale-en.min.js", pagefunction);



</script>