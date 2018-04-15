<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.gas.php");


$ui = new SmartUI;
$converter = new Encryption;

$page_title = "La Bacheca del mio GAS";
$page_id ="gas_bacheca";


$title_navbar='La bacheca del mio GAS';

if(_USER_PERMISSIONS & perm::puo_postare_messaggi){
    $buttons[]='<button  class="show_add_post btn btn-link animated wobble"></i>&nbsp<i class="fa fa-plus"></i> Nuovo Messaggio</button>';
}
$buttons[]='<button  class="show_filter_post btn btn-link"><i class="fa fa-filter"></i> Visualizza</button>';


//AMAZON S3
$bucket = 'retedes';
$folder = 'public_rd4/bacheca/gas/'._USER_ID_GAS;

// these can be found on your Account page, under Security Credentials > Access Keys
    $accessKeyId = __AMAZON_S3_ACCESS_KEY;
    $secret = __AMAZON_S3_SECRET_KEY;

$policy = base64_encode(json_encode(array(
  // ISO 8601 - date('c'); generates uncompatible date, so better do it manually
  'expiration' => date('Y-m-d\TH:i:s.000\Z', strtotime('+2 days')),
  'conditions' => array(
    array('bucket' => $bucket),
    array('acl' => 'public-read'),
    array('success_action_status' => '201'),
    array('starts-with', '$key', $folder.'/')
  )
)));

$signature = base64_encode(hash_hmac('sha1', $policy, $secret, true));


//AMAZON S3


?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.1/summernote.css" rel="stylesheet">
<?php echo navbar2($title_navbar,$buttons); ?>

