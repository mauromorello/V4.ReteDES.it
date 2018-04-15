<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.gas.php");
$ui = new SmartUI;
$converter = new Encryption;

$page_title = "Opzioni per gli ordini";
$page_id ="opzioni_ordini";

$G = new gas(_USER_ID_GAS);
$id_gas=_USER_ID_GAS;

if((_USER_PERMISSIONS & perm::puo_vedere_tutti_ordini)){

}else{
    rd4_go_back("Non puoi...");
}


if(_USER_GAS_USA_CASSA){
    $gas_usa_cassa = ' CHECKED="CHECKED" ';
}else{
    $gas_usa_cassa = ' ';
}

if(_USER_GAS_VISIONE_CONDIVISA){
    $gas_visione_condivisa = ' CHECKED="CHECKED" ';
}else{
    $gas_visione_condivisa = ' ';
}

if(_USER_GAS_VISIONE_DATI_UTENTI){
    $gas_visione_dati_utenti = ' CHECKED="CHECKED" ';
}else{
    $gas_visione_dati_utenti = ' ';
}

if(_USER_GAS_PUO_PART_ORD_EST){
    $gas_part_ord_est = ' CHECKED="CHECKED" ';
}else{
    $gas_part_ord_est = ' ';
}
if(_USER_GAS_PUO_COND_ORD_EST){
    $gas_cond_ord_est = ' CHECKED="CHECKED" ';
}else{
    $gas_cond_ord_est = ' ';
}


$op1='<form class="smart-form row">
        <section class="col col-sm-12">

            <p class="label">Gestione condivisione <strong>ordini</strong>:</p>
            <label class="toggle font-sm">
                <input class="gas_option" type="checkbox"  data-act="gas_part_ord_est" name="checkbox-toggle" '.$gas_part_ord_est.'>
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>Il tuo GAS può partecipare ad ordini aperti da altri GAS
            </label>
            <label class="toggle font-sm">
                <input class="gas_option" type="checkbox"  data-act="gas_cond_ord_est" name="checkbox-toggle" '.$gas_cond_ord_est.'>
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>Il tuo GAS può condividere propri ordini con altri GAS
            </label>
        </section>
        <hr />
        <section class="col col-sm-12">
            <p class="label">Uso dello strumento <strong>cassa</strong>: </p>
            <label class="toggle font-sm">
                <input class="gas_option" data-act="gas_option_cassa"  type="checkbox"  name="checkbox-toggle" '.$gas_usa_cassa.'>
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>Il tuo gas usa la cassa
            </label>
        </section>
        <hr />
        </form>';


$op4='<form class="smart-form row">
        <section class="col col-sm-12 ">
            <p class="label">Visibilità <strong>ordini</strong> condivisa:</p>
            <label class="toggle font-sm">
                <input  class="gas_option" type="checkbox" data-act="gas_option_visione_condivisa" name="checkbox-toggle" '.$gas_visione_condivisa.'>
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>Ogni utente può vedere la merce acquistata da altri (dello stesso gas)
            </label>
        </section>
        <section class="col col-sm-12 ">
            <p class="label">Dati <strong>utenti</strong> condivisi:</p>
            <label class="toggle font-sm">
                <input  class="gas_option" type="checkbox" data-act="gas_option_visione_dati_utenti" name="checkbox-toggle" '.$gas_visione_dati_utenti.'>
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>Ogni utente può vedere i dati degli altri utenti (dello stesso gas)
            </label>
        </section>
        <hr />
      </form>';

$stmt = $db->prepare("SELECT valore_int FROM retegas_options WHERE id_gas=:id_gas AND chiave='_GAS_REPORT_SHOW_ID' LIMIT 1;");
$stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch();
if($row["valore_int"]>0){
    $show_id=' checked="checked" ';
}else{
    $show_id='';
}

$stmt = $db->prepare("SELECT valore_int FROM retegas_options WHERE id_gas=:id_gas AND chiave='_GAS_REPORT_SHOW_TEL' LIMIT 1;");
$stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch();
if($row["valore_int"]>0){
    $show_tel=' checked="checked" ';
}else{
    $show_tel='';
}

$stmt = $db->prepare("SELECT valore_int FROM retegas_options WHERE id_gas=:id_gas AND chiave='_GAS_REPORT_SHOW_INDIRIZZO' LIMIT 1;");
$stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch();
if($row["valore_int"]>0){
    $show_indirizzo=' checked="checked" ';
}else{
    $show_indirizzo='';
}


