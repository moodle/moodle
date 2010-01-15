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
 * Functions used by some stages of rubric grading upgrade
 *
 * @package   workshopform_rubric
 * @copyright 2010 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Check if there are some legacy workshop 1.x data to be migrated and upgrade them
 *
 * This must be called after workshop core migration has finished so that
 * all assessments are already upgraded and tables are correctly renamed.
 */
function workshopform_rubric_upgrade_legacy() {

    if (!workshopform_rubric_upgrade_legacy_needed()) {
        return;
    }
    workshopform_rubric_upgrade_legacy_criterion();
    // todo workshopform_rubric_upgrade_legacy_rubric();
}

/**
 * Upgrades legacy workshops using criterion grading strategy
 */
function workshopform_rubric_upgrade_legacy_criterion() {
    global $CFG, $DB, $OUTPUT;
    require_once($CFG->dirroot . '/mod/workshop/db/upgradelib.php');

    // get the list of all legacy workshops using this grading strategy
    if ($legacyworkshops = $DB->get_records('workshop_old', array('gradingstrategy' => 3), 'course,id', 'id')) {
        echo $OUTPUT->notification('Copying assessment forms elements', 'notifysuccess');
        $legacyworkshops = array_keys($legacyworkshops);
        // get the list of all form elements
        list($workshopids, $params) = $DB->get_in_or_equal($legacyworkshops, SQL_PARAMS_NAMED);
        $sql = "SELECT *
                  FROM {workshop_elements_old}
                 WHERE workshopid $workshopids
                       AND newid IS NULL";
        $rs = $DB->get_recordset_sql($sql, $params);
        $newworkshopids = workshop_upgrade_workshop_id_mappings();
        $newdimensionids = array(); // (int)oldworkshopid => (int)dimensionid
        foreach ($rs as $old) {
            // create rubric criterion and the configuration if necessary
            if (!isset($newdimensionids[$old->workshopid])) {
                if (!$DB->record_exists('workshopform_rubric', array('workshopid' => $newworkshopids[$old->workshopid], 'sort' => 1))) {
                    $newdimension = new stdclass();
                    $newdimenison->workshopid = $newworkshopids[$old->workshopid];
                    $newdimenison->sort = 1;
                    $newdimenison->description = trim(get_string('dimensionnumber', 'workshopform_rubric', ''));
                    $newdimenison->descriptionformat = FORMAT_HTML;
                    $newdimensionids[$old->workshopid] = $DB->insert_record('workshopform_rubric', $newdimenison);
                } else {
                    $newdimensionids[$old->workshopid] = $DB->get_field('workshopform_rubric', 'id',
                                                                array('workshopid' => $newworkshopids[$old->workshopid], 'sort' => 1));
                }
                if (!$DB->record_exists('workshopform_rubric_config', array('workshopid' => $newworkshopids[$old->workshopid]))) {
                    $newconfig = new stdclass();
                    $newconfig->workshopid = $newworkshopids[$old->workshopid];
                    $newconfig->layout = 'list';
                    $DB->insert_record('workshopform_rubric_config', $newconfig);
                }
            }
            // process the information about the criterion levels
            if (trim($old->description)) {
                $new = workshopform_rubric_upgrade_criterion_level($old, $newdimensionids[$old->workshopid]);
                $newid = $DB->insert_record('workshopform_rubric_levels', $new);
            }
            $DB->set_field('workshop_elements_old', 'newplugin', 'rubric_levels', array('id' => $old->id));
            $DB->set_field('workshop_elements_old', 'newid', $newid, array('id' => $old->id));
        }
        $rs->close();

        // reload the mappings - this must be reloaded to that we can run this during recovery
        $newelementids = workshop_upgrade_element_id_mappings('rubric_levels');

        // migrate all grades for these elements (it est the values that reviewers put into forms)
        $sql = "SELECT *
                  FROM {workshop_grades_old}
                 WHERE workshopid $workshopids
                       AND elementno = 0
                       AND newid IS NULL";
        $rs = $DB->get_recordset_sql($sql, $params);
        $newassessmentids = workshop_upgrade_assessment_id_mappings();
        $newdimensionids = $DB->get_records('workshopform_rubric_levels', array(), '', 'id,dimensionid');
        foreach ($rs as $old) {
            if (!isset($newelementids[$old->workshopid]) or !isset($newelementids[$old->workshopid][$old->elementno])) {
                // orphaned grade - the assessment form element has been removed after the grade was recorded
                continue;
            }
            $newlevelid = $newelementids[$old->workshopid][$old->elementno]->newid;
            $new                = new stdclass();
            $new->assessmentid  = $newassessmentids[$old->assessmentid];
            $new->strategy      = 'rubric';
            $new->dimensionid   = $newdimensionids[$newlevelid]->dimensionid;
            $new->grade         = $newelementids[$old->workshopid][$old->grade]->maxscore;
            $newid = $DB->insert_record('workshop_grades', $new);
            $DB->set_field('workshop_grades_old', 'newplugin', 'rubric', array('id' => $old->id));
            $DB->set_field('workshop_grades_old', 'newid', $newid, array('id' => $old->id));
        }
        $rs->close();
    }
}

/**
 * Transforms a given record from workshop_elements_old into an object to be saved into workshopform_rubric_levels
 *
 * This is used during Criterion -> Rubric conversion
 *
 * @param stdclass $old legacy record from workshop_elements_old
 * @param int $newdimensionid id of the new workshopform_rubric dimension record to be linked to
 * @return stdclass to be saved in workshopform_rubric_levels
 */
function workshopform_rubric_upgrade_criterion_level(stdclass $old, $newdimensionid) {
    $new = new stdclass();
    $new->dimensionid = $newdimensionid;
    $new->grade = $old->maxscore;
    $new->definition = $old->description;
    $new->definitionformat = FORMAT_HTML;
    return $new;
}

/**
 * Check if the the migration from legacy workshop 1.9 is needed
 *
 * @return bool
 */
function workshopform_rubric_upgrade_legacy_needed() {
    global $CFG, $DB;

    $dbman = $DB->get_manager();
    if (!($dbman->table_exists('workshop_elements_old') and $dbman->table_exists('workshop_grades_old'))) {
        return false;
    }
    return true;
}
