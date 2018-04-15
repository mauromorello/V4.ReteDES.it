<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.user.php");

$ui = new SmartUI;
$page_title = "Gestione notifiche";
$page_id    = 'user_notifiche';


$U = new user(_USER_ID);
$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>false,
                    "deletebutton"=>false,
                    "colorbutton"=>true);



$a='<form action="" class="smart-form">
                            <section>
                                    <label class="toggle margin-top-10">
                                            <input id="cb_avvisi_aperture" type="checkbox" name="avvisi_aperture" '.$avvisi_aperture.'>
                                            <i data-swchon-text="SI" data-swchoff-text="NO"></i>
                                            Avvisami alle aperture degli ordini<br>
                                            <div class="note">Se selezioni "No" non riceverai la mail che ti avvisa dell\'apertura di un ordine</div>
                                    </label>

                                    <hr>

                                    <span class="toggle margin-top-10">Avvisi alle chiusure degli ordini</span>
                                    <label class="select">
                                        <select id="cb_alert_days">
                                            <option value="0" '.$zero.'>Nessun avviso</option>
                                            <option value="1" '.$uno.'>Un giorno prima</option>
                                            <option value="2" '.$due.'>Due giorni prima</option>
                                            <option value="3" '.$tre.'>Tre giorni prima</option>

                                        </select>
                                         <i></i>
                                    </label>
                                    <hr>


                                </section>


</form>';




//Telegram
$link_telegram = $U->te_connected;
if($link_telegram==''){
    $link_telegram = 'RETEDES_'.random_string();
    $stmt = $db->prepare("UPDATE maaking_users SET te_connected = :te_connected
                         WHERE userid='"._USER_ID."'");
    $stmt->bindParam(':te_connected', $link_telegram, PDO::PARAM_STR);
    $stmt->execute();
    $status = "Il tuo account non è collegato a telegram;";
    $button_disattiva = '';
    $button_attiva='<a id="collega_telegram" target="_BLANK" href="https://telegram.me/reteDESbot?start='.$link_telegram.'" class="btn btn-success">ATTIVA il bot telegram di ReteDES.it</a>';
}else{
    $status = "Il tuo account è già collegato a telegram e pronto all'uso;";
    $button_disattiva = '<a id="scollega_telegram" class="btn btn-danger">DISATTIVA il bot telegram di ReteDES.it</a>';
    $button_attiva='';
}


?>
<div class="inbox-nav-bar no-content-padding">
    <h1 class="page-title txt-color-blueDark"><i class="fa fa-fw fa-paper-plane-o"></i> Notifiche &nbsp;</h1>
</div>

<section id="widget-grid" class="margin-top-10">



    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
        </article>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well well-sm">
                <h1>Telegram <small><?php echo $status?></small></h1>
                <hr>
                <?php echo $button_attiva.$button_disattiva;?>
            </div>
        </div>

    </div>

</section>


<script type="text/javascript">

    pageSetUp();

    var pagefunction = function() {

        //------------HELP WIDGET
        <?php echo help_render_js($page_id);?>
        //------------END HELP WIDGET

        $('#scollega_telegram').click(function (e) {
            e.preventDefault();
            $.ajax({
              type: "POST",
              url: "ajax_rd4/user/_act.php",
              dataType: 'json',
              data: {act: "disattiva_telegram"},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                    okReload(data.msg);
                }else{
                    ko(data.msg);
                }
            });
        });
        $('#collega_telegram').click(function (e) {

            okReload("Ricarica la pagina");
        });
        $(document).on('change','.scelta_metodo_inserimento',function(){
            var scelta = $(this).val();
            $.ajax({
                      type: "POST",
                      url: "ajax_rd4/user/_act.php",
                      dataType: 'json',
                      data: {act: "scelta_metodo_inserimento", scelta:scelta},
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
    loadScript("js/plugin/jquery-form/jquery-form.min.js", pagefunction());



</script>