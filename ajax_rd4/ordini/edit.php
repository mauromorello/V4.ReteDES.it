<?php require_once("inc/init.php");
$ui = new SmartUI;
$page_title= "Gestisci Ordine";

//CONTROLLI
$id_ordine = (int)$_GET["id"];

if (!posso_gestire_ordine($id_ordine)){
    echo "Non ho i permessi per gestire questo ordine";
    die();
}

$stmt = $db->prepare("SELECT    O.id_ordini,
                                O.id_listini,
                                O.descrizione_ordini,
                                O.note_ordini,
                                O.is_printable,
                                O.costo_gestione,
                                O.costo_trasporto,
                                O.mail_level,
                                DATE_FORMAT(O.data_apertura,'%d/%m/%Y %H:%i') as data_apertura,
                                DATE_FORMAT(O.data_chiusura,'%d/%m/%Y %H:%i') as data_chiusura,
                                O.id_stato,
                                U.fullname,
                                L.descrizione_listini
                        FROM retegas_ordini O
                            inner join maaking_users U on U.userid=O.id_utente
                            inner join retegas_listini L on L.id_listini=O.id_listini
                        WHERE id_ordini=:id LIMIT 1;");
$stmt->bindValue(':id', $id_ordine, PDO::PARAM_INT);
$stmt->execute();
$rowo = $stmt->fetch(PDO::FETCH_ASSOC);


$s =<<<SEMPLICE
<form>

    <fieldset>
        <section class="margin-top-10 well well-sm">
            <label for="descrizione_ordini">Titolo</label>
            <h3><i class="fa fa-pencil pull-right"></i>&nbsp;&nbsp;<span class="editable" id="descrizione_ordini" data-pk="{$rowo["id_ordini"]}">{$rowo["descrizione_ordini"]}</span></h3>
        </section>

        <section class="margin-top-10 well well-sm">
         <label for="note_ordini">Note</label>
         <div class="summer" id="note_ordini">{$rowo["note_ordini"]}</div>
         <button class="btn btn-success pull-right margin-top-10">Salva le note</button>
         <div class="clearfix"></div>
        </section>

    </fieldset>
</form>
SEMPLICE;

$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>false,
                    "deletebutton"=>false,
                    "colorbutton"=>true);
$wg_edit_ordine = $ui->create_widget($options);
$wg_edit_ordine->id = "wg_edit_ordine";
$wg_edit_ordine->body = array("content" => $s,"class" => "");
$wg_edit_ordine->header = array(
    "title" => '<h2>Titolo & note</h2>',
    "icon" => 'fa fa-list'
);

//ordine futuro ? si può cambiare la data di apertura
$data_apertura = strtotime(str_replace('/', '-', $rowo["data_apertura"]));
$data_chiusura = strtotime(str_replace('/', '-', $rowo["data_chiusura"]));
$data_now = strtotime(date("d-m-Y H:i"));

if($data_apertura>$data_now){
    $ed_ap="date_ordine";
    $ic_ap="fa-pencil";
    $btn_ap ='<a  class="btn btn-success btn-md" id="start_ordine">FAI PARTIRE SUBITO</a >';
    $btn_ch ='<a  class="btn btn-default btn-md disable">CHIUDI  SUBITO</a >';
    $btn_co ='<a  class="btn btn-default btn-md disable">CONVALIDA</a >';
}else{
    $ed_ap="";
    $ic_ap="fa-lock";

    $btn_ap ='<a  class="btn btn-default btn-md disabled" >FAI PARTIRE  SUBITO</a >';

    if($data_chiusura>$data_now){
        $btn_co ='<a  class="btn btn-default btn-md disabled">CONVALIDA</a >';
        $btn_ch ='<a  class="btn btn-danger btn-md" id="end_ordine"  >CHIUDI SUBITO</a >';
    }else{
        $btn_ch ='<a  class="btn btn-default btn-md disabled" >CHIUDI SUBITO</a >';
        if($rowo["is_printable"]<>1){
            $btn_co ='<a  class="btn btn-info btn-md" id="convalida_ordine">CONVALIDA</a >';
        }else{
            $btn_co ='<a  class="btn btn-info btn-md" id="convalida_ordine">RIPRISTINA</a >';
        }
    }
}



