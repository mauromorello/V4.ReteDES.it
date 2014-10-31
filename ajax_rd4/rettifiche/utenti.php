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


$page_title = "Rettifiche Totali Utenti";
$title_navbar='<i class="fa fa-pencil fa-2x pull-left"></i> Rettifica Totale ordine  #'.$id_ordine.'<br><small class="note">'.$rowo[descrizione_ordini].'</small>';

//PASSO GLI USERS
        $stmt = $db->prepare("SELECT DISTINCT D.id_utenti, U.fullname, G.descrizione_gas from retegas_dettaglio_ordini D inner join maaking_users U on U.userid=D.id_utenti inner join retegas_gas G on G.id_gas = U.id_gas WHERE id_ordine=:id_ordine");
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $err =0;

        $t = '<table id="tabella_rettifica_utenti" >
                <thead>
                    <th class="filter-select">GAS</th>
                    <th>Utente</th>
                    <th>Totale attuale</th>
                    <th class="filter-false">Nuovo totale</th>
                </thead>
                <tbody>';

        foreach ($rows as $row) {
            $t .= '<tr>';
            $t .= '<td>'.$row["descrizione_gas"].'</td>';
            $t .= '<td>'.$row["fullname"].'</td>';
            $t .= '<td class="text-right font-lg"><span class="totale_attuale" data-id_utente="'.$row["id_utenti"].'">'.number_format(VA_ORDINE_USER($id_ordine,$row["id_utenti"]),4).'</span></td>';
            //$t .= '<td class="text-right font-lg">'.number_format(VA_ORDINE_USER($id_ordine,$row["id_utenti"]),4).'</td>';
            $t .= '<td><input class="utenti_values input-bg" data-id_utente="'.$row["id_utenti"].'" type="text" value="" style="width:100px;"> <button data-id_utente="'.$row["id_utenti"].'" class="save_nuovo_valore btn btn-circle btn-success pull-right btn-xs"><i class="fa fa-check"></i></button></td>';
            $t .= '</tr>';
        }

        $t .= '</tbody>
                <tbody class="tablesorter-infoOnly">
                <tr>
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
    <p class="font-xl">Rettifica il TOTALE di ogni singolo UTENTE:</p>

    <form class="smart-form">
        <div class="row">
            <section class="col col-3 ">
                <label class="label">Scegli il tipo di movimento</label>
                <label class="select">
                    <select id="select_tipo_movimento">
                        <option value="1">Rettifica</option>
                        <option value="2">Trasporto</option>
                        <option value="3">Gestione</option>
                        <option value="4">Progetto</option>
                        <option value="5">Rimborso</option>
                        <option value="6">Maggiorazione</option>
                        <option value="7">Sconto</option>
                        <option value="8">Abbuono</option>
                    </select> <i></i> </label>
                <div class="note">
                    Puoi indicare un tipo di rettifica a tua scelta.
                </div>
            </section>
            <section class="col col-9">
                <label class="label">Descrizione movimento</label>
                <label class="input">
                    <input type="text" maxlength="50" id="descrizione_movimento">
                </label>
                <div class="note">
                    <strong>Opzionale:</strong> (se lasciato vuoto verrà indicato automaticamente)
                </div>
            </section>
        </div>
    </form>

    <div class="row padding-5">
    <?php echo $t ?>

    </div>
</div>
<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html('rettifiche_totale_utenti',$page_title); ?>
        </article>
    </div>
</section>



<script type="text/javascript">

    pageSetUp();



    var pagefunction = function(){
        //------------HELP WIDGET
        <?php echo help_render_js('rettifiche_totale_utenti');?>
        //------------END HELP WIDGET

        loadScript("js_rd4/plugin/tablesorter/js/jquery.tablesorter.widgets.js",loadMath);
        function loadMath(){
            loadScript("js_rd4/plugin/tablesorter/js/widgets/widget-math.js", startTable);
        }


        $('.save_nuovo_valore').click(function(){
            id_utente = $(this).data("id_utente");
            console.log("utente : " + id_utente);
            nuovo_valore = $('.utenti_values[data-id_utente='+id_utente+']').val();
            console.log("Valore : " + nuovo_valore);
            tipo_movimento=$('#select_tipo_movimento').val();
            console.log("Movimento : " + tipo_movimento);
            descrizione_movimento=$('#descrizione_movimento').val();
            console.log("Descrizione : " + descrizione_movimento);

            $.ajax({
                          type: "POST",
                          url: "ajax_rd4/rettifiche/_act.php",
                          dataType: 'json',
                          data: {act: "rettifica_totali_utente",
                                 id_ordine : <?php echo $id_ordine ?>,
                                 id_utente : id_utente,
                                 nuovo_valore : nuovo_valore,
                                 tipo_movimento : tipo_movimento,
                                 descrizione_rettifica : descrizione_movimento},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                ok(data.msg);
                                $('.totale_attuale[data-id_utente='+id_utente+']').html('<span class="text-success">'+data.nuovo_totale+'</span>');
                                $('#tabella_rettifica_utenti').trigger('update');
                            }else{
                                ko(data.msg);}
                        });

        })

        function startTable(){
                // clears memory even if nothing is in the function
                $.extend($.tablesorter.themes.bootstrap, {
                    // these classes are added to the table. To see other table classes available,
                    // look here: http://twitter.github.com/bootstrap/base-css.html#tables
                    table      : 'table table-bordered',
                    caption    : 'caption',
                    header     : '', // give the header a gradient background
                    footerRow  : '',
                    footerCells: '',
                    icons      : '', // add "icon-white" to make them white; this icon class is added to the <i> in the header
                    sortNone   : 'bootstrap-icon-unsorted',
                    sortAsc    : 'fa fa-arrow-up',     // includes classes for Bootstrap v2 & v3
                    sortDesc   : 'fa fa-arrow-down', // includes classes for Bootstrap v2 & v3
                    active     : '', // applied when column is sorted
                    hover      : '', // use custom css here - bootstrap class may not override it
                    filterRow  : '', // filter row class
                    even       : '', // odd row zebra striping
                    odd        : ''  // even row zebra striping
                  });


                $('#tabella_rettifica_utenti').tablesorter({
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

                            return result;
                          }
                        }
                });
        }


    } // end pagefunction

    loadScript("js_rd4/plugin/tablesorter/js/jquery.tablesorter.min.js", pagefunction);

</script>
