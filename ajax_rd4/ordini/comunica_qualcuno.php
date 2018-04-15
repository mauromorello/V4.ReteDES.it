<?php
require_once("inc/init.php");
if(file_exists("../../lib_rd4/class.rd4.ordine.php")){require_once("../../lib_rd4/class.rd4.ordine.php");}
if(file_exists("../lib_rd4/class.rd4.ordine.php")){require_once("../lib_rd4/class.rd4.ordine.php");}
$ui = new SmartUI;
$converter = new Encryption;

$page_title = "Comunica a qualche utente partecipante";
$page_id = "comunica_qualcuno";


$id_ordine = CAST_TO_INT($_POST["id"],0);
if ($id_ordine==0){
    $id_ordine = CAST_TO_INT($_GET["id"],0);
}

if($id_ordine==0){echo rd4_go_back("KO!");die;}

if (!posso_gestire_ordine_come_gas($id_ordine)){
    echo rd4_go_back("Non ho i permessi necessari");die;
}
$filter='';
if (!posso_gestire_ordine($id_ordine)){
    $filter=" AND maaking_users.id_gas= "._USER_ID_GAS." ";
}


$O=new ordine($id_ordine);

$sql = "SELECT
        maaking_users.fullname,
        maaking_users.email,
        maaking_users.user_site_option,
        retegas_referenze.id_gas_referenze,
        retegas_gas.descrizione_gas,
        maaking_users.userid
        FROM
        retegas_ordini
        Inner Join retegas_referenze ON retegas_ordini.id_ordini = retegas_referenze.id_ordine_referenze
        Inner Join maaking_users ON retegas_referenze.id_gas_referenze = maaking_users.id_gas
        Inner Join retegas_gas ON retegas_referenze.id_gas_referenze = retegas_gas.id_gas
        WHERE
        retegas_ordini.id_ordini =:id_ordine
        AND
        maaking_users.isactive = 1
        $filter ;";
$stmt = $db->prepare($sql);
$stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);


foreach ($rows as $row) {

    $useridEnc = $converter->encode($row["userid"]);

    $p1 = '<img class="" src="'.src_user($row["userid"],64).'" style="width:32px;height:32px;">';

    $valore_ordine = _NF(VA_ORDINE_USER($id_ordine,$row["userid"]));
    if($valore_ordine>0){
        $partecipa="SI";
    }else{
        $valore_ordine = 0;
        $partecipa="NO";
    }


    $z .= '<tr rel="'.$useridEnc.'">
            <td><label class="checkbox"><input type="checkbox" class="utente" value="'.$row["userid"].'"><i></i></label></td>
            <td>'.$p1.'</td>
            <td><a href="#ajax_rd4/user/scheda.php?id='.$useridEnc.'">'.$row["fullname"].'</a></td>
            <td>'.$row["descrizione_gas"].'</td>
            <td>'.$partecipa.'</td>
            <td>'.$valore_ordine.'</td>
          </tr>';

}


$a='    <div class="" style="max-height:600px;overflow-y:auto;">
        
        
        <table id="dt_utenti_gas" class="table table-striped margin-top-10 has-tickbox smart-form">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>Nome</th>
                                        <th><i class="fa fa-home"></i>&nbsp;GAS</th>
                                        <th>partecipa</th>
                                        <th>per Euro</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    '.$z.'
                                </tbody>
                            </table>
        </div>
        <div class="well margin-top-5">
            <label>Messaggio:</label>
            <textarea  id="messaggio_a_utenti" style="width:100%;" rows="10">
            
            
            
            </textarea>
            <p></p>
            <p><button class="btn btn-default" id="manda_messaggio_a_utenti"><i class="fa fa-envelope"></i>   manda agli utenti selezionati questo messaggio.</button></p>
            <div class="alert alert-info"><strong>ATTENZIONE:</strong> Non abusare di questa funzione. A nessuno piace ricevere mail inutili.</div>
        </div>

        ';

        $mp ='
        <div class="row ">
            <div class=" col-xs-4">
                <h4>Filtra la tabella:</h4>
                <div class="btn-group-vertical btn-block">
                    <a class="show_Tutti btn btn-default">Tutti</a>
                    <a class="show_Partecipa btn btn-default">Che partecipano </a>
                    <a class="show_NonPartecipa btn btn-default">Che non partecipano</a>
                </div>
            </div>

            <div class="col-xs-4">
                <h4>Seleziona i destinatari</h4>
                <p>Cliccando sul check a sinistra del nome oppure</p>
                <form class="smart-form">
                    <section>
                        <label class="pull-left">
                            <input class="selectall" type="checkbox"> Seleziona / deseleziona tutti
                        </label>
                    </section>
                </form>
            </div>
            <div class="col-xs-4">
            </div>
        </div>
      ';

      //AMAZON S3
    $bucket = 'retedes';
    $folder = 'public_rd4/bacheca/ordine/'.$O->id_ordini.'/messaggi';

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
      
      
?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.1/summernote.css" rel="stylesheet">

