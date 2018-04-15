<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.gas.php");
$ui = new SmartUI;
$page_title = "timeline ordini";
$page_id ="timeline_ordini";




?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.0.3/leaflet.css" rel="stylesheet">
<style>
    .leaflet-bottom.leaflet-left{
        width: 100%;
    }
    .leaflet-control-container .leaflet-timeline-controls{
        box-sizing: border-box;
        width: 100%;
        margin: 0;
        margin-bottom: 15px;
    }
</style>

<h1>TIMELINE ordini <small>Leggi l'help per capire di cosa si tratta</small></h1>


<div id="map" style="width:100%;height:700px;"></div>   
<div id="info">
    <ul id="displayed-list"></ul>
</div>
<p></p>
<section id="widget-grid" class="margin-top-10">
<div id="report"></div>
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
        </article>
    </div>

</section>
<script type="text/javascript">

    pageSetUp();



    var pagefunction = function(){

        //------------HELP WIDGET
        document.title = '<?php echo "ReteDES.it :: $page_title";?>';
        <?php echo help_render_js($page_id);?>
        //------------END HELP WIDGET

        var osmUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWltbW96MDEiLCJhIjoiY2o0OG9xNWxlMGZrejMzcGUxNmJvcmRzOSJ9.UNc86BjwpuDNljHy6fi5Pg';
        var osmAttrib = '&copy; <a href="https://openstreetmap.org/copyright">' +
            'OpenStreetMap</a> contributors';
        var osm = L.tileLayer(osmUrl, {
            maxZoom: 18,
            attribution: osmAttrib,
            noWrap: true
        });
        var map = L.map('map', {
            layers: [osm],
            center: new L.LatLng(44,11),
            zoom: 6,
            maxBounds: [[90,-180], [-90, 180]],
            id: 'mapbox.streets'
        });
        
        function updateList(timeline){
            var displayed = timeline.getLayers();
            var list = document.getElementById('displayed-list');
            list.innerHTML = "";
            displayed.forEach(function(quake){
              var li = document.createElement('li');
              li.innerHTML = quake.feature.properties.title;
              //list.appendChild(li);
            });
          }

          function eqfeed_callback(data){
            var getInterval = function(quake) {
              // earthquake data only has a time, so we'll use that as a "start"
              // and the "end" will be that + some value based on magnitude
              // 18000000 = 30 minutes, so a quake of magnitude 5 would show on the
              // map for 150 minutes or 2.5 hours
              return {
                start: quake.properties.time,
                end: quake.properties.time2
                //end:   quake.properties.time + quake.properties.mag * 1800000 * 10
              };
            };
            var timelineControl = L.timelineSliderControl({
              formatOutput: function(date){
                return moment(date).format("YYYY-MM-DD HH:MM:SS");
              }
            });
            var timeline = L.timeline(data, {
              getInterval: getInterval,
              pointToLayer: function(data, latlng){
                var hue_min = 240;
                var hue_max = 0;
                var hue = data.properties.mag / 50 * (hue_max - hue_min) + hue_min;
                return L.circleMarker(latlng, {
                  radius: data.properties.mag * 5,
                  color: "hsl("+hue+", 100%, 50%)",
                  fillColor: "hsl("+hue+", 100%, 50%)"
                }).bindPopup('<a href="' + data.properties.url + '">' + data.properties.title + '</a><br> Eu: ' + Math.round(data.properties.mag * 100));
              }
            });
            timelineControl.addTo(map);
            timelineControl.addTimelines(timeline);
            timeline.addTo(map);
            timeline.on('change', function(e){
              updateList(e.target);
            });
            updateList(timeline);
          }
        
          //------------------------ 1514396707840
          eqfeed_callback(
            {  "features":[
                    
                    <?php
                    
                    //ORDINI APERTI
                    $sql="SELECT O.*, D.ditte_gc_lat,D.ditte_gc_lng, D.descrizione_ditte 
                            FROM retegas_ordini O
                            INNER JOIN retegas_listini L on L.id_listini=O.id_listini
                            INNER JOIN retegas_ditte D on D.id_ditte=L.id_ditte
                            INNER JOIN maaking_users U on U.userid=O.id_utente
                            INNER JOIN retegas_gas G on G.id_gas=U.id_gas
                            WHERE D.ditte_gc_lat >0 AND O.id_stato=3 AND data_chiusura<NOW()
                            AND G.id_des="._USER_ID_DES."
                            AND O.valore_finale>0
                            ORDER BY id_ordini DESC LIMIT 1000";
                    $stmt = $db->prepare($sql);
                    $stmt->execute();
                    $rows = $row = $stmt->fetchAll();
                    foreach($rows as $row){
                    
                    echo '{"type":"Feature",
                                "properties":{
                                "mag":'.ROUND(($row["valore_finale"]/100),2).',
                                "time":'.(strtotime($row["data_apertura"])*1000).',
                                "time2":'.(strtotime($row["data_chiusura"])*1000).',
                                "url":"www.retedes.it",
                                "title":"'.str_replace('"',"'",$row["descrizione_ditte"]).'"},
                        "geometry":{
                            "type":"Point",
                            "coordinates":['.$row["ditte_gc_lng"].','.$row["ditte_gc_lat"].']
                            },
                        "id":"nc72944901"
                        },';
                    }    
                    ?>
                    
                    
                    ]
            });

            //------------------------
    } // end pagefunction

    function loadLeaf(){
        loadScript("js_rd4/plugin/leaflet_timeline/leaflet.timeline.js",pagefunction);    
    }
    
    
    function loadHS(){
        loadScript("https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.min.js",loadLeaf);    
    }   
    
    loadScript("https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.0.3/leaflet.js",loadHS);


