<?php
require_once("inc/init.php");

$ui = new SmartUI;
$page_title = "Help GAS";


?>
<div class="inbox-nav-bar no-content-padding">
    <h1 class="page-title txt-color-blueDark"><i class="fa fa-fw fa-home"></i> Gestione GAS! &nbsp;</h1>
</div>

<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html("help_gas","Help GAS"); ?>
        </article>
    </div>
</section>
<div class="alert alert-info margin-bottom-15 margin-top-5">Qua sotto trovi i box di HELP presenti nelle pagine specifiche.</div>
<section id="widget-grid" class="margin-top-10">
<div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <?php echo help_render_html('gas_home',$page_title,true); ?>
        </article>
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        </article>

    </div>
</section>
<script type="text/javascript">
    pageSetUp();

    var pagefunction = function(){

        //------------HELP WIDGET
        <?php echo help_render_js("help_gas");?>
        <?php echo help_render_js("gas_home");?>
        //------------END HELP WIDGET




    };

    pagefunction();

</script>
