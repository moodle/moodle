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
 * Performance overview report
 *
 * @package   report_performance
 * @copyright 2013 Rajesh Taneja
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require('../../config.php');
require_once($CFG->dirroot.'/report/performance/locallib.php');
require_once($CFG->libdir.'/adminlib.php');


// Show detailed info about one issue only.
$issue = optional_param('issue', '', PARAM_ALPHANUMEXT);

$reportperformance = new report_performance();
$issues = $reportperformance->get_issue_list();

// Test if issue valid string.
if (array_search($issue, $issues, true) === false) {
    $issue = '';
}

// Print the header.
admin_externalpage_setup('reportperformance', '', null, '', array('pagelayout'=>'report'));
echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('pluginname', 'report_performance'));

$strissue = get_string('issue', 'report_performance');
$strvalue = get_string('value', 'report_performance');
$strcomments = get_string('comments', 'report_performance');
$stredit = get_string('edit');

$table = new html_table();
$table->head  = array($strissue, $strvalue, $strcomments, $stredit);
$table->colclasses = array('mdl-left issue', 'mdl-left value', 'mdl-left comments', 'mdl-left config');
$table->attributes = array('class' => 'admintable performancereport generaltable');
$table->id = 'performanceissuereporttable';
$table->data  = array();

// Print details of one issue only.
if ($issue and ($issueresult = $reportperformance::$issue())) {
    $reportperformance->add_issue_to_table($table, $issueresult, true);

    $PAGE->set_docs_path('report/security/' . $issue);

    echo html_writer::table($table);

    echo $OUTPUT->box($issueresult->details, 'generalbox boxwidthnormal boxaligncenter');

    echo $OUTPUT->continue_button(new moodle_url('/report/performance/index.php'));
} else {
    // Add Performance report description on main list page.
    $morehelplink = $OUTPUT->doc_link('report/performance', get_string('morehelp', 'report_performance'));
    echo $OUTPUT->box(get_string('performancereportdesc', 'report_performance', $morehelplink), 'generalbox mdl-align');

    foreach ($issues as $issue) {
        $issueresult = $reportperformance::$issue();
        if (!$issueresult) {
            // Ignore this test.
            continue;
        }
        $reportperformance->add_issue_to_table($table, $issueresult, false);
    }
    echo html_writer::table($table);
}

echo $OUTPUT->footer();
