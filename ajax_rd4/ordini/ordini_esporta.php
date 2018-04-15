<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.ordine.php");
$ui = new SmartUI;
$converter = new Encryption;

$page_title = "Esportazione ordine";
$page_id ="ordini_esporta";

//CONTROLLI
$id_ordine = (int)$_GET["id"];
$O = new ordine($id_ordine);

if (!posso_gestire_ordine($id_ordine)){
    echo rd4_go_back("Non ho i permessi necessari");die;
}


?>
<?php echo $O->navbar_ordine();?>
<section>
    <div class="row margin-top-10">
        <div class="col col-xs-12 col-md-4">
            <div class="well well-sm">
                <h3>Seleziona i dati</h3>
                <form class="smart-form">

                    <?php
                        $colonne = array("id_ordine"            =>"ID ORDINE",
                                         "descrizione_ordini"   =>"DESCRIZIONE ORDINE",
                                         "data_inserimento"     =>"DATA",
                                         "id_utenti"            =>"ID_UTENTE",
                                         "fullname"             =>"NOME",
                                         "id_gas"               =>"ID GAS",
                                         "descrizione_gas"      =>"DESCRIZIONE GAS",
                                         "id_ditta"             =>"ID DITTA",
                                         "descrizione_ditta"    =>"DESCRIZIONE_DITTA",
                                         "art_codice"           =>"CODICE ARTICOLO",
                                         "art_desc"             =>"DESCRIZIONE ARTICOLO",
                                         "art_um"               =>"UNITA' DI VENDITA",
                                         "qta_ord"              =>"QTA ORDINATA",
                                         "qta_arr"              =>"QTA ARRIVATA",
                                         "prz_dett"             =>"PREZZO",
                                         "prz_dett_arr"         =>"PREZZO REALE",
                                         "tot_riga"             =>"TOTALE_RIGA");
                        $java="";
                        foreach($colonne as $key => $value){

                            $java.= '"'.$key.'",';
                            ?>
                            <label class="toggle">
                                <input type="checkbox" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo $key; ?>">
                                <i data-swchon-text="SI" data-swchoff-text="NO"></i><?php echo $value; ?>
                            </label>

                            <?PHP
                        }
                        $java=rtrim($java,",");
                        ?>
                </form>
                <div class="clearfix"></div>
            </div>
        </div>

        <div class=" col col-xs-12 col-md-4">
            <div class="well well-lg">
                <form class="smart-form">
                <section>
                    <label class="label">Ordina per:</label>
                    <div class="row">
                        <div class="col col-12">
                            <label class="radio">
                                <input type="radio" name="tipo_ordinamento" checked="checked"   value="id_utente" class="tipo_ordinamento">
                                <i></i>Utente</label>
                            <label class="radio">
                                <input type="radio" name="tipo_ordinamento"                     value="id_gas" class="tipo_ordinamento" >
                                <i></i>Gas</label>
                            <label class="radio">
                                <input type="radio" name="tipo_ordinamento"                     value="art_codice" class="tipo_ordinamento" >
                                <i></i>Articolo</label>
                            <label class="radio">
                                <input type="radio" name="tipo_ordinamento"                     value="data_inserimento" class="tipo_ordinamento" >
                                <i></i>Data inserimento</label>
                        </div>
                </section>
                <section>
                    <label class="label">Visualizza dati come:</label>
                    <div class="row">
                        <div class="col col-12">
                            <label class="radio">
                                <input type="radio" name="tipo_visualizza" checked="checked" value="tabella" class="tipo_visualizza">
                                <i></i>Tabella</label>
                            <label class="radio">
                                <input type="radio" name="tipo_visualizza"  value="puri" class="tipo_visualizza">
                                <i></i>Valori puri</label>
                        </div>
                </section>
                </form>


            </div>

        </div>

        <div class="col col-xs-12 col-md-4">
            <div class="well well-lg text-center">
                <button class="btn btn-block btn-default" id="copia_dati"><i class="fa fa-copy pull-left"></i>&nbsp;SELEZIONA E COPIA</button>
                <button class="btn btn-block btn-default" id="stampa_dati"><i class="fa fa-print pull-left"></i>&nbsp;STAMPA da BROWSER</button>
                <a id="esporta_csv_button" href="#" class="btn btn-block btn-default export_csv"><i class="fa fa-download pull-left"></i>&nbsp;ESPORTA CSV</a>
                <a id="esporta_xls_button" href="javascript:void(0);" class="btn btn-block btn-default export_xls"><i class="fa fa-download pull-left"></i>&nbsp;ESPORTA EXCEL</a>

            </div>
        </div>



    </div>

    <div class="row margin-top-10">
        <div class="col col-xs-12 col-md-12">
            <div class="well well-sm">
                <h3>Dati <small>L'esportazione sarà formattata in base al tipo scelto</small></h3>
                <div id="box-dati-esporta" style="overflow-x:auto;"></div>
            </div>
        </div>
    </div>

</section>


<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
        </article>
    </div>
</section>

