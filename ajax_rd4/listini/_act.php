<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.listino.php");
require_once("../../lib_rd4/class.rd4.gas.php");

require_once("../../lib_rd4/htmlpurifier-4.7.0/library/HTMLPurifier.auto.php");


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
        l_l_i($id_listino,"Esportazione CSV");

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
        l_l_i($id_listino,"Esportazione XLS");

        die();
        break;

    }
}
if(!empty($_POST["act"])){
    switch ($_POST["act"]) {

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
        
    case "note_listino":
        if (!posso_gestire_listino($_POST['pk'])){
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



         $stmt = $db->prepare("UPDATE retegas_listini SET note_listino = :note_listino
                             WHERE id_listini=:id_listini LIMIT 1;");

         $stmt->bindParam(':note_listino', $note , PDO::PARAM_STR);
         $stmt->bindParam(':id_listini', $_POST['pk'], PDO::PARAM_INT);

         $stmt->execute();
        if($stmt->rowCount()<>1){
            $res=array("result"=>"KO", "msg"=>"Errore." );
        }else{
            $res=array("result"=>"OK", "msg"=>"OK" );
        }
        echo json_encode($res);
        l_l_i($_POST['pk'],"Set note listino: $note");
     break;


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
        l_l_i($_POST['pk'],"Gestore extra aggiunto: $id_user");
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
        l_l_i($_POST['pk'],"Gestore extra tolto: $id_user");
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

        l_l_i($_POST["id"],"Export HTML");
        die();
        break;

    case "aggiungi_listino":

    if(trim($_POST["value"])==""){
        echo json_encode(array("result"=>"KO", "msg"=>"Non puoi lasciare questo campo vuoto"));
        die();
    }

    if(_USER_PERMISSIONS & perm::puo_creare_listini){}else{
        echo json_encode(array("result"=>"KO", "msg"=>"Non puoi creare listini :("));
        die();
    }

    if(CAST_TO_INT($_POST["id"],0)>0){
        $is_multiditta=0;
    }else{
        $is_multiditta=1;
    }

    //esiste
    $stmt = $db->prepare("INSERT INTO retegas_listini (id_utenti,descrizione_listini, id_ditte, tipo_listino, is_multiditta, data_creazione) VALUES ('"._USER_ID."',:nome,:id_ditta,0,:is_multiditta, NOW()) ;");
    $stmt->bindParam(':nome', $_POST['value'], PDO::PARAM_STR);
    $stmt->bindParam(':id_ditta', $_POST['id'], PDO::PARAM_INT);
    $stmt->bindParam(':is_multiditta', $is_multiditta, PDO::PARAM_INT);
    $stmt->execute();

    $id = $db->lastInsertId();

    if($stmt->rowCount()==1){
        $res=array("result"=>"OK", "msg"=>"Listino aggiunto", "id"=>$id );
    }else{
        $res=array("result"=>"KO", "msg"=>"Errore nel db." );
    }

    echo json_encode($res);
    l_l_n($id,"Creazione listino ".$_POST['value']);

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

        $oggetto = "[reteDES] richiesta di condivisione listino da ".$fullnameFROM;
        $profile = new Template('../../email_rd4/referente_extra_listino.html');

        $profile->set("FULLNAME", $fullnameTO );
        $profile->set("FULLNAME_EXTRA", $fullnameFROM );
        $profile->set("GAS_EXTRA", _USER_GAS_NOME );
        $profile->set("MESSAGGIO", $value );
        $profile->set("DESCRIZIONE_LISTINO", $L->descrizione_listini );
        $profile->set("DESCRIZIONE_DITTA", $L->descrizione_ditte );
        $profile->set("URL_LISTINO", APP_URL.'/?#ajax_rd4/listini/listino.php?id='.$L->id_listini );
        $profile->set("URL_FORNITORE", APP_URL.'/?#ajax_rd4/fornitori/scheda.php?id='.$L->id_ditte );


        $messaggio = $profile->output();

        if(SEmail($fullnameTO,$mailTO,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"CondivisioneListino")){
            $res=array("result"=>"OK", "msg"=>"Richiesta consegnata." );

        }else{
            $res=array("result"=>"KO", "msg"=>"Utente inserito, ma mail non inviata perchè indirizzo utente non valido" );

        }

    }else{
        $res=array("result"=>"KO", "msg"=>"Errore nel db." );
    }
    l_l_i($id,"Offerta gestore listino extra");
    echo json_encode($res);


    break;
    case "aggiungi_multiditta":
        //Controllare se posso manovrare il listino multiditta
        $id_listino = CAST_TO_INT($_POST["id_listino"],0);
            if(!posso_gestire_listino($id_listino)){
               $res=array("result"=>"KO", "msg"=>"Listino non tuo." );
                echo json_encode($res);
                die();
        }
        //Per ogni articolo passato
        $articoli=CAST_TO_ARRAY($_POST["selectedIDs"]);
        foreach($articoli as $id_articolo){
            //Prendere l'id_ditta e la descrizione ditta originale
            $stmt = $db->prepare("SELECT A.codice, D.id_ditte, D.descrizione_ditte FROM retegas_articoli A inner join retegas_listini L on L.id_listini=A.id_listini LEFT JOIN retegas_ditte D on D.id_ditte=L.id_ditte WHERE A.id_articoli=:id_articoli;");
            $stmt->bindParam(':id_articoli', $id_articolo, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $id_ditte=CAST_TO_INT($row["id_ditte"],0);
            $descrizione_ditte=$row["descrizione_ditte"];
            $codice=$row["codice"];

            //verificare se nel listino multiditta non vi sia lo stesso codice
            $stmt = $db->prepare("SELECT count(*) as conto FROM retegas_articoli A WHERE A.codice=:codice AND id_listini=:id_listini;");
            $stmt->bindParam(':id_listini', $id_listino, PDO::PARAM_INT);
            $stmt->bindParam(':codice', $codice, PDO::PARAM_STR);
            $stmt->execute();
            $rowC= $stmt->fetch(PDO::FETCH_ASSOC);
            if($rowC["conto"]>0){
                //nel casoaggiungere un random
                if(CAST_TO_INT($_POST["skip_doppi"])>0){
                    $log .= "Art. $codice saltato perchè già presente.<br>";
                    $skip = true;
                }else{
                    $codice = $codice."-".rand(50000,500000);
                    $skip = false;
                }
            }

            if(!$skip){
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
                is_disabled,
                id_ditta,
                descrizione_ditta
                )
                 SELECT
                :id_listino ,
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
                is_disabled,
                :id_ditta,
                :descrizione_ditta
                FROM retegas_articoli
                WHERE id_articoli = :id_articolo;";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':id_articolo', $id_articolo, PDO::PARAM_INT);
                $stmt->bindParam(':id_listino', $id_listino, PDO::PARAM_INT);
                $stmt->bindParam(':id_ditta', $id_ditte, PDO::PARAM_INT);
                $stmt->bindParam(':codice', $codice, PDO::PARAM_STR);
                $stmt->bindParam(':descrizione_ditta', $descrizione_ditte, PDO::PARAM_STR);
                $stmt->execute();
                $n++;
                }
            //Copiarlo con il codice listino del multiditta, l'id ditta e la descrizione

        }

        $res=array("result"=>"OK", "msg"=>"Aggiunti ".$n." articoli nel listino ".$id_listino.'<br>'.$log  );
        echo json_encode($res);
        l_l_n($id_listino,"Aggiunti $n articoli multiditta ");
        die();
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
                `is_disabled`,
                id_ditta,
                descrizione_ditta
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
                `articoli_opz_3` ,
                `is_disabled`,
                id_ditta,
                descrizione_ditta
                FROM retegas_articoli
                WHERE id_articoli = :id_articolo;";



                $stmt = $db->prepare($sql);
                $stmt->bindParam(':id_articolo', $id, PDO::PARAM_INT);
                $stmt->bindParam(':codice', $codice, PDO::PARAM_STR);
                $stmt->execute();

                $res=array("result"=>"OK", "msg"=>"Articolo clonato." );
                echo json_encode($res);

                //NON SO IL LISTINO
                //l_l_n($id,"Articolo clonato: $codice");
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
                        $stmt = $db->prepare("UPDATE retegas_listini SET is_deleted=1 WHERE id_listini=:id_listini LIMIT 1;");
                        $stmt->bindParam(':id_listini', $id, PDO::PARAM_INT);
                        $stmt->execute();
                        $res=array("result"=>"OK", "msg"=>"Listino nascosto." );
                        echo json_encode($res);
                        l_l_n($id,"Listino nascosto;");
                        die();
                    }else{
                        $stmt = $db->prepare("DELETE FROM retegas_listini WHERE id_listini=:id_listini LIMIT 1;");
                        $stmt->bindParam(':id_listini', $id, PDO::PARAM_INT);
                        $stmt->execute();
                        $res=array("result"=>"OK", "msg"=>"Listino eliminato." );
                        echo json_encode($res);
                        l_l_n($id,"Listino eliminato;");
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
                l_l_n($id,"Tutti gli articoli eliminati;");
                die();

            }

        }else{
            $res=array("result"=>"KO", "msg"=>"Listino non trovato" );
            echo json_encode($res);
            die();
        }

        break;

        case "varia_percentuale":

            $id = CAST_TO_INT($_POST["id_listino"]);
            if(!posso_gestire_listino($id)){
               $res=array("result"=>"KO", "msg"=>"Listino non tuo." );
                echo json_encode($res);
                die();
            }

            $valore_percentuale=CAST_TO_FLOAT($_POST["valore_percentuale"]);

            if($valore_percentuale<>0){
                $valore_percentuale = (100+$valore_percentuale)/100;
            }else{
                $res=array("result"=>"KO", "msg"=>"La percentuale deve essere inferiore o superiore a zero: ");
                echo json_encode($res);
                die();
            }

            //passo gli id
            $articoli=CAST_TO_ARRAY($_POST["selectedIDs"]);
            foreach($articoli as $id_articolo){
                $stmt = $db->prepare("UPDATE retegas_articoli SET
                                                            prezzo = prezzo*:variazione
                                                            WHERE id_articoli = :id_articoli LIMIT 1;");
                $stmt->bindParam(':id_articoli', $id_articolo, PDO::PARAM_INT);
                $stmt->bindParam(':variazione', $valore_percentuale, PDO::PARAM_STR);
                $stmt->execute();

            }




            $res=array("result"=>"OK", "msg"=>"Variazione effettuata: ");
            echo json_encode($res);
            l_l_n($id,"Variato prezzo articoli del $valore_percentuale %;");
            die();

        break;

        case "varia_assoluto":

            $id = CAST_TO_INT($_POST["id_listino"]);
            if(!posso_gestire_listino($id)){
               $res=array("result"=>"KO", "msg"=>"Listino non tuo." );
                echo json_encode($res);
                die();
            }

            $valore_assoluto=CAST_TO_FLOAT($_POST["valore_assoluto"]);

            if($valore_assoluto<>0){

            }else{
                $res=array("result"=>"KO", "msg"=>"Il valore deve essere inferiore o superiore a zero" );
                echo json_encode($res);
                die();
            }

            //passo gli id
            $articoli=CAST_TO_ARRAY($_POST["selectedIDs"]);
            foreach($articoli as $id_articolo){
                $stmt = $db->prepare("UPDATE retegas_articoli SET
                                                            prezzo =  IF((prezzo+:variazione)>0,prezzo+:variazione,0)
                                                            WHERE id_articoli = :id_articoli LIMIT 1;");
                $stmt->bindParam(':id_articoli', $id_articolo, PDO::PARAM_INT);
                $stmt->bindParam(':variazione', $valore_assoluto, PDO::PARAM_STR);
                $stmt->execute();

            }




            $res=array("result"=>"OK", "msg"=>"Variazione effettuata");
            echo json_encode($res);
            l_l_n($id,"Variato prezzo articoli di $valore_assoluto ;");
            die();

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
        //print_r($rowL); die();
        /*
        Array
        (
            [id_listini] => 10220
            [descrizione_listini] => Per Lorena
            [id_utenti] => 2
            [id_tipologie] => 3
            [id_ditte] => 135
            [data_valido] => 2018-12-31
            [tipo_listino] => 0
            [is_privato] => 0
            [opz_usage] => 0
            [is_multiditta] => 0
            [note_listino] => 
            [data_creazione] => 2018-01-16 23:37:18
            [is_deleted] => 0
        )

        */
        
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
                                                                    opz_usage,
                                                                    is_multiditta,
                                                                    note_listino,
                                                                    data_creazione,
                                                                    is_deleted
                                                                    ) VALUES (
                                                                    :descrizione_listini,
                                                                    '"._USER_ID."',
                                                                    '".$rowL["id_tipologie"]."',
                                                                    '".$rowL["id_ditte"]."',
                                                                    '".$rowL["data_valido"]."',
                                                                    '".$rowL["tipo_listino"]."',
                                                                    '".$rowL["is_privato"]."',
                                                                    '".$rowL["opz_usage"]."',
                                                                    '".$rowL["is_multiditta"]."',
                                                                    '".$rowL["note_listino"]."',
                                                                    NOW(),
                                                                    0
                                                                    );");
                $stmt->bindParam(':descrizione_listini', $descrizione_listini, PDO::PARAM_STR);
                $stmt->execute();
                //emetto il nuovo ID
                $newid= $db->lastInsertId();
                if($newid==0){
                    $res=array("result"=>"KO", "msg"=>"Errore antipatico" );
                    echo json_encode($res);
                    die();    
                }
                
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
                                                  is_disabled,
                                                  id_ditta,
                                                  descrizione_ditta)
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
                                          is_disabled,
                                          id_ditta,
                                          descrizione_ditta
                                FROM
                                        retegas_articoli WHERE id_listini = '".$id."';";
                $stmt = $db->prepare($query_copia);
                $stmt->execute();

                $res=array("result"=>"OK", "msg"=>"Listino clonato, attendi il reindirizzamento...", "id" => $newid );
                echo json_encode($res);
                l_l_n($id,"Listino clonato, nuovo listino: $newid");
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
        l_l_n($_POST['pk'],"Descrizione listino: $nuovo");
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
        l_l_n($_POST['pk'],"Data validità: $nuovo");
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
        l_l_n($_POST['pk'],"Tipo listino: $tipo_listino");
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
        l_l_n($_POST['pk'],"Listino privato: $is_privato");
        echo json_encode($res);
     break;
     case "is_multiditta":
        //esiste
        $is_multiditta = CAST_TO_INT($_POST['value'],0,1);
        if(!posso_gestire_listino($_POST['pk'])){$res=array("result"=>"KO", "msg"=>"Non posso gestire questo listino" );echo json_encode($res);die();}


        $stmt = $db->prepare("UPDATE retegas_listini SET is_multiditta= :is_multiditta, id_ditte=0
                             WHERE id_listini=:id_listini");
        $stmt->bindParam(':id_listini', $_POST['pk'], PDO::PARAM_INT);
        $stmt->bindParam(':is_multiditta', $is_multiditta, PDO::PARAM_INT);
        $stmt->execute();
        if($stmt->rowCount()==1){;
            $res=array("result"=>"OK", "msg"=>"OK..." );
        }else{
            $res=array("result"=>"KO", "msg"=>"Errore" );
        }
        l_l_n($_POST['pk'],"is multiditta: $is_multiditta");
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
        l_l_n($_POST['pk'],"Tipologia: $id_tipologia");
        echo json_encode($res);
     break;
     default :
    $res=array("result"=>"KO", "msg"=>"Comando '".$_POST["name"]."' non riconosciuto" );
    echo json_encode($res);
    break;
    }
}