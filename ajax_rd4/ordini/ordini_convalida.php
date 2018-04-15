<?php
require_once("inc/init.php");
$ui = new SmartUI;
$converter = new Encryption;

$page_title = "Convalida ordine";
$page_id = "ordini_convalida";

//CONTROLLI
$id_ordine = (int)$_GET["id"];
$O = new ordine($id_ordine);



if (!posso_gestire_ordine($id_ordine)){
    if(!posso_gestire_ordine_come_gas($id_ordine)){
        echo rd4_go_back("Non ho i permessi necessari");die;
    }
}

if($O->codice_stato=="AP"){
        echo rd4_go_back("Ordine ancora aperto;");die;
    }

$ok=false;
$rows = $O->lista_gas_potenziali_partecipanti();
foreach($rows as $row){
    if($row["id_gas"]==_USER_ID_GAS){
        $ok=true;
    }
}

if(!$ok){
    echo rd4_go_back("Questo ordine non è condiviso con il tuo GAS.");
    die();
}

//FUNCTION TABELLA ORDINE
function tabella_ordine($O,$id_gas=0){
    global $db;
    $show_fullname=true;

    $stmt = $db->prepare("SELECT valore_int FROM retegas_options WHERE id_gas=:id_gas AND chiave='_GAS_REPORT_SHOW_ID' LIMIT 1;");
    $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();
    if($row["valore_int"]>0){
        $show_id=true;
    }else{
        $show_id=false;
    }

    $stmt = $db->prepare("SELECT valore_int FROM retegas_options WHERE id_gas=:id_gas AND chiave='_GAS_REPORT_SHOW_TEL' LIMIT 1;");
    $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();
    if($row["valore_int"]>0){
        $show_tel=true;
    }else{
        $show_tel=false;
    }

    $stmt = $db->prepare("SELECT valore_int FROM retegas_options WHERE id_gas=:id_gas AND chiave='_GAS_REPORT_SHOW_TESSERA' LIMIT 1;");
    $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();
    if($row["valore_int"]>0){
        $show_tessera=true;
    }else{
        $show_tessera=false;
    }


$html= '<h3>Ordine #'.$O->id_ordini.' - '.$O->descrizione_ordini.' - <b>'.$G->descrizione_gas.'</b></h3>
        <table id="table_ordine" class="table table-condensed" style="margin-left: auto; margin-right: auto">
            <thead >
                <tr class="intestazione" >
                    <th >Utente</th>
                    <th class="filter-select">GAS</th>
                    <th >Articolo</th>
                    <th >Descrizione</th>
                    <th class="text-right"></th>
                    <th class="text-right"></th>
                    <th class="text-right"></th>
                </tr>
            </thead>
            <tbody>';
//-----------------------------------DATI

if($id_gas>0){
    $filter_gas=' AND U.id_gas=:id_gas ';
}else{
    $filter_gas=' ';
}

$sql = "SELECT U.fullname, U.tel, U.userid, U.tessera, U.id_gas, G.descrizione_gas
            FROM retegas_dettaglio_ordini D
            INNER JOIN maaking_users U ON U.userid = D.id_utenti
            INNER JOIN retegas_gas G on G.id_gas=U.id_gas
            WHERE D.id_ordine=:id_ordine
            $filter_gas
            GROUP BY U.userid
            $sort";

$stmt = $db->prepare($sql);
$stmt->bindParam(':id_ordine', $O->id_ordini, PDO::PARAM_INT);
if($id_gas>0){
    $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
}
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($rows AS $rowU){
      $html_Rett ="";
      $html_ut='';

      //ARTICOLI AND LEFT(art_codice , 2)<>'@@' AND LEFT(art_codice , 2)<>'##'
      $sql = "SELECT art_codice, art_desc, art_um, qta_ord, qta_arr, prz_dett_arr, prz_dett, id_articoli FROM retegas_dettaglio_ordini WHERE id_ordine=:id_ordine AND id_utenti=:id_utenti;";
      $stmt = $db->prepare($sql);
      $stmt->bindParam(':id_ordine', $O->id_ordini, PDO::PARAM_INT);
      $stmt->bindParam(':id_utenti', $rowU["userid"], PDO::PARAM_INT);
      $stmt->execute();
      $rowsA = $stmt->fetchAll(PDO::FETCH_ASSOC);

      $tot_utente = 0;
      $tot_utente_R = 0;
      $art_count =0;

      foreach($rowsA AS $rowA){

      $art_count ++;
      $qta_arr = $rowA["qta_arr"];
      $prz_dett_arr = $rowA["prz_dett_arr"];
      $tot_riga = $rowA["prz_dett_arr"]*$rowA["qta_arr"];

      $tot_utente = $tot_utente + $tot_riga;

      if($rowA["qta_arr"]<>$rowA["qta_ord"]){
          $q_modificata = 'style="border-left:5px solid red;"';
          $q_modificata_text_1 ='<br><span class="note">('.rd4_nf($rowA["qta_ord"],$decimals).')</span>';
          if($rowA["qta_arr"]==0){
              $q_modificata_text_2='ANNULLATA';
          }else{
              $q_modificata_text_2='MODIFICATA';
          }
      }else{
          $q_modificata = '';
          $q_modificata_text_1 ='';
          $q_modificata_text_2 ='';
      }



      $html_A ='<tr>';
        $html_A .='<td>'.$rowU["fullname"].'</td>';
        $html_A .='<td>'.$rowU["descrizione_gas"].'</td>';
        $html_A .='<td>'.$rowA["art_codice"].'</td>';
        $html_A .='<td>'.$rowA["art_desc"].' <span class="note">'.$rowA["art_um"].'</span></td>';
        $html_A .='<td class="text-center" '.$q_modificata.'>'._NF($qta_arr).$q_modificata_text_1.'</td>';
        $html_A .='<td class="text-right">'._NF($prz_dett_arr).'</td>';
        $html_A .='<td class="text-right">'._NF($tot_riga).'</td>';

      $html_A .='</tr>';
      $html_ut .= $html_A;

      }//LOOP ARTICOLO

      $html .= $html_ut;


}//UTENTE



//-----------------------------------DATI
$html.='</tbody>
                <tbody class="tablesorter-infoOnly">
                <tr>
                  <th></th>
                  <th colspan=5>Totali:</th>
                  <th data-math="col-sum" class="font-lg text-right"></th>
                </tr>
              </tbody>


         </table>';



    return $html;
}


