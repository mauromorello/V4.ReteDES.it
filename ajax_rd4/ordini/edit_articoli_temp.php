<?php

require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.listino.php");

$ui = new SmartUI;
$converter = new Encryption;

$page_title = "Gestisci articoli temporanei";
$page_id = "gestisci_articoli_temp";



$id_ordine = CAST_TO_INT($_GET["id"]);
if($id_ordine==0){
    $id_ordine = CAST_TO_INT($_POST["id"]);
    if($id_ordine==0){
        echo "id missing";die();
    }
}
$O = new ordine($id_ordine);
$id_listino = $O->id_listini;
$L = new listino($id_listino);

//PERMESSI QUA
if(!posso_gestire_ordine($id_ordine)){
    echo rd4_go_back("NON PUOI");die();
}


//-----------------------------------------------------ARTICOLI
//if($L->is_multiditta){
if(false){
    $iladd='';
}else{
    $iladd='<button  class="btn btn-default btn-circle btn-lg" onclick="$(\'#jqgrid_iladd\').click();$(\'.ui-search-toolbar\').hide();$(\'#save_riga\').removeClass(\'hidden\');$(\'#save_cancel\').removeClass(\'hidden\');"><i class="fa fa-plus"></i></button>';
}

$a = '
        <div class="well well-sm well-light">
            <!--
            <span class="pull-right">

                <button  class="btn btn-danger btn-circle btn-lg" onclick="$(\'#del_jqgrid\').click();"><i class="fa fa-trash-o "></i></button>
            </span>
            -->
            <button  class="btn btn-default btn-circle btn-lg" onclick="$(\'.ui-search-toolbar\').toggle();"><i class="fa fa-filter "></i></button>
            '.$iladd.'
            <button  class="btn btn-warning btn-circle btn-lg hidden" id="duplica_riga"><i class="fa fa-copy "></i></button>
            <button  class="btn btn-warning btn-circle btn-lg hidden" id="edita_riga" onclick="$(\'#jqgrid_iledit\').click();$(\'.ui-search-toolbar\').hide();"><i class="fa fa-pencil "></i></button>
            <button  class="btn btn-success btn-circle btn-lg hidden" id="save_riga" onclick="$(\'#jqgrid_ilsave\').click();"><i class="fa fa-save "></i></button>
            <button  class="btn btn-default btn-circle btn-lg hidden" id="save_cancel" onclick="$(\'#jqgrid_ilcancel\').click();"><i class="fa fa-sign-out "></i></button>


        </div>
        <div id="jqgcontainer" style="height:360px;">
            <table id="jqgrid"></table>
            <div id="pjqgrid"></div>
        </div>
        <div class="margin-top-10 well well-sm well-light">

                <a id="aumenta_altezza" class="btn btn-circle btn-default pull-right"><i class="fa fa-arrow-down"></i></a>
                <a id="diminuisci_altezza" class="btn btn-circle btn-default pull-right"><i class="fa fa-arrow-up"></i></a>
                <span class="btn btn-circle btn-default pull-right" data-action="minifyMenu" style=""><i class="fa fa-arrow-left"></i></span>
                <div class="clearfix"></div>
                <hr>
            <div class="text-center">
            <h1 class="txt-bold">se hai selezionato più articoli...</h1>
            <hr>
            <form class="form-inline text-right" role="form">
                <fieldset>
                    <div class="form-group">
                        <label class="" for="applica_percentuale">...varia il il loro prezzo di questa percentuale:</label>
                        <input type="text" class="form-control" id="valore_percentuale" placeholder="Inserisci una percentuale">
                    </div>
                    <button type="submit" class="btn btn-primary" id="variazione_percentuale">
                        Applica
                    </button>
                </fieldset>
                <p class="note">La variazione percentuale può anche essere negativa;</p>
            
            </form>
            <hr>
            <form class="form-inline text-right" role="form">
                <fieldset>
                    <div class="form-group">
                        <label class="input" for="applica_assoluto">...varia il il loro prezzo di questa cifra:</label>
                        <input type="text" class="form-control" id="valore_assoluto" placeholder="Inserisci un valore">
                    </div>
                    <button type="submit" class="btn btn-primary" id="variazione_assoluta">
                        Applica
                    </button>
                </fieldset>
                <p class="note">Il valore da aggiungere può anche essere negativo. Se un articolo dovesse avere il suo prezzo negativo, questo verrà rettificato a zero.</p>
            </form>
            <hr>
            <form class="form-inline text-right" role="form">
                <fieldset>
                    <div class="form-group">
                        <label class="input" for="imposta_assoluto">...imposta il loro prezzo a questa cifra:</label>
                        <input type="text" class="form-control" id="imposta_assoluto" placeholder="Inserisci un valore">
                    </div>
                    <button type="submit" class="btn btn-primary" id="imposta_assoluta">
                        Applica
                    </button>
                </fieldset>
                <p class="note">Tutti gli articoli selezionati avranno lo stesso prezzo</p>
            </form>
            <hr>
            <form class="form-inline text-right" role="form">
                <fieldset>
                    <button type="submit" class="btn btn-primary" id="imposta_disabilita">
                        Disablitali
                    </button>
                </fieldset>
                <p class="note">Gli articoli disabilitati non verranno visualizzati nella pagina "Compra"</p>
            </form>
            </div>
         </div>
      ';

