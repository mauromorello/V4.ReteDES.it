<?php
require_once("../../../lib/config.php");

$sql="SELECT * from retegas_ditte WHERE id_ditte=:id_ditta AND id_proponente='"._USER_ID."' LIMIT 1;";
$stmt = $db->prepare($sql);
$stmt->bindParam(':id_ditta', $_POST['id_ditta'], PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch();

$sql="SELECT * from retegas_listini WHERE id_ditte=:id_ditta;";
$stmt = $db->prepare($sql);
$stmt->bindParam(':id_ditta', $_POST['id_ditta'], PDO::PARAM_INT);
$stmt->execute();
$lis= $stmt->fetch(PDO::FETCH_ASSOC);
if( ! $lis)
{
    $cancella_ditta = '<button id="elimina_ditta" rel="'.$row["id_ditte"].'" class="btn btn-block btn-danger">ELIMINA DITTA</button>';
}


if (!empty($row)) {
    $result="OK";
    $msg='
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <label for="descrizione_ditte">Nome:</label>
            <p id="descrizione_ditte" class="editable font-xl" data-type="text" data-pk="'.$row["id_ditte"].'">'.$row["descrizione_ditte"].'</p>
            <label for="indirizzo">Indirizzo:</label>
            <p id="indirizzo" class="editable_map" data-type="textarea" data-pk="'.$row["id_ditte"].'">'.$row["indirizzo"].'</p>
            <div class="hidden" id="id_ditte" rel="'.$row["id_ditte"].'"></div>
            <div class="hidden" id="ditte_gc_lat" rel="'.$row["ditte_gc_lat"].'"></div>
            <div class="hidden" id="ditte_gc_lng" rel="'.$row["ditte_gc_lng"].'"></div>
            <div id="map-canvas" style="width:100%;height:180px;"></div>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <hr>
            <label for="telefono">Telefono:</label>
            <p id="telefono" class="editable" data-type="text" data-pk="'.$row["id_ditte"].'">'.$row["telefono"].'</p>
            <label for="mail_ditte">Email:</label>
            <p id="mail_ditte" class="editable" data-type="email" data-pk="'.$row["id_ditte"].'">'.$row["mail_ditte"].'</p>
            <label for="website">Link:</label>
            <p id="website" class="editable" data-type="text" data-pk="'.$row["id_ditte"].'">'.$row["website"].'</p>
            <hr>
            <label for="tag_ditte">Parole chiave:</label>
            <p id="tag_ditte" class="editable" data-type="textarea" data-pk="'.$row["id_ditte"].'">'.$row["tag_ditte"].'</p>
            <hr>
            '.$cancella_ditta.'
        </div>
    </div>

    <div class="row well well-sm margin-top-10 padding-5">
        <label for="note_ditte">Note:</label>
        <div id="note_ditte" class="summernote">'.$row["note_ditte"].'</div>
        <button id="save_note" class="btn btn-success pull-right margin-top-10">Salva le note</button>
    </div>

            ';


}else{
    $result="KO";
    $msg=' <div class="padding-10">
            <div class="alert alert-warning text-center">
                            <h5><i class="fa fa-truck fa-2x"></i>&nbsp;&nbsp;Clicca sul nome di una ditta per vedere qua i suoi listini</h5>
                        </div>
          </div>  ';
}

$res=array("result"=>$result, "msg"=>$msg );
    echo json_encode($res);
