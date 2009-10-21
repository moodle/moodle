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

$id      = required_param('id', PARAM_INT);
$fid     = optional_param('fid', 0, PARAM_INT);
$action  = optional_param('action', '', PARAM_ACTION);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

$PAGE->set_url('admin/websevice/service_functions.php', array('id'=>$id));

admin_externalpage_setup('externalservicefunctions');

$returnurl = "$CFG->wwwroot/$CFG->admin/settings.php?section=externalservices";
$thisurl   = "service_functions.php?id=$id";

$service = $DB->get_record('external_services', array('id'=>$id), '*', MUST_EXIST);

if ($action === 'delete' and confirm_sesskey() and $service and empty($service->component)) {
    $function = $DB->get_record('external_functions', array('id'=>$fid), '*', MUST_EXIST);
    if (!$confirm) {
        admin_externalpage_print_header();
        $optionsyes = array('id'=>$id, 'action'=>'delete', 'confirm'=>1, 'sesskey'=>sesskey(), 'fid'=>$function->id);
        $optionsno  = array('id'=>$id);
        $formcontinue = html_form::make_button('service_functions.php', $optionsyes, get_string('delete'), 'post');
        $formcancel = html_form::make_button('service_functions.php', $optionsno, get_string('cancel'), 'get');
        echo $OUTPUT->confirm(get_string('removefunctionconfirm', 'webservice', (object)array('service'=>$service->name, 'function'=>$function->name)), $formcontinue, $formcancel);
        echo $OUTPUT->footer();
        die;
    }
    $DB->delete_records('external_services_functions', array('externalserviceid'=>$service->id, 'functionname'=>$function->name));
    redirect($thisurl);

}
else if ($action === 'add') {

    if (optional_param('save', 0, PARAM_ACTION)) {

        ignore_user_abort(true); // no interruption here!
        $functionname = optional_param('function', 0, PARAM_ACTION);
        if (!empty($functionname)) {
            $function = $DB->get_record('external_functions', array('name'=> $functionname), '*', MUST_EXIST);
            // make sure the function is not there yet
            if ($DB->record_exists('external_services_functions', array('externalserviceid'=>$service->id, 'functionname'=>$function->name))) {
                redirect($thisurl);
            }
            $new = new object();
            $new->externalserviceid = $service->id;
            $new->functionname      = $functionname;
            $DB->insert_record('external_services_functions', $new);
            redirect($thisurl);
        } 
        else {
            $errormessage = get_string('nofunctionselected', 'webservice');
        }

    }

    // Prepare the list of function to choose from
   $select = "name NOT IN (SELECT s.functionname
                                  FROM {external_services_functions} s
                                 WHERE s.externalserviceid = :sid
                               )";
    $functions = $DB->get_records_select_menu('external_functions', $select, array('sid'=>$id), 'name', 'id, name');
    $functionchoices = array();

    foreach ($functions as $functionname) {
        $functionchoices[$functionname] = $functionname . ': ' . get_string($functionname, 'servicedescription');
    }

    // Javascript for the function search/selection fields
    $PAGE->requires->yui_lib('event');
    $PAGE->requires->js($CFG->admin.'/webservice/script.js');
    $PAGE->requires->js_function_call('capability_service.cap_filter_init', array(get_string('search'))); //TODO generalize javascript

    admin_externalpage_print_header();
    if (!empty($errormessage)) {
        echo $OUTPUT->notification($errormessage);
    }
    echo $OUTPUT->heading($service->name);
     echo $OUTPUT->box_start('generalbox boxwidthwide boxaligncenter centerpara');
    //the service form
    $form = new html_form();
    $form->url = new moodle_url('service_functions.php', array('id' => $id, 'action' => 'add', 'save' => 1)); // Required
    $form->button = new html_button();
    $form->button->id = 'settingssubmit';
    $form->button->text = get_string('addfunction', 'webservice'); // Required
    $form->button->disabled = false;
    $form->button->title = get_string('addfunction', 'webservice');
    $form->method = 'post';
    $form->id = 'settingsform';
    //help text
    $contents = '<p id="intro">'. get_string('addfunctionhelp', 'webservice') . '</p>';
    //function section (search field + selection field)
    $select = new html_select();
    $select->options = $functionchoices;
    $select->name = 'function';
    $select->id = 'menucapability'; //TODO generalize javascript
    $select->nothingvalue = '';
    $select->listbox = true;
    $select->tabindex = 0;
    $contents .= $OUTPUT->select($select);
    $contents .= "<br/><br/>";
    echo $OUTPUT->form($form, $contents);

    echo $OUTPUT->box_end();

    echo $OUTPUT->footer();
    die;
}

admin_externalpage_print_header();

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
    //TODO: manage when the description is into a module/plugin lang file
    $description = "<span style=\"font-size:90%\">".get_string($function->name,'servicedescription')."</span>";
    if (empty($service->component)) {
        $delete = "<a href=\"$durl&amp;action=delete&amp;fid=$function->id&amp;id=$service->id\">$strdelete</a>";
        $table->data[] = array($function->name, $description, $delete);
    } else {
        $table->data[] = array($function->name, $description);
    }
}

echo $OUTPUT->table($table);


// we can edit only custom functions, the build-in would be overridden after each upgrade
if (empty($service->component)) {
    $form = new html_form();
    $form->url = new moodle_url('service_functions.php', array('sesskey'=>sesskey(), 'id'=>$service->id, 'action'=>'add'));
    $form->button->text = get_string('add');
    $form->method = 'get';
    echo $OUTPUT->button($form);
}

// simple back button
$form = new html_form();
$form->url = new moodle_url('../settings.php', array('section'=>'externalservices'));
$form->button->text = get_string('back');
$form->method = 'get';
echo $OUTPUT->button($form);

echo $OUTPUT->footer();

