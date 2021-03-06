<?php
/**								search_action.php
 *
 * called by the search options in the sidebar
 */

$ids=array();	
$action_post_vars=array('selsavedview','title','comid','yeargroup','comtype','section');
$title='';
//if(isset($_SESSION['savedview'])){$savedview=$_SESSION['savedview'];}
//else{$savedview='';}

/*posts from the student group search*/
if(isset($_POST['newyid']) and $_POST['newyid']!=''){
	$com=array('id'=>'','type'=>'year','name'=>$_POST['newyid']);
	$yeargroup=$_POST['newyid'];
	$selsavedview='year';
	}
elseif(isset($_POST['newfid']) and $_POST['newfid']!=''){
	$com=get_community($_POST['newfid']);
	$selsavedview='form';
	}
elseif(isset($_POST['newsecid']) and $_POST['newsecid']!=''){
	$com=array('id'=>$_POST['newsecid'],'type'=>'section','name'=>'');
	$section=$_POST['newsecid'];
	$selsavedview='section';
	}
elseif(isset($_POST['newcomid']) and $_POST['newcomid']!=''){
	if(is_numeric($_POST['newcomid'])){
		$com=get_community($_POST['newcomid']);
		$selsavedview='';
		$title='<label>'.get_string($com['type']).'</label> - '.$com['name'];
		}
	else{
		$comtype=$_POST['newcomid'];
		$title='<label>'.get_string($com['type']).'</label>';
		trigger_error($comtype);
		}
	}
elseif(isset($_POST['newcomid1']) and $_POST['newcomid1']!=''){
	//$com=array('id'=>$_POST['newcomid1'],'type'=>'','name'=>'');
	$com=get_community($_POST['newcomid1']);
	$selsavedview='';
	}

if(isset($com)){
	$table='student';
	$comid=$com['id'];

	if($com['type']=='accomodation'){
		/*TODO: temporary hack!*/
		$startdate='2000-01-01';
		$enddate='2015-01-01';
		$students=(array)listin_community($com,$enddate,$startdate);
		/*to remove!*/
		}
	elseif($com['type']=='tutor'){
		$students=(array)listin_community($com);
		$selsavedview='club';
		}
	elseif($com['type']=='transport'){
		$students=(array)list_bus_journey_students($com['name']);
		$selsavedview='transport';
		}
	elseif($com['type']=='section'){
		/* In this case $comid is $secid assigned in the first part */
		if($comid=='1' or $comid=='0'){$comid='%';}
		$yeargroups=list_yeargroups($comid);
		$students=array();
		foreach($yeargroups as $yg){
			$yid=$yg['id'];
			$comid=update_community(array('id'=>'','type'=>'year','name'=>$yid));
			$com=get_community($comid);
			$students=array_merge($students,listin_community($com));
			}
		$selsavedview='section';
		}
	else{
		$students=(array)listin_community($com);
		}

	$rows=sizeof($students);
	foreach($students as $student){
		$ids[]=$student['id'];
		}
	}
elseif(isset($comtype)){
	$table='student';
	$selsavedview='';
	$listcoms=(array)list_communities($comtype);
	$students=array();
	$sids=array();
	foreach($listcoms as $com){
		if($com['type']=='accomodation'){
			/*TODO: temporary hack!*/
			$startdate='2000-01-01';
			$enddate='2015-01-01';
			$comstudents=(array)listin_community($com,$enddate,$startdate);
			/*to remove!*/
			}
		elseif($com['type']=='tutor'){
			$comstudents=(array)listin_community($com);
			$selsavedview='club';
			}
		elseif($com['type']=='transport'){
			$comstudents=(array)list_bus_journey_students($com['name']);
			$selsavedview='transport';
			}
		else{
			$comstudents=(array)listin_community($com);
			}
		/* Students may be in more than one comunity but only list once. */
		foreach($comstudents as $student){
			if(!array_key_exists($student['id'],$sids)){
				$sids[$student['id']]=$student['id'];
				$ids[]=$student['id'];
				$students[]=$student;
				}
			}
		}
	$rows=sizeof($students);
	}

