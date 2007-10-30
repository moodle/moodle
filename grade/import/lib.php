<?php  // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards  Martin Dougiamas  http://moodle.com       //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

require_once($CFG->libdir.'/gradelib.php');

/**
 * Returns new improtcode for current user
 * @return int importcode
 */
function get_new_importcode() {
    global $USER;

    $importcode = time();
    while (get_record('grade_import_values', 'importcode', $importcode, 'importer', $USER->id)) {
        $importcode--;
    }

    return $importcode;
}

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
function grade_import_commit($courseid, $importcode, $importfeedback=true, $verbose=true) {
    global $CFG, $USER;

    $commitstart = time(); // start time in case we need to roll back
    $newitemids = array(); // array to hold new grade_item ids from grade_import_newitem table, mapping array

    /// first select distinct new grade_items with this batch

    if ($newitems = get_records_sql("SELECT *
                                       FROM {$CFG->prefix}grade_import_newitem
                                      WHERE importcode = $importcode AND importer={$USER->id}")) {

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
                    if (!$gradeitem->update_final_grade($grade->userid, $grade->finalgrade, 'import', $grade->feedback, FORMAT_MOODLE)) {
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
                                        WHERE importcode = $importcode AND importer={$USER->id} AND itemid > 0")) {

        $modifieditems = array();

        foreach ($gradeitems as $itemid=>$notused) {

            if (!$gradeitem = new grade_item(array('id'=>$itemid))) {
                // not supposed to happen, but just in case
                import_cleanup($importcode);
                return false;
            }
            // get all grades with this item
            if ($grades = get_records('grade_import_values', 'itemid', $itemid)) {

                // make the grades array for update_grade
                foreach ($grades as $grade) {
                    if (!$importfeedback) {
                        $grade->feedback = false; // ignore it
                    }
                    if (!$gradeitem->update_final_grade($grade->userid, $grade->finalgrade, 'import', $grade->feedback)) {
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

    if ($verbose) {
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
    global $USER;

    // remove entries from buffer table
    delete_records('grade_import_values', 'importcode', $importcode, 'importer', $USER->id);
    delete_records('grade_import_newitem', 'importcode', $importcode, 'importer', $USER->id);
}

?>
