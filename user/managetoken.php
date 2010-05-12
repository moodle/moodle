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
 * @author    Jerome Mouneyrac
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../config.php');
require($CFG->dirroot.'/webservice/lib.php');

$PAGE->set_url('/user/managetoken.php');
$PAGE->set_title(get_string('securitykeys', 'webservice'));
$PAGE->set_heading(get_string('securitykeys', 'webservice'));

require_login();
require_sesskey();

$webservicetokenboxhtml = '';
/// Manage user web service tokens
if ( !is_siteadmin($USER->id) && !empty($CFG->enablewebservices) &&
        has_capability('moodle/webservice:createtoken', get_system_context())) {

    $action  = optional_param('action', '', PARAM_ACTION);
    $tokenid = optional_param('tokenid', '', PARAM_SAFEDIR);
    $confirm = optional_param('confirm', 0, PARAM_BOOL);

    $webservice = new webservice(); //load the webservice library
    $wsrenderer = $PAGE->get_renderer('core', 'webservice');

    if ($action == 'resetwstoken') {
            $token = $webservice->get_created_by_user_ws_token($USER->id, $tokenid);
            /// Display confirmation page to Reset the token
            if (!$confirm) {
                $resetconfirmation = $wsrenderer->user_reset_token_confirmation($token);
            } else {
            /// Delete the token that need to be regenerated
                $webservice->delete_user_ws_token($tokenid);
            }      
    }

    $webservice->generate_user_ws_tokens($USER->id); //generate all token that need to be generated
    $tokens = $webservice->get_user_ws_tokens($USER->id);
    $webservicetokenboxhtml =  $wsrenderer->user_webservice_tokens_box($tokens, $USER->id); //display the box for web service token
}

//TODO Manage RSS keys
//1- the reset confirmation page content should go into $resetconfirmation
//2- create a table/box for the RSS key
//PS: in 2 if you prefer to add a special row to the ws table in this case move
//the renderer somewhere else, add a new column to make difference between web service and RSS, change the name of the
//renderer function.



// PAGE OUTPUT
echo $OUTPUT->header();
if (!empty($resetconfirmation)) {
    echo $resetconfirmation;  //TODO the RSS regenerate confirmation content code should
                              //be containt into $resetconfirmation too
} else {
    echo $webservicetokenboxhtml;
    //TODO: echo RSS table html here
}
echo $OUTPUT->footer();


