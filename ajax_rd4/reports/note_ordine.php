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
//MOSTRA
$m = CAST_TO_STRING($_POST["m"]);
if ($m==""){
    $m = CAST_TO_STRING($_GET["m"]);
    if ($m==""){
        $m="solo_note";
    }
}


//ORDINAMENTO
$s = CAST_TO_STRING($_POST["s"]);
if ($s==""){
    $s = CAST_TO_STRING($_GET["s"]);
    if ($s==""){
        $s="fullname";
    }
}


if ($s=="id_utente"){
    $sort = " ORDER BY userid ASC ";
}
if ($s=="fullname"){
    $sort = " ORDER BY fullname ASC ";
}
if ($s=="tessera"){
    $sort = " ORDER BY tessera ASC ";
}
if ($s=="id_gas"){
    $sort = " ORDER BY id_gas ASC";
}
//ORDINAMENTO


if($id_ordine==0){echo "missing id"; die();}
$O = new ordine($id_ordine);
$page_title = "Report - note utenti";
$page_id = "report_note_utenti";
$orientation = "landscape"; // portrait / landscape
//------------------------------PAGE


$html='<h3>Ordine #'.$O->id_ordini.' - '.$O->descrizione_ordini.'</h3>';
//-----------------------------------CONTENT
$html.='<table id="table_exp" class="rd4" style="margin-left: auto; margin-right: auto">
            <thead >
                <tr class="intestazione" >
                    <th style="width:5%;">ID</th>
                    <th style="width:5%;">TESSERA</th>
                    <th style="width:20%;">Utente</th>
                    <th style="width:20%;">GAS</th>
                    <th style="">Note</th>
                </tr>
            </thead>
            <tbody>';
//-----------------------------------DATI

//WARNINGS
if($O->codice_stato<>"CO"){
    $warning ='<div class="alert alert-danger margin-top-10"><strong>ATTENZIONE:</strong> questo ordine non è ancora stato convalidato, e quindi le cifre potrebbero non essere definitive.</div>';
}else{
    $warning ='';
}
$old_descrizione_ditta="";

$sql = "SELECT DISTINCT D.id_utenti, U.fullname, G.descrizione_gas, U.id_gas, U.tessera from retegas_dettaglio_ordini D inner join maaking_users U on U.userid=D.id_utenti inner join retegas_gas G on G.id_gas = U.id_gas WHERE id_ordine=:id_ordine $sort";
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

    $note_ordine=$O->get_note_utente($row["id_utenti"]);

    
    if($m=="tutti"){
        $html.='<tr class="'.$row_class.'">';
        $html.='<td>'.$row["id_utenti"].'</td>';
        $html.='<td>'.$row["tessera"].'</td>';
        $html.='<td>'.$row["fullname"].'</td>';
        $html.='<td>'.$row["descrizione_gas"].'</td>';
        $html.='<td>'.$note_ordine.'</td>';
        $html.='</tr>';
    }else{
        if($note_ordine<>''){
            $html.='<tr class="'.$row_class.'">';
            $html.='<td>'.$row["id_utenti"].'</td>';
            $html.='<td>'.$row["tessera"].'</td>';
            $html.='<td>'.$row["fullname"].'</td>';
            $html.='<td>'.$row["descrizione_gas"].'</td>';
            $html.='<td>'.$note_ordine.'</td>';
            $html.='</tr>';
        }
        
    }

}


//-----------------------------------DATI
$html.='    </tbody>
            <tfoot>

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
                            <input type="radio" name="radio-inline" <?php if($s=="id_utente"){echo'checked="checked"';} ?> value="id_utente">
                            <i></i>ID utente</label>
                        <label class="radio">
                            <input type="radio" name="radio-inline" <?php if($s=="fullname"){echo'checked="checked"';} ?> value="fullname">
                            <i></i>Nome</label>
                        <label class="radio">
                            <input type="radio" name="radio-inline" <?php if($s=="tessera"){echo'checked="checked"';} ?> value="tessera">
                            <i></i>Tessera</label>
                        <label class="radio">
                            <input type="radio" name="radio-inline" <?php if($s=="id_gas"){echo'checked="checked"';} ?> value="id_gas">
                            <i></i>GAS</label>
                    </div>
                </section>
            </div>
        </div>
        </form>
</div>
<div class="row">
        <form class="smart-form">
        <div class="col-md-12">
            <div class="padding-10">
                <section>
                    <label class="label">Mostra:</label>
                    <div class="inline-group">
                        <label class="radio">
                            <input type="radio" name="mostra" <?php if($m=="solo_note"){echo'checked="checked"';} ?> value="solo_note">
                            <i></i>Solo utenti con note</label>
                        <label class="radio">
                            <input type="radio" name="mostra" <?php if($m=="tutti"){echo'checked="checked"';} ?> value="tutti">
                            <i></i>Tutti gli utenti</label>
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

            open('POST', '<?php echo APP_URL; ?>/ajax_rd4/reports/note_ordine.php', {id:id, o:'pdf', dummy:<?php echo rand(1000,9999); ?>, s:s}, '_blank');
            return false;
        });

        $('#aggiorna_report').click(function(){
            var $this = $(this);
            var id = $this.data('id_ordine');
            var s = $('input[name="radio-inline"]:checked').val();
            var m = $('input[name="mostra"]:checked').val();

            location.replace('<?php echo APP_URL; ?>/#ajax_rd4/reports/note_ordine.php?id='+id+'&s='+s+'&m='+m);
            return false;
        });

    } // end pagefunction

pagefunction();
</script>
