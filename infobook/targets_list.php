<?php
/**                                  targets_list.php
 */

$action='targets_list_action.php';
$cancel='student_view.php';

$Targets=(array)fetchTargets($sid);
$PreviousTargets=(array)fetchPreviousTargets($sid);

three_buttonmenu();
?>
  <div id="heading">
      <h4>
	<label><?php print_string('targets',$book);?></label>
	<?php print $Student['DisplayFullName']['value'];?>
	</h4>
  </div>


  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

<?php
	$yid=$Student['YearGroup']['value'];
	$perm=getYearPerm($yid, $respons);

	foreach($Targets['Target'] as $index => $Target){
		if(is_array($Target)){
			$cattype=$Target['Category']['value_db'];
			$success=$Target['Success']['value'];
?>
			  <table>
				<tr>
				  <td>&nbsp;</td>
			   	</tr>
				<tr>
				  <td>
				  <label for="Detail<?php print $index;?>">
					<?php print $Target['Category']['value']; ?>
				  </label>
				  <div class="right">
					<label>
					<?php xmlelement_input($Target['EntryDate'],$index,$tab,$book);?>
					</label>
				  </div>
				  <span title="<?php print_string('achieved',$book);?>">
					<button type="button" name="success<?php print $index;?>" id="success<?php print $index;?>-butt" value="<?php print $success;?>" onclick="parent.seleryGrow(this,2)"  class="rowaction selery selerytick">
					  <span class="fa"></span>
					</button>
					<input type="hidden" id="success<?php print $index;?>" name="success<?php print $index;?>" value="<?php print $success;?>" />
				  </span>
				  </td>
				</tr>
				<tr>
				  <td>
				  <textarea id="Detail<?php print $index;?>" wrap="on" rows="5" tabindex="<?php print $tab++;?>" name="<?php print $Target['Detail']['field_db'].$index;?>"><?php print $Target['Detail']['value_db'];?></textarea>
				  </td>
				</tr>
			  </table>
<?php
			}
		}
?>
	<input type="hidden" name="current" value="<?php print $action;?>"/>
 	<input type="hidden" name="cancel" value="<?php print $cancel;?>"/>
 	<input type="hidden" name="choice" value="<?php print $choice;?>"/>
	</form>
<h4><?php print get_string('previous',$book).' '.get_string('targets',$book);?></h4>
	  <table class="listmenu">
		<thead>
		  <tr>
			<th colspan="100"></th>
		  </tr>
		</thead>
<?php
		$rown=0;
		foreach($PreviousTargets['Target'] as $index => $Target){
			if(is_array($Target)){
?>
		<tbody id="<?php print $index;?>">
		  <tr id="<?php print $index.'-'.$rown++;?>">
<?php 
		   if($Target['Success']['value']==2){print '<td><div class="negative-box"><span class="fa fa-times"></span></div></td>';}
		   elseif($Target['Success']['value']==1){print'<td> <div class="positive-box"><span class="fa fa-check"></span></div></td>';}
		   else{print '<td></td>';}
?>
			 <td>
<?php
		   print '<strong>'.$Target['Category']['value'].'</strong><div style="margin:5px 5px;""> '.$Target['Detail']['value_db'].'</div>';
?>
			</td>
<?php
		   if(isset($Target['Teacher']['value'])){print '<td>'.$Target['Teacher']['value'].'</td>';}
		   else{print'<td></td>';}
		   if(isset($Target['EntryDate']['value'])){print '<td><label>'.display_date($Target['EntryDate']['value']).'</label></td>';}
		   else{print'<td></td>';}
?>
		  </tr>
		  </tbody>
<?php
			}
		}
?>
	  </table>
  </div>
