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
 * Various PHPUnit classes and functions
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once 'PHPUnit/Autoload.php';


/**
 * Collection of utility methods.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class phpunit_util {
    /**
     * @var array original content of all database tables
     */
    protected static $tabledata = null;

    /**
     * @var array An array of globals cloned from CFG
     */
    protected static $globals = array();

    /**
     * @var int last value of db writes counter, used for db resetting
     */
    protected static $lastdbwrites = null;

    /**
     * @var phpunit_data_generator
     */
    protected static $generator = null;

    protected static $lockhandle = null;

    /**
     * Prevent parallel test execution - this can not work in Moodle because we modify DB and dataroot.
     *
     * Note: do not call manually!
     *
     * @static
     * @return void
     */
    public static function acquire_test_lock() {
        global $CFG;
        if (!file_exists("$CFG->phpunit_dataroot/phpunit/lock")) {
            file_put_contents("$CFG->phpunit_dataroot/phpunit/lock", 'This file prevents concurrent execution of Moodle PHPUnit tests');
            phpunit_boostrap_fix_file_permissions("$CFG->phpunit_dataroot/phpunit/lock");
        }
        if (self::$lockhandle = fopen("$CFG->phpunit_dataroot/phpunit/lock", 'r')) {
            $wouldblock = null;
            $locked = flock(self::$lockhandle, (LOCK_EX | LOCK_NB), $wouldblock);
            if (!$locked and $wouldblock) {
                echo "Waiting for other test execution to complete...\n";
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
     * Get data generator
     * @static
     * @return phpunit_data_generator
     */
    public static function get_data_generator() {
        if (is_null(self::$generator)) {
            require_once(__DIR__.'/generatorlib.php');
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
     * Reset all database tables to default values.
     * @static
     * @return bool true if reset done, false if skipped
     */
    public static function reset_database() {
        global $DB;

        $tables = $DB->get_tables(false);
        if (!$tables or empty($tables['config'])) {
            // not installed yet
            return false;
        }

        if (!is_null(self::$lastdbwrites) and self::$lastdbwrites == $DB->perf_get_writes()) {
            return false;
        }
        if (!$data = self::get_tabledata()) {
            // not initialised yet
            return false;
        }

        $trans = $DB->start_delegated_transaction(); // faster and safer

        $resetseq = array();
        foreach ($data as $table=>$records) {
            if (empty($records)) {
                if ($DB->count_records($table)) {
                    $DB->delete_records($table, array());
                    $resetseq[$table] = $table;
                }
                continue;
            }

            $firstrecord = reset($records);
            if (property_exists($firstrecord, 'id')) {
                if ($DB->count_records($table) >= count($records)) {
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
                            $remainingfirst = reset($currentrecords);
                            $lastrecord = end($records);
                            if ($remainingfirst->id > $lastrecord->id) {
                                $DB->delete_records_select($table, "id >= ?", array($remainingfirst->id));
                                $resetseq[$table] = $table;
                                continue;
                            }
                        } else {
                            continue;
                        }
                    }
                }
            }

            $DB->delete_records($table, array());
            if (property_exists($firstrecord, 'id')) {
                $resetseq[$table] = $table;
            }
            foreach ($records as $record) {
                $DB->import_record($table, $record, false, true);
            }
        }
        // reset all sequences
        foreach ($resetseq as $table) {
            $DB->get_manager()->reset_sequence($table, true);
        }

        $trans->allow_commit();

        // remove extra tables
        foreach ($tables as $tablename) {
            if (!isset($data[$tablename])) {
                $DB->get_manager()->drop_table(new xmldb_table($tablename));
            }
        }

        self::$lastdbwrites = $DB->perf_get_writes();

        return true;
    }

    /**
     * Purge dataroot
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
    }

    /**
     * Reset contents of all database tables to initial values, reset caches, etc.
     *
     * Note: this is relatively slow (cca 2 seconds for pg and 7 for mysql) - please use with care!
     *
     * @param bool $logchanges log changes in global state and database in error log
     * @return void
     * @static
     */
    public static function reset_all_data($logchanges = false) {
        global $DB, $CFG, $USER, $SITE, $COURSE, $PAGE, $OUTPUT, $SESSION;

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

        // restore original config
        $_SERVER = self::get_global_backup('_SERVER');
        $CFG = self::get_global_backup('CFG');
        $SITE = self::get_global_backup('SITE');
        $COURSE = $SITE;

        // recreate globals
        $OUTPUT = new bootstrap_renderer();
        $PAGE = new moodle_page();
        $FULLME = null;
        $ME = null;
        $SCRIPT = null;
        $SESSION = new stdClass();
        $_SESSION['SESSION'] =& $SESSION;

        // set fresh new user
        $user = new stdClass();
        $user->id = 0;
        $user->mnethostid = $CFG->mnet_localhost_id;
        session_set_user($user);

        // reset all static caches
        accesslib_clear_all_caches(true);
        get_string_manager()->reset_caches();
        //TODO: add more resets here and probably refactor them to new core function

        // purge dataroot
        self::reset_dataroot();

        // restore original config once more in case resetting of caches changes CFG
        $CFG = self::get_global_backup('CFG');

        // remember db writes
        self::$lastdbwrites = $DB->perf_get_writes();

        // inform data generator
        self::get_data_generator()->reset();

        // fix PHP settings
        error_reporting($CFG->debug);

        if ($warnings) {
            $warnings = implode("\n", $warnings);
            trigger_error($warnings, E_USER_WARNING);
        }
    }

    /**
     * Called during bootstrap only!
     * @static
     */
    public static function bootstrap_init() {
        global $CFG, $SITE;

        // backup the globals
        self::$globals['_SERVER'] = $_SERVER;
        self::$globals['CFG'] = clone($CFG);
        self::$globals['SITE'] = clone($SITE);

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
            return array(131, 'Can not use test database, try changing prefix');
        }

        if (!file_exists("$CFG->dataroot/phpunit/tabledata.ser")) {
            if (empty($tables)) {
                return array(132, '');
            } else {
                return array(133, '');
            }
        }

        if (!file_exists("$CFG->dataroot/phpunit/versionshash.txt")) {
            if (empty($tables)) {
                return array(132, '');
            } else {
                return array(133, '');
            }
        }

        $hash = phpunit_util::get_version_hash();
        $oldhash = file_get_contents("$CFG->dataroot/phpunit/versionshash.txt");

        if ($hash !== $oldhash) {
            return array(133, '');
        }

        return array(0, '');
    }

    /**
     * Drop all test site data.
     *
     * Note: To be used from CLI scripts only.
     *
     * @static
     * @return void may terminate execution with exit code
     */
    public static function drop_site() {
        global $DB, $CFG;

        if (!self::is_test_site()) {
            cli_error('Can not drop non-test sites!!', 131);
        }

        // purge dataroot
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
        foreach ($tables as $tablename) {
            $table = new xmldb_table($tablename);
            $DB->get_manager()->drop_table($table);
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
            cli_error('Can not install non-test sites!!', 131);
        }

        if ($DB->get_tables()) {
            cli_error('Database tables already installed, drop the site first.', 133);
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
        set_config('phpunittest', 'phpunittest');

        // store data for all tables
        $data = array();
        $tables = $DB->get_tables();
        foreach ($tables as $table) {
            $columns = $DB->get_columns($table);
            if (isset($columns['id'])) {
                $data[$table] = $DB->get_records($table, array(), 'id ASC');
            } else {
                // there should not be many of these
                $data[$table] = $DB->get_records($table, array());
            }
        }
        $data = serialize($data);
        file_put_contents("$CFG->dataroot/phpunit/tabledata.ser", $data);
        phpunit_boostrap_fix_file_permissions("$CFG->dataroot/phpunit/tabledata.ser");

        // hash all plugin versions - helps with very fast detection of db structure changes
        $hash = phpunit_util::get_version_hash();
        file_put_contents("$CFG->dataroot/phpunit/versionshash.txt", $hash);
        phpunit_boostrap_fix_file_permissions("$CFG->dataroot/phpunit/versionshash.txt", $hash);
    }

    /**
     * Calculate unique version hash for all available plugins and core.
     * @static
     * @return string sha1 hash
     */
    public static function get_version_hash() {
        global $CFG;

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

        $hash = sha1(serialize($versions));

        return $hash;
    }

    /**
     * Builds dirroot/phpunit.xml and dataroot/phpunit/webrunner.xml file using defaults from /phpunit.xml.dist
     * @static
     * @return bool true means main config file created, false means only dataroot file created
     */
    public static function build_config_file() {
        global $CFG;

        $template = '
        <testsuite name="@component@">
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
                $dir = preg_replace("|$CFG->dirroot/|", '', $fullplug, 1);
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
        $data = str_replace('lib/phpunit/', "$CFG->dirroot/lib/phpunit/", $data);
        $data = preg_replace('|<directory suffix="_test.php">([^<]+)</directory>|', '<directory suffix="_test.php">'.$CFG->dirroot.'/$1</directory>', $data);
        file_put_contents("$CFG->dataroot/phpunit/webrunner.xml", $data);
        phpunit_boostrap_fix_file_permissions("$CFG->dataroot/phpunit/webrunner.xml");

        return (bool)$result;
    }
}


/**
 * Simplified emulation test case for legacy SimpleTest.
 *
 * Note: this is supposed to work for very simple tests only.
 *
 * @deprecated since 2.3
 * @package    core
 * @category   phpunit
 * @author     Petr Skoda
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class UnitTestCase extends PHPUnit_Framework_TestCase {

    /**
     * @deprecated since 2.3
     * @param bool $expected
     * @param string $message
     * @return void
     */
    public function expectException($expected, $message = '') {
        // use phpdocs: @expectedException ExceptionClassName
        if (!$expected) {
            return;
        }
        $this->setExpectedException('moodle_exception', $message);
    }

    /**
     * @deprecated since 2.3
     * @param bool $expected
     * @param string $message
     * @return void
     */
    public static function expectError($expected = false, $message = '') {
        // not available in PHPUnit
        if (!$expected) {
            return;
        }
        self::skipIf(true);
    }

    /**
     * @deprecated since 2.3
     * @static
     * @param mixed $actual
     * @param string $messages
     * @return void
     */
    public static function assertTrue($actual, $messages = '') {
        parent::assertTrue((bool)$actual, $messages);
    }

    /**
     * @deprecated since 2.3
     * @static
     * @param mixed $actual
     * @param string $messages
     * @return void
     */
    public static function assertFalse($actual, $messages = '') {
        parent::assertFalse((bool)$actual, $messages);
    }

    /**
     * @deprecated since 2.3
     * @static
     * @param mixed $expected
     * @param mixed $actual
     * @param string $message
     * @return void
     */
    public static function assertEqual($expected, $actual, $message = '') {
        parent::assertEquals($expected, $actual, $message);
    }

    /**
     * @deprecated since 2.3
     * @static
     * @param mixed $expected
     * @param mixed $actual
     * @param string $message
     * @return void
     */
    public static function assertNotEqual($expected, $actual, $message = '') {
        parent::assertNotEquals($expected, $actual, $message);
    }

    /**
     * @deprecated since 2.3
     * @static
     * @param mixed $expected
     * @param mixed $actual
     * @param string $message
     * @return void
     */
    public static function assertIdentical($expected, $actual, $message = '') {
        parent::assertSame($expected, $actual, $message);
    }

    /**
     * @deprecated since 2.3
     * @static
     * @param mixed $expected
     * @param mixed $actual
     * @param string $message
     * @return void
     */
    public static function assertNotIdentical($expected, $actual, $message = '') {
        parent::assertNotSame($expected, $actual, $message);
    }

    /**
     * @deprecated since 2.3
     * @static
     * @param mixed $actual
     * @param mixed $expected
     * @param string $message
     * @return void
     */
    public static function assertIsA($actual, $expected, $message = '') {
        if ($expected === 'array') {
            parent::assertEquals(gettype($actual), 'array', $message);
        } else {
            parent::assertInstanceOf($expected, $actual, $message);
        }
    }
}


/**
 * The simplest PHPUnit test case customised for Moodle
 *
 * It is intended for isolated tests that do not modify database or any globals.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class basic_testcase extends PHPUnit_Framework_TestCase {

    /**
     * Constructs a test case with the given name.
     *
     * Note: use setUp() or setUpBeforeClass() in custom test cases.
     *
     * @param string $name
     * @param array  $data
     * @param string $dataName
     */
    final public function __construct($name = null, array $data = array(), $dataName = '') {
        parent::__construct($name, $data, $dataName);

        $this->setBackupGlobals(false);
        $this->setBackupStaticAttributes(false);
        $this->setRunTestInSeparateProcess(false);
    }

    /**
     * Runs the bare test sequence and log any changes in global state or database.
     * @return void
     */
    public function runBare() {
        parent::runBare();
        phpunit_util::reset_all_data(true);
    }
}


