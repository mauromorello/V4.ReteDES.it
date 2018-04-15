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

//---INGOMBRO
$i = CAST_TO_STRING($_POST["i"]);
if ($i==""){
    $i = CAST_TO_STRING($_GET["i"]);
}

//---AMICI
$a = CAST_TO_STRING($_POST["a"]);
if ($a==""){
    $a = CAST_TO_STRING($_GET["a"]);
}

//---NO_PAGE_JUMP
$n = CAST_TO_STRING($_POST["n"]);
if ($n==""){
    $n = CAST_TO_STRING($_GET["n"]);
}
if($n=="no_page_jump"){
    $no_page_jump=true;    
}else{
    $no_page_jump=false;
}

//--- ORDINAMENTO
$s = CAST_TO_STRING($_POST["s"]);
if ($s==""){
    $s = CAST_TO_STRING($_GET["s"]);
    if ($s==""){
        $s="userid";
    }
}

if ($s=="userid"){
    $sort = " ORDER BY U.userid ASC ";
}
if ($s=="fullname"){
    $sort = " ORDER BY U.fullname ASC ";
}
if ($s=="tessera"){
    $sort = " ORDER BY U.tessera ASC ";
}
//--- ORDINAMENTO


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
$id_gas_js=$id_gas;


if($id_gas==-1){
    $show_only_gas = false;
    $lista_gas = $O->lista_gas_partecipanti();
        
}else{
    $show_only_gas = true;
    $lista_gas = array(array("id_gas"=>$id_gas));    
}



foreach($lista_gas as $rowGAS){
    
    $id_gas = $rowGAS["id_gas"];
    $G = new gas($id_gas);       
    //echo "ID GAS: ".$id_gas.'<hr>';

    $decimals = CAST_TO_INT($_POST["d"]);
    if ($decimals==0){
        $decimals = CAST_TO_INT($_GET["d"]);
    }
    if($decimals==0){$decimals=2;}

    $show_fullname=true;

    $stmt = $db->prepare("SELECT valore_int FROM retegas_options WHERE id_gas=:id_gas AND chiave='_GAS_REPORT_SHOW_ID' LIMIT 1;");
    $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();
    if($row["valore_int"]>0){
        $show_id=true;
    }else{
        $show_id=false;
    }

    $stmt = $db->prepare("SELECT valore_int FROM retegas_options WHERE id_gas=:id_gas AND chiave='_GAS_REPORT_SHOW_TEL' LIMIT 1;");
    $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();
    if($row["valore_int"]>0){
        $show_tel=true;
    }else{
        $show_tel=false;
    }

    $stmt = $db->prepare("SELECT valore_int FROM retegas_options WHERE id_gas=:id_gas AND chiave='_GAS_REPORT_SHOW_INDIRIZZO' LIMIT 1;");
    $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();
    if($row["valore_int"]>0){
        $show_indirizzo=true;
    }else{
        $show_indirizzo=false;
    }


    $stmt = $db->prepare("SELECT valore_int FROM retegas_options WHERE id_gas=:id_gas AND chiave='_GAS_REPORT_SHOW_TESSERA' LIMIT 1;");
    $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();
    if($row["valore_int"]>0){
        $show_tessera=true;
    }else{
        $show_tessera=false;
    }

    $stmt = $db->prepare("SELECT valore_int FROM retegas_options WHERE id_gas=:id_gas AND chiave='_GAS_REPORT_SHOW_CASSA' LIMIT 1;");
    $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();
    if($row["valore_int"]>0){
        $show_cassa=true;
    }else{
        $show_cassa=false;
    }

    //AMICI
    if($a=="show_amici"){
        $show_amici=true;    
    }else{
        $show_amici=false;
    }


    //INGOMBRO
    $stmt = $db->prepare("SELECT valore_text FROM retegas_options WHERE id_ordine=:id_ordine AND chiave='_RETTIFICA_INGOMBRO' AND id_gas=:id_gas LIMIT 1;");
    $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
    $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
    $stmt->execute();
    $rowING=$stmt->fetch();
    if(CAST_TO_STRING($rowING["valore_text"])=="SI"){
        $show_ingombro=true;
        if($i=="hide_ingombro"){
            $show_ingombro=false;    
        }
    }else{
        $show_ingombro=false;
        if($i=="show_ingombro"){
            $show_ingombro=true;    
        }    
    }
    
    

$html.= '<hr><p></p><h3>Ordine #'.$O->id_ordini.' - '.$O->descrizione_ordini.' - <b>'.$G->descrizione_gas.'</b></h3>
        <table id="table_exp_'.$id_gas.'" class="rd4" style="margin-left: auto; margin-right: auto">
            <thead >
                <tr class="intestazione" >
                    <th style="width:15%;">Utente</th>
                    <th style="width:15%;">Articolo</th>
                    <th >Descrizione</th>
                    <th style="width:10%;" class="text-right"></th>
                    <th style="width:10%;" class="text-right"></th>
                    <th style="width:10%;" class="text-right"></th>
                    <th style="width:5%;" class="text-right"></th>
                    <th style="width:5%;" class="text-right"></th>
                    <th style="width:10%;" class="text-right"></th>

                </tr>
            </thead>
            <tbody>';
//-----------------------------------DATI




$sql = "SELECT U.fullname, U.tel, U.userid, U.tessera, U.city, U.country
            FROM retegas_dettaglio_ordini D
            INNER JOIN maaking_users U ON U.userid = D.id_utenti
            WHERE D.id_ordine=:id_ordine
            AND U.id_gas=:id_gas
            GROUP BY U.userid
            $sort";
$stmt = $db->prepare($sql);
$stmt->bindParam(':id_ordine', $O->id_ordini, PDO::PARAM_INT);

$stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);


