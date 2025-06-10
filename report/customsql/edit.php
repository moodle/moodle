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
 * Script for editing a custom SQL report.
 *
 * @package report_customsql
 * @copyright 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/locallib.php');
require_once(dirname(__FILE__) . '/edit_form.php');
require_once($CFG->libdir . '/adminlib.php');

$id = optional_param('id', 0, PARAM_INT);
$urlparams = [];
if ($id) {
    $urlparams['id'] = $id;
}

admin_externalpage_setup('report_customsql', '', $urlparams, '/report/customsql/edit.php');
$context = context_system::instance();
require_capability('report/customsql:definequeries', $context);

$relativeurl = 'edit.php';
$report = null;
$reportquerysql = '';

// Are we editing an existing report, or creating a new one.
if ($id) {
    $report = $DB->get_record('report_customsql_queries', array('id' => $id));
    if (!$report) {
        print_error('invalidreportid', 'report_customsql', report_customsql_url('index.php'), $id);
    }
    $reportquerysql = $report->querysql;
    $queryparams = !empty($report->queryparams) ? unserialize($report->queryparams) : array();
    foreach ($queryparams as $param => $value) {
        $report->{'queryparam'.$param} = $value;
    }
    $relativeurl .= '?id=' . $id;
}

$querysql = optional_param('querysql', $reportquerysql, PARAM_RAW);
$queryparams = array();
foreach (report_customsql_get_query_placeholders($querysql) as $queryparam) {
    $queryparams[substr($queryparam, 1)] = 'queryparam' . substr($queryparam, 1);
}

$mform = new report_customsql_edit_form(report_customsql_url($relativeurl), $queryparams);

if ($mform->is_cancelled()) {
    redirect(report_customsql_url('index.php'));
}

if ($newreport = $mform->get_data()) {
    $newreport->descriptionformat = $newreport->description['format'];
    $newreport->description = $newreport->description['text'];

    // Set the following fields to empty strings if the report is running manually.
    if ($newreport->runable === 'manual') {
        $newreport->at = '';
        $newreport->emailto = '';
        $newreport->emailwhat = '';
        $newreport->customdir = '';
    }
    if ($newreport->runable == 'manual' || empty($newreport->singlerow)) {
        $newreport->singlerow = 0;
    }

    // Pick up named parameters into serialised array.
    if ($queryparams) {
        foreach ($queryparams as $queryparam => $formparam) {
            $queryparams[$queryparam] = $newreport->{$formparam};
            unset($newreport->{$formparam});
        }
        $newreport->queryparams = serialize($queryparams);
    } else {
        $newreport->queryparams = '';
    }

    if ($id) {
        $newreport->id = $id;
        $ok = $DB->update_record('report_customsql_queries', $newreport);
        if (!$ok) {
            print_error('errorupdatingreport', 'report_customsql',
                        report_customsql_url('edit.php?id=' . $id));
        }

    } else {
        $id = $DB->insert_record('report_customsql_queries', $newreport);
        if (!$id) {
            print_error('errorinsertingreport', 'report_customsql',
                        report_customsql_url('edit.php'));
        }
    }

    report_customsql_log_edit($id);
    if ($newreport->runable == 'manual') {
        redirect(report_customsql_url('view.php?id=' . $id));
    } else {
        redirect(report_customsql_url('index.php'));
    }
}

admin_externalpage_setup('report_customsql');
echo $OUTPUT->header().
     $OUTPUT->heading(get_string('editingareport', 'report_customsql'));

if ($report) {
    $report->description = array('text' => $report->description, 'format' => $report->descriptionformat);
    $mform->set_data($report);
}

$mform->display();

echo $OUTPUT->footer();
