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
 * @package   webservice
 * @copyright 2009 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir.'/externallib.php');

define('WEBSERVICE_AUTHMETHOD_USERNAME', 0);
define('WEBSERVICE_AUTHMETHOD_PERMANENT_TOKEN', 1);
define('WEBSERVICE_AUTHMETHOD_SESSION_TOKEN', 2);

/**
 * General web service library
 */
class webservice {

    /**
     * Add a user to the list of authorised user of a given service
     * @param object $user
     */
    public function add_ws_authorised_user($user) {
        global $DB;
        $user->timecreated = mktime();
        $DB->insert_record('external_services_users', $user);
    }

    /**
     * Remove a user from a list of allowed user of a service
     * @param object $user
     * @param int $serviceid
     */
    public function remove_ws_authorised_user($user, $serviceid) {
        global $DB;
        $DB->delete_records('external_services_users',
                array('externalserviceid' => $serviceid, 'userid' => $user->id));
    }

    /**
     * Update service allowed user settings
     * @param object $user
     */
    public function update_ws_authorised_user($user) {
        global $DB;
        $DB->update_record('external_services_users', $user);
    }

    /**
     * Return list of allowed users with their options (ip/timecreated / validuntil...)
     * for a given service
     * @param int $serviceid
     * @return array $users
     */
    public function get_ws_authorised_users($serviceid) {
        global $DB, $CFG;
        $params = array($CFG->siteguest, $serviceid);
        $sql = " SELECT u.id as id, esu.id as serviceuserid, u.email as email, u.firstname as firstname,
                        u.lastname as lastname,
                        esu.iprestriction as iprestriction, esu.validuntil as validuntil,
                        esu.timecreated as timecreated
                   FROM {user} u, {external_services_users} esu
                  WHERE u.id <> ? AND u.deleted = 0 AND u.confirmed = 1
                        AND esu.userid = u.id
                        AND esu.externalserviceid = ?";

        $users = $DB->get_records_sql($sql, $params);
        return $users;
    }

    /**
     * Return a authorised user with his options (ip/timecreated / validuntil...)
     * @param int $serviceid
     * @param int $userid
     * @return object
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
     * Generate all ws token needed by a user
     * @param int $userid
     */
    public function generate_user_ws_tokens($userid) {
        global $CFG, $DB;

        /// generate a token for non admin if web service are enable and the user has the capability to create a token
        if (!is_siteadmin() && has_capability('moodle/webservice:createtoken', get_context_instance(CONTEXT_SYSTEM), $userid) && !empty($CFG->enablewebservices)) {
        /// for every service than the user is authorised on, create a token (if it doesn't already exist)

            ///get all services which are set to all user (no restricted to specific users)
            $norestrictedservices = $DB->get_records('external_services', array('restrictedusers' => 0));
            $serviceidlist = array();
            foreach ($norestrictedservices as $service) {
                $serviceidlist[] = $service->id;
            }

            //get all services which are set to the current user (the current user is specified in the restricted user list)
            $servicesusers = $DB->get_records('external_services_users', array('userid' => $userid));
            foreach ($servicesusers as $serviceuser) {
                if (!in_array($serviceuser->externalserviceid,$serviceidlist)) {
                     $serviceidlist[] = $serviceuser->externalserviceid;
                }
            }

            //get all services which already have a token set for the current user
            $usertokens = $DB->get_records('external_tokens', array('userid' => $userid, 'tokentype' => EXTERNAL_TOKEN_PERMANENT));
            $tokenizedservice = array();
            foreach ($usertokens as $token) {
                    $tokenizedservice[]  = $token->externalserviceid;
            }

            //create a token for the service which have no token already
            foreach ($serviceidlist as $serviceid) {
                if (!in_array($serviceid, $tokenizedservice)) {
                    //create the token for this service
                    $newtoken = new stdClass();
                    $newtoken->token = md5(uniqid(rand(),1));
                    //check that the user has capability on this service
                    $newtoken->tokentype = EXTERNAL_TOKEN_PERMANENT;
                    $newtoken->userid = $userid;
                    $newtoken->externalserviceid = $serviceid;
                    //TODO: find a way to get the context - UPDATE FOLLOWING LINE
                    $newtoken->contextid = get_context_instance(CONTEXT_SYSTEM)->id;
                    $newtoken->creatorid = $userid;
                    $newtoken->timecreated = time();

                    $DB->insert_record('external_tokens', $newtoken);
                }
            }


        }
    }

