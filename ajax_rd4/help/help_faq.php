<?php
require_once("inc/init.php");

$ui = new SmartUI;
$page_title = "FAQ: domande frequenti";
$page_id = "help_faq";

$faq_comeOrdinare = "faq_comeOrdinare";
$faq_comeAnagrafiche = "faq_comeAnagrafiche";
$faq_comeAprireOrdini = "faq_comeAprireOrdini";
$faq_comeGestireConCassa = "faq_comeGestireConCassa";
$faq_comeAprireGAS = "faq_comeAprireGAS";
$faq_comeGestireListino = "faq_comeGestireListino";
$faq_comeGestireListinoMD = "faq_comeGestireListinoMD";

?>
<div class="inbox-nav-bar no-content-padding">
    <h1 class="page-title txt-color-blueDark"><i class="fa fa-fw fa-question-circle"></i> Informazioni di base &nbsp;</h1>
</div>
<section  class="margin-top-10">
<div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html("help_inizio","Inizio"); ?>
        </article>
    </div>
</section>
<section  class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($faq_comeOrdinare,"sul come ordinare la merce",true); ?>
        </article>
    </div>
</section>
<section  class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($faq_comeAnagrafiche,"sul come gestire i propri dati",true); ?>
        </article>
    </div>
</section>
<section  class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($faq_comeAprireOrdini,"gestire ordine senza cassa",true); ?>
        </article>
    </div>
</section>
<section  class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($faq_comeGestireConCassa,"Gestire Ordine con cassa",true); ?>
        </article>
    </div>
</section>
<section  class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($faq_comeGestireListino,"Come creare e gestire un listino",true); ?>
        </article>
    </div>
</section>
<section class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($faq_comeGestireListinoMD,"Come creare e gestire un listino multiditta",true); ?>
        </article>
    </div>
</section>
<script type="text/javascript">
    pageSetUp();

    var pagefunction = function(){

        //------------HELP WIDGET
        <?php echo help_render_js("help_inizio");?>
        <?php echo help_render_js($faq_comeOrdinare);?>
        <?php echo help_render_js($faq_comeAnagrafiche);?>
        <?php echo help_render_js($faq_comeAprireOrdini);?>
        <?php echo help_render_js($faq_comeGestireConCassa);?>
        <?php echo help_render_js($faq_comeAprireGAS);?>
        <?php echo help_render_js($faq_comeGestireListino);?>
        <?php echo help_render_js($faq_comeGestireListinoMD);?>
        //------------END HELP WIDGET




    };

    pagefunction();

</script>
