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
 * Script to view a particular custom SQL report.
 *
 * @package report_lsusql
 * @copyright 2009 The Open University
 * @copyright 2022 Louisiana State University
 * @copyright 2022 Robert Russo
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/locallib.php');
require_once(dirname(__FILE__) . '/view_form.php');
require_once($CFG->libdir . '/adminlib.php');

$id = required_param('id', PARAM_INT);
$urlparams = ['id' => $id];
$report = $DB->get_record('report_lsusql_queries', array('id' => $id));
if (!$report) {
    throw new moodle_exception('invalidreportid', 'report_lsusql', report_lsusql_url('index.php'), $id);
}

$permittedusers = !empty($report->userlimit) ? array_map('trim', explode(',', $report->userlimit)) : array($USER->username);

$category = $DB->get_record('report_lsusql_categories', ['id' => $report->categoryid], '*', MUST_EXIST);
$mainurl = $CFG->wwwroot . '/report/lsusql/index.php';

$embed = optional_param('embed', 0, PARAM_BOOL);
$urlparams['embed'] = $embed;

// Setup the page.
admin_externalpage_setup('report_lsusql', '', $urlparams,
        '/report/lsusql/view.php', ['pagelayout' => 'report']);
$PAGE->set_title(format_string($report->displayname));
$PAGE->navbar->add(format_string($category->name), report_lsusql_url('category.php', ['id' => $report->categoryid]));
$PAGE->navbar->add(format_string($report->displayname));

if ($embed) {
    $PAGE->set_pagelayout('embedded');
}

$output = $PAGE->get_renderer('report_lsusql');

$context = context_system::instance();
if (!empty($report->capability)) {
    require_capability($report->capability, $context);

    $alloweduser = $report->capability == 'report/lsusql:view'
               ? has_capability($report->capability, $context)
                   && in_array ($USER->username, $permittedusers)
                   || is_siteadmin($USER->id)
               : has_capability($report->capability, $context)
                   || is_siteadmin($USER->id);
    if (!$alloweduser) {
        redirect($mainurl, get_string('noaccess', 'report_lsusql'), 5);
    }
}

report_lsusql_log_view($id);

// We don't want slow reports blocking the session in other tabs.
\core\session\manager::write_close();

if ($report->runable == 'manual') {

    // Allow query parameters to be entered.
    if (!empty($report->queryparams)) {
        $queryparams = report_lsusql_get_query_placeholders_and_field_names($report->querysql);

        // Get any query param values that are given in the URL.
        $paramvalues = [];
        foreach ($queryparams as $queryparam => $notused) {
            $value = optional_param($queryparam, null, PARAM_RAW);
            if ($value !== null && $value !== '') {
                $paramvalues[$queryparam] = $value;
            }
        }

        $relativeurl = 'view.php?id=' . $id;
        $mform = new report_lsusql_view_form(report_lsusql_url($relativeurl), $queryparams);
        $formdefaults = [];
        if ($report->queryparams) {
            foreach (unserialize($report->queryparams) as $queryparam => $defaultvalue) {
                $formdefaults[$queryparams[$queryparam]] = $defaultvalue;
            }
        }
        foreach ($paramvalues as $queryparam => $value) {
            $formdefaults[$queryparams[$queryparam]] = $value;
        }
        $mform->set_data($formdefaults);

        if ($mform->is_cancelled()) {
            redirect(report_lsusql_url('index.php'));
        }

        if (($newreport = $mform->get_data()) || count($paramvalues) == count($queryparams)) {

            // Pick up named parameters into serialised array.
            if ($newreport) {
                foreach ($queryparams as $queryparam => $formparam) {
                    $paramvalues[$queryparam] = $newreport->{$formparam};
                }
            }
            $report->queryparams = serialize($paramvalues);

        } else {

            admin_externalpage_setup('report_lsusql', '', $urlparams,
                    '/report/lsusql/view.php');
            $PAGE->set_title(format_string($report->displayname));
            echo $OUTPUT->header();
            echo $OUTPUT->heading(format_string($report->displayname));
            if (!html_is_blank($report->description)) {
                echo html_writer::tag('p', format_text($report->description, FORMAT_HTML));
            }
            $mform->display();

            echo $output->render_report_actions($report, $category, $context);

            echo $OUTPUT->footer();
            die;
        }
    }

    try {
        $csvtimestamp = report_lsusql_generate_csv($report, time(), false);
        // Get the updated execution times.
        $report = $DB->get_record('report_lsusql_queries', array('id' => $id));
    } catch (Exception $e) {
        throw new moodle_exception('queryfailed', 'report_lsusql', report_lsusql_url('index.php'),
                    $e->getMessage());
    }
} else {
    // Runs on schedule.
    $csvtimestamp = optional_param('timestamp', null, PARAM_INT);
    if ($csvtimestamp === null) {
        $archivetimes = report_lsusql_get_archive_times($report);
        $csvtimestamp = array_shift($archivetimes);
    }
    if ($csvtimestamp === null) {
        $csvtimestamp = time();
    }
    $urlparams['timestamp'] = $csvtimestamp;
}

