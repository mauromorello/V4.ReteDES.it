<?php
require_once("inc/init.php");
$ui = new SmartUI;
$converter = new Encryption;

$page_title = "Nuovo Fornitore";
$page_id = "nuovo_fornitore";

$sql = "SELECT COUNT(*) as conto FROM retegas_ditte;";
$stmt = $db->prepare($sql);
$stmt->execute();
$row = $stmt->fetch();
$n_fornitori = $row["conto"];        
 
?>


<h1><strong>Inserimento nuovo fornitore.</strong><br><small>In reteDES ad oggi ci sono <strong><?php echo $n_fornitori ?></strong> fornitori inseriti, è possibile che anche quello che proponi tu ci sia sia già.
Prima aggiungere il tuo controlliamo che non sia effettivamente così, per cui....</small></h1>
<br>
<div class="well well-sm">
<h1>Tutti i campi sono obbligatori.</h1>
    <form action="ajax_rd4/fornitori/_act.php" class="smart-form" id="nuova_ditta_form" novalidate="novalidate">
        <div class="row">
            <section class="col col-md-6">
                <label class="label">Nome ditta.</label>
                <label class="input">
                    <input type="text" class="input-sm" id="nome_ditta" name="nome_ditta">
                </label>
            </section >
            <section class="col col-md-6">
                <label class="label">Contatto telefonico:</label>
                <label class="input">
                    <input type="text" class="input-sm" id="tel_ditta" name="tel_ditta">
                </label>
            </section>
        </div>
        <div class="row">
        <section class="col col-md-6">
            <label class="label">Contatto EMAIL (che sia valido)</label>
            <label class="input">
                <input type="text" class="input-sm" id="email_ditta" name="email_ditta">
            </label>
            
        </section>
        <section class="col col-md-6">
            <label class="label">Inserisci il codice fiscale oppure la partita IVA:</label>
            <label class="input">
                <input type="text" class="input-sm" id="piva_ditta" name="piva_ditta">
            </label>
            <p id="piva_name"></p>
        </section>
        </div>
        <p class="note">La verifica potrebbe trovare dei dati UGUALI, per cui non è possibile inserire dei doppioni, oppure potrebbe trovare dei dati SIMILI (Solo sul numero di telefono e sul nome).<br>
        In quel caso è possibile inserire il nuovo fornitore ma è consigliato di verificare cliccando sul link che porta alla scheda.<br>
        Per poter modificare i dati è necessario cliccare su "resetta".</p>
        <div id="result" class="margin-top-10"></div>
        <footer>
            <input type="hidden" name="act" value="do_inserisci_nuova_ditta">
            
            <button class="btn btn-default disabled" name="submit" value="2" id="button_inserisci">
                INSERISCI
            </button>
            <button class="btn btn-primary" name="submit" value="1" id="button_verifica">
                VERIFICA
            </button>
            <span class="btn btn-warning pull-right" id="button_riverifica" style="display:none">
                RESETTA
            </span>
        </footer>
    </form>
    
    
</div>
    
    
<div class="row">
    <!-- PRIMA COLONNA-->
    <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <?php echo help_render_html($page_id,$page_title); ?>
    </article>

</div>

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
        
        var $contactForm = $("#nuova_ditta_form").validate({
            // Rules for form validation
            rules : {
                nome_ditta : {
                    required : true
                },
                email_ditta : {
                    required : true,
                    email : true
                },
                tel_ditta : {
                    required : true
                    
                },
                piva_ditta : {
                    required : true
                    
                }
            },

            // Messages for form validation
            messages : {
                nome_ditta : {
                    required : 'Inserisci un nome',
                },
                email_ditta : {
                    required : 'Inserisci una mail',
                    email : 'Inserisci una mail VALIDA'
                },
                tel_ditta : {
                    required : 'Inserisci il telefono'
                },
                piva_ditta : {
                    required : 'Inserisci la partita iva o il codice fiscale'
                }
            },

            
            submitHandler : function(form) {
                    
                
                
                $(form).ajaxSubmit({
                    type:"POST",
                    dataType: 'json',
                    success : function(data) {
                            if(data.result=="OK"){
                                $('#result').html(data.html);
                                $('#piva_name').html(data.piva_name);
                                
                                if(data.procedi==1){
                                    $('#button_inserisci').removeClass('disabled').removeClass('btn-default').addClass('btn-success');   
                                    $('#piva_ditta, #nome_ditta, #tel_ditta, #email_ditta').prop( "readonly", true );
                                    $('#button_verifica').hide();
                                    $('#button_riverifica').show();
                                }
                                if(data.procedi==3){
                                   location.replace('<?php echo APP_URL; ?>/#ajax_rd4/fornitori/scheda.php?id='+data.id);   
                                }                               
                            }else{
                                ko(data.msg);}
                            }
                });    
            },

            // Do not change code below
            errorPlacement : function(error, element) {
                error.insertAfter(element.parent());
            }
        });
     
        $(document).off('click','#button_riverifica');
        $(document).on('click','#button_riverifica',function(){
            $('#piva_ditta, #nome_ditta, #tel_ditta, #email_ditta').prop( "readonly", false );
            $('#button_inserisci').addClass('disabled').removeClass('btn-success').addClass('btn-default');
            $('#button_verifica').show();
            $('#button_riverifica').hide();
            ok("ok");
        });
     
    } // end pagefunction



    loadScript("js/plugin/jquery-form/jquery-form.min.js", pagefunction);
</script>
