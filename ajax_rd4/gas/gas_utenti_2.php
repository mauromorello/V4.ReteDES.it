<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.gas.php");
$ui = new SmartUI;
$converter = new Encryption;

$G= new gas(_USER_ID_GAS);

$page_title = "Utenti GAS avanzata";
$page_id= "utenti_gas_avanzata";

if((_USER_PERMISSIONS & perm::puo_gestire_utenti) OR (_USER_ID==$G->id_referente_gas) OR (_USER_PERMISSIONS & perm::puo_vedere_retegas)){

}else{
    rd4_go_back("Non puoi...");
}


/*PERMESSI NUOVI UTENTI*/


//PARTECIPA
if($G->default_permission & perm::puo_partecipare_ordini){
    $partecipa_checked='checked="CHECKED"';
}else{
    $partecipa_checked='';
}
//CREA ORDINI
if($G->default_permission & perm::puo_creare_ordini){
    $creare_ordini_checked='checked="CHECKED"';
}else{
    $creare_ordini_checked='';
}
//DITTE E LISTINI
if($G->default_permission & perm::puo_creare_listini){
    $ditte_checked='checked="CHECKED"';
}else{
    $ditte_checked='';
}
if($G->default_permission & perm::puo_creare_ditte){
    $ditte_checked='checked="CHECKED"';
}else{
    $ditte_checked='';
}
//POSTARE
if($G->default_permission & perm::puo_postare_messaggi){
    $messaggi_checked='checked="CHECKED"';
}else{
    $messaggi_checked='';
}
//OPERARE
if($G->default_permission & perm::puo_operare_con_crediti){
    $crediti_checked='checked="CHECKED"';
}else{
    $crediti_checked='';
}
//AMICI
if($G->default_permission & perm::puo_avere_amici){
    $amici_checked='checked="CHECKED"';
}else{
    $amici_checked='';
}

    $t = '<table id="utenti_custom" class="smart-form">
            <thead>
                <th class="filter-false"></th>
                <th class="filter-false"></th>
                <th class="filter-select">Stato</th>
                <th>ID/TESSERA</th>
                <th>Nome</th>
                <th>Indirizzo</th>
                <th>'.$G->custom_1_nome.'</th>
                <th>'.$G->custom_2_nome.'</th>
                <th>'.$G->custom_3_nome.'</th>
                <th class="filter-select">Cassa</th>
                <th>Ultima attività</th>
            </thead>
            <tbody>';

    $stmt = $db->prepare("SELECT * FROM  maaking_users WHERE id_gas = '"._USER_ID_GAS."'");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {

            $art_desc = iconv('UTF-8', 'UTF-8//IGNORE', $row["art_desc"]);

            $useridEnc = $converter->encode($row["userid"]);

            $indirizzo=$row["country"].'<br><span class="note">'.$row["city"].'</span>';
            if(CAST_TO_INT($row["user_gc_lat"]>0)){
                $geo='<i class="fa fa-check text-success"></i>';
            }else{
                $geo='<i class="fa fa-ban text-danger"></i>';
            }



            //SE NON E' SI/NO
            if($G->custom_1_tipo==2){
                if(($row["custom_1"]<>"SI") AND ($row["custom_1"]<>"NO") ){
                    $class_custom_1=' text-danger ';
                }else{
                    $class_custom_1='';
                }
            }
            if($G->custom_2_tipo==2){
                if(($row["custom_2"]<>"SI") AND ($row["custom_2"]<>"NO") ){
                    $class_custom_2=' text-danger ';
                }else{
                    $class_custom_2='';
                }
            }


            if($row["isactive"]==0){
                $stato="ATTESA";
            }
            if($row["isactive"]==1){
                $stato="ATTIVO";
            }
            if($row["isactive"]==2){
                $stato="SOSPESO";
            }
            if($row["isactive"]==3){
                $stato="CANCELLATO";
            }
            if($row["isactive"]==4){
                $stato="TRASFERITO";
            }

            //FROM USER CASSA
            $stmtC = $db->prepare("SELECT * FROM retegas_options WHERE id_user=".$row["userid"]." AND chiave='_USER_USA_CASSA' LIMIT 1;");
            $stmtC->execute();
            $rowC = $stmtC->fetch(PDO::FETCH_ASSOC);
            $rowC["valore_text"]=='SI' ? $ha_cassa="SI": $ha_cassa="NO";

            //BOUNCED EMAIL
            if(cannotSend($row["email"])){
                $bounced='<br><span class="fa-stack unban_user"  data-id="'.$row["userid"].'">
                                      <i id="v4_connection" class="fa fa-envelope fa-stack-1x text-info"></i>
                                      <i id="v4_poor_connection" class="fa fa-ban fa-stack-2x text-danger" title="Mail non funzionante"></i>
                                    </span>';
            }else{
                $bounced='';    
            }

            $t .= '<tr>';
                $t .= '<td data-math="ignore"><label class="checkbox"><input type="checkbox" class="utente" value="'.$row["userid"].'"><i></i></label></td>';
                $t .= '<td data-math="ignore" class="text-center"><a href="#ajax_rd4/user/scheda.php?id='.$useridEnc.'"><IMG SRC="'.src_user($row["userid"]).'" class="img" style="width:32px;"></a></td>';
                $t .= '<td data-math="ignore" >'.$stato.$bounced.'</td>';
                $t .= '<td data-math="ignore" class="text-left"><a href="#ajax_rd4/user/gestisci.php?id='.$useridEnc.'" alt="gestisci" title="gestisci"><i class="fa fa-gear"></i></a> '.$row["userid"].'<br><span data-pk="'.$row["userid"].'" class="tessera-edit">'.$row["tessera"].'</span></td>';
                $t .= '<td data-math="ignore"><span data-pk="'.$row["userid"].'" class="fullname-edit">'.$row["fullname"].'</span><br><span class="note mail-edit" data-pk="'.$row["userid"].'" >'.$row["email"].'</span><br><span class="note tel-edit" data-pk="'.$row["userid"].'" >'.$row["tel"].'</span></td>';
                $t .= '<td data-math="ignore">'.$geo.' '.$indirizzo.'</td>';

                $t .= '<td><span data-pk="'.$row["userid"].'" class="custom_1-edit  btn-block '.$class_custom_1.'">'.$row["custom_1"].'</span></td>';
                $t .= '<td><span data-pk="'.$row["userid"].'" class="custom_2-edit  btn-block">'.$row["custom_2"].'</span></td>';
                $t .= '<td><span data-pk="'.$row["userid"].'" class="custom_3-edit  btn-block">'.$row["custom_3"].'</span></td>';
                $t .= '<td>'.$ha_cassa.'</td>';
                $t .= '<td>'.conv_datetime_from_db($row["last_activity"]).'</td>';
                //$t .= '<td class="font-md"><span data-pk="'.$row["userid"].'" class="custom_4-edit  btn-block">'.$row["custom_4"].'</span></td>';

            $t .= '</tr>';
        }

        if($G->custom_1_tipo==1){
            $somma_custom_1=' data-math="col-sum" class="text-center" ';
        }
        if($G->custom_2_tipo==1){
            $somma_custom_2=' data-math="col-sum"  class="text-center" ';
        }
        if($G->custom_3_tipo==1){
            $somma_custom_3=' data-math="col-sum" class="text-center" ';
        }

        $t .= '</tbody>
                <tbody class="tablesorter-infoOnly">
                <tr>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th '.$somma_custom_1.'></th>
                  <th '.$somma_custom_2.'></th>
                  <th '.$somma_custom_3.'></th>
                  <th></th>
                </tr>
              </tbody>
              </table>

              ';

