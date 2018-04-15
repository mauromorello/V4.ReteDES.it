<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.cassa.php");
$ui = new SmartUI;



//-------------------------------PAGE
$page_title = "Report - riepilogo mia cassa";
$page_id = "report_riepilogo_mia_cassa";
$orientation = "landscape"; // portrait / landscape
//------------------------------PAGE

//------------------------------SEND MAIL
$send_mail=CAST_TO_INT($_GET["send_mail"],0);
//------------------------------SEND MAIL


//SORTING-------------------------
$s=CAST_TO_STRING($_GET["s"]);
if($s==''){
    $s=CAST_TO_STRING($_POST["s"]);    
}
if($s==''){$s='data';}

//-------------------------SORTING

//-------------------------OPTIONS
$nascondi_dettagli=CAST_TO_INT($_GET["nascondi_dettagli"],0);
if($nascondi_dettagli==0){
    $nascondi_dettagli=CAST_TO_INT($_POST["nascondi_dettagli"],0);    
}
$nascondi_totali=CAST_TO_INT($_GET["nascondi_totali"],0);
if($nascondi_totali==0){
    $nascondi_totali=CAST_TO_INT($_POST["nascondi_totali"],0);    
}
$nascondi_intestazioni=CAST_TO_INT($_GET["nascondi_intestazioni"],0);
if($nascondi_intestazioni==0){
    $nascondi_intestazioni=CAST_TO_INT($_POST["nascondi_intestazioni"],0);    
}
$nascondi_crediti=CAST_TO_INT($_GET["nascondi_crediti"],0);
if($nascondi_crediti==0){
    $nascondi_crediti=CAST_TO_INT($_POST["nascondi_crediti"],0);    
}
$nascondi_debiti=CAST_TO_INT($_GET["nascondi_debiti"],0);
if($nascondi_debiti==0){
    $nascondi_debiti=CAST_TO_INT($_POST["nascondi_debiti"],0);    
}
$nascondi_pregressi=CAST_TO_INT($_GET["nascondi_pregressi"],0);
if($nascondi_pregressi==0){
    $nascondi_pregressi=CAST_TO_INT($_POST["nascondi_pregressi"],0);    
}

//-------------------------OPTIONS

//FILTER----------------------------
$id_ordine=CAST_TO_INT($_GET["id_ordine"],0);
if($id_ordine==0){
    $id_ordine=CAST_TO_INT($_POST["id_ordine"],0);    
}
$id_ditta=CAST_TO_INT($_GET["id_ditta"],0);
if($id_ditta==0){
    $id_ditta=CAST_TO_INT($_POST["id_ditta"],0);    
}

    $id_utente=_USER_ID;    

$tipo_movimento=CAST_TO_INT($_GET["tipo_movimento"],0);
if($tipo_movimento==0){
    $tipo_movimento=CAST_TO_INT($_POST["tipo_movimento"],0);    
}
$data_da=CAST_TO_STRING($_GET["data_da"]);
if($data_da==''){
    $data_da=CAST_TO_STRING($_POST["data_da"]);    
}
$data_a=CAST_TO_STRING($_GET["data_a"]);
if($data_a==''){
    $data_a=CAST_TO_STRING($_POST["data_a"]);    
}
$nascondi_registrati=CAST_TO_INT($_GET["nascondi_registrati"],0);
if($nascondi_registrati==''){
    $nascondi_registrati=CAST_TO_INT($_POST["nascondi_registrati"],0);    
}
$nascondi_non_registrati=CAST_TO_INT($_GET["nascondi_non_registrati"],0);
if($nascondi_non_registrati==''){
    $nascondi_non_registrati=CAST_TO_INT($_POST["nascondi_non_registrati"],0);    
}
//---------------------------FILTER







$C = new cassa(_USER_ID_GAS);

$fw='';

//----------------FILTER
$w = "WHERE U.id_gas = "._USER_ID_GAS." ";
if($id_ordine>0){
    $w.=" AND id_ordine='$id_ordine' ";
    $fw .=' Solo ordine '.$id_ordine.'; ';
}
if($id_ditta>0){
    $w.=" AND id_ditta='$id_ditta' ";
    $fw .=' Solo ditta '.$id_ditta.'; ';
}

    $w.=" AND U.userid='"._USER_ID."' ";
    $fw .=' Solo utente '.$id_utente.'; ';