$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($rows AS $rowU){
      $html_Rett ="";

      $html_ut ='<tr>';
        $html_ut .='<td colspan=9 class="separator"></td>';
      $html_ut .='</tr>';

      if($show_tel){
        $show_telefono_report = '<span>('.$rowU["tel"].')</span>';
      }else{
        $show_telefono_report = '';
      }
      if($show_indirizzo){
        $show_indirizzo_report = '<br><span>'.$rowU["city"].' '.$rowU["country"].'</span>';
      }else{
        $show_indirizzo_report = '';
      }
      if($show_tessera){
        $show_tessera_report = '<span> '.$rowU["tessera"].' </span>';
      }else{
        $show_tessera_report = '';
      }
      if($show_id){
        $show_id_report = '<span> #'.$rowU["userid"].' </span>';
      }else{
        $show_id_report = '';
      }

      if($show_cassa){
        if(_USER_USA_CASSA){
            $show_cassa_report = '<span> in cassa: <strong>'.Round(VA_CASSA_SALDO_UTENTE_TOTALE($rowU["userid"]),2).' Eu.</strong></span>';
        }else{
            $show_cassa_report = '<span> NO CASSA </span>';
        }

      }else{
        $show_cassa_report = '';
      }


      $html_ut .='<tr class="utente">';
        $html_ut .='<td colspan=3>' .$show_id_report.' '
                                    .$show_tessera_report
                                    .' <strong>'.$rowU["fullname"].'</strong> '
                                    .$show_telefono_report
                                    .$show_cassa_report
                                    .$show_indirizzo_report
                                    .'</td>';
        $html_ut .='<td class="text-center">Quantità</td>';
        $html_ut .='<td class="text-right">Prezzo</td>';
        $html_ut .='<td class="text-right">Netto</td>';
        $html_ut .='<td class="text-right">Altro</td>';
        $html_ut .='<td class="text-right">GAS</td>';
        $html_ut .='<td class="text-right">Totale</td>';

      $html_ut .='</tr>';

      //ARTICOLI
      $sql = "SELECT art_codice, art_desc, art_um, qta_ord, qta_arr, prz_dett_arr, prz_dett, id_articoli, art_ingombro FROM retegas_dettaglio_ordini WHERE id_ordine=:id_ordine AND id_utenti=:id_utenti AND LEFT(art_codice , 2)<>'@@' AND LEFT(art_codice , 2)<>'##'";
      $stmt = $db->prepare($sql);
      $stmt->bindParam(':id_ordine', $O->id_ordini, PDO::PARAM_INT);
      $stmt->bindParam(':id_utenti', $rowU["userid"], PDO::PARAM_INT);
      $stmt->execute();
      $rowsA = $stmt->fetchAll(PDO::FETCH_ASSOC);

      $tot_utente = 0;
      $tot_utente_R = 0;
      $tot_utente_RG= 0;
      $art_count =0;

      foreach($rowsA AS $rowA){

          $art_count ++;
          $qta_arr = $rowA["qta_arr"];
          $prz_dett_arr = $rowA["prz_dett_arr"];
          $tot_riga = $rowA["prz_dett_arr"]*$rowA["qta_arr"];

          if($show_ingombro){
              //INGOMBRO
              $ingombro = CAST_TO_FLOAT($rowA["art_ingombro"]);
              if($ingombro>0){
                $tot_ingombro = ' {'._NF($ingombro*$rowA["qta_arr"]).'}';
              }else{
                $tot_ingombro ='';  
              }
          }else{
              $tot_ingombro ='';
          }
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

          if(($art_count % 2)==0){
              $class="even";
          }else{
              $class="odd";
          }

          $html_A ='<tr class="'.$class.'">';
              $html_A .='<td></td>';
              $html_A .='<td>'.$rowA["art_codice"].'</td>';
              $html_A .='<td>'.$rowA["art_desc"].' <span class="note">'.$rowA["art_um"].' '.$tot_ingombro.'</span></td>';
              $html_A .='<td class="text-center" '.$q_modificata.'>'._NF($qta_arr).$q_modificata_text_1.'</td>';
              $html_A .='<td class="text-right">'._NF($prz_dett_arr).'</td>';
              $html_A .='<td class="text-right">'._NF($tot_riga).'</td>';
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
            $stmtN->bindParam(':id_user', $rowU["userid"], PDO::PARAM_INT);
            $stmtN->execute();
            $rowN = $stmtN->fetch();
            if(trim(CAST_TO_STRING($rowN["valore_text"]))<>""){
                $html_A .='<tr>';
                $html_A .='<td>&nbsp;</td>';
                $html_A .='<td colspan=8>'.$rowN["valore_text"].'</td>';
                $html_A .='</tr>';
            }
            //NOTA ARTICOLO

            
            
            
            $html_ut .= $html_A;

      }//LOOP ARTICOLO

      if($show_amici){
                $html_ut .='<tr>';
                    $html_ut .='<td>&nbsp;</td>';
                    $html_ut .='<td colspan=8>AMICI DI '.$rowU["fullname"].'</td>';
                $html_ut .='</tr>';    
      }


      //RETTIFICHE ORDINE
      $sql = "SELECT art_codice, art_desc, art_um, qta_ord, qta_arr, prz_dett_arr, prz_dett FROM retegas_dettaglio_ordini WHERE id_ordine=:id_ordine AND id_utenti=:id_utenti AND LEFT(art_codice , 2)='@@'";
      $stmt = $db->prepare($sql);
      $stmt->bindParam(':id_ordine', $O->id_ordini, PDO::PARAM_INT);
      $stmt->bindParam(':id_utenti', $rowU["userid"], PDO::PARAM_INT);
      $stmt->execute();
      $rowsR = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $html_R = "";
      $html_Rett ="";
      foreach($rowsR AS $rowR){
          $qta_arr_R = $rowR["qta_arr"];
          $prz_dett_arr_R = $rowR["prz_dett_arr"];
          $tot_riga_R = $rowR["prz_dett_arr"]*$rowR["qta_arr"];
          $tot_utente_R = $tot_utente_R + $tot_riga_R;
          $html_R ='<tr class="even">';
            $html_R .='<td></td>';
            $html_R .='<td>'.$rowR["art_codice"].'</td>';
            $html_R .='<td colspan=4>'.$rowR["art_desc"].'</td>';
            $html_R .='<td class="text-right">'._NF($tot_riga_R).'</td>';
            $html_R .='<td></td>';
            $html_R .='<td></td>';
            $html_R .='</tr>';
            $html_Rett .= $html_R;
      }
      if($html_Rett<>""){
        $html_Rett = '<tr><td colspan=9 class="note" style="line-height:12px;">Rettifiche e costi aggiuntivi:</td></tr>'.$html_Rett;
      }

      //RETTIFICHE GAS
      $sql = "SELECT art_codice, art_desc, art_um, qta_ord, qta_arr, prz_dett_arr, prz_dett FROM retegas_dettaglio_ordini WHERE id_ordine=:id_ordine AND id_utenti=:id_utenti AND LEFT(art_codice , 2)='##'";
      $stmt = $db->prepare($sql);
      $stmt->bindParam(':id_ordine', $O->id_ordini, PDO::PARAM_INT);
      $stmt->bindParam(':id_utenti', $rowU["userid"], PDO::PARAM_INT);
      $stmt->execute();
      $rowsRG = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $html_RG = "";
      $tot_utente_RG=0;
      $html_Rett_Gas ="";
      foreach($rowsRG AS $rowRG){
      $qta_arr_RG = $rowRG["qta_arr"];
      $prz_dett_arr_RG = $rowRG["prz_dett_arr"];
      $tot_riga_RG = $rowRG["prz_dett_arr"]*$rowRG["qta_arr"];
      $tot_utente_RG = $tot_utente_RG + $tot_riga_RG;
      $html_RG ='<tr class="even">';
        $html_RG .='<td></td>';
        $html_RG .='<td>'.$rowRG["art_codice"].'</td>';
        $html_RG .='<td colspan=4>'.$rowRG["art_desc"].'</td>';
        $html_RG .='<td></td>';
        $html_RG .='<td class="text-right">'._NF($tot_riga_RG).'</td>';
        $html_RG .='<td></td>';
        $html_RG .='</tr>';
        $html_Rett_Gas .= $html_RG;
      }
      if($html_Rett_Gas<>""){
        $html_Rett_Gas = '<tr><td colspan=9 class="note" style="line-height:12px;">Rettifiche del tuo GAS:</td></tr>'.$html_Rett_Gas;
      }

      $tot_tot = $tot_utente_R + $tot_utente_RG + $tot_utente;

      $html .= $html_ut.$html_Rett.$html_Rett_Gas;
      $html .='<tr class="subtotale">';
        $html .='<td colspan=5 class="text-right">Totale di '.$rowU["fullname"].':</td>';
        $html .='<td class="text-right">'._NF($tot_utente).'</td>';
        $html .='<td class="text-right">'._NF($tot_utente_R).'</td>';
        $html .='<td class="text-right">'._NF($tot_utente_RG).'</td>';
        $html .='<td class="text-right"><strong>'._NF($tot_tot).'</strong></td>';
      $html .='</tr>';
      

}//UTENTE



