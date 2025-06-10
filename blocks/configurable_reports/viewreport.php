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
 * Configurable Reports a Moodle block for creating customizable reports
 *
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @package    block_configurable_reports
 * @author     Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once($CFG->dirroot . "/blocks/configurable_reports/locallib.php");

$id = required_param('id', PARAM_INT);
$download = optional_param('download', false, PARAM_BOOL);
$format = optional_param('format', '', PARAM_ALPHA);
$courseid = optional_param('courseid', null, PARAM_INT);
$embed = optional_param('embed', false, PARAM_BOOL);

if (!$report = $DB->get_record('block_configurable_reports', ['id' => $id])) {
    throw new moodle_exception('reportdoesnotexists', 'block_configurable_reports');
}

if ($courseid && $report->global) {
    $report->courseid = $courseid;
} else {
    $courseid = $report->courseid;
}

if (!$course = $DB->get_record('course', ['id' => $courseid])) {
    throw new moodle_exception('No such course id');
}

// Force user login in course (SITE or Course).
if ((int) $course->id === SITEID) {
    require_login();
    $context = context_system::instance();
} else {
    require_login($course);
    $context = context_course::instance($course->id);
}

require_once($CFG->dirroot . '/blocks/configurable_reports/report.class.php');
require_once($CFG->dirroot . '/blocks/configurable_reports/reports/' . $report->type . '/report.class.php');

$reportclassname = 'report_' . $report->type;
$reportclass = new $reportclassname($report);

if (!$reportclass->check_permissions($USER->id, $context)) {
    throw new moodle_exception('badpermissions', 'block_configurable_reports');
}

$PAGE->set_context($context);
$PAGE->set_pagelayout('incourse');
$PAGE->set_url('/blocks/configurable_reports/viewreport.php', ['id' => $id]);
$PAGE->requires->jquery();

$download = $download && $format && strpos($report->export, $format . ',') !== false;

if ($download && $report->type === "sql") {
    $reportclass->set_forexport(true);
}
$reportclass->create_report();

$action = (!empty($download)) ? 'download' : 'view';

// No download, build navigation header etc..
if (!$download) {
    $reportclass->check_filters_request();
    $reportname = format_string($report->name);
    $navlinks = [];

    $hasmanageallcap = has_capability('block/configurable_reports:managereports', $context);
    $hasmanageowncap = has_capability('block/configurable_reports:manageownreports', $context);

    if ($hasmanageallcap || ($hasmanageowncap && $report->ownerid == $USER->id)) {
        $managereporturl = new moodle_url('/blocks/configurable_reports/managereport.php', ['courseid' => $report->courseid]);
        $PAGE->navbar->add(get_string('managereports', 'block_configurable_reports'), $managereporturl);
        $PAGE->navbar->add($reportname);
    } else {
        // These users don't have the capability to manage reports but we still want them to see some breadcrumbs.
        $PAGE->navbar->add(get_string('viewreport', 'block_configurable_reports'));
        $PAGE->navbar->add($reportname);
    }

    $PAGE->set_title($reportname);
    $PAGE->set_heading($reportname);
    $PAGE->set_cacheable(true);
    if ($embed) {
        $PAGE->set_pagelayout('embedded');
    }
    echo $OUTPUT->header();

    $canmanage = ($hasmanageallcap || ($hasmanageowncap && $report->ownerid == $USER->id));
    if (!$embed && $canmanage) {
        $currenttab = 'viewreport';
        include('tabs.php');
    }

    // Print the report HTML.
    $reportclass->print_report_page($PAGE);

} else {
    // Large exports are likely to take their time and memory.
    core_php_time_limit::raise();
    raise_memory_limit(MEMORY_EXTRA);
    $exportplugin = $CFG->dirroot . '/blocks/configurable_reports/export/' . $format . '/export.php';
    if (file_exists($exportplugin)) {
        require_once($exportplugin);
        export_report($reportclass->finalreport);
    }
    die;
}

// Never reached if download = true.
echo $OUTPUT->footer();
