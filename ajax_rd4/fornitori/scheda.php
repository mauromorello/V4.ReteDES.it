<?php
require_once("inc/init.php");

$ui = new SmartUI;
$page_title = "Scheda fornitore";
$id_ditte = CAST_TO_INT($_GET["id"]);

$stmt = $db->prepare("SELECT * FROM  retegas_ditte WHERE id_ditte = :id_ditte AND id_proponente='"._USER_ID."';");
$stmt->bindParam(':id_ditte', $id_ditte, PDO::PARAM_INT);
$stmt->execute();
if($stmt->rowCount()==1){
    $proprietario="true";
    $label ='<span class="pull-right label bg-color-greenLight" style="color:#FFF !important">SEI IL PROPRIETARIO</span>';
    $editable = "editable";
    $button_save_note ='<button id="save_note" class="btn btn-success pull-right margin-top-10">Salva le note</button>';

}else{
    $proprietario="false";
    $editable = "";
    $button_save_note="";

}

$sql="SELECT * from retegas_ditte WHERE id_ditte=:id_ditta LIMIT 1;";
$stmt = $db->prepare($sql);
$stmt->bindParam(':id_ditta', $id_ditte, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch();

$sql="SELECT * from retegas_listini WHERE id_ditte=:id_ditta;";
$stmt = $db->prepare($sql);
$stmt->bindParam(':id_ditta', $id_ditte, PDO::PARAM_INT);
$stmt->execute();
$lis= $stmt->fetch(PDO::FETCH_ASSOC);
if( ! $lis)
{
    $cancella_ditta = '<button id="elimina_ditta" rel="'.$row["id_ditte"].'" class="btn btn-block btn-danger">ELIMINA DITTA</button>';
}

if (!empty($row)) {
    $c='<div id="mio_fornitore_container">
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <label for="descrizione_ditte">Nome:</label>
            <p id="descrizione_ditte" class="'.$editable.' font-xl" data-type="text" data-pk="'.$row["id_ditte"].'">'.$row["descrizione_ditte"].'</p>
            <label for="indirizzo">Indirizzo:</label>
            <p id="indirizzo" class="editable_map" data-type="textarea" data-pk="'.$row["id_ditte"].'">'.$row["indirizzo"].'</p>
            <div class="hidden" id="id_ditte" rel="'.$row["id_ditte"].'"></div>
            <div class="hidden" id="ditte_gc_lat" rel="'.$row["ditte_gc_lat"].'"></div>
            <div class="hidden" id="ditte_gc_lng" rel="'.$row["ditte_gc_lng"].'"></div>
            <div id="map-canvas" style="width:100%;height:180px;"></div>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <hr>
            <label for="telefono">Telefono:</label>
            <p id="telefono" class="'.$editable.'" data-type="text" data-pk="'.$row["id_ditte"].'">'.$row["telefono"].'</p>
            <label for="mail_ditte">Email:</label>
            <p id="mail_ditte" class="'.$editable.'" data-type="email" data-pk="'.$row["id_ditte"].'">'.$row["mail_ditte"].'</p>
            <label for="website">Link:</label>
            <p id="website" class="'.$editable.'" data-type="text" data-pk="'.$row["id_ditte"].'">'.$row["website"].'</p>
            <hr>
            <label for="tag_ditte">Parole chiave:</label>
            <input id="tag_ditte"  data-role="tagsinput" value="'.$row["tag_ditte"].'" />
            <hr>
            '.$cancella_ditta.'
        </div>

    </div>
    <div class="well well-sm margin-top-10 padding-5">
        <label for="note_ditte">Note:</label>
        <div id="note_ditte" class="summernote">'.$row["note_ditte"].'</div>
        '.$button_save_note.'
        <div class="clearfix"></div>
    </div>
    </div>


            ';

}
$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>false,
                    "deletebutton"=>false,
                    "colorbutton"=>true);
$wg_dett_forn = $ui->create_widget($options);
$wg_dett_forn->id = "wg_scheda_fornitore";
$wg_dett_forn->body = array("content" => $c ,"class" => "");
$wg_dett_forn->header = array(
    "title" => '<h2>Anagrafica fornitore</h2>',
    "icon" => 'fa fa-pencil'
);

