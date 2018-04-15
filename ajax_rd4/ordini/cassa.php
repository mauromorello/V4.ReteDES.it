<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.ordine.php");
require_once("../../lib_rd4/class.rd4.gas.php");

$ui = new SmartUI;
$page_title= "Cassa ordine";
$page_id= "cassa_ordine";
$orientation = "landscape"; // portrait / landscape
//-------------------------------INIT


//-------------------------------PERM
if(!(_USER_PERMISSIONS & perm::puo_gestire_la_cassa)){
    echo rd4_go_back("Non ho i permessi per la cassa");die;
}
//------------------------------PERM

//-------------------------------PAGE
$id_ordine = CAST_TO_INT($_POST["id"],0);
if ($id_ordine==0){
    $id_ordine = CAST_TO_INT($_GET["id"],0);
}
if($id_ordine==0){echo "missing id"; die();}
if($id_gas==0){$id_gas=_USER_ID_GAS;}

//------------------------------PAGE
$O = new ordine($id_ordine);
$G = new gas($id_gas);

if (!$O->convalidato_gas){echo rd4_go_back("Ordine non ancora convalidato a livello GAS.");die;}


$valore_ordine = round(VA_ORDINE_GAS($O->id_ordini,_USER_ID_GAS),2);
$valore_cassa = round(VA_CASSA_SALDO_ORDINE_TOTALE_GAS($O->id_ordini,_USER_ID_GAS),2);

if(ABS($valore_ordine+$valore_cassa)<0.01){
//if(false){
    $allineamento = '<div class="alert alert-success">Questo ordine è già allineato con la cassa<br>
                     <span class="note">Se vuoi riallinearlo comunque clicca <span id="allinea_cassa" data-id="'.$O->id_ordini.'" style="cursor:pointer">QUA</span></span></div>';
}else{
    /*/
    CONTROLLARE SE C'E' UN AllINEAMENTO CASSA GIA' FATTO ANTECEDENTE ALLA DATA DI QUESTO ORDINE
    SE C'E' ALLORA MOSTRARE UN AVVISO "ORDINE ANTECEDENTE AL CONSOLIDAMENTO CASSA DEL XX YY ZZZZ"
    /*/
    
    
    
    
    $id_gas = _USER_ID_GAS;
    $stmt = $db->prepare("SELECT * from retegas_options WHERE chiave='_CASSA_CONSOLIDAMENTO' AND id_gas=:id_gas ORDER BY valore_data DESC LIMIT 1;");
     $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();

    if($stmt->rowCount()==1){
        //SE LA DATA DI  CHIUSURA E' ANTECEDENTE AL CONSOLIDAMENTO ALLORA SEGNALARLO
        if($O->data_chiusura_time < strtotime($row["valore_data"])){
            $allineamento = '<div class="alert alert-danger">Questo ordine è stato chiuso ('.$O->data_chiusura_time.') prima del consolidamento della cassa del tuo GAS ('.strtotime($row["valore_data"]).'), e non è più allineabile.</div>';
            $puoi_allineare = false;
        }else{
            $allineamento = '<div class="alert alert-danger">Questo ordine <strong>NON</strong> è allineato con la cassa per <strong>'._NF(ABS($valore_ordine+$valore_cassa)).'</strong> Eu.</div>';    
            $puoi_allineare = true;
        }    
    
    
    }else{
        $allineamento = '<div class="alert alert-danger">Questo ordine <strong>NON</strong> è allineato con la cassa per <strong>'._NF(ABS($valore_ordine+$valore_cassa)).'</strong> Eu.</div>';
        $puoi_allineare = true;
    }
    
    if(ABS($valore_ordine+$valore_cassa)<0.01){
        //$allineamento .= '<div class="alert alert-danger margin-top-10"><STRONG>ATTENZIONE</STRONG> Questo ordine sembra sia già allineato con la cassa.</div>';    
    }
    
}
$maggiorazione_percentuale_referenza=round($O->maggiorazione_percentuale_referenza_v2(_USER_ID_GAS),2);
if($maggiorazione_percentuale_referenza==0){
    $maggiorazione_percentuale_referenza = '';
}else{
    if(VA_ORDINE_GAS_SOLO_EXTRA_GAS($O->id_ordini, _USER_ID_GAS)>0){
        $maggiorazione_percentuale_referenza = '';
    }else{
        $maggiorazione_percentuale_referenza = '<div class="alert alert-danger">Questo ordine ha una <strong>MAGGIORAZIONE GAS</strong> prevista del <strong>'._NF($maggiorazione_percentuale_referenza).'% </strong>, ma non sono stati trovate voci corrispondenti nell\'ordine.<a class="btn btn-default pull-right btn-xs" href="#ajax_rd4/rettifiche/sconti.php?id='.$O->id_ordini.'">RETTIFICA</a><div class="clearfix"></div></div>';
    }
}

