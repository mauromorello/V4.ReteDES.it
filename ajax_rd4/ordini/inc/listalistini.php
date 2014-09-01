<?php
require_once("../../../lib/config.php");


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

