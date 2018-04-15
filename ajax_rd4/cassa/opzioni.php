<?php
require_once("inc/init.php");
if(file_exists("../../lib_rd4/class.rd4.cassa.php")){require_once("../../lib_rd4/class.rd4.cassa.php");}
if(file_exists("../lib_rd4/class.rd4.cassa.php")){require_once("../lib_rd4/class.rd4.cassa.php");}

$ui = new SmartUI;
$page_title = "Opzioni cassa";
$page_id = "opzioni_cassa";

$C = new cassa(_USER_ID_GAS);

if ($C->_GAS_CASSA_CHECK_MIN_LEVEL=="SI"){$_GAS_CASSA_CHECK_MIN_LEVEL_CHECKED=' checked="checked" ';}else{$_GAS_CASSA_CHECK_MIN_LEVEL_CHECKED='';}
$_GAS_COPERTURA_CASSA = $C->_GAS_COPERTURA_CASSA;
$_GAS_CASSA_MIN_LEVEL = $C->_GAS_CASSA_MIN_LEVEL;
$_GAS_IBAN = $C->_GAS_IBAN;
$_GAS_ID_CREDITORE = $C->_GAS_ID_CREDITORE;

if ($C->_GAS_CASSA_SCARICO_AUTOMATICO=="SI"){$_GAS_CASSA_SCARICO_AUTOMATICO_CHECKED=' checked="checked" ';}else{$_GAS_CASSA_SCARICO_AUTOMATICO_CHECKED='';}
if ($C->_GAS_CASSA_BONIFICO_AUTOMATICO=="SI"){$_GAS_CASSA_BONIFICO_AUTOMATICO_CHECKED=' checked="checked" ';}else{$_GAS_CASSA_BONIFICO_AUTOMATICO_CHECKED='';}
if ($C->_GAS_CASSA_PRENOTAZIONE_ORDINI=="SI"){$_GAS_CASSA_PRENOTAZIONE_ORDINI_CHECKED=' checked="checked" ';}else{$_GAS_CASSA_PRENOTAZIONE_ORDINI_CHECKED='';}
if ($C->_GAS_CASSA_VISUALIZZAZIONE_SALDO>1){$_GAS_CASSA_VISUALIZZAZIONE_SALDO_CHECKED=' checked="checked" ';}else{$_GAS_CASSA_VISUALIZZAZIONE_SALDO_CHECKED='';}
if ($C->_GAS_CASSA_ORDINI_SCASSATI=="SI"){$_GAS_CASSA_ORDINI_SCASSATI_CHECKED=' checked="checked" ';}else{$_GAS_CASSA_ORDINI_SCASSATI_CHECKED='';}
if ($C->_GAS_CASSA_REGISTRA_AUTOMATICO=="SI"){$_GAS_CASSA_REGISTRA_AUTOMATICO_CHECKED=' checked="checked" ';}else{$_GAS_CASSA_REGISTRA_AUTOMATICO_CHECKED='';}

$allineamento = $C->_GAS_CASSA_ALLINEAMENTO_ORDINI;
if($allineamento==0){$zero= "SELECTED"; };
if($allineamento==1){$uno= "SELECTED"; };
if($allineamento==2){$due= "SELECTED"; };

