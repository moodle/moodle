<?php // $Id$
      // These functions are required very early in the Moodle
      // setup process, before any of the main libraries are
      // loaded.


/**
 * Simple class
 */
class object {};

/**
 * Base Moodle Exception class
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

        $message = get_string($errorcode, $module, $a);

        parent::__construct($message, 0);
    }
}

/**
 * Exception indicating programming error, must be fixed by a programer.
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
 * Default exception handler, uncought exceptions are equivalent to using print_error()
 */
function default_exception_handler($ex) {
    global $CFG;

    $backtrace = $ex->getTrace();
    $place = array('file'=>$ex->getFile(), 'line'=>$ex->getLine(), 'exception'=>get_class($ex));
    array_unshift($backtrace, $place);

    if ($ex instanceof moodle_exception) {
        if (!isset($CFG->theme) or !isset($CFG->stylesheets)) {
            _print_early_error($ex->errorcode, $ex->module, $ex->a, $backtrace, $ex->debuginfo);
        } else {
            _print_normal_error($ex->errorcode, $ex->module, $ex->a, $ex->link, $backtrace, $ex->debuginfo);
        }
    } else {
        if (!isset($CFG->theme) or !isset($CFG->stylesheets)) {
            _print_early_error('generalexceptionmessage', 'error', $ex->getMessage(), $backtrace);
        } else {
            _print_normal_error('generalexceptionmessage', 'error', $ex->getMessage(), '', $backtrace);
        }
    }
}

/**
 * Initialises $FULLME and friends.
 * @return void
 */
