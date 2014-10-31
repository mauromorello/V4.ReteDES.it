<?php
require_once("inc/init.php");

if(!empty($_POST["act"])){
    switch ($_POST["act"]) {

     case "save_coord":
        //esiste
        $stmt = $db->prepare("UPDATE retegas_ditte SET ditte_gc_lat=:ditte_gc_lat, ditte_gc_lng= :ditte_gc_lng
                             WHERE id_ditte=:id_ditte AND id_proponente='"._USER_ID."' LIMIT 1;");
        $stmt->bindParam(':id_ditte', $_POST['id_ditte'], PDO::PARAM_INT);
        $stmt->bindParam(':ditte_gc_lat', $_POST['ditte_gc_lat'], PDO::PARAM_STR);
        $stmt->bindParam(':ditte_gc_lng', $_POST['ditte_gc_lng'], PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()==1){;
            $res=array("result"=>"OK", "msg"=>"Nuove coordinate OK." );
        }else{
            $res=array("result"=>"KO", "msg"=>"Errore nel DB... RC: ".$stmt->rowCount() );
        }

        echo json_encode($res);
     break;
     case "save_note":
        //esiste
        $stmt = $db->prepare("UPDATE retegas_ditte SET note_ditte= :note_ditte
                             WHERE id_ditte=:id_ditte AND id_proponente='"._USER_ID."' LIMIT 1;");
        $stmt->bindParam(':id_ditte', $_POST['id_ditte'], PDO::PARAM_INT);
        $stmt->bindParam(':note_ditte', $_POST['note_ditte'], PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()==1){;
            $res=array("result"=>"OK", "msg"=>"Note salvate" );
        }else{
            $res=array("result"=>"KO", "msg"=>"Errore nel DB... RC: ".$stmt->rowCount() );
        }

        echo json_encode($res);
     break;

     case "aggiungi_ditta":

    if($_POST["value"]==""){
        echo json_encode(array("result"=>"KO", "msg"=>"Non puoi lasciare questo campo vuoto"));
        die();
    }

    //esiste
    $stmt = $db->prepare("INSERT INTO retegas_ditte (id_proponente,descrizione_ditte) VALUES ('"._USER_ID."',:nome) ;");
    $stmt->bindParam(':nome', $_POST['value'], PDO::PARAM_STR);
    $stmt->execute();

    $id = $db->lastInsertId();

    if($stmt->rowCount()==1){
        $res=array("result"=>"OK", "msg"=>"Ditta aggiunta", "id"=>$id );
    }else{
        $res=array("result"=>"KO", "msg"=>"Errore nel db." );
    }

    echo json_encode($res);

    break;
    //-------------------------------------------------------------------------------------

    case "delete_ditta":

    //esiste
    $stmt = $db->prepare("DELETE FROM retegas_ditte WHERE id_ditte=:id_ditta AND id_proponente='"._USER_ID."' LIMIT 1;");
    $stmt->bindParam(':id_ditta', $_POST['value'], PDO::PARAM_INT);
    $stmt->execute();
    if($stmt->rowCount()==1){
        $res=array("result"=>"OK", "msg"=>"Ditta Eliminata" );
    }else{
        $res=array("result"=>"KO", "msg"=>"Errore nel db ". $stmt->rowCount() );
    }

    echo json_encode($res);

    break;
    //-------------------------------------------------------------------------------------

     default:
        $res=array("result"=>"KO", "msg"=>"Comando non riconosciuto" );
        echo json_encode($res);
     break;
    }
};

if(!empty($_POST["name"])){
    switch ($_POST["name"]) {

     case "descrizione_ditte":
        //esiste
        $stmt = $db->prepare("UPDATE retegas_ditte SET descrizione_ditte= :descrizione_ditte
                             WHERE id_ditte=:id_ditte AND id_proponente='"._USER_ID."'");
        $stmt->bindParam(':id_ditte', $_POST['pk'], PDO::PARAM_INT);
        $stmt->bindParam(':descrizione_ditte', $_POST['value'], PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()==1){;
            $res=array("result"=>"OK", "msg"=>"Nuovo nome salvato." );
        }else{
            $res=array("result"=>"KO", "msg"=>"Errore nel DB..." );
        }

        echo json_encode($res);
     break;

     case "telefono":
        //esiste
        $stmt = $db->prepare("UPDATE retegas_ditte SET telefono= :telefono
                             WHERE id_ditte=:id_ditte AND id_proponente='"._USER_ID."'");
        $stmt->bindParam(':id_ditte', $_POST['pk'], PDO::PARAM_INT);
        $stmt->bindParam(':telefono', $_POST['value'], PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()==1){;
            $res=array("result"=>"OK", "msg"=>"Nuovo telefono salvato." );
        }else{
            $res=array("result"=>"KO", "msg"=>"Errore nel DB..." );
        }

        echo json_encode($res);
     break;

     case "website":
        //esiste
        $stmt = $db->prepare("UPDATE retegas_ditte SET website= :website
                             WHERE id_ditte=:id_ditte AND id_proponente='"._USER_ID."'");
        $stmt->bindParam(':id_ditte', $_POST['pk'], PDO::PARAM_INT);
        $stmt->bindParam(':website', $_POST['value'], PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()==1){;
            $res=array("result"=>"OK", "msg"=>"Nuovo link salvato." );
        }else{
            $res=array("result"=>"KO", "msg"=>"Errore nel DB..." );
        }

        echo json_encode($res);
     break;

     case "mail_ditte":
        //esiste
        $stmt = $db->prepare("UPDATE retegas_ditte SET mail_ditte= :mail_ditte
                             WHERE id_ditte=:id_ditte AND id_proponente='"._USER_ID."'");
        $stmt->bindParam(':id_ditte', $_POST['pk'], PDO::PARAM_INT);
        $stmt->bindParam(':mail_ditte', $_POST['value'], PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()==1){;
            $res=array("result"=>"OK", "msg"=>"Nuova mail salvata." );
        }else{
            $res=array("result"=>"KO", "msg"=>"Errore nel DB..." );
        }

        echo json_encode($res);
     break;

     case "indirizzo":
        //esiste
        $stmt = $db->prepare("UPDATE retegas_ditte SET indirizzo= :indirizzo
                             WHERE id_ditte=:id_ditte AND id_proponente='"._USER_ID."'");
        $stmt->bindParam(':id_ditte', $_POST['pk'], PDO::PARAM_INT);
        $stmt->bindParam(':indirizzo', $_POST['value'], PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()==1){;
            $res=array("result"=>"OK", "msg"=>$_POST['value'] );
        }else{
            $res=array("result"=>"KO", "msg"=>"Errore nel DB..." );
        }

        echo json_encode($res);
     break;
     case "tag_ditte":
        //esiste
        $stmt = $db->prepare("UPDATE retegas_ditte SET tag_ditte= :tag_ditte
                             WHERE id_ditte=:id_ditte AND id_proponente='"._USER_ID."'");
        $stmt->bindParam(':id_ditte', $_POST['pk'], PDO::PARAM_INT);
        $stmt->bindParam(':tag_ditte', $_POST['value'], PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()==1){;
            $res=array("result"=>"OK", "msg"=>$_POST['value'] );
        }else{
            $res=array("result"=>"KO", "msg"=>"Errore nel DB..." );
        }

        echo json_encode($res);
     break;

     default:
        $res=array("result"=>"KO", "msg"=>"Comando non riconosciuto" );
        echo json_encode($res);
     break;
    }
}
