<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.ordine.php");

$ui = new SmartUI;
$page_title= "Compra articoli";
$page_id = "compra_articoli";
//CONTROLLI
$id_ordine = CAST_TO_INT($_GET["id"],0);
$O = new ordine($id_ordine);

if($O->is_nascosto_per_il_gas(_USER_ID_GAS)){
    echo rd4_go_back("Non è possibile comprare in questo ordine.");
    die();    
}

//GAS POTENZIALE PARTECIPANTE
$ok=false;
$rows = $O->lista_gas_potenziali_partecipanti();
foreach($rows as $row){
    if($row["id_gas"]==_USER_ID_GAS){
        $ok=true;
    }
}
if(!$ok){
    echo rd4_go_back("Questo ordine non è condiviso con il tuo GAS.");
    die();
}
//GAS POTENZIALE PARTECIPANTE


if($O->costo_trasporto>0){$msg_trasporto = '<div class="alert alert-info margin-top-5">Il referente prevede un costo di trasporto merce di circa <strong>'.round($O->costo_trasporto,2).' €</strong> che verrà ripartito tra i partecipanti.</div>';}
if($O->costo_gestione>0){$msg_gestione = '<div class="alert alert-info margin-top-5">Il referente prevede un costo di gestione ordine di circa '.$O->costo_gestione.' che verrà ripartito tra i partecipanti.</div>';}
if($O->costo_gas_referenza_v2(_USER_ID_GAS)>0){$msg_costo_gas = '<div class="alert alert-warning margin-top-5">Il referente del tuo GAS prevede un costo aggiuntivo di <strong>'.round($O->costo_gas_referenza_v2(_USER_ID_GAS),2).' &euro;</strong> che verrà ripartito tra i partecipanti.</div>';}
if($O->maggiorazione_percentuale_referenza_v2(_USER_ID_GAS)>0){$msg_maggiorazione_gas = '<div class="alert alert-warning margin-top-5">Il referente del tuo GAS prevede una maggiorazione del <strong>'.round($O->maggiorazione_percentuale_referenza_v2(_USER_ID_GAS),2).'% </strong> che sarà applicata alla tua spesa fatta, addotta a questa motivazione:<br><p>'.$O->motivo_maggiorazione_v2(_USER_ID_GAS).'</p></div>';}
if($O->metodo_scatole>0){$msg_scatole = '<div class="alert alert-info margin-top-5">Questo ordine ha il controllo delle scatole e avanzi a livello locale, il che significa che verranno indicati i quantitativi necessari a riempirle in base agli acquisti esclusivi del tuo GAS.</div>';}

if (DO_CHECK_USER_PRENOTAZIONE_ORDINE($O->id_ordini, _USER_ID)=="SI"){
    $prenotazione_alert ='<p class="margin-top-10 alert alert-danger"><b>ATTENZIONE:</b> per questo ordine hai attivato la "prenotazione", il che significa che puoi ordinare merce senza che ti venga toccato il tuo credito. Se però non confermi la tua prenotazione entro la sua data di scadenza, il tuo ordine verrà automaticamente annullato.</p>';
    $prenotazione_delete='<button class="btn btn-danger margin-top-10 margin-bottom-10" id="elimina_prenotazione"><i class="fa fa-trash"></i>  Elimina la prenotazione</button>  ';
    $prenotazione_conferma='<button class="btn btn-success margin-top-10 margin-bottom-10" id="conferma_prenotazione"><i class="fa fa-check"></i>  Conferma la prenotazione</button>  ';
    $prenotazione_attiva='';
}else{
    $prenotazione_alert ='';
    $prenotazione_delete='';
    $prenotazione_conferma='';
    $prenotazione_attiva='<button class="btn btn-primary margin-top-10 margin-bottom-10" id="attiva_prenotazione"><i class="fa fa-plus-o"></i>  Attiva la prenotazione</button>';
}

    //metodo inserimento
    $textbox_importi='<button class="btn btn-default margin-top-10 margin-bottom-10" id="toggle_textbox_importi"><i class="fa fa-recycle"></i> Tasto "+" oppure inserimento importi</button>  ';
    if(_USER_INSERIMENTO_TEXTBOX){
        $metodo_textbox='';
        $metodo_pulsante=' display:none; ';
    }else{
        $metodo_textbox=' display:none; ';
        $metodo_pulsante='';
    }


    //RAGGRUPPA ARTICOLI  e ditte
    $sql2 = "SELECT * from retegas_articoli where id_listini='".$O->id_listini."';";
    $stmt = $db->prepare($sql2);
    $stmt->execute();
    $rows2 = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $opts= array();
    $i=0;
    $i_d=0;
    foreach($rows2 as $row2){
        if(!in_array($row2["articoli_opz_1"],$opts)){
            $opts[]=$row2["articoli_opz_1"];
            $i++;
        }
        if(!in_array($row2["articoli_opz_2"],$opts)){
            $opts[]=$row2["articoli_opz_2"];
            $i++;
        }
        if(!in_array($row2["articoli_opz_3"],$opts)){
            $opts[]=$row2["articoli_opz_3"];
            $i++;
        }
        if(!in_array($row2["descrizione_ditta"],$opts_d)){
            $opts_d[]=$row2["descrizione_ditta"];
            $i_d++;
        }

    }
    //ordina, taglia e svuota
    asort($opts);
    $opts2 = array_map('trim', $opts);
    array_filter($opts2);

    asort($opts_d);
    $opts2_d = array_map('trim', $opts_d);
    array_filter($opts2_d);


    if($i>1){
        foreach ($opts2 as $opt){
            $o .= "<option>".trim($opt)."</option>";
        }
        $o = '  <label class="select">
            <select class="search form-control" type="search" data-column="1">

            <option value="" disabled selected>seleziona tra '.($i -1).' categorie</option>
            '.trim($o).'
            </select>
            </label>';

    }
    //RAGGRUPPA DITTE
    if($i_d>1){
        foreach ($opts2_d as $opt_d){
            $o_d .= "<option>".trim($opt_d)."</option>";
        }
        $o_d = '  <label class="select">
            <select class="search form-control" type="search" data-column="1">

            <option value="" disabled selected>seleziona tra '.($i_d).' fornitori</option>
            '.trim($o_d).'
            </select>
            </label>';

    }



?>
<!-- Dynamic Modal -->
<div class="modal fade" id="modal_scatole" tabindex="-1" role="dialog" aria-labelledby="remoteModalLabelscatole" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

        </div>
    </div>
</div>
<!-- /.modal -->
<?php echo $O->navbar_ordine()
            .$msg_trasporto
            .$msg_gestione
            .$msg_costo_gas
            .$msg_maggiorazione_gas
            .$msg_scatole; ?>

<?php
    if($O->privato==2){
     ?>
        <div class="alert alert-danger margin-top-10">QUESTO ORDINE E' STATO CREATO CON LA VECCHIA VERSIONE DI RETEDES, PER CUI PER COMPRARE MERCE E' NECESSARIO USARE QUELLA.</div>

     <?php
     die();
    }
?>

<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
        </article>
    </div>
</section>

