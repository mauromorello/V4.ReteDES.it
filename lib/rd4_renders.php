<?php
function help_render_html($page_title){

global $db,$ui;
//--------------------------------HELP WIDGET

$stmt = $db->prepare( "SELECT * from retegas_options WHERE valore_text='$page_title' AND chiave='_HELP_V4' ORDER BY valore_int DESC LIMIT 1;" );
$stmt->execute();
$row = $stmt->fetch();
$h='<div id="'.$page_title.'_help_container">'.$row["note_1"].'</div>';
$f='<div id="'.$page_title.'_help_footer" class="widget-footer"><code class="note pull-right">Vers. '.$row["valore_int"].' del '.conv_datetime_from_db($row["timbro"]).'</code></div>';

if (_USER_PUO_MODIFICARE_HELP){
    $modifica_help='<button class="btn btn-warning" id="edita_help_'.$page_title.'"><i class="fa fa-edit" ></i> Modifica</button>';
    $salva_help='<button class="btn btn-success hidden" id="salva_help_'.$page_title.'"><i class="fa fa-check"></i> Salva le modifiche</button>';
    $esci_help='<button class="btn btn-danger hidden" id="cancella_help_'.$page_title.'"><i class="fa fa-trash-o" ></i> Esci</button>';
    $toolbar=array($modifica_help,$salva_help,$esci_help);
}

$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>false,
                    "deletebutton"=>false,
                    "colorbutton"=>false);
$wg_help = $ui->create_widget($options);
$wg_help->id = "wg_help_".$page_title;
$wg_help->body = array("content" => $h.$f,"class" => "");
$wg_help->header = array(
    "title" => '<h2>Aiuto  <small>'.$page_title.'</small></h2>',
    "icon" => 'fa fa-question-circle',
    "toolbar" => $toolbar
);
//--------------------------------------------------------------HELP WIDGET

return $wg_help->print_html();
}


function help_render_js($page_title){

    return "$('#edita_help_$page_title').click(function(){

            $('#".$page_title."_help_container').summernote({

            });
            $('#salva_help_$page_title').removeClass('hidden');
            $('#cancella_help_$page_title').removeClass('hidden');

        });
        $('#salva_help_$page_title').click(function(){

            var sHTML = $('#".$page_title."_help_container').code();
            var pagina = '$page_title';
            console.log(sHTML);

            $.ajax({
                  type: 'POST',
                  url: 'ajax_rd4/_act_main.php',
                  dataType: 'json',
                  data: {act: 'salva_help', sHTML : sHTML, pagina : pagina },
                  context: document.body
                }).done(function(data) {
                    if(data.result=='OK'){
                        $('#".$page_title."_help_footer').html(data.msg);
                        $('#".$page_title."_help_container').destroy();
                        $('#salva_help_$page_title').addClass('hidden');
                        $('#cancella_help_$page_title').addClass('hidden');
                        ok('Modifiche salvate!')
                    }else{
                        ko(data.msg)
                    ;}

                });




        });";


        $a= "$('#cancella_help_$page_title').click(function(){

            $.SmartMessageBox({
                    title : 'Vuoi uscire dall'editor ?',
                    content : 'Le modifiche non verranno salvate.',
                    buttons : '[NO][SI]'
                        }, function(ButtonPress, Value) {
                            if(ButtonPress=='SI'){
                                $('#".$page_title."_help_container').destroy();
                                $('#salva_help_$page_title').addClass('hidden');
                                $('#cancella_help_$page_title').addClass('hidden');
                            }
                        });

        });";

}
