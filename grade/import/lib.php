<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

require_once($CFG->libdir.'/gradelib.php');

/**
 * Returns new improtcode for current user
 * @return int importcode
 */
function get_new_importcode() {
    global $USER, $DB;

    $importcode = time();
    while ($DB->get_record('grade_import_values', array('importcode' => $importcode, 'importer' => $USER->id))) {
        $importcode--;
    }

    return $importcode;
}

/**
 * given an import code, commits all entries in buffer tables
 * (grade_import_value and grade_import_newitem)
 * If this function is called, we assume that all data collected
 * up to this point is fine and we can go ahead and commit
 * @param int $courseid - ID of the course.
 * @param int $importcode - Import batch identifier.
 * @param bool $importfeedback - Whether to import feedback as well.
 * @param bool $verbose - Print feedback and continue button.
 * @return bool success
 */
function grade_import_commit($courseid, $importcode, $importfeedback=true, $verbose=true) {
    global $CFG, $USER, $DB, $OUTPUT;

    $failed = false;
    $executionerrors = false;
    $commitstart = time(); // start time in case we need to roll back
    $newitemids = array(); // array to hold new grade_item ids from grade_import_newitem table, mapping array

    /// first select distinct new grade_items with this batch
    $params = array($importcode, $USER->id);
    if ($newitems = $DB->get_records_sql("SELECT *
                                           FROM {grade_import_newitem}
                                          WHERE importcode = ? AND importer=?", $params)) {

        // instances of the new grade_items created, cached
        // in case grade_update fails, so that we can remove them
        $instances = array();
        foreach ($newitems as $newitem) {
            // get all grades with this item

            $gradeimportparams = array('newgradeitem' => $newitem->id, 'importcode' => $importcode, 'importer' => $USER->id);
            if ($grades = $DB->get_records('grade_import_values', $gradeimportparams)) {
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

    if ($gradeitems = $DB->get_records_sql("SELECT DISTINCT (itemid)
                                             FROM {grade_import_values}
                                            WHERE importcode = ? AND importer=? AND itemid > 0",
                                            array($importcode, $USER->id))) {

        $modifieditems = array();

        foreach ($gradeitems as $itemid=>$notused) {

            if (!$gradeitem = new grade_item(array('id'=>$itemid))) {
                // not supposed to happen, but just in case
                import_cleanup($importcode);
                return false;
            }
            // get all grades with this item
            $gradeimportparams = array('itemid' => $itemid, 'importcode' => $importcode, 'importer' => $USER->id);
            if ($grades = $DB->get_records('grade_import_values', $gradeimportparams)) {

                // make the grades array for update_grade
                foreach ($grades as $grade) {
                    if (!$importfeedback || $grade->feedback === null) {
                        $grade->feedback = false; // ignore it
                    }
                    if ($grade->importonlyfeedback) {
                        // False means do not change. See grade_itme::update_final_grade().
                        $grade->finalgrade = false;
                    }
                    if (!$gradeitem->update_final_grade($grade->userid, $grade->finalgrade, 'import', $grade->feedback)) {
                        $errordata = new stdClass();
                        $errordata->itemname = $gradeitem->itemname;
                        $errordata->userid = $grade->userid;
                        $executionerrors[] = get_string('errorsettinggrade', 'grades', $errordata);
                        $failed = true;
                        break 2;
                    }
                }
                //$itemdetails -> idnumber = $gradeitem->idnumber;
                $modifieditems[] = $itemid;

            }
        }

        if ($failed) {
            if ($executionerrors && $verbose) {
                echo $OUTPUT->notification(get_string('gradeimportfailed', 'grades'));
                foreach ($executionerrors as $errorstr) {
                    echo $OUTPUT->notification($errorstr);
                }
            }
            import_cleanup($importcode);
            return false;
        }
    }

    if ($verbose) {
        echo $OUTPUT->notification(get_string('importsuccess', 'grades'), 'notifysuccess');
        $unenrolledusers = get_unenrolled_users_in_import($importcode, $courseid);
        if ($unenrolledusers) {
            $list = array();
            foreach ($unenrolledusers as $u) {
                $u->fullname = fullname($u);
                $list[] = get_string('usergrade', 'grades', $u);
            }
            echo $OUTPUT->notification(get_string('unenrolledusersinimport', 'grades', html_writer::alist($list)), 'notifysuccess');
        }
        echo $OUTPUT->continue_button($CFG->wwwroot.'/grade/index.php?id='.$courseid);
    }
    // clean up
    import_cleanup($importcode);

    return true;
}

/**
 * This function returns an array of grades that were included in the import,
 * but where the user does not currently have a graded role on the course. These grades
 * are still stored in the database, but will not be visible in the gradebook unless
 * this user subsequently enrols on the course in a graded roles.
 *
 * The returned objects have fields user firstname, lastname and useridnumber, and gradeidnumber.
 *
 * @param integer $importcode import batch identifier
 * @param integer $courseid the course we are importing to.
 * @return mixed and array of user objects, or false if none.
 */
function get_unenrolled_users_in_import($importcode, $courseid) {
    global $CFG, $DB;

    $coursecontext = context_course::instance($courseid);

    // We want to query both the current context and parent contexts.
    list($relatedctxsql, $relatedctxparams) = $DB->get_in_or_equal($coursecontext->get_parent_context_ids(true), SQL_PARAMS_NAMED, 'relatedctx');

    // Users with a gradeable role.
    list($gradebookrolessql, $gradebookrolesparams) = $DB->get_in_or_equal(explode(',', $CFG->gradebookroles), SQL_PARAMS_NAMED, 'grbr');

    // Enrolled users.
    $context = context_course::instance($courseid);
    list($enrolledsql, $enrolledparams) = get_enrolled_sql($context);
    list($sort, $sortparams) = users_order_by_sql('u');

    $sql = "SELECT giv.id, u.firstname, u.lastname, u.idnumber AS useridnumber,
                   COALESCE(gi.idnumber, gin.itemname) AS gradeidnumber
              FROM {grade_import_values} giv
              JOIN {user} u
                   ON giv.userid = u.id
              LEFT JOIN {grade_items} gi
                        ON gi.id = giv.itemid
              LEFT JOIN {grade_import_newitem} gin
                        ON gin.id = giv.newgradeitem
              LEFT JOIN ($enrolledsql) je
                        ON je.id = u.id
              LEFT JOIN {role_assignments} ra
                        ON (giv.userid = ra.userid AND ra.roleid $gradebookrolessql AND ra.contextid $relatedctxsql)
             WHERE giv.importcode = :importcode
                   AND (ra.id IS NULL OR je.id IS NULL)
          ORDER BY gradeidnumber, $sort";
    $params = array_merge($gradebookrolesparams, $enrolledparams, $sortparams, $relatedctxparams);
    $params['importcode'] = $importcode;

    return $DB->get_records_sql($sql, $params);
}

/**
 * removes entries from grade import buffer tables grade_import_value and grade_import_newitem
 * after a successful import, or during an import abort
 * @param string importcode - import batch identifier
 */
function import_cleanup($importcode) {
    global $USER, $DB;

    // remove entries from buffer table
    $DB->delete_records('grade_import_values', array('importcode' => $importcode, 'importer' => $USER->id));
    $DB->delete_records('grade_import_newitem', array('importcode' => $importcode, 'importer' => $USER->id));
}


