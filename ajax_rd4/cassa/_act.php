<?php
require_once("inc/init.php");
if(file_exists("../../lib_rd4/class.rd4.user.php")){require_once("../../lib_rd4/class.rd4.user.php");}
if(file_exists("../lib_rd4/class.rd4.user.php")){require_once("../lib_rd4/class.rd4.user.php");}


if(!empty($_POST["act"])){
    switch ($_POST["act"]) {
    case "del_richiesta":
        if(_USER_PERMISSIONS & perm::puo_gestire_la_cassa){
            $id_gas = _USER_ID_GAS;
            $id_option = CAST_TO_INT($_POST["id"]);
            $stmt = $db->prepare("SELECT * from retegas_options WHERE id_option=:id_option AND chiave ='PREN_MOV_CASSA' AND id_gas=:id_gas LIMIT 1;");
            $stmt->bindParam(':id_option', $id_option, PDO::PARAM_INT);
            $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch();

            if($stmt->rowCount()==1){
                //cancella l'opzione
                $stmt = $db->prepare("DELETE from retegas_options WHERE id_option=:id_option LIMIT 1;");
                $stmt->bindParam(':id_option', $id_option, PDO::PARAM_INT);
                $stmt->execute();
                $res=array("result"=>"OK", "msg"=>"Richiesta nascosta." );
            }else{
                $res=array("result"=>"KO", "msg"=>"KO" );
            }
        }else{
            $res=array("result"=>"KO", "msg"=>"KO" );
        }

        echo json_encode($res);
    break;
    case "add_richiesta":
        if(_USER_PERMISSIONS & perm::puo_gestire_la_cassa){
            $id_gas = _USER_ID_GAS;
            $id_option = CAST_TO_INT($_POST["id"]);
            $stmt = $db->prepare("SELECT * from retegas_options WHERE id_option=:id_option AND chiave ='PREN_MOV_CASSA' AND id_gas=:id_gas LIMIT 1;");
            $stmt->bindParam(':id_option', $id_option, PDO::PARAM_INT);
            $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch();

            if($stmt->rowCount()==1){

                    $importo=$row["valore_real"];
                    $descrizione=$row["valore_text"];
                    $documento=$row["note_1"];
                    $id_utente=$row["id_user"];

                    $sql = "INSERT INTO retegas_cassa_utenti (   id_utente ,
                                                            id_gas,
                                                            importo ,
                                                            segno ,
                                                            tipo_movimento ,
                                                            descrizione_movimento ,
                                                            data_movimento ,
                                                            id_cassiere ,
                                                            registrato ,
                                                            data_registrato ,
                                                            contabilizzato ,
                                                            data_contabilizzato,
                                                            numero_documento
                                                          )VALUES(
                                                          :id_utente,
                                                          '"._USER_ID_GAS."',
                                                          :importo,
                                                          '+',
                                                          '1',
                                                          :descrizione,
                                                          NOW(),
                                                          '"._USER_ID."',
                                                          'si',
                                                          NOW(),
                                                          'no',
                                                          NULL,
                                                          :documento
                                                          )";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':id_utente', $id_utente, PDO::PARAM_INT);
                    $stmt->bindParam(':documento', $documento, PDO::PARAM_STR);
                    $stmt->bindParam(':descrizione', $descrizione, PDO::PARAM_STR);
                    $stmt->bindParam(':importo', $importo, PDO::PARAM_STR);
                    $stmt->execute();

                    if($stmt->rowCount()==1){
                            $fullnameFROM = _USER_FULLNAME;
                            $mailFROM = _USER_MAIL;

                            $U = new user($id_utente);
                            $mailTO = $U->email;
                            $fullnameTO = $U->fullname;
                            unset($U);


                            //manda mail di carico credito
                            $oggetto = "[reteDES.it] Ricevuta di carico credito";
                            $profile = new Template('../../email_rd4/ricevuta_carico_da_cassiere.html');

                            $profile->set("fullname_cassiere", $fullnameFROM );
                            $profile->set("importo", $importo );
                            $profile->set("descrizione", $descrizione );
                            $profile->set("documento", $documento );

                            $messaggio = $profile->output();

                            if(SEmail($fullnameTO,$mailTO,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"RicevutaCaricoCredito")){

                            }else{

                            }


                            //cancella l'opzione
                            $stmt = $db->prepare("DELETE from retegas_options WHERE id_option=:id_option LIMIT 1;");
                            $stmt->bindParam(':id_option', $id_option, PDO::PARAM_INT);
                            $stmt->execute();
                            if($stmt->rowCount()==1){

                                $res=array("result"=>"OK", "msg"=>"Carico effettuato, Mail inviata." );
                            }else{
                                $res=array("result"=>"KO", "msg"=>"KO cancellazione option" );
                            }
                    }else{
                        $res=array("result"=>"KO", "msg"=>"KO Inserimento in tabella cassa." );
                    }
            }else{
                $res=array("result"=>"KO", "msg"=>"KO Option non tua." );
            }
        }else{
            $res=array("result"=>"KO", "msg"=>"KO" );
        }
        echo json_encode($res);
    break;



        default :
        $res=array("result"=>"KO", "msg"=>"Comando '".$_POST["act"]."' non riconosciuto" );
        echo json_encode($res);
        break;

    }
}
