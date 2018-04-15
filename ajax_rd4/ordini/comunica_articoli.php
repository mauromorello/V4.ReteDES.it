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

if (!posso_gestire_ordine_come_gas($id_ordine)){
    echo rd4_go_back("Non ho i permessi necessari");die;
}
$O=new ordine($id_ordine);
$id_listino = $O->id_listini;


$page_title = "Comunica a chi ha acquistato determinati articoli";
$page_id ="comunica_ordine_articoli";

//PASSO GLI ARTICOLI
        $stmt = $db->prepare("SELECT  D.descrizione_ditta, D.art_codice, D.art_desc, ROUND(SUM(D.qta_ord)) as qta_ord, ROUND(SUM(D.qta_arr)) as qta_arr, COUNT(D.id_utenti) as q_utenti, (SELECT CONCAT(ROUND(A.qta_scatola),'-',ROUND(A.qta_minima)) FROM retegas_articoli A WHERE A.id_listini='$id_listino' AND A.codice=D.art_codice) as qta_art, (SELECT prezzo FROM retegas_articoli A WHERE A.id_listini='$id_listino' AND A.codice=D.art_codice) as prezzo FROM retegas_dettaglio_ordini D  WHERE id_ordine=:id_ordine GROUP BY D.art_codice, D.art_desc, D.prz_dett_arr");
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $err =0;

        $t = '<table id="tabella_comunica_articoli" class="has-checkbox">
                <thead class="font-sm">
                    <th class="filter-false"></th>
                    <th>Codice</th>
                    <th>Descrizione</th>
                    <th class="filter-false">Prezzo</th>
                    <th class="filter-false">Utenti.</th>
                </thead>
                <tbody>';

        foreach ($rows as $row) {

            $misto = explode("-",$row["qta_art"]);
            $scatola = $misto[0];
            $multiplo = $misto[1];
            $scatole=0;
            $avanzo=0;


            $t .= '<tr class="'.$class.'">';
              $t .='<td data-math="ignore"><input type="checkbox" name="art_codice" class="art_selector" value="'.$row["art_codice"].'"></td>';
              $t .='<td class="text-left">'.$row["art_codice"].'<br><span class="note">'.$row["descrizione_ditta"].'</span></td>';
              $t .='<td class="text-left">'.$row["art_desc"].'</td>';
              $t .='<td class="text-right">'._NF($row["prezzo"]).'</td>';
              $t .='<td class="text-right">'.$row["q_utenti"].'</td>';

            $t .= '</tr>';
        }

        $t .= '</tbody>

              </table>
              <br>
              ';

//LIsta utenti ordine;



?>
<?php echo $O->navbar_ordine(); ?>
<p></p>
<div class="panel panel-blueLight padding-10" >
    <p class="font-xl">Comunica a chi ha acquistato determinati articoli:</p>

    <form class="smart-form">
        <div class="row">
            <section class="col col-6 ">
                <p>Leggere attentaqmente l'help in fondo alla pagina.</p>
            </section>
            <section class="col col-6">

            </section>

        </div>
    </form>


    <div class="row padding-5">
        <?php echo $t ?>
    </div>
    <h3>Manda un messaggio alle persone che hanno in ordine gli articoli selezionati</h3>
    <hr>
    <div class="row">
        <div class="col col-sm-6 padding-10">
            <div class="margin-top-5">
                <form class="smart-form">
                    <section>
                    <label class="label">Testo del messaggio</label>
                    <label class="textarea">
                        <textarea name="messaggio_testo" id="art_messaggio_testo" class="custom-scroll" rows="3"></textarea>
                    </label>
                    </section>
                </form>

                <button id="art_messaggio" class="btn btn-primary pull-right margin-top-10" data-id_ordine="<?php echo $id_ordine; ?>">INVIA</button>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="col col-sm-6 padding-10">
            <div id="lista_box"></div>
        </div>


    </div>


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


                var t = $('#tabella_comunica_articoli').tablesorter({
                    theme: 'bootstrap',
                        //debug:true,
                        widgets: ["uitheme","filter","zebra"],
                     widgetOptions : {
                         zebra : ["even", "odd"],
                         filter_reset : ".reset"
                        }
                });

            $(document).off('change','#tabella_comunica_articoli input:checkbox');
            $(document).on('change','#tabella_comunica_articoli input:checkbox',function(){
                $('#lista_box').html("");
                var id_ordine = <?php echo $O->id_ordini; ?>;
                var codici = $("#tabella_comunica_articoli input:checkbox").serializeArray();

                if (codici.length === 0) {
                    return;
                }else{
                    $.ajax({
                      type: "POST",
                      url: "ajax_rd4/rettifiche/_act.php",
                      dataType: 'json',
                      data: {act: "art_manda_messaggio", id_ordine:id_ordine, codici : codici, tipo:1},
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
        }//END STARTTABLE
    } // end pagefunction

    loadScript("js_rd4/plugin/tablesorter/js/jquery.tablesorter.min.js", pagefunction);

</script>

