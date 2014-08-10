<?php

//Movimenti CASSA --> TESTO
$__movcas = array(  "0"=>"Non definito",
                    "1"=>"Ricarica",
                    "2"=>"Pagamento",
                    "3"=>"Rettifica",
                    "4"=>"Scarico",
                    "5"=>"Finanziamento GAS",
                    "6"=>"Restituzione Credito",
                    "7"=>"Scarico (Merce)",
                    "8"=>"Scarico (Trasporto)",
                    "9"=>"Scarico (Gestione)",
                    "10"=>"Scarico (Finanziamento GAS)",
                    "11"=>"Scarico (Anticipo per copertura)",
                    "12"=>"Scarico (Costi GAS)",
                    "13"=>"Scarico (Maggiorazione GAS)");

class perm{
        const puo_creare_ordini         =1;     //0
        const puo_partecipare_ordini    =2;     //1
        const puo_creare_gas            =4;     //2     SUPER USER
        const puo_creare_ditte          =8;     //3
        const puo_creare_listini        =16;    //4
        const puo_mod_perm_user_gas     =32;    //5     SUPER USER
        const puo_vedere_messaggi       =64;    //6     SUPER USER
        const puo_vedere_users_attesa   =128;   //7     SUPER USER
        const puo_avere_amici           =256;   //8
        const puo_postare_messaggi      =512;   //9
        const puo_eliminare_messaggi    =1024;  //10    SUPER USER
        const puo_gestire_utenti        =2048;  //11    SUPER USER
        const puo_vedere_tutti_ordini   =4096;  //12    SUPER USER
        const puo_gestire_la_cassa      =8192;  //13
        const puo_operare_con_crediti   =16384; //14
        const puo_vedere_retegas        =32768; //15    SUPER USER
        const puo_gestire_retegas       =65536; //16    ZEUS USER
    }