    /**
     * Return all ws user token with ws enabled/disabled and ws restricted users mode.
     * @param integer $userid
     * @return array of token
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
     * Return a user token that has been created by the user
     * If doesn't exist a exception is thrown
     * @param integer $userid
     * @param integer $tokenid
     * @return object token
     * ->id token id
     * ->token
     * ->firstname user firstname
     * ->lastname
     * ->name service name
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
     * Return a token for a given id
     * @param integer $tokenid
     * @return object token
     */
    public function get_token_by_id($tokenid) {
        global $DB;
        return $DB->get_record('external_tokens', array('id' => $tokenid));
    }

    /**
     * Delete a user token
     * @param int $tokenid
     */
    public function delete_user_ws_token($tokenid) {
        global $DB;
        $DB->delete_records('external_tokens', array('id'=>$tokenid));
    }

    /**
     * Delete a service - it also delete the functions and users references to this service
     * @param int $serviceid
     */
    public function delete_service($serviceid) {
        global $DB;
        $DB->delete_records('external_services_users', array('externalserviceid' => $serviceid));
        $DB->delete_records('external_services_functions', array('externalserviceid' => $serviceid));
        $DB->delete_records('external_tokens', array('externalserviceid' => $serviceid));
        $DB->delete_records('external_services', array('id' => $serviceid));
    }

    /**
     * Get a user token by token
     * @param string $token
     * @throws moodle_exception if there is multiple result
     */
    public function get_user_ws_token($token) {
        global $DB;
        return $DB->get_record('external_tokens', array('token'=>$token), '*', MUST_EXIST);
    }

    /**
     * Get the list of all functions for given service ids
     * @param array $serviceids
     * @return array functions
     */
    public function get_external_functions($serviceids) {
        global $DB;
        if (!empty($serviceids)) {
            list($serviceids, $params) = $DB->get_in_or_equal($serviceids);
            $sql = "SELECT f.*
                      FROM {external_functions} f
                     WHERE f.name IN (SELECT sf.functionname
                                        FROM {external_services_functions} sf
                                       WHERE sf.externalserviceid $serviceids)";
            $functions = $DB->get_records_sql($sql, $params);
        } else {
            $functions = array();
        }
        return $functions;
    }

    /**
     * Get the list of all functions for given service shortnames
     * @param array $serviceshortnames
     * @param $enabledonly if true then only return function for the service that has been enabled
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
     * Get the list of all functions not in the given service id
     * @param int $serviceid
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
     * @param integer $serviceid
     * @return array
     * example of return value:
     *  Array
     *  (
     *    [moodle_group_create_groups] => Array
     *    (
     *       [0] => moodle/course:managegroups
     *    )
     *
     *    [moodle_enrol_get_enrolled_users] => Array
     *    (
     *       [0] => moodle/site:viewparticipants
     *       [1] => moodle/course:viewparticipants
     *       [2] => moodle/role:review
     *       [3] => moodle/site:accessallgroups
     *       [4] => moodle/course:enrolreview
     *    )
     *  )
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
     * Only usefull for documentation purpose
     * @param integer $userid
     * @return array
     */
    public function get_user_capabilities($userid) {
        global $DB;
        //retrieve the user capabilities
        $sql = "SELECT rc.id, rc.capability FROM {role_capabilities} rc, {role_assignments} ra
            WHERE rc.roleid=ra.roleid AND ra.userid= ?";
        $dbusercaps = $DB->get_records_sql($sql, array($userid));
        $usercaps = array();
        foreach ($dbusercaps as $usercap) {
            $usercaps[$usercap->capability] = true;
        }
        return $usercaps;
    }

