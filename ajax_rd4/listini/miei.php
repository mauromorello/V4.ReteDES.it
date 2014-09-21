<?php
require_once("inc/init.php");
$ui = new SmartUI;
$converter = new Encryption;

$page_title = "Miei listini";




$stmt = $db->prepare("SELECT * FROM retegas_listini WHERE id_utenti='"._USER_ID."' ORDER BY data_valido DESC;");

    $stmt->execute();
    $rows = $stmt->fetchAll();


    $li = '<ul class="list-group" style="max-height:600px; overflow-y:auto;">';
    foreach($rows as $row){

        $stmt = $db->prepare("SELECT U.fullname,G.descrizione_gas,G.id_gas FROM maaking_users U inner join retegas_gas G on G.id_gas=U.id_gas WHERE U.userid=:userid LIMIT 1;");
        $stmt->bindParam(':userid', $row["id_utenti"], PDO::PARAM_INT);
        $stmt->execute();
        $utente = $stmt->fetch();

        $stmt = $db->prepare("SELECT D.id_ditte, D.descrizione_ditte,D.indirizzo FROM retegas_ditte D WHERE D.id_ditte=:id_ditta LIMIT 1;");
        $stmt->bindParam(':id_ditta', $row["id_ditte"], PDO::PARAM_INT);
        $stmt->execute();
        $ditta = $stmt->fetch();

        $stmt = $db->prepare("SELECT count(*) as conto FROM retegas_articoli WHERE id_listini=:id_listini");
        $stmt->bindParam(':id_listini', $row["id_listini"], PDO::PARAM_INT);
        $stmt->execute();
        $articoli = $stmt->fetch();


        if(strtotime($row["data_valido"])<strtotime(date("Y-m-d H:i:s"))){
            $class_scaduto=" bg-color-redLight txt-color-white";
            $scade = 'Scaduto dal '.conv_datetime_from_db($row["data_valido"]);
            $icona_scade='<i class="pull-right fa fa-warning fa-2x txt-color-red"></i>';
        }else{
            $class_scaduto ="";
            //$scade = "Scadrà il ".conv_datetime_from_db($row["data_valido"]);
            $scade="";
            $icona_scade='<i class="pull-right fa fa-check-square-o fa-2x txt-color-green"></i>';
        }

        if($row["tipo_listino"]==1){
            $icona_scade='<i class="pull-right fa fa-archive fa-2x txt-color-blue"></i>';
            $magazzino = " LISTINO MAGAZZINO<br>";
            $scade="";
        }else{
            $magazzino = "";
        }


        if(($row["is_privato"]==1) AND ($utente["id_gas"]<>_USER_ID_GAS)){
            $li.= '<li class="list-group-item" >
                    <i class="pull-right fa fa-eye-slash fa-2x txt-color-grey"></i>
                    <h3 class="txt-color-grey">Listino privato '.$utente["descrizione_gas"].'</h3>
                    </li>';
        }else{
            $li.= '<li class="list-group-item" >

                    '.$icona_scade.'
                    <strong class="font-sm" style="cursor:pointer" rel="C"><a href="#ajax_rd4/listini/listino.php?id='.$row["id_listini"].'">'.$row["descrizione_listini"].'</a></strong><br>
                    <a href="javascript:void(0);"><i class="fa fa-truck"></a></i>&nbsp;'.$ditta["descrizione_ditte"].'&nbsp;&nbsp;<small class="font-xs"><i class="fa fa-cubes"></i>&nbsp;<b>'.$articoli["conto"].'</b></small><br>
                    '.$magazzino.'
                    '.$scade.'

                    </li>';
        }
    }

    $li .="</ul>";

$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>true,
                    "deletebutton"=>false,
                    "colorbutton"=>true);
$wg_miei_listini = $ui->create_widget($options);
$wg_miei_listini->id = "wg_misi_listini";
$wg_miei_listini->body = array("content" => $li,"class" => "no-padding");
$wg_miei_listini->header = array(
    "title" => '<h2>Miei listini</h2>',
    "icon" => 'fa fa-cubes'
    );



?>
<div class="inbox-nav-bar no-content-padding">
    <h1 class="page-title txt-color-blueDark"><i class="fa fa-fw fa-cubes"></i> Miei listini  &nbsp;</h1>
</div>

<section id="widget-grid" class="margin-top-10">

    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <?php echo help_render_html('miei_listini',$page_title); ?>
        </article>
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <?php echo $wg_miei_listini->print_html(); ?>
        </article>

    </div>

    <hr>

    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

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
        <?php echo help_render_js("miei_listini"); ?>
        //-------------------------HELP
    } // end pagefunction



    pagefunction();
</script>
