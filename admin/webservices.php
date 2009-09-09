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

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/webservice/lib.php');


$serviceid      = optional_param('serviceid', '', PARAM_FORMAT);

$pagename = 'webservicessettings';


admin_externalpage_setup($pagename);
require_login(SITEID, false);
require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

$sesskeyurl = "$CFG->wwwroot/$CFG->admin/webservices.php?sesskey=" . sesskey();
$baseurl    = "$CFG->wwwroot/$CFG->admin/settings.php?section=webservices";

if (!confirm_sesskey()) {
    print_error('confirmsesskeybad', '', $baseurl);
}

if (!empty($serviceid)) {
    admin_externalpage_print_header();

    //cannot use moodle form in order to display complex list of functions
    $form = new html_form();
    $service = $DB->get_record('external_services',array('id' => $serviceid));
    $formhtml = get_string('servicename', 'webservice').': '.get_string($service->name, 'webservice') ;
    $formhtml .= "<br/><br/>";
    //display function selector
    if (empty($serviceid)) {

    }

    //display service functions
    $servicesfunctions = $DB->get_records_sql("SELECT fs.id as id, f.component as component, fs.enabled as enabled, s.name as servicename, s.id as serviceid, f.name as functionname, f.id as functionid
                                    FROM {external_services} s, {external_functions} f, {external_services_functions} fs
                                   WHERE fs.externalserviceid = s.id AND fs.externalfunctionid = f.id AND s.id = ?", array($serviceid));

    //save the administrator changes
    $saved      = optional_param('saved', 0, PARAM_NUMBER);

    if ($saved) {
        foreach($servicesfunctions as &$servicefunction) { //need to be a refence cause we're going to update the form value too
            $enabled = optional_param($servicefunction->functionname, '', PARAM_ALPHANUMEXT);
            if ($enabled) {
                $servicefunction->enabled =  1; //update the form "enabled" value
            } else {
                $servicefunction->enabled = 0; //update the form "enabled" value
            }
            $wsservicefunction = new object();
            $wsservicefunction->id = $servicefunction->id;
            $wsservicefunction->enabled = $servicefunction->enabled;
            $DB->update_record('external_services_functions',$wsservicefunction);
        }
    }


    $data = array();
    reset($servicesfunctions);
    foreach($servicesfunctions as $function) {
        $checkbox = html_select_option::make_checkbox($function->functionid, $function->enabled, 'functionenabled');
        $checkbox->label->add_class('accesshide');
        $data[] = array($function->functionname, $function->component, $OUTPUT->checkbox($checkbox, $function->functionname));
    }
    $table = new html_table();
    $table->head  = array(get_string('functionname', 'webservice'), get_string('component', 'webservice'), get_string('enabled', 'webservice'));
    $table->size  = array('40%', '40%', '20%');
    $table->align = array('left', 'left', 'left');
    //$table->width = '30%';
    $table->data  = $data;
    $table->tablealign  = 'center';
    $formhtml .= $OUTPUT->table($table);

    $form->button->text = get_string('save', 'webservice');
    $form->button->title = get_string('save', 'webservice');
    $form->button->id = 'save';
    $form->url = new moodle_url('webservices.php', array('serviceid' => $serviceid, 'saved' => 1));
    //$form->add_class($class);
    $formhtml .= "<br/><br/>";

    echo $OUTPUT->box_start('generalbox boxaligncenter centerpara');
    echo $OUTPUT->form($form, $formhtml);
    echo $OUTPUT->box_end();

    echo $OUTPUT->footer();

}

?>
