<?php // $Id$ 
      // These functions are required very early in the Moodle 
      // setup process, before any of the main libraries are 
      // loaded.


/**
 * Simple class
 */
class object {};


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
  
    $PERF = new Object;
    $PERF->dbqueries = 0;   
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
function reduce_memory_limit ($newlimit) {
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
            @fwrite($handle, "deny from all\r\nAllowOverride None\r\nNote: this file is broken intentionally, we do not want anybody to undo it in subdirectory!\r\n");
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

/**
 * This function will introspect inside DB to detect it it's a UTF-8 DB or no
 * Used from setup.php to set correctly "set names" when the installation
 * process is performed without the initial and beautiful installer
 */
function setup_is_unicodedb() {

    global $CFG, $db, $INSTALL;

    $unicodedb = false;
    
    // Calculate $CFG->dbfamily
    $dbfamily = set_dbfamily();

    switch ($dbfamily) {
        case 'mysql':
            $rs = $db->Execute("SHOW LOCAL VARIABLES LIKE 'character_set_database'");
            if ($rs && !$rs->EOF) { // rs_EOF() not available yet
                $records = $rs->GetAssoc(true);
                $encoding = $records['character_set_database']['Value'];
                if (strtoupper($encoding) == 'UTF8') {
                    $unicodedb = true;
                }
            }
            break;
        case 'postgres':
        /// Get PostgreSQL server_encoding value
            $rs = $db->Execute("SHOW server_encoding");
            if ($rs && !$rs->EOF) { // rs_EOF() not available yet
                $encoding = $rs->fields['server_encoding'];
                if (strtoupper($encoding) == 'UNICODE' || strtoupper($encoding) == 'UTF8') {
                    $unicodedb = true;
                }
            }
            break;
        case 'mssql':
        /// MSSQL only runs under UTF8 + the proper ODBTP driver (both for Unix and Win32)
            $unicodedb = true;
            break;
        case 'oracle':
        /// Get Oracle DB character set value
            $rs = $db->Execute("SELECT parameter, value FROM nls_database_parameters where parameter = 'NLS_CHARACTERSET'");
            if ($rs && !$rs->EOF) { // rs_EOF() not available yet
                $encoding = $rs->fields['value'];
                if (strtoupper($encoding) == 'AL32UTF8') {
                    $unicodedb = true;
                }
            }
            break;
    }
    return $unicodedb;
}

/**
 * This internal function sets and returns the proper value for $CFG->dbfamily based on $CFG->dbtype
 * It's called by preconfigure_dbconnection() and at install time. Shouldn't be used
 * in other places. Code should rely on dbfamily to perform conditional execution
 * instead of using dbtype directly. This allows quicker adoption of different
 * drivers going against the same DB backend.
 *
 * This function must contain the init code needed for each dbtype supported.
 *
 * return string dbfamily value (mysql, postgres, oracle, mssql)
 */
function set_dbfamily() {

    global $CFG, $INSTALL;

    // Since this function is also used during installation process, i.e. during install.php before $CFG->dbtype is set.
    // we need to get dbtype from the right variable 
    if (!empty($INSTALL['dbtype'])) {
        $dbtype = $INSTALL['dbtype'];
    } else {
        $dbtype = $CFG->dbtype;
    }

    switch ($dbtype) {
        case 'mysql':
        case 'mysqli':
            $CFG->dbfamily='mysql';
            break;
        case 'postgres7':
            $CFG->dbfamily='postgres';
            break;
        case 'mssql':
        case 'mssql_n':
        case 'odbc_mssql':
            $CFG->dbfamily='mssql';
            break;
        case 'oci8po':
            $CFG->dbfamily='oracle';
            break;
    }

    return $CFG->dbfamily;
}

/**
 * This internal function, called from setup.php BEFORE stabilishing the DB
 * connection, defines the $CFG->dbfamily global -by calling set_dbfamily()-
 * and predefines some constants needed by ADOdb to switch some default
 * behaviours.
 *
 * This function must contain all the pre-connection code needed for each
 * dbtype supported.
 */
function preconfigure_dbconnection() {

    global $CFG;

/// Define dbfamily
    set_dbfamily();

/// Based on $CFG->dbfamily, set some ADOdb settings
    switch ($CFG->dbfamily) {
        /// list here family types where we know
        /// the fieldnames will come in lowercase
        /// so we can avoid expensive tolower()
        case 'postgres':
        case 'mysql':
        case 'mssql':
            define ('ADODB_ASSOC_CASE', 2);
            break;
        case 'oracle':
            define ('ADODB_ASSOC_CASE', 0); /// Use lowercase fieldnames for ADODB_FETCH_ASSOC
                                            /// (only meaningful for oci8po, it's the default
                                            /// for other DB drivers so this won't affect them)
            /// Row prefetching uses a bit of memory but saves a ton
            /// of network latency. With current AdoDB and PHP, only
            /// Oracle uses this setting.
            define ('ADODB_PREFETCH_ROWS', 1000);
            break;
        default:
            /// if we have to lowercase it, set to 0
            /// - note that the lowercasing is very expensive
            define ('ADODB_ASSOC_CASE', 0); //Use lowercase fieldnames for ADODB_FETCH_ASSOC
    }
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
