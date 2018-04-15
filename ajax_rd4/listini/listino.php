<?php
$page_title = "Listino";
$page_id ="listino";
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.listino.php");

$ui = new SmartUI;
$converter = new Encryption;

//var_dump($_POST, $_FILES);

$id_listino = CAST_TO_INT($_GET["id"]);
if($id_listino==0){
    $id_listino = CAST_TO_INT($_POST["id"]);
    if($id_listino==0){
        echo "id missing";die();
    }
}

$L = new listino($id_listino);

//AMAZON S3
$bucket = 'retedes';
$folder = 'public_rd4/note_listini/'.$L->id_listini.'/';

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


//VEDO SE l'utente corrente ha lisini multiditta
$stmt = $db->prepare("SELECT id_listini, descrizione_listini FROM  retegas_listini WHERE id_ditte=0 and id_utenti="._USER_ID);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$i=0;
foreach ($rows as $row){
    $i++;
    $o.= '<option value="'.$row["id_listini"].'">'.$row["id_listini"].' - '.$row["descrizione_listini"].'</option>';
}

if($i>0 AND (!$L->is_multiditta)){
    $user_ha_multiditta=true;
    $md='<div class="well well-sm"><h1><i class="fa fa-truck text-success"></i>&nbsp;<i class="fa fa-truck text-danger"></i>&nbsp;<i class="fa fa-truck text-warning"></i>&nbsp;Hai listini multiditta:</h1>
                       <p>Se vuoi inserire articoli in un tuo listino multiditta, selezionalo qua sotto, poi seleziona gli articoli che vuoi copiare e clicca su "trasferisci articoli".</p>
                       <div class="row">
                       <div class="col col-xs-6">
                       <select class="select2" id="select_multiditta">
                            '.$o.'
                       <select>
                       </div>
                       <div class="col col-xs-6">
                       <button id="aggiungi_multiditta" class="btn btn-success">TRASFERISCI ARTICOLI</button></div>
                       </div>
                       </div>';
}else{
    $user_ha_multiditta=false;
    $md='';
}

if(posso_gestire_listino($id_listino)){
    $proprietario="true";
    //$buttons[] ='<a href="javascript:void(0);"><i class="fa fa-unlock fa-2x fa-border text-success" rel="popover" data-placement="left" data-original-title="Permessi" data-content="Puoi lavorare su questo listino perchè ne sei il proprietario"></i></a>';
    $editable = "editable";
}else{
    $proprietario="false";
    $editable = "";

    //$buttons[] ='<a href="javascript:void(0);"><i class="fa fa-lock fa-2x fa-border text-danger" rel="popover" data-placement="left" data-original-title="Permessi" data-content="Non sei l\'autore di questo listino."></i></a>';
}




$stmt = $db->prepare("SELECT * FROM  retegas_listini WHERE id_listini = :id_listino LIMIT 1;");
$stmt->bindParam(':id_listino', $id_listino, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$L->is_completo){
//if((conv_date_from_db($row["data_valido"]))=="00/00/0000" OR ($row["id_tipologie"]==0)){
    $incompleto=true;
}else{
    $incompleto=false;
}

//---------------DELETED
if($row["is_deleted"]==1){
    echo rd4_go_back("Listino inesistente");
    die();    
}


//---------------DELETED


if($row["tipo_listino"]==1){
    $tipo="Magazzino";
}else{
    $tipo="Normale";
}
if($row["is_privato"]==1){
    $privato="Privato";
}else{
    $privato="Pubblico";
}
if($row["is_multiditta"]==1){
    $multiditta="Multiditta";
}else{
    $multiditta="Monoditta";
}


$stmt = $db->prepare("SELECT U.fullname,G.descrizione_gas  FROM  maaking_users U inner join retegas_gas G on G.id_gas=U.id_gas WHERE userid = :userid;");
$stmt->bindParam(':userid', $row["id_utenti"], PDO::PARAM_INT);
$stmt->execute();
$p = $stmt->fetch(PDO::FETCH_ASSOC);
$fullname_p = $p["fullname"];
$gas_p = $p["descrizione_gas"];


$stmt = $db->prepare("SELECT *  FROM  retegas_ditte WHERE id_ditte = :id_ditte;");
$stmt->bindParam(':id_ditte', $row["id_ditte"], PDO::PARAM_INT);
$stmt->execute();
$d = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $db->prepare("SELECT count(*) as count FROM  retegas_articoli WHERE id_listini = :id_listino");
$stmt->bindParam(':id_listino', $id_listino, PDO::PARAM_INT);
$stmt->execute();
$a = $stmt->fetch(PDO::FETCH_ASSOC);
$n_articoli = $a["count"];

//Ordini aperti ora
$stmt = $db->prepare("SELECT count(*) as count FROM  retegas_ordini WHERE id_listini = :id_listino AND data_apertura<NOW() and data_chiusura>NOW() LIMIT 1;");
$stmt->bindParam(':id_listino', $id_listino, PDO::PARAM_INT);
$stmt->execute();
$r = $stmt->fetch(PDO::FETCH_ASSOC);
$ordini_aperti = $r["count"];

$stmt = $db->prepare("SELECT count(*) as count FROM  retegas_ordini WHERE id_listini = :id_listino AND data_apertura>NOW() LIMIT 1;");
$stmt->bindParam(':id_listino', $id_listino, PDO::PARAM_INT);
$stmt->execute();
$r = $stmt->fetch(PDO::FETCH_ASSOC);
$ordini_futuri = $r["count"];

$stmt = $db->prepare("SELECT count(*) as count FROM  retegas_ordini WHERE id_listini = :id_listino AND data_chiusura<NOW() LIMIT 1;");
$stmt->bindParam(':id_listino', $id_listino, PDO::PARAM_INT);
$stmt->execute();
$r = $stmt->fetch(PDO::FETCH_ASSOC);
$ordini_chiusi = $r["count"];

