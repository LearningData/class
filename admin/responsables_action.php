<?php 
/**									responsables_action.php
 *
 */

$action='responsables.php';
include('scripts/sub_action.php');

$crid=$_POST['crid'];
$bid=$_POST['bid'];
$yid=$_POST['newyid'];
$newuid=$_POST['user'];
$perm=$_POST['privilege'];
if(isset($_POST['email'])){$email=$_POST['email'];}else{$email='no';}


/* the permissions allowed by change, edit, or view*/
if($perm=='x'){$newperms=array('r'=>1,'w'=>1,'x'=>1);}
if($perm=='w'){$newperms=array('r'=>1,'w'=>1,'x'=>0);}
if($perm=='r'){$newperms=array('r'=>1,'w'=>0,'x'=>0);}
if($email=='yes'){$newperms['e']=1;}else{$newperms['e']=0;}

if($bid!='' or $crid!=''){

	if($bid==''){$bid='%';}
	if($crid==''){$crid='%';}

	$permc=getCoursePerm($crid,$respons);
	$permb=getSubjectPerm($bid,$respons);

	if(($permc['x']==1 and $crid!='%') or ($permb['x']==1 and $bid!='%' and $crid=='%')){
		$d_group=mysql_query("SELECT gid FROM groups WHERE
				subject_id='$bid' AND course_id='$crid' AND yeargroup_id IS NULL AND type='a';");
		if(mysql_num_rows($d_group)==0){
			/*if no group exists create one for this combination*/
			if ($crid!='%' and $bid!='%'){$name=$crid.'/'.$bid;}
			else if ($crid!='%'){$name=$crid;}
			else {$name=$bid;}
			mysql_query("INSERT groups (course_id, subject_id, type) VALUES ('$crid', '$bid', 'a');");
			$gid=mysql_insert_id();
			}
		else{$gid=mysql_result($d_group,0);}

		if($gid==0){print 'Failed on group!'; exit;}

		update_staff_perms($newuid,$gid,$newperms);

		}
	elseif($permc['x']!=1 and $crid!='%'){
		$error[]=get_string('nopermissions');
		}
	elseif($permb['x']!=1 and $bid!='%' and $crid=='%'){
		$error[]=get_string('nosubjectpermissions');
		}
	}
else{
	$error[]='You need to select a Course or a Subject to assign academic priviliges.';
	}
include('scripts/results.php');
include('scripts/redirect.php');
?>
