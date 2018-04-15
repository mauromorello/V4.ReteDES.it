<?php
require_once("inc/init.php");
if(file_exists("../../lib_rd4/class.rd4.ordine.php")){require_once("../../lib_rd4/class.rd4.ordine.php");}
if(file_exists("../lib_rd4/class.rd4.ordine.php")){require_once("../lib_rd4/class.rd4.ordine.php");}

if(!(_USER_PERMISSIONS & perm::puo_gestire_retegas)){die("KO");}

$page_title = "PHP INFO";
$page_id = "php_info";






?>

<section><?php
                       echo phpinfo();
                   ?></section>



<script type="text/javascript">

    pageSetUp();

    var pagefunction = function() {

    };

    // end pagefunction

    // run pagefunction on load
    pagefunction();

</script>