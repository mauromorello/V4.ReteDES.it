<?php
require_once("inc/init.php");
$ui = new SmartUI;
$converter = new Encryption;

$page_title = "Il mio GAS";

$stmt = $db->prepare("SELECT * FROM  maaking_users WHERE id_gas = '"._USER_ID_GAS."'");
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $row) {

    $useridEnc = $converter->encode($row["userid"]);

    $status ="";
    $status_n ="Attivo";
    $activate ='';
    $suspend ='<li>
                    <a href="javascript:sospendi(\''.$useridEnc.'\',\''.$row["fullname"].'\');"><i class="fa fa-warning"></i>&nbsp;Sospendi</a>
                </li>';
    $delete ='';

    if($row["isactive"]==2){
        $status = " warning ";
        $status_n = "Sospeso";
        $activate ='<li>
                    <a href="javascript:attiva(\''.$useridEnc.'\',\''.$row["fullname"].'\');"><i class="fa fa-check"></i>&nbsp; Attiva</a>
                </li>';
        $suspend ='';
        $delete = '<li>
                    <a href="javascript:elimina(\''.$useridEnc.'\',\''.$row["fullname"].'\');"><i class="fa fa-trash-o"></i>&nbsp;Elimina</a>
                  </li>';
    }
    if($row["isactive"]==3){
        $status = " danger ";
        $status_n = "Eliminato";
        $suspend ='';
        $delete='';
        $activate ='<li>
                    <a href="javascript:attiva(\''.$useridEnc.'\',\''.$row["fullname"].'\');"><i class="fa fa-check"></i>&nbsp; Attiva</a>
                </li>';
    }
    if($row["isactive"]==0){
        $status = " success ";
        $status_n = "in Attesa";
        $suspend ='';
        $delete='<li>
                    <a href="javascript:elimina(\''.$useridEnc.'\',\''.$row["fullname"].'\');"><i class="fa fa-trash-o"></i>&nbsp; Elimina</a>
                  </li>';
        $activate ='<li>
                     <a href="javascript:attiva(\''.$useridEnc.'\',\''.$row["fullname"].'\');"><i class="fa fa-check"></i>&nbsp; Attiva</a>
                    </li>';
    }


    if(_USER_PERMISSIONS & perm::puo_gestire_utenti){
        $p = '  '.$activate.'
                '.$suspend.'
                '.$delete.'
             ';
        $p1='<div class="btn-group">
                                <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-gear"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a data-id="'.$row["userid"].'" href="ajax_rd4/gas/inc/messaggio.php?id='.$row["userid"].'" data-toggle="modal" data-target="#remoteModal"><i class="fa fa-envelope"></i>&nbsp; Messaggia</a></li>
                                    <li class="divider"></li>
                                    '.$p.'
                                </ul>
        </div>';
    }else{
        $p1 = '<a class="btn btn-default" data-id="'.$row["userid"].'" href="ajax_rd4/gas/inc/messaggio.php?id='.$row["userid"].'" data-toggle="modal" data-target="#remoteModal"><i class="fa fa-envelope"></i></a>';
    }
    if((_USER_PERMISSIONS & perm::puo_gestire_utenti) OR $row["isactive"]==1){

        $z .= '<tr rel="'.$useridEnc.'" class="'.$status.'">
                <td>'.$row["fullname"].'</td>
                <td>'.$row["email"].'</td>
                <td>'.$row["tel"].'</td>
                <td>'.$status_n.'</td>
                <td>
                     '.$p1.'
                </td>
              </tr>';
    }
}


$a='                            <table id="dt_utenti_gas" class="table table-striped margin-top-10">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th><i class="fa fa-envelope"></i>&nbsp;Mail</th>
                                        <th>Tel</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    '.$z.'
                                </tbody>
                            </table>

                        ';
