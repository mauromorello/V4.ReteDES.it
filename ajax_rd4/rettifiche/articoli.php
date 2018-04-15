<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.ordine.php");
$ui = new SmartUI;
$converter = new Encryption;

$id_ordine = CAST_TO_INT($_POST["id"],0);
if ($id_ordine==0){
    $id_ordine = CAST_TO_INT($_GET["id"],0);
}

if($id_ordine==0){echo rd4_go_back("KO!");die;}

if (!posso_gestire_ordine($id_ordine)){
    echo rd4_go_back("Non ho i permessi necessari");die;
}
$O=new ordine($id_ordine);
if($O->codice_stato=="CO"){
    echo rd4_go_back("Ordine già convalidato");
    die;
}
if(livello_gestire_ordine($id_ordine)<2){
    echo rd4_go_back("Puoi solo gestire la parte del tuo GAS.");die;
}

$stmt = $db->prepare("SELECT * from retegas_ordini WHERE id_ordini=:id_ordine;");
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->execute();
        $rowo = $stmt->fetch(PDO::FETCH_ASSOC);
        $id_listino = $rowo["id_listini"];


$page_title = "Rettifiche Articoli";

//PASSO GLI ARTICOLI
        $stmt = $db->prepare("SELECT  D.descrizione_ditta, D.art_codice, D.art_desc, ROUND(SUM(D.qta_ord)) as qta_ord, ROUND(SUM(D.qta_arr)) as qta_arr, (SELECT CONCAT(ROUND(A.qta_scatola),'-',ROUND(A.qta_minima)) FROM retegas_articoli A WHERE A.id_listini='$id_listino' AND A.codice=D.art_codice) as qta_art, (SELECT prezzo FROM retegas_articoli A WHERE A.id_listini='$id_listino' AND A.codice=D.art_codice) as prezzo FROM retegas_dettaglio_ordini D  WHERE id_ordine=:id_ordine GROUP BY D.art_codice, D.art_desc, D.prz_dett_arr");
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $err =0;

        $t = '<table id="tabella_rettifica_dettaglio" class="has-checkbox">
                <thead class="font-sm">
                    <th class="filter-false"></th>
                    <th>Codice</th>
                    <th>Descrizione</th>
                    <th class="filter-false">Prz art.</th>
                    <th class="filter-false">Prz ord.</th>
                    <th class="filter-false">Ord.</th>
                    <th class="filter-false">Arr.</th>
                    <th class="filter-false">S/M</th>
                    <th class="filter-select"></th>
                    <th class="filter-false">Scatole</th>
                    <th class="filter-false">Avanzi</th>
                    <th class="filter-false"></th>
                </thead>
                <tbody>';

        foreach ($rows as $row) {

            $misto = explode("-",$row["qta_art"]);
            $scatola = $misto[0];
            $multiplo = $misto[1];
            $scatole=0;
            $avanzo=0;



            if($scatola>0){
                $i=$row["qta_arr"];
                while($i>0){
                    $i -= $scatola;
                    $scatole ++;
                }
                if($i<0){
                    $alert='<span class="label label-danger">NO</span>';
                    $avanzo = $scatola + $i;
                }else{
                    $alert="OK";
                    $scatole++;
                }
                $scatole--;

            }
            if($avanzo>0){$avanzo = '<b>'.$avanzo.'</b>';}else{$avanzo="";}

            if((substr($row["art_codice"],0,2)=="@@") OR substr($row["art_codice"],0,2)=="##"){
                $class=" warning ";
                $ignore = ' data-math="ignore" ';
            }else{
                $class=" ";
                $ignore='';
            }

            $sql="SELECT AVG(D.prz_dett_arr) FROM retegas_dettaglio_ordini D WHERE D.id_ordine=:id_ordine AND D.art_codice=:codice;";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
            $stmt->bindParam(':codice', $row["art_codice"], PDO::PARAM_STR);
            $stmt->execute();
            $rowAVG = $stmt->fetch();
            $avg = _NF($rowAVG[0]);



            $t .= '<tr class="'.$class.'">';
              $t .='<td data-math="ignore"><form class="smart-form"><label class="checkbox"><input type="checkbox" name="art_codice" class="art_selector" value="'.$row["art_codice"].'"><i></i></label></form></td>';
              $t .='<td class="text-left"><a href="#ajax_rd4/rettifiche/dettaglio.php?id='.$id_ordine.'&codice='.$row["art_codice"].'">'.$row["art_codice"].'</a><br><span class="note">'.$row["descrizione_ditta"].'</span></td>';
              $t .='<td class="text-left">'.$row["art_desc"].'</td>';
              $t .='<td class="text-right"><span data-math="ignore" >'._NF($row["prezzo"]).'</span></td>';
              $t .='<td class="text-right"><span data-math="ignore" data-pk="'.$row["art_codice"].'" class="prz_edit btn-block">'.$avg.'</span></td>';
              $t .='<td class="text-right">'.$row["qta_ord"].'</td>';
              $t .='<td class="text-right"><span data-pk="'.$row["art_codice"].'" class="tot_edit btn-block">'.$row["qta_arr"].'</span></td>';
              $t .='<td class="text-center">'.$scatola.' / '.$multiplo.'</td>';
              $t .='<td class="text-center">'.$alert.'</td>';
              $t .='<td class="text-center">'.$scatole.'</td>';
              $t .='<td class="text-center text-danger">'.$avanzo.'</td>';
              $t .='<td data-math="ignore" class="text-center text-sm"><a class="art_row_delete hidden text-danger" data-codice="'.$row["art_codice"].'" data-id_ordine="'.$id_ordine.'" href="javascript:void(0);"><i class="fa fa-trash-o"></i></a></td>';
            $t .= '</tr>';
        }

        $t .= '</tbody>
                <tbody class="tablesorter-infoOnly">
                <tr>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th data-math="col-sum" class="font-md text-right"></th>
                  <th data-math="col-sum" class="font-lg text-right"></th>
                  <th></th>
                  <th></th>
                  <th data-math="col-sum" class="font-md text-center"></th>
                  <th></th>
                </tr>
              </tbody>
              </table>
              <br>
              ';

