<?php
require_once("../../../lib/config.php");

if(isset($_POST["id_ditta"])){
    $stmt = $db->prepare("SELECT * FROM retegas_listini WHERE id_ditte=:id_ditta ORDER BY data_valido DESC;");
    $stmt->bindParam(':id_ditta', $_POST['id_ditta'], PDO::PARAM_STR);
    $stmt->execute();
    $rows = $stmt->fetchAll();


    $li = '<ul class="list-group" style="max-height:600px; overflow-y:auto;">';
    foreach($rows as $row){

        $stmt = $db->prepare("SELECT U.fullname,G.descrizione_gas,G.id_gas FROM maaking_users U inner join retegas_gas G on G.id_gas=U.id_gas WHERE U.userid=:userid LIMIT 1;");
        $stmt->bindParam(':userid', $row["id_utenti"], PDO::PARAM_INT);
        $stmt->execute();
        $utente = $stmt->fetch();

        $stmt = $db->prepare("SELECT count(*) as conto FROM retegas_articoli WHERE id_listini=:id_listini");
        $stmt->bindParam(':id_listini', $row["id_listini"], PDO::PARAM_INT);
        $stmt->execute();
        $articoli = $stmt->fetch();


        if(strtotime($row["data_valido"])<strtotime(date("Y-m-d H:i:s"))){
            $class_scaduto=" bg-color-redLight txt-color-white";
            $scade = 'Scaduto dal '.conv_datetime_from_db($row["data_valido"]);
            $icona_scade='<i class="pull-right fa fa-warning fa-2x txt-color-red"></i>';
        }else{
            $class_scaduto ="";
            //$scade = "Scadrà il ".conv_datetime_from_db($row["data_valido"]);
            $scade="";
            $icona_scade='<i class="pull-right fa fa-check-square-o fa-2x txt-color-green"></i>';
        }

        if($row["tipo_listino"]==1){
            $icona_scade='<i class="pull-right fa fa-archive fa-2x txt-color-blue"></i>';
            $magazzino = " LISTINO MAGAZZINO<br>";
            $scade="";
        }else{
            $magazzino = "";
        }


        if(($row["is_privato"]==1) AND ($utente["id_gas"]<>_USER_ID_GAS)){
            $li.= '<li class="list-group-item" >
                    <i class="pull-right fa fa-eye-slash fa-2x txt-color-grey"></i>
                    <h3 class="txt-color-grey">Listino privato '.$utente["descrizione_gas"].'</h3>
                    </li>';
        }else{
            $li.= '<li class="list-group-item" >
                    '.$icona_scade.'
                    <strong class="font-sm" style="cursor:pointer" rel="C">'.$row["descrizione_listini"].'</strong><br>
                    <i class="fa fa-user"></i>&nbsp;'.$utente["fullname"].' <i class="fa fa-home"></i>&nbsp;'.$utente["descrizione_gas"].'&nbsp;&nbsp;<small class="font-xs"><i class="fa fa-cubes"></i>&nbsp;<b>'.$articoli["conto"].'</b></small><br>
                    '.$magazzino.'
                    '.$scade.'

                    </li>';
        }
    }

    $li .="</ul>";

echo $li;



}else{
    echo ' <div class="padding-10">
            <div class="alert alert-warning text-center">
                            <h5><i class="fa fa-table fa-2x"></i>&nbsp;&nbsp;Clicca sul nome di una ditta per vedere qua i suoi listini</h5>
                        </div>
          </div>  ';
}

