<?php
require_once("inc/init.php");
require_once("../../lib_rd4/class.rd4.gas.php");
$converter = new Encryption;
$page_title = "Importa utenti";
$page_id = "importa_utenti";

$id_gas = CAST_TO_INT($_GET["id_gas"]);
$file = CAST_TO_STRING($_GET["f"]);
$extension = CAST_TO_STRING($_GET["e"]);
$filename = "UTE_".$file."_RD4.".$extension;

if($_GET["s"]=="e"){
    $show_all = false;
}else{
    $show_all = true;
}
if($_GET["do"]=="ins"){
    $insert = true;
}else{
    $insert = false;
}
if(!file_exists("../../public_rd4/users_import/".$filename)){echo "file missing...";die();}

if($_GET["act"]=="check"){
            //arico excel.php
             require_once('../../lib_rd4/PHPExcel.php');

             /** Load $inputFileName to a PHPExcel Object  **/


             $objPHPExcel = PHPExcel_IOFactory::load("../../public_rd4/users_import/".$filename);
             $rowIterator = $objPHPExcel->getActiveSheet()->getRowIterator(2);
             $worksheet = $objPHPExcel->getActiveSheet();

             $riga=0;
             $username_ko = 0;
             $email_ko = 0;
             $username_zero = 0;
             $username_lungo=0;
             $email_zero = 0;
             $fullname_zero = 0;
             $telefono_zero = 0;
             $errore_riga = 0;

             $G = new gas(_USER_ID_GAS);
             $permessi = $G->default_permission;
             $gas_usa_cassa = $G->gas_usa_cassa;

             $t.='  <table class="table table-bordered table-condensed">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Username (*)</th>
                                <th>Email (*)</th>
                                <th>Nome e cognome (*)</th>
                                <th>Password (*)</th>
                                <th>Telefono (*)</th>
                                <th>Note</th>
                                <th>Tessera</th>
                                <th>CUSTOM 1</th>
                                <th>CUSTOM 2</th>
                                <th>CUSTOM 3</th>
                            </tr>
                        </thead>
                        <tbody>
                    ';

             $inserted=0;
             $lastRow = $worksheet->getHighestRow();
             for ($row = 2; $row <= $lastRow; $row++) {

                    $riga++;
                    //$t.='<tr>';
                    //USERNAME
                    $cell = $worksheet->getCell('A'.$row);
                    $value= $cell->getValue();
                    $username=html_entity_decode(CAST_TO_STRING($value));
                    if($username==""){
                        $username_vuoto++;
                        $errore_riga++;
                        $username='<i class="fa fa-question-circle text-danger"></i>';
                    }else{


                        //CONTROLLO USERNAME NON UNIVOCO
                        $sql = "SELECT count(*) as c FROM  maaking_users WHERE  username =:username;";
                        $stmt = $db->prepare($sql);
                        $stmt->bindParam(':username', $username, PDO::PARAM_STR);

                        $stmt->execute();
                        $rowC = $stmt->fetch(PDO::FETCH_ASSOC);
                        if($rowC["c"]>0){
                            $username_ko++;
                            $errore_riga++;
                            $username_err_class= 'text-danger bold';
                        }else{
                            //SE NON E' GIA' NEL FILE
                            if(in_array($username,$array_usernames)){
                                $username_ko++;
                                $errore_riga++;
                                $username_err_class= 'text-danger bold';
                            }else{
                                if(strlen($username)>15){
                                    $username_lungo++;
                                    $errore_riga++;
                                    $username_err_class= 'text-danger bold';
                                }else{
                                    $array_usernames[]=$username;
                                    $username_err_class= '';
                                }

                            }
                        }


                    }
                    $r.='<td class="'.$username_err_class.'">'.$username.'</td>';

                    //EMAIL
                    $cell = $worksheet->getCell('B'.$row);
                    $value= $cell->getValue();
                    $email=html_entity_decode(CAST_TO_STRING($value));
                    if($email==""){
                        $email_vuoto++;
                        $errore_riga++;
                        $email='<i class="fa fa-question-circle  text-danger"></i>';
                    }else{
                        //CONTROLLO EMAIL NON UNIVOCO
                        $sql = "SELECT count(*) as c FROM  maaking_users WHERE  email =:email;";
                        $stmt = $db->prepare($sql);
                        $stmt->bindParam(':email', $email, PDO::PARAM_STR);

                        $stmt->execute();
                        $rowC = $stmt->fetch(PDO::FETCH_ASSOC);
                        if($rowC["c"]>0){
                            $email_ko++;
                            $errore_riga++;
                            $email_err_class= 'text-danger bold';
                        }else{
                            //SE NON E' GIA' NEL FILE
                            if(in_array($email,$array_emails)){
                                $email_ko++;
                                $errore_riga++;
                                $email_err_class= 'text-danger bold';
                            }else{
                                $array_emails[]=$email;
                                $email_err_class= '';
                            }
                        }
                    }
                    $r.='<td class="'.$email_err_class.'">'.$email.'</td>';

                    //FULLNAME - C
                    $cell = $worksheet->getCell('C'.$row);
                    $value= $cell->getValue();
                    $fullname = html_entity_decode(clean(CAST_TO_STRING($value)));
                    if($fullname==""){
                        $fullname_vuota++;
                        $errore_riga++;
                        $fullname='<i class="fa fa-question-circle text-danger"></i>';
                    }
                    $r.='<td>'.$fullname.'</td>';

                    //PASSWORD - D
                    $cell = $worksheet->getCell('D'.$row);
                    $value= $cell->getValue();
                    $password = html_entity_decode(clean(CAST_TO_STRING($value)));
                    if($password==""){
                        $password_vuota++;
                        $errore_riga++;
                        $password='<i class="fa fa-question-circle  text-danger"></i>';
                    }
                    $r.='<td>'.$password.'</td>';

                    //TELEFONO - E
                    $cell = $worksheet->getCell('E'.$row);
                    $value= $cell->getValue();
                    $telefono = html_entity_decode(clean(CAST_TO_STRING($value)));
                    if($telefono==""){
                        $telefono_vuota++;
                        $errore_riga++;
                        $telefono='<i class="fa fa-question-circle  text-danger"></i>';
                    }
                    $r.='<td>'.$telefono.'</td>';

                    // 8 NOTE BREVI - F
                    $cell = $worksheet->getCell('F'.$row);
                    $value= $cell->getValue();
                    $note = html_entity_decode((CAST_TO_STRING(clean($value))));
                    $r.='<td><small>'.$note.'</small></td>';

                    // 9 TESSERA - G
                    $cell = $worksheet->getCell('G'.$row);
                    $value= $cell->getValue();
                    $tessera = html_entity_decode((CAST_TO_STRING(clean($value))));
                    $r.='<td><small>'.$tessera.'</small></td>';

                    // 10 CUSTOM_1 - H
                    $cell = $worksheet->getCell('H'.$row);
                    $value= $cell->getValue();
                    $custom_1 = html_entity_decode((CAST_TO_STRING(clean($value))));
                    $r.='<td>'.$custom_1.'</td>';


                    //11 CUSTOM 2 - I
                    $cell = $worksheet->getCell('I'.$row);
                    $value= $cell->getValue();
                    $custom_2 = html_entity_decode((CAST_TO_STRING(clean($value))));
                    $r.='<td>'.$custom_2.'</td>';

                    //12 CUSTOM_3 - J
                    $cell = $worksheet->getCell('J'.$row);
                    $value= $cell->getValue();
                    $custom_3 = html_entity_decode((CAST_TO_STRING(clean($value))));
                    $r.='<td>'.$custom_3.'</td>';



                    if($errore_riga>0){

                        $riga_sbagliata++;
                        $t.='<tr><td><i class="fa fa-times text-danger"></i></td>'.$r.'</tr>
                        ';
                    }else{

                        if($show_all){
                            $t.='<tr><td><i class="fa fa-check text-success"></i></td>'.$r.'</tr>
                            ';
                        }
                    if($insert){

                        $code = rand(10000000,99999999);
                        $codeLINK = APP_URL.'/ajax_rd4/?do=c&c='.$code;

                        $stmt = $db->prepare("INSERT INTO maaking_users
                                        (username,
                                         password,
                                         email,
                                         fullname,
                                         regdate,
                                         isactive,
                                         code,
                                         id_gas,
                                         consenso,
                                         tel,
                                         user_permission,
                                         user_site_option,
                                         tessera,
                                         custom_1,
                                         custom_2,
                                         custom_3)
                                         VALUES
                                        (:username,
                                         :md5password,
                                         :email,
                                         :fullname,
                                         NOW(),
                                         '1',
                                         :code,
                                         :id_gas,
                                         '1',
                                         :tel,
                                         :permessi,
                                         '31',
                                         :tessera,
                                         :custom_1,
                                         :custom_2,
                                         :custom_3);");
                         $md5password = md5($password);
                         $id_gas=_USER_ID_GAS;

                        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                        $stmt->bindParam(':md5password', $md5password, PDO::PARAM_STR);
                        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                        $stmt->bindParam(':fullname', $fullname, PDO::PARAM_STR);
                        $stmt->bindParam(':tel', $telefono, PDO::PARAM_STR);

                        $stmt->bindParam(':tessera', $tessera, PDO::PARAM_STR);
                        $stmt->bindParam(':custom_1', $custom_1, PDO::PARAM_STR);
                        $stmt->bindParam(':custom_2', $custom_2, PDO::PARAM_STR);
                        $stmt->bindParam(':custom_3', $custom_3, PDO::PARAM_STR);

                        $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
                        $stmt->bindParam(':code', $code, PDO::PARAM_INT);
                        $stmt->bindParam(':permessi', $permessi, PDO::PARAM_INT);



                        $stmt->execute();
                        //PRENDO ID DEL NUOVO UTENTE
                        $newid = $db->lastInsertId();

                        if($stmt->rowCount()==1){
                            $inserted++;

                            //CASSA
                            //SE IL GAS HA LA CASSA INSERISCO L'OPZIONE NELL'UTENTE
                            if($gas_usa_cassa){
                                if($newid>0){
                                    $stmt = $db->prepare("INSERT INTO retegas_options (id_user,chiave,valore_text)
                                    VALUES (".$newid.",'_USER_USA_CASSA','SI')");
                                    $stmt->execute();
                                }
                            }

                            //MAIL AL POSTINO

                            $mailFROM = _USER_MAIL;
                            $fullnameFROM = _USER_FULLNAME;

                            $mailTO = $email;
                            $fullnameTO = $fullname;

                            $oggetto = "[reteDES] nuovo account creato da ".$fullnameFROM;
                            $profile = new Template('../../email_rd4/nuovo_utente_da_gas.html');

                            $profile->set("fullnameFROM", $fullnameFROM );
                            $profile->set("gasNOME", _USER_GAS_NOME );
                            $profile->set("newUSERNAME", $username );
                            $profile->set("newPASSWORD", $password );
                            $profile->set("newFULLNAME", $fullname );
                            $profile->set("newTEL", $tel );
                            $profile->set("newCODE", $codeLINK );

                            $messaggio = $profile->output();
                            //INVIO RITARDATO.
                            SPostino($fullnameTO,$mailTO,$fullnameFROM,$mailFROM,$oggetto,$messaggio,"CreatoAccount");

                            }
                        }
                    }//ERRORE RIGA=0

                    $errore_riga=0;
                    $r='';
                    //$t.='</tr>';

             }

             if($riga_sbagliata>0){
                $panel='<div class="well">
                    <a class="btn btn-default pull-left" href="#ajax_rd4/gas/gas_utenti_2.php">Torna alla scheda GAS</a>
                    <a class="btn btn-default pull-right" href="#ajax_rd4/gas/upload_act.php?id='.$id_listino.'&f='.$file.'&e='.$extension.'&act=check&s=e">Mostra solo errori</a>
                    <div class="clearfix"></div>
                    </div>
                    ';
             }else{
               $panel='<div class="jumbotron text-center"><button id="do_upload_users" class="btn btn-xl btn-success"><h1><i class="fa fa-group"></i>   Carica!</h1></button></div>';
             }

             $t.='  </tbody>
                </table>';



             $objPHPExcel->disconnectWorksheets();
             unset($objPHPExcel);



}
if($insert){
    if($inserted==$riga){
        $res=array("result"=>"OK", "msg"=>"$inserted su $riga utenti caricati " );
    }else{
        $res=array("result"=>"KO", "msg"=>"$inserted su $riga utenti caricati " );
    }

    unlink("../../public_rd4/users_import/".$filename);

    echo json_encode($res);
    die();
}


?>
<div class="inbox-nav-bar no-content-padding">
    <h1 class="page-title txt-color-blueDark"><i class="fa fa-fw fa-upload"></i> Importa utenti  &nbsp;</h1>
</div>
<div class="margin-top-10">
    <?php echo $panel; ?>
</div>
<hr>
<div class="margin-top-10 " style="overflow-x:auto !important">
    <?php echo $t; ?>
</div>


<script type="text/javascript">

    pageSetUp();



    var pagefunction = function(){

        $('body').removeClass('modal-open');

        $('#do_upload_users').click(function(){
            console.log("do_upload");
            $.ajax({
                          type: "GET",
                          url: "ajax_rd4/gas/upload_act.php?id_gas=<?php echo _USER_ID_GAS?>&f=<?php echo $file?>&e=<?php echo $extension; ?>&act=check&do=ins",
                          dataType: 'json',

                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                okWait(data.msg);
                                location.replace('<?php echo APP_URL; ?>/#ajax_rd4/gas/gas_utenti.php');
                            }else{
                                ko(data.msg);
                                loadURL("ajax_rd4/gas/gas_utenti_2.php?id=<?php echo _USER_ID_GAS?>",$('#content'));
                            ;}

                        });
        })

    } // end pagefunction



    pagefunction();
</script>
