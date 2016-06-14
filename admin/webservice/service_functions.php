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
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/webservice/lib.php');
require_once('forms.php');

$serviceid = required_param('id', PARAM_INT);
$functionid = optional_param('fid', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHANUMEXT);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

admin_externalpage_setup('externalservicefunctions');

//define nav bar
$PAGE->set_url('/' . $CFG->admin . '/webservice/service_functions.php', array('id' => $serviceid));
$node = $PAGE->settingsnav->find('externalservices', navigation_node::TYPE_SETTING);
if ($node) {
    $node->make_active();
}
$PAGE->navbar->add(get_string('functions', 'webservice'),
        new moodle_url('/' . $CFG->admin . '/webservice/service_functions.php', array('id' => $serviceid)));

$service = $DB->get_record('external_services', array('id' => $serviceid), '*', MUST_EXIST);
$webservicemanager = new webservice();
$renderer = $PAGE->get_renderer('core', 'webservice');
$functionlisturl = new moodle_url('/' . $CFG->admin . '/webservice/service_functions.php',
        array('id' => $serviceid));

// Add or Delete operations
switch ($action) {
    case 'add':
        $PAGE->navbar->add(get_string('addfunctions', 'webservice'));
        /// Add function operation
        if (confirm_sesskey() and $service and empty($service->component)) {
            $mform = new external_service_functions_form(null,
                    array('action' => 'add', 'id' => $service->id));

            //cancelled add operation, redirect to function list page
            if ($mform->is_cancelled()) {
                redirect($functionlisturl);
            }

            //add the function to the service then redirect to function list page
            if ($data = $mform->get_data()) {
                ignore_user_abort(true); // no interruption here!
                foreach ($data->fids as $fid) {
                    $function = $webservicemanager->get_external_function_by_id(
                            $fid, MUST_EXIST);
                    // make sure the function is not there yet
                    if (!$webservicemanager->service_function_exists($function->name,
                            $service->id)) {
                        $webservicemanager->add_external_function_to_service(
                                $function->name, $service->id);
                    }
                }
                redirect($functionlisturl);
            }

            //Add function operation page output
            echo $OUTPUT->header();
            echo $OUTPUT->heading($service->name);
            $mform->display();
            echo $OUTPUT->footer();
            die;
        }

        break;

    case 'delete':
        $PAGE->navbar->add(get_string('removefunction', 'webservice'));
        /// Delete function operation
        if (confirm_sesskey() and $service and empty($service->component)) {
            //check that the function to remove exists
            $function = $webservicemanager->get_external_function_by_id(
                            $functionid, MUST_EXIST);

            //display confirmation page
            if (!$confirm) {
                echo $OUTPUT->header();
                echo $renderer->admin_remove_service_function_confirmation($function, $service);
                echo $OUTPUT->footer();
                die;
            }

            //or remove the function from the service, then redirect to the function list
            $webservicemanager->remove_external_function_from_service($function->name,
                   $service->id);
            redirect($functionlisturl);
        }
        break;
}

/// OUTPUT function list page
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('addservicefunction', 'webservice', $service->name));
$functions = $webservicemanager->get_external_functions(array($service->id));
echo $renderer->admin_service_function_list($functions, $service);
echo $OUTPUT->footer();