    /**
     * Get users missing capabilities for a given service
     * @param array $users
     * @param integer $serviceid
     * @return array of missing capabilities, the key being the user id
     */
    public function get_missing_capabilities_by_users($users, $serviceid) {
        global $DB;
        $usersmissingcaps = array();

        //retrieve capabilities required by the service
        $servicecaps = $this->get_service_required_capabilities($serviceid);

        //retrieve users missing capabilities
        foreach ($users as $user) {
            //cast user array into object to be a bit more flexible
            if (is_array($user)) {
                $user = (object) $user;
            }
            $usercaps = $this->get_user_capabilities($user->id);

            //detect the missing capabilities
            foreach ($servicecaps as $functioname => $functioncaps) {
                foreach ($functioncaps as $functioncap) {
                    if (!key_exists($functioncap, $usercaps)) {
                        if (!isset($usersmissingcaps[$user->id])
                                or array_search($functioncap, $usersmissingcaps[$user->id]) === false) {
                            $usersmissingcaps[$user->id][] = $functioncap;
                        }
                    }
                }
            }
        }

        return $usersmissingcaps;
    }

    /**
     * Get a external service for a given id
     * @param service id $serviceid
     * @param integer $strictness IGNORE_MISSING, MUST_EXIST...
     * @return object external service
     */
    public function get_external_service_by_id($serviceid, $strictness=IGNORE_MISSING) {
        global $DB;
        $service = $DB->get_record('external_services',
                        array('id' => $serviceid), '*', $strictness);
        return $service;
    }

    /**
     * Get a external service for a given shortname
     * @param service shortname $shortname
     * @param integer $strictness IGNORE_MISSING, MUST_EXIST...
     * @return object external service
     */
    public function get_external_service_by_shortname($shortname, $strictness=IGNORE_MISSING) {
        global $DB;
        $service = $DB->get_record('external_services',
                        array('shortname' => $shortname), '*', $strictness);
        return $service;
    }

    /**
     * Get a external function for a given id
     * @param function id $functionid
     * @param integer $strictness IGNORE_MISSING, MUST_EXIST...
     * @return object external function
     */
    public function get_external_function_by_id($functionid, $strictness=IGNORE_MISSING) {
        global $DB;
        $function = $DB->get_record('external_functions',
                            array('id' => $functionid), '*', $strictness);
        return $function;
    }

    /**
     * Add a function to a service
     * @param string $functionname
     * @param integer $serviceid
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
     * @param object $service
     * @return serviceid integer
     */
    public function add_external_service($service) {
        global $DB;
        $service->timecreated = mktime();
        $serviceid = $DB->insert_record('external_services', $service);
        return $serviceid;
    }

     /**
     * Update a service
     * @param object $service
     */
    public function update_external_service($service) {
        global $DB;
        $service->timemodified = mktime();
        $DB->update_record('external_services', $service);
    }

    /**
     * Test whether a external function is already linked to a service
     * @param string $functionname
     * @param integer $serviceid
     * @return bool true if a matching function exists for the service, else false.
     * @throws dml_exception if error
     */
    public function service_function_exists($functionname, $serviceid) {
        global $DB;
        return $DB->record_exists('external_services_functions',
                            array('externalserviceid' => $serviceid,
                                'functionname' => $functionname));
    }

    public function remove_external_function_from_service($functionname, $serviceid) {
        global $DB;
        $DB->delete_records('external_services_functions',
                    array('externalserviceid' => $serviceid, 'functionname' => $functionname));

    }


}

/**
 * Exception indicating access control problem in web service call
 * @author Petr Skoda (skodak)
 */
class webservice_access_exception extends moodle_exception {
    /**
     * Constructor
     */
    function __construct($debuginfo) {
        parent::__construct('accessexception', 'webservice', '', null, $debuginfo);
    }
}

/**
 * Is protocol enabled?
 * @param string $protocol name of WS protocol
 * @return bool
 */
function webservice_protocol_is_enabled($protocol) {
    global $CFG;

    if (empty($CFG->enablewebservices)) {
        return false;
    }

    $active = explode(',', $CFG->webserviceprotocols);

    return(in_array($protocol, $active));
}

//=== WS classes ===

/**
 * Mandatory interface for all test client classes.
 * @author Petr Skoda (skodak)
 */
