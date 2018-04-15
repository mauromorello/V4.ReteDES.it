<?php
require_once("inc/init.php");

$param = $_GET["param"];

if(CAST_TO_STRING($_GET["param"])==''){echo rd4_go_back("Nulla da cercare...");die();}

$link = new Encryption();


//ORDINI
$query = "SELECT
                O.id_ordini,
                O.descrizione_ordini,
                O.note_ordini,
                DATE_FORMAT(O.data_apertura,'%d/%m/%Y') as data_apertura,
                DATE_FORMAT(O.data_chiusura,'%d/%m/%Y') as data_chiusura,
                U.fullname,
                G.descrizione_gas,
                G.id_gas,
                D.id_des,
                L.id_tipologie
                FROM retegas_ordini O
            INNER JOIN maaking_users U on U.userid=O.id_utente
            INNER JOIN retegas_gas G on G.id_gas = U.id_gas
            INNER JOIN retegas_des D on D.id_des = G.id_des
            INNER JOIN retegas_listini L on L.id_listini=O.id_listini
            WHERE
                descrizione_ordini LIKE :descrizione_ordini
            OR  note_ordini LIKE :note_ordini
            OR  id_ordini=:id_ordini
            OR  fullname LIKE :fullname
            ORDER BY id_ordini DESC";
