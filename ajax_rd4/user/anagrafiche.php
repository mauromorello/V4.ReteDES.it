<?php
require_once("inc/init.php");

$ui = new SmartUI;
$page_title="Anagrafica utente";
$page_id="user_anagrafica";

$U = new user(_USER_ID);

$query_p = "SELECT valore_text, valore_int from retegas_options WHERE id_user="._USER_ID." AND chiave='_USER_SECONDA_EMAIL' LIMIT 1;";
$stmt = $db->prepare($query_p);
$stmt->execute();
$row_p = $stmt->fetch();
if(CAST_TO_STRING($row_p["valore_text"])<>""){
    if($row_p["valore_int"]>0){
        $seconda_mail_verificata='<note id="status_seconda_mail" class="text-success">QUESTO INDIRIZZO  E\' STATO VERIFICATO</note>';
    }else{
        $seconda_mail_verificata='<note id="status_seconda_mail" class="text-danger">QUESTO INDIRIZZO NON E\' ANCORA STATO VERIFICATO</note>';
    }
}else{
    $seconda_mail_verificata='<note id="status_seconda_mail" class="muted"></note>';
}
$query_p = "SELECT valore_text, valore_int from retegas_options WHERE id_user="._USER_ID." AND chiave='_USER_TERZA_EMAIL'  LIMIT 1;";
$stmt = $db->prepare($query_p);
$stmt->execute();
$row_p = $stmt->fetch();
if(CAST_TO_STRING($row_p["valore_text"])<>""){
    if($row_p["valore_int"]>0){
        $terza_mail_verificata='<note id="status_terza_mail" class="text-success">QUESTO INDIRIZZO E\' STATO VERIFICATO</note>';
    }else{
        $terza_mail_verificata='<note id="status_terza_mail" class="text-danger">QUESTO INDIRIZZO NON E\' STATO VERIFICATO</note>';
    }
}else{
    $terza_mail_verificata='<note id="status_terza_mail" class="text-muted"></note>';
}

//---------------------------------------------------ANAGRAFICA UTENTE

