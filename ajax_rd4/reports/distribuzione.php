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
$page_title = "Report - distribuzione merce";
$page_id = "report_distribuzione";
$orientation = "portrait"; // portrait / landscape
//------------------------------PAGE

//------------------------------MEMORY USAGE
$x = '';
//while(true) {
  //echo "not real: ".(memory_get_peak_usage(false)/1024/1024)." MiB - ";
  //echo "real: ".(memory_get_peak_usage(true)/1024/1024)." MiB<br>";
 // $x .= str_repeat(' ', 1024*25); //store 25kb more to string
//}



//-----------------------------------CONTENT
$gas_list = CAST_TO_STRING($_POST["gs"]);
if ($gas_list==""){
    $gas_list = CAST_TO_STRING($_GET["gs"]);
}
if($gas_list<>""){
    $gas_list = explode(",", $gas_list);

    $gas_filter = true;
}else{
    $gas_filter = false;
}




$html= '<table id="table_exp" class="rd4" style="margin-left: auto; margin-right: auto">
            <thead >
                <tr class="intestazione" >
                    <th style="width:15%;">Codice</th>
                    <th style="width:25%;">Descrizione</th>
                    <th style="width:10%;" class="text-right">Quantità Articoli</th>
                    <th style="width:10%;" class="text-right">Scatole</th>
                    <th style="width:10%;" class="text-right">Avanzo</th>
                </tr>
            </thead>
            <tbody>';
//-----------------------------------DATI

$sql = "SELECT D.art_codice, D.art_desc, D.art_um, SUM(D.qta_arr) as totale_articolo
            FROM retegas_dettaglio_ordini D
            WHERE D.id_ordine=:id_ordine GROUP BY D.art_codice, D.art_desc
            HAVING SUM(D.qta_arr)>0";
$stmt = $db->prepare($sql);
$stmt->bindParam(':id_ordine', $O->id_ordini, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($rows AS $row){



            $r++;
            if(is_integer($riga/2)){
                $row_class="odd";
            }else{
                $row_class="even";
            }


            if($row["art_codice"]<>$articolo_attuale){
                $top_articolo = "border-top: solid 3px #000;";
            }else{
                $top_articolo = "";
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
                    $i -= round($scatola,2);
                    $scatole ++;
                }
                if($i<0){
                    //$alert='<span class="label label-danger">NO</span>';
                    $avanzo = round($scatola + $i,2);
                }else{
                    //$alert="OK";
                    $scatole++;
                }
                $scatole--;

            }else{
                $scatole="";
                $avanzo="";
                $scatola= 0;


            }
            if($scatole==0){$scatole="";}
            if($avanzo==0){$avanzo="";}


            $html_show='<tr class="'.$row_class.'" style="'.$top_articolo.'">';
            $html_show.='<td><strong>'.$row["art_codice"].'</strong></td>';
            $html_show.='<td>'.$row["art_desc"].' <span class="note">('.$row["art_um"].')</span></td>';
            $html_show.='<td style="text-align:right"><strong>'.number_format($row["totale_articolo"],2, ',', '').'</strong></td>';
            $html_show.='<td style="text-align:right">'.number_format($scatole,2, ',', '').'</td>';
            $html_show.='<td style="text-align:right">'.number_format($avanzo,2, ',', '').'</td>';
            $html_show.='</tr>';

            $totale += round($row["totale_articolo"]);
            $Tscatole += round($scatole);
            $Tavanzi += round($avanzo);
            $gas_attuale = "";
            //---------------------------------------------------DETTAGLIO
            $sql = "SELECT D.art_codice, D.art_desc, D.art_um, D.qta_arr as totale_articolo, U.fullname, G.descrizione_gas, G.id_gas
                    FROM retegas_dettaglio_ordini D
                    inner join maaking_users U on U.userid= D.id_utenti
                    inner join retegas_gas G on G.id_gas=U.id_gas
                    WHERE D.id_ordine=:id_ordine AND D.art_codice=:art_codice

                    ORDER BY G.descrizione_gas ASC";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_ordine', $O->id_ordini, PDO::PARAM_INT);
            $stmt->bindParam(':art_codice', $row["art_codice"], PDO::PARAM_INT);
            $stmt->execute();
            $rowsD = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $html_show.='<tr class="'.$row_class.'" style="'.$top_gas.'">';
            $html_show.='<td colspan=5>';
            $show =0;
            foreach($rowsD AS $rowD){
                $r++;
                $scatole=0;
                $avanzo=0;

                $i=$rowD["totale_articolo"];
                if($scatola>0){
                    while($i>0){
                        //echo $i;
                        $i = round($i-$scatola,2);
                        $scatole ++;
                    }
                    if($i<0){
                        //$alert='<span class="label label-danger">NO</span>';
                        $avanzo = round($scatola + $i,2);
                    }else{
                        //$alert="OK";
                        $scatole++;
                    }
                    $scatole--;
                }else{
                    $scatole='';
                    $avanzo='';
                }

                if($rowD["descrizione_gas"]<>$gas_attuale){
                    $top_gas = "border-top: dotted 3px #AAA ";
                    $pixels = 3;
                    $descrizione_gas=$rowD["descrizione_gas"];
                }else{
                    $top_gas = "";
                    $descrizione_gas=$rowD["descrizione_gas"];
                    $pixels = 1;
                }

                if(ROUND($rowD["totale_articolo"],4)<>round($scatole,4)){
                    if($avanzo>0){
                        if($avanzo==1){
                            $plurale_avanzi=" avanzo";
                        }else{
                            $plurale_avanzi=" avanzi";
                        }

                        $parola_avanzo=' '.number_format($avanzo,2, ',', '').' '.$plurale_avanzi;
                    }else{
                        $parola_avanzo='';
                    }

                    if($scatole>0){

                        if($scatole==1){
                                $plurale_scatola=" scatola";
                        }else{
                                $plurale_scatola=" scatole";
                        }
                        $gruppo_scatole='  <span style="font-size:10px;">('.number_format($scatole,2, ',', '').' '.$plurale_scatola.' '.$parola_avanzo.' )</span>';
                    }else{
                        $gruppo_scatole='';
                    }
                }else{
                    $gruppo_scatole="";
                }
                if(ROUND($rowD["totale_articolo"],4)==1){
                    $articoli="articolo";
                }else{
                    $articoli="articoli";
                }


                $html_row ='<div style="border-top:'.$pixels.'px solid #ccc;">
                            <span style="width:220px; display:inline-block; font-size:12px;">'.$descrizione_gas.'</span><span style="width:200px;display:inline-block;">
                                <strong style="font-size:12px;">'.$rowD["fullname"].'</strong>
                            </span>
                            <span style="font-size:12px;">'.number_format($rowD["totale_articolo"],2, ',', '').' '.$articoli.'.</span> '.$gruppo_scatole.'</div>';
                $gas_attuale = $rowD["descrizione_gas"];

                if($gas_filter){
                    if(in_array($rowD["id_gas"],$gas_list)){
                        $html_show .= $html_row;
                        $show++;
                    }//IN_ARRAY
                }else{
                    $html_show .= $html_row;
                    $show++;
                }//FILTER GAS
                $html_row="";

            }
            $html_show.='</td>';
            $html_show.='</tr>';
            //---------------------------------------------------DETTAGLIO

            $articolo_attuale = $row["art_codice"];

            if($show>0){
                $riga++;
                $html .= $html_show;
            }

}//CODICE

    if($Tavanzi==0){$Tavanzi="";}

