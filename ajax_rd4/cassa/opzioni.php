<?php
require_once("inc/init.php");

$ui = new SmartUI;
$page_title = "Opzioni cassa";
$page_id = "opzioni_cassa";




$a ='<div class="row">
        <div class="col-sm-6">

        <section class="col col-12">
            <p class="font-md">Copertura cassa.</p>
            <form class="smart-form well">
                <label class="checkbox margin-top-10">
                    <input name="checkbox" checked="checked" type="checkbox">
                    <i></i> Controlla il minimo di cassa
                    <p class="note">Controlla che gli utenti non possano scendere sotto il minimo di cassa quando fanno gli ordini</p>
                </label>
                <hr>
                <label class="input margin-top-10">
                    Valore del minimo di cassa:
                    <input class="parametri col col-2 pull-right" type="text" data-tipo="" name="minimo_cassa" value="'.$minimo_di_cassa.'">
                    <p class="note">Somma minima che ogni utente deve avere sul suo conto. Se scende sotto questa soglia non è possibile per lui ordinare merce</p>
                </label>
                <hr>
                <label class="input margin-top-10">
                    Percentuale per copertura cassa:
                    <input class="parametri col col-2 pull-right" type="text" data-tipo="" name="percentuale_copertura_cassa">
                    <p class="note">Percentuale che in automatico viene aggiunta all\'importo di un ordine per garantire la copertura della cassa, a fronte di spese di trasporto e di gestione non confermate.</p>
                </label>
                <hr>
                <footer>
                    <button type="submit" class="btn btn-primary pull-right">
                        Salva le modifiche
                    </button>
                </footer>

            </form>

        </section>

        </div>

        <div class="col-sm-6">
        <form class="smart-form">
        <section class="col col-12">
            <p class="label">Operatività</p>
            <label class="toggle margin-top-5">
                <input class="abilitazioni" type="checkbox" data-userid="'.$useridEnc.'" data-tipo="'.perm::puo_creare_ordini.'" name="checkbox-toggle" '.$creare_ordini_checked.'>
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>Può creare e gestire Ordini
            </label>

        </section>

        </form>
        </div>
    </div>';

$wg_scheda_cassa_opz = $ui->create_widget($options);
$wg_scheda_cassa_opz->id = "wg_scheda_opzioni_cassa";
$wg_scheda_cassa_opz->body = array("content" => $a,"class" => "");
$wg_scheda_cassa_opz->header = array(
    "title" => '<h2>Opzioni cassa</h2>',
    "icon" => 'fa fa-check'
    );





?>

<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
            <?php echo $wg_scheda_cassa_opz->print_html(); ?>
        </article>
    </div>
</section>


<script type="text/javascript">
    pageSetUp();

    var pagefunction = function(){

        //------------HELP WIDGET
        <?php echo help_render_js($page_id);?>
        //------------END HELP WIDGET

        $('.abilitazioni').change(function () {
            if(this.checked) {value = 1;}else{value = 0;}
            var id = $(this).data('userid');
            var tipo = $(this).data('tipo');
            $.ajax({
              type: "POST",
              url: "ajax_rd4/user/_act.php",
              dataType: 'json',
              data: {act: "abilitazioni", value : value, id:id, tipo:tipo},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                        ok(data.msg);}else{ko(data.msg);}
            });


        });
        $('.delete').change(function () {
            if(this.checked) {value = 1;}else{value = 0;}
            var id = $(this).data('userid');
            var tipo = $(this).data('tipo');
            $.SmartMessageBox({
                title : "Elimini questo utente?",
                content : "Questa operazione non può essere annullata (a meno che lui si ri-iscriva)",
                buttons : "[Esci][Elimina]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="Elimina"){
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/user/_act.php",
                          dataType: 'json',
                          data: {act: "del_user", id : id},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                ok(data.msg);

                            }else{
                                ko(data.msg);

                            ;}

                        });
                }
            });


        });



    }

    pagefunction();
</script>