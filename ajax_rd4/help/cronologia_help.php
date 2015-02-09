<?php
require_once("inc/init.php");

$ui = new SmartUI;
$page_title = "Cronologia Help";
$page_id = "cronologia_help";

if(!_USER_PUO_MODIFICARE_HELP){echo rd4_back("Ops.. !");}

$stmt = $db->prepare("SELECT O.*, U.fullname from retegas_options O inner join maaking_users U on U.userid = O.id_user WHERE chiave='_HELP_V4' order by valore_int desc;");
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $h = '<table id="cronologia_help" class="table table-condensed">';
    $h.= '<thead>';
    $h.= '<tr>';
    $h.= '<th>#</th>';
    $h.= '<th>Scheda</th>';
    $h.= '<th>Data</th>';
    $h.= '</tr>';
    $h.= '</thead>';
    $h.= '<tbody>';


    foreach($rows as $row){



            $h.= '<tr data-id_option="'.$row["id_option"].'" class="row_selector">';
                $h.= '<td>'.$row["id_option"].'</td>';
                $h.= '<td>'.$row["fullname"].'</td>';
                $h.= '<td><span class="note">'.$row["valore_int"].'</span> '.$row["valore_text"].'</span></td>';
            $h.= '</tr>';


        unset($O);
        unset($U);
    }

    $h.= '</tbody>';
    $h.= '<tfoot>';
    $h.= '<tr>';
    $h.= '<th>1</th>';
    $h.= '<th>2</th>';
    $h.= '<th>3</th>';

    $h.= '</tr>';
    $h.= '</tfoot>';
    $h.= '</table>';


?>

<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
        </article>
    </div>
</section>

<div class="row">
    <div class="col col-md-6"><?php echo $h;?></div>
    <div class="col col-md-6"><div class="well well-sm">Test</div></div>
</div>

<script type="text/javascript">
    pageSetUp();

    var pagefunction = function(){

        //------------HELP WIDGET
        <?php echo help_render_js($page_id);?>
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
