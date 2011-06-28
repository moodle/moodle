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
 * These functions are required very early in the Moodle
 * setup process, before any of the main libraries are
 * loaded.
 *
 * @package    core
 * @subpackage lib
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/// Debug levels ///
/** no warnings at all */
define('DEBUG_NONE', 0);
/** E_ERROR | E_PARSE */
define('DEBUG_MINIMAL', 5);
/** E_ERROR | E_PARSE | E_WARNING | E_NOTICE */
define('DEBUG_NORMAL', 15);
/** E_ALL without E_STRICT for now, do show recoverable fatal errors */
define('DEBUG_ALL', 6143);
/** DEBUG_ALL with extra Moodle debug messages - (DEBUG_ALL | 32768) */
define('DEBUG_DEVELOPER', 38911);

/** Remove any memory limits */
define('MEMORY_UNLIMITED', -1);
/** Standard memory limit for given platform */
define('MEMORY_STANDARD', -2);
/**
 * Large memory limit for given platform - used in cron, upgrade, and other places that need a lot of memory.
 * Can be overridden with $CFG->extramemorylimit setting.
 */
define('MEMORY_EXTRA', -3);
/** Extremely large memory limit - not recommended for standard scripts */
define('MEMORY_HUGE', -4);

/**
 * Software maturity levels used by the core and plugins
 */
define('MATURITY_ALPHA',    50);    // internals can be tested using white box techniques
define('MATURITY_BETA',     100);   // feature complete, ready for preview and testing
define('MATURITY_RC',       150);   // tested, will be released unless there are fatal bugs
define('MATURITY_STABLE',   200);   // ready for production deployment

/**
 * Simple class. It is usually used instead of stdClass because it looks
 * more familiar to Java developers ;-) Do not use for type checking of
 * function parameters. Please use stdClass instead.
 *
 * @package    core
 * @subpackage lib
 * @copyright  2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated since 2.0
 */
class object extends stdClass {};

/**
 * Base Moodle Exception class
 *
 * Although this class is defined here, you cannot throw a moodle_exception until
 * after moodlelib.php has been included (which will happen very soon).
 *
 * @package    core
 * @subpackage lib
 * @copyright  2008 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_exception extends Exception {
    public $errorcode;
    public $module;
    public $a;
    public $link;
    public $debuginfo;

    /**
     * Constructor
     * @param string $errorcode The name of the string from error.php to print
     * @param string $module name of module
     * @param string $link The url where the user will be prompted to continue. If no url is provided the user will be directed to the site index page.
     * @param object $a Extra words and phrases that might be required in the error string
     * @param string $debuginfo optional debugging information
     */
    function __construct($errorcode, $module='', $link='', $a=NULL, $debuginfo=null) {
        if (empty($module) || $module == 'moodle' || $module == 'core') {
            $module = 'error';
        }

        $this->errorcode = $errorcode;
        $this->module    = $module;
        $this->link      = $link;
        $this->a         = $a;
        $this->debuginfo = $debuginfo;

        if (get_string_manager()->string_exists($errorcode, $module)) {
            $message = get_string($errorcode, $module, $a);
        } else {
            $message = $module . '/' . $errorcode;
        }

        parent::__construct($message, 0);
    }
}

/**
 * Course/activity access exception.
 *
 * This exception is thrown from require_login()
 *
 * @package    core
 * @subpackage lib
 * @copyright  2010 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class require_login_exception extends moodle_exception {
    function __construct($debuginfo) {
        parent::__construct('requireloginerror', 'error', '', NULL, $debuginfo);
    }
}

/**
 * Web service parameter exception class
 *
 * This exception must be thrown to the web service client when a web service parameter is invalid
 * The error string is gotten from webservice.php
 */
class webservice_parameter_exception extends moodle_exception {
    /**
     * Constructor
     * @param string $errorcode The name of the string from webservice.php to print
     * @param string $a The name of the parameter
     */
    function __construct($errorcode=null, $a = '') {
        parent::__construct($errorcode, 'webservice', '', $a, null);
    }
}

/**
 * Exceptions indicating user does not have permissions to do something
 * and the execution can not continue.
 *
 * @package    core
 * @subpackage lib
 * @copyright  2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class required_capability_exception extends moodle_exception {
    function __construct($context, $capability, $errormessage, $stringfile) {
        $capabilityname = get_capability_string($capability);
        if ($context->contextlevel == CONTEXT_MODULE and preg_match('/:view$/', $capability)) {
            // we can not go to mod/xx/view.php because we most probably do not have cap to view it, let's go to course instead
            $paranetcontext = get_context_instance_by_id(get_parent_contextid($context));
            $link = get_context_url($paranetcontext);
        } else {
            $link = get_context_url($context);
        }
        parent::__construct($errormessage, $stringfile, $link, $capabilityname);
    }
}

/**
 * Exception indicating programming error, must be fixed by a programer. For example
 * a core API might throw this type of exception if a plugin calls it incorrectly.
 *
 * @package    core
 * @subpackage lib
 * @copyright  2008 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class coding_exception extends moodle_exception {
    /**
     * Constructor
     * @param string $hint short description of problem
     * @param string $debuginfo detailed information how to fix problem
     */
    function __construct($hint, $debuginfo=null) {
        parent::__construct('codingerror', 'debug', '', $hint, $debuginfo);
    }
}

/**
 * Exception indicating malformed parameter problem.
 * This exception is not supposed to be thrown when processing
 * user submitted data in forms. It is more suitable
 * for WS and other low level stuff.
 *
 * @package    core
 * @subpackage lib
 * @copyright  2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class invalid_parameter_exception extends moodle_exception {
    /**
     * Constructor
     * @param string $debuginfo some detailed information
     */
    function __construct($debuginfo=null) {
        parent::__construct('invalidparameter', 'debug', '', null, $debuginfo);
    }
}

