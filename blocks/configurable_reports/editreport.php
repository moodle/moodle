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

$id = optional_param('id', 0, PARAM_INT);
$courseid = optional_param('courseid', SITEID, PARAM_INT);
$delete = optional_param('delete', 0, PARAM_BOOL);
$confirm = optional_param('confirm', 0, PARAM_BOOL);
$show = optional_param('show', 0, PARAM_BOOL);
$hide = optional_param('hide', 0, PARAM_BOOL);
$duplicate = optional_param('duplicate', 0, PARAM_BOOL);

$report = null;

if ($id) {
    $courseid = $DB->get_field('block_configurable_reports', 'courseid', ['id' => $id]);
}

if (!$course = $DB->get_record('course', ['id' => $courseid])) {
    throw new moodle_exception('nosuchcourseid', 'block_configurable_reports');
}

// Force user login in course (SITE or Course).
if ($course->id == SITEID) {
    require_login();
    $context = context_system::instance();
} else {
    require_login($course->id);
    $context = context_course::instance($course->id);
}

$hasmanagereportcap = has_capability('block/configurable_reports:managereports', $context);
if (!$hasmanagereportcap && !has_capability('block/configurable_reports:manageownreports', $context)) {
    throw new moodle_exception('badpermissions', 'block_configurable_reports');
}

$PAGE->set_context($context);
$PAGE->set_pagelayout('incourse');

if ($id) {
    if (!$report = $DB->get_record('block_configurable_reports', ['id' => $id])) {
        throw new moodle_exception('reportdoesnotexists', 'block_configurable_reports');
    }

    if (!$hasmanagereportcap && $report->ownerid != $USER->id) {
        throw new moodle_exception('badpermissions', 'block_configurable_reports');
    }
    // Extra check.
    if ($report->type == 'sql' && !block_configurable_reports_can_managesqlreports($context)) {
        throw new moodle_exception('nosqlpermissions');
    }

    $title = format_string($report->name);

    $courseid = $report->courseid;
    if (!$course = $DB->get_record('course', ['id' => $courseid])) {
        throw new moodle_exception('nosuchcourseid', 'block_configurable_reports');
    }

    require_once($CFG->dirroot . '/blocks/configurable_reports/report.class.php');
    require_once($CFG->dirroot . '/blocks/configurable_reports/reports/' . $report->type . '/report.class.php');
    $reportclassname = 'report_' . $report->type;
    $reportclass = new $reportclassname($report->id);
    $PAGE->set_url('/blocks/configurable_reports/editreport.php', ['id' => $id]);
} else {
    $title = get_string('newreport', 'block_configurable_reports');
    $PAGE->set_url('/blocks/configurable_reports/editreport.php', null);
}

if ($report) {
    $title = format_string($report->name);
} else {
    $title = get_string('report', 'block_configurable_reports');
}

$courseurl = new moodle_url($CFG->wwwroot . '/course/view.php', ['id' => $courseid]);
$PAGE->navbar->add($course->shortname, $courseurl);

if (!empty($report->courseid)) {
    $params = ['courseid' => $report->courseid];
} else {
    $params = ['courseid' => $courseid];
}

$managereporturl = new moodle_url($CFG->wwwroot . '/blocks/configurable_reports/managereport.php', $params);
$PAGE->navbar->add(get_string('managereports', 'block_configurable_reports'), $managereporturl);

$PAGE->navbar->add($title);

// Common actions.
if (($show || $hide) && confirm_sesskey()) {
    $visible = ($show) ? 1 : 0;
    if (!$DB->set_field('block_configurable_reports', 'visible', $visible, ['id' => $report->id])) {
        throw new moodle_exception('cannotupdatereport', 'block_configurable_reports');
    }
    $action = ($visible) ? 'showed' : 'hidden';

    header("Location: $CFG->wwwroot/blocks/configurable_reports/managereport.php?courseid=$courseid");
    die;
}

if ($duplicate && confirm_sesskey()) {
    $newreport = $report;
    unset($newreport->id);
    $newreport->name = get_string('copyasnoun') . ' ' . $newreport->name;
    if (!$newreportid = $DB->insert_record('block_configurable_reports', $newreport)) {
        throw new moodle_exception('cannotduplicate', 'block_configurable_reports');
    }

    header("Location: $CFG->wwwroot/blocks/configurable_reports/managereport.php?courseid=$courseid");
    die;
}

