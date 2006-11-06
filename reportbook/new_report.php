<?php
/**											new_report.php
 */

$action='new_report_action.php';
$choice='new_report.php';

$rcrid=$respons[$r]['course_id'];
if($rcrid!=''){
    $d_report=mysql_query("SELECT id FROM report WHERE 
						course_id='$rcrid' ORDER BY date DESC, title");
	$extrabuttons['newsubjectreport']=array('name'=>'current','value'=>'new_report_action.php');
	$tablecaption=get_string('subjectreports');
	}
else{
    $d_report=mysql_query("SELECT id FROM report WHERE 
						course_id='wrapper' ORDER BY date DESC, title");
	$extrabuttons['newreportbinder']=array('name'=>'current','value'=>'new_report_action.php');
	$tablecaption=get_string('reportwrappers',$book);
	}

two_buttonmenu($extrabuttons);
?>
  <form id="formtoprocess" name="formtoprocess" 
	  enctype="multipart/form-data" method="post" action="<?php print $host;?>">

	  <input type="text" style="display:none;" id="Id_db" name="id" value="" />
	  <input type="hidden" name="cancel" value="<?php print '';?>" />
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="choice" value="<?php print $current;?>" />
  </form>

  <div class="content">
	<div class="center">
	  <table class="listmenu" name="listmenu">
		<caption><?php print $tablecaption;?></caption>
		<thead>
		  <tr>
			<th></th>
			<th><?php print get_string('date');?></th>
			<th><?php print_string('stage');?></th>
			<th><?php print_string('title');?></th>
		  </tr>
		</thead>
<?php
	while($report=mysql_fetch_array($d_report,MYSQL_ASSOC)){
	    unset($ReportDef);
		$rid=$report['id'];
		$ReportDef=fetchReportDefinition($rid);
		$rown=0;
?>
		<tbody id="<?php print $rid;?>">
		  <tr class="rowplus" onClick="clickToReveal(this)" id="<?php print $rid.'-'.$rown++;?>">
			<th>&nbsp</th>
			<td><?php print $ReportDef['report']['date']; ?></td>
			<td><?php print $ReportDef['report']['stage']; ?></td>
			<td><?php print $ReportDef['report']['title']; ?></td>
		  </tr>
		  <tr class="hidden" id="<?php print $rid.'-'.$rown++;?>">
			<td colspan="6">
			  <p>
<?php
		if($rcrid!=''){
?>
				<value id="<?php print $rid;?>-MarkCount"><?php print
						 $ReportDef['MarkCount']['value'];?></value> 
				<?php print_string('markbookcolumns',$book);?>
<?php
			}
		else{
			foreach($ReportDef['reptable']['rep'] as $Rep){
?>
				<value id="<?php print $rep['id_db'];?>">
				  <?php print $Rep['course_id'].' '.$Rep['stage'].' '.$Rep['name'];?>
				</value>
				<br />
<?php
				}
			}
?>

			  </p>

			  <button class="rowaction" title="Delete this report"
				name="current" value="delete_report.php" onClick="clickToAction(this)">
				<img class="clicktodelete" />
			  </button>
<?php
		if($rcrid!=''){
?>
			  <button class="rowaction" title="Delete MarkBook columns" name="current" 
				value="delete_report_columns.php" onClick="clickToAction(this)">
				<?php print_string('deletecolumns',$book);?>
			  </button>
			  <button class="rowaction" title="Generate MarkBook columns" name="current" 
				value="generate_report_columns.php" onClick="clickToAction(this)">
				<?php print_string('generatecolumns',$book);?>
			  </button>
<?php
			}
?>
			</td>
		  </tr>
		  <div id="<?php print 'xml-'.$rid;?>" style="display:none;">
<?php
	xmlpreparer('ReportDefinition',$ReportDef);
?>
		  </div>
		</tbody>
<?php
	   }
?>
	  </table>
	</div>
  </div>	
