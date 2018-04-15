<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.ordine.php");

$ui = new SmartUI;
$page_title= "Comunica agli utenti di questo ordine";
$page_id= "comunica_ordine";

//CONTROLLI
$id_ordine = (int)$_GET["id"];

if (!posso_gestire_ordine($id_ordine)){
    echo rd4_go_back("Non ho i permessi necessari");die;
}

$O = new ordine($id_ordine);
$stato = $O->codice_stato;


?>
<?php echo $O->navbar_ordine(); ?>

<div class="row">

    <fieldset class="col col-md-6">
        <h1>Comunica a...</small></h1>
        <div class="well well-sm">
            <form class="smart-form" id="comunica_ordine" action="ajax_rd4/ordini/_act.php" method="post">
            <input type="hidden" name="act" value="comunica_ordine">
            <input type="hidden" name="id_ordine" value="<?php echo $id_ordine ?>">
            <section>
                <label class="label">Destinatari</label>
                <div class="row">
                    <div class="col col-12">
                        <label class="radio">
                            <input type="radio" name="tipo_mail" checked="checked" value="1">
                            <i></i>Tutti gli utenti che hanno acquistato qualcosa, ma solo del tuo GAS</label>
                        <label class="radio">
                            <input type="radio" name="tipo_mail" value="2">
                            <i></i>Tutti gli utenti che hanno acquistato qualcosa, di tutti i GAS</label>
                        <label class="radio">
                            <input type="radio" name="tipo_mail" value="3" <?php if($stato<>"AP"){echo ' disabled="DISABLED" ';}?>>
                            <i></i>Potenziali utenti del tuo GAS</label>
                        <label class="radio">
                            <input type="radio" name="tipo_mail" value="4" <?php if($stato<>"AP"){echo ' disabled="DISABLED" ';}?>>
                            <i></i> <span class="fa fa-warning text-danger"></span> Potenziali utenti di TUTTI i GAS</label>
                    </div>
                </div>

            </section>
            <section>
                <label class="label">Testo del messaggio</label>
                <label class="textarea">
                    <textarea rows="3" class="custom-scroll" name="messaggio"></textarea>
                </label>
                <div class="note">
                    <strong>Nota:</strong> Non esagerare con le mail!
                </div>
            </section>
            <footer>
                <button type="submit" class="btn btn-primary">
                    Invia
                </button>
             </footer>
            </form>
        </div>

    </fieldset>
    <fieldset class="col col-md-6 ">
        <h1>Lista destinatari: <span id="n_destinatari"></span></h1>
        <div id="box_destinatari"></div>

    </fieldset>
</div>
<br>
<div class="clearfix"></div>
<?php echo help_render_html($page_id,$page_title); ?>

<script type="text/javascript">

    pageSetUp();


    var pagefunction = function() {

        //-------------------------HELP
        //document.title = escape('<?php echo "ReteDES.it :: ".$O->descrizione_ordini;?>');
        <?php echo help_render_js($page_id); ?>
        //-------------------------HELP

        var selectedVal = "";
        $(document).on('change','input[type=radio][name=tipo_mail]',function(e){
            var selected = $("#comunica_ordine input[type='radio']:checked");
                if (selected.length > 0) {
                    selectedVal = selected.val();
                }
            //alert(selectedVal);

            $.ajax({
                  type: "POST",
                  url: "ajax_rd4/ordini/_act.php",
                  dataType: 'json',
                  data: {act: "comunica_ordine_lista",tipo_mail: selectedVal, id_ordine : <?php echo $id_ordine;?>},
                  context: document.body
                }).done(function(data) {
                    if(data.result=="OK"){
                        //ok(data.msg);
                        $('#box_destinatari').empty();
                        $('#box_destinatari').html(data.html);
                        $('#n_destinatari').html(data.n);
                    }else{
                        ko(data.msg);
                    }
                });

        })


        var $comunica_ordine = $('#comunica_ordine').validate({

            submitHandler : function(form) {
                $.blockUI();
                var selected = $("#comunica_ordine input[type='radio']:checked");
                if (selected.length > 0) {
                    selectedVal = selected.val();
                }
                //alert(selectedVal);

                    $(form).ajaxSubmit({
                        type:"POST",
                        dataType: 'json',
                        success : function(data) {
                                if(data.result=="OK"){
                                    ok(data.msg);

                                }else{
                                    ko(data.msg);

                                }
                        $.unblockUI();
                        }
                    });

            },

            // Do not change code below
            errorPlacement : function(error, element) {
                error.insertAfter(element.parent());
            }
        });




        //PRIMA VOLTA
        $.ajax({
                  type: "POST",
                  url: "ajax_rd4/ordini/_act.php",
                  dataType: 'json',
                  data: {act: "comunica_ordine_lista",tipo_mail: 1, id_ordine : <?php echo $id_ordine;?>},
                  context: document.body
                }).done(function(data) {
                    if(data.result=="OK"){
                        //ok(data.msg);
                        $('#box_destinatari').empty();
                        $('#box_destinatari').html(data.html);
                        $('#n_destinatari').html(data.n);
                    }else{
                        ko(data.msg);
                    }
                });




    }
    // end pagefunction


    loadScript("js/plugin/jquery-form/jquery-form.min.js", pagefunction);



</script>