$a ='
<form id="account-utente" class="smart-form" action="ajax_rd4/user/_act.php" novalidate="novalidate" method="POST">
                                <legend>
                                    Accesso al sito
                                </legend>

                                <fieldset>

                                    <section>
                                        <label class="label">Username</label>
                                        <label class="input"> <i class="icon-prepend fa fa-user"></i>
                                            <input type="text" name="username" id="acc_username" value="'._USER_USERNAME.'">
                                            <b class="tooltip tooltip-top-right"><i class="fa fa-login txt-color-teal"></i> Il tuo nome utente che viene usato per riconoscerti quando fai il login su reteDES.it</b></label>
                                    </section>
                               </fieldset>
                               <footer>
                                    <input type="hidden" name="act" value="save_acc_user">
                                    <button type="submit" class="btn btn-primary">
                                        Salva le modifiche
                                    </button>
                                </footer>
                               </form>
                               <form id="password-utente" class="smart-form" action="ajax_rd4/user/_act.php" novalidate="novalidate">
                                <legend>
                                    Cambio di password
                                </legend>
                               <fieldset>
                                    <section>
                                        <label class="label">Vecchia password</label>
                                        <label class="input"> <i class="icon-prepend fa fa-unlock"></i>
                                            <input type="password" name="acc_oldpwd" id="acc_oldpwd">
                                            <b class="tooltip tooltip-top-right"><i class="fa fa-envelope txt-color-teal"></i> La tua password attuale</b></label>
                                    </section>
                                    <section>
                                        <label class="label">Nuova password</label>
                                        <label class="input"> <i class="icon-prepend fa fa-lock"></i>
                                            <input type="password" name="acc_newpwd" id="acc_newpwd">
                                            <b class="tooltip tooltip-top-right"><i class="fa fa-envelope txt-color-teal"></i> La nuova password</b></label>
                                    </section>
                                    <section>
                                        <label class="label">Ripeti</label>
                                        <label class="input"> <i class="icon-prepend fa fa-lock"></i>
                                            <input type="password" name="acc_newpwd2" id="acc_newpwd2">
                                            <b class="tooltip tooltip-top-right"><i class="fa fa-envelope txt-color-teal"></i> Ripeti la nuova password</b></label>
                                    </section>

                                </fieldset>
                                <footer>
                                    <input type="hidden" name="act" value="save_pwd_user">
                                    <button type="submit" class="btn btn-primary">
                                        Salva le modifiche
                                    </button>
                                </footer>
                            </form>

                            <form action="ajax_rd4/user/_act.php" id="checkout-form" class="smart-form" novalidate="novalidate">
                            <legend>
                                    Anagrafica utente
                                </legend>
                            <fieldset>

                                    <section class="">
                                        <label class="input"> <i class="icon-prepend fa fa-user"></i>
                                            <input type="text" name="fullname" placeholder="Fullname" value="'._USER_FULLNAME.'">
                                        </label>
                                    </section>

                                    <section class="">
                                        <label class="input"> <i class="icon-prepend fa fa-envelope-o"></i>
                                            <input type="email" name="email" placeholder="E-mail" value="'._USER_MAIL.'">
                                        </label>
                                    </section>

                                    <section class="">
                                        <label class="input"> <i class="icon-prepend fa fa-phone"></i>
                                            <input type="tel" name="tel" placeholder="Telefono" value="'._USER_TEL.'">
                                        </label>
                                    </section>

                                    <section class="">
                                        <label class="input"> <i class="icon-prepend fa fa-credit-card"></i>
                                            <input type="text" name="tessera" placeholder="Tessera GAS" value="'._USER_TESSERA.'">
                                        </label>
                                    </section>

                            </fieldset>



                            <footer>
                                <input type="hidden" name="act" value="save_anagrafica">


                                <button type="submit" class="btn btn-primary">
                                    Salva le modifiche
                                </button>
                            </footer>
                        </form>
                        <form action="ajax_rd4/user/_act.php" id="email_extra" class="smart-form" novalidate="novalidate">
                            <legend>
                                    Indirizzi aggiuntivi
                            </legend>
                            <fieldset>
                                    <section class="">
                                        <label class="input"> <i class="icon-prepend fa fa-envelope-o"></i>
                                            <input type="email" name="seconda_email" placeholder="Aggiungi una seconda E-mail" value="'._USER_SECONDA_EMAIL.'">
                                            '.$seconda_mail_verificata.'
                                        </label>

                                    </section>
                                    <section class="">
                                        <label class="input"> <i class="icon-prepend fa fa-envelope-o"></i>
                                            <input type="email" name="terza_email" placeholder="Aggiungi una terza E-mail" value="'._USER_TERZA_EMAIL.'">
                                            '.$terza_mail_verificata.'
                                        </label>
                                    </section>
                            </fieldset>
                            <footer>
                                <input type="hidden" name="act" value="save_email_extra">
                                <button type="submit" class="btn btn-primary">
                                    Salva le modifiche
                                </button>
                            </footer>
                        </form>';


$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>false,
                    "deletebutton"=>false,
                    "colorbutton"=>true);
$wg_ana = $ui->create_widget($options);
$wg_ana->id = "wg_anagrafica";
$wg_ana->body = array("content" => $a,"class" => "no-padding");
$wg_ana->header = array(
    "title" => '<h2>Anagrafica utente</h2>',
    "icon" => 'fa fa-user'
);



$i='<div class="jumbotron">
    <div class="polaroid-images">
        <a href="javascript:void(0)" class="fileinput-button"><img SRC="'.src_user(_USER_ID,240).'" id="img_user" class="" alt="'._USER_FULLNAME.'" style="height:128px;width:128px"></img></a>
    </div>
    <div class="clearfix"></div>
    <div class="progress progress-micro margin-top-10">
                <div class="progress-bar progress-bar-primary" role="progressbar" style="width: 0;" id="loadingprogress"></div>
    </div>
    <p class="margin-top-10">Per caricare o modificare la tua immagine clicca o trascinaci sopra una foto.</p>
    <p class="note font-xs">Le immagini verranno ridimensionate a 200 pixel, per cui non è necessario caricare file di grandi dimensioni !!</p>
    </div>
    ';

$wg_ana_img = $ui->create_widget($options);
$wg_ana_img->id = "wg_anagrafica_img";
$wg_ana_img->body = array("content" => $i,"class" => "");
$wg_ana_img->header = array(
    "title" => '<h2>Foto!</h2>',
    "icon" => 'fa fa-camera'
);





