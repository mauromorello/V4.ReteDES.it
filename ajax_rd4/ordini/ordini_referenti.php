<?php
require_once("inc/init.php");
if(file_exists("../../lib_rd4/class.rd4.ordine.php")){require_once("../../lib_rd4/class.rd4.gas.php");}
if(file_exists("../lib_rd4/class.rd4.ordine.php")){require_once("../lib_rd4/class.rd4.gas.php");}
$ui = new SmartUI;
$converter = new Encryption;

$page_title = "Referenti ordine";
$page_id = "referenti_ordine";

//CONTROLLI
$id_ordine = (int)$_GET["id"];
$O = new ordine($id_ordine);

$array_nomi = array();

if (!posso_gestire_ordine($id_ordine)){
    echo rd4_go_back("Non ho i permessi necessari");die;      ;
}


    //REFERENTI 
   $rows = $O->lista_referenti_gas_partecipanti();
   foreach($rows as $row){
    $Gas .= 'R GAS--:'.$row['userid'].'<br>';
    
    $useridEnc = $converter->encode($row["userid"]);
    $p1 = '<img class="" src="'.src_user($row["userid"],64).'" style="width:32px;height:32px;">';
    
    $z .= '<tr rel="'.$useridEnc.'">
            <td><label class="checkbox"><input type="checkbox" class="utente" value="'.$row["userid"].'"><i></i></label></td>
            <td>'.$p1.'</td>
            <td><a href="#ajax_rd4/user/scheda.php?id='.$useridEnc.'">'.$row["fullname"].'</a></td>
            <td>'.$row["descrizione_gas"].'</td>
            <td><a class="note" href="mailto:'.$row["email"].'?subject=[reteDES] Comunicazione su ordine #'.$O->id_ordini.'" TARGET="_BLANK">'.$row["email"].'</a><br/><a class="note" href="tel:'.$row["tel"].'">'.$row["tel"].'</a></td>
            <td>REFERENTE GAS</td>
          </tr>';
    $array_nomi[]='<tr rel="'.$useridEnc.'">
            <td><label class="checkbox"><input type="checkbox" class="utente" value="'.$row["userid"].'"><i></i></label></td>
            <td>'.$p1.'</td>
            <td><a href="#ajax_rd4/user/scheda.php?id='.$useridEnc.'">'.$row["fullname"].'</a></td>
            <td>'.$row["descrizione_gas"].'</td>
            <td><a class="note" href="mailto:'.$row["email"].'?subject=[reteDES] Comunicazione su ordine #'.$O->id_ordini.'" TARGET="_BLANK">'.$row["email"].'</a><br/><a class="note" href="tel:'.$row["tel"].'">'.$row["tel"].'</a></td>
            <td>REFERENTE GAS</td>
          </tr>';
    
   }


   //REFERENTI EXTRA
   $rows = $O->lista_referenti_extra();
   foreach($rows as $row){
    $Gas .= 'EXTR--:'.$row['userid'].'<br>';
    
    $useridEnc = $converter->encode($row["userid"]);
    $p1 = '<img class="" src="'.src_user($row["userid"],64).'" style="width:32px;height:32px;">';
    
    $z .= '<tr rel="'.$useridEnc.'">
            <td><label class="checkbox"><input type="checkbox" class="utente" value="'.$row["userid"].'"><i></i></label></td>
            <td>'.$p1.'</td>
            <td><a href="#ajax_rd4/user/scheda.php?id='.$useridEnc.'">'.$row["fullname"].'</a></td>
            <td>'.$row["descrizione_gas"].'</td>
            <td><a class="note" href="mailto:'.$row["email"].'?subject=[reteDES] Comunicazione su ordine #'.$O->id_ordini.'" TARGET="_BLANK">'.$row["email"].'</a><br/><a class="note" href="tel:'.$row["tel"].'">'.$row["tel"].'</a></td>
            <td>REFERENTE EXTRA</td>
          </tr>';
    $array_nomi[]='<tr rel="'.$useridEnc.'">
            <td><label class="checkbox"><input type="checkbox" class="utente" value="'.$row["userid"].'"><i></i></label></td>
            <td>'.$p1.'</td>
            <td><a href="#ajax_rd4/user/scheda.php?id='.$useridEnc.'">'.$row["fullname"].'</a></td>
            <td>'.$row["descrizione_gas"].'</td>
            <td><a class="note" href="mailto:'.$row["email"].'?subject=[reteDES] Comunicazione su ordine #'.$O->id_ordini.'" TARGET="_BLANK">'.$row["email"].'</a><br/><a class="note" href="tel:'.$row["tel"].'">'.$row["tel"].'</a></td>
            <td>REFERENTE EXTRA</td>
          </tr>';
   }


