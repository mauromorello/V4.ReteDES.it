<?php
require_once("inc/init.php");
if(file_exists("../../lib_rd4/class.rd4.ordine.php")){require_once("../../lib_rd4/class.rd4.ordine.php");}
if(file_exists("../lib_rd4/class.rd4.ordine.php")){require_once("../lib_rd4/class.rd4.ordine.php");}

if(!(_USER_PERMISSIONS & perm::puo_gestire_retegas)){die("KO");}

$page_title = "Admin log";
$page_id = "admin_log";

//foreach (new DirectoryIterator('../../public_rd4/logs/ordini/') as $fileInfo) {
//    if($fileInfo->isDot()) continue;
//    $id_ordine=CAST_TO_INT($fileInfo->getFilename());
//    $h .='<p class="log_ordine" rel='.$id_ordine.' style="cursor:pointer">'.$id_ordine.' : '.$O->descrizione_ordini.'</p>';
//}

$id_ordine = CAST_TO_INT($_GET["id"]);




?>
<div class="inbox-nav-bar no-content-padding">
    <h1 class="page-title txt-color-blueDark"><i class="fa fa-fw fa-unlock"></i> Admin LOG&nbsp;</h1>
</div>

<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <form class="smart-form">
                <fieldset>
                    <section>
                        <label class="label">Log ordine:</label>
                        <label class="input">
                            <input type="text" class="input-lg" id="ordine_log" value="<?php if($id_ordine>0){echo $id_ordine;}?>">
                        </label>
                    </section>
                </fieldset>

            </form>

        </article>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well well-sm" style="max-height:400px; overflow-y:auto;">
                <div id="log_box" style="font-family: monospace;"></div>
            </div>
        </article>
    </div>
</section>


<script type="text/javascript">

    pageSetUp();

    var pagefunction = function() {


        var timer;
        var delay = 600; // 0.6 seconds delay after last input

        $('#ordine_log').bind('input', function() {
            window.clearTimeout(timer);
            timer = window.setTimeout(function(){
                  console.log("fired");

                  var id_ordine = $('#ordine_log').val();
                  $.ajax({
                          type: "POST",
                          url: "ajax_rd4/admin/_act_admin.php",
                          dataType: 'json',
                          data: {act: "log_ordine", id_ordine:id_ordine},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    $('#log_box').html(data.msg);
                            }else{
                                    ko(data.msg);
                            }
                        });



            }, delay);
        })


    };

    // end pagefunction

    // run pagefunction on load
    pagefunction();

</script>