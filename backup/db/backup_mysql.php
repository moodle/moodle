<?PHP  //$Id$
//
// This file keeps track of upgrades to Moodle's
// backup/restore utility.
// 
// Sometimes, changes between versions involve 
// alterations to database structures and other 
// major things that may break installations.  
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installtion to the current version.
//
// If there's something it cannot do itself, it 
// will tell you what you need to do.
//
// Versions are defined by backup_version.php
//
// This file is tailored to MySQL

function backup_upgrade($oldversion=0) {

    global $CFG;

    $result = true;

    if ($oldversion < 2003050300 and $result) {
        $result = execute_sql("CREATE TABLE `{$CFG->prefix}backup_ids` (
                        `backup_code` INT(12) UNSIGNED NOT NULL, 
                        `table_name` VARCHAR(30) NOT NULL, 
                        `old_id` INT(10) UNSIGNED NOT NULL, 
                        `new_id` INT(10) UNSIGNED,
                         PRIMARY KEY (`backup_code`, `table_name`, `old_id`)
                     )
                     COMMENT = 'To store and convert ids in backup/restore'");
    }

    if ($oldversion < 2003050301 and $result) {
        $result = execute_sql("ALTER TABLE `{$CFG->prefix}backup_ids`
                         ADD `time` INT(10) UNSIGNED");
    } 


    //Finally, return result
    return $result;

}

?>
