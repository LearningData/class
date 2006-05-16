<?php
/**									report_comments_list.php
 *
 *	Finds and lists students identified as having concerns.
 */

$action='report_comments_print.php';

$date0=$_POST['date0'];
if(isset($_POST{'date1'})){$date1=$_POST['date1'];}else{$date1=date("Y-m-d");}
if(isset($_POST{'bid'})){$bid=$_POST{'bid'};}else{$bid='';}
if(isset($_POST{'newyid'})){$yid=$_POST{'newyid'};}else{$yid='';}
if(isset($_POST{'fid'})){$fid=$_POST{'fid'};}else{$fid='';}

include('scripts/sub_action.php');

if($yid!=''){
		if($d_comments=mysql_query("SELECT * FROM comments JOIN
		student ON student.id=comments.student_id WHERE
		comments.entrydate > '$date0' AND student.yeargroup_id LIKE
		'$yid' ORDER BY student.surname")){}
		else{print mysql_error();}
		}
elseif($bid!=''){
		if($d_comments=mysql_query("SELECT * FROM comments WHERE entrydate
				> '$date0' AND subject_id LIKE '$bid'")){}
		else{print mysql_error();}
		}
else {
		$error[]=get_string('needselectstudents');
	    $current=$choice;
    	include('scripts/results.php');
	    include('scripts/redirect.php');
		exit;
		}

	$summary=array();
	$sids=array();
	while($comment=mysql_fetch_array($d_comments,MYSQL_ASSOC)){
		$sid=$comment['student_id'];
		$bids=array();
		if(in_array($sid,$sids)){
			$bids=$summary[$sid];
			$bids[]=$comment['subject_id'];
			}
		else{
			$sids[]=$sid;
			$bids[]=$comment['subject_id'];
			}
		$summary[$sid]=$bids;
		}

twoplusprint_buttonmenu();
?>
<div id="viewcontent" class="content">
<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>"> 
	  <table class="listmenu">
		<tr>
		  <th>
			<label id="checkall"><?php print_string('checkall');?>
			  <input type="checkbox" name="checkall" value="yes" onChange="checkAll	(this);" />
			</label>
		  </th>
		  <th colspan="2"><?php print_string('student');?></th>
		  <th><?php print_string('formgroup');?></th>
		  <th><?php print_string('areasforcomments',$book);?></th>
		</tr>
<?php	
	while(list($index,$sid)=each($sids)){
		$d_student=mysql_query("SELECT * FROM student WHERE id='$sid'");
		$student=mysql_fetch_array($d_student,MYSQL_ASSOC);
?>
		<tr>
		  <td>
			<input type='checkbox' name='sids[]' value='<?php print $sid; ?>' />
		  </td>
		  <td>
			<a href="infobook.php?current=comments_list.php&sid=<?php print $sid;?>"
			  target="viewinfobook" onclick="parent.viewBook('infobook');">C</a>
		  </td>
		  <td>
		<?php print $student['surname']; ?>, <?php print $student['forename']; ?>			
		  </td>
		  <td>
			<?php print $student['form_id']; ?>
		  </td>
		  <td>
<?php
		for($c=0;$c<sizeof($summary[$sid]);$c++){
				print $summary[$sid][$c].'&nbsp;';
				}
?>
		  </td>
		</tr>
<?php	
		}
	reset($sids);
?>
	  </table>

	</fieldset>

	<input type="hidden" name="bid" value="<?php print $bid;?>" />
	<input type="hidden" name="fid" value="<?php print $fid;?>" />
	<input type="hidden" name="newyid" value="<?php print $yid;?>" />
	<input type="hidden" name="date0" value="<?php print $date0;?>" />
	<input type="hidden" name="date1" value="<?php print $date1;?>" />
 	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
 	<input type="hidden" name="choice" value="<?php print $choice;?>" />
 	<input type="hidden" name="current" value="<?php print $action;?>" />
	</form>
  </div>

