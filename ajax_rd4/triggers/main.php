<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.ordine.php");

$ui = new SmartUI;
$page_title = "Triggers";
$page_id="triggers";


$id_ordine = CAST_TO_INT($_GET["id_ordine"],0);
if($id_ordine>0){
    $param_ordine='?id_ordine='.$id_ordine;
    $O = new ordine($id_ordine);
    $descrizione_ordini= $O->descrizione_ordini;
}else{
    $param_ordine='';
}


$id_owner=_USER_ID;

$sql = "SELECT * from retegas_triggers WHERE id_owner=:id_owner;";
$stmt = $db->prepare($sql);
$stmt->bindParam(':id_owner', $id_owner, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);    
foreach ($rows as $row){
    
    if(conv_datetime_from_db($row["scattato_il"])=="// 00:00"){
        $scattato="non ancora";
        $scattato_class = "text-success";
    }else{
        $scattato=conv_datetime_from_db($row["scattato_il"]);
        $scattato_class = "text-danger";
    }
    
    $oggetto="";
    
    if($row["tipo"]==1){
        $oggetto="Ord. #".$row["id_ordine"];
        $valore=$row["valore"];
    }
    if($row["tipo"]==2){
        $oggetto="Data/ora";
        $valore= conv_datetime_from_db($row["quando"]);
    }
        
    $t .='<tr>';    
        //$t.='<td>'.$row["id_trigger"].'</td>';
        $t.='<td>'.$row["tipo"].'.'.$row["sottotipo"].'</td>';
        $t.='<td>'.$oggetto.'</td>';
        $t.='<td>'.$valore.'</td>';
        $t.='<td>'.user_fullname($row["id_utente"]).'</td>';
        $t.='<td>'.substr(strip_tags($row["testo_azione"]),0,20).'...</td>';
        $t.='<td><span class="'.$scattato_class.'">'.$scattato.'</span></td>';
        $t.='<td><i class="fa fa-times text-danger delete_trigger" data-id="'.$row["id_trigger"].'" style="cursor:pointer"></i></td>';
   $t.='</tr>';     
}

if(_USER_PERMISSIONS & perm::puo_vedere_tutti_ordini){
    //TRIGGER_1_1
    $trigger_1_1='<a class="" href="#ajax_rd4/triggers/trigger_1_1.php'.$param_ordine.'">
                    <strong>1.1:</strong> Manda un messaggio ad un utente specifico quando un dato ordine supera un certo quantitativo di scatole piene.
                    </a><hr>';
    //TRIGGER_1_2
    $trigger_1_2='<a class="" href="#ajax_rd4/triggers/trigger_1_2.php'.$param_ordine.'">
            <strong>1.2:</strong> Manda un messaggio ad un utente specifico quando un ordine supera un certo numero di articoli totali 
            </a><hr>';
    
    //TRIGGER_1_3
    $trigger_1_3='<a class="" href="#ajax_rd4/triggers/trigger_1_3.php'.$param_ordine.'">
            <strong>1.3:</strong> Manda un messaggio ad un utente specifico quando il valore totale di un ordine supera una certa soglia 
            </a><hr>';
    //TRIGGER_1_4
    $trigger_1_4='<a class="" href="#ajax_rd4/triggers/trigger_1_4.php'.$param_ordine.'">
            <strong>1.4:</strong> Manda un messaggio ad un utente specifico quando i partecipanti ad un dato ordine superano un certo numero 
            </a><hr>';
    
    
}
//TRIGGER_1_4
    $trigger_2_1='<a class="" href="#ajax_rd4/triggers/trigger_2_1.php'.$param_ordine.'">
            <strong>2.1:</strong> Manda un messaggio ad un utente specifico ad una certa data. 
            </a><hr>';

?>


