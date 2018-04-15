<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.user.php");

$ui = new SmartUI;
$page_title = "Scheda utente";
$page_id="gestisci_utente";


$converter = new Encryption();
    $userid = CAST_TO_STRING($_POST["id"]);
if($userid==""){
    $userid = CAST_TO_STRING($_GET["id"]);
}
$userid = $converter->decode($userid);

$U = new user($userid);



$u="userid : $userid";
$stmt = $db->prepare("SELECT U.user_permission, U.fullname, U.userid, U.tel, U.id_gas, U.email, G.descrizione_gas, G.id_referente_gas FROM  maaking_users U INNER JOIN retegas_gas G ON G.id_gas=U.id_gas  WHERE userid = :userid LIMIT 1;");
$stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if(($stmt->rowCount()<>1) or ($row["userid"]==0)){
    echo "No: $userid";die();
}

$useridEnc = $converter->encode($row["userid"]);

if(
    (_USER_PERMISSIONS & perm::puo_gestire_utenti)
     AND ($row["id_gas"]==_USER_ID_GAS)
     OR
     (_USER_PERMISSIONS & perm::puo_vedere_retegas)
     OR
     ($row["id_referente_gas"]==_USER_ID)
     )
    {$ok=true;}else{echo "not allowed $userid";die();}


if($ok){
    //Elimina utente
    $id_utente = $row["userid"];
    $allow_del = true;
    //Ordini ?
    $stmt = $db->prepare("SELECT count(*) as conto from retegas_dettaglio_ordini where id_utenti=:id_utente;");
    $stmt->bindParam(':id_utente', $id_utente, PDO::PARAM_INT);
    $stmt->execute();
    $r = $stmt->fetch();
    if($r["conto"]>0){
        $allow_del = false;
        $dettaglio_ordini = $r["conto"];
    }else{
        $dettaglio_ordini = 0;
    }


    //Capo di un gas ?
    $stmt = $db->prepare("SELECT count(*) as conto from retegas_gas where id_referente_gas=:id_utente;");
    $stmt->bindParam(':id_utente', $id_utente, PDO::PARAM_INT);
    $stmt->execute();
    $r = $stmt->fetch();
    if($r["conto"]>0){
        $allow_del = false;
    }
    //Ha una ditta ?
    $stmt = $db->prepare("SELECT count(*) as conto from retegas_ditte where id_proponente=:id_utente;");
    $stmt->bindParam(':id_utente', $id_utente, PDO::PARAM_INT);
    $stmt->execute();
    $r = $stmt->fetch();
    if($r["conto"]>0){
        $allow_del = false;
        $dettaglio_ditte =$r["conto"];
    }else{
        $dettaglio_ditte = 0;
    }
    //ha gestito ordini ?
    $stmt = $db->prepare("SELECT count(*) as conto from retegas_referenze where id_utente_referenze=:id_utente;");
    $stmt->bindParam(':id_utente', $id_utente, PDO::PARAM_INT);
    $stmt->execute();
    $r = $stmt->fetch();
    if($r["conto"]>0){
        $allow_del = false;
        $dettaglio_gestioni = $r["conto"];
    }else{
        $dettaglio_gestioni =0;
    }
    if ($allow_del){
        $allow_del = '<p class="label">Operazioni <b>EXTRA</b></p>
                        <label class="toggle">
                        <input class="delete" type="checkbox" data-userid="'.$useridEnc.'" data-tipo="delete" name="checkbox-toggle">
                        <i data-swchon-text="SI" data-swchoff-text="NO"></i>Se l\'utente non ha ancora attività si può eliminare
                    </label>';
    }
}




//CASSA
if($row["user_permission"] & perm::puo_gestire_la_cassa){
    $cassa_checked='checked="CHECKED"';
}else{
    $cassa_checked='';
}
//ORDINI
if($row["user_permission"] & perm::puo_vedere_tutti_ordini){
    $ordini_checked='checked="CHECKED"';
}else{
    $ordini_checked='';
}
    //ORDINI
if($row["user_permission"] & perm::puo_gestire_utenti){
    $utenti_checked='checked="CHECKED"';
}else{
    $utenti_checked='';
}
    //GAS