//FUNCTION TABELLA ORDINE


//ORDINE DEL MIO GAS

if($O->id_gas_referente==_USER_ID_GAS){


    // POSSO CONVALIDARE ?
    if($O->codice_stato=="CH"){
        // SE POSSO CONVALIDARE
        // DESCRIZIONE
        // PULSANTE CONVALIDA TOTALE + GAS
        // TABELLA ORDINE COMPLETO
        $msg ='Ordine CHIUSO, del mio gas. Posso convalidare.';
        $tabella = tabella_ordine($O,0);
        $caso=1;
    }else{
        if($O->codice_stato=="CO" AND $O->convalidato_gas==1){
            //ORDINE CONVALIDATO
            //POSSO STORNARE LA CONVALIDA
            //LA TOLGO A TUTTI I GAS
            $msg ='Ordine del mio gas convalidato. Posso stornare la convalida.';
            $tabella = tabella_ordine($O,0);
            $caso=2;
        }else{
            //MESSAGGIO
            //ORDINE NON CONVALIDABILE
            //UPDATE `retegas_referenze` R
            //INNER JOIN retegas_ordini O on O.id_ordini=R.id_ordine_referenze
            //SET R.convalida_referenze=1
            //WHERE O.is_printable=1
            //AND R.convalida_referenze=0
            //AND R.id_utente_referenze>0
            //AND (SELECT id_gas FROM maaking_users WHERE userid=O.id_utente)=R.id_gas_referenze
            //AND id_gas_referenze=1
            //$tabella = tabella_ordine($O,0);
            $msg ='Ordine non convalidabile.';
            $caso=3;
        }

    }
}else{
    //ORDINE DI UN ALTRO GAS
    // GAS PROPRIETARIO HA CONVALIDATO
    if($O->codice_stato=="CO"){
        if($O->convalidato_gas==0){
            //ORDINE CONVALIDATO GENERALE MA NON GAS
            //DESCRIZIONE
            //PULSANTE CONVALIDA SOLO GAS
            //TABELLA ORDINE SOLO MIO GAS
            $msg ='Ordine di un altro GAS già convalidato, posso convalidare per il mio GAS';
            $tabella = tabella_ordine($O,_USER_ID_GAS);
            $caso=4;
        }else{
            //ORDINE DI UN ALTRO GAS GIA' CONVALIDATO
            //POSSO TOGLIERE CONVALIDA GAS
            $msg ='Ordine di un altro GAS convalidato, già convalidato anche per il mio GAS';
            $tabella = tabella_ordine($O,_USER_ID_GAS);
            $caso=5;
        }


    }else{
        //GAS PROPRIETARIO NON HA CONVALIDATO
        //MESSAGGIO DI SOLLECITO AL REFERENTE GAS PROPRIETARIO
        $msg ='Ordine di un altro GAS, non convalidato.';
        $tabella = '';
        $caso=6;
    }
}



