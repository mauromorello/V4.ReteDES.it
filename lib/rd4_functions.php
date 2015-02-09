<?php
//-------------CASTING
function CAST_TO_INT($var, $min = FALSE, $max = FALSE){
    $var = is_int($var) ? $var : (int)(is_scalar($var) ? $var : 0);
    if ($min !== FALSE && $var < $min)
        return $min;

    elseif($max !== FALSE && $var > $max)
        return $max;

    return $var;

}
function CAST_TO_FLOAT($var, $min = FALSE, $max = FALSE){
    $var = is_float($var) ? $var : (float)(is_scalar($var) ? $var : 0);
    if ($min !== FALSE && $var < $min)
        return $min;
    elseif($max !== FALSE && $var > $max)
        return $max;
    return $var;
}
function CAST_TO_BOOL($var){
    return (bool)(is_bool($var) ? $var : is_scalar($var) ? $var : FALSE);
}
function CAST_TO_STRING($var, $length = FALSE){
    if ($length !== FALSE && is_int($length) && $length > 0)
        return substr(trim(is_string($var)
                    ? $var
                    : (is_scalar($var) ? $var : '')), 0, $length);

    return trim(
                is_string($var)
                ? $var
                : (is_scalar($var) ? $var : ''));
}
function CAST_TO_ARRAY($var){
    return is_array($var)
            ? $var
            : is_scalar($var) && $var
                ? array($var)
                : is_object($var) ? (array)$var : array();
}
function CAST_TO_OBJECT($var){
    return is_object($var)
            ? $var
            : is_scalar($var) && $var
                ? (object)$var
                : is_array($var) ? (object)$var : (object)NULL;
}
function random_string($length = 32, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890'){
    $chars_length = (strlen($chars) - 1);
    $string = $chars{rand(0, $chars_length)};
    for ($i = 1; $i < $length; $i = strlen($string))
    {
        $r = $chars{rand(0, $chars_length)};
        if ($r != $string{$i - 1}) $string .=  $r;
    }
    return $string;
}
class sec {
    var $secVersion = '1.0';
    var $to = '';
    var $Cc = array();
    var $Bcc = array();
    var $subject = '';
    var $message = '';
    var $attachment = array();
    var $embed = array();
    var $charset = 'utf-8';
    var $emailboundary = '';
    var $emailheader = '';
    var $textheader = '';
    var $errors = array();

    function sec($toname, $toemail, $fromname, $fromemail) {
        $this->emailboundary = uniqid(time());
        if($toname<>''){
            $this->to = "{$toname} <".$this->validateEmail($toemail).">";
        }else{
            $this->to = '';
        }
        $email = $this->validateEmail($fromemail);
        $this->emailheader .= "From: reteDES.it <retegas@altervista.org>\r\n";
        $this->emailheader .= "Reply-To: {$fromname} <{$email}>\r\n";
    }

    function validateEmail($email) {
       // if (!preg_match('/^[A-Z0-9._%-]+@(?:[A-Z0-9-]+\\.)+[A-Z]{2,4}$/i', $email))
       //     die('The Email '.$email.' is not Valid.');

        return $email;
    }

    function Cc($email) {
        $this->Cc[] = $this->validateEmail($email);

    }

    function Bcc($email) {
        $this->Bcc[] = $this->validateEmail($email);

    }

    function buildHead($type) {
        $count = count($this->$type);
        if($count > 0) {
            $this->emailheader .= "{$type}: ";
            $array = $this->$type;
            for($i=0; $i < $count; $i++) {
                if($i > 0) $this->emailheader .= ',';
                $this->emailheader .= $this->validateEmail($array[$i]);
            }
            $this->emailheader .= "\r\n";
        }
    }

    function buildMimeHead() {
        $this->buildHead('Cc');
        $this->buildHead('Bcc');

        $this->emailheader .= "X-Mailer: simpleEmailClass v{$this->secVersion}\r\n";
        $this->emailheader .= "MIME-Version: 1.0\r\n";
    }

