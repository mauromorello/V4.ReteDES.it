<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.user.php");

$ui = new SmartUI;
$page_title = "Impostazioni sito";
$page_id    = 'user_impostazioni';

$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>false,
                    "deletebutton"=>false,
                    "colorbutton"=>true);
$U = new user(_USER_ID);

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
                        <div class="note margin-top-5">Prima di cliccare, accertati di aver capito bene il suo funzionamento leggendo <a href="#ajax_rd4/help/help_cassa.php">qui</a>; Se hai la cassa attiva la puoi disattivare contattando il tuo cassiere.</div>
                     </label>
                     <hr>
                     <span class="toggle margin-top-10">IBAN</span>
                        <label class="input">
                            <label class="input note" for="_USER_IBAN">
                                  <input id="_USER_IBAN" name="_USER_IBAN" value="'._USER_IBAN.'">
                                  <p class="note margin-top-5">Se il tuo GAS usa il sistema SDD inserisci qua il tuo IBAN senza spazi.</p>
                            </label>
                        </label>
                     <hr>
                     <span class="toggle margin-top-10">ID Mandato</span>
                        <label class="input">
                            <label class="input note" for="_USER_MANDATE_ID">
                                  <input id="_USER_MANDATE_ID" name="_USER_MANDATE_ID" value="'._USER_MANDATE_ID.'">
                                  <p class="note margin-top-5">Il codice di mandato rilasciato dalla tua banca a fronte della richiesta di SDD.</p>
                            </label>
                        </label>                                  
                        <hr>
                    <span class="toggle margin-top-10">Data Mandato</span>
                    <label class="input">
                        <label class="input note" for="_USER_MANDATE_DATE">
                              <input id="_USER_MANDATE_DATE" name="_USER_MANDATE_DATE" data-mask="99/99/9999" value="'._USER_MANDATE_DATE.'">
                              <p class="note margin-top-5">La data del mandato, in formato GG/MM/AAAA.</p>
                        </label>
                    </label>                                  
                    <hr>';


}else{
   $gas_usa_cassa ='<label class="toggle margin-top-10">
                                            <input id="cb_user_usa_cassa" type="checkbox" name="usa_cassa" disabled="disabled">
                                            <i data-swchon-text="SI" data-swchoff-text="NO"></i>
                                            Usi la cassa ?
                                            <div class="note">Prima di cliccare, accertati di aver capito bene il suo funzionamento leggendo <a href="#ajax_rd4/help/help_cassa.php">qui</a>; Se hai la cassa attiva la puoi disattivare contattando il tuo cassiere.</div>
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

if(_USER_CARATTERE_DECIMALE==","){
    $decimale_virgola = ' checked = "CHECKED" ';
    $decimale_punto = '';
}else{
    $decimale_punto = ' checked = "CHECKED" ';
    $decimale_virgola = '';
}
if(_USER_INSERIMENTO_TEXTBOX){
    $metodo_textbox = ' checked = "CHECKED" ';
    $metodo_pulsante = '';
}else{
    $metodo_pulsante = ' checked = "CHECKED" ';
    $metodo_textbox = '';
}
if(_USER_HAS_TRIGGERS){
    $abilita_triggers = ' checked = "CHECKED" ';
}else{
    $abilita_triggers = '';
}
if(_USER_ADDTOCALENDAR){
    $atc_si = ' checked = "CHECKED" ';
    $atc_no = '';
}else{
    $atc_no = ' checked = "CHECKED" ';
    $atc_si = '';
}


$i='<form action="" class="smart-form">
                            <section>
                                    <span class="toggle margin-top-10">Carattere delimitatore di campo CSV</span>
                                    <label class="input">
                                        <label class="input note" for="csv_separator">
                                              <input id="csv_separator" name="csv_separator" value="'._USER_CSV_SEPARATOR.'">
                                              Scegli il carattere che vuoi, se non metti nulla il carattere predefinito è la virgola
                                        </label>
                                    </label>
                                    <hr>

                                    <span class="toggle margin-top-10">Carattere delimitatore di decimali</span>

                                    <form class="smart-form">
                                        <section class="font-xs">
                                            <div class="inline-group">
                                                <label class="radio">
                                                    <input type="radio" name="scelta_decimale" '.$decimale_virgola.' value="virgola" class="scelta_decimale">
                                                    <i></i>Virgola ","</label>
                                                <label class="radio">
                                                    <input type="radio" name="scelta_decimale" '.$decimale_punto.' value="punto" class="scelta_decimale">
                                                    <i></i>Punto "."</label>
                                            </div>
                                        </section>
                                        <span class="note">Dove è previsto questo sarà il carattere per distinguere i decimali, utile per gestire le esportazioni a seconda del sistema usato.</span>
                                    </form>

                                    <hr>

                                    <span class="toggle margin-top-10">Metodo inserimento ordini</span>

                                    <form class="smart-form">
                                        <section class="font-xs">
                                            <div class="inline-group">
                                                <label class="radio">
                                                    <input type="radio" name="scelta_metodo" '.$metodo_pulsante.' value="pulsante" class="scelta_metodo_inserimento">
                                                    <i></i>Pulsante "+"</label>
                                                <label class="radio">
                                                    <input type="radio" name="scelta_metodo" '.$metodo_textbox.' value="textbox" class="scelta_metodo_inserimento">
                                                    <i></i>Valori numerici</label>
                                            </div>
                                        </section>
                                        <span class="note">Quando entri nella pagina per acquistare la merce troverai il metodo di inserimento scelto qua. (si può anche cambiare all\'interno della pagina)</span>
                                    </form>
                                    
                                    
                                    <hr>

                                    <form class="smart-form">
                                        <section class="font-xs">
                                            <label class="toggle margin-top-10 disabled">
                                                <input id="abilita_triggers" type="checkbox" name="abilita_triggers" '.$abilita_triggers.'>
                                                <i data-swchon-text="SI" data-swchoff-text="NO"></i>
                                                Abilita i triggers<br>
                                                <div class="note">I triggers sono delle automazioni che permettono l\'invio di messaggi o altre operazioni allo scattare di un evento. Abilitando i triggers comparirà una nuova voce di menu</div>
                                            </label>    
                                        </section>
                                    </form>
                                </section>

</form>';


if(_USER_AVVISIAPERTURE){
    $avvisi_aperture = ' checked = "CHECKED" ';
}else{
    $avvisi_aperture = '';
}

if(_USER_CHIUSURE_ORDINI_GESTORI){
    $chiusure_gestore = ' checked = "CHECKED" ';
}else{
    $chiusure_gestore = '';
}

if(_USER_CONVALIDA_ORDINI_GESTORI){
    $convalida_gestore = ' checked = "CHECKED" ';
}else{
    $convalida_gestore = '';
}

if(_USER_MAIL_RICHIESTA_RICARICA){
    $richiesta_ricarica = ' checked = "CHECKED" ';
}else{
    $richiesta_ricarica = '';
}


$a='<form action="" class="smart-form">
    <div class="row">
        <div class="col col-md-6 col-xs-12">
        <section >
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

           </section>
           </div>';


$b='<div class="col col-md-6 col-xs-12">
                <section>

                    <label class="toggle margin-top-10">
                            <input id="cb_chiusure_gestore" type="checkbox" name="chiusure_gestore" '.$chiusure_gestore.'>
                            <i data-swchon-text="SI" data-swchoff-text="NO"></i>
                            Avvisami alla chiusura di un ordine (Se puoi gestirlo).<br>
                            <div class="note">Se selezioni "No" non riceverai la mail che ti avvisa dell\'avvenuta chiusura di un ordine che puoi gestire</div>
                    </label>

                    <hr>

                    <label class="toggle margin-top-10">
                            <input id="cb_convalida_gestore" type="checkbox" name="convalida_gestore" '.$convalida_gestore.'>
                            <i data-swchon-text="SI" data-swchoff-text="NO"></i>
                            Avvisami se un ordine è convalidato, ripristinato o eliminato (Se sei un cassiere o un gestore o un supervisore ordini).<br>
                            <div class="note">Se selezioni "No" non riceverai la mail che ti avvisa dell\'avvenuta convalida, ripristino o eliminazione di un ordine.</div>
                    </label>

                    <hr>

                    <label class="toggle margin-top-10">
                            <input id="cb_richiesta_ricarica" type="checkbox" name="richiesta_ricarica" '.$richiesta_ricarica.'>
                            <i data-swchon-text="SI" data-swchoff-text="NO"></i>
                            Avvisami se un utente chiede una ricarica (Se sei un cassiere)<br>
                            <div class="note">Se selezioni "No" non riceverai la mail che ti avvisa della richiesta di ricarica da parte degli utenti.</div>
                    </label>

                </section>
            </div>
      </div>

</form>';




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
                    <input type="checkbox" id="smart-suonini" class="checkbox style-0">
                    <span>Zittisci suonini fastidiosi</span>
                </label>
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
                    <a href="javascript:void(0);" class="btn btn-xs btn-block btn-primary" id="reset-smart-widget" data-action="resetWidgets" data-title="Resetta">
                        <i class="fa fa-refresh"></i> Fai un reset !</a>
                <h6 class="margin-top-10 semi-bold margin-bottom-5">Ripristina tutti gli aiuti</h6>
                    <a href="javascript:void(0);" class="btn btn-xs btn-block btn-default" id="ripristina_help">
                        <i class="fa fa-refresh"></i> Ripristina !</a>
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

//GOOGLE ID
if(CAST_TO_STRING($U->google_id)<>''){
    $gid='<a class="btn btn-block btn-danger" href="javascript:void(0);" id="remove_id_google">SCOLLEGATI da Google</a>'; 
}else{
    $gid='<a class="btn btn-block btn-success" href="google_connect.php">COLLEGA reteDES a Google</a>';
}




// GESTIONE DEI PERMESSI ----------------------------------------------------
$user_permission = _USER_PERMISSIONS;
//permessi
function r($status,$title,$pop=null){return $status ? '<span rel="popover-hover" data-placement="top" data-original-title="'.$title.'" data-content="'.$pop.'"><i class="fa fa-check txt-color-green"></i> '.$title.' </span>' : '<span class="txt-color-blueLight"><i class="fa fa-times txt-color-red"></i> '.$title.' </span';}

if(_USER_PUO_MODIFICARE_HELP){
    $user_modifica_help = '<span><i class="fa fa-check txt-color-green"></i> Può modificare gli help </span>';
}else{
    $user_modifica_help = '<span><i class="fa fa-times txt-color-red"></i> Può modificare gli help </span>';
}

if(_USER_SUPERVISORE_ANAGRAFICHE){
    $user_supervisione_anagrafiche = '<span><i class="fa fa-check txt-color-green"></i> Può supervisionare le anagrafiche </span>';
}else{
    $user_supervisione_anagrafiche = '<span><i class="fa fa-times txt-color-red"></i><span class="txt-color-blueLight"> Può supervisionare le anagrafiche </span></span>';
}

$p= '
<dl class="dl-horizontal">
    <dt>Operativi</dt>
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
    <dt>Gestionali GAS</dt>
    <dd>
    '.r($user_permission & perm::puo_vedere_tutti_ordini,"Supervisionare gli ordini").'
    </dd>
    <dd>
    '.r($user_permission & perm::puo_gestire_utenti,"Gestire gli utenti").'
    </dd>
    <dd>
    '.r($user_permission & perm::puo_creare_gas,"Gestire le anagrafiche del proprio GAS").'
    </dd>
    <dd>
    '.r($user_permission & perm::puo_gestire_la_cassa,"Gestire la cassa (se attiva)").'
    </dd>
    <dt>Amministrativi</dt>
    <dd>
    '.$user_modifica_help.'
    </dd>
    <dd>
    '.r($user_permission & perm::puo_eliminare_messaggi,"Moderare i commenti").'
    </dd>
    <dd>
    '.r($user_permission & perm::puo_vedere_retegas,"Gestire il proprio DES").'
    </dd>
    <dd>
    '.$user_supervisione_anagrafiche.'
    </dd>

</dl>';



//SFONDI EXTRA

$margherite=    "<img src='img/pattern/margherite-xs.png'   data-htmlbg-url='img/pattern/margherite.png'    width='22' height='22' class='bordered cursor-pointer margin-right-5'>";
$legno_chiaro=  "<img src='img/pattern/wood-xs.jpg'         data-htmlbg-url='img/pattern/wood.jpg'          width='22' height='22' class='bordered cursor-pointer margin-right-5 margin-top-5'>";
$tessuto_blu=   "<img src='img/pattern/tessuto_blu-xs.png'  data-htmlbg-url='img/pattern/tessuto_blu.jpg'          width='22' height='22' class='bordered cursor-pointer margin-right-5 margin-top-5'>";
$stelle=        "<img src='img/pattern/star-space-tile-xs.png'  data-htmlbg-url='img/pattern/star-space-tile.jpg'          width='22' height='22' class='bordered cursor-pointer margin-right-5 margin-top-5'>";
$righine=       "<img src='img/pattern/righine.jpg'  data-htmlbg-url='img/pattern/righine.jpg'          width='22' height='22' class='bordered cursor-pointer margin-right-5 margin-top-5'>";
$wood_06=       "<img src='img/pattern/wood_06.jpg'  data-htmlbg-url='img/pattern/wood_06.jpg'          width='22' height='22' class='bordered cursor-pointer margin-right-5 margin-top-5'>";


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
    $button_attiva='<a id="collega_telegram" target="_BLANK" href="https://telegram.me/reteDESbot?start='.$link_telegram.'" class="btn btn-success margin-top-10" >ATTIVA il bot telegram di ReteDES.it</a>';
}else{
    $status = "Il tuo account è già collegato a telegram e pronto all'uso;";
    $button_disattiva = '<a id="scollega_telegram" class="btn btn-danger margin-top-10">DISATTIVA il bot telegram di ReteDES.it</a> <a href="https://web.telegram.org/#/im?p=@ReteDESbot" class="btn btn-info margin-top-10" TARGET="_BLANK">Apri telegram</a>';
    $button_attiva='';
}

if(_USER_NOTIFICHE_TELEGRAM){
    $notifiche_telegram = ' checked = "CHECKED" ';
}else{
    $notifiche_telegram = '';
}

$t='<form action="" class="smart-form">
                            <section>
                                    <label class="toggle margin-top-10">
                                            <input id="te_notifiche" type="checkbox" name="notifiche_telegram" '.$notifiche_telegram.'>
                                            <i data-swchon-text="SI" data-swchoff-text="NO"></i>
                                            Mandami notifiche da telegram<br>
                                            <div class="note">Se selezioni "No" non riceverai notifiche dal bot di telegram, ma potrai comunque usarlo per richiedere informazioni.</div>
                                    </label>
                            </section>
     </form>';



//ADDTOCALENDAR
$atc='                  <section>
                        <span class="toggle margin-top-10">Trasforma le date in link per gestire gli eventi nel tuo calendario.</span>

                                    <form class="smart-form">
                                        <section class="font-xs">
                                            <div class="inline-group">
                                                <label class="radio">
                                                    <input type="radio" name="scelta_atc" '.$atc_si.' value="SI" class="scelta_addtocalendar">
                                                    <i></i>SI</label>
                                                <label class="radio">
                                                    <input type="radio" name="scelta_atc" '.$atc_no.' value="NO" class="scelta_addtocalendar">
                                                    <i></i>NO</label>
                                            </div>
                                        </section>
                                        <span class="note">Questa opzione ti permette di aggiungere le date che leggin retedes ad un tuo calendario personale.</span>
                                    </form>

                                </section>';     
     
?>

<script src="https://apis.google.com/js/platform.js?onload=renderButton" async defer></script>
<div class="inbox-nav-bar no-content-padding">
    <h1 class="page-title txt-color-blueDark"><i class="fa fa-fw fa-gear"></i> Impostazioni &nbsp;</h1>
</div>

<section id="widget-grid" class="margin-top-10">



    <div class="row">
        <!-- PRIMA COLONNA-->
        

        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well well-sm">
                <h3><i class="fa fa-envelope-o text-info"></i>&nbsp;notifiche via email</h3>
                <?php echo $a; ?>
                <?php echo $b; ?>
            </div>
        </div>
        <div class="">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                <div class="well well-sm">
                    <h3><i class="fa fa-paper-plane-o text-info"></i>&nbsp;Notifiche Telegram</h3>
                    <small><?php echo $status?></small>
                    <p class="note">Leggi l'help per tutte le informazioni</p>
                    <?php echo $button_attiva.$button_disattiva; ?>
                    <?php echo $t;?>
                    <hr>
                    <h3><i class="fa fa-check-square-o text-info"></i>&nbsp;Opzioni</h3>
                    <?php echo $i; ?>
                    <hr>
                    <h3>Funzioni sperimentali</h3>
                    <p>Uso di servizi personalizzati GOOGLE</p>
                    <?php echo $gid;  ?>
                    <div class="clearfix"></div>
                    <p></p>
                    <small class="note margin-top-10"><strong>NB:</strong> il collegamento a GOOGLE è facoltativo (funziona tutto ugualmente se non lo si fa). Serve per abilitare una serie di funzioni per ora sperimentali, ma che saranno rese pubbliche appena testate in maniera soddisfacente.</small>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                <div class="well well-sm">
                    <h3><i class="fa fa-bank text-info"></i>&nbsp;Cassa</h3>
                    <form action="" class="smart-form">
                    <?php echo $gas_usa_cassa; ?>
                    </form>
                    <hr>
                    <h3><i class="fa fa-calendar-o text-info"></i>&nbsp;Aggiungi a calendario</h3>
                    <?php echo $atc; ?>
                    <hr>
                    <h3><i class="fa fa-lock text-info"></i>&nbsp;Permessi</h3>
                    <?php echo $p; ?>
                    
                    
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12col-md-12 col-lg-12">
            <?php echo $wg_visual->print_html(); ?>
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
        </article>
    </div>

</section>


<script type="text/javascript">

    pageSetUp();
    
    
    

    //-------------------------HELP
    <?php echo help_render_js($page_id); ?>
    //-------------------------HELP

    var delay = (function(){
          var timer = 0;
          return function(callback, ms){
            clearTimeout (timer);
            timer = setTimeout(callback, ms);
          };
        })();
    
    // hide bg options
    var smartbgimage = "<h6 class='margin-top-10 semi-bold'>Sfondo</h6><img src='img/pattern/graphy-xs.png' data-htmlbg-url='img/pattern/graphy.png' width='22' height='22' class='margin-right-5 bordered cursor-pointer'><img src='img/pattern/tileable_wood_texture-xs.png' width='22' height='22' data-htmlbg-url='img/pattern/tileable_wood_texture.png' class='margin-right-5 bordered cursor-pointer'><img src='img/pattern/sneaker_mesh_fabric-xs.png' width='22' height='22' data-htmlbg-url='img/pattern/sneaker_mesh_fabric.png' class='margin-right-5 bordered cursor-pointer'><img src='img/pattern/nistri-xs.png' data-htmlbg-url='img/pattern/nistri.png' width='22' height='22' class='margin-right-5 bordered cursor-pointer'><img src='img/pattern/paper-xs.png' data-htmlbg-url='img/pattern/paper.png' width='22' height='22' class='bordered cursor-pointer margin-right-5 '><?php echo $margherite.$legno_chiaro.$tessuto_blu.$stelle.$righine.$wood_06?>";
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

$('#smart-suonini')
    .on('change', function (e) {
        if ($(this)
            .prop('checked')) {
            localStorage.setItem('_USER_SOUND_MUTED', 1);
        } else {
            localStorage.setItem('_USER_SOUND_MUTED', 0);
        }
    });
    if (localStorage.getItem('_USER_SOUND_MUTED') == 1) {
    $('#smart-suonini')
        .prop('checked', true);
    } else {
        $('#smart-suonini')
            .prop('checked', false);
    }
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

            $.ajax({
                      type: "POST",
                      url: "ajax_rd4/_act_main.php",
                      dataType: 'json',
                      data: {act: "insidecontainer", value : "SI"},
                      context: document.body
                    }).done(function(data) {
                        if(data.result=="OK"){
                            ok(data.msg);
                        }else{
                            ko(data.msg);
                        }

                    });

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

            $.ajax({
                      type: "POST",
                      url: "ajax_rd4/_act_main.php",
                      dataType: 'json',
                      data: {act: "insidecontainer", value : "NO"},
                      context: document.body
                    }).done(function(data) {
                        if(data.result=="OK"){
                            ok(data.msg);
                        }else{
                            ko(data.msg);
                        }

                    });


            $("#smart-bgimages")
                .fadeOut();
            $("html").css("background-image", '');
        }
    });

 /*
 * REFRESH WIDGET

$("#reset-smart-widget")
    .bind("click", function () {
        $('#refresh')
            .click();
        return false;
    });
    */
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
    $('#cb_avvisi_aperture').change(function () {
        if(this.checked) {value = "SI";}else{value = "NO";}
        $.ajax({
          type: "POST",
          url: "ajax_rd4/user/_act.php",
          dataType: 'json',
          data: {act: "avvisi_aperture", value : value},
          context: document.body
        }).done(function(data) {
            if(data.result=="OK"){
                    ok(data.msg);}else{ko(data.msg);}
        });
    });

    $('#cb_chiusure_gestore').change(function () {
        if(this.checked) {value = "SI";}else{value = "NO";}
        $.ajax({
          type: "POST",
          url: "ajax_rd4/user/_act.php",
          dataType: 'json',
          data: {act: "chiusure_gestore", value : value},
          context: document.body
        }).done(function(data) {
            if(data.result=="OK"){
                    ok(data.msg);}else{ko(data.msg);}
        });

    });

    $('#cb_convalida_gestore').change(function () {
        if(this.checked) {value = "SI";}else{value = "NO";}
        $.ajax({
          type: "POST",
          url: "ajax_rd4/user/_act.php",
          dataType: 'json',
          data: {act: "convalida_gestore", value : value},
          context: document.body
        }).done(function(data) {
            if(data.result=="OK"){
                    ok(data.msg);}else{ko(data.msg);}
        });

    });
    $('#cb_richiesta_ricarica').change(function () {
        if(this.checked) {value = "SI";}else{value = "NO";}
        $.ajax({
          type: "POST",
          url: "ajax_rd4/user/_act.php",
          dataType: 'json',
          data: {act: "richiesta_ricarica", value : value},
          context: document.body
        }).done(function(data) {
            if(data.result=="OK"){
                    ok(data.msg);}else{ko(data.msg);}
        });

    });

    $('#te_notifiche').change(function () {
        if(this.checked) {value = "SI";}else{value = "NO";}
        $.ajax({
          type: "POST",
          url: "ajax_rd4/user/_act.php",
          dataType: 'json',
          data: {act: "notifiche_telegram", value : value},
          context: document.body
        }).done(function(data) {
            if(data.result=="OK"){
                    ok(data.msg);}else{ko(data.msg);}
        });


    });
    
     $('#abilita_triggers').change(function () {
        if(this.checked) {value = "SI";}else{value = "NO";}
        $.ajax({
          type: "POST",
          url: "ajax_rd4/user/_act.php",
          dataType: 'json',
          data: {act: "abilita_triggers", value : value},
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
                content : 'Se attivi la cassa, confermi di accettare quanto riportato <a href="#ajax_rd4/help/help_cassa.php" target="_BLANK">qui</a>',
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
                    $('#cb_user_usa_cassa').prop('checked', false);
                }

            });


            e.preventDefault();
        });

        function save_mandate_id(){
            var mandate_id = $('#_USER_MANDATE_ID').val();
            $.ajax({
                      type: "POST",
                      url: "ajax_rd4/user/_act.php",
                      dataType: 'json',
                      data: {act: "_USER_MANDATE_ID", mandate_id: mandate_id},
                      context: document.body
                    }).done(function(data) {
                        if(data.result=="OK"){
                            ok(data.msg);
                        }else{
                            ko(data.msg);
                        }

                    });
        }
        
        $('#_USER_MANDATE_ID').keyup(function() {
            delay(function(){
              save_mandate_id();
            }, 1000 );
        });
        
        function save_iban(){
            var iban = $('#_USER_IBAN').val();
                $.ajax({
                      type: "POST",
                      url: "ajax_rd4/user/_act.php",
                      dataType: 'json',
                      data: {act: "_USER_IBAN", iban: iban},
                      context: document.body
                    }).done(function(data) {
                        if(data.result=="OK"){
                            ok(data.msg);
                        }else{
                            ko(data.msg);
                        }

                    });
        }
        
        $('#_USER_IBAN').keyup(function() {
            delay(function(){
              save_iban();
            }, 1000 );
        });
        
        function save_mandate_date(){
            var mandate_date = $('#_USER_MANDATE_DATE').val();
                $.ajax({
                      type: "POST",
                      url: "ajax_rd4/user/_act.php",
                      dataType: 'json',
                      data: {act: "_USER_MANDATE_DATE", mandate_date: mandate_date},
                      context: document.body
                    }).done(function(data) {
                        if(data.result=="OK"){
                            ok(data.msg);
                        }else{
                            ko(data.msg);
                        }

                    });
        }
        
        $('#_USER_MANDATE_DATE').keyup(function() {
            delay(function(){
              save_mandate_date();
            }, 1000 );
        });
        
        $('#csv_separator').keyup(function(e){
            if (e.which < 0x20) {
                console.log('this is not printable char');
                return;
            }
            else {
                var chara = $(this).val();
                $.ajax({
                      type: "POST",
                      url: "ajax_rd4/user/_act.php",
                      dataType: 'json',
                      data: {act: "csv_separator", chara: chara},
                      context: document.body
                    }).done(function(data) {
                        if(data.result=="OK"){
                            ok(data.msg);

                        }else{ko(data.msg);}

                    });
            }
        })

        $(document).off('change','.scelta_decimale');
        $(document).on('change','.scelta_decimale',function(){
            var scelta = $(this).val();
            $.ajax({
                      type: "POST",
                      url: "ajax_rd4/user/_act.php",
                      dataType: 'json',
                      data: {act: "scelta_decimale", scelta:scelta},
                      context: document.body
                    }).done(function(data) {
                        if(data.result=="OK"){
                            ok(data.msg);
                        }else{
                            ko(data.msg);
                        }

                    });
        });
        $(document).off('change','.scelta_metodo_inserimento');
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

        $(document).off('change','.scelta_addtocalendar');
        $(document).on('change','.scelta_addtocalendar',function(){
            var scelta = $(this).val();
            $.ajax({
                      type: "POST",
                      url: "ajax_rd4/user/_act.php",
                      dataType: 'json',
                      data: {act: "scelta_atc", scelta:scelta},
                      context: document.body
                    }).done(function(data) {
                        if(data.result=="OK"){
                            okReload(data.msg);
                        }else{
                            ko(data.msg);
                        }

                    });
        });
        
        $('#ripristina_help').click(function(){
                    console.log("Ripristina help");
                    $.ajax({
                      type: "POST",
                      url: "ajax_rd4/user/_act.php",
                      dataType: 'json',
                      data: {act: "ripristina_help"},
                      context: document.body
                    }).done(function(data) {
                        if(data.result=="OK"){
                                $.smallBox({
                                    title : 'ReteDES.it',
                                    content : data.msg + '<p class=\"text-align-right\"><a href=\"javascript:void(0);\" class=\"btn btn-default btn-sm\" onclick=\"javascript:location.reload();\">Ok</a></p>',
                                    color : '#0074A7',
                                    //timeout: 8000,
                                    icon : 'fa fa-bell swing animated'
                                });
                        }else{ko(data.msg);}

                    });
            });

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
</script>

