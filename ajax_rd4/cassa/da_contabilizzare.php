<?php
    require_once("inc/init.php");
    require_once("../../lib_rd4/class.rd4.user.php");

    $ui = new SmartUI;
    $page_title = "Movimenti da contabilizzare";
    $page_id = "da_contabilizzare";

    if(!(_USER_PERMISSIONS & perm::puo_gestire_la_cassa)){
        echo "Non hai i permessi per gestire la cassa.";
    }







  //-------------------------ORDINO

?>

<h1>Movimenti da contabilizzare:</h1>
<hr>
<?php echo h; ?>


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

    }
    // end pagefunction

    pagefunction();



</script>