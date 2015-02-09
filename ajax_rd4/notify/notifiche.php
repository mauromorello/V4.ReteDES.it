<?php
  $page_title = "Notifiche";
  require_once("inc/init.php");
  require_once("../../lib_rd4/class.rd4.listino.php");
  require_once("../../lib_rd4/class.rd4.ordine.php");



  $chiavi = array();
  $valori = array();


  //AIUTI LISTINI----------------------------------------------->
  $sql = "SELECT O.timbro,O.valore_int,O.id_user,U.fullname,L.id_listini,L.descrizione_listini from retegas_listini L
                        inner join retegas_options O on O.id_listino=L.id_listini
                        inner join maaking_users U on U.userid=O.id_user
                        where L.id_utenti='"._USER_ID."'
                        AND O.chiave='_AIUTO_GESTIONE_LISTINO'
                        AND valore_int=0";
  $stmt = $db->prepare($sql);
  $stmt->execute();
  $rows = $stmt->fetchAll();

  foreach($rows as $row){
      $notifiche[$row["timbro"]] = render_notifica($row["id_user"],"LISTINI","Posso gestire questo?",$row["descrizione_listini"],"#ajax_rd4/listini/listino.php?id=".$row["id_listini"]);
  }
  //AIUTI LISTINI

  //PRENOTAZIONE CREDITI----------------------------------------->

  if(_USER_PERMISSIONS & perm::puo_gestire_la_cassa){
      $sql = "select O.id_user, O.valore_real, O.valore_text,O.timbro from retegas_options O where O.id_gas=1 and O.chiave='PREN_MOV_CASSA' ";
      $stmt = $db->prepare($sql);
      $stmt->execute();
      $rows = $stmt->fetchAll();

      foreach($rows as $row){
          $notifiche[$row["timbro"]] = render_notifica($row["id_user"],"PRENOTAZIONE_CARICO","Prenotazione carico Eu.".$row["valore_real"].";",$row["valore_text"],"#");
      }
  }




  //-------------------------ORDINO
  krsort($notifiche);
?>
<ul class="notification-body">
     <?php foreach($notifiche as $notifica){echo $notifica;} ?>
</ul>