//GRAFICO
$stmt = $db->prepare("SELECT count(*) FROM  maaking_users WHERE id_gas='"._USER_ID_GAS."'");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_NUM);
    $user_Tutti = $row[0];

    $stmt = $db->prepare("SELECT count(*) FROM  maaking_users WHERE id_gas='"._USER_ID_GAS."' and isactive=1");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_NUM);
    $user_Attivi = $row[0];

    $stmt = $db->prepare("SELECT count(*) FROM  maaking_users WHERE id_gas='"._USER_ID_GAS."' and isactive=2");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_NUM);
    $user_Sospesi = $row[0];

    $stmt = $db->prepare("SELECT count(*) FROM  maaking_users WHERE id_gas='"._USER_ID_GAS."' and isactive=3");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_NUM);
    $user_Eliminati = $row[0];

    $stmt = $db->prepare("SELECT count(*) FROM  maaking_users WHERE id_gas='"._USER_ID_GAS."' and isactive=0");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_NUM);
    $user_Attesa = $row[0];
    if ($user_Attesa>0){
        $btn_Attesa = '<div class="alert alert-info margin-top-10">Ci sono <strong>'.$user_Attesa.'</strong> utenti che attendono di essere attivati</div>';
    }else{
        $btn_Attesa = "";
    }


    /*SOSPENSIONE ACCOUNT*/
    $sql="SELECT valore_int, valore_text FROM retegas_options
            WHERE
            id_gas = :id_gas
            AND
            chiave = '_GAS_SOSPENSIONE_UTENTI'
            LIMIT 1;";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id_gas', $G->id_gas, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();
    $giorni_sospensione = CAST_TO_INT($row["valore_int"],0);
    $frase_sospensione = CAST_TO_STRING($row["valore_text"]);



?>
<?php echo $G->render_toolbar("Gestione Avanzata "); ?>


