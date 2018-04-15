<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.ordine.php");

$ui = new SmartUI;
$page_title= "Gestisci Ordine per il tuo GAS";
$page_id= "edit_ordine_gas";

//CONTROLLI
$id_ordine = (int)$_GET["id"];

if (!posso_gestire_ordine_come_gas($id_ordine)){
    echo rd4_go_back("Non ho i permessi necessari");die;
}

$O = new ordine($id_ordine);










$stmt = $db->prepare("select * from retegas_referenze where id_utente_referenze='"._USER_ID."' AND id_gas_referenze='"._USER_ID_GAS."' AND id_ordine_referenze=:id_ordine");
$stmt->bindParam(':id_ordine', $O->id_ordini , PDO::PARAM_INT);
$stmt->execute();
if($stmt->rowCount()>0){
    $gestoreGAS ="Gestore GAS";
    $pagina_gas ='<div class="well text-center"><a href="#ajax_rd4/ordini/report.php?id='.$id_ordine.'" class="btn btn-md btn-warning btn-block font-md">GAS..</a><br><span class="font-xs">...gestisci la parte GAS di questo ordine.</span></div>';

}else{
    $gestoreGAS ="";
    $pagina_gas ='';

}

if($O->id_gas_referente==_USER_ID_GAS){
    if(_USER_PERMISSIONS & perm::puo_vedere_tutti_ordini){
            $supervisore = "Supervisore";
    }
}

/*
if($O->is_printable<>1){
    $btn_co_gas ='<a  class="btn btn-success btn-md disabled" id="convalida_ordine_gas" data-id_ordine="'.$O->id_ordini.'">CONVALIDA GAS</a >';
}else{
    if($O->convalidato_gas<>1){
        $btn_co_gas ='<a  class="btn btn-success btn-md " id="convalida_ordine_gas" data-id_ordine="'.$O->id_ordini.'">CONVALIDA GAS</a >';
    }else{
        $btn_co_gas ='<a  class="btn btn-success btn-md " id="ripristina_ordine_gas" data-id_ordine="'.$O->id_ordini.'">RIPRISTINA GAS</a >';
    }
}
*/

