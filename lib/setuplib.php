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

// Debug levels - always keep the values in ascending order!
/** No warnings and errors at all */
define('DEBUG_NONE', 0);
/** Fatal errors only */
define('DEBUG_MINIMAL', E_ERROR | E_PARSE);
/** Errors, warnings and notices */
define('DEBUG_NORMAL', E_ERROR | E_PARSE | E_WARNING | E_NOTICE);
/** All problems except strict PHP warnings */
define('DEBUG_ALL', E_ALL & ~E_STRICT);
/** DEBUG_ALL with all debug messages and strict warnings */
define('DEBUG_DEVELOPER', E_ALL | E_STRICT);

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
class object extends stdClass {
    /**
     * Constructor.
     */
    public function __construct() {
        debugging("'object' class has been deprecated, please use stdClass instead.", DEBUG_DEVELOPER);
    }
};

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

    /**
     * @var string The name of the string from error.php to print
     */
    public $errorcode;

    /**
     * @var string The name of module
     */
    public $module;

    /**
     * @var mixed Extra words and phrases that might be required in the error string
     */
    public $a;

    /**
     * @var string The url where the user will be prompted to continue. If no url is provided the user will be directed to the site index page.
     */
    public $link;

    /**
     * @var string Optional information to aid the debugging process
     */
    public $debuginfo;

    /**
     * Constructor
     * @param string $errorcode The name of the string from error.php to print
     * @param string $module name of module
     * @param string $link The url where the user will be prompted to continue. If no url is provided the user will be directed to the site index page.
     * @param mixed $a Extra words and phrases that might be required in the error string
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
        $this->debuginfo = is_null($debuginfo) ? null : (string)$debuginfo;

        if (get_string_manager()->string_exists($errorcode, $module)) {
            $message = get_string($errorcode, $module, $a);
            $haserrorstring = true;
        } else {
            $message = $module . '/' . $errorcode;
            $haserrorstring = false;
        }

        if (defined('PHPUNIT_TEST') and PHPUNIT_TEST and $debuginfo) {
            $message = "$message ($debuginfo)";
        }

        if (!$haserrorstring and defined('PHPUNIT_TEST') and PHPUNIT_TEST) {
            // Append the contents of $a to $debuginfo so helpful information isn't lost.
            // This emulates what {@link get_exception_info()} does. Unfortunately that
            // function is not used by phpunit.
            $message .= PHP_EOL.'$a contents: '.print_r($a, true);
        }

        parent::__construct($message, 0);
    }
}

/**
 * Course/activity access exception.
 *
 * This exception is thrown from require_login()
 *
 * @package    core_access
 * @copyright  2010 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class require_login_exception extends moodle_exception {
    /**
     * Constructor
     * @param string $debuginfo Information to aid the debugging process
     */
    function __construct($debuginfo) {
        parent::__construct('requireloginerror', 'error', '', NULL, $debuginfo);
    }
}

/**
 * Session timeout exception.
 *
 * This exception is thrown from require_login()
 *
 * @package    core_access
 * @copyright  2015 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class require_login_session_timeout_exception extends require_login_exception {
    /**
     * Constructor
     */
    public function __construct() {
        moodle_exception::__construct('sessionerroruser', 'error');
    }
}

/**
 * Web service parameter exception class
 * @deprecated since Moodle 2.2 - use moodle exception instead
 * This exception must be thrown to the web service client when a web service parameter is invalid
 * The error string is gotten from webservice.php
 */
class webservice_parameter_exception extends moodle_exception {
    /**
     * Constructor
     * @param string $errorcode The name of the string from webservice.php to print
     * @param string $a The name of the parameter
     * @param string $debuginfo Optional information to aid debugging
     */
    function __construct($errorcode=null, $a = '', $debuginfo = null) {
        parent::__construct($errorcode, 'webservice', '', $a, $debuginfo);
    }
}

