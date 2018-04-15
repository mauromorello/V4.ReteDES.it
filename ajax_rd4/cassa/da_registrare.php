<?php
    require_once("inc/init.php");
    require_once("../../lib_rd4/class.rd4.user.php");
    require_once("../../lib_rd4/class.rd4.ordine.php");
    require_once("../../lib_rd4/class.rd4.cassa.php");

    $ui = new SmartUI;
    $page_title = "Movimenti da registrare";
    $page_id = "da_registrare";

    if(!(_USER_PERMISSIONS & perm::puo_gestire_la_cassa)){
        echo rd4_go_back("Non ho i permessi per la cassa");die;
    }
    $h = '<div class="table-responsive">';
    $h.= '<table id="table_da_registrare" class="table table-condensed smart-form has-tickbox">';
    $h.= '<thead>';
    $h.= '<tr>';
    $h.= '<th></th>';
    $h.= '<th>#ID</th>';
    $h.= '<th>Utente</th>';
    $h.= '<th>Ordine</th>';
    $h.= '<th>Movimento</th>';
    $h.= '<th>Importo</th>';
    $h.= '</tr>';
    $h.= '</thead>';
    $h.= '<tbody>';

    $sql = "SELECT * from retegas_cassa_utenti where registrato='no' and id_gas='"._USER_ID_GAS."' ORDER BY id_cassa_utenti DESC";

    //Modifica per far vedere i movimenti da registrare con ordini convalidati a livello GAS.
    $sql = "SELECT C.*, R.* from retegas_cassa_utenti C
                inner join retegas_referenze R on (R.id_ordine_referenze=C.id_ordine AND R.id_gas_referenze=C.id_gas)
                where
                C.registrato='no'
                and C.id_gas='"._USER_ID_GAS."'
                and R.convalida_referenze>0
                ORDER BY C.id_cassa_utenti DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll();

        $C = new cassa(_USER_ID_GAS);

    foreach($rows as $row){

        $U = new user($row["id_utente"]);
        $O = new ordine($row["id_ordine"]);

        $data_movimento = conv_datetime_from_db($row["data_movimento"]);

        $importo=$row["importo"];
        if($row["segno"]=="+"){
            $class=' class="text-success font-lg" ';

        }else{
            $class=' class="text-danger font-lg" ';
            $importo = -$importo;
        }

        //if(true){
        if($O->codice_stato=="CO"){
            $h.= '<tr>';
                $h.= '<td class="filter-false"><label class="checkbox"><input type="checkbox" class="registra" value="'.$row["id_cassa_utenti"].'"><i></i></label></td>';
                $h.= '<td>'.$row["id_cassa_utenti"].'<br><span class="note">'.$data_movimento.'</span></td>';
                $h.= '<td>#<b>'.$U->userid.'</b> '.$U->fullname.'</td>';
                $h.= '<td>#<b>'.$O->id_ordini.'</b> '.$O->descrizione_ordini.'</td>';
                $h.= '<td>'.$row["descrizione_movimento"].'</td>';
                $h.= '<td class="text-right"><span '.$class.'>'.$importo.'</span></td>';
            $h.= '</tr>';
        }

        unset($O);
        unset($U);
    }

    $h.= '</tbody>';
    $h.= '<tfoot>';
    $h.= '<tr>';
    $h.= '<th></th>';
    $h.= '<th></th>';
    $h.= '<th></th>';
    $h.= '<th></th>';
    $h.= '<th></th>';
    $h.= '<th></th>';
    $h.= '</tr>';
    $h.= '</tfoot>';
    $h.= '</table>';
    $h.= '</div>';
  //-------------------------ORDINO

?>

<h1>Movimenti da registrare : <?php echo $C->get_movimenti_da_registrare(); ?></h1>
<p class="alert alert-info"><b>NB: </b>vengono mostrati solo movimenti da registrare di ordini convalidati</p>
<hr>
<?php echo $h; ?>
<div class="well margin-top-5">
            <label class="pull-right ">
            <input class="selectall" type="checkbox"> Seleziona / deseleziona tutti
            </label>
            <p><button class="btn btn-default" id="registra_tutti_movimenti"><i class="fa fa-check-circle-o"></i>   registra i movimenti selezionati.</button></p>
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


    var pagefunction = function() {

        //-------------------------HELP
        <?php echo help_render_js($page_id); ?>
        //-------------------------HELP

        loadScript("js_rd4/plugin/tablesorter/js/jquery.tablesorter.widgets.js",startTable);
        function startTable(){

                $.extend($.tablesorter.themes.bootstrap, {
                    table      : 'table table-bordered',
                    caption    : 'caption',
                    sortNone   : 'bootstrap-icon-unsorted',
                    sortAsc    : 'fa fa-arrow-up',
                    sortDesc   : 'fa fa-arrow-down'

                  });

                t = $('#table_da_registrare').tablesorter({
                    theme: 'bootstrap',
                        //debug:true,
                        widgets: ["uitheme","filter","zebra"],
                     widgetOptions : {
                         zebra : ["even", "odd"],
                         filter_reset : ".reset"
                        }
                });



        }//END STARTTABLE

        $('.selectall').click(function(event) {  //on click
            console.log("Click select");

                if(this.checked ) { // check select status
                    $('.registra').each(function() { //loop through each checkbox
                        if($(this).is(':visible')){
                            this.checked = true;  //select all checkboxes with class "checkbox1"
                        }
                    });
                }else{
                    $('.registra').each(function() { //loop through each checkbox
                        if($(this).is(':visible')){
                            this.checked = false; //deselect all checkboxes with class "checkbox1"
                        }
                    });
                }

        });

        $('#registra_tutti_movimenti').click(function(){
            console.log("Click");
            values = $('input:checkbox:checked.registra').map(function () {
              return this.value;
            }).get();

            console.log(values);
            $.SmartMessageBox({
                title : "Registra",
                content : "Confermi? saranno registrati " + values.length + " movimenti",
                buttons : "[Esci][REGISTRA]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="REGISTRA"){

                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/cassa/_act.php",
                          dataType: 'json',
                          data: {act: "registra_tutti_movimenti", values : values},
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


        });


    }
    // end pagefunction

    loadScript("js_rd4/plugin/tablesorter/js/jquery.tablesorter.min.js", pagefunction);



</script>