<div class="hidden-xs margin-bottom-5">
     <a href="javascript:void(0);" class="show_box_filtro btn btn-default">Filtra gli articoli</a>
     <a href="javascript:void(0);" class="show_box_miaspesa btn btn-default">Mostra la spesa</a>
     <a href="javascript:void(0);" class="show_box_textbox_importi btn btn-default">Metodo</a>
     <?php
     if(_USER_GAS_USA_CASSA){
         ?>
         <a href="javascript:void(0);" class="show_box_prenotazione btn btn-default">Prenotazione</a>
         <?php
     }
     ?>
     <a href="javascript:void(0);" class="show_box_note_ordine btn btn-default">Mie note ordine</a>
     <a href="javascript:void(0);" class="show_box_compra_amico btn btn-default">Compra per un amico</a>

     <a href="javascript:void(0);" class="cancella_spesa_button btn btn-danger pull-right">Elimina spesa</a>

</div>
<div class="btn-group margin-bottom-10 pull-right visible-xs">
        <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);">Opzioni&nbsp;&nbsp;&nbsp;<span class="caret"></span></a>
    <ul class="dropdown-menu">
        <li>
            <a href="javascript:void(0);" class="show_box_filtro">Mostra il filtro</a>
        </li>
        <li>
            <a href="javascript:void(0);" class="show_box_miaspesa">Mostra la spesa</a>
        </li>
        <li>
            <a href="javascript:void(0);" class="show_box_textbox_importi">Metodo</a>
        </li>
        <?php
        if(_USER_GAS_USA_CASSA){
        ?>
        <li>
            <a href="javascript:void(0);" class="show_box_prenotazione">Prenotazione</a>
        </li>
        <?php
        }
        ?>
        <li>
            <a href="javascript:void(0);" class="show_box_note_ordine">Mie note ordine</a>
        </li>
        <li>
            <a href="javascript:void(0);" class="show_box_compra_amico">Compra per un amico</a>
        </li>
        <li class="divider"></li>
        <li class="text-danger">
            <a href="javascript:void(0);" class="cancella_spesa_button">Elimina spesa</a>
        </li>
    </ul>
</div>

<div class="clearfix"></div>

<div class="text-align-center" id="waiting" style="height:2000px;">
    <h1></h1>
    <p class="font-lg"><i class="fa fa-smile fa-5x"></i>  ecco, ci siamo....</p>
</div>

<div class="panel padding-10 collapse" id="box_filtro">
<button id="close_box_filtro" class="btn btn-default btn-mini btn-xs pull-right"><i class="fa fa-times text-danger"></i></button>
<h3>Filtro</h3>
<p class="note">riduci gli articoli visualizzati filtrandoli in base alla categoria oppure al loro nome o codice.</p>
<form class="smart-form" >
    <section class="margin-bottom-10">
        <?php echo $o; ?>
    </section>
    <section class="margin-bottom-10">
        <?php echo $o_d; ?>
    </section>
    <section class="margin-bottom-10">
    <label class="input"><i class="icon-append fa fa-times reset"></i>
        <input class="search" placeholder="Filtra" data-column="all" id="lemma">
    </label>
    </section>
</form>
</div>

<div class="panel padding-10 collapse" id="box_compra_amico">
<button id="close_box_compra_amico" class="btn btn-default btn-mini btn-xs pull-right"><i class="fa fa-times text-danger"></i></button>
<h3>Compra per un amico</h3>
<p>Scegli un tuo amico in rubrica usando la lista qua sotto, oppure <button class="btn btn-link" id="aggiungi_amico">clicca qua</button> per aggiungerne uno; quando la pagina si ricarica lo ritroverai nella lista. Gli articoli che comprerai quando questa opzione è attivata saranno assegnati a lui.</p>
<form class="smart-form" >
    <label class="select">
        <select id="select_compra_amico">
            <option value="0">me stesso</option>
            <?php
                $stmt = $db->prepare("SELECT id_amici,nome FROM retegas_amici WHERE id_referente="._USER_ID." AND status=1 ORDER BY nome ASC");
                $stmt->execute();
                $rowsA = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach($rowsA as $rowA){
                    echo '<option value="'.$rowA["id_amici"].'">'.$rowA["nome"].'</option>';
                }
            ?>
    </select> <i></i> </label>
</form>
<hr>
<p>Se devi correggere i quantitativi ordinati e assegnati agli amici, vai in questa pagina:</p>
<p><a href="#ajax_rd4/ordini/ordini_amici.php?id=<?php echo $O->id_ordini; ?>" class="btn btn-success">GESTIONE SPESA AMICI</a></p>
</div>

<div class="panel padding-10 collapse" id="box_prenotazione">
    <button id="close_box_prenotazione" class="btn btn-default btn-mini btn-xs pull-right"><i class="fa fa-times text-danger"></i></button>
    <h3>Prenotazione</h3>
    <p class="note">Se hai la cassa attiva ma non hai abbastanza credito attiva la prenotazione: avrai tempo per ricaricare la cassa fino alla chiusura dell'ordine. Se alla chiusura il tuo credito risulta insufficiente, allora il tuo ordine verrà eliminato.</p>
    <?php
        echo $prenotazione_attiva.
             $prenotazione_delete.
             $prenotazione_conferma;
    ?>
</div>


<div class="panel padding-10 collapse" id="box_miaspesa">
    <button id="close_box_miaspesa" class="btn btn-default btn-mini btn-xs pull-right"><i class="fa fa-times text-danger"></i></button>
    <h3>La mia spesa <small>Qua vengono indicate le quantità acquistate.</small></h3>
    <div id="miaspesa_container" class="table-responsive"></div>
</div>

<div class="panel padding-10 collapse" id="box_textbox_importi">
        <button id="close_box_textbox_importi" class="btn btn-default btn-mini btn-xs pull-right"><i class="fa fa-times text-danger"></i></button>

    <h3>Metodo inserimento</h3>
    <p class="note">Usa il comodo tasto + oppure inserisci le quantità;</p>
    <?php
        echo $textbox_importi;
    ?>
</div>

<div class="panel padding-10 collapse" id="box_note_ordine">
<button id="close_box_filtro" class="btn btn-default btn-mini btn-xs pull-right"><i class="fa fa-times text-danger"></i></button>
<?php if(VA_ORDINE_USER($O->id_ordini,_USER_ID)>0){?>
<h3>Mie note ordine</h3>
<p class="note">Le note che scrivo qua saranno visibili ai gestori degli ordini.</p>
<form class="" >
    <div class="form-group ">
        <div class="">
            <textarea class="form-control font-xl" placeholder="Scrivi qua e poi salva...." rows="4" id="nota_ordine"><?php echo $O->get_note_utente(_USER_ID);?></textarea>
        </div>
    </div>
    <div class="">
        <button class="btn btn-success pull-right" type="submit" id="salva_nota_ordine">
            <i class="fa fa-save fa-fw"></i>
            Salva
        </button>
    </div>
</form>
<?php
}else{
?>
    <h3><i class="fa fa-frown-o text-danger fa-fw"></i> Le note possono essere inserite solo da chi ha già comprato qualcosa</h3>
<?php
}
?>
<div class="clearfix"></div>
</div>

