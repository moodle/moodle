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
 * Utility class.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Collection of utility methods.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class phpunit_util {
    /** @var string current version hash from php files */
    protected static $versionhash = null;

    /** @var array original content of all database tables*/
    protected static $tabledata = null;

    /** @var array original structure of all database tables */
    protected static $tablestructure = null;

    /** @var array original structure of all database tables */
    protected static $sequencenames = null;

    /** @var array An array of original globals, restored after each test */
    protected static $globals = array();

    /** @var int last value of db writes counter, used for db resetting */
    public static $lastdbwrites = null;

    /** @var phpunit_data_generator */
    protected static $generator = null;

    /** @var resource used for prevention of parallel test execution */
    protected static $lockhandle = null;

    /** @var array list of debugging messages triggered during the last test execution */
    protected static $debuggings = array();

    /** @var phpunit_message_sink alternative target for moodle messaging */
    protected static $messagesink = null;

    /** @var phpunit_phpmailer_sink alternative target for phpmailer messaging */
    protected static $phpmailersink = null;

    /**
     * Prevent parallel test execution - this can not work in Moodle because we modify database and dataroot.
     *
     * Note: do not call manually!
     *
     * @internal
     * @static
     * @return void
     */
    public static function acquire_test_lock() {
        global $CFG;
        if (!file_exists("$CFG->phpunit_dataroot/phpunit")) {
            // dataroot not initialised yet
            return;
        }
        if (!file_exists("$CFG->phpunit_dataroot/phpunit/lock")) {
            file_put_contents("$CFG->phpunit_dataroot/phpunit/lock", 'This file prevents concurrent execution of Moodle PHPUnit tests');
            phpunit_boostrap_fix_file_permissions("$CFG->phpunit_dataroot/phpunit/lock");
        }
        if (self::$lockhandle = fopen("$CFG->phpunit_dataroot/phpunit/lock", 'r')) {
            $wouldblock = null;
            $locked = flock(self::$lockhandle, (LOCK_EX | LOCK_NB), $wouldblock);
            if (!$locked) {
                if ($wouldblock) {
                    echo "Waiting for other test execution to complete...\n";
                }
                $locked = flock(self::$lockhandle, LOCK_EX);
            }
            if (!$locked) {
                fclose(self::$lockhandle);
                self::$lockhandle = null;
            }
        }
        register_shutdown_function(array('phpunit_util', 'release_test_lock'));
    }

    /**
     * Note: do not call manually!
     * @internal
     * @static
     * @return void
     */
    public static function release_test_lock() {
        if (self::$lockhandle) {
            flock(self::$lockhandle, LOCK_UN);
            fclose(self::$lockhandle);
            self::$lockhandle = null;
        }
    }

    /**
     * Load global $CFG;
     * @internal
     * @static
     * @return void
     */
    public static function initialise_cfg() {
        global $DB;
        $dbhash = false;
        try {
            $dbhash = $DB->get_field('config', 'value', array('name'=>'phpunittest'));
        } catch (Exception $e) {
            // not installed yet
            initialise_cfg();
            return;
        }
        if ($dbhash !== phpunit_util::get_version_hash()) {
            // do not set CFG - the only way forward is to drop and reinstall
            return;
        }
        // standard CFG init
        initialise_cfg();
    }

    /**
     * Get data generator
     * @static
     * @return phpunit_data_generator
     */
    public static function get_data_generator() {
        if (is_null(self::$generator)) {
            require_once(__DIR__.'/../generatorlib.php');
            self::$generator = new phpunit_data_generator();
        }
        return self::$generator;
    }

    /**
     * Returns contents of all tables right after installation.
     * @static
     * @return array $table=>$records
     */
    protected static function get_tabledata() {
        global $CFG;

        if (!file_exists("$CFG->dataroot/phpunit/tabledata.ser")) {
            // not initialised yet
            return array();
        }

        if (!isset(self::$tabledata)) {
            $data = file_get_contents("$CFG->dataroot/phpunit/tabledata.ser");
            self::$tabledata = unserialize($data);
        }

        if (!is_array(self::$tabledata)) {
            phpunit_bootstrap_error(1, 'Can not read dataroot/phpunit/tabledata.ser or invalid format, reinitialize test database.');
        }

        return self::$tabledata;
    }

    /**
     * Returns structure of all tables right after installation.
     * @static
     * @return array $table=>$records
     */
    public static function get_tablestructure() {
        global $CFG;

        if (!file_exists("$CFG->dataroot/phpunit/tablestructure.ser")) {
            // not initialised yet
            return array();
        }

        if (!isset(self::$tablestructure)) {
            $data = file_get_contents("$CFG->dataroot/phpunit/tablestructure.ser");
            self::$tablestructure = unserialize($data);
        }

        if (!is_array(self::$tablestructure)) {
            phpunit_bootstrap_error(1, 'Can not read dataroot/phpunit/tablestructure.ser or invalid format, reinitialize test database.');
        }

        return self::$tablestructure;
    }

    /**
     * Returns the names of sequences for each autoincrementing id field in all standard tables.
     * @static
     * @return array $table=>$sequencename
     */
    public static function get_sequencenames() {
        global $DB;

        if (isset(self::$sequencenames)) {
            return self::$sequencenames;
        }

        if (!$structure = self::get_tablestructure()) {
            return array();
        }

        self::$sequencenames = array();
        foreach ($structure as $table=>$ignored) {
            $name = $DB->get_manager()->generator->getSequenceFromDB(new xmldb_table($table));
            if ($name !== false) {
                self::$sequencenames[$table] = $name;
            }
        }

        return self::$sequencenames;
    }

    /**
     * Returns list of tables that are unmodified and empty.
     *
     * @static
     * @return array of table names, empty if unknown
     */
    protected static function guess_unmodified_empty_tables() {
        global $DB;

        $dbfamily = $DB->get_dbfamily();

        if ($dbfamily === 'mysql') {
            $empties = array();
            $prefix = $DB->get_prefix();
            $rs = $DB->get_recordset_sql("SHOW TABLE STATUS LIKE ?", array($prefix.'%'));
            foreach ($rs as $info) {
                $table = strtolower($info->name);
                if (strpos($table, $prefix) !== 0) {
                    // incorrect table match caused by _
                    continue;
                }
                if (!is_null($info->auto_increment)) {
                    $table = preg_replace('/^'.preg_quote($prefix, '/').'/', '', $table);
                    if ($info->auto_increment == 1) {
                        $empties[$table] = $table;
                    }
                }
            }
            $rs->close();
            return $empties;

        } else if ($dbfamily === 'mssql') {
            $empties = array();
            $prefix = $DB->get_prefix();
            $sql = "SELECT t.name
                      FROM sys.identity_columns i
                      JOIN sys.tables t ON t.object_id = i.object_id
                     WHERE t.name LIKE ?
                       AND i.name = 'id'
                       AND i.last_value IS NULL";
            $rs = $DB->get_recordset_sql($sql, array($prefix.'%'));
            foreach ($rs as $info) {
                $table = strtolower($info->name);
                if (strpos($table, $prefix) !== 0) {
                    // incorrect table match caused by _
                    continue;
                }
                $table = preg_replace('/^'.preg_quote($prefix, '/').'/', '', $table);
                $empties[$table] = $table;
            }
            $rs->close();
            return $empties;

        } else if ($dbfamily === 'oracle') {
            $sequences = phpunit_util::get_sequencenames();
            $sequences = array_map('strtoupper', $sequences);
            $lookup = array_flip($sequences);
            $empties = array();
            list($seqs, $params) = $DB->get_in_or_equal($sequences);
            $sql = "SELECT sequence_name FROM user_sequences WHERE last_number = 1 AND sequence_name $seqs";
            $rs = $DB->get_recordset_sql($sql, $params);
            foreach ($rs as $seq) {
                $table = $lookup[$seq->sequence_name];
                $empties[$table] = $table;
            }
            $rs->close();
            return $empties;

        } else {
            return array();
        }
    }

    /**
     * Reset all database sequences to initial values.
     *
     * @static
     * @param array $empties tables that are known to be unmodified and empty
     * @return void
     */
    public static function reset_all_database_sequences(array $empties = null) {
        global $DB;

        if (!$data = self::get_tabledata()) {
            // not initialised yet
            return;
        }
        if (!$structure = self::get_tablestructure()) {
            // not initialised yet
            return;
        }

        $dbfamily = $DB->get_dbfamily();
        if ($dbfamily === 'postgres') {
            $queries = array();
            $prefix = $DB->get_prefix();
            foreach ($data as $table=>$records) {
                if (isset($structure[$table]['id']) and $structure[$table]['id']->auto_increment) {
                    if (empty($records)) {
                        $nextid = 1;
                    } else {
                        $lastrecord = end($records);
                        $nextid = $lastrecord->id + 1;
                    }
                    $queries[] = "ALTER SEQUENCE {$prefix}{$table}_id_seq RESTART WITH $nextid";
                }
            }
            if ($queries) {
                $DB->change_database_structure(implode(';', $queries));
            }

        } else if ($dbfamily === 'mysql') {
            $sequences = array();
            $prefix = $DB->get_prefix();
            $rs = $DB->get_recordset_sql("SHOW TABLE STATUS LIKE ?", array($prefix.'%'));
            foreach ($rs as $info) {
                $table = strtolower($info->name);
                if (strpos($table, $prefix) !== 0) {
                    // incorrect table match caused by _
                    continue;
                }
                if (!is_null($info->auto_increment)) {
                    $table = preg_replace('/^'.preg_quote($prefix, '/').'/', '', $table);
                    $sequences[$table] = $info->auto_increment;
                }
            }
            $rs->close();
            $prefix = $DB->get_prefix();
            foreach ($data as $table=>$records) {
                if (isset($structure[$table]['id']) and $structure[$table]['id']->auto_increment) {
                    if (isset($sequences[$table])) {
                        if (empty($records)) {
                            $nextid = 1;
                        } else {
                            $lastrecord = end($records);
                            $nextid = $lastrecord->id + 1;
                        }
                        if ($sequences[$table] != $nextid) {
                            $DB->change_database_structure("ALTER TABLE {$prefix}{$table} AUTO_INCREMENT = $nextid");
                        }

                    } else {
                        // some problem exists, fallback to standard code
                        $DB->get_manager()->reset_sequence($table);
                    }
                }
            }

        } else if ($dbfamily === 'oracle') {
            $sequences = phpunit_util::get_sequencenames();
            $sequences = array_map('strtoupper', $sequences);
            $lookup = array_flip($sequences);

            $current = array();
            list($seqs, $params) = $DB->get_in_or_equal($sequences);
            $sql = "SELECT sequence_name, last_number FROM user_sequences WHERE sequence_name $seqs";
            $rs = $DB->get_recordset_sql($sql, $params);
            foreach ($rs as $seq) {
                $table = $lookup[$seq->sequence_name];
                $current[$table] = $seq->last_number;
            }
            $rs->close();

            foreach ($data as $table=>$records) {
                if (isset($structure[$table]['id']) and $structure[$table]['id']->auto_increment) {
                    $lastrecord = end($records);
                    if ($lastrecord) {
                        $nextid = $lastrecord->id + 1;
                    } else {
                        $nextid = 1;
                    }
                    if (!isset($current[$table])) {
                        $DB->get_manager()->reset_sequence($table);
                    } else if ($nextid == $current[$table]) {
                        continue;
                    }
                    // reset as fast as possible - alternatively we could use http://stackoverflow.com/questions/51470/how-do-i-reset-a-sequence-in-oracle
                    $seqname = $sequences[$table];
                    $cachesize = $DB->get_manager()->generator->sequence_cache_size;
                    $DB->change_database_structure("DROP SEQUENCE $seqname");
                    $DB->change_database_structure("CREATE SEQUENCE $seqname START WITH $nextid INCREMENT BY 1 NOMAXVALUE CACHE $cachesize");
                }
            }

        } else {
            // note: does mssql support any kind of faster reset?
            if (is_null($empties)) {
                $empties = self::guess_unmodified_empty_tables();
            }
            foreach ($data as $table=>$records) {
                if (isset($empties[$table])) {
                    continue;
                }
                if (isset($structure[$table]['id']) and $structure[$table]['id']->auto_increment) {
                    $DB->get_manager()->reset_sequence($table);
                }
            }
        }
    }

    /**
     * Reset all database tables to default values.
     * @static
     * @return bool true if reset done, false if skipped
     */
    public static function reset_database() {
        global $DB;

        if (!is_null(self::$lastdbwrites) and self::$lastdbwrites == $DB->perf_get_writes()) {
            return false;
        }

        $tables = $DB->get_tables(false);
        if (!$tables or empty($tables['config'])) {
            // not installed yet
            return false;
        }

        if (!$data = self::get_tabledata()) {
            // not initialised yet
            return false;
        }
        if (!$structure = self::get_tablestructure()) {
            // not initialised yet
            return false;
        }

        $empties = self::guess_unmodified_empty_tables();

        foreach ($data as $table=>$records) {
            if (empty($records)) {
                if (isset($empties[$table])) {
                    // table was not modified and is empty
                } else {
                    $DB->delete_records($table, array());
                }
                continue;
            }

            if (isset($structure[$table]['id']) and $structure[$table]['id']->auto_increment) {
                $currentrecords = $DB->get_records($table, array(), 'id ASC');
                $changed = false;
                foreach ($records as $id=>$record) {
                    if (!isset($currentrecords[$id])) {
                        $changed = true;
                        break;
                    }
                    if ((array)$record != (array)$currentrecords[$id]) {
                        $changed = true;
                        break;
                    }
                    unset($currentrecords[$id]);
                }
                if (!$changed) {
                    if ($currentrecords) {
                        $lastrecord = end($records);
                        $DB->delete_records_select($table, "id > ?", array($lastrecord->id));
                        continue;
                    } else {
                        continue;
                    }
                }
            }

            $DB->delete_records($table, array());
            foreach ($records as $record) {
                $DB->import_record($table, $record, false, true);
            }
        }

        // reset all next record ids - aka sequences
        self::reset_all_database_sequences($empties);

        // remove extra tables
        foreach ($tables as $table) {
            if (!isset($data[$table])) {
                $DB->get_manager()->drop_table(new xmldb_table($table));
            }
        }

        self::$lastdbwrites = $DB->perf_get_writes();

        return true;
    }

    /**
     * Purge dataroot directory
     * @static
     * @return void
     */
    public static function reset_dataroot() {
        global $CFG;

        $handle = opendir($CFG->dataroot);
        $skip = array('.', '..', 'phpunittestdir.txt', 'phpunit', '.htaccess');
        while (false !== ($item = readdir($handle))) {
            if (in_array($item, $skip)) {
                continue;
            }
            if (is_dir("$CFG->dataroot/$item")) {
                remove_dir("$CFG->dataroot/$item", false);
            } else {
                unlink("$CFG->dataroot/$item");
            }
        }
        closedir($handle);
        make_temp_directory('');
        make_cache_directory('');
        make_cache_directory('htmlpurifier');
        // Reset the cache API so that it recreates it's required directories as well.
        cache_factory::reset();
        // Purge all data from the caches. This is required for consistency.
        // Any file caches that happened to be within the data root will have already been clearer (because we just deleted cache)
        // and now we will purge any other caches as well.
        cache_helper::purge_all();
    }

    /**
     * Reset contents of all database tables to initial values, reset caches, etc.
     *
     * Note: this is relatively slow (cca 2 seconds for pg and 7 for mysql) - please use with care!
     *
     * @static
     * @param bool $logchanges log changes in global state and database in error log
     * @return void
     */
    public static function reset_all_data($logchanges = false) {
        global $DB, $CFG, $USER, $SITE, $COURSE, $PAGE, $OUTPUT, $SESSION, $GROUPLIB_CACHE;

        // Stop any message redirection.
        phpunit_util::stop_message_redirection();

        // Stop any message redirection.
        phpunit_util::stop_phpmailer_redirection();

        // Release memory and indirectly call destroy() methods to release resource handles, etc.
        gc_collect_cycles();

        // Show any unhandled debugging messages, the runbare() could already reset it.
        self::display_debugging_messages();
        self::reset_debugging();

        // reset global $DB in case somebody mocked it
        $DB = self::get_global_backup('DB');

        if ($DB->is_transaction_started()) {
            // we can not reset inside transaction
            $DB->force_transaction_rollback();
        }

        $resetdb = self::reset_database();
        $warnings = array();

        if ($logchanges) {
            if ($resetdb) {
                $warnings[] = 'Warning: unexpected database modification, resetting DB state';
            }

            $oldcfg = self::get_global_backup('CFG');
            $oldsite = self::get_global_backup('SITE');
            foreach($CFG as $k=>$v) {
                if (!property_exists($oldcfg, $k)) {
                    $warnings[] = 'Warning: unexpected new $CFG->'.$k.' value';
                } else if ($oldcfg->$k !== $CFG->$k) {
                    $warnings[] = 'Warning: unexpected change of $CFG->'.$k.' value';
                }
                unset($oldcfg->$k);

            }
            if ($oldcfg) {
                foreach($oldcfg as $k=>$v) {
                    $warnings[] = 'Warning: unexpected removal of $CFG->'.$k;
                }
            }

            if ($USER->id != 0) {
                $warnings[] = 'Warning: unexpected change of $USER';
            }

            if ($COURSE->id != $oldsite->id) {
                $warnings[] = 'Warning: unexpected change of $COURSE';
            }

        }

        if (ini_get('max_execution_time') != 0) {
            // This is special warning for all resets because we do not want any
            // libraries to mess with timeouts unintentionally.
            // Our PHPUnit integration is not supposed to change it either.

            // TODO: MDL-38912 uncomment and fix all + somehow resolve timeouts in failed tests.
            //$warnings[] = 'Warning: max_execution_time was changed.';
            set_time_limit(0);
        }

        // restore original globals
        $_SERVER = self::get_global_backup('_SERVER');
        $CFG = self::get_global_backup('CFG');
        $SITE = self::get_global_backup('SITE');
        $_GET = array();
        $_POST = array();
        $_FILES = array();
        $_REQUEST = array();
        $COURSE = $SITE;

        // reinitialise following globals
        $OUTPUT = new bootstrap_renderer();
        $PAGE = new moodle_page();
        $FULLME = null;
        $ME = null;
        $SCRIPT = null;
        $SESSION = new stdClass();
        $_SESSION['SESSION'] =& $SESSION;

        // set fresh new not-logged-in user
        $user = new stdClass();
        $user->id = 0;
        $user->mnethostid = $CFG->mnet_localhost_id;
        session_set_user($user);

        // reset all static caches
        accesslib_clear_all_caches(true);
        get_string_manager()->reset_caches(true);
        reset_text_filters_cache(true);
        events_get_handlers('reset');
        textlib::reset_caches();
        if (class_exists('repository')) {
            repository::reset_caches();
        }
        $GROUPLIB_CACHE = null;
        //TODO MDL-25290: add more resets here and probably refactor them to new core function

        // Reset course and module caches.
        if (class_exists('format_base')) {
            // If file containing class is not loaded, there is no cache there anyway.
            format_base::reset_course_cache(0);
        }
        get_fast_modinfo(0, 0, true);

        // Reset other singletons.
        if (class_exists('plugin_manager')) {
            plugin_manager::reset_caches(true);
        }
        if (class_exists('available_update_checker')) {
            available_update_checker::reset_caches(true);
        }
        if (class_exists('available_update_deployer')) {
            available_update_deployer::reset_caches(true);
        }

        // purge dataroot directory
        self::reset_dataroot();

        // restore original config once more in case resetting of caches changed CFG
        $CFG = self::get_global_backup('CFG');

        // inform data generator
        self::get_data_generator()->reset();

        // fix PHP settings
        error_reporting($CFG->debug);

        // verify db writes just in case something goes wrong in reset
        if (self::$lastdbwrites != $DB->perf_get_writes()) {
            error_log('Unexpected DB writes in phpunit_util::reset_all_data()');
            self::$lastdbwrites = $DB->perf_get_writes();
        }

        if ($warnings) {
            $warnings = implode("\n", $warnings);
            trigger_error($warnings, E_USER_WARNING);
        }
    }

    /**
     * Called during bootstrap only!
     * @internal
     * @static
     * @return void
     */
    public static function bootstrap_init() {
        global $CFG, $SITE, $DB;

        // backup the globals
        self::$globals['_SERVER'] = $_SERVER;
        self::$globals['CFG'] = clone($CFG);
        self::$globals['SITE'] = clone($SITE);
        self::$globals['DB'] = $DB;

        // refresh data in all tables, clear caches, etc.
        phpunit_util::reset_all_data();
    }

    /**
     * Returns original state of global variable.
     * @static
     * @param string $name
     * @return mixed
     */
    public static function get_global_backup($name) {
        if ($name === 'DB') {
            // no cloning of database object,
            // we just need the original reference, not original state
            return self::$globals['DB'];
        }
        if (isset(self::$globals[$name])) {
            if (is_object(self::$globals[$name])) {
                $return = clone(self::$globals[$name]);
                return $return;
            } else {
                return self::$globals[$name];
            }
        }
        return null;
    }

    /**
     * Does this site (db and dataroot) appear to be used for production?
     * We try very hard to prevent accidental damage done to production servers!!
     *
     * @static
     * @return bool
     */
    public static function is_test_site() {
        global $DB, $CFG;

        if (!file_exists("$CFG->dataroot/phpunittestdir.txt")) {
            // this is already tested in bootstrap script,
            // but anyway presence of this file means the dataroot is for testing
            return false;
        }

        $tables = $DB->get_tables(false);
        if ($tables) {
            if (!$DB->get_manager()->table_exists('config')) {
                return false;
            }
            if (!get_config('core', 'phpunittest')) {
                return false;
            }
        }

        return true;
    }

    /**
     * Is this site initialised to run unit tests?
     *
     * @static
     * @return int array errorcode=>message, 0 means ok
     */
    public static function testing_ready_problem() {
        global $CFG, $DB;

        $tables = $DB->get_tables(false);

        if (!self::is_test_site()) {
            // dataroot was verified in bootstrap, so it must be DB
            return array(PHPUNIT_EXITCODE_CONFIGERROR, 'Can not use database for testing, try different prefix');
        }

        if (empty($tables)) {
            return array(PHPUNIT_EXITCODE_INSTALL, '');
        }

        if (!file_exists("$CFG->dataroot/phpunit/tabledata.ser") or !file_exists("$CFG->dataroot/phpunit/tablestructure.ser")) {
            return array(PHPUNIT_EXITCODE_REINSTALL, '');
        }

        if (!file_exists("$CFG->dataroot/phpunit/versionshash.txt")) {
            return array(PHPUNIT_EXITCODE_REINSTALL, '');
        }

        $hash = phpunit_util::get_version_hash();
        $oldhash = file_get_contents("$CFG->dataroot/phpunit/versionshash.txt");

        if ($hash !== $oldhash) {
            return array(PHPUNIT_EXITCODE_REINSTALL, '');
        }

        $dbhash = get_config('core', 'phpunittest');
        if ($hash !== $dbhash) {
            return array(PHPUNIT_EXITCODE_REINSTALL, '');
        }

        return array(0, '');
    }

    /**
     * Drop all test site data.
     *
     * Note: To be used from CLI scripts only.
     *
     * @static
     * @param bool $displayprogress if true, this method will echo progress information.
     * @return void may terminate execution with exit code
     */
    public static function drop_site($displayprogress = false) {
        global $DB, $CFG;

        if (!self::is_test_site()) {
            phpunit_bootstrap_error(PHPUNIT_EXITCODE_CONFIGERROR, 'Can not drop non-test site!!');
        }

        // Purge dataroot
        if ($displayprogress) {
            echo "Purging dataroot:\n";
        }
        self::reset_dataroot();
        phpunit_bootstrap_initdataroot($CFG->dataroot);
        $keep = array('.', '..', 'lock', 'webrunner.xml');
        $files = scandir("$CFG->dataroot/phpunit");
        foreach ($files as $file) {
            if (in_array($file, $keep)) {
                continue;
            }
            $path = "$CFG->dataroot/phpunit/$file";
            if (is_dir($path)) {
                remove_dir($path, false);
            } else {
                unlink($path);
            }
        }

        // drop all tables
        $tables = $DB->get_tables(false);
        if (isset($tables['config'])) {
            // config always last to prevent problems with interrupted drops!
            unset($tables['config']);
            $tables['config'] = 'config';
        }

        if ($displayprogress) {
            echo "Dropping tables:\n";
        }
        $dotsonline = 0;
        foreach ($tables as $tablename) {
            $table = new xmldb_table($tablename);
            $DB->get_manager()->drop_table($table);

            if ($dotsonline == 60) {
                if ($displayprogress) {
                    echo "\n";
                }
                $dotsonline = 0;
            }
            if ($displayprogress) {
                echo '.';
            }
            $dotsonline += 1;
        }
        if ($displayprogress) {
            echo "\n";
        }
    }

    /**
     * Perform a fresh test site installation
     *
     * Note: To be used from CLI scripts only.
     *
     * @static
     * @return void may terminate execution with exit code
     */
    public static function install_site() {
        global $DB, $CFG;

        if (!self::is_test_site()) {
            phpunit_bootstrap_error(PHPUNIT_EXITCODE_CONFIGERROR, 'Can not install on non-test site!!');
        }

        if ($DB->get_tables()) {
            list($errorcode, $message) = phpunit_util::testing_ready_problem();
            if ($errorcode) {
                phpunit_bootstrap_error(PHPUNIT_EXITCODE_REINSTALL, 'Database tables already present, Moodle PHPUnit test environment can not be initialised');
            } else {
                phpunit_bootstrap_error(0, 'Moodle PHPUnit test environment is already initialised');
            }
        }

        $options = array();
        $options['adminpass'] = 'admin';
        $options['shortname'] = 'phpunit';
        $options['fullname'] = 'PHPUnit test site';

        install_cli_database($options, false);

        // install timezone info
        $timezones = get_records_csv($CFG->libdir.'/timezone.txt', 'timezone');
        update_timezone_records($timezones);

        // add test db flag
        $hash = phpunit_util::get_version_hash();
        set_config('phpunittest', $hash);

        // store data for all tables
        $data = array();
        $structure = array();
        $tables = $DB->get_tables();
        foreach ($tables as $table) {
            $columns = $DB->get_columns($table);
            $structure[$table] = $columns;
            if (isset($columns['id']) and $columns['id']->auto_increment) {
                $data[$table] = $DB->get_records($table, array(), 'id ASC');
            } else {
                // there should not be many of these
                $data[$table] = $DB->get_records($table, array());
            }
        }
        $data = serialize($data);
        file_put_contents("$CFG->dataroot/phpunit/tabledata.ser", $data);
        phpunit_boostrap_fix_file_permissions("$CFG->dataroot/phpunit/tabledata.ser");

        $structure = serialize($structure);
        file_put_contents("$CFG->dataroot/phpunit/tablestructure.ser", $structure);
        phpunit_boostrap_fix_file_permissions("$CFG->dataroot/phpunit/tablestructure.ser");

        // hash all plugin versions - helps with very fast detection of db structure changes
        file_put_contents("$CFG->dataroot/phpunit/versionshash.txt", $hash);
        phpunit_boostrap_fix_file_permissions("$CFG->dataroot/phpunit/versionshash.txt", $hash);
    }

    /**
     * Calculate unique version hash for all plugins and core.
     * @static
     * @return string sha1 hash
     */
    public static function get_version_hash() {
        global $CFG;

        if (self::$versionhash) {
            return self::$versionhash;
        }

        $versions = array();

        // main version first
        $version = null;
        include($CFG->dirroot.'/version.php');
        $versions['core'] = $version;

        // modules
        $mods = get_plugin_list('mod');
        ksort($mods);
        foreach ($mods as $mod => $fullmod) {
            $module = new stdClass();
            $module->version = null;
            include($fullmod.'/version.php');
            $versions[$mod] = $module->version;
        }

        // now the rest of plugins
        $plugintypes = get_plugin_types();
        unset($plugintypes['mod']);
        ksort($plugintypes);
        foreach ($plugintypes as $type=>$unused) {
            $plugs = get_plugin_list($type);
            ksort($plugs);
            foreach ($plugs as $plug=>$fullplug) {
                $plugin = new stdClass();
                $plugin->version = null;
                @include($fullplug.'/version.php');
                $versions[$plug] = $plugin->version;
            }
        }

        self::$versionhash = sha1(serialize($versions));

        return self::$versionhash;
    }

    /**
     * Builds dirroot/phpunit.xml and dataroot/phpunit/webrunner.xml files using defaults from /phpunit.xml.dist
     * @static
     * @return bool true means main config file created, false means only dataroot file created
     */
    public static function build_config_file() {
        global $CFG;

        $template = '
        <testsuite name="@component@ test suite">
            <directory suffix="_test.php">@dir@</directory>
        </testsuite>';
        $data = file_get_contents("$CFG->dirroot/phpunit.xml.dist");

        $suites = '';

        $plugintypes = get_plugin_types();
        ksort($plugintypes);
        foreach ($plugintypes as $type=>$unused) {
            $plugs = get_plugin_list($type);
            ksort($plugs);
            foreach ($plugs as $plug=>$fullplug) {
                if (!file_exists("$fullplug/tests/")) {
                    continue;
                }
                $dir = substr($fullplug, strlen($CFG->dirroot)+1);
                $dir .= '/tests';
                $component = $type.'_'.$plug;

                $suite = str_replace('@component@', $component, $template);
                $suite = str_replace('@dir@', $dir, $suite);

                $suites .= $suite;
            }
        }

        $data = preg_replace('|<!--@plugin_suites_start@-->.*<!--@plugin_suites_end@-->|s', $suites, $data, 1);

        $result = false;
        if (is_writable($CFG->dirroot)) {
            if ($result = file_put_contents("$CFG->dirroot/phpunit.xml", $data)) {
                phpunit_boostrap_fix_file_permissions("$CFG->dirroot/phpunit.xml");
            }
        }

        // relink - it seems that xml:base does not work in phpunit xml files, remove this nasty hack if you find a way to set xml base for relative refs
        $data = str_replace('lib/phpunit/', $CFG->dirroot.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'phpunit'.DIRECTORY_SEPARATOR, $data);
        $data = preg_replace('|<directory suffix="_test.php">([^<]+)</directory>|',
            '<directory suffix="_test.php">'.$CFG->dirroot.(DIRECTORY_SEPARATOR === '\\' ? '\\\\' : DIRECTORY_SEPARATOR).'$1</directory>',
            $data);
        file_put_contents("$CFG->dataroot/phpunit/webrunner.xml", $data);
        phpunit_boostrap_fix_file_permissions("$CFG->dataroot/phpunit/webrunner.xml");

        return (bool)$result;
    }

    /**
     * Builds phpunit.xml files for all components using defaults from /phpunit.xml.dist
     *
     * @static
     * @return void, stops if can not write files
     */
    public static function build_component_config_files() {
        global $CFG;

        $template = '
        <testsuites>
            <testsuite name="@component@">
                <directory suffix="_test.php">.</directory>
            </testsuite>
        </testsuites>';

        // Use the upstream file as source for the distributed configurations
        $ftemplate = file_get_contents("$CFG->dirroot/phpunit.xml.dist");
        $ftemplate = preg_replace('|<!--All core suites.*</testsuites>|s', '<!--@component_suite@-->', $ftemplate);

        // Get all the components
        $components = self::get_all_plugins_with_tests() + self::get_all_subsystems_with_tests();

        // Get all the directories having tests
        $directories = self::get_all_directories_with_tests();

        // Find any directory not covered by proper components
        $remaining = array_diff($directories, $components);

        // Add them to the list of components
        $components += $remaining;

        // Create the corresponding phpunit.xml file for each component
        foreach ($components as $cname => $cpath) {
            // Calculate the component suite
            $ctemplate = $template;
            $ctemplate = str_replace('@component@', $cname, $ctemplate);

            // Apply it to the file template
            $fcontents = str_replace('<!--@component_suite@-->', $ctemplate, $ftemplate);

            // fix link to schema
            $level = substr_count(str_replace('\\', '/', $cpath), '/') - substr_count(str_replace('\\', '/', $CFG->dirroot), '/');
            $fcontents = str_replace('lib/phpunit/', str_repeat('../', $level).'lib/phpunit/', $fcontents);

            // Write the file
            $result = false;
            if (is_writable($cpath)) {
                if ($result = (bool)file_put_contents("$cpath/phpunit.xml", $fcontents)) {
                    phpunit_boostrap_fix_file_permissions("$cpath/phpunit.xml");
                }
            }
            // Problems writing file, throw error
            if (!$result) {
                phpunit_bootstrap_error(PHPUNIT_EXITCODE_CONFIGWARNING, "Can not create $cpath/phpunit.xml configuration file, verify dir permissions");
            }
        }
    }

    /**
     * Returns all the plugins having PHPUnit tests
     *
     * @return array all the plugins having PHPUnit tests
     *
     */
    private static function get_all_plugins_with_tests() {
        $pluginswithtests = array();

        $plugintypes = get_plugin_types();
        ksort($plugintypes);
        foreach ($plugintypes as $type => $unused) {
            $plugs = get_plugin_list($type);
            ksort($plugs);
            foreach ($plugs as $plug => $fullplug) {
                // Look for tests recursively
                if (self::directory_has_tests($fullplug)) {
                    $pluginswithtests[$type . '_' . $plug] = $fullplug;
                }
            }
        }
        return $pluginswithtests;
    }

    /**
     * Returns all the subsystems having PHPUnit tests
     *
     * Note we are hacking here the list of subsystems
     * to cover some well-known subsystems that are not properly
     * returned by the {@link get_core_subsystems()} function.
     *
     * @return array all the subsystems having PHPUnit tests
     */
    private static function get_all_subsystems_with_tests() {
        global $CFG;

        $subsystemswithtests = array();

        $subsystems = get_core_subsystems();

        // Hack the list a bit to cover some well-known ones
        $subsystems['backup'] = 'backup';
        $subsystems['db-dml'] = 'lib/dml';
        $subsystems['db-ddl'] = 'lib/ddl';

        ksort($subsystems);
        foreach ($subsystems as $subsys => $relsubsys) {
            if ($relsubsys === null) {
                continue;
            }
            $fullsubsys = $CFG->dirroot . '/' . $relsubsys;
            if (!is_dir($fullsubsys)) {
                continue;
            }
            // Look for tests recursively
            if (self::directory_has_tests($fullsubsys)) {
                $subsystemswithtests['core_' . $subsys] = $fullsubsys;
            }
        }
        return $subsystemswithtests;
    }

    /**
     * Returns all the directories having tests
     *
     * @return array all directories having tests
     */
    private static function get_all_directories_with_tests() {
        global $CFG;

        $dirs = array();
        $dirite = new RecursiveDirectoryIterator($CFG->dirroot);
        $iteite = new RecursiveIteratorIterator($dirite);
        $sep = preg_quote(DIRECTORY_SEPARATOR, '|');
        $regite = new RegexIterator($iteite, '|'.$sep.'tests'.$sep.'.*_test\.php$|');
        foreach ($regite as $path => $element) {
            $key = dirname(dirname($path));
            $value = trim(str_replace('/', '_', str_replace($CFG->dirroot, '', $key)), '_');
            $dirs[$key] = $value;
        }
        ksort($dirs);
        return array_flip($dirs);
    }

    /**
     * Returns if a given directory has tests (recursively)
     *
     * @param $dir string full path to the directory to look for phpunit tests
     * @return bool if a given directory has tests (true) or no (false)
     */
    private static function directory_has_tests($dir) {
        if (!is_dir($dir)) {
            return false;
        }

        $dirite = new RecursiveDirectoryIterator($dir);
        $iteite = new RecursiveIteratorIterator($dirite);
        $sep = preg_quote(DIRECTORY_SEPARATOR, '|');
        $regite = new RegexIterator($iteite, '|'.$sep.'tests'.$sep.'.*_test\.php$|');
        $regite->rewind();
        if ($regite->valid()) {
            return true;
        }
        return false;
    }

    /**
     * To be called from debugging() only.
     * @param string $message
     * @param int $level
     * @param string $from
     */
    public static function debugging_triggered($message, $level, $from) {
        // Store only if debugging triggered from actual test,
        // we need normal debugging outside of tests to find problems in our phpunit integration.
        $backtrace = debug_backtrace();

        foreach ($backtrace as $bt) {
            $intest = false;
            if (isset($bt['object']) and is_object($bt['object'])) {
                if ($bt['object'] instanceof PHPUnit_Framework_TestCase) {
                    if (strpos($bt['function'], 'test') === 0) {
                        $intest = true;
                        break;
                    }
                }
            }
        }
        if (!$intest) {
            return false;
        }

        $debug = new stdClass();
        $debug->message = $message;
        $debug->level   = $level;
        $debug->from    = $from;

        self::$debuggings[] = $debug;

        return true;
    }

    /**
     * Resets the list of debugging messages.
     */
    public static function reset_debugging() {
        self::$debuggings = array();
    }

    /**
     * Returns all debugging messages triggered during test.
     * @return array with instances having message, level and stacktrace property.
     */
    public static function get_debugging_messages() {
        return self::$debuggings;
    }

    /**
     * Prints out any debug messages accumulated during test execution.
     * @return bool false if no debug messages, true if debug triggered
     */
    public static function display_debugging_messages() {
        if (empty(self::$debuggings)) {
            return false;
        }
        foreach(self::$debuggings as $debug) {
            echo 'Debugging: ' . $debug->message . "\n" . trim($debug->from) . "\n";
        }

        return true;
    }

    /**
     * Start message redirection.
     *
     * Note: Do not call directly from tests,
     *       use $sink = $this->redirectMessages() instead.
     *
     * @return phpunit_message_sink
     */
    public static function start_message_redirection() {
        if (self::$messagesink) {
            self::stop_message_redirection();
        }
        self::$messagesink = new phpunit_message_sink();
        return self::$messagesink;
    }

    /**
     * End message redirection.
     *
     * Note: Do not call directly from tests,
     *       use $sink->close() instead.
     */
    public static function stop_message_redirection() {
        self::$messagesink = null;
    }

    /**
     * Are messages redirected to some sink?
     *
     * Note: to be called from messagelib.php only!
     *
     * @return bool
     */
    public static function is_redirecting_messages() {
        return !empty(self::$messagesink);
    }

    /**
     * To be called from messagelib.php only!
     *
     * @param stdClass $message record from message_read table
     * @return bool true means send message, false means message "sent" to sink.
     */
    public static function message_sent($message) {
        if (self::$messagesink) {
            self::$messagesink->add_message($message);
        }
    }

    /**
     * Start phpmailer redirection.
     *
     * Note: Do not call directly from tests,
     *       use $sink = $this->redirectEmails() instead.
     *
     * @return phpunit_phpmailer_sink
     */
    public static function start_phpmailer_redirection() {
        if (self::$phpmailersink) {
            self::stop_phpmailer_redirection();
        }
        self::$phpmailersink = new phpunit_phpmailer_sink();
        return self::$phpmailersink;
    }

    /**
     * End phpmailer redirection.
     *
     * Note: Do not call directly from tests,
     *       use $sink->close() instead.
     */
    public static function stop_phpmailer_redirection() {
        self::$phpmailersink = null;
    }

    /**
     * Are messages for phpmailer redirected to some sink?
     *
     * Note: to be called from moodle_phpmailer.php only!
     *
     * @return bool
     */
    public static function is_redirecting_phpmailer() {
        return !empty(self::$phpmailersink);
    }

    /**
     * To be called from messagelib.php only!
     *
     * @param stdClass $message record from message_read table
     * @return bool true means send message, false means message "sent" to sink.
     */
    public static function phpmailer_sent($message) {
        if (self::$phpmailersink) {
            self::$phpmailersink->add_message($message);
        }
    }
}
