<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.ordine.php");
$ui = new SmartUI;
//-------------------------------INIT

//-------------------------------PAGE
$id_ordine = CAST_TO_INT($_POST["id"],0);
if ($id_ordine==0){
    $id_ordine = CAST_TO_INT($_GET["id"],0);
}
if($id_ordine==0){echo "missing id"; die();}
$O = new ordine($id_ordine);
$page_title = "Report - La mia spesa";
$page_id ="report_la_mia_spesa";
$orientation = "landscape"; // portrait / landscape
//------------------------------PAGE



//-----------------------------------CONTENT
$html='<table id="table_exp" class="rd4" style="margin-left: auto; margin-right: auto">
            <thead >
                <tr class="intestazione" >
                    <th style="width:10%;">Codice</th>
                    <th >Descrizione</th>
                    <th style="width:5%;">Ordinati</th>
                    <th style="width:10%;">Arrivati</th>
                    <th style="width:10%;">Prezzo</th>
                    <th style="width:5%;">Totale articoli</th>
                    <th style="width:5%;">Totale rettifiche</th>
                    <th style="width:10%;">Totale </th>
                </tr>
            </thead>
            <tbody>';
//-----------------------------------DATI

$sql = "SELECT art_codice, art_desc, art_um, qta_ord, qta_arr, prz_dett_arr, prz_dett FROM retegas_dettaglio_ordini WHERE id_ordine=:id_ordine AND id_utenti='"._USER_ID."'";
$stmt = $db->prepare($sql);
$stmt->bindParam(':id_ordine', $O->id_ordini, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($rows AS $row){
    $riga++;
    if(is_integer($riga/2)){
        $row_class="odd";
    }else{
        $row_class="even";
    }

    $pos = strrpos($row["art_codice"], "@@");
    if ($pos === false) {
        // not found...
        $totale_articoli = round($row["qta_arr"]*$row["prz_dett_arr"],4);
        $totale_costi = "";
        $qta_ord = $row["qta_ord"];
    }else{
        $totale_costi = round($row["qta_arr"]*$row["prz_dett_arr"],4);
        $totale_articoli = "";
        $row_class = "costo";
        $qta_ord="";
    }
    $label="";
    $label_prz ="";

    if(round($row["qta_arr"],4)<>round($row["qta_ord"],4)){
        $label = '<br><div style="margin-left: auto; margin-right: auto; padding:1px; background-color:#AC5301; color:#FFF; font-size:10px;">MODIFICATA</div>';
    }
    if(round($row["qta_arr"],4)==0){
        $label = '<br><div style="margin-left: auto; margin-right: auto; padding:1px; background-color:#AC5301; color:#FFF; font-size:10px;"><strong style="font-size:10px;">ANNULLATA</strong></div>';
    }
    if(round($row["prz_dett"],4)<>round($row["prz_dett_arr"],4)){
        $label_prz = '<br><div style="text-align:center; padding:1px; background-color:#AC5301; color:#FFF; font-size:10px;">MODIFICATO</div>';
    }
    $html.='<tr class="'.$row_class.'">';
    $html.='<td>'.$row["art_codice"].'</td>';
    $html.='<td>'.$row["art_desc"].' <span class="note">('.$row["art_um"].')</span></td>';
    $html.='<td style="text-align:center">'.number_format($qta_ord,2, ',', '').'</td>';
    $html.='<td style="text-align:center">'.number_format($row["qta_arr"],2, ',', '').$label.'</td>';
    $html.='<td style="text-align:right">'.number_format($row["prz_dett_arr"],2, ',', '').' &#0128;'.$label_prz.'</td>';
    $html.='<td style="text-align:right"><strong>'.number_format($totale_articoli,2, ',', '').'</strong></td>';
    $html.='<td style="text-align:right">'.number_format($totale_costi,2, ',', '').'</td>';
    $html.='<td style="text-align:right"></td>';
    $html.='</tr>';

    $totaleA += round($totale_articoli,4);
    $totaleC += round($totale_costi,4);

}
    if($totaleC==0){$totaleC="";}
    $totaleT = $totaleA+$totaleC;
//-----------------------------------DATI
$html.='    </tbody>
            <tfoot>
                <tr class="totale">
                    <th colspan=5 style="text-align:right">Totali</th>
                    <th style="text-align:right"><strong>'.number_format($totaleA,2, ',', '').' </strong></th>
                    <th style="text-align:right"><strong>'.number_format($totaleC,2, ',', '').' </strong></th>
                    <th style="text-align:right"><strong>'.number_format($totaleT,2, ',', '').' </strong></th>
                </tr>
            </tfoot>
         </table>';
//-----------------------------------FOOTER

$css = css_report($orientation);
if($_POST["o"]=="print"){}
if($_POST["o"]=="pdf"){
    include_once('../../lib_rd4/dompdf/dompdf_config.inc.php');
    $dompdf = new DOMPDF();

      $html ='<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <title>ReteDES.it :: Ordine #'.$O->id_ordini.'</title>
    '.pdf_css().$css.'
  </head>
  <body>'.pdf_testata($O,$orientation).$html.'</body></html>';

      $dompdf->load_html($html);
      $dompdf->set_paper("letter", $orientation);
      $dompdf->render();
      $file_title = "Ord. ".$O->id_ordini."_".$page_id."_".rand(1000,1000000).".pdf";
      $dompdf->stream($file_title, array("Attachment" => false));

      exit(0);
}

$buttons[]='<button  data-id_ordine="'.$O->id_ordini.'" class="show_pdf btn btn-defalut btn-default"><i class="fa fa-file-pdf-o"></i>  PDF</button>';
$buttons[]='<button  onclick="selectElementContents( document.getElementById(\'table_exp\') ); " class="btn btn-default btn-default"><i class="fa fa-copy"></i>  COPIA</button>';
$buttons[]='<button  data-id_ordine="" class="btn btn-default btn-default" onclick=\'printDiv("table_container")\'><i class="fa fa-print"></i>  Stampa</button>';
$buttons[]='<button  data-id_ordine="'.$O->id_ordini.'" class="btn btn-default btn-default" onclick=\'$("#opzioni_report").toggleClass("hidden");\'><i class="fa fa-gear"></i>  Opzioni</button>';


?>
<?php echo $O->navbar_ordine(); ?>
<?php echo navbar_report($page_title, $buttons); ?>
<div class="well well-sm hidden" id="opzioni_report">
Nessuna opzione per questo report.
</div>
<div class="container_report" style="overflow-x:auto;width:100%; height:400px; overflow-y:auto;">
    <div id="table_container"><?php echo $css.$html; ?></div>
</div>
<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
        </article>
    </div>
</section>
<script type="text/javascript">

    pageSetUp();
    //onclick="printDiv('printableArea')"


    var pagefunction = function(){


        //------------HELP WIDGET
        document.title = '<?php echo "ReteDES.it :: $page_title";?>';
        <?php echo help_render_js($page_id);?>
        //------------END HELP WIDGET


        $('.show_pdf').click(function(){
            var $this = $(this);
            var id = $this.data('id_ordine');
            open('POST', 'http://retegas.altervista.org/gas4/ajax_rd4/reports/la_mia_spesa.php', {id:id, o:'pdf', dummy:<?php echo rand(1000,9999); ?> }, '_blank');
            return false;
        });

    } // end pagefunction

pagefunction();
</script>
