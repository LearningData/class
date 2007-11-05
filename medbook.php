<?php 
/**												medbook.php
 *	This is the hostpage for the medbook.
 */

$host='medbook.php';
$book='medbook';

include('scripts/head_options.php');
include('scripts/set_book_vars.php');
$session_vars=array('sid','newyid','medtype');
include('scripts/set_book_session_vars.php');

if($sid=='' or $current==''){
	$current='med_student_list.php';
	$_SESSION['medbooksid']='';
	}
elseif($sid!=''){
	/*working with a single student*/
	$Student=fetchStudent($sid);
	$MedicalFlag=$Student['MedicalFlag'];
	}
?>
  <div id="bookbox" class="medbookcolor">
<?php
	if($current!=''){
		include($book.'/'.$current);
		}
?>
  </div>


  <div style="visibility:hidden;" id="hiddenbookoptions" class="bookoptions">
	<form id="medbookchoice" name="medbookchoice" method="post" 
		action="medbook.php" target="viewmedbook">

<?php 
	  if($sid==''){
?>
	  <fieldset class="medbook">
		<legend><?php print_string('filterlist',$book);?></legend>
<?php
		  $onsidechange='yes';
		  include('scripts/list_year.php');

		  $listname='medtype';
		  $listlabel='medtype';
		  $cattype='med';
		  $onsidechange='yes';
		  include('scripts/list_category.php');
?>
	  </fieldset>
<?php
		  }
?>



	</form>
  </div>
<?php
include('scripts/end_options.php');
?>