$costo_gas_referenza=round($O->costo_gas_referenza_v2(_USER_ID_GAS),2);
if($costo_gas_referenza==0){
    $costo_gas_referenza = '';
}else{
    if(VA_ORDINE_GAS_SOLO_EXTRA_GAS($o->id_ordini,_USER_ID_GAS)>0){
        $costo_gas_referenza = '';
    }else{
        $costo_gas_referenza = '<div class="alert alert-danger">Questo ordine ha un <strong>COSTO GAS</strong> previsto di <strong>'._NF($costo_gas_referenza).' Eu.</strong>, ma non sono stati trovate voci corrispondenti nell\'ordine.<a class="btn btn-default pull-right btn-xs" href="#ajax_rd4/rettifiche/altro.php?id='.$O->id_ordini.'">RETTIFICA</a><div class="clearfix"></div></div>';

    }
}
$trasporto = round($O->costo_trasporto,2);
if($trasporto==0){
    $trasporto = '';
}else{
    if(VA_ORDINE_GAS_SOLO_RETT($o->id_ordini,_USER_ID_GAS)>0){
        $trasporto = '';
    }else{
        $trasporto = '<div class="alert alert-warning">Questo ordine ha un <strong>COSTO TRASPORTO TOTALE</strong> previsto di <strong>'._NF($trasporto).' Eu.</strong>, ma probabilmente non ci sono voci corrispondenti nell\'ordine.<div class="clearfix"></div></div>';
    }
}
$gestione = round($O->costo_gestione,2);
if($gestione==0){
    $gestione = '';
}else{
    if(VA_ORDINE_GAS_SOLO_RETT($o->id_ordini,_USER_ID_GAS)>0){
        $gestione = '';
    }else{
        $gestione = '<div class="alert alert-warning">Questo ordine ha un <strong>COSTO GESTIONE TOTALE</strong> previsto di <strong>'._NF($gestione).' Eu.</strong>, ma probabilmente non ci sono voci corrispondenti nell\'ordine.<div class="clearfix"></div></div>';
    }
}

$tipo_allineamento = '<div class="alert alert-info">L\'allineamento produrrà un solo movimento di cassa per ogni utente, anche se ci sono voci extra o legate al proprio GAS.</div>';
if(_GAS_CASSA_ALLINEAMENTO_ORDINI==1){
    $tipo_allineamento = '<div class="alert alert-info">L\'allineamento produrrà due movimenti di cassa per ogni utente, se esistono voci correlate esclusivamente al proprio GAS</div>';
}
if(_GAS_CASSA_ALLINEAMENTO_ORDINI==2){
    $tipo_allineamento = '<div class="alert alert-info">L\'allineamento produrrà tre movimenti di cassa per ogni utente, se esistono voci di spesa extra o correlate al proprio GAS</div>';
}

$sql = "SELECT U.fullname, U.tel, U.userid
            FROM retegas_dettaglio_ordini D
            INNER JOIN maaking_users U ON U.userid = D.id_utenti
            WHERE D.id_ordine=:id_ordine
            AND U.id_gas=:id_gas
            GROUP BY U.userid";
