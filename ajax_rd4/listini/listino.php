<?php
require_once("inc/init.php");
$ui = new SmartUI;
$converter = new Encryption;

$page_title = "Listino ";
$id_listino = CAST_TO_INT($_GET["id"]);
if($id_listino==0){echo "id missing";die();}

$stmt = $db->prepare("SELECT * FROM  retegas_listini WHERE id_listini = :id_listini AND id_utenti='"._USER_ID."';");
$stmt->bindParam(':id_listini', $id_listino, PDO::PARAM_INT);
$stmt->execute();
if($stmt->rowCount()==1){
    $proprietario="true";
    $label ='<span class="pull-right label bg-color-greenLight" style="color:#FFF !important">SEI IL PROPRIETARIO</span>';
    $editable = "editable";
}else{
    $proprietario="false";
    $editable = "";
}

$stmt = $db->prepare("SELECT * FROM  retegas_listini WHERE id_listini = :id_listino LIMIT 1;");
$stmt->bindParam(':id_listino', $id_listino, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if($row["tipo_listino"]==1){$tipo="Standard";}else{$tipo="Magazzino";}

$s = '  <div>
            <label for="descrizione_listini">Descrizione</label>
            <p class="font-lg editable" id="descrizione_listini" data-type="text" data-pk="'.$row["id_listini"].'">'.$row["descrizione_listini"].'</p>
            <label for="data_valido">Termine di validità</label>
            <p class="font-lg" id="data_valido">'.conv_datetime_from_db($row["data_valido"]).'</p>
            <label for="tipo_listino">Tipo listino</label>
            <p class="font-lg" id="tipo_listino">'.$tipo.'</p>
            <label for="tipo_listino">Pubblico / Privato</label>
            <p class="font-lg" id="tipo_listino">'.$row["is_privato"].'</p>
            <div class="btn-group-vertical btn-block">
                <a class="btn btn-default">Clona il listino</a>
                <a class="btn btn-default">Elimina il listino</a>
            </div>
        </div>';


$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>true,
                    "deletebutton"=>false,
                    "colorbutton"=>true);
$wg_listino = $ui->create_widget($options);
$wg_listino->id = "wg_listino_scheda";
$wg_listino->body = array("content" => $s,"class" => "");
$wg_listino->header = array(
    "title" => '<h2>Scheda listino</h2>',
    "icon" => 'fa fa-cubes'
    );

//-----------------------------------------------------ARTICOLI

$a = '<div class="" >
        <div class="well well-sm">

        </div>
        <div id="jqgcontainer">
            <table id="jqgrid"></table>
            <div id="pjqgrid"></div>
        </div>
      </div>       ';

$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>true,
                    "deletebutton"=>false,
                    "colorbutton"=>true);
$wg_articoli = $ui->create_widget($options);
$wg_articoli->id = "wg_listino_articoli";
$wg_articoli->body = array("content" => $a,"class" => "no-padding");
$wg_articoli->header = array(
    "title" => '<h2>Articoli nel listino</h2>',
    "icon" => 'fa fa-cube'
    );

?>
<div class="inbox-nav-bar no-content-padding">
    <h1 class="page-title txt-color-blueDark"><i class="fa fa-fw fa-cubes"></i> <?php echo $row["descrizione_listini"]; ?>  &nbsp;
        <?php echo $label; ?>
    </h1>

</div>

