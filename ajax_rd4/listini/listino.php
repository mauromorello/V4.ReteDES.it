<?php
$page_title = "Listino ";
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.listino.php");

$ui = new SmartUI;
$converter = new Encryption;

//var_dump($_POST, $_FILES);

$id_listino = CAST_TO_INT($_GET["id"]);
if($id_listino==0){
    $id_listino = CAST_TO_INT($_POST["id"]);
    if($id_listino==0){
        echo "id missing";die();
    }
}

$L = new listino($id_listino);


if(posso_gestire_listino($id_listino)){
    $proprietario="true";
    //$buttons[] ='<a href="javascript:void(0);"><i class="fa fa-unlock fa-2x fa-border text-success" rel="popover" data-placement="left" data-original-title="Permessi" data-content="Puoi lavorare su questo listino perchè ne sei il proprietario"></i></a>';
    $editable = "editable";
}else{
    $proprietario="false";
    $editable = "";

    //$buttons[] ='<a href="javascript:void(0);"><i class="fa fa-lock fa-2x fa-border text-danger" rel="popover" data-placement="left" data-original-title="Permessi" data-content="Non sei l\'autore di questo listino."></i></a>';
}

$stmt = $db->prepare("SELECT * FROM  retegas_listini WHERE id_listini = :id_listino LIMIT 1;");
$stmt->bindParam(':id_listino', $id_listino, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$L->is_completo){
//if((conv_date_from_db($row["data_valido"]))=="00/00/0000" OR ($row["id_tipologie"]==0)){
    $incompleto=true;
}else{
    $incompleto=false;
}

if($row["tipo_listino"]==1){
    $tipo="Magazzino";
}else{
    $tipo="Normale";
}
if($row["is_privato"]==1){
    $privato="Privato";
}else{
    $privato="Pubblico";
}



$stmt = $db->prepare("SELECT U.fullname,G.descrizione_gas  FROM  maaking_users U inner join retegas_gas G on G.id_gas=U.id_gas WHERE userid = :userid;");
$stmt->bindParam(':userid', $row["id_utenti"], PDO::PARAM_INT);
$stmt->execute();
$p = $stmt->fetch(PDO::FETCH_ASSOC);
$fullname_p = $p["fullname"];
$gas_p = $p["descrizione_gas"];


$stmt = $db->prepare("SELECT *  FROM  retegas_ditte WHERE id_ditte = :id_ditte;");
$stmt->bindParam(':id_ditte', $row["id_ditte"], PDO::PARAM_INT);
$stmt->execute();
$d = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $db->prepare("SELECT count(*) as count FROM  retegas_articoli WHERE id_listini = :id_listino");
$stmt->bindParam(':id_listino', $id_listino, PDO::PARAM_INT);
$stmt->execute();
$a = $stmt->fetch(PDO::FETCH_ASSOC);
$n_articoli = $a["count"];

//Ordini aperti ora
$stmt = $db->prepare("SELECT count(*) as count FROM  retegas_ordini WHERE id_listini = :id_listino AND data_apertura<NOW() and data_chiusura>NOW() LIMIT 1;");
$stmt->bindParam(':id_listino', $id_listino, PDO::PARAM_INT);
$stmt->execute();
$r = $stmt->fetch(PDO::FETCH_ASSOC);
$ordini_aperti = $r["count"];

$stmt = $db->prepare("SELECT count(*) as count FROM  retegas_ordini WHERE id_listini = :id_listino AND data_apertura>NOW() LIMIT 1;");
$stmt->bindParam(':id_listino', $id_listino, PDO::PARAM_INT);
$stmt->execute();
$r = $stmt->fetch(PDO::FETCH_ASSOC);
$ordini_futuri = $r["count"];

$stmt = $db->prepare("SELECT count(*) as count FROM  retegas_ordini WHERE id_listini = :id_listino AND data_chiusura<NOW() LIMIT 1;");
$stmt->bindParam(':id_listino', $id_listino, PDO::PARAM_INT);
$stmt->execute();
$r = $stmt->fetch(PDO::FETCH_ASSOC);
$ordini_chiusi = $r["count"];