if($O->is_nascosto_per_il_gas(_USER_ID_GAS)){
    $btn_nas ='<a  class="btn btn-success btn-md " id="visibila_ordine_gas" data-id_ordine="'.$O->id_ordini.'">RENDI VISIBILE</a >';
}else{
    $btn_nas ='<a  class="btn btn-warning btn-md " id="nascondi_ordine_gas" data-id_ordine="'.$O->id_ordini.'">NASCONDI</a >';
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

if($O->ingombro_per_rettifiche($id_ordine,_USER_ID_GAS)=="SI"){
    $ingombro_per_rettifiche = '<div class="alert alert-info">Questo ordine usa il campo "ingombro" degli articoli come discriminante per le rettifiche.<div class="clearfix"></div></div>';
}else{
    $ingombro_per_rettifiche = '';
}

?>
<?php echo $O->navbar_ordine(); ?>

<div class="row">
    <?php echo $maggiorazione_percentuale_referenza.$costo_gas_referenza.$ingombro_per_rettifiche;  ?>
    
    <fieldset class="col col-md-6">
        <h1>Previsione Costi <small>(Del tuo GAS)</small></h1>
        <div class="well well-sm">
        <form>

            <section class="margin-top-10">
                <label for="costo_gas">Costo GAS</label><br>
                <i class="fa fa-euro fa-2x"></i><i class="fa fa-pencil fa-2x pull-right"></i>&nbsp;&nbsp;<a class="font-xl costi" id="costo_gas" data-type="text"   data-pk="<?php echo $O->id_ordini ?>" data-original-title="Costo GAS previsto:"><?php echo$O->costo_gas_referenza_v2(_USER_ID_GAS); ?></a>
            </section>

            <section class="margin-top-10">
                <label for="maggiorazione_gas" >Maggiorazione GAS</label><br>
                <i class="fa fa-2x">%</i><i class="fa fa-pencil fa-2x pull-right"></i>&nbsp;&nbsp;<a class="font-xl costi" id="maggiorazione_gas" data-type="text"   data-pk="<?php echo $O->id_ordini ?>" data-original-title="Maggiorazione GAS prevista:"><?php echo $O->maggiorazione_percentuale_referenza_v2(_USER_ID_GAS); ?></a>
            </section>

            <section class="margin-top-10">
                <label for="motivo_maggiorazione" >Motivo Maggiorazione GAS</label><br>
                <i class="fa fa-newspaper-o fa-2x"></i><i class="fa fa-pencil fa-2x pull-right"></i>&nbsp;&nbsp;<a class="font-xl costi" id="motivo_maggiorazione" data-type="textarea"   data-pk="<?php echo $O->id_ordini ?>" data-original-title="Motivo maggiorazione GAS:"><?php echo $O->motivo_maggiorazione_v2(_USER_ID_GAS); ?></a>
            </section>
            <div class="alert alert-info margin-top-10"><b>NB: </b> i costi GAS e le maggiorazioni sono gestiti in maniera diversa rispetto alle altre versioni. Si consiglia di leggere almeno una volta l'help.</div>
        </form>
        </div>
        <h1>Operazioni GAS</h1>
        <div class="well well-sm">
                
                
                <div class="margin-top-10 ">
                    <label><i class="fa fa-warning"></i>  Nascondi / Rendi visibile</label><br>
                    <div class="btn-group btn-group-justified"><?php echo $btn_nas ?></div>
                </div>
            </section>
        </div>
    </fieldset>
    <fieldset class="col col-md-6 ">

        <h1>Gestione consegna merce <small>(Del tuo GAS)</small></h1>
            <div class="well well-sm padding-10">
            <form class="smart-form" id="distribuzione_gas" action="ajax_rd4/ordini/_act.php" method="post">

            <label class="label margin-top-5">Luogo di consegna</label>
            <label class="input"> <i class="icon-prepend fa fa-map-marker"></i>
                <input placeholder="Indirizzo" type="text" id="consegna_gas" name="consegna_gas" value="<?php echo $O->luogo_distribuzione(_USER_ID_GAS) ?>">
                <b class="tooltip tooltip-top-left">
                    <i class="fa fa-warning txt-color-teal"></i>
                    Inserisci un indirizzo valido</b>
            </label>

            <label class="label margin-top-5">Data e ora di consegna</label>
            <label class="input"> <i class="icon-prepend fa fa-calendar-o"></i>
                <input placeholder="Data e Ora" type="datetime" id="data_consegna_gas" name="data_consegna_gas" data-mask="99/99/9999 99:99" value="<?php echo conv_datetime_from_db($O->data_distribuzione_start(_USER_ID_GAS)) ?>">
                <b class="tooltip tooltip-top-left">
                    <i class="fa fa-warning txt-color-teal"></i>
                    La data e l'ora nella quale verrà distribuita la merce<br><strong>in formato GG/MM/AAAA HH:MM</strong></b>
            </label>

            <label class="label margin-top-5">Note di consegna</label>
            <label class="input"> <i class="icon-prepend fa fa-paperclip"></i>
                <input placeholder="Note" type="text" id="note_consegna_gas" name="note_consegna_gas" value="<?php echo $O->testo_distribuzione(_USER_ID_GAS) ?>">
                <b class="tooltip tooltip-top-left">
                    <i class="fa fa-warning txt-color-teal"></i>
                    Se ci sono delle note specifiche per questa consegna</b>
            </label>

            <div class="alert alert-warning hidden margin-top-10" id="indirizzo_alert"><strong>NB:</strong> L'indirizzo immesso non è stato riconosciuto.</div>

            <footer>
                <button type="submit" class="btn btn-primary" id="salva_consegna">
                    Salva le modifiche
                </button>
            </footer>


            <div id="map-canvas" class="google_maps margin-top-10" style="width:100%;"></div>
            <input id="lat" type="hidden" name="lat" value="<?php echo $O->lat_distribuzione(_USER_ID_GAS) ?>">
            <input id="lng" type="hidden" name="lng" value="<?php echo $O->lng_distribuzione(_USER_ID_GAS) ?>">
            <input id="act" type="hidden" name="act" value="distribuzione_gas">
            <input type="hidden" name="id_ordine" value="<?php echo $O->id_ordini ?>">
        </form>
       </div>

    </fieldset>
</div>
<?php echo help_render_html($page_id,$page_title); ?>

<script type="text/javascript">

    pageSetUp();


    var pagefunction = function() {
        function codeAddress() {
              var markers = [];
              geocoder = new google.maps.Geocoder();
              var location = $('#consegna_gas').val();

              if( geocoder ) {
                geocoder.geocode({ 'address': location }, function (results, status) {
                  if( status == google.maps.GeocoderStatus.OK ) {
                    map.setCenter(results[0].geometry.location);

                      for (var i = 0; i < markers.length; i++) {
                        markers[i].setMap(null);
                      }

                      var marker = new google.maps.Marker({
                          map: map,
                          position: results[0].geometry.location
                      });
                      markers.push(marker);

                      if (results[0].geometry.location.lat()!=0){
                        $('#lat').val (results[0].geometry.location.lat());
                        $('#lng').val (results[0].geometry.location.lng());
                        $('#indirizzo_alert').hide();
                        console.log("Code address Riconosciuto per " + $('#consegna_gas').val());
                      }else{
                          $('#lat').val (0);
                          $('#lng').val (0);
                          $('#indirizzo_alert').show();
                          console.log("Code address NON Riconosciuto per " + $('#consegna_gas').val());
                      }




                  }else{
                     $('#lat').val (0);
                     $('#lng').val (0);
                     $('#indirizzo_alert').show();

                  }
                });
              }

        }
        //-------------------------HELP
        //document.title = escape('<?php echo "ReteDES.it :: ".$O->descrizione_ordini;?>');
        <?php echo help_render_js($page_id); ?>
        //-------------------------HELP

        $.fn.editable.defaults.url = 'ajax_rd4/ordini/_act.php';


        $('.costi').editable({
                ajaxOptions: { dataType: 'json' },
                success: function(response, newValue) {
                        console.log(response);
                        if(response.result == 'KO'){
                            return response.msg;
                        }
                    }
            });



        var $distribuzione_gas = $('#distribuzione_gas').validate({

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

            // Do not change code below
            errorPlacement : function(error, element) {
                error.insertAfter(element.parent());
            }
        });

        var geocoder;
        var map;
        function initialize() {
          geocoder = new google.maps.Geocoder();
          var latlng = new google.maps.LatLng(<?php echo _USER_LAT.","._USER_LNG; ?>);
          var mapOptions = {
            zoom: 12,
            center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
          }
          map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
          console.log("Fine mappa initialized");


        }

        initialize();
        codeAddress();
        $('#consegna_gas').change(function(){
            codeAddress();
        })
        loadScript("js/plugin/jquery-form/jquery-form.min.js");

        $("#visibila_ordine_gas").click(function(e) {
            var id_ordine=$(this).data("id_ordine");
            $.SmartMessageBox({
                title : "Rendi visibile questo ordine per il tuo GAS",
                content : "Gli utenti del tuo gas lo vedranno nella lista degli ordini aperti",
                buttons : "[Annulla][OK]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="OK"){
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/ordini/_act.php",
                          dataType: 'json',
                          data: {act: "visibila_ordine_gas", id_ordine:id_ordine},
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
        
        $("#nascondi_ordine_gas").click(function(e) {
            var id_ordine=$(this).data("id_ordine");
            $.SmartMessageBox({
                title : "Nascondi questo ordine per il tuo GAS",
                content : "Se non gradisci che questo ordine venga visto dagli utenti del tuo GAS",
                buttons : "[Annulla][OK]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="OK"){
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/ordini/_act.php",
                          dataType: 'json',
                          data: {act: "nascondi_ordine_gas", id_ordine:id_ordine},
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
        $("#ripristina_ordine_gas").click(function(e) {
            var id_ordine=$(this).data("id_ordine");
            $.SmartMessageBox({
                title : "Ripristina questo ordine",
                content : "Il ripristino dell\'ordine a livello GAS serve a correggere eventuali errori.",
                buttons : "[Annulla][OK]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="OK"){
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/ordini/_act.php",
                          dataType: 'json',
                          data: {act: "ripristina_ordine_gas", id_ordine:id_ordine},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    okReload(data.msg);}else{ko(data.msg);}

                        });
                }
            });

            e.preventDefault();
        })

    }
    // end pagefunction
    $(window).unbind('gMapsLoaded');
    $(window).bind('gMapsLoaded',loadMa);
    window.loadGoogleMaps();

    function loadMa(){
        loadScript("js/plugin/x-editable/moment.min.js", loadXEditable);
    }

    function loadXEditable(){
        loadScript("js/plugin/x-editable/x-editable.min.js", loadSummerNote);
    }
    function loadSummerNote(){
        loadScript("js/plugin/summernote/summernote.min.js", pagefunction)
    }

</script>
