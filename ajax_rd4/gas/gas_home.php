<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.gas.php");



$ui = new SmartUI;
$converter = new Encryption;

$page_title = "Il mio GAS";
$page_id ="gas_home";


$G = new gas(_USER_ID_GAS);


$supervisori = $G->lista_supervisori_ordini();
foreach($supervisori as $supervisore){
    $sup .= $supervisore["fullname"].", ";
}
$sup = rtrim($sup,", ");

$gestori_utenti = $G->lista_gestori_utenti();
foreach($gestori_utenti as $gestore_utenti){
    $gesut .= $gestore_utenti["fullname"].", ";
}
$gesut = rtrim($gesut,", ");

$gestori_gas = $G->lista_gestori_gas();
foreach($gestori_gas as $gestore_gas){
    $gesgas .= $gestore_gas["fullname"].", ";
}
$gesgas = rtrim($gesgas,", ");

$cassieri = $G->lista_cassieri();
foreach($cassieri as $cassiere){
    $cass .= $cassiere["fullname"].", ";
}
$cass = rtrim($cass,", ");

$ps  ='<p>Responsabile per reteDES del tuo gas:  <strong>'.user_fullname($G->id_referente_gas).'</strong></p>';
$ps .='<p>Gestori cassa: <strong>'.$cass.'</strong></p>';
$ps .='<p>Supervisori ordini: <strong>'.$sup.'</strong></p>';
$ps .='<p>Gestori utenti: <strong>'.$gesut.'</strong></p>';
$ps .='<p>Gestori gas: <strong>'.$gesgas.'</strong></p>';


if(_USER_PERMISSIONS & perm::puo_creare_gas){
    $alert_referente = '<span>Clicca <a class="label label-default" href="#ajax_rd4/gas/gas_opzioni.php">QUA</a> per modificare le anagrafiche del tuo gas';
}

if($G->sede_gas==""){
    $sede_gas='<div class="alert alert-danger">Nessun dato inserito. '.$alert_referente.'</div>';
}else{
    $sede_gas=$G->sede_gas;
}



$g = '<div class="row">
         <div class="col-xs-7">
            <label class="text-muted" >Nome</label><p class="font-lg edit">'.$G->descrizione_gas.'</p>';
$g.= '      <label class="text-muted">Sede</label><p>'.$sede_gas.'</p>';
$g.= '      <label class="text-muted">Ragione sociale</label><p>'.$G->nome_gas.'</p>
            <label class="text-muted">Sito</label><p><a href="'.$G->website_gas.'" target="_BLANK">'.$G->website_gas.'</a></p>
            <label class="text-muted">Mail</label><p><a href="mailto:'.$G->mail_gas.'" target="_BLANK">'.$G->mail_gas.'</a></p>
        </div>
        <div class="col-xs-5 ">
            <div class="well well-sm">
                <div id="map_gas" style="width:100%;height:320px;">
                </div>
            </div>
        </div>
        </div>';


$nu ='<form action="" id="smart-form-register" class="smart-form" method="POST">
                            <header>
                                Inserisci un nuovo utente
                            </header>
                            <br>
                            <p class="alert alert-danger">Inserendo un nuovo utente da questa scheda lo si rende attivo da subito. Verrà inviata contestualmente all\'iscrizione una mail per avvisarlo, che risulterà proveniente dall\'user che lo ha iscritto.
                                                            Chi inserisce un nuovo utente si assume la responsabilità di accettare regole e disclaimer per conto terzi.
                                                            </p>
                            <fieldset>
                                <section>
                                    <label class="input"> <i class="icon-append fa fa-user"></i>
                                        <input id="fullname" type="text" name="fullname" placeholder="Nome e cognome">
                                        <b class="tooltip tooltip-bottom-right">Il nome e cognome reale</b> </label>
                                </section>

                                <section>
                                    <label class="input"> <i class="icon-append fa fa-user"></i>
                                        <input id="username" type="text" name="username" placeholder="username">
                                        <b class="tooltip tooltip-bottom-right">Nome utente usato per accedere</b> </label>
                                </section>
                                <section>
                                    <label class="input"> <i class="icon-append fa fa-envelope-o"></i>
                                        <input id="email" type="email" name="email" placeholder="Email">
                                        <b class="tooltip tooltip-bottom-right">Inserisci un email valida!</b> </label>
                                </section>

                                <section>
                                    <label class="input"> <i class="icon-append fa fa-lock"></i>
                                        <input id="password" type="password" name="password" placeholder="Password" id="password">
                                        <b class="tooltip tooltip-bottom-right">Inserisci la password</b> </label>
                                </section>

                                <section>
                                    <label class="input"> <i class="icon-append fa fa-lock"></i>
                                        <input id="password2" type="password" name="passwordConfirm" placeholder="Ripeti password">
                                        <b class="tooltip tooltip-bottom-right">Controllo password</b> </label>
                                </section>

                                <section>
                                    <label class="input"> <i class="icon-append fa fa-phone"></i>
                                        <input id="tel" type="text" name="tel" placeholder="Telefono">
                                        <b class="tooltip tooltip-bottom-right">Il suo recapito telefonico</b> </label>
                                </section>
                                <section>
                                    <label class="checkbox">
                                        <input type="checkbox"  name="puo_partecipare" id="puo_partecipare" checked="checked">
                                        <i></i>Può partecipare agli ordini</label>
                                    <label class="checkbox">
                                        <input type="checkbox" name="puo_gestire" id="puo_gestire" checked="checked">
                                        <i></i>Può gestire ordini</label>
                                </section>

                            </fieldset>

                            <footer>
                                <button type="submit" class="btn btn-primary">
                                    Inserisci
                                </button>
                            </footer>
                        </form>';


