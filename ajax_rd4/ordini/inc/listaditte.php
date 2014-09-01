<?php
require_once("../../../lib/config.php");


$sql = "SELECT id_ditte as id, descrizione_ditte as text FROM retegas_ditte WHERE descrizione_ditte LIKE :q;";
$stmt = $db->prepare($sql);
//$q = "%iri%";
$q = "%".$_GET['q']."%";
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

