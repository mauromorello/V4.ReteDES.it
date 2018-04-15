<?php
require_once("inc/init.php");

$ui = new SmartUI;
$page_title = "Migliora V4!";
$page_id = "miglioraV4";

?>
<div class="inbox-nav-bar no-content-padding">
    <h1 class="page-title txt-color-blueDark"><i class="fa fa-fw fa-rocket"></i> Miglioriamo V4!</h1>
</div>
<form class="smart-form margin-top-10 well well-light">
    <label class="label">Scrivi qua un tuo suggerimento. Rimarrà in questa pagina come promemoria :)</label>
    <label class="textarea">
        <textarea rows="3" class="custom-scroll" name="suggerimento" id="suggerimento"></textarea>
    </label>
    <div class="note">
        <strong>Note:</strong> Verrà indicato il tuo nome e il GAS al quale appartieni.
    </div>
    <footer>
        <button type="submit" class="btn btn-primary" id="salva_suggerimento">
            Salva il tuo suggerimento.
        </button>
    </footer>
</form>

<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title,true); ?>
        </article>
    </div>
</section>
<div class="row">
    <div id="box_suggerimenti" class="col-xs-6 col-sm-6 col-md-6 col-lg-6"></div>
    <div id="box_suggerimenti_vecchi" class="col-xs-6 col-sm-6 col-md-6 col-lg-6"></div>
</div>
<script type="text/javascript">
    pageSetUp();

    var pagefunction = function(){

        function updateSuggerimenti(){
            $.ajax({
                  type: 'POST',
                  url: 'ajax_rd4/_act_main.php',
                  dataType: 'json',
                  data: {act: 'update_suggerimenti'},
                  context: document.body
                }).done(function(data) {
                    if(data.result=='OK'){
                        $('#box_suggerimenti').html(data.html);
                    }else{
                        ko(data.msg)
                    ;}

                });

        }
        function updateSuggerimentiVecchi(){
            $.ajax({
                  type: 'POST',
                  url: 'ajax_rd4/_act_main.php',
                  dataType: 'json',
                  data: {act: 'update_suggerimenti_vecchi'},
                  context: document.body
                }).done(function(data) {
                    if(data.result=='OK'){
                        $('#box_suggerimenti_vecchi').html(data.html);
                    }else{
                        ko(data.msg)
                    ;}

                });

        }


        //------------HELP WIDGET
        <?php echo help_render_js($page_id);?>
        //------------END HELP WIDGET

        $('#salva_suggerimento').click(function(e){
            e.preventDefault();
            console.log("salva suggerimento");
            var sHTML = $('#suggerimento').val();

            $.ajax({
                  type: 'POST',
                  url: 'ajax_rd4/_act_main.php',
                  dataType: 'json',
                  data: {act: 'salva_suggerimento', sHTML : sHTML},
                  context: document.body
                }).done(function(data) {
                    
                    if(data.result=='OK'){
                        $("#suggerimento").val('');
                        ok('Modifiche salvate!')
                        updateSuggerimenti();
                    }else{
                        ko(data.msg)
                    ;}

                });



        });

        $(document).off('click','.delete_suggerimento_totale');
        $(document).on('click','.delete_suggerimento_totale', function(e){
            id_suggerimento = $(this).data('id');
            console.log("delete suggerimento totale" + id_suggerimento);
            $.ajax({
                  type: 'POST',
                  url: 'ajax_rd4/_act_main.php',
                  dataType: 'json',
                  data: {act: 'delete_suggerimento_totale', id_option : id_suggerimento},
                  context: document.body
                }).done(function(data) {
                    if(data.result=='OK'){
                        ok('Suggerimento eliminato!')
                        updateSuggerimenti();
                        updateSuggerimentiVecchi();
                    }else{
                        ko(data.msg)
                    ;}

                });
        })

        $(document).off('click','.delete_suggerimento');
        $(document).on('click','.delete_suggerimento', function(e){
            id_suggerimento = $(this).data('id');
            console.log("delete suggerimento" + id_suggerimento);
            $.ajax({
                  type: 'POST',
                  url: 'ajax_rd4/_act_main.php',
                  dataType: 'json',
                  data: {act: 'delete_suggerimento', id_option : id_suggerimento},
                  context: document.body
                }).done(function(data) {
                    if(data.result=='OK'){
                        ok('Suggerimento eliminato!')
                        updateSuggerimenti();
                        updateSuggerimentiVecchi();
                    }else{
                        ko(data.msg)
                    ;}

                });
        })
        updateSuggerimenti();
        updateSuggerimentiVecchi();
    };

    pagefunction();

</script>
