<?php
/**			  					new_contact_action.php
 *
 */

$action='new_contact.php';
$action_post_vars=array('pregid','sid','contactno');

if(isset($_POST['pregid']) and $_POST['pregid']!=''){
	/*don't need to do anything else*/
	$sid=$_POST['sid'];
	$pregid=$_POST['pregid'];
	}
elseif(isset($_POST['sid']) and $_POST['sid']!=''){

	if(isset($_POST['contactno']) and $_POST['contactno']>0){
		$action='new_student.php';
		$cancel='new_student.php';
		$contactno=2;
		}
	else{
		$contactno=1;
		}

	$sid=$_POST['sid'];
	if(isset($_POST['gid'])){$gid=$_POST['gid'];}
	}

include('scripts/sub_action.php');

if($sub=='Submit'){

	if(isset($gid) and $gid!='-1' and $gid!=''){
		$Contact=fetchContact(array('guardian_id'=>$gid,'student_id'=>'-1'));
		$Phones=(array)$Contact['Phones'];
		$Addresses=$Contact['Addresses'];
		}
	else{
		if(isset($sid)){
			$Contact=fetchContact(array('guardian_id'=>'-1','student_id'=>'-1'));
			}
		else{
			$Contact=fetchContact(array('guardian_id'=>'-1'));
			}
		$Phones=array();
		$Addresses=array();
		mysql_query("INSERT INTO guardian SET surname='';");
		$gid=mysql_insert_id();
		}

	$Addresses[]=fetchAddress();
	while(sizeof($Phones)<4){$Phones[]=fetchPhone();}



	if(isset($sid)){
		mysql_query("INSERT INTO gidsid SET guardian_id='$gid', student_id='$sid';");
		}


	reset($Contact);
	while(list($key,$val)=each($Contact)){
		if(isset($val['value']) and is_array($val) and isset($val['table_db'])){
			$field=$val['field_db'];
			$inname=$field;
			$inval=clean_text($_POST[$inname]);
			if($val['table_db']=='guardian'){
				mysql_query("UPDATE guardian SET $field='$inval' WHERE id='$gid';");
				}
			elseif($val['table_db']=='gidsid'){
				mysql_query("UPDATE gidsid SET $field='$inval' 
						WHERE guardian_id='$gid' AND student_id='$sid';");
				}
			}
		}


	reset($Phones);
	while(list($phoneno,$Phone)=each($Phones)){
		$phoneid=$Phone['id_db'];
		while(list($key,$val)=each($Phone)){
			if(isset($val['value']) and is_array($val) and isset($val['field_db'])){	
				$field=$val['field_db'];
				$inname=$field.$phoneno;
				$inval=clean_text($_POST["$inname"]);
				if($val['value']!=$inval){
					if($phoneid=='-1' and $inval!=''){
						mysql_query("INSERT INTO phone SET some_id='$gid';");
						$phoneid=mysql_insert_id();
						}
					mysql_query("UPDATE phone SET $field='$inval'
							WHERE some_id='$gid' AND id='$phoneid';");
					}
				}
			}
		}


	reset($Addresses);
	while(list($addressno,$Address)=each($Addresses)){
		$aid=$Address['id_db'];
		reset($Address);
		while(list($key,$val)=each($Address)){
			if(isset($val['value']) and is_array($val) and isset($val['table_db'])){
				$field=$val['field_db'];
				$inname=$field. $addressno;
				$inval=clean_text($_POST[$inname]);
				if($inval!='' and $aid=='-1'){
					mysql_query("INSERT INTO address SET country='';");
					$aid=mysql_insert_id();
					mysql_query("INSERT INTO gidaid SET guardian_id='$gid',
					address_id='$aid';");
					}
				if($val['table_db']=='address' and isset($aid)){
					mysql_query("UPDATE address SET $field='$inval'	WHERE id='$aid';");
					}
				elseif($val['table_db']=='gidaid' and isset($aid)){
					mysql_query("UPDATE gidaid SET $field='$inval'
	   					   WHERE guardian_id='$gid' AND address_id='$aid';");
					}
				}
			}
		}

	$result[]=get_string('newcontactadded',$book);

	}


include('scripts/results.php');
include('scripts/redirect.php');
?>