function conv_datetime_from_db ($data){
  //list ($y, $m, $d) = explode ("-", $data);
  //return "$d/$m/$y";   YYYY-MM-DD
  $y=substr($data, 0, 4);
  $m=substr($data, 5, 2);
  $d=substr($data, 8, 2);
  $h=substr($data, 11, 2);
  $min=substr($data, 14, 2);
  $sec=substr($data, 17, 2);

  if(empty($h)){$h="00";}
  if(empty($min)){$min="00";}
  if(empty($sec)){$sec="00";}


  return "$d/$m/$y $h:$min";//":$sec";




}
function IsLoggedIn(){
    global $db;

    $read_cookie = explode("|", base64_decode($_COOKIE["user"]));
    $userid = addslashes($read_cookie[0]);
    $user_id = ($read_cookie[0]);
    $passwd = $read_cookie[2];
    $userid = intval($userid);

    if ($userid != "" AND $passwd != "") {
        //$result = $db->sql_query("SELECT * FROM maaking_users WHERE userid='$userid'");
        //$row = $db->sql_fetchrow($result);

        $stmt = $db->prepare("SELECT * FROM maaking_users WHERE userid=:id LIMIT 1;");
        $stmt->bindValue(':id', $userid, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);


        $pass = $row['password'];
    if($pass == $passwd && $pass != "") {

           //USER

           define("_USER_PERMISSIONS",$row["user_permission"]);
           define("_USER_OPTIONS",$row["user_site_option"]);
           define("_USER_LOGGED_IN",true);
           define("_USER_ID",$user_id);
           define("_USER_TEL",$row["tel"]);
           define("_USER_INDIRIZZO",$row["country"]);
           define("_USER_CITTA",$row["city"]);
           define("_USER_ID_GAS",$row["id_gas"]);
           define("_USER_FULLNAME",$row["fullname"]);
           define("_USER_USERNAME",$row["username"]);
           define("_USER_PASSWORD",$row["password"]);
           define("_USER_MAIL",$row["email"]);
           define("_USER_LAST_LOGIN",$row["lastlogin"]);
           define("_USER_LAT",$row["user_gc_lat"]);
           define("_USER_LNG",$row["user_gc_lng"]);

           //USER CASSA
           $stmt = $db->prepare("SELECT * FROM retegas_options WHERE id_user="._USER_ID." AND chiave='_USER_USA_CASSA' LIMIT 1;");
           $stmt->execute();
           $row = $stmt->fetch(PDO::FETCH_ASSOC);
           define("_USER_USA_CASSA",$row["valore_text"]=='SI' ? true: false);

           //USER PERMETTE MODIFICA
           $stmt = $db->prepare("SELECT * FROM retegas_options WHERE id_user="._USER_ID." AND chiave='_USER_PERMETTI_MODIFICA' LIMIT 1;");
           $stmt->execute();
           $row = $stmt->fetch(PDO::FETCH_ASSOC);
           define("_USER_PERMETTI_MODIFICA",$row["valore_text"]=='SI' ? true: false);

           //USER DAY_ALERTS _USER_ALERT_DAYS
           $stmt = $db->prepare("SELECT * FROM retegas_options WHERE id_user="._USER_ID." AND chiave='_USER_ALERT_DAYS' LIMIT 1;");
           $stmt->execute();
           $row = $stmt->fetch(PDO::FETCH_ASSOC);
           define("_USER_ALERT_DAYS",$row["valore_int"]);

           //GAS
           $stmt = $db->prepare("SELECT * FROM retegas_gas WHERE id_gas=:id LIMIT 1;");
           $stmt->bindValue(':id', _USER_ID_GAS, PDO::PARAM_INT);
           $stmt->execute();
           $row = $stmt->fetch(PDO::FETCH_ASSOC);

           define("_USER_GAS_NOME",$row["descrizione_gas"]);
           define("_USER_GAS_MAIL",$row["mail_gas"]);
           define("_USER_GAS_LAT",$row["gas_gc_lat"]);
           define("_USER_GAS_LNG",$row["gas_gc_lng"]);
           define("_USER_GAS_ID_DES",$row["id_des"]);

           //GAS CASSA
           $stmt = $db->prepare("SELECT * FROM retegas_options WHERE id_gas =:id_gas AND chiave ='_GAS_USA_CASSA';");
           $stmt->bindValue(':id_gas', _USER_ID_GAS, PDO::PARAM_INT);
           $stmt->execute();
           $row = $stmt->fetch(PDO::FETCH_ASSOC);

           define("_USER_GAS_USA_CASSA",$row['valore_text']=='SI' ? true: false );

           //GAS CASSA VISUALIZZAZIONE SALDO
           $stmt = $db->prepare("SELECT * FROM retegas_options WHERE id_gas =:id_gas AND chiave ='_GAS_CASSA_VISUALIZZAZIONE_SALDO';");
           $stmt->bindValue(':id_gas', _USER_ID_GAS, PDO::PARAM_INT);
           $stmt->execute();
           $row = $stmt->fetch(PDO::FETCH_ASSOC);

           define("_GAS_CASSA_VISUALIZZAZIONE_SALDO",$row['valore_text']=='SI' ? true: false );


           //DES
           $stmt = $db->prepare("SELECT * FROM retegas_des WHERE id_des=:id LIMIT 1;");
           $stmt->bindValue(':id', _USER_GAS_ID_DES, PDO::PARAM_INT);
           $stmt->execute();
           $row = $stmt->fetch(PDO::FETCH_ASSOC);

           define("_USER_DES_NOME",$row["des_descrizione"]);
           define("_USER_DES_LAT",$row["des_lat"]);
           define("_USER_DES_LNG",$row["des_lng"]);
           define("_USER_DES_ZOOM",$row["des_zoom"]);


           return 1;
    }
    }



    define("_USER_LOGGED_IN",false);
    return 0;

}