if(_USER_PERMISSIONS & perm::puo_gestire_utenti){

    $stmt = $db->prepare("SELECT count(*) FROM  maaking_users WHERE id_gas='"._USER_ID_GAS."'");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_NUM);
    $user_Tutti = $row[0];

    $stmt = $db->prepare("SELECT count(*) FROM  maaking_users WHERE id_gas='"._USER_ID_GAS."' and isactive=1");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_NUM);
    $user_Attivi = $row[0];

    $stmt = $db->prepare("SELECT count(*) FROM  maaking_users WHERE id_gas='"._USER_ID_GAS."' and isactive=2");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_NUM);
    $user_Sospesi = $row[0];

    $stmt = $db->prepare("SELECT count(*) FROM  maaking_users WHERE id_gas='"._USER_ID_GAS."' and isactive=3");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_NUM);
    $user_Eliminati = $row[0];

    $stmt = $db->prepare("SELECT count(*) FROM  maaking_users WHERE id_gas='"._USER_ID_GAS."' and isactive=0");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_NUM);
    $user_Attesa = $row[0];
    if ($user_Attesa>0){
        $btn_Attesa = '<a class="show_Attesa btn btn-success">in Attesa (<b>'.$user_Attesa.'</b>)</a>';
    }else{
        $btn_Attesa = "";
    }

$mp ='
        <div class="row ">
            <div class=" col-xs-4">
                <h4>Filtra la tabella:</h4>
                <div class="btn-group-vertical btn-block">
                    <a class="show_Tutti btn btn-default">Tutti (<b>'.$user_Tutti.'</b>)</a>
                    <a class="show_Sospesi btn btn-default">Sospesi (<b>'.$user_Sospesi.'</b>)</a>
                    <a class="show_Attivi btn btn-default">Attivi (<b>'.$user_Attivi.'</b>)</a>
                    <a class="show_Eliminati btn btn-default">Eliminati (<b>'.$user_Eliminati.'</b>)</a>
                    '.$btn_Attesa.'
                </div>
            </div>

            <div class="col-xs-4">
                <h4>Composizione GAS</h4>
                <br>
                <div style="margin-left: auto;margin-right: auto; width: 70%;">
                    <span  class="sparkline"  data-sparkline-type="pie" data-sparkline-offset="90" data-sparkline-piesize="128px">'.$user_Attivi.','.$user_Sospesi.','.$user_Eliminati.','.$user_Attesa.'</span>
                </div>
            </div>
            <div class="col-xs-4">
                <h4>Info <small>(Clicca su una riga...)</small></h4>
                <div id="schedina_utente"></div>
            </div>
        </div>
      ';

}
$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>true,
                    "deletebutton"=>false,
                    "colorbutton"=>true);
$wg_utentigas = $ui->create_widget($options);
$wg_utentigas->id = "wg_utentigas";
$wg_utentigas->body = array("content" => $mp.'<hr>'.$a,"class" => "");
$wg_utentigas->header = array(
    "title" => '<h2>Utenti</h2>',
    "icon" => 'fa fa-group'
    );


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
                <div id="map_gas" style="width:100%;height:160px;">
                </div>
            </div>
        </div>
        </div>';

$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>true,
                    "deletebutton"=>false,
                    "colorbutton"=>true);
$wg_schedagas = $ui->create_widget($options);
$wg_schedagas->id = "wg_schedagas";
$wg_schedagas->body = array("content" => $g,"class" => "");
$wg_schedagas->header = array(
    "title" => '<h2>Scheda</h2>',
    "icon" => 'fa fa-home'
    );

//LISTA GEO USERS
$stmt = $db->prepare("SELECT * FROM maaking_users WHERE (city<>'') AND (user_gc_lat > 0) AND (id_gas='"._USER_ID_GAS."');");
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

