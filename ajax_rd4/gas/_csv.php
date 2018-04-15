<?php
  require_once("inc/init.php");


  if(CAST_TO_STRING($_POST["do"])=="o1_go"){

     /*
    - ID ordine
    - data ordine
    - Descrizione ordine
    - Id fornitore
    - Nome fornitore
    - ID gruppo GAS
    - Nome gruppo GAS
    - Codice articolo
    - Descrizione articolo
    - Quantità (arrivata: a noi interessa quella da fatturare)
    - Prezzo unitario
    - Unità di misura
     */
      $from = trim(CAST_TO_STRING($_POST["from"]));
      $to=trim(CAST_TO_STRING($_POST["to"]));

      if(CAST_TO_STRING($_POST["output"])=="vis"){$csv=false;}else{$csv=true;}

      if($from<>"" AND $to<>""){
          $from = conv_date_to_db($from);
          $to = conv_date_to_db($to);
          $w = " AND O.data_chiusura>=:from AND O.data_chiusura<=:to ";
      }else{
          $w = "";
      }

      $idutente = CAST_TO_INT($_POST["idutente"]);
      if($idutente>0){
            $w .= " AND D.id_utenti=:idutente ";
      }else{
            $w .= "";
      }

      $idordine = CAST_TO_INT($_POST["idordine"]);
      if($idordine>0){
            $w .= " AND O.id_ordini=:idordine ";
      }else{
            $w .= "";
      }

      $idreferente = CAST_TO_INT($_POST["idreferente"]);
      if($idreferente>0){
            $w .= " AND O.id_utente=:idreferente ";
      }else{
            $w .= "";
      }
      
      $idditta = CAST_TO_INT($_POST["idditta"]);
      if($idditta>0){
            $w .= " AND DI.id_ditte=:idditta ";
      }else{
            $w .= "";
      }

      if(CAST_TO_STRING($_POST["includi_extra"])<>"on"){
            $w .= " AND U.id_gas='"._USER_ID_GAS."' ";
      }else{

            $w .= "";
      }

      if($csv){
          header('Content-Type: text/csv; charset=utf-8');
          header('Content-Disposition: attachment; filename='.date('Ymdhis').'.csv');
          $output = fopen('php://output', 'w');
          fputcsv($output, array('ID ordine','Id referente', 'Data chiusura', 'Descrizione','id_utente','utente', 'id_ditta','ditta','id_gas','descrizione gas','codice','descrizione articolo','qta arr','prezzo','um'),_USER_CSV_SEPARATOR);
      }

          //DISTINCT
          $sql ="SELECT 
                    D.id_ordine,
                    O.id_utente as id_referente,
                    DATE_FORMAT(O.data_chiusura, '%d/%m/%Y') as data_chiusura,
                    O.descrizione_ordini,
                    D.id_utenti,
                    U.fullname,
                    DI.id_ditte,
                    DI.descrizione_ditte,
                    U.id_gas,
                    G.descrizione_gas,
                    D.art_codice,
                    D.art_desc,
                    D.qta_arr,
                    D.prz_dett_arr,
                    D.art_um
                    FROM
                    retegas_dettaglio_ordini AS D
                    inner join maaking_users U on U.userid=D.id_utenti
                    inner join retegas_ordini O on O.id_ordini = D.id_ordine
                    inner join retegas_gas G on G.id_gas = U.id_gas
                    inner join retegas_listini L on L.id_listini = O.id_listini
                    inner join retegas_ditte DI on DI.id_ditte=L.id_ditte
                    inner join retegas_referenze R on R.id_ordine_referenze=D.id_ordine
                    inner join maaking_users U2 on U2.userid=R.id_utente_referenze
                    WHERE
                    U2.id_gas = '"._USER_ID_GAS."'
                    ".$w."
                    ORDER BY
                    U.id_gas ASC,
                    U.userid ASC;";



      $stmt = $db->prepare($sql);
      if($from<>"" AND $to<>""){
        $stmt->bindParam(':from', $from, PDO::PARAM_STR);
        $stmt->bindParam(':to', $to, PDO::PARAM_STR);
      }
      if($idutente>0){
        $stmt->bindParam(':idutente', $idutente, PDO::PARAM_INT);
      }
      if($idreferente>0){
        $stmt->bindParam(':idreferente', $idreferente, PDO::PARAM_INT);
      }
      if($idditta>0){
        $stmt->bindParam(':idditta', $idditta, PDO::PARAM_INT);
      }
      if($idordine>0){
        $stmt->bindParam(':idordine', $idordine, PDO::PARAM_INT);
      }


      $h ='<table class="table table-condensed table-striped">';
      $h.='<thead>';
      $h.='<tr>';
      $h.='<td>Id ordine</td>';
      $h.='<td>Id Ref.</td>';
      $h.='<td>Data chiusura</td>';
      $h.='<td>Descrizione</td>';
      $h.='<td>Id utente</td>';
      $h.='<td>Nome</td>';
      $h.='<td>Id ditta</td>';
      $h.='<td>Ditta</td>';
      $h.='<td>Id gas</td>';
      $h.='<td>Gas</td>';
      $h.='<td>Codice</td>';
      $h.='<td>Descrizione</td>';
      $h.='<td>Qt&agrave; arrivata</td>';
      $h.='<td>Prezzo</td>';
      $h.='<td>U.M.</td>';
      $h.='</tr>';
      $h.='</thead>';
      $h.='<tbody>';



      $stmt->execute();
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach($rows as $row){
          if($csv){
            $row["qta_arr"]=_NF($row["qta_arr"]);
            $row["prz_dett_arr"]=_NF($row["prz_dett_arr"]);  
            fputcsv($output, $row,_USER_CSV_SEPARATOR);
          }else{
            $h.='<tr>';
            $h.='<td>'.$row["id_ordine"].'</td>';
            $h.='<td>'.$row["id_referente"].'</td>';
            $h.='<td>'.$row["data_chiusura"].'</td>';
            $h.='<td>'.$row["descrizione_ordini"].'</td>';
            $h.='<td>'.$row["id_utenti"].'</td>';
            $h.='<td>'.$row["fullname"].'</td>';
            $h.='<td>'.$row["id_ditte"].'</td>';
            $h.='<td>'.$row["descrizione_ditte"].'</td>';
            $h.='<td>'.$row["id_gas"].'</td>';
            $h.='<td>'.$row["descrizione_gas"].'</td>';
            $h.='<td>'.$row["art_codice"].'</td>';
            $h.='<td>'.$row["art_desc"].'</td>';
            $h.='<td>'._NF($row["qta_arr"]).'</td>';
            $h.='<td>'._NF($row["prz_dett_arr"]).'</td>';
            $h.='<td>'.$row["art_um"].'</td>';
            $h.='</tr>';
          }
      }
      $h.='</tbody>';
      $h.='</table>';


      if($csv){
        fclose($output);
      }else{
          $h = utf8_encode($h);
          $res=array("result"=>"OK", "msg"=>$h, "query"=>"" );
          echo json_encode($res);
      }
      die();
  }
?>
