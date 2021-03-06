<?php
	while(list($index,$out)=each($result)){
		error_log($out,0,$CFG->serverlog);
		}
	while(list($index,$out)=each($error)){
		error_log($out,0,$CFG->serverlog);
		}
	if(isset($returnXML)){
		if(!$xmlechoer){
			$onload='';
			$xml=xmlpreparer($rootName,$returnXML);
			$xml='<'.'?xml version="1.0" encoding="utf-8"?'.'>'.$xml.'';
			if(isset($_GET['transform']) and $_GET['transform']!=''){
				$template=$_GET['transform'];
				}
			elseif(isset($returnXML['Transform'])){
				$template=$returnXML['Transform'];
				}
			else{
				function array_searchRecursive($string, $array, $strict=false, $value=array()){
					if(!is_array($array)){return false;}
					foreach($array as $key=>$val){
						if(is_array($val) && $sub=array_searchRecursive($string, $val, $strict, $value)){
							$value=$sub;
							return $value;
							}
						elseif((!$strict && $key==$string) || ($strict && $key===$string)){
							$value[$key]=$val;
							return $value;
							}
						}
					return false;
					}
				$search=array_searchRecursive('Transform',$returnXML, true);
				$template=$search['Transform'];
				}
			$html="<!DOCTYPE html>
				<head>
				<meta charset=\"utf-8\">
				<link rel='stylesheet' type='text/css' href='../templates/".$template.".css' media='all'/>
				<link rel='stylesheet' type='text/css' href='css/font-awesome.min.css' media='screen'/>
				<link rel='stylesheet' type='text/css' href='css/templates.css' media='screen'/>
				<script language='JavaScript' type='text/javascript' src='js/templates.js' charset='utf-8'></script>
				<script language='JavaScript' type='text/javascript' src='js/raphael.js' charset='utf-8'></script>
				<script language='JavaScript' type='text/javascript' src='js/g.raphael-min.js' charset='utf-8'></script>
				<script language='JavaScript' type='text/javascript' src='js/g.bar-min.js' charset='utf-8'></script>
				<script language='JavaScript' type='text/javascript' src='js/d3/d3.v3.min.js' charset='utf-8'></script>
				<script language='JavaScript' type='text/javascript' src='js/jcrop/jquery.min.js' charset='utf-8'></script>";
			if($template and file_exists($CFG->installpath.'/templates/'.$template.'.js')){
				$html.="<script language='JavaScript' type='text/javascript' src='../templates/".$template.".js' charset='utf-8'></script>";
				$html.='<link title="Calendar theme" media="all" href="lib/jscalendar/skins/aqua/theme.css" type="text/css" rel="stylesheet">';
				$html.='<script charset="utf-8" src="lib/jscalendar/calendar.js" type="text/javascript" language="JavaScript"></script>';
				$html.='<script charset="utf-8" src="lib/jscalendar/lang/calendar-en.js" type="text/javascript" language="JavaScript"></script>';
				$html.='<script charset="utf-8" src="lib/jscalendar/calendar-setup.js" type="text/javascript" language="JavaScript"></script>';
				$onload="onLoad='".$template."();'";
				}
			$html.="<meta http-equiv='pragma' content='no-cache'/>
				<meta http-equiv='Expires' content='0'/>
				</head>
				<body $onload>";
			if($template and file_exists($CFG->installpath.'/templates/'.$template.'.xsl')){
				$html.=xmlprocessor($xml,$template.'.xsl');
				}
			else{
				$html.="<p>".get_string("notemplatefound")."</p>";
				}
			$html.="</body>
				</html>";
			header('Content-Type: application/json');
			$html=str_replace(array("\n","\r","\t"),'',$html);
			$response=array("html"=>$html,"template"=>$template,'xml'=>$xml);
			echo json_encode($response);
			}
		else{
			header('Content-Type: text/xml');
			xmlechoer("$rootName",$returnXML);
			}
		}
	elseif(isset($returnText)){
		header('Content-Type: text/plain'); 
		echo $returnText;
		}
?>
