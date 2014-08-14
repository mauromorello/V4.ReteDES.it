<?php
require_once("inc/init.php");

$ui = new SmartUI;
$page_title = "mie Ditte";


$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>true,
                    "deletebutton"=>false,
                    "colorbutton"=>false);


$stmt = $db->prepare("SELECT * FROM retegas_ditte WHERE id_proponente='"._USER_ID."';");
$stmt->execute();
$rows = $stmt->fetchAll();

$li = '<ul class="list-group">';
foreach($rows as $row){

if($row["mail_ditte"]<>""){
    $mail='<i class=" glyphicon glyphicon-envelope text-success"></i>';
    $mail=$mail.'&nbsp;<a href="mailto:'.$row["mail_ditte"].'">'.$row["mail_ditte"].'</a>';
}else{
    $mail='';
}

if($row["ditte_gc_lat"]>0){
    $geo='<i class=" glyphicon glyphicon-map-marker text-success" rel="tooltip" data-original-title="GeoReferenziata"></i>';
    $geo = $geo.'&nbsp;'.$row["indirizzo"].'&nbsp;-&nbsp;';
}else{
    $geo='<i class=" glyphicon glyphicon-map-marker text-danger" rel="tooltip" data-original-title="NON GeoReferenziata"></i>';
}

if($row["telefono"]<>""){
    $tel='<i class=" glyphicon glyphicon-earphone text-success" ></i>';
    $tel= $tel.'&nbsp;<a href="tel:'.$row["telefono"].'">'.$row["telefono"].'</a>&nbsp;';
}else{
    $tel='';
}

if($row["tag_ditte"]<>""){
    $tag='<i class=" glyphicon glyphicon-tags text-success" rel="tooltip" data-original-title="'.$row["tag_ditte"].'"></i>';
}else{
    $tag='<i class=" glyphicon glyphicon-tags text-danger"></i>';
}

if($row["note_ditte"]<>""){
    $note='<i class=" glyphicon glyphicon-book text-success" rel="tooltip" data-original-title="Nessuna note inserita"></i>';
}else{
    $note='<i class=" glyphicon glyphicon-book text-danger"></i>';
}

if($row["website"]<>""){
    $web='<i class=" glyphicon glyphicon-link text-success" rel="tooltip" data-original-title="'.$row["website"].'"></i>';
    $web= $web.'&nbsp;<a href="'.$row["website"].'" target="_blank">'.$row["website"].'</a>';
}else{
    $web='';
}

    $li.= '<li class="list-group-item">
            <span class="pull-right">'.$note.'<br>'.$tag.'</span>
            <strong class="font-md ditta_selector" style="cursor:pointer" rel="'.$row["id_ditte"].'">'.$row["descrizione_ditte"].'</strong><br>'
            .$tel.$web.'<br>'
            .'<small class="font-sm">'.$geo.$mail.'</small>
            </li>';
}

$li .="</ul>";



$wg_miei_forn = $ui->create_widget($options);
$wg_miei_forn->id = "wg_fornitori_miei";
$wg_miei_forn->body = array("content" => $li ,"class" => "no-padding");
$wg_miei_forn->header = array(
    "title" => '<h2>Fornitori inseriti da me</h2>',
    "icon" => 'fa fa-truck'
);

$c='<div class="">
                <div class="alert alert-warning text-center">
                    <h4><i class="fa fa-truck fa-2x"></i>&nbsp;Clicca sul nome di una ditta per vedere qua i dettagli</h4>
                </div>
    </div>';

$wg_dett_forn = $ui->create_widget($options);
$wg_dett_forn->id = "wg_fornitori_miei_dettaglio";
$wg_dett_forn->body = array("content" => $c ,"class" => "");
$wg_dett_forn->header = array(
    "title" => '<h2>Dettaglio fornitore</h2>',
    "icon" => 'fa fa-pencil'
);

?>
<div class="inbox-nav-bar no-content-padding">
    <h1 class="page-title txt-color-blueDark"><i class="fa fa-fw fa-unlock"></i> Admin &nbsp;</h1>
</div>

<section id="widget-grid" class="margin-top-10">

    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <?php echo help_render_html("mie_ditte",$page_title); ?>
            <?php echo $wg_dett_forn->print_html(); ?>
        </article>
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <?php echo $wg_miei_forn->print_html(); ?>
        </article>

    </div>

</section>


<script type="text/javascript">

    pageSetUp();

    var pagefunction = function() {
        //-------------------------HELP
        <?php echo help_render_js("mie_ditte"); ?>
        //-------------------------HELP

        $('.ditta_selector').on('click', function(e){
            var id_ditta = $(this).attr('rel');
            $.ajax({
              type: "POST",
              url: "ajax_rd4/fornitori/inc/scheda_fornitore.php",
              dataType: 'json',
              data: {id_ditta : id_ditta},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                    ok(data.msg);
                }else{
                    ko(data.msg);
                }
            });


        });

    };

    // end pagefunction

    // run pagefunction on load
    pagefunction();

</script>
