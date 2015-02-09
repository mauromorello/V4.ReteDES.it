<?php require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.ordine.php");
require_once("../../lib_rd4/class.rd4.user.php");

$ui = new SmartUI;
$page_title= "Aiutanti";
$page_id ="ordine_aiutanti";

//CONTROLLI
$id_ordine = (int)$_GET["id"];

if (!posso_gestire_ordine($id_ordine)){
    echo "Non ho i permessi per gestire questo ordine";
    die();
}

$O = new ordine($id_ordine);


      $sql = "select O.id_option, O.id_user, O.valore_real, O.valore_text,O.timbro, O.note_1, O.valore_int from retegas_options O where  O.chiave = 'AIUTO_ORDINI' AND O.id_ordine =:id_ordine; ";
      $stmt = $db->prepare($sql);
      $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
      $stmt->execute();
      $rows = $stmt->fetchAll();
      $h_o_c = $h_c_c = 0;
      foreach($rows as $row){
          $i++;
          $U = new user($row["id_user"]);
          $gas = $U->descrizione_gas;
          $fullname = $U->fullname;
          unset($U);

          $accetta    = '<button class="btn btn-block btn-success accetta_aiuto"  data-id_option="'.$row["id_option"].'">ACCETTA</button>';
          $rifiuta    = '<button class="btn btn-block btn-danger declina_aiuto"  data-id_option="'.$row["id_option"].'">DECLINA</button>';

          if($row["valore_int"]==0){
              $h_o_c++;
              $h_o.=' <div class="well well-sm richiesta">
                        <div class="row">
                            <div class="col-md-2"><img src="'.src_user($row["id_user"],240).'" class="img-responsive img-circle"></div>
                            <div class="col-md-8">
                                <h3>'.$fullname.', <small>'.$gas.'</small></h3>

                                <p class="font-md"><i class="fa fa-quote-left fa-2x pull-left fa-border"></i> '.$row["valore_text"].'</p>
                                <p class="note"><strong>'.$row["note_1"].'</strong></p>
                            </div>
                            <div class="col-md-2">
                                '.$accetta.'<br>
                                '.$rifiuta.'
                            </div>
                        </div>
                    </div>
                    <hr/>';
          }
          if($row["valore_int"]==1){
              $h_c_c++;
              $h_c.=' <div class="well well-sm richiesta">
                        <div class="row">
                            <div class="col-md-2"><img src="'.src_user($row["id_user"],240).'" class="img-responsive img-circle"></div>
                            <div class="col-md-10">
                                <h3>'.$fullname.', <small>'.$gas.'</small></h3>
                                <p class="font-md"><i class="fa fa-quote-left fa-2x pull-left fa-border"></i> '.$row["valore_text"].'</p>
                                <p class="note"><strong>'.$row["note_1"].'</strong></p>
                            </div>

                        </div>
                    </div>
                    <hr/>';
          }
      }
      if($h_c_c>0){
        $conferme ='<h1>Utenti che ti aiutano...</h1>'.$h_c;
      }else{
        $conferme ='';
      }
      if($h_o_c>0){
        $offerte ='<h1>Qualcuno vuole aiutarti:</h1>'.$h_o;
      }else{
        $offerte ='';
      }
      if(($h_c_c+$h_o_c)>0){
        $buu = '';
      }else{
        $buu= '<div class="jumbotron"><h1>Nessuno vuole aiutarti.</h1></div>';
      }


?>

<?php echo $O->navbar_ordine(); ?>

<section id="widget-grid" class="margin-top-10">
    <?php echo $buu; ?>
    <?php echo $offerte; ?>
    <?php echo $conferme; ?>

    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
        </article>

    </div>

</section>

<script type="text/javascript">
    /* DO NOT REMOVE : GLOBAL FUNCTIONS!
     *
     * pageSetUp(); WILL CALL THE FOLLOWING FUNCTIONS
     *
     * // activate tooltips
     * $("[rel=tooltip]").tooltip();
     *
     * // activate popovers
     * $("[rel=popover]").popover();
     *
     * // activate popovers with hover states
     * $("[rel=popover-hover]").popover({ trigger: "hover" });
     *
     * // activate inline charts
     * runAllCharts();
     *
     * // setup widgets
     * setup_widgets_desktop();
     *
     * // run form elements
     * runAllForms();
     *
     ********************************
     *
     * pageSetUp() is needed whenever you load a page.
     * It initializes and checks for all basic elements of the page
     * and makes rendering easier.
     *
     */

    pageSetUp();


    var pagefunction = function() {

        //-------------------------HELP
        <?php echo help_render_js($page_id); ?>
        //-------------------------HELP
        $('.accetta_aiuto').click(function(){
            $this=$(this);
            id_option = $this.data('id_option');
            console.log('id_option accetta: '+id_option);
            $.ajax({
                          type: "POST",
                          url: "ajax_rd4/ordini/_act.php",
                          dataType: 'json',
                          data: {act: "accetta_aiuto", id_option:id_option},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    ok(data.msg);}else{ko(data.msg);}
                                    //location.reload();
                        });

        });
        $('.declina_aiuto').click(function(){
            $this=$(this);
            id_option = $this.data('id_option');
            console.log('id_option rifiuta: '+id_option);
            $.ajax({
                          type: "POST",
                          url: "ajax_rd4/ordini/_act.php",
                          dataType: 'json',
                          data: {act: "declina_aiuto", id_option:id_option},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    ok(data.msg);}else{ko(data.msg);}
                                    location.reload();
                        });
        });

    }
    // end pagefunction

    loadScript("js/plugin/jquery-form/jquery-form.min.js", pagefunction);



</script>
