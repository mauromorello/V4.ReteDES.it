<?php
require_once("inc/init.php");



$id_ordine = CAST_TO_INT($_POST["id"],0);
if ($id_ordine==0){
    $id_ordine = CAST_TO_INT($_GET["id"],0);
}


if($id_ordine==0){echo "missing id"; die();}

$page_title = "Report - La mia spesa";


$html='<div style="width:1024px;border:2px solid red;">
         <p>Test '.$id_ordine.'</p>
       </div>';

if($_POST["o"]=="print"){

}
if($_POST["o"]=="pdf"){
    include_once('../../lib_rd4/dompdf/dompdf_config.inc.php');
    $dompdf = new DOMPDF();

      $html ='<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <title>Header and Footer example</title>
    <style type="text/css">
      @page {
        margin: 10px;
      }
    </style>
  </head>
  <body>'.$html.'</body></html>';

      $dompdf->load_html($html);
      $dompdf->set_paper("letter", "landscape");
      $dompdf->render();
      $dompdf->stream("dompdf_out-".rand(1000,1000000).".pdf", array("Attachment" => true));

      exit(0);
}
if($_POST["o"]=="xls"){

}

$title_navbar='<i class="fa fa-newspaper-o fa-2x pull-left"></i> La mia spesa<br><small class="note">Bla bla bla</small>';
if(_USER_PERMISSIONS & perm::puo_creare_listini){
    $buttons[]='<form style="margin-right:10px;"><button  id="show_pdf" data-id_ordine="'.$id_ordine.'" class="btn btn-primary btn-default navbar-btn"><i class="fa fa-file-pdf-o"></i>  PDF</button></form>';
    $buttons[]='<form style="margin-right:10px;"><button  data-id_ordine="" class="btn btn-default btn-default navbar-btn"><i class="fa fa-file-excel-o"></i>  XLS</button></form>';
    $buttons[]='<form style="margin-right:10px;"><button  data-id_ordine="" class="btn btn-default btn-default navbar-btn"><i class="fa fa-print"></i>  Stampa</button></form>';
}

?>
<?php echo navbar($title_navbar,$buttons); ?>
<div class="container" style="overflow-x:auto;width:100%;">
    <?php echo $html; ?>
</div>

<script type="text/javascript">

    pageSetUp();



    var pagefunction = function(){
        $('#show_pdf').click(function(){
            var id = $(this).data('id_ordine');
            open('POST', 'http://retegas.altervista.org/gas4/ajax_rd4/reports/test_pdf_1.php', {id:id, o:'pdf', dummy:<?php echo rand(1000,9999); ?> }, '_blank');
            return false;
        });

    } // end pagefunction

pagefunction();
</script>
