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
require_once($CFG->dirroot . '/admin/webservice/forms.php');
require_once($CFG->libdir . '/externallib.php');

$action = optional_param('action', '', PARAM_ACTION);
$tokenid = optional_param('tokenid', '', PARAM_SAFEDIR);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

$PAGE->set_url('/admin/webservice/tokens.php');
$PAGE->navbar->ignore_active(true);
$PAGE->navbar->add(get_string('administrationsite'));
$PAGE->navbar->add(get_string('plugins', 'admin'));
$PAGE->navbar->add(get_string('webservices', 'webservice'));
$PAGE->navbar->add(get_string('managetokens', 'webservice'),
        new moodle_url('/admin/settings.php?section=webservicetokens'));
if ($action == "delete") {
    $PAGE->navbar->add(get_string('delete'));
} else {
    $PAGE->navbar->add(get_string('createtoken', 'webservice'));
}

admin_externalpage_setup('addwebservicetoken');

require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

$tokenlisturl = new moodle_url("/admin/settings.php", array('section' => 'webservicetokens'));

switch ($action) {

    case 'create':
        $mform = new web_service_token_form(null, array('action' => 'create'));
        $data = $mform->get_data();
        if ($mform->is_cancelled()) {
            redirect($tokenlisturl);
        } else if ($data and confirm_sesskey()) {
            ignore_user_abort(true);
            //TODO improvement: either move this function from externallib.php to webservice/lib.php
            // either move most of webservicelib.php functions into externallib.php
            // (create externalmanager class) MDL-23523
            external_generate_token(EXTERNAL_TOKEN_PERMANENT, $data->service,
                    $data->user, get_context_instance(CONTEXT_SYSTEM),
                    $data->validuntil, $data->iprestriction);
            redirect($tokenlisturl);
        }

        //OUTPUT: create token form
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('createtoken', 'webservice'));
        $mform->display();
        echo $OUTPUT->footer();
        die;
        break;

    case 'delete':
        require_once($CFG->dirroot . "/webservice/lib.php");
        $webservicemanager = new webservice();
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
