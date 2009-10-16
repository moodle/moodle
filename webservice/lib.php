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

function webservice_protocol_is_enabled($protocol) {
    global $CFG;

    if (empty($CFG->enablewebservices)) {
        return false;
    }

    $active = explode(',', $CFG->webserviceprotocols);

    return(in_array($protocol, $active));
}

/**
 * Mandatory web service server interface
 * @author Petr Skoda (skodak)
 */
interface webservice_server {
    /**
     * Process request from client.
     * @param bool $simple use simple authentication
     * @return void
     */
    public function run($simple);
}

/**
 * Special abstraction of our srvices that allows
 * interaction with stock Zend ws servers.
 * @author skodak
 */
abstract class webservice_zend_server implements webservice_server {

    /** @property string name of the zend server class */
    protected $zend_class;

    /** @property object Zend server instance */
    protected $zend_server;

    /** @property string $wsname name of the web server plugin */
    protected $wsname = null;

    /** @property bool $simple true if simple auth used */
    protected $simple;

    /** @property string $service_class virtual web service class with all functions user name execute, created on the fly */
    protected $service_class;

    /** @property object restricted context */
    protected $restricted_context;

    /**
     * Contructor
     */
    public function __construct($zend_class) {
        $this->zend_class = $zend_class;
    }

    /**
     * Process request from client.
     * @param bool $simple use simple authentication
     * @return void
     */
    public function run($simple) {
        $this->simple = $simple;

        // we will probably need a lot of memory in some functions
        @raise_memory_limit('128M');

        // set some longer timeout, this script is not sending any output,
        // this means we need to manually extend the timeout operations
        // that need longer time to finish
        external_api::set_timeout();

        // set up exception handler first, we want to sent them back in correct format that
        // the other system understands
        // we do not need to call the original default handler because this ws handler does everything
        set_exception_handler(array($this, 'exception_handler'));

        // now create the instance of zend server
        $this->init_zend_server();

        // this sets up $USER and $SESSION and context restrictions
        $this->authenticate_user();

        // make a list of all functions user is allowed to excecute
        $this->init_service_class();

        // start the server
        $this->zend_server->setClass($this->service_class);
        $response = $this->zend_server->handle();

        // session cleanup
        $this->session_cleanup();

        //TODO: we need to send some headers too I guess
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
        if ($this->simple) {
            // now make sure the function is listed in at least one service user is allowed to use
            // allow access only if:
            //  1/ entry in the external_services_users table if required
            //  2/ validuntil not reached
            //  3/ has capability if specified in service desc
            //  4/ iprestriction

            $sql = "SELECT s.*, NULL AS iprestriction
                      FROM {external_services} s
                      JOIN {external_services_functions} sf ON (sf.externalserviceid = s.id AND s.restrictedusers = 0)
                     WHERE s.enabled = 1

                     UNION

                    SELECT s.*, su.iprestriction
                      FROM {external_services} s
                      JOIN {external_services_functions} sf ON (sf.externalserviceid = s.id AND s.restrictedusers = 1)
                      JOIN {external_services_users} su ON (su.externalserviceid = s.id AND su.userid = :userid)
                     WHERE s.enabled = 1 AND su.validuntil IS NULL OR su.validuntil < :now";
            $params = array('userid'=>$USER->id, 'now'=>time());
        } else {

            //TODO: token may restrict access to one service only
            die('not implemented yet');
        }

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
        if ($serviceids) {
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

        // now make the virtual WS class with all the fuctions for this particular user
        $methods = '';
        foreach ($functions as $function) {
            $methods .= $this->get_virtual_method_code($function);
        }

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
echo "<xmp>".$code."</xmp>";
        $this->service_class = $classname;
    }

    /**
     * returns virtual method code
     * @param object $function
     * @return string PHP code
     */
    protected function get_virtual_method_code($function) {
        global $CFG;

        //first find and include the ext implementation class
        $function->classpath = empty($function->classpath) ? get_component_directory($function->component).'/externallib.php' : $CFG->dirroot.'/'.$function->classpath;
        if (!file_exists($function->classpath)) {
            throw new coding_exception('Can not find file with external function implementation');
        }
        require_once($function->classpath);

        $function->parameters_method = $function->methodname.'_parameters';
        $function->returns_method    = $function->methodname.'_returns';

        // make sure the implementaion class is ok
        if (!method_exists($function->classname, $function->methodname)) {
            throw new coding_exception('Missing implementation method');
        }
        if (!method_exists($function->classname, $function->parameters_method)) {
            throw new coding_exception('Missing parameters description');
        }
        if (!method_exists($function->classname, $function->returns_method)) {
            throw new coding_exception('Missing returned values description');
        }

        // fetch the parameters description
        $function->parameters_desc = call_user_func(array($function->classname, $function->parameters_method));
        if (!($function->parameters_desc instanceof external_function_parameters)) {
            throw new coding_exception('Invalid parameters description');
        }

        // fetch the return values description
        $function->returns_desc = call_user_func(array($function->classname, $function->returns_method));
        // null means void result or result is ignored
        if (!is_null($function->returns_desc) and !($function->returns_desc instanceof external_description)) {
            throw new coding_exception('Invalid return description');
        }

        $params      = array();
        $params_desc = array();
        foreach ($function->parameters_desc->keys as $name=>$keydesc) {
            $params[]      = '$'.$name;
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
                $type = 'struct';
            } else if ($keydesc instanceof external_multiple_structure) {
                $type = 'array';
            }
            $params_desc[] = '     * @param '.$type.' $'.$name.' '.$keydesc->desc;
        }
        $params      = implode(', ', $params);
        $params_desc = implode("\n", $params_desc);

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
                $type = 'struct';
            } else if ($function->returns_desc instanceof external_multiple_structure) {
                $type = 'array';
            }
            $return = '     * @return '.$type.' '.$function->returns_desc->desc;
        }
        
        // now crate a virtual method that calls the ext implemenation
        // TODO: add PHP docs and all missing info here

        $code = '
    /**
     * External function: '.$function->name.'
     * TODO: add function description
'.$params_desc.'
'.$return.'
     */
    public function '.$function->name.'('.$params.') {
        return '.$function->classname.'::'.$function->methodname.'('.$params.');
    }
