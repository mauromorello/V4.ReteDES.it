<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.ordine.php");

$ui = new SmartUI;
$page_title= "Report ordine";
//CONTROLLI
$id_ordine = CAST_TO_INT($_GET["id"],0);
$O = new ordine($id_ordine);

//GAS POTENZIALE PARTECIPANTE
$ok=false;
$rows = $O->lista_gas_potenziali_partecipanti();
foreach($rows as $row){
    if($row["id_gas"]==_USER_ID_GAS){
        $ok=true;
    }
}
if(!$ok){
    echo rd4_go_back("Questo ordine non è condiviso con il tuo GAS.");
    die();
}
//GAS POTENZIALE PARTECIPANTE

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
$dettaglio_amici ='<div class="well text-center"><a href="#ajax_rd4/reports/dettaglio_distribuzione.php?id='.$id_ordine.'&a=all" class="btn btn-md btn-warning btn-block font-md">DETTAGLIO AMICI</a><br><span class="font-xs">...visualizza quello che hai ordinato diviso per amico</span></div>';

if($gestore | _USER_GAS_VISIONE_CONDIVISA){
    $dettaglio_utenti_gas ='<div class="well text-center"><a href="#ajax_rd4/reports/utenti_gas.php?id='.$id_ordine.'" class="btn btn-md btn-info btn-block font-md">UTENTI GAS</a><br><span class="font-xs">...visualizza quello che ha acquistato il tuo GAS diviso per utente</span></div>';
    $articoli_raggruppati ='<div class="well text-center"><a href="#ajax_rd4/reports/articoli_raggruppati.php?id='.$id_ordine.'" class="btn btn-md btn-success btn-block font-md">ARTICOLI</a><br><span class="font-xs">...visualizza l\'ordine completo raggruppato per articolo, segnalando le scatole piene e gli avanzi.</span></div>';
    $distribuzione ='<div class="well text-center"><a href="#ajax_rd4/reports/distribuzione.php?id='.$id_ordine.'" class="btn btn-md btn-success btn-block font-md">DISTRIBUZIONE</a><br><span class="font-xs">...per ogni articolo, vedi il dettaglio di chi l\'ha messo in ordine.</span></div>';
    
    $articoli_gas ='<div class="well text-center"><a href="#ajax_rd4/reports/articoli_raggruppati_gas.php?id='.$id_ordine.'" class="btn btn-md btn-info btn-block font-md">ARTICOLI GAS</a><br><span class="font-xs">...visualizza gli articoli che ha acquistato il tuo GAS</span></div>';
    $articoli_raggruppati_semplificata ='<div class="well text-center"><a href="#ajax_rd4/reports/articoli_semplificato.php?id='.$id_ordine.'" class="btn btn-md btn-success btn-block font-md">SEMPLIFICATO</a><br><span class="font-xs">prodotto - quantità ordinata - costo (prezzo*quantità)</span></div>';

    $title_gas = "Cosa ha acquistato il tuo GAS";
    $title_fornitore = "Per ordinare la merce al fornitore";
    $title_distribuzione ="Per distribuire la merce arrivata";
    $title_merce = "Per aiutare nella distribuzione";

    if($gestore){
        $title_multigas="Gestione ordine multiGas";
        $title_valore_multigas="Valori articoli multiGas";
        $title_multiditta="Gestione ordine multiDitta";
        $articoli_multigas ='<div class="well text-center"><a href="#ajax_rd4/reports/articoli_raggruppati_multigas.php?id='.$id_ordine.'" class="btn btn-md btn-danger btn-block font-md">ARTICOLI MULTIGAS</a><br><span class="font-xs">...visualizza gli articoli che hanno acquistato tutti i gas</span></div>';
        $articoli_multiditta ='<div class="well text-center"><a href="#ajax_rd4/reports/articoli_raggruppati_multiditta.php?id='.$id_ordine.'" class="btn btn-md btn-danger btn-block font-md">ARTICOLI MULTIDITTA</a><br><span class="font-xs">...visualizza gli articoli raggruppati per ditta</span></div>';
        $valore_multigas='<div class="well text-center"><a href="#ajax_rd4/reports/valori_raggruppati_multigas.php?id='.$id_ordine.'" class="btn btn-md btn-danger btn-block font-md">VALORE MULTIGAS</a><br><span class="font-xs">...visualizza il valore degli articoli raggruppati per tutti i gas</span></div>';

    }else{
        $title_multiditta="";
        $title_multigas="";
        $articoli_multigas ='';
        $articoli_multiditta ='';
        $title_valore_multigas='';
    }

}else{
    $dettaglio_utenti_gas = '';
    $articoli_raggruppati = '';
    $distribuzione =        '';
    $articoli_gas =         '';
    $articoli_raggruppati_semplificata ='';
    $articoli_multigas ='';
    $valore_multigas='';

    $title_gas='';
    $title_fornitore ='';
    $title_distribuzione ='';
    $title_multigas='';
    $title_merce='';
    $title_valore_multigas='';
}

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
<h1 class="font-xl"><?php echo $title_gas; ?></h1>
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
<h1 class="font-xl"><?php echo $title_fornitore; ?></h1>
<div class="row margin-top-10">
    <div class="col-md-4">
        <?php echo $articoli_raggruppati; ?>
    </div>
    <div class="col-md-4">
        <?php echo $articoli_raggruppati_semplificata; ?>
    </div>
    <div class="col-md-4">
    </div>
</div>
<h1 class="font-xl"><?php echo $title_merce; ?></h1>
<div class="row margin-top-10">
    <div class="col-md-4">
        <?php echo $distribuzione; ?>
    </div>
    <div class="col-md-4">
    </div>
    <div class="col-md-4">
    </div>
</div>
<h1 class="font-xl"><?php echo $title_multigas; ?></h1>
<div class="row margin-top-10">
    <div class="col-md-4">
        <?php echo $articoli_multigas; ?>
    </div>
    <div class="col-md-4">
        <?php echo $valore_multigas; ?>
    </div>
    <div class="col-md-4">
    </div>
</div>
<h1 class="font-xl"><?php echo $title_multiditta; ?></h1>
<div class="row margin-top-10">
    <div class="col-md-4">
        <?php echo $articoli_multiditta; ?>
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
