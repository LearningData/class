<?php
/**										scripts/list_assessment.php
 *
 *
 * $multi>1 returns eids[] or $multi=1 returns eid (default=10)
 * set $required='no' to make not required (default=yes)
 * first call returns eid, second call returns eid1 etc.
 *
 * Lists relevant assessments based on selected academic
 * responsibility or defaults to pastoral responsibilities.
 *
 */

	$cohorts=array();
	$cohids=array();
	if(!isset($seleids)){$seleids=array();}
	if(!isset($curryear)){$curryear='%';}
	if(!isset($selprofid)){$selprofid='';}
	/* Use the academic responsibility if set */
	if($r>-1){
		if($rcrid=='%'){
			/* Just has a subject responsibility, spanning courses. */
			$d_cridbid=mysql_query("SELECT DISTINCT course_id FROM component WHERE
						subject_id='$rbid' AND id='' ORDER BY course_id;"); 
			while($course=mysql_fetch_array($d_cridbid,MYSQL_ASSOC)){
				$cohorts[]=array('id'=>'',
								 'course_id'=>$course['course_id'],
								 'stage'=>'%',
								 'year'=>$curryear
								 );
				}
			}
		elseif(!empty($stage) and !empty($year)){
			/* Try to figure out each previous years cohorts for these students. */
			$stages=(array)list_course_stages($rcrid,$year);
			$nextstage='no';
			for($c2=sizeof($stages)-1;$c2>-1;$c2--){
				if($stages[$c2]['id']==$stage or $nextstage=='yes' or $stage=='%'){
					$cohorts[]=array('id'=>'',
									 'course_id'=>$rcrid,
									 'stage'=>$stages[$c2]['id'],
									 'year'=>$year
									 );
					$nextstage='yes';
					$year--;
					}
				}
			}
		else{
			/**/
			$cohorts[]=array('id'=>'',
							 'course_id'=>$rcrid,
							 'stage'=>'%',
							 'year'=>$curryear
							 );
			}
		}
	elseif(sizeof($ryids)>0){
		/* No academic repsonsiblity set so use a selected pastoral group. */
   		foreach($ryids as $ryid){
			$comcohorts=(array)list_community_cohorts(array('id'=>'','type'=>'year','name'=>$ryid),false);
			foreach($comcohorts as $cohort){
				$cohorts[$cohort['id']]=$cohort;
				}
			}
		}
	elseif(sizeof($rforms)>0){
		$comcohorts=list_community_cohorts(array('id'=>'','type'=>'form','name'=>$rforms[0]['name']),false);
		foreach($comcohorts as $cohort){
			$cohorts[$cohort['id']]=$cohort;
			}
		}


	if(!isset($required)){$required='yes';}
	if(!isset($multi)){$multi='10';}
	if(!isset($ieid)){$ieid='';}else{$ieid++;}
?><br />
	<label for="Assessments"><?php print_string('assessment');?></label>
	<select id="Assessments" tabindex="<?php print $tab++;?>"
		<?php if($required=='yes'){ print ' class="required" ';} ?>
		size="<?php print $multi;?>"
		<?php if($multi>1){print ' name="eids'.$ieid.'[]" multiple="multiple"';}
				else{print ' name="eid'.$ieid.'"';}?> >
		<option value=""></option>
<?php
		$eids=array();
		foreach($cohorts as $cohort){
			$AssDefs=array();
			$AssDefs=(array)fetch_cohortAssessmentDefinitions($cohort,$selprofid,'S');

			print '<optgroup label="'.$cohort['course_id'].' - '.$cohort['year'].'">';

			foreach($AssDefs as$AssDef){
				if(!array_key_exists($AssDef['id_db'],$eids)){
					$eids[$AssDef['id_db']]=$AssDef['id_db'];
?>
		<option 
<?php
					if(in_array($AssDef['id_db'], $seleids)){print ' selected="selected" ';}
					print 'value="'.$AssDef['id_db'].'">';
					print $AssDef['Description']['value']. ' ('.display_date($AssDef['Deadline']['value']).' )'. ' - '.$AssDef['Element']['value'];
					if($AssDef['Stage']['value']!='%'){
						print ' - '.$AssDef['Stage']['value'];
						}
?>
		</option>
<?php
					}
				}
			print '</optgroup>';
			}
?>
	</select>

<?php
unset($required);
unset($multi);
unset($cohorts);
unset($eids);
unset($AssDefs);
unset($selprofid);
?>
