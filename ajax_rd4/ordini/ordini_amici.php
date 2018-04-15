<?php require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.ordine.php");
require_once("../../lib_rd4/class.rd4.user.php");

$ui = new SmartUI;
$page_title= "Gestione distribuzione";
$page_id ="gestione_distribuzione";

//CONTROLLI
$id_ordine = (int)$_GET["id"];


$O = new ordine($id_ordine);
if($O->codice_stato<>"AP"){
    $aperto=false;
}else{
    $aperto=true;
}

if(CAST_TO_INT(QTA_ARRIVATA_ORDINE_USER($id_ordine,_USER_ID),0)==0){
    echo rd4_go_back("Non hai ancora comprato nulla per questo ordine.");die;
}


if($aperto){
    $testo_aperto='<p class="alert alert-info margin-top-10"><strong>NB:</strong> Questo ordine è attualmente aperto. Se cancelli delle assegnazioni i totali dell\'ordine saranno aggiornati.</p>';
    $lucchetto = '<i class="fa fa-unlock text-success"></i>';
}else{
    $testo_aperto='<p class="alert alert-danger margin-top-10"><strong>ATTENZIONE: </strong> Questo ordine NON è aperto, pertanto non puoi modificare i totali delle righe di dettaglio. Se cancelli l\'assegnazione di un amico, le sue quantità verranno trasferite a te.</p>';
    $lucchetto = '<i class="fa fa-lock text-danger"></i>';
}




