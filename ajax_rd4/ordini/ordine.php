<?php require_once("inc/init.php");
$ui = new SmartUI;
$page_title= "Scheda ordine";
//CONTROLLI
$id_ordine = CAST_TO_INT($_GET["id"],0);
$stmt = $db->prepare("SELECT    O.id_ordini,
                                O.id_listini,
                                O.descrizione_ordini,
                                O.note_ordini,
                                O.is_printable,
                                O.costo_gestione,
                                O.costo_trasporto,
                                O.mail_level,
                                DATE_FORMAT(O.data_apertura,'%d/%m/%Y %H:%i') as data_apertura,
                                DATE_FORMAT(O.data_chiusura,'%d/%m/%Y %H:%i') as data_chiusura,
                                O.id_stato,
                                U.fullname,
                                L.descrizione_listini
                        FROM retegas_ordini O
                            inner join maaking_users U on U.userid=O.id_utente
                            inner join retegas_listini L on L.id_listini=O.id_listini
                        WHERE id_ordini=:id LIMIT 1;");
$stmt->bindValue(':id', $id_ordine, PDO::PARAM_INT);
$stmt->execute();
$rowo = $stmt->fetch(PDO::FETCH_ASSOC);



$title_navbar='<i class="fa fa-newspaper-o  fa-2x pull-left"></i> '.$rowo["descrizione_ordini"].'<br><small class="note"> Ord. '.$rowo["id_ordini"].'</small>';
?>
<?php echo navbar_ordine($id_ordine); ?>


<div class="row margin-top-10">
    <div class="col-md-4">
        <div class="well text-center"><button class="btn btn-lg btn-primary btn-block disabled"><i class="fa fa-2x fa-shopping-cart pull-left"></i>COMPRA...</button><br><span class="font-xs"> ... scegliendo tra gli articoli disponibili.</span></div>
        <div class=" well text-center"><button class="btn btn-lg btn-primary btn-block  disabled"><i class="fa fa-2x fa-hand-o-right pull-left "></i>AIUTA..</button><br><span class="font-xs">... i referenti, a fare cosa lo decidi tu!</span></div>
    </div>
    <div class="col-md-8">
            <div class="row">
                <div class="col-md-6"><div class=" well text-center"><button class="btn btn-lg btn-primary btn-block disabled"><i class="fa fa-2x fa-envelope-square pull-left"></i>COMUNICA..</button><br><span class="font-xs">...con gli utenti coinvolti in questo ordine</span></div></div>
                <div class="col-md-6"><div class=" well text-center"><button class="btn btn-lg btn-primary btn-block disabled"><i class="fa fa-2x fa-eye pull-left"></i>CONTROLLA..</button><br><span class="font-xs">...la tua spesa e gli articoli acquistati</span></div></div>
            </div>
            <div class="row">
                <div class="col-md-6"><div class=" well text-center"><a href="#ajax_rd4/ordini/edit.php?id=<?php echo $id_ordine; ?>" class="btn btn-lg btn-primary btn-block font-lg"><i class="fa fa-2x fa-gears pull-left"></i><p></p>GESTISCI..</a><br><span class="font-xs">...tutto quello che riguarda questo ordine</span></div></div>
                <div class="col-md-6"><div class=" well text-center"><a href="#ajax_rd4/ordini/edit.php?id=<?php echo $id_ordine; ?>" class="btn btn-lg btn-primary btn-block font-lg"><i class="fa fa-2x fa-question pull-left"></i><p></p>ALTRO....</a><br><span class="font-xs">...tutto quello non c'è qua :)</span></div></div>
            </div>

    </div>
</div>


<?php // echo schedina_ordine($id_ordine);?>

<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html("scheda_ordine",$page_title); ?>
        </article>
    </div>
</section>
<script type="text/javascript">
    /* DO NOT REMOVE : GLOBAL FUNCTIONS!
     *
     * pageSetUp(); WILL CALL THE FOLLOWING FUNCTIONS
     *
     * // activate tooltips
     * $("[rel=tooltip]").tooltip();
     *
     * // activate popovers
     * $("[rel=popover]").popover();
     *
     * // activate popovers with hover states
     * $("[rel=popover-hover]").popover({ trigger: "hover" });
     *
     * // activate inline charts
     * runAllCharts();
     *
     * // setup widgets
     * setup_widgets_desktop();
     *
     * // run form elements
     * runAllForms();
     *
     ********************************
     *
     * pageSetUp() is needed whenever you load a page.
     * It initializes and checks for all basic elements of the page
     * and makes rendering easier.
     *
     */

    pageSetUp();


    var pagefunction = function() {

        //-------------------------HELP
        <?php echo help_render_js("scheda_ordine"); ?>
        //-------------------------HELP

        $('#go_gestisci').click(function(){
            container = $('#content');
            var $this   = $(this)
            var id = $this.data('id_ordine');
            loadURL('ajax_rd4/ordini/edit.php?id='+id,container);
        })

    }
    // end pagefunction

    pagefunction();



</script>
