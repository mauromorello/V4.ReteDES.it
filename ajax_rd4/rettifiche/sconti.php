<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.ordine.php");
$ui = new SmartUI;
$converter = new Encryption;

$id_ordine = CAST_TO_INT($_POST["id"],0);
if ($id_ordine==0){
    $id_ordine = CAST_TO_INT($_GET["id"],0);
}

if($id_ordine==0){echo rd4_go_back("KO!");die;}

if (!posso_gestire_ordine($id_ordine)){
    echo rd4_go_back("Non ho i permessi necessari");die;
}

$O = new ordine($id_ordine);
if($O->codice_stato=="CO"){
    //echo rd4_go_back("Ordine già convalidato");
    //die;
    $show=false;
    $msg_conv='<div class="alert alert-danger"><strong>N.B:</strong> Questo ordine è già stato convalidato, potrai fare solo rettifiche che riguardano il tuo GAS.</div>';
}else{
    $show=true;
    $msg_conv='';
}

$stmt = $db->prepare("SELECT * from retegas_ordini WHERE id_ordini=:id_ordine;");
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->execute();
        $rowo = $stmt->fetch(PDO::FETCH_ASSOC);


$page_title = "Sconti & Maggiorazioni";



?>
<?php echo $O->navbar_ordine(); ?>
<?php echo $msg_conv; ?>
<div class="panel panel-blueLight padding-10">
    <p class="font-xl">Applica sconti o maggiorazioni percentuali.</p>
    <div class="row">

        <div class="col-xs-4 ">
            <div class="well well-lg boxino_riepilogo" >
            </div>
        </div>
        <div class="col-xs-8">
            <form id="rettifica_sconto" class="smart-form" action="ajax_rd4/rettifiche/_act.php">
                <fieldset>
                    <section>
                    <label class="label">Maggiorazione / Sconto da effettuare (in %)</label>
                        <label class="input">
                            <input type="text" class="input-lg" name="importo_da_aggiungere_sconto" id="importo_da_aggiungere_sconto">
                        </label>
                        <div class="note">
                            Puoi inserire cifre positive (maggiorazioni) o negative (sconti)
                        </div>
                    </section>
                </fieldset>
                <fieldset>

                    <section>
                        <label class="label">Come comportarsi con le rettifiche esistenti ?</label>
                        <div class="row">
                            <div class="col col-12">
                                <label class="radio">
                                    <input type="radio" name="applica_rettifiche_sconto" value="1" checked="CHECKED">
                                    <i></i>Includi le rettifiche esistenti</label>
                                <label class="radio">
                                    <input type="radio" name="applica_rettifiche_sconto" value="2" >
                                    <i></i>Applica solo al valore netto</label>
                            </div>
                        </div>
                        <div class="note">
                            Per valore netto si intende il valore effettivo della merce arrivata.
                        </div>
                    </section>
                    <section class="">
                        <label class="label">Chi è coinvolto ?</label>
                        <label class="select">
                            <select id="select_coinvolto_aggiunta" name="select_coinvolto_aggiunta">
                               <?php if((livello_gestire_ordine($id_ordine)>1) and $show){ ?><option value="1" selected>Tutti gli utenti partecipanti a questo ordine</option><?php } ?>
                                <option value="2" selected>Solo utenti partecipanti a questo ordine del tuo GAS</option>
                            </select> <i></i> </label>
                        <div class="note">
                            Se scegli solo gli utenti del tuo GAS la variazione sarà applicata soltanto a loro.
                        </div>
                    </section>
                    <section class="">
                        <label class="label">Scegli il tipo di movimento</label>
                        <label class="select">
                            <select id="select_tipo_movimento_aggiunta" name="select_tipo_movimento_aggiunta">
                                <option value="1">Rettifica</option>
                                <option value="2">Trasporto</option>
                                <option value="3">Gestione</option>
                                <option value="4">Progetto</option>
                                <option value="5">Rimborso</option>
                                <option value="6" selected>Maggiorazione</option>
                                <option value="7">Sconto</option>
                                <option value="8">Abbuono</option>
                            </select> <i></i> </label>
                        <div class="note">
                            Puoi indicare un tipo di operazione a tua scelta, ti sarà utile per raggruppare le cifre successivamente.
                        </div>
                    </section>
                    <section>
                    <label class="label">Descrizione operazione</label>
                        <label class="input">
                            <input type="text" class="" name="descrizione_operazione_aggiunta" id="descrizione_operazione_aggiunta">
                        </label>
                        <div class="note">
                            Opzionale, se lasciato vuoto verrà indicato il tipo di operazione scelta.
                        </div>
                    </section>
                </fieldset>

                <footer>
                    <input type="hidden"  name="id_ordine" value="<?php echo $id_ordine ?>">
                    <input type="hidden"  name="act" value="rettifica_aggiunta_sconto">
                    <button type="submit" class="btn btn-primary">
                        Esegui!
                    </button>
                    <button type="button" class="btn btn-default" onclick="window.history.back();">
                        indietro
                    </button>
                </footer>
            </form>
        </div>
    </div>
</div>


<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html('rettifiche_sconto',$page_title); ?>
        </article>
    </div>
</section>

<script type="text/javascript">

    pageSetUp();



    var pagefunction = function(){
        //------------HELP WIDGET
        <?php echo help_render_js('rettifiche_sconto');?>
        //------------END HELP WIDGET

        var $totaleForm = $("#rettifica_sconto").validate({

            rules : {
                importo_da_aggiungere_sconto : {
                    required : true,
                    number : true
                }
            },
            messages : {
                importo_da_aggiungere_sconto : {
                    required : 'Inserisci qualcosa',
                    number : 'Inserisci un numero valido (Usa il punto, non la virgola)',
                }
            },
            // Ajax form submition
            submitHandler : function(form) {
                $(form).ajaxSubmit({
                    type:"POST",
                    dataType: 'json',
                    success : function(data) {
                        if(data.result=="OK"){
                            ok(data.msg);
                            $.ajax({
                              type: "POST",
                              url: "ajax_rd4/rettifiche/_act.php",
                              dataType: 'json',
                              data: {act: "schedina riepilogo", id_ordine : <?php echo $id_ordine ?>},
                              context: document.body
                            }).done(function(data) {
                                if(data.result=="OK"){
                                        $('.boxino_riepilogo').html(data.msg);
                                }
                            });

                        }else{
                            ko(data.msg);}
                        }

                });
                return false;
            },

            // Do not change code below
            errorPlacement : function(error, element) {
                error.insertAfter(element.parent());
            }
        });
        $.ajax({
          type: "POST",
          url: "ajax_rd4/rettifiche/_act.php",
          dataType: 'json',
          data: {act: "schedina riepilogo", id_ordine : <?php echo $id_ordine ?>},
          context: document.body
        }).done(function(data) {
            if(data.result=="OK"){
                    $('.boxino_riepilogo').html(data.msg);
            }
        });

    } // end pagefunction

loadScript("js/plugin/jquery-form/jquery-form.min.js", pagefunction);

</script>
