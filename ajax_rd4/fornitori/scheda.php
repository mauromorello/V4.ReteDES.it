<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.ditta.php");

$ui = new SmartUI;
$page_title = "Scheda fornitore";
$id_ditte = CAST_TO_INT($_GET["id"],0);
$id_gas= _USER_ID_GAS;
$D = new ditta($id_ditte);


if($id_ditte<1){
    echo rd4_go_back("Non hai i permessi per vedere questa pagina");
    die();
}
$stmt = $db->prepare("SELECT * FROM  retegas_ditte WHERE id_ditte = :id_ditte;");
$stmt->bindParam(':id_ditte', $id_ditte, PDO::PARAM_INT);
$stmt->execute();
if($stmt->rowCount()<>1){
    echo rd4_go_back("Questa ditta non esiste");
    die();
}


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

if(_USER_PERMISSIONS & perm::puo_vedere_tutti_ordini){

    $stmt = $db->prepare("SELECT COUNT(valore_int) as conto FROM retegas_options
                             WHERE id_ditta=:id_ditta AND id_gas=:id_gas AND chiave='_GAS_FORNITORE_PREFERITO'");
        $stmt->bindParam(':id_ditta', $id_ditte, PDO::PARAM_INT);
        $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
        $stmt->execute();
        $rowS = $stmt->fetch();
        if($rowS["conto"]>0){
            $star="fa-star";
        }else{
            $star="fa-star-o";
        }

        $fornitori_preferiti='<button class="fornitore_preferito btn btn-link"><i class="preferito_icon fa '.$star.' text-success"></i> Preferito dal tuo GAS</button>';
    }else{
        $fornitori_preferiti='';
    }

    /*FORNITORE BANNATO*/
    if($id_ditte>0){
        $id_utente = _USER_ID;
        $stmt = $db->prepare("SELECT COUNT(valore_int) as conto FROM retegas_options
                             WHERE id_ditta=:id_ditta AND id_user=:id_user AND chiave='_USER_FORNITORE_BANNATO'");
        $stmt->bindParam(':id_ditta', $id_ditte, PDO::PARAM_INT);
        $stmt->bindParam(':id_user', $id_utente, PDO::PARAM_INT);
        $stmt->execute();
        $rowS = $stmt->fetch();
        if($rowS["conto"]>0){
            $ban="fa-toggle-off ";
            $ricevo_notifiche = "<strong>NO</strong> ";
        }else{
            $ban="fa-toggle-on ";
            $ricevo_notifiche = "<strong>SI</strong> ";
        }

        $fornitore_escluso='<button class="fornitore_bannato btn btn-link"><i class="bannato_icon fa '.$ban.' text-info"></i> <span class="bannato_testo">'.$ricevo_notifiche.' notifiche</span></button>';

    }





/*FORNITORE BANNATO*/

if (!empty($row)) {
    $c.='<div id="mio_fornitore_container">
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <label for="descrizione_ditte">Nome:</label>
            <p id="descrizione_ditte" class="'.$editable.' font-xl" data-type="text" data-pk="'.$row["id_ditte"].'">'.$row["descrizione_ditte"].'</p>
            <label for="indirizzo">Indirizzo:</label>
            <p id="indirizzo" class="editable_map" data-type="textarea" data-pk="'.$row["id_ditte"].'">'.$row["indirizzo"].'</p>
            <div class="hidden" id="id_ditte" rel="'.$row["id_ditte"].'"></div>
            <div class="hidden" id="ditte_gc_lat" rel="'.$row["ditte_gc_lat"].'"></div>
            <div class="hidden" id="ditte_gc_lng" rel="'.$row["ditte_gc_lng"].'"></div>
            <div id="map-canvas" style="width:100%;height:280px;"></div>
        </div>';
    if($proprietario=="true"){

    $c.='<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            
            <hr>
            <label for="contatto">Contatto:</label>
            <p id="contatto" class="'.$editable.'" data-type="text" data-pk="'.$row["id_ditte"].'">'.$row["contatto"].'</p>

            <label for="telefono">Telefono:</label>
            <p id="telefono" class="'.$editable.'" data-type="text" data-pk="'.$row["id_ditte"].'">'.$row["telefono"].'</p>
            <label for="mail_ditte">Email:</label>
            <p id="mail_ditte" class="'.$editable.'" data-type="text" data-pk="'.$row["id_ditte"].'">'.$row["mail_ditte"].'</p>
            <label for="website">Link:</label>
            <p id="website" class="'.$editable.'" data-type="text" data-pk="'.$row["id_ditte"].'">'.$row["website"].'</p>
            <label for="iban">IBAN:</label>
            <p id="iban" class="'.$editable.'" data-type="text" data-pk="'.$row["id_ditte"].'">'.$row["iban"].'</p>
            <label for="iban">P.IVA o COD FISC.:</label>
            <p id="piva_ditte" class="'.$editable.'" data-type="text" data-pk="'.$row["id_ditte"].'">'.$row["P_IVA"].'</p>
            
            <hr>
            <label for="tag_ditte">Parole chiave:</label>
            <input id="tag_ditte"  data-role="tagsinput" value="'.$row["tag_ditte"].'" />
            <p class="note">NON usare questo campo per taggare il proprio GAS, grazie :)</p>
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
    </div>';
    }else{

        $url=$row["website"];
        if(substr( $url, 0, 7 ) === "http://"){

        }else{
            if(substr( $url, 0, 8 ) === "https://"){
                $url = "https://".$url;
            }else{
                $url = "http://".$url;
            }
        }

        $c.='<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">

                <label for="contatto">Contatto:</label>
                <p id="contatto" style="background-color:#EEE">'.$row["contatto"].'</p>

                <label for="telefono">Telefono:</label>
                <p style="background-color:#EEE"><a href="tel:+39'.ltrim($row["telefono"],'0').'">'.$row["telefono"].'</a></p>
                <label for="mail_ditte">Email:</label>
                <p style="background-color:#EEE"><a href="mailto:'.$row["mail_ditte"].'">'.$row["mail_ditte"].'</a></p>
                <label for="website">Link:</label>
                <p style="background-color:#EEE"><a href="'.$url.'" target="_BLANK">'.$row["website"].'</a></p>
                <label for="iban">IBAN:</label>
                <p style="background-color:#EEE"><strong>'.$row["iban"].'</strong></p>
                <label for="iban">P.IVA / COD FISC.:</label>
                <p style="background-color:#EEE"><strong>'.$row["P_IVA"].'</strong></p>
                <hr>
                <label for="tag_ditte">Parole chiave:</label>
                <input id="tag_ditte" data-role="tagsinput" value="'.$row["tag_ditte"].'" />
                
                <hr>
            </div>

        </div>
        <div class="well well-sm margin-top-10 padding-5">
            <label for="note_ditte">Note:</label>
            <div id="note_ditte">'.$row["note_ditte"].'</div>
            <div class="clearfix"></div>
        </div>
        </div>';
    }
}



$title_navbar="Scheda ditta";
if(_USER_PERMISSIONS & perm::puo_creare_listini){
    //$buttons[]='<form style="margin-right:10px;"><button  data-id_ditta="'.$row["id_ditte"].'" class="aggiungi_listino btn btn-default btn-success navbar-btn"><i class="fa fa-plus"></i> Nuovo Listino</button></form>';
    $buttons[]='<button  data-id_ditta="'.$row["id_ditte"].'" class="aggiungi_listino btn btn-link"><i class="fa fa-plus"></i> Nuovo Listino</button>';
    $buttons[]=$fornitori_preferiti;
}

$buttons[]=$fornitore_escluso;
if($proprietario<>"true"){
    $buttons[]='<button class="btn btn-link"  data-toggle="modal" data-target="#modal_segnalazione_ditta" id="chiedi_info_button"><i class="fa fa-bullhorn text-info"></i> Segnala un problema</button>';
}
//SEGNALAZIONI DITTA
if($proprietario=="true"){

    $rows = $D->lista_segnalazioni_ditta();
    foreach($rows as $row){
        if($row["is_hidden"]==0){
            $lsl .='<li class="list-item"><i class="fa fa-times text-danger hide_segnalazione" data-id="'.$row["id_segnalazione"].'"></i> '.$row["testo_segnalazione"].' <span class="note">di '.$row["fullname_segnalante"].' del '.conv_date_from_db($row["data_segnalazione"]).'</a></li>';    
        }
    }    
    if($lsl<>""){
        $lsl='
                <div class="well well-sm margin-top-10">
                <h1>Segnalazioni per questa ditta:</h1>
                <ul class="list-unstyled">
                    '.$lsl.'
                </ul>
                </div>';
    }
    
}else{
    $rows = $D->lista_segnalazioni_ditta();
    foreach($rows as $row){
        if($row["id_segnalante"]==_USER_ID){
            $lsl .='<li class="list-item"><i class="fa fa-bullhorn text-success"></i> '.$row["testo_segnalazione"].'  <span class="note">del '.conv_date_from_db($row["data_segnalazione"]).'</span></li>';    
        }
    }    
    
    if($lsl<>""){
        $lsl='
                <div class="well well-sm margin-top-10">
                <h1>Su questa ditta hai fatto queste segnalazioni:</h1>
                <ul class="list-unstyled">
                    '.$lsl.'
                </ul>
                </div>';
    }    
    
}



?>


<?php echo navbar2($title_navbar,$buttons); ?>
<?php echo $lsl; ?>
<section id="widget-grid" class="margin-top-10">

    <div class="row">
        <!-- PRIMA COLONNA-->

        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well well-sm">
                <?php echo $c; ?>
            </div>
        </article>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well well-sm">
                <div id="listini_fornitore"></div>
            </div>
        </article>

        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="well">
                    <table class="table table-striped table-forum">
                        <thead>
                            <tr>
                                <th colspan="2">
                                    <i class="fa fa-comments"></i>&nbsp;
                                    Bacheca di questo fornitore
                                    <button class="pull-right show_add_post btn btn-xs btn-info"><i class="fa fa-plus"></i> Aggiungi commento</button>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="container_post">
                            <!-- add Post -->
                            <tr id="add_post" style="display: none;">
                                <td class="text-center" style="width: 12%;">
                                    <div class="push-bit">
                                        <strong>Nuovo commento</strong>
                                    </div>
                                </td>
                                <td>
                                    <div id="forumPostDitte"></div>
                                    <button class="btn btn-primary margin-top-10 pull-right save_post" data-id_ordine="0" data-id_ditta="<?php echo $id_ditte; ?>">Pubblica</button>
                                </td>
                            </tr>
                            <!-- end  add Post -->

                        </tbody>
                    </table>
                </div>

        </article>

        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html("scheda_fornitore",$page_title); ?>
        </article>

    </div>
    <div class="modal fade" id="modal_segnalazione_ditta" tabindex="-1" role="dialog" aria-labelledby="richiesta_info_Label">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Segnalazione sulla ditta #<?php echo $id_ditte; ?></h4>

              </div>
              <div class="modal-body">
                <form class="form-horizontal" role="form">
                  <div class="form-group">
                    <label  class="col-sm-2 control-label"
                              for="inputEmail3">Messaggio</label>
                    <div class="col-sm-10">
                        <textarea type="textarea" class="form-control" id="inputEmail3" placeholder="scrivi qua..." rows="6"></textarea>
                    </div>
                  </div>


                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <button type="submit" class="btn btn-default" id="do_segnalazione_ditta">Invia</button>
                      <p class="note margin-top-10">Cliccando su "invia" verrà inoltrata una mail al gestore della ditta e al gestore del GAS di appartenenza;</p>
                    </div>
                  </div>
                </form>
                
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Annulla</button>
              </div>
            </div>
          </div>
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
                    codeAddress(response.msg ,true, <?php echo $id_ditte;?>);
                    initialize($('#ditte_gc_lat').attr('rel'),$('#ditte_gc_lng').attr('rel'));
                }
            }
        });
    }
    function start_summer(){
        $('.summernote').summernote({
            height : 180,
            focus : false,
            tabsize : 2,
        toolbar: [
                ['style', ['bold', 'italic', 'underline','clear']],
                ['para', ['ul', 'ol', 'paragraph']],
              ]

        });

        $('#forumPostDitte').summernote({
            height : 180,
            focus : false,
            tabsize : 2,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
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

                  for(var i in results[0].address_components){
                        console.log(results[0].address_components[i].short_name);
                  }

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


        <?php if($editable=="editable"){?>loadScript("js/plugin/x-editable/x-editable.min.js", startEdit);<?php }?>
        loadScript("js/plugin/summernote/summernote.min.js",start_summer);



        //-------------------------HELP
        document.title = '<?php echo "ReteDES.it :: $page_title";?>';
        <?php echo help_render_js("scheda_fornitore"); ?>
        //-------------------------HELP
        console.log("Start Pagefuncion");

        //BACHECA------------------------------



        var carica_post=function( gas, id_ordine, utente, id_ditta, page){
            $.ajax({
              type: "POST",
              url: "ajax_rd4/bacheca/_act.php",
              dataType: 'json',
              data: {act: "show_bacheca", page:page, gas:gas, id_ordine:id_ordine, utente:utente, id_ditta:id_ditta},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                    //ok(data.msg);

                    $('.container_post').append(data.post);
                }else{
                    ko(data.msg);
                }
            });

        }
        $(document).off("click",".show_add_post");
        $(document).on("click",".show_add_post", function(e){
            e.preventDefault();
            $("#add_post").show();
        })
        $(document).off("click",".liked_post");
        $(document).on("click",".liked_post", function(e){
            var $t=$(this);
            var id_post=$(this).data("id_post");
            console.log("liked " + id_post);
            $.ajax({
              type: "POST",
              url: "ajax_rd4/bacheca/_act.php",
              dataType: 'json',
              data: {act: "liked_post", id_post:id_post},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                    if(data.preferito=="SI"){
                        $('.icona_liked[data-id_post="'+id_post+'"]').removeClass("fa-star-o").addClass("fa-star");
                    }else{
                        $('.icona_liked[data-id_post="'+id_post+'"]').removeClass("fa-star").addClass("fa-star-o");
                    }
                }else{
                    ko(data.msg);
                }
            });
        })
        $(document).off("click",".save_post");
        $(document).on("click",".save_post", function(e){
            e.preventDefault();
            var sHTML = $('#forumPostDitte').code();
            var id_ordine = $(this).data("id_ordine");
            var id_ditta = $(this).data("id_ditta");
            console.log("save" + sHTML);
            $.ajax({
              type: "POST",
              url: "ajax_rd4/bacheca/_act.php",
              dataType: 'json',
              data: {act: "save_post", sHTML :sHTML, id_ordine:id_ordine, id_ditta:id_ditta },
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                    $('.post_item').remove();
                    $('#forumPostDitte').code('');
                    $("#add_post").fadeOut();
                    console.log("Loading after save post");
                    carica_post(0,0,0,<?php echo $id_ditte;?>,1);

                }else{
                    ko(data.msg);
                }
            });
        })
        $(document).off("click",".hide_post");
        $(document).on("click",".hide_post", function(e){
            var $t=$(this);
            var id_post=$(this).data("id_post");
            console.log("hide " + id_post);
            $.ajax({
              type: "POST",
              url: "ajax_rd4/bacheca/_act.php",
              dataType: 'json',
              data: {act: "hide_post", id_post:id_post},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                    if(data.msg=="HIDE"){
                        $($t).parents('tr').css('opacity', '0.4');
                    }else{
                        $($t).parents('tr').css('opacity', '1');
                    }
                }else{
                    ko(data.msg);
                }
            });
        })
        $(document).off("click",".delete_post");
        $(document).on("click",".delete_post", function(e){
            var $t=$(this);
            var id_post=$(this).data("id_post");
            console.log("delete " + id_post);
            $.ajax({
              type: "POST",
              url: "ajax_rd4/bacheca/_act.php",
              dataType: 'json',
              data: {act: "delete_post", id_post:id_post},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                    $($t).parents('tr').fadeOut();
                }else{
                    ko(data.msg);
                }
            });
        })
        $(document).off("click",".edit_post");
        $(document).on("click",".edit_post", function(e){
            var $t=$(this);
            var id_post=$(this).data("id_post");
            //$('.messaggio[data-id_post="'+id_post+'"]').fadeOut();
            console.log("editing" + id_post);
            $('.messaggio[data-id_post="'+id_post+'"]').summernote({
                height : 180,
                focus : false,
                tabsize : 2,
                toolbar: [
                    //[groupname, [button list]]
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],

                  ]

            });
            $('.messaggio[data-id_post="'+id_post+'"]').next('div').append('<button class="btn btn-primary pull-right margin-top-5 save_edited_post" data-id_post="'+id_post+'"> Salva le modifiche</button>');

        })
        $(document).off("click",".save_edited_post");
        $(document).on("click",".save_edited_post", function(e){
            var $t=$(this);
            var id_post=$(this).data("id_post");
            var sHTML = $('.messaggio[data-id_post="'+id_post+'"]').code();
            console.log("save edited " + id_post);
            $.ajax({
              type: "POST",
              url: "ajax_rd4/bacheca/_act.php",
              dataType: 'json',
              data: {act: "save_edited_post", id_post:id_post, sHTML:sHTML},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                    ok("fatto");
                    $('.messaggio[data-id_post="'+id_post+'"]').next('div').remove();
                    $('.messaggio[data-id_post="'+id_post+'"]').empty().html(data.msg).fadeIn();
                }else{
                    ko(data.msg);
                }
            });
        })

        $(document).off("click",".show_posts");
        $(document).on("click",".show_posts", function(e){
            e.preventDefault();
            $('button.show_posts').remove();
            console.log("show_post");
            var page=$(this).data("page");
            var gas=$(this).data("gas");
            var id_ordine = $(this).data("id_ordine");
            var id_ditta = $(this).data("id_ditta");
            var utente = $(this).data("utente");
            console.log("Loading after show posts");
            carica_post(gas, id_ordine, utente, id_ditta ,page);

        })
        //BACHECA------------------------------



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
                $(document).off("change","#tag_ditte");
                $(document).on("change","#tag_ditte", function(event){
                //$('#tag_ditte').on('change', function(event) {

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

                // ---> PREFERITO
                $('.fornitore_preferito').on('click', function(event) {
                    console.log("fornitore preferito");
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/fornitori/_act.php",
                          dataType: 'json',
                          data: {act: "fornitore_preferito",id_ditta:<?php echo $id_ditte;?>},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                ok(data.msg);
                                if(data.preferito=="SI"){
                                    $('.preferito_icon').removeClass("fa-star-o").addClass("fa-star");
                                }else{
                                    $('.preferito_icon').removeClass("fa-star").addClass("fa-star-o");
                                }
                            }else{
                                ko(data.msg);
                            }

                        });
                });

                // -------> BANNATO

                $('.fornitore_bannato').on('click', function(event) {
                    console.log("fornitore bannato");
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/fornitori/_act.php",
                          dataType: 'json',
                          data: {act: "fornitore_bannato",id_ditta:<?php echo $id_ditte;?>},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                ok(data.msg);
                                if(data.bannato=="SI"){
                                    $('.bannato_icon').removeClass("fa-toggle-on").addClass("fa-toggle-off");
                                    $('.bannato_testo').html('<strong>NO </strong>notifiche');
                                }else{
                                    $('.bannato_icon').removeClass("fa-toggle-off").addClass("fa-toggle-on");
                                    $('.bannato_testo').html('<strong>SI </strong>notifiche');
                                }
                            }else{
                                ko(data.msg);
                            }

                        });
                });

                //RIMUOVI SEGNALAZIONE DITTA
                $(document).off('click','.hide_segnalazione');
                $(document).on('click','.hide_segnalazione', function(e){
                    var segnalazione = $(this);
                    id_segnalazione = $(this).data('id');
                    console.log("hide segnalazione" + id_segnalazione);
                    $.ajax({
                          type: 'POST',
                          url: 'ajax_rd4/segnalazioni/_act.php',
                          dataType: 'json',
                          data: {act: 'hide_segnalazione', id_segnalazione : id_segnalazione},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=='OK'){
                                ok('Segnalazione tolta.');
                            }else{
                                ko(data.msg)
                            ;}
                        });
                });
                
        //BACHECA------------------------------
        console.log("Loading after start");
        carica_post(0,0,0,<?php echo $id_ditte;?>,1);
        //BACHECA------------------------------

        //SEGNALAZIONE DITTA
        $('#do_segnalazione_ditta').click(function(e){
            e.preventDefault();
            $('#modal_segnalazione_ditta').modal('hide')
            $.blockUI({ message: null });
            var messaggio = $('#inputEmail3').val().replace(/\r\n|\r|\n/g,"<br />");
            $.ajax({
              type: "POST",
              url: "ajax_rd4/segnalazioni/_act.php",
              dataType: 'json',
              data: {act: "do_segnalazione_ditta", messaggio:messaggio, id_ditte:<?php echo $id_ditte; ?>},
              context: document.body
            }).done(function(data) {
                $.unblockUI();
                if(data.result=="OK"){
                        $('#inputEmail3').val('');
                        ok(data.msg);
                }else{
                    ko(data.msg);
                }

            });


        })
        //SEGNALAZIONE DITTA
        
        
        $('#tag_ditte').tagsinput();
        
        
    };

    // end pagefunction

    // run pagefunction on load
    function loadMap(){
        console.log("LoadMap");
        
        $(window).bind('gMapsLoaded',pagefunction);
        window.loadGoogleMaps();

    }
    $(window).unbind('gMapsLoaded');
    loadScript("js/plugin/bootstrap-tags/bootstrap-tagsinput.min.js", loadMap);
    

</script>