<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.gas.php");
$ui = new SmartUI;
$converter = new Encryption;

$page_title = "Opzioni del mio GAS";
$page_id ="opzioni_gas";

$G = new gas(_USER_ID_GAS);
$id_gas=_USER_ID_GAS;

if((_USER_PERMISSIONS & perm::puo_creare_gas) OR (_USER_ID==$G->id_referente_gas) OR (_USER_PERMISSIONS & perm::puo_vedere_retegas)){

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

if(_USER_GAS_VISIONE_DATI_AMICI){
    $gas_visione_dati_amici = ' CHECKED="CHECKED" ';
}else{
    $gas_visione_dati_amici = ' ';
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
if(_USER_GAS_PERM_GEST_EST){
    $gas_perm_gest_est = ' CHECKED="CHECKED" ';
}else{
    $gas_perm_gest_est = ' ';
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
            <label class="toggle font-sm">
                <input class="gas_option" type="checkbox"  data-act="gas_perm_gest_est" name="checkbox-toggle" '.$gas_perm_gest_est.'>
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>Il tuo GAS permette ai gas esterni di assegnare il referente ordine quando si è inseriti in un ordine condiviso.
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
        <section class="col col-sm-12 ">
            <p class="label">Dati <strong>"Amici"</strong> visibili:</p>
            <label class="toggle font-sm">
                <input  class="gas_option" type="checkbox" data-act="gas_option_visione_dati_amici" name="checkbox-toggle" '.$gas_visione_dati_amici.'>
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>I referenti possono vedere i dettagli ordini degli "amici" degli utenti.
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
            <p class="label">Nome utente</p>
            <label class="toggle font-sm">
                <input  class="report_option" type="checkbox" name="checkbox-toggle" checked="checked">
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>Questa opzione non può essere disattivata
            </label>
        </section>
        <section class="col col-sm-12 ">
            <p class="label">ID utente di retedes</p>
            <label class="toggle font-sm">
                <input '.$show_id.'  class="report_option" type="checkbox" data-act="report_show_id" name="checkbox-toggle">
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>E\' quel numero che reteDES assegna ad ogni nuovo utente all\'atto della sua iscrizione.
            </label>
        </section>
        <section class="col col-sm-12 ">
            <p class="label">Telefono</p>
            <label class="toggle font-sm">
                <input  '.$show_tel.' class="report_option" type="checkbox" data-act="report_show_tel" name="checkbox-toggle">
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>Se si vuole o non si vuole mostrare il telefono accanto al nome.
            </label>
        </section>
        <section class="col col-sm-12 ">
            <p class="label">Indirizzo</p>
            <label class="toggle font-sm">
                <input  '.$show_indirizzo.' class="report_option" type="checkbox" data-act="report_show_indirizzo" name="checkbox-toggle">
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>Se si vuole o non si vuole mostrare l\'indirizzo accanto al nome.
            </label>
        </section>
        <section class="col col-sm-12 ">
            <p class="label">Tessera</p>
            <label class="toggle font-sm">
                <input  '.$show_tessera.' class="report_option" type="checkbox" data-act="report_show_tessera" name="checkbox-toggle">
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>Se il proprio gas usa un sistema interno per identificare gli utenti.
            </label>
        </section>
        <section class="col col-sm-12 ">
            <p class="label">Cassa</p>
            <label class="toggle font-sm">
                <input  '.$show_cassa.' class="report_option" type="checkbox" data-act="report_show_cassa" name="checkbox-toggle">
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>Mostra o meno il saldo cassa dell\'utente.
            </label>
        </section>
        <hr />
      </form>';


if(_GAS_VISUALIZZAZIONE_MAIL_ORDINE==0){$selected_2=' selected="selected" ';}
if(_GAS_VISUALIZZAZIONE_MAIL_ORDINE==3){$selected_3=' selected="selected" ';}
if(_GAS_VISUALIZZAZIONE_MAIL_ORDINE==4){$selected_4=' selected="selected" ';}

$op2='
        <form class="smart-form row">
        <section class="col col-sm-12">

            <label class="label">Versione alla quale puntano i link:</label>
                                    <label class="select">
                                        <select id="select_versione_mail">
                                            <option value="0" '.$selected_2.' disabled="DISABLED">Versione 2</option>
                                            <option value="3" '.$selected_3.' >Versione 3</option>
                                            <option value="4" '.$selected_4.' >Versione 4</option>
                                        </select> <i></i> </label>
        <p class="note">Scegli a quale versione linkare la mail di avviso apertura ordine; Se scegli la versione 3 si aprirà la pagina principale, e non quella specifica dell\'ordine<p>
        </section>
        <hr />
        </form>';


$op_wp='
        <form class="smart-form row">
        <section class="col col-sm-12">
        <label class="label margin-top-5">Codice assegnato al tuo GAS:</label>
            <label class="input"> <i class="icon-prepend fa fa-wordpress"></i>
                <input placeholder="" type="text" id="wp_code" name="wp_code" value="'.$G->get_wp_code().'">
                <b class="tooltip tooltip-top-left">
                    <i class="fa fa-warning txt-color-teal"></i>
                    Leggi l\'help per le spiegazioni</b>
            </label>
            <br>
            <p>
            <a class="btn btn-link"  href="'.APP_URL.'/public_rd4/wp/retedes.zip"><i class="fa fa-download"></i>   Scarica qua il plugin "RETEDES per WORDPRESS"</a>
            </p>
        </section>


        </form>';

$converter = new Encryption;
//LISTA GEO USERS
$stmt = $db->prepare("SELECT * FROM maaking_users WHERE (city<>'') AND (user_gc_lat > 0) AND (id_gas='"._USER_ID_GAS."') AND isactive=1;");
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $row) {
      $useridEnc = $converter->encode($row["userid"]);
      $infowindow= '<a href=\"'.APP_URL.'/#ajax_rd4/user/scheda.php?id='.$useridEnc.'\" target=\"_BLANK\">'.$row["fullname"].'</a><br>'.$row["city"];

      $geo_users .='["'.$infowindow.'", '.$row["user_gc_lat"].', '.$row["user_gc_lng"].',1], ';
}
$geo_users = rtrim($geo_users,", ");

//IMMAGINE
$immagine='<div class="margin-top-10">
                <div class="polaroid-images pull-right">
                    <a href="javascript:void(0)" class="fileinput-button"><img SRC="'.src_gas(_USER_ID_GAS,240).'" id="img_gas" class="" alt="'._USER_GAS_NOME.'" style="height:128px;width:128px"></img></a>
                </div>
                <div class="clearfix"></div>
                <div class="progress progress-micro margin-top-10">
                            <div class="progress-bar progress-bar-primary" role="progressbar" style="width: 0;" id="loadingprogress"></div>
                </div>
                <div>
                    <form class="smart-form pull-right">
                    <section>
                        <label class="label-input" for="colorsel">Colore di sfondo
                            <input id="colorsel" value="#FFFFFF" class="color text-right input-md">
                        </label>
                    </section>
                    </form>
                </div>
            </div>
            ';


?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.1/summernote.css" rel="stylesheet">
<?php echo $G->render_toolbar();?>


<div class="row margin-top-10">
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        <h1>Immagine GAS</h1>
        <?php if((_USER_PERMISSIONS & perm::puo_creare_gas) OR (_USER_ID==$G->id_referente_gas) OR (_USER_PERMISSIONS & perm::puo_vedere_retegas)){echo $immagine;}else{echo '<h3>Non hai i permessi</h3>';} ?>
        <div class="clearfix"></div>
        <h1>Opzioni di operatività</h1>
        <div class="well well-sm">
        <?php if(_USER_PERMISSIONS & perm::puo_creare_gas){echo $op1;}else{echo '<h3>Non hai i permessi per gestire le opzioni del tuo GAS</h3>';}?>
        </div>


    </div>

    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">

        <h1>Vari dati del tuo GAS</h1>
         <form class="smart-form" id="anagrafiche_gas" action="ajax_rd4/gas/_act.php" method="post">
        <div class="well well-sm padding-10">
            <label class="label margin-top-5">Nome GAS</label>
            <label class="input"> <i class="icon-prepend fa fa-home"></i>
                <input placeholder="" type="text" id="descrizione_gas" name="descrizione_gas" value="<?php echo $G->descrizione_gas ?>">
                <b class="tooltip tooltip-top-left">
                    <i class="fa fa-warning txt-color-teal"></i>
                    Il nome del tuo GAS</b>
            </label>
            <label class="label margin-top-5">Mail</label>
            <label class="input"> <i class="icon-prepend fa fa-envelope"></i>
                <input placeholder="mail@example.com" type="email" id="mail_gas" name="mail_gas" value="<?php echo $G->mail_gas ?>">
                <b class="tooltip tooltip-top-left">
                    <i class="fa fa-warning txt-color-teal"></i>
                    Inserisci una email valida</b>
            </label>
            <label class="label margin-top-5">Sito Web</label>
            <label class="input"> <i class="icon-prepend fa fa-globe"></i>
                <input placeholder="http://www.esempio.it" type="url" id="website_gas" name="website_gas" value="<?php echo $G->website_gas ?>">
                <b class="tooltip tooltip-top-left">
                    <i class="fa fa-warning txt-color-teal"></i>
                    Inserisci un URL valido</b>
            </label>
            <label class="label margin-top-5">Ragione sociale o una breve descrizione</label>
            <label class="textarea">
                <textarea id="" name="nome_gas" class="textarea textarea-expandable summer_gas"><?php echo $G->nome_gas ?></textarea>
            </label>
            <input id="act" type="hidden" name="act" value="anagrafiche_gas">
        <footer>
                <button type="submit" class="btn btn-primary" id="save_anagrafiche">
                    Salva le nuove anagrafiche
                </button>
            </footer>

       </div>
       </form>



    </div>

</div>
<div class="row">
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        <h1>Sede del tuo GAS</h1>
        <form class="smart-form" id="geolocate_gas" action="ajax_rd4/gas/_act.php" method="post">
        <div class="well well-sm padding-10">

            <label class="label margin-top-5">Indirizzo sede</label>
            <label class="input"> <i class="icon-prepend fa fa-map-marker"></i>
                <input placeholder="" type="text" id="indirizzo_gas" name="indirizzo_gas" value="<?php echo $G->sede_gas ?>">
                <b class="tooltip tooltip-top-left">
                    <i class="fa fa-warning txt-color-teal"></i>
                    Inserisci un indirizzo valido</b>
            </label>
            <div class="alert alert-warning margin-top-10" id="indirizzo_alert" style="display:none"><strong>NB:</strong> L'indirizzo immesso non è stato riconosciuto.</div>

            <footer>
                <button type="submit" class="btn btn-primary" id="salva_consegna">
                    Salva l'indirizzo
                </button>
            </footer>
            <div id="map-canvas" class="google_maps margin-top-10" style="width:100%;"></div>
            <input id="lat" type="hidden" name="lat" value="<?php echo _USER_GAS_LAT ?>">
            <input id="lng" type="hidden" name="lng" value="<?php echo _USER_GAS_LNG ?>">
            <input id="act" type="hidden" name="act" value="geolocate_gas">
       </div>
       </form>

       

    </div>

    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
       <h1>Gestione mail</h1>
        <div class="well well-sm">
        <?php if(_USER_PERMISSIONS & perm::puo_creare_gas){echo $op2;}else{echo '<h3>Non hai i permessi per gestire le opzioni del tuo GAS</h3>';} ?>
        </div>
       <h1>Widget WordPress</h1>
        <div class="well well-sm">
        <?php if(_USER_PERMISSIONS & perm::puo_creare_gas){echo $op_wp;}else{echo '<h3>Non hai i permessi per gestire le opzioni del tuo GAS</h3>';} ?>
        </div>
       
       <!--
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
       -->
       <h1>Maledetta privacy!</h1>
       <div class="well well-sm">
       <?php if(_USER_PERMISSIONS & perm::puo_gestire_utenti){echo $op4;}else{echo '<h3>Non hai i permessi per gestire le opzioni del tuo GAS</h3>';}?>
       </div>
       <!--
       <h1>Parametri report</h1>
        <div class="well well-sm">
        <?php if(_USER_PERMISSIONS & perm::puo_creare_gas){echo $op5;}else{echo '<h3>Non hai i permessi per gestire le opzioni del tuo GAS</h3>';} ?>
        </div>
        -->
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
        
        var delay = (function(){
          var timer = 0;
          return function(callback, ms){
            clearTimeout (timer);
            timer = setTimeout(callback, ms);
          };
        })();

        var  initDropzone = function (){
                //-----------------------------------------------DROPZONE DEMO
                try{Dropzone.autoDiscover = false;}catch(e){}


                 try{

                 console.log("initDropzoneGasIMG");
                 myDropZone = new Dropzone(document.body, { // Make the whole body a dropzone
                  maxFiles:1,
                  url: "upload.php", // Set the url
                  clickable: ".fileinput-button", // Define the element that should be used as click trigger to select files.
                  success: function(file,response){
                        console.log(file);
                        console.log(response);
                        var data = JSON.stringify(eval("(" + response + ")"));
                        var json = JSON.parse(data);
                        console.log(json.result);
                        $("#loadingprogress").width( 0 );

                        this.removeAllFiles();

                        if(json.result==="OK"){
                                   ok(json.msg);
                                   $('#img_gas').attr("src", "public_rd4/gas/"+json.src);
                                   return true;
                               }else{
                                    ko(json.msg);
                                    return false;
                                }


                }
                 });
               myDropZone.on('sending', function(file, xhr, formData){
                    formData.append('id_gas', '<?php echo _USER_ID_GAS?>');
                    formData.append('bkcol', $('#colorsel').val());
                    formData.append('act', 'gasIMG');


                });
                myDropZone.on('uploadprogress', function(file, progress ){
                    console.log(progress );
                    $("#loadingprogress").width( progress + '%' );
                });
            }catch(err){
                console.log("dropZone already attached..." + err);
                location.reload();
            }
            //-----------------------------------------------DROPZONE DEMO
        }

        function codeAddress() {
              var markers = [];
              geocoder = new google.maps.Geocoder();
              var location = $('#indirizzo_gas').val();

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
                        console.log("Code address Riconosciuto per " + $('#indirizzo_gas').val());
                      }else{
                          $('#lat').val (0);
                          $('#lng').val (0);
                          $('#indirizzo_alert').show();
                      console.log("Code address NON Riconosciuto per " + $('#indirizzo_gas').val());
                      }
                  }else{
                     $('#lat').val (0);
                     $('#lng').val (0);
                     $('#indirizzo_alert').show();
                     console.log("Code address NON Riconosciuto per " + $('#indirizzo_gas').val());
                  }
                });
              }

        }

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

        $('.gas_option').change(function (e) {
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
        $('#select_versione_mail').change(function (e) {
            var select = $(this);
            var value = select.val();
            $.SmartMessageBox({
                title : "Modifichi questa opzione ?",
                content : "<b>Attenzione:</b> alcune modifiche influiranno su quello che gli utenti del tuo gas potranno fare o vedere. ",
                buttons : "[Cancella][Procedi]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="Procedi"){
                    var act = 'visualizzazione_mail_ordine';
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

        var $geolocate_gas = $('#geolocate_gas').validate({
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

        var $anagrafiche_gas = $('#anagrafiche_gas').validate({
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

        var geocoder;
        var map;
        function initialize() {
          geocoder = new google.maps.Geocoder();
          var latlng = new google.maps.LatLng(<?php echo _USER_GAS_LAT.","._USER_GAS_LNG; ?>);
          var mapOptions = {
            zoom: 12,
            center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
          }
          var image = "/gas4/img_rd4/male-2.png";
          map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

          var locations = [<?php echo $geo_users ?>];
          var infowindow = new google.maps.InfoWindow()

          for (i = 0; i < locations.length; i++) {
                marker = new google.maps.Marker({
                    position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                    map: map,
                    icon: image
                });
                google.maps.event.addListener(marker, "click", (function(marker, i) {
                return function() {
                  infowindow.setContent(locations[i][0]);
                  infowindow.open(map, marker);
                }
              })(marker, i));
          }

          console.log("Fine mappa initialized");

          $('.summer_gas').summernote({
            height : 180,
            focus : false,
            tabsize : 2,
            toolbar: [
               //[groupname, [button list]]
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
            
              ],
            callbacks:{
                
            }
        });
          

        }


        initialize();
        codeAddress();
        
        $('.color').colorPicker({opacity: false,
                                renderCallback: function($elm, toggled) {
                                    console.log($elm.val());
                                    var val = $elm.val();
                                    $('#colorsel').attr('value',val);
                                }
                                });

        $('#indirizzo_gas').keyup(function() {
            delay(function(){
              codeAddress();
            }, 1000 );
        });
        loadScript("js/plugin/jquery-form/jquery-form.min.js");
        $('#nome_gas').summernote(
            {height : 120,
            focus : false,
            tabsize : 2});






        //DROPZONE
        loadScript("js/plugin/dropzone/dropzone.min.js",initDropzone);


    } // end pagefunction

    $(window).unbind('gMapsLoaded');

    function loadMap(){
        console.log("LoadMap");
        $(window).bind('gMapsLoaded',pagefunction);
        window.loadGoogleMaps();

    }



    loadScript("js/plugin/summernote/new_summernote.min.js", function(){
        loadScript("js/plugin/x-editable/x-editable.min.js", 
            loadScript("js_rd4/plugin/colorpicker/colorpicker.min.js", loadMap));
    });
</script>