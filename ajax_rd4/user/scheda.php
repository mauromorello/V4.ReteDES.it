<?php
require_once("inc/init.php");

$ui = new SmartUI;
$page_title = "Scheda utente";
$converter = new Encryption;
    $userid = CAST_TO_STRING($_POST["id"]);
if($userid==""){
    $userid = CAST_TO_STRING($_GET["id"]);
}
$userid = CAST_TO_INT($converter->decode($userid),0);


$u="userid : $userid";
$stmt = $db->prepare("SELECT U.user_permission, U.fullname, U.userid, U.tel, U.id_gas, U.email, G.descrizione_gas, G.id_referente_gas FROM  maaking_users U INNER JOIN retegas_gas G ON G.id_gas=U.id_gas  WHERE userid = :userid LIMIT 1;");
$stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if(($stmt->rowCount()<>1) or ($row["userid"]==0)){
    echo "Not allowed $userid";die();
}

$useridEnc = $converter->encode($row["userid"]);

$u='<div class="row">
        <div class="col-sm-6">
            <div class="well">
            <span class="pull-right">
                <div class="polaroid-images">
                    <a href="javascript:void(0)" title="'.$row["fullname"].'">
                    <img src="'.src_user($row["userid"],240).'" class="" style="width:120px;height:120px;">
                    </a>
                </div>
            </span>
            <label>Identificativo</label>
            <p class="font-lg">'.$row["userid"].'</p>
            <label>Nome</label>
            <p class="font-lg">'.$row["fullname"].'</p>
            <label>Gas</label>
            <p class="font-lg">'.$row["descrizione_gas"].'</p>
            <label>Telefono</label>
            <p class="font-lg"><a href="tel:'.$row["tel"].'" class="pull-right"><i class="fa fa-phone"></i></a>  '.$row["tel"].'</p>
            <label>Email</label>
            <p class="font-lg"><a href="mailto:'.$row["email"].'" target="_BLANK" class="pull-right"><i class="fa fa-envelope"></i></a>  '.$row["email"].'</p>
            </div>
        </div>
        <div class="col-sm-6">
            <div>
            <form class="form">
            <div class="form-group">
                <label>Manda un messaggio</label>
                <textarea class="form-control" placeholder="Scrivi qua..." rows="3" style="margin-top: 0px; margin-bottom: 0px; height: 166px;" id="usermessage"></textarea>
            </div>
            <button class="btn btn-info pull-right margin-top-10"  type="submit" id="usermessage_go" data-userid="'.$userid.'">Invia</button>
            </form>

            </div>
        </div>
    </div>';


$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>false,
                    "deletebutton"=>false,
                    "colorbutton"=>true);
$wg_scheda_user = $ui->create_widget($options);
$wg_scheda_user->id = "wg_scheda_utente";
$wg_scheda_user->body = array("content" => $u,"class" => "");
$wg_scheda_user->header = array(
    "title" => '<h2>Profilo</h2>',
    "icon" => 'fa fa-user'
    );


if(_USER_PERMISSIONS & perm::puo_gestire_utenti){
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
    }
    //ha gestito ordini ?
    $stmt = $db->prepare("SELECT count(*) as conto from retegas_referenze where id_utente_referenze=:id_utente;");
    $stmt->bindParam(':id_utente', $id_utente, PDO::PARAM_INT);
    $stmt->execute();
    $r = $stmt->fetch();
    if($r["conto"]>0){
        $allow_del = false;
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
//USER EDIT HELP
$stmt = $db->prepare("SELECT valore_text FROM retegas_options WHERE id_user=".$row["userid"]." AND chiave='_USER_PUO_MODIFICARE_HELP' LIMIT 1;");
$stmt->execute();
$h = $stmt->fetch(PDO::FETCH_ASSOC);
if($h["valore_text"]=='SI'){
    $help_checked='checked="CHECKED"';
}else{
    $help_checked='';
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
                <input class="abilitazioni" type="checkbox" data-userid="'.$useridEnc.'" data-tipo="'.perm::puo_eliminare_messaggi.'" name="checkbox-toggle" '.$bacheca_checked.'>
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>Moderazione feedback utenti.
            </label>
            <label class="toggle">
                <input class="abilitazioni" type="checkbox" data-userid="'.$useridEnc.'" data-tipo="gestione_help" name="checkbox-toggle" '.$help_checked.'>
                <i data-swchon-text="SI" data-swchoff-text="NO"></i>Gestire gli HELP.
            </label>
            <label class="toggle">
                <input class="abilitazioni" type="checkbox" data-userid="'.$useridEnc.'" data-tipo="gestione_retedes" name="checkbox-toggle" '.$retedes_checked.'>
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

$wg_scheda_user_amm = $ui->create_widget($options);
$wg_scheda_user_amm->id = "wg_scheda_utente_amm";
$wg_scheda_user_amm->body = array("content" => $a,"class" => "");
$wg_scheda_user_amm->header = array(
    "title" => '<h2>Amministra</h2>',
    "icon" => 'fa fa-user'
    );





?>
<div class="inbox-nav-bar no-content-padding">
    <h1 class="page-title txt-color-blueDark"><i class="fa fa-fw fa-user"></i> Scheda Utente &nbsp;</h1>
</div>

<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html("scheda_user",$page_title); ?>
            <?php echo $wg_scheda_user->print_html(); ?>
            <?php if(
                        (_USER_PERMISSIONS & perm::puo_gestire_utenti)
                         AND ($row["id_gas"]==_USER_ID_GAS)
                         OR
                         (_USER_PERMISSIONS & perm::puo_vedere_retegas)
                         OR
                         ($row["id_referente_gas"]==_USER_ID)
                         )
                        {echo $wg_scheda_user_amm->print_html();} ?>
        </article>
    </div>
</section>


<script type="text/javascript">
    pageSetUp();

    var pagefunction = function(){

        //------------HELP WIDGET
        <?php echo help_render_js("scheda_user");?>
        //------------END HELP WIDGET

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

        var id;
        var messaggio;

        $(document).on( 'change', '#usermessage', function() {
            messaggio = $(this).val();
            console.log("messaggio = " + messaggio);

        });

        $('#usermessage_go').click(function(){
        //$('body').on('click', '#usermessage_go', function () {
            //invio il messaggio ciccio
            id=$('#usermessage_go').data("userid");

            $.ajax({
              type: "POST",
              url: "ajax_rd4/gas/_act.php",
              dataType: 'json',
              data: {act: "messaggia", messaggio : messaggio, id:id},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                    ok(data.msg);
                    messaggio='';
                    $("#usermessage").val("");
                }else{
                    ko(data.msg);
                }
            });

        });


    }

    pagefunction();
</script>