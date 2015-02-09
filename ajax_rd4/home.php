<?php
require_once("inc/init.php");
require_once("../lib_rd4/class.rd4.ordine.php");
$ui = new SmartUI;

$page_title = "Cruscotto";

$h=file_get_contents("help/home.html");
$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>false,
                    "deletebutton"=>false,
                    "colorbutton"=>false);
$wg_help = $ui->create_widget($options);
$wg_help->id = "wg_help_home";
$wg_help->body = array("content" => $h,"class" => "");
$wg_help->header = array(
    "title" => '<h2>Aiuto</h2>',
    "icon" => 'fa fa-question-circle'
);


//-------SALDO
$stmt = $db->prepare("SELECT  (
                    COALESCE((SELECT SUM(importo) FROM retegas_cassa_utenti WHERE id_utente='"._USER_ID."' AND segno='+'),0)
                    -
                    COALESCE((SELECT SUM(importo) FROM retegas_cassa_utenti WHERE id_utente='"._USER_ID."' AND segno='-'),0)
                    )  As risultato");
$stmt->execute();
$row = $stmt->fetch();
$saldo =  (float)round($row["risultato"],2);

$stmt = $db->prepare("SELECT  (
            COALESCE((SELECT SUM(importo) FROM retegas_cassa_utenti WHERE id_utente='"._USER_ID."' AND segno='+' AND registrato='no'),0)
            -
            COALESCE((SELECT SUM(importo) FROM retegas_cassa_utenti WHERE id_utente='"._USER_ID."' AND segno='-' AND registrato='no'),0)
            )  As risultato");
$stmt->execute();
$row = $stmt->fetch();
$saldo_non_conf =  abs((float)round($row["risultato"],2));

if(_GAS_CASSA_VISUALIZZAZIONE_SALDO){
    $saldo +=  $saldo_non_conf;
}





$cassa='<h1>Hai <b><span class="txt-color-blue font-xl">'.$saldo.' €</span></b> disponibili</h1>
        <h3>ma <b><span class="txt-color-red font-lg">'.$saldo_non_conf.' €</span></b> non ancora contabilizzati
        <h6>Per visualizzare i movimenti della tua cassa e prenotare una ricarica clicca <a href="#ajax_rd4/user/miacassa.php">qua</a></h6>';

$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>false,
                    "deletebutton"=>false,
                    "colorbutton"=>true);
$wg_cassa = $ui->create_widget($options);
$wg_cassa->id = "wg_cassa_home";
$wg_cassa->body = array("content" => $cassa,"class" => "");
$wg_cassa->header = array(
    "title" => '<h2>Cassa</h2>',
    "icon" => 'fa fa-euro'
);

$stmt = $db->prepare("SELECT retegas_ordini.id_ordini,
            retegas_ordini.descrizione_ordini,
            retegas_listini.descrizione_listini,
            retegas_listini.id_tipologie,
            retegas_ditte.descrizione_ditte,
            retegas_ordini.data_chiusura,
            retegas_gas.descrizione_gas,
            retegas_referenze.id_gas_referenze,
            retegas_referenze.id_utente_referenze,
            maaking_users.userid,
            maaking_users.fullname,
            retegas_ordini.id_utente,
            retegas_ordini.id_listini,
            retegas_ditte.id_ditte,
            retegas_ordini.data_apertura
            FROM (((((retegas_ordini INNER JOIN retegas_referenze ON retegas_ordini.id_ordini = retegas_referenze.id_ordine_referenze) LEFT JOIN maaking_users ON retegas_referenze.id_utente_referenze = maaking_users.userid) INNER JOIN retegas_listini ON retegas_ordini.id_listini = retegas_listini.id_listini) INNER JOIN retegas_ditte ON retegas_listini.id_ditte = retegas_ditte.id_ditte) INNER JOIN maaking_users AS maaking_users_1 ON retegas_ordini.id_utente = maaking_users_1.userid) INNER JOIN retegas_gas ON maaking_users_1.id_gas = retegas_gas.id_gas
            WHERE (((retegas_ordini.data_chiusura)>NOW())
            AND ((retegas_ordini.data_apertura)<NOW())
            AND ((retegas_referenze.id_gas_referenze)="._USER_ID_GAS."))
            ORDER BY retegas_ordini.data_chiusura ASC ;");
$stmt->execute();
$rows = $stmt->fetchAll();

$no=0;
foreach($rows as $row){
$no++;
//TEMPO ALLA CHIUSURA
        $inittime=time();
        $datexmas=strtotime($row["data_chiusura"]);
        $timediff = $datexmas - $inittime;

        $days=intval($timediff/86400);
        $remaining=$timediff%86400;
        if($days>0){$dd="<b>$days</b> gg. e ";}else{$dd="";}


        $hours=intval($remaining/3600);
        $remaining=$remaining%3600;

        $mins=intval($remaining/60);
        $secs=$remaining%60;

        if ($days<2){
            $color = "<span class=\"label label-danger\">SCADE</span>";
        }else{
            $color = "";
        }

        if($row["id_utente_referenze"]<1){
            $gestore='MANCA il referente per il tuo GAS';
            $colge =' text-danger ';
        }else{
            $gestore= $row["fullname"].', '.$row["descrizione_gas"];
            $colge ='';
        }

$oa .=' <li>
        <span class="">
            <a href="#ajax_rd4/ordini/ordine.php?id='.$row["id_ordini"].'" class="msg">
                <img src="img_rd4/t_'.$row["id_tipologie"].'_240.png" alt="" class="air air-top-left margin-top-5" width="40" height="40">
                <span class="from">'.$row["descrizione_ordini"].' <i class="icon-paperclip">'.$color.'</i></span>
                <time>'.$dd .'<b>'.$hours.'</b> h.</time>
                <span class=" '.$colge.'">'.$gestore.'</span>
                <span class="msg-body">'.$row["descrizione_ditte"].', '.$row["descrizione_listini"].'</span>
            </a>
            <span class="note pull-left margin-top-10">Non hai ancora partecipato.</span>
        </span>
        </li>';
}



$oa ='<ul class="notification-body">'.$oa.'</ul>';

$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>false,
                    "deletebutton"=>false,
                    "colorbutton"=>true);
