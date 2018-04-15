<?php
require_once("inc/init.php");
if(file_exists("../../lib_rd4/class.rd4.ditta.php")){require_once("../../lib_rd4/class.rd4.ditta.php");}
if(file_exists("../lib_rd4/class.rd4.ditta.php")){require_once("../lib_rd4/class.rd4.ditta.php");}
if(file_exists("../../lib_rd4/class.rd4.gas.php")){require_once("../../lib_rd4/class.rd4.gas.php");}
if(file_exists("../lib_rd4/class.rd4.gas.php")){require_once("../lib_rd4/class.rd4.gas.php");}

function controlloPIVA($pi){                          
    if( $pi === '' )  return false;
    if( strlen($pi) != 11 ) return false;
    if( preg_match("/^[0-9]+\$/", $pi) != 1 ) return false;
    $s = 0;
    for( $i = 0; $i <= 9; $i += 2 )
        $s += ord($pi[$i]) - ord('0');
    for( $i = 1; $i <= 9; $i += 2 ){
        $c = 2*( ord($pi[$i]) - ord('0') );
        if( $c > 9 )  $c = $c - 9;
        $s += $c;
    }
    if( ( 10 - $s%10 )%10 != ord($pi[10]) - ord('0') ) return false;
    
    return true;
}
function controlloPIVA2($stringa){
    $pattern = "/^(IT){0,1}[0-9]{11}$/i";
if(preg_match($pattern, trim($stringa)))
return true;
else
return false;
    
}

function controllaCF($cf){
    if( $cf === '' )  return false;
    if( strlen($cf) != 16 ) return false;
    $cf = strtoupper($cf);
    if( preg_match("/^[A-Z0-9]+\$/", $cf) != 1 ){
        return false;
    }
    $s = 0;
    for( $i = 1; $i <= 13; $i += 2 ){
        $c = $cf[$i];
        if( strcmp($c, "0") >= 0 and strcmp($c, "9") <= 0 )
            $s += ord($c) - ord('0');
        else
            $s += ord($c) - ord('A');
    }
    for( $i = 0; $i <= 14; $i += 2 ){
        $c = $cf[$i];
        switch( $c ){
        case '0':  $s += 1;  break;
        case '1':  $s += 0;  break;
        case '2':  $s += 5;  break;
        case '3':  $s += 7;  break;
        case '4':  $s += 9;  break;
        case '5':  $s += 13;  break;
        case '6':  $s += 15;  break;
        case '7':  $s += 17;  break;
        case '8':  $s += 19;  break;
        case '9':  $s += 21;  break;
        case 'A':  $s += 1;  break;
        case 'B':  $s += 0;  break;
        case 'C':  $s += 5;  break;
        case 'D':  $s += 7;  break;
        case 'E':  $s += 9;  break;
        case 'F':  $s += 13;  break;
        case 'G':  $s += 15;  break;
        case 'H':  $s += 17;  break;
        case 'I':  $s += 19;  break;
        case 'J':  $s += 21;  break;
        case 'K':  $s += 2;  break;
        case 'L':  $s += 4;  break;
        case 'M':  $s += 18;  break;
        case 'N':  $s += 20;  break;
        case 'O':  $s += 11;  break;
        case 'P':  $s += 3;  break;
        case 'Q':  $s += 6;  break;
        case 'R':  $s += 8;  break;
        case 'S':  $s += 12;  break;
        case 'T':  $s += 14;  break;
        case 'U':  $s += 16;  break;
        case 'V':  $s += 10;  break;
        case 'W':  $s += 22;  break;
        case 'X':  $s += 25;  break;
        case 'Y':  $s += 24;  break;
        case 'Z':  $s += 23;  break;
        /*. missing_default: .*/
        }
    }
    if( chr($s%26 + ord('A')) != $cf[15] ) return false;
    return true;
}
function get_web_page($url) {
    $options = array(
        CURLOPT_RETURNTRANSFER => true,   // return web page
        CURLOPT_HEADER         => false,  // don't return headers
        CURLOPT_FOLLOWLOCATION => true,   // follow redirects
        CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
        CURLOPT_ENCODING       => "",     // handle compressed
        CURLOPT_USERAGENT      => "test", // name of client
        CURLOPT_AUTOREFERER    => true,   // set referrer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,    // time-out on connect
        CURLOPT_TIMEOUT        => 120,    // time-out on response
    ); 

    $ch = curl_init($url);
    curl_setopt_array($ch, $options);

    $content  = curl_exec($ch);

    curl_close($ch);

    return $content;
}


