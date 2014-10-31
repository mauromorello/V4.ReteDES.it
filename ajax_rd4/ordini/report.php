<?php require_once("inc/init.php");
$ui = new SmartUI;
$page_title= "Report ordine";
//CONTROLLI
$id_ordine = CAST_TO_INT($_GET["id"],0);

if (posso_gestire_ordine($id_ordine)){
    $gestore=true;
}else{
    $gestore=false;
}
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

$u = '<div class="margin-top-10">
                <label>Mostra gli articoli acquistati in questo ordine</label><br>
                <div class="btn-group btn-group-justified">
                    <a class="btn btn-default btn-md" href="javascript:void(0)">LA MIA SPESA</a>
                </div>
      </div>
      <div class="margin-top-10">
                <label>Mostra gli articoli acquistati in questo ordine, divisi per amico</label><br>
                <div class="btn-group btn-group-justified">
                    <a class="btn btn-default btn-md" href="javascript:void(0)">DETTAGLIO AMICI</a>
                </div>
      </div>
      <div class="margin-top-10">
                <label>Mostra solo i totali, ma divisi per amico</label><br>
                <div class="btn-group btn-group-justified">
                    <a class="btn btn-default btn-md" href="javascript:void(0)">TOTALE AMICI</a>
                </div>
      </div>';


$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>true,
                    "deletebutton"=>false,
                    "colorbutton"=>true);
$wg_report_utente = $ui->create_widget($options);
$wg_report_utente->id = "wg_report_utente";
$wg_report_utente->body = array("content" => $u,"class" => "");
$wg_report_utente->header = array(
    "title" => '<h2>Utente</h2>',
    "icon" => 'fa fa-user'
);

$g = 'Gas';


$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>true,
                    "deletebutton"=>false,
                    "colorbutton"=>true);
$wg_report_gas = $ui->create_widget($options);
$wg_report_gas->id = "wg_report_gas";
$wg_report_gas->body = array("content" => $g,"class" => "");
$wg_report_gas->header = array(
    "title" => '<h2>GAS</h2>',
    "icon" => 'fa fa-home'
);

$d = '<div class="margin-top-10">
                <label>Stampa gli articoli raggruppati per il fornitore.</label><br>
                <div class="btn-group btn-group-justified">
                    <a class="btn btn-default btn-md" href="javascript:void(0)">ARTICOLI (COMPLETA)</a>
                </div>
      </div>
      <div class="margin-top-10">
                <label>Visualizza gli articoli raggruppati per il fornitore, semplificato, adatto ad essere esportato facilmente.</label><br>
                <div class="btn-group btn-group-justified">
                    <a class="btn btn-default btn-md" href="javascript:void(0)">ARTICOLI (SEMPLICE)</a>
                </div>
      </div>
      ';


$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>true,
                    "deletebutton"=>false,
                    "colorbutton"=>true);
$wg_report_ditta = $ui->create_widget($options);
$wg_report_ditta->id = "wg_report_ditta";
$wg_report_ditta->body = array("content" => $d,"class" => "");
$wg_report_ditta->header = array(
    "title" => '<h2>Ditta</h2>',
    "icon" => 'fa fa-truck'
);

$ge = '<div class="margin-top-10">
                <label>Vedi il dettaglio raggruppato per utente, utile per dividere la merce arrivata utente per utente</label><br>
                <div class="btn-group btn-group-justified">
                    <a class="btn btn-default btn-md" href="javascript:void(0)">DETTAGLIO UTENTI</a>
                </div>
      </div>
      <div class="margin-top-10">
                <label>Visualizza gli articoli raggruppati per GAS, utile per dividere la merce arrivata per GAS</label><br>
                <div class="btn-group btn-group-justified">
                    <a class="btn btn-default btn-md" href="javascript:void(0)">RAGGRUPPA PER GAS</a>
                </div>
      </div>
      <div class="margin-top-10">
                <label>Visualizza il dettaglio raggruppato per articolo, utile per dividere la merce arrivata.</label><br>
                <div class="btn-group btn-group-justified">
                    <a class="btn btn-default btn-md" href="javascript:void(0)">RAGGRUPPA PER ARTICOLO</a>
                </div>
      </div>
      ';


$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>true,
                    "deletebutton"=>false,
                    "colorbutton"=>true);
$wg_report_gestore = $ui->create_widget($options);
$wg_report_gestore->id = "wg_report_gestore";
$wg_report_gestore->body = array("content" => $ge,"class" => "");
$wg_report_gestore->header = array(
    "title" => '<h2>GESTORE</h2>',
    "icon" => 'fa fa-gear'
);


?>

<?php echo navbar_ordine($id_ordine); ?>

<section id="widget-grid" class="margin-top-10">



    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <?php echo $wg_report_utente->print_html(); ?>

            <?php echo $wg_report_gestore->print_html(); ?>
        </article>
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">

            <?php echo help_render_html("report_ordine",$page_title); ?>
            <?php echo $wg_report_ditta->print_html(); ?>
            <?php echo $wg_report_gas->print_html(); ?>
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
        <?php echo help_render_js("report_ordine"); ?>
        //-------------------------HELP



    }
    // end pagefunction

    pagefunction();



</script>
