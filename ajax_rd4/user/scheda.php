<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.user.php");

$ui = new SmartUI;
$page_title = "Scheda utente";
$converter = new Encryption;
    $userid = CAST_TO_STRING($_POST["id"]);
if($userid==""){
    $userid = CAST_TO_STRING($_GET["id"]);
}
$userid = CAST_TO_INT($converter->decode($userid),0);

if(!_USER_GAS_VISIONE_DATI_UTENTI){
    if(!((_USER_PERMISSIONS & perm::puo_gestire_utenti) OR (_USER_PERMISSIONS & perm::puo_creare_gas))){
        echo rd4_go_back("Il tuo GAS non permette la visione dei dati utenti");die;
    }
}


$u="userid : $userid";
$U= new user($userid);

$stmt = $db->prepare("SELECT U.user_permission, U.fullname, U.userid, U.tel, U.tessera, U.id_gas, U.email, G.descrizione_gas, G.id_referente_gas FROM  maaking_users U INNER JOIN retegas_gas G ON G.id_gas=U.id_gas  WHERE userid = :userid LIMIT 1;");
$stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if(($stmt->rowCount()<>1) or ($row["userid"]==0)){
    echo "NO: $userid";die();
}

$useridEnc = $converter->encode($row["userid"]);

if(
    (_USER_PERMISSIONS & perm::puo_gestire_utenti AND $row["id_gas"]==_USER_ID_GAS)
     OR
     (_USER_PERMISSIONS & perm::puo_vedere_retegas)
     OR
     ($row["id_referente_gas"]==_USER_ID)
     )
    {
    $ok=true;
}else{
    //echo "not allowed $userid";
    //die();
}


if($ok){
    $gestisci='<a href="'.APP_URL.'/#ajax_rd4/user/gestisci.php?id='.$useridEnc.'"  class="btn btn-default pull-right link_gestisci"><i class="fa fa-gear"></i>&nbsp;&nbsp;<span class="hidden-xs">Gestisci</span></a>';
}else{
    $gestisci='';
}


//CAMPO CUSTOM_1
$custom_1='';
if($G->custom_1_privato==0){
    if($U->custom_1_nome<>"Non definito"){
        $custom_1='<label>'.$U->custom_1_nome.'</label>
                <p class="font-lg">'.$U->custom_1.'</p>
                ';
    }
}
//CAMPO CUSTOM_2
$custom_2='';
if($G->custom_2_privato==0){
    if($U->custom_2_nome<>"Non definito"){
        $custom_2='<label>'.$U->custom_2_nome.'</label>
                <p class="font-lg">'.$U->custom_2.'</p>
                ';
    }
}
//CAMPO CUSTOM_3
$custom_3='';
if($G->custom_3_privato==0){
    if($U->custom_3_nome<>"Non definito"){
        $custom_3='<label>'.$U->custom_3_nome.'</label>
                <p class="font-lg">'.$U->custom_3.'</p>
                ';
    }
}


//BOUNCED EMAIL
$bc=bounce_class($U->email);
if($bc>0 AND $bc<>51){
   $bounced='<div class="alert alert-danger margin-top-10 clearfix"><strong>QUESTO UTENTE NON HA UNA MAIL VALIDA, NON POTRA\' RICEVERE MESSAGGI.</strong></div>';
   $bounced_button='';
    
}else{
    $bounced='';
    $bounced_button='<button class="btn btn-info pull-right margin-top-10"  type="submit" id="usermessage_go" data-userid="'.$userid.'">Invia</button>';    
}

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
            <label>Tessera</label>
            <p class="font-lg">'.$row["tessera"].'</p>
            <label>Nome</label>
            <p class="font-lg">'.$row["fullname"].'</p>
            <label>Gas</label>
            <p class="font-lg">'.$row["descrizione_gas"].'</p>
            <label>Telefono</label>
            <p class="font-lg"><a href="tel:'.$row["tel"].'" class="pull-right"><i class="fa fa-phone"></i></a>  '.$row["tel"].'</p>
            <label>Email</label>
            <p class="font-lg"><a href="mailto:'.$row["email"].'" target="_BLANK" class="pull-right"><i class="fa fa-envelope"></i></a>  '.$row["email"].'</p>
            '.$custom_1.'
            '.$custom_2.'
            '.$custom_3.'
            </div>

        </div>
        <div class="col-sm-6">
            <div>
                <form class="form">
                <div class="form-group">
                    <label>Manda un messaggio</label>
                    <textarea class="form-control" placeholder="Scrivi qua..." rows="3" style="margin-top: 0px; margin-bottom: 0px; height: 166px;" id="usermessage"></textarea>
                </div>
                <fieldset>
                <div class="input-group">
                    <span class="input-group-addon">
                        <span class="radio">
                            <label>
                                <input type="radio" class="radiobox style-0" name="metodo" value="MAIL" checked="CHECKED">
                                <span> MAIL</span> 
                            </label>
                        </span>
                    </span>
                    <span class="input-group-addon">
                        <span class="radio">
                            <label>
                                <input type="radio" class="radiobox style-0" name="metodo" value="SMS">
                                <span> SMS</span> 
                            </label>
                        </span>
                    </span>
                    <span class="input-group-addon">
                        <span class="radio">
                            <label>
                                <input type="radio" class="radiobox style-0" name="metodo" value="TELEGRAM">
                                <span> TELEGRAM</span> 
                            </label>
                        </span>
                    </span>
                </div>
                
                </fieldset>
                '.$bounced_button.'
                </form>
                </div>
            '.$bounced.'
            <div class="clearfix"></div>
            <p class="margin-top-5"><strong>NB:</strong> la funzione SMS è sperimentale; logicamente funziona se l\'utente ha inserito un numero di telefono in grado di riceverli. Il testo verrà troncato a 140 caratteri.</p>
            <p class="margin-top-5">L\'invio attraverso il canale TELEGRAM è subordinato al fatto che l\'utente abbia attivato il BOT di reteDES sul suo TELEGRAM.</p>
        
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




?>
<div class="inbox-nav-bar no-content-padding">
    <h1 class="page-title txt-color-blueDark"><i class="fa fa-fw fa-user"></i> Scheda Utente &nbsp;
    <?php echo $gestisci; ?></h1>
</div>

<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html("scheda_user",$page_title); ?>
            <?php echo $wg_scheda_user->print_html(); ?>
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

        $('#usermessage_go').click(function(e){
        //$('body').on('click', '#usermessage_go', function () {
            //invio il messaggio ciccio
            e.preventDefault();
            $.blockUI();
            id=$('#usermessage_go').data("userid");
            var metodo = $('input[name=metodo]:checked').val(); 
            
            $.ajax({
              type: "POST",
              url: "ajax_rd4/gas/_act.php",
              dataType: 'json',
              data: {act: "messaggia", messaggio : messaggio, id:id, metodo: metodo},
              context: document.body
            }).done(function(data) {
                $.unblockUI();
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