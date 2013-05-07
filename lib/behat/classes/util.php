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
 * Utils for behat-related stuff
 *
 * @package    core
 * @category   test
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib.php');
require_once(__DIR__ . '/../../testing/classes/util.php');
require_once(__DIR__ . '/behat_command.php');
require_once(__DIR__ . '/behat_config_manager.php');

require_once(__DIR__ . '/../../filelib.php');

/**
 * Init/reset utilities for Behat database and dataroot
 *
 * @package   core
 * @category  test
 * @copyright 2013 David Monllaó
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_util extends testing_util {

    /**
     * @var array Files to skip when resetting dataroot folder
     */
    protected static $datarootskiponreset = array('.', '..', 'behat', 'behattestdir.txt');

    /**
     * @var array Files to skip when dropping dataroot folder
     */
    protected static $datarootskipondrop = array('.', '..', 'lock');

    /**
     * Installs a site using $CFG->dataroot and $CFG->prefix
     * @throws coding_exception
     * @return void
     */
    public static function install_site() {
        global $DB;

        if (!defined('BEHAT_UTIL')) {
            throw new coding_exception('This method can be only used by Behat CLI tool');
        }

        // New dataroot.
        self::reset_dataroot();

        $options = array();
        $options['adminuser'] = 'admin';
        $options['adminpass'] = 'admin';
        $options['fullname'] = 'Acceptance test site';
        $options['shortname'] = 'Acceptance test site';

        install_cli_database($options, false);

        // Update admin user info.
        $user = $DB->get_record('user', array('username' => 'admin'));
        $user->email = 'moodle@moodlemoodle.com';
        $user->firstname = 'Admin';
        $user->lastname = 'User';
        $user->city = 'Perth';
        $user->country = 'AU';
        $DB->update_record('user', $user);

        // Disable email message processor.
        $DB->set_field('message_processors', 'enabled', '0', array('name' => 'email'));

        // Sets maximum debug level.
        set_config('debug', DEBUG_DEVELOPER);
        set_config('debugdisplay', true);

        // Keeps the current version of database and dataroot.
        self::store_versions_hash();

        // Stores the database contents for fast reset.
        self::store_database_state();
    }

    /**
     * Drops dataroot and remove test database tables
     * @throws coding_exception
     * @return void
     */
    public static function drop_site() {

        if (!defined('BEHAT_UTIL')) {
            throw new coding_exception('This method can be only used by Behat CLI tool');
        }

        self::reset_dataroot();
        self::drop_dataroot();
        self::drop_database(true);
    }

    /**
     * Checks if $CFG->behat_wwwroot is available
     *
     * @return bool
     */
    public static function is_server_running() {
        global $CFG;

        $request = new curl();
        $request->get($CFG->behat_wwwroot);

        if ($request->get_errno() === 0) {
            return true;
        }
        return false;
    }

    /**
     * Checks whether the test database and dataroot is ready
     * Stops execution if something went wrong
     * @throws coding_exception
     * @return void
     */
    protected static function test_environment_problem() {
        global $CFG, $DB;

        if (!defined('BEHAT_UTIL')) {
            throw new coding_exception('This method can be only used by Behat CLI tool');
        }

        if (!self::is_test_site()) {
            behat_error(1, 'This is not a behat test site!');
        }

        $tables = $DB->get_tables(false);
        if (empty($tables)) {
            behat_error(BEHAT_EXITCODE_INSTALL, '');
        }

        if (!self::is_test_data_updated()) {
            behat_error(BEHAT_EXITCODE_REINSTALL, 'The test environment was initialised for a different version');
        }
    }

    /**
     * Enables test mode
     *
     * It uses CFG->behat_dataroot
     *
     * Starts the test mode checking the composer installation and
     * the test environment and updating the available
     * features and steps definitions.
     *
     * Stores a file in dataroot/behat to allow Moodle to switch
     * to the test environment when using cli-server (or $CFG->behat_switchcompletely)
     * @throws coding_exception
     * @return void
     */
    public static function start_test_mode() {
        global $CFG;

        if (!defined('BEHAT_UTIL')) {
            throw new coding_exception('This method can be only used by Behat CLI tool');
        }

        // Checks the behat set up and the PHP version.
        if ($errorcode = behat_command::behat_setup_problem(true)) {
            exit($errorcode);
        }

        // Check that test environment is correctly set up.
        self::test_environment_problem();

        // Updates all the Moodle features and steps definitions.
        behat_config_manager::update_config_file();

        if (self::is_test_mode_enabled()) {
            return;
        }

        $contents = '$CFG->behat_wwwroot, $CFG->behat_prefix and $CFG->behat_dataroot' .
            ' are currently used as $CFG->wwwroot, $CFG->prefix and $CFG->dataroot';
        $filepath = self::get_test_file_path();
        if (!file_put_contents($filepath, $contents)) {
            behat_error(BEHAT_EXITCODE_PERMISSIONS, 'File ' . $filepath . ' can not be created');
        }
    }

    /**
     * Returns the status of the behat test environment
     *
     * @return int Error code
     */
    public static function get_behat_status() {

        if (!defined('BEHAT_UTIL')) {
            throw new coding_exception('This method can be only used by Behat CLI tool');
        }

        // Checks the behat set up and the PHP version, returning an error code if something went wrong.
        if ($errorcode = behat_command::behat_setup_problem(true)) {
            return $errorcode;
        }

        // Check that test environment is correctly set up, stops execution.
        self::test_environment_problem();
    }

    /**
     * Disables test mode
     * @throws coding_exception
     * @return void
     */
    public static function stop_test_mode() {

        if (!defined('BEHAT_UTIL')) {
            throw new coding_exception('This method can be only used by Behat CLI tool');
        }

        $testenvfile = self::get_test_file_path();

        if (!self::is_test_mode_enabled()) {
            echo "Test environment was already disabled\n";
        } else {
            if (!unlink($testenvfile)) {
                behat_error(BEHAT_EXITCODE_PERMISSIONS, 'Can not delete test environment file');
            }
        }
    }

    /**
     * Checks whether test environment is enabled or disabled
     *
     * To check is the current script is running in the test
     * environment
     *
     * @return bool
     */
    public static function is_test_mode_enabled() {

        $testenvfile = self::get_test_file_path();
        if (file_exists($testenvfile)) {
            return true;
        }

        return false;
    }

    /**
     * Returns the path to the file which specifies if test environment is enabled
     * @return string
     */
    protected final static function get_test_file_path() {
        return behat_command::get_behat_dir() . '/test_environment_enabled.txt';
    }

}
