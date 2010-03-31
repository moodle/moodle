<?php

require('../../config.php');
require_once('lib.php');
require_once('database_export_form.php');

require_login();
admin_externalpage_setup('dbexport');

//create form
$form = new database_export_form();

if ($data = $form->get_data()) {
    dbtransfer_export_xml_database($data->description, $DB);
    die;
}

echo $OUTPUT->header();
// TODO: add some more info here
$form->display();
echo $OUTPUT->footer();
