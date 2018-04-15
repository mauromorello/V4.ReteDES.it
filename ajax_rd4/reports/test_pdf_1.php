<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.ordine.php");
$ui = new SmartUI;

$id_ordine = CAST_TO_INT($_POST["id"],0);
if ($id_ordine==0){
    $id_ordine = CAST_TO_INT($_GET["id"],0);
}
if($id_ordine==0){echo "missing id"; die();}

$O = new ordine($id_ordine);
$page_title = "Report - articoli ordinati";




//-----------------------------------HEADER
$html='<table id="table_exp" class="rd4">
            <thead >
                <tr class="intestazione" >
                    <th style="width:20%;">Codice</th>
                    <th style="width:40%;">Descrizione</th>
                    <th style="width:20%;" class="text-right">Quantità Articoli</th>
                    <th style="width:10%;" class="text-right">Scatole</th>
                    <th style="width:10%;" class="text-right">Avanzo</th>
                </tr>
            </thead>
            <tbody>';
//-----------------------------------DATI

$sql = "SELECT art_codice, art_desc,art_um,SUM(qta_arr) as totale_articolo FROM retegas_dettaglio_ordini WHERE id_ordine=:id_ordine GROUP BY art_codice, art_desc HAVING SUM(qta_arr)>0";
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

    $sql = "SELECT qta_scatola, qta_minima FROM retegas_articoli WHERE id_listini=:id_listini and codice=:art_codice LIMIT 1;";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id_listini', $O->id_listini, PDO::PARAM_INT);
    $stmt->bindParam(':art_codice', $row["art_codice"], PDO::PARAM_STR);
    $stmt->execute();
    $rowA = $stmt->fetch(PDO::FETCH_ASSOC);
    if($stmt->rowCount()==1){
        $scatola = $rowA["qta_scatola"];
        $multiplo = $rowA["qta_minima"];
        $scatole=0;
        $avanzo=0;

        $i=$row["totale_articolo"];
        while($i>0){
            $i -= $scatola;
            $scatole ++;
        }
        if($i<0){
            //$alert='<span class="label label-danger">NO</span>';
            $avanzo = $scatola + $i;
        }else{
            //$alert="OK";
            $scatole++;
        }
        $scatole--;

    }else{
        $scatole="";
        $avanzo="";
    }
    if($scatole==0){$scatole="";}
    if($avanzo==0){$avanzo="";}



    $html.='<tr class="'.$row_class.'">';
    $html.='<td>'.$row["art_codice"].'</td>';
    $html.='<td>'.$row["art_desc"].' <span class="note">('.$row["art_um"].')</span></td>';
    $html.='<td class="text-right"><strong>'.number_format($row["totale_articolo"],2, ',', '').'</strong></td>';
    $html.='<td class="text-right">'.number_format($scatole,2, ',', '').'</td>';
    $html.='<td class="text-right">'.number_format($avanzo,2, ',', '').'</td>';
    $html.='</tr>';

    $totale += $row["totale_articolo"];
    $Tscatole += $scatole;
    $Tavanzi += $avanzo;

}


//-----------------------------------DATI
$html.='    </tbody>
            <tfoot>
                <tr class="totale">
                    <th colspan=2 class="text-right">Totale</th>
                    <th class="text-right"><strong>'.number_format($totale,2, ',', '').'</strong></th>
                    <th class="text-right"><strong>'.number_format($Tscatole,2, ',', '').'</strong></th>
                    <th class="text-right"><strong>'.number_format($Tavanzi,2, ',', '').'</strong></th>
                </tr>
            </tfoot>
         </table>';
//-----------------------------------FOOTER

$css = css_report();

if($_POST["o"]=="print"){

}
if($_POST["o"]=="pdf"){
    include_once('../../lib_rd4/dompdf/dompdf_config.inc.php');
    $dompdf = new DOMPDF();

      $html ='<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <title>ReteDES.it :: Ordine #'.$O->id_ordini.'</title>
    '.pdf_css().'
  </head>
  <body>'.pdf_testata($O).$css.$html.'</body></html>';

      $dompdf->load_html($html);
      $dompdf->set_paper("letter", "landscape");
      $dompdf->render();
      $dompdf->stream("dompdf_out-".rand(1000,1000000).".pdf", array("Attachment" => true));

      exit(0);
}
if($_POST["o"]=="xls"){

}


$buttons[]='<button  data-id_ordine="'.$O->id_ordini.'" class="show_pdf btn btn-defalut btn-default"><i class="fa fa-file-pdf-o"></i>  PDF</button>';
$buttons[]='<button  onclick="selectElementContents( document.getElementById(\'table_exp\') ); " class="btn btn-default btn-default"><i class="fa fa-copy"></i>  COPIA</button>';
$buttons[]='<button  data-id_ordine="" class="btn btn-default btn-default" onclick=\'printDiv("table_container")\'><i class="fa fa-print"></i>  Stampa</button>';
$buttons[]='<button  data-id_ordine="'.$O->id_ordini.'" class="btn btn-default btn-default" onclick=\'$("#opzioni_report").toggleClass("hidden");\'><i class="fa fa-gear"></i>  Opzioni</button>';


?>
<?php echo navbar_ordine($O->id_ordini); ?>
<?php echo navbar_report("Test", $buttons); ?>
<div class="well well-sm hidden" id="opzioni_report">
Nessuna opzione per questo report.
</div>
<div class="container_report" style="overflow-x:auto;width:100%; height:600px; overflow-y:auto;">
    <div id="table_container"><?php echo $css.$html; ?></div>
</div>
<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html('report_articoli_1',$page_title); ?>
        </article>
    </div>
</section>
<script type="text/javascript">

    pageSetUp();
    //onclick="printDiv('printableArea')"


    var pagefunction = function(){

        //------------HELP WIDGET
        <?php echo help_render_js('report_articoli_1');?>
        //------------END HELP WIDGET


        $('.show_pdf').click(function(){
            var $this = $(this);
            var id = $this.data('id_ordine');
            open('POST', '<?php echo APP_URL; ?>/ajax_rd4/reports/test_pdf_1.php', {id:id, o:'pdf', dummy:<?php echo rand(1000,9999); ?> }, '_blank');
            return false;
        });

    } // end pagefunction

pagefunction();
</script>
