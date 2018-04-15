<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.des.php");
$ui = new SmartUI;
$converter = new Encryption;

$page_title = "Opzioni del mio DES";
$page_id ="opzioni_des";

$D = new des(_USER_ID_DES);


//GEO GAS
//LISTA GEO USERS
$stmt = $db->prepare("SELECT * FROM retegas_gas WHERE (gas_gc_lat > 0) AND (id_des='"._USER_ID_DES."');");
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $row) {
      $useridEnc = $converter->encode($row["userid"]);
      $infowindow= '<a href=\"'.APP_URL.'/#ajax_rd4/user/scheda.php?id='.$useridEnc.'\" target=\"_BLANK\">'.$row["fullname"].'</a><br>'.$row["city"];

      $geo_gas .='["'.$infowindow.'", '.$row["gas_gc_lat"].', '.$row["gas_gc_lng"].',1], ';
}
$geo_gas = rtrim($geo_gas,", ");

//IMMAGINE
$immagine='<div class="margin-top-10">
                <div class="polaroid-images pull-right">
                    <a href="javascript:void(0)" class="fileinput-button"><img SRC="'.src_des(_USER_ID_DES,240).'" id="img_des" class="" alt="'._USER_DES_NOME.'" style="height:128px;width:128px"></img></a>
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
<div class="inbox-nav-bar no-content-padding">

    <h1 class="page-title txt-color-blueDark">
            <!--<span class="pull-right">
            <a class="btn btn-success" href="#ajax_rd4/gas/gas_nuovo.php"><i class="fa fa-plus"></i> <i class="hidden-xs">Nuovo GAS</i></a>
            <a class="btn btn-info" href="#ajax_rd4/gas/gas_storico.php"><i class="fa fa-bar-chart-o"></i> <i class="hidden-xs">Storico</i></a>
            </span>-->
    <i class="fa fa-fw fa-home"></i> OPZIONI <?php echo $D->des_descrizione; ?>  &nbsp;</h1>
</div>


<div class="row margin-top-10">

    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        <h1>Nome del tuo DES</h1>
        <p class="editable font-lg" data-pk="<?php echo $D->id_des; ?>"><?php echo $D->des_descrizione; ?></p>
        <hr>
        <h1>Immagine DES</h1>
        <?php if(_USER_PERMISSIONS & perm::puo_vedere_retegas){echo $immagine;}else{echo '<h3>Non hai i permessi</h3>';} ?>
    </div>

    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        <h1>Centro del tuo DES</h1>
         <div class="well well-sm padding-10">
            <p class="note">Sposta la mappa per includere tutti i gas del tuo DES</p>
            <div id="map-canvas" class="google_maps margin-top-10" style="width:100%;"></div>
       </div>
       
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
                                   $('#img_des').attr("src", "public_rd4/des/"+json.src);
                                   return true;
                               }else{
                                    ko(json.msg);
                                    return false;
                                }


                }
                 });
               myDropZone.on('sending', function(file, xhr, formData){
                    formData.append('id_des', '<?php echo _USER_ID_DES?>');
                    formData.append('bkcol', $('#colorsel').val());
                    formData.append('act', 'desIMG');


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

        
        //------------HELP WIDGET
        document.title = '<?php echo "ReteDES.it :: $page_title";?>';
        <?php echo help_render_js($page_id);?>
        //------------END HELP WIDGET

        var id;
        var messaggio;

        console.log("Inizio Initialized");

        loadScript("js/plugin/jquery-form/jquery-form.min.js");

        //DROPZONE
        loadScript("js/plugin/dropzone/dropzone.min.js",initDropzone);

        
        
        
        var geocoder;
        var map;
        function initialize() {
          geocoder = new google.maps.Geocoder();
          var latlng = new google.maps.LatLng(<?php echo _USER_DES_LAT.","._USER_DES_LNG; ?>);
          var mapOptions = {
            zoom: <?php echo $D->des_zoom; ?>,
            center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
          }
          var image = "/gas4/img_rd4/GAS.png";
          map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

          var locations = [<?php echo $geo_gas ?>];
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

          //EVENT LISTENERS
          google.maps.event.addListener(map,'idle',function(){
          if(!this.get('dragging') && this.get('oldCenter') && this.get('oldCenter')!==this.getCenter()) {
            
              //CENTRO              
              console.log("TROVATO CENTRO: " + this.getCenter().lat());
              var lat = this.getCenter().lat();
              var lng = this.getCenter().lng()
              
              //ZOOM
              console.log("TROVATO ZOOM: " + this.getZoom());
              var zoom = this.getZoom();
              
               $.ajax({
                      type: "POST",
                      url: "ajax_rd4/des/_act.php",
                      dataType: 'json',
                      data: {act: 'set_gc_des', lat : lat, lng : lng, zoom : zoom},
                      context: document.body
                    }).done(function(data) {
                        if(data.result=="OK"){
                            ok(data.msg);
                        }else{
                            ko(data.msg);
                        }
                    });
              
              
          }
          if(!this.get('dragging')){
           this.set('oldCenter',this.getCenter())
          }

        });

        google.maps.event.addListener(map,'dragstart',function(){
          this.set('dragging',true);          
        });

        google.maps.event.addListener(map,'dragend',function(){
          this.set('dragging',false);
          google.maps.event.trigger(this,'idle',{});
        });
          
          
        }


        initialize();
        $('.color').colorPicker({opacity: false,
                                renderCallback: function($elm, toggled) {
                                    console.log($elm.val());
                                    var val = $elm.val();
                                    $('#colorsel').attr('value',val);
                                }
                                });
        
        $(".editable").editable({
                url: 'ajax_rd4/des/_act.php',
                ajaxOptions: { dataType: 'json' },
                type: 'text',
                params:  {
                    'act': 'edit_descrizione_des'
                },
                success: function(response, newValue) {
                    console.log(response);
                    if(response.result == 'KO'){
                        return response.msg;
                    }else{
                        ok(response.msg);
                    }
                }
                });
        

    } // end pagefunction

    $(window).unbind('gMapsLoaded');

    function loadMap(){
        console.log("LoadMap");
        $(window).bind('gMapsLoaded',pagefunction);
        window.loadGoogleMaps();

    }



    loadScript("js/plugin/summernote/summernote.min.js", function(){
        loadScript("js/plugin/x-editable/x-editable.min.js", 
            loadScript("js_rd4/plugin/colorpicker/colorpicker.min.js", loadMap));
    });
</script>