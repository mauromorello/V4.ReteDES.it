<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.ordine.php");
require_once("../../lib_rd4/class.rd4.cassa.php");


$ui = new SmartUI;
$page_title= "Riepilogo Cassa";
$page_id="riepilogo_cassa";

if(!(_USER_PERMISSIONS & perm::puo_gestire_la_cassa)){
    echo rd4_go_back("Non ho i permessi per la cassa");die;
}

$id_gas = _USER_ID_GAS;
$C = new cassa($id_gas);

$query = "SELECT  (
                COALESCE((SELECT SUM(importo) FROM retegas_cassa_utenti WHERE id_gas=:id_gas AND segno='+' AND registrato='si' ),0)
                -
                COALESCE((SELECT SUM(importo) FROM retegas_cassa_utenti WHERE id_gas=:id_gas AND segno='-' AND registrato='si' ),0)
                )  As risultato";
$stmt = $db->prepare($query);
$stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
$stmt->execute();
$rowTC = $stmt->fetch(PDO::FETCH_ASSOC);
$tc = $rowTC["risultato"];
$totale_gas_registrato = _NF($rowTC["risultato"]);

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
$totale_gas_non_registrato = _NF($rowTC["risultato"]);
$totale_disponibile = _NF($tc + $rowTC["risultato"]);


$movimenti_non_registrati = $C->get_movimenti_da_registrare();

?>
<div class="jumbotron">
<h1>Saldo GAS: <strong class="text-info"><?php echo $totale_gas_registrato; ?> €</strong></h1>
<h1>Ordini attivi: <strong class="text-danger"><?php echo $totale_gas_non_registrato; ?> € <small></small></strong></h1>
<h1>Disponibile: <strong class="text-success"><?php echo $totale_disponibile; ?> € <small></small></strong></h1>
<h2>Movimenti <span class="text-danger">NON</span> registrati di ordini convalidati: <strong><?php echo $movimenti_non_registrati; ?></strong><a class="btn btn-default pull-right" href="#ajax_rd4/cassa/da_registrare.php">REGISTRA</a></h2>
</div>

<div class="row margin-top-10">
    <div class="col-md-4">
        <div class="well text-center"><a href="#ajax_rd4/cassa/saldo_utenti.php" class="btn btn-lg btn-primary btn-block"><i class="fa fa-2x fa-euro pull-left"></i><p></p>SALDI</a></div>
        <div class="well text-center"><a href="#ajax_rd4/cassa/opzioni.php" class="btn btn-lg btn-primary btn-block"><i class="fa fa-2x fa-list-alt pull-left"></i><p></p>OPZIONI</a></div>
        <div class="well well-light">
            <form class="smart-form txt-color-blueDark" id="allinea_ordine">
                <section>
                    <label class="label "><strong>Allinea</strong> un ordine specifico:</label>
                    <label class="input-append">
                        <input type="text" class="input-sm" name="id_allinea_ordine" id="id_allinea_ordine" placeholder="ID ordine">
                        <button type="submit" class="btn btn-circle btn-primary" id="go_allinea_ordine"><i class="fa fa-check"></i></button>
                    </label>
                </section>
            </form>
        </div>

    </div>
    <div class="col-md-4">
        <div class="well text-center"><a href="#ajax_rd4/cassa/richieste.php" class="btn btn-lg btn-primary btn-block"><i class="fa fa-2x fa-plus-square pull-left"></i><p></p>CARICHI</a></div>
        <div class="well text-center"><a href="#ajax_rd4/cassa/movimenti.php" class="btn btn-lg btn-primary btn-block"><i class="fa fa-2x fa-table pull-left"></i><p></p>MOVIMENTI</a></div>
        <div class="well text-center"><a href="#ajax_rd4/cassa/operazioni.php" class="btn btn-lg btn-primary btn-block"><i class="fa fa-2x fa-pencil pull-left"></i><p></p>OPERAZIONI</a></div>
     </div>
     <div class="col-md-4">
        <div class="well text-center"><a href="#ajax_rd4/cassa/allineamento_ordini.php" class="btn btn-lg btn-primary btn-block"><i class="fa fa-2x fa-balance-scale pull-left"></i><p></p>ALLINEA</a></div>
        <div class="well text-center"><a href="#ajax_rd4/cassa/da_registrare.php" class="btn btn-lg btn-primary btn-block" ><i class="fa fa-2x fa-check-circle pull-left"></i><p></p>REGISTRA</a></div>
        <div class="well text-center"><a href="#ajax_rd4/cassa/ordini_gas.php" class="btn btn-lg btn-primary btn-block" ><i class="fa fa-2x fa-eye pull-left"></i><p></p>VEDI</a></div>
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
    pageSetUp();

    var pagefunction = function() {

        //-------------------------HELP
        <?php echo help_render_js("riepilogo_cassa"); ?>
        //-------------------------HELP
       $('#go_allinea_ordine').click(function(e){
           e.preventDefault();
           loadURL('<?php echo APP_URL; ?>/ajax_rd4/ordini/cassa.php?id='+$('#id_allinea_ordine').val(), $('#content'));
       });

    }
    // end pagefunction

    pagefunction();



</script>