$stmt = $db->prepare("SELECT valore_int FROM retegas_options WHERE id_gas=:id_gas AND chiave='_GAS_REPORT_SHOW_TESSERA' LIMIT 1;");
$stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch();
if($row["valore_int"]>0){
    $show_tessera=' checked="checked" ';
}else{
    $show_tessera='';
}

$stmt = $db->prepare("SELECT valore_int FROM retegas_options WHERE id_gas=:id_gas AND chiave='_GAS_REPORT_SHOW_CASSA' LIMIT 1;");
$stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch();
if($row["valore_int"]>0){
    $show_cassa=' checked="checked" ';
}else{
    $show_cassa='';
}

$op5='<form class="smart-form row">
        <section class="col col-sm-12 ">
            <p class="label"><strong>Nome utente</strong></p>
            <label class="toggle font-sm">
                <input  class="report_option" type="checkbox" name="checkbox-toggle" checked="checked">
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>Questa opzione non può essere disattivata
            </label>
        </section>
        <section class="col col-sm-12 ">
            <p class="label"><strong>ID utente di retedes</strong></p>
            <label class="toggle font-sm">
                <input '.$show_id.'  class="report_option" type="checkbox" data-act="report_show_id" name="checkbox-toggle">
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>E\' quel numero che reteDES assegna ad ogni nuovo utente all\'atto della sua iscrizione.
            </label>
        </section>
        <section class="col col-sm-12 ">
            <p class="label"><strong>Telefono</strong></p>
            <label class="toggle font-sm">
                <input  '.$show_tel.' class="report_option" type="checkbox" data-act="report_show_tel" name="checkbox-toggle">
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>Se si vuole o non si vuole mostrare il telefono accanto al nome.
            </label>
        </section>
        <section class="col col-sm-12 ">
            <p class="label"><strong>Indirizzo</strong></p>
            <label class="toggle font-sm">
                <input  '.$show_indirizzo.' class="report_option" type="checkbox" data-act="report_show_indirizzo" name="checkbox-toggle">
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>Se si vuole o non si vuole mostrare l\'indirizzo accanto al nome.
            </label>
        </section>
        <section class="col col-sm-12 ">
            <p class="label"><strong>Tessera</strong></p>
            <label class="toggle font-sm">
                <input  '.$show_tessera.' class="report_option" type="checkbox" data-act="report_show_tessera" name="checkbox-toggle">
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>Se il proprio gas usa un sistema interno per identificare gli utenti.
            </label>
        </section>
        <section class="col col-sm-12 ">
            <p class="label"><strong>Cassa</strong></p>
            <label class="toggle font-sm">
                <input  '.$show_cassa.' class="report_option" type="checkbox" data-act="report_show_cassa" name="checkbox-toggle">
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>Mostra o meno il saldo cassa dell\'utente.
            </label>
        </section>
        <hr />
      </form>';

if(_USER_GAS_ORDINAMENTO_COMPRA==0){$selected_0=' selected="selected" ';}
if(_USER_GAS_ORDINAMENTO_COMPRA==1){$selected_1=' selected="selected" ';}
if(_USER_GAS_ORDINAMENTO_COMPRA==2){$selected_2=' selected="selected" ';}
if(_USER_GAS_ORDINAMENTO_COMPRA==3){$selected_3=' selected="selected" ';}
if(_USER_GAS_ORDINAMENTO_COMPRA==4){$selected_4=' selected="selected" ';}
$op21='
        <form class="smart-form row">
        <section class="col col-sm-12">

            <label class="label">Ordinamento nella pagina "compra":</label>
                                    <label class="select">
                                        <select id="select_ordinamento_compra">
                                            <option value="0" '.$selected_0.' >Valore ordine, codice (default)</option>
                                            <option value="1" '.$selected_1.' >Valore ordine, descrizione</option>
                                            <option value="2" '.$selected_2.' >codice</option>
                                            <option value="3" '.$selected_3.' >descrizione</option>
                                            <option value="4" '.$selected_4.' >Tag 1</option>
                                        </select> <i></i> </label>
        <p class="note">Scegli il tipo di ordinamento da utilizzare nella pagina "compra"<p>
        </section>
        <hr />
        </form>';




$converter = new Encryption;




?>
<?php echo $G->render_toolbar("Opzioni ordini");?>


