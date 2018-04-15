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


//--- ORDINAMENTO
$s = CAST_TO_STRING($_POST["s"]);
if ($s==""){
    $s = CAST_TO_STRING($_GET["s"]);
    if ($s==""){
        $s="userid";
    }
}

if ($s=="userid"){
    $order_amico = " ORDER BY id_amici ASC ";
}
if ($s=="fullname"){
    $order_amico = " ORDER BY nome ASC ";
}

//--- ORDINAMENTO

//RETTIFICHE
$re = CAST_TO_STRING($_POST["re"]);
if ($re==""){
    $re = CAST_TO_STRING($_GET["re"]);
    if ($re==""){
        $re="mestesso";
    }
}


$page_title = "Report - distribuzione amici";
$page_id = "report_distribuzione_amici";
$orientation = "landscape"; // portrait / landscape
//------------------------------PAGE





//-----------------------------------CONTENT
if(($_POST["a"])=="all"){
    $id_amico = -1;
}
if(($_POST["a"])=="0"){
    $id_amico = 0;
}
if(CAST_TO_INT($_POST["a"],0)>0){
    $id_amico = CAST_TO_INT($_POST["a"],0);
}
if(($_GET["a"])=="all"){
    $id_amico = -1;
}
if(($_GET["a"])=="0"){
    $id_amico = 0;
}
if(CAST_TO_INT($_GET["a"],0)>0){
    $id_amico = CAST_TO_INT($_GET["a"],0);
}else{
    $id_amico = -1;   
}


if($id_amico<0){
    $titolo_amico="Tutti gli amici";
}
if($id_amico==0){
    $titolo_amico="Solo articoli miei";
    $where_amico=" AND D.id_amico='".$id_amico."' ";
}
if($id_amico>0){
     $sql = "SELECT nome from retegas_amici WHERE id_amici=:id_amici";
       $stmt = $db->prepare($sql);
       $stmt->bindParam(':id_amici', $id_amico , PDO::PARAM_INT);
       $stmt->execute();
       $amico = $stmt->fetch();
    $titolo_amico="Articoli di ".$amico[0];
    $where_amico=" AND D.id_amico='".$id_amico."' ";
}

$decimals = CAST_TO_INT($_POST["d"]);
if ($decimals==0){
    $decimals = CAST_TO_INT($_GET["d"]);
}
if($decimals==0){$decimals=2;}





$html= '<h3>Ordine #'.$O->id_ordini.' - '.$O->descrizione_ordini.'</h3>';
$html.= '<h3>'.$titolo_amico.'</h3>
        <table id="table_exp" class="rd4" style="margin-left: auto; margin-right: auto">
            <thead >
                <tr class="intestazione" >
                    <th style="width:15%;" class="text-left">Amico</th>
                    <th style="width:15%;" class="text-left">Articolo</th>
                    <th class="text-left">Descrizione</th>
                    <th style="width:10%;" class="text-right"></th>
                    <th style="width:10%;" class="text-right"></th>
                    <th style="width:10%;" class="text-right"></th>
                    <th style="width:5%;"  class="text-right"></th>
                    <th style="width:5%;"  class="text-right"></th>
                    <th style="width:10%;" class="text-right"></th>
                </tr>
            </thead>
            <tbody>';
//-----------------------------------DATI

//CONTEGGIO AMICI
$n_amici = count($O->lista_amici_partecipanti(_USER_ID));



$sql = "SELECT IFNULL(A.nome,'Me stesso') as nome , IFNULL(A.telefono,'') as telefono , IFNULL(A.id_amici, 0) as id_amici, id_user
            FROM retegas_distribuzione_spesa D
            LEFT JOIN retegas_amici A ON A.id_amici = D.id_amico
            WHERE D.id_ordine=:id_ordine
            AND D.id_user='"._USER_ID."'
            ".$where_amico."
            GROUP BY A.id_amici
            ".$order_amico.";";
