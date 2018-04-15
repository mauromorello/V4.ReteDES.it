<?php
require_once("inc/init.php");
if(file_exists("../../lib_rd4/class.rd4.user.php")){require_once("../../lib_rd4/class.rd4.user.php");}
if(file_exists("../lib_rd4/class.rd4.user.php")){require_once("../lib_rd4/class.rd4.user.php");}
if(file_exists("../../lib_rd4/class.rd4.ordine.php")){require_once("../../lib_rd4/class.rd4.ordine.php");}
if(file_exists("../lib_rd4/class.rd4.ordine.php")){require_once("../lib_rd4/class.rd4.ordine.php");}
if(file_exists("../../lib_rd4/class.rd4.articolo.php")){require_once("../../lib_rd4/class.rd4.articolo.php");}
if(file_exists("../lib_rd4/class.rd4.articolo.php")){require_once("../lib_rd4/class.rd4.articolo.php");}
if(file_exists("../../lib_rd4/class.rd4.gas.php")){require_once("../../lib_rd4/class.rd4.gas.php");}
if(file_exists("../lib_rd4/class.rd4.gas.php")){require_once("../lib_rd4/class.rd4.gas.php");}
if(file_exists("../../lib_rd4/class.rd4.listino.php")){require_once("../../lib_rd4/class.rd4.listino.php");}
if(file_exists("../lib_rd4/class.rd4.listino.php")){require_once("../lib_rd4/class.rd4.listino.php");}

$converter = new Encryption;

if(!empty($_POST["act"])){
    switch ($_POST["act"]) {

    case "do_triggers":
    
        $id_ordine = CAST_TO_INT($_POST["id_ordine"],0);
        if($id_ordine==0){
            $res=array("result"=>"KO", "msg"=>"ID ordine a 0" );
            echo json_encode($res);        
        }
        $id_utente = _USER_ID;
        $O = new ordine($id_ordine);
    
        /*TRIGGER_1_1 Raggiungimento di n scatole piene*/
        $n_scatole_piene=$O->n_scatole_piene();
        $params_1_1=array("id_ordine" => $id_ordine,"valore"=>$n_scatole_piene);
        trigger_engine(1,1,$params_1_1);
        /*---------------------------------------------*/
    
        /*TRIGGER_1_2 Raggiungimento di n articoli_ordinati*/
        $n_articoli_ordinati=$O->n_articoli_ordinati();
        $params_1_2=array("id_ordine" => $id_ordine,"valore"=>$n_articoli_ordinati);
        trigger_engine(1,2,$params_1_2);
        /*---------------------------------------------*/
        
        /*TRIGGER_1_3 Raggiungimento di n articoli_ordinati*/
        $valore_ordine =VA_ORDINE($id_ordine);
        $params_1_3=array("id_ordine" => $id_ordine,"valore"=>$valore_ordine);
        trigger_engine(1,3,$params_1_3);
        /*---------------------------------------------*/
        
        /*TRIGGER_1_4 Raggiungimento di n partecipanti*/
        $numero_partecipanti =$O->n_utenti_partecipanti();
        $params_1_4=array("id_ordine" => $id_ordine,"valore"=>$numero_partecipanti);
        trigger_engine(1,4,$params_1_4);
        /*---------------------------------------------*/
        $res=array("result"=>"OK", "msg"=>"Triggers eseguiti" );
        echo json_encode($res);
    break;

    default:
        $res=array("result"=>"KO", "msg"=>"Comando non riconosciuto" );
        echo json_encode($res);
     break;
    }
}