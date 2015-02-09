<?php
require_once("inc/init.php");
$ui = new SmartUI;
$converter = new Encryption;

$page_title = "Il mio GAS";


$stmt = $db->prepare("SELECT * FROM  retegas_gas WHERE id_gas = '"._USER_ID_GAS."'");
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$g = '<div class="row">

         <div class="col-xs-7">
            <label>Nome</label><p class="font-lg edit">'._USER_GAS_NOME.'</p>';
$g.= '      <label>Sede</label><p>'.$row["sede_gas"].'</p>';
$g.= '      <label>Ragione sociale</label><p>'.$row["nome_gas"].'</p>
            <label>Sito</label><p><a href="'.$row["website_gas"].'" target="_BLANK">'.$row["website_gas"].'</a></p>
            <label>Mail</label><p><a href="mailto:'.$row["mail_gas"].'" target="_BLANK">'.$row["mail_gas"].'</a></p>
        </div>
        <div class="col-xs-5 ">
            <div class="well well-sm">
                <div id="map_gas" style="width:100%;height:320px;">
                </div>
            </div>
        </div>
        </div>';


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

$op='<div class="row">

        <form class="smart-form">
        <section class="col col-sm-12">
            <button class="btn btn-link pull-right"><i class="fa fa-question"></i></button>
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
        <section class="col col-sm-12 ">
            <button class="btn btn-link pull-right"><i class="fa fa-question"></i></button>
            <p class="label">Visibilità <strong>ordini</strong> condivisa:</p>
            <label class="toggle font-sm">
                <input  class="gas_option" type="checkbox" data-act="gas_option_visione_condivisa" name="checkbox-toggle" '.$gas_visione_condivisa.'>
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>Ogni utente può vedere la merce acquistata da altri (dello stesso gas)
            </label>
        </section>
        <hr />
        <section class="col col-sm-12">
            <a href="javascript:void(0);" class="pull-right" data-placement="top" rel="tooltip" data-original-title="Tooltip"><i class="fa fa-question"></i></a>
            <p class="label">Uso dello strumento <strong>cassa</strong>: </p>
            <label class="toggle font-sm">
                <input class="gas_option" data-act="gas_option_cassa"  type="checkbox"  name="checkbox-toggle" '.$gas_usa_cassa.'>
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>Il tuo gas usa la cassa
            </label>
        </section>
        <hr />
        </form>
        </div>';
$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>false,
                    "deletebutton"=>false,
                    "colorbutton"=>true);
$wg_gasopt = $ui->create_widget($options);
$wg_gasopt->id = "wg_optiongas";
$wg_gasopt->body = array("content" => $op,"class" => "");
$wg_gasopt->header = array(
    "title" => '<h2>Opzioni GAS</h2>',
    "icon" => 'fa fa-check'
    );

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

$wg_scheda_nuovouser = $ui->create_widget($options);
$wg_scheda_nuovouser->id = "wg_scheda_nuovouser";
$wg_scheda_nuovouser->body = array("content" => $nu,"class" => "no-padding");
$wg_scheda_nuovouser->header = array(
    "title" => '<h2>Nuovo utente</h2>',
    "icon" => 'fa fa-star'
    );

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

<section id="widget-grid" class="margin-top-10">

    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <?php echo help_render_html('gas_home',$page_title); ?>
            <?php if(_USER_PERMISSIONS & perm::puo_gestire_utenti){echo $wg_scheda_nuovouser->print_html();} ?>

        </article>
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <?php if(_USER_PERMISSIONS & perm::puo_creare_gas){echo $wg_gasopt->print_html(); }?>
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

    } // end pagefunction






    $(window).unbind('gMapsLoaded');

    function loadMap(){
        console.log("LoadMap");
        $(window).bind('gMapsLoaded',pagefunction);
        window.loadGoogleMaps();

    }




    loadScript("js/plugin/x-editable/x-editable.min.js", loadMap);

</script>