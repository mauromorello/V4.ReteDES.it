<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.ordine.php");
$converter = new Encryption;

$ui = new SmartUI;
$page_title= "Scheda ordine";
$page_id = "scheda_ordine";
//CONTROLLI

$id_ordine = CAST_TO_INT($_GET["id"],0);
$O = new ordine($id_ordine);
$gestore=false;
$link_gestore =='';
$pulsante_gestisci ='';

if($O->is_nascosto_per_il_gas(_USER_ID_GAS)){
    echo rd4_go_back("Non è possibile visionare questo ordine.");
    die();    
}

$richiedi_info = CAST_TO_INT($_GET["i"],0,1);


    //GAS POTENZIALE PARTECIPANTE
    $ok=false;
    $rows = $O->lista_gas_potenziali_partecipanti();
    foreach($rows as $row){
        if($row["id_gas"]==_USER_ID_GAS){
            $ok=true;
        }

        $colore = " text-success ";
        if($row["userid"]>0){
            $ref = 'GAS che partecipa';
            $colore = " txt-color-greenDark ";
        }else{
            $ref = 'in attesa di un referente';
            $colore = " txt-color-lightDark ";
        }

        $dimensione = " ";
        if($row["userid"]==$O->id_referente_ordine){
            $ref = 'GAS che gestisce l\'ordine';
            $dimensione = " fa-2x ";
        }

        $gas_coinvolti.= '<li>
                                <span class="">
                                    <span class="pull-right '.$colore.'"><i class="fa fa-home '.$dimensione.'"></i></span>
                                    <img src="'.src_gas($row["id_gas"]).'" alt="" class="air air-top-left" width="46" height="46" style="margin-left:6px;"/>
                                    <a href="javascript:void(0);" class="msg">

                                        <span class="subject"><strong>'.$row["descrizione_gas"].'</strong></span>
                                        <span class="msg-body font-xs">'.$ref.'</span>
                                    </a>
                                </span>
                            </li>';
    }
    if(!$ok){
        echo rd4_go_back("Questo ordine non è condiviso con il tuo GAS.");
        die();
    }

    //Pulsante chiedi info

    //$button_spesa='<div class=" well text-center">        <a href="#ajax_rd4/reports/la_mia_spesa.php?id='.$id_ordine.'" class="btn btn-lg btn-info btn-block"><i class="fa fa-2x fa-eye pull-left"></i><p></p>VEDI..</a><br><span class="font-xs">...la tua spesa e gli articoli acquistati</span></div>';

    $pulsante_chiedi_info='<div class=" well text-center"><button class="btn btn-lg btn-default btn-block" data-toggle="modal" data-target="#modal_richiesta_info" id="chiedi_info_button"><i class="fa fa-2x fa-question pull-left text-muted"></i><p></p>CHIEDI..</button><br><span class="font-xs">...informazioni ai gestori dell\'ordine</span></div>';


    //GAS POTENZIALE PARTECIPANTE

    if (posso_gestire_ordine($O->id_ordini)){
        if($O->id_gas_referente==_USER_ID_GAS){
             $pulsante_gestisci ='<a href="#ajax_rd4/ordini/edit.php?id='.$id_ordine.'" class="btn btn-default btn-block m-bottom-10"><i class="fa fa-gears pull-left"></i><b>GESTISCI</b><br>tutto l\'ordine</a>';
             $gestore=true;
             $link_gestore = '<a href="#ajax_rd4/ordini/edit.php?id='.$O->id_ordini.'"><i class="fa fa-gears"></i></a>';
        }
    }

    $rows = $O->lista_referenti_extra();
    if(count($rows)>0){
        foreach($rows as $row){
           $userGestoriExtraidEnc = $converter->encode($row["id_user"]);
           $gestori_extra .= '<li>
                            <span class="read">
                                <span class="pull-right text-success"><i class="fa fa-briefcase"></i></span>
                                <a href="#ajax_rd4/user/scheda.php?id='.$userGestoriExtraidEnc.'" class="msg">
                                    <img src="'.src_user($row["id_user"]).'" alt="" class="air air-top-left" width="36" height="36" />
                                    <span class="subject"><strong>'.$row["fullname"].'</strong> <i class="font-xs">'.$row["descrizione_gas"].'</i></span>
                                    <span class="msg-body font-xs">Referente EXTRA</span>
                                </a>
                            </span>
                        </li>';
        }

    }
    $userGestoreidEnc = $converter->encode($O->id_utente);
    $referente_ordine ='<li>
                        <span class="read">
                            <span class="pull-right text-success"><i class="fa fa-graduation-cap  fa-2x"></i></span>
                            <a href="#ajax_rd4/user/scheda.php?id='.$userGestoreidEnc.'" class="msg">
                                <img src="'.src_user($O->id_utente).'" alt="" class="air air-top-left" width="36" height="36" />
                                <span class="subject"><strong>'.$O->fullname_referente.'</strong> <i class="font-xs">'.$O->descrizione_gas_referente.'</i></span>
                                <span class="msg-body font-xs"><strong>Referente ordine</strong></span>
                            </a>
                        </span>
                    </li>';
    $rowG = $O->referente_ordine_gas(_USER_ID_GAS);
    $pulsante_gestisci_per_gas='';
    if($rowG["userid"]>0){
        $userGestoreGasidEnc = $converter->encode($rowG["userid"]);
        $referente_ordine_gas ='<li>
                            <span class="read">
                                <span class="pull-right text-success"><i class="fa fa-graduation-cap"></i></span>
                                <a href="#ajax_rd4/user/scheda.php?id='.$userGestoreGasidEnc.'" class="msg">
                                    <img src="'.src_user($rowG["userid"]).'" alt="" class="air air-top-left" width="36" height="36" />
                                    <span class="subject"><strong>'.$rowG["fullname"].'</strong> <i class="font-xs">'.$rowG["descrizione_gas"].'</i></span>
                                    <span class="msg-body font-xs"><strong>Referente per il tuo GAS</strong></span>
                                </a>
                            </span>
                        </li>';
        $pulsante_offerta_referente='<button class="btn btn-default btn-block disabled"><i class="fa fa-graduation-cap  pull-left"></i>  <strong>DIVENTA REFERENTE</strong><br> PER IL TUO GAS</button>';
        $pulsante_preferito='<button class="btn btn-default btn-block text-success"><i class="fa fa-star-o pull-left"></i>  Preferito</button>';



        //Sono il referente GAS
        if($rowG["userid"]==_USER_ID){
            $pulsante_gestisci_per_gas='<a href="#ajax_rd4/ordini/edit_gas.php?id='.$id_ordine.'" class="btn btn-default btn-block margin-top-10" id="gestisci_per_gas"><i class="fa fa-gear  pull-left"></i><b>GESTISCI</b><br>per il tuo GAS</a><hr>';
        }

        $pulsante_gestisci_amici='<a href="#ajax_rd4/ordini/ordini_amici.php?id='.$id_ordine.'" class="btn btn-default btn-block margin-top-10" id="gestisci_amici"><i class="fa fa-group  pull-left"></i><b>GESTISCI</b> la spesa amici</a><hr>';


        if($O->codice_stato<>"CO"){
            if(_USER_ID<>$O->id_referente_ordine){
                if(_USER_ID<>$O->referente_ordine_gas(_USER_ID_GAS)){
                    $pulsante_aiuta='<button class="btn btn-default btn-block animated swing margin-top-5" id="offri_aiuto"><i class="fa fa-hand-o-up  pull-left"></i>  OFFRI IL TUO AIUTO</button>';
                }else{
                    $pulsante_aiuta='<button class="btn btn-default btn-block  disabled margin-top-5"><i class="fa fa-hand-o-up  pull-left"></i>  OFFRI IL TUO AIUTO</button>';
                }
            }else{
                $pulsante_aiuta='<button class="btn btn-default btn-block  disabled margin-top-5"><i class="fa fa-hand-o-up  pull-left"></i>  OFFRI IL TUO AIUTO</button>';
            }
        }else{
            $pulsante_aiuta='<button class="btn btn-default btn-block  disabled margin-top-5"><i class="fa fa-hand-o-up  pull-left"></i>  OFFRI IL TUO AIUTO</button>';
        }

    }else{
        $referente_ordine_gas ='';
        $pulsante_aiuta='<button class="btn btn-default btn-block  disabled margin-top-5"><i class="fa fa-hand-o-up  pull-left"></i>  OFFRI IL TUO AIUTO</button>';

        //solo se è aperto
        if(($O->codice_stato=="AP") OR ($O->codice_stato=="PR")){
            $pulsante_offerta_referente='<button class="btn btn-default btn-block animated swing" id="diventa_referente"><i class="fa fa-graduation-cap  pull-left"></i>  <strong>DIVENTA REFERENTE</strong><br> PER IL TUO GAS</button>';
            $info_referente='<p class="alert alert-danger"><b>NB:</b> Non puoi comprare nulla in questo ordine perchè non ha un referente per il tuo GAS.</p>';
        }else{
            $pulsante_offerta_referente='<button class="btn btn-default btn-block disabled"><i class="fa fa-graduation-cap  pull-left"></i>  <strong>DIVENTA REFERENTE</strong><br> PER IL TUO GAS</button>';
        }
    }

