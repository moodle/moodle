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
 * Web service test client.
 *
 * @package   webservice
 * @copyright 2009 Moodle Pty Ltd (http://moodle.com)
 * @author    Petr Skoda (skodak)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../config.php');

$PAGE->set_url('user/managetoken.php');

$action  = optional_param('action', '', PARAM_ACTION);
$tokenid = optional_param('tokenid', '', PARAM_SAFEDIR);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

require_login();
require_sesskey();
$returnurl = "$CFG->wwwroot/user/managetoken.php?sesskey=" . sesskey();

//TODO: include tabs.php => tabs.php is a bit ugly require variable $course, $user to be defined here
//look to a better solution, do we really need it? (see /user/portfolio.php and other profil pages)

switch ($action) {

    case 'create':
        require_once($CFG->dirroot."/admin/webservice/forms.php");
        $mform = new web_service_token_form(null, array('action' => 'create', 'nouserselection' => true));
        if ($mform->is_cancelled()) {
            redirect($returnurl);
        } else if ($data = $mform->get_data()) {
            ignore_user_abort(true); // no interruption here!

            //generate token
            $generatedtoken = md5(uniqid(rand(),1));

            // make sure the token doesn't exist (even if it should be almost impossible with the random generation)
            if ($DB->record_exists('external_tokens', array('token'=>$generatedtoken))) {
                throw new moodle_exception('tokenalreadyexist');
            } else {
                $newtoken = new object();
                $newtoken->token = $generatedtoken;
                //check that the user has capability on this service
                $service = $DB->get_record('external_services', array('id' => $data->service));
                if (empty($service)) {
                    throw new moodle_exception('servicedonotexist');
                }
                if (empty($service->requiredcapability) || has_capability($service->requiredcapability, $systemcontext, $USER->id)) {
                    $newtoken->externalserviceid = $data->service;
                } else {
                    throw new moodle_exception('nocapabilitytousethisservice');
                }

                $newtoken->tokentype = EXTERNAL_TOKEN_PERMANENT;
                $newtoken->userid = $USER->id;
                //TODO: find a way to get the context - UPDATE FOLLOWING LINE
                $newtoken->contextid = get_context_instance(CONTEXT_SYSTEM)->id;
                $newtoken->creatorid = $USER->id;
                $newtoken->timecreated = time();
                $newtoken->validuntil = $data->validuntil;
                if (!empty($data->iprestriction)) {
                    $newtoken->iprestriction = $data->iprestriction;
                }
                $DB->insert_record('external_tokens', $newtoken);
            }
            redirect($returnurl);
        }

        //ask for function id
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('createtoken', 'webservice'));
        $mform->display();
        echo $OUTPUT->footer();
        die;
        break;

    case 'delete':
        $sql = "SELECT
                    token.id, token.token, user.firstname, user.lastname, service.name
                FROM
                    {external_tokens} token, {user} user, {external_services} service
                WHERE
                    token.creatorid=? AND token.id=? AND token.tokentype = ".EXTERNAL_TOKEN_PERMANENT." AND service.id = token.externalserviceid AND token.userid = user.id";
        $token = $DB->get_record_sql($sql, array($USER->id, $tokenid), MUST_EXIST); //must be the token creator
        if (!$confirm) {
            echo $OUTPUT->header();
            echo $OUTPUT->heading(get_string('managetokens', 'webservice'));
            $optionsyes = array('tokenid'=>$tokenid, 'action'=>'delete', 'confirm'=>1, 'sesskey'=>sesskey());
            $optionsno  = array('section'=>'webservicetokens', 'sesskey'=>sesskey());
            $formcontinue = new single_button(new moodle_url($returnurl, $optionsyes), get_string('delete'));
            $formcancel = new single_button(new moodle_url($returnurl, $optionsno), get_string('cancel'), 'get');
            echo $OUTPUT->confirm(get_string('deletetokenconfirm', 'webservice', (object)array('user'=>$token->firstname." ".$token->lastname, 'service'=>$token->name)), $formcontinue, $formcancel);
            echo $OUTPUT->footer();
            die;
        }
        $DB->delete_records('external_tokens', array('id'=>$token->id));
        redirect($returnurl);
        break;

    default: //display the list of token

        // display strings
        $stroperation = get_string('operation', 'webservice');
        $strtoken = get_string('token', 'webservice');
        $strservice = get_string('service', 'webservice');
        $struser = get_string('user');
        $strcontext = get_string('context', 'webservice');
        $strvaliduntil = get_string('validuntil', 'webservice');

        $return = $OUTPUT->heading(get_string('webservicetokens', 'webservice'), 3, 'main', true);
        $return .= $OUTPUT->box_start('generalbox webservicestokenui');

        $table = new html_table();
        $table->head  = array($strtoken, $struser, $strservice, $strcontext, $strvaliduntil, $stroperation);
        $table->align = array('left', 'left', 'left', 'left', 'center');
        $table->width = '100%';
        $table->data  = array();

        //TODO: automatically delete obsolete token (service don't exist anymore), use LEFT JOIN for detection

        //here retrieve token list (including linked users firstname/lastname and linked services name)
        $sql = "SELECT
                    token.id, token.token, user.firstname, user.lastname, service.name, token.validuntil
                FROM
                    {external_tokens} token, {user} user, {external_services} service
                WHERE
                    token.creatorid=? AND token.tokentype = 2 AND service.id = token.externalserviceid AND token.userid = user.id";
        $tokens = $DB->get_records_sql($sql, array( $USER->id));
        if (!empty($tokens)) {
            foreach ($tokens as $token) {
                //TODO: retrieve context

                $delete = "<a href=\"".$returnurl."&amp;action=delete&amp;tokenid=".$token->id."\">";
                $delete .= get_string('delete')."</a>";

                if (empty($_SERVER['HTTPS'])) {
                    $token->token = get_string('activatehttps', 'webservice');
                }

                $validuntil = '';
                if (!empty($token->validuntil)) {
                    $validuntil = date("F j, Y"); //TODO: language support (look for moodle function)
                }

                $table->data[] = array($token->token, $token->firstname." ".$token->lastname, $token->name, '', $validuntil, $delete);
            }

            $return .= $OUTPUT->table($table);
            $return .= get_string('httpswarning', 'webservice');
        } else {
            $return .= get_string('notoken', 'webservice');
        }

        $return .= $OUTPUT->box_end();
        // "add a token" link
        $return .= "<a href=\"".$returnurl."&amp;action=create\">";
        $return .= get_string('add')."</a>";
        echo $OUTPUT->header();
        echo $return;
        echo $OUTPUT->footer();
        die();
        break;
}

redirect($returnurl);