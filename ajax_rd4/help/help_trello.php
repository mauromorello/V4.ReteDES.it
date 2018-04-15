<?php
require_once("inc/init.php");

$ui = new SmartUI;
$page_title = "Trello";
$page_id = "help_trello";

?>
<div class="jumbotron">
    <h1><i class="fa fa-trello"></i>&nbsp;Trello</h1>
    <p>Trello è una "lavagna" virtuale dove aggiorno l'evoluzione di reteDES.<br>Si può consultare liberamente, e se si vuole partecipare alla discussione occorre solamente mandare comunicazione a info@retedes.it, e verrete aggiunti ai volenterosi che collaborano a questo progetto.</p>
    <p>Le schede che si vedono riguardano idee generiche, le cose da fare, errori segnalati; una volta gestite vengono spostate nelle colonne delle cose finite o sospese.</p>
    <p>Visita <a href="https://trello.com/b/yEdqynu1/retedes-it" target="_BLANK">TRELLO</a></p>

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
