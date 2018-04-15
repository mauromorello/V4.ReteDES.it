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


        case "log_ordine":
            $id_ordine=CAST_TO_INT($_POST["id_ordine"],0);
            if($id_ordine>0){
                $O = new ordine($id_ordine);
                $h = '<h1>'.$O->descrizione_ordini.'</h1>';
                $h.= '<p>'.$O->fullname_referente.' GAS #'.$O->id_gas_referente.'</p>';

                $fh = fopen(dirname(__FILE__).'/../../public_rd4/logs/ordini/'.$id_ordine."/log.txt",'r');
                $ha = array();
                while ($line = fgets($fh)) {
                   $ha[] = $line;
                }
                fclose($fh);
                arsort($ha);

                foreach($ha as $row){
                     $h.=$row."<br>";
                }


                $res=array("result"=>"OK", "msg"=>$h );
            }else{
                $res=array("result"=>"KO", "msg"=>"NO id" );
            }




            echo json_encode($res);
        break;

        default:
            $res=array("result"=>"KO", "msg"=>"Comando non riconosciuto" );
            echo json_encode($res);
        break;
     }

}

if(isset($_POST["name"])){
    switch ($_POST["name"]) {
        /*
        /* -------------------------RETTIFICHE DA XEDITABLE
        */
        case "user_gas":
            
            $id=CAST_TO_INT($_POST["pk"],0);
            $value = CAST_TO_INT($_POST["value"],0);
            
            $sql='UPDATE retegas_gas SET id_referente_gas=:value WHERE id_gas=:id LIMIT 1;';
            
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':value', $value, PDO::PARAM_INT);
            $stmt->execute();
            if($stmt->rowCount()<>1){
                $res=array("result"=>"KO", "msg"=>"DB Fail" );
                echo json_encode($res);    
            }else{
                $res=array("result"=>"OK", "msg"=>"OK" );
                echo json_encode($res);    
            }
            
            
        break;
    
    case "id_des":
            
            $id=CAST_TO_INT($_POST["pk"],0);
            $value = CAST_TO_INT($_POST["value"],0);
            
            $sql='UPDATE retegas_gas SET id_des=:value WHERE id_gas=:id LIMIT 1;';
            
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':value', $value, PDO::PARAM_INT);
            $stmt->execute();
            if($stmt->rowCount()<>1){
                $res=array("result"=>"KO", "msg"=>"DB Fail" );
                echo json_encode($res);    
            }else{
                $res=array("result"=>"OK", "msg"=>"OK" );
                echo json_encode($res);    
            }
            
            
        break;
    
    
        default:
            $res=array("result"=>"KO", "msg"=>"Comando non riconosciuto" );
            echo json_encode($res);
        break;

    
    }

}
if(!empty($_GET["name"])){
     switch ($_GET["name"]) {

     }

}