$stmt = $db->prepare("SELECT count(id_utente) as count FROM  retegas_ordini WHERE id_listini = :id_listino  GROUP BY id_utente LIMIT 1;");
$stmt->bindParam(':id_listino', $id_listino, PDO::PARAM_INT);
$stmt->execute();
$r = $stmt->fetch(PDO::FETCH_ASSOC);
$numero_gestori = $r["count"];

$stmt = $db->prepare("SELECT count(id_utente) as count FROM  retegas_ordini WHERE id_listini = :id_listino  GROUP BY id_utente LIMIT 1;");
$stmt->bindParam(':id_listino', $id_listino, PDO::PARAM_INT);
$stmt->execute();
$r = $stmt->fetch(PDO::FETCH_ASSOC);
$numero_gas = $r["count"];

if($incompleto){
    $alert_incompleto= '<i class="fa fa-warning fa-2x text-danger"></i> ';
    $multiditta='<p class="font-lg '.$editable.'_multiditta" id="is_multiditta" data-type="select" data-pk="'.$L->id_listini.'" data-value="'.$L->is_multiditta.'">'.$multiditta.'</p>';
    $nuovo_ordine ='';
}else{
    $multiditta='<p class="font-lg '.$editable.'_multiditta" id="is_multiditta" data-type="select" data-pk="'.$L->id_listini.'" data-value="'.$L->is_multiditta.'">'.$multiditta.'</p>';
    $nuovo_ordine ='';
}

$rows = $L->lista_referenti_extra();
if(count($rows)>0){
    foreach($rows as $row){
        $op2='';
        if($row["valore_int"]==0){
           if($L->id_utenti==_USER_ID){
                $op1='<div class="btn-group pull-right">
                                    <a class="btn btn-xs btn-success attiva_extra" href="javascript:void(0);" data-id_listini="'.$L->id_listini.'" data-id_user="'.$row["id_user"].'"><i class="fa fa-check"></i> attiva</a>
                                    <a class="btn btn-xs btn-danger elimina_extra" href="javascript:void(0);" data-id_listini="'.$L->id_listini.'" data-id_user="'.$row["id_user"].'"><i class="fa fa-times"></i> elimina</a>
                                </div>';
                $op2='<div class="btn-group pull-right">
                                    <a class="btn btn-xs btn-danger elimina_extra" href="javascript:void(0);" data-id_listini="'.$L->id_listini.'" data-id_user="'.$row["id_user"].'"><i class="fa fa-times"></i></a>
                                </div>';
           }else{
                $op1='';
                $op2='';
           }

           $gestori_extra_attesa .= '<li>
                            <span class="read">
                                <a href="javascript:void(0);" class="msg">
                                    <img src="'.src_user($row["id_user"]).'" alt="" class="margin-top-5" width="32" height="32" />
                                    <span class="subject"><strong>'.$row["fullname"].'</strong></span>
                                    <span class="subject font-xs">'.$row["descrizione_gas"].'</span>
                                </a>
                                '.$op1.'
                            </span>
                        </li>';
       }else{
        if($L->id_utenti==_USER_ID){
            $op2='<div class="btn-group pull-right">
                                    <a class="btn btn-xs btn-danger elimina_extra" href="javascript:void(0);" data-id_listini="'.$L->id_listini.'" data-id_user="'.$row["id_user"].'"><i class="fa fa-times"></i></a>
                                </div>';
        }else{
            $op2='';
        }
        $gestori_extra_ok.= '<li>
                            <span class="read">
                                <a href="javascript:void(0);" class="msg">
                                    <img src="'.src_user($row["id_user"]).'" alt="" class="margin-top-5" width="32" height="32" />
                                    <span class="subject"><strong>'.$row["fullname"].'</strong></span>
                                    <span class="subject font-xs">'.$row["descrizione_gas"].'</span>
                                </a>
                                '.$op2.'
                            </span>
                        </li>';


       }
    }

}

    if(empty($gestori_extra_ok)){$gestori_extra_ok='<li>Nessuno.</li>';}
    if(empty($gestori_extra_attesa)){$gestori_extra_attesa='<li>Nessuno.</li>';}

$s = '  <div>
            <h3>Scheda</h3>
            <img id="img_listino" src="img_rd4/t_'.$L->id_tipologie.'_240.png" alt="" class="air air-top-right margin-top-5" width="80" height="80">
            <label for="descrizione_listini">Identificativo:</label>
            <p class="font-lg" id="id_listino" >#'.$L->id_listini.'</p>
            <label for="descrizione_listini">Descrizione</label>
            <p class="font-lg '.$editable.'" id="descrizione_listini" data-type="text" data-pk="'.$L->id_listini.'">'.$L->descrizione_listini.'</p>
            <nr>
            <label for="data_valido">'.$alert_incompleto.' Termine di validità</label>
            <p class="font-lg '.$editable.'" id="data_valido" data-type="date" data-pk="'.$L->id_listini.'" data-format="dd/mm/yyyy">'.$L->data_valido.'</p>
            <hr>
            <label for="tipo_listino" >Tipo listino</label>
            <p class="font-lg '.$editable.'_tipo" id="tipo_listino" data-type="select" data-pk="'.$L->id_listini.'" data-value="'.$L->tipo_listino.'">'.$tipo.'</p>
            '.$multiditta.'
            <hr>
            <label for="tipo_listino">Pubblico / Privato</label>
            <p class="font-lg '.$editable.'_privato" id="is_privato" data-type="select" data-pk="'.$L->id_listini.'" data-value="'.$L->is_private.'">'.$privato.'</p>
            <hr>
            <label for="id_tipologie">'.$alert_incompleto.' Categoria</label>
            <p class="font-lg '.$editable.'_tipologia" id="id_tipologie" data-type="select" data-pk="'.$L->id_listini.'" data-value="'.$L->id_tipologie.'">'.$L->descrizione_tipologia.'</p>
            <hr>
            <div class="row padding-10">
            <h4>Gestori extra in attesa:</h4>
            <ul class="list-unstyled">'.$gestori_extra_attesa.'</ul></div>
            <h4>Gestori extra:</h4>
            <ul class="list-unstyled">'.$gestori_extra_ok.'</ul></div>
           </div>';


