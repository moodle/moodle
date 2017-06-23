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
    print_error('invalidcourseid');
}

require_login($course);
$context = context_course::instance($course->id);
require_capability('moodle/grade:manage', $context);

$PAGE->requires->js_call_amd('core_grades/edittree_index', 'enhance');

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
        print_error('invalidelementid', '', $returnurl);
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

$grade_edit_tree = new grade_edit_tree($gtree, $movingeid, $gpr);

switch ($action) {
    case 'delete':
        if ($eid && confirm_sesskey()) {
            if (!$grade_edit_tree->element_deletable($element)) {
                // no deleting of external activities - they would be recreated anyway!
                // exception is activity without grading or misconfigured activities
                break;
            }
            $confirm = optional_param('confirm', 0, PARAM_BOOL);

            if ($confirm) {
                $object->delete('grade/report/grader/category');
                redirect($returnurl);

            } else {
                $PAGE->set_title($strgrades . ': ' . $strgraderreport);
                $PAGE->set_heading($course->fullname);
                echo $OUTPUT->header();
                $strdeletecheckfull = get_string('deletecheck', '', $object->get_name());
                $optionsyes = array('eid'=>$eid, 'confirm'=>1, 'sesskey'=>sesskey(), 'id'=>$course->id, 'action'=>'delete');
                $optionsno  = array('id'=>$course->id);
                $formcontinue = new single_button(new moodle_url('index.php', $optionsyes), get_string('yes'));
                $formcancel = new single_button(new moodle_url('index.php', $optionsno), get_string('no'), 'get');
                echo $OUTPUT->confirm($strdeletecheckfull, $formcontinue, $formcancel);
                echo $OUTPUT->footer();
                die;
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
                print_error('invalidelementid', '', $returnurl);
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

//if we go straight to the db to update an element we need to recreate the tree as
// $grade_edit_tree has already been constructed.
//Ideally we could do the updates through $grade_edit_tree to avoid recreating it
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

        $grade_edit_tree->move_elements($elements, $returnurl);
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

print_grade_page_head($courseid, 'settings', 'setup', get_string('gradebooksetup', 'grades'));

// Print Table of categories and items
echo $OUTPUT->box_start('gradetreebox generalbox');

//did we update something in the db and thus invalidate $grade_edit_tree?
if ($recreatetree) {
    $grade_edit_tree = new grade_edit_tree($gtree, $movingeid, $gpr);
}

$bulkmoveoptions = ['' => get_string('choosedots')] + $grade_edit_tree->categories;
$tpldata = (object) [
    'actionurl' => $returnurl,
    'sesskey' => sesskey(),
    'showsave' => !$moving,
    'showbulkmove' => !$moving && count($grade_edit_tree->categories) > 1,
    'bulkmoveoptions' => array_map(function($option) use ($bulkmoveoptions) {
        return [
            'name' => $bulkmoveoptions[$option],
            'value' => $option
        ];
    }, array_keys($bulkmoveoptions))
];

// Check to see if we have a normalisation message to send.
if ($weightsadjusted) {
    $notification = new \core\output\notification(get_string('weightsadjusted', 'grades'), \core\output\notification::NOTIFY_INFO);
    $tpldata->notification = $notification->export_for_template($OUTPUT);
}

$tpldata->table = html_writer::table($grade_edit_tree->table);

echo $OUTPUT->render_from_template('core_grades/edit_tree', $tpldata);

echo $OUTPUT->box_end();

// Print action buttons
echo $OUTPUT->container_start('buttons mdl-align');

if ($moving) {
    echo $OUTPUT->single_button(new moodle_url('index.php', array('id'=>$course->id)), get_string('cancel'), 'get');
} else {
    echo $OUTPUT->single_button(new moodle_url('category.php', array('courseid'=>$course->id)), get_string('addcategory', 'grades'), 'get');
    echo $OUTPUT->single_button(new moodle_url('item.php', array('courseid'=>$course->id)), get_string('additem', 'grades'), 'get');

    if (!empty($CFG->enableoutcomes)) {
        echo $OUTPUT->single_button(new moodle_url('outcomeitem.php', array('courseid'=>$course->id)), get_string('addoutcomeitem', 'grades'), 'get');
    }

    //echo $OUTPUT->(new moodle_url('index.php', array('id'=>$course->id, 'action'=>'autosort')), get_string('autosort', 'grades'), 'get');
}

echo $OUTPUT->container_end();

$PAGE->requires->yui_module('moodle-core-formchangechecker',
    'M.core_formchangechecker.init',
    array(array(
        'formid' => 'gradetreeform'
    ))
);
$PAGE->requires->string_for_js('changesmadereallygoaway', 'moodle');

echo $OUTPUT->footer();
die;


