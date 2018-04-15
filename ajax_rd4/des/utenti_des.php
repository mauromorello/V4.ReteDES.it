<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.gas.php");


$ui = new SmartUI;
$converter = new Encryption;

$page_title = "Gli utenti nel mio DES";
$page_id ="utenti_des";


$title_navbar='Utenti di: '._USER_DES_NOME;

if(_USER_PERMISSIONS&perm::puo_gestire_retegas){
    if(CAST_TO_INT($_GET["id_des"],0)>0){
        $id_des =  CAST_TO_INT($_GET["id_des"]);
    }else{
        $id_des =  _USER_ID_DES;
    }
}else{
    $id_des =  _USER_ID_DES;
}

if(($id_des==0 | $id_des==1) AND (!(_USER_PERMISSIONS & perm::puo_vedere_retegas))){
    ?><h3 class="text-center">Il tuo gas non appartiene ad alcun DES.</h3>
      <p class="text-center">Se sei interessato alla costituzione di un DES o all'inserimento del tuo GAS in un DES esistente<br> contatta <b>info@retedes.it</b> per avere tutte le informazioni in merito.</p>   <?php
    die();
}

if(!(_USER_PERMISSIONS & perm::puo_vedere_retegas)){
   ?><h3 class="text-center">Non hai i permessi per gestire il tuo DES.</h3><?php
   die();
}


//LISTA GEO USERS
$stmt = $db->prepare("SELECT U.*, G.* FROM maaking_users U inner join retegas_gas G on G.id_gas=U.id_gas INNER JOIN retegas_des D on D.id_des=G.id_des WHERE (user_gc_lat > 0) AND (G.id_des='".$id_des."');");
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $row) {
      $user_des .='["'.$row["fullname"].' - '.$row["descrizione_gas"].'", '.$row["user_gc_lat"].', '.$row["user_gc_lng"].',1], ';
}
$user_des = rtrim($user_des,", ");

$p  ='<div class="table-responsive">
                    <table class="table table-striped smart-form has-tickbox" id="dt_basic">';



$sql="SELECT U.*, G.* FROM maaking_users U inner join retegas_gas G on G.id_gas=U.id_gas inner join retegas_des D on D.id_des=G.id_des where D.id_des=".$id_des.";";

$stmt = $db->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $row) {
    if($row["user_gc_lat"]>0){
        $gc='<i class="fa fa-map-marker text-success"></i>';
    }else{
        $gc='';
    }

    $useridEnc = $converter->encode($row["userid"]);

    $cl='';

    if($row["isactive"]==0){
        $stato="@*";
        $cl=" bg-info ";
    }
    if($row["isactive"]==1){
        $stato="@A";
    }
    if($row["isactive"]==2){
        $stato="@S";
    }
    if($row["isactive"]==3){
        $stato="@C";
    }
    if($row["isactive"]==4){
        $stato="@T";
    }
    
    $riga.='<tr class="'.$cl.'">

            <td><label class="checkbox">
                <input class="utente" value="'.$row["userid"].'" type="checkbox">
                <i></i>
                </label></td>
            <td>'.$row["userid"].'</td>

            <td><a href="#ajax_rd4/user/scheda.php?id='.$useridEnc.'">'.$row["fullname"].'</a></td>
            <td>'.$row["email"].'</td>
            <td>'.$row["descrizione_gas"].'</td>

            <td>'.$gc.'</td>
            <td>'.$stato.'</td>
            </tr>';


}

//OPTION GAS
if(_USER_PERMISSIONS & perm::puo_gestire_retegas){
    $sql = "SELECT * FROM retegas_gas;";
}else{
    $sql = "SELECT * FROM retegas_gas WHERE id_des='"._USER_ID_DES."';";
}
$stmt = $db->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $row) {
    $option_gas.='<option value="'.$row["id_gas"].'">'.$row["descrizione_gas"].'</option>';
}

//OPTION GAS


$p .='<thead>
                    <tr>
                        <th></th>
                        <th>ID</th>
                        <th>Utente</th>
                        <th>Mail</th>
                        <th>GAS</th>
                        <th>GEO</th>
                        <th>STATO</th>
                    </tr>
                </thead>
         <tbody>
         '.$riga.'
         </tbody>
         <tfoot>
             <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
             </tr>
         </tfoot>
         </table>
         </div>
         <div class="well margin-top-5">
            <label>
                <input class="selectall" type="checkbox"> Seleziona / deseleziona tutti quelli visibili
            </label>
         </div>

         <div class="well margin-top-5">

            <p><button class="btn btn-success" id="manda_messaggio_a_utenti"><i class="fa fa-envelope"></i>&nbsp; Manda agli utenti selezionati un messaggio.</button></p>

            <label>Messaggio:</label>
            <textarea  id="messaggio_a_utenti" style="width:100%;"></textarea>
            <p></p>
            <div class="alert alert-danger"><strong>ATTENZIONE:</strong> Non abusare di questa funzione. A nessuno piace ricevere mail inutili.</div>
        </div>
        <div class="well margin-top-5">
            <h3>Sposta gli utenti selezionati in questo GAS:</h3>
            
            <form class="smart-form">
            <fieldset>
            <div class="row">
                <section class="col col-6">
                    <select style="width:100%" class="select2" id="gas_selection">
                        '.$option_gas.'
                    </select>
                </section>
                <section class="col col-6">
                    <label class="checkbox">
                        <input type="checkbox" name="conserva_dati" checked="checked" id="conserva_i_dati">
                        <i></i>Conserva i dati dell\'utente nel suo vecchio GAS
                    </label>
                </section>
                <p class="note">Leggi l\'help per capire come funziona questa opzione.</p>
            </div>
            </fieldset>
            </form>
        
            
            
            <p class="margin-top-5">
                <button class="btn btn-success pull-right" id="sposta_utenti"><i class="fa fa-arrow-right"></i>&nbsp; Sposta</button>
            </p>
            <div class="clearfix"></div>
        </div>
        <div class="well margin-top-5">
            <h3>Cambia "stato" agli utenti selezionati</h3>
            <button class="btn btn-success btn-lg btn-block cambia_stato" data-op="attiva">ATTIVA</button>
            <button class="btn btn-warning btn-lg btn-block cambia_stato" data-op="sospendi">SOSPENDI</button>
            <button class="btn btn-danger btn-lg btn-block cambia_stato" data-op="cancella">CANCELLA</button>
            <div class="note">NB: <strong>non</strong> verrà inviata alcuna mail in automatico per avvisarli del cambio di "stato".</div>
            <div class="clearfix"></div>
        </div>';



