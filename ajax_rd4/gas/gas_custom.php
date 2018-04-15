<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.gas.php");
$ui = new SmartUI;
$converter = new Encryption;

$page_title = "Campi personalizzati";
$page_id ="custom_gas";

$G = new gas(_USER_ID_GAS);
$id_gas=_USER_ID_GAS;


$converter = new Encryption;



?>
<?php echo $G->render_toolbar('OPZIONI AVANZATE');?>

<h1>Campi personalizzati <small>(per saperne di più leggi l'help in fondo alla pagina)</small></h1>  

<div class="row margin-top-10">
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        <div class="well well-sm">
            <form class="smart-form" id="custom_1_form" action="ajax_rd4/gas/_act.php" method="post">

                    <h3>Campo personalizzato 1</h3> 
                        <fieldset>
                        <label class="label margin-top-5">Nome</label>
                        <label class="input"> <i class="icon-prepend fa fa-pencil"></i>
                            <input placeholder="" type="text" id="custom_1_nome" name="custom_1_nome" value="<?php echo $G->custom_1_nome ?>">
                            <b class="tooltip tooltip-top-left">
                                <i class="fa fa-warning txt-color-teal"></i>
                                Il nome che intesta la colonna</b>
                        </label>
                        </fieldset>
                        <fieldset>
                        <div class="form-group margin-top-10">
                            <label class="col-md-2 control-label">Tipo di dati</label>
                                <div class="col-md-10">
                                    <div class="radio">
                                        <label>
                                          <input type="radio" name="custom_1_tipo" value="0" class="radiobox style-0" <?php echo ($G->custom_1_tipo==0 ? 'checked="checked"':''); ?> >
                                          <span>Testo</span>
                                        </label>
                                    </div>

                                <div class="radio">
                                    <label>
                                      <input type="radio" name="custom_1_tipo" value="1" class="radiobox style-0" <?php echo ($G->custom_1_tipo==1 ? 'checked="checked"':''); ?>>
                                      <span>Numerici</span>
                                    </label>
                                </div>

                                <div class="radio">
                                    <label>
                                      <input type="radio" name="custom_1_tipo" value="2" class="radiobox style-0" <?php echo ($G->custom_1_tipo==2 ? 'checked="checked"':''); ?>>
                                      <span>SI/NO</span>
                                    </label>
                                </div>

                                <div class="radio">
                                    <label>
                                      <input type="radio" name="custom_1_tipo" value="3" class="radiobox style-0" <?php echo ($G->custom_1_tipo==3 ? 'checked="checked"':''); ?>>
                                      <span>Data (gg/mm/AAAA)</span>
                                    </label>
                                </div>

                            </div>
                        </div>
                        </fieldset>
                        <fieldset>
                        <div class="form-group margin-top-10">
                            <label class="col-md-2 control-label">Visibilità</label>
                                <div class="col-md-10">
                                    <div class="radio">
                                        <label>
                                          <input type="radio" name="custom_1_privato" value="0" class="radiobox style-0" <?php echo ($G->custom_1_privato==0 ? 'checked="checked"':''); ?>>
                                          <span>Tutti gli utenti</span>
                                        </label>
                                    </div>
                                <div class="radio">
                                    <label>
                                      <input type="radio" name="custom_1_privato" value="1" class="radiobox style-0" <?php echo ($G->custom_1_privato==1 ? 'checked="checked"':''); ?>>
                                      <span>Solo utente interessato</span>
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                      <input type="radio" name="custom_1_privato" value="2" class="radiobox style-0" <?php echo ($G->custom_1_privato==2 ? 'checked="checked"':''); ?>>
                                      <span>Solo responsabili GAS</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        </fieldset>
                        <fieldset>
                        <div class="form-group margin-top-10">
                            <label class="col-md-2 control-label">Modifiche</label>
                                <div class="col-md-10">
                                    <div class="radio">
                                        <label>
                                          <input type="radio" name="custom_1_proprieta" value="0" class="radiobox style-0" <?php echo ($G->custom_1_proprieta==0 ? 'checked="checked"':''); ?>>
                                          <span>Gas e singoli utenti</span>
                                        </label>
                                    </div>

                                    <div class="radio">
                                        <label>
                                          <input type="radio" name="custom_1_proprieta" value="1" class="radiobox style-0" <?php echo ($G->custom_1_proprieta==1 ? 'checked="checked"':''); ?>>
                                          <span>Solo responsabili GAS</span>
                                        </label>
                                    </div>
                            </div>
                        </div>
                        </fieldset>

                    <input id="act" type="hidden" name="act" value="do_custom_1">
                    <footer>
                            <button type="submit" class="btn btn-primary">
                                Salva i parametri
                            </button>
                    </footer>
           </form>
       </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        <div class="well well-sm">
            <form class="smart-form" id="custom_2_form" action="ajax_rd4/gas/_act.php" method="post">

                    <h3>Campo personalizzato 2</h3>
                        <fieldset>
                        <label class="label margin-top-5">Nome</label>
                        <label class="input"> <i class="icon-prepend fa fa-pencil"></i>
                            <input placeholder="" type="text" id="custom_2_nome" name="custom_2_nome" value="<?php echo $G->custom_2_nome ?>">
                            <b class="tooltip tooltip-top-left">
                                <i class="fa fa-warning txt-color-teal"></i>
                                Il nome che intesta la colonna</b>
                        </label>
                        </fieldset>
                        <fieldset>
                        <div class="form-group margin-top-10">
                            <label class="col-md-2 control-label">Tipo di dati</label>
                                <div class="col-md-10">
                                    <div class="radio">
                                        <label>
                                          <input type="radio" name="custom_2_tipo" value="0" class="radiobox style-0" <?php echo ($G->custom_2_tipo==0 ? 'checked="checked"':''); ?> >
                                          <span>Testo</span>
                                        </label>
                                    </div>

                                <div class="radio">
                                    <label>
                                      <input type="radio" name="custom_2_tipo" value="1" class="radiobox style-0" <?php echo ($G->custom_2_tipo==1 ? 'checked="checked"':''); ?>>
                                      <span>Numerici</span>
                                    </label>
                                </div>

                                <div class="radio">
                                    <label>
                                      <input type="radio" name="custom_2_tipo" value="2" class="radiobox style-0" <?php echo ($G->custom_2_tipo==2 ? 'checked="checked"':''); ?>>
                                      <span>SI/NO</span>
                                    </label>
                                </div>

                                <div class="radio">
                                    <label>
                                      <input type="radio" name="custom_2_tipo" value="3" class="radiobox style-0" <?php echo ($G->custom_2_tipo==3 ? 'checked="checked"':''); ?>>
                                      <span>Data (gg/mm/AAAA)</span>
                                    </label>
                                </div>

                            </div>
                        </div>
                        </fieldset>
                        <fieldset>
                        <div class="form-group margin-top-10">
                            <label class="col-md-2 control-label">Visibilità</label>
                                <div class="col-md-10">
                                    <div class="radio">
                                        <label>
                                          <input type="radio" name="custom_2_privato" value="0" class="radiobox style-0" <?php echo ($G->custom_2_privato==0 ? 'checked="checked"':''); ?>>
                                          <span>Tutti gli utenti</span>
                                        </label>
                                    </div>
                                <div class="radio">
                                    <label>
                                      <input type="radio" name="custom_2_privato" value="1" class="radiobox style-0" <?php echo ($G->custom_2_privato==1 ? 'checked="checked"':''); ?>>
                                      <span>Solo utente interessato</span>
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                      <input type="radio" name="custom_2_privato" value="2" class="radiobox style-0" <?php echo ($G->custom_2_privato==2 ? 'checked="checked"':''); ?>>
                                      <span>Solo responsabili GAS</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        </fieldset>
                        <fieldset>
                        <div class="form-group margin-top-10">
                            <label class="col-md-2 control-label">Modifiche</label>
                                <div class="col-md-10">
                                    <div class="radio">
                                        <label>
                                          <input type="radio" name="custom_2_proprieta" value="0" class="radiobox style-0" <?php echo ($G->custom_2_proprieta==0 ? 'checked="checked"':''); ?>>
                                          <span>Gas e singoli utenti</span>
                                        </label>
                                    </div>

                                    <div class="radio">
                                        <label>
                                          <input type="radio" name="custom_2_proprieta" value="1" class="radiobox style-0" <?php echo ($G->custom_2_proprieta==1 ? 'checked="checked"':''); ?>>
                                          <span>Solo responsabili GAS</span>
                                        </label>
                                    </div>
                            </div>
                        </div>
                        </fieldset>

                    <input id="act" type="hidden" name="act" value="do_custom_2">
                    <footer>
                            <button type="submit" class="btn btn-primary">
                                Salva i parametri
                            </button>
                    </footer>
           </form>
       </div>
    </div>
</div>

<div class="row margin-top-10">
<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        <div class="well well-sm">
            <form class="smart-form" id="custom_3_form" action="ajax_rd4/gas/_act.php" method="post">

                    <h3>Campo personalizzato 3</h3>
                        <fieldset>
                        <label class="label margin-top-5">Nome</label>
                        <label class="input"> <i class="icon-prepend fa fa-pencil"></i>
                            <input placeholder="" type="text" id="custom_3_nome" name="custom_3_nome" value="<?php echo $G->custom_3_nome ?>">
                            <b class="tooltip tooltip-top-left">
                                <i class="fa fa-warning txt-color-teal"></i>
                                Il nome che intesta la colonna</b>
                        </label>
                        </fieldset>
                        <fieldset>
                        <div class="form-group margin-top-10">
                            <label class="col-md-2 control-label">Tipo di dati</label>
                                <div class="col-md-10">
                                    <div class="radio">
                                        <label>
                                          <input type="radio" name="custom_3_tipo" value="0" class="radiobox style-0" <?php echo ($G->custom_3_tipo==0 ? 'checked="checked"':''); ?> >
                                          <span>Testo</span>
                                        </label>
                                    </div>

                                <div class="radio">
                                    <label>
                                      <input type="radio" name="custom_3_tipo" value="1" class="radiobox style-0" <?php echo ($G->custom_3_tipo==1 ? 'checked="checked"':''); ?>>
                                      <span>Numerici</span>
                                    </label>
                                </div>

                                <div class="radio">
                                    <label>
                                      <input type="radio" name="custom_3_tipo" value="2" class="radiobox style-0" <?php echo ($G->custom_3_tipo==2 ? 'checked="checked"':''); ?>>
                                      <span>SI/NO</span>
                                    </label>
                                </div>

                                <div class="radio">
                                    <label>
                                      <input type="radio" name="custom_3_tipo" value="3" class="radiobox style-0" <?php echo ($G->custom_3_tipo==3 ? 'checked="checked"':''); ?>>
                                      <span>Data (gg/mm/AAAA)</span>
                                    </label>
                                </div>

                            </div>
                        </div>
                        </fieldset>
                        <fieldset>
                        <div class="form-group margin-top-10">
                            <label class="col-md-2 control-label">Visibilità</label>
                                <div class="col-md-10">
                                    <div class="radio">
                                        <label>
                                          <input type="radio" name="custom_3_privato" value="0" class="radiobox style-0" <?php echo ($G->custom_3_privato==0 ? 'checked="checked"':''); ?>>
                                          <span>Tutti gli utenti</span>
                                        </label>
                                    </div>
                                <div class="radio">
                                    <label>
                                      <input type="radio" name="custom_3_privato" value="1" class="radiobox style-0" <?php echo ($G->custom_3_privato==1 ? 'checked="checked"':''); ?>>
                                      <span>Solo utente interessato</span>
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                      <input type="radio" name="custom_3_privato" value="2" class="radiobox style-0" <?php echo ($G->custom_3_privato==2 ? 'checked="checked"':''); ?>>
                                      <span>Solo responsabili GAS</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        </fieldset>
                        <fieldset>
                        <div class="form-group margin-top-10">
                            <label class="col-md-2 control-label">Modifiche</label>
                                <div class="col-md-10">
                                    <div class="radio">
                                        <label>
                                          <input type="radio" name="custom_3_proprieta" value="0" class="radiobox style-0" <?php echo ($G->custom_3_proprieta==0 ? 'checked="checked"':''); ?>>
                                          <span>Gas e singoli utenti</span>
                                        </label>
                                    </div>

                                    <div class="radio">
                                        <label>
                                          <input type="radio" name="custom_3_proprieta" value="1" class="radiobox style-0" <?php echo ($G->custom_3_proprieta==1 ? 'checked="checked"':''); ?>>
                                          <span>Solo responsabili GAS</span>
                                        </label>
                                    </div>
                            </div>
                        </div>
                        </fieldset>

                    <input id="act" type="hidden" name="act" value="do_custom_3">
                    <footer>
                            <button type="submit" class="btn btn-primary">
                                Salva i parametri
                            </button>
                    </footer>
           </form>
       </div>
    </div>



</div>



<div class="row margin-top-10">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <?php echo help_render_html($page_id,$page_title); ?>
    </div>
</div>

<script type="text/javascript">

    pageSetUp();

    var pagefunction = function(){


        //------------HELP WIDGET
        document.title = '<?php echo "ReteDES.it :: $page_title";?>';
        <?php echo help_render_js($page_id);?>
        //------------END HELP WIDGET

        var id;
        var messaggio;

        var $custom_1_form = $('#custom_1_form').validate({
            submitHandler : function(form) {
                    $(form).ajaxSubmit({
                        type:"POST",
                        dataType: 'json',
                        success : function(data) {
                                if(data.result=="OK"){
                                    ok(data.msg);}else{ko(data.msg);}
                                }
                    });
            },
            errorPlacement : function(error, element) {
                error.insertAfter(element.parent());
            }
        });
        var $custom_2_form = $('#custom_2_form').validate({
            submitHandler : function(form) {
                    $(form).ajaxSubmit({
                        type:"POST",
                        dataType: 'json',
                        success : function(data) {
                                if(data.result=="OK"){
                                    ok(data.msg);}else{ko(data.msg);}
                                }
                    });
            },
            errorPlacement : function(error, element) {
                error.insertAfter(element.parent());
            }
        });
        var $custom_3_form = $('#custom_3_form').validate({
            submitHandler : function(form) {
                    $(form).ajaxSubmit({
                        type:"POST",
                        dataType: 'json',
                        success : function(data) {
                                if(data.result=="OK"){
                                    ok(data.msg);}else{ko(data.msg);}
                                }
                    });
            },
            errorPlacement : function(error, element) {
                error.insertAfter(element.parent());
            }
        });

        loadScript("js/plugin/jquery-form/jquery-form.min.js");

    } // end pagefunction


    loadScript("js/plugin/summernote/summernote.min.js", function(){
        loadScript("js/plugin/x-editable/x-editable.min.js", pagefunction);
    });
</script> 