<?php echo $O->navbar_ordine(); ?>

<h3>Comunica a qualche utente legato a questo ordine.</h3>
<?php echo $mp.'<hr>'.$a; ?>

<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
        </article>
    </div>
</section>

<!-- Dynamic Modal -->
<div class="modal fade" id="remoteModal" tabindex="-1" role="dialog" aria-labelledby="remoteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- content will be filled here from "ajax/modal-content/model-content-1.html" -->
        </div>
    </div>
</div>
                        <!-- /.modal -->

<script type="text/javascript">

    pageSetUp();

    //BACHECA AMAZON S3
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


    var pagefunction = function(){



        console.log("pagefunction");
        //------------HELP WIDGET
        document.title = '<?php echo "ReteDES.it :: $page_title";?>';
        <?php echo help_render_js($page_id);?>
        //------------END HELP WIDGET
        var oTable= $('#dt_utenti_gas').dataTable({
                                            "bPaginate": false
                                        });
        var id;
        var messaggio;
        
        $('#messaggio_a_utenti').summernote({
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
                            sendFile(file,editor,$editable,file.name,'#messaggio_a_utenti');
                    });
                }
            }

        });
        
        
        

        $('#manda_messaggio_a_utenti').click(function(){
            console.log("Click");
            values = $('input:checkbox:checked.utente').map(function () {
              return this.value;
            }).get();
            var messaggio = $('#messaggio_a_utenti').summernote('code');
            //messaggio = $('#messaggio_a_utenti').val();
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
                    $.blockUI();
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/gas/_act.php",
                          dataType: 'json',
                          data: {act: "messaggia_utenti", values : values, messaggio : messaggio},
                          context: document.body
                        }).done(function(data) {
                            $.unblockUI();
                            if(data.result=="OK"){
                                    ok(data.msg);
                                    $('#messaggio_a_utenti').val('');
                                    //location.reload();
                            }else{
                                ko(data.msg);
                            }

                        });
                }
            });




        }//messaggio vuoto

        });


        $('.selectall').click(function(event) {  //on click
            console.log("Click select");
            if(this.checked) { // check select status
                $('.utente').each(function() { //loop through each checkbox
                    this.checked = true;  //select all checkboxes with class "checkbox1"
                });
            }else{
                $('.utente').each(function() { //loop through each checkbox
                    this.checked = false; //deselect all checkboxes with class "checkbox1"
                });
            }
        });





        $('body').on('hidden.bs.modal', '.modal', function () {
          $(this).removeData('bs.modal');
        });

        $('body').on('shown.bs.modal','.modal', function(e) {
            console.log("Modal opened");
            id = $(e.relatedTarget).attr('data-id');
        });


        $(document).on( 'change', '#usermessage', function() {
            messaggio = $(this).val();
            console.log("messaggio = " + messaggio);

        });

        $('.show_Partecipa').click(function(){oTable.fnFilter( 'SI',4 );});
        $('.show_NonPartecipa').click(function(){oTable.fnFilter( 'NO',4 );});
        $('.show_Tutti').click(function(){  oTable.fnFilter('',4);
                                            oTable.fnFilter('');}
        );

        //$('#dt_utenti_gas thead th').each( function () {
        //    var title = $(this).text();
        //    $(this).html( '<input type="text" placeholder="Filtra '+title+'" />' );
        //} );

        //METTE I LINK IN FONDO AL MESSAGGIO
        $('#messaggio_a_utenti').summernote('code','<br><br><br><hr><a href="<?php echo $O->url_scheda_ordine(); ?>">LINK ALL\'ORDINE</a><br><a href="<?php echo $O->url_scheda_produttore(); ?>">LINK AL PRODUTTORE</a>');
        
        

    } // end pagefunction


    loadScript("js/plugin/summernote/new_summernote.min.js", function(){
        loadScript("js/plugin/datatables/jquery.dataTables.min.js", function(){
            loadScript("js/plugin/datatables/dataTables.colVis.min.js", function(){
                loadScript("js/plugin/datatables/dataTables.tableTools.min.js", function(){
                    loadScript("js/plugin/datatables/dataTables.bootstrap.min.js", function(){
                        loadScript("js/plugin/datatable-responsive/datatables.responsive.min.js", function(){
                                loadScript("js/plugin/x-editable/x-editable.min.js", pagefunction())
                                    
                        });
                    });
                });
            });
        });
    });
</script>