//LIsta utenti ordine;



?>
<?php echo $O->navbar_ordine(); ?>
<p></p>
<div class="panel panel-blueLight padding-10" >
    <p class="font-xl">Rettifica gli ARTICOLI arrivati:</p>

    <form class="smart-form">
        <div class="row">
            <section class="col col-6 ">
                <p>Leggere attentaqmente l'help in fondo alla pagina.</p>
            </section>
            <section class="col col-6">
                <label class="toggle">
                    <input type="checkbox" name="checkbox-toggle" onclick="$('.art_row_delete').toggleClass('hidden');">
                    <i data-swchon-text="SI" data-swchoff-text="NO"></i>Permetti eliminazione articoli
                </label>

                <div class="note">
                    <b>ATTENZIONE: </b> Gli articoli saranno eliminati fisicamente nell'ordine di ogni utenti che li aveva prenotati.
                </div>
                <hr>
                <label class="checkbox margin-top-10">
                    <input type="checkbox" class="selectall">
                    <i></i> Seleziona / Deseleziona tutti
                </label>
            </section>

        </div>
    </form>


    <div class="row padding-5">
        
            <?php echo $t ?>
        
    </div>
    <hr>
    <div class="padding-10">
        <h3>Se hai selezionato più righe:</h3>
        <div class="panel panel-default padding-10 margin-top-5">
            <p>Applica questo sconto:</p>
            <input class="input" id="sconto_percentuale" placeholder="Inserire sconto">
            <button id="applica_sconto_percentuale" class="btn btn-primary pull-right" data-id_ordine="<?php echo $id_ordine; ?>">ESEGUI</button>
            <div class="clearfix"></div>
        </div>
        <div class="panel panel-default padding-10 margin-top-5">
            <p>Metti le quantità a zero (non sono arrivati):</p>
            <button id="art_annulla_quantita" class="btn btn-primary pull-right" data-id_ordine="<?php echo $id_ordine; ?>">ESEGUI</button>
            <div class="clearfix"></div>
        </div>
        <div class="panel panel-default padding-10 margin-top-5">
            <p><i class="fa fa-exclamation-triangle text-danger"></i>&nbsp; Elimina fisicamente dall'ordine<br>
            <span class="note">Questa operazione è irreversibile. Verranno eliminati TUTTI gli articoli con lo stesso CODICE, anche se la loro descrizione ed il loro prezzo sono differenti.</span></p>
            <button id="art_elimina_dall_ordine" class="btn btn-primary pull-right" data-id_ordine="<?php echo $id_ordine; ?>">ESEGUI</button>
            <div class="clearfix"></div>
        </div>
        <div class="panel panel-default padding-10 margin-top-5">
            
            <p>Manda questo messaggio a chi ha nell'ordine questi articoli:</p>
            <input class="input" id="art_messaggio_testo" placeholder="Scrivi qua..." style="width:100%">
           
            <br>
            <button id="art_messaggio" class="btn btn-primary pull-right margin-top-10" data-id_ordine="<?php echo $id_ordine; ?>">INVIA</button>
            <div class="clearfix"></div>
            
        </div>
    </div>
</div>
<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html('rettifiche_articoli',$page_title); ?>
        </article>
    </div>
</section>

<script type="text/javascript">

    pageSetUp();



    var pagefunction = function(){
        //------------HELP WIDGET
        <?php echo help_render_js('rettifiche_articoli');?>
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
                 $("#tabella_rettifica_dettaglio")
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

                $.tablesorter.equations['product'] = function(arry) {
                    // multiple all array values together
                    var product = 1;
                    $.each(arry, function(i,v){
                        // oops, we shouldn't have any zero values in the array
                        //if (v !== 0) {
                            product *= v;
                        //}
                    });
                    return product;
                };
                var t = $('#tabella_rettifica_dettaglio').tablesorter({
                    theme: 'bootstrap',
                        //debug:true,
                        widgets: ["uitheme","filter","zebra","math"],
                     widgetOptions : {
                         zebra : ["even", "odd"],
                         filter_reset : ".reset",
                         math_data     : 'math', // data-math attribute
                         math_ignore   : [0,1,2],
                         math_mask     : '0.##',
                         math_complete : function($cell, wo, result, value, arry) {
                            result = result;
                            return result;
                          }
                        }
                });
                $('.prz_edit').editable({
                            url: 'ajax_rd4/rettifiche/_act.php',
                            type: 'text',
                            name: 'prz_art',
                            title: 'Inserisci nuovo prezzo',
                            ajaxOptions: {
                                dataType: 'json'
                            },
                            params: function (params) {  //params already contain `name`, `value` and `pk`
                                params.id_ordine = <?php echo $id_ordine ?>;
                                return params;
                            },
                            success: function(data, newValue) {
                                if(data.result=="OK") {
                                        setTimeout(function(){
                                            t.trigger( 'update' );
                                            ok(data.msg);
                                        }, 1000);
                                     return;
                                }else{
                                     return data.msg;
                                }
                            }
                });
                $('.tot_edit').editable({
                            url: 'ajax_rd4/rettifiche/_act.php',
                            type: 'text',
                            name: 'tot_art_arr',
                            title: 'Inserisci nuovo quantitativo',
                            ajaxOptions: {
                                dataType: 'json'
                            },
                            params: function (params) {  //params already contain `name`, `value` and `pk`
                                params.id_ordine = <?php echo $id_ordine ?>;
                                return params;
                            },
                            success: function(data, newValue) {
                                if(data.result=="OK") {
                                        setTimeout(function(){
                                            t.trigger( 'update' );
                                            ok(data.msg);
                                        }, 1000);
                                     return;
                                }else{
                                     return data.msg;
                                }
                            }
                });

                $('.art_row_delete').click(function(){
                   $this = $(this);
                   var codice = $this.data("codice");
                   var id_ordine = $this.data("id_ordine");

                   $.SmartMessageBox({
                                title : "Elimini questo articolo ?",
                                content : "Sarà eliminato dall ordine di tutti gli utenti, anche quelli di altri GAS.",
                                buttons : '[OK][ANNULLA]'
                            }, function(ButtonPressed) {
                                if (ButtonPressed === "OK") {
                                    $.ajax({
                                          type: "POST",
                                          url: "ajax_rd4/rettifiche/_act.php",
                                          dataType: 'json',
                                          data: {act: "art_delete", id_ordine:id_ordine, codice : codice },
                                          context: document.body
                                        }).done(function(data) {
                                            if(data.result=="OK"){
                                                setTimeout(function(){
                                                                $this.closest('tr').css('fast', function() {
                                                                    $(this).remove();
                                                                });
                                                                ok(data.msg);
                                                                t.trigger( 'update' );}
                                                                ,1000)

                                            }else{
                                                ko(data.msg);
                                            }

                                        });

                                }
                            });
                });

                $('.qta_edit').editable({
                    url: 'ajax_rd4/rettifiche/_act.php',
                    type: 'text',
                    name: 'qta_arr',
                    title: 'Inserisci nuova quantità',
                            ajaxOptions: {
                                dataType: 'json'
                            },
                            success: function(data, newValue) {
                                if(data.result=="OK") {
                                        setTimeout(function(){
                                            t.trigger( 'update' );
                                            ok(data.msg);
                                        }, 1000);


                                     return;

                                }else{
                                     return data.msg;
                                }
                            }
                });

                $('.art_row_add').click(function(){
                    $this = $(this);
                    id_ordine = $this.data('id_ordine');
                    codice = $this.data('codice');

                    $.SmartMessageBox({
                        title : "Aggiungi: ",
                        content : 'Nella prossima schermata',
                        buttons : '[OK]',
                        input : "select",
                        options : "[Costa Rica][United States][Autralia][Spain]"
                    }, function(ButtonPressed, Value) {
                        if (ButtonPressed === "OK") {
                            ok(Value + '  '+ id_ordine);
                        }
            });


                });




            $('#applica_sconto_percentuale').click(function(e){
                console.log("OK");
                var id_ordine = $(this).data("id_ordine");
                var codici = $("#tabella_rettifica_dettaglio input:checkbox").serializeArray();
                var sconto = $("#sconto_percentuale").val();

                if (codici.length === 0) {
                    ko("Devi selezionare almeno un articolo");
                    return;
                }

                $.ajax({
                  type: "POST",
                  url: "ajax_rd4/rettifiche/_act.php",
                  dataType: 'json',
                  data: {act: "art_varia_prezzo", id_ordine:id_ordine, codici : codici, sconto:sconto },
                  context: document.body
                }).done(function(data) {
                    if(data.result=="OK"){
                        okWait(data.msg);
                    }else{
                        ko(data.msg);
                    }
                });
            });
            $('#art_elimina_dall_ordine').click(function(e){


                var id_ordine = $(this).data("id_ordine");
                var codici = $("#tabella_rettifica_dettaglio input:checkbox").serializeArray();

                if (codici.length === 0) {

                    ko("Devi selezionare almeno un articolo");
                    return;
                }


                $.SmartMessageBox({
                title : "ELIMINI QUESTI ARTICOLI ?",
                content : "L'eliminazione è permanente ed irreversibile.",
                buttons : "[Annulla][OK]",
                //input : "text",
                //placeholder : "Scrivi qua...",
                //inputValue: ''
                }, function(ButtonPress, Value) {
                    ///QUA
                    if(ButtonPress=="OK"){
                        $.blockUI();
                        $.ajax({
                          type: "POST",
                          url: "ajax_rd4/rettifiche/_act.php",
                          dataType: 'json',
                          data: {act: "art_elimina_dall_ordine", id_ordine:id_ordine, codici : codici},
                          context: document.body
                        }).done(function(data) {
                            $.unblockUI();
                            if(data.result=="OK"){
                                okReload(data.msg);
                            }else{
                                ko(data.msg);
                            }
                        });
                    }
                    // QUA
                });

            });



            $('#art_annulla_quantita').click(function(e){


                var id_ordine = $(this).data("id_ordine");
                var codici = $("#tabella_rettifica_dettaglio input:checkbox").serializeArray();

                if (codici.length === 0) {
                    ko("Devi selezionare almeno un articolo");
                    return;
                }

                $.ajax({
                  type: "POST",
                  url: "ajax_rd4/rettifiche/_act.php",
                  dataType: 'json',
                  data: {act: "art_annulla_quantita", id_ordine:id_ordine, codici : codici},
                  context: document.body
                }).done(function(data) {
                    if(data.result=="OK"){
                        okWait(data.msg);
                    }else{
                        ko(data.msg);
                    }
                });
            })
            $('#art_messaggio').click(function(e){
                var id_ordine = $(this).data("id_ordine");
                var codici = $("#tabella_rettifica_dettaglio input:checkbox").serializeArray();
                var testo = $("#art_messaggio_testo").val();

                if (codici.length === 0) {
                    ko("Devi selezionare almeno un articolo");
                    return;
                }

                $.ajax({
                  type: "POST",
                  url: "ajax_rd4/rettifiche/_act.php",
                  dataType: 'json',
                  data: {act: "art_manda_messaggio", id_ordine:id_ordine, codici : codici, testo:testo},
                  context: document.body
                }).done(function(data) {
                    if(data.result=="OK"){
                        okWait(data.msg);
                    }else{
                        ko(data.msg);
                    }
                });
            })
            
            $('.selectall').click(function(event) {
            console.log("Click select");
            if(this.checked) { 
                $('.art_selector:visible').each(function() { 
                    this.checked = true;
                });
            }else{
                $('.art_selector:visible').each(function() { 
                    this.checked = false;
                });
            }
        });
            
        }//END STARTTABLE
    } // end pagefunction

    loadScript("js_rd4/plugin/tablesorter/js/jquery.tablesorter.min.js", pagefunction);

</script>
