<?php
	require_once('../../school.php');
	require_once('../classdata.php');
	require_once('session.php');
	start_class_phpsession();
	kill_class_phpsession();
	global $CFG;
	if(isset($_SERVER['HTTPS'])){
		$http='https';
		}
	else{
		$http='http';
		}
	header("Location: ".$http."://".$CFG->siteaddress.$CFG->sitepath."/index.php");
	exit;
?>
