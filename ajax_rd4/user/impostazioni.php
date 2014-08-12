<?php
require_once("inc/init.php");

$ui = new SmartUI;
$page_title = "Impostazioni sito";

$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>false,
                    "deletebutton"=>false,
                    "colorbutton"=>true);


if(_USER_GAS_USA_CASSA) {
    if (_USER_USA_CASSA){
        $usa_cassa = 'checked="checked" disabled="disabled"';
    }else{
        $usa_cassa = '';
    }

    $gas_usa_cassa ='<label class="toggle margin-top-10">
                                            <input id="cb_user_usa_cassa" type="checkbox" name="usa_cassa" '.$usa_cassa.'>
                                            <i data-swchon-text="SI" data-swchoff-text="NO"></i>
                                            Usi la cassa ?
                                            <div class="note">Prima di cliccare, accertati di aver capito bene il suo funzionamento leggendo <a href="#">qui</a>; Se hai la cassa attiva la puoi disattivare contattando il tuo cassiere.</div>
                                        </label>
                                        <hr>';


}



if (_USER_PERMETTI_MODIFICA){
    $permetti = 'checked="checked"';
}else{
    $permetti = '';
}

if(_USER_ALERT_DAYS==0){$zero="SELECTED";}
if(_USER_ALERT_DAYS==1){$uno="SELECTED";}
if(_USER_ALERT_DAYS==2){$due="SELECTED";}
if(_USER_ALERT_DAYS==3){$tre="SELECTED";}

$i='<form action="" class="smart-form">
                            <section>

                                        <label class="toggle margin-top-10">
                                            <input id="cb_permetti_modifica" type="checkbox" name="permetti_modifica" '.$permetti.'>
                                            <i data-swchon-text="SI" data-swchoff-text="NO"></i>
                                            Permetti modifiche ai tuoi ordini<br>
                                            <div class="note">Chi gestisce gli ordini o le persone abilitate a controllare tutti gli ordini possono modificare quello che tu hai acquistato, anche aggiungendo o togliendo alcuni articoli.</div>
                                        </label>
                                        <hr>
                                        '.$gas_usa_cassa.'
                                    <label class="label margin-top-10">Avvisi alle chiusure degli ordini</label>
                                    <label class="select">
                                        <select id="cb_alert_days">
                                            <option value="0" '.$zero.'>Nessun avviso</option>
                                            <option value="1" '.$uno.'>Un giorno prima</option>
                                            <option value="2" '.$due.'>Due giorni prima</option>
                                            <option value="3" '.$tre.'>Tre giorni prima</option>

                                        </select>
                                         <i></i>
                                    </label>
                                </section>

                            </section>
</form>';

$wg_impostazioni = $ui->create_widget($options);
$wg_impostazioni->id = "wg_impostazioni";
$wg_impostazioni->body = array("content" => $i);
$wg_impostazioni->header = array(
    "title" => '<h2>Sito</h2>',
    "icon" => 'fa fa-laptop'
);

//CASSA
$wg_cassa = $ui->create_widget($options);
$wg_cassa->body = array("content" => 'Per ora non ci sono opzioni disponibili');
$wg_cassa->id = "wg_cassa";
$wg_cassa->header = array(
    "title" => '<h2>Cassa</h2>',
    "icon" => 'fa fa-dollar'
);

