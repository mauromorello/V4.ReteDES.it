<?php
require_once("inc/init.php");

$ui = new SmartUI;
$page_title = "Tutti gli help";
$page_id = "tutti_help";

    $stmt = $db->prepare("SELECT valore_text, max(valore_int) as valore_int from retegas_options  WHERE chiave='_HELP_V4' group by valore_text order by max(valore_int) desc;");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $row){

    //$h.= '<tr data-id_option="'.$row["id_option"].'" class="row_selector">';
    //    $h.= '<td>'.$row["valore_int"].'</td>';
    //    $h.= '<td>'.$row["valore_text"].'</td>';
    //$h.= '</tr>';

    $h .='<section id="widget-grid" class="margin-top-10">
                <div class="row">
                    <!-- PRIMA COLONNA-->
                    <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        '. help_render_html($row["valore_text"],$row["valore_text"],true) .'
                    </article>
                </div>
            </section>';
    $j .= help_render_js($row["valore_text"]);

    }



?>
<?php echo $h;?>

<script type="text/javascript">
    pageSetUp();

    var pagefunction = function(){

        //------------HELP WIDGET
        <?php echo $j;?>
        //------------END HELP WIDGET
        loadScript("js_rd4/plugin/tablesorter/js/jquery.tablesorter.widgets.js",startTable);
        function startTable(){

                $.extend($.tablesorter.themes.bootstrap, {
                    table      : 'table table-bordered',
                    caption    : 'caption',
                    sortNone   : 'bootstrap-icon-unsorted',
                    sortAsc    : 'fa fa-arrow-up',
                    sortDesc   : 'fa fa-arrow-down'

                  });

                t = $('#cronologia_help').tablesorter({
                    theme: 'bootstrap',
                        //debug:true,
                        widgets: ["uitheme","filter","zebra"],
                     widgetOptions : {
                         zebra : ["even", "odd"],
                         filter_reset : ".reset"
                        }
                });



        }//END STARTTABLE

        $('.row_selector').click(function(){
            $this = $(this);
            console.log("id: "+ $this.data("id_option"));


        });

    }
    // end pagefunction

    loadScript("js_rd4/plugin/tablesorter/js/jquery.tablesorter.min.js", pagefunction);

</script>