if($row["user_permission"] & perm::puo_creare_gas){
    $gas_checked='checked="CHECKED"';
}else{
    $gas_checked='';
}
    //BACHECA
if($row["user_permission"] & perm::puo_eliminare_messaggi){
    $bacheca_checked='checked="CHECKED"';
}else{
    $bacheca_checked='';
}
//RETEDES
if($row["user_permission"] & perm::puo_vedere_retegas){
    $retedes_checked='checked="CHECKED"';
}else{
    $retedes_checked='';
}

//PARTECIPA
if($row["user_permission"] & perm::puo_partecipare_ordini){
    $partecipa_checked='checked="CHECKED"';
}else{
    $partecipa_checked='';
}
//CREA ORDINI
if($row["user_permission"] & perm::puo_creare_ordini){
    $creare_ordini_checked='checked="CHECKED"';
}else{
    $creare_ordini_checked='';
}
//DITTE E LISTINI
if($row["user_permission"] & perm::puo_creare_listini){
    $ditte_checked='checked="CHECKED"';
}else{
    $ditte_checked='';
}
if($row["user_permission"] & perm::puo_creare_ditte){
    $ditte_checked='checked="CHECKED"';
}else{
    $ditte_checked='';
}
//POSTARE
if($row["user_permission"] & perm::puo_postare_messaggi){
    $messaggi_checked='checked="CHECKED"';
}else{
    $messaggi_checked='';
}
//OPERARE
if($row["user_permission"] & perm::puo_operare_con_crediti){
    $crediti_checked='checked="CHECKED"';
}else{
    $crediti_checked='';
}
//AMICI
if($row["user_permission"] & perm::puo_avere_amici){
    $amici_checked='checked="CHECKED"';
}else{
    $amici_checked='';
}
//HELP

//USER SUPERVISORE ANAGRAFICHE
$stmt = $db->prepare("SELECT valore_text FROM retegas_options WHERE id_user=".$row["userid"]." AND chiave='_USER_SUPERVISORE_ANAGRAFICHE' LIMIT 1;");
$stmt->execute();
$h = $stmt->fetch(PDO::FETCH_ASSOC);
if($h["valore_text"]=='SI'){
    $supana_checked='checked="CHECKED"';
}else{
    $supana_checked='';
}



//USER EDIT HELP
$stmt = $db->prepare("SELECT valore_text FROM retegas_options WHERE id_user=".$row["userid"]." AND chiave='_USER_PUO_MODIFICARE_HELP' LIMIT 1;");
$stmt->execute();
$h = $stmt->fetch(PDO::FETCH_ASSOC);
if($h["valore_text"]=='SI'){
    $help_checked='checked="CHECKED"';
}else{
    $help_checked='';
}

//USER SUPERVISORE LISTINI
$stmt = $db->prepare("SELECT valore_text FROM retegas_options WHERE id_user=".$row["userid"]." AND chiave='_USER_SUPERVISORE_LISTINI' LIMIT 1;");
$stmt->execute();
$h = $stmt->fetch(PDO::FETCH_ASSOC);
if($h["valore_text"]=='SI'){
    $suplis_checked='checked="CHECKED"';
}else{
    $suplis_checked='';
}

