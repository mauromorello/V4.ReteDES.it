<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.user.php");
require_once("../../lib_rd4/class.rd4.gas.php");
require_once("../../lib_rd4/htmlpurifier-4.7.0/library/HTMLPurifier.auto.php");

$converter = new Encryption;

function purify($string){
    $config = HTMLPurifier_Config::createDefault();
    $config->set('CSS.MaxImgLength', null);
    $config->set('HTML.MaxImgLength', null);
    $config->set('HTML.AllowedAttributes', 'href, src, height, width, alt');
    $config->set('HTML.SafeIframe', true);
    $config->set('URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%'); //allow YouTube and Vimeo
    $config->set('Attr.AllowedFrameTargets', array('_blank','_self'));
    $config->set('URI.AllowedSchemes', array('http' => true, 'https' => true, 'mailto' => true, 'ftp'=> true, 'nntp' => true, 'news' => true, 'data' => true));
    $purifier = new HTMLPurifier($config);
    return $purifier->purify($string);    
}

if(!empty($_POST["act"])){
    switch ($_POST["act"]) {

        case "save_trigger_1_1":
            
            $id_owner = _USER_ID;
            
            $id_target = CAST_TO_INT($_POST["id_target"],0);
            if($id_target==0){$id_target=_USER_ID;}
            
            $soglia_scatole=CAST_TO_INT($_POST["soglia_scatole"],0);
            if($soglia_scatole==0){
                $res=array("result"=>"KO", "msg"=>"Soglia scatole a 0" );
                echo json_encode($res);
                die();    
            }
            
            $id_ordine=CAST_TO_INT($_POST["id_ordine"],0);
            if($id_ordine==0){
                $res=array("result"=>"KO", "msg"=>"id ordine a 0" );
                echo json_encode($res);
                die();    
            }
            $O=new ordine($id_ordine);
            if(($O->codice_stato=="CH") OR ($O->codice_stato=="CO")){
                $res=array("result"=>"KO", "msg"=>"Ordine già chiuso o convalidato" );
                echo json_encode($res);
                die();    
            }
            $name=CAST_TO_STRING($_POST["name"]);
            
            
            $messaggio='<p>Attenzione! l\'ordine #'.$id_ordine.' ha superato le '.$soglia_scatole.' scatole piene.</p>'.
                        CAST_TO_STRING($_POST["messaggio"]);
            
            
                
            
            $messaggio = purify($messaggio);
            
            
            
            $stmt = $db->prepare("INSERT INTO retegas_triggers 
                                    (id_owner,
                                    name,
                                    tipo,
                                    sottotipo,
                                    valore,
                                    id_ordine,
                                    testo_azione,
                                    id_utente)
                                    VALUES
                                    (:id_owner,
                                    :name,
                                    1,
                                    1,
                                    :soglia_scatole,
                                    :id_ordine,
                                    :messaggio,
                                    :id_target);");
            $stmt->bindParam(':id_owner', $id_owner, PDO::PARAM_INT);
            $stmt->bindParam(':name', $name, PDO::PARAM_INT);
            $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
            $stmt->bindParam(':id_target', $id_target, PDO::PARAM_INT);
            $stmt->bindParam(':soglia_scatole', $soglia_scatole, PDO::PARAM_INT);
            $stmt->bindParam(':messaggio', $messaggio, PDO::PARAM_STR);
            $stmt->execute();
            if($stmt->rowCount()<>1){
                $res=array("result"=>"KO", "msg"=>"Trigger non inserito" );
                echo json_encode($res);
                die();
            }else{
            
                $res=array("result"=>"OK", "msg"=>"trigger inserito :)" );
                echo json_encode($res);
                die();
            }
        break;
        
        case "save_trigger_1_2"://----------------------------------------------------------- START SOGLIA ARTICOLI
            
            $id_owner = _USER_ID;
            
            $id_target = CAST_TO_INT($_POST["id_target"],0);
            if($id_target==0){$id_target=_USER_ID;}
            
            $soglia_articoli=CAST_TO_INT($_POST["soglia_articoli"],0);
            if($soglia_articoli==0){
                $res=array("result"=>"KO", "msg"=>"Soglia articoli a 0" );
                echo json_encode($res);
                die();    
            }
            
            $id_ordine=CAST_TO_INT($_POST["id_ordine"],0);
            if($id_ordine==0){
                $res=array("result"=>"KO", "msg"=>"id ordine a 0" );
                echo json_encode($res);
                die();    
            }
            $O=new ordine($id_ordine);
            if(($O->codice_stato=="CH") OR ($O->codice_stato=="CO")){
                $res=array("result"=>"KO", "msg"=>"Ordine già chiuso o convalidato" );
                echo json_encode($res);
                die();    
            }
            $name=CAST_TO_STRING($_POST["name"]);
            
            
            $messaggio='<p>Attenzione! l\'ordine #'.$id_ordine.' ha superato i '.$soglia_articoli.' articoli ordinati.</p>'.
                        CAST_TO_STRING($_POST["messaggio"]);
            
            
                
            
            $messaggio = purify($messaggio);
            
            
            
            $stmt = $db->prepare("INSERT INTO retegas_triggers 
                                    (id_owner,
                                    name,
                                    tipo,
                                    sottotipo,
                                    valore,
                                    id_ordine,
                                    testo_azione,
                                    id_utente)
                                    VALUES
                                    (:id_owner,
                                    :name,
                                    1,
                                    2,
                                    :soglia_articoli,
                                    :id_ordine,
                                    :messaggio,
                                    :id_target);");
            $stmt->bindParam(':id_owner', $id_owner, PDO::PARAM_INT);
            $stmt->bindParam(':name', $name, PDO::PARAM_INT);
            $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
            $stmt->bindParam(':id_target', $id_target, PDO::PARAM_INT);
            $stmt->bindParam(':soglia_articoli', $soglia_articoli, PDO::PARAM_INT);
            $stmt->bindParam(':messaggio', $messaggio, PDO::PARAM_STR);
            $stmt->execute();
            if($stmt->rowCount()<>1){
                $res=array("result"=>"KO", "msg"=>"Trigger non inserito" );
                echo json_encode($res);
                die();
            }else{
            
                $res=array("result"=>"OK", "msg"=>"trigger inserito :)" );
                echo json_encode($res);
                die();
            }
        break;//----------------------------------------------------------- END SOGLIA ARTICOLI
        
        case "save_trigger_1_3"://------------------------------------------ START VALORE ORDINE
            
            $id_owner = _USER_ID;
            
            $id_target = CAST_TO_INT($_POST["id_target"],0);
            if($id_target==0){$id_target=_USER_ID;}
            
            $valore=CAST_TO_INT($_POST["valore"],0);
            if($valore==0){
                $res=array("result"=>"KO", "msg"=>"Soglia a 0" );
                echo json_encode($res);
                die();    
            }
            
            $id_ordine=CAST_TO_INT($_POST["id_ordine"],0);
            if($id_ordine==0){
                $res=array("result"=>"KO", "msg"=>"id ordine a 0" );
                echo json_encode($res);
                die();    
            }
            $O=new ordine($id_ordine);
            if(($O->codice_stato=="CH") OR ($O->codice_stato=="CO")){
                $res=array("result"=>"KO", "msg"=>"Ordine già chiuso o convalidato" );
                echo json_encode($res);
                die();    
            }
            $name=CAST_TO_STRING($_POST["name"]);
            
            
            $messaggio='<p>Attenzione! l\'ordine #'.$id_ordine.' ha superato il valore di '.$valore.' Eu.</p>'.
                        CAST_TO_STRING($_POST["messaggio"]);
            
            
                
            
            $messaggio = purify($messaggio);
            
            
            
            $stmt = $db->prepare("INSERT INTO retegas_triggers 
                                    (id_owner,
                                    name,
                                    tipo,
                                    sottotipo,
                                    valore,
                                    id_ordine,
                                    testo_azione,
                                    id_utente)
                                    VALUES
                                    (:id_owner,
                                    :name,
                                    1,
                                    3,
                                    :valore,
                                    :id_ordine,
                                    :messaggio,
                                    :id_target);");
            $stmt->bindParam(':id_owner', $id_owner, PDO::PARAM_INT);
            $stmt->bindParam(':name', $name, PDO::PARAM_INT);
            $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
            $stmt->bindParam(':id_target', $id_target, PDO::PARAM_INT);
            $stmt->bindParam(':valore', $valore, PDO::PARAM_INT);
            $stmt->bindParam(':messaggio', $messaggio, PDO::PARAM_STR);
            $stmt->execute();
            if($stmt->rowCount()<>1){
                $res=array("result"=>"KO", "msg"=>"Trigger non inserito" );
                echo json_encode($res);
                die();
            }else{
            
                $res=array("result"=>"OK", "msg"=>"trigger inserito :)" );
                echo json_encode($res);
                die();
            }
        break;//----------------------------------------------------------- END VALORE ORDINE
        
        case "save_trigger_1_4"://------------------------------------------ START PARTECIPANTI ORDINE
            
            $id_owner = _USER_ID;
            
            $id_target = CAST_TO_INT($_POST["id_target"],0);
            if($id_target==0){$id_target=_USER_ID;}
            
            $valore=CAST_TO_INT($_POST["valore"],0);
            if($valore==0){
                $res=array("result"=>"KO", "msg"=>"Soglia a 0" );
                echo json_encode($res);
                die();    
            }
            
            $id_ordine=CAST_TO_INT($_POST["id_ordine"],0);
            if($id_ordine==0){
                $res=array("result"=>"KO", "msg"=>"id ordine a 0" );
                echo json_encode($res);
                die();    
            }
            $O=new ordine($id_ordine);
            if(($O->codice_stato=="CH") OR ($O->codice_stato=="CO")){
                $res=array("result"=>"KO", "msg"=>"Ordine già chiuso o convalidato" );
                echo json_encode($res);
                die();    
            }
            $name=CAST_TO_STRING($_POST["name"]);
            
            
            $messaggio='<p>Attenzione! l\'ordine #'.$id_ordine.' ha superato i '.$valore.' partecipanti.</p>'.
                        CAST_TO_STRING($_POST["messaggio"]);
            
            
                
            
            $messaggio = purify($messaggio);
            
            
            
            $stmt = $db->prepare("INSERT INTO retegas_triggers 
                                    (id_owner,
                                    name,
                                    tipo,
                                    sottotipo,
                                    valore,
                                    id_ordine,
                                    testo_azione,
                                    id_utente)
                                    VALUES
                                    (:id_owner,
                                    :name,
                                    1,
                                    4,
                                    :valore,
                                    :id_ordine,
                                    :messaggio,
                                    :id_target);");
            $stmt->bindParam(':id_owner', $id_owner, PDO::PARAM_INT);
            $stmt->bindParam(':name', $name, PDO::PARAM_INT);
            $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
            $stmt->bindParam(':id_target', $id_target, PDO::PARAM_INT);
            $stmt->bindParam(':valore', $valore, PDO::PARAM_INT);
            $stmt->bindParam(':messaggio', $messaggio, PDO::PARAM_STR);
            $stmt->execute();
            if($stmt->rowCount()<>1){
                $res=array("result"=>"KO", "msg"=>"Trigger non inserito" );
                echo json_encode($res);
                die();
            }else{
            
                $res=array("result"=>"OK", "msg"=>"trigger inserito :)" );
                echo json_encode($res);
                die();
            }
        break;//----------------------------------------------------------- END VALORE ORDINE
        
        case "save_trigger_2_1"://------------------------------------------ START MESSAGGIO A TEMPO
            
            $id_owner = _USER_ID;
            
            $id_target = CAST_TO_INT($_POST["id_target"],0);
            if($id_target==0){$id_target=_USER_ID;}
            
            $valore=CAST_TO_STRING($_POST["valore"]);
            
            $valore = strtotime(str_replace('/', '-', $_POST['valore']));
            $data_now = strtotime(date("d-m-Y H:i"));
            
            if($data_now > $valore){
                $res=array("result"=>"KO", "msg"=>"la data è nel passato." );
                echo json_encode($res);
                die();    
            }

            $messaggio = CAST_TO_STRING($_POST["messaggio"]);
            $messaggio = purify($messaggio);
            if($messaggio=="<p><br /></p>"){
                $res=array("result"=>"KO", "msg"=>"Messaggio Vuoto" );
                echo json_encode($res);
                die();    
            }
            
            
            $stmt = $db->prepare("INSERT INTO retegas_triggers 
                                    (id_owner,
                                    
                                    tipo,
                                    sottotipo,
                                    quando,
                                    
                                    testo_azione,
                                    id_utente)
                                    VALUES
                                    (:id_owner,
                                    
                                    2,
                                    1,
                                    :valore,
                                    
                                    :messaggio,
                                    :id_target);");
            $stmt->bindParam(':id_owner', $id_owner, PDO::PARAM_INT);
            
            
            $stmt->bindParam(':id_target', $id_target, PDO::PARAM_INT);
            $stmt->bindParam(':valore', conv_date_to_db($_POST['valore']), PDO::PARAM_INT);
            $stmt->bindParam(':messaggio', $messaggio, PDO::PARAM_STR);
            $stmt->execute();
            if($stmt->rowCount()<>1){
                $res=array("result"=>"KO", "msg"=>"Trigger non inserito" );
                echo json_encode($res);
                die();
            }else{
            
                $res=array("result"=>"OK", "msg"=>"trigger inserito :)" );
                echo json_encode($res);
                die();
            }
        break;//----------------------------------------------------------- END MESSAGGIO A TEMPO
        
        
        case "delete_trigger":
        
            $id_trigger=CAST_TO_INT($_POST["id_trigger"],0);
            $id_owner = _USER_ID;
            
            $sql="DELETE FROM retegas_triggers WHERE id_trigger=:id_trigger AND id_owner=:id_owner LIMIT 1;";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_trigger', $id_trigger, PDO::PARAM_INT);
            $stmt->bindParam(':id_owner', $id_owner, PDO::PARAM_INT);
            $stmt->execute();
            if($stmt->rowCount()<>1){
                $res=array("result"=>"KO", "msg"=>"Non possibile" );
                echo json_encode($res);
                break;
            }else{
                $res=array("result"=>"OK", "msg"=>"trigger eliminato" );
                echo json_encode($res);
                break;    
            }
        
        
        break;    
        
        
        default:
        $res=array("result"=>"KO", "msg"=>"comando sconosciuto" );
        echo json_encode($res);
        die();
        
        
    }
}        
?>