//ADD TO CALENDAR
    if(_USER_ADDTOCALENDAR){
            $atc='
                    <span class="addtocalendar" >
                    <a class="atcb-link"><i class="fa fa-calendar"></i></a>
                    <var class="atc_event">
                        <var class="atc_date_start">'.$O->data_apertura.'</var>
                        <var class="atc_date_end">'.$O->data_chiusura.'</var>
                        <var class="atc_timezone">Europe/Rome</var>
                        <var class="atc_title">Ord #'.$O->id_ordini.': '.$O->descrizione_ordini.'</var>
                        <var class="atc_description">Referente: '.$O->fullname_referente.' '.$O->descrizione_ordini.'; Ditta:'.$O->descrizione_ditte.', Listino: '.$O->descrizione_listini.' Note:'.substr(strip_tags($O->note_ordini),0,200).'</var>
                        <var class="atc_location">'.$O->descrizione_gas_referente.'</var>
                        <var class="atc_organizer">'.$O->fullname_referente.' '.$O->descrizione_gas_referente.'</var>
                        
                    </var>
                </span>&nbsp;&nbsp
                ';
            //$atc="";
            
            $array_consegna = $O->referente_ordine_gas(_USER_ID_GAS);
            $atc_cons='
                    <span class="addtocalendar" >
                    <a class="atcb-link"><i class="fa fa-calendar"></i></a>
                    <var class="atc_event">
                        <var class="atc_date_start">'.$O->data_distribuzione_start(_USER_ID_GAS).'</var>
                        <var class="atc_date_end">'.$O->data_distribuzione_start(_USER_ID_GAS).'</var>
                        <var class="atc_timezone">Europe/Rome</var>
                        <var class="atc_title">CONSEGNA Ord #'.$O->id_ordini.': '.$O->descrizione_ordini.'</var>
                        <var class="atc_description">Referente: '.$array_consegna[0].' '.$O->descrizione_ordini.'; Ditta:'.$O->descrizione_ditte.', Listino: '.$O->descrizione_listini.' Note distribuzione:'.strip_tags($O->testo_distribuzione(_USER_ID_GAS)).'</var>
                        <var class="atc_location">'.$O->luogo_distribuzione(_USER_ID_GAS).'</var>
                        <var class="atc_organizer">'.$array_consegna[0].' '.$O->descrizione_gas_referente.'</var>
                        
                    </var>
                </span>&nbsp;&nbsp;
                ';
            $pulsante_atc_ordine=$atc;
            $pulsante_atc_consegna=$atc_cons;    
        }else{
            $pulsante_atc_ordine="";
            $pulsante_atc_consegna="";
        }
    
    