$a ='<div class="row">
        <div class="col-sm-6">
               <div class="font-xs alert alert-danger">Modificare queste opzioni con molta cautela, soprattutto se il tuo gas ha già ordini aperti, o se non sei sicuro di cosa tu stia facendo.</div>

        <section class="col col-12">
            <p class="font-md">Copertura cassa. '._GAS_CASSA_CHECK_MIN_LEVEL.'</p>
            <form class="smart-form well">
                <label class="checkbox margin-top-10">

                    <input id="controlla_minimo" name="controlla_minimo" '.$_GAS_CASSA_CHECK_MIN_LEVEL_CHECKED.' type="checkbox" >
                    <i></i> Controlla il minimo di cassa (SI/NO)
                    <p class="note">Controlla che gli utenti non possano scendere sotto il minimo di cassa quando fanno gli ordini</p>
                </label>
                <hr>
                <label class="input margin-top-10">
                    Valore del minimo di cassa (Euro):
                    <input id="minimo_cassa" class="col col-2 pull-right" type="text" data-tipo="" name="_GAS_CASSA_MIN_LEVEL" value="'.$_GAS_CASSA_MIN_LEVEL.'">
                    <p class="note">Somma minima che ogni utente deve avere sul suo conto. Se scende sotto questa soglia non è possibile per lui ordinare merce</p>
                </label>
                <hr>
                <label class="input margin-top-10">
                    Percentuale per copertura cassa (%):
                    <input id="percentuale_copertura_cassa" class="col col-2 pull-right" type="text" data-tipo="" name="_GAS_COPERTURA_CASSA" value="'.$_GAS_COPERTURA_CASSA.'">
                    <p class="note">Percentuale che in automatico viene aggiunta all\'importo di un ordine per garantire la copertura della cassa, a fronte di spese di trasporto e di gestione non confermate.</p>
                </label>
                <hr>
                <footer>
                    <button class="btn btn-primary pull-right" id="cassa_option_1_save">
                        Salva le modifiche
                    </button>
                </footer>

            </form>

        </section>
        <p></p>
        <section class="col col-12">
            <p class="font-md">SEPA</p>
            <form class="smart-form well">
                <label class="input margin-top-10">
                    IBAN del GAS:
                    <input id="gas_iban" class="col col-2 pull-right" type="text" data-tipo="" name="_GAS_IBAN" value="'.$_GAS_IBAN.'">
                    <p class="note">Inserire l\'IBAN senza spazi. Non vi è nessun controllo sulla sua correttezza.</p>
                </label>
                <hr>
                <label class="input margin-top-10">
                    ID Creditore:
                    <input id="gas_id_creditore" class="col col-2 pull-right" type="text" data-tipo="" name="_GAS_ID_CREDITORE" value="'.$_GAS_ID_CREDITORE.'">
                    <p class="note">Questo dato è fornito dalla banca dove si appoggia il GAS per poter ricevere gli SDD.</p>
                </label>
                <hr>
                <footer>
                    <button class="btn btn-primary pull-right" id="cassa_option_2_save">
                        Salva le modifiche
                    </button>
                </footer>

            </form>

        </section>
        
        
        </div>

        <div class="col-sm-6">
        <section class="col col-12">
            <p class="font-md">Automatizzazioni:</p>
            <form class="smart-form well">

                <label class="toggle margin-top-5">
                    <input class="" type="checkbox" name="_GAS_CASSA_SCARICO_AUTOMATICO_CHECKED" id="_GAS_CASSA_SCARICO_AUTOMATICO_CHECKED" '.$_GAS_CASSA_SCARICO_AUTOMATICO_CHECKED.'>
                    <i data-swchon-text="SI" data-swchoff-text="NO"></i>Scarico credito automatico
                    <p class="note">Alla CONVALIDA dell\'ordine da parte di uno dei suoi gestori, tutti i movimenti della cassa saranno allineati con qualli dell\'ordine, seguendo le successive impostazioni.</p>

                </label>
                <p class="alert alert-warning"><b>NB: </b>l\'utente che effettua l\'allineamento, sia esso gestore, che gestore extra o supervisore ordini, deve avere abilitato il permesso "può operare con credito altrui".</p>
                <label class="margin-top-5">Allineamento ordini</label>
                <label class="margin-top-5 select">
                    <select id="allineamento_cassa">
                        <option value="0" '.$zero.'>1 movimento: (Netto + extra + GAS)</option>
                        <option value="1" '.$uno.'>2 movimenti: (Netto + extra), GAS (Default)</option>
                        <option value="2" '.$due.'>3 movimenti: Netto, extra, GAS</option>
                    </select>
                    <i></i>
                    <p class="note">Quando si allinea un ordine con la cassa, si può scegliere se generare uno o più movimenti.</p>
                </label>

                <label class="toggle margin-top-5">
                    <input class="" type="checkbox" name="_GAS_CASSA_REGISTRA_AUTOMATICO" id="_GAS_CASSA_REGISTRA_AUTOMATICO_CHECKED" '.$_GAS_CASSA_REGISTRA_AUTOMATICO_CHECKED.'>
                    <i data-swchon-text="SI" data-swchoff-text="NO"></i>Registrazione movimenti automatica
                    <p class="note">Quando si allinea un ordine, contestualmente si registrano i movimenti in automatico.</p>
                </label>

                <label class="toggle margin-top-5">
                    <input class="" type="checkbox" name="_GAS_CASSA_BONIFICO_AUTOMATICO_CHECKED" id="_GAS_CASSA_BONIFICO_AUTOMATICO_CHECKED" '.$_GAS_CASSA_BONIFICO_AUTOMATICO_CHECKED.'>
                    <i data-swchon-text="SI" data-swchoff-text="NO"></i>Carico credito automatico
                    <p class="note">Quando un utente richiede una ricarica, questa viene subito registrata in cassa.</p>
                </label>

        </form>
        </section>
        <p></p>
        <section class="col col-12">
            <p class="font-md">Altro:</p>
            <form class="smart-form well">

                <label class="toggle margin-top-5">
                    <input class="" type="checkbox" name="" id="_GAS_CASSA_PRENOTAZIONE_ORDINI" '.$_GAS_CASSA_PRENOTAZIONE_ORDINI_CHECKED.'>
                    <i data-swchon-text="SI" data-swchoff-text="NO"></i>Permetti prenotazioni
                    <p class="note">Permetti che gli utenti possano ordinare merce SENZA intaccare il loro credito. (SI/NO)</p>
                </label>
                <label class="toggle margin-top-5">
                    <input class="" type="checkbox" name="" id="_GAS_CASSA_ORDINI_SCASSATI" '.$_GAS_CASSA_ORDINI_SCASSATI_CHECKED.'>
                    <i data-swchon-text="SI" data-swchoff-text="NO"></i>Gli utenti ordinano SENZA intaccare la loro cassa
                    <p class="note">Quando un utente ordina merce il suo credito non scala.</p>
                </label>
                <label class="toggle margin-top-5">
                    <input class="" type="checkbox" name="" id="_GAS_CASSA_VISUALIZZAZIONE_SALDO" '.$_GAS_CASSA_VISUALIZZAZIONE_SALDO_CHECKED.'>
                    <i data-swchon-text="2" data-swchoff-text="1"></i>Visualizzazione saldo
                    <p class="note">Scegliere "1" per visualizzare il saldo come credito residuo effettivo (totale - ancora da confermare), mentre "2" includendo anche i movimenti da confermare.</p>
                </label>

        </form>
        </section>

        </div>
    </div>';

