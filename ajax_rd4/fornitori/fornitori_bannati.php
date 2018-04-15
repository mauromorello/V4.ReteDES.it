<?php
require_once("inc/init.php");
$ui = new SmartUI;
$converter = new Encryption;

$page_title = "Fornitori esclusi";
$page_id = "fornitori_esclusi";

if(CAST_TO_STRING($_GET["do"]=="add")){
    $id=CAST_TO_INT($_GET["id"],0);
    if($id>0){
        $sql = "DELETE FROM retegas_options WHERE id_user="._USER_ID." AND chiave='_USER_FORNITORE_BANNATO' AND id_ditta=".$id." LIMIT 5; ";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        $sql = "INSERT INTO retegas_options (id_user, chiave, valore_int, id_ditta) values ("._USER_ID.",'_USER_FORNITORE_BANNATO',1,".$id.")";
        $stmt = $db->prepare($sql);
        $stmt->execute();
    }
}



$sql="  SELECT
        D.*,O.*
        FROM retegas_options O
        INNER JOIN retegas_ditte D
        ON D.id_ditte=O.id_ditta
        WHERE
        O.chiave='_USER_FORNITORE_BANNATO'
        AND
        O.id_user='"._USER_ID."';";


$stmt = $db->prepare($sql);

    $stmt->execute();
    $rows = $stmt->fetchAll();


    $li = '<ul class="list-group" style="max-height:600px; overflow-y:auto;" id="list">';
    foreach($rows as $row){

        $stmt = $db->prepare("SELECT U.fullname,G.descrizione_gas,G.id_gas FROM maaking_users U inner join retegas_gas G on G.id_gas=U.id_gas WHERE U.userid=:userid LIMIT 1;");
        $stmt->bindParam(':userid', $row["id_proponente"], PDO::PARAM_INT);
        $stmt->execute();
        $utente = $stmt->fetch();

        $stmt = $db->prepare("SELECT D.id_ditte, D.descrizione_ditte,D.indirizzo FROM retegas_ditte D WHERE D.id_ditte=:id_ditta LIMIT 1;");
        $stmt->bindParam(':id_ditta', $row["id_ditte"], PDO::PARAM_INT);
        $stmt->execute();
        $ditta = $stmt->fetch();



        $li.= '<li class="list-group-item '.$hidden.'" >
            <span class="element">
                <span class="pull-right"><span style="cursor:pointer" class="unban_fornitore" data-id="'.$row["id_ditte"].'"><i class="fa fa-times text-danger"></i></span> ripristina</span>
                <strong class="font-sm" style="cursor:pointer" rel="C"><a href="#ajax_rd4/fornitori/scheda.php?id='.$row["id_ditte"].'">'.$row["descrizione_ditte"].'</a></strong> -
                '.$row["indirizzo"].'&nbsp;&nbsp;<small class="font-xs"><i class="fa fa-envelope"></i>&nbsp;<b>'.$row["mail_ditte"].'</b></small><br>
                Inserita da '.$utente["fullname"].' ('.$utente["descrizione_gas"].')
            </span>
        </li>';

    }

    $li .="</ul>";



    $title_navbar='Fornitori esclusi';
    //if(_USER_PERMISSIONS & perm::puo_ges){
        //$buttons[]='<form style="margin-right:10px;"><button  data-id_ditta="0" class="aggiungi_listino btn btn-default btn-success navbar-btn"><i class="fa fa-plus"></i> Nuovo Listino</button></form>';
    //}

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
            <h1>Fornitori esclusi dai messaggi di apertura ordine</h1>
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


        $(document).off('click','.unban_fornitore');
        $(document).on('click','.unban_fornitore',function(e){
            var $t = $(this);
            var id_ditta = $(this).data("id");
            $.ajax({
                          type: "POST",
                          url: "ajax_rd4/fornitori/_act.php",
                          dataType: 'json',
                          data: {act: "fornitore_unbannato",id_ditta:id_ditta},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                ok(data.msg);
                                $t.closest('.list-group-item').fadeOut();
                            }else{
                                ko(data.msg);
                                //$t.closest('.list-group-item').fadeOut();
                            }

                        });


        });

    } // end pagefunction



    pagefunction();
</script>