<section>
<div class="row margin-top-10">
    <div class="col col-xs-12 col-md-6 ">
        <h1>Inserisci un nuovo utente</h1>
        <form action="" id="smart-form-register" class="smart-form" method="POST">
            <div class="well well-sm padding-10">
            <p class="alert alert-info">Inserendo un nuovo utente da questa scheda lo si rende attivo da subito. Verrà inviata contestualmente all\'iscrizione una mail per avvisarlo, che risulterà proveniente dall\'user che lo ha iscritto.
                                            Chi inserisce un nuovo utente si assume la responsabilità di accettare regole e disclaimer per conto terzi.
                                            </p>

                <section>
                    <label class="input"> <i class="icon-append fa fa-user"></i>
                        <input id="fullname" type="text" name="fullname" placeholder="Nome e cognome">
                        <b class="tooltip tooltip-bottom-right">Il nome e cognome reale</b> </label>
                </section>

                <section>
                    <label class="input"> <i class="icon-append fa fa-user"></i>
                        <input id="username" type="text" name="username" placeholder="username">
                        <b class="tooltip tooltip-bottom-right">Nome utente usato per accedere</b> </label>
                </section>
                <section>
                    <label class="input"> <i class="icon-append fa fa-envelope-o"></i>
                        <input id="email" type="email" name="email" placeholder="Email">
                        <b class="tooltip tooltip-bottom-right">Inserisci un email valida!</b> </label>
                </section>

                <section>
                    <label class="input"> <i class="icon-append fa fa-lock"></i>
                        <input id="password" type="password" name="password" placeholder="Password" id="password">
                        <b class="tooltip tooltip-bottom-right">Inserisci la password</b> </label>
                </section>

                <section>
                    <label class="input"> <i class="icon-append fa fa-lock"></i>
                        <input id="password2" type="password" name="passwordConfirm" placeholder="Ripeti password">
                        <b class="tooltip tooltip-bottom-right">Controllo password</b> </label>
                </section>

                <section>
                    <label class="input"> <i class="icon-append fa fa-phone"></i>
                        <input id="tel" type="text" name="tel" placeholder="Telefono">
                        <b class="tooltip tooltip-bottom-right">Il suo recapito telefonico</b> </label>
                </section>
                <!--<section>
                    <label class="checkbox">
                        <input type="checkbox"  name="puo_partecipare" id="puo_partecipare" checked="checked">
                        <i></i>Può partecipare agli ordini</label>
                    <label class="checkbox">
                        <input type="checkbox" name="puo_gestire" id="puo_gestire" checked="checked">
                        <i></i>Può gestire ordini</label>
                </section>-->



            <footer>
                <button type="submit" class="btn btn-primary">
                    Inserisci
                </button>
            </footer>
            </div>
        </form>

        </div>

        <div class="col col-xs-12 col-md-6">
            <h1>Inserisci MOLTI utenti</h1>

            <div class="well well-sm">
                <label>Importa da un file csv o excel</label>

                <div class="btn-group pull-right">
                    <span class="btn btn-default fileinput-button dz-clickable">
                        <i class="fa fa-spinner fa-spin hidden" id="loadingSpinner"></i>
                        <span>Carica...</span>
                    </span>
                </div>
                <div class="clearfix "></div>
                <div class="progress progress-micro margin-top-10">
                    <div class="progress-bar progress-bar-primary" role="progressbar" style="width: 0;" id="loadingprogress"></div>
                </div>
                <p class="alert alert-info">Leggere l'help per capire come fare.</p>
            </div>
            <hr>
            <h1>Permessi di default nuovi utenti</h1>

            <form class="smart-form">
              <div class="well well-sm padding-10">
                <label class="toggle margin-top-5">
                    <input  id="default_puo_gestire_ordini" type="checkbox"  data-tipo="<?php echo perm::puo_creare_ordini?>" name="checkbox-toggle" <?php echo $creare_ordini_checked;?> >
                    <i data-swchon-text="SI" data-swchoff-text="NO"></i>Può creare e gestire Ordini
                </label>
                <label class="toggle">
                    <input  id="default_puo_partecipare_ordini" type="checkbox"  data-tipo="<?php echo perm::puo_partecipare_ordini?>" name="checkbox-toggle" <?php echo $partecipa_checked ?>>
                    <i data-swchon-text="SI" data-swchoff-text="NO"></i>Può partecipare agli ordini
                </label>
                <label class="toggle">
                    <input  id="default_puo_avere_amici" type="checkbox"  data-tipo="<?php echo perm::puo_avere_amici; ?>" name="checkbox-toggle" <?php echo $amici_checked ?>>
                    <i data-swchon-text="SI" data-swchoff-text="NO"></i>Può usare la rubrica "Amici"
                </label>
                <label class="toggle">
                    <input  id="default_puo_creare_ditte" type="checkbox"  data-tipo="<?php echo perm::puo_creare_ditte?>" name="checkbox-toggle" <?php echo  $ditte_checked ?>>
                    <i data-swchon-text="SI" data-swchoff-text="NO"></i>Può creare e gestire Fornitori
                </label>
                <label class="toggle">
                    <input  id="default_puo_operare_con_crediti" type="checkbox"  data-tipo="<?php echo perm::puo_operare_con_crediti?>" name="checkbox-toggle" <?php echo $crediti_checked ?>>
                    <i data-swchon-text="SI" data-swchoff-text="NO"></i>Può movimentare crediti altrui
                </label>
                <label class="toggle">
                    <input  id="default_puo_postare_messaggi" type="checkbox"  data-tipo="<?php echo perm::puo_postare_messaggi ?>" name="checkbox-toggle" <?php echo $messaggi_checked; ?>>
                    <i data-swchon-text="SI" data-swchoff-text="NO"></i>Può commentare ditte e ordini
                </label>
            </div>
           </form>
           <hr>
           <h1>Composizione GAS</h1>
           <div class="easy-pie-chart txt-color-green" data-percent="<?php echo CAST_TO_INT(($user_Attivi/$user_Tutti)*100)?>" data-size="136" data-pie-size="20">ATTIVI: <?php echo $user_Attivi?> su <?php echo $user_Tutti?></div>
           <div class="easy-pie-chart txt-color-yellow " data-percent="<?php echo CAST_TO_INT(($user_Sospesi/$user_Tutti)*100)?>" data-size="136" data-pie-size="20">SOSPESI: <?php echo $user_Sospesi?> su <?php echo $user_Tutti?></div>
           <div class="easy-pie-chart txt-color-red" data-percent="<?php echo CAST_TO_INT(($user_Eliminati/$user_Tutti)*100)?>" data-size="136" data-pie-size="20">ELIMINATI: <?php echo $user_Eliminati?> su <?php echo $user_Tutti?></div>
           <hr>
           <?php echo $btn_Attesa."<hr>"; ?>

        </div>