$stmt = $db->prepare($sql);
$stmt->bindParam(':id_ordine', $O->id_ordini, PDO::PARAM_INT);
$stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach($rows AS $rowU){

$va_ordine_user = round(VA_ORDINE_USER($O->id_ordini,$rowU["userid"]),2);
$va_cassa_utente_ordine = round(VA_CASSA_UTENTE_ORDINE($rowU["userid"],$O->id_ordini),2);

if(abs($va_ordine_user)<>abs($va_cassa_utente_ordine)){
    $colore=" txt-color-red ";
}else{
    $colore=" txt-color-green ";
}

$h.='<div class="padding-10 well well-sm">
        <h1>#'.$rowU["userid"].' '.$rowU["fullname"].'  <small>Tel. <a href="tel:+39'.$rowU["tel"].'">'.$rowU["tel"].'</a></small></h1>
        <div class="row">
            <div class="col col-xs-6 font-md">
                    <div class="jumbotron text-center"><h1>'._NF($va_ordine_user).'</h1><p>ordine</p></div>
                    <strong>Netto: </strong>'._NF(VA_ORDINE_USER_SOLO_NETTO($O->id_ordini,$rowU["userid"])).' <small class="note">('.VA_ORDINE_USER_SOLO_NETTO_N_RIGHE($O->id_ordini,$rowU["userid"]).' righe)</small><button data-ido="'.$O->id_ordini.'" data-idu="'.$rowU["userid"].'" class="show_movimenti_user pull-right btn btn-info btn-xs"><i class="fa fa-plus"></i></button><br>
                    <strong>Rettifiche: </strong>'._NF(VA_ORDINE_USER_SOLO_RETTIFICHE($O->id_ordini,$rowU["userid"])).' <small class="note">('.VA_ORDINE_USER_SOLO_RETTIFICHE_N_RIGHE($O->id_ordini,$rowU["userid"]).' righe)</small><br>
                    <strong>Al tuo GAS: </strong>'._NF(VA_ORDINE_USER_SOLO_EXTRA_GAS($O->id_ordini,$rowU["userid"])).'<br>
                    <div class="box_movimenti_ordine" data-idu="'.$rowU["userid"].'"></div>
            </div>
            <div class="col col-xs-6 font-md '.$colore.'">
                <div class="jumbotron text-center"><h1>'._NF($va_cassa_utente_ordine).'</h1><p>cassa</p></div>
                <button data-ido="'.$O->id_ordini.'" data-idu="'.$rowU["userid"].'" class="show_cassa_user pull-right btn btn-info btn-xs"><i class="fa fa-plus"></i></button><br>
                <br>
                <strong>'.VA_CASSA_UTENTE_ORDINE_N_MOVIMENTI($rowU["userid"],$O->id_ordini).'</strong> moviment* di cassa.
                <div class="box_movimenti_cassa" data-idu="'.$rowU["userid"].'"></div>
            </div>
        </div>
     </div>
     ';


}

?>

<?php echo $O->navbar_ordine(); ?>
<?php
    if($puoi_allineare){    
    ?>
    <div class="jumbotron text-center">
        <h1>Vuoi allineare la cassa?</h1>
        <p>Trovi tutte le spiegazioni in fondo a questa pagina.</p>
        <button class="btn btn-success" id="allinea_cassa" data-id="<?php echo $O->id_ordini; ?>">Si, lo voglio.</button>
    </div>
    <?php
    }
?>

