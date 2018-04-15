<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.gas.php");


$ui = new SmartUI;
$converter = new Encryption;

$page_title = "Post singolo";
$page_id ="post_singolo";



$title_navbar='Post';


$id_post = CAST_TO_INT($_POST["id"],0);

if($id_post==0){
    $id_post = CAST_TO_INT($_GET["id"],0);
    if($id_post==0){
        echo rd4_go_back("Questo post non esiste");
        die();
    }
}



$sql="SELECT * FROM retegas_bacheca where id_bacheca=:id_post LIMIT 1;";
$stmt = $db->prepare($sql);
$stmt->bindParam(':id_post', $id_post, PDO::PARAM_INT);
$stmt->execute();

$p = $stmt->fetch();



if(CAST_TO_STRING($p["code"])==""){

    $codice=random_string(39);

    $url=APP_URL."/p/?ost=".$codice;
    $short_url = json_decode(file_get_contents("https://api.bit.ly/v3/shorten?login=o_4l9fuqj0lh&apiKey=R_c3f01107d84d4b3babee919004018795&longUrl=".urlencode($url)."&format=json"))->data->url;

    if($short_url<>""){
        $stmt = $db->prepare("UPDATE retegas_bacheca SET code=:code, public_url=:public_url WHERE id_bacheca=:id_post LIMIT 1;");
        $stmt->bindParam(':public_url', $short_url, PDO::PARAM_STR);
        $stmt->bindParam(':code', $codice, PDO::PARAM_STR);
        $stmt->bindParam(':id_post', $id_post, PDO::PARAM_INT);
        $stmt->execute();
    }
}else{

   $short_url=$p["public_url"];

}


$url_addthis='href="https://api.addthis.com/oexchange/0.8/offer?url='.$short_url.'" rel="nofollow" ';
$url_facebook='href="https://api.addthis.com/oexchange/0.8/forward/facebook/offer?url='.$short_url.'" rel="nofollow" ';
$url_twitter='href="https://api.addthis.com/oexchange/0.8/forward/twitter/offer?url='.$short_url.'" rel="nofollow" ';


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

<?php //echo navbar2($title_navbar,$buttons); ?>


<!-- Go to www.addthis.com/dashboard to customize your tools -->


<!-- BACHECA -->

<div class="row">
    <div class="col-sm-12">
        <div class="well">
            <?php if($p["id_utente"]==_USER_ID){?>
                <p class="alert alert-danger">
                <strong>ATTENZIONE PRIVACY:</strong> In questo istante solo tu e chi è iscritto a reteDES può vedere questo post. Questo significa che il resto del mondo ne è tagliato fuori. Questo link che ti propongo, così come i pulsanti "condividi con" rendono il post VISIBILE ALL'ESTERNO.<br>
                Solo chi ha il link può arrivare alla pagina, ma sappiamo bene tutti come funziona internet: una volta fuori nel mondo il post può essere copiato, ripreso, ecc ecc.<br>
                </p>
            <?php }else{?>
            <h3>Visualizzazione singolo post della bacheca</h3>
            <?php }?>
        </div>
    </div>
</div>

<div class="row">

    <div class="col-sm-12">

        <div class="well">

            <table class="table table-striped table-forum">
                <thead>
                    <tr>
                        <th colspan="2">
                            <?php if($p["id_utente"]==_USER_ID){?>
                            <div>
                                Link pubblico: <a href="<?php echo $short_url;?>" target="_BLANK"><?php echo $short_url;?></a><br>
                            </div>
                            <div class="pull-right">
                               condividi su &nbsp;
                                    <a <?php echo $url_facebook;?> class="btn btn-info btn-circle"><i class="fa fa-facebook"></i></a>&nbsp;
                                    <a <?php echo $url_twitter;?> class="btn btn-warning btn-circle"><i class="fa fa-twitter"></i></a>&nbsp;
                                    <a <?php echo $url_addthis;?> class="btn btn-danger btn-circle"><i class="fa fa-plus-square-o"></i></a>
                            </div>
                            <div class="clearfix"></div>

                            <?php }?>
                        </th>
                    </tr>
                </thead>
                <tbody class="container_post">
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

