<?php
/**							statementbank.php
 *
 *
 *	@package	Classis
 *	@author		stj@laex.org
 *	@copyright	S T Johnson 2004-2016
 *	@version
 *	@since
 */

/**
 *
 * @global array $CFG
 * @return string 
 */
function connect_statementbank(){
	global $CFG;
	$dbstat='';
	if($CFG->statementbank_db!=''){
		$dbstat=db_connect(true,$CFG->statementbank_db);
		}
	return $dbstat;
	}

/**
 *
 * @param string $crid
 * @param string $bid
 * @param string $pid
 * @param string $stage
 * @param string $dbstat
 * @return array
 */
function fetchStatementBank($crid,$bid,$pid,$stage,$dbstat=''){
	$StatementBank=array();
	if($dbstat==''){$dbstat=connect_statementbank();}
	if($dbstat!=''){
		if($pid==''){$pid='%';}
		if($stage==''){$stage='%';}
		$d_area=mysql_query("SELECT DISTINCT area.id, area.name FROM area
				JOIN grouping ON area.id=grouping.area_id WHERE
	   			(grouping.course_id='$crid' OR grouping.course_id='%')
				AND grouping.subject_id='$bid' AND 
	   			(grouping.component_id LIKE '$pid' OR
					grouping.component_id='%') AND (grouping.stage
					LIKE '$stage' OR grouping.stage='%')");
		while($area=mysql_fetch_array($d_area,MYSQL_ASSOC)){
			$areaid=$area['id'];
			$StatementBank['Area']["$areaid"]['Name']=$area['name'];
			$d_grouping=mysql_query("SELECT DISTINCT id, rating_name FROM grouping WHERE
						(course_id LIKE '$crid' OR course_id='%') 
						AND subject_id='$bid' AND area_id='$areaid'
						AND (component_id LIKE '$pid' OR
						component_id='%') AND (stage LIKE '$stage' OR stage='%');");

			$Statements=array();
			while($grouping=mysql_fetch_array($d_grouping,MYSQL_ASSOC)){
				$grid=$grouping['id'];
				$ratingname=$grouping['rating_name'];

				if(!isset($nolevels)){
					/*TODO: this will only use one set of levels and even
					worse that of the first group only*/
					$d_rating=mysql_query("SELECT descriptor, value FROM
						rating WHERE name='$ratingname' ORDER BY value
						DESC;");
					$Levels=array();
					while($rating=mysql_fetch_array($d_rating,MYSQL_ASSOC)){
						$Level=array();
						$Level['Name']=$rating['descriptor'];
						$Level['Value']=$rating['value'];
						$Levels[]=$Level;
						}
					$nolevels=sizeof($Levels);
					}

				$d_stat=mysql_query("SELECT * FROM statement JOIN gridstid 
				ON statement.id=gridstid.statement_id WHERE gridstid.grouping_id='$grid';");
				while($statement=mysql_fetch_array($d_stat,MYSQL_ASSOC)){
					$Statements[]=fetchStatement($statement,$nolevels);
					}
				}
			$StatementBank['Area']["$areaid"]['Statements']=$Statements;
			$StatementBank['Area']["$areaid"]['Levels']=$Levels;
			}
		}
	return $StatementBank;
	}


/**
 *
 * @param array $new
 * @param string $dbstat
 * @return string 
 */
function add_statement($new,$dbstat=''){
	/*currently the bid can not be set to a wildcard and perhaps it*/
	/*should not be allowed this value either?*/
	$result='no';
	$todate=date('Y').'-'.date('n').'-'.date('j');
	if($dbstat==''){$dbstat=connect_statementbank();}
	if($dbstat!=''){
		if($new['crid']==''){$crid='%';}else{$crid=$new['crid'];}
		if($new['bid']==''){$bid='%';}else{$bid=$new['bid'];}
		if($new['pid']==''){$pid='%';}else{$pid=$new['pid'];}
		if($new['stage']==''){$stage='%';}else{$stage=$new['stage'];}
		$area=$new['area'];
		$subarea=$new['subarea'];
		$statement=$new['statement'];
		$ability=$new['ability'];

		if(mysql_query("INSERT INTO statement (author,
					   	entrydate, statement_text, rating_fraction
					) VALUES ('Classis', '$todate', '$statement','$ability');")){
			$stid=mysql_insert_id();
			$result='yes';
			$d_area=mysql_query("SELECT id FROM area WHERE name='$area';");
			if(mysql_num_rows($d_area)>0){$areaid=mysql_result($d_area,0);}
			else{
				mysql_query("INSERT INTO area (name) VALUES ('$area');");
				$areaid=mysql_insert_id();
				}

			$d_grouping=mysql_query("SELECT DISTINCT id, rating_name FROM grouping WHERE
						course_id='$crid' AND subject_id='$bid' AND area_id='$areaid'
						AND component_id='$pid' AND stage='$stage';");
			if(mysql_num_rows($d_grouping)>0){$grid=mysql_result($d_grouping,0);}
			else{
				/*everyhting currently only gets a default fivegrade rating_name!!!!*/
				mysql_query("INSERT INTO grouping (area_id,
					   	subarea_id, course_id, subject_id,
						component_id, stage, rating_name
					) VALUES ('$areaid', '0', '$crid','$bid','$pid','$stage','fivegrade');");
				$grid=mysql_insert_id();
				}

			mysql_query("INSERT INTO gridstid (grouping_id, statement_id
					) VALUES ('$grid', '$stid');");
			}
		}
	return $result;
	}


?>