/**
 * Exception indicating malformed response problem.
 * This exception is not supposed to be thrown when processing
 * user submitted data in forms. It is more suitable
 * for WS and other low level stuff.
 */
class invalid_response_exception extends moodle_exception {
    /**
     * Constructor
     * @param string $debuginfo some detailed information
     */
    function __construct($debuginfo=null) {
        parent::__construct('invalidresponse', 'debug', '', null, $debuginfo);
    }
}

/**
 * An exception that indicates something really weird happened. For example,
 * if you do switch ($context->contextlevel), and have one case for each
 * CONTEXT_... constant. You might throw an invalid_state_exception in the
 * default case, to just in case something really weird is going on, and
 * $context->contextlevel is invalid - rather than ignoring this possibility.
 *
 * @package    core
 * @subpackage lib
 * @copyright  2009 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class invalid_state_exception extends moodle_exception {
    /**
     * Constructor
     * @param string $hint short description of problem
     * @param string $debuginfo optional more detailed information
     */
    function __construct($hint, $debuginfo=null) {
        parent::__construct('invalidstatedetected', 'debug', '', $hint, $debuginfo);
    }
}

/**
 * An exception that indicates incorrect permissions in $CFG->dataroot
 *
 * @package    core
 * @subpackage lib
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class invalid_dataroot_permissions extends moodle_exception {
    /**
     * Constructor
     * @param string $debuginfo optional more detailed information
     */
    function __construct($debuginfo = NULL) {
        parent::__construct('invaliddatarootpermissions', 'error', '', NULL, $debuginfo);
    }
}

/**
 * An exception that indicates that file can not be served
 *
 * @package    core
 * @subpackage lib
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class file_serving_exception extends moodle_exception {
    /**
     * Constructor
     * @param string $debuginfo optional more detailed information
     */
    function __construct($debuginfo = NULL) {
        parent::__construct('cannotservefile', 'error', '', NULL, $debuginfo);
    }
}

/**
 * Default exception handler, uncaught exceptions are equivalent to error() in 1.9 and earlier
 *
 * @param Exception $ex
 * @return void -does not return. Terminates execution!
 */
function default_exception_handler($ex) {
    global $CFG, $DB, $OUTPUT, $USER, $FULLME, $SESSION;

    // detect active db transactions, rollback and log as error
    abort_all_db_transactions();

    if (($ex instanceof required_capability_exception) && !CLI_SCRIPT && !AJAX_SCRIPT && !empty($CFG->autologinguests) && !empty($USER->autologinguest)) {
        $SESSION->wantsurl = $FULLME;
        redirect(get_login_url());
    }

    $info = get_exception_info($ex);

    if (debugging('', DEBUG_MINIMAL)) {
        $logerrmsg = "Default exception handler: ".$info->message.' Debug: '.$info->debuginfo."\n".format_backtrace($info->backtrace, true);
        error_log($logerrmsg);
    }

    if (is_early_init($info->backtrace)) {
        echo bootstrap_renderer::early_error($info->message, $info->moreinfourl, $info->link, $info->backtrace, $info->debuginfo);
    } else {
        try {
            if ($DB) {
                // If you enable db debugging and exception is thrown, the print footer prints a lot of rubbish
                $DB->set_debug(0);
            }
            echo $OUTPUT->fatal_error($info->message, $info->moreinfourl, $info->link, $info->backtrace, $info->debuginfo);
        } catch (Exception $out_ex) {
            // default exception handler MUST not throw any exceptions!!
            // the problem here is we do not know if page already started or not, we only know that somebody messed up in outputlib or theme
            // so we just print at least something instead of "Exception thrown without a stack frame in Unknown on line 0":-(
            if (CLI_SCRIPT or AJAX_SCRIPT) {
                // just ignore the error and send something back using the safest method
                echo bootstrap_renderer::early_error($info->message, $info->moreinfourl, $info->link, $info->backtrace, $info->debuginfo);
            } else {
                echo bootstrap_renderer::early_error_content($info->message, $info->moreinfourl, $info->link, $info->backtrace, $info->debuginfo);
                $outinfo = get_exception_info($out_ex);
                echo bootstrap_renderer::early_error_content($outinfo->message, $outinfo->moreinfourl, $outinfo->link, $outinfo->backtrace, $outinfo->debuginfo);
            }
        }
    }

    exit(1); // General error code
}

/**
 * Default error handler, prevents some white screens.
 * @param int $errno
 * @param string $errstr
 * @param string $errfile
 * @param int $errline
 * @param array $errcontext
 * @return bool false means use default error handler
 */
function default_error_handler($errno, $errstr, $errfile, $errline, $errcontext) {
    if ($errno == 4096) {
        //fatal catchable error
        throw new coding_exception('PHP catchable fatal error', $errstr);
    }
    return false;
}

/**
 * Unconditionally abort all database transactions, this function
 * should be called from exception handlers only.
 * @return void
 */
function abort_all_db_transactions() {
    global $CFG, $DB, $SCRIPT;

    // default exception handler MUST not throw any exceptions!!

    if ($DB && $DB->is_transaction_started()) {
        error_log('Database transaction aborted automatically in ' . $CFG->dirroot . $SCRIPT);
        // note: transaction blocks should never change current $_SESSION
        $DB->force_transaction_rollback();
    }
}

/**
 * This function encapsulates the tests for whether an exception was thrown in
 * early init -- either during setup.php or during init of $OUTPUT.
 *
 * If another exception is thrown then, and if we do not take special measures,
 * we would just get a very cryptic message "Exception thrown without a stack
 * frame in Unknown on line 0". That makes debugging very hard, so we do take
 * special measures in default_exception_handler, with the help of this function.
 *
 * @param array $backtrace the stack trace to analyse.
 * @return boolean whether the stack trace is somewhere in output initialisation.
 */