//VISUAL
$v = '
        <form class="smart-form">
            <legend class="no-padding margin-bottom-10">Impostazioni di visualizzazione</legend>
            <legend class="note no-padding margin-bottom-10">Regola queste impostazioni a seconda del dispositivo che stai usando.</legend>
            <div class="row">
            <section class="col col-6">
                <label>
                    <input name="subscription" id="smart-fixed-header" type="checkbox" class="checkbox style-0">
                        <span>Intestazione fissa</span>
                </label><br>
                <label>
                    <input type="checkbox" name="terms" id="smart-fixed-navigation" class="checkbox style-0">
                        <span>Navigazione fissa</span>
                </label><br>
                <label>
                    <input type="checkbox" name="terms" id="smart-fixed-ribbon" class="checkbox style-0">
                        <span>Barra info fissa</span>
                </label><br>
                <label>
                    <input type="checkbox" name="terms" id="smart-fixed-footer" class="checkbox style-0">
                    <span>Piè di pagina fisso</span>
                </label>
            </section>
            <section class="col col-6">

                <label style="display:block;">
                    <input type="checkbox" id="smart-topmenu" class="checkbox style-0">
                    <span>Menu <b>sopra</b></span>
                </label>
                <label>
                    <input type="checkbox" name="terms" id="smart-fixed-container" class="checkbox style-0">
                    <span>Larghezza fissa</span>

                </label>
                <span id="smart-bgimages"></span>
           </section>
           </div>


            <div class="row">
                <section class="col col-6">
                <h6 class="margin-top-10 semi-bold margin-bottom-5">Qualcosa non funziona ?</h6>
                    <a href="javascript:void(0);" class="btn btn-xs btn-block btn-primary" id="reset-smart-widget">
                        <i class="fa fa-refresh"></i> Fai un reset !</a>
                </section>


                <section id="smart-styles" class="col col-6">
                    <h6 class="margin-top-10 semi-bold margin-bottom-5">Temi ReteDES 4.0</h6>
                    <a href="javascript:void(0);" id="smart-style-0" data-skinlogo="img/logo_rd4.png" class="btn btn-block btn-xs txt-color-white margin-right-5" style="background-color:#4E463F;"><i class="fa fa-check fa-fw" id="skin-checked"></i>Scuro</a>
                    <!--<a href="javascript:void(0);" id="smart-style-1" data-skinlogo="img/logo_rd4.png" class="btn btn-block btn-xs txt-color-white" style="background:#3A4558;">Dark Elegance</a>-->
                    <a href="javascript:void(0);" id="smart-style-2" data-skinlogo="img/logo_rd4.png" class="btn btn-xs btn-block txt-color-darken margin-top-5" style="background:#fff;">Chiaro</a>
                    <!--<a href="javascript:void(0);" id="smart-style-3" data-skinlogo="img/logo_rd4.png" class="btn btn-xs btn-block txt-color-white margin-top-5" style="background:#f78c40">Google Skin</a>-->
                    </section>
             </div>
                </form>

                ';
$wg_visual = $ui->create_widget($options);
$wg_visual->body = array("content" => $v);
$wg_visual->id = "wg_visual";
$wg_visual->header = array(
    "title" => '<h2>Visualizzazione</h2>',
    "icon" => 'fa fa-eye'
);




// GESTIONE DEI PERMESSI ----------------------------------------------------
$user_permission = _USER_PERMISSIONS;
//permessi
function r($status,$title,$pop=null){return $status ? '<span rel="popover-hover" data-placement="top" data-original-title="'.$title.'" data-content="'.$pop.'"><i class="fa fa-check txt-color-green"></i> '.$title.' </span>' : '<span class="txt-color-blueLight"><i class="fa fa-times txt-color-red"></i> '.$title.' </span';}

if(_USER_PUO_MODIFICARE_HELP){
    $user_modifica_help = '<span><i class="fa fa-check txt-color-green"></i> Può modificare gli help </span>';
}else{
    $user_modifica_help = '<span><i class="fa fa-times txt-color-red"></i> Può modificare gli help </span>';
}

