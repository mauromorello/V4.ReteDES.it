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

require_once("../../lib_rd4/htmlpurifier-4.7.0/library/HTMLPurifier.auto.php");



$converter = new Encryption;

if(!empty($_POST["act"])){
    switch ($_POST["act"]) {
        
        case "save_referente_gas":
        
            $id_referenza=CAST_TO_INT($_POST["id_referenza"],0);
            $id_referente=CAST_TO_INT($_POST["id_referente"],0);
            
            //RECORD DELLE REFERENZE
            $stmt = $db->prepare("SELECT * from retegas_referenze WHERE id_referenze=:id_referenze LIMIT 1;");
            $stmt->bindParam(':id_referenze', $id_referenza, PDO::PARAM_INT);
            $stmt->execute();
            $rowREF = $stmt->fetch();
        
            $id_gas_referenze = $rowREF["id_gas_referenze"];
            $id_ordine = $rowREF["id_ordine_referenze"];
            
            //GUARDO SE L'UTENTE FA PARTE DEL GAS E SE E' ATTIVO
            $stmt = $db->prepare("SELECT COUNT(*) as conto from maaking_users WHERE userid=:id_referente AND id_gas=:id_gas AND isactive=1 LIMIT 1;");
            $stmt->bindParam(':id_referente', $id_referente, PDO::PARAM_INT);
            $stmt->bindParam(':id_gas', $id_gas_referenze, PDO::PARAM_INT);
            $stmt->execute();
            $rowGAS = $stmt->fetch();
            if($rowGAS["conto"]==1){
                $stmt = $db->prepare("UPDATE retegas_referenze SET id_utente_referenze=:id_referente WHERE id_referenze=:id_referenze LIMIT 1;");
                $stmt->bindParam(':id_referenze', $id_referenza, PDO::PARAM_INT);
                $stmt->bindParam(':id_referente', $id_referente, PDO::PARAM_INT);
                $stmt->execute();
                l_n($id_ordine,"Referente esterno per il gas $id_gas_referenze forzato a id $id_referente.");    
            }else{
                $res = array("result"=>"KO","msg"=>"Non è possibile assegnare questo utente."); 
                echo json_encode($res);
                die();    
            }
        
            
        
            $res = array("result"=>"OK","msg"=>"Utente assegnato."); 
            echo json_encode($res);
        break;
        
        case "ordine_tipo_listino":
        
            if(_USER_ID<>2){
                echo json_encode(array("result"=>"KO", "msg"=>"NON ancora attiva" ));
                die();
            }
            $tipo_listino = $_POST["tipo_listino"];
            $id_ordine = $_POST["id_ordine"];
            $O = new ordine($id_ordine);
            $id_listino = $O->id_listini;
            
            $sql="DELETE FROM retegas_options WHERE chiave='_ORDINE_TIPO_LISTINO' and id_ordine=:id_ordine;";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
            $stmt->execute();
            
            if($tipo_listino=="ORD"){
                $sql="INSERT INTO retegas_options (id_ordine,chiave,valore_text) VALUES (:id_ordine,'_ORDINE_TIPO_LISTINO','ORD');";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
                $stmt->execute();
                
                $sql = "SELECT COUNT(*) as conto FROM retegas_articoli_temp WHERE id_ordine=:id_ordine;";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':id_ordine', $id_ordine , PDO::PARAM_INT);
                $stmt->execute();
                $row = $stmt->fetch();
                if($row["conto"]==0){
                    //COPIO GLI ARTICOLI DEL LISTINO ORIGINALE NEL LISTINO TEMPORANEO
                    //USARE ID LISTINO
                    //COPIARE ANCHE ID
                    //AGGIUNGERE IL NUMERO D'ORDINE
                 
                    $query_copia="INSERT INTO
                                    retegas_articoli_temp (id_articoli,
                                                          codice,
                                                          id_listini,
                                                          u_misura,
                                                          misura,
                                                          descrizione_articoli,
                                                          qta_scatola,
                                                          prezzo,
                                                          ingombro,
                                                          qta_minima,
                                                          qta_multiplo,
                                                          articoli_note,
                                                          articoli_unico,
                                                          articoli_opz_1,
                                                          articoli_opz_2,
                                                          articoli_opz_3,
                                                          is_disabled,
                                                          id_ditta,
                                                          descrizione_ditta,
                                                          id_ordine)
                                    SELECT
                                            id_articoli,
                                            codice,
                                            :id_listino,
                                            u_misura,
                                            misura,
                                            descrizione_articoli,
                                            qta_scatola,
                                            prezzo,
                                            ingombro,
                                              qta_minima,
                                              qta_multiplo,
                                              articoli_note,
                                              articoli_unico,
                                              articoli_opz_1,
                                              articoli_opz_2,
                                              articoli_opz_3,
                                              is_disabled,
                                              id_ditta,
                                              descrizione_ditta,
                                              :id_ordine
                                    FROM
                                            retegas_articoli WHERE id_listini=:id_listino;";
                   $stmt = $db->prepare($query_copia);
                   $stmt->bindParam(':id_ordine', $id_ordine , PDO::PARAM_INT);
                   $stmt->bindParam(':id_listino', $id_listino , PDO::PARAM_INT);
                   $stmt->execute();
                            
                }
                
            }
            
        
            echo json_encode(array("result"=>"OK", "msg"=>"OK" ));
        break;
        case "nascondi_ordine_gas":
        if (!posso_gestire_ordine_come_gas($_POST['id_ordine'])){
            echo json_encode(array("result"=>"KO", "msg"=>"Non hai i permessi necessari" ));
            die();
        }
        
        $id_ordine = CAST_TO_INT($_POST['id_ordine']);
        $id_gas    =_USER_ID_GAS;
        $O=new ordine($id_ordine);

        $sql="DELETE FROM retegas_options WHERE id_ordine=:id_ordine AND id_gas=:id_gas AND chiave='_ORDINE_NASCOSTO_GAS' LIMIT 3;";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
        $stmt->execute();

        $sql="INSERT INTO retegas_options (id_ordine,id_gas,chiave,valore_text) VALUES (:id_ordine,:id_gas,'_ORDINE_NASCOSTO_GAS','SI');";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
        $stmt->execute();
         
         
        $res = array("result"=>"OK","msg"=>"Ordine nascosto."); 
        l_n($id_ordine,"Nascosto ordine GAS: eseguita su gas "._USER_GAS_NOME);
        echo json_encode($res);
     break;
        
     case "visibila_ordine_gas":
        if (!posso_gestire_ordine_come_gas($_POST['id_ordine'])){
            echo json_encode(array("result"=>"KO", "msg"=>"Non hai i permessi necessari" ));
            die();
        }
        
        $id_ordine = CAST_TO_INT($_POST['id_ordine']);
        $id_gas    =_USER_ID_GAS;
        $O=new ordine($id_ordine);

        $sql="DELETE FROM retegas_options WHERE id_ordine=:id_ordine AND id_gas=:id_gas AND chiave='_ORDINE_NASCOSTO_GAS' LIMIT 3;";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
        $stmt->execute();
        
        $res = array("result"=>"OK","msg"=>"Ordine reso visibile."); 
        l_n($id_ordine,"Reso visibile ordine GAS: eseguita su gas "._USER_GAS_NOME);
        echo json_encode($res);
     break;   
        
        
        case "visualizza_ordine_per_esportazione";

            $id_ordine = CAST_TO_INT($_POST["id_ordine"],0);
            $values = $_POST["values"];
            $tipo_visualizza=CAST_TO_STRING($_POST["tipo_visualizza"][0]);
            $tipo_ordinamento=CAST_TO_STRING($_POST["tipo_ordinamento"][0]);

            $order_by = " ORDER BY D.id_utenti ASC ";
            if($tipo_ordinamento==="id_gas"){
                $order_by = " ORDER BY G.id_gas ASC ";
            }
            if($tipo_ordinamento==="art_codice"){
                $order_by = " ORDER BY D.art_codice ASC ";
            }
            if($tipo_ordinamento==="data_inserimento"){
                $order_by = " ORDER BY D.data_inserimento ASC ";
            }
            $colonne = array("id_ordine"            =>"ID ORDINE",
                             "descrizione_ordini"   =>"DESCRIZIONE ORDINE",
                             "data_inserimento"     =>"DATA",
                             "id_utenti"            =>"ID_UTENTE",
                             "fullname"             =>"NOME",
                             "id_gas"               =>"ID GAS",
                             "descrizione_gas"      =>"DESCRIZIONE GAS",
                             "id_ditta"             =>"ID DITTA",
                             "descrizione_ditta"    =>"DESCRIZIONE_DITTA",
                             "art_codice"           =>"CODICE ARTICOLO",
                             "art_desc"             =>"DESCRIZIONE ARTICOLO",
                             "art_um"               =>"UNITA' DI VENDITA",
                             "qta_ord"              =>"QTA ORDINATA",
                             "qta_arr"              =>"QTA ARRIVATA",
                             "prz_dett"             =>"PREZZO",
                             "prz_dett_arr"         =>"PREZZO REALE",
                             "tot_riga"             =>"TOTALE_RIGA");

            //HEADER TABELLA

            if($tipo_visualizza==="tabella"){
                $l.='<table class="table table-condensed" id="tabella_ordine_esportazione"><thead><tr>';
            }


            foreach($colonne as $key => $value){
                if(in_array($key,$values)){
                    if($tipo_visualizza==="tabella"){
                        $l.="<th>".$value."</th>";
                    }else{
                        $l.="<b>".$value."</b>"._USER_CSV_SEPARATOR." ";
                    }


                }
            }

            if($tipo_visualizza==="tabella"){
                $l.='</tr></thead><tbody>';
            }else{
                rtrim($l,_USER_CSV_SEPARATOR." ");
                $l.="<br>";
            }
            $html .=$l;
            unset($l);
            //HEADER

            $sql="SELECT    D.id_ordine,
                            O.descrizione_ordini,
                            D.id_utenti,
                            U.fullname,
                            G.id_gas,
                            G.descrizione_gas,
                            D.art_codice,
                            D.art_desc,
                            D.art_um,
                            REPLACE(D.prz_dett, '.', '.') as prz_dett,
                            REPLACE(D.prz_dett_arr, '.', '.') as prz_dett_arr,
                            D.id_ditta,
                            D.descrizione_ditta,
                            REPLACE(D.qta_ord, '.', '.') as qta_ord,
                            REPLACE(D.qta_arr, '.', '.') as qta_arr,
                            REPLACE(ROUND((D.qta_arr * D.prz_dett_arr),4), '.', '.') as tot_riga,
                            DATE_FORMAT(D.data_inserimento,'%d/%m/%Y %k:%i') as data_inserimento
                  FROM retegas_dettaglio_ordini D
                  INNER JOIN retegas_ordini O on O.id_ordini=D.id_ordine
                  INNER JOIN maaking_users U on U.userid=D.id_utenti
                  INNER JOIN retegas_gas G on G.id_gas=U.id_gas
                  WHERE D.id_ordine=:id_ordine
                  $order_by ;";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll();

            foreach($rows as $row){
                reset($colonne);
                if($tipo_visualizza==="tabella"){
                    $l.='<tr>';
                }

                foreach($colonne as $nome_colonna => $valore_colonna){

                    if(in_array($nome_colonna,$values)){
                        if($tipo_visualizza==="tabella"){
                            if($nome_colonna=="prz_dett"){
                                $l.='<td data-tableexport-msonumberformat="\@">'.$row[$nome_colonna]."</td>";    
                            }elseif($nome_colonna=="prz_dett_arr"){
                                $l.='<td data-tableexport-msonumberformat="\@">'.$row[$nome_colonna]."</td>";    
                            }elseif($nome_colonna=="qta_ord"){
                                $l.='<td data-tableexport-msonumberformat="\@">'.$row[$nome_colonna]."</td>";    
                            }elseif($nome_colonna=="qta_arr"){
                                $l.='<td data-tableexport-msonumberformat="\@">'.$row[$nome_colonna]."</td>";    
                            }elseif($nome_colonna=="tot_riga"){
                                $l.='<td data-tableexport-msonumberformat="\@">'.$row[$nome_colonna]."</td>";    
                            }else{
                                $l.='<td>'.$row[$nome_colonna]."</td>";    
                            }
                            
                            
                            
                        }else{
                            $l.=$row[$nome_colonna]._USER_CSV_SEPARATOR." ";
                        }
                    }
                }

                if($tipo_visualizza==="tabella"){
                    $l.='</tr>';
                }else{
                    rtrim($l,_USER_CSV_SEPARATOR." ");
                     $l.="<br>";

                }

                $html .=$l;
                unset($l);
                //FINE DELLA RIGA

            }

            if($tipo_visualizza==="tabella"){
                $html.='</tbody></table>';
            }



            $res=array("result"=>"OK", "msg"=>"ok", "html"=>$html, "sql"=>$sql  );
            echo json_encode($res);
            die();
        break;

        case "do_metodo_scatole":
        $id_ordine = CAST_TO_INT($_POST["id_ordine"],0);
        $metodo_scatole = CAST_TO_INT($_POST["metodo_scatole"],0);

        if(posso_gestire_ordine($id_ordine)){
            $id_gas=_USER_ID_GAS;

            $sql="DELETE FROM retegas_options WHERE id_ordine=:id_ordine AND chiave='_ORDINE_METODO_SCATOLE' LIMIT 3;";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
            $stmt->execute();

            $sql="INSERT INTO retegas_options (id_ordine,chiave,valore_int) VALUES (:id_ordine,'_ORDINE_METODO_SCATOLE',:metodo_scatole);";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
            $stmt->bindParam(':metodo_scatole', $metodo_scatole, PDO::PARAM_INT);

            $stmt->execute();
            l_n($id_ordine,"Metodo scatole: ".$metodo_scatole);

            $res=array("result"=>"OK", "msg"=>"ok" );
        }else{
            $res=array("result"=>"KO", "msg"=>"Non puoi" );
        }
        echo json_encode($res);
        die();
    break;


        case "prendi_note_listino":

        $L = new listino($_POST["id"]);
        $res=array("result"=>"OK", "html"=>$L->note_listino );
        echo json_encode($res);
        break;

        case "ordine_preferito":
        //esiste
        $id_ordine=CAST_TO_INT($_POST["id_ordine"]);
        $id_user=_USER_ID;

        $stmt = $db->prepare("SELECT COUNT(valore_int) as conto FROM retegas_options
                             WHERE id_ordine=:id_ordine AND id_user=:id_user AND chiave='_USER_ORDINE_PREFERITO'");
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        if($row["conto"]>0){
            $stmt = $db->prepare("DELETE FROM retegas_options
                             WHERE id_ordine=:id_ordine AND id_user=:id_user AND chiave='_USER_ORDINE_PREFERITO'");
            $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
            $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
            $stmt->execute();
            $result="NO";
            $msg="Tolto dai preferiti";
        }else{
            $stmt = $db->prepare("INSERT INTO retegas_options
                                    (id_ordine,id_user,chiave,valore_int) VALUES (:id_ordine,:id_user,'_USER_ORDINE_PREFERITO',1)");
            $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
            $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
            $stmt->execute();
            $result="SI";
            $msg="Aggiunto ai preferiti";
        }

       $res=array("result"=>"OK", "msg"=>$msg, "preferito"=>$result );


       echo json_encode($res);
     break;


        case "do_richiesta_info":

        $messaggio_testo  = CAST_TO_STRING($_POST["messaggio"]);
        $id_ordine = CAST_TO_INT($_POST["id_ordine"],0);



        if($id_ordine==0){
            $res=array("result"=>"KO", "msg"=>"Manca id ordine."  );
            echo json_encode($res);die();
        }

        $O = new ordine($id_ordine);

        if($messaggio_testo==""){
            $res=array("result"=>"KO", "msg"=>"Messaggio vuoto."  );
            echo json_encode($res);die();
        }

        $rows = $O->EMAIL_lista_referenti();
        foreach($rows as $row){
            $lista_destinatari .= $row["name"]."<br>";
            $r[]=array( 'email' => $row["email"],
                    'name' => $row["name"]
                    );
        }

        $fullnameFROM = _USER_FULLNAME;
         $mailFROM = _USER_MAIL;
        $oggetto = "[reteDES] "._USER_FULLNAME." chiede qualcosa sull'ordine #".$O->id_ordini;
        $profile = new Template('../../email_rd4/comunica_ai_referenti.html');
        $profile->set("fullname", _USER_FULLNAME );
        $profile->set("id_ordine", $id_ordine );
        $profile->set("descrizione_ordine", $O->descrizione_ordini);
        $profile->set("messaggio_testo", $messaggio_testo);

        $messaggio = $profile->output();
        //echo $messaggio; die();

        SEmailMulti($r,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"V4richiestaInfo");
        //SSparkPostMulti($r,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"V4richiestaInfo");

        unset ($profile);


        $res=array("result"=>"OK", "msg"=>"Messaggio inviato a:<br>$lista_destinatari"  );

        echo json_encode($res);
        break;

        case "do_switch_listino":
            $id_ordine = CAST_TO_INT($_POST["id_ordine"],0);
            $id_listino = CAST_TO_INT($_POST["id_listino"],0);
            $tipo = CAST_TO_INT($_POST["tipo"],1,2);

            $O = new ordine($id_ordine);
            $listino_precedente = $O->descrizione_listini;

            $L = new listino($id_listino);
            $listino_attuale = $L->descrizione_listini;

            //SE L'ORDINE E' CONVALIDATO
            if($O->is_printable==1){
                $res=array("result"=>"KO", "msg"=>"Ordine già convalidato."  );
                echo json_encode($res);die();
            }

            //SE MANCA IL LISTINO
            if(($id_ordine==0)|($id_listino==0)){
                $res=array("result"=>"KO", "msg"=>"Manca listino"  );
                echo json_encode($res);die();
            }

            //SE E' LO STESSO LISTINO
            if($O->id_listini==$id_listino){
               $res=array("result"=>"KO", "msg"=>"Stesso listino di prima."  );
               echo json_encode($res);die();
            }

            if($tipo==1){

                $frase_tipo ='Gli articoli già in ordine non sono stati toccati';

                $sql = "UPDATE retegas_ordini SET id_listini=:id_listino WHERE id_ordini=:id_ordine LIMIT 1;";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':id_listino', $id_listino, PDO::PARAM_INT);
                $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
                $stmt->execute();

                $res=array("result"=>"OK", "msg"=>"Listino cambiato."  );
            }else{
                $i=0;
                //CAMBIO COMUNQUE IL LISTINO
                $sql = "UPDATE retegas_ordini SET id_listini=:id_listino WHERE id_ordini=:id_ordine LIMIT 1;";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':id_listino', $id_listino, PDO::PARAM_INT);
                $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
                $stmt->execute();


                //Passo tutti gli articoli del listino nuovo
                $sql = "SELECT * from retegas_articoli WHERE id_listini=:id_listino;";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':id_listino', $id_listino, PDO::PARAM_INT);
                $stmt->execute();
                $rows = $stmt->fetchAll();
                foreach($rows as $row){
                //Per ogni articolo
                    $sql="UPDATE retegas_dettaglio_ordini SET
                            prz_dett=:prezzo_articolo,
                            prz_dett_arr=:prezzo_articolo,
                            art_desc=:art_desc,
                            art_um=:art_um,
                            id_articoli=:id_articoli
                          WHERE
                            id_ordine=:id_ordine
                            AND
                            art_codice=:art_codice
                            ";
                    $art_codice = $row["codice"];
                    $id_articoli = $row["id_articoli"];
                    $art_um = $row["u_misura"]." ".$row["misura"];
                    $art_desc = $row["descrizione_articoli"];
                    $prezzo_articolo = $row["prezzo"];
                    //SE Codice nuovo = Codice in ordine
                    //CAMBIO PREZZO e DESCRIZIONE
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':art_codice', $art_codice, PDO::PARAM_STR);
                    $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
                    $stmt->bindParam(':id_articoli', $id_articoli, PDO::PARAM_INT);
                    $stmt->bindParam(':art_um', $art_um, PDO::PARAM_STR);
                    $stmt->bindParam(':art_desc', $art_desc, PDO::PARAM_STR);
                    $stmt->bindParam(':prezzo_articolo', $prezzo_articolo, PDO::PARAM_STR);

                    $stmt->execute();

                    if($stmt->rowCount()>0){
                        $i++;
                        $log .= 'Per articolo '.$art_codice.' - '.$art_desc.' modificate '.$stmt->rowCount().' righe.<br>';
                        //SEGNO LE RIGHE CAMBIATE
                    }
                }

                $frase_tipo ='Eventuali articoli già in ordine con lo stesso codice di articoli presenti nel nuovo listino sono stati aggiornati.<br>'.$log;
                $res=array("result"=>"OK", "msg"=>"Listino cambiato, e ritoccate $i righe di ordini già fatti"  );
            }

            //MAIL AI GESTORI SOLO SE L'ORDINE E' GIA' APERTO O CHIUSO
            //NON PROGRAMMATO
            if($O->codice_stato<>"PR"){

                $rows = $O->EMAIL_lista_referenti();
                foreach($rows as $row){
                    $lista_destinatari .= $row["fullname"]."<br>";
                    $r[]=array( 'email' => $row["email"],
                            'name' => $row["name"]
                            );
                }

                $fullnameFROM = _USER_FULLNAME;
                 $mailFROM = _USER_MAIL;
                $oggetto = "[reteDES] "._USER_FULLNAME." ha cambiato il listino dell'ordine #".$O->id_ordini;
                $profile = new Template('../../email_rd4/modifica_listino_ordine.html');
                $profile->set("autore_cambio", _USER_FULLNAME );
                $profile->set("id_ordine", $id_ordine );
                $profile->set("descrizione_ordine", $O->descrizione_ordini);
                $profile->set("frase_tipo", $frase_tipo);
                $profile->set("listino_precedente", $listino_precedente);
                $profile->set("listino_attuale", $listino_attuale);

                $messaggio = $profile->output();

                //SSparkPostMulti($r,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"V4switchListino");
                SEmailMulti($r,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"V4switchListino");
                
                unset ($profile);
            }

        echo json_encode($res);
        l_n($id_ordine,"Switch listino $listino_precedente -> $listino_attuale");
        die();

        break;
        case "comunica_ordine_lista":
        $tipo_mail = CAST_TO_INT($_POST["tipo_mail"]);
        $id_ordine = CAST_TO_INT($_POST["id_ordine"]);
        $id_gas = _USER_ID_GAS;

        if($tipo_mail==1){

        $sql = "SELECT U.fullname, U.tel, U.userid, U.email
            FROM retegas_dettaglio_ordini D
            INNER JOIN maaking_users U ON U.userid = D.id_utenti
            WHERE D.id_ordine=:id_ordine
            AND U.isactive=1
            AND U.id_gas=:id_gas
            GROUP BY U.userid";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);

        }
        if($tipo_mail==2){

        $sql = "SELECT U.fullname, U.tel, U.userid, U.email
            FROM retegas_dettaglio_ordini D
            INNER JOIN maaking_users U ON U.userid = D.id_utenti
            WHERE D.id_ordine=:id_ordine
            AND U.isactive=1
            GROUP BY U.userid";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);

        }

        if($tipo_mail==3){

        $sql = "SELECT
                maaking_users.fullname,
                maaking_users.email,
                maaking_users.user_site_option,
                retegas_referenze.id_gas_referenze,
                retegas_gas.descrizione_gas,
                maaking_users.userid
                FROM
                retegas_ordini
                Inner Join retegas_referenze ON retegas_ordini.id_ordini = retegas_referenze.id_ordine_referenze
                Inner Join maaking_users ON retegas_referenze.id_gas_referenze = maaking_users.id_gas
                Inner Join retegas_gas ON retegas_referenze.id_gas_referenze = retegas_gas.id_gas
                WHERE
                retegas_ordini.id_ordini =:id_ordine
                AND
                maaking_users.id_gas=:id_gas
                AND
                maaking_users.isactive = 1;";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);

        }
        if($tipo_mail==4){

        $sql = "SELECT
                maaking_users.fullname,
                maaking_users.email,
                maaking_users.user_site_option,
                retegas_referenze.id_gas_referenze,
                retegas_gas.descrizione_gas,
                maaking_users.userid
                FROM
                retegas_ordini
                Inner Join retegas_referenze ON retegas_ordini.id_ordini = retegas_referenze.id_ordine_referenze
                Inner Join maaking_users ON retegas_referenze.id_gas_referenze = maaking_users.id_gas
                Inner Join retegas_gas ON retegas_referenze.id_gas_referenze = retegas_gas.id_gas
                WHERE
                retegas_ordini.id_ordini =:id_ordine
                AND
                maaking_users.isactive = 1;";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);

        }

        $dest=0;
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($rows AS $rowU){
            $src = src_user($rowU["userid"]);
            $h.='<span class="label label-default"><img class="img" SRC='.$src.' style="width:16px;margin-right:2px;margin-top:2px;">'.utf8_encode($rowU["fullname"]).'</span> ';
            $dest ++;
        }

        $res=array("result"=>"OK", "msg"=>"OK", "html"=>$h, "n"=>"<strong>".$dest."</strong> utenti."  );
        echo json_encode($res);

        die();
        break;

    case "comunica_ordine":
        $tipo_mail = CAST_TO_INT($_POST["tipo_mail"],0);
        $id_ordine = CAST_TO_INT($_POST["id_ordine"],0);
        $id_gas = _USER_ID_GAS;
        $messaggio = clean(CAST_TO_STRING($_POST["messaggio"]));

        if($id_ordine==0){
            $res=array("result"=>"KO", "msg"=>"KO");
            echo json_encode($res);
            die();
        }
        if($messaggio==""){
            $res=array("result"=>"KO", "msg"=>"Manca il testo del messaggio");
            echo json_encode($res);
            die();
        }


        $O = new ordine($id_ordine);

        if($tipo_mail==1){

        $sql = "SELECT U.fullname, U.tel, U.userid, U.email
            FROM retegas_dettaglio_ordini D
            INNER JOIN maaking_users U ON U.userid = D.id_utenti
            WHERE D.id_ordine=:id_ordine
            AND U.isactive=1
            AND U.id_gas=:id_gas
            GROUP BY U.userid";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);

        }

        if($tipo_mail==2){

        $sql = "SELECT U.fullname, U.tel, U.userid, U.email
            FROM retegas_dettaglio_ordini D
            INNER JOIN maaking_users U ON U.userid = D.id_utenti
            WHERE D.id_ordine=:id_ordine
            AND U.isactive=1
            GROUP BY U.userid";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);

        }

        if($tipo_mail==3){

        $sql = "SELECT
                maaking_users.fullname,
                maaking_users.email,
                maaking_users.user_site_option,
                retegas_referenze.id_gas_referenze,
                retegas_gas.descrizione_gas,
                maaking_users.userid
                FROM
                retegas_ordini
                Inner Join retegas_referenze ON retegas_ordini.id_ordini = retegas_referenze.id_ordine_referenze
                Inner Join maaking_users ON retegas_referenze.id_gas_referenze = maaking_users.id_gas
                Inner Join retegas_gas ON retegas_referenze.id_gas_referenze = retegas_gas.id_gas
                WHERE
                retegas_ordini.id_ordini =:id_ordine
                AND
                maaking_users.id_gas=:id_gas
                AND
                maaking_users.isactive = 1;";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);

        }

        if($tipo_mail==4){

        $sql = "SELECT
                maaking_users.fullname,
                maaking_users.email,
                maaking_users.user_site_option,
                retegas_referenze.id_gas_referenze,
                retegas_gas.descrizione_gas,
                maaking_users.userid
                FROM
                retegas_ordini
                Inner Join retegas_referenze ON retegas_ordini.id_ordini = retegas_referenze.id_ordine_referenze
                Inner Join maaking_users ON retegas_referenze.id_gas_referenze = maaking_users.id_gas
                Inner Join retegas_gas ON retegas_referenze.id_gas_referenze = retegas_gas.id_gas
                WHERE
                retegas_ordini.id_ordini =:id_ordine
                AND
                maaking_users.isactive = 1;";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);

        }

        $dest=0;
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($rows AS $rowU){
            $r[]=array( 'email' => $rowU["email"],
                        'name' => utf8_encode($rowU["fullname"]),
                        'type' => 'bcc');
            $dest ++;
        }
        //MAIL------------------------------------------------
        $fullnameFROM = _USER_FULLNAME;
        $mailFROM = _USER_MAIL;


        $oggetto = "[reteDES] Comunicazione ordine #".$id_ordine." (".$O->descrizione_ordini.")";
        $profile = new Template('../../email_rd4/comunicazione_ordine.html');

        $profile->set("referente_ordine", _USER_FULLNAME );
        $profile->set("gas_referente_ordine", $O->descrizione_gas_referente );
        $profile->set("ditta", $O->descrizione_ditte );
        $profile->set("url_ordine",APP_URL.'/#ajax_rd4/ordini/ordine.php?id='.$id_ordine);
        $profile->set("url_ditta",APP_URL.'/#ajax_rd4/fornitori/scheda.php?id='.$O->id_ditte);
        $profile->set("id_ditta", $O->id_ditte);
        $profile->set("id_ordine", $id_ordine );
        $profile->set("saluto", "Ciao" );
        $profile->set("data_chiusura", $O->data_chiusura_lunga );
        $profile->set("descrizione_ordine", $O->descrizione_ordini );
        $profile->set("messaggio", $messaggio );
        $messaggio = $profile->output();
        //echo $r4." ".$fullnameFROM."<br> ".$mailFROM."<br> ".$oggetto."<br> ".$messaggio."<br>AperturaOrdine<hr>";
        SEmailMulti($r,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"ComunicazioneOrdine");
        unset ($profile);



        $res=array("result"=>"OK", "msg"=>"Mail inviate" );
        echo json_encode($res);

        die();
        break;

        case "elimina_referente_extra":
        $id_utente = CAST_TO_INT($_POST["id_utente"],0);
        $id_ordine = CAST_TO_INT($_POST["id_ordine"],0);
        $stmt = $db->prepare("DELETE FROM retegas_options WHERE id_user=:id_utente AND id_ordine=:id_ordine AND chiave='_REFERENTE_EXTRA' LIMIT 1;");
        $stmt->bindParam(':id_utente', $id_utente, PDO::PARAM_INT);
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->execute();
        $res=array("result"=>"OK", "msg"=>"Eliminato" );
        echo json_encode($res);
        l_n($id_ordine,"Eliminato referente extra #$id_utente");
        die();
        break;

        case "show_lista_referenti_extra":

        $id_ordine = CAST_TO_INT($_POST["id_ordine"],0);
        $O = new ordine($id_ordine);

        $rows = $O->lista_referenti_extra();
        if(count($rows)>0){
            foreach($rows as $row){
               $userGestoriExtraidEnc = $converter->encode($row["id_user"]);
               $gestori_extra .= '<li>
                                <span class="read">
                                    <span class="pull-right text-danger delete_referenza_extra" style="cursor:pointer;"  data-id_utente="'.$row["id_user"].'"><i class="fa fa-times"></i></span>
                                    <a href="#ajax_rd4/user/scheda.php?id='.$userGestoriExtraidEnc.'" class="msg">
                                        <img src="'.src_user($row["id_user"]).'" alt="" class="air air-top-left margin-top-5" width="32" height="32" />
                                        <span class="subject"><strong>'.$row["fullname"].'</strong> <i class="font-xs">'.$row["descrizione_gas"].'</i></span>
                                        <span class="msg-body font-xs">'.$row["valore_text"].'</span>
                                    </a>
                                </span>
                            </li>';
            }

        }else{
            $gestori_extra = '<li>
                                <span class="read">
                                    <p class="text-center"><i class="fa fa-fw fa-frown-o"></i> Nessuno.</p>
                                </span>
                            </li>';
        }
        $res=array("result"=>"OK", "msg"=> $gestori_extra );
        echo json_encode($res);
        die();

        break;

        case "nuovo_referente_extra":
        $id_utente=CAST_TO_INT($_POST["idutente"]);
        $id_ordine=CAST_TO_INT($_POST["id_ordine"]);
        $ruolo=clean(CAST_TO_STRING($_POST["messaggio"]));

        //CONTROLLO SE POSSO
        if (!posso_gestire_ordine(CAST_TO_INT($_POST['id_ordine']))){
            echo json_encode(array("result"=>"KO", "msg"=>"Non hai i permessi necessari" ));
            die();
         }
        //CANCELLO EVENTUALE VECCHIO RECORD
        $qry = "DELETE FROM retegas_options
                WHERE
                id_user = :id_utente
                AND
                id_ordine = :id_ordine
                AND
                chiave = '_REFERENTE_EXTRA';";
        $stmt = $db->prepare($qry);
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->bindParam(':id_utente', $id_utente, PDO::PARAM_INT);
        $stmt->execute();

        //CREAZIONE CODICE DI CANCELLAZIONE
        $code = rand(10000000,99999999);
        $codeLINK = APP_URL.'/ajax_rd4/?do=r&c='.$code;

        //AGGIUNGO REFERENTE
        $qry = "INSERT INTO
                    `retegas_options` (
                        `id_option` ,
                        `id_user` ,
                        `id_ordine` ,
                        `chiave` ,
                        `valore_text` ,
                        `timbro` ,
                        `valore_int` ,
                        `valore_real` ,
                        `note_1`
                        )
                    VALUES (
                             NULL ,
                             :id_utente,
                             :id_ordine,
                             '_REFERENTE_EXTRA',
                             :ruolo,
                             CURRENT_TIMESTAMP,
                             :code,
                             NULL ,
                             ''
                    );";
        $stmt = $db->prepare($qry);
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->bindParam(':id_utente', $id_utente, PDO::PARAM_INT);
        $stmt->bindParam(':ruolo', $ruolo, PDO::PARAM_STR);
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->execute();

        //MAIL AL PRESCELTO CON MESSAGGIO
        $mailFROM = _USER_MAIL;
        $fullnameFROM = _USER_FULLNAME;

        $U = new user($id_utente);
        $O = new ordine($id_ordine);

        $mailTO = $U->email;
        $fullnameTO = $U->fullname;;

        $oggetto = "[reteDES] ".$fullnameFROM." vorrebbe che lo aiutassi a gestire un ordine.";
        $profile = new Template('../../email_rd4/nuovo_referente_extra.html');

        $profile->set("fullnameFROM", $fullnameFROM );
        $profile->set("id_ordine", $O->id_ordini );
        $profile->set("descrizione_ordine", $O->descrizione_ordini);
        $profile->set("link_ordine", APP_URL.'/gas4/#ajax_rd4/ordini/ordine.php?id='.$O->id_ordini);
        $profile->set("ruolo", $ruolo );
        $profile->set("link_code", $codeLINK );

        $messaggio = $profile->output();

        if(SEmail($fullnameTO,$mailTO,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"ReferenteEXTRA")){
            $res=array("result"=>"OK", "msg"=>"$fullnameTO inserito nella lista referenti.<br>Mail inviata." );
            echo json_encode($res);
            l_n($id_ordine,"Nuovo referente extra #$id_utente");

            die();
            break;
        }else{
            $res=array("result"=>"KO", "msg"=>"Utente inserito, ma mail non inviata." );
            echo json_encode($res);
            die();
            break;
        }



        $arr = array("result"=>"OK","msg"=>"Test OK");
        echo json_encode($arr);

        break;

        case "update_status_scatole":

        //SCATOLE PIENE per COMPRA FULLWIDTH
        $id_ordine = CAST_TO_INT($_POST["id_ordine"]);
        $id_articolo = CAST_TO_INT($_POST["id_articolo"]);
        $mode = CAST_TO_STRING($_POST["mode"]);



        $O=new ordine($id_ordine);

        //BINOTTO
        //$scatole_intere = (int)         QTA_SCATOLE_INTERE_ARTICOLO_ORDINE($id_articolo,$O->id_ordini);
        //$avanzo_articolo = (float)round(QTA_SCATOLA_AVANZO_ARTICOLO_ORDINE($id_articolo,$O->id_ordini),2);

        if($O->metodo_scatole==0){
        //if(($id_ordine<>9881) AND ($id_ordine<>9814)){
            $scatole_intere = (int)         QTA_SCATOLE_INTERE_ARTICOLO_ORDINE($id_articolo,$O->id_ordini);
            $avanzo_articolo = (float)round(QTA_SCATOLA_AVANZO_ARTICOLO_ORDINE($id_articolo,$O->id_ordini),2);
        }else{
            $scatole_intere = (int)         QTA_SCATOLE_INTERE_ARTICOLO_ORDINE_GAS($id_articolo,$O->id_ordini,_USER_ID_GAS);
            $avanzo_articolo = (float)round(QTA_SCATOLA_AVANZO_ARTICOLO_ORDINE_GAS($id_articolo,$O->id_ordini,_USER_ID_GAS),2);
        }



        $qta_ordinata_user =            QTA_ORDINATA_ORDINE_ARTICOLO_USER($O->id_ordini,$id_articolo,_USER_ID);
        $stmt = $db->prepare("SELECT * FROM retegas_articoli WHERE id_articoli=:id_articolo LIMIT 1;");
        $stmt->bindParam(':id_articolo', $id_articolo, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $qta_scatola = $row["qta_scatola"];


        $per_completare_scatola ="";
        if($scatole_intere==0){
            //Se ? la prima scatola
            if($avanzo_articolo>0){
                $per_completare_scatola = ($qta_scatola - $avanzo_articolo);
                if($qta_ordinata_user>0){
                    //Se sono io che ho ordinato
                    $colore = "text-danger";
                    $per_completare_scatola_m = $per_completare_scatola;
                    $per_completare_scatola = "<strong class=\"$colore font-md\">$per_completare_scatola</strong> per chiudere la prima scatola";
                }else{
                    //Se sono altri che hanno ordinato
                    $colore = "text-warning";
                    $per_completare_scatola_m = $per_completare_scatola;
                    $per_completare_scatola = "<strong class=\"$colore font-md\">$per_completare_scatola</strong> per chiudere la prima scatola";
                }
            }else{
               // Nessun articolo ordinato da nessuno
               $per_completare_scatola = "Nessuna scatola da riempire!";
               $colore ="";
            }
        }else{
            //Se ci sono già scatole
            if($avanzo_articolo>0){
                $per_completare_scatola = ($qta_scatola - $avanzo_articolo);
                if($qta_ordinata_user>0){
                    $colore = "text-danger";
                    $per_completare_scatola_m = $per_completare_scatola;
                    $per_completare_scatola = "<strong class=\"$colore font-md\">$per_completare_scatola</strong> per chiudere un'altra scatola";
                }else{
                    //Se sono altri che hanno ordinato
                    $colore = "text-warning";
                    $per_completare_scatola_m = $per_completare_scatola;
                    $per_completare_scatola = "<strong class=\"$colore font-md\">$per_completare_scatola</strong> per chiudere un'altra scatola";
                }
            }else{
               // Nessun articolo ordinato da nessuno
               $colore ="";
               $per_completare_scatola_m = "";
               $per_completare_scatola = "Nessuna scatola da riempire!";

            }

        }
         //INFO SU ARTICOLO
         $stmt = $db->prepare("SELECT * from retegas_articoli where id_articoli='".$id_articolo."' LIMIT 1;");
         $stmt->execute();
         $row = $stmt->fetch(PDO::FETCH_ASSOC);
         $um = $row["u_misura"]." <b class=\"text-info\">".$row["misura"]."</b> per <span class=\"text-danger\"><b>"._NF($row["prezzo"])."</b> Eu.</span>";
         $scat ="<small>Scat. da <b class=\"text-info\">"._NF($row["qta_scatola"])."</b>, Min. <b class=\"text-info\">"._NF($row["qta_minima"])."</b></small>";
         $note = '<hr>Note:<br><small>'.clean($row["articoli_note"]).'</small><br>';

        if($mode<>"m"){
            //echo $um.'<br>'.$scat.'<br>'.$per_completare_scatola;
            echo $per_completare_scatola;
        }else{

            echo $um.'<br>'.$scat.'<br>'.$per_completare_scatola_m.$note;
        }//PER COMPLETARE SCATOLA

        break;

        case "del_miaspesa":
        $id_ordine = CAST_TO_INT($_POST["id_ordine"]);
        $check = DO_CHECK_USER_PARTECIPA_ORDINE($id_ordine);
        
        if($check=="OK"){
            //CANCELLA NETTO
            DO_DELETE_ORDINE_UTENTE($id_ordine,_USER_ID);
            $arr = array("result"=>"OK","msg"=>"Spesa eliminata.<br>Ricarica la pagina per vedere la tua cassa aggiornata.");
            l($id_ordine,"Eliminazione spesa.");
            echo json_encode($arr);
            break;
        }else{
            $arr = array("result"=>"KO","msg"=>$check);
            echo json_encode($arr);
            break;    
        }

        

        
        break;

        case "show_miaspesa":
        $id_ordine = CAST_TO_INT($_POST["id_ordine"]);

        $O = new ordine($id_ordine);
        //-----------------------------------CONTENT
        $html='<table id="table_miaspesa" class="table table-condensed" style="margin-left: auto; margin-right: auto">
                    <thead >
                        <tr class="intestazione" >
                            <th style="width:10%;">Codice</th>
                            <th >Descrizione</th>
                            <th style="width:5%;">Assegnazione</th>
                            <th style="width:5%;">Ordinati</th>
                            <th style="width:10%;">Prezzo</th>
                            <th style="width:5%;">Totale articoli</th>
                        </tr>
                    </thead>
                    <tbody>';
        //-----------------------------------DATI

        //$sql = "SELECT art_codice, art_desc, art_um, qta_ord, qta_arr, prz_dett_arr, prz_dett, descrizione_ditta FROM retegas_dettaglio_ordini WHERE id_ordine=:id_ordine AND id_utenti='"._USER_ID."'";
        $sql = "SELECT AMI.nome, D.art_codice, D.art_desc, D.art_um, A.qta_ord, D.prz_dett_arr, D.descrizione_ditta FROM retegas_dettaglio_ordini D left join retegas_distribuzione_spesa A on A.id_riga_dettaglio_ordine=D.id_dettaglio_ordini LEFT JOIN retegas_amici AMI on AMI.id_amici=A.id_amico WHERE D.id_ordine=:id_ordine AND D.id_utenti='"._USER_ID."'";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_ordine', $O->id_ordini, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($rows AS $row){
            $riga++;
            if(is_integer($riga/2)){
                $row_class="odd";
            }else{
                $row_class="even";
            }


            $totale_articoli = round($row["qta_ord"]*$row["prz_dett_arr"],4);

            $qta_ord = $row["qta_ord"];

            $label="";
            $label_prz ="";

            if($O->is_multiditta){
                $md=' <i class="text-primary">'.$row["descrizione_ditta"].'</i>';
            }else{
                $md='';
            }

            if(CAST_TO_STRING(trim($row["nome"]))==""){
                $nome = "me stesso";
            }else{
                $nome =  $row["nome"];
            }

            $html.='<tr class="'.$row_class.'">';
                $html.='<td>'.$row["art_codice"].'</td>';
                $html.='<td>'.$row["art_desc"].' <span class="note">('.$row["art_um"].')'.$md.'</span></td>';
                $html.='<td>'.$nome.'</td>';
                $html.='<td style="text-align:center">'._NF($qta_ord).'</td>';
                $html.='<td style="text-align:right">'._NF($row["prz_dett_arr"]).' &#0128;'.$label_prz.'</td>';
                $html.='<td style="text-align:right"><strong>'._NF($totale_articoli).' &#0128;</strong></td>';
            $html.='</tr>';

            $totaleA += round($totale_articoli,4);

        }
            if($totaleC==0){$totaleC="";}
            $totaleT = $totaleA+$totaleC;
        //-----------------------------------DATI
        $html.='    </tbody>
                    <tfoot>
                        <tr class="totale">
                            <th colspan=5 style="text-align:right">Totali</th>
                            <th style="text-align:right"><strong>'.number_format($totaleA,2, ',', '').' &#0128;</strong></th>
                        </tr>
                    </tfoot>
                 </table>';
        //-----------------------------------FOOTER

        $arr = array("result"=>"OK","html"=>$html);
        echo json_encode($arr);
        break;

        case "elimina_prenotazione":
            $id_ordine = CAST_TO_INT($_POST["id_ordine"]);
            DO_DELETE_ORDINE_UTENTE($id_ordine,_USER_ID);

            $stmt = $db->prepare("DELETE FROM retegas_options
                                WHERE
                                chiave='PRENOTAZIONE_ORDINI'
                                AND
                                id_user='"._USER_ID."'
                                AND
                                id_ordine=:id_ordine
                                LIMIT 1;");
            $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
            $stmt->execute();
            $arr = array("result"=>"OK","msg"=>"Prenotazione eliminata;");
            l($id_ordine,"Eliminazione prenotazione");

            echo json_encode($arr);
        break;
        case "conferma_prenotazione":
            $id_ordine = CAST_TO_INT($_POST["id_ordine"]);

            $value_ordine = VA_ORDINE_USER($id_ordine,_USER_ID);
            $value_cassa = VA_CASSA_SALDO_UTENTE_TOTALE(_USER_ID);
            //SE IL GAS NON SCALA LA CASSA SU UTENTE

            $is_ok = DO_CHECK_USER_CASSA_ORDINE($valore_ordine,$id_ordine);

            if($is_ok=="SI"){
               //Aggiorno CASSA
               if(!_GAS_CASSA_ORDINI_SCASSATI){
                    if(_USER_USA_CASSA){
                        //SE L'ORDINE E' IN MODALITA' PRENOTAZIONE ALLORA SALTA L'AGGIORNAMENTO DELLA CASSA
                        $res = DO_CASSA_UPDATE_ORDINE_UTENTE($id_ordine,_USER_ID);
                        //$arr = array("result"=>"KO","msg"=>$res);echo json_encode($arr);break;

                    }
                }
                //cancello la prenotazione
                $stmt = $db->prepare("DELETE FROM retegas_options
                                WHERE
                                chiave='PRENOTAZIONE_ORDINI'
                                AND
                                id_user='"._USER_ID."'
                                AND
                                id_ordine=:id_ordine
                                LIMIT 1;");
                $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
                $stmt->execute();

                $arr = array("result"=>"OK","msg"=>"Prenotazione confermata. Ricarica la pagina per vedere i totali aggiornati.");
                l($id_ordine,"Conferma prenotazione OK");
            }else{
                $arr = array("result"=>"KO","msg"=>$is_ok);
                l($id_ordine,"Conferma prenotazione NEGATA");
            }



            echo json_encode($arr);
        break;

        case "attiva_prenotazione":
            $id_ordine = CAST_TO_INT($_POST["id_ordine"]);
            DO_DELETE_ORDINE_UTENTE($id_ordine,_USER_ID);

            $stmt = $db->prepare("DELETE FROM retegas_options
                                WHERE
                                chiave='PRENOTAZIONE_ORDINI'
                                AND
                                id_user='"._USER_ID."'
                                AND
                                id_ordine=:id_ordine
                                LIMIT 1;");
            $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
            $stmt->execute();

            //esiste
            $stmt = $db->prepare("INSERT INTO retegas_options (id_user,
                                                chiave,
                                                valore_text,
                                                id_gas,
                                                id_ordine,
                                                valore_int)
                                                VALUES (
                                                "._USER_ID.",
                                                'PRENOTAZIONE_ORDINI',
                                                'SI',
                                                "._USER_ID_GAS.",
                                                :id_ordine,
                                                0);");

            $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
            $stmt->execute();

            $arr = array("result"=>"OK","msg"=>"Prenotazione attivata;");
            l($id_ordine,"Prenotazione ATTIVATA");

            echo json_encode($arr);


        break;

        case "salva_nota_ordine":

            $id_ordine = CAST_TO_INT($_POST["id_ordine"]);
            $O = new ordine($id_ordine);

            if(VA_ORDINE_USER($O->id_ordini,_USER_ID)>0){
                $O->set_note_utente(_USER_ID,$_POST["nota_ordine"]);
                $arr = array("result"=>"OK","msg"=>"Nota aggiornata.");
            }else{
                $arr = array("result"=>"KO","msg"=>"Non hai ancora acquistato nulla");
            }
            echo json_encode($arr);
            break;

        case "add_articolo_ordine":

            $id_ordine = CAST_TO_INT($_POST["id_ordine"]);
            $id_articolo = CAST_TO_INT($_POST["id_articolo"]);
            $id_articolo_temp = CAST_TO_INT($_POST["id_articolo_temp"]);
            $id_amico= CAST_TO_INT($_POST["id_amico"]);
            //inserimento quantità manuale
            $qta = CAST_TO_STRING($_POST["qta"]);

            $check = DO_CHECK_USER_PARTECIPA_ORDINE($id_ordine);
            
            if($check<>"OK"){
                $arr = array("result"=>"KO","msg"=>$check,"value_ordine"=>$value_ordine,"value_cassa"=>$value_cassa,"value_articolo_new"=>$valore_articolo,"importo_articolo_new"=>0);
                echo json_encode($arr);
                break;
            }

            
            $value_ordine = VA_ORDINE_USER($id_ordine,_USER_ID);
            $value_cassa = VA_CASSA_SALDO_UTENTE_TOTALE(_USER_ID);

            $O = new ordine($id_ordine);
            $tipo_listino = $O->get_tipo_listino();
            
            if($tipo_listino=="ORD"){
                $A = new articolo_temp($id_articolo_temp);
                $qta_articolo = QTA_ORDINATA_ORDINE_ARTICOLO_USER($id_ordine,$id_articolo_temp,_USER_ID);
            }else{
                $A = new articolo($id_articolo);
                $qta_articolo = QTA_ORDINATA_ORDINE_ARTICOLO_USER($id_ordine,$id_articolo,_USER_ID);    
            }
            
            
            
            
            //$arr = array("result"=>"KO","msg"=>$A->id_articoli);echo json_encode($arr);break;


            $prezzo = $A->prezzo;
            $q_min = $A->qta_minima;
            $valore_ordine_new = $value_ordine + ($prezzo*$q_min);

            //SE IL GAS NON SCALA LA CASSA SU UTENTE
            if(_GAS_CASSA_ORDINI_SCASSATI){
                $is_ok="SI";
            }else{
                $is_ok = DO_CHECK_USER_CASSA_ORDINE($valore_ordine_new,$id_ordine);
            }
            if($is_ok<>"SI"){
               // log_me($id_ordine,_USER_ID,"GS3","KO","CASSA : false",0,"Ordine : $id_ordine, articolo : $id_articolo");
                $arr = array("res"=>"KO","msg"=>$is_ok,"value_ordine"=>round($value_ordine,2),"value_cassa"=>round($value_cassa,2),"value_articolo_new"=>round($valore_articolo,2),"importo_articolo_new"=>$valore_articolo);
                echo json_encode($arr);
                break;
            }

            $descrizione_attuale = $A->descrizione_articoli;
            $codice_attuale = $A->codice;
            $udm_attuale = $A->u_misura." ".$A->misura;
            $id_ditta = $A->id_ditta;
            $descrizione_ditta=$A->descrizione_ditta;
            //$ingombro = ($A->ingombro*$qta);
            $ingombro = $A->ingombro;

            if(($qta_articolo==0) OR ($A->articoli_unico==1)){
                //E' un univoco o è il primo

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

              if($stmt->rowCount()<>1){
                $arr = array("res"=>"KO","msg"=>"Articolo non aggiunto;","value_ordine"=>round($value_ordine,2),"value_cassa"=>round($value_cassa,2),"value_articolo_new"=>round($valore_articolo,2),"importo_articolo_new"=>$valore_articolo);
                echo json_encode($arr);
                break;    
              }
              
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

            }else{
                //E' normale
                //Trovo id dettaglio
                if($tipo_listino=="ORD"){
                    $sql = "SELECT * from retegas_dettaglio_ordini WHERE id_ordine='$id_ordine' AND id_utenti='"._USER_ID."' AND id_articoli='$id_articolo_temp';";
                }else{
                    $sql = "SELECT * from retegas_dettaglio_ordini WHERE id_ordine='$id_ordine' AND id_utenti='"._USER_ID."' AND id_articoli='$id_articolo';";
                }
                $stmt = $db->prepare($sql);
                if($tipo_listino=="ORD"){
                    $stmt->bindParam(':id_articolo', $id_articolo_temp, PDO::PARAM_INT);
                }else{
                    $stmt->bindParam(':id_articolo', $id_articolo, PDO::PARAM_INT);    
                }
                $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
                $stmt->execute();
                if($stmt->rowCount()<>1){
                    $arr = array("res"=>"KO","msg"=>"Più di un dettaglio ordine :(","value_ordine"=>round($value_ordine,2),"value_cassa"=>round($value_cassa,2),"value_articolo_new"=>round($valore_articolo,2),"importo_articolo_new"=>$valore_articolo);
                    echo json_encode($arr);
                    break;
                }
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $id_dettaglio = $row["id_dettaglio_ordini"];

                //Aggiungo una qmin
                $sql = "UPDATE retegas_dettaglio_ordini SET qta_ord=qta_ord+:q_min_1, qta_arr=qta_arr+:q_min_2 WHERE id_dettaglio_ordini=:id_dettaglio LIMIT 1;";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':q_min_1', $q_min, PDO::PARAM_STR);
                $stmt->bindParam(':q_min_2', $q_min, PDO::PARAM_STR);
                $stmt->bindParam(':id_dettaglio', $id_dettaglio, PDO::PARAM_INT);
                $stmt->execute();


                //VEDO SE l'amico ha già una Q per quell'id dettaglio
                $sql = "SELECT * from retegas_distribuzione_spesa WHERE id_ordine='$id_ordine' AND id_user='"._USER_ID."' AND id_riga_dettaglio_ordine='$id_dettaglio' and id_amico='$id_amico';";
                $stmt = $db->prepare($sql);
                $stmt->execute();
                if($stmt->rowCount()>0){
                    //Aggiungo una qmin
                    $sql = "UPDATE retegas_distribuzione_spesa SET qta_ord=qta_ord+:q_min_1, qta_arr=qta_arr+:q_min_2 WHERE id_riga_dettaglio_ordine=:id_dettaglio AND id_user='"._USER_ID."' AND id_amico='$id_amico' LIMIT 1; ";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':q_min_1', $q_min, PDO::PARAM_STR);
                    $stmt->bindParam(':q_min_2', $q_min, PDO::PARAM_STR);
                    $stmt->bindParam(':id_dettaglio', $id_dettaglio, PDO::PARAM_INT);
                    $stmt->execute();
                }else{
                    //inserisco una qmin
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
                    $stmt->bindParam(':last_id', $id_dettaglio, PDO::PARAM_INT);
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
                }



            }

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
            $valore_riga = ($qta_articolo + $q_min)*$prezzo;

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
                            "qta_articolo_new"=>$qta_articolo + $q_min,
                            "amici_coinvolti"=>$amici_coinvolti,
                            "importo_articolo_new"=>$valore_riga);

            l($id_ordine,"Aggiunta + n.$qta art.$codice_attuale ($descrizione_attuale) valore ordine: $valore_ordine_new");
            echo json_encode($arr);
        break;
        case "delete_articolo_ordine":

            $id_ordine = CAST_TO_INT($_POST["id_ordine"]);
            $O = new ordine($id_ordine);
            
            $check = DO_CHECK_USER_PARTECIPA_ORDINE($id_ordine);
            
            if($check<>"OK"){
                $arr = array("result"=>"KO","msg"=>$check,"value_ordine"=>$value_ordine,"value_cassa"=>$value_cassa,"value_articolo_new"=>$valore_articolo,"importo_articolo_new"=>0);
                echo json_encode($arr);
                break;
            }
            
            $id_articolo = CAST_TO_INT($_POST["id_articolo"]);
            $id_articolo_temp = CAST_TO_INT($_POST["id_articolo_temp"]);
            
            if($O->get_tipo_listino()=="ORD"){
                $A=new articolo_temp($id_articolo_temp);
                $codice = $A->codice;
                $descrizione = $A->descrizione_articoli;
                DO_DELETE_ARTICOLO_ORDINE_UTENTE($id_articolo_temp,$id_ordine,_USER_ID);
            }else{
                $A=new articolo($id_articolo);
                $codice = $A->codice;
                $descrizione = $A->descrizione_articoli;
                DO_DELETE_ARTICOLO_ORDINE_UTENTE($id_articolo,$id_ordine,_USER_ID);
            }
            
            
            
            

            $value_ordine = VA_ORDINE_USER($id_ordine,_USER_ID);
            $value_cassa = VA_CASSA_SALDO_UTENTE_TOTALE(_USER_ID);

            $qta_articolo_new =0;
            $importo_articolo_new =0;

            $res=array( "result"=>"OK",
                        "value_ordine"=>$value_ordine,
                        "value_cassa"=>$value_cassa,
                        "qta_articolo_new"=>$qta_articolo_new,
                        "importo_articolo_new"=>$importo_articolo_new );

            l($id_ordine,"Eliminazione articolo $codice ($descrizione) valore ordine: $value_ordine");
            echo json_encode($res);
        break;

        case "distribuzione_gas":


         //se posso
         $luogo_consegna=CAST_TO_STRING($_POST['consegna_gas']);
         $lat=CAST_TO_FLOAT($_POST['lat']);
         $lng=CAST_TO_FLOAT($_POST['lng']);
         $note_consegna=CAST_TO_STRING($_POST['note_consegna_gas']);
         $data_consegna_start=CAST_TO_STRING($_POST['data_consegna_gas']);
         $data_consegna_start= conv_date_to_db($data_consegna_start);

         $id_ordini=CAST_TO_INT($_POST['id_ordine']);
         $id_gas = _USER_ID_GAS;

         //Controllo se posso
         $stmt = $db->prepare("SELECT id_utente_referenze FROM retegas_referenze WHERE id_ordine_referenze=:id_ordine_referenze AND id_gas_referenze=:id_gas_referenze LIMIT 1;");
         $stmt->bindParam(':id_ordine_referenze', $id_ordini, PDO::PARAM_INT);
         $stmt->bindParam(':id_gas_referenze', $id_gas, PDO::PARAM_INT);
         $stmt->execute();
         $row = $stmt->fetch(PDO::FETCH_ASSOC);
         $ok = false;
         $res=array("result"=>"KO", "msg"=>"Non hai i permessi necessari" );
         if($row["id_utente_referenze"]>0){
             if(_USER_ID==$row["id_utente_referenze"]){
                $ok=true;
             }
             if(_USER_PERMISSIONS & perm::puo_vedere_tutti_ordini){
                $ok=true;
             }
         }else{
             $res=array("result"=>"KO", "msg"=>"Non esiste ancora un referente ordine per il tuo GAS." );
         }

         if (!$ok){
             echo json_encode($res);
             die();
         }

         $stmt = $db->prepare("UPDATE retegas_referenze
                                SET
                                luogo_distribuzione = :luogo_distribuzione,
                                lat_distribuzione = :lat,
                                lng_distribuzione = :lng,
                                data_distribuzione_start = :data_distribuzione_start,
                                testo_distribuzione= :testo_distribuzione
                                WHERE id_ordine_referenze=:id_ordine_referenze
                                AND id_gas_referenze=:id_gas_referenze
                                LIMIT 1;");

         $stmt->bindParam(':id_ordine_referenze', $id_ordini, PDO::PARAM_INT);
         $stmt->bindParam(':id_gas_referenze', $id_gas, PDO::PARAM_INT);

         $stmt->bindParam(':lat', $lat, PDO::PARAM_STR);
         $stmt->bindParam(':lng', $lng, PDO::PARAM_STR);
         $stmt->bindParam(':luogo_distribuzione',$luogo_consegna, PDO::PARAM_STR);
         $stmt->bindParam(':data_distribuzione_start',$data_consegna_start, PDO::PARAM_STR);
         $stmt->bindParam(':testo_distribuzione',$note_consegna, PDO::PARAM_STR);

         $stmt->execute();
         if($stmt->rowCount()<>1){
                $res=array("result"=>"KO", "msg"=>"Errore." );
         }else{
                $res=array("result"=>"OK", "msg"=>"Informazioni salvate." );
         }
         l_n($id_ordini,"Distribuzione per il gas "._USER_GAS_NOME." aggiornata");

        echo json_encode($res);
     break;
    case "diventa_referente":

    $id_ordine= CAST_TO_INT($_POST["id_ordine"],0);

    $O = new ordine($id_ordine);

    if(_USER_PERMISSIONS & perm::puo_creare_ordini){
        $sql = "UPDATE retegas_referenze SET retegas_referenze.id_utente_referenze = '"._USER_ID."'
                                WHERE
                                (((retegas_referenze.id_ordine_referenze)=:id_ordine)
                                AND ((retegas_referenze.id_gas_referenze)='"._USER_ID_GAS."'));";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->execute();

        $lista = $O->EMAIL_lista_potenziali_partecipanti(_USER_ID_GAS);
                 

        $mailFROM = _USER_MAIL;
        $fullnameFROM = _USER_FULLNAME;

        $oggetto = "[reteDES] Nuovo referente: ".$fullnameFROM;

        $profile = new Template('../../email_rd4/nuovo_referente.html');
        $profile->set("fullnameFROM", $fullnameFROM );
        $profile->set("descrizioneORDINE", $O->descrizione_ordini );
        $profile->set("descrizioneDITTA", $O->descrizione_ditte );
        $profile->set("fullnameGESTORE", $O->fullname_referente );
        $profile->set("gasGESTORE", $O->descrizione_gas_referente );
        $profile->set("dataCHIUSURA", $O->data_chiusura_lunga );
        $profile->set("linkORDINE", APP_URL.'/#ajax_rd4/ordini/ordine.php?id='.$O->id_ordini );
        $profile->set("linkDITTA", APP_URL.'/#ajax_rd4/fornitori/scheda.php?id='.$O->id_ditte );

        $messaggio = $profile->output();

        //if(SEmail('MAuro','famiglia.morello@gmail.com',$fullnameFROM,$mailFROM,$oggetto,$messaggio)){
        if(SEmailMulti($lista,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"referenteGAS")){
            $res=array("result"=>"OK", "msg"=>"Sei un referente GAS! Sono stati avvisati i tuoi amici gasisti." );
        }else{
            $res=array("result"=>"KO", "msg"=>"KO" );
        }
        
        //MANDO MAIL AGLI ALTRI REFERENTI
        $rowsG = $O->EMAIL_lista_referenti();
        $fullnameFROM = _USER_FULLNAME;
        $mailFROM = _USER_MAIL;
        $oggetto = "[reteDES] Nuovo referente per "._USER_GAS_NOME;
        $profile = new Template('../../email_rd4/basic_2.html');
        $profile->set("fullname_from",_USER_FULLNAME);
        $profile->set("fullnameFROM",_USER_FULLNAME);
        $profile->set("messaggio",$messaggio);
        $messaggio = $profile->output();
        
        foreach($rowsG as $rowG){
            Spostino($rowG["name"],$rowG["email"],$fullnameFROM,$mailFROM,$oggetto,$messaggio,"V4Avviso APE");
        }
        
        
        
    }else{
        $res = array("result"=>"KO", "msg"=>"Non hai i permessi per gestire gli ordini :(");
    }
    l_n($id_ordine,"Nuovo referente per il gas "._USER_GAS_NOME." - ");

    echo json_encode($res);
    die();

    break;

    case "accetta_aiuto":

    $id_option= CAST_TO_INT($_POST["id_option"],0);
    $stmt = $db->prepare("UPDATE retegas_options
                            SET valore_int=1
                            WHERE
                            id_option=:id_option
                            LIMIT 1;");
    $stmt->bindParam(':id_option', $id_option, PDO::PARAM_INT);
    $stmt->execute();

    //PRENDO l'ORDINE PER IL LOG
    $stmt = $db->prepare("SELECT * FROM retegas_options
                            WHERE
                            id_option=:id_option
                            LIMIT 1;");
    $stmt->bindParam(':id_option', $id_option, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();

    echo json_encode(array("result"=>"OK", "msg"=>"Confermato.".$id_option));
    l_n($row["id_ordine"],"Aiuto accettato");

    die();

    break;
    case "declina_aiuto":

    $id_option= CAST_TO_INT($_POST["id_option"],0);
    $stmt = $db->prepare("DELETE FROM retegas_options
                            WHERE
                            id_option=:id_option
                            LIMIT 1;");
    $stmt->bindParam(':id_option', $id_option, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(array("result"=>"OK", "msg"=>"Offerta declinata gentilmente."));
    l_n($id_ordine,"Aiuto rifiutato");
    die();

    break;


    case "offerta_aiuto":
    $id_ordine =  CAST_TO_INT($_POST['id_ordine'],0);

    if($id_ordine<1){
        echo json_encode(array("result"=>"KO", "msg"=>"Id missing"));
        die();
    }

    if(CAST_TO_STRING($_POST["value"])==""){
        echo json_encode(array("result"=>"KO", "msg"=>"Serve un commento"));
        die();
    }

    $stmt = $db->prepare("DELETE FROM retegas_options
                            WHERE
                                chiave='AIUTO_ORDINI'
                                AND
                                id_user='"._USER_ID."'
                                AND
                                id_ordine=:id_ordine
                                LIMIT 1;");
    $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
    $stmt->execute();

    //esiste
    $stmt = $db->prepare("INSERT INTO retegas_options (id_user,
                                        chiave,
                                        valore_text,
                                        id_gas,
                                        id_ordine,
                                        valore_int)
                                        VALUES (
                                        "._USER_ID.",
                                        'AIUTO_ORDINI',
                                        :value,
                                        "._USER_ID_GAS.",
                                        :id_ordine,
                                        0);");

    $stmt->bindParam(':value', CAST_TO_STRING($_POST['value']), PDO::PARAM_STR);
    $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
    $stmt->execute();

    if($stmt->rowCount()==1){
        $fullnameFROM = _USER_FULLNAME;
        $mailFROM = _USER_MAIL;

        $O = new ordine($id_ordine);
        $mailTO = $O->email_referente;
        $fullnameTO = $O->fullname_referente;
        $descrizione_ordine = $O->descrizione_ordini;
        $gas_referente = $O->descrizione_gas_referente;

        unset($O);


        //manda mail di carico credito
        $oggetto = "[reteDES] Offerta di aiuto :)";
        $profile = new Template('../../email_rd4/offerta_di_aiuto.html');

        $profile->set("fullnameFROM", $fullnameFROM );
        $profile->set("gasFROM", $gas_referente );
        $profile->set("id_ordine", $id_ordine );
        $profile->set("ruolo", CAST_TO_STRING($_POST['value']) );
        $profile->set("descrizione_ordine", $descrizione_ordine );
        $profile->set("url_pagina", APP_URL."/#ajax_rd4/ordini/aiutanti.php?id=".$id_ordine );

        $messaggio = $profile->output();

        if(SEmail($fullnameTO,$mailTO,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"OffertaAiuto")){

        }else{

        }
        $res=array("result"=>"OK", "msg"=>"Richiesta inoltrata :)", "Mail"=>$mailTO );

    }else{
        $res=array("result"=>"KO", "msg"=>"Errore." );
    }

    l_n($id_ordine,"aiuto offerto");
    echo json_encode($res);
    break;

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
                    $msg="condiviso con #".$_POST['value']." ".$row_gas["descrizione_gas"];
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
                    $msg="tolta condivisione con gas ".$_POST['value'];
             }
         }
        l_n($_POST['id_ordine'],$msg);
        echo json_encode($res);
     break;

        case "start_ordine":

        echo json_encode(array("result"=>"KO", "msg"=>"Operazione per il momento disattivata." ));
        die();

        //CONTROLLARE LA DATA DI CHIUSURA

        if (!posso_gestire_ordine($_POST['pk'])){
            echo json_encode(array("result"=>"KO", "msg"=>"Non hai i permessi necessari" ));
            die();
         }


         $stmt = $db->prepare("UPDATE retegas_ordini SET data_apertura = NOW(), id_stato=2
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
        //echo json_encode(array("result"=>"KO", "msg"=>"Operazione per il momento disattivata." ));
        //die();

        //CONTROLLARE LA DATA DI APERTURA

        if (!posso_gestire_ordine($_POST['pk'])){
            echo json_encode(array("result"=>"KO", "msg"=>"Non hai i permessi necessari" ));
            die();
         }

         $O=new ordine($_POST['pk']);
         if($O->codice_stato<>"AP"){
            echo json_encode(array("result"=>"KO", "msg"=>"Ordine non aperto" ));
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

        l_n($_POST['pk'],"Chiusura forzata ordine");
        echo json_encode($res);
     break;
     case "convalida_ordine_gas":
        if (!posso_gestire_ordine_come_gas($_POST['id_ordine'])){
            echo json_encode(array("result"=>"KO", "msg"=>"Non hai i permessi necessari" ));
            die();
         }
         $id_ordine = CAST_TO_INT($_POST['id_ordine']);
         $id_gas    =_USER_ID_GAS;
         $O=new ordine($id_ordine);

         $va_or = VA_ORDINE($id_ordine);
         $va_or_gas = VA_ORDINE_GAS($id_ordine,_USER_ID_GAS);
         
         //CONVALIDA GAS
         $stmt = $db->prepare("UPDATE retegas_referenze SET convalida_referenze = 1
                             WHERE id_ordine_referenze=:id_ordini
                             AND id_gas_referenze=:id_gas LIMIT 1;");
         $stmt->bindParam(':id_ordini', $id_ordine  , PDO::PARAM_INT);
         $stmt->bindParam(':id_gas', $id_gas  , PDO::PARAM_INT);
         $stmt->execute();

         //GAS SCARICO AUTOMATICO
         if(_USER_GAS_USA_CASSA){
             if(_GAS_CASSA_SCARICO_AUTOMATICO){
                if(_USER_PERMISSIONS & perm::puo_operare_con_crediti){
                    DO_CASSA_ALLINEA_ORDINE($id_ordine);
                    l_n($id_ordine,"Convalida GAS: (Val Ord. ".$va_or." - Val Ord GAS: ".$va_or_gas.") allineamento cassa su gas "._USER_GAS_NOME);
                }
             }
         }

         if(_USER_GAS_USA_CASSA){
             //MAIL AI CASSIERI
             unset ($r);
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

                        //SE VUOLE LA MAIL DELLA CONVALIDA
                        $sqlO="SELECT O.valore_text FROM retegas_options O inner join maaking_users U on U.userid=O.id_user WHERE U.email='".$row_u["email"]."' AND  O.chiave='_USER_CONVALIDA_ORDINI_GESTORI' LIMIT 1";
                        $stmt = $db->prepare($sqlO);
                        $stmt->execute();
                        $rowO = $stmt->fetch();

                        if(CAST_TO_STRING($rowO[0])<>"NO"){
                            $lista_destinatari .= $row_u["fullname"]."<br>";
                            $r[]=array( 'email' => $row_u["email"],
                                'name' => $row_u["fullname"],
                                'type' => 'bcc');
                        }
                        //SE VUOLE LA MAIL DELLA CONVALIDA

                }
             }

             $fullnameFROM = _USER_FULLNAME;
                 $mailFROM = _USER_MAIL;
             $oggetto = "[reteDES] "._USER_FULLNAME." ha convalidato per il tuo GAS l'ordine ".$O->id_ordini;
             $profile = new Template('../../email_rd4/convalida_ordine_cassieri.html');
             $profile->set("fullname", _USER_FULLNAME );
             $profile->set("id_ordine", $id_ordine );
             $profile->set("descrizione_ordine", $O->descrizione_ordini);
             $profile->set("link_ordine", APP_URL.'/#ajax_rd4/ordini/ordine.php?id='.$id_ordine);
             $profile->set("link_registra", APP_URL.'/#ajax_rd4/cassa/da_registrare.php?id='.$id_ordine);
             $profile->set("link_allinea", APP_URL.'/#ajax_rd4/ordini/cassa.php?id='.$id_ordine);

             $messaggio = $profile->output();
             SEmailMulti($r,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"V4AvvisoCassieri");
             unset ($profile);
         }
         //MAIL AI CASSIERI

         $res=array("result"=>"OK", "msg"=>"L'ordine è stato convalidato anche per il tuo GAS." );
         l_n($id_ordine,"Convalida ordine GAS: (Val Ord. ".$va_or." - Val Ord GAS: ".$va_or_gas.") eseguita su gas "._USER_GAS_NOME);
        echo json_encode($res);
     break;

     case "elimina_ordine":
        //echo json_encode(array("result"=>"KO", "msg"=>"Funzione non ancora attiva." ));
        //die();


        $livello = livello_gestire_ordine($_POST['id_ordine']);

        if ($livello<2){
            echo json_encode(array("result"=>"KO", "msg"=>"Non hai i permessi necessari (livello $livello)" ));
            die();
        }

        $id_ordine = CAST_TO_INT($_POST['id_ordine']);

        if ($id_ordine==0){
            echo json_encode(array("result"=>"KO", "msg"=>"Ordine inesistente" ));
            die();
        }

        $O = new ordine($id_ordine);

        //TABLE retegas_ordini
        $table="retegas_ordini";
        $log.="<br><hr><h4>$table</h4>";
        $log.= table_to_CSV($table," WHERE id_ordini=".$O->id_ordini);
        $stmt = $db->prepare("DELETE FROM $table WHERE id_ordini=:id_ordini LIMIT 1;");
        $stmt->bindParam(':id_ordini', $O->id_ordini  , PDO::PARAM_INT);
        $stmt->execute();
        $log.="<small>Eliminati ".$stmt->rowCount()." record dalla tabella <strong>$table</strong></small><br>";

        //TABLE dettaglio_ordini
        $table="retegas_dettaglio_ordini";
        $log.="<br><hr><h4>$table</h4>";
        $log.= table_to_CSV($table," WHERE id_ordine=".$O->id_ordini);
        $stmt = $db->prepare("DELETE FROM $table WHERE id_ordine=:id_ordini;");
        $stmt->bindParam(':id_ordini', $O->id_ordini  , PDO::PARAM_INT);
        $stmt->execute();
        $log.="<small>Eliminati ".$stmt->rowCount()." record dalla tabella <strong>$table</strong></small><br>";

        //TABLE distribuzione spesa
        $table="retegas_distribuzione_spesa";
        $log.="<br><hr><h4>$table</h4>";
        $log.= table_to_CSV($table," WHERE id_ordine=".$O->id_ordini);
        $stmt = $db->prepare("DELETE FROM $table WHERE id_ordine=:id_ordini;");
        $stmt->bindParam(':id_ordini', $O->id_ordini  , PDO::PARAM_INT);
        $stmt->execute();
        $log.="<small>Eliminati ".$stmt->rowCount()." record dalla tabella <strong>$table</strong></small><br>";

        //TABLE messaggi
        $table="retegas_messaggi";
        $log.="<br><hr><h4>$table</h4>";
        $log.= table_to_CSV($table," WHERE id_ordine=".$O->id_ordini);
        $stmt = $db->prepare("DELETE FROM $table WHERE id_ordine=:id_ordini;");
        $stmt->bindParam(':id_ordini', $O->id_ordini  , PDO::PARAM_INT);
        $stmt->execute();
        $log.="<small>Eliminati ".$stmt->rowCount()." record dalla tabella <strong>$table</strong></small><br>";

        //TABLE options -
            //NOTE ORDINE
            //NOTE ARTICOLI
            //AIUTANTI
            //REFERENTI EXTRA
        $table="retegas_options";
        $log.="<br><hr><h4>$table</h4>";
        $log.= table_to_CSV($table," WHERE id_ordine=".$O->id_ordini);
        $stmt = $db->prepare("DELETE FROM $table WHERE id_ordine=:id_ordini;");
        $stmt->bindParam(':id_ordini', $O->id_ordini  , PDO::PARAM_INT);
        $stmt->execute();
        $log.="<small>Eliminati ".$stmt->rowCount()." record dalla tabella <strong>$table</strong></small><br>";

        //TABLE referenze
        $table="retegas_referenze";
        $log.="<br><hr><h4>$table</h4>";
        $log.= table_to_CSV($table," WHERE id_ordine_referenze=".$O->id_ordini);
        $stmt = $db->prepare("DELETE FROM $table WHERE id_ordine_referenze=:id_ordini;");
        $stmt->bindParam(':id_ordini', $O->id_ordini  , PDO::PARAM_INT);
        $stmt->execute();
        $log.="<small>Eliminati ".$stmt->rowCount()." record dalla tabella <strong>$table</strong></small><br>";

        //TABLE cassa_utenti
        $table="retegas_cassa_utenti";
        $log.="<br><hr><h4>$table</h4>";
        $log.= table_to_CSV($table," WHERE id_ordine=".$O->id_ordini);
        $stmt = $db->prepare("DELETE FROM $table WHERE id_ordine=:id_ordini;");
        $stmt->bindParam(':id_ordini', $O->id_ordini  , PDO::PARAM_INT);
        $stmt->execute();
        $log.="<small>Eliminati ".$stmt->rowCount()." record dalla tabella <strong>$table</strong></small><br>";

        //TABLE opinioni
        $table="retegas_opinioni";
        $log.="<br><hr><h4>$table</h4>";
        $log.= table_to_CSV($table," WHERE id_ordine=".$O->id_ordini);
        $stmt = $db->prepare("DELETE FROM $table WHERE id_ordine=:id_ordini;");
        $stmt->bindParam(':id_ordini', $O->id_ordini  , PDO::PARAM_INT);
        $stmt->execute();
        $log.="<small>Eliminati ".$stmt->rowCount()." record dalla tabella <strong>$table</strong></small><br>";



        //MAIL AI GESTORI
        $rows = $O->EMAIL_lista_referenti();
        foreach($rows as $row){

            //SE VUOLE LA MAIL DELLA CONVALIDA
            $sqlO="SELECT O.valore_text FROM retegas_options O inner join maaking_users U on U.userid=O.id_user WHERE U.email='".$row["email"]."' AND  O.chiave='_USER_CONVALIDA_ORDINI_GESTORI' LIMIT 1";
            $stmt = $db->prepare($sqlO);
            $stmt->execute();
            $rowO = $stmt->fetch();

            if(CAST_TO_STRING($rowO[0])<>"NO"){
                $lista_destinatari .= $row["fullname"]."<br>";
                $r[]=array( 'email' => $row["email"],
                        'name' => $row["name"]
                        );
            }
            //SE VUOLE LA MAIL DELLA CONVALIDA

        }
        $log .="<br><br><hr><p>Questa mail è stata mandata a tutti coloro i quali possono gestire questo ordine.</p><br>";

         $fullnameFROM = _USER_FULLNAME;
             $mailFROM = _USER_MAIL;
         $oggetto = "[reteDES] "._USER_FULLNAME." ha eliminato l'ordine ".$O->id_ordini;
         $profile = new Template('../../email_rd4/eliminazione_ordine.html');
         $profile->set("fullname", _USER_FULLNAME );
         $profile->set("id_ordine", $id_ordine );
         $profile->set("descrizione_ordine", $O->descrizione_ordini);
         $profile->set("lista_movimenti", $log);

         $messaggio = $profile->output();
         //SSparkPostMulti($r,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"V4EliminaOrdine");
         SEmailMulti($r,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"V4EliminaOrdine");
         
         unset ($profile);

        $res=array("result"=>"OK", "msg"=>"Ok, è andata.." );
        l_n($id_ordine,"Eliminazione ordine: eseguita.");
        echo json_encode($res);
     break;

     case "convalida_ordine":
        if (!posso_gestire_ordine($_POST['id_ordine'])){
            echo json_encode(array("result"=>"KO", "msg"=>"Non hai i permessi necessari" ));
            die();
         }
         $id_ordine = CAST_TO_INT($_POST['id_ordine']);
         $id_gas    = _USER_ID_GAS;
         $O = new ordine($id_ordine);

         //CONVALIDA ORDINE
         $va_or = VA_ORDINE($id_ordine);
         
         
         //La convalida può essere fatta solo dal gas proprietario dell'ordine
         $stmt = $db->prepare("UPDATE retegas_ordini SET is_printable = 1
                             WHERE id_ordini=:id_ordini LIMIT 1;");
         $stmt->bindParam(':id_ordini', $id_ordine  , PDO::PARAM_INT);
         $stmt->execute();

         //CONVALIDA GAS
         $stmt = $db->prepare("UPDATE retegas_referenze SET convalida_referenze = 1
                             WHERE id_ordine_referenze=:id_ordini
                             AND id_gas_referenze=:id_gas LIMIT 1;");
         $stmt->bindParam(':id_ordini', $id_ordine  , PDO::PARAM_INT);
         $stmt->bindParam(':id_gas', $id_gas  , PDO::PARAM_INT);
         $stmt->execute();


         //GAS SCARICO AUTOMATICO --> GAS PROPRIETARIO ORDINE
         if(_USER_GAS_USA_CASSA){
             if(_GAS_CASSA_SCARICO_AUTOMATICO){
                if(_USER_PERMISSIONS & perm::puo_operare_con_crediti){
                    DO_CASSA_ALLINEA_ORDINE($id_ordine);
                    l($id_ordine,"Convalida generale: (Valore ordine ".$va_or.") Allineamento cassa gas "._USER_GAS_NOME);
                }
             }
         }
         
         //CANCELLAZIONE TRIGGERS TODO
         
         //MAIL AI REFERENTI GAS
         $rg = $O->lista_referenti_gas_partecipanti();
         foreach($rg as $row_u){

            //Escludo se sono io il referente gas
            if($row_u["userid"]<>_USER_ID){


                //SE VUOLE LA MAIL DELLA CONVALIDA
                $sqlO="SELECT O.valore_text FROM retegas_options O inner join maaking_users U on U.userid=O.id_user WHERE U.email='".$row_u["email"]."' AND  O.chiave='_USER_CONVALIDA_ORDINI_GESTORI' LIMIT 1";
                $stmt = $db->prepare($sqlO);
                $stmt->execute();
                $rowO = $stmt->fetch();

                if(CAST_TO_STRING($rowO[0])<>"NO"){
                    $lista_destinatari .= $row_u["fullname"]."<br>";
                $r[]=array( 'email' => $row_u["email"],
                    'name' => $row_u["fullname"],
                    'type' => 'bcc');
                }
                //SE VUOLE LA MAIL DELLA CONVALIDA




            }
         }

         $fullnameFROM = _USER_FULLNAME;
            $mailFROM = _USER_MAIL;
         $oggetto = "[reteDES] "._USER_FULLNAME." ha convalidato l'ordine ".$O->descrizione_ordini;
         $profile = new Template('../../email_rd4/convalida_ordine.html');
         $profile->set("fullname", _USER_FULLNAME );
         $profile->set("id_ordine", $id_ordine );
         $profile->set("descrizione_ordine", $O->descrizione_ordini);
         $profile->set("link_ordine", APP_URL.'/#ajax_rd4/ordini/ordine.php?id='.$id_ordine);
         $profile->set("link_pagina_gestione_gas", APP_URL.'/#ajax_rd4/ordini/edit_gas.php?id='.$id_ordine);
         $profile->set("link_pagina_rettifiche", APP_URL.'/#ajax_rd4/rettifiche/start.php?id='.$id_ordine);

         $messaggio = $profile->output();
         SEmailMulti($r,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"V4ConvalidaOrdine");
         unset ($profile);

         if(_USER_GAS_USA_CASSA){
             //MAIL AI CASSIERI
             unset ($r);
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

                    //SE VUOLE LA MAIL DELLA CONVALIDA
                    $sqlO="SELECT O.valore_text FROM retegas_options O inner join maaking_users U on U.userid=O.id_user WHERE U.email='".$row_u["email"]."' AND  O.chiave='_USER_CONVALIDA_ORDINI_GESTORI' LIMIT 1;";
                    $stmt = $db->prepare($sqlO);
                    $stmt->execute();
                    $rowO = $stmt->fetch();

                    if(CAST_TO_STRING($rowO[0])<>"NO"){
                        $lista_destinatari .= $row_u["fullname"]."<br>";
                        $r[]=array( 'email' => $row_u["email"],
                            'name' => $row_u["fullname"],
                            'type' => 'bcc');
                    }
                    //SE VUOLE LA MAIL DELLA CONVALIDA


                }
             }

             $fullnameFROM = _USER_FULLNAME;
                 $mailFROM = _USER_MAIL;
             $oggetto = "[reteDES] "._USER_FULLNAME." ha convalidato l'ordine ".$O->id_ordini;
             $profile = new Template('../../email_rd4/convalida_ordine_cassieri.html');
             $profile->set("fullname", _USER_FULLNAME );
             $profile->set("id_ordine", $id_ordine );
             $profile->set("descrizione_ordine", $O->descrizione_ordini);
             $profile->set("link_registra", APP_URL.'/#ajax_rd4/cassa/da_registrare.php?id='.$id_ordine);
             $profile->set("link_allinea", APP_URL.'/#ajax_rd4/ordini/cassa.php?id='.$id_ordine);

             $messaggio = $profile->output();
             SEmailMulti($r,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"V4AvvisoCassieri");
             unset ($profile);
             //MAIL AI CASSIERI
         }

         //FINALE
         $res=array("result"=>"OK", "msg"=>"L'ordine è stato convalidato." );
         
         l_n($id_ordine,"Convalida ordine generale: (Valore ordine ".$va_or.") eseguita su gas "._USER_GAS_NOME);
         echo json_encode($res);
     break;
     case "ripristina_ordine_gas":
        $id_ordine=CAST_TO_INT($_POST['id_ordine']);
        if (!posso_gestire_ordine_come_gas($id_ordine)){
            echo json_encode(array("result"=>"KO", "msg"=>"Non hai i permessi necessari" ));
            die();
        }
        $id_gas=_USER_ID_GAS;
        $O = new ordine($id_ordine);

        $stmt = $db->prepare("UPDATE retegas_referenze SET convalida_referenze = 0
                             WHERE id_ordine_referenze=:id_ordini
                             AND id_gas_referenze=:id_gas LIMIT 1;");

         $stmt->bindParam(':id_ordini',$id_ordine , PDO::PARAM_INT);
         $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
         $stmt->execute();
         if($stmt->rowCount()<>1){
                $res=array("result"=>"KO", "msg"=>"Errore." );
         }else{
                $res=array("result"=>"OK", "msg"=>"L'ordine è stato ripristinato." );
                    //MAIL AI CASSIERI PROPRIO GAS
                    if(_USER_GAS_USA_CASSA){
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

                            //SE VUOLE LA MAIL DELLA CONVALIDA
                            $sqlO="SELECT O.valore_text FROM retegas_options O inner join maaking_users U on U.userid=O.id_user WHERE U.email='".$row_u["email"]."' AND  O.chiave='_USER_CONVALIDA_ORDINI_GESTORI' LIMIT 1";
                            $stmt = $db->prepare($sqlO);
                            $stmt->execute();
                            $rowO = $stmt->fetch();

                            if(CAST_TO_STRING($rowO[0])<>"NO"){
                                $lista_destinatari .= $row_u["fullname"]."<br>";
                                $r[]=array( 'email' => $row_u["email"],
                                    'name' => $row_u["fullname"],
                                    'type' => 'bcc');
                            }
                            //SE VUOLE LA MAIL DELLA CONVALIDA

                        }
                    }

                    $fullnameFROM = "ReteDES.it";
                        $mailFROM = "info@retedes.it";
                    $oggetto = "[reteDES] ordine #$id_ordine ripristinato da "._USER_FULLNAME;
                    $profile = new Template('../../email_rd4/ordine_ripristinato.html');
                    $profile->set("fullname", _USER_FULLNAME );
                    $profile->set("id_ordine", $id_ordine );
                    $profile->set("link_ordine", APP_URL.'/gas4/#ajax_rd4/ordini/ordine.php?id='.$id_ordine );
                    $profile->set("descrizione_ordine", $O->descrizione_ordini );
                    $messaggio = $profile->output();
                    SEmailMulti($r,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"V4RicaricaCredito");
                    unset ($profile);
                    //MAIL AI CASSIERI
                }


         }




        l_n($id_ordine,"Ripristino convalida GAS: eseguita su "._USER_GAS_NOME);
        echo json_encode($res);
     break;
     case "ripristina_ordine":
         if (!posso_gestire_ordine($_POST['id_ordine'])){
            echo json_encode(array("result"=>"KO", "msg"=>"Non hai i permessi necessari" ));
            die();
         }

         $id_ordine=CAST_TO_INT($_POST['id_ordine'],0);
         $O= new ordine($id_ordine);

         $stmt = $db->prepare("UPDATE retegas_ordini SET is_printable = 0
                             WHERE id_ordini=:id_ordini LIMIT 1;");
         $stmt->bindParam(':id_ordini', $id_ordine, PDO::PARAM_INT);
         $stmt->execute();

         $stmt = $db->prepare("UPDATE retegas_referenze SET convalida_referenze = 0
                             WHERE id_ordine_referenze=:id_ordini;");
         $stmt->bindParam(':id_ordini', $id_ordine, PDO::PARAM_INT);
         $stmt->execute();

         if($stmt->rowCount()==0){
                $res=array("result"=>"KO", "msg"=>"Errore." );
         }else{
                $res=array("result"=>"OK", "msg"=>"L'ordine è stato ripristinato." );

                //MAIL AI REFERENTI GAS
                $rg = $O->lista_referenti_gas_partecipanti();
                foreach($rg as $row_u){

                    //Escludo se sono io il referente gas
                    if($row_u["userid"]<>_USER_ID){


                        //SE VUOLE LA MAIL DELLA CONVALIDA
                        $sqlO="SELECT O.valore_text FROM retegas_options O inner join maaking_users U on U.userid=O.id_user WHERE U.email='".$row_u["email"]."' AND  O.chiave='_USER_CONVALIDA_ORDINI_GESTORI' LIMIT 1";
                        $stmt = $db->prepare($sqlO);
                        $stmt->execute();
                        $rowO = $stmt->fetch();

                        if(CAST_TO_STRING($rowO[0])<>"NO"){
                            $lista_destinatari .= $row_u["fullname"]."<br>";
                            $r[]=array( 'email' => $row_u["email"],
                                'name' => $row_u["fullname"],
                                'type' => 'bcc');
                        }
                        //SE VUOLE LA MAIL DELLA CONVALIDA



                    }
                 }

                 $fullnameFROM = _USER_FULLNAME;
                    $mailFROM = _USER_MAIL;
                 $oggetto = "[reteDES] "._USER_FULLNAME." ha ripristinato l'ordine ".$O->descrizione_ordini;
                 $profile = new Template('../../email_rd4/ordine_ripristinato.html');
                 $profile->set("fullname", _USER_FULLNAME );
                 $profile->set("id_ordine", $id_ordine );
                 $profile->set("descrizione_ordine",$O->descrizione_ordini );
                 $profile->set("link_ordine", APP_URL.'/gas4/#ajax_rd4/ordini/ordine.php?id='.$id_ordine);

                 $messaggio = $profile->output();
                 SEmailMulti($r,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"V4RipristinaOrdine");
                 unset ($profile);




         }
        l_n($_POST['id_ordine'],"Ripristino convalida GENERALE: eseguita su "._USER_GAS_NOME);
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


        //Note ordine
        $config = HTMLPurifier_Config::createDefault();
        $config->set('CSS.MaxImgLength', null);
        $config->set('HTML.MaxImgLength', null);
        $config->set('HTML.SafeIframe', true);
        $config->set('URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%'); //allow YouTube and Vimeo
        $config->set('Attr.AllowedFrameTargets', array('_blank','_self'));

        $purifier = new HTMLPurifier($config);
        $noteordine = $purifier->purify($_POST['noteordine']);

        //esiste
        $stmt = $db->prepare("INSERT INTO retegas_ordini
            (id_listini,
            id_utente,
            descrizione_ordini,
            data_chiusura,
            
            
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
            solo_cassati,
            data_creazione)
            VALUES
            (:idlistino,
            '"._USER_ID."',
            :nomeordine,
            :datachiusura,
            
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
            '$solocassati',
            NOW());");
        $stmt->bindParam(':idlistino', $_POST['idlistino'], PDO::PARAM_INT);
        $stmt->bindParam(':nomeordine', $_POST['nomeordine'], PDO::PARAM_STR);
        $stmt->bindParam(':dataapertura', $dataapertura, PDO::PARAM_STR);
        $stmt->bindParam(':datachiusura', $datachiusura, PDO::PARAM_STR);
        $stmt->bindParam(':noteordine', $noteordine, PDO::PARAM_STR);
        $stmt->execute();
        $newId = $db->lastInsertId();
        if($stmt->rowCount()==1){


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
        l_n($newId,"Creazione ordine");
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
         
         
     case  "edit_nota_dettaglio":
        
        $pk = CAST_TO_INT($_POST["pk"]);
        $value = CAST_TO_STRING($_POST["value"]);
        $id_ordine = CAST_TO_INT($_POST["id_ordine"]);
        $id_articolo = CAST_TO_INT($_POST["id_articolo"]);
        $id_utente = CAST_TO_INT($_POST["id_utente"]);
        
        if (!posso_gestire_ordine($id_ordine)){
            echo json_encode(array("result"=>"KO", "msg"=>"Non hai i permessi necessari" ));
            die();
        }
        
        $sql = "DELETE FROM retegas_options
                WHERE id_option=:pk
                LIMIT 1;";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':pk', $pk, PDO::PARAM_INT);
        $stmt->execute();
        $res=array("result"=>"OK", "msg"=>"OK" );
            
        if(trim($value)<>""){
            $sql = "INSERT INTO retegas_options (id_articolo,
                                                 id_user,
                                                 id_ordine,
                                                 chiave,
                                                 valore_text)
                                                 VALUES
                                                 (:id_articolo,
                                                 :id_utente,
                                                 :id_ordine,
                                                 '_NOTE_DETTAGLIO',
                                                 :value
                                                 )";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
            $stmt->bindParam(':id_utente', $id_utente, PDO::PARAM_INT);
            $stmt->bindParam(':id_articolo', $id_articolo, PDO::PARAM_INT);
            $stmt->bindParam(':value', $value, PDO::PARAM_STR);

            $stmt->execute();
            $newid = $db->lastInsertId();
            
            if($stmt->rowCount()<>1){
                $res=array("result"=>"KO", "msg"=>"Errore inserimento." );
            }else{
                $res=array("result"=>"OK", "msg"=>"OK", "pk"=>$newid );
                l_n($id_ordine, "Update note dettaglio");
            }
        }
        
        
    
        echo json_encode($res);
        
        break;    
         
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
        l_n($_POST['pk'],"Update descrizione: ".$_POST['value']);
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
        l_n($_POST['pk'],"Nuova previsione costo gestione: ".$costo_gestione);
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
        l_n($_POST['pk'],"Nuova previsione costo trasporto: ".$costo_trasporto);
        echo json_encode($res);
     break;
     case "costo_gas":


         if (!(perm::puo_vedere_tutti_ordini & _USER_PERMISSIONS)){
            echo json_encode(array("result"=>"KO", "msg"=>"Non hai i permessi necessari" ));
            die();
         }
         $costo_gas_referenza= str_replace(',', '.', $_POST['value']);
         $costo_gas_referenza = CAST_TO_FLOAT($costo_gas_referenza,0);

         $stmt = $db->prepare("UPDATE retegas_referenze SET
                                maggiorazione_referenza= :costo_gas_referenza
                                WHERE
                                id_gas_referenze='"._USER_ID_GAS."' AND
                                id_ordine_referenze=:id_ordini LIMIT 1;");

        $stmt->bindParam(':costo_gas_referenza', $costo_gas_referenza, PDO::PARAM_STR);
        $stmt->bindParam(':id_ordini', $_POST['pk'], PDO::PARAM_INT);

        $stmt->execute();
        if($stmt->rowCount()<>1){
            $res=array("result"=>"KO", "msg"=>"Errore." );
        }else{
            $res=array("result"=>"OK", "msg"=>"OK" );
        }
        l_n($_POST['pk'],"Nuovo costo GAS: ".$costo_gas_referenza." "._USER_GAS_NOME);
        echo json_encode($res);
        unset($O);
     break;
     case "maggiorazione_gas":


        if (!(perm::puo_vedere_tutti_ordini & _USER_PERMISSIONS)){
            echo json_encode(array("result"=>"KO", "msg"=>"Non hai i permessi necessari" ));
            die();
         }
         $maggiorazione_gas= str_replace(',', '.', $_POST['value']);
         $maggiorazione_gas = CAST_TO_FLOAT($maggiorazione_gas,0);

         $stmt = $db->prepare("UPDATE retegas_referenze SET
                                maggiorazione_percentuale_referenza= :maggiorazione_gas
                                WHERE
                                id_gas_referenze='"._USER_ID_GAS."' AND
                                id_ordine_referenze=:id_ordini LIMIT 1;");

        $stmt->bindParam(':maggiorazione_gas', $maggiorazione_gas, PDO::PARAM_STR);
        $stmt->bindParam(':id_ordini', $_POST['pk'], PDO::PARAM_INT);

        $stmt->execute();
        if($stmt->rowCount()<>1){
            $res=array("result"=>"KO", "msg"=>"Errore." );
        }else{
            $res=array("result"=>"OK", "msg"=>"OK" );
        }
        l_n($_POST['pk'],"Nuova maggiorazione GAS: ".$maggiorazione_gas." "._USER_GAS_NOME);
        echo json_encode($res);
        unset($O);
     break;
      case "motivo_maggiorazione":

         if (!(perm::puo_vedere_tutti_ordini & _USER_PERMISSIONS)){
            echo json_encode(array("result"=>"KO", "msg"=>"Non hai i permessi necessari" ));
            die();
         }
         $motivo_maggiorazione= CAST_TO_STRING($_POST['value']);


         $stmt = $db->prepare("UPDATE retegas_referenze SET
                                note_referenza= :motivo_maggiorazione
                                WHERE
                                id_gas_referenze='"._USER_ID_GAS."' AND
                                id_ordine_referenze=:id_ordini LIMIT 1;");

        $stmt->bindParam(':motivo_maggiorazione', $motivo_maggiorazione, PDO::PARAM_STR);
        $stmt->bindParam(':id_ordini', $_POST['pk'], PDO::PARAM_INT);

        $stmt->execute();
        if($stmt->rowCount()<>1){
            $res=array("result"=>"KO", "msg"=>"Errore." );
        }else{
            $res=array("result"=>"OK", "msg"=>"OK" );
        }
        echo json_encode($res);
        unset($O);
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
         $data_apertura_old = strtotime(str_replace('/', '-', $row["data_apertura"]));
         $data_chiusura = strtotime(str_replace('/', '-', $row["data_chiusura"]));
         $data_now = strtotime(date("d-m-Y H:i"));

         if($data_apertura==$data_apertura_old){
            echo json_encode(array("result"=>"KO", "msg"=>"Non hai cambiato nulla ;)" ));
            die();
         }

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
        l_n($_POST['pk'],"Nuova data apertura: ".$_POST['value']);
        echo json_encode($res);
     break;
     case "note_ordini":
        if (!posso_gestire_ordine($_POST['pk'])){
            echo json_encode(array("result"=>"KO", "msg"=>"Non hai i permessi necessari" ));
            die();
         }
        $note = CAST_TO_STRING($_POST['value']);

         //Note ordine
        $config = HTMLPurifier_Config::createDefault();
        $config->set('CSS.MaxImgLength', null);
        $config->set('HTML.MaxImgLength', null);
        $config->set('HTML.SafeIframe', true);
        $config->set('URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%'); //allow YouTube and Vimeo
        $config->set('Attr.AllowedFrameTargets', array('_blank','_self'));


        $purifier = new HTMLPurifier($config);
        $note = $purifier->purify($note);



         $stmt = $db->prepare("UPDATE retegas_ordini SET note_ordini = :note_ordini
                             WHERE id_ordini=:id_ordini LIMIT 1;");

         $stmt->bindParam(':note_ordini', $note , PDO::PARAM_STR);
         $stmt->bindParam(':id_ordini', $_POST['pk'], PDO::PARAM_INT);

         $stmt->execute();
        if($stmt->rowCount()<>1){
            $res=array("result"=>"KO", "msg"=>"Non aggiornato.");
        }else{
            $res=array("result"=>"OK", "msg"=>"OK" );
        }
        echo json_encode($res);
        l_n($_POST['pk'],"Update note: ".$_POST['value']);
     break;
     case "data_chiusura":
        if (!posso_gestire_ordine($_POST['pk'])){
            echo json_encode(array("result"=>"KO", "msg"=>"Non hai i permessi necessari" ));
            die();
         }
         $id_ordine = CAST_TO_INT($_POST["pk"],0);

         $O = new ordine($id_ordine);
         if ($O->is_printable==1){
            echo json_encode(array("result"=>"KO", "msg"=>"Questo ordine è già convalidato." ));
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
         $data_chiusura_old = strtotime(str_replace('/', '-', $row["data_chiusura"]));
         $data_now = strtotime(date("d-m-Y H:i"));

         if($data_chiusura==$data_chiusura_old){
            echo json_encode(array("result"=>"KO", "msg"=>"Non hai cambiato nulla ;)" ));
            die();
         }

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
        l_n($_POST['pk'],"Nuova data chiusura: ".$_POST['value']);
        echo json_encode($res);
     break;

     case "update_note_dettaglio":

        $value = CAST_TO_STRING($_POST["value"]);
        $pk = CAST_TO_INT($_POST["pk"]);
        $id_ordine = CAST_TO_INT($_GET["id_ordine"]);


        $sql = "DELETE FROM retegas_options
                WHERE id_user='"._USER_ID."'
                AND id_articolo=:pk
                AND id_ordine=:id_ordine
                AND chiave='_NOTE_DETTAGLIO' LIMIT 1;";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':pk', $pk, PDO::PARAM_INT);
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->execute();

        if(trim($value)<>""){
            $sql = "INSERT INTO retegas_options (id_articolo,
                                                 id_user,
                                                 id_ordine,
                                                 chiave,
                                                 valore_text)
                                                 VALUES
                                                 (:pk,
                                                 '"._USER_ID."',
                                                 :id_ordine,
                                                 '_NOTE_DETTAGLIO',
                                                 :value
                                                 )";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':pk', $pk, PDO::PARAM_INT);
            $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
            $stmt->bindParam(':value', $value, PDO::PARAM_STR);

            $stmt->execute();
            if($stmt->rowCount()<>1){
                $res=array("result"=>"KO", "msg"=>"Errore." );
            }else{
                $res=array("result"=>"OK", "msg"=>"OK" );
            }
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


if(!empty($_GET["name"])){
     switch ($_GET["name"]) {

        
         
        case "update_status_scatole":

        //SCATOLE PIENE
        $id_ordine = CAST_TO_INT($_GET["id_ordine"]);
        $id_articolo = CAST_TO_INT($_GET["id_articolo"]);
        $mode = CAST_TO_STRING($_GET["mode"]);


        $O=new ordine($id_ordine);

        //BINOTTO
        if($O->metodo_scatole==0){
        //if(($id_ordine<>9881) AND ($id_ordine<>9814)){
            $scatole_intere = (int)         QTA_SCATOLE_INTERE_ARTICOLO_ORDINE($id_articolo,$O->id_ordini);
            $avanzo_articolo = (float)round(QTA_SCATOLA_AVANZO_ARTICOLO_ORDINE($id_articolo,$O->id_ordini),2);
        }else{
            $scatole_intere = (int)         QTA_SCATOLE_INTERE_ARTICOLO_ORDINE_GAS($id_articolo,$O->id_ordini,_USER_ID_GAS);
            $avanzo_articolo = (float)round(QTA_SCATOLA_AVANZO_ARTICOLO_ORDINE_GAS($id_articolo,$O->id_ordini,_USER_ID_GAS),2);
        }




        $qta_ordinata_user =            QTA_ORDINATA_ORDINE_ARTICOLO_USER($O->id_ordini,$id_articolo,_USER_ID);
        $stmt = $db->prepare("SELECT * FROM retegas_articoli WHERE id_articoli=:id_articolo LIMIT 1;");
        $stmt->bindParam(':id_articolo', $id_articolo, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $qta_scatola = $row["qta_scatola"];


        $per_completare_scatola ="";
        if($scatole_intere==0){
            //Se ? la prima scatola
            if($avanzo_articolo>0){
                $per_completare_scatola = ($qta_scatola - $avanzo_articolo);
                if($qta_ordinata_user>0){
                    //Se sono io che ho ordinato
                    $colore = "text-danger";
                    $per_completare_scatola = "<strong class=\"$colore font-md\">$per_completare_scatola</strong> per chiudere la prima scatola";
                }else{
                    //Se sono altri che hanno ordinato
                    $colore = "text-warning";
                    $per_completare_scatola = "<strong class=\"$colore font-md\">$per_completare_scatola</strong> per chiudere la prima scatola";
                }
            }else{
               // Nessun articolo ordinato da nessuno
               $per_completare_scatola = "Nessuna scatola da riempire!";
               $colore ="";
            }
        }else{
            //Se ci sono già scatole
            if($avanzo_articolo>0){
                $per_completare_scatola = ($qta_scatola - $avanzo_articolo);
                if($qta_ordinata_user>0){
                    $colore = "text-danger";
                    $per_completare_scatola = "<strong class=\"$colore font-md\">$per_completare_scatola</strong> per chiudere un'altra scatola";
                }else{
                    //Se sono altri che hanno ordinato
                    $colore = "text-warning";
                    $per_completare_scatola = "<strong class=\"$colore font-md\">$per_completare_scatola</strong> per chiudere un'altra scatola";
                }
            }else{
               // Nessun articolo ordinato da nessuno
               $colore ="";
               $per_completare_scatola = "Nessuna scatola da riempire!";

            }

        }

        //INFO SU ARTICOLO
         $stmt = $db->prepare("SELECT * from retegas_articoli where id_articoli='".$id_articolo."' LIMIT 1;");
         $stmt->execute();
         $row = $stmt->fetch(PDO::FETCH_ASSOC);
         $um = $row["u_misura"]." <b class=\"text-info\">".$row["misura"]."</b> per <span class=\"text-danger\"><b>"._NF($row["prezzo"])."</b> Eu.</span>";
         $scat ="<small>Scat. da <b class=\"text-info\">"._NF($row["qta_scatola"])."</b>, Min. <b class=\"text-info\">"._NF($row["qta_minima"])."</b></small>";
         if($row["articoli_note"]<>""){
            $note = '<br>Note:<br><small>'.$row["articoli_note"].'</small>';
         }else{
            $note = '';
         }
         if($row["ingombro"]<>""){
            $ingombro = '<br><small>'.$row["ingombro"].'</small>';
         }else{
            $ingombro = '';
         }

        if($mode<>"m"){
            echo $um.'<br>'.$scat.'<br>'.$per_completare_scatola;
        }else{
            echo '<div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title" id="myModalLabel">Dettaglio articolo</h4>
                    </div>
                    <div class="modal-body">
                        '.$um.'<br>
                        '.$scat.'<br><br>
                        '.$per_completare_scatola.'
                        '.$note.'
                        '.$ingombro.'
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">
                            Ok
                        </button>
                    </div>';
        }
        //PER COMPLETARE SCATOLA

        break;
     }

}