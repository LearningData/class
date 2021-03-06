<?php 
/**												lms.php
 *	This is the hostpage for the lms
 *  It currently works only with customised install of Moodle
 */

$host='lms.php';
$book='lms';

include ('scripts/head_options.php');

$ip=$_SERVER['REMOTE_ADDR'];
$salt=$CFG->lmsshare.$CFG->support;
$secret=md5($salt . $ip);
$token=md5($tid . $secret);
$entrypage='';
$user=get_user($tid);
$externalparams=array(
			  'lang' => current_language(),
			  'token' => $token,
			  'user' => $tid,
			  'passwd' => '',
			  );
/*construct the redirect string*/
$externalred=$CFG->lmssite . '/'.$entrypage;
while(list($param,$value)=each($externalparams)){
	if(!isset($joiner)){$joiner='?';}
	else{$joiner='&';}
	$externalred=$externalred . $joiner . $param . '=' . $value;
	}
?>

  <div style="visibility:hidden;" id="hiddenbookoptions">
	<fieldset class="lms">
	  <legend><?php print_string('options');?></legend>
	</fieldset>
  </div>

  <div id="bookbox" class="lmscolor">
	<iframe id="externalbook" name="externalbook" class="externalbookframe"></iframe>
  </div>

<?php
include('scripts/end_options.php');
?>
<script>frames["externalbook"].location.href="<?php print $externalred;?>";</script>
