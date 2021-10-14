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
 * Web services utility functions and classes
 *
 * @package    core_webservice
 * @copyright  2009 Jerome Mouneyrac <jerome@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir.'/externallib.php');

/**
 * WEBSERVICE_AUTHMETHOD_USERNAME - username/password authentication (also called simple authentication)
 */
define('WEBSERVICE_AUTHMETHOD_USERNAME', 0);

/**
 * WEBSERVICE_AUTHMETHOD_PERMANENT_TOKEN - most common token authentication (external app, mobile app...)
 */
define('WEBSERVICE_AUTHMETHOD_PERMANENT_TOKEN', 1);

/**
 * WEBSERVICE_AUTHMETHOD_SESSION_TOKEN - token for embedded application (requires Moodle session)
 */
define('WEBSERVICE_AUTHMETHOD_SESSION_TOKEN', 2);

/**
 * General web service library
 *
 * @package    core_webservice
 * @copyright  2010 Jerome Mouneyrac <jerome@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class webservice {
    /**
     * Only update token last access once per this many seconds. (This constant controls update of
     * the external tokens last access field. There is a similar define LASTACCESS_UPDATE_SECS
     * which controls update of the web site last access fields.)
     *
     * @var int
     */
    const TOKEN_LASTACCESS_UPDATE_SECS = 60;

    /**
     * Authenticate user (used by download/upload file scripts)
     *
     * @param string $token
     * @return array - contains the authenticated user, token and service objects
     */
    public function authenticate_user($token) {
        global $DB, $CFG;

        // web service must be enabled to use this script
        if (!$CFG->enablewebservices) {
            throw new webservice_access_exception('Web services are not enabled in Advanced features.');
        }

        // Obtain token record
        if (!$token = $DB->get_record('external_tokens', array('token' => $token))) {
            //client may want to display login form => moodle_exception
            throw new moodle_exception('invalidtoken', 'webservice');
        }

        $loginfaileddefaultparams = array(
            'other' => array(
                'method' => WEBSERVICE_AUTHMETHOD_PERMANENT_TOKEN,
                'reason' => null,
                'tokenid' => $token->id
            )
        );

        // Validate token date
        if ($token->validuntil and $token->validuntil < time()) {
            $params = $loginfaileddefaultparams;
            $params['other']['reason'] = 'token_expired';
            $event = \core\event\webservice_login_failed::create($params);
            $event->add_record_snapshot('external_tokens', $token);
            $event->set_legacy_logdata(array(SITEID, 'webservice', get_string('tokenauthlog', 'webservice'), '',
                get_string('invalidtimedtoken', 'webservice'), 0));
            $event->trigger();
            $DB->delete_records('external_tokens', array('token' => $token->token));
            throw new webservice_access_exception('Invalid token - token expired - check validuntil time for the token');
        }

        // Check ip
        if ($token->iprestriction and !address_in_subnet(getremoteaddr(), $token->iprestriction)) {
            $params = $loginfaileddefaultparams;
            $params['other']['reason'] = 'ip_restricted';
            $event = \core\event\webservice_login_failed::create($params);
            $event->add_record_snapshot('external_tokens', $token);
            $event->set_legacy_logdata(array(SITEID, 'webservice', get_string('tokenauthlog', 'webservice'), '',
                get_string('failedtolog', 'webservice') . ": " . getremoteaddr(), 0));
            $event->trigger();
            throw new webservice_access_exception('Invalid token - IP:' . getremoteaddr()
                    . ' is not supported');
        }

        //retrieve user link to the token
        $user = $DB->get_record('user', array('id' => $token->userid, 'deleted' => 0), '*', MUST_EXIST);

        // let enrol plugins deal with new enrolments if necessary
        enrol_check_plugins($user);

        // setup user session to check capability
        \core\session\manager::set_user($user);
        set_login_session_preferences();

        //assumes that if sid is set then there must be a valid associated session no matter the token type
        if ($token->sid) {
            if (!\core\session\manager::session_exists($token->sid)) {
                $DB->delete_records('external_tokens', array('sid' => $token->sid));
                throw new webservice_access_exception('Invalid session based token - session not found or expired');
            }
        }

        // Cannot authenticate unless maintenance access is granted.
        $hasmaintenanceaccess = has_capability('moodle/site:maintenanceaccess', context_system::instance(), $user);
        if (!empty($CFG->maintenance_enabled) and !$hasmaintenanceaccess) {
            //this is usually temporary, client want to implement code logic  => moodle_exception
            throw new moodle_exception('sitemaintenance', 'admin');
        }

        //retrieve web service record
        $service = $DB->get_record('external_services', array('id' => $token->externalserviceid, 'enabled' => 1));
        if (empty($service)) {
            // will throw exception if no token found
            throw new webservice_access_exception('Web service is not available (it doesn\'t exist or might be disabled)');
        }

        //check if there is any required system capability
        if ($service->requiredcapability and !has_capability($service->requiredcapability, context_system::instance(), $user)) {
            throw new webservice_access_exception('The capability ' . $service->requiredcapability . ' is required.');
        }

        //specific checks related to user restricted service
        if ($service->restrictedusers) {
            $authoriseduser = $DB->get_record('external_services_users', array('externalserviceid' => $service->id, 'userid' => $user->id));

            if (empty($authoriseduser)) {
                throw new webservice_access_exception(
                        'The user is not allowed for this service. First you need to allow this user on the '
                        . $service->name . '\'s allowed users administration page.');
            }

            if (!empty($authoriseduser->validuntil) and $authoriseduser->validuntil < time()) {
                throw new webservice_access_exception('Invalid service - service expired - check validuntil time for this allowed user');
            }

            if (!empty($authoriseduser->iprestriction) and !address_in_subnet(getremoteaddr(), $authoriseduser->iprestriction)) {
                throw new webservice_access_exception('Invalid service - IP:' . getremoteaddr()
                    . ' is not supported - check this allowed user');
            }
        }

        //only confirmed user should be able to call web service
        if (empty($user->confirmed)) {
            $params = $loginfaileddefaultparams;
            $params['other']['reason'] = 'user_unconfirmed';
            $event = \core\event\webservice_login_failed::create($params);
            $event->add_record_snapshot('external_tokens', $token);
            $event->set_legacy_logdata(array(SITEID, 'webservice', 'user unconfirmed', '', $user->username));
            $event->trigger();
            throw new moodle_exception('usernotconfirmed', 'moodle', '', $user->username);
        }

        //check the user is suspended
        if (!empty($user->suspended)) {
            $params = $loginfaileddefaultparams;
            $params['other']['reason'] = 'user_suspended';
            $event = \core\event\webservice_login_failed::create($params);
            $event->add_record_snapshot('external_tokens', $token);
            $event->set_legacy_logdata(array(SITEID, 'webservice', 'user suspended', '', $user->username));
            $event->trigger();
            throw new webservice_access_exception('Refused web service access for suspended username: ' . $user->username);
        }

        //check if the auth method is nologin (in this case refuse connection)
        if ($user->auth == 'nologin') {
            $params = $loginfaileddefaultparams;
            $params['other']['reason'] = 'nologin';
            $event = \core\event\webservice_login_failed::create($params);
            $event->add_record_snapshot('external_tokens', $token);
            $event->set_legacy_logdata(array(SITEID, 'webservice', 'nologin auth attempt with web service', '', $user->username));
            $event->trigger();
            throw new webservice_access_exception('Refused web service access for nologin authentication username: ' . $user->username);
        }

        //Check if the user password is expired
        $auth = get_auth_plugin($user->auth);
        if (!empty($auth->config->expiration) and $auth->config->expiration == 1) {
            $days2expire = $auth->password_expire($user->username);
            if (intval($days2expire) < 0) {
                $params = $loginfaileddefaultparams;
                $params['other']['reason'] = 'password_expired';
                $event = \core\event\webservice_login_failed::create($params);
                $event->add_record_snapshot('external_tokens', $token);
                $event->set_legacy_logdata(array(SITEID, 'webservice', 'expired password', '', $user->username));
                $event->trigger();
                throw new moodle_exception('passwordisexpired', 'webservice');
            }
        }

        // log token access
        self::update_token_lastaccess($token);

        return array('user' => $user, 'token' => $token, 'service' => $service);
    }

    /**
     * Updates the last access time for a token.
     *
     * @param \stdClass $token Token object (must include id, lastaccess fields)
     * @param int $time Time of access (0 = use current time)
     * @throws dml_exception If database error
     */
    public static function update_token_lastaccess($token, int $time = 0) {
        global $DB;

        if (!$time) {
            $time = time();
        }

        // Only update the field if it is a different time from previous request,
        // so as not to waste database effort.
        if ($time >= $token->lastaccess + self::TOKEN_LASTACCESS_UPDATE_SECS) {
            $DB->set_field('external_tokens', 'lastaccess', $time, array('id' => $token->id));
        }
    }

    /**
     * Allow user to call a service
     *
     * @param stdClass $user a user
     */
    public function add_ws_authorised_user($user) {
        global $DB;
        $user->timecreated = time();
        $DB->insert_record('external_services_users', $user);
    }

    /**
     * Disallow a user to call a service
     *
     * @param stdClass $user a user
     * @param int $serviceid
     */
    public function remove_ws_authorised_user($user, $serviceid) {
        global $DB;
        $DB->delete_records('external_services_users',
                array('externalserviceid' => $serviceid, 'userid' => $user->id));
    }

    /**
     * Update allowed user settings (ip restriction, valid until...)
     *
     * @param stdClass $user
     */
    public function update_ws_authorised_user($user) {
        global $DB;
        $DB->update_record('external_services_users', $user);
    }

    /**
     * Return list of allowed users with their options (ip/timecreated / validuntil...)
     * for a given service
     *
     * @param int $serviceid the service id to search against
     * @return array $users
     */
    public function get_ws_authorised_users($serviceid) {
        global $DB, $CFG;

        $params = array($CFG->siteguest, $serviceid);

        $userfields = \core_user\fields::for_identity(context_system::instance())->with_name()->excluding('id');
        $fieldsql = $userfields->get_sql('u');

        $sql = " SELECT u.id as id, esu.id as serviceuserid {$fieldsql->selects},
                        esu.iprestriction as iprestriction, esu.validuntil as validuntil,
                        esu.timecreated as timecreated
                   FROM {user} u
                   JOIN {external_services_users} esu ON esu.userid = u.id
                        {$fieldsql->joins}
                  WHERE u.id <> ? AND u.deleted = 0 AND u.confirmed = 1
                        AND esu.externalserviceid = ?";

        $users = $DB->get_records_sql($sql, array_merge($fieldsql->params, $params));

        return $users;
    }

    /**
     * Return an authorised user with their options (ip/timecreated / validuntil...)
     *
     * @param int $serviceid the service id to search against
     * @param int $userid the user to search against
     * @return stdClass
     */
    public function get_ws_authorised_user($serviceid, $userid) {
        global $DB, $CFG;
        $params = array($CFG->siteguest, $serviceid, $userid);
        $sql = " SELECT u.id as id, esu.id as serviceuserid, u.email as email, u.firstname as firstname,
                        u.lastname as lastname,
                        esu.iprestriction as iprestriction, esu.validuntil as validuntil,
                        esu.timecreated as timecreated
                   FROM {user} u, {external_services_users} esu
                  WHERE u.id <> ? AND u.deleted = 0 AND u.confirmed = 1
                        AND esu.userid = u.id
                        AND esu.externalserviceid = ?
                        AND u.id = ?";
        $user = $DB->get_record_sql($sql, $params);
        return $user;
    }

    /**
     * Generate all tokens of a specific user
     *
     * @param int $userid user id
     */
    public function generate_user_ws_tokens($userid) {
        global $CFG, $DB;

        // generate a token for non admin if web service are enable and the user has the capability to create a token
        if (!is_siteadmin() && has_capability('moodle/webservice:createtoken', context_system::instance(), $userid) && !empty($CFG->enablewebservices)) {
            // for every service than the user is authorised on, create a token (if it doesn't already exist)

            // get all services which are set to all user (no restricted to specific users)
            $norestrictedservices = $DB->get_records('external_services', array('restrictedusers' => 0));
            $serviceidlist = array();
            foreach ($norestrictedservices as $service) {
                $serviceidlist[] = $service->id;
            }

            // get all services which are set to the current user (the current user is specified in the restricted user list)
            $servicesusers = $DB->get_records('external_services_users', array('userid' => $userid));
            foreach ($servicesusers as $serviceuser) {
                if (!in_array($serviceuser->externalserviceid,$serviceidlist)) {
                     $serviceidlist[] = $serviceuser->externalserviceid;
                }
            }

            // get all services which already have a token set for the current user
            $usertokens = $DB->get_records('external_tokens', array('userid' => $userid, 'tokentype' => EXTERNAL_TOKEN_PERMANENT));
            $tokenizedservice = array();
            foreach ($usertokens as $token) {
                    $tokenizedservice[]  = $token->externalserviceid;
            }

            // create a token for the service which have no token already
            foreach ($serviceidlist as $serviceid) {
                if (!in_array($serviceid, $tokenizedservice)) {
                    // create the token for this service
                    $newtoken = new stdClass();
                    $newtoken->token = md5(uniqid(rand(),1));
                    // check that the user has capability on this service
                    $newtoken->tokentype = EXTERNAL_TOKEN_PERMANENT;
                    $newtoken->userid = $userid;
                    $newtoken->externalserviceid = $serviceid;
                    // TODO MDL-31190 find a way to get the context - UPDATE FOLLOWING LINE
                    $newtoken->contextid = context_system::instance()->id;
                    $newtoken->creatorid = $userid;
                    $newtoken->timecreated = time();
                    // Generate the private token, it must be transmitted only via https.
                    $newtoken->privatetoken = random_string(64);

                    $DB->insert_record('external_tokens', $newtoken);
                }
            }


        }
    }

    /**
     * Return all tokens of a specific user
     * + the service state (enabled/disabled)
     * + the authorised user mode (restricted/not restricted)
     *
     * @param int $userid user id
     * @return array
     */
    public function get_user_ws_tokens($userid) {
        global $DB;
        //here retrieve token list (including linked users firstname/lastname and linked services name)
        $sql = "SELECT
                    t.id, t.creatorid, t.token, u.firstname, u.lastname, s.id as wsid, s.name, s.enabled, s.restrictedusers, t.validuntil
                FROM
                    {external_tokens} t, {user} u, {external_services} s
                WHERE
                    t.userid=? AND t.tokentype = ".EXTERNAL_TOKEN_PERMANENT." AND s.id = t.externalserviceid AND t.userid = u.id";
        $tokens = $DB->get_records_sql($sql, array( $userid));
        return $tokens;
    }

    /**
     * Return a token that has been created by the user (i.e. to created by an admin)
     * If no tokens exist an exception is thrown
     *
     * The returned value is a stdClass:
     * ->id token id
     * ->token
     * ->firstname user firstname
     * ->lastname
     * ->name service name
     *
     * @param int $userid user id
     * @param int $tokenid token id
     * @return stdClass
     */
    public function get_created_by_user_ws_token($userid, $tokenid) {
        global $DB;
        $sql = "SELECT
                        t.id, t.token, u.firstname, u.lastname, s.name
                    FROM
                        {external_tokens} t, {user} u, {external_services} s
                    WHERE
                        t.creatorid=? AND t.id=? AND t.tokentype = "
                . EXTERNAL_TOKEN_PERMANENT
                . " AND s.id = t.externalserviceid AND t.userid = u.id";
        //must be the token creator
        $token = $DB->get_record_sql($sql, array($userid, $tokenid), MUST_EXIST);
        return $token;
    }

    /**
     * Return a token of an arbitrary user by tokenid, including details of the associated user and the service name.
     * If no tokens exist an exception is thrown
     *
     * The returned value is a stdClass:
     * ->id token id
     * ->token
     * ->firstname user firstname
     * ->lastname
     * ->name service name
     *
     * @param int $tokenid token id
     * @return stdClass
     */
    public function get_token_by_id_with_details($tokenid) {
        global $DB;
        $sql = "SELECT t.id, t.token, u.id AS userid, u.firstname, u.lastname, s.name, t.creatorid
                FROM {external_tokens} t, {user} u, {external_services} s
                WHERE t.id=? AND t.tokentype = ? AND s.id = t.externalserviceid AND t.userid = u.id";
        $token = $DB->get_record_sql($sql, array($tokenid, EXTERNAL_TOKEN_PERMANENT), MUST_EXIST);
        return $token;
    }

    /**
     * Return a database token record for a token id
     *
     * @param int $tokenid token id
     * @return object token
     */
    public function get_token_by_id($tokenid) {
        global $DB;
        return $DB->get_record('external_tokens', array('id' => $tokenid));
    }

    /**
     * Delete a token
     *
     * @param int $tokenid token id
     */
    public function delete_user_ws_token($tokenid) {
        global $DB;
        $DB->delete_records('external_tokens', array('id'=>$tokenid));
    }

    /**
     * Delete all the tokens belonging to a user.
     *
     * @param int $userid the user id whose tokens must be deleted
     */
    public static function delete_user_ws_tokens($userid) {
        global $DB;
        $DB->delete_records('external_tokens', array('userid' => $userid));
    }

    /**
     * Delete a service
     * Also delete function references and authorised user references.
     *
     * @param int $serviceid service id
     */
    public function delete_service($serviceid) {
        global $DB;
        $DB->delete_records('external_services_users', array('externalserviceid' => $serviceid));
        $DB->delete_records('external_services_functions', array('externalserviceid' => $serviceid));
        $DB->delete_records('external_tokens', array('externalserviceid' => $serviceid));
        $DB->delete_records('external_services', array('id' => $serviceid));
    }

    /**
     * Get a full database token record for a given token value
     *
     * @param string $token
     * @throws moodle_exception if there is multiple result
     */
    public function get_user_ws_token($token) {
        global $DB;
        return $DB->get_record('external_tokens', array('token'=>$token), '*', MUST_EXIST);
    }

    /**
     * Get the functions list of a service list (by id)
     *
     * @param array $serviceids service ids
     * @return array of functions
     */
    public function get_external_functions($serviceids) {
        global $DB;
        if (!empty($serviceids)) {
            list($serviceids, $params) = $DB->get_in_or_equal($serviceids);
            $sql = "SELECT f.*
                      FROM {external_functions} f
                     WHERE f.name IN (SELECT sf.functionname
                                        FROM {external_services_functions} sf
                                       WHERE sf.externalserviceid $serviceids)
                     ORDER BY f.name ASC";
            $functions = $DB->get_records_sql($sql, $params);
        } else {
            $functions = array();
        }
        return $functions;
    }

    /**
     * Get the functions of a service list (by shortname). It can return only enabled functions if required.
     *
     * @param array $serviceshortnames service shortnames
     * @param bool $enabledonly if true then only return functions for services that have been enabled
     * @return array functions
     */
    public function get_external_functions_by_enabled_services($serviceshortnames, $enabledonly = true) {
        global $DB;
        if (!empty($serviceshortnames)) {
            $enabledonlysql = $enabledonly?' AND s.enabled = 1 ':'';
            list($serviceshortnames, $params) = $DB->get_in_or_equal($serviceshortnames);
            $sql = "SELECT f.*
                      FROM {external_functions} f
                     WHERE f.name IN (SELECT sf.functionname
                                        FROM {external_services_functions} sf, {external_services} s
                                       WHERE s.shortname $serviceshortnames
                                             AND sf.externalserviceid = s.id
                                             " . $enabledonlysql . ")";
            $functions = $DB->get_records_sql($sql, $params);
        } else {
            $functions = array();
        }
        return $functions;
    }

    /**
     * Get functions not included in a service
     *
     * @param int $serviceid service id
     * @return array functions
     */
    public function get_not_associated_external_functions($serviceid) {
        global $DB;
        $select = "name NOT IN (SELECT s.functionname
                                  FROM {external_services_functions} s
                                 WHERE s.externalserviceid = :sid
                               )";

        $functions = $DB->get_records_select('external_functions',
                        $select, array('sid' => $serviceid), 'name');

        return $functions;
    }

    /**
     * Get list of required capabilities of a service, sorted by functions
     * Example of returned value:
     *  Array
     *  (
     *    [core_group_create_groups] => Array
     *    (
     *       [0] => moodle/course:managegroups
     *    )
     *
     *    [core_enrol_get_enrolled_users] => Array
     *    (
     *       [0] => moodle/user:viewdetails
     *       [1] => moodle/user:viewhiddendetails
     *       [2] => moodle/course:useremail
     *       [3] => moodle/user:update
     *       [4] => moodle/site:accessallgroups
     *    )
     *  )
     * @param int $serviceid service id
     * @return array
     */
    public function get_service_required_capabilities($serviceid) {
        $functions = $this->get_external_functions(array($serviceid));
        $requiredusercaps = array();
        foreach ($functions as $function) {
            $functioncaps = explode(',', $function->capabilities);
            if (!empty($functioncaps) and !empty($functioncaps[0])) {
                foreach ($functioncaps as $functioncap) {
                    $requiredusercaps[$function->name][] = trim($functioncap);
                }
            }
        }
        return $requiredusercaps;
    }

    /**
     * Get user capabilities (with context)
     * Only useful for documentation purpose
     * WARNING: do not use this "broken" function. It was created in the goal to display some capabilities
     * required by users. In theory we should not need to display this kind of information
     * as the front end does not display it itself. In pratice,
     * admins would like the info, for more info you can follow: MDL-29962
     *
     * @deprecated since Moodle 3.11 in MDL-67748 without a replacement.
     * @todo MDL-70187 Please delete this method completely in Moodle 4.3, thank you.
     * @param int $userid user id
     * @return array
     */
    public function get_user_capabilities($userid) {
        global $DB;

        debugging('webservice::get_user_capabilities() has been deprecated.', DEBUG_DEVELOPER);

        //retrieve the user capabilities
        $sql = "SELECT DISTINCT rc.id, rc.capability FROM {role_capabilities} rc, {role_assignments} ra
            WHERE rc.roleid=ra.roleid AND ra.userid= ? AND rc.permission = ?";
        $dbusercaps = $DB->get_records_sql($sql, array($userid, CAP_ALLOW));
        $usercaps = array();
        foreach ($dbusercaps as $usercap) {
            $usercaps[$usercap->capability] = true;
        }
        return $usercaps;
    }

    /**
     * Get missing user capabilities for the given service's functions.
     *
     * Every external function can declare some required capabilities to allow for easier setup of the web services.
     * However, that is supposed to be used for informational admin report only. There is no automatic evaluation of
     * the declared capabilities and the context of the capability evaluation is ignored. Also, actual capability
     * evaluation is much more complex as it allows for overrides etc.
     *
     * Returned are capabilities that the given users do not seem to have assigned anywhere at the site and that should
     * be checked by the admin.
     *
     * Do not use this method for anything else, particularly not for any security related checks. See MDL-29962 for the
     * background of why we have this - there are arguments for dropping this feature completely.
     *
     * @param array $users List of users to check, consisting of objects, arrays or integer ids.
     * @param int $serviceid The id of the external service to check.
     * @return array List of missing capabilities: (int)userid => array of (string)capabilitynames
     */
    public function get_missing_capabilities_by_users(array $users, int $serviceid): array {
        global $DB;

        // The following are default capabilities for all authenticated users and we will assume them granted.
        $commoncaps = get_default_capabilities('user');

        // Get the list of additional capabilities required by the service.
        $servicecaps = [];
        foreach ($this->get_service_required_capabilities($serviceid) as $service => $caps) {
            foreach ($caps as $cap) {
                if (empty($commoncaps[$cap])) {
                    $servicecaps[$cap] = true;
                }
            }
        }

        if (empty($servicecaps)) {
            return [];
        }

        // Prepare a list of user ids we want to check.
        $userids = [];
        foreach ($users as $user) {
            if (is_object($user) && isset($user->id)) {
                $userids[$user->id] = true;
            } else if (is_array($user) && isset($user['id'])) {
                $userids[$user['id']] = true;
            } else {
                throw new coding_exception('Unexpected format of users list in webservice::get_missing_capabilities_by_users().');
            }
        }

        // Prepare a matrix of missing capabilities x users - consider them all missing by default.
        foreach (array_keys($userids) as $userid) {
            foreach (array_keys($servicecaps) as $capname) {
                $matrix[$userid][$capname] = true;
            }
        }

        list($capsql, $capparams) = $DB->get_in_or_equal(array_keys($servicecaps), SQL_PARAMS_NAMED, 'paramcap');
        list($usersql, $userparams) = $DB->get_in_or_equal(array_keys($userids), SQL_PARAMS_NAMED, 'paramuser');

        $sql = "SELECT c.name AS capability, u.id AS userid
                  FROM {capabilities} c
                  JOIN {role_capabilities} rc ON c.name = rc.capability
                  JOIN {role_assignments} ra ON ra.roleid = rc.roleid
                  JOIN {user} u ON ra.userid = u.id
                 WHERE rc.permission = :capallow
                   AND c.name {$capsql}
                   AND u.id {$usersql}";

        $params = $capparams + $userparams + [
            'capallow' => CAP_ALLOW,
        ];

        $rs = $DB->get_recordset_sql($sql, $params);

        foreach ($rs as $record) {
            // If there was a potential role assignment found that might grant the user the given capability,
            // remove it from the matrix. Again, we ignore all the contexts, prohibits, prevents and other details
            // of the permissions evaluations. See the function docblock for details.
            unset($matrix[$record->userid][$record->capability]);
        }

        $rs->close();

        foreach ($matrix as $userid => $caps) {
            $matrix[$userid] = array_keys($caps);
            if (empty($matrix[$userid])) {
                unset($matrix[$userid]);
            }
        }

        return $matrix;
    }

    /**
     * Get an external service for a given service id
     *
     * @param int $serviceid service id
     * @param int $strictness IGNORE_MISSING, MUST_EXIST...
     * @return stdClass external service
     */
    public function get_external_service_by_id($serviceid, $strictness=IGNORE_MISSING) {
        global $DB;
        $service = $DB->get_record('external_services',
                        array('id' => $serviceid), '*', $strictness);
        return $service;
    }

    /**
     * Get an external service for a given shortname
     *
     * @param string $shortname service shortname
     * @param int $strictness IGNORE_MISSING, MUST_EXIST...
     * @return stdClass external service
     */
    public function get_external_service_by_shortname($shortname, $strictness=IGNORE_MISSING) {
        global $DB;
        $service = $DB->get_record('external_services',
                        array('shortname' => $shortname), '*', $strictness);
        return $service;
    }

    /**
     * Get an external function for a given function id
     *
     * @param int $functionid function id
     * @param int $strictness IGNORE_MISSING, MUST_EXIST...
     * @return stdClass external function
     */
    public function get_external_function_by_id($functionid, $strictness=IGNORE_MISSING) {
        global $DB;
        $function = $DB->get_record('external_functions',
                            array('id' => $functionid), '*', $strictness);
        return $function;
    }

    /**
     * Add a function to a service
     *
     * @param string $functionname function name
     * @param int $serviceid service id
     */
    public function add_external_function_to_service($functionname, $serviceid) {
        global $DB;
        $addedfunction = new stdClass();
        $addedfunction->externalserviceid = $serviceid;
        $addedfunction->functionname = $functionname;
        $DB->insert_record('external_services_functions', $addedfunction);
    }

    /**
     * Add a service
     * It generates the timecreated field automatically.
     *
     * @param stdClass $service
     * @return serviceid integer
     */
    public function add_external_service($service) {
        global $DB;
        $service->timecreated = time();
        $serviceid = $DB->insert_record('external_services', $service);
        return $serviceid;
    }

    /**
     * Update a service
     * It modifies the timemodified automatically.
     *
     * @param stdClass $service
     */
    public function update_external_service($service) {
        global $DB;
        $service->timemodified = time();
        $DB->update_record('external_services', $service);
    }

    /**
     * Test whether an external function is already linked to a service
     *
     * @param string $functionname function name
     * @param int $serviceid service id
     * @return bool true if a matching function exists for the service, else false.
     * @throws dml_exception if error
     */
    public function service_function_exists($functionname, $serviceid) {
        global $DB;
        return $DB->record_exists('external_services_functions',
                            array('externalserviceid' => $serviceid,
                                'functionname' => $functionname));
    }

    /**
     * Remove a function from a service
     *
     * @param string $functionname function name
     * @param int $serviceid service id
     */
    public function remove_external_function_from_service($functionname, $serviceid) {
        global $DB;
        $DB->delete_records('external_services_functions',
                    array('externalserviceid' => $serviceid, 'functionname' => $functionname));

    }

    /**
     * Return a list with all the valid user tokens for the given user, it only excludes expired tokens.
     *
     * @param  string $userid user id to retrieve tokens from
     * @return array array of token entries
     * @since Moodle 3.2
     */
    public static function get_active_tokens($userid) {
        global $DB;

        $sql = 'SELECT t.*, s.name as servicename FROM {external_tokens} t JOIN
                {external_services} s ON t.externalserviceid = s.id WHERE
                t.userid = :userid AND (COALESCE(t.validuntil, 0) = 0 OR t.validuntil > :now)';
        $params = array('userid' => $userid, 'now' => time());
        return $DB->get_records_sql($sql, $params);
    }
}

