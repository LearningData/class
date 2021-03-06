<?php
/**						httpscripts/contact_labels_print.php
 *
 * Allows for a list of sids to select the recipients (coming from the
 * Admin book probably). Or an already selected list of recipients set as a
 * session var within the InfoBook.
 *
 */

require_once('../../scripts/http_head_options.php');
$book='infobook';

if(isset($_POST['sids'])){$sids=(array)$_POST['sids'];unset($_SESSION[$book.'recipients']);}
elseif(isset($_GET['sids'])){$sids=(array)$_GET['sids'];unset($_SESSION[$book.'recipients']);}
elseif(isset($_SESSION[$book.'recipients'])){$recipients=$_SESSION[$book.'recipients'];}
//elseif(isset($_POST['recipients'])){$recipients=(array)$_POST['recipients'];}else{$recipients=array();}
if(isset($_POST['template'])){$template=$_POST['template'];}
elseif(isset($_GET['template'])){$template=$_GET['template'];}
else{$template='address_labels2x7';}
if(isset($_POST['explanation'])){$explanation=$_POST['explanation'];}
elseif(isset($_GET['explanation'])){$explanation=$_GET['explanation'];}
else{$explanation='blank';}
if(isset($_POST['messageto'])){$messageto=$_POST['messageto'];}
elseif(isset($_GET['messageto'])){$messageto=$_GET['messageto'];}
else{$messageto='blank';}
if(isset($_POST['text'])){$text=$_POST['text'];}
elseif(isset($_GET['text'])){$text=$_GET['text'];}
else{$text='';}
if(isset($_POST['orderby'])){$orderby=$_POST['orderby'];}
elseif(isset($_GET['orderby'])){$orderby=$_GET['orderby'];}
else{$orderby='sortstudent';}

$Students['Transform']=$template;
$Students['Paper']='portrait';
$Students['homecountry']=strtoupper($CFG->sitecountry);
$Students['explanation']=$explanation;
$Students['content']=$text;
if($messageto=='studentbadge'){$Students['type']='badge';}

if(isset($recipients) and sizeof($recipients)>0){

	if($orderby=='sortcontact'){
		$sort_titles=array();
		foreach($recipients['Recipient'] as $Recipient){
			$sort_titles[]=$Recipient['DisplayFullName']['value'];
			}
		array_multisort($sort_titles,SORT_ASC,SORT_STRING,$recipients['Recipient']);
		}

	$Students['recipients']=$recipients;
	$returnXML=$Students;
	$rootName='Students';
	}
else{
	$result[]=get_string('youneedtoselectstudents');
	$returnXML=$result;
	$rootName='Error';
	}

require_once('../../scripts/http_end_options.php');
exit;
?>
