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

$PAGE->set_url('/user/managetoken.php');
$PAGE->set_title(get_string('securitykeys', 'webservice'));
$PAGE->set_heading(get_string('securitykeys', 'webservice'));

$action  = optional_param('action', '', PARAM_ACTION);
$tokenid = optional_param('tokenid', '', PARAM_SAFEDIR);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

require_login();
require_sesskey();
$returnurl = "$CFG->wwwroot/user/managetoken.php?sesskey=" . sesskey();

//TODO: include tabs.php => tabs.php is a bit ugly require variable $course, $user to be defined here
//look to a better solution, do we really need it? (see /user/portfolio.php and other profil pages)

switch ($action) {

    case 'reset':
        $sql = "SELECT
                    t.id, t.token, u.firstname, u.lastname, s.name
                FROM
                    {external_tokens} t, {user} u, {external_services} s
                WHERE
                    t.creatorid=? AND t.id=? AND t.tokentype = ".EXTERNAL_TOKEN_PERMANENT." AND s.id = t.externalserviceid AND t.userid = u.id";
        $token = $DB->get_record_sql($sql, array($USER->id, $tokenid), MUST_EXIST); //must be the token creator
        if (!$confirm) {
            echo $OUTPUT->header();
            echo $OUTPUT->heading(get_string('managetokens', 'webservice'));
            $optionsyes = array('tokenid'=>$tokenid, 'action'=>'reset', 'confirm'=>1, 'sesskey'=>sesskey());
            $optionsno  = array('section'=>'webservicetokens', 'sesskey'=>sesskey());
            $formcontinue = new single_button(new moodle_url($returnurl, $optionsyes), get_string('reset'));
            $formcancel = new single_button(new moodle_url($returnurl, $optionsno), get_string('cancel'), 'get');
            echo $OUTPUT->confirm(get_string('resettokenconfirm', 'webservice', (object)array('user'=>$token->firstname." ".$token->lastname, 'service'=>$token->name)), $formcontinue, $formcancel);
            echo $OUTPUT->footer();
            die;
        }
        $DB->delete_records('external_tokens', array('id'=>$token->id));
        
        redirect($returnurl);
        break;

    default: //display the list of token

    /// generate a token for non admin if web service are enable and the user has the capability to create a token
        if (!is_siteadmin($USER->id) && has_capability('moodle/webservice:createtoken', get_context_instance(CONTEXT_SYSTEM)) && !empty($CFG->enablewebservices)) {
        /// for every service than the user is authorised on, create a token (if it doesn't already exist)

            ///get all services which are set to all user (no restricted to specific users)
            $norestrictedservices = $DB->get_records('external_services', array('restrictedusers' => 0));
            $serviceidlist = array();
            foreach ($norestrictedservices as $service) {
                $serviceidlist[] = $service->id;
            }

            //get all services which are set to the current user (the current user is specified in the restricted user list)
            $servicesusers = $DB->get_records('external_services_users', array('userid' => $USER->id));
            foreach ($servicesusers as $serviceuser) {
                if (!in_array($serviceuser->externalserviceid,$serviceidlist)) {
                     $serviceidlist[] = $serviceuser->externalserviceid;
                }
            }

            //get all services which already have a token set for the current user
            $usertokens = $DB->get_records('external_tokens', array('userid' => $USER->id, 'tokentype' => EXTERNAL_TOKEN_PERMANENT));
            $tokenizedservice = array();
            foreach ($usertokens as $token) {
                    $tokenizedservice[]  = $token->externalserviceid;
            }

            //create a token for the service which have no token already
            foreach ($serviceidlist as $serviceid) {
                if (!in_array($serviceid, $tokenizedservice)) {
                    //create the token for this service
                    $newtoken = new object();
                    $newtoken->token = md5(uniqid(rand(),1));
                    //check that the user has capability on this service
                    $newtoken->tokentype = EXTERNAL_TOKEN_PERMANENT;
                    $newtoken->userid = $USER->id;
                    $newtoken->externalserviceid = $serviceid;
                    //TODO: find a way to get the context - UPDATE FOLLOWING LINE
                    $newtoken->contextid = get_context_instance(CONTEXT_SYSTEM)->id;
                    $newtoken->creatorid = $USER->id;
                    $newtoken->timecreated = time();
                   
                    $DB->insert_record('external_tokens', $newtoken);
                }
            }
            
            
        }

        // display strings
        $stroperation = get_string('operation', 'webservice');
        $strtoken = get_string('key', 'webservice');
        $strservice = get_string('service', 'webservice');
        $strcreator = get_string('tokencreator', 'webservice');
        $strcontext = get_string('context', 'webservice');
        $strvaliduntil = get_string('validuntil', 'webservice');

        $return = $OUTPUT->heading(get_string('securitykeys', 'webservice'), 3, 'main', true);
        $return .= $OUTPUT->box_start('generalbox webservicestokenui');

        $return .= get_string('keyshelp', 'webservice');

        $table = new html_table();
        $table->head  = array($strtoken, $strservice, $strvaliduntil, $strcreator, $stroperation);
        $table->align = array('left', 'left', 'left', 'center', 'left', 'center');
        $table->width = '100%';
        $table->data  = array();

        //TODO: automatically delete obsolete token (service don't exist anymore), use LEFT JOIN for detection

        //here retrieve token list (including linked users firstname/lastname and linked services name)
        $sql = "SELECT
                    t.id, t.creatorid, t.token, u.firstname, u.lastname, s.name, t.validuntil
                FROM
                    {external_tokens} t, {user} u, {external_services} s
                WHERE
                    t.userid=? AND t.tokentype = ".EXTERNAL_TOKEN_PERMANENT." AND s.id = t.externalserviceid AND t.userid = u.id";
        $tokens = $DB->get_records_sql($sql, array( $USER->id));
        if (!empty($tokens)) {
            foreach ($tokens as $token) {
                //TODO: retrieve context

                if ($token->creatorid == $USER->id) {
                    $reset = "<a href=\"".$returnurl."&amp;action=reset&amp;tokenid=".$token->id."\">";
                    $reset .= get_string('reset')."</a>";
                    $creator = $token->firstname." ".$token->lastname;
                } else {
                    //retrive administrator name
                    require_once($CFG->dirroot.'/user/lib.php');
                    $creators = user_get_users_by_id(array($token->creatorid));
                    $admincreator = $creators[$token->creatorid];
                    $creator = $admincreator->firstname." ".$admincreator->lastname;
                    $reset = '';
                }

                $userprofilurl = new moodle_url('/user/view.php?id='.$token->creatorid);
                $creatoratag = html_writer::start_tag('a', array('href' => $userprofilurl));
                $creatoratag .= $creator;
                $creatoratag .= html_writer::end_tag('a');

                $validuntil = '';
                if (!empty($token->validuntil)) {
                    $validuntil = date("F j, Y"); //TODO: language support (look for moodle function)
                }

                $table->data[] = array($token->token, $token->name, $validuntil, $creatoratag, $reset);
            }
            $return .= html_writer::table($table);
          
        } else {
            $return .= get_string('notoken', 'webservice');
        }

        $return .= $OUTPUT->box_end();
        echo $OUTPUT->header();
        echo $return;
        echo $OUTPUT->footer();
        die();
        break;
}

redirect($returnurl);