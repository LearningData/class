<?php 
/*												import_students.php	
*/
$choice='import_students.php';
$action='import_students_action0.php';

three_buttonmenu();
?>
  <div class="content">
	<form name="formtoprocess" id="formtoprocess" 
	  method="post" enctype="multipart/form-data" action="<?php print $host;?>">

	  <fieldset class="divgroup left">
  		<h5><?php print_string('requirements');?></h5>
  		<?php print_string('importstudentfileinstructions',$book);?>
	  </fieldset>
	
	  <fieldset class="divgroup right">
		<h5><?php print_string('selectfile',$book);?></h5>
		<label for="Filename"><?php print_string('filename',$book);?></label>
		<input class="required" type="file" id="Filename" name="importfile" />
	  </fieldset>
	

	  <fieldset class="left">
		<h5><?php print_string('fieldseparator',$book);?></h5>
		<label for="sid"><?php print_string('comma',$book);?></label>
		<input type="radio" name="separator" tabindex="<?php print $tab++;?>"
			eitheror="enrolno"  class="requiredor" checked="checked" 
			id="comma" title="" value="comma" />

		<label for="enrolno"><?php print_string('semicolon','infobook');?></label>
		<input type="radio" name="separator" tabindex="<?php print $tab++;?>"
		  eitheror="sid"  class="requiredor"
		  title="" id="semicolon" value="semicolon" />
	  </fieldset>


	  <fieldset class="right">
		<h5><?php print_string('records',$book);?></h5>
		<label for="multiline"><?php print_string('multiplelines',$book);?></label>
		<select class="required" id="multiline" name="multiline">
		  <option value="1">1</option>
		  <option value="2">2</option>
		  <option value="3">3</option>
		  <option value="4">4</option>
		  <option value="5">5</option>
		</select>
	  </fieldset>
	
 	<input type="hidden" name="MAX_FILE_SIZE" value="800000">	
 	<input type="hidden" name="current" value="<?php print $action;?>">
 	<input type="hidden" name="choice" value="<?php print $choice;?>">
 	<input type="hidden" name="cancel" value="<?php print '';?>">
	</form>
  </div>















