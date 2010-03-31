<?php

require('../../config.php');
require_once('lib.php');
require_once('database_transfer_form.php');

require_login();
admin_externalpage_setup('dbtransfer');

// Create the form
$form = new database_transfer_form();

// If we have valid input.
if ($data = $form->get_data()) {
    // Connect to the other database.
    list($dbtype, $dblibrary) = explode('/', $data->driver);
    $targetdb = moodle_database::get_driver_instance($dbtype, $dblibrary);
    if (!$targetdb->connect($data->dbhost, $data->dbuser, $data->dbpass, $data->dbname, $data->prefix, null)) {
        throw new dbtransfer_exception('notargetconectexception', null, "$CFG->wwwroot/$CFG->admin/dbtransfer/");
    }
    if ($targetdb->get_tables()) {
        throw new dbtransfer_exception('targetdatabasenotempty', null, "$CFG->wwwroot/$CFG->admin/dbtransfer/");
    }

    // Start output.
    echo $OUTPUT->header();
    $data->dbtype = $dbtype;
    echo $OUTPUT->heading(get_string('transferringdbto', 'dbtransfer', $data));

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
echo $OUTPUT->heading(get_string('transferdbtoserver', 'dbtransfer'));
echo '<p>', get_string('transferdbintro', 'dbtransfer'), "</p>\n\n";
$form->display();
echo $OUTPUT->footer();
