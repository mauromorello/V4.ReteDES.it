<?php
require_once("inc/init.php");


$ui = new SmartUI;
$page_title= "Cose(in)utili";
$page_id="coseinutili";

$lat = _USER_GAS_LAT;
$lng = _USER_GAS_LNG;

$llz = $lat.",".$lng.",10";
$h = "<div class=\"rg_widget rg_widget_helper\"><iframe frameborder=0 src=\"http://www.coseinutili.it/geo/maps/index.php?llz=$llz\" style=\"width:800px;height:680px;\"></iframe></div>";


?>
<a href="http://www.coseinutili.it" target="_BLANK"><img class="center-block margin-top-10" SRC="http://www.coseinutili.it/images/common/logo2.png"></a>
<div class="jumbotron">
    <h2><strong></strong>Cos’è cose(in)utili?</strong><br>
È un sito di baratto, che ti permette di pubblicare annunci per lo scambio di oggetti o di prestazioni di tempo.
È una forma “evoluta” di baratto, il baratto asincrono: non è necessario che lo scambio sia diretto (tu dai una cosa a me e io ne do una a te), ma può essere effettuato con qualunque membro della comunità, perché è regolato da un sistema di crediti.
Il baratto diretto viene accettato, ma solo per annunci specifici e solo se indicato nella descrizione dell'annuncio..<br>
da ReteDES puoi iscriverti, visualizzare oggetti e tempo vicini al tuo gas e collegare gli account: userai la stessa username e password per accedere ai due siti.</h2>
</div>
<div id="box_oggetti" class="well well-sm table-responsive">
    <?php echo $h?>
</div>

<div class="row margin-top-10">
    <div class="col-md-4">
        <div class="well text-center"><a href="" class="btn btn-lg btn-primary btn-block"><i class="fa fa-2x fa-cubes pull-left"></i><p></p>OGGETTI</a></div>
    </div>
    <div class="col-md-4">
        <div class="well text-center"><a href="" class="btn btn-lg btn-primary btn-block"><i class="fa fa-2x fa-clock-o pull-left"></i><p></p>TEMPO</a></div>
     </div>
     <div class="col-md-4">
        <div class="well text-center"><a href="" class="btn btn-lg btn-primary btn-block"><i class="fa fa-2x fa-plus-circle pull-left"></i><p></p>ISCRIVITI</a></div>
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
        <?php echo help_render_js($page_id); ?>
        //-------------------------HELP
       

    }
    // end pagefunction

    pagefunction();



</script>

