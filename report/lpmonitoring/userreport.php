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
 * Report competency for user.
 *
 * @package    report_lpmonitoring
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

$userid = required_param('userid', PARAM_INT);
$context = context_user::instance($userid);
$user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);

require_login();
\core_competency\api::require_enabled();

if (!\core_competency\plan::can_read_user($userid)) {
    throw new required_capability_exception($context, 'moodle/competency:planview', 'nopermissions', '');
}

$urlparams = array('userid' => $userid);

$url = new moodle_url('/report/lpmonitoring/userreport.php', $urlparams);
$title = get_string('pluginname', 'report_lpmonitoring');

// If not his own report, we want to extend the navigation for the user.
$iscurrentuser = ($USER->id == $user->id);
if (!$iscurrentuser) {
    $PAGE->navigation->extend_for_user($user);
    $PAGE->navigation->set_userid_for_parent_checks($user->id);
}

// Set css.
$PAGE->requires->css('/report/lpmonitoring/style/checkbox.css');
$PAGE->set_context($context);
$PAGE->set_pagelayout('report');
$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($title);

// Create the appropriate breadcrumb.
$navigationnode = array('url' => $url, 'name' => $title);
$PAGE->add_report_nodes($user->id, $navigationnode);

$output = $PAGE->get_renderer('report_lpmonitoring');

echo $output->header();
echo $output->heading($title);

$page = new \report_lpmonitoring\output\user_report_page($userid);
echo $output->render($page);
echo $output->footer();