$rows = $O->lista_aiuti_ordine();
if(count($rows)>0){
    foreach($rows as $row){
        if($row["id_user"]==_USER_ID){$pulsante_aiuta='';}
        $userAiutaidEnc = $converter->encode($row["id_user"]);
        if($row["valore_int"]==1){
            $aiuti .= '<li>
                            <span class="read">
                                <span class="pull-right text-success"><i class="fa fa-wrench "></i></span>
                                <a href="#ajax_rd4/user/scheda.php?id='.$userAiutaidEnc.'" class="msg">
                                    <img src="'.src_user($row["id_user"]).'" alt="" class="air air-top-left margin-top-5" width="32" height="32" />
                                    <span class="subject"><strong>'.$row["fullname"].'</strong> <i class="font-xs">'.$row["descrizione_gas"].'</i></span>
                                    <span class="msg-body font-xs">Umile aiutante</span>
                                </a>
                            </span>
                        </li>';
        }else{
           $aiuti .= '<li>
                            <span class="read">
                                <span class="pull-right text-danger"><i class="fa fa-wrench "></i></span>
                                <a href="#ajax_rd4/user/scheda.php?id='.$userAiutaidEnc.'" class="msg">
                                    <img src="'.src_user($row["id_user"]).'" alt="" class="air air-top-left margin-top-5" width="32" height="32" />
                                    <span class="subject text-muted"><strong>'.$row["fullname"].'</strong> <i class="font-xs">'.$row["descrizione_gas"].'</i></span>
                                    <span class="msg-body font-xs">Vorrebbe aiutare...</span>
                                </a>
                            </span>
                        </li>';
        }
    }

}
$r = $O->lista_referenze(_USER_ID_GAS);
//if(true){
    if(conv_datetime_from_db($O->data_distribuzione_start(_USER_ID_GAS))<>"00/00/0000 00:00"){
        $consegna='<p>'.$pulsante_atc_consegna.'Consegna: '.conv_datetime_from_db($O->data_distribuzione_start(_USER_ID_GAS)).'</p>
                    <p>Luogo: '.$O->luogo_distribuzione(_USER_ID_GAS).'</p>
                    <p class="alert alert-info">Note: <i>'.$O->testo_distribuzione(_USER_ID_GAS).'</i></p>';


        $oldLocale = setlocale(LC_TIME, 'it_IT');
        $data_consegna_lunga = '<strong>'.utf8_encode( strftime("%A %d %B %G alle %H:%M", strtotime($O->data_distribuzione_start(_USER_ID_GAS)))).'</strong>';
        $data_consegna_lunga = $pulsante_atc_consegna.'&nbsp;'.str_replace("alle 00:00", "", $data_consegna_lunga);

        setlocale(LC_TIME, $oldLocale);
    }else{
        $consegna='';
        $data_consegna_lunga = ''; 
    }
    if($O->luogo_distribuzione(_USER_ID_GAS)<>""){
        $presso = ' presso <strong>'.$O->luogo_distribuzione(_USER_ID_GAS).'</strong> ';
    }else{
        $presso = '';
    }

    if($O->testo_distribuzione(_USER_ID_GAS)<>""){
        $note_consegna= '('.$O->testo_distribuzione(_USER_ID_GAS).')';
    }else{
        $note_consegna= '';
    }

    $consegna='<p id="data_consegna">Consegna: '.$data_consegna_lunga.$presso.$note_consegna.'</p>';