/* else results from the free text searches */
else{

	unset($field);
	/*these are the switchlabels set for guardian and student*/
	$sfield=$_POST['sfield'];
	$gfield=$_POST['gfield'];
	if(isset($_POST['contact'.$gfield]) and $_POST['contact'.$gfield]!=''){
		$value=clean_text($_POST['contact'.$gfield]);
		$table='guardian';
		$field=$gfield;
		}
	if(isset($_POST['student'.$sfield]) and $_POST['student'.$sfield]!=''){
		$value=clean_text($_POST['student'.$sfield]);
		$table='student';
		$field=$sfield;
		}
	/*
	 *	Returns array of $d_sids and number of $rows in it
	 *  $table should be set to student or guardian
	 */

	/*new search method using field and value - currently for admin and office only*/
	if(isset($field)){
		if(($table=='student' or $table=='guardian') and $field=='surname'){
			/*defaults to surname or gender search*/
			$d_sids=mysql_query("SELECT id FROM $table WHERE
				MATCH (surname) AGAINST ('*$value*' IN BOOLEAN MODE) 
				OR surname LIKE '%$value%' OR surname='$value' 
				ORDER BY surname, forename");
			}
		elseif($table=='student' and $field=='forename'){
			$d_sids=mysql_query("SELECT id FROM $table WHERE 
				MATCH (forename,preferredforename) AGAINST ('*$value*' IN BOOLEAN MODE) 
				OR forename='$value' OR forename LIKE '%$value%'
				ORDER BY surname, forename");
			}
		elseif($table=='student' and $field=='preferredforename'){
			$d_sids=mysql_query("SELECT id FROM $table WHERE 
				MATCH (preferredforename) AGAINST ('*$value*' IN BOOLEAN MODE) 
				OR preferredforename='$value' OR preferredforename LIKE '%$value%'
				ORDER BY surname, forename");
			}
		elseif($table=='student' and $field=='gender'){
			$d_sids=mysql_query("SELECT id FROM $table WHERE
				$field='$value' ORDER BY surname, forename");
			}
		elseif($table=='guardian' and $field=='forename'){
			$d_sids=mysql_query("SELECT id FROM $table WHERE 
				MATCH (forename) AGAINST ('*$value*' IN BOOLEAN MODE) 
				OR forename LIKE '%$value%' OR forename='$value'  
				ORDER BY surname, forename");
			}
		elseif($table=='guardian' and $field=='country'){
			$d_sids=mysql_query("SELECT DISTINCT guardian_id AS id FROM gidaid
				JOIN address ON gidaid.address_id=address.id WHERE
				MATCH (address.country) AGAINST ('$value*' IN BOOLEAN MODE) 
				OR address.country='$value'");
			}
		elseif($table=='guardian' and $field=='postcode'){
			$d_sids=mysql_query("SELECT DISTINCT guardian_id AS id FROM gidaid
				JOIN address ON gidaid.address_id=address.id WHERE
				MATCH (address.postcode) AGAINST ('$value*' IN BOOLEAN MODE) 
				OR address.postcode='$value'");
			}
		else{
			if($table=='student'){
				$d_sids=mysql_query("SELECT id FROM student JOIN info ON
					info.student_id=student.id WHERE
				MATCH (info.$field) AGAINST ('$value*' IN BOOLEAN MODE) 
				OR info.$field='$value' ORDER BY student.surname, student.forename");
				}
			elseif($table=='guardian'){
				//trigger_error($table.' '.$field.' '.$value,E_USER_WARNING);
				$d_sids=mysql_query("SELECT id FROM $table WHERE 
						$field LIKE '%$value%' OR $field='$value'  
						ORDER BY surname, forename");
				}
			}
		}

	/*simple search method using surname and forename for non-office staff*/
	else{
		$surname=clean_text($_POST['surname']);
		$forename=clean_text($_POST['forename']);
		$table='student';
		if($forename!='' and $surname!=''){
			$d_sids=mysql_query("SELECT id FROM $table WHERE
				(MATCH (surname) AGAINST ('$surname*' IN BOOLEAN MODE) 
					OR surname LIKE '%$surname%' OR $surname='$surname') 
					AND (MATCH (forename,preferredforename) 
					AGAINST ('$forename*' IN BOOLEAN MODE) 
					OR forename='$forename' OR preferredforename='$forename')
						ORDER BY surname, forename");
			}
		elseif($surname!=''){
			$d_sids=mysql_query("SELECT id FROM $table WHERE
				MATCH (surname) AGAINST ('$surname*' IN BOOLEAN MODE) 
				OR surname LIKE '%$surname%' 
				OR surname='$surname' ORDER BY surname, forename");
			}
		elseif($forename!=''){
			$d_sids=mysql_query("SELECT id FROM $table WHERE 
				MATCH (forename,preferredforename) 
				AGAINST ('*$forename*' IN BOOLEAN MODE) 
				OR forename='$forename' OR preferredforename='$forename' 
				ORDER BY surname, forename");
			}
		}

	if(!isset($d_sids)){
		$rows=0;
		$result[]='No matches found!';
		}
	else{
		$rows=mysql_num_rows($d_sids);
		while($student=mysql_fetch_array($d_sids,MYSQL_ASSOC)){
			$ids[]=$student['id'];
			}
		}
	}


if($rows>0 and $table=='student'){
	$_SESSION['infosids']=$ids;
	$_SESSION['infosearchgids']=array();
	$action='student_list.php';
	}
elseif($rows>0 and $table=='guardian'){
	$_SESSION['infosearchgids']=$ids;
	$_SESSION['infosids']=array();
	$action='contact_list.php';
	}
else{
	$result[]=get_string('nostudentsfoundtryanothersearch',$book);
	$action='';
	include('scripts/results.php');
	}
include('scripts/redirect.php');
?>
