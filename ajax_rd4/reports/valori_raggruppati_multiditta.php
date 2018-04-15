<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.ordine.php");
require_once("../../lib_rd4/class.rd4.ditta.php");
$ui = new SmartUI;
//-------------------------------INIT

//-------------------------------PAGE
$id_ordine = CAST_TO_INT($_POST["id"],0);
if ($id_ordine==0){
    $id_ordine = CAST_TO_INT($_GET["id"],0);
}
if($id_ordine==0){echo "missing id"; die();}
$O = new ordine($id_ordine);

$ro = CAST_TO_INT($_POST["ro"],0);
if ($ro==0){
    $ro = CAST_TO_INT($_GET["ro"],0);
}
$rg = CAST_TO_INT($_POST["rg"],0);
if ($rg==0){
    $rg = CAST_TO_INT($_GET["rg"],0);
}

$page_title = "Report - valori raggruppati MULTIDITTA";
$page_id = "report_valori_raggruppati_multiditta";
$orientation = "portrait"; // portrait / landscape
//------------------------------PAGE



//-----------------------------------CONTENT-
$html= '<h3>Ordine #'.$O->id_ordini.' - '.$O->descrizione_ordini.'</h3>';
$html.=' <h3>Raggruppamento Valori MultiDITTA</h3>
        <table id="table_exp" class="rd4" style="margin-left: auto; margin-right: auto">
            <thead >
                <tr class="intestazione" >
                    <th style="width:20%;">Codice</th>
                    <th style="width:40%;">Descrizione</th>
                    <th style="width:20%;" class="text-right">Q. Arr</th>
                    <th style="width:10%;" class="text-right">Valore</th>
                </tr>
            </thead>
            <tbody>';
//-----------------------------------DATI
$sql = "SELECT id_ditta, descrizione_ditta from retegas_dettaglio_ordini WHERE id_ordine=:id_ordine GROUP BY id_ditta HAVING id_ditta IS NOT NULL ";
$stmt = $db->prepare($sql);
$stmt->bindParam(':id_ordine', $O->id_ordini, PDO::PARAM_INT);
$stmt->execute();
$rowsD = $stmt->fetchAll(PDO::FETCH_ASSOC);


foreach($rowsD as $rowD){
    //-----------------------------------------------------------------------------------SINGLE GAS
    $D = new ditta($rowD["id_ditta"]);

    $totale=0;
    $totale_valore = 0;

    $html.='<tr>
                <th colspan=4 style="text-align:left">'.$D->descrizione_ditte.'</th>
            </tr>';

    if($ro==0){
        $escludiRettifiche=" AND art_codice NOT LIKE '@@%' ";
    }

    if($rg==0){
        $escludiRettificheGAS=" AND art_codice NOT LIKE '##%'";
    }

    $sql = "SELECT art_codice, art_desc,art_um,SUM(qta_arr) as totale_articolo FROM retegas_dettaglio_ordini inner join maaking_users U on U.userid=id_utenti WHERE id_ordine=:id_ordine AND id_ditta=:id_ditta ".$escludiRettifiche.$escludiRettificheGAS." GROUP BY art_codice, art_desc HAVING SUM(qta_arr)>0";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id_ordine', $O->id_ordini, PDO::PARAM_INT);
    $stmt->bindParam(':id_ditta', $D->id_ditte, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows AS $row){
        $riga++;
        if(is_integer($riga/2)){
            $row_class="odd";
        }else{
            $row_class="even";
        }

        $codice = $row["art_codice"];
        $descrizione= $row["art_desc"];
        $um = $row["art_um"];

        $sql = "SELECT qta_arr, prz_dett_arr FROM retegas_dettaglio_ordini inner join maaking_users U on U.userid=id_utenti WHERE id_ordine=:id_ordine AND id_ditta=:id_ditta AND art_codice=:art_codice ";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_ordine', $O->id_ordini, PDO::PARAM_INT);
        $stmt->bindParam(':id_ditta', $D->id_ditte, PDO::PARAM_INT);
        $stmt->bindParam(':art_codice', $codice, PDO::PARAM_STR);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $tot_riga=0;
        foreach($rows AS $rowA){
            $tot_riga += ($rowA["qta_arr"]*$rowA["prz_dett_arr"]);
        }


        $html.='<tr class="'.$row_class.'">';
        $html.='<td>'.$codice.'</td>';
        $html.='<td>'.$descrizione.' <span class="note">('.$um.')</span></td>';
        $html.='<td style="text-align:right"><strong>'.($row["totale_articolo"]+0).'</strong></td>';
        $html.='<td style="text-align:right">'._NF($tot_riga).'</td>';
        $html.='</tr>';

        $totale += round($row["totale_articolo"]);
        $totale_valore += $tot_riga;

        $totaleGROSSO += round($row["totale_articolo"]);
        $totale_valoreGROSSO += $tot_riga;

    }

    $html.='<tr class="subtotale">
                        <th colspan=2 style="text-align:right">subtotale '.$G->descrizione_gas.'</th>
                        <th style="text-align:right"><strong>'.($totale+0).'</strong></th>
                        <th style="text-align:right"><strong>'._NF($totale_valore+0).'</strong></th>
                    </tr>';
    //-----------------------------------DATI
    //-----------------------------------------------------------------------------------SINGLE GAS
}



$html.='    </tbody>
            <tfoot>
                <tr class="totale">
                    <th colspan=2 style="text-align:right">Totale tutti i GAS</th>
                    <th style="text-align:right"><strong>'.($totaleGROSSO+0).'</strong></th>
                    <th style="text-align:right"><strong>'._NF($totale_valoreGROSSO+0).'</strong></th>
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
<div class="well well-sm hidden margin-top-10" id="opzioni_report">
    <div class="row ">
            <div class="col-md-6">
                <div class="padding-10 smart-form">
                    <label class="checkbox">
                        <input type="checkbox" name="includiRETT" id="includirettifiche">
                        <i></i>Includi rettifiche ordine</label>
                    <label class="checkbox">
                        <input type="checkbox" name="includiRETTGAS" id="includirettificheGAS">
                        <i></i>Includi rettifiche GAS</label>

                </div>
            </div>
    </div>
    <button class="btn btn-success pull-right" id="aggiorna_report"  data-id_ordine="<?php echo $O->id_ordini; ?>">Aggiorna</button>
    <div class="clearfix"></div>
</div>
<div class="alert alert-danger margin-top-10"><strong>ATTENZIONE: </strong> Le cifre prodotte da questo record sono da verificare incrociandole con quelle di altri report, in quanto non è ancora stato testato a fondo.</div>

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
            open('POST', '<?php echo APP_URL; ?>/ajax_rd4/reports/valori_raggruppati_multiditta.php', {id:id, o:'pdf', dummy:<?php echo rand(1000,9999); ?> }, '_blank');
            return false;
        });

        $('#aggiorna_report').click(function(){
            var $this = $(this);
            var id = $this.data('id_ordine');
            var ro=0;
            if ($('#includirettifiche').prop('checked')){
                ro=1;
            }
            var rg=0;
            if ($('#includirettificheGAS').prop('checked')){
               rg=1;
            }

            location.replace('<?php echo APP_URL; ?>/#ajax_rd4/reports/valori_raggruppati_multiditta.php?id='+id+'&ro='+ro+'&rg='+rg);
            return false;
        });

    } // end pagefunction

pagefunction();
</script>
