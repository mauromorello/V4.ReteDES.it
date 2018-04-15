<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.ordine.php");

$ui = new SmartUI;
$page_title= "Gestisci Ordine";
$page_id= "edit_ordine";

//CONTROLLI
$id_ordine = (int)$_GET["id"];

if (!posso_gestire_ordine($id_ordine)){
    echo rd4_go_back("Non ho i permessi necessari");die;
}

$O = new ordine($id_ordine);

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


//ordine futuro ? si può cambiare la data di apertura
$data_apertura = $O->data_apertura_time;
$data_chiusura = $O->data_chiusura_time;
$data_now = strtotime(date("d-m-Y H:i"));


if($data_apertura>$data_now){
    $ed_ap="";
    $ic_ap="fa-pencil";
    $btn_ap ='<a  class="btn btn-success btn-md" id="start_ordine">FAI PARTIRE SUBITO</a >';
    $btn_ch ='<a  class="btn btn-default btn-md disable">CHIUDI  SUBITO</a >';
    $btn_co ='<a  class="btn btn-default btn-md disable">CONVALIDA</a >';
}else{
    $ed_ap=" disabled ";
    $ic_ap="fa-lock";

    $btn_ap ='<a  class="btn btn-default btn-md disabled" >FAI PARTIRE  SUBITO</a >';

    if($data_chiusura>$data_now){
        $btn_co ='<a  class="btn btn-default btn-md disabled">CONVALIDA</a >';
        $btn_ch ='<a  class="btn btn-danger btn-md" id="end_ordine"  >CHIUDI SUBITO</a >';
    }else{
        $btn_ch ='<a  class="btn btn-default btn-md disabled" >CHIUDI SUBITO</a >';
        if($O->is_printable<>1){
            $btn_co ='<a  class="btn btn-success btn-md" id="convalida_ordine" data-id_ordine="'.$O->id_ordini.'">CONVALIDA</a >';
        }else{
            $btn_co ='<a  class="btn btn-success btn-md" id="ripristina_ordine" data-id_ordine="'.$O->id_ordini.'">RIPRISTINA</a >';
        }
    }
}
$btn_el ='<a class="btn btn-danger btn-xs" id="elimina_ordine" data-id_ordine="'.$O->id_ordini.'">ATTENZIONE: ELIMINAZIONE ORDINE</a >';


//QUERY PER DISTANZA
//(select ROUND((DEGREES(ACOS((SIN(RADIANS((SELECT user_gc_lat FROM maaking_users WHERE userid =2))) * SIN(RADIANS(G.gas_gc_lat))) + (COS(RADIANS((SELECT user_gc_lat FROM maaking_users WHERE userid =2 ))) * COS(RADIANS(G.gas_gc_lat)) * COS(RADIANS(G.gas_gc_lng -(SELECT user_gc_lng FROM maaking_users WHERE userid = 2)))))) * 69.09) * 1.609344) km from maaking_users WHERE userid = U.userid) km



