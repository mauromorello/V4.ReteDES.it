<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.gas.php");


$ui = new SmartUI;
$converter = new Encryption;

$page_title = "Relazioni tra DES, GAS e utenti";
$page_id ="GR_des_gas_users";




if(_USER_PERMISSIONS&perm::puo_gestire_retegas){
    if(CAST_TO_INT($_GET["id_des"],0)>0){
        $id_des =  CAST_TO_INT($_GET["id_des"]);
    }else{
        $id_des =  _USER_ID_DES;
    }
}else{
    $id_des =  _USER_ID_DES;
}

if(($id_des==0 | $id_des==1) AND (!(_USER_PERMISSIONS & perm::puo_vedere_retegas))){
    ?><h3 class="text-center">Il tuo gas non appartiene ad alcun DES.</h3>
      <p class="text-center">Se sei interessato alla costituzione di un DES o all'inserimento del tuo GAS in un DES esistente<br> contatta <b>info@retedes.it</b> per avere tutte le informazioni in merito.</p>   <?php
    die();
}

if(!(_USER_PERMISSIONS & perm::puo_vedere_retegas)){
   ?><h3 class="text-center">Non hai i permessi per gestire il tuo DES.</h3><?php
   die();
}

$title_navbar="Des #".$id_des;


$js_des= "graph.addNode('des_".$id_des."', {url : '".src_des($id_des)."'});";

//CREO LISTA GAS
$sql="SELECT * FROM retegas_gas G WHERE G.id_des='$id_des' AND (SELECT COUNT(*) from maaking_users U WHERE U.id_gas=G.id_gas AND U.isactive=1) > 10;";
$stmt = $db->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll();

foreach($rows as $row){
    $js_gas .= "graph.addNode('gas_".$row["id_gas"]."', {url : '".src_gas($row["id_gas"])."'});";
    $js_des_gas .="graph.addLink('des_".$id_des."', 'gas_".$row["id_gas"]."');";

    $id_gas=$row["id_gas"];
    //CREO LISTA UTENTI
    $sql="SELECT U.*  FROM maaking_users U where U.id_gas='$id_gas' and U.isactive=1;";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $rowsU = $stmt->fetchAll();
    foreach($rowsU as $rowU){
        $js_users .= "graph.addNode('user_".$rowU["userid"]."', {url : '".src_user($rowU["userid"])."'});";
        $js_gas_users .="graph.addLink('user_".$rowU["userid"]."', 'gas_".$id_gas."');";
    }
}



?>
 <style type='text/css'>
            #graphContainer {
                width: 100%;
                height: 600px;
            }
            #graphContainer > svg {
                width: 100%;
                height: 100%;
            }
 </style>
<?php echo navbar2($title_navbar,$buttons); ?>

<!-- MAP -->

<?php if(_USER_PERMISSIONS & perm::puo_gestire_retegas){?><div class="alert alert-info">Sei qua come ADMIN: usa ?id_des=x</div><?php } ?>

<div class="well well-sm">
    <div id="graphContainer"></div>
</div>

<section id="widget-grid" class="margin-top-10">

    <div class="row">
        <!-- PRIMA COLONNA
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <div class="well well-sm"><?php if(_USER_PERMISSIONS & perm::puo_gestire_utenti){echo $nu;} ?></div>

        </article>-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
        </article>
    </div>

</section>

<script type="text/javascript">

    pageSetUp();



    var pagefunction = function(){

        //------------HELP WIDGET
        document.title = '<?php echo "ReteDES.it :: $page_title";?>';
        <?php echo help_render_js($page_id);?>
        //------------END HELP WIDGET


        var graph = Viva.Graph.graph();

        <?php echo $js_des; ?>
        <?php echo $js_gas; ?>
        <?php echo $js_users; ?>
        <?php echo $js_gas_users; ?>
        <?php echo $js_des_gas; ?>


        // Set custom nodes appearance
        var graphics = Viva.Graph.View.svgGraphics();
        graphics.node(function(node) {
               // The function is called every time renderer needs a ui to display node
               return Viva.Graph.svg('image')
                     .attr('width', 24)
                     .attr('height', 24)
                     .link(node.data.url); // node.data holds custom object passed to graph.addNode();
            })
            .placeNode(function(nodeUI, pos){
                // Shift image to let links go to the center:
                nodeUI.attr('x', pos.x - 12).attr('y', pos.y - 12);
            });

        var layout = Viva.Graph.Layout.forceDirected(graph, {
            springLength : 10,
            springCoeff : 0.1,
            dragCoeff : 0.02,
            gravity : 1
        });

        var renderer = Viva.Graph.View.renderer(graph,
            {
                container: document.getElementById('graphContainer'),
                graphics : graphics,
                prerender: 20,
                renderLinks : false
            });

        renderer.run();

    } // end pagefunction





    loadScript("js_rd4/plugin/vivagraph/vivagraph.js", pagefunction);


</script>