function initialise_fullme() {
    global $CFG, $FULLME, $ME, $SCRIPT, $FULLSCRIPT;

    if (substr($CFG->wwwroot, -1) == '/') {
        print_error('wwwrootslash', 'error');
    }

    $url = parse_url($CFG->wwwroot);
    if (!isset($url['path'])) {
        $url['path'] = '';
    }
    $url['path'] .= '/';

    if (CLI_SCRIPT) {
        // urls do not make much sense in CLI scripts
        $backtrace = debug_backtrace();
        $topfile = array_pop($backtrace);
        $topfile = realpath($topfile['file']);
        $dirroot = realpath($CFG->dirroot);

        if (strpos($topfile, $dirroot) !== 0) {
            $SCRIPT = $FULLSCRIPT = $FULLME = $ME = null;
        } else {
            $relme = substr($topfile, strlen($dirroot));
            $relme = str_replace('\\', '/', $relme); // Win fix
            $SCRIPT = $FULLSCRIPT = $FULLME = $ME = $relme;
        }

        return;
    }

    $rurl = array();
    $hostport = explode(':', $_SERVER['HTTP_HOST']);
    $rurl['host'] = reset($hostport);
    $rurl['port'] = $_SERVER['SERVER_PORT'];
    $rurl['path'] = $_SERVER['SCRIPT_NAME']; // script path without slash arguments

    if (stripos($_SERVER['SERVER_SOFTWARE'], 'apache') !== false) {
        //Apache server
        $rurl['scheme']   = empty($_SERVER['HTTPS']) ? 'http' : 'https';
        $rurl['fullpath'] = $_SERVER['REQUEST_URI']; // TODO: verify this is always properly encoded

    } else if (stripos($_SERVER['SERVER_SOFTWARE'], 'lighttpd') !== false) {
        //lighttpd
        $rurl['scheme']   = empty($_SERVER['HTTPS']) ? 'http' : 'https';
        $rurl['fullpath'] = $_SERVER['REQUEST_URI']; // TODO: verify this is always properly encoded

    } else if (stripos($_SERVER['SERVER_SOFTWARE'], 'iis') !== false) {
        //IIS
        $rurl['scheme']   = ($_SERVER['HTTPS'] == 'off') ? 'http' : 'https';
        $rurl['fullpath'] = $_SERVER['SCRIPT_NAME'];

        // NOTE: ignore PATH_INFO because it is incorrectly encoded using 8bit filesystem legacy encoding in IIS
        //       since 2.0 we rely on iis rewrite extenssion like Helicon ISAPI_rewrite
        //       example rule: RewriteRule ^([^\?]+?\.php)(\/.+)$ $1\?file=$2 [QSA]

        if ($_SERVER['QUERY_STRING'] != '') {
            $rurl['fullpath'] .= '?'.$_SERVER['QUERY_STRING'];
        }
        $_SERVER['REQUEST_URI'] = $rurl['fullpath']; // extra IIS compatibility

    } else {
        print_error('unsupportedwebserver', 'error', '', $_SERVER['SERVER_SOFTWARE']);
    }

    if (strpos($rurl['path'], $url['path']) === 0) {
        $SCRIPT = substr($rurl['path'], strlen($url['path'])-1);
    } else {
        // probably some weird external script
        $SCRIPT = $FULLSCRIPT = $FULLME = $ME = null;
        return;
    }

    // $CFG->sslproxy specifies if external SSL apliance is used (server using http, ext box translating everything to https)
    if (empty($CFG->sslproxy)) {
        if ($rurl['scheme'] == 'http' and $url['scheme'] == 'https') {
            print_error('sslonlyaccess', 'error');
        }
    }

    // $CFG->reverseproxy specifies if reverse proxy server used - used in advanced load balancing setups only!
    // this is not supposed to solve lan/wan access problems!!!!!
    if (empty($CFG->reverseproxy)) {
        if (($rurl['host'] != $url['host']) or (!empty($url['port']) and $rurl['port'] != $url['port'])) {
            print_error('wwwrootmismatch', 'error', '', $CFG->wwwroot);
        }
    } else {
        if ($rurl['host'] == $url['host']) {
            // hopefully this will stop all those "clever" admins trying to set up moodle with two different addresses in intranet and Internet
            print_error('reverseproxyabused', 'error');
        }
    }

    $FULLME     = $rurl['scheme'].'://'.$url['host'];
    if (!empty($url['port'])) {
        $FULLME .= ':'.$url['port'];
    }
    $FULLSCRIPT = $FULLME.$rurl['path'];
    $FULLME     = $FULLME.$rurl['fullpath'];
    $ME         = $rurl['fullpath'];

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

    $PERF = new object();
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
 * Function to raise the memory limit to a new value.
 * Will respect the memory limit if it is higher, thus allowing
 * settings in php.ini, apache conf or command line switches
 * to override it
 *
 * The memory limit should be expressed with a string (eg:'64M')
 *
 * @param string $newlimit the new memory limit
 * @return bool
 */
function raise_memory_limit($newlimit) {

    if (empty($newlimit)) {
        return false;
    }

    $cur = @ini_get('memory_limit');
    if (empty($cur)) {
        // if php is compiled without --enable-memory-limits
        // apparently memory_limit is set to ''
        $cur=0;
    } else {
        if ($cur == -1){
            return true; // unlimited mem!
        }
      $cur = get_real_size($cur);
    }

    $new = get_real_size($newlimit);
    if ($new > $cur) {
        ini_set('memory_limit', $newlimit);
        return true;
    }
    return false;
}

/**
 * Converts numbers like 10M into bytes.
 *
 * @param mixed $size The size to be converted
 * @return mixed
 */
function get_real_size($size=0) {
    if (!$size) {
        return 0;
    }
    $scan = array();
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
 * Create a directory.
 *
 * @uses $CFG
 * @param string $directory  a string of directory names under $CFG->dataroot eg  stuff/assignment/1
 * param bool $shownotices If true then notification messages will be printed out on error.
 * @return string|false Returns full path to directory if successful, false if not
 */
function make_upload_directory($directory, $shownotices=true) {

    global $CFG;

    $currdir = $CFG->dataroot;

    umask(0000);

    if (!file_exists($currdir)) {
        if (! mkdir($currdir, $CFG->directorypermissions)) {
            if ($shownotices) {
                echo '<div class="notifyproblem" align="center">ERROR: You need to create the directory '.
                     $currdir .' with web server write access</div>'."<br />\n";
            }
            return false;
        }
    }

    // Make sure a .htaccess file is here, JUST IN CASE the files area is in the open
    if (!file_exists($currdir.'/.htaccess')) {
        if ($handle = fopen($currdir.'/.htaccess', 'w')) {   // For safety
            @fwrite($handle, "deny from all\r\nAllowOverride None\r\n");
            @fclose($handle);
        }
    }

    $dirarray = explode('/', $directory);

    foreach ($dirarray as $dir) {
        $currdir = $currdir .'/'. $dir;
        if (! file_exists($currdir)) {
            if (! mkdir($currdir, $CFG->directorypermissions)) {
                if ($shownotices) {
                    echo '<div class="notifyproblem" align="center">ERROR: Could not find or create a directory ('.
                         $currdir .')</div>'."<br />\n";
                }
                return false;
            }
            //@chmod($currdir, $CFG->directorypermissions);  // Just in case mkdir didn't do it
        }
    }

    return $currdir;
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



?>