?>

<?php echo navbar2($title_navbar,$buttons); ?>

<!-- MAP -->

<?php if(_USER_PERMISSIONS & perm::puo_gestire_retegas){?><div class="alert alert-info">Sei qua come ADMIN: usa ?id_des=x</div><?php } ?>

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
                        {"bPaginate": true,
                        "aoColumnDefs": [
                                { "sType": "numeric" }
                            ]
                        }
                        );

        var initialize= function() {

        var latlng = new google.maps.LatLng(<?php echo _USER_DES_LAT ?>,<?php echo _USER_DES_LNG ?>);
        var image = "/gas4/img_rd4/male-2.png";
   
        var mapOptions = {
          zoom: <?php echo _USER_DES_ZOOM; ?>,
          center: latlng,
          mapTypeId: google.maps.MapTypeId.TERRAIN,
          minZoom: 5,
          maxZoom: 12
          };

          var map = new google.maps.Map(document.getElementById('map_gas'), mapOptions);
          var locations = [<?php echo $user_des ?>];
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

        });


        $('.selectall').click(function(event) {  //on click
            console.log("Click select");
            if(this.checked) { // check select status
                $('.utente').each(function() { //loop through each checkbox
                    this.checked = true;  //select all checkboxes with class "checkbox1"
                });
            }else{
                $('.utente').each(function() { //loop through each checkbox
                    this.checked = false; //deselect all checkboxes with class "checkbox1"
                });
            }
        });

        $(document).on( 'change', '#usermessage', function() {
            messaggio = $(this).val();
            console.log("messaggio = " + messaggio);

        });

        $('#sposta_utenti').click(function(){
            console.log("Click sposta");
            values = $('input:checkbox:checked.utente').map(function () {
              return this.value;
            }).get();
            
            var crea_ghost;
            if($('#conserva_i_dati').is(":checked")){
                crea_ghost = 1;    
            }else{
                crea_ghost = 0;
            }
            
            var id_gas = $('#gas_selection').val();
            console.log(id_gas);
            if(values.length==0){
                ko("Nessuno da spostare");
            }else{
            console.log(values);

            $.SmartMessageBox({
                title : "Sposta",
                content : "Confermi? lo spostamento riguarderà " + values.length + " utenti",
                buttons : "[Esci][SPOSTA]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="SPOSTA"){
                    $.blockUI();
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/gas/_act.php",
                          dataType: 'json',
                          data: {act: "sposta_utenti", values : values, id_gas : id_gas, crea_ghost : crea_ghost},
                          context: document.body
                        }).done(function(data) {
                            $.unblockUI();
                            if(data.result=="OK"){
                                    ok(data.msg);
                                    //location.reload();
                            }else{
                                ko(data.msg);
                            }

                        });
                }
            });
        }//messaggio vuoto
        });

        $('#manda_messaggio_a_utenti').click(function(){
            console.log("Click");
            values = $('input:checkbox:checked.utente').map(function () {
              return this.value;
            }).get();
            messaggio = $('#messaggio_a_utenti').val();
            console.log("Messaggio " + messaggio);
            if(!messaggio){
                ko("Messaggio vuoto");
            }
            else if(values.length==0){
                ko("Nessun destinatario");
            }else{
            console.log(values);

            $.SmartMessageBox({
                title : "Messaggia",
                content : "Confermi? la mail sarà inviata a " + values.length + " utenti",
                buttons : "[Esci][INVIA]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="INVIA"){
                    $.blockUI();
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/gas/_act.php",
                          dataType: 'json',
                          data: {act: "messaggia_utenti", values : values, messaggio : messaggio},
                          context: document.body
                        }).done(function(data) {
                            $.unblockUI();
                            if(data.result=="OK"){
                                    ok(data.msg);
                                    //location.reload();
                            }else{
                                ko(data.msg);
                            }

                        });
                }
            });




        }//messaggio vuoto

        });

        //CAMBIA STATO
        $('.cambia_stato').click(function(){
            console.log("Click cambia stato");
            values = $('input:checkbox:checked.utente').map(function () {
              return this.value;
            }).get();

            var op = $(this).data('op');
            console.log(values);

            $.SmartMessageBox({
                title : "Cambia stato",
                content : "Confermi? lo stato sarà cambiato a " + values.length + " utenti",
                buttons : "[Esci][OK]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="OK"){
                    $.blockUI();
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/gas/_act.php",
                          dataType: 'json',
                          data: {act: "cambia_stato_utenti", values : values, op : op},
                          context: document.body
                        }).done(function(data) {
                            $.unblockUI();
                            if(data.result=="OK"){
                                ok(data.msg);
                            }else{
                                ko(data.msg);
                            }

                        });
                }
            });

        });





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


    loadScript("js/plugin/summernote/summernote.min.js", loadDataTable);


</script>