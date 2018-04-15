<?php
require_once("inc/init.php");

$ui = new SmartUI;
$page_title= "Cose(in)utili: Oggetti";
$page_id="coseinutili_oggetti";

$geo = _USER_GAS_LAT.","._USER_GAS_LNG;
$offset = CAST_TO_INT($_GET["offset"],1);
$sort= CAST_TO_INT($_GET["sort"],1);
if($sort==1){$sort_vicini=' checked="checked" ';}
if($sort==2){$sort_nuovi=' checked="checked" ';}
if($sort==3){$sort_interessanti=' checked="checked" ';}

$lista_oggetti = file_get_contents('http://www.coseinutili.it/php/searchv4.php?geo='.$geo.'&offset='.$offset."&sort=".$sort);
$listona = json_decode($lista_oggetti);

foreach ($listona as $i => $values) {
    $i++;

    $titolo = $values->Annuncio;
    //$foto = "http://www.coseinutili.it/public/annunci/".$values->id."_1_200.jpg";
    $foto = "http://www.coseinutili.it/public/annunci/".substr(str_pad($values->id, 8, "0", STR_PAD_LEFT),0,5) . "/".$values->id."_1_200.jpg";

    $descrizione = myTruncate($values->descrizione,30);
    $raccontaci = myTruncate($values->raccontaci,60);
    //$descrizione = $values->descrizione;
    //$raccontaci =  $values->raccontaci;
    $crediti = $values->crediti;
    $comune = $values->Comune." (".$values->targa.")";
    $lat = $values->user_lat;
    $lng = $values->user_lng;
    $id_annuncio= $values->id;
    $fullname = $values->nome." ".$values->cognome;
    $km = $values->km;
    $id_utente = $values->utente_id;
    $username = $values->username;
    $u_img = $values->user_img;
    $consegna = $values->consegna;
    $cat_padre = $values->categoriaPadre;
    $cat = "<br>".$values->Categoria;
    $Sutente = $values->ScambiUtente;


    $pos = strpos($consegna, "1");
    if ($pos !== false) {
        $mano='<i class="fa fa-hand-paper-o" title="consegna a mano"></i>';
    } else {
        $mano="";
    }

    $pos1 = strpos($consegna, "3");
    if ($pos1 !== false) {
        $poste='<i class="fa fa-gift" title="consegna tramite spedizione postale"></i>';
    } else {
        $poste="";
    }

    $pos2 = strpos($consegna, "2");
    if ($pos2 !== false) {
        $corriere='<i class="fa fa-truck" title="consegna tramite corriere"></i>';
    } else {
        $corriere="";
    }

    if($u_img=="true"){
        $src='http://www.coseinutili.it/public/utenti/'.$id_utente.'_60.jpg';
    }else{
        $src='http://www.coseinutili.it/public/utenti/0_60.jpg';
    }

    $su = '<i class="fa fa-refresh hidden-xs"></i> <small class="font-xs hidden-xs">'.$Sutente.'</small>';
    $cr = '<small class="font-xs visible-xs"><strong>'.$crediti.'</strong> crediti</small>';
    $au = '<i class="fa fa-gift"></i> <small class="font-xs">'.$Autente.'</small>';
    $fu = '<i class="fa fa-gift"></i> <small class="font-xs">'.$Futente.'</small>';

    $full[$i]='<div class="grid-item" style="overflow:hidden;">
                    <div class="thumbnail" style="box-shadow:2px 2px 3px 0px rgba(50, 50, 50, 0.25);">
                        <a href="http://www.coseinutili.it/annuncio/?id='.$id_annuncio.'" target="_BLANK">
                            <img src="'.$foto.'" class="img img-responsive" style="">
                            <div class="caption text-grey" >
                                <p class="visible-xs text-xs" href="javascript:void(0);" title="'.$descrizione.'">'.$titolo.'</p>
                                <p class="hidden-xs"><small><strong>'.$titolo.': </strong><br><span style="text-align: justify;">'.$descrizione.'</span></small></p>
                            </div>
                        </a>
                        <small class="font-xs hidden-xs" >
                                <a href="#">'.$cat_padre.'</a> <a href="#">'.$cat.'</a>
                        </small>
                        <div class="clearfix"></div>
                        <div class="text-muted font-xs" style="display:block; float:right; margin:0;">'.$comune.'</div>
                        <hr>

                        <div  style="float: left; ; width:100%; margin-top:-18px;">
                            <div  style="height:50px; float: left; background-color:#FFF;width:75%; text-align:left; overflow:hidden;">
                                <IMG SRC="'.$src.'" class="img img-circle hidden-xs" style="width:36px;margin-top:10px; float:left; display:inline-block;">
                                <div class="font-xs hidden-xs" style="margin-top:12px; margin-left:6px;overflow:hidden; display:inline-block;">
                                    <span><strong>'.$crediti.'</strong> crediti</span><br>
                                    <span class="text-muted">di '.$username.'</span>
                                </div>
                                <span class="text-muted visible-xs">di '.$username.'</span>
                            </div>
                            <div  style="float: right; margin-top:4px;">
                                '.$su.$cr.'
                            </div>
                        </div>

                        <div class="clearfix"></div>
                    </div>

                  </div>';


}
 //se è il feed
   if($offset>1){
       for($i=0;$i<16;$i++){
           echo $full[$i];
       }
       die();
   }
?>
<div class="well well-sm text-center">
    <a href="http://www.coseinutili.it" target="_BLANK"><img class="center-block margin-top-10" SRC="http://www.coseinutili.it/images/common/logo2.png"></a>
    <p class="margin-top-5">Per tutte le informazioni consulta l'help in fondo a questa pagina.</p>
    <p></p>

</div>
<p><form action="" class="smart-form">
    <section>
        <label class="label">Mostra gli annunci:</label>
        <div class="inline-group">
            <label class="radio">
                <input name="radio-inline" onclick="window.location.href = '<?php echo APP_URL; ?>/#ajax_rd4/ci/oggetti.php?sort=1';" <? echo $sort_vicini;?>  type="radio">
                <i></i>Più vicini al tuo GAS</label>
            <label class="radio">
                <input name="radio-inline" onclick="window.location.href = '<?php echo APP_URL; ?>/#ajax_rd4/ci/oggetti.php?sort=2';" <? echo $sort_nuovi;?> type="radio">
                <i></i>Più recenti</label>
            <label class="radio">
                <input name="radio-inline" onclick="window.location.href = '<?php echo APP_URL; ?>/#ajax_rd4/ci/oggetti.php?sort=3';" <? echo $sort_interessanti;?> type="radio">
                <i></i>Più interessanti</label>  
        </div>

    </section>
    </form></p>

<div id="ci_oggetti_container" >
    <div class="grid-sizer"></div>
    <?php for($i=0;$i<16;$i++){echo $full[$i];};?>
</div>
<div class="well well-large text-center font-xl">
      <a href="javascript:void(0);" id="more_items"><i class="fa fa-plus-square"></i>  Carica altri oggetti</a>
</div>

<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
        </article>
    </div>
</section>
<script type="text/javascript">
    pageSetUp();
    var offset=2;
    var geo="<?php echo $geo; ?>";
    var sort="<?php echo $sort; ?>";
    var pagefunction = function() {

        //-------------------------HELP
        <?php echo help_render_js($page_id); ?>
        //-------------------------HELP

        var rtime;
        var timeout = false;
        var delta = 200;
        $(window).resize(function() {
            rtime = new Date();
            if (timeout === false) {
                timeout = true;
                setTimeout(resizeend, delta);
            }
        });

        function resizeend() {
            if (new Date() - rtime < delta) {
                setTimeout(resizeend, delta);
            } else {
                timeout = false;
                $container.imagesLoaded( function() {
                                $container.masonry('layout');
                });
            }
        }

       var $container = $('#ci_oggetti_container').masonry({
                          // options
                          itemSelector: '.grid-item',
                          columnWidth: '.grid-sizer',
                          isFitWidth: true,
                          percentPosition: true,
                          gutter:10
                        });

       $('#more_items').on('click', function(){
           $.ajax({
                    url: '/gas4/ajax_rd4/ci/oggetti.php',
                    type: 'GET',
                    data: { offset: offset, geo:geo, sort:sort},
                    success: function(html){
                        if (html.length > 0) {
                            var $el = jQuery(html);
                            $container.append($el).masonry( 'appended', $el );

                            $container.imagesLoaded( function() {
                                $container.masonry('layout');
                            });
                            offset = parseInt(offset) + 1;
                        }

                    }
                });

       });

       $container.imagesLoaded( function() {
                                $container.masonry('layout');
       });


    }
    // end pagefunction

    loadScript("js_rd4/plugin/Masonry/imageloaded.js",
        loadScript("js/plugin/jquery-form/jquery-form.min.js",
            loadScript("js_rd4/plugin/Masonry/masonry.js", pagefunction)
        )
    );

</script>