';
        return $code;
    }

    /**
     * Set up zend serice class
     * @return void
     */
    protected function init_zend_server() {
        include "Zend/Loader.php";
        Zend_Loader::registerAutoload();
        //TODO: set up some server options and debugging too - maybe a new method
        //TODO: add some zend exeption handler too
        $this->zend_server = new $this->zend_class();
    }

    /**
     * Send the error information to the WS client.
     * @param exception $ex
     * @return void
     */
    protected function send_error($ex=null) {
        var_dump($ex);
        die('TODO');
        // TODO: find some way to send the error back through the Zend
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

        if ($this->simple) {
            $this->restricted_context = get_context_instance(CONTEXT_SYSTEM);

            if (!is_enabled_auth('webservice')) {
                die('WS auth not enabled');
            }

            if (!$auth = get_auth_plugin('webservice')) {
                die('WS auth missing');
            }

            // the username is hardcoded as URL parameter because we can not easily parse the request data :-(
            if (!$username = optional_param('wsusername', '', PARAM_RAW)) {
                throw new invalid_parameter_exception('Missing username');
            }

            // the password is hardcoded as URL parameter because we can not easily parse the request data :-(
            if (!$password = optional_param('wspassword', '', PARAM_RAW)) {
                throw new invalid_parameter_exception('Missing password');
            }

            if (!$auth->user_login_webservice($username, $password)) {
                throw new invalid_parameter_exception('Wrong username or password');
            }

            $user = $DB->get_record('user', array('username'=>$username, 'mnethostid'=>$CFG->mnet_localhost_id, 'deleted'=>0), '*', MUST_EXIST);

            // now fake user login, the session is completely empty too
            session_set_user($user);

        } else {

            //TODO: not implemented yet
            die('token login not implemented yet');
            //TODO: $this->restricted_context is derived from the token context
        }

        if (!has_capability("webservice/$this->wsname:use", $this->restricted_context)) {
            throw new invalid_parameter_exception('Access to web service not allowed');
        }

        external_api::set_context_restriction($this->restricted_context);
    }

    /**
     * Specialised exception handler, we can not use the standard one because
     * it can not just print html to output.
     *
     * @param exception $ex
     * @return void does not return
     */
    public function exception_handler($ex) {
        global $CFG, $DB, $SCRIPT;

        // detect active db transactions, rollback and log as error
        if ($DB->is_transaction_started()) {
            error_log('Database transaction aborted by exception in ' . $CFG->dirroot . $SCRIPT);
            try {
                // note: transaction blocks should never change current $_SESSION
                $DB->rollback_sql();
            } catch (Exception $ignored) {
            }
        }

        // now let the plugin send the exception to client
        $this->send_error($ex);

        // some hacks might need a cleanup hook
        $this->session_cleanup($ex);

        // not much else we can do now, add some logging later
        exit(1);
    }

    /**
     * Future hook needed for emulated sessions.
     * @param exception $exception null means normal termination, $exception received when WS call failed
     * @return void
     */
    protected function session_cleanup($exception=null) {
        if ($this->simple) {
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
abstract class webservice_base_server implements webservice_server {

    /** @property string $wsname name of the web server plugin */
    protected $wsname = null;

    /** @property bool $simple true if simple auth used */
    protected $simple;

    /** @property string $username name of local user */
    protected $username = null;

    /** @property string $password password of the local user */
    protected $password = null;

    /** @property string $token authentication token*/
    protected $token = null;

    /** @property object restricted context */
    protected $restricted_context;

    /** @property array $parameters the function parameters - the real values submitted in the request */
    protected $parameters = null;

    /** @property string $functionname the name of the function that is executed */
    protected $functionname = null;

    /** @property object $function full function description */
    protected $function = null;

    /** @property mixed $returns function return value */
    protected $returns = null;

    /**
     * Contructor
     */
    public function __construct() {
    }

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
     * @param bool $simple use simple authentication
     * @return void
     */
    public function run($simple) {
        $this->simple = $simple;

        // we will probably need a lot of memory in some functions
        @raise_memory_limit('128M');

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
        global $CFG, $DB, $SCRIPT;

        // detect active db transactions, rollback and log as error
        if ($DB->is_transaction_started()) {
            error_log('Database transaction aborted by exception in ' . $CFG->dirroot . $SCRIPT);
            try {
                // note: transaction blocks should never change current $_SESSION
                $DB->rollback_sql();
            } catch (Exception $ignored) {
            }
        }

        // now let the plugin send the exception to client
        $this->send_error($ex);

        // some hacks might need a cleanup hook
        $this->session_cleanup($ex);

        // not much else we can do now, add some logging later
        exit(1);
    }

    /**
     * Future hook needed for emulated sessions.
     * @param exception $exception null means normal termination, $exception received when WS call failed
     * @return void
     */
    protected function session_cleanup($exception=null) {
        if ($this->simple) {
            // nothing needs to be done, there is no persistent session
        } else {
            // close emulated session if used
        }
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

        if ($this->simple) {
            $this->restricted_context = get_context_instance(CONTEXT_SYSTEM);

            if (!is_enabled_auth('webservice')) {
                die('WS auth not enabled');
            }

            if (!$auth = get_auth_plugin('webservice')) {
                die('WS auth missing');
            }

            if (!$this->username) {
                throw new invalid_parameter_exception('Missing username');
            }

            if (!$this->password) {
                throw new invalid_parameter_exception('Missing password');
            }

            if (!$auth->user_login_webservice($this->username, $this->password)) {
                throw new invalid_parameter_exception('Wrong username or password');
            }

            $user = $DB->get_record('user', array('username'=>$this->username, 'mnethostid'=>$CFG->mnet_localhost_id, 'deleted'=>0), '*', MUST_EXIST);

            // now fake user login, the session is completely empty too
            session_set_user($user);
        } else {

            //TODO: not implemented yet
            die('token login not implemented yet');
            //TODO: $this->restricted_context is derived from the token context
        }

        if (!has_capability("webservice/$this->wsname:use", $this->restricted_context)) {
            throw new invalid_parameter_exception('Access to web service not allowed');
        }

        external_api::set_context_restriction($this->restricted_context);
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
        $function = $DB->get_record('external_functions', array('name'=>$this->functionname), '*', MUST_EXIST);


        // now let's verify access control
        if ($this->simple) {
            // now make sure the function is listed in at least one service user is allowed to use
            // allow access only if:
            //  1/ entry in the external_services_users table if required
            //  2/ validuntil not reached
            //  3/ has capability if specified in service desc
            //  4/ iprestriction

            $sql = "SELECT s.*, NULL AS iprestriction
                      FROM {external_services} s
                      JOIN {external_services_functions} sf ON (sf.externalserviceid = s.id AND s.restrictedusers = 0 AND sf.functionname = :name1)
                     WHERE s.enabled = 1

                     UNION

                    SELECT s.*, su.iprestriction
                      FROM {external_services} s
                      JOIN {external_services_functions} sf ON (sf.externalserviceid = s.id AND s.restrictedusers = 1 AND sf.functionname = :name2)
                      JOIN {external_services_users} su ON (su.externalserviceid = s.id AND su.userid = :userid)
                     WHERE s.enabled = 1 AND su.validuntil IS NULL OR su.validuntil < :now";
            $params = array('userid'=>$USER->id, 'name1'=>$function->name, 'name2'=>$function->name, 'now'=>time());
        } else {

            //TODO: token may restrict access to one service only
            die('not implemented yet');
        }

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
            throw new invalid_parameter_exception('Access to external function not allowed');
        }
        // now we finally know the user may execute this function,
        // the last step is to set context restriction - in this simple case
        // we use system context because each external system has different user account
        // and we can manage everything through normal permissions.

        // get the params and return descriptions of the function
        unset($function->id); // we want to prevent any accidental db updates ;-)

        $function->classpath = empty($function->classpath) ? get_component_directory($function->component).'/externallib.php' : $CFG->dirroot.'/'.$function->classpath;
        if (!file_exists($function->classpath)) {
            throw new coding_exception('Can not find file with external function implementation');
        }
        require_once($function->classpath);

        $function->parameters_method = $function->methodname.'_parameters';
        $function->returns_method    = $function->methodname.'_returns';

        // make sure the implementaion class is ok
        if (!method_exists($function->classname, $function->methodname)) {
            throw new coding_exception('Missing implementation method');
        }
        if (!method_exists($function->classname, $function->parameters_method)) {
            throw new coding_exception('Missing parameters description');
        }
        if (!method_exists($function->classname, $function->returns_method)) {
            throw new coding_exception('Missing returned values description');
        }

        // fetch the parameters description
        $function->parameters_desc = call_user_func(array($function->classname, $function->parameters_method));
        if (!($function->parameters_desc instanceof external_function_parameters)) {
            throw new coding_exception('Invalid parameters description');
        }

        // fetch the return values description
        $function->returns_desc = call_user_func(array($function->classname, $function->returns_method));
        // null means void result or result is ignored
        if (!is_null($function->returns_desc) and !($function->returns_desc instanceof external_description)) {
            throw new coding_exception('Invalid return description');
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


