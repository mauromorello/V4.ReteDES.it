<?php
require_once("inc/init.php");


$ui = new SmartUI;
$page_title= "Cose(in)utili: Mappa";
$page_id="coseinutili_mappa";

$lat = _USER_GAS_LAT;
$lng = _USER_GAS_LNG;

$llz = $lat.",".$lng.",11";
$h = "<div class=\"rg_widget rg_widget_helper\"><iframe frameborder=0 src=\"http://www.coseinutili.it/geo/maps/index.php?llz=$llz\" style=\"width:800px;height:680px;\"></iframe></div>";


?>
<div class="well well-sm">
    <a href="http://www.coseinutili.it" target="_BLANK"><img class="center-block margin-top-10" SRC="http://www.coseinutili.it/images/common/logo2.png"></a>
    <h3>In questa mappa puoi vedere gli oggetti di cose(in)utili di utenti che abitano vicino al tuo GAS.</h3>
</div>

<div id="box_mappa" class="well well-sm table-responsive margin-top-10">
    <?php echo $h?>
</div>

<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
        </article>
    </div>
</section>
<script type="text/javascript">
    pageSetUp();

    var pagefunction = function() {

        //-------------------------HELP
        <?php echo help_render_js($page_id); ?>
        //-------------------------HELP
       $('#go_allinea_ordine').click(function(e){
           e.preventDefault();
           loadURL('<?php echo APP_URL; ?>/ajax_rd4/ordini/cassa.php?id='+$('#id_allinea_ordine').val(), $('#content'));
       });

    }
    // end pagefunction

    pagefunction();



</script>