function is_early_init($backtrace) {
    $dangerouscode = array(
        array('function' => 'header', 'type' => '->'),
        array('class' => 'bootstrap_renderer'),
        array('file' => dirname(__FILE__).'/setup.php'),
    );
    foreach ($backtrace as $stackframe) {
        foreach ($dangerouscode as $pattern) {
            $matches = true;
            foreach ($pattern as $property => $value) {
                if (!isset($stackframe[$property]) || $stackframe[$property] != $value) {
                    $matches = false;
                }
            }
            if ($matches) {
                return true;
            }
        }
    }
    return false;
}

/**
 * Abort execution by throwing of a general exception,
 * default exception handler displays the error message in most cases.
 *
 * @param string $errorcode The name of the language string containing the error message.
 *      Normally this should be in the error.php lang file.
 * @param string $module The language file to get the error message from.
 * @param string $link The url where the user will be prompted to continue.
 *      If no url is provided the user will be directed to the site index page.
 * @param object $a Extra words and phrases that might be required in the error string
 * @param string $debuginfo optional debugging information
 * @return void, always throws exception!
 */
function print_error($errorcode, $module = 'error', $link = '', $a = null, $debuginfo = null) {
    throw new moodle_exception($errorcode, $module, $link, $a, $debuginfo);
}

/**
 * Returns detailed information about specified exception.
 * @param exception $ex
 * @return object
 */
function get_exception_info($ex) {
    global $CFG, $DB, $SESSION;

    if ($ex instanceof moodle_exception) {
        $errorcode = $ex->errorcode;
        $module = $ex->module;
        $a = $ex->a;
        $link = $ex->link;
        $debuginfo = $ex->debuginfo;
    } else {
        $errorcode = 'generalexceptionmessage';
        $module = 'error';
        $a = $ex->getMessage();
        $link = '';
        $debuginfo = null;
    }

    $backtrace = $ex->getTrace();
    $place = array('file'=>$ex->getFile(), 'line'=>$ex->getLine(), 'exception'=>get_class($ex));
    array_unshift($backtrace, $place);

    // Be careful, no guarantee moodlelib.php is loaded.
    if (empty($module) || $module == 'moodle' || $module == 'core') {
        $module = 'error';
    }
    if (function_exists('get_string_manager')) {
        if (get_string_manager()->string_exists($errorcode, $module)) {
            $message = get_string($errorcode, $module, $a);
        } elseif ($module == 'error' && get_string_manager()->string_exists($errorcode, 'moodle')) {
            // Search in moodle file if error specified - needed for backwards compatibility
            $message = get_string($errorcode, 'moodle', $a);
        } else {
            $message = $module . '/' . $errorcode;
        }
    } else {
        $message = $module . '/' . $errorcode;
    }

    // Be careful, no guarantee weblib.php is loaded.
    if (function_exists('clean_text')) {
        $message = clean_text($message);
    } else {
        $message = htmlspecialchars($message);
    }

    if (!empty($CFG->errordocroot)) {
        $errordoclink = $CFG->errordocroot . '/en/';
    } else {
        $errordoclink = get_docs_url();
    }

    if ($module === 'error') {
        $modulelink = 'moodle';
    } else {
        $modulelink = $module;
    }
    $moreinfourl = $errordoclink . 'error/' . $modulelink . '/' . $errorcode;

    if (empty($link)) {
        if (!empty($SESSION->fromurl)) {
            $link = $SESSION->fromurl;
            unset($SESSION->fromurl);
        } else {
            $link = $CFG->wwwroot .'/';
        }
    }

    $info = new stdClass();
    $info->message     = $message;
    $info->errorcode   = $errorcode;
    $info->backtrace   = $backtrace;
    $info->link        = $link;
    $info->moreinfourl = $moreinfourl;
    $info->a           = $a;
    $info->debuginfo   = $debuginfo;

    return $info;
}

/**
 * Returns the Moodle Docs URL in the users language
 *
 * @global object
 * @param string $path the end of the URL.
 * @return string The MoodleDocs URL in the user's language. for example {@link http://docs.moodle.org/en/ http://docs.moodle.org/en/$path}
 */
function get_docs_url($path=null) {
    global $CFG;
    // Check that $CFG->release has been set up, during installation it won't be.
    if (empty($CFG->release)) {
        // It's not there yet so look at version.php
        include($CFG->dirroot.'/version.php');
    } else {
        // We can use $CFG->release and avoid having to include version.php
        $release = $CFG->release;
    }
    // Attempt to match the branch from the release
    if (preg_match('/^(.)\.(.)/', $release, $matches)) {
        // We should ALWAYS get here
        $branch = $matches[1].$matches[2];
    } else {
        // We should never get here but in case we do lets set $branch to .
        // the smart one's will know that this is the current directory
        // and the smarter ones will know that there is some smart matching
        // that will ensure people end up at the latest version of the docs.
        $branch = '.';
    }
    if (!empty($CFG->docroot)) {
        return $CFG->docroot . '/' . $branch . '/' . current_language() . '/' . $path;
    } else {
        return 'http://docs.moodle.org/'. $branch . '/en/' . $path;
    }
}

/**
 * Formats a backtrace ready for output.
 *
 * @param array $callers backtrace array, as returned by debug_backtrace().
 * @param boolean $plaintext if false, generates HTML, if true generates plain text.
 * @return string formatted backtrace, ready for output.
 */
