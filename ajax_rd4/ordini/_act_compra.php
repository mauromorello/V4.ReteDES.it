<?php
require_once("inc/init.php");
if(file_exists("../../lib_rd4/class.rd4.user.php")){require_once("../../lib_rd4/class.rd4.user.php");}
if(file_exists("../lib_rd4/class.rd4.user.php")){require_once("../lib_rd4/class.rd4.user.php");}
if(file_exists("../../lib_rd4/class.rd4.ordine.php")){require_once("../../lib_rd4/class.rd4.ordine.php");}
if(file_exists("../lib_rd4/class.rd4.ordine.php")){require_once("../lib_rd4/class.rd4.ordine.php");}
if(file_exists("../../lib_rd4/class.rd4.articolo.php")){require_once("../../lib_rd4/class.rd4.articolo.php");}
if(file_exists("../lib_rd4/class.rd4.articolo.php")){require_once("../lib_rd4/class.rd4.articolo.php");}
if(file_exists("../../lib_rd4/class.rd4.gas.php")){require_once("../../lib_rd4/class.rd4.gas.php");}
if(file_exists("../lib_rd4/class.rd4.gas.php")){require_once("../lib_rd4/class.rd4.gas.php");}
if(file_exists("../../lib_rd4/class.rd4.listino.php")){require_once("../../lib_rd4/class.rd4.listino.php");}
if(file_exists("../lib_rd4/class.rd4.listino.php")){require_once("../lib_rd4/class.rd4.listino.php");}


$converter = new Encryption;