if($tipo_movimento>0){
    $w.=" AND tipo_movimento='$tipo_movimento' ";
    $fw .=' Solo movimenti tipo '.$tipo_movimento.'; ';
}
if($data_da<>''){
    $w.=" AND data_movimento>'".conv_date_to_db($data_da)."' ";
    $fw .=' Movimenti inseriti dopo il  '.$data_da.'; ';
}
if($data_a<>''){
    $w.=" AND data_movimento<'".conv_date_to_db($data_a)."' ";
    $fw .=' Movimenti inseriti prima del '.$data_a.'; ';
}

if($nascondi_registrati>0){
    $w.=" AND registrato<>'si' ";
    $fw .=' Non sono considerati i movimenti ancora da registrare; ';    
}
if($nascondi_non_registrati>0){
    $w.=" AND registrato<>'no' ";
    $fw .=' Non sono considerati i movimenti già registrati; ';    
}
if($nascondi_crediti>0){
    $w.=" AND segno<>'+' ";
    $fw .=' Non sono considerati i movimenti di CREDITO; ';    
}
if($nascondi_debiti>0){
    $w.=" AND segno<>'-' ";
    $fw .=' Non sono considerati i movimenti di DEBITO; ';    
}

//-------------------------------------------FILTER


//-------------------------------------------SORTING
if($s=="data"){
    $sort = ", R.data_movimento DESC ";
}
if($s=="tipo"){
    $sort = ", tipo_movimento DESC, R.data_movimento DESC ";
}
if($s=="importo_ASC"){
    $sort = ", importo ASC ";
}
if($s=="importo_DESC"){
    $sort = ", importo DESC ";
}

//-------------------------------------------SORTING

$html='<h3>Cassa</h3><p><strong>Filtro attivo: </strong>'.$fw.'</p>';
//-----------------------------------CONTENT
$html.='<table id="table_exp" class="rd4" style="margin-left: auto; margin-right: auto">
            <thead >
                <tr class="intestazione" >
                    <th style="width:14%;">Data</th>
                    <th style="width:12%;">Cassiere</th>
                    <th style="">Descrizione</th>
                    <th style="width:28%;">Movimento</th>
                    <th style="width:7%;" class="text-right">Credito</th>
                    <th style="width:7%;" class="text-right">Debito</th>
                    <th style="width:7%;" class="text-right">Saldo</th>
                </tr>
            </thead>
            <tbody>';
           
//-----------------------------------DATI



