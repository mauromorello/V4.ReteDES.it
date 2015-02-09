<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.listino.php");

if(!empty($_GET["act"])){
    switch ($_GET["act"]) {
        case "exp_csv":

        $id_listino=CAST_TO_INT($_GET["id"]);
        $exporter = new ExportDataCSV('browser', 'listino_RD4_'.$id_listino.'.csv');
        $exporter->initialize();
        $exporter->addRow(array("codice", "descrizione", "prezzo", "u.misura","misura","note brevi","q.scatola","q.multiplo","note lunghe","univoco (0/1)","tag 1","tag 2","tag3","disabilitato"));

        $stmt = $db->prepare("SELECT * from retegas_articoli WHERE id_listini=:id_listini");
        $stmt->bindParam(':id_listini', $id_listino, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();
        foreach($rows as $row){
            $exporter->addRow(array(html_entity_decode($row["codice"]),html_entity_decode($row[descrizione_articoli]),$row[prezzo],$row[u_misura],$row[misura],html_entity_decode($row[ingombro]),$row[qta_scatola],$row[qta_minima],html_entity_decode($row[articoli_note]),$row[articoli_unico],html_entity_decode($row[articoli_opz_1]),html_entity_decode($row[articoli_opz_2]),html_entity_decode($row[articoli_opz_3]), $row[is_disabled]));
        }
        $exporter->finalize();
        die();


        break;
        case "exp_xls":
       // error_reporting(E_ALL);
        $id_listino=CAST_TO_INT($_GET["id"]);
        if($_GET["t"]=="XLS"){
            $tipo="Excel5";
            $exte="XLS";
        }else{
            $tipo="Excel2007";
            $exte="XLSX";
        }

        include("../../lib_rd4/PHPExcel.php");
        include '../../lib_rd4/PHPExcel/Writer/Excel5.php';

        $objPHPExcel = new PHPExcel();

        $objPHPExcel->getProperties()->setCreator(_USER_FULLNAME);
        $objPHPExcel->getProperties()->setLastModifiedBy(_USER_FULLNAME);
        $objPHPExcel->getProperties()->setTitle("Listino ". $id_listino );
        $objPHPExcel->getProperties()->setSubject("Listino ". $id_listino);
        $objPHPExcel->getProperties()->setDescription("Listino ". $id_listino);

        $objPHPExcel->setActiveSheetIndex(0);

        $stmt = $db->prepare("SELECT * from retegas_articoli WHERE id_listini=:id_listini");
        $stmt->bindParam(':id_listini', $id_listino, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Codice');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Descrizione');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Prezzo');
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'U.misura');
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Misura');
        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Note brevi');
        $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Qta scatola');
        $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Qta multiplo');
        $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Note');
        $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Univoco (0/1)');
        $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Tag 1');
        $objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Tag 2');
        $objPHPExcel->getActiveSheet()->SetCellValue('M1', 'Tag 3');
        $objPHPExcel->getActiveSheet()->SetCellValue('N1', 'Disabilitato');

        $i=1;
        foreach($rows as $row){
            //$exporter->addRow(array($row["codice"],$row[descrizione_articoli],$row[prezzo],$row[u_misura],
            //$row[misura],$row[qta_scatola],$row[qta_minima],$row[articoli_unico],$row[ingombro],$row[articoli_opz_1],$row[articoli_opz_2],$row[articoli_opz_3],$row[articoli_note]));
            $i=$i+1;

            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, html_entity_decode(($row[codice])));
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$i, html_entity_decode(($row[descrizione_articoli])));
            $objPHPExcel->getActiveSheet()->SetCellValue('C'.$i, $row[prezzo]);
            $objPHPExcel->getActiveSheet()->SetCellValue('D'.$i, html_entity_decode(($row[u_misura])));
            $objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, $row[misura]);
            $objPHPExcel->getActiveSheet()->SetCellValue('F'.$i, html_entity_decode(($row[ingombro])));
            $objPHPExcel->getActiveSheet()->SetCellValue('G'.$i, $row[qta_scatola]);
            $objPHPExcel->getActiveSheet()->SetCellValue('H'.$i, $row[qta_minima]);
            $objPHPExcel->getActiveSheet()->SetCellValue('I'.$i, html_entity_decode(($row[articoli_note])));
            $objPHPExcel->getActiveSheet()->SetCellValue('J'.$i, $row[articoli_unico]);
            $objPHPExcel->getActiveSheet()->SetCellValue('K'.$i, html_entity_decode(($row[articoli_opz_1])));
            $objPHPExcel->getActiveSheet()->SetCellValue('L'.$i, html_entity_decode(($row[articoli_opz_2])));
            $objPHPExcel->getActiveSheet()->SetCellValue('M'.$i, html_entity_decode(($row[articoli_opz_3])));
            $objPHPExcel->getActiveSheet()->SetCellValue('N'.$i, $row[is_disabled]);

        }

        $objPHPExcel->getActiveSheet()->setTitle("Listino ". $id_listino);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="listino_RD4_'.$id_listino.'.'.$exte.'"');
        header('Cache-Control: max-age=0');
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $tipo);
        //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        //$objWriter->save('php://output');

        $filePath = "../../public_rd4/listini/AAA_". rand(0, getrandmax()) . rand(0, getrandmax()) . ".tmp";
        $objWriter->save($filePath);
        readfile($filePath);
        unlink($filePath);


        die();
        break;

    }
}
if(!empty($_POST["act"])){
    switch ($_POST["act"]) {

    case "attiva_aiuta_a_gestire":
        $id_listini=CAST_TO_INT($_POST["id_listini"]);
        $L = new listino($id_listini);

        if($L->id_utenti<>_USER_ID){
            echo json_encode(array("result"=>"KO", "msg"=>"Non sei il proprietario del listino"));
            die();
        }

        $id_user = CAST_TO_INT($_POST["id_user"]);

        $stmt = $db->prepare("UPDATE retegas_options set valore_int=1 WHERE chiave='_AIUTO_GESTIONE_LISTINO' AND id_user=:id_user AND id_listino=:id_listino LIMIT 1;");
        $stmt->bindParam(':id_listino', $id_listini, PDO::PARAM_INT);
        $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        $stmt->execute();
        if($stmt->rowCount()==1){
            $res=array("result"=>"OK", "msg"=>"Gestore aggiunto; Ricarica la pagina per vederlo nella lista giusta ;)" );
        }else{
            $res=array("result"=>"KO", "msg"=>"Errore nel db." );
        }

        echo json_encode($res);
    break;
    case "elimina_aiuta_a_gestire":
        $id_listini=CAST_TO_INT($_POST["id_listini"]);
        $L = new listino($id_listini);

        if($L->id_utenti<>_USER_ID){
            echo json_encode(array("result"=>"KO", "msg"=>"Non sei il proprietario del listino"));
            die();
        }

        $id_user = CAST_TO_INT($_POST["id_user"]);

        $stmt = $db->prepare("DELETE FROM retegas_options WHERE chiave='_AIUTO_GESTIONE_LISTINO' AND id_user=:id_user AND id_listino=:id_listino LIMIT 1;");
        $stmt->bindParam(':id_listino', $id_listini, PDO::PARAM_INT);
        $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        $stmt->execute();
        if($stmt->rowCount()==1){
            $res=array("result"=>"OK", "msg"=>"Richiesta eliminata." );
        }else{
            $res=array("result"=>"KO", "msg"=>"Errore nel db." );
        }

        echo json_encode($res);
    break;
    case "exp_htm":

        $id_listino=CAST_TO_INT($_POST["id"]);

        $stmt = $db->prepare("SELECT * from retegas_articoli WHERE id_listini=:id_listini");
        $stmt->bindParam(':id_listini', $id_listino, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();
        $t  = '<html><table>
        ';
  $t .= '<tr>
            <th>Codice</th>
            <th>Descrizione</th>
            <th>Prezzo</th>
            <th>U.Misura</th>
            <th>Misura</th>
            <th>Note brevi</th>
            <th>Qta Scatola</th>
            <th>Minimo Multiplo</th>
            <th>Note</th>
            <th>UNIVOCO (1/0)</th>
            <th>TAG 1</th>
            <th>TAG 2</th>
            <th>TAG 3</th>
            <th>1=Disabilitato</th>
         </tr>';

        foreach($rows as $row){
            //$exporter->addRow(array(clean($row["codice"]),clean($row[descrizione_articoli]),$row[prezzo],$row[u_misura],$row[misura],clean($row[ingombro]),$row[qta_scatola],$row[qta_minima],clean($row[articoli_note]),$row[articoli_unico],clean($row[articoli_opz_1]),clean($row[articoli_opz_2]),clean($row[articoli_opz_3])));
            $t .= '<tr>
                    <td>'.clean($row["codice"]).'</td>
                    <td>'.clean($row["descrizione_articoli"]).'</td>
                    <td>'.$row["prezzo"].'</td>
                    <td>'.$row["u_misura"].'</td>
                    <td>'.$row["misura"].'</td>
                    <td>'.clean($row["ingombro"]).'</td>
                    <td>'.$row["qta_scatola"].'</td>
                    <td>'.$row["qta_minima"].'</td>
                    <td>'.clean($row["articoli_note"]).'</td>
                    <td>'.$row["articoli_unico"].'</td>
                    <td>'.clean($row["articoli_opz_1"]).'</td>
                    <td>'.clean($row["articoli_opz_2"]).'</td>
                    <td>'.clean($row["articoli_opz_3"]).'</td>
                    <td>'.$row["is_disabled"].'</td>
                 </tr>';

        }
        $t .= '</table></html>';
        echo $t;
        die();
        break;

    case "aggiungi_listino":

    if($_POST["value"]==""){
        echo json_encode(array("result"=>"KO", "msg"=>"Non puoi lasciare questo campo vuoto"));
        die();
    }

    if(_USER_PERMISSIONS & perm::puo_creare_listini){}else{
        echo json_encode(array("result"=>"KO", "msg"=>"Non puoi creare listini :("));
        die();
    }

    //esiste
    $stmt = $db->prepare("INSERT INTO retegas_listini (id_utenti,descrizione_listini, id_ditte, tipo_listino) VALUES ('"._USER_ID."',:nome,:id_ditta,0) ;");
    $stmt->bindParam(':nome', $_POST['value'], PDO::PARAM_STR);
    $stmt->bindParam(':id_ditta', $_POST['id'], PDO::PARAM_INT);
    $stmt->execute();

    $id = $db->lastInsertId();

    if($stmt->rowCount()==1){
        $res=array("result"=>"OK", "msg"=>"Listino aggiunto", "id"=>$id );
    }else{
        $res=array("result"=>"KO", "msg"=>"Errore nel db." );
    }

    echo json_encode($res);

    break;
    //-------------------------------------------------------------------------------------
    //-----------------AIUTA A GESTIRE ----------------------------------------------------------

    case "aiuta_a_gestire":
    $id =CAST_TO_INT($_POST["id"],0);
    $value = CAST_TO_STRING($_POST["value"]);


    $L=new listino($id);

    //delete in options
    $sql = "DELETE FROM retegas_options WHERE id_user='"._USER_ID."' AND chiave='_AIUTO_GESTIONE_LISTINO' AND id_listino=:id_listino;";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id_listino', $L->id_listini, PDO::PARAM_INT);
    $stmt->execute();

    //insert in option
    $sql = "INSERT INTO retegas_options (id_user,chiave,valore_text,valore_int, id_listino) VALUES ('"._USER_ID."','_AIUTO_GESTIONE_LISTINO',:value,0,:id_listino);";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':value', $_POST['value'], PDO::PARAM_STR);
    $stmt->bindParam(':id_listino', $L->id_listini, PDO::PARAM_INT);
    $stmt->execute();

    //mail al referente listino
    if($stmt->rowCount()==1){
        $mailFROM = _USER_MAIL;
        $fullnameFROM = _USER_FULLNAME;

        $mailTO = $L->email_proprietario_listino;
        $fullnameTO = $L->fullmane_proprietario_listino;

        $oggetto = "[reteDES.it] richiesta di condivisione listino da ".$fullnameFROM;
        $profile = new Template('../../email_rd4/referente_extra_listino.html');

        $profile->set("FULLNAME", $fullnameTO );
        $profile->set("FULLNAME_EXTRA", $fullnameFROM );
        $profile->set("GAS_EXTRA", _USER_GAS_NOME );
        $profile->set("MESSAGGIO", $value );
        $profile->set("DESCRIZIONE_LISTINO", $L->descrizione_listini );
        $profile->set("DESCRIZIONE_DITTA", $L->descrizione_ditte );
        $profile->set("URL_LISTINO", 'http://retegas.altervista.org/gas4/?#ajax_rd4/listini/listino.php?id='.$L->id_listini );
        $profile->set("URL_FORNITORE", 'http://retegas.altervista.org/gas4/?#ajax_rd4/fornitori/scheda.php?id='.$L->id_ditte );


        $messaggio = $profile->output();

        if(SEmail($fullnameTO,$mailTO,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"CondivisioneListino")){
            $res=array("result"=>"OK", "msg"=>"Richiesta consegnata." );

        }else{
            $res=array("result"=>"KO", "msg"=>"Utente inserito, ma mail non inviata." );

        }

    }else{
        $res=array("result"=>"KO", "msg"=>"Errore nel db." );
    }

    echo json_encode($res);


    break;
        //-----------------CLONA ARTICOLO ----------------------------------------------------------

        case "clona_articolo":


            $id =CAST_TO_INT($_POST["id"],0);


            $sql="SELECT * FROM retegas_articoli WHERE id_articoli=:id_articolo";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_articolo', $id, PDO::PARAM_INT);
            $stmt->execute();
            $rowA = $stmt->fetch(PDO::FETCH_ASSOC);
            $codice = $rowA["codice"]."-".rand(50000,500000);

            $sql="INSERT INTO  `my_retegas`.`retegas_articoli` (

                `id_listini` ,
                `codice` ,
                `u_misura` ,
                `misura` ,
                `descrizione_articoli` ,
                `qta_scatola` ,
                `prezzo` ,
                `ingombro` ,
                `qta_minima` ,
                `qta_multiplo` ,
                `articoli_note` ,
                `articoli_unico` ,
                `articoli_opz_1` ,
                `articoli_opz_2` ,
                `articoli_opz_3` ,
                is_disabled
                )
                 SELECT
                `id_listini` ,
                :codice,
                `u_misura` ,
                `misura` ,
                `descrizione_articoli` ,
                `qta_scatola` ,
                `prezzo` ,
                `ingombro` ,
                `qta_minima` ,
                `qta_multiplo` ,
                `articoli_note` ,
                `articoli_unico` ,
                `articoli_opz_1` ,
                `articoli_opz_2` ,
                `articoli_opz_3`,
                is_disabled
                FROM retegas_articoli
                WHERE id_articoli = :id_articolo;";



                $stmt = $db->prepare($sql);
                $stmt->bindParam(':id_articolo', $id, PDO::PARAM_INT);
                $stmt->bindParam(':codice', $codice, PDO::PARAM_STR);
                $stmt->execute();

                $res=array("result"=>"OK", "msg"=>"Articolo clonato." );
                echo json_encode($res);
                die();

        break;

        case "del_listino":
        $id =CAST_TO_INT($_POST["id"],0);

        $stmt = $db->prepare("SELECT * FROM retegas_listini WHERE id_listini=:id_listini");
        $stmt->bindParam(':id_listini', $id, PDO::PARAM_INT);
        $stmt->execute();
        $rowL = $stmt->fetch(PDO::FETCH_ASSOC);

        if($stmt->rowCount()==1){
            //controllo se è un listino privato.
            if(!posso_gestire_listino($id)){
                $res=array("result"=>"KO", "msg"=>"Listino non tuo." );
                echo json_encode($res);
                die();
            }else{
                //Controllo che non abbia articoli o che non sia stato usato
                $stmt = $db->prepare("SELECT * FROM retegas_articoli WHERE id_listini=:id_listini");
                $stmt->bindParam(':id_listini', $id, PDO::PARAM_INT);
                $stmt->execute();

                if($stmt->rowCount()>0){
                    $res=array("result"=>"KO", "msg"=>"Ci sono ancora articoli caricati." );
                    echo json_encode($res);
                    die();
                }else{
                    $stmt = $db->prepare("SELECT * FROM retegas_ordini WHERE id_listini=:id_listini");
                    $stmt->bindParam(':id_listini', $id, PDO::PARAM_INT);
                    $stmt->execute();
                    if($stmt->rowCount()>0){
                        $res=array("result"=>"KO", "msg"=>"Ci sono ordini con questo listino." );
                        echo json_encode($res);
                        die();
                    }else{
                        $stmt = $db->prepare("DELETE FROM retegas_listini WHERE id_listini=:id_listini LIMIT 1;");
                        $stmt->bindParam(':id_listini', $id, PDO::PARAM_INT);
                        $stmt->execute();
                        $res=array("result"=>"OK", "msg"=>"Listino eliminato." );
                        echo json_encode($res);
                        die();
                    }

                }



            }

        }else{
            $res=array("result"=>"KO", "msg"=>"Listino non trovato" );
            echo json_encode($res);
            die();
        }

        break;

        case "del_articoli":
        $id =CAST_TO_INT($_POST["id"],0);

        $stmt = $db->prepare("SELECT * FROM retegas_listini WHERE id_listini=:id_listini");
        $stmt->bindParam(':id_listini', $id, PDO::PARAM_INT);
        $stmt->execute();
        $rowL = $stmt->fetch(PDO::FETCH_ASSOC);

        if($stmt->rowCount()==1){;
            //controllo se è un listino privato.
            if(!posso_gestire_listino($id)){
                $res=array("result"=>"KO", "msg"=>"Listino non tuo." );
                echo json_encode($res);
                die();
            }else{
                $stmt = $db->prepare("DELETE FROM retegas_articoli WHERE id_listini=:id_listini;");
                $stmt->bindParam(':id_listini', $id, PDO::PARAM_INT);
                $stmt->execute();
                $res=array("result"=>"OK", "msg"=>"Articoli cancellati." );
                echo json_encode($res);
                die();

            }

        }else{
            $res=array("result"=>"KO", "msg"=>"Listino non trovato" );
            echo json_encode($res);
            die();
        }

        break;
        case "clona_listino":
        //controllo ID
        $id =CAST_TO_INT($_POST["id"],0);

        //controllo nome
        $descrizione_listini = CAST_TO_STRING(clean($_POST["value"]));
        if($descrizione_listini==""){
            $res=array("result"=>"KO", "msg"=>"Devi assegnare un nome al nuovo listino clonato." );
                echo json_encode($res);
                die();
        }

        $stmt = $db->prepare("SELECT * FROM retegas_listini WHERE id_listini=:id_listini");
        $stmt->bindParam(':id_listini', $id, PDO::PARAM_INT);
        $stmt->execute();
        $rowL = $stmt->fetch(PDO::FETCH_ASSOC);

        if($stmt->rowCount()==1){;
            //controllo se è un listino privato.
            if(($rowL["is_privato"]==1) AND ($rowL["id_utenti"]<>_USER_ID)){
                $res=array("result"=>"KO", "msg"=>"Un listino privato può essere clonato solo dal suo proprietario" );
                echo json_encode($res);
                die();
            }else{
                //Clono l'intestazione
                $stmt = $db->prepare("INSERT INTO retegas_listini ( descrizione_listini,
                                                                    id_utenti,
                                                                    id_tipologie,
                                                                    id_ditte,
                                                                    data_valido,
                                                                    tipo_listino,
                                                                    is_privato,
                                                                    opz_usage
                                                                    ) VALUES (
                                                                    :descrizione_listini,
                                                                    '"._USER_ID."',
                                                                    '".$rowL["id_tipologie"]."',
                                                                    '".$rowL["id_ditte"]."',
                                                                    '".$rowL["data_valido"]."',
                                                                    '".$rowL["tipo_listino"]."',
                                                                    '".$rowL["is_privato"]."',
                                                                    '".$rowL["opz_usage"]."'
                                                                    );");
                $stmt->bindParam(':descrizione_listini', $descrizione_listini, PDO::PARAM_STR);
                $stmt->execute();
                //emetto il nuovo ID
                $newid= $db->lastInsertId();
                //Clono gli articoli
                $query_copia="INSERT INTO
                                retegas_articoli (codice,
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
                                                  is_disabled)
                                SELECT
                                        codice,
                                        '$newid',
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
                                          is_disabled
                                FROM
                                        retegas_articoli WHERE id_listini = '".$id."';";
                $stmt = $db->prepare($query_copia);
                $stmt->execute();

                $res=array("result"=>"OK", "msg"=>"Listino clonato, attendi il reindirizzamento...", "id" => $newid );
                echo json_encode($res);
                die();

            }

        }else{
            $res=array("result"=>"KO", "msg"=>"Listino non trovato" );
            echo json_encode($res);
            die();
        }







        break;


        default :
        $res=array("result"=>"KO", "msg"=>"Comando '".$_POST["act"]."' non riconosciuto" );
        echo json_encode($res);
        break;

    }
}
if(!empty($_POST["name"])){
    switch ($_POST["name"]) {

     case "descrizione_listini":
        //esiste
        $nuovo = trim(strip_tags($_POST['value']));
        if(!posso_gestire_listino($_POST['pk'])){$res=array("result"=>"KO", "msg"=>"Non posso gestire questo listino" );echo json_encode($res);die();}

        if($nuovo<>""){}else{$res=array("result"=>"KO", "msg"=>"Non può essere vuoto" );echo json_encode($res);die();}

        $stmt = $db->prepare("UPDATE retegas_listini SET descrizione_listini= :descrizione_listini
                             WHERE id_listini=:id_listini");
        $stmt->bindParam(':id_listini', $_POST['pk'], PDO::PARAM_INT);
        $stmt->bindParam(':descrizione_listini', $nuovo, PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()==1){;
            $res=array("result"=>"OK", "msg"=>"Nuovo nome salvato." );
        }else{
            $res=array("result"=>"KO", "msg"=>"Errore nel DB..." );
        }

        echo json_encode($res);
     break;
     case "data_valido":
        //esiste
        $nuovo = trim(strip_tags($_POST['value']));
                if(!posso_gestire_listino($_POST['pk'])){$res=array("result"=>"KO", "msg"=>"Non posso gestire questo listino" );echo json_encode($res);die();}

        if($nuovo<>""){}else{$res=array("result"=>"KO", "msg"=>"Non può essere vuoto" );echo json_encode($res);die();}
        $data_valido = conv_date_to_db($nuovo);

        $stmt = $db->prepare("UPDATE retegas_listini SET data_valido= :data_valido
                             WHERE id_listini=:id_listini");
        $stmt->bindParam(':id_listini', $_POST['pk'], PDO::PARAM_INT);
        $stmt->bindParam(':data_valido', $data_valido, PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()==1){;
            $res=array("result"=>"OK", "msg"=>"Nuova scadenza salvata." );
        }else{
            $res=array("result"=>"KO", "msg"=>"Data non riconosciuta." );
        }

        echo json_encode($res);
     break;
     case "tipo_listino":
        //esiste
        $tipo_listino = CAST_TO_INT($_POST['value'],0,1);
        if(!posso_gestire_listino($_POST['pk'])){$res=array("result"=>"KO", "msg"=>"Non posso gestire questo listino" );echo json_encode($res);die();}


        $stmt = $db->prepare("UPDATE retegas_listini SET tipo_listino= :tipo_listino
                             WHERE id_listini=:id_listini");
        $stmt->bindParam(':id_listini', $_POST['pk'], PDO::PARAM_INT);
        $stmt->bindParam(':tipo_listino', $tipo_listino, PDO::PARAM_INT);
        $stmt->execute();
        if($stmt->rowCount()==1){;
            $res=array("result"=>"OK", "msg"=>"OK..." );
        }else{
            $res=array("result"=>"KO", "msg"=>"Errore" );
        }

        echo json_encode($res);
     break;
     case "is_privato":
        //esiste
        $is_privato = CAST_TO_INT($_POST['value'],0,1);
        if(!posso_gestire_listino($_POST['pk'])){$res=array("result"=>"KO", "msg"=>"Non posso gestire questo listino" );echo json_encode($res);die();}


        $stmt = $db->prepare("UPDATE retegas_listini SET is_privato= :is_privato
                             WHERE id_listini=:id_listini");
        $stmt->bindParam(':id_listini', $_POST['pk'], PDO::PARAM_INT);
        $stmt->bindParam(':is_privato', $is_privato, PDO::PARAM_INT);
        $stmt->execute();
        if($stmt->rowCount()==1){;
            $res=array("result"=>"OK", "msg"=>"OK..." );
        }else{
            $res=array("result"=>"KO", "msg"=>"Errore" );
        }

        echo json_encode($res);
     break;
     case "id_tipologie":
        //esiste
        $id_tipologia = CAST_TO_INT($_POST['value'],1,17);
        if(!posso_gestire_listino($_POST['pk'])){$res=array("result"=>"KO", "msg"=>"Non posso gestire questo listino" );echo json_encode($res);die();}


        $stmt = $db->prepare("UPDATE retegas_listini SET id_tipologie= :id_tipologia
                             WHERE id_listini=:id_listini");
        $stmt->bindParam(':id_listini', $_POST['pk'], PDO::PARAM_INT);
        $stmt->bindParam(':id_tipologia', $id_tipologia, PDO::PARAM_INT);
        $stmt->execute();
        if($stmt->rowCount()==1){;
            $res=array("result"=>"OK", "msg"=>"OK..." );
        }else{
            $res=array("result"=>"KO", "msg"=>"Errore" );
        }

        echo json_encode($res);
     break;
     default :
    $res=array("result"=>"KO", "msg"=>"Comando '".$_POST["name"]."' non riconosciuto" );
    echo json_encode($res);
    break;
    }
}