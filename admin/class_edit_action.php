<?php 
/**										   		class_edit_action.php
 */

$action='class_edit.php';
$action_post_vars=array('newtid','newcid','orderbyname');

if(isset($_POST['newcid'])){$newcid=$_POST['newcid'];}
if(isset($_POST['detail'])){$detail=$_POST['detail'];}
if(isset($_POST['orderbyname'])){$orderbyname=$_POST['orderbyname'];}
if(isset($_POST['description'])){$description=$_POST['description'];}
if(isset($_POST['newtid'])){$newtid=$_POST['newtid'];}else{$newtid='';}
if(isset($_POST['newsid'])){$newsid=(array)$_POST['newsid'];}else{$newsid=array();}

include('scripts/sub_action.php');

if($sub=='Unassign' and $newtid!=''){
   	mysql_query("DELETE FROM tidcid WHERE teacher_id='$newtid' AND
						class_id='$newcid' LIMIT 1;");
	$action=$cancel;
	}

elseif($sub=='Submit'){
   	$d_student=mysql_query("SELECT a.student_id, b.surname,
		b.forename FROM cidsid a, student b WHERE a.class_id='$newcid' 
		AND b.id=a.student_id ORDER BY b.surname");
   	while($student=mysql_fetch_array($d_student, MYSQL_ASSOC)){
			$sid=$student['student_id'];
			if(isset($_POST["$sid"])){
				mysql_query("DELETE FROM cidsid WHERE
					student_id='$sid' AND class_id='$newcid' LIMIT 1;");
				}
			}
	foreach($newsid as $sid){
   		mysql_query("INSERT INTO cidsid SET student_id='$sid', class_id='$newcid';");
   		}

	if(isset($detail)){
		$detail=clean_text($detail);
		mysql_query("UPDATE class SET detail='$detail' WHERE id='$newcid';");
		}
	if(isset($description)){
		$description=clean_text($description);
		$d_c=mysql_query("SELECT course_id, stage FROM cohort JOIN class ON cohort.id=class.cohort_id 
								WHERE class.id='$newcid';");
		$crid=mysql_result($d_c,0,0);
		$stage=mysql_result($d_c,0,1);
		mysql_query("UPDATE classes, class SET classes.description='$description' 
						WHERE classes.course_id='$crid' AND classes.subject_id=class.subject_id 
						AND classes.stage='$stage' AND class.id='$newcid';");
		}
	}

include('scripts/redirect.php');
?>