$sql = "SELECT R.id_cassa_utenti AS id, U.fullname, U.userid, U2.fullname as cassiere, U.email, DATE_FORMAT(R.data_movimento,'%d/%m/%Y %h:%i') as data, tipo_movimento as tipo, segno, importo, id_ordine, id_ditta, D.descrizione_ditte, U2.fullname as cassiere, DATE_FORMAT(R.data_registrato,'%d/%m/%y %h:%i') as data_registrato, registrato, descrizione_movimento
        FROM retegas_cassa_utenti R
        INNER JOIN maaking_users U on U.userid=R.id_utente
        LEFT JOIN retegas_ditte D on D.id_ditte=R.id_ditta
        LEFT JOIN maaking_users U2 on U2.userid=R.id_cassiere
        $w
        ORDER BY U.userid $sort";
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

    
    
    $new_id=$row["userid"];
    
    if($old_id<>$new_id){
        if($ucount>0){
            if($nascondi_totali==0){    
                $show++;
                $html.='<tr class="subtotale">
                            <th colspan=4 style="text-align:left">Totale utente #'.$old_id.' '.$old_fullname.':</th>
                            <th style="text-align:right">'._NF($totale_credito_utente).'</th>
                            <th style="text-align:right">'._NF($totale_debito_utente).'</th>
                            <th style="text-align:right">'._NF($totale_credito_utente-$totale_debito_utente).'</th>
                        </tr>';
                $html.='<tr class="">
                            <th colspan=7 style=""></th>
                        </tr>';
                if($send_mail>0){
                    $m .= 'TOTALE CREDITO: '._NF($totale_credito_utente).'<br>';
                    $m .= 'TOTALE DEBITO: '._NF($totale_debito_utente).'<br>';
                    $m .= 'SALDO: <strong>'._NF($totale_credito_utente-$totale_debito_utente).'</strong><br>';
                }                    
            
            }
            
            if($send_mail>0){
                $m = '<p>Filtro: '.$fw.'</p>'.$m;    
                SPostino($old_fullname,$old_email,"Retedes","info@retedes.it","Riepilogo cassa",$m);
                $m ='';
            }
                        
            $totale_credito_utente=0;
            $totale_debito_utente=0;
                     
        }        
        if($nascondi_intestazioni==0){
            $show++;
            $html.='<tr class="totale">
                        <th colspan=7 style="text-align:left">Utente: '.$row["fullname"].'</th>
                    </tr>';
        }
        
        if(($data_da<>"") and $nascondi_pregressi==0){
                $debiti_pregressi = VA_CASSA_SALDO_UTENTE_DEBITI_ALLADATA($row["userid"],$data_da);
                $crediti_pregressi = VA_CASSA_SALDO_UTENTE_CREDITI_ALLADATA($row["userid"],$data_da);
                $saldo_pregresso = VA_CASSA_SALDO_UTENTE_TOTALE_ALLADATA($row["userid"],$data_da);
                
                $totale_credito_utente = $crediti_pregressi;
                $totale_debito_utente = $debiti_pregressi; 
                
                $totale_debito += $debiti_pregressi;
                $totale_credito += $crediti_pregressi;
                
                $html.='<tr class="">
                            <td colspan=4 style="text-align:left">Movimenti pregressi alla data '.$data_da.'</th>
                            <td  style="text-align:right">'._NF($crediti_pregressi).'</td>
                            <td  style="text-align:right">'._NF($debiti_pregressi).'</td>
                            <td  style="text-align:right">'._NF($saldo_pregresso).'</td>
                        </tr>';
                if($send_mail>0){
                    $m.= 'Movimenti pregressi alla data '.$data_da.':<br>';
                    $m.= 'CREDITI: '._NF($crediti_pregressi).'<br>';
                    $m.= 'DEBITI: '._NF($debiti_pregressi).'<br>';
                    $m.= 'SALDO AL '.$data_da.': '._NF($saldo_pregresso).'<br><hr><br>';
                }
                        
        }
               
        $ucount ++;
        $html_u='';
               
    }
    
    if($row["segno"]=="+"){
        $credito=_NF($row["importo"]);
        $totale_credito += $row["importo"];
        $totale_credito_utente += $row["importo"];
        $debito="";
    }else{
        $credito="";
        $debito=_NF($row["importo"]);
        $totale_debito += ($row["importo"]);
        $totale_debito_utente += ($row["importo"]);
    }
    
    $tipo_movimento_r= $row["descrizione_movimento"];
    if($row["tipo"]==1){
        $tipo_movimento_r = "RICARICA";
    }
    
    if($row["id_ordine"]==0){
        $ordine = "";    
    }else{
        $ordine = "Ord #".$row["id_ordine"].' <span class="note">#'.$row["id_ditta"].' '.$row["descrizione_ditte"].'</span>';     
    }
    
    if($nascondi_dettagli==0){
        $show++;
        $html.='<tr class="'.$row_class.'">';
        $html.='<td>'.$row["data"].'</td>';
        $html.='<td>'.$row["cassiere"].'</td>';
        $html.='<td><span class="note">['.$row["tipo"].' '.$row["registrato"].']</span> '.$tipo_movimento_r.'</td>';
        $html.='<td>'.$ordine.'</td>';
        $html.='<td style="text-align:right">'.$credito.'</td>';
        $html.='<td style="text-align:right">'.$debito.'</td>';
        $html.='<td></td>';
        $html.='</tr>';
        
        if($send_mail>0){
            $m .= $row["data"].', '.$tipo_movimento_r.', ordine '.$ordine.', CREDITO:'.$credito.', DEBITO:'.$debito.'<br>';
        }
        
    }
    $old_id=$row["userid"];
    $old_fullname=$row["fullname"];
    $old_email=$row["email"];
    $old_id=$row["userid"];
    //}
    
}
if($nascondi_totali==0){
    $show++;    
    $html.='<tr class="subtotale">
                            <th colspan=4 style="text-align:left">Totale utente #'.$old_id.' '.$old_fullname.':</th>
                            <th style="text-align:right">'._NF($totale_credito_utente).'</th>
                            <th style="text-align:right">'._NF($totale_debito_utente).'</th>
                            <th style="text-align:right">'._NF($totale_credito_utente-$totale_debito_utente).'</th>
                        </tr>';
    if($send_mail>0){
        $m .= 'TOTALE CREDITO: '._NF($totale_credito_utente).'<br>';
        $m .= 'TOTALE DEBITO: '._NF($totale_debito_utente).'<br>';
        $m .= 'SALDO: <strong>'._NF($totale_credito_utente-$totale_debito_utente).'</strong><br>';
    }                                                                    
}
if($send_mail>0){
    $m = '<p>Filtro: '.$fw.'</p>'.$m;
    SPostino($old_fullname,$old_email,"Retedes","info@retedes.it","Riepilogo cassa",$m);  
}