if(!empty($_POST["act"])){
    switch ($_POST["act"]) {

        case "delete_riga_amico":

        $id_dettaglio = CAST_TO_INT($_POST["id_dettaglio"],0);
        $id_distribuzione = CAST_TO_INT($_POST["id_distribuzione"],0);

        $delete_dettaglio = "NO";

        if(($id_dettaglio==0) OR ($id_distribuzione==0)){
            $arr = array("result"=>"KO","msg"=>"ID vuoto.");
            echo json_encode($arr);
            die();
        }

        //PRENDERE ID ORDINE DA ID DETTAGLIO
        $sql="SELECT * from retegas_dettaglio_ordini WHERE id_dettaglio_ordini=:id_dettaglio LIMIT 1;";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_dettaglio', $id_dettaglio, PDO::PARAM_INT);
        $stmt->execute();
        $rowD = $stmt->fetch();
        $id_ordine = CAST_TO_INT($rowD["id_ordine"],0);
        $id_articolo = $rowD["id_articoli"];
        $A = new articolo($id_articolo);



        //CONTROLLARE SE SONO L'UTENTE DI ID DETTAGLIO
        if($rowD["id_utenti"]<>_USER_ID){
            $arr = array("result"=>"ko","msg"=>"Riga non tua, ord:".$id_ordine);
            echo json_encode($arr);
            break;
        }

        $O=new ordine($id_ordine);
        if($O->codice_stato<>"AP"){
            //SE ORDINE E' CHIUSO
            //PRENDERE LA QTA_ORD e QTA_ARR DELLA RIGA AMICO
            $sql="SELECT * from retegas_distribuzione_spesa WHERE id_distribuzione=:id_distribuzione LIMIT 1;";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_distribuzione', $id_distribuzione, PDO::PARAM_INT);
            $stmt->execute();
            $rowA = $stmt->fetch();
            $amico_qord=$rowA["qta_ord"];
            $amico_qarr=$rowA["qta_arr"];

            //PRENDERE LA QTA_ORD e QTA_ARR DI MESTESSO
            $sql="SELECT * from retegas_distribuzione_spesa WHERE id_amico=0 AND id_riga_dettaglio_ordine=:id_dettaglio LIMIT 1;";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_dettaglio', $id_dettaglio, PDO::PARAM_INT);
            $stmt->execute();
            $rowB = $stmt->fetch();
            $mestesso_qord = $rowB["qta_ord"] + $amico_qord;
            $mestesso_qarr = $rowB["qta_arr"] + $amico_qarr;

            //SE ESISTA LA RIGA DI MESTESSO PER LA DISTRIBUZIONE (ORDINE/USER/AMICO)

            if($stmt->rowCount()>0){
                // E AGGIUNGERE A ME STESSO LA QUOTA DI AMICO
                $sql="UPDATE retegas_distribuzione_spesa SET qta_arr=:qta_arr, qta_ord=:qta_ord WHERE id_amico=0 AND id_riga_dettaglio_ordine=:id_dettaglio LIMIT 1;";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':id_dettaglio', $id_dettaglio, PDO::PARAM_INT);
                $stmt->bindParam(':qta_ord', $mestesso_qord , PDO::PARAM_STR);
                $stmt->bindParam(':qta_arr', $mestesso_qarr , PDO::PARAM_STR);
                $stmt->execute();
                if($stmt->rowCount()==1){
                    //CANCELLARE AMICO
                    $sql="DELETE FROM retegas_distribuzione_spesa WHERE id_distribuzione=:id_distribuzione LIMIT 1;";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':id_distribuzione', $id_distribuzione , PDO::PARAM_INT);
                    $stmt->execute();
                    if($stmt->rowCount()==1){
                        $arr = array("result"=>"OK","msg"=>"Riga eliminata.");
                        echo json_encode($arr);
                        break;
                    }else{
                        $arr = array("result"=>"KO","msg"=>"Non è stato possibile completare l'operazione di eliminazione.");
                        echo json_encode($arr);
                        break;
                    }
                }else{
                    $arr = array("result"=>"KO","msg"=>"Non è stato possibile completare l'operazione");
                    echo json_encode($arr);
                    break;
                }
            }else{
                //AGGIORNO LA RIGA DI DISTRIBUZIONE E METTO id_amico=0
                $sql="UPDATE retegas_distribuzione_spesa SET id_amico=0 WHERE  id_distribuzione=:id_distribuzione LIMIT 1;";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':id_distribuzione', $id_distribuzione , PDO::PARAM_INT);
                $stmt->execute();
                if($stmt->rowCount()==1){
                    $arr = array("result"=>"OK","msg"=>"Riga modificata");
                    echo json_encode($arr);
                    break;
                }else{
                    $arr = array("result"=>"KO","msg"=>"Non è stato possibile completare l'operazione di trasferimento da amico a me stesso.");
                    echo json_encode($arr);
                    break;
                }


            }
        }else{

            //$arr = array("result"=>"KO","msg"=>"Operazione per ora valida solo su ordini NON APERTI");
            //echo json_encode($arr);
            //break;

            //SE L'ORDINE E' APERTO
            //PRENDERE QORD E QARR AMICO
            $sql="SELECT * from retegas_distribuzione_spesa WHERE id_distribuzione=:id_distribuzione LIMIT 1;";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_distribuzione', $id_distribuzione, PDO::PARAM_INT);
            $stmt->execute();
            $rowA = $stmt->fetch();
            $amico_qord = $rowA["qta_ord"];
            $amico_qarr = $rowA["qta_arr"];

            $dettaglio_qord = QTA_ORDINATA_ID_DETTAGLIO($id_dettaglio);
            $dettaglio_qarr = QTA_ARRIVATA_ID_DETTAGLIO($id_dettaglio);

            $nuova_qord = $dettaglio_qord - $amico_qord;
            $nuova_qarr = $dettaglio_qarr - $amico_qarr;

            $q_min = $A->qta_minima;

            //CONTROLLO SU MULTIPLO
            $app = $nuova_qord;
            $volte = 0;

            while ($app>0) {
                $volte ++;
                $app = round($app - $q_min,2);
            }

            if($app==0){
            }else{
                $arr = array("res"=>"KO","msg"=>"Quantità risultante non multiplo di quella minima.");
                echo json_encode($arr);
                break;
            }

            //SOTTRARRE A QORD E QARR DETTAGLIO
            $sql="UPDATE retegas_dettaglio_ordini SET qta_ord=qta_ord-:qta_ord, qta_arr=qta_arr-:qta_arr WHERE id_dettaglio_ordini=:id_dettaglio LIMIT 1;";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_dettaglio', $id_dettaglio, PDO::PARAM_INT);
            $stmt->bindParam(':qta_ord', $amico_qord, PDO::PARAM_STR);
            $stmt->bindParam(':qta_arr', $amico_qarr, PDO::PARAM_STR);
            $stmt->execute();
            if($stmt->rowCount()==1){
                if(QTA_ARRIVATA_ID_DETTAGLIO($id_dettaglio)==0){
                    //SE TOTALE RIGA=0 CANCELLO RIGA DETTAGLIO
                    $sql = "delete from retegas_dettaglio_ordini WHERE id_dettaglio_ordini=:id_dettaglio LIMIT 1;";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':id_dettaglio', $id_dettaglio, PDO::PARAM_INT);
                    $stmt->execute();

                    // CANCELLO LE NOTE
                    $sql = "DELETE FROM retegas_options
                            WHERE id_user='"._USER_ID."'
                            AND id_articolo=:id_articolo
                            AND id_ordine=:id_ordine
                            AND chiave='_NOTE_DETTAGLIO';";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':id_articolo', $id_articolo, PDO::PARAM_INT);
                    $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
                    $stmt->execute();

                    $delete_dettaglio = "SI";

                }

                //CANCELLO AMICO
                $sql="DELETE FROM retegas_distribuzione_spesa WHERE id_distribuzione=:id_distribuzione LIMIT 1;";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':id_distribuzione', $id_distribuzione , PDO::PARAM_INT);
                $stmt->execute();
                if($stmt->rowCount()<>1){
                    $arr = array("result"=>"KO","msg"=>"Non è stato possibile completare l'operazione di eliminazione su valore 0.");
                    echo json_encode($arr);
                    break;
                }

                //SE HO LA CASSA RICALCOLO CASSA
                if(!_GAS_CASSA_ORDINI_SCASSATI){
                    if(_USER_USA_CASSA){
                        //SE L'ORDINE E' IN MODALITA' PRENOTAZIONE ALLORA SALTA L'AGGIORNAMENTO DELLA CASSA
                        if(DO_CHECK_USER_PRENOTAZIONE_ORDINE($id_ordine,_USER_ID)<>"SI"){
                            $res = DO_CASSA_UPDATE_ORDINE_UTENTE($id_ordine,_USER_ID);
                            //$arr = array("result"=>"KO","msg"=>$res);echo json_encode($arr);break;
                        }else{

                        }
                    }
                }


                //ALLA FINE
                $arr = array("result"=>"OK","msg"=>"Riga eliminata.", "delete_dettaglio"=>$delete_dettaglio);
                echo json_encode($arr);
                break;

            }else{
                $arr = array("result"=>"KO","msg"=>"Non è stato possibile completare l'operazione di aggiornamento riga dettaglio.");
                echo json_encode($arr);
                break;
            }









        }


        //CANCELLARE RIGA AMICO ID_DISTRIBUZIONE

        $arr = array("result"=>"OK","msg"=>"Comando non ancora attivo");
        echo json_encode($arr);
        break;

        case "add_articolo_ordine_qta":

            //if(_USER_ID<>2){
            //    $arr = array("result"=>"KO","msg"=>"Opzione non ancora attiva","value_ordine"=>$value_ordine,"value_cassa"=>$value_cassa,"value_articolo_new"=>$valore_articolo,"importo_articolo_new"=>0);
            //    echo json_encode($arr);
            //    break;
            //}


            $id_ordine = CAST_TO_INT($_POST["id_ordine"]);
            $id_articolo = CAST_TO_INT($_POST["id_articolo"]);
            $id_articolo_temp = CAST_TO_INT($_POST["id_articolo_temp"]);
            $id_amico= CAST_TO_INT($_POST["id_amico"]);
            //inserimento quantità manuale
            //$qta = CAST_TO_STRING($_POST["qta"]);
            $qta = CAST_TO_FLOAT(str_replace(",",".",$_POST["qta"]));

            $value_ordine = VA_ORDINE_USER($id_ordine,_USER_ID);
            $value_cassa = VA_CASSA_SALDO_UTENTE_TOTALE(_USER_ID);
            
            $O = new ordine($id_ordine);
            $tipo_listino = $O->get_tipo_listino();
            
            if($tipo_listino=="ORD"){
                $qta_articolo = QTA_ORDINATA_ORDINE_ARTICOLO_USER($id_ordine,$id_articolo_temp,_USER_ID);
                
            }else{
                $qta_articolo = QTA_ORDINATA_ORDINE_ARTICOLO_USER($id_ordine,$id_articolo,_USER_ID);
            }
            //INSERIRE QUA IL CONTROLLO TRIGGER A LIVELLO DI ARTICOLO
            
            //INSERIRE QUA IL CONTROLLO TRIGGER A LIVELLO DI ARTICOLO
            

            $check = DO_CHECK_USER_PARTECIPA_ORDINE($id_ordine);
            if($check<>"OK"){
                $arr = array("result"=>"KO","msg"=>$check,"value_ordine"=>$value_ordine,"value_cassa"=>$value_cassa,"value_articolo_new"=>$valore_articolo,"importo_articolo_new"=>0,"qta_articolo_old"=>round($qta_articolo,2));
                echo json_encode($arr);
                break;
            }
            if($qta<=0){
                $arr = array("result"=>"KO","msg"=>"Numero non valido","value_ordine"=>$value_ordine,"value_cassa"=>$value_cassa,"value_articolo_new"=>$valore_articolo,"importo_articolo_new"=>0,"qta_articolo_old"=>round($qta_articolo,2));
                echo json_encode($arr);
                break;
            }

            if($tipo_listino=="ORD"){
                $A = new articolo_temp($id_articolo_temp);
            }else{
                $A = new articolo($id_articolo);
            }
            $prezzo = $A->prezzo;
            $q_min = $A->qta_minima;
            //$qta_articolo_old = QTA_ORDINATA_ORDINE_ARTICOLO_USER($id_ordine,$id_articolo,_USER_ID);
            $qta_articolo_old = $qta_articolo;
            
            $valore_ordine_new = $value_ordine + ($prezzo*$qta)-($prezzo*$qta_articolo_old);
            //$valore_ordine_new =  ($prezzo*$qta);

            //CONTROLLO SU MULTIPLO
            $app = $qta;
            $volte = 0;

            while ($app>0) {
                $volte ++;
                $app = round($app - $q_min,2);
            }

            if($app==0){

            }else{
                $arr = array("res"=>"KO","msg"=>"Quantità non multiplo di quella minima.","value_ordine"=>round($value_ordine,2),"value_cassa"=>round($value_cassa,2),"value_articolo_new"=>round($valore_articolo,2),"importo_articolo_new"=>$valore_articolo,"qta_articolo_old"=>round($qta_articolo,2));
                echo json_encode($arr);
                break;
            }

            //$arr = array("res"=>"KO","msg"=>"Funzione non ancora attiva. ($volte x $q_min)","value_ordine"=>round($value_ordine,2),"value_cassa"=>round($value_cassa,2),"value_articolo_new"=>round($valore_articolo,2),"importo_articolo_new"=>$valore_articolo,"qta_articolo_old"=>round($qta_articolo,2));
            //echo json_encode($arr);
            //break;

            //SE IL GAS NON SCALA LA CASSA SU UTENTE
            if(_GAS_CASSA_ORDINI_SCASSATI){
                $is_ok="SI";
            }else{
                $is_ok = DO_CHECK_USER_CASSA_ORDINE($valore_ordine_new,$id_ordine);
            }
            if($is_ok<>"SI"){
               // log_me($id_ordine,_USER_ID,"GS3","KO","CASSA : false",0,"Ordine : $id_ordine, articolo : $id_articolo");
                $arr = array("res"=>"KO","msg"=>$is_ok,"value_ordine"=>round($value_ordine,2),"value_cassa"=>round($value_cassa,2),"value_articolo_new"=>round($valore_articolo,2),"importo_articolo_new"=>$valore_articolo,"qta_articolo_old"=>round($qta_articolo,2));
                echo json_encode($arr);
                break;
            }

            $descrizione_attuale = $A->descrizione_articoli;
            $codice_attuale = $A->codice;
            $udm_attuale = $A->u_misura." ".$A->misura;
            $id_ditta = $A->id_ditta;
            $descrizione_ditta=$A->descrizione_ditta;
            //$ingombro=($A->ingombro*$qta);
            $ingombro=($A->ingombro);

            //CANCELLO I DATI PRECEDENTI
            if($tipo_listino=="ORD"){
                DO_DELETE_ARTICOLO_ORDINE_UTENTE($id_articolo_temp,$id_ordine,_USER_ID);
            }else{
                DO_DELETE_ARTICOLO_ORDINE_UTENTE($id_articolo,$id_ordine,_USER_ID);
            }
            
            if($A->articoli_unico==1){
                //E' un univoco

            }else{
                $volte=1;
                $q_min = $qta;
            }

                for($i=0; $i<$volte; $i++){
                    //INSERT
                    $query_inserimento_articolo = "INSERT INTO retegas_dettaglio_ordini (
                                                id_utenti,
                                                id_articoli,
                                                data_inserimento,
                                                qta_ord,
                                                id_amico,
                                                id_ordine,
                                                qta_arr,
                                                prz_dett,
                                                prz_dett_arr,
                                                art_codice,
                                                art_desc,
                                                art_um,
                                                id_ditta,
                                                descrizione_ditta,
                                                art_ingombro)
                                                VALUES (
                                                    '"._USER_ID."',
                                                    :id_articolo,
                                                    NOW(),
                                                    :q_min_1,
                                                    '0',
                                                    :id_ordine,
                                                    :q_min_2,
                                                    :prezzo_1,
                                                    :prezzo_2,
                                                    :codice_attuale,
                                                    :descrizione_attuale,
                                                    :udm_attuale,
                                                    :id_ditta,
                                                    :descrizione_ditta,
                                                    :ingombro
                                                    );";
                  $stmt = $db->prepare($query_inserimento_articolo);
                  if($tipo_listino=="ORD"){
                    $stmt->bindParam(':id_articolo', $id_articolo_temp, PDO::PARAM_INT);
                  }else{
                    $stmt->bindParam(':id_articolo', $id_articolo, PDO::PARAM_INT);  
                  }
                  $stmt->bindParam(':q_min_1', $q_min, PDO::PARAM_STR);
                  $stmt->bindParam(':q_min_2', $q_min, PDO::PARAM_STR);
                  $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
                  $stmt->bindParam(':id_ditta', $id_ditta, PDO::PARAM_INT);
                  $stmt->bindParam(':prezzo_1', $prezzo, PDO::PARAM_STR);
                  $stmt->bindParam(':prezzo_2', $prezzo, PDO::PARAM_STR);
                  $stmt->bindParam(':codice_attuale', $codice_attuale, PDO::PARAM_STR);
                  $stmt->bindParam(':descrizione_attuale', $descrizione_attuale, PDO::PARAM_STR);
                  $stmt->bindParam(':descrizione_ditta', $descrizione_ditta, PDO::PARAM_STR);
                  $stmt->bindParam(':udm_attuale', $udm_attuale, PDO::PARAM_STR);
                  $stmt->bindParam(':ingombro', $ingombro, PDO::PARAM_STR);
                  $stmt->execute();

                  $last_id=$db->lastInsertId();
                  $id_dettaglio = $last_id;

                  $query_distribuzione_spesa = "INSERT INTO retegas_distribuzione_spesa (
                                                 id_riga_dettaglio_ordine,
                                                 id_amico,
                                                 qta_ord,
                                                 qta_arr,
                                                 data_ins,
                                                 id_articoli,
                                                 id_user,
                                                 id_ordine)
                                                 VALUES (
                                                    :last_id,
                                                    :id_amico,
                                                    :q_min_1,
                                                    :q_min_2,
                                                     NOW(),
                                                    :id_articolo,
                                                    '"._USER_ID."',
                                                    :id_ordine
                                                    );";
                    $stmt = $db->prepare($query_distribuzione_spesa);
                    $stmt->bindParam(':last_id', $last_id, PDO::PARAM_INT);
                    $stmt->bindParam(':q_min_1', $q_min, PDO::PARAM_STR);
                    $stmt->bindParam(':q_min_2', $q_min, PDO::PARAM_STR);
                    $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
                    if($tipo_listino=="ORD"){
                        $stmt->bindParam(':id_articolo', $id_articolo_temp, PDO::PARAM_INT);
                    }else{
                        $stmt->bindParam(':id_articolo', $id_articolo, PDO::PARAM_INT);
                    }
                    $stmt->bindParam(':id_amico', $id_amico, PDO::PARAM_INT);
                    $stmt->execute();

                } //FOR VOLTE




            //Aggiorno CASSA
            if(!_GAS_CASSA_ORDINI_SCASSATI){
                if(_USER_USA_CASSA){
                    //SE L'ORDINE E' IN MODALITA' PRENOTAZIONE ALLORA SALTA L'AGGIORNAMENTO DELLA CASSA
                    if(DO_CHECK_USER_PRENOTAZIONE_ORDINE($id_ordine,_USER_ID)<>"SI"){
                        $res = DO_CASSA_UPDATE_ORDINE_UTENTE($id_ordine,_USER_ID);
                        //$arr = array("result"=>"KO","msg"=>$res);echo json_encode($arr);break;
                    }else{

                    }
                }
            }

            $valore_cassa = VA_CASSA_SALDO_UTENTE_TOTALE(_USER_ID);
            $valore_riga =  $qta*$prezzo;

            //AMICI COINVOLTI
            $sql = "SELECT * from retegas_distribuzione_spesa WHERE id_ordine='$id_ordine' AND id_user='"._USER_ID."' AND id_riga_dettaglio_ordine='$id_dettaglio'  group by id_amico;";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $amici_coinvolti = $stmt->rowCount();

            $qta_articolo_new =1;
            $importo_articolo_new =1;
            $arr = array(   "result"=>"OK",
                            "value_ordine"=>$valore_ordine_new,
                            "value_cassa"=>$valore_cassa,
                            "qta_articolo_new"=>$qta,
                            "amici_coinvolti"=>$amici_coinvolti,
                            "importo_articolo_new"=>$valore_riga,
                            "qta_articolo_old"=>round($qta_articolo,2));
            l($id_ordine,"Modifica NUMERICA n.$qta art.$codice_attuale ($descrizione_attuale) valore ordine: $valore_ordine_new");
            echo json_encode($arr);
        break;


        default:
            $res=array("result"=>"KO", "msg"=>"Comando non riconosciuto" );
            echo json_encode($res);
        break;
     }

}
if(!empty($_GET["name"])){
     switch ($_GET["name"]) {

     }

}