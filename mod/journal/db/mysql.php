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
    
    return $result;
}