function format_backtrace($callers, $plaintext = false) {
    // do not use $CFG->dirroot because it might not be available in destructors
    $dirroot = dirname(dirname(__FILE__));

    if (empty($callers)) {
        return '';
    }

    $from = $plaintext ? '' : '<ul style="text-align: left">';
    foreach ($callers as $caller) {
        if (!isset($caller['line'])) {
            $caller['line'] = '?'; // probably call_user_func()
        }
        if (!isset($caller['file'])) {
            $caller['file'] = 'unknownfile'; // probably call_user_func()
        }
        $from .= $plaintext ? '* ' : '<li>';
        $from .= 'line ' . $caller['line'] . ' of ' . str_replace($dirroot, '', $caller['file']);
        if (isset($caller['function'])) {
            $from .= ': call to ';
            if (isset($caller['class'])) {
                $from .= $caller['class'] . $caller['type'];
            }
            $from .= $caller['function'] . '()';
        } else if (isset($caller['exception'])) {
            $from .= ': '.$caller['exception'].' thrown';
        }
        $from .= $plaintext ? "\n" : '</li>';
    }
    $from .= $plaintext ? '' : '</ul>';

    return $from;
}

/**
 * This function makes the return value of ini_get consistent if you are
 * setting server directives through the .htaccess file in apache.
 *
 * Current behavior for value set from php.ini On = 1, Off = [blank]
 * Current behavior for value set from .htaccess On = On, Off = Off
 * Contributed by jdell @ unr.edu
 *
 * @param string $ini_get_arg The argument to get
 * @return bool True for on false for not
 */
function ini_get_bool($ini_get_arg) {
    $temp = ini_get($ini_get_arg);

    if ($temp == '1' or strtolower($temp) == 'on') {
        return true;
    }
    return false;
}

/**
 * This function verifies the sanity of PHP configuration
 * and stops execution if anything critical found.
 */
function setup_validate_php_configuration() {
   // this must be very fast - no slow checks here!!!

   if (ini_get_bool('register_globals')) {
       print_error('globalswarning', 'admin');
   }
   if (ini_get_bool('session.auto_start')) {
       print_error('sessionautostartwarning', 'admin');
   }
   if (ini_get_bool('magic_quotes_runtime')) {
       print_error('fatalmagicquotesruntime', 'admin');
   }
}

/**
 * Initialise global $CFG variable
 * @return void
 */
function initialise_cfg() {
    global $CFG, $DB;

    try {
        if ($DB) {
            $localcfg = $DB->get_records_menu('config', array(), '', 'name,value');
            foreach ($localcfg as $name=>$value) {
                if (property_exists($CFG, $name)) {
                    // config.php settings always take precedence
                    continue;
                }
                $CFG->{$name} = $value;
            }
        }
    } catch (dml_read_exception $e) {
        // most probably empty db, going to install soon
    }
}

/**
 * Initialises $FULLME and friends. Private function. Should only be called from
 * setup.php.
 */
function initialise_fullme() {
    global $CFG, $FULLME, $ME, $SCRIPT, $FULLSCRIPT;

    // Detect common config error.
    if (substr($CFG->wwwroot, -1) == '/') {
        print_error('wwwrootslash', 'error');
    }

    if (CLI_SCRIPT) {
        initialise_fullme_cli();
        return;
    }

    $wwwroot = parse_url($CFG->wwwroot);
    if (!isset($wwwroot['path'])) {
        $wwwroot['path'] = '';
    }
    $wwwroot['path'] .= '/';

    $rurl = setup_get_remote_url();

    // Check that URL is under $CFG->wwwroot.
    if (strpos($rurl['path'], $wwwroot['path']) === 0) {
        $SCRIPT = substr($rurl['path'], strlen($wwwroot['path'])-1);
    } else {
        // Probably some weird external script
        $SCRIPT = $FULLSCRIPT = $FULLME = $ME = null;
        return;
    }

    // $CFG->sslproxy specifies if external SSL appliance is used
    // (That is, the Moodle server uses http, with an external box translating everything to https).
    if (empty($CFG->sslproxy)) {
        if ($rurl['scheme'] === 'http' and $wwwroot['scheme'] === 'https') {
            print_error('sslonlyaccess', 'error');
        }
    } else {
        if ($wwwroot['scheme'] !== 'https') {
            throw new coding_exception('Must use https address in wwwroot when ssl proxy enabled!');
        }
        $rurl['scheme'] = 'https'; // make moodle believe it runs on https, squid or something else it doing it
    }

    // $CFG->reverseproxy specifies if reverse proxy server used.
    // Used in load balancing scenarios.
    // Do not abuse this to try to solve lan/wan access problems!!!!!
    if (empty($CFG->reverseproxy)) {
        if (($rurl['host'] != $wwwroot['host']) or
                (!empty($wwwroot['port']) and $rurl['port'] != $wwwroot['port'])) {
            // Explain the problem and redirect them to the right URL
            if (!defined('NO_MOODLE_COOKIES')) {
                define('NO_MOODLE_COOKIES', true);
            }
            redirect($CFG->wwwroot, get_string('wwwrootmismatch', 'error', $CFG->wwwroot), 3);
        }
    }

    // hopefully this will stop all those "clever" admins trying to set up moodle
    // with two different addresses in intranet and Internet
    if (!empty($CFG->reverseproxy) && $rurl['host'] == $wwwroot['host']) {
        print_error('reverseproxyabused', 'error');
    }

    $hostandport = $rurl['scheme'] . '://' . $wwwroot['host'];
    if (!empty($wwwroot['port'])) {
        $hostandport .= ':'.$wwwroot['port'];
    }

    $FULLSCRIPT = $hostandport . $rurl['path'];
    $FULLME = $hostandport . $rurl['fullpath'];
    $ME = $rurl['fullpath'];
}

/**
 * Initialises $FULLME and friends for command line scripts.
 * This is a private method for use by initialise_fullme.
 */
