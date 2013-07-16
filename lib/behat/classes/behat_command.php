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
    const DOCS_URL = 'http://docs.moodle.org/dev/Acceptance_testing';

    /**
     * @var Allowed types when using text selectors arguments.
     */
    public static $allowedtextselectors = array(
        'css_element' => 'css_element',
        'xpath_element' => 'xpath_element'
    );

    /**
     * @var Allowed types when using selector arguments.
     */
    public static $allowedselectors = array(
        'link' => 'link',
        'button' => 'button',
        'link_or_button' => 'link_or_button',
        'select' => 'select',
        'checkbox' => 'checkbox',
        'radio' => 'radio',
        'file' => 'file',
        'optgroup' => 'optgroup',
        'option' => 'option',
        'table' => 'table',
        'field' => 'field',
        'fieldset' => 'fieldset',
        'css_element' => 'css_element',
        'xpath_element' => 'xpath_element'
    );

    /**
     * Ensures the behat dir exists in moodledata
     * @return string Full path
     */
    public static function get_behat_dir() {
        global $CFG;

        $behatdir = $CFG->behat_dataroot . '/behat';

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
     * @return string
     */
    public final static function get_behat_command() {
        return 'vendor' . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'behat';
    }

    /**
     * Runs behat command with provided options
     *
     * Execution continues when the process finishes
     *
     * @param  string $options  Defaults to '' so tests would be executed
     * @return array            CLI command outputs [0] => string, [1] => integer
     */
    public final static function run($options = '') {
        global $CFG;

        $currentcwd = getcwd();
        chdir($CFG->dirroot);
        exec(self::get_behat_command() . ' ' . $options, $output, $code);
        chdir($currentcwd);

        return array($output, $code);
    }

    /**
     * Checks if behat is set up and working
     *
     * Uses notice() instead of behat_error() because is
     * also called from web interface
     *
     * It checks behat dependencies have been installed and runs
     * the behat help command to ensure it works as expected
     *
     * @param  bool $checkphp Extra check for the PHP version
     * @return int Error code or 0 if all ok
     */
    public static function behat_setup_problem($checkphp = false) {
        global $CFG;

        // We don't check the PHP version if $CFG->behat_switchcompletely has been enabled.
        // Here we are in CLI.
        if (empty($CFG->behat_switchcompletely) && $checkphp && version_compare(PHP_VERSION, '5.4.0', '<')) {
            behat_error(BEHAT_EXITCODE_REQUIREMENT, 'PHP 5.4 is required. See config-dist.php for possible alternatives');
        }

        $clibehaterrorstr = "Behat dependencies not installed. Ensure you ran the composer installer. " . self::DOCS_URL . "#Installation\n";

        // Moodle setting.
        if (!self::are_behat_dependencies_installed()) {


            // With HTML.
            if (!CLI_SCRIPT) {

                $msg = get_string('wrongbehatsetup', 'tool_behat');
                $docslink = self::DOCS_URL . '#Installation';
                $docslink = html_writer::tag('a', $docslink, array('href' => $docslink, 'target' => '_blank'));
                $msg .= get_string('moreinfoin', 'tool_behat', $docslink);
            } else {
                $msg = $clibehaterrorstr;
            }

            self::output_msg($msg);
            return BEHAT_EXITCODE_COMPOSER;
        }

        // Behat test command.
        list($output, $code) = self::run(' --help');

        if ($code != 0) {
            // Returning composer error code to avoid conflicts with behat and moodle error codes.
            if (!CLI_SCRIPT) {
                $msg = get_string('wrongbehatsetup', 'tool_behat');
            } else {
                $msg = $clibehaterrorstr;
            }
            self::output_msg($msg);
            return BEHAT_EXITCODE_COMPOSER;
        }

        // Checking behat dataroot existence otherwise echo about admin/tool/behat/cli/init.php.
        if (empty($CFG->behat_dataroot) || !is_dir($CFG->behat_dataroot) || !is_writable($CFG->behat_dataroot)) {
            self::output_msg(get_string('runclitool', 'tool_behat', 'php admin/tool/behat/cli/init.php'));
            return BEHAT_EXITCODE_CONFIG;
        }

        return 0;
    }

    /**
     * Has the site installed composer with --dev option
     * @return bool
     */
    public static function are_behat_dependencies_installed() {
        if (!is_dir(__DIR__ . '/../../../vendor/behat')) {
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

        if (!CLI_SCRIPT) {
            // General info about the tool purpose.
            $msg = get_string('aim', 'tool_behat') . '<br /><br />' . $msg;
            notice($msg);
        } else {
            echo $msg;
        }
    }

}
