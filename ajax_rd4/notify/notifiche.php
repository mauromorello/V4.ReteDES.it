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


  //NUOVI USERS
    if(_USER_PERMISSIONS & perm::puo_gestire_utenti){
        $stmt = $db->prepare("select count(*) as conto from maaking_users where isactive=0 AND id_gas='"._USER_ID_GAS."' ");
        $stmt->execute();
        $row = $stmt->fetch();
        if($row["conto"]>0){
            if($row["conto"]==1){
                $uten = 'C\'è <b>un</b> nuovo utente da attivare;';
            }else{
                $uten = 'Ci sono <b>'.$row["conto"].'</b> nuovi utenti da attivare;';
            }
            $alert_users='<p class="alert alert-warning margin-top-10"><a href="#ajax_rd4/gas/gas_attivazioni.php" class="pull-left margin-top-5" style="margin-right:10px;" rel="tooltip" data-content="Vai alla tabella movimenti"><i class="fa fa-2x fa-user-plus"></i></a><span class="note">COME GESTORE UTENTI</span><br>  '.$uten.'</p>';
            $notifiche[] = render_notifica(0,"NUOVI_UTENTI",$uten,"", APP_URL."/#ajax_rd4/gas/gas_attivazioni.php");

        }else{
            $alert_users='';

        }

    }



  //-------------------------ORDINO
  krsort($notifiche);
?>
<ul class="notification-body">

     <?php foreach($notifiche as $notifica){echo $notifica;} ?>
</ul>