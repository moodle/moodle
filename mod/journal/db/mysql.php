<?PHP // $Id$

function journal_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

    global $CFG;

    $result = true;

    if ($oldversion < 2002081000) {
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

    if ($oldversion < 2003081701) {
        table_column("journal", "assessed", "assessed", "integer", "10", "", "0");
        table_column("journal_entries", "rating", "rating", "integer", "10", "", "0");
    }

    if ($oldversion < 2003081705) {
        $defaultscale = NULL;
        $defaultscale->courseid = 0;
        $defaultscale->userid = 0;
        $defaultscale->timemodified = time();
        $defaultscale->name  = get_string("journalrating2", "journal");
        $defaultscale->scale = get_string("journalrating1", "journal").",".
                               get_string("journalrating2", "journal").",".
                               get_string("journalrating3", "journal");

        if ($defaultscale->id = insert_record("scale", $defaultscale)) {
            execute_sql("UPDATE {$CFG->prefix}journal SET assessed = '-$defaultscale->id'", false);
        } else {
            notify("An error occurred while inserting the default journal scale");
            $result = false;
        }
    }

    if ($oldversion < 2004011400) {
        table_column("journal", "", "introformat", "integer", "2", "", "1", "not null", "intro");
    }

    
    return $result;
}

?>
