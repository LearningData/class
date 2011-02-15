<?php
/**							lib/community_functions.php
 *	@package	ClaSS
 *	@author		stj@laex.org
 *	@copyright	S T Johnson 2004-2008
 *	@version	
 *	@since		
 */

/**
 * Return an array of communitites of one particular type
 * ignores differences in year by default
 *
 *	@param string $type type of community
 *	@param string $year 
 *	@return array all students in a community
 */
function list_communities($type='',$year=''){
	if($type!='' and $year==''){
		if($type=='year'){
			$d_com=mysql_query("SELECT community.id, community.name, 
					community.year, community.capacity,
					community.detail, yeargroup.name AS displayname 
					FROM community JOIN yeargroup ON
					community.name=yeargroup.id WHERE 
					community.type='$type' ORDER BY yeargroup.section_id,
					yeargroup.sequence");
			}
		elseif($type=='form'){
			$d_com=mysql_query("SELECT community.id, community.name, 
					community.year, community.capacity,
					community.detail, form.name AS displayname 
					FROM community JOIN form ON
					community.name=form.id WHERE 
					community.type='$type' ORDER BY form.yeargroup_id,
					form.name");
			}
		else{
			$d_com=mysql_query("SELECT id, name, year, capacity, detail FROM community WHERE 
								type='$type' ORDER BY name");
			}
		}
	elseif($type!=''){
		$d_com=mysql_query("SELECT id, name, year, capacity, detail FROM community WHERE 
								type='$type' AND year='$year' ORDER BY name");
		}

	$communities=array();
	if(isset($d_com) and mysql_num_rows($d_com)>0){
		while($com=mysql_fetch_array($d_com,MYSQL_ASSOC)){
			$community=array();
			$community['id']=$com['id'];
			$community['type']=$type;
			$community['name']=$com['name'];
			$community['year']=$com['year'];
			$community['capacity']=$com['capacity'];
			$community['detail']=$com['detail'];
			if(!isset($com['displayname'])){$community['displayname']=$community['name'];}
			else{$community['displayname']=$com['displayname'];}
			if($community['detail']!=''){$community['displayname']=$community['detail'];}
  			$communities[]=$community;
			}
		}

	return $communities;
	}

/**
 * Returns the xml Community identified by its comid
 *
 *	@param string $comid
 *	@return array 
 */
function fetchCommunity($comid=''){
  	$d_com=mysql_query("SELECT name, type, year, capacity, 
					detail FROM community WHERE id='$comid'");
	$com=mysql_fetch_array($d_com,MYSQL_ASSOC);
	$Community=array();
	$Community['id_db']=$comid;
	$Community['Type']=array('label' => 'type',
							 'value' => ''.$com['type']);
	$Community['Name']=array('label' => 'name',
							 'value' => ''.$com['name']);
	$Community['Year']=array('label' => 'year',
							 'value' => ''.$com['year']);
	$Community['Capacity']=array('label' => 'capacity',
							 'value' => ''.$com['capacity']);
	$Community['Detail']=array('label' => 'name',
							 'value' => ''.$com['detail']);
	if($community['detail']==''){$display=$community['name'];}
	else{$display=$community['detail'];}
	$Community['Displayname']=array('label' => 'displayname',
							 'value' => ''.$display);
	return $Community;
	}

/**
 * Returns a community array identified by its comid
 *
 *	@param string $comid
 *	@return array 
 *
 */
function get_community($comid=''){
	$community=array();
  	$d_com=mysql_query("SELECT name, type, year, capacity, detail, charge, chargetype, sessions 
					FROM community WHERE id='$comid'");
	if(mysql_num_rows($d_com)>0){
		$com=mysql_fetch_array($d_com,MYSQL_ASSOC);
		$community=$com;
		$community['id']=$comid;
		if($community['detail']==''){$community['displayname']=$community['name'];}
		else{$community['displayname']=$community['detail'];}
		}
	else{
		$community['id']='';
		$community['type']='';
		$community['name']='';
		$community['year']='';
		$community['capacity']='';
		$community['detail']='';
		$community['displayname']='';
		}
	return $community;
	}

/**
 *
 * This is the uber community function, it first checks if a community
 * exists and either updates or creates. It expects an array with at
 * least type and name set.
 *
 *	@param array $community
 *	@param array $communityfresh
 *	@return string $comid.
 *
 */
function update_community($community,$communityfresh=array('id'=>'','type'=>'','name'=>'')){
	$comid='';
	$type=$community['type'];
	$name=$community['name'];
	if(isset($community['year'])){$year=$community['year'];}
	elseif($type=='accepted' or $type=='applied' or $type=='enquired'){
		$year=get_curriculumyear();
		}
	else{$year='0000';}
	if(isset($community['detail'])){$detail=$community['detail'];}else{$detail='';}
	if(isset($community['capacity'])){$capacity=$community['capacity'];}else{$capacity='0';}
	if($type!='' and $name!=''){
		$d_community=mysql_query("SELECT id FROM community WHERE
				type='$type' AND name='$name' AND year='$year'");	
		if(mysql_num_rows($d_community)==0){
			if($type=='year'){
				/*a year group always gets a detail of yeargroup_name*/
				$d_y=mysql_query("SELECT name FROM yeargroup WHERE id='$name'");
				if(mysql_num_rows($d_y)>0){$detail=mysql_result($d_y,0);}
				else{$detail='No year group';}
				}
			mysql_query("INSERT INTO community (name,type,year,capacity,detail) VALUES
				('$name', '$type', '$year', '$capacity', '$detail')");
			$comid=mysql_insert_id();
			}
		else{
			$comid=mysql_result($d_community,0);
			$tfresh=$communityfresh['type'];
			$nfresh=$communityfresh['name'];
			if($tfresh!='' and $nfresh!=''){
				mysql_query("UPDATE community SET type='$tfresh', 
									name='$nfresh' WHERE id='$comid'");
				//trigger_error('upcom type:'.$type.' name:'.$name.' >' .$nfresh .mysql_error(),E_USER_WARNING);
				}
			if(isset($communityfresh['detail'])){
				$dfresh=$communityfresh['detail'];
				mysql_query("UPDATE community SET detail='$dfresh' WHERE id='$comid';");
				}
			if(isset($communityfresh['year'])){
				$yfresh=$communityfresh['year'];
				mysql_query("UPDATE community SET year='$yfresh' WHERE id='$comid';");
				}
			if(isset($communityfresh['capacity'])){
				$cfresh=$communityfresh['capacity'];
				mysql_query("UPDATE community SET capacity='$cfresh' WHERE id='$comid';");
				}
			if(isset($communityfresh['sessions'])){
				$cfresh=$communityfresh['sessions'];
				mysql_query("UPDATE community SET sessions='$cfresh' WHERE id='$comid';");
				}
			if(isset($communityfresh['charge'])){
				$cfresh=$communityfresh['charge'];
				mysql_query("UPDATE community SET charge='$cfresh' WHERE id='$comid';");
				}
			}
		}
	return $comid;
	}


/* 
 * Lists all sids who are current members of both commmunities
 *
 *	@param array $community1
 *	@param array $community2
 *	@return array 
 *
 */
function listin_union_communities($community1,$community2){
	$todate=date("Y-m-d");
	if(isset($community1['id']) and $community1['id']!=''){$comid1=$community1['id'];}
	else{$comid1=update_community($community1);}
	if(isset($community2['id']) and $community2['id']!=''){$comid2=$community2['id'];}
	else{$comid2=update_community($community2);}

	mysql_query("CREATE TEMPORARY TABLE com1students
			(SELECT a.student_id, b.surname, b.forename,
			b.middlenames, b.preferredforename, b.form_id FROM
			comidsid a, student b WHERE a.community_id='$comid1' AND
			b.id=a.student_id AND (a.leavingdate>'$todate' 
			OR a.leavingdate='0000-00-00' OR a.leavingdate IS NULL))");
	mysql_query("CREATE TEMPORARY TABLE com2students
			(SELECT a.student_id, b.surname, b.forename,
			b.middlenames, b.preferredforename, b.form_id FROM
			comidsid a, student b WHERE a.community_id='$comid2' AND
			b.id=a.student_id AND (a.leavingdate>'$todate' OR 
			a.leavingdate='0000-00-00' OR a.leavingdate IS NULL))");

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
	mysql_query("DROP TABLE com2students");
	mysql_query("DROP TABLE com1students");
	return array('scab'=>$scabstudents,'union'=>$unionstudents);
	}


/**
 *
 * Lists all sids who are current members of a commmunity.
 * With $stardate set all students who joined after that date
 * and with $enddate set lists all student members in that period.
 *
 *	@param array $community
 *	@param date $enddate
 *	@param date $startdate
 *	return array
 */
function listin_community($community,$enddate='',$startdate=''){
	$todate=date('Y-m-d');
	if($enddate==''){$enddate=$todate;}
	if($startdate==''){$startdate=$enddate;}
	if(isset($community['id']) and $community['id']!=''){$comid=$community['id'];}
	else{$comid=update_community($community);}

	$d_student=mysql_query("SELECT id, surname,
				forename, preferredforename, form_id, gender, dob, comidsid.special AS special FROM student 
				JOIN comidsid ON comidsid.student_id=student.id
				WHERE comidsid.community_id='$comid' AND
				(comidsid.leavingdate>'$enddate' OR 
				comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL) 
				AND (comidsid.joiningdate<='$startdate' OR 
				comidsid.joiningdate='0000-00-00' OR
				comidsid.joiningdate IS NULL) ORDER BY surname, forename");

	$students=array();
	while($student=mysql_fetch_array($d_student, MYSQL_ASSOC)){
		if($student['id']!=''){$students[]=$student;}
		}
	return $students;
	}


/**
 *
 * Lists all sids who newly joined a commmunity, not just current members.
 * With $stardate set all students who joined after that date.
 * With $enddate set lists all student members who joined between the two dates.
 *
 *	@param array $community
 *	@param date $enddate
 *	@param date $startdate
 *	return array
 */
function listin_community_new($community,$startdate='',$enddate=''){
	$todate=date('Y-m-d');
	if($enddate==''){$enddate=$todate;}
	if($startdate==''){$startdate=$enddate;}
	if(isset($community['id']) and $community['id']!=''){$comid=$community['id'];}
	else{$comid=update_community($community);}
	/* 
					AND (leavingdate>'$todate' OR 
					leavingdate='0000-00-00' OR leavingdate IS NULL) 
	*/
	$d_student=mysql_query("SELECT id, surname,
				forename, preferredforename, form_id, gender, dob, comidsid.special AS special FROM student 
				JOIN comidsid ON comidsid.student_id=student.id
				WHERE comidsid.community_id='$comid' 
				AND comidsid.joiningdate>='$startdate' AND comidsid.joiningdate<='$enddate' 
				ORDER BY surname, forename;");

	$students=array();
	while($student=mysql_fetch_array($d_student, MYSQL_ASSOC)){
		if($student['id']!=''){$students[]=$student;}
		}
	return $students;
	}


/** 
 * Joins $sid to the appropriate accomodation community based on a residencial stay
 * identified by $accid
 *
 *	@param string $sid
 *	@param string $accid 	
 *
 */
function set_accomodation($sid,$accid=''){
	$Student=fetchStudent_short($sid);
	$field=fetchStudent_singlefield($sid,'Boarder');
	$Student=array_merge($Student,$field);
	if($accid==''){
		$d_a=mysql_query("SELECT id FROM accomodation ORDER BY departuredate DESC");
		if(mysql_num_rows($d_a)<1){
			if($Student['Boarder']['value']!='' and  $Student['Boarder']['value']!='N'){
				mysql_query("INSERT INTO accomodation SET student_id='$sid'");
				$accid=mysql_insert_id();
				}
			}
		else{$a=mysql_fetch_array($d_a);$accid=$a['id'];}
		}
	$d_acc=mysql_query("SELECT * FROM accomodation WHERE id='$accid'");
	$acc=mysql_fetch_array($d_acc, MYSQL_ASSOC);
	$oldcomid=$acc['community_id'];
	$comname=$Student['Gender']['value']. $acc['roomcategory']. $Student['Boarder']['value'];
	if($oldcomid!=0){$oldcom=get_community($oldcomid);}
	else{$oldcom=array('id'=>'','name'=>'');}
	if($oldcom['name']!=$comname and $oldcomid!=0){
		/* if the accomodation needs have changed then the community */
		/* membership will have to change too, delete the old one*/
		mysql_query("DELETE FROM comidsid WHERE community_id='$oldcomid' AND student_id='$sid'");
		}

	if($Student['Boarder']['value']!='' and  $Student['Boarder']['value']!='N'){
		$community=array('type'=>'accomodation','name'=>$comname);
		$comid=set_community_stay($sid,$community,$acc['arrivaldate'],$acc['departuredate']);
		mysql_query("UPDATE accomodation SET community_id='$comid' WHERE id='$accid'");
		}
	else{
		mysql_query("DELETE FROM accomodation WHERE id='$accid' LIMIT 1");
		}
		/*delete any double bookings for accomodation!!!
		$d_comidsid=mysql_query("DELETE FROM comidsid USING comidsid, community WHERE
				 community.id=comidsid.community_id AND community.type='accomodation' 
				AND community.id!='$comid' AND comidsid.student_id='$sid' AND
   				(comidsid.joiningdate<'$enddate' OR comidsid.joiningdate IS NULL) 
				AND (comidsid.leavingdate>'$startdate' OR 
				comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL)");
		*/
	}

/** 
 * Joins up a student to a commmunity for a set period
 * Can be used instead of join_community but
 * NOT to be used for enrolment communities, yeargroups, or forms!
 *
 *	@param integer $sid student
 *	@param array $community 
 *	@param date $startdate
 *	@param date $enddate
 *	@return string
 */
function set_community_stay($sid,$community,$startdate,$enddate){
	if(isset($community['id']) and $community['id']!=''){$comid=$community['id'];}
	else{$comid=update_community($community);}
	if($comid!=''){
		$d_comidsid=mysql_query("SELECT * FROM comidsid WHERE
				community_id='$comid' AND student_id='$sid'");
		if(mysql_num_rows($d_comidsid)==0){
			mysql_query("INSERT INTO comidsid SET joiningdate='$startdate',
			   	leavingdate='$enddate', community_id='$comid', student_id='$sid'");
			}
		else{
			mysql_query("UPDATE comidsid SET joiningdate='$startdate', 
				leavingdate='$enddate' WHERE community_id='$comid' AND student_id='$sid'");
			}
		}
	return $comid;
	}


/**
 * Simply does what it says. In very rare occasions (ie. applications) 
 * the values are not counted but are instead static values stored in 
 * the community table itself.
 *
 *	@param array $community 
 *	@param date $startdate
 *	@param date $enddate
 *	@param logical $static
 *	@return integer
 */
function countin_community($community,$enddate='',$startdate='',$static=false){
	if(isset($community['id']) and $community['id']!=''){$comid=$community['id'];}
	else{$comid=update_community($community);}
	if($static){
		$d_c=mysql_query("SELECT count FROM community WHERE id='$comid';");
		$nosids=mysql_result($d_c,0);
		}
	else{
		$todate=date('Y-m-d');
		if($enddate==''){$enddate=$todate;}
		if($startdate==''){$startdate=$enddate;}
		$d_student=mysql_query("SELECT COUNT(student_id) FROM comidsid
				 WHERE community_id='$comid' AND (comidsid.leavingdate>'$enddate' OR 
				comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL) 
				AND (comidsid.joiningdate<='$startdate' OR 
				comidsid.joiningdate='0000-00-00' OR comidsid.joiningdate IS NULL);");
		$nosids=mysql_result($d_student,0);
		}
	return $nosids;
	}


/**
 * Another count function but this time returning a number for one gender.
 *
 *	@param array $community 
 *	@param date $startdate
 *	@param date $enddate
 *	@param string $gender
 *	@return integer
 *
 */
function countin_community_gender($community,$gender='M',$enddate='',$startdate=''){
	$todate=date('Y-m-d');
	if($enddate==''){$enddate=$todate;}
	if($startdate==''){$startdate=$enddate;}
	if(isset($community['id']) and $community['id']!=''){$comid=$community['id'];}
	else{$comid=update_community($community);}
	$d_student=mysql_query("SELECT COUNT(student_id) FROM comidsid
				JOIN student ON student.id=comidsid.student_id	
				WHERE comidsid.community_id='$comid' AND student.gender='$gender'
				AND (comidsid.leavingdate>='$enddate' OR 
				comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL) 
				AND (comidsid.joiningdate<='$startdate' OR 
				comidsid.joiningdate='0000-00-00' OR comidsid.joiningdate IS NULL)");
	$nosids=mysql_result($d_student,0);
	return $nosids;
	}

function countin_community_extra($community,$field,$value,$enddate='',$startdate=''){
	$todate=date('Y-m-d');
	if($enddate==''){$enddate=$todate;}
	if($startdate==''){$startdate=$enddate;}
	if(isset($community['id']) and $community['id']!=''){$comid=$community['id'];}
	else{$comid=update_community($community);}
	$d_student=mysql_query("SELECT COUNT(comidsid.student_id) FROM comidsid
				JOIN info ON info.student_id=comidsid.student_id	
				WHERE comidsid.community_id='$comid' AND info.$field='$value'
				AND (comidsid.leavingdate>='$enddate' OR 
				comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL) 
				AND (comidsid.joiningdate<='$startdate' OR 
				comidsid.joiningdate='0000-00-00' OR comidsid.joiningdate IS NULL)");
	$nosids=mysql_result($d_student,0);
	return $nosids;
	}



/**
 *
 * Returns all communities to which a student is currently enrolled.
 * Filter for community id, name or type optional.
 * With $stardate set all communities joined after that date
 * and with $enddate set lists communitiess in that period.
 *
 *	@param integer $sid
 *	@param array $community 

 *	@return array
 */
function list_member_communities($sid,$community,$current=true){
	$todate=date("Y-m-d");
	$type=$community['type'];
	$name=$community['name'];
	if($community['id']!=''){
		$comid=$community['id'];
		$d_community=mysql_query("SELECT * FROM community JOIN
				comidsid ON community.id=comidsid.community_id
				WHERE community.id='$comid' AND comidsid.student_id='$sid' AND
   				(comidsid.joiningdate<='$todate' OR comidsid.joiningdate  IS NULL) 
				AND (comidsid.leavingdate>'$todate' OR 
				comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL)");
		}
	elseif($name!=''){
		$comid=update_community($community);
		$d_community=mysql_query("SELECT * FROM community JOIN
				comidsid ON community.id=comidsid.community_id
				WHERE community.id='$comid' AND comidsid.student_id='$sid' AND
   				(comidsid.joiningdate<='$todate' OR comidsid.joiningdate IS NULL) 
				AND (comidsid.leavingdate>'$todate' OR 
				comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL)");
		}
	elseif($type!=''){
		if($current){
			$d_community=mysql_query("SELECT * FROM community JOIN
				comidsid ON community.id=comidsid.community_id
				WHERE community.type='$type' AND comidsid.student_id='$sid' AND
   				(comidsid.joiningdate<='$todate' OR comidsid.joiningdate IS NULL)
				AND (comidsid.leavingdate>'$todate' OR 
				comidsid.leavingdate IS NULL OR comidsid.leavingdate='0000-00-00')");
			}
		else{
			$d_community=mysql_query("SELECT * FROM community JOIN
				comidsid ON community.id=comidsid.community_id
				WHERE community.type='$type' AND comidsid.student_id='$sid' AND
   				(comidsid.joiningdate<=comidsid.leavingdate AND comidsid.leavingdate!='0000-00-00' AND comidsid.leavingdate<'$todate');");
			}
		}
	else{
		$d_community=mysql_query("SELECT * FROM community JOIN
				comidsid ON community.id=comidsid.community_id
				WHERE comidsid.student_id='$sid' AND
   				(comidsid.joiningdate<='$todate' OR comidsid.joiningdate IS NULL)
				AND (comidsid.leavingdate>'$todate' OR 
				comidsid.leavingdate IS NULL OR comidsid.leavingdate='0000-00-00')");
		}
	$communities=array();
   	while($community=mysql_fetch_array($d_community, MYSQL_ASSOC)){
		$communities[]=$community;
		}
	return $communities;
	}


/**
 *
 * Add a sid to a community, type must be set, if name is blank then
 * you are actually leaving any communities of that type. Will also
 * leave any communitites which conflict with the one being joined. Always
 * returns an array of oldcommunities left.
 *
 *	@param integer $sid
 *	@param array $community
 *	@return array
 *
 */
function join_community($sid,$community){
	$todate=date('Y-m-d');
	$type=$community['type'];
	$name=$community['name'];
	if(isset($community['year'])){$year=$community['year'];}
	else{$year='';}

	/* Membership of a form or yeargroup is exclusive - need to remove
	 * from old group first, and also where student progresses through
	 * application procedure from enquired to apllied to accepted to year
	 */
	$oldtypes=array();
    if($type=='form'){
		$studentfield='form_id';
		$oldtypes[]=$type;
		$enrolstatus='C';
		if($name!=''){
			/* If no new fid given then just remove from form group
					and leave in the same yeargroup*/
			$d_yeargroup=mysql_query("SELECT yeargroup_id FROM form WHERE id='$name';");
			$newyid=mysql_result($d_yeargroup,0);
			$d_student=mysql_query("SELECT yeargroup_id FROM student WHERE id='$sid';");
			$oldyid=mysql_result($d_student,0);
			/*if new form is in another yeargroup then need to move yeargroup too*/
			if($newyid!=$oldyid){join_community($sid,array('type'=>'year','name'=>$newyid));}
			}
		}
	elseif($type=='year'){
		$studentfield='yeargroup_id';
		$newyid=$name;
		$d_student=mysql_query("SELECT yeargroup_id FROM student WHERE id='$sid';");
		$oldyid=mysql_result($d_student,0);
		/*if moving yeargroup then need to leave old form too*/
		if($newyid!=$oldyid){$oldtypes[]='form';$oldtypes[]=$type;}
		/*may be joining from previous point in application procedure*/
		$oldtypes[]='alumni';
		$oldtypes[]='accepted';
		$oldtypes[]='applied';
		$oldtypes[]='enquired';
		/*on current roll so can't just disappear if yeargroup blank*/
		if($name=='' or $name=='none'){$name='none';$community['name']='none';}
		$enrolstatus='C';
		}
	elseif($type=='alumni'){
		$oldtypes[]='year';
		$oldtypes[]='form';
		/*may be joining from previous point in application procedure*/
		$oldtypes[]='accepted';
		$oldtypes[]='applied';
		$oldtypes[]='enquired';
		$enrolstatus='P';
		}
	elseif($type=='accepted' or $type=='applied' or $type=='enquired'){
		list($enrolstatus,$yid)=explode(':',$name);
		/*may be joining from previous point in application procedure*/
		$oldtypes[]='accepted';
		$oldtypes[]='applied';
		$oldtypes[]='enquired';
		/*remove from current roll if appropriate*/
		$d_student=mysql_query("SELECT yeargroup_id FROM student WHERE id='$sid'");
		if(mysql_num_rows($d_student)>0){
			$oldtypes[]='year';
			$oldtypes[]='form';
			}
		}

	if(isset($community['id']) and $community['id']!=''){$comid=$community['id'];}
	elseif($name!=''){$comid=update_community($community);}
	else{$comid='';}

	/*first remove sid from any old conflicting communities*/
	$leftcommunities=array();
	while(list($index,$oldtype)=each($oldtypes)){
		$checkcommunity=array('id'=>'','type'=>$oldtype,'name'=>'');
		$oldcommunities=array();
		$oldcommunities=(array)list_member_communities($sid,$checkcommunity);
		while(list($index,$oldcommunity)=each($oldcommunities)){
			if($oldcommunity['name']!=$name or $oldcommunity['year']!=$year){
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
		mysql_query("UPDATE info SET enrolstatus='$enrolstatus' WHERE student_id='$sid';");
		if($enrolstatus=='P'){
			/* Record the school leaving date. */
			mysql_query("UPDATE info SET leavingdate='$todate' WHERE student_id='$sid';");
			}
		}

	return $leftcommunities;
	}


/**
 *
 * Mark a sid as having left a commmunity
 * Does not delete the record only sets leavingdate to today
 * Should only really be called to do the work from within join_community
 *
 *	@param integer $sid
 *	@param array $community
 *	@return null
 */
function leave_community($sid,$community){
	$todate=date('Y-m-d');
	$type=$community['type'];
	$name=$community['name'];
	if($community['id']!=''){
		$comid=$community['id'];
		//else{$comid=update_community($community);}
		mysql_query("UPDATE comidsid SET leavingdate='$todate' WHERE
							community_id='$comid' AND student_id='$sid'");
		}
	if($type=='year'){mysql_query("UPDATE student SET yeargroup_id=NULL WHERE id='$sid'");}
	elseif($type=='form'){mysql_query("UPDATE student SET form_id='' WHERE id='$sid'");}
	return;
	}


/**
 * Returns the yeargroup to which the form belongs.
 *
 *	@param integer $fid
 *	@return integer
 */
function get_form_yeargroup($fid){
	if($fid!=' ' and $fid!=''){
		$d_subject=mysql_query("SELECT yeargroup_id FROM form WHERE id='$fid'");
		$yid=mysql_result($d_subject,0);
		}
	else{
		$yid='';
		}
	return $yid;
	}

/**
 * Reutrns the form tutor for hte given fid
 *
 *	@param integer $fid
 *	@return string
 */
function get_tutor_user($fid){
	if($fid!=' ' and $fid!=''){
		$d_t=mysql_query("SELECT teacher_id FROM form WHERE id='$fid';");
		$tid=mysql_result($d_t,0);
		$user=get_user($tid);
		}
	else{
		$user=array();
		}
	return $user;
	}


/** 
 * Returns the section id for the yeargroup to which the 
 * student belongs, defaults to whole school secid=1 if nothing else available.
 *
 *	@param integer $sid
 *	@return integer
 */
function get_student_section($sid){
	if($sid!=' ' and $sid!=''){
		$d_s=mysql_query("SELECT section_id FROM yeargroup JOIN
				student ON student.yeargroup_id=yeargroup.id WHERE student.id='$sid';");
		$secid=mysql_result($d_s,0);
		}
	else{
		$secid=1;// default to whole school
		}
   	//trigger_error('SECTION'.$secid,E_USER_WARNING);
	return $secid;
	}

/**
 * 
 * Find all current cohorts with which a community is associated.
 * Only returns cohorts for this academic year.
 *
 *	@param array $community
 *	@return array
 *
 */
function list_community_cohorts($community){
	if($community['type']=='form'){
		/*forms only associate with cohorts through their yeargroup*/
		$fid=$community['name'];
		$d_form=mysql_query("SELECT yeargroup_id FROM form WHERE id='$fid';");
		if(mysql_num_rows($d_form)>0){$yid=mysql_result($d_form,0);}
		else{$yid='';}
		$community=array('id'=>'','type'=>'year','name'=>$yid);
		}

	if($community['id']!=''){$comid=$community['id'];}
	else{$comid=update_community($community);}

	$cohorts=array();
	$d_cohort=mysql_query("SELECT * FROM cohort JOIN
						cohidcomid ON cohidcomid.cohort_id=cohort.id WHERE
						cohidcomid.community_id='$comid' ORDER BY course_id;");
   	while($cohort=mysql_fetch_array($d_cohort, MYSQL_ASSOC)){
		$currentyear=get_curriculumyear($cohort['course_id']);
		$currentseason='S';
		if($cohort['year']==$currentyear and $cohort['season']==$currentseason){
			$cohorts[]=$cohort;
			}
		//$cohorts[]=$cohort;
		}
	return $cohorts;
	}


/**
 *
 * Generates a new epfusername for the given User xml-array.
 *
 * The epfusername format is decided by one of three possible roles: 
 * staff, student or guardian.
 *
 * Student username will be firstname plus four digit number.
 * Contact username will be first six characters of surname plus four digit number.
 * Staff username will be the schools clientid plus their ClaSS username.
 *
 * This on its own does not guarantee a unique epfusername (which it
 * must be!) but instead relies on the calling function to ensure
 * uniqueness (probably through an ldap query).
 *
 *	@param array $User
 *	@param string role	
 *	@return string
 */
function generate_epfusername($User=array(),$role='student'){
	global $CFG;
	setlocale(LC_CTYPE,'en_GB');
	$epfusername='';
	$classid=$User['id_db'];

	$nums='';
	$code='';
	if($role=='student'){
		/* Only use the first part of the forename. */
		$forename=(array)explode(' ',$User['Forename']['value']);
		$start=iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $forename[0]);
		$start=utf8_to_ascii($start);
		$classtable='info';
		$classfield='student_id';
		while(count($nums)<9){$nums[rand(1,9)]=null;}
		while(strlen($code)<4){$code.=array_rand($nums);}
		$tail=$code;
		}
	elseif($role=='guardian'){
		/* Only use the first part of the surname. */
		$surname=$User['Surname']['value'];
		$start=iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $surname);
		$start=utf8_to_ascii($start);
		$start=str_replace(' ','',$start);
		$start=str_replace("'",'',$start);
		$start=str_replace('-','',$start);
		$start=str_replace('?','',$start);
		$start=substr($start,0,6);
		$classtable='guardian';
		$classfield='id';
		while(count($nums)<9){$nums[rand(1,9)]=null;}
		while(strlen($code)<4){$code.=array_rand($nums);}
		$tail=$code;
		}
	elseif($role=='staff'){
		/* Staff usernames are unique within their own ClaSS but need
		 * to maintain that within the epf by adding the school's clientid.
		 */
		if(isset($CFG->clientid)){$start=$CFG->clientid;}
		else{$start='';}
		$tail=$User['Username']['value'];
		$classtable='users';
		$classfield='uid';
		}

	$epfusername=good_strtolower($start. $tail);
	$start=str_replace('-','',$epfusername);
	$epfusername=str_replace("'",'',$epfusername);
	$epfusername=str_replace(' ','',$epfusername);
	$epfusername=clean_text($epfusername);

	mysql_query("UPDATE $classtable SET epfusername='$epfusername' WHERE $classfield='$classid';");

	return $epfusername;
	}

?>