<div class="row margin-top-10">
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        
       <h1>Parametri per gli ordini</h1>
       <form class="smart-form" id="parametri_ordini_gas" action="ajax_rd4/gas/_act.php" method="post">
        <div class="well well-sm padding-10">
            <p class="alert alert-danger"><strong>NB:</strong> la maggiorazione ordini funziona in modo diverso rispetto alle versioni 2 e 3. Leggere il relativo HELP.</p>
            <label class="label margin-top-5">Percentuale di maggiorazione ordini</label>
            <label class="input"> <i class="icon-prepend fa fa-plus-square"></i>
                <input placeholder="" type="text" id="maggiorazione_ordini" name="maggiorazione_ordini" value="<?php echo $G->maggiorazione_ordini ?>">
                <b class="tooltip tooltip-top-left">
                    <i class="fa fa-warning txt-color-teal"></i>
                    Percentuale di maggiorazione ordini</b>
            </label>
            <label class="label margin-top-5">Motivo della maggiorazione</label>
            <label class="input"> <i class="icon-prepend fa fa-pencil-square-o"></i>
                <input placeholder="" type="text" id="comunicazione_referenti" name="comunicazione_referenti" value="<?php echo $G->comunicazione_referenti ?>">
                <b class="tooltip tooltip-top-left">
                    <i class="fa fa-warning txt-color-teal"></i>
                    Motivo della maggiorazione</b>
            </label>
            <input id="act" type="hidden" name="act" value="parametri_ordini_gas">
        <footer>
                <button type="submit" class="btn btn-primary" id="save_parametri">
                    Salva i parametri
                </button>
        </footer>

       </div>
       </form>

       <h1>Varie</h1>
        <div class="well well-sm">
        <?php if(_USER_PERMISSIONS & perm::puo_vedere_tutti_ordini){echo $op21;}else{echo '<h3>Non hai i permessi per gestire le opzioni del tuo GAS</h3>';} ?>
        </div>
       
    </div>

    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        

        <h1>Parametri report</h1>
        <div class="well well-sm">
        <?php if(_USER_PERMISSIONS & perm::puo_vedere_tutti_ordini){echo $op5;}else{echo '<h3>Non hai i permessi per gestire le opzioni del tuo GAS</h3>';} ?>
        </div>
        
        
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

        var id;
        var messaggio;

        console.log("Inizio Initialized");

        $('.report_option').change(function (e) {
            var checkbox = $(this);
            if(this.checked) {var value = 1;}else{var value = 0;}
            $.SmartMessageBox({
                title : "Modifichi questa opzione ?",
                content : "<b>Attenzione:</b> alcune modifiche influiranno su quello che gli utenti del tuo gas potranno fare o vedere. ",
                buttons : "[Cancella][Procedi]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="Procedi"){
                    var act = $(checkbox).data('act');
                    $.ajax({
                      type: "POST",
                      url: "ajax_rd4/gas/_act.php",
                      dataType: 'json',
                      data: {act: act, value : value},
                      context: document.body
                    }).done(function(data) {
                        if(data.result=="OK"){
                            ok(data.msg);
                        }else{
                            ko(data.msg);
                            $(checkbox).prop('checked', !checkbox.checked);
                        }
                    });
                }else{
                    console.log("cancella");
                    $(checkbox).prop('checked', !checkbox.checked);
                }
            });
        });

        
        $('#select_ordinamento_compra').change(function (e) {
            var select = $(this);
            var value = select.val();
            $.SmartMessageBox({
                title : "Modifichi questa opzione ?",
                content : "",
                buttons : "[Cancella][Procedi]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="Procedi"){
                    var act = 'ordinamento_pagina_compra';
                    $.ajax({
                      type: "POST",
                      url: "ajax_rd4/gas/_act.php",
                      dataType: 'json',
                      data: {act: act, value : value},
                      context: document.body
                    }).done(function(data) {
                        if(data.result=="OK"){
                            ok(data.msg);
                        }else{
                            ko(data.msg);
                        }
                    });
                }else{
                    console.log("cancella");
                }
            });
        });

        var $parametri_ordini_gas = $('#parametri_ordini_gas').validate({
            submitHandler : function(form) {
                    $(form).ajaxSubmit({
                        type:"POST",
                        dataType: 'json',
                        success : function(data) {
                                if(data.result=="OK"){
                                    ok(data.msg);}else{ko(data.msg);}
                                }
                    });
            },
            errorPlacement : function(error, element) {
                error.insertAfter(element.parent());
            }
        });


        loadScript("js/plugin/jquery-form/jquery-form.min.js");
        

    } // end pagefunction




    loadScript("js/plugin/summernote/new_summernote.min.js", function(){
        loadScript("js/plugin/x-editable/x-editable.min.js", pagefunction);
    });
</script>