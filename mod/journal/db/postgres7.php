<?PHP // $Id$

function journal_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

    global $CFG;

    $result = true;

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

    if ($oldversion < 2004020500) {
        if ($journals = get_records("journal")) {
            foreach ($journals as $journal) {
                if ($cm = get_coursemodule_from_instance("journal", $journal->id, $journal->course)) {
                    execute_sql("UPDATE {$CFG->prefix}log SET cmid = '$cm->id' 
                                 WHERE module = 'journal' AND url = 'report.php?id=$cm->id'");

                    execute_sql("UPDATE {$CFG->prefix}log SET cmid = '$cm->id' 
                                 WHERE module = 'journal' AND url = 'view.php?id=$cm->id'");
                }
            }
        }
    }

    return $result;
}
