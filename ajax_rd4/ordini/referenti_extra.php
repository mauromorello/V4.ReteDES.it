<?php require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.ordine.php");
require_once("../../lib_rd4/class.rd4.user.php");

$ui = new SmartUI;
$page_title= "Referenti extra";
$page_id ="referenti_extra";

//CONTROLLI
$id_ordine = (int)$_GET["id"];

if (!posso_gestire_ordine($id_ordine)){
    echo "Non ho i permessi per gestire questo ordine";
    die();
}

$O = new ordine($id_ordine);
$info='<div class="alert alert-info">Puoi selezionare gli utenti tra quelli attivi del tuo GAS.</div>';


?>

<?php echo $O->navbar_ordine(); ?>



<div class="row padding-10">
    <div class="col-sm-6 well well-sm">

        <form action="ajax_rd4/ordini/_act.php" method="post" id="referente_extra_form" class="smart-form">

       <?php
           echo $info;
       ?>
        <fieldset>

            <section>
                    <input class="hidden" id="idutente" name="idutente" type="text" value="0">
                    <input  name="act" type="hidden" value="nuovo_referente_extra">
                    <input  name="id_ordine" type="hidden" value="<?php echo $id_ordine; ?>">

                    <label for="listautenti" class="label">Seleziona un utente.</label>
                    <div id="listautenti" style="width:100%" class="" rel=""></div>

            </section>
            <section>
                    <label for="messaggio" class="label">Indica al nuovo referente che ruolo andrà a ricoprire.</label>
                    <label class="textarea">
                        <textarea name="messaggio" class="textarea"></textarea>
                    </label>
                    <p class="note">Questa nota verrà inclusa nella mail che il nuovo referente riceverà per comunicare la sua nomina.</p>
            </section>

           </fieldset>

            <footer>
                <button id="start_gas" type="submit" name="submit" class="btn btn-success">
                    <i class="fa fa-save fa-fw"></i>
                    &nbsp;Aggiungi un referente EXTRA!
                </button>

            </footer>
        </form>
    </div>
    <div class="col-sm-6">
        <h1>Referenti extra attuali:</h1>
        <div class="well well-sm" >
        <ul class="notification-body no-padding margin-top-10" id="box_referenti_extra">
        </ul>
        </div>
    </div>
</div>


<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
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

    function update_box_referenti_extra(){
        var $box_re=$('#box_referenti_extra');

        $.ajax({
              type: "POST",
              url: "ajax_rd4/ordini/_act.php",
              dataType: 'json',
              data: {act: "show_lista_referenti_extra", id_ordine:<?php echo $id_ordine;?>},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                    $box_re.html(data.msg);
                }else{
                    ko(data.msg);
                }
                        //location.reload();
            });


    }

    var pagefunction = function() {

        //-------------------------HELP
        <?php echo help_render_js($page_id); ?>
        //-------------------------HELP

        $("#listautenti").select2({
                placeholder: "Cerca tra gli utenti..",
                    //minimumInputLength: 3,
                    ajax: {
                    url: "ajax_rd4/gas/inc/listautenti.php",
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
                    return '<span><strong>'+data.text+'</span></strong>&nbsp;<span>'+data.descrizione_gas+'<span> <span class="note">('+data.des_descrizione+')<span>' ;
                },
                escapeMarkup: function(m) { return m; }
        });

        $('#listautenti').on("select2-selecting",
        function(e) {
                console.log(e.val);
                $('#idutente').val(e.val);
        });

        var $referenteExtra = $('#referente_extra_form').validate({
                        ignore: ".select2-focusser, .select2-input",

                        // Rules for form validation
                        rules : {
                            idutente : {
                                required : true,
                                digits: true,
                                min: 1
                            }
                        },

                        // Messages for form validation
                        messages : {
                            idutente :{
                                required : 'Devi indicare un utente!',
                                min: "E\' necessario indicare un utente!"
                            }
                        },

                        // Do not change code below
                        errorPlacement : function(error, element) {
                            error.insertAfter(element.parent());
                        },
                        submitHandler: function(form) {
                            $.SmartMessageBox({
                                title : "Stai per aggiungere un referente a questo ordine!",
                                content : "L'utente selezionato avrà le stesse capacità operative d egestionali del referente ordine originale.",
                                buttons : '[OK][ANNULLA]'
                            }, function(ButtonPressed) {
                                if (ButtonPressed === "OK") {
                                    $(form).ajaxSubmit({
                                        dataType: 'json',
                                        success: function(data, status) {
                                            console.log(data.result + ' - ' + data.msg);
                                            if(data.result=="OK"){
                                                ok(data.msg);
                                                update_box_referenti_extra();
                                            }else{
                                                ko(data.msg);
                                            }
                                        }
                                    });

                                }
                            });

                        }
                    });


        $(document).on('click','.delete_referenza_extra',function(e){
            var id_utente=$(this).data('id_utente');
            $.ajax({
              type: "POST",
              url: "ajax_rd4/ordini/_act.php",
              dataType: 'json',
              data: {act: "elimina_referente_extra", id_utente:id_utente, id_ordine:<?php echo $id_ordine;?>},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                    ok(data.msg);
                    update_box_referenti_extra();
                }else{
                    ko(data.msg);
                }

                        //location.reload();
            });
        })

        update_box_referenti_extra();

    }
    // end pagefunction

    loadScript("js/plugin/jquery-form/jquery-form.min.js", pagefunction);



</script>