interface webservice_test_client_interface {
    /**
     * Execute test client WS request
     * @param string $serverurl
     * @param string $function
     * @param array $params
     * @return mixed
     */
    public function simpletest($serverurl, $function, $params);
}

/**
 * Mandatory interface for all web service protocol classes
 * @author Petr Skoda (skodak)
 */
interface webservice_server_interface {
    /**
     * Process request from client.
     * @return void
     */
    public function run();
}

/**
 * Abstract web service base class.
 * @author Petr Skoda (skodak)
 */
abstract class webservice_server implements webservice_server_interface {

    /** @property string $wsname name of the web server plugin */
    protected $wsname = null;

    /** @property string $username name of local user */
    protected $username = null;

    /** @property string $password password of the local user */
    protected $password = null;

    /** @property int $userid the local user */
    protected $userid = null;

    /** @property integer $authmethod authentication method one of WEBSERVICE_AUTHMETHOD_* */
    protected $authmethod;

    /** @property string $token authentication token*/
    protected $token = null;

    /** @property object restricted context */
    protected $restricted_context;

    /** @property int restrict call to one service id*/
    protected $restricted_serviceid = null;

    /**
     * Contructor
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
     * @return void
     */
    protected function authenticate_user() {
        global $CFG, $DB;

        if (!NO_MOODLE_COOKIES) {
            throw new coding_exception('Cookies must be disabled in WS servers!');
        }

        if ($this->authmethod == WEBSERVICE_AUTHMETHOD_USERNAME) {

            //we check that authentication plugin is enabled
            //it is only required by simple authentication
            if (!is_enabled_auth('webservice')) {
                throw new webservice_access_exception(get_string('wsauthnotenabled', 'webservice'));
            }

            if (!$auth = get_auth_plugin('webservice')) {
                throw new webservice_access_exception(get_string('wsauthmissing', 'webservice'));
            }

            $this->restricted_context = get_context_instance(CONTEXT_SYSTEM);

            if (!$this->username) {
                throw new webservice_access_exception(get_string('missingusername', 'webservice'));
            }

            if (!$this->password) {
                throw new webservice_access_exception(get_string('missingpassword', 'webservice'));
            }

            if (!$auth->user_login_webservice($this->username, $this->password)) {
                // log failed login attempts
                add_to_log(SITEID, 'webservice', get_string('simpleauthlog', 'webservice'), '' , get_string('failedtolog', 'webservice').": ".$this->username."/".$this->password." - ".getremoteaddr() , 0);
                throw new webservice_access_exception(get_string('wrongusernamepassword', 'webservice'));
            }

            $user = $DB->get_record('user', array('username'=>$this->username, 'mnethostid'=>$CFG->mnet_localhost_id, 'deleted'=>0), '*', MUST_EXIST);

        } else if ($this->authmethod == WEBSERVICE_AUTHMETHOD_PERMANENT_TOKEN){
            $user = $this->authenticate_by_token(EXTERNAL_TOKEN_PERMANENT);
        } else {
            $user = $this->authenticate_by_token(EXTERNAL_TOKEN_EMBEDDED);
        }

        // now fake user login, the session is completely empty too
        session_set_user($user);
        $this->userid = $user->id;

        if ($this->authmethod != WEBSERVICE_AUTHMETHOD_SESSION_TOKEN && !has_capability("webservice/$this->wsname:use", $this->restricted_context)) {
            throw new webservice_access_exception(get_string('accessnotallowed', 'webservice'));
        }

        external_api::set_context_restriction($this->restricted_context);
    }

