<?php
require_once("inc/init.php");
$ui = new SmartUI;
$converter = new Encryption;

$page_title = "Abituali del mio DES";
$page_id = "des_fornitori_abituali";

if(_USER_ID_DES<2){
    echo rd4_go_back("Non fai parte di un DES :(");
    die();
}

$sql="  SELECT D.*, COUNT(D.id_ditte) as conto FROM `retegas_ditte` D
    LEFT JOIN retegas_listini L on L.id_ditte=D.id_ditte
    LEFT JOIN retegas_ordini O on O.id_listini=L.id_listini
    LEFT JOIN maaking_users U on U.userid=O.id_utente
    INNER JOIN retegas_gas G on G.id_gas=U.id_gas
    WHERE G.id_des='"._USER_ID_DES."'
    GROUP BY D.id_ditte
    ORDER BY COUNT(D.id_ditte) DESC;";



$stmt = $db->prepare($sql);

    $stmt->execute();
    $rows = $stmt->fetchAll();


    $li = '<ul class="list-group" style="max-height:600px; overflow-y:auto;" id="list">';
    foreach($rows as $row){

        $stmt = $db->prepare("SELECT U.fullname,G.descrizione_gas,G.id_gas, D.des_descrizione FROM maaking_users U inner join retegas_gas G on G.id_gas=U.id_gas INNER JOIN retegas_des D ON D.id_des=G.id_des WHERE U.userid=:userid LIMIT 1;");
        $stmt->bindParam(':userid', $row["id_proponente"], PDO::PARAM_INT);
        $stmt->execute();
        $utente = $stmt->fetch();

        if(CAST_TO_INT($row["id_ditte"])===0){
            $descrizione_ditta="ORDINE MULTIDITTA";
            $inserita_da="";
            $indirizzo_e_mail='Questi ordini sono stati effettuati con listini multiditta.<br>';
            $background=' bg-color-orange ';
        }else{
            $descrizione_ditta=$row["descrizione_ditte"];
            $inserita_da='Inserita dal '.$utente["descrizione_gas"];
            $indirizzo_e_mail= $row["indirizzo"].'&nbsp;&nbsp;<small class="font-xs"><i class="fa fa-envelope"></i>&nbsp;<b>'.$row["mail_ditte"].'</b></small><br>';
            $background='';
        }

        $li.= '<li class="list-group-item '.$hidden.$background.'" >
            <span class="element">
                <span class="pull-right"><span class="badge">'.$row["conto"].'</span> ordini fatti</span>
                <strong class="font-sm" style="cursor:pointer" rel="C"><a href="#ajax_rd4/fornitori/scheda.php?id='.$row["id_ditte"].'">'.$descrizione_ditta.'</a></strong> -
                '.$indirizzo_e_mail.'
                '.$inserita_da.'
            </span>
        </li>';

    }

    $li .="</ul>";



    $title_navbar='Fornitori abituali del mio GAS';


?>

<?php echo navbar2($title_navbar,$buttons); ?>

<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
        </article>

    </div>

    <div class="row">
        <!-- PRIMA COLONNA-->

        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <h1>Fornitori abituali del mio GAS</h1>
            <form class="smart-form">
                <section>
                    <label class="input"> <i class="icon-append fa fa-filter"></i>
                        <input type="text" placeholder="filtra tra i fornitori..." id="listfilter">
                    </label>
                </section>
            </form>
            <?php echo $li; ?>
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
                        <!-- /.modal -->

<script type="text/javascript">

    pageSetUp();



    var pagefunction = function(){
        //-------------------------HELP
        <?php echo help_render_js($page_id); ?>
        //-------------------------HELP

     jQuery.expr[':'].Contains = function(a,i,m){
              return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase())>=0;
          };
        function listFilter(list) { // header is any element, list is an unordered list
            // create and add the filter form to the header
            $('#listfilter')
              .change( function () {
                  var filter = $(this).val();
                  console.log(filter);
                if(filter) {
                  // this finds all links in a list that contain the input,
                  // and hide the ones not containing the input while showing the ones that do
                  $(list).find("span.element:not(:Contains(" + filter + "))").parent().hide();
                  $(list).find("span.element:Contains(" + filter + ")").parent().show();
                } else {
                  $(list).find("li").show();
                }
                return false;
              })
            .keyup( function () {
                // fire the above change event after every letter
                $(this).change();
            });
          }


     listFilter( $("#list"));
    } // end pagefunction



    pagefunction();
</script>
