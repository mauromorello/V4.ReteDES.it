<?php
require_once("../../../lib/config.php");

$stmt = $db->prepare("SELECT * FROM  maaking_users WHERE userid = :userid");
$stmt->bindParam(':userid', $_GET['id'], PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
        &times;
    </button>
    <h4 class="modal-title" id="myModalLabel">Messaggio a <strong></strong><?php echo $row["fullname"] ?></strong></h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <textarea id="usermessage" class="form-control" placeholder="Scrivi qua..." rows="5" required ></textarea>
            </div>
        </div>
    </div>

</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">
        Cancella
    </button>
    <button id="usermessage_go" type="button" class="btn btn-primary">
        Invia
    </button>
</div>