<?php  //$Id$

require('../../config.php');
require_once('lib.php');
require_once('database_transfer_form.php');

require_login();
admin_externalpage_setup('dbtransfer');

//create form
$form = new database_transfer_form();

if ($data = $form->get_data()) {
    list($dbtype, $dblibrary) = explode('/', $data->driver);
    $targetdb = moodle_database::get_driver_instance($dbtype, $dblibrary);
    if (!$targetdb->connect($data->dbhost, $data->dbuser, $data->dbpass, $data->dbname, false, $data->prefix, null)) {
        throw new dbtransfer_exception('notargetconectexception', null, "$CFG->wwwroot/$CFG->admin/dbtransfer/");
    }
    if ($targetdb->get_tables()) {
        // TODO add exception or string...
        error('Sorry, tables already exist in selected database. Can not continue.'); 
    }
    admin_externalpage_print_header();
    dbtransfer_transfer_database($DB, $targetdb);
    notify(get_string('success'), 'notifysuccess');
    print_continue("$CFG->wwwroot/$CFG->admin/");
    admin_externalpage_print_footer();
    die;
}

admin_externalpage_print_header();
// TODO: add some more info here
$form->display();
admin_externalpage_print_footer();
