<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.ordine.php");

$ui = new SmartUI;
$converter = new Encryption;

$id_ordine = CAST_TO_INT($_POST["id"],0);
if ($id_ordine==0){
    $id_ordine = CAST_TO_INT($_GET["id"],0);
}

if($id_ordine==0){echo rd4_go_back("KO!");die;}
$O = new ordine($id_ordine);
$page_title = "Rettifiche";

if($O->codice_stato=="CO"){
    //echo rd4_go_back("Ordine già convalidato");
    //die;
    $disabled=' disabled="disabled" ';
}

?>
<?php echo $O->navbar_ordine(); ?>
<div class="alert alert-info">Scegli qua sotto il tipo di rettifica più adatta. Troverai in fondo ad ogni pagina la spiegazione di come funziona il meccanismo.</div>
<p></p>

<div class="row">
    <div class="col col-md-4">
        <div class="well well-sm well-light">
            <a <?php echo $disabled; ?> class="btn btn-block btn-primary font-lg" href="#ajax_rd4/rettifiche/totale.php?id=<?php echo $id_ordine ?>">Totale ordine</a><br>
            <p class="">Immetti soltanto la cifra del totale ordine (quella che hai pagato al fornitore).</p>
        </div>
    </div>
    <div class="col col-md-4">
        <div class="well well-sm well-light">
            <a <?php echo $disabled; ?> class="btn btn-block btn-primary font-lg" href="#ajax_rd4/rettifiche/utenti.php?id=<?php echo $id_ordine ?>">Totale utente</a><br>
            <p class="">Inserisci la cifra complessiva che ogni utente ha speso.</p>
        </div>
    </div>
    <div class="col col-md-4">
        <div class="well well-sm well-light">
            <a <?php echo $disabled; ?> class="btn btn-block btn-primary font-lg" href="#ajax_rd4/rettifiche/dettaglio.php?id=<?php echo $id_ordine ?>">Dettaglio</a><br>
            <p class="">Controlla ed eventualmente rettifica tutte le singole voci che compongono la spesa di ogni utente. In questa pagina è possibile aggiungere articoli agli utenti.</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col col-md-4">
        <div class="well well-sm well-light">
            <a <?php echo $disabled; ?> class="btn btn-block btn-primary" href="#ajax_rd4/rettifiche/articoli.php?id=<?php echo $id_ordine ?>">Articoli &amp; Scatole</a><br>
            <p class="">Rettifica  le quantità di articoli arrivate, escludi le scatole non piene oppure elimina articoli specifici dall'ordine.</p>
        </div>
    </div>
    <div class="col col-md-4">
        <div class="well well-sm well-light">
            <a class="btn btn-block btn-primary" href="#ajax_rd4/rettifiche/altro.php?id=<?php echo $id_ordine ?>">Aggiunte &amp; detrazioni</a><br>
            <p class="">Inserisci qua le spese di trasporto e gestione, o ogni altro tipo di importo assoluto.</p>
        </div>
    </div>
    <div class="col col-md-4">
        <div class="well well-sm well-light">
            <a class="btn btn-block btn-primary" href="#ajax_rd4/rettifiche/sconti.php?id=<?php echo $id_ordine ?>">Sconti &amp; Maggiorazioni</a><br>
            <p class="">Varia gli importi dovuti dagli utenti applicando sconti o maggiorazioni.</p>
        </div>
    </div>
</div>


<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html('rettifiche_home',$page_title); ?>
        </article>
    </div>
</section>

<script type="text/javascript">

    pageSetUp();



    var pagefunction = function(){
        //------------HELP WIDGET
        <?php echo help_render_js('rettifiche_home');?>
        //------------END HELP WIDGET

    } // end pagefunction

pagefunction();
</script>
