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
          $row["descrizione_articoli"] = iconv('UTF-8', 'UTF-8//IGNORE', $row["descrizione_articoli"]);
          $r[]=$row;
}

//$stripped_of_invalid_utf8_chars_string = iconv('UTF-8', 'UTF-8//IGNORE', $r);
//if ($stripped_of_invalid_utf8_chars_string !== $r) {
    // one or more chars were invalid, and so they were stripped out.
    // if you need to know where in the string the first stripped character was,
    // then see http://stackoverflow.com/questions/7475437/find-first-character-that-is-different-between-two-strings
//}
//$str = json_encode($stripped_of_invalid_utf8_chars_string);


$str =  json_encode($r);
$str =  str_replace('\u0000', 'false', $str);
$str =  str_replace('\u0001', 'true', $str);

echo $str;
die();

