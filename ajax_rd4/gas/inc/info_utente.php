<?php
require_once("../../../lib/config.php");
$converter = new Encryption;

$userid = $_POST["userid"];
$userid = $converter->decode($userid);

$stmt = $db->prepare("SELECT * FROM  maaking_users WHERE userid = :userid");
$stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if(trim($row["profile"])<>""){
    if(_USER_PERMISSIONS & perm::puo_gestire_utenti){
        $profilo= $row["profile"];
    }
}else{
    $profilo ="";
}




?>
<div class="well well-sm margin-top-10">
<b><?php echo $row["fullname"]; ?></b><br>
<p><?php echo $row["country"]; ?>, <?php echo $row["city"]; ?></p>
<cite><?php echo $profilo; ?></cite>
</div>