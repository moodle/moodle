<?php  //$Id$

/*
 * This file is used for special upgrade functions - for example groups and gradebook.
 * These functions must use SQL and database related functions only- no other Moodle API,
 * because it might depend on db structures that are not yet present during upgrade.
 * (Do not use functions from accesslib.php, grades classes or group functions at all!)
 */

function upgrade_fix_category_depths() {
    global $CFG, $DB;

    // first fix incorrect parents
    $sql = "SELECT c.id
              FROM {course_categories} c
             WHERE c.parent > 0 AND c.parent NOT IN (SELECT pc.id FROM {course_categories} pc)";
    if ($rs = $DB->get_recordset_sql($sql)) {
        foreach ($rs as $cat) {
            $cat->depth  = 1;
            $cat->path   = '/'.$cat->id;
            $cat->parent = 0;
            $DB->update_record('course_categories', $cat);
        }
        $rs->close();
    }

    // now add path and depth to top level categories
    $sql = "UPDATE {course_categories}
               SET depth = 1, path = ".$DB->sql_concat("'/'", "id")."
             WHERE parent = 0";
    $DB->execute($sql);

    // now fix all other levels - slow but works in all supported dbs
    $parentdepth = 1;
    while ($DB->record_exists('course_categories', array('depth'=>0))) {
        $sql = "SELECT c.id, pc.path
                  FROM {course_categories} c, {course_categories} pc
                 WHERE c.parent=pc.id AND c.depth=0 AND pc.depth=?";
        if ($rs = $DB->get_recordset_sql($sql, array($parentdepth))) {
            $DB->set_debug(false);
            foreach ($rs as $cat) {
                $cat->depth = $parentdepth+1;
                $cat->path  = $cat->path.'/'.$cat->id;
                $DB->update_record('course_categories', $cat);
            }
            $rs->close();
            $DB->set_debug(false);
        }
        $parentdepth++;
        if ($parentdepth > 100) {
            //something must have gone wrong - nobody can have more than 100 levels of categories, right?
            debugging('Unknown error fixing category depths');
            break;
        }
    }
}

?>
