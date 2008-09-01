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
    public $extralocations;

    /**
     * Constructor
     * @param string $errorcode The name of the string from error.php to print
     * @param string $module name of module
     * @param string $link The url where the user will be prompted to continue. If no url is provided the user will be directed to the site index page.
     * @param object $a Extra words and phrases that might be required in the error string
     * @param string $debuginfo optional debugging information
     * @param array $extralocations An array of strings with other locations to look for string files
     */
    function __construct($errorcode, $module='error', $link='', $a=NULL, $debuginfo=null, $extralocations=null) {
        if (empty($module) || $module === 'moodle' || $module === 'core') {
            $module = 'error';
        }

        $this->errorcode      = $errorcode;
        $this->module         = $module;
        $this->link           = $link;
        $this->a              = $a;
        $this->debuginfo      = $debuginfo;
        $this->extralocations = $extralocations;

        $message = get_string($errorcode, $module, $a, $extralocations);
        if ($module === 'error' and strpos($message, '[[') === 0) {
            //search in moodle file if error specified - needed for backwards compatibility
            $message = get_string($errorcode, 'moodle', $a, $extralocations);
        }

        parent::__construct($message, 0);
    }
}

/**
 * Default exception handler, uncought exceptions are equivalent to using print_error()
 */
function default_exception_handler($ex) {
    $backtrace = $ex->getTrace();
    $place = array('file'=>$ex->getFile(), 'line'=>$ex->getLine(), 'exception'=>get_class($ex));
    array_unshift($backtrace, $place);

    if ($ex instanceof moodle_exception) {
        _print_normal_error($ex->errorcode, $ex->module, $ex->a, $ex->link, $backtrace, $ex->debuginfo, $ex->extralocations);
    } else {
        _print_normal_error('generalexceptionmessage', 'error', $ex->getMessage(), '', $backtrace);
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
function raise_memory_limit ($newlimit) {

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
