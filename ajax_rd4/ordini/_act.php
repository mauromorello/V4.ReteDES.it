<?php
require_once("inc/init.php");

if(!empty($_POST["act"])){
    switch ($_POST["act"]) {
        case "gas_partecipa":
        if (!posso_gestire_ordine((int)$_POST['id_ordine'])){
            echo json_encode(array("result"=>"KO", "msg"=>"Non hai i permessi necessari" ));
            die();
         }

         if($_POST["action"]=="insert"){
             $stmt = $db->prepare("SELECT * FROM retegas_gas WHERE id_gas=:id_gas");
             $stmt->bindParam(':id_gas', $_POST['value'], PDO::PARAM_INT);
             $stmt->execute();
             $row_gas= $stmt->fetch(PDO::FETCH_ASSOC);

             $stmt = $db->prepare("INSERT INTO retegas_referenze (id_ordine_referenze, id_utente_referenze, id_gas_referenze, note_referenza, maggiorazione_percentuale_referenza) VALUES (:id_ordine, '0', :id_gas , '".$row_gas["comunicazione_referenti"]."', '".$row_gas["maggiorazione_ordini"]."');");
             $stmt->bindParam(':id_ordine', $_POST['id_ordine'], PDO::PARAM_INT);
             $stmt->bindParam(':id_gas', $_POST['value'], PDO::PARAM_INT);
             $stmt->execute();

             if($stmt->rowCount()<>1){
                    $res=array("result"=>"KO", "msg"=>"Errore." );
             }else{
                    $res=array("result"=>"OK", "msg"=>"Condiviso con ".$row_gas["descrizione_gas"]);
             }
         }else{
             $stmt = $db->prepare("DELETE FROM retegas_referenze WHERE id_ordine_referenze=:id_ordine AND id_gas_referenze =:id_gas LIMIT 1;");
             $stmt->bindParam(':id_ordine', $_POST['id_ordine'], PDO::PARAM_INT);
             $stmt->bindParam(':id_gas', $_POST['value'], PDO::PARAM_INT);
             $stmt->execute();
             if($stmt->rowCount()<>1){
                    $res=array("result"=>"KO", "msg"=>"Errore." );
             }else{
                    $res=array("result"=>"OK", "msg"=>"Condivisione tolta");
             }
         }
        echo json_encode($res);
     break;

        case "start_ordine":
        if (!posso_gestire_ordine($_POST['pk'])){
            echo json_encode(array("result"=>"KO", "msg"=>"Non hai i permessi necessari" ));
            die();
         }


         $stmt = $db->prepare("UPDATE retegas_ordini SET data_apertura = NOW()
                             WHERE id_ordini=:id_ordini LIMIT 1;");

         $stmt->bindParam(':id_ordini', $_POST['pk'], PDO::PARAM_INT);

         $stmt->execute();
         if($stmt->rowCount()<>1){
                $res=array("result"=>"KO", "msg"=>"Errore." );
         }else{
                $res=array("result"=>"OK", "msg"=>"L'ordine si aprirà il prima possibile." );
         }
        echo json_encode($res);
     break;
     case "end_ordine":
        if (!posso_gestire_ordine($_POST['pk'])){
            echo json_encode(array("result"=>"KO", "msg"=>"Non hai i permessi necessari" ));
            die();
         }


         $stmt = $db->prepare("UPDATE retegas_ordini SET data_chiusura = NOW()
                             WHERE id_ordini=:id_ordini LIMIT 1;");

         $stmt->bindParam(':id_ordini', $_POST['pk'], PDO::PARAM_INT);

         $stmt->execute();
         if($stmt->rowCount()<>1){
                $res=array("result"=>"KO", "msg"=>"Errore." );
         }else{
                $res=array("result"=>"OK", "msg"=>"L'ordine si chiuderà il prima possibile." );
         }
        echo json_encode($res);
     break;
     case "nuovo_ordine":

        //CONTROLLI
        if (!(_USER_PERMISSIONS & perm::puo_creare_ordini)){
             $res=array("result"=>"KO", "msg"=>"Non puoi creare ordini");
             echo json_encode($res);
             die();
        }


        //CHIUSURA
        $data_chiusura = (int)$_POST["quantigiorni"];
        $data_chiusura = date("d/m/Y",gas_mktime(date("d/m/Y")) + (60 * 60 * 24 * $data_chiusura));
        $data_chiusura = $data_chiusura ." 22:00";

        //IDLISTINO
        $idlistino = (int)$_POST["idlistino"];
        if($idlistino<1){
            $res=array("result"=>"KO", "msg"=>"Errore id listino");
             echo json_encode($res);
             die();
        }
        //POSTICIPO DI DUE ORE L'APERTURA
        $date_now  = date( "d/m/Y H:i" );
        $time_now  = time( $date_now );
        $time_next = $time_now + 2 * 60 * 60;
        $date_next = date( "d/m/Y H:i", $time_next);

        $dataapertura = conv_date_to_db($date_next);
        $datachiusura = conv_date_to_db($data_chiusura);

        // L'opzione SOLO CASSATI E' PRESA DAL DEFAULT DELLE OPZIONI CASSA
        //$data_20=read_option_gas_text(_USER_ID_GAS,"_GAS_CASSA_DEFAULT_SOLO_CASSATI");
        if(_GAS_CASSA_DEFAULT_SOLO_CASSATI){
            $solocassati="SI";
        }else{
            $solocassati="NO";
        }


        //esiste
        $stmt = $db->prepare("INSERT INTO retegas_ordini
            (id_listini,
            id_utente,
            descrizione_ordini,
            data_chiusura,
            data_merce,
            costo_trasporto,
            costo_gestione,
            min_articoli,
            min_scatola,
            privato,
            data_apertura,
            id_stato,
            senza_prezzo,
            mail_level,
            note_ordini,
            solo_cassati)
            VALUES
            (:idlistino,
            '"._USER_ID."',
            :nomeordine,
            :datachiusura,
            '',
            '0',
            '0',
            '0',
            '0',
            '0',
            :dataapertura,
            '1',
            '0',
            '1',
            :noteordine,
            '$solocassati');");
        $stmt->bindParam(':idlistino', $_POST['idlistino'], PDO::PARAM_INT);
        $stmt->bindParam(':nomeordine', $_POST['nomeordine'], PDO::PARAM_STR);
        $stmt->bindParam(':dataapertura', $dataapertura, PDO::PARAM_STR);
        $stmt->bindParam(':datachiusura', $datachiusura, PDO::PARAM_STR);
        $stmt->bindParam(':noteordine', $_POST['noteordine'], PDO::PARAM_STR);
        $stmt->execute();
        $newId = $db->lastInsertId();
        if($stmt->rowCount()==1){;


            $stmt = $db->prepare("SELECT * FROM retegas_gas WHERE id_gas=:id_gas");
            $stmt->bindValue(':id_gas', _USER_ID_GAS, PDO::PARAM_INT);
            $stmt->execute();
            $row_gas = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $db->prepare("INSERT INTO retegas_referenze (id_ordine_referenze,
                id_utente_referenze,
                id_gas_referenze,
                note_referenza,
                maggiorazione_referenza,
                maggiorazione_percentuale_referenza)
                VALUES
                ('$newId',
                '"._USER_ID."',
                '"._USER_ID_GAS."',
                '".$row_gas["comunicazione_referenti"]."',
                '0',
                '".$row_gas["maggiorazione_ordini"]."');");
           $stmt->execute();
           $res=array("result"=>"OK", "msg"=>"Ordine ".$_POST['nomeordine']." inserito correttamente", "id"=>"{$newId}" );


        }else{
            $res=array("result"=>"KO", "msg"=>"Errore nel DB... RC: ".$stmt->rowCount() );
        }

        echo json_encode($res);
     break;


     default:
        $res=array("result"=>"KO", "msg"=>"Comando non riconosciuto" );
        echo json_encode($res);
     break;
    }
}
if(!empty($_POST["name"])){
     switch ($_POST["name"]) {
     case "descrizione_ordini":
         if (!posso_gestire_ordine($_POST['pk'])){
            echo json_encode(array("result"=>"KO", "msg"=>"Non hai i permessi necessari" ));
            die();
         }

         if(trim($_POST['value'])==""){
            echo json_encode(array("result"=>"KO", "msg"=>"Devi immettere un titolo" ));
            die();
         }
         $stmt = $db->prepare("UPDATE retegas_ordini SET descrizione_ordini = :descrizione_ordini
                             WHERE id_ordini=:id_ordini LIMIT 1;");

        $stmt->bindParam(':descrizione_ordini', $_POST['value'], PDO::PARAM_STR);
        $stmt->bindParam(':id_ordini', $_POST['pk'], PDO::PARAM_INT);

        $stmt->execute();
        if($stmt->rowCount()<>1){
            $res=array("result"=>"KO", "msg"=>"Errore." );
        }else{
            $res=array("result"=>"OK", "msg"=>"OK" );
        }
        echo json_encode($res);
     break;
     case "costo_gestione":
         if (!posso_gestire_ordine($_POST['pk'])){
            echo json_encode(array("result"=>"KO", "msg"=>"Non hai i permessi necessari" ));
            die();
         }

         $costo_gestione = str_replace(',', '.', $_POST['value']);
         $costo_gestione = CAST_TO_FLOAT($costo_gestione,0);

         if($costo_gestione<0){
            echo json_encode(array("result"=>"KO", "msg"=>"Devi immettere un valore superiore a zero." ));
            die();
         }
         $stmt = $db->prepare("UPDATE retegas_ordini SET costo_gestione = :costo_gestione
                             WHERE id_ordini=:id_ordini LIMIT 1;");

        $stmt->bindParam(':costo_gestione', $costo_gestione, PDO::PARAM_STR);
        $stmt->bindParam(':id_ordini', $_POST['pk'], PDO::PARAM_INT);

        $stmt->execute();
        if($stmt->rowCount()<>1){
            $res=array("result"=>"KO", "msg"=>"Errore." );
        }else{
            $res=array("result"=>"OK", "msg"=>"OK" );
        }
        echo json_encode($res);
     break;
     case "costo_trasporto":
         if (!posso_gestire_ordine($_POST['pk'])){
            echo json_encode(array("result"=>"KO", "msg"=>"Non hai i permessi necessari" ));
            die();
         }
         $costo_trasporto= str_replace(',', '.', $_POST['value']);
         $costo_trasporto = CAST_TO_FLOAT($costo_trasporto,0);

         if($costo_trasporto<0){
            echo json_encode(array("result"=>"KO", "msg"=>"Devi immettere un valore superiore a zero." ));
            die();
         }
         $stmt = $db->prepare("UPDATE retegas_ordini SET costo_trasporto = :costo_trasporto
                             WHERE id_ordini=:id_ordini LIMIT 1;");

        $stmt->bindParam(':costo_trasporto', $costo_trasporto, PDO::PARAM_STR);
        $stmt->bindParam(':id_ordini', $_POST['pk'], PDO::PARAM_INT);

        $stmt->execute();
        if($stmt->rowCount()<>1){
            $res=array("result"=>"KO", "msg"=>"Errore." );
        }else{
            $res=array("result"=>"OK", "msg"=>"OK" );
        }
        echo json_encode($res);
     break;
     case "data_apertura":
        if (!posso_gestire_ordine($_POST['pk'])){
            echo json_encode(array("result"=>"KO", "msg"=>"Non hai i permessi necessari" ));
            die();
         }

         if(trim($_POST['value'])==""){
            echo json_encode(array("result"=>"KO", "msg"=>"...azz!" ));
            die();
         }

         $stmt = $db->prepare("SELECT
                                DATE_FORMAT(O.data_apertura,'%d/%m/%Y %H:%i') as data_apertura,
                                DATE_FORMAT(O.data_chiusura,'%d/%m/%Y %H:%i') as data_chiusura
                                FROM retegas_ordini O WHERE id_ordini=:id LIMIT 1;");
         $stmt->bindValue(':id', $_POST['pk'], PDO::PARAM_INT);
         $stmt->execute();
         $row = $stmt->fetch(PDO::FETCH_ASSOC);

         $data_apertura = strtotime(str_replace('/', '-', $_POST['value']));
         $data_chiusura = strtotime(str_replace('/', '-', $row["data_chiusura"]));
         $data_now = strtotime(date("d-m-Y H:i"));

         if($data_apertura<=$data_now){
            echo json_encode(array("result"=>"KO", "msg"=>"La data di apertura non può essere nel passato." ));
            die();
         }

         if($data_apertura>=$data_chiusura){
            echo json_encode(array("result"=>"KO", "msg"=>"La data di apertura non può essere successiva quella di chiusura." ));
            die();
         }


         $stmt = $db->prepare("UPDATE retegas_ordini SET data_apertura = :data_apertura
                             WHERE id_ordini=:id_ordini LIMIT 1;");

         $stmt->bindParam(':data_apertura', conv_date_to_db($_POST['value']), PDO::PARAM_STR);
         $stmt->bindParam(':id_ordini', $_POST['pk'], PDO::PARAM_INT);

         $stmt->execute();
        if($stmt->rowCount()<>1){
            $res=array("result"=>"KO", "msg"=>"Errore." );
        }else{
            $res=array("result"=>"OK", "msg"=>"OK" );
        }
        echo json_encode($res);
     break;
     case "data_chiusura":
        if (!posso_gestire_ordine($_POST['pk'])){
            echo json_encode(array("result"=>"KO", "msg"=>"Non hai i permessi necessari" ));
            die();
         }

         if(trim($_POST['value'])==""){
            echo json_encode(array("result"=>"KO", "msg"=>"...azz!" ));
            die();
         }

         $stmt = $db->prepare("SELECT
                                DATE_FORMAT(O.data_apertura,'%d/%m/%Y %H:%i') as data_apertura,
                                DATE_FORMAT(O.data_chiusura,'%d/%m/%Y %H:%i') as data_chiusura
                                FROM retegas_ordini O WHERE id_ordini=:id LIMIT 1;");
         $stmt->bindValue(':id', $_POST['pk'], PDO::PARAM_INT);
         $stmt->execute();
         $row = $stmt->fetch(PDO::FETCH_ASSOC);

         $data_apertura = strtotime(str_replace('/', '-', $row["data_apertura"]));
         $data_chiusura = strtotime(str_replace('/', '-', $_POST['value']));
         $data_now = strtotime(date("d-m-Y H:i"));

         if($data_chiusura<=$data_now){
            echo json_encode(array("result"=>"KO", "msg"=>"La data di chiusura non può essere nel passato." ));
            die();
         }

         if($data_apertura>=$data_chiusura){
            echo json_encode(array("result"=>"KO", "msg"=>"La data di chiusura non può essere antecedente a quella di apertura." ));
            die();
         }


         $stmt = $db->prepare("UPDATE retegas_ordini SET data_chiusura = :data_chiusura
                             WHERE id_ordini=:id_ordini LIMIT 1;");

         $stmt->bindParam(':data_chiusura', conv_date_to_db($_POST['value']), PDO::PARAM_STR);
         $stmt->bindParam(':id_ordini', $_POST['pk'], PDO::PARAM_INT);

         $stmt->execute();
        if($stmt->rowCount()<>1){
            $res=array("result"=>"KO", "msg"=>"Errore." );
        }else{
            $res=array("result"=>"OK", "msg"=>"OK" );
        }
        echo json_encode($res);
     break;

        default:
            $res=array("result"=>"KO", "msg"=>"Comando non riconosciuto" );
            echo json_encode($res);
         break;
     }

}