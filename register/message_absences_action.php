<?php
/**									message_absences_action.php
 *
 */

$action='absence_list.php';


$recipients=array();

include('scripts/sub_action.php');

if($_POST['all0']=='yes'){
	$recipients=$_SESSION[$book.'authrecipients'];
	}
elseif($_POST['unauth0']=='yes'){
	$recipients=$_SESSION[$book.'unauthrecipients'];
	}
else{
	$current='';
	$action='';
	$choice='';

 	$result[]=get_string('noactiontaken');

	include('scripts/results.php');
	include('scripts/redirect.php');
	exit;
	}



if($recipients and sizeof($recipients)>0){
	$sentno=0;
	$smsno=0;
	$failno=0;
	if($CFG->emailoff!='yes'){

		if(!empty($CFG->emailregisternoreply)){
			$replyto=$CFG->emailregisternoreply;
			}
		else{
			if(is_array($CFG->emailnoreply)){
				$replyto=$CFG->emailnoreply[0];
				}
			else{
				$replyto=$CFG->emailnoreply;
				}
			}

		$footer=get_string('guardianemailfooterdisclaimer');
		$messagesubject=$CFG->schoolname.': '.get_string('attendance',$book);
		foreach($recipients as $recipient){
			
			if($recipient['email']!=''){
				$message='<p>'.get_string('absencemessage',$book,$recipient['studentname']).'</p>';
				$messagetxt=strip_tags(html_entity_decode($message, ENT_QUOTES, 'UTF-8'))."\r\n".'--'. "\r\n" . $footer;
				$message.='<br /><hr><p>'. $footer.'<p>';
				$send_result=send_email_to($recipient['email'],'',$messagesubject,$messagetxt,$message,'',$replyto);
				if($send_result){$sentno++;}
				else{$failno++;}
				}
			if(isset($CFG->smsoff) and $CFG->smsoff=='no' and $_POST['sms0']=='yes' and $recipient['mobile']!=''){
				$message=get_string('absencemessage',$book,$recipient['studentname']);
				$messagetxt=strip_tags(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
				$send_result=send_sms_to($recipient['mobile'],$messagetxt);
				if($send_result){$smsno++;}
				else{$failno++;}
				}

			}

		if($sentno>0){
			$result[]=get_string('emailsentto','infobook').' '. $sentno.' '.get_string('contacts','infobook');
			}
		if($smsno>0){
			$result[]=get_string('smssentto','infobook').' '. $smsno.' '.get_string('contacts','infobook');
			}
		}

	if($failno>0){$result[]=get_string('failedtosend','infobook').' '.$failno;}

	}
else{
	$result[]=get_string('no','infobook').' '.get_string('contacts','infobook');
	}

unset($_SESSION[$book.'unauthrecipients']);
unset($_SESSION[$book.'authrecipients']);
include('scripts/results.php');	
include('scripts/redirect.php');	
?>
