<?php
require_once("inc/init.php");

$ui = new SmartUI;
$page_title="Anagrafica utente";

//---------------------------------------------------ANAGRAFICA UTENTE

$a ='
<form id="account-utente" class="smart-form" action="ajax_rd4/user/_act.php" novalidate="novalidate" method="POST">
                                <header>
                                    Accesso al sito
                                </header>

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
                                <header>
                                    Cambio di password
                                </header>
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
                            <header>
                                    Anagrafica utente
                                </header>
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

                            </fieldset>



                            <footer>
                                <input type="hidden" name="act" value="save_anagrafica">


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
        <header>
                                    Geolocalizzazione
                                </header>

        <fieldset>
                    <section>
                        <label for="address1" class="input">
                            <input id="address1" type="text" name="address1" placeholder="Città" value="'._USER_CITTA.'">
                        </label>
                     </section>

                    <section>
                        <label for="address2" class="input">
                            <input id="address2" type="text" name="address2" placeholder="Indirizzo" value="'._USER_INDIRIZZO.'">
                        </label>
                    </section>

        </fieldset>

<div id="map-canvas" class="google_maps" style="width:100%;"></div>
    <footer class="margin-top-10">
        <input id="lat" type="hidden" name="lat" value="'._USER_LAT.'">
        <input id="lng" type="hidden" name="lng" value="'._USER_LNG.'">
        <input type="hidden" name="act" value="save_geolocalizzazione">
        <button id="cerca" class="btn btn-primary" disabled="disabled">
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

            <?php echo $wg_map->print_html(); ?>
            <?php echo $wg_ana_img->print_html(); ?>
        </article>
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">

            <?php echo $wg_ana->print_html(); ?>
            <?php echo help_render_html("user_anagrafica",$page_title); ?>
        </article>

    </div>

</section>
<script type="text/javascript">

    pageSetUp();

    var pagefunction = function() {

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




        var $checkoutForm = $('#checkout-form').validate({
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

                $(form).ajaxSubmit({
                    type:"POST",
                    dataType: 'json',
                    success : function(data) {
                        //$("#checkout-form").addClass('submited');
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
                            //$("#checkout-form").addClass('submited');
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
              var address = document.getElementById('address1').value + ', ' + document.getElementById('address2').value;
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
                  $("#cerca").removeAttr("disabled");

                } else {
                  //alert('Geocode was not successful for the following reason: ' + status);
                  ko("Indirizzo non riconosciuto :(");
                  console.log("Non Riconosciuto");
                  $("#cerca").attr("disabled","disabled");
                }

              });

        }


        console.log("Inizio Initialized");
        initialize();
        codeAddress();

        $('#address1, #address2').change(function(){
            codeAddress();
        })

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
