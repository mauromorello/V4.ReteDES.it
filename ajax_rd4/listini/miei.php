<?php
require_once("inc/init.php");
$ui = new SmartUI;
$converter = new Encryption;

$page_title = "Miei listini";
$page_id = "miei_listini";



$stmt = $db->prepare("SELECT * FROM retegas_listini WHERE id_utenti='"._USER_ID."' AND is_deleted=0 ORDER BY data_valido DESC;");

    $stmt->execute();
    $rows = $stmt->fetchAll();
    $n_scaduto=0;

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
            $class_scaduto="scaduto";
            $style_scaduto=' style="display:none;" ';
            $n_scaduto++;
            $scade = 'Scaduto dal '.conv_datetime_from_db($row["data_valido"]);
            $icona_scade='<i class="pull-right fa fa-warning fa-2x txt-color-red"></i>';
        }else{
            $class_scaduto ="";
            $style_scaduto="";
            //$scade = "Scadrà il ".conv_datetime_from_db($row["data_valido"]);
            $scade='Scadrà il '.conv_datetime_from_db($row["data_valido"]);
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
            $li.= '<li class="list-group-item '.$class_scaduto.' " '.$style_scaduto.'>
                    <i class="pull-right fa fa-eye-slash fa-2x txt-color-grey"></i>
                    <h3 class="txt-color-grey">Listino privato '.$utente["descrizione_gas"].'</h3>
                    </li>';
        }else{
            if($row[is_multiditta]>0){
                $multiditta='<i class="fa fa-truck text-success"></i>&nbsp;<i class="fa fa-truck text-danger"></i>&nbsp;<i class="fa fa-truck text-warning"></i>&nbsp;MULTIDITTA';
            }else{
                $multiditta='<a href="javascript:void(0);"><i class="fa fa-truck"></a></i>&nbsp;'.$ditta["descrizione_ditte"].'';
            }


            $li.= '<li class="list-group-item '.$class_scaduto.'" '.$style_scaduto.'>

                    '.$icona_scade.'
                    <strong class="font-sm" style="cursor:pointer" rel="C"><a href="#ajax_rd4/listini/listino.php?id='.$row["id_listini"].'">'.$row["descrizione_listini"].'</a></strong> '.$row["descrizione_listini"].'</a></strong> '.(conv_date_from_db($row["data_creazione"]) <> "00/00/0000" ? "del ".conv_date_from_db($row["data_creazione"]) : "").'<br>
                    '.$multiditta.'&nbsp;&nbsp;<small class="font-xs"><i class="fa fa-cubes"></i>&nbsp;<b>'.$articoli["conto"].'</b></small><br>
                    '.$magazzino.'
                    '.$scade.'

                    </li>';
        }
    }

    $li .="</ul>";

    //$stmt = $db->prepare("SELECT * FROM retegas_listini WHERE id_utenti='"._USER_ID."' ORDER BY data_valido DESC;");
    $stmt = $db->prepare("SELECT * FROM retegas_options WHERE id_user='"._USER_ID."' AND chiave='_AIUTO_GESTIONE_LISTINO' AND valore_int=1;");

    $stmt->execute();
    $rows = $stmt->fetchAll();


    $li_aiuto = '<ul class="list-group" style="max-height:600px; overflow-y:auto;">';
    $li_aiuto_n = 0;
    $n_scaduto_2=0;
    foreach($rows as $row){
        $li_aiuto_n ++;

        $stmt = $db->prepare("SELECT * FROM retegas_listini WHERE id_listini=:id_listini;");
        $stmt->bindParam(':id_listini', $row["id_listino"], PDO::PARAM_INT);
        $stmt->execute();
        $listino = $stmt->fetch();


        $stmt = $db->prepare("SELECT U.fullname,G.descrizione_gas,G.id_gas FROM maaking_users U inner join retegas_gas G on G.id_gas=U.id_gas WHERE U.userid=:userid LIMIT 1;");
        $stmt->bindParam(':userid', $listino["id_utenti"], PDO::PARAM_INT);
        $stmt->execute();
        $utente = $stmt->fetch();

        $stmt = $db->prepare("SELECT D.id_ditte, D.descrizione_ditte,D.indirizzo FROM retegas_ditte D WHERE D.id_ditte=:id_ditta LIMIT 1;");
        $stmt->bindParam(':id_ditta', $listino["id_ditte"], PDO::PARAM_INT);
        $stmt->execute();
        $ditta = $stmt->fetch();

        $stmt = $db->prepare("SELECT count(*) as conto FROM retegas_articoli WHERE id_listini=:id_listini");
        $stmt->bindParam(':id_listini', $row["id_listino"], PDO::PARAM_INT);
        $stmt->execute();
        $articoli = $stmt->fetch();


        if(strtotime($listino["data_valido"])<strtotime(date("Y-m-d H:i:s"))){
            $class_scaduto_2=" scaduto_2 ";
            $style_scaduto_2=' style="display:none;" ';
            $n_scaduto_2++;
            $scade = 'Scaduto dal '.conv_datetime_from_db($listino["data_valido"]);
            $icona_scade='<i class="pull-right fa fa-warning fa-2x txt-color-red"></i>';
        }else{
            $class_scaduto_2 ="";
            $style_scaduto_2 ='';
            //$scade = "Scadrà il ".conv_datetime_from_db($row["data_valido"]);
            $scade='Scadrà il '.conv_datetime_from_db($listino["data_valido"]);
            $icona_scade='<i class="pull-right fa fa-check-square-o fa-2x txt-color-green"></i>';
        }

        if($listino["tipo_listino"]==1){
            $icona_scade='<i class="pull-right fa fa-archive fa-2x txt-color-blue"></i>';
            $magazzino = " LISTINO MAGAZZINO<br>";
            $scade="";
        }else{
            $magazzino = "";
        }



            $li_aiuto.= '<li class="list-group-item '.$class_scaduto_2.' " '.$style_scaduto_2.'>
                    di '.$utente["fullname"].':
                    '.$icona_scade.'
                    <strong class="font-sm" style="cursor:pointer" rel="C"><a href="#ajax_rd4/listini/listino.php?id='.$listino["id_listini"].'">'.$listino["descrizione_listini"].'</a></strong> '.$listino["descrizione_listini"].'</a></strong> '.(conv_date_from_db($listino["data_creazione"]) <> "00/00/0000" ? "del ".conv_date_from_db($row["data_creazione"]) : "").'<br>
                    <a href="javascript:void(0);"><i class="fa fa-truck"></a></i>&nbsp;'.$ditta["descrizione_ditte"].'&nbsp;&nbsp;<small class="font-xs"><i class="fa fa-cubes"></i>&nbsp;<b>'.$articoli["conto"].'</b></small><br>
                    '.$magazzino.'
                    '.$scade.'

                    </li>';

    }
    if($li_aiuto_n==0){
        $li_aiuto .='<li><p class="alert alert-info">Non aiuti a gestire nessun listino.</p></li>';
    }
    $li_aiuto .="</ul>";

    $title_navbar='Miei listini';
    if(_USER_PERMISSIONS & perm::puo_creare_listini){
        $buttons[]='<button  data-id_ditta="0" class="aggiungi_listino btn btn-link"><i class="fa fa-plus"></i> Nuovo Listino multiditta</button>';
    }