?>

<?php echo $O->navbar_ordine();?>

<article>
    <section>
        <div class="jumbotron">

            <?php if($caso==1){ ?>
            <p class="font-lg">
                L'ordine appartiene al tuo gas, ed è chiuso. Se tutti gli importi sono corretti puoi convalidarlo. I cassieri del tuo gas potranno scalare i rispettivi importi agli utenti, e verrà mandata una mail ai referenti degli altri gas per avvisarli della avvenuta convalida.
            </p>
            <button class="btn btn-success btn-block" id="convalida_ordine" data-id_ordine="<?php echo $O->id_ordini; ?>"><i class="fa fa-check"></i> CONVALIDA</button>
            <p>Nella tabella sottostante trovi tutti gli articoli ordinati da tutti gli utenti.</p>
            <?php }?>

            <?php if($caso==2){ ?>
            <p class="font-lg">
                Questo ordine è del tuo gas, ed è già stato convalidato. Stornando la convalida GENERALE l'effetto si propagherà anche agli altri gas partecipanti, e sarà tolta anche a loro.<br>
                Una mail avviserà tutti i referenti ordine degli altri gas.
            </p>
            <button class="btn btn-danger btn-block" id="ripristina_ordine" data-id_ordine="<?php echo $O->id_ordini; ?>"><i class="fa fa-undo"></i> STORNA CONVALIDA GENERALE + GAS</button>
            <hr>
            <p class="font-lg">
                Volendo, se devi solo modificare le spese aggiuntive o i costi ESCLUSIVAMENTE del tuo GAS, puoi stornare la convalida GAS. In questo modo non disturberai i referenti dei gas che hanno partecipato.
                <br><strong>NB:</strong> stornando solo per il tuo GAS non potrai modificare i quantitativi o i prezzi degli articoli.
            </p>
            <button class="btn btn-danger btn-block" id="ripristina_ordine_gas" data-id_ordine="<?php echo $O->id_ordini; ?>"><i class="fa fa-undo"></i> STORNA CONVALIDA GAS</button>


            <?php }?>

            <?php if($caso==3){ ?>
            <p class="font-lg">
                Questo ordine è convalidato GENERALE, ma non per il tuo GAS. Significa che se hai bisogno puoi aggiungere o togliere degli importi riferiti esclusivamente al tuo GAS. Quando hai fatto ricordati di convalidarlo da questa pagina.
            </p>
            <button class="btn btn-success btn-block" id="convalida_ordine_gas" data-id_ordine="<?php echo $O->id_ordini; ?>"><i class="fa fa-check"></i> CONVALIDA</button>

            <?php }?>

            <?php if($caso==4){ ?>
            <p class="font-lg">
                L'ordine appartiene a <strong><?php echo $O->descrizione_gas_referente; ?></strong>, il quale lo ha già convalidato. Se vuoi puoi procedere alla convalida per il tuo GAS.
            </p>
            <button class="btn btn-success btn-block" id="convalida_ordine_gas" data-id_ordine="<?php echo $O->id_ordini; ?>"><i class="fa fa-check"></i> CONVALIDA</button>
            <br>
            <p>Nella tabella sottostante trovi tutti gli articoli ordinati dagli utenti del tuo GAS.</p>
            <?php }?>

            <?php if($caso==5){ ?>
            <p class="font-lg">
                L'ordine è gestito dal <strong><?php echo $O->descrizione_gas_referente; ?></strong>, che lo ha già convalidato. Risulta già convalidato anche per il tuo GAS.
                Se vuoi ritoccare i totali del TUO GAS puoi stornare la convalida; in questo caso verrà
                inviata una mail ai cassieri del tuo gas per avvisarli che le cifre che hanno scaricato dai conti degli utenti potrebbero non essere corrette.
            </p>
            <button id="ripristina_ordine_gas" data-id_ordine="<?php echo $O->id_ordini; ?>" class="btn btn-danger btn-block"><i class="fa fa-undo"></i> STORNA CONVALIDA</button>
            <?php }?>

            <?php if($caso==6){ ?>
            <p class="font-lg">
                L'ordine è gestito dal <strong><?php echo $O->descrizione_gas_referente; ?></strong>, che non lo ha ancora convalidato. Se lo ritieni necessario puoi mandare una mail ai referenti ordine abilitati, per sollecitarli.
                <form class="smart-form">
                    <fieldset>
                        <section>
                            <label class="label">Messaggio</label>
                            <label class="textarea">
                                <textarea rows="3" class="custom-scroll" id="usermessage">Chiedo gentilmente ai referenti dell'ordine #<?php $O->id_ordini?> - <?php echo $O->descrizione_ordini?> di procedere alla sua convalida, in modo che possano essere validati anche per il gas <?php echo _USER_GAS_NOME?> gli importi da scalare agli utenti.
Cordiali saluti, <?php echo _USER_FULLNAME?>
                                </textarea>
                            </label>
                            <div class="note">
                                <strong>Nota:</strong> questa mail sarà inviata a tutti referenti con permessi operativi.
                            </div>
                        </section>
                    </fieldset>
                    <footer>
                        <button type="submit" class="btn btn-primary" id="usermessage_go">
                            INVIA
                        </button>
                    </footer>
                </form>
            </p>
            <?php }?>

        </div>
    </section>
    <section>
        <div class="table-responsive">
            <?php echo $tabella; ?>
        </div>
    </section>