$Gas_p = $O->lista_gas_partecipanti();
foreach($Gas_p as $G){
   $Gas .= 'GAS:'.$G[0].'<br>';
   
   
   $GA = new gas($G[0]);
   
   
   //SUPERVISORI ORDINI
   $rows = $GA->lista_supervisori_ordini();
   foreach($rows as $row){
    $Gas .= 'SUP--:'.$row['userid'].'<br>';
    
    $useridEnc = $converter->encode($row["userid"]);
    $p1 = '<img class="" src="'.src_user($row["userid"],64).'" style="width:32px;height:32px;">';
    
    $z .= '<tr rel="'.$useridEnc.'">
            <td><label class="checkbox"><input type="checkbox" class="utente" value="'.$row["userid"].'"><i></i></label></td>
            <td>'.$p1.'</td>
            <td><a href="#ajax_rd4/user/scheda.php?id='.$useridEnc.'">'.$row["fullname"].'</a></td>
            <td>'.$G["descrizione_gas"].'</td>
            <td><a class="note" href="mailto:'.$row["email"].'?subject=[reteDES] Comunicazione su ordine #'.$O->id_ordini.'" TARGET="_BLANK">'.$row["email"].'</a><br/><a class="note" href="tel:'.$row["tel"].'">'.$row["tel"].'</a></td>
            <td>SUPERVISORE</td>
          </tr>';
    $array_nomi[]='<tr rel="'.$useridEnc.'">
            <td><label class="checkbox"><input type="checkbox" class="utente" value="'.$row["userid"].'"><i></i></label></td>
            <td>'.$p1.'</td>
            <td><a href="#ajax_rd4/user/scheda.php?id='.$useridEnc.'">'.$row["fullname"].'</a></td>
            <td>'.$G["descrizione_gas"].'</td>
            <td><a class="note" href="mailto:'.$row["email"].'?subject=[reteDES] Comunicazione su ordine #'.$O->id_ordini.'" TARGET="_BLANK">'.$row["email"].'</a><br/><a class="note" href="tel:'.$row["tel"].'">'.$row["tel"].'</a></td>
            <td>SUPERVISORE</td>
          </tr>';
   }
   
   
   
   
   
}

$array_nomis= array_unique($array_nomi);
foreach ($array_nomis as $nome){$x.=$nome;}


$a='    <div class="" style="max-height:600px;overflow-y:auto;">
        
        <table id="dt_utenti_gas" class="table table-striped margin-top-10 has-tickbox smart-form">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>Nome</th>
                                        <th><i class="fa fa-home"></i>&nbsp;GAS</th>
                                        <th>Contatti</th>
                                        <th>Ruolo</th>
                                    </tr>
                                </thead>
                             
                                <tbody>
                                    '.$x.'
                                </tbody>
                             </TABLE>        
                                        
        </div>
        <div class="well margin-top-5">
            <label class="pull-right ">
            <input class="selectall" type="checkbox"> Seleziona / deseleziona tutti
            </label>
            <p><button class="btn btn-default" id="manda_messaggio_a_utenti"><i class="fa fa-envelope"></i>   manda ai referenti selezionati un messaggio.</button></p>

            <label>Messaggio:</label>
            <textarea  id="messaggio_a_utenti" style="width:100%;"></textarea>
            <p></p>
            <div class="alert alert-info"><strong>ATTENZIONE:</strong> Non abusare di questa funzione. A nessuno piace ricevere mail inutili.</div>
        </div>

        ';
        $mp ='
        <div class="row ">
            <div class=" col-xs-4">
                <h4>Filtra la tabella:</h4>
                <div class="btn-group-vertical btn-block">
                    <a class="show_Tutti btn btn-default">Tutti</a>
                    <a class="show_Partecipa btn btn-default">Solo i referenti </a>
                </div>
            </div>

            <div class="col-xs-4">
            </div>
            <div class="col-xs-4">
            </div>
        </div>
      ';

