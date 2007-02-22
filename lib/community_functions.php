<?php

/* return an array of communitites of one particular type*/
function list_communities($type=''){
	if($type!=''){
		$d_com=mysql_query("SELECT id, name FROM community WHERE 
								type='$type' ORDER BY name");
		$communities=array();
		while($com=mysql_fetch_array($d_com,MYSQL_ASSOC)){
			$community=array();
			$community['id']=$com['id'];
			$community['type']=$type;
			$community['name']=$com['name'];
			$communities[]=$community;
			}
		}
	return $communities;
	}

/*Returns the Community identified by its comid*/
function fetchCommunity($comid=''){
  	$d_com=mysql_query("SELECT name, type FROM community WHERE id='$comid'");
	$com=mysql_fetch_array($d_com,MYSQL_ASSOC);
	$Community=array();
	$Community['id_db']=$comid;
	$Community['Type']=array('label' => 'type',
							 'value' => ''.$com['type']);
	$Community['Name']=array('label' => 'name',
							 'value' => ''.$com['name']);
	return $Community;
	}

/* checks for a community and either updates or creates*/
/* expects an array with at least type and name set*/
function update_community($community,$communityfresh=array('id'=>'','type'=>'','name'=>'')){
	$comid='';
	$type=$community['type'];
	$name=$community['name'];
	//trigger_error('upcom type:'.$type.' name:'.$name,E_USER_WARNING);
	$typefresh=$communityfresh['type'];
	$namefresh=$communityfresh['name'];
	if(isset($community['details'])){$details=$community['details'];}
	if($type!='' and $name!=''){
		$d_community=mysql_query("SELECT id FROM community WHERE
				type='$type' AND name='$name'");	
		if(mysql_num_rows($d_community)==0){
			mysql_query("INSERT INTO community (name,type,details) VALUES
				('$name', '$type', '$details')");
			$comid=mysql_insert_id();
			}
		else{
			$comid=mysql_result($d_community,0);
			if($typefresh!='' and $namefresh!=''){
				if(isset($communityfresh['details'])){$detailsfresh=$communityfresh['details'];}
				mysql_query("UPDATE community SET type='$typefresh',
							name='$namefresh', details='$detailsfresh' WHERE name='$name'
								AND type='$type'");
				}
			}
		}
	return $comid;
	}


/*Lists all sids who are current members of a commmunity*/
function listin_union_communities($community1,$community2){
	if($community1['id']!=''){$comid1=$community1['id'];}
	else{$comid1=update_community($community1);}
	if($community2['id']!=''){$comid2=$community2['id'];}
	else{$comid2=update_community($community2);}

	mysql_query("CREATE TEMPORARY TABLE com1students
			(SELECT a.student_id, b.surname, b.forename,
			b.middlenames, b.preferredforename, b.form_id FROM
			comidsid a, student b WHERE a.community_id='$comid1' AND
			b.id=a.student_id AND (a.leavingdate='0000-00-00' OR a.leavingdate IS NULL))");
	mysql_query("CREATE TEMPORARY TABLE com2students
			(SELECT a.student_id, b.surname, b.forename,
			b.middlenames, b.preferredforename, b.form_id FROM
			comidsid a, student b WHERE a.community_id='$comid2' AND
			b.id=a.student_id AND (a.leavingdate='0000-00-00' OR a.leavingdate IS NULL))");
  	$d_student=mysql_query("SELECT a.student_id, a.forename, a.middlenames,
					a.surname, a.preferredforename, a.form_id FROM
					com2students AS a LEFT JOIN com1students AS b ON
					a.student_id=b.student_id WHERE
					b.student_id IS NULL ORDER BY a.form_id, a.surname");
	$scabstudents=array();
	while($student=mysql_fetch_array($d_student, MYSQL_ASSOC)){
		if($student['student_id']!=''){$scabstudents[]=$student;}
		}

  	$d_student=mysql_query("SELECT a.student_id, a.forename, a.middlenames,
					a.surname, a.preferredforename, a.form_id FROM
					com2students AS a LEFT JOIN com1students AS b ON
					a.student_id=b.student_id WHERE
					b.student_id IS NOT NULL ORDER BY a.form_id, a.surname");
	$unionstudents=array();
	while($student=mysql_fetch_array($d_student, MYSQL_ASSOC)){
		if($student['student_id']!=''){$unionstudents[]=$student;}
		}
	return array('scab'=>$scabstudents,'union'=>$unionstudents);
	}

/*Lists all sids who are current members of a commmunity*/
function listin_community($community){
	if($community['id']!=''){$comid=$community['id'];}
	else{$comid=update_community($community);}
	$d_student=mysql_query("SELECT id, surname,
				forename, form_id FROM student 
				JOIN comidsid ON comidsid.student_id=student.id
				WHERE comidsid.community_id='$comid' AND
				(comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL)
					ORDER BY student.surname");
	$students=array();
	while($student=mysql_fetch_array($d_student, MYSQL_ASSOC)){
		if($student['id']!=''){$students[]=$student;}
		}
	return $students;
	}

/*simply does what it says*/
function countin_community($community){
	if($community['id']!=''){$comid=$community['id'];}
	else{$comid=update_community($community);}
	$d_student=mysql_query("SELECT COUNT(student_id) FROM comidsid
							  WHERE community_id='$comid' AND
				(comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL)");
	$nosids=mysql_result($d_student,0);
	return $nosids;
	}

/*Returns all communities to which a student is currently enrolled*/
function list_member_communities($sid,$community){
	$todate=date("Y-m-d");
	$type=$community['type'];
	$name=$community['name'];
	if($community['id']!=''){
		$comid=$community['id'];
		$d_community=mysql_query("SELECT * FROM community JOIN
				comidsid ON community.id=comidsid.community_id
				WHERE community.id='$comid' AND comidsid.student_id='$sid' AND
   				(comidsid.entrydate<='$todate' OR comidsid.joiningdate  IS NULL) 
				AND (comidsid.leavingdate IS NULL OR comidsid.leavingdate='0000-00-00')");
		}
	elseif($name!=''){
		$comid=update_community($community);
		$d_community=mysql_query("SELECT * FROM community JOIN
				comidsid ON community.id=comidsid.community_id
				WHERE community.id='$comid' AND comidsid.student_id='$sid' AND
   				(comidsid.entrydate<='$todate' OR comidsid.joiningdate IS NULL) 
				AND (comidsid.leavingdate IS NULL OR comidsid.leavingdate='0000-00-00')");
		}
	elseif($type!=''){
		$d_community=mysql_query("SELECT * FROM community JOIN
				comidsid ON community.id=comidsid.community_id
				WHERE community.type='$type' AND comidsid.student_id='$sid' AND
   				(comidsid.joiningdate<='$todate' OR comidsid.joiningdate IS NULL)
				AND (comidsid.leavingdate IS NULL OR comidsid.leavingdate='0000-00-00')");
		}
	else{
		$d_community=mysql_query("SELECT * FROM community JOIN
				comidsid ON community.id=comidsid.community_id
				WHERE comidsid.student_id='$sid' AND
   				(comidsid.joiningdate<='$todate' OR comidsid.joiningdate IS NULL)
				AND (comidsid.leavingdate IS NULL OR comidsid.leavingdate='0000-00-00')");
		}
	$communities=array();
   	while($community=mysql_fetch_array($d_community, MYSQL_ASSOC)){
		$communities[]=$community;
		}
	return $communities;
	}

/* Add a sid to a community, type must be set, if name is blank then */
/* you are actually leaving any communities of that type. Will also */
/* leave any communitites which conflict the one being joined. Always */
/* returns an array of oldcommunities left*/
function join_community($sid,$community){
	$todate=date("Y-m-d");
	$type=$community['type'];
	$name=$community['name'];

	/*membership of a form or yeargroup is exclusive - need to remove
	from old group first, and also where student progresses through
	application procedure from enquired to apllied to accepted to year*/
	$oldtypes=array();
    if($type=='form'){
		$studentfield='form_id';
		$oldtypes[]=$type;
		$enrolstatus='C';
		$d_yeargroup=mysql_query("SELECT yeargroup_id FROM form WHERE id='$name'");
		$newyid=mysql_result($d_yeargroup,0);
		$d_student=mysql_query("SELECT yeargroup_id FROM student WHERE id='$sid'");
		$oldyid=mysql_result($d_yeargroup,0);
		if($newyid!=$oldyid){join_community($sid,array('type'=>'year','name'=>$yid));}
		}
	elseif($type=='year'){
		$studentfield='yeargroup_id';
		$oldtypes[]='form';
		$oldtypes[]=$type;
		$oldtypes[]='accepted';
		$oldtypes[]='applied';
		$oldtypes[]='enquired';
		$enrolstatus='C';
		/*on current roll so can't just disappear*/
		if($name=='' or $name=='none'){$name='none';$community['name']='none';}
		}
	elseif($type=='alumni'){
		$oldtypes[]='year';
		$oldtypes[]='form';
		$enrolstatus='P';
		}
	elseif($type=='accepted'){
		$oldtypes[]='applied';
		$oldtypes[]='enquired';
		$enrolstatus='AC';
		}
	elseif($type=='applied'){
		$oldtypes[]='enquired';
		$enrolstatus='AP';
		}
	elseif($type=='enquired'){
		$enrolstatus='EN';
		}
	if($community['id']!=''){$comid=$community['id'];}
	elseif($name!=''){$comid=update_community($community);}
	else{$comid='';}

	/*first remove sid from any old conflicting communities*/
	$leftcommunities=array();
	while(list($index,$oldtype)=each($oldtypes)){
		$checkcommunity=array('id'=>'','type'=>$oldtype,'name'=>'');
		$oldcommunities=array();
		$oldcommunities=list_member_communities($sid,$checkcommunity);
		while(list($index,$oldcommunity)=each($oldcommunities)){
			if($oldcommunity['name']!=$name){
				$leftcommunities[$oldtype][]=$oldcommunity;
				leave_community($sid,$oldcommunity);
				}
			}
		}

	if($comid!=''){
		$d_comidsid=mysql_query("SELECT * FROM comidsid WHERE
				community_id='$comid' AND student_id='$sid'");
		if(mysql_num_rows($d_comidsid)==0){
			mysql_query("INSERT INTO comidsid SET joiningdate='$todate',
							community_id='$comid', student_id='$sid'");
			}
		else{
			mysql_query("UPDATE comidsid SET leavingdate='' WHERE
							community_id='$comid' AND student_id='$sid'");
			}
		}

	/*update the student with new enrolstatus, and new id for form or yeargroup*/
	if(isset($studentfield)){
		if($name!='none'){
			mysql_query("UPDATE student SET $studentfield='$name' WHERE id='$sid'");
			}
		else{
			mysql_query("UPDATE student SET $studentfield NULL WHERE id='$sid'");
			}
		}
	if(isset($enrolstatus)){
		mysql_query("UPDATE info SET enrolstatus='$enrolstatus' WHERE student_id='$sid'");
		}

	return $leftcommunities;
	}

/* Remove a sid from a commmunity*/
/* Should only really be called to do the work from within join_community*/
function leave_community($sid,$community){
	$todate=date('Y-m-d');
	$type=$community['type'];
	$name=$community['name'];
	if($community['id']!=''){$comid=$community['id'];}
	else{$comid=update_community($community);}
	mysql_query("UPDATE comidsid SET leavingdate='$todate' WHERE
							community_id='$comid' AND student_id='$sid'");
	if($type=='year'){mysql_query("UPDATE student SET yeargroup_id=NULL WHERE id='$sid'");}
	elseif($type=='form'){mysql_query("UPDATE student SET form_id='' WHERE id='$sid'");}
	return;
	}

/*Checks for a cohort and creates if it doesn't exist*/
/*expects an array with at least course_id and stage set*/
/*returns the cohort_id*/
function update_cohort($cohort){
	$crid=$cohort['course_id'];
	$stage=$cohort['stage'];
	if(isset($cohort['year'])){$year=$cohort['year'];}
	else{$year=get_curriculumyear($crid);}
	if(isset($cohort['season'])){$season=$cohort['season'];}
	else{$season='S';}
	if($crid!='' and $stage!=''){
		$d_cohort=mysql_query("SELECT id FROM cohort WHERE
				course_id='$crid' AND stage='$stage' AND year='$year'
				AND season='$season'");
		if(mysql_num_rows($d_cohort)==0){
			mysql_query("INSERT INTO cohort (course_id,stage,year,season) VALUES
				('$crid','$stage','$year','$season')");
			$cohid=mysql_insert_id();
			}
		else{
			$cohid=mysql_result($d_cohort,0);
			}
		}
	return $cohid;
	}


/*Lists all sids who are current members of a cohort*/
function listin_cohort($cohort){
	if($cohort['id']!=''){$cohid=$cohort['id'];}
	else{$cohid=update_cohort($cohort);}
	mysql_query("CREATE TEMPORARY TABLE cohortstudent (SELECT DISTINCT student_id FROM comidsid 
				JOIN cohidcomid ON comidsid.community_id=cohidcomid.community_id
				WHERE cohidcomid.cohort_id='$cohid' AND
				(comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL))");
	$d_cohortstudent=mysql_query("SELECT b.id, b.surname,
				b.forename, b.middlenames, b.preferredforename, 
				b.form_id FROM cohortstudent a,
				student b WHERE b.id=a.student_id ORDER BY b.surname");
	$students=array();
   	while($student=mysql_fetch_array($d_cohortstudent,MYSQL_ASSOC)){
		$students[]=$student;
		}
	return $students;
	}

/*Find all current cohorts which a community is associated with*/
function list_community_cohorts($community){
	if($community['type']=='form'){
		/*forms only associate with cohorts through their yeargroup*/
		$fid=$community['name'];
		$d_form=mysql_query("SELECT yeargroup_id FROM form WHERE id='$fid'");
		$yid=mysql_result($d_form,0);
		$community=array('id'=>'','type'=>'year','name'=>$yid);
		}

	if($community['id']!=''){$comid=$community['id'];}
	else{$comid=update_community($community);}

	$cohorts=array();
	$d_cohort=mysql_query("SELECT * FROM cohort JOIN
						cohidcomid ON cohidcomid.cohort_id=cohort.id WHERE
						cohidcomid.community_id='$comid' ORDER BY course_id");
   	while($cohort=mysql_fetch_array($d_cohort, MYSQL_ASSOC)){
		$currentyear=get_curriculumyear($cohort['course_id']);
		$currentseason='S';
		if($cohort['year']==$currentyear and $cohort['season']==$currentseason){
			$cohorts[]=$cohort;
			}
		}
	return $cohorts;
	}

/*Defined as the calendar year that the current academic year ends */
/*TODO to sophisticate in future to cover definite endmonths for courses*/
function get_curriculumyear($crid=''){
	$d_course=mysql_query("SELECT endmonth FROM course WHERE id='$crid'");
	if(mysql_num_rows($d_course)>0){$endmonth=mysql_result($d_course,0);}
	else{$endmonth='';}
	if($endmonth==''){$endmonth='7';/*defaults to July*/}
	$thismonth=date('m');
	$thisyear=date('Y');
	if($thismonth>$endmonth){$thisyear++;}
	return $thisyear;
	}

/*Returns the id for the cohort specified by crid and stage*/
/*will default to the current active cohort unless year is specified*/
/*TODO should be made redundant by update_cohort()?*/
//function getcurrentCohortId($crid,$stage,$year='',$season='S'){
//	if($year==''){$year=getCurriculumYear($crid);}
//	$d_cohort=mysql_query("SELECT id FROM cohort WHERE
//						course_id='$crid' AND stage='$stage' AND
//						year='$year' AND season='$season'");
//	$cohid=mysql_result($d_cohort,0);
//	return $cohid;
//	}

?>