$stmt = $db->prepare("SELECT count(id_utente) as count FROM  retegas_ordini WHERE id_listini = :id_listino  GROUP BY id_utente LIMIT 1;");
$stmt->bindParam(':id_listino', $id_listino, PDO::PARAM_INT);
$stmt->execute();
$r = $stmt->fetch(PDO::FETCH_ASSOC);
$numero_gestori = $r["count"];

$stmt = $db->prepare("SELECT count(id_utente) as count FROM  retegas_ordini WHERE id_listini = :id_listino  GROUP BY id_utente LIMIT 1;");
$stmt->bindParam(':id_listino', $id_listino, PDO::PARAM_INT);
$stmt->execute();
$r = $stmt->fetch(PDO::FETCH_ASSOC);
$numero_gas = $r["count"];

if($incompleto){
$alert_incompleto= '<i class="fa fa-warning fa-2x text-danger"></i> ';
}

$rows = $L->lista_referenti_extra();
if(count($rows)>0){
    foreach($rows as $row){
       if($row["valore_int"]==0){
           if($L->id_utenti==_USER_ID){
                $op1='<div class="btn-group pull-right">
                                    <a class="btn btn-xs btn-success attiva_extra" href="javascript:void(0);" data-id_listini="'.$L->id_listini.'" data-id_user="'.$row["id_user"].'"><i class="fa fa-check"></i> attiva</a>
                                    <a class="btn btn-xs btn-danger elimina_extra" href="javascript:void(0);" data-id_listini="'.$L->id_listini.'" data-id_user="'.$row["id_user"].'"><i class="fa fa-times"></i> elimina</a>
                                </div>';
                $op2='<div class="btn-group pull-right">
                                    <a class="btn btn-xs btn-danger elimina_extra" href="javascript:void(0);" data-id_listini="'.$L->id_listini.'" data-id_user="'.$row["id_user"].'"><i class="fa fa-times"></i></a>
                                </div>';
           }else{
                $op1='';
                $op2='';
           }

           $gestori_extra_attesa .= '<li>
                            <span class="read">
                                <a href="javascript:void(0);" class="msg">
                                    <img src="'.src_user($row["id_user"]).'" alt="" class="margin-top-5" width="32" height="32" />
                                    <span class="subject"><strong>'.$row["fullname"].'</strong></span>
                                    <span class="subject font-xs">'.$row["descrizione_gas"].'</span>
                                </a>
                                '.$op1.'
                            </span>
                        </li>';
       }else{
        $gestori_extra_ok.= '<li>
                            <span class="read">
                                <a href="javascript:void(0);" class="msg">
                                    <img src="'.src_user($row["id_user"]).'" alt="" class="margin-top-5" width="32" height="32" />
                                    <span class="subject"><strong>'.$row["fullname"].'</strong></span>
                                    <span class="subject font-xs">'.$row["descrizione_gas"].'</span>
                                </a>
                                '.$op2.'
                            </span>
                        </li>';


       }
    }

}

    if(empty($gestori_extra_ok)){$gestori_extra_ok='<li>Nessuno.</li>';}
    if(empty($gestori_extra_attesa)){$gestori_extra_attesa='<li>Nessuno.</li>';}

