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
 * Download Kaltura logs page.
 *
 * @package    local_kaltura
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

require_once('../../config.php');
require_once($CFG->dirroot.'/local/kaltura/download_log_form.php');

global $DB;

$url = new moodle_url('/mod/lti/instructor_edit_tool_type.php');
$context = context_system::instance();
$heading = get_string('download_logs_title', 'local_kaltura');
$site = get_site();

$PAGE->navbar->add(get_string('administrationsite'));
$PAGE->navbar->add(get_string('plugins', 'admin'));
$PAGE->navbar->add(get_string('localplugins'));
$PAGE->navbar->add(get_string('pluginname', 'local_kaltura'), new moodle_url('/admin/settings.php', array('section' => 'local_kaltura')));
$PAGE->navbar->add(get_string('download_logs_title', 'local_kaltura'));
$PAGE->set_url($url);
$PAGE->set_context($context);

$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_pagetype('local-kaltura-download-log');
$PAGE->set_title($heading);
$PAGE->set_heading($site->fullname);

require_login(null, false);

require_capability('local/kaltura:download_trace_logs', $context);

$url = new moodle_url('/admin/settings.php', array('section' => 'local_kaltura'));
$downloadurl = new moodle_url('/local/kaltura/download_log.php');

$form = new local_kaltura_download_log_form();
if ($data = $form->get_data()) {
    // User hit cancel. Redirect them back to the settings page.
    if (isset($data->cancel)) {
        redirect($url);
    }

    require_sesskey();

    // User hit submit button.  Check for records since the configured date.
    if (isset($data->submitbutton)) {
        $rs = $DB->get_recordset_select('local_kaltura_log', 'timecreated >= ?', array($data->logs_start_time), 'timecreated ASC');

        // Check if the recordset contains any data.
        if ($rs->valid()) {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=kalturalogs.csv');

            // create a file pointer connected to the output stream
            $output = fopen('php://output', 'w');

            // output the column headings
            fputcsv($output, array(get_string('request', 'local_kaltura'), get_string('time', 'local_kaltura'),
                get_string('module', 'local_kaltura'), get_string('endpoint', 'local_kaltura'), get_string('data', 'local_kaltura')));

            foreach ($rs as $record) {
                $record->data = json_encode(unserialize($record->data));
                fputcsv($output, array($record->type, userdate($record->timecreated), $record->module, $record->endpoint, $record->data));
            }

            $rs->close();
            die();
        } else {
            notice(get_string('no_records', 'local_kaltura'), $downloadurl);
        }
    }

    if (isset($data->deletelogs)) {
        $DB->delete_records_select('local_kaltura_log', 'id > 0');
        notice(get_string('records_deleted', 'local_kaltura'), $downloadurl);
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('download_logs_title', 'local_kaltura'));
$form->display();
echo $OUTPUT->footer();