$a ='<div class="row">
        <div class="col-sm-6">
        <form class="smart-form">
        <section class="col col-12">
            <p class="label">Permessi <strong>Gestionali</strong>: solo il responsabile gas può modificarli</p>
            <label class="toggle">
                <input class="abilitazioni" type="checkbox" data-userid="'.$useridEnc.'" data-tipo="'.perm::puo_gestire_la_cassa.'" name="checkbox-toggle" '.$cassa_checked.' id="puo_gestione_cassa">
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>Gestione della cassa
            </label>

            <label class="toggle">
                <input class="abilitazioni" type="checkbox" data-userid="'.$useridEnc.'" data-tipo="'.perm::puo_vedere_tutti_ordini.'" name="checkbox-toggle" '.$ordini_checked.' id="puo_supervisione_ordini">
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>Supervisione ordini
            </label>

            <label class="toggle">
                <input class="abilitazioni" type="checkbox" data-userid="'.$useridEnc.'" data-tipo="supervisore_listini" name="checkbox-toggle" '.$suplis_checked.'>
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>Supervisione listini
            </label>
            
            <label class="toggle">
                <input  class="abilitazioni" type="checkbox" data-userid="'.$useridEnc.'" data-tipo="'.perm::puo_gestire_utenti.'" name="checkbox-toggle" '.$utenti_checked.' id="puo_gestire_utenti">
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>Gestione utenti
            </label>

            <label class="toggle">
                <input  class="abilitazioni" type="checkbox" data-userid="'.$useridEnc.'" data-tipo="'.perm::puo_creare_gas.'" name="checkbox-toggle" '.$gas_checked.' id="puo_gestire_gas">
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>Gestione GAS
            </label>

        </section>

        <section class="col col-12 margin-top-10">
            <p class="label">Permessi <strong>Amministrativi</strong>: (Contattare lo staff di ReteDES.it)</p>
            <label class="toggle">
                <input class="abilitazioni" type="checkbox" data-userid="'.$useridEnc.'" data-tipo="supervisore_anagrafiche" name="checkbox-toggle" '.$supana_checked.'>
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>Supervisione anagrafiche.
            </label>
            <label class="toggle">
                <input class="abilitazioni" type="checkbox" data-userid="'.$useridEnc.'" data-tipo="'.perm::puo_eliminare_messaggi.'" name="checkbox-toggle" '.$bacheca_checked.'>
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>Moderazione feedback utenti.
            </label>
            <label class="toggle">
                <input class="abilitazioni" type="checkbox" data-userid="'.$useridEnc.'" data-tipo="gestione_help" name="checkbox-toggle" '.$help_checked.'>
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>Gestire gli HELP.
            </label>
            <label class="toggle">
                <input class="abilitazioni" type="checkbox" data-userid="'.$useridEnc.'" data-tipo="gestione_des" name="checkbox-toggle" '.$retedes_checked.'>
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>Può gestire il proprio DES.
            </label>
        </section>
        </form>
        </div>

        <div class="col-sm-6">
        <form class="smart-form">
        <section class="col col-12">
            <p class="label">Permessi <strong>Operativi</strong>: li modifica chi può gestire gli utenti.</p>
            <label class="toggle margin-top-5">
                <input class="abilitazioni" type="checkbox" data-userid="'.$useridEnc.'" data-tipo="'.perm::puo_creare_ordini.'" name="checkbox-toggle" '.$creare_ordini_checked.'>
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>Può creare e gestire Ordini
            </label>
            <label class="toggle">
                <input class="abilitazioni" type="checkbox" data-userid="'.$useridEnc.'" data-tipo="'.perm::puo_partecipare_ordini.'" name="checkbox-toggle" '.$partecipa_checked.'>
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>Può partecipare agli ordini
            </label>
            <label class="toggle">
                <input class="abilitazioni" type="checkbox" data-userid="'.$useridEnc.'" data-tipo="'.perm::puo_avere_amici.'" name="checkbox-toggle" '.$amici_checked.'>
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>Può usare la rubrica "Amici"
            </label>
            <label class="toggle">
                <input class="abilitazioni" type="checkbox" data-userid="'.$useridEnc.'" data-tipo="'.perm::puo_creare_ditte.'" name="checkbox-toggle" '.$ditte_checked.'>
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>Può creare e gestire Fornitori
            </label>
            <label class="toggle">
                <input class="abilitazioni" type="checkbox" data-userid="'.$useridEnc.'" data-tipo="'.perm::puo_operare_con_crediti.'" name="checkbox-toggle" '.$crediti_checked.'>
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>Può movimentare crediti altrui
            </label>
            <label class="toggle">
                <input class="abilitazioni" type="checkbox" data-userid="'.$useridEnc.'" data-tipo="'.perm::puo_postare_messaggi.'" name="checkbox-toggle" '.$messaggi_checked.'>
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>Può commentare ditte e ordini
            </label>

        </section>
        <section class="col col-12">'.$allow_del.'</section>
        </form>



        </div>
    </div>';

    /*TABELLA OPZIONI*/
    $t_o ='<table class="table-condensed" id="opzioni_table" >
            <thead>
                <tr>
                    <th class="filter-false"></th>
                    <th class="filter-select">Chiave</th>
                    <th>Updated</th>
                    <th>TEXT</th>
                    <th>INT</th>
                    <th>REAL</th>
                    <th>DATA</th>
                    <th>NOTE</th>
                    <th>G</th>
                </tr>
            </thead>
            <tbody>';
    $sql="SELECT * from retegas_options WHERE id_user=:id_utente AND id_ordine=0 and id_dettaglio=0 and id_articolo=0;";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id_utente', $id_utente, PDO::PARAM_INT);
    $stmt->execute();
    $rowsO = $stmt->fetchAll();
    foreach($rowsO as $rowO){

        $t_o.='<tr>';
        $t_o.='<td><input type="checkbox" name="id" value="'.$rowO["userid"].'"></td>';
        $t_o.='<td>'.$rowO["chiave"].'</td>';
        $t_o.='<td>'.$rowO["timbro"].'</td>';

        $t_o.='<td>'.substr($rowO["valore_text"],0,20).'</td>';
        $t_o.='<td>'.$rowO["valore_int"].'</td>';
        $t_o.='<td>'.$rowO["valore_real"].'</td>';
        $t_o.='<td>'.$rowO["valore_data"].'</td>';
        $t_o.='<td>'.substr(strip_tags($rowO["note_1"]),0,20).'</td>';
        $t_o.='<td>'.$rowO["id_gas"].'</td>';
        $t_o.='';
        $t_o.='';
        $t_o.='';
        $t_o.='';
        $t_o.='';
        $t_o.='';
        $t_o.='';
        $t_o.='';
        $t_o.='</tr>';


    }



    $t_o .='
            </tbody>
            </table>';

