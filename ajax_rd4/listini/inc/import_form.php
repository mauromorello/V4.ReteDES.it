<?php
require_once("../../../lib/config.php");
$id_listino=CAST_TO_INT($_GET["id"]);

?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
        &times;
    </button>
    <h4 class="modal-title" id="myModalLabel">Carica un file di articoli</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <form class="dropzone" action="upload.php" method="POST" id="myAwesomeDropzone">
                <input type="hidden" name="act" value="listino">
                <input type="hidden" name="id_listino" value="<?php echo $id_listino; ?>"
            </form>
        </div>
    </div>

</div>