function initialise_fullme_cli() {
    global $CFG, $FULLME, $ME, $SCRIPT, $FULLSCRIPT;

    // Urls do not make much sense in CLI scripts
    $backtrace = debug_backtrace();
    $topfile = array_pop($backtrace);
    $topfile = realpath($topfile['file']);
    $dirroot = realpath($CFG->dirroot);

    if (strpos($topfile, $dirroot) !== 0) {
        // Probably some weird external script
        $SCRIPT = $FULLSCRIPT = $FULLME = $ME = null;
    } else {
        $relativefile = substr($topfile, strlen($dirroot));
        $relativefile = str_replace('\\', '/', $relativefile); // Win fix
        $SCRIPT = $FULLSCRIPT = $relativefile;
        $FULLME = $ME = null;
    }
}

/**
 * Get the URL that PHP/the web server thinks it is serving. Private function
 * used by initialise_fullme. In your code, use $PAGE->url, $SCRIPT, etc.
 * @return array in the same format that parse_url returns, with the addition of
 *      a 'fullpath' element, which includes any slasharguments path.
 */
function setup_get_remote_url() {
    $rurl = array();
    list($rurl['host']) = explode(':', $_SERVER['HTTP_HOST']);
    $rurl['port'] = $_SERVER['SERVER_PORT'];
    $rurl['path'] = $_SERVER['SCRIPT_NAME']; // Script path without slash arguments
    $rurl['scheme'] = (empty($_SERVER['HTTPS']) or $_SERVER['HTTPS'] === 'off' or $_SERVER['HTTPS'] === 'Off' or $_SERVER['HTTPS'] === 'OFF') ? 'http' : 'https';

    if (stripos($_SERVER['SERVER_SOFTWARE'], 'apache') !== false) {
        //Apache server
        $rurl['fullpath'] = $_SERVER['REQUEST_URI'];

    } else if (stripos($_SERVER['SERVER_SOFTWARE'], 'iis') !== false) {
        //IIS - needs a lot of tweaking to make it work
        $rurl['fullpath'] = $_SERVER['SCRIPT_NAME'];

        // NOTE: ignore PATH_INFO because it is incorrectly encoded using 8bit filesystem legacy encoding in IIS
        //       since 2.0 we rely on iis rewrite extenssion like Helicon ISAPI_rewrite
        //       example rule: RewriteRule ^([^\?]+?\.php)(\/.+)$ $1\?file=$2 [QSA]

        if ($_SERVER['QUERY_STRING'] != '') {
            $rurl['fullpath'] .= '?'.$_SERVER['QUERY_STRING'];
        }
        $_SERVER['REQUEST_URI'] = $rurl['fullpath']; // extra IIS compatibility

/* NOTE: following servers are not fully tested! */

    } else if (stripos($_SERVER['SERVER_SOFTWARE'], 'lighttpd') !== false) {
        //lighttpd - not officially supported
        $rurl['fullpath'] = $_SERVER['REQUEST_URI']; // TODO: verify this is always properly encoded

    } else if (stripos($_SERVER['SERVER_SOFTWARE'], 'nginx') !== false) {
        //nginx - not officially supported
        if (!isset($_SERVER['SCRIPT_NAME'])) {
            die('Invalid server configuration detected, please try to add "fastcgi_param SCRIPT_NAME $fastcgi_script_name;" to the nginx server configuration.');
        }
        $rurl['fullpath'] = $_SERVER['REQUEST_URI']; // TODO: verify this is always properly encoded

     } else if (stripos($_SERVER['SERVER_SOFTWARE'], 'cherokee') !== false) {
         //cherokee - not officially supported
         $rurl['fullpath'] = $_SERVER['REQUEST_URI']; // TODO: verify this is always properly encoded

     } else if (stripos($_SERVER['SERVER_SOFTWARE'], 'zeus') !== false) {
         //zeus - not officially supported
         $rurl['fullpath'] = $_SERVER['REQUEST_URI']; // TODO: verify this is always properly encoded

    } else if (stripos($_SERVER['SERVER_SOFTWARE'], 'LiteSpeed') !== false) {
        //LiteSpeed - not officially supported
        $rurl['fullpath'] = $_SERVER['REQUEST_URI']; // TODO: verify this is always properly encoded

    } else if ($_SERVER['SERVER_SOFTWARE'] === 'HTTPD') {
        //obscure name found on some servers - this is definitely not supported
        $rurl['fullpath'] = $_SERVER['REQUEST_URI']; // TODO: verify this is always properly encoded

     } else {
        throw new moodle_exception('unsupportedwebserver', 'error', '', $_SERVER['SERVER_SOFTWARE']);
    }

    // sanitize the url a bit more, the encoding style may be different in vars above
    $rurl['fullpath'] = str_replace('"', '%22', $rurl['fullpath']);
    $rurl['fullpath'] = str_replace('\'', '%27', $rurl['fullpath']);

    return $rurl;
}

/**
 * Initializes our performance info early.
 *
 * Pairs up with get_performance_info() which is actually
 * in moodlelib.php. This function is here so that we can
 * call it before all the libs are pulled in.
 *
 * @uses $PERF
 */
function init_performance_info() {

    global $PERF, $CFG, $USER;

    $PERF = new stdClass();
    $PERF->logwrites = 0;
    if (function_exists('microtime')) {
        $PERF->starttime = microtime();
    }
    if (function_exists('memory_get_usage')) {
        $PERF->startmemory = memory_get_usage();
    }
    if (function_exists('posix_times')) {
        $PERF->startposixtimes = posix_times();
    }
    if (function_exists('apd_set_pprof_trace')) {
        // APD profiling
        if ($USER->id > 0 && $CFG->perfdebug >= 15) {
            $tempdir = $CFG->dataroot . '/temp/profile/' . $USER->id;
            mkdir($tempdir);
            apd_set_pprof_trace($tempdir);
            $PERF->profiling = true;
        }
    }
}

