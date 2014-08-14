<?php
require_once("inc/init.php");
if(!(_USER_PERMISSIONS & perm::puo_gestire_retegas)){die("KO");}
$ui = new SmartUI;
$options = array(   "editbutton" => false,
                    "fullscreenbutton"=>true,
                    "deletebutton"=>false,
                    "colorbutton"=>false);


$stmt = $db->prepare("SELECT U.fullname,U.userid,G.descrizione_gas FROM retegas_options O inner join maaking_users U on U.userid=O.id_user inner join retegas_gas G on G.id_gas=U.id_gas  WHERE O.chiave='_USER_PUO_MODIFICARE_HELP' AND O.valore_text ='SI';");
$stmt->execute();
$rows = $stmt->fetchAll();
$uh ="<ul>";
foreach($rows as $row){
   $uh.="<li><b><a href=\"#ajax_rd4/admin/admin.php?do=del_user_help&id=".$row["userid"]."\">".$row["fullname"]."</a></b>, ".$row["descrizione_gas"]." (".$row["contributi"].")</li>";
}

$uh .='</ul>';


$wg_ute_help = $ui->create_widget($options);
$wg_ute_help->id = "wg_admin_user_help_allowed";
$wg_ute_help->body = array("content" => $uh ,"class" => "");
$wg_ute_help->header = array(
    "title" => '<h2>Utenti con abilitazione per HELP</h2>',
    "icon" => 'fa fa-question'
);



$stmt = $db->prepare("SELECT userid, fullname, email, G.descrizione_gas FROM maaking_users U inner join retegas_gas G on G.id_gas=U.id_gas;");
$stmt->execute();
$rows = $stmt->fetchAll();
$us ='<select style="width:100%"  id="user_selection">';
foreach($rows as $row){
    $us .='<option value="'.$row["userid"].'">'.$row["userid"].' '.$row["fullname"].' - '.$row["email"].' - '.$row["descrizione_gas"].'</option>';
}
$us .= '</select>
        <div class="well font-xl margin-top-10" id="userid"></div>';

$wg_ute_sel = $ui->create_widget($options);
$wg_ute_sel->id = "wg_admin_user_selection";
$wg_ute_sel->body = array("content" => $us ,"class" => "");
$wg_ute_sel->header = array(
    "title" => '<h2>Selezione User</h2>',
    "icon" => 'fa fa-user'
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
            <?php echo $wg_ute_sel->print_html(); ?>
        </article>

    </div>

</section>


<script type="text/javascript">

    pageSetUp();

    var pagefunction = function() {
        $('#user_selection').select2();
        $('#user_selection').on("select2-selected", function(e){$('#userid').html(e.val)});
    };

    // end pagefunction

    // run pagefunction on load
    pagefunction();

</script>