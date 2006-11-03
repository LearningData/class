<?php	
/**												permissions.php
 */	

function findResponsibles($sid, $bid){
  /* given a sid and a bid this will return a numerical array which
   * lists the responsibles (both pastoral academic) who have been
   * flagged to receive emails
   */

    $gids=array();
	$recipients=array();

	/*first find pastoral group*/
  	$d_group = mysql_query("SELECT gid FROM groups JOIN student
  	ON student.yeargroup_id=groups.yeargroup_id WHERE
  	student.id='$sid' AND groups.course_id=''"); 
	$group=mysql_fetch_array($d_group);
	$gids[]=$group['gid'];

	/*academic groups for course and subject*/
	if($bid!='' and $bid!='%' and $bid!='General'){
	  	$d_class=mysql_query("SELECT course_id FROM class JOIN cidsid
		  	ON cidsid.class_id=class.id WHERE
		  	class.subject_id='$bid' AND cidsid.student_id='$sid'"); 
		$crid=mysql_result($d_class,0);
	  	$d_group=mysql_query("SELECT gid FROM groups WHERE
		  	course_id='$crid' AND subject_id='%'");
		$group=mysql_fetch_array($d_group);
		$gids[]=$group['gid'];

	  	$d_group=mysql_query("SELECT gid FROM groups WHERE
		  	course_id='%' AND subject_id='$bid'"); 
		$group=mysql_fetch_array($d_group);
		$gids[]=$group['gid'];

	  	$d_group=mysql_query("SELECT gid FROM groups WHERE
		  	course_id='$crid' AND subject_id='$bid'"); 
		$group=mysql_fetch_array($d_group);
		$gids[]=$group['gid'];
		}

	foreach($gids as $key => $gid){
		$d_users=mysql_query("SELECT * FROM users JOIN perms ON users.uid=perms.uid WHERE
			perms.gid='$gid' AND perms.e='1'");
		while($user=mysql_fetch_array($d_users)){
			if(eregi('^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$', $user['email'])){ 
				$recipients[]=array('username'=>$user['username'], 'email'=>$user['email']);
				}
			}
		}
	return $recipients;
	}

function getResponStaff($tid,$respons,$r=0){
	/* will return all details of users of interest based on the
	teaching staff for the classes identified in the current
	selected respons in an array with the uid as the key*/
   	$users=array();

	if($r>-1){
		$rbid=$respons[$r]['subject_id'];
		$rcrid=$respons[$r]['course_id'];
		$d_cids=mysql_query("SELECT DISTINCT id FROM class WHERE
		subject_id LIKE '$rbid' AND course_id LIKE '$rcrid' ORDER BY id");
		while($cid=mysql_fetch_row($d_cids)){
			$d_users=mysql_query("SELECT DISTINCT uid,
			   	username, passwd, forename, surname, email, nologin,
				firstbookpref, role FROM users JOIN tidcid ON 
				users.username=tidcid.teacher_id WHERE
				tidcid.class_id='$cid[0]' ORDER BY username");
		  while($user=mysql_fetch_array($d_users,MYSQL_ASSOC)){
			$uid=$user['uid'];
			if(!array_key_exists($uid,$users)){
				$users["$uid"]=$user;
				}
			}
		  }
		}
	return $users;
	}

function getPastoralStaff($ryid,$perms){
	/* will return all details of users of interest based on the current
	 * selected yeargroup in an array with the uid as the key
	 * a head of year would be 111, form tutors 110
	 */
   	$users=array();

	$r=$perms['r'];
	$w=$perms['w'];
	$x=$perms['x'];
	$d_group=mysql_query("SELECT gid FROM groups WHERE
			course_id='' AND subject_id='' 
			AND yeargroup_id LIKE '$ryid'");
	$gid=mysql_result($d_group,0);
	$d_users=mysql_query("SELECT DISTINCT users.uid,
			   	username, passwd, forename, surname, email, nologin,
				firstbookpref, role FROM users JOIN perms ON 
				users.uid=perms.uid WHERE perms.gid='$gid' AND perms.r='$r' 
				AND perms.w='$w' AND perms.x='$x'");
	while($user=mysql_fetch_array($d_users,MYSQL_ASSOC)){
		$uid=$user['uid'];
		$users["$uid"]=$user;
		}
	return $users;
	}

function getAllStaff($nologin='%'){
	/* will return all details of all users*/
   	$users=array();
	$d_users=mysql_query("SELECT uid, username, passwd, forename,
				surname, email, nologin, firstbookpref, role, worklevel
				FROM users WHERE nologin LIKE '$nologin' ORDER BY role, username");
	while($user=mysql_fetch_array($d_users,MYSQL_ASSOC)){;
		$uid=$user['uid'];
		$users["$uid"]=$user;
		}
	return $users;
	}

function getTeachingStaff($crid='',$bid=''){
	$users=array();
	if($crid!='' or $bid!=''){
		/*Get ids for the teachers of this subject and store in tids[]*/
		$d_teacher=mysql_query("SELECT DISTINCT teacher_id FROM tidcid JOIN
				class ON class.id=tidcid.class_id WHERE class.subject_id LIKE '$bid' 
				AND class.course_id LIKE '$crid' ORDER BY teacher_id");
		while($teacher=mysql_fetch_array($d_teacher,MYSQL_ASSOC)){
			$tid=$teacher['teacher_id'];
			$d_users=mysql_query("SELECT uid, username, passwd, forename,
				surname, email, nologin, firstbookpref, role, worklevel
				FROM users WHERE username='$tid'");
			$user=mysql_fetch_array($d_users,MYSQL_ASSOC);
			$users["$tid"]=$user;
			}
		}
	else{
		/*Otherwise just return all active teaching staff ie. nologin=0*/
		$d_user=mysql_query("SELECT uid, username, passwd, forename,
				surname, email, nologin, firstbookpref, role, worklevel FROM users WHERE
				(role='teacher' or role='admin') AND nologin='0' AND
				username!='administrator' ORDER BY username");
		while($user=mysql_fetch_array($d_user,MYSQL_ASSOC)){
			$tid=$user['username'];
			$users["$tid"]=$user;
			}
		}
	return $users;
	}

function getUid($tid){
	$d_users=mysql_query("SELECT uid FROM users WHERE username='$tid'");
	$uid=mysql_result($d_users,0);
	return $uid;
	}

function checkCurrentRespon($r,$respons,$required='subject'){
	if($r>-1){
		$bid=$respon[$r]['subject_id'];
		$crid=$respons[$r]['course_id'];
		if($bid==''){$bid='%';}
		}
	if($required=='subject' and $bid==''){
		$error[]=get_string('selectresponsibility');
		}
	elseif($required=='course' and ($crid=='' or $crid=='%')){
		$error[]=get_string('selectcourseresponsibility');
		}
	return array($crid,$bid,$error);
	}

function getYearPerm($year,$respons){
	/*return perm for yeargroup*/	
	$perm['r']=0;
	$perm['w']=0;
	$perm['x']=0;
	for($c=0;$c<sizeof($respons);$c++){
		$resp=$respons[$c];
		if($resp['name']!='Form'){
			if($resp['yeargroup_id']==$year){
				$perm['r']=$resp['r'];
				$perm['w']=$resp['w'];
				$perm['x']=$resp['x'];
				}
			}
		}
	if($_SESSION['role']=='admin'){$perm['r']=1; $perm['w']=1; $perm['x']=1;}		
	elseif($_SESSION['role']=='office'){$perm['r']=1; $perm['w']=1; $perm['x']=0;}		
	return $perm;
	}

function getFormPerm($fid,$respons){
	/*return perm for form group*/
	$perm['r']=0;
	$perm['w']=0;
	$perm['x']=0;
	$d_form=mysql_query("SELECT yeargroup_id FROM form WHERE id='$fid'");
	$formyid=mysql_result($d_form,0);
	for($c=0;$c<sizeof($respons);$c++){
		$resp=$respons[$c];
		if($resp['name']=='Form'){
			if($resp['form_id']==$fid){
				$perm['r']=$resp['r'];
				$perm['w']=$resp['w'];
				$perm['x']=$resp['x'];
				}
			}
		elseif($resp['yeargroup_id']!=''){
			if($resp['yeargroup_id']==$formyid){
				$perm['r']=$resp['r'];
				$perm['w']=$resp['w'];
				$perm['x']=$resp['x'];
				}
			}
		}
	if($_SESSION['role']=='admin'){$perm['r']=1; $perm['w']=1; $perm['x']=1;}		
	elseif($_SESSION['role']=='office'){$perm['r']=1; $perm['w']=1; $perm['x']=0;}
	return $perm;
	}

function getMarkPerm($mid, $respons){
	$d_class=mysql_query("SELECT subject_id, course_id FROM class
		 JOIN midcid ON class.id=midcid.class_id WHERE midcid.mark_id='$mid'");
	$class=mysql_fetch_array($d_class,MYSQL_ASSOC);
	/*this will only takes the first crid/bid, would be a prolem if
				the mark is defined  across more than one type of class*/
	$bid=$class['subject_id'];
	$crid=$class['course_id'];

	$perm['r']=0;
	$perm['w']=0;
	$perm['x']=0;
	for($c=0;$c<sizeof($respons);$c++){
		$resp=$respons[$c];
		if(($resp['subject_id']==$bid or $resp['subject_id']=='%') and
				($resp['course_id']==$crid or $resp['course_id']=='%')){
			$perm['r']=$resp{'r'};
			$perm['w']=$resp{'w'};
			$perm['x']=$resp{'x'};
			}
		}
	if($_SESSION['role']=='admin'){$perm['r']=1; $perm['w']=1; $perm['x']=1;}		
	return $perm;
	}

function getSubjectPerm ($subject, $respons){
	$perm['r']=0;
	$perm['w']=0;
	$perm['x']=0;
	for($c=0;$c<sizeof($respons);$c++){
		$resp=$respons[$c];
		if($resp['subject_id']==$subject and $resp['course_id']=='%'){
			$perm['r']=$resp['r'];
			$perm['w']=$resp['w'];
			$perm['x']=$resp['x'];
			}
		}
	return $perm;
	}

function getCoursePerm($course,$respons){
	$perm['r']=0;
	$perm['w']=0;
	$perm['x']=0;
	for($c=0;$c<sizeof($respons);$c++){
		$resp=$respons[$c];
		if($resp['subject_id']=='%' and $resp['course_id']==$course){
			$perm['r']=$resp['r'];
			$perm['w']=$resp['w'];
			$perm['x']=$resp['x'];
			}
		}
	if($_SESSION['role']=='admin'){$perm['r']=1; $perm['w']=1; $perm['x']=1;}		
	return $perm;
	}

function listPastoralRespon($respons){
	$rfids=array();
	$ryids=array();
	for($c=0;$c<sizeof($respons);$c++){
		$resp=$respons[$c];
		if($resp['name']=='Form'){
			if($resp['form_id']!=''){$rfids[]=$resp['form_id'];}
			}
		elseif($resp['yeargroup_id']!=''){
			/*academic respons must have null yeargroup_id for this to work!*/
			$ryids[]=$resp['yeargroup_id'];
			}
		}
	return array('forms'=>$rfids,'years'=>$ryids);
	}

function updateUser($user,$update='no',$short='class'){
	/* Optional $update='yes' will amend an existing record.*/
	$username=$user['username'];
	$surname=checkEntry($user['surname']);
	$forename=$user['forename'];
	$role=$user['role'];
	if(isset($user['worklevel'])){
		$worklevel=$user['worklevel'];
		}
	else{
		if($role=='office'){$worklevel='-1';}
		elseif($role=='admin'){$worklevel='3';}
		elseif($role=='teacher'){$worklevel='0';}
		elseif($role=='support'){$worklevel='-1';}
		else{$worklevel='0';}
		}
	if(isset($user['firstbookpref'])){
		$firstbookpref=$user['firstbookpref'];
		}
	else{
		if($role=='office'){$firstbookpref='infobook';}
		elseif($role=='admin'){$firstbookpref='admin';}
		elseif($role=='teacher'){$firstbookpref='markbook';}
		else{$firstbookpref='infobook';}
		}
    if(isset($user['email'])){
	   if(eregi('^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$', $user['email'])){ 
			$user['email']=strtolower($user['email']);
			$email=$user['email'];
	   		}
		}
	else{
		$email='';
		}
    if(isset($user['nologin'])){$nologin=$user['nologin'];}else{$nologin='0';}

 	/*All users get a passwd based on the shortkeyword (set in $CFG) and a
   	/*userno, this formula should be personalised to meet your needs*/
	/*The userno should be unique to each staff login, is not stored in*/
	/*the database and should be recorded elsewhere for reference if needed*/
	if($user['passwd']!=''){$passwd=$user['passwd'];}
	elseif(isset($user['userno'])){$passwd=$short.$user['userno'];}
	if(isset($passwd)){$assword=md5($passwd);}
	else{$assword='';}

	$d_user=mysql_query("SELECT username, surname, forename 
							FROM users WHERE username='$username'");
	if($olduser=mysql_fetch_array($d_user)){
		  if($update!='yes'){
				$result=$result.'Username '.$username.' already exists
							for a member of staff:
						'.$olduser{'surname'}.', '.$olduser{'forename'};
				}
		  else{
			  mysql_query("UPDATE users SET
				  surname='$surname', forename='$forename',
							email='$email', role='$role', worklevel='$worklevel',
							nologin='$nologin',
					firstbookpref='$firstbookpref' WHERE username='$username'");
			  $result=$result.'Updated details for user '.$username;
			}
		}
	else{
		mysql_query("INSERT INTO users (username, passwd, forename, surname,
					email, role, nologin, worklevel, firstbookpref) 
					VALUES ('$username', '$assword', '$forename',
					'$surname', '$email', '$role', '$nologin', '$worklevel',
							'$firstbookpref')");
		$result=$result.'Username '.$username.' added.';
		}
	if($assword!=''){
		  $d_user=mysql_query("UPDATE users SET
					passwd='$assword' WHERE username='$username'");
		   }
	return $result;
	}

function updateStaffPerms($uid,$gid,$newperms){
	$r=$newperms['r'];
	$w=$newperms['w'];
	$x=$newperms['x'];
	$e=$newperms['e'];

	if($r==0 and $w==0 and $x==0){
		mysql_query("DELETE FROM perms WHERE uid='$uid' AND gid='$gid' LIMIT 1");
		$result=get_string('removedresponsibility','admin');
		}
	else{
		if(mysql_query("INSERT perms (uid, gid, r, w, x, e) VALUES
				('$uid', '$gid', '$r', '$w', '$x', '$e')")){
				$result=get_string('assignednewresponsibilities','admin');
				}
		else{mysql_query("UPDATE perms SET r='$r', w='$w', x='$x',
				e='$e' WHERE uid='$uid' AND gid='$gid'"); 
				$result=get_string('updatedresponsibilities','admin');
				}
		}
	return $result;
	}
?>