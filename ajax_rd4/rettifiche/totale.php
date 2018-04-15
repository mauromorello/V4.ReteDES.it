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
    echo rd4_go_back("Ordine già convalidato");die;
}

if(livello_gestire_ordine($id_ordine)<2){
    echo rd4_go_back("Puoi solo gestire la parte del tuo GAS.");die;
}

$stmt = $db->prepare("SELECT * from retegas_ordini WHERE id_ordini=:id_ordine;");
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->execute();
        $rowo = $stmt->fetch(PDO::FETCH_ASSOC);


$page_title = "Rettifiche Totale Ordine";
$title_navbar='<i class="fa fa-pencil fa-2x pull-left"></i> Rettifica Totale ordine  #'.$id_ordine.'<br><small class="note">'.$rowo[descrizione_ordini].'</small>';


?>
<?php echo $O->navbar_ordine(); ?>

<div class="well well-sm margin-top-10">
    <p class="font-xl">Rettifica il TOTALE dell'ordine:</p>
    <div class="row">

        <div class="col-xs-4 ">
            <div class="well well-lg">
                <p>Totale ordine attuale:</p>
                <h1  class="font-xl  text-success text-center"><strong id="show_totale_ordine"><?php echo VA_ORDINE($id_ordine) ?></strong> €</h1>
            </div>
        </div>
        <div class="col-xs-8">
            <form id="rettifica_totale" class="smart-form" action="ajax_rd4/rettifiche/_act.php">
                <fieldset>
                    <section>
                    <label class="label">Nuovo Totale</label>
                        <label class="input">
                            <input type="text" class="input-lg" name="nuovo_totale" id="nuovo_totale">
                        </label>
                    </section>
                </fieldset>
                <fieldset>
                    <section>
                        <label class="label">Come gestire la differenza ?</label>
                        <div class="row">
                            <div class="col col-12">
                                <label class="radio">
                                    <input type="radio" name="tipo" value="1">
                                    <i></i>Aggiungi una riga di rettifica ad ogni utente che ha partecipato, proporzionalmente alla sua spesa</label>
                                <label class="radio">
                                    <input type="radio" name="tipo" value="3" >
                                    <i></i>Aggiungi una riga di rettifica ad ogni utente che ha partecipato, dividendola equamente tra di loro.</label>
                                <label class="radio">
                                    <input type="radio" name="tipo" value="4">
                                    <i></i>Aggiungi una riga di rettifica ad ogni utente che ha partecipato, proporzionalmente al numero degli articoli acquistati.</label>
                                <label class="radio">
                                    <input type="radio" name="tipo" value="2" checked="CHECKED">
                                    <i></i>Aggiungi una riga di rettifica solo al gestore dell'ordine.</label>

                            </div>
                        </div>
                    </section>
                    <section>
                    <label class="label">Descrizione rettifica (opzionale)</label>
                        <label class="input">
                            <input type="text" class="" name="descrizione_rettifica">
                        </label>
                    </section>
                </fieldset>
                <footer>
                    <input type="hidden"  name="id_ordine" value="<?php echo $id_ordine ?>">
                    <input type="hidden"  name="act" value="rettifica_totale">
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
            <?php echo help_render_html('rettifiche_totale',$page_title); ?>
        </article>
    </div>
</section>

<script type="text/javascript">

    pageSetUp();



    var pagefunction = function(){
        //------------HELP WIDGET
        <?php echo help_render_js('rettifiche_totale');?>
        //------------END HELP WIDGET

        var $totaleForm = $("#rettifica_totale").validate({

            rules : {
                nuovo_totale : {
                    required : true,
                    number : true
                }
            },
            messages : {
                nuovo_totale : {
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
                            $('#show_totale_ordine').html(data.nuovo_totale);
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


    } // end pagefunction

loadScript("js/plugin/jquery-form/jquery-form.min.js", pagefunction);

</script>