<?php echo $prenotazione_alert; ?>
<table id="tabella_articoli" class="margin-top-10">
    <thead>
        <tr>
            <th>Codice</th>
            <th>Descrizione</th>
            <th>Acquista</th>
        </tr>
    </thead>
<tbody>
<?php
//ARTICOLI LOOP
    $escludi = array("(", ")", ".", ",", "/", ";", ":", "-", "_", "'", '"');
    
    //SCELGO SE E' UN LISTINO STANDARD OPPURE SE E' UNO LEGATO ALL'ORDINE
    $tipo_listino = $O->get_tipo_listino();
    if($tipo_listino=="ORD"){
        $stmt = $db->prepare("SELECT * from retegas_articoli_temp where id_ordine='".$O->id_ordini."' AND is_disabled=0; ");
    }else{
        $stmt = $db->prepare("SELECT * from retegas_articoli where id_listini='".$O->id_listini."' AND is_disabled=0; ");
    }
    
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $row){

        $opz_1 = trim(str_replace($escludi,"", $row["articoli_opz_1"]));
        $opz_2 = trim(str_replace($escludi,"", $row["articoli_opz_2"]));
        $opz_3 = trim(str_replace($escludi,"", $row["articoli_opz_3"]));

        if($opz_1<>""){
            $opz_1="<span class=\"label label-primary tag_1\">".$row["articoli_opz_1"]."</span>";
            $opz_1m=$row["articoli_opz_1"];
        }else{
           $opz_1= $opz_1m = "";
        }
        if($opz_2<>""){
            $opz_2="<span class=\"label label-primary tag_2\">".$row["articoli_opz_2"]."</span>";
            $opz_2m=$row["articoli_opz_2"];
        }else{
            $opz_2= $opz_2m = "";
        }
        if($opz_3<>""){
            $opz_3="<span class=\"label label-primary tag_3\">".$row["articoli_opz_3"]."</span>";
            $opz_3m=$row["articoli_opz_3"];
        }else{
            $opz_3= $opz_3m = "";
        }

        if($O->is_multiditta){
            $md='<i class="fa fa-truck fa-fw"></i><span class="font-xs">'.$row["descrizione_ditta"].'</span>';
            $md_m='<span class="font-xs note">'.$row["descrizione_ditta"].'</span>';
        }else{
            $md='';
            $md_m='';
        }


        $data_id_ordine = " data-id_ordine=\"".$O->id_ordini."\" ";
        $data_id_articolo = " data-id_articolo=\"".$row["id_articoli"]."\" ";
        $data_id_articolo_temp = " data-id_articolo_temp=\"".$row["id"]."\" ";
        
        $um = $row["u_misura"]." <b class=\"text-info\">".$row["misura"]."</b> per <span class=\"text-danger\"><b>"._NF($row["prezzo"])."</b> Eu.</span>";
        $scat ="<small>Scat. da <b class=\"text-info\">"._NF($row["qta_scatola"])."</b>, Min. <b class=\"text-info\">"._NF($row["qta_minima"])."</b></small>";



        //TOTALE RIGA
        if($tipo_listino=="ORD"){
        $query = "SELECT
                    Sum(retegas_dettaglio_ordini.qta_arr * retegas_dettaglio_ordini.prz_dett_arr) as TRIGA
                    FROM
                    retegas_dettaglio_ordini
                    Inner Join retegas_articoli ON retegas_dettaglio_ordini.id_articoli = retegas_articoli.id_articoli
                    WHERE
                    retegas_dettaglio_ordini.id_ordine =  '".$O->id_ordini."' AND
                    retegas_dettaglio_ordini.id_utenti =  '"._USER_ID."' AND
                    retegas_dettaglio_ordini.id_articoli = '".$row["id"]."';";
        }else{
        $query = "SELECT
                    Sum(retegas_dettaglio_ordini.qta_arr * retegas_dettaglio_ordini.prz_dett_arr) as TRIGA
                    FROM
                    retegas_dettaglio_ordini
                    Inner Join retegas_articoli ON retegas_dettaglio_ordini.id_articoli = retegas_articoli.id_articoli
                    WHERE
                    retegas_dettaglio_ordini.id_ordine =  '".$O->id_ordini."' AND
                    retegas_dettaglio_ordini.id_utenti =  '"._USER_ID."' AND
                    retegas_dettaglio_ordini.id_articoli = '".$row["id_articoli"]."';";    
        }
        
        $stmt = $db->prepare($query);
        $stmt->execute();
        $TR = $stmt->fetch(PDO::FETCH_ASSOC);
        $vn = _NF($TR["TRIGA"]);
        //TOTALE RIGA

        //RIGA NOTE
        if($vn>0){
            if($tipo_listino=="ORD"){
            $sqld = "SELECT valore_text FROM retegas_options
                    WHERE id_user='"._USER_ID."'
                    AND id_articolo='".$row["id"]."'
                    AND id_ordine='".$O->id_ordini."'
                    AND chiave='_NOTE_DETTAGLIO';";
            }else{
            $sqld = "SELECT valore_text FROM retegas_options
                    WHERE id_user='"._USER_ID."'
                    AND id_articolo='".$row["id_articoli"]."'
                    AND id_ordine='".$O->id_ordini."'
                    AND chiave='_NOTE_DETTAGLIO';";    
                
            }        
            $stmt = $db->prepare($sqld);
            $stmt->execute();
            $rowd = $stmt->fetch(PDO::FETCH_ASSOC);
            if($rowd["valore_text"]<>""){
                $valued = $rowd["valore_text"];
            }else{
                $valued = "Scrivi qua una nota !";
            }
            if($tipo_listino=="ORD"){
                $n = '<span class="note_dettaglio" id="art_'.$row["id"].'" data-name="update_note_dettaglio" data-type="textarea" data-pk="'.$row["id"].'" data-url="'.APP_URL.'/ajax_rd4/ordini/_act.php?id_ordine='.$O->id_ordini.'">'.$valued.'</span>';
            }else{
                $n = '<span class="note_dettaglio" id="art_'.$row["id_articoli"].'" data-name="update_note_dettaglio" data-type="textarea" data-pk="'.$row["id_articoli"].'" data-url="'.APP_URL.'/ajax_rd4/ordini/_act.php?id_ordine='.$O->id_ordini.'">'.$valued.'</span>';
            }
        }else{
            $n = "";
        }
        //RIGA NOTE

        //SCATOLE PIENE
        $per_completare_scatola_m = "";

        //BINOTTO
        //$scatole_intere = (int)         QTA_SCATOLE_INTERE_ARTICOLO_ORDINE($row["id_articoli"],$O->id_ordini);
        //$avanzo_articolo = (float)round(QTA_SCATOLA_AVANZO_ARTICOLO_ORDINE($row["id_articoli"],$O->id_ordini),2);
        if($O->metodo_scatole==0){
        
            $scatole_intere = (int)         QTA_SCATOLE_INTERE_ARTICOLO_ORDINE($row["id_articoli"],$O->id_ordini);
            $avanzo_articolo = (float)round(QTA_SCATOLA_AVANZO_ARTICOLO_ORDINE($row["id_articoli"],$O->id_ordini),2);
        }else{
            $scatole_intere = (int)         QTA_SCATOLE_INTERE_ARTICOLO_ORDINE_GAS($row["id_articoli"],$O->id_ordini,_USER_ID_GAS);
            $avanzo_articolo = (float)round(QTA_SCATOLA_AVANZO_ARTICOLO_ORDINE_GAS($row["id_articoli"],$O->id_ordini,_USER_ID_GAS),2);
        }

        if($tipo_listino=="ORD"){
            $qta_ordinata_user = QTA_ORDINATA_ORDINE_ARTICOLO_USER($O->id_ordini,$row["id"],_USER_ID);
        }else{
            $qta_ordinata_user = QTA_ORDINATA_ORDINE_ARTICOLO_USER($O->id_ordini,$row["id_articoli"],_USER_ID);
        }
        $qta_scatola = $row["qta_scatola"];
        $per_completare_scatola ="";
        if($scatole_intere==0){
            //Se ? la prima scatola
            if($avanzo_articolo>0){
                $per_completare_scatola = ($qta_scatola - $avanzo_articolo);
                if($qta_ordinata_user>0){
                    //Se sono io che ho ordinato
                    $colore = "text-danger";
                    $per_completare_scatola_m = $per_completare_scatola;
                    $per_completare_scatola = "<strong class=\"$colore font-md\">$per_completare_scatola</strong> per chiudere la prima scatola";
                }else{
                    //Se sono altri che hanno ordinato
                    $colore = "text-warning";
                    $per_completare_scatola_m = $per_completare_scatola;
                    $per_completare_scatola = "<strong class=\"$colore font-md\">$per_completare_scatola</strong> per chiudere la prima scatola";
                }
            }else{
               // Nessun articolo ordinato da nessuno
               $colore ="";
            }
        }else{
            //Se ci sono gi? scatole
            if($avanzo_articolo>0){
                $per_completare_scatola = ($qta_scatola - $avanzo_articolo);
                if($qta_ordinata_user>0){
                    $colore = "text-danger";
                    $per_completare_scatola_m = $per_completare_scatola;
                    $per_completare_scatola = "<strong class=\"$colore font-md\">$per_completare_scatola</strong> per chiudere un'altra scatola";
                }else{
                    //Se sono altri che hanno ordinato
                    $colore = "text-warning";
                    $per_completare_scatola_m = $per_completare_scatola;
                    $per_completare_scatola = "<strong class=\"$colore font-md\">$per_completare_scatola</strong> per chiudere un'altra scatola";
                }
            }else{
               // Nessun articolo ordinato da nessuno
               $colore ="";
            }

        }
        //PER COMPLETARE SCATOLA

        //TOTALE RIGA
        if($qta_ordinata_user<>0){
            $qta_ordinata_user=_NF($qta_ordinata_user);
            $hidden = " collapse in ";
            $colore_acquisto = "";
        }else{
            $hidden=" collapse ";
            $qta_ordinata_user="0,00";
            $colore_acquisto = " text-muted ";
        }
        $t_riga="&euro; <b class=\"importo_riga $colore_acquisto \" $data_id_ordine $data_id_articolo $data_id_articolo_temp>".$vn."</b><button class=\"btn btn-xs btn-link ord_delete $hidden pull-left\" $data_id_ordine $data_id_articolo $data_id_articolo_temp><i class=\"fa fa-times text-danger\"></i></button>";

        //Tolto il 15/5/16
        //<!--<button rel="popover" data-trigger="manual" data-html="true" data-content="'.strip_tags($um).'<br>'.strip_tags($scat).'" class="btn btn-info btn-xs"><span class="glyphicon glyphicon-info-sign"></span></button>-->
        //                <!--<br>-->
        //                <!--<span class="situazione_scatole_m" '.$data_id_ordine." ".$data_id_articolo.'>'.$per_completare_scatola_m.'</span>&nbsp;-->

        if($tipo_listino=="ORD"){
            $id_articolo_stringa = $row["id"];
        }else{
            $id_articolo_stringa = $row["id_articoli"];
        }
        

        echo ' <tr >
                <td>
                    <span class="hidden">'.$row["codice"].'</span>
                    <span class="visible-xs"><h6><small>'.$row["codice"].'</small></h6><h4>'._nf($row["prezzo"]).' &euro;</h4></span>
                    <h4 class="hidden-xs"><small>'.$row["codice"].'</small></h4>
                    <h3 class="hidden-xs">'._nf($row["prezzo"]).' &euro;</h3>
                </td>
                <td>
                    <span class="hidden-xs sorter" style="font-size: larger;">'. $row["descrizione_articoli"].'</span>
                    <span class="visible-xs small sorter">'. $row["descrizione_articoli"].'</span>

                    <div class="pull-right hidden-xs" style="text-align:right;">
                        <span>'.$um.'</span><br>
                        <span>'.$scat.'</span><br>
                        <span class="situazione_scatole note" '.$data_id_ordine." ".$data_id_articolo." ".$data_id_articolo_temp.'>'.$per_completare_scatola.'</span><br>
                        <span class="situazione_amici" '.$data_id_ordine." ".$data_id_articolo." ".$data_id_articolo_temp.'></span>
                    </div>

                    <div>
                        <span class="hidden-xs">'. $opz_1." ".$opz_2." ".$opz_3.' '.$md.'</span>
                        <span class="visible-xs note">'.$opz_1m." ".$opz_2m." ".$opz_3m.' '.$md_m.'</span>
                    </div>

                    <span class="hidden-xs pull-left margin-top-5" style="margin-right:5px;">
                        <a data-target="#modal_scatole" data-toggle="modal" href="/gas4/ajax_rd4/ordini/_act.php?name=update_status_scatole&mode=m&id_ordine='.$O->id_ordini.'&id_articolo='.$row["id_articoli"].'" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-info-sign"></span></a>
                    </span>

                    <span id="note_dett_container_'.$id_articolo_stringa.'">'.$n.'</span>

                    <span class="visible-xs pull-right margin-top-5">

                        <a data-target="#modal_scatole" data-toggle="modal" href="/gas4/ajax_rd4/ordini/_act.php?name=update_status_scatole&mode=m&id_ordine='.$O->id_ordini.'&id_articolo='.$id_articolo_stringa .'" class="btn btn-default btn-xs situazione_scatole_xs">
                            <span class="glyphicon glyphicon-info-sign"></span>
                        </a>

                        <span class="situazione_amici" '.$data_id_ordine." ".$data_id_articolo." ".$data_id_articolo_temp.'></span>
                    </span>
                </td>
                <td class="text-right" style="vertical-align:bottom">
                    '.$t_riga.'
                    <div class="clearfix"></div>
                    <hr/>
                    <div class="inserimento_button" style="'.$metodo_pulsante.'">
                        <span id="dett_'.$id_articolo_stringa .'" data-pk="'.$id_articolo_stringa .'" class="riga_ordine font-lg pull-left '.$colore_acquisto.'"'. $data_id_ordine.$data_id_articolo.$data_id_articolo_temp.'>'.$qta_ordinata_user.'</span>
                        <button class="btn btn-success btn-circle ord_plus pull-right" '.$data_id_ordine.$data_id_articolo.$data_id_articolo_temp.'>
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                    <div class="form-group inserimento_button " style="'.$metodo_textbox.' max-width:160px; float:right; margin-bottom:0;">
                                    <div class="input-group">
                                        <input size="6" class="riga_textbox form-control" id="textbox_'.$id_articolo_stringa.'" data-pk="'.$id_articolo_stringa.'" type="text" name="articolo_'.$id_articolo_stringa.'" value="'.$qta_ordinata_user.'" '. $data_id_ordine.$data_id_articolo.$data_id_articolo_temp.' style="text-align:right">
                                        <span class="input-group-addon ord_inserisci" style="cursor:pointer" '. $data_id_ordine.$data_id_articolo.$data_id_articolo_temp.' ><i class="fa fa-check icona_inserisci" '. $data_id_ordine.$data_id_articolo.$data_id_articolo_temp.'></i></span>
                                    </div>
                                    <div class="clearfix"></div>
                    </div>

                </td>
              </tr>';
    }

