<?php
/**								scripts/set_book_session_vars.php
 *
 * For any book variables which maintain state between page loads.
 * Only guaranteed for single-valued vars NOT arrays!
 * Most likely applies to the vars posted by the sideoptions. 
 * The vars are listed in array $session_variables
 */

while(list($index,$varname)=each($session_vars)){
	$session_varname=$book . $varname;
	if(!isset($_SESSION[$session_varname])){$_SESSION[$session_varname]='';}
	if(isset($_POST[$varname])){
		$_SESSION[$session_varname]=$_POST[$varname];
		}
	if(isset($_GET[$varname])){
		$_SESSION[$session_varname]=$_GET[$varname];
		}
	$$varname=$_SESSION[$session_varname];
	//if($book=='register'){trigger_error($varname.': '.$$varname,E_USER_WARNING);}
	}
?>