$stmt = $db->prepare("SELECT    G.id_gas,
                                G.descrizione_gas,
                                G.gas_gc_lat as lat,
                                G.gas_gc_lng as lng,
                                COUNT(U.userid) as utenti,
                                D.des_descrizione,
                                O.valore_text,
                                D.id_des,
                                (select ROUND(
                                    (DEGREES
                                        (ACOS
                                            (
                                                (SIN
                                                    (RADIANS
                                                        (
                                                            ("._USER_GAS_LAT.")
                                                        )
                                                    ) * SIN(
                                                            RADIANS(G.gas_gc_lat)
                                                        )
                                                )
                                            + (COS
                                                (RADIANS
                                                    (
                                                        ("._USER_GAS_LAT.")
                                                    )
                                                )
                                            * COS(
                                                RADIANS(G.gas_gc_lat)
                                            )
                                            * COS(
                                                RADIANS(
                                                    G.gas_gc_lng - ("._USER_GAS_LNG.")
                                                        )
                                                 )
                                             )
                                             )
                                        ) * 69.09
                                    )
                                    * 1.609344) km
                                FROM maaking_users WHERE userid = U.userid
                            ) km
                FROM retegas_gas G
                inner join maaking_users U on U.id_gas = G.id_gas
                left join retegas_des D on D.id_des = G.id_des
                left join retegas_options O on O.id_gas = G.id_gas
                WHERE G.id_gas <> "._USER_ID_GAS."
                    AND G.id_gas >0
                    AND D.id_des >0
                    AND U.isactive=1
                    AND O.chiave = '_GAS_PUO_PART_ORD_EST'
                GROUP BY U.id_gas
                ORDER by KM asc
                LIMIT 100;
                ");


         $stmt->execute();
         $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
         $p  ='<div class="table-responsive">
                    <table class="table table-striped smart-form has-tickbox form-horizontal" id="dt_basic">';
         $p .='<thead>
                    <tr>
                        <th></th>
                        <th>GAS</th>
                        <th>Referente</th>
                        
                        <th>Distanza (Km)</th>
                        <th>Condivisione</th>
                    </tr>
                </thead>
         <tbody>';
         foreach ($rows as $row) {
             
             if($row["valore_text"]=="SI"){

                 //RECORD DELLE REFERENZE
                 $stmt = $db->prepare("SELECT * from retegas_referenze WHERE id_ordine_referenze=:id_ordine AND id_gas_referenze='".$row["id_gas"]."' LIMIT 1;");
                 $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
                 $stmt->execute();
                 $rowREF = $stmt->fetch();
                 
                 if($stmt->rowCount()>0){
                    $col = ' success ';
                    $checked = ' checked="checked" ';
                    $icona = " fa-check-circle text-success";
                    $id_referente = $rowREF["id_utente_referenze"];

                    if($id_referente>0){
                        $fullname_referente = rd4_db_value("maaking_users","fullname","userid=".$id_referente);    
                    }else{
                        //OPZIONE SE SI PUO' IMPOSTARE REFERENTE
                        $stmt = $db->prepare("SELECT * FROM retegas_options WHERE id_gas =:id_gas AND chiave ='_USER_GAS_PERM_GEST_EST';");
                        $stmt->bindValue(':id_gas', $row["id_gas"], PDO::PARAM_INT);
                        $stmt->execute();
                        $rowPER = $stmt->fetch(PDO::FETCH_ASSOC);
                        if($rowPER["valore_text"]=="SI"){
                            $fullname_referente='<label class="input"><i class="save_referente fa fa-save icon-append text-success" style="cursor:pointer" data-id="'.$rowREF["id_referenze"].'"></i><input id="input_'.$rowREF["id_referenze"].'" type="text" value="'.$rowREF["id_utente_referenze"].'"></label>';    
                        }else{
                            $fullname_referente='';
                        }
                        
                    }
                 
                 }else{
                    $col = '';
                    $checked = '';
                    $icona = "";
                    $fullname_referente = '';
                    
                 }

                 
                 
                 
                 $stmt = $db->prepare("SELECT * from retegas_dettaglio_ordini O inner join maaking_users U on U.userid = O.id_utenti WHERE U.id_Gas=".$row["id_gas"]." AND O.id_ordine=:id_ordine");
                 $stmt->bindParam(':id_ordine', $_GET["id"], PDO::PARAM_INT);
                 $stmt->execute();

                 if($stmt->rowCount()>0){
                    $col = ' warning ';
                    $checked = '';
                    $icona = "fa-cubes ";
                    $tooltip = ' rel="tooltip" data-original-title="Questo GAS ha già articoli in ordine" data-container="body" ';
                    $tb ="";
                 }else{
                    $tb = '<label class="checkbox"><input class="gas_partecipa" type="checkbox" value="'.$row["id_gas"].'" '.$checked.'><i></i></label>';
                    $tooltip='';
                 }




             }else{
                $tb = '&nbsp;';
                $col = ' danger ';
                $icona = "fa-lock";
                $tooltip = ' rel="tooltip" data-original-title="Questo GAS non vuole condividere ordini" data-container="body" ';
             }

             if($row["id_des"]<>_USER_ID_DES){
                $des ="<span> ".$row["des_descrizione"]." </span>";
             }else{
                $des ='';
             }



             $p.="<tr class=\"$col\">";
                $p.='<td>'.$tb.'</td>';
                $p.= "  <td> ".$row["descrizione_gas"]."&nbsp;<span class=\"note\">(".$row["utenti"].")</span>&nbsp;$des</td>
                        <td>".$fullname_referente."</td>
                        <td>".$row["km"]."</td>
                        <td><span $tooltip ><i class=\"fa $icona\"></i></span></td>";
                $p.="</tr>";
         }
         $p.="</tbody>
                </table>
                    </div>";



//OPERAZIONI
if (posso_gestire_ordine($id_ordine)){
    $pagina_rettifiche ='<div class="well text-center"><a href="#ajax_rd4/rettifiche/start.php?id='.$id_ordine.'" class="btn btn-md btn-primary btn-block font-md">RETTIFICHE..</a><br><span class="font-xs">...fai coincidere il totale reale con quello di reteDES.</span></div>';
}else{
    $pagina_rettifiche ='';
}

if (posso_gestire_ordine($id_ordine)){
    $pagina_report ='<div class="well text-center"><a href="#ajax_rd4/ordini/report.php?id='.$id_ordine.'" class="btn btn-md btn-primary btn-block font-md">REPORT..</a><br><span class="font-xs">...visualizza il pannello dei report.</span></div>';
    $pagina_aiuti = '<div class="well text-center"><a href="#ajax_rd4/ordini/aiutanti.php?id='.$id_ordine.'" class="btn btn-md btn-success btn-block font-md">AIUTI..</a><br><span class="font-xs">...gestisci i tuoi aiutanti.</span></div>';
    $pagina_referenti = '<div class="well text-center"><a href="#ajax_rd4/ordini/referenti_extra.php?id='.$id_ordine.'" class="btn btn-md btn-info btn-block font-md">REFERENTI..</a><br><span class="font-xs">...gestisci i referenti extra.</span></div>';
    $pagina_comunica ='<div class="well text-center"><a href="#ajax_rd4/ordini/comunica.php?id='.$id_ordine.'" class="btn btn-md btn-default btn-block font-md">COMUNICA..</a><br><span class="font-xs">...agli utenti di questo ordine.</span></div>';
}else{
    $pagina_report ='';
    $pagina_aiuti = '';
    $pagina_referenti = '';
    $pagina_comunica ='';
}
if (_USER_PERMISSIONS & perm::puo_gestire_la_cassa){
    if($O->convalidato_gas){
        $pagina_cassa ='<div class="well text-center"><a href="#ajax_rd4/ordini/cassa.php?id='.$id_ordine.'" class="btn btn-md btn-warning btn-block font-md">CASSA..</a><br><span class="font-xs">...allinea la cassa con le cifre di questo ordine.</span></div>';
    }else{
        $pagina_cassa ='<div class="well text-center"><button class="btn btn-md btn-warning btn-block font-md btn-disabled" disabled="DISABLED">CASSA..</button><br><span class="font-xs">...allinea la cassa con le cifre di questo ordine.</span></div>';
    }
}else{
    $pagina_cassa ='';
}




$apertura = strtotime($row["data_apertura"]);
$chiusura = strtotime($row["data_chiusura"]);
$today = strtotime(date("Y-m-d H:i"));
if($apertura>$today){$color="text-info";}
if($chiusura>$today AND $apertura<$today){$color="text-success";}
if($chiusura<$today){$color="text-danger";}
if($row["is_printable"]>0){$color="text-muted";}

if($O->id_utente==_USER_ID){
    $gestore = "Gestore";
}else{
    $gestore = "";
}
//$stmt = $db->prepare("select * from retegas_referenze where id_utente_referenze='"._USER_ID."' AND id_gas_referenze='"._USER_ID_GAS."' AND id_ordine_referenze=:id_ordine");
//$stmt->bindParam(':id_ordine', $O->id_ordini , PDO::PARAM_INT);
//$stmt->execute();
//if($stmt->rowCount()>0){
    $gestoreGAS ="Gestore GAS";
    $pagina_gas ='<div class="well text-center"><a href="#ajax_rd4/ordini/edit_gas.php?id='.$id_ordine.'" class="btn btn-md btn-warning btn-block font-md">GAS..</a><br><span class="font-xs">...gestisci la parte GAS di questo ordine.</span></div>';

//}else{
//    $gestoreGAS ="";
//    $pagina_gas ='';
//
//}

if($O->id_gas_referente==_USER_ID_GAS){
    if(_USER_PERMISSIONS & perm::puo_vedere_tutti_ordini){
            $supervisore = "Supervisore";
    }
}


?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.1/summernote.css" rel="stylesheet">


<?php echo $O->navbar_ordine(); ?>

<div class="row margin-top-10">
    <div class="col-md-4">
        <?php echo $pagina_rettifiche; ?>
        <?php echo $pagina_referenti; ?>
        <?php echo $pagina_comunica; ?>
    </div>
    <div class="col-md-4">
        <?php echo $pagina_report; ?>
        <?php echo $pagina_gas; ?>
    </div>
    <div class="col-md-4">
        <?php echo $pagina_cassa; ?>
        <?php echo $pagina_aiuti; ?>
    </div>
</div>


<div class="row">

    <fieldset class="col col-md-6">
        <h1>Anagrafiche</h1>
        <div class="well well-sm">

            <section class="margin-top-10">
                <label for="descrizione_ordini">Titolo</label>
                <h3><i class="fa fa-pencil pull-right"></i>&nbsp;&nbsp;<span class="editable" id="descrizione_ordini" data-pk="<?php echo $O->id_ordini ?>"><?php echo $O->descrizione_ordini ?></span></h3>
            </section>

            <section class="margin-top-10">
                <label for="summernote">Note</label>
                <div id="summernote"><?php echo $O->note_ordini; ?></div>
                <button class="btn btn-primary pull-right margin-top-10" id="go_note_ordini">Salva le note</button>
                <div class="clearfix"></div>
            </section>
        </div>

        <h1>Date &amp; scadenze</h1>
        <div class="well well-sm">

            <section class="margin-top-10">
                    <div class="form-group">
                        <label>Data di apertura:</label>
                        <div class="input-group">
                            <input type="text" id="data_apertura" placeholder="Scegli una data" class="form-control datepicker" data-dateformat="dd/mm/yy" value="<?php echo $O->data_apertura_solo_data; ?>">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        </div>
                        <label>Ora di apertura:</label>
                        <div class="input-group">
                            <input class="form-control" id="clockpicker_apertura" type="text" placeholder="Scegli l'ora" data-autoclose="false" name="ora_apertura" value="<?php echo $O->data_apertura_solo_ora; ?>">
                            <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                        </div>
                        <div class="input-group-btn">
                            <button type="submit" class="btn btn-primary pull-right margin-top-10 <?php echo $ed_ap; ?>" id="go_data_apertura">Salva</button>
                        </div>
                    </div>
            </section>
            <hr>
            <section class="margin-top-10">
                    <div class="form-group">
                        <label>Data di chiusura:</label>
                        <div class="input-group">
                            <input type="text" id="data_chiusura" placeholder="Scegli una data" class="form-control datepicker" data-dateformat="dd/mm/yy" value="<?php echo $O->data_chiusura_solo_data; ?>">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        </div>
                        <label>Ora di chiusura:</label>
                        <div class="input-group">
                            <input class="form-control" id="clockpicker_chiusura" type="text" placeholder="Scegli l'ora" data-autoclose="false" name="ora_chiusura" value="<?php echo $O->data_chiusura_solo_ora; ?>">
                            <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                        </div>
                        <div class="input-group-btn ">
                            <button type="submit" class="btn btn-primary pull-right margin-top-10" id="go_data_chiusura">Salva</button>
                        </div>
                    </div>
            </section>
        </div>
    </fieldset>

    <fieldset class="col col-md-6">
        
        

        <h1>Listino</h1>
        <div class="well well-sm">

            <section class="margin-top-10">
            <fieldset>
                <div class="input-group">
                    <span class="input-group-addon">
                        <span class="radio">
                            <label>
                                <input type="radio" class="tipo_listino radiobox style-0" name="tipo_listino" value="STD" <?php if($O->get_tipo_listino()=='STD') echo ' checked="CHECKED" '; ?>>
                                <span> STANDARD</span> 
                            </label>
                        </span>
                    </span>
                    <span class="input-group-addon">
                        <span class="radio">
                            <label>
                                <input type="radio" class="tipo_listino radiobox style-0" name="tipo_listino" value="ORD" <?php if($O->get_tipo_listino()=='ORD') echo ' checked="CHECKED" '; ?>>
                                <span> LEGATO ALL'ORDINE</span> 
                            </label>
                        </span>
                    </span>
                </div>
                
                </fieldset>    
            </section>
        </div>
        
        <h1>Operazioni</h1>
        <div class="well well-sm">

            <section class="margin-top-10">
                <div class="margin-top-10 ">
                    <label><i class="fa fa-power-off"></i> Apri / Chiudi</label><br>
                    <div class="btn-group btn-group-justified"><?php echo $btn_ap.$btn_ch;?></div>
                </div>
                <div class="margin-top-10 ">
                    <label><i class="fa fa-warning fa-spin"></i>  Elimina</label><br>
                    <div class="btn-group btn-group-justified"><?php echo $btn_el ?></div>
                </div>
            </section>
        </div>

        <h1>Previsione spannometrica dei costi</h1>
        <div class="well well-sm">

            <section class="margin-top-10">
                <label for="costo_gestione">Costo gestione</label><br>
                <i class="fa fa-euro fa-2x"></i><i class="fa fa-pencil fa-2x pull-right"></i>&nbsp;&nbsp;<a class="font-xl costi" id="costo_gestione" data-type="text"   data-pk="<?php echo $O->id_ordini ?>" data-original-title="Costo gestione previsto:"><?php echo$O->costo_gestione; ?></a>
            </section>

            <section class="margin-top-10">
                <label for="costo_trasporto" >Costo trasporto</label><br>
                <i class="fa fa-euro fa-2x"></i><i class="fa fa-pencil fa-2x pull-right"></i>&nbsp;&nbsp;<a class="font-xl costi" id="costo_trasporto" data-type="text"   data-pk="<?php echo $O->id_ordini ?>" data-original-title="Costo trasporto previsto:"><?php echo $O->costo_trasporto; ?></a>
            </section>
        </div>
        
    </fieldset>



</div>
 <?php
 if($O->is_printable<>1){
        if(_USER_GAS_PUO_COND_ORD_EST){ ?>
            <div class="margin-top-10 well well-sm">
                <h3>Condivisione con i gas esterni <small>seleziona i gas con i quali condividerai l'ordine.</small></h3>
                <?php echo $p; ?>
           </div>
<?php   }
 }
?>

<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
        </article>
    </div>
</section>

<script type="text/javascript">

    pageSetUp();


    var pagefunction = function() {

        //-------------------------HELP
        //document.title = escape('<?php echo "ReteDES.it :: ".$O->descrizione_ordini;?>');
        <?php echo help_render_js($page_id); ?>
        //-------------------------HELP

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


        $.fn.editable.defaults.url = 'ajax_rd4/ordini/_act.php';

        /*TABELLA GAS*/

        var oTable= $('#dt_basic').dataTable(
                        {"bPaginate": true,
                        "aoColumnDefs": [
                                { "sType": "numeric" }
                            ]
                        }
                        );

        /*$('#dt_basic').dataTable({
            "sDom": "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'l>r>"+
                "t"+
                "<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-xs-12 col-sm-6'p>>",
            "autoWidth" : true
        });*/

        /*TABELLA GAS*/



        var editable = $('.editable').editable({
            ajaxOptions: { dataType: 'json' },
            success: function(response, newValue) {
                        console.log(response);
                        if(response.result == 'KO'){
                            return response.msg;
                        }
                    }
        });



        //DATA APERTURA
        $('#go_data_apertura').click(function(e){
            var value = $('#data_apertura').val() + " " + $('#clockpicker_apertura').val();
            $.ajax({
              type: "POST",
              url: "ajax_rd4/ordini/_act.php",
              dataType: 'json',
              data: {name: "data_apertura", pk: <?php echo $O->id_ordini; ?>, value:value},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                        ok(data.msg);}else{ko(data.msg);}
                        //location.reload();
            });
        });

        //DATA CHIUSURA
        $('#go_data_chiusura').click(function(e){
            var value = $('#data_chiusura').val() + " " + $('#clockpicker_chiusura').val();
            $.ajax({
              type: "POST",
              url: "ajax_rd4/ordini/_act.php",
              dataType: 'json',
              data: {name: "data_chiusura", pk: <?php echo $O->id_ordini; ?>, value:value},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                        ok(data.msg);}else{ko(data.msg);}
                        //location.reload();
            });
        });

        $('.costi').editable({
                ajaxOptions: { dataType: 'json' },
                success: function(response, newValue) {
                        console.log(response);
                        if(response.result == 'KO'){
                            return response.msg;
                        }
                    }
            });
        $('#summernote').summernote({
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
                            sendFile(file,editor,$editable,file.name,'#summernote');
                    });
                },
                onChange: function ($editable, sHtml) {
                  //console.log($editable, sHtml);
                  $('#noteordine').val($editable);
                }
            }

        });
        //NOTE



       $("#start_ordine").click(function(e) {

            $.SmartMessageBox({
                title : "Apri subito questo ordine",
                content : "Cliccando si OK l'ordine si aprirà il prima possibile (ci vorranno circa 10 minuti...)",
                buttons : "[Annulla][OK]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="OK"){
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/ordini/_act.php",
                          dataType: 'json',
                          data: {act: "start_ordine",pk:<?php echo $O->id_ordini; ?>},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    ok(data.msg);}else{ko(data.msg);}
                                    //location.reload();
                        });
                }
            });

            e.preventDefault();
        })
        $("#end_ordine").click(function(e) {

            $.SmartMessageBox({
                title : "Chiudi subito questo ordine",
                content : "Cliccando su OK l'ordine si chiuderà il prima possibile (ci vorranno circa 10 minuti...)",
                buttons : "[Annulla][OK]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="OK"){
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/ordini/_act.php",
                          dataType: 'json',
                          data: {act: "end_ordine", pk:<?php echo $O->id_ordini; ?>},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    ok(data.msg);}else{ko(data.msg);}
                                    //location.reload();
                        });
                }
            });

            e.preventDefault();
        })
        $("#convalida_ordine").click(function(e) {
            var id_ordine=$(this).data("id_ordine");
            $.SmartMessageBox({
                title : "Convalida questo ordine",
                content : "La convalida dell\'ordine serve ad avvertire i referenti GAS che tutti gli importi globali sono corretti e confermati.",
                buttons : "[Annulla][OK]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="OK"){
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/ordini/_act.php",
                          dataType: 'json',
                          data: {act: "convalida_ordine", id_ordine:id_ordine},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    okReload(data.msg);}else{ko(data.msg);}

                        });
                }
            });

            e.preventDefault();
        });

        $("#ripristina_ordine").click(function(e) {
            var id_ordine=$(this).data("id_ordine");
            $.SmartMessageBox({
                title : "Ripristina questo ordine",
                content : "Il ripristino dell\'ordine serve a correggere eventuali errori.",
                buttons : "[Annulla][OK]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="OK"){
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/ordini/_act.php",
                          dataType: 'json',
                          data: {act: "ripristina_ordine", id_ordine:id_ordine},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    okReload(data.msg);}else{ko(data.msg);}

                        });
                }
            });

            e.preventDefault();
        })
        $("#elimina_ordine").click(function(e) {
            var id_ordine=$(this).data("id_ordine");
            $.SmartMessageBox({
                title : "Elimina questo ordine",
                content : "Attenzione. Non sarà possibile recuperare i dati cancellati.",
                buttons : "[Annulla][PROSEGUI]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="PROSEGUI"){
                    $.blockUI();
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/ordini/_act.php",
                          dataType: 'json',
                          data: {act: "elimina_ordine", id_ordine:id_ordine},
                          context: document.body
                        }).done(function(data) {
                            $.unblockUI();
                            if(data.result=="OK"){
                                okGo(data.msg,'<?php echo APP_URL ?>');
                            }else{
                                ko(data.msg);
                            }
                        });
                }
            });

            e.preventDefault();
        })

        $(document).off("click",".save_referente");
        $(document).on("click",".save_referente",function(e){
            console.log(this.value);
            var id_referenza = $(this).data("id");
            var id_referente = $('#input_'+id_referenza).val();
            $.ajax({
                          type: "POST",
                          url: "ajax_rd4/ordini/_act.php",
                          dataType: 'json',
                          data: {act: "save_referente_gas", id_referenza: id_referenza, id_referente: id_referente},
                          context: document.body
                        }).done(function(data) {
                            
                            if(data.result=="OK"){
                                ok(data.msg);
                            }else{
                                ko(data.msg);
                                $('#input_'+id_referenza).val('0');
                            }
                        });
        
            
        });
        
        
        $(document).off("change",".tipo_listino");
        $(document).on("change",".tipo_listino",function(e){
            
            console.log(this.value);
            var $t = $(this).val();
            $.ajax({
                          type: "POST",
                          url: "ajax_rd4/ordini/_act.php",
                          dataType: 'json',
                          data: {act: "ordine_tipo_listino", tipo_listino: $t, id_ordine : <?php echo $id_ordine;?>},
                          context: document.body
                        }).done(function(data) {
                            
                            if(data.result=="OK"){
                                ok(data.msg);
                            }else{
                                ko(data.msg);
                            }
                        });
        });
        
        $(document).off("change",".gas_partecipa");
        $(document).on("change",".gas_partecipa",function(e){
            $.blockUI();
            var action;
            if(this.checked) {
                action = "insert";
            }else{
                action = "delete";
            }
            console.log(this.value);
            var $t = $(this);
            $.ajax({
                          type: "POST",
                          url: "ajax_rd4/ordini/_act.php",
                          dataType: 'json',
                          data: {act: "gas_partecipa", action: action, value : this.value, id_ordine : <?php echo $id_ordine;?>},
                          context: document.body
                        }).done(function(data) {
                            $.unblockUI();
                            if(data.result=="OK"){
                                if(action=="insert"){
                                    $t.closest('tr').addClass(' success ');
                                }else{
                                    $t.closest('tr').removeClass(' success ');
                                }
                                ok(data.msg);
                            }else{
                                $t.closest('tr').removeClass(' success ');
                                $t.closest('tr').addClass(' danger ');
                                ko(data.msg);
                            }
                        });
        });
        loadScript("js/plugin/clockpicker/clockpicker.min.js", runClockPicker);

        function runClockPicker(){
            $('#clockpicker_apertura').clockpicker({
                placement: 'top',
                donetext: 'Fatto'
            });
            $('#clockpicker_chiusura').clockpicker({
                placement: 'top',
                donetext: 'Fatto'
            });
        }

        //NOTE_ORDINI
        $('#go_note_ordini').click(function(e){
            var value = $('#summernote').summernote('code');
            //var value = $('#summernote').code();
            $.ajax({
              type: "POST",
              url: "ajax_rd4/ordini/_act.php",
              dataType: 'json',
              data: {name: "note_ordini", pk: <?php echo $O->id_ordini; ?>, value:value},
              context: document.body
            }).done(function(data) {
                if(data.result=="OK"){
                        ok(data.msg);}else{ko(data.msg);}
                        //location.reload();
            });
        });

    }
    // end pagefunction
     //Load time picker script



    loadScript("js/plugin/x-editable/moment.min.js", loadXEditable);

    function loadXEditable(){
        loadScript("js/plugin/x-editable/x-editable.min.js", loadSummerNote);
    }
    function loadSummerNote(){
        loadScript("js/plugin/summernote/new_summernote.min.js", loadDataTable)
    }

    function loadDataTable(){
        loadScript("js/plugin/datatables/jquery.dataTables.min.js", function(){
            loadScript("js/plugin/datatables/dataTables.colVis.min.js", function(){
                loadScript("js/plugin/datatables/dataTables.tableTools.min.js", function(){
                    loadScript("js/plugin/datatables/dataTables.bootstrap.min.js", function(){
                        loadScript("js/plugin/datatable-responsive/datatables.responsive.min.js", pagefunction)
                    });
                });
            });
        });
    }

</script>
