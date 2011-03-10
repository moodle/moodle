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
 * Functions used by some stages of accumulative grading upgrade
 *
 * @package    workshopform
 * @subpackage accumulative
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Check if there are some legacy workshop 1.x data to be migrated and upgrade them
 *
 * This must be called after workshop core migration has finished so that
 * all assessments are already upgraded and tables are correctly renamed.
 */
function workshopform_accumulative_upgrade_legacy() {
    global $CFG, $DB, $OUTPUT;
    require_once($CFG->dirroot . '/mod/workshop/db/upgradelib.php');

    if (!workshopform_accumulative_upgrade_legacy_needed()) {
        return;
    }

    // get the list of all legacy workshops using this grading strategy
    if ($legacyworkshops = $DB->get_records('workshop_old', array('gradingstrategy' => 1), 'course,id', 'id')) {
        echo $OUTPUT->notification('Copying assessment forms elements', 'notifysuccess');
        $legacyworkshops = array_keys($legacyworkshops);
        // get the list of all form elements
        list($workshopids, $params) = $DB->get_in_or_equal($legacyworkshops, SQL_PARAMS_NAMED);
        $sql = "SELECT *
                  FROM {workshop_elements_old}
                 WHERE workshopid $workshopids
                       AND newid IS NULL";
        $rs = $DB->get_recordset_sql($sql, $params);
        // prepare system (global) scales to replace the legacy in-built ones
        $newscaleids = workshopform_accumulative_upgrade_scales();
        foreach ($rs as $old) {
            $new = workshopform_accumulative_upgrade_element($old, $newscaleids, $old->workshopid);
            $newid = $DB->insert_record('workshopform_accumulative', $new);
            $DB->set_field('workshop_elements_old', 'newplugin', 'accumulative', array('id' => $old->id));
            $DB->set_field('workshop_elements_old', 'newid', $newid, array('id' => $old->id));
        }
        $rs->close();

        // now we need to reload the legacy ids. Although we have them in $newelements after the first run, we must
        // refetch them from DB so that this function can be called during recovery
        $newelementids = workshop_upgrade_element_id_mappings('accumulative');

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
            $new = workshopform_accumulative_upgrade_grade($old, $newassessmentids[$old->assessmentid],
                                                         $newelementids[$old->workshopid][$old->elementno]);
            $newid = $DB->insert_record('workshop_grades', $new);
            $DB->set_field('workshop_grades_old', 'newplugin', 'accumulative', array('id' => $old->id));
            $DB->set_field('workshop_grades_old', 'newid', $newid, array('id' => $old->id));
        }
        $rs->close();
    }
}

/**
 * Transforms a given record from workshop_elements_old into an object to be saved into workshopform_accumulative
 *
 * @param stdClass $old legacy record from workshop_elements_old
 * @param array $newscaleids mapping from old scale types into new standard ones
 * @param int $newworkshopid id of the new workshop instance that replaced the previous one
 * @return stdclass to be saved in workshopform_accumulative
 */
