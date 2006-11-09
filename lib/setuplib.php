<?php // $Id$ 
      // These functions are required very early in the Moodle 
      // setup process, before any of the main libraries are 
      // loaded.


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

    global $PERF;

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

/**
 * This function will introspect inside DB to detect it it's a UTF-8 DB or no
 * Used from setup.php to set correctly "set names" when the installation
 * process is performed without the initial and beautiful installer
 */
function setup_is_unicodedb() {

    global $CFG, $db, $INSTALL;

    $unicodedb = false;
    
    // Since this function is also used during installation process, i.e. during install.php before $CFG->dbtype is set.
    // we need to get dbtype from the right variable 
    if (!empty($INSTALL['dbtype'])) {
        $dbtype = $INSTALL['dbtype'];
    } else {
        $dbtype = $CFG->dbtype;
    }

    switch ($dbtype) {
        case 'mysql':
            $rs = $db->Execute("SHOW VARIABLES LIKE 'character_set_database'");
            if ($rs && $rs->RecordCount() > 0) {
                $records = $rs->GetAssoc(true);
                $encoding = $records['character_set_database']['Value'];
                if (strtoupper($encoding) == 'UTF8') {
                    $unicodedb = true;
                }
            }
            break;
        case 'postgres7':
        /// Get PostgreSQL server_encoding value
            $rs = $db->Execute("SHOW server_encoding");
            if ($rs && $rs->RecordCount() > 0) {
                $encoding = $rs->fields['server_encoding'];
                if (strtoupper($encoding) == 'UNICODE' || strtoupper($encoding) == 'UTF8') {
                    $unicodedb = true;
                }
            }
            break;
        case 'mssql':
        case 'mssql_n':
        case 'odbc_mssql':
        /// MSSQL only runs under UTF8 + the proper ODBTP driver (both for Unix and Win32)
            $unicodedb = true;
            break;
        case 'oci8po':
        /// Get Oracle DB character set value
            $rs = $db->Execute("SELECT parameter, value FROM nls_database_parameters where parameter = 'NLS_CHARACTERSET'");
            if ($rs && $rs->RecordCount() > 0) {
                $encoding = $rs->fields['value'];
                if (strtoupper($encoding) == 'AL32UTF8') {
                    $unicodedb = true;
                }
            }
            break;
    }
    return $unicodedb;
}

?>
