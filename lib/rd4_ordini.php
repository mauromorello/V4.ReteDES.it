<?php
  function posso_gestire_ordine($id_ordine){
    global $db;

    $stmt = $db->prepare("SELECT U.id_gas, O.id_utente FROM retegas_ordini O inner join maaking_users U on U.userid=O.id_utente WHERE id_ordini=:id LIMIT 1;");
    $stmt->bindValue(':id', $id_ordine, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($stmt->rowCount()<>1){
        // ordine inesistente
        return false;
        die();
    }

    if($row["id_utente"]==_USER_ID){
        //sono il gestore
        return true;
        die();
    }

    if($row["id_gas"]==_USER_ID_GAS){
        //sono del gas
        if(_USER_PERMISSIONS & perm::puo_vedere_tutti_ordini){
            //e posso vedere tutti gli ordini
            return true;
            die();
        }

    }

    $stmt = $db->prepare("SELECT * FROM retegas_options WHERE
                            id_ordine = :id
                            AND
                            id_user = '"._USER_ID."'
                            AND
                            chiave = '_REFERENTE_EXTRA';");
    $stmt->bindValue(':id', $id_ordine, PDO::PARAM_INT);
    $stmt->execute();
    if ($stmt->rowCount()>0){
        //sono un referente extra
        return true;
        die();
    }

  return false;
  }

function rettifica_amici($key,$nq){

    global $db;
    //Conto le righe interessate
    $stmt = $db->prepare("SELECT COUNT(*) as conto FROM retegas_distribuzione_spesa WHERE id_riga_dettaglio_ordine=:key");
    $stmt->bindValue(':key', $key, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalrows = $row["conto"];

    // Ho la lista degli amici riferita all'articolo KEY
    $qry ="SELECT
    id_distribuzione,
    id_riga_dettaglio_ordine,
    qta_ord,
    qta_arr,
    id_amico
    FROM
    retegas_distribuzione_spesa
    WHERE
    id_riga_dettaglio_ordine = :key
    ORDER BY
    id_amico DESC";

    $rimasto = CAST_TO_FLOAT($nq,0);
    $i = 0;
    // Adesso la popolo con la nuova quantità partendo dall'ultima riga immessa;
    // in realtà cancellando e ripopolando tutto ho sempre lo stesso utente penalizzato;
    $stmt = $db->prepare($qry);
    $stmt->bindValue(':key', $key, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {

        $i++;
        $l .= "------------->Ciclo n.$i<br>";

        $a = round(($rimasto - $row['qta_ord']),4);
        $id_q = $row['id_distribuzione'];

        if($a>0){
            $l .= "------------->Rimasto - Qord > 0 <br>";
            $q_a = $row['qta_ord'];
            $rimasto=$a;

            // se è l'ultima riga allora aggiungo un po' di roba
            if($i==$totalrows){

                 $q_a = round(($rimasto + $row['qta_ord']),4);
                 $rimasto=0;
                 $l .= "------------->Ultima riga; qa= (rimasto + qord) $q_a <br>";
            }

        }else{

            $l .= "------------->Rimasto - Qord = 0 <br>";
            $q_a = round($rimasto,4);
            $rimasto=0;
        }


    $l .= "------------->INSERISCO $q_a in $id_q<br>";
    // update


    $query2 = "UPDATE retegas_distribuzione_spesa
                SET qta_arr = '$q_a',
                data_ins = NOW()
                WHERE (id_distribuzione='$id_q');";
     $stmt = $db->prepare($query2);
     $stmt->execute();
     $l .= "------------->Fine riga<br><br>";

    // CICLO DI UPDATE
    }
    //echo $l;
    return;
}

?>
