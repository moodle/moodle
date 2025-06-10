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
 * @package report_lsusql
 * @copyright 2009 The Open University
 * @copyright 2022 Louisiana State University
 * @copyright 2022 Robert Russo
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/locallib.php');
require_once(dirname(__FILE__) . '/edit_form.php');
require_once($CFG->libdir . '/adminlib.php');

$id = optional_param('id', 0, PARAM_INT);
$categoryid = optional_param('categoryid', 0, PARAM_INT);
$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$urlparams = [];
if ($id) {
    $urlparams['id'] = $id;
}
if ($categoryid) {
    $urlparams['categoryid'] = $categoryid;
}

admin_externalpage_setup('report_lsusql', '', $urlparams, '/report/lsusql/edit.php');
$context = context_system::instance();
require_capability('report/lsusql:definequeries', $context);

$relativeurl = 'edit.php';
$report = null;
$reportquerysql = '';
$params = [];

if (!empty($returnurl)) {
    $returnurl = new moodle_url($returnurl);
    $params['returnurl'] = $returnurl->out_as_local_url(false);
}

// Are we editing an existing report, or creating a new one.
if ($id) {
    $report = $DB->get_record('report_lsusql_queries', array('id' => $id));
    if (!$report) {
        throw new moodle_exception('invalidreportid', 'report_lsusql', report_lsusql_url('index.php'), $id);
    }
    $reportquerysql = $report->querysql;
    $queryparams = !empty($report->queryparams) ? unserialize($report->queryparams) : array();
    foreach ($queryparams as $param => $value) {
        $report->{'queryparam'.$param} = $value;
    }
    $params['id'] = $id;
    $category = $DB->get_record('report_lsusql_categories', ['id' => $report->categoryid], '*', MUST_EXIST);
    $PAGE->navbar->add(format_string($category->name), report_lsusql_url('category.php', ['id' => $category->id]));
    $PAGE->navbar->add(format_string($report->displayname));
} else {
    // If we add new query in a category, add a breadcrumb for it.
    if ($categoryid) {
        $category = $DB->get_record('report_lsusql_categories', ['id' => $categoryid], '*', MUST_EXIST);
        $PAGE->navbar->add(format_string($category->name), report_lsusql_url('category.php', ['id' => $category->id]));
    }
    $PAGE->navbar->add(get_string('addreport', 'report_lsusql'));
}

$querysql = optional_param('querysql', $reportquerysql, PARAM_RAW);
$queryparams = report_lsusql_get_query_placeholders_and_field_names($querysql);
$customdata = ['queryparams' => $queryparams, 'forcecategoryid' => $categoryid];

$mform = new report_lsusql_edit_form(report_lsusql_url($relativeurl, $params), $customdata);

if ($mform->is_cancelled()) {
    if ($returnurl) {
        redirect($returnurl);
    } else {
        redirect(report_lsusql_url('index.php'));
    }
}

if ($newreport = $mform->get_data()) {
    $newreport->descriptionformat = $newreport->description['format'];
    $newreport->description = $newreport->description['text'];

    // Currently, autocomplete can return an empty value in the array. If we get one, strip it out.
    $newreport->emailto = trim(implode(',', $newreport->emailto), ',');

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

    $newreport->usermodified = $USER->id;
    $newreport->timemodified = \report_lsusql\utils::time();
    if ($id) {
        $newreport->id = $id;
        if (empty($report->timemodified)) {
            $newreport->timecreated = $newreport->timemodified;
        }
        $ok = $DB->update_record('report_lsusql_queries', $newreport);
        if (!$ok) {
            throw new moodle_exception('errorupdatingreport', 'report_lsusql',
                        report_lsusql_url('edit.php?id=' . $id));
        }

    } else {
        $newreport->timecreated = $newreport->timemodified;
        $id = $DB->insert_record('report_lsusql_queries', $newreport);
        if (!$id) {
            throw new moodle_exception('errorinsertingreport', 'report_lsusql',
                        report_lsusql_url('edit.php'));
        }
    }

    report_lsusql_log_edit($id);
    if ($newreport->runable == 'manual') {
        redirect(report_lsusql_url('view.php?id=' . $id));
    } else if ($returnurl) {
        redirect($returnurl);
    } else {
        redirect(report_lsusql_url('index.php'));
    }
}

admin_externalpage_setup('report_lsusql');
echo $OUTPUT->header();

if ($id) {
    echo $OUTPUT->heading(get_string('editingareport', 'report_lsusql'));
} else {
    echo $OUTPUT->heading(get_string('addingareport', 'report_lsusql'));
}

if ($report) {
    $report->description = array('text' => $report->description, 'format' => $report->descriptionformat);
    $mform->set_data($report);
}

$mform->display();

echo $OUTPUT->footer();