if(!empty($_POST["act"])){
    switch ($_POST["act"]) {
    
    case "do_inserisci_nuova_ditta":
        if(!_USER_PERMISSIONS & perm::puo_creare_ditte){
            $res=array("result"=>"KO", "msg"=>"Non hai i permessi necessari, sorry." );
            echo json_encode($res);
            die();    
        }
    
        $nome_ditta=trim(strip_tags(CAST_TO_STRING($_POST["nome_ditta"])));
        $tel_ditta=trim(strip_tags(CAST_TO_STRING($_POST["tel_ditta"])));
        $email_ditta=trim(strip_tags(CAST_TO_STRING($_POST["email_ditta"])));
        $piva_ditta=trim(strip_tags(CAST_TO_STRING($_POST["piva_ditta"])));
        $operazione=CAST_TO_INT($_POST["submit"],1,2);
        
        if(!controlloPIVA($piva_ditta)){
            $is_piva=false;    
            if(!controllaCF($piva_ditta)){
                $is_cf=false;
                $res=array("result"=>"KO", "msg"=>"Partita IVA o Cod. Fiscale non in formato corretto." );
                echo json_encode($res);
                die();
            }else{
                $is_cf=true;
            }    
        }else{
            $is_piva=true;
        }
        
        if($is_piva){
            $country = 'IT';
            $url = 'http://ec.europa.eu/taxation_customs/vies/viesquer.do?ms='.$country.'&iso='.$country.'&vat='.$piva_ditta.'&name=&companyType=&street1=&postcode=&city=&BtnSubmitVat=Verify';
            $response = file_get_contents($url);
            $first_step = explode( '<td class="labelStyle">Name' , $response );
            $second_step = explode("</tr>" , $first_step[1] );
            $piva_name=  strip_tags($second_step[0]);
            
            $first_step = explode( '<span class="validStyle">' , $response );
            $second_step = explode("</span>" , $first_step[1] );
            $piva_state=  strip_tags($second_step[0]);
            
            $first_step = explode( '<td class="labelStyle">Address' , $response );
            $second_step = explode("</tr>" , $first_step[1] );
            $piva_address=  strip_tags($second_step[0]);
            
            
        }else{
               
        }
        if($operazione==1){
            //VERIFICA
            $sql = "SELECT * FROM retegas_ditte;";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll();
            foreach($rows as $row){
                
                $url_ditta = '<a href="/#ajax_rd4/fornitori/scheda.php?id='.$row["id_ditte"].'">'.$row["descrizione_ditte"].'</a>';
                
                if(strtoupper($row["descrizione_ditte"])==strtoupper($nome_ditta)){
                    $nomi_uguali .= '<i class="fa fa-warning text-danger"></i> nome UGUALE: #'.$row["id_ditte"]."  ".$url_ditta."<br>";
                    $blocco++;
                }else{
                    if(levenshtein(strtoupper($row["descrizione_ditte"]),strtoupper($nome_ditta))<3){
                        $nomi_simili .= '<i class="fa fa-warning text-warning"></i> nome simile: #'.$row["id_ditte"]."  ".$url_ditta."<br>";
                        $simili++;
                    }    
                }
                if(strtoupper($row["mail_ditte"])==strtoupper($email_ditta)){
                    $email_uguali .= '<i class="fa fa-warning text-danger"></i> mail UGUALE:  #'.$row["id_ditte"].' '.$url_ditta.'<br>';
                    $blocco++;
                }else{
                        
                }
                if(str_replace(' ', '', $row["telefono"])==str_replace(' ', '', $tel_ditta)){
                    $telefono_uguali .= '<i class="fa fa-warning text-danger"></i> telefono UGUALE: #'.$row["id_ditte"].' '.$url_ditta.'<br>';
                    $blocco++;
                }else{
                    if(levenshtein(str_replace(' ', '', $row["telefono"]),str_replace(' ', '', $tel_ditta))<2){
                        $telefono_simili .= '<i class="fa fa-warning text-warning"></i> telefono simile: '.$row["telefono"].' #'.$row["id_ditte"].' '.$url_ditta.'<br>';
                        $simili++;
                    }    
                }
                if($row["P_IVA"]==$piva_ditta){
                    $piva_uguali .= '<i class="fa fa-warning text-danger"></i> P.IVA o C.FISC UGUALE: #'.$row["id_ditte"]." ".$url_ditta."<br>";
                    $blocco++;
                }else{
                        
                }
                
            }
            
            if($blocco>0 or $simili>0){
            
                $html =     '<h3>Ci sono alcuni problemi...</h3>'.
                            $nomi_simili.$nomi_uguali.'<hr>'.
                            $email_simili.$email_uguali.'<hr>'.
                            $telefono_simili.$telefono_uguali.'<hr>'.
                            $piva_simili.$piva_uguali.$piva;
                            
            }
            
            if($blocco>0){
                $procedi=0;
            }else{
                $procedi=1;
                $html = '<h3><i class="fa fa-check text-success"></i> OK! puoi inserire la ditta nuova!</h3>';
            }
                
            $res=array("result"=>"OK", "html"=>$html, "procedi"=>$procedi, "piva_name"=>$piva_state."; ".$piva_name." - ".$piva_address);
            echo json_encode($res);
            die();
        }
        if($operazione==2){
            
            //INSERT
            $stmt = $db->prepare("INSERT INTO retegas_ditte (
                                                                id_proponente,
                                                                descrizione_ditte, 
                                                                data_creazione,
                                                                mail_ditte,
                                                                telefono,
                                                                P_IVA
                                                                
                                                                ) VALUES (
                                                                '"._USER_ID."',
                                                                :nome_ditta, 
                                                                NOW(),
                                                                :mail_ditta,
                                                                :tel_ditta,
                                                                :piva_ditta
                                                                
                                                                ) ;");
            $stmt->bindParam(':nome_ditta', $nome_ditta, PDO::PARAM_STR);
            $stmt->bindParam(':mail_ditta', $email_ditta, PDO::PARAM_STR);
            $stmt->bindParam(':piva_ditta', $piva_ditta, PDO::PARAM_STR);
            $stmt->bindParam(':tel_ditta', $tel_ditta, PDO::PARAM_STR);
            $stmt->execute();

            
            $id = $db->lastInsertId();
            
            
            $procedi=3;
            $res=array("result"=>"OK", "html"=>$html, "procedi"=>$procedi, "id"=>$id);
            echo json_encode($res);
            die();    
            
        }
        
    
        $res=array("result"=>"KO", "msg"=>"Non è stato cliccato sul pulsante.", "blocco"=>$blocco );
        echo json_encode($res);
        die();
        
    break;   
        
        
     case "show_scheda_paragone_ditte":
        $id = CAST_TO_INT($_POST["id"]);
        $D = new ditta($id);
        
        
        //TELEFONO DITTA
        $stmt = $db->prepare("SELECT * from retegas_ditte;");
        $stmt->execute();
        $rows = $stmt->fetchAll();
        $n_t=0;
        $telefono = preg_replace('/\s+/', '', $D->telefono);
        foreach($rows as $row){
            $telefono_esaminato = preg_replace('/\s+/', '', $row["telefono"]);    
            if((levenshtein($telefono,$telefono_esaminato)<3) AND $D->id_ditte<>$row["id_ditte"]){
                $t.= $row["id_ditte"].' - '.$row["descrizione_ditte"].' - '.$row["telefono"].'<br>';
                $n_t++;    
            }
        }
        $c .= "<hr>$n_t risultati telefono simili a ".$telefono."<br>".$t;
        
        //MAIL DITTA
        $stmt = $db->prepare("SELECT * from retegas_ditte;");
        $stmt->execute();
        $rows = $stmt->fetchAll();
        $n_m=0;
        $mail_ditte = preg_replace('/\s+/', '', $D->mail_ditte);
        foreach($rows as $row){
            $mail_ditte_esaminato = preg_replace('/\s+/', '', $row["mail_ditte"]);    
            if((levenshtein($mail_ditte,$mail_ditte_esaminato)<3) AND $D->id_ditte<>$row["id_ditte"]){
                $m.= $row["id_ditte"].' - '.$row["descrizione_ditte"].' - '.$row["mail_ditte"].'<br>';
                $n_m++;    
            }
        }
        $c .= "<hr>$n_m risultati email simili a ".$mail_ditte."<br>".$m;
        
                
        
        $res=array("result"=>"OK", "msg"=>"Ok", "html"=>$c );
        echo json_encode($res);   
     break;
        
     case "show_edit_scheda_ditta":
        
        $id = CAST_TO_INT($_POST["id"]);
        
        $D = new ditta($id);
        $editable = "editable";
         $button_save_note ='<button id="save_note" class="btn btn-success pull-right margin-top-10">Salva le note</button>';
        //SCHEDA DITTA
        $c.='<div id="mio_fornitore_container">
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <label for="id_ditte">ID:</label>
                    <p class="font-xl" id="id_ditte_1">'.$D->id_ditte.'</p>
                    <hr>
                    <label for="indirizzo">Indirizzo:</label>
                    <p id="indirizzo" class="editable_map" data-type="textarea" data-pk="'.$D->id_ditte.'">'.$D->indirizzo.'</p>
                    <div class="hidden" id="id_ditte" rel="'.$D->id_ditte.'"></div>
                    <div class="hidden" id="ditte_gc_lat" rel="'.$D->ditte_gc_lat.'"></div>
                    <div class="hidden" id="ditte_gc_lng" rel="'.$D->ditte_gc_lng.'"></div>
                    <div id="map-canvas" style="width:100%;height:280px;"></div>
                </div>';
         $c.='<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <label for="id_proponente">ID proponente:</label>
                    <p id="id_proponente" class="'.$editable.'" data-type="text" data-pk="'.$D->id_ditte.'">'.$D->id_proponente.'</p>
                    <hr class="simple">
                    
                    <label for="descrizione_ditte">Nome:</label>
                    <p id="descrizione_ditte" class="'.$editable.' font-xl" data-type="text" data-pk="'.$D->id_ditte.'">'.$D->descrizione_ditte.'</p>
                    
                    
                    
                    <label for="contatto">Contatto:</label>
                    <p id="contatto" class="'.$editable.'" data-type="text" data-pk="'.$D->id_ditte.'">'.$D->contatto.'</p>

                    <label for="telefono">Telefono:</label>
                    <p id="telefono" class="'.$editable.'" data-type="text" data-pk="'.$D->id_ditte.'">'.$D->telefono.'</p>
                    <label for="mail_ditte">Email:</label>
                    <p id="mail_ditte" class="'.$editable.'" data-type="email" data-pk="'.$D->id_ditte.'">'.$D->mail_ditte.'</p>
                    <label for="website">Link:</label>
                    <p id="website" class="'.$editable.'" data-type="text" data-pk="'.$D->id_ditte.'">'.$D->website.'</p>
                    <label for="iban">IBAN:</label>
                    <p id="iban" class="'.$editable.'" data-type="text" data-pk="'.$D->id_ditte.'">'.$D->iban.'</p>
                    <label for="iban">P_IVA / CF:</label>
                    <p id="piva_ditte" class="'.$editable.'" data-type="text" data-pk="'.$D->id_ditte.'">'.$D->P_IVA.'</p>
                    
                    <hr>
                    <label for="tag_ditte">Parole chiave:</label>
                    <p id="tag_ditte" class="'.$editable.'" data-type="textarea" data-pk="'.$D->id_ditte.'">'.$D->tag_ditte.'</p>
                    <hr>
                </div>

            </div>
            <div class="well well-sm margin-top-10 padding-5">
                <label for="note_ditte">Note:</label>
                <div id="note_ditte" class="summernote">'.$D->note_ditte.'</div>
                '.$button_save_note.'
                <div class="clearfix"></div>
            </div>
            <hr>
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                <h3>Statistiche</h3>
                <ul>
                    <li>Campi usati: <strong>'.$D->n_info_disponibili().'</strong> su <strong>'.$D->n_info_totali().'</strong></li>
                    <li>Listini associati: <strong>'.$D->n_listini_totali().'</strong></li>
                    <li>Dettagli totali: <strong>'.$D->n_dettagli_totali().'</strong></li>
                    <li>Ordini totali: <strong>'.$D->n_ordini_totali().'</strong></li>
                    <li>Gas che la usano: <strong>'.$D->n_gas_serviti().'</strong></li>
                </ul>
            </div>
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 disabled">
                <h3>Operazioni</h3>
                <ul>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                </ul>
            </div>
         </div>';   
     $res=array("result"=>"OK", "msg"=>"Ok", "html"=>$c );
     echo json_encode($res);   
     break;
     
        
     case "check_ditte_visiona":
     
     $html="Ciao";
     
     
     $res=array("result"=>"OK", "msg"=>"Ok", "html"=>$html );
     echo json_encode($res);
     break;   
     
     case "merge_ditte_effettua":

     $ditta_1 = CAST_TO_INT($_POST["ditta_1"],0);
     $ditta_2 = CAST_TO_INT($_POST["ditta_2"],0);

     if(!_USER_SUPERVISORE_ANAGRAFICHE){
        echo json_encode(array("result"=>"KO", "msg"=>"Non puoi."));
        die();
     }
     
     if(($ditta_1==0) | ($ditta_2==0)){
        echo json_encode(array("result"=>"KO", "msg"=>"Manca una ditta"));
        die();
     }

     $D1 = new ditta($ditta_1);
     $D2 = new ditta($ditta_2);

     //UPDATE TAGS
     $tags_1 = $D1->tag_ditte;
     $tags_2 = $D2->tag_ditte;
     if(CAST_TO_STRING($tags_1)<>""){
         if(CAST_TO_STRING($tags_2)<>""){
            $tags = $tags_1.', '.$tags_2;
         }else{
            $tags = $tags_1;
         }
     }else{
        $tags = $tags_2;
     }


     $sql_listini="UPDATE retegas_ditte set tag_ditte=:tags WHERE id_ditte=:ditta_1;";
     $stmt = $db->prepare($sql_listini);
     $stmt->bindParam(':tags', $tags, PDO::PARAM_STR);
     $stmt->bindParam(':ditta_1', $ditta_1, PDO::PARAM_INT);
     $stmt->execute();

     //UPDATE NOTE
     $note_1 = $D1->note_ditte;
     $note_2 = $D2->note_ditte;
     $note = $note_1.'<hr>'.$note_2;

     $sql_listini="UPDATE retegas_ditte set note_ditte=:note WHERE id_ditte=:ditta_1;";
     $stmt = $db->prepare($sql_listini);
     $stmt->bindParam(':note', $note, PDO::PARAM_STR);
     $stmt->bindParam(':ditta_1', $ditta_1, PDO::PARAM_INT);
     $stmt->execute();


     //UPDATE LISTINI
     $sql_listini="UPDATE retegas_listini set id_ditte=:ditta_1 WHERE id_ditte=:ditta_2;";
     $stmt = $db->prepare($sql_listini);
     $stmt->bindParam(':ditta_1', $ditta_1, PDO::PARAM_INT);
     $stmt->bindParam(':ditta_2', $ditta_2, PDO::PARAM_INT);
     $stmt->execute();
     $updated_listini = $stmt->rowCount();
     $log .= "Listini interessati: $updated_listini<br>";

     //UPDATE OPTIONS ---> OPTIONS CON DITTA 2 diventano con DITTA_1
     $sql_options="UPDATE retegas_options set id_ditta=:ditta_1 WHERE id_ditta=:ditta_2;";
     $stmt = $db->prepare($sql_options);
     $stmt->bindParam(':ditta_1', $ditta_1, PDO::PARAM_INT);
     $stmt->bindParam(':ditta_2', $ditta_2, PDO::PARAM_INT);
     $stmt->execute();
     $updated_options = $stmt->rowCount();
     $log .= "Options interessate: $updated_options<br>";

     //UPDATE MESSAGGI --->MESSAGGI CON DITTA2 diventano con id_ditta_1
     $sql_bacheca="UPDATE retegas_bacheca set id_ditta=:ditta_1 WHERE id_ditta=:ditta_2;";
     $stmt = $db->prepare($sql_bacheca);
     $stmt->bindParam(':ditta_1', $ditta_1, PDO::PARAM_INT);
     $stmt->bindParam(':ditta_2', $ditta_2, PDO::PARAM_INT);
     $stmt->execute();
     $updated_bacheca = $stmt->rowCount();
     $log .= "Messaggi interessati: $updated_bacheca<br>";
     
     //UPDATE DETTAGLI ORDINE ---> ID DITTA CON DITTA2 diventano come ID DITTA 1
     $sql_bacheca="UPDATE retegas_dettaglio_ordini set id_ditta=:ditta_1 WHERE id_ditta=:ditta_2;";
     $stmt = $db->prepare($sql_bacheca);
     $stmt->bindParam(':ditta_1', $ditta_1, PDO::PARAM_INT);
     $stmt->bindParam(':ditta_2', $ditta_2, PDO::PARAM_INT);
     $stmt->execute();
     $updated_bacheca = $stmt->rowCount();
     $log .= "Dettagli ordine interessati: $updated_bacheca<br>";

     //DELETE DITTA_2
     $sql_delete="DELETE FROM retegas_ditte WHERE id_ditte=:ditta_2 LIMIT 1;";
     $stmt = $db->prepare($sql_delete);
     $stmt->bindParam(':ditta_2', $ditta_2, PDO::PARAM_INT);
     $stmt->execute();
     $updated_deleted = $stmt->rowCount();
     $log .= "ELIMINATA DITTA #".$ditta_2." ".$D2->descrizione_ditte." di ".$D2->fullname_proponente." (".$D2->descrizione_gas_proponente.")<br>";


     //mail

     $messaggio = "<h3>Unione ditte</h3>
                    <p>Gentili utenti,</p>
                    <p>ricevete questa mail in quanto avete in passato inserito la ditta #".$D1->id_ditte." ".$D1->descrizione_ditte." in reteDES.</p>
                    <p>In data odierna l'ho unita con la ditta #".$D2->id_ditte." ".$D2->descrizione_ditte.", in quanto un suo duplicato all'interno del sistema.</p>
                    <p>Ogni listino e dato riferito ad esse *dovrebbe* essere stato spostato di conseguenza.</p>
                    <p>Questa mail è stata spedita per conoscenza, di seguito il dettaglio dell'operazione:</p>
                    <hr>
                    ".$log."
                    <hr>                    
                    <p>Cordiali saluti, "._USER_FULLNAME.".</p>";




     $oggetto = "[reteDES] Unione ditte";

     $profile = new Template('../../email_rd4/merge_ditte.html');
     $profile->set("messaggio", $messaggio );
     $messaggio = $profile->output();

     $mailFROM = _USER_MAIL;
     $fullnameFROM = _USER_FULLNAME;

     //DITTA 1;
     $mailTO = $D1->email_proponente;
     $fullnameTO = $D1->fullname_proponente;
     SEmail($fullnameTO,$mailTO,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"V4mergeditte");
     $log.="Mail ditta 1 inviata a ".$D1->email_proponente."<br>";

     //DITTA 2;
     sleep(1);
     $mailTO = $D2->email_proponente;
     $fullnameTO = $D2->fullname_proponente;

     SEmail($fullnameTO,$mailTO,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"V4mergeditte");
     $log.="Mail ditta 2 (Che non esiste più) inviata a ".$D2->email_proponente."<br>";

     $log = '<div class="jumbotron"><p>'.$log.'</p></div>';

     SEmail("Mauro","famiglia.morello@gmail.com",$fullnameFROM,$mailFROM,$oggetto,$messaggio.'<hr>'.$log,"V4mergeditte");

     $res=array("result"=>"OK", "msg"=>$log );
     echo json_encode($res);
     break;


     case "merge_ditte_visiona":

     $ditta_1 = CAST_TO_INT($_POST["ditta_1"],0);
     $ditta_2 = CAST_TO_INT($_POST["ditta_2"],0);

     if(!_USER_SUPERVISORE_ANAGRAFICHE){
        echo json_encode(array("result"=>"KO", "msg"=>"Non puoi."));
        die();
     }
     
     if(($ditta_1==0) | ($ditta_2==0)){
        echo json_encode(array("result"=>"KO", "msg"=>"Manca una ditta"));
        die();
     }

     //<i class="fa fa-arrow-left fa-pull-left fa-border" aria-hidden="true"></i> 
     //<i class="fa fa-arrow-right fa-pull-right fa-border" aria-hidden="true"></i>
     
     $D1 = new ditta($ditta_1);
     $D2 = new ditta($ditta_2);

     $html.='
     <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th></th>
                <th>'.$ditta_1.'</th>
                <th>'.$ditta_2.'</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th>Nome</th>
                <td><span class="editable" data-pk="'.$D1->id_ditte.'" data-type="text" data-name="descrizione_ditte">'.$D1->descrizione_ditte.'</span></td>
                <td><span class="editable" data-pk="'.$D2->id_ditte.'" data-type="text" data-name="descrizione_ditte">'.$D2->descrizione_ditte.'</span></td>
            </tr>
            
            <tr>
                <th>Mail</th>
                <td><span class="editable" data-pk="'.$D1->id_ditte.'" data-type="text" data-name="mail_ditte">'.$D1->mail_ditte.'</span></td>
                <td><span class="editable" data-pk="'.$D2->id_ditte.'" data-type="text" data-name="mail_ditte">'.$D2->mail_ditte.'</span></td>
            </tr>
            <tr>
                <th>Telefono</th>
                <td><span class="editable" data-pk="'.$D1->id_ditte.'" data-type="text" data-name="telefono">'.$D1->telefono.'</span></td>
                <td><span class="editable" data-pk="'.$D2->id_ditte.'" data-type="text" data-name="telefono">'.$D2->telefono.'</span></td>
            </tr>
            <tr>
                <th>Sito</th>
                <td><span class="editable" data-pk="'.$D1->id_ditte.'" data-type="text" data-name="website">'.$D1->website.'</span></td>
                <td><span class="editable" data-pk="'.$D2->id_ditte.'" data-type="text" data-name="website">'.$D2->website.'</span></td>
            </tr>
            <tr>
                <th rowspan="2">Indirizzo<br><span class="note">Qua non si può editare</span></th>
                <td>'.$D1->indirizzo.'</td>
                <td>'.$D2->indirizzo.'</td>
            </tr>
            <tr>
                
                <td><img border="0" src="https://maps.googleapis.com/maps/api/staticmap?markers='.$D1->ditte_gc_lat.','.$D1->ditte_gc_lng.'&zoom=9&size=200x100&key='.__GOOGLE_STATIC_MAP_API.'"></td>
                <td><img border="0" src="https://maps.googleapis.com/maps/api/staticmap?markers='.$D2->ditte_gc_lat.','.$D2->ditte_gc_lng.'&zoom=9&size=200x100&key='.__GOOGLE_STATIC_MAP_API.'"></td>
            </tr>
            <tr>
                <th>Tags</th>
                <td><span class="editable" data-pk="'.$D1->id_ditte.'" data-type="text" data-name="tag_ditte">'.$D1->tag_ditte.'</span></td>
                <td><span class="editable" data-pk="'.$D2->id_ditte.'" data-type="text" data-name="tag_ditte">'.$D2->tag_ditte.'</span></td>
            </tr>
            <tr>
                <th>P.IVA</th>
                <td><span class="editable" data-pk="'.$D1->id_ditte.'" data-type="text" data-name="piva_ditte">'.$D1->P_IVA.'</span></td>
                <td><span class="editable" data-pk="'.$D2->id_ditte.'" data-type="text" data-name="piva_ditte">'.$D2->P_IVA.'</span></td>
            </tr>
            <tr>
                <th>Listini</th>
                <td>'.$D1->n_listini_totali().'</td>
                <td>'.$D2->n_listini_totali().'</td>
            </tr>
            <tr>
                <th>Dettagli</th>
                <td>'.$D1->n_dettagli_totali().'</td>
                <td>'.$D2->n_dettagli_totali().'</td>
            </tr>
            <tr>
                <th>Data creazione</th>
                <td>'.$D1->data_creazione_completa.'</td>
                <td>'.$D2->data_creazione_completa.'</td>
            </tr>
            <tr>
                <th>Data scadenza ultimo listino</th>
                <td>'.$D1->data_scadenza_ultimo_listino().'</td>
                <td>'.$D2->data_scadenza_ultimo_listino().'</td>
            </tr>
            <tr>
                <th>Inserita da</th>
                <td>'.$D1->fullname_proponente.' ('.$D1->descrizione_gas_proponente.')</td>
                <td>'.$D2->fullname_proponente.' ('.$D2->descrizione_gas_proponente.')</td>
            </tr>
            <tr>
                <th></th>
                <td><button class="btn btn-primary  merge_go" data-ditta_1="'.$D1->id_ditte.'" data-ditta_2="'.$D2->id_ditte.'">RIMANE LA DITTA '.$D1->id_ditte.'</button></td>
                <td><button class="btn btn-primary  merge_go" data-ditta_1="'.$D2->id_ditte.'" data-ditta_2="'.$D1->id_ditte.'">RIMANE LA DITTA '.$D2->id_ditte.'</button></td>
            </tr>
        </tbody>
     </table>
     ';


     $res=array("result"=>"OK", "msg"=>"Ok", "html"=>$html );
     echo json_encode($res);
     break;
     
     case "fornitore_unbannato":
        //esiste
        $id_ditta=CAST_TO_INT($_POST["id_ditta"]);
        $id_user=_USER_ID;

        $stmt = $db->prepare("DELETE FROM retegas_options
                         WHERE id_ditta=:id_ditta AND id_user=:id_user AND chiave='_USER_FORNITORE_BANNATO'");
        $stmt->bindParam(':id_ditta', $id_ditta, PDO::PARAM_INT);
        $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        $stmt->execute();
        $msg="Riceverai di nuovo notifiche per questo fornitore.";


       $res=array("result"=>"OK", "msg"=>$msg );
       echo json_encode($res);
     break;


     /*BANNATO*/
     case "fornitore_bannato":
        //esiste
        $id_ditta=CAST_TO_INT($_POST["id_ditta"]);
        $id_user=_USER_ID;

        $stmt = $db->prepare("SELECT COUNT(valore_int) as conto FROM retegas_options
                             WHERE id_ditta=:id_ditta AND id_user=:id_user AND chiave='_USER_FORNITORE_BANNATO'");
        $stmt->bindParam(':id_ditta', $id_ditta, PDO::PARAM_INT);
        $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        if($row["conto"]>0){
            $stmt = $db->prepare("DELETE FROM retegas_options
                             WHERE id_ditta=:id_ditta AND id_user=:id_user AND chiave='_USER_FORNITORE_BANNATO'");
            $stmt->bindParam(':id_ditta', $id_ditta, PDO::PARAM_INT);
            $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
            $stmt->execute();
            $result="NO";
            $msg="RICEVERAI notifiche su ordini di questo fornitore";
        }else{
            $stmt = $db->prepare("INSERT INTO retegas_options
                                    (id_ditta,id_gas,chiave,valore_int,id_user) VALUES (:id_ditta,'"._USER_ID_GAS."','_USER_FORNITORE_BANNATO',1,'"._USER_ID."')");
            $stmt->bindParam(':id_ditta', $id_ditta, PDO::PARAM_INT);
            $stmt->execute();
            $result="SI";
            $msg="NON RICEVERAI notifiche su ordini di questo fornitore";
        }

       $res=array("result"=>"OK", "msg"=>$msg, "bannato"=>$result );
       echo json_encode($res);
     break;

     /*PREFERITO*/
     case "fornitore_preferito":
        //esiste
        $id_ditta=CAST_TO_INT($_POST["id_ditta"]);
        $id_gas=_USER_ID_GAS;

        $stmt = $db->prepare("SELECT COUNT(valore_int) as conto FROM retegas_options
                             WHERE id_ditta=:id_ditta AND id_gas=:id_gas AND chiave='_GAS_FORNITORE_PREFERITO'");
        $stmt->bindParam(':id_ditta', $id_ditta, PDO::PARAM_INT);
        $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        if($row["conto"]>0){
            $stmt = $db->prepare("DELETE FROM retegas_options
                             WHERE id_ditta=:id_ditta AND id_gas=:id_gas AND chiave='_GAS_FORNITORE_PREFERITO'");
            $stmt->bindParam(':id_ditta', $id_ditta, PDO::PARAM_INT);
            $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
            $stmt->execute();
            $result="NO";
        }else{
            $stmt = $db->prepare("INSERT INTO retegas_options
                                    (id_ditta,id_gas,chiave,valore_int,id_user) VALUES (:id_ditta,:id_gas,'_GAS_FORNITORE_PREFERITO',1,'"._USER_ID."')");
            $stmt->bindParam(':id_ditta', $id_ditta, PDO::PARAM_INT);
            $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
            $stmt->execute();
            $result="SI";
        }

       $res=array("result"=>"OK", "msg"=>"Ok", "preferito"=>$result );
       echo json_encode($res);
     break;

     case "save_coord":
        //esiste
        if(!_USER_SUPERVISORE_ANAGRAFICHE){
            $and =" AND id_proponente='"._USER_ID."'";
        }else{
            $and='';
        }
        
        
        $stmt = $db->prepare("UPDATE retegas_ditte SET ditte_gc_lat=:ditte_gc_lat, ditte_gc_lng= :ditte_gc_lng
                             WHERE id_ditte=:id_ditte $and LIMIT 1;");
        $stmt->bindParam(':id_ditte', $_POST['id_ditte'], PDO::PARAM_INT);
        $stmt->bindParam(':ditte_gc_lat', $_POST['ditte_gc_lat'], PDO::PARAM_STR);
        $stmt->bindParam(':ditte_gc_lng', $_POST['ditte_gc_lng'], PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()==1){;
            $res=array("result"=>"OK", "msg"=>"Nuove coordinate OK." );
        }else{
            $res=array("result"=>"KO", "msg"=>"Errore nel DB... RC: ".$stmt->rowCount() );
        }

        echo json_encode($res);
     break;
     case "save_note":
        //esiste
        if(!_USER_SUPERVISORE_ANAGRAFICHE){
            $and =" AND id_proponente='"._USER_ID."'";
        }else{
            $and='';
        }
        $stmt = $db->prepare("UPDATE retegas_ditte SET note_ditte= :note_ditte
                             WHERE id_ditte=:id_ditte $and LIMIT 1;");
        $stmt->bindParam(':id_ditte', $_POST['id_ditte'], PDO::PARAM_INT);
        $stmt->bindParam(':note_ditte', $_POST['note_ditte'], PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()==1){;
            $res=array("result"=>"OK", "msg"=>"Note salvate" );
        }else{
            $res=array("result"=>"KO", "msg"=>"Errore nel DB... RC: ".$stmt->rowCount() );
        }

        echo json_encode($res);
     break;

     case "aggiungi_ditta":

    if($_POST["value"]==""){
        echo json_encode(array("result"=>"KO", "msg"=>"Non puoi lasciare questo campo vuoto"));
        die();
    }

    //esiste
    $stmt = $db->prepare("INSERT INTO retegas_ditte (id_proponente,descrizione_ditte, data_creazione) VALUES ('"._USER_ID."',:nome, NOW()) ;");
    $stmt->bindParam(':nome', $_POST['value'], PDO::PARAM_STR);
    $stmt->execute();

    $id = $db->lastInsertId();

    if($stmt->rowCount()==1){
        $res=array("result"=>"OK", "msg"=>"Ditta aggiunta", "id"=>$id );
    }else{
        $res=array("result"=>"KO", "msg"=>"Errore nel db." );
    }

    echo json_encode($res);

    break;
    //-------------------------------------------------------------------------------------

    case "delete_ditta":

    //esiste
    $stmt = $db->prepare("DELETE FROM retegas_ditte WHERE id_ditte=:id_ditta AND id_proponente='"._USER_ID."' LIMIT 1;");
    $stmt->bindParam(':id_ditta', $_POST['value'], PDO::PARAM_INT);
    $stmt->execute();
    if($stmt->rowCount()==1){
        $res=array("result"=>"OK", "msg"=>"Ditta Eliminata" );
    }else{
        $res=array("result"=>"KO", "msg"=>"Errore nel db ". $stmt->rowCount() );
    }

    echo json_encode($res);

    break;
    //-------------------------------------------------------------------------------------

     default:
        $res=array("result"=>"KO", "msg"=>"Comando non riconosciuto" );
        echo json_encode($res);
     break;
    }
};

if(!empty($_POST["name"])){
    switch ($_POST["name"]) {

    case "id_proponente":
        //esiste
        if(!_USER_SUPERVISORE_ANAGRAFICHE){
            $and =" AND id_proponente='"._USER_ID."';";
        }else{
            $and='';
        }
        
        $stmt = $db->prepare("UPDATE retegas_ditte SET id_proponente= :id_proponente
                             WHERE id_ditte=:id_ditte $and LIMIT 1;");
        $stmt->bindParam(':id_ditte', $_POST['pk'], PDO::PARAM_INT);
        $stmt->bindParam(':id_proponente', $_POST['value'], PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()==1){;
            $res=array("result"=>"OK", "msg"=>"ID proponente salvato." );
        }else{
            $res=array("result"=>"KO", "msg"=>"Errore nel DB..." );
        }

        echo json_encode($res);
     break;    
    
    
     
    case "contatto":
        //esiste
        if(!_USER_SUPERVISORE_ANAGRAFICHE){
            $and =" AND id_proponente='"._USER_ID."';";
        }else{
            $and='';
        }
        
        $stmt = $db->prepare("UPDATE retegas_ditte SET contatto= :contatto
                             WHERE id_ditte=:id_ditte $and LIMIT 1;");
        $stmt->bindParam(':id_ditte', $_POST['pk'], PDO::PARAM_INT);
        $stmt->bindParam(':contatto', $_POST['value'], PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()==1){;
            $res=array("result"=>"OK", "msg"=>"Contatto salvato." );
        }else{
            $res=array("result"=>"KO", "msg"=>"Errore nel DB..." );
        }

        echo json_encode($res);
     break;

     case "descrizione_ditte":
        //esiste
        
        if(!_USER_SUPERVISORE_ANAGRAFICHE){
            $and =" AND id_proponente='"._USER_ID."'";
        }else{
            $and='';
        }
        
        $stmt = $db->prepare("UPDATE retegas_ditte SET descrizione_ditte= :descrizione_ditte
                             WHERE id_ditte=:id_ditte $and LIMIT 1;");
        $stmt->bindParam(':id_ditte', $_POST['pk'], PDO::PARAM_INT);
        $stmt->bindParam(':descrizione_ditte', $_POST['value'], PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()==1){;
            $res=array("result"=>"OK", "msg"=>"Nuovo nome salvato." );
        }else{
            $res=array("result"=>"KO", "msg"=>"Errore nel DB..." );
        }

        echo json_encode($res);
     break;

     case "telefono":
        //esiste
        if(!_USER_SUPERVISORE_ANAGRAFICHE){
            $and =" AND id_proponente='"._USER_ID."'";
        }else{
            $and='';
        }
        
        
        $stmt = $db->prepare("UPDATE retegas_ditte SET telefono= :telefono
                             WHERE id_ditte=:id_ditte $and LIMIT 1;");
        $stmt->bindParam(':id_ditte', $_POST['pk'], PDO::PARAM_INT);
        $stmt->bindParam(':telefono', $_POST['value'], PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()==1){;
            $res=array("result"=>"OK", "msg"=>"Nuovo telefono salvato." );
        }else{
            $res=array("result"=>"KO", "msg"=>"Errore nel DB..." );
        }

        echo json_encode($res);
     break;

     case "website":
        //esiste
        
        if(!_USER_SUPERVISORE_ANAGRAFICHE){
            $and =" AND id_proponente='"._USER_ID."'";
        }else{
            $and='';
        }
        
        $stmt = $db->prepare("UPDATE retegas_ditte SET website= :website
                             WHERE id_ditte=:id_ditte $and LIMIT 1;");
        $stmt->bindParam(':id_ditte', $_POST['pk'], PDO::PARAM_INT);
        $stmt->bindParam(':website', $_POST['value'], PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()==1){;
            $res=array("result"=>"OK", "msg"=>"Nuovo link salvato." );
        }else{
            $res=array("result"=>"KO", "msg"=>"Errore nel DB..." );
        }

        echo json_encode($res);
     break;

     case "mail_ditte":
        //esiste
        if(!_USER_SUPERVISORE_ANAGRAFICHE){
            $and =" AND id_proponente='"._USER_ID."'";
        }else{
            $and='';
        }
        
        
        $stmt = $db->prepare("UPDATE retegas_ditte SET mail_ditte= :mail_ditte
                             WHERE id_ditte=:id_ditte $and LIMIT 1;");
        $stmt->bindParam(':id_ditte', $_POST['pk'], PDO::PARAM_INT);
        $stmt->bindParam(':mail_ditte', $_POST['value'], PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()==1){;
            $res=array("result"=>"OK", "msg"=>"Nuova mail salvata." );
        }else{
            $res=array("result"=>"KO", "msg"=>"Errore nel DB..." );
        }

        echo json_encode($res);
     break;
                                                            
     case "indirizzo":
        //esiste
        if(!_USER_SUPERVISORE_ANAGRAFICHE){
            $and =" AND id_proponente='"._USER_ID."' ";
        }else{
            $and='';
        }
        
        $stmt = $db->prepare("UPDATE retegas_ditte SET indirizzo= :indirizzo
                             WHERE id_ditte=:id_ditte $and LIMIT 1;");
        $stmt->bindParam(':id_ditte', $_POST['pk'], PDO::PARAM_INT);
        $stmt->bindParam(':indirizzo', $_POST['value'], PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()==1){;
            $res=array("result"=>"OK", "msg"=>$_POST['value'] );
        }else{
            $res=array("result"=>"KO", "msg"=>"Errore nel DB..." );
        }

        echo json_encode($res);
     break;

     case "piva_ditte":
        //esiste
        
        if(!_USER_SUPERVISORE_ANAGRAFICHE){
            $and =" AND id_proponente='"._USER_ID."'";
        }else{
            $and='';
        }
        
        $stmt = $db->prepare("UPDATE retegas_ditte SET P_IVA=:P_IVA
                             WHERE id_ditte=:id_ditte $and LIMIT 1;");
        $stmt->bindParam(':id_ditte', $_POST['pk'], PDO::PARAM_INT);
        $stmt->bindParam(':P_IVA', $_POST['value'], PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()==1){;
            $res=array("result"=>"OK", "msg"=>$_POST['value'] );
        }else{
            $res=array("result"=>"KO", "msg"=>"Errore nel DB..." );
        }

        echo json_encode($res);
     break;
     case "iban":
        //esiste
        
        if(!_USER_SUPERVISORE_ANAGRAFICHE){
            $and =" AND id_proponente='"._USER_ID."'";
        }else{
            $and='';
        }
        
        $stmt = $db->prepare("UPDATE retegas_ditte SET iban=:iban
                             WHERE id_ditte=:id_ditte $and LIMIT 1;");
        $stmt->bindParam(':id_ditte', $_POST['pk'], PDO::PARAM_INT);
        $stmt->bindParam(':iban', $_POST['value'], PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()==1){;
            $res=array("result"=>"OK", "msg"=>$_POST['value'] );
        }else{
            $res=array("result"=>"KO", "msg"=>"Errore nel DB..." );
        }

        echo json_encode($res);
     break;
     case "tag_ditte":
        //esiste
        if(!_USER_SUPERVISORE_ANAGRAFICHE){
            $and =" AND id_proponente='"._USER_ID."'";
        }else{
            $and='';
        }
        
        $stmt = $db->prepare("UPDATE retegas_ditte SET tag_ditte= :tag_ditte
                             WHERE id_ditte=:id_ditte $and LIMIT 1;");
        $stmt->bindParam(':id_ditte', $_POST['pk'], PDO::PARAM_INT);
        $stmt->bindParam(':tag_ditte', $_POST['value'], PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()==1){;
            $res=array("result"=>"OK", "msg"=>$_POST['value'] );
        }else{
            $res=array("result"=>"KO", "msg"=>"Errore nel DB..." );
        }

        echo json_encode($res);
     break;

     default:
        $res=array("result"=>"KO", "msg"=>"Comando non riconosciuto" );
        echo json_encode($res);
     break;
    }
}
