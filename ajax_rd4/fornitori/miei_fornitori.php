<?php
require_once("inc/init.php");

$ui = new SmartUI;
$page_title = "mie Ditte";


$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>true,
                    "deletebutton"=>false,
                    "colorbutton"=>false);


$li = '<div id="statistica_mio_fornitore"></div>';
$wg_miei_forn_stat= $ui->create_widget($options);
$wg_miei_forn_stat->id = "wg_statistica_fornitore_mio";
$wg_miei_forn_stat->body = array("content" => $li ,"class" => "");
$wg_miei_forn_stat->header = array(
    "title" => '<h2>Statistiche</h2>',
    "icon" => 'fa fa-bar-chart-o'
);

$li = '<div id="listini_fornitore"></div>';
$wg_list_forn= $ui->create_widget($options);
$wg_list_forn->id = "wg_listini_fornitore_mio";
$wg_list_forn->body = array("content" => $li ,"class" => "no-padding");
$wg_list_forn->header = array(
    "title" => '<h2>Listini <small id="nomedittaperlistini"></small></h2>',
    "icon" => 'fa fa-table'
);
if(_USER_PERMISSIONS & perm::puo_creare_ditte){
    $crea_ditta = '<button id="aggiungi_ditta" class="btn btn-success btn-lg"><i class="fa fa-plus-circle"></i>&nbsp;Aggiungi ditta</button>';
}


$li = ' <div class="row padding-10 text-center">'.$crea_ditta.'</div>
        <div id="lista_miei_fornitori"></div>';
$wg_miei_forn = $ui->create_widget($options);
$wg_miei_forn->id = "wg_fornitori_miei";
$wg_miei_forn->body = array("content" => $li ,"class" => "no-padding");
$wg_miei_forn->header = array(
    "title" => '<h2>Fornitori inseriti da me</h2>',
    "icon" => 'fa fa-truck'
);

$c='         <div id="mio_fornitore_container">
                        <div class="alert alert-warning text-center">
                            <h5><i class="fa fa-truck fa-2x"></i>&nbsp;Clicca sul nome di una ditta per vedere qua i dettagli</h5>
                        </div>
            </div>
       ';

$wg_dett_forn = $ui->create_widget($options);
$wg_dett_forn->id = "wg_fornitori_miei_dettaglio";
$wg_dett_forn->body = array("content" => $c ,"class" => "");
$wg_dett_forn->header = array(
    "title" => '<h2>Anagrafica fornitore</h2>',
    "icon" => 'fa fa-pencil'
);



?>
<div class="inbox-nav-bar no-content-padding">
    <div class="row">
        <div class="col col-xs-12 col-sm-8 col-md-8 col-lg-8">
            <h1 class="page-title txt-color-blueDark"><i class="fa fa-fw fa-truck"></i> Le mie ditte &nbsp;</h1>
        </div>
        <div class="col col-xs-12 col-sm-4 col-md-4 col-lg-4">

        </div>
    </div>
    <div class="clearfix"></div>
</div>

<section id="widget-grid" class="margin-top-10">

    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <?php echo help_render_html("mie_ditte",$page_title); ?>
            <?php echo $wg_dett_forn->print_html(); ?>
        </article>
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <?php echo $wg_miei_forn->print_html(); ?>
            <?php echo $wg_list_forn->print_html(); ?>
        </article>

    </div>

</section>


