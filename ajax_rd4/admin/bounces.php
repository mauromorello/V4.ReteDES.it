<?php
require_once("inc/init.php");
if(file_exists("../../lib_rd4/class.rd4.ordine.php")){require_once("../../lib_rd4/class.rd4.ordine.php");}
if(file_exists("../lib_rd4/class.rd4.ordine.php")){require_once("../lib_rd4/class.rd4.ordine.php");}

if(!(_USER_PERMISSIONS & perm::puo_gestire_retegas)){die("KO");}

$page_title = "Admin bounces";
$page_id = "admin_bounces";

   
$res = json_decode(sparkpostAPIget("suppression-list/summary"),TRUE);
$res_total = $res["results"]["total"];

//$res_suppression_list = sparkpostAPIget("suppression-list?to=2017-11-11T09:00:00-0400&from=2017-01-01T00:00:00-0400");

?>
<div class="inbox-nav-bar no-content-padding">
    <h1 class="page-title txt-color-blueDark"><i class="fa fa-fw fa-unlock"></i> Admin BOUNCES&nbsp;</h1>
</div>
<div class="well well-lg margin-top-10">
    <h3>SPARKPOST DATA</h3>
    <p>Sparkost bounces total: <span class="font-md"><?php echo $res_total; ?></span></p>
    <p>Sparkost suppression_list: <span class="font-md"><?php echo print_r($res_suppression_list); ?></span></p>
</div>
<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="table-responsive" style="overflow-y:auto;overflow-x:auto;height:600px;">
            <table id="tabella_bounces">
                <thead>
                    <tr>
                        <th>id</th>
                        <th class="filter-select">type</th>
                        <th class="filter-select">bounce_class</th>
                        <th>raw_rcpt_to</th>
                        <th>raw_reason</th>
                        <th>timestamp</th>
                        <th>subject</th>
                        <th class="filter-select">userid</th>
                        <th class="filter-select">id_gas</th>
                        <th></th>
                    </tr>
                </thead>    
                    
                <tbody>
<?php
                $sql = "SELECT * from retegas_bounced ORDER BY id DESC;";
                $stmt = $db->prepare($sql);
                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);    
                foreach ($rows as $row){
                    $h ='<tr>';    
                        $h.='<td>'.$row["id"].'</td>';
                        $h.='<td>'.$row["type"].'</td>';
                        $h.='<td>'.$row["bounce_class"].'</td>';
                        $h.='<td>'.$row["raw_rcpt_to"].'</td>';
                        $h.='<td>'.$row["raw_reason"].'</td>';
                        $h.='<td>'.$row["timestamp"].'</td>';
                        $h.='<td>'.$row["subject"].'</td>';
                        $h.='<td>'.$row["userid"].'</td>';
                        $h.='<td>'.$row["id_gas"].'</td>';
                        $h.='<td><span class="unban_user" data-id="'.$row["id"].'" style="cursor:pointer"><i class="fa fa-times text-danger"></i></span></td>';
                    $h.='</tr>';
                    echo $h;
                }
    
    
    
?>
                </tbody>
            
                <tfoot>

                </tfoot>

            </table>
            </div>
        </article>
    </div>
</section>


<script type="text/javascript">

    pageSetUp();

    var pagefunction = function() {
    
    loadScript("js_rd4/plugin/tablesorter/js/jquery.tablesorter.widgets.js",startTable);
        function startTable(){    
                $.extend($.tablesorter.themes.bootstrap, {
                    table      : 'table table-bordered',
                    caption    : 'caption',
                    sortNone   : 'bootstrap-icon-unsorted',
                    sortAsc    : 'fa fa-arrow-up',
                    sortDesc   : 'fa fa-arrow-down'

                  });

                var $table = $('#tabella_bounces').tablesorter({
                    theme: 'bootstrap',
                        //debug:true,
                        widgets: ["uitheme","filter","zebra"],
                        widgetOptions : {
                            zebra : ["even", "odd"],
                            filter_reset : ".reset",
                            filter_columnFilters: true
                        }
                });
                
                //UNBAN USER
                $(document).off("click",".unban_user");
                $(document).on("click",".unban_user", function(e){
                    var $t = $(this);
                    var id = $(this).data("id");
                    
                    
                    console.log("Unban " + id);
                    $.SmartMessageBox({
                        title : "RIPRISTINA",
                        content : "Attenzione: se la mail non è realmente riattivata la riattivazione è inutile",
                        buttons : "[Esci][RIATTIVA]"
                    }, function(ButtonPress, Value) {

                        if(ButtonPress=="RIATTIVA"){

                            $.ajax({
                                  type: "POST",
                                  url: "ajax_rd4/gas/_act.php",
                                  dataType: 'json',
                                  data: {act: "unban_user", id : id },
                                  context: document.body
                                }).done(function(data) {
                                    if(data.result=="OK"){
                                            ok(data.msg);
                                            $t.hide();
                                            $t.parents('th').hide;
                                            //location.reload();
                                    }else{
                                        ko(data.msg);
                                    }

                                });
                        }
                    });
                    
                        
                });
                
        }        
    };



    loadScript("js_rd4/plugin/tablesorter/js/jquery.tablesorter.min.js", pagefunction);

</script>