</article>


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



    var pagefunction = function(){
        //-------------------------HELP
        <?php echo help_render_js($page_id); ?>
        //-------------------------HELP

        loadScript("js_rd4/plugin/tablesorter/js/jquery.tablesorter.widgets.js",loadMath);
        function loadMath(){
            loadScript("js_rd4/plugin/tablesorter/js/widgets/widget-math.js", startTable);
        }

        function startTable(){

                $.extend($.tablesorter.themes.bootstrap, {
                    table      : 'table table-bordered',
                    caption    : 'caption',
                    sortNone   : 'bootstrap-icon-unsorted',
                    sortAsc    : 'fa fa-arrow-up',
                    sortDesc   : 'fa fa-arrow-down'

                  });

                t = $('#table_ordine').tablesorter({
                    theme: 'bootstrap',
                        //debug:true,
                        widgets: ["uitheme","filter","zebra","math"],
                     widgetOptions : {
                         zebra : ["even", "odd"],
                         filter_reset : ".reset",
                         math_data     : 'math', // data-math attribute
                         math_ignore   : [0,1],
                         math_mask     : '0<?echo _USER_CARATTERE_DECIMALE ?>00',
                         math_complete : function($cell, wo, result, value, arry) {

                            return (value / 100);
                          }
                        }
                });



        }//END STARTTABLE

        <?php if($caso==1){ ?>
            console.log("caso 1");
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


        <?php }?>
        <?php if($caso==2){ ?>
            console.log("caso 2");
            $(document).off("click","#ripristina_ordine");
            $(document).on("click","#ripristina_ordine", function(e){
                var id_ordine=$(this).data("id_ordine");
                $.SmartMessageBox({
                    title : "Storna la convalida",
                    content : "Verranno tolte le convalide anche a tutti i gas partecipanti.",
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
            });

            $(document).off("click","#ripristina_ordine_gas");
            $(document).on("click","#ripristina_ordine_gas", function(e){
            //$("#ripristina_ordine_gas").click(function(e) {
            var id_ordine=$('#ripristina_ordine_gas').data("id_ordine");
            $.SmartMessageBox({
                title : "Storna la convalida",
                content : "Il ripristino dell\'ordine a livello GAS serve a correggere eventuali errori.",
                buttons : "[Annulla][OK]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="OK"){
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/ordini/_act.php",
                          dataType: 'json',
                          data: {act: "ripristina_ordine_gas", id_ordine:id_ordine},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    okReload(data.msg);}else{ko(data.msg);}

                        });
                }
            });

            e.preventDefault();
        });

        <?php }?>
        <?php if($caso==3){ ?>
            console.log("caso 3");
            $("#convalida_ordine_gas").click(function(e) {
                var id_ordine=$('#convalida_ordine_gas').data("id_ordine");
                $.SmartMessageBox({
                    title : "Convalida questo ordine per il tuo GAS",
                    content : "La convalida dell\'ordine per il tuo gas serve ad avvertire gli utenti del tuo GAS ed il cassiere che le cifre dell\'ordine sono corrette.",
                    buttons : "[Annulla][OK]"
                }, function(ButtonPress, Value) {

                    if(ButtonPress=="OK"){
                        $.ajax({
                              type: "POST",
                              url: "ajax_rd4/ordini/_act.php",
                              dataType: 'json',
                              data: {act: "convalida_ordine_gas", id_ordine:id_ordine},
                              context: document.body
                            }).done(function(data) {
                                if(data.result=="OK"){
                                    okReload(data.msg);
                                }else{
                                    ko(data.msg);
                                }

                            });
                    }
                });

                e.preventDefault();
            });

        <?php }?>
        <?php if($caso==4){ ?>
            console.log("caso 4");
            $("#convalida_ordine_gas").click(function(e) {
                var id_ordine=$('#convalida_ordine_gas').data("id_ordine");
                $.SmartMessageBox({
                    title : "Convalida questo ordine per il tuo GAS",
                    content : "La convalida dell\'ordine per il tuo gas serve ad avvertire gli utenti del tuo GAS ed il cassiere che le cifre dell\'ordine sono corrette.",
                    buttons : "[Annulla][OK]"
                }, function(ButtonPress, Value) {

                    if(ButtonPress=="OK"){
                        $.ajax({
                              type: "POST",
                              url: "ajax_rd4/ordini/_act.php",
                              dataType: 'json',
                              data: {act: "convalida_ordine_gas", id_ordine:id_ordine},
                              context: document.body
                            }).done(function(data) {
                                if(data.result=="OK"){
                                    okReload(data.msg);
                                }else{
                                    ko(data.msg);
                                }

                            });
                    }
                });

                e.preventDefault();
            });

        <?php }?>
        <?php if($caso==5){ ?>
            console.log("caso 5");
            $(document).off("click","#ripristina_ordine_gas");
            $(document).on("click","#ripristina_ordine_gas", function(e){
            //$("#ripristina_ordine_gas").click(function(e) {
            var id_ordine=$('#ripristina_ordine_gas').data("id_ordine");
            $.SmartMessageBox({
                title : "Storna la convalida",
                content : "Il ripristino dell\'ordine a livello GAS serve a correggere eventuali errori.",
                buttons : "[Annulla][OK]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="OK"){
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/ordini/_act.php",
                          dataType: 'json',
                          data: {act: "ripristina_ordine_gas", id_ordine:id_ordine},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    okReload(data.msg);}else{ko(data.msg);}

                        });
                }
            });

            e.preventDefault();
        });



        <?php }?>
        <?php if($caso==6){ ?>
            console.log("caso 6");
            var messaggio;

            messaggio= $('#usermessage').val();

            $(document).on( 'change', '#usermessage', function() {
                messaggio = $(this).val();
                console.log("messaggio = " + messaggio);

            });

            //$('#usermessage_go').click(function(e){
            $(document).off("#usermessage_go");
            $(document).on('click', '#usermessage_go', function (e) {
                //invio il messaggio ciccio
                e.preventDefault();
                $('#usermessage_go').prop('disabled', true);

                $.ajax({
                  type: "POST",
                  url: "ajax_rd4/ordini/_act.php",
                  dataType: 'json',
                  data: {act: "do_richiesta_info", messaggio : messaggio, id_ordine : <?php echo $O->id_ordini; ?>},
                  context: document.body
                }).done(function(data) {
                    if(data.result=="OK"){
                        ok(data.msg);

                    }else{
                        ko(data.msg);
                    }
                });

            });

        <?php }?>

    } // end pagefunction



    loadScript("js_rd4/plugin/tablesorter/js/jquery.tablesorter.min.js", pagefunction);
</script>
