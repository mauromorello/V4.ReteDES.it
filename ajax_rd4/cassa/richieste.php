<?php
    require_once("inc/init.php");
    require_once("../../lib_rd4/class.rd4.user.php");

    $ui = new SmartUI;
    $page_title = "Richieste carico";
    $page_id = "cassa_richieste";

    if(!(_USER_PERMISSIONS & perm::puo_gestire_la_cassa)){
        echo rd4_go_back("Non ho i permessi per la cassa");die;
    }

    $i = 0;
    if(_GAS_CASSA_BONIFICO_AUTOMATICO){
        $avviso = '<h4 class="alert alert-danger">Il tuo gas adotta l\'opzione "Bonifico automatico", il che vuol dire che al momento della richiesta i soldi vengono accreditati sul conto del richiedente. In questa pagina puoi solo controllare gli importi caricati, e se sono corretti nascondere il messaggio di avviso.</h4>';
    }ELSE{
        $avviso = '';
    }

  if(_USER_PERMISSIONS & perm::puo_gestire_la_cassa){
      $sql = "select O.id_option, O.id_user, O.valore_real, O.valore_text,O.timbro, O.note_1 from retegas_options O where O.id_gas=:id_gas and O.chiave='PREN_MOV_CASSA' ";
      $stmt = $db->prepare($sql);
      $id_gas = _USER_ID_GAS;
      $stmt->bindValue(':id_gas', $id_gas, PDO::PARAM_INT);
      $stmt->execute();
      $rows = $stmt->fetchAll();

      foreach($rows as $row){
          $i++;
          $U = new user($row["id_user"]);
          $gas = $U->descrizione_gas;
          $fullname = $U->fullname;
          unset($U);

          if(!_GAS_CASSA_BONIFICO_AUTOMATICO){
            $carica     = '<button class="btn btn-block btn-success add_richiesta"  data-id_option="'.$row["id_option"].'">CARICA</button>';
            $rifiuta    = '<button class="btn btn-block btn-danger del_richiesta"  data-id_option="'.$row["id_option"].'">RIFIUTA</button>';
          }else{
            $carica     = '';
            $rifiuta    = '<button class="btn btn-block btn-primary del_richiesta"  data-id_option="'.$row["id_option"].'">CONFERMA</button>';
          }

          $h.=' <div class="well well-sm richiesta">
                    <div class="row">
                        <div class="col-md-1"><img src="'.src_user($row["id_user"],240).'" class="img-responsive img-circle"></div>
                        <div class="col-md-9">
                            <h3>'.$fullname.', <small>'.$gas.'</small></h3>
                            <p>Richiesta di carico di Eu. <strong>'.$row["valore_real"].'</strong></p>
                            <p>'.$row["valore_text"].'</p>
                            <p>Del: <strong>'.conv_datetime_from_db($row["timbro"]).'</p>
                            <p class="note">Documento: <strong>'.$row["note_1"].'</strong></p>
                        </div>
                        <div class="col-md-2">
                            '.$carica.'<br>
                            '.$rifiuta.'
                        </div>
                    </div>
                </div>
                <hr/>';

      }
  }

  //20.30 alfieri fabio
  //13 dicembre openday medie


  if($i==0){
      $h ='<div class="jumbotron"><h1>Attualmente non ci sono richieste di carico.</h1></div>';
  }
  //AIUTI LISTINI





  //-------------------------ORDINO

?>

<h1>Richieste di carico :</h1>
<hr>
<?php echo $avviso.$h; ?>


<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
        </article>
    </div>
</section>
<script type="text/javascript">

    pageSetUp();


    var pagefunction = function() {

        //-------------------------HELP
        <?php echo help_render_js($page_id); ?>
        //-------------------------HELP
        $('.add_richiesta').click(function(){
            $.blockUI();
            var $this = $(this);
            id_option = $this.data('id_option');
            
            $.ajax({
                          type: "POST",
                          url: "ajax_rd4/cassa/_act.php",
                          dataType: 'json',
                          data: {act: "add_richiesta", id : id_option},
                          context: document.body
                        }).done(function(data) {
                            $.unblockUI();
                            if(data.result=="OK"){
                                ok(data.msg);
                                $this.closest('.richiesta').hide();
                            }else{
                                ko(data.msg)
                            ;}

                        });

        });
        $('.del_richiesta').click(function(){
            $.blockUI();
            var $this = $(this);
            id_option = $this.data('id_option');
            $.ajax({
                          type: "POST",
                          url: "ajax_rd4/cassa/_act.php",
                          dataType: 'json',
                          data: {act: "del_richiesta", id : id_option},
                          context: document.body
                        }).done(function(data) {
                            $.unblockUI();
                            if(data.result=="OK"){
                                ok(data.msg);
                                $this.closest('.richiesta').hide();
                            }else{
                                ko(data.msg)
                            ;}

                        });

        });
    }
    // end pagefunction

    pagefunction();



</script>