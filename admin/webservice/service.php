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
require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once('forms.php');
require_once($CFG->dirroot . '/webservice/lib.php');

admin_externalpage_setup('externalservice');

//define nav bar
$node = $PAGE->settingsnav->find('externalservice', navigation_node::TYPE_SETTING);
$newnode = $PAGE->settingsnav->find('externalservices', navigation_node::TYPE_SETTING);
if ($node && $newnode) {
    $node->display = false;
    $newnode->make_active();
}
$PAGE->navbar->add(get_string('externalservice', 'webservice'));

//Retrieve few general parameters
$id = required_param('id', PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHANUMEXT);
$confirm = optional_param('confirm', 0, PARAM_BOOL);
$webservicemanager = new webservice;
$renderer = $PAGE->get_renderer('core', 'webservice');
$returnurl = $CFG->wwwroot . "/" . $CFG->admin . "/settings.php?section=externalservices";
$service = $id ? $webservicemanager->get_external_service_by_id($id, MUST_EXIST) : null;

/// DELETE operation
if ($action == 'delete' and confirm_sesskey() and $service and empty($service->component)) {
    //Display confirmation Page
    if (!$confirm) {
        echo $OUTPUT->header();
        echo $renderer->admin_remove_service_confirmation($service);
        echo $OUTPUT->footer();
        die;
    }
    //The user has confirmed the deletion, delete and redirect
    $webservicemanager->delete_service($service->id);
    add_to_log(SITEID, 'webservice', 'delete', $returnurl, get_string('deleteservice', 'webservice', $service));
    redirect($returnurl);
}

/// EDIT/CREATE/CANCEL operations => at the end redirect to add function page / main service page
$mform = new external_service_form(null, $service);
if ($mform->is_cancelled()) {
    redirect($returnurl);
} else if ($servicedata = $mform->get_data()) {
    $servicedata = (object) $servicedata;
    if (!empty($servicedata->requiredcapability) && $servicedata->requiredcapability == "norequiredcapability") {
        $servicedata->requiredcapability = "";
    }

    //create operation
    if (empty($servicedata->id)) {
        $servicedata->id = $webservicemanager->add_external_service($servicedata);
        add_to_log(SITEID, 'webservice', 'add', $returnurl, get_string('addservice', 'webservice', $servicedata));

        //redirect to the 'add functions to service' page
        $addfunctionpage = new moodle_url(
                        $CFG->wwwroot . '/' . $CFG->admin . '/webservice/service_functions.php',
                        array('id' => $servicedata->id));
        $returnurl = $addfunctionpage->out(false);
    } else {
        //update operation
        $webservicemanager->update_external_service($servicedata);
        add_to_log(SITEID, 'webservice', 'edit', $returnurl, get_string('editservice', 'webservice', $servicedata));
    }

    redirect($returnurl);
}

//OUTPUT edit/create form
echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();

