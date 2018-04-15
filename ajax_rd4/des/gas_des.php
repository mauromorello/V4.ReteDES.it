<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.gas.php");


$ui = new SmartUI;
$converter = new Encryption;

$page_title = "I gas nel mio DES";
$page_id ="gas_des";


$title_navbar='Il mio DES: '._USER_DES_NOME;

if(_USER_PERMISSIONS&perm::puo_gestire_retegas){
    if(CAST_TO_INT($_GET["id_des"],0)>0){
        $id_des =  CAST_TO_INT($_GET["id_des"]);
    }else{
        $id_des =  _USER_ID_DES;
    }
}else{
    $id_des =  _USER_ID_DES;
    if($id_des==0 | $id_des==1){
        ?><h3 class="text-center">Il tuo gas non appartiene ad alcun DES.</h3>
          <p class="text-center">Se sei interessato alla costituzione di un DES o all'inserimento del tuo GAS in un DES esistente<br> contatta <b>info@retedes.it</b> per avere tutte le informazioni in merito.</p>   <?php
        die();
    }
    
    
}



//LISTA GEO GAS
$stmt = $db->prepare("SELECT * FROM retegas_gas WHERE (gas_gc_lat > 0) AND (id_des='".$id_des."');");
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $row) {
      $geo_gas .='["'.$row["descrizione_gas"].'", '.$row["gas_gc_lat"].', '.$row["gas_gc_lng"].',1], ';
}
$geo_gas = rtrim($geo_gas,", ");

$p  ='<div class="table-responsive">
                    <table class="table table-striped  has-tickbox" id="dt_basic">';


$sql="SELECT G.descrizione_gas, G.gas_gc_lat, G.id_referente_gas, G.targa_gas, G.id_gas,
                (SELECT COUNT(userid) FROM maaking_users U WHERE U.id_gas=G.id_gas AND U.isactive=1 GROUP BY U.id_gas) as utenti_gas,
                (SELECT UR.fullname FROM maaking_users UR WHERE UR.userid=G.id_referente_gas) AS referente_gas
                FROM retegas_gas G WHERE
                id_des='".$id_des."';";
$stmt = $db->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $row) {
    if($row["gas_gc_lat"]>0){
        $gc='<i class="fa fa-map-marker text-success"></i>';
    }else{
        $gc='';
    }

    $useridEnc = $converter->encode($row["id_referente_gas"]);
    $totale_utenti += $row["utenti_gas"];
    
    if(_USER_PERMISSIONS && perm::puo_vedere_retegas){
        $targa='<span  class="editable_targa" data-original-title ="Provincia"  data-type="text" data-pk="'.$row["id_gas"].'">'.$row["targa_gas"].'</span>';    
        $descrizione_gas='<span  class="editable_descrizione" data-original-title ="Descrizione"  data-type="text" data-pk="'.$row["id_gas"].'">'.$row["descrizione_gas"].'</span>';    

    }else{
        $targa= $row["targa_gas"];
        $descrizione_gas= $row["descrizione_gas"];    
    }

    
    $riga.='<tr>

            <td></td>
            <td>'.$descrizione_gas.'</td>
            <td>'.$row["utenti_gas"].'</td>
            <td><a href="#ajax_rd4/user/scheda.php?id='.$useridEnc.'">'.$row["referente_gas"].'</a></td>
            <td>'.$gc.'</td>
            <td>'.$targa.'</td>
            </tr>';


}


$p .='<thead>
                    <tr>
                        <th></th>
                        <th>GAS</th>
                        <th>Utenti</th>
                        <th>Referente</th>
                        <th>GEO</th>
                        <th>Provincia</th>
                    </tr>
                </thead>
         <tbody>
         '.$riga.'
         </tbody>
         <tfoot>
             <tr>
                <th></th>
                <th>Totale utenti DES:</th>
                <th>'.$totale_utenti.'</th>
                <th></th>
                <th></th>
                <th></th>
             </tr>
         </tfoot>
         </table>
         </div>';



?>

<?php echo navbar2($title_navbar,$buttons); ?>

<!-- MAP -->
<div class="well well-sm">
                <div id="map_gas" style="width:100%;height:320px;">
                </div>
</div>
<!-- MAP -->
<hr>
<?php echo $p; ?>

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

        var oTable= $('#dt_basic').dataTable(
                        {"bPaginate": false,
                        "aoColumnDefs": [
                                { "sType": "numeric" }
                            ]
                        }
                        );
                        
                        
        $(".editable_targa").editable({
                url: 'ajax_rd4/des/_act.php',
                ajaxOptions: { dataType: 'json' },
                type: 'text',
                params:  {
                    'act': 'edit_targa_gas'
                },
                success: function(response, newValue) {
                    console.log(response);
                    if(response.result == 'KO'){
                        return response.msg;
                    }
                }
                });                
        $(".editable_descrizione").editable({
                url: 'ajax_rd4/des/_act.php',
                ajaxOptions: { dataType: 'json' },
                type: 'text',
                params:  {
                    'act': 'edit_descrizione_gas'
                },
                success: function(response, newValue) {
                    console.log(response);
                    if(response.result == 'KO'){
                        return response.msg;
                    }
                }
                });
        var initialize= function() {

        var latlng = new google.maps.LatLng(<?php echo _USER_DES_LAT ?>,<?php echo _USER_DES_LNG ?>);
        var image = "/gas4/img_rd4/GAS.png";
        //var image = "$RG_addr["img_gas_home"].'";
        var mapOptions = {
          zoom: <?php echo _USER_DES_ZOOM ?>,
          center: latlng,
          mapTypeId: google.maps.MapTypeId.TERRAIN,
          minZoom: 5,
          maxZoom: 12
          };

          var map = new google.maps.Map(document.getElementById('map_gas'), mapOptions);
          var locations = [<?php echo $geo_gas ?>];
          var infowindow = new google.maps.InfoWindow({
          //content: contentString,
              maxWidth: 200
          });

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


        }


        console.log("pagefunction");
        //------------HELP WIDGET
        document.title = '<?php echo "ReteDES.it :: $page_title";?>';
        <?php echo help_render_js($page_id);?>
        //------------END HELP WIDGET




        $(document).off("click",".hide_post");
        $(document).on("click",".hide_post", function(e){

        })
        initialize();

    } // end pagefunction

    $(window).unbind('gMapsLoaded');

    function loadMap(){
        console.log("LoadMap");
        $(window).bind('gMapsLoaded',pagefunction);
        window.loadGoogleMaps();

    }
    function loadDataTable(){
        loadScript("js/plugin/datatables/jquery.dataTables.min.js", function(){
            loadScript("js/plugin/datatables/dataTables.colVis.min.js", function(){
                loadScript("js/plugin/datatables/dataTables.tableTools.min.js", function(){
                    loadScript("js/plugin/datatables/dataTables.bootstrap.min.js", function(){
                        loadScript("js/plugin/datatable-responsive/datatables.responsive.min.js", loadMap)
                    });
                });
            });
        });
    }


    loadScript("js/plugin/summernote/summernote.min.js", loadScript("js/plugin/x-editable/x-editable.min.js", loadDataTable));


</script>