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
 * Performs actions on grade items and categories like hiding and locking
 *
 * @package   core_grades
 * @copyright 2007 Petr Skoda
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once '../../../config.php';
require_once $CFG->dirroot.'/grade/lib.php';

$courseid = required_param('id', PARAM_INT);
$action   = required_param('action', PARAM_ALPHA);
$eid      = required_param('eid', PARAM_ALPHANUM);

$PAGE->set_url('/grade/edit/tree/action.php', array('id'=>$courseid, 'action'=>$action, 'eid'=>$eid));

/// Make sure they can even access this course
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    throw new \moodle_exception('invalidcourseid');
}
require_login($course);
$context = context_course::instance($course->id);

// default return url
$gpr = new grade_plugin_return();
$returnurl = $gpr->get_return_url($CFG->wwwroot.'/grade/edit/tree/index.php?id='.$course->id);

// get the grading tree object
$gtree = new grade_tree($courseid, false, false);

// what are we working with?
if (!$element = $gtree->locate_element($eid)) {
    throw new \moodle_exception('invalidelementid', '', $returnurl);
}
$object = $element['object'];
$type   = $element['type'];


switch ($action) {
    case 'hide':
        if ($eid and confirm_sesskey()) {
            if (!has_capability('moodle/grade:manage', $context) and !has_capability('moodle/grade:hide', $context)) {
                throw new \moodle_exception('nopermissiontohide', '', $returnurl);
            }
            if ($type == 'grade' and empty($object->id)) {
                $object->insert();
            }
            if (!$object->can_control_visibility()) {
                throw new \moodle_exception('componentcontrolsvisibility', 'grades', $returnurl);
            }
            $object->set_hidden(1, true);
        }
        break;

    case 'show':
        if ($eid and confirm_sesskey()) {
            if (!has_capability('moodle/grade:manage', $context) and !has_capability('moodle/grade:hide', $context)) {
                throw new \moodle_exception('nopermissiontoshow', '', $returnurl);
            }
            if ($type == 'grade' and empty($object->id)) {
                $object->insert();
            }
            if (!$object->can_control_visibility()) {
                throw new \moodle_exception('componentcontrolsvisibility', 'grades', $returnurl);
            }
            $object->set_hidden(0, true);
        }
        break;

    case 'lock':
        if ($eid and confirm_sesskey()) {
            if (!has_capability('moodle/grade:manage', $context) and !has_capability('moodle/grade:lock', $context)) {
                throw new \moodle_exception('nopermissiontolock', '', $returnurl);
            }
            if ($type == 'grade' and empty($object->id)) {
                $object->insert();
            }
            $object->set_locked(1, true, true);
        }
        break;

    case 'unlock':
        if ($eid and confirm_sesskey()) {
            if (!has_capability('moodle/grade:manage', $context) and !has_capability('moodle/grade:unlock', $context)) {
                throw new \moodle_exception('nopermissiontounlock', '', $returnurl);
            }
            if ($type == 'grade' and empty($object->id)) {
                $object->insert();
            }
            $object->set_locked(0, true, true);
        }
        break;

    case 'resetweights':
        if ($eid && confirm_sesskey()) {

            // This is specific to category items with natural weight as an aggregation method, and can
            // only be done by someone who can manage the grades.
            if ($type != 'category' || $object->aggregation != GRADE_AGGREGATE_SUM ||
                    !has_capability('moodle/grade:manage', $context)) {
                throw new \moodle_exception('nopermissiontoresetweights', 'grades', $returnurl);
            }

            // Remove the weightoverride flag from the children.
            $children = $object->get_children();
            foreach ($children as $item) {
                if ($item['type'] == 'category') {
                    $gradeitem = $item['object']->load_grade_item();
                } else {
                    $gradeitem = $item['object'];
                }

                if ($gradeitem->weightoverride == false) {
                    continue;
                }

                $gradeitem->weightoverride = false;
                $gradeitem->update();
            }

            // Force regrading.
            $object->force_regrading();
        }
}

redirect($returnurl);
//redirect($returnurl, 'debug delay', 5);