    protected function authenticate_by_token($tokentype){
        global $DB;
        if (!$token = $DB->get_record('external_tokens', array('token'=>$this->token, 'tokentype'=>$tokentype))) {
            // log failed login attempts
            add_to_log(SITEID, 'webservice', get_string('tokenauthlog', 'webservice'), '' , get_string('failedtolog', 'webservice').": ".$this->token. " - ".getremoteaddr() , 0);
            throw new webservice_access_exception(get_string('invalidtoken', 'webservice'));
        }

        if ($token->validuntil and $token->validuntil < time()) {
            $DB->delete_records('external_tokens', array('token'=>$this->token, 'tokentype'=>$tokentype));
            throw new webservice_access_exception(get_string('invalidtimedtoken', 'webservice'));
        }

        if ($token->sid){//assumes that if sid is set then there must be a valid associated session no matter the token type
            $session = session_get_instance();
            if (!$session->session_exists($token->sid)){
                $DB->delete_records('external_tokens', array('sid'=>$token->sid));
                throw new webservice_access_exception(get_string('invalidtokensession', 'webservice'));
            }
        }

        if ($token->iprestriction and !address_in_subnet(getremoteaddr(), $token->iprestriction)) {
            add_to_log(SITEID, 'webservice', get_string('tokenauthlog', 'webservice'), '' , get_string('failedtolog', 'webservice').": ".getremoteaddr() , 0);
            throw new webservice_access_exception(get_string('invalidiptoken', 'webservice'));
        }

        $this->restricted_context = get_context_instance_by_id($token->contextid);
        $this->restricted_serviceid = $token->externalserviceid;

        $user = $DB->get_record('user', array('id'=>$token->userid, 'deleted'=>0), '*', MUST_EXIST);

        // log token access
        $DB->set_field('external_tokens', 'lastaccess', time(), array('id'=>$token->id));

        return $user;

    }
}

/**
 * Special abstraction of our srvices that allows
 * interaction with stock Zend ws servers.
 * @author Petr Skoda (skodak)
 */
abstract class webservice_zend_server extends webservice_server {

    /** @property string name of the zend server class : Zend_XmlRpc_Server, Zend_Soap_Server, Zend_Soap_AutoDiscover, ...*/
    protected $zend_class;

    /** @property object Zend server instance */
    protected $zend_server;

    /** @property string $service_class virtual web service class with all functions user name execute, created on the fly */
    protected $service_class;

    /**
     * Contructor
     * @param integer $authmethod authentication method - one of WEBSERVICE_AUTHMETHOD_*
     */
    public function __construct($authmethod, $zend_class) {
        parent::__construct($authmethod);
        $this->zend_class = $zend_class;
    }

    /**
     * Process request from client.
     * @param bool $simple use simple authentication
     * @return void
     */
    public function run() {
        // we will probably need a lot of memory in some functions
        raise_memory_limit(MEMORY_EXTRA);

        // set some longer timeout, this script is not sending any output,
        // this means we need to manually extend the timeout operations
        // that need longer time to finish
        external_api::set_timeout();

        // now create the instance of zend server
        $this->init_zend_server();

        // set up exception handler first, we want to sent them back in correct format that
        // the other system understands
        // we do not need to call the original default handler because this ws handler does everything
        set_exception_handler(array($this, 'exception_handler'));

        // init all properties from the request data
        $this->parse_request();

        // this sets up $USER and $SESSION and context restrictions
        $this->authenticate_user();

        // make a list of all functions user is allowed to excecute
        $this->init_service_class();

        // tell server what functions are available
        $this->zend_server->setClass($this->service_class);

        //log the web service request
        add_to_log(SITEID, 'webservice', '', '' , $this->zend_class." ".getremoteaddr() , 0, $this->userid);

        //send headers
        $this->send_headers();

        // execute and return response, this sends some headers too
        $response = $this->zend_server->handle();

        // session cleanup
        $this->session_cleanup();

        //finally send the result
        echo $response;
        die;
    }

