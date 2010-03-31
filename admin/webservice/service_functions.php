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
 * Web services function UI
 *
 * @package   webservice
 * @copyright 2009 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/externallib.php');
require_once('forms.php');

$id      = required_param('id', PARAM_INT);
$fid     = optional_param('fid', 0, PARAM_INT);
$action  = optional_param('action', '', PARAM_ACTION);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

$PAGE->set_url('/admin/websevice/service_functions.php', array('id'=>$id));
$PAGE->navbar->ignore_active(true);
$PAGE->navbar->add(get_string('administrationsite'));
$PAGE->navbar->add(get_string('plugins', 'admin'));
$PAGE->navbar->add(get_string('webservices', 'webservice'));
$PAGE->navbar->add(get_string('externalservices', 'webservice'), new moodle_url('/admin/settings.php?section=externalservices'));
$PAGE->navbar->add(get_string('functions', 'webservice'), new moodle_url('/admin/webservice/service_functions.php?id='.$id));
if ($action == "add") {
    $PAGE->navbar->add(get_string('addfunctions', 'webservice'));
} else if ($action == "delete") {
    $PAGE->navbar->add(get_string('removefunction', 'webservice'));
}

admin_externalpage_setup('externalservicefunctions');

$returnurl = "$CFG->wwwroot/$CFG->admin/settings.php?section=externalservices";
$thisurl   = "service_functions.php?id=$id";

$service = $DB->get_record('external_services', array('id'=>$id), '*', MUST_EXIST);

if ($action === 'delete' and confirm_sesskey() and $service and empty($service->component)) {
    $function = $DB->get_record('external_functions', array('id'=>$fid), '*', MUST_EXIST);
    if (!$confirm) {
        echo $OUTPUT->header();
        $optionsyes = array('id'=>$id, 'action'=>'delete', 'confirm'=>1, 'sesskey'=>sesskey(), 'fid'=>$function->id);
        $optionsno  = array('id'=>$id);
        $formcontinue = new single_button(new moodle_url('service_functions.php', $optionsyes), get_string('remove'));
        $formcancel = new single_button(new moodle_url('service_functions.php', $optionsno), get_string('cancel'), 'get');
        echo $OUTPUT->confirm(get_string('removefunctionconfirm', 'webservice', (object)array('service'=>$service->name, 'function'=>$function->name)), $formcontinue, $formcancel);
        echo $OUTPUT->footer();
        die;
    }
    $DB->delete_records('external_services_functions', array('externalserviceid'=>$service->id, 'functionname'=>$function->name));
    redirect($thisurl);

} else if ($action === 'add' and confirm_sesskey() and $service and empty($service->component)) {
    $mform = new external_service_functions_form(null, array('action'=>'add', 'id'=>$service->id));

    if ($mform->is_cancelled()) {
        redirect($thisurl);
    } else if ($data = $mform->get_data()) {
        ignore_user_abort(true); // no interruption here!
        foreach($data->fid as $fid) {
            $function = $DB->get_record('external_functions', array('id'=>$fid), '*', MUST_EXIST);
            // make sure the function is not there yet
            if (!$DB->record_exists('external_services_functions', array('externalserviceid'=>$service->id, 'functionname'=>$function->name))) {
                $new = new object();
                $new->externalserviceid = $service->id;
                $new->functionname      = $function->name;
                $DB->insert_record('external_services_functions', $new);
            }
        }
        redirect($thisurl);
    }

    //ask for function id
    echo $OUTPUT->header();
    echo $OUTPUT->heading($service->name);
    $mform->display();
    echo $OUTPUT->footer();
    die;
}

echo $OUTPUT->header();

echo $OUTPUT->heading($service->name);

$select = "name IN (SELECT s.functionname
                      FROM {external_services_functions} s
                     WHERE s.externalserviceid = :sid
                   )";

$functions = $DB->get_records_select('external_functions', $select, array('sid'=>$service->id), 'name');

$strfunction = get_string('function', 'webservice');
$strdelete = get_string('removefunction', 'webservice');
$stredit = get_string('edit');

$table = new html_table();
$table->head  = array($strfunction);
$table->align = array('left');
$table->width = '100%';
$table->data  = array();
$table->head[] = get_string('description');
$table->align[] = 'left';

if (empty($service->component)) {
    $table->head[] = $stredit;
    $table->align[] = 'center';
}

$durl = "service_functions.php?sesskey=".sesskey();

foreach ($functions as $function) {
    $function = external_function_info($function);
    $description = "<span style=\"font-size:90%\">".$function->description."</span>"; //TODO: must use class here!
    if (empty($service->component)) {
        $delete = "<a href=\"$durl&amp;action=delete&amp;fid=$function->id&amp;id=$service->id\">$strdelete</a>";
        $table->data[] = array($function->name, $description, $delete);
    } else {
        $table->data[] = array($function->name, $description);
    }
}

echo html_writer::table($table);


// we can edit only custom functions, the build-in would be overridden after each upgrade
if (empty($service->component)) {
    $url = new moodle_url('service_functions.php', array('sesskey'=>sesskey(), 'id'=>$service->id, 'action'=>'add'));
    echo "<a href=$url>".get_string('add')."</a>";
}


echo $OUTPUT->footer();