if ($delete && confirm_sesskey()) {
    if (!$confirm) {
        $PAGE->set_title($title);
        $PAGE->set_heading($title);
        $PAGE->set_cacheable(true);
        echo $OUTPUT->header();
        $message = get_string('confirmdeletereport', 'block_configurable_reports');
        $optionsyes = ['id' => $report->id, 'delete' => $delete, 'sesskey' => sesskey(), 'confirm' => 1];
        $optionsno = [];
        $buttoncontinue = new single_button(new moodle_url('editreport.php', $optionsyes), get_string('yes'), 'get');
        $buttoncancel = new single_button(new moodle_url('managereport.php', $optionsno), get_string('no'), 'get');
        echo $OUTPUT->confirm($message, $buttoncontinue, $buttoncancel);
        echo $OUTPUT->footer();
        exit;
    }

    $DB->delete_records('block_configurable_reports', ['id' => $report->id]);
    header("Location: $CFG->wwwroot/blocks/configurable_reports/managereport.php?courseid=$courseid");
    die;
}

require_once('editreport_form.php');

if (!empty($report)) {
    $editform = new report_edit_form('editreport.php', compact('report', 'courseid', 'context'));
} else {
    $editform = new report_edit_form('editreport.php', compact('courseid', 'context'));
}

if (!empty($report)) {
    $export = explode(',', $report->export);
    if (!empty($export)) {
        foreach ($export as $e) {
            $report->{'export_' . $e} = 1;
        }
    }
    $editform->set_data($report);
}

if ($editform->is_cancelled()) {
    if (!empty($report)) {
        redirect($CFG->wwwroot . '/blocks/configurable_reports/editreport.php?id=' . $report->id);
    }

    redirect($CFG->wwwroot . '/blocks/configurable_reports/editreport.php');

} else if ($data = $editform->get_data()) {
    require_once($CFG->dirroot . '/blocks/configurable_reports/report.class.php');
    require_once($CFG->dirroot . '/blocks/configurable_reports/reports/' . $data->type . '/report.class.php');
    if (empty($report)) {
        $reportclassname = 'report_' . $data->type;
    } else {
        $reportclassname = 'report_' . $report->type;
    }

    $arraydata = (array) $data;
    $data->export = '';
    foreach ($arraydata as $key => $d) {
        if (strpos($key, 'export_') !== false) {
            $data->export .= str_replace('export_', '', $key) . ',';
        }
    }

    if (!isset($data->global)) {
        $data->global = 0;
    }

    if (!isset($data->jsordering)) {
        $data->jsordering = 0;
    }

    if (!isset($data->remote)) {
        $data->remote = 0;
    }

    if (!isset($data->cron)) {
        $data->cron = 0;
    }

    if (!isset($data->displaytotalrecords)) {
        $data->displaytotalrecords = 0;
    }

    if (!isset($data->displayprintbutton)) {
        $data->displayprintbutton = 0;
    }

    if (empty($report)) {
        $data->ownerid = $USER->id;
        $data->courseid = $courseid;
        $data->visible = 1;
        $data->components = '';

        // Extra check.
        if ($data->type === 'sql' && !block_configurable_reports_can_managesqlreports($context)) {
            throw new moodle_exception('nosqlpermissions');
        }

        if (!$lastid = $DB->insert_record('block_configurable_reports', $data)) {
            throw new moodle_exception('errorsavingreport', 'block_configurable_reports');
        }

        $reportclass = new $reportclassname($lastid);
        redirect(
            $CFG->wwwroot . '/blocks/configurable_reports/editcomp.php?id=' . $lastid . '&comp=' . $reportclass->components[0]
        );
    } else {

        $reportclass = new $reportclassname($data->id);
        $data->type = $report->type;

        if (!$DB->update_record('block_configurable_reports', $data)) {
            throw new moodle_exception('errorsavingreport', 'block_configurable_reports');
        }

        redirect(
            $CFG->wwwroot . '/blocks/configurable_reports/editcomp.php?id=' . $data->id . '&comp=' . $reportclass->components[0]
        );
    }
}

$PAGE->set_context($context);

$PAGE->set_pagelayout('incourse');

$PAGE->set_title($title);

$PAGE->set_heading($title);

$PAGE->set_cacheable(true);

echo $OUTPUT->header();

if ($id) {
    $currenttab = 'report';
    include('tabs.php');
}

$editform->display();

echo $OUTPUT->footer();