$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>true,
                    "deletebutton"=>false,
                    "colorbutton"=>true);
$wg_listino = $ui->create_widget($options);
$wg_listino->id = "wg_listino_scheda";
$wg_listino->body = array("content" => $s,"class" => "");
$wg_listino->header = array(
    "title" => '<h2>Scheda listino</h2>',
    "icon" => 'fa fa-cubes'
    );


//-----------------------------------------------------ARTICOLI
$a = '
        <div class="well well-sm well-light">
            <button rel="popover-hover" data-placement="bottom" data-original-title="Filtra" data-content="Filtra tutto il listino in base a cosa inserisci nei rispettivi campi. Clicca per far comparire la riga del filtro." class="btn btn-default btn-circle btn-lg" onclick="$(\'.ui-search-toolbar\').toggle();"><i class="fa fa-filter "></i></button>
        </div>
        <div id="jqgcontainer" style="height:360px;">
            <table id="jqgrid"></table>
            <div id="pjqgrid"></div>
        </div>
        <div class="margin-top-10 well well-sm well-light">
                <span>Usa le frecce a destra per ingrandire la tabella. Seleziona quante righe devono essere visualizzate per ogni pagina. Gestisci gli articoli dalla pagina apposita, cliccando su "gestisci articoli"; questa voce ti compare solo se hai i permessi per farlo.</span>
                <a id="aumenta_altezza" class="btn btn-circle btn-default pull-right"><i class="fa fa-arrow-down"></i></a>
                <a id="diminuisci_altezza" class="btn btn-circle btn-default pull-right"><i class="fa fa-arrow-up"></i></a>
                <span class="btn btn-circle btn-default pull-right" data-action="minifyMenu" style=""><i class="fa fa-arrow-left"></i></span>
                <div class="clearfix"></div>
        </div>

      ';

$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>true,
                    "deletebutton"=>false,
                    "colorbutton"=>true);
$wg_articoli = $ui->create_widget($options);
$wg_articoli->id = "wg_listino_articoli";
$wg_articoli->body = array("content" => $a,"class" => "no-padding");
$wg_articoli->header = array(
    "title" => '<h2>Articoli nel listino</h2>',
    "icon" => 'fa fa-cube'
    );
//OPERAZIONI -----------------------------------

if($proprietario=="true"){
    if(!$incompleto){
        $upload='<hr>
                <label>Importa articoli</label>
                <div class="btn-group pull-right" id="clona_group">
                    <span class="btn btn-default fileinput-button dz-clickable">
                        <i class="fa fa-spinner fa-spin hidden" id="loadingSpinner"></i>
                        <span>Carica...</span>
                    </span>
                </div>
                <div class="clearfix "></div>
                <div class="progress progress-micro margin-top-10">
                    <div class="progress-bar progress-bar-primary" role="progressbar" style="width: 0;" id="loadingprogress"></div>
                </div>';
    }else{
        $upload='';
    }
    if($n_articoli>0){
        if($ordini_aperti==0){
            $cancella =' <hr><a class="btn btn-danger btn-block" id="del_articoli">ELIMINA ARTICOLI</a>';
        }
    }else{
        $cancella ='<hr><a class="btn btn-danger btn-block" id="del_listino">ELIMINA LISTINO</a>';
    }


}else{
    $upload='';
    $cancella='';
    $aiuta_a_gestire='<hr><a class="btn btn-primary btn-block" id="aiuta_a_gestire">AIUTA A GESTIRE</a>';
}

if(posso_gestire_listino($L->id_listini)){
        $gestisci_articoli ='<hr><a class="btn btn-info btn-block" id="gestisci_articoli">GESTISCI ARTICOLI</a>';
        $visualizza_log ='<hr><a class="btn btn-default btn-block" id="visualizza_log">VISUALIZZA LOG</a>';

  }


if($n_articoli>0){

    $esporta =' <hr class="margin-top-5">
        <label for="exp_group">Esporta questo listino:</label>
        <div class="btn-group pull-right" id="exp_group">
                <a class="btn btn-default" id="export_listino_HTM">HTML</a>
                <a class="btn btn-default" id="export_listino_CSV">CSV</a>
                <div class="btn-group">
                            <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                EXCEL <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="javascript:void(0);" id="export_listino_XLS">XLS (Excel5)</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" id="export_listino_XLSX">XLSX (Excel2007)</a>
                                </li>
                            </ul>
                        </div>
       </div>';
    $clona='<hr><a class="btn btn-success btn-block" id="clona_listino">CLONA LISTINO</a>';
    $nuovo_ordine='<hr>
                    <h4>APRI L\'ORDINE</h4><br>
                    <form class="smart-form">
                        <section>
                            <label class="input">
                                <input class="input-sm" name="nomeordine" value="'.$L->descrizione_listini.'" id="nomeordine">
                            </label>
                        </section>
                    </form>
                    <a class="btn btn-default btn-block" id="ordine_nuovo">OK</a>';
}else{
    $esporta ='';
    $clona='';
    $nuovo_ordine='';
}





$o = ' <h3>Operatività</h3>
       '.$esporta.'
       '.$upload.'
       '.$clona.'

       '.$cancella.'
       '.$gestisci_articoli.'
       '.$visualizza_log.'
       '.$aiuta_a_gestire.'
       '.$nuovo_ordine.'
       <hr>
';



//INCOMPLETO

if($incompleto){
    $inco = '<div class="alert alert-danger fade in"><h3>Questo listino è ancora incompleto.</h3>
    <p>Occorre compilarne tutti i campi per poterci aggiungere articoli.</p>
    <p>Una volta fatto, ricarica la pagina per accedere alle altre funzioni.</p></div>';
}else{
    $inco = '';
}

$L->lista_referenti_extra();

//SE C'E' UN ORDINE APERTO O CHIUSO NON CONVALIDATO
$rowOs = $L->lista_ordini_che_bloccano_eliminazioni();
$noie=0;