$p= '
<dl class="dl-horizontal">
    <dt>Permessi standard</dt>
    <dd>
    '.r($user_permission & perm::puo_partecipare_ordini,"Partecipare agli ordini","Senza questo permesso non si può partecipare agli ordini acquistando merce.").'
    </dd>
    <dd>
    '.r($user_permission & perm::puo_creare_ordini,"Inserire nuovi ordini","Con questo permesso l'utente è abilitato ad inserire nuovi ordini.").'
    </dd>
    <dd>
    '.r($user_permission & perm::puo_avere_amici,"Gestire la rubrica amici","Una rubrica che permette di dividere gli ordini fatti tra la propria cerchia di amici.").'
    </dd>
    <dd>
    '.r($user_permission & perm::puo_creare_ditte,"Inserire nuove ditte").'
    </dd>
    <dd>
    '.r($user_permission & perm::puo_creare_listini,"Inserire i listini","La croce e delizia di ReteDES.it").'
    </dd>
    <dd>
    '.r($user_permission & perm::puo_postare_messaggi,"Inserire commenti e opinioni","Quando si partecipa ad un ordine è utile avere un feedback, sia sulla merce che sul fornitore").'
    </dd>
    <dd>
    '.r($user_permission & perm::puo_operare_con_crediti,"Operare sul credito di altri utenti (se è attiva la cassa)","Questo permesso abilita l'utente a scaricare il credito ad ordine convalidato, senza aspettare il cassiere").'
    </dd>
    <dt>Permessi gestionali GAS</dt>
    <dd>
    '.r($user_permission & perm::puo_vedere_tutti_ordini,"Supervisionare gli ordini").'
    </dd>
    <dd>
    '.r($user_permission & perm::puo_gestire_utenti,"Gestire gli utenti").'
    </dd>
    <dd>
    '.r($user_permission & perm::puo_mod_perm_user_gas,"Gestire i permessi e le abilitazioni").'
    </dd>
    <dd>
    '.r($user_permission & perm::puo_creare_gas,"Gestire le anagrafiche del proprio GAS").'
    </dd>
    <dd>
    '.r($user_permission & perm::puo_gestire_la_cassa,"Gestire la cassa (se attiva)").'
    </dd>
    <dt>Permessi amministrativi</dt>
    <dd>
    '.$user_modifica_help.'
    </dd>
    <dd>
    '.r($user_permission & perm::puo_eliminare_messaggi,"Moderare i commenti").'
    </dd>
    <dd>
    '.r($user_permission & perm::puo_vedere_retegas,"Gestire il proprio DES").'
    </dd>


</dl>';

$wg_permessi = $ui->create_widget($options);
$wg_permessi->body = array("content" => $p);
$wg_permessi->id = "wg_permessi";
$wg_permessi->header = array(
    "title" => '<h2>Permessi</h2>',
    "icon" => 'fa fa-lock'
);


?>
<div class="inbox-nav-bar no-content-padding">
    <h1 class="page-title txt-color-blueDark"><i class="fa fa-fw fa-gear"></i> Impostazioni &nbsp;</h1>
</div>

<section id="widget-grid" class="margin-top-10">



    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <?php echo help_render_html("user_impostazioni",$page_title); ?>
            <?php echo $wg_visual->print_html(); ?>
            <?php echo $wg_permessi->print_html(); ?>
        </article>
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <?php echo $wg_cassa->print_html(); ?>
            <?php echo $wg_impostazioni->print_html(); ?>
        </article>

    </div>

</section>


