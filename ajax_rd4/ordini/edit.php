<?php require_once("inc/init.php");
$ui = new SmartUI;
$page_title= "Gestisci Ordine";

//CONTROLLI
$id_ordine = (int)$_GET["id"];

if (!posso_gestire_ordine($id_ordine)){
    echo "Non ho i permessi per gestire questo ordine";
    die();
}

$stmt = $db->prepare("SELECT    O.id_ordini,
                                O.id_listini,
                                O.descrizione_ordini,
                                O.note_ordini,
                                O.is_printable,
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
$row = $stmt->fetch(PDO::FETCH_ASSOC);


$s =<<<SEMPLICE
<form>

    <fieldset>
        <section>
            <label for="descrizione_ordini">Titolo</label>
            <h3><i class="fa fa-pencil"></i>&nbsp;&nbsp;<span class="editable" id="descrizione_ordini" data-pk="{$row["id_ordini"]}">{$row["descrizione_ordini"]}</span></h3>
        </section>
        <hr>
        <section class="margin-top-10">
         <label for="note_ordini">Note</label>
         <div class="summer" id="note_ordini">{$row["note_ordini"]}</div>
         <button class="btn btn-success pull-right margin-top-10">Salva le note</button>
        </section>

    </fieldset>
</form>
SEMPLICE;

$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>false,
                    "deletebutton"=>false,
                    "colorbutton"=>true);
$wg_edit_ordine = $ui->create_widget($options);
$wg_edit_ordine->id = "wg_edit_ordine";
$wg_edit_ordine->body = array("content" => $s,"class" => "");
$wg_edit_ordine->header = array(
    "title" => '<h2>Titolo & note</h2>',
    "icon" => 'fa fa-list'
);

//ordine futuro ? si può cambiare la data di apertura
$data_apertura = strtotime(str_replace('/', '-', $row["data_apertura"]));
$data_now = strtotime(date("d-m-Y H:i"));

if($data_apertura>$data_now){
    $ed_ap="date_ordine";
    $ic_ap="fa-pencil";
    $btn_ap ='<button class="btn btn-success btn-xl">FAI PARTIRE L\'ORDINE SUBITO</button>';
}else{
    $ed_ap="";
    $ic_ap="fa-lock";
    $btn_ap ='<button class="btn btn-danger btn-xl">CHIUDI L\'ORDINE SUBITO</button>';
}



$d =<<<SEMPLICE
<form>

    <fieldset>
        <section>
            <label for="data_apertura">Data / ora apertura ordine</label><br>
            <i class="fa {$ic_ap} fa-2x"></i>&nbsp;&nbsp;<a class="font-xl {$ed_ap}" id="data_apertura" data-type="combodate" data-template="DD MM YYYY  HH : mm" data-format="DD/MM/YYYY HH:mm" data-viewformat="DD/MM/YYYY HH:mm" data-pk="{$row["id_ordini"]}" data-original-title="Seleziona da questo elenco:">{$row["data_apertura"]}</a>
        </section>
        <hr>
        <section class="margin-top-10">
            <label for="data_apertura" >Data / ora chiusura ordine</label><br>
            <i class="fa fa-pencil fa-2x"></i>&nbsp;&nbsp;<a class="font-xl date_ordine" id="data_chiusura" data-type="combodate" data-template="DD MM YYYY  HH : mm" data-format="DD/MM/YYYY HH:mm" data-viewformat="DD/MM/YYYY HH:mm" data-pk="{$row["id_ordini"]}" data-original-title="Seleziona da questo elenco:">{$row["data_chiusura"]}</a>
        </section>
        <hr>
        <section class="margin-top-10">
            <label>Operazioni eseguibili:</label><br>
            {$btn_ap}
        </section>
    </fieldset>
</form>
SEMPLICE;

$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>false,
                    "deletebutton"=>false,
                    "colorbutton"=>true);
$wg_edit_scadenze = $ui->create_widget($options);
$wg_edit_scadenze->id = "wg_edit_scadenze";
$wg_edit_scadenze->body = array("content" => $d,"class" => "");
$wg_edit_scadenze->header = array(
    "title" => '<h2>Date & scadenze</h2>',
    "icon" => 'fa fa-calendar'
);

?>

<div class="inbox-nav-bar no-content-padding">
    <h1 class="page-title txt-color-blueDark"><i class="fa fa-fw fa-cogs"></i> Gestisci ordine&nbsp;</h1>
</div>

<section id="widget-grid" class="margin-top-10">



    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <?php echo $wg_edit_ordine->print_html(); ?>
            <?php echo $wg_edit_scadenze->print_html(); ?>
        </article>
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">

            <?php echo help_render_html("edit_ordine",$page_title); ?>
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
        <?php echo help_render_js("edit_ordine"); ?>
        //-------------------------HELP

        $.fn.editable.defaults.url = 'ajax_rd4/ordini/_act.php';

        var editable = $('.editable').editable({
            ajaxOptions: { dataType: 'json' },
            success: function(response, newValue) {
                        console.log(response);
                        if(response.result == 'KO'){
                            return response.msg;
                        }
                    }
        });
         $('.date_ordine').editable({
                language: 'it',
                placement: 'center',
                combodate: {
                    minYear: 2013,
                    maxYear: 2022
                },
                ajaxOptions: { dataType: 'json' },
                success: function(response, newValue) {
                        console.log(response);
                        if(response.result == 'KO'){
                            return response.msg;
                        }
                    }
            });
        var summernote = $('.summer').summernote({
              toolbar: [
                //[groupname, [button list]]

                ['style', ['bold', 'italic', 'underline', 'clear']],

                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
              ]
            });

    }
    // end pagefunction

    loadScript("js/plugin/x-editable/moment.min.js", loadXEditable);

    function loadXEditable(){
        loadScript("js/plugin/x-editable/x-editable.min.js", loadSummerNote);
    }
    function loadSummerNote(){
        loadScript("js/plugin/summernote/summernote.min.js", pagefunction)
    }

</script>
