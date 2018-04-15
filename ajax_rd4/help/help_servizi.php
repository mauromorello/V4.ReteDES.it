<?php
require_once("inc/init.php");

$ui = new SmartUI;
$page_title = "Servizi esterni";
$page_id = "help_servizi";

?>
<div class="jumbotron">
    <h1><i class="fa fa-globe"></i>&nbsp;Servizi esterni</h1>
    <p>Ogni gas è un mondo a sé. In questa pagina ci sono alcuni servizi esterni che potrebbero tornare utili ai gasisti per gestire tutto quello che con reteDES non si può.</p>
</div>
<div class="jumbotron">
    <p><strong>Gestione baratto e banca del tempo</strong></p>
    <h1><IMG class="img img-thumbnail" SRC="<?php echo ASSETS_URL; ?>/img_rd4/logo3.png"></h1>
    <p>Iscrivendosi a cose(in)utili si ha accesso ad una delle comunità di baratto e di banca del tempo più attive del panorama nazionale.</p>
    <p>Migliaia di oggetti a disposizione, che attendono una nuova vita, in cambio di tempo e/o altri oggetti, autoprodotti o che avrebbero fatto una "brutta fine".</p>
    <p>Visita <a href="http://www.coseinutili.it" target="_BLANK">cose(in)utili</a></p>
</div>
<div class="jumbotron">
    <p><strong>GAI: Gruppo Acquisto Ibrido</strong></p>
    <h1><IMG class="img img-thumbnail" SRC="http://www.gruppoacquistoibrido.it/wp-content/uploads/2014/05/gruppo-acquisto-ibrido.h79.png"></h1>
    <p>Il GAI Gruppo Acquisto Ibrido è un’associazione culturale che ha lo scopo di promuovere ed incentivare una mobilità più sostenibile attraverso la creazione ed il coordinamento di gruppi d’acquisto per veicoli a basso impatto ambientale.</p>
    <p>Visita <a href="http://www.gruppoacquistoibrido.it/" target="_BLANK">GAI </a></p>
</div>
<div class="jumbotron">
    <p><strong>Cohousing</strong></p>
    <h1><IMG class="img img-thumbnail" SRC="<?php echo APP_URL; ?>/img_rd4/abitaregea.png"></h1>
    <p>Abitare Gea facilita la creazione di comunità sostenibili per cohousing, ecovillaggi, cofarming e gruppi di acquisto solidale.</p>
    <p>Visita <a href="http://www.abitaregea.it" target="_BLANK">abitareGEA </a></p>
</div>


<!--
<section id="widget-grid" class="margin-top-10">
    <div class="row">

        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id); ?>
        </article>
    </div>
</section>
-->

<script type="text/javascript">
    pageSetUp();

    var pagefunction = function(){

        //------------HELP WIDGET
        <?php echo help_render_js($page_id);?>
        //------------END HELP WIDGET




    };

    pagefunction();

</script>
