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
 * Sample simple REST web service server.
 *
 * This WS entry point accepts only users from the 'auth/webservice'
 * plugin. Enabling of webservice for each user is explicitly allowed
 * via records in the external_services_users table.
 *
 * @package    moodlecore
 * @subpackage file
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/*
 * NOTE: this is a proposal for a new WS server OOP framework
 *
 * I did not understand much how the WS protocols are supposed
 * to work, I just tried to create some clean abstraction that
 * should allow easy implementation of any protocol server.
 * The REST was chosen only because it seems to be the easiest
 * one with no real standardisation ;-)
 *
 * == How to test this ==
 * 1/ create new service that includes function 'moodle_group_get_groups' by adding new local plugin,
 *    this adds new record into external_services table
 * 2/ create new user and select 'webservice' as auth plugin and enable webservice auth plugin
 * 3/ add $CFG->enablesimplewebservices=true; to your config.php
 * 4/ manually enable the rest service: set_config('enable', 1, 'rest');
 * 5/ manually insert record into external_services_users - your new service + your new user
 * 6/ create course with some groups
 * 7/ execute the REST query: http://nahore.skodak.local/moodle20/webservice/rest/simpleserver.php?wsusername=a&wspassword=p&wsfunction=moodle_group_get_groups&groupids[0]=1&groupids[1]=3
 * 8/ sit tight and watch in awe the super hyper mega cool xml response of fully working REST WS server :-)
 *
 * skodak
 */


define('NO_MOODLE_COOKIES', true);

require('../../config.php');
require_once("$CFG->libdir/externallib.php");
//require_once("$CFG->dirroot/webservice/rest/lib.php"); TODO: uncomment when rewrite finished
//require_once("$CFG->dirroot/webservice/rest/locallib.php"); TODO: uncomment when rewrite finished



//======== NOTE: this should be defined in /webservice/rest/locallib.php =============================

/**
 * REST service server class.
 *
 * @author Petr Skoda (skodak)
 */
class webservice_rest_server extends webservice_base_server {
    /**
     * Contructor
     * @param bool $simple use simple authentication
     */
    public function __construct($simple) {
        parent::__construct($simple);
        $this->wsname = 'rest';
    }

    /**
     * This method parses the $_REQUEST superglobal and looks for
     * the following information:
     *  1/ user authentication - username+password or token (wsusername, wspassword and wstoken parameters)
     *  2/ function name (wsfunction parameter)
     *  3/ function parameters (all other parameters except those above)
     *
     * @return void
     */
    protected function parse_request() {
        if ($this->simple) {
            $this->username = isset($_REQUEST['wsusername']) ? $_REQUEST['wsusername'] : null;
            unset($_REQUEST['wsusername']);

            $this->password = isset($_REQUEST['wspassword']) ? $_REQUEST['wspassword'] : null;
            unset($_REQUEST['wspassword']);

            $this->functionname = isset($_REQUEST['wsfunction']) ? $_REQUEST['wsfunction'] : null;
            unset($_REQUEST['wsfunction']);

            $this->parameters = $_REQUEST;

        } else {
            //TODO
            die('not implemented yet');
        }
    }

    /**
     * Send the result of function call to the WS client
     * formatted as XML document.
     * @return void
     */
    protected function send_response() {
        $this->send_headers();
        $xml = '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
        $xml .= '<RESPONSE>'."\n";
        $xml .= self::xmlize_result($this->returns, $this->function->returns_desc);
        $xml .= '</RESPONSE>'."\n";
        echo $xml;
    }

    /**
     * Send the error information to the WS client
     * formatted as XML document.
     * @param exception $ex
     * @return void
     */
    protected function send_error($ex=null) {
        $this->send_headers();
        $xml = '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
        $xml .= '<EXCEPTION class="'.get_class($ex).'">'."\n";
        $xml .= '<MESSAGE>'.htmlentities($ex->getMessage(), ENT_COMPAT, 'UTF-8').'</MESSAGE>'."\n";
        if (debugging() and isset($ex->debuginfo)) {
            $xml .= '<DEBUGINFO>'.htmlentities($ex->debuginfo, ENT_COMPAT, 'UTF-8').'</DEBUGINFO>'."\n";
        }
        $xml .= '</EXCEPTION>'."\n";
        echo $xml;
    }

    /**
     * Internal implementation - sending of page headers.
     * @return void
     */
    protected function send_headers() {
        header('Content-Type: application/xml');
        header('Content-Disposition: inline; filename="response.xml"');
        header('Cache-Control: private, must-revalidate, pre-check=0, post-check=0, max-age=0');
        header('Expires: '. gmdate('D, d M Y H:i:s', 0) .' GMT');
        header('Pragma: no-cache');
        header('Accept-Ranges: none');
    }

