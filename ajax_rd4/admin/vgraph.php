<?php
require_once("inc/init.php");
if(!(_USER_PERMISSIONS & perm::puo_gestire_retegas)){die("KO");}




$id_des=_USER_ID_DES;

//CREO LISTA GAS
$sql="SELECT id_ditta, SUM(`prz_dett` * `qta_arr`) as totale FROM `retegas_dettaglio_ordini` Where id_ditta>0 GROUP BY id_ditta ;";
$stmt = $db->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll();
foreach($rows as $row){
    //$js_ditte .= "graph.addNode('gas_".$row["id_gas"]."', {url : '".src_gas($row["id_gas"])."'});";
    $js_ditte .= '{"name":"ditta_'.$row["id_ditta"].'","totale":'.CAST_TO_INT($row["totale"]/100).'},';
}
$js_ditte = rtrim($js_ditte,",");

?>
<style type='text/css'>
            #graph1 {
                width: 100%;
                height: 600px;
            }
            #graph1 > svg {
                width: 100%;
                height: 100%;
            }
 </style>
<div class="inbox-nav-bar no-content-padding">
    <h1 class="page-title txt-color-blueDark"><i class="fa fa-fw fa-unlock"></i> vGraph &nbsp;</h1>
</div>
<div id="graph1"></div>


<script type="text/javascript">

    pageSetUp();

    var pagefunction = function() {


        var d3Sample = function(){
                    var data = {"nodes":[<?php echo $js_ditte;?>],"links":[]};
                    var g = Viva.Graph.graph();
                    g.Name = "Sample graph from d3 library";
                    g.addNode("0000", "0000");
                    for (var i = 0; i < data.nodes.length; ++i){
                        g.addNode(i, data.nodes[i]);
                        g.addLink(i, "0000");
                    }
                    for (i = 0; i < data.links.length; ++i){
                        var link = data.links[i];
                        g.addLink(link.source, link.target, link.value);
                    }
                    return g;
                };
                 var colors = [
                        "#1f77b4", "#aec7e8",
                        "#ff7f0e", "#ffbb78",
                        "#2ca02c", "#98df8a",
                        "#d62728", "#ff9896",
                        "#9467bd", "#c5b0d5",
                        "#8c564b", "#c49c94",
                        "#e377c2", "#f7b6d2",
                        "#7f7f7f", "#c7c7c7",
                        "#bcbd22", "#dbdb8d",
                        "#17becf", "#9edae5"
                        ];
                 var example = function() {
                    var graph = d3Sample();
                    var layout = Viva.Graph.Layout.forceDirected(graph, {
                        springLength : 1000,
                        springCoeff : 0.0001,
                        dragCoeff : 0,
                        gravity : -0.001
                    });
                    var svgGraphics = Viva.Graph.View.svgGraphics();
                    svgGraphics.node(function(node){
                        var totale = node.data.totale;
                        var circle = Viva.Graph.svg('circle')
                            .attr('r', totale)
                            .attr('stroke', '#fff')
                            .attr('stroke-width', '15px')
                            .attr("fill", colors[2]);
                        circle.append('title').text(node.data.name);

                        $(circle).click(function() {
                            console.log("Click" + node.id);
                        });

                        return circle;

                    }).placeNode(function(nodeUI, pos){
                        nodeUI.attr( "cx", pos.x).attr("cy", pos.y);
                    });
                    svgGraphics.link(function(link){
                        return Viva.Graph.svg('line')
                                .attr('stroke', '#999')
                                .attr('stroke-width', Math.sqrt(link.data));
                    });
                    var renderer = Viva.Graph.View.renderer(graph, {
                        container : document.getElementById('graph1'),
                        layout : layout,
                        graphics : svgGraphics,
                        prerender: 20,
                        renderLinks : false
                    });
                    renderer.run(500);
                }();


    };



    // end pagefunction


    // Load morris dependencies and run pagefunction

    loadScript("js_rd4/plugin/vivagraph/vivagraph.js", pagefunction);

</script>