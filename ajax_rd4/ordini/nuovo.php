<?php require_once("inc/init.php");
$ui = new SmartUI;
$page_title= "Nuovo Ordine";
$page_id ="nuovo_ordine";

$var ="ciccio";

//AMAZON S3
$bucket = 'retedes';
$folder = 'public_rd4/note_ordini/'.date('YmdHi').'/';

// these can be found on your Account page, under Security Credentials > Access Keys
$accessKeyId = __AMAZON_S3_ACCESS_KEY;
$secret = __AMAZON_S3_SECRET_KEY;

$policy = base64_encode(json_encode(array(
  // ISO 8601 - date('c'); generates uncompatible date, so better do it manually
  'expiration' => date('Y-m-d\TH:i:s.000\Z', strtotime('+2 days')),
  'conditions' => array(
    array('bucket' => $bucket),
    array('acl' => 'public-read'),
    array('success_action_status' => '201'),
    array('starts-with', '$key', $folder.'/')
  )
)));

$signature = base64_encode(hash_hmac('sha1', $policy, $secret, true));


//AMAZON S3


$title_navbar='<i class="fa fa-shopping-cart fa-2x pull-left"></i> Nuovo ordine!<br><small class="note">Buttati nella mischia!</small>';
?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.1/summernote.css" rel="stylesheet">

<?php echo navbar($title_navbar); ?>

<section id="widget-grid" class="margin-top-10">



    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12col-lg-12">
            <style>.select2-hidden-accessible{display:none}</style>
            <div class="row padding-10">
                <div class="col-12">
                <form action="ajax_rd4/ordini/_act.php" method="post" id="nuovo_ordine_form" class="smart-form">
                    <fieldset>
                        <section>
                            <label class="label">Seleziona il listino tra...</label>
                            <div class="inline-group">
                                <label class="radio">
                                    <input type="radio" name="scelta_listino" value="1" checked="checked">
                                    <i></i>Tutti quelli validi</label>
                                <label class="radio">
                                    <input type="radio" name="scelta_listino" value="2">
                                    <i></i>Quelli che posso gestire</label>
                                <label class="radio">
                                    <input type="radio" name="scelta_listino" value="3">
                                    <i></i>Inseriti dal mio GAS</label>
                            </div>
                        </section>


                        <section>
                            <input class="hidden" id="idlistino" name="idlistino" type="text" value="0">
                            <input  name="act" type="hidden" value="nuovo_ordine">
                            <label for="listalistini" class="label">Seleziona un listino tra quelli disponibili, digitando nel box il suo nome o il nome della ditta</label>

                            <div id="listalistini" style="width:100%" class="" rel=""></div>

                        </section>
                    <hr>
                    <section>
                            <label for="nomeordine" class="label">Inserisci un nome che identifica l'ordine</label>
                            <label class="input">
                                <input type="text" name="nomeordine" placeholder="Nome">
                            </label>
                    </section>

                    <section>
                            <label for="quantigiorni" class="label">Tra quanti giorni questo ordine si deve chiudere</label>
                            <label class="input">
                                <input type="text" name="quantigiorni" placeholder="Giorni">
                                <input type="hidden" name="noteordine" id="noteordine">
                            </label>
                            <p class="alert alert-info margin-top-10"><b>N.B:</b> Il conteggio parte alla mezzanotte di oggi.</p>
                    </section>

                    <footer>
                        <button id="start_ordine" data-go="ok" type="submit" name="submit" class="btn btn-success">
                            <i class="fa fa-save"></i>
                            &nbsp;Inserisci l'ordine
                        </button>
                    </footer>


                    </fieldset>
                    </form>

                    <div class="well well-sm margin-top-10">
                        <h4>Se vuoi aggiungi qua delle note prima di inserire l'ordine.</h4>
                        <br>
                        <textarea rows="5" class="custom-scroll" name="summernoteordine" id="summernoteordine"></textarea>
                    </div>





            </div>
            </div>
            <?php echo help_render_html("nuovo_ordine",$page_title); ?>
        </article>

    </div>

</section>

