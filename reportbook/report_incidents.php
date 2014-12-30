<?php
/**			       		report_incidents.php
 */

$action='report_incidents_list.php';
$choice='report_incidents.php';

//last week by default
$todate=date('Y-m-d',mktime(0,0,0,date('m'),date('d')-7,date('Y')));

three_buttonmenu();
?>
    <div class="content">
        <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
            <div class="center">
                <fieldset class="divgroup">
                    <h5><?php print_string('collateforstudentsfrom',$book);?></h5>
<?php
				$required='yes'; include('scripts/'.$listgroup);
?>
				<div class='right'>
<?php
					$listtype='section';
					$listname='secid';
					$listlabel='section';
					$listlabelstyle='eternal';
					include('scripts/list_section.php');
?>
				</div>
                </fieldset>
            </div>
            <div class="center">
                <fieldset class="divgroup">
	            <div class="left">
                    <h5><?php print_string('collatesince',$book);?></h5>
                    <?php include('scripts/jsdate-form.php'); ?>
		    </div>
		     <div class="right">
                    <h5><?php print_string('collateuntil',$book);?></h5>
                    <?php $required='no'; unset($todate); include('scripts/jsdate-form.php'); ?>
		    </div>
                </fieldset>
            </div>
            <div class="right">
                <fieldset class="divgroup">
                    <h5><?php print_string('sanction');?></h5>
<?php 
                        $listlabel='sanction'; $required='no';
                        $listid='sanction';$cattype='inc';$groupby='name';
                        include('scripts/list_category.php');
?>
                </fieldset>
            </div>
            <div class="left">
            <fieldset class="divgroup" >
                <h5><?php print_string('limittoonesubject');?></h5>
                <?php
                  $required='no';
                  $selbid='%';
                  include('scripts/list_subjects.php');
                ?>
            </fieldset>
            </div>
            <input type="hidden" name="cancel" value="<?php print ''; ?>">
            <input type="hidden" name="current" value="<?php print $action; ?>">
            <input type="hidden" name="choice" value="<?php print $choice; ?>">
        </form>  
    </div>
