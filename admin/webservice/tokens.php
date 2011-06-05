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
 * Web services tokens admin UI
 *
 * @package   webservice
 * @author Jerome Mouneyrac
 * @copyright 2009 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/webservice/forms.php');
require_once($CFG->libdir . '/externallib.php');

$action = optional_param('action', '', PARAM_ACTION);
$tokenid = optional_param('tokenid', '', PARAM_SAFEDIR);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

admin_externalpage_setup('addwebservicetoken');

//Deactivate the second 'Manage token' navigation node, and use the main 'Manage token' navigation node
$node = $PAGE->settingsnav->find('addwebservicetoken', navigation_node::TYPE_SETTING);
$newnode = $PAGE->settingsnav->find('webservicetokens', navigation_node::TYPE_SETTING);
if ($node && $newnode) {
    $node->display = false;
    $newnode->make_active();
}

require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

$tokenlisturl = new moodle_url("/" . $CFG->admin . "/settings.php", array('section' => 'webservicetokens'));

require_once($CFG->dirroot . "/webservice/lib.php");
$webservicemanager = new webservice();

switch ($action) {

    case 'create':
        $mform = new web_service_token_form(null, array('action' => 'create'));
        $data = $mform->get_data();
        if ($mform->is_cancelled()) {
            redirect($tokenlisturl);
        } else if ($data and confirm_sesskey()) {
            ignore_user_abort(true);

            //check the the user is allowed for the service
            $selectedservice = $webservicemanager->get_external_service_by_id($data->service);
            if ($selectedservice->restrictedusers) {
                $restricteduser = $webservicemanager->get_ws_authorised_user($data->service, $data->user);
                if (empty($restricteduser)) {
                    $allowuserurl = new moodle_url('/' . $CFG->admin . '/webservice/service_users.php',
                            array('id' => $selectedservice->id));
                    $allowuserlink = html_writer::tag('a', $selectedservice->name , array('href' => $allowuserurl));
                    $errormsg = $OUTPUT->notification(get_string('usernotallowed', 'webservice', $allowuserlink));
                }
            }

            //process the creation
            if (empty($errormsg)) {
                //TODO improvement: either move this function from externallib.php to webservice/lib.php
                // either move most of webservicelib.php functions into externallib.php
                // (create externalmanager class) MDL-23523
                external_generate_token(EXTERNAL_TOKEN_PERMANENT, $data->service,
                        $data->user, get_context_instance(CONTEXT_SYSTEM),
                        $data->validuntil, $data->iprestriction);
                redirect($tokenlisturl);
            }
        }

        //OUTPUT: create token form
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('createtoken', 'webservice'));
        if (!empty($errormsg)) {
            echo $errormsg;
        }
        $mform->display();
        echo $OUTPUT->footer();
        die;
        break;

    case 'delete':
        $token = $webservicemanager->get_created_by_user_ws_token($USER->id, $tokenid);

        //Delete the token
        if ($confirm and confirm_sesskey()) {
            $webservicemanager->delete_user_ws_token($token->id);
            redirect($tokenlisturl);
        }

        ////OUTPUT: display delete token confirmation box
        echo $OUTPUT->header();
        $renderer = $PAGE->get_renderer('core', 'webservice');
        echo $renderer->admin_delete_token_confirmation($token);
        echo $OUTPUT->footer();
        die;
        break;

    default:
        //wrong url access
        redirect($tokenlisturl);
        break;
}