$d =<<<SEMPLICE
<form>

    <fieldset>
        <section class="margin-top-10 well well-sm">
            <label for="data_apertura">Data / ora apertura ordine</label><br>
            <i class="fa {$ic_ap} fa-2x pull-right"></i>&nbsp;&nbsp;<a class="font-xl {$ed_ap}" id="data_apertura" data-type="combodate" data-template="DD MM YYYY  HH : mm" data-format="DD/MM/YYYY HH:mm" data-viewformat="DD/MM/YYYY HH:mm" data-pk="{$rowo["id_ordini"]}" data-original-title="Seleziona da questo elenco:">{$rowo["data_apertura"]}</a>
        </section>

        <section class="margin-top-10 well well-sm">
            <label for="data_apertura" >Data / ora chiusura ordine</label><br>
            <i class="fa fa-pencil fa-2x pull-right" ></i>&nbsp;&nbsp;<a class="font-xl date_ordine" id="data_chiusura" data-type="combodate" data-template="DD MM YYYY  HH : mm" data-format="DD/MM/YYYY HH:mm" data-viewformat="DD/MM/YYYY HH:mm" data-pk="{$rowo["id_ordini"]}" data-original-title="Seleziona da questo elenco:">{$rowo["data_chiusura"]}</a>
        </section>
    </fieldset>
</form>
SEMPLICE;

$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>false,
                    "deletebutton"=>false,
                    "colorbutton"=>true);
$wg_edit_scadenze = $ui->create_widget($options);
$wg_edit_scadenze->id = "wg_edit_scadenze";
$wg_edit_scadenze->body = array("content" => $d,"class" => "");
$wg_edit_scadenze->header = array(
    "title" => '<h2>Date & scadenze</h2>',
    "icon" => 'fa fa-calendar'
);

//QUERY PER DISTANZA
//(select ROUND((DEGREES(ACOS((SIN(RADIANS((SELECT user_gc_lat FROM maaking_users WHERE userid =2))) * SIN(RADIANS(G.gas_gc_lat))) + (COS(RADIANS((SELECT user_gc_lat FROM maaking_users WHERE userid =2 ))) * COS(RADIANS(G.gas_gc_lat)) * COS(RADIANS(G.gas_gc_lng -(SELECT user_gc_lng FROM maaking_users WHERE userid = 2)))))) * 69.09) * 1.609344) km from maaking_users WHERE userid = U.userid) km



$stmt = $db->prepare("SELECT    G.id_gas,
                                G.descrizione_gas,
                                G.gas_gc_lat as lat,
                                G.gas_gc_lng as lng,
                                COUNT(U.userid) as utenti,
                                D.des_descrizione,
                                O.valore_text,
                                D.id_des,
                                (select ROUND(
                                    (DEGREES
                                        (ACOS
                                            (
                                                (SIN
                                                    (RADIANS
                                                        (
                                                            ("._USER_GAS_LAT.")
                                                        )
                                                    ) * SIN(
                                                            RADIANS(G.gas_gc_lat)
                                                        )
                                                )
                                            + (COS
                                                (RADIANS
                                                    (
                                                        ("._USER_GAS_LAT.")
                                                    )
                                                )
                                            * COS(
                                                RADIANS(G.gas_gc_lat)
                                            )
                                            * COS(
                                                RADIANS(
                                                    G.gas_gc_lng - ("._USER_GAS_LNG.")
                                                        )
                                                 )
                                             )
                                             )
                                        ) * 69.09
                                    )
                                    * 1.609344) km
                                FROM maaking_users WHERE userid = U.userid
                            ) km
                FROM retegas_gas G
                inner join maaking_users U on U.id_gas = G.id_gas
                left join retegas_des D on D.id_des = G.id_des
                left join retegas_options O on O.id_gas = G.id_gas
                WHERE G.id_gas <> "._USER_ID_GAS."
                    AND G.id_gas >0
                    AND D.id_des >0
                    AND U.isactive=1
                    AND O.chiave = '_GAS_PUO_PART_ORD_EST'
                GROUP BY U.id_gas
                ORDER by KM asc
                LIMIT 30;
                ");


         $stmt->execute();
         $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
         $p  ='<div class="table-responsive"><table class="table table-striped smart-form has-tickbox">';
         $p .="
         <tbody>";
         foreach ($rows as $row) {
             if($row["valore_text"]=="SI"){

                 $stmt = $db->prepare("SELECT * from retegas_referenze WHERE id_ordine_referenze=:id_ordine AND id_gas_referenze='".$row["id_gas"]."'");
                 $stmt->bindParam(':id_ordine', $_GET["id"], PDO::PARAM_INT);
                 $stmt->execute();

                 if($stmt->rowCount()>0){
                    $col = ' success ';
                    $checked = ' checked="checked" ';
                    $icona = "";
                 }else{
                    $col = '';
                    $checked = '';
                    $icona = "";
                 }

                 $stmt = $db->prepare("SELECT * from retegas_dettaglio_ordini O inner join maaking_users U on U.userid = O.id_utenti WHERE U.id_Gas=".$row["id_gas"]." AND O.id_ordine=:id_ordine");
                 $stmt->bindParam(':id_ordine', $_GET["id"], PDO::PARAM_INT);
                 $stmt->execute();

                 if($stmt->rowCount()>0){
                    $col = ' warning ';
                    $checked = '';
                    $icona = "fa-cubes ";
                    $tooltip = ' rel="tooltip" data-original-title="Questo GAS ha già articoli in ordine" data-container="body" ';
                    $tb ="";
                 }else{
                    $tb = '<label class="checkbox"><input class="gas_partecipa" type="checkbox" value="'.$row["id_gas"].'" '.$checked.'><i></i></label>';
                    $tooltip='';
                 }




             }else{
                $tb = '&nbsp;';
                $col = ' danger ';
                $icona = "fa-lock";
                $tooltip = ' rel="tooltip" data-original-title="Questo GAS non vuole condividere ordini" data-container="body" ';
             }

             if($row["id_des"]<>_USER_ID_DES){
                $des ="<span> ".$row["des_descrizione"]." </span>";
             }else{
                $des ='';
             }



             $p .="<tr class=\"$col\">";
                $p .='<td>'.$tb.'</td>';
                $p.= "<td> ".$row["descrizione_gas"]."&nbsp;$des<span class=\"pull-right\"><b>".$row["utenti"]."</b> utenti, a km ".$row["km"]."</span></td><td><span $tooltip ><i class=\"fa $icona\"></i></span></td>";
            $p .="</tr>";
         }
         $p.="</tbody>
                </table>
                    </div>";


$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>false,
                    "deletebutton"=>false,
                    "colorbutton"=>true);
