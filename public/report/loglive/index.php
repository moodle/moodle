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
 * Displays live view of recent logs
 *
 * This file generates live view of recent logs.
 *
 * @package    report_loglive
 * @copyright  2011 Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\report_helper;

require('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/course/lib.php');
global $SITE, $PAGE;

$id = optional_param('id', 0, PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$logreader = optional_param('logreader', '', PARAM_COMPONENT); // Reader which will be used for displaying logs.

// Get course details.
if (!empty($id)) {
    $course = $DB->get_record('course', ['id' => $id], '*');
    if ($course) {
        require_login($course);
        $context = context_course::instance($course->id);
        $coursename = format_string($course->fullname, true, ['context' => $context]);
    }
}

if (empty($course)) {
    admin_externalpage_setup('reportloglive', '', null, '', ['pagelayout' => 'report']);
    $context = context_system::instance();
    $coursename = format_string($SITE->fullname, true, ['context' => $context]);
}
require_capability('report/loglive:view', $context);

$params = array();
if ($id != 0) {
    $params['id'] = $id;
}
if ($page != 0) {
    $params['page'] = $page;
}
if ($logreader !== '') {
    $params['logreader'] = $logreader;
}
$url = new moodle_url("/report/loglive/index.php", $params);

$PAGE->set_url($url);
$PAGE->set_pagelayout('report');
$PAGE->set_context($context);
$strlivelogs = get_string('livelogs', 'report_loglive');
$PAGE->set_title("$coursename: $strlivelogs");
$output = $PAGE->get_renderer('report_loglive');
echo $output->header();
if (!report_helper::has_valid_group($context)) {
    echo $output->notification(get_string('notingroup'));
    echo $output->footer();
    exit();
}

$renderable = new report_loglive_renderable($logreader, $id, $url, 0, $page);
$refresh = $renderable->get_refresh_rate();
$logreader = $renderable->selectedlogreader;
$strupdatesevery = get_string('updatesevery', 'moodle', $refresh);
$PAGE->set_heading($coursename);

// Print selector dropdown.
$pluginname = get_string('pluginname', 'report_loglive');
report_helper::print_report_selector($pluginname);
echo html_writer::div(get_string('livelogswithupdate', 'report_loglive', $strupdatesevery), 'mb-3');
echo $output->reader_selector($renderable);
echo $output->toggle_liveupdate_button($renderable);
echo $output->render($renderable);

// Include and trigger ajax requests.
if ($page == 0 && !empty($logreader)) {
    // Tell Js to fetch new logs only, by passing the latest timestamp of records in the table.
    $until = $renderable->get_table()->get_until();
    $jsparams = array('since' => $until , 'courseid' => $id, 'page' => $page, 'logreader' => $logreader,
        'interval' => $refresh, 'perpage' => $renderable->perpage);
    $PAGE->requires->strings_for_js(array('pause', 'resume'), 'report_loglive');
    $PAGE->requires->yui_module('moodle-report_loglive-fetchlogs', 'Y.M.report_loglive.FetchLogs.init', array($jsparams));
}

// Trigger a logs viewed event.
$event = \report_loglive\event\report_viewed::create(array('context' => $context));
$event->trigger();

echo $output->footer();