//-----------------------------------DATI
$html.='    </tbody>
            <tfoot>
                <tr>
                    <th colspan=7></th>
                </tr>
                <tr class="totale">
                    <th colspan=4 style="text-align:left">Totali</th>
                    
                    <th style="text-align:right"><strong>'._NF($totale_credito).'</strong></th>
                    <th style="text-align:right"><strong>'._NF($totale_debito).'</strong></th>
                    <th style="text-align:right">'._NF($totale_credito-$totale_debito).'</th>
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
    <title>ReteDES.it :: Riepilogo Cassa Utente</title>
    '.pdf_css().$css.'
  </head>
  <body>'.pdf_testata_pura($orientation).$html.'</body></html>';

      $dompdf->load_html($html);
      $dompdf->set_paper("letter", $orientation);
      $dompdf->render();
      $file_title = "Ord. ".$O->id_ordini."_".$page_id."_".rand(1000,1000000).".pdf";
      $dompdf->stream($file_title, array("Attachment" => false));

      exit(0);
}
$buttons[]='<button  class="send_mail btn btn-defalut btn-default"><i class="fa fa-envelope"></i><span class="hidden-xs">  MAIL</span></button>';
$buttons[]='<button  class="show_pdf btn btn-defalut btn-default"><i class="fa fa-file-pdf-o"></i><span class="hidden-xs">  PDF</span></button>';
$buttons[]='<button  onclick="selectElementContents( document.getElementById(\'table_exp\') ); " class="btn btn-default btn-default"><i class="fa fa-copy"></i><span class="hidden-xs">  COPIA</span></button>';
$buttons[]='<button  class="btn btn-default btn-default" onclick=\'printDiv("table_container")\'><i class="fa fa-print"></i><span class="hidden-xs">  Stampa</span></button>';
$buttons[]='<button  class="btn btn-default btn-default" onclick=\'$("#opzioni_report").toggleClass("hidden");\'><i class="fa fa-gear"></i><span class="hidden-xs">  Opzioni</span></button>';




?>

