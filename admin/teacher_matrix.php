<?php 
/**			  									teacher_matrix.php
 */

$choice='teacher_matrix.php';
$action='teacher_matrix_action.php';

list($crid,$bid,$error)=checkCurrentRespon($r,$respons);
if(sizeof($error)>0){include('scripts/results.php');exit;}

$curryear=get_curriculumyear();

$teachers=list_teacher_users($crid,$bid);

$perm=getCoursePerm($crid,$respons);
if($perm['x']==1){
	$extrabuttons['editclasses']=array('name'=>'current','value'=>'new_class.php');
	}
else{
	$extrabuttons=array();
	}
three_buttonmenu($extrabuttons,$book);
?>
  <div class="topform">
	<form id="formtoprocess" name="formtoprocess" method="post"
	  action="<?php print $host; ?>" >
	  <fieldset class="left divgroup">
		<legend><?php print_string('teacher',$book);?></legend>

		<div class="center">
		  <label for="tid"><?php print_string('unassigned',$book);?></label>
		  <select name="tid" id="tid"  
			eitheror="subtid"  class="requiredor" 
			tabindex="<?php print $tab++;?>" size="1">
<?php
	$allteachers=list_teacher_users();
   	print '<option value="" selected="selected" ></option>';		
   	while(list($tid,$user)=each($allteachers)){
		if(!array_key_exists($tid,$teachers)){
			print '<option  value="'.$tid.'">'.$tid.' ('.$user['surname'].')</option>';
			}
   		}
?>		
			</select>
		</div>

		<div class="center">
		  <label for="subtid"><?php print_string('assigned',$book);?></label>
			<select tabindex="<?php print $tab++;?>"  id="subtid"
			eitheror="tid"  class="requiredor" 
			name="subtid" size="1">
<?php
   	print '<option value="" selected="selected" ></option>';
   	while(list($tid,$user)=each($teachers)){
   		print '<option  value="'.$tid.'">'.$tid.' ('.$user['surname'].')</option>';
   		}
?>		
			</select>
		</div>

	  </fieldset>

	  <fieldset class="right divgroup">
		<legend><?php print_string('classes',$book);?></legend>
		<div class="left">
		  <label for="Unassigned"><?php print_string('unassigned',$book);?></label>
		  <select id="Unassigned" name="newcid[]"  
			tabindex="<?php print $tab++;?>" size="8" multiple="multiple">
<?php		
/*
  	$d_cids=mysql_query("SELECT DISTINCT class.id, class.name FROM class LEFT JOIN tidcid
	  	ON class.id=tidcid.class_id WHERE tidcid.class_id IS NULL AND class.subject_id
		LIKE '$bid' AND class.course_id LIKE '$crid' 
		ORDER BY class.subject_id, class.course_id, name");
   	while($class=mysql_fetch_array($d_cids,MYSQL_ASSOC)){
*/

	$classes=list_course_classes($crid,$bid,'%',$curryear,'nottaught');
	foreach($classes as $class){
   		print '<option ';
		print	' value="'.$class['id'].'">'.$class['name'].'</option>';
	   	}
?>		
		  </select>
		</div>
		<div class="right">
		  <label for="Assigned"><?php print_string('assigned',$book);?></label>
		  <select id="Assigned" name="newcid[]" 
			tabindex="<?php print $tab++;?>" size="8" multiple="multiple">
<?php
/*
  	$d_cids=mysql_query("SELECT DISTINCT class.id, class.name FROM class LEFT
  	JOIN tidcid ON class.id=tidcid.class_id WHERE tidcid.class_id IS
  	NOT NULL AND class.subject_id LIKE '$bid' AND class.course_id LIKE '$crid' ORDER BY
  	class.subject_id, class.course_id, name");

	while($class=mysql_fetch_array($d_cids,MYSQL_ASSOC)){
*/
	$classes=list_course_classes($crid,$bid,'%',$curryear,'taught');
	foreach($classes as $class){
		print '<option ';
		print	' value="'.$class['id'].'">'.$class['name'].'</option>';
		}
?>		
		  </select>
		</div>
	  </fieldset>

	    <input type="hidden" name="curryear" value="<?php print $curryear;?>" />
	    <input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
		<input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>
  </div>

  <div class="content" id="viewcontent">
	<div class="center">
	  <table class="listmenu">
		<tr>
		  <th><?php print get_string('assigned',$book).' '.get_string('teachers',$book);?></th>
		  <th><?php print_string('classesalreadyassigned',$book);?></th>
		</tr>
<?php
   	reset($teachers);
   	while(list($tid,$user)=each($teachers)){
		print '<tr><td>'.$tid.' ('.$user['surname'].')</td>';
		print '<td>';
		$classes=list_teacher_classes($tid,$crid,$curryear);
		foreach($classes as $class){
			print '<span title="'.$class['detail'].'"><a href="admin.php?current=class_edit.php&choice='.$choice.'&cancel='.$choice.'&newtid='.$tid.'&newcid='.$class['id'].'">'.$class['name'].'</a>&nbsp&nbsp</span>';
			}
		print '</td></tr>';
		}
?>
	  </table>
	</div>
  </div>