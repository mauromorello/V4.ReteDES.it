<?php
    require_once("inc/init.php");
    require_once("../../lib_rd4/class.rd4.user.php");
    require_once("../../lib_rd4/class.rd4.ordine.php");

    $ui = new SmartUI;
    $page_title = "Movimenti da registrare";
    $page_id = "da_registrare";

    if(!(_USER_PERMISSIONS & perm::puo_gestire_la_cassa)){
        echo "Non hai i permessi per gestire la cassa.";
    }

    $h = '<table id="table_da_registrare" class="table table-condensed smart-form has-tickbox">';
    $h.= '<thead>';
    $h.= '<tr>';
    $h.= '<th></th>';
    $h.= '<th>#ID</th>';
    $h.= '<th>Utente</th>';
    $h.= '<th>Ordine</th>';
    $h.= '<th>Importo</th>';
    $h.= '</tr>';
    $h.= '</thead>';
    $h.= '<tbody>';

    $sql = "SELECT * from retegas_cassa_utenti where registrato='no' and id_gas='"._USER_ID_GAS."' ORDER BY id_cassa_utenti DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    foreach($rows as $row){

        $U = new user($row["id_utente"]);
        $O = new ordine($row["id_ordine"]);

        $data_movimento = conv_datetime_from_db($row["data_movimento"]);

        $importo=$row["importo"];
        if($row["segno"]=="+"){
            $class=' class="text-success font-lg" ';

        }else{
            $class=' class="text-danger font-lg" ';
            $importo = -$importo;
        }

        if($O->codice_stato=="CO"){
            $h.= '<tr>';
                $h.= '<td><input type="checkbox" name="id_operazione"></td>';
                $h.= '<td>'.$row["id_cassa_utenti"].'<br><span class="note">'.$data_movimento.'</span></td>';
                $h.= '<td>'.$U->fullname.'</td>';
                $h.= '<td>#<b>'.$O->id_ordini.'</b> '.$O->descrizione_ordini.'</td>';
                $h.= '<td>'.$row["descrizione_movimento"].'</td>';
                $h.= '<td class="text-right"><span '.$class.'>'.$importo.'</span></td>';
            $h.= '</tr>';
        }

        unset($O);
        unset($U);
    }

    $h.= '</tbody>';
    $h.= '<tfoot>';
    $h.= '<tr>';
    $h.= '<th>1</th>';
    $h.= '<th>2</th>';
    $h.= '<th>3</th>';
    $h.= '<th>4</th>';
    $h.= '<th>5</th>';
    $h.= '<th>6</th>';
    $h.= '</tr>';
    $h.= '</tfoot>';
    $h.= '</table>';

  //-------------------------ORDINO

?>

<h1>Movimenti da registrare :</h1>
<hr>
<?php echo $h; ?>


<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
        </article>
    </div>
</section>
<script type="text/javascript">

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

                t = $('#table_da_registrare').tablesorter({
                    theme: 'bootstrap',
                        //debug:true,
                        widgets: ["uitheme","filter","zebra"],
                     widgetOptions : {
                         zebra : ["even", "odd"],
                         filter_reset : ".reset"
                        }
                });



        }//END STARTTABLE

    }
    // end pagefunction

    loadScript("js_rd4/plugin/tablesorter/js/jquery.tablesorter.min.js", pagefunction);



</script>