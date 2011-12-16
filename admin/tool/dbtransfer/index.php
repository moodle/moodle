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
 * Transfer tool
 *
 * @package    tool
 * @subpackage dbtransfer
 * @copyright  2008 Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require('../../../config.php');
require_once('locallib.php');
require_once('database_transfer_form.php');

require_login();
admin_externalpage_setup('tooldbtransfer');

// Create the form
$form = new database_transfer_form();

// If we have valid input.
if ($data = $form->get_data()) {
    // Connect to the other database.
    list($dbtype, $dblibrary) = explode('/', $data->driver);
    $targetdb = moodle_database::get_driver_instance($dbtype, $dblibrary);
    $dboptions = array();
    if ($data->dbport) {
        $dboptions['dbport'] = $data->dbport;
    }
    if ($data->dbsocket) {
        $dboptions['dbsocket'] = $data->dbsocket;
    }
    if (!$targetdb->connect($data->dbhost, $data->dbuser, $data->dbpass, $data->dbname, $data->prefix, $dboptions)) {
        throw new dbtransfer_exception('notargetconectexception', null, "$CFG->wwwroot/$CFG->admin/tool/dbtransfer/");
    }
    if ($targetdb->get_tables()) {
        throw new dbtransfer_exception('targetdatabasenotempty', null, "$CFG->wwwroot/$CFG->admin/tool/dbtransfer/");
    }

    // Start output.
    echo $OUTPUT->header();
    $data->dbtype = $dbtype;
    echo $OUTPUT->heading(get_string('transferringdbto', 'tool_dbtransfer', $data));

    // Do the transfer.
    $feedback = new html_list_progress_trace();
    dbtransfer_transfer_database($DB, $targetdb, $feedback);
    $feedback->finished();

    // Finish up.
    echo $OUTPUT->notification(get_string('success'), 'notifysuccess');
    echo $OUTPUT->continue_button("$CFG->wwwroot/$CFG->admin/");
    echo $OUTPUT->footer();
    die;
}

// Otherwise display the settings form.
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('transferdbtoserver', 'tool_dbtransfer'));
echo '<p>', get_string('transferdbintro', 'tool_dbtransfer'), "</p>\n\n";
$form->display();
echo $OUTPUT->footer();
