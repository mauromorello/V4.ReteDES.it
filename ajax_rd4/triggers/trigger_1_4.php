<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.ordine.php");

$ui = new SmartUI;

$tipo=1;
$sottotipo=4;

$page_title = "Trigger $tipo.$sottotipo";
$page_id="trigger_".$tipo."_".$sottotipo;


//PERMESSI
if(!_USER_PERMISSIONS & perm::puo_vedere_tutti_ordini){
    echo rd4_go_back("Non hai abbastanza permessi, qua ce ne voglio un sacco.");
}

$id_ordine = CAST_TO_INT($_GET["id_ordine"],0);
if($id_ordine>0){
    $O = new ordine($id_ordine);
    $descrizione_ordini = $O->descrizione_ordini;
    
}

$id_owner=_USER_ID;

$sql = "SELECT * from retegas_triggers WHERE id_owner=:id_owner AND tipo='$tipo' AND sottotipo='$sottotipo';";
$stmt = $db->prepare($sql);
$stmt->bindParam(':id_owner', $id_owner, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);    
foreach ($rows as $row){
    
if(conv_datetime_from_db($row["scattato_il"])=="// 00:00"){
    $scattato="non ancora";
    $scattato_class = "text-success";
}else{
    $scattato=conv_datetime_from_db($row["scattato_il"]);
    $scattato_class = "text-danger";
}    
    $t .='<tr>';    
        //$t.='<td>'.$row["id_trigger"].'</td>';
        //$t.='<td>'.$row["name"].'</td>';
        $t.='<td>'.$row["id_ordine"].'</td>';
        $t.='<td>'.$row["valore"].'</td>';
        $t.='<td>'.user_fullname($row["id_utente"]).'</td>';
        $t.='<td>'.substr(strip_tags($row["testo_azione"]),0,20).'...</td>';
        $t.='<td><span class="'.$scattato_class.'">'.$scattato.'</span></td>';
        $t.='<td><i class="fa fa-times text-danger delete_trigger" data-id="'.$row["id_trigger"].'" style="cursor:pointer"></i></td>';
   $t.='</tr>';     
}
?>
                        <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.1/summernote.css" rel="stylesheet">
                        
                        <h1>TRIGGER <?php echo $tipo.".".$sottotipo;?></h1>
                        
                        <div class="row padding-10">
                            
                            <div class="well well-sm">
                            <form id="trigger_<?php echo $tipo."_".$sottotipo;?>" action="ajax_rd4/triggers/_act.php" novalidate="novalidate" method="POST" class="smart-form ">
                                <header>
                                    <strong>Funzione: </strong>Messaggio mail al raggiungimento di un certo numero di partecipanti;<br>
                                    <strong>Ripetibilità: </strong>Questo trigger scatta una sola volta.
                                </header>
                                <fieldset class="col col-md-6">
                                    <!--<section>
                                        <label class="label">1) Nome trigger</label>
                                        <label class="input" >
                                            <input type="text" id="name" name="name">
                                        </label>
                                        <div class="note">
                                        <strong>NB:</strong> Serve per ricordarselo   
                                        </div>
                                    </section>-->
                                    
                                    <?php if($id_ordine==0){?>
                                    <section>
                                        <label class="label">1) Quando su questo ordine:</label>
                                        <label class="input" >
                                            <input class="hidden" id="id_ordine" name="id_ordine" type="text" value="0">
                                            <div id="listaOrdini" style="width:100%;" class="" ></div>
                                        </label>
                                        <div class="note">
                                        <strong>NB:</strong> Inserire l'ID ordine    
                                        </div>
                                    </section>
                                    <?php }else{ ?>
                                    <section>
                                        <label class="label">1) Quando sull'ordine</label><br>
                                        <input class="hidden" id="id_ordine" name="id_ordine" type="text" value="<?php echo $id_ordine;?>">
                                        <p class="font-lg">
                                        #<?php echo $id_ordine." ".$descrizione_ordini; ?> 
                                        </p>
                                    </section>
                                    <?php }?>
                                    <section>
                                        <label class="label">2) Ci sono più di questi partecipanti:</label>
                                        <label class="input">
                                            <input type="text" maxlength="10" id="valore" name="valore">
                                        </label>
                                        <div class="note">
                                           
                                        </div>
                                    </section>
                                    <section>
                                        <label class="label">3) Manda a questo utente:</label>
                                        <label class="input">
                                            <input class="hidden" id="idUtente" name="idUtente" type="text" value="0">    
                                            <div id="listaUtenti" style="width:100%" class="" ></div>
                                        </label>
                                        <div class="note">
                                        <strong>NB:</strong> se si hanno i permessi si vedranno anche gli utenti del proprio DES, altrimenti solo quelli del proprio GAS. Se non si indica nulla il messaggio arriverà a sé stessi.   
                                        </div>
                                    </section>
                                </fieldset>
                                <fieldset class="col col-md-6">
                                    
                                    <section>
                                        <label class="label">4) Il seguente messaggio:</label>
                                        <label class="textarea">                                         
                                            <textarea rows="4" class="custom-scroll" id="summernote_trigger_<?php echo $tipo."_".$sottotipo;?>"></textarea> 
                                        </label>
                                        <div class="note">
                                        <strong>NB:</strong> se si lascia vuoto il messaggio si riceverà un avviso generico.   
                                        </div>
                                    </section>
                                </fieldset>
                            <footer>
                                <button type="submit" class="btn btn-primary">
                                    Inserisci
                                </button>
                            </footer>
                        </form>
                        </div>
                        
                        </div>
                        <div class="col col-md-12">
                        <h3>Triggers di questo tipo attivi</h3>
                            <div class="table-responsive" style="overflow-x:auto">
                                <table id="tabella_trigger_<?php echo $tipo."_".$sottotipo;?>">
                                    <thead>
                                        <tr>
                                            <!--<th>nome</th>-->
                                            <th>ordine</th>
                                            <th>soglia</th>
                                            <th>target</th>
                                            <th>messaggio</th>
                                            <th>scattato il</th>
                                            <th data-filter="false"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php echo $t; ?>
                                    
                                    </tbody>
                                
                                </table>
                            </div>
                        </div>
                        

<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
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
    
    loadScript("js_rd4/plugin/tablesorter/js/jquery.tablesorter.widgets.js",startTable);
        function startTable(){    
                $.extend($.tablesorter.themes.bootstrap, {
                    table      : 'table table-bordered',
                    caption    : 'caption',
                    sortNone   : 'bootstrap-icon-unsorted',
                    sortAsc    : 'fa fa-arrow-up',
                    sortDesc   : 'fa fa-arrow-down'

                  });

                var $table = $('#tabella_trigger_<?php echo $tipo."_".$sottotipo;?>').tablesorter({
                    theme: 'bootstrap',
                        //debug:true,
                        widgets: ["uitheme","filter","zebra"],
                        widgetOptions : {
                            zebra : ["even", "odd"],
                            filter_reset : ".reset",
                            filter_columnFilters: true
                        }
                });
        }        
    
    //DELETE TRIGGER
    $(document).off('click','.delete_trigger');
        $(document).on('click','.delete_trigger',function(e){
            var $t = $(this);
            var id_trigger = $(this).data("id");
            $.ajax({
                          type: "POST",
                          url: "ajax_rd4/triggers/_act.php",
                          dataType: 'json',
                          data: {act: "delete_trigger",id_trigger:id_trigger},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                ok(data.msg);
                                $t.closest('tr').fadeOut();
                            }else{
                                ko(data.msg);
                                //$t.closest('.list-group-item').fadeOut();
                            }

                        });


        });
    //DELETE TRIGGER
    
    //LISTA ORDINI-------------------------------------------------------------------------
    $("#listaOrdini").select2({
            placeholder: "Cerca tra gli ordini aperti o futuri...",
                //minimumInputLength: 3,
                ajax: {
                url: "ajax_rd4/triggers/inc/listaordini.php",
                dataType: 'json',
                data: function (term, page) {
                    return {
                        q: term
                    };
                },
                results: function (data, page) {
                    return { results: data };
                }

            },
            formatResult: function(data){
                return '<span>#'+data.id+'</span><span class="note"> '+data.descrizione_ditte+'</span><br><strong>'+data.text+'</strong>' ;
            },
            escapeMarkup: function(m) { return m; }
    });

    $('#listaOrdini').on("select2-selecting",
    function(e) {
            console.log(e.val);
            $('#id_ordine').val(e.val);
    });
    
    //LISTA ORDINI ---------------------------------------------------
    
    
    //LISTA UTENTI ---------------------------------------------------
    $("#listaUtenti").select2({
            placeholder: "Cerca tra gli utenti..",
                //minimumInputLength: 3,
                ajax: {
                url: "ajax_rd4/triggers/inc/listautenti.php",
                dataType: 'json',
                data: function (term, page) {
                    return {
                        q: term
                    };
                },
                results: function (data, page) {
                    return { results: data };
                }

            },
            formatResult: function(data){
                return '<span>'+data.descrizione_gas+'</span><br><strong>'+data.text+'</strong>' ;
            },
            escapeMarkup: function(m) { return m; }
    });

    $('#listaUtenti').on("select2-selecting",
    function(e) {
            console.log(e.val);
            $('#idUtente').val(e.val);
    });
    
    //LISTA UTENTI---------------------------------------------------------
    
    //SUMMERNOTE-----------------------------------------------------------
    
    $('#summernote_trigger_<?php echo $tipo."_".$sottotipo;?>').summernote({
            height : 180,
            focus : false,
            tabsize : 2,
            toolbar: [
                //[groupname, [button list]]
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
               // ['insert', ['link', 'picture']]
              ]
    });
    
    //SUMMERNOTE----------------------------------------------------------    
    
    
    //FORM----------------------------------------------------------------    
    var $trigger_<?php echo $tipo."_".$sottotipo;?> = $('#trigger_<?php echo $tipo."_".$sottotipo;?>').validate({
        
            rules : {id_ordine:{required : true},valore:{required : true}},
            messages : {id_ordine : {required : 'Per cortesia inserisci qualcosa...'},valore:{required : 'non scherziamo, dai....'}},
            submitHandler : function(form) {
                
                $(form).ajaxSubmit({
                    type:"POST",
                    dataType: 'json',
                    data: {id_ordine:$('#id_ordine').val(),id_target:$('#idUtente').val(), messaggio: $('#summernote_trigger_<?php echo $tipo."_".$sottotipo;?>').summernote('code'), act:'save_trigger_<?php echo $tipo."_".$sottotipo;?>'},
                    success : function(data) {
                            if(data.result=="OK"){
                                okReload(data.msg);
                            }else{
                                ko(data.msg);}
                            }
                });
            },
            errorPlacement : function(error, element) {
                error.insertAfter(element.parent());
            }
        });
        //FORM-----------------------------------------------------------
    }
    // end pagefunction

    function loadSummerNote(){
            loadScript("js/plugin/summernote/new_summernote.min.js", pagefunction)
    }
    
    loadScript("js_rd4/plugin/tablesorter/js/jquery.tablesorter.min.js",
            loadScript("js/plugin/jquery-form/jquery-form.min.js", loadSummerNote)
    );



</script>