$stmt = $db->prepare($sql);
$stmt->bindParam(':id_ordine', $O->id_ordini, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($rows AS $rowU){
      $html_Rett ="";

      $html_ut ='<tr>';
        $html_ut .='<td colspan=9 class="separator"></td>';
      $html_ut .='</tr>';

      $html_ut .='<tr class="utente">';
        $html_ut .='<td colspan=3><strong>'.$rowU["nome"].'</strong> <span>('.$rowU["telefono"].')</span></td>';
        $html_ut .='<td class="text-center">Quantità</td>';
        $html_ut .='<td class="text-right">Prezzo</td>';
        $html_ut .='<td class="text-right">Netto</td>';
        $html_ut .='<td class="text-right">Altro</td>';
        $html_ut .='<td class="text-right">GAS</td>';
        $html_ut .='<td class="text-right">Totale</td>';

      $html_ut .='</tr>';

      //ARTICOLI
      $sql = "SELECT D.prz_dett_arr, D.prz_dett,D.art_codice, D.art_desc, D.art_um, A.qta_ord, A.qta_arr, D.id_articoli
                FROM
                retegas_distribuzione_spesa A
                INNER JOIN
                retegas_dettaglio_ordini D ON D.id_dettaglio_ordini=A.id_riga_dettaglio_ordine
                WHERE D.id_ordine=:id_ordine
                AND D.id_utenti='"._USER_ID."'
                AND A.id_amico=:id_amico
                AND LEFT(D.art_codice , 2)<>'@@'
                AND LEFT(D.art_codice , 2)<>'##'";
      $stmt = $db->prepare($sql);
      $stmt->bindParam(':id_ordine', $O->id_ordini, PDO::PARAM_INT);
      $stmt->bindParam(':id_amico', $rowU["id_amici"], PDO::PARAM_INT);
      $stmt->execute();
      $rowsA = $stmt->fetchAll(PDO::FETCH_ASSOC);

      $tot_utente = 0;
      $tot_utente_R = 0;
      $tot_utente_RG =0;
      
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
        $html_A .='<td class="text-right">'.rd4_nf($prz_dett_arr,$decimals).'</td>';
        $html_A .='<td class="text-right">'.rd4_nf($tot_riga,$decimals).'</td>';
        $html_A .='<td></td>';
        $html_A .='<td></td>';
        $html_A .='<td class="text-right">'.$q_modificata_text_2.'</td>';

        $html_A .='</tr>';

        //NOTA ARTICOLO
        $sql = "SELECT * FROM retegas_options
                WHERE
                id_articolo=:id_articolo AND
                id_user=:id_user AND
                chiave='_NOTE_DETTAGLIO' AND
                id_ordine=:id_ordine";
        $stmtN = $db->prepare($sql);
        $stmtN->bindParam(':id_articolo', $rowA["id_articoli"], PDO::PARAM_INT);
        $stmtN->bindParam(':id_ordine', $O->id_ordini, PDO::PARAM_INT);
        $stmtN->bindParam(':id_user', $rowU["id_user"], PDO::PARAM_INT);
        $stmtN->execute();
        $rowN = $stmtN->fetch();
        if(trim(CAST_TO_STRING($rowN["valore_text"]))<>""){
            $html_A .='<tr>';
            $html_A .='<td>&nbsp;</td>';
            $html_A .='<td colspan=9>'.$rowN["valore_text"].'</td>';
            $html_A .='</tr>';
        }
        //NOTA ARTICOLO

        $html_ut .= $html_A;

      }//LOOP ARTICOLO

      $html_R="";
      $html_Rett ="";
      $html_Rett_Gas="";
      
      //RETTIFICHE NUOVE
      if($re=='mestesso'){
          //SE SONO IO
          if($rowU["nome"]=="Me stesso"){
            $tot_utente_R = VA_ORDINE_USER_SOLO_RETTIFICHE($id_ordine,_USER_ID);   
            $tot_utente_RG = VA_ORDINE_USER_SOLO_EXTRA_GAS($id_ordine,_USER_ID);
          
            if($tot_utente_R>0){
                $html_R ='<tr class="even">';
                $html_R .='<td></td>';
                $html_R .='<td></td>';
                $html_R .='<td colspan=4>Riferite all\'ordine</td>';
                $html_R .='<td class="text-right">'._NF($tot_utente_R).'</td>';
                $html_R .='<td></td>';
                $html_R .='<td></td>';
                $html_R .='</tr>';
                $html_Rett = $html_R;
                if($html_Rett<>""){
                    $html_Rett = '<tr><td colspan=9 class="note" style="line-height:12px;">Rettifiche e costi aggiuntivi</td></tr>'.$html_Rett;
                }
            }
            
            if($tot_utente_RG>0){
                $html_R ='<tr class="even">';
                $html_R .='<td></td>';
                $html_R .='<td></td>';
                $html_R .='<td colspan=4>Riferite al tuo GAS</td>';
                $html_R .='<td></td>';
                $html_R .='<td class="text-right">'._NF($tot_utente_RG).'</td>';
                $html_R .='<td></td>';
                $html_R .='</tr>';
                $html_Rett_Gas = $html_R;
                if($html_Rett_Gas<>""){
                    $html_Rett_Gas = '<tr><td colspan=9 class="note" style="line-height:12px;">Rettifiche e costi aggiuntivi:</td></tr>'.$html_Rett_Gas;
                }
            }
          }    
      }

      if($re=='propo'){
            
            $tot_ordine_utente = VA_ORDINE_USER_SOLO_NETTO($id_ordine, _USER_ID);
            $tot_amico_spesa = $tot_utente;     
          
            $percentuale = $tot_utente / $tot_ordine_utente;
            
            $tot_utente_R = VA_ORDINE_USER_SOLO_RETTIFICHE($id_ordine,_USER_ID)*$percentuale;   
            $tot_utente_RG = VA_ORDINE_USER_SOLO_EXTRA_GAS($id_ordine,_USER_ID)*$percentuale;
            
            if($tot_utente_R>0){
                $html_R ='<tr class="even">';
                $html_R .='<td></td>';
                $html_R .='<td></td>';
                $html_R .='<td colspan=4>Riferite all\'ordine</td>';
                $html_R .='<td class="text-right">'._NF($tot_utente_R).'</td>';
                $html_R .='<td></td>';
                $html_R .='<td></td>';
                $html_R .='</tr>';
                $html_Rett = $html_R;
                if($html_Rett<>""){
                    $html_Rett = '<tr><td colspan=9 class="note" style="line-height:12px;">Rettifiche e costi aggiuntivi</td></tr>'.$html_Rett;
                }
            }
            
            if($tot_utente_RG>0){
                $html_R ='<tr class="even">';
                $html_R .='<td></td>';
                $html_R .='<td></td>';
                $html_R .='<td colspan=4>Riferite al tuo GAS</td>';
                $html_R .='<td></td>';
                $html_R .='<td class="text-right">'._NF($tot_utente_RG).'</td>';
                $html_R .='<td></td>';
                $html_R .='</tr>';
                $html_Rett_Gas = $html_R;
                if($html_Rett_Gas<>""){
                    $html_Rett_Gas = '<tr><td colspan=9 class="note" style="line-height:12px;">Rettifiche e costi aggiuntivi:</td></tr>'.$html_Rett_Gas;
                }
            }
            
            
          
          
      }

      if($re=='equamente'){
            $tot_utente_R = VA_ORDINE_USER_SOLO_RETTIFICHE($id_ordine,_USER_ID)/$n_amici;   
            $tot_utente_RG = VA_ORDINE_USER_SOLO_EXTRA_GAS($id_ordine,_USER_ID)/$n_amici;    
          
            if($tot_utente_R>0){
                $html_R ='<tr class="even">';
                $html_R .='<td></td>';
                $html_R .='<td></td>';
                $html_R .='<td colspan=4>Rettifiche ordine</td>';
                $html_R .='<td class="text-right">'._NF($tot_utente_R).'</td>';
                $html_R .='<td></td>';
                $html_R .='<td></td>';
                $html_R .='</tr>';
                $html_Rett = $html_R;
                if($html_Rett<>""){
                    $html_Rett = '<tr><td colspan=9 class="note" style="line-height:12px;">Rettifiche e costi aggiuntivi</td></tr>'.$html_Rett;
                }
                
            }
            
            if($tot_utente_RG>0){
                $html_R ='<tr class="even">';
                $html_R .='<td></td>';
                $html_R .='<td></td>';
                $html_R .='<td colspan=4>Rettifiche GAS</td>';
                $html_R .='<td></td>';
                $html_R .='<td class="text-right">'._NF($tot_utente_RG).'</td>';
                $html_R .='<td></td>';
                $html_R .='</tr>';
                $html_Rett_Gas = $html_R;
                if($html_Rett_Gas<>""){
                    $html_Rett_Gas = '<tr><td colspan=9 class="note" style="line-height:12px;">Rettifiche e costi aggiuntivi:</td></tr>'.$html_Rett_Gas;
                }
            }
          
      }
      
      

      $tot_tot = $tot_utente_R + $tot_utente_RG + $tot_utente;

      $html .= $html_ut.$html_Rett.$html_Rett_Gas;
      $html .='<tr class="subtotale">';
        $html .='<td colspan=5 class="text-right">Totale di '.$rowU["nome"].':</td>';
        $html .='<td class="text-right">'._NF($tot_utente).'</td>';
        $html .='<td class="text-right">'._NF($tot_utente_R).'</td>';
        $html .='<td class="text-right">'._NF($tot_utente_RG).'</td>';
        $html .='<td class="text-right"><strong>'._NF($tot_tot).'</strong></td>';
      $html .='</tr>';

      $super_totale += $tot_tot;
      $super_totale_R += $tot_utente_R;
      $super_totale_RG += $tot_utente_RG;
}//AMICO



