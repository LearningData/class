<?php
/**											jsdate-form.php
 *	 generic date selection, configured to use the jscalendar
 * auto-fill value will be today's date unless $todate has a value set
 * required by default (set $required to no if not wanted)
 * will be named and ided as date0, date1, date2 etc. if called multiple
 * times, need to set xmldate to change this (probably only needed if
 * the form to be auo-completed from xml)
 */
if(!isset($required)){$required='yes';}
if(!isset($onchange)){$onchange='no';}
if(!isset($onchangeaction)){$onchangeaction='';}
if(isset($todate)){$thedate=$todate;}
else{$thedate=date('Y-m-d');}
/* Set if this is the ith time that date-form has been called*/
if(!isset($idate)){$idate=0;}else{$idate++;}
if(isset($xmldate)){$dateid=$xmldate;}else{$dateid='Date'.$idate;}
?>
<div id="calendar-Date<?php print $idate;?>">
	<label for="<?php print $dateid;?>"><?php print_string('date');?></label>
	<input type="date" tabindex="<?php print $tab++;?>"
	  <?php if($onchange=='yes' and $onchangeaction!=''){print ' onchange="'.$onchangeaction.'" ';} ?>
	  <?php if($required=='yes'){print ' class="required" ';} ?> id="<?php print $dateid;?>" name="<?php print strtolower($dateid);?>" value="<?php print $thedate;?>" />
	<img class="calendar">
<?php 
/* print display_date($thedate) */;
?>
</div>
<?php
	unset($onchange);
	unset($onchangeaction);
	unset($required);
	unset($thedate);
?>
