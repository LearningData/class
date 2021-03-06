<?php
/**								   register_notice.php
 *
 */

$action='register_notice_action.php';
$choice='completion_list.php';

if(isset($_POST['comids'])){
	$comids=(array)$_POST['comids'];
	}
else{
	$comids=array();
	}


include('scripts/sub_action.php');

if(sizeof($comids)==0){
	$result[]='Please choose a group for the notice.';
	$action='completion_list.php';
	include('scripts/results.php');
	include('scripts/redirect.php');
	exit;
	}


$from_user=get_user($tid);


three_buttonmenu();
?>

  <div id="heading">
	<label><?php print_string('notice',$book);?></label>
  </div>

  <div id="viewcontent" class="content">

	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <div class="divgroup center">
		<div class="center">
		</div>

		  <div class="right">
			<?php $required='yes'; include('scripts/jsdate-form.php');?>
		  </div>

		<div class="center">
		  <textarea  tabindex="<?php print $tab++;?>" name="noticebody" 
			class="htmleditorarea" cols="78" rows="12" id="noticebody"></textarea>
		</div>


<?php
		foreach($comids as $comid){
			print '<input type="hidden" name="comids[]" value="'.$comid.'" />';
			}
?>
		<input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="cancel" value="<?php print $choice;?>" />
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
	  </div>

	</form>


	</div>
  </div>
<script src="lib/tiny_mce/tiny_mce.js" type="text/javascript"></script>
<script src="lib/tiny_mce/loadeditor.js?version=<?php echo $CFG->version; ?>" type="text/javascript"></script>
<script type="text/javascript">loadEditor();</script>