    /**
     * Internal implementation - recursive function producing XML markup.
     * @param mixed $returns
     * @param $desc
     * @return unknown_type
     */
    protected static function xmlize_result($returns, $desc) {
        if ($desc === null) {
            return '';

        } else if ($desc instanceof external_value) {
            //TODO: there should be some way to indicate the real NULL value
            return '<VALUE>'.htmlentities($returns, ENT_COMPAT, 'UTF-8').'</VALUE>'."\n";

        } else if ($desc instanceof external_multiple_structure) {
            $mult = '<MULTIPLE>'."\n";
            foreach ($returns as $val) {
                $mult .= self::xmlize_result($val, $desc->content);
            }
            $mult .= '</MULTIPLE>'."\n";
            return $mult;

        } else if ($desc instanceof external_single_structure) {
            $single = '<SINGLE>'."\n";
            foreach ($desc->keys as $key=>$subdesc) {
                if (!array_key_exists($key, $returns)) {
                    if ($subdesc->rewquired) {
                        $single .= '<ERROR>Missing key</ERROR>';
                        continue;
                    } else {
                        //optional field
                        continue;
                    }
                }
                $single .= '<KEY name="'.$key.'">'.self::xmlize_result($returns[$key], $subdesc).'</KEY>'."\n";
            }
            $single .= '</SINGLE>'."\n";
            return $single;
        }
    }
}




//======== NOTE: this should be defined in /webservice/lib.php =============================

/**
 * Web Service server base class, this class handles both
 * simple and token authentication.
 */
abstract class webservice_base_server {

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
     * @param bool $simple use simple authentication
     */
    public function __construct($simple) {
        $this->simple = $simple;
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


    public function run() {
        // first make sure this service is enabled
        if (!$this->is_enabled()) {
            die();
        }

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

            /* //TODO: add web service usage capabilities
            if (!has_capability("webservice/$this->wsname:usesimple", get_context_instance(CONTEXT_SYSTEM))) {
                throw new invalid_parameter_exception('Access to web service not allowed');
            }
            */

        } else {
            //TODO: not implemented yet
            die('token login not implemented yet');

            /* //TODO: add web service usage capabilities
            // note we had to wait until here because we did not know the security context earlier
            if (!has_capability("webservice/$this->wsname:use", $context)) {
                throw new invalid_parameter_exception('Access to web service not allowed');
            }
            */
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
        $function = $DB->get_record('external_functions', array('name'=>$this->functionname), '*', MUST_EXIST);


        // now let's verify access control
        if ($this->simple) {
            // now make sure the function is listed in at least one service user is allowed to use
            // allow access only if:
            //  1/ entry in the external_services_users table - the restricted users flag is ignored in service desc
            //  2/ validuntil not reached
            //  3/ has capability if specified in service desc
            //  4/ iprestriction

            $sql = "SELECT s.*, su.iprestriction
                      FROM {external_services} s
                      JOIN {external_services_functions} sf ON (sf.externalserviceid = s.id AND sf.functionname = :name)
                      JOIN {external_services_users} su ON (su.externalserviceid = s.id AND su.userid = :userid)
                     WHERE su.validuntil IS NULL OR su.validuntil < :now";
            $rs = $DB->get_recordset_sql($sql, array('userid'=>$USER->id, 'name'=>$function->name, 'now'=>time()));
            // now make sure user may access at least one service
            $syscontext = get_context_instance(CONTEXT_SYSTEM);
            $remoteaddr = getremoteaddr();
            $allowed = false;
            foreach ($rs as $service) {
                if ($service->requiredcapability and !has_capability($service->requiredcapability, $syscontext)) {
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
            external_api::set_context_restriction($syscontext);

        } else {
            //TODO: implement token security checks
            die('not implemented yet');
        }

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

    /**
     * Returns human readable protocol name.
     * @return string
     */
    public function get_protocolname() {
        return get_string('protocolname', 'webservice_'.$this->wsname); //TODO: add to lang pack
    }

    /**
     * Returns WS plugin name (not localized)
     * @return string
     */
    public function get_name() {
        return $this->wsname;
    }

    /**
     * Is this WS server plugin enabled?
     * @return bool
     */
    public function is_enabled() {
        return get_config($this->wsname, 'enable');
    }

    /**
     * Change enabled flag
     * @param bool $enable
     * @return void
     */
    public function set_enable($enable) {
        set_config('enable', $enable, $this->wsname);
    }

    /**
     * Returns the settings form,
     * the current data and defaults are already loaded.
     * @return moodleform or null if settings not used
     */
    public function settings_form() {
        //NOTE: store the form definition in separate file, not directly in lib.php!!
        return null;
    }

    /**
     * Saves settings form data to db.
     * @param array $data
     * @return void
     */
    public function settings_save($data) {
        return;
    }
}



//======== NOTE: this is finally the code that would be in this file =============================


if (empty($CFG->enablesimplewebservices)) {
    die;
}

$server = new webservice_rest_server(true);
$server->run();
die;