?>

<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title).$C->_GAS_CASSA_ORDINI_SCASSATI; ?>

        </article>
    </div>
</section>
<div class="row"><div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><?php echo $a ?></div></div>

<script type="text/javascript">
    $(document).prop('title', 'ReteDes::<?echo $page_title?>');

    pageSetUp();

    var pagefunction = function(){

        //------------HELP WIDGET
        <?php echo help_render_js($page_id);?>
        //------------END HELP WIDGET

        $(document).off('click','#cassa_option_2_save');
        $(document).on('click','#cassa_option_2_save',function(e){
            
            var gas_iban =  $('#gas_iban').val();
            var gas_id_creditore = $('#gas_id_creditore').val();
            $.ajax({
              type: "POST",
              url: "ajax_rd4/cassa/_act.php",
              dataType: 'json',
              data: {act: "opzioni_cassa_2", gas_iban : gas_iban, gas_id_creditore:gas_id_creditore},
              context: document.body
                }).done(function(data) {
                    if(data.result=="OK"){
                    ok(data.msg);
                    }else{
                        ko(data.msg);
                }
            });



            console.log(minimo_cassa);
            e.preventDefault();
        })
        
        $(document).off('click','#cassa_option_1_save');
        $(document).on('click','#cassa_option_1_save',function(e){
            var controlla_minimo = $('#controlla_minimo')[0].checked;
            var minimo_cassa =  $('#minimo_cassa').val();
            var percentuale_copertura_cassa = $('#percentuale_copertura_cassa').val();
            $.ajax({
              type: "POST",
              url: "ajax_rd4/cassa/_act.php",
              dataType: 'json',
              data: {act: "opzioni_cassa_1", controlla_minimo : controlla_minimo, minimo_cassa:minimo_cassa, percentuale_copertura_cassa:percentuale_copertura_cassa},
              context: document.body
                }).done(function(data) {
                    if(data.result=="OK"){
                            ok(data.msg);
                    }else{
                        ko(data.msg);
                }
            });



            console.log(minimo_cassa);
            e.preventDefault();
        })

        $('#_GAS_CASSA_BONIFICO_AUTOMATICO_CHECKED').change(function () {
            if(this.checked) {value = 1;}else{value = 0;}

            $.ajax({
              type: "POST",
              url: "ajax_rd4/cassa/_act.php",
              dataType: 'json',
              data: {act: "_GAS_CASSA_BONIFICO_AUTOMATICO", value : value},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                        ok(data.msg);}else{ko(data.msg);}
            });


        });
        $('#_GAS_CASSA_REGISTRA_AUTOMATICO_CHECKED').change(function () {
            if(this.checked) {value = 1;}else{value = 0;}

            $.ajax({
              type: "POST",
              url: "ajax_rd4/cassa/_act.php",
              dataType: 'json',
              data: {act: "_GAS_CASSA_REGISTRA_AUTOMATICO", value : value},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                        ok(data.msg);}else{ko(data.msg);}
            });


        });
        $('#_GAS_CASSA_SCARICO_AUTOMATICO_CHECKED').change(function () {
            if(this.checked) {value = 1;}else{value = 0;}

            $.ajax({
              type: "POST",
              url: "ajax_rd4/cassa/_act.php",
              dataType: 'json',
              data: {act: "_GAS_CASSA_SCARICO_AUTOMATICO", value : value},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                        ok(data.msg);}else{ko(data.msg);}
            });


        });
        $('#_GAS_CASSA_PRENOTAZIONE_ORDINI').change(function () {
            if(this.checked) {value = 1;}else{value = 0;}

            $.ajax({
              type: "POST",
              url: "ajax_rd4/cassa/_act.php",
              dataType: 'json',
              data: {act: "_GAS_CASSA_PRENOTAZIONE_ORDINI", value : value},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                        ok(data.msg);}else{ko(data.msg);}
            });
        });
        $('#_GAS_CASSA_VISUALIZZAZIONE_SALDO').change(function () {
            if(this.checked) {value = 2;}else{value = 1;}

            $.ajax({
              type: "POST",
              url: "ajax_rd4/cassa/_act.php",
              dataType: 'json',
              data: {act: "_GAS_CASSA_VISUALIZZAZIONE_SALDO", value : value},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                        ok(data.msg);}else{ko(data.msg);}
            });
        });
        $('#_GAS_CASSA_ORDINI_SCASSATI').change(function () {
            if(this.checked) {value = 1;}else{value = 0;}

            $.ajax({
              type: "POST",
              url: "ajax_rd4/cassa/_act.php",
              dataType: 'json',
              data: {act: "_GAS_CASSA_ORDINI_SCASSATI", value : value},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                        ok(data.msg);}else{ko(data.msg);}
            });
        });

        $('#allineamento_cassa').on('change', function() {
          var value= this.value; // or $(this).val()
          $.ajax({
              type: "POST",
              url: "ajax_rd4/cassa/_act.php",
              dataType: 'json',
              data: {act: "_GAS_CASSA_ALLINEAMENTO_ORDINI", value : value},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                        ok(data.msg);}else{ko(data.msg);}
            });
        });
    }

    pagefunction();
</script>