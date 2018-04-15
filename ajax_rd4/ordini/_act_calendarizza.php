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

    case "do_calendarizzazione":
        $id_ordine = CAST_TO_INT($_POST["id_ordine"]);
        $id_gas=_USER_ID_GAS;

        //CONTROLLI
        if (!(_USER_PERMISSIONS & perm::puo_creare_ordini)){
             $res=array("result"=>"KO", "msg"=>"Non puoi creare ordini, e nemmeno calendarizzarli.");
             echo json_encode($res);
             die();
        }

        if($id_ordine<1){
            $res=array("result"=>"KO", "msg"=>"Manca ID ordine");
            echo json_encode($res);
            die();
        }
        $O=new ordine($id_ordine);

        //PRENDO I DATI DELL'ORDINE
        $ora_apertura_padre = $O->data_apertura_solo_ora;
        $ora_chiusura_padre = $O->data_chiusura_solo_ora;
        $log.= 'ora apertura padre: '.$ora_apertura_padre."\r\n";

        //Giorni
        $now = strtotime($O->data_chiusura);
        $your_date = strtotime($O->data_apertura);
        $datediff = abs($now - $your_date);
        $giorni =  floor($datediff/(60*60*24));
        $log.="giorni = ".$giorni."\r\n";

        //SOLO CASSATI
        $solocassati = $O->solo_cassati;

        //LISTINO
        $idlistino = $O->id_listini;

        //REFERENTE
        $idreferente = $O->id_referente_ordine;

        //NOTE
        $noteordine = $O->note_ordini;


        //PRENDO I DATI DELL'ORDINE

        //Vedo se ci sono calendarizzazioni
        $sql="SELECT COUNT(*) as conto FROM retegas_options WHERE id_ordine=:id_ordine AND chiave='_CALENDARIZZA' AND valore_int<1;";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_ordine', $id_ordine , PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        if(CAST_TO_INT($row["conto"],0)<1){
            $res=array("result"=>"KO", "msg"=>"Non c'è calendarizzazione per questo ordine");
            echo json_encode($res);
            die();
        }

        //scorro calendarizzazione
        $sql="SELECT *  FROM retegas_options  WHERE id_ordine=:id_ordine AND chiave='_CALENDARIZZA' AND valore_int<1 ORDER BY valore_data ASC";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_ordine', $id_ordine , PDO::PARAM_INT);
        $stmt->execute();
        $rowsC = $stmt->fetchAll();
        $i=0;
        foreach($rowsC as $rowC){
            $i++;
            $l.="Data: ". conv_date_from_db($rowC["valore_data"])."<br>";
            //PER OGNI CALENDARIZZAZIONE

            //DATA APERTURA
            $dataapertura = substr($rowC["valore_data"],0,10)." ".$ora_apertura_padre;
            $log.="dataapertura = ".$dataapertura."\r\n";

            //CHIUSURA
            $datachiusura = date('Y-m-d', strtotime($dataapertura. ' + '.$giorni.' days'))." ".$ora_chiusura_padre;
            $log.="datachiusura = ".$datachiusura."\r\n";

            //NOME
            $descrizioneordine = $rowC["valore_text"];
            $log.="ValoreTEXT = ".$descrizioneordine."\r\n";

            //INSERISCO
            //esiste
            $sql="INSERT INTO retegas_ordini
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
                calendario)
                VALUES
                (:idlistino,
                 :idreferente,
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
                '$id_ordine');";

            $log.=$sql."\r\n";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':idlistino', $idlistino, PDO::PARAM_INT);
            $stmt->bindParam(':idreferente', $idreferente, PDO::PARAM_INT);

            $stmt->bindParam(':nomeordine', $descrizioneordine, PDO::PARAM_STR);
            $stmt->bindParam(':dataapertura', $dataapertura, PDO::PARAM_STR);
            $stmt->bindParam(':datachiusura', $datachiusura, PDO::PARAM_STR);
            $stmt->bindParam(':noteordine', $noteordine, PDO::PARAM_STR);
            $stmt->execute();
            $newId = $db->lastInsertId();
            if($stmt->rowCount()==1){;
                $ins++;
                $id_gas = _USER_ID_GAS;
                $stmt = $db->prepare("SELECT * FROM retegas_gas WHERE id_gas=:id_gas");
                $stmt->bindValue(':id_gas', $id_gas, PDO::PARAM_INT);
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
                    :idreferente,
                    '"._USER_ID_GAS."',
                    '".$row_gas["comunicazione_referenti"]."',
                    '0',
                    '".$row_gas["maggiorazione_ordini"]."');");

               $stmt->bindParam(':idreferente', $idreferente, PDO::PARAM_INT);
               $stmt->execute();
            }else{
                $err++;
            }



            //PER OGNI CALENDARIZZAZIONE
        }
        //scorro calendarizzazione


        //SE sono a posto cancello la calendarizzazione
        if($err==0){
            $sql="UPDATE retegas_options SET valore_int=1 WHERE chiave='_CALENDARIZZA' AND id_ordine=:id_ordine";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_ordine', $id_ordine , PDO::PARAM_INT);
            $stmt->execute();
        }else{
            $res=array("result"=>"KO", "msg"=>"Errore del cervello quantico.", "log"=>$log,"html"=>$html);
            echo json_encode($res);
            die();
        }


        $html=' <div class="jumbotron">
                    <p>La calendarizzazione è avvenuta correttamente.<br>Sono stati programmati n.'.$ins.' ordini nuovi.</p>
                    <p class="note">Ricordati di modificare se necessario i parametri dei nuovi ordini.</p>
                </div>';


        if(_USER_PERMISSIONS & perm::puo_gestire_retegas){

        }else{
            //$log='';
        }

        $res=array("result"=>"OK", "msg"=>"ok", "log"=>$log,"html"=>$html);
        echo json_encode($res);

        break;
    case "delete_calendarizzazione":
        $id_option = CAST_TO_INT($_POST["id_option"]);

        $sql="DELETE FROM retegas_options WHERE id_option=:id_option AND chiave='_CALENDARIZZA' AND valore_int=0 LIMIT 1;";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_option', $id_option , PDO::PARAM_INT);
        $stmt->execute();

        $res=array("result"=>"OK", "msg"=>"OK",);
        echo json_encode($res);
    break;


    case "show_lista_calendarizzazione":

        $id_ordine = CAST_TO_INT($_POST["id_ordine"]);
        $id_gas=_USER_ID_GAS;
        if($id_ordine<1){
            $res=array("result"=>"KO", "msg"=>"Manca ID ordine");
            echo json_encode($res);
            die();
        }
        $O=new ordine($id_ordine);
        $sql="SELECT O.* from retegas_options O WHERE chiave='_CALENDARIZZA' AND id_ordine=:id_ordine AND id_gas=:id_gas ORDER BY valore_data ASC;";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_ordine', $id_ordine , PDO::PARAM_INT);
        $stmt->bindParam(':id_gas', $id_gas , PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();
        foreach($rows as $row){
            if(CAST_TO_STRING($row["valore_text"])==""){
                $descr =    $O->descrizione_ordini;
            }else{
                $descr =    CAST_TO_STRING($row["valore_text"]);
            }

            if(CAST_TO_INT($row["valore_int"],0)>0){
                $icona='<span class="pull-right" ><i class="fa fa-calendar fa-2x text-info"></i></span>';
                $editable='';
            }else{
                $icona='<span style="cursor:pointer;" class="pull-right delete_calendarizzato" data-id_option="'.$row["id_option"].'"><i class="fa fa-times text-danger"></i></span>';
                $editable=' class="editable_descrizione_calendarizzazione" data-pk="'.$row["id_option"].'" ';
            }

            $i++;
            $li.='<li class="list-group-item">
                    '.$icona.'
                    <strong>APRE IL '.conv_date_from_db($row["valore_data"]).'</strong><br>
                    <span '.$editable.'>'.$descr.'</span>
                  </li>';
        }
        if($i>0){
            $li='<ul class="list-unstyled">'.$li.'</ul>';
        }else{
            $li='<p>Nessun ordine programmato.</p>';
        }

        $res=array("result"=>"OK", "html"=>$li);
        echo json_encode($res);
    break;


    case "toggle_calendarizzazione":

        $id_ordine = CAST_TO_INT($_POST["id_ordine"]);
        $O=new ordine($id_ordine);

        if($id_ordine<1){
            $res=array("result"=>"KO", "msg"=>"Manca ID ordine");
            echo json_encode($res);
            die();
        }
        $data_ordine=CAST_TO_STRING($_POST["date"]);
        if(!validateDate($data_ordine)){
            $res=array("result"=>"KO", "msg"=>"Data non valida");
            echo json_encode($res);
            die();
        }
        $data_ordine.=" 00:00:00";

        $today = date("Y-m-d H:i:s");
        if ($data_ordine < $today) {
            $res=array("result"=>"KO", "msg"=>"Non puoi programmare ordini nel passato.");
            echo json_encode($res);
            die();
        }

        $id_gas=_USER_ID_GAS;



        $sql="DELETE FROM retegas_options WHERE id_ordine=:id_ordine AND chiave='_CALENDARIZZA' AND id_gas=:id_gas AND valore_data=:date AND valore_int<1 LIMIT 1;";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_ordine', $id_ordine , PDO::PARAM_INT);
        $stmt->bindParam(':id_gas', $id_gas , PDO::PARAM_INT);
        $stmt->bindParam(':date', $data_ordine , PDO::PARAM_STR);
        $stmt->execute();
        $resa="TOLTO $sql";
        if($stmt->rowCount()==0){
            $sql="INSERT INTO retegas_options (id_ordine,chiave,id_gas,valore_data,valore_int, valore_text) VALUES (:id_ordine,'_CALENDARIZZA',:id_gas,:valore_data,0,:valore_text );";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_ordine', $id_ordine , PDO::PARAM_INT);
            $stmt->bindParam(':id_gas', $id_gas , PDO::PARAM_INT);
            $stmt->bindParam(':valore_data', $data_ordine , PDO::PARAM_STR);
            $stmt->bindParam(':valore_text', $O->descrizione_ordini , PDO::PARAM_STR);
            $stmt->execute();
            $resa="AGGIUNTO $sql";
        }




    $res=array("result"=>"OK", "msg"=>"OK", "html"=>$li);
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
         case "edit_descrizione_calendarizzazione":
         if(trim($_POST['value'])==""){
            echo json_encode(array("result"=>"KO", "msg"=>"Devi immettere un titolo" ));
            die();
         }
         $stmt = $db->prepare("UPDATE retegas_options SET valore_text = :descrizione_ordini
                             WHERE id_option=:id_option LIMIT 1;");

        $stmt->bindParam(':descrizione_ordini', $_POST['value'], PDO::PARAM_STR);
        $stmt->bindParam(':id_option', $_POST['pk'], PDO::PARAM_INT);

        $stmt->execute();
        $res=array("result"=>"OK", "msg"=>"OK" );
        echo json_encode($res);
     break;

     default:
        $res=array("result"=>"KO", "msg"=>"Comando non riconosciuto" );
        echo json_encode($res);
     break;
    }
}