/**
 * Exceptions indicating user does not have permissions to do something
 * and the execution can not continue.
 *
 * @package    core_access
 * @copyright  2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class required_capability_exception extends moodle_exception {
    /**
     * Constructor
     * @param context $context The context used for the capability check
     * @param string $capability The required capability
     * @param string $errormessage The error message to show the user
     * @param string $stringfile
     */
    function __construct($context, $capability, $errormessage, $stringfile) {
        $capabilityname = get_capability_string($capability);
        if ($context->contextlevel == CONTEXT_MODULE and preg_match('/:view$/', $capability)) {
            // we can not go to mod/xx/view.php because we most probably do not have cap to view it, let's go to course instead
            $parentcontext = $context->get_parent_context();
            $link = $parentcontext->get_url();
        } else {
            $link = $context->get_url();
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
 * Default exception handler.
 *
 * @param Exception $ex
 * @return void -does not return. Terminates execution!
 */
function default_exception_handler($ex) {
    global $CFG, $DB, $OUTPUT, $USER, $FULLME, $SESSION, $PAGE;

    // detect active db transactions, rollback and log as error
    abort_all_db_transactions();

    if (($ex instanceof required_capability_exception) && !CLI_SCRIPT && !AJAX_SCRIPT && !empty($CFG->autologinguests) && !empty($USER->autologinguest)) {
        $SESSION->wantsurl = qualified_me();
        redirect(get_login_url());
    }

    $info = get_exception_info($ex);

    if (debugging('', DEBUG_MINIMAL)) {
        $logerrmsg = "Default exception handler: ".$info->message.' Debug: '.$info->debuginfo."\n".format_backtrace($info->backtrace, true);
        error_log($logerrmsg);
    }

    if (is_early_init($info->backtrace)) {
        echo bootstrap_renderer::early_error($info->message, $info->moreinfourl, $info->link, $info->backtrace, $info->debuginfo, $info->errorcode);
    } else {
        try {
            if ($DB) {
                // If you enable db debugging and exception is thrown, the print footer prints a lot of rubbish
                $DB->set_debug(0);
            }
            echo $OUTPUT->fatal_error($info->message, $info->moreinfourl, $info->link, $info->backtrace, $info->debuginfo);
        } catch (Exception $e) {
            $out_ex = $e;
        } catch (Throwable $e) {
            // Engine errors in PHP7 throw exceptions of type Throwable (this "catch" will be ignored in PHP5).
            $out_ex = $e;
        }

        if (isset($out_ex)) {
            // default exception handler MUST not throw any exceptions!!
            // the problem here is we do not know if page already started or not, we only know that somebody messed up in outputlib or theme
            // so we just print at least something instead of "Exception thrown without a stack frame in Unknown on line 0":-(
            if (CLI_SCRIPT or AJAX_SCRIPT) {
                // just ignore the error and send something back using the safest method
                echo bootstrap_renderer::early_error($info->message, $info->moreinfourl, $info->link, $info->backtrace, $info->debuginfo, $info->errorcode);
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
        $debuginfo = '';
    }

    // Append the error code to the debug info to make grepping and googling easier
    $debuginfo .= PHP_EOL."Error code: $errorcode";

    $backtrace = $ex->getTrace();
    $place = array('file'=>$ex->getFile(), 'line'=>$ex->getLine(), 'exception'=>get_class($ex));
    array_unshift($backtrace, $place);

    // Be careful, no guarantee moodlelib.php is loaded.
    if (empty($module) || $module == 'moodle' || $module == 'core') {
        $module = 'error';
    }
    // Search for the $errorcode's associated string
    // If not found, append the contents of $a to $debuginfo so helpful information isn't lost
    if (function_exists('get_string_manager')) {
        if (get_string_manager()->string_exists($errorcode, $module)) {
            $message = get_string($errorcode, $module, $a);
        } elseif ($module == 'error' && get_string_manager()->string_exists($errorcode, 'moodle')) {
            // Search in moodle file if error specified - needed for backwards compatibility
            $message = get_string($errorcode, 'moodle', $a);
        } else {
            $message = $module . '/' . $errorcode;
            $debuginfo .= PHP_EOL.'$a contents: '.print_r($a, true);
        }
    } else {
        $message = $module . '/' . $errorcode;
        $debuginfo .= PHP_EOL.'$a contents: '.print_r($a, true);
    }

    // Remove some absolute paths from message and debugging info.
    $searches = array();
    $replaces = array();
    $cfgnames = array('tempdir', 'cachedir', 'localcachedir', 'themedir', 'dataroot', 'dirroot');
    foreach ($cfgnames as $cfgname) {
        if (property_exists($CFG, $cfgname)) {
            $searches[] = $CFG->$cfgname;
            $replaces[] = "[$cfgname]";
        }
    }
    if (!empty($searches)) {
        $message   = str_replace($searches, $replaces, $message);
        $debuginfo = str_replace($searches, $replaces, $debuginfo);
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

    // When printing an error the continue button should never link offsite.
    // We cannot use clean_param() here as it is not guaranteed that it has been loaded yet.
    $httpswwwroot = str_replace('http:', 'https:', $CFG->wwwroot);
    if (stripos($link, $CFG->wwwroot) === 0) {
        // Internal HTTP, all good.
    } else if (!empty($CFG->loginhttps) && stripos($link, $httpswwwroot) === 0) {
        // Internal HTTPS, all good.
    } else {
        // External link spotted!
        $link = $CFG->wwwroot . '/';
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
 * Generate a uuid.
 *
 * Unique is hard. Very hard. Attempt to use the PECL UUID functions if available, and if not then revert to
 * constructing the uuid using mt_rand.
 *
 * It is important that this token is not solely based on time as this could lead
 * to duplicates in a clustered environment (especially on VMs due to poor time precision).
 *
 * @return string The uuid.
 */
function generate_uuid() {
    $uuid = '';

    if (function_exists("uuid_create")) {
        $context = null;
        uuid_create($context);

        uuid_make($context, UUID_MAKE_V4);
        uuid_export($context, UUID_FMT_STR, $uuid);
    } else {
        // Fallback uuid generation based on:
        // "http://www.php.net/manual/en/function.uniqid.php#94959".
        $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

            // 32 bits for "time_low".
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),

            // 16 bits for "time_mid".
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4.
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1.
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node".
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
    }
    return trim($uuid);
}

/**
 * Returns the Moodle Docs URL in the users language for a given 'More help' link.
 *
 * There are three cases:
 *
 * 1. In the normal case, $path will be a short relative path 'component/thing',
 * like 'mod/folder/view' 'group/import'. This gets turned into an link to
 * MoodleDocs in the user's language, and for the appropriate Moodle version.
 * E.g. 'group/import' may become 'http://docs.moodle.org/2x/en/group/import'.
 * The 'http://docs.moodle.org' bit comes from $CFG->docroot.
 *
 * This is the only option that should be used in standard Moodle code. The other
 * two options have been implemented because they are useful for third-party plugins.
 *
 * 2. $path may be an absolute URL, starting http:// or https://. In this case,
 * the link is used as is.
 *
 * 3. $path may start %%WWWROOT%%, in which case that is replaced by
 * $CFG->wwwroot to make the link.
 *
 * @param string $path the place to link to. See above for details.
 * @return string The MoodleDocs URL in the user's language. for example @link http://docs.moodle.org/2x/en/$path}
 */
function get_docs_url($path = null) {
    global $CFG;

    // Absolute URLs are used unmodified.
    if (substr($path, 0, 7) === 'http://' || substr($path, 0, 8) === 'https://') {
        return $path;
    }

    // Paths starting %%WWWROOT%% have that replaced by $CFG->wwwroot.
    if (substr($path, 0, 11) === '%%WWWROOT%%') {
        return $CFG->wwwroot . substr($path, 11);
    }

    // Otherwise we do the normal case, and construct a MoodleDocs URL relative to $CFG->docroot.

    // Check that $CFG->branch has been set up, during installation it won't be.
    if (empty($CFG->branch)) {
        // It's not there yet so look at version.php.
        include($CFG->dirroot.'/version.php');
    } else {
        // We can use $CFG->branch and avoid having to include version.php.
        $branch = $CFG->branch;
    }
    // ensure branch is valid.
    if (!$branch) {
        // We should never get here but in case we do lets set $branch to .
        // the smart one's will know that this is the current directory
        // and the smarter ones will know that there is some smart matching
        // that will ensure people end up at the latest version of the docs.
        $branch = '.';
    }
    if (empty($CFG->doclang)) {
        $lang = current_language();
    } else {
        $lang = $CFG->doclang;
    }
    $end = '/' . $branch . '/' . $lang . '/' . $path;
    if (empty($CFG->docroot)) {
        return 'http://docs.moodle.org'. $end;
    } else {
        return $CFG->docroot . $end ;
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

    $from = $plaintext ? '' : '<ul style="text-align: left" data-rel="backtrace">';
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

   if (ini_get_bool('session.auto_start')) {
       print_error('sessionautostartwarning', 'admin');
   }
}

/**
 * Initialise global $CFG variable.
 * @private to be used only from lib/setup.php
 */
function initialise_cfg() {
    global $CFG, $DB;

    if (!$DB) {
        // This should not happen.
        return;
    }

    try {
        $localcfg = get_config('core');
    } catch (dml_exception $e) {
        // Most probably empty db, going to install soon.
        return;
    }

    foreach ($localcfg as $name => $value) {
        // Note that get_config() keeps forced settings
        // and normalises values to string if possible.
        $CFG->{$name} = $value;
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

    $rurl = setup_get_remote_url();
    $wwwroot = parse_url($CFG->wwwroot.'/');

    if (empty($rurl['host'])) {
        // missing host in request header, probably not a real browser, let's ignore them

    } else if (!empty($CFG->reverseproxy)) {
        // $CFG->reverseproxy specifies if reverse proxy server used
        // Used in load balancing scenarios.
        // Do not abuse this to try to solve lan/wan access problems!!!!!

    } else {
        if (($rurl['host'] !== $wwwroot['host']) or
                (!empty($wwwroot['port']) and $rurl['port'] != $wwwroot['port']) or
                (strpos($rurl['path'], $wwwroot['path']) !== 0)) {

            // Explain the problem and redirect them to the right URL
            if (!defined('NO_MOODLE_COOKIES')) {
                define('NO_MOODLE_COOKIES', true);
            }
            // The login/token.php script should call the correct url/port.
            if (defined('REQUIRE_CORRECT_ACCESS') && REQUIRE_CORRECT_ACCESS) {
                $wwwrootport = empty($wwwroot['port'])?'':$wwwroot['port'];
                $calledurl = $rurl['host'];
                if (!empty($rurl['port'])) {
                    $calledurl .=  ':'. $rurl['port'];
                }
                $correcturl = $wwwroot['host'];
                if (!empty($wwwrootport)) {
                    $correcturl .=  ':'. $wwwrootport;
                }
                throw new moodle_exception('requirecorrectaccess', 'error', '', null,
                    'You called ' . $calledurl .', you should have called ' . $correcturl);
            }
            redirect($CFG->wwwroot, get_string('wwwrootmismatch', 'error', $CFG->wwwroot), 3);
        }
    }

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
        $_SERVER['HTTPS'] = 'on'; // Override $_SERVER to help external libraries with their HTTPS detection.
        $_SERVER['SERVER_PORT'] = 443; // Assume default ssl port for the proxy.
    }

    // hopefully this will stop all those "clever" admins trying to set up moodle
    // with two different addresses in intranet and Internet
    if (!empty($CFG->reverseproxy) && $rurl['host'] === $wwwroot['host']) {
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
    if (isset($_SERVER['HTTP_HOST'])) {
        list($rurl['host']) = explode(':', $_SERVER['HTTP_HOST']);
    } else {
        $rurl['host'] = null;
    }
    $rurl['port'] = $_SERVER['SERVER_PORT'];
    $rurl['path'] = $_SERVER['SCRIPT_NAME']; // Script path without slash arguments
    $rurl['scheme'] = (empty($_SERVER['HTTPS']) or $_SERVER['HTTPS'] === 'off' or $_SERVER['HTTPS'] === 'Off' or $_SERVER['HTTPS'] === 'OFF') ? 'http' : 'https';

    if (stripos($_SERVER['SERVER_SOFTWARE'], 'apache') !== false) {
        //Apache server
        $rurl['fullpath'] = $_SERVER['REQUEST_URI'];

        // Fixing a known issue with:
        // - Apache versions lesser than 2.4.11
        // - PHP deployed in Apache as PHP-FPM via mod_proxy_fcgi
        // - PHP versions lesser than 5.6.3 and 5.5.18.
        if (isset($_SERVER['PATH_INFO']) && (php_sapi_name() === 'fpm-fcgi') && isset($_SERVER['SCRIPT_NAME'])) {
            $pathinfodec = rawurldecode($_SERVER['PATH_INFO']);
            $lenneedle = strlen($pathinfodec);
            // Checks whether SCRIPT_NAME ends with PATH_INFO, URL-decoded.
            if (substr($_SERVER['SCRIPT_NAME'], -$lenneedle) === $pathinfodec) {
                // This is the "Apache 2.4.10- running PHP-FPM via mod_proxy_fcgi" fingerprint,
                // at least on CentOS 7 (Apache/2.4.6 PHP/5.4.16) and Ubuntu 14.04 (Apache/2.4.7 PHP/5.5.9)
                // => SCRIPT_NAME contains 'slash arguments' data too, which is wrongly exposed via PATH_INFO as URL-encoded.
                // Fix both $_SERVER['PATH_INFO'] and $_SERVER['SCRIPT_NAME'].
                $lenhaystack = strlen($_SERVER['SCRIPT_NAME']);
                $pos = $lenhaystack - $lenneedle;
                // Here $pos is greater than 0 but let's double check it.
                if ($pos > 0) {
                    $_SERVER['PATH_INFO'] = $pathinfodec;
                    $_SERVER['SCRIPT_NAME'] = substr($_SERVER['SCRIPT_NAME'], 0, $pos);
                }
            }
        }

    } else if (stripos($_SERVER['SERVER_SOFTWARE'], 'iis') !== false) {
        //IIS - needs a lot of tweaking to make it work
        $rurl['fullpath'] = $_SERVER['SCRIPT_NAME'];

        // NOTE: we should ignore PATH_INFO because it is incorrectly encoded using 8bit filesystem legacy encoding in IIS.
        //       Since 2.0, we rely on IIS rewrite extensions like Helicon ISAPI_rewrite
        //         example rule: RewriteRule ^([^\?]+?\.php)(\/.+)$ $1\?file=$2 [QSA]
        //       OR
        //       we rely on a proper IIS 6.0+ configuration: the 'FastCGIUtf8ServerVariables' registry key.
        if (isset($_SERVER['PATH_INFO']) and $_SERVER['PATH_INFO'] !== '') {
            // Check that PATH_INFO works == must not contain the script name.
            if (strpos($_SERVER['PATH_INFO'], $_SERVER['SCRIPT_NAME']) === false) {
                $rurl['fullpath'] .= clean_param(urldecode($_SERVER['PATH_INFO']), PARAM_PATH);
            }
        }

        if (isset($_SERVER['QUERY_STRING']) and $_SERVER['QUERY_STRING'] !== '') {
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

    } else if (strpos($_SERVER['SERVER_SOFTWARE'], 'PHP') === 0) {
        // built-in PHP Development Server
        $rurl['fullpath'] = $_SERVER['REQUEST_URI'];

    } else {
        throw new moodle_exception('unsupportedwebserver', 'error', '', $_SERVER['SERVER_SOFTWARE']);
    }

    // sanitize the url a bit more, the encoding style may be different in vars above
    $rurl['fullpath'] = str_replace('"', '%22', $rurl['fullpath']);
    $rurl['fullpath'] = str_replace('\'', '%27', $rurl['fullpath']);

    return $rurl;
}

/**
 * Try to work around the 'max_input_vars' restriction if necessary.
 */
function workaround_max_input_vars() {
    // Make sure this gets executed only once from lib/setup.php!
    static $executed = false;
    if ($executed) {
        debugging('workaround_max_input_vars() must be called only once!');
        return;
    }
    $executed = true;

    if (!isset($_SERVER["CONTENT_TYPE"]) or strpos($_SERVER["CONTENT_TYPE"], 'multipart/form-data') !== false) {
        // Not a post or 'multipart/form-data' which is not compatible with "php://input" reading.
        return;
    }

    if (!isloggedin() or isguestuser()) {
        // Only real users post huge forms.
        return;
    }

    $max = (int)ini_get('max_input_vars');

    if ($max <= 0) {
        // Most probably PHP < 5.3.9 that does not implement this limit.
        return;
    }

    if ($max >= 200000) {
        // This value should be ok for all our forms, by setting it in php.ini
        // admins may prevent any unexpected regressions caused by this hack.

        // Note there is no need to worry about DDoS caused by making this limit very high
        // because there are very many easier ways to DDoS any Moodle server.
        return;
    }

    // Worst case is advanced checkboxes which use up to two max_input_vars
    // slots for each entry in $_POST, because of sending two fields with the
    // same name. So count everything twice just in case.
    if (count($_POST, COUNT_RECURSIVE) * 2 < $max) {
        return;
    }

    // Large POST request with enctype supported by php://input.
    // Parse php://input in chunks to bypass max_input_vars limit, which also applies to parse_str().
    $str = file_get_contents("php://input");
    if ($str === false or $str === '') {
        // Some weird error.
        return;
    }

    $delim = '&';
    $fun = create_function('$p', 'return implode("'.$delim.'", $p);');
    $chunks = array_map($fun, array_chunk(explode($delim, $str), $max));

    // Clear everything from existing $_POST array, otherwise it might be included
    // twice (this affects array params primarily).
    foreach ($_POST as $key => $value) {
        unset($_POST[$key]);
        // Also clear from request array - but only the things that are in $_POST,
        // that way it will leave the things from a get request if any.
        unset($_REQUEST[$key]);
    }

    foreach ($chunks as $chunk) {
        $values = array();
        parse_str($chunk, $values);

        merge_query_params($_POST, $values);
        merge_query_params($_REQUEST, $values);
    }
}

/**
 * Merge parsed POST chunks.
 *
 * NOTE: this is not perfect, but it should work in most cases hopefully.
 *
 * @param array $target
 * @param array $values
 */
function merge_query_params(array &$target, array $values) {
    if (isset($values[0]) and isset($target[0])) {
        // This looks like a split [] array, lets verify the keys are continuous starting with 0.
        $keys1 = array_keys($values);
        $keys2 = array_keys($target);
        if ($keys1 === array_keys($keys1) and $keys2 === array_keys($keys2)) {
            foreach ($values as $v) {
                $target[] = $v;
            }
            return;
        }
    }
    foreach ($values as $k => $v) {
        if (!isset($target[$k])) {
            $target[$k] = $v;
            continue;
        }
        if (is_array($target[$k]) and is_array($v)) {
            merge_query_params($target[$k], $v);
            continue;
        }
        // We should not get here unless there are duplicates in params.
        $target[$k] = $v;
    }
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
        // MEMORY_HUGE uses 2G or MEMORY_EXTRA, whichever is bigger.
        $newlimit = get_real_size('2G');
        if (!empty($CFG->extramemorylimit)) {
            $extra = get_real_size($CFG->extramemorylimit);
            if ($extra > $newlimit) {
                $newlimit = $extra;
            }
        }

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

    static $binaryprefixes = array(
        'K' => 1024,
        'k' => 1024,
        'M' => 1048576,
        'm' => 1048576,
        'G' => 1073741824,
        'g' => 1073741824,
        'T' => 1099511627776,
        't' => 1099511627776,
    );

    if (preg_match('/^([0-9]+)([KMGT])/i', $size, $matches)) {
        return $matches[1] * $binaryprefixes[$matches[2]];
    }

    return (int) $size;
}

/**
 * Try to disable all output buffering and purge
 * all headers.
 *
 * @access private to be called only from lib/setup.php !
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

    // disable any other output handlers
    ini_set('output_handler', '');

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
    $lastmajordbchanges = 2014093001.00;
    if (empty($CFG->version) or (float)$CFG->version < $lastmajordbchanges or
            during_initial_install() or !empty($CFG->adminsetuppending)) {
        try {
            @\core\session\manager::terminate_current();
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
 * Makes sure that upgrade process is not running
 *
 * To be inserted in the core functions that can not be called by pluigns during upgrade.
 * Core upgrade should not use any API functions at all.
 * See {@link http://docs.moodle.org/dev/Upgrade_API#Upgrade_code_restrictions}
 *
 * @throws moodle_exception if executed from inside of upgrade script and $warningonly is false
 * @param bool $warningonly if true displays a warning instead of throwing an exception
 * @return bool true if executed from outside of upgrade process, false if from inside upgrade process and function is used for warning only
 */
function upgrade_ensure_not_running($warningonly = false) {
    global $CFG;
    if (!empty($CFG->upgraderunning)) {
        if (!$warningonly) {
            throw new moodle_exception('cannotexecduringupgrade');
        } else {
            debugging(get_string('cannotexecduringupgrade', 'error'), DEBUG_DEVELOPER);
            return false;
        }
    }
    return true;
}

/**
 * Function to check if a directory exists and by default create it if not exists.
 *
 * Previously this was accepting paths only from dataroot, but we now allow
 * files outside of dataroot if you supply custom paths for some settings in config.php.
 * This function does not verify that the directory is writable.
 *
 * NOTE: this function uses current file stat cache,
 *       please use clearstatcache() before this if you expect that the
 *       directories may have been removed recently from a different request.
 *
 * @param string $dir absolute directory path
 * @param boolean $create directory if does not exist
 * @param boolean $recursive create directory recursively
 * @return boolean true if directory exists or created, false otherwise
 */
function check_dir_exists($dir, $create = true, $recursive = true) {
    global $CFG;

    umask($CFG->umaskpermissions);

    if (is_dir($dir)) {
        return true;
    }

    if (!$create) {
        return false;
    }

    return mkdir($dir, $CFG->directorypermissions, $recursive);
}

/**
 * Create a new unique directory within the specified directory.
 *
 * @param string $basedir The directory to create your new unique directory within.
 * @param bool $exceptiononerror throw exception if error encountered
 * @return string The created directory
 * @throws invalid_dataroot_permissions
 */
function make_unique_writable_directory($basedir, $exceptiononerror = true) {
    if (!is_dir($basedir) || !is_writable($basedir)) {
        // The basedir is not writable. We will not be able to create the child directory.
        if ($exceptiononerror) {
            throw new invalid_dataroot_permissions($basedir . ' is not writable. Unable to create a unique directory within it.');
        } else {
            return false;
        }
    }

    do {
        // Generate a new (hopefully unique) directory name.
        $uniquedir = $basedir . DIRECTORY_SEPARATOR . generate_uuid();
    } while (
            // Ensure that basedir is still writable - if we do not check, we could get stuck in a loop here.
            is_writable($basedir) &&

            // Make the new unique directory. If the directory already exists, it will return false.
            !make_writable_directory($uniquedir, $exceptiononerror) &&

            // Ensure that the directory now exists
            file_exists($uniquedir) && is_dir($uniquedir)
        );

    // Check that the directory was correctly created.
    if (!file_exists($uniquedir) || !is_dir($uniquedir) || !is_writable($uniquedir)) {
        if ($exceptiononerror) {
            throw new invalid_dataroot_permissions('Unique directory creation failed.');
        } else {
            return false;
        }
    }

    return $uniquedir;
}

/**
 * Create a directory and make sure it is writable.
 *
 * @private
 * @param string $dir  the full path of the directory to be created
 * @param bool $exceptiononerror throw exception if error encountered
 * @return string|false Returns full path to directory if successful, false if not; may throw exception
 */
function make_writable_directory($dir, $exceptiononerror = true) {
    global $CFG;

    if (file_exists($dir) and !is_dir($dir)) {
        if ($exceptiononerror) {
            throw new coding_exception($dir.' directory can not be created, file with the same name already exists.');
        } else {
            return false;
        }
    }

    umask($CFG->umaskpermissions);

    if (!file_exists($dir)) {
        if (!@mkdir($dir, $CFG->directorypermissions, true)) {
            clearstatcache();
            // There might be a race condition when creating directory.
            if (!is_dir($dir)) {
                if ($exceptiononerror) {
                    throw new invalid_dataroot_permissions($dir.' can not be created, check permissions.');
                } else {
                    debugging('Can not create directory: '.$dir, DEBUG_DEVELOPER);
                    return false;
                }
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

/**
 * Protect a directory from web access.
 * Could be extended in the future to support other mechanisms (e.g. other webservers).
 *
 * @private
 * @param string $dir  the full path of the directory to be protected
 */
function protect_directory($dir) {
    global $CFG;
    // Make sure a .htaccess file is here, JUST IN CASE the files area is in the open and .htaccess is supported
    if (!file_exists("$dir/.htaccess")) {
        if ($handle = fopen("$dir/.htaccess", 'w')) {   // For safety
            @fwrite($handle, "deny from all\r\nAllowOverride None\r\nNote: this file is broken intentionally, we do not want anybody to undo it in subdirectory!\r\n");
            @fclose($handle);
            @chmod("$dir/.htaccess", $CFG->filepermissions);
        }
    }
}

/**
 * Create a directory under dataroot and make sure it is writable.
 * Do not use for temporary and cache files - see make_temp_directory() and make_cache_directory().
 *
 * @param string $directory  the full path of the directory to be created under $CFG->dataroot
 * @param bool $exceptiononerror throw exception if error encountered
 * @return string|false Returns full path to directory if successful, false if not; may throw exception
 */
function make_upload_directory($directory, $exceptiononerror = true) {
    global $CFG;

    if (strpos($directory, 'temp/') === 0 or $directory === 'temp') {
        debugging('Use make_temp_directory() for creation of temporary directory and $CFG->tempdir to get the location.');

    } else if (strpos($directory, 'cache/') === 0 or $directory === 'cache') {
        debugging('Use make_cache_directory() for creation of cache directory and $CFG->cachedir to get the location.');

    } else if (strpos($directory, 'localcache/') === 0 or $directory === 'localcache') {
        debugging('Use make_localcache_directory() for creation of local cache directory and $CFG->localcachedir to get the location.');
    }

    protect_directory($CFG->dataroot);
    return make_writable_directory("$CFG->dataroot/$directory", $exceptiononerror);
}

/**
 * Get a per-request storage directory in the tempdir.
 *
 * The directory is automatically cleaned up during the shutdown handler.
 *
 * @param bool $exceptiononerror throw exception if error encountered
 * @return string|false Returns full path to directory if successful, false if not; may throw exception
 */
function get_request_storage_directory($exceptiononerror = true) {
    global $CFG;

    static $requestdir = null;

    if (!$requestdir || !file_exists($requestdir) || !is_dir($requestdir) || !is_writable($requestdir)) {
        if ($CFG->localcachedir !== "$CFG->dataroot/localcache") {
            check_dir_exists($CFG->localcachedir, true, true);
            protect_directory($CFG->localcachedir);
        } else {
            protect_directory($CFG->dataroot);
        }

        if ($requestdir = make_unique_writable_directory($CFG->localcachedir, $exceptiononerror)) {
            // Register a shutdown handler to remove the directory.
            \core_shutdown_manager::register_function('remove_dir', array($requestdir));
        }
    }

    return $requestdir;
}

/**
 * Create a per-request directory and make sure it is writable.
 * This can only be used during the current request and will be tidied away
 * automatically afterwards.
 *
 * A new, unique directory is always created within the current request directory.
 *
 * @param bool $exceptiononerror throw exception if error encountered
 * @return string full path to directory if successful, false if not; may throw exception
 */
function make_request_directory($exceptiononerror = true) {
    $basedir = get_request_storage_directory($exceptiononerror);
    return make_unique_writable_directory($basedir, $exceptiononerror);
}

/**
 * Create a directory under tempdir and make sure it is writable.
 *
 * Where possible, please use make_request_directory() and limit the scope
 * of your data to the current HTTP request.
 *
 * Do not use for storing cache files - see make_cache_directory(), and
 * make_localcache_directory() instead for this purpose.
 *
 * Temporary files must be on a shared storage, and heavy usage is
 * discouraged due to the performance impact upon clustered environments.
 *
 * @param string $directory  the full path of the directory to be created under $CFG->tempdir
 * @param bool $exceptiononerror throw exception if error encountered
 * @return string|false Returns full path to directory if successful, false if not; may throw exception
 */
function make_temp_directory($directory, $exceptiononerror = true) {
    global $CFG;
    if ($CFG->tempdir !== "$CFG->dataroot/temp") {
        check_dir_exists($CFG->tempdir, true, true);
        protect_directory($CFG->tempdir);
    } else {
        protect_directory($CFG->dataroot);
    }
    return make_writable_directory("$CFG->tempdir/$directory", $exceptiononerror);
}

/**
 * Create a directory under cachedir and make sure it is writable.
 *
 * Note: this cache directory is shared by all cluster nodes.
 *
 * @param string $directory  the full path of the directory to be created under $CFG->cachedir
 * @param bool $exceptiononerror throw exception if error encountered
 * @return string|false Returns full path to directory if successful, false if not; may throw exception
 */
function make_cache_directory($directory, $exceptiononerror = true) {
    global $CFG;
    if ($CFG->cachedir !== "$CFG->dataroot/cache") {
        check_dir_exists($CFG->cachedir, true, true);
        protect_directory($CFG->cachedir);
    } else {
        protect_directory($CFG->dataroot);
    }
    return make_writable_directory("$CFG->cachedir/$directory", $exceptiononerror);
}

/**
 * Create a directory under localcachedir and make sure it is writable.
 * The files in this directory MUST NOT change, use revisions or content hashes to
 * work around this limitation - this means you can only add new files here.
 *
 * The content of this directory gets purged automatically on all cluster nodes
 * after calling purge_all_caches() before new data is written to this directory.
 *
 * Note: this local cache directory does not need to be shared by cluster nodes.
 *
 * @param string $directory the relative path of the directory to be created under $CFG->localcachedir
 * @param bool $exceptiononerror throw exception if error encountered
 * @return string|false Returns full path to directory if successful, false if not; may throw exception
 */
function make_localcache_directory($directory, $exceptiononerror = true) {
    global $CFG;

    make_writable_directory($CFG->localcachedir, $exceptiononerror);

    if ($CFG->localcachedir !== "$CFG->dataroot/localcache") {
        protect_directory($CFG->localcachedir);
    } else {
        protect_directory($CFG->dataroot);
    }

    if (!isset($CFG->localcachedirpurged)) {
        $CFG->localcachedirpurged = 0;
    }
    $timestampfile = "$CFG->localcachedir/.lastpurged";

    if (!file_exists($timestampfile)) {
        touch($timestampfile);
        @chmod($timestampfile, $CFG->filepermissions);

    } else if (filemtime($timestampfile) <  $CFG->localcachedirpurged) {
        // This means our local cached dir was not purged yet.
        remove_dir($CFG->localcachedir, true);
        if ($CFG->localcachedir !== "$CFG->dataroot/localcache") {
            protect_directory($CFG->localcachedir);
        }
        touch($timestampfile);
        @chmod($timestampfile, $CFG->filepermissions);
        clearstatcache();
    }

    if ($directory === '') {
        return $CFG->localcachedir;
    }

    return make_writable_directory("$CFG->localcachedir/$directory", $exceptiononerror);
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

    /**
     * Constructor - to be used by core code only.
     * @param string $method The method to call
     * @param array $arguments Arguments to pass to the method being called
     * @return string
     */
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
     * @static
     * @param string $message error message
     * @param string $moreinfourl (ignored in early errors)
     * @param string $link (ignored in early errors)
     * @param array $backtrace
     * @param string $debuginfo
     * @return string
     */
    public static function early_error_content($message, $moreinfourl, $link, $backtrace, $debuginfo = null) {
        global $CFG;

        $content = '<div style="margin-top: 6em; margin-left:auto; margin-right:auto; color:#990000; text-align:center; font-size:large; border-width:1px;
border-color:black; background-color:#ffffee; border-style:solid; border-radius: 20px; border-collapse: collapse;
width: 80%; -moz-border-radius: 20px; padding: 15px">
' . $message . '
</div>';
        // Check whether debug is set.
        $debug = (!empty($CFG->debug) && $CFG->debug >= DEBUG_DEVELOPER);
        // Also check we have it set in the config file. This occurs if the method to read the config table from the
        // database fails, reading from the config table is the first database interaction we have.
        $debug = $debug || (!empty($CFG->config_php_settings['debug'])  && $CFG->config_php_settings['debug'] >= DEBUG_DEVELOPER );
        if ($debug) {
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
     * @static
     * @param string $message error message
     * @param string $moreinfourl (ignored in early errors)
     * @param string $link (ignored in early errors)
     * @param array $backtrace
     * @param string $debuginfo extra information for developers
     * @return string
     */
    public static function early_error($message, $moreinfourl, $link, $backtrace, $debuginfo = null, $errorcode = null) {
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
            $e->errorcode  = $errorcode;
            @header('Content-Type: application/json; charset=utf-8');
            echo json_encode($e);
            return;
        }

        // In the name of protocol correctness, monitoring and performance
        // profiling, set the appropriate error headers for machine consumption.
        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
        @header($protocol . ' 503 Service Unavailable');

        // better disable any caching
        @header('Content-Type: text/html; charset=utf-8');
        @header('X-UA-Compatible: IE=edge');
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

    /**
     * Early notification message
     * @static
     * @param string $message
     * @param string $classes usually notifyproblem or notifysuccess
     * @return string
     */
    public static function early_notification($message, $classes = 'notifyproblem') {
        return '<div class="' . $classes . '">' . $message . '</div>';
    }

    /**
     * Page should redirect message.
     * @static
     * @param string $encodedurl redirect url
     * @return string
     */
    public static function plain_redirect_message($encodedurl) {
        $message = '<div style="margin-top: 3em; margin-left:auto; margin-right:auto; text-align:center;">' . get_string('pageshouldredirect') . '<br /><a href="'.
                $encodedurl .'">'. get_string('continue') .'</a></div>';
        return self::plain_page(get_string('redirect'), $message);
    }

    /**
     * Early redirection page, used before full init of $PAGE global
     * @static
     * @param string $encodedurl redirect url
     * @param string $message redirect message
     * @param int $delay time in seconds
     * @return string redirect page
     */
    public static function early_redirect_message($encodedurl, $message, $delay) {
        $meta = '<meta http-equiv="refresh" content="'. $delay .'; url='. $encodedurl .'" />';
        $content = self::early_error_content($message, null, null, null);
        $content .= self::plain_redirect_message($encodedurl);

        return self::plain_page(get_string('redirect'), $content, $meta);
    }

    /**
     * Output basic html page.
     * @static
     * @param string $title page title
     * @param string $content page content
     * @param string $meta meta tag
     * @return string html page
     */
    public static function plain_page($title, $content, $meta = '') {
        if (function_exists('get_string') && function_exists('get_html_lang')) {
            $htmllang = get_html_lang();
        } else {
            $htmllang = '';
        }

        $footer = '';
        if (MDL_PERF_TEST) {
            $perfinfo = get_performance_info();
            $footer = '<footer>' . $perfinfo['html'] . '</footer>';
        }

        return '<!DOCTYPE html>
<html ' . $htmllang . '>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
'.$meta.'
<title>' . $title . '</title>
</head><body>' . $content . $footer . '</body></html>';
    }
}
