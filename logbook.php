<?php
	$host='logbook.php';
	$book='logbook';
	$fresh='';
	include('scripts/head_options.php');
	if(isset($_POST['new_r'])){$_SESSION['r']=$_POST['new_r'];$fresh='yes';}
	if(!isset($_SESSION['r'])){$_SESSION['r']=-1;$fresh='very';}
?>
  <div style="visibility:hidden;" id="hiddenbookoptions">	
  </div>

  <div style="visibility:hidden;" id="hiddenloginlabel">
	<?php print $tid;?>
  </div>

  <div style="visibility:hidden;" id="hiddenlogbook">
	<div id="logbookstripe" class="logbook"></div>
	<div id="loginworking">
	  <form  id="loginchoice" name="workingas" method="post" 
						action="logbook.php" target="viewlogbook">
	  <select name="new_r" size="1" onChange="document.workingas.submit();">
		<option value="-1" <?php  if($r==-1){print 'selected="selected" ';} ?>
		  ><?php print_string('myclasses');?></option>
<?php 
    for($c=0;$c<(sizeof($respons));$c++){
		/*only lists the academic responsibilities*/
		if($respons[$c]['type']=='a'){
			print '<option value="'.$c.'"';
			if(isset($r) and $r==$c){print ' selected="selected" ';}
			print '>'.$respons[$c]['name'].'</option>';
			}
		}
?>
		</select>
	  </form>
	</div>

	<div id="sidebuttons" class="sidebuttons">
	  <button onclick="viewBook('aboutbook');" title="<?php print_string('about');?>">
		<img src="images/help-browser.png" /></button>
	  <button id="sitestatus" class="hide" >
		<img src="images/roller.gif"/></button>
	  <button id="siteicon" class="show" onClick="loadBook('');" 
		title="<?php print_string('reload');?>" >
		<img src="images/view-refresh.png" alt="<?php print_string('reload');?>" /></button>
	  <button onClick="printGenericContent();" title="<?php print_string('print');?>">
		<img src="images/printer.png" alt="<?php print_string('print');?>" /></button>
	</div>

  </div>

<?php
	if($fresh!=''){
		$role=$_SESSION['role'];
		if($_SESSION['senrole']=='1'){$books[$role]['seneeds']='Support';}
		if($_SESSION['medrole']=='1'){$books[$role]['medbook']='Medical';}
		}

	if($fresh=='yes'){
		/* Responsibilities selection has changed 
		 * (re)loading all the $r dependent ClaSS books.
		 */
		foreach($books[$role] as $bookhost=>$bookname){
			if($bookhost=='markbook' or $bookhost=='reportbook'
			   or $bookhost=='admin'){
?>
				<script>parent.loadBook("<?php print $bookhost; ?>")</script>
<?php
				}
			if($bookhost=='markbook'){
				/* Clear everything because the user's current
				 * selection will no longer be avaible. 
				 */
				unset($_SESSION['classes']);
				unset($_SESSION['cids']);
				unset($_SESSION['cid']);
				unset($_SESSION['pids']);
				unset($_SESSION['pid']);
				unset($_SESSION['umntype']);
				unset($_SESSION['umnrank']);
				unset($_SESSION['umns']);
				unset($_SESSION['viewtable']);
				}
		   }
		}
	elseif($fresh=='very'){
		/* This was loaded after a new login so do some extra stuff:
		 * load the externalbooks, booktabs, update langpref, and raise firstbook
		 */

		if($role=='office'){
			/* This will prevent session timeouts, making an
			 * xmlhttprequest to the logbook/httpscripts/session_alive.php 
			 * every 15 minutes. But only for office users.
			 */
?>
		<script>setInterval("parent.sessionAlive(pathtobook);",15*60*1000);</script>
<?php
			}

		foreach($books[$role] as $bookhost=>$bookname){
?>
				<script>parent.loadBook("<?php print $bookhost; ?>")</script>
<?php
		   }

		$externalbooks=array();
		if(isset($books['external'][$role])){$externalbooks[$role]=$books['external'][$role];}
		else{$externalbooks[$role]=array();}
		foreach($externalbooks[$role] as $bookhost=>$bookname){
			/*loading all the external books - only needed once*/
?>
			<script>parent.loadBook("<?php print $bookhost; ?>")</script>
<?php
		   }

		$showtabs=$books[$role]+$externalbooks[$role];
?>
			<script>parent.loadBook("aboutbook")</script>

  <div style="visibility:hidden;" id="hiddennavtabs">
	<div class="booktabs">
	  <ul>
		<label id="loginlabel">
		</label>
		<li id="logbooktab"><p class="logbook" onclick="logOut();">LogOut</p></li>
		<li id="aboutbooktab" style="display:none;"><p id="currentbook" class="aboutbook">About</p>
		</li>
<?php
		foreach($showtabs as $bookhost=>$bookname){
?>
		<li id="<?php print $bookhost.'tab';?>"><p class="<?php print $bookhost;?>"
		onclick="viewBook(this.getAttribute('class'))"><?php print $bookname;?></p></li>
<?php
			}
?>
	  </ul>
	</div>
  </div>
<?php
		$firstbookpref=$_SESSION['firstbookpref'];
		update_user_language(current_language());
?>
		<script>parent.logInSuccess();</script>
		<script>setTimeout("parent.viewBook('<?php print $firstbookpref; ?>');",6000);</script>
<?php
		}
?>
</body>
</html>
