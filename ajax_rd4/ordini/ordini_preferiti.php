<?php
require_once("inc/init.php");
$ui = new SmartUI;
$converter = new Encryption;

$page_title = "Ordini preferiti";
$page_id = "ordini_preferiti";



$sql="  SELECT
        ORD.*,O.*
        FROM retegas_options O
        INNER JOIN retegas_ordini ORD
        ON ORD.id_ordini=O.id_ordine
        WHERE
        O.chiave='_USER_ORDINE_PREFERITO'
        AND
        O.id_user='"._USER_ID."';";


$stmt = $db->prepare($sql);

    $stmt->execute();
    $rows = $stmt->fetchAll();


    $li = '<ul class="list-group" style="max-height:600px; overflow-y:auto;" id="list">';

    $i=0;

    foreach($rows as $row){
        $i++;

        $stmt = $db->prepare("SELECT U.fullname,G.descrizione_gas,G.id_gas FROM maaking_users U inner join retegas_gas G on G.id_gas=U.id_gas WHERE U.userid=:userid LIMIT 1;");
        $stmt->bindParam(':userid', $row["id_utente"], PDO::PARAM_INT);
        $stmt->execute();
        $utente = $stmt->fetch();

        $stmt = $db->prepare("SELECT D.id_ditte, D.descrizione_ditte,D.indirizzo FROM retegas_ditte D WHERE D.id_ditte=:id_ditta LIMIT 1;");
        $stmt->bindParam(':id_ditta', $row["id_ditte"], PDO::PARAM_INT);
        $stmt->execute();
        $ditta = $stmt->fetch();

        $stmt = $db->prepare("SELECT count(*) as conto FROM retegas_listini WHERE id_ditte=:id_ditte");
        $stmt->bindParam(':id_ditte', $row["id_ditte"], PDO::PARAM_INT);
        $stmt->execute();
        $listini = $stmt->fetch();

        $li.= '<li class="list-group-item lista_preferito_item'.$hidden.'" >
            <span class="element">
                <span class="pull-right"><button data-id_ordine="'.$row["id_ordini"].'" class="btn btn-xs btn-danger ordine_preferito"><i class="fa fa-times"></i></button></span>
                <i class="note">#'.$row["id_ordini"].'</i>&nbsp;<strong class="font-sm" style="cursor:pointer" rel="C"><a href="#ajax_rd4/ordini/ordine.php?id='.$row["id_ordini"].'">'.$row["descrizione_ordini"].'</a></strong><br>
                Inserito da '.$utente["fullname"].' ('.$utente["descrizione_gas"].')
            </span>
        </li>';

    }

    $li .="</ul>";

    if($i==0){
        $preferiti_vuoti='<div class="alert alert-info">
                          <p>Non hai segnato come "preferito" nessun ordine. Puoi farlo dalla sua scheda cliccando sulla stellina verde in alto a destra.</p>
                            </div>';
    }


?>


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
            <h1>Ordini preferiti</h1>
            <form class="smart-form">
                <section>
                    <label class="input"> <i class="icon-append fa fa-filter"></i>
                        <input type="text" placeholder="filtra tra gli ordini..." id="listfilter">
                    </label>
                </section>
            </form>
            <?php echo $li.$preferiti_vuoti; ?>
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
