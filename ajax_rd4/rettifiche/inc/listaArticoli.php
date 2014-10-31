<?php
require_once("../../../lib/config.php");

$id_listino = CAST_TO_INT($_GET["id"]);

$sql = "SELECT  A.id_articoli as id, A.codice as text, A.descrizione_articoli as descr, CONCAT(ROUND(A.prezzo,2) , ' Eu x ' ,A.u_misura,' ',A.misura) as longo FROM retegas_articoli A WHERE id_listini=:id_listino AND CONCAT(A.descrizione_articoli,' ', A.codice) LIKE :q";
$stmt = $db->prepare($sql);
//$q = "%iri%";
$q = "%".$_GET['q']."%";
$stmt->bindParam(':id_listino', $id_listino, PDO::PARAM_INT);
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

