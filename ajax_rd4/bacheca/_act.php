<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.user.php");
require_once("../../lib_rd4/class.rd4.gas.php");
require_once("../../lib_rd4/htmlpurifier-4.7.0/library/HTMLPurifier.auto.php");




$converter = new Encryption;

switch ($_POST["act"]) {

    case "vetrina_post":
        //esiste
        $id_post=CAST_TO_INT($_POST["id_post"]);
        $id_utente=_USER_ID;

        $stmt = $db->prepare("SELECT is_vetrina FROM retegas_bacheca
                             WHERE id_bacheca=:id_bacheca");
        $stmt->bindParam(':id_bacheca', $id_post, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        if($row["is_vetrina"]>0){
            $stmt = $db->prepare("UPDATE retegas_bacheca SET is_vetrina=0 WHERE id_bacheca=:id_post LIMIT 1;");
            $result="NO";
        }else{
            $stmt = $db->prepare("UPDATE retegas_bacheca SET is_vetrina=1 WHERE id_bacheca=:id_post LIMIT 1;");
            $result="SI";
        }
        $stmt->bindParam(':id_post', $id_post, PDO::PARAM_INT);
        $stmt->execute();
        $res=array("result"=>"OK", "msg"=>"Ok", "vetrina"=>$result );
        echo json_encode($res);
     break;

    case "liked_post":
        //esiste
        $id_post=CAST_TO_INT($_POST["id_post"]);
        $id_utente=_USER_ID;

        $stmt = $db->prepare("SELECT COUNT(valore_int) as conto FROM retegas_options
                             WHERE id_bacheca=:id_bacheca AND id_user=:id_utente AND chiave='_USER_POST_LIKED'");
        $stmt->bindParam(':id_bacheca', $id_post, PDO::PARAM_INT);
        $stmt->bindParam(':id_utente', $id_utente, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        if($row["conto"]>0){
            $stmt = $db->prepare("DELETE FROM retegas_options
                             WHERE id_bacheca=:id_bacheca AND id_user=:id_utente AND chiave='_USER_POST_LIKED'");
            $stmt->bindParam(':id_bacheca', $id_post, PDO::PARAM_INT);
            $stmt->bindParam(':id_utente', $id_utente, PDO::PARAM_INT);
            $stmt->execute();
            $result="NO";
        }else{
            $stmt = $db->prepare("INSERT INTO retegas_options
                                    (id_bacheca,id_user,chiave,valore_int) VALUES (:id_bacheca,:id_utente,'_USER_POST_LIKED',1)");
            $stmt->bindParam(':id_bacheca', $id_post, PDO::PARAM_INT);
            $stmt->bindParam(':id_utente', $id_utente, PDO::PARAM_INT);
            $stmt->execute();
            $result="SI";
        }

       $res=array("result"=>"OK", "msg"=>"Ok", "preferito"=>$result );
       echo json_encode($res);
     break;

    case "save_edited_post":

            if(_USER_PERMISSIONS & perm::puo_postare_messaggi){

            }else{
                $res=array("result"=>"KO", "msg"=>"Non puoi postare messaggi");
                echo json_encode($res);
                die();
            }
            $id_post =CAST_TO_INT($_POST["id_post"],0);
            $is_vetrina = CAST_TO_INT($_POST["is_vetrina"],0);

            $sHTML =CAST_TO_STRING($_POST["sHTML"]);
            //$sHTML = strip_tags($sHTML,"<a><table><tr><td><code><small><alt><b><strong><font><ul><li><ol><br><hr><h1><h2><h3><h4><h5><h6><p><hr>");


            $config = HTMLPurifier_Config::createDefault();
            $config->set('CSS.MaxImgLength', null);
            $config->set('HTML.MaxImgLength', null);
            $config->set('HTML.SafeIframe', true);
            $config->set('URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%'); //allow YouTube and Vimeo
            $config->set('Attr.AllowedFrameTargets', array('_blank','_self'));
            $config->set('URI.AllowedSchemes', array('http' => true, 'https' => true, 'mailto' => true, 'ftp'=> true, 'nntp' => true, 'news' => true, 'data' => true));
            $purifier = new HTMLPurifier($config);
            $sHTML = $purifier->purify($sHTML);



            $sHTML .= '<small class="note editato_'.$id_post.'"><br>Editato da '._USER_FULLNAME.' il '.date('d/m/Y H:i').'</small>';
            $sql ="";
            $stmt = $db->prepare("UPDATE retegas_bacheca SET messaggio=:messaggio, titolo_messaggio='' WHERE id_bacheca= :id_post LIMIT 1;");
            $stmt->bindParam(':id_post', $id_post, PDO::PARAM_INT);

            $stmt->bindParam(':messaggio', $sHTML, PDO::PARAM_STR);
            $stmt->execute();

            $res=array("result"=>"OK", "msg"=>$sHTML);
            echo json_encode($res);
            die();

    break;

    case "save_post":

            if(_USER_PERMISSIONS & perm::puo_postare_messaggi){

            }else{
                $res=array("result"=>"KO", "msg"=>"Non puoi postare messaggi");
                echo json_encode($res);
                die();
            }

            $id_ordine=CAST_TO_INT($_POST["id_ordine"],0);
            $id_ditta =CAST_TO_INT($_POST["id_ditta"],0);

            $sHTML =$_POST["sHTML"];
            //$sHTML = strip_tags($sHTML,"<a><table><tr><td><code><small><alt><b><strong><font><ul><li><ol><br><hr><h1><h2><h3><h4><h5><h6><p><hr>");

            $config = HTMLPurifier_Config::createDefault();
            $config->set('CSS.MaxImgLength', null);
            $config->set('HTML.MaxImgLength', null);
            $config->set('HTML.SafeIframe', true);
            $config->set('URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%'); //allow YouTube and Vimeo
            $config->set('Attr.AllowedFrameTargets', array('_blank','_self'));

            $purifier = new HTMLPurifier($config);
            $sHTML = $purifier->purify($sHTML);

            if(CAST_TO_STRING($sHTML)==""){die();}


            $stmt = $db->prepare("INSERT INTO retegas_bacheca (id_utente, id_gas, id_des, messaggio, dataInserimento, id_ordine, id_ditta) VALUES ('"._USER_ID."', '"._USER_ID_GAS."', '"._USER_ID_DES."' ,:sHTML, NOW(), :id_ordine, :id_ditta)");
            $stmt->bindParam(':sHTML', $sHTML, PDO::PARAM_STR);
            $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
            $stmt->bindParam(':id_ditta', $id_ditta, PDO::PARAM_INT);
            $stmt->execute();

            if($stmt->rowCount()<>1){
                $res=array("result"=>"KO", "msg"=>"Non è stato possibile inserire il messagio.");
                echo json_encode($res);
                die();    
            }
            
            
            //NOTIFICHE TELEGRAM
            $href=APP_URL."/#ajax_rd4/gas/gas_bacheca.php";
            $msg = "in bacheca del tuo "._USER_GAS_NOME;

            if($id_ditta>0){
                $href=APP_URL."/#ajax_rd4/fornitori/scheda.php?id=".$id_ditta;
                $msg=" riguardo la ditta #$id_ditta";
            }
            if($id_ordine>0){
                $href=APP_URL."/#ajax_rd4/ordini/ordine.php?id=".$id_ordine;
                $msg=" riguardo all'ordine #$id_ordine";
            }


            $res=TE_GAS(_USER_ID_GAS,'C\'è un nuovo <a href="'.$href.'">messaggio</a> '.$msg.'!');


            $res=array("result"=>"OK", "msg"=>$res);
            echo json_encode($res);
            die();

    break;
    case "delete_post":

         $id_post=CAST_TO_INT($_POST["id_post"]);

         $sql="SELECT is_hidden, id_utente FROM retegas_bacheca WHERE id_bacheca='$id_post' LIMIT 1;";
         $stmt = $db->prepare($sql);
         $stmt->execute();
         $row = $stmt->fetch();

         if(!(_USER_PERMISSIONS&perm::puo_eliminare_messaggi)){
            if($row["id_utente"]<>_USER_ID){
                $res=array("result"=>"KO", "msg"=>"Non puoi" );
                echo json_encode($res);
                die();
            }
         }

         $sql="DELETE FROM retegas_bacheca WHERE id_bacheca='$id_post' LIMIT 1;";

         $stmt = $db->prepare($sql);
         $stmt->execute();
         $res=array("result"=>"OK", "msg"=>$result);
         echo json_encode($res);
    break;

    case "hide_post":

         $id_post=CAST_TO_INT($_POST["id_post"]);

         $sql="SELECT is_hidden, id_utente FROM retegas_bacheca WHERE id_bacheca='$id_post' LIMIT 1;";
         $stmt = $db->prepare($sql);
         $stmt->execute();
         $row = $stmt->fetch();

         if(!(_USER_PERMISSIONS&perm::puo_eliminare_messaggi)){
            if($row["id_utente"]<>_USER_ID){
                $res=array("result"=>"KO", "msg"=>"Non puoi" );
                echo json_encode($res);
                die();
            }
         }
         if(CAST_TO_INT($row["is_hidden"])>0){
            $result="SHOW";
            $sql="UPDATE retegas_bacheca set is_hidden=0 WHERE id_bacheca='$id_post' LIMIT 1;";
         }else{
            $sql="UPDATE retegas_bacheca set is_hidden=1 WHERE id_bacheca='$id_post' LIMIT 1;";
            $result="HIDE";
         }
         $stmt = $db->prepare($sql);
         $stmt->execute();
         $res=array("result"=>"OK", "msg"=>$result);
         echo json_encode($res);
    break;

    //---------------------------------------LIKED
    case "show_bacheca_liked":
        $filter = CAST_TO_STRING($_POST["filter"]);
        $page = CAST_TO_INT($_POST["page"],1);

        $limit = CAST_TO_INT($_POST["limit"],0);
        if($limit==0){
            $limit=10;
        }


        $from = ($page-1)*$limit;


    $G = new gas(_USER_ID_GAS);
    // (SELECT COUNT(*) FROM retegas_bacheca B2 where B2.id_utente=B.id_utente) as messaggi

    $sql="SELECT    B.id_bacheca,
                    B.id_utente,
                    B.titolo_messaggio,
                    B.messaggio,
                    B.timbro_bacheca,
                    B.id_ordine,
                    B.is_hidden,
                    U.fullname,
                    G.descrizione_gas,
                    D.descrizione_ditte,
                    G.id_gas
                FROM retegas_bacheca B
                INNER JOIN retegas_options O on  O.id_bacheca=B.id_bacheca
                INNER JOIN maaking_users U on U.userid=B.id_utente
                INNER JOIN retegas_gas G on G.id_gas=U.id_gas
                LEFT JOIN retegas_ditte D on D.id_ditte = B.id_ditta
          WHERE O.chiave='_USER_POST_LIKED' AND O.id_user='"._USER_ID."' ORDER BY timbro_bacheca DESC LIMIT $limit OFFSET $from;";

    //echo $sql; die();
    //$stmt->bindParam(':userid', $row["id_proponente"], PDO::PARAM_INT);
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $posts = $stmt->fetchAll();
    $i=0;
    $id_utente = _USER_ID;
foreach($posts as $p){

    if(CAST_TO_STRING($p["titolo_messaggio"])<>""){
        $titolo=$p["titolo_messaggio"]."<br>";
    }else{
        $titolo='';
    }

    if(CAST_TO_STRING($p["messaggio"])<>""){
        $messaggio=$p["messaggio"];
    }else{
        $messaggio='';
    }

    if(CAST_TO_STRING($p["descrizione_ditte"])<>""){
        $ditta ='Merce da <strong>'.$p["descrizione_ditte"].'</strong> ';
    }else{
        $ditta ='';
    }

    if(CAST_TO_INT($p["id_ordine"])>0){
        $ordine =' ordine <strong>#'.$p["id_ordine"].'</strong>';
    }else{
        $ordine ='';
    }

    $data = conv_datetime_from_db($p["timbro_bacheca"]);

    $show_post=true;
    $hidden = '';
    if(CAST_TO_INT($p["is_hidden"])>0){
        if(_USER_PERMISSIONS & perm::puo_eliminare_messaggi){
            $hidden = ' style="opacity:0.3" ';
        }else{
            $show_post=false;
        }
    }

    if($show_post){
    $i++;


        $stmt = $db->prepare("SELECT COUNT(valore_int) as conto FROM retegas_options
                             WHERE id_bacheca=:id_bacheca AND id_user=:id_utente AND chiave='_USER_POST_LIKED'");
        $stmt->bindParam(':id_bacheca', $p["id_bacheca"], PDO::PARAM_INT);
        $stmt->bindParam(':id_utente', $id_utente, PDO::PARAM_INT);
        $stmt->execute();
        $rowL = $stmt->fetch();
        if($rowL["conto"]>0){
            $star='fa-star';
        }else{
            $star='fa-star-o';
        }
        $liked_post='&nbsp;<i class="icona_liked fa '.$star.' text-success liked_post" data-id_post="'.$p["id_bacheca"].'" style="cursor:pointer"></i>&nbsp;';

    if(_USER_ID==$p["id_utente"] | _USER_PERMISSIONS & perm::puo_eliminare_messaggi){
        $delete_post='&nbsp;<i class="fa fa-times text-danger delete_post" data-id_post="'.$p["id_bacheca"].'" style="cursor:pointer"></i>&nbsp;';
        $hide_post ='&nbsp;<i class="fa fa-eye text-info hide_post" data-id_post="'.$p["id_bacheca"].'" style="cursor:pointer"></i>&nbsp;';
        $edit_post ='&nbsp;<i class="fa fa-pencil text-warning edit_post" data-id_post="'.$p["id_bacheca"].'" style="cursor:pointer"></i>&nbsp;';

    }else{
        $delete_post='';
        $hide_post='';
        $edit_post='';
    }


    $post .= '      <!-- Post -->
                    <tr class="post_item" '.$hidden.' data-id_post="'.$p["id_bacheca"].'">
                        <td class="text-center">
                            <div class="push-bit">
                                <a href="#ajax_rd4/user/scheda.php?id='.$converter->encode($p["id_utente"]).'">
                                    <img src="'.src_user($p["id_utente"]).'" width="40" alt="'.$p["id_utente"].'" class="margin-top-5">
                                </a>
                                <img src="'.src_gas($p["id_gas"]).'" width="40" alt="'.$p["id_gas"].'" class="margin-top-5">
                            </div>
                        </td>
                        <td>
                            <span class="pull-right">
                            '.$edit_post.'
                            '.$delete_post.'
                            '.$hide_post.'
                            '.$liked_post.'
                            </span>

                            <span class="text-muted">
                                <em>'.$data.'</em>, <strong>'.$p["fullname"].'</strong>,  '.$p["descrizione_gas"].'<br>
                                '.$ditta.' '.$ordine.'<br>
                            </span>
                            <div class="messaggio" data-id_post="'.$p["id_bacheca"].'">
                                '.$titolo.'
                                '.$messaggio.'
                            </div>
                        </td>
                    </tr>
                    <!-- end Post -->';
        }//Show post
    }

    $post .= '<tr class="post_item">
                <td colspan=2>
                    <p class="text-center">
                         <button class="show_posts btn btn-success" data-page="'.($page+1).'" data-gas="'.$gas.'" data-id_ordine="'.$id_ordine.'" data-utente="'.$utente.'" data-id_ditta="'.$id_ditta.'"> Mostra altri post </button>
                    </p>
                </td>
                </tr>';
    if($i==0){
        $post = '<tr class="post_item">
                <td colspan=2>
                    <p class="text-center">
                        Nessun elemento da mostrare
                    </p>
                </td>
                </tr>';
    }

    if(!(_USER_PERMISSIONS&perm::puo_vedere_retegas)){$sql='';}

    $res=array("result"=>"OK", "msg"=>$msg, "post"=>$post, "sql"=>$sql );
    echo json_encode($res);
    break;

case "show_bacheca":
        $filter = CAST_TO_STRING($_POST["filter"]);
        $page = CAST_TO_INT($_POST["page"],1);

        $limit = CAST_TO_INT($_POST["limit"],0);
        if($limit==0){
            $limit=10;
        }

        $id_post=CAST_TO_INT($_POST["id_post"],0);
        if($id_post==0){
            $gas=CAST_TO_INT($_POST["gas"],0);
            if($gas>0){
                $where_gas = " AND B.id_gas="._USER_ID_GAS." AND id_ordine=0 AND id_ditta=0 ";
            }else{
                $where_gas = " AND B.id_gas="._USER_ID_GAS." ";
            }

            $id_ordine = CAST_TO_INT($_POST["id_ordine"],0);
            if($id_ordine>0){
                $where_ordine = " AND id_ordine=".$id_ordine." ";
                $where_gas = "";
            }

            $id_ditta = CAST_TO_INT($_POST["id_ditta"],0);
            if($id_ditta>0){
                $where_ditta = " AND id_ditta=".$id_ditta." AND id_ordine=0 ";
                $where_gas = "";
            }

            $utente =CAST_TO_INT($_POST["utente"],0);
            if($utente>0){
                $where_utente = " AND id_utente=".$utente." ";
                $where_gas = "";
            }
        }else{
           $where_id_post = " AND id_bacheca=".$id_post." ";
           $where_gas = " AND B.id_gas="._USER_ID_GAS." ";
        }



        $from = ($page-1)*$limit;


    $G = new gas(_USER_ID_GAS);
    // (SELECT COUNT(*) FROM retegas_bacheca B2 where B2.id_utente=B.id_utente) as messaggi

    $sql="SELECT    B.id_bacheca,
                    B.id_utente,
                    B.titolo_messaggio,
                    B.messaggio,
                    B.timbro_bacheca,
                    B.id_ordine,
                    B.is_hidden,
                    B.is_vetrina,
                    U.fullname,
                    G.descrizione_gas,
                    D.descrizione_ditte,
                    G.id_gas
                FROM retegas_bacheca B
                INNER JOIN maaking_users U on U.userid=B.id_utente
                INNER JOIN retegas_gas G on G.id_gas=U.id_gas
                LEFT JOIN retegas_ditte D on D.id_ditte = B.id_ditta
          WHERE  1=1 ".$where_id_post." ".$where_gas." ".$where_ordine." ".$where_utente." ".$where_ditta." ORDER BY timbro_bacheca DESC LIMIT $limit OFFSET $from;";

    //echo $sql; die();
    //$stmt->bindParam(':userid', $row["id_proponente"], PDO::PARAM_INT);
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $posts = $stmt->fetchAll();
    $i=0;
    $id_utente = _USER_ID;
foreach($posts as $p){

    if(CAST_TO_STRING($p["titolo_messaggio"])<>""){
        $titolo=$p["titolo_messaggio"]."<br>";
    }else{
        $titolo='';
    }

    if(CAST_TO_STRING($p["messaggio"])<>""){
        $messaggio=$p["messaggio"];
    }else{
        $messaggio='';
    }

    if(CAST_TO_STRING($p["descrizione_ditte"])<>""){
        $ditta ='Merce da <strong>'.$p["descrizione_ditte"].'</strong> ';
    }else{
        $ditta ='';
    }

    if(CAST_TO_INT($p["id_ordine"])>0){
        $ordine =' ordine <strong>#'.$p["id_ordine"].'</strong>';
    }else{
        $ordine ='';
    }

    $data = conv_datetime_from_db($p["timbro_bacheca"]);

    $show_post=true;
    $hidden = '';
    if(CAST_TO_INT($p["is_hidden"])>0){
        if(_USER_PERMISSIONS & perm::puo_eliminare_messaggi){
            $hidden = ' style="opacity:0.3" ';
        }else{
            $show_post=false;
        }
    }

    if($show_post){
    $i++;


        $stmt = $db->prepare("SELECT COUNT(valore_int) as conto FROM retegas_options
                             WHERE id_bacheca=:id_bacheca AND id_user=:id_utente AND chiave='_USER_POST_LIKED'");
        $stmt->bindParam(':id_bacheca', $p["id_bacheca"], PDO::PARAM_INT);
        $stmt->bindParam(':id_utente', $id_utente, PDO::PARAM_INT);
        $stmt->execute();
        $rowL = $stmt->fetch();
        if($rowL["conto"]>0){
            $star='fa-star';
        }else{
            $star='fa-star-o';
        }
        $liked_post='&nbsp;<i class="icona_liked fa '.$star.' text-success liked_post" data-id_post="'.$p["id_bacheca"].'" style="cursor:pointer"></i>&nbsp;';

    if($p["is_vetrina"]>0){
            $fa_toggle=' fa-toggle-on ';
        }else{
            $fa_toggle=' fa-toggle-off ';
        }

    $link_post='&nbsp;<a href="#ajax_rd4/bacheca/post_bacheca.php?id='.$p["id_bacheca"].'"><i class="fa fa-link"></i></a>&nbsp;';


    if(_USER_ID==$p["id_utente"] | _USER_PERMISSIONS & perm::puo_eliminare_messaggi){
        $delete_post='&nbsp;<i class="fa fa-times text-danger delete_post" data-id_post="'.$p["id_bacheca"].'" style="cursor:pointer"></i>&nbsp;';
        $hide_post ='&nbsp;<i class="fa fa-eye text-info hide_post" data-id_post="'.$p["id_bacheca"].'" style="cursor:pointer"></i>&nbsp;';
        $edit_post ='&nbsp;<i class="fa fa-pencil text-warning edit_post" data-id_post="'.$p["id_bacheca"].'" style="cursor:pointer"></i>&nbsp;';
        $vetrina_post = '&nbsp;<i class="fa '.$fa_toggle.' text-info vetrina_post" data-id_post="'.$p["id_bacheca"].'" style="cursor:pointer"></i>&nbsp;';
    }else{
        $delete_post='';
        $hide_post='';
        $edit_post='';
        $vetrina_post = '';
    }



    if($id_post<>0){
        //$delete_post='';
        //$hide_post='';
        //$edit_post='';
        //$vetrina_post = '';
        $link_post='';
    }


    $post .= '      <!-- Post -->
                    <tr class="post_item" '.$hidden.' data-id_post="'.$p["id_bacheca"].'">
                        <td class="text-center">
                            <div class="push-bit">
                                <a href="#ajax_rd4/user/scheda.php?id='.$converter->encode($p["id_utente"]).'">
                                    <img src="'.src_user($p["id_utente"]).'" width="40" alt="'.$p["id_utente"].'" class="margin-top-5">
                                </a>
                                <img src="'.src_gas($p["id_gas"]).'" width="40" alt="'.$p["id_gas"].'" class="margin-top-5">
                            </div>
                        </td>
                        <td>
                            <span class="pull-right">
                            '.$vetrina_post.'
                            '.$edit_post.'
                            '.$delete_post.'
                            '.$hide_post.'
                            '.$liked_post.'
                            '.$link_post.'
                            </span>

                            <span class="text-muted">
                                <em>'.$data.'</em>, <strong>'.$p["fullname"].'</strong>,  '.$p["descrizione_gas"].'<br>
                                '.$ditta.' '.$ordine.'<br>
                            </span>
                            <div class="messaggio" data-id_post="'.$p["id_bacheca"].'">
                                '.$titolo.'
                                '.$messaggio.'
                            </div>
                        </td>
                    </tr>
                    <!-- end Post -->';
        }//Show post
    }

    if($id_post==0){
        $post .= '<tr class="post_item">
                <td colspan=2>
                    <p class="text-center">
                         <button class="show_posts btn btn-success" data-page="'.($page+1).'" data-gas="'.$gas.'" data-id_ordine="'.$id_ordine.'" data-utente="'.$utente.'" data-id_ditta="'.$id_ditta.'"> Mostra altri post </button>
                    </p>
                </td>
                </tr>';

        if($i==0){
            $post = '<tr class="post_item">
                    <td colspan=2>
                        <p class="text-center">
                            Nessun elemento da mostrare
                        </p>
                    </td>
                    </tr>';
        }
    }
    if(!(_USER_PERMISSIONS&perm::puo_vedere_retegas)){$sql='';}

    $res=array("result"=>"OK", "msg"=>$msg, "post"=>$post, "sql"=>$sql );
    echo json_encode($res);
    break;

    //------------------------------------------------------------------------------------
    case "show_vetrina":


    $gas=_USER_ID_GAS;
    $where_gas = " AND B.id_gas="._USER_ID_GAS." AND id_ordine=0 AND id_ditta=0 ";



    $G = new gas(_USER_ID_GAS);
    // (SELECT COUNT(*) FROM retegas_bacheca B2 where B2.id_utente=B.id_utente) as messaggi

    $sql="SELECT    B.id_bacheca,
                    B.id_utente,
                    B.titolo_messaggio,
                    B.messaggio,
                    B.timbro_bacheca,
                    B.id_ordine,
                    B.is_vetrina,
                    B.is_hidden,
                    U.fullname,
                    G.descrizione_gas,
                    D.descrizione_ditte,
                    G.id_gas
                FROM retegas_bacheca B
                INNER JOIN maaking_users U on U.userid=B.id_utente
                INNER JOIN retegas_gas G on G.id_gas=U.id_gas
                LEFT JOIN retegas_ditte D on D.id_ditte = B.id_ditta
                WHERE  B.is_vetrina=1  ".$where_gas." AND B.is_hidden=0 ORDER BY id_bacheca DESC LIMIT 1;";

    //echo $sql; die();
    //$stmt->bindParam(':userid', $row["id_proponente"], PDO::PARAM_INT);
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $posts = $stmt->fetchAll();
    $i=0;
    $id_utente = _USER_ID;
foreach($posts as $p){

    if(CAST_TO_STRING($p["titolo_messaggio"])<>""){
        $titolo=$p["titolo_messaggio"]."<br>";
    }else{
        $titolo='';
    }

    if(CAST_TO_STRING($p["messaggio"])<>""){
        $messaggio=$p["messaggio"];
    }else{
        $messaggio='';
    }


    $data = conv_datetime_from_db($p["timbro_bacheca"]);

    $show_post=true;

    if($show_post){
    $i++;


        $stmt = $db->prepare("SELECT COUNT(valore_int) as conto FROM retegas_options
                             WHERE id_bacheca=:id_bacheca AND id_user=:id_utente AND chiave='_USER_POST_LIKED'");
        $stmt->bindParam(':id_bacheca', $p["id_bacheca"], PDO::PARAM_INT);
        $stmt->bindParam(':id_utente', $id_utente, PDO::PARAM_INT);
        $stmt->execute();
        $rowL = $stmt->fetch();
        if($rowL["conto"]>0){
            $star='fa-star';
        }else{
            $star='fa-star-o';
        }
        $liked_post='&nbsp;<i class="icona_liked fa '.$star.' text-success liked_post" data-id_post="'.$p["id_bacheca"].'" style="cursor:pointer"></i>&nbsp;';
        $link_post='&nbsp;<a href="#ajax_rd4/bacheca/post_bacheca.php?id='.$p["id_bacheca"].'"><i class="fa fa-link"></i></a>&nbsp;';


    $post .= '      <!-- Post -->
                    <tr class="post_item" '.$hidden.' data-id_post="'.$p["id_bacheca"].'">
                        <td class="text-center">
                            <div class="push-bit">
                                <a href="#ajax_rd4/user/scheda.php?id='.$converter->encode($p["id_utente"]).'">
                                    <img src="'.src_user($p["id_utente"]).'" width="40" alt="'.$p["id_utente"].'" class="margin-top-5">
                                </a>
                                <img src="'.src_gas($p["id_gas"]).'" width="40" alt="'.$p["id_gas"].'" class="margin-top-5">
                            </div>
                        </td>
                        <td>
                            <span class="pull-right">
                            '.$edit_post.'
                            '.$delete_post.'
                            '.$hide_post.'
                            '.$liked_post.'
                            '.$link_post.'
                            </span>
                            <span class="text-muted">
                                <em>'.$data.'</em>, <strong>'.$p["fullname"].'</strong><br>
                            </span>
                            <div class="messaggio" data-id_post="'.$p["id_bacheca"].'">
                                '.$messaggio.'
                            </div>
                        </td>
                    </tr>
                    <!-- end Post -->';
        }//Show post
    }

    if(_USER_PERMISSIONS & perm::puo_postare_messaggi){
        if($i==0){
            $post='<div class="alert alert-info margin-top-10" style="max-height:200px; overflow-y:auto;"><i class="fa fa-comments"></i>&nbsp;Clicca <strong><a href="#ajax_rd4/gas/gas_bacheca.php">qua</a></strong> per inserire una comunicazione visibile a tutto il tuo GAS</div>';
        }
    }

    if(!(_USER_PERMISSIONS&perm::puo_vedere_retegas)){$sql='';}

    $res=array("result"=>"OK", "msg"=>$msg, "post"=>$post, "sql"=>$sql );
    echo json_encode($res);
    break;

    //

    default :
    $res=array("result"=>"KO", "msg"=>"Comando '".$_POST["act"]."' non riconosciuto" );
    echo json_encode($res);
    break;

}