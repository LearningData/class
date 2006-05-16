<?php	
/*												
											fetch_assessment.php

	Retrieves all assessment information about one student using only their sid.
	Returns the data in an array $Assessments.
*/

function sigfigs($number,$sigfigs,$dec='.',$noround=false) {
    if(!$noround){
        $sigfigs++;
    }
    for ($sfi=0;$sfi<=strlen($number) && $sfdone<$sigfigs;$sfi++) {
        $temp=substr($number,$sfi,1);
        if ($temp != "0" && $temp != $dec) {
            $after1stsf = true;
        }

        if ((($temp != "0") && ($temp != $dec)) || ($after1stsf && ($temp != $dec))){
            $sfdone++;
        }
        if ($temp == "."){
            $temp = $dec;
        }
        $output .= $temp;
    }
    if (substr($output,0,1) == $dec) {
        $output = "0".$output;
    }
    if (!$noround) {
        $splitbydp = explode($dec,$output);
        $numdps = strlen($splitbydp[1]);
        $output = round($output, ($numdps-1));
    }
    return $output;
}

function scoreToGrade($score,$grading_grades){
	/*
	Looks up the grade equivalent of the numerical score.
	If $score is empty then an empty $grade string is returned.	
	The numerical equivalents for the grades (levels in the grading
	scheme) must have integer values.
	*/
	if($score!=''){
		$pairs=explode(';', $grading_grades);
	    $score=round($score);
		$high=sizeof($pairs);
		for($c=0; $c<sizeof($pairs); $c++){
			list($levelgrade, $level)=split(':',$pairs[$c]);
			if($score>=$level){
				$lowgrade=$levelgrade;
				$lowlevel=$level;
				$high=$c+1;
				}
			}
		$grade=$lowgrade;
		if($high<$c){
			list($highgrade, $highlevel)=split(':',$pairs[$high]);
   			if(($highlevel-$score)<=($score-$lowlevel)){$grade=$highgrade;}
			}
		}
	else{$grade='';}
	return $grade;
	}


function gradeToScore($grade,$grading_grades){
	/*
	Looks up the numerical equivalent of a grade. 
	If the grade is an empty string then empty score is returned. 
	*/
    $score='';
	$pairs=explode (';', $grading_grades);
	if($grade!=''){
		for($c=0; $c<sizeof($pairs); $c++){
			list($levelgrade, $level)=split(':',$pairs[$c]);
			if($grade==$levelgrade){$score=$level;}	
			}
		}
	return $score;
	}

/*
function fetchResult($score){
	$mid=$score['mark_id'];
	$d_markdef=mysql_query("SELECT * FROM markdef JOIN mark ON
		mark.def_name=markdef.name WHERE mark.id='$mid'");
	$markdef=mysql_fetch_array($d_markdef,MYSQL_ASSOC);
	$scoretype=$markdef['scoretype'];
	if($scoretype=='grade'){
		$gradingname=$markdef['grading_name'];
		$d_grading=mysql_query("SELECT grades FROM grading WHERE name='$gradingname'");
		$grading_grades=mysql_result($d_grading,0);
		$out=scoreToGrade($score['grade'],$grading_grades);
		}
   	elseif($scoretype=='value'){
		$out=$score['value'];
		}
	elseif($scoretype=='percentage'){
		$score_value=$score{'value'};
		$score_total=$score{'outoftotal'};
		$marktotal=$mark_total[$c];
		if(!isset($score_total)){$score_total=$marktotal;}
		include('markbook/percent_score.php');
		if(isset($percent)){
			$out=$percent.' ('.number_format($score_value,1,'.','').')';
			}
		else{$out='';}      
		}
	elseif($scoretype=='comment'){
		$out=$score{'comment'};
		}
	elseif($scoretype=='tier'){
		$out=$score{'tier'};
		}
	return $out;
   	}
*/

