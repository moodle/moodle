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

/** LearnerScript Reports
 * A Moodle block for creating LearnerScript Reports
 * @package blocks
 * @author: eAbyas info solutions
 * @date: 2009
 */
require_once("../../config.php");

$id = required_param('id', PARAM_INT);

if (!$report = $DB->get_record('block_learnerscript', array('id' => $id)))
    print_error('reportdoesnotexists', 'block_learnerscript');


if (!$course = $DB->get_record("course", array("id" => $report->courseid))) {
    print_error("nosuchcourseid", 'block_learnerscript');
}

// Force user login in course (SITE or Course)
if ($course->id == SITEID) {
    require_login();
    $context = context_system::instance();
} else {
    require_login($course->id);
    $context = context_course::instance($course->id);
}

$PAGE->set_context($context);

if (!has_capability('block/learnerscript:managereports', $context) && !(has_capability('block/learnerscript:manageownreports', $context) && $report->ownerid == $USER->id))
    print_error('badpermissions', 'block_learnerscript');

if (!confirm_sesskey())
    print_error('badpermissions', 'block_learnerscript');

$downloadfilename = clean_filename(format_string($report->name)) . '.xml';
$version = $DB->get_field('config_plugins', 'value', array('plugin' => 'block_learnerscript', 'name' => 'version'));
if (!$version) {
    if (!$version = $DB->get_field('block', 'version', array('name' => 'learnerscript'))) {
        print_error(get_string('Pluginnotfound','block_learnerscript'));
    }
}

$data = '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";
$data .= "<report version=\"$version\">";

$reportdata = (array) $report;
unset($reportdata['id']);
unset($reportdata['courseid']);
unset($reportdata['ownerid']);
$reportdata['components'] = base64_encode($reportdata['components']);

foreach ($reportdata as $key => $value) {
    $data .= "<$key><![CDATA[$value]]></$key>\n";
}
$blockinstances = $DB->get_records_sql("SELECT * FROM {block_instances} WHERE pagetypepattern LIKE :pagetypepattern", ['pagetypepattern' => '%blocks-reportdashboard%']);
foreach($blockinstances as $bi) {
    if(empty($bi->configdata)) {
        continue;
    }
    $configdata = unserialize(base64_decode($bi->configdata));
    if($configdata->reportlist != $id) {
        continue;
    }
    if($configdata->reportlist == $id) {
        $data .= "<instance version=\"$version\">";
        $bidata = (array) $bi;
        unset($bidata['id']);
        unset($bidata['configdata']);
        foreach($bidata as $key => $value) {
            $data .= "<$key><![CDATA[$value]]></$key>\n";
        }
        $configdatalist = (array) $configdata;
        unset($configdatalist['reportlist']);
        foreach($configdatalist as $key => $value) {
            $data .= "<$key><![CDATA[$value]]></$key>\n";
        }
        $data .= "</instance>";
        $blockposition = $DB->get_record_sql('SELECT * FROM {block_positions} WHERE blockinstanceid = :blockinstanceid', array('blockinstanceid' => $bi->id));
        $data .= "<position version=\"$version\">";
        if(!empty($blockposition)) {
            $blockpositiondata = (array) $blockposition;
            unset($blockpositiondata['id']);
            unset($blockpositiondata['blockinstanceid']);
            foreach($blockpositiondata as $key => $value) {
                $data .= "<$key><![CDATA[$value]]></$key>\n";
            }
        }
        $data .= "</position>";
    }
}
$data .= "</report>";
if (strpos($CFG->wwwroot, 'https://') === 0) { //https sites - watch out for IE! KB812935 and KB316431
    @header('Cache-Control: max-age=10');
    @header('Expires: ' . gmdate('D, d M Y H:i:s', 0) . ' GMT');
    @header('Pragma: ');
} else { //normal http - prevent caching at all cost
    @header('Cache-Control: private, must-revalidate, pre-check=0, post-check=0, max-age=0');
    @header('Expires: ' . gmdate('D, d M Y H:i:s', 0) . ' GMT');
    @header('Pragma: no-cache');
}
header("Content-type: text/xml; charset=UTF-8");
header("Content-Disposition: attachment; filename=\"$downloadfilename\"");

print($data);