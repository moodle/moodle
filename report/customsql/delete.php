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
 * @package report_customsql
 * @copyright 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/locallib.php');
require_once($CFG->libdir . '/adminlib.php');

$id = required_param('id', PARAM_INT);

admin_externalpage_setup('report_customsql', '', ['id' => $id],
        '/report/customsql/delete.php');
$context = context_system::instance();
require_capability('report/customsql:definequeries', $context);

$report = $DB->get_record('report_customsql_queries', array('id' => $id));
if (!$report) {
    print_error('invalidreportid', 'report_customsql', report_customsql_url('index.php'), $id);
}

if (optional_param('confirm', false, PARAM_BOOL)) {
    $ok = $DB->delete_records('report_customsql_queries', array('id' => $id));
    if (!$ok) {
        print_error('errordeletingreport', 'report_customsql', report_customsql_url('index.php'));
    }
    report_customsql_log_delete($id);
    redirect(report_customsql_url('index.php'));
}

$runnableoptions = report_customsql_runable_options();

// Start the page.
echo $OUTPUT->header().
     $OUTPUT->heading(get_string('deleteareyousure', 'report_customsql')).

     html_writer::tag('p', get_string('displaynamex', 'report_customsql',
                                      html_writer::tag('b', format_string($report->displayname)))).
     html_writer::tag('p', get_string('querysql', 'report_customsql')).
     html_writer::tag('pre', htmlspecialchars($report->querysql)).
     html_writer::tag('p', get_string('runablex', 'report_customsql',
                      $runnableoptions[$report->runable])).

     $OUTPUT->confirm(get_string('deleteareyousure', 'report_customsql'),
                      new single_button(new moodle_url(report_customsql_url('delete.php'),
                                        array('id' => $id, 'confirm' => 1)), get_string('yes')),
                      new single_button(new moodle_url(report_customsql_url('index.php')),
                                        get_string('no'))).

     $OUTPUT->footer();
