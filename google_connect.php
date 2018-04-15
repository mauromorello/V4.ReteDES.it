<?php 

require_once("inc/init.php");


$page_css[] = "your_style.css";
$no_main_header = true;
$page_body_prop = array("id"=>"extr-page", "class"=>"animated fadeInDown container");
include("inc/header.php");

?>
<style>
            .centered {
              position: fixed;
              top: 50%;
              left: 50%;
              /* bring your own prefixes */
              transform: translate(-50%, -50%);
            }
            .centered2 {
              position: fixed;
              top: 10%;
              left: 50%;
              /* bring your own prefixes */
              transform: translate(-50%, -50%);
            }
            .centered3 {
              position: fixed;
              top: 90%;
              left: 50%;
              /* bring your own prefixes */
              transform: translate(-50%, -50%);
            }
        </style>
<script src="https://apis.google.com/js/platform.js?onload=renderButton" async defer></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
        <script>
            if (!window.jQuery) {
                document.write('<script src="<?php echo ASSETS_URL; ?>/js/libs/jquery-2.0.2.min.js"><\/script>');
            }
        </script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
        <script>
            if (!window.jQuery.ui) {
                document.write('<script src="<?php echo ASSETS_URL; ?>/js/libs/jquery-ui-1.10.3.min.js"><\/script>');
            }
        </script>
        <script src="<?php echo ASSETS_URL; ?>/js/notification/SmartNotification.min.js"></script>
        
        
<div class="centered2">
    <img SRC="https://retegas.altervista.org/gas4/img/logo_rd4.png">
</div>
<div class="centered">
    <div class="clearfix"></div>
    <div id="my-signin2" class="centered"></div>
</div>
<div class="centered3">
    <a class="btn btn-success btn-lg" href="https://retegas.altervista.org/gas4/#ajax_rd4/user/impostazioni.php">TORNA A RETEDES</a>
</div>
<script>
  function signOut() {
    var auth2 = gapi.auth2.getAuthInstance();
    auth2.signOut().then(function () {
      console.log('User signed out.');
      //DELETE GID sul server
    });
  }
</script>
</div>

                    <script>
                    function ok(msg){
        $.smallBox({
                            title : "ReteDES.it",
                            content : "<i class='fa fa-check'></i> <i>" + msg + "</i>",
                            color : "#0074A7",
                            iconSmall : "fa fa-thumbs-up bounce animated",
                            timeout : 4000
        });
    }
                    
                    
                        function onSuccess(googleUser) {
                          console.log('Logged in as: ' + googleUser.getBasicProfile().getName());
                          //MANDARE SUL SERVER ID
                          var id_token = googleUser.getAuthResponse().id_token;
                          var xhr = new XMLHttpRequest();
                            xhr.open('POST', 'https://retegas.altervista.org/gas4/oAuth/index.php');
                            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                            
                            xhr.onload = function() {
                              var data= eval("(" + xhr.responseText + ")");
                              if(data.result=="OK"){
                                  ok(data.msg);
                                  
                              }else{
                                  ko(data.msg);
                              }

                            };
                            xhr.send('idtoken=' + id_token);
                         
                        }
                        function onFailure(error) {
                          console.log(error);
                        }
                        function renderButton() {
                          gapi.signin2.render('my-signin2', {
                            'scope': 'profile email openid',
                            'width': 240,
                            'height': 50,
                            'longtitle': true,
                            'theme': 'dark',
                            'onsuccess': onSuccess,
                            'onfailure': onFailure
                          });
                        }
                      </script>