$wg_edit_partecipazione = $ui->create_widget($options);
$wg_edit_partecipazione->id = "wg_edit_condivisione";
$wg_edit_partecipazione->body = array("content" => $p,"class" => "");
$wg_edit_partecipazione->header = array(
    "title" => '<h2>Condivisione ordine</h2>',
    "icon" => 'fa fa-group'
);

//OPERAZIONI


$o =<<<SEMPLICE
            <div class="well margin-top-10 ">
                <label>Stato dell'ordine</label><br>
                <div class="btn-group btn-group-justified">{$btn_ap}{$btn_ch}{$btn_co}</div>
            </div>
            <div class="well margin-top-10">
                <label>Rettifiche</label><br>
                <div class="btn-group btn-group-justified">
                    <a class="btn btn-default btn-md">TOTALE</a>
                    <a class="btn btn-default btn-md">UTENTE</a>
                    <a class="btn btn-default btn-md">DETTAGLIO</a>
                </div>
            </div>
            <div class="well margin-top-10">
                <label>AIUTI</label><br>
                <div class="btn-group btn-group-justified">
                    <a class="btn btn-default btn-md">REFERENTI<br>EXTRA</a>
                    <a class="btn btn-default btn-md">AIUTI</a>
                    <a class="btn btn-default btn-md">CAMBIA<br>REFERENTE</a>
                </div>
            </div>
SEMPLICE;



$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>false,
                    "deletebutton"=>false,
                    "colorbutton"=>true);
$wg_edit_operazioni = $ui->create_widget($options);
$wg_edit_operazioni->id = "wg_edit_operazioni";
$wg_edit_operazioni->body = array("content" => $o,"class" => "");
$wg_edit_operazioni->header = array(
    "title" => '<h2>Operazioni effettuabili</h2>',
    "icon" => 'fa fa-gear'
);

$c =<<<SEMPLICE
<form>

    <fieldset>
        <section class="margin-top-10 well well-sm">
            <label for="costo_gestione">Costo gestione</label><br>
            <i class="fa fa-euro fa-2x"></i><i class="fa fa-pencil fa-2x pull-right"></i>&nbsp;&nbsp;<a class="font-xl costi" id="costo_gestione" data-type="text"   data-pk="{$rowo["id_ordini"]}" data-original-title="Costo gestione:">{$rowo["costo_gestione"]}</a>
        </section>

        <section class="margin-top-10 well well-sm">
            <label for="costo_trasporto" >Costo trasporto</label><br>
            <i class="fa fa-euro fa-2x"></i><i class="fa fa-pencil fa-2x pull-right"></i>&nbsp;&nbsp;<a class="font-xl costi" id="costo_trasporto" data-type="text"   data-pk="{$rowo["id_ordini"]}" data-original-title="Costo trasporto:">{$rowo["costo_trasporto"]}</a>
        </section>
    </fieldset>
</form>
SEMPLICE;



$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>false,
                    "deletebutton"=>false,
                    "colorbutton"=>true);
$wg_edit_costi = $ui->create_widget($options);
$wg_edit_costi->id = "wg_edit_costi";
$wg_edit_costi->body = array("content" => $c,"class" => "");
$wg_edit_costi->header = array(
    "title" => '<h2>Costi generali</h2>',
    "icon" => 'fa fa-euro'
);


?>

<div class="inbox-nav-bar no-content-padding">
    <h1 class="page-title txt-color-blueDark"><i class="fa fa-fw fa-cogs"></i> <?php echo "#".$id_ordine." <small>".$rowo["descrizione_ordini"]."</small>"?>&nbsp;</h1>