//-----------------------------------DATI
$html.='
            </tbody>
            <tfoot>
                <tr class="totale">
                    <th colspan=6 style="text-align:right">Totale</th>
                    <th style="text-align:right">'._nf($super_totale_R).'</th>
                    <th style="text-align:right">'._nf($super_totale_RG).'</th>
                    <th style="text-align:right">'._nf($super_totale).'</th>
                </tr>
            </tfoot>
         </table>';
//-----------------------------------FOOTER
//{id:0,text:"Tutti"},
//                {id:1,text:"bug"},
//                {id:2,text:"duplicate"},
//                {id:3,text:"invalid"},
 //               {id:4,text:"wontfix"}

$asel ='{id:"all", text:"Tutti"},';
foreach($O->lista_amici_partecipanti(_USER_ID) as $rowA){
    $asel.='{id:'.$rowA["id_amico"].', text:"'.$rowA["nome"].'"},';
}
$asel = rtrim($asel,",");


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
        <form class="smart-form">
            <div class="col-md-6">
                <div class="padding-10">
                    <label>Seleziona quale amico vuoi visualizzare, lascia vuoto per visualizzarli tutti;</label>
                    <div id="amici_select" data-init-text="Tutti"></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="padding-10">
                    <section>
                        <label class="label">Ordinamento amici:</label>
                        <div class="inline-group">
                            <label class="radio">
                                <input type="radio" name="radio-inline" <?php if($s=="userid"){echo'checked="checked"';} ?> value="userid">
                                <i></i>ID amico</label>
                            <label class="radio">
                                <input type="radio" name="radio-inline" <?php if($s=="fullname"){echo'checked="checked"';} ?> value="fullname">
                                <i></i>Nome</label>
                        </div>
                    </section>
                </div>
            </div>
            <div class="margin-top-10">
                <div class="col-md-12">
                    <div class="padding-10">
                        <section>
                            <label class="label">Gestione rettifiche:</label>
                            <div class="inline-group">
                                <label class="radio">
                                    <input type="radio" name="radio-rett" <?php if($re=="mestesso"){echo'checked="checked"';} ?> value="mestesso">
                                    <i></i>Assegnale a me stesso (standard)</label>
                                <label class="radio">
                                    <input type="radio" name="radio-rett" <?php if($re=="propo"){echo'checked="checked"';} ?> value="propo">
                                    <i></i>Proporzionale alla spesa</label>
                                   
                                <label class="radio">
                                    <input type="radio" name="radio-rett" <?php if($re=="equamente"){echo'checked="checked"';} ?> value="equamente">
                                    <i></i>Dividile equamente</label>
      
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </form>
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
    var amico_selection,a,re,s;

    var pagefunction = function(){

        amico_selection = {id:<?php if($id_amico==-1){echo '"all"';}else{echo $id_amico;};?>};
        a = <?php  if($id_amico==-1){echo '"all"';}else{echo $id_amico;}?>;

        //------------HELP WIDGET
        document.title = '<?php echo "ReteDES.it :: $page_title";?>';
        <?php echo help_render_js($page_id);?>
        //------------END HELP WIDGET
        var as = $('#amici_select');
        $(as).select2({
            //multiple: true,
            data:[
                <?php echo $asel; ?>
            ],
            width: "100%"
        });
        $(as).change(function() {
            console.log("Prima: " + amico_selection);
            amico_selection = $(as).select2('data');
            console.log("Dopo: "+amico_selection.id);
        });
        $('.show_pdf').click(function(){
            var $this = $(this);
            var id = $this.data('id_ordine');
            a = amico_selection.id;
            s = $('input[name="radio-inline"]:checked').val();
            re = $('input[name="radio-rett"]:checked').val();
            
            open('POST', '<?php echo APP_URL; ?>/ajax_rd4/reports/dettaglio_distribuzione.php', {id:id, o:'pdf', dummy:<?php echo rand(1000,9999); ?>, a: a, re: re, s:s}, '_blank');
            return false;
        });
        $('#aggiorna_report').click(function(){
            var $this = $(this);
            var id = $this.data('id_ordine');
            a = amico_selection.id;
            s = $('input[name="radio-inline"]:checked').val();
            re = $('input[name="radio-rett"]:checked').val();
            console.log('S='+s);
            location.replace('<?php echo APP_URL; ?>/#ajax_rd4/reports/dettaglio_distribuzione.php?id='+id+'&a='+a+'&s='+s+'&re='+re);
            return false;
        });

    } // end pagefunction

pagefunction();
</script>
