<?php
require_once('../lib_rd4/mandrill/src/Mandrill.php');

try {
    $mandrill = new Mandrill(__MANDRILL_APPKEY);
    $message = array(
        'html' => '<p>Example HTML content 4</p>',
        'subject' => 'example subject 4',
        'from_email' => 'retegas.ap@gmail.com',
        'from_name' => 'ReteDES.it',
        'to' => array(
            array(
                'email' => 'famiglia.morello@gmail.com',
                'name' => 'Mauro Morello',
                'type' => 'bcc'
            ),
            array(
                'email' => 'mauro.morello.0@gmail.com',
                'name' => 'Mauro Morello MAURO',
                'type' => 'bcc'
            ),
            array(
                'email' => 'ma.morez@tiscali.it',
                'name' => 'Mauro Morello TISCALI',
                'type' => 'bcc'
            ),
            array(
                'email' => 'cicciopasticci@gmail.it',
                'name' => 'Mauro Morello TISCALI',
                'type' => 'bcc'
            )
        ),
        'headers' => array('Reply-To' => 'elisa.boldi@gmail.com')

    );
    $async = false;
    $ip_pool = 'Main Pool';
    $send_at = '';
    $result = $mandrill->messages->send($message, $async, $ip_pool, $send_at);
    print_r($result);
    /*
    Array
    (
        [0] => Array
            (
                [email] => recipient.email@example.com
                [status] => sent
                [reject_reason] => hard-bounce
                [_id] => abc123abc123abc123abc123abc123
            )

    )
    */
} catch(Mandrill_Error $e) {
    // Mandrill errors are thrown as exceptions
    echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
    // A mandrill error occurred: Mandrill_Unknown_Subaccount - No subaccount exists with the id 'customer-123'
    throw $e;
}



/*
define("__SENDGRID_USER","mimmoz01");
define("__SENDGRID_PASSWORD","zxcasdqwe123");
echo "Hi!<br>";//die();
require_once("../lib_rd4/sendgrid-php/sendgrid-php.php");
require_once('../lib_rd4/swiftmailer-master/lib/swift_required.php');

$sendgrid = new SendGrid(__SENDGRID_USER, __SENDGRID_PASSWORD);
$email = new SendGrid\Email();
$email->setFrom('retegas.ap@gmail.com');
$email->addTo('retegas.ap@gmail.com');
$email->addBcc('ma.morez@tiscali.it');
$email->addBcc('mauro.morello@loropiana.com');
$email->addBcc('famiglia.morello@gmail.com');
$email->addBcc('trash.coseinutili@gmail.com');
$email->setSubject('Soggetto mattina');
$email->setText('Hello World! mattina');
$email->setHtml('<strong>TEST 6!</strong> Messaggio mattina BCC');
$email->setReplyTo('ma.morez@tiscali.it');
$email->addCategory("TEST");
print_r($sendgrid->send($email));
*/


/*
//SMTP

$transport  = Swift_SmtpTransport::newInstance('smtp.sendgrid.net', 587, array("turn_off_ssl_verification" => true));
$transport->setUsername(__SENDGRID_USER);
$transport->setPassword(__SENDGRID_PASSWORD);

$mailer     = Swift_Mailer::newInstance($transport);

$message    = new Swift_Message();
$message->setTo("famiglia.morello@gmail.com");
//$message->setCc("sendgrid1@mailinator.com");
$message->addBcc("ma.morez@tiscali.it");
$message->addBcc("mauro.morello@loropiana.com");
$message->setFrom("famiglia.morello@gmail.com");
$message->setSubject("[smtp-php-example] Owl named %yourname%");
$message->setBody("%how% are you doing?");

$header           = new Smtpapi\Header();
$header->addSubstitution("%yourname%", array("Mr. Owl"));
$header->addSubstitution("%how%", array("Owl"));

$message_headers  = $message->getHeaders();
$message_headers->addTextHeader("x-smtpapi", $header->jsonString());

try {
  $response = $mailer->send($message);
  print_r($response);
} catch(\Swift_TransportException $e) {
  print_r($e);
  print_r('Bad username / password');
}
*/
/*

$url = 'https://api.sendgrid.com/';
$user = __SENDGRID_USER;
$pass = __SENDGRID_PASSWORD;

$json_string = array(

  'to' => array(
    'famiglia.morello@gmail.com', 'retegas.ap@gmail.com','mauro.morello@loropiana.com'
  ),
  'category' => 'test_category'
);


$params = array(
    'api_user'  => $user,
    'api_key'   => $pass,
    'x-smtpapi' => json_encode($json_string),
    'to'        => 'retegas.ap@gmail.com',
    'subject'   => 'testing from curl 4',
    'html'      => 'testing body',
    'text'      => 'testing body',
    'from'      => 'retegas.ap@gmail.com',
    'reply-to'  => 'mauro.morello@loropiana.com',
  );


$request =  $url.'api/mail.send.json';

// Generate curl request
$session = curl_init($request);
// Tell curl to use HTTP POST
curl_setopt ($session, CURLOPT_POST, true);
// Tell curl that this is the body of the POST
curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
// Tell curl not to return headers, but do return the response
curl_setopt($session, CURLOPT_HEADER, false);
// Tell PHP not to use SSLv3 (instead opting for TLS)
curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

// obtain response
$response = curl_exec($session);
curl_close($session);

// print everything out
print_r($response);
*/
?>
