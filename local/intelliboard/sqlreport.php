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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intelliboard
 * @copyright  2018 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    https://intelliboard.net/
 */

require('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot .'/local/intelliboard/locallib.php');
require_once($CFG->dirroot.'/local/intelliboard/output/forms/local_intelliboard_sqlreport.php');

require_login();
admin_externalpage_setup('intelliboardsql');

if (!is_siteadmin()) {
    throw new moodle_exception('invalidaccess', 'error');
}
if (isset($CFG->intelliboardsql) and $CFG->intelliboardsql == false) {
    throw new moodle_exception('invalidaccess', 'error');
}

$id = required_param('id', PARAM_INT);
$delete    = optional_param('delete', 0, PARAM_BOOL);
$confirm   = optional_param('confirm', 0, PARAM_BOOL);
$returnurl = new moodle_url('/local/intelliboard/sqlreports.php');

if (!$data = $DB->get_record("local_intelliboard_reports", array('id'=>$id))) {
    throw new moodle_exception('invalidaccess', 'error');
}

if ($delete and $data->id) {
    $PAGE->url->param('delete', 1);
    if ($confirm and confirm_sesskey()) {
        $intelliboard = intelliboard(['task'=>'delete', 'id'=>$data->appid], 'sql');

        $DB->delete_records('local_intelliboard_reports', array('id'=>$data->id));
        redirect($returnurl, get_string('remove_message', 'local_intelliboard'));
    }
    $strheading = get_string('delete');
    $PAGE->navbar->add($strheading);
    echo $OUTPUT->header();
    echo $OUTPUT->heading($strheading);
    $yesurl = new \moodle_url(
        '/local/intelliboard/sqlreport.php',
        array('id'=>$data->id, 'delete'=>1, 'confirm'=>1,'sesskey'=>sesskey())
    );
    $message = get_string('delete_message', 'local_intelliboard');
    echo $OUTPUT->confirm($message, $yesurl, $returnurl);
    echo $OUTPUT->footer();
    die;
}

$editform = new local_intelliboard_sqlreport_form(null, array('data'=>$data));
if ($editform->is_cancelled()) {
    redirect($returnurl);
} else if ($sql = $editform->get_data()) {
    unset($sql->sqlcode);

    $DB->update_record('local_intelliboard_reports', $sql);
    $intelliboard = intelliboard(['task'=>'save', 'id'=>$data->appid, 'status'=>$sql->status], 'sql');

    redirect($returnurl, get_string('success_message', 'local_intelliboard'));
}

$intelliboard = intelliboard(['task'=>'sqlreport']);

echo $OUTPUT->header();

echo $editform->display();

echo $OUTPUT->footer();
