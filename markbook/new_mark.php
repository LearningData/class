<?php 
/** 									new_mark.php
 *	Creates a new row in the table:mark for one or more classes using table:midcid
 */

$action='new_mark_action1.php';

	$markdef=array();
	for($c=0;$c<sizeof($cids);$c++){
		$cid=$cids[$c];	
		$d_cridbid=mysql_query("SELECT subject_id, course_id 
									FROM class WHERE id='$cid'");
		$bid=mysql_result($d_cridbid,0,0);
		$crid=mysql_result($d_cridbid,0,1);
   		$d_markdef=mysql_query("SELECT name, comment FROM markdef WHERE 
					(subject_id LIKE '$bid' OR subject_id='%') AND
					(course_id LIKE '$crid' OR course_id='%') ORDER BY subject_id");
   		$c2=0;
		while ($new=mysql_fetch_array($d_markdef,MYSQL_ASSOC)){
			if(!in_array($new,$markdef)){
				$markdef[$c2]=$new;
				$c2++;
				}
			}
		}

three_buttonmenu();
?>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <div class="center">
		<table class="listmenu">
		  <caption><?php print_string('chooseamarkdefinition',$book);?></caption>
<?php
	   	for($c=0; $c<sizeof($markdef); $c++){
?>
		  <tr>
			<td>
			  <input type="radio" name="def_name" id="def_name" tabindex="<?php print $c;?>"
				value="<?php print $markdef[$c]['name'];?>" />
			</td><td><?php print $markdef[$c]['name'];?>
			</td><td><?php print $markdef[$c]['comment'];?></td>
		  </tr>
<?php
	 }
if($_SESSION['role']=='admin'){
?>
		  <tr class="special">
			<td><input type="radio" name="def_name" id="def_name" value="custom" /></td>
			<td><?php print_string('newdefinition',$book);?></td>
			<td><?php print_string('newuserdefinedmarktype',$book);?></td>
		  </tr>
<?php
		}
?>
		</table>
	  </div>
 	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	  <input type="hidden" name="cancel" value="<?php print $choice;?>" />
	</form>
  </div>
