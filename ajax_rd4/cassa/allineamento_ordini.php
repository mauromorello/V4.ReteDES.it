<?php
    require_once("inc/init.php");
    require_once("../../lib_rd4/class.rd4.user.php");
    require_once("../../lib_rd4/class.rd4.ordine.php");

    $ui = new SmartUI;
    $page_title = "Allineamento ordini";
    $page_id = "allineamento_ordini";

    if(!(_USER_PERMISSIONS & perm::puo_gestire_la_cassa)){
        echo "Non hai i permessi per gestire la cassa.";
    }

    $h = '<table id="table_da_registrare" class="table table-condensed smart-form has-tickbox">';
    $h.= '<thead>';
    $h.= '<tr>';
    //$h.= '<th class="filter-false"></th>';
    $h.= '<th>Ordine</th>';
    $h.= '<th></th>';
    $h.= '<th></th>';
    $h.= '<th class="filter-false">NETTO</th>';
    $h.= '<th class="filter-false">SPESA</th>';
    $h.= '<th class="filter-false">LORDO</th>';
    $h.= '<th class="filter-false">CASSA</th>';
    $h.= '<th class="filter-false">Delta</th>';
    $h.= '<th class="filter-select"></th>';
    $h.= '</tr>';
    $h.= '</thead>';
    $h.= '<tbody>';

    $sql = "SELECT R.*,O.*,U.* from retegas_referenze R
            INNER JOIN retegas_ordini O on O.id_ordini=R.id_ordine_referenze
            INNER JOIN maaking_users U on U.userid=R.id_utente_referenze
            WHERE id_gas_referenze='"._USER_ID_GAS."'
                AND R.id_utente_referenze>0

            ORDER BY id_ordine_referenze desc
            LIMIT 100;";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    foreach($rows as $row){



        $vao = round(VA_ORDINE_GAS_SOLO_NETTO($row["id_ordine_referenze"],_USER_ID_GAS),2);
        $V4 = VA_ORDINE_GAS_SOLO_RETT($row["id_ordine_referenze"],_USER_ID_GAS);
        $V2V3 = VA_ORDINE_GAS_SOLO_EXTRA_V2_V3($row["id_ordine_referenze"],_USER_ID_GAS);

        $query = "SELECT  (
                COALESCE((SELECT SUM(importo) FROM retegas_cassa_utenti WHERE id_gas=:id_gas AND segno='+' AND id_ordine=:id_ordine ),0)
                -
                COALESCE((SELECT SUM(importo) FROM retegas_cassa_utenti WHERE id_gas=:id_gas AND segno='-' AND id_ordine=:id_ordine ),0)
                )  As risultato";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id_gas', $row["id_gas_referenze"], PDO::PARAM_INT);
        $stmt->bindParam(':id_ordine', $row["id_ordine_referenze"], PDO::PARAM_INT);
        $stmt->execute();
        $rowTC = $stmt->fetch(PDO::FETCH_ASSOC);

        $tnc = round($rowTC["risultato"],2);

        $delta = round($vao+$V4+$V2V3+$tnc,2);
        $spesa = round($V4+$V2V3,2);
        $lordo = round($vao+$V4+$V2V3,2);

        if($row["is_printable"]>0){
            $sign='<i class="fa fa-circle text-muted"></i>';
        }else{
            $sign='<i class="fa fa-circle text-danger"></i>';
        }

        if($delta==0){
            $pal = 'OK <i class="fa fa-check text-success"></i>';
        }else{
            $pal = 'NO <i class="fa fa-times text-danger"></i>';
        }


        $h.= '<tr>';
            //$h.= '<td><a href="#ajax_rd4/ordini/edit.php?id='.$row["id_ordini"].'"><i class="fa fa-wrench"></i></a></td>';
            $h.= '<td>'.$sign.' '.$row["id_ordini"].'</td>';
            $h.= '<td>'.$row["descrizione_ordini"].'</td>';
            $h.= '<td>'.$row["fullname"].'</td>';
            $h.= '<td class="text-right">'.$vao.'</td>';
            $h.= '<td class="text-right">'.$spesa.'</td>';
            $h.= '<td class="text-right">'.$lordo.'</td>';
            $h.= '<td class="text-right">'.$tnc.'</td>';
            $h.= '<td class="text-right">'.$delta.'</td>';
            $h.= '<td class="text-right">'.$pal.'</td>';
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
    $h.= '<th>4</th>';
    $h.= '<th>5</th>';
    $h.= '<th>6</th>';
    $h.= '</tr>';
    $h.= '</tfoot>';
    $h.= '</table>';

  //-------------------------ORDINO

?>

<h1>Allineamento ordini :</h1>
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