<button class="btn btn-link btn-xs pull-right delete_tutto" rel="tooltip" title="Cancella tutti i movimenti di cassa riferiti a questo ordine." data-id="<?php echo $O->id_ordini; ?>"><i class="fa fa-trash"></i></button>
<button class="btn btn-link btn-xs pull-right registra_tutto" rel="tooltip" title="Registra tutti i movimenti di cassa riferiti a questo ordine." data-id="<?php echo $O->id_ordini; ?>"><i class="fa fa-check-circle"></i></button>
<div class="clearfix"></div>
<?php echo $tipo_allineamento; ?>
<?php echo $allineamento; ?>
<?php echo $maggiorazione_percentuale_referenza; ?>
<?php echo $costo_gas_referenza; ?>
<?php echo $trasporto; ?>
<?php echo $gestione; ?>
<?php echo $h; ?>
<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
            <?php echo help_render_html($page_id,$page_title); ?>
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
        <?php echo help_render_js($page_id); ?>
        //-------------------------HELP

        $(document).on('click','#allinea_cassa',function(e){
            id = $(this).data('id');
            $.SmartMessageBox({
                title : "Sei sicuro ?",
                content : "",
                buttons : "[Esci][Allinea]"
            }, function(ButtonPress, Value) {
                if(ButtonPress=="Allinea"){
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/cassa/_act.php",
                          dataType: 'json',
                          data: {act: "allinea_ordine", id:id},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    okReload(data.msg);
                            }else{
                                    ko(data.msg);
                            }

                        });
                }
            });
            e.preventDefault();


        });

        $(document).on('click','.show_movimenti_user',function(e){
            var idu= $(this).data('idu');
            var ido= $(this).data('ido');
            $.ajax({
              type: "POST",
              url: "ajax_rd4/cassa/_act.php",
              dataType: 'json',
              data: {act: "show_movimenti_small", idu:idu, ido:ido},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                    console.log(data.idu);
                    $('.box_movimenti_ordine[data-idu="'+data.idu+'"]').html(data.html);
                }else{
                    ko(data.msg);
                }

            });

        });
        $(document).on('click','.show_cassa_user',function(e){
            var idu= $(this).data('idu');
            var ido= $(this).data('ido');
            $.ajax({
              type: "POST",
              url: "ajax_rd4/cassa/_act.php",
              dataType: 'json',
              data: {act: "show_cassa_small", idu:idu, ido:ido},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                    console.log(data.idu);
                    $('.box_movimenti_cassa[data-idu="'+data.idu+'"]').html(data.html);

                    $xeditable_cassa = $('.cassa_editable').editable({
                        url: 'ajax_rd4/cassa/_act.php',
                        type: 'text',
                        name: 'importo',
                        params: function(params) {
                            //originally params contain pk, name and value
                            //params.tipo_totale = $("input[name='operazione_per_totale']:checked").val();
                            return params;
                        },
                        title: 'Inserisci nuovo importo',
                                ajaxOptions: {
                                    dataType: 'json'
                                },
                                success: function(data, newValue) {
                                    if(data.result=="OK") {
                                         //$('.importo_edit[data-pk='+ data.id+']').text(data.qta_arr);
                                         //update_page(data);
                                         return;
                                    }else{
                                         return data.msg;
                                    }
                                }
                    });



                }else{
                    ko(data.msg);
                }

            });

        });


        $(document).on("click",".delete_movimento",function(e){
            var id= $(this).data('id');
            $.ajax({
              type: "POST",
              url: "ajax_rd4/cassa/_act.php",
              dataType: 'json',
              data: {act: "delete_movimento", id:id},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                    ok(data.msg);
                }else{
                    ko(data.msg);
                }

            });
        })
        $(document).on("click",".delete_tutto",function(e){
            var id= $(this).data('id');
            $.ajax({
              type: "POST",
              url: "ajax_rd4/cassa/_act.php",
              dataType: 'json',
              data: {act: "delete_tutto", id:id},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                    okReload(data.msg);
                }else{
                    ko(data.msg);
                }

            });
        })
        $(document).on("click",".registra_tutto",function(e){
            var id= $(this).data('id');
            $.ajax({
              type: "POST",
              url: "ajax_rd4/cassa/_act.php",
              dataType: 'json',
              data: {act: "registra_tutto", id:id},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                    ok(data.msg);
                }else{
                    ko(data.msg);
                }

            });
        })

    }
    // end pagefunction

    loadScript("js/plugin/x-editable/x-editable.min.js", pagefunction());


</script>
