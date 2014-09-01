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
?>
