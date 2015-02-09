<?php
require_once("inc/init.php");


$ui = new SmartUI;
$page_title= "Riepilogo Cassa";

$id_gas = _USER_ID_GAS;

$query = "SELECT  (
                COALESCE((SELECT SUM(importo) FROM retegas_cassa_utenti WHERE id_gas=:id_gas AND segno='+' AND contabilizzato='si' ),0)
                -
                COALESCE((SELECT SUM(importo) FROM retegas_cassa_utenti WHERE id_gas=:id_gas AND segno='-' AND contabilizzato='si' ),0)
                )  As risultato";
$stmt = $db->prepare($query);
$stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
$stmt->execute();
$rowTC = $stmt->fetch(PDO::FETCH_ASSOC);
$tc = $rowTC["risultato"];
$totale_gas_contabilizzato = number_format(round($rowTC["risultato"],4)+0,2,",","");

$query = "SELECT  (
                COALESCE((SELECT SUM(importo) FROM retegas_cassa_utenti WHERE id_gas=:id_gas AND segno='+' AND registrato='no' ),0)
                -
                COALESCE((SELECT SUM(importo) FROM retegas_cassa_utenti WHERE id_gas=:id_gas AND segno='-' AND registrato='no' ),0)
                )  As risultato";
$stmt = $db->prepare($query);
$stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
$stmt->execute();
$rowTC = $stmt->fetch(PDO::FETCH_ASSOC);
$tnc = $rowTC["risultato"];
$totale_gas_non_registrato = number_format(round($rowTC["risultato"],4)+0,2,",","");

$delta = round(($tnc/$tc) *100,2);

if(abs($delta)<100){$delta_color=" text-danger ";}
if(abs($delta)<50){$delta_color=" text-warning ";}
if(abs($delta)<1){$delta_color=" text-success ";}

$query = "SELECT count(*) as conto FROM retegas_cassa_utenti where id_gas=:id_gas AND registrato='no' ";
$stmt = $db->prepare($query);
$stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
$stmt->execute();
$rowMNR = $stmt->fetch(PDO::FETCH_ASSOC);
$movimenti_non_registrati = round($rowMNR["conto"],4)+0;

?>
<div class="alert alert-danger">ATTENZIONE: Le cifre di questa pagina NON sono vere, è ancora tutto in test</div>
<div class="jumbotron">
<h1>Saldo GAS: <strong><?php echo $totale_gas_contabilizzato; ?> €</strong></h1>
<h2>Disallineamento: <strong class="<?php echo $delta_color; ?>"><?php echo $delta; ?> %</strong></h2>
<h2>Movimenti <span class="text-danger">NON</span> registrati: <strong><?php echo $movimenti_non_registrati; ?></strong> (<?php echo $totale_gas_non_registrato ?> €)<a class="btn btn-default pull-right" href="#ajax_rd4/cassa/da_registrare.php">REGISTRA</a></h2>
</div>

<div class="row margin-top-10">
    <div class="col-md-4">
        <div class="well text-center"><a href="#ajax_rd4/ordini/report.php?id=<?php echo $id_ordine; ?>" class="btn btn-lg btn-primary btn-block" disabled="disabled"><i class="fa fa-2x fa-euro pull-left"></i><p></p>SALDO UTENTI</a></div>
        <div class="well text-center"><a href="#ajax_rd4/cassa/opzioni.php?" class="btn btn-lg btn-primary btn-block"><i class="fa fa-2x fa-check pull-left"></i><p></p>OPZIONI</a></div>
    </div>
    <div class="col-md-4">
        <div class="well text-center"><a href="#ajax_rd4/cassa/richieste.php" class="btn btn-lg btn-primary btn-block"><i class="fa fa-2x fa-plus-square pull-left"></i><p></p>RICHIESTE di CARICO</a></div>
        <div class="well text-center"><a href="#ajax_rd4/ordini/report.php?id=<?php echo $id_ordine; ?>" class="btn btn-lg btn-primary btn-block" disabled="disabled"><i class="fa fa-2x fa-table pull-left"></i><p></p>TUTTI i MOVIMENTI</a></div>
     </div>
     <div class="col-md-4">
        <div class="well text-center"><a href="#ajax_rd4/cassa/allineamento_ordini.php" class="btn btn-lg btn-primary btn-block"><i class="fa fa-2x fa-shopping-cart pull-left"></i><p></p>ORDINI</a></div>
        <div class="well text-center"><a href="#ajax_rd4/ordini/report.php?id=<?php echo $id_ordine; ?>" class="btn btn-lg btn-primary btn-block" disabled="disabled"><i class="fa fa-2x fa-envelope pull-left"></i><p></p>COMUNICA</a></div>
     </div>
</div>

<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html("riepilogo_cassa",$page_title); ?>
        </article>
    </div>
</section>
<script type="text/javascript">
    /* DO NOT REMOVE : GLOBAL FUNCTIONS!
     *
     * pageSetUp(); WILL CALL THE FOLLOWING FUNCTIONS
     *
     * // activate tooltips
     * $("[rel=tooltip]").tooltip();
     *
     * // activate popovers
     * $("[rel=popover]").popover();
     *
     * // activate popovers with hover states
     * $("[rel=popover-hover]").popover({ trigger: "hover" });
     *
     * // activate inline charts
     * runAllCharts();
     *
     * // setup widgets
     * setup_widgets_desktop();
     *
     * // run form elements
     * runAllForms();
     *
     ********************************
     *
     * pageSetUp() is needed whenever you load a page.
     * It initializes and checks for all basic elements of the page
     * and makes rendering easier.
     *
     */

    pageSetUp();


    var pagefunction = function() {

        //-------------------------HELP
        <?php echo help_render_js("riepilogo_cassa"); ?>
        //-------------------------HELP


    }
    // end pagefunction

    pagefunction();



</script>