/**
 * Exception indicating access control problem in web service call
 * This exception should return general errors about web service setup.
 * Errors related to the user like wrong username/password should not use it,
 * you should not use this exception if you want to let the client implement
 * some code logic against an access error.
 *
 * @package    core_webservice
 * @copyright  2009 Petr Skodak
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class webservice_access_exception extends moodle_exception {

    /**
     * Constructor
     *
     * @param string $debuginfo the debug info
     */
    function __construct($debuginfo) {
        parent::__construct('accessexception', 'webservice', '', null, $debuginfo);
    }
}

/**
 * Check if a protocol is enabled
 *
 * @param string $protocol name of WS protocol ('rest', 'soap', 'xmlrpc'...)
 * @return bool true if the protocol is enabled
 */
function webservice_protocol_is_enabled($protocol) {
    global $CFG;

    if (empty($CFG->enablewebservices)) {
        return false;
    }

    $active = explode(',', $CFG->webserviceprotocols);

    return(in_array($protocol, $active));
}

/**
 * Mandatory interface for all test client classes.
 *
 * @package    core_webservice
 * @copyright  2009 Petr Skodak
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface webservice_test_client_interface {

    /**
     * Execute test client WS request
     *
     * @param string $serverurl server url (including the token param)
     * @param string $function web service function name
     * @param array $params parameters of the web service function
     * @return mixed
     */
    public function simpletest($serverurl, $function, $params);
}