//-----------------------------------DATI
$html.='    </tbody>
            <tfoot>
                <tr class="totale">
                    <th colspan=2 style="text-align:right">Totale</th>
                    <th style="text-align:right"><strong>'.number_format($totale,2, ',', '').'</strong></th>
                    <th style="text-align:right"><strong>'.number_format($Tscatole,2, ',', '').'</strong></th>
                    <th style="text-align:right"><strong>'.number_format($Tavanzi,2, ',', '').'</strong></th>
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
    var gas_selection,gas_list;

    var pagefunction = function(){


        //------------HELP WIDGET
        document.title = '<?php echo "ReteDES.it :: $page_title";?>';
        <?php echo help_render_js($page_id);?>
        //------------END HELP WIDGET
        var gs = $('#gas_select');
        $(gs).select2({
            data:[
                <?php echo $gsel; ?>
            ],
            multiple: true,
            width: "100%"
        });
        $(gs).change(function() {
            gas_selection = $(gs).select2('data');
            console.log(gas_selection);
        });
        $('.show_pdf').click(function(){
            var $this = $(this);
            var id = $this.data('id_ordine');
            gas_list ='';
            for (index = 0; index < gas_selection.length; ++index) {
                gas_list = gas_list + gas_selection[index].id+",";
            }
            open('POST', 'http://retegas.altervista.org/gas4/ajax_rd4/reports/distribuzione.php', {id:id, o:'pdf', dummy:<?php echo rand(1000,9999); ?>, gs: gas_list}, '_blank');
            return false;
        });
        $('#aggiorna_report').click(function(){
            var $this = $(this);
            var id = $this.data('id_ordine');
            gas_list ='';
            for (index = 0; index < gas_selection.length; ++index) {
                gas_list = gas_list + gas_selection[index].id+",";
            }

            console.log(gas_list);
            location.replace('http://retegas.altervista.org/gas4/#ajax_rd4/reports/distribuzione.php?id='+id+'&gs='+gas_list);
            //window.location.assign('http://retegas.altervista.org/gas4/#ajax_rd4/reports/distribuzione.php?id='+id+'&gas_select='+gas_selection);
            //open('GET', 'http://retegas.altervista.org/gas4/#ajax_rd4/reports/distribuzione.php', {id:id, dummy:<?php echo rand(1000,9999); ?>, gas_selection: gas_selection}, '_self');
            return false;
        });
        gas_selection = $(gs).select2('data');
    } // end pagefunction

pagefunction();
</script>
