<?php
require_once("inc/init.php");

$ui = new SmartUI;
$page_title = "Admin fornitori";
$page_id = "fornitori_admin";

if(!_USER_SUPERVISORE_ANAGRAFICHE){
    
    die(rd4_go_back("Non puoi amministrare le anagrafiche"));
}

if(_USER_PERMISSIONS & perm::puo_creare_ditte){
    $button[] = '<form style="margin-right:10px;"><button  class="aggiungi_ditta btn btn-default btn-success navbar-btn"><i class="fa fa-plus"></i> Nuova ditta</button></form>';
}




?>
<?php echo navbar('Amministrazione fornitori',$button); ?>

<section id="widget-grid" class="margin-top-10">

    <div class="row">
        <!-- PRIMA COLONNA-->
        <div class="well well-sm">
            <div id="table_container">
                <table id="ditte_table"><tr><td></td></tr></table>
                <div id="pager"></div>
            </div>
            
            <!-- ACCORDION -->
            
            <div class="panel-group smart-accordion-default margin-top-10" id="accordion">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" class="collapsed"> <i class="fa fa-lg fa-angle-down pull-right"></i> <i class="fa fa-lg fa-angle-up pull-right"></i> UNIONE DITTE </a></h4>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse" style="height: 0px;">
                        <div class="panel-body">
                                        
                            <!-- UNIONE DITTE -->
                            
                            <form id="merge_ditte" class="smart-form" method="POST" action="_act.php">
                                
                                <fieldset>
                                <div class="row ">
                                    <section class="col col-6 margin-top-10">
                                        <label class="input"> <i class="icon-prepend fa fa-truck"></i>
                                            <input id="ditta_1" type="text" name="ditta_1" placeholder="Prima ditta">
                                        </label>
                                    </section>
                                    <section class="col col-6 margin-top-10">
                                        <label class="input"> <i class="icon-prepend fa fa-truck"></i>
                                            <input id="ditta_2" type="text" name="ditta_2" placeholder="Seconda ditta">
                                            <input type="hidden" name="act" value="merge_ditte">
                                        </label>
                                    </section>

                                    <footer>
                                        <button type="submit" class="btn btn-primary" id="merge_ditte_go">
                                            CONTROLLA
                                        </button>
                                    </footer>
                                </div>
                                </fieldset>
                            </form>
                                        
                            <div id="merge_container"></div>
                            <div id="merge_container_result"></div>
                            
                            <!-- UNIONE DITTE -->
                                            
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" class="collapsed"> <i class="fa fa-lg fa-angle-down pull-right"></i> <i class="fa fa-lg fa-angle-up pull-right"></i> RICERCA DUPLICATI </a></h4>
                    </div>
                    <div id="collapseTwo" class="panel-collapse collapse" style="height: 0px;">
                        <div class="panel-body">
                            <!-- RICERCA DUPLICATI -->
                            <form id="check_ditte" class="smart-form" method="POST" action="_act.php">
                                
                                <fieldset>
                                <div class="row ">
                                    <section class="col col-6 margin-top-10">
                                        <label class="input"> <i class="icon-prepend fa fa-truck"></i>
                                            <input id="trova_simili_ditta" type="text" name="trova_simili_ditta" placeholder="Ditta da controllare">
                                        </label>
                                    </section>
                                    <footer>
                                        <button type="submit" class="btn btn-primary" id="trova_simili_go">
                                            CERCA
                                        </button>
                                    </footer>
                                </div>
                                </fieldset>
                            </form>
                            
                            <div id="trova_simili_box"></div>
                            
                            <!-- RICERCA DUPLICATI -->
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#collapseThree" class="collapsed"> <i class="fa fa-lg fa-angle-down pull-right"></i> <i class="fa fa-lg fa-angle-up pull-right"></i> EDIT </a></h4>
                    </div>
                    <div id="collapseThree" class="panel-collapse collapse" style="">
                        <div class="panel-body">
                        <!-- EDIT -->
                        
                        <div id="box_edit_ditta"></div>
                        
                        <!-- EDIT -->
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- ACCORDION -->
            
        </div>

        
            
        
        
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
        </article>

    </div>

</section>


