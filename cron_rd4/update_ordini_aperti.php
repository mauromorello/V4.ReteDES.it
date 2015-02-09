<?php

if($_GET["q"]=="430ri92rjdkf0irordòlfksdòfìsdfpl234"){
    $skip_check=true;
}else{
    $skip_check=false;
}
require_once("inc/init.php");
if(file_exists("../../lib_rd4/class.rd4.ordine.php")){require_once("../../lib_rd4/class.rd4.ordine.php");}
if(file_exists("../lib_rd4/class.rd4.ordine.php")){require_once("../lib_rd4/class.rd4.ordine.php");}


// seleziona gli ordini ancora da aprire  (1) con data apertura già passata;

$stmt = $db->prepare("SELECT * from retegas_ordini
             WHERE ((retegas_ordini.id_stato='1')
             AND (retegas_ordini.data_apertura <= now()));");

$stmt->execute();
if($stmt->rowCount()>0){

    $loggone .= "Ci sono ordini futuri da rendere presenti <br>";
    $rows = $stmt->fetchAll();

    foreach($rows as $row){
            $n++;
            $O = new ordine ($row["id_ordini"]);
            $ordine = $row["id_ordini"];
            $descrizione = $O->descrizione_ordini;
            $note = $O->note_ordini;

            $loggone .= "<hr>".$ordine." ".$descrizione. ":<br>";

            if($note<>""){
                $note ="<p>Il gestore ha aggiunto delle note : <p><br><p>$note</p>";
            }else{
                $note ="";
            }

            $data_chiusura =conv_date_from_db($row["data_chiusura"]);

            $qry="SELECT
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
                maaking_users.isactive ='1' AND
                retegas_ordini.id_ordini =:ordine";

            $stmt = $db->prepare($qry);
            $stmt->bindParam(':ordine', $ordine, PDO::PARAM_INT);
            $stmt->execute();
            $rows_m = $stmt->fetchAll(PDO::FETCH_ASSOC);

            //Crea la lista dei destinatari
            foreach($rows_m as $row_m){
                $query_p = "SELECT valore_text from retegas_options WHERE id_user='".$row_m["userid"]."' AND chiave='_V4_AVVISIAPERTURE' LIMIT 1;";
                $stmt = $db->prepare($query_p);
                $stmt->execute();
                $row_p = $stmt->fetch();
                if($row_p["valore_text"]<>"NO"){
                    $r[]=array( 'email' => $row_m["email"],
                        'name' => $row_m["fullname"],
                        'type' => 'bcc');
                    $loggone.= $row_m["fullname"]." (".$row_m["email"].") SI; <br>";
                }else{
                    $loggone.= $row_m["fullname"]." (".$row_m["email"].") NO; <br>";
                }


            }

            //MAIL------------------------------------------------
            $fullnameFROM = "ReteDES.it";
            $mailFROM = "info@retedes.it";


            //manda mail di carico credito
            $oggetto = "[reteDES.it] Apertura ordine #".$ordine." (".$descrizione.")";
            $profile = new Template('../email_rd4/apertura_ordine.html');

            $profile->set("referente_ordine", $O->fullname_referente );
            $profile->set("gas_referente_ordine", $O->descrizione_gas_referente );
            $profile->set("ditta", $O->descrizione_ditte );
            $profile->set("id_ditta", $O->id_ditte);
            $profile->set("id_ordine", $ordine );
            $profile->set("saluto", "Ciao" );
            $profile->set("data_chiusura", $O->data_chiusura_lunga );
            $profile->set("descrizione_ordine", $descrizione );
            $profile->set("note", $note );

            $messaggio = $profile->output();

            SEmailMulti($r,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"AperturaOrdine");
            //if($n==9){
                //sEmail("Mauro Morello","famiglia.morello@gmail.com","Topo Gigio","elisa.boldi@gmail.com",$oggetto,$messaggio,"AperturaOrdine");
            //}
            //MAIL------------------------------------------------

        unset ($O);
        }// END WHILE




    }else{
        $loggone .= "Nessun ordine da aprire <br>";
    }

    // poi esegue l'aggiornamento
    $loggone .= "Fuori da tutto <br>";
    //echo $loggone;
    //print_r($r);
    //$query = "
   // ";
    $stmt = $db->prepare("UPDATE  `retegas_ordini`
    SET  `id_stato` =  '2'
    WHERE  `retegas_ordini`.`data_apertura` <= now()
    AND `retegas_ordini`.`data_chiusura` > now()");

    $stmt->execute();
    if($stmt->rowCount()>0){
        $l=$stmt->rowCount()." righe interessate.<br>";
        sEmail("Mauro Morello","famiglia.morello@gmail.com","Topo Gigio","famiglia.morello@gmail.com","Aggiornamento ordini nuovo",$loggone,"LOG");
    }else{
        $l="Nulla da fare";
    }

    echo $l;