$s = '  <div>

            <img id="img_listino" src="img_rd4/t_'.$L->id_tipologie.'_240.png" alt="" class="air air-top-right margin-top-5" width="80" height="80">
            <label for="descrizione_listini">Identificativo:</label>
            <p class="font-lg" id="id_listino" >#'.$L->id_listini.'</p>
            <label for="descrizione_listini">Descrizione</label>
            <p class="font-lg '.$editable.'" id="descrizione_listini" data-type="text" data-pk="'.$L->id_listini.'">'.$L->descrizione_listini.'</p>
            <label for="data_valido">'.$alert_incompleto.' Termine di validità</label>
            <p class="font-lg '.$editable.'" id="data_valido" data-type="date" data-pk="'.$L->id_listini.'" data-format="dd/mm/yyyy">'.$L->data_valido.'</p>
            <label for="tipo_listino" >Tipo listino</label>
            <p class="font-lg '.$editable.'_tipo" id="tipo_listino" data-type="select" data-pk="'.$L->id_listini.'" data-value="'.$L->tipo_listino.'">'.$tipo.'</p>
            <label for="tipo_listino">Pubblico / Privato</label>
            <p class="font-lg '.$editable.'_privato" id="is_privato" data-type="select" data-pk="'.$L->id_listini.'" data-value="'.$L->is_private.'">'.$privato.'</p>
            <label for="id_tipologie">'.$alert_incompleto.' Categoria</label>
            <p class="font-lg '.$editable.'_tipologia" id="id_tipologie" data-type="select" data-pk="'.$L->id_listini.'" data-value="'.$L->id_tipologie.'">'.$L->descrizione_tipologia.'</p>
            <div class="row padding-10">
            <h4>Gestori extra in attesa:</h4>
            <ul class="list-unstyled">'.$gestori_extra_attesa.'</ul></div>
            <h4>Gestori extra:</h4>
            <ul class="list-unstyled">'.$gestori_extra_ok.'</ul></div>
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
$a = '
        <div class="well well-sm well-light">
            <button rel="popover-hover" data-placement="bottom" data-original-title="Filtra" data-content="Filtra tutto il listino in base a cosa inserisci nei rispettivi campi. Clicca per far comparire la riga del filtro." class="btn btn-default btn-circle btn-lg" onclick="$(\'.ui-search-toolbar\').toggle();"><i class="fa fa-filter "></i></button>
        </div>
        <div id="jqgcontainer" style="height:360px;">
            <table id="jqgrid"></table>
            <div id="pjqgrid"></div>
        </div>
        <div class="margin-top-10 well well-sm well-light">
                <span>Usa le frecce a destra per ingrandire la tabella. Seleziona quante righe devono essere visualizzate per ogni pagina.</span>
                <a id="aumenta_altezza" class="btn btn-circle btn-default pull-right"><i class="fa fa-arrow-down"></i></a>
                <a id="diminuisci_altezza" class="btn btn-circle btn-default pull-right"><i class="fa fa-arrow-up"></i></a>
                <span class="btn btn-circle btn-default pull-right" data-action="minifyMenu" style=""><i class="fa fa-arrow-left"></i></span>
                <div class="clearfix"></div>
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
    "title" => '<h2>Articoli nel listino</h2>',
    "icon" => 'fa fa-cube'
    );
//OPERAZIONI -----------------------------------

if($proprietario=="true"){
    if(!$incompleto){
        $upload='<hr>
                <label>Importa articoli</label>
                <div class="btn-group pull-right" id="clona_group">
                    <span class="btn btn-default fileinput-button dz-clickable">
                        <i class="fa fa-spinner fa-spin hidden" id="loadingSpinner"></i>
                        <span>Carica...</span>
                    </span>
                </div>
                <div class="clearfix "></div>
                <div class="progress progress-micro margin-top-10">
                    <div class="progress-bar progress-bar-primary" role="progressbar" style="width: 0;" id="loadingprogress"></div>
                </div>';
    }else{
        $upload='';
    }
    if($n_articoli>0){
        if($ordini_aperti==0){
            $cancella =' <hr><a class="btn btn-danger btn-block" id="del_articoli">ELIMINA ARTICOLI</a>';
        }
    }else{
        $cancella ='<hr><a class="btn btn-danger btn-block" id="del_listino">ELIMINA LISTINO</a>';
    }


}else{
    $upload='';
    $cancella='';
    $aiuta_a_gestire='<hr><a class="btn btn-primary btn-block" id="aiuta_a_gestire">AIUTA A GESTIRE</a>';
}
if($n_articoli>0){
    if(posso_gestire_listino($L->id_listini)){
        $gestisci_articoli ='<hr><a class="btn btn-info btn-block" id="gestisci_articoli">GESTISCI ARTICOLI</a>';
    }
    $esporta =' <hr class="margin-top-5">
        <label for="exp_group">Esporta questo listino:</label>
        <div class="btn-group pull-right" id="exp_group">
                <a class="btn btn-default" id="export_listino_HTM">HTML</a>
                <a class="btn btn-default" id="export_listino_CSV">CSV</a>
                <div class="btn-group">
                            <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                EXCEL <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="javascript:void(0);" id="export_listino_XLS">XLS (Excel5)</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" id="export_listino_XLSX">XLSX (Excel2007)</a>
                                </li>
                            </ul>
                        </div>
       </div>';
    $clona='<hr><a class="btn btn-success btn-block" id="clona_listino">CLONA LISTINO</a>';
}else{
    $esporta ='';
    $clona='';
}