/**
 * Indicates whether we are in the middle of the initial Moodle install.
 *
 * Very occasionally it is necessary avoid running certain bits of code before the
 * Moodle installation has completed. The installed flag is set in admin/index.php
 * after Moodle core and all the plugins have been installed, but just before
 * the person doing the initial install is asked to choose the admin password.
 *
 * @return boolean true if the initial install is not complete.
 */
function during_initial_install() {
    global $CFG;
    return empty($CFG->rolesactive);
}

/**
 * Function to raise the memory limit to a new value.
 * Will respect the memory limit if it is higher, thus allowing
 * settings in php.ini, apache conf or command line switches
 * to override it.
 *
 * The memory limit should be expressed with a constant
 * MEMORY_STANDARD, MEMORY_EXTRA or MEMORY_HUGE.
 * It is possible to use strings or integers too (eg:'128M').
 *
 * @param mixed $newlimit the new memory limit
 * @return bool success
 */
function raise_memory_limit($newlimit) {
    global $CFG;

    if ($newlimit == MEMORY_UNLIMITED) {
        ini_set('memory_limit', -1);
        return true;

    } else if ($newlimit == MEMORY_STANDARD) {
        if (PHP_INT_SIZE > 4) {
            $newlimit = get_real_size('128M'); // 64bit needs more memory
        } else {
            $newlimit = get_real_size('96M');
        }

    } else if ($newlimit == MEMORY_EXTRA) {
        if (PHP_INT_SIZE > 4) {
            $newlimit = get_real_size('384M'); // 64bit needs more memory
        } else {
            $newlimit = get_real_size('256M');
        }
        if (!empty($CFG->extramemorylimit)) {
            $extra = get_real_size($CFG->extramemorylimit);
            if ($extra > $newlimit) {
                $newlimit = $extra;
            }
        }

    } else if ($newlimit == MEMORY_HUGE) {
        $newlimit = get_real_size('2G');

    } else {
        $newlimit = get_real_size($newlimit);
    }

    if ($newlimit <= 0) {
        debugging('Invalid memory limit specified.');
        return false;
    }

    $cur = ini_get('memory_limit');
    if (empty($cur)) {
        // if php is compiled without --enable-memory-limits
        // apparently memory_limit is set to ''
        $cur = 0;
    } else {
        if ($cur == -1){
            return true; // unlimited mem!
        }
        $cur = get_real_size($cur);
    }

    if ($newlimit > $cur) {
        ini_set('memory_limit', $newlimit);
        return true;
    }
    return false;
}

/**
 * Function to reduce the memory limit to a new value.
 * Will respect the memory limit if it is lower, thus allowing
 * settings in php.ini, apache conf or command line switches
 * to override it
 *
 * The memory limit should be expressed with a string (eg:'64M')
 *
 * @param string $newlimit the new memory limit
 * @return bool
 */
function reduce_memory_limit($newlimit) {
    if (empty($newlimit)) {
        return false;
    }
    $cur = ini_get('memory_limit');
    if (empty($cur)) {
        // if php is compiled without --enable-memory-limits
        // apparently memory_limit is set to ''
        $cur = 0;
    } else {
        if ($cur == -1){
            return true; // unlimited mem!
        }
        $cur = get_real_size($cur);
    }

    $new = get_real_size($newlimit);
    // -1 is smaller, but it means unlimited
    if ($new < $cur && $new != -1) {
        ini_set('memory_limit', $newlimit);
        return true;
    }
    return false;
}

/**
 * Converts numbers like 10M into bytes.
 *
 * @param string $size The size to be converted
 * @return int
 */
function get_real_size($size = 0) {
    if (!$size) {
        return 0;
    }
    $scan = array();
    $scan['GB'] = 1073741824;
    $scan['Gb'] = 1073741824;
    $scan['G'] = 1073741824;
    $scan['MB'] = 1048576;
    $scan['Mb'] = 1048576;
    $scan['M'] = 1048576;
    $scan['m'] = 1048576;
    $scan['KB'] = 1024;
    $scan['Kb'] = 1024;
    $scan['K'] = 1024;
    $scan['k'] = 1024;

    while (list($key) = each($scan)) {
        if ((strlen($size)>strlen($key))&&(substr($size, strlen($size) - strlen($key))==$key)) {
            $size = substr($size, 0, strlen($size) - strlen($key)) * $scan[$key];
            break;
        }
    }
    return $size;
}

/**
 * Try to disable all output buffering and purge
 * all headers.
 *
 * @private to be called only from lib/setup.php !
 * @return void
 */
function disable_output_buffering() {
    $olddebug = error_reporting(0);

    // disable compression, it would prevent closing of buffers
    if (ini_get_bool('zlib.output_compression')) {
        ini_set('zlib.output_compression', 'Off');
    }

    // try to flush everything all the time
    ob_implicit_flush(true);

    // close all buffers if possible and discard any existing output
    // this can actually work around some whitespace problems in config.php
    while(ob_get_level()) {
        if (!ob_end_clean()) {
            // prevent infinite loop when buffer can not be closed
            break;
        }
    }

    error_reporting($olddebug);
}

/**
 * Check whether a major upgrade is needed. That is defined as an upgrade that
 * changes something really fundamental in the database, so nothing can possibly
 * work until the database has been updated, and that is defined by the hard-coded
 * version number in this function.
 */
function redirect_if_major_upgrade_required() {
    global $CFG;
    $lastmajordbchanges = 2010111700;
    if (empty($CFG->version) or (int)$CFG->version < $lastmajordbchanges or
            during_initial_install() or !empty($CFG->adminsetuppending)) {
        try {
            @session_get_instance()->terminate_current();
        } catch (Exception $e) {
            // Ignore any errors, redirect to upgrade anyway.
        }
        $url = $CFG->wwwroot . '/' . $CFG->admin . '/index.php';
        @header($_SERVER['SERVER_PROTOCOL'] . ' 303 See Other');
        @header('Location: ' . $url);
        echo bootstrap_renderer::plain_redirect_message(htmlspecialchars($url));
        exit;
    }
}

