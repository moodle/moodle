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

require_login();
require_sesskey();

$usercontext = get_context_instance(CONTEXT_USER, $USER->id);

$PAGE->set_context($usercontext);
$PAGE->set_url('/user/managetoken.php');
$PAGE->set_title(get_string('securitykeys', 'webservice'));
$PAGE->set_heading(get_string('securitykeys', 'webservice'));
$PAGE->set_pagelayout('standard');

$rsstokenboxhtml = $webservicetokenboxhtml = '';
/// Manage user web service tokens
if ( !is_siteadmin($USER->id)
    && !empty($CFG->enablewebservices)
    && has_capability('moodle/webservice:createtoken', $usercontext )) {
    require($CFG->dirroot.'/webservice/lib.php');

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

    //no point creating the table is we're just displaying a confirmation screen
    if (empty($resetconfirmation)) {
        $webservice->generate_user_ws_tokens($USER->id); //generate all token that need to be generated
        $tokens = $webservice->get_user_ws_tokens($USER->id);
        foreach ($tokens as $token) {
            if ($token->restrictedusers) {
                $authlist = $webservice->get_ws_authorised_user($token->wsid, $USER->id);
                if (empty($authlist)) {
                    $token->enabled = false;
                }
            }
        }
        $webservicetokenboxhtml =  $wsrenderer->user_webservice_tokens_box($tokens, $USER->id,
                $CFG->enablewsdocumentation); //display the box for web service token
    }
}

//RSS keys
if (!empty($CFG->enablerssfeeds)) {
    require_once($CFG->dirroot.'/lib/rsslib.php');

    $action  = optional_param('action', '', PARAM_ACTION);
    $confirm = optional_param('confirm', 0, PARAM_BOOL);

    $rssrenderer = $PAGE->get_renderer('core', 'rss');

    if ($action=='resetrsstoken') {
        /// Display confirmation page to Reset the token
        if (!$confirm) {
            $resetconfirmation = $rssrenderer->user_reset_rss_token_confirmation();
        } else {
            rss_delete_token($USER->id);
        }
    }
    if (empty($resetconfirmation)) {
        $token = rss_get_token($USER->id);
        $rsstokenboxhtml = $rssrenderer->user_rss_token_box($token); //display the box for the user's RSS token
    }
}

// PAGE OUTPUT
echo $OUTPUT->header();
if (!empty($resetconfirmation)) {
    echo $resetconfirmation;
} else {
    echo $webservicetokenboxhtml;
    echo $rsstokenboxhtml;
}
echo $OUTPUT->footer();


