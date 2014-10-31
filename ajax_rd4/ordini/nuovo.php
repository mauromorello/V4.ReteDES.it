<?php require_once("inc/init.php");
$ui = new SmartUI;
$page_title= "Nuovo Ordine";

$var ="ciccio";
$s =<<<SEMPLICE
<style>.select2-hidden-accessible{display:none}</style>
<div class="row">
<div class="col-12">
<form action="ajax_rd4/ordini/_act.php" method="post" id="nuovo_ordine_form" class="smart-form">
                            <fieldset>

                                <section>
                                        <input class="hidden" id="idlistino" name="idlistino" type="text" value="0">
                                        <input  name="act" type="hidden" value="nuovo_ordine">
                                        <label for="listalistini" class="label">Seleziona un listino tra quelli disponibili, digitando nel box il suo nome o il nome della ditta</label>

                                        <div id="listalistini" style="width:100%" class="" rel=""></div>

                                </section>
                                <hr>
                                <section>
                                        <label for="nomeordine" class="label">Inserisci un nome che identifica l'ordine</label>
                                        <label class="input">
                                            <input type="text" name="nomeordine" placeholder="Nome">
                                        </label>
                                </section>

                                <section>
                                        <label for="quantigiorni" class="label">Tra quanti giorni questo ordine si deve chiudere</label>
                                        <label class="input">
                                            <input type="text" name="quantigiorni" placeholder="Giorni">
                                        </label>
                                </section>
                                <section>
                                    <label class="label">Inserisci qua le note di questo ordine <small>(OPZIONALI)</small></label>
                                    <label class="textarea">
                                        <textarea rows="3" class="custom-scroll" name="noteordine"></textarea>
                                    </label>

                                </section>
                               </fieldset>

                            <footer>
                                <button id="start_ordine" type="submit" name="submit" class="btn btn-success">
                                    <i class="fa fa-save"></i>
                                    &nbsp;Fai partire l'ordine
                                </button>

                            </footer>
</form>
</div>
</div>
SEMPLICE;

$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>false,
                    "deletebutton"=>false,
                    "colorbutton"=>true);
$wg_nuovo_semplice = $ui->create_widget($options);
$wg_nuovo_semplice->id = "wg_nuovo_semplice";
$wg_nuovo_semplice->body = array("content" => $s,"class" => "");
$wg_nuovo_semplice->header = array(
    "title" => '<h2>Nuovo ordine</h2>',
    "icon" => 'fa fa-sign-in'
);
$title_navbar='<i class="fa fa-shopping-cart fa-2x pull-left"></i> Nuovo ordine!<br><small class="note">Buttati nella mischia!</small>';
?>
<?php echo navbar($title_navbar); ?>

<section id="widget-grid" class="margin-top-10">



    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12col-lg-12">
            <?php echo $wg_nuovo_semplice->print_html(); ?>
            <?php echo help_render_html("nuovo_ordine",$page_title); ?>
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
        <?php echo help_render_js("nuovo_ordine"); ?>
        //-------------------------HELP

        $("#listalistini").select2({
            placeholder: "Cerca tra i listini..",
                //minimumInputLength: 3,
                ajax: {
                url: "ajax_rd4/ordini/inc/listalistini.php",
                dataType: 'json',
                data: function (term, page) {
                    return {
                        q: term
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
     var $orderForm = $('#nuovo_ordine_form').validate({
                        ignore: ".select2-focusser, .select2-input",

                        // Rules for form validation
                        rules : {
                            nomeordine : {
                                required : true
                            },
                            quantigiorni : {
                                required : true,
                                digits: true
                            },
                            idlistino : {
                                required : true,
                                digits: true,
                                min: 1
                            }
                        },

                        // Messages for form validation
                        messages : {
                            nomeordine : {
                                required : 'E\' necessario indicare un nome per l\'ordine'
                            },
                            quantigiorni : {
                                required : 'E\' necessario indicare quanti giorni dura l\'ordine'
                            },
                            idlistino :{
                                required : 'Devi indicare un listino!',
                                min: "E\' necessario indicare un listino!"
                            }
                        },

                        // Do not change code below
                        errorPlacement : function(error, element) {
                            error.insertAfter(element.parent());
                        },
                        submitHandler: function(form) {
                            $.SmartMessageBox({
                                title : "Stai per far partire un nuovo ordine!",
                                content : "Questo ordine si aprirà in automatico tra 2 ore. Se vuoi fare delle modifiche o eliminarlo vai nella pagina I MIEI ORDINI",
                                buttons : '[OK][ANNULLA]'
                            }, function(ButtonPressed) {
                                if (ButtonPressed === "OK") {
                                    $(form).ajaxSubmit({
                                        dataType: 'json',
                                        success: function(data, status) {
                                            console.log(data.result + ' - ' + data.msg);
                                            if(data.result=="OK"){
                                                ok(data.msg);
                                                window.setTimeout(function(){
                                                    window.location.href = "#ajax_rd4/ordini/edit.php?id="+data.id;
                                                }, 5000);
                                            }else{
                                                ko(data.msg);
                                            }
                                        }
                                    });

                                }
                            });

                        }
                    });




    }
    // end pagefunction

    loadScript("js/plugin/jquery-form/jquery-form.min.js", pagefunction);



</script>
