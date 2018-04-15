<?php
require_once("../../../lib/config.php");


$sql = "SELECT  U.userid as id,
                U.fullname as text,
                G.id_gas,
                G.descrizione_gas,
                G.id_des,
                D.des_descrizione
                FROM maaking_users U
                INNER JOIN retegas_gas G on G.id_gas=U.id_gas
                INNER JOIN retegas_des D on D.id_des=G.id_des
                WHERE
                U.fullname LIKE :q
                AND U.isactive=1";
$stmt = $db->prepare($sql);
//$q = "%iri%";
$q = "%".$_GET['q']."%";
$stmt->bindParam(':q', $q, PDO::PARAM_STR);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$r=array();
$id_des = CAST_TO_INT(_USER_ID_DES);
foreach($rows as $row){

    if($row["id_gas"]<>_USER_ID_GAS){
        if(_USER_PERMISSIONS & perm::puo_gestire_retegas){
            $r[]=$row;
        }else{
            if(_USER_PERMISSIONS & perm::puo_vedere_retegas){
                if($row["id_des"]==$id_des){
                    $r[]=$row;
                }
            }
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
