<?php
/*								define_grades_action2.php
*/

$current='new_assessment.php';
$action='new_assessment.php';
$choice='new_assessment.php';
$cancel='define_grades.php';

include('scripts/sub_action.php');

$gena=$_POST['gena'];
$num=$_POST['num'];
$bid=$_POST['bid'];
$crid=$_POST['crid'];
$comment=$_POST['comment'];
$grades=$_POST['grades'];
$weights=$_POST['weights'];

$gradelist='';
$previous=-1000;
for($c3=0; $c3<$num; $c3++){
    if ($weights[$c3]>$previous){
	if($c3>0){$gradelist=$gradelist.';'.$grades[$c3].':'.$weights[$c3];}
	else{$gradelist=$grades[$c3].':'.$weights[$c3];}
	$previous=$weights[$c3];
	}
    else{
	$error[]='Grades must be in ascending order!';
	$current='define_grades_action1.php';
	include('scripts/results.php');
	include('scripts/redirect.php');
	}
    }

if(mysql_query("INSERT INTO grading SET name='$gena',
	grades='$gradelist', comment='$comment',  author='$tid'")){
    $result[]='Grades have been weighted.';
    }
else{
    $error[]='Failed on levelling insert!'; $error[]=mysql_error();
    }

include('scripts/results.php');
include('scripts/redirect.php');
?>
