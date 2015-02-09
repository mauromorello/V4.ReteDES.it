<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.ordine.php");
require_once("../../lib_rd4/class.rd4.gas.php");
$ui = new SmartUI;
//-------------------------------INIT

//-------------------------------PAGE
$id_ordine = CAST_TO_INT($_POST["id"],0);
if ($id_ordine==0){
    $id_ordine = CAST_TO_INT($_GET["id"],0);
}
if($id_ordine==0){echo "missing id"; die();}
$O = new ordine($id_ordine);
$page_title = "Report - dettaglio utenti GAS";
$page_id = "report_utenti_gas";
$orientation = "landscape"; // portrait / landscape
//------------------------------PAGE





//-----------------------------------CONTENT
$id_gas = CAST_TO_INT($_POST["g"]);
if ($id_gas==0){
    $id_gas = CAST_TO_INT($_GET["g"]);
}
if($id_gas==0){$id_gas=_USER_ID_GAS;}
$G = new gas($id_gas);

$decimals = CAST_TO_INT($_POST["d"]);
if ($decimals==0){
    $decimals = CAST_TO_INT($_GET["d"]);
}
if($decimals==0){$decimals=2;}


$html= '<h3>'.$G->descrizione_gas.'</h3>
        <table id="table_exp" class="rd4" style="margin-left: auto; margin-right: auto">
            <thead >
                <tr class="intestazione" >
                    <th style="width:15%;">Utente</th>
                    <th style="width:15%;">Articolo</th>
                    <th >Descrizione</th>
                    <th style="width:10%;" class="text-right"></th>
                    <th style="width:10%;" class="text-right"></th>
                    <th style="width:10%;" class="text-right"></th>
                    <th style="width:10%;" class="text-right"></th>
                    <th style="width:10%;" class="text-right"></th>
                </tr>
            </thead>
            <tbody>';
//-----------------------------------DATI

$sql = "SELECT U.fullname, U.tel, U.userid
            FROM retegas_dettaglio_ordini D
            INNER JOIN maaking_users U ON U.userid = D.id_utenti
            WHERE D.id_ordine=:id_ordine
            AND U.id_gas=:id_gas
            GROUP BY U.userid";
$stmt = $db->prepare($sql);
$stmt->bindParam(':id_ordine', $O->id_ordini, PDO::PARAM_INT);
$stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($rows AS $rowU){
      $html_Rett ="";

      $html_ut ='<tr>';
        $html_ut .='<td colspan=8 class="separator"></td>';
      $html_ut .='</tr>';

      $html_ut .='<tr class="utente">';
        $html_ut .='<td colspan=3><strong>'.$rowU["fullname"].'</strong> <span>('.$rowU["tel"].')</span></td>';
        $html_ut .='<td class="text-center">Quantità</td>';
        $html_ut .='<td class="text-right">Prezzo</td>';
        $html_ut .='<td class="text-right">Netto</td>';
        $html_ut .='<td class="text-right">Altro</td>';
        $html_ut .='<td class="text-right">Totale</td>';

      $html_ut .='</tr>';

      //ARTICOLI
      $sql = "SELECT art_codice, art_desc, art_um, qta_ord, qta_arr, prz_dett_arr, prz_dett FROM retegas_dettaglio_ordini WHERE id_ordine=:id_ordine AND id_utenti=:id_utenti AND LEFT(art_codice , 2)<>'@@'";
      $stmt = $db->prepare($sql);
      $stmt->bindParam(':id_ordine', $O->id_ordini, PDO::PARAM_INT);
      $stmt->bindParam(':id_utenti', $rowU["userid"], PDO::PARAM_INT);
      $stmt->execute();
      $rowsA = $stmt->fetchAll(PDO::FETCH_ASSOC);

      $tot_utente = 0;
      $tot_utente_R = 0;
      foreach($rowsA AS $rowA){

      $qta_arr = $rowA["qta_arr"];
      $prz_dett_arr = $rowA["prz_dett_arr"];
      $tot_riga = $rowA["prz_dett_arr"]*$rowA["qta_arr"];

      $tot_utente = $tot_utente + $tot_riga;

      if($rowA["qta_arr"]<>$rowA["qta_ord"]){
          $q_modificata = 'style="border-left:5px solid red;"';
          $q_modificata_text_1 ='<br><span class="note">('.rd4_nf($rowA["qta_ord"],$decimals).')</span>';
          if($rowA["qta_arr"]==0){
              $q_modificata_text_2='ANNULLATA';
          }else{
              $q_modificata_text_2='MODIFICATA';
          }
      }else{
          $q_modificata = '';
          $q_modificata_text_1 ='';
          $q_modificata_text_2 ='';
      }


      $html_A ='<tr>';
        $html_A .='<td></td>';
        $html_A .='<td>'.$rowA["art_codice"].'</td>';
        $html_A .='<td>'.$rowA["art_desc"].' <span class="note">'.$rowA["art_um"].'</span></td>';
        $html_A .='<td class="text-center" '.$q_modificata.'>'.rd4_nf($qta_arr,$decimals).$q_modificata_text_1.'</td>';
        $html_A .='<td class="text-right">'.rd4_nf($prz_dett_arr,$decimals).' Eu.</td>';
        $html_A .='<td class="text-right">'.rd4_nf($tot_riga,$decimals).' Eu.</td>';
        $html_A .='<td></td>';
        $html_A .='<td class="text-right">'.$q_modificata_text_2.'</td>';

        $html_A .='</tr>';

        $html_ut .= $html_A;
      }
      //RETTIFICHE
      $sql = "SELECT art_codice, art_desc, art_um, qta_ord, qta_arr, prz_dett_arr, prz_dett FROM retegas_dettaglio_ordini WHERE id_ordine=:id_ordine AND id_utenti=:id_utenti AND LEFT(art_codice , 2)='@@'";
      $stmt = $db->prepare($sql);
      $stmt->bindParam(':id_ordine', $O->id_ordini, PDO::PARAM_INT);
      $stmt->bindParam(':id_utenti', $rowU["userid"], PDO::PARAM_INT);
      $stmt->execute();
      $rowsR = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $html_R = "";
      foreach($rowsR AS $rowR){

      $qta_arr_R = $rowR["qta_arr"];
      $prz_dett_arr_R = $rowR["prz_dett_arr"];
      $tot_riga_R = $rowR["prz_dett_arr"]*$rowR["qta_arr"];

      $tot_utente_R = $tot_utente_R + $tot_riga_R;

      $html_R ='<tr class="even">';
        $html_R .='<td></td>';
        $html_R .='<td>'.$rowR["art_codice"].'</td>';
        $html_R .='<td colspan=4>'.$rowR["art_desc"].'</td>';
        //$html_R .='<td class="text-center">'.rd4_nf($qta_arr_R,$decimals).'</td>';
        //$html_R .='<td class="text-right">'.rd4_nf($prz_dett_arr_R,$decimals).' Eu.</td>';
        //$html_R .='<td class="text-right"></td>';
        $html_R .='<td class="text-right">'.rd4_nf($tot_riga_R,$decimals).' Eu.</td>';
        $html_R .='<td></td>';

        $html_R .='</tr>';

        $html_Rett .= $html_R;
      }

      if($html_Rett<>""){
        $html_Rett = '<tr><td colspan=8 class="note" style="line-height:12px;">Rettifiche e costi aggiuntivi:</td></tr>'.$html_Rett;
      }

      $tot_tot = $tot_utente_R + $tot_utente;

      $html .= $html_ut.$html_Rett;
      $html .='<tr class="subtotale">';
        $html .='<td colspan=5 class="text-right">Totale di '.$rowU["fullname"].':</td>';
        $html .='<td class="text-right">'.rd4_nf($tot_utente,$decimals).' Eu.</td>';
        $html .='<td class="text-right">'.rd4_nf($tot_utente_R,$decimals).' Eu.</td>';
        $html .='<td class="text-right"><strong>'.rd4_nf($tot_tot,$decimals).'</strong> Eu.</td>';
      $html .='</tr>';


}//UTENTE



