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
 * Edit the grade options for an individual grade category
 *
 * @package   core_grades
 * @copyright 2007 Petr Skoda
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once($CFG->dirroot.'/grade/lib.php');
require_once($CFG->dirroot.'/grade/edit/tree/lib.php');
require_once($CFG->dirroot.'/grade/report/lib.php');
require_once('category_form.php');

$courseid = required_param('courseid', PARAM_INT);
$id       = optional_param('id', 0, PARAM_INT); // grade_category->id

$url = new moodle_url('/grade/edit/tree/category.php', array('courseid'=>$courseid));
if ($id !== 0) {
    $url->param('id', $id);
}
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
navigation_node::override_active_url(new moodle_url('/grade/edit/tree/index.php',
    array('id'=>$courseid)));

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    throw new \moodle_exception('invalidcourseid');
}

require_login($course);
$context = context_course::instance($course->id);
require_capability('moodle/grade:manage', $context);

// default return url
$gpr = new grade_plugin_return();
$returnurl = $gpr->get_return_url('index.php?id='.$course->id);


$heading = get_string('categoryedit', 'grades');

if ($id) {
    if (!$grade_category = grade_category::fetch(array('id'=>$id, 'courseid'=>$course->id))) {
        throw new \moodle_exception('invalidcategory');
    }
    $grade_category->apply_forced_settings();
    $category = $grade_category->get_record_data();
    // set parent
    $category->parentcategory = $grade_category->parent;
    $grade_item = $grade_category->load_grade_item();
    // nomalize coef values if needed
    $parent_category = $grade_category->get_parent_category();

    foreach ($grade_item->get_record_data() as $key => $value) {
        $category->{"grade_item_$key"} = $value;
    }

    $decimalpoints = $grade_item->get_decimals();

    $category->grade_item_grademax   = format_float($category->grade_item_grademax, $decimalpoints);
    $category->grade_item_grademin   = format_float($category->grade_item_grademin, $decimalpoints);
    $category->grade_item_gradepass  = format_float($category->grade_item_gradepass, $decimalpoints);
    $category->grade_item_multfactor = format_float($category->grade_item_multfactor, 4);
    $category->grade_item_plusfactor = format_float($category->grade_item_plusfactor, 4);
    $category->grade_item_aggregationcoef2 = format_float($category->grade_item_aggregationcoef2 * 100.0, 4);

    if (!$parent_category) {
        // keep as is
    } else if ($parent_category->aggregation == GRADE_AGGREGATE_SUM or $parent_category->aggregation == GRADE_AGGREGATE_WEIGHTED_MEAN2) {
        $category->grade_item_aggregationcoef = $category->grade_item_aggregationcoef == 0 ? 0 : 1;
    } else {
        $category->grade_item_aggregationcoef = format_float($category->grade_item_aggregationcoef, 4);
    }
    // Check to see if the gradebook is frozen. This allows grades to not be altered at all until a user verifies that they
    // wish to update the grades.
    $gradebookcalculationsfreeze = get_config('core', 'gradebook_calculations_freeze_' . $courseid);
    // Stick with the original code if the grade book is frozen.
    if ($gradebookcalculationsfreeze && (int)$gradebookcalculationsfreeze <= 20150627) {
        if ($category->aggregation == GRADE_AGGREGATE_SUM) {
            // Input fields for grademin and grademax are disabled for the "Natural" category,
            // this means they will be ignored if user does not change aggregation method.
            // But if user does change aggregation method the default values should be used.
            $category->grademax = 100;
            $category->grade_item_grademax = 100;
            $category->grademin = 0;
            $category->grade_item_grademin = 0;
        }
    } else {
        if ($category->aggregation == GRADE_AGGREGATE_SUM && !$grade_item->is_calculated()) {
            // Input fields for grademin and grademax are disabled for the "Natural" category,
            // this means they will be ignored if user does not change aggregation method.
            // But if user does change aggregation method the default values should be used.
            // This does not apply to calculated category totals.
            $category->grademax = 100;
            $category->grade_item_grademax = 100;
            $category->grademin = 0;
            $category->grade_item_grademin = 0;
        }
    }

} else {
    $heading = get_string('newcategory', 'grades');
    $grade_category = new grade_category(array('courseid'=>$courseid), false);
    $grade_category->apply_default_settings();
    $grade_category->apply_forced_settings();

    $category = $grade_category->get_record_data();

    $grade_item = new grade_item(array('courseid'=>$courseid, 'itemtype'=>'manual'), false);
    foreach ($grade_item->get_record_data() as $key => $value) {
        $category->{"grade_item_$key"} = $value;
    }
}

$mform = new edit_category_form(null, array('current'=>$category, 'gpr'=>$gpr));

if ($mform->is_cancelled()) {
    redirect($returnurl);

} else if ($data = $mform->get_data()) {
    grade_edit_tree::update_gradecategory($grade_category, $data);
    redirect($returnurl);
}

$PAGE->navbar->add($heading);
print_grade_page_head($courseid, 'settings', null, $heading, false, false, false);

$mform->display();

echo $OUTPUT->footer();
die;
