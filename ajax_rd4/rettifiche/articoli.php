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
        $id_listino = $rowo["id_listini"];


$page_title = "Rettifiche Articoli";

//PASSO GLI ARTICOLI
        $stmt = $db->prepare("SELECT  D.art_codice, D.art_desc, ROUND(SUM(D.qta_ord)) as qta_ord, ROUND(SUM(D.qta_arr)) as qta_arr, (SELECT CONCAT(ROUND(A.qta_scatola),'-',ROUND(A.qta_minima)) FROM retegas_articoli A WHERE A.id_listini='$id_listino' AND A.codice=D.art_codice) as qta_art FROM retegas_dettaglio_ordini D  WHERE id_ordine=:id_ordine GROUP BY D.art_codice, D.art_desc");
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $err =0;

        $t = '<table id="tabella_rettifica_dettaglio" >
                <thead>
                    <th>Codice</th>
                    <th>Descrizione</th>
                    <th class="filter-false">Ord.</th>
                    <th class="filter-false tot_edit">Arr.</th>
                    <th class="filter-false">Sca / Mul</th>
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

            if(substr($row["art_codice"],0,2)=="@@"){
                $class=" warning ";
                $ignore = ' data-math="ignore" ';
            }else{
                $class=" ";
                $ignore='';
            }

            $t .= '<tr class="'.$class.'">';
              $t .='<td class="text-left"><a href="#ajax_rd4/rettifiche/dettaglio.php?id='.$id_ordine.'&codice='.$row["art_codice"].'">'.$row["art_codice"].'</a></td>';
              $t .='<td class="text-left">'.$row["art_desc"].'</td>';
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
                  <th data-math="col-sum" class="font-md text-right"></th>
                  <th data-math="col-sum" class="font-lg text-right"></th>
                  <th></th>
                  <th></th>
                  <th data-math="col-sum" class="font-md text-center"></th>
                  <th></th>
                </tr>
              </tbody>
              </table>';

//LIsta utenti ordine;



?>
<?php echo navbar_ordine($id_ordine); ?>
<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html('rettifiche_articoli',$page_title); ?>
        </article>
    </div>
</section>
<div class="panel panel-blueLight padding-10" >
    <p class="font-xl">Rettifica gli ARTICOLI arrivati:<br><span class="note">Controlla anche le scatole.</span></p>

    <form class="smart-form">
        <div class="row">
            <section class="col col-6 ">
                Se si rettificano gli articoli arrivati, verranno penalizzate le persone che per ultime li hanno ordinati. Se si vuole gestire manualmente cliccare sul nome dell'articolo, verranno visualizzati gli utenti che lo hanno prenotato.
            </section>
            <section class="col col-6">
                <label class="toggle">
                    <input type="checkbox" name="checkbox-toggle" onclick="$('.art_row_delete').toggleClass('hidden');">
                    <i data-swchon-text="SI" data-swchoff-text="NO"></i>Permetti eliminazione articoli
                </label>

                <div class="note">
                    <b>ATTENZIONE: </b> Gli articoli saranno eliminati fisicamente nell'ordine di ogni utenti che li aveva prenotati.
                </div>
            </section>

        </div>
    </form>


    <div class="row padding-5">
    <?php echo $t ?>

    </div>

</div>


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
                         math_ignore   : [0,1],
                         math_mask     : '0.##',
                         math_complete : function($cell, wo, result, value, arry) {
                            result = result;
                            return result;
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


        }//END STARTTABLE


    } // end pagefunction

    loadScript("js_rd4/plugin/tablesorter/js/jquery.tablesorter.min.js", pagefunction);

</script>