?>

<?php echo navbar2($title_navbar,$buttons); ?>

<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
        </article>

    </div>
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <h1>Listini che gestisco</h1>
            <?php if($n_scaduto>0){ ?>
            <button class="pull-right btn btn-xs btn-default" id="mostra_scaduti">Mostra anche quelli scaduti <strong>(<?php echo $n_scaduto;?>)</strong></button>
            <div class="clearfix margin-bottom-10"></div>
            <?php }?>
            <?php echo $li; ?>
        </article>
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <h1>Listini che aiuto a gestire</h1>
            <?php if($n_scaduto_2>0){ ?>
            <button class="pull-right btn btn-xs btn-default" id="mostra_scaduti_2">Mostra anche quelli scaduti <strong>(<?php echo $n_scaduto_2;?>)</strong></button>
            <div class="clearfix margin-bottom-10"></div>
            <?php }?>
            <?php echo $li_aiuto; ?>
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

        $(document).off('click','#mostra_scaduti');
        $(document).on('click','#mostra_scaduti',function(e){

            e.preventDefault();
            $('.scaduto').show();

        });
        $(document).off('click','#mostra_scaduti_2');
        $(document).on('click','#mostra_scaduti_2',function(e){

            e.preventDefault();
            $('.scaduto_2').show();

        });

    } // end pagefunction



    pagefunction();
</script>
