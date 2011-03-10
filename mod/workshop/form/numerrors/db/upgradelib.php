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
 * Functions used by some stages of number of errors grading upgrade
 *
 * @package    workshopform
 * @subpackage numerrors
 * @copyright  2010 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir.'/gradelib.php'); // grade_floatval() called here

/**
 * Check if there are some legacy workshop 1.x data to be migrated and upgrade them
 *
 * This must be called after workshop core migration has finished so that
 * all assessments are already upgraded and tables are correctly renamed.
 */
function workshopform_numerrors_upgrade_legacy() {
    global $CFG, $DB, $OUTPUT;
    require_once($CFG->dirroot . '/mod/workshop/db/upgradelib.php');

    if (!workshopform_numerrors_upgrade_legacy_needed()) {
        return;
    }

    // get the list of all legacy workshops using this grading strategy
    if ($legacyworkshops = $DB->get_records('workshop_old', array('gradingstrategy' => 2), 'course,id', 'id')) {
        echo $OUTPUT->notification('Copying assessment forms elements and grade mappings', 'notifysuccess');
        $legacyworkshops = array_keys($legacyworkshops);
        // get some needed info about the workshops
        $workshopinfos = $DB->get_records_list('workshop_old', 'id', $legacyworkshops, 'id', 'id,grade');
        // get the list of all form elements
        list($workshopids, $params) = $DB->get_in_or_equal($legacyworkshops, SQL_PARAMS_NAMED);
        $sql = "SELECT *
                  FROM {workshop_elements_old}
                 WHERE workshopid $workshopids
                       AND newid IS NULL";
        $rs = $DB->get_recordset_sql($sql, $params);
        foreach ($rs as $old) {
            // process the information about mapping
            $newmapping = new stdclass();
            $newmapping->workshopid = $old->workshopid;
            $newmapping->nonegative = $old->elementno;
            $newmapping->grade = $old->maxscore;
            if ($old->maxscore > 0) {
                $newmapping->grade = grade_floatval($old->maxscore / $workshopinfos[$old->workshopid]->grade * 100);
            } else {
                $newmapping->grade = 0;
            }
            $DB->delete_records('workshopform_numerrors_map',
                                array('workshopid' => $newmapping->workshopid, 'nonegative' => $newmapping->nonegative));
            $DB->insert_record('workshopform_numerrors_map', $newmapping);
            // process the information about the element itself
            if (trim($old->description) and $old->description <> '@@ GRADE_MAPPING_ELEMENT @@') {
                $new = workshopform_numerrors_upgrade_element($old, $old->workshopid);
                $newid = $DB->insert_record('workshopform_numerrors', $new);
            } else {
                $newid = 0;
            }
            $DB->set_field('workshop_elements_old', 'newplugin', 'numerrors', array('id' => $old->id));
            $DB->set_field('workshop_elements_old', 'newid', $newid, array('id' => $old->id));
        }
        $rs->close();

        // now we need to reload the legacy ids. Although we have them in $newelements after the first run, we must
        // refetch them from DB so that this function can be called during recovery
        $newelementids = workshop_upgrade_element_id_mappings('numerrors');

        // migrate all grades for these elements (i.e. the values that reviewers put into forms)
        echo $OUTPUT->notification('Copying assessment form grades', 'notifysuccess');
        $sql = "SELECT *
                  FROM {workshop_grades_old}
                 WHERE workshopid $workshopids
                       AND newid IS NULL";
        $rs = $DB->get_recordset_sql($sql, $params);
        $newassessmentids = workshop_upgrade_assessment_id_mappings();
        foreach ($rs as $old) {
            if (!isset($newassessmentids[$old->assessmentid])) {
                // orphaned grade - the assessment was removed but the grade remained
                continue;
            }
            if (!isset($newelementids[$old->workshopid]) or !isset($newelementids[$old->workshopid][$old->elementno])) {
                // orphaned grade - the assessment form element has been removed after the grade was recorded
                continue;
            }
            $newelementinfo = $newelementids[$old->workshopid][$old->elementno];
            if ($newelementinfo->newid == 0 or $old->feedback == '@@ GRADE_ADJUSTMENT @@') {
                // this is not a real grade - it was used just for mapping purposes
                $DB->set_field('workshop_grades_old', 'newplugin', 'numerrors_map', array('id' => $old->id));
                $DB->set_field('workshop_grades_old', 'newid', 0, array('id' => $old->id));
                continue;
            }
            $new = workshopform_numerrors_upgrade_grade($old, $newassessmentids[$old->assessmentid],
                                                         $newelementids[$old->workshopid][$old->elementno]);
            $newid = $DB->insert_record('workshop_grades', $new);
            $DB->set_field('workshop_grades_old', 'newplugin', 'numerrors', array('id' => $old->id));
            $DB->set_field('workshop_grades_old', 'newid', $newid, array('id' => $old->id));
        }
        $rs->close();
    }
}

/**
 * Transforms a given record from workshop_elements_old into an object to be saved into workshopform_numerrors
 *
 * @param stdClass $old legacy record from workshop_elements_old
 * @param int $newworkshopid id of the new workshop instance that replaced the previous one
 * @return stdclass to be saved in workshopform_numerrors
 */
function workshopform_numerrors_upgrade_element(stdclass $old, $newworkshopid) {
    $new = new stdclass();
    $new->workshopid = $newworkshopid;
    $new->sort = $old->elementno;
    $new->description = $old->description;
    $new->descriptionformat = FORMAT_HTML;
    $new->grade0 = get_string('grade0default', 'workshopform_numerrors');
    $new->grade1 = get_string('grade1default', 'workshopform_numerrors');
    // calculate new weight of the element. Negative weights are not supported any more and
    // are replaced with weight = 0. Legacy workshop did not store the raw weight but the index
    // in the array of weights (see $WORKSHOP_EWEIGHTS in workshop 1.x)
    // workshop 2.0 uses integer weights only (0-16) so all previous weights are multiplied by 4.
    switch ($old->weight) {
        case 8: $new->weight = 1; break;
        case 9: $new->weight = 2; break;
        case 10: $new->weight = 3; break;
        case 11: $new->weight = 4; break;
        case 12: $new->weight = 6; break;
        case 13: $new->weight = 8; break;
        case 14: $new->weight = 16; break;
        default: $new->weight = 0;
    }
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
function workshopform_numerrors_upgrade_grade(stdclass $old, $newassessmentid, stdclass $newdimensioninfo) {
    $new                    = new stdclass();
    $new->assessmentid      = $newassessmentid;
    $new->strategy          = 'numerrors';
    $new->dimensionid       = $newdimensioninfo->newid;
    $new->grade             = $old->grade;
    $new->peercomment       = $old->feedback;
    $new->peercommentformat = FORMAT_HTML;
    return $new;
}

/**
 * Check if the the migration from legacy workshop 1.9 is needed
 *
 * @return bool
 */
function workshopform_numerrors_upgrade_legacy_needed() {
    global $CFG, $DB;

    $dbman = $DB->get_manager();
    if (!($dbman->table_exists('workshop_elements_old') and $dbman->table_exists('workshop_grades_old'))) {
        return false;
    }
    return true;
}
