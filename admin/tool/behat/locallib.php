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
 * Behat commands
 *
 * @package    tool_behat
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir . '/filestorage/file_exceptions.php');
require_once($CFG->libdir . '/phpunit/bootstraplib.php');
require_once($CFG->libdir . '/phpunit/classes/tests_finder.php');

require_once($CFG->dirroot . '/' . $CFG->admin . '/tool/behat/steps_definitions_form.php');

/**
 * Behat commands manager
 *
 * @package    tool_behat
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_behat {

    /**
     * Path where each component's tests are stored
     * @var string
     */
    private static $behat_tests_path = '/tests/behat';

    /**
     * Steps types
     * @var array
     */
    private static $steps_types = array('given', 'when', 'then');

    /** @var string Docu url */
    public static $docsurl = 'http://docs.moodle.org/dev/Acceptance_testing';

    /**
     * Lists the available steps definitions
     *
     * @param string $type
     * @param string $component
     * @param string $filter
     * @return string
     */
    public static function stepsdefinitions($type, $component, $filter) {
        global $CFG;

        self::check_behat_setup();

        // The loaded steps depends on the component specified.
        self::update_config_file($component);

        // The Moodle\BehatExtension\HelpPrinter\MoodleDefinitionsPrinter will parse this search format.
        if ($type) {
            $filter .= '&&' . $type;
        }

        if ($filter) {
            $filteroption = ' -d "' . $filter . '"';
        } else {
            $filteroption = ' -di';
        }

        $currentcwd = getcwd();
        chdir($CFG->dirroot . '/lib/behat');
        exec('bin/behat --config="' . self::get_behat_config_filepath() . '" ' . $filteroption, $steps, $code);
        chdir($currentcwd);

        if ($steps) {
            $stepshtml = implode('', $steps);
        }

        if (!isset($stepshtml) || $stepshtml == '') {
            $stepshtml = get_string('nostepsdefinitions', 'tool_behat');
        }

        return $stepshtml;
    }

    /**
     * Allows / disables the test environment to be accesses through the built-in server
     *
     * Built-in server must be started separately
     *
     * @param string $testenvironment enable|disable
     */
    public static function switchenvironment($testenvironment = false) {
        global $CFG;

        // Priority to the one specified as argument.
        if (!$testenvironment) {
            $testenvironment = optional_param('testenvironment', 'enable', PARAM_ALPHA);
        }

        if ($testenvironment == 'enable') {
            self::start_test_mode();
        } else if ($testenvironment == 'disable') {
            self::stop_test_mode();
        }
    }

    /**
     * Runs the acceptance tests
     *
     * It starts test mode and runs the built-in PHP
     * erver and stops it all then it's done
     *
     * @param boolean $withjavascript Include tests with javascript
     * @param string $tags Restricts the executed tests to the ones that matches the tags
     * @param string $extra Extra CLI behat options
     */
    public static function runtests($withjavascript = false, $tags = false, $extra = '') {
        global $CFG;

        // Checks the behat set up and the PHP version.
        self::check_behat_setup(true);

        // Check that PHPUnit test environment is correctly set up.
        self::test_environment_problem();

        // Updates all the Moodle features and steps definitions.
        self::update_config_file();

        @set_time_limit(0);

        // No javascript by default.
        if (!$withjavascript && strstr($tags, 'javascript') == false) {
            $jsstr = '~@javascript';
        }

        // Adding javascript option to --tags.
        $tagsoption = '';
        if ($tags) {
            if (!empty($jsstr)) {
                $tags .= '&&' . $jsstr;
            }
            $tagsoption = " --tags '" . $tags . "'";

        // No javascript by default.
        } else if (!empty($jsstr)) {
            $tagsoption = " --tags '" . $jsstr . "'";
        }

        // Starts built-in server and inits test mode.
        self::start_test_mode();
        $server = self::start_test_server();

        // Runs the tests switching the current working directory to behat path.
        $currentcwd = getcwd();
        chdir($CFG->dirroot . '/lib/behat');
        ob_start();
        passthru('bin/behat --ansi --config="' . self::get_behat_config_filepath() .'" ' . $tagsoption . ' ' .$extra, $code);
        $output = ob_get_contents();
        ob_end_clean();
        chdir($currentcwd);

        // Stops built-in server and stops test mode.
        self::stop_test_server($server[0], $server[1]);
        self::stop_test_mode();

        // Output.
        echo $output;
    }

    /**
     * Updates the config file
     * @param string $component Restricts the obtained steps definitions to the specified component
     * @throws file_exception
     */
    private static function update_config_file($component = '') {
        global $CFG;

        $behatpath = $CFG->dirroot . '/lib/behat';

        // Behat config file specifing the main context class,
        // the required Behat extensions and Moodle test wwwroot.
        $contents = 'default:
  paths:
    features: ' . $behatpath . '/features
    bootstrap: ' . $behatpath . '/features/bootstrap
  context:
    class: behat_init_context
  extensions:
    Behat\MinkExtension\Extension:
      base_url: ' . $CFG->test_wwwroot . '
      goutte: ~
      selenium2: ~
    ' . $CFG->dirroot . '/vendor/moodlehq/behat-extension/init.php:
';

        // Gets all the components with features.
        $components = tests_finder::get_components_with_tests('features');
        if ($components) {
            $featurespaths = array('');
            foreach ($components as $componentname => $path) {
                $path = self::clean_path($path) . self::$behat_tests_path;
                if (empty($featurespaths[$path]) && file_exists($path)) {
                    $featurespaths[$path] = $path;
                }
            }
            $contents .= '      features:' . implode(PHP_EOL . '        - ', $featurespaths) . PHP_EOL;
        }


        // Gets all the components with steps definitions.
        $steps = self::get_components_steps_definitions();
        if ($steps) {
            $stepsdefinitions = array('');
            foreach ($steps as $key => $filepath) {
                if ($component == '' || $component === $key) {
                    $stepsdefinitions[$key] = $key . ': ' . $filepath;
                }
            }
            $contents .= '      steps_definitions:' . implode(PHP_EOL . '        ', $stepsdefinitions) . PHP_EOL;
        }

        // Stores the file.
        if (!file_put_contents(self::get_behat_config_filepath(), $contents)) {
            throw new file_exception('cannotcreatefile', self::get_behat_config_filepath());
        }

    }

    /**
     * Gets the list of Moodle steps definitions
     *
     * Class name as a key and the filepath as value
     *
     * Externalized from update_config_file() to use
     * it from the steps definitions web interface
     *
     * @return array
     */
    public static function get_components_steps_definitions() {

        $components = tests_finder::get_components_with_tests('stepsdefinitions');
        if (!$components) {
            return false;
        }

        $stepsdefinitions = array();
        foreach ($components as $componentname => $componentpath) {
            $componentpath = self::clean_path($componentpath);
            $diriterator = new DirectoryIterator($componentpath . self::$behat_tests_path);
            $regite = new RegexIterator($diriterator, '|behat_.*\.php$|');

            // All behat_*.php inside self::$behat_tests_path are added as steps definitions files.
            foreach ($regite as $file) {
                $key = $file->getBasename('.php');
                $stepsdefinitions[$key] = $file->getPathname();
            }
        }

        return $stepsdefinitions;
    }


    /**
     * Cleans the path returned by get_components_with_tests() to standarize it
     *
     * {@see tests_finder::get_all_directories_with_tests()} it returns the path including /tests/
     * @param string $path
     * @return string The string without the last /tests part
     */
    private static function clean_path($path) {

        $path = rtrim($path, '/');

        $parttoremove = '/tests';

        $substr = substr($path, strlen($path) - strlen($parttoremove));
        if ($substr == $parttoremove) {
            $path = substr($path, 0, strlen($path) - strlen($parttoremove));
        }

        return rtrim($path, '/');
    }

    /**
     * Checks whether the test database and dataroot is ready
     * Stops execution if something went wrong
     */
    private static function test_environment_problem() {
        global $CFG;

        // PHPUnit --diag returns nothing if the test environment is set up correctly.
        exec('php ' . $CFG->dirroot . '/' . $CFG->admin . '/tool/phpunit/cli/util.php --diag', $output, $code);

        // If something is not ready stop execution and display the CLI command output.
        if ($code != 0) {
            notice(get_string('phpunitenvproblem', 'tool_behat') . ': ' . implode(' ', $output));
        }
    }

    /**
     * Checks if behat is set up and working
     *
     * It checks behat dependencies have been installed and runs
     * the behat help command to ensure it works as expected
     * @param boolean $checkphp Extra check for the PHP version
     */
    private static function check_behat_setup($checkphp = false) {
        global $CFG;

        if ($checkphp && version_compare(PHP_VERSION, '5.4.0', '<')) {
            throw new Exception(get_string('wrongphpversion', 'tool_behat'));
        }

        // Moodle setting.
        if (!is_dir($vendor = __DIR__ . '/../../../vendor/behat')) {

            $msg = get_string('wrongbehatsetup', 'tool_behat');

            // With HTML.
            $docslink = tool_behat::$docsurl . '#Installation';
            if (!CLI_SCRIPT) {
                $docslink = html_writer::tag('a', $docslink, array('href' => $docslink, 'target' => '_blank'));
            }
            $msg .= '. ' . get_string('moreinfoin', 'tool_behat') . ' ' . $docslink;
            notice($msg);
        }

        // Behat test command.
        $currentcwd = getcwd();
        chdir($CFG->dirroot . '/lib/behat');
        exec('bin/behat --help', $output, $code);
        chdir($currentcwd);

        if ($code != 0) {
            notice(get_string('wrongbehatsetup', 'tool_behat'));
        }
    }

    /**
     * Enables test mode
     *
     * Stores a file in dataroot/behat to allow Moodle to switch
     * to the test environment when using cli-server
     *
     * @throws file_exception
     */
    private static function start_test_mode() {
        global $CFG;

        if (self::is_test_mode_enabled()) {
            debugging('Test environment was already enabled');
            return;
        }

        $behatdir = self::get_behat_dir();

        $contents = '$CFG->test_wwwroot, $CFG->phpunit_prefix and $CFG->phpunit_dataroot' .
            ' are currently used as $CFG->wwwroot, $CFG->prefix and $CFG->dataroot';
        $filepath = $behatdir . '/test_environment_enabled.txt';
        if (!file_put_contents($filepath, $contents)) {
            throw new file_exception('cannotcreatefile', $filepath);
        }
        chmod($filepath, $CFG->directorypermissions);
    }

    /**
     * Runs the php built-in server
     * @return array The process running the server and the pipes array
     */
    private static function start_test_server() {
        global $CFG;

        $descriptorspec = array(
            array("pipe", "r"),
            array("pipe", "w"),
            array("pipe", "a")
        );

        $server = str_replace('http://', '', $CFG->test_wwwroot);
        $process = proc_open('php -S ' . $server, $descriptorspec, $pipes, $CFG->dirroot);

        // TODO If it's already started close pipes.

        if (!is_resource($process)) {
            print_error('testservercantrun');
        }

        return array($process, $pipes);
    }

    /**
     * Disables test mode
     * @throws file_exception
     */
    private static function stop_test_mode() {

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
     * Stops the built-in server
     *
     * @param resource $process
     * @param array $pipes IN, OUT and error pipes
     */
    private static function stop_test_server($process, $pipes) {

        if (is_resource($process)) {

            // Closing pipes.
            fclose($pipes[0]);
            fclose($pipes[1]);
            fclose($pipes[2]);

            // Closing process.
            proc_terminate($process);
            proc_close($process);
        }
    }

    /**
     * Checks whether test environment is enabled or disabled
     *
     * To check is the current script is running in the test
     * environment {@see tool_behat::is_test_environment_running()}
     *
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
    private static function is_test_environment_running() {
        global $CFG;

        if (!empty($CFG->originaldataroot) || defined('BEHAT_RUNNING')) {
            return true;
        }

        return false;
    }

    /**
     * Returns the path to the file which specifies if test environment is enabled
     * @return string
     */
    private static function get_test_filepath() {
        global $CFG;

        if (self::is_test_environment_running()) {
            $testenvfile = $CFG->originaldataroot . '/behat/test_environment_enabled.txt';
        } else {
            $testenvfile = $CFG->dataroot . '/behat/test_environment_enabled.txt';
        }

        return $testenvfile;
    }


    /**
     * Ensures the behat dir exists in moodledata
     * @throws file_exception
     * @return string Full path
     */
    private static function get_behat_dir() {
        global $CFG;

        $behatdir = $CFG->dataroot . '/behat';

        if (!is_dir($behatdir)) {
            if (!mkdir($behatdir, $CFG->directorypermissions, true)) {
                throw new file_exception('storedfilecannotcreatefiledirs');
            }
        }

        if (!is_writable($behatdir)) {
            throw new file_exception('storedfilecannotcreatefiledirs');
        }

        return $behatdir;
    }

    /**
     * Returns the behat config file path
     * @return string
     */
    private static function get_behat_config_filepath() {
        return self::get_behat_dir() . '/behat.yml';
    }

}