/**
 * Function to check if a directory exists and by default create it if not exists.
 *
 * Previously this was accepting paths only from dataroot, but we now allow
 * files outside of dataroot if you supply custom paths for some settings in config.php.
 * This function does not verify that the directory is writable.
 *
 * @param string $dir absolute directory path
 * @param boolean $create directory if does not exist
 * @param boolean $recursive create directory recursively
 * @return boolean true if directory exists or created, false otherwise
 */
function check_dir_exists($dir, $create = true, $recursive = true) {
    global $CFG;

    umask(0000); // just in case some evil code changed it

    if (is_dir($dir)) {
        return true;
    }

    if (!$create) {
        return false;
    }

    return mkdir($dir, $CFG->directorypermissions, $recursive);
}

/**
 * Create a directory in dataroot and make sure it is writable.
 *
 * @param string $directory  a string of directory names under $CFG->dataroot eg  temp/something
 * @param bool $exceptiononerror throw exception if error encountered
 * @return string|false Returns full path to directory if successful, false if not; may throw exception
 */
function make_upload_directory($directory, $exceptiononerror = true) {
    global $CFG;

    // Make sure a .htaccess file is here, JUST IN CASE the files area is in the open and .htaccess is supported
    if (!file_exists("$CFG->dataroot/.htaccess")) {
        if ($handle = fopen("$CFG->dataroot/.htaccess", 'w')) {   // For safety
            @fwrite($handle, "deny from all\r\nAllowOverride None\r\nNote: this file is broken intentionally, we do not want anybody to undo it in subdirectory!\r\n");
            @fclose($handle);
        }
    }

    $dir = "$CFG->dataroot/$directory";

    if (file_exists($dir) and !is_dir($dir)) {
        if ($exceptiononerror) {
            throw new coding_exception($dir.' directory can not be created, file with the same name already exists.');
        } else {
            return false;
        }
    }

    umask(0000); // just in case some evil code changed it

    if (!file_exists($dir)) {
        if (!mkdir($dir, $CFG->directorypermissions, true)) {
            if ($exceptiononerror) {
                throw new invalid_dataroot_permissions($dir.' can not be created, check permissions.');
            } else {
                return false;
            }
        }
    }

    if (!is_writable($dir)) {
        if ($exceptiononerror) {
            throw new invalid_dataroot_permissions($dir.' is not writable, check permissions.');
        } else {
            return false;
        }
    }

    return $dir;
}

function init_memcached() {
    global $CFG, $MCACHE;

    include_once($CFG->libdir . '/memcached.class.php');
    $MCACHE = new memcached;
    if ($MCACHE->status()) {
        return true;
    }
    unset($MCACHE);
    return false;
}

function init_eaccelerator() {
    global $CFG, $MCACHE;

    include_once($CFG->libdir . '/eaccelerator.class.php');
    $MCACHE = new eaccelerator;
    if ($MCACHE->status()) {
        return true;
    }
    unset($MCACHE);
    return false;
}

/**
 * Checks if current user is a web crawler.
 *
 * This list can not be made complete, this is not a security
 * restriction, we make the list only to help these sites
 * especially when automatic guest login is disabled.
 *
 * If admin needs security they should enable forcelogin
 * and disable guest access!!
 *
 * @return bool
 */
function is_web_crawler() {
    if (!empty($_SERVER['HTTP_USER_AGENT'])) {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'Googlebot') !== false ) {
            return true;
        } else if (strpos($_SERVER['HTTP_USER_AGENT'], 'google.com') !== false ) { // Google
            return true;
        } else if (strpos($_SERVER['HTTP_USER_AGENT'], 'Yahoo! Slurp') !== false ) {  // Yahoo
            return true;
        } else if (strpos($_SERVER['HTTP_USER_AGENT'], '[ZSEBOT]') !== false ) {  // Zoomspider
            return true;
        } else if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSNBOT') !== false ) {  // MSN Search
            return true;
        } else if (strpos($_SERVER['HTTP_USER_AGENT'], 'Yandex') !== false ) {
            return true;
        } else if (strpos($_SERVER['HTTP_USER_AGENT'], 'AltaVista') !== false ) {
            return true;
        }
    }
    return false;
}

