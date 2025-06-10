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
 * Report page.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2022
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_intellidata\output\forms\local_intellidata_sql_report;
use local_intellidata\helpers\SettingsHelper;

require('../../../config.php');

require_login();

if (!is_siteadmin()) {
    throw new moodle_exception('invalidaccess', 'error');
}

$id      = required_param('id', PARAM_INT);
$delete  = optional_param('delete', 0, PARAM_BOOL);
$confirm = optional_param('confirm', 0, PARAM_BOOL);
$debug   = optional_param('debug', 0, PARAM_BOOL);

$PAGE->set_url(new \moodle_url('/local/intellidata/sql_reports/report.php', ['id' => $id, 'debug' => $debug]));
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout(SettingsHelper::get_page_layout());

$returnurl = new moodle_url('/local/intellidata/sql_reports/index.php');

if (!$report = $DB->get_record('local_intellidata_reports', ['id' => $id])) {
    throw new moodle_exception('invalidaccess', 'error');
}

$PAGE->navbar->add(get_string('sqlreports', 'local_intellidata'), new \moodle_url('/local/intellidata/sql_reports/index.php'));
$PAGE->navbar->add($report->name);
$PAGE->set_title($report->name);
$PAGE->set_heading($report->name);

if ($delete && $report->id) {
    $PAGE->url->param('delete', 1);

    if ($confirm && confirm_sesskey()) {
        $supernovasqlreportsapi = new \local_intellidata\tools\supernova_sql_reports_api($report, $debug);
        $supernovasqlreportsapi->delete();

        $DB->delete_records('local_intellidata_reports', ['id' => $report->id]);
        redirect($returnurl, get_string('sql_report_remove_message', 'local_intellidata'));
    }

    $strheading = get_string('delete');

    $PAGE->navbar->add($strheading);

    echo $OUTPUT->header();
    echo $OUTPUT->heading($strheading);
    echo $OUTPUT->confirm(
        get_string('sql_report_delete_message', 'local_intellidata'),
        new \moodle_url(
            '/local/intellidata/sql_reports/report.php',
            ['id' => $report->id, 'delete' => 1, 'confirm' => 1, 'sesskey' => sesskey(), 'debug' => $debug]
        ),
        $returnurl
    );
    echo $OUTPUT->footer();
    die;
}

$report->debug = $debug;
$editform = new local_intellidata_sql_report(null, ['data' => $report]);

if ($editform->is_cancelled()) {
    redirect($returnurl);
} else if ($sqlreportdata = $editform->get_data()) {
    $DB->update_record('local_intellidata_reports', [
        'id' => $sqlreportdata->id,
        'status' => $sqlreportdata->status,
        'name' => $sqlreportdata->name,
    ]);
    $supernovasqlreportsapi = new \local_intellidata\tools\supernova_sql_reports_api($report, $debug);
    $supernovasqlreportsapi->save([
        'status' => $sqlreportdata->status,
        'name' => $sqlreportdata->name,
    ]);

    redirect($returnurl, get_string('sql_report_success_message', 'local_intellidata'));
}

echo $OUTPUT->header();

echo $editform->display();

echo $OUTPUT->footer();
