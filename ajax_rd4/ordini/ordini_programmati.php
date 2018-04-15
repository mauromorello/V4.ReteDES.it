<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.ordine.php");
$ui = new SmartUI;
$converter = new Encryption;

$page_title = "Ordini programmati";
$page_id ="ordini_programmati";

        //PASSO GLI ORDINI
        $stmt = $db->prepare("SELECT retegas_ordini.id_ordini,
            retegas_ordini.descrizione_ordini,
            retegas_listini.descrizione_listini,
            retegas_listini.id_tipologie,
            retegas_ditte.descrizione_ditte,
            retegas_ordini.data_chiusura,
            retegas_gas.descrizione_gas,
            retegas_referenze.id_gas_referenze,
            retegas_referenze.id_utente_referenze,
            datediff(data_apertura, NOW()) as tra_giorni,
            datediff(data_chiusura, data_apertura) as durata,
            maaking_users.userid,
            maaking_users.fullname,
            retegas_ordini.id_utente,
            retegas_ordini.id_listini,
            retegas_ditte.id_ditte,
            retegas_ordini.data_apertura
            FROM (((((retegas_ordini INNER JOIN retegas_referenze ON retegas_ordini.id_ordini = retegas_referenze.id_ordine_referenze) LEFT JOIN maaking_users ON retegas_referenze.id_utente_referenze = maaking_users.userid) INNER JOIN retegas_listini ON retegas_ordini.id_listini = retegas_listini.id_listini) INNER JOIN retegas_ditte ON retegas_listini.id_ditte = retegas_ditte.id_ditte) INNER JOIN maaking_users AS maaking_users_1 ON retegas_ordini.id_utente = maaking_users_1.userid) INNER JOIN retegas_gas ON maaking_users_1.id_gas = retegas_gas.id_gas
            WHERE (((retegas_ordini.data_apertura)>NOW())
            AND ((retegas_referenze.id_gas_referenze)="._USER_ID_GAS."))
            ORDER BY retegas_ordini.data_apertura ASC ;");
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $t = '<table id="tabella_ordini_programmati" class="has-checkbox">
                <thead class="font-sm">
                    <!--<th class="filter-false"></th>-->
                    <th>ID</th>
                    <th>Descrizione</th>
                    <th>Referente / GAS</th>
                    <th>Apertura</th>
                    <th>Chiusura</th>

                    <th>Operazioni</th>
                </thead>
                <tbody>';
        $azz=0;
        foreach ($rows as $row) {
            $azz++;
            

            //ORDINE NASCOSTO PER IL GAS
                $show_this = true;
                $is_nascosto_icon="";

                $sqln = "SELECT valore_text FROM retegas_options WHERE
                        chiave='_ORDINE_NASCOSTO_GAS' AND id_ordine= :id_ordine AND id_gas='"._USER_ID_GAS."' LIMIT 1;";
                $stmt = $db->prepare($sqln);
                $stmt->bindParam(':id_ordine', $row["id_ordini"] , PDO::PARAM_INT);
                $stmt->execute();
                $rowna = $stmt->fetch();
                if($rowna["valore_text"]=="SI"){
                    $is_nascosto = true;
                    $is_nascosto_icon='<span class="label label-danger visibila_ordine" style="cursor:pointer" data-id_ordine="'.$row["id_ordini"].'"><i class="fa fa-eye-slash"></i></span>';
                }else{
                    $is_nascosto = false;
                    $is_nascosto_icon='<span class="label label-info nascondi_ordine" style="cursor:pointer" data-id_ordine="'.$row["id_ordini"].'"><i class="fa fa-eye"></i></span>';
     
                }
            //ORDINE NASCOSTO PER IL GAS
            
            
            
            if(posso_gestire_ordine($row["id_ordini"])){
                $posso_gestire='<a href="'.APP_URL.'/#ajax_rd4/ordini/edit.php?id='.$row["id_ordini"].'"><span class="label label-success">G</span></a>';
                $edit_descrizione_ordini=' edit_descrizione_ordini ';
                $edit_data_apertura= ' edit_data_apertura ';
                $edit_data_chiusura= ' edit_data_chiusura ';
                $posso_eliminare='<span class="label label-danger  elimina_ordine" style="cursor:pointer" data-id_ordine="'.$row["id_ordini"].'"><i class="fa fa-times"></i></span>';
                $show_this = true;
                $opt =  $is_nascosto_icon."&nbsp;".
                        $posso_gestire."&nbsp;".
                        $posso_eliminare;
                        
            }else{
                $posso_gestire='';
                $edit_descrizione_ordini='';
                $edit_data_apertura='';
                $edit_data_chiusura= '';
                $posso_eliminare='';
                if($is_nascosto){
                    $show_this = false;
                }else{
                    $show_this = true;    
                }
            }

            

            //<label class="checkbox"><input type="checkbox" class="utente" value="'.$row["userid"].'"><i></i></label>
            if($show_this){
            $t .= '<tr class="'.$class.'">';
                  $t .='<td class="text-left"><a href="'.APP_URL.'/#ajax_rd4/ordini/ordine.php?id='.$row["id_ordini"].'">'.$row["id_ordini"].'</a></td>';
                  $t .='<td class="text-left"><span class="'.$edit_descrizione_ordini.'" data-pk="'.$row["id_ordini"].'">'.$row["descrizione_ordini"].'</span><br><span class="note">'.$row["descrizione_ditte"].'</span></td>';
                  $t .='<td class="text-left">'.$row["fullname"].'</td>';
                  $t .='<td class="text-right"><span class="'.$edit_data_apertura.'" data-pk="'.$row["id_ordini"].'">'.conv_datetime_from_db($row["data_apertura"]).'</span><br><span class="note">Tra '.$row["tra_giorni"].' giorni</span></td>';
                  $t .='<td class="text-right"><span class="'.$edit_data_chiusura.'" data-pk="'.$row["id_ordini"].'">'.conv_datetime_from_db($row["data_chiusura"]).'</span><br><span class="note">Dura '.$row["durata"].' giorni</span></td>';
                  $t .='<td class="text-right">'.$opt.'</td>';
                  $t .= '</tr>';
            }
        }

        $t .= '</tbody>

              </table>
              <br>
              ';
        if($azz==0){
            $t='<div class="jumbotron">
                    <h1>Nulla...</h1>
                    <p>Il tuo gas vive alla giornata, non ha programmato manco un ordine :(</p>
                </div>';
        }
//LIsta utenti ordine;


$title_navbar='<i class="fa fa-calendar fa-2x pull-left"></i> Ordini programmati ('.$azz.')<br><small class="note">Pianifica i tuoi acquisti.</small>';

?>
<?php echo navbar($title_navbar); ?>

<div class="row padding-5">
    <?php echo $t ?>
</div>


<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
        </article>
    </div>
</section>

<script type="text/javascript">

    pageSetUp();
    var $xeditable_descrizione_ordini;
    var $xeditable_data_apertura;
    var $xeditable_data_chiusura;

    var pagefunction = function(){
        //------------HELP WIDGET
        <?php echo help_render_js($page_id);?>
        //------------END HELP WIDGET

        loadScript("js_rd4/plugin/tablesorter/js/jquery.tablesorter.widgets.js",loadMath);

        function loadMath(){
            loadScript("js_rd4/plugin/tablesorter/js/widgets/widget-math.js", loadXeditable);
        }
        function loadXeditable(){
             loadScript("js/plugin/x-editable/x-editable.min.js", startTable);
        }

        function startTable(){
                // clears memory even if nothing is in the function
                 $("#tabella_ordini_programmati")
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


                var t = $('#tabella_ordini_programmati').tablesorter({
                    theme: 'bootstrap',
                        //debug:true,
                        widgets: ["uitheme","filter","zebra"],
                     widgetOptions : {
                         zebra : ["even", "odd"],
                         filter_reset : ".reset"
                        }
                });

            $(document).off('change','#tabella_ordini_programmati:checkbox');
            $(document).on('change','#tabella_ordini_programmati input:checkbox',function(){

                var codici = $("#tabella_ordini_programmati input:checkbox").serializeArray();

                if (codici.length === 0) {
                    return;
                }else{
                    $.ajax({
                      type: "POST",
                      url: "ajax_rd4/rettifiche/_act.php",
                      dataType: 'json',
                      data: {act: "art_manda", id_ordine:id_ordine, codici : codici, tipo:1},
                      context: document.body
                    }).done(function(data) {
                        if(data.result=="OK"){
                            $('#lista_box').html(data.html);
                        }else{
                            ko(data.msg);
                        }
                    });
                }
            })

            $('#art_messaggio').click(function(e){
                var id_ordine = $(this).data("id_ordine");
                var codici = $("#tabella_comunica_articoli input:checkbox").serializeArray();
                var testo = $("#art_messaggio_testo").val();

                if (codici.length === 0) {
                    ko("Devi selezionare almeno un articolo");
                    return;
                }

                $.ajax({
                  type: "POST",
                  url: "ajax_rd4/rettifiche/_act.php",
                  dataType: 'json',
                  data: {act: "art_manda", id_ordine:id_ordine, codici : codici, testo:testo},
                  context: document.body
                }).done(function(data) {
                    if(data.result=="OK"){
                        okWait(data.msg);
                    }else{
                        ko(data.msg);
                    }
                });
            })


            $xeditable_descrizione_ordini = $('.edit_descrizione_ordini').editable({
                    url: 'ajax_rd4/ordini/_act.php',
                    type: 'text',
                    name: 'descrizione_ordini',
                    title: 'Inserisci un nuovo titolo',
                            ajaxOptions: {
                                dataType: 'json'
                            },
                            success: function(data, newValue) {
                                if(data.result=="OK") {
                                     ok(data.msg);
                                }else{
                                     return data.msg;
                                }
                            }
                });
            $xeditable_data_apertura = $('.edit_data_apertura').editable({
                    url: 'ajax_rd4/ordini/_act.php',
                    type: 'text',
                    name: 'data_apertura',
                    title: 'Inserisci una nuova data di apertura',
                            ajaxOptions: {
                                dataType: 'json'
                            },
                            success: function(data, newValue) {
                                if(data.result=="OK") {
                                     ok(data.msg);
                                }else{
                                     return data.msg;
                                }
                            }
                });
           $($xeditable_data_apertura).on('shown', function () {
                $(this).data('editable').input.$input.mask('99/99/9999 99:99');
           });
           $xeditable_data_chiusura = $('.edit_data_chiusura').editable({
                    url: 'ajax_rd4/ordini/_act.php',
                    type: 'text',
                    name: 'data_chiusura',
                    title: 'Inserisci una nuova data di chiusura',
                            ajaxOptions: {
                                dataType: 'json'
                            },
                            success: function(data, newValue) {
                                if(data.result=="OK") {
                                     ok(data.msg);
                                }else{
                                     return data.msg;
                                }
                            }
                });
           $($xeditable_data_chiusura).on('shown', function () {
                $(this).data('editable').input.$input.mask('99/99/9999 99:99');
           });

           $(".elimina_ordine").click(function(e) {
            var $th=$(this);
            var id_ordine=$(this).data("id_ordine");
            $.SmartMessageBox({
                title : "Elimina questo ordine",
                content : "Attenzione. Non sarà possibile recuperare i dati cancellati.",
                buttons : "[Annulla][PROSEGUI]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="PROSEGUI"){
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/ordini/_act.php",
                          dataType: 'json',
                          data: {act: "elimina_ordine", id_ordine:id_ordine},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    ok(data.msg);
                                    $th.closest('tr').remove();
                                    t.trigger("update")
                                      .trigger("sorton", t.get(0).config.sortList)
                                      .trigger("appendCache")
                                      .trigger("applyWidgets");
                            }else{
                                    ko(data.msg);
                            }
                        });
                }
            });

            e.preventDefault();
        })

        }//END STARTTABLE
    } // end pagefunction

    loadScript("js_rd4/plugin/tablesorter/js/jquery.tablesorter.min.js", pagefunction);

</script>

