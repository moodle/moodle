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
/** All problems. Formerly, all problems, except the erstwhile strict PHP warnings before E_STRICT got deprecated. */
define('DEBUG_ALL', E_ALL);
/** Same as DEBUG_ALL since E_STRICT was deprecated. */
define('DEBUG_DEVELOPER', E_ALL);

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
 * Get the Whoops! handler.
 *
 * @return \Whoops\Run|null
 */
function get_whoops(): ?\Whoops\Run {
    global $CFG;

    if (CLI_SCRIPT || AJAX_SCRIPT) {
        return null;
    }

    if (defined('PHPUNIT_TEST') && PHPUNIT_TEST) {
        return null;
    }

    if (defined('BEHAT_SITE_RUNNING') && BEHAT_SITE_RUNNING) {
        return null;
    }

    if (empty($CFG->debugdisplay)) {
        return null;
    }

    if (empty($CFG->debug_developer_use_pretty_exceptions)) {
        return null;
    }

    $composerautoload = "{$CFG->dirroot}/../vendor/autoload.php";
    if (file_exists($composerautoload)) {
        require_once($composerautoload);
    }

    if (!class_exists(\Whoops\Run::class)) {
        return null;
    }

    // We have Whoops available, use it.
    $whoops = new \Whoops\Run();

    // Append a custom handler to add some more information to the frames.
    $whoops->appendHandler(function ($exception, $inspector, $run) {
        $collection = $inspector->getFrames();

        // Detect if the Whoops handler was immediately invoked by a call to `debugging()`.
        // If so, we remove the top frames in the collection to avoid showing the inner
        // workings of debugging, and the point that we trigger the error that is picked up by Whoops.
        $isdebugging = count($collection) > 2;
        $isdebugging = $isdebugging && str_ends_with($collection[1]->getFile(), '/lib/weblib.php');
        $isdebugging = $isdebugging && $collection[2]->getFunction() === 'debugging';

        if ($isdebugging) {
            $remove = array_slice($collection->getArray(), 0, 2);
            $collection->filter(function ($frame) use ($remove): bool {
                return array_search($frame, $remove) === false;
            });
        } else {
            // Moodle exceptions often have a link to the Moodle docs pages for them.
            // Add that to the first frame in the stack.
            $info = get_exception_info($exception);
            if ($info->moreinfourl) {
                $collection[0]->addComment("{$info->moreinfourl}", 'More info');
            }
        }
    });

    // Add the Pretty page handler. It's the bee's knees.
    $handler = new \Whoops\Handler\PrettyPageHandler();
    if (isset($CFG->debug_developer_editor)) {
        $handler->setEditor($CFG->debug_developer_editor ?: null);
    }
    $whoops->appendHandler($handler);

    return $whoops;
}

/**
 * Default exception handler.
 *
 * @param Throwable $ex
 * @return void -does not return. Terminates execution!
 */