<script type="text/javascript">
    
    function start_summer(){
        $('.summernote').summernote({
            height : 180,
            focus : false,
            tabsize : 2,
        toolbar: [
                ['style', ['bold', 'italic', 'underline','clear']],
                ['para', ['ul', 'ol', 'paragraph']],
              ]

        });

        $('#save_note').click(function(){
            var id_ditta = $('#id_ditte').attr('rel');
            var aHTML = $('.summernote').code(); //save HTML If you need(aHTML: array).
                      console.log("ID : " + id_ditta);
                      console.log(aHTML);
           $.ajax({
                          type: "POST",
                          url: "ajax_rd4/fornitori/_act.php",
                          dataType: 'json',
                          data: {act: "save_note", id_ditte: id_ditta, note_ditte: aHTML},
                          context: document.body
           }).done(function(data) {
                            if(data.result=="OK"){
                                    ok(data.msg);
                            }else{
                                    ko(data.msg);
                            }
           });

        });

    }
    
    
    
    
    //GEOCODING
    
    
    
    var initialize=function(lat,lng) {
          geocoder = new google.maps.Geocoder();
          var latlng = new google.maps.LatLng(lat,lng);
          var mapOptions = {
            zoom: 10,
            center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
          }
          map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
          console.log("Fine mappa initialized");
        }
        
    function codeAddress(address, save_coord, id_ditte) {
        console.log("Start codeaddress");
        var markers = [];
              geocoder.geocode( { 'address': address}, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {


                  for (var i = 0; i < markers.length; i++) {
                    markers[i].setMap(null);
                  }

                  var marker = new google.maps.Marker({
                      map: map,
                      position: results[0].geometry.location
                  });
                  markers.push(marker);
                  map.setCenter(results[0].geometry.location);

                  for(var i in results[0].address_components){
                        console.log(results[0].address_components[i].short_name);
                  }

                  if(save_coord){

                      $.ajax({
                          type: "POST",
                          url: "ajax_rd4/fornitori/_act.php",
                          dataType: 'json',
                          data: {act: "save_coord", id_ditte: id_ditte, ditte_gc_lat : results[0].geometry.location.lat(),ditte_gc_lng:results[0].geometry.location.lng()},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    ok(data.msg);
                            }else{
                                    ko(data.msg);
                            }
                        });
                  }
                  //$('#ditte_gc_lat').attr('rel',results[0].geometry.location.lat());
                  //$('#ditte_gc_lng').attr('rel',results[0].geometry.location.lng());
                  console.log("Riconosciuto");

                } else {
                  
                  ko("Indirizzo non riconosciuto :(");
                  console.log("Non Riconosciuto");
                }

              });

        }    
    
    //EDIT
    var startEdit = function(){
        
        console.log("startEdit");
        $(".editable").editable({
            url: 'ajax_rd4/fornitori/_act.php',
            ajaxOptions: { dataType: 'json' },
            success: function(response, newValue) {
                console.log(response);
                if(response.result == 'KO'){
                    return response.msg;
                }else{
                    ok(response.msg);
                }
            }
        });

        $(".editable_map").editable({
                url: 'ajax_rd4/fornitori/_act.php',
                ajaxOptions: { dataType: 'json' },
                success: function(response, newValue) {
                    console.log(response);
                    if(response.result == 'KO'){
                        return response.msg;
                    }else{
                    codeAddress(response.msg ,true, $("#id_ditte").html());
                    initialize($('#ditte_gc_lat').attr('rel'),$('#ditte_gc_lng').attr('rel'));
                }
            }
        });
        
        
    }
    

    pageSetUp();

    var pagefunction = function() {
        //-------------------------HELP
        document.title = '<?php echo "ReteDES.it :: $page_title";?>';
        <?php echo help_render_js($page_id); ?>
        //-------------------------HELP

        loadScript("js/plugin/jqgrid/jquery.jqGrid.min.js",startTable);

        var Link= function(id) {

                    var row = id.split("=");
                    var row_ID = row[1];
                    var sitename= $("#users_grid").getCell(row_ID, 'Site_Name');
                    var url = "http://"+sitename;

                    window.open(url);


                }

        function startTable(){



                jQuery("#ditte_table").jqGrid({
                    url: "ajax_rd4/fornitori/fornitori_db.php",
                    datatype: "json",
                    mtype: "POST",
                    postData:{userid:"<?php echo $userid; ?>"},
                    colNames: ["id", "descrizione", "listini", "indirizzo", "telefono", "mail", "tags"],
                    colModel: [

                            {name:'id_ditte',index:'id_ditte', width:10},
                            {name:'descrizione_ditte',index:'descrizione_ditte', width:40},
                            {name:'listini_attivi',index:'listini_attivi', width:10},
                            {name:'indirizzo',index:'indirizzo', width:40},
                            {name:'telefono',index:'telefono', width:20},
                            {name:'mail_ditte',index:'mail_ditte', width:40},
                            {name:'tags',index:'tags', width:40},
                            //{name:'fullname',index:'fullname', width:30},
                            //{name:'descrizione_gas',index:'descrizione_gas', width:30},
                    ],
                    pager: "#pager",
                    autowidth: true,
                    height : 600,
                    rowNum: 50,
                    rowList: [50, 100, 200, 1000],
                    sortname: "D.id_ditte",
                    sortorder: "DESC",
                    viewrecords: true,
                    gridview: true,
                    autoencode: true,
                    caption: "",
                    onSelectRow: function(id){
                        
                        $.ajax({
                          type: "POST",
                          url: "ajax_rd4/fornitori/_act.php",
                          dataType: 'json',
                          data: {act: "show_edit_scheda_ditta", id : id },
                          context: document.body
                       }).done(function(data) {
                           
                           if(data.result=="OK"){
                                $('#box_edit_ditta').html(data.html);
                                
                                //MAPPA
                                initialize($('#ditte_gc_lat').attr('rel'),$('#ditte_gc_lng').attr('rel'));
                                codeAddress($('#indirizzo').html(),false);
                                
                                //EDIT
                                startEdit();
                                loadScript("js/plugin/summernote/summernote.min.js",start_summer);
                                
                            }else{
                                ko(data.msg);
                            }
                       });
                        
                        //$('#id_movimento_singolo').html(id);
                        //$('#id_movimento_singolo_edit').html(id);
                        //$('#importo_edit').val(jQuery("#movimenti_cassa_table").jqGrid ('getCell', id, 'importo'));
                    }
                });
                jQuery("#ditte_table").jqGrid('filterToolbar',{});



                $(window).on('resize.jqGrid', function() {
                    console.log ("resizing ");

                        jQuery("#ditte_table").jqGrid('setGridWidth', $("#table_container").width());

                });
                // remove classes
                $(".ui-jqgrid").removeClass("ui-widget ui-widget-content");
                $(".ui-jqgrid-view").children().removeClass("ui-widget-header ui-state-default");
                $(".ui-jqgrid-labels, .ui-search-toolbar").children().removeClass("ui-state-default ui-th-column ui-th-ltr");
                $(".ui-jqgrid-pager").removeClass("ui-state-default");
                $(".ui-jqgrid").removeClass("ui-widget-content");

                // add classes
                $(".ui-jqgrid-htable").addClass("table table-bordered table-hover");
                $(".ui-jqgrid-btable").addClass("table table-bordered table-striped");



        }//END STARTTABLE

        

        $(document).off('click','#merge_ditte_go');
        $(document).on('click','#merge_ditte_go',function(e){
            $('#merge_container_result').html('-');
            $.blockUI();
            e.preventDefault();
            var ditta_1=$('#ditta_1').val();
            var ditta_2=$('#ditta_2').val();
            console.log(ditta_1 + " <--> " + ditta_2);
            $.ajax({
              type: "POST",
              url: "ajax_rd4/fornitori/_act.php",
              dataType: 'json',
              data: {act: "merge_ditte_visiona", ditta_1 : ditta_1, ditta_2:ditta_2},
              context: document.body
           }).done(function(data) {
               $.unblockUI();
               if(data.result=="OK"){
                    $('#merge_container').html(data.html);
                    startEdit();
                    
                    
                    $(document).off('click','.merge_go');
                    $(document).on('click','.merge_go',function(e){
                        e.preventDefault();
                        $.blockUI();
                        var ditta_1=$(this).data('ditta_1');
                        var ditta_2=$(this).data('ditta_2');
                        console.log(ditta_1 + " <-*-> " + ditta_2);

                        $.ajax({
                          type: "POST",
                          url: "ajax_rd4/fornitori/_act.php",
                          dataType: 'json',
                          data: {act: "merge_ditte_effettua", ditta_1 : ditta_1, ditta_2:ditta_2},
                          context: document.body
                       }).done(function(data) {
                           $.unblockUI(); 
                           if(data.result=="OK"){
                                $('#merge_container').html('-');
                                $('#merge_container_result').html(data.msg);
                                
                            }else{
                                ko(data.msg);
                            }
                       });
                    });



            }else{ko(data.msg);}
           });


        });

        
        //CHECK DITTE
        $(document).off('click','#check_ditte_go');
        $(document).on('click','#check_ditte_go',function(e){
            $('#check_container_result').html('-');
            $.blockUI();
            e.preventDefault();
            var ditta_1=$('#check_ditta_1').val();
            console.log(ditta_1);
            $.ajax({
              type: "POST",
              url: "ajax_rd4/fornitori/_act.php",
              dataType: 'json',
              data: {act: "check_ditte_visiona", ditta_1 : ditta_1},
              context: document.body
           }).done(function(data) {
               $.unblockUI();
               if(data.result=="OK"){
                    $('#check_container').html(data.html);
                    

            }else{ko(data.msg);}
           });


        });
        
        //TROVA SIMILI
        $(document).off('click','#trova_simili_go');
        $(document).on('click','#trova_simili_go',function(e){
            $('#trova_simili_box').html('-');
            $.blockUI();
            e.preventDefault();
            var ditta_1=$('#trova_simili_ditta').val();
            console.log(ditta_1);
            $.ajax({
              type: "POST",
              url: "ajax_rd4/fornitori/_act.php",
              dataType: 'json',
              data: {act: "show_scheda_paragone_ditte", id : ditta_1},
              context: document.body
           }).done(function(data) {
               $.unblockUI();
               if(data.result=="OK"){
                    $('#trova_simili_box').html(data.html);

            }else{ko(data.msg);}
           });


        });
        
        
    };

    // end pagefunction

    // run pagefunction on load

        
        $(window).unbind('gMapsLoaded');
        $(window).bind('gMapsLoaded',loadScript("js/plugin/jqgrid/grid.locale-en.min.js", loadScript("js/plugin/x-editable/x-editable.min.js", pagefunction)));
        window.loadGoogleMaps();


</script>
