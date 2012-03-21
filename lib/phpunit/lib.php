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

// necessary when loaded from cli/util.php script
// If this is missing then PHPUnit is not in your PHP include path. This normally
// happens if installation didn't complete correctly. Check your environment.
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
     * Returns contents of all tables right after installation.
     * @static
     * @return array $table=>$records
     */
    protected static function get_tabledata() {
        global $CFG;

        if (!isset(self::$tabledata)) {
            $data = file_get_contents("$CFG->dataroot/phpunit/tabledata.ser");
            self::$tabledata = unserialize($data);
        }

        if (!is_array(self::$tabledata)) {
            phpunit_bootstrap_error('Can not read dataroot/phpunit/tabledata.ser or invalid format!');
        }

        return self::$tabledata;
    }

    /**
     * Initialise CFG using data from fresh new install.
     * @static
     */
    public static function initialise_cfg() {
        global $CFG, $DB;

        if (!file_exists("$CFG->dataroot/phpunit/tabledata.ser")) {
            // most probably PHPUnit CLI installer
            return;
        }

        if (!$DB->get_manager()->table_exists('config') or !$DB->count_records('config')) {
            @unlink("$CFG->dataroot/phpunit/tabledata.ser");
            @unlink("$CFG->dataroot/phpunit/versionshash.txt");
            self::$tabledata = null;
            return;
        }

        $data = self::get_tabledata();

        foreach($data['config'] as $record) {
            $name = $record->name;
            $value = $record->value;
            if (property_exists($CFG, $name)) {
                // config.php settings always take precedence
                continue;
            }
            $CFG->{$name} = $value;
        }
    }

    /**
     * Reset contents of all database tables to initial values, reset caches, etc.
     *
     * Note: this is relatively slow (cca 2 seconds for pg and 7 for mysql) - please use with care!
     *
     * @static
     */
    public static function reset_all_data() {
        global $DB, $CFG;

        $data = self::get_tabledata();

        $trans = $DB->start_delegated_transaction(); // faster and safer
        foreach ($data as $table=>$records) {
            $DB->delete_records($table, array());
            $resetseq = null;
            foreach ($records as $record) {
                if (is_null($resetseq)) {
                    $resetseq = property_exists($record, 'id');
                }
                $DB->import_record($table, $record, false, true);
            }
            if ($resetseq === true) {
                $DB->get_manager()->reset_sequence($table, true);
            }
        }
        $trans->allow_commit();

        purge_all_caches();

        $user = new stdClass();
        $user->id = 0;
        $user->mnet = 0;
        $user->mnethostid = $CFG->mnet_localhost_id;
        session_set_user($user);
        accesslib_clear_all_caches_for_unit_testing();
    }

    /**
     * Called during bootstrap only!
     * @static
     */
    public static function init_globals() {
        global $CFG;

        self::$globals['CFG'] = clone($CFG);
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
            // but anway presence of this file means the dataroot is for testing
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
     * @return bool
     */
    public static function is_testing_ready() {
        global $DB, $CFG;

        if (!self::is_test_site()) {
            return false;
        }

        $tables = $DB->get_tables(true);

        if (!$tables) {
            return false;
        }

        if (!get_config('core', 'phpunittest')) {
             return false;
        }

        if (!file_exists("$CFG->dataroot/phpunit/tabledata.ser")) {
            return false;
        }

        if (!file_exists("$CFG->dataroot/phpunit/versionshash.txt")) {
            return false;
        }

        $hash = phpunit_util::get_version_hash();
        $oldhash = file_get_contents("$CFG->dataroot/phpunit/versionshash.txt");

        if ($hash !== $oldhash) {
            return false;
        }

        return true;
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

        // drop dataroot
        remove_dir($CFG->dataroot, true);
        phpunit_bootstrap_initdataroot($CFG->dataroot);

        // drop all tables
        $trans = $DB->start_delegated_transaction();
        $tables = $DB->get_tables(false);
        foreach ($tables as $tablename) {
            $DB->delete_records($tablename, array());
        }
        $trans->allow_commit();

        // now drop them
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
        $options['adminpass'] = 'admin'; // removed later
        $options['shortname'] = 'phpunit';
        $options['fullname'] = 'PHPUnit test site';

        install_cli_database($options, false);

        // just in case remove admin password so that normal login is not possible
        $DB->set_field('user', 'password', 'not cached', array('username' => 'admin'));

        // add test db flag
        set_config('phpunittest', 'phpunittest');

        // store data for all tables
        $data = array();
        $tables = $DB->get_tables();
        foreach ($tables as $table) {
            $data[$table] = $DB->get_records($table, array());
        }
        $data = serialize($data);
        @unlink("$CFG->dataroot/phpunit/tabledata.ser");
        file_put_contents("$CFG->dataroot/phpunit/tabledata.ser", $data);

        // hash all plugin versions - helps with very fast detection of db structure changes
        $hash = phpunit_util::get_version_hash();
        @unlink("$CFG->dataroot/phpunit/versionshash.txt");
        file_put_contents("$CFG->dataroot/phpunit/versionshash.txt", $hash);
    }

    /**
     * Culculate unique version hash for all available plugins and core.
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
     * Builds /phpunit.xml file using defaults from /phpunit.xml.dist
     * @static
     * @return void
     */
    public static function build_config_file() {
        global $CFG;

        $template = '
    <testsuites>
        <testsuite name="@component@">
            <directory suffix="_test.php">@dir@</directory>
        </testsuite>
    </testsuites>';
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

        @unlink("$CFG->dirroot/phpunit.xml");
        file_put_contents("$CFG->dirroot/phpunit.xml", $data);
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
     */
    public static function assertTrue($actual, $messages = '') {
        parent::assertTrue((bool)$actual, $messages);
    }

    /**
     * @deprecated since 2.3
     * @static
     * @param mixed $actual
     * @param string $messages
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
     */
    public static function assertIsA($actual, $expected, $message = '') {
        parent::assertInstanceOf($expected, $actual, $message);
    }
}


/**
 * The simplest PHPUnit test case customised for Moodle
 *
 * This test case does not modify database or any globals.
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
     * @param string $name
     * @param array  $data
     * @param string $dataName
     */
    public function __construct($name = NULL, array $data = array(), $dataName = '') {
        parent::__construct($name, $data, $dataName);

        $this->setBackupGlobals(false);
        $this->setBackupStaticAttributes(false);
        $this->setRunTestInSeparateProcess(false);
        $this->setInIsolation(false);
    }

    /**
     * Runs the bare test sequence.
     * @return void
     */
    public function runBare() {
        global $CFG, $USER, $DB;

        $dbwrites = $DB->perf_get_writes();

        parent::runBare();

        $oldcfg = phpunit_util::get_global_backup('CFG');
        foreach($CFG as $k=>$v) {
            if (!property_exists($oldcfg, $k)) {
                unset($CFG->$k);
                error_log('warning: unexpected new $CFG->'.$k.' value in testcase: '.get_class($this).'->'.$this->getName(true));
            } else if ($oldcfg->$k !== $CFG->$k) {
                $CFG->$k = $oldcfg->$k;
                error_log('warning: unexpected change of $CFG->'.$k.' value in testcase: '.get_class($this).'->'.$this->getName(true));
            }
            unset($oldcfg->$k);

        }
        if ($oldcfg) {
            foreach($oldcfg as $k=>$v) {
                $CFG->$k = $v;
                error_log('warning: unexpected removal of $CFG->'.$k.' in testcase: '.get_class($this).'->'.$this->getName(true));
            }
        }

        if ($USER->id != 0) {
            error_log('warning: unexpected change of $USER in testcase: '.get_class($this).'->'.$this->getName(true));
            $USER = new stdClass();
            $USER->id = 0;
        }

        if ($dbwrites != $DB->perf_get_writes()) {
            //TODO: find out what was changed exactly
            error_log('warning: unexpected database modification, resetting DB state in testcase: '.get_class($this).'->'.$this->getName(true));
            phpunit_util::reset_all_data();
        }

        //TODO: somehow find out if there are changes in dataroot
    }
}