    /**
     * Load virtual class needed for Zend api
     * @return void
     */
    protected function init_service_class() {
        global $USER, $DB;

        // first ofall get a complete list of services user is allowed to access

        if ($this->restricted_serviceid) {
            $params = array('sid1'=>$this->restricted_serviceid, 'sid2'=>$this->restricted_serviceid);
            $wscond1 = 'AND s.id = :sid1';
            $wscond2 = 'AND s.id = :sid2';
        } else {
            $params = array();
            $wscond1 = '';
            $wscond2 = '';
        }

        // now make sure the function is listed in at least one service user is allowed to use
        // allow access only if:
        //  1/ entry in the external_services_users table if required
        //  2/ validuntil not reached
        //  3/ has capability if specified in service desc
        //  4/ iprestriction

        $sql = "SELECT s.*, NULL AS iprestriction
                  FROM {external_services} s
                  JOIN {external_services_functions} sf ON (sf.externalserviceid = s.id AND s.restrictedusers = 0)
                 WHERE s.enabled = 1 $wscond1

                 UNION

                SELECT s.*, su.iprestriction
                  FROM {external_services} s
                  JOIN {external_services_functions} sf ON (sf.externalserviceid = s.id AND s.restrictedusers = 1)
                  JOIN {external_services_users} su ON (su.externalserviceid = s.id AND su.userid = :userid)
                 WHERE s.enabled = 1 AND su.validuntil IS NULL OR su.validuntil < :now $wscond2";

        $params = array_merge($params, array('userid'=>$USER->id, 'now'=>time()));

        $serviceids = array();
        $rs = $DB->get_recordset_sql($sql, $params);

        // now make sure user may access at least one service
        $remoteaddr = getremoteaddr();
        $allowed = false;
        foreach ($rs as $service) {
            if (isset($serviceids[$service->id])) {
                continue;
            }
            if ($service->requiredcapability and !has_capability($service->requiredcapability, $this->restricted_context)) {
                continue; // cap required, sorry
            }
            if ($service->iprestriction and !address_in_subnet($remoteaddr, $service->iprestriction)) {
                continue; // wrong request source ip, sorry
            }
            $serviceids[$service->id] = $service->id;
        }
        $rs->close();

        // now get the list of all functions
        $wsmanager = new webservice();
        $functions = $wsmanager->get_external_functions($serviceids);

        // now make the virtual WS class with all the fuctions for this particular user
        $methods = '';
        foreach ($functions as $function) {
            $methods .= $this->get_virtual_method_code($function);
        }

        // let's use unique class name, there might be problem in unit tests
        $classname = 'webservices_virtual_class_000000';
        while(class_exists($classname)) {
            $classname++;
        }

        $code = '
/**
 * Virtual class web services for user id '.$USER->id.' in context '.$this->restricted_context->id.'.
 */
class '.$classname.' {
'.$methods.'
}
';

        // load the virtual class definition into memory
        eval($code);
        $this->service_class = $classname;
    }

