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
require_once($CFG->libdir.'/adminlib.php');
require_once('forms.php');
require_once($CFG->libdir.'/externallib.php');

$action  = optional_param('action', '', PARAM_ACTION);
$tokenid = optional_param('tokenid', '', PARAM_SAFEDIR);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

$PAGE->set_url('/admin/webservice/tokens.php');
$PAGE->navbar->ignore_active(true);
$PAGE->navbar->add(get_string('administrationsite'));
$PAGE->navbar->add(get_string('plugins', 'admin'));
$PAGE->navbar->add(get_string('webservices', 'webservice'));
$PAGE->navbar->add(get_string('managetokens', 'webservice'), new moodle_url('/admin/settings.php?section=webservicetokens'));
if ($action == "delete") {
    $PAGE->navbar->add(get_string('delete'));
} else {
    $PAGE->navbar->add(get_string('createtoken', 'webservice'));
}

admin_externalpage_setup('addwebservicetoken');

require_login();
require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

$returnurl = "$CFG->wwwroot/$CFG->admin/settings.php?section=webservicetokens";

////////////////////////////////////////////////////////////////////////////////
// process actions

if (!confirm_sesskey()) {
    redirect($returnurl);
}

switch ($action) {
    
    case 'create':
        $mform = new web_service_token_form(null, array('action' => 'create'));
        if ($mform->is_cancelled()) {
            redirect($returnurl);
        } else if ($data = $mform->get_data()) {
            ignore_user_abort(true); // no interruption here!
            external_generate_token(EXTERNAL_TOKEN_PERMANENT, $data->service, $data->user, get_context_instance(CONTEXT_SYSTEM), $data->validuntil, $data->iprestriction);
            redirect($returnurl);
        }

        //ask for function id
        admin_externalpage_print_header();
        echo $OUTPUT->heading(get_string('createtoken', 'webservice'));
        $mform->display();
        echo $OUTPUT->footer();
        die;
        break;

    case 'delete':
        $sql = "SELECT
                    t.id, t.token, u.firstname, u.lastname, s.name
                FROM
                    {external_tokens} t, {user} u, {external_services} s
                WHERE
                    t.creatorid=? AND t.id=? AND t.tokentype = ".EXTERNAL_TOKEN_PERMANENT." AND s.id = t.externalserviceid AND t.userid = u.id";
        $token = $DB->get_record_sql($sql, array($USER->id, $tokenid), MUST_EXIST); //must be the token creator
        if (!$confirm) {
            admin_externalpage_print_header();
            $optionsyes = array('tokenid'=>$tokenid, 'action'=>'delete', 'confirm'=>1, 'sesskey'=>sesskey());
            $optionsno  = array('section'=>'webservicetokens', 'sesskey'=>sesskey());
            $formcontinue = new single_button(new moodle_url('/admin/webservice/tokens.php', $optionsyes), get_string('delete'));
            $formcancel = new single_button(new moodle_url('/admin/settings.php', $optionsno), get_string('cancel'), 'get');
            echo $OUTPUT->confirm(get_string('deletetokenconfirm', 'webservice', (object)array('user'=>$token->firstname." ".$token->lastname, 'service'=>$token->name)), $formcontinue, $formcancel);
            echo $OUTPUT->footer();
            die;
        }
        $DB->delete_records('external_tokens', array('id'=>$token->id));
        redirect($returnurl);
        break;

    default:
        break;
}

redirect($returnurl);