</div>

<section id="widget-grid" class="margin-top-10">



    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <?php if($rowo["is_printable"]<>1){echo $wg_edit_ordine->print_html();}?>
            <?php if($rowo["is_printable"]<>1){echo $wg_edit_scadenze->print_html();} ?>
            <?php if($rowo["is_printable"]<>1){echo $wg_edit_partecipazione->print_html();} ?>
        </article>
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">

            <?php echo help_render_html("edit_ordine",$page_title); ?>
            <?php if($rowo["is_printable"]<>1){echo $wg_edit_costi->print_html();} ?>
            <?php echo $wg_edit_operazioni->print_html(); ?>
        </article>

    </div>

</section>

<script type="text/javascript">

    pageSetUp();


    var pagefunction = function() {

        //-------------------------HELP
        <?php echo help_render_js("edit_ordine"); ?>
        //-------------------------HELP

        $.fn.editable.defaults.url = 'ajax_rd4/ordini/_act.php';

        var editable = $('.editable').editable({
            ajaxOptions: { dataType: 'json' },
            success: function(response, newValue) {
                        console.log(response);
                        if(response.result == 'KO'){
                            return response.msg;
                        }
                    }
        });
         $('.date_ordine').editable({
                language: 'it',
                placement: 'center',
                combodate: {
                    minYear: 2013,
                    maxYear: 2022
                },
                ajaxOptions: { dataType: 'json' },
                success: function(response, newValue) {
                        console.log(response);
                        if(response.result == 'KO'){
                            return response.msg;
                        }
                    }
            });
        $('.costi').editable({
                ajaxOptions: { dataType: 'json' },
                success: function(response, newValue) {
                        console.log(response);
                        if(response.result == 'KO'){
                            return response.msg;
                        }
                    }
            });
        var summernote = $('.summer').summernote({
              toolbar: [
                //[groupname, [button list]]

                ['style', ['bold', 'italic', 'underline', 'clear']],

                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
              ]
            });

       $("#start_ordine").click(function(e) {

            $.SmartMessageBox({
                title : "Apri subito questo ordine",
                content : "Cliccando si OK l'ordine si aprirà il prima possibile (ci vorranno circa 10 minuti...)",
                buttons : "[Annulla][OK]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="OK"){
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/ordini/_act.php",
                          dataType: 'json',
                          data: {act: "start_ordine"},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    ok(data.msg);}else{ko(data.msg);}
                                    //location.reload();
                        });
                }
            });

            e.preventDefault();
        })
        $("#end_ordine").click(function(e) {

            $.SmartMessageBox({
                title : "Chiudi subito questo ordine",
                content : "Cliccando su OK l'ordine si chiuderà il prima possibile (ci vorranno circa 10 minuti...)",
                buttons : "[Annulla][OK]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="OK"){
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/ordini/_act.php",
                          dataType: 'json',
                          data: {act: "end_ordine"},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    ok(data.msg);}else{ko(data.msg);}
                                    //location.reload();
                        });
                }
            });

            e.preventDefault();
        })
        $("#convalida_ordine").click(function(e) {

            $.SmartMessageBox({
                title : "Convalida questo ordine",
                content : "La convalida dell\'ordine serve ad avvertire gli utenti che tutti gli importi sono corretti e confermati.",
                buttons : "[Annulla][OK]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="OK"){
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/ordini/_act.php",
                          dataType: 'json',
                          data: {act: "convalida_ordine"},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    ok(data.msg);}else{ko(data.msg);}
                                    location.reload();
                        });
                }
            });

            e.preventDefault();
        })
        $(".gas_partecipa").change(function() {
            var action;
            if(this.checked) {
                action = "insert";
            }else{
                action = "delete";
            }
            console.log(this.value);
            var $t = $(this);
            $.ajax({
                          type: "POST",
                          url: "ajax_rd4/ordini/_act.php",
                          dataType: 'json',
                          data: {act: "gas_partecipa", action: action, value : this.value, id_ordine : <?php echo $id_ordine;?>},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                if(action=="insert"){
                                    $t.closest('tr').addClass(' success ');
                                }else{
                                    $t.closest('tr').removeClass(' success ');
                                }
                                ok(data.msg);
                            }else{
                                $t.closest('tr').removeClass(' success ');
                                $t.closest('tr').addClass(' danger ');
                                ko(data.msg);
                            }
                        });
        });


    }
    // end pagefunction

    loadScript("js/plugin/x-editable/moment.min.js", loadXEditable);

    function loadXEditable(){
        loadScript("js/plugin/x-editable/x-editable.min.js", loadSummerNote);
    }
    function loadSummerNote(){
        loadScript("js/plugin/summernote/summernote.min.js", pagefunction)
    }

</script>
