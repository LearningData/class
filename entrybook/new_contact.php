<?php
/**									new_contact.php
 */

$action='new_contact_action.php';

if(isset($_POST['sid']) and $_POST['sid']!=''){
	$sid=$_POST['sid'];
	if(isset($_POST['pregid']) and $_POST['pregid']!=''){
		$gid=$_POST['pregid'];
		}
	else{
		$gid=-1;
		}
	$Contact=fetchContact(array('guardian_id'=>$gid,'student_id'=>-1));
	$Phones=(array)$Contact['Phones'];
	$Addresses=$Contact['Addresses'];
	$Student=fetchStudent_short($sid);
	$extrabuttons=array();
	$d_guardian=mysql_query("SELECT id, CONCAT(surname,', ',forename)
								AS name FROM guardian ORDER BY surname;");
	}
else{
	$choice='new_contact.php';
	$Contact=fetchContact(array('guardian_id'=>-1));
	$Phones=array();
	}

three_buttonmenu();


/*always add a blank record for new entries*/
while(sizeof($Phones)<4){$Phones[]=fetchPhone();}
$Addresses[]=fetchAddress();

/*TODO: temporarily only one address for display*/
$Address=$Addresses[0];


if(isset($sid)){
?>
  <div id="heading">
	<form id="headertoprocess" name="headertoprocess" method="post" action="<?php print $host;?>">
	<h4><label><?php print $Student['DisplayFullName']['value'];?></label></h4>
<?php
		$listname='pregid';$listlabel='';$liststyle='width:12em;';
		include('scripts/set_list_vars.php');
		list_select_db($d_guardian,$listoptions,$book);
		$button['linkcontact']=array('name'=>'sub','value'=>'Link');
		all_extrabuttons($button,'entrybook','processHeader(this)');
?>
 	<input type="hidden" name="sid" value="<?php print $sid;?>">
 	<input type="hidden" name="current" value="<?php print $action;?>">
 	<input type="hidden" name="cancel" value="<?php print $cancel;?>">
 	<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
  </div>
<?php
	}
?>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	  <div class="center">
		  <?php $tab=xmlarray_form($Contact,'','newcontact',$tab,'infobook'); ?>
	  </div>
	  <div class="center">
		  <?php $tab=xmlarray_form($Address,'0','contactaddress',$tab,'infobook'); ?>
	  </div>
<?php
	reset($Phones);
	while(list($phoneno,$Phone)=each($Phones)){
?>
	  <div class="center">
		  <?php $tab=xmlarray_form($Phone,$phoneno,'',$tab,'infobook'); ?>
	  </div>
<?php
		}
if(isset($sid)){
?>
	    <input type="hidden" name="sid" value="<?php print $sid;?>">
<?php
		}

if(isset($gid)){
?>
	    <input type="hidden" name="gid" value="<?php print $gid;?>">
<?php
		}
if(isset($_POST['contactno'])){
?>
	    <input type="hidden" name="contactno" value="<?php print $_POST['contactno'];?>">
<?php
		}
?>
	    <input type="hidden" name="current" value="<?php print $action;?>">
		<input type="hidden" name="cancel" value="<?php print '';?>">
		<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
  </div>