<?php
require_once("inc/init.php");
if(file_exists("../../lib_rd4/class.rd4.ordine.php")){require_once("../../lib_rd4/class.rd4.ordine.php");}
if(file_exists("../lib_rd4/class.rd4.ordine.php")){require_once("../lib_rd4/class.rd4.ordine.php");}

if(!(_USER_PERMISSIONS & perm::puo_gestire_retegas)){die("KO");}

$page_title = "Admin GAS";
$page_id = "admin_gas";




?>
<div class="inbox-nav-bar no-content-padding">
    <h1 class="page-title txt-color-blueDark"><i class="fa fa-fw fa-unlock"></i> GAS &nbsp;</h1>
</div>
<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well well-sm">
                <ul>
                    <li>GAS: <strong><?php echo $n_gas; ?></strong></li>
                </ul>
            </div>
        </article>
    </div>
</section>
        

<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="table-responsive" style="overflow-y:auto;overflow-x:auto;height:600px;">
            <table id="tabella_bounces">
                <thead>
                    <tr>
                        <th>id</th>
                        <th>DESC</th>
                        <th>REF</th>
                        <th>IND</th>
                        <th class="filter-select">DES</th>
                    </tr>
                </thead>    
                    
                <tbody>
<?php
                
                $sql ='SELECT G.*, U.fullname, D.des_descrizione FROM retegas_gas G 
                                inner join maaking_users U on U.userid=G.id_referente_gas
                                inner join retegas_des D on D.id_des=G.id_des;';
                $stmt = $db->prepare($sql);
                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach($rows AS $row){
                    $h ='<tr>';    
                        $h.='<td>'.$row["id_gas"].'</td>';
                        $h.='<td>'.$row["descrizione_gas"].'</td>';
                        $h.='<td><span class="user_gas" data-pk="'.$row["id_gas"].'">'.$row["id_referente_gas"].'</span> '.$row["fullname"].'</td>';
                        $h.='<td>'.$row["sede_gas"].' ('.$row["targa_gas"].')</td>';
                        $h.='<td><span class="id_des" data-pk="'.$row["id_gas"].'">'.$row["id_des"].'</span> '.$row["des_descrizione"].'</td>';
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
        
            $xeditable_user_gas = $('.user_gas').editable({
                    url: 'ajax_rd4/admin/_act_admin.php',
                    type: 'text',
                    name: 'user_gas',
                    title: 'Referente GAS',
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
            $xeditable_id_des = $('.id_des').editable({
                    url: 'ajax_rd4/admin/_act_admin.php',
                    type: 'text',
                    name: 'id_des',
                    title: 'ID DES',
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
        }        
    };

    function loadXeditable(){
             loadScript("js/plugin/x-editable/x-editable.min.js", pagefunction);
    }

    loadScript("js_rd4/plugin/tablesorter/js/jquery.tablesorter.min.js", loadXeditable);

</script>