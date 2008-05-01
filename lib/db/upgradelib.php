<?php  //$Id$

/*
 * This file is used for special upgrade functions - for example groups and gradebook.
 * These functions must use SQL and database related functions only- no other Moodle API,
 * because it might depend on db structures that are not yet present during upgrade.
 * (Do not use functions from accesslib.php, grades classes or group functions at all!)
 */

function upgrade_fix_category_depths() {
    global $CFG, $db;

    // first fix incorrect parents
    $sql = "SELECT c.id
              FROM {$CFG->prefix}course_categories c
             WHERE c.parent > 0 AND c.parent NOT IN (SELECT pc.id FROM {$CFG->prefix}course_categories pc)";
    if ($rs = get_recordset_sql($sql)) {
        while ($cat = rs_fetch_next_record($rs)) {
            $cat->depth  = 1;
            $cat->path   = '/'.$cat->id;
            $cat->parent = 0;
            update_record('course_categories', $cat);
        }
        rs_close($rs);
    }

    // now add path and depth to top level categories
    $sql = "UPDATE {$CFG->prefix}course_categories
               SET depth = 1, path = ".sql_concat("'/'", "id")."
             WHERE parent = 0";
    execute_sql($sql);

    // now fix all other levels - slow but works in all supported dbs
    $parentdepth = 1;
    $db->debug = true;
    while (record_exists('course_categories', 'depth', 0)) {
        $sql = "SELECT c.id, pc.path
                  FROM {$CFG->prefix}course_categories c, {$CFG->prefix}course_categories pc
                 WHERE c.parent=pc.id AND c.depth=0 AND pc.depth=$parentdepth";
        if ($rs = get_recordset_sql($sql)) {
            while ($cat = rs_fetch_next_record($rs)) {
                $cat->depth = $parentdepth+1;
                $cat->path  = $cat->path.'/'.$cat->id;
                update_record('course_categories', $cat);
            }
            rs_close($rs);
        }
        $parentdepth++;
        if ($parentdepth > 100) {
            //something must have gone wrong - nobody can have more than 100 levels of categories, right?
            debugging('Unknown error fixing category depths');
            break;
        }
    }
    $db->debug = true;
}

?>
