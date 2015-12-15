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
 * User competency page. Lists everything known about a user competency.
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../config.php');

$userid = optional_param('userid', 0, PARAM_INT);
$competencyid = required_param('competencyid', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);

require_login(null, false);
if (isguestuser()) {
    throw new require_login_exception('Guests are not allowed here.');
}

if (empty($userid)) {
    $context = context_course::instance($courseid);
    $gradable = get_enrolled_users($context, 'tool/lp:coursecompetencygradable', 0, 'u.id', null, 0, 1);
    if (empty($gradable)) {
        print_error('noparticipants');
    }
    $userid = array_pop($gradable)->id;
}

$params = array('userid' => $userid, 'competencyid' => $competencyid, 'courseid' => $courseid);
$course = $DB->get_record('course', array('id' => $courseid));
$url = new moodle_url('/admin/tool/lp/user_competency_in_course.php', $params);

$usercontext = context_user::instance($userid);
$user = $DB->get_record('user', array('id' => $userid));
$competency = new \tool_lp\competency($competencyid);

// Does a permissions check for us.
$usercompetencies = \tool_lp\api::list_user_competencies_in_course($courseid, $userid);
$subtitle = $competency->get_shortname() . ' <em>' . $competency->get_idnumber() . '</em>';

list($title, $subtitle) = \tool_lp\page_helper::setup_for_course($url, $course, $subtitle);

$output = $PAGE->get_renderer('tool_lp');
$userheading = array(
    'heading' => fullname($user),
    'user' => $user,
    'usercontext' => $usercontext
);
echo $output->header();
echo $OUTPUT->context_header($userheading, 3);
//echo $output->heading($title, 3);

$baseurl = new moodle_url('/admin/tool/lp/user_competency_in_course.php');
$nav = new \tool_lp\output\user_competency_course_navigation($userid, $competencyid, $courseid, $baseurl);
echo $output->render($nav);
$page = new \tool_lp\output\user_competency_summary_in_course_page($userid, $competencyid, $courseid);
echo $output->render($page);

echo $output->footer();