$o='
        <div class="row">
            <div class="col-md-6">
            <p class="font-lg text-center padding-10">Informazioni:</p>
                <div class="well well-sm">

                    <p>'.$pulsante_atc_ordine.'Apre: <strong>'.$O->data_apertura_lunga.'</strong></p>
                    <p>Chiude: <strong>'.$O->data_chiusura_lunga.'</strong></p>
                    '.$consegna.'
                </div>
            <p class="font-lg text-center padding-10">I GAS coinvolti: </p>
                <div class="well well-sm no-padding">
                <ul class="notification-body no-padding margin-top-10">
                    '.$gas_coinvolti.'
                </ul>
                </div>

            </div>
            <div class="col-md-6" style="max-height:320px;overflow:auto;">
                <p class="font-lg text-center padding-10">La macchina organizzativa: </p>
                <div class="well well-sm no-padding">
                <ul class="notification-body no-padding margin-top-10">
                    '.$referente_ordine.'
                    '.$referente_ordine_gas.'
                    '.$gestori_extra.'
                    '.$aiuti.'
                </ul>
                </div>
            </div>
        </div>
    ';

    if($O->costo_trasporto>0){$msg_trasporto = '<div class="alert alert-info margin-top-5">Il referente prevede un costo di trasporto merce di circa <strong>'.round($O->costo_trasporto,2).' €</strong> che verrà ripartito tra i partecipanti una volta concluso l\'ordine.</div>';}
    if($O->costo_gestione>0){$msg_gestione = '<div class="alert alert-info margin-top-5">Il referente prevede un costo di gestione ordine di circa <strong>'.round($O->costo_gestione,2).' € </strong> che verrà ripartito tra i partecipanti.</div>';}
    if($O->costo_gas_referenza_v2(_USER_ID_GAS)>0){$msg_costo_gas = '<div class="alert alert-warning margin-top-5">Il referente del tuo GAS prevede un costo aggiuntivo di <strong>'.round($O->costo_gas_referenza_v2(_USER_ID_GAS),2).' &euro;</strong> che verrà ripartito tra i partecipanti.</div>';}
    if($O->maggiorazione_percentuale_referenza_v2(_USER_ID_GAS)>0){$msg_maggiorazione_gas = '<div class="alert alert-warning margin-top-5">Il referente del tuo GAS prevede una maggiorazione del <strong>'.round($O->maggiorazione_percentuale_referenza_v2(_USER_ID_GAS),2).'% </strong> che sarà applicata alla tua spesa fatta, addotta a questa motivazione:<br><p>'.$O->motivo_maggiorazione_v2(_USER_ID_GAS).'</p></div>';}
    if($O->numero_ordini_calendarizzati()>0){$msg_ordini_calendarizzati = '<div class="alert alert-danger margin-top-5">Questo ordine ha una lista di '.$O->numero_ordini_calendarizzati().' ordini calendarizzati che non sono ancora stati programmati.</div>';}
    if($O->numero_ordini_programmati()>0){$msg_ordini_programmati = '<div class="alert alert-info margin-top-5">Questo ordine ha una lista di '.$O->numero_ordini_programmati().' ordini programmati.</div>';}

    if($O->calendario>0){$msg_figlio_calendarizzato = '<div class="alert alert-info margin-top-5">Questo ordine è stato calendarizzato dall\'ordine base <strong><a href="#ajax_rd4/ordini/ordine.php?id='.$O->calendario.'">#'.$O->calendario.'</a></strong></div>';}

    if($O->codice_stato=="AP"){
        $button_aperto='<div class="well text-center"><a href="#ajax_rd4/ordini/compra.php?id='.$O->id_ordini.'" class="btn btn-lg rd4_soldi btn-block"><i class="fa fa-2x fa-shopping-cart pull-left"></i><p></p>COMPRA...</a><br><span class="font-xs"> ... scegliendo tra gli articoli disponibili.</span></div>';
    }else{
        if(posso_gestire_ordine($O->id_ordini)){
            $button_aperto='<div class="well text-center"><a href="#ajax_rd4/ordini/compra.php?id='.$O->id_ordini.'" class="btn btn-lg btn-block"><i class="fa fa-2x fa-shopping-cart pull-left"></i><p></p>COMPRA...</a><br><span class="font-xs"> ... Perchè sei un gestore.</span></div>';
        }else{
            $button_aperto='<div class="well text-center"><a href="javascript:void(0);" class="btn btn-lg disabled rd4_soldi btn-block"><i class="fa fa-2x fa-shopping-cart pull-left"></i><p></p>COMPRA...</a><br><span class="font-xs"> ... scegliendo tra gli articoli disponibili.</span></div>';
        }
        //$button_aperto='';
    }

    if(VA_ORDINE_USER($id_ordine,_USER_ID)>0){
        $button_spesa='<div class=" well text-center"><a href="#ajax_rd4/reports/la_mia_spesa.php?id='.$id_ordine.'" class="btn btn-lg btn-info btn-block"><i class="fa fa-2x fa-eye pull-left"></i><p></p>VEDI..</a><br><span class="font-xs">...la tua spesa e gli articoli acquistati</span></div>';
    }else{
        $button_spesa='<div class=" well text-center"><a href="javascript:void(0);" class="btn btn-lg btn-info btn-block disabled"><i class="fa fa-2x fa-eye pull-left"></i><p></p>VEDI..</a><br><span class="font-xs">...la tua spesa e gli articoli acquistati</span></div>';
        //$button_spesa='';
    }

    if($O->get_note_utente(_USER_ID)<>""){
        $nota_utente='<p class="alert alert-info margin-top-10"><b>HAI UNA TUA NOTA: </b> '.$O->get_note_utente(_USER_ID).'</p>';
    }else{
        $nota_utente='';
    }

    //AMAZON S3
    $bucket = 'retedes';
    $folder = 'public_rd4/bacheca/ordine/'.$O->id_ordini;

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

    <!-- Modal -->
        <div class="modal fade" id="modal_richiesta_info" tabindex="-1" role="dialog" aria-labelledby="richiesta_info_Label">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Richiesta informazioni</h4>

              </div>
              <div class="modal-body">
                <form class="form-horizontal" role="form">
                  <div class="form-group">
                    <label  class="col-sm-2 control-label"
                              for="inputEmail3">Messaggio</label>
                    <div class="col-sm-10">
                        <textarea type="textarea" class="form-control" id="inputEmail3" placeholder="scrivi qua..." rows="6"></textarea>
                    </div>
                  </div>


                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <button type="submit" class="btn btn-default" id="do_richiesta_info">Invia</button>
                      <p class="note margin-top-10">Cliccando su "invia" verrà inoltrata una mail a tutti i gestori di questo ordine;</p>
                    </div>
                  </div>
                </form>
                
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Annulla</button>
              </div>
            </div>
          </div>
        </div>