<script type="text/javascript">
    /* DO NOT REMOVE : GLOBAL FUNCTIONS!
     *
     * pageSetUp(); WILL CALL THE FOLLOWING FUNCTIONS
     *
     * // activate tooltips
     * $("[rel=tooltip]").tooltip();
     *
     * // activate popovers
     * $("[rel=popover]").popover();
     *
     * // activate popovers with hover states
     * $("[rel=popover-hover]").popover({ trigger: "hover" });
     *
     * // activate inline charts
     * runAllCharts();
     *
     * // setup widgets
     * setup_widgets_desktop();
     *
     * // run form elements
     * runAllForms();
     *
     ********************************
     *
     * pageSetUp() is needed whenever you load a page.
     * It initializes and checks for all basic elements of the page
     * and makes rendering easier.
     *
     */

    pageSetUp();


    var pagefunction = function() {

        //-------------------------HELP
        <?php echo help_render_js($page_id); ?>
        //-------------------------HELP

        var tipo = 1;


        //NOTE

        function sendFile(file, editor, welEditable, dup, container) {
          console.log("sendfile acting...");

          formData = new FormData();
          formData.append('key', '<?php echo $folder; ?>/' + file.name);
          formData.append('AWSAccessKeyId', '<?php echo $accessKeyId; ?>');
          formData.append('acl', 'public-read');
          formData.append('policy', '<?php echo $policy; ?>');
          formData.append('signature', '<?php echo $signature; ?>');
          formData.append('success_action_status', '201');
          formData.append('file', file);

          $.ajax({
            data: formData,
            dataType: 'xml',
            type: "POST",
            cache: false,
            contentType: false,
            processData: false,
            url: "https://<?php echo $bucket ?>.s3.amazonaws.com/",
            success: function(data) {
              console.log("sendfile success!!");
              // getting the url of the file from amazon and insert it into the editor
              var url = $(data).find('Location').text();
              //editor.insertImage(welEditable, url);
              $(container).summernote('editor.insertImage', url);
              $('.sto_caricando').hide();
            }
          });
        }


        $('#summernoteordine').summernote({
            height : 180,
            focus : false,
            tabsize : 2,
            toolbar: [
                //[groupname, [button list]]
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'picture']]
              ],

            callbacks:{
                onImageUpload: function(files, editor, $editable) {
                    $('.sto_caricando').show();
                    console.log("calling sendfile...");
                    $.each(files, function (idx, file) {
                            console.log("calling for "+file.name);
                            sendFile(file,editor,$editable,file.name,'#summernoteordine');
                    });
                },
                onChange: function ($editable, sHtml) {
                  //console.log($editable, sHtml);
                  $('#noteordine').val($editable);
                }
            }

        });
        //NOTE

        $(document).on("change","input[type='radio'][name='scelta_listino']",function(){
            var selected = $("input[type='radio'][name='scelta_listino']:checked");
            if (selected.length > 0) {
                tipo = selected.val();
            }
        });

        $("#listalistini").select2({
            placeholder: "Cerca tra i listini..",
                //minimumInputLength: 3,
                ajax: {
                url: "ajax_rd4/ordini/inc/listalistini.php",
                dataType: 'json',
                data: function (term, page) {
                    return {
                        q: term,
                        t: tipo
                    };
                },
                results: function (data, page) {
                    return { results: data };
                }
            },
            formatResult: function(data){
                return '<span>'+data.descrizione_ditte+'</span><br><span>#'+data.id+'</span> <span><strong>'+data.text+'</strong><span><br><span class="font-xs">'+data.fullname+', '+data.descrizione_gas+' (valido fino al <b>'+data.data_valido+'</b>)</span>' ;
            },
            escapeMarkup: function(m) { return m; }
    });
    $('#listalistini').on("select2-selecting",
        function(e) {
                console.log(e.val);
                $('#idlistino').val(e.val);
                //PRENDE LE NOTE LISTINO
                $.ajax({
                          type: "POST",
                          url: "ajax_rd4/ordini/_act.php",
                          dataType: 'json',
                          data: {act: "prendi_note_listino", id:e.val},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                $('#summernoteordine').summernote('code', data.html);
                                console.log(data.html);
                            }else{

                                //ko(data.msg);
                            }

                        });
                //PRENDE LE NOTE LISTINO
        });
     var $orderForm = $('#nuovo_ordine_form').validate({
                        ignore: ".select2-focusser, .select2-input",

                        // Rules for form validation
                        rules : {
                            nomeordine : {
                                required : true
                            },
                            quantigiorni : {
                                required : true,
                                digits: true
                            },
                            idlistino : {
                                required : true,
                                digits: true,
                                min: 1
                            }
                        },

                        // Messages for form validation
                        messages : {
                            nomeordine : {
                                required : 'E\' necessario indicare un nome per l\'ordine'
                            },
                            quantigiorni : {
                                required : 'E\' necessario indicare quanti giorni dura l\'ordine'
                            },
                            idlistino :{
                                required : 'Devi indicare un listino!',
                                min: "E\' necessario indicare un listino!"
                            }
                        },

                        // Do not change code below
                        errorPlacement : function(error, element) {
                            error.insertAfter(element.parent());
                        },
                        submitHandler: function(form) {

                            // ORDINE NORMALE
                            $.SmartMessageBox({
                                title : "Stai per inserire un nuovo ordine!",
                                content : "Questo ordine è in coda e si aprirà in automatico tra 2 ore. Se vuoi fare delle modifiche o eliminarlo vai nella pagina I MIEI ORDINI",
                                buttons : '[OK][ANNULLA]'
                            }, function(ButtonPressed) {
                                if (ButtonPressed === "OK") {
                                    $(form).ajaxSubmit({
                                        dataType: 'json',
                                        success: function(data, status) {
                                            console.log(data.result + ' - ' + data.msg);
                                            if(data.result=="OK"){
                                                ok(data.msg);
                                                window.setTimeout(function(){
                                                    window.location.href = "#ajax_rd4/ordini/edit.php?id="+data.id;
                                                }, 5000);
                                            }else{
                                                ko(data.msg);
                                            }
                                        }
                                    });

                                }
                            });
                            // ORDINE NORMALE

                        }
                    });




    }
    // end pagefunction
    loadScript("js/plugin/jquery-form/jquery-form.min.js",
        loadScript("js/plugin/summernote/new_summernote.min.js", pagefunction)
    );


</script>
