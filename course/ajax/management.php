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
 * Performs course category management ajax actions.
 *
 * Please note functions may throw exceptions, please ensure your JS handles them as well as the outcome objects.
 *
 * @package    core_course
 * @copyright  2013 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require_once('../../config.php');
require_once($CFG->dirroot.'/lib/coursecatlib.php');
require_once($CFG->dirroot.'/course/lib.php');

$action = required_param('action', PARAM_ALPHA);
require_sesskey(); // Gotta have the sesskey.
require_login(); // Gotta be logged in (of course).
$PAGE->set_context(context_system::instance());

// Prepare an outcome object. We always use this.
$outcome = new stdClass;
$outcome->error = false;
$outcome->outcome = false;

echo $OUTPUT->header();

switch ($action) {
    case 'movecourseup' :
        $courseid = required_param('courseid', PARAM_INT);
        $outcome->outcome = \core_course\management\helper::action_course_change_sortorder_up_one_by_record($courseid);
        break;
    case 'movecoursedown' :
        $courseid = required_param('courseid', PARAM_INT);
        $outcome->outcome = \core_course\management\helper::action_course_change_sortorder_down_one_by_record($courseid);
        break;
    case 'movecourseintocategory':
        $courseid = required_param('courseid', PARAM_INT);
        $categoryid = required_param('categoryid', PARAM_INT);
        $course = get_course($courseid);
        $oldcategory = coursecat::get($course->category);
        $category = coursecat::get($categoryid);
        $outcome->outcome = \core_course\management\helper::move_courses_into_category($category, $courseid);
        $perpage = (int)get_user_preferences('coursecat_management_perpage', $CFG->coursesperpage);
        $totalcourses = $oldcategory->get_courses_count();
        $totalpages = ceil($totalcourses / $perpage);
        if ($totalpages == 0) {
            $str = get_string('nocoursesyet');
        } else if ($totalpages == 1) {
            $str = get_string('showingacourses', 'moodle', $totalcourses);
        } else {
            $a = new stdClass;
            $a->start = ($page * $perpage) + 1;
            $a->end = min((($page + 1) * $perpage), $totalcourses);
            $a->total = $totalcourses;
            $str = get_string('showingxofycourses', 'moodle', $a);
        }
        $outcome->totalcatcourses = $category->get_courses_count();
        $outcome->fromcatcoursecount = $totalcourses;
        $outcome->paginationtotals = $str;
        break;
    case 'movecourseafter' :
        $courseid = required_param('courseid', PARAM_INT);
        $moveaftercourseid = required_param('moveafter', PARAM_INT);
        $outcome->outcome = \core_course\management\helper::action_course_change_sortorder_after_course(
            $courseid, $moveaftercourseid);
        break;
    case 'hidecourse' :
        $courseid = required_param('courseid', PARAM_INT);
        $outcome->outcome = \core_course\management\helper::action_course_hide_by_record($courseid);
        break;
    case 'showcourse' :
        $courseid = required_param('courseid', PARAM_INT);
        $outcome->outcome = \core_course\management\helper::action_course_show_by_record($courseid);
        break;
    case 'movecategoryup' :
        $categoryid = required_param('categoryid', PARAM_INT);
        $outcome->outcome = \core_course\management\helper::action_category_change_sortorder_up_one_by_id($categoryid);
        break;
    case 'movecategorydown' :
        $categoryid = required_param('categoryid', PARAM_INT);
        $outcome->outcome = \core_course\management\helper::action_category_change_sortorder_down_one_by_id($categoryid);
        break;
    case 'hidecategory' :
        $categoryid = required_param('categoryid', PARAM_INT);
        $selectedcategoryid = optional_param('selectedcategory', null, PARAM_INT);
        $outcome->outcome = \core_course\management\helper::action_category_hide_by_id($categoryid);
        $outcome->categoryvisibility = \core_course\management\helper::get_category_children_visibility($categoryid);
        $outcome->coursevisibility = \core_course\management\helper::get_category_courses_visibility($categoryid);
        if ($selectedcategoryid !== null) {
            $outcome->coursevisibility = array_merge(
                $outcome->coursevisibility,
                \core_course\management\helper::get_category_courses_visibility($selectedcategoryid)
            );
        }
        break;
    case 'showcategory' :
        $categoryid = required_param('categoryid', PARAM_INT);
        $selectedcategoryid = optional_param('selectedcategory', null, PARAM_INT);
        $outcome->outcome = \core_course\management\helper::action_category_show_by_id($categoryid);
        $outcome->categoryvisibility = \core_course\management\helper::get_category_children_visibility($categoryid);
        $outcome->coursevisibility = \core_course\management\helper::get_category_courses_visibility($categoryid);
        if ($selectedcategoryid !== null) {
            $outcome->coursevisibility = array_merge(
                $outcome->coursevisibility,
                \core_course\management\helper::get_category_courses_visibility($selectedcategoryid)
            );
        }
        break;
    case 'expandcategory':
        $categoryid = required_param('categoryid', PARAM_INT);
        $coursecat = coursecat::get($categoryid);
        \core_course\management\helper::record_expanded_category($coursecat);
        $outcome->outcome = true;
        break;
    case 'collapsecategory':
        $categoryid = required_param('categoryid', PARAM_INT);
        $coursecat = coursecat::get($categoryid);
        \core_course\management\helper::record_expanded_category($coursecat, false);
        $outcome->outcome = true;
        break;
    case 'getsubcategorieshtml' :
        $categoryid = required_param('categoryid', PARAM_INT);
        /* @var core_course_management_renderer $renderer */
        $renderer = $PAGE->get_renderer('core_course', 'management');
        $outcome->html = html_writer::start_tag('ul',
            array('class' => 'ml', 'role' => 'group', 'id' => 'subcategoriesof'.$categoryid));
        $coursecat = coursecat::get($categoryid);
        foreach ($coursecat->get_children() as $subcat) {
            $outcome->html .= $renderer->category_listitem($subcat, array(), $subcat->get_children_count());
        }
        $outcome->html .= html_writer::end_tag('ul');
        $outcome->outcome = true;
        break;
}

echo json_encode($outcome);
echo $OUTPUT->footer();
// Thats all folks.
// Don't ever even consider putting anything after this. It just wouldn't make sense.
// But you already knew that, you smart developer you.
exit;