<!-- BACHECA -->
<div class="row">

    <div class="col-sm-12">

        <div class="well">

            <table class="table table-striped table-forum">
                <thead>
                    <tr>
                        <th colspan="2">
                            I messaggi del tuo GAS
                        </th>
                    </tr>
                </thead>
                <tbody class="container_post">

                    <!-- add Post -->

                    <tr id="add_post" style="display: none;">
                        <td class="text-center" style="width: 12%;">
                            <div class="push-bit">
                                <strong>Nuovo messaggio</strong>
                            </div>

                        </td>
                        <td>

                            <div id="forumPost"></div>
                            <span class="pull-left"></span>
                            <span class="pull-left sto_caricando margin-top-10 animated bounceIn" style="display:none;"><i class="fa fa-spin fa-gear"></i> sto caricando la foto.... attendi pazientemente :)</span>
                            <button class="btn btn-primary margin-top-10 pull-right save_post">Pubblica</button>
                            <!--<button class="btn btn-success margin-top-10">Save for later</button>-->

                        </td>
                    </tr>
                    <!-- end  add Post -->
                    <!-- add Post -->

                    <tr id="filter_post" style="display: none;">
                        <td class="text-center" style="width: 12%;">
                            <div class="push-bit">
                                <strong>Filtra i messaggi</strong>
                            </div>

                        </td>
                        <td>
                            <div class="form-group">
                                <form class="smart-form">
                                <label class="label">Mostra post</label>
                                    <div class="inline-group">
                                        <label class="radio">
                                            <input type="radio" name="gas_post" checked="checked" value="gas">
                                            <i></i>Solo del gas</label>
                                        <label class="radio">
                                            <input type="radio" name="gas_post" value="0">
                                            <i></i>Del gas degli ordini e delle ditte</label>
                                    </div>
                                 </form>
                            </div>


                            <div class="form-group">
                                    <label>Mostra solo i messaggi di:</label>
                                    <select style="width:100%" class="select2 select-id_utente">
                                            <option value="0">Scegli...</option>
                                            <?php
                                             $G=new gas(_USER_ID_GAS);
                                             $rows=$G->lista_utenti_attivi();
                                             foreach($rows as $row){
                                                $l .='<option value="'.$row["userid"].'">'.$row["fullname"].'</option>';
                                             }
                                             echo $l;
                                             ?>
                                    </select>
                                    <div class="note">
                                        Seleziona tra gli utenti del tuo gas.
                                    </div>
                            </div>

                            <div class="form-group">
                                    <label>Mostra solo i messaggi di questo ordine:</label>
                                    <select style="width:100%" class="select2 select-id_ordine">
                                            <option value="0">Scegli...</option>
                                            <?php
                                             $stmt = $db->prepare("SELECT    O.id_ordini,
                                                                    O.data_apertura,
                                                                    O.data_chiusura,
                                                                    O.is_printable,
                                                                    O.descrizione_ordini,
                                                                    O.id_utente as id_referente,
                                                                    R.id_utente_referenze as id_referente_gas,
                                                                    R.convalida_referenze
                                                            FROM retegas_referenze R
                                                            INNER JOIN retegas_ordini O on O.id_ordini=R.id_ordine_referenze
                                                          WHERE R.id_gas_referenze=:id_gas and id_utente_referenze>0
                                                          ORDER BY O.data_apertura DESC;");
                                                          $id_gas = _USER_ID_GAS;
                                                          $stmt->bindParam(':id_gas', $id_gas , PDO::PARAM_INT);
                                                          $stmt->execute();
                                                          $rows = $stmt->fetchAll();


                                             foreach($rows as $row){
                                                $o .='<option value="'.$row["id_ordini"].'">#'.$row["id_ordini"].' '.$row["descrizione_ordini"].'</option>';
                                             }
                                             echo $o;
                                             ?>
                                    </select>
                                    <div class="note">
                                        Seleziona tra gli ordini del tuo gas.
                                    </div>
                            </div>

                            <div class="form-group">
                                    <label>Mostra solo i messaggi di questo fornitore:</label>
                                    <select style="width:100%" class="select2 select-id_ditte">
                                            <option value="0">Scegli...</option>
                                            <?php
                                             $stmt = $db->prepare("SELECT *
                                                            FROM retegas_ditte;");
                                                          $stmt->execute();
                                                          $rows = $stmt->fetchAll();

                                             foreach($rows as $row){
                                                $d .='<option value="'.$row["id_ditte"].'">#'.$row["id_ditte"].' '.$row["descrizione_ditte"].'</option>';
                                             }
                                             echo $d;
                                             ?>
                                    </select>
                                    <div class="note">
                                        Seleziona tra le ditte di retedes.
                                    </div>
                            </div>

                        </td>
                    </tr>
                    <!-- end  add Post -->

                </tbody>
            </table>

        </div>
    </div>
</div>
<!-- BACHECA -->


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

        function sendFile(file, editor, welEditable, dup, container) {
          console.log("sendfile acting...");

          formData = new FormData();
          formData.append('key', '<?php echo $folder; ?>/' + file.name);
          formData.append('AWSAccessKeyId', '<?php echo $accessKeyId; ?>');
          formData.append('acl', 'public-read');
          formData.append('policy', '<?php echo $policy; ?>');
          formData.append('signature', '<?php echo $signature; ?>');
          formData.append('success_action_status', '201');
          formData.append('file', file);

          $.ajax({
            data: formData,
            dataType: 'xml',
            type: "POST",
            cache: false,
            contentType: false,
            processData: false,
            url: "https://<?php echo $bucket ?>.s3.amazonaws.com/",
            success: function(data) {
              console.log("sendfile success!!");
              // getting the url of the file from amazon and insert it into the editor
              var url = $(data).find('Location').text();
              //editor.insertImage(welEditable, url);
              $(container).summernote('editor.insertImage', url);
              $('.sto_caricando').hide();
            }
          });
        }

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
                    $( ".messaggio img" ).wrap(function() {
                        return "<a class='swipebox' title='Immagine' href='" + $( this ).attr('src') + "'></a>";
                    });
                    $('.swipebox').swipebox();
                    // RIDIMENSIONA PER VIDEO
                    var $allVideos = $("iframe[src^='//player.vimeo.com'], iframe[src^='//www.youtube.com']"),
                        $fluidEl = $(".messaggio");
                    $allVideos.each(function() {
                      $(this)
                        .data('aspectRatio', this.height / this.width)
                        .removeAttr('height')
                        .removeAttr('width');
                    });
                    $(window).resize(function() {
                      var newWidth = $fluidEl.width()-20;
                      $allVideos.each(function() {
                        var $el = $(this);
                        $el
                          .width(newWidth)
                          .height(newWidth * $el.data('aspectRatio'));
                      });
                    }).resize();
                    // RIDIMENSIONA PER VIDEO


                }else{
                    ko(data.msg);
                }
            });

        }

        console.log("pagefunction");
        //------------HELP WIDGET
        document.title = '<?php echo "ReteDES.it :: $page_title";?>';
        <?php echo help_render_js($page_id);?>
        //------------END HELP WIDGET

        $('#forumPost').summernote({
            height : 180,
            focus : false,
            tabsize : 2,
            //toolbar: [
            //    //[groupname, [button list]]
            //    ['style', ['bold', 'italic', 'underline', 'clear']],
            //    ['para', ['ul', 'ol', 'paragraph']],
            //
            //  ],
            callbacks:{
                onImageUpload: function(files, editor, $editable) {
                    $('.sto_caricando').show();
                    console.log("calling sendfile...");
                    $.each(files, function (idx, file) {
                            console.log("calling for "+file.name);
                            sendFile(file,editor,$editable,file.name,'#forumPost');
                    });
                }
            }
        });


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

            carica_post(gas, id_ordine, utente, id_ditta ,page);

        })
        $(document).off("click",".show_add_post");
        $(document).on("click",".show_add_post", function(e){
            e.preventDefault();
            $("#add_post").show();
        })
        $(document).off("click",".show_filter_post");
        $(document).on("click",".show_filter_post", function(e){
            e.preventDefault();
            $("#filter_post").show();
        })


        $(document).off("change",".select-id_utente");
        $(document).on("change",".select-id_utente", function(e){
            var id_utente=$(this).val();
            console.log(id_utente);
            $('.post_item').remove();
            carica_post(0,0,id_utente,0,1);
        })
        $(document).off("change",".select-id_ordine");
        $(document).on("change",".select-id_ordine", function(e){
            var id_ordine=$(this).val();
            console.log(id_ordine);
            $('.post_item').remove();
            carica_post(0,id_ordine,0,0,1);
        })
        $(document).off("change",".select-id_ditte");
        $(document).on("change",".select-id_ditte", function(e){
            var id_ditte=$(this).val();
            console.log(id_ditte);
            $('.post_item').remove();
            carica_post(0,0,0,id_ditte,1);
        })
        $(document).off("change","input[type=radio][name=gas_post]");
        $('input[type=radio][name=gas_post]').change(function() {
            console.log("gas_change");
            if (this.value == 'gas') {
                $('.post_item').remove();
                carica_post(<?php echo _USER_ID_GAS; ?> ,0,0,0,1);
            }
            else if (this.value == '0') {
                $('.post_item').remove();
                carica_post(0,0,0,0,1);
            }
        });

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
                        $($t).parents('tr').css('opacity', '0.5');
                    }else{
                        $($t).parents('tr').css('opacity', '1');
                    }
                }else{
                    ko(data.msg);
                }
            });
        })

        $(document).off("click",".vetrina_post");
        $(document).on("click",".vetrina_post", function(e){
            var $t=$(this);
            var id_post=$(this).data("id_post");
            console.log("vetrina " + id_post);
            $.ajax({
              type: "POST",
              url: "ajax_rd4/bacheca/_act.php",
              dataType: 'json',
              data: {act: "vetrina_post", id_post:id_post},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                    if(data.vetrina=="SI"){
                        $('.vetrina_post[data-id_post="'+id_post+'"]').removeClass("fa-toggle-off").addClass("fa-toggle-on");
                    }else{
                        $('.vetrina_post[data-id_post="'+id_post+'"]').removeClass("fa-toggle-on").addClass("fa-toggle-off");
                    }
                }else{
                    ko(data.msg);
                }
            });
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
                callbacks:{
                onImageUpload: function(files, editor, $editable) {
                        $('.sto_caricando').show();
                        console.log("calling sendfile...");
                        $.each(files, function (idx, file) {
                                console.log("calling for "+file.name);
                                sendFile(file,editor,$editable,file.name,'.messaggio[data-id_post="'+id_post+'"]');
                        });
                    }
                }
            });
            $('.messaggio[data-id_post="'+id_post+'"]').next('div').append('<button class="btn btn-primary pull-right margin-top-5 save_edited_post" data-id_post="'+id_post+'"> Salva le modifiche</button>');

        })
        $(document).off("click",".save_edited_post");
        $(document).on("click",".save_edited_post", function(e){
            $.blockUI({ message: null });
            var $t=$(this);
            var id_post=$(this).data("id_post");
            //var sHTML = $('.messaggio[data-id_post="'+id_post+'"]').code();
            var sHTML = $('.messaggio[data-id_post="'+id_post+'"]').summernote('code');
            var isVetrina;
            if($('.is_vetrina_check[data-id_post="'+id_post+'"]').prop('checked')){
                isVetrina=1;
            }else{
                isVetrina=0;
            }

            console.log("save edited " + id_post);
            $.ajax({
              type: "POST",
              url: "ajax_rd4/bacheca/_act.php",
              dataType: 'json',
              data: {act: "save_edited_post", id_post:id_post, sHTML:sHTML,is_vetrina:isVetrina},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                    $.unblockUI();
                    ok("fatto");
                    $('.messaggio[data-id_post="'+id_post+'"]').next('div').remove();
                    $('.messaggio[data-id_post="'+id_post+'"]').empty().html(data.msg).fadeIn();
                }else{
                    ko(data.msg);
                }
            });
        })
        $(document).off("click",".save_post");
        $(document).on("click",".save_post", function(e){
            //$('#summernote').summernote('code');
            $.blockUI({ message: null });
            var sHTML = $('#forumPost').summernote('code');
            console.log("save" + sHTML);
            $.ajax({
              type: "POST",
              url: "ajax_rd4/bacheca/_act.php",
              dataType: 'json',
              data: {act: "save_post", sHTML :sHTML },
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                    $('.post_item').remove();
                    $('#forumPost').summernote('code');
                    $("#add_post").fadeOut();
                    $.unblockUI();
                    carica_post(<?php echo _USER_ID_GAS; ?>,0,0,0,1);

                }else{
                    ko(data.msg);
                }
            });
        })









        carica_post(<?php echo _USER_ID_GAS; ?>,0,0,0,1);




    } // end pagefunction

    loadScript("js_rd4/plugin/swipebox/jquery.swipebox.min.js",
        loadScript("js/plugin/summernote/new_summernote.min.js", pagefunction)
    );
</script>