<script type="text/javascript">

    pageSetUp();

    //-------------------------HELP
    <?php echo help_render_js("user_impostazioni"); ?>
    //-------------------------HELP

    // hide bg options
    var smartbgimage = "<h6 class='margin-top-10 semi-bold'>Background</h6><img src='img/pattern/graphy-xs.png' data-htmlbg-url='img/pattern/graphy.png' width='22' height='22' class='margin-right-5 bordered cursor-pointer'><img src='img/pattern/tileable_wood_texture-xs.png' width='22' height='22' data-htmlbg-url='img/pattern/tileable_wood_texture.png' class='margin-right-5 bordered cursor-pointer'><img src='img/pattern/sneaker_mesh_fabric-xs.png' width='22' height='22' data-htmlbg-url='img/pattern/sneaker_mesh_fabric.png' class='margin-right-5 bordered cursor-pointer'><img src='img/pattern/nistri-xs.png' data-htmlbg-url='img/pattern/nistri.png' width='22' height='22' class='margin-right-5 bordered cursor-pointer'><img src='img/pattern/paper-xs.png' data-htmlbg-url='img/pattern/paper.png' width='22' height='22' class='bordered cursor-pointer'>";
    $("#smart-bgimages")
        .fadeOut();
    /*
    * FIXED HEADER
    */
    $('input[type="checkbox"]#smart-fixed-header')
    .click(function () {
        if ($(this)
            .is(':checked')) {
            //checked
            $.root_.addClass("fixed-header");
        } else {
            //unchecked
            $('input[type="checkbox"]#smart-fixed-ribbon')
                .prop('checked', false);
            $('input[type="checkbox"]#smart-fixed-navigation')
                .prop('checked', false);

            $.root_.removeClass("fixed-header");
            $.root_.removeClass("fixed-navigation");
            $.root_.removeClass("fixed-ribbon");

        }
    });

    /*
     * FIXED NAV
     */
    $('input[type="checkbox"]#smart-fixed-navigation')
        .click(function () {
            if ($(this)
                .is(':checked')) {
                //checked
                $('input[type="checkbox"]#smart-fixed-header')
                    .prop('checked', true);

                $.root_.addClass("fixed-header");
                $.root_.addClass("fixed-navigation");

                $('input[type="checkbox"]#smart-fixed-container')
                    .prop('checked', false);
                $.root_.removeClass("container");

            } else {
                //unchecked
                $('input[type="checkbox"]#smart-fixed-ribbon')
                    .prop('checked', false);
                $.root_.removeClass("fixed-navigation");
                $.root_.removeClass("fixed-ribbon");
            }
        });

    /*
 * FIXED RIBBON
 */
$('input[type="checkbox"]#smart-fixed-ribbon')
    .click(function () {
        if ($(this)
            .is(':checked')) {

            //checked
            $('input[type="checkbox"]#smart-fixed-header')
                .prop('checked', true);
            $('input[type="checkbox"]#smart-fixed-navigation')
                .prop('checked', true);
            $('input[type="checkbox"]#smart-fixed-ribbon')
                .prop('checked', true);

            //apply
            $.root_.addClass("fixed-header");
            $.root_.addClass("fixed-navigation");
            $.root_.addClass("fixed-ribbon");

            $('input[type="checkbox"]#smart-fixed-container')
                .prop('checked', false);
            $.root_.removeClass("container");

        } else {
            //unchecked
            $.root_.removeClass("fixed-ribbon");
        }
    });

    /*
 * RTL SUPPORT
 */
$('input[type="checkbox"]#smart-fixed-footer')
    .click(function () {
        if ($(this)
            .is(':checked')) {

            //checked
            $.root_.addClass("fixed-page-footer");

        } else {
            //unchecked
            $.root_.removeClass("fixed-page-footer");
        }
    });

    /*
 * MENU ON TOP
 */

$('#smart-topmenu')
    .on('change', function (e) {
        if ($(this)
            .prop('checked')) {
            //window.location.href = '?menu=top';
            localStorage.setItem('sm-setmenu', 'top');
            location.reload();
        } else {
            //window.location.href = '?';
            localStorage.setItem('sm-setmenu', 'left');
            location.reload();
        }
    });
    if (localStorage.getItem('sm-setmenu') == 'top') {
    $('#smart-topmenu')
        .prop('checked', true);
} else {
    $('#smart-topmenu')
        .prop('checked', false);
}

 /*
 * INSIDE CONTAINER
 */
if ($.root_.hasClass("container")){
    $('input[type="checkbox"]#smart-fixed-container')
                    .prop('checked', true);
    if (smartbgimage) {
                $("#smart-bgimages")
                    .append(smartbgimage)
                    .fadeIn(1000);
                $("#smart-bgimages img")
                    .bind("click", function () {
                        var $this = $(this);
                        var $html = $('html')
                        bgurl = ($this.data("htmlbg-url"));
                        $html.css("background-image", "url(" + bgurl + ")");
                        localStorage.setItem('sm-background', bgurl);
                    })
                smartbgimage = null;
            } else {
                $("#smart-bgimages")
                    .fadeIn(1000);

            }
}