<?php echo navbar_report($page_title, $buttons); ?>
<div class="margin-top-10 well well-lg" id="opzioni_report">

        <form class="smart-form">
        
            
                <div class="row">
                    <label class="label"><strong>Filtra per:</strong></label>
                    <section class="col col-4">
                        <label class="input">
                            <input type="text" placeholder="ID ordine" id="id_ordine" name="id_ordine" <?php if($id_ordine>0){echo 'value="'.$id_ordine.'"';} ?>>
                        </label>
                    </section>
                    
                    <section class="col col-4">
                        <label class="input">
                            <input type="text" placeholder="ID ditta" id="id_ditta" name="id_ditta" <?php if($id_ditta>0){echo 'value="'.$id_ditta.'"';} ?>>
                        </label>
                    </section>
                    <section class="col col-4">
                        <label class="input">
                            <input type="text" placeholder="Tipo Movimento" id="tipo_movimento" name="tipo_movimento" <?php if($tipo_movimento>0){echo 'value="'.$tipo_movimento.'"';} ?>>
                        </label>
                    </section>
                </div>
                <div class="row">
                    <label class="label"><strong>Intervallo date:</strong></label>
                    <section class="col col-6">
                        <label class="input">
                            <input type="text" placeholder="Da (incluso)" data-mask="99/99/9999" id="data_da" name="data_da" <?php if($data_da<>''){echo 'value="'.$data_da.'"';} ?>>
                        </label>
                    </section>
                    <section class="col col-6">
                        <label class="input">
                            <input type="text" placeholder="A (escluso)" data-mask="99/99/9999" id="data_a" name="data_a" <?php if($data_a<>''){echo 'value="'.$data_a.'"';} ?>>
                        </label>
                    </section>
                </div>
                <div class="row">
                    <label class="label"><strong>Dati da considerare:</strong></label>
                    <section class="col col-12">
                        <div class="inline-group">
                            <label class="checkbox">
                                <input type="checkbox" name="checkbox-inline" id="nascondi_pregressi" value="1" <?php if($nascondi_pregressi>0){echo ' checked="CHECKED" ';} ?>>
                                <i></i>Escludi movimenti pregressi</label>
                            <label class="checkbox">
                                <input type="checkbox" name="checkbox-inline" id="nascondi_registrati" value="1" <?php if($nascondi_registrati>0){echo ' checked="CHECKED" ';} ?>>
                                <i></i>Escludi registrati</label>
                            <label class="checkbox">
                                <input type="checkbox" name="checkbox-inline" id="nascondi_non_registrati" value="1" <?php if($nascondi_non_registrati>0){echo ' checked="CHECKED" ';} ?>>
                                <i></i>Escludi NON registrati</label>
                            <label class="checkbox">
                                <input type="checkbox" name="checkbox-inline" id="nascondi_debiti" value="1" <?php if($nascondi_debiti>0){echo ' checked="CHECKED" ';} ?>>
                                <i></i>Escludi Debiti</label>
                            <label class="checkbox">
                                <input type="checkbox" name="checkbox-inline" id="nascondi_crediti" value="1" <?php if($nascondi_crediti>0){echo ' checked="CHECKED" ';} ?>>
                                <i></i>Escludi Crediti</label>
                        </div>
                    </section>
                </div>
                <div class="row">
                    <label class="label"><strong>Dati da visualizzare:</strong></label>
                    <section class="col col-12">
                        
                        <div class="inline-group">
                            <label class="checkbox">
                                <input type="checkbox" name="checkbox-inline" id="nascondi_dettagli" value="1" <?php if($nascondi_dettagli>0){echo ' checked="CHECKED" ';} ?>>
                                <i></i>Nascondi Dettagli</label>
                            <label class="checkbox">
                                <input type="checkbox" name="checkbox-inline" id="nascondi_totali" value="1" <?php if($nascondi_totali>0){echo ' checked="CHECKED" ';} ?>>
                                <i></i>Nascondi Subtotali</label>
                            <label class="checkbox">
                                <input type="checkbox" name="checkbox-inline" id="nascondi_intestazioni" value="1" <?php if($nascondi_intestazioni>0){echo ' checked="CHECKED" ';} ?>>
                                <i></i>Nascondi Intestazioni</label>        
                        </div>
                    </section>
                </div>
                <div class="row">
                    <label class="label"><strong>Ordina per:</strong></label>
                    <section class="col col-12">
                        
                        <div class="inline-group">
                            <label class="radio">
                                <input type="radio" name="radio-inline" <?php if($s=="data"){echo'checked="checked"';} ?> value="data">
                                <i></i>Data</label>
                            <label class="radio">
                                <input type="radio" name="radio-inline" <?php if($s=="tipo"){echo'checked="checked"';} ?> value="tipo">
                                <i></i>Tipo movimento</label>
                            <label class="radio">
                                <input type="radio" name="radio-inline" <?php if($s=="importo_ASC"){echo'checked="checked"';} ?> value="importo_ASC">
                                <i></i>Importo (Dal più basso)</label>
                            <label class="radio">
                                <input type="radio" name="radio-inline" <?php if($s=="importo_DESC"){echo'checked="checked"';} ?> value="importo_DESC">
                                <i></i>Importo (Dal più alto)</label>
                        </div>
                    </section>
                </div>
                        
        </form>