//-----------------------------------DATI

$html.='
            </tbody>
            <tfoot>
                <tr class="totale">
                    <th colspan=5 style="text-align:right">TOTALI:</th>
                    <th style="text-align:right">'._NF(VA_ORDINE_GAS_SOLO_NETTO($O->id_ordini,$id_gas)).'</th>
                    <th style="text-align:right">'._NF(VA_ORDINE_GAS_SOLO_RETT($O->id_ordini,$id_gas)).'</th>
                    <th style="text-align:right">'._NF(VA_ORDINE_GAS_SOLO_EXTRA_GAS($O->id_ordini,$id_gas)).'</th>
                    <th style="text-align:right">'._NF(VA_ORDINE_GAS($O->id_ordini,$id_gas)).'</th>
                </tr>
            </tfoot>
         </table>';
         
         
         if(!$no_page_jump){
            $html.='<!--SALTO PAGINA--><div style="page-break-after: always;"></div>';
         }

} //LOOP GAS         
//-----------------------------------FOOTER
function str_lreplace($search, $replace, $subject){
    $pos = strrpos($subject, $search);
    if($pos !== false){
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }
    return $subject;
}
$html = str_lreplace('<!--SALTO PAGINA--><div style="page-break-after: always;"></div>', '', $html);



$gsel.='{id:-1, text:"TUTTI I GAS"},';
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
  <body>'.pdf_testata($O,$orientation).$html." 
  
  ".'<script type="text/php">
     if (isset($pdf)) {
        $x = $pdf->get_width()-80;
        $y = $pdf->get_height()-35;
        $pdf->page_text($x, $y, "Pagina {PAGE_NUM} di {PAGE_COUNT}", null, 10);'."
      }
      </script> 
      </body></html>";
      
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
<div class="well well-sm hidden margin-top-10" id="opzioni_report">
<div class="row">
        <form class="smart-form">
        <div class="col-md-6">
            <div class="padding-10">
                <section>
                    <label>Seleziona quale gas vuoi visualizzare, lascia vuoto per visualizzare solo il tuo;</label>
                    <div id="gas_select"></div>
                </section>
            </div>
        </div>
        <div class="col-md-6">
            <div class="padding-10">
                <section>
                    <label class="label">Ordinamento:</label>
                    <div class="inline-group">
                        <label class="radio">
                            <input type="radio" name="radio-inline" <?php if($s=="userid"){echo'checked="checked"';} ?> value="userid">
                            <i></i>ID utente</label>
                        <label class="radio">
                            <input type="radio" name="radio-inline" <?php if($s=="fullname"){echo'checked="checked"';} ?> value="fullname">
                            <i></i>Nome</label>
                        <label class="radio">
                            <input type="radio" name="radio-inline" <?php if($s=="tessera"){echo'checked="checked"';} ?> value="tessera">
                            <i></i>Tessera</label>
                    </div>
                </section>
                <!--
                <section>
                    <div class="inline-group">
                        <label class="checkbox">
                            <input type="checkbox" name="show_amici" <?php if($show_amici){echo'checked="checked"';} ?> value="show_amici">
                            <i></i>Mostra anche gli amici dell'utente</label>
                    </div>
                </section>
                -->
                <section>
                    <div class="inline-group">
                        <label class="checkbox">
                            <input type="checkbox" name="show_ingombro" <?php if($show_ingombro){echo'checked="checked"';} ?> value="show_ingombro">
                            <i></i>Mostra il campo "ingombro" se presente</label>
                    </div>
                </section>
                <section>
                    <div class="inline-group">
                        <label class="checkbox">
                            <input type="checkbox" name="no_page_jump" <?php if($no_page_jump){echo'checked="checked"';} ?> value="no_page_jump">
                            <i></i>Non saltare la pagina al cambio di GAS (in caso di multiGAS)</label>
                    </div>
                </section>
            </div>
        </div>
        </form>