$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>true,
                    "deletebutton"=>false,
                    "colorbutton"=>true);
$wg_articoli = $ui->create_widget($options);
$wg_articoli->id = "wg_listino_articoli";
$wg_articoli->body = array("content" => $a,"class" => "no-padding");
$wg_articoli->header = array(
    "title" => '<h2>Articoli nel listino temporaneo</h2>',
    "icon" => 'fa fa-cube'
    );



?>

<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo $wg_articoli->print_html(); ?>
        </article>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
        </article>
    </div>

    <br /><br />
</section>
<!-- Dynamic Modal -->
                        <div class="modal fade" id="remoteModalImport" tabindex="-1" role="dialog" aria-labelledby="remoteModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">

                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="remoteModalConfirm" tabindex="-2" role="dialog" aria-labelledby="remoteModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content" >
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel">Caricamento effettuato: <span id="remoteModalTitle"></span></h4>
                                </div>
                                <div class="modal-body" id="remoteModalConfirmContent">

                                </div>
                                </div>
                            </div>
                        </div>
<!-- /.modal -->

<script type="text/javascript">

    $(document).prop('title', 'ReteDes::<?echo $page_title?>');
    pageSetUp();


    var dz;
    var myDropZone;
    var file_code;
    var ext;
    var lastSel = 0;
    var editingRowId;
    var isEditing = false;


    var pagefunction = function(){



        <?php if(!$incompleto){ ?>
        loadScript("js/plugin/jqgrid/jquery.jqGrid.min.js", run_jqgrid_function);
        <?php } ?>


        function run_jqgrid_function() {
            var rowid=0;

            var is_editable=true;

            jQuery("#jqgrid").jqGrid({
               
               url:'ajax_rd4/ordini/inc/articoli_temp.php?id_listino=<?php echo $id_listino?>&id_ordine=<?php echo $id_ordine?>',
            datatype: "json",
               colNames:[   'Codice',
                            'Descrizione',
                            'Prezzo',

                            'U.M',
                            'Misura',
                            'ingombro',

                            'Qta S.',
                            'Qta M.',
                            'Note',

                            'U',
                            'T1',
                            'T2',
                            'T3',

                            'D'

                            ],
               colModel:[
                   {name:'codice',index:'codice', width:60,editable:is_editable},
                   {name:'descrizione_articoli',index:'descrizione_articoli', width:150, align:"left",editable:is_editable},
                   {name:'prezzo',index:'prezzo', width:50,align:"right",editable:is_editable,search:false},

                   {name:'u_misura',index:'u_misura', width:20,align:"right",editable:is_editable,search:false},
                   {name:'misura',index:'misura', width:30,align:"left",editable:is_editable,search:false},

                   {name:'ingombro',index:'ingombro', width:50, editable:is_editable,search:false,edittype:'textarea'},

                   {name:'qta_scatola',index:'qta_scatola', width:25, align:"center",editable:is_editable,search:false},
                   {name:'qta_minima',index:'qta_minima', width:25, align:"center",editable:is_editable,search:false},

                   {name:'articoli_note',index:'articoli_note', width:70, sortable:false,editable:is_editable,search:true,edittype:'textarea'},

                   {name:'articoli_unico',index:'articoli_unico', width:15, align:"center",editable:is_editable,edittype:'checkbox',editoptions: { value:"1:0"},formatter: "checkbox", search:false},
                   {name:'articoli_opz_1',index:'articoli_opz_1', width:50,align:"left",editable:is_editable},
                   {name:'articoli_opz_2',index:'articoli_opz_2', width:50,align:"left",editable:is_editable},
                   {name:'articoli_opz_3',index:'articoli_opz_3', width:50,align:"left",editable:is_editable},

                   {name:'is_disabled',index:'is_disabled', width:15,align:"center",editable:is_editable,edittype:'checkbox',editoptions: { value:"1:0"},formatter: "checkbox", search:false},

               ],

               multiselect: true,
               beforeSelectRow:function(id){
                   console.log('Before select row ' + id);
                   if(isEditing === false){
                        return true;
                   }else{
                       //ko("Edit leaved");
                       isEditing = false;
                       $('#jqgrid_iledit').click();
                       return false;

                   }

               },
               onSelectRow:function(id){

                    console.log("Last sel: " + lastSel);
                    console.log("Actual id: " + id)
                    rowid=id;
                    var selRows = $(this).jqGrid('getGridParam','selarrrow');
                    if (selRows.length === 0) {
                        //alert ("no rows are selected now");
                        $('#duplica_riga').addClass('hidden');
                        $('#edita_riga').addClass('hidden');
                        $('#save_riga').addClass('hidden');
                        $('#save_cancel').addClass('hidden');
                    } else {
                        //alert ("a row is selected now");
                        // you can disable the button
                        $('#duplica_riga').removeClass('hidden');
                        $('#edita_riga').removeClass('hidden');
                    }
                    //$("#jqgrid").addRowData(rowid,data, position, lastsel);
                    lastSel=id;
               },
               rowNum:20,
               rowList:[20,50,500,5000],
               pager: '#pjqgrid',
               gridview: true,
               rowattr: function (rd) {
                    if (rd.is_disabled === "1") {
                        return {"class": "danger"};
                    }
                },
               //sortname: 'id_articolo',
               viewrecords: true,
                //sortorder: "desc",
            //editurl: "ajax_rd4/listini/inc/articoli.php?id_listino=<?php echo $id_listino?>",
            editurl: "ajax_rd4/ordini/inc/articoli_temp.php?id_listino=<?php echo $id_listino?>&id_ordine=<?php echo $id_ordine?>",
            caption:"",
            gridComplete : function(){
                console.log("Grid Complete!");

            }

        });

        //-------------------FORMATTERS
        function codiceFormatter (cellvalue, options, rowObject)
            {
               console.log("Formatter: "+ options.rowId);
            }


        jQuery("#jqgrid").jqGrid('navGrid','#pjqgrid',{
                edit:false,
                add:false,
                <?php if($noie>0){ ?>
                del:false,
                <?php }else{ ?>
                del:true,
                <?php } ?>
                search:false
        });


        jQuery("#jqgrid").jqGrid('filterToolbar',{});
        jQuery("#jqgrid").jqGrid('inlineNav', "#pjqgrid",{
                addParams: {
                    addRowParams: {

                        aftersavefunc: function(rowid, response) {

                            $('#save_riga').addClass('hidden');
                            $('#save_cancel').addClass('hidden');
                            console.log(response);
                            data = $.parseJSON(response.responseText);
                            console.log(data.result);
                            if(data.result!="OK"){
                                ko(data.msg);
                                console.log("no: "+ rowid);
                                //jQuery('#jqgrid').jqGrid("showAddEditButtons");
                                //$('#save_riga').removeClass('hidden');
                                //$('#save_cancel').removeClass('hidden');
                                jQuery('#jqgrid').delRowData(rowid);
                                //$('#jqgrid').jqGrid('delRowData',rowid);
                                return false;
                            }else{

                                var newId = data.id,
                                $self = $(this),
                                idPrefix = $self.jqGrid("getGridParam", "idPrefix", newId),
                                selrow = $self.jqGrid("getGridParam", "selrow", newId),
                                selArrayRow = $self.jqGrid("getGridParam", "selarrrow", newId),
                                oldId = $.jgrid.stripPref(idPrefix, rowid),
                                dataIndex = $self.jqGrid("getGridParam", "_index", newId),
                                i;
                                // update id in the _index
                                if (dataIndex[oldId] !== undefined) {
                                    dataIndex[newId] = dataIndex[oldId];
                                    delete dataIndex[oldId];
                                }
                                // update id in <tr>
                                $("#" + $.jgrid.jqID(rowid)).attr("id", idPrefix + newId);
                                // update id of selected row
                                if (selrow === rowid) {
                                    $self.jqGrid("setGridParam", { selrow: idPrefix + newId });
                                }
                                // update id in case of usage multiselect: true option
                                if ($.isArray(selArrayRow)) {
                                    i = $.inArray(rowid, selArrayRow);
                                    if (i >= 0) {
                                        selArrayRow[i] = idPrefix + newId;
                                    }
                                }
                                // the next line is required if we use ajaxRowOptions: { async: true }
                                $self.jqGrid("showAddEditButtons");


                                ok(data.id);
                            }


                        }
                    }
                },
                editParams: {
                    keys: true,

                    oneditfunc: function (id) {
                            isEditing = true;
                            console.log("editing : " + id);
                            $('#save_riga').removeClass('hidden');
                            $('#save_cancel').removeClass('hidden');
                            editingRowId = id;
                    },

                    afterrestorefunc: function (id) {
                        isEditing = false;
                        $('#save_riga').addClass('hidden');
                        $('#save_cancel').addClass('hidden');
                        console.log("afterrestore " + id );

                    },
                    successfunc: function(response){
                        console.log(response)

                        isEditing = false;

                        $('#save_riga').addClass('hidden');
                        $('#save_cancel').addClass('hidden');

                        data = $.parseJSON(response.responseText);
                        if(data.result==="OK"){
                                            ok(data.msg);
                                            return true ;
                                        }else{
                                            ko(data.msg);
                                            jQuery('#jqgrid').restoreRow(data.id);
                                            //$self.jqGrid("showAddEditButtons");
                                            return false;
                                        }
                    }
                }
            });

            $(window).on('resize.jqGrid', function() {
                console.log ("resizing ");

                    jQuery("#jqgrid").jqGrid('setGridWidth', $("#jqgcontainer").width());
                    jQuery("#jqgrid").jqGrid('setGridHeight', $("#jqgcontainer").height() - ($("#gbox_jqgrid").height() - $('#gbox_jqgrid .ui-jqgrid-bdiv').height()));

            });

            $('#duplica_riga').click(function(){
               console.log("duplica riga: " + rowid);
               if(rowid>0){
               $.ajax({
                          type: "POST",
                          url: "ajax_rd4/listini/_act.php",
                          dataType: 'json',
                          data: {act: "clona_articolo", id : rowid},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                ok(data.msg);
                                
                                $("#jqgrid").jqGrid('setGridParam',{datatype:'json'}).trigger('reloadGrid');
                            }else{
                                ko(data.msg)
                            ;}

                        });
               }else{
                    ko("Seleziona una riga per duplicarla");
               }
            });



            $('.ui-search-toolbar').hide();

            // remove classes
            $(".ui-jqgrid").removeClass("ui-widget ui-widget-content");
            $(".ui-jqgrid-view").children().removeClass("ui-widget-header ui-state-default");
            $(".ui-jqgrid-labels, .ui-search-toolbar").children().removeClass("ui-state-default ui-th-column ui-th-ltr");
            $(".ui-jqgrid-pager").removeClass("ui-state-default");
            $(".ui-jqgrid").removeClass("ui-widget-content");

            // add classes
            $(".ui-jqgrid-htable").addClass("table table-bordered table-hover");
            $(".ui-jqgrid-btable").addClass("table table-bordered table-striped");

            $(".ui-pg-div").removeClass().addClass("btn btn-sm btn-primary");
            $(".ui-icon.ui-icon-plus").removeClass().addClass("fa fa-plus");
            $(".ui-icon.ui-icon-pencil").removeClass().addClass("fa fa-pencil");
            $(".ui-icon.ui-icon-trash").removeClass().addClass("fa fa-trash-o").parent(".btn-primary").removeClass("btn-primary").addClass("btn-danger");
            $(".ui-icon.ui-icon-search").removeClass().addClass("fa fa-search");
            $(".ui-icon.ui-icon-refresh").removeClass().addClass("fa fa-refresh");
            $(".ui-icon.ui-icon-disk").removeClass().addClass("fa fa-save").parent(".btn-primary").removeClass("btn-primary").addClass("btn-success");
            $(".ui-icon.ui-icon-cancel").removeClass().addClass("fa fa-times").parent(".btn-primary").removeClass("btn-primary").addClass("btn-danger");

            $(".ui-icon.ui-icon-seek-prev").wrap("<div class='btn btn-sm btn-default'></div>");
            $(".ui-icon.ui-icon-seek-prev").removeClass().addClass("fa fa-backward");

            $(".ui-icon.ui-icon-seek-first").wrap("<div class='btn btn-sm btn-default'></div>");
            $(".ui-icon.ui-icon-seek-first").removeClass().addClass("fa fa-fast-backward");

            $(".ui-icon.ui-icon-seek-next").wrap("<div class='btn btn-sm btn-default'></div>");
            $(".ui-icon.ui-icon-seek-next").removeClass().addClass("fa fa-forward");

            $(".ui-icon.ui-icon-seek-end").wrap("<div class='btn btn-sm btn-default'></div>");
            $(".ui-icon.ui-icon-seek-end").removeClass().addClass("fa fa-fast-forward");
            $('#jqgrid_ilcancel').hide();
            $('#jqgrid_ilsave').hide();
            $('#jqgrid_iladd').hide();
            $('#jqgrid_iledit').hide();
            //$('#del_jqgrid').hide();


            $('#aumenta_altezza').click(function(){
                $('#jqgcontainer').height($('#jqgcontainer').height()+200);
                jQuery("#jqgrid").jqGrid('setGridHeight', $("#jqgcontainer").height() - ($("#gbox_jqgrid").height() - $('#gbox_jqgrid .ui-jqgrid-bdiv').height()));
            });
            $('#diminuisci_altezza').click(function(){
                if(($('#jqgcontainer').height()-200)>300){
                $('#jqgcontainer').height($('#jqgcontainer').height()-200);
                jQuery("#jqgrid").jqGrid('setGridHeight', $("#jqgcontainer").height() - ($("#gbox_jqgrid").height() - $('#gbox_jqgrid .ui-jqgrid-bdiv').height()));
                }
            });
            //resize to fit page size
            jQuery("#jqgrid").jqGrid('setGridWidth', $("#jqgcontainer").width());
            jQuery("#jqgrid").jqGrid('setGridHeight', $("#jqgcontainer").height() - ($("#gbox_jqgrid").height() - $('#gbox_jqgrid .ui-jqgrid-bdiv').height()));

            $("#variazione_percentuale").click(function(){
                var grid = jQuery("#jqgrid").jqGrid();
                var rowKey = grid.getGridParam("selrow");
                if (!rowKey)
                    ko("Nessun articolo selezionato");
                else {
                    var selectedIDs = grid.getGridParam("selarrrow");
                    var valore_percentuale = $("#valore_percentuale").val();
                    $.ajax({
                      type: "POST",
                      url: "ajax_rd4/listini/_act.php",
                      dataType: 'json',
                      data: {act: "varia_percentuale", selectedIDs : selectedIDs, id_listino:<?php echo $id_listino; ?>, valore_percentuale:valore_percentuale},
                      context: document.body
                    }).done(function(data) {
                        if(data.result=="OK"){
                            ok(data.msg);
                            $('#jqgrid').trigger( 'reloadGrid' );
                            for (var i = 0; i < selectedIDs.length; i++) {
                                grid.setSelection(selectedIDs[i]);
                            }
                        }else{
                            ko(data.msg)
                        ;}
                    });
                }
            })
            $("#variazione_assoluta").click(function(){
                var grid = jQuery("#jqgrid").jqGrid();
                var rowKey = grid.getGridParam("selrow");
                if (!rowKey)
                    ko("Nessun articolo selezionato");
                else {
                    var selectedIDs = grid.getGridParam("selarrrow");
                    var valore_assoluto = $("#valore_assoluto").val();
                    $.ajax({
                      type: "POST",
                      url: "ajax_rd4/listini/_act.php",
                      dataType: 'json',
                      data: {act: "varia_assoluto", selectedIDs : selectedIDs, id_listino:<?php echo $id_listino; ?>, valore_assoluto:valore_assoluto},
                      context: document.body
                    }).done(function(data) {
                        if(data.result=="OK"){
                            ok(data.msg);
                            $('#jqgrid').trigger( 'reloadGrid' );
                        }else{
                            ko(data.msg)
                        ;}
                    });
                }
            })




        } // end jqgrid init


        //-------------------------HELP
        <?php echo help_render_js($page_id); ?>
        //-------------------------HELP



    } // end pagefunction



    loadScript("js/plugin/jqgrid/grid.locale-en.min.js", pagefunction);

    </script>