foreach($rowOs as $rowO){
    $oie.='<p>#'.$rowO["id_ordini"].' '.$rowO["descrizione_ordini"].'</p>';
    $noie++;
}
if($noie==0){
    $oie='';
}else{
    if(_USER_PERMISSIONS & perm::puo_gestire_retegas){
        $oie='<div class="panel panel-red padding-10"><h3>Ordini che bloccano il listino:</h3>'.$oie.'</div>';
    }else{
        if(posso_gestire_listino($L->id_listini)){
            $buttons[]='<a class="btn btn-link" href="#ajax_rd4/listini/listini_log-php?id='.$L->id_listini.'"><i class="fa fa-list"></i> LOG Listino</a>';
            $oie='<p class="alert alert-danger">Ci sono <strong>'.$noie.'</strong> ordini aperti o chiusi ma non confermati che limitano l\'operatività su questo listino.</p>';
        }else{
            $oie='';
        }
    }

}

//NAVBAR
//$title='<i class="fa fa-cubes fa-2x pull-left"></i> '.$row["descrizione_listini"].'<br><small class="note"></small>';

if(posso_gestire_listino($L->id_listini)){

    $rows = $L->lista_segnalazioni_listino();
    foreach($rows as $row){
        if($row["is_hidden"]==0){
            $lsl .='<li class="list-item"><i class="fa fa-times text-danger hide_segnalazione" data-id="'.$row["id_segnalazione"].'" style="cursor:pointer;"></i> '.$row["testo_segnalazione"].' <span class="note">di '.$row["fullname_segnalante"].' del '.conv_date_from_db($row["data_segnalazione"]).'</a></li>';    
        }
    }    
    if($lsl<>""){
        $lsl='
                <div class="well well-sm margin-top-10">
                <h1>Segnalazioni per questo listino:</h1>
                <ul class="list-unstyled">
                    '.$lsl.'
                </ul>
                </div>';
    }
    
}else{
    $rows = $L->lista_segnalazioni_listino();
    foreach($rows as $row){
        if($row["id_segnalante"]==_USER_ID){
            $lsl .='<li class="list-item"><i class="fa fa-bullhorn text-success"></i> '.$row["testo_segnalazione"].'  <span class="note">del '.conv_date_from_db($row["data_segnalazione"]).'</span></li>';    
        }
    }    
    
    if($lsl<>""){
        $lsl='
                <div class="well well-sm margin-top-10">
                <h1>Hai fatto queste segnalazioni:</h1>
                <ul class="list-unstyled">
                    '.$lsl.'
                </ul>
                </div>';
    }    
    
}    


?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.1/summernote.css" rel="stylesheet">

<?php echo $L->navbar_listino($buttons); ?>
<?php echo $lsl; ?>
<div class="alert alert-info"><i class="fa fa-2x fa-bullhorn"></i> Qualcosa non quadra?&nbsp; <span style="cursor:pointer" data-toggle="modal" data-target="#modal_segnalazione_listino" id="chiedi_info_button"><strong>Segnalalo ai gestori!</strong></span></div>
<hr>

<?php echo $inco; ?>
<?php echo $oie; ?>
<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        </article>
    </div>
    <div class="row">
        <!-- PRIMA COLONNA-->
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <div class="well padding-10"><?php echo $o;?></div>
            <?php //echo $wg_articoli_oper->print_html(); ?>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <div class="well padding-10">
                <?php if(posso_gestire_listino($L->id_listini)){echo $s;} ?>
            </div>
            <?php //if(posso_gestire_listino($L->id_listini)){echo $wg_listino->print_html();} ?>
        </div>

    </div>
    <hr>

    <div class="row">
        <!-- NOTE LISTINO-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                 <label for="summernote">Note di questo listino</label>
                <div id="summernote"><?php echo $L->note_listino; ?></div>
                <button class="btn btn-primary pull-right margin-top-10" id="go_note_listino">Salva le note</button>
                <div class="clearfix"></div>
        </article>
    </div>

    <hr>

    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo $md;?>
            <?php echo $wg_articoli->print_html(); ?>
            <?php echo help_render_html($page_id,$page_title); ?>

        </article>


    </div>
</section>
<!-- Dynamic Modal -->
                        <div class="modal fade" id="remoteModalImport" tabindex="-1" role="dialog" aria-labelledby="remoteModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">

                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="remoteModalConfirm" tabindex="-2" role="dialog" aria-labelledby="remoteModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content" >
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel">Caricamento effettuato: <span id="remoteModalTitle"></span></h4>
                                </div>
                                <div class="modal-body" id="remoteModalConfirmContent">

                                </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="modal_segnalazione_listino" tabindex="-1" role="dialog" aria-labelledby="richiesta_info_Label">
                              <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel">Segnalazione sul listino #<?php echo $id_listino; ?></h4>

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
                                          <button type="submit" class="btn btn-default" id="do_segnalazione_listino">Invia</button>
                                          <p class="note margin-top-10">Cliccando su "invia" verrà inoltrata una mail ai gestori del listino e al gestore del GAS di appartenenza;</p>
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
<!-- /.modal -->

