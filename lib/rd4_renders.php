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
        if(file_exists("../public_rd4/users/".$id_utente."_$res.jpg")){

        $dumb = date ("His", filemtime("../public_rd4/users/".$id_utente."_$res.jpg"));
        $src = USER_IMG_URL.$id_utente."_$res.jpg?d=".$dumb;
        $found=true;
        }else{
            $src = USER_IMG_URL."0_240.png";
        }
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
function navbar_report($title,$buttons_right){

$n.= '
<span class="font-xl pull-left" style="margin-top:-30px;">'.$title.'</span>
<div class="pull-right btn-group" style="margin-top:-30px;">
      ';

foreach($buttons_right as $button){$n.= $button;}
$n.='</div>
    <div class="clearfix"></div>
    <p></p>';

return $n;
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


function pdf_css(){

    return '<style type="text/css">

                html, body, div, span, applet, object, iframe,
                h1, h2, h3, h4, h5, h6, p, blockquote, pre,
                a, abbr, acronym, address, big, cite, code,
                del, dfn, em, img, ins, kbd, q, s, samp,
                small, strike, strong, sub, sup, tt, var,
                b, u, i, center,
                dl, dt, dd, ol, ul, li,
                fieldset, form, label, legend,
                table, caption, tbody, tfoot, thead, tr, th, td,
                article, aside, canvas, details, embed,
                figure, figcaption, footer, header, hgroup,
                menu, nav, output, ruby, section, summary,
                time, mark, audio, video {
                    margin: 0;
                    padding: 0;
                    border: 0;
                    font-size: 100%;
                    vertical-align: baseline;
                }
                /* HTML5 display-role reset for older browsers */
                article, aside, details, figcaption, figure,
                footer, header, hgroup, menu, nav, section {
                    display: block;
                }

                ol, ul {
                    list-style: none;
                }
                table {
                    border-collapse: collapse;
                    border-spacing: 0;
                }
                @page {
                    margin: 10px;
                  }
                body {
                    line-height: 1;
                    padding:12px;
                }
                .note {font-size:10px;}

            </style>';
}
function pdf_testata($O,$orientation){
if($orientation=="landscape"){
    $w = 1024;
}else{
    $w = 720;
}
$t ='
<table class="rd4_h" style="width:'.$w.'px; margin-left: auto; margin-right: auto">
  <tr>
    <th style="text-align:left;" ><img src="../../img_rd4/logo_rd4.jpg" style="width:180px;"></th>
    <th style=""></th>
    <th style="text-align:right;font-size:11px;">ReteDES.it<br>'.$O->descrizione_gas_referente.'</th>
  </tr>
  <tr>
    <td colspan="3" style="text-align:right;font-size:11px;">Ordine '.$O->descrizione_ordini.' di '.$O->fullname_referente.'</td>
  </tr>
  <tr>
    <td colspan="3" style="px;text-align:right;font-size:11px;">Ditta '.$O->descrizione_ditte.'</td>
  </tr>
</table>';

return $t;

}
function css_report($orientation){

if($orientation=="landscape"){
    $w = 1024;
}else{
    $w = 720;
}

return '<style type="text/css">

table.rd4, table.rd4_h {
  width:'.$w.'px;
  margin-top:20px;
  background-color:#FFF;
}

.row_beige{
    background-color:#FF0000;
}
.row_blue{
    background-color:#0000FF;
}
table.rd4 td, table.rd4 th{
  border: 1px solid #CCC;
  padding: 0.25rem;
}
table.rd4_h td, table.rd4_h th{
  border: 0;
  padding: 0.1rem;
}
.text-right {text-align: right;}
.text-left {text-align: left;}
.text-center {text-align: center;}
.intestazione {background-color:#FFF; border:0; padding-bottom:4px; border-bottom:2px solid #444;}
.totale {background-color:#CCFFFF; border-top:2px solid #444; padding-top:4px;}
.odd {background-color:#FFFFFF;}
.even {background-color:#FAF0E0;}
.costo {background-color:#FFB933;}
.subtotale {background-color:#C0FFC0; border-bottom:3px solid #444; padding-bottom:4px;}
.separator {line-height:10px; border-bottom:3px solid #444}
.utente {background-color:#C0C0FF; border-top:3px solid #444; padding-top:4px;}
</style>';
}

function render_notifica($user_id_from, $tipo, $titolo, $msg, $link){

    if(file_exists("../../lib_rd4/class.rd4.user.php")){require_once("../../lib_rd4/class.rd4.user.php");}
    if(file_exists("../lib_rd4/class.rd4.user.php")){require_once("../lib_rd4/class.rd4.user.php");}
    if(file_exists("lib_rd4/class.rd4.user.php")){require_once("lib_rd4/class.rd4.user.php");}

      global $db;

      switch ($tipo){
          case "LISTINI":
            $tipo = '<span class="label label-info pull-right font-xs">LISTINI</span>';
          break;
          case "PRENOTAZIONE_CARICO":
            $tipo = '<span class="label label-danger pull-right font-xs">CASSA</span>';
          break;
      }

      $U = new user($user_id_from);
      $gas = $U->descrizione_gas;
      $fullname = $U->fullname;
      unset($U);

      return '<li>
                    <span class="">
                        <a href="'.$link.'" class="msg">
                            '.$tipo.'
                            <img src="'.src_user($user_id_from,64).'" alt="" class="air air-top-left margin-top-5" width="40" height="40" />
                            <span class="from">'.$fullname.' <i class="icon-paperclip font-xs note">'.$gas.'</i></span>

                            <span class="subject">'.$titolo.'</span>
                            <span class="msg-body">'.$msg.'</span>
                        </a>
                    </span>
                </li>';

  }
