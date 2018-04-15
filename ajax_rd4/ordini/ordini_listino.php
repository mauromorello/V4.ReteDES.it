<?php
require_once("inc/init.php");
$ui = new SmartUI;
$converter = new Encryption;

$page_title = "Listino di questo ordine";
$page_id = "listino_ordine";

//CONTROLLI
$id_ordine = (int)$_GET["id_ordine"];
$O = new ordine($id_ordine);

if (!posso_gestire_ordine($id_ordine)){
    echo rd4_go_back("Non ho i permessi necessari");die;
}

$rows = $O->get_articoli_temp();
foreach($rows as $row){
    $j .="{
            CODICE: '".$row["codice"]."',
            DESCRIZIONE: '".$row["descrizione_articoli"]."',
            PREZZO: '".$row["prezzo"]."',
            UDM: '".$row["u_misura"]."',
            MISURA: '".$row["misura"]."',
            INGOMBRO: '".$row["ingombro"]."',
            Q_S:'".$row["qta_scatola"]."',
            Q_M:'".$row["qta_minima"]."',
            NOTE:'".$row["articoli_note"]."',
            U:'".$row["articoli_unico"]."',
            TAG_1:'".$row["articoli_opz_1"]."',
            TAG_2:'".$row["articoli_opz_2"]."',
            TAG_3:'".$row["articoli_opz_3"]."',
            DIS:'".$row["is_disabled"]."'
            },";
    
    
}
$j = rtrim($j,',');

?>
<link href="<?php echo APP_URL.'/css/hst2.css';?>" rel="stylesheet">
<?php echo $O->navbar_ordine();?>
<h1>PAGINA DI TEST - NON ATTIVA</h1>
<div  style="overflow:hidden; height: 320px"><div id="example"></div></div>

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
        //-------------------------HELP
        <?php echo help_render_js($page_id); ?>
        //-------------------------HELP
        

        var dataObject = [<?php echo $j;?>];     
         
        var container = document.getElementById('example');
        var autosaveNotification;
        var hot = new Handsontable(container, {
            colHeaders: ['CODICE', 'DESCRIZIONE', 'PREZZO','UDM','MISURA','INGOMBRO','Q_S','Q_M','NOTE','U','TAG_1','TAG_2','TAG_3','DIS'],
            data: dataObject,
            minSpareCols: 0,
            minSpareRows: 0,
            rowHeaders: false,
            stretchH: "all",
            contextMenu: false,
            filters: true,
            dropdownMenu: ['filter_by_value','filter_action_bar'],
            //dropdownMenu: true,
            afterChange: function (change, source) {
              clearTimeout(autosaveNotification);
              $.ajax({
                      type: "POST",
                      url: "ajax_rd4/gas/_act.php",
                      dataType: 'json',
                      data: {change : change},
                      context: document.body
                    }).done(function(data) {
                        autosaveNotification = setTimeout(function() {
                        }, 1000);
                        if(data.result=="OK"){
                            ok(data.msg);
                        }else{
                            ko(data.msg);
                        }
                    });
              
             
            }
        });
        
        
    }
    
    loadScript('js_rd4/plugin/handsontable/hst2.js',pagefunction);
    
    
</script>