<h1>TRIGGERS <small>Leggere l'help in fondo alla pagina.</small></h1>
<div class="row margin-top-10">
    <div class="col-md-4">
        <div class="well well-lg" style="max-height:500px; overflow-y:auto;">
            <h1>Ordini</h1>
            <?php
                if($id_ordine>0){
            ?>
                <p class="note">Questi trigger riguardano l'ordine specifico #<?php echo $id_ordine." ".$descrizione_ordini; ?>.</p>

            <?php
                }else{    
            ?>
                <p class="note">Questi trigger riguardano gli ordini.</p>

            <?php
                }
            ?>
            <?php echo  $trigger_1_1.
                        $trigger_1_2.
                        $trigger_1_3.
                        $trigger_1_4;
            ?>            
        </div>
        

                  
    </div>
    <div class="col-md-4">
        <div class="well well-lg" style="max-height:500px; overflow-y:auto;">
            <h1>Tempo</h1>
            <?php echo  $trigger_2_1.
                        $trigger_2_2.
                        $trigger_2_3.
                        $trigger_2_4;
            ?>
        </div>    
                
    </div>
    <div class="col-md-4">
        <div class="well well-lg" style="max-height:500px; overflow-y:auto;">

        </div>    
    </div>
</div>
<div class="table-responsive" style="overflow-x:auto">
                                <table id="tabella_trigger">
                                    <thead>
                                        <tr>
                                            <th>tipo</th>
                                            <th>oggetto</th>
                                            <th>soglia</th>
                                            <th>target</th>
                                            <th>messaggio</th>
                                            <th>scattato il</th>
                                            <th data-filter="false"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php echo $t; ?>
                                    
                                    </tbody>
                                
                                </table>
                            </div>

<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
        </article>
    </div>
</section>

<script type="text/javascript">
    /* DO NOT REMOVE : GLOBAL FUNCTIONS!
     *
     * pageSetUp(); WILL CALL THE FOLLOWING FUNCTIONS
     *
     * // activate tooltips
     * $("[rel=tooltip]").tooltip();
     *
     * // activate popovers
     * $("[rel=popover]").popover();
     *
     * // activate popovers with hover states
     * $("[rel=popover-hover]").popover({ trigger: "hover" });
     *
     * // activate inline charts
     * runAllCharts();
     *
     * // setup widgets
     * setup_widgets_desktop();
     *
     * // run form elements
     * runAllForms();
     *
     ********************************
     *
     * pageSetUp() is needed whenever you load a page.
     * It initializes and checks for all basic elements of the page
     * and makes rendering easier.
     *
     */

    pageSetUp();


    var pagefunction = function() {

    //-------------------------HELP
    <?php echo help_render_js($page_id); ?>
    //-------------------------HELP

    loadScript("js_rd4/plugin/tablesorter/js/jquery.tablesorter.widgets.js",startTable);
    
    function startTable(){    
                $.extend($.tablesorter.themes.bootstrap, {
                    table      : 'table table-bordered',
                    caption    : 'caption',
                    sortNone   : 'bootstrap-icon-unsorted',
                    sortAsc    : 'fa fa-arrow-up',
                    sortDesc   : 'fa fa-arrow-down'

                  });

                var $table = $('#tabella_trigger').tablesorter({
                    theme: 'bootstrap',
                        //debug:true,
                        widgets: ["uitheme","filter","zebra"],
                        widgetOptions : {
                            zebra : ["even", "odd"],
                            filter_reset : ".reset",
                            filter_columnFilters: true
                        }
                });
        }
    
    
    //DELETE TRIGGER ---------------------------------------------------------------------------
    $(document).off('click','.delete_trigger');
        $(document).on('click','.delete_trigger',function(e){
            var $t = $(this);
            var id_trigger = $(this).data("id");
            $.ajax({
                          type: "POST",
                          url: "ajax_rd4/triggers/_act.php",
                          dataType: 'json',
                          data: {act: "delete_trigger",id_trigger:id_trigger},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                ok(data.msg);
                                $t.closest('tr').fadeOut();
                            }else{
                                ko(data.msg);
                                //$t.closest('.list-group-item').fadeOut();
                            }

                        });


        });
    //DELETE TRIGGER------------------------------------------------------------
    
    
    
    
    }
    // end pagefunction
    loadScript("js_rd4/plugin/tablesorter/js/jquery.tablesorter.min.js",pagefunction);



</script>
