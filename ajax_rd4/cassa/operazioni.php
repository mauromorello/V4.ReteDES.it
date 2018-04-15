<?php
require_once("inc/init.php");
if(file_exists("../../lib_rd4/class.rd4.cassa.php")){require_once("../../lib_rd4/class.rd4.cassa.php");}
if(file_exists("../lib_rd4/class.rd4.cassa.php")){require_once("../lib_rd4/class.rd4.cassa.php");}

$ui = new SmartUI;
$page_title = "Operazioni cassa";
$page_id = "operazioni_cassa";

$C = new cassa(_USER_ID_GAS);
$stmt = $db->prepare("SELECT userid, fullname, email, G.descrizione_gas FROM maaking_users U inner join retegas_gas G on G.id_gas=U.id_gas WHERE U.id_gas="._USER_ID_GAS." and isactive=1;");
$stmt->execute();
$rows = $stmt->fetchAll();
$us ='<select style="width:100%"  id="user_selection"><option value="0">Scegli un utente del tuo GAS.</option>';
foreach($rows as $row){
    //FROM USER CASSA
            $stmtC = $db->prepare("SELECT * FROM retegas_options WHERE id_user=".$row["userid"]." AND chiave='_USER_USA_CASSA' LIMIT 1;");
            $stmtC->execute();
            $rowC = $stmtC->fetch(PDO::FETCH_ASSOC);
            if($rowC["valore_text"]=='SI'){
                $us .='<option value="'.$row["userid"].'">#'.$row["userid"].': '.$row["fullname"].'</option>';    
            }
    
    
}
$us .= '</select>';


$a =$us ;

$button[] = '<a href="#ajax_rd4/cassa/opzioni.php" class=""><i class="fa fa-check " title="Opzioni"></i></a> ';
$button[] = '<a href="#ajax_rd4/cassa/richieste.php" class=""><i class="fa fa-plus-square " title="Carichi"></i></a> ';
$button[] = '<a href="#ajax_rd4/cassa/operazioni.php" class=""><i class="fa fa-pencil " title="operazioni"></i></a> ';
$button[] = '<a href="#ajax_rd4/cassa/saldo_utenti.php" class=""><i class="fa fa-euro " title="Saldi utenti"></i></a> ';
$button[] = '<a href="#ajax_rd4/cassa/allineamento_ordini.php" class=""><i class="fa fa-balance-scale " title="Allineamento ordini"></i></a> ';
$button[] = '<a href="#ajax_rd4/cassa/cassa_home.php" class=""><i class="fa fa-bank " title="riepilogo"></i></a> ';
?>

<?php echo navbar('<i class="fa fa-bank fa"></i>  Cassa',$button); ?>
<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>

        </article>
    </div>
</section>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="well well-sm">
        <header>
            <h3>Inserisci un movimento di cassa ad un utente</h3>
        </header>

        <form class="smart-form">
        <section>
        <?php echo $a ?>
        <div class="note"><strong>NB:</strong> si possono fare operazioni solo su utenti attivi e che hanno la cassa attivata.</div>
        <div id="user_box" class="margin-top-10"></div>
        </section>
        <br>
        <section>
            <label class="label">Inserisci la cifra (positiva o negativa)</label>
            <label class="input">
                <input type="text" class="input-lg"  id="importo">
            </label>
        </section>
        <section>
            <label class="label">Inserisci una descrizione per il movimento</label>
            <label class="input">
                <input type="text" class="input" id="descrizione">
            </label>
        </section>
        <section>
            <label class="label">Inserisci un ID ordine (opzionale)</label>
            <label class="input">
                <input type="text" class="input" id="id_ordine">
            </label>
            <div class="note">L'ID ordine può servire se si vuole collegare il movimento ad uno specifico ordine.<br>
            <strong>NB:</strong> non vi è un controllo se l'ID ordine è compatibile con un ordine reale del proprio GAS.</div>
        </section>

        <section>
            <label class="label">Inserisci un numero di documento (opzionale)</label>
            <label class="input">
                <input type="text" class="input" id="documento">
            </label>
        </section>
        <section>
            <label class="label">Data del movimento (opzionale)</label>
            <label class="input">
                <input type="text" class="input" id="data_movimento">
            </label>
            <div class="note">QUESTO PARAMETRO NON E' ANCORA ATTIVO</div>
        </section>
        <footer>
            <button type="submit" class="btn btn-primary" id="button-inserisci">
                Inserisci
            </button>

        </footer>
        </form>
        </div>

    </div>
</div>

<script type="text/javascript">
    $(document).prop('title', 'ReteDes::<?echo $page_title?>');

    pageSetUp();
    var userid=0;
    var pagefunction = function(){

        //------------HELP WIDGET
        <?php echo help_render_js($page_id);?>
        //------------END HELP WIDGET

         $('#user_selection').select2();
         $('#user_selection').on("select2-selected", function(e){
            userid=e.val;
            $.ajax({
                  type: "POST",
                  url: "ajax_rd4/cassa/_act.php",
                  dataType: 'json',
                  data: {act: "show_cassa_user_box", idu:userid},
                  context: document.body
                }).done(function(data) {
                    if(data.result=="OK"){
                        $('#user_box').html(data.html);
                    }else{
                        ko(data.msg);
                    }

                });

         });
         $('#button-inserisci').click(function(e){


             var importo=$('#importo').val();
             var descrizione=$('#descrizione').val();
             var documento=$('#documento').val();
             var id_ordine=$('#id_ordine').val();
             var data_movimento=$('#data_movimento').val();
             $.ajax({
                  type: "POST",
                  url: "ajax_rd4/cassa/_act.php",
                  dataType: 'json',
                  data: {act: "cassa_add_movimento", idu:userid,importo:importo,descrizione:descrizione,documento:documento,id_ordine:id_ordine,data_movimento:data_movimento},
                  context: document.body
                }).done(function(data) {
                    if(data.result=="OK"){
                        $('#user_box').html(data.html);
                        ok("Movimento aggiunto.");
                    }else{
                        ko(data.msg);
                    }

                });
             e.preventDefault;
         })

    }

    pagefunction();
</script>