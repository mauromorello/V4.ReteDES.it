<?php
function help_render_html($id_help,$page_title=null,$force=false){

global $db,$ui;

if(!isset($page_title)){$page_title = $id_help;}

$stmt = $db->prepare( "SELECT valore_int from retegas_options WHERE valore_text='$id_help' AND chiave='_HELP_V4_HIDE' AND id_user='"._USER_ID."' LIMIT 1;" );
$stmt->execute();
$rowU = $stmt->fetch();

if (($rowU["valore_int"]<>1) OR ($force)){
    //--------------------------------HELP WIDGET

    $stmt = $db->prepare( "SELECT O.note_1, O.valore_text, O.timbro, O.valore_int, U.fullname from retegas_options O inner join maaking_users U on U.userid=O.id_user WHERE valore_text='$id_help' AND chiave='_HELP_V4' ORDER BY valore_int DESC LIMIT 1;" );
    $stmt->execute();
    $row = $stmt->fetch();

    $h='<div id="'.$id_help.'_help_container">'.$row["note_1"].'</div>';
    $f='<div id="'.$id_help.'_help_footer" class="widget-footer margin-top-10"><a href="javascript:void(0);" class="pull-left font-xs" id="elimina_help_'.$id_help.'"><i class="fa fa-trash-o"></i> Nascondi</a><span class="note pull-right">Vers. '.$row["valore_int"].' di '.$row["fullname"].' del '.conv_datetime_from_db($row["timbro"]).'</span></div>';

    if (_USER_PUO_MODIFICARE_HELP){
        $modifica_help='<button class="btn btn-warning" id="edita_help_'.$id_help.'"><i class="fa fa-edit" ></i></button>';
        $salva_help='<button class="btn btn-success hidden" id="salva_help_'.$id_help.'"><i class="fa fa-check"></i></button>';
        $esci_help='<button class="btn btn-danger hidden" id="cancella_help_'.$id_help.'"><i class="fa fa-trash-o" ></i></button>';
        $toolbar=array($modifica_help,$salva_help);
    }

    $options = array(   "editbutton" => false,
                        "fullscreenbutton"=>false,
                        "deletebutton"=>false,
                        "colorbutton"=>false);
    $wg_help = $ui->create_widget($options);
    $wg_help->id = "wg_help_".$id_help;
    $wg_help->body = array("content" => $h.$f,"class" => "");
    $wg_help->header = array(
        "title" => '<h2>Aiuto  <span>'.$page_title.'</span></h2>',
        "icon" => 'fa fa-question-circle',
        "toolbar" => $toolbar
    );
    //--------------------------------------------------------------HELP WIDGET

        return $wg_help->print_html();

    }else{
        return '';
    }


}


function help_render_js($id_help){
    global $db;
    $stmt = $db->prepare( "SELECT valore_int from retegas_options WHERE valore_text='$id_help' AND chiave='_HELP_V4_HIDE' AND id_user='"._USER_ID."' LIMIT 1;" );
    $stmt->execute();
    $rowU = $stmt->fetch();

    if ($rowU["valore_int"]<>1){
    return "//-------------------------------HELP JS
            function do_summer_$id_help(){
                $('#".$id_help."_help_container').summernote({

                });
            }
            $('#elimina_help_$id_help').click(function(){
                console.log('elimina help');
                $.ajax({
                  type: 'POST',
                  url: 'ajax_rd4/user/_act.php',
                  dataType: 'json',
                  data: {act: 'user_elimina_help', value : '$id_help'},
                  context: document.body
                }).done(function(data) {
                    if(data.result=='OK'){
                            $.smallBox({
                                    title : 'ReteDES.it',
                                    content : data.msg + '<p class=\"text-align-right\"><a href=\"javascript:void(0);\" class=\"btn btn-default btn-sm\" onclick=\"javascript:location.reload();\">Ok</a></p>',
                                    color : '#0074A7',
                                    //timeout: 8000,
                                    icon : 'fa fa-bell swing animated'
                                });

                    }else{
                        ko(data.msg)
                    ;}
                });
            });
            $('#edita_help_$id_help').click(function(){


            loadScript('js/plugin/summernote/summernote.min.js',do_summer_$id_help);

            $('#salva_help_$id_help').removeClass('hidden');
            $('#cancella_help_$id_help').removeClass('hidden');

        });
        $('#salva_help_$id_help').click(function(){

            var sHTML = $('#".$id_help."_help_container').code();
            var pagina = '$id_help';
            console.log(sHTML);

            $.ajax({
                  type: 'POST',
                  url: 'ajax_rd4/_act_main.php',
                  dataType: 'json',
                  data: {act: 'salva_help', sHTML : sHTML, pagina : pagina },
                  context: document.body
                }).done(function(data) {
                    if(data.result=='OK'){
                        $('#".$id_help."_help_footer').html(data.msg);
                        $('#".$id_help."_help_container').destroy();
                        $('#salva_help_$id_help').addClass('hidden');
                        $('#cancella_help_$id_help').addClass('hidden');
                        ok('Modifiche salvate!')
                    }else{
                        ko(data.msg)
                    ;}

                });




        });";
    }
}

