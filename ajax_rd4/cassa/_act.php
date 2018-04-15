<?php
require_once("inc/init.php");
if(file_exists("../../lib_rd4/class.rd4.user.php")){require_once("../../lib_rd4/class.rd4.user.php");}
if(file_exists("../lib_rd4/class.rd4.user.php")){require_once("../lib_rd4/class.rd4.user.php");}
if(file_exists("../../lib_rd4/class.rd4.cassa.php")){require_once("../../lib_rd4/class.rd4.cassa.php");}
if(file_exists("../lib_rd4/class.rd4.cassa.php")){require_once("../lib_rd4/class.rd4.cassa.php");}
if(file_exists("../../lib_rd4/class.rd4.ordine.php")){require_once("../../lib_rd4/class.rd4.ordine.php");}
if(file_exists("../lib_rd4/class.rd4.ordine.php")){require_once("../lib_rd4/class.rd4.ordine.php");}

if(!empty($_POST["act"])){
    switch ($_POST["act"]) {
        case "edit_movimento_singolo":

        if(_USER_PERMISSIONS & perm::puo_gestire_la_cassa){
                $id_cassa_utenti = CAST_TO_INT($_POST["id"],0);

                $importo=CAST_TO_FLOAT(str_replace(",",".",$_POST["importo"]));

                if($importo>=0){
                    $segno="+";
                }else{
                    $segno="-";
                    $importo=abs($importo);
                }


                $stmt = $db->prepare("UPDATE retegas_cassa_utenti
                                        SET importo=:importo, segno=:segno
                                        WHERE
                                        id_cassa_utenti=:id_cassa_utenti
                                        LIMIT 1;");
                $stmt->bindParam(':id_cassa_utenti', $id_cassa_utenti, PDO::PARAM_INT);
                $stmt->bindParam(':importo', $importo, PDO::PARAM_STR);
                $stmt->bindParam(':segno', $segno, PDO::PARAM_STR);
                $stmt->execute();
                if($stmt->rowCount()==1){
                    $res=array("result"=>"OK", "msg"=>"importo aggiornato", "id"=>$id_cassa_utenti, "segno"=>$segno, "importo"=>$importo );
                }else{
                    $res=array("result"=>"KO", "msg"=>"Importo NON aggiornato");
                }
            }else{
                $res=array("result"=>"KO", "msg"=>"No");
            }
            echo json_encode($res);
            break;
            die();


        case "registra_tutti_movimenti":
        $movimenti = $_POST["values"];
        if(_USER_PERMISSIONS & perm::puo_gestire_la_cassa){
            foreach ($movimenti as $movimento){
                $movimento=CAST_TO_INT($movimento);
                $query = "UPDATE retegas_cassa_utenti SET registrato='si', data_registrato=NOW() WHERE id_cassa_utenti='$movimento';";
                $stmt = $db->prepare($query);
                $stmt->execute();
                $i++;
            }

            $res=array("result"=>"OK", "msg"=> $i." movimenti registrati.");
        }else{
            $res=array("result"=>"KO", "msg"=>"No");
        }
        echo json_encode($res);
        break;

        case "consolida_movimenti":

        if(_USER_PERMISSIONS & perm::puo_gestire_la_cassa){

            $id_gas=_USER_ID_GAS;
            $consolidati=0;

            /*PER OGNI UTENTE DEL GAS */
            $stmt = $db->prepare("SELECT * from maaking_users WHERE id_gas=:id_gas;");
            $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll();
            foreach($rows as $row){

                /*VALORE CASSA USER*/
                $userid = $row["userid"];
                $l .= 'Utente '.$row["fullname"].': ';
                $VC = round(VA_CASSA_SALDO_UTENTE_TOTALE($userid),2);
                $l .= 'SALDO: '.$VC.'<br>';

                /*ELIMINA MOVIMENTI USER*/
                $stmt = $db->prepare("DELETE from retegas_cassa_utenti WHERE id_utente=:id_utente;");
                $stmt->bindParam(':id_utente', $userid, PDO::PARAM_INT);
                $stmt->execute();

                if($stmt->rowCount()>0){
                    $l .= 'Cancellati '.$stmt->rowCount().' movimenti.';

                    if($VC>0){
                        $segno="+";
                    }else{
                        $segno="-";
                    }

                    /*INSERISCE EQUIVALENTE*/
                    DO_INSERT_CASSA_UTENTI( $userid,
                                            0,
                                            _USER_ID,
                                            _USER_ID_GAS,
                                            0,
                                            $VC,
                                            $segno,
                                            2,
                                            0,
                                            "Consolidamento cassa",
                                            "",
                                            "",
                                            "si",
                                            "si");


                    /*SE OPERAZIONE OK*/
                    $consolidati++;
                    $l .= 'Inserito nuovo movimento con valore '.$VC.' Eu.';
                }else{
                    $l .= 'Nessun movimento cancellato';
                }
                $l .="<hr>";


                /*LOG*/
            }


            if($consolidati>0){
                $res=array("result"=>"OK", "msg"=>"Cassa consolidata", "consolidati"=>$consolidati );

                //MAIL AI CASSIERI
                $query_users="SELECT
                        maaking_users.fullname,
                        maaking_users.email,
                        maaking_users.id_gas,
                        maaking_users.userid,
                        maaking_users.user_permission
                        FROM
                        maaking_users
                        WHERE
                        maaking_users.isactive=1 AND
                        maaking_users.id_gas = '"._USER_ID_GAS."'
                        ";
                $stmt = $db->prepare($query_users);
                $stmt->execute();
                $rows_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach($rows_users as $row_u){
                    if($row_u["user_permission"] & perm::puo_gestire_la_cassa){
                            $lista_destinatari .= $row_u["fullname"]."<br>";

                            $r[]=array( 'email' => $row_u["email"],
                                'name' => $row_u["fullname"],
                                'type' => 'bcc');

                    }
                }

                $messaggio_cassiere='Sono stati consolidati i movimenti dalla cassa del tuo gas.<br> Ecco la situazione ad oggi:<br>'.$l;

                $fullnameFROM = "ReteDES.it";
                    $mailFROM = "info@retedes.it";
                $oggetto = "[reteDES] "._USER_FULLNAME." consolidamento CASSA GAS";
                $profile = new Template('../../email_rd4/avviso_cassiere.html');
                $profile->set("fullname_cassiere", _USER_FULLNAME );
                $profile->set("messaggio_cassiere", $messaggio_cassiere);

                $messaggio = $profile->output();
                SEmailMulti($r,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"");
                unset ($profile);
                //MAIL AI CASSIERI

                //INSERIMENTO OPTION COME CONSOLIDAMENTO CASSA
                $id_gas = _USER_ID_GAS;
                $id_utente = _USER_ID;
                
                $stmt = $db->prepare("INSERT INTO retegas_options
                                    (id_gas,id_user,chiave,valore_data) VALUES (:id_gas,:id_utente,'_CASSA_CONSOLIDAMENTO',NOW())");
                $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
                $stmt->bindParam(':id_utente', $id_utente, PDO::PARAM_INT);
                $stmt->execute();
                //INSERIMENTO OPTION COME CONSOLIDAMENTO CASSA
                

           }else{
               $res=array("result"=>"KO", "msg"=>"Importo NON aggiornato");
           }

        }else{
            $res=array("result"=>"KO", "msg"=>"No");
        }
        echo json_encode($res);
        break;
        die();


        /*DELETE MOVIMENTO TUTTI*/
        case "delete_movimenti_tutti":

        if(_USER_PERMISSIONS & perm::puo_gestire_la_cassa){

            $id_gas=_USER_ID_GAS;

            /*CICLO DELETE*/
            $stmt = $db->prepare("DELETE FROM retegas_cassa_utenti
                                        WHERE
                                        id_gas=:id_gas;");
            $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
            $stmt->execute();
            /*CICLO DELETE*/



            if($stmt->rowCount()>0){
               $res=array("result"=>"OK", "msg"=>"Cassa azzerata", "deleted"=>$stmt->rowCount() );

                //MAIL AI CASSIERI
                $query_users="SELECT
                        maaking_users.fullname,
                        maaking_users.email,
                        maaking_users.id_gas,
                        maaking_users.userid,
                        maaking_users.user_permission
                        FROM
                        maaking_users
                        WHERE
                        maaking_users.isactive=1 AND
                        maaking_users.id_gas = '"._USER_ID_GAS."'
                        ";
                $stmt = $db->prepare($query_users);
                $stmt->execute();
                $rows_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach($rows_users as $row_u){
                    if($row_u["user_permission"] & perm::puo_gestire_la_cassa){
                            $lista_destinatari .= $row_u["fullname"]."<br>";

                            $r[]=array( 'email' => $row_u["email"],
                                'name' => $row_u["fullname"],
                                'type' => 'bcc');

                    }
                }

                $messaggio_cassiere='Sono stati eliminati TUTTI i movimenti dalla cassa del tuo gas.';

                $fullnameFROM = "ReteDES.it";
                    $mailFROM = "info@retedes.it";
                $oggetto = "[reteDES] "._USER_FULLNAME." eliminazione totale CASSA GAS";
                $profile = new Template('../../email_rd4/avviso_cassiere.html');
                $profile->set("fullname_cassiere", _USER_FULLNAME );
                $profile->set("messaggio_cassiere", $messaggio_cassiere);

                $messaggio = $profile->output();
                SEmailMulti($r,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"");
                unset ($profile);
                //MAIL AI CASSIERI


           }else{
               $res=array("result"=>"KO", "msg"=>"Importo NON aggiornato");
           }

        }else{
            $res=array("result"=>"KO", "msg"=>"No");
        }
        echo json_encode($res);
        break;
        die();
        /*DELETE MOVIMENTO TUTTI*/


        /*DELETE MOVIMENTO MULTIPLO*/
        case "delete_movimento_multiplo":

        if(_USER_PERMISSIONS & perm::puo_gestire_la_cassa){

            $ids = CAST_TO_ARRAY($_POST["ids"]);

            foreach($ids as $id_cassa_utenti){

                /*LOG*/
                $stmt = $db->prepare("SELECT * FROM retegas_cassa_utenti
                                            WHERE
                                            id_cassa_utenti=:id_cassa_utenti
                                            LIMIT 1;");
                $stmt->bindParam(':id_cassa_utenti', $id_cassa_utenti, PDO::PARAM_INT);
                $stmt->execute();
                $row = $stmt->fetch(MYSQL_NUM);
                $log .= implode(",",$row)."<br>";
                /*LOG*/

                /*CICLO DELETE*/
                $stmt = $db->prepare("DELETE FROM retegas_cassa_utenti
                                            WHERE
                                            id_cassa_utenti=:id_cassa_utenti
                                            LIMIT 1;");
                $stmt->bindParam(':id_cassa_utenti', $id_cassa_utenti, PDO::PARAM_INT);
                $stmt->execute();
                /*CICLO DELETE*/

                $deleted++;
            }

            if($deleted>0){
               $res=array("result"=>"OK", "msg"=>"Movimenti eliminato", "id"=>$id_cassa_utenti, "segno"=>$segno, "importo"=>$importo );

                //MAIL AI CASSIERI
                $query_users="SELECT
                        maaking_users.fullname,
                        maaking_users.email,
                        maaking_users.id_gas,
                        maaking_users.userid,
                        maaking_users.user_permission
                        FROM
                        maaking_users
                        WHERE
                        maaking_users.isactive=1 AND
                        maaking_users.id_gas = '"._USER_ID_GAS."'
                        ";
                $stmt = $db->prepare($query_users);
                $stmt->execute();
                $rows_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach($rows_users as $row_u){
                    if($row_u["user_permission"] & perm::puo_gestire_la_cassa){
                            $lista_destinatari .= $row_u["fullname"]."<br>";

                            $r[]=array( 'email' => $row_u["email"],
                                'name' => $row_u["fullname"],
                                'type' => 'bcc');

                    }
                }

                $messaggio_cassiere='Sono stati eliminati '.$deleted.' movimenti dalla cassa del tuo gas: ecco la lista completa nel caso possa servire:<br><p>'.$log.'</p>';

                $fullnameFROM = "ReteDES.it";
                    $mailFROM = "info@retedes.it";
                $oggetto = "[reteDES] "._USER_FULLNAME." eliminazione movimenti CASSA";
                $profile = new Template('../../email_rd4/avviso_cassiere.html');
                $profile->set("fullname_cassiere", _USER_FULLNAME );
                $profile->set("messaggio_cassiere", $messaggio_cassiere);

                $messaggio = $profile->output();
                SEmailMulti($r,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"");
                unset ($profile);
                //MAIL AI CASSIERI


           }else{
               $res=array("result"=>"KO", "msg"=>"Importo NON aggiornato");
           }

        }else{
            $res=array("result"=>"KO", "msg"=>"No");
        }
        echo json_encode($res);
        break;
        die();
        /*DELETE MOVIMENTO MULTIPLO*/


        case "delete_movimento":
        if(_USER_PERMISSIONS & perm::puo_gestire_la_cassa){
            $id_cassa_utenti=CAST_TO_INT($_POST["id"]);
            $stmt = $db->prepare("DELETE FROM retegas_cassa_utenti
                                        WHERE
                                        id_cassa_utenti=:id_cassa_utenti
                                        LIMIT 1;");
            $stmt->bindParam(':id_cassa_utenti', $id_cassa_utenti, PDO::PARAM_INT);
            $stmt->execute();
           if($stmt->rowCount()==1){
               $res=array("result"=>"OK", "msg"=>"Movimento eliminato", "id"=>$id_cassa_utenti, "segno"=>$segno, "importo"=>$importo );

                //MAIL AI CASSIERI
                $query_users="SELECT
                        maaking_users.fullname,
                        maaking_users.email,
                        maaking_users.id_gas,
                        maaking_users.userid,
                        maaking_users.user_permission
                        FROM
                        maaking_users
                        WHERE
                        maaking_users.isactive=1 AND
                        maaking_users.id_gas = '"._USER_ID_GAS."'
                        ";
                $stmt = $db->prepare($query_users);
                $stmt->execute();
                $rows_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach($rows_users as $row_u){
                    if($row_u["user_permission"] & perm::puo_gestire_la_cassa){
                            $lista_destinatari .= $row_u["fullname"]."<br>";

                            $r[]=array( 'email' => $row_u["email"],
                                'name' => $row_u["fullname"],
                                'type' => 'bcc');

                    }
                }

                $messaggio_cassiere='Il movimento di cassa #'.$id_cassa_utenti.' è stato eliminato.';

                $fullnameFROM = "ReteDES.it";
                    $mailFROM = "info@retedes.it";
                $oggetto = "[reteDES] "._USER_FULLNAME." movimento di cassa eliminato";
                $profile = new Template('../../email_rd4/avviso_cassiere.html');
                $profile->set("fullname_cassiere", _USER_FULLNAME );
                $profile->set("messaggio_cassiere", $messaggio_cassiere);

                $messaggio = $profile->output();
                SEmailMulti($r,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"V4RicaricaCredito");
                unset ($profile);
                //MAIL AI CASSIERI


           }else{
               $res=array("result"=>"KO", "msg"=>"Importo NON aggiornato");
           }

        }else{
            $res=array("result"=>"KO", "msg"=>"No");
        }
        echo json_encode($res);
        break;
        die();
        case "delete_tutto":
        if(_USER_PERMISSIONS & perm::puo_gestire_la_cassa){
            $id_ordine=CAST_TO_INT($_POST["id"]);
            DO_CASSA_DELETE_ORDINE_GAS($id_ordine);
            $res=array("result"=>"OK", "msg"=>"Movimenti eliminati");
        }else{
            $res=array("result"=>"KO", "msg"=>"No");
        }
        echo json_encode($res);
        break;
        die();
        case "registra_tutto":
        if(_USER_PERMISSIONS & perm::puo_gestire_la_cassa){
            $id_ordine=CAST_TO_INT($_POST["id"]);
            DO_CASSA_REGISTRA_ORDINE_GAS($id_ordine);
            $res=array("result"=>"OK", "msg"=>"Movimenti registrati");
        }else{
            $res=array("result"=>"KO", "msg"=>"No");
        }
        echo json_encode($res);
        break;
        die();
        case "show_movimenti_small":
        $iduser = CAST_TO_INT($_POST["idu"]);
        $id_ordine = CAST_TO_INT($_POST["ido"]);

        $O = new ordine($id_ordine);
        //-----------------------------------CONTENT
        $html='<table id="" class="table table-condensed" style="margin-left: auto; margin-right: auto">
                    <thead >
                        <tr class="intestazione" >
                            <th style="width:10%;">Codice</th>
                            <th >Descrizione</th>
                            <th style="width:5%;">Arrivati</th>
                            <th style="width:10%;">Prezzo</th>
                            <th style="width:5%;">Totale articoli</th>
                        </tr>
                    </thead>
                    <tbody>';
        //-----------------------------------DATI

        $sql = "SELECT art_codice, art_desc, art_um, qta_ord, qta_arr, prz_dett_arr, prz_dett FROM retegas_dettaglio_ordini WHERE id_ordine=:id_ordine AND id_utenti=:iduser";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_ordine', $O->id_ordini, PDO::PARAM_INT);
        $stmt->bindParam(':iduser', $iduser, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($rows AS $row){
            $riga++;
            if(is_integer($riga/2)){
                $row_class="odd";
            }else{
                $row_class="even";
            }


            $totale_articoli = round($row["qta_arr"]*$row["prz_dett_arr"],4);

            $qta_ord = $row["qta_ord"];

            $label="";
            $label_prz ="";


            $html.='<tr class="'.$row_class.'">';
                $html.='<td>'.$row["art_codice"].'</td>';
                $html.='<td>'.$row["art_desc"].' <span class="note">('.$row["art_um"].')</span></td>';
                $html.='<td style="text-align:center">'._NF($row["qta_arr"]).'</td>';
                $html.='<td style="text-align:right">'._NF($row["prz_dett_arr"]).' '.$label_prz.'</td>';
                $html.='<td style="text-align:right"><strong>'._NF($totale_articoli).'</strong></td>';
            $html.='</tr>';

            $totaleA += round($totale_articoli,4);


        }
            if($totaleC==0){$totaleC="";}
            $totaleT = $totaleA+$totaleC;
        //-----------------------------------DATI
        $html.='    </tbody>
                    <tfoot>
                        <tr class="totale">
                            <th colspan=4 style="text-align:right">Totali</th>
                            <th style="text-align:right"><strong>'.number_format($totaleA,2, ',', '').' </strong></th>
                        </tr>
                    </tfoot>
                 </table>';
        //-----------------------------------FOOTER

            $res=array("result"=>"OK", "html"=>$html, "idu"=>$iduser );
            echo json_encode($res);
        break;
        case "cassa_add_movimento":
            $iduser = CAST_TO_INT($_POST["idu"],0);
            if($iduser==0){
                $res=array("result"=>"KO", "msg"=>"Devi selezionare un utente" );
                echo json_encode($res);
                break;
            }

            $stmtC = $db->prepare("SELECT valore_text FROM retegas_options WHERE id_user=:id_user AND chiave='_USER_USA_CASSA' LIMIT 1;");
            $stmtC->bindParam(':id_user', $iduser, PDO::PARAM_INT);
            $stmtC->execute();
            $rowC = $stmtC->fetch();
            if($rowC["valore_text"]<>'SI'){
                $res=array("result"=>"KO", "msg"=>"utente con cassa NON attiva." );
                echo json_encode($res);
                break;      
            }
            
            
            $id_ordine = CAST_TO_INT($_POST["id_ordine"],0);
            $importo = round(CAST_TO_FLOAT($_POST["importo"]),4);
            $documento = CAST_TO_STRING($_POST["documento"]);
            $data_movimento = CAST_TO_STRING($_POST["data_movimento"]);
            if($data_movimento==""){$data_movimento=="NOW()";} 

            if($importo==0){
                $res=array("result"=>"KO", "msg"=>"Devi inserire un importo" );
                echo json_encode($res);
                break;
            }

            $descrizione = CAST_To_STRING($_POST["descrizione"]);

            if(trim($descrizione)==""){
                $res=array("result"=>"KO", "msg"=>"Deve esserci una descrizione" );
                echo json_encode($res);
                break;
            }

            if($importo>=0){
                $segno="+";
            }else{
                $segno="-";
                $importo=abs($importo);
            }


            DO_INSERT_CASSA_UTENTI($iduser,
                                $id_ordine,
                                _USER_ID,
                                _USER_ID_GAS,
                                0,
                                $importo,
                                $segno,
                                3,
                                1,
                                $descrizione,
                                null,
                                $documento,
                                $registrato = "si",
                                $contabilizzato = "si"
                                );

            $fullnameFROM = _USER_FULLNAME;
            $mailFROM = _USER_MAIL;

            $U = new user($iduser);
            $mailTO = $U->email;
            $fullnameTO = $U->fullname;
            unset($U);


            //manda mail di carico credito
            $oggetto = "[reteDES] Movimento sulla tua cassa di reteDES.it";
            $profile = new Template('../../email_rd4/movimento_singolo_cassa.html');

            $profile->set("fullname_cassiere", $fullnameFROM );
            $profile->set("importo", $segno.$importo );
            $profile->set("descrizione", $descrizione );
            $profile->set("documento", $documento );

            $messaggio = $profile->output();

            if(SEmail($fullnameTO,$mailTO,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"MovimentoSingolo")){

            }else{

            }
            $cassa = _NF(VA_CASSA_SALDO_UTENTE_TOTALE($iduser));
            $html = '<div class="well well-lg padding-10"><h3 class="text-danger">Nuovo saldo: '.$cassa.'</h3></div>';

            $res=array("result"=>"OK", "html"=>$html, "idu"=>$iduser );
            echo json_encode($res);
            break;
        case "show_cassa_user_box":
            $iduser = CAST_TO_INT($_POST["idu"]);
            $cassa = _NF(VA_CASSA_SALDO_UTENTE_TOTALE($iduser));
            $html = '<div class="well well-lg padding-10"><h3>Saldo: '.$cassa.'</h3></div>';

            $res=array("result"=>"OK", "html"=>$html, "idu"=>$iduser );
            echo json_encode($res);
            break;
        case "show_cassa_small":
        $iduser = CAST_TO_INT($_POST["idu"]);
        $id_ordine = CAST_TO_INT($_POST["ido"]);

        $O = new ordine($id_ordine);
        //-----------------------------------CONTENT
        $html='<table id="" class="table table-condensed" style="margin-left: auto; margin-right: auto">
                    <thead >
                        <tr class="intestazione" >
                            <th style="width:10%;">Mov.</th>
                            <th style="width:5%;">Tipo</th>
                            <th >Descrizione</th>
                            <th style="width:10%;">Registrato</th>
                            <th style="width:5%;">Totale</th>
                        </tr>
                    </thead>
                    <tbody>';
        //-----------------------------------DATI

        $sql = "SELECT id_cassa_utenti, tipo_movimento, descrizione_movimento, registrato,  DATE_FORMAT(data_movimento,'%d/%m/%Y') as data, DATE_FORMAT(data_registrato,'%d/%m/%Y') as data_reg, importo   FROM retegas_cassa_utenti WHERE id_ordine=:id_ordine AND id_utente=:id_utente";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_ordine', $O->id_ordini, PDO::PARAM_INT);
        $stmt->bindParam(':id_utente', $iduser, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($rows AS $row){
            $riga++;
            if(is_integer($riga/2)){
                $row_class="odd";
            }else{
                $row_class="even";
            }

            if($row["registrato"]<>"si"){
                $class_registrato = '<span class="label label-danger font-xs">NON registrato</span>';
            }else{
                $class_registrato = '<span class="label label-success font-xs small">R '.$row["data_reg"].'</span>';
            }

            $html.='<tr class="'.$row_class.'">';
                $html.='<td>'.$row["id_cassa_utenti"].'</td>';
                $html.='<td>'.$row["tipo_movimento"].'</td>';
                $html.='<td style="text-align:center" class="small">'.$row["descrizione_movimento"].'</td>';
                $html.='<td style="text-align:right">'.$class_registrato.'</td>';
                $html.='<td style="text-align:right"><strong class="cassa_editable" data-pk="'.$row["id_cassa_utenti"].'">'._NF($row["importo"]).'</strong><br><i class="fa fa-times text-danger delete_movimento" data-id="'.$row["id_cassa_utenti"].'" style="cursor:pointer;" title="Elimina questo movimento"></i></td>';
            $html.='</tr>';

            $totaleA += round($row["importo"],2);


        }

        //-----------------------------------DATI
        $html.='    </tbody>
                    <tfoot>
                        <tr class="totale">
                            <th colspan=4 style="text-align:right">Totali</th>
                            <th style="text-align:right"><strong>'._NF($totaleA).'</strong></th>
                        </tr>
                    </tfoot>
                 </table>';
        //-----------------------------------FOOTER

            $res=array("result"=>"OK", "html"=>$html, "idu"=>$iduser );
            echo json_encode($res);
        break;
        case "allinea_ordine":
        if(_USER_PERMISSIONS & perm::puo_gestire_la_cassa){
            $id_ordine = CAST_TO_INT($_POST["id"]);

            DO_CASSA_ALLINEA_ORDINE($id_ordine);
            $res=array("result"=>"OK", "msg"=>"Ordine allineato." );
        }else{
            $res=array("result"=>"KO", "msg"=>"KO" );
        }

        echo json_encode($res);
    break;

    case "opzioni_cassa_2":
        if(_USER_PERMISSIONS & perm::puo_gestire_la_cassa){
            $id_gas = _USER_ID_GAS;
            
            $_GAS_IBAN = CAST_TO_STRING($_POST["gas_iban"]);
            $_GAS_ID_CREDITORE = CAST_TO_STRING($_POST["gas_id_creditore"]);


            $C = new cassa($id_gas);
            
            $C->set_GAS_IBAN($id_gas,$_GAS_IBAN);
            $C->set_GAS_ID_CREDITORE($id_gas,$_GAS_ID_CREDITORE);
            
            $res=array("result"=>"OK", "msg"=>"Impostazioni SEPA salvate" );

        }else{
            $res=array("result"=>"KO", "msg"=>"KO" );
        }

        echo json_encode($res);
    break;
    
    case "opzioni_cassa_1":
        if(_USER_PERMISSIONS & perm::puo_gestire_la_cassa){
            $id_gas = _USER_ID_GAS;
            if($_POST["controlla_minimo"]=="true"){$controlla_minimo="SI";}else{$controlla_minimo="NO";}
            $_GAS_COPERTURA_CASSA = CAST_TO_FLOAT($_POST["percentuale_copertura_cassa"],0);
            $_GAS_CASSA_MIN_LEVEL = CAST_TO_FLOAT($_POST["minimo_cassa"],-1000,1000);


            $C = new cassa($id_gas);
            $C->set_GAS_CASSA_CHECK_MIN_LEVEL($id_gas,$controlla_minimo);
            $C->set_GAS_COPERTURA_CASSA($id_gas,$_GAS_COPERTURA_CASSA);
            $C->set_GAS_CASSA_MIN_LEVEL($id_gas,$_GAS_CASSA_MIN_LEVEL);


            $res=array("result"=>"OK", "msg"=>"Impostazioni salvate" );

        }else{
            $res=array("result"=>"KO", "msg"=>"KO" );
        }

        echo json_encode($res);
    break;
    case "_GAS_CASSA_REGISTRA_AUTOMATICO":
        if(_USER_PERMISSIONS & perm::puo_gestire_la_cassa){
            $id_gas = _USER_ID_GAS;
            if($_POST["value"]>0){$_GAS_CASSA_REGISTRA_AUTOMATICO_CHECKED="SI";}else{$_GAS_CASSA_REGISTRA_AUTOMATICO_CHECKED="NO";}
            $C = new cassa($id_gas);
            $C->set_GAS_CASSA_REGISTRA_AUTOMATICO($id_gas,$_GAS_CASSA_REGISTRA_AUTOMATICO_CHECKED);
            $res=array("result"=>"OK", "msg"=>"Impostazioni registrazione movimenti salvate" );
        }else{
            $res=array("result"=>"KO", "msg"=>"KO" );
        }

        echo json_encode($res);
    break;
    case "_GAS_CASSA_BONIFICO_AUTOMATICO":
        if(_USER_PERMISSIONS & perm::puo_gestire_la_cassa){
            $id_gas = _USER_ID_GAS;
            if($_POST["value"]>0){$_GAS_CASSA_BONIFICO_AUTOMATICO_CHECKED="SI";}else{$_GAS_CASSA_BONIFICO_AUTOMATICO_CHECKED="NO";}
            $C = new cassa($id_gas);
            $C->set_GAS_CASSA_BONIFICO_AUTOMATICO($id_gas,$_GAS_CASSA_BONIFICO_AUTOMATICO_CHECKED);
            $res=array("result"=>"OK", "msg"=>"Impostazioni carico salvate" );
        }else{
            $res=array("result"=>"KO", "msg"=>"KO" );
        }

        echo json_encode($res);
    break;
    case "_GAS_CASSA_ALLINEAMENTO_ORDINI":
        if(_USER_PERMISSIONS & perm::puo_gestire_la_cassa){
            $id_gas = _USER_ID_GAS;
            $value = CAST_TO_INT($_POST["value"]);
            $C = new cassa($id_gas);
            $C->set_GAS_CASSA_ALLINEAMENTO_ORDINI($id_gas,$value);
            $res=array("result"=>"OK", "msg"=>"Impostazioni Allineamento salvate" );
        }else{
            $res=array("result"=>"KO", "msg"=>"KO" );
        }
        echo json_encode($res);
    break;

    case "_GAS_CASSA_ORDINI_SCASSATI":
        if(_USER_PERMISSIONS & perm::puo_gestire_la_cassa){
            $id_gas = _USER_ID_GAS;
            if($_POST["value"]>0){$_GAS_CASSA_ORDINI_SCASSATI_CHECKED="SI";}else{$_GAS_CASSA_ORDINI_SCASSATI_CHECKED="NO";}
            $C = new cassa($id_gas);
            $C->set_GAS_CASSA_ORDINI_SCASSATI($id_gas,$_GAS_CASSA_ORDINI_SCASSATI_CHECKED);
            $res=array("result"=>"OK", "msg"=>"Impostazioni Scarico salvate" );
        }else{
            $res=array("result"=>"KO", "msg"=>"KO" );
        }
        echo json_encode($res);
    break;
    case "_GAS_CASSA_SCARICO_AUTOMATICO":
        if(_USER_PERMISSIONS & perm::puo_gestire_la_cassa){
            $id_gas = _USER_ID_GAS;
            if($_POST["value"]>0){$_GAS_CASSA_SCARICO_AUTOMATICO_CHECKED="SI";}else{$_GAS_CASSA_SCARICO_AUTOMATICO_CHECKED="NO";}
            $C = new cassa($id_gas);
            $C->set_GAS_CASSA_SCARICO_AUTOMATICO($id_gas,$_GAS_CASSA_SCARICO_AUTOMATICO_CHECKED);
            $res=array("result"=>"OK", "msg"=>"Impostazioni Scarico salvate" );
        }else{
            $res=array("result"=>"KO", "msg"=>"KO" );
        }

        echo json_encode($res);
    break;
    case "_GAS_CASSA_PRENOTAZIONE_ORDINI":
        if(_USER_PERMISSIONS & perm::puo_gestire_la_cassa){
            $id_gas = _USER_ID_GAS;
            if($_POST["value"]>0){$_GAS_CASSA_PRENOTAZIONE_ORDINI="SI";}else{$_GAS_CASSA_PRENOTAZIONE_ORDINI="NO";}
            $C = new cassa($id_gas);
            $C->set_GAS_CASSA_PRENOTAZIONE_ORDINI($id_gas,$_GAS_CASSA_PRENOTAZIONE_ORDINI);
            $res=array("result"=>"OK", "msg"=>"Impostazioni Prenotazioni salvate" );
        }else{
            $res=array("result"=>"KO", "msg"=>"KO" );
        }

        echo json_encode($res);
    break;
    case "_GAS_CASSA_VISUALIZZAZIONE_SALDO":
        if(_USER_PERMISSIONS & perm::puo_gestire_la_cassa){
            $id_gas = _USER_ID_GAS;
            $value = CAST_TO_INT($_POST["value"],1,2);
            $C = new cassa($id_gas);
            $C->set_GAS_CASSA_VISUALIZZAZIONE_SALDO($id_gas,$value);
            $res=array("result"=>"OK", "msg"=>"Impostazioni Visualizzazione saldo salvate" );
        }else{
            $res=array("result"=>"KO", "msg"=>"KO" );
        }

        echo json_encode($res);
    break;
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
                            $oggetto = "[reteDES] Ricevuta di carico credito";
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
if(isset($_POST["name"])){
    switch ($_POST["name"]) {
    case "importo":

            if(_USER_PERMISSIONS & perm::puo_gestire_la_cassa){
                $id_cassa_utenti = CAST_TO_INT($_POST["pk"],0);

                $importo=CAST_TO_FLOAT(str_replace(",",".",$_POST["value"]));
                if($importo>=0){
                    $segno="+";
                }else{
                    $segno="-";
                    $importo=abs($importo);
                }


                $stmt = $db->prepare("UPDATE retegas_cassa_utenti
                                        SET importo=:importo, segno=:segno
                                        WHERE
                                        id_cassa_utenti=:id_cassa_utenti
                                        LIMIT 1;");
                $stmt->bindParam(':id_cassa_utenti', $id_cassa_utenti, PDO::PARAM_INT);
                $stmt->bindParam(':importo', $importo, PDO::PARAM_STR);
                $stmt->bindParam(':segno', $segno, PDO::PARAM_STR);
                $stmt->execute();
                if($stmt->rowCount()==1){
                    $res=array("result"=>"OK", "msg"=>"importo aggiornato", "id"=>$id_cassa_utenti );
                }
            }else{
                $res=array("result"=>"KO", "msg"=>"No");
            }
            echo json_encode($res);
            break;
            die();
    default :
        $res=array("result"=>"KO", "msg"=>"Comando '".$_POST["name"]."' name non riconosciuto" );
        echo json_encode($res);
        break;
    }

}