/**
 * Advanced PHPUnit test case customised for Moodle.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class advanced_testcase extends PHPUnit_Framework_TestCase {
    /** @var bool automatically reset everything? null means log changes */
    protected $resetAfterTest;

    /**
     * Constructs a test case with the given name.
     *
     * Note: use setUp() or setUpBeforeClass() in custom test cases.
     *
     * @param string $name
     * @param array  $data
     * @param string $dataName
     */
    final public function __construct($name = null, array $data = array(), $dataName = '') {
        parent::__construct($name, $data, $dataName);

        $this->setBackupGlobals(false);
        $this->setBackupStaticAttributes(false);
        $this->setRunTestInSeparateProcess(false);
    }

    /**
     * Runs the bare test sequence.
     * @return void
     */
    public function runBare() {
        $this->resetAfterTest = null;

        parent::runBare();

        if ($this->resetAfterTest === true) {
            self::resetAllData();
        } else if ($this->resetAfterTest === false) {
            // keep all data untouched for other tests
        } else {
            // reset but log what changed
            phpunit_util::reset_all_data(true);
        }

        $this->resetAfterTest = null;
    }

    /**
     * Reset everything after current test.
     * @param bool $reset true means reset state back, false means keep all data for the next test,
     *      null means reset state and show warnings if anything changed
     * @return void
     */
    public function resetAfterTest($reset = true) {
        $this->resetAfterTest = $reset;
    }

    /**
     * Cleanup after all tests are executed.
     *
     * Note: do not forget to call this if overridden...
     *
     * @static
     * @return void
     */
    public static function tearDownAfterClass() {
        phpunit_util::reset_all_data();
    }

    /**
     * Reset all database tables, restore global state and clear caches and optionally purge dataroot dir.
     * @static
     * @return void
     */
    public static function resetAllData() {
        phpunit_util::reset_all_data();
    }

    /**
     * Set current $USER, reset access cache.
     * @static
     * @param null|int|stdClass $user user record, null means non-logged-in, integer means userid
     * @return void
     */
    public static function setUser($user = null) {
        global $CFG, $DB;

        if (is_object($user)) {
            $user = clone($user);
        } else if (!$user) {
            $user = new stdClass();
            $user->id = 0;
            $user->mnethostid = $CFG->mnet_localhost_id;
        } else {
            $user = $DB->get_record('user', array('id'=>$user));
        }
        unset($user->description);
        unset($user->access);

        session_set_user($user);
    }

    /**
     * Get data generator
     * @static
     * @return phpunit_data_generator
     */
    public static function getDataGenerator() {
        return phpunit_util::get_data_generator();
    }

    /**
     * Recursively visit all the files in the source tree. Calls the callback
     * function with the pathname of each file found.
     *
     * @param string $path the folder to start searching from.
     * @param string $callback the method of this class to call with the name of each file found.
     * @param string $fileregexp a regexp used to filter the search (optional).
     * @param bool $exclude If true, pathnames that match the regexp will be ignored. If false,
     *     only files that match the regexp will be included. (default false).
     * @param array $ignorefolders will not go into any of these folders (optional).
     * @return void
     */
    public function recurseFolders($path, $callback, $fileregexp = '/.*/', $exclude = false, $ignorefolders = array()) {
        $files = scandir($path);

        foreach ($files as $file) {
            $filepath = $path .'/'. $file;
            if (strpos($file, '.') === 0) {
                /// Don't check hidden files.
                continue;
            } else if (is_dir($filepath)) {
                if (!in_array($filepath, $ignorefolders)) {
                    $this->recurseFolders($filepath, $callback, $fileregexp, $exclude, $ignorefolders);
                }
            } else if ($exclude xor preg_match($fileregexp, $filepath)) {
                $this->$callback($filepath);
            }
        }
    }
}


