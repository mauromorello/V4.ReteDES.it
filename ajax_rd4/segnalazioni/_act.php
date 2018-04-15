<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.listino.php");
require_once("../../lib_rd4/class.rd4.ditta.php");
require_once("../../lib_rd4/class.rd4.gas.php");

require_once("../../lib_rd4/htmlpurifier-4.7.0/library/HTMLPurifier.auto.php");


if(!empty($_POST["act"])){
    switch ($_POST["act"]) {

        case "hide_segnalazione":
            $id_segnalazione = CAST_TO_INT($_POST["id_segnalazione"],0);
            
            $sql="UPDATE retegas_segnalazioni SET id_hider='"._USER_ID."', is_hidden=1, data_hide=NOW() WHERE id_segnalazione=:id_segnalazione LIMIT 1;";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_segnalazione', $id_segnalazione, PDO::PARAM_STR);
            $stmt->execute();
        
            $res=array("result"=>"OK", "msg"=>"Segnalazione nascosta."  );
            echo json_encode($res);die();
            break;
            
        case "do_segnalazione_ditta":

            $messaggio_testo  = CAST_TO_STRING($_POST["messaggio"]);
            $id_ditte = CAST_TO_INT($_POST["id_ditte"],0);

            if($id_ditte==0){
                $res=array("result"=>"KO", "msg"=>"Manca id ditta."  );
                echo json_encode($res);die();
            }

            $D = new ditta($id_ditte);

            if($messaggio_testo==""){
                $res=array("result"=>"KO", "msg"=>"Messaggio vuoto."  );
                echo json_encode($res);die();
            }else{
                $id_segnalante=_USER_ID;
                
                $sql="INSERT INTO retegas_segnalazioni (id_segnalante, id_ditta, testo_segnalazione,data_segnalazione) VALUES (:id_segnalante,:id_ditta,:testo_segnalazione,NOW());";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':id_ditta', $id_ditte, PDO::PARAM_INT);
                $stmt->bindParam(':id_segnalante', $id_segnalante, PDO::PARAM_INT);
                $stmt->bindParam(':testo_segnalazione', $messaggio_testo, PDO::PARAM_STR);
                $stmt->execute();
                
                
                $messaggio_testo = '<hr>
                <strong>SEGNALAZIONE DITTA #'.$id_ditte.' <a href="'.$D->ditta_url.'" TARGET="_BLANK">'.$D->descrizione_ditte.'</a></strong>

                </hr>
                <br><br>'.$messaggio_testo;
                
            }

            
            $G=new gas($D->id_gas_proponente);
                
            $r[]=array( 'email' => $D->email_proponente,
                        'name' => $D->fullname_proponente
                        );
            $r[]=array( 'email' => _EMAIL_SUPERADMIN,
                        'name' => _FULLNAME_SUPERADMIN
                        );
            $r[]=array( 'email' => $G->email_referente_gas,
                        'name' =>  $G->fullname_referente_gas
                        );

            $fullnameFROM = _USER_FULLNAME;
            $mailFROM = _USER_MAIL;
            $oggetto = "[reteDES] "._USER_FULLNAME." segnala ditta #".$id_ditte;
            $profile = new Template('../../email_rd4/basic_2.html');
            $profile->set("fullnameFROM", _USER_FULLNAME );
            $profile->set("messaggio", $messaggio_testo);

            $messaggio = $profile->output();

            SEmailMulti($r,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"V4segnalaDitta");
            unset ($profile);


            $res=array("result"=>"OK", "msg"=>"Segnalazione inviata"  );

            echo json_encode($res);
        break;
        
        case "do_segnalazione_listino":
            $messaggio_testo  = trim(strip_tags(CAST_TO_STRING($_POST["messaggio"])));
            $id_listino = CAST_TO_INT($_POST["id_listino"],0);



            if($id_listino==0){
                $res=array("result"=>"KO", "msg"=>"Manca id lsitino."  );
                echo json_encode($res);die();
            }

            $L = new listino($id_listino);

            if($messaggio_testo==""){
                $res=array("result"=>"KO", "msg"=>"Messaggio vuoto."  );
                echo json_encode($res);die();
            }else{
                
                $id_segnalante=_USER_ID;
                
                $sql="INSERT INTO retegas_segnalazioni (id_segnalante, id_listino, testo_segnalazione,data_segnalazione) VALUES (:id_segnalante,:id_listino,:testo_segnalazione,NOW());";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':id_listino', $id_listino, PDO::PARAM_INT);
                $stmt->bindParam(':id_segnalante', $id_segnalante, PDO::PARAM_INT);
                $stmt->bindParam(':testo_segnalazione', $messaggio_testo, PDO::PARAM_STR);
                $stmt->execute();
                
                $messaggio_testo = '<hr>
                <strong>SEGNALAZIONE LISTINO #'.$id_listino.' <a href="'.$L->url_listino.'" TARGET="_BLANK">'.$L->descrizione_listini.'</a></strong>

                </hr>
                <br><br>'.$messaggio_testo;
                
            }

            foreach($L->lista_referenti_extra() as $row){
                $r[]=array( 'email' => $row["email"],
                            'name' => $row["fullname"]
                            );    
            }
            
                
            $r[]=array( 'email' => $L->email_proprietario_listino,
                        'name' => $L->fullmane_proprietario_listino
                        );
            $r[]=array( 'email' => _EMAIL_SUPERADMIN,
                        'name' => _FULLNAME_SUPERADMIN
                        );
            

            $fullnameFROM = _USER_FULLNAME;
            $mailFROM = _USER_MAIL;
            $oggetto = "[reteDES] "._USER_FULLNAME." segnala listino #".$id_listino;
            $profile = new Template('../../email_rd4/basic_2.html');
            $profile->set("fullnameFROM", _USER_FULLNAME );
            $profile->set("messaggio", $messaggio_testo);

            $messaggio = $profile->output();
            //echo $messaggio; die();

            SEmailMulti($r,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"V4segnalaListino");
            //SSparkPostMulti($r,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"V4richiestaInfo");

            unset ($profile);


            $res=array("result"=>"OK", "msg"=>"Segnalazione inviata"  );

            echo json_encode($res);
        break;    
        
    


        default :
        $res=array("result"=>"KO", "msg"=>"Comando '".$_POST["act"]."' non riconosciuto" );
        echo json_encode($res);
        break;

    }
}
if(!empty($_POST["name"])){
    switch ($_POST["name"]) {
        default :
        $res=array("result"=>"KO", "msg"=>"Comando '".$_POST["name"]."' non riconosciuto" );
        echo json_encode($res);
        break;
    }
}