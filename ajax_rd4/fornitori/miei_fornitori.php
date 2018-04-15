<?php
require_once("inc/init.php");

$ui = new SmartUI;
$page_title = "mie Ditte";


$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>true,
                    "deletebutton"=>false,
                    "colorbutton"=>false);


$li = '<div id="statistica_mio_fornitore"></div>';
$wg_miei_forn_stat= $ui->create_widget($options);
$wg_miei_forn_stat->id = "wg_statistica_fornitore_mio";
$wg_miei_forn_stat->body = array("content" => $li ,"class" => "");
$wg_miei_forn_stat->header = array(
    "title" => '<h2>Statistiche</h2>',
    "icon" => 'fa fa-bar-chart-o'
);

$li = '<div id="listini_fornitore"></div>';
$wg_list_forn= $ui->create_widget($options);
$wg_list_forn->id = "wg_listini_fornitore_mio";
$wg_list_forn->body = array("content" => $li ,"class" => "no-padding");
$wg_list_forn->header = array(
    "title" => '<h2>Listini <small id="nomedittaperlistini"></small></h2>',
    "icon" => 'fa fa-table'
);



$li = '<div id="lista_miei_fornitori"></div>';
$wg_miei_forn = $ui->create_widget($options);
$wg_miei_forn->id = "wg_fornitori_miei";
$wg_miei_forn->body = array("content" => $li ,"class" => "no-padding");
$wg_miei_forn->header = array(
    "title" => '<h2>Fornitori inseriti da me</h2>',
    "icon" => 'fa fa-truck'
);

$c='         <div id="mio_fornitore_container">
                        <div class="alert alert-warning text-center">
                            <h5><i class="fa fa-truck fa-2x"></i>&nbsp;Clicca sul nome di una ditta per vedere qua i dettagli</h5>
                        </div>
            </div>
       ';

$wg_dett_forn = $ui->create_widget($options);
$wg_dett_forn->id = "wg_fornitori_miei_dettaglio";
$wg_dett_forn->body = array("content" => $c ,"class" => "");
$wg_dett_forn->header = array(
    "title" => '<h2>Anagrafica fornitore</h2>',
    "icon" => 'fa fa-pencil'
);


?>

<?php echo navbar('<i class="fa fa-2x fa-truck pull-left"></i> Mie Ditte<br>',$button); ?>
<section id="widget-grid" class="margin-top-10">

    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <?php echo help_render_html("mie_ditte",$page_title); ?>

        </article>
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <?php echo $wg_miei_forn->print_html(); ?>

        </article>

    </div>

</section>


<script type="text/javascript">

    pageSetUp();
    var geocoder;
    var map;

    var loadlist = function(){
    $.ajax({
              type: "POST",
              url: "ajax_rd4/fornitori/inc/lista_miei_fornitori.php",
              context: document.body
           }).done(function(data) {
              $('#lista_miei_fornitori').html(data);
           });
    }






    var pagefunction = function() {
        //-------------------------HELP
        document.title = '<?php echo "ReteDES.it :: $page_title";?>';
        <?php echo help_render_js("mie_ditte"); ?>
        //-------------------------HELP


        loadlist();

    };

    // end pagefunction

    // run pagefunction on load

    $(window).bind('gMapsLoaded',loadScript("js/plugin/x-editable/x-editable.min.js", pagefunction));



</script>
