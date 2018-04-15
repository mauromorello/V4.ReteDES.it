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

if (!posso_gestire_ordine_come_gas($id_ordine)){
    echo rd4_go_back("Non ho i permessi necessari");die;
}
$O=new ordine($id_ordine);
if($O->codice_stato=="CO"){
    //echo '<div class="jumbotron text-center"><i class="fa fa-frown-o fa-3x"></i><h1>Ordine già convalidato.</h1><a class="pull-right btn btn-danger" href="javascript:history.go(-1)">Indietro</a></div>';
    //die;
    $msg_conv='<div class="alert alert-danger"><strong>N.B:</strong> Questo ordine è già stato convalidato, potrai fare solo rettifiche che riguardano il tuo GAS.</div>';
    $show=false;
}else{
    $show=true;
    $msg_conv='';
}

$stmt = $db->prepare("SELECT * from retegas_ordini WHERE id_ordini=:id_ordine;");
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->execute();
        $rowo = $stmt->fetch(PDO::FETCH_ASSOC);


$page_title = "Aggiunte e detrazioni";

$costo_gas_referenza=round($O->costo_gas_referenza_v2(_USER_ID_GAS),2);
if($costo_gas_referenza==0){
    $costo_gas_referenza = '';
}else{
    if(VA_ORDINE_GAS_SOLO_EXTRA_GAS($o->id_ordini,_USER_ID_GAS)>0){
        $costo_gas_referenza = '';
    }else{
        $costo_gas_referenza = '<div class="alert alert-danger">Questo ordine ha un <strong>COSTO GAS</strong> previsto di <strong>'._NF($costo_gas_referenza).' Eu.</strong>, ma non sono stati trovate voci corrispondenti nell\'ordine. In questa pagina puoi correggere l\'anomalia. Se l\'ordine è condiviso tra più GAS ricordati di selezionare la voce "Solo utenti del mio GAS".</div>';

    }
}
?>
<?php echo $O->navbar_ordine(); ?>
<?php echo $msg_conv; ?>
<?php echo $costo_gas_referenza; ?>
<div class="panel panel-blueLight padding-10 margin-top-10">
    <p class="font-xl">Aggiungi o sottrai degli importi all'ordine:</p>
    <div class="row">

        <div class="col-xs-4 ">
            <div class="well well-lg boxino_riepilogo">
            </div>
        </div>
        <div class="col-xs-8">
            <form id="rettifica_aggiunta" class="smart-form" action="ajax_rd4/rettifiche/_act.php">
                <fieldset>
                    <section>
                    <label class="label">Importo aggiungere/sottrarre</label>
                        <label class="input">
                            <input type="text" class="input-lg" name="importo_da_aggiungere" id="importo_da_aggiungere">
                        </label>
                        <div class="note">
                            Puoi inserire cifre positive (aggiunte) o negative (sottratte)
                        </div>
                    </section>
                </fieldset>
                <fieldset>
                    <section>
                        <label class="label">Come gestire questo importo ?</label>
                        <div class="row">
                            <div class="col col-12">
                                <label class="radio">
                                    <input type="radio" name="tipo" value="1" checked="CHECKED">
                                    <i></i>Ripartisci proporzionalmente tra gli utenti in base alla loro spesa</label>
                                <label class="radio">
                                    <input type="radio" name="tipo" value="2" >
                                    <i></i>Ripartisci l'importo equamente tra gli utenti.</label>
                                <label class="radio">
                                    <input type="radio" name="tipo" value="3" >
                                    <i></i>Dividi in proporzione al campo "note brevi" detto anche "ingombro"</label>
                            </div>
                        </div>
                        <div class="note">
                            La prima opzione è come funzionava nelle vecchie versioni.
                        </div>
                    </section>
                    <section>
                        <label class="label">Nel caso abbia scelto la suddivisone proporzionale, come comportarsi con le rettifiche esistenti ?</label>
                        <div class="row">
                            <div class="col col-12">
                                <label class="radio">
                                    <input type="radio" name="applica_rettifiche" value="1" checked="CHECKED">
                                    <i></i>Includi le rettifiche esistenti</label>
                                <label class="radio">
                                    <input type="radio" name="applica_rettifiche" value="2" >
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
                                <option value="2">Solo utenti partecipanti a questo ordine del tuo GAS</option>
                            </select> <i></i> </label>
                        <div class="note">
                            Se scegli solo gli utenti del tuo GAS la variazione sarà applicata soltanto a loro.
                        </div>
                    </section>
                    <section class="">
                        <label class="label">Scegli il tipo di movimento</label>
                        <label class="select">
                            <select id="select_tipo_movimento_aggiunta" name="select_tipo_movimento_aggiunta">
                                <option value="1" selected>Rettifica</option>
                                <option value="2">Trasporto</option>
                                <option value="3">Gestione</option>
                                <option value="4">Progetto</option>
                                <option value="5">Rimborso</option>
                                <option value="6">Maggiorazione</option>
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
                    <input type="hidden"  name="act" value="rettifica_aggiunta">
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
            <?php echo help_render_html('rettifiche_aggiunte',$page_title); ?>
        </article>
    </div>
</section>

<script type="text/javascript">

    pageSetUp();



    var pagefunction = function(){
        //------------HELP WIDGET
        <?php echo help_render_js('rettifiche_aggiunte');?>
        //------------END HELP WIDGET

        var update_boxino = function(){
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
        }


        var $totaleForm = $("#rettifica_aggiunta").validate({

            rules : {
                importo_da_aggiungere : {
                    required : true,
                    number : true
                }
            },
            messages : {
                importo_da_aggiungere : {
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


       $(document).off('click','#elimina_rettifiche_gas');
       $(document).on('click','#elimina_rettifiche_gas',function(e){
           $.ajax({
              type: "POST",
              url: "ajax_rd4/rettifiche/_act.php",
              dataType: 'json',
              data: {act: "art_delete_rett_gas", id_ordine : <?php echo $id_ordine ?>},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                        ok(data.msg);
                        update_boxino();
                }
            });
       })

       $(document).off('click','#elimina_rettifiche_globali');
       $(document).on('click','#elimina_rettifiche_globali',function(e){
           $.ajax({
              type: "POST",
              url: "ajax_rd4/rettifiche/_act.php",
              dataType: 'json',
              data: {act: "art_delete_rett_globali", id_ordine : <?php echo $id_ordine ?>},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                        ok(data.msg);
                        update_boxino();
                }
            });
       })

       update_boxino();

    } // end pagefunction

loadScript("js/plugin/jquery-form/jquery-form.min.js", pagefunction);

</script>