/**
 * Special test case for testing of DML drivers and DDL layer.
 *
 * Note: Use only 'test_table*' when creating new tables.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class database_driver_testcase extends PHPUnit_Framework_TestCase {
    /** @var moodle_database connection to extra database */
    protected static $extradb = null;

    /** @var moodle_database used in these tests*/
    protected $tdb;

    /**
     * Constructs a test case with the given name.
     *
     * @param string $name
     * @param array  $data
     * @param string $dataName
     */
    final public function __construct($name = null, array $data = array(), $dataName = '') {
        parent::__construct($name, $data, $dataName);

        $this->setBackupGlobals(false);
        $this->setBackupStaticAttributes(false);
        $this->setRunTestInSeparateProcess(false);
    }

    public static function setUpBeforeClass() {
        global $CFG;
        parent::setUpBeforeClass();

        if (!defined('PHPUNIT_TEST_DRIVER')) {
            // use normal $DB
            return;
        }

        if (!isset($CFG->phpunit_extra_drivers[PHPUNIT_TEST_DRIVER])) {
            throw new exception('Can not find driver configuration options with index: '.PHPUNIT_TEST_DRIVER);
        }

        $dblibrary = empty($CFG->phpunit_extra_drivers[PHPUNIT_TEST_DRIVER]['dblibrary']) ? 'native' : $CFG->phpunit_extra_drivers[PHPUNIT_TEST_DRIVER]['dblibrary'];
        $dbtype = $CFG->phpunit_extra_drivers[PHPUNIT_TEST_DRIVER]['dbtype'];
        $dbhost = $CFG->phpunit_extra_drivers[PHPUNIT_TEST_DRIVER]['dbhost'];
        $dbname = $CFG->phpunit_extra_drivers[PHPUNIT_TEST_DRIVER]['dbname'];
        $dbuser = $CFG->phpunit_extra_drivers[PHPUNIT_TEST_DRIVER]['dbuser'];
        $dbpass = $CFG->phpunit_extra_drivers[PHPUNIT_TEST_DRIVER]['dbpass'];
        $prefix = $CFG->phpunit_extra_drivers[PHPUNIT_TEST_DRIVER]['prefix'];
        $dboptions = empty($CFG->phpunit_extra_drivers[PHPUNIT_TEST_DRIVER]['dboptions']) ? array() : $CFG->phpunit_extra_drivers[PHPUNIT_TEST_DRIVER]['dboptions'];

        $classname = "{$dbtype}_{$dblibrary}_moodle_database";
        require_once("$CFG->libdir/dml/$classname.php");
        $d = new $classname();
        if (!$d->driver_installed()) {
            throw new exception('Database driver for '.$classname.' is not installed');
        }

        $d->connect($dbhost, $dbuser, $dbpass, $dbname, $prefix, $dboptions);

        self::$extradb = $d;
    }

    protected function setUp() {
        global $DB;
        parent::setUp();

        if (self::$extradb) {
            $this->tdb = self::$extradb;
        } else {
            $this->tdb = $DB;
        }
    }

    protected function tearDown() {
        // delete all test tables
        $dbman = $this->tdb->get_manager();
        $tables = $this->tdb->get_tables(false);
        foreach($tables as $tablename) {
            if (strpos($tablename, 'test_table') === 0) {
                $table = new xmldb_table($tablename);
                $dbman->drop_table($table);
            }
        }
        parent::tearDown();
    }

    public static function tearDownAfterClass() {
        if (self::$extradb) {
            self::$extradb->dispose();
            self::$extradb = null;
        }
        phpunit_util::reset_all_data();
        parent::tearDownAfterClass();
    }
}
