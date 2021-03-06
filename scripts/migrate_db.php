#! /usr/bin/php -q
<?php
/**
 *												 migrate_db.php
 *
 */ 
/* The path is passed as a command line argument. */
function arguments($argv){
	$ARGS=array();
	foreach($argv as $arg){
		if (ereg('--([^=]+)=(.*)',$arg,$reg)){
			$ARGS[$reg[1]]=$reg[2];
			}
		elseif(ereg('-([a-zA-Z0-9])',$arg,$reg)){
			$ARGS[$reg[1]]='true';
			}
		}
	return $ARGS;
	}
$ARGS=arguments($_SERVER['argv']);
require_once($ARGS['path'].'/school.php');
require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_head_options.php');

if(check_class_release()){
    echo "Database has been migrated.";
    }

require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_end_options.php');

?>
