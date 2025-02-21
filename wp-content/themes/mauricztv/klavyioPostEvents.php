<?php
require_once("../../../wp-load.php");
 

$payment    = new EDD_Payment( 49893 );
klavyioPostEvents($payment);

echo "OK";
exit();