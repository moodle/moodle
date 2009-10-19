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
 * Web services admin UI
 *
 * @package   webservice
 * @copyright 2009 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');

$id      = required_param('id', PARAM_INT);
$action  = optional_param('action', '', PARAM_ACTION);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

$PAGE->set_url('admin/external_service.php', array('id'=>$id));

admin_externalpage_setup('externalservice');

$returnurl = "$CFG->wwwroot/$CFG->admin/settings.php?section=externalservices";

if ($id) {
    $service = $DB->get_record('external_services', array('id'=>$id), '*', MUST_EXIST);
} else {
    $service = null;
}

// delete a service
if (!empty($action) and $action == 'delete' and confirm_sesskey() and $service and empty($service->component)) {
    if (!$confirm) {
        admin_externalpage_print_header();
        $optionsyes = array('id'=>$id, 'action'=>'delete', 'confirm'=>1, 'sesskey'=>sesskey());
        $optionsno  = array('section'=>'externalservices');
        $formcontinue = html_form::make_button('external_service.php', $optionsyes, get_string('delete'), 'post');
        $formcancel = html_form::make_button('settings.php', $optionsno, get_string('cancel'), 'get');
        echo $OUTPUT->confirm(get_string('deleteserviceconfirm', 'webservice', $service->name), $formcontinue, $formcancel);
        echo $OUTPUT->footer();
        die;
    }
    $DB->delete_records('external_services_users', array('externalserviceid'=>$service->id));
    $DB->delete_records('external_services_functions', array('externalserviceid'=>$service->id));
    $DB->delete_records('external_services', array('id'=>$service->id));
    redirect($returnurl);
}

$clear = optional_param('clearbutton', false, PARAM_BOOL);
$servicename  = optional_param('servicename', '', PARAM_TEXT);
$enableservice  = optional_param('enableservice', 0, PARAM_BOOL);
$restrictedusers  = optional_param('restrictedusers', 0, PARAM_BOOL);
$capability  = optional_param('capability', '', PARAM_CAPABILITY);

// clear the capability field
if (!empty($clear)) {
        $service->name = $servicename;
        $service->enabled = $enableservice;
        $service->requiredcapability = "";
        $service->restrictedusers = $restrictedusers;
} else {
// add/update a service
    if ((!empty($action) and ($action == 'add' || $action == 'update') and confirm_sesskey())) {
        
        if (!empty($servicename)) {
            $tempservice = new object();
            $tempservice->name = $servicename;
            $tempservice->enabled = $enableservice;
            $tempservice->requiredcapability = $capability;
            $tempservice->restrictedusers = $restrictedusers;

            if ($action == 'add') {
                $DB->insert_record('external_services', $tempservice);
            }
            else {
                $tempservice->id = $service->id;
                $DB->update_record('external_services', $tempservice);
            }

            redirect($returnurl);

        }
        //administrator has omitted service name => display error message
        else {
            $service->name = $servicename;
            $service->enabled = $enableservice;
            $service->requiredcapability = $capability;
            $service->restrictedusers = $restrictedusers;
            $errormessage = get_string('emptyname', 'webservice');
        }
    }

} 


admin_externalpage_print_header();
if (!empty($errormessage)) {
    echo $OUTPUT->notification($errormessage);
}


// Prepare the list of capabilites to choose from
    $systemcontext = get_context_instance(CONTEXT_SYSTEM);
    $allcapabilities = fetch_context_capabilities($systemcontext);
    $capabilitychoices = array();
    foreach ($allcapabilities as $cap) {
        $capabilitychoices[$cap->name] = $cap->name . ': ' . get_capability_string($cap->name);
    }

// Javascript for the capability search/selection fields
    $PAGE->requires->yui_lib('event');
    $PAGE->requires->js('admin/webservice/script.js');
    $PAGE->requires->js_function_call('capability_service.cap_filter_init', array(get_string('search')));

//  UI
    $capability = optional_param('capability', '', PARAM_CAPABILITY);
    echo $OUTPUT->box_start('generalbox boxwidthwide boxaligncenter centerpara');

    $action = (empty($id))?'add':'update'; //if 'id' GET parameter = 0 we're adding a service, otherwise updating

    //the service form
    $form = new html_form();
    $form->url = new moodle_url('/admin/external_service.php', array('id' => $id, 'action' => $action)); // Required
    $form->button = new html_button();
    $form->button->id = 'settingssubmit';
    $form->button->text = get_string('saveservice', 'webservice'); // Required
    $form->button->disabled = false;
    $form->button->title = get_string('saveservice', 'webservice');
    $form->method = 'post';
    $form->id = 'settingsform';

    echo $OUTPUT->heading(get_string('externalservice', 'webservice'));
    //service name field
    $namefield =  "<label>".get_string('servicename','webservice')." </label>";
    $nametextfield = new html_field();
    $nametextfield->name = 'servicename';
    $nametextfield->value = empty($service->name)?"":$service->name;
    $nametextfield->style = 'width: 30em;';
    $namefield .= $OUTPUT->textfield($nametextfield);
    $contents = $namefield;
    //enable field
    $servicecheckbox = new html_select_option();
    $servicecheckbox->value = true;
    $servicecheckbox->selected = empty($service->enabled)?false:true;
    $servicecheckbox->text = get_string('enabled', 'webservice');
    $servicecheckbox->label->text = get_string('enabled', 'webservice');
    $servicecheckbox->alt = get_string('enabled', 'webservice');
    $contents .=  $OUTPUT->checkbox($servicecheckbox, 'enableservice');
    //help text
    $contents .= '<p id="intro">'. get_string('addservicehelp', 'webservice') . '</p>';
    //restricted users option
    $restricteduserscheckbox = new html_select_option();
    $restricteduserscheckbox->value = true;
    $restricteduserscheckbox->selected = empty($service->restrictedusers)?false:true;
    $restricteduserscheckbox->text = get_string('restrictedusers', 'webservice');
    $restricteduserscheckbox->label->text = get_string('restrictedusers', 'webservice');
    $restricteduserscheckbox->alt = get_string('restrictedusers', 'webservice');
    $contents .= $OUTPUT->checkbox($restricteduserscheckbox, 'restrictedusers');
    //capability section (search field + selection field)
    $contents .= '<p><label for="menucapability"> ' . get_string('requiredcapability', 'webservice') . '</label></p> ';
    $capabilityname = new html_field();
    $capabilityname->name = 'capabilityname';
    $capabilityname->id = 'capabilityname';
    $capabilityname->value = empty($service->requiredcapability)?"":$service->requiredcapability;
    $capabilityname->disabled = true;
    $capabilityname->style = 'width: 20em;';
    $capability = empty($service->requiredcapability)?"":$service->requiredcapability;
    $select = html_select::make($capabilitychoices, 'capability', $capability);
    $select->nothingvalue = '';
    $select->listbox = true;
    $select->tabindex = 0;
    $contents .= $OUTPUT->select($select);
    $contents .= '<br/><label for="menucapability"> ' . get_string('selectedcapability', 'webservice') . '</label> ';
    $contents .= $OUTPUT->textfield($capabilityname);
    $contents .= '<input type="submit" name="clearbutton" id="clearbutton" value="' . get_string('clear') . '" />';
    $contents .= "<br/><br/>";
    echo $OUTPUT->form($form, $contents);

    echo $OUTPUT->box_end();

echo $OUTPUT->footer();

