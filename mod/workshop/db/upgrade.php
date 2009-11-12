<?php

/**
 * Performs upgrade of the database structure and data
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_workshop_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result = true;

    //===== 1.9.0 upgrade line ======//

    if ($result && $oldversion < 2007101510) {
        $orphans = get_records_sql("SELECT wa.id
                                      FROM {$CFG->prefix}workshop_assessments wa
                                 LEFT JOIN {$CFG->prefix}workshop_submissions ws ON wa.submissionid = ws.id
                                     WHERE ws.id IS NULL");
        if (!empty($orphans)) {
            notify('Orphaned assessment records found - cleaning...');
            foreach (array_keys($orphans) as $waid) {
                $result = $result && delete_records('workshop_assessments', 'id', $waid);
            }
        }
    }

    return $result;
}

?>
