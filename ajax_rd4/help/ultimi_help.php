<?php
require_once("inc/init.php");

$ui = new SmartUI;
$page_title = "Ultimi aggiornamenti";
$page_id = "ultimi_help";


//CANCELLO LA DATA DI ULTIMA VISITA
$sql="DELETE FROM retegas_options WHERE id_user=:id AND chiave='_LAST_HELP_VIEWED' LIMIT 3;";
$stmt = $db->prepare($sql);
$stmt->bindValue(':id', _USER_ID, PDO::PARAM_INT);
$stmt->execute();

$sql="INSERT INTO retegas_options (chiave, timbro, id_user) VALUES ('_LAST_HELP_VIEWED',NOW(),:id);";
$stmt = $db->prepare($sql);
$stmt->bindValue(':id', _USER_ID, PDO::PARAM_INT);
$stmt->execute();

$h ='

            <section id="widget-grid" class="margin-top-10">
            <div class="row">
                <!-- PRIMA COLONNA-->
                <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    '. help_render_html($page_id,$page_title,true) .'
                </article>
            </div>
        </section>
        <hr>';
$j = help_render_js($page_id);

$stmt = $db->prepare("SELECT valore_text from retegas_options  WHERE chiave='_HELP_V4' AND valore_real=1 order by timbro desc LIMIT 10;");
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($rows as $row){
    $i++;
    $h .='<section id="" class="margin-top-10">
                <div class="row">
                    <!-- PRIMA COLONNA-->
                    <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        '. help_render_html($row["valore_text"],$row["valore_text"],true,true,false) .'
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