<section id="widget-grid" class="margin-top-10">

    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <?php echo help_render_html('listino',$page_title); ?>
        </article>
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <?php echo $wg_listino->print_html(); ?>
        </article>

    </div>

    <hr>

    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo $wg_articoli->print_html(); ?>
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
    loadScript("js/plugin/jqgrid/jquery.jqGrid.min.js", run_jqgrid_function);

        function run_jqgrid_function() {
            var rowid;
            var lastSel;
            var is_editable = <?php echo $proprietario; ?>;


            jQuery("#jqgrid").jqGrid({
               url:'ajax_rd4/listini/inc/articoli.php?id_listino=<?php echo $id_listino?>',
            datatype: "json",
               colNames:[   'Codice',
                            'Descrizione',
                            'Prezzo',

                            'U.M',
                            'Misura',

                            'Qta S.',
                            'Qta M.',

                            'U',
                            'ingombro',
                            'T1',
                            'T2',
                            'T3',
                            'Note'
                            ],
               colModel:[
                   {name:'codice',index:'codice', width:60,editable:is_editable},
                   {name:'descrizione_articoli',index:'descrizione_articoli', width:150, align:"left",editable:is_editable},
                   {name:'prezzo',index:'prezzo', width:50,align:"right",editable:is_editable,search:false},

                   {name:'u_misura',index:'u_misura', width:20,align:"right",editable:is_editable,search:false},
                   {name:'misura',index:'misura', width:30,align:"left",editable:is_editable,search:false},

                   {name:'qta_scatola',index:'qta_scatola', width:25, align:"center",editable:is_editable,search:false},
                   {name:'qta_minima',index:'qta_minima', width:25, align:"center",editable:is_editable,search:false},
                   {name:'articoli_unico',index:'articoli_unico', width:25, align:"center",editable:is_editable,search:false},

                   {name:'ingombro',index:'ingombro', width:75, editable:is_editable,search:false,edittype:'textarea'},

                   {name:'articoli_opz_1',index:'articoli_opz_1', width:50,align:"left",editable:is_editable},
                   {name:'articoli_opz_2',index:'articoli_opz_2', width:50,align:"left",editable:is_editable},
                   {name:'articoli_opz_3',index:'articoli_opz_3', width:50,align:"left",editable:is_editable},

                   {name:'articoli_note',index:'articoli_note', width:75, sortable:false,editable:is_editable,search:true,edittype:'textarea'},
               ],
               width: '100%',
               height: 600,

               onSelectRow: function(id){
                 var rowid = id;
                 console.log("selected " + id)
                 if(id && id!==lastSel){
                    jQuery('#jqgrid').restoreRow(lastSel);
                    lastSel=id;
                 }
                 //jQuery('#jqgrid').editRow(id, true);
                 jQuery("#jqgrid").jqGrid('editRow',id,
                    {
                        keys : true,
                        oneditfunc: function() {
                            //alert ("edited");
                        },
                        successfunc: function(response) {
                            var data = JSON.stringify(eval("(" + response.responseText + ")"));
                            var json = JSON.parse(data);
                            console.log(json.result);
                            if(json.result==="OK"){
                                ok(json.msg);
                                return true ;
                            }else{
                                ko(json.msg);
                                jQuery('#jqgrid').restoreRow(id);
                                return false;
                            }
                        }
                    });
               },
               rowNum:20,
               rowList:[20,50,500,5000],
               pager: '#pjqgrid',
               //sortname: 'id_articolo',
            viewrecords: true,
            //sortorder: "desc",
            editurl: "ajax_rd4/listini/inc/articoli.php?id_listino=<?php echo $id_listino?>",

            caption:""
        });
        jQuery("#jqgrid").jqGrid('navGrid','#pjqgrid',{
                edit:false,
                add:false,
                del:false
        });


        jQuery("#jqgrid").jqGrid('filterToolbar',{});
        //jQuery("#jqgrid").jqGrid('inlineNav', "#pjqgrid");

        $(window).on('resize.jqGrid', function() {
            $("#jqgrid").jqGrid('setGridWidth', $("#content").width());

        });



        } // end jqgrid init


        //-------------------------HELP
        <?php echo help_render_js("listino"); ?>
        //-------------------------HELP

        $(".editable").editable({
                                url: 'ajax_rd4/listini/_act.php',
                                ajaxOptions: { dataType: 'json' },
                                success: function(response, newValue) {
                                    console.log(response);
                                    if(response.result == 'KO'){
                                        return response.msg;

                                    }else{
                                        ok(response.msg);

                                    }
                                }
                            });


    } // end pagefunction



    loadScript("js/plugin/jqgrid/grid.locale-en.min.js", loadScript("js/plugin/x-editable/x-editable.min.js", pagefunction));
</script>
