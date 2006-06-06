<?php
/**                    httpscripts/comment_writer_action.php
 */

require_once('common.php');
$sub=$_POST{'sub'};
$sid=$_POST{'sid'};
$rid=$_POST{'rid'};
$tid=$_SESSION['username'];

/*categories are not handled by the commentwriter*/
/*and are not yet used for summary comments anyway*/
$incategory='';

if($sub=='Cancel'){
	$openerId='-100';
	$incom='';
	}
elseif($sub=='Submit'){
	$openerId=$_POST['openid'];
	$Student=fetchshortStudent($sid);
	if(isset($_POST{'bid'})){$bid=$_POST{'bid'};}
	if(isset($_POST{'pid'})){$pid=$_POST{'pid'};}
	if(isset($_POST{'incom'})){$incom=clean_text($_POST{'incom'});}
	if(isset($_POST{'inmust'})){$inmust=$_POST{'inmust'};}


	if($inmust=='yes' and ($incom!='' or $incategory!='')){
		if(mysql_query("INSERT INTO reportentry (comment,
						category, teacher_id, report_id, student_id, 
						subject_id, component_id) VALUES
						('$incom', '$incategory', '$tid', '$rid', '$sid',
						'$bid', '$pid')")){
			$entryn=mysql_insert_id();
			}
		else {$error[]=mysql_error();}
		}
	elseif($inmust!='yes' and ($incom!='' or $incategory!='')){
		$entryn=$inmust;
		if(mysql_query("UPDATE reportentry SET
						comment='$incom', category='$incategory' WHERE report_id='$rid' AND
						student_id='$sid' AND subject_id='$bid' AND
						component_id='$pid' AND entryn='$entryn'")){}
		else {$error[]=mysql_error();}
		}
	elseif($inmust!='yes' and $incom=='' and $incategory==''){	   
		$entryn=$inmust;
		if(mysql_query("DELETE FROM reportentry WHERE
						 report_id='$rid' AND
						student_id='$sid' AND subject_id='$bid' AND
						component_id='$pid' AND entryn='$entryn' LIMIT 1")){}
		else {$error[]=mysql_error();}
		}
	}
$comment=js_addslashes($incom);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>ClaSS Comment Writer</title>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
<meta http-equiv="Content-Script-Type" content="text/JavaScript" />
<meta name="copyright" content="Copyright 2002-2006 S T Johnson.  All trademarks acknowledged. All rights reserved" />
<meta name="version" content='<?php print "$CFG->version"; ?>' />
<meta name="licence" content="GNU General Public License version 2" />
<link id="viewstyle" rel="stylesheet" type="text/css" href="../../css/viewstyle.css" />
<link id="viewstyle" rel="stylesheet" type="text/css" href="../../css/commentwriter.css" />
<script language="JavaScript" type="text/javascript" src="../../js/formfunctions.js"></script>
</head>
<body onload="closeCommentWriter(<?php print '\''.$openerId.'\',\''.$entryn.'\',\''.$comment.'\'';?>);">
	<div id="bookbox">

	  <div id="heading">
			  <label><?php print_string('student'); ?></label>
			<?php print $Student['DisplayFullName']['value'];?>
	  </div>

	  <div id="viewcontent" class="content">
<?php
	  include('../../scripts/results.php');
?>
	  </div>

	</div>
</body>
</html>