</div>
</section>
<div class="row margin-top-10" >
    <h1>Campi utenti</h1>

    <form class="smart-form margin-top-10">
        <div class="row padding-10">
            <section class="col col-6 col-xs-12">
                <label class="toggle">
                    <input type="checkbox" name="qta_ord-toggle" id="custom_1-toggle">
                    <i data-swchon-text="SI" data-swchoff-text="NO"></i>Modifica la colonna <b><?php echo $G->custom_1_nome; ?></b>
                </label>
                <label class="toggle">
                    <input type="checkbox" name="qta_ord-toggle" id="custom_2-toggle">
                    <i data-swchon-text="SI" data-swchoff-text="NO"></i>Modifica la colonna <b><?php echo $G->custom_2_nome; ?></b>
                </label>
                <label class="toggle">
                    <input type="checkbox" name="qta_ord-toggle" id="custom_3-toggle">
                    <i data-swchon-text="SI" data-swchoff-text="NO"></i>Modifica la colonna <b><?php echo $G->custom_3_nome; ?></b>
                </label>
            </section>
            <section class="col col-6 col-xs-12">
                
                <label class="toggle">
                    <input type="checkbox" name="" id="fullname-toggle">
                    <i data-swchon-text="SI" data-swchoff-text="NO"></i>Modifica la colonna del nome utente</b>
                </label>
                <label class="toggle">
                    <input type="checkbox" name="" id="mail-toggle">
                    <i data-swchon-text="SI" data-swchoff-text="NO"></i>Modifica la mail dell'utente</b>
                </label>
                <label class="toggle">
                    <input type="checkbox" name="" id="tel-toggle">
                    <i data-swchon-text="SI" data-swchoff-text="NO"></i>Modifica il telefono dell'utente</b>
                </label>
                <label class="toggle">
                    <input type="checkbox" name="" id="tessera-toggle">
                    <i data-swchon-text="SI" data-swchoff-text="NO"></i>Modifica il numero tessera dell'utente</b>
                </label>
            </section>
        </div>
    </form>

    <div class="dettaglio_wrapper table-responsive" style="height:400px; max-height:400px; overflow-y:auto !important; overflow-x:auto">
        <?php echo $t ?>
    </div>
    <div class="well margin-top-5">
        <label class="pull-right ">
        <input class="selectall" type="checkbox"> Seleziona / deseleziona tutti
        </label>
        <h1>Operazioni sugli utenti selezionati (<span id="numero_utenti_selezionati">0</span>)</h1>
        <hr>
        <p><button class="btn btn-default" id="manda_messaggio_a_utenti"><i class="fa fa-envelope"></i>   Manda un messaggio.</button></p>
        <label>Messaggio:</label>
        <textarea  id="messaggio_a_utenti" style="width:100%;"></textarea>
        <p></p>
        <div class="alert alert-info"><strong>ATTENZIONE:</strong> Non abusare di questa funzione. A nessuno piace ricevere mail inutili.</div>
        <hr>
        <p>
            <button class="btn btn-success" id="do_attiva_utenti"><i class="fa fa-plus"></i>   Attivali</button>
            <button class="btn btn-warning" id="do_sospendi_utenti"><i class="fa fa-ban"></i>   Sospendili</button>
            <button class="btn btn-danger" id="do_cancella_utenti"><i class="fa fa-times"></i>   Cancellali</button>
        </p>
        <div class="alert alert-info"><strong>NB:</strong> Gli utenti attivati saranno avvisati con una mail, quelli sospesi e cancellati no.</div>



    </div>

    <div class="row">
        <div class="col col-md-6 col-lg-6">
            <div class="margin-top-5 well well-sm">
                <h1>Gestione sospensione account</h1>
                <hr>
                <form action="" id="sospensione_utenti_form" class="smart-form" method="POST">
                    <div class=" padding-10">

                    <section>
                        <label class="input"> <i class="icon-append fa fa-clock-o"></i>
                            <input id="giorni_sospensione" type="text" name="giorni_sospensione" placeholder="Giorni di inattività" value="<?php echo $giorni_sospensione; ?>">
                            <b class="tooltip tooltip-bottom-right">Dopo questi giorni di inattività l'utente viene sospeso</b> </label>
                            <p class="note">0 = nessuna sospensione</p>
                    </section>
                    <section>
                        <label class="input"> <i class="icon-append fa fa-user"></i>
                            <input id="frase_sospensione" type="text" name="frase_sospensione" placeholder="Motivo sospensione" value="<?php echo $frase_sospensione; ?>">
                            <b class="tooltip tooltip-bottom-right">Questo messaggio comparirà all'utente la prima volta che proverà ad accedere.</b> </label>
                    </section>

                    <footer>
                        <button type="submit" class="btn btn-primary">
                            Aggiorna
                        </button>
                    </footer>
                    </div>
                </form>
            </div>
        </div> <!-- COL 1 -->

    </div>

</div>

<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
        </article>
    </div>
</section>
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

