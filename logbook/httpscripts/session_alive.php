<?php
/**                    httpscripts/session_alive.php
 */

require_once('../../scripts/http_head_options.php');

if(!isset($xmlid)){print 'Failed'; exit;}

//trigger_error('ALIVE!!!!',E_USER_WARNING);

$returnXML=array('status'=>1);
$rootName='session';
require_once('../../scripts/http_end_options.php');
exit;
?>

















