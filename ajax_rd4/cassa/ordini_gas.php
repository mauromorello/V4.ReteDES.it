<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.ordine.php");
require_once("../../lib_rd4/class.rd4.gas.php");
$ui = new SmartUI;
//-------------------------------INIT

//-------------------------------PAGE
$page_title = "Cassa - ordini GAS";
$page_id = "report_cassa_ordini_gas";
$orientation = "portrait"; // portrait / landscape
//------------------------------PAGE


//------------PARAMETRI
$data_da=CAST_TO_STRING($_GET["data_da"]);
$data_a= CAST_TO_STRING($_GET["data_a"]);

if($data_da=="" | $data_a==""){
    $data_da = "01/01/2015";
    $data_a = date("d/m/Y");
}

if((!empty($_GET["id_ditta"])) AND $_GET["id_ditta"]<>""){
    $id_ditta = CAST_TO_INT($_GET["id_ditta"]);
}

$includi_extra_gas=CAST_TO_INT($_GET["includi_extra_gas"],0);;

if(gas_mktime($data_da)>gas_mktime($data_a)){
    $data_da = "01/01/2015";
    $data_a = date("d/m/Y");
}

if($id_ditta>0){
    $filtro_ditta=" AND retegas_listini.id_ditte='$id_ditta' ";
    $title_ditta=", solo la ditta \"$id_ditta\" ";
}else{
    $filtro_ditta="";
    $title_ditta=", tutte le ditte ";
}

$data_da_f = $data_da;
$data_a_f = $data_a;
$data_da = conv_date_to_db($data_da);
$data_a = conv_date_to_db($data_a);


//------------PARAMETRI


//-----------------------------------CONTENT
$id_gas=_USER_ID_GAS;
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
                    <th style="width:6%;">ID</th>
                    <th >Ordine</th>
                    <th style="width:10%;" class="text-right">REF</th>
                    <th style="width:10%;" class="text-right">NETTO</th>
                    <th style="width:10%;" class="text-right">@@RETT</th>
                    <th style="width:10%;" class="text-right">##GAS</th>
                    <th style="width:10%;" class="text-right">TOTALE</th>
                </tr>
            </thead>
            <tbody>';
//-----------------------------------DATI

$sql_int ="SELECT
            retegas_ordini.id_ordini,
            retegas_ordini.descrizione_ordini,
            retegas_ordini.data_chiusura,
            costo_trasporto,
            costo_gestione,
            maaking_users.fullname,
            retegas_listini.id_ditte,
            retegas_ditte.descrizione_ditte
            FROM
            retegas_ordini
            Inner Join maaking_users ON retegas_ordini.id_utente = maaking_users.userid
            Inner Join retegas_listini ON retegas_ordini.id_listini = retegas_listini.id_listini
            Inner Join retegas_ditte ON retegas_listini.id_ditte = retegas_ditte.id_ditte
            WHERE
            maaking_users.id_gas =  '$id_gas' AND
            retegas_ordini.data_chiusura BETWEEN  '$data_da' AND '$data_a'
            $filtro_ditta
            ORDER BY data_chiusura DESC;";

$sql_ext = "SELECT
            retegas_referenze.id_utente_referenze,
            retegas_ordini.descrizione_ordini,
            retegas_ordini.data_chiusura,
            maaking_users.fullname,
            retegas_ordini.id_ordini,
            retegas_listini.id_ditte,
            retegas_ditte.descrizione_ditte,
            maaking_users.id_gas
            FROM
            retegas_referenze
            Inner Join retegas_ordini ON retegas_referenze.id_ordine_referenze = retegas_ordini.id_ordini
            Inner Join maaking_users ON retegas_referenze.id_utente_referenze = maaking_users.userid
            Inner Join retegas_listini ON retegas_ordini.id_listini = retegas_listini.id_listini
            Inner Join retegas_ditte ON retegas_listini.id_ditte = retegas_ditte.id_ditte
            WHERE
            retegas_referenze.id_gas_referenze =  '$id_gas' AND
            retegas_referenze.id_utente_referenze <>  '0' AND
            retegas_ordini.data_chiusura BETWEEN  '$data_da' AND '$data_a'
            $filtro_ditta
            ORDER BY data_chiusura DESC;";

if($includi_extra_gas>0){
    $stmt = $db->prepare($sql_ext);
    $html .="<h3>INCLUSI ORDINI GAS ESTERNI</h3>";
}else{
    $stmt = $db->prepare($sql_int);
    $html .="<h3>ESCLUSI ORDINI GAS ESTERNI</h3>";
}