    function buildMessage($subject, $message = '') {
        $textboundary = uniqid(time());
        $this->subject = strip_tags(trim($subject));

        $this->textheader = "Content-Type: multipart/alternative; boundary=\"$textboundary\"\r\n\r\n";
        $this->textheader .= "--{$textboundary}\r\n";
        $this->textheader .= "Content-Type: text/plain; charset=\"{$this->charset}\"\r\n";
        $this->textheader .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $this->textheader .= strip_tags($message)."\r\n\r\n";
        $this->textheader .= "--$textboundary\r\n";
        $this->textheader .= "Content-Type: text/html; charset=\"$this->charset\"\r\n";
        $this->textheader .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $this->textheader .= "{$message}\r\n\r\n";
        $this->textheader .= "--{$textboundary}--\r\n\r\n";

    }

    function mime_type($file) {
        return (function_exists('mime_content_type')) ? mime_content_type($file) : trim(exec('file -bi '.escapeshellarg($file)));
    }

    function attachment($file) {
        if(is_file($file)) {
            $basename = basename($file);
            $attachmentheader = "--{$this->emailboundary}\r\n";
            $attachmentheader .= "Content-Type: ".$this->mime_type($file)."; name=\"{$basename}\"\r\n";
            $attachmentheader .= "Content-Transfer-Encoding: base64\r\n";
            $attachmentheader .= "Content-Disposition: attachment; filename=\"{$basename}\"\r\n\r\n";
            $attachmentheader .= chunk_split(base64_encode(fread(fopen($file,"rb"),filesize($file))),72)."\r\n";

            $this->attachment[] = $attachmentheader;
        } else {
            die('The File '.$file.' does not exsist.');
        }
    }

    function embed($file) {
        if(is_file($file)) {
            $basename = basename($file);
            $fileinfo = pathinfo($basename);
            $contentid = md5(uniqid(time())).".".$fileinfo['extension'];
            $embedheader = "--{$this->emailboundary}\r\n";
            $embedheader .= "Content-Type: ".$this->mime_type($file)."; name=\"{$basename}\"\r\n";
            $embedheader .= "Content-Transfer-Encoding: base64\r\n";
            $embedheader .= "Content-Disposition: inline; filename=\"{$basename}\"\r\n";
            $embedheader .= "Content-ID: <{$contentid}>\r\n\r\n";
            $embedheader .= chunk_split(base64_encode(fread(fopen($file,"rb"),filesize($file))),72)."\r\n";

            $this->embed[] = $embedheader;

            return "<img src=3D\"cid:{$contentid}\">";
        } else {
            die('The File '.$file.' does not exsist.');
        }
    }

    function sendmail() {
        $this->buildMimeHead();

        $header = $this->emailheader;

        $attachcount = count($this->attachment);
        $embedcount = count($this->embed);

        if($attachcount > 0 || $embedcount > 0) {
            $header .= "Content-Type: multipart/mixed; boundary=\"{$this->emailboundary}\"\r\n\r\n";
            $header .= "--{$this->emailboundary}\r\n";
            $header .= $this->textheader;

            if($attachcount > 0) $header .= implode("",$this->attachment);
            if($embedcount > 0) $header .= implode("",$this->embed);
            $header .= "--{$this->emailboundary}--\r\n\r\n";
        } else {
            $header .= $this->textheader;
        }

        return mail($this->to, $this->subject, $this->message, $header);
    }
}

function SEmail($fullnameTO,$emailTO,$fullnameFROM,$emailFROM,$oggetto,$messaggio,$tag=null){

    $mandrill = new Mandrill(__MANDRILL_APPKEY);
    $message = array(
        'html' => $messaggio,
        'subject' => $oggetto,
        'from_email' => "info@retedes.it",
        'from_name' => $fullnameFROM,
        'to' => array(
            array(
                'email' => $emailTO,
                'name' => $fullnameTO,
                'type' => 'to'
            )
        ),
        'headers' => array('Reply-To' => $emailFROM),
        'tags' => array($tag)
    );
    $async = false;
    $ip_pool = '';
    $send_at = '';
    $result = $mandrill->messages->send($message, $async, $ip_pool, $send_at);
    //print_r($result);
    return true;

    /*
    $sec = new sec($fullnameTO, $emailTO, $fullnameFROM, $emailFROM);
    $sec->Bcc('ReteDES.it <retegas.ap@gmail.com>');
    $sec->buildMessage($oggetto, $messaggio);
    if($sec->sendmail()) {
        return true;
    } else {
        return false;
    }
    */




}
function SEmailMulti($ArrayTO,$fullnameFROM,$emailFROM,$oggetto,$messaggio,$tag=null){

    //print_r($ArrayTO);
    //die();

    //require_once('../../lib_rd4/mandrill/src/Mandrill.php');
    $mandrill = new Mandrill(__MANDRILL_APPKEY);
    //$mandrill = new Mandrill(__MANDRILL_APPKEY_TEST);
    $message = array(
        'html' => $messaggio,
        'subject' => $oggetto,
        'from_email' => "info@retedes.it",
        'from_name' => $fullnameFROM,
        'to' => $ArrayTO,
        'headers' => array('Reply-To' => $emailFROM),
        'tags' => array($tag)
    );
    $async = false;
    $ip_pool = '';
    $send_at = '';
    $result = $mandrill->messages->send($message, $async, $ip_pool, $send_at);

    return true;

    //$sec = new sec('ReteDES.it', '<retegas.ap@gmail.com>', $fullnameFROM, $emailFROM);
    //foreach($ArrayTO AS $emailTO){
    //    $sec->Bcc($emailTO);
    //}
    //$sec->buildMessage($oggetto, $messaggio);
    //if($sec->sendmail()) {
    //    return true;
    //} else {
    //    return false;
    //}




}

