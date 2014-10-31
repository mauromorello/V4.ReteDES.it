<?php
require_once("../../../lib/config.php");

$id_ordine = CAST_TO_INT($_GET["id"]);

$sql = "SELECT  maaking_users.userid as id,
                maaking_users.fullname as text,
                retegas_gas.descrizione_gas as gas
                FROM
                retegas_ordini
                Inner Join retegas_referenze ON retegas_ordini.id_ordini = retegas_referenze.id_ordine_referenze
                Inner Join maaking_users ON retegas_referenze.id_gas_referenze = maaking_users.id_gas
                Inner Join retegas_gas ON retegas_referenze.id_gas_referenze = retegas_gas.id_gas
                WHERE
                retegas_ordini.id_ordini =:id_ordine
                AND
                maaking_users.isactive = 1
                AND
                CONCAT(maaking_users.fullname,' ', retegas_gas.descrizione_gas) LIKE :q";
$stmt = $db->prepare($sql);
//$q = "%iri%";
$q = "%".$_GET['q']."%";
$stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
$stmt->bindParam(':q', $q, PDO::PARAM_STR);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$r=array();
foreach($rows as $row){

        $r[]=$row;

}

$str =  json_encode($r);
$str =  str_replace('\u0000', 'false', $str);
$str =  str_replace('\u0001', 'true', $str);

echo $str;
die();