?>

<?php echo $O->navbar_ordine();?>
<h1>Referenti ordine</h1>
<?php echo $a; ?>
<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
        </article>
    </div>
</section>

<!-- Dynamic Modal -->
<div class="modal fade" id="remoteModal" tabindex="-1" role="dialog" aria-labelledby="remoteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- content will be filled here from "ajax/modal-content/model-content-1.html" -->
        </div>
    </div>
</div>

<script type="text/javascript">

    pageSetUp();



    var pagefunction = function(){
        //-------------------------HELP
        <?php echo help_render_js($page_id); ?>
        //-------------------------HELP
         var oTable= $('#dt_utenti_gas').dataTable({
                                            "bPaginate": false
                                        });
        var id;
        var messaggio;

        $('#manda_messaggio_a_utenti').click(function(){
            console.log("Click");
            values = $('input:checkbox:checked.utente').map(function () {
              return this.value;
            }).get();
            messaggio = $('#messaggio_a_utenti').val();
            console.log("Messaggio " + messaggio);
            if(!messaggio){
                ko("Messaggio vuoto");
            }
            else if(values.length==0){
                ko("Nessun destinatario");
            }else{
            console.log(values);

            $.SmartMessageBox({
                title : "Messaggia",
                content : "Confermi? la mail sarà inviata a " + values.length + " utenti",
                buttons : "[Esci][INVIA]"
            }, function(ButtonPress, Value) {

                if(ButtonPress=="INVIA"){
                    $.blockUI();
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/gas/_act.php",
                          dataType: 'json',
                          data: {act: "messaggia_utenti", values : values, messaggio : messaggio},
                          context: document.body
                        }).done(function(data) {
                            $.unblockUI();
                            if(data.result=="OK"){
                                    ok(data.msg);
                                    //location.reload();
                            }else{
                                ko(data.msg);
                            }

                        });
                }
            });




        }//messaggio vuoto

        });


        $('.selectall').click(function(event) {  //on click
            console.log("Click select");
            if(this.checked) { // check select status
                $('.utente').each(function() { //loop through each checkbox
                    this.checked = true;  //select all checkboxes with class "checkbox1"
                });
            }else{
                $('.utente').each(function() { //loop through each checkbox
                    this.checked = false; //deselect all checkboxes with class "checkbox1"
                });
            }
        });





        $('body').on('hidden.bs.modal', '.modal', function () {
          $(this).removeData('bs.modal');
        });

        $('body').on('shown.bs.modal','.modal', function(e) {
            console.log("Modal opened");
            id = $(e.relatedTarget).attr('data-id');
        });


        $(document).on( 'change', '#usermessage', function() {
            messaggio = $(this).val();
            console.log("messaggio = " + messaggio);

        });

        $('.show_Partecipa').click(function(){oTable.fnFilter( 'SI',4 );});
        $('.show_NonPartecipa').click(function(){oTable.fnFilter( 'NO',4 );});
        $('.show_Tutti').click(function(){  oTable.fnFilter('',4);
                                            oTable.fnFilter('');}
        );

        //$('#dt_utenti_gas thead th').each( function () {
        //    var title = $(this).text();
        //    $(this).html( '<input type="text" placeholder="Filtra '+title+'" />' );
        //} );
     
    } // end pagefunction



    loadScript("js/plugin/datatables/jquery.dataTables.min.js", function(){
        loadScript("js/plugin/datatables/dataTables.colVis.min.js", function(){
            loadScript("js/plugin/datatables/dataTables.tableTools.min.js", function(){
                loadScript("js/plugin/datatables/dataTables.bootstrap.min.js", function(){
                    loadScript("js/plugin/datatable-responsive/datatables.responsive.min.js", function(){
                            loadScript("js/plugin/x-editable/x-editable.min.js", pagefunction())

                    });
                });
            });
        });
    });
</script>