<button class="btn btn-success pull-right" id="aggiorna_report" >Aggiorna</button>
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
    

    var pagefunction = function(){


        //------------HELP WIDGET
        document.title = '<?php echo "ReteDES.it :: $page_title";?>';
        <?php echo help_render_js($page_id);?>
        //------------END HELP WIDGET

        
        $(document).off('click','.send_mail');
        $(document).on('click', '.send_mail', function(e){
            $.SmartMessageBox({
                title : "MANDA MAIL",
                content : "Ti verrà inviata una mail con situazione cassa come l\'hai filtrata.",
                buttons : "[Esci][INVIA]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="INVIA"){
                    //ko("Funzione non ancora attiva");        
                    //return;
                    
                    var s = $('input[name="radio-inline"]:checked').val();
            
                    var id_ordine = $('#id_ordine').val();
                    if(id_ordine>0){
                        var ido = "&id_ordine="+id_ordine;
                    }else{
                        var ido = '';
                    }
                    var id_utente = $('#id_utente').val();
                    if(id_utente>0){
                       var idu = "&id_utente="+id_utente;
                       
                    }else{
                        var idu = '';
                    }
                    var id_ditta = $('#id_ditta').val();
                    if(id_ditta>0){
                        var idd = "&id_ditta="+id_ditta;
                    }else{
                        var idd = '';
                    }
                    var tipo_movimento = $('#tipo_movimento').val();
                    if(tipo_movimento>0){
                        var tpm = "&tipo_movimento="+tipo_movimento;
                    }else{
                        var tpm = '';
                    }
                    var data_da = $('#data_da').val();
                    if (typeof data_da === 'undefined' || !data_da){
                        var dtda = "";
                    }else{
                        var dtda = '&data_da='+data_da;    
                    }
                    var data_a = $('#data_a').val();
                    if (typeof data_a === 'undefined' || !data_a){
                        var dta = "";
                    }else{
                        var dta = '&data_a='+data_a;    
                    }
                    var nascondi_registrati = $('#nascondi_registrati:checked').val();
                    if (nascondi_registrati == null){
                        var nsr = "";
                    }else{
                        var nsr = '&nascondi_registrati='+nascondi_registrati;    
                    }
                    
                    var nascondi_non_registrati = $('#nascondi_non_registrati:checked').val();
                    if (nascondi_non_registrati == null){
                        var nsnr = "";
                    }else{
                        var nsnr = '&nascondi_non_registrati='+nascondi_non_registrati;    
                    }
                    
                    var nascondi_crediti = $('#nascondi_crediti:checked').val();
                    if (nascondi_crediti == null){
                        var nscr = "";
                    }else{
                        var nscr = '&nascondi_crediti='+nascondi_crediti;    
                    }
                    
                    var nascondi_debiti = $('#nascondi_debiti:checked').val();
                    if (nascondi_debiti == null){
                        var nsdb = "";
                    }else{
                        var nsdb = '&nascondi_debiti='+nascondi_debiti;    
                    }
                    
                    var nascondi_pregressi = $('#nascondi_pregressi:checked').val();
                    if (nascondi_pregressi == null){
                        var nspr = "";
                    }else{
                        var nspr = '&nascondi_pregressi='+nascondi_pregressi;    
                    }
                    
                    var nascondi_dettagli = $('#nascondi_dettagli:checked').val();
                    if (typeof nascondi_dettagli === 'undefined' || !nascondi_dettagli){
                        var nsd = "";
                    }else{
                        var nsd = '&nascondi_dettagli='+nascondi_dettagli;    
                    }
                    var nascondi_totali = $('#nascondi_totali:checked').val();
                    if (nascondi_totali == null){
                        var nst = "";
                    }else{
                        var nst = '&nascondi_totali='+nascondi_totali;    
                    }
                    
                    var nascondi_intestazioni = $('#nascondi_intestazioni:checked').val();
                    if (nascondi_intestazioni == null){
                        var nsi = "";
                    }else{
                        var nsi = '&nascondi_intestazioni='+nascondi_intestazioni;    
                    }
                    
                    location.replace('<?php echo APP_URL; ?>/#ajax_rd4/user/mia_cassa.php?'+ido+idu+idd+tpm+nsd+nst+nsi+nsr+nsnr+nsdb+nscr+nspr+dtda+dta+'&s='+s+'&send_mail=1');
                    return false;
                    
                    
                }
            });
            
            
            
        });
        
        $(document).off('click','.show_pdf');
        $(document).on('click', '.show_pdf', function(e){
       // $('.show_pdf').click(function(){
            var $this = $(this);
            
            <?php if($show>200){ ?>
                ko("PDF non generabile, restringere il filtro... :(");
                return;
            
            <?php }?>
            
            var s = $('input[name="radio-inline"]:checked').val();

            var id_ordine = $('#id_ordine').val();
            if(id_ordine>0){
                var ido =id_ordine;
            }else{
                var ido = '';
            }
            var id_utente = $('#id_utente').val();
            if(id_utente>0){
                var idu = id_utente;
            }else{
                var idu = '';
            }
            var id_ditta = $('#id_ditta').val();
            if(id_ditta>0){
                var idd = id_ditta;
            }else{
                var idd = '';
            }
            var tipo_movimento = $('#tipo_movimento').val();
            if(tipo_movimento>0){
                var tpm = tipo_movimento;
            }else{
                var tpm = '';
            }
            var data_da = $('#data_da').val();
            if (typeof data_da === 'undefined' || !data_da){
                var dtda = "";
            }else{
                var dtda = data_da;    
            }
            var data_a = $('#data_a').val();
            if (typeof data_a === 'undefined' || !data_a){
                var dta = "";
            }else{
                var dta = data_a;    
            }
            var nascondi_registrati = $('#nascondi_registrati:checked').val();
            if (nascondi_registrati == null){
                var nsr = "";
            }else{
                var nsr = nascondi_registrati;    
            }
            
            var nascondi_non_registrati = $('#nascondi_non_registrati:checked').val();
            if (nascondi_non_registrati == null){
                var nsnr = "";
            }else{
                var nsnr = nascondi_non_registrati;    
            }
            
            var nascondi_crediti = $('#nascondi_crediti:checked').val();
            if (nascondi_crediti == null){
                var nscr = "";
            }else{
                var nscr = nascondi_crediti;    
            }
            
            var nascondi_debiti = $('#nascondi_debiti:checked').val();
            if (nascondi_debiti == null){
                var nsdb = "";
            }else{
                var nsdb = nascondi_debiti;    
            }
            
            var nascondi_pregressi = $('#nascondi_pregressi:checked').val();
            if (nascondi_pregressi == null){
                var nspr = "";
            }else{
                var nspr = nascondi_pregressi;    
            }
            
            var nascondi_dettagli = $('#nascondi_dettagli:checked').val();
            if (typeof nascondi_dettagli === 'undefined' || !nascondi_dettagli){
                var nsd = "";
            }else{
                var nsd = nascondi_dettagli;    
            }
            var nascondi_totali = $('#nascondi_totali:checked').val();
            if (nascondi_totali == null){
                var nst = "";
            }else{
                var nst = nascondi_totali;    
            }
            
            var nascondi_intestazioni = $('#nascondi_intestazioni:checked').val();
            if (nascondi_intestazioni == null){
                var nsi = "";
            }else{
                var nsi = nascondi_intestazioni;    
            }
            
            //--------------------------------------------------------------------------------------------------+ido+idu+idd+tpm+nsd+nst+nsi+nsr+nsnr+nsdb+nscr+dtda+dta+'&s='+s
            
            open('POST', '<?php echo APP_URL; ?>/ajax_rd4/user/mia_cassa.php', { o:'pdf', 
                                                                                        dummy:<?php echo rand(1000,9999); ?>, 
                                                                                        s:s, 
                                                                                        nascondi_intestazioni:nsi,
                                                                                        nascondi_totali:nst,
                                                                                        nascondi_dettagli:nsd,
                                                                                        nascondi_debiti:nsdb,
                                                                                        nascondi_crediti:nscr,
                                                                                        nascondi_pregressi:nspr,
                                                                                        nascondi_non_registrati:nsnr,
                                                                                        nascondi_registrati:nsr,
                                                                                        tipo_movimento:tpm,
                                                                                        id_ordine:ido,
                                                                                        id_ditta:idd,
                                                                                        id_utente:idu,
                                                                                        data_da:dtda,
                                                                                        data_a:dta 
                                                                                        
                                                                                        }, '_blank');
            return false;
        });

        $('#aggiorna_report').click(function(){
            var $this = $(this);
            var s = $('input[name="radio-inline"]:checked').val();
            
            var id_ordine = $('#id_ordine').val();
            if(id_ordine>0){
                var ido = "&id_ordine="+id_ordine;
            }else{
                var ido = '';
            }
            var id_utente = $('#id_utente').val();
            if(id_utente>0){
               var idu = "&id_utente="+id_utente;
               
            }else{
                var idu = '';
            }
            var id_ditta = $('#id_ditta').val();
            if(id_ditta>0){
                var idd = "&id_ditta="+id_ditta;
            }else{
                var idd = '';
            }
            var tipo_movimento = $('#tipo_movimento').val();
            if(tipo_movimento>0){
                var tpm = "&tipo_movimento="+tipo_movimento;
            }else{
                var tpm = '';
            }
            var data_da = $('#data_da').val();
            if (typeof data_da === 'undefined' || !data_da){
                var dtda = "";
            }else{
                var dtda = '&data_da='+data_da;    
            }
            var data_a = $('#data_a').val();
            if (typeof data_a === 'undefined' || !data_a){
                var dta = "";
            }else{
                var dta = '&data_a='+data_a;    
            }
            var nascondi_registrati = $('#nascondi_registrati:checked').val();
            if (nascondi_registrati == null){
                var nsr = "";
            }else{
                var nsr = '&nascondi_registrati='+nascondi_registrati;    
            }
            
            var nascondi_non_registrati = $('#nascondi_non_registrati:checked').val();
            if (nascondi_non_registrati == null){
                var nsnr = "";
            }else{
                var nsnr = '&nascondi_non_registrati='+nascondi_non_registrati;    
            }
            
            var nascondi_crediti = $('#nascondi_crediti:checked').val();
            if (nascondi_crediti == null){
                var nscr = "";
            }else{
                var nscr = '&nascondi_crediti='+nascondi_crediti;    
            }
            
            var nascondi_debiti = $('#nascondi_debiti:checked').val();
            if (nascondi_debiti == null){
                var nsdb = "";
            }else{
                var nsdb = '&nascondi_debiti='+nascondi_debiti;    
            }
            
            var nascondi_pregressi = $('#nascondi_pregressi:checked').val();
            if (nascondi_pregressi == null){
                var nspr = "";
            }else{
                var nspr = '&nascondi_pregressi='+nascondi_pregressi;    
            }
                    
            var nascondi_dettagli = $('#nascondi_dettagli:checked').val();
            if (typeof nascondi_dettagli === 'undefined' || !nascondi_dettagli){
                var nsd = "";
            }else{
                var nsd = '&nascondi_dettagli='+nascondi_dettagli;    
            }
            var nascondi_totali = $('#nascondi_totali:checked').val();
            if (nascondi_totali == null){
                var nst = "";
            }else{
                var nst = '&nascondi_totali='+nascondi_totali;    
            }
            
            var nascondi_intestazioni = $('#nascondi_intestazioni:checked').val();
            if (nascondi_intestazioni == null){
                var nsi = "";
            }else{
                var nsi = '&nascondi_intestazioni='+nascondi_intestazioni;    
            }
            
            location.replace('<?php echo APP_URL; ?>/#ajax_rd4/user/mia_cassa.php?'+ido+idu+idd+tpm+nsd+nst+nsi+nsr+nsnr+nsdb+nspr+nscr+dtda+dta+'&s='+s);
            return false;
        });

    } // end pagefunction

pagefunction();
</script>