$li = '<div id="listini_fornitore"></div>';
$wg_list_forn= $ui->create_widget($options);
$wg_list_forn->id = "wg_listini_fornitore_mio";
$wg_list_forn->body = array("content" => $li ,"class" => "no-padding");
$wg_list_forn->header = array(
    "title" => '<h2>Listini <small id="nomedittaperlistini"></small></h2>',
    "icon" => 'fa fa-table'
);


$title_navbar='<i class="fa fa-truck fa-2x pull-left"></i> '.$row["descrizione_ditte"].'<br><small class="note">di...</small>';
if(_USER_PERMISSIONS & perm::puo_creare_listini){
    $buttons[]='<form style="margin-right:10px;"><button  data-id_ditta="'.$row["id_ditte"].'" class="aggiungi_listino btn btn-default btn-success navbar-btn"><i class="fa fa-plus"></i> Nuovo Listino</button></form>';
}

?>
<?php echo navbar($title_navbar,$buttons); ?>

<section id="widget-grid" class="margin-top-10">

    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <?php echo help_render_html("scheda_fornitore",$page_title); ?>
            <?php echo $wg_list_forn->print_html(); ?>
        </article>
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <?php echo $wg_dett_forn->print_html(); ?>
        </article>

    </div>

</section>


<script type="text/javascript">

    pageSetUp();


    var geocoder;
    var map;



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
    var startEdit = function(){
    $(".editable").editable({
                                url: 'ajax_rd4/fornitori/_act.php',
                                ajaxOptions: { dataType: 'json' },
                                success: function(response, newValue) {
                                    console.log(response);
                                    if(response.result == 'KO'){
                                        return response.msg;

                                    }else{
                                        ok(response.msg);
                                        //loadlist();
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
                                        codeAddress(response.msg ,true, <?php echo $id_ditte;?>);
                                        initialize($('#ditte_gc_lat').attr('rel'),$('#ditte_gc_lng').attr('rel'));
                                        //loadlist();
                                    }
                                }
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
                          data: {act: "save_note", id_ditte: <?php echo $id_ditte;?>, note_ditte: aHTML},
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


    var initialize=function(lat,lng) {
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
        console.log("Start codeaddress");
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
                          data: {act: "save_coord", id_ditte: <?php echo $id_ditte;?>, ditte_gc_lat : results[0].geometry.location.lat(),ditte_gc_lng:results[0].geometry.location.lng()},
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
                  console.log("Riconosciuto");


                } else {
                  //alert('Geocode was not successful for the following reason: ' + status);
                  ko("Indirizzo non riconosciuto :(");
                  //$('#map-canvas').html('<i class="fa fa-frown-o fa-4x text-center"></i><br><h4>'+status+'</h4>');
                  console.log("Non Riconosciuto");

                }

              });

        }


    var pagefunction = function() {
        var tags_old = $('#tag_ditte').val();

        loadScript("js/plugin/bootstrap-tags/bootstrap-tagsinput.min.js");
        <?php if($editable=="editable"){?>loadScript("js/plugin/x-editable/x-editable.min.js", startEdit);<?php }?>
        <?php if($editable=="editable"){?>loadScript("js/plugin/summernote/summernote.min.js",start_summer); <?php }?>



        //-------------------------HELP
        document.title = '<?php echo "ReteDES.it :: $page_title";?>';
        <?php echo help_render_js("scheda_fornitore"); ?>
        //-------------------------HELP
        console.log("Start Pagefuncion");

        <?php if($editable=="editable"){?>
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
                                                                //loadlist();
                                                                loadlistini();
                                                                $('#mio_fornitore_container').html('<h3 class="text-center text-muted">ditta eliminata</h3>');
                                                        }else{ko(data.msg);}

                                                    });
                                            }
                                        });


                            });
                <?php }?>

                initialize($('#ditte_gc_lat').attr('rel'),$('#ditte_gc_lng').attr('rel'));
                codeAddress($('#indirizzo').html(),false);
                loadlistini(<?php echo $id_ditte;?>);

                <?php if($editable=="editable"){?>
                $('#tag_ditte').on('change', function(event) {

                    var tags_new = $('#tag_ditte').val();

                    console.log("eccomi");
                    if(tags_new!=tags_old){
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/fornitori/_act.php",
                          dataType: 'json',
                          data: {name: "tag_ditte",pk:<?php echo $id_ditte;?>, value : tags_new},
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
                <?php }?>

    };

    // end pagefunction

    // run pagefunction on load


    $(window).unbind('gMapsLoaded');
    $(window).bind('gMapsLoaded',pagefunction);
    window.loadGoogleMaps();


</script>