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
 * @package    enrol_workdayhrm
 * @copyright  2023 onwards LSU Online & Continuing Education
 * @copyright  2023 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/enrol/workdayhrm/lib.php');

// Disables the time limit.
set_time_limit(0);

// Set up the page params.
$pageparams = [
    'courseid' => required_param('courseid', PARAM_INT),
];

$courseid = $pageparams['courseid'];

$coursecontext = \context_course::instance($courseid); 
require_capability('enrol/workdayhrm:reprocess', $coursecontext);

// Authentication.
require_login();
if (!is_siteadmin()) {
    $helpers->redirect_to_url('/my');
}

//$title = get_string('pluginname', 'enrol_workdayhrm') . ' > ' . get_string('reprocess', 'enrol_workdayhrm');
$title = get_string('reprocess', 'enrol_workdayhrm');
$pagetitle = $title;
$url = new moodle_url('/enrol/workdayhrm/reprocess.php?courseid=' . $courseid);
$context = \context_system::instance();

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('admin');

// Navbar Bread Crumbs
$PAGE->navbar->add(get_string('back', 'moodle'), new moodle_url('/course/view.php?id=' . $courseid));

echo $OUTPUT->header();

$starttime = microtime(true);

echo"<pre>";
enrol_workdayhrm_plugin::run_workdayhrm_full_enroll($courseid);

$elapsedtime = round(microtime(true) - $starttime, 3);
mtrace("\n\nThis entire process took " . $elapsedtime . " seconds.");
echo"</pre>";

echo $OUTPUT->footer();