<!-- Go to www.addthis.com/dashboard to customize your tools -->



<script type="text/javascript">

    pageSetUp();
    var isEditing=false;


    var pagefunction = function(){

        function sendFile(file, editor, welEditable, dup, container) {
          console.log("sendfile acting...");
          $.blockUI();
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
              $.unblockUI();
              // getting the url of the file from amazon and insert it into the editor
              var url = $(data).find('Location').text();
              //editor.insertImage(welEditable, url);
              $(container).summernote('editor.insertImage', url);

            }
          });
        }

        var carica_post=function(id_post){
            $.ajax({
              type: "POST",
              url: "ajax_rd4/bacheca/_act.php",
              dataType: 'json',
              data: {act: "show_bacheca", id_post:id_post},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                    //ok(data.msg);

                    $('.container_post').append(data.post);
                    $( ".messaggio img" ).wrapAll(function() {
                        console.log("Wrapping: " + $( this ).attr('src') )
                        return "<a class='swipebox' title='Immagine' href='" + $( this ).attr('src') + "'></a>";
                    });
                    $('.swipebox').swipebox();
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


        /*LIKED POST*/
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

        /*VETRINA*/
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


        /*HIDE*/
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

        /*EDIT POST*/
        $(document).off("click",".edit_post");
        $(document).on("click",".edit_post", function(e){
            console.log("is Editing: " + isEditing);
            if(isEditing===false){
                isEditing=true;
                console.log("EDITING: is Editing: " + isEditing);
                var $t=$(this);
                var id_post=$(this).data("id_post");

                $('.editato_'+id_post).remove();

                console.log("editing post: " + id_post);

                $('<div class="summernote_'+id_post+'"></div>')
                  .insertAfter('.messaggio[data-id_post="'+id_post+'"]')
                  .html($('.messaggio[data-id_post="'+id_post+'"]').html())
                  .summernote({
                    height : 180,
                    focus : false,
                    tabsize : 2,
                    callbacks:{
                    onImageUpload: function(files, editor, $editable) {
                            $('.sto_caricando').show();
                            console.log("calling sendfile...");
                            $.each(files, function (idx, file) {
                                    console.log("calling for "+file.name);
                                    sendFile(file,editor,$editable,file.name,'.summernote_'+id_post);
                            });
                        }
                    }
                  });
                $('.messaggio[data-id_post="'+id_post+'"]').empty();
                $('.summernote_'+id_post).next('div').append('<button class="btn btn-primary pull-right margin-top-5 save_edited_post" data-id_post="'+id_post+'"> Salva le modifiche</button>');

            }
        })

        /*SAVE EDITED*/
        $(document).off("click",".save_edited_post");
        $(document).on("click",".save_edited_post", function(e){

            $.blockUI();
            var $t=$(this);
            var id_post=$(this).data("id_post");
            //var sHTML = $('.messaggio[data-id_post="'+id_post+'"]').code();
            //var sHTML = $('.messaggio[data-id_post="'+id_post+'"]').summernote('code');

            var sHTML = $('.summernote_'+id_post).summernote('code');
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
                    isEditing=false;
                    ok("fatto");
                    $('.summernote_'+id_post).next('div').remove();
                    $('.summernote_'+id_post).remove();
                    $('.messaggio[data-id_post="'+id_post+'"]').empty().html(data.msg).fadeIn();

                    //$('.messaggio[data-id_post="'+id_post+'"]').replaceWith(data.msg);
                    
                    console.log("is editing: " + isEditing);
                }else{
                    ko(data.msg);
                }
            });
        })

        carica_post(<?php echo $id_post; ?>);

    } // end pagefunction

    loadScript("js_rd4/plugin/swipebox/jquery.swipebox.min.js",
        loadScript("js/plugin/summernote/new_summernote.min.js", pagefunction)
    );
</script>
<!--<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-584be86083a7ed29"></script>-->