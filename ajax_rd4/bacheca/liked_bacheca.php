<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.gas.php");


$ui = new SmartUI;
$converter = new Encryption;

$page_title = "I miei messaggi preferiti";
$page_id ="liked_bacheca";


$title_navbar='I miei messaggi preferiti';


?>

<?php echo navbar2($title_navbar,$buttons); ?>

<!-- BACHECA -->
<div class="row">

    <div class="col-sm-12">

        <div class="well">

            <table class="table table-striped table-forum">
                <thead>
                    <tr>
                        <th colspan="2">
                            I messaggi preferiti
                        </th>
                    </tr>
                </thead>
                <tbody class="container_post">

                    <!-- add Post -->


                    <!-- end  add Post -->
                    <!-- add Post -->


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

        var carica_post=function( gas, id_ordine, utente, id_ditta, page){
            $.ajax({
              type: "POST",
              url: "ajax_rd4/bacheca/_act.php",
              dataType: 'json',
              data: {act: "show_bacheca_liked", page:page, gas:gas, id_ordine:id_ordine, utente:utente, id_ditta:id_ditta},
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

        console.log("pagefunction");
        //------------HELP WIDGET
        document.title = '<?php echo "ReteDES.it :: $page_title";?>';
        <?php echo help_render_js($page_id);?>
        //------------END HELP WIDGET


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
                        $($t).parents('tr').css('opacity', '0.6');
                    }else{
                        $($t).parents('tr').css('opacity', '1');
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
                        $($t).parents('tr').fadeOut();
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
            $.blockUI();
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
                    $.unblockUI();
                    ok("fatto");
                    $('.messaggio[data-id_post="'+id_post+'"]').next('div').remove();
                    $('.messaggio[data-id_post="'+id_post+'"]').empty().html(data.msg).fadeIn();
                }else{
                    ko(data.msg);
                }
            });
        })

        carica_post(<?php echo _USER_ID_GAS; ?>,0,0,0,1);

    } // end pagefunction


    loadScript("js/plugin/summernote/summernote.min.js", pagefunction);

</script>