$o = '
       '.$esporta.'
       '.$upload.'
       '.$clona.'
       '.$cancella.'
       '.$gestisci_articoli.'
       '.$aiuta_a_gestire.'
       <hr>
';

$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>false,
                    "deletebutton"=>false,
                    "colorbutton"=>true);
$wg_articoli_oper = $ui->create_widget($options);
$wg_articoli_oper ->id = "wg_listino_articoli_operazioni";
$wg_articoli_oper ->body = array("content" => $o,"class" => "");
$wg_articoli_oper ->header = array(
    "title" => '<h2>Operatività</h2>',
    "icon" => 'fa fa-gear'
    );

//INCOMPLETO

if($incompleto){
    $inco = '<div class="alert alert-danger fade in"><h3>Questo listino è ancora incompleto.</h3>
    <p>Occorre compilarne tutti i campi per poterci aggiungere articoli.</p>
    <p>Una volta fatto, ricarica la pagina per accedere alle altre funzioni.</p></div>';
}else{
    $inco = '';
}

//NAVBAR
//$title='<i class="fa fa-cubes fa-2x pull-left"></i> '.$row["descrizione_listini"].'<br><small class="note"></small>';


?>

<?php echo $L->navbar_listino(); ?>
<?php echo $inco; ?>
<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        </article>
    </div>
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <?php echo $wg_articoli_oper->print_html(); ?>
        </article>
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <?php if(posso_gestire_listino($L->id_listini)){echo $wg_listino->print_html();} ?>
        </article>

    </div>

    <hr>

    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo $wg_articoli->print_html(); ?>
            <?php echo help_render_html('listino',$page_title); ?>
        </article>
    </div>
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


    pageSetUp();


    var dz;
    var myDropZone;
    var file_code;
    var ext;

    var pagefunction = function(){

        <?php if(!$incompleto){ ?>
        loadScript("js/plugin/jqgrid/jquery.jqGrid.min.js", run_jqgrid_function);
        <?php } ?>

        <?php if($proprietario=="true" AND !$incompleto){ ?>
        var  initDropzone = function (){
                //-----------------------------------------------DROPZONE DEMO
                try{Dropzone.autoDiscover = false;}catch(e){}


                 try{

                 console.log("initDropzone");
                 myDropZone = new Dropzone(document.body, { // Make the whole body a dropzone
                  maxFiles:1,
                  url: "upload.php", // Set the url
                  //thumbnailWidth: 80,
                  //thumbnailHeight: 80,
                  //parallelUploads: 20,
                  //previewTemplate: previewTemplate,
                  //autoQueue: false, // Make sure the files aren't queued until manually added
                  //previewsContainer: "#previews", // Define the container to display the previews
                  clickable: ".fileinput-button", // Define the element that should be used as click trigger to select files.
                  success: function(file,response){
                        console.log(file);
                        console.log(response);
                        var data = JSON.stringify(eval("(" + response + ")"));
                        var json = JSON.parse(data);
                        console.log(json.result);
                        $("#loadingprogress").width( 0 );
                        $("#loadingSpinner").addClass('hidden');
                        this.removeAllFiles();

                        if(json.result==="OK"){
                                   //ok(json.msg);
                                   $('#remoteModalConfirm').modal({ show: false});
                                   $('#remoteModalConfirm').modal('show');
                                   $('#remoteModalConfirmContent').html(json.msg);
                                   $('#remoteModalTitle').html(json.title);
                                   file_code = json.file;
                                   ext =json.ext;
                                   console.log("file" + file_code + "ext :" + ext);
                                   return true;
                               }else{
                                    ko(json.msg);
                                    return false;
                        }


                }
                 });
               myDropZone.on('sending', function(file, xhr, formData){
                    formData.append('id_listino', '<?php echo $id_listino?>');
                    formData.append('act', 'listino');
                    $("#loadingSpinner").removeClass('hidden');

                });
                myDropZone.on('uploadprogress', function(file, progress ){
                    console.log(progress );
                    $("#loadingprogress").width( progress + '%' );
                });
            }catch(err){
                console.log("dropZone already attached..." + err);
                location.reload();
            }
            //-----------------------------------------------DROPZONE DEMO
        }
        loadScript("js/plugin/dropzone/dropzone.min.js",initDropzone);
        <?php }?>


        function run_jqgrid_function() {
            var rowid;
            var lastSel;
            var is_editable = false;

            jQuery("#jqgrid").jqGrid({
               url:'ajax_rd4/listini/inc/articoli.php?id_listino=<?php echo $id_listino?>&s=en',
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
                            'T3'


                            ],
               colModel:[
                   {name:'codice',index:'codice', width:60,editable:is_editable, },
                   {name:'descrizione_articoli',index:'descrizione_articoli', width:150, align:"left",editable:is_editable},
                   {name:'prezzo',index:'prezzo', width:50,align:"right",editable:is_editable,search:false},

                   {name:'u_misura',index:'u_misura', width:20,align:"right",editable:is_editable,search:false},
                   {name:'misura',index:'misura', width:30,align:"left",editable:is_editable,search:false},

                   {name:'ingombro',index:'ingombro', width:50, editable:is_editable,search:false,edittype:'textarea'},

                   {name:'qta_scatola',index:'qta_scatola', width:25, align:"center",editable:is_editable,search:false},
                   {name:'qta_minima',index:'qta_minima', width:25, align:"center",editable:is_editable,search:false},

                   {name:'articoli_note',index:'articoli_note', width:75, sortable:false,editable:is_editable,search:true,edittype:'textarea'},

                   {name:'articoli_unico',index:'articoli_unico', width:25, align:"center",editable:is_editable,search:false},
                   {name:'articoli_opz_1',index:'articoli_opz_1', width:50,align:"left",editable:is_editable},
                   {name:'articoli_opz_2',index:'articoli_opz_2', width:50,align:"left",editable:is_editable},
                   {name:'articoli_opz_3',index:'articoli_opz_3', width:50,align:"left",editable:is_editable},


               ],

               multiselect: false,
               onSelectRow:function(id){
                    var lastsel= jQuery('#jqgrid').jqGrid('getGridParam','selrow');
                    console.log(lastsel);
                    //$("#jqgrid").addRowData(rowid,data, position, lastsel);
               },

               rowNum:20,
               rowList:[20,50,500,5000],
               pager: '#pjqgrid',

               //sortname: 'id_articolo',
            viewrecords: true,
            //sortorder: "desc",
            editurl: "ajax_rd4/listini/inc/articoli.php?id_listino=<?php echo $id_listino?>",

            caption:"",
            gridComplete : function(){
                console.log("Grid Complete!");
                //------------------------------XEDITABLE INIT

            }

        });


        /*
        jQuery("#jqgrid").jqGrid('navGrid','#pjqgrid',{
                edit:false,
                add:false,
                del:false,
                search:false
        });
        */
        jQuery("#jqgrid").jqGrid('filterToolbar',{});


        $(window).on('resize.jqGrid', function() {
            console.log ("resizing ");

                jQuery("#jqgrid").jqGrid('setGridWidth', $("#jqgcontainer").width());
                jQuery("#jqgrid").jqGrid('setGridHeight', $("#jqgcontainer").height() - ($("#gbox_jqgrid").height() - $('#gbox_jqgrid .ui-jqgrid-bdiv').height()));

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
       $(".editable_tipo").editable({
                                url: 'ajax_rd4/listini/_act.php',
                                source: [
                                  {value: 0, text: 'Normale'},
                                  {value: 1, text: 'Magazzino', disabled: true}
                               ],
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
       $(".editable_privato").editable({
                                url: 'ajax_rd4/listini/_act.php',
                                source: [
                                  {value: 0, text: 'Pubblico'},
                                  {value: 1, text: 'Privato'}
                               ],
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
      $(".editable_tipologia").editable({
                                url: 'ajax_rd4/listini/_act.php',
                                source: [
                                  {value: 1, text: 'Non definito'},
                                  {value: 5, text: 'Alimentari (Generi vari)'},
                                  {value: 2, text: 'Pasta, Riso, Farine'},
                                  {value: 3, text: 'Frutta e verdura'},
                                  {value: 4, text: 'Carne e Pesce'},
                                  {value: 12, text: 'Vino o Birra'},
                                  {value: 7, text: 'Miele e dolciumi'},
                                  {value: 17, text: 'Formaggi e latticini'},
                                  {value: 11, text: 'Abbigliamento'},
                                  {value: 8, text: 'Intimo'},
                                  {value: 9, text: 'Calzature'},
                                  {value: 10, text: 'Accessori'},
                                  {value: 13, text: 'Cosmesi e Igiene Personale'},
                                  {value: 6, text: 'Libri - Riviste'},
                                  {value: 14, text: 'Elettronica (Accessori)'},
                                  {value: 15, text: 'Elettrodomestici'},
                                  {value: 16, text: 'Informatica'}

                               ],
                                ajaxOptions: { dataType: 'json' },
                                success: function(response, newValue) {
                                    console.log(response);
                                    if(response.result == 'KO'){
                                        return response.msg;
                                    }else{
                                        ok(response.msg);
                                        $('#img_listino').attr("src", "img_rd4/t_"+newValue+"_240.png");
                                        console.log(newValue);
                                    }
                                }
                            });
    $("#export_listino_CSV").click(function(e) {
        open("GET","http://retegas.altervista.org/gas4/ajax_rd4/listini/_act.php",{"act":"exp_csv","id":<?php echo $id_listino?>},"_BLANK");
    });
    $("#export_listino_XLS").click(function(e) {
         open("GET","http://retegas.altervista.org/gas4/ajax_rd4/listini/_act.php",{"act":"exp_xls","t":"XLS","id":<?php echo $id_listino?>},"_BLANK");
    });
    $("#export_listino_XLSX").click(function(e) {
        open("GET","http://retegas.altervista.org/gas4/ajax_rd4/listini/_act.php",{"act":"exp_xls","t":"XLSX","id":<?php echo $id_listino?>},"_BLANK");
    });
    $("#export_listino_HTM").click(function(e) {
        open("POST","http://retegas.altervista.org/gas4/ajax_rd4/listini/_act.php",{"act":"exp_htm","id":<?php echo $id_listino?>},"_BLANK");
    });
    $("#gestisci_articoli").click(function(e) {
        //open("POST","http://retegas.altervista.org/gas4/ajax_rd4/listini/_act.php",{"act":"exp_htm","id":<?php echo $id_listino?>},"_BLANK");
        location.replace('http://retegas.altervista.org/gas4/#ajax_rd4/listini/edit.php?id=<?php echo $id_listino?>');
    });
    $("#clona_listino").click(function(e) {
    $.SmartMessageBox({
                title : "Cloni questo listino ?",
                content : "Diventerai il proprietario della copia clonata e potrai modificarla a tuo piacimento.<br>Una volta clonato, verrai rediretto nella scheda del tuo nuovo listino.",
                buttons : "[Esci][Clona]",
                input : "text",
                placeholder : "<?php echo $descrizione_listini ?> (clone)",
                inputValue: 'Listino <?php echo $descrizione_listini ?> (clone)',
            }, function(ButtonPress, Value) {

                if(ButtonPress=="Clona"){
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/listini/_act.php",
                          dataType: 'json',
                          data: {act: "clona_listino", value : Value, id : <?php echo $id_listino?>},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                ok(data.msg);
                                console.log(data.id);
                                setInterval(function(){
                                        location.replace("http://retegas.altervista.org/gas4/#ajax_rd4/listini/listino.php?id="+data.id);
                                        clearInterval();
                                }, 3000);

                            }else{
                                ko(data.msg)
                            ;}

                        });
                }
            });
    });
    $("#del_articoli").click(function(e) {
    $.SmartMessageBox({
                title : "Elimini tutti gli articoli di questo listino?",
                content : "Con questa operazione eliminerai tutti gli articoli di questo listino.<br>Se ci sono ordini chiusi con questo listino gli utenti conserveranno comunque tutti i loro dati.<br>Prima di eliminarli puoi farne una copia usando la funzione <b>esporta</b>",
                buttons : "[Esci][Elimina]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="Elimina"){
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/listini/_act.php",
                          dataType: 'json',
                          data: {act: "del_articoli", id : <?php echo $id_listino?>},
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
            });
    });
    $("#del_listino").click(function(e) {
    $.SmartMessageBox({
                title : "Elimini questo listino?",
                content : "Con questa operazione eliminerai il listino.",
                buttons : "[Esci][Elimina]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="Elimina"){
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/listini/_act.php",
                          dataType: 'json',
                          data: {act: "del_listino", id : <?php echo $id_listino?>},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                ok(data.msg);
                                location.replace("http://retegas.altervista.org/gas4/#ajax_rd4/listini/miei.php");
                            }else{
                                ko(data.msg)
                            ;}

                        });
                }
            });
    });

    $('body').on('hidden.bs.modal', '.modal', function () {
          $(this).removeData('bs.modal');
          //dz.destroy();
          //dz=null;
    });

    $('body').on('shown.bs.modal','.modal', function(e) {
            console.log("Modal opened");
            dz=null;
            //initDropzone();
    });
    $('body').on('click','#go_controlla', function(e) {
            console.log("go_controlla");
            $('#remoteModalConfirm').modal('hide');
            try{myDropZone.destroy();}catch(e){console.log("Destroy failed")}
    });
    $('body').on('click','#go_esci', function(e) {
            console.log("go_esci");
            $('#remoteModalConfirm').modal('hide');
            //try{myDropZone.destroy();}catch(e){console.log("Destroy failed")}
    });
    $('body').on('click','#go_upload', function(e) {
            console.log("do_upload");
            console.log("file: " + file_code + " ext: " + ext);
            $.ajax({
                          type: "GET",
                          url: "ajax_rd4/listini/upload_act.php?id=<?php echo $id_listino?>&f="+file_code+"&e="+ext+"&act=check&do=ins",
                          dataType: 'json',
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                ok(data.msg);
                            }else{
                                ko(data.msg);
                         ;}
                        $('#remoteModalConfirm').modal('hide');
                        $('#jqgrid').trigger( 'reloadGrid' );
                        });
    });

    $('.attiva_extra').click(function(){
        $this = $(this);
        id_user = $this.data('id_user');
        //ok("Lis " + id_listino + " User " + id_user);
        $.ajax({
                          type: "POST",
                          url: "ajax_rd4/listini/_act.php",
                          dataType: 'json',
                          data: {act: "attiva_aiuta_a_gestire", id_user:id_user, id_listini:<?php echo $L->id_listini;?>},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    ok(data.msg);
                            }else{
                                    ko(data.msg)
                            ;}

                        });



    });

    $('.elimina_extra').click(function(){
        $this = $(this);
        id_user = $this.data('id_user');
        $.ajax({
                          type: "POST",
                          url: "ajax_rd4/listini/_act.php",
                          dataType: 'json',
                          data: {act: "elimina_aiuta_a_gestire", id_user:id_user, id_listini:<?php echo $L->id_listini;?>},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    ok(data.msg);
                            }else{
                                    ko(data.msg)
                            ;}

                        });
    });

    $('#aiuta_a_gestire').click(function(){
    $.SmartMessageBox({
                title : "Vuoi aiutare a gestire questo listino ?",
                content : "Scrivi un breve messaggio che verrà inviato all\'autore di questo listino, e se lo vorrà ti inserirà tra i suoi gestori.",
                buttons : "[Esci][Invia]",
                input : "text",
                placeholder : "msg",
                inputValue: '',
            }, function(ButtonPress, Value) {

                if(ButtonPress=="Invia"){
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/listini/_act.php",
                          dataType: 'json',
                          data: {act: "aiuta_a_gestire", value : Value, id:<?php echo $L->id_listini;?>},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    ok(data.msg);
                                    //location.replace("http://retegas.altervista.org/gas4/?#ajax_rd4/listini/listino.php?id="+data.id);
                            }else{
                                ko(data.msg)
                            ;}

                        });
                }
            });

            e.preventDefault();
        })

    } // end pagefunction



        loadScript("js/plugin/jqgrid/grid.locale-en.min.js",
            loadScript("js/plugin/x-editable/x-editable.min.js", pagefunction));





    </script>
