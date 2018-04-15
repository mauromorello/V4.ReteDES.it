<?php
require_once("inc/init.php");

$ui = new SmartUI;
$page_title = "Sviluppatori";


?>
<div class="inbox-nav-bar no-content-padding">
    <h1 class="page-title txt-color-blueDark"><i class="fa fa-fw fa-cogs"></i> Sviluppatori &nbsp;</h1>
</div>

<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html("help_developer_documentazione"); ?>
            <?php echo help_render_html("help_developer_pagina"); ?>
        </article>
    </div>
</section>


<script type="text/javascript">
    pageSetUp();

    var pagefunction = function(){

        //------------HELP WIDGET
        <?php echo help_render_js("help_developer_documentazione");?>
        <?php echo help_render_js("help_developer_pagina");?>
        //------------END HELP WIDGET




    };

    pagefunction();

</script>
