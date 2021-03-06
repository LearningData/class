<?php 
/**								staff_details.php
 *
 */

$action='staff_details_action.php';

if(isset($_POST['seluid'])){$seluid=$_POST['seluid'];}
elseif(isset($_GET['seluid'])){$seluid=$_GET['seluid'];}
else{$seluid='';}

/*This is the record being edited.*/
$User=fetchUser($seluid);

/* Super user perms for user accounts. */ 
$aperm=get_admin_perm('u',$_SESSION['uid']);

three_buttonmenu();
?>
  <div class="content">
	<form name="formtoprocess" id="formtoprocess" method="post" novalidate action="<?php print $host; ?>">

		<div class="staff-box passlabel imgprofile">
		  <?php photo_img($User['EPFUsername']['value'],$seluid,'w','staff'); ?>
		</div>

		<div class="staff-box rowone">
		  <fieldset class="divgroup">
			<p>
			  <label for="ID"><?php print_string('username');?></label>
			  <input pattern="truealphanumeric" readonly="readonly" type="text" id="ID" name="username" maxlength="14" value="<?php print $User['Username']['value'];?>" />
			</p>
			<p>
<?php 
				$selbook=$User['FirstBook']['value'];
				include('scripts/list_books.php');
?>
		  </p>
		  </fieldset>
		</div>


		<div class="staff-box rowone">
		  <fieldset class="divgroup">

<?php
if($_SESSION['role']=='admin' or $aperm==1){
?>
		  <p>
			<label for="Work level"><?php print_string('workexperiencelevel',$book);?></label>
			<select name="worklevel" id="Worklevel" size="1" tabindex="<?php print $tab++;?>" class="required" >
			  <option value=""></option>
				<?php
						$worklevels=array('-1'=>'useless','0'=>'tryharder','1'=>'good','2'=>'verygood');
						foreach($worklevels as $index => $worklevel){
							print '<option ';
							if(isset($User['Worklevel']['value'])){if($User['Worklevel']['value']==$index){print 'selected="selected"';}}
							print	' value="'.$index.'">'.get_string($worklevel,$book).'</option>';
							}
				?>
			</select>
		  </p>
		  <p>
<?php 
					 $selrole=$User['Role']['value']; 
					 include('scripts/list_roles.php');
?>
		  </p>
		  <p>
<?php
					  unset($key);
					  $listname='senrole';$selsenrole=$User['SENRole']['value'];
					  $listlabel='senrole';$listlabelstyle='external';
					  include('scripts/set_list_vars.php');
					  $list[]=array('id'=>'0','name'=>get_string('no'));
					  $list[]=array('id'=>'1','name'=>get_string('yes'));
					  list_select_list($list,$listoptions);
?>
		  </p>
		  <p>
<?php		
					  unset($key);
					  $listname='medrole';$selmedrole=$User['MedRole']['value'];
					  $listlabel='medrole';$listlabelstyle='external';
					  $required='yes';
					  include('scripts/set_list_vars.php');
					  list_select_list($list,$listoptions);
			
?>
		  </p>
<?php
		}
	else{
?>
	  <input type="hidden" name="role" value="<?php print $User['Role']['value']; ?>">
	  <input type="hidden" name="senrole" value="<?php print $User['SENRole']['value']; ?>">
	  <input type="hidden" name="medrole" value="<?php print $User['MedRole']['value']; ?>">
	  <input type="hidden" name="worklevel" value="<?php print $User['Worklevel']['value']; ?>">
	  <input type="hidden" name="nologin" value="<?php print $User['NoLogin']['value']; ?>">
<?php
		}
?>
		</fieldset>
	</div>

