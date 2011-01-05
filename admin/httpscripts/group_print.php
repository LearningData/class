<?php
/**									group_print.php
 *
 */

require_once('../../scripts/http_head_options.php');
require_once('../../lib/fetch_transport.php');

/*NB. The comids are pulled by checksidsAction hence they really are called sids!*/
if(isset($_GET['sids'])){$comids=(array)$_GET['sids'];}else{$comids=array();}
if(isset($_POST['sids'])){$comids=(array)$_POST['sids'];}
if(isset($_GET['newcomtype'])){$newcomtype=$_GET['newcomtype'];}else{$newcomtype='';}
if(isset($_POST['newcomtype'])){$newcomtype=$_POST['newcomtype'];}

$printdate=date('Y-m-d');
$today=date('N',strtotime($printdate));
/* calculate difference in days from now for past attendance */
$d=explode('-',$printdate);
$diff=mktime(0,0,0,date('m'),date('d'),date('Y'))-mktime(0,0,0,$d[1],$d[2],$d[0]);
$attday=-round($diff/(60*60*24));


if(sizeof($comids)==0){
	$result[]=get_string('youneedtoselectstudents');
	$returnXML=$result;
	$rootName='Error';
	}
else{

	$Students=array();
	$Students['Group']=array();

	foreach($comids as $comid){
		if($comid!=''){

			$com=get_community($comid);
			$students=(array)listin_community($com);
			$Group=array();
			$Group['Name']=array('value'=>$com['name']);
			$Group['Day']=array('value'=>get_string(displayEnum($today,'dayofweek'),$book));
			$Group['Date']=array('value'=>display_date($printdate));
			$Group['Student']=array();
			$community=array('id'=>'','name'=>'','type'=>'tutor');

			foreach($students as $student){
				$sid=$student['id'];
				$Student=(array)fetchStudent_short($sid);

				/*
				$communities=(array)list_member_communities($sid,$community);
				foreach($communities as $club){
					$pos=strpos($club['sessions'],"A$today");
					if($pos!==false){
						$Student['Club']['value']=$club['name'];
						}
					}
				*/
				$Student['Attendances']=(array)fetchAttendances($sid,$attday,1);
				$Student['Journey']=array();
				$field=fetchStudent_singlefield($sid,'FirstContactPhone');
				$Student=array_merge($Student,$field);
				$field=fetchStudent_singlefield($sid,'SecondContactPhone');
				$Student=array_merge($Student,$field);
				$Group['Student'][]=$Student;
				}
			$Students['Group'][]=$Group;
			}
		}

	$returnXML=$Students;
	$rootName='Students';
	}

require_once('../../scripts/http_end_options.php');
exit;
?>