<section id="widget-grid" class="margin-top-10">

    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <?php echo help_render_html('gas_home',$page_title); ?>
        </article>
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <?php echo $wg_schedagas->print_html(); ?>
        </article>

    </div>

    <hr>

    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo $wg_utentigas->print_html(); ?>
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
        <?php echo help_render_js('gas_home');?>
        //------------END HELP WIDGET
        var oTable= $('#dt_utenti_gas').dataTable({
                                            "bPaginate": false
                                        });
        var id;
        var messaggio;

        console.log("Inizio Initialized");
        initialize();

        $('body').on('hidden.bs.modal', '.modal', function () {
          $(this).removeData('bs.modal');
        });

        $('body').on('shown.bs.modal','.modal', function(e) {
            console.log("Modal opened");
            id = $(e.relatedTarget).attr('data-id');
        });


        $(document).on( 'change', '#usermessage', function() {
            messaggio = $(this).val();
            console.log("messaggio = " + messaggio);

        });

        $('body').on('click', '#usermessage_go', function () {
            //invio il messaggio ciccio

            $.ajax({
              type: "POST",
              url: "ajax_rd4/gas/_act.php",
              dataType: 'json',
              data: {act: "messaggia", messaggio : messaggio, id:id},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                    ok(data.msg);
                    messaggio='';
                }else{
                    ko(data.msg);
                }
            });


            //chiudo il modal
            $('#remoteModal').modal('hide');
        });

        $('.show_Attesa').click(function(){oTable.fnFilter( 'in Attesa',3 );});
        $('.show_Attivi').click(function(){oTable.fnFilter( 'Attivo',3 );});
        $('.show_Sospesi').click(function(){oTable.fnFilter( 'Sospeso',3 );});
        $('.show_Eliminati').click(function(){oTable.fnFilter( 'Eliminato',3 );});
        $('.show_Tutti').click(function(){  oTable.fnFilter('',3);
                                            oTable.fnFilter('');});
        //MOstro solo gli attivi
        oTable.fnFilter( 'Attivo',3 );

        //se clicco
        $('#dt_utenti_gas tbody').on( 'click', 'tr', function () {
            var value = $(this).attr('rel');
                $.ajax({
                  type: "POST",
                  url: "ajax_rd4/gas/inc/info_utente.php",
                  data: {userid : value},
                  context: document.body
               }).done(function(data) {

                   $('#schedina_utente').html(data);


            });
        } );


    } // end pagefunction


    var attiva = function(userid, fullname) {

            $.SmartMessageBox({
                title : "Attiva " + fullname,
                content : "Verrà comunicata l'avvenuta attivazione con una mail.",
                buttons : "[Esci][Attiva]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="Attiva"){

                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/gas/_act.php",
                          dataType: 'json',
                          data: {act: "attiva_utente", value : userid},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    ok(data.msg);}else{ko(data.msg);}
                                    //location.reload();
                        });
                }
            });
        }
    var sospendi = function(userid, fullname) {

            $.SmartMessageBox({
                title : "Sospendi " + fullname,
                content : "L'utente rimarrà sospeso fino ad una nuova riattivazione.",
                buttons : "[Esci][Sospendi]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="Sospendi"){

                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/gas/_act.php",
                          dataType: 'json',
                          data: {act: "sospendi_utente", value : userid},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    ok(data.msg);
                                    //location.reload();
                            }else{ko(data.msg);}

                        });
                }
            });
        }

        var elimina = function(userid, fullname) {

            $.SmartMessageBox({
                title : "Elimina " + fullname,
                content : "L'utente eliminato non potrà più accedere, nè reiscriversi con la stessa mail.<br>Tutti i suoi dati (ordini, listini ecc) saranno comunque conservati.",
                buttons : "[Esci][Elimina]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="Elimina"){

                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/gas/_act.php",
                          dataType: 'json',
                          data: {act: "elimina_utente", value : userid},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    ok(data.msg);
                                    //location.reload();
                            }else{ko(data.msg);}

                        });
                }
            });
        }
    $(window).unbind('gMapsLoaded');

    function loadMap(){
        console.log("LoadMap");
        $(window).bind('gMapsLoaded',pagefunction);
        window.loadGoogleMaps();

    }


    loadScript("js/plugin/datatables/jquery.dataTables.min.js", function(){
        loadScript("js/plugin/datatables/dataTables.colVis.min.js", function(){
            loadScript("js/plugin/datatables/dataTables.tableTools.min.js", function(){
                loadScript("js/plugin/datatables/dataTables.bootstrap.min.js", function(){
                    loadScript("js/plugin/datatable-responsive/datatables.responsive.min.js", function(){

                            loadScript("js/plugin/x-editable/x-editable.min.js", loadMap)

                    });
                });
            });
        });
    });

</script>