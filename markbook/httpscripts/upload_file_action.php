<?php
/**                    httpscripts/upload_file_action.php
 *
 */

require_once('../../scripts/http_head_options.php');
require_once('../../lib/eportfolio_functions.php');

$sub=$_POST['sub'];
$sid=$_POST['sid'];
$tid=$_SESSION['username'];

if($sub=='Cancel'){
	$openerId='-100';
	$incom='';
	}
elseif($sub=='Submit' and isset($_FILES['importfile']) and $_FILES['importfile']['tmp_name']!=''){
	$publishdata=array();
	$entryn='';
	$openerId=$_POST['openid'];
	$Student=fetchStudent_short($sid);
	$filename=$_FILES['importfile']['name'];
	$tmpname=$_FILES['importfile']['tmp_name'];
	if(isset($_POST['inmust'])){$inmust=$_POST['inmust'];}
	if(isset($_POST['mid'])){$bid=$_POST['mid'];}
	if(isset($_POST['cid'])){$cid=$_POST['cid'];}
	if(isset($_POST['pid'])){$pid=$_POST['pid'];}
	if(isset($_POST['title'])){$publishdata['title']=clean_text($_POST['title']);}else{$publishdata['title']='';}
	if(isset($_POST['comment'])){$publishdata['description']=clean_text($_POST['comment']);}else{$publishdata['description']='';}
	if(isset($_POST['news'])){$news=$_POST['news'];}else{$news='no';}

	$EPFUsername=fetchStudent_singlefield($sid,'EPFUsername');

	$publishdata['foldertype']='work';
	$publishdata['batchfiles'][]=array('epfusername'=>$EPFUsername['value'],
									   'filename'=>$filename,
									   'tmpname'=>$tmpname);

	if($inmust=='yes'){
		/*Create a new entry*/
		trigger_error($sid.' : '.$epfusername. ' : '.$filename,E_USER_WARNING);

		elgg_upload_files($publishdata);

		}
	elseif($inmust!='yes'){
		/* TODO: Update an existing file*/
		$entryn=$inmust;


		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>ClaSS File Uploader</title>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
<meta http-equiv="Content-Script-Type" content="text/JavaScript" />
<meta name="copyright" content="Copyright 2002-2012 S T Johnson.  All trademarks acknowledged. All rights reserved" />
<meta name="version" content='<?php print "$CFG->version"; ?>' />
<meta name="licence" content="GNU Affero General Public License version 3" />
<link id="viewstyle" rel="stylesheet" type="text/css" href="../../css/bookstyle.css" />
<script language="JavaScript" type="text/javascript" src="../../js/book.js?version=1013"></script>
</head>
<body onload="closeHelperWindow(<?php print '\''.$openerId.'\',\''.$entryn.'\',\'\'';?>);">
	<div id="bookbox">
	  <div id="heading">
	  </div>
	  <div id="viewcontent" class="content">
	  </div>
	</div>
</body>
</html>