    /**
     * returns virtual method code
     * @param object $function
     * @return string PHP code
     */
    protected function get_virtual_method_code($function) {
        global $CFG;

        $function = external_function_info($function);

        //arguments in function declaration line with defaults.
        $paramanddefaults      = array();
        //arguments used as parameters for external lib call.
        $params      = array();
        $params_desc = array();
        foreach ($function->parameters_desc->keys as $name=>$keydesc) {
            $param = '$'.$name;
            $paramanddefault = $param;
            //need to generate the default if there is any
            if ($keydesc instanceof external_value) {
                if ($keydesc->required == VALUE_DEFAULT) {
                    if ($keydesc->default===null) {
                        $paramanddefault .= '=null';
                    } else {
                        switch($keydesc->type) {
                            case PARAM_BOOL:
                                $paramanddefault .= '='.$keydesc->default; break;
                            case PARAM_INT:
                                $paramanddefault .= '='.$keydesc->default; break;
                            case PARAM_FLOAT;
                                $paramanddefault .= '='.$keydesc->default; break;
                            default:
                                $paramanddefault .= '=\''.$keydesc->default.'\'';
                        }
                    }
                } else if ($keydesc->required == VALUE_OPTIONAL) {
                    //it does make sens to declare a parameter VALUE_OPTIONAL
                    //VALUE_OPTIONAL is used only for array/object key
                    throw new moodle_exception('parametercannotbevalueoptional');
                }
            } else { //for the moment we do not support default for other structure types
                 if ($keydesc->required == VALUE_DEFAULT) {
                     //accept empty array as default
                     if (isset($keydesc->default) and is_array($keydesc->default)
                             and empty($keydesc->default)) {
                         $paramanddefault .= '=array()';
                     } else {
                        throw new moodle_exception('errornotemptydefaultparamarray', 'webservice', '', $name);
                     }
                 }
                 if ($keydesc->required == VALUE_OPTIONAL) {
                     throw new moodle_exception('erroroptionalparamarray', 'webservice', '', $name);
                 }
            }
            $params[] = $param;
            $paramanddefaults[] = $paramanddefault;
            $type = 'string';
            if ($keydesc instanceof external_value) {
                switch($keydesc->type) {
                    case PARAM_BOOL: // 0 or 1 only for now
                    case PARAM_INT:
                        $type = 'int'; break;
                    case PARAM_FLOAT;
                        $type = 'double'; break;
                    default:
                        $type = 'string';
                }
            } else if ($keydesc instanceof external_single_structure) {
                $type = 'object|struct';
            } else if ($keydesc instanceof external_multiple_structure) {
                $type = 'array';
            }
            $params_desc[] = '     * @param '.$type.' $'.$name.' '.$keydesc->desc;
        }
        $params                = implode(', ', $params);
        $paramanddefaults      = implode(', ', $paramanddefaults);
        $params_desc           = implode("\n", $params_desc);

        $serviceclassmethodbody = $this->service_class_method_body($function, $params);

        if (is_null($function->returns_desc)) {
            $return = '     * @return void';
        } else {
            $type = 'string';
            if ($function->returns_desc instanceof external_value) {
                switch($function->returns_desc->type) {
                    case PARAM_BOOL: // 0 or 1 only for now
                    case PARAM_INT:
                        $type = 'int'; break;
                    case PARAM_FLOAT;
                        $type = 'double'; break;
                    default:
                        $type = 'string';
                }
            } else if ($function->returns_desc instanceof external_single_structure) {
                $type = 'object|struct'; //only 'object' is supported by SOAP, 'struct' by XML-RPC MDL-23083
            } else if ($function->returns_desc instanceof external_multiple_structure) {
                $type = 'array';
            }
            $return = '     * @return '.$type.' '.$function->returns_desc->desc;
        }

        // now crate the virtual method that calls the ext implementation

        $code = '
    /**
     * '.$function->description.'
     *
'.$params_desc.'
'.$return.'
     */
    public function '.$function->name.'('.$paramanddefaults.') {
'.$serviceclassmethodbody.'
    }
';
        return $code;
    }