/**
 * Mandatory interface for all web service protocol classes
 *
 * @package    core_webservice
 * @copyright  2009 Petr Skodak
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface webservice_server_interface {

    /**
     * Process request from client.
     */
    public function run();
}

/**
 * Abstract web service base class.
 *
 * @package    core_webservice
 * @copyright  2009 Petr Skodak
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class webservice_server implements webservice_server_interface {

    /** @var string Name of the web server plugin */
    protected $wsname = null;

    /** @var string Name of local user */
    protected $username = null;

    /** @var string Password of the local user */
    protected $password = null;

    /** @var int The local user */
    protected $userid = null;

    /** @var integer Authentication method one of WEBSERVICE_AUTHMETHOD_* */
    protected $authmethod;

    /** @var string Authentication token*/
    protected $token = null;

    /** @var stdClass Restricted context */
    protected $restricted_context;

    /** @var int Restrict call to one service id*/
    protected $restricted_serviceid = null;

    /**
     * Constructor
     *
     * @param integer $authmethod authentication method one of WEBSERVICE_AUTHMETHOD_*
     */
    public function __construct($authmethod) {
        $this->authmethod = $authmethod;
    }


    /**
     * Authenticate user using username+password or token.
     * This function sets up $USER global.
     * It is safe to use has_capability() after this.
     * This method also verifies user is allowed to use this
     * server.
     */
    protected function authenticate_user() {
        global $CFG, $DB;

        if (!NO_MOODLE_COOKIES) {
            throw new coding_exception('Cookies must be disabled in WS servers!');
        }

        $loginfaileddefaultparams = array(
            'other' => array(
                'method' => $this->authmethod,
                'reason' => null
            )
        );

        if ($this->authmethod == WEBSERVICE_AUTHMETHOD_USERNAME) {

            //we check that authentication plugin is enabled
            //it is only required by simple authentication
            if (!is_enabled_auth('webservice')) {
                throw new webservice_access_exception('The web service authentication plugin is disabled.');
            }

            if (!$auth = get_auth_plugin('webservice')) {
                throw new webservice_access_exception('The web service authentication plugin is missing.');
            }

            $this->restricted_context = context_system::instance();

            if (!$this->username) {
                throw new moodle_exception('missingusername', 'webservice');
            }

            if (!$this->password) {
                throw new moodle_exception('missingpassword', 'webservice');
            }

            if (!$auth->user_login_webservice($this->username, $this->password)) {

                // Log failed login attempts.
                $params = $loginfaileddefaultparams;
                $params['other']['reason'] = 'password';
                $params['other']['username'] = $this->username;
                $event = \core\event\webservice_login_failed::create($params);
                $event->set_legacy_logdata(array(SITEID, 'webservice', get_string('simpleauthlog', 'webservice'), '' ,
                    get_string('failedtolog', 'webservice').": ".$this->username."/".$this->password." - ".getremoteaddr() , 0));
                $event->trigger();

                throw new moodle_exception('wrongusernamepassword', 'webservice');
            }

            $user = $DB->get_record('user', array('username'=>$this->username, 'mnethostid'=>$CFG->mnet_localhost_id), '*', MUST_EXIST);

        } else if ($this->authmethod == WEBSERVICE_AUTHMETHOD_PERMANENT_TOKEN){
            $user = $this->authenticate_by_token(EXTERNAL_TOKEN_PERMANENT);
        } else {
            $user = $this->authenticate_by_token(EXTERNAL_TOKEN_EMBEDDED);
        }

        // Cannot authenticate unless maintenance access is granted.
        $hasmaintenanceaccess = has_capability('moodle/site:maintenanceaccess', context_system::instance(), $user);
        if (!empty($CFG->maintenance_enabled) and !$hasmaintenanceaccess) {
            throw new moodle_exception('sitemaintenance', 'admin');
        }

        //only confirmed user should be able to call web service
        if (!empty($user->deleted)) {
            $params = $loginfaileddefaultparams;
            $params['other']['reason'] = 'user_deleted';
            $params['other']['username'] = $user->username;
            $event = \core\event\webservice_login_failed::create($params);
            $event->set_legacy_logdata(array(SITEID, '', '', '', get_string('wsaccessuserdeleted', 'webservice',
                $user->username) . " - ".getremoteaddr(), 0, $user->id));
            $event->trigger();
            throw new webservice_access_exception('Refused web service access for deleted username: ' . $user->username);
        }

        //only confirmed user should be able to call web service
        if (empty($user->confirmed)) {
            $params = $loginfaileddefaultparams;
            $params['other']['reason'] = 'user_unconfirmed';
            $params['other']['username'] = $user->username;
            $event = \core\event\webservice_login_failed::create($params);
            $event->set_legacy_logdata(array(SITEID, '', '', '', get_string('wsaccessuserunconfirmed', 'webservice',
                $user->username) . " - ".getremoteaddr(), 0, $user->id));
            $event->trigger();
            throw new moodle_exception('wsaccessuserunconfirmed', 'webservice', '', $user->username);
        }

        //check the user is suspended
        if (!empty($user->suspended)) {
            $params = $loginfaileddefaultparams;
            $params['other']['reason'] = 'user_unconfirmed';
            $params['other']['username'] = $user->username;
            $event = \core\event\webservice_login_failed::create($params);
            $event->set_legacy_logdata(array(SITEID, '', '', '', get_string('wsaccessusersuspended', 'webservice',
                $user->username) . " - ".getremoteaddr(), 0, $user->id));
            $event->trigger();
            throw new webservice_access_exception('Refused web service access for suspended username: ' . $user->username);
        }

        //retrieve the authentication plugin if no previously done
        if (empty($auth)) {
          $auth  = get_auth_plugin($user->auth);
        }

        // check if credentials have expired
        if (!empty($auth->config->expiration) and $auth->config->expiration == 1) {
            $days2expire = $auth->password_expire($user->username);
            if (intval($days2expire) < 0 ) {
                $params = $loginfaileddefaultparams;
                $params['other']['reason'] = 'password_expired';
                $params['other']['username'] = $user->username;
                $event = \core\event\webservice_login_failed::create($params);
                $event->set_legacy_logdata(array(SITEID, '', '', '', get_string('wsaccessuserexpired', 'webservice',
                    $user->username) . " - ".getremoteaddr(), 0, $user->id));
                $event->trigger();
                throw new webservice_access_exception('Refused web service access for password expired username: ' . $user->username);
            }
        }

        //check if the auth method is nologin (in this case refuse connection)
        if ($user->auth=='nologin') {
            $params = $loginfaileddefaultparams;
            $params['other']['reason'] = 'login';
            $params['other']['username'] = $user->username;
            $event = \core\event\webservice_login_failed::create($params);
            $event->set_legacy_logdata(array(SITEID, '', '', '', get_string('wsaccessusernologin', 'webservice',
                $user->username) . " - ".getremoteaddr(), 0, $user->id));
            $event->trigger();
            throw new webservice_access_exception('Refused web service access for nologin authentication username: ' . $user->username);
        }

        // now fake user login, the session is completely empty too
        enrol_check_plugins($user);
        \core\session\manager::set_user($user);
        set_login_session_preferences();
        $this->userid = $user->id;

        if ($this->authmethod != WEBSERVICE_AUTHMETHOD_SESSION_TOKEN && !has_capability("webservice/$this->wsname:use", $this->restricted_context)) {
            throw new webservice_access_exception('You are not allowed to use the {$a} protocol (missing capability: webservice/' . $this->wsname . ':use)');
        }

        external_api::set_context_restriction($this->restricted_context);
    }

    /**
     * User authentication by token
     *
     * @param string $tokentype token type (EXTERNAL_TOKEN_EMBEDDED or EXTERNAL_TOKEN_PERMANENT)
     * @return stdClass the authenticated user
     * @throws webservice_access_exception
     */
    protected function authenticate_by_token($tokentype){
        global $DB;

        $loginfaileddefaultparams = array(
            'other' => array(
                'method' => $this->authmethod,
                'reason' => null
            )
        );

        if (!$token = $DB->get_record('external_tokens', array('token'=>$this->token, 'tokentype'=>$tokentype))) {
            // Log failed login attempts.
            $params = $loginfaileddefaultparams;
            $params['other']['reason'] = 'invalid_token';
            $event = \core\event\webservice_login_failed::create($params);
            $event->set_legacy_logdata(array(SITEID, 'webservice', get_string('tokenauthlog', 'webservice'), '' ,
                get_string('failedtolog', 'webservice').": ".$this->token. " - ".getremoteaddr() , 0));
            $event->trigger();
            throw new moodle_exception('invalidtoken', 'webservice');
        }

        if ($token->validuntil and $token->validuntil < time()) {
            $DB->delete_records('external_tokens', array('token'=>$this->token, 'tokentype'=>$tokentype));
            throw new webservice_access_exception('Invalid token - token expired - check validuntil time for the token');
        }

        if ($token->sid){//assumes that if sid is set then there must be a valid associated session no matter the token type
            if (!\core\session\manager::session_exists($token->sid)){
                $DB->delete_records('external_tokens', array('sid'=>$token->sid));
                throw new webservice_access_exception('Invalid session based token - session not found or expired');
            }
        }

        if ($token->iprestriction and !address_in_subnet(getremoteaddr(), $token->iprestriction)) {
            $params = $loginfaileddefaultparams;
            $params['other']['reason'] = 'ip_restricted';
            $params['other']['tokenid'] = $token->id;
            $event = \core\event\webservice_login_failed::create($params);
            $event->add_record_snapshot('external_tokens', $token);
            $event->set_legacy_logdata(array(SITEID, 'webservice', get_string('tokenauthlog', 'webservice'), '' ,
                get_string('failedtolog', 'webservice').": ".getremoteaddr() , 0));
            $event->trigger();
            throw new webservice_access_exception('Invalid service - IP:' . getremoteaddr()
                    . ' is not supported - check this allowed user');
        }

        $this->restricted_context = context::instance_by_id($token->contextid);
        $this->restricted_serviceid = $token->externalserviceid;

        $user = $DB->get_record('user', array('id'=>$token->userid), '*', MUST_EXIST);

        // log token access
        webservice::update_token_lastaccess($token);

        return $user;

    }

    /**
     * Intercept some moodlewssettingXXX $_GET and $_POST parameter
     * that are related to the web service call and are not the function parameters
     */
    protected function set_web_service_call_settings() {
        global $CFG;

        // Default web service settings.
        // Must be the same XXX key name as the external_settings::set_XXX function.
        // Must be the same XXX ws parameter name as 'moodlewssettingXXX'.
        $externalsettings = array(
            'raw' => array('default' => false, 'type' => PARAM_BOOL),
            'fileurl' => array('default' => true, 'type' => PARAM_BOOL),
            'filter' => array('default' => false, 'type' => PARAM_BOOL),
            'lang' => array('default' => '', 'type' => PARAM_LANG),
            'timezone' => array('default' => '', 'type' => PARAM_TIMEZONE),
        );

        // Load the external settings with the web service settings.
        $settings = external_settings::get_instance();
        foreach ($externalsettings as $name => $settingdata) {

            $wsparamname = 'moodlewssetting' . $name;

            // Retrieve and remove the setting parameter from the request.
            $value = optional_param($wsparamname, $settingdata['default'], $settingdata['type']);
            unset($_GET[$wsparamname]);
            unset($_POST[$wsparamname]);

            $functioname = 'set_' . $name;
            $settings->$functioname($value);
        }

    }
}

