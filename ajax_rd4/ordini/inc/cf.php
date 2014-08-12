<?php

require_once("../../../lib/config.php");

$stmt = $db->prepare("SELECT retegas_ordini.id_ordini,
            retegas_ordini.descrizione_ordini,
            retegas_listini.descrizione_listini,
            retegas_listini.id_tipologie,
            retegas_ditte.descrizione_ditte,
            retegas_ordini.data_chiusura,
            retegas_gas.descrizione_gas,
            retegas_referenze.id_gas_referenze,
            maaking_users.userid,
            maaking_users.fullname,
            retegas_ordini.id_utente,
            retegas_ordini.id_listini,
            retegas_ditte.id_ditte,
            retegas_ordini.data_apertura
            FROM (((((retegas_ordini INNER JOIN retegas_referenze ON retegas_ordini.id_ordini = retegas_referenze.id_ordine_referenze) LEFT JOIN maaking_users ON retegas_referenze.id_utente_referenze = maaking_users.userid) INNER JOIN retegas_listini ON retegas_ordini.id_listini = retegas_listini.id_listini) INNER JOIN retegas_ditte ON retegas_listini.id_ditte = retegas_ditte.id_ditte) INNER JOIN maaking_users AS maaking_users_1 ON retegas_ordini.id_utente = maaking_users_1.userid) INNER JOIN retegas_gas ON maaking_users_1.id_gas = retegas_gas.id_gas
            WHERE (retegas_referenze.id_gas_referenze='"._USER_ID_GAS."')
            ORDER BY retegas_ordini.data_chiusura DESC LIMIT 300;");
$stmt->execute();
$rows = $stmt->fetchAll();

$no=0;
$o=array();

foreach($rows as $row){



$oa =' <li>
        <span class="">
            <a href="javascript:void(0);" class="msg">
                <img src="img_rd4/t_'.$row["id_tipologie"].'_240.png" alt="" class="air air-top-left margin-top-5" width="40" height="40">
                <span class="from">'.$row["descrizione_ordini"].' <i class="icon-paperclip">'.$color.'</i></span>
                <time>'.$dd .'<b>'.$hours.'</b> h.</time>
                <span class="subject">'.$row["fullname"].', '.$row["descrizione_gas"].'</span>
                <span class="msg-body">'.$row["descrizione_ditte"].', '.$row["descrizione_listini"].'</span>

            </a>
            <span class="note pull-left margin-top-10">Non hai ancora partecipato.</span>
            <span class="pull-right"><a href="javascript:void(0);" class="btn btn-default btn-xs" rel="popover" data-placement="top" data-original-title="Di questo ordine puoi:" data-content="<div class=\'btn-group-vertical btn-xs\'><button type=\'button\' class=\'btn btn-default\'>Eliminare la tua spesa</button><button type=\'button\' class=\'btn btn-default\'>Contattare il referente</button><button type=\'button\' class=\'btn btn-default\'>Altro</button></div>" data-html="true" aria-describedby="popover'.$row["id_ordini"].'"><i class="fa fa-cog"></i> Opzioni</a></span>

        </span>
        </li>';
    //2014-09-19 00:00:00
    $color = "bg-color-greenLight";
    $ciccio = "A";
    if($row["data_apertura"]>date("Y-m-d H:i:s")){
        $color = "bg-color-blueLight";
        $ciccio = "F";
    }
    if($row["data_chiusura"]<date("Y-m-d H:i:s")){
        $color = "bg-color-redLight";
        $ciccio = "C";
    }

    if($row["id_utente"]==_USER_ID){
        $gestore = "SI";
    }else{
        $gestore = "NO";
    }
    $content ='<p>Listino: <a href="#">'.$row["descrizione_listini"].'</a></p>
               <p>Ditta: <a href="#">'.$row["descrizione_ditte"].'</a></p>';

    $a=array('title'=> "#".$row["id_ordini"]." ".$row["descrizione_ordini"],
             'start'=> $row["data_apertura"],
             'end'=> $row["data_chiusura"],
             'className' => array("event", $color),
             'icon' => 'fa-cube',
             'ciccio' => $ciccio,
             'contenuto' => $content,
             'gestore' => $gestore);
    array_push($o,$a);
}
//[{  "allday":"false",
//    "borderColor":"#666666",
//    "color":"#cccccc",
//    "description": "<p>Testing the 3rd event source.<\/p><p>That's all!<\/p>",
//    "end":"2014-08-03 00:00",
//    "start":"2014-08-03 00:00",
//    "textColor":"#000000",
//    "title":"3rd Source 1",
//    "url":"http:\/\/mikesmithdev.com"},

//    {"allday":"false",
//    "borderColor":"#666666","color":"#cccccc","description":"<p>Testing the 3rd event source.<\/p><p>That's all!<\/p>","end":"2014-08-04 00:00","start":"2014-08-04 00:00","textColor":"#000000","title":"3rd Source 2","url":"http:\/\/mikesmithdev.com"},{"allday":"false","borderColor":"#666666","color":"#cccccc","description":"<p>Testing the 3rd event source.<\/p><p>That's all!<\/p>","end":"2014-08-03 00:00","start":"2014-08-03 00:00","textColor":"#000000","title":"3rd Source 3","url":"http:\/\/mikesmithdev.com"},{"allday":"false","borderColor":"#666666","color":"#cccccc","description":"<p>Testing the 3rd event source.<\/p><p>That's all!<\/p>","end":"2014-08-08 00:00","start":"2014-08-06 00:00","textColor":"#000000","title":"3rd Source 4","url":"http:\/\/mikesmithdev.com"},{"allday":"false","borderColor":"#666666","color":"#cccccc","description":"<p>Testing the 3rd event source.<\/p><p>That's all!<\/p>","end":"2014-08-07 00:00","start":"2014-08-07 00:00","textColor":"#000000","title":"3rd Source 5","url":"http:\/\/mikesmithdev.com"},{"allday":"false","borderColor":"#666666","color":"#cccccc","description":"<p>Testing the 3rd event source.<\/p><p>That's all!<\/p>","end":"2014-08-06 00:00","start":"2014-08-06 00:00","textColor":"#000000","title":"3rd Source 6","url":"http:\/\/mikesmithdev.com"},{"allday":"false","borderColor":"#666666","color":"#cccccc","description":"<p>Testing the 3rd event source.<\/p><p>That's all!<\/p>","end":"2014-08-09 00:00","start":"2014-08-09 00:00","textColor":"#000000","title":"3rd Source 7","url":"http:\/\/mikesmithdev.com"}]


echo json_encode($o);
