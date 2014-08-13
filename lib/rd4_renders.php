<?php
function help_render_html($id_help,$page_title=null){

global $db,$ui;

if(!isset($page_title)){$page_title = $id_page;}

//--------------------------------HELP WIDGET

$stmt = $db->prepare( "SELECT * from retegas_options WHERE valore_text='$id_help' AND chiave='_HELP_V4' ORDER BY valore_int DESC LIMIT 1;" );
$stmt->execute();
$row = $stmt->fetch();
$h='<div id="'.$id_help.'_help_container">'.$row["note_1"].'</div>';
$f='<div id="'.$id_help.'_help_footer" class="widget-footer"><code class="note pull-right">Vers. '.$row["valore_int"].' del '.conv_datetime_from_db($row["timbro"]).'</code></div>';

if (_USER_PUO_MODIFICARE_HELP){
    $modifica_help='<button class="btn btn-warning" id="edita_help_'.$id_help.'"><i class="fa fa-edit" ></i></button>';
    $salva_help='<button class="btn btn-success hidden" id="salva_help_'.$id_help.'"><i class="fa fa-check"></i></button>';
    $esci_help='<button class="btn btn-danger hidden" id="cancella_help_'.$id_help.'"><i class="fa fa-trash-o" ></i></button>';
    $toolbar=array($modifica_help,$salva_help,$esci_help);
}

$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>false,
                    "deletebutton"=>false,
                    "colorbutton"=>false);
$wg_help = $ui->create_widget($options);
$wg_help->id = "wg_help_".$id_help;
$wg_help->body = array("content" => $h.$f,"class" => "");
$wg_help->header = array(
    "title" => '<h2>Aiuto  <small><b>'.$page_title.'</b></small></h2>',
    "icon" => 'fa fa-question-circle',
    "toolbar" => $toolbar
);
//--------------------------------------------------------------HELP WIDGET

return $wg_help->print_html();
}


function help_render_js($id_help){

    return "//-------------------------------HELP JS
            function do_summer_$id_help(){
                $('#".$id_help."_help_container').summernote({

                });
            }

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


        $a= "$('#cancella_help_$id_help').click(function(){

            $.SmartMessageBox({
                    title : 'Vuoi uscire ?',
                    content : 'Le modifiche non verranno salvate.',
                    buttons : '[NO][SI]'
                        }, function(ButtonPress, Value) {
                            if(ButtonPress=='SI'){
                                $('#".$id_help."_help_container').destroy();
                                $('#salva_help_$id_help').addClass('hidden');
                                $('#cancella_help_$id_help').addClass('hidden');
                            }
                        });

        });";

}