</div>
<button class="btn btn-success pull-right" id="aggiorna_report"  data-id_ordine="<?php echo $O->id_ordini; ?>">Aggiorna</button>
<div class="clearfix"></div>
</div>
<div class="container_report margin-top-10" style="overflow-x:auto;width:100%; height:400px; overflow-y:auto;">
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

        gas_selection = {id:<?php echo $id_gas_js;?>};
        g = <?php echo $id_gas_js;?>;

        //------------HELP WIDGET
        document.title = '<?php echo "ReteDES.it :: $page_title";?>';
        <?php echo help_render_js($page_id);?>
        //------------END HELP WIDGET
        var gs = $('#gas_select');
        $(gs).select2({
            //multiple: true,
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
            //g = gas_selection.id;
            //g = $(gs).select2('data');
            g=<?php echo $id_gas_js; ?>;
            console.log("GAS: " + g);
            s = $('input[name="radio-inline"]:checked').val();
            i = $('input[name="show_ingombro"]:checked').val() || "hide_ingombro";
            a = $('input[name="show_amici"]:checked').val() || "hide_amici";
            n = $('input[name="no_page_jump"]:checked').val() || "yes_page_jump";
            open('POST', '<?php echo APP_URL; ?>/ajax_rd4/reports/utenti_gas.php', {id:id, o:'pdf', dummy:<?php echo rand(1000,9999); ?>, g: g, s: '<?php echo $s ?>', a:a,i:i,n:n}, '_blank');
            return false;
        });
        $('#aggiorna_report').click(function(){
            var $this = $(this);
            var id = $this.data('id_ordine');
            g = gas_selection.id;
            s = $('input[name="radio-inline"]:checked').val();
            i = $('input[name="show_ingombro"]:checked').val() || "hide_ingombro";
            a = $('input[name="show_amici"]:checked').val() || "hide_amici";
            n = $('input[name="no_page_jump"]:checked').val() || "yes_page_jump";
            console.log(g);
            location.replace('<?php echo APP_URL; ?>/#ajax_rd4/reports/utenti_gas.php?id='+id+'&g='+g+'&s='+s+'&i='+i+'&a='+a+'&n='+n);
            return false;
        });

    } // end pagefunction

pagefunction();
</script>
