<?php
require_once("inc/init.php");
if(!(_USER_PERMISSIONS & perm::puo_gestire_retegas)){die("KO");}
$ui = new SmartUI;
$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>true,
                    "deletebutton"=>false,
                    "colorbutton"=>false);


$stmt = $db->prepare("SELECT U.fullname,U.userid,G.descrizione_gas, COUNT(O.id_options) as contributi FROM retegas_options O inner join maaking_users U on U.userid=O.id_user inner join retegas_gas G on G.id_gas=U.id_gas WHERE O.chiave='_USER_PUO_MODIFICARE_HELP' AND O.valore_text ='SI' group by O.id_options;");
$stmt->execute();
$rows = $stmt->fetchAll();
$uh ="<ul>";
foreach($rows as $row){
   $uh.="<li><b><a href=\"#ajax_rd4/admin/admin.php?do=del_user_help&id=".$row["userid"]."\">".$row["fullname"]."</a></b>, ".$row["descrizione_gas"]." (".$row["contributi"].")</li>";
}

$uh .="</ul>";


$wg_ute_help = $ui->create_widget($options);
$wg_ute_help->id = "wg_cassa_home";
$wg_ute_help->body = array("content" => $uh ,"class" => "");
$wg_ute_help->header = array(
    "title" => '<h2>Utenti con abilitazione per HELP</h2>',
    "icon" => 'fa fa-question'
);



?>
<div class="inbox-nav-bar no-content-padding">
    <h1 class="page-title txt-color-blueDark"><i class="fa fa-fw fa-unlock"></i> Admin &nbsp;</h1>
</div>

<section id="widget-grid" class="margin-top-10">

    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <?php echo $wg_ute_help->print_html(); ?>
        </article>
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        </article>

    </div>

</section>


<script type="text/javascript">

    pageSetUp();

    var pagefunction = function() {
        // clears memory even if nothing is in the function
    };

    // end pagefunction

    // run pagefunction on load
    pagefunction();

</script>