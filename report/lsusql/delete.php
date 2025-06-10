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
 * Script to delete a particular custom SQL report, with confirmation.
 *
 * @package report_lsusql
 * @copyright 2009 The Open University
 * @copyright 2022 Louisiana State University
 * @copyright 2022 Robert Russo
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/locallib.php');
require_once($CFG->libdir . '/adminlib.php');

$id = required_param('id', PARAM_INT);
$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);

admin_externalpage_setup('report_lsusql', '', ['id' => $id],
        '/report/lsusql/delete.php');
$context = context_system::instance();
require_capability('report/lsusql:definequeries', $context);

$report = $DB->get_record('report_lsusql_queries', array('id' => $id));
if (!$report) {
    throw new moodle_exception('invalidreportid', 'report_lsusql', report_lsusql_url('index.php'), $id);
}

if ($returnurl) {
    $returnurl = new moodle_url($returnurl);
} else {
    $returnurl = report_lsusql_url('category.php', ['id' => $report->categoryid]);
}

if (optional_param('confirm', false, PARAM_BOOL)) {
    $ok = $DB->delete_records('report_lsusql_queries', array('id' => $id));
    if (!$ok) {
        throw new moodle_exception('errordeletingreport', 'report_lsusql', report_lsusql_url('index.php'));
    }
    report_lsusql_log_delete($id);

    // We can not return to the view report page because the report is deleted.
    if (strpos($returnurl, 'report/lsusql/view.php?id=' . $id)) {
        redirect(report_lsusql_url('category.php', ['id' => $report->categoryid]));
    } else {
        redirect($returnurl);
    }
}

$runnableoptions = report_lsusql_runable_options();

// Start the page.
echo $OUTPUT->header().
     $OUTPUT->heading(get_string('deleteareyousure', 'report_lsusql')).

     html_writer::tag('p', get_string('displaynamex', 'report_lsusql',
                                      html_writer::tag('b', format_string($report->displayname)))).
     html_writer::tag('p', get_string('querysql', 'report_lsusql')).
     html_writer::tag('pre', htmlspecialchars($report->querysql)).
     html_writer::tag('p', get_string('runablex', 'report_lsusql',
                      $runnableoptions[$report->runable])).

     $OUTPUT->confirm(get_string('deleteareyousure', 'report_lsusql'),
                      new single_button(report_lsusql_url('delete.php',
                                        ['id' => $id, 'confirm' => 1, 'returnurl' => $returnurl->out_as_local_url(false)]),
                                        get_string('yes')),
                      new single_button($returnurl, get_string('no'))).

     $OUTPUT->footer();