/**
 * This class solves the problem of how to initialise $OUTPUT.
 *
 * The problem is caused be two factors
 * <ol>
 * <li>On the one hand, we cannot be sure when output will start. In particular,
 * an error, which needs to be displayed, could be thrown at any time.</li>
 * <li>On the other hand, we cannot be sure when we will have all the information
 * necessary to correctly initialise $OUTPUT. $OUTPUT depends on the theme, which
 * (potentially) depends on the current course, course categories, and logged in user.
 * It also depends on whether the current page requires HTTPS.</li>
 * </ol>
 *
 * So, it is hard to find a single natural place during Moodle script execution,
 * which we can guarantee is the right time to initialise $OUTPUT. Instead we
 * adopt the following strategy
 * <ol>
 * <li>We will initialise $OUTPUT the first time it is used.</li>
 * <li>If, after $OUTPUT has been initialised, the script tries to change something
 * that $OUTPUT depends on, we throw an exception making it clear that the script
 * did something wrong.
 * </ol>
 *
 * The only problem with that is, how do we initialise $OUTPUT on first use if,
 * it is going to be used like $OUTPUT->somthing(...)? Well that is where this
 * class comes in. Initially, we set up $OUTPUT = new bootstrap_renderer(). Then,
 * when any method is called on that object, we initialise $OUTPUT, and pass the call on.
 *
 * Note that this class is used before lib/outputlib.php has been loaded, so we
 * must be careful referring to classes/functions from there, they may not be
 * defined yet, and we must avoid fatal errors.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class bootstrap_renderer {
    /**
     * Handles re-entrancy. Without this, errors or debugging output that occur
     * during the initialisation of $OUTPUT, cause infinite recursion.
     * @var boolean
     */
    protected $initialising = false;

    /**
     * Have we started output yet?
     * @return boolean true if the header has been printed.
     */
    public function has_started() {
        return false;
    }

    public function __call($method, $arguments) {
        global $OUTPUT, $PAGE;

        $recursing = false;
        if ($method == 'notification') {
            // Catch infinite recursion caused by debugging output during print_header.
            $backtrace = debug_backtrace();
            array_shift($backtrace);
            array_shift($backtrace);
            $recursing = is_early_init($backtrace);
        }

        $earlymethods = array(
            'fatal_error' => 'early_error',
            'notification' => 'early_notification',
        );

        // If lib/outputlib.php has been loaded, call it.
        if (!empty($PAGE) && !$recursing) {
            if (array_key_exists($method, $earlymethods)) {
                //prevent PAGE->context warnings - exceptions might appear before we set any context
                $PAGE->set_context(null);
            }
            $PAGE->initialise_theme_and_output();
            return call_user_func_array(array($OUTPUT, $method), $arguments);
        }

        $this->initialising = true;

        // Too soon to initialise $OUTPUT, provide a couple of key methods.
        if (array_key_exists($method, $earlymethods)) {
            return call_user_func_array(array('bootstrap_renderer', $earlymethods[$method]), $arguments);
        }

        throw new coding_exception('Attempt to start output before enough information is known to initialise the theme.');
    }

    /**
     * Returns nicely formatted error message in a div box.
     * @return string
     */
    public static function early_error_content($message, $moreinfourl, $link, $backtrace, $debuginfo = null) {
        global $CFG;

        $content = '<div style="margin-top: 6em; margin-left:auto; margin-right:auto; color:#990000; text-align:center; font-size:large; border-width:1px;
border-color:black; background-color:#ffffee; border-style:solid; border-radius: 20px; border-collapse: collapse;
width: 80%; -moz-border-radius: 20px; padding: 15px">
' . $message . '
</div>';
        if (!empty($CFG->debug) && $CFG->debug >= DEBUG_DEVELOPER) {
            if (!empty($debuginfo)) {
                $debuginfo = s($debuginfo); // removes all nasty JS
                $debuginfo = str_replace("\n", '<br />', $debuginfo); // keep newlines
                $content .= '<div class="notifytiny">Debug info: ' . $debuginfo . '</div>';
            }
            if (!empty($backtrace)) {
                $content .= '<div class="notifytiny">Stack trace: ' . format_backtrace($backtrace, false) . '</div>';
            }
        }

        return $content;
    }

    /**
     * This function should only be called by this class, or from exception handlers
     * @return string
     */
    public static function early_error($message, $moreinfourl, $link, $backtrace, $debuginfo = null) {
        global $CFG;

        if (CLI_SCRIPT) {
            echo "!!! $message !!!\n";
            if (!empty($CFG->debug) and $CFG->debug >= DEBUG_DEVELOPER) {
                if (!empty($debuginfo)) {
                    echo "\nDebug info: $debuginfo";
                }
                if (!empty($backtrace)) {
                    echo "\nStack trace: " . format_backtrace($backtrace, true);
                }
            }
            return;

        } else if (AJAX_SCRIPT) {
            $e = new stdClass();
            $e->error      = $message;
            $e->stacktrace = NULL;
            $e->debuginfo  = NULL;
            if (!empty($CFG->debug) and $CFG->debug >= DEBUG_DEVELOPER) {
                if (!empty($debuginfo)) {
                    $e->debuginfo = $debuginfo;
                }
                if (!empty($backtrace)) {
                    $e->stacktrace = format_backtrace($backtrace, true);
                }
            }
            @header('Content-Type: application/json; charset=utf-8');
            echo json_encode($e);
            return;
        }

        // In the name of protocol correctness, monitoring and performance
        // profiling, set the appropriate error headers for machine consumption
        if (isset($_SERVER['SERVER_PROTOCOL'])) {
            // Avoid it with cron.php. Note that we assume it's HTTP/1.x
            // The 503 ode here means our Moodle does not work at all, the error happened too early
            @header($_SERVER['SERVER_PROTOCOL'] . ' 503 Service Unavailable');
        }

        // better disable any caching
        @header('Content-Type: text/html; charset=utf-8');
        @header('Cache-Control: no-store, no-cache, must-revalidate');
        @header('Cache-Control: post-check=0, pre-check=0', false);
        @header('Pragma: no-cache');
        @header('Expires: Mon, 20 Aug 1969 09:23:00 GMT');
        @header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

        if (function_exists('get_string')) {
            $strerror = get_string('error');
        } else {
            $strerror = 'Error';
        }

        $content = self::early_error_content($message, $moreinfourl, $link, $backtrace, $debuginfo);

        return self::plain_page($strerror, $content);
    }

    public static function early_notification($message, $classes = 'notifyproblem') {
        return '<div class="' . $classes . '">' . $message . '</div>';
    }

    public static function plain_redirect_message($encodedurl) {
        $message = '<p>' . get_string('pageshouldredirect') . '</p><p><a href="'.
                $encodedurl .'">'. get_string('continue') .'</a></p>';
        return self::plain_page(get_string('redirect'), $message);
    }

    protected static function plain_page($title, $content) {
        if (function_exists('get_string') && function_exists('get_html_lang')) {
            $htmllang = get_html_lang();
        } else {
            $htmllang = '';
        }

        return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" ' . $htmllang . '>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>' . $title . '</title>
</head><body>' . $content . '</body></html>';
    }
}
