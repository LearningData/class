<?php 
/**									scripts/list_template.php
 *
 */

if(!isset($required)){$required='yes';}
$showtemplates=(array)list_directory_files('../templates','xsl');
?>
  <label for="template"><?php print_string('template');?></label>
  <select name="template" id="template" size="1"
  <?php if($required=='yes'){ print ' class="required" ';} ?>
	>
   	<option value=""></option>
<?php
	foreach($showtemplates as $key => $templatename){
		print '<option ';
		if(isset($seltemplate)){if($seltemplate==$templatename){print 'selected="selected"';}}
		print	' value="'.$templatename.'">'.$templatename.'</option>';
		}
?>
  </select>

 





