<script type="text/javascript">

    pageSetUp();
    var $xeditable_custom_1;
    var $xeditable_custom_2;
    var $xeditable_custom_3;
    var $xeditable_fullname;
    var t;


    function update_page(data){
        //alert(data.msg);
        setTimeout(function(){
            t.trigger( 'update' );
            //ok(data.msg);
        }, 1000);

    }

    function stop_xeditable_custom_1(){
        $xeditable_custom_1 = $('.custom_1-edit').editable('toggleDisabled');
        $xeditable_custom_1 = $('.custom_1-edit').editable('destroy');
        $xeditable_custom_1 = null;
    }
    function stop_xeditable_custom_2(){
        $xeditable_custom_2 = $('.custom_2-edit').editable('toggleDisabled');
        $xeditable_custom_2 = $('.custom_2-edit').editable('destroy');
        $xeditable_custom_2 = null;
    }
    function stop_xeditable_custom_3(){
        $xeditable_custom_3 = $('.custom_3-edit').editable('toggleDisabled');
        $xeditable_custom_3 = $('.custom_3-edit').editable('destroy');
        $xeditable_custom_3 = null;
    }
    function stop_xeditable_fullname(){
        $xeditable_fullname = $('.fullname-edit').editable('toggleDisabled');
        $xeditable_fullname = $('.fullname-edit').editable('destroy');
        $xeditable_fullname = null;
    }
    function stop_xeditable_mail(){
        $xeditable_fullname = $('.mail-edit').editable('toggleDisabled');
        $xeditable_fullname = $('.mail-edit').editable('destroy');
        $xeditable_fullname = null;
    }
    function stop_xeditable_tel(){
        $xeditable_fullname = $('.tel-edit').editable('toggleDisabled');
        $xeditable_fullname = $('.tel-edit').editable('destroy');
        $xeditable_fullname = null;
    }
    function stop_xeditable_tessera(){
        $xeditable_fullname = $('.tessera-edit').editable('toggleDisabled');
        $xeditable_fullname = $('.tessera-edit').editable('destroy');
        $xeditable_fullname = null;
    }
    function start_xeditable_tessera(){
        $.fn.editable.defaults.mode = 'inline';
        $xeditable_fullname = $('.tessera-edit').editable({
                    url: 'ajax_rd4/user/_act.php',
                    type: 'text',
                    name: 'do_user_tessera',
                    title: 'Inserisci un nuovo numero di tessera',
                            ajaxOptions: {
                                dataType: 'json'
                            },
                            success: function(data, newValue) {
                                if(data.result=="OK") {
                                        update_page(data);
                                     return;
                                }else{
                                     return data.msg;
                                }
                            }
                });

    }
    function start_xeditable_tel(){
        $.fn.editable.defaults.mode = 'inline';
        $xeditable_fullname = $('.tel-edit').editable({
                    url: 'ajax_rd4/user/_act.php',
                    type: 'text',
                    name: 'do_user_tel',
                    title: 'Inserisci un nuovo recapito telefonico',
                            ajaxOptions: {
                                dataType: 'json'
                            },
                            success: function(data, newValue) {
                                if(data.result=="OK") {
                                        update_page(data);
                                     return;
                                }else{
                                     return data.msg;
                                }
                            }
                });

    }
        function start_xeditable_mail(){
        $.fn.editable.defaults.mode = 'inline';
        $xeditable_fullname = $('.mail-edit').editable({
                    url: 'ajax_rd4/user/_act.php',
                    type: 'text',
                    name: 'do_user_mail',
                    title: 'Inserisci nuova mail',
                            ajaxOptions: {
                                dataType: 'json'
                            },
                            success: function(data, newValue) {
                                if(data.result=="OK") {
                                        update_page(data);
                                     return;
                                }else{
                                     return data.msg;
                                }
                            }
                });

    }
    function start_xeditable_fullname(){
        $.fn.editable.defaults.mode = 'inline';
        $xeditable_fullname = $('.fullname-edit').editable({
                    url: 'ajax_rd4/user/_act.php',
                    type: 'text',
                    name: 'do_user_fullname',
                    title: 'Inserisci nuovo nome',
                            ajaxOptions: {
                                dataType: 'json'
                            },
                            success: function(data, newValue) {
                                if(data.result=="OK") {
                                        update_page(data);
                                     return;
                                }else{
                                     return data.msg;
                                }
                            }
                });

    }
    function start_xeditable_custom_1(){
        $.fn.editable.defaults.mode = 'inline';
        $xeditable_custom_1 = $('.custom_1-edit').editable({
                    url: 'ajax_rd4/user/_act.php',
                    type: 'text',
                    name: 'do_user_custom_1',
                    title: 'Inserisci nuovo valore',
                            ajaxOptions: {
                                dataType: 'json'
                            },
                            success: function(data, newValue) {
                                if(data.result=="OK") {
                                        update_page(data);
                                     return;
                                }else{
                                     return data.msg;
                                }
                            }
                });

    }
    function start_xeditable_custom_2(){
        $.fn.editable.defaults.mode = 'inline';
        $xeditable_custom_2 = $('.custom_2-edit').editable({
                    url: 'ajax_rd4/user/_act.php',
                    type: 'text',
                    name: 'do_user_custom_2',
                    title: 'Inserisci nuovo valore',
                            ajaxOptions: {
                                dataType: 'json'
                            },
                            success: function(data, newValue) {
                                if(data.result=="OK") {
                                        update_page(data);
                                     return;
                                }else{
                                     return data.msg;
                                }
                            }
                });

    }
    function start_xeditable_custom_3(){
        $.fn.editable.defaults.mode = 'inline';
        $xeditable_custom_3 = $('.custom_3-edit').editable({
                    url: 'ajax_rd4/user/_act.php',
                    type: 'text',
                    name: 'do_user_custom_3',
                    title: 'Inserisci nuovo valore',
                            ajaxOptions: {
                                dataType: 'json'
                            },
                            success: function(data, newValue) {
                                if(data.result=="OK") {
                                        update_page(data);
                                     return;
                                }else{
                                     return data.msg;
                                }
                            }
                });

    }

    var pagefunction = function(){
        //------------HELP WIDGET
        <?php echo help_render_js($page_id);?>
        //------------END HELP WIDGET

        var  initDropzone = function (){
                //-----------------------------------------------DROPZONE DEMO
                try{Dropzone.autoDiscover = false;}catch(e){}


                 try{

                 console.log("initDropzone");
                 myDropZone = new Dropzone(document.body, { // Make the whole body a dropzone
                  maxFiles:1,
                  url: "upload.php", // Set the url
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
                    formData.append('id_gas', '<?php echo _USER_ID_GAS; ?>');
                    formData.append('act', 'utenti');
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
            //-----------------------------------------------DROPZONE
        }
        loadScript("js/plugin/dropzone/dropzone.min.js",initDropzone);
        loadScript("js_rd4/plugin/tablesorter/js/jquery.tablesorter.widgets.js",loadMath);
        function loadMath(){
            loadScript("js_rd4/plugin/tablesorter/js/widgets/widget-math.js", loadXeditable);
        }
        function loadXeditable(){
             loadScript("js/plugin/x-editable/x-editable.min.js", loadValidation);
        }
        function loadValidation(){
            loadScript("js/plugin/jquery-form/jquery-form.min.js", startTable);
        }
        function startTable(){
                // clears memory even if nothing is in the function
                 $("#utenti_custom")
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

                $.tablesorter.equations['product'] = function(arry) {
                    // multiple all array values together
                    var product = 1;
                    $.each(arry, function(i,v){
                        // oops, we shouldn't have any zero values in the array
                        //if (v !== 0) {
                            product *= v;
                        //}
                    });
                    return product;
                };
                t = $('#utenti_custom').tablesorter({
                    theme: 'bootstrap',
                        //debug:true,
                        widgets: ["uitheme","filter","zebra","math"],
                     widgetOptions : {
                         zebra : ["even", "odd"],
                         filter_reset : ".reset",
                         math_data     : 'math', // data-math attribute
                         math_ignore   : [0,1],
                         math_mask     : '0.0000',
                         math_complete : function($cell, wo, result, value, arry) {
                            result = result;
                            return result;
                          }
                        }
                });


                //TOGGLE
                $('#tessera-toggle').change(function(){
                    if ($(this).is(':checked')){
                        start_xeditable_tessera();
                    } else {
                        stop_xeditable_tessera();
                    }
                })
                $('#fullname-toggle').change(function(){
                    if ($(this).is(':checked')){
                        start_xeditable_fullname();
                    } else {
                        stop_xeditable_fullname();
                    }
                })
                $('#tel-toggle').change(function(){
                    if ($(this).is(':checked')){
                        start_xeditable_tel();
                    } else {
                        stop_xeditable_tel();
                    }
                })
                $('#mail-toggle').change(function(){
                    if ($(this).is(':checked')){
                        start_xeditable_mail();
                    } else {
                        stop_xeditable_mail();
                    }
                })
                $('#custom_1-toggle').change(function(){
                    if ($(this).is(':checked')){
                        start_xeditable_custom_1();
                    } else {
                        stop_xeditable_custom_1();
                    }
                })
                $('#custom_2-toggle').change(function(){
                    if ($(this).is(':checked')){
                        start_xeditable_custom_2();
                    } else {
                        stop_xeditable_custom_2();
                    }
                })
                $('#custom_3-toggle').change(function(){
                    if ($(this).is(':checked')){
                        start_xeditable_custom_3();
                    } else {
                        stop_xeditable_custom_3();
                    }
                })

                t.bind('filterInit filterEnd', function (event, data) {
                        $('#filtered_rows').html( data.filteredRows);
                        $('#total_rows').html( data.totalRows);
                });


                //DATA
                <?php if($G->custom_1_tipo==3){?>
                $('.custom_1-edit').on('shown', function () {
                    $(this).data('editable').input.$input.mask('99/99/9999');
                });
                <?php } ?>
                <?php if($G->custom_2_tipo==3){?>
                $('.custom_2-edit').on('shown', function () {
                    $(this).data('editable').input.$input.mask('99/99/9999');
                });
                <?php } ?>
                <?php if($G->custom_3_tipo==3){?>
                $('.custom_3-edit').on('shown', function () {
                    $(this).data('editable').input.$input.mask('99/99/9999');
                });
                <?php } ?>

        }//END STARTTABLE


        //FORM NUOVO USER
        var $registerForm = $("#smart-form-register").validate({

            // Rules for form validation
            rules : {
                username : {
                    required : true,
                    maxlength : 20
                },
                email : {
                    required : true,
                    email : true
                },
                password : {
                    required : true,
                    minlength : 3,
                    maxlength : 20
                },
                passwordConfirm : {
                    required : true,
                    minlength : 3,
                    maxlength : 20,
                    equalTo : '#password'
                },
                tel : {
                    required : true
                },
                fullname : {
                    required : true
                }
            },

            // Messages for form validation
            messages : {
                login : {
                    required : 'Inserisci un username'
                },
                email : {
                    required : 'Inserisci un indirizzo Email',
                    email : 'Inserisci un Email valida'
                },
                password : {
                    required : 'Inserisci una password'
                },
                passwordConfirm : {
                    required : 'Inserisci nuovamente la password',
                    equalTo : 'Inserisci la stessa password'
                },
                tel : {
                    required : 'Inserisci un recapito telefonico'
                },
                fullname : {
                    required : 'Inserisci il suo nome reale'
                }
            },

            submitHandler: function(form) {
                var fullname = $("#fullname").val();
                var username = $("#username").val();
                var password = $("#password").val();
                var password2 = $("#password2").val();
                var tel = $("#tel").val();
                var email = $("#email").val();
                //if($('#puo_partecipare').prop('checked')) {var puo_partecipare = 1;}else{ var puo_partecipare = 0;}
                //if($('#puo_gestire').prop('checked')) {var puo_gestire = 1;}else{ var puo_gestire = 0;}
                $.ajax({
                      type: "POST",
                      url: "ajax_rd4/gas/_act.php",
                      dataType: 'json',
                      data: {act: "register_new",
                        fullname : fullname,
                        username:username,
                        password:password,
                        password2:password2,
                        tel:tel,
                        email:email},
                      context: document.body
                    }).done(function(data) {
                        if(data.result=="OK"){
                            ok(data.msg);
                            $("#fullname").val('');
                            $("#username").val('');
                            $("#password").val('');
                            $("#password2").val('');
                            $("#tel").val('');
                            $("#email").val('');
                        }else{
                            ko(data.msg);
                        }
                    });



                return false;
            },

            // Do not change code below
            errorPlacement : function(error, element) {
                error.insertAfter(element.parent());
            }
        });

        var $sospensione_utenti = $("#sospensione_utenti_form").validate({
            rules : {

            },
            messages : {

            },
            submitHandler: function(form) {
                var giorni_sospensione = $("#giorni_sospensione").val();
                var frase_sospensione = $("#frase_sospensione").val();
                $.ajax({
                      type: "POST",
                      url: "ajax_rd4/gas/_act.php",
                      dataType: 'json',
                      data: {act: "sospensione_utenti",
                        giorni_sospensione : giorni_sospensione,
                        frase_sospensione : frase_sospensione},
                      context: document.body
                    }).done(function(data) {
                        if(data.result=="OK"){
                            ok(data.msg);
                        }else{
                            ko(data.msg);
                        }
                    });



                return false;
            },

            // Do not change code below
            errorPlacement : function(error, element) {
                error.insertAfter(element.parent());
            }
        });


        /*PERMESSI NUOVI UTENTI*/
        $('#default_puo_partecipare_ordini').change(function(){
            var v;
            if(this.checked) {v=1;}else{v=0;}
            $.ajax({
              type: "POST",
              url: "ajax_rd4/gas/_act.php",
              dataType: 'json',
              data: {act: "default_puo_partecipare_ordini", v:v},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                        ok(data.msg);}else{ko(data.msg);}
            });
        })
        $('#default_puo_gestire_ordini').change(function(){
            var v;
            if(this.checked) {v=1;}else{v=0;}
            $.ajax({
              type: "POST",
              url: "ajax_rd4/gas/_act.php",
              dataType: 'json',
              data: {act: "default_puo_gestire_ordini", v:v},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                        ok(data.msg);}else{ko(data.msg);}
            });
        })
        $('#default_puo_avere_amici').change(function(){
            var v;
            if(this.checked) {v=1;}else{v=0;}
            $.ajax({
              type: "POST",
              url: "ajax_rd4/gas/_act.php",
              dataType: 'json',
              data: {act: "default_puo_avere_amici", v:v},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                        ok(data.msg);}else{ko(data.msg);}
            });
        })
        $('#default_puo_creare_ditte').change(function(){
            var v;
            if(this.checked) {v=1;}else{v=0;}
            $.ajax({
              type: "POST",
              url: "ajax_rd4/gas/_act.php",
              dataType: 'json',
              data: {act: "default_puo_creare_ditte", v:v},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                        ok(data.msg);}else{ko(data.msg);}
            });
        })
        $('#default_puo_operare_con_crediti').change(function(){
            var v;
            if(this.checked) {v=1;}else{v=0;}
            $.ajax({
              type: "POST",
              url: "ajax_rd4/gas/_act.php",
              dataType: 'json',
              data: {act: "default_puo_operare_con_crediti", v:v},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                        ok(data.msg);}else{ko(data.msg);}
            });
        })
        $('#default_puo_postare_messaggi').change(function(){
            var v;
            if(this.checked){v=1;}else{v=0;}
            $.ajax({
              type: "POST",
              url: "ajax_rd4/gas/_act.php",
              dataType: 'json',
              data: {act: "default_puo_postare_messaggi", v:v},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                        ok(data.msg);}else{ko(data.msg);}
            });
        })

        /*SELECT ALL USERS*/
        $('.selectall').click(function(event) {  //on click
            console.log("Click select");
            if(this.checked) { // check select status
                $('.utente:visible').each(function() { //loop through each checkbox
                    this.checked = true;  //select all checkboxes with class "checkbox1"
                });
            }else{
                $('.utente').each(function() { //loop through each checkbox
                    this.checked = false; //deselect all checkboxes with class "checkbox1"
                });
            }
            values = $('input:checkbox:checked.utente').map(function () {
              return this.value;
            }).get();
            $('#numero_utenti_selezionati').html(values.length);
        });
        /*SELECT SINGLE USER*/
        $(document).off('change','.utente');
        $(document).on('change','.utente',function(e){
            values = $('input:checkbox:checked.utente').map(function () {
              return this.value;
            }).get();
            $('#numero_utenti_selezionati').html(values.length);
        })

        
        //UNBAN USER
        $(document).off("click",".unban_user");
        $(document).on("click",".unban_user", function(e){
            var $t = $(this);
            var userid = $(this).data("id");
            
            
            console.log("Unban " + userid);
            $.SmartMessageBox({
                title : "RIPRISTINA",
                content : "Attenzione: se la mail non è realmente riattivata la riattivazione è inutile",
                buttons : "[Esci][RIATTIVA]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="RIATTIVA"){

                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/gas/_act.php",
                          dataType: 'json',
                          data: {act: "unban_user", userid : userid },
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    ok(data.msg);
                                    $t.hide();
                                    //location.reload();
                            }else{
                                ko(data.msg);
                            }

                        });
                }
            });
            
                
        });
        
        
        /*MESSAGGIO A SELECTED*/
        var id;
        var messaggio;

        $('#manda_messaggio_a_utenti').click(function(){
            console.log("Click");
            values = $('input:checkbox:checked.utente').map(function () {
              return this.value;
            }).get();
            messaggio = $('#messaggio_a_utenti').val();
            console.log("Messaggio " + messaggio);
            if(!messaggio){
                ko("Messaggio vuoto");
            }
            else if(values.length==0){
                ko("Nessun destinatario");
            }else{
            console.log(values);

            $.SmartMessageBox({
                title : "Messaggia",
                content : "Confermi? la mail sarà inviata a " + values.length + " utenti",
                buttons : "[Esci][INVIA]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="INVIA"){

                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/gas/_act.php",
                          dataType: 'json',
                          data: {act: "messaggia_utenti", values : values, messaggio : messaggio},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    ok(data.msg);
                                    //location.reload();
                            }else{
                                ko(data.msg);
                            }

                        });
                }
            });




        }//messaggio vuoto

        });


        /*ATTIVA SELECTED */
        $(document).off("click","#do_attiva_utenti");
        $(document).on("click","#do_attiva_utenti", function(e){
        //$('#do_attiva_utenti').click(function(){

            console.log("Click");
            values = $('input:checkbox:checked.utente').map(function () {
              return this.value;
            }).get();

            if(values.length==0){
                ko("Nessun utente selezionato");
            }else{
            console.log(values);

            $.SmartMessageBox({
                title : "Attiva utenti",
                content : "Confermi? saranno attivati  " + values.length + " utenti",
                buttons : "[Esci][INVIA]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="INVIA"){

                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/gas/_act.php",
                          dataType: 'json',
                          data: {act: "do_attiva_utenti", values : values},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    ok(data.msg);
                                    //location.reload();
                            }else{
                                ko(data.msg);
                            }

                        });
                }
            });




            }

        });

        /*SOSPENDI SELECTED */
        $(document).off("click","#do_sospendi_utenti");
        $(document).on("click","#do_sospendi_utenti", function(e){
        //$('#do_sospendi_utenti').click(function(){

            console.log("Click");
            values = $('input:checkbox:checked.utente').map(function () {
              return this.value;
            }).get();

            if(values.length==0){
                ko("Nessun utente selezionato");
            }else{
            console.log(values);

            $.SmartMessageBox({
                title : "Attiva utenti",
                content : "Confermi? saranno sospesi  " + values.length + " utenti",
                buttons : "[Esci][INVIA]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="INVIA"){

                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/gas/_act.php",
                          dataType: 'json',
                          data: {act: "do_sospendi_utenti", values : values},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    ok(data.msg);
                                    //location.reload();
                            }else{
                                ko(data.msg);
                            }

                        });
                }
            });




            }

        });

        /*CANCELLA SELECTED */
        $(document).off("click","#do_cancella_utenti");
        $(document).on("click","#do_cancella_utenti", function(e){
        //$('#do_cancella_utenti').click(function(){

            console.log("Click");
            values = $('input:checkbox:checked.utente').map(function () {
              return this.value;
            }).get();

            if(values.length==0){
                ko("Nessun utente selezionato");
            }else{
            console.log(values);

            $.SmartMessageBox({
                title : "Cancella utenti",
                content : "Confermi? saranno cancellati  " + values.length + " utenti",
                buttons : "[Esci][INVIA]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="INVIA"){

                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/gas/_act.php",
                          dataType: 'json',
                          data: {act: "do_cancella_utenti", values : values},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    ok(data.msg);
                                    //location.reload();
                            }else{
                                ko(data.msg);
                            }

                        });
                }
            });




            }

        });

    } // end pagefunction

    loadScript("js_rd4/plugin/tablesorter/js/jquery.tablesorter.min.js", pagefunction);

</script>
