<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.ordine.php");

$ui = new SmartUI;
$page_title= "Operazioni avanzate";
$page_id= "edit_ordine_avanzato";

//CONTROLLI
$id_ordine = (int)$_GET["id"];

if (!posso_gestire_ordine($id_ordine)){
    echo rd4_go_back("'Gna fai...");die;
}

$O = new ordine($id_ordine);


?>
<?php echo $O->navbar_ordine(); ?>

<div class="row margin-top-10">
    <div class="col col-sm-6">
        <h3>Cambia listino</h3>
        <div class="well well-sm">
            <form class="smart-form">
                <fieldset>
                    <section>
                        Listino in uso: <?php echo $O->descrizione_listini ?><br>
                        Ditta: <?php echo $O->descrizione_ditte ?>
                    </section>
                    <section>
                            <input class="hidden" id="idlistino" name="idlistino" type="text" value="0">
                            <label for="listalistini" class="label">Seleziona un listino tra quelli disponibili di questa ditta.</label>
                            <div id="listalistini" style="width:100%" class="" rel=""></div>
                    </section>
                    <section>
                            <label class="label">Cosa fare con gli articoli già in ordine:</label>
                            <div class="">
                                <label class="radio">
                                    <input type="radio" name="scelta_listino" value="1" checked="checked">
                                    <i></i>Lasciali stare</label>
                                <label class="radio">
                                    <input type="radio" name="scelta_listino" value="2">
                                    <i></i>Se hanno lo stesso codice di un articolo presente nel nuovo listino varia il loro prezzo e la loro descrizione.</label>
                            </div>
                        </section>
                </fieldset>
            </form>
            <button id="do_listino_switch" data-id_ordine="<?php echo $O->id_ordini; ?>" class="btn btn-success pull-right">Cambia il listino</button>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="col col-sm-6">
        <h3>Cose varie</h3>
        <div class="well well-sm">
            <form class="smart-form">
                <fieldset>
                    <section>
                            <label class="label">Metodo per indicare le scatole e gli avanzi:</label>
                            <div class="">
                                <p class="note">Come è sempre stato</p>
                                <label class="radio">
                                    <input type="radio" name="metodo_scatole" value="0" <?php echo $O->metodo_scatole==0 ? ' checked="CHECKED" ':'';?>>
                                    <i></i>Livello Globale</label>
                                <p class="note">Ogni gas vede solo le sue scatole.</p>
                                <label class="radio">
                                    <input type="radio" name="metodo_scatole" value="1" <?php echo $O->metodo_scatole==1 ? ' checked="CHECKED" ':'';?>>
                                    <i></i>Livello GAS</label>
                            </div>
                        </section>
                </fieldset>
            </form>
            <div class="clearfix"></div>
        </div>
    </div>
</div>

<?php echo help_render_html($page_id,$page_title); ?>

<script type="text/javascript">

    pageSetUp();


    var pagefunction = function() {

        //-------------------------HELP
        document.title = '<?php echo "ReteDES.it :: Operazioni avanzate";?>';
        <?php echo help_render_js($page_id); ?>
        //-------------------------HELP

        var tipo = 1;
        var metodo_scatole = 1;
        var id_ordine = <?php echo $O->id_ordini; ?>;

        $(document).off("change","input[type='radio'][name='scelta_listino']");
        $(document).on("change","input[type='radio'][name='scelta_listino']",function(){
            var selected = $("input[type='radio'][name='scelta_listino']:checked");
            if (selected.length > 0) {
                tipo = selected.val();
            }
        });
        $(document).off("change","input[type='radio'][name='metodo_scatole']");
        $(document).on("change","input[type='radio'][name='metodo_scatole']",function(){
            var selected = $("input[type='radio'][name='metodo_scatole']:checked");
            if (selected.length > 0) {
                metodo_scatole = selected.val();

                $.ajax({
                  type: "POST",
                  url: "ajax_rd4/ordini/_act.php",
                  dataType: 'json',
                  data: {act: "do_metodo_scatole", id_ordine:id_ordine, metodo_scatole:metodo_scatole},
                  context: document.body
                }).done(function(data) {
                    if(data.result=="OK"){
                            ok(data.msg);}else{ko(data.msg);}

                });

            }
        });

        var d = <?php echo $O->id_ditte?>;

        $("#listalistini").select2({
            placeholder: "Cerca tra i listini..",
                //minimumInputLength: 3,
                ajax: {
                url: "ajax_rd4/ordini/inc/listalistini.php",
                dataType: 'json',
                data: function (term, page) {
                    return {
                        q: term,
                        d: d
                    };
                },

                results: function (data, page) {

                    return { results: data };
                }

            },
            formatResult: function(data){
                return '<span>'+data.descrizione_ditte+'</span><br><span>#'+data.id+'</span> <span><strong>'+data.text+'</strong><span><br><span class="font-xs">'+data.fullname+', '+data.descrizione_gas+' (valido fino al <b>'+data.data_valido+'</b>)</span>' ;
            },
            escapeMarkup: function(m) { return m; }
        });
        $('#listalistini').on("select2-selecting",
        function(e) {
                console.log(e.val);
                $('#idlistino').val(e.val);
        });


        loadScript("js/plugin/jquery-form/jquery-form.min.js");

        $("#do_listino_switch").click(function(e) {
            var id_ordine=$(this).data("id_ordine");
            var id_listino=$("#idlistino").val();

            $.SmartMessageBox({
                title : "Cambia il listino di questo ordine",
                content : "Sei sicur*?",
                buttons : "[Annulla][OK]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="OK"){
                    $.blockUI({ message: null });
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/ordini/_act.php",
                          dataType: 'json',
                          data: {act: "do_switch_listino", id_ordine:id_ordine, id_listino:id_listino, tipo:tipo},
                          context: document.body
                        }).done(function(data) {
                            $.unblockUI();
                            if(data.result=="OK"){
                                ok(data.msg);
                            }else{
                                ko(data.msg);
                            }

                        });
                }
            });

            e.preventDefault();
        });
        $("#ripristina_ordine_gas").click(function(e) {
            var id_ordine=$(this).data("id_ordine");
            $.SmartMessageBox({
                title : "Ripristina questo ordine",
                content : "Il ripristino dell\'ordine a livello GAS serve a correggere eventuali errori.",
                buttons : "[Annulla][OK]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="OK"){
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/ordini/_act.php",
                          dataType: 'json',
                          data: {act: "ripristina_ordine_gas", id_ordine:id_ordine},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    okReload(data.msg);}else{ko(data.msg);}

                        });
                }
            });

            e.preventDefault();
        })

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