function default_exception_handler(Throwable $ex): void {
    global $CFG, $DB, $OUTPUT, $USER, $FULLME, $SESSION, $PAGE;

    // detect active db transactions, rollback and log as error
    abort_all_db_transactions();

    if (($ex instanceof required_capability_exception) && !CLI_SCRIPT && !AJAX_SCRIPT && !empty($CFG->autologinguests) && !empty($USER->autologinguest)) {
        $SESSION->wantsurl = qualified_me();
        redirect(get_login_url());
    }

    $info = get_exception_info($ex);

    // If we already tried to send the header remove it, the content length
    // should be either empty or the length of the error page.
    @header_remove('Content-Length');

    if ($whoops = get_whoops()) {
        // If whoops is available we will use it. The get_whoops() function checks whether all conditions are met.
        $whoops->handleException($ex);
    }

    if (is_early_init($info->backtrace)) {
        echo bootstrap_renderer::early_error($info->message, $info->moreinfourl, $info->link, $info->backtrace, $info->debuginfo, $info->errorcode);
    } else {
        if (debugging('', DEBUG_MINIMAL)) {
            $logerrmsg = "Default exception handler: ".$info->message.' Debug: '.$info->debuginfo."\n".format_backtrace($info->backtrace, true);
            error_log($logerrmsg);
        }

        try {
            if ($DB) {
                // If you enable db debugging and exception is thrown, the print footer prints a lot of rubbish
                $DB->set_debug(0);
            }
            if (AJAX_SCRIPT) {
                // If we are in an AJAX script we don't want to use PREFERRED_RENDERER_TARGET.
                // Because we know we will want to use ajax format.
                $renderer = new core_renderer_ajax($PAGE, 'ajax');
            } else {
                $renderer = $OUTPUT;
            }
            echo $renderer->fatal_error($info->message, $info->moreinfourl, $info->link, $info->backtrace, $info->debuginfo,
                $info->errorcode);
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
 * @return bool false means use default error handler
 */
function default_error_handler($errno, $errstr, $errfile, $errline) {
    if ($whoops = get_whoops()) {
        // If whoops is available we will use it. The get_whoops() function checks whether all conditions are met.
        $whoops->handleError($errno, $errstr, $errfile, $errline);
    }
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
        array('file' => __DIR__.'/setup.php'),
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
 * Returns detailed information about specified exception.
 *
 * @param Throwable $ex any sort of exception or throwable.
 * @return stdClass standardised info to display. Fields are clear if you look at the end of this function.
 */
function get_exception_info($ex): stdClass {
    global $CFG;

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
    $cfgnames = array('backuptempdir', 'tempdir', 'cachedir', 'localcachedir', 'themedir', 'dataroot', 'dirroot');
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
        $message = htmlspecialchars($message, ENT_COMPAT);
    }

    if (!empty($CFG->errordocroot)) {
        $errordoclink = $CFG->errordocroot . '/en/';
    } else {
        // Only if the function is available. May be not for early errors.
        if (function_exists('current_language')) {
            $errordoclink = get_docs_url();
        } else {
            $errordoclink = 'https://docs.moodle.org/en/';
        }
    }

    if ($module === 'error') {
        $modulelink = 'moodle';
    } else {
        $modulelink = $module;
    }
    $moreinfourl = $errordoclink . 'error/' . $modulelink . '/' . $errorcode;

    if (empty($link)) {
        $link = get_local_referer(false) ?: ($CFG->wwwroot . '/');
    }

    // When printing an error the continue button should never link offsite.
    // We cannot use clean_param() here as it is not guaranteed that it has been loaded yet.
    if (stripos($link, $CFG->wwwroot) === 0) {
        // Internal HTTP, all good.
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
    if ($path === null) {
        $path = '';
    }

    $path = $path ?? '';
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
 * This function does not include function arguments because they could contain sensitive information
 * not suitable to be exposed in a response.
 *
 * @param array $callers backtrace array, as returned by debug_backtrace().
 * @param boolean $plaintext if false, generates HTML, if true generates plain text.
 * @return string formatted backtrace, ready for output.
 */
function format_backtrace($callers, $plaintext = false) {
    // Do not use $CFG->dirroot because it might not be available in destructors.
    $dirroot = dirname(__DIR__, 2);

    if (empty($callers)) {
        return '';
    }

    $from = $plaintext ? '' : '<ul style="text-align: left" data-rel="backtrace">';
    foreach ($callers as $caller) {
        if (!isset($caller['line'])) {
            $caller['line'] = '?'; // Probably call_user_func().
        }
        if (!isset($caller['file'])) {
            $caller['file'] = 'unknownfile'; // Probably call_user_func().
        }
        $line = $plaintext ? '* ' : '<li>';
        $line .= sprintf(
            'line %d of %s',
            $caller['line'],
            str_replace($dirroot, '', $caller['file']),
        );
        if (isset($caller['function'])) {
            $line .= ': call to ';
            if (isset($caller['class'])) {
                $line .= $caller['class'] . $caller['type'];
            }
            $line .= "{$caller['function']}()";
        } else if (isset($caller['exception'])) {
            $line .= ": {$caller['exception']} thrown";
        }

        // Remove any non printable chars.
        $line = preg_replace('/[[:^print:]]/', '', $line);

        $line .= $plaintext ? "\n" : '</li>';
        $from .= $line;
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
        throw new \moodle_exception('sessionautostartwarning', 'admin');
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
 * Cache any immutable config locally to avoid constant DB lookups.
 *
 * Only to be used only from lib/setup.php
 */
function initialise_local_config_cache() {
    global $CFG;

    $bootstraplocalfile = $CFG->localcachedir . '/bootstrap.php';
    $bootstrapsharedfile = $CFG->cachedir . '/bootstrap.php';

    if (!is_readable($bootstraplocalfile) && is_readable($bootstrapsharedfile)) {
        // If we don't have a local cache but do have a shared cache then clone it,
        // for example when scaling up new front ends.
        make_localcache_directory('', true);
        copy($bootstrapsharedfile, $bootstraplocalfile);
    }

    if (!empty($CFG->siteidentifier) && !file_exists($bootstrapsharedfile) && defined('SYSCONTEXTID')) {
        $contents = "<?php
// ********** This file is generated DO NOT EDIT **********
\$CFG->siteidentifier = " . var_export($CFG->siteidentifier, true) . ";
\$CFG->bootstraphash = " . var_export(hash_local_config_cache(), true) . ";
// Only if the file is not stale and has not been defined.
if (\$CFG->bootstraphash === hash_local_config_cache() && !defined('SYSCONTEXTID')) {
    define('SYSCONTEXTID', ".SYSCONTEXTID.");
}
";

        // Create the central bootstrap first.
        $temp = $bootstrapsharedfile . '.tmp' . uniqid();
        file_put_contents($temp, $contents);
        @chmod($temp, $CFG->filepermissions);
        rename($temp, $bootstrapsharedfile);

        // Then prewarm the local cache as well.
        make_localcache_directory('', true);
        copy($bootstrapsharedfile, $bootstraplocalfile);
    }
}

/**
 * Calculate a proper hash to be able to invalidate stale cached configs.
 *
 * Only to be used to verify bootstrap.php status.
 *
 * @return string md5 hash of all the sensible bits deciding if cached config is stale or no.
 */
function hash_local_config_cache() {
    global $CFG;

    // This is pretty much {@see moodle_database::get_settings_hash()} that is used
    // as identifier for the database meta information MUC cache. Should be enough to
    // react against any of the normal changes (new prefix, change of DB type) while
    // *incorrectly* keeping the old dataroot directory unmodified with stale data.
    // This may need more stuff to be considered if it's discovered that there are
    // more variables making the file stale.
    return md5($CFG->dbtype . $CFG->dbhost . $CFG->dbuser . $CFG->dbname . $CFG->prefix);
}

/**
 * Initialises $FULLME and friends. Private function. Should only be called from
 * setup.php.
 */
function initialise_fullme() {
    global $CFG, $FULLME, $ME, $SCRIPT, $FULLSCRIPT;

    $setuphelper = \core\di::get(\core\setup::class);

    // Detect common config errors in the wwwroot.
    $setuphelper->validate_wwwroot();

    if (CLI_SCRIPT) {
        initialise_fullme_cli();
        return;
    }
    if (!empty($CFG->overridetossl)) {
        if (strpos($CFG->wwwroot, 'http://') === 0) {
            $CFG->wwwroot = str_replace('http:', 'https:', $CFG->wwwroot);
        } else {
            unset_config('overridetossl');
        }
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
            $rfullpath = $rurl['fullpath'];
            // Check that URL is under $CFG->wwwroot.
            if (strpos($rfullpath, $wwwroot['path']) === 0) {
                $rfullpath = substr($rurl['fullpath'], strlen($wwwroot['path']) - 1);
                $rfullpath = (new moodle_url($rfullpath))->out(false);
            }
            redirect($rfullpath, get_string('wwwrootmismatch', 'error', $CFG->wwwroot), 3);
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
            if (defined('REQUIRE_CORRECT_ACCESS') && REQUIRE_CORRECT_ACCESS) {
                throw new \moodle_exception('sslonlyaccess', 'error');
            } else {
                redirect($CFG->wwwroot, get_string('wwwrootmismatch', 'error', $CFG->wwwroot), 3);
            }
        }
    } else {
        if ($wwwroot['scheme'] !== 'https') {
            throw new coding_exception('Must use https address in wwwroot when ssl proxy enabled!');
        }
        $rurl['scheme'] = 'https'; // make moodle believe it runs on https, squid or something else it doing it
        $_SERVER['HTTPS'] = 'on'; // Override $_SERVER to help external libraries with their HTTPS detection.
        $_SERVER['SERVER_PORT'] = 443; // Assume default ssl port for the proxy.
    }

    // Using Moodle in "reverse proxy" mode, it's expected that the HTTP Host Moodle receives is different
    // from the wwwroot configured host. Those URLs being identical could be the consequence of various
    // issues, including:
    // - Intentionally trying to set up moodle with 2 distinct addresses for intranet and Internet: this
    //   configuration is unsupported and will lead to bigger problems down the road (the proper solution
    //   for this is adjusting the network routes, and avoid relying on the application for network concerns).
    // - Misconfiguration of the reverse proxy that would be forwarding the Host header: while it is
    //   standard in many cases that the reverse proxy would do that, in our case, the reverse proxy
    //   must leave the Host header pointing to the internal name of the server.
    // Port forwarding is allowed, though.
    if (!empty($CFG->reverseproxy) && $rurl['host'] === $wwwroot['host'] && (empty($wwwroot['port']) || $rurl['port'] === $wwwroot['port'])) {
        throw new \moodle_exception('reverseproxyabused', 'error');
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
    $rurl['port'] = (int)$_SERVER['SERVER_PORT'];
    $rurl['path'] = $_SERVER['SCRIPT_NAME']; // Script path without slash arguments
    $rurl['scheme'] = (empty($_SERVER['HTTPS']) or $_SERVER['HTTPS'] === 'off' or $_SERVER['HTTPS'] === 'Off' or $_SERVER['HTTPS'] === 'OFF') ? 'http' : 'https';

    if (stripos($_SERVER['SERVER_SOFTWARE'], 'apache') !== false) {
        //Apache server
        $rurl['fullpath'] = $_SERVER['REQUEST_URI'];

        // Fixing a known issue with:
        // - Apache
        // - PHP deployed in Apache as PHP-FPM via mod_proxy_fcgi
        // - PHP versions lesser than 8.1.18 or 8.2.5.
        if (isset($_SERVER['PATH_INFO']) && (php_sapi_name() === 'fpm-fcgi') && isset($_SERVER['SCRIPT_NAME'])) {
            $_SERVER['PATH_INFO'] = rawurldecode($_SERVER['PATH_INFO']);
            if (PHP_VERSION_ID < 80118 || (PHP_VERSION_ID >= 80200 && PHP_VERSION_ID < 80205)) {
                $lenneedle = strlen($_SERVER['PATH_INFO']);
                // Checks whether SCRIPT_NAME ends with PATH_INFO, URL-decoded.
                if (substr($_SERVER['SCRIPT_NAME'], -$lenneedle) === $_SERVER['PATH_INFO']) {
                    // This is the "Apache running PHP-FPM via mod_proxy_fcgi with PHP < 8.1.18 or PHP < 8.2.5" fingerprint,
                    // at least on CentOS 7 (Apache/2.4.6 PHP/8.0.30)
                    // => SCRIPT_NAME contains 'slash arguments' data too, which is wrongly exposed via PATH_INFO as URL-encoded.
                    // Fix $_SERVER['SCRIPT_NAME'].
                    $lenhaystack = strlen($_SERVER['SCRIPT_NAME']);
                    $pos = $lenhaystack - $lenneedle;
                    // Here $pos is greater than 0 but let's double check it.
                    if ($pos > 0) {
                        $_SERVER['SCRIPT_NAME'] = substr($_SERVER['SCRIPT_NAME'], 0, $pos);
                    }
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

    } else if (stripos($_SERVER['SERVER_SOFTWARE'], 'nginx') !== false) {
        if (!isset($_SERVER['SCRIPT_NAME'])) {
            die('Invalid server configuration detected, please try to add "fastcgi_param SCRIPT_NAME $fastcgi_script_name;" to the nginx server configuration.');
        }
        $rurl['fullpath'] = $_SERVER['REQUEST_URI'];
    } else {
        // Any other servers we can assume will pass the request_uri normally.
        $rurl['fullpath'] = $_SERVER['REQUEST_URI'];
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
    $fun = function($p) use ($delim) {
        return implode($delim, $p);
    };
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
        'K' => 1024 ** 1,
        'k' => 1024 ** 1,
        'M' => 1024 ** 2,
        'm' => 1024 ** 2,
        'G' => 1024 ** 3,
        'g' => 1024 ** 3,
        'T' => 1024 ** 4,
        't' => 1024 ** 4,
        'P' => 1024 ** 5,
        'p' => 1024 ** 5,
    );

    if (preg_match('/^([0-9]+)([KMGTP])/i', $size, $matches)) {
        return $matches[1] * $binaryprefixes[$matches[2]];
    }

    return (int) $size;
}

/**
 * Check whether a major upgrade is needed.
 *
 * That is defined as an upgrade that changes something really fundamental
 * in the database, so nothing can possibly work until the database has
 * been updated, and that is defined by the hard-coded version number in
 * this function.
 *
 * @return bool
 */
function is_major_upgrade_required() {
    global $CFG;
    $lastmajordbchanges = 2024010400.00; // This should be the version where the breaking changes happen.

    $required = empty($CFG->version);
    $required = $required || (float)$CFG->version < $lastmajordbchanges;
    $required = $required || during_initial_install();
    $required = $required || !empty($CFG->adminsetuppending);

    return $required;
}

/**
 * Redirect to the Notifications page if a major upgrade is required, and
 * terminate the current user session.
 */
function redirect_if_major_upgrade_required() {
    global $CFG;
    if (is_major_upgrade_required()) {
        try {
            @\core\session\manager::terminate_current();
        } catch (Exception $e) {
            // Ignore any errors, redirect to upgrade anyway.
        }
        $url = $CFG->wwwroot . '/' . $CFG->admin . '/index.php';
        @header($_SERVER['SERVER_PROTOCOL'] . ' 303 See Other');
        @header('Location: ' . $url);
        echo bootstrap_renderer::plain_redirect_message(htmlspecialchars($url, ENT_COMPAT));
        exit;
    }
}

/**
 * Makes sure that upgrade process is not running
 *
 * To be inserted in the core functions that can not be called by pluigns during upgrade.
 * Core upgrade should not use any API functions at all.
 * See {@link https://moodledev.io/docs/guides/upgrade#upgrade-code-restrictions}
 *
 * @throws moodle_exception if executed from inside of upgrade script and $warningonly is false
 * @param bool $warningonly if true displays a warning instead of throwing an exception
 * @return bool true if executed from outside of upgrade process, false if from inside upgrade process and function is used for warning only
 */
#[\core\attribute\deprecated(
    replacement: 'Use \core\setup::ensure_upgrade_is_not_running() or \core\setup::warn_if_upgrade_is_running() instead.',
    mdl: 'MDL-87107',
    since: '5.2',
)]
function upgrade_ensure_not_running($warningonly = false) {
    \core\deprecation::emit_deprecation(__FUNCTION__);
    if ($warningonly) {
        return !\core\setup::warn_if_upgrade_is_running();
    } else {
        return !\core\setup::ensure_upgrade_is_not_running();
    }
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
        // Let's use uniqid() because it's "unique enough" (microtime based). The loop does handle repetitions.
        // Windows and old PHP don't like very long paths, so try to keep this shorter. See MDL-69975.
        $uniquedir = $basedir . DIRECTORY_SEPARATOR . uniqid();
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
 * @param   bool    $exceptiononerror throw exception if error encountered
 * @param   bool    $forcecreate Force creation of a new parent directory
 * @return  string  Returns full path to directory if successful, false if not; may throw exception
 */
function get_request_storage_directory($exceptiononerror = true, bool $forcecreate = false) {
    global $CFG;

    static $requestdir = null;

    $writabledirectoryexists = (null !== $requestdir);
    $writabledirectoryexists = $writabledirectoryexists && file_exists($requestdir);
    $writabledirectoryexists = $writabledirectoryexists && is_dir($requestdir);
    $writabledirectoryexists = $writabledirectoryexists && is_writable($requestdir);
    $createnewdirectory = $forcecreate || !$writabledirectoryexists;

    if ($createnewdirectory) {

        // Let's add the first chars of siteidentifier only. This is to help separate
        // paths on systems which host multiple moodles. We don't use the full id
        // as Windows and old PHP don't like very long paths. See MDL-69975.
        $basedir = $CFG->localrequestdir . '/' . substr($CFG->siteidentifier, 0, 4);

        make_writable_directory($basedir);
        protect_directory($basedir);

        if ($dir = make_unique_writable_directory($basedir, $exceptiononerror)) {
            // Register a shutdown handler to remove the directory.
            \core\shutdown_manager::register_function('remove_dir', [$dir]);
        }

        $requestdir = $dir;
    }

    return $requestdir;
}

/**
 * Create a per-request directory and make sure it is writable.
 * This can only be used during the current request and will be tidied away
 * automatically afterwards.
 *
 * A new, unique directory is always created within a shared base request directory.
 *
 * In some exceptional cases an alternative base directory may be required. This can be accomplished using the
 * $forcecreate parameter. Typically this will only be requried where the file may be required during a shutdown handler
 * which may or may not be registered after a previous request directory has been created.
 *
 * @param   bool    $exceptiononerror throw exception if error encountered
 * @param   bool    $forcecreate Force creation of a new parent directory
 * @return  string  The full path to directory if successful, false if not; may throw exception
 */
function make_request_directory(bool $exceptiononerror = true, bool $forcecreate = false) {
    $basedir = get_request_storage_directory($exceptiononerror, $forcecreate);
    return make_unique_writable_directory($basedir, $exceptiononerror);
}

/**
 * Get the full path of a directory under $CFG->backuptempdir.
 *
 * @param string $directory  the relative path of the directory under $CFG->backuptempdir
 * @return string|false Returns full path to directory given a valid string; otherwise, false.
 */
function get_backup_temp_directory($directory) {
    global $CFG;
    if (($directory === null) || ($directory === false)) {
        return false;
    }
    return "$CFG->backuptempdir/$directory";
}

/**
 * Create a directory under $CFG->backuptempdir and make sure it is writable.
 *
 * Do not use for storing generic temp files - see make_temp_directory() instead for this purpose.
 *
 * Backup temporary files must be on a shared storage.
 *
 * @param string $directory  the relative path of the directory to be created under $CFG->backuptempdir
 * @param bool $exceptiononerror throw exception if error encountered
 * @return string|false Returns full path to directory if successful, false if not; may throw exception
 */
function make_backup_temp_directory($directory, $exceptiononerror = true) {
    global $CFG;
    if ($CFG->backuptempdir !== "$CFG->tempdir/backup") {
        check_dir_exists($CFG->backuptempdir, true, true);
        protect_directory($CFG->backuptempdir);
    } else {
        protect_directory($CFG->tempdir);
    }
    return make_writable_directory("$CFG->backuptempdir/$directory", $exceptiononerror);
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

        // Then prewarm the local boostrap.php file as well.
        initialise_local_config_cache();
    }

    if ($directory === '') {
        return $CFG->localcachedir;
    }

    return make_writable_directory("$CFG->localcachedir/$directory", $exceptiononerror);
}

/**
 * Webserver access user logging
 */
function set_access_log_user() {
    global $USER, $CFG;
    if ($USER && isset($USER->username)) {
        $logmethod = '';
        $logvalue = 0;
        if (!empty($CFG->apacheloguser) && function_exists('apache_note')) {
            $logmethod = 'apache';
            $logvalue = $CFG->apacheloguser;
        }
        if (!empty($CFG->headerloguser)) {
            $logmethod = 'header';
            $logvalue = $CFG->headerloguser;
        }
        if (!empty($logmethod)) {
            $loguserid = $USER->id;
            $logusername = clean_filename($USER->username);
            $logname = '';
            if (isset($USER->firstname)) {
                // We can assume both will be set
                // - even if to empty.
                $logname = clean_filename($USER->firstname . " " . $USER->lastname);
            }
            if (\core\session\manager::is_loggedinas()) {
                $realuser = \core\session\manager::get_realuser();
                $logusername = clean_filename($realuser->username." as ".$logusername);
                $logname = clean_filename($realuser->firstname." ".$realuser->lastname ." as ".$logname);
                $loguserid = clean_filename($realuser->id." as ".$loguserid);
            }
            switch ($logvalue) {
                case 3:
                    $logname = $logusername;
                    break;
                case 2:
                    $logname = $logname;
                    break;
                case 1:
                default:
                    $logname = $loguserid;
                    break;
            }
            if ($logmethod == 'apache') {
                apache_note('MOODLEUSER', $logname);
            }

            if ($logmethod == 'header' && !headers_sent()) {
                header("X-MOODLEUSER: $logname");
            }
        }
    }
}


/**
 * Add http stream instrumentation
 *
 * This detects which any reads or writes to a php stream which uses
 * the 'http' handler. Ideally 100% of traffic uses the Moodle curl
 * libraries which do not use php streams.
 *
 * @param array $code stream callback code
 */
function proxy_log_callback($code) {
    if ($code == STREAM_NOTIFY_CONNECT) {
        $trace = debug_backtrace();
        $function = $trace[count($trace) - 1];
        $error = "Unsafe internet IO detected: {$function['function']} with arguments " . join(', ', $function['args']) . "\n";
        error_log($error . format_backtrace($trace, true)); // phpcs:ignore
    }
}

/**
 * A helper function for deprecated files to use to ensure that, when they are included for unit tests,
 * they are run in an isolated process.
 *
 * @throws \coding_exception The exception thrown when the process is not isolated.
 */
function require_phpunit_isolation(): void {
    if (!defined('PHPUNIT_TEST') || !PHPUNIT_TEST) {
        // Not a test.
        return;
    }

    if (defined('PHPUNIT_ISOLATED_TEST') && PHPUNIT_ISOLATED_TEST) {
        // Already isolated.
        return;
    }

    throw new \coding_exception(
        'When including this file for a unit test, the test must be run in an isolated process. ' .
            'See the PHPUnit @runInSeparateProcess and @runTestsInSeparateProcesses annotations.'
    );
}
