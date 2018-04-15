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

//ORDINAMENTO
$s = CAST_TO_STRING($_POST["s"]);
if ($s==""){
    $s = CAST_TO_STRING($_GET["s"]);
    if ($s==""){
        $s=="codice";
    }
}


if ($s=="codice"){
    $sort = " ORDER BY art_codice ASC ";
}
if ($s=="descrizione"){
    $sort = " ORDER BY art_desc ASC ";
}
if ($s=="qta_ord"){
    $sort = " ORDER BY  SUM(qta_ord) DESC ";
}
if ($s=="ditta"){
    $sort = " ORDER BY descrizione_ditta ASC";
}
//ORDINAMENTO


if($id_ordine==0){echo "missing id"; die();}
$O = new ordine($id_ordine);
$page_title = "Report - articoli raggruppati semplificato";
$page_id = "report_articoli_raggruppati_semplificato";
$orientation = "portrait"; // portrait / landscape
//------------------------------PAGE


$html='<h3>Ordine #'.$O->id_ordini.' - '.$O->descrizione_ordini.'</h3>';
//-----------------------------------CONTENT
$html.='<table id="table_exp" class="rd4" style="margin-left: auto; margin-right: auto">
            <thead >
                <tr class="intestazione" >
                    <th style="width:20%;">Codice</th>
                    <th style="">Descrizione</th>
                    <th style="width:20%;" class="text-right">Quantità Ordinata</th>
                    <th style="width:10%;" class="text-right">Prezzo</th>
                    <th style="width:10%;" class="text-right">Totale</th>
                </tr>
            </thead>
            <tbody>';
//-----------------------------------DATI

//WARNINGS
if($O->codice_stato<>"CO"){
    $warning ='<div class="alert alert-danger"><strong>ATTENZIONE:</strong> questo ordine non è ancora stato convalidato, e quindi le cifre potrebbero non essere definitive.</div>';
}else{
    $warning ='';
}
$old_descrizione_ditta="";

$sql = "SELECT art_codice, art_desc,art_um , SUM(qta_ord) as qta_ord, prz_dett, descrizione_ditta FROM retegas_dettaglio_ordini WHERE id_ordine=:id_ordine GROUP BY art_codice, prz_dett $sort;";
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

    if($s=="ditta"){
        $descrizione_ditta=$row["descrizione_ditta"];

        if($old_descrizione_ditta<>$descrizione_ditta){
            $html.='<tr class="">';
            $html.='<td colspan="5"><h4>'.$row["descrizione_ditta"].'</h4></td>';
            $html.='</tr>';
        }
    }

    $html.='<tr class="'.$row_class.'">';
    $html.='<td>'.$row["art_codice"].'</td>';
    $html.='<td>'.$row["art_desc"].' <span class="note">('.$row["art_um"].')</span></td>';
    $html.='<td style="text-align:right">'._NF($row["qta_ord"]).'</td>';
    $html.='<td style="text-align:right">'._NF($row["prz_dett"]).'</td>';
    $html.='<td style="text-align:right">'._NF($row["prz_dett"]*$row["qta_ord"]).'</td>';
    $html.='</tr>';

    $totale_articoli += $row["qta_ord"];
    $totale_cifra += ($row["prz_dett"]*$row["qta_ord"]);
    if($s=="ditta"){
        $old_descrizione_ditta=$descrizione_ditta;
    }
}


//-----------------------------------DATI
$html.='    </tbody>
            <tfoot>
                <tr class="totale">
                    <th colspan=2 style="text-align:right">Totali</th>
                    <th style="text-align:right"><strong>'._NF($totale_articoli).'</strong></th>
                    <th style="text-align:right"><strong></strong></th>
                    <th style="text-align:right"><strong>'._NF($totale_cifra).'</strong></th>
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

$buttons[]='<button  data-id_ordine="'.$O->id_ordini.'" class="show_pdf btn btn-defalut btn-default"><i class="fa fa-file-pdf-o"></i><span class="hidden-xs">  PDF</span></button>';
$buttons[]='<button  onclick="selectElementContents( document.getElementById(\'table_exp\') ); " class="btn btn-default btn-default"><i class="fa fa-copy"></i><span class="hidden-xs">  COPIA</span></button>';
$buttons[]='<button  data-id_ordine="" class="btn btn-default btn-default" onclick=\'printDiv("table_container")\'><i class="fa fa-print"></i><span class="hidden-xs">  Stampa</span></button>';
$buttons[]='<button  data-id_ordine="'.$O->id_ordini.'" class="btn btn-default btn-default" onclick=\'$("#opzioni_report").toggleClass("hidden");\'><i class="fa fa-gear"></i><span class="hidden-xs">  Opzioni</span></button>';


?>
<?php echo $O->navbar_ordine(); ?>
<?php echo navbar_report($page_title, $buttons); ?>
<div class="well well-sm hidden margin-top-10" id="opzioni_report">
<div class="row">
        <form class="smart-form">
        <div class="col-md-12">
            <div class="padding-10">
                <section>
                    <label class="label">Ordinamento:</label>
                    <div class="inline-group">
                        <label class="radio">
                            <input type="radio" name="radio-inline" <?php if($s=="codice"){echo'checked="checked"';} ?> value="codice">
                            <i></i>Codice</label>
                        <label class="radio">
                            <input type="radio" name="radio-inline" <?php if($s=="descrizione"){echo'checked="checked"';} ?> value="descrizione">
                            <i></i>Descrizione</label>
                        <label class="radio">
                            <input type="radio" name="radio-inline" <?php if($s=="qta_ord"){echo'checked="checked"';} ?> value="qta_ord">
                            <i></i>Quantità ordinata</label>
                        <label class="radio">
                            <input type="radio" name="radio-inline" <?php if($s=="ditta"){echo'checked="checked"';} ?> value="ditta">
                            <i></i>Ditta</label>
                    </div>
                </section>
            </div>
        </div>
        </form>
</div>
<button class="btn btn-success pull-right" id="aggiorna_report"  data-id_ordine="<?php echo $O->id_ordini; ?>">Aggiorna</button>
<div class="clearfix"></div>
</div>
<div class="container_report" style="overflow-x:auto;width:100%; height:400px; overflow-y:auto;">
    <?php echo $warning; ?>
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
            var s = $('input[name="radio-inline"]:checked').val();

            open('POST', '<?php echo APP_URL; ?>/ajax_rd4/reports/articoli_semplificato.php', {id:id, o:'pdf', dummy:<?php echo rand(1000,9999); ?>, s:s}, '_blank');
            return false;
        });

        $('#aggiorna_report').click(function(){
            var $this = $(this);
            var id = $this.data('id_ordine');
            var s = $('input[name="radio-inline"]:checked').val();


            location.replace('<?php echo APP_URL; ?>/#ajax_rd4/reports/articoli_semplificato.php?id='+id+'&s='+s);
            return false;
        });

    } // end pagefunction

pagefunction();
</script>
