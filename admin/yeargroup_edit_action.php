<?php 
/**									   		yeargroup_edit_action.php
 */

$action='yeargroup_edit.php';
$action_post_vars=array('newcomid','comid','enrolyear');

if(isset($_POST['comid'])){$comid=$_POST['comid'];}else{$comid='';}
if(isset($_POST['newcomid'])){$newcomid=$_POST['newcomid'];}else{$newcomid='';}
if(isset($_POST['newsids'])){$newsids=(array)$_POST['newsids'];}
else{$newsids=array();}
if(isset($_POST['sids'])){$oldsids=(array)$_POST['sids'];}
else{$oldsids=array();}
if(isset($_POST['enrolyear'])){$enrolyear=$_POST['enrolyear'];}

include('scripts/sub_action.php');

		/*Check user has permission to edit*/
		$perm=getYearPerm($comname);
		$neededperm='w';
		include('scripts/perm_action.php');

if($sub=='Submit'){
	/*not currently offering this option to the user*/
	/*should really always be yes, surely?*/
	$classestoo='yes';

	/*sids to remove*/
	$newcommunity=get_community($newcomid);
   	foreach($oldsids as $sid){
		$oldcommunities=join_community($sid,$newcommunity);
		if($classestoo=='yes' and isset($oldcommunities['form'])){
			$changeclasses=(array)list_forms_classes($oldcommunities['form'][0]['name']);
			foreach($changeclasses as $class){
				$cid=$class['id'];
				mysql_query("DELETE FROM cidsid WHERE student_id='$sid' AND class_id='$cid' LIMIT 1;");
				}
			}
		}

	/*sids to add*/
	$currentcommunity=get_community($comid);
	foreach($newsids as $sid){
		$oldcommunities=join_community($sid,$currentcommunity);
		if($classestoo=='yes' and isset($oldcommunities['form'])){
			$changeclasses=(array)list_forms_classes($oldcommunities['form'][0]['name']);
			foreach($changeclasses as $class){
				$cid=$class['id'];
				mysql_query("DELETE FROM cidsid WHERE student_id='$sid' AND class_id='$cid' LIMIT 1");
				}
			}
		}
	}

include('scripts/redirect.php');
?>
