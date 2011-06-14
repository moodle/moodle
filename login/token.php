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
 * Return token
 * @package    moodlecore
 * @copyright  2011 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);
define('NO_MOODLE_COOKIES', true);

require_once(dirname(dirname(__FILE__)) . '/config.php');

$username = required_param('username', PARAM_USERNAME);
$password = required_param('password', PARAM_RAW);
$service  = required_param('service',  PARAM_ALPHANUMEXT);

echo $OUTPUT->header();

if (!$CFG->enablewebservices) {
    throw new moodle_exception('enablewsdescription', 'webservice');
}
$username = trim(moodle_strtolower($username));
if (is_restored_user($username)) {
    throw new moodle_exception('restoredaccountresetpassword', 'webservice');
}
$user = authenticate_user_login($username, $password);
if (!empty($user)) {
    if (isguestuser($user)) {
        throw new moodle_exception('noguest');
    }
    if (empty($user->confirmed)) {
        throw new moodle_exception('usernotconfirmed', 'moodle', '', $user->username);
    }
    // check credential expiry
    $userauth = get_auth_plugin($user->auth);
    if (!empty($userauth->config->expiration) and $userauth->config->expiration == 1) {
        $days2expire = $userauth->password_expire($user->username);
        if (intval($days2expire) < 0 ) {
            throw new moodle_exception('passwordisexpired', 'webservice');
        }
    }

    // setup user session to check capability
    session_set_user($user);

    $admintokenssql = "SELECT t.*
              FROM {external_tokens} t
              JOIN {external_services} s
                   ON t.externalserviceid = s.id
             WHERE s.shortname = ?
               AND s.enabled = 1
               AND t.userid = ?
               AND (t.validuntil = 0 OR t.validuntil IS NULL OR t.validuntil > ?)
               AND t.userid != t.creatorid
          ORDER BY t.timecreated ASC";
    $tokens = $DB->get_records_sql($admintokenssql, array($service, $user->id, time()));
    foreach ($tokens as $key=>$admin_token) {
        // remove token if its ip not in whitelist
        if (isset($admin_token->iprestriction) and !address_in_subnet(getremoteaddr(), $admin_token->iprestriction)) {
            unset($tokens[$key]);
        }
    }
    // if admin created token then use the most recent created one over user created token
    if (count($tokens) > 0) {
        $token = array_pop($tokens);
    } else {
        // if no admin created tokens, try to use user created token
        // NOTE user created token doesn't have valid date and ip limits
        $usertokensql = "SELECT t.*
                  FROM {external_tokens} t
                  JOIN {external_services} s
                       ON t.externalserviceid = s.id
                 WHERE s.shortname = ?
                   AND s.enabled = 1
                   AND t.userid = ?
                   AND t.userid = t.creatorid";

        $token = $DB->get_record_sql($usertokensql, array($service, $user->id));
        // create token if not exists
        if (!$token) {
            // This is an exception for Moodle Mobiel App
            // if user doesn't have token, we will create one on the fly
            // even user doesn't have createtoken permission
            if ($service == MOODLE_OFFICIAL_MOBILE_SERVICE) {
                if (has_capability('moodle/webservice:createmobiletoken', get_system_context())) {
                    // if service doesn't exist, dml will throw exception
                    $service_record = $DB->get_record('external_services', array('shortname'=>$service, 'enabled'=>1), '*', MUST_EXIST);
                    // create a new token
                    $token = new stdClass;
                    $token->token = md5(uniqid(rand(), 1));
                    $token->userid = $user->id;
                    $token->tokentype = EXTERNAL_TOKEN_PERMANENT;
                    $token->contextid = get_context_instance(CONTEXT_SYSTEM)->id;
                    $token->creatorid = $user->id;
                    $token->timecreated = time();
                    $token->externalserviceid = $service_record->id;
                    $tokenid = $DB->insert_record('external_tokens', $token);
                    add_to_log(SITEID, 'webservice', get_string('createtokenforuserauto', 'webservice'), '' , 'User ID: ' . $user->id);
                    $token->id = $tokenid;
                } else {
                    throw new moodle_exception('cannotcreatemobiletoken', 'webservice');
                }
            } else {
                // will throw exception if no token found
                throw new moodle_exception('invalidtoken', 'webservice');
            }
        }
    }

    // log token access
    $DB->set_field('external_tokens', 'lastaccess', time(), array('id'=>$token->id));

    add_to_log(SITEID, 'webservice', 'user request webservice token', '' , 'User ID: ' . $user->id);

    $usertoken = new stdClass;
    $usertoken->token = $token->token;
    echo json_encode($usertoken);
} else {
    throw new moodle_exception('usernamenotfound', 'moodle');
}
