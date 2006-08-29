<?php
/**									new_staff.php
 */

$choice='new_staff.php';
$action='new_staff_action.php';

include('scripts/sub_action.php');

three_buttonmenu();

?>

  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <div class="center">
		<table class="listmenu">
		  <caption><?php print_string('staff',$book);?></caption>

		  <tr>
			<td><label for="ID"><?php print_string('teacherid');?></label></td>
			<td><input class="required"  pattern="alphanumeric" 
				type="text" id="ID" name="newtid" maxlength="14" /></td>
		  </tr>

		  <tr>
			<td><label for="Surname"><?php print_string('surname');?></label></td>
			<td><input class="required" pattern="alphanumeric"
			  type="text" id="Surname" name="surname" maxlength="30" /></td>
		  </tr>

		  <tr>
			<td><label for="Forename"><?php print_string('forename');?></label></td>
			<td><input class="required" pattern="alphanumeric"
				type="text" id="Forename" name="forename"
			  maxlength="30" /></td>
		  </tr>

		  <tr>
			<td><label for="Number"><?php print_string('staffno');?></label></td>
			<td><input  class="required" pattern="integer"
			type="text" id="Number" name="no" maxlength="3" /></td>
		  </tr>

		  <tr>
			<td><label for="Role"><?php print_string('role');?></label></td>
			<td><?php include('scripts/list_roles.php');?></td>
		  </tr>
		  
		  <tr>
			<td><label for="Email"><?php print_string('email');?></label></td>
			<td><input pattern="email"
				  type="text" id="Email" name="email" maxlength="190" /></td>
		  </tr>
		</table>
	    <input type="hidden" name="current" value="<?php print $action;?>">
		<input type="hidden" name="cancel" value="<?php print '';?>">
		<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
  </div>