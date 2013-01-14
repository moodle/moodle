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

require_once(__DIR__ . '/../../testing/classes/util.php');

require_once(__DIR__ . '/behat_command.php');
require_once(__DIR__ . '/behat_config_manager.php');

require_once(__DIR__ . '/../../filestorage/file_exceptions.php');
require_once(__DIR__ . '/../../phpunit/bootstraplib.php');

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
     * Allows / disables the test environment to be accessed through the built-in server
     *
     * Built-in server must be started separately
     *
     * @param string $testenvironment enable|disable
     */
    public static function switchenvironment($testenvironment) {
        if ($testenvironment == 'enable') {
            self::start_test_mode();
        } else if ($testenvironment == 'disable') {
            self::stop_test_mode();
        }
    }

    /**
     * Checks if $CFG->behat_wwwroot is available
     *
     * @return boolean
     */
    public static function is_server_running() {
        global $CFG;

        $request = new curl();
        $request->get($CFG->behat_wwwroot);
        return (true && !$request->get_errno());
    }

    /**
     * Checks whether the test database and dataroot is ready
     * Stops execution if something went wrong
     */
    protected static function test_environment_problem() {
        global $CFG;

        // PHPUnit --diag returns nothing if the test environment is set up correctly.
        exec('php ' . $CFG->dirroot . '/' . $CFG->admin . '/tool/phpunit/cli/util.php --diag', $output, $code);

        // If something is not ready stop execution and display the CLI command output.
        if ($code != 0) {
            notice(get_string('phpunitenvproblem', 'tool_behat') . ': ' . implode(' ', $output));
        }
    }

    /**
     * Enables test mode
     *
     * Starts the test mode checking the composer installation and
     * the phpunit test environment and updating the available
     * features and steps definitions.
     *
     * Stores a file in dataroot/behat to allow Moodle to switch
     * to the test environment when using cli-server (or $CFG->behat_switchcompletely)
     *
     * @throws file_exception
     */
    protected static function start_test_mode() {
        global $CFG;

        // Checks the behat set up and the PHP version.
        behat_command::check_behat_setup(true);

        // Check that PHPUnit test environment is correctly set up.
        self::test_environment_problem();

        // Updates all the Moodle features and steps definitions.
        behat_config_manager::update_config_file();

        if (self::is_test_mode_enabled()) {
            debugging('Test environment was already enabled');
            return;
        }

        $behatdir = behat_command::get_behat_dir();

        $contents = '$CFG->behat_wwwroot, $CFG->phpunit_prefix and $CFG->phpunit_dataroot' .
            ' are currently used as $CFG->wwwroot, $CFG->prefix and $CFG->dataroot';
        $filepath = $behatdir . '/test_environment_enabled.txt';
        if (!file_put_contents($filepath, $contents)) {
            throw new file_exception('cannotcreatefile', $filepath);
        }
        chmod($filepath, $CFG->directorypermissions);
    }

    /**
     * Disables test mode
     * @throws file_exception
     */
    protected static function stop_test_mode() {

        $testenvfile = self::get_test_filepath();

        if (!self::is_test_mode_enabled()) {
            debugging('Test environment was already disabled');
        } else {
            if (!unlink($testenvfile)) {
                throw new file_exception('cannotdeletetestenvironmentfile');
            }
        }
    }

    /**
     * Checks whether test environment is enabled or disabled
     *
     * To check is the current script is running in the test
     * environment
     *
     * @see tool_behat::is_test_environment_running()
     * @return bool
     */
    public static function is_test_mode_enabled() {

        $testenvfile = self::get_test_filepath();
        if (file_exists($testenvfile)) {
            return true;
        }

        return false;
    }

    /**
     * Returns true if Moodle is currently running with the test database and dataroot
     * @return bool
     */
    public static function is_test_environment_running() {
        global $CFG;

        if (!empty($CFG->originaldataroot)) {
            return true;
        }

        return false;
    }

    /**
     * Returns the path to the file which specifies if test environment is enabled
     *
     * The file is in dataroot/behat/ but we need to
     * know if test mode is running because then we swap
     * it to phpunit_dataroot and we need the original value
     *
     * @return string
     */
    protected final static function get_test_filepath() {
        global $CFG;

        if (self::is_test_environment_running()) {
            $prefix = $CFG->originaldataroot;
        } else {
            $prefix = $CFG->dataroot;
        }

        return $prefix . '/behat/test_environment_enabled.txt';
    }

}
