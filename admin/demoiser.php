<?php
/**								demoiser.php
 *
 * Update the database tables to match with entries from the curriculum
 * files. It does not (as yet) remove any data fro mthe database even if 
 * it has been removed from the curriculum files.
 */

$host='admin.php';
$current='demoiser.php';
$choice='demoiser.php';

function tableRead($table,$sort='',$order='ASC'){
	$trows=array();
	if($sort!=''){$d_table=mysql_query("SELECT * FROM $table ORDER BY $sort $order;");}
	else{$d_table=mysql_query("SELECT * FROM $table;");}
   	while($row=mysql_fetch_array($d_table,MYSQL_ASSOC)){
		$trows[]=$row;
		}
	return $trows;
	}

function tableClear($table){
	$d_table=mysql_query("DELETE FROM $table");
	}


function generate_random_name($gender){
	if($gender=='M'){
		$start=array('John','Paul','David','James','Eric','Ryan','Christopher','Mark','Edward',
					 'Chris','Luke','Robert','Jack','Steve');
		$middle=array('John','Paul','David','James','Eric','Ryan','Christopher','Mark','Edward',
					  'Chris','Luke','Robert','Jack','Steve');
		}
	else{
		$start=array('Emma','Claire','Tracy','Jane','Ann','Fiona','Lara','Sophie','Rachel',
					 'Louise','Jessica','Pamela');
		$middle=array('Emma','Claire','Tracy','Jane','Ann','Fiona','Lara','Sophie','Rachel',
					  'Louise','Jessica','Pamela');
		}
	$end=array('Smith','Lee','Colback','Nunn','Gardner','Stewart','Mignolet',
			   'White','Kirkpatrick','Stokoe','Owen','Davidson','Rowell','Phillips',
			   'McClean','Robson','Ball','Quinn','Davis','Johnson','Hope','Blair',
			   'Fawcett','Lawrence','Whitehead','Robinson','Wylie','McCartney','Collins',
			   'Westwood','Anderson','Carter','Mitchell','Main','Porterfield','Royal','Welsh','Roy',
			   'Robertson','Riley','Newman','Turner','Hardy','Dene','Poll','Wright',
			   'Montgomery','Anderson','Clough','Fletcher','Reid','Murray','Hurley','Ashurst');
	$name=array();
    srand((double)microtime()*1000000);
	$name[]=$start[(rand() %  count($start))];
	$name[]=$middle[(rand() % count($middle))];
	$name[]=$end[(rand() %    count($end))];
    return($name);
	}


	$table='address';
	$trows=array();
	$trows=tableRead($table);
	foreach($trows as $row){
		$id=$row['id'];
		mysql_query("UPDATE $table SET 
		street='36 Longstreet', neighbourhood='Housing
		estate', region='Small town', postcode='SG4 9PQ', country='England'
					 WHERE id='$id'");
		}

	$table='background';
	$trows=array();
	$trows=tableRead($table);
	while(list($index, $row)=each($trows)){
		$id=$row['id'];
		mysql_query("UPDATE $table SET detail='A specific piece of
			background information.', entrydate='2000-01-01', teacher_id='' WHERE id='$id'");
		}

	$table='comments';
	$trows=array();
	$trows=tableRead($table);
	while(list($index, $row)=each($trows)){
		$id=$row['id'];
		mysql_query("UPDATE $table SET detail='A general comment
			about positive or negative progress.', teacher_id='' WHERE id='$id'");
		}

	$table='exclusions';
	$trows=array();
	$trows=tableRead($table);
	while(list($index, $row)=each($trows)){
		$id=$row['student_id'];
		mysql_query("UPDATE $table SET reason='The reason for the exclusion.',
	   		startdate='2000-01-01', enddate='2000-01-02' WHERE student_id='$id'");
		}

	$table='history';
	$trows=tableClear($table);

	$table='incidenthistory';
	$trows=tableClear($table);

	mysql_query("UPDATE attendance SET comment='', teacher_id='';");
	mysql_query("UPDATE attendance_booking SET comment='';");

	$table='incidents';
	$trows=array();
	$trows=tableRead($table);
	while(list($index, $row)=each($trows)){
		$id=$row['id'];
		mysql_query("UPDATE $table SET detail='The nature of the incident.',
					entrydate='2007-09-21' WHERE id='$id'");
		}

	$table='info';
	$trows=array();
	$trows=tableRead($table);
	while(list($index,$row)=each($trows)){
		$id=$row['student_id'];
		mysql_query("UPDATE $table SET formerupn='20987', otherpn1='', otherpn2='',
			ethnicity='', email='', phonenumber='', countryoforigin='', birthplace='',
			language='EN', religion='', incare='N', appnotes='', appdate='',
			staffchild='N', entrydate='2001-04-01', leavingdate='', nationality='GB', 
			appcategory='', secondnationality='', medical='N', epfusername='', 
			appnotes='', appdate='', siblings='N', incare='N' WHERE student_id='$id'");
		}

	$table='phone';
	$trows=array();
	$trows=tableRead($table);
	while (list($index, $row) = each($trows)) {
		$id=$row['id'];
		mysql_query("UPDATE $table SET number='1907893333'
				WHERE id='$id'");
		}

	$table='sencurriculum';
	$trows=array();
	mysql_query("UPDATE $table SET comments='The background.',
					targets='To improve.', outcome='The result.', extra='';");

	$table='student';
	$trows=array();
	$trows=tableRead($table);
	while(list($index, $row)=each($trows)){
		$id=$row['id'];
		$name=generate_random_name($row['gender']);
		mysql_query("UPDATE $table SET surname='$name[2]',
				forename='$name[0]', middlenames='$name[1]', preferredforename='', formersurname='', dob='1998-04-01'
				WHERE id='$id'");
		}


	$table='guardian';
	$trows=array();
	$trows=tableRead($table);
	while (list($index, $row) = each($trows)) {
		$id=$row['id'];
		$d_gidsid=mysql_query("SELECT relationship FROM gidsid WHERE guardian_id='$id'");
		$rel=mysql_result($d_gidsid,0);
		if($rel!=''){
			$d_sid=mysql_query("SELECT surname FROM student JOIN gidsid
					ON gidsid.student_id=student.id WHERE gidsid.guardian_id='$id';");
			$surname=mysql_result($d_sid,0);
			if($rel=='PAF'){$gender='M';$title='1';}else{$gender='F';$title='2';}
			$name=generate_random_name($gender);
			mysql_query("UPDATE $table SET surname='$surname',
				forename='$name[0]', middlenames='$name[1]', title='$title',
				profession='', email='', companyname='', nationality='', language='',
				dob='', epfusername='', note='', code='' WHERE id='$id'");
			}
		else{
			$d_guardian=mysql_query("DELETE FROM guardian WHERE id = '$id';");
			$d_address=mysql_query("DELETE FROM address WHERE guardian_id = '$id';");
			}
		}


	$table='users';
	$trows=tableRead($table,'logcount','DESC');
	$profindex=1;
	$officeindex=1;
	$adminindex=1;
	$senindex=1;
	$medicalindex=1;
	$libraryindex=1;
	foreach($trows as $row){
		$id=$row['uid'];
		$username=$row['username'];
		$role=$row['role'];
		if($username!='administrator'){$passwd=md5('guest');}
		if($role=='teacher'){
			$nun='Prof'.$profindex++;
			}
		elseif($role=='admin'){
			if($username!='administrator'){$nun='admin'.$adminindex++;}
			else{$nun=$username;}
			}
		elseif($role=='office'){
			$nun='office'.$officeindex++;
			}
		elseif($role=='sen'){
			$nun='sen'.$senindex++;
			}
		elseif($role=='library'){
			$nun='library'.$libraryindex++;
			}
		elseif($role=='medical'){
			$nun='medical'.$medicalindex++;
			}
		
		$zsurname = $nun;

		if($username!='administrator' or ($username=='administrator' and isset($passwd) and $passwd!='')){
			mysql_query("UPDATE $table SET username='$nun',
				forename='P', surname='$zsurname', email='', emailuser='', emailpasswd='', nologin='0', session='',
				logcount='0', passwd='$passwd', ip='', epfusername='$nun', homephone='', mobilephone='', personalcode='', 
				dob='', contractdate='' WHERE uid='$id';");
			}
		mysql_query("UPDATE orderaction SET teacher_id='$nun' WHERE teacher_id='$username'");
		mysql_query("UPDATE orderorder SET teacher_id='$nun' WHERE teacher_id='$username'");
		mysql_query("UPDATE tidcid SET teacher_id='$nun'
			WHERE teacher_id='$username'");
		mysql_query("UPDATE reportentry SET teacher_id='$nun'
			WHERE teacher_id='$username'");
		mysql_query("UPDATE comments SET teacher_id='$nun'
			WHERE teacher_id='$username'");
		mysql_query("UPDATE incidents SET teacher_id='$nun'
			WHERE teacher_id='$username'");
		mysql_query("UPDATE merits SET teacher_id='$nun'
			WHERE teacher_id='$username'");
		mysql_query("UPDATE grading SET author='$nun'
			WHERE author='$username'");
		mysql_query("UPDATE markdef SET author='$nun'
			WHERE author='$username'");
		mysql_query("UPDATE homework SET author='$nun'
			WHERE author='$username'");
		mysql_query("UPDATE mark SET author='$nun'
			WHERE author='$username'");
		}

	$table='orderorder';
	mysql_query("UPDATE $table SET entrydate='2008-01-01', detail='';");

	$table='orderaction';
	mysql_query("UPDATE $table SET detail='';");

	$table='ordermaterial';
	mysql_query("UPDATE $table SET unitcost='1.23', detail='some
	stuff', refno='st';");

	$table='orderbudget';
	mysql_query("UPDATE $table SET costlimit='600', name='Another Budget';");

	$table='orderinvoice';
	mysql_query("UPDATE $table SET reference='aref1', deliverycost='0', taxcost='0', discountcost='0', totalcost='0', debitcost='0';");

	$table='transport_stop';
	mysql_query("UPDATE $table SET name=id, detail='Bus Stop', lat='0', lng='0';");

	$table='transport_route';
	mysql_query("UPDATE $table SET detail=name;");
	
	$table='transport_booking';
	mysql_query("UPDATE $table SET comment='';");


	$table='reportentry';
	mysql_query("UPDATE $table SET comment='<p>A constructive comment from a subject teacher which is for reporting to parents. A constructive comment from a subject teacher which is for reporting to parents. A constructive comment from a subject teacher which is for reporting to parents. A constructive comment from a subject teacher which is for reporting to parents. A constructive comment from a subject teacher which is for reporting to parents. A constructive comment from a subject teacher which is for reporting to parents. A constructive comment from a subject teacher which is for reporting to parents.</p>'");

	$table='score';
	mysql_query("UPDATE $table SET comment='';");

	$forms=list_formgroups();
	$name=array('AA','BB','CC','DD','EE','FF','GG','HH','JJ','KK','LL','MM');
	$i=2;
	$yid=-100;
	foreach($forms as $row){
		if($yid!=$row['yeargroup_id']){$i=2;}
		else{$i++;}
		$yid=$row['yeargroup_id'];
		$id=$row['name'];
		if($yid=='-2'){$nid='PRE'.$name[$i];}
		elseif($yid=='-1'){$nid='NUR'.$name[$i];}
		elseif($yid=='0'){$nid='REC'.$name[$i];}
		else{$nid=$yid.''.$name[$i];}
		mysql_query("UPDATE student SET form_id='$nid' WHERE form_id='$id';");
		mysql_query("UPDATE community SET name='$nid' WHERE name='$id' AND type='form';");
		}

	$table='classes';
	$trows=tableRead($table);
	$name=array('AA','BB','CC','DD','EE','FF','GG','HH','JJ','KK','LL','MM');
	foreach($trows as $row){
		if($row['generate']=='forms'){
			$bid=$row['subject_id'];
			$crid=$row['course_id'];
			$stage=$row['stage'];
			$curryear=get_curriculumyear($crid);
			$d_class=mysql_query("SELECT class.id, class.name FROM class JOIN cohort ON class.cohort_id=cohort.id 
						WHERE cohort.stage='$stage' AND cohort.course_id='$crid' 
						AND cohort.year='$curryear' AND class.subject_id='$bid';");
			$i=0;
			while($row=mysql_fetch_array($d_class,MYSQL_ASSOC)){
				$cid=$row['id'];
				$newcid=$bid . $stage . $name[$i];
				$i++;
				$d_c=mysql_query("UPDATE class SET name='$newcid' WHERE id='$cid'");
				/*
				$d_c=mysql_query("SELECT id FROM class WHERE name='$newcid' AND cohort_id='';");
				if(mysql_num_rows($d_c)>0){
					$newcid=$bid . $stage . $name[$i] .$i;
					$d_c=mysql_query("UPDATE class SET name='$newcid' WHERE id='$cid'");
					}
				else{
					$d_c=mysql_query("UPDATE class SET name='$newcid' WHERE id='$cid'");
					}
				*/
				}
			}
		}

	$table='fees_account';
	$trows=tableRead($table);
	$newtest='classis1234';
	$newaccess='1234';
	if(count($trows)>0){
		foreach($trows as $row){
			$aid=$row['id'];
			$gid=$row['guardian_id'];
			if($aid==1 and $gid==0){
				mysql_query("UPDATE fees_account SET bankname=AES_ENCRYPT('$newtest','$newaccess') WHERE id='1';");
				}
			elseif($aid>1 and $gid==0){
				mysql_query("UPDATE fees_account SET 
								accountname=AES_ENCRYPT('Classis','$newaccess'),
								bankname=AES_ENCRYPT('Bankname','$newaccess'),
								banknumber=AES_ENCRYPT('0012002356','$newaccess'), 
								bankcode=AES_ENCRYPT('2310','$newaccess'), 
								bankbranch=AES_ENCRYPT('0001','$newaccess'), 
								bankcontrol=AES_ENCRYPT('18','$newaccess'),
								bankcountry=AES_ENCRYPT('ES','$newaccess'), 
								iban=AES_ENCRYPT('ES8023100001180012002356','$newaccess')
						WHERE id='$aid';");
				}
			else{
				$guardian=fetchContact($gid);
				$guardian_name='';
				mysql_query("UPDATE fees_account SET 
								accountname=AES_ENCRYPT('$guardian_name','$newaccess'),
								bankname=AES_ENCRYPT('Bankname','$newaccess'),
								banknumber=AES_ENCRYPT('0012002356','$newaccess'), 
								bankcode=AES_ENCRYPT('2310','$newaccess'), 
								bankbranch=AES_ENCRYPT('0001','$newaccess'), 
								bankcontrol=AES_ENCRYPT('18','$newaccess'),
								bankcountry=AES_ENCRYPT('ES','$newaccess'), 
								iban=AES_ENCRYPT('ES8023100001180012002356','$newaccess')
						WHERE id='$aid';");
				}
			}
		$result[]="Test password: $newtest, access password: $newaccess.";

		$table='fees_concept';
		$trows=tableRead($table);
		$concepts=array('Subject','Stationery','Transport','Refectory','Enrolment');
		foreach($trows as $row){
			$id=$row['id'];
			srand((double)microtime()*1000000);
			$concept=$concepts[(rand() %  count($concepts))];
			mysql_query("UPDATE $table SET name='$concept' WHERE id='$id';");
			}

		$table='fees_remittance';
		$trows=tableRead($table);
		$no=array();
		foreach($trows as $row){
			$id=$row['id'];
			$date=explode("-",$row['duedate']);
			$rem=$date.$row['yeargroups'];
			if(!isset($no[$rem])){$no[$rem]=0;}
			$name="Y".$row['yeargroups']." ".$date['0'].".".$date[1].": DEMO ".$no[$rem];
			$no[$rem]++;
			mysql_query("UPDATE $table SET name='$name' WHERE id='$id';");
			}

		$table='fees_tarif';
		$trows=tableRead($table);
		$no=array();
		$tariffs=array('Subject'=>'120.00','Stationery'=>'210.00','Transport'=>'90.00','Refectory'=>'50.00','Enrolment'=>'500.00');
		foreach($trows as $row){
			$id=$row['id'];
			$fid=$row['concept_id'];
			$d_concept=mysql_query("SELECT name FROM fees_concept WHERE id=$fid;");
			$cname=mysql_result($d_concept,0,'name');
			if(!isset($no[$fid])){$no[$fid]=0;}
			$name=$cname." ".$no[$fid];
			$amount=$tariffs[$cname]+$no[$fid];
			mysql_query("UPDATE $table SET name='$name',amount='$amount' WHERE id='$id';");
			$no[$fid]++;
			}

		$table='fees_charge';
		$trows=tableRead($table);
		foreach($trows as $row){
			$id=$row['id'];
			$tarifid=$row['tarif_id'];
			$d_tarifs=mysql_query("SELECT amount FROM fees_tarif WHERE id=$tarifid;");
			$amount=mysql_result($d_tarifs,0,'amount');
			mysql_query("UPDATE $table SET amount='$amount' WHERE id='$id';");
			}
		}

$d_class=mysql_query("TRUNCATE TABLE message_event;");
$d_class=mysql_query("TRUNCATE TABLE message_event_seq;");
$d_class=mysql_query("TRUNCATE TABLE message_text_event;");
$d_class=mysql_query("TRUNCATE TABLE report_event;");

$result[]='You\'ve been demoised!';
include('scripts/results.php');
?>
