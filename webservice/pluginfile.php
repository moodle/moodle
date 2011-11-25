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
 * A script to serve files from web service client
 *
 * @package    core
 * @subpackage file
 * @copyright  2011 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_MOODLE_COOKIES', true);
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->libdir . '/filelib.php');

$relativepath = get_file_argument();
$token = required_param('token', PARAM_ALPHANUM);

// web service must be enabled to use this script
if (!$CFG->enablewebservices) {
    print_error('enablewsdescription', 'webservice');
}

// Obtain token record
if (!$token = $DB->get_record('external_tokens', array('token'=>$token))) {
    print_error('invalidtoken', 'webservice');
}

//retrieve web service record
$servicesql = 'SELECT s.*
                 FROM {external_services} s, {external_tokens} t
                WHERE t.externalserviceid = s.id
                      AND t.token = ? AND t.userid = ? AND s.enabled = 1';
$service = $DB->get_record_sql($servicesql, array($token->token, $token->userid), MUST_EXIST);

$enabledfiledownload = (int)$service->downloadfiles;

if (empty($enabledfiledownload)) {
    print_error('enabledirectdownload', 'webservice');
}

$user = $DB->get_record('user', array('id'=>$token->userid, 'deleted'=>0), '*', MUST_EXIST);

//Non admin can not authenticate if maintenance mode
$hassiteconfig = has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM), $user);
if (!empty($CFG->maintenance_enabled) and !$hassiteconfig) {
    print_error('sitemaintenance', 'admin');
}

// Validate token date
if ($token->validuntil and $token->validuntil < time()) {
    add_to_log(SITEID, 'webservice', get_string('tokenauthlog', 'webservice'), '' , get_string('invalidtimedtoken', 'webservice'), 0);
    $DB->delete_records('external_tokens', array('token'=>$token->token));
    print_error('invalidtimedtoken', 'webservice');
}

//assumes that if sid is set then there must be a valid associated session no matter the token type
if ($token->sid) {
    $session = session_get_instance();
    if (!$session->session_exists($token->sid)) {
        $DB->delete_records('external_tokens', array('sid'=>$token->sid));
        print_error('invalidtokensession', 'webservice');
    }
}

// Check ip
if ($token->iprestriction and !address_in_subnet(getremoteaddr(), $token->iprestriction)) {
    add_to_log(SITEID, 'webservice', get_string('tokenauthlog', 'webservice'), '' , get_string('failedtolog', 'webservice').": ".getremoteaddr(), 0);
    print_error('invalidiptoken', 'webservice');
}

//only confirmed user should be able to call web service
if (empty($user->confirmed)) {
    add_to_log(SITEID, 'webservice', 'user unconfirmed', '', $user->username);
    print_error('usernotconfirmed', 'moodle', '', $user->username);
}

//check the user is suspended
if (!empty($user->suspended)) {
    add_to_log(SITEID, 'webservice', 'user suspended', '', $user->username);
    print_error('usersuspended', 'webservice');
}

//check if the auth method is nologin (in this case refuse connection)
if ($user->auth == 'nologin') {
    add_to_log(SITEID, 'webservice', 'nologin auth attempt with web service', '', $user->username);
    print_error('nologinauth', 'webservice');
}

$auth  = get_auth_plugin($user->auth);

if (!empty($auth->config->expiration) and $auth->config->expiration == 1) {
    $days2expire = $auth->password_expire($user->username);
    if (intval($days2expire) < 0 ) {
        add_to_log(SITEID, 'webservice', 'expired password', '', $user->username);
        print_error('passwordisexpired', 'webservice');
    }
}

// log token access
$DB->set_field('external_tokens', 'lastaccess', time(), array('id'=>$token->id));
session_set_user($user);

file_pluginfile($relativepath, 0);
