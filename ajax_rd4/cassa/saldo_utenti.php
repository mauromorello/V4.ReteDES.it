<?php
    require_once("inc/init.php");
    require_once("../../lib_rd4/class.rd4.user.php");
    require_once("../../lib_rd4/class.rd4.ordine.php");
    $converter = new Encryption();
    $ui = new SmartUI;
    $page_title = "Saldo Utenti";
    $page_id = "saldo_utenti";
    $orientation = "portrait";

    if(!(_USER_PERMISSIONS & perm::puo_gestire_la_cassa)){
        echo rd4_go_back("Non ho i permessi per la cassa");die;
    }

    $h = '<div id="table_container">';
    $h.= '<table id="table_saldo_utenti" class="table table-condensed smart-form has-tickbox rd4">';
    $h.= '<thead>';
    $h.= '<tr>';
    //$h.= '<th class="filter-false"></th>';
    $h.= '<th class="filter-false"></th>';
    $h.= '<th class="filter-select"></th>';
    $h.= '<th>Utente</th>';
    $h.= '<th class="">Da registrare</th>';
    $h.= '<th class="">Totale</th>';
    $h.= '<th class="">Disponibile</th>';
    $h.= '</tr>';
    $h.= '</thead>';
    $h.= '<tbody>';

    $stmt = $db->prepare( "SELECT * FROM maaking_users WHERE id_gas=:id_gas;");
    $id_gas = _USER_ID_GAS;
    $stmt->bindParam(':id_gas',$id_gas, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    foreach($rows as $row){

        if($row["isactive"]<>1){
            $icona_user = " text-danger ";
            $testo_user = "KO";

        }else{
            $icona_user = " text-success ";
            $testo_user = "OK";
        }

        $link_user = $converter->encode($row["userid"]);
        $h.= '<tr>';
            //$h.= '<td><a href="#ajax_rd4/ordini/edit.php?id='.$row["id_ordini"].'"><i class="fa fa-wrench"></i></a></td>';
            $h.= '<td style="vertical-align: middle;"><a href="#ajax_rd4/cassa/movimenti.php?id='.$link_user.'"class="btn btn-circle btn-xs"><i class="fa fa-table"></i></a></td>';
            $h.= '<td class="text-center"><i class="fa fa-user '.$icona_user.'"></i><br><small>'.$testo_user.'</small></td>';
            $h.= '<td>'.$row["userid"].': '.$row["fullname"].'<br><small><a href="mailto:'.$row["email"].'?subject=Messaggio dal cassiere '._USER_FULLNAME.'" target="_BLANK"">'.$row["email"].'</a></small></td>';
            $h.= '<td class="text-right text-danger font-md" style="vertical-align: middle;">'._NF(VA_CASSA_SALDO_UTENTE_DA_REGISTRARE($row["userid"])).'</td>';
            $h.= '<td class="text-right font-md" style="vertical-align: middle;">'._NF(VA_CASSA_SALDO_UTENTE_REGISTRATO($row["userid"])).'</td>';
            $h.= '<td class="text-right text-success font-md" style="vertical-align: middle;">'._NF(VA_CASSA_SALDO_UTENTE_TOTALE($row["userid"])).'</td>';

        $h.= '</tr>';


        unset($O);
        unset($U);
    }

    $h.= '</tbody>';
    $h.='<tbody class="tablesorter-infoOnly">
                <tr>
                  <th></th>
                  <th colspan=2>Totali:</th>
                  <th data-math="col-sum" class="font-lg text-right"></th>
                  <th data-math="col-sum" class="font-lg text-right"></th>
                  <th data-math="col-sum" class="font-lg text-right"></th>
                </tr>
              </tbody>';
    $h.= '</table>
          </div>';

  //-------------------------ORDINO
  $css = css_report($orientation);
  if($_POST["o"]=="pdf"){

    include_once('../../lib_rd4/dompdf/dompdf_config.inc.php');
        $dompdf = new DOMPDF();

          $html ='<html>
      <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <title>ReteDES.it :: Saldi utenti</title>
        '.pdf_css().$css.'
      </head>
      <body>'.pdf_testata($O,$orientation).$h.'</body></html>';

          $dompdf->load_html($html);
          $dompdf->set_paper("letter", $orientation);
          $dompdf->render();
          $file_title = "Cassa_Utenti_".rand(1000,1000000).".pdf";
          $dompdf->stream($file_title, array("Attachment" => false));

          exit(0);
    }

  $buttons[]='<button  class="show_pdf btn btn-defalut btn-default"><i class="fa fa-file-pdf-o"></i>  PDF</button>';
  $buttons[]='<button  onclick="selectElementContents( document.getElementById(\'table_container\') ); " class="btn btn-default btn-default"><i class="fa fa-copy"></i>  COPIA</button>';
  $buttons[]='<button   class="btn btn-default btn-default" onclick=\'printDiv("table_container")\'><i class="fa fa-print"></i>  Stampa</button>';


?>

<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
        </article>
    </div>
</section>
<p></p>
<?php echo navbar_report($page_title, $buttons); ?>
<hr>
<?php echo $h; ?>



<script type="text/javascript">

    pageSetUp();


    var pagefunction = function() {

        //-------------------------HELP
        document.title = '<?php echo "ReteDES.it :: $page_title";?>';
        <?php echo help_render_js($page_id); ?>
        //-------------------------HELP

        //loadScript("js_rd4/plugin/tablesorter/js/jquery.tablesorter.widgets.js",startTable);
        loadScript("js_rd4/plugin/tablesorter/js/jquery.tablesorter.widgets.js",loadMath);
        function loadMath(){
            loadScript("js_rd4/plugin/tablesorter/js/widgets/widget-math.js", startTable);
        }

        function startTable(){

                $.extend($.tablesorter.themes.bootstrap, {
                    table      : 'table table-bordered',
                    caption    : 'caption',
                    sortNone   : 'bootstrap-icon-unsorted',
                    sortAsc    : 'fa fa-arrow-up',
                    sortDesc   : 'fa fa-arrow-down'

                  });

                t = $('#table_saldo_utenti').tablesorter({
                    theme: 'bootstrap',
                        //debug:true,
                        widgets: ["uitheme","filter","zebra","math"],
                     widgetOptions : {
                         zebra : ["even", "odd"],
                         filter_reset : ".reset",
                         math_data     : 'math', // data-math attribute
                         math_ignore   : [0,1],
                         math_mask     : '0<?echo _USER_CARATTERE_DECIMALE ?>00',
                         math_complete : function($cell, wo, result, value, arry) {

                            return (value / 100);
                          }
                        }
                });



        }//END STARTTABLE

        $('.show_pdf').click(function(){
            var $this = $(this);
            var id = $this.data('id_ordine');
            open('POST', '<?php echo APP_URL; ?>/ajax_rd4/cassa/saldo_utenti.php', {id:id, o:'pdf', dummy:<?php echo rand(1000,9999); ?> }, '_blank');
            return false;
        });
        $('.show_movimenti_utente').click(function(){
            var $this = $(this);
            var id = $this.data('id');

            return false;
        });
    }
    // end pagefunction

    loadScript("js_rd4/plugin/tablesorter/js/jquery.tablesorter.min.js", pagefunction);



</script>