class Template {
        protected $file;
        protected $values = array();
        public function __construct($file) {
            $this->file = $file;
        }

        public function set($key, $value) {
            $this->values[$key] = $value;
        }
        public function output() {

            if (!file_exists($this->file)) {
                return "Error loading template file ($this->file).<br />";
            }
            $output = file_get_contents($this->file);

            foreach ($this->values as $key => $value) {
                $tagToReplace = "[@$key]";
                $output = str_replace($tagToReplace, $value, $output);
            }

            return $output;
        }

        static public function merge($templates, $separator = "\n") {

            $output = "";

            foreach ($templates as $template) {
                $content = (get_class($template) !== "Template")
                    ? "Error, incorrect type - expected Template."
                    : $template->output();
                $output .= $content . $separator;
            }

            return $output;
        }
    }

    function clean($string){
        //reject overly long 2 byte sequences, as well as characters above U+10000 and replace with ?
        $string = preg_replace('/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]'.
         '|[\x00-\x7F][\x80-\xBF]+'.
         '|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*'.
         '|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})'.
         '|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/S',
         '?', $string );

        //reject overly long 3 byte sequences and UTF-16 surrogates and replace with ?
        $string = preg_replace('/\xE0[\x80-\x9F][\x80-\xBF]'.
         '|\xED[\xA0-\xBF][\x80-\xBF]/S','?', $string );


        return htmlentities(trim(strip_tags($string)),ENT_NOQUOTES,'UTF-8');


    }



    //FUNCTION PER ORDINI
    function VA_ORDINE($id_ordine){
        global $db;
        $stmt = $db->prepare("SELECT SUM(qta_arr * prz_dett_arr) as totale FROM retegas_dettaglio_ordini WHERE id_ordine=:id_ordine ");
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        //$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //foreach ($rows as $row) {}
        return round($row["totale"],4);

    }
    function VA_ORDINE_SOLO_NETTO($id_ordine){
        global $db;
        $stmt = $db->prepare("SELECT SUM(qta_arr * prz_dett_arr) as totale FROM retegas_dettaglio_ordini WHERE id_ordine=:id_ordine AND LEFT(art_codice,2) <> '@@' AND LEFT(art_codice,2) <> '##'");
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        //$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //foreach ($rows as $row) {}
        return round($row["totale"],4);

    }
    function VA_ORDINE_ESCLUDI_RETT($id_ordine){
        global $db;
        $stmt = $db->prepare("SELECT SUM(qta_arr * prz_dett_arr) as totale FROM retegas_dettaglio_ordini WHERE id_ordine=:id_ordine AND LEFT(art_codice,2) <> '@@'");
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        //$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //foreach ($rows as $row) {}
        return round($row["totale"],4);

    }
    function VA_ORDINE_ESCLUDI_EXTRA_GAS($id_ordine){
        global $db;
        $stmt = $db->prepare("SELECT SUM(qta_arr * prz_dett_arr) as totale FROM retegas_dettaglio_ordini WHERE id_ordine=:id_ordine AND LEFT(art_codice,2) <> '##'");
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        //$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //foreach ($rows as $row) {}
        return round($row["totale"],4);

    }
    function VA_ORDINE_SOLO_RETT($id_ordine){
        global $db;
        $stmt = $db->prepare("SELECT SUM(qta_arr * prz_dett_arr) as totale FROM retegas_dettaglio_ordini WHERE id_ordine=:id_ordine AND LEFT(art_codice,2) = '@@'");
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        //$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //foreach ($rows as $row) {}
        return round($row["totale"],4);

    }
    function VA_ORDINE_SOLO_EXTRA_GAS($id_ordine){
        global $db;
        $stmt = $db->prepare("SELECT SUM(qta_arr * prz_dett_arr) as totale FROM retegas_dettaglio_ordini WHERE id_ordine=:id_ordine AND LEFT(art_codice,2) = '##'");
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        //$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //foreach ($rows as $row) {}
        return round($row["totale"],4);

    }

    function VA_ORDINE_GAS($id_ordine,$id_gas){
        global $db;
        $stmt = $db->prepare("SELECT SUM(D.qta_arr * D.prz_dett_arr) as totale FROM retegas_dettaglio_ordini D inner join maaking_users U on U.userid=D.id_utenti WHERE D.id_ordine=:id_ordine AND U.id_gas=:id_gas");
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return round($row["totale"],4);
    }
    function VA_ORDINE_GAS_SOLO_NETTO($id_ordine,$id_gas){
        global $db;
        $stmt = $db->prepare("SELECT SUM(D.qta_arr * D.prz_dett_arr) as totale FROM retegas_dettaglio_ordini D inner join maaking_users U on U.userid=D.id_utenti WHERE D.id_ordine=:id_ordine AND U.id_gas=:id_gas AND LEFT(D.art_codice,2) <> '@@'  AND LEFT(D.art_codice,2) <> '##'");
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return round($row["totale"],4);
    }
    function VA_ORDINE_GAS_ESCLUDI_RETT($id_ordine,$id_gas){
        global $db;
        $stmt = $db->prepare("SELECT SUM(D.qta_arr * D.prz_dett_arr) as totale FROM retegas_dettaglio_ordini D inner join maaking_users U on U.userid=D.id_utenti WHERE D.id_ordine=:id_ordine AND U.id_gas=:id_gas AND LEFT(D.art_codice,2) <> '@@'");
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return round($row["totale"],4);
    }
    function VA_ORDINE_GAS_ESCLUDI_EXTRA_GAS($id_ordine,$id_gas){
        global $db;
        $stmt = $db->prepare("SELECT SUM(D.qta_arr * D.prz_dett_arr) as totale FROM retegas_dettaglio_ordini D inner join maaking_users U on U.userid=D.id_utenti WHERE D.id_ordine=:id_ordine AND U.id_gas=:id_gas AND LEFT(D.art_codice,2) <> '##'");
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return round($row["totale"],4);
    }
    function VA_ORDINE_GAS_SOLO_RETT($id_ordine,$id_gas){
        global $db;
        $stmt = $db->prepare("SELECT SUM(D.qta_arr * D.prz_dett_arr) as totale FROM retegas_dettaglio_ordini D inner join maaking_users U on U.userid=D.id_utenti WHERE D.id_ordine=:id_ordine AND U.id_gas=:id_gas AND LEFT(D.art_codice,2) = '@@'");
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return round($row["totale"],4);
    }
    function VA_ORDINE_GAS_SOLO_EXTRA_GAS($id_ordine,$id_gas){
        global $db;
        $stmt = $db->prepare("SELECT SUM(D.qta_arr * D.prz_dett_arr) as totale FROM retegas_dettaglio_ordini D inner join maaking_users U on U.userid=D.id_utenti WHERE D.id_ordine=:id_ordine AND U.id_gas=:id_gas AND LEFT(D.art_codice,2) = '##'");
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return round($row["totale"],4);
    }
    function VA_ORDINE_GAS_SOLO_EXTRA_V2_V3($id_ordine,$id_gas){
        global $db;
        $stmt = $db->prepare("SELECT SUM(D.qta_arr * D.prz_dett_arr) as totale FROM retegas_dettaglio_ordini D inner join maaking_users U on U.userid=D.id_utenti WHERE D.id_ordine=:id_ordine AND U.id_gas=:id_gas ");
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $netto_gas = round($row["totale"],4);

        $stmt = $db->prepare("SELECT SUM(D.qta_arr * D.prz_dett_arr) as totale FROM retegas_dettaglio_ordini D inner join maaking_users U on U.userid=D.id_utenti WHERE D.id_ordine=:id_ordine");
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $netto_totale = round($row["totale"],4);

        $percentuale_gas = $netto_gas / $netto_totale;

        $stmt = $db->prepare("SELECT costo_trasporto, costo_gestione FROM retegas_ordini  WHERE id_ordini=:id_ordine");
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $trasporto_totale = round($row["costo_trasporto"],4);
        $gestione_totale = round($row["costo_gestione"],4);

        $trasporto_gas = $trasporto_totale * $percentuale_gas;
        $gestione_gas = $gestione_totale * $percentuale_gas;

        $stmt = $db->prepare("SELECT maggiorazione_referenza, maggiorazione_percentuale_referenza FROM retegas_referenze  WHERE id_ordine_referenze=:id_ordine AND id_gas_referenze=:id_gas;");
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->bindParam(':id_gas', $id_gas, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $maggiorazione_referenza = round($row["maggiorazione_referenza"],4);
        $maggiorazione_percentuale_referenza = round($row["maggiorazione_percentuale_referenza"],4);

        $maggiorazione_gas = $maggiorazione_referenza;
        $percentuale_gas = ($netto_gas/100) * $maggiorazione_percentuale_referenza;

        //return $id_gas;

        return round(   $trasporto_gas +
                        $gestione_gas +
                        $maggiorazione_gas +
                        $percentuale_gas
               ,4);



    }


    function VA_ORDINE_USER($id_ordine,$id_user){
        global $db;
        $stmt = $db->prepare("SELECT SUM(qta_arr * prz_dett_arr) as totale FROM retegas_dettaglio_ordini WHERE id_ordine=:id_ordine AND id_utenti=:id_user ");
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        //$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //foreach ($rows as $row) {}
        return round($row[totale],4);

    }
    function VA_ORDINE_USER_SOLO_NETTO($id_ordine,$id_user){
        global $db;
        $stmt = $db->prepare("SELECT SUM(qta_arr * prz_dett_arr) as totale FROM retegas_dettaglio_ordini WHERE id_ordine=:id_ordine AND id_utenti=:id_user AND LEFT(art_codice,2) <> '@@' AND LEFT(art_codice,2) <> '##'");
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        //$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //foreach ($rows as $row) {}
        return round($row[totale],4);

    }
    function VA_ORDINE_USER_ESCLUDI_RETT($id_ordine,$id_user){
        global $db;
        $stmt = $db->prepare("SELECT SUM(qta_arr * prz_dett_arr) as totale FROM retegas_dettaglio_ordini WHERE id_ordine=:id_ordine AND id_utenti=:id_user AND LEFT(art_codice,2) <> '@@'");
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        //$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //foreach ($rows as $row) {}
        return round($row[totale],4);

    }
    function VA_ORDINE_USER_ESCLUDI_EXTRA_GAS($id_ordine,$id_user){
        global $db;
        $stmt = $db->prepare("SELECT SUM(qta_arr * prz_dett_arr) as totale FROM retegas_dettaglio_ordini WHERE id_ordine=:id_ordine AND id_utenti=:id_user AND LEFT(art_codice,2) <> '##' ");
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        //$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //foreach ($rows as $row) {}
        return round($row[totale],4);

    }
    function VA_ORDINE_USER_SOLO_RETTIFICHE($id_ordine,$id_user){
        global $db;
        $stmt = $db->prepare("SELECT SUM(qta_arr * prz_dett_arr) as totale FROM retegas_dettaglio_ordini WHERE id_ordine=:id_ordine AND id_utenti=:id_user AND LEFT(art_codice,2) = '@@' ");
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        //$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //foreach ($rows as $row) {}
        return round($row[totale],4);

    }
    function VA_ORDINE_USER_SOLO_EXTRA_GAS($id_ordine,$id_user){
        global $db;
        $stmt = $db->prepare("SELECT SUM(qta_arr * prz_dett_arr) as totale FROM retegas_dettaglio_ordini WHERE id_ordine=:id_ordine AND id_utenti=:id_user AND LEFT(art_codice,2) = '##' ");
        $stmt->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
        $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        //$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //foreach ($rows as $row) {}
        return round($row[totale],4);

    }
