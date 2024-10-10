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
 * Behat command utils
 *
 * @package    core
 * @category   test
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib.php');

/**
 * Behat command related utils
 *
 * @package    core
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_command {

    /**
     * Docs url
     */
    const DOCS_URL = 'https://moodledev.io/general/development/tools/behat';

    /**
     * Ensures the behat dir exists in moodledata
     *
     * @return string Full path
     */
    public static function get_parent_behat_dir() {
        global $CFG;

        // If not set then return empty string.
        if (!isset($CFG->behat_dataroot_parent)) {
            return "";
        }

        return $CFG->behat_dataroot_parent;
    }

    /**
     * Ensures the behat dir exists in moodledata
     * @param int $runprocess run process for which behat dir is returned.
     * @return string Full path
     */
    public static function get_behat_dir($runprocess = 0) {
        global $CFG;

        // If not set then return empty string.
        if (!isset($CFG->behat_dataroot)) {
            return "";
        }

        // If $CFG->behat_parallel_run starts with index 0 and $runprocess for parallel run starts with 1.
        if (!empty($runprocess) && isset($CFG->behat_parallel_run[$runprocess - 1]['behat_dataroot'])) {
            $behatdir = $CFG->behat_parallel_run[$runprocess - 1]['behat_dataroot'] . '/behat';;
        } else {
            $behatdir = $CFG->behat_dataroot . '/behat';
        }

        if (!is_dir($behatdir)) {
            if (!mkdir($behatdir, $CFG->directorypermissions, true)) {
                behat_error(BEHAT_EXITCODE_PERMISSIONS, 'Directory ' . $behatdir . ' can not be created');
            }
        }

        if (!is_writable($behatdir)) {
            behat_error(BEHAT_EXITCODE_PERMISSIONS, 'Directory ' . $behatdir . ' is not writable');
        }

        return $behatdir;
    }

    /**
     * Returns the executable path
     *
     * Allows returning a customized command for cygwin when the
     * command is just displayed, when using exec(), system() and
     * friends we stay with DIRECTORY_SEPARATOR as they use the
     * normal cmd.exe (in Windows).
     *
     * @param  bool $custombyterm  If the provided command should depend on the terminal where it runs
     * @param bool $parallelrun If parallel run is installed.
     * @param bool $absolutepath return command with absolute path.
     * @return string
     */
    final public static function get_behat_command($custombyterm = false, $parallerun = false, $absolutepath = false) {
        $separator = DIRECTORY_SEPARATOR;
        $exec = 'behat';

        // Cygwin uses linux-style directory separators.

        if ($custombyterm && testing_is_cygwin()) {
            $separator = '/';

            // MinGW can not execute .bat scripts.
            if (!testing_is_mingw()) {
                $exec = 'behat.bat';
            }
        }

        // If relative path then prefix relative path.
        if ($absolutepath) {
            $pathprefix = testing_cli_argument_path('/');
            if (!empty($pathprefix)) {
                $pathprefix .= $separator;
            }
        } else {
            $pathprefix = '';
        }

        if (!$parallerun) {
            $command = $pathprefix . 'vendor' . $separator . 'bin' . $separator . $exec;
        } else {
            $command = 'php ' . $pathprefix . 'admin' . $separator . 'tool' . $separator . 'behat' . $separator . 'cli'
                . $separator . 'run.php';
        }

        return $command;
    }

    /**
     * Runs behat command with provided options
     *
     * Execution continues when the process finishes
     *
     * @param  string $options  Defaults to '' so tests would be executed
     * @return array            CLI command outputs [0] => string, [1] => integer
     */
    final public static function run($options = '') {
        global $CFG;

        $currentcwd = getcwd();
        chdir(dirname($CFG->dirroot));
        exec(self::get_behat_command() . ' ' . $options, $output, $code);
        chdir($currentcwd);

        return array($output, $code);
    }

    /**
     * Checks if behat is set up and working
     *
     * Notifies failures both from CLI and web interface.
     *
     * It checks behat dependencies have been installed and runs
     * the behat help command to ensure it works as expected
     *
     * @return int Error code or 0 if all ok
     */
    public static function behat_setup_problem(): int {
        global $CFG;

        // Moodle setting.
        if (!self::are_behat_dependencies_installed()) {
            // Returning composer error code to avoid conflicts with behat and moodle error codes.
            self::output_msg(get_string('errorcomposer', 'tool_behat'));
            return TESTING_EXITCODE_COMPOSER;
        }

        // Behat test command.
        $dirrootconfigpath = $CFG->dirroot . DIRECTORY_SEPARATOR . 'behat.yml';
        if (file_exists($dirrootconfigpath)) {
            self::output_msg(get_string('warndirrootconfigfound', 'tool_behat', $dirrootconfigpath));
        }
        list($output, $code) = self::run(" --help");

        if ($code != 0) {

            // Returning composer error code to avoid conflicts with behat and moodle error codes.
            self::output_msg(get_string('errorbehatcommand', 'tool_behat', self::get_behat_command()));
            return TESTING_EXITCODE_COMPOSER;
        }

        // No empty values.
        if (empty($CFG->behat_dataroot) || empty($CFG->behat_prefix) || empty($CFG->behat_wwwroot)) {
            self::output_msg(get_string('errorsetconfig', 'tool_behat'));
            return BEHAT_EXITCODE_CONFIG;

        }

        // Not repeated values.
        // We only need to check this when the behat site is not running as
        // at this point, when it is running, all $CFG->behat_* vars have
        // already been copied to $CFG->dataroot, $CFG->prefix and $CFG->wwwroot.
        $phpunitprefix = empty($CFG->phpunit_prefix) ? '' : $CFG->phpunit_prefix;
        $behatdbname = empty($CFG->behat_dbname) ? $CFG->dbname : $CFG->behat_dbname;
        $phpunitdbname = empty($CFG->phpunit_dbname) ? $CFG->dbname : $CFG->phpunit_dbname;
        $behatdbhost = empty($CFG->behat_dbhost) ? $CFG->dbhost : $CFG->behat_dbhost;
        $phpunitdbhost = empty($CFG->phpunit_dbhost) ? $CFG->dbhost : $CFG->phpunit_dbhost;

        $samedataroot = $CFG->behat_dataroot == $CFG->dataroot;
        $samedataroot = $samedataroot || (!empty($CFG->phpunit_dataroot) && $CFG->phpunit_dataroot == $CFG->behat_dataroot);
        $samewwwroot = $CFG->behat_wwwroot == $CFG->wwwroot;
        $sameprefix = ($CFG->behat_prefix == $CFG->prefix && $behatdbname == $CFG->dbname && $behatdbhost == $CFG->dbhost);
        $sameprefix = $sameprefix || ($CFG->behat_prefix == $phpunitprefix && $behatdbname == $phpunitdbname &&
                $behatdbhost == $phpunitdbhost);
        if (!defined('BEHAT_SITE_RUNNING') && ($samedataroot || $samewwwroot || $sameprefix)) {
            self::output_msg(get_string('erroruniqueconfig', 'tool_behat'));
            return BEHAT_EXITCODE_CONFIG;
        }

        // Checking behat dataroot existence otherwise echo about admin/tool/behat/cli/init.php.
        if (!empty($CFG->behat_dataroot)) {
            $CFG->behat_dataroot = realpath($CFG->behat_dataroot);
        }
        if (empty($CFG->behat_dataroot) || !is_dir($CFG->behat_dataroot) || !is_writable($CFG->behat_dataroot)) {
            self::output_msg(get_string('errordataroot', 'tool_behat'));
            return BEHAT_EXITCODE_CONFIG;
        }

        return 0;
    }

    /**
     * Has the site installed composer.
     * @return bool
     */
    public static function are_behat_dependencies_installed() {
        if (!is_dir(__DIR__ . '/../../../../vendor/behat')) {
            return false;
        }
        return true;
    }

    /**
     * Outputs a message.
     *
     * Used in CLI + web UI methods. Stops the
     * execution in web.
     *
     * @param string $msg
     * @return void
     */
    protected static function output_msg($msg) {
        global $CFG, $PAGE;

        // If we are using the web interface we want pretty messages.
        if (!CLI_SCRIPT) {

            $renderer = $PAGE->get_renderer('tool_behat');
            echo $renderer->render_error($msg);

            // Stopping execution.
            exit(1);

        } else {

            // We continue execution after this.
            $clibehaterrorstr = "Ensure you set \$CFG->behat_* vars in config.php " .
                "and you ran admin/tool/behat/cli/init.php.\n" .
                "More info in " . self::DOCS_URL;

            echo 'Error: ' . $msg . "\n\n" . $clibehaterrorstr;
        }
    }

}
