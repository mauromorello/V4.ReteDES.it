<?php
//include the information needed for the connection to MySQL data base server.
// we store here username, database and password
require_once("inc/init.php");
$converter = new Encryption();


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
if($sidx=="data"){$sidx="R.data_movimento";}
$sord = $_GET['sord']; // get the direction
if(!$sidx) $sidx =1;
$oper=$_GET['oper'];
if($oper=='excel'){
    $page=1;
    $limit=999999999;
}

// connect to the database

if($_GET["_search"]=="true"){

    $segno = trim(($_GET["segno"]));
    if($segno<>""){
        $w .= " AND (R.segno = '".$segno."') ";
    }
    $fullname = trim(($_GET["fullname"]));
    if(strlen($fullname)>0){
        $w .= " AND (U.fullname LIKE '%".$fullname."%') ";
    }
    $id_ordine = trim((int)($_GET["id_ordine"]));
    if($id_ordine>0){
        $w .= " AND (R.id_ordine = '".$id_ordine."') ";
    }
    $ditta = trim(($_GET["ditta"]));
    if(strlen($ditta)>0){
        $w .= " AND ((D.descrizione_ditte LIKE '%".$ditta."%') OR (R.id_ditta='".$ditta."')) ";
    }
    $descrizione_movimento = trim(($_GET["descrizione_movimento"]));
    if(strlen($descrizione_movimento)>0){
        $w .= " AND (R.descrizione_movimento LIKE '%".$descrizione_movimento."%') ";
    }
    $tipo = trim((int)($_GET["tipo"]));
    if($tipo>0){
        $w .= " AND (R.tipo_movimento = '".$tipo."') ";
    }
    $registrato = trim($_GET["registrato"]);
    if(strlen($registrato)>0){
        $w .= " AND (R.registrato = '".$registrato."') ";
    }
}
if(CAST_TO_STRING($_GET["userid"])<>""){
    $userid=CAST_TO_INT($converter->decode(CAST_TO_STRING($_GET["userid"])));
    if($userid>0){
        $w .= " AND (R.id_utente = '".$userid."') ";
    }
}

if($w<>""){

    $w = ltrim($w," AND");
    $w = " WHERE ".$w;
}else{
    $w = " WHERE 1=1 ";
}
$q =     "SELECT count(*) as count
        FROM retegas_cassa_utenti R
        INNER JOIN maaking_users U on U.userid=R.id_utente
        LEFT JOIN retegas_ditte D on D.id_ditte=R.id_ditta
        LEFT JOIN maaking_users U2 on U2.userid=R.id_cassiere
        INNER JOIN retegas_options O ON (O.chiave='_USER_USA_CASSA' AND O.id_user=R.id_utente AND O.valore_text='SI')
        $w AND U.id_gas = "._USER_ID_GAS.";";
//echo $q; die();

//$result = mysql_query($q);
//$row = mysql_fetch_array($result,MYSQL_ASSOC);
//$count = $row['count'];

$stmt = $db->prepare($q);
$stmt->execute();
$row = $stmt->fetch();
$count = $row['count'];




if( $count >0 ) {
    $total_pages = ceil($count/$limit);
} else {
    $total_pages = 0;
}
if ($page > $total_pages) $page=$total_pages;
$start = $limit*$page - $limit; // do not put $limit*($page - 1)
if($start<0){$start=0;}

$SQL = "SELECT R.id_cassa_utenti AS id, U.fullname, DATE_FORMAT(R.data_movimento,'%d/%m/%y %h:%i') as data, tipo_movimento as tipo, segno, CONCAT(segno, REPLACE(CAST(ABS(importo) AS CHAR), '.', '"._USER_CARATTERE_DECIMALE."') ) as importo, R.id_ordine, R.id_ditta, D.descrizione_ditte, U2.fullname as cassiere, DATE_FORMAT(R.data_registrato,'%d/%m/%y %h:%i') as data_registrato, registrato, descrizione_movimento
        FROM retegas_cassa_utenti R
        INNER JOIN maaking_users U on U.userid=R.id_utente
        LEFT JOIN retegas_ditte D on D.id_ditte=R.id_ditta
        LEFT JOIN maaking_users U2 on U2.userid=R.id_cassiere
        INNER JOIN retegas_options O ON (O.chiave='_USER_USA_CASSA' AND O.id_user=R.id_utente AND O.valore_text='SI')
        $w
        AND U.id_gas = "._USER_ID_GAS."
        ORDER BY $sidx $sord LIMIT $start , $limit";
//echo $SQL; die();


//$result = mysql_query( $SQL ) or die("Couldn t execute query.".mysql_error());

$stmt = $db->prepare($SQL);
$stmt->execute();
$rows = $stmt->fetchAll();


$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i=0;

if($oper=='excel'){
          header('Content-Type: text/csv; charset=utf-8');
          header('Content-Disposition: attachment; filename='.date('Ymdhis').'.csv');
          $output = fopen('php://output', 'w');
          //fputcsv($output, array('ID ordine', 'Data chiusura', 'Descrizione','id_utente','utente', 'id_ditta','ditta','id_gas','descrizione gas','codice','descrizione articolo','qta arr','prezzo','um'),_USER_CSV_SEPARATOR);
}

//while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
foreach($rows as $row){
    $ditta = $row["id_ditta"]." ".$row["descrizione_ditte"];
    $riga = array($row["id"],$row["fullname"],$row["data"],$row["tipo"],$row["segno"],$row["importo"],$row["descrizione_movimento"],$row["id_ordine"],$ditta, $row["cassiere"],$row["data_registrato"],$row["registrato"]);
    if($oper<>'excel'){
        $responce->rows[$i]['id']=$row["id"];
        $responce->rows[$i]['cell']=$riga;
    }else{
        fputcsv($output,$riga,_USER_CSV_SEPARATOR);
    }
    $i++;
}

if($oper=='excel'){
    fclose($output);
}else{
    echo json_encode($responce);
}


?>