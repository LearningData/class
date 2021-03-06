<?php 
/**						   			  orders_limit.php
 *
 */

$action='orders_limit_action.php';

if(isset($_GET['budid'])){$budid=$_GET['budid'];}else{$budid='';}
if(isset($_POST['budid'])){$budid=$_POST['budid'];}
if(isset($_GET['budgetyear'])){$budgetyear=$_GET['budgetyear'];}else{$budgetyear='';}
if(isset($_POST['budgetyear'])){$budgetyear=$_POST['budgetyear'];}

$Budget=fetchBudget($budid);
$users=list_all_users('0');
$perms=array('r'=>1,'w'=>1,'x'=>1);
$xusers=(array)list_budget_users($budid,$perms);
$perms=array('r'=>1,'w'=>1,'x'=>0);
$wusers=(array)list_budget_users($budid,$perms);
$perms=array('r'=>1,'w'=>0,'x'=>0);/* workaround for old permissions */
$wusers=$wusers+list_budget_users($budid,$perms);
$tab=1;

$extrabuttons=array();
if($Budget['overbudid_db']==0){
	$extrabuttons['newbudget']=array('name'=>'current','value'=>'new_budget.php');
	$subbudgets=list_subbudgets($budid);
	}
else{
	$subbudgets=array();
	}
$d_o=mysql_query("SELECT COUNT(id) FROM orderorder WHERE budget_id='$budid'");
if(($_SESSION['role']=='admin' or get_admin_perm('b',$_SESSION['uid'])==1) and mysql_result($d_o,0)==0 and sizeof($subbudgets)==0){
	$extrabuttons['delete']=array('name'=>'sub','value'=>'Delete');
	}
three_buttonmenu($extrabuttons,$book);
?>
  <div id="heading">
  	<h4>
  	 <label><?php print_string('budget',$book); ?></label>
  	 <?php print $Budget['Name']['value'];?>
    </h4>
  </div>

  <div class="content">
  <form name="formtoprocess" id="formtoprocess" method="post" action="<?php print $host; ?>">
	  <fieldset class="divgroup">
		  <label for="<?php print $Budget['Limit']['label'];?>">
			<?php print_string($Budget['Limit']['label'],$book);?>
		  </label>
		  <?php $tab=xmlelement_input($Budget['Limit'],'',$tab,'admin');?>
      <br/><br/>
		  <label for="<?php print $Budget['Name']['label'];?>"> <?php print_string($Budget['Name']['label'],$book);?></label>
		  <?php	$tab=xmlelement_input($Budget['Name'],'',$tab,'admin');?>
	  </fieldset>
		<input type="hidden" name="budgetyear" value="<?php print $budgetyear;?>" />
	    <input type="hidden" name="budid" value="<?php print $budid;?>" /> 
		  <input type="hidden" name="choice" value="<?php print $choice;?>" />
		  <input type="hidden" name="current" value="<?php print $action;?>" />
		  <input type="hidden" name="cancel" value="<?php print $cancel;?>" />
	</form>
	<div class="left divgroup">
	  <fieldset>
  	  <h5><?php print_string('authorise',$book); ?></h5>
      <?php 
        while(list($uid,$user)=each($xusers)){
          $Responsible=array('id_db'=>$budid.'-'.$uid);
      ?>
      <div  id="<?php print $budid.'-'.$uid;?>" class="rowaction" >
        <button title="Remove this responsibility" name="current" value="responsables_edit_budget.php" onClick="clickToAction(this)">
          <?php print $user['username'].' ('.$user['surname'].')';?>
        </button>
        <div id="<?php print 'xml-'.$budid.'-'.$uid;?>" style="display:none;">
          <?php xmlechoer('Responsible',$Responsible);?>
        </div>
      </div>
      <?php
        }
      ?>
      <div class="center">
      <?php 
        $listlabel='allow';
        $listname='xuid';
        $liststyle='width:100%; margin-top: 20px;';
        $listvaluefield='uid';
        $listdescriptionfield='username';
        $onchange='yes';
        include('scripts/set_list_vars.php');
        list_select_list($users,$listoptions,$book);
        unset($listoptions);
      ?>
      </div>
    </fieldset>
  </div>



  <div class="right divgroup">
    <fieldset>
    <h5><?php print_string('order',$book); ?></h5>
    <?php
    	while(list($uid,$user)=each($wusers)){
    		$Responsible=array('id_db'=>$budid.'-'.$uid);
    ?>
    	<div  id="<?php print $budid.'-'.$uid;?>" class="rowaction" >
    	  <button title="Remove this responsibility" name="current" value="responsables_edit_budget.php" onClick="clickToAction(this)">
          <?php print $user['username'].' ('.$user['surname'].')';?>
  		  </button>
  		  <div id="<?php print 'xml-'.$budid.'-'.$uid;?>" style="display:none;">
    		  <?php xmlechoer('Responsible',$Responsible);?>
  		  </div>
  		</div>
      <?php
        }
      ?>
      <div class="center">
      <?php 
        $listlabel='allow';
        $listname='wuid';
        $liststyle='width:100%; margin-top: 20px;';
        $listvaluefield='uid';
        $listdescriptionfield='username';
        $onchange='yes';
        include('scripts/set_list_vars.php');
        list_select_list($users,$listoptions,$book);
        unset($listoptions);
      ?>
      </div>
    </fieldset>
  </div>
  <?php
  	if(sizeof($subbudgets)>0){
  ?>
  <table class="listmenu smalltable center">
		<tr>
		  <th><?php print_string('subbudget','admin');?></th>
		  <th><?php print_string('limit',$book);?></th>
		  <th><?php print_string('currentbalance',$book);?></th>
		  <th><?php print_string('projectedbalance',$book);?></th>
		</tr>
    <?php
      while(list($subindex,$subbudget)=each($subbudgets)){
    ?>
		<tr>
		  <td>
      <?php
        print '<a  href="admin.php?current=orders_list.php&cancel='.$choice.'&choice='.$choice.'&budid='.$subbudget['id'].'&budgetyear='.$budgetyear.'">'.$subbudget['name'].'</a>';
      ?>
		  </td>
		  <td>
      <?php 
        print '<a href="admin.php?current=orders_limit.php&cancel='.
        				$choice.'&choice='. $choice.'&budid='. $subbudget['id'].'">' 
        				.round($subbudget['costlimit'],0).'</a>';
      ?>
		  </td>
		  <td><?php print get_budget_current($subbudget['id']);?></td>
		  <td><?php print get_budget_projected($subbudget['id']);?></td>
		</tr>
  <?php
    }
  ?>
  </table>
  <?php
	 }
  ?>
  </div>