?>
</tbody>
</table>

<!-- Dynamic Modal -->
<div class="modal fade" id="modal_amici" tabindex="-1" role="dialog" aria-labelledby="remoteModalLabelamici" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        </div>
    </div>
</div>
<!-- /.modal -->



<script type="text/javascript">
    pageSetUp(true);

    var imadding = false;
    
    var id_amico;
    var nome_amico;
    var pagefunction = function() {

        //-------------------------HELP
        document.title = '<?php echo "ReteDES.it :: ".trim(str_replace($escludi," ", $O->descrizione_ordini))?>';
        <?php echo help_render_js($page_id); ?>
        //-------------------------HELP
        loadScript("js_rd4/plugin/tablesorter/js/jquery.tablesorter.widgets.js",loadMath);

        function loadMath(){
            loadScript("js_rd4/plugin/tablesorter/js/widgets/widget-math.js", loadXeditable);
        }
        function loadXeditable(){
             loadScript("js/plugin/x-editable/x-editable.min.js", startTable);
        }
        function startTable(){

                function updateMiaspesa(){
                    $('#miaspesa_container').empty();
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/ordini/_act.php",
                          dataType: 'json',
                          data: {act: "show_miaspesa", id_ordine: <?php echo $id_ordine?> },
                          context: document.body
                        })
                    .done(function(data) {
                        $('#miaspesa_container').html(data.html);
                    });
                }


                $.extend($.tablesorter.themes.bootstrap, {
                    table      : 'table table-bordered',
                    caption    : 'caption',
                    sortNone   : 'bootstrap-icon-unsorted',
                    sortAsc    : 'fa fa-arrow-up',
                    sortDesc   : 'fa fa-arrow-down'

                  });

                var $table = $('#tabella_articoli').tablesorter({
                    theme: 'bootstrap',
                        //debug:true,
                        widgets: ["uitheme","filter","zebra"],
                        <?php 
                            if(_USER_GAS_ORDINAMENTO_COMPRA==4){
                        ?>    
                        textExtraction: {
                          1: function(node, table, cellIndex){ return $(node).find("span.tag_1").text(); }
                        },        
                        <?php        
                            }
                        ?>
                        widgetOptions : {
                            zebra : ["even", "odd"],
                            filter_reset : ".reset",
                            filter_columnFilters: false
                        }
                }).bind('filterEnd', function(e, filter){
                      rowCount = $("#tabella_articoli tr:visible").length - 1;
                      $('#lemma').attr("placeholder", "Filtra tra " + rowCount + " articoli");
                  });
                $("#tabella_articoli").bind("updateComplete",function(e, table) {
                    console.log("updated");
                });
                $.tablesorter.filter.bindSearch( $table, $('.search'), false );
                
                var sorting = [<?php if(_USER_GAS_ORDINAMENTO_COMPRA==0){echo "[2,1],[0,0]";} if(_USER_GAS_ORDINAMENTO_COMPRA==1){echo "[2,1],[1,0]";} if(_USER_GAS_ORDINAMENTO_COMPRA==2){echo "[0,0]";} if(_USER_GAS_ORDINAMENTO_COMPRA==3){echo "[1,0]";} if(_USER_GAS_ORDINAMENTO_COMPRA==4){echo "[],[1,0]";}?>];
                $table.trigger("sorton",[sorting]);
                
                

        rowCount = $("#tabella_articoli tr:visible").length - 2;
        $('#lemma').attr("placeholder", "Filtra tra " + rowCount + " articoli");


        $('.note_dettaglio').editable({
                ajaxOptions: { dataType: 'json' },
                success: function(response, newValue) {
                        if(response.result == 'KO'){
                            return response.msg;
                        }
                    }
        });

        //$("body").on("click", ".ord_delete", function(e){
        $(".ord_delete").click(function(e){
            e.preventDefault();

            var id_ordine =$(this).data("id_ordine");
            var id_articolo = $(this).data("id_articolo");
            var id_articolo_temp = $(this).data("id_articolo_temp");
            var f=document.createElement("audio");

            console.log("Ordine: "+ id_ordine);
            console.log("Articolo: "+ id_articolo);

            $.ajax({
                          type: "POST",
                          url: "ajax_rd4/ordini/_act.php",
                          dataType: 'json',
                          data: {act: "delete_articolo_ordine", id_ordine: id_ordine, id_articolo:id_articolo, id_articolo_temp:id_articolo_temp},
                          context: document.body
                        })
            .done(function(data) {
                    console.log("RES: "+ data.result);
                    console.log("value_ordine: " + data.value_ordine);
                    console.log("value_cassa: "+data.value_cassa);
                    console.log("qta_articolo_new: "+data.qta_articolo_new);
                    console.log("importo_articolo_new:: "+data.importo_articolo_new);

                    if(data.result=="OK"){
                        //ok(data.result);
                        //riga.removeClass("danger");
                        //riga.addClass("success");
                        var q_new =  parseFloat(data.qta_articolo_new);
                        var i_new =  parseFloat(data.importo_articolo_new);
                        var v_new =  parseFloat(data.value_ordine);
                        var c_new =  parseFloat(data.value_cassa);


                        var q_q   =  q_new.toFixed(2);
                        var i_i   =  i_new.toFixed(2);
                        var v_v   =  v_new.toFixed(2);
                        var c_c   =  c_new.toFixed(2);


                        //Aggiorno qta riga
                        $('.riga_ordine[data-id_ordine='+id_ordine+'][data-id_articolo='+id_articolo+']').html(q_q);

                        //Aggiorno qta textbox
                        $('.riga_textbox[data-id_ordine='+id_ordine+'][data-id_articolo='+id_articolo+']').val(q_q);


                        //Aggiorno Totale riga
                        $('.importo_riga[data-id_ordine='+id_ordine+'][data-id_articolo='+id_articolo+']').html(i_i);
                        //Aggiorno totale Ordine:
                        $('.totale_ordine').html(v_v);
                        //$('.nav_totale_ordine[data-id_ordine='+id_ordine+']').html(v_v);
                        //Aggiorno cassa
                        $('.totale_cassa').html(c_c);
                        //Tolgo il bidone
                        $('.ord_delete[data-id_ordine='+id_ordine+'][data-id_articolo='+id_articolo+']').collapse("hide");
                        //Aggiorno la situazione scatole fullwidth e mobile
                        $( '.situazione_scatole[data-id_ordine='+id_ordine+'][data-id_articolo='+id_articolo+']').empty();
                        $( '.situazione_scatole_m[data-id_ordine='+id_ordine+'][data-id_articolo='+id_articolo+']').empty();
                        //Aggiorno amici coinvolti
                        $('.situazione_amici[data-id_ordine='+id_ordine+'][data-id_articolo='+id_articolo+']').empty();
                        //Aggiorno x-editable
                        $( "#note_dett_container_" + id_articolo ).empty();
                        //AggiornoMiaSpesa
                        if ($('#miaspesa_container').is(':visible')){updateMiaspesa();}

                        // SUono
                        f.setAttribute("src",$.sound_path+"voice_off.mp3"),$.get(),f.addEventListener("load",function(){f.play()},!0),$.sound_on&&(f.pause(),f.play());


                    }else{
                        ko(data.msg);
                    }


                });

            });

        //ORDINE AGGIUNGI
        //$("body").on("click", ".ord_plus", function(e){
        $(".ord_plus").click(function(e){
            e.preventDefault();
            
            if(imadding===true){console.log("Waiting for response...");return;}
            imadding=true;
            
            
            var id_ordine =$(this).data("id_ordine");
            var id_articolo = $(this).data("id_articolo");
            var id_articolo_temp = $(this).data("id_articolo_temp");
            
            id_amico=$("#select_compra_amico").val();
            var f=document.createElement("audio");
            var qta = 1;

            console.log("Ordine: "+ id_ordine);
            console.log("Articolo: "+ id_articolo);

            $.ajax({
                          type: "POST",
                          url: "ajax_rd4/ordini/_act.php",
                          dataType: 'json',
                          data: {   act: "add_articolo_ordine",
                                    id_ordine: id_ordine,
                                    id_articolo:id_articolo,
                                    id_articolo_temp:id_articolo_temp,
                                    qta:qta,
                                    id_amico:id_amico},
                          context: document.body
                        })
            .done(function(data) {
                    
                    console.log("MSG: "+ data.msg);
                    console.log("RES: "+ data.result);
                    console.log("value_ordine: " + data.value_ordine);
                    console.log("value_cassa: "+data.value_cassa);
                    console.log("qta_articolo_new: "+data.qta_articolo_new);
                    console.log("importo_articolo_new:: "+data.importo_articolo_new);

                    if(data.result=="OK"){
                        //ok(data.result);
                        //riga.removeClass("danger");
                        //riga.addClass("success");
                        var q_new =  parseFloat(data.qta_articolo_new);
                        var i_new =  parseFloat(data.importo_articolo_new);
                        var v_new =  parseFloat(data.value_ordine);
                        var c_new =  parseFloat(data.value_cassa);
                        var a_coi =  parseFloat(data.amici_coinvolti);

                        var q_q   =  q_new.toFixed(2);
                        var i_i   =  i_new.toFixed(2);
                        var v_v   =  v_new.toFixed(2);
                        var c_c   =  c_new.toFixed(2);
                        var a_c   =  a_coi.toFixed(0);

                        //console.log("Amici: " + a_c);

                        //Aggiorno qta riga
                        $('.riga_ordine[data-id_ordine='+id_ordine+'][data-id_articolo='+id_articolo+']').html(q_q);

                        //Aggiorno qta textbox
                        $('.riga_textbox[data-id_ordine='+id_ordine+'][data-id_articolo='+id_articolo+']').val(q_q);

                        //Aggiorno Totale riga
                        $('.importo_riga[data-id_ordine='+id_ordine+'][data-id_articolo='+id_articolo+']').html(i_i);
                        //Aggiorno totale Ordine:
                        $('.totale_ordine').html(v_v);
                        //$('.nav_totale_ordine[data-id_ordine='+id_ordine+']').html(v_v);
                        //Aggiorno cassa
                        $('.totale_cassa').html(c_c);
                        //Aggiungo il bidone
                        $('.ord_delete[data-id_ordine='+id_ordine+'][data-id_articolo='+id_articolo+']').collapse('show');
                        //Aggiorno la situazione scatole fullwidth
                        console.log("Aggiorno qta scatole");
                        $( '.situazione_scatole[data-id_ordine='+id_ordine+'][data-id_articolo='+id_articolo+']').load( "/gas4/ajax_rd4/ordini/_act.php", {act: "update_status_scatole", id_ordine: id_ordine, id_articolo:id_articolo}, function(e){});
                        $( '.situazione_scatole_m[data-id_ordine='+id_ordine+'][data-id_articolo='+id_articolo+']').load( "/gas4/ajax_rd4/ordini/_act.php", {act: "update_status_scatole", id_ordine: id_ordine, id_articolo:id_articolo, mode:"m"}, function(e){});
                        //Aggiorno amici
                        if(a_c>0){
                            $('.situazione_amici[data-id_ordine='+id_ordine+'][data-id_articolo='+id_articolo+']').html('<span class="label label-info">'+a_c+' <i class="fa fa-group"></i></span>');
                        }

                        //Aggiorno x-editable
                        if ($("#note_dett_container_"+id_articolo).is(':empty')){
                            $( "#note_dett_container_"+id_articolo ).append( '<br><span class="note_dettaglio" data-type="textarea" data-pk='+id_articolo+' data-url="/gas3/_pages/ACT.php?id_ordine='+id_ordine+'&act=update_note_dettaglio">Scrivi qua una nota !</span>');
                        }
                        $('.note_dettaglio').editable();

                        //AggiornoMiaSpesa
                        if ($('#miaspesa_container').is(':visible')){updateMiaspesa();}

                        //suono
                        f.setAttribute("src",$.sound_path+"voice_on.mp3"),$.get(),f.addEventListener("load",function(){f.play()},!0),$.sound_on&&(f.pause(),f.play());

                        //triggers
                        
                        $.ajax({
                          type: "POST",
                          url: "ajax_rd4/ordini/_act_triggers.php",
                          dataType: 'json',
                          data: {act: 'do_triggers', id_ordine : id_ordine},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                console.log(data.msg);    
                            }else{
                                console.log("Trigger KO");
                            }
                        });

                    }else{
                        ko(data.msg);
                    }


                }).fail(function() {
                    
                    console.log( "fail" );
                }).always(function() {
                    imadding=false;
                    console.log( "complete" );
                });

            });



        $(".riga_textbox").keyup(function (e) {
            if (e.keyCode == 13) {
                var id_ordine =$(this).data("id_ordine");
                var id_articolo = $(this).data("id_articolo");
                console.log("ENTER on "+ id_articolo);
                $( '.ord_inserisci[data-id_ordine='+id_ordine+'][data-id_articolo='+id_articolo+']').trigger( "click" );
            }
        });



        //ORDINE AGGIUNGI
        //$("body").on("click", ".ord_plus", function(e){
        $(".ord_inserisci").click(function(e){

            console.log("inserisci");


            e.preventDefault();

            var id_ordine =$(this).data("id_ordine");
            var id_articolo = $(this).data("id_articolo");
            var id_articolo_temp = $(this).data("id_articolo_temp");
            
            id_amico=$("#select_compra_amico").val();
            var f=document.createElement("audio");
            var qta = $("#textbox_" + id_articolo).val();

            $('.icona_inserisci[data-id_ordine='+id_ordine+'][data-id_articolo='+id_articolo+']').removeClass("fa-check").addClass("fa-cog fa-spin");


            console.log("Ordine: "+ id_ordine);
            console.log("Articolo: "+ id_articolo);
            console.log("Articolo_temp: "+ id_articolo_temp);
            console.log("qta : "+ qta);
            console.log("Amico: " + id_amico)

            //return false;

            $.ajax({
                          type: "POST",
                          url: "ajax_rd4/ordini/_act_compra.php",
                          dataType: 'json',
                          data: {   act: "add_articolo_ordine_qta",
                                    id_ordine: id_ordine,
                                    id_articolo:id_articolo,
                                    id_articolo_temp:id_articolo_temp,
                                    qta:qta,
                                    id_amico:id_amico},
                          context: document.body
                        })
            .done(function(data) {
                    console.log("Ins qta");
                    console.log("MSG: "+ data.msg);
                    console.log("RES: "+ data.result);
                    console.log("value_ordine: " + data.value_ordine);
                    console.log("value_cassa: "+data.value_cassa);
                    console.log("qta_articolo_new: "+data.qta_articolo_new);
                    console.log("qta_articolo_old: "+data.qta_articolo_old);
                    console.log("importo_articolo_new:: "+data.importo_articolo_new);

                    var q_new =  parseFloat(data.qta_articolo_new);
                    var q_old =  parseFloat(data.qta_articolo_old);
                    var i_new =  parseFloat(data.importo_articolo_new);
                    var v_new =  parseFloat(data.value_ordine);
                    var c_new =  parseFloat(data.value_cassa);
                    var a_coi =  parseFloat(data.amici_coinvolti);

                    var q_q   =  q_new.toFixed(2);
                    var q_o   =  q_old.toFixed(2);
                    var i_i   =  i_new.toFixed(2);
                    var v_v   =  v_new.toFixed(2);
                    var c_c   =  c_new.toFixed(2);
                    var a_c   =  a_coi.toFixed(0);


                    if(data.result=="OK"){
                        //ok(data.result);
                        //riga.removeClass("danger");
                        //riga.addClass("success");


                        //console.log("Amici: " + a_c);

                        //Aggiorno qta riga
                        $('.riga_ordine[data-id_ordine='+id_ordine+'][data-id_articolo='+id_articolo+']').html(q_q);

                        //Aggiorno qta textbox
                        $('.riga_textbox[data-id_ordine='+id_ordine+'][data-id_articolo='+id_articolo+']').val(q_q);

                        //Aggiorno Totale riga
                        $('.importo_riga[data-id_ordine='+id_ordine+'][data-id_articolo='+id_articolo+']').html(i_i);
                        //Aggiorno totale Ordine:
                        $('.totale_ordine').html(v_v);
                        //$('.nav_totale_ordine[data-id_ordine='+id_ordine+']').html(v_v);
                        //Aggiorno cassa
                        $('.totale_cassa').html(c_c);
                        //Aggiungo il bidone
                        $('.ord_delete[data-id_ordine='+id_ordine+'][data-id_articolo='+id_articolo+']').collapse('show');
                        //Aggiorno la situazione scatole fullwidth
                        console.log("Aggiorno qta scatole");
                        $( '.situazione_scatole[data-id_ordine='+id_ordine+'][data-id_articolo='+id_articolo+']').load( "/gas4/ajax_rd4/ordini/_act.php", {act: "update_status_scatole", id_ordine: id_ordine, id_articolo:id_articolo}, function(e){});
                        $( '.situazione_scatole_m[data-id_ordine='+id_ordine+'][data-id_articolo='+id_articolo+']').load( "/gas4/ajax_rd4/ordini/_act.php", {act: "update_status_scatole", id_ordine: id_ordine, id_articolo:id_articolo, mode:"m"}, function(e){});
                        //Aggiorno amici
                        if(a_c>0){
                            $('.situazione_amici[data-id_ordine='+id_ordine+'][data-id_articolo='+id_articolo+']').html('<span class="label label-info">'+a_c+' <i class="fa fa-group"></i></span>');
                        }

                        //Aggiorno x-editable
                        if ($("#note_dett_container_"+id_articolo).is(':empty')){
                            $( "#note_dett_container_"+id_articolo ).append( '<br><span class="note_dettaglio" data-type="textarea" data-pk='+id_articolo+' data-url="/gas3/_pages/ACT.php?id_ordine='+id_ordine+'&act=update_note_dettaglio">Scrivi qua una nota !</span>');
                        }
                        $('.note_dettaglio').editable();

                        //AggiornoMiaSpesa
                        if ($('#miaspesa_container').is(':visible')){updateMiaspesa();}

                        //suono
                        f.setAttribute("src",$.sound_path+"voice_on.mp3"),$.get(),f.addEventListener("load",function(){f.play()},!0),$.sound_on&&(f.pause(),f.play());

                        //triggers
                        $.ajax({
                          type: "POST",
                          url: "ajax_rd4/ordini/_act_triggers.php",
                          dataType: 'json',
                          data: {act: 'do_triggers', id_ordine : id_ordine},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                console.log(data.msg);    
                            }else{
                                console.log("Trigger KO");
                            }
                        });

                    }else{
                        ko(data.msg);
                        //Aggiorno qta textbox
                        $('.riga_textbox[data-id_ordine='+id_ordine+'][data-id_articolo='+id_articolo+']').val(''+q_o+'');

                    }

                    $('.icona_inserisci[data-id_ordine='+id_ordine+'][data-id_articolo='+id_articolo+']').removeClass("fa-cog fa-spin").addClass("fa-check");

                });

            });

            $('.totale_ordine').html("<?php echo _NF(VA_ORDINE_USER($O->id_ordini,_USER_ID)) ?>");
            $('.totale_cassa').html("<?php echo _NF(VA_CASSA_SALDO_UTENTE_TOTALE(_USER_ID))?>");
            $('#waiting').hide();
            $('#navbox_amico_ordine').html('<span><span class="hidden-xs">Compro per <strong>me stesso</strong></span><span class="visible-xs"><i class="fa fa-user text-success" rel="tooltip" data-original-title="compro per me stesso"></i></span></span>');

            $('.show_box_filtro').click(function(){$('#box_filtro').collapse('toggle');});
            $('#close_box_filtro').click(function(){$('#box_filtro').collapse('toggle');});

            $('.show_box_prenotazione').click(function(){$('#box_prenotazione').collapse('toggle');});
            $('#close_box_prenotazione').click(function(){$('#box_prenotazione').collapse('toggle');});

            $('.show_box_miaspesa').click(function(){$('#box_miaspesa').collapse('toggle');updateMiaspesa();});
            $('#close_box_miaspesa').click(function(){$('#box_miaspesa').collapse('toggle');});

            $('.show_box_textbox_importi').click(function(){$('#box_textbox_importi').collapse('toggle');});
            $('#close_box_textbox_importi').click(function(){$('#box_textbox_importi').collapse('toggle');});


            $('.show_box_note_ordine').click(function(){$('#box_note_ordine').collapse('toggle');});
            $('#close_box_note_ordine').click(function(){$('#box_note_ordine').collapse('toggle');});

            $('.show_box_compra_amico').click(function(){$('#box_compra_amico').collapse('toggle');});
            $('#close_box_compra_amico').click(function(){$('#box_compra_amico').collapse('toggle');});

            //NOTA ORDINE
            $('#salva_nota_ordine').click(function (e) {
                var id_ordine=<?php echo $O->id_ordini ?>;
                var nota_ordine=$('#nota_ordine').val();
                $.ajax({
                  type: "POST",
                  url: "ajax_rd4/ordini/_act.php",
                  dataType: 'json',
                  data: {act: 'salva_nota_ordine', id_ordine : id_ordine, nota_ordine:nota_ordine},
                  context: document.body
                }).done(function(data) {
                    if(data.result=="OK"){
                        ok(data.msg);
                    }else{
                        ko(data.msg);
                    }
                });
            });
            //ATTIVA PRENOTAZIONE
            $('#attiva_prenotazione').click(function (e) {
                $.SmartMessageBox({
                    title : "Attivi la prenotazione ?",
                    content : "<b>Attenzione:</b> L attivazione della prenotazione cancella ogni articolo già inserito in questo ordine.",
                    buttons : "[Cancella][Attiva]"
                }, function(ButtonPress, Value) {

                    if(ButtonPress=="Attiva"){
                        var id_ordine=<?php echo $O->id_ordini ?>;
                        $.ajax({
                          type: "POST",
                          url: "ajax_rd4/ordini/_act.php",
                          dataType: 'json',
                          data: {act: 'attiva_prenotazione', id_ordine : id_ordine},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                okReload(data.msg);
                            }else{
                                ko(data.msg);
                            }
                        });
                    }else{
                        console.log("cancella");
                    }
                });
            });
            //PRENOTAZIONE

            //CONFERMA PRENOTAZIONE
            $('#conferma_prenotazione').click(function (e) {
                $.SmartMessageBox({
                    title : "Confermi la prenotazione ?",
                    content : "<b>Attenzione:</b> La conferma della prenotazione può avvenire solo se si dispone del credito necessario.",
                    buttons : "[CONFERMA][Esci]"
                }, function(ButtonPress, Value) {

                    if(ButtonPress=="CONFERMA"){
                        var id_ordine=<?php echo $O->id_ordini ?>;
                        $.ajax({
                          type: "POST",
                          url: "ajax_rd4/ordini/_act.php",
                          dataType: 'json',
                          data: {act: 'conferma_prenotazione', id_ordine : id_ordine},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                okReload(data.msg);
                            }else{
                                ko(data.msg);
                            }
                        });
                    }else{
                        console.log("cancella");
                    }
                });
            });
            //PRENOTAZIONE

            //CANCELLA PRENOTAZIONE
            $('#elimina_prenotazione').click(function (e) {
                $.SmartMessageBox({
                    title : "Elimini la prenotazione ?",
                    content : "<b>Attenzione:</b> L eliminazione della prenotazione cancella ogni articolo già inserito in questo ordine.",
                    buttons : "[ELIMINA][Esci]"
                }, function(ButtonPress, Value) {

                    if(ButtonPress=="ELIMINA"){
                        var id_ordine=<?php echo $O->id_ordini ?>;
                        $.ajax({
                          type: "POST",
                          url: "ajax_rd4/ordini/_act.php",
                          dataType: 'json',
                          data: {act: 'elimina_prenotazione', id_ordine : id_ordine},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                okReload(data.msg);
                            }else{
                                ko(data.msg);
                            }
                        });
                    }else{
                        console.log("cancella");
                    }
                });
            });
            //PRENOTAZIONE

            //METODO INSERIMENTO
            $('#toggle_textbox_importi').click(function (e) {
                console.log("Toggle importi");
                $(".inserimento_button").toggle();
            });

            //CANCELLA SPESA
            $('.cancella_spesa_button').click(function (e) {
                $.SmartMessageBox({
                    title : "Elimini la tua spesa ?",
                    content : "<b>Attenzione:</b> Saranno eliminati tutti gli articoli ordinati in questo ordine.",
                    buttons : "[Esci][Elimina]"
                }, function(ButtonPress, Value) {

                    if(ButtonPress=="Elimina"){
                        var id_ordine=<?php echo $O->id_ordini ?>;
                        $.ajax({
                          type: "POST",
                          url: "ajax_rd4/ordini/_act.php",
                          dataType: 'json',
                          data: {act: 'del_miaspesa', id_ordine : id_ordine},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                okReload(data.msg);
                            }else{
                                ko(data.msg);
                            }
                        });
                    }else{
                        console.log("cancella");
                    }
                });
            });
            //CANCELLA SPESA

        //$('.riga_ordine').editable();
        }//end starttable

        $(document).on('change','#select_compra_amico',function(){
            //alert($('#select_compra_amico').val());
            //alert($("#select_compra_amico option:selected").text());
            if($('#select_compra_amico').val()>0){
                $('#navbox_amico_ordine').html('<span><span class="hidden-xs">Compro per <strong>'+$("#select_compra_amico option:selected").text()+'</strong></span><span class="visible-xs"><i class="fa fa-user text-danger" title="'+$("#select_compra_amico option:selected").text()+'" data-target="manual"></i></span></span>');
            }else{
                $('#navbox_amico_ordine').html('<span><span class="hidden-xs">Compro per <strong>'+$("#select_compra_amico option:selected").text()+'</strong></span><span class="visible-xs"><i class="fa fa-user text-success" title="'+$("#select_compra_amico option:selected").text()+'" data-target="manual"></i></span></span>');
            }
        })

        $("#aggiungi_amico").click(function(e) {

            $.SmartMessageBox({
                title : "Aggiungi un amico alla tua rubrica",
                content : "Inserisci il suo nome, potrai poi aggiungere gli altri dati nella sezione AMICI",
                buttons : "[Esci][Salva]",
                input : "text",
                placeholder : "Nome",
                inputValue: '',
            }, function(ButtonPress, Value) {

                if(ButtonPress=="Salva"){
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/user/_act.php",
                          dataType: 'json',
                          data: {act: "aggiungi_amico", value : Value},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    ok(data.msg);}else{ko(data.msg);}
                                    location.reload();
                        });
                }
            });

            e.preventDefault();
        })


    }
    // end pagefunction
    loadScript("js_rd4/plugin/tablesorter/js/jquery.tablesorter.min.js", pagefunction);



</script>