<?php echo $O->navbar_ordine().$msg_trasporto.$msg_ordini_programmati.$msg_figlio_calendarizzato.$msg_gestione.$msg_ordini_calendarizzati.$msg_costo_gas.$msg_maggiorazione_gas.$info_referente.$nota_utente; ?>

<div class="row margin-top-10">
    <div class="col-md-4">
        <?php echo $button_aperto; ?>
    </div>
    <div class="col-md-4">
        <?php echo $button_spesa; ?>
    </div>
    <div class="col-md-4">
        <?php echo $pulsante_chiedi_info; ?>
    </div>
</div>
<div class="row margin-top-5">
    <div class="col-md-4">
        <?php echo $pulsante_offerta_referente; ?>
    </div>
    <div class="col-md-4">
        <?php echo $pulsante_aiuta; ?>
    </div>
    <div class="col-md-4">
        <?php echo $pulsante_gestisci_amici; ?>
    </div>
</div>
<div class="row margin-top-5">
    <div class="col-md-4">
    </div>
    <div class="col-md-4">
    </div>
    <div class="col-md-4">
    </div>
</div>


<?php echo $o;?>

<div class="row">
    <div class="col-sm-12">
        <div class="well">
            <table class="table table-striped table-forum">
                <thead>
                    <tr>
                        <th colspan="2">
                            <i class="fa fa-comments"></i>&nbsp;
                            Bacheca di questo ordine
                            <button class="pull-right show_add_post btn btn-xs btn-info"><i class="fa fa-plus"></i> Aggiungi commento</button>
                        </th>
                    </tr>
                </thead>
                <tbody class="container_post">
                    <!-- add Post -->
                    <tr id="add_post" style="display: none;">
                        <td class="text-center" style="width: 12%;">
                            <div class="push-bit">
                                <strong>Nuovo commento</strong>
                            </div>
                        </td>
                        <td>
                            <div id="forumPost"></div>
                            <span class="pull-left"></span>
                            <span class="pull-left sto_caricando margin-top-10 animated bounceIn" style="display:none;"><i class="fa fa-spin fa-gear"></i> sto caricando la foto.... attendi pazientemente :)</span>
                            <button class="btn btn-primary margin-top-10 pull-right save_post" data-id_ordine="<?php echo $O->id_ordini; ?>" data-id_ditta="<?php echo $O->id_ditte; ?>">Pubblica</button>
                        </td>
                    </tr>
                    <!-- end  add Post -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html("scheda_ordine",$page_title); ?>
        </article>
    </div>
