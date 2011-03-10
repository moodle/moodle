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

/**
 * Functions used by some stages of comments-only grading upgrade
 *
 * @package    workshopform
 * @subpackage comments
 * @copyright  2010 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Check if there are some legacy workshop 1.x data to be migrated and upgrade them
 *
 * This must be called after workshop core migration has finished so that
 * all assessments are already upgraded and tables are correctly renamed.
 */
function workshopform_comments_upgrade_legacy() {
    global $CFG, $DB, $OUTPUT;
    require_once($CFG->dirroot . '/mod/workshop/db/upgradelib.php');

    if (!workshopform_comments_upgrade_legacy_needed()) {
        return;
    }

    // get the list of all legacy workshops using this grading strategy
    if ($legacyworkshops = $DB->get_records('workshop_old', array('gradingstrategy' => 0), 'course,id', 'id')) {
        echo $OUTPUT->notification('Copying assessment forms elements', 'notifysuccess');
        $legacyworkshops = array_keys($legacyworkshops);
        // get the list of all form elements
        list($workshopids, $params) = $DB->get_in_or_equal($legacyworkshops, SQL_PARAMS_NAMED);
        $sql = "SELECT *
                  FROM {workshop_elements_old}
                 WHERE workshopid $workshopids
                       AND newid IS NULL";
        $rs = $DB->get_recordset_sql($sql, $params);
        foreach ($rs as $old) {
            $new = workshopform_comments_upgrade_element($old, $old->workshopid);
            $newid = $DB->insert_record('workshopform_comments', $new);
            $DB->set_field('workshop_elements_old', 'newplugin', 'comments', array('id' => $old->id));
            $DB->set_field('workshop_elements_old', 'newid', $newid, array('id' => $old->id));
        }
        $rs->close();

        // now we need to reload the legacy element ids
        $newelementids = workshop_upgrade_element_id_mappings('comments');

        // migrate all comments for these elements (i.e. the values that reviewers put into forms)
        echo $OUTPUT->notification('Copying assessment form comments', 'notifysuccess');
        $sql = "SELECT *
                  FROM {workshop_grades_old}
                 WHERE workshopid $workshopids
                       AND newid IS NULL";
        $rs = $DB->get_recordset_sql($sql, $params);
        $newassessmentids = workshop_upgrade_assessment_id_mappings();
        foreach ($rs as $old) {
            if (!isset($newassessmentids[$old->assessmentid])) {
                // orphaned comment - the assessment was removed but the grade remained
                continue;
            }
            if (!isset($newelementids[$old->workshopid]) or !isset($newelementids[$old->workshopid][$old->elementno])) {
                // orphaned comment - the assessment form element has been removed after the grade was recorded
                continue;
            }
            $new = workshopform_comments_upgrade_grade($old, $newassessmentids[$old->assessmentid],
                                                         $newelementids[$old->workshopid][$old->elementno]);
            $newid = $DB->insert_record('workshop_grades', $new);
            $DB->set_field('workshop_grades_old', 'newplugin', 'comments', array('id' => $old->id));
            $DB->set_field('workshop_grades_old', 'newid', $newid, array('id' => $old->id));
        }
        $rs->close();
    }
}

/**
 * Transforms a given record from workshop_elements_old into an object to be saved into workshopform_comments
 *
 * @param stdClass $old legacy record from workshop_elements_old
 * @param int $newworkshopid id of the new workshop instance that replaced the previous one
 * @return stdclass to be saved in workshopform_comments
 */
function workshopform_comments_upgrade_element(stdclass $old, $newworkshopid) {
    $new                    = new stdclass();
    $new->workshopid        = $newworkshopid;
    $new->sort              = $old->elementno;
    $new->description       = $old->description;
    $new->descriptionformat = FORMAT_HTML;
    return $new;
}

/**
 * Transforms given grade record
 *
 * @param stdClass $old
 * @param int $newassessmentid
 * @param stdClass $newdimensioninfo
 * @return stdclass
 */
function workshopform_comments_upgrade_grade(stdclass $old, $newassessmentid, stdclass $newdimensioninfo) {
    $new                    = new stdclass();
    $new->assessmentid      = $newassessmentid;
    $new->strategy          = 'comments';
    $new->dimensionid       = $newdimensioninfo->newid;
    $new->grade             = 100.00000;
    $new->peercomment       = $old->feedback;
    $new->peercommentformat = FORMAT_HTML;
    return $new;
}

/**
 * Check if the the migration from legacy workshop 1.9 is needed
 *
 * @return bool
 */
function workshopform_comments_upgrade_legacy_needed() {
    global $CFG, $DB;

    $dbman = $DB->get_manager();
    if (!($dbman->table_exists('workshop_elements_old') and $dbman->table_exists('workshop_grades_old'))) {
        return false;
    }
    return true;
}
