<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.ordine.php");

$ui = new SmartUI;
$page_title= "Report ordine";
//CONTROLLI
$id_ordine = CAST_TO_INT($_GET["id"],0);
$O = new ordine($id_ordine);

if (posso_gestire_ordine($id_ordine)){
    $gestore=true;
}else{
    $gestore=false;
}

$stmt = $db->prepare("SELECT    O.id_ordini,
                                O.id_listini,
                                O.descrizione_ordini,
                                O.note_ordini,
                                O.is_printable,
                                O.costo_gestione,
                                O.costo_trasporto,
                                O.mail_level,
                                DATE_FORMAT(O.data_apertura,'%d/%m/%Y %H:%i') as data_apertura,
                                DATE_FORMAT(O.data_chiusura,'%d/%m/%Y %H:%i') as data_chiusura,
                                O.id_stato,
                                U.fullname,
                                L.descrizione_listini
                        FROM retegas_ordini O
                            inner join maaking_users U on U.userid=O.id_utente
                            inner join retegas_listini L on L.id_listini=O.id_listini
                        WHERE id_ordini=:id LIMIT 1;");
$stmt->bindValue(':id', $id_ordine, PDO::PARAM_INT);
$stmt->execute();
$rowo = $stmt->fetch(PDO::FETCH_ASSOC);


$la_mia_spesa ='<div class="well text-center"><a href="#ajax_rd4/reports/la_mia_spesa.php?id='.$id_ordine.'" class="btn btn-md btn-warning btn-block font-md">LA MIA SPESA</a><br><span class="font-xs">...visualizza quello che hai ordinato di questo ordine.</span></div>';
$dettaglio_amici ='<div class="well text-center"><a href="#ajax_rd4/rettifiche/start.php?id='.$id_ordine.'" class="btn btn-md btn-warning btn-block font-md disabled">DETTAGLIO AMICI</a><br><span class="font-xs">...visualizza quello che hai ordinato diviso per amico</span></div>';
$articoli_gas ='<div class="well text-center"><a href="#ajax_rd4/rettifiche/start.php?id='.$id_ordine.'" class="btn btn-md btn-info btn-block font-md disabled">ARTICOLI GAS</a><br><span class="font-xs">...visualizza gli articoli che ha acquistato il tuo GAS</span></div>';
$dettaglio_utenti_gas ='<div class="well text-center"><a href="#ajax_rd4/reports/utenti_gas.php?id='.$id_ordine.'" class="btn btn-md btn-info btn-block font-md">UTENTI GAS</a><br><span class="font-xs">...visualizza quello che ha acquistato il tuo GAS diviso per utente</span></div>';
$articoli_raggruppati ='<div class="well text-center"><a href="#ajax_rd4/reports/articoli_raggruppati.php?id='.$id_ordine.'" class="btn btn-md btn-success btn-block font-md">ARTICOLI</a><br><span class="font-xs">...visualizza l\'ordine completo raggruppato per articolo, segnalando le scatole piene e gli avanzi.</span></div>';
$distribuzione ='<div class="well text-center"><a href="#ajax_rd4/reports/distribuzione.php?id='.$id_ordine.'" class="btn btn-md btn-success btn-block font-md">DISTRIBUZIONE</a><br><span class="font-xs">...per ogni articolo, vedi il dettaglio di chi l\'ha messo in ordine.</span></div>';


?>

<?php echo $O->navbar_ordine(); ?>
<p></p>
<h1 class="font-xl">Se hai acquistato articoli in questo ordine</h1>
<div class="row margin-top-10">
    <div class="col-md-4">
        <?php echo $la_mia_spesa; ?>
    </div>
    <div class="col-md-4">
        <?php echo $dettaglio_amici; ?>
    </div>
    <div class="col-md-4">
    </div>
</div>
<h1 class="font-xl">Cosa ha acquistato il tuo GAS</h1>
<div class="row margin-top-10">
    <div class="col-md-4">
        <?php echo $articoli_gas; ?>
    </div>
    <div class="col-md-4">
        <?php echo $dettaglio_utenti_gas; ?>
    </div>
    <div class="col-md-4">
    </div>
</div>
<h1 class="font-xl">Per ordinare la merce al fornitore.</h1>
<div class="row margin-top-10">
    <div class="col-md-4">
        <?php echo $articoli_raggruppati; ?>
    </div>
    <div class="col-md-4">
    </div>
    <div class="col-md-4">
    </div>
</div>
<h1 class="font-xl">Per distribuire la merce</h1>
<div class="row margin-top-10">
    <div class="col-md-4">
        <?php echo $distribuzione; ?>
    </div>
    <div class="col-md-4">
    </div>
    <div class="col-md-4">
    </div>
</div>

<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html("report_ordine",$page_title); ?>
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
        <?php echo help_render_js("report_ordine"); ?>
        //-------------------------HELP



    }
    // end pagefunction

    pagefunction();



</script>