function src_user($id_utente=0,$res=64){

    $found=false;

    if(file_exists("../../public_rd4/users/".$id_utente."_$res.jpg")){

        $dumb = date ("His", filemtime("../../public_rd4/users/".$id_utente."_$res.jpg"));
        $src = USER_IMG_URL.$id_utente."_$res.jpg?d=".$dumb;
        $found=true;
    }else{
        $src = USER_IMG_URL."0_240.png";
    }
    if(!$found){
        if(file_exists("../public_rd4/users/".$id_utente."_$res.jpg")){
            $dumb = date ("His", filemtime("../public_rd4/users/".$id_utente."_$res.jpg"));
            $src = USER_IMG_URL.$id_utente."_$res.jpg?d=".$dumb;
            $found=true;
        }else{
            $src = USER_IMG_URL."0_240.png";
        }
    }
    if(!$found){
        if(file_exists("public_rd4/users/".$id_utente."_$res.jpg")){
            $dumb = date ("His", filemtime("public_rd4/users/".$id_utente."_$res.jpg"));
            $src = USER_IMG_URL.$id_utente."_$res.jpg?d=".$dumb;
        }else{
            $src = USER_IMG_URL."0_240.png";
        }
    }

    return $src;
}
function navbar($title="Senza titolo",$buttons_right=null,$buttons_left=null){

$n='<div class="no-content-padding">
     <div class="navbar navbar-default"style="height:64px;">

                    <!-- Brand and toggle get grouped for better mobile display -->
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand font-lg txt-color-blueDark" href="javascript:void(0)">'.$title.'</a>
                    </div>

                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="navbar-collapse collapse" id="bs-example-navbar-collapse-1" style="height: 1px;">
                        <ul class="nav navbar-nav">
                        ';
foreach($buttons_left as $button){
            $n.= '<li>'.$button.'</li>';
            }
$n.='
                        </ul>
                        <ul class="nav navbar-nav navbar-right">
';
foreach($buttons_right as $button){
            $n.= '<li>'.$button.'</li>';
            }
$n.='                        </ul>
                    </div><!-- /.navbar-collapse -->

            </div>

</div> <!-- /.no-content-paddinge -->';

return $n;

}
function navbar_ordine($id_ordine,$buttons_right=null){
    global $db;
    $stmt = $db->prepare("SELECT    O.id_ordini,
                                O.id_listini,
                                O.descrizione_ordini,
                                O.note_ordini,
                                O.is_printable,
                                O.costo_gestione,
                                O.costo_trasporto,
                                O.mail_level,
                                DATE_FORMAT(O.data_apertura,'%d/%m/%Y %H:%i') as data_apertura,
                                DATE_FORMAT(O.data_chiusura,'%d/%m/%Y %H:%i') as data_chiusura,
                                O.id_stato,
                                U.fullname,
                                L.descrizione_listini,
                                D.descrizione_ditte,
                                D.id_ditte
                        FROM retegas_ordini O
                            inner join maaking_users U on U.userid=O.id_utente
                            inner join retegas_listini L on L.id_listini=O.id_listini
                            inner join retegas_ditte D on D.id_ditte=L.id_ditte
                        WHERE id_ordini=:id LIMIT 1;");
$stmt->bindValue(':id', $id_ordine, PDO::PARAM_INT);
$stmt->execute();
$rowo = $stmt->fetch(PDO::FETCH_ASSOC);

if($rowo["note_ordini"]<>""){
    $note_1 ='<button type="button" class=" btn btn-link" data-toggle="collapse" data-target="#note_ordini">Note <i class="fa fa-arrow-down"></i></button>';
    $note_2 ='<div id="note_ordini" class="collapse out">'.$rowo["note_ordini"].'</div>';
}else{
    $note_1 ='';
    $note_2 ='';
}

$n='<div class="panel no-content-padding padding-10" >
    <span class="pull-right align-right text-right">
        <p class="font-lg"><b class="label label-danger">CHIUSO</b></p>
        <p class="margin-top-10 font-lg"><b class="label label-success">'.VA_ORDINE_USER($id_ordine,_USER_ID).' €</b></p>
        <div class="btn-group margin-top-10">
        <a href="#ajax_rd4/ordini/ordine.php?id='.$id_ordine.'" type="button" class="btn btn-default">
            <i class="fa fa-shopping-cart"></i>
        </a>
        <a type="button" class="btn btn-default">
            <i class="fa fa-eye"></i>
        </a>
        <a type="button" class="btn btn-default">
            <i class="fa fa-hand-o-right"></i>
        </a>
        <div class="btn-group">
                            <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-cogs"></i> &nbsp; <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#ajax_rd4/ordini/edit.php?id='.$id_ordine.'">Edita</a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="#ajax_rd4/rettifiche/start.php?id='.$id_ordine.'" class="text-center"><strong>Rettifiche:</strong></a>
                                </li>
                                <li>
                                    <a href="#ajax_rd4/rettifiche/totale.php?id='.$id_ordine.'">Totale Ordine</a>
                                </li>
                                <li>
                                    <a href="#ajax_rd4/rettifiche/utenti.php?id='.$id_ordine.'">Totale Utenti</a>
                                </li>
                                <li>
                                    <a href="#ajax_rd4/rettifiche/dettaglio.php?id='.$id_ordine.'">Dettagli</a>
                                </li>
                                <li>
                                    <a href="#ajax_rd4/rettifiche/articoli.php?id='.$id_ordine.'">Articoli</a>
                                </li>
                                <li>
                                    <a href="#ajax_rd4/rettifiche/altro.php?id='.$id_ordine.'">Aggiunte</a>
                                </li>
                                <li>
                                    <a href="#ajax_rd4/rettifiche/sconti.php?id='.$id_ordine.'">Maggiorazioni</a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="javascript:void(0);">Comunica</a>
                                </li>
                            </ul>
                        </div>
        <a type="button" class="btn btn-default">
            <i class="fa fa-envelope"></i>
        </a>

    </div>
    </span>
    <div class="padding-10">
    <p class="font-lg"><i class="fa fa-shopping-cart"></i> <b class="note">'.$id_ordine.'</b> '.$rowo["descrizione_ordini"].'</p>
    <p class="font-md"><i class="fa fa-truck"></i> '.$rowo["descrizione_ditte"].', <i class="fa fa-cubes"></i> '.$rowo["descrizione_listini"].'</p>




    <p class="font-sm"><i class="fa fa-user"></i> Di '.$rowo["fullname"].', <i class="fa fa-home"></i> GAS ajskdja ksdk </p>
    '.$note_1.'
    <div class="clearfix"></div>
    '.$note_2.'
    </div>
</div>
<hr>
';

return $n;

}
function schedina_ordine($id_ordine){
global $db;
if (posso_gestire_ordine($id_ordine)){
    $gestore=true;
    $link_gestore = '<a href="#ajax_rd4/ordini/edit.php?id='.$id_ordine.'"><i class="fa fa-gears"></i></a>';
}else{
    $gestore=false;
    $link_gestore =='';
}
$stmt = $db->prepare("SELECT    O.id_ordini,
                                O.id_listini,
                                O.descrizione_ordini,
                                O.note_ordini,
                                O.is_printable,
                                O.costo_gestione,
                                O.costo_trasporto,
                                O.mail_level,
                                DATE_FORMAT(O.data_apertura,'%d/%m/%Y %H:%i') as data_apertura,
                                DATE_FORMAT(O.data_chiusura,'%d/%m/%Y %H:%i') as data_chiusura,
                                O.id_stato,
                                U.fullname,
                                L.descrizione_listini,
                                D.descrizione_ditte,
                                D.id_ditte
                        FROM retegas_ordini O
                            inner join maaking_users U on U.userid=O.id_utente
                            inner join retegas_listini L on L.id_listini=O.id_listini
                            inner join retegas_ditte D on D.id_ditte=L.id_ditte
                        WHERE id_ordini=:id LIMIT 1;");
$stmt->bindValue(':id', $id_ordine, PDO::PARAM_INT);
$stmt->execute();
$rowo = $stmt->fetch(PDO::FETCH_ASSOC);

$o='
        <div class="row">
            <div class="col-md-4">
                <div class="well well-sm">
                    <p class="font-lg">'.$link_gestore.'  '.$rowo["descrizione_ordini"].'</p>
                    <p><a href="#ajax_rd4/fornitori/scheda.php?id='.$rowo["id_ditte"].'"><i class="fa fa-truck"></i></a>  '.$rowo["descrizione_ditte"].'</p>
                    <p><a href="#ajax_rd4/listini/listino.php?id='.$rowo["id_listini"].'"><i class="fa fa-cubes"></i></a>  '.$rowo["descrizione_listini"].'</p>
                    <hr>
                    <p>Apre: '.$rowo["data_apertura"].'</p>
                    <p>Chiude: '.$rowo["data_chiusura"].'</p>
                    <p>Consegna:  ---</p>
                    <p>Luogo: </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="well well-sm">
                <p>
                <label>Referente ordine: </label><br>
                <i class="fa fa-envelope"></i> Tizio Caio (GAS Borgomanero)
                </p>
                <p>
                <label>Referente tuo gas:</label><br>
                <i class="fa fa-envelope"></i> Marco Pisellonio
                </p>
                <p>
                <label>Tua Spesa:</label><br>
                <span class="font-xl">14.52 Eu.</span>
                </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="well well-sm">
                    <label>Report</label><br>
                            <button class="btn btn-default btn-block">LA MIA SPESA</button>
                            <button class="btn btn-default btn-block">DETTAGLIO AMICI</button>
                            <button class="btn btn-default btn-block">TOTALE AMICI</button>
                            <a href="#ajax_rd4/ordini/report.php?id='.$id_ordine.'" class="btn btn-default btn-block">PANNELLO REPORT</a>
                    <hr>
                    <label>Aiuti</label><br>
                            <button class="btn btn-default btn-block">OFFRI AIUTO</button>
                            <button class="btn btn-default btn-block">DIVENTA REFERENTE</button>
                    <hr>
                </div>
            </div>
        </div>
    ';
return $o;

}
