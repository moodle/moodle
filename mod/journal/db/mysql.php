<?PHP // $Id$

function journal_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

    $result = true;

    if ($oldversion < 20020810) {
       if (! execute_sql("ALTER TABLE `journal_entries` ADD `mailed` TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL")) {
           $result = false;
       }
    }
    if ($oldversion < 2002101200) {
       execute_sql(" ALTER TABLE `journal_entries` ADD `format` TINYINT(2) UNSIGNED DEFAULT '0' NOT NULL AFTER `text` ");
    }
    if ($oldversion < 2002122300) {
        execute_sql("ALTER TABLE `journal_entries` CHANGE `user` `userid` INT(10) UNSIGNED DEFAULT '0' NOT NULL ");
    }

    
    return $result;
}
