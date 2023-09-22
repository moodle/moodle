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
 * The Gradebook setup page.
 *
 * @package   core_grades
 * @copyright 2008 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true); // The progress bar may be used here.

require_once '../../../config.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->dirroot.'/grade/report/lib.php'; // for preferences
require_once $CFG->dirroot.'/grade/edit/tree/lib.php';

$courseid        = required_param('id', PARAM_INT);
$action          = optional_param('action', 0, PARAM_ALPHA);
$eid             = optional_param('eid', 0, PARAM_ALPHANUM);
$weightsadjusted = optional_param('weightsadjusted', 0, PARAM_INT);

$url = new moodle_url('/grade/edit/tree/index.php', array('id' => $courseid));
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');

/// Make sure they can even access this course
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    throw new \moodle_exception('invalidcourseid');
}

require_login($course);
$context = context_course::instance($course->id);
require_capability('moodle/grade:manage', $context);

$PAGE->requires->js_call_amd('core_grades/edittree_index', 'init', [$courseid, $USER->id]);
$PAGE->requires->js_call_amd('core_grades/gradebooksetup_forms', 'init');

$decsep = get_string('decsep', 'langconfig');
// This setting indicates if we should use algorithm prior to MDL-49257 fix for calculating extra credit weights.
$gradebookcalculationfreeze = (int) get_config('core', 'gradebook_calculations_freeze_' . $courseid);
$oldextracreditcalculation = $gradebookcalculationfreeze && ($gradebookcalculationfreeze <= 20150619);
$PAGE->requires->js_call_amd('core_grades/edittree_weights', 'init', [$decsep, $oldextracreditcalculation]);

/// return tracking object
$gpr = new grade_plugin_return(array('type'=>'edit', 'plugin'=>'tree', 'courseid'=>$courseid));
$returnurl = $gpr->get_return_url(null);

// get the grading tree object
// note: total must be first for moving to work correctly, if you want it last moving code must be rewritten!
$gtree = new grade_tree($courseid, false, false);

if (empty($eid)) {
    $element = null;
    $object  = null;

} else {
    if (!$element = $gtree->locate_element($eid)) {
        throw new \moodle_exception('invalidelementid', '', $returnurl);
    }
    $object = $element['object'];
}

$switch = grade_get_setting($course->id, 'aggregationposition', $CFG->grade_aggregationposition);

$strgrades             = get_string('grades');
$strgraderreport       = get_string('graderreport', 'grades');

$moving = false;
$movingeid = false;

if ($action == 'moveselect') {
    if ($eid and confirm_sesskey()) {
        $movingeid = $eid;
        $moving=true;
    }
}

$gradeedittree = new grade_edit_tree($gtree, $movingeid, $gpr);

switch ($action) {
    case 'duplicate':
        if ($eid and confirm_sesskey()) {
            if (!$el = $gtree->locate_element($eid)) {
                throw new \moodle_exception('invalidelementid', '', $returnurl);
            }

            $object->duplicate();
            redirect($returnurl);
        }
        break;

    case 'delete':
        if ($eid && confirm_sesskey()) {
            if (!$gradeedittree->element_deletable($element)) {
                // no deleting of external activities - they would be recreated anyway!
                // exception is activity without grading or misconfigured activities
                break;
            }
            $confirm = optional_param('confirm', 0, PARAM_BOOL);

            if ($confirm) {
                $object->delete('grade/report/grader/category');
                redirect($returnurl);

            }
        }
        break;

    case 'autosort':
        //TODO: implement autosorting based on order of mods on course page, categories first, manual items last
        break;

    case 'move':
        if ($eid and confirm_sesskey()) {
            $moveafter = required_param('moveafter', PARAM_ALPHANUM);
            $first = optional_param('first', false,  PARAM_BOOL); // If First is set to 1, it means the target is the first child of the category $moveafter

            if(!$after_el = $gtree->locate_element($moveafter)) {
                throw new \moodle_exception('invalidelementid', '', $returnurl);
            }

            $after = $after_el['object'];
            $sortorder = $after->get_sortorder();

            if (!$first) {
                $parent = $after->get_parent_category();
                $object->set_parent($parent->id);
            } else {
                $object->set_parent($after->id);
            }

            $object->move_after_sortorder($sortorder);

            redirect($returnurl);
        }
        break;

    default:
        break;
}

// If we go straight to the db to update an element we need to recreate the tree as
// $gradeedittree has already been constructed.
// Ideally we could do the updates through $gradeedittree to avoid recreating it.
$recreatetree = false;

if ($data = data_submitted() and confirm_sesskey()) {
    // Perform bulk actions first
    if (!empty($data->bulkmove)) {
        $elements = array();

        foreach ($data as $key => $value) {
            if (preg_match('/select_(ig[0-9]*)/', $key, $matches)) {
                $elements[] = $matches[1];
            }
        }

        $gradeedittree->move_elements($elements, $returnurl);
    }

    // Update weights (extra credits) on categories and items.
    foreach ($data as $key => $value) {
        if (preg_match('/^weight_([0-9]+)$/', $key, $matches)) {
            $aid   = $matches[1];

            $value = unformat_float($value);
            $value = clean_param($value, PARAM_FLOAT);

            $grade_item = grade_item::fetch(array('id' => $aid, 'courseid' => $courseid));

            // Convert weight to aggregation coef2.
            $aggcoef = $grade_item->get_coefstring();
            if ($aggcoef == 'aggregationcoefextraweightsum') {
                // The field 'weight' should only be sent when the checkbox 'weighoverride' is checked,
                // so there is not need to set weightoverride here, it is done below.
                $value = $value / 100.0;
                $grade_item->aggregationcoef2 = $value;
            } else if ($aggcoef == 'aggregationcoefweight' || $aggcoef == 'aggregationcoefextraweight') {
                $grade_item->aggregationcoef = $value;
            }

            $grade_item->update();

            $recreatetree = true;

        // Grade item checkbox inputs.
        } elseif (preg_match('/^(weightoverride)_([0-9]+)$/', $key, $matches)) {
            $param   = $matches[1];
            $aid     = $matches[2];
            $value   = clean_param($value, PARAM_BOOL);

            $grade_item = grade_item::fetch(array('id' => $aid, 'courseid' => $courseid));
            $grade_item->$param = $value;

            $grade_item->update();

            $recreatetree = true;
        }
    }
}

$originalweights = grade_helper::fetch_all_natural_weights_for_course($courseid);

/**
 * Callback function to adjust the URL if weights changed after the
 * regrade.
 *
 * @param int $courseid The course ID
 * @param array $originalweights The weights before the regrade
 * @param int $weightsadjusted Whether weights have been adjusted
 * @return moodle_url A URL to redirect to after regrading when a progress bar is displayed.
 */
$grade_edit_tree_index_checkweights = function() use ($courseid, $originalweights, &$weightsadjusted) {
    global $PAGE;

    $alteredweights = grade_helper::fetch_all_natural_weights_for_course($courseid);
    if (array_diff($originalweights, $alteredweights)) {
        $weightsadjusted = 1;
        return new moodle_url($PAGE->url, array('weightsadjusted' => $weightsadjusted));
    }
    return $PAGE->url;
};

if (grade_regrade_final_grades_if_required($course, $grade_edit_tree_index_checkweights)) {
    $recreatetree = true;
}

$actionbar = new \core_grades\output\gradebook_setup_action_bar($context);
print_grade_page_head($courseid, 'settings', 'setup', false,
    false, false, true, null, null, null, $actionbar);

// Print Table of categories and items
echo $OUTPUT->box_start('gradetreebox generalbox');

// Did we update something in the db and thus invalidate $gradeedittree?
if ($recreatetree) {
    $gradeedittree = new grade_edit_tree($gtree, $movingeid, $gpr);
}

$tpldata = (object) [
    'actionurl' => $returnurl,
    'sesskey' => sesskey(),
    'movingmodeenabled' => $moving,
    'courseid' => $courseid
];

// Check to see if we have a normalisation message to send.
if ($weightsadjusted) {
    $notification = new \core\output\notification(get_string('weightsadjusted', 'grades'), \core\output\notification::NOTIFY_INFO);
    $tpldata->notification = $notification->export_for_template($OUTPUT);
}

$tpldata->table = html_writer::table($gradeedittree->table);

// If not in moving mode and there is more than one grade category, then initialise the bulk action module.
if (!$moving && count($gradeedittree->categories) > 1) {
    $PAGE->requires->js_call_amd('core_grades/bulkactions/edit/tree/bulk_actions', 'init', [$courseid]);
}

$footercontent = $OUTPUT->render_from_template('core_grades/edit_tree_sticky_footer', $tpldata);
$stickyfooter = new core\output\sticky_footer($footercontent);
$tpldata->stickyfooter = $OUTPUT->render($stickyfooter);

echo $OUTPUT->render_from_template('core_grades/edit_tree', $tpldata);

echo $OUTPUT->box_end();

$PAGE->requires->js_call_amd('core_form/changechecker', 'watchFormById', ['gradetreeform']);

echo $OUTPUT->footer();
die;
