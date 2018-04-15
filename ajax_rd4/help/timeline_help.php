<?php
require_once("inc/init.php");




$ui = new SmartUI;
$page_title = "Timeline Help";
$page_id = "timeline_help";

$id_help = CAST_TO_STRING($_GET["id_help"]);

$stmt = $db->prepare("SELECT O.id_option, O.note_1, O.valore_int,O.timbro, U.fullname from retegas_options O inner join maaking_users U on U.userid=O.id_user WHERE chiave='_HELP_V4' AND valore_text=:id_help order by valore_int asc;");
$stmt->bindParam(':id_help', $id_help, PDO::PARAM_STR);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$i=0;

foreach($rows as $row){


    $htmlA=$row["note_1"];


    if(_USER_PERMISSIONS & perm::puo_gestire_retegas){
        $pulsante_elimina='<i class="fa fa-times text-danger"></i>';
    }else{
        $pulsante_elimina='';
    }

    $new_text = $row["note_1"];

    $html = htmlDiff($old_text,$new_text);

    $h[]='
        <div class="help_item">
        <div class="well well-sm">
        <code> Versione '.$row["valore_int"].' di '.$row["fullname"].' del '.conv_datetime_from_db($row["timbro"]).'</code>
        <button class="btn btn-link pull-right delete_help" data-id_option="'.$row["id_option"].'">'.$pulsante_elimina.'</button>
        <hr>
        '.$html.'
        </div>
        <hr>
        </div>';

    $old_text = $new_text;

}
$rows = array_reverse($h);
foreach($rows as $row){
    $h.=$row;
}



?>

</style>
<h3>VERSIONE ATTUALE:</h3>
<div class="well well-lg"><?php echo $htmlA;?></div>
<hr>
<h3>VERSIONE PRECEDENTI: <small>Le aggiunte sono <ins>in verde</ins>, le eliminazioni sono <del>in rosso</del></small></h3>
<p><?php echo $h ?></p>

<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
        </article>
    </div>
</section>



<script type="text/javascript">
    pageSetUp();

    var pagefunction = function(){

        //------------HELP WIDGET
        <?php echo help_render_js($page_id);?>
        //------------END HELP WIDGET

        $(document).off('click','.delete_help');
        $(document).on('click','.delete_help',function(e){
            var $t = $(this);
            var id_option = $t.data('id_option');
            $.ajax({
                      type: "POST",
                      url: "ajax_rd4/help/_act.php",
                      dataType: 'json',
                      data: {act: 'elimina_help', id_option : id_option},
                      context: document.body
                    }).done(function(data) {
                        if(data.result=="OK"){
                            ok(data.msg);
                            $t.parents('div .help_item').fadeOut();
                        }else{
                            ko(data.msg);
                        }
                    });


        });


    }
    // end pagefunction

    pagefunction();

</script>