//LISTA GEO USERS
$stmt = $db->prepare("SELECT * FROM maaking_users WHERE (city<>'') AND (user_gc_lat > 0) AND (id_gas='"._USER_ID_GAS."') AND isactive=1;");
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $row) {
      $geo_users .='["Utente", '.$row["user_gc_lat"].', '.$row["user_gc_lng"].',1], ';
}
$geo_users = rtrim($geo_users,", ");


?>
<div class="inbox-nav-bar no-content-padding">
    <h1 class="page-title txt-color-blueDark"><i class="fa fa-fw fa-home"></i> <?php echo _USER_GAS_NOME; ?>  &nbsp;</h1>
</div>
<p></p>
<div class="panel panel-blueLight padding-10 margin-top-10">
    <?php echo $g; ?>
</div>
<div class="panel panel-blueLight padding-10 margin-top-10">
    <h1>Utenti con permessi speciali</h1>
    <?php echo $ps; ?>
</div>

<section id="widget-grid" class="margin-top-10">

    <div class="row">
        <!-- PRIMA COLONNA
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <div class="well well-sm"><?php if(_USER_PERMISSIONS & perm::puo_gestire_utenti){echo $nu;} ?></div>

        </article>-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
        </article>
    </div>

</section>
<!-- Dynamic Modal -->
                        <div class="modal fade" id="remoteModal" tabindex="-1" role="dialog" aria-labelledby="remoteModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <!-- content will be filled here from "ajax/modal-content/model-content-1.html" -->
                                </div>
                            </div>
                        </div>
                        <!-- /.modal -->

<script type="text/javascript">

    pageSetUp();



    var pagefunction = function(){

        var initialize= function() {

        var latlng = new google.maps.LatLng(<?php echo _USER_GAS_LAT ?>,<?php echo _USER_GAS_LNG ?>);
        var image = "/gas4/img_rd4/male-2.png";
        //var image2 = "'.$RG_addr["img_gas_home"].'";
        var mapOptions = {
          zoom: 12,
          center: latlng,
          mapTypeId: google.maps.MapTypeId.TERRAIN,
          minZoom: 9,
          maxZoom: 12
          };

          var map = new google.maps.Map(document.getElementById('map_gas'), mapOptions);
          var locations = [<?php echo $geo_users ?>];

          for (i = 0; i < locations.length; i++) {
                marker = new google.maps.Marker({
                    position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                    map: map,
                    icon: image
                });
                  /*google.maps.event.addListener(marker, "click", (function(marker, i) {
                return function() {
                  infowindow.setContent(locations[i][0]);
                  infowindow.open(map, marker);
                }
              })(marker, i));*/
          }
          console.log("Fine mappa initialized");


        }

        console.log("pagefunction");
        //------------HELP WIDGET
        document.title = '<?php echo "ReteDES.it :: $page_title";?>';
        <?php echo help_render_js('gas_home');?>
        //------------END HELP WIDGET

        var id;
        var messaggio;

        console.log("Inizio Initialized");
        initialize();

        var $registerForm = $("#smart-form-register").validate({

            // Rules for form validation
            rules : {
                username : {
                    required : true,
                    maxlength : 20
                },
                email : {
                    required : true,
                    email : true
                },
                password : {
                    required : true,
                    minlength : 3,
                    maxlength : 20
                },
                passwordConfirm : {
                    required : true,
                    minlength : 3,
                    maxlength : 20,
                    equalTo : '#password'
                },
                tel : {
                    required : true
                },
                fullname : {
                    required : true
                }
            },

            // Messages for form validation
            messages : {
                login : {
                    required : 'Inserisci un username'
                },
                email : {
                    required : 'Inserisci un indirizzo Email',
                    email : 'Inserisci un Email valida'
                },
                password : {
                    required : 'Inserisci una password'
                },
                passwordConfirm : {
                    required : 'Inserisci nuovamente la password',
                    equalTo : 'Inserisci la stessa password'
                },
                tel : {
                    required : 'Inserisci un recapito telefonico'
                },
                fullname : {
                    required : 'Inserisci il suo nome reale'
                }
            },

            submitHandler: function(form) {
                var fullname = $("#fullname").val();
                var username = $("#username").val();
                var password = $("#password").val();
                var password2 = $("#password2").val();
                var tel = $("#tel").val();
                var email = $("#email").val();
                if($('#puo_partecipare').prop('checked')) {var puo_partecipare = 1;}else{ var puo_partecipare = 0;}
                if($('#puo_gestire').prop('checked')) {var puo_gestire = 1;}else{ var puo_gestire = 0;}
                $.ajax({
                      type: "POST",
                      url: "ajax_rd4/gas/_act.php",
                      dataType: 'json',
                      data: {act: "register_new",
                        fullname : fullname,
                        username:username,
                        password:password,
                        password2:password2,
                        tel:tel,
                        email:email,
                        puo_partecipare:puo_partecipare,
                        puo_gestire:puo_gestire},
                      context: document.body
                    }).done(function(data) {
                        if(data.result=="OK"){
                            ok(data.msg);
                            $("#fullname").val('');
                            $("#username").val('');
                            $("#password").val('');
                            $("#password2").val('');
                            $("#tel").val('');
                            $("#email").val('');
                        }else{
                            ko(data.msg);
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






    $(window).unbind('gMapsLoaded');

    function loadMap(){
        console.log("LoadMap");
        $(window).bind('gMapsLoaded',pagefunction);
        window.loadGoogleMaps();

    }




    loadScript("js/plugin/x-editable/x-editable.min.js", loadMap);

</script>