<script type="text/javascript">


    pageSetUp();


    var dz;
    var myDropZone;
    var file_code;
    var ext;

    var pagefunction = function(){

        <?php if(!$incompleto){ ?>
        loadScript("js/plugin/jqgrid/jquery.jqGrid.min.js", run_jqgrid_function);
        <?php } ?>

        <?php if($proprietario=="true" AND !$incompleto){ ?>
        var  initDropzone = function (){
                //-----------------------------------------------DROPZONE DEMO
                try{Dropzone.autoDiscover = false;}catch(e){}


                 try{

                 console.log("initDropzone");
                 myDropZone = new Dropzone(document.body, { // Make the whole body a dropzone
                  maxFiles:1,
                  url: "upload.php", // Set the url
                  //thumbnailWidth: 80,
                  //thumbnailHeight: 80,
                  //parallelUploads: 20,
                  //previewTemplate: previewTemplate,
                  //autoQueue: false, // Make sure the files aren't queued until manually added
                  //previewsContainer: "#previews", // Define the container to display the previews
                  clickable: ".fileinput-button", // Define the element that should be used as click trigger to select files.
                  success: function(file,response){
                        console.log(file);
                        console.log(response);
                        var data = JSON.stringify(eval("(" + response + ")"));
                        var json = JSON.parse(data);
                        console.log(json.result);
                        $("#loadingprogress").width( 0 );
                        $("#loadingSpinner").addClass('hidden');
                        this.removeAllFiles();

                        if(json.result==="OK"){
                                   //ok(json.msg);
                                   $('#remoteModalConfirm').modal({ show: false});
                                   $('#remoteModalConfirm').modal('show');
                                   $('#remoteModalConfirmContent').html(json.msg);
                                   $('#remoteModalTitle').html(json.title);
                                   file_code = json.file;
                                   ext =json.ext;
                                   console.log("file" + file_code + "ext :" + ext);
                                   return true;
                               }else{
                                    ko(json.msg);
                                    return false;
                        }


                }
                 });
               myDropZone.on('sending', function(file, xhr, formData){
                    formData.append('id_listino', '<?php echo $id_listino?>');
                    formData.append('act', 'listino');
                    $("#loadingSpinner").removeClass('hidden');

                });
                myDropZone.on('uploadprogress', function(file, progress ){
                    console.log(progress );
                    $("#loadingprogress").width( progress + '%' );
                });
            }catch(err){
                console.log("dropZone already attached..." + err);
                location.reload();
            }
            //-----------------------------------------------DROPZONE DEMO
        }
        loadScript("js/plugin/dropzone/dropzone.min.js",initDropzone);
        <?php }?>

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

        function run_jqgrid_function() {
            var rowid;
            var lastSel;
            var is_editable = false;

            jQuery("#jqgrid").jqGrid({
               url:'ajax_rd4/listini/inc/articoli.php?id_listino=<?php echo $id_listino?>&s=en',
            datatype: "json",
               colNames:[   'Codice',
                            'Descrizione',
                            'Prezzo',

                            'U.M',
                            'Misura',
                            'ingombro',

                            'Qta S.',
                            'Qta M.',
                            'Note',

                            'U',
                            'T1',
                            'T2',
                            'T3'


                            ],
               colModel:[
                   {name:'codice',index:'codice', width:60,editable:is_editable, },
                   {name:'descrizione_articoli',index:'descrizione_articoli', width:150, align:"left",editable:is_editable},
                   {name:'prezzo',index:'prezzo', width:50,align:"right",editable:is_editable,search:false},

                   {name:'u_misura',index:'u_misura', width:20,align:"right",editable:is_editable,search:false},
                   {name:'misura',index:'misura', width:30,align:"right",editable:is_editable,search:false},

                   {name:'ingombro',index:'ingombro', width:50, editable:is_editable,search:false,edittype:'textarea'},

                   {name:'qta_scatola',index:'qta_scatola', width:25, align:"center",editable:is_editable,search:false},
                   {name:'qta_minima',index:'qta_minima', width:25, align:"center",editable:is_editable,search:false},

                   {name:'articoli_note',index:'articoli_note', width:75, sortable:false,editable:is_editable,search:true,edittype:'textarea'},

                   {name:'articoli_unico',index:'articoli_unico', width:25, align:"center",editable:is_editable,search:false},
                   {name:'articoli_opz_1',index:'articoli_opz_1', width:50,align:"left",editable:is_editable},
                   {name:'articoli_opz_2',index:'articoli_opz_2', width:50,align:"left",editable:is_editable},
                   {name:'articoli_opz_3',index:'articoli_opz_3', width:50,align:"left",editable:is_editable},


               ],

               multiselect: true,
               onSelectRow:function(id){
                    var lastsel= jQuery('#jqgrid').jqGrid('getGridParam','selrow');
                    console.log(lastsel);
                    //$("#jqgrid").addRowData(rowid,data, position, lastsel);
               },

               rowNum:20,
               rowList:[20,50,500,5000],
               pager: '#pjqgrid',

               //sortname: 'id_articolo',
            viewrecords: true,
            //sortorder: "desc",
            editurl: "ajax_rd4/listini/inc/articoli.php?id_listino=<?php echo $id_listino?>",

            caption:"",
            gridComplete : function(){
                console.log("Grid Complete!");
                //------------------------------XEDITABLE INIT

            }

        });


        /*
        jQuery("#jqgrid").jqGrid('navGrid','#pjqgrid',{
                edit:false,
                add:false,
                del:false,
                search:false
        });
        */
        jQuery("#jqgrid").jqGrid('filterToolbar',{});


        $(window).on('resize.jqGrid', function() {
            console.log ("resizing ");

                jQuery("#jqgrid").jqGrid('setGridWidth', $("#jqgcontainer").width());
                jQuery("#jqgrid").jqGrid('setGridHeight', $("#jqgcontainer").height() - ($("#gbox_jqgrid").height() - $('#gbox_jqgrid .ui-jqgrid-bdiv').height()));

        });


            $('.ui-search-toolbar').hide();

            // remove classes
            $(".ui-jqgrid").removeClass("ui-widget ui-widget-content");
            $(".ui-jqgrid-view").children().removeClass("ui-widget-header ui-state-default");
            $(".ui-jqgrid-labels, .ui-search-toolbar").children().removeClass("ui-state-default ui-th-column ui-th-ltr");
            $(".ui-jqgrid-pager").removeClass("ui-state-default");
            $(".ui-jqgrid").removeClass("ui-widget-content");

            // add classes
            $(".ui-jqgrid-htable").addClass("table table-bordered table-hover");
            $(".ui-jqgrid-btable").addClass("table table-bordered table-striped");

            $(".ui-pg-div").removeClass().addClass("btn btn-sm btn-primary");
            $(".ui-icon.ui-icon-plus").removeClass().addClass("fa fa-plus");
            $(".ui-icon.ui-icon-pencil").removeClass().addClass("fa fa-pencil");
            $(".ui-icon.ui-icon-trash").removeClass().addClass("fa fa-trash-o").parent(".btn-primary").removeClass("btn-primary").addClass("btn-danger");
            $(".ui-icon.ui-icon-search").removeClass().addClass("fa fa-search");
            $(".ui-icon.ui-icon-refresh").removeClass().addClass("fa fa-refresh");
            $(".ui-icon.ui-icon-disk").removeClass().addClass("fa fa-save").parent(".btn-primary").removeClass("btn-primary").addClass("btn-success");
            $(".ui-icon.ui-icon-cancel").removeClass().addClass("fa fa-times").parent(".btn-primary").removeClass("btn-primary").addClass("btn-danger");

            $(".ui-icon.ui-icon-seek-prev").wrap("<div class='btn btn-sm btn-default'></div>");
            $(".ui-icon.ui-icon-seek-prev").removeClass().addClass("fa fa-backward");

            $(".ui-icon.ui-icon-seek-first").wrap("<div class='btn btn-sm btn-default'></div>");
            $(".ui-icon.ui-icon-seek-first").removeClass().addClass("fa fa-fast-backward");

            $(".ui-icon.ui-icon-seek-next").wrap("<div class='btn btn-sm btn-default'></div>");
            $(".ui-icon.ui-icon-seek-next").removeClass().addClass("fa fa-forward");

            $(".ui-icon.ui-icon-seek-end").wrap("<div class='btn btn-sm btn-default'></div>");
            $(".ui-icon.ui-icon-seek-end").removeClass().addClass("fa fa-fast-forward");
            $('#jqgrid_ilcancel').hide();
            $('#jqgrid_ilsave').hide();
            $('#jqgrid_iladd').hide();
            $('#jqgrid_iledit').hide();
            //$('#del_jqgrid').hide();


            $('#aumenta_altezza').click(function(){
                $('#jqgcontainer').height($('#jqgcontainer').height()+200);
                jQuery("#jqgrid").jqGrid('setGridHeight', $("#jqgcontainer").height() - ($("#gbox_jqgrid").height() - $('#gbox_jqgrid .ui-jqgrid-bdiv').height()));
            });
            $('#diminuisci_altezza').click(function(){
                if(($('#jqgcontainer').height()-200)>300){
                $('#jqgcontainer').height($('#jqgcontainer').height()-200);
                jQuery("#jqgrid").jqGrid('setGridHeight', $("#jqgcontainer").height() - ($("#gbox_jqgrid").height() - $('#gbox_jqgrid .ui-jqgrid-bdiv').height()));
                }
            });
            //resize to fit page size
                jQuery("#jqgrid").jqGrid('setGridWidth', $("#jqgcontainer").width());
                jQuery("#jqgrid").jqGrid('setGridHeight', $("#jqgcontainer").height() - ($("#gbox_jqgrid").height() - $('#gbox_jqgrid .ui-jqgrid-bdiv').height()));

            $("#aggiungi_multiditta").click(function(){
                var grid = jQuery("#jqgrid").jqGrid();
                var rowKey = grid.getGridParam("selrow");
                if (!rowKey)
                    ko("Nessun articolo selezionato");
                else {
                    var selectedIDs = grid.getGridParam("selarrrow");
                    var listino_multiditta = $("#select_multiditta").val();
                    console.log(listino_multiditta + " " + selectedIDs);
                    $.ajax({
                      type: "POST",
                      url: "ajax_rd4/listini/_act.php",
                      dataType: 'json',
                      data: {act: "aggiungi_multiditta", selectedIDs : selectedIDs, id_listino:listino_multiditta},
                      context: document.body
                    }).done(function(data) {
                        if(data.result=="OK"){
                            ok(data.msg);
                        }else{
                            ko(data.msg)
                        ;}
                    });
                }
            })
        } // end jqgrid init


        //-------------------------HELP
        <?php echo help_render_js($page_id); ?>
        //-------------------------HELP





        $(".editable").editable({
                                url: 'ajax_rd4/listini/_act.php',
                                ajaxOptions: { dataType: 'json' },
                                success: function(response, newValue) {
                                    console.log(response);
                                    if(response.result == 'KO'){
                                        return response.msg;

                                    }else{
                                        ok(response.msg);

                                    }
                                }
                            });
       $(".editable_tipo").editable({
                                url: 'ajax_rd4/listini/_act.php',
                                source: [
                                  {value: 0, text: 'Normale'},
                                  {value: 1, text: 'Magazzino', disabled: true}
                               ],
                                ajaxOptions: { dataType: 'json' },
                                success: function(response, newValue) {
                                    console.log(response);
                                    if(response.result == 'KO'){
                                        return response.msg;

                                    }else{
                                        ok(response.msg);

                                    }
                                }
                            });
       $(".editable_privato").editable({
                                url: 'ajax_rd4/listini/_act.php',
                                source: [
                                  {value: 0, text: 'Pubblico'},
                                  {value: 1, text: 'Privato'}
                               ],
                                ajaxOptions: { dataType: 'json' },
                                success: function(response, newValue) {
                                    console.log(response);
                                    if(response.result == 'KO'){
                                        return response.msg;

                                    }else{
                                        ok(response.msg);

                                    }
                                }
                            });
      $(".editable_multiditta").editable({
                                url: 'ajax_rd4/listini/_act.php',
                                source: [
                                  {value: 0, text: 'Monoditta', disabled: true},
                                  {value: 1, text: 'Multiditta'}
                               ],
                                ajaxOptions: { dataType: 'json' },
                                success: function(response, newValue) {
                                    console.log(response);
                                    if(response.result == 'KO'){
                                        return response.msg;

                                    }else{
                                        ok(response.msg);

                                    }
                                }
                            });
      $(".editable_tipologia").editable({
                                url: 'ajax_rd4/listini/_act.php',
                                source: [
                                  {value: 1, text: 'Non definito'},
                                  {value: 5, text: 'Alimentari (Generi vari)'},
                                  {value: 2, text: 'Pasta, Riso, Farine'},
                                  {value: 3, text: 'Frutta e verdura'},
                                  {value: 4, text: 'Carne e Pesce'},
                                  {value: 12, text: 'Vino o Birra'},
                                  {value: 7, text: 'Miele e dolciumi'},
                                  {value: 17, text: 'Formaggi e latticini'},
                                  {value: 11, text: 'Abbigliamento'},
                                  {value: 8, text: 'Intimo'},
                                  {value: 9, text: 'Calzature'},
                                  {value: 10, text: 'Accessori'},
                                  {value: 13, text: 'Cosmesi e Igiene Personale'},
                                  {value: 6, text: 'Libri - Riviste'},
                                  {value: 14, text: 'Elettronica (Accessori)'},
                                  {value: 15, text: 'Elettrodomestici'},
                                  {value: 16, text: 'Informatica'}

                               ],
                                ajaxOptions: { dataType: 'json' },
                                success: function(response, newValue) {
                                    console.log(response);
                                    if(response.result == 'KO'){
                                        return response.msg;
                                    }else{
                                        ok(response.msg);
                                        $('#img_listino').attr("src", "img_rd4/t_"+newValue+"_240.png");
                                        console.log(newValue);
                                    }
                                }
                            });
    $("#export_listino_CSV").click(function(e) {
        open("GET","<?php echo APP_URL; ?>/ajax_rd4/listini/_act.php",{"act":"exp_csv","id":<?php echo $id_listino?>},"_BLANK");
    });
    $("#export_listino_XLS").click(function(e) {
         open("GET","<?php echo APP_URL; ?>/ajax_rd4/listini/_act.php",{"act":"exp_xls","t":"XLS","id":<?php echo $id_listino?>},"_BLANK");
    });
    $("#export_listino_XLSX").click(function(e) {
        open("GET","<?php echo APP_URL; ?>/ajax_rd4/listini/_act.php",{"act":"exp_xls","t":"XLSX","id":<?php echo $id_listino?>},"_BLANK");
    });
    $("#export_listino_HTM").click(function(e) {
        open("POST","<?php echo APP_URL; ?>/ajax_rd4/listini/_act.php",{"act":"exp_htm","id":<?php echo $id_listino?>},"_BLANK");
    });
    $("#gestisci_articoli").click(function(e) {
        location.replace('<?php echo APP_URL; ?>/#ajax_rd4/listini/edit.php?id=<?php echo $id_listino?>');
    });
    $("#visualizza_log").click(function(e) {
        location.replace('<?php echo APP_URL; ?>/#ajax_rd4/listini/listino_log.php?id=<?php echo $id_listino?>');
    });
    $("#clona_listino").click(function(e) {
    $.SmartMessageBox({
                title : "Cloni questo listino ?",
                content : "Diventerai il proprietario della copia clonata e potrai modificarla a tuo piacimento.<br>Una volta clonato, verrai rediretto nella scheda del tuo nuovo listino.",
                buttons : "[Esci][Clona]",
                input : "text",
                placeholder : "<?php echo $descrizione_listini ?> (clone)",
                inputValue: 'Listino <?php echo $descrizione_listini ?> (clone)',
            }, function(ButtonPress, Value) {

                if(ButtonPress=="Clona"){
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/listini/_act.php",
                          dataType: 'json',
                          data: {act: "clona_listino", value : Value, id : <?php echo $id_listino?>},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                ok(data.msg);
                                console.log(data.id);
                                setInterval(function(){
                                        location.replace("<?php echo APP_URL; ?>/#ajax_rd4/listini/listino.php?id="+data.id);
                                        clearInterval();
                                }, 3000);

                            }else{
                                ko(data.msg)
                            ;}

                        });
                }
            });
    });
    $("#del_articoli").click(function(e) {
    $.SmartMessageBox({
                title : "Elimini tutti gli articoli di questo listino?",
                content : "Con questa operazione eliminerai tutti gli articoli di questo listino.<br>Se ci sono ordini chiusi con questo listino gli utenti conserveranno comunque tutti i loro dati.<br>Prima di eliminarli puoi farne una copia usando la funzione <b>esporta</b>",
                buttons : "[Esci][Elimina]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="Elimina"){
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/listini/_act.php",
                          dataType: 'json',
                          data: {act: "del_articoli", id : <?php echo $id_listino?>},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                ok(data.msg);
                                $('#jqgrid').trigger( 'reloadGrid' );
                            }else{
                                ko(data.msg)
                            ;}

                        });
                }
            });
    });
    $("#del_listino").click(function(e) {
    $.SmartMessageBox({
                title : "Elimini questo listino?",
                content : "Con questa operazione eliminerai il listino.",
                buttons : "[Esci][Elimina]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="Elimina"){
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/listini/_act.php",
                          dataType: 'json',
                          data: {act: "del_listino", id : <?php echo $id_listino?>},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                ok(data.msg);
                                location.replace("<?php echo APP_URL; ?>/#ajax_rd4/listini/miei.php");
                            }else{
                                ko(data.msg)
                            ;}

                        });
                }
            });
    });

    $("#ordine_nuovo").click(function(e){
    // ORDINE NORMALE
        var nomeordine = $("#nomeordine").val();
        var noteordine = $('#summernote').summernote('code');
        $.SmartMessageBox({
            title : "Stai per far partire un nuovo ordine!",
            content : "Questo ordine è in coda e si aprirà in automatico tra 2 ore. Se vuoi fare delle modifiche o eliminarlo vai nella pagina I MIEI ORDINI",
            buttons : '[OK][ANNULLA]'
        }, function(ButtonPressed) {
            if (ButtonPressed === "OK") {
                $.ajax({
                    type: "POST",
                    url: "ajax_rd4/ordini/_act.php",
                    dataType: 'json',
                    data: {act: "nuovo_ordine", idlistino:<?php echo $id_listino;?>, nomeordine:nomeordine, quantigiorni:7, noteordine:noteordine},
                    context: document.body
                    }).done(function(data) {
                        console.log(data.result + ' - ' + data.msg);
                        if(data.result=="OK"){
                            ok(data.msg);
                            window.setTimeout(function(){
                                window.location.href = "#ajax_rd4/ordini/edit.php?id="+data.id;
                            }, 5000);
                        }else{
                            ko(data.msg);
                        }
                });

            }
        });
        // ORDINE NORMALE

    });

    $('body').on('hidden.bs.modal', '.modal', function () {
          $(this).removeData('bs.modal');
          //dz.destroy();
          //dz=null;
    });

    $('body').on('shown.bs.modal','.modal', function(e) {
            console.log("Modal opened");
            dz=null;
            //initDropzone();
    });
    $('body').on('click','#go_controlla', function(e) {
            console.log("go_controlla");
            $('#remoteModalConfirm').modal('hide');
            try{myDropZone.destroy();}catch(e){console.log("Destroy failed")}
    });
    $('body').on('click','#go_esci', function(e) {
            console.log("go_esci");
            $('#remoteModalConfirm').modal('hide');
            //try{myDropZone.destroy();}catch(e){console.log("Destroy failed")}
    });
    $('body').on('click','#go_upload', function(e) {
            console.log("do_upload");
            console.log("file: " + file_code + " ext: " + ext);
            $.ajax({
                          type: "GET",
                          url: "ajax_rd4/listini/upload_act.php?id=<?php echo $id_listino?>&f="+file_code+"&e="+ext+"&act=check&do=ins",
                          dataType: 'json',
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                ok(data.msg);
                            }else{
                                ko(data.msg);
                         ;}
                        $('#remoteModalConfirm').modal('hide');
                        $('#jqgrid').trigger( 'reloadGrid' );
                        });
    });

    $('.attiva_extra').click(function(){
        $this = $(this);
        id_user = $this.data('id_user');
        //ok("Lis " + id_listino + " User " + id_user);
        $.ajax({
                          type: "POST",
                          url: "ajax_rd4/listini/_act.php",
                          dataType: 'json',
                          data: {act: "attiva_aiuta_a_gestire", id_user:id_user, id_listini:<?php echo $L->id_listini;?>},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    ok(data.msg);
                            }else{
                                    ko(data.msg)
                            ;}

                        });



    });

    $('.elimina_extra').click(function(e){
        $this = $(this);
        id_user = $this.data('id_user');
        $.ajax({
                          type: "POST",
                          url: "ajax_rd4/listini/_act.php",
                          dataType: 'json',
                          data: {act: "elimina_aiuta_a_gestire", id_user:id_user, id_listini:<?php echo $L->id_listini;?>},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    ok(data.msg);
                            }else{
                                    ko(data.msg)
                            ;}

                        });
    });

    $('#aiuta_a_gestire').click(function(e){
    $.SmartMessageBox({
                title : "Vuoi aiutare a gestire questo listino ?",
                content : "Scrivi un breve messaggio che verrà inviato all\'autore di questo listino, e se lo vorrà ti inserirà tra i suoi gestori.",
                buttons : "[Esci][Invia]",
                input : "text",
                placeholder : "msg",
                inputValue: '',
            }, function(ButtonPress, Value) {

                if(ButtonPress=="Invia"){
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/listini/_act.php",
                          dataType: 'json',
                          data: {act: "aiuta_a_gestire", value : Value, id:<?php echo $L->id_listini;?>},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    ok(data.msg);
                            }else{
                                ko(data.msg)
                            ;}

                        });
                }
            });

            e.preventDefault();
        })

        $('#summernote').summernote({
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
                            sendFile(file,editor,$editable,file.name,'#summernote');
                    });
                },
                onChange: function ($editable, sHtml) {
                  //console.log($editable, sHtml);
                  //$('#noteordine').val($editable);
                }
            }

        });

        //NOTE_LISTINO
        $('#go_note_listino').click(function(e){
            var value = $('#summernote').summernote('code');
            //var value = $('#summernote').code();
            $.ajax({
              type: "POST",
              url: "ajax_rd4/listini/_act.php",
              dataType: 'json',
              data: {act: "note_listino", pk: <?php echo $L->id_listini; ?>, value:value},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                        ok(data.msg);}else{ko(data.msg);}
                        //location.reload();
            });
        });
        //NOTE

        //RIMUOVI SEGNALAZIONE
                $(document).off('click','.hide_segnalazione');
                $(document).on('click','.hide_segnalazione', function(e){
                    var segnalazione = $(this);
                    id_segnalazione = $(this).data('id');
                    console.log("hide segnalazione " + id_segnalazione);
                    $.ajax({
                          type: 'POST',
                          url: 'ajax_rd4/segnalazioni/_act.php',
                          dataType: 'json',
                          data: {act: 'hide_segnalazione', id_segnalazione : id_segnalazione},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=='OK'){
                                ok('Segnalazione tolta.');
                            }else{
                                ko(data.msg)
                            ;}
                        });
                });
        
        //SEGNALAZIONE LISTINO
        $('#do_segnalazione_listino').click(function(e){
            e.preventDefault();
            $('#modal_segnalazione_listino').modal('hide')
            $.blockUI({ message: null });
            var messaggio = $('#inputEmail3').val().replace(/\r\n|\r|\n/g,"<br />");
            $.ajax({
              type: "POST",
              url: "ajax_rd4/segnalazioni/_act.php",
              dataType: 'json',
              data: {act: "do_segnalazione_listino", messaggio:messaggio, id_listino:<?php echo $id_listino; ?>},
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
        //SEGNALAZIONE LISTINO
        
    } // end pagefunction

        function loadSummerNote(){
            loadScript("js/plugin/summernote/new_summernote.min.js", pagefunction)
        }

        loadScript("js/plugin/jqgrid/grid.locale-en.min.js",
            loadScript("js/plugin/x-editable/x-editable.min.js", loadSummerNote ));





    </script>
