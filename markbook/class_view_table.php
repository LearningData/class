<?php
/*********							class_view_table.php
Generates the array $viewtable and stores as a session variable.
*/
$d_students = mysql_query("SELECT * FROM students ORDER BY surname"); 
$row=0;
$viewtable=array();

/*shows comments for last month, needs sohpisticating!!!*/
$tomonth = date('n')-1;
$today	= date('j');
$toyear = date('Y');
$commentdate=$toyear.'-'.$tomonth.'-'.$today;

while($student=mysql_fetch_array($d_students, MYSQL_ASSOC)){
		$sid = $student{'student_id'}; 
		$d_info= mysql_query("SELECT sen FROM info WHERE student_id='$sid'");
		$sen = mysql_result($d_info, 0);

		$comment=commentDisplay($sid,$commentdate);

		$studentrow=array('row'=>$row, 'sen'=>$sen,
				'commentclass'=>$comment['class'], 
				'commentbody'=>$comment['body'], 
				'sid'=>$sid,
				'surname'=>$student{'surname'},
				'forename'=>$student{'forename'},
				'form_id'=>$student{'form_id'},
				'class_id'=>$student{'class_id'});

	for($c=0;$c<$c_marks;$c++) {
		$col_mid=$umns[$c]['id'];

		if ($umns[$c]['display']=='yes' or $umns[$c]['assessment']=='yes'){
/*
		The mark can be one of the four types, and if a score one of a further five
*/
		  $marktype=$umns[$c]['marktype'];
		  $scoretype=$umns[$c]['scoretype'];
	      if($marktype=='score'){
/*				Mark is a score*/
      		$d_score = mysql_query("SELECT * FROM score 
					WHERE mark_id='$col_mid' AND student_id='$sid'");
		    $score=mysql_fetch_array($d_score,MYSQL_ASSOC);

/*			The score can be one of six types: grade, value, percentage, comment, or tier*/
		    if($scoretype=='grade'){
		      	$score_grade=$score{'grade'};
				$grading_grades=$scoregrades[$c];
				//		      	include ('grade_score.php');
				$grade=scoreToGrade($score_grade,$grading_grades);
				$outrank=$score_grade;
			    $out=$grade;
		      	}
		    elseif($scoretype=='value'){
					$score_value=$score{'value'};
		      		$out = $score_value;
					$outrank=$score_value;			      
			      	}
		    elseif($scoretype=='percentage'){
		      	$score_value=$score{'value'};
		      	$score_total=$score{'outoftotal'};
				$marktotal=$mark_total[$c];
		      	if(!isset($score_total)){$score_total=$marktotal;}
		      	include('percent_score.php');
		      	if(isset($percent)){
		      		$out = $percent.' ('.number_format($score_value,1,'.','').')';
					$outrank=$cent;
		      		} 
		      	else{$out='';$outrank=-100;}			      
		      	}

		      elseif($scoretype=='comment'){
			      	$out=$score{'comment'};
					$outrank=-100;
					}
		      	
		      elseif($scoretype=='tier'){
			    $out=$score{'tier'};
				$outrank=-100;
		      	}
   			}
/*********************************************************/

	   	elseif ($marktype=='average') {
/*		Mark is average of several score values*/
			$mids=explode(' ',$midlist[$c]);
			$d_markdef=mysql_query("SELECT markdef.scoretype FROM markdef
				JOIN  mark ON markdef.name=mark.def_name WHERE mark.id='$mids[1]'");
			$avtype=mysql_fetch_array($d_markdef,MYSQL_ASSOC);
			if($avtype{'scoretype'}=='grade'){
				$grading_grades=$scoregrades[$c];
				$gradesum=0;
				$gradecount=0;
				
				for ($c2=0; $c2<sizeof($mids); $c2++){
					$d_score=mysql_query("SELECT grade FROM score WHERE mark_id='$mids[$c2]' AND student_id='$sid'");
					$grade=mysql_fetch_array($d_score,MYSQL_ASSOC);

					if(isset($grade{'grade'})){$gradesum=$gradesum+$grade{'grade'};
											$gradecount++;}
					}
				if($gradecount > 0){
						$score_grade=$gradesum/$gradecount;
						$score_grade=round($score_grade);
						} 
				else {unset($score_grade);}
				//				include('grade_score.php');		
				$grade=scoreToGrade($score_grade,$grading_grades);

				$out=$grade;
				if(isset($score_grade)){$outrank=$score_grade;}else{$outrank=-100;}
			    }
			elseif($avtype{'scoretype'}=='percentage'){
				$scoresum=0;
				$scorecount=0;
				for ($c2=0; $c2<sizeof($mids); $c2++){
					$d_score=mysql_query("SELECT value, outoftotal 
						FROM score WHERE mark_id='$mids[$c2]' AND student_id='$sid'");
					$score=mysql_fetch_array($d_score,MYSQL_ASSOC);
					if($score['value']){
						$score_value=$score{'value'};
						$score_total=$score{'outoftotal'};
						include('percent_score.php'); 
						$scoresum=$cent+$scoresum;
						$scorecount++;
						}
					}
				if($scorecount>0){
					$cent=$scoresum/$scorecount;
					$cent=round($cent,1);
					$percent=sprintf('% 01.1f%%',$cent);
					$out = $percent.' ('.number_format($scorecount,0,'.','').')';
					$outrank = $percent;
					}
				else{$out='';$outrank=-100;}
				}
			else{$out=$avtype{'scoretype'}.'type';$outrank=-100;}
			}


/*********************************************************/

	   	elseif ($marktype =='sum') {
/*		Mark is the sum of several score values*/
				$mids=explode(' ',$midlist[$c]);
				$score_value=0;
				$score_total=0;
				for ($c2=0; $c2<sizeof($mids); $c2++){
					$d_score=mysql_query("SELECT value, outoftotal FROM score WHERE mark_id='$mids[$c2]' AND student_id='$sid'");
					$score=mysql_fetch_array($d_score,MYSQL_ASSOC);
					if($score['value']){$score_value=$score['value']+$score_value; $yesval=1;}
					if($score['outoftotal']){$score_total=$score{'outoftotal'}+$score_total; $yestotal=1;}
					}

				if(isset($yestotal)){include('percent_score.php');
/*					if the scores have a total then must be dealing with a percent */
					if(isset($percent)){
						$out = $percent.' ('.number_format($score_value,1,'.','').')';
						$outrank = $percent;
						}
					else{$out='';$outrank=-100;}
					}
				else{
/*					otherwise its a raw score*/
					if(isset($yesval)){$out=$score_value; $outrank = $score_value;}
					else{$out='';$outrank=-100;}
					}
				unset($yesval);
				unset($yestotal);
			    }

/*********************************************************/

	   	elseif ($marktype =='level') {
/*		Mark is the levelled grade of a score*/
				$levelling_levels=$levels[$c];
				$d_score=mysql_query("SELECT value, outoftotal FROM 
					score WHERE mark_id='$midlist[$c]' AND student_id='$sid'");
				$score=mysql_fetch_array($d_score,MYSQL_ASSOC);
		      	$score_value=$score{'value'};
		      	$score_total=$score{'outoftotal'};
		      	$marktotal=$mark_total[$c];
		      	include('percent_score.php');
		      	include('level_score.php'); 
				$out=$grade;
				$outrank=$cent;	
				}

/*********************************************************/

	   	elseif ($marktype =='compound') {
/*		Mark is a compound column*/
				$mids=explode(' ',$midlist[$c]);
				$yesval=0;
				for ($c2=0; $c2<sizeof($mids); $c2++){
					$d_score=mysql_query("SELECT value, outoftotal FROM score WHERE mark_id='$mids[$c2]' AND student_id='$sid'");
					$score=mysql_fetch_array($d_score,MYSQL_ASSOC);
					if($score['value']){$yesval++;}
					}

				if($yesval<sizeof($mids)){$out='c';}else{$out='C';}
				$outrank=-100;
				}

/*********************************************************/

	   	elseif ($marktype =='report') {
/*		Mark is a compound column*/
				$out='<a href="markbook.php?current=edit_reports.php&choice=class_view.php&midlist='.$umns[$c]['midlist'].'&title='.$umns[$c]['topic'].'&mid='.$umns[$c]['id'].'&pid='.$umns[$c]['component'].'&sid='.$sid.'&col='.$c.'&bid='.$bid[0].'">R</a>';
				$outrank=-100;
				}

/********finished with this mark*******************/
/*		 If no $out set then the mark must be faulty......*/
			if(!isset($out)){$out=''; $outrank=-100;}
					/* .....in case!!!!! */

/*			three entries for each score in the student's row in the $viewtable*/
			$studentrow["$col_mid"]=$out;
/*				displayed on the screen*/
			$studentrow["rank$col_mid"]=$outrank;
/*				the criteria used to sort by should the column be ranked*/
			if(!isset($score['outoftotal'])){$score['outoftotal']='';}
			if(!isset($score['value'])){$score['value']='';}
			if(!isset($score['grade'])){$score['grade']='';}
			if(!isset($score['comment'])){$score['comment']='';}
			$studentrow["score$col_mid"]=$score;
/*				and score values form the database to be used by column_scripts*/
			}
		}
		array_push($viewtable, $studentrow);
		$row++;		
		}

/**************************************************************/
/*		Rank order the table according to $umnrank choice*/	

	if($umnrank=='surname'){
		$sort_array[0]['name']='surname';
		$sort_array[0]['sort']='ASC';
		$sort_array[0]['case']=TRUE;
		$sort_array[1]['name']='forename';
		$sort_array[1]['sort']='ASC';
		$sort_array[1]['case']=TRUE;
		sortx($viewtable, $sort_array);
		}
	else{
		$sort_array[0]['name']="rank$umnrank";
		$sort_array[0]['sort']='DESC';
		$sort_array[0]['case']=TRUE;
		$sort_array[1]['name']='surname';
		$sort_array[1]['sort']='ASC';
		$sort_array[1]['case']=TRUE;
	    sortx($viewtable, $sort_array);
		}

function sortx(&$array, $sort = array()) {
   $function = '';
   while (list($key) = each($sort)) {
     if (isset($sort[$key]['case'])&&($sort[$key]['case'] == TRUE)) {
       $function .= 'if (good_strtolower($a["' . $sort[$key]['name'] . '"])<>good_strtolower($b["' . $sort[$key]['name'] . '"])) { return (good_strtolower($a["' . $sort[$key]['name'] . '"]) ';
     } else {
       $function .= 'if ($a["' . $sort[$key]['name'] . '"]<>$b["' . $sort[$key]['name'] . '"]) { return ($a["' . $sort[$key]['name'] . '"] ';
     }
     if (isset($sort[$key]['sort'])&&($sort[$key]['sort'] == 'DESC')) {
       $function .= '<';
     } else {
       $function .= '>';
     }
     if (isset($sort[$key]['case'])&&($sort[$key]['case'] == TRUE)) {
       $function .= ' good_strtolower($b["' . $sort[$key]['name'] . '"])) ? 1 : -1; } else';
     } else {
       $function .= ' $b["' . $sort[$key]['name'] . '"]) ? 1 : -1; } else';
     }
   }
   $function .= ' { return 0; }';
   usort($array, create_function('$a, $b', $function));
  }
/*****************************************/
/*	All finished.*/

$_SESSION{'viewtable'}=$viewtable;
$_SESSION{'umns'}=$umns;
?>