function fetchAssessmentDefinition($eid){
   	$AssDef=array();
  	$AssDef['id_db']=$eid;
   	$d_ass=mysql_query("SELECT * FROM assessment WHERE id='$eid'");
	if(mysql_numrows($d_ass)==0){$AssDef['exists']='false';}
	else{$AssDef['exists']='true';}
	$ass=mysql_fetch_array($d_ass,MYSQL_ASSOC);
	$ass=nullCorrect($ass);

	$d_mid=mysql_query("SELECT mark_id FROM eidmid WHERE assessment_id='$eid'");
	$markcount=mysql_numrows($d_mid);
	$d_score=mysql_query("SELECT student_id FROM score
		JOIN eidmid ON eidmid.mark_id=score.mark_id WHERE eidmid.assessment_id='$eid'");
	$scorecount=mysql_numrows($d_score);
	$d_eidsid=mysql_query("SELECT student_id FROM eidsid
									WHERE assessment_id='$eid'");
	$archivecount=mysql_numrows($d_eidsid);

   	$AssDef['Subject']=array('label' => 'Subject','table_db' =>
					'assessment', 'field_db' => 'subject_id',
					'type_db'=>'varchar(10)', 'value' => $ass['subject_id']);
   	$AssDef['Component']=array('label' => 'Component','table_db' =>
					'asssessment', 'field_db' => 'component_id',
					'type_db'=>'varchar(10)', 'value' => $ass['component_id']);
   	$AssDef['Stage']=array('label' => 'Stage','table_db' => 'assessment', 'field_db' => 'stage',
					'type_db'=>'char(3)', 'value' => $ass['stage']);
   	$AssDef['Method']=array('label' => 'Method','table_db' =>
					'assessment', 'field_db' => 'method',
					'type_db'=>'char(3)', 'value' => $ass['method']);
   	$AssDef['Element']=array('label' => 'Element','table_db' =>
					'assessment', 'field_db' => 'element',
					'type_db'=>'char(3)', 'value' => $ass['element']);
   	$AssDef['Description']=array('label' => 'Description','table_db'
					=> 'assessment', 'field_db' => 'description',
					'type_db'=>'varchar(60)', 'value' => $ass['description']);
   	$AssDef['PrintLabel']=array('label' => 'Print Label','table_db' =>
					'assessment', 'field_db' => 'label',
					'type_db'=>'label', 'value' => $ass['label']);
   	$AssDef['ResultQualifier']=array('label' => 
					'Result Qualifier','table_db' => 'assessment', 
					'field_db' => 'resultqualifier',
					'type_db'=>'char(2)', 'value' => $ass['resultqualifier']);
   	$AssDef['ResultStatus']=array('label' => 'Result Status','table_db' => 'assessment', 
					'field_db' => 'resultstatus',
					'type_db'=>'enum', 'value' => $ass['resultstatus']);
   	$AssDef['OutOfTotal']=array('label' => 'Out of Total','table_db'
					=> 'assessment', 'field_db' => 'outoftotal',
					'type_db'=>'smallint', 'value' => $ass['outoftotal']);
   	$AssDef['Derivation']=array('label' => 'Derivation','table_db' => 
					'assessment', 'field_db' => 'derivation',
					'type_db'=>'varchar(60)', 'value' => $ass['derivation']);
   	$AssDef['Course']=array('label' => 'Course','table_db' =>
					'assessment', 'field_db' => 'course_id',
					'type_db'=>'varchar(10)', 'value' => $ass['course_id']);
   	$AssDef['ComponentStatus']=array('label' => 'Component Status', 
					'table_db' => 'assessment', 'field_db' => 'component_status',
					'type_db'=>'enum', 'value' => $ass['component_status']);
   	$AssDef['Year']=array('label' => 'Year', 'table_db' => 'assessment', 'field_db' => 'year',
					'type_db'=>'year', 'value' => $ass['year']);
   	$AssDef['Season']=array('label' => 'Season', 'table_db' =>
					'assessment', 'field_db' => 'season',
					'type_db'=>'enum', 'value' => $ass['season']);
   	$AssDef['MarkCount']=array('label' => 'Mark Columns', 'table_db' =>
					'', 'field_db' => '',
					'type_db'=>'', 'value' => $markcount);
   	$AssDef['ScoreCount']=array('label' => 'Mark Scores', 'table_db' =>
					'', 'field_db' => '',
					'type_db'=>'', 'value' => $scorecount);
   	$AssDef['ArchiveCount']=array('label' => 'Archive Scores', 'table_db' =>
					'', 'field_db' => '',
					'type_db'=>'', 'value' => $archivecount);
	return $AssDef;
   	}

function fetchAssessments($sid){
	$Assessments=array();

/*
	Assessments is an xml compliant array designed for use with Serialize
	to generate xml from the values in the database. Each value from
	the database is stored in an array element identified by its
	xmltag. Various useful accompanying attributes are also stored. Of
	particular use are Label for displaying the value and _db
	attributes facilitating updates to the database when values are
	changed (the type_db for instance facilitates validation).

	$Assessment['xmltag']=array('label' => 'Display label','table_db' => '', 'field_db' =>
				'ClaSSdb field name', 'type_db'=>'ClaSSdb data-type',
				'value' => from database);

	The table from which the values are pulled are generally
	identifiable by the array in which they are stored (eg. address,
	student etc.) but table_db is avaiable if needed.


   	$Assessment['']=array('label' => '','table_db' => '', 'field_db' => '',
					'type_db'=>'', 'value' => $['']);
*/



/*	Get all those assessment results that are still current scores
		in the MarkBook's tables
	$d_score = mysql_query("SELECT * FROM score JOIN mark ON
		mark.id=score.mark_id WHERE mark.assessment='yes' AND 
		score.student_id='$sid' ORDER BY mark.entrydate");

  	while($score=mysql_fetch_array($d_score,MYSQL_ASSOC)){
		$mid=$score['mark_id'];
		$d_mark = mysql_query("SELECT * FROM mark WHERE id='$mid'");
		$mark = mysql_fetch_array($d_mark,MYSQL_ASSOC);
		$mark = nullCorrect($mark);

		$d_ass = mysql_query("SELECT * FROM assessment JOIN eidmid ON
			eidmid.assessment_id=assessment.id WHERE eidmid.mark_id='$mid'");
		$ass = mysql_fetch_array($d_ass,MYSQL_ASSOC);
		$ass = nullCorrect($ass);

		$eid=$ass{'id'};
		$d_eidmid = mysql_query("SELECT * FROM eidmid WHERE
				mark_id='$mid' AND assessment_id='$eid'");
		$eidmid = mysql_fetch_array($d_eidmid,MYSQL_ASSOC);
		$eidmid = nullCorrect($eidmid);

		$mid=$eidmid{'mark_id'};

		if($ass['subject_id']=='%'){
				$d_bid = mysql_query("SELECT DISTINCT subject_id FROM class JOIN midcid ON
					midcid.class_id=class.id WHERE midcid.mark_id='$mid'");
				$bid=mysql_result($d_bid,0);
				}
		else{$bid=$ass['subject_id'];}

		$Assessment['id_db']=$eid;
	   	$Assessment['Stage']=array('label' => 'Stage','table_db' =>
					'assessment', 'field_db' => 'stage',
					'type_db'=>'char(3)', 'value' => $ass['stage']);
	   	$Assessment['Course']=array('label' => 'Course','table_db' =>
					'assessment', 'field_db' => 'course_id',
					'type_db'=>'varchar(10)', 'value' => $ass['course_id']);
	   	$Assessment['Subject']=array('label' => 'Subject','table_db'
					=> 'assessment', 'field_db' => 'subject_id',
					'type_db'=>'varchar(10)', 'value' => $bid);
	   	$Assessment['SubjectComponent']=array('label' => 'Subject Component','table_db'
					=> 'mark', 'field_db' => 'component_id',
					'type_db'=>'varchar(10)', 'value' => $mark['component_id']);
	   	$Assessment['Method']=array('label' => 'Method','table_db' =>
					'assessment', 'field_db' => 'method',
					'type_db'=>'char(3)', 'value' => $ass['method']);
	   	$Assessment['Element']=array('label' =>
					'Element','table_db' => 'assessment', 'field_db' => 'element',
					'type_db'=>'char(3)', 'value' => $ass['element']);
	   	$Assessment['Description']=array('label' =>
					'Description','table_db' => 'assessment', 'field_db' => 'description',
					'type_db'=>'varchar(60)', 'value' => $ass['description']);
	   	$Assessment['Year']=array('label' =>
					'Year', 'type_db' => 'year', 'field_db' => 'year', 'value' => $ass['year']);
	   	$Assessment['PrintLabel']=array('label' =>
					'PrintLabel','table_db' => 'assessment', 'field_db' => 'label',
					'type_db'=>'varchar(10)', 'value' => $ass['label']);
	   	$Assessment['ResultQualifier']=array('label' =>
					'Qualifier','table_db' => 'assessment', 'field_db' => 'resultqualifier',
					'type_db'=>'char(2)', 'value' => $ass['resultqualifier']);
	   	$Assessment['OutOfTotal']=array('label' => 'Total','table_db'
					=> 'assessment', 'field_db' => 'outoftotal',
					'type_db'=>'smallint(5)', 'value' => $ass['outoftotal']);
	   	$Assessment['Derivation']=array('label' =>
					'Derivation','table_db' => 'assessment', 
					'field_db' => 'derivation', 'type_db'=>'varchar(60)', 
						'value' => $ass['derivation']);
	   	$Assessment['Season']=array('label' => 'Season','table_db' => 
					'assessment', 'field_db' => 'season',
					'type_db'=>'enum', 'value' => $ass['season']);
	   	$Assessment['Date']=array('label' => 'Date','table_db' =>
					'date', 'field_db' => 'eidmid',
					'type_db'=>'date', 'value' => $eidmid['date']);
	   	$Assessment['Year']=array('label' => 'Year','table_db' =>
					'assessment', 'field_db' => 'year',
					'type_db'=>'year', 'value' => $ass['year']);
		if($eidmid['resultstatus']!=''){
		   	$Assessment['ResultStatus']=array('label' => 'Status','table_db'
					=> 'eidmid', 'field_db' => 'resultstatus',
					'type_db'=>'enum', 'value' => $eidmid['resultstatus']);
	   		}
		else{
		   	$Assessment['ResultStatus']=array('label' => 'Status','table_db'
					=> 'assessment', 'field_db' => 'resultstatus',
					'type_db'=>'enum', 'value' => $ass['resultstatus']);
			}
	   	$Assessment['ExamBoard']=array('label' => 'Board','table_db'
					=> 'eidmid', 'field_db' => 'examboard',
					'type_db'=>'char(3)', 'value' => $eidmid['examboard']);
	   	$Assessment['ExamBoardSyllabusID']=array('label' =>
					'Syllabus','table_db' => 'eidmid', 
					'field_db' => 'examsyallabus',
					'type_db'=>'char(3)', 'value' => $eidmid['examsyllabus']);

		if($eidmid['result']==' '){
				$result=fetchResult($score);
				}

		else{$result=$eidmid['result'];}

	   	$Assessment['Result']=array('label' => 'Result','table_db'
					=> 'eidmid', 'field_db' => 'result', 
					'type_db'=>'', 'value' => $result);

		$Assessment['Result']=nullCorrect($Assessment['Result']);
		$Assessment=nullCorrect($Assessment);
		$Assessments[]=$Assessment;
		}
*/

/*	Get all those assessment results that are archived*/
/*under development*/
   	$d_eidsid=mysql_query("SELECT * FROM eidsid WHERE
				student_id='$sid'");
  	while($eidsid=mysql_fetch_array($d_eidsid,MYSQL_ASSOC)) {
		$eidsid=nullCorrect($eidsid);
		$eid=$eidsid{'assessment_id'};
		$d_ass = mysql_query("SELECT * FROM assessment WHERE id='$eid'");
		$ass = mysql_fetch_array($d_ass,MYSQL_ASSOC);
		$ass = nullCorrect($ass);

		$Assessment['id_db']=$ass{'id'};
	   	$Assessment['Stage']=array('label' => 'Stage','table_db' =>
					'assessment', 'field_db' => 'stage',
					'type_db'=>'char(3)', 'value' => $ass['stage']);
	   	$Assessment['Course']=array('label' => 'Course','table_db' =>
					'assessment', 'field_db' => 'course_id',
					'type_db'=>'varchar(10)', 'value' => $ass['course_id']);
		if($eidsid['subject_id']=='%'){$subject='';}
				else{$subject=$eidsid['subject_id'];}
	   	$Assessment['Subject']=array('label' => 'Subject','table_db'
					=> 'assessment', 'field_db' => 'subject_id',
					'type_db'=>'varchar(10)', 'value' => $subject);
		if($eidsid['component_id']=='%'){$component='';}
				else{$component=$eidsid['component_id'];}
	   	$Assessment['SubjectComponent']=array('label' => 'Subject Component','table_db'
					=> 'mark', 'field_db' => 'component_id',
					'type_db'=>'varchar(10)', 'value' => $component);
	   	$Assessment['Method']=array('label' => 'Method','table_db' =>
					'assessment', 'field_db' => 'method',
					'type_db'=>'char(3)', 'value' => $ass['method']);
	   	$Assessment['Element']=array('label' =>
					'Element','table_db' => 'assessment', 'field_db' => 'element',
					'type_db'=>'char(3)', 'value' => $ass['element']);
	   	$Assessment['Description']=array('label' =>
					'Description','table_db' => 'assessment', 'field_db' => 'description',
					'type_db'=>'varchar(60)', 'value' => $ass['description']);
	   	$Assessment['Year']=array('label' =>
					'Year', 'type_db' => 'year', 'field_db' => 'year', 'value' => $ass['year']);
	   	$Assessment['PrintLabel']=array('label' =>
					'PrintLabel','table_db' => 'assessment', 'field_db' => 'label',
					'type_db'=>'varchar(10)', 'value' => $ass['label']);
	   	$Assessment['ResultQualifier']=array('label' =>
					'Qualifier','table_db' => 'assessment', 'field_db' => 'resultqualifier',
					'type_db'=>'char(2)', 'value' => $ass['resultqualifier']);
	   	$Assessment['OutOfTotal']=array('label' => 'Total','table_db'
					=> 'assessment', 'field_db' => 'outoftotal',
					'type_db'=>'smallint(5)', 'value' => $ass['outoftotal']);
	   	$Assessment['Derivation']=array('label' =>
					'Derivation','table_db' => 'assessment', 
					'field_db' => 'derivation', 'type_db'=>'varchar(60)', 
					'value' => $ass['derivation']);
	   	$Assessment['Season']=array('label' => 'Season','table_db' =>
					'assessment', 'field_db' => 'season',
					'type_db'=>'enum', 'value' => $ass['season']);
	   	$Assessment['Date']=array('label' => 'Date','table_db' =>
					'date', 'field_db' => 'eidmid',
					'type_db'=>'date', 'value' => $eidsid['date']);
	   	$Assessment['Year']=array('label' => 'Year', 'table_db' =>
					'assessment', 'field_db' => 'year',
					'type_db'=>'year', 'value' => $ass['year']);
		if($eidsid['resultstatus']!=''){
		   	$Assessment['ResultStatus']=array('label' => 'Status','table_db'
					=> 'eidsid', 'field_db' => 'resultstatus',
					'type_db'=>'enum', 'value' => $eidsid['resultstatus']);
	   		}
		else{
		   	$Assessment['ResultStatus']=array('label' => 'Status','table_db'
					=> 'assessment', 'field_db' => 'resultstatus',
					'type_db'=>'enum', 'value' => $ass['resultstatus']);
			}
	   	$Assessment['ExamBoard']=array('label' => 'Board','table_db'
					=> 'eidmid', 'field_db' => 'examboard',
					'type_db'=>'char(3)', 'value' => $eidsid['examboard']);
	   	$Assessment['ExamBoardSyllabusID']=array('label' =>
					'Syllabus','table_db' => 'eidmid', 
					'field_db' => 'examsyallabus',
					'type_db'=>'char(3)', 'value' => $eidsid['examsyllabus']);
	   	$Assessment['Result']=array('label' => 'Result','table_db'
					=> 'eidsid', 'field_db' => 'result', 
					'type_db'=>'', 'value' => $eidsid['result']);
		$Assessment=nullCorrect($Assessment);
		$Assessments[]=$Assessment;
		}
	
	return $Assessments;
}	


function fetchshortAssessments($sid){

	$Assessments=array();

/*		Get all those assessment results that are still current scores
		in the MarkBook's tables

	$d_score = mysql_query("SELECT * FROM score JOIN mark ON
		mark.id=score.mark_id WHERE mark.assessment='yes' AND 
		score.student_id='$sid' ORDER BY mark.entrydate");
  	while($score = mysql_fetch_array($d_score,MYSQL_ASSOC)) {
    	$score=nullCorrect($score);
		$mid=$score['mark_id'];

		$d_mark=mysql_query("SELECT * FROM mark WHERE id='$mid'");
		$mark=mysql_fetch_array($d_mark,MYSQL_ASSOC);
		$mark=nullCorrect($mark);
		$d_ass=mysql_query("SELECT * FROM assessment JOIN eidmid ON
			eidmid.assessment_id=assessment.id WHERE eidmid.mark_id='$mid'");
		$ass=mysql_fetch_array($d_ass,MYSQL_ASSOC);
		$ass=nullCorrect($ass);
		$eid=$ass['id'];
		$d_eidmid=mysql_query("SELECT * FROM eidmid WHERE
				mark_id='$mid' AND assessment_id='$eid'");
		$eidmid=mysql_fetch_array($d_eidmid,MYSQL_ASSOC);
		$eidmid=nullCorrect($eidmid);
		$mid=$eidmid['mark_id'];

		if($ass['subject_id']=='%'){
				$d_bid = mysql_query("SELECT DISTINCT subject_id FROM class JOIN midcid ON
					midcid.class_id=class.id WHERE midcid.mark_id='$mid'");
				$bid=mysql_result($d_bid,0);
				}
		else{$bid=$ass['subject_id'];}

		$Assessment['id_db']=$eid;
	   	$Assessment['Method']=array('label' => 'Method','table_db' =>
					'assessment', 'field_db' => 'method',
					'type_db'=>'char(3)', 'value' => $ass['method']);
	   	$Assessment['ResultQualifier']=array('label' =>
					'Qualifier','table_db' => 'assessment', 'field_db' => 'resultqualifier',
					'type_db'=>'char(2)', 'value' => $ass['resultqualifier']);
	   	$Assessment['Course']=array('label' => 'Course', 'value' => $ass['course_id']);
	   	$Assessment['Subject']=array('label' => 'Subject', 'value' => $bid);
	   	$Assessment['SubjectComponent']=array('label' => 'Subject Component', 
					'value' => $mark['component_id']);
	   	$Assessment['Description']=array('label' =>
					'Description', 'value' => $ass['description']);
	   	$Assessment['Year']=array('label' =>
					'Year', 'value' => $ass['year']);
	   	$Assessment['PrintLabel']=array('label' =>
					'PrintLabel', 'value' => $ass['label']);
		if($eidmid['result']==' '){
				$result=fetchResult($score);
				}
		else{$result=$eidmid['result'];}
	   	$Assessment['Result']=array('label' => 'Result', 'value' => $result);
		$Assessment=nullCorrect($Assessment);
		$Assessments[]=$Assessment;
		}
*/

/*	Get all those assessment results that are archived*/
/*under development*/
   	$d_eidsid=mysql_query("SELECT * FROM eidsid WHERE
				student_id='$sid'");
  	while($eidsid=mysql_fetch_array($d_eidsid,MYSQL_ASSOC)) {
		$eidsid=nullCorrect($eidsid);
		$eid=$eidsid{'assessment_id'};
		$d_ass=mysql_query("SELECT * FROM assessment WHERE id='$eid'");
		$ass=mysql_fetch_array($d_ass,MYSQL_ASSOC);
		$ass=nullCorrect($ass);
		$Assessment['id_db']=$ass{'id'};
	   	$Assessment['Method']=array('label' => 'Method','table_db' =>
					'assessment', 'field_db' => 'method',
					'type_db'=>'char(3)', 'value' => $ass['method']);
	   	$Assessment['ResultQualifier']=array('label' =>
					'Qualifier','table_db' => 'assessment', 'field_db' => 'resultqualifier',
					'type_db'=>'char(2)', 'value' => $ass['resultqualifier']);
	   	$Assessment['Course']=array('label' => 'Course', 'value' => $ass['course_id']);
		if($eidsid['subject_id']=='%'){$subject='';}
				else{$subject=$eidsid['subject_id'];}
	   	$Assessment['Subject']=array('label' => 'Subject', 'value' => $subject);
		if($eidsid['component_id']=='%'){$component='';}
				else{$component=$eidsid['component_id'];}
	   	$Assessment['SubjectComponent']=array('label' => 'Subject Component', 
					'value' => $component);
	   	$Assessment['Description']=array('label' =>
					'Description','value' => $ass['description']);
	   	$Assessment['Year']=array('label' =>
					'Year', 'value' => $ass['year']);
	   	$Assessment['PrintLabel']=array('label' =>
					'PrintLabel', 'value' => $ass['label']);
	   	$Assessment['Result']=array('label' => 'Result','table_db'
					=> 'eidsid', 'field_db' => 'result', 
					'type_db'=>'', 'value' => $eidsid['result']);
		$Assessment['Result']=nullCorrect($Assessment['Result']);
		$Assessment=nullCorrect($Assessment);
		$Assessments[]=$Assessment;
		}
	return $Assessments;
}
?>