$stmt = $db->prepare($query);
$descrizione_ordine = "%".$param."%";
$fullname = "%".$param."%";
$note_ordini = "%".$param."%";
$id_ordini = CAST_TO_INT($param);
$stmt->bindParam(':descrizione_ordini', $descrizione_ordine, PDO::PARAM_STR);
$stmt->bindParam(':note_ordini', $note_ordini, PDO::PARAM_STR);
$stmt->bindParam(':fullname', $fullname, PDO::PARAM_STR);
$stmt->bindParam(':id_ordini', $id_ordini, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll();
$risultati_ordini=0;
$risultati_ordini_tutti=0;
foreach($rows as $row){
    $show=false;
    $permission_ordini = "Nessun permesso";

    if($row["id_gas"]==_USER_ID_GAS){
        $show=true;
    }

    if((_USER_PERMISSIONS & perm::puo_vedere_retegas) AND $row["id_des"]==_USER_ID_DES){
        $show=true;
    }

    if(_USER_PERMISSIONS & perm::puo_gestire_retegas){
        $show=true;
    }

    $risultati_ordini_tutti ++;
    if($show){
        $risultati_ordini++;
        if(strip_tags($row["note_ordini"])<>""){
            $note = '<p class="description">'.strip_tags($row["note_ordini"]).'</p>';
        }else{
            $note = '';
        }
        $h.='<div class="search-results clearfix">
                        <span class="pull-right note">
                            Apre il <strong>'.$row["data_apertura"].'</strong><br>
                            Chiude il <strong>'.$row["data_chiusura"].'</strong>
                        </span>
                        <h4><strong>#'.$row["id_ordini"].'</strong> <a href="'.APP_URL.'/#ajax_rd4/ordini/ordine.php?id='.$row["id_ordini"].'">'.$row["descrizione_ordini"].'</a></h4>
                        <div>
                            <div class="url text-success">
                                di '.$row["fullname"].', <strong>'.$row["descrizione_gas"].'</strong>
                            </div>
                            '.$note.'
                        </div>
                    </div>';
    }
}
//ORDINI

//DITTE
$query = "SELECT
                D.id_ditte,
                D.descrizione_ditte,
                D.indirizzo,
                D.website,
                D.note_ditte,
                D.tag_ditte,
                U.fullname,
                D.telefono,
                D.mail_ditte,
                G.descrizione_gas,
                G.id_gas,
                DE.id_des
                FROM retegas_ditte D
            INNER JOIN maaking_users U on U.userid=D.id_proponente
            INNER JOIN retegas_gas G on G.id_gas = U.id_gas
            INNER JOIN retegas_des DE on DE.id_des = G.id_des
            WHERE
                descrizione_ditte LIKE :descrizione_ditte
            OR  note_ditte LIKE :note_ditte
            OR  id_ditte=:id_ditte
            OR  tag_ditte LIKE :tag_ditte
            OR  fullname LIKE :fullname
            OR  indirizzo LIKE :indirizzo
            ORDER BY id_ditte DESC";
$stmt = $db->prepare($query);
$descrizione_ditte = "%".$param."%";
$fullname = "%".$param."%";
$note_ditte = "%".$param."%";
$id_ditte = CAST_TO_INT($param,0);
$tag_ditte = "%".$param."%";
$indirizzo = "%".$param."%";

$stmt->bindParam(':descrizione_ditte', $descrizione_ditte, PDO::PARAM_STR);
$stmt->bindParam(':note_ditte', $note_ditte, PDO::PARAM_STR);
$stmt->bindParam(':fullname', $fullname, PDO::PARAM_STR);
if($id_ditte=0){$id_ditte=-1;}
$stmt->bindParam(':id_ditte', $id_ditte, PDO::PARAM_INT);
$stmt->bindParam(':tag_ditte', $tag_ditte, PDO::PARAM_STR);
$stmt->bindParam(':indirizzo', $indirizzo, PDO::PARAM_STR);
$stmt->execute();
$rows = $stmt->fetchAll();
$risultati_ditte=0;
foreach($rows as $row){


    if(strip_tags($row["note_ditte"])<>""){
        $note = '<p class="description"><strong>Note:</strong> '.strip_tags($row["note_ditte"]).'</p>';
    }else{
        $note = '';
    }
    if(strip_tags($row["tag_ditte"])<>""){
        $tags = '<p class="description"><strong>Tags:</strong> '.strip_tags($row["tag_ditte"]).'</p>';
    }else{
        $tags = '';
    }
    if(strip_tags($row["website"])<>"" AND strip_tags($row["website"])<>"NON DEFINITO"){
        $web = ' <strong><i class="fa fa-globe"></i></strong> <a href="'.strip_tags($row["website"]).'" target="_BLANK">'.strip_tags($row["website"]).'</a>';
    }else{
        $web = '';
    }
    if(strip_tags($row["mail_ditte"])<>"" AND strip_tags($row["mail_ditte"])<>"NON DEFINITA"){
        $mail = ' <strong><i class="fa fa-envelope"></i></strong> <a href="mailTo:'.strip_tags($row["mail_ditte"]).'" target="_BLANK">'.strip_tags($row["mail_ditte"]).'</a>';
    }else{
        $mail = '';
    }
    if(strip_tags($row["telefono"])<>""){
        $tel = ' <strong><i class="fa fa-phone"></i></strong> <a  href="tel:'.strip_tags($row["telefono"]).'" target="_BLANK"> '.strip_tags($row["telefono"]).'</a>';
    }else{
        $tel = '';
    }

    if($row["id_ditte"]>0){
    $risultati_ditte ++;
    $d.='<div class="search-results clearfix">
                    <h4><strong>#'.$row["id_ditte"].'</strong> <a href="'.APP_URL.'/#ajax_rd4/fornitori/scheda.php?id='.$row["id_ditte"].'">'.$row["descrizione_ditte"].'</a></h4>
                    <div>
                        <i class="fa fa-map-marker"></i> '.$row["indirizzo"].'
                        <p class="note">
                        '.$web.$mail.$tel.'
                        </p>
                        <div class="url text-success">
                            di '.$row["fullname"].', <strong>'.$row["descrizione_gas"].'</strong>
                        </div>
                        '.$note.$tags.'
                    </div>
                </div>';
    }

}
//DITTE

//LISTINI
$query = "SELECT
                L.id_listini,
                L.descrizione_listini,
                L.is_privato,
                U.fullname,
                DI.descrizione_ditte,
                DI.indirizzo,
                G.descrizione_gas,
                DATE_FORMAT(L.data_valido,'%d/%m/%Y') as data_valido,
                G.id_gas,
                D.id_des,
                T.descrizione_tipologia
                FROM retegas_listini L
            INNER JOIN maaking_users U on U.userid=L.id_utenti
            INNER JOIN retegas_gas G on G.id_gas = U.id_gas
            INNER JOIN retegas_ditte DI on DI.id_ditte = L.id_ditte
            INNER JOIN retegas_tipologia T on T.id_tipologia = L.id_tipologie
            INNER JOIN retegas_des D on D.id_des = G.id_des
            WHERE
                L.data_valido>NOW() AND
                (
                descrizione_listini LIKE :descrizione_listini
            OR  descrizione_tipologia LIKE :descrizione_tipologia
            OR  id_listini=:id_listini
            OR  fullname LIKE :fullname
                )
            ORDER BY id_listini DESC";
$stmt = $db->prepare($query);
$descrizione_tipologia = "%".$param."%";
$fullname = "%".$param."%";
$descrizione_listini = "%".$param."%";
$id_listini = CAST_TO_INT($param);


$stmt->bindParam(':descrizione_listini', $descrizione_listini, PDO::PARAM_STR);
$stmt->bindParam(':descrizione_tipologia', $descrizione_tipologia, PDO::PARAM_STR);
$stmt->bindParam(':fullname', $fullname, PDO::PARAM_STR);
$stmt->bindParam(':id_listini', $id_listini, PDO::PARAM_INT);

$stmt->execute();
$rows = $stmt->fetchAll();
$risultati_listini=0;
$risultati_listini_tutti =0;
foreach($rows as $row){
    $show=false;
    $risultati_listini_tutti ++;

    if($row["is_privato"]>0){
        if($row["id_gas"]==_USER_ID_GAS){
            $show=true;
        }
    }else{
        $show=true;
    }

    if($show){
        $risultati_listini ++;
        $l.='<div class="search-results clearfix">
                        <span class="pull-right note">Valido fino al <strong>'.$row["data_valido"].'</strong></span>
                        <h4><strong>#'.$row["id_listini"].'</strong> <a href="'.APP_URL.'/#ajax_rd4/listini/listino.php?id='.$row["id_listini"].'">'.$row["descrizione_listini"].'</a></h4>
                        <div>
                            <i class="fa fa-tags"></i> '.$row["descrizione_tipologia"].'
                            <p class="note">
                            <i class="fa fa-truck"></i> '.$row["descrizione_ditte"].', '.$row["indirizzo"].'
                            </p>
                            <div class="url text-success">
                                di '.$row["fullname"].', <strong>'.$row["descrizione_gas"].'</strong>
                            </div>
                        </div>
                    </div>';
    }
}
//LISTINI

//ARTICOLI
$query = "SELECT
                A.id_articoli,
                A.codice,
                A.descrizione_articoli,
                A.articoli_note,
                A.articoli_opz_1,
                A.articoli_opz_2,
                A.articoli_opz_3,
                L.is_privato,
                L.id_listini,
                DATE_FORMAT(L.data_valido,'%d/%m/%Y') as data_valido,
                D.descrizione_ditte,
                D.indirizzo,
                L.descrizione_listini
                FROM retegas_articoli A
            INNER JOIN retegas_listini L on A.id_listini = L.id_listini
            INNER JOIN retegas_ditte D on D.id_ditte = L.id_ditte
            WHERE
                L.data_valido>NOW() AND
                (
                A.codice LIKE :codice
            OR  A.descrizione_articoli LIKE :descrizione_articoli
            OR  A.articoli_note LIKE :articoli_note
            OR  id_articoli=:id_articoli
            OR  A.articoli_opz_1 LIKE :articoli_opz_1
            OR  A.articoli_opz_2 LIKE :articoli_opz_2
            OR  A.articoli_opz_3 LIKE :articoli_opz_3
                )
            ORDER BY id_articoli DESC";
$stmt = $db->prepare($query);

$codice = "%".$param."%";
$descrizione_articoli = "%".$param."%";
$articoli_note = "%".$param."%";
$articoli_opz_1 = "%".$param."%";
$articoli_opz_2 = "%".$param."%";
$articoli_opz_3 = "%".$param."%";
if(CAST_TO_INT($param)>0){
    $id_articoli = CAST_TO_INT($param);
}else{
    $id_articoli = 0;
}
$stmt->bindParam(':id_articoli', $id_articoli, PDO::PARAM_INT);
$stmt->bindParam(':codice', $codice, PDO::PARAM_STR);
$stmt->bindParam(':descrizione_articoli', $descrizione_articoli, PDO::PARAM_STR);
$stmt->bindParam(':articoli_note', $articoli_note, PDO::PARAM_STR);

$stmt->bindParam(':articoli_opz_1', $articoli_opz_1, PDO::PARAM_STR);
$stmt->bindParam(':articoli_opz_2', $articoli_opz_2, PDO::PARAM_STR);
$stmt->bindParam(':articoli_opz_3', $articoli_opz_3, PDO::PARAM_STR);

$stmt->execute();
$rows = $stmt->fetchAll();
$risultati_articoli=0;
$risultati_articoli_tutti =0;
foreach($rows as $row){
    $show=false;
    $risultati_articoli_tutti ++;

    if($row["is_privato"]>0){
        if($row["id_gas"]==_USER_ID_GAS){
            $show=true;
        }
    }else{
        $show=true;
    }

    if($show){
        $risultati_articoli ++;
        if(strip_tags($row["articoli_note"])<>""){
            $note = '<p class="description">'.strip_tags($row["articoli_note"]).'</p>';
        }else{
            $note = '';
        }

        $tags = $row["articoli_opz_1"].' '.$row["articoli_opz_2"].' '.$row["articoli_opz_3"];
        if(trim(strip_tags($row["articoli_note"]))<>""){
            $tags = '<p class="description">'.strip_tags($tags).'</p>';
        }else{
            $tags = '';
        }


        $a.='<div class="search-results clearfix">
                        <span class="pull-right note">Valido fino al <strong>'.$row["data_valido"].'</strong></span>
                        <h5><strong>#'.$row["codice"].'</strong> '.$row["descrizione_articoli"].'</h4>
                        <div>
                            <div class="url text-success">
                                Listino <a href="'.APP_URL.'/#ajax_rd4/listini/listino.php?id='.$row["id_listini"].'">'.$row["descrizione_listini"].'</a> '.$row["descrizione_ditte"].', '.$row["indirizzo"].'
                            </div>
                        </div>
                        '.$tags.'
                        '.$note.'
                    </div>';
    }
}
//ARTICOLI
//UTENTI
unset($rows);
$query = "SELECT
                U.userid,
                U.username,
                U.email,
                U.fullname,
                U.profile,
                U.country,
                U.city,
                G.descrizione_gas,
                G.id_gas,
                G.id_des
                FROM maaking_users U
            INNER JOIN retegas_gas G on G.id_gas = U.id_gas
            WHERE
                username LIKE :username
            OR  profile LIKE :profile
            OR  userid=:userid
            OR  fullname LIKE :fullname
            OR  email LIKE :email
            ORDER BY fullname ASC";
$stmt = $db->prepare($query);
$email = "%".$param."%";
$username = "%".$param."%";
$fullname = "%".$param."%";
$profile = "%".$param."%";

$stmt->bindParam(':username', $username, PDO::PARAM_STR);
$stmt->bindParam(':profile', $profile, PDO::PARAM_STR);
$stmt->bindParam(':fullname', $fullname, PDO::PARAM_STR);
$stmt->bindParam(':email', $email, PDO::PARAM_INT);
if(CAST_TO_INT($param)>0){
    $userid = CAST_TO_INT($param);
}else{
    $userid = 1;
}
$stmt->bindParam(':userid', $userid, PDO::PARAM_INT);


$stmt->execute();
$rowsU = $stmt->fetchAll();
$risultati_utenti=0;
$risultati_utenti_tutti=0;

$u="";
foreach($rowsU as $row){
    $show=false;
    $user_link= $link->encode($row["userid"]);

    if($row["id_gas"]==_USER_ID_GAS){
        $show=true;
        if(_USER_PERMISSIONS & perm::puo_gestire_utenti){
            $gestisci='<a href="'.APP_URL.'/#ajax_rd4/user/gestisci.php?id='.$user_link.'" class="btn btn-default pull-right"><i class="fa fa-gear"></i> <span class="hidden-xs">Gestisci</span></a>';
        }else{
            $gestisci='';
        }
    }

    if((_USER_PERMISSIONS & perm::puo_vedere_retegas) AND $row["id_des"]==_USER_ID_DES){
        $show=true;

    }

    if(_USER_PERMISSIONS & perm::puo_gestire_retegas){
        $show=true;
        $gestisci='<a href="'.APP_URL.'/#ajax_rd4/user/gestisci.php?id='.$user_link.'" class="btn btn-danger pull-right link_gestisci" data-id="'.$user_link.'"><i class="fa fa-gear"></i> <span class="hidden-xs">Gestisci</span></a>';
    }

    $risultati_utenti_tutti ++;


    if($show){
        $risultati_utenti++;

        //note visibili solo a chi può gestire utenti
        $note = '';
        if(strip_tags($row["profile"])<>""){
            if(_USER_PERMISSIONS & perm::puo_gestire_utenti){
                if($row["id_gas"]==_USER_ID_GAS){
                    $note = '<p class="description">'.strip_tags($row["profile"]).'</p>';
                }
            }
        }


        $user_img = src_user($row["userid"]);

        $u.='<div class="search-results clearfix">
                        <img class="pull-left" SRC="'.$user_img .'" style="width:64px;height:64px;">
                        <h4><strong>#'.$row["userid"].'</strong> <a class="link_scheda_utente" href="'.APP_URL.'/#ajax_rd4/user/scheda.php?id='.$user_link.'" data-id="'.$user_link.'">'.utf8_encode($row["fullname"]).'</a> <small>'.$row["email"].'</small></h4>
                        <div>
                            <div class="url text-success">
                                <strong>'.$row["descrizione_gas"].'</strong>
                            </div>
                            <p class="note">'.$row["country"].' '.$row["city"].'</p>
                            '.$note.'
                        </div>
                        '.$gestisci.'
                    </div>';
    }
}
//UTENTI


//GAS




?>
<div class="row">

    <div class="col-sm-12">
        <br>
        <form action="#ajax_rd4/search.php" class="">
        <div class="input-group input-group-lg">
                <input class="form-control input-lg" name="param" type="text" placeholder="Cerca ancora..." id="search-user">
                <div class="input-group-btn">
                    <button type="submit" class="btn btn-default">
                        <i class="fa fa-fw fa-search fa-lg"></i>
                    </button>
                </div>
            </div>
        </form>
        <br>
        <ul id="myTab1" class="nav nav-tabs bordered">
            <li class="active">
                <a href="#s1" data-toggle="tab">Ordini <strong>(<?php echo $risultati_ordini ?>)</strong></i></a>
            </li>
            <li>
                <a href="#s2" data-toggle="tab">Ditte <strong>(<?php echo $risultati_ditte ?>)</strong></a>
            </li>
            <li>
                <a href="#s3" data-toggle="tab">Listini <strong>(<?php echo $risultati_listini ?>)</strong></a>
            </li>
            <li>
                <a href="#s4" data-toggle="tab">Articoli <strong>(<?php echo $risultati_articoli ?>)</strong></a>
            </li>
            <li>
                <a href="#s5" data-toggle="tab">Utenti <strong>(<?php echo $risultati_utenti ?>)</strong></a>
            </li>
            <li class="pull-right hidden-mobile">
                <a href="javascript:void(0);"> <span class="note">Circa <?php echo $risultati_ordini+$risultati_ditte+$risultati_listini+$risultati_articoli+$risultati_utenti ?> voci </span> </a>
            </li>
        </ul>

        <div id="myTabContent1" class="tab-content bg-color-white padding-10">
            <div class="tab-pane fade in active" id="s1">
                <h1 class="font-md">La ricerca di <strong><?php echo $param ?></strong> negli <strong>ordini</strong> ha prodotto <small class="text-danger"> <?php echo $risultati_ordini ?> risultati visibili</small>, su <?php echo $risultati_ordini_tutti ?> totali.</h1>
                <div class="alert alert-info">I risultati sono limitati in funzione delle abilitazioni attive.</div>
                <?php
                    echo $h;
                ?>
            </div>

            <div class="tab-pane fade" id="s2">
                <h1 class="font-md">La ricerca di <strong><?php echo $param ?></strong> nelle <strong>ditte</strong> ha prodotto <small class="text-danger"> <?php echo $risultati_ditte ?> risultati visibili</small>.</h1>
                <br>
                <?php
                    echo $d;
                ?>
            </div>

            <div class="tab-pane fade" id="s3">
                <h1 class="font-md">La ricerca di <strong><?php echo $param ?></strong> nei <strong>listini</strong> ha prodotto <small class="text-danger"> <?php echo $risultati_listini ?> risultati visibili, su <?php echo $risultati_listini_tutti ?> totali.</small>.</h1>
                <div class="alert alert-info">Non sono stati considerati i listini scaduti o quelli privati.</div>
                <br>
                <?php
                    echo $l;
                ?>
            </div>
            <div class="tab-pane fade" id="s4">
                <h1 class="font-md">La ricerca di <strong><?php echo $param ?></strong> negli <strong>articoli</strong> ha prodotto <small class="text-danger"> <?php echo $risultati_articoli ?> risultati visibili, su <?php echo $risultati_articoli_tutti ?> totali.</small>.</h1>
                <div class="alert alert-info">Non sono stati considerati gli articoli appartenenti a listini scaduti o privati.</div>
                <br>
                <?php
                    echo $a;
                ?>
            </div>
            <div class="tab-pane fade" id="s5">
                <h1 class="font-md">La ricerca di <strong><?php echo $param ?></strong> negli <strong>utenti</strong> ha prodotto <small class="text-danger"> <?php echo $risultati_utenti ?> risultati visibili, su <?php echo $risultati_utenti_tutti ?> totali.</small>.</h1>
                <div class="alert alert-info">Non sono stati considerati gli utenti appartenenti a gas o des diversi dal tuo.</div>
                <br>
                <?php
                    echo $u;
                ?>
            </div>
        </div>

    </div>

</div>

<!-- end row -->

<script type="text/javascript">
    pageSetUp();
    var pagefunction = function() {

        document.title = '<?php echo "ReteDES.it :: ".strip_tags($param);?>';
        $("#search-project").focus();
        /*
        $('.link_gestisci').click(function(e){
            loadURL('/gas4/ajax_rd4/user/gestisci.php?id='+$(this).data('id'),$('#content'));
            e.preventDefault();
        })
        $('.link_scheda_utente').click(function(e){
            loadURL('/gas4/ajax_rd4/user/scheda.php?id='+$(this).data('id'),$('#content'));
            e.preventDefault();
        })*/
    };
    pagefunction();
</script>

