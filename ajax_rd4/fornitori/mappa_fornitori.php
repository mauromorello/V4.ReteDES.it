<?php
require_once("inc/init.php");
$ui = new SmartUI;
$converter = new Encryption;

$page_title = "Mappa fornitori";
$page_id = "mappa_fornitori";


$title_navbar='Mappa dei fornitori di reteDES';


?>

<h1>Mappa dei fornitori inseriti su reteDES.</h1>
<p>Usare il tasto "filtro" per visualizzare solo quelli con i tag attivi</p>

<section class="margin-top-10">
    
    <div class="margin-top-10">
            <iframe src="<?php echo APP_URL; ?>/geo/geo_ditte.php" seamless='seamless' width="100%" height="500px" frameBorder="0"></iframe>
    </div>
    
    
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
        </article>

    </div>    
</section>
<!-- Dynamic Modal -->
<div class="modal fade" id="remoteModal" tabindex="-1" role="dialog" aria-labelledby="remoteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- content will be filled here from "ajax/modal-content/model-content-1.html" -->
        </div>
    </div>
</div>
                        <!-- /.modal -->

<script type="text/javascript">

    pageSetUp();



    var pagefunction = function(){
        //-------------------------HELP
        <?php echo help_render_js($page_id); ?>
        //-------------------------HELP

    
    } // end pagefunction



    pagefunction();
</script>
