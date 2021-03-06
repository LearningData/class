<?php
/**										scripts/list_report_wrapper.php
 *
 */

	$todate=date('Y-m-d');
	/* Only include reports which are no more than 10 weeks ahead. */
	$startdate=date('Y-m-d',mktime(0,0,0,date('m'),date('d')+70,date('Y')));

	$reports=array();

	foreach($cohorts as $cohort){
		$crid=$cohort['course_id'];
		$year=$cohort['year'];
		$stage=$cohort['stage'];
		$d_r=mysql_query("SELECT report_id FROM ridcatid JOIN report ON ridcatid.categorydef_id=report.id
								WHERE ridcatid.subject_id='wrapper' AND report.year='$year' AND report.course_id='$crid' 
								AND (report.stage='$stage' OR report.stage='%')
								AND report.date<'$startdate' ORDER BY report.date DESC, report.title;");
		while($r=mysql_fetch_array($d_r,MYSQL_ASSOC)){
			$rid=$r['report_id'];
			$d_report=mysql_query("SELECT id, title, date FROM report WHERE id='$rid';");
			$reports[$rid]=mysql_fetch_array($d_report,MYSQL_ASSOC);
			}
		}
?>
	<h5 for="Reports"><?php print get_string('current').' '.get_string('reports');?></h5>
	<ul id="Reports" class="report-list" >
		<li>
			<ul class="listnothide">
<?php
		foreach($reports as $rid => $report){
			if(strtotime($report['date'])>=strtotime($todate)){
?>
				<li class="listselector">
					<input type="radio" value="<?php print $report['id'];?>" id="<?php print $report['id'];?>" name="wrapper_rid">
					<label for="<?php print $report['id'];?>"><?php print $report['title'].' ('.display_date($report['date']).')';?></label>
				</li>
<?php
				}
			}
?>
			</ul>
		</li>

		<li>
			<div class="button">
				<strong class="plus"></strong>
				<?php print_string('previous');?>
			</div>
			<ul class="listhide">
<?php
		foreach($reports as $rid => $report){
			if(strtotime($report['date']) < strtotime($todate)){
?>
				<li>
					<input type="radio" value="<?php print $report['id'];?>" id="<?php print $report['id'];?>" name="wrapper_rid">
					<label for="<?php print $report['id'];?>"><?php print $report['title'].' ('.$report['date'].')';?></label>
				</li>
<?php
				}
			}
?>
			</ul>
		</li>
	</ul>