<script type="text/javascript">

    pageSetUp();



    var values, tipo_visualizza, tipo_ordinamento;
    var pagefunction = function(){
        //------------HELP WIDGET
        <?php echo help_render_js($page_id);?>
        //------------END HELP WIDGET


        $(document).off('click','.export_xls');
          $(document).on('click','.export_xls', function(event) {
              console.log("exporting XLS...");
              var options = {

                worksheetName: 'Ordine_'+<?PHP echo $O->id_ordini ?>,
                numbers: {
                    output: {
                        decimalMark: ',',
                        thousandsSeparator: ''
                    }
                }
              };

              $.extend(true, options, {type: 'xlsx',fileName: 'Ordine_'+<?PHP echo $O->id_ordini ?>});
              $('#tabella_ordine_esportazione').tableExport(options);
          });


        function exportTableToCSV($table, filename) {

            var $rows = $table.find('tr:has(td, th)'),
              tmpColDelim = String.fromCharCode(11), // vertical tab character
              tmpRowDelim = String.fromCharCode(0), // null character
              colDelim = '"'+"<?php echo _USER_CSV_SEPARATOR; ?>"+'"',
              rowDelim = '"\r\n"',
              csv = '"' + $rows.map(function(i, row) {
                var $row = $(row),
                  $cols = $row.find('td, th');
                return $cols.map(function(j, col) {
                  var $col = $(col),
                    text = $col.text();
                  return text.replace(/"/g, '""'); // escape double quotes
                }).get().join(tmpColDelim);
              }).get().join(tmpRowDelim)
              .split(tmpRowDelim).join(rowDelim)
              .split(tmpColDelim).join(colDelim) + '"';
            if (false && window.navigator.msSaveBlob) {
              var blob = new Blob([decodeURIComponent(csv)], {
                type: 'text/csv;charset=utf8'
              });
              window.navigator.msSaveBlob(blob, filename);
            } else if (window.Blob && window.URL) {
              // HTML5 Blob
              var blob = new Blob([csv], {
                type: 'text/csv;charset=utf-8'
              });
              var csvUrl = URL.createObjectURL(blob);

              $(this)
                .attr({
                  'download': filename,
                  'href': csvUrl
                });
            } else {
              // Data URI
              var csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);

              $(this)
                .attr({
                  'download': filename,
                  'href': csvData,
                  'target': '_blank'
                });
            }
          }

          $(document).off('click','.export_csv');
          $(document).on('click','.export_csv', function(event) {
            var args = [$('#box-dati-esporta>table'), 'Ordine_'+<?php echo $O->id_ordini;?>+'.csv'];
            exportTableToCSV.apply(this, args);
          });

        var visualizzami= function(){
            var id_ordine=<?PHP echo $O->id_ordini ?>;
            values = $('input:checkbox:checked').map(function () {
              return this.value;
            }).get();
            console.log(values);

            tipo_visualizza = $('input.tipo_visualizza:radio:checked').map(function () {
              return this.value;
            }).get();
            console.log(tipo_visualizza);

            tipo_ordinamento = $('input.tipo_ordinamento:radio:checked').map(function () {
              return this.value;
            }).get();
            console.log(tipo_ordinamento);


            $.ajax({
                  type: "POST",
                  url: "ajax_rd4/ordini/_act.php",
                  dataType: 'json',
                  data: {act: "visualizza_ordine_per_esportazione", values : values, id_ordine:id_ordine, tipo_visualizza:tipo_visualizza, tipo_ordinamento:tipo_ordinamento},
                  context: document.body
                }).done(function(data) {
                    $.unblockUI();
                    if(data.result=="OK"){
                        //ok(data.msg);
                        $('#box-dati-esporta').html(data.html);
                    }else{
                        ko(data.msg);
                    }

                });


        };


        //SAVE STATE CHECKBOX IN LOCALSTORAGE
        var dati= [<?php echo $java;?>];

        var arrayLength = dati.length;
        for (var i = 0; i < arrayLength; i++) {
            (function (index) {
                console.log(dati[index]);
                var colonna = dati[index];
                if (localStorage.getItem(colonna) !== null) {
                    $("input[name='"+colonna+"']").attr("checked", "checked");
                }
                $(document).off('change',"input[name='"+colonna+"']");
                $(document).on('change',"input[name='"+colonna+"']",function(){
                    if ($(this).is(":checked")) {
                        localStorage.setItem(colonna, 1);
                    } else {
                        localStorage.removeItem(colonna);
                    }

                    visualizzami();
                });
            })(i);

       }
       $(document).off('change','input[type=radio][name=tipo_visualizza]')
       $(document).on('change',"input[type=radio][name=tipo_visualizza]",function(){

           visualizzami();
           if($(this).val()==="tabella"){
                console.log("tipo visualizza: TABELLA");
                $('#esporta_csv_button').show()
                $('#esporta_xls_button').show();
           }else{
                console.log("tipo visualizza: PURI");
                $('#esporta_csv_button').hide();
                $('#esporta_xls_button').hide();
           }
        });
       $(document).off('change','input[type=radio][name=tipo_ordinamento]')
       $(document).on('change',"input[type=radio][name=tipo_ordinamento]",function(){
           console.log("tipo ordinamento: " + $(this).val());
           visualizzami();
        });

       $(document).off("click","#copia_dati");
       $(document).on("click","#copia_dati", function(){
           selectElementContents(document.getElementById('box-dati-esporta'));
       });

       $(document).off("click","#stampa_dati");
       $(document).on("click","#stampa_dati", function(){
           printDiv('box-dati-esporta');
       });


       visualizzami();
    } // end pagefunction

   loadScript("js_rd4/plugin/tableExport/libs/FileSaver/FileSaver.min.js",
    loadScript("js_rd4/plugin/tableExport/libs/js-xlsx/xlsx.core.min.js",
        loadScript("js_rd4/plugin/tableExport/tableExport.min.js", pagefunction)
    )
    );

    pagefunction();


</script>

