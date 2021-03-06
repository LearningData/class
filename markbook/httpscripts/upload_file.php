<?php
/**		   					httpscripts/upload_file.php
 */

$book='markbook';
$tab=1;
/* $inmust forces an insert of the submissions as a new entry
 * TODO: would be to allow updates for existing files.
 */
$inmust='yes';

require_once('../../scripts/http_head_options.php');

if(isset($_GET['sid'])){$sid=$_GET['sid'];}else{$sid='';}
if(isset($_POST['sid'])){$sid=$_POST['sid'];}
if(isset($_GET['eid'])){$eid=$_GET['eid'];}else{$eid='';}
if(isset($_POST['eid'])){$eid=$_POST['eid'];}
if(isset($_GET['bid'])){$bid=$_GET['bid'];}else{$bid='';}
if(isset($_POST['bid'])){$bid=$_POST['bid'];}
if(isset($_GET['pid'])){$pid=$_GET['pid'];}else{$pid='';}
if(isset($_POST['pid'])){$pid=$_POST['pid'];}
if(isset($_GET['openid'])){$openid=$_GET['openid'];}
if(isset($_GET['foldertype'])){$foldertype=$_GET['foldertype'];}
if(isset($_POST['foldertype'])){$foldertype=$_POST['foldertype'];}

if($sid==''){
	$result[]=get_string('youneedtoselectstudents');
	$returnXML=$result;
	$rootName='Error';
	}
else{
	$Student=fetchStudent($sid);
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Upload File Helper</title>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
<meta http-equiv="Content-Script-Type" content="text/JavaScript" />
<meta name="copyright" content="Copyright 2002-2016 S T Johnson.  All trademarks acknowledged. All rights reserved" />
<meta name="version" content="<?php print $CFG -> version; ?>" />
<meta name="licence" content="GNU Affero General Public License version 3" />
<link rel="stylesheet" type="text/css" href="../../css/bookstyle.css" />
<link rel="stylesheet" type="text/css" href="../../css/markbook.css" />
<link rel="stylesheet" href="../../css/uniform.edit.css" media="screen" />
<link href='//fonts.googleapis.com/css?family=Lato:100,300,400,700,900' rel='stylesheet' type='text/css'>
<link href="../../css/font-awesome.min.css" rel="stylesheet">
<script src="../../js/editor.js" type="text/javascript"></script>
<script src="../../js/jcrop/jquery.min.js" type="text/javascript"></script>
<script src="../../js/book.js" type="text/javascript"></script>
<script src="../../js/documentdrop.js" type="text/javascript"></script>
<script src="../../js/qtip.js" type="text/javascript"></script>
</head>
<body onload="loadRequired('<?php print $book; ?>');documentdropInit();">
	<div class="markcolor" id="bookbox">
		<?php two_buttonmenu_submit(); ?>
		<div id="heading">
		<h4><label><?php print_string('student'); ?></label> <?php print $Student['DisplayFullName']['value']; ?></h4>
	</div>

	<div class="content">
		<div class="listmenu fileupload">
<?php
		require_once('../../lib/eportfolio_functions.php');
		if($openid=="sharedcomment" or $openid=='comment'){
			html_document_drop($Student['EPFUsername']['value'],'comment',$eid,'',$openid);
			}
		elseif($openid=='' and $foldertype!=''){
			html_document_drop($Student['EPFUsername']['value'],$foldertype,$eid);
			}
		else{
?>
		<form id="formtoprocess2" name="formtoprocess2" method="post" action="upload_file_action.php">
				<fieldset class="center">
					<legend><?php print_string('documents',$book);?></legend>
					<div style="width:90%;float:left;">
<?php
				$oid=$eid;
				$d_s=mysql_query("SELECT id FROM report_skill WHERE id='$bid' AND subject_id='$pid';");
				if(mysql_num_rows($d_s)>0){$oid=$bid;}
				html_files_preview($Student['EPFUsername']['value'],$oid,true,'',$foldertype);
				$d_f=mysql_query("SELECT id FROM file WHERE other_id='$oid';");
?>
					</div>
<?php
				if(mysql_num_rows($d_s)>0 or mysql_num_rows($d_f)>0){
?>
					<div style="width:60px;float:left;">
						<button name="action"
							  value="Remove"><?php print_string('remove');?></button>
					</div>
<?php
					}
?>
				</fieldset>
			</div>
			<input type="hidden" name="sid" value="<?php print $sid; ?>"/>
			<input type="hidden" name="eid" value="<?php print $eid; ?>"/>
			<input type="hidden" name="bid" value="<?php print $bid; ?>"/>
			<input type="hidden" name="pid" value="<?php print $pid; ?>"/>
			<input type="hidden" name="openid" value="<?php print $openid; ?>"/>
			<input type="hidden" name="foldertype" value="<?php print $foldertype; ?>"/>
		</form>
		<fieldset class="center">
			<legend><?php print_string('upload',$book);?></legend>
<?php
			$otherid=$eid;
			if(is_numeric($bid) and $bid>0){$otherid=$bid;}
			html_document_drop($Student['EPFUsername']['value'],$foldertype,$otherid,'-1','',false);
?>
		</fieldset>
<?php
			}
?>

	<form id="formtoprocess" name="formtoprocess" method="post" action="upload_file_action.php">

<?php
	if($openid!="sharedcomment" and $openid!="comment" and $openid!=""){
?>
		<fieldset class="center">
			<legend><?php print_string('copy',$book);?></legend>
			<div style="width:90%;float:left;">
<?php
			//html_files_preview($Student['EPFUsername']['value'],$otherid);
			html_files_preview($Student['EPFUsername']['value'],$otherid,false,$pid,'comment');
			$d_o=mysql_query("SELECT DISTINCT id FROM report_skill WHERE profile_id='$eid' AND id!='$bid';");
			while($other=mysql_fetch_array($d_o,MYSQL_ASSOC)){
				html_files_preview($Student['EPFUsername']['value'],$other['id']);
				}
?>
			</div>
<?php
			//if(mysql_num_rows($d_o)>0){
?>
			<div style="width:40px;float:left;">
				<button  name="action"
				  value="Copy"><?php print_string('copy');?></button>
			</div>
<?php
			//	}
?>
		</fieldset>
<?php
		}
?>
			<input type="hidden" name="inmust" value="<?php print $inmust; ?>"/>
			<input type="hidden" name="sid" value="<?php print $sid; ?>"/>
			<input type="hidden" name="bid" value="<?php print $bid; ?>"/>
			<input type="hidden" name="pid" value="<?php print $pid; ?>"/>
			<input type="hidden" name="eid" value="<?php print $eid; ?>"/>
			<input type="hidden" name="openid" value="<?php print $openid; ?>"/>
			<input type="hidden" name="foldertype" value="<?php print $foldertype; ?>"/>
		</form>
	</div>


</body>
</html>