$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($rows AS $rowO){

        $netto = VA_ORDINE_GAS_SOLO_NETTO($rowO["id_ordini"],$id_gas);
        $totale_netto += $netto;

        $extra = VA_ORDINE_GAS_SOLO_RETT($rowO["id_ordini"],$id_gas);
        $totale_extra += $extra;

        $gas = VA_ORDINE_GAS_SOLO_EXTRA_GAS($rowO["id_ordini"],$id_gas);
        $totale_gas += $gas;

        $totale = VA_ORDINE_GAS($rowO["id_ordini"],$id_gas);
        $totale_totale += $totale;

        if($_POST["o"]<>"pdf"){
            $id_ordine = '<a href="'.APP_URL.'/#ajax_rd4/reports/utenti_gas.php?id='.$rowO["id_ordini"].'">'.$rowO["id_ordini"].'</a>';
        }else{
            $id_ordine = $rowO["id_ordini"];
        }

        $html .='<tr>';
        $html .='<td>'.$id_ordine.'</td>';
        $html .='<td>'.$rowO["descrizione_ordini"].'<br><small class="note">'.conv_date_from_db($rowO["data_chiusura"]).' ('.$rowO["id_ditte"].' '.$rowO["descrizione_ditte"].')</small></td>';
        $html .='<td>'.$rowO["fullname"].'</td>';
        $html .='<td class="text-right">'._nf($netto).'</td>';
        $html .='<td class="text-right">'._nf($extra).'</td>';
        $html .='<td class="text-right">'._nf($gas).'</td>';
        $html .='<td class="text-right"><strong>'._NF($totale).'</strong></td>';
        $html .='</tr>';

}//ORDINE



//-----------------------------------DATI
$html.='
            </tbody>
            <tfoot>
                <tr class="totale">
                    <th style="text-align:right"></th>
                    <th style="text-align:right"></th>
                    <th style="text-align:right"></th>
                    <th style="text-align:right">'._nf($totale_netto).'</th>
                    <th style="text-align:right">'._nf($totale_extra).'</th>
                    <th style="text-align:right">'._nf($totale_gas).'</th>
                    <th style="text-align:right">'._nf($totale_totale).'</th>
                </tr>
            </tfoot>
         </table>';
//-----------------------------------FOOTER
//{id:0,text:"Tutti"},
//                {id:1,text:"bug"},
//                {id:2,text:"duplicate"},
//                {id:3,text:"invalid"},
 //               {id:4,text:"wontfix"}


$css = css_report($orientation);
if($_POST["o"]=="print"){}
if($_POST["o"]=="pdf"){
    include_once('../../lib_rd4/dompdf/dompdf_config.inc.php');
    $dompdf = new DOMPDF();

      $html ='<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <title>ReteDES.it :: Ordini GAS</title>
    '.pdf_css().$css.'
  </head>
  <body>'.pdf_testata($O,$orientation).$html.'</body></html>';
      //echo $html;die();
      $dompdf->load_html($html);
      $dompdf->set_paper("letter", $orientation);
      $dompdf->render();
      $file_title = $page_id."_".rand(1000,1000000).".pdf";
      $dompdf->stream($file_title, array("Attachment" => false));

      exit(0);
}

$buttons[]='<button  class="show_pdf btn btn-defalut btn-default"><i class="fa fa-file-pdf-o"></i>  PDF</button>';
$buttons[]='<button  onclick="selectElementContents( document.getElementById(\'table_exp\') ); " class="btn btn-default btn-default"><i class="fa fa-copy"></i>  COPIA</button>';
$buttons[]='<button  data-id_ordine="" class="btn btn-default btn-default" onclick=\'printDiv("table_container")\'><i class="fa fa-print"></i>  Stampa</button>';



?>
<br><br><br>
<?php echo navbar_report($page_title, $buttons); ?>
<div class="well well-sm" id="opzioni_report">
    <div class="row padding-10">
    <form action="" method="GET" id="c1_form">
        <div class="row padding-10">
            <div class="col-sm-6">
                <div class="form-group">
                    <div class="input-group">
                        <input class="form-control datepicker" data-dateformat="dd/mm/yy" name="data_da" placeholder="Data inizio" type="text" value="<?php echo $data_da_f; ?>">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <div class="input-group">
                        <input class="form-control datepicker" name="data_a" data-dateformat="dd/mm/yy" placeholder="Data fine" type="text" value="<?php echo $data_a_f; ?>">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>

            </div>
        </div>
         <div class="row padding-10">
            <div class="col-sm-6">
                <div class="form-group">
                    <div class="input-group">
                        <input class="form-control text"  name="id_ditta" placeholder="ID ditta" type="text" value="<?php echo $id_ditta; ?>">
                        <span class="input-group-addon"><i class="fa fa-truck"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row padding-10">
            <div class="col-sm-12">
                <label>
                    <input class="checkbox" type="checkbox" name="includi_extra_gas" <?php if($includi_extra_gas>0)echo' CHECKED="CHECKED" ';?> value="1">
                <span>Includi partecipazioni extra-gas</span></label>
            </div>
        </div>

        <div class="pull-right btn-group-sm">
            <button class="btn btn-success pull-right" id="aggiorna_report" type="submit">Aggiorna</button>
        </div>
        <div class="clearfix"></div>
    </form>
    </div>

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

        $(document).on('click','.show_pdf', function(){
        //$('.show_pdf').click(function(){
            var $this = $(this);

            open('POST', '<?php echo APP_URL; ?>/ajax_rd4/cassa/ordini_gas.php', {o:'pdf', dummy:<?php echo rand(1000,9999); ?>}, '_blank');
            return false;
        });


    } // end pagefunction

pagefunction();
</script>