$sql = "SELECT art_codice, art_desc, art_um, qta_ord, qta_arr, prz_dett_arr, prz_dett, descrizione_ditta, id_dettaglio_ordini FROM retegas_dettaglio_ordini WHERE id_ordine=:id_ordine AND id_utenti='"._USER_ID."' ORDER BY id_ditta DESC";
$stmt = $db->prepare($sql);
$stmt->bindParam(':id_ordine', $O->id_ordini, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($rows AS $row){

    $sqlA = "SELECT * FROM retegas_distribuzione_spesa  WHERE id_riga_dettaglio_ordine=".$row["id_dettaglio_ordini"].";";
    $stmtA = $db->prepare($sqlA);
    $stmtA->execute();
    $rowsA = $stmtA->fetchAll(PDO::FETCH_ASSOC);
    $h.='<table class="table table-condensed table-bordered">';
        $h.='<thead>';
        $h.='<tr>';
            $h.='<td>Riga <strong>'.$row["id_dettaglio_ordini"].'</strong></td>';
            $h.='<td>'.$row["art_codice"].' - '.$row["art_desc"].' (Eu. <span class="prezzo_dettaglio" data-id_dettaglio="'.$row["id_dettaglio_ordini"].'">'.round($row["prz_dett_arr"],2).'</span> per '.$row["art_um"].')</td>';
            $h.='<td>Chi</td>';
            $h.='<td>Quantità</td>';
            $h.='<td>Valore</td>';
            $h.='<td></td>';
        $h.='</tr>';
        $h.='</thead>';
        $h.='<tbody>';
    $somma_amico = 0;
    foreach($rowsA AS $rowA){
        if($rowA["id_amico"]==0){
            $mestesso= 'ME STESSO';
            $classmestesso='info';

            if($aperto){
                $op_1='<i class="fa fa-times delete_row_amico text-danger" data-id="'.$rowA["id_distribuzione"].'" data-id_dettaglio="'.$row["id_dettaglio_ordini"].'" style="cursor:pointer"></i>';
                $riga_V='Vamico';
                $riga_Q='Qamico';
            }else{
                $op_1='';
                $riga_V='Vmestesso';
                $riga_Q='Qmestesso';
            }


        }else{

            $sqlAM = "SELECT nome FROM retegas_amici WHERE id_amici=".$rowA["id_amico"].";";
            $stmtAM = $db->prepare($sqlAM);
            $stmtAM->execute();
            $rowAM = $stmtAM->fetch();

            $riga_V='Vamico';
            $riga_Q='Qamico';

            $mestesso= $rowAM["nome"];
            $classmestesso='';
            $op_1='<i class="fa fa-times delete_row_amico text-danger" data-id="'.$rowA["id_distribuzione"].'" data-id_dettaglio="'.$row["id_dettaglio_ordini"].'" style="cursor:pointer"></i>';
        }

        $h.='<tr class="'.$classmestesso.'">';
            $h.='<td colspan=2></td>';
            $h.='<td >'.$mestesso.'</td>';
            $h.='<td class="text-right"><span class="'.$riga_Q.'" data-id_dettaglio="'.$row["id_dettaglio_ordini"].'" data-id="'.$rowA["id_distribuzione"].'">'.round($rowA["qta_arr"],2).'</span></td>';
            $h.='<td class="text-right">Eu. <span class="'.$riga_V.'" data-id_dettaglio="'.$row["id_dettaglio_ordini"].'" data-id="'.$rowA["id_distribuzione"].'">'.round($rowA["qta_arr"]*$row["prz_dett_arr"],2).'</span></td>';
            $h.='<td class="text-right">'.$op_1.'</td>';
        $h.='</tr>';
        $somma_amico += ($rowA["qta_arr"]*$row["prz_dett_arr"]);
    }
    $h.='</tbody>';
    $h.='<tfoot>';
        $h.='<tr>';
            $h.='<th colspan=2></th>';
            $h.='<th>Totale:</th>';
            $h.='<th class="text-right"><span class="totale_dettaglio" data-id_dettaglio="'.$row["id_dettaglio_ordini"].'">'.round($row["qta_arr"],2).'</span></th>';
            $h.='<th class="text-right">Eu. <span class="valore_dettaglio" data-id_dettaglio="'.$row["id_dettaglio_ordini"].'">'.round($somma_amico,2).'</span></th>';
            $h.='<th class="text-right">'.$lucchetto.'</th>';
        $h.='</tr>';
    $h.='</tfoot';

    $h.='</table><hr>';

}

?>

<?php echo $O->navbar_ordine(); ?>

<?php
    echo $testo_aperto;
?>

<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
        </article>

    </div>
</section>

<h1>GESTISCI SPESA AMICI</h1>
<div class="table-responsive">
    <?php echo $h;?>
</div>

<script type="text/javascript">
    pageSetUp();


    var pagefunction = function() {

        //-------------------------HELP
        <?php echo help_render_js($page_id); ?>
        //-------------------------HELP

        $(document).off("click",'.delete_row_amico');
        $(document).on('click','.delete_row_amico',function(e){
            var $t = $(this);
            var id_riga = $t.data('id');
            var id_dettaglio = $t.data('id_dettaglio');

            var totale_dettaglio = $('.totale_dettaglio[data-id_dettaglio='+id_dettaglio+']').html();
            var prezzo_dettaglio = $('.prezzo_dettaglio[data-id_dettaglio='+id_dettaglio+']').html();

            var valore_amico = $('.Vamico[data-id='+id_riga+']').html();
            var valore_mestesso = $('.Vmestesso[data-id_dettaglio='+id_dettaglio+']').html();

            var qta_amico = $('.Qamico[data-id='+id_riga+']').html();
            var qta_mestesso =  $('.Qmestesso[data-id_dettaglio='+id_dettaglio+']').html();

            console.log("Del riga " + id_riga + " dettaglio " + id_dettaglio + " valore amico: " + valore_amico + " Qta amico: "+ qta_amico + " Qta_me stesso: " + qta_mestesso + " Valore me stesso: " + valore_mestesso  );


            $.ajax({
                  type: "POST",
                  url: "ajax_rd4/ordini/_act_compra.php",
                  dataType: 'json',
                  data: {act: "delete_riga_amico",id_distribuzione:id_riga,id_dettaglio:id_dettaglio},
                  context: document.body
                }).done(function(data) {
                    if(data.result=="OK"){
                        ok(data.msg);

                        <?php if($aperto){ ?>
                            var nuovo_totale_dettaglio = math.subtract(totale_dettaglio,qta_amico);
                            var nuovo_valore_dettaglio = math.round(math.multiply(nuovo_totale_dettaglio,prezzo_dettaglio ),2);

                            $('.totale_dettaglio[data-id_dettaglio='+id_dettaglio+']').html(nuovo_totale_dettaglio);
                            $('.valore_dettaglio[data-id_dettaglio='+id_dettaglio+']').html(nuovo_valore_dettaglio);
                        <?php }else{ ?>
                            var nuovo_valore_mestesso = math.add(valore_amico,valore_mestesso);
                            var nuovo_qta_mestesso = math.add(qta_amico,qta_mestesso);

                            $('.Vmestesso[data-id_dettaglio='+id_dettaglio+']').html(nuovo_valore_mestesso);
                            $('.Qmestesso[data-id_dettaglio='+id_dettaglio+']').html(nuovo_qta_mestesso);
                        <?php } ?>

                        $t.closest('tr').fadeOut().empty();

                    }else{
                        ko(data.msg);
                    }

                });








        })


    }
    // end pagefunction

    loadScript("js/plugin/jquery-form/jquery-form.min.js",
        loadScript("js_rd4/plugin/math/math.js",
            pagefunction()
        )
    );



</script>
