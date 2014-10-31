<?php
require_once("inc/init.php");
$ui = new SmartUI;
$converter = new Encryption;

$id_ordine = CAST_TO_INT($_POST["id"],0);
if ($id_ordine==0){
    $id_ordine = CAST_TO_INT($_GET["id"],0);
}

if($id_ordine==0){echo "missing id"; die();}

if (!posso_gestire_ordine($id_ordine)){
        echo "Non posso farlo..."; die();
}


$stmt = $db->prepare("SELECT * from retegas_ordini WHERE id_ordini=:id_ordine;");
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->execute();
        $rowo = $stmt->fetch(PDO::FETCH_ASSOC);
        $id_listino=$rowo["id_listini"];

$page_title = "Rettifiche Dettaglio Righe";
$title_navbar='<i class="fa fa-pencil fa-2x pull-left"></i> Rettifica Totale ordine  #'.$id_ordine.'<br><small class="note">'.$rowo[descrizione_ordini].'</small>';

//PASSO GLI USERS
        $codice = CAST_TO_STRING($_GET["codice"]);
        if($codice<>""){
            $where_codice = " AND D.art_codice=:codice ";
            $titolo_codice = ", codice: <b>$codice</b>";
            $togli_filtro = '<span class="pull-right font-lg"><a href="#ajax_rd4/rettifiche/dettaglio.php?id='.$id_ordine.'" class="btn btn-circle btn-default"><i class="fa fa-refresh"></i></a></span>';
        }else{
            $where_codice ="";
            $titolo_codice ="";

        }
        $utente = CAST_TO_INT($_GET["utente"],0);
        if($utente>0){
            $where_utente = " AND D.id_utenti=:utente ";
            $titolo_utente = ", utente: <b>$utente</b>";
            $togli_filtro = '<span class="pull-right font-lg"><a href="#ajax_rd4/rettifiche/dettaglio.php?id='.$id_ordine.'" class="btn btn-circle btn-default"><i class="fa fa-refresh"></i></a></span>';

        }else{
            $where_utente ="";
            $titolo_utente ="";

        }

        $stmt = $db->prepare("SELECT D.id_dettaglio_ordini, D.prz_dett, D.prz_dett_arr, D.id_utenti, D.qta_arr, D.qta_ord, D.art_codice, D.art_desc, U.fullname, G.descrizione_gas from retegas_dettaglio_ordini D inner join maaking_users U on U.userid=D.id_utenti inner join retegas_gas G on G.id_gas = U.id_gas
                                WHERE id_ordine=:id_ordine ". $where_codice.$where_utente);
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        if($codice<>""){$stmt->bindParam(':codice', $codice, PDO::PARAM_STR);}
        if($utente>0){$stmt->bindParam(':utente', $utente, PDO::PARAM_INT);}
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $err =0;

        $t = '<table id="tabella_rettifica_dettaglio" >
                <thead>
                    <th>Utente<br>GAS</th>
                    <th>Codice<br>Descrizione</th>
                    <th class="filter-false">Ord.</th>
                    <th class="filter-false">Arr.</th>
                    <th class="filter-false">Prz.</th>
                    <th class="filter-false">Nuovo Prz</th>
                    <th class="filter-false">Totale</th>
                    <th class="filter-false"></th>
                </thead>
                <tbody>';

        foreach ($rows as $row) {

            if($row["qta_arr"]<>$row["qta_ord"]){
                $wa_q = "text-danger";
            }else{
                $wa_q = "";
            }

            if($row["prz_dett"]<>$row["prz_dett_arr"]){
                $wa_p = "text-danger";
            }else{
                $wa_p = "";
            }

            $t .= '<tr>';
                $t .= '<td data-math="ignore"><a href="#ajax_rd4/rettifiche/dettaglio.php?id='.$id_ordine.'&utente='.$row["id_utenti"].'">'.$row["fullname"].'</a><br><span class="note">'.$row["descrizione_gas"].'</span></td>';
                $t .= '<td data-math="ignore"><a href="#ajax_rd4/rettifiche/dettaglio.php?id='.$id_ordine.'&codice='.$row["art_codice"].'">'.$row["art_codice"].'</a><br><span class="note">'.$row["art_desc"].'</span></td>';
                $t .= '<td data-math="ignore" class="text-center">'.round($row["qta_ord"]).'</td>';
                $t .= '<td class="font-md text-center '.$wa_q.'"><span class="qta_edit btn-block" data-pk="'.$row["id_dettaglio_ordini"].'">'.round($row["qta_arr"]).'</span></td>';
                $t .= '<td data-math="ignore" class="text-right">'.$row["prz_dett"].'</td>';
                $t .= '<td class="font-md '.$wa_p.' text-right"><span class="prz_edit" data-pk="'.$row["id_dettaglio_ordini"].'">'.$row["prz_dett_arr"].'</span></td>';
                $t .= '<td data-math="row-product" class="text-right font-lg"></td>';
                $t .= '<td data-math="ignore" class="text-center"><a href="javascript:void(0);" class="row_delete hidden text-danger" data-id="'.$row["id_dettaglio_ordini"].'"><i class="fa fa-trash-o"></i></a></td>';
            $t .= '</tr>';
        }

        $t .= '</tbody>
                <tbody class="tablesorter-infoOnly">
                <tr>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th data-math="col-sum" class="font-lg text-center"></th>
                  <th></th>
                  <th></th>
                  <th data-math="col-sum" class="font-lg text-right"></th>
                  <th></th>
                </tr>
              </tbody>
              </table>';
?>
<?php echo navbar_ordine($id_ordine); ?>

<div class="panel panel-blueLight padding-10" >
    <?php echo $togli_filtro?>
    <p class="font-xl">Rettifica il DETTAGLIO di ogni singola RIGA<?php echo $titolo_utente.$titolo_codice?><br><span class="note">Modificando le quantità o i prezzi.</span></p>

    <form class="smart-form">
        <div class="row">
            <section class="col col-6 ">
                Se si clicca sul nome dell'utente, verranno visualizzati tutti gli articoli di quell'utente; Se si clicca su di un articolo verranno visualizzati gli utenti che lo hanno prenotato. Puoi anche ottenere lo stesso risultato usando i filtri in testa alla tabella.
            </section>
            <section class="col col-6">
                <label class="toggle">
                    <input type="checkbox" name="checkbox-toggle" onclick="$('.row_delete').toggleClass('hidden');">
                    <i data-swchon-text="SI" data-swchoff-text="NO"></i>Permetti eliminazione righe
                </label>

                <div class="note">
                    <b>ATTENZIONE: </b> Gli articoli saranno eliminati fisicamente nell'ordine di ogni utenti che li aveva prenotati.
                </div>
            </section>
        </div>
    </form>

    <div class="row padding-5 table-responsive" style="height:800px; max-height:800px; overflow-y:auto !important;">
    <?php echo $t ?>

    </div>

</div>

<div class="panel padding-10 panel-blueDark">
    <p class="font-xl">Aggiungi articoli <br><span class="note">Cosa, quanto e a chi vuoi te.</span></p>
<form action="ajax_rd4/rettifiche/_act.php" method="post" id="nuovo_articolo_form" class="smart-form">

    <div class="row">
    <section class="col col-4 col-sm-6">
            <input class="hidden" id="idArticolo" name="idArticolo" type="text" value="0">
            <label for="listaArticoli" class="label">Seleziona un articolo</label>
            <div id="listaArticoli" style="width:100%" class="" rel=""></div>

    </section>

    <section class="col col-4 col-sm-6">
            <input class="hidden" id="idUtente" name="idUtente" type="text" value="0">
            <label for="listaUtenti" class="label">Seleziona un utente..</label>
            <div id="listaUtenti" style="width:100%" class="" rel=""></div>

    </section>



    <section class="col col-4 col-sm-6">
            <label for="quantita_nuovo_articolo" class="label">Inserisci la quantità</label>
            <label class="input">
                <input id="quantita_nuovo_articolo" type="text" name="quantita_nuovo_articolo" placeholder="Quantità">
            </label>
    </section>
      </div>



    <footer>
        <input type="hidden" name="act" value="add_nuovo_articolo">
        <input type="hidden" name="id_ordine" value="<?php echo $id_ordine ?>">
        <button id="start_ordine" type="submit" name="submit" class="btn btn-success">
            <i class="fa fa-save"></i>
            &nbsp;Aggiungi questi articoli
        </button>
    </footer>

</form>
</div>

<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html('rettifiche_dettaglio_righe',$page_title); ?>
        </article>
    </div>
</section>


<script type="text/javascript">

    pageSetUp();



    var pagefunction = function(){
        //------------HELP WIDGET
        <?php echo help_render_js('rettifiche_dettaglio_righe');?>
        //------------END HELP WIDGET

        loadScript("js_rd4/plugin/tablesorter/js/jquery.tablesorter.widgets.js",loadMath);

        function loadMath(){
            loadScript("js_rd4/plugin/tablesorter/js/widgets/widget-math.js", loadXeditable);
        }
        function loadXeditable(){
             loadScript("js/plugin/x-editable/x-editable.min.js", loadValidation);
        }
        function loadValidation(){
            loadScript("js/plugin/jquery-form/jquery-form.min.js", startTable);
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
                         math_ignore   : [0,1],
                         math_mask     : '0.0000',
                         math_complete : function($cell, wo, result, value, arry) {
                            result = result;
                            return result;
                          }
                        }
                });
                $('.prz_edit').editable({
                            url: 'ajax_rd4/rettifiche/_act.php',
                            type: 'text',
                            name: 'prz_dett_arr',
                            title: 'Inserisci nuovo prezzo',
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

                $('.row_delete').click(function(){
                   $this = $(this);
                   var id_dettaglio_ordini = $this.data("id");

                   $.SmartMessageBox({
                                title : "Elimini questa riga?",
                                content : "",
                                buttons : '[OK][ANNULLA]'
                            }, function(ButtonPressed) {
                                if (ButtonPressed === "OK") {
                                    $.ajax({
                                          type: "POST",
                                          url: "ajax_rd4/rettifiche/_act.php",
                                          dataType: 'json',
                                          data: {act: "row_delete", id_dettaglio_ordini : id_dettaglio_ordini },
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
                //$('.tot_edit').editable({
                //            url: 'ajax_rd4/rettifiche/_act.php',
                //            type: 'text',
                //            name: 'qta_tot',
                //            title: 'Inserisci nuovo totale'
                //});
                $("#listaArticoli").select2({
                        placeholder: "Cerca tra gli articoli..",
                            //minimumInputLength: 3,
                            ajax: {
                            url: "ajax_rd4/rettifiche/inc/listaArticoli.php?id=<?php echo $id_listino?>",
                            dataType: 'json',
                            data: function (term, page) {
                                return {
                                    q: term
                                };
                            },
                            results: function (data, page) {
                                return { results: data };
                            }

                        },
                        formatResult: function(data){
                            return '<span>'+data.text+'</span><br><strong>'+data.descr+'</strong><br><span class="font-xs">'+data.longo+'</span>' ;
                        },
                        escapeMarkup: function(m) { return m; }
                });
                $("#listaUtenti").select2({
                        placeholder: "Cerca tra gli utenti..",
                            //minimumInputLength: 3,
                            ajax: {
                            url: "ajax_rd4/rettifiche/inc/listaUtenti.php?id=<?php echo $id_ordine?>",
                            dataType: 'json',
                            data: function (term, page) {
                                return {
                                    q: term
                                };
                            },
                            results: function (data, page) {
                                return { results: data };
                            }

                        },
                        formatResult: function(data){
                            return '<span>'+data.gas+'</span><br><strong>'+data.text+'</strong>' ;
                        },
                        escapeMarkup: function(m) { return m; }
                });
                $('#listaArticoli').on("select2-selecting",
                function(e) {
                        console.log(e.val);
                        $('#idArticolo').val(e.val);
                });
                $('#listaUtenti').on("select2-selecting",
                function(e) {
                        console.log(e.val);
                        $('#idUtente').val(e.val);
                });
                var $nuovoarticoloForm = $('#nuovo_articolo_form').validate({
                        ignore: ".select2-focusser, .select2-input",

                        // Rules for form validation
                        rules : {
                            quantita_nuovo_articolo : {
                                required : true
                            },
                            idArticolo : {
                                required : true,
                                digits: true,
                                min: 1
                            },
                            idUtente : {
                                required : true,
                                digits: true,
                                min: 1
                            }
                        },

                        // Messages for form validation
                        messages : {
                            quantita_nuovo_articolo : {
                                required : 'E\' necessario indicare una quantità'
                            },
                            idArticolo :{
                                required : 'Devi indicare un articolo!',
                                min: "E\' necessario indicare un articolo!"
                            },
                            idUtente :{
                                required : 'Devi indicare un utente!',
                                min: "E\' necessario indicare un utente!"
                            }
                        },

                        // Do not change code below
                        errorPlacement : function(error, element) {
                            error.insertAfter(element.parent());
                        },
                        submitHandler: function(form) {

                            $.SmartMessageBox({
                                title : "Sei sicuro ?",
                                content : "",
                                buttons : '[OK][ANNULLA]'
                            }, function(ButtonPressed) {
                                if (ButtonPressed === "OK") {
                                    $(form).ajaxSubmit({
                                        dataType: 'json',
                                        success: function(data, status) {
                                            //console.log(data.result + ' - ' + data.msg);
                                            if(data.result=="OK"){
                                                //ok(data.msg);
                                                $.bigBox({
                                                    title : data.title,
                                                    content : data.desc+"<br>Per visualizzarlo nella tabella ricarica la pagina!",
                                                    color : "#739E73",
                                                    //timeout: 8000,
                                                    icon : "fa fa-check",
                                                    number : ""
                                                }, function() {
                                                    closedthis();
                                                });
                                            }else{
                                                ko(data.msg);
                                            }
                                        }
                                    });

                                }
                            });

                        }
                    });

        }//END STARTTABLE


    } // end pagefunction

    loadScript("js_rd4/plugin/tablesorter/js/jquery.tablesorter.min.js", pagefunction);

</script>