<?php
	if($_SESSION['role']=='admin'  or $aperm==1){
?>
  <div class="center">
	<div class="staff-box passlabel">
	  <fieldset class="divgroup">
		<h5><?php print_string('password',$book);?></h5>
			<p>
		  <label style="width: 150px; display: inline-block;" for="Number1"><?php print_string('newstaffpin',$book);?></label>
		  <input pattern="integer" tabindex="<?php print $tab++;?>" type="password" id="Number1" name="pin1" pattern="integer" maxlength="4" />
			</p>
			<p>
		  <label style="width: 150px; display: inline-block;" for="Number2"><?php print_string('retypenewstaffpin',$book);?></label>
		  <input pattern="integer" tabindex="<?php print $tab++;?>" type="password" id="Number2" name="pin2" pattern="integer" maxlength="4" />
			</p>
		  <input type="checkbox" id="Nologin" name="nologin"  tabindex="<?php print $tab++;?>" <?php if($User['NoLogin']['value']=='1'){print 'checked="checked"';}?> value="1"/>
		  <label for="Nologin"><?php print_string('disablelogin',$book);?></label>
	  </fieldset>
	</div>

	<div class="staff-box staff-box-middle">
	  <fieldset class="divgroup">
		<h5><?php print_string('section',$book);?></h5>
<?php
		$sections=(array)list_sections(true);
		$access_groups=(array)list_user_groups($seluid,'s');
		foreach($sections as $section){
			if(in_array($section['gid'],$access_groups)){$editaperm=true;}
			else{$editaperm=false;}
?>
		  <p>
		  <input type="checkbox" id="a<?php print $section['gid'];?>" name="a<?php print $section['gid'];?>"  tabindex="<?php print $tab++;?>" <?php if($editaperm){print 'checked="checked"';}?> value="1"/>
		  <label for="<?php print $section['name'];?>"><?php print $section['name'];?></label>
		  </p>
<?php
			}
?>
		</fieldset>
	</div>
	<div class="staff-box">
<?php
	if($_SESSION['role']=='admin'){
?>
	  <fieldset class="divgroup">
		<h5><?php print_string('specialadminpermissions',$book);?></h5>
<?php
		$agroups=(array)list_admin_groups();
		foreach($agroups as $type=>$agroup){
			$editaperm=get_admin_perm($type,$seluid);
?>
			<p>
			<input type="checkbox" id="a<?php print $agroup['gid'];?>" name="a<?php print $agroup['gid'];?>"  tabindex="<?php print $tab++;?>" <?php if($editaperm){print 'checked="checked"';}?> value="1"/>
			<label for="<?php print $agroup['name'];?>"><?php print_string($agroup['name'],$book);?></label>
			</p>
<?php
			}
?>
	  </fieldset>
<?php
		}
?>
	 </div>
  </div>
<?php
		}
?>

  	  <div class="center" style="margin: 30px 0; float: left;">
		<?php $tab=xmlarray_form($User,'','details',$tab,'infobook'); ?>
	  </div>

<?php
	if(isset($User['ExtraInfo']) and count($User['ExtraInfo'])>0){
?>
	  <div class="center" style="margin: 0 0 30px; float: left;">
		<?php
			$tab=xmlarray_form($User['ExtraInfo'],'','extrainfo',$tab,'infobook'); 
		?>
	  </div>
<?php
		}
?>

	  <div class="center" style="margin: 0 0 30px; float: left;">
		<?php
			$addressno='0';/*Only doing one address.*/
			$tab=xmlarray_form($User['Address'],$addressno,'contactaddress',$tab,'infobook'); 
		?>
		<input type="hidden" name="addid" value="<?php print $User['Address']['id_db']; ?>">
	  </div>

	  <input type="hidden" name="seluid" value="<?php print $seluid; ?>">
	  <input type="hidden" name="current" value="<?php print $action; ?>">
	  <input type="hidden" name="choice" value="<?php print $choice; ?>">
	  <input type="hidden" name="cancel" value="<?php print $choice; ?>">
	</form>

<?php
	require_once('lib/eportfolio_functions.php');
	html_document_drop($User['EPFUsername']['value'],'staff');
?>
  </div>
