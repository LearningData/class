<?php 
/**			   			   				import_students_savecidef.php
 *
 *	Saves a file definition.	
 */

$action='import_students_action1.php';
$cancel=$action;

include('scripts/sub_action.php');

	$nofields=$_SESSION['nofields'];
	$idef=$_SESSION['idef'];

//$outname=$fname.'.cidef';
	$filepath=$CFG->eportfolio_dataroot. '/cache/files/';
  	$filepath.='class_export.csv';
  	$file=fopen($filepath, 'w');
	if(!$file){
		$error[]='Unable to open remote file for writing.';
		}
	else{
		//$csv=array();
		for($c=0;$c<$nofields;$c++){
			//$csv=$c.','.$idef[$c][1].','.$idef[$c][2].','.$idef[$c][3];
			//fputs($file,  $out);
			$csv=$idef[$c];
			file_putcsv($file,$csv);
			}
		fclose($file);
		$result[]='Saving definition file.';
?>
		<input type="hidden" name="openexport" id="openexport" value="csv">
<?php
		}

include('scripts/results.php');
include('scripts/redirect.php');
?>
