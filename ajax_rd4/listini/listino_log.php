<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.listino.php");

$ui = new SmartUI;
$converter = new Encryption;

$page_title = "Log Listino";
$page_id = "log_listino";

//CONTROLLI
$id_listino = (int)$_GET["id"];
$L = new listino($id_listino);

if (!posso_gestire_listino($id_listino)){
    echo rd4_go_back("Non ho i permessi necessari");die;
}

$fh = fopen(dirname(__FILE__).'/../../public_rd4/logs/listini/'.$id_listino."/log.txt",'r');
$ha = array();
while ($line = fgets($fh)) {
   $ha[] = $line;
}
fclose($fh);
arsort($ha);

foreach($ha as $row){
     $h.=$row."<br>";
}


?>

<?php echo $L->navbar_listino();?>
<h1>Log listino</h1>
<button  onclick="selectElementContents( document.getElementById('log_box') ); " class="btn btn-default btn-default pull-right"><i class="fa fa-copy"></i>  SELEZIONA TUTTO IL LOG</button>
<div class="clearfix"></div>
<br>
<div class="well well-light margin-top-10" id="log_box" style="max-height:320px; overflow-y:auto; background-color: #000; color:green; font-family: monospace;">
   <?php echo $h ?>
</div>

<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html($page_id,$page_title); ?>
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