// Output.
echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($report->displayname));

if (!html_is_blank($report->description)) {
    echo html_writer::tag('p', format_text($report->description, FORMAT_HTML));
}

if (!empty($paramvalues)) {
    foreach ($paramvalues as $name => $value) {
        if (report_lsusql_get_element_type($name) == 'date_time_selector') {
            $value = userdate($value, '%F %T');
        }
        echo html_writer::tag('p', get_string('parametervalue', 'report_lsusql',
                array('name' => html_writer::tag('b', str_replace('_', ' ', $name)),
                'value' => s($value))));
    }
}

$count = 0;
if (is_null($csvtimestamp)) {
    echo html_writer::tag('p', get_string('nodatareturned', 'report_lsusql'));
} else {
    list($csvfilename, $csvtimestamp) = report_lsusql_csv_filename($report, $csvtimestamp);
    if (!is_readable($csvfilename)) {
        echo html_writer::tag('p', get_string('notrunyet', 'report_lsusql'));
    } else {
        $handle = fopen($csvfilename, 'r');

        if ($report->runable != 'manual' && !$report->singlerow) {
            echo $OUTPUT->heading(get_string('reportfor', 'report_lsusql',
                    userdate($csvtimestamp, get_string('strftimedate'))), 3);
        }

        $table = new html_table();
        $table->id = 'report_lsusql_results';
        list($table->head, $linkcolumns) = report_lsusql_get_table_headers(
                report_lsusql_read_csv_row($handle));

        $rowlimitexceeded = false;
        while ($row = report_lsusql_read_csv_row($handle)) {
            $data = report_lsusql_display_row($row, $linkcolumns);
            if (isset($data[0]) && $data[0] === REPORT_LSUSQL_LIMIT_EXCEEDED_MARKER) {
                $rowlimitexceeded = true;
            } else {
                $table->data[] = $data;
                $count += 1;
            }
        }

        // For scheduled reports that accumulate one row at a time,
        // show most recent data first.
        if ($report->runable != 'manual' && $report->singlerow) {
            $table->data = array_reverse($table->data);
        }

        fclose($handle);
        echo html_writer::table($table);

        if ($rowlimitexceeded) {
            echo html_writer::tag('p', get_string('recordlimitreached', 'report_lsusql',
                    $report->querylimit ?? get_config('report_lsusql', 'querylimitdefault')),
                    array('class' => 'admin_note'));
        } else {
            echo html_writer::tag('p', get_string('recordcount', 'report_lsusql', $count),
                    array('class' => 'admin_note'));
        }

        echo report_lsusql_time_note($report, 'p');

        $urlparams = [];
        if (!empty($paramvalues)) {
            $urlparams = $paramvalues;
        }

        $urlparams['timestamp'] = $csvtimestamp;

        // Instantiate the LSUSQL downloader.
        $downloadurl = report_lsusql_downloadurl($id, $urlparams);

        $lsusqlselector = download_lsusql_dataformat_selector(get_string('downloadthisreportas', 'report_lsusql'),
            $downloadurl, 'dataformat', $urlparams);

        // Output the LSUSQL downloader.
        echo $lsusqlselector;
    }
}

if (!empty($queryparams)) {
    echo html_writer::tag('p',
            $OUTPUT->action_link(
                    report_lsusql_url('view.php', ['id' => $id]),
                    $OUTPUT->pix_icon('t/editstring', '') . ' ' .
                    get_string('changetheparameters', 'report_lsusql')));
}

echo $output->render_report_actions($report, $category, $context);

$archivetimes = report_lsusql_get_archive_times($report);
if (count($archivetimes) > 1) {
    echo $OUTPUT->heading(get_string('archivedversions', 'report_lsusql'), 3).
            html_writer::start_tag('ul');
    foreach ($archivetimes as $time) {
        $formattedtime = userdate($time, get_string('strftimedate'));
        echo html_writer::start_tag('li');
        if ($time == $csvtimestamp) {
            echo html_writer::tag('b', $formattedtime);
        } else {
            echo html_writer::tag('a', $formattedtime,
                    array('href' => report_lsusql_url('view.php',
                            ['id' => $id, 'timestamp' => $time])));
        }
        echo '</li>';
    }
    echo html_writer::end_tag('ul');
}

echo $OUTPUT->footer();