?>
<div class="inbox-nav-bar no-content-padding">
    <h1 class="page-title txt-color-blueDark"><i class="fa fa-fw fa-user"></i> <strong>#<?php echo $U->userid;?></strong> <?php echo $U->fullname;?> <small>(<?php echo $U->descrizione_gas;?>)</small></h1>
</div>

<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>

        </article>
    </div>
</section>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="panel panel-blueLight padding-10">
        <?php echo $a;?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        <div class="panel panel-blueLight padding-10">
            <h3>Note personali</h3>
            <form class="smart-form" id="set_profile" action="ajax_rd4/user/_act.php" novalidate="novalidate" method="POST">
                <section>
                    <label class="label">Visibili solo dai gestori utenti del GAS</label>
                    <label class="textarea"> <i class="icon-append fa fa-question-circle"></i>
                        <input type="hidden" name="act" value="save_user_profile">
                        <textarea name="profile" rows="3" placeholder="Scrivi qualcosa..."><?php echo $U->profile; ?> </textarea>
                    </label>
                </section>
                <footer>
                    <button type="submit" class="btn btn-primary">
                        Salva le modifiche
                    </button>
                </footer>
            </form>
        </div>

        <div class="panel panel-blueLight padding-10">
        Dettagli ordini: <strong><?php echo $dettaglio_ordini; ?></strong><br>
        Ordini gestiti: <strong><?php echo $dettaglio_gestioni; ?></strong><br>
        Ditte inserite: <strong><?php echo $dettaglio_ditte; ?></strong>
        </div>

        <div class="panel panel-blueLight padding-10">
            <h3>Situazione account </h3>
            <form class="smart-form" id="new_stato" action="ajax_rd4/user/_act.php" novalidate="novalidate" method="POST">
                <section>
                    <label class="label">Stato utente</label>
                    <label class="select">
                        <select name="nuovo_stato">
                            <option value="0" <?php if ($U->isactive==0){echo ' selected="SELECTED" ';}?>>Mail non confermata</option>
                            <option value="1" <?php if ($U->isactive==1){echo ' selected="SELECTED" ';}?>>Attivo</option>
                            <option value="2" <?php if ($U->isactive==2){echo ' selected="SELECTED" ';}?>>Sospeso</option>
                            <option value="3" <?php if ($U->isactive==3){echo ' selected="SELECTED" ';}?>>Cancellato</option>
                            <option value="4" <?php if ($U->isactive==4){echo ' selected="SELECTED" ';}?> disabled="disabled">Trasferito</option>
                        </select> <i></i> </label>
                        <div class="note">La modifica è "silente", non verrà inviata nessuna mail all'utente.</div>
                </section>
                <section>
                    <label class="label">Motivo sospensione</label>
                    <label class="input"> <i class="icon-prepend fa fa-ban"></i>
                        <input type="text" name="motivo_sospensione" id="motivo_sospensione" value="<?php echo $U->get_motivo_sospensione()?>">
                   </label>
                </section>
                <footer>
                    <input type="hidden" name="act" value="save_stato_utente">
                    <button type="submit" class="btn btn-primary">
                        Salva le modifiche
                    </button>
                </footer>
            </form>
        </div>
        <div class="panel panel-blueLight padding-10">
            <h3>Contatti utente </h3>
            <form class="smart-form" id="new_email" action="ajax_rd4/user/_act.php" novalidate="novalidate" method="POST">
                <section>
                    <label class="label">Telefono</label>
                    <label class="input"> <i class="icon-prepend fa fa-phone"></i>
                        <input type="text" name="tel" id="tel" value="<?php echo $U->tel?>">
                    </label>
                </section>
                
                <section>
                    <label class="label">Mail primaria</label>
                    <label class="input"> <i class="icon-prepend fa fa-envelope"></i>
                        <input type="email" name="email_utente" id="email_utente" value="<?php echo $U->email?>">
                    </label>
                    <?php
                        if(!$U->has_email_blocked_by_retedes()){
                            echo '<p><i class="fa fa-check text-success"></i> Email funzionante</p>';    
                        }else{
                            $res = json_decode(sparkpostAPIget("suppression-list/".$U->email),TRUE);
                            echo '<p><i class="fa fa-times text-danger"></i> Email <strong>NON</strong> funzionante : '.$U->reason_email_blocked()." ".$res["results"][0]["source"]."</p>";
                        }
                    ?>
                </section>
                
                
                <section>
                    <label class="label">Mail secondaria</label>
                    <label class="input"> <i class="icon-prepend fa fa-envelope"></i>
                        <input type="email" name="email_utente_2" id="email_utente_2" value="<?php echo $U->email_2?>">
                   </label>
                   <?php
                        if($U->email_2<>""){
                            if(!$U->has_email_2_blocked_by_retedes()){
                                echo '<p><i class="fa fa-check text-success"></i> Email funzionante</p>';    
                            }else{
                                $res = json_decode(sparkpostAPIget("suppression-list/".$U->email),TRUE);
                            echo '<p><i class="fa fa-times text-danger"></i> Email <strong>NON</strong> funzionante : '.$U->reason_email_blocked()." ".$res["results"][0]["source"]."</p>";
                           }
                  
                        }
                        
                    ?>
                </section>
                <section>
                    <label class="label">Mail terziaria</label>
                    <label class="input"> <i class="icon-prepend fa fa-envelope"></i>
                        <input type="email" name="email_utente_3" id="email_utente_3" value="<?php echo $U->email_3?>">
                   </label>
                    <?php
                        if($U->email_3<>""){
                            if(!$U->has_email_3_blocked_by_retedes()){
                                echo '<p><i class="fa fa-check text-success"></i> Email funzionante';    
                            }else{
                                $res = json_decode(sparkpostAPIget("suppression-list/".$U->email_3),TRUE);
                                echo '<p><i class="fa fa-times text-danger"></i> Email <strong>NON</strong> funzionante : '.$U->reason_email_blocked()." ".$res["results"][0]["source"]."</p>";
                           }
                       }
                    ?>
                </section>
                
                <footer>
                    <input type="hidden" name="act" value="save_contatti_utente">
                    <button type="submit" class="btn btn-primary">
                        Salva le modifiche
                    </button>
                </footer>
            </form>
        </div>
        <?php if(_USER_PERMISSIONS & perm::puo_gestire_retegas){

            $sql = "SELECT * FROM retegas_gas WHERE id_gas>0 ORDER BY descrizione_gas ASC;";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll();
            foreach($rows as $row){
                    $idgas = $row['id_gas'];
                    $descrizionegas = $row['descrizione_gas'];
                    $targa = '('.$row['targa_gas'].')';
                    if($idgas==$U->id_gas){
                        $selected=' SELECTED="SELECTED" ';
                    }else{
                        $selected='';
                    }
            $h .= "<option value=\"".$idgas ."\" >#".$idgas." ".$descrizionegas ." ".$targa." </option>";
             }//end while


        ?>
        <div class="panel panel-red padding-10">
            <form class="smart-form" id="new_gas" action="ajax_rd4/user/_act.php" novalidate="novalidate" method="POST">
                <section>
                    <label class="label">nuovo GAS utente</label>
                    <label class="select">
                        <select name="nuovo_gas" class="select2" id="nuovo_gas">
                        <?php echo $h;  ?>
                        </select>
                        <div class="note">La modifica è "silente", non verrà inviata nessuna mail all'utente.</div>
                </section>
                <footer>
                    <input type="hidden" name="act" value="save_new_gas">
                    <button type="submit" class="btn btn-danger">
                        Salva le modifiche
                    </button>
                </footer>
            </form>
        </div>
        <?php }?>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        <div class="panel panel-blueLight padding-10">
        <form id="new_username" class="smart-form" action="ajax_rd4/user/_act.php" novalidate="novalidate" method="POST" autocomplete="off">
        <header>
            Nuovo username per <?php echo $U->fullname; ?>
        </header>
        <fieldset>
            <section>
                <label class="label">Username</label>
                <label class="input"> <i class="icon-prepend fa fa-user"></i>
                    <input type="text" name="username" id="new_username" value="<?php echo $U->username?>" autocomplete="off">
               </label>
            </section>
       </fieldset>
       <footer>
            <input type="hidden" name="act" value="save_new_username">
            <button type="submit" class="btn btn-primary" id="save_new_username">
                Salva nuovo username
            </button>
        </footer>
       </form>
       <hr>
       <form id="new_password" class="smart-form" action="ajax_rd4/user/_act.php" novalidate="novalidate" method="POST" autocomplete="off">
        <header>
            Nuova password per <?php echo $U->fullname; ?>
        </header>
        <fieldset>
            <section>
                <label class="label">Password</label>
                <label class="input"> <i class="icon-prepend fa fa-lock"></i>
                    <input type="text" name="password" id="new_password" value="" autocomplete="off">
                </label>
            </section>
       </fieldset>
       <footer>
            <input type="hidden" name="act" value="save_new_password">
            <button type="submit" class="btn btn-primary" id="save_new_password">
                Salva nuova password
            </button>
        </footer>
       </form>

       </div>

       <div class="panel panel-blueLight padding-10">
            <h3>Indirizzo</h3>
            <p class="alert alert-info">L'indirizzo deve essere valido (riconosciuto da google) per poter essere salvato.</p>
            <form class="smart-form" id="set_address" action="ajax_rd4/user/_act.php" novalidate="novalidate" method="POST">
                <section>
                    <label class="label">Indirizzo</label>
                    <label class="input"> <i class="icon-append fa fa-map-marker"></i>
                        <input type="hidden" name="act" value="save_user_address">
                        <input type="hidden" id="u_gc_lat" name="u_gc_lat" value="0">
                        <input type="hidden" id="u_gc_lng" name="u_gc_lng" value="0">
                        <input type="text" id="country" name="country" value="<?php echo $U->country?>">
                    </label>
                </section>
                <section>
                    <label class="label">Città</label>
                    <label class="input"> <i class="icon-append fa fa-map-marker"></i>
                        <input type="text" id="city" name="city" value="<?php echo $U->city?>">
                    </label>
                </section>
                
                <p id="riconosciuto" class=""><i class="fa fa-spin fa-spinner"></i></p>

                <footer>
                    <button type="submit" class="btn btn-primary" id="salva_indirizzo">
                        Salva le modifiche
                    </button>
                </footer>
            </form>
        </div>

    </div>