$('input[type="checkbox"]#smart-fixed-container')
    .click(function () {
        if ($(this)
            .is(':checked')) {
            //checked
            $.root_.addClass("container");

            localStorage.setItem('sm-container', "TRUE");

            $('input[type="checkbox"]#smart-fixed-ribbon')
                .prop('checked', false);
            $.root_.removeClass("fixed-ribbon");

            $('input[type="checkbox"]#smart-fixed-navigation')
                .prop('checked', false);
            $.root_.removeClass("fixed-navigation");

            if (smartbgimage) {
                $("#smart-bgimages")
                    .append(smartbgimage)
                    .fadeIn(1000);
                $("#smart-bgimages img")
                    .bind("click", function () {
                        var $this = $(this);
                        var $html = $('html')
                        bgurl = ($this.data("htmlbg-url"));
                        $html.css("background-image", "url(" + bgurl + ")");
                        localStorage.setItem('sm-background', bgurl);
                    })
                smartbgimage = null;
            } else {
                $("#smart-bgimages")
                    .fadeIn(1000);

            }

        } else {
            //unchecked
            $.root_.removeClass("container");
            localStorage.setItem('sm-container', "FALSE");
            $("#smart-bgimages")
                .fadeOut();
            $("html").css("background-image", '');
        }
    });

 /*
 * REFRESH WIDGET
 */
$("#reset-smart-widget")
    .bind("click", function () {
        $('#refresh')
            .click();
        return false;
    });

    /*
 * STYLES
 */
$("#smart-styles > a")
   .on('click', function() {
        var $this = $(this);
        var $logo = $("#logo img");
        $.root_.removeClassPrefix('smart-style')
            .addClass($this.attr("id"));
        $logo.attr('src', $this.data("skinlogo"));
        $("#smart-styles > a #skin-checked")
            .remove();
        $this.prepend("<i class='fa fa-check fa-fw' id='skin-checked'></i>");
    });

    $('#cb_permetti_modifica').change(function () {
        if(this.checked) {value = "SI";}else{value = "NO";}
        $.ajax({
          type: "POST",
          url: "ajax_rd4/user/_act.php",
          dataType: 'json',
          data: {act: "permetti_modifica", value : value},
          context: document.body
        }).done(function(data) {
            if(data.result=="OK"){
                    ok(data.msg);}else{ko(data.msg);}
        });


    });

    $('#cb_alert_days').change(function () {
        value = this.value;

        $.ajax({
          type: "POST",
          url: "ajax_rd4/user/_act.php",
          dataType: 'json',
          data: {act: "user_alert_days", value : value},
          context: document.body
        }).done(function(data) {
            if(data.result=="OK"){
                    ok(data.msg);}else{ko(data.msg);}
        });


    });

    $("#cb_user_usa_cassa").change(function(e) {
            if(this.checked) {value = "SI";}else{value = "NO";}
            $.SmartMessageBox({
                title : "Attenzione !",
                content : 'Se attivi la cassa, confermi di accettare quanto riportato <a href="https://sites.google.com/site/retegasapwiki/regole-e-disclaimer/" target="_BLANK">qui</a>',
                buttons : '[No][Si]'
            }, function(ButtonPressed) {
                if (ButtonPressed === "Si") {


                    console.log("Value: " + value);
                    $.ajax({
                      type: "POST",
                      url: "ajax_rd4/user/_act.php",
                      dataType: 'json',
                      data: {act: "user_usa_cassa", value : value},
                      context: document.body
                    }).done(function(data) {
                        if(data.result=="OK"){
                                ok(data.msg);
                                $('#cb_user_usa_cassa').prop('disabled', true);
                        }else{ko(data.msg);}

                    });

                }
                if (ButtonPressed === "No") {
                    $.smallBox({
                        title : "ReteDES.it",
                        content : "<i class='fa fa-clock-o'></i> <i>Operazione annullata</i>",
                        color : "#C46A69",
                        iconSmall : "fa fa-times fa-2x fadeInRight animated",
                        timeout : 4000
                    });
                    $('#cb_user_usa_cassa').prop('checked', false);
                }

            });
            e.preventDefault();
        })


</script>