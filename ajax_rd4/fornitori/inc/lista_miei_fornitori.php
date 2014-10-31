<?php
require_once("../../../lib/config.php");

$stmt = $db->prepare("SELECT * FROM retegas_ditte WHERE id_proponente='"._USER_ID."' ORDER BY id_ditte DESC;");
$stmt->execute();
$rows = $stmt->fetchAll();


$li = '<ul class="list-group" style="max-height:600px; overflow-y:auto;">';
foreach($rows as $row){

if($row["mail_ditte"]<>""){
    $mail='<i class=" glyphicon glyphicon-envelope text-success"></i>';
    $mail=$mail.'&nbsp;<a href="mailto:'.$row["mail_ditte"].'">'.$row["mail_ditte"].'</a>';
}else{
    $mail='';
}

if($row["ditte_gc_lat"]>0){
    $geo='<i class=" glyphicon glyphicon-map-marker text-success" rel="tooltip" data-original-title="GeoReferenziata"></i>';
    $geo = $geo.'&nbsp;'.$row["indirizzo"].'&nbsp;-&nbsp;';
}else{
    $geo='<i class=" glyphicon glyphicon-map-marker text-danger" rel="tooltip" data-original-title="NON GeoReferenziata"></i>';
}

if($row["telefono"]<>""){
    $tel='<i class=" glyphicon glyphicon-earphone text-success" ></i>';
    $tel= $tel.'&nbsp;<a href="tel:'.$row["telefono"].'">'.$row["telefono"].'</a>&nbsp;';
}else{
    $tel='';
}

if($row["tag_ditte"]<>""){
    $tag='<i class=" glyphicon glyphicon-tags text-success" rel="tooltip" data-original-title="'.$row["tag_ditte"].'"></i>';
}else{
    $tag='<i class=" glyphicon glyphicon-tags text-danger"></i>';
}

if($row["note_ditte"]<>""){
    $note='<i class=" glyphicon glyphicon-book text-success" rel="tooltip" data-original-title="Nessuna note inserita"></i>';
}else{
    $note='<i class=" glyphicon glyphicon-book text-danger"></i>';
}

if($row["website"]<>""){
    $web='<i class=" glyphicon glyphicon-link text-success" rel="tooltip" data-original-title="'.$row["website"].'"></i>';
    $web= $web.'&nbsp;<a href="'.$row["website"].'" target="_blank">'.$row["website"].'</a>';
}else{
    $web='';
}

    $li.= '<li class="list-group-item fornitore" name="'.$row["id_ditte"].'">
            <span class="pull-right">'.$note.'<br>'.$tag.'</span>
            <strong class="font-md ditta_selector" style="cursor:pointer" rel="'.$row["id_ditte"].'"><a href="#ajax_rd4/fornitori/scheda.php?id='.$row["id_ditte"].'">'.$row["descrizione_ditte"].'</a></strong><br>'
            .$tel.$web.'<br>'
            .'<small class="font-sm">'.$geo.$mail.'</small>
            </li>';
}

$li .="</ul>";

echo $li;