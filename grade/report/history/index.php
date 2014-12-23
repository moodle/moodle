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
 * The gradebook grade history report
 *
 * @package    gradereport_history
 * @copyright  2013 NetSpot Pty Ltd (https://www.netspot.com.au)
 * @author     Adam Olley <adam.olley@netspot.com.au>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/gradelib.php');
require_once($CFG->dirroot.'/grade/lib.php');

$download      = optional_param('download', '', PARAM_ALPHA);
$courseid      = required_param('id', PARAM_INT);        // Course id.
$page          = optional_param('page', 0, PARAM_INT);   // Active page.

$PAGE->set_pagelayout('report');
$url = new moodle_url('/grade/report/history/index.php', array('id' => $courseid));
$PAGE->set_url($url);

$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
require_login($course);
$context = context_course::instance($course->id);

require_capability('gradereport/history:view', $context);
require_capability('moodle/grade:viewall', $context);

// Last selected report session tracking.
if (!isset($USER->grade_last_report)) {
    $USER->grade_last_report = array();
}
$USER->grade_last_report[$course->id] = 'history';

$select = "itemtype <> 'course' AND courseid = :courseid AND " . $DB->sql_isnotempty('grade_items', 'itemname', true, true);
$itemids = $DB->get_records_select_menu('grade_items', $select, array('courseid' => $course->id), 'itemname ASC', 'id, itemname');
$itemids = array(0 => get_string('allgradeitems', 'gradereport_history')) + $itemids;

$output = $PAGE->get_renderer('gradereport_history');
$graders = \gradereport_history\helper::get_graders($course->id);
$params = array('course' => $course, 'itemids' => $itemids, 'graders' => $graders, 'userbutton' => null);
$mform = new \gradereport_history\filter_form(null, $params);
$filters = array();
if ($data = $mform->get_data()) {
    $filters = (array)$data;

    if (!empty($filters['datetill'])) {
        $filters['datetill'] += DAYSECS - 1; // Set to end of the chosen day.
    }
} else {
    $filters = array(
        'id' => $courseid,
        'userids' => optional_param('userids', '', PARAM_SEQUENCE),
        'itemid' => optional_param('itemid', 0, PARAM_INT),
        'grader' => optional_param('grader', 0, PARAM_INT),
        'datefrom' => optional_param('datefrom', 0, PARAM_INT),
        'datetill' => optional_param('datetill', 0, PARAM_INT),
        'revisedonly' => optional_param('revisedonly', 0, PARAM_INT),
    );
}

$table = new \gradereport_history\output\tablelog('gradereport_history', $context, $url, $filters, $download, $page);

$names = array();
foreach ($table->get_selected_users() as $key => $user) {
    $names[$key] = fullname($user);
}
$filters['userfullnames'] = implode(',', $names);

// Set up js.
\gradereport_history\helper::init_js($course->id, $names);

// Now that we have the names, reinitialise the button so its able to control them.
$button = new \gradereport_history\output\user_button($PAGE->url, get_string('selectusers', 'gradereport_history'), 'get');

$userbutton = $output->render($button);
$params = array('course' => $course, 'itemids' => $itemids, 'graders' => $graders, 'userbutton' => $userbutton);
$mform = new \gradereport_history\filter_form(null, $params);
$mform->set_data($filters);

if ($table->is_downloading()) {
    // Download file and exit.
    \core\session\manager::write_close();
    echo $output->render($table);
    die();
}

// Print header.
print_grade_page_head($COURSE->id, 'report', 'history', get_string('pluginname', 'gradereport_history'), false, '');
$mform->display();

// Render table.
echo $output->render($table);

$event = \gradereport_history\event\grade_report_viewed::create(
    array(
        'context' => $context,
        'courseid' => $courseid
    )
);
$event->trigger();

echo $OUTPUT->footer();
