<?php  // $Id$

/**
 * given an import code, commits all entries in buffer tables
 * (grade_import_value and grade_import_newitem)
 * If this function is called, we assume that all data collected
 * up to this point is fine and we can go ahead and commit
 * @param int courseid - id of the course
 * @param string importcode - import batch identifier
 * @param feedback print feedback and continue button
 * @return bool success
 */
function grade_import_commit($courseid, $importcode, $feedback=true) {
    global $CFG;

    include_once($CFG->libdir.'/gradelib.php');
    include_once($CFG->libdir.'/grade/grade_item.php');
    $commitstart = time(); // start time in case we need to roll back
    $newitemids = array(); // array to hold new grade_item ids from grade_import_newitem table, mapping array

    /// first select distinct new grade_items with this batch

    if ($newitems = get_records_sql("SELECT *
                                     FROM {$CFG->prefix}grade_import_newitem
                                     WHERE import_code = $importcode")) {

        // instances of the new grade_items created, cached
        // in case grade_update fails, so that we can remove them
        $instances = array();
        $failed = false;
        foreach ($newitems as $newitem) {
            // get all grades with this item

            if ($grades = get_records('grade_import_values', 'newgradeitem', $newitem->id)) {
                /// create a new grade item for this - must use false as second param!
                /// TODO: we need some bounds here too
                $gradeitem = new grade_item(array('courseid'=>$courseid, 'itemtype'=>'manual', 'itemname'=>$newitem->itemname), false);
                $gradeitem->insert('import');
                $instances[] = $gradeitem;

                // insert each individual grade to this new grade item
                foreach ($grades as $grade) {
                    if (!$gradeitem->update_final_grade($grade->userid, $grade->finalgrade, 'import', NULL, $grade->feedback)) {
                        $failed = true;
                        break 2;
                    }
                }
            }
        }

        if ($failed) {
            foreach ($instances as $instance) {
                $gradeitem->delete('import');
            }
            import_cleanup($importcode);
            return false;
        }
    }

    /// then find all existing items

    if ($gradeitems = get_records_sql("SELECT DISTINCT (itemid)
                                       FROM {$CFG->prefix}grade_import_values
                                       WHERE import_code = $importcode
                                       AND itemid > 0")) {

        $modifieditems = array();

        foreach ($gradeitems as $itemid=>$iteminfo) {

            if (!$gradeitem = new grade_item(array('id'=>$itemid))) {
                // not supposed to happen, but just in case
                import_cleanup($importcode);
                return false;
            }
            // get all grades with this item
            if ($grades = get_records('grade_import_values', 'itemid', $itemid)) {

                // make the grades array for update_grade
                foreach ($grades as $grade) {
                    if (!$gradeitem->update_final_grade($grade->userid, $grade->finalgrade, 'import', NULL, $grade->feedback)) {
                        $failed = 1;
                        break 2;
                    }
                }
                //$itemdetails -> idnumber = $gradeitem->idnumber;
                $modifieditems[] = $itemid;

            }

            if (!empty($failed)) {
                import_cleanup($importcode);
                return false;
            }
        }
    }

    if ($feedback) {
        notify(get_string('importsuccess', 'grades'), 'notifysuccess');
        print_continue($CFG->wwwroot.'/grade/index.php?id='.$courseid);
    }
    // clean up
    import_cleanup($importcode);

    return true;
}

/**
 * removes entries from grade import buffer tables grade_import_value and grade_import_newitem
 * after a successful import, or during an import abort
 * @param string importcode - import batch identifier
 */
function import_cleanup($importcode) {
    // remove entries from buffer table
    delete_records('grade_import_values', 'import_code', $importcode);
    delete_records('grade_import_newitem', 'import_code', $importcode);
}

/// Returns the file as one big long string
function my_file_get_contents($filename, $use_include_path = 0) {

    $data = "";
    $file = @fopen($filename, "rb", $use_include_path);
    if ($file) {
        while (!feof($file)) {
            $data .= fread($file, 1024);
        }
        fclose($file);
    }
    return $data;
}
?>
