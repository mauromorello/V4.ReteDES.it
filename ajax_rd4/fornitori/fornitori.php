<?php
require_once("inc/init.php");

$ui = new SmartUI;
$page_title = "Tutti i fornitori";
$page_id = "fornitori";


?>
<?php echo navbar('Tutte le  ditte',$button); ?>

<section id="widget-grid" class="margin-top-10">

    <div class="row">
        <!-- PRIMA COLONNA-->
        <div class="well well-sm">
            <div id="table_container">
                <table id="ditte_table"><tr><td></td></tr></table>
                <div id="pager"></div>
            </div>
        </div>
        <?php if((_USER_PERMISSIONS & perm::puo_gestire_retegas) OR (_USER_PERMISSIONS & perm::puo_vedere_retegas)){ ?>

        <div class="well well-sm padding-10">
            <form id="merge_ditte" class="smart-form" method="POST" action="_act.php">
                <h3>Unione ditte</h3>
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
                            Vedi
                        </button>
                    </footer>
                </div>
                </fieldset>
            </form>
        </div>
        <div id="merge_container"></div>
        <div id="merge_container_result"></div>
        <?php } ?>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
        </article>

    </div>

</section>


<script type="text/javascript">

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
                            {name:'descrizione_ditte',index:'descrizione_ditte', width:40,formatter: 'showlink', formatoptions: { baseLinkUrl: '<?php echo APP_URL; ?>/#ajax_rd4/fornitori/scheda.php'}},
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
                        //$('#id_movimento_singolo').html(id);
                        //$('#id_movimento_singolo_edit').html(id);
                        //$('#importo_edit').val(jQuery("#movimenti_cassa_table").jqGrid ('getCell', id, 'importo'));
                    }
                });
                jQuery("#ditte_table").jqGrid('filterToolbar',{});

                //jQuery("#movimenti_cassa_table").jqGrid('navButtonAdd','#pager',{
                //       caption:"ESPORTA",
                //       onClickButton : function () {
                          // jQuery("#movimenti_cassa_table").jqGrid('excelExport',{"url":"ajax_rd4/cassa/movimenti_db.php"});
                //       }
                //});

                $(window).on('resize.jqGrid', function() {
                    console.log ("resizing ");

                        jQuery("#ditte_table").jqGrid('setGridWidth', $("#table_container").width());
                        //jQuery("#movimenti_cassa_table").jqGrid('setGridHeight', $("#table_container").height() - ($("#gbox_jqgrid").height() - $('#gbox_jqgrid .ui-jqgrid-bdiv').height()));

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

        $('.show_csv').click(function(){
            jQuery("#movimenti_cassa_table").jqGrid('excelExport',{"url":"ajax_rd4/cassa/movimenti_db.php"});
            return false;
        });

        $('.show_pdf').click(function(){
            var $this = $(this);
            var id = $this.data('id_ordine');
            open('POST', '<?php echo APP_URL; ?>/ajax_rd4/cassa/movimenti.php', {id:id, o:'pdf', dummy:<?php echo rand(1000,9999); ?> }, '_blank');
            return false;
        });

        $(document).off('click','#merge_ditte_go');
        $(document).on('click','#merge_ditte_go',function(e){
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
              if(data.result=="OK"){
                    $('#merge_container').html(data.html);
                    $(document).off('click','.merge_go');
                    $(document).on('click','.merge_go',function(e){
                        e.preventDefault();

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
                            if(data.result=="OK"){
                                $('#merge_container_result').html(data.msg);
                            }else{
                                ko(data.msg);
                            }
                       });
                    });



            }else{ko(data.msg);}
           });


        });

    };

    // end pagefunction

    // run pagefunction on load

        loadScript("js/plugin/jqgrid/grid.locale-en.min.js", pagefunction);



</script>
