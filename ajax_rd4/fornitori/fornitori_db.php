<?php
//include the information needed for the connection to MySQL data base server.
// we store here username, database and password
require_once("inc/init.php");
$converter = new Encryption();


$page = $_POST['page']; // get the requested page
$limit = $_POST['rows']; // get how many rows we want to have into the grid
$sidx = $_POST['sidx']; // get index row - i.e. user click to sort
$sord = $_POST['sord']; // get the direction

if(!$sidx) $sidx =1;
$oper=$_POST['oper'];

if($oper=='excel'){
    $page=1;
    $limit=999999999;
}

// connect to the database

if($_POST["_search"]=="true"){

    $id_ditte = CAST_TO_INT($_POST["id_ditte"],0);
    if($id_ditte>0){
        $w .= " AND (D.id_ditte =".$id_ditte.") ";
    }
    $descrizione_ditte = trim(($_POST["descrizione_ditte"]));
    if(strlen($descrizione_ditte)>0){
        $w .= " AND (D.descrizione_ditte LIKE '%".$descrizione_ditte."%') ";
    }
    $indirizzo = trim(($_POST["indirizzo"]));
    if(strlen($indirizzo)>0){
        $w .= " AND (D.indirizzo LIKE '%".$indirizzo."%') ";
    }
    $telefono = trim(($_POST["telefono"]));
    if(strlen($telefono)>0){
        $w .= " AND (D.telefono LIKE '%".$telefono."%') ";
    }
    $mail_ditte = trim(($_POST["mail_ditte"]));
    if(strlen($mail_ditte)>0){
        $w .= " AND (D.mail_ditte LIKE '%".$mail_ditte."%') ";
    }
    $fullname= trim(($_POST["fullname"]));
    if(strlen($fullname)>0){
        $w .= " AND (U.fullname LIKE '%".$fullname."%') ";
    }
    $descrizione_gas= trim(($_POST["descrizione_gas"]));
    if(strlen($descrizione_gas)>0){
        $w .= " AND (G.descrizione_gas LIKE '%".$descrizione_gas."%') ";
    }
    $tags= trim(($_POST["tags"]));
    if(strlen($tags)>0){
        $w .= " AND (D.tag_ditte LIKE '%".$tags."%') ";
    }

    //OLD
    $id_ordine = trim((int)($_POST["id_ordine"]));
    if($id_ordine>0){
        $w .= " AND (R.id_ordine = '".$id_ordine."') ";
    }
    $ditta = trim(($_POST["ditta"]));
    if(strlen($ditta)>0){
        $w .= " AND ((D.descrizione_ditte LIKE '%".$ditta."%') OR (R.id_ditta='".$ditta."')) ";
    }
    $tipo = trim((int)($_POST["tipo"]));
    if($tipo>0){
        $w .= " AND (R.tipo_movimento = '".$tipo."') ";
    }
    $registrato = trim($_POST["registrato"]);
    if(strlen($registrato)>0){
        $w .= " AND (R.registrato = '".$registrato."') ";
    }
}
if(CAST_TO_STRING($_POST["userid"])<>""){
    $userid=CAST_TO_INT($converter->decode(CAST_TO_STRING($_POST["userid"])));
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

$q =    "SELECT count(*) as count
        FROM retegas_ditte D
        INNER JOIN maaking_users U on U.userid=D.id_proponente
        INNER JOIN retegas_gas G on G.id_gas=U.id_gas
        $w
        AND D.id_ditte>0";
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


$SQL = "SELECT  D.id_ditte AS id,
                D.mail_ditte,
                D.descrizione_ditte,
                (SELECT COUNT(*) FROM retegas_listini L where L.id_ditte=D.id_ditte AND data_valido>NOW() ) as listini_attivi,
                D.indirizzo,
                D.telefono,
                U.fullname,
                G.descrizione_gas,
                D.tag_ditte
        FROM retegas_ditte D
        INNER JOIN maaking_users U on U.userid=D.id_proponente
        INNER JOIN retegas_gas G on G.id_gas=U.id_gas
        $w
        AND D.id_ditte>0
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

    $id=$row["id"];
    $descrizione_ditte = $row["descrizione_ditte"];
    $indirizzo = $row["indirizzo"];
    $telefono = $row["telefono"];
    $mail_ditte = $row["mail_ditte"];
    $fullname = $row["fullname"];
    $descrizione_gas = $row["descrizione_gas"];
    $listini_attivi= $row["listini_attivi"];
    $tags = $row["tag_ditte"];

    $riga = array(  $id,
                    $descrizione_ditte,
                    $listini_attivi,
                    $indirizzo,
                    $telefono,
                    $mail_ditte,
                    $tags
                    //$fullname,
                    //$descrizione_gas
                    );

    if($oper<>'excel'){
        $responce->rows[$i]['id']=$id;
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