$g='    <form action="ajax_rd4/user/_act.php" id="geolocate-form" class="smart-form" novalidate="novalidate">
        <legend>Localizzazione</legend>

        <fieldset>
                    <section>
                        <label for="address1" class="input">
                            <input id="address1" type="text" name="address1" placeholder="Città" value="'._USER_CITTA.' '._USER_INDIRIZZO.'">
                        </label>
                     </section>

                    <!--<section>
                        <label for="address2" class="input">
                            <input id="address2" type="text" name="address2" placeholder="Indirizzo" value="'._USER_INDIRIZZO.'">
                        </label>
                    </section>-->

        </fieldset>

<div id="map-canvas" class="google_maps" style="width:100%;"></div>
    <footer class="margin-top-10">
        <input id="lat" type="hidden" name="lat" value="'._USER_LAT.'">
        <input id="lng" type="hidden" name="lng" value="'._USER_LNG.'">
        <input type="hidden" name="act" value="save_geolocalizzazione">
        <button id="cerca" class="btn btn-primary">
            Salva le modifiche
        </button>
        <span class="note">Una volta inserita la città e la via (e se saranno state riconosciute dal sistema), sarà possibile salvarle premendo questo pulsante</span>
    </footer>
</form>';

$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>false,
                    "deletebutton"=>false,
                    "colorbutton"=>true);
$wg_map = $ui->create_widget($options);
$wg_map->id = "wg_geolocalizzazione";
$wg_map->body = array("content" => $g,"class" => "no-padding");
$wg_map->header = array(
    "title" => '<h2>Geolocalizzazione</h2>',
    "icon" => 'fa fa-thumb-tack'
);


?>
<div class="inbox-nav-bar no-content-padding">
    <h1 class="page-title txt-color-blueDark"><i class="fa fa-fw fa-user"></i> Anagrafica &nbsp;</h1>
</div>

<section id="widget-grid" class="margin-top-10">



    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <div class="well well-sm"><?php echo $g; ?></div>
            <div class="well well-sm">
                <legend>Immagine del profilo</legend>
                <?php echo $i; ?>
            </div>
        </article>
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <div class="well well-sm"><?php echo $a; ?></div>
        </article>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <!--
        <div class="well well-sm">
            <form action="ajax_rd4/user/_act.php" id="user_form_custom_1_DEL" class="smart-form" novalidate="novalidate">
                <legend>Campi personalizzati dal tuo GAS</legend>
                <?php if($U->custom_1_privato<2){?>
                <fieldset>
                    <label class="label margin-top-5">Campo personalizzato 1: <?php echo $U->custom_1_nome; ?></label>
                    <label class="input"> <i class="icon-prepend fa fa-pencil"></i>
                        <input placeholder="<?php echo $U->custom_1 ?>" type="text" id="custom_1" name="custom_1" value="<?php echo $U->custom_1 ?>" <?php echo $U->custom_1_proprieta==1 ? ' DISABLED="DISABLED" ':'' ;?>>
                    </label>
                </fieldset>
                <?php }?>

                <input id="act" type="hidden" name="act" value="do_user_custom_1">
                <footer>
                    <button type="submit" class="btn btn-primary">
                        Salva i nuovi valori
                    </button>
                </footer>
            </form>
        </div>
        -->
        <div class="well well-sm">
            <h3>Campi definiti dal tuo GAS:</h3>
            <?php if($U->custom_1_privato<2){?>
            <form action="ajax_rd4/user/_act.php" id="user_form_custom_1" novalidate="novalidate">
            <fieldset>
            <div class="form-group margin-top-5">
                <label class="control-label col-md-3" for="appendbutton"><b><?php echo $U->custom_1_nome; ?></b></label>
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="input-group">
                                <input id="act" type="hidden" name="act" value="do_user_custom_1">
                                <input placeholder="<?php echo $U->custom_1 ?>" type="text" id="custom_1" name="custom_1" value="<?php echo $U->custom_1 ?>" <?php echo $U->custom_1_proprieta==1 ? ' DISABLED="DISABLED" ':'' ;?> class="form-control">
                                <div class="input-group-btn">
                                    <button class="btn btn-primary" type="submit" <?php echo $U->custom_1_proprieta==1 ? ' DISABLED="DISABLED" ':'' ;?>>
                                        SALVA
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </fieldset>
            </form>
            <?php }?>
            <?php if($U->custom_2_privato<2){?>
            <form action="ajax_rd4/user/_act.php" id="user_form_custom_2" novalidate="novalidate">
            <fieldset>
            <div class="form-group margin-top-5">
                <label class="control-label col-md-3" for="appendbutton"><b><?php echo $U->custom_2_nome; ?></b></label>
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="input-group">
                                <input id="act" type="hidden" name="act" value="do_user_custom_2">
                                <input placeholder="<?php echo $U->custom_2 ?>" type="text" id="custom_2" name="custom_2" value="<?php echo $U->custom_2 ?>" <?php echo $U->custom_2_proprieta==1 ? ' DISABLED="DISABLED" ':'' ;?> class="form-control">
                                <div class="input-group-btn">
                                    <button class="btn btn-primary" type="submit" <?php echo $U->custom_2_proprieta==1 ? ' DISABLED="DISABLED" ':'' ;?>>
                                        SALVA
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </fieldset>
            </form>
            <?php } ?>
            <?php if($U->custom_3_privato<2){?>
            <form action="ajax_rd4/user/_act.php" id="user_form_custom_3" novalidate="novalidate">
            <fieldset>
            <div class="form-group margin-top-5">
                <label class="control-label col-md-3" for="appendbutton"><b><?php echo $U->custom_3_nome; ?></b></label>
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="input-group">
                                <input id="act" type="hidden" name="act" value="do_user_custom_3">
                                <input placeholder="<?php echo $U->custom_3 ?>" type="text" id="custom_3" name="custom_3" value="<?php echo $U->custom_3 ?>" <?php echo $U->custom_3_proprieta==1 ? ' DISABLED="DISABLED" ':'' ;?> class="form-control">
                                <div class="input-group-btn">
                                    <button class="btn btn-primary" type="submit" <?php echo $U->custom_3_proprieta==1 ? ' DISABLED="DISABLED" ':'' ;?>>
                                        SALVA
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </fieldset>
            </form>
            <?php }?>
        </div>

        </article>

    </div>
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
        </article>

    </div>
</section>
<script type="text/javascript">

    pageSetUp();

    var pagefunction = function() {
        var delay = (function(){
          var timer = 0;
          return function(callback, ms){
            clearTimeout (timer);
            timer = setTimeout(callback, ms);
          };
        })();
        <?php echo help_render_js("user_anagrafica"); ?>

        var  initDropzone = function (){
                //-----------------------------------------------DROPZONE DEMO
                try{Dropzone.autoDiscover = false;}catch(e){}


                 try{

                 console.log("initDropzoneUserIMG");
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
                                   $('#img_user').attr("src", "public_rd4/users/"+json.src);
                                   return true;
                               }else{
                                ko(json.msg);
                                return false;
                                }


                }
                 });
               myDropZone.on('sending', function(file, xhr, formData){
                    formData.append('id_utente', '<?php echo _USER_ID?>');
                    formData.append('act', 'userIMG');


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

        var $user_form_custom_1 = $('#user_form_custom_1').validate({
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
        var $user_form_custom_2 = $('#user_form_custom_2').validate({
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
        var $user_form_custom_3 = $('#user_form_custom_3').validate({
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

        var $checkoutForm1 = $('#email_extra').validate({
        // Rules for form validation
            rules : {

                seconda_email : {
                    required : false,
                    email : true
                },
                terza_email : {
                    required : false,
                    email : true
                }

            },

            // Messages for form validation
            messages : {


                seconda_email : {
                    required : 'Serve un indirizzo mail',
                    email : 'Serve un indirizzo mail VALIDO'
                },
                terza_email : {
                    required : 'Serve un indirizzo mail',
                    email : 'Serve un indirizzo mail VALIDO'
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
                                $('#status_seconda_mail').html("IN ATTESA DI VERIFICA...");
                                $('#status_terza_mail').html("IN ATTESA DI VERIFICA...");
                            }else{
                                    ko(data.msg);
                                }
                            }
                });
            },

            // Do not change code below
            errorPlacement : function(error, element) {
                error.insertAfter(element.parent());
            }
        });

        var $checkoutForm2 = $('#checkout-form').validate({
        // Rules for form validation
            rules : {
                fullname : {
                    required : true
                },
                email : {
                    required : true,
                    email : true
                },
                tel : {
                    required : true
                }
            },

            // Messages for form validation
            messages : {

                fullname : {
                    required : 'Per cortesia inserisci il tuo nome e cognome.'
                },
                email : {
                    required : 'Serve un indirizzo mail',
                    email : 'Serve un indirizzo mail VALIDO'
                },
                tel : {
                    required : 'Per cortesia inserisci un recapito telefonico'
                }
            },

            // Ajax form submition
            submitHandler : function(form) {
                $.blockUI();
                $(form).ajaxSubmit({
                    type:"POST",
                    dataType: 'json',
                    success : function(data) {
                        
                            $.unblockUI();
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
        var $accountUtente = $('#account-utente').validate({
        // Rules for form validation
            rules : {
                username : {
                    required : true
                }
            },
            messages : {
                username : {
                    required : 'Per cortesia inserisci qualcosa... max 15 caratteri!'
                }
            },
            submitHandler : function(form) {

                $(form).ajaxSubmit({
                    type:"POST",
                    dataType: 'json',
                    success : function(data) {
                            //$("#account-utente").addClass('submited');
                            if(data.result=="OK"){
                                okWait(data.msg);
                                $.ajax({
                                  type: "POST",
                                  url: "ajax_rd4/_act_main.php",
                                  dataType: 'json',
                                  data: {act: "do_logout"},
                                  context: document.body
                                }).done(function(data) {
                                    if(data.result=="OK"){
                                        location.reload(true);
                                    }else{
                                        ko(data.msg);
                                    }
                                });
                            }else{
                                ko(data.msg);}
                            }
                });
            },

            // Do not change code below
            errorPlacement : function(error, element) {
                error.insertAfter(element.parent());
            }
        });
        var $passwordUtente = $('#password-utente').validate({
        // Rules for form validation
            rules : {
                acc_oldpwd : {
                    required : true
                },
                acc_newpwd : {
                    required : true
                },
                acc_newpwd2 : {
                    required : true
                }

            },
            messages : {
                acc_oldpwd : {
                    required : 'Inserire la password attualmente usata'
                },
                acc_newpwd : {
                    required : 'Inserire la nuova password'
                },
                acc_newpwd2 : {
                    required : 'Ripetere la nuova password'
                }
            },
            submitHandler : function(form) {

                $(form).ajaxSubmit({
                    type:"POST",
                    dataType: 'json',
                    success : function(data) {
                            //$("#account-utente").addClass('submited');
                            if(data.result=="OK"){
                                okWait(data.msg);
                                $.ajax({
                                  type: "POST",
                                  url: "../ajax_rd4/_act_main.php",
                                  dataType: 'json',
                                  data: {act: "do_logout"},
                                  context: document.body
                                }).done(function(data) {
                                    if(data.result=="OK"){
                                        location.reload(true);
                                    }else{
                                        ko(data.msg);
                                    }
                                });
                            }else{
                                ko(data.msg);}
                            }
                });
            },

            // Do not change code below
            errorPlacement : function(error, element) {
                error.insertAfter(element.parent());
            }
        });
        var $geolocateForm = $('#geolocate-form').validate({

            // Ajax form submition
            submitHandler : function(form) {
                console.log(codeAddress());

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

        function codeAddress() {
              var markers = [];
              var address = document.getElementById('address1').value;
              geocoder.geocode( { 'address': address}, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                  map.setCenter(results[0].geometry.location);

                  for (var i = 0; i < markers.length; i++) {
                    markers[i].setMap(null);
                  }

                  var marker = new google.maps.Marker({
                      map: map,
                      position: results[0].geometry.location
                  });
                  markers.push(marker);

                  $('#lat').val (results[0].geometry.location.lat());
                  $('#lng').val (results[0].geometry.location.lng());
                  console.log("Riconosciuto");


                } else {
                  //alert('Geocode was not successful for the following reason: ' + status);
                  ko("Indirizzo non riconosciuto :(");
                  console.log("Non Riconosciuto");

                }

              });

        }


        console.log("Inizio Initialized");
        initialize();
        codeAddress();
        $('#address1').keyup(function() {
            delay(function(){
              codeAddress();
            }, 1000 );
        });

        //$('#address1, #address2').change(function(){
        //    codeAddress();
        //})

        loadScript("js/plugin/dropzone/dropzone.min.js",initDropzone);
    };

    // end pagefunction
    console.log("Inizio script");
    // Load form valisation dependency
    loadScript("js/plugin/jquery-form/jquery-form.min.js");
    $(window).unbind('gMapsLoaded');
    $(window).bind('gMapsLoaded',pagefunction);
    window.loadGoogleMaps();




    console.log("Fine script");


</script>