/**
 * Web Service server base class.
 *
 * This class handles both simple and token authentication.
 *
 * @package    core_webservice
 * @copyright  2009 Petr Skodak
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class webservice_base_server extends webservice_server {

    /** @var array The function parameters - the real values submitted in the request */
    protected $parameters = null;

    /** @var string The name of the function that is executed */
    protected $functionname = null;

    /** @var stdClass Full function description */
    protected $function = null;

    /** @var mixed Function return value */
    protected $returns = null;

    /** @var array List of methods and their information provided by the web service. */
    protected $servicemethods;

    /** @var  array List of struct classes generated for the web service methods. */
    protected $servicestructs;

    /**
     * This method parses the request input, it needs to get:
     *  1/ user authentication - username+password or token
     *  2/ function name
     *  3/ function parameters
     */
    abstract protected function parse_request();

    /**
     * Send the result of function call to the WS client.
     */
    abstract protected function send_response();

    /**
     * Send the error information to the WS client.
     *
     * @param exception $ex
     */
    abstract protected function send_error($ex=null);

    /**
     * Process request from client.
     *
     * @uses die
     */
    public function run() {
        global $CFG, $USER, $SESSION;

        // we will probably need a lot of memory in some functions
        raise_memory_limit(MEMORY_EXTRA);

        // set some longer timeout, this script is not sending any output,
        // this means we need to manually extend the timeout operations
        // that need longer time to finish
        external_api::set_timeout();

        // set up exception handler first, we want to sent them back in correct format that
        // the other system understands
        // we do not need to call the original default handler because this ws handler does everything
        set_exception_handler(array($this, 'exception_handler'));

        // init all properties from the request data
        $this->parse_request();

        // authenticate user, this has to be done after the request parsing
        // this also sets up $USER and $SESSION
        $this->authenticate_user();

        // find all needed function info and make sure user may actually execute the function
        $this->load_function_info();

        // Log the web service request.
        $params = array(
            'other' => array(
                'function' => $this->functionname
            )
        );
        $event = \core\event\webservice_function_called::create($params);
        $event->set_legacy_logdata(array(SITEID, 'webservice', $this->functionname, '' , getremoteaddr() , 0, $this->userid));
        $event->trigger();

        // Do additional setup stuff.
        $settings = external_settings::get_instance();
        $sessionlang = $settings->get_lang();
        if (!empty($sessionlang)) {
            $SESSION->lang = $sessionlang;
        }

        setup_lang_from_browser();

        if (empty($CFG->lang)) {
            if (empty($SESSION->lang)) {
                $CFG->lang = 'en';
            } else {
                $CFG->lang = $SESSION->lang;
            }
        }

        // Change timezone only in sites where it isn't forced.
        $newtimezone = $settings->get_timezone();
        if (!empty($newtimezone) && (!isset($CFG->forcetimezone) || $CFG->forcetimezone == 99)) {
            $USER->timezone = $newtimezone;
        }

        // finally, execute the function - any errors are catched by the default exception handler
        $this->execute();

        // send the results back in correct format
        $this->send_response();

        // session cleanup
        $this->session_cleanup();

        die;
    }

    /**
     * Specialised exception handler, we can not use the standard one because
     * it can not just print html to output.
     *
     * @param exception $ex
     * $uses exit
     */
    public function exception_handler($ex) {
        // detect active db transactions, rollback and log as error
        abort_all_db_transactions();

        // some hacks might need a cleanup hook
        $this->session_cleanup($ex);

        // now let the plugin send the exception to client
        $this->send_error($ex);

        // not much else we can do now, add some logging later
        exit(1);
    }

    /**
     * Future hook needed for emulated sessions.
     *
     * @param exception $exception null means normal termination, $exception received when WS call failed
     */
    protected function session_cleanup($exception=null) {
        if ($this->authmethod == WEBSERVICE_AUTHMETHOD_USERNAME) {
            // nothing needs to be done, there is no persistent session
        } else {
            // close emulated session if used
        }
    }

    /**
     * Fetches the function description from database,
     * verifies user is allowed to use this function and
     * loads all paremeters and return descriptions.
     */
    protected function load_function_info() {
        global $DB, $USER, $CFG;

        if (empty($this->functionname)) {
            throw new invalid_parameter_exception('Missing function name');
        }

        // function must exist
        $function = external_api::external_function_info($this->functionname);

        if ($this->restricted_serviceid) {
            $params = array('sid1'=>$this->restricted_serviceid, 'sid2'=>$this->restricted_serviceid);
            $wscond1 = 'AND s.id = :sid1';
            $wscond2 = 'AND s.id = :sid2';
        } else {
            $params = array();
            $wscond1 = '';
            $wscond2 = '';
        }

        // now let's verify access control

        // now make sure the function is listed in at least one service user is allowed to use
        // allow access only if:
        //  1/ entry in the external_services_users table if required
        //  2/ validuntil not reached
        //  3/ has capability if specified in service desc
        //  4/ iprestriction

        $sql = "SELECT s.*, NULL AS iprestriction
                  FROM {external_services} s
                  JOIN {external_services_functions} sf ON (sf.externalserviceid = s.id AND s.restrictedusers = 0 AND sf.functionname = :name1)
                 WHERE s.enabled = 1 $wscond1

                 UNION

                SELECT s.*, su.iprestriction
                  FROM {external_services} s
                  JOIN {external_services_functions} sf ON (sf.externalserviceid = s.id AND s.restrictedusers = 1 AND sf.functionname = :name2)
                  JOIN {external_services_users} su ON (su.externalserviceid = s.id AND su.userid = :userid)
                 WHERE s.enabled = 1 AND (su.validuntil IS NULL OR su.validuntil < :now) $wscond2";
        $params = array_merge($params, array('userid'=>$USER->id, 'name1'=>$function->name, 'name2'=>$function->name, 'now'=>time()));

        $rs = $DB->get_recordset_sql($sql, $params);
        // now make sure user may access at least one service
        $remoteaddr = getremoteaddr();
        $allowed = false;
        foreach ($rs as $service) {
            if ($service->requiredcapability and !has_capability($service->requiredcapability, $this->restricted_context)) {
                continue; // cap required, sorry
            }
            if ($service->iprestriction and !address_in_subnet($remoteaddr, $service->iprestriction)) {
                continue; // wrong request source ip, sorry
            }
            $allowed = true;
            break; // one service is enough, no need to continue
        }
        $rs->close();
        if (!$allowed) {
            throw new webservice_access_exception(
                    'Access to the function '.$this->functionname.'() is not allowed.
                     There could be multiple reasons for this:
                     1. The service linked to the user token does not contain the function.
                     2. The service is user-restricted and the user is not listed.
                     3. The service is IP-restricted and the user IP is not listed.
                     4. The service is time-restricted and the time has expired.
                     5. The token is time-restricted and the time has expired.
                     6. The service requires a specific capability which the user does not have.
                     7. The function is called with username/password (no user token is sent)
                     and none of the services has the function to allow the user.
                     These settings can be found in Administration > Site administration
                     > Server > Web services > External services and Manage tokens.');
        }

        // we have all we need now
        $this->function = $function;
    }

    /**
     * Execute previously loaded function using parameters parsed from the request data.
     */
    protected function execute() {
        // validate params, this also sorts the params properly, we need the correct order in the next part
        $params = call_user_func(array($this->function->classname, 'validate_parameters'), $this->function->parameters_desc, $this->parameters);
        $params = array_values($params);

        // Allow any Moodle plugin a chance to override this call. This is a convenient spot to
        // make arbitrary behaviour customisations, for example to affect the mobile app behaviour.
        // The overriding plugin could call the 'real' function first and then modify the results,
        // or it could do a completely separate thing.
        $callbacks = get_plugins_with_function('override_webservice_execution');
        foreach ($callbacks as $plugintype => $plugins) {
            foreach ($plugins as $plugin => $callback) {
                $result = $callback($this->function, $params);
                if ($result !== false) {
                    // If the callback returns anything other than false, we assume it replaces the
                    // real function.
                    $this->returns = $result;
                    return;
                }
            }
        }

        // execute - yay!
        $this->returns = call_user_func_array(array($this->function->classname, $this->function->methodname), $params);
    }

    /**
     * Load the virtual class needed for the web service.
     *
     * Initialises the virtual class that contains the web service functions that the user is allowed to use.
     * The web service function will be available if the user:
     * - is validly registered in the external_services_users table.
     * - has the required capability.
     * - meets the IP restriction requirement.
     * This virtual class can be used by web service protocols such as SOAP, especially when generating WSDL.
     */
    protected function init_service_class() {
        global $USER, $DB;

        // Initialise service methods and struct classes.
        $this->servicemethods = array();
        $this->servicestructs = array();

        $params = array();
        $wscond1 = '';
        $wscond2 = '';
        if ($this->restricted_serviceid) {
            $params = array('sid1' => $this->restricted_serviceid, 'sid2' => $this->restricted_serviceid);
            $wscond1 = 'AND s.id = :sid1';
            $wscond2 = 'AND s.id = :sid2';
        }

        $sql = "SELECT s.*, NULL AS iprestriction
                  FROM {external_services} s
                  JOIN {external_services_functions} sf ON (sf.externalserviceid = s.id AND s.restrictedusers = 0)
                 WHERE s.enabled = 1 $wscond1

                 UNION

                SELECT s.*, su.iprestriction
                  FROM {external_services} s
                  JOIN {external_services_functions} sf ON (sf.externalserviceid = s.id AND s.restrictedusers = 1)
                  JOIN {external_services_users} su ON (su.externalserviceid = s.id AND su.userid = :userid)
                 WHERE s.enabled = 1 AND (su.validuntil IS NULL OR su.validuntil < :now) $wscond2";
        $params = array_merge($params, array('userid' => $USER->id, 'now' => time()));

        $serviceids = array();
        $remoteaddr = getremoteaddr();

        // Query list of external services for the user.
        $rs = $DB->get_recordset_sql($sql, $params);

        // Check which service ID to include.
        foreach ($rs as $service) {
            if (isset($serviceids[$service->id])) {
                continue; // Service already added.
            }
            if ($service->requiredcapability and !has_capability($service->requiredcapability, $this->restricted_context)) {
                continue; // Cap required, sorry.
            }
            if ($service->iprestriction and !address_in_subnet($remoteaddr, $service->iprestriction)) {
                continue; // Wrong request source ip, sorry.
            }
            $serviceids[$service->id] = $service->id;
        }
        $rs->close();

        // Generate the virtual class name.
        $classname = 'webservices_virtual_class_000000';
        while (class_exists($classname)) {
            $classname++;
        }
        $this->serviceclass = $classname;

        // Get the list of all available external functions.
        $wsmanager = new webservice();
        $functions = $wsmanager->get_external_functions($serviceids);

        // Generate code for the virtual methods for this web service.
        $methods = '';
        foreach ($functions as $function) {
            $methods .= $this->get_virtual_method_code($function);
        }

        $code = <<<EOD
/**
 * Virtual class web services for user id $USER->id in context {$this->restricted_context->id}.
 */
class $classname {
$methods
}
EOD;
        // Load the virtual class definition into memory.
        eval($code);
    }

    /**
     * Generates a struct class.
     *
     * @param external_single_structure $structdesc The basis of the struct class to be generated.
     * @return string The class name of the generated struct class.
     */
    protected function generate_simple_struct_class(external_single_structure $structdesc) {
        global $USER;

        $propeties = array();
        $fields = array();
        foreach ($structdesc->keys as $name => $fieldsdesc) {
            $type = $this->get_phpdoc_type($fieldsdesc);
            $propertytype = array('type' => $type);
            if (empty($fieldsdesc->allownull) || $fieldsdesc->allownull == NULL_ALLOWED) {
                $propertytype['nillable'] = true;
            }
            $propeties[$name] = $propertytype;
            $fields[] = '    /** @var ' . $type . ' $' . $name . '*/';
            $fields[] = '    public $' . $name .';';
        }
        $fieldsstr = implode("\n", $fields);

        // We do this after the call to get_phpdoc_type() to avoid duplicate class creation.
        $classname = 'webservices_struct_class_000000';
        while (class_exists($classname)) {
            $classname++;
        }
        $code = <<<EOD
/**
 * Virtual struct class for web services for user id $USER->id in context {$this->restricted_context->id}.
 */
class $classname {
$fieldsstr
}
EOD;
        // Load into memory.
        eval($code);

        // Prepare struct info.
        $structinfo = new stdClass();
        $structinfo->classname = $classname;
        $structinfo->properties = $propeties;
        // Add the struct info the the list of service struct classes.
        $this->servicestructs[] = $structinfo;

        return $classname;
    }

    /**
     * Returns a virtual method code for a web service function.
     *
     * @param stdClass $function a record from external_function
     * @return string The PHP code of the virtual method.
     * @throws coding_exception
     * @throws moodle_exception
     */
    protected function get_virtual_method_code($function) {
        $function = external_api::external_function_info($function);

        // Parameters and their defaults for the method signature.
        $paramanddefaults = array();
        // Parameters for external lib call.
        $params = array();
        $paramdesc = array();
        // The method's input parameters and their respective types.
        $inputparams = array();
        // The method's output parameters and their respective types.
        $outputparams = array();

        foreach ($function->parameters_desc->keys as $name => $keydesc) {
            $param = '$' . $name;
            $paramanddefault = $param;
            if ($keydesc->required == VALUE_OPTIONAL) {
                // It does not make sense to declare a parameter VALUE_OPTIONAL. VALUE_OPTIONAL is used only for array/object key.
                throw new moodle_exception('erroroptionalparamarray', 'webservice', '', $name);
            } else if ($keydesc->required == VALUE_DEFAULT) {
                // Need to generate the default, if there is any.
                if ($keydesc instanceof external_value) {
                    if ($keydesc->default === null) {
                        $paramanddefault .= ' = null';
                    } else {
                        switch ($keydesc->type) {
                            case PARAM_BOOL:
                                $default = (int)$keydesc->default;
                                break;
                            case PARAM_INT:
                                $default = $keydesc->default;
                                break;
                            case PARAM_FLOAT;
                                $default = $keydesc->default;
                                break;
                            default:
                                $default = "'$keydesc->default'";
                        }
                        $paramanddefault .= " = $default";
                    }
                } else {
                    // Accept empty array as default.
                    if (isset($keydesc->default) && is_array($keydesc->default) && empty($keydesc->default)) {
                        $paramanddefault .= ' = array()';
                    } else {
                        // For the moment we do not support default for other structure types.
                        throw new moodle_exception('errornotemptydefaultparamarray', 'webservice', '', $name);
                    }
                }
            }

            $params[] = $param;
            $paramanddefaults[] = $paramanddefault;
            $type = $this->get_phpdoc_type($keydesc);
            $inputparams[$name]['type'] = $type;

            $paramdesc[] = '* @param ' . $type . ' $' . $name . ' ' . $keydesc->desc;
        }
        $paramanddefaults = implode(', ', $paramanddefaults);
        $paramdescstr = implode("\n ", $paramdesc);

        $serviceclassmethodbody = $this->service_class_method_body($function, $params);

        if (empty($function->returns_desc)) {
            $return = '* @return void';
        } else {
            $type = $this->get_phpdoc_type($function->returns_desc);
            $outputparams['return']['type'] = $type;
            $return = '* @return ' . $type . ' ' . $function->returns_desc->desc;
        }

        // Now create the virtual method that calls the ext implementation.
        $code = <<<EOD
/**
 * $function->description.
 *
 $paramdescstr
 $return
 */
public function $function->name($paramanddefaults) {
$serviceclassmethodbody
}
EOD;

        // Prepare the method information.
        $methodinfo = new stdClass();
        $methodinfo->name = $function->name;
        $methodinfo->inputparams = $inputparams;
        $methodinfo->outputparams = $outputparams;
        $methodinfo->description = $function->description;
        // Add the method information into the list of service methods.
        $this->servicemethods[] = $methodinfo;

        return $code;
    }

    /**
     * Get the phpdoc type for an external_description object.
     * external_value => int, double or string
     * external_single_structure => object|struct, on-fly generated stdClass name.
     * external_multiple_structure => array
     *
     * @param mixed $keydesc The type description.
     * @return string The PHP doc type of the external_description object.
     */
    protected function get_phpdoc_type($keydesc) {
        $type = null;
        if ($keydesc instanceof external_value) {
            switch ($keydesc->type) {
                case PARAM_BOOL: // 0 or 1 only for now.
                case PARAM_INT:
                    $type = 'int';
                    break;
                case PARAM_FLOAT;
                    $type = 'double';
                    break;
                default:
                    $type = 'string';
            }
        } else if ($keydesc instanceof external_single_structure) {
            $type = $this->generate_simple_struct_class($keydesc);
        } else if ($keydesc instanceof external_multiple_structure) {
            $type = 'array';
        }

        return $type;
    }

    /**
     * Generates the method body of the virtual external function.
     *
     * @param stdClass $function a record from external_function.
     * @param array $params web service function parameters.
     * @return string body of the method for $function ie. everything within the {} of the method declaration.
     */
    protected function service_class_method_body($function, $params) {
        // Cast the param from object to array (validate_parameters except array only).
        $castingcode = '';
        $paramsstr = '';
        if (!empty($params)) {
            foreach ($params as $paramtocast) {
                // Clean the parameter from any white space.
                $paramtocast = trim($paramtocast);
                $castingcode .= "    $paramtocast = json_decode(json_encode($paramtocast), true);\n";
            }
            $paramsstr = implode(', ', $params);
        }

        $descriptionmethod = $function->methodname . '_returns()';
        $callforreturnvaluedesc = $function->classname . '::' . $descriptionmethod;

        $methodbody = <<<EOD
$castingcode
    if ($callforreturnvaluedesc == null) {
        $function->classname::$function->methodname($paramsstr);
        return null;
    }
    return external_api::clean_returnvalue($callforreturnvaluedesc, $function->classname::$function->methodname($paramsstr));
EOD;
        return $methodbody;
    }
}

/**
 * Early WS exception handler.
 * It handles exceptions during setup and returns the Exception text in the WS format.
 * If a raise function is found nothing is returned. Throws Exception otherwise.
 *
 * @param  Exception $ex Raised exception.
 * @throws Exception
 */
function early_ws_exception_handler(Exception $ex): void {
    if (function_exists('raise_early_ws_exception')) {
        raise_early_ws_exception($ex);
        die;
    }

    throw $ex;
}
