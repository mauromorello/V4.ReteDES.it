<?php require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.ordine.php");
require_once("../../lib_rd4/class.rd4.user.php");



$ui = new SmartUI;
$page_title= "Calendarizza";
$page_id ="ordine_calendarizza";

//CONTROLLI
$id_ordine = (int)$_GET["id"];

if (!posso_gestire_ordine($id_ordine)){
    echo "Non ho i permessi per gestire questo ordine";
    die();
}


$O = new ordine($id_ordine);

?>

<?php echo $O->navbar_ordine(); ?>

    <h3>Calendarizzazione ordine</h3>


    <div class="row">
        <div class="col col-sm-12 col-md-6">
            <p><strong>PASSO 1:</strong> Clicca sulle date del calendario e crea una lista di ordini calendarizzati che si apriranno alla data scelta.</p>
            <div class="well well-sm">
                <div id="my-calendar"></div>
            </div>
        </div>
        <div class="col col-sm-12 col-md-6" >
            <p><strong>PASSO 2:</strong> Se vuoi, cambia il nome ai nuovi ordini.</p>
            <div id="container_lista_calendarizzazione">
                <p>Nulla di calendarizzato...</p>
            </div>
        </div>
    </div>

    <hr>
    <p><strong>PASSO 3:</strong> Trasforma la lista che hai creato in ordini programmati.</p>


    <div class="text-center">
        <button class="margin-top-10 btn btn-success btn-xl" id="start_calendarizzazione"><i class="fa fa-calendar-o"></i>&nbsp; Programma la calendarizzazione!</button>
    </div>
    <hr>
 <section id="widget-grid" class="margin-top-10">
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

        function carica_lista_calendarizzazione(){
            $.ajax({
                  type: "POST",
                  url: "ajax_rd4/ordini/_act_calendarizza.php",
                  dataType: 'json',
                  data: {act: "show_lista_calendarizzazione", id_ordine:<?php echo $id_ordine; ?>},
                  context: document.body
                }).done(function(data) {
                    $('#container_lista_calendarizzazione').html(data.html);
                    $('.editable_descrizione_calendarizzazione').editable({
                        url: 'ajax_rd4/ordini/_act_calendarizza.php',
                        type: 'text',
                        name: 'edit_descrizione_calendarizzazione',
                        title: 'Inserisci un nuovo nome ordine',
                                ajaxOptions: {
                                    dataType: 'json'
                                },
                                success: function(data, newValue) {
                                    if(data.result=="OK") {
                                         return;
                                    }else{
                                         return data.msg;
                                    }
                                }
                    });
                });
            }
        function toggle_calendarizzazione(date){
            $.ajax({
                  type: "POST",
                  url: "ajax_rd4/ordini/_act_calendarizza.php",
                  dataType: 'json',
                  data: {act: "toggle_calendarizzazione", date:date ,id_ordine:<?php echo $O->id_ordini; ?>},
                  context: document.body
                }).done(function(data) {
                    if(data.result=="OK"){
                        ok(data.msg);
                        carica_lista_calendarizzazione();
                    }else{
                        ko(data.msg);
                    }
                });
        }
        function myDateFunction(id, fromModal) {

            var date = $("#" + id).data("date");
            console.log("zabuto id: "+ id);
            console.log("zabuto date: "+ date);
            toggle_calendarizzazione(date);
            //$('#'+id+'_day').toggleClass("badge bg-color-info");
        };

        //-------------------------HELP
        <?php echo help_render_js($page_id); ?>
        //-------------------------HELP


        $("#my-calendar").zabuto_calendar({
                language: "it",
                show_previous: false,
                action: function () {
                    return myDateFunction(this.id, false);
                }
        });
        carica_lista_calendarizzazione();

        $(document).off("click",".delete_calendarizzato");
        $(document).on("click",".delete_calendarizzato",function(){
            var id_option=$(this).data("id_option");
            var $t=$(this);
            $t.removeClass("fa-times").addClass("fa-spin");
            console.log("delete id: " + id_option);
            $.ajax({
                  type: "POST",
                  url: "ajax_rd4/ordini/_act_calendarizza.php",
                  dataType: 'json',
                  data: {act: "delete_calendarizzazione",id_option:id_option},
                  context: document.body
                }).done(function(data) {
                    if(data.result=="OK"){
                        ok(data.msg);
                        $t.parents('li').fadeOut();
                        //carica_lista_calendarizzazione();

                    }else{
                        ko(data.msg);
                        $t.removeClass("fa-spin").addClass("fa-times");
                    }
                });
        })

        $(document).off("click","#start_calendarizzazione");
        $(document).on("click","#start_calendarizzazione",function(e){
            $.blockUI();
            $.ajax({
                  type: "POST",
                  url: "ajax_rd4/ordini/_act_calendarizza.php",
                  dataType: 'json',
                  data: {act: "do_calendarizzazione",id_ordine:<?php echo $O->id_ordini; ?>},
                  context: document.body
                }).done(function(data) {
                    console.log(data.log);
                    $.unblockUI();
                    if(data.result=="OK"){
                        console.log(data.log);
                        ok(data.msg);
                        //$('#container_lista_calendarizzazione').html(data.html);
                        carica_lista_calendarizzazione();
                    }else{
                        ko(data.msg);
                    }
                }).fail(function(){
                    $.unblockUI();
                });


        });

    }
    // end pagefunction
    loadScript("js/plugin/x-editable/x-editable.min.js",
        loadScript("js_rd4/plugin/zabuto/zabuto_calendar.min.js", pagefunction)
    );


</script>