<script type="text/javascript">

    pageSetUp();
    var geocoder;
    var map;

    var aggiungi_ditta = function(){
    $("#aggiungi_ditta").click(function(e) {

            $.SmartMessageBox({
                title : "Aggiungi un nuovo fornitore ?",
                content : "Inserisci solo il suo nome, potrai poi fare tutte le altre operazioni successivamente.",
                buttons : "[Esci][Salva]",
                input : "text",
                placeholder : "Nome",
                inputValue: '',
            }, function(ButtonPress, Value) {

                if(ButtonPress=="Salva"){
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/fornitori/_act.php",
                          dataType: 'json',
                          data: {act: "aggiungi_ditta", value : Value},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    ok(data.msg);
                                    loadlist();
                            }else{
                                ko(data.msg)
                            ;}

                        });
                }
            });

            e.preventDefault();
        })
    }

    var loadlistini = function(id_ditta){
        $.ajax({
              type: "POST",
              url: "ajax_rd4/fornitori/inc/listini_fornitore.php",
              data: {id_ditta : id_ditta},
              context: document.body
           }).done(function(data) {

               $('#listini_fornitore').html(data);


        });
    }

    var loadditta = function(id_ditta){
        $.ajax({
                  type: "POST",
                  url: "ajax_rd4/fornitori/inc/scheda_fornitore.php",
                  dataType: 'json',
                  data: {id_ditta : id_ditta},
                  context: document.body
                }).done(function(data) {
                        if(data.result=="OK"){

                            $('#mio_fornitore_container').html(data.msg);
                            loadlistini(id_ditta);

                            $('#elimina_ditta').click(function(){
                                $.SmartMessageBox({
                                    title : "Vuoi eliminare questa ditta ?",
                                    content : "Non sarà più possibile tornare indietro...",
                                    buttons : "[NO][SI]"

                                        }, function(ButtonPress, Value) {

                                            if(ButtonPress=="SI"){
                                                var Value = $('#elimina_ditta').attr('rel');
                                                console.log(Value);
                                                $.ajax({
                                                      type: "POST",
                                                      url: "ajax_rd4/fornitori/_act.php",
                                                      dataType: 'json',
                                                      data: {act: "delete_ditta", value : Value},
                                                      context: document.body
                                                    }).done(function(data) {
                                                        if(data.result=="OK"){
                                                                ok(data.msg);
                                                                loadlist();
                                                                loadlistini();
                                                                $('#mio_fornitore_container').html('<h3 class="text-center text-muted">ditta eliminata</h3>');
                                                        }else{ko(data.msg);}

                                                    });
                                            }
                                        });


                            });



                            $(".editable").editable({
                                url: 'ajax_rd4/fornitori/_act.php',
                                ajaxOptions: { dataType: 'json' },
                                success: function(response, newValue) {
                                    console.log(response);
                                    if(response.result == 'KO'){
                                        return response.msg;

                                    }else{
                                        ok(response.msg);
                                        loadlist();
                                    }
                                }
                            });

                            $(".editable_map").editable({
                                    url: 'ajax_rd4/fornitori/_act.php',
                                    ajaxOptions: { dataType: 'json' },
                                    success: function(response, newValue) {
                                        console.log(response);
                                        if(response.result == 'KO'){
                                            return response.msg;

                                        }else{

                                        //codeaddress salva le coordinate se google le riconosce
                                        codeAddress(response.msg ,true, id_ditta);
                                        //initialize($('#ditte_gc_lat').attr('rel'),$('#ditte_gc_lng').attr('rel'));
                                        loadlist();
                                    }
                                }
                            });

                        try {
                            initialize($('#ditte_gc_lat').attr('rel'),$('#ditte_gc_lng').attr('rel'));
                            codeAddress($('#indirizzo').html(),false);
                        }
                        catch(err) {
                            ko("Mappa non disponibile");
                        }

                        loadScript("js/plugin/summernote/summernote.min.js",start_summer);
                    }else{
                        ko(data.msg);
                    }
                });
    }

    var loadlist = function(){
    $.ajax({
              type: "POST",
              url: "ajax_rd4/fornitori/inc/lista_miei_fornitori.php",
              context: document.body
           }).done(function(data) {
              $('#lista_miei_fornitori').html(data);

              $('.ditta_selector').on('click', function(e){
                var id_ditta = $(this).attr('rel');

                //$('.fornitore').removeClass('bg-color-greenLight');
                //$(".fornitore [name='"+id_ditta+"']").addClass('bg-color-greenLight');

                loadditta(id_ditta);
                localStorage.setItem('id_ditta-mie_ditte',id_ditta);
              });
           });
    }


    function start_summer(){
        $('.summernote').summernote({
        toolbar: [
                //[groupname, [button list]]

                ['style', ['bold', 'italic', 'underline','clear']],

                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']]
              ]

        });
        $('#save_note').click(function(){
            var id_ditta = $('#id_ditte').attr('rel');
            var aHTML = $('.summernote').code(); //save HTML If you need(aHTML: array).
                      console.log("ID : " + id_ditta);
                      console.log(aHTML);
           $.ajax({
                          type: "POST",
                          url: "ajax_rd4/fornitori/_act.php",
                          dataType: 'json',
                          data: {act: "save_note", id_ditte: id_ditta, note_ditte: aHTML},
                          context: document.body
           }).done(function(data) {
                            if(data.result=="OK"){
                                    ok(data.msg);
                            }else{
                                    ko(data.msg);
                            }
           });

        });

    }

    function initialize(lat,lng) {
      geocoder = new google.maps.Geocoder();
      var latlng = new google.maps.LatLng(lat,lng);
      var mapOptions = {
        zoom: 10,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
      }
      map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
      console.log("Fine mappa initialized");


    }


    function codeAddress(address, save_coord, id_ditte) {
              var markers = [];
              geocoder.geocode( { 'address': address}, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {


                  for (var i = 0; i < markers.length; i++) {
                    markers[i].setMap(null);
                  }

                  var marker = new google.maps.Marker({
                      map: map,
                      position: results[0].geometry.location
                  });
                  markers.push(marker);
                  map.setCenter(results[0].geometry.location);

                  if(save_coord){

                      $.ajax({
                          type: "POST",
                          url: "ajax_rd4/fornitori/_act.php",
                          dataType: 'json',
                          data: {act: "save_coord", id_ditte: id_ditte, ditte_gc_lat : results[0].geometry.location.lat(),ditte_gc_lng:results[0].geometry.location.lng()},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    ok(data.msg);
                            }else{
                                    ko(data.msg);
                            }
                        });
                  }
                  //$('#ditte_gc_lat').attr('rel',results[0].geometry.location.lat());
                  //$('#ditte_gc_lng').attr('rel',results[0].geometry.location.lng());
                  //console.log("Riconosciuto");


                } else {
                  //alert('Geocode was not successful for the following reason: ' + status);
                  ko("Indirizzo non riconosciuto :(");
                  //$('#map-canvas').html('<i class="fa fa-frown-o fa-4x text-center"></i><br><h4>'+status+'</h4>');
                  console.log("Non Riconosciuto");

                }

              });

        }


    var pagefunction = function() {
        //-------------------------HELP
        <?php echo help_render_js("mie_ditte"); ?>
        //-------------------------HELP

        var id_ditta = localStorage.getItem('id_ditta-mie_ditte');

        loadlist();
        aggiungi_ditta();
        loadlistini(id_ditta);
        if(id_ditta >0){
            loadditta(id_ditta);
        }


    };

    // end pagefunction

    // run pagefunction on load

    $(window).unbind('gMapsLoaded');

    $(window).bind('gMapsLoaded',loadScript("js/plugin/x-editable/x-editable.min.js", pagefunction));
    window.loadGoogleMaps();


</script>
