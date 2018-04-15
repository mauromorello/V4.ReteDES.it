<?php
    require_once("inc/init.php");
    require_once("../../lib_rd4/class.rd4.user.php");

    $ui = new SmartUI;
    $converter = new Encryption;

    $page_title = "Attivazione utenti nuovi";
    $page_id = "attivazione_utenti";

    if(!(_USER_PERMISSIONS & perm::puo_gestire_utenti)){
        echo rd4_go_back("Non ho i permessi per gestire le attivazioni");die;
    }

    if(isset($_GET["id_gas"])){
        $id_gas=CAST_TO_INT($_GET["id_gas"],0);
    }else{
        $id_gas=_USER_ID_GAS;
    }


    $sql = "select * from maaking_users U where U.id_gas=:id_gas and U.isactive=0";
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':id_gas', $id_gas, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();

      foreach($rows as $row){
          $i++;
          $U = new user($row["userid"]);
          $gas = $U->descrizione_gas;
          $fullname = $U->fullname;
          $email = $U->email;
          $useridEnc = $converter->encode($row["userid"]);
          unset($U);

          $nome = str_ireplace("'",  "\'",$row["fullname"]);

          
          if(canSend($email)){
                $attiva    =  '<a href="javascript:attiva(\''.$useridEnc.'\',\''.$nome.'\');" class="btn btn-block btn-success" >ATTIVA</a>';
          }else{
                $attiva    =  '<a href="javascript:void(0);" class="btn btn-block btn-success disabled" >ATTIVA</a>';         
                $msg_bounce=  '<p class="alert alert-danger">Questo utente non è attivabile perchè la sua mail risulta irraggiungibile.</p>';
          }
          
          $sospendi    = '<a href="javascript:sospendi(\''.$useridEnc.'\',\''.$nome.'\');"  class="btn btn-block btn-warning">SOSPENDI</a>';
          $elimina    = '<a href="javascript:elimina(\''.$useridEnc.'\',\''.$nome.'\');" class="btn btn-block btn-danger"><i class="fa fa-warning"></i> ELIMINA</a>';

          
          
          if(trim($row["profile"])<>""){
              $suo_messaggio='Suo messaggio: <i>'.$row["profile"].'</i>';
          }else{
              $suo_messaggio='';
          }

          $h.=' <div class="well well-sm richiesta '.$useridEnc.'">
                    <div class="row">
                        <div class="col-md-1"><img src="'.src_user($row["id_user"],240).'" class="img-responsive img-circle"></div>
                        <div class="col-md-9">
                            <h3><b>#'.$row["userid"].'</b> '.$fullname.', <small>'.$gas.'</small></h3>
                            <p class="note">Registrata il <b>'.conv_datetime_from_db($row["regdate"]).'</b></p>
                            <p>Mail <a href="mailto:'.$row["email"].'">'.$row["email"].'</a> Tel. <a href="tel:'.$row["tel"].'">'.$row["tel"].'</a></p>
                            '.$suo_messaggio.'<br>
                            '.$msg_bounce.'
                        </div>
                        <div class="col-md-2">
                            '.$attiva.'<br>
                            '.$sospendi.'<br>
                            '.$elimina.'
                        </div>
                    </div>
                </div>
                <hr/>';

      }





  if($i==0){
      $h ='<div class="jumbotron"><h1>Attualmente non ci sono nuovi utenti da attivare</h1></div>';
  }
  //AIUTI LISTINI





  //-------------------------ORDINO

?>

<h1>Attivazione nuovi utenti :</h1>
<hr>
<?php echo $h; ?>


<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
        </article>
    </div>
</section>
<script type="text/javascript">

    pageSetUp();

    var attiva = function(userid, fullname) {


            $.SmartMessageBox({
                title : "Attiva " + fullname,
                content : "Verrà comunicata l'avvenuta attivazione con una mail.",
                buttons : "[Esci][Attiva]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="Attiva"){

                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/gas/_act.php",
                          dataType: 'json',
                          data: {act: "attiva_utente", value : userid},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    ok(data.msg);
                                    $('.'+userid).hide();
                            }else{ko(data.msg);}
                                    //location.reload();
                        });
                }
            });
        }
    var sospendi = function(userid, fullname) {

            $.SmartMessageBox({
                title : "Sospendi " + fullname,
                content : "L'utente rimarrà sospeso fino ad una nuova riattivazione.",
                buttons : "[Esci][Sospendi]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="Sospendi"){

                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/gas/_act.php",
                          dataType: 'json',
                          data: {act: "sospendi_utente", value : userid},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    ok(data.msg);
                                    $('.'+userid).hide();
                                    //location.reload();
                            }else{ko(data.msg);}

                        });
                }
            });
        }
    var elimina = function(userid, fullname) {

            $.SmartMessageBox({
                title : "Sospendi " + fullname,
                content : "L'utente viene eliminato fisicamente da reteDES.",
                buttons : "[Esci][Elimina]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="Elimina"){

                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/gas/_act.php",
                          dataType: 'json',
                          data: {act: "elimina_utente_secco", value : userid},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    ok(data.msg);
                                    $('.'+userid).hide();
                                    //location.reload();
                            }else{ko(data.msg);}

                        });
                }
            });
        }
    var pagefunction = function() {

        //-------------------------HELP
        <?php echo help_render_js($page_id); ?>
        //-------------------------HELP

    }
    // end pagefunction

    pagefunction();



</script>