<?php
/**                    httpscripts/delete_assessment_columns.php
 */

require_once('../../scripts/http_head_options.php');

if(!isset($xmlid)){print "Failed"; exit;}

	$eid=$xmlid;
	$AssDef=fetchAssessmentDefinition($eid);
	$AssCount=fetchAssessmentCount($eid);
	$AssDef=array_merge($AssDef,$AssCount);

	$crid=$AssDef['Course']['value'];
	/*Check user has permission to configure*/
	$perm=getCoursePerm($crid,$respons);
	$neededperm='x';
	if($perm["$neededperm"]==1 and $AssDef['MarkCount']['value']>0){
		$d_midcid=mysql_query("DELETE mark, eidmid FROM mark JOIN eidmid ON
	   	eidmid.mark_id=mark.id WHERE eidmid.assessment_id='$eid'");
		$d_midcid=mysql_query("DELETE midcid, eidmid FROM midcid JOIN eidmid ON
	   	eidmid.mark_id=midcid.mark_id WHERE eidmid.assessment_id='$eid'");
		$d_score=mysql_query("DELETE score, eidmid FROM score JOIN eidmid ON
	   	eidmid.mark_id=score.mark_id WHERE eidmid.assessment_id='$eid'");
		$d_eidmid=mysql_query("DELETE FROM eidmid WHERE assessment_id='$eid'");
		$result[]="Deleted mark columns.";
		}

$AssCount=(array)fetchAssessmentCount($eid);
$returnXML=array_merge($AssDef,$AssCount);
$rootName='AssessmentDefinition';
$xmlechoer=true;
require_once('../../scripts/http_end_options.php');
exit;
?>

