function workshopform_accumulative_upgrade_element(stdclass $old, array $newscaleids, $newworkshopid) {
    $new = new stdclass();
    $new->workshopid = $newworkshopid;
    $new->sort = $old->elementno;
    $new->description = $old->description;
    $new->descriptionformat = FORMAT_HTML;
    // calculate new grade/scale of the element
    if ($old->scale >= 0 and $old->scale <= 6 and isset($newscaleids[$old->scale])) {
        $new->grade = -$newscaleids[$old->scale];
    } elseif ($old->scale == 7) {
        $new->grade = 10;
    } elseif ($old->scale == 8) {
        $new->grade = 20;
    } elseif ($old->scale == 9) {
        $new->grade = 100;
    } else {
        $new->grade = 0;    // something is wrong
    }
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
function workshopform_accumulative_upgrade_grade(stdclass $old, $newassessmentid, stdclass $newdimensioninfo) {
    $new                    = new stdclass();
    $new->assessmentid      = $newassessmentid;
    $new->strategy          = 'accumulative';
    $new->dimensionid       = $newdimensioninfo->newid;
    if ($newdimensioninfo->type == 'value') {
        $new->grade         = $old->grade;
    } elseif ($newdimensioninfo->type == 'scale') {
        // in workshop 1.x, scale items are numbered starting from 0 but Moodle in-built scales start numbering from 1
        $new->grade         = $old->grade + 1;
    }
    $new->peercomment       = $old->feedback;
    $new->peercommentformat = FORMAT_HTML;

    return $new;
}

/**
 * Check if the the migration from legacy workshop 1.9 is needed
 *
 * @return bool
 */
function workshopform_accumulative_upgrade_legacy_needed() {
    global $CFG, $DB;

    $dbman = $DB->get_manager();
    if (!($dbman->table_exists('workshop_elements_old') and $dbman->table_exists('workshop_grades_old'))) {
        return false;
    }
    return true;
}

/**
 * Creates new standard (global) scales to replace the legacy workshop ones
 *
 * In workshop 1.x, scale field in workshop_elements had the following meaning:
 *   0 | 2 point Yes/No scale
 *   1 | 2 point Present/Absent scale
 *   2 | 2 point Correct/Incorrect scale
 *   3 | 3 point Good/Poor scale
 *   4 | 4 point Excellent/Very Poor scale
 *   5 | 5 point Excellent/Very Poor scale
 *   6 | 7 point Excellent/Very Poor scale
 *   7 | Score out of 10
 *   8 | Score out of 20
 *   9 | Score out of 100
 *
 * @return array (int)oldscale => (int)newscaleid
 */
function workshopform_accumulative_upgrade_scales() {
    global $DB, $CFG, $USER;
    require_once($CFG->libdir . '/gradelib.php');

    $sql = 'SELECT DISTINCT scale
              FROM {workshop_elements_old}
             WHERE newplugin IS NULL';
    $oldscales = $DB->get_records_sql($sql);
    $newscales = array();
    foreach($oldscales as $oldscale => $whatever) {
        switch ($oldscale) {
        case 0:
            $data                       = new stdclass();
            $data->courseid             = 0;
            $data->userid               = $USER->id;
            $data->name                 = get_string('scalename0', 'workshopform_accumulative');
            $data->scale                = implode(',', array(get_string('no'), get_string('yes')));
            $data->description          = '';
            $data->descriptionformat    = FORMAT_HTML;

            $scale = new grade_scale();
            grade_scale::set_properties($scale, $data);
            $newscales[0] = $scale->insert('mod/workshop');
            break;
        case 1:
            $data                       = new stdclass();
            $data->courseid             = 0;
            $data->userid               = $USER->id;
            $data->name                 = get_string('scalename1', 'workshopform_accumulative');
            $data->scale                = implode(',', array(get_string('absent', 'workshopform_accumulative'),
                                                             get_string('present', 'workshopform_accumulative')));
            $data->description          = '';
            $data->descriptionformat    = FORMAT_HTML;

            $scale = new grade_scale();
            grade_scale::set_properties($scale, $data);
            $newscales[1] = $scale->insert('mod/workshop');
            break;
        case 2:
            $data                       = new stdclass();
            $data->courseid             = 0;
            $data->userid               = $USER->id;
            $data->name                 = get_string('scalename2', 'workshopform_accumulative');
            $data->scale                = implode(',', array(get_string('incorrect', 'workshopform_accumulative'),
                                                             get_string('correct', 'workshopform_accumulative')));
            $data->description          = '';
            $data->descriptionformat    = FORMAT_HTML;

            $scale = new grade_scale();
            grade_scale::set_properties($scale, $data);
            $newscales[2] = $scale->insert('mod/workshop');
            break;
        case 3:
            $data                       = new stdclass();
            $data->courseid             = 0;
            $data->userid               = $USER->id;
            $data->name                 = get_string('scalename3', 'workshopform_accumulative');
            $data->scale                = implode(',', array('* ' . get_string('poor', 'workshopform_accumulative'),
                                                             '**',
                                                             '*** ' . get_string('good', 'workshopform_accumulative')));
            $data->description          = '';
            $data->descriptionformat    = FORMAT_HTML;

            $scale = new grade_scale();
            grade_scale::set_properties($scale, $data);
            $newscales[3] = $scale->insert('mod/workshop');
            break;
        case 4:
            $data                       = new stdclass();
            $data->courseid             = 0;
            $data->userid               = $USER->id;
            $data->name                 = get_string('scalename4', 'workshopform_accumulative');
            $data->scale                = implode(',', array('* ' . get_string('verypoor', 'workshopform_accumulative'),
                                                             '**',
                                                             '***',
                                                             '**** ' . get_string('excellent', 'workshopform_accumulative')));
            $data->description          = '';
            $data->descriptionformat    = FORMAT_HTML;

            $scale = new grade_scale();
            grade_scale::set_properties($scale, $data);
            $newscales[4] = $scale->insert('mod/workshop');
            break;
        case 5:
            $data                       = new stdclass();
            $data->courseid             = 0;
            $data->userid               = $USER->id;
            $data->name                 = get_string('scalename5', 'workshopform_accumulative');
            $data->scale                = implode(',', array('* ' . get_string('verypoor', 'workshopform_accumulative'),
                                                             '**',
                                                             '***',
                                                             '****',
                                                             '***** ' . get_string('excellent', 'workshopform_accumulative')));
            $data->description          = '';
            $data->descriptionformat    = FORMAT_HTML;

            $scale = new grade_scale();
            grade_scale::set_properties($scale, $data);
            $newscales[5] = $scale->insert('mod/workshop');
            break;
        case 6:
            $data                       = new stdclass();
            $data->courseid             = 0;
            $data->userid               = $USER->id;
            $data->name                 = get_string('scalename6', 'workshopform_accumulative');
            $data->scale                = implode(',', array('* ' . get_string('verypoor', 'workshopform_accumulative'),
                                                             '**',
                                                             '***',
                                                             '****',
                                                             '*****',
                                                             '******',
                                                             '******* ' . get_string('excellent', 'workshopform_accumulative')));
            $data->description          = '';
            $data->descriptionformat    = FORMAT_HTML;

            $scale = new grade_scale();
            grade_scale::set_properties($scale, $data);
            $newscales[6] = $scale->insert('mod/workshop');
            break;
        }
    }

    return $newscales;
}
