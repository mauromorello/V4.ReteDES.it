<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.gas.php");
$ui = new SmartUI;
$converter = new Encryption;

$page_title = "Nuovo GAS";
$page_id ="nuovo_gas";

$G = new gas(_USER_ID_GAS);
if((_USER_PERMISSIONS & perm::puo_creare_gas) OR (_USER_ID==$G->id_referente_gas) OR (_USER_PERMISSIONS & perm::puo_vedere_retegas)){

}else{
    rd4_go_back("Non puoi...");
}

if(_USER_PERMISSIONS & perm::puo_creare_gas){
    $info='<div class="alert alert-info">Puoi selezionare gli utenti tra quelli attivi del tuo GAS.</div>';
}

if(_USER_PERMISSIONS & perm::puo_vedere_retegas){
    $info='<div class="alert alert-info">Puoi selezionare gli utenti tra quelli attivi del tuo DES.</div>';
}

if(_USER_PERMISSIONS & perm::puo_gestire_retegas){
    $info='<div class="alert alert-info">Puoi selezionare gli utenti tra quelli attivi di RETEDES.</div>';
}

?>
<?php echo $G->render_toolbar('CREA GAS da');?>


<div class="row padding-10">
    <div class="col-12">
    <form action="ajax_rd4/gas/_act.php" method="post" id="nuovo_gas_form" class="smart-form well well-sm">

                               <?php
                                   echo $info;
                               ?>
                                <fieldset>

                                    <section>
                                            <label for="nomegas" class="label">Inserisci il nome del nuovo GAS</label>
                                            <label class="input">
                                                <input type="text" name="nomegas" placeholder="Nome">
                                            </label>
                                            <p class="note">Tutti i dati del nuovo GAS saranno poi gestibili dal suo responsabile.</p>
                                    </section>
                                    <hr>
                                    <section>
                                            <input class="hidden" id="idutente" name="idutente" type="text" value="0">
                                            <input  name="act" type="hidden" value="nuovo_gas">

                                            <label for="listautenti" class="label">Seleziona un utente tra quelli disponibili, digitando nel box il suo nome.</label>
                                            <div id="listautenti" style="width:100%" class="" rel=""></div>

                                    </section>
                                    <section>
                                            <label for="messaggio" class="label">Scrivi qualcosa al nuovo responsabile GAS per incoraggiarlo!</label>
                                            <label class="textarea">
                                                <textarea name="messaggio" class="textarea"></textarea>
                                            </label>
                                            <p class="note">Questa nota verrà inclusa nella mail che il nuovo responsabile riceverà per comunicare l'avvenuta attivazione del nuovo gas.</p>
                                    </section>

                                   </fieldset>

                                <footer>
                                    <button id="start_gas" type="submit" name="submit" class="btn btn-success">
                                        <i class="fa fa-save"></i>
                                        &nbsp;Fai nascere il nuovo GAS
                                    </button>

                                </footer>
    </form>
    </div>
</div>



<div class="row margin-top-10">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <?php echo help_render_html($page_id,$page_title); ?>
    </div>
</div>

<script type="text/javascript">

    pageSetUp();

    var pagefunction = function(){
        //------------HELP WIDGET
        document.title = '<?php echo "ReteDES.it :: $page_title";?>';
        <?php echo help_render_js($page_id);?>
        //------------END HELP WIDGET


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

        var $gasNuovoForm = $('#nuovo_gas_form').validate({
                        ignore: ".select2-focusser, .select2-input",

                        // Rules for form validation
                        rules : {
                            nomegas : {
                                required : true
                            },
                            idutente : {
                                required : true,
                                digits: true,
                                min: 1
                            }
                        },

                        // Messages for form validation
                        messages : {
                            nomegas : {
                                required : 'E\' necessario indicare un nome per il nuovo GAS'
                            },
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
                                title : "Stai per far nascere un nuovo GAS!",
                                content : "L'utente selezionato verrà spostato nel nuovo GAS, e ne diventerà il responsabile.",
                                buttons : '[OK][ANNULLA]'
                            }, function(ButtonPressed) {
                                if (ButtonPressed === "OK") {
                                    $(form).ajaxSubmit({
                                        dataType: 'json',
                                        success: function(data, status) {
                                            console.log(data.result + ' - ' + data.msg);
                                            if(data.result=="OK"){
                                                ok(data.msg);
                                            }else{
                                                ko(data.msg);
                                            }
                                        }
                                    });

                                }
                            });

                        }
                    });


    } // end pagefunction

    loadScript("js/plugin/jquery-form/jquery-form.min.js", pagefunction);

</script>