$wg_oa = $ui->create_widget($options);
$wg_oa->id = "wg_ordini_aperti_home";
$wg_oa->body = array("content" => $oa,"class" => "no-padding");
$wg_oa->header = array(
    "title" => '<h2>Ci sono <b>'.$no.'</b> ordini aperti</h2>',
    "icon" => 'fa fa-shopping-cart'
);

//ORDINI IO COINVOLTO
/*$stmt = $db->prepare("SELECT retegas_ordini.id_ordini,
            retegas_ordini.descrizione_ordini,
            retegas_listini.descrizione_listini,
            retegas_ditte.descrizione_ditte,
            retegas_ordini.data_chiusura,
            retegas_gas.descrizione_gas,
            retegas_gas.id_gas,
            retegas_referenze.id_gas_referenze,
            maaking_users.userid,
            maaking_users.fullname,
            maaking_users.id_gas,
            retegas_ordini.id_utente,
            retegas_ordini.id_listini,
            retegas_ditte.id_ditte,
            retegas_ordini.data_apertura,
            retegas_ordini.is_printable
            FROM (((((retegas_ordini
            INNER JOIN retegas_referenze ON retegas_ordini.id_ordini = retegas_referenze.id_ordine_referenze)
            LEFT JOIN maaking_users ON retegas_referenze.id_utente_referenze = maaking_users.userid)
            INNER JOIN retegas_listini ON retegas_ordini.id_listini = retegas_listini.id_listini)
            INNER JOIN retegas_ditte ON retegas_listini.id_ditte = retegas_ditte.id_ditte)
            INNER JOIN maaking_users AS maaking_users_1 ON retegas_ordini.id_utente = maaking_users_1.userid)
            INNER JOIN retegas_gas ON maaking_users_1.id_gas = retegas_gas.id_gas
            WHERE ((retegas_referenze.id_gas_referenze)= :id_gas)
            ORDER BY retegas_ordini.data_apertura DESC;"); */
            $stmt = $db->prepare("SELECT    O.id_ordini,
                                            O.data_apertura,
                                            O.data_chiusura,
                                            O.is_printable,
                                            O.descrizione_ordini,
                                            O.id_utente as id_referente,
                                            R.id_utente_referenze as id_referente_gas
                                    FROM retegas_referenze R
                                    INNER JOIN retegas_ordini O on O.id_ordini=R.id_ordine_referenze
                                  WHERE R.id_gas_referenze=:id_gas and id_utente_referenze>0
                                  ORDER BY O.data_apertura DESC;");
            $id_gas = _USER_ID_GAS;
            $stmt->bindParam(':id_gas', $id_gas , PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll();

        $r = '  <div>
                <div class="panel">
                <form class="smart-form">
                    <label class="input"> <i class="icon-append fa fa-filter"></i>
                                        <input type="text" placeholder="filtra tra gli ordini..." id="listfilter">
                                    </label>
                </form>
                </div>
                <ul id="list" style="height:400px;overflow-y:auto" class="list-unstyled">';

        foreach($rows as $row){

                $gestore = "";
                $gestoreGAS = "";
                $supervisore = "";
                $partecipante ="";
                $umile_aiutante ='';

                $apertura = strtotime($row["data_apertura"]);
                $chiusura = strtotime($row["data_chiusura"]);
                $today = strtotime(date("Y-m-d H:i"));
                if($apertura>$today){$color="text-info";}
                if($chiusura>$today AND $apertura<$today){$color="text-success";}
                if($chiusura<$today){$color="text-danger";}
                if($row["is_printable"]>0){$color="text-muted";}



                $stmt = $db->prepare("select * from retegas_dettaglio_ordini where id_utenti='"._USER_ID."' AND id_ordine=:id_ordine");
                $stmt->bindParam(':id_ordine', $row["id_ordini"] , PDO::PARAM_INT);
                $stmt->execute();

                if($stmt->rowCount()>0){
                    $partecipante ='<a href="#ajax_rd4/reports/la_mia_spesa.php?id='.$row["id_ordini"].'""><i class="fa fa-eye"></i></a> Partecipante';
                }else{
                    $partecipante ='';
                }




                if($row["id_referente"]==_USER_ID){
                    $gestore = '<a href="#ajax_rd4/ordini/edit.php?id='.$row["id_ordini"].'""><i class="fa fa-gears"></i></a> Gestore';
                }else{
                    if($row["id_referente_gas"]==_USER_ID){
                        $gestoreGAS ='<a href="#ajax_rd4/ordini/edit.php?id='.$row["id_ordini"].'""><i class="fa fa-home"></i></a> Gestore GAS';
                    }else{
                        if(_USER_PERMISSIONS & perm::puo_vedere_tutti_ordini){
                            $supervisore='<a href="#ajax_rd4/ordini/edit.php?id='.$row["id_ordini"].'""><i class="fa fa-star"></i></a> Supervisore';
                        }
                    }

                }


                $stmt = $db->prepare("select * from retegas_options where id_user='"._USER_ID."' AND id_ordine=:id_ordine AND chiave='AIUTO_ORDINI' and valore_int=1");
                $stmt->bindParam(':id_ordine', $row["id_ordini"] , PDO::PARAM_INT);
                $stmt->execute();
                if($stmt->rowCount()>0){
                    $umile_aiutante ='<a href="#ajax_rd4/ordini/ordine.php?id='.$row["id_ordini"].'""><i class="fa fa-hand-o-up "></i></a> Umile aiutante';
                }else{
                    $umile_aiutante ='';
                }


                if($partecipante<>'' | $supervisore<>'' | $gestoreGAS<>'' | $gestore<>'' | $umile_aiutante<>''){
                    $r .= '<li>
                        <i class="fa fa-circle '.$color.'"></i>
                        <span>
                        #'.$row["id_ordini"].' <a href="#ajax_rd4/ordini/ordine.php?id='.$row["id_ordini"].'">'.$row["descrizione_ordini"].'</a><br>
                        <i class="note">'.$partecipante.' '.$gestore.' '.$gestoreGAS.' '.$supervisore.' '.$umile_aiutante.'</i></span>


                     </li>';
                }
        }
        $r.="</ul>";


$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>false,
                    "deletebutton"=>false,
                    "colorbutton"=>true);
$wg_oco = $ui->create_widget($options);
$wg_oco->id = "wg_ordini_coinvolto_home";
$wg_oco->body = array("content" => $r,"class" => "");
$wg_oco->header = array(
    "title" => '<h2>Ordini che mi coinvolgono</h2>',
    "icon" => 'fa fa-heart'
);

?>
<div class="inbox-nav-bar no-content-padding">
    <h1 class="page-title txt-color-blueDark"><i class="fa fa-fw fa-dashboard"></i> Cruscotto &nbsp;</h1>
</div>

<section id="widget-grid" class="margin-top-10">

    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <?php if(!_USER_NONMOSTRAREHELPHOME){echo $wg_help->print_html();} ?>
            <?php echo $wg_oco->print_html(); ?>
        </article>
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <?php if(_USER_USA_CASSA){ echo $wg_cassa->print_html();} ?>
            <?php echo $wg_oa->print_html(); ?>
        </article>

    </div>

</section>


<script type="text/javascript">

    pageSetUp();

    var pagefunction = function() {

        $(document).on('change','#nonmostrarepiu',function(){
            if(this.checked) {value = "SI";}else{value = "NO";}
            $.ajax({
                      type: "POST",
                      url: "ajax_rd4/_act_main.php",
                      dataType: 'json',
                      data: {act: "nonmostrarepiu", value : value},
                      context: document.body
                    }).done(function(data) {
                        if(data.result=="OK"){
                            ok(data.msg);
                        }else{
                            ko(data.msg);
                        }

                    });
        });
         jQuery.expr[':'].Contains = function(a,i,m){
              return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase())>=0;
          };
        function listFilter(list) { // header is any element, list is an unordered list
            // create and add the filter form to the header

            $('#listfilter')
              .change( function () {
                var filter = $(this).val();
                if(filter) {
                  // this finds all links in a list that contain the input,
                  // and hide the ones not containing the input while showing the ones that do
                  $(list).find("span:not(:Contains(" + filter + "))").parent().hide();
                  $(list).find("span:Contains(" + filter + ")").parent().show();
                } else {
                  $(list).find("li").show();
                }
                return false;
              })
            .keyup( function () {
                // fire the above change event after every letter
                $(this).change();
            });
          }

        /*
        // clears memory even if nothing is in the function
        $.extend($.tablesorter.themes.bootstrap, {
            // these classes are added to the table. To see other table classes available,
            // look here: http://twitter.github.com/bootstrap/base-css.html#tables
            table      : 'table table-bordered',
            caption    : 'caption',
            header     : 'bootstrap-header', // give the header a gradient background
            footerRow  : '',
            footerCells: '',
            icons      : '', // add "icon-white" to make them white; this icon class is added to the <i> in the header
            sortNone   : 'bootstrap-icon-unsorted',
            sortAsc    : 'icon-chevron-up glyphicon glyphicon-chevron-up',     // includes classes for Bootstrap v2 & v3
            sortDesc   : 'icon-chevron-down glyphicon glyphicon-chevron-down', // includes classes for Bootstrap v2 & v3
            active     : '', // applied when column is sorted
            hover      : '', // use custom css here - bootstrap class may not override it
            filterRow  : '', // filter row class
            even       : '', // odd row zebra striping
            odd        : ''  // even row zebra striping
          });
        var pagerOptions = {

        // target the pager markup - see the HTML block below
        container: $(".pager"),

        // use this url format "http:/mydatabase.com?page={page}&size={size}&{sortList:col}"
        ajaxUrl: null,

        // modify the url after all processing has been applied
        customAjaxUrl: function(table, url) { return url; },

        // process ajax so that the data object is returned along with the total number of rows
        // example: { "data" : [{ "ID": 1, "Name": "Foo", "Last": "Bar" }], "total_rows" : 100 }
        ajaxProcessing: function(ajax){
            if (ajax && ajax.hasOwnProperty('data')) {
                // return [ "data", "total_rows" ];
                return [ ajax.total_rows, ajax.data ];
            }
        },

        // output string - default is '{page}/{totalPages}'
        // possible variables: {page}, {totalPages}, {filteredPages}, {startRow}, {endRow}, {filteredRows} and {totalRows}
        // also {page:input} & {startRow:input} will add a modifiable input in place of the value
        output: 'da {startRow:input} a {endRow} ({totalRows} Tot. - {filteredRows} filtrati',

        // apply disabled classname to the pager arrows when the rows at either extreme is visible - default is true
        updateArrows: true,

        // starting page of the pager (zero based index)
        page: 0,

        // Number of visible rows - default is 10
        size: 10,

        // Save pager page & size if the storage script is loaded (requires $.tablesorter.storage in jquery.tablesorter.widgets.js)
        savePages : true,

        //defines custom storage key
        storageKey:'tablesorter-pager',

        // if true, the table will remain the same height no matter how many records are displayed. The space is made up by an empty
        // table row set to a height to compensate; default is false
        fixedHeight: true,

        // remove rows from the table to speed up the sort of large tables.
        // setting this to false, only hides the non-visible rows; needed if you plan to add/remove rows with the pager enabled.
        removeRows: false,

        // css class names of pager arrows
        cssNext: '.next', // next page arrow
        cssPrev: '.prev', // previous page arrow
        cssFirst: '.first', // go to first page arrow
        cssLast: '.last', // go to last page arrow
        cssGoto: '.gotoPage', // select dropdown to allow choosing a page

        cssPageDisplay: '.pagedisplay', // location of where the "output" is displayed
        cssPageSize: '.pagesize', // page size selector - select dropdown that sets the "size" option

        // class added to arrows when at the extremes (i.e. prev/first arrows are "disabled" when on the first page)
        cssDisabled: 'disabled', // Note there is no period "." in front of this class name
        cssErrorRow: 'tablesorter-errorRow' // ajax error information row

    };

        //$('#ordini_io_coinvolto').tablesorter({
        //    theme: 'bootstrap',
        //    widgets: ["uitheme","filter","zebra"]
       // }).tablesorterPager(pagerOptions);
     */
     listFilter( $("#list"));
    };

    // end pagefunction


    pagefunction();

</script>