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

if (!$report = $DB->get_record('block_configurable_reports', ['id' => $id])) {
    throw new moodle_exception('reportdoesnotexists', 'block_configurable_reports');
}

if (!$course = $DB->get_record('course', ['id' => $report->courseid])) {
    throw new moodle_exception('nosuchcourseid', 'block_configurable_reports');
}

// Force user login in course (SITE or Course).
if ((int) $course->id === SITEID) {
    require_login();
    $context = context_system::instance();
} else {
    require_login($course->id);
    $context = context_course::instance($course->id);
}

$PAGE->set_context($context);

if (!has_capability('block/configurable_reports:managereports', $context) &&
    !(has_capability('block/configurable_reports:manageownreports', $context) && $report->ownerid == $USER->id)) {
    throw new moodle_exception('badpermissions', 'block_configurable_reports');
}

if (!confirm_sesskey()) {
    throw new moodle_exception('badpermissions', 'block_configurable_reports');
}

$downloadfilename = clean_filename(format_string($report->name)) . '.xml';

$version = $DB->get_field('config_plugins', 'value', ['plugin' => 'block_configurable_reports', 'name' => 'version']);
if (!$version && !$version = $DB->get_field('block', 'version', ['name' => 'configurable_reports'])) {
    throw new moodle_exception('Plugin not found');
}

$data = '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";
$data .= "<report version=\"$version\">";

$reportdata = (array) $report;
unset($reportdata['id'], $reportdata['courseid'], $reportdata['ownerid']);

$reportdata['components'] = base64_encode($reportdata['components']);

foreach ($reportdata as $key => $value) {
    $data .= "<$key><![CDATA[$value]]></$key>\n";
}

$data .= "</report>";

if (strpos($CFG->wwwroot, 'https://') === 0) {
    // Https sites - watch out for IE! KB812935 and KB316431.
    @header('Cache-Control: max-age=10');
    @header('Expires: ' . gmdate('D, d M Y H:i:s', 0) . ' GMT');
    @header('Pragma: ');
} else {
    // Normal http - prevent caching at all cost.
    @header('Cache-Control: private, must-revalidate, pre-check=0, post-check=0, max-age=0');
    @header('Expires: ' . gmdate('D, d M Y H:i:s', 0) . ' GMT');
    @header('Pragma: no-cache');
}
header("Content-type: text/xml; charset=UTF-8");
header("Content-Disposition: attachment; filename=\"$downloadfilename\"");

print($data);