    /**
     * You can override this function in your child class to add extra code into the dynamically
     * created service class. For example it is used in the amf server to cast types of parameters and to
     * cast the return value to the types as specified in the return value description.
     * @param stdClass $function
     * @param array $params
     * @return string body of the method for $function ie. everything within the {} of the method declaration.
     */
    protected function service_class_method_body($function, $params){
        //cast the param from object to array (validate_parameters except array only)
        $castingcode = '';
        if ($params){
            $paramstocast = explode(',', $params);
            foreach ($paramstocast as $paramtocast) {
                //clean the parameter from any white space
                $paramtocast = trim($paramtocast);
                $castingcode .= $paramtocast .
                '=webservice_zend_server::cast_objects_to_array('.$paramtocast.');';
            }

        }

        $descriptionmethod = $function->methodname.'_returns()';
        $callforreturnvaluedesc = $function->classname.'::'.$descriptionmethod;
        return $castingcode . '    if ('.$callforreturnvaluedesc.' == null)  {'.
                        $function->classname.'::'.$function->methodname.'('.$params.');
                        return null;
                    }
                    return external_api::clean_returnvalue('.$callforreturnvaluedesc.', '.$function->classname.'::'.$function->methodname.'('.$params.'));';
    }

    /**
     * Recursive function to recurse down into a complex variable and convert all
     * objects to arrays.
     * @param mixed $param value to cast
     * @return mixed Cast value
     */
    public static function cast_objects_to_array($param){
        if (is_object($param)){
            $param = (array)$param;
        }
        if (is_array($param)){
            $toreturn = array();
            foreach ($param as $key=> $param){
                $toreturn[$key] = self::cast_objects_to_array($param);
            }
            return $toreturn;
        } else {
            return $param;
        }
    }

    /**
     * Set up zend service class
     * @return void
     */
    protected function init_zend_server() {
        $this->zend_server = new $this->zend_class();
    }

    /**
     * This method parses the $_REQUEST superglobal and looks for
     * the following information:
     *  1/ user authentication - username+password or token (wsusername, wspassword and wstoken parameters)
     *
     * @return void
     */
    protected function parse_request() {
        if ($this->authmethod == WEBSERVICE_AUTHMETHOD_USERNAME) {
            //note: some clients have problems with entity encoding :-(
            if (isset($_REQUEST['wsusername'])) {
                $this->username = $_REQUEST['wsusername'];
            }
            if (isset($_REQUEST['wspassword'])) {
                $this->password = $_REQUEST['wspassword'];
            }
        } else {
            if (isset($_REQUEST['wstoken'])) {
                $this->token = $_REQUEST['wstoken'];
            }
        }
    }

    /**
     * Internal implementation - sending of page headers.
     * @return void
     */
    protected function send_headers() {
        header('Cache-Control: private, must-revalidate, pre-check=0, post-check=0, max-age=0');
        header('Expires: '. gmdate('D, d M Y H:i:s', 0) .' GMT');
        header('Pragma: no-cache');
        header('Accept-Ranges: none');
    }

    /**
     * Specialised exception handler, we can not use the standard one because
     * it can not just print html to output.
     *
     * @param exception $ex
     * @return void does not return
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
     * Send the error information to the WS client
     * formatted as XML document.
     * @param exception $ex
     * @return void
     */
    protected function send_error($ex=null) {
        $this->send_headers();
        echo $this->zend_server->fault($ex);
    }

    /**
     * Future hook needed for emulated sessions.
     * @param exception $exception null means normal termination, $exception received when WS call failed
     * @return void
     */
    protected function session_cleanup($exception=null) {
        if ($this->authmethod == WEBSERVICE_AUTHMETHOD_USERNAME) {
            // nothing needs to be done, there is no persistent session
        } else {
            // close emulated session if used
        }
    }

}

/**
 * Web Service server base class, this class handles both
 * simple and token authentication.
 * @author Petr Skoda (skodak)
 */
abstract class webservice_base_server extends webservice_server {

    /** @property array $parameters the function parameters - the real values submitted in the request */
    protected $parameters = null;

    /** @property string $functionname the name of the function that is executed */
    protected $functionname = null;

    /** @property object $function full function description */
    protected $function = null;

    /** @property mixed $returns function return value */
    protected $returns = null;

    /**
     * This method parses the request input, it needs to get:
     *  1/ user authentication - username+password or token
     *  2/ function name
     *  3/ function parameters
     *
     * @return void
     */
    abstract protected function parse_request();

    /**
     * Send the result of function call to the WS client.
     * @return void
     */
    abstract protected function send_response();

    /**
     * Send the error information to the WS client.
     * @param exception $ex
     * @return void
     */
    abstract protected function send_error($ex=null);

    /**
     * Process request from client.
     * @return void
     */
    public function run() {
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

        //log the web service request
        add_to_log(SITEID, 'webservice', $this->functionname, '' , getremoteaddr() , 0, $this->userid);

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
     * @return void does not return
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
     * @param exception $exception null means normal termination, $exception received when WS call failed
     * @return void
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
     * @return void
     */
    protected function load_function_info() {
        global $DB, $USER, $CFG;

        if (empty($this->functionname)) {
            throw new invalid_parameter_exception('Missing function name');
        }

        // function must exist
        $function = external_function_info($this->functionname);

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
                 WHERE s.enabled = 1 AND su.validuntil IS NULL OR su.validuntil < :now $wscond2";
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
            throw new webservice_access_exception('Access to external function not allowed');
        }

        // we have all we need now
        $this->function = $function;
    }

    /**
     * Execute previously loaded function using parameters parsed from the request data.
     * @return void
     */
    protected function execute() {
        // validate params, this also sorts the params properly, we need the correct order in the next part
        $params = call_user_func(array($this->function->classname, 'validate_parameters'), $this->function->parameters_desc, $this->parameters);

        // execute - yay!
        $this->returns = call_user_func_array(array($this->function->classname, $this->function->methodname), array_values($params));
    }
}