</div>

<div class="row">
    <div class="col col-xs-12">
        <h1>Tabella opzioni</h1>
        <p class="alert alert-info">Questa tabella contiene tutti i parametri di reteDES associati all'utente. E' in sola lettura e permette una gestione più veloce di eventuali problematiche.</p>
        <div style="overflow-x:auto; height:480px;">
            <?php echo $t_o; ?>
        </div>
    </div>
</div>


<script type="text/javascript">
    pageSetUp();



    var pagefunction = function(){

        //------------HELP WIDGET
        <?php echo help_render_js($page_id);?>
        //------------END HELP WIDGET

        var delay = (function(){
          var timer = 0;
          return function(callback, ms){
            clearTimeout (timer);
            timer = setTimeout(callback, ms);
          };
        })();



        function startTable(){
                // clears memory even if nothing is in the function
                 $("#opzioni_table")
                    .bind("updateComplete",function(e, table) {
                        console.log("updated");

                    });


                $.extend($.tablesorter.themes.bootstrap, {
                    table      : 'table table-bordered',
                    caption    : 'caption',
                    sortNone   : 'bootstrap-icon-unsorted',
                    sortAsc    : 'fa fa-arrow-up',
                    sortDesc   : 'fa fa-arrow-down'

                  });


                t = $('#opzioni_table').tablesorter({
                    theme: 'bootstrap',
                        //debug:true,
                        widgets: ["uitheme","filter","zebra"],
                     widgetOptions : {
                         zebra : ["even", "odd"],
                         filter_reset : ".reset",

                        }
                });
        }


        $('.abilitazioni').change(function () {
            if(this.checked) {value = 1;}else{value = 0;}
            var id = $(this).data('userid');
            var tipo = $(this).data('tipo');
            $.ajax({
              type: "POST",
              url: "ajax_rd4/user/_act.php",
              dataType: 'json',
              data: {act: "abilitazioni", value : value, id:id, tipo:tipo},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                        ok(data.msg);}else{ko(data.msg);}
            });


        });
        $('.delete').change(function () {
            if(this.checked) {value = 1;}else{value = 0;}
            var id = $(this).data('userid');
            var tipo = $(this).data('tipo');
            $.SmartMessageBox({
                title : "Elimini questo utente?",
                content : "Questa operazione non può essere annullata (a meno che lui si ri-iscriva)",
                buttons : "[Esci][Elimina]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="Elimina"){
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/user/_act.php",
                          dataType: 'json',
                          data: {act: "del_user", id : id},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                ok(data.msg);

                            }else{
                                ko(data.msg);

                            ;}

                        });
                }
            });


        });


        $("#nuovo_gas").select2('data', {id: '<?PHP echo $U->id_gas; ?>', text: "<?php echo $U->descrizione_gas; ?>"});
        var $new_gas = $('#new_gas').validate({
        // Rules for form validation
            rules : {},
            messages : {},
            submitHandler : function(form) {

                $(form).ajaxSubmit({
                    type:"POST",
                    dataType: 'json',
                    data: {id : '<?php echo $converter->encode($U->userid);?>'},
                    success : function(data) {
                            if(data.result=="OK"){
                                ok(data.msg);
                            }else{
                                ko(data.msg);}
                            }
                });
            },
            // Do not change code below
            errorPlacement : function(error, element) {
                error.insertAfter(element.parent());
            }
        });

        var $new_email = $('#new_email').validate({
        // Rules for form validation
            rules : {email_utente:{required : true}},
            messages : {email_utente : {required : 'Per cortesia inserisci qualcosa... '}},
            submitHandler : function(form) {

                $(form).ajaxSubmit({
                    type:"POST",
                    dataType: 'json',
                    data: {id : '<?php echo $converter->encode($U->userid);?>'},
                    success : function(data) {
                            if(data.result=="OK"){
                                ok(data.msg);
                            }else{
                                ko(data.msg);}
                            }
                });
            },
            // Do not change code below
            errorPlacement : function(error, element) {
                error.insertAfter(element.parent());
            }
        });
        
        var $new_stato = $('#new_stato').validate({
        // Rules for form validation
            rules : {},
            messages : {},
            submitHandler : function(form) {

                $(form).ajaxSubmit({
                    type:"POST",
                    dataType: 'json',
                    data: {id : '<?php echo $converter->encode($U->userid);?>'},
                    success : function(data) {
                            if(data.result=="OK"){
                                ok(data.msg);
                            }else{
                                ko(data.msg);}
                            }
                });
            },
            // Do not change code below
            errorPlacement : function(error, element) {
                error.insertAfter(element.parent());
            }
        });

        var $new_username = $('#new_username').validate({
        // Rules for form validation
            rules : {username:{required : true}},
            messages : {username : {required : 'Per cortesia inserisci qualcosa... max 15 caratteri!'}},
            submitHandler : function(form) {
                $('#save_new_username').fadeOut();
                $(form).ajaxSubmit({
                    type:"POST",
                    dataType: 'json',
                    data: {id : <?php echo $U->userid;?>},
                    success : function(data) {
                            if(data.result=="OK"){
                                ok(data.msg);
                            }else{
                                ko(data.msg);}
                            }
                });
            },
            // Do not change code below
            errorPlacement : function(error, element) {
                error.insertAfter(element.parent());
            }
        });

        var $new_password = $('#new_password').validate({
        // Rules for form validation
            rules : {username:{required : true}},
            messages : {username : {required : 'Per cortesia inserisci qualcosa...!'}},
            submitHandler : function(form) {
                $('#save_new_password').hide();
                $(form).ajaxSubmit({
                    type:"POST",
                    dataType: 'json',
                    data: {id : '<?php echo $converter->encode($U->userid);?>'},
                    success : function(data) {
                            if(data.result=="OK"){
                                ok(data.msg);
                            }else{
                                ko(data.msg);}

                            }
                });
            },
            // Do not change code below
            errorPlacement : function(error, element) {
                error.insertAfter(element.parent());
            }
        });
        var $set_profile = $('#set_profile').validate({
        // Rules for form validation
            rules : {},
            submitHandler : function(form) {

                $(form).ajaxSubmit({
                    type:"POST",
                    dataType: 'json',
                    data: {id : '<?php echo $converter->encode($U->userid);?>'},
                    success : function(data) {
                            if(data.result=="OK"){
                                ok(data.msg);
                            }else{
                                ko(data.msg);}

                            }
                });
            },
            // Do not change code below
            errorPlacement : function(error, element) {
                error.insertAfter(element.parent());
            }
        });


        var $set_address = $('#set_address').validate({
        // Rules for form validation
            rules : {
                country:{required : true},
                city:{required : true}
            },
            messages : {
                country : {required : 'Non può essere vuoto'},
                city : {required : 'Non può essere vuoto'}
            },

            submitHandler : function(form) {

                $(form).ajaxSubmit({
                    type:"POST",
                    dataType: 'json',
                    data: {id : '<?php echo $converter->encode($U->userid);?>'},
                    success : function(data) {
                            if(data.result=="OK"){
                                ok(data.msg);
                            }else{
                                ko(data.msg);}

                            }
                });
            },
            // Do not change code below
            errorPlacement : function(error, element) {
                error.insertAfter(element.parent());
            }
        });

        /*GEOCODER*/
        var geocoder;


        function initialize() {
          geocoder = new google.maps.Geocoder();
          console.log("Fine mappa initialized");
        }

        function codeAddress() {
              //var markers = [];
              var address = document.getElementById('country').value + " " + document.getElementById('city').value;

              console.log("Geocoding... "+address);
              geocoder.geocode( { 'address': address}, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {

                  $('#u_gc_lat').val(results[0].geometry.location.lat());
                  $('#u_gc_lng').val(results[0].geometry.location.lng());
                  $('#riconosciuto').html('<p class="text-success"><i class="fa fa-check"></i> Indirizzo correttamente riconosciuto:<br><span class="note">'+ results[0].formatted_address +'</span></p>');

                  console.log("Riconosciuto: " + results[0].geometry.location.lat());
                  $('#salva_indirizzo').prop('disabled', false);

                } else {
                  //alert('Geocode was not successful for the following reason: ' + status);
                  ko("Indirizzo non riconosciuto :(");
                  console.log("Non Riconosciuto");
                  $('#riconosciuto').html('<p class="text-danger"><i class="fa fa-ban"> Indirizzo NON riconosciuto</p>');
                  $('#salva_indirizzo').prop('disabled', true);
                }

              });

        }
        /*GEOCODER*/

        loadScript("js_rd4/plugin/tablesorter/js/jquery.tablesorter.widgets.js",startTable);

        initialize();
        codeAddress();

        $('#country,#city').keyup(function() {
            delay(function(){
              codeAddress();
            }, 1000 );
        });

    }


    function loadTables(){
        loadScript("js_rd4/plugin/tablesorter/js/jquery.tablesorter.min.js",
            loadScript("js/plugin/jquery-form/jquery-form.min.js", pagefunction)
        );
    }
    $(window).unbind('gMapsLoaded');
    $(window).bind('gMapsLoaded',loadTables);
    window.loadGoogleMaps();



</script>