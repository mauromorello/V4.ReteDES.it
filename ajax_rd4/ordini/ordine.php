<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.ordine.php");

$ui = new SmartUI;
$page_title= "Scheda ordine";
//CONTROLLI
$id_ordine = CAST_TO_INT($_GET["id"],0);
$O = new ordine($id_ordine);
    $gestore=false;
    $link_gestore =='';
    $pulsante_gestisci ='';

    if (posso_gestire_ordine($O->id_ordini)){
        if($O->id_gas_referente==_USER_ID_GAS){
             $pulsante_gestisci ='<a href="#ajax_rd4/ordini/edit.php?id='.$id_ordine.'" class="btn btn-default btn-block m-bottom-10"><i class="fa fa-gears pull-left"></i><b>GESTISCI</b><br>tutto l\'ordine</a>';
             $gestore=true;
             $link_gestore = '<a href="#ajax_rd4/ordini/edit.php?id='.$O->id_ordini.'"><i class="fa fa-gears"></i></a>';
        }
    }

    $rows = $O->lista_referenti_extra();
    if(count($rows)>0){
        foreach($rows as $row){
           $gestori_extra .= '<li>
                            <span class="read">
                                <span class="pull-right text-success"><i class="fa fa-briefcase"></i></span>
                                <a href="javascript:void(0);" class="msg">
                                    <img src="'.src_user($row["id_user"]).'" alt="" class="air air-top-left margin-top-5" width="32" height="32" />
                                    <span class="subject"><strong>'.$row["fullname"].'</strong></span>
                                    <span class="subject font-xs">'.$row["descrizione_gas"].'</span>
                                    <span class="msg-body font-xs">Aiuto Referente</span>
                                </a>
                            </span>
                        </li>';
        }

    }
    $referente_ordine ='<li>
                        <span class="read">
                            <span class="pull-right text-success"><i class="fa fa-graduation-cap  fa-2x"></i></span>
                            <a href="javascript:void(0);" class="msg">
                                <img src="'.src_user($O->id_utente).'" alt="" class="air air-top-left margin-top-5" width="32" height="32" />
                                <span class="subject"><strong>'.$O->fullname_referente.'</strong></span>
                                <span class="subject font-xs">'.$O->descrizione_gas_referente.'</span>
                                <span class="msg-body font-xs"><strong>Referente ordine</strong></span>
                            </a>
                        </span>
                    </li>';
    $rowG = $O->referente_ordine_gas(_USER_ID_GAS);
    $pulsante_gestisci_per_gas='';
    if($rowG["userid"]>0){
        $referente_ordine_gas ='<li>
                            <span class="read">
                                <span class="pull-right text-success"><i class="fa fa-graduation-cap"></i></span>
                                <a href="javascript:void(0);" class="msg">
                                    <img src="'.src_user($rowG["userid"]).'" alt="" class="air air-top-left margin-top-5" width="32" height="32" />
                                    <span class="subject"><strong>'.$rowG["fullname"].'</strong></span>
                                    <span class="subject font-xs">'.$rowG["descrizione_gas"].'</span>
                                    <span class="msg-body font-xs"><strong>Referente per il tuo GAS</strong></span>
                                </a>
                            </span>
                        </li>';
        $pulsante_offerta_referente='';

        //Sono il referente GAS
        if($rowG["userid"]==_USER_ID){
            $pulsante_gestisci_per_gas='<a href="#ajax_rd4/ordini/edit.php?id='.$id_ordine.'" class="btn btn-default btn-block" id="gestisci_per_gas"><i class="fa fa-gear  pull-left"></i><b>GESTISCI</b><br>per il tuo GAS</a><hr>';
        }

        if($O->codice_stato<>"CO"){
            $pulsante_aiuta='<button class="btn btn-default btn-block" id="offri_aiuto"><i class="fa fa-hand-o-up  pull-left"></i>  OFFRI IL TUO AIUTO</button><hr>';
        }else{
            $pulsante_aiuta='';
        }

    }else{
        $referente_ordine_gas ='';
        $pulsante_aiuta='';

        //solo se è aperto
        if($O->codice_stato=="AP"){
            $pulsante_offerta_referente='<button class="btn btn-default btn-block" id="diventa_referente"><i class="fa fa-graduation-cap  pull-left"></i>  <strong>DIVENTA REFERENTE</strong><br> PER IL TUO GAS</button><hr>';
        }else{
            $pulsante_offerta_referente='';
        }
    }



$rows = $O->lista_aiuti_ordine();
if(count($rows)>0){
    foreach($rows as $row){
        if($row["id_user"]==_USER_ID){$pulsante_aiuta='';}
        if($row["valore_int"]==1){
            $aiuti .= '<li>
                            <span class="read">
                                <span class="pull-right text-success"><i class="fa fa-wrench "></i></span>
                                <a href="javascript:void(0);" class="msg">
                                    <img src="'.src_user($row["id_user"]).'" alt="" class="air air-top-left margin-top-5" width="32" height="32" />
                                    <span class="subject"><strong>'.$row["fullname"].'</strong></span>
                                    <span class="subject font-xs">'.$row["descrizione_gas"].'</span>
                                    <span class="msg-body font-xs">Umile aiutante</span>
                                </a>
                            </span>
                        </li>';
        }else{
           $aiuti .= '<li>
                            <span class="read">
                                <span class="pull-right text-danger"><i class="fa fa-wrench "></i></span>
                                <a href="javascript:void(0);" class="msg">
                                    <img src="'.src_user($row["id_user"]).'" alt="" class="air air-top-left margin-top-5" width="32" height="32" />
                                    <span class="subject text-muted"><strong>'.$row["fullname"].'</strong></span>
                                    <span class="subject font-xs">'.$row["descrizione_gas"].'</span>
                                    <span class="msg-body font-xs">Vorrebbe aiutare...</span>
                                </a>
                            </span>
                        </li>';
        }
    }

}
$r = $O->lista_referenze(_USER_ID_GAS);
$o='
        <div class="row">
            <div class="col-md-6">
            <p class="font-lg text-center padding-10">Informazioni:</p>
                <div class="well well-sm">

                    <p>Apre: <strong>'.$O->data_apertura_lunga.'</strong></p>
                    <p>Chiude: <strong>'.$O->data_chiusura_lunga.'</strong></p>
                    <p>Consegna: '.$r["data_distribuzione_start"].'</p>
                    <p>Luogo: '.$r["luogo_distribuzione"].'</p>
                </div>
            </div>
            <div class="col-md-6" style="max-height:320px;overflow:auto;">
                <p class="font-lg text-center padding-10">Gestori: </p>
                <div class="well well-sm no-padding">

                <ul class="notification-body no-padding margin-top-10">
                    '.$referente_ordine.'
                    '.$referente_ordine_gas.'
                    '.$gestori_extra.'
                    '.$aiuti.'
                </ul>
                </div>
            </div>
        </div>
    ';

    if($O->costo_trasporto>0){$msg_trasporto = '<div class="alert alert-info">Il referente prevede un costo di trasporto merce di circa <strong>'.round($O->costo_trasporto,2).' €</strong> che verrà ripartito tra i partecipanti.</div>';}
    if($O->costo_gestione>0){$msg_gestione = '<div class="alert alert-info">Il referente prevede un costo di gestione ordine di circa '.$O->costo_gestione.' che verrà ripartito tra i partecipanti.</div>';}


?>
<?php echo $O->navbar_ordine().$msg_trasporto.$msg_gestione; ?>
<div class="row margin-top-10">
    <div class="col-md-4">
        <div class="well text-center"><a href="#ajax_rd4/ordini/report.php?id=<?php echo $id_ordine; ?>" class="btn btn-lg btn-primary btn-block disabled"><i class="fa fa-2x fa-shopping-cart pull-left"></i><p></p>COMPRA...</a><br><span class="font-xs"> ... scegliendo tra gli articoli disponibili.</span></div>
    </div>
    <div class="col-md-4">
        <div class=" well text-center"><a href="#ajax_rd4/ordini/report.php?id=<?php echo $id_ordine; ?>" class="btn btn-lg btn-success btn-block font-lg"><i class="fa fa-2x fa-eye pull-left"></i><p></p>VEDI..</a><br><span class="font-xs">...la tua spesa e gli articoli acquistati</span></div>
    </div>
    <div class="col-md-4">
        <?php echo $pulsante_gestisci;
        //SEmailMulti($O->EMAIL_lista_potenziali_partecipanti(_USER_ID_GAS),$fullnameFROM,$mailFROM,$oggetto,$messaggio)

         ?>
        <?php echo $pulsante_gestisci_per_gas; ?>
        <?php echo $pulsante_offerta_referente; ?>
        <?php echo $pulsante_aiuta; ?>
    </div>

</div>


<?php echo $o;?>

<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html("scheda_ordine",$page_title); ?>
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


    var pagefunction = function() {

        //-------------------------HELP
        //document.title = '<?php echo "ReteDES.it :: ".$O->descrizione_ordini;?>';
        <?php echo help_render_js("scheda_ordine"); ?>
        //-------------------------HELP

        $('#go_gestisci').click(function(){
            container = $('#content');
            var $this   = $(this)
            var id = $this.data('id_ordine');
            loadURL('ajax_rd4/ordini/edit.php?id='+id,container);
        })
        $("#offri_aiuto").click(function(e) {

            $.SmartMessageBox({
                title : "Offri il tuo aiuto ?",
                content : "Descrivi (brevemente) in cosa potrai essere d'aiuto. La richiesta verrà inviata al referente.",
                buttons : "[Annulla][OK]",
                input : "text",
                placeholder : "Scrivi qua...",
                inputValue: ''
            }, function(ButtonPress, Value) {

                if(ButtonPress=="OK"){
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/ordini/_act.php",
                          dataType: 'json',
                          data: {act: "offerta_aiuto", value:Value, id_ordine:<?php echo $O->id_ordini; ?>},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    ok(data.msg);}else{ko(data.msg);}

                        });
                }
            });

            e.preventDefault();
        });
        $("#diventa_referente").click(function(e) {

            $.SmartMessageBox({
                title : "Diventi referente per il tuo GAS ?",
                content : "In breve: farai da ponte tra il gestore dell'ordine ed il tuo gas. Leggi l'aiuto in fondo alla pagina per tutte le informazioni su cosa comporta questo incarico.",
                buttons : "[Annulla][OK]"
            }, function(ButtonPress, Value) {
                if(ButtonPress=="OK"){
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/ordini/_act.php",
                          dataType: 'json',
                          data: {act: "diventa_referente", id_ordine:<?php echo $O->id_ordini; ?>},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                ok(data.msg);
                            }else{
                                ko(data.msg);
                            }

                        });
                }
            });

            e.preventDefault();
        });
    }
    // end pagefunction

    pagefunction();



</script>