</section>
<script type="text/javascript">

    pageSetUp();


    var pagefunction = function() {

        //-------------------------HELP
        //document.title = '<?php echo "ReteDES.it :: ".$O->descrizione_ordini;?>';
        <?php echo help_render_js("scheda_ordine"); ?>
        <?php if(_USER_ADDTOCALENDAR){ ?>
            addtocalendar.load();
        <?php } ?>
        //-------------------------HELP
        
        
        //BACHECA------------------------------
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

        $('#forumPost').summernote({
            height : 180,
            focus : false,
            tabsize : 2,
            toolbar: [
                //[groupname, [button list]]
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'picture']]
              ],
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
                }else{
                    ko(data.msg);
                }
            });

        }

        $(document).on("click",".show_add_post", function(e){
            e.preventDefault();
            $("#add_post").show();
        })

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
        $(document).off("click",".save_post");
        $(document).on("click",".save_post", function(e){
            e.preventDefault();
            //var sHTML = $('#forumPost').code();
            var sHTML = $('#forumPost').summernote('code');
            var id_ordine = $(this).data("id_ordine");
            var id_ditta = $(this).data("id_ditta");
            console.log("save" + sHTML);
            $.ajax({
              type: "POST",
              url: "ajax_rd4/bacheca/_act.php",
              dataType: 'json',
              data: {act: "save_post", sHTML :sHTML, id_ordine:id_ordine, id_ditta:id_ditta },
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                    $('.post_item').remove();
                    $('#forumPost').summernote('code');
                    $("#add_post").fadeOut();
                    carica_post(0,<?php echo $O->id_ordini; ?>,0,0,1);

                }else{
                    ko(data.msg);
                }
            });
        })

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
                        $($t).parents('tr').css('opacity', '0.4');
                    }else{
                        $($t).parents('tr').css('opacity', '1');
                    }
                }else{
                    ko(data.msg);
                }
            });
        })

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
                    ['insert', ['link', 'picture']]
                  ],
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
            $('.messaggio[data-id_post="'+id_post+'"]').next('div').append('<button class="btn btn-primary pull-right margin-top-5 save_edited_post" data-id_post="'+id_post+'"> Salva le modifiche</button>');

        })
        $(document).off("click",".save_edited_post");
        $(document).on("click",".save_edited_post", function(e){
            var $t=$(this);
            var id_post=$(this).data("id_post");
            //var sHTML = $('.messaggio[data-id_post="'+id_post+'"]').code();
            var sHTML = $('.messaggio[data-id_post="'+id_post+'"]').summernote('code');
            console.log("save edited " + id_post);
            $.ajax({
              type: "POST",
              url: "ajax_rd4/bacheca/_act.php",
              dataType: 'json',
              data: {act: "save_edited_post", id_post:id_post, sHTML:sHTML},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                    ok("fatto");
                    $('.messaggio[data-id_post="'+id_post+'"]').next('div').remove();
                    $('.messaggio[data-id_post="'+id_post+'"]').empty().html(data.msg).fadeIn();
                }else{
                    ko(data.msg);
                }
            });
        })
        //BACHECA------------------------------

        $('#do_richiesta_info').click(function(e){
            e.preventDefault();
            $('#modal_richiesta_info').modal('hide')
            $.blockUI({ message: null });
            var messaggio = $('#inputEmail3').val().replace(/\r\n|\r|\n/g,"<br />");
           
            
            
            $.ajax({
              type: "POST",
              url: "ajax_rd4/ordini/_act.php",
              dataType: 'json',
              data: {act: "do_richiesta_info", messaggio:messaggio, id_ordine:<?php echo $O->id_ordini; ?>},
              context: document.body
            }).done(function(data) {
                $.unblockUI();
                if(data.result=="OK"){
                        $('#inputEmail3').val('');
                        ok(data.msg);
                }else{
                    ko(data.msg);
                }

            });


        })

        $('#go_gestisci').click(function(){
            container = $('#content');
            var $this   = $(this)
            var id = $this.data('id_ordine');
            loadURL('ajax_rd4/ordini/edit.php?id='+id,container);
        })
        $("#offri_aiuto").click(function(e) {

            $.SmartMessageBox({
                title : "Offri il tuo aiuto ?",
                content : "Descrivi (brevemente) in cosa potrai essere d'aiuto. La richiesta verrà inviata al referente.",
                buttons : "[Annulla][OK]",
                input : "text",
                placeholder : "Scrivi qua...",
                inputValue: ''
            }, function(ButtonPress, Value) {

                if(ButtonPress=="OK"){
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/ordini/_act.php",
                          dataType: 'json',
                          data: {act: "offerta_aiuto", value:Value, id_ordine:<?php echo $O->id_ordini; ?>},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    ok(data.msg);}else{ko(data.msg);}

                        });
                }
            });

            e.preventDefault();
        });
        $("#diventa_referente").click(function(e) {

            $.SmartMessageBox({
                title : "Diventi referente per il tuo GAS ?",
                content : "In breve: farai da ponte tra il gestore dell'ordine ed il tuo gas. Leggi l'aiuto in fondo alla pagina per tutte le informazioni su cosa comporta questo incarico.",
                buttons : "[Annulla][OK]"
            }, function(ButtonPress, Value) {
                if(ButtonPress=="OK"){
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/ordini/_act.php",
                          dataType: 'json',
                          data: {act: "diventa_referente", id_ordine:<?php echo $O->id_ordini; ?>},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                ok(data.msg);
                            }else{
                                ko(data.msg);
                            }

                        });
                }
            });

            e.preventDefault();
        });


        //BACHECA------------------------------
        carica_post(0,<?php echo $O->id_ordini;; ?>,0,0,1);
        //BACHECA------------------------------
        
        <?php if($richiedi_info==1){?>
            $('#modal_richiesta_info').modal('show');
        <?php }?>
    }
    // end pagefunction

    loadScript("js/plugin/summernote/new_summernote.min.js", pagefunction);



</script>