//-----------------------------------DATI
$html.='
            </tbody>
            <tfoot>
                <tr class="totale">
                    <th colspan=5 style="text-align:right"></th>
                    <th style="text-align:right"></th>
                    <th style="text-align:right"></th>
                    <th style="text-align:right"></th>
                </tr>
            </tfoot>
         </table>';
//-----------------------------------FOOTER
//{id:0,text:"Tutti"},
//                {id:1,text:"bug"},
//                {id:2,text:"duplicate"},
//                {id:3,text:"invalid"},
 //               {id:4,text:"wontfix"}

foreach($O->lista_gas_partecipanti() as $rowG){
    $gsel.='{id:'.$rowG["id_gas"].', text:"'.$rowG["descrizione_gas"].'"},';
}
$gsel = rtrim($gsel,",");


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
      //echo $html;die();
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
<div class="row">
        <div class="col-md-6">
            <div class="padding-10">
                <label>Seleziona quali gas vuoi visualizzare, lascia vuoto per visualizzarli tutti; Puoi selezionarne più di uno;</label>
                <div id="gas_select" data-init-text="Tutti"></div>
            </div>
        </div>
</div>
<button class="btn btn-success pull-right" id="aggiorna_report"  data-id_ordine="<?php echo $O->id_ordini; ?>">Aggiorna</button>
<div class="clearfix"></div>
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
    var gas_selection,g;

    var pagefunction = function(){

        gas_selection = {id:<?php echo $id_gas;?>};
        g = <?php echo $id_gas;?>;

        //------------HELP WIDGET
        document.title = '<?php echo "ReteDES.it :: $page_title";?>';
        <?php echo help_render_js($page_id);?>
        //------------END HELP WIDGET
        var gs = $('#gas_select');
        $(gs).select2({
            data:[
                <?php echo $gsel; ?>
            ],
            width: "100%"
        });
        $(gs).change(function() {
            console.log(gas_selection);
            gas_selection = $(gs).select2('data');
            console.log(gas_selection.id);
        });
        $('.show_pdf').click(function(){
            var $this = $(this);
            var id = $this.data('id_ordine');
            g = gas_selection.id;
            open('POST', 'http://retegas.altervista.org/gas4/ajax_rd4/reports/utenti_gas.php', {id:id, o:'pdf', dummy:<?php echo rand(1000,9999); ?>, g: g}, '_blank');
            return false;
        });
        $('#aggiorna_report').click(function(){
            var $this = $(this);
            var id = $this.data('id_ordine');
            g = gas_selection.id;

            console.log(g);
            location.replace('http://retegas.altervista.org/gas4/#ajax_rd4/reports/utenti_gas.php?id='+id+'&g='+g);
            return false;
        });

    } // end pagefunction

pagefunction();
</script>
