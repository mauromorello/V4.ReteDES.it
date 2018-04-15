<?php
require_once("../../../lib/config.php");

//LISTINI DELLA STESSA DITTA
if(CAST_TO_INT($_GET["d"],0)>0){
        $sql = "SELECT  L.id_listini as id,
                        L.descrizione_listini as text,
                        L.is_privato,
                        U.fullname,
                        U.id_gas,
                        G.descrizione_gas,
                        D.descrizione_ditte,
                        DATE_FORMAT(L.data_valido, '%d/%m/%Y') as data_valido
                        FROM retegas_ditte D
                    inner join retegas_listini L on L.id_ditte = D.id_ditte
                    inner join maaking_users U on U.userid=L.id_utenti
                    inner join retegas_gas G on G.id_gas=U.id_gas
                    WHERE
                    CONCAT(D.descrizione_ditte,' ', L.descrizione_listini) LIKE :q
                    AND L.tipo_listino=0
                    AND L.id_ditte=:d
                    AND L.data_valido>NOW()";
        //echo $sql;die();
        $stmt = $db->prepare($sql);
        //$q = "%iri%";
        $q = "%".$_GET['q']."%";
        $d = CAST_TO_INT($_GET["d"],0);
        $stmt->bindParam(':q', $q, PDO::PARAM_STR);
        $stmt->bindParam(':d', $d, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $r=array();
        foreach($rows as $row){

            if($row["is_privato"]==1){
                if($row["id_gas"]==_USER_ID_GAS){
                    $r[]=$row;
                }
            }else{
                $r[]=$row;
            }
        }
        $str =  json_encode($r);
        $str =  str_replace('\u0000', 'false', $str);
        $str =  str_replace('\u0001', 'true', $str);
        echo $str;
        die();
}

if(CAST_TO_INT($_GET["t"],0)==1){
        $sql = "SELECT  L.id_listini as id,
                        L.descrizione_listini as text,
                        L.is_privato,
                        U.fullname,
                        U.id_gas,
                        G.descrizione_gas,
                        D.descrizione_ditte,
                        DATE_FORMAT(L.data_valido, '%d/%m/%Y') as data_valido
                        FROM retegas_ditte D
                    inner join retegas_listini L on L.id_ditte = D.id_ditte
                    inner join maaking_users U on U.userid=L.id_utenti
                    inner join retegas_gas G on G.id_gas=U.id_gas
                    WHERE
                    CONCAT(D.descrizione_ditte,' ', L.descrizione_listini) LIKE :q
                    AND L.tipo_listino=0
                    AND L.data_valido>NOW()";
        $stmt = $db->prepare($sql);
        //$q = "%iri%";
        $q = "%".$_GET['q']."%";
        $stmt->bindParam(':q', $q, PDO::PARAM_STR);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $r=array();
        foreach($rows as $row){

            if($row["is_privato"]==1){
                if($row["id_gas"]==_USER_ID_GAS){
                    $r[]=$row;
                }
            }else{
                $r[]=$row;
            }
        }
        $str =  json_encode($r);
        $str =  str_replace('\u0000', 'false', $str);
        $str =  str_replace('\u0001', 'true', $str);
        echo $str;
        die();
}
//if(true){
if(CAST_TO_INT($_GET["t"],0)==2){
    $stmt = $db->prepare("SELECT * FROM retegas_options WHERE id_user='"._USER_ID."' AND chiave='_AIUTO_GESTIONE_LISTINO' AND valore_int=1;");

    $stmt->execute();
    $rows = $stmt->fetchAll();
    $r=array();
    foreach($rows as $rowL){
        $sql = "SELECT  L.id_listini as id,
                        L.descrizione_listini as text,
                        L.is_privato,
                        U.fullname,
                        U.id_gas,
                        G.descrizione_gas,
                        D.descrizione_ditte,
                        DATE_FORMAT(L.data_valido, '%d/%m/%Y') as data_valido
                        FROM retegas_ditte D
                    inner join retegas_listini L on L.id_ditte = D.id_ditte
                    inner join maaking_users U on U.userid=L.id_utenti
                    inner join retegas_gas G on G.id_gas=U.id_gas
                    WHERE
                    L.id_listini = ".$rowL["id_listino"]." AND
                    CONCAT(D.descrizione_ditte,' ', L.descrizione_listini) LIKE :q
                    AND L.tipo_listino=0
                    AND L.data_valido>NOW()";
        $stmt = $db->prepare($sql);
        //$q = "%iri%";
        $q = "%".$_GET['q']."%";
        $stmt->bindParam(':q', $q, PDO::PARAM_STR);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach($rows as $row){
                $r[]=$row;
        }
    } //AIUTANTE

    //MIEI
    $sql = "SELECT  L.id_listini as id,
                        L.descrizione_listini as text,
                        L.is_privato,
                        U.fullname,
                        U.id_gas,
                        G.descrizione_gas,
                        D.descrizione_ditte,
                        DATE_FORMAT(L.data_valido, '%d/%m/%Y') as data_valido
                        FROM retegas_ditte D
                    inner join retegas_listini L on L.id_ditte = D.id_ditte
                    inner join maaking_users U on U.userid=L.id_utenti
                    inner join retegas_gas G on G.id_gas=U.id_gas
                    WHERE
                    L.id_utenti = "._USER_ID." AND
                    CONCAT(D.descrizione_ditte,' ', L.descrizione_listini) LIKE :q
                    AND L.tipo_listino=0
                    AND L.data_valido>NOW()";
    $stmt = $db->prepare($sql);
    //$q = "%iri%";
    $q = "%".$_GET['q']."%";
    $stmt->bindParam(':q', $q, PDO::PARAM_STR);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $row){
            $r[]=$row;
    }

    $str =  json_encode($r);
    $str =  str_replace('\u0000', 'false', $str);
    $str =  str_replace('\u0001', 'true', $str);
    echo $str;
    die();


}
//if(false){
if(CAST_TO_INT($_GET["t"],0)==3){

    //$listini_mio_gas ="SELECT * FROM retegas_listini inner join maaking_users
    //                on userid=id_utenti WHERE id_gas='"._USER_ID_GAS."' ORDER BY data_valido DESC";


    //MIEI
    $sql = "SELECT  L.id_listini as id,
                        L.descrizione_listini as text,
                        L.is_privato,
                        U.fullname,
                        U.id_gas,
                        G.descrizione_gas,
                        D.descrizione_ditte,
                        DATE_FORMAT(L.data_valido, '%d/%m/%Y') as data_valido
                        FROM retegas_ditte D
                    inner join retegas_listini L on L.id_ditte = D.id_ditte
                    inner join maaking_users U on U.userid=L.id_utenti
                    inner join retegas_gas G on G.id_gas=U.id_gas
                    WHERE
                    U.id_gas='"._USER_ID_GAS."' AND
                    CONCAT(D.descrizione_ditte,' ', L.descrizione_listini) LIKE :q
                    AND L.tipo_listino=0
                    AND L.data_valido>NOW()";
    $stmt = $db->prepare($sql);
    //$q = "%iri%";
    $q = "%".$_GET['q']."%";
    $stmt->bindParam(':q', $q, PDO::PARAM_STR);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $row){
            $r[]=$row;
    }

    $str =  json_encode($r);
    $str =  str_replace('\u0000', 'false', $str);
    $str =  str_replace('\u0001', 'true', $str);
    echo $str;
    die();


}