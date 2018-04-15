<?php
require_once("inc/init.php");
$ui = new SmartUI;
$converter = new Encryption;

$page_title = "Listini del mio GAS";
$page_id = "gas_listini";



$stmt = $db->prepare("SELECT * FROM retegas_listini inner join maaking_users on userid=id_utenti WHERE id_gas='"._USER_ID_GAS."' AND is_deleted=0 ORDER BY data_valido DESC");

    $stmt->execute();
    $rows = $stmt->fetchAll();


    $li = '<ul class="list-group" style="max-height:600px; overflow-y:auto;" id="list">';
    foreach($rows as $row){

        $stmt = $db->prepare("SELECT U.fullname,G.descrizione_gas,G.id_gas FROM maaking_users U inner join retegas_gas G on G.id_gas=U.id_gas WHERE U.userid=:userid LIMIT 1;");
        $stmt->bindParam(':userid', $row["id_utenti"], PDO::PARAM_INT);
        $stmt->execute();
        $utente = $stmt->fetch();

        $stmt = $db->prepare("SELECT D.id_ditte, D.descrizione_ditte,D.indirizzo FROM retegas_ditte D WHERE D.id_ditte=:id_ditta LIMIT 1;");
        $stmt->bindParam(':id_ditta', $row["id_ditte"], PDO::PARAM_INT);
        $stmt->execute();
        $ditta = $stmt->fetch();

        $stmt = $db->prepare("SELECT count(*) as conto FROM retegas_articoli WHERE id_listini=:id_listini");
        $stmt->bindParam(':id_listini', $row["id_listini"], PDO::PARAM_INT);
        $stmt->execute();
        $articoli = $stmt->fetch();


        //if(strtotime($row["data_valido"])<strtotime(date("Y-m-d H:i:s"))){
        //    $class_scaduto=" bg-color-redLight txt-color-white";
        //    $scade = 'Scaduto dal '.conv_datetime_from_db($row["data_valido"]);
        //    $icona_scade='<i class="pull-right fa fa-warning fa-2x txt-color-red"></i>';
        //    $hidden=' hidden ';
        //}else{
        //    $class_scaduto ="";
        //    //$scade = "Scadrà il ".conv_datetime_from_db($row["data_valido"]);
        //    $scade='Scadrà il '.conv_datetime_from_db($row["data_valido"]);
        //    $icona_scade='<i class="pull-right fa fa-check-square-o fa-2x txt-color-green"></i>';
        //    $hidden='';
        //}
        
        if(strtotime($row["data_valido"])<strtotime(date("Y-m-d H:i:s"))){
            $class_scaduto="scaduto";
            $style_scaduto=' style="display:none;" ';
            $n_scaduto++;
            $scade = 'Scaduto dal '.conv_datetime_from_db($row["data_valido"]);
            $icona_scade='<i class="pull-right fa fa-warning fa-2x txt-color-red"></i>';
        }else{
            $class_scaduto ="";
            $style_scaduto="";
            //$scade = "Scadrà il ".conv_datetime_from_db($row["data_valido"]);
            $scade='Scadrà il '.conv_datetime_from_db($row["data_valido"]);
            $icona_scade='<i class="pull-right fa fa-check-square-o fa-2x txt-color-green"></i>';
        }

        if($row["tipo_listino"]==1){
            $icona_scade='<i class="pull-right fa fa-archive fa-2x txt-color-blue"></i>';
            $magazzino = " LISTINO MAGAZZINO";
            $scade="";
            $hidden='';
        }else{
            $magazzino = "";
        }

        if(_USER_PERMISSIONS & perm::puo_creare_listini){
            if($class_scaduto<>"scaduto"){
                $aiuta_a_gestire = '<button class="aiuta_a_gestire_listino btn btn-success btn-link btn-xs pull-right" data-id="'.$row["id_listini"].'">Aiuta a gestire</button>';
            }else{
                $aiuta_a_gestire = '';
            }   
            
        }else{
            $aiuta_a_gestire = '';
        }


        if(($row["is_privato"]==1) AND ($utente["id_gas"]<>_USER_ID_GAS)){
            $li.= '<li class="list-group-item" >
                    <i class="pull-right fa fa-eye-slash fa-2x txt-color-grey"></i>
                    <h3 class="txt-color-grey">Listino privato '.$utente["descrizione_gas"].'</h3>
                    </li>';
        }else{
            if($row[is_multiditta]>0){
                $multiditta='<i class="fa fa-truck text-success"></i>&nbsp;<i class="fa fa-truck text-danger"></i>&nbsp;<i class="fa fa-truck text-warning"></i>&nbsp;MULTIDITTA';
            }else{
                $multiditta='<a href="javascript:void(0);"><i class="fa fa-truck"></a></i>&nbsp;'.$ditta["descrizione_ditte"].'';
            }


            $li.= '<li class="list-group-item '.$class_scaduto.'" '.$style_scaduto.' >
                        <span>
                            '.$utente["fullname"].':
                            '.$icona_scade.'
                            <strong class="font-sm" style="cursor:pointer" rel="C"><a href="#ajax_rd4/listini/listino.php?id='.$row["id_listini"].'">'.$row["descrizione_listini"].'</a></strong> '.(conv_date_from_db($row["data_creazione"]) <> "00/00/0000" ? "del ".conv_date_from_db($row["data_creazione"]) : "").'<br>
                            '.$multiditta.'&nbsp;&nbsp;<small class="font-xs"><i class="fa fa-cubes"></i>&nbsp;<b>'.$articoli["conto"].'</b></small><br>
                            '.$magazzino.'
                            '.$scade.'
                            '.$aiuta_a_gestire.'
                        </span>
                    </li>';
        }
    }

    $li .="</ul>";



    $title_navbar='Listini del mio GAS';
    if(_USER_PERMISSIONS & perm::puo_creare_listini){
        //$buttons[]='<button  data-id_ditta="0" class="aggiungi_listino btn btn-link"><i class="fa fa-plus"></i> Nuovo Listino</button>';
    }

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
            <form class="smart-form">
                <section>
                    <label class="input"> <i class="icon-append fa fa-filter"></i>
                        <input type="text" placeholder="filtra tra i listini..." id="listfilter">
                    </label>
                </section>
            </form>
            <?php if($n_scaduto>0){ ?>
                <div>
                    <button class="pull-right btn btn-xs btn-default" id="mostra_scaduti">Mostra anche quelli scaduti <strong>(<?php echo $n_scaduto;?>)</strong></button>
                </div>
                <div class="clearfix margin-bottom-10"></div>
                <?php }?>
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

        $(document).off('click','#mostra_scaduti');
        $(document).on('click','#mostra_scaduti',function(e){

            e.preventDefault();
            $('.scaduto').show();

        });
        
        
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
                  $(list).find("span:not(:Contains(" + filter + "))").parent().hide();
                  $(list).find("span:Contains(" + filter + ")").parent().show();
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



    $(document).on('click','#show_listini_hidden',function(){
        $('li.hidden').removeClass('hidden');
    })

    $('.aiuta_a_gestire_listino').click(function(e){

        var id_l = $(this).data("id");

        $.SmartMessageBox({
                title : "Vuoi aiutare a gestire questo listino ?",
                content : "Scrivi un breve messaggio che verrà inviato all\'autore di questo listino, e se lo vorrà ti inserirà tra i suoi gestori.",
                buttons : "[Esci][Invia]",
                input : "text",
                placeholder : "msg",
                inputValue: '',
            }, function(ButtonPress, Value) {

                if(ButtonPress=="Invia"){
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/listini/_act.php",
                          dataType: 'json',
                          data: {act: "aiuta_a_gestire", value : Value, id:id_l},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    ok(data.msg);
                            }else{
                                ko(data.msg)
                            ;}

                        });
                }
            });

            e.preventDefault();
        })


    } // end pagefunction



    pagefunction();
</script>
