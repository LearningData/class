<?php
/**								student_transport_action.php
 *
 */

$action='student_transport.php';
include('scripts/sub_action.php');
if(isset($_POST['newcomid'])){$newcomid=$_POST['newcomid'];}else{$newcomid='';}

if($sub=='Submit'){
	/*Check user has permission to edit*/
	$yid=$Student['YearGroup']['value'];
	$perm=getYearPerm($yid, $respons);
	include('scripts/perm_action.php');

	$coms=list_member_communities($sid,array('id'=>'','name'=>'','type'=>'tutor'));
	$coms=array_merge($coms,list_member_communities($sid,array('id'=>'','name'=>'','type'=>'tutor'),false));
	foreach($coms as $com){
		$comid=$com['id'];
		if(isset($_POST[$comid.'fee'.$sid]) and $_POST[$comid.'fee'.$sid]!=$com['special']){
			$fee=$_POST[$comid.'fee'.$sid];
			mysql_query("UPDATE comidsid SET special='$fee' WHERE community_id='$comid' AND student_id='$sid';");
			}
		}
	}
elseif($sub=='TUTOR' and $newcomid!=''){

	$newcomtype=$sub;
	$enddate='0000-00-00';
	$startdate=date('Y-m-d');
	/*TODO: allowing setting of joining and leaving dates for clubs. */
	//if(isset($_POST['date0'])){$startdate=$_POST['date0'];}else{$startdate=date('Y-m-d');}

	$com=array('type'=>$newcomtype,'id'=>$newcomid);
	set_community_stay($sid,$com,$startdate,$enddate);

	}

	include('scripts/redirect.php');
?>
