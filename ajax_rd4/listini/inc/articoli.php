<?php
require_once("../../../lib/config.php");

$id_listino = CAST_TO_INT($_GET['id_listino'],0);
$page = (int)$_GET['page']; // get the requested page
$limit = (int)$_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if(!$sidx) $sidx ="codice";

// SE NON E' EDIT
if($_POST["oper"]<>"edit"){
    if($_GET['_search']=="true"){
        if($_GET['descrizione_articoli']<>""){
            $search .= " AND descrizione_articoli LIKE :descrizione_articoli ";
            $descrizione_articoli = "%".$_GET['descrizione_articoli']."%";
        }
        if($_GET['codice']<>""){
            $search .= " AND codice LIKE :codice ";
            $codice = "%".$_GET['codice']."%";
        }
        if($_GET['articoli_opz_1']<>""){
            $search .= " AND articoli_opz_1 LIKE :articoli_opz_1 ";
            $articoli_opz_1 = "%".$_GET['articoli_opz_1']."%";
        }
        if($_GET['articoli_opz_2']<>""){
            $search .= " AND articoli_opz_2 LIKE :articoli_opz_2 ";
            $articoli_opz_2 = "%".$_GET['articoli_opz_2']."%";
        }
        if($_GET['articoli_opz_3']<>""){
            $search .= " AND articoli_opz_3 LIKE :articoli_opz_3 ";
            $articoli_opz_3 = "%".$_GET['articoli_opz_3']."%";
        }
    }
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM  retegas_articoli WHERE id_listini = :id_listino $search LIMIT 1;");
    $stmt->bindParam(':id_listino', $id_listino, PDO::PARAM_INT);
    if($_GET['descrizione_articoli']<>""){$stmt->bindParam(':descrizione_articoli', $descrizione_articoli, PDO::PARAM_STR);}
    if($_GET['codice']<>""){$stmt->bindParam(':codice', $codice, PDO::PARAM_STR);}
    if($_GET['articoli_opz_1']<>""){$stmt->bindParam(':articoli_opz_1', $articoli_opz_1, PDO::PARAM_STR);}
    if($_GET['articoli_opz_2']<>""){$stmt->bindParam(':articoli_opz_2', $articoli_opz_2, PDO::PARAM_STR);}
    if($_GET['articoli_opz_3']<>""){$stmt->bindParam(':articoli_opz_3', $articoli_opz_3, PDO::PARAM_STR);}

    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $count = $row['count'];

    if( $count >0 ) {
        $total_pages = ceil($count/$limit);
    } else {
        $total_pages = 0;
    }
    if ($page > $total_pages) $page=$total_pages;
    $start = $limit*$page - $limit; // do not put $limit*($page - 1)

    //$SQL = "SELECT a.id, a.invdate, b.name, a.amount,a.tax,a.total,a.note FROM invheader a, clients b WHERE a.client_id=b.client_id ORDER BY $sidx $sord LIMIT $start , $limit";
    //$result = mysql_query( $SQL ) or die("Couldn t execute query.".mysql_error());
    $sql = "SELECT * FROM  retegas_articoli WHERE id_listini = :id_listino $search ORDER BY $sidx $sord LIMIT $start , $limit";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id_listino', $id_listino, PDO::PARAM_INT);
    //SEARCH
    if($_GET['descrizione_articoli']<>""){$stmt->bindParam(':descrizione_articoli', $descrizione_articoli, PDO::PARAM_STR);}
    if($_GET['codice']<>""){$stmt->bindParam(':codice', $codice, PDO::PARAM_STR);}
    if($_GET['articoli_opz_1']<>""){$stmt->bindParam(':articoli_opz_1', $articoli_opz_1, PDO::PARAM_STR);}
    if($_GET['articoli_opz_2']<>""){$stmt->bindParam(':articoli_opz_2', $articoli_opz_2, PDO::PARAM_STR);}
    if($_GET['articoli_opz_3']<>""){$stmt->bindParam(':articoli_opz_3', $articoli_opz_3, PDO::PARAM_STR);}
    //$stmt->bindParam(':sidx', $sidx, PDO::PARAM_INT);
    //$stmt->bindParam(':sord', $sord, PDO::PARAM_INT);
    //$stmt->bindParam(':start', $start, PDO::PARAM_INT);
    //$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $responce->page = $page;
    $responce->total = $total_pages;
    $responce->records = $count;
    $responce->sql = $sql;
    $i=0;
    foreach($rows as $row){
    //while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
        $responce->rows[$i]['id']=$row[id_articoli];
        $responce->rows[$i]['cell']=array(  //$row[id_articoli],
                                            $row[codice],
                                            $row[descrizione_articoli],
                                            $row[prezzo],

                                            $row[u_misura],
                                            $row[misura],

                                            round($row[qta_scatola]),
                                            round($row[qta_minima]),

                                            $row[articoli_unico],
                                            $row[ingombro],

                                            $row[articoli_opz_1],
                                            $row[articoli_opz_2],
                                            $row[articoli_opz_3],
                                            strip_tags($row[articoli_note]),);
                                            //

                                            //$row[articoli_opz_1],
                                            //$row[articoli_opz_2],
                                           // $row[articoli_opz_3]);
        $i++;
    }
    echo json_encode($responce);
    die();
}else{
    //Check se sono il proprietario del listino
    $id_articolo = CAST_TO_INT($_POST["id"]);

    $stmt = $db->prepare("SELECT * FROM  retegas_articoli WHERE id_articoli = :id_articoli  LIMIT 1;");
    $stmt->bindParam(':id_articoli', $id_articolo, PDO::PARAM_INT);
    $stmt->execute();
    $rowA = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $db->prepare("SELECT * FROM  retegas_listini WHERE id_listini = :id_listini AND id_utenti='"._USER_ID."';");
    $stmt->bindParam(':id_listini', $rowA["id_listini"], PDO::PARAM_INT);
    $stmt->execute();
    if($stmt->rowCount()==1){
        //CASTING
        $codice = trim(strip_tags(CAST_TO_STRING($_POST["codice"])));

        //CONTROLLO CODICE NON UNIVOCO
        $sql = "SELECT count(*) as c FROM  retegas_articoli WHERE id_listini = :id_listini AND codice = :codice AND id_articoli <> :id_articoli ";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_listini', $rowA["id_listini"], PDO::PARAM_INT);
        $stmt->bindParam(':codice', $codice, PDO::PARAM_STR);
        $stmt->bindParam(':id_articoli', $id_articolo, PDO::PARAM_INT);
        $stmt->execute();
        $rowC = $stmt->fetch(PDO::FETCH_ASSOC);
        if($rowC["c"]>0){
            $res=array("result"=>"KO", "msg"=>"Codice articolo già esistente" );
            echo json_encode($res);
            die();
        }

        $descrizione_articoli = trim(strip_tags(CAST_TO_STRING($_POST["descrizione_articoli"])));
        $prezzo = CAST_TO_FLOAT($_POST["prezzo"],0);
        $u_misura = trim(strip_tags(CAST_TO_STRING($_POST["u_misura"])));
        $misura = CAST_TO_FLOAT($_POST["misura"],0);
        $qta_scatola = round(CAST_TO_FLOAT($_POST["qta_scatola"],0),4);
        $qta_minima = round(CAST_TO_FLOAT($_POST["qta_minima"],0),4);

        if (  $misura==0 OR
            $qta_scatola==0 OR
            $qta_minima==0 OR
            $prezzo ==0){
            $res=array("result"=>"KO", "msg"=>"Misura, Quantità, e prezzo non possono essere zero." );
            echo json_encode($res);
            die();
        }


        if($qta_minima>$qta_scatola){
            $res=array("result"=>"KO", "msg"=>"La quantità di multiplo non può essere superiore alla quantità di scatola." );
            echo json_encode($res);
            die();
        }else{
            $big = $qta_scatola;
            while ($big > 0) {
                $big=$big-$qta_minima;
            }
            if($big<>0){
                $res=array("result"=>"KO", "msg"=>"La quantità di multiplo non è corretta." );
                echo json_encode($res);
                die();
            }
        }




        $articoli_unico = CAST_TO_INT($_POST["articoli_unico"],0,1);
        $ingombro = trim(strip_tags(CAST_TO_STRING($_POST["ingombro"])));
        //CONTROLLO SU MULTIPLO

        $articoli_opz_1 = trim(strip_tags(CAST_TO_STRING($_POST["articoli_opz_1"])));
        $articoli_opz_2 = trim(strip_tags(CAST_TO_STRING($_POST["articoli_opz_2"])));
        $articoli_opz_3 = trim(strip_tags(CAST_TO_STRING($_POST["articoli_opz_3"])));
        $articoli_note = trim(strip_tags(CAST_TO_STRING($_POST["articoli_note"])));

        $stmt = $db->prepare("UPDATE retegas_articoli SET   codice= :codice,
                                                            descrizione_articoli= :descrizione_articoli,
                                                            prezzo = :prezzo,
                                                            u_misura = :u_misura,
                                                            misura = :misura,
                                                            qta_scatola = :qta_scatola,
                                                            qta_minima = :qta_minima,
                                                            articoli_unico = :articoli_unico,
                                                            ingombro = :ingombro,
                                                            articoli_opz_1 = :articoli_opz_1,
                                                            articoli_opz_2 = :articoli_opz_2,
                                                            articoli_opz_3 = :articoli_opz_3,
                                                            articoli_note = :articoli_note
                                                            WHERE id_articoli = :id_articoli LIMIT 1;");
        $stmt->bindParam(':id_articoli', $id_articolo, PDO::PARAM_INT);
        $stmt->bindParam(':codice', $codice, PDO::PARAM_STR);
        $stmt->bindParam(':descrizione_articoli', $descrizione_articoli, PDO::PARAM_STR);

        $stmt->bindParam(':prezzo', $prezzo, PDO::PARAM_STR);
        $stmt->bindParam(':u_misura', $u_misura, PDO::PARAM_STR);
        $stmt->bindParam(':misura', $misura, PDO::PARAM_STR);
        $stmt->bindParam(':qta_scatola', $qta_scatola, PDO::PARAM_STR);
        $stmt->bindParam(':qta_minima', $qta_minima, PDO::PARAM_STR);

        $stmt->bindParam(':articoli_unico', $articoli_unico, PDO::PARAM_INT);
        $stmt->bindParam(':ingombro', $ingombro, PDO::PARAM_STR);

        $stmt->bindParam(':articoli_opz_1', $articoli_opz_1, PDO::PARAM_STR);
        $stmt->bindParam(':articoli_opz_2', $articoli_opz_2, PDO::PARAM_STR);
        $stmt->bindParam(':articoli_opz_3', $articoli_opz_3, PDO::PARAM_STR);

        $stmt->bindParam(':articoli_note', $articoli_note, PDO::PARAM_STR);

        $stmt->execute();
        if($stmt->rowCount()==1){
            $res=array("result"=>"OK", "msg"=>"Articolo aggiornato" );
        }else{
            $res=array("result"=>"KO", "msg"=>"Salvataggio non riuscito" );
        }


    }else{
        $res=array("result"=>"KO", "msg"=>"Non sei il proprietario del listino" );
    }



    echo json_encode($res);
}