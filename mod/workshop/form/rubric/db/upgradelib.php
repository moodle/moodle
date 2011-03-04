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
 * @package    workshopform
 * @subpackage rubric
 * @copyright  2010 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
    workshopform_rubric_upgrade_legacy_rubric();
}

/**
 * Upgrades legacy workshops using criterion grading strategy
 */
function workshopform_rubric_upgrade_legacy_criterion() {
    global $CFG, $DB, $OUTPUT;
    require_once($CFG->dirroot . '/mod/workshop/db/upgradelib.php');

    // get the list of all legacy workshops using this grading strategy
    if ($legacyworkshops = $DB->get_records('workshop_old', array('gradingstrategy' => 3), 'course,id', 'id')) {
        echo $OUTPUT->notification('Copying criterion assessment form elements', 'notifysuccess');
        $legacyworkshops = array_keys($legacyworkshops);
        // get the list of all form elements
        list($workshopids, $params) = $DB->get_in_or_equal($legacyworkshops, SQL_PARAMS_NAMED);
        $sql = "SELECT *
                  FROM {workshop_elements_old}
                 WHERE workshopid $workshopids
                       AND newid IS NULL";
        $rs = $DB->get_recordset_sql($sql, $params);
        $newdimensionids = array(); // (int)workshopid => (int)dimensionid
        foreach ($rs as $old) {
            // create rubric criterion and the configuration if necessary
            if (!isset($newdimensionids[$old->workshopid])) {
                if (!$DB->record_exists('workshopform_rubric', array('workshopid' => $old->workshopid, 'sort' => 1))) {
                    $newdimension = new stdclass();
                    $newdimension->workshopid = $old->workshopid;
                    $newdimension->sort = 1;
                    $newdimension->description = trim(get_string('dimensionnumber', 'workshopform_rubric', ''));
                    $newdimension->descriptionformat = FORMAT_HTML;
                    $newdimensionids[$old->workshopid] = $DB->insert_record('workshopform_rubric', $newdimension);
                } else {
                    $newdimensionids[$old->workshopid] = $DB->get_field('workshopform_rubric', 'id',
                                                                array('workshopid' => $old->workshopid, 'sort' => 1));
                }
                if (!$DB->record_exists('workshopform_rubric_config', array('workshopid' => $old->workshopid))) {
                    $newconfig = new stdclass();
                    $newconfig->workshopid = $old->workshopid;
                    $newconfig->layout = 'list';
                    $DB->insert_record('workshopform_rubric_config', $newconfig);
                }
            }
            // process the information about the criterion levels
            if (trim($old->description)) {
                $new = workshopform_rubric_upgrade_criterion_level($old, $newdimensionids[$old->workshopid]);
                $newid = $DB->insert_record('workshopform_rubric_levels', $new);
                $DB->set_field('workshop_elements_old', 'newplugin', 'rubric_levels', array('id' => $old->id));
                $DB->set_field('workshop_elements_old', 'newid', $newid, array('id' => $old->id));
            }
        }
        $rs->close();

        // reload the mappings - this must be reloaded to that we can run this during recovery
        $newelementids = workshop_upgrade_element_id_mappings('rubric_levels');

        // migrate all grades for these elements (i.e. the values that reviewers put into forms)
        echo $OUTPUT->notification('Copying criterion assessment form grades', 'notifysuccess');
        $sql = "SELECT *
                  FROM {workshop_grades_old}
                 WHERE workshopid $workshopids
                       AND elementno = 0
                       AND newid IS NULL";
        $rs = $DB->get_recordset_sql($sql, $params);
        $newassessmentids = workshop_upgrade_assessment_id_mappings();
        $newdimensionids = $DB->get_records('workshopform_rubric_levels', array(), '', 'id,dimensionid');
        foreach ($rs as $old) {
            if (!isset($newassessmentids[$old->assessmentid])) {
                // orphaned grade - the assessment was removed but the grade remained
                continue;
            }
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
 * Upgrades legacy workshops using rubric grading strategy
 */
function workshopform_rubric_upgrade_legacy_rubric() {
    global $CFG, $DB, $OUTPUT;
    require_once($CFG->dirroot . '/mod/workshop/db/upgradelib.php');

    // get the list of all legacy workshops using this grading strategy
    if ($legacyworkshops = $DB->get_records('workshop_old', array('gradingstrategy' => 4), 'course,id', 'id')) {
        echo $OUTPUT->notification('Copying rubric assessment form elements', 'notifysuccess');
        $legacyworkshops = array_keys($legacyworkshops);
        // get the list of all form elements and rubrics
        list($workshopids, $params) = $DB->get_in_or_equal($legacyworkshops, SQL_PARAMS_NAMED);
        $sql = "SELECT e.id AS eid, e.workshopid AS workshopid, e.elementno AS esort, e.description AS edesc, e.weight AS eweight,
                       r.id AS rid, r.rubricno AS rgrade, r.description AS rdesc
                  FROM {workshop_elements_old} e
             LEFT JOIN {workshop_rubrics_old} r ON (r.elementno = e.elementno AND r.workshopid = e.workshopid)
                 WHERE e.workshopid $workshopids
                       AND e.newid IS NULL
                       AND r.newid IS NULL
              ORDER BY e.workshopid, e.elementno, r.rubricno";
        $rs = $DB->get_recordset_sql($sql, $params);
        $newdimensionids    = array();  // (int)workshopid => (int)elementno => (int)dimensionid
        $newlevelids        = array();  // (int)oldrubricid => (int)newlevelid
        $prevelement        = null;
        foreach ($rs as $old) {
            // create rubric criterion and the configuration if necessary
            if (!isset($newdimensionids[$old->workshopid]) or !isset($newdimensionids[$old->workshopid][$old->esort])) {
                if (!$DB->record_exists('workshopform_rubric', array('workshopid' => $old->workshopid, 'sort' => $old->esort))) {
                    $newdimension = new stdclass();
                    $newdimension->workshopid = $old->workshopid;
                    $newdimension->sort = $old->esort;
                    $newdimension->description = $old->edesc;
                    $newdimension->descriptionformat = FORMAT_HTML;
                    $newdimensionids[$old->workshopid][$old->esort] = $DB->insert_record('workshopform_rubric', $newdimension);
                } else {
                    $newdimensionids[$old->workshopid][$old->esort] = $DB->get_field('workshopform_rubric', 'id',
                                                                    array('workshopid' => $old->workshopid, 'sort' => $old->esort));
                }
                if (!$DB->record_exists('workshopform_rubric_config', array('workshopid' => $old->workshopid))) {
                    $newconfig = new stdclass();
                    $newconfig->workshopid = $old->workshopid;
                    $newconfig->layout = 'grid';
                    $DB->insert_record('workshopform_rubric_config', $newconfig);
                }
            }
            // process the information about the criterion levels
            if (trim($old->rdesc)) {
                $new = workshopform_rubric_upgrade_rubric_level($old, $newdimensionids[$old->workshopid][$old->esort]);
                $newid = $DB->insert_record('workshopform_rubric_levels', $new);
                $DB->set_field('workshop_rubrics_old', 'newplugin', 'rubric_levels', array('id' => $old->rid));
                $DB->set_field('workshop_rubrics_old', 'newid', $newid, array('id' => $old->rid));
            }
            // mark the whole element as processed if the last level was processed
            if ($old->rgrade == 4) {
                $DB->set_field('workshop_elements_old', 'newplugin', 'rubric', array('id' => $old->eid));
                $DB->set_field('workshop_elements_old', 'newid', $newdimensionids[$old->workshopid][$old->esort], array('id' => $old->eid));
            }
        }
        $rs->close();

        // reload the mappings - this must be reloaded so that we can run this during recovery
        $newelementids = workshop_upgrade_element_id_mappings('rubric');

        // load the legacy element weights and multiply the new max grade by it
        echo $OUTPUT->notification('Recalculating rubric assessment form element weights', 'notifysuccess');
        $oldweights = $DB->get_records('workshop_elements_old', array('newplugin' => 'rubric'), '', 'id,workshopid,elementno,weight');
        $newweights = array();
        foreach ($oldweights as $eid => $element) {
            $newweights[$newelementids[$element->workshopid][$element->elementno]->newid] = workshopform_rubric_upgrade_weight($element->weight);
        }
        unset($oldweights);
        unset($element);

        // migrate all grades for these elements (i.e. the values that reviewers put into forms)
        echo $OUTPUT->notification('Copying rubric assessment form grades', 'notifysuccess');
        $sql = "SELECT *
                  FROM {workshop_grades_old}
                 WHERE workshopid $workshopids
                       AND newid IS NULL";
        $rs = $DB->get_recordset_sql($sql, $params);
        $newassessmentids = workshop_upgrade_assessment_id_mappings();
        foreach ($rs as $old) {
            if (!isset($newelementids[$old->workshopid]) or !isset($newelementids[$old->workshopid][$old->elementno])) {
                // orphaned grade - the assessment form element has been removed after the grade was recorded
                continue;
            }
            $new                    = new stdclass();
            $new->assessmentid      = $newassessmentids[$old->assessmentid];
            $new->strategy          = 'rubric';
            $new->dimensionid       = $newelementids[$old->workshopid][$old->elementno]->newid;
            $new->grade             = $old->grade * $newweights[$new->dimensionid];
            $new->peercomment       = $old->feedback;
            $new->peercommentformat = FORMAT_HTML;
            $newid = $DB->insert_record('workshop_grades', $new);
            $DB->set_field('workshop_grades_old', 'newplugin', 'rubric', array('id' => $old->id));
            $DB->set_field('workshop_grades_old', 'newid', $newid, array('id' => $old->id));
        }
        $rs->close();
    }
}

/**
 * Transforms given record from workshop_elements_old into an object to be saved into workshopform_rubric_levels
 *
 * This is used during Criterion 1.9 -> Rubric 2.0 conversion
 *
 * @param stdClass $old legacy record from workshop_elements_old
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
 * Transforms given record into an object to be saved into workshopform_rubric_levels
 *
 * This is used during Rubric 1.9 -> Rubric 2.0 conversion
 *
 * @param stdClass $old legacy record from joined workshop_elements_old + workshop_rubrics_old
 * @param int $newdimensionid id of the new workshopform_rubric dimension record to be linked to
 * @return stdclass to be saved in workshopform_rubric_levels
 */
function workshopform_rubric_upgrade_rubric_level(stdclass $old, $newdimensionid) {
    $new = new stdclass();
    $new->dimensionid = $newdimensionid;
    $new->grade = $old->rgrade * workshopform_rubric_upgrade_weight($old->eweight);
    $new->definition = $old->rdesc;
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

/**
 * Given old workshop element weight, returns the weight multiplier
 *
 * Negative weights are not supported any more and are replaced with weight = 0.
 * Legacy workshop did not store the raw weight but the index in the array
 * of weights (see $WORKSHOP_EWEIGHTS in workshop 1.x). workshop 2.0 uses
 * integer weights only (0-16) so all previous weights are multiplied by 4.
 *
 * @param $oldweight index in legacy $WORKSHOP_EWEIGHTS
 * @return int new weight
 */
function workshopform_rubric_upgrade_weight($oldweight) {

    switch ($oldweight) {
        case 8: $weight = 1; break;
        case 9: $weight = 2; break;
        case 10: $weight = 3; break;
        case 11: $weight = 4; break;
        case 12: $weight = 6; break;
        case 13: $weight = 8; break;
        case 14: $weight = 16; break;
        default: $weight = 0;
    }
    return $weight;
}
