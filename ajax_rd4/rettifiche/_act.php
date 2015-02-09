<?php
require_once("inc/init.php");
$converter = new Encryption;
if(isset($_POST["act"])){
    switch ($_POST["act"]) {
        /*
        /* -------------------------PERMESSi SUPERPOTERI
        */
         case "add_nuovo_articolo":
            $id_ordine = CAST_TO_INT($_POST["id_ordine"],0);

            if (!posso_gestire_ordine($id_ordine)){
                $res=array("result"=>"KO", "msg"=>"Non ho i permessi per questa operazione;" );
                echo json_encode($res);
                break;
                die();
            }

            $qta = CAST_TO_FLOAT($_POST["quantita_nuovo_articolo"]);
            if($qta<0){
                $res=array("result"=>"KO", "msg"=>"La quantità non può essere negativa;" );
                echo json_encode($res);
                break;
                die();
            }

            $id_utente = CAST_TO_INT($_POST["idUtente"],0);
            $stmt = $db->prepare("SELECT * from maaking_users WHERE userid=:id_utente");
            $stmt->bindParam(':id_utente', $id_utente, PDO::PARAM_INT);
            $stmt->execute();
            $rowU = $stmt->fetch(PDO::FETCH_ASSOC);

            $id_articolo=CAST_TO_INT($_POST["idArticolo"]);
            $stmt = $db->prepare("SELECT * from retegas_articoli WHERE id_articoli=:id_articolo");
            $stmt->bindParam(':id_articolo', $id_articolo, PDO::PARAM_INT);
            $stmt->execute();
            $rowA = $stmt->fetch(PDO::FETCH_ASSOC);

            $id_listino = $rowA["id_listini"];
            $stmt = $db->prepare("SELECT * from retegas_ordini WHERE id_ordini=:id_ordine");
            $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $id_listino_o = $row["id_listini"];

            if($id_listino_o<>$id_listino){
                $res=array("result"=>"KO", "msg"=>"Non è un articolo del listino usato per questo ordine" );
                echo json_encode($res);
                break;
                die();
            }

            $stmt = $db->prepare("INSERT INTO retegas_dettaglio_ordini
                                    (id_utenti,
                                    id_articoli,
                                    id_stati,
                                    data_inserimento,
                                    data_convalida,
                                    qta_ord,
                                    id_amico,
                                    id_ordine,
                                    qta_conf,
                                    qta_arr,
                                    prz_dett,
                                    prz_dett_arr,
                                    art_codice,
                                    art_desc,
                                    art_um,
                                    check_code)
                                    VALUES
                                    (:id_utenti,
                                    :id_articoli,
                                    0,
                                    NOW(),
                                    NOW(),
                                    :qta_ord,
                                    0,
                                    :id_ordini,
                                    1,
                                    :qta_arr,
                                    :prz_dett,
                                    :prz_dett_arr,
                                    :art_codice,
                                    :art_desc,
                                    :art_um,
                                    0)");
            $rowA_um = $rowA["u_misura"]." ".$rowA["misura"];
            $stmt->bindParam(':id_articoli', $id_articolo, PDO::PARAM_INT);
            $stmt->bindParam(':id_ordini', $id_ordine, PDO::PARAM_INT);
            $stmt->bindParam(':id_utenti', $id_utente, PDO::PARAM_INT);
            $stmt->bindParam(':qta_ord', $qta, PDO::PARAM_STR);
            $stmt->bindParam(':qta_arr', $qta, PDO::PARAM_STR);
            $stmt->bindParam(':prz_dett', $rowA["prezzo"], PDO::PARAM_STR);
            $stmt->bindParam(':prz_dett_arr', $rowA["prezzo"], PDO::PARAM_STR);
            $stmt->bindParam(':art_codice', $rowA["codice"], PDO::PARAM_STR);
            $stmt->bindParam(':art_desc', $rowA["descrizione_articoli"], PDO::PARAM_STR);
            $stmt->bindParam(':art_um', $rowA_um , PDO::PARAM_STR);
            $stmt->execute();
            if($stmt->rowCount()==1){
                $id = $db->lastInsertId();
                $stmt = $db->prepare("INSERT INTO retegas_distribuzione_spesa
                                    (id_riga_dettaglio_ordine,
                                    id_amico,
                                    qta_ord,
                                    qta_arr,
                                    data_ins,
                                    id_articoli,
                                    id_user,
                                    id_ordine)
                                    VALUES
                                    (:id,
                                    0,
                                    1,
                                    1,
                                    NOW(),
                                    0,
                                    :id_user,
                                    :id_ordine)");
                $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->bindParam(':id_user', $id_utente, PDO::PARAM_INT);
                $stmt->execute();
                if($stmt->rowCount()==1){
                    $desc = 'Utente <b>'.$rowU["fullname"].'</b></br>Quantità aggiunta: <b>'.$qta.'</b><br>Valore attuale suo ordine: <b>'.VA_ORDINE_USER($id_ordine,$id_utente).' Eu.</b>';
                    $title = '<b>'.$rowA["codice"].'</b> '.$rowA["descrizione_articoli"];
                    $res=array("result"=>"OK", "msg"=>"Rettifica effettuata", "desc"=>$desc, "title"=>$title );
                }else{
                    $res=array("result"=>"KO", "msg"=>"Errore nella tabella distribuzione" );
                }

            }else{
                $res=array("result"=>"KO", "msg"=>"Errore nella tabella dettaglio" );
            }


            echo json_encode($res);
        break;
        die();


        break;
        case "schedina riepilogo":
        $id_ordine = CAST_TO_INT($_POST["id_ordine"],0);

        $h= '   <p>Totale articoli:</p>
                <h1  class="font-lg  text-success text-right"><strong>'. VA_ORDINE_SOLO_NETTO($id_ordine).'</strong> €</h1>
                <p>Rettifiche:</p>
                <h1  class="font-lg  text-danger text-right"><strong>'. VA_ORDINE_SOLO_RETT($id_ordine) .'</strong> €</h1>
                <p>Totale ordine:</p>
                <h1  class="font-xl  text-success text-right"><strong>'. (VA_ORDINE_SOLO_RETT($id_ordine) + VA_ORDINE_SOLO_NETTO($id_ordine)).'</strong> €</h1>
                <hr>
                <p>Totale articoli tuo GAS:</p>
                <h1  class="font-lg  text-success text-right"><strong>'. VA_ORDINE_GAS_SOLO_NETTO($id_ordine,_USER_ID_GAS).'</strong> €</h1>
                <p>Rettifiche tuo GAS:</p>
                <h1  class="font-lg  text-danger text-right"><strong>'. VA_ORDINE_GAS_SOLO_RETT($id_ordine,_USER_ID_GAS).'</strong> €</h1>
                <p>Extra tuo GAS:</p>
                <h1  class="font-lg  text-danger text-right"><strong>'. VA_ORDINE_GAS_SOLO_EXTRA_GAS($id_ordine,_USER_ID_GAS).'</strong> €</h1>
                <p>Totale ordine tuo GAS:</p>
                <h1  class="font-xl  text-success text-right"><strong>'. VA_ORDINE_GAS($id_ordine,_USER_ID_GAS).'</strong> €</h1>


            ';
        $res=array("result"=>"OK", "msg"=>$h);
        echo json_encode($res);
        break;
        die();


        break;

        case "rettifica_aggiunta_sconto":
            $id_ordine = CAST_TO_INT($_POST["id_ordine"],0);
            if (!posso_gestire_ordine($id_ordine)){
                $res=array("result"=>"KO", "msg"=>"Non ho i permessi per questa operazione;" );
                echo json_encode($res);
                break;
                die();
            }



            $tipo_movimento = CAST_TO_INT($_POST["select_tipo_movimento_aggiunta"],1,8);
            $nuovo_valore = round(CAST_TO_FLOAT($_POST["importo_da_aggiungere_sconto"]),4);
            $descrizione_rettifica = clean($_POST["descrizione_operazione_aggiunta"]);
            $coinvolti = CAST_TO_INT($_POST["select_coinvolto_aggiunta"],1,2);
            $applica_rettifiche = CAST_TO_INT($_POST["applica_rettifiche_sconto"],1,2);



            //--------------------------------RETTIFICA

            if($descrizione_rettifica==""){$descrizione_rettifica=$d;}


            if($coinvolti==1){
                $operazione = ope::rettifica;
                //operazioni con @@
                switch ($tipo_movimento){
                    case 1: $operazione = ope::rettifica; $d="Rettifica"; break;
                    case 2: $operazione = ope::trasporto; $d="Spese Trasporto";break;
                    case 3: $operazione = ope::gestione; $d="Spese Gestione"; break;
                    case 4: $operazione = ope::progetto; $d="Finanziamento progetto"; break;
                    case 5: $operazione = ope::rimborso; $d="Rimborso spese"; break;
                    case 6: $operazione = ope::maggiorazione; $d="Maggiorazione"; break;
                    case 7: $operazione = ope::sconto; $d="Sconto"; break;
                    case 8: $operazione = ope::abbuono;$d="Abbuono"; break;
                }


                //tutti gli utenti
                $sql = "SELECT DISTINCT id_utenti FROM retegas_dettaglio_ordini WHERE id_ordine=:id_ordine";
                $n_partecipanti = rd4_rowCount("SELECT D.id_utenti from retegas_dettaglio_ordini D WHERE D.id_ordine='".$id_ordine."' GROUP BY D.id_utenti");
                if($applica_rettifiche==1){
                    //tutti i movimenti
                    $valore_totale = VA_ORDINE($id_ordine);
                }else{
                    //escludi rettifiche
                    $valore_totale = VA_ORDINE_SOLO_NETTO($id_ordine);
                }
            }else{
                //solo il proprio GAS

                $operazione = opeGAS::rettifica;
                //operazioni con ##
                switch ($tipo_movimento){
                    case 1: $operazione = opeGAS::rettifica.' '._USER_ID_GAS; $d="Rettifica"; break;
                    case 2: $operazione = opeGAS::trasporto.' '._USER_ID_GAS; $d="Spese Trasporto";break;
                    case 3: $operazione = opeGAS::gestione.' '._USER_ID_GAS; $d="Spese Gestione"; break;
                    case 4: $operazione = opeGAS::progetto.' '._USER_ID_GAS; $d="Finanziamento progetto"; break;
                    case 5: $operazione = opeGAS::rimborso.' '._USER_ID_GAS; $d="Rimborso spese"; break;
                    case 6: $operazione = opeGAS::maggiorazione.' '._USER_ID_GAS; $d="Maggiorazione"; break;
                    case 7: $operazione = opeGAS::sconto.' '._USER_ID_GAS; $d="Sconto"; break;
                    case 8: $operazione = opeGAS::abbuono.' '._USER_ID_GAS;$d="Abbuono"; break;
                }

                $sql = "SELECT DISTINCT D.id_utenti FROM retegas_dettaglio_ordini D inner join maaking_users U on U.userid=D.id_utenti WHERE id_ordine=:id_ordine AND U.id_gas='"._USER_ID_GAS."'";
                $n_partecipanti = rd4_rowCount("SELECT D.id_utenti from retegas_dettaglio_ordini D inner join maaking_users U on U.userid=D.id_utenti WHERE D.id_ordine='".$id_ordine."' AND U.id_gas='"._USER_ID_GAS."' GROUP BY D.id_utenti");
                if($applica_rettifiche==1){
                    //tutti i movimenti
                    $valore_totale = VA_ORDINE_GAS($id_ordine,_USER_ID_GAS);
                }else{
                    //escludi rettifiche
                    $valore_totale = VA_ORDINE_GAS_SOLO_NETTO($id_ordine,_USER_ID_GAS);
                }

            }


            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rows as $row) {


            if($applica_rettifiche==1){
                //Tutti i movimenti
                $valore_totale_user = VA_ORDINE_USER($id_ordine,$row["id_utenti"]);
            }else{
                //Escludi rettifiche
                $valore_totale_user = VA_ORDINE_USER_SOLO_NETTO($id_ordine,$row["id_utenti"]);
            }
            $importo = ROUND((($valore_totale_user / 100) *  $nuovo_valore),4);




            $stmt = $db->prepare("INSERT INTO retegas_dettaglio_ordini
                                    (id_utenti,
                                    id_articoli,
                                    id_stati,
                                    data_inserimento,
                                    data_convalida,
                                    qta_ord,
                                    id_amico,
                                    id_ordine,
                                    qta_conf,
                                    qta_arr,
                                    prz_dett,
                                    prz_dett_arr,
                                    art_codice,
                                    art_desc,
                                    art_um,
                                    check_code)
                                    VALUES
                                    (:id_utenti,
                                    0,
                                    0,
                                    NOW(),
                                    NOW(),
                                    1,
                                    0,
                                    :id_ordini,
                                    1,
                                    1,
                                    :prz_dett,
                                    :prz_dett_arr,
                                    :art_codice,
                                    :art_desc,
                                    '',
                                    :check_code)");
            $stmt->bindParam(':id_ordini', $id_ordine, PDO::PARAM_INT);
            $stmt->bindParam(':id_utenti', $row["id_utenti"], PDO::PARAM_INT);
            $stmt->bindParam(':prz_dett', $importo, PDO::PARAM_STR);
            $stmt->bindParam(':prz_dett_arr', $importo, PDO::PARAM_STR);
            $stmt->bindParam(':art_codice', $operazione, PDO::PARAM_STR);
            $stmt->bindParam(':art_desc', $descrizione_rettifica, PDO::PARAM_STR);
            $stmt->bindParam(':check_code', $operazione, PDO::PARAM_STR);
            $stmt->execute();
            if($stmt->rowCount()==1){
                $id = $db->lastInsertId();
                $stmt = $db->prepare("INSERT INTO retegas_distribuzione_spesa
                                    (id_riga_dettaglio_ordine,
                                    id_amico,
                                    qta_ord,
                                    qta_arr,
                                    data_ins,
                                    id_articoli,
                                    id_user,
                                    id_ordine)
                                    VALUES
                                    (:id,
                                    0,
                                    1,
                                    1,
                                    NOW(),
                                    0,
                                    :id_user,
                                    :id_ordine)");
                $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->bindParam(':id_user', $row["id_utenti"], PDO::PARAM_INT);
                $stmt->execute();
                if($stmt->rowCount()==1){
                    $ok_a++;
                }else{
                    $err++;
                }

            }else{
                $err++;
            }
            //-----------------------------------------------RETTIFICA


            }//END FOREACH
            if($err>0){
                $res=array("result"=>"KO", "msg"=>"Errori $err;" );
            }else{
                $res=array("result"=>"OK", "msg"=>"Rettifiche aggiunte" );
            }

            echo json_encode($res);

        break;


        case "rettifica_aggiunta":
            $id_ordine = CAST_TO_INT($_POST["id_ordine"],0);
            if (!posso_gestire_ordine($id_ordine)){
                $res=array("result"=>"KO", "msg"=>"Non ho i permessi per questa operazione;" );
                echo json_encode($res);
                break;
                die();
            }



            $tipo_movimento = CAST_TO_INT($_POST["select_tipo_movimento_aggiunta"],1,8);
            $nuovo_valore = round(CAST_TO_FLOAT($_POST["importo_da_aggiungere"]),4);
            $descrizione_rettifica = clean($_POST["descrizione_operazione_aggiunta"]);
            $coinvolti = CAST_TO_INT($_POST["select_coinvolto_aggiunta"],1,2);
            $tipo = CAST_TO_INT($_POST["tipo"],1,3);//1 = old : 2= equal 3 = note_brevi
            $applica_rettifiche = CAST_TO_INT($_POST["applica_rettifiche"],1,2);

            if ($tipo>2){
                $res=array("result"=>"KO", "msg"=>"Funzione non ancora attivata;" );
                echo json_encode($res);
                break;
                die();
            }


            //--------------------------------RETTIFICA

            if($descrizione_rettifica==""){$descrizione_rettifica=$d;}


            if($coinvolti==1){
                $operazione = ope::rettifica;
                switch ($tipo_movimento){
                    case 1: $operazione = ope::rettifica; $d="Rettifica"; break;
                    case 2: $operazione = ope::trasporto; $d="Spese Trasporto";break;
                    case 3: $operazione = ope::gestione; $d="Spese Gestione"; break;
                    case 4: $operazione = ope::progetto; $d="Finanziamento progetto"; break;
                    case 5: $operazione = ope::rimborso; $d="Rimborso spese"; break;
                    case 6: $operazione = ope::maggiorazione; $d="Maggiorazione"; break;
                    case 7: $operazione = ope::sconto; $d="Sconto"; break;
                    case 8: $operazione = ope::abbuono;$d="Abbuono"; break;
                }

                //tutti gli utenti
                $sql = "SELECT DISTINCT id_utenti FROM retegas_dettaglio_ordini WHERE id_ordine=:id_ordine";
                $n_partecipanti = rd4_rowCount("SELECT D.id_utenti from retegas_dettaglio_ordini D WHERE D.id_ordine='".$id_ordine."' GROUP BY D.id_utenti");
                if($applica_rettifiche==1){
                    //tutti i movimenti
                    $valore_totale = VA_ORDINE($id_ordine);
                }else{
                    //escludi rettifiche
                    $valore_totale = VA_ORDINE_ESCLUDI_RETT($id_ordine);
                }
            }else{
                //solo il proprio GAS
                $operazione = opeGAS::rettifica;
                switch ($tipo_movimento){
                    case 1: $operazione = opeGAS::rettifica.' '._USER_ID_GAS; $d="Rettifica"; break;
                    case 2: $operazione = opeGAS::trasporto.' '._USER_ID_GAS; $d="Spese Trasporto";break;
                    case 3: $operazione = opeGAS::gestione.' '._USER_ID_GAS; $d="Spese Gestione"; break;
                    case 4: $operazione = opeGAS::progetto.' '._USER_ID_GAS; $d="Finanziamento progetto"; break;
                    case 5: $operazione = opeGAS::rimborso.' '._USER_ID_GAS; $d="Rimborso spese"; break;
                    case 6: $operazione = opeGAS::maggiorazione.' '._USER_ID_GAS; $d="Maggiorazione"; break;
                    case 7: $operazione = opeGAS::sconto.' '._USER_ID_GAS; $d="Sconto"; break;
                    case 8: $operazione = opeGAS::abbuono.' '._USER_ID_GAS;$d="Abbuono"; break;
                }

                $sql = "SELECT DISTINCT D.id_utenti FROM retegas_dettaglio_ordini D inner join maaking_users U on U.userid=D.id_utenti WHERE id_ordine=:id_ordine AND U.id_gas='"._USER_ID_GAS."'";
                $n_partecipanti = rd4_rowCount("SELECT D.id_utenti from retegas_dettaglio_ordini D inner join maaking_users U on U.userid=D.id_utenti WHERE D.id_ordine='".$id_ordine."' AND U.id_gas='"._USER_ID_GAS."' GROUP BY D.id_utenti");
                if($applica_rettifiche==1){
                    //tutti i movimenti
                    $valore_totale = VA_ORDINE_GAS($id_ordine,_USER_ID_GAS);
                }else{
                    //escludi rettifiche
                    $valore_totale = VA_ORDINE_GAS_SOLO_NETTO($id_ordine,_USER_ID_GAS);
                }

            }

            //$res=array("result"=>"OK", "msg"=>"partecipanti : $partecipanti" );
            //echo json_encode($res);
            //break;
            //die();


            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rows as $row) {

            if($tipo==1){
                //Proporzionale
                if($applica_rettifiche==1){
                    //Tutti i movimenti
                    $valore_totale_user = VA_ORDINE_USER($id_ordine,$row["id_utenti"]);
                }else{
                    //Escludi rettifiche
                    $valore_totale_user = VA_ORDINE_USER_SOLO_NETTO($id_ordine,$row["id_utenti"]);
                }
                $importo = ROUND((($valore_totale_user * $nuovo_valore) /  $valore_totale),4);

            };
            if($tipo==2){$importo=ROUND(($nuovo_valore/$n_partecipanti),4);};
            if($tipo==3){};

            $stmt = $db->prepare("INSERT INTO retegas_dettaglio_ordini
                                    (id_utenti,
                                    id_articoli,
                                    id_stati,
                                    data_inserimento,
                                    data_convalida,
                                    qta_ord,
                                    id_amico,
                                    id_ordine,
                                    qta_conf,
                                    qta_arr,
                                    prz_dett,
                                    prz_dett_arr,
                                    art_codice,
                                    art_desc,
                                    art_um,
                                    check_code)
                                    VALUES
                                    (:id_utenti,
                                    0,
                                    0,
                                    NOW(),
                                    NOW(),
                                    1,
                                    0,
                                    :id_ordini,
                                    1,
                                    1,
                                    :prz_dett,
                                    :prz_dett_arr,
                                    :art_codice,
                                    :art_desc,
                                    '',
                                    :check_code)");
            $stmt->bindParam(':id_ordini', $id_ordine, PDO::PARAM_INT);
            $stmt->bindParam(':id_utenti', $row["id_utenti"], PDO::PARAM_INT);
            $stmt->bindParam(':prz_dett', $importo, PDO::PARAM_STR);
            $stmt->bindParam(':prz_dett_arr', $importo, PDO::PARAM_STR);
            $stmt->bindParam(':art_codice', $operazione, PDO::PARAM_STR);
            $stmt->bindParam(':art_desc', $descrizione_rettifica, PDO::PARAM_STR);
            $stmt->bindParam(':check_code', $operazione, PDO::PARAM_STR);
            $stmt->execute();
            if($stmt->rowCount()==1){
                $id = $db->lastInsertId();
                $stmt = $db->prepare("INSERT INTO retegas_distribuzione_spesa
                                    (id_riga_dettaglio_ordine,
                                    id_amico,
                                    qta_ord,
                                    qta_arr,
                                    data_ins,
                                    id_articoli,
                                    id_user,
                                    id_ordine)
                                    VALUES
                                    (:id,
                                    0,
                                    1,
                                    1,
                                    NOW(),
                                    0,
                                    :id_user,
                                    :id_ordine)");
                $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->bindParam(':id_user', $row["id_utenti"], PDO::PARAM_INT);
                $stmt->execute();
                if($stmt->rowCount()==1){
                    $ok_a++;
                }else{
                    $err++;
                }

            }else{
                $err++;
            }
            //-----------------------------------------------RETTIFICA


            }//END FOREACH
            if($err>0){
                $res=array("result"=>"KO", "msg"=>"Errori $err;" );
            }else{
                $res=array("result"=>"OK", "msg"=>"Rettifiche aggiunte" );
            }

            echo json_encode($res);

        break;

        case "art_delete":

            $id_ordine = CAST_TO_INT($_POST["id_ordine"]);
            $codice = CAST_TO_STRING($_POST["codice"]);

            if (!posso_gestire_ordine($id_ordine)){
                $res=array("result"=>"KO", "msg"=>"Non ho i permessi per questa operazione;" );
                echo json_encode($res);
                break;
                die();
            }
            //PASSA LE RIGHE
            $stmt = $db->prepare("SELECT id_dettaglio_ordini FROM retegas_dettaglio_ordini
                                 WHERE
                                    id_ordine=:id_ordine
                                    AND
                                    art_codice=:codice");
            $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
            $stmt->bindParam(':codice', $codice, PDO::PARAM_STR);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $err =0;
            //CANCELLA LE DESTRIBUZIONI
            foreach ($rows as $row) {
                $stmt = $db->prepare("DELETE FROM retegas_distribuzione_spesa
                                        WHERE
                                        id_riga_dettaglio_ordine=:id_dettaglio_ordini;");
                $stmt->bindParam(':id_dettaglio_ordini', $row["id_dettaglio_ordini"], PDO::PARAM_INT);
                $stmt->execute();
            }
            //CANCELLA LE RIGHE
            $stmt = $db->prepare("DELETE FROM retegas_dettaglio_ordini
                                    WHERE
                                    id_ordine=:id_ordine
                                    AND
                                    art_codice=:codice");
            $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
            $stmt->bindParam(':codice', $codice, PDO::PARAM_STR);
            $stmt->execute();

            if($stmt->rowCount()>0){
                $res=array("result"=>"OK", "msg"=>"Articolo cancellato" );
            }else{
                $res=array("result"=>"KO", "msg"=>"Errore nel db." );
            }

            echo json_encode($res);
            break;
            die();
        break;


        case "row_delete":
            $id_dettaglio_ordini = CAST_TO_INT($_POST["id_dettaglio_ordini"],0);
            $stmt = $db->prepare("SELECT id_utenti, id_ordine from retegas_dettaglio_ordini WHERE id_dettaglio_ordini=:id_dettaglio_ordini");
            $stmt->bindParam(':id_dettaglio_ordini', $id_dettaglio_ordini, PDO::PARAM_INT);
            $stmt->execute();
            $rowo = $stmt->fetch(PDO::FETCH_ASSOC);
            $id_ordine = $rowo["id_ordine"];

            if (!posso_gestire_ordine($id_ordine)){
                $res=array("result"=>"KO", "msg"=>"Non ho i permessi per questa operazione;" );
                echo json_encode($res);
                break;
                die();
            }

            $stmt = $db->prepare("DELETE FROM retegas_dettaglio_ordini
                                    WHERE
                                    id_dettaglio_ordini=:id_dettaglio_ordini
                                    LIMIT 1");
            $stmt->bindParam(':id_dettaglio_ordini', $id_dettaglio_ordini, PDO::PARAM_INT);
            $stmt->execute();
            $stmt = $db->prepare("DELETE FROM retegas_distribuzione_spesa
                                    WHERE
                                    id_riga_dettaglio_ordine=:id_dettaglio_ordini");
            $stmt->bindParam(':id_dettaglio_ordini', $id_dettaglio_ordini, PDO::PARAM_INT);
            $stmt->execute();
            if($stmt->rowCount()>0){
                $res=array("result"=>"OK", "msg"=>"Cancellata" );
            }else{
                $res=array("result"=>"KO", "msg"=>"Errore nel db." );
            }

            echo json_encode($res);
            break;
            die();
        break;
        case "rettifica_totali_utente":
            $id_ordine = CAST_TO_INT($_POST["id_ordine"],0);
            if (!posso_gestire_ordine($id_ordine)){
                $res=array("result"=>"KO", "msg"=>"Non ho i permessi per questa operazione;" );
                echo json_encode($res);
                break;
                die();
            }
            $tipo_movimento = CAST_TO_INT($_POST["tipo_movimento"],1,8);
            $id_utente = CAST_TO_INT($_POST["id_utente"]);
            $nuovo_valore = round(CAST_TO_FLOAT($_POST["nuovo_valore"]),4);
            $descrizione_rettifica = clean($_POST["descrizione_rettifica"]);
            if($descrizione_rettifica==""){$descrizione_rettifica="Rettifica";}

            $valore_attuale = VA_ORDINE_USER($id_ordine,$id_utente);
            $differenza = round($nuovo_valore - $valore_attuale ,4);

            if($differenza==0){
                $res=array("result"=>"KO", "msg"=>"Nessuna differenza!" );
                echo json_encode($res);
                break;
                die();
            }
            if($nuovo_valore<0){
                $res=array("result"=>"KO", "msg"=>"Totale negativo!" );
                echo json_encode($res);
                break;
                die();
            }

            //--------------------------------RETTIFICA
            $operazione = ope::rettifica;
            switch ($tipo_movimento){
                case 1: $operazione = ope::rettifica; break;
                case 2: $operazione = ope::trasporto; break;
                case 3: $operazione = ope::gestione; break;
                case 4: $operazione = ope::progetto; break;
                case 5: $operazione = ope::rimborso; break;
                case 6: $operazione = ope::maggiorazione; break;
                case 7: $operazione = ope::sconto; break;
                case 8: $operazione = ope::abbuono; break;
            }


            $stmt = $db->prepare("INSERT INTO retegas_dettaglio_ordini
                                    (id_utenti,
                                    id_articoli,
                                    id_stati,
                                    data_inserimento,
                                    data_convalida,
                                    qta_ord,
                                    id_amico,
                                    id_ordine,
                                    qta_conf,
                                    qta_arr,
                                    prz_dett,
                                    prz_dett_arr,
                                    art_codice,
                                    art_desc,
                                    art_um,
                                    check_code)
                                    VALUES
                                    (:id_utenti,
                                    0,
                                    0,
                                    NOW(),
                                    NOW(),
                                    1,
                                    0,
                                    :id_ordini,
                                    1,
                                    1,
                                    :prz_dett,
                                    :prz_dett_arr,
                                    :art_codice,
                                    :art_desc,
                                    '',
                                    :check_code)");
            $stmt->bindParam(':id_ordini', $id_ordine, PDO::PARAM_INT);
            $stmt->bindParam(':id_utenti', $id_utente, PDO::PARAM_INT);
            $stmt->bindParam(':prz_dett', $differenza, PDO::PARAM_STR);
            $stmt->bindParam(':prz_dett_arr', $differenza, PDO::PARAM_STR);
            $stmt->bindParam(':art_codice', $operazione, PDO::PARAM_STR);
            $stmt->bindParam(':art_desc', $descrizione_rettifica, PDO::PARAM_STR);
            $stmt->bindParam(':check_code', $operazione, PDO::PARAM_STR);
            $stmt->execute();
            if($stmt->rowCount()==1){
                $id = $db->lastInsertId();
                $stmt = $db->prepare("INSERT INTO retegas_distribuzione_spesa
                                    (id_riga_dettaglio_ordine,
                                    id_amico,
                                    qta_ord,
                                    qta_arr,
                                    data_ins,
                                    id_articoli,
                                    id_user,
                                    id_ordine)
                                    VALUES
                                    (:id,
                                    0,
                                    1,
                                    1,
                                    NOW(),
                                    0,
                                    :id_user,
                                    :id_ordine)");
                $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->bindParam(':id_user', $id_utente, PDO::PARAM_INT);
                $stmt->execute();
                if($stmt->rowCount()==1){
                    $res=array("result"=>"OK", "msg"=>"Rettifica effettuata", "nuovo_totale" => number_format(VA_ORDINE_USER($id_ordine,$id_utente),4) );
                }else{
                    $res=array("result"=>"KO", "msg"=>"Errore nella tabella distribuzione" );
                }

            }else{
                $res=array("result"=>"KO", "msg"=>"Errore nella tabella dettaglio" );
            }
            //-----------------------------------------------RETTIFICA




        echo json_encode($res);
        break;

        case "rettifica_totale":
        $id_ordine = CAST_TO_INT($_POST["id_ordine"],0);

        if (!posso_gestire_ordine($id_ordine)){
            $res=array("result"=>"KO", "msg"=>"Non ho i permessi per questa operazione;" );
            echo json_encode($res);
            break;
            die();
        }

        $tipo = CAST_TO_INT($_POST["tipo"],1,2);

        $nuovo_totale = CAST_TO_FLOAT($_POST["nuovo_totale"],0);
        $descrizione_rettifica = CAST_TO_STRING($_POST["descrizione_rettifica"]);
        if($descrizione_rettifica==""){$descrizione_rettifica="Rettifica";}
        $operazione = ope::rettifica;

        //Spalmata
        if($tipo==1){
            $valore_attuale = VA_ORDINE($id_ordine);
            $differenza = round($nuovo_totale - $valore_attuale ,4);

            //PASSO GLI USERS
            $stmt = $db->prepare("SELECT DISTINCT id_utenti from retegas_dettaglio_ordini WHERE id_ordine=:id_ordine");
            $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $err =0;

            foreach ($rows as $row) {
                $id_utente = $row["id_utenti"];
                $valore_ordine_utente = VA_ORDINE_USER($id_ordine,$id_utente);
                $percentuale_utente = $valore_ordine_utente / $valore_attuale;
                $rettifica_utente = $differenza * $percentuale_utente;

                $stmt = $db->prepare("INSERT INTO retegas_dettaglio_ordini
                                        (id_utenti,
                                        id_articoli,
                                        id_stati,
                                        data_inserimento,
                                        data_convalida,
                                        qta_ord,
                                        id_amico,
                                        id_ordine,
                                        qta_conf,
                                        qta_arr,
                                        prz_dett,
                                        prz_dett_arr,
                                        art_codice,
                                        art_desc,
                                        art_um,
                                        check_code)
                                        VALUES
                                        (:id_utenti,
                                        0,
                                        0,
                                        NOW(),
                                        NOW(),
                                        1,
                                        0,
                                        :id_ordini,
                                        1,
                                        1,
                                        :prz_dett,
                                        :prz_dett_arr,
                                        :art_codice,
                                        :art_desc,
                                        '',
                                        :check_code)");
                $stmt->bindParam(':id_ordini', $id_ordine, PDO::PARAM_INT);
                $stmt->bindParam(':id_utenti', $id_utente, PDO::PARAM_INT);
                $stmt->bindParam(':prz_dett', $rettifica_utente, PDO::PARAM_STR);
                $stmt->bindParam(':prz_dett_arr', $rettifica_utente, PDO::PARAM_STR);
                $stmt->bindParam(':art_codice', $operazione, PDO::PARAM_STR);
                $stmt->bindParam(':art_desc', $descrizione_rettifica, PDO::PARAM_STR);
                $stmt->bindParam(':check_code', $operazione, PDO::PARAM_STR);
                $stmt->execute();
                if($stmt->rowCount()==1){
                    $id = $db->lastInsertId();
                    $stmt = $db->prepare("INSERT INTO retegas_distribuzione_spesa
                                        (id_riga_dettaglio_ordine,
                                        id_amico,
                                        qta_ord,
                                        qta_arr,
                                        data_ins,
                                        id_articoli,
                                        id_user,
                                        id_ordine)
                                        VALUES
                                        (:id,
                                        0,
                                        1,
                                        1,
                                        NOW(),
                                        0,
                                        :id_user,
                                        :id_ordine)");
                    $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->bindParam(':id_user', $id_utente, PDO::PARAM_INT);
                    $stmt->execute();
                    if($stmt->rowCount()==1){
                        $ute++;
                    }else{
                        $err++;
                    }

                }else{
                    $err++;
                }


            }
            if($err>0){
                $res=array("result"=>"KO", "msg"=>"Sono stati contati $err errori." );
            }else{
                $res=array("result"=>"OK", "msg"=>"Rettifica effettuata", "nuovo_totale" => VA_ORDINE($id_ordine) );
            }


        }


        //gestore
        if($tipo==2){
            $valore_attuale = VA_ORDINE($id_ordine);
            $differenza = round($nuovo_totale - $valore_attuale ,4);

            $stmt = $db->prepare("SELECT id_utente from retegas_ordini WHERE id_ordini=:id_ordini");
            $stmt->bindParam(':id_ordini', $id_ordine, PDO::PARAM_INT);
            $stmt->execute();
            $rowo = $stmt->fetch(PDO::FETCH_ASSOC);
            $id_gestore = $rowo["id_utente"];

            $stmt = $db->prepare("INSERT INTO retegas_dettaglio_ordini
                                    (id_utenti,
                                    id_articoli,
                                    id_stati,
                                    data_inserimento,
                                    data_convalida,
                                    qta_ord,
                                    id_amico,
                                    id_ordine,
                                    qta_conf,
                                    qta_arr,
                                    prz_dett,
                                    prz_dett_arr,
                                    art_codice,
                                    art_desc,
                                    art_um,
                                    check_code)
                                    VALUES
                                    (:id_utenti,
                                    0,
                                    0,
                                    NOW(),
                                    NOW(),
                                    1,
                                    0,
                                    :id_ordini,
                                    1,
                                    1,
                                    :prz_dett,
                                    :prz_dett_arr,
                                    :art_codice,
                                    :art_desc,
                                    '',
                                    :check_code)");
            $stmt->bindParam(':id_ordini', $id_ordine, PDO::PARAM_INT);
            $stmt->bindParam(':id_utenti', $id_gestore, PDO::PARAM_INT);
            $stmt->bindParam(':prz_dett', $differenza, PDO::PARAM_STR);
            $stmt->bindParam(':prz_dett_arr', $differenza, PDO::PARAM_STR);
            $stmt->bindParam(':art_codice', $operazione, PDO::PARAM_STR);
            $stmt->bindParam(':art_desc', $descrizione_rettifica, PDO::PARAM_STR);
            $stmt->bindParam(':check_code', $operazione, PDO::PARAM_STR);
            $stmt->execute();
            if($stmt->rowCount()==1){
                $id = $db->lastInsertId();
                $stmt = $db->prepare("INSERT INTO retegas_distribuzione_spesa
                                    (id_riga_dettaglio_ordine,
                                    id_amico,
                                    qta_ord,
                                    qta_arr,
                                    data_ins,
                                    id_articoli,
                                    id_user,
                                    id_ordine)
                                    VALUES
                                    (:id,
                                    0,
                                    1,
                                    1,
                                    NOW(),
                                    0,
                                    :id_user,
                                    :id_ordine)");
                $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->bindParam(':id_user', $id_gestore, PDO::PARAM_INT);
                $stmt->execute();
                if($stmt->rowCount()==1){
                    $res=array("result"=>"OK", "msg"=>"Rettifica effettuata", "nuovo_totale" => VA_ORDINE($id_ordine) );
                }else{
                    $res=array("result"=>"KO", "msg"=>"Errore nella tabella distribuzione" );
                }

            }else{
                $res=array("result"=>"KO", "msg"=>"Errore nella tabella dettaglio" );
            }


        }

        echo json_encode($res);
        break;


        default :
        $res=array("result"=>"KO", "msg"=>"Comando '".$_POST["act"]."' non riconosciuto" );
        echo json_encode($res);
        break;

        }
}
if(isset($_POST["name"])){
    switch ($_POST["name"]) {
        /*
        /* -------------------------RETTIFICHE DA XEDITABLE
        */
        case "tot_art_arr":
            $codice = CAST_TO_STRING($_POST["pk"],0);
            $id_ordine = CAST_TO_INT($_POST["id_ordine"],0);
            if (!posso_gestire_ordine($id_ordine)){
                $res=array("result"=>"KO", "msg"=>"Non ho i permessi per questa operazione;" );
                echo json_encode($res);
                break;
                die();
            }

            $tot_qta_arr=CAST_TO_FLOAT($_POST["value"],0);

            $stmt = $db->prepare("SELECT id_dettaglio_ordini, qta_ord, qta_arr FROM retegas_dettaglio_ordini WHERE art_codice=:codice AND id_ordine=:id_ordine ORDER BY id_dettaglio_ordini ASC");
            $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
            $stmt->bindParam(':codice', $codice, PDO::PARAM_STR);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach($rows AS $row){
                if(((round($tot_qta_arr - $row["qta_ord"]))>=0)){
                    if($row["qta_ord"]<>$row["qta_arr"]){
                        $stmt = $db->prepare("UPDATE retegas_dettaglio_ordini
                                                SET qta_arr=:qta_arr
                                                WHERE
                                                id_dettaglio_ordini=:id_dettaglio_ordini
                                                LIMIT 1");
                        $stmt->bindParam(':id_dettaglio_ordini', $row["id_dettaglio_ordini"], PDO::PARAM_INT);
                        $stmt->bindParam(':qta_arr', $row["qta_ord"], PDO::PARAM_STR);
                        $stmt->execute();
                        //rettifica amici
                        rettifica_amici($row["id_dettaglio_ordini"], $tot_qta_arr);

                    }
                    $tot_qta_arr -= ROUND($row["qta_ord"],4);
                }else{
                    //echo "Tot_qta_arr MOD $tot_qta_arr <br>";
                    $stmt = $db->prepare("UPDATE retegas_dettaglio_ordini
                                            SET qta_arr=:qta_arr
                                            WHERE
                                            id_dettaglio_ordini=:id_dettaglio_ordini
                                            LIMIT 1");
                    $stmt->bindParam(':id_dettaglio_ordini', $row["id_dettaglio_ordini"], PDO::PARAM_INT);
                    $stmt->bindParam(':qta_arr', $tot_qta_arr, PDO::PARAM_STR);
                    $stmt->execute();
                    //rettifica amici
                    rettifica_amici($row["id_dettaglio_ordini"], $tot_qta_arr);
                    $tot_qta_arr =0;
                }

            }


            $res=array("result"=>"OK", "msg"=>"Quantità aggiornata" );
            echo json_encode($res);
            break;
            die();



        break;

        case "qta_tot":
            $id_dettaglio_ordini = CAST_TO_INT($_POST["pk"],0);
            $tipo_totale= CAST_TO_INT($_POST["tipo_totale"],1,2);

            $stmt = $db->prepare("SELECT qta_arr, prz_dett_arr, id_utenti, id_ordine from retegas_dettaglio_ordini WHERE id_dettaglio_ordini=:id_dettaglio_ordini");
            $stmt->bindParam(':id_dettaglio_ordini', $id_dettaglio_ordini, PDO::PARAM_INT);
            $stmt->execute();
            $rowo = $stmt->fetch(PDO::FETCH_ASSOC);
            $id_ordine = $rowo["id_ordine"];

            if (!posso_gestire_ordine($id_ordine)){
                $res=array("result"=>"KO", "msg"=>"Non ho i permessi per questa operazione;" );
                echo json_encode($res);
                break;
                die();
            }

            $qta_tot=CAST_TO_FLOAT($_POST["value"],0);
            if($tipo_totale>1){
                $new_q = ROUND(($qta_tot / $rowo["prz_dett_arr"]),4);
                $stmt = $db->prepare("UPDATE retegas_dettaglio_ordini
                                    SET qta_arr=:new_q
                                    WHERE
                                    id_dettaglio_ordini=:id_dettaglio_ordini
                                    LIMIT 1");
                $stmt->bindParam(':id_dettaglio_ordini', $id_dettaglio_ordini, PDO::PARAM_INT);
                $stmt->bindParam(':new_q', $new_q, PDO::PARAM_STR);

                $prz_dett_arr = $rowo["prz_dett_arr"];
                $qta_arr = $new_q;
            }else{
                $new_q = ROUND(($qta_tot / $rowo["qta_arr"]),4);
                $stmt = $db->prepare("UPDATE retegas_dettaglio_ordini
                                    SET prz_dett_arr=:new_q
                                    WHERE
                                    id_dettaglio_ordini=:id_dettaglio_ordini
                                    LIMIT 1");
                $stmt->bindParam(':id_dettaglio_ordini', $id_dettaglio_ordini, PDO::PARAM_INT);
                $stmt->bindParam(':new_q', $new_q, PDO::PARAM_STR);

                $prz_dett_arr = $new_q;
                $qta_arr = $rowo["qta_arr"];
            }


            $stmt->execute();
            if($stmt->rowCount()==1){
                //AGGIORNO AMICI
                rettifica_amici($id_dettaglio_ordini, $qta_arr);
                $res=array("result"=>"OK", "msg"=>"Quantità aggiornata", "qta_arr"=>$qta_arr,"id"=>$id_dettaglio_ordini,"prz_dett_arr"=>$prz_dett_arr );
                echo json_encode($res);
                break;
                die();
            }else{
                $res=array("result"=>"KO", "msg"=>"Problemi..." );
                echo json_encode($res);
                break;
                die();

            }


        break;

        case "qta_ord":
            $id_dettaglio_ordini = CAST_TO_INT($_POST["pk"],0);
            $stmt = $db->prepare("SELECT id_utenti, id_ordine from retegas_dettaglio_ordini WHERE id_dettaglio_ordini=:id_dettaglio_ordini");
            $stmt->bindParam(':id_dettaglio_ordini', $id_dettaglio_ordini, PDO::PARAM_INT);
            $stmt->execute();
            $rowo = $stmt->fetch(PDO::FETCH_ASSOC);
            $id_ordine = $rowo["id_ordine"];

            if (!posso_gestire_ordine($id_ordine)){
                $res=array("result"=>"KO", "msg"=>"Non ho i permessi per questa operazione;" );
                echo json_encode($res);
                break;
                die();
            }

            $qta_ord=CAST_TO_FLOAT($_POST["value"],0);

            $stmt = $db->prepare("UPDATE retegas_dettaglio_ordini
                                    SET qta_ord=:qta_ord,
                                        qta_arr=:qta_ord
                                    WHERE
                                    id_dettaglio_ordini=:id_dettaglio_ordini
                                    LIMIT 1");
            $stmt->bindParam(':id_dettaglio_ordini', $id_dettaglio_ordini, PDO::PARAM_INT);
            $stmt->bindParam(':qta_ord', $qta_ord, PDO::PARAM_STR);
            $stmt->execute();
            if($stmt->rowCount()==1){
                //AGGIORNO AMICI
                rettifica_amici($id_dettaglio_ordini, $qta_arr);
                $res=array("result"=>"OK", "msg"=>"Quantità aggiornata", "qta_arr"=>$qta_ord,"id"=>$id_dettaglio_ordini );
                echo json_encode($res);
                break;
                die();
            }else{
                $res=array("result"=>"KO", "msg"=>"Problemi..." );
                echo json_encode($res);
                break;
                die();

            }


        break;

        case "qta_arr":
            $id_dettaglio_ordini = CAST_TO_INT($_POST["pk"],0);
            $stmt = $db->prepare("SELECT id_utenti, id_ordine from retegas_dettaglio_ordini WHERE id_dettaglio_ordini=:id_dettaglio_ordini");
            $stmt->bindParam(':id_dettaglio_ordini', $id_dettaglio_ordini, PDO::PARAM_INT);
            $stmt->execute();
            $rowo = $stmt->fetch(PDO::FETCH_ASSOC);
            $id_ordine = $rowo["id_ordine"];

            if (!posso_gestire_ordine($id_ordine)){
                $res=array("result"=>"KO", "msg"=>"Non ho i permessi per questa operazione;" );
                echo json_encode($res);
                break;
                die();
            }

            $qta_arr=CAST_TO_FLOAT($_POST["value"],0);

            $stmt = $db->prepare("UPDATE retegas_dettaglio_ordini
                                    SET qta_arr=:qta_arr
                                    WHERE
                                    id_dettaglio_ordini=:id_dettaglio_ordini
                                    LIMIT 1");
            $stmt->bindParam(':id_dettaglio_ordini', $id_dettaglio_ordini, PDO::PARAM_INT);
            $stmt->bindParam(':qta_arr', $qta_arr, PDO::PARAM_STR);
            $stmt->execute();
            if($stmt->rowCount()==1){
                //AGGIORNO AMICI
                rettifica_amici($id_dettaglio_ordini, $qta_arr);
                $res=array("result"=>"OK", "msg"=>"Quantità aggiornata","id"=>$id_dettaglio_ordini );
                echo json_encode($res);
                break;
                die();
            }else{
                $res=array("result"=>"KO", "msg"=>"Problemi..." );
                echo json_encode($res);
                break;
                die();

            }


        break;


        case "prz_dett_arr":
            $id_dettaglio_ordini = CAST_TO_INT($_POST["pk"],0);
            $stmt = $db->prepare("SELECT id_utenti, id_ordine from retegas_dettaglio_ordini WHERE id_dettaglio_ordini=:id_dettaglio_ordini");
            $stmt->bindParam(':id_dettaglio_ordini', $id_dettaglio_ordini, PDO::PARAM_INT);
            $stmt->execute();
            $rowo = $stmt->fetch(PDO::FETCH_ASSOC);
            $id_ordine = $rowo["id_ordine"];

            if (!posso_gestire_ordine($id_ordine)){
                $res=array("result"=>"KO", "msg"=>"Non ho i permessi per questa operazione;" );
                echo json_encode($res);
                break;
                die();
            }

            $prz_dett_arr=CAST_TO_FLOAT($_POST["value"],0);

            $stmt = $db->prepare("UPDATE retegas_dettaglio_ordini
                                    SET prz_dett_arr=:prz_dett_arr
                                    WHERE
                                    id_dettaglio_ordini=:id_dettaglio_ordini
                                    LIMIT 1");
            $stmt->bindParam(':id_dettaglio_ordini', $id_dettaglio_ordini, PDO::PARAM_INT);
            $stmt->bindParam(':prz_dett_arr', $prz_dett_arr, PDO::PARAM_STR);
            $stmt->execute();
            if($stmt->rowCount()==1){
                $res=array("result"=>"OK", "msg"=>"Prezzo aggiornato", "id"=>$id_dettaglio_ordini );
                echo json_encode($res);
                break;
                die();
            }


        break;

        default :
        $res=array("result"=>"KO", "msg"=>"Comando '".$_POST["name"]."' name non riconosciuto" );
        echo json_encode($res);
        break;
    }
}