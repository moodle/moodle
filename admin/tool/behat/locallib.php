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

/**
 * Behat commands manager
 *
 * @package    tool_behat
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_behat {

    private static $behat_tests_path = '/tests/behat';

    /**
     * Displays basic info about acceptance tests
     */
    public static function info() {

        $html = self::get_header();
        $html .= self::get_info();
        $html .= self::get_steps_definitions_form();
        $html .= self::get_footer();

        echo $html;
    }

    /**
     * Lists the available steps definitions
     * @param string $filter Keyword to filter the list of steps definitions availables
     */
    public static function stepsdefinitions($filter = false) {
        global $CFG;

        self::check_behat_setup();

        self::update_config_file();

        // Priority to the one specified as argument.
        if (!$filter) {
            $filter = optional_param('filter', false, PARAM_ALPHANUMEXT);
        }

        if ($filter) {
            $filteroption = ' -d ' . $filter;
        } else {
            $filteroption = ' -di';
        }

        $color = '';
        if (CLI_SCRIPT) {
            $color = '--ansi ';
        }

        $currentcwd = getcwd();
        chdir($CFG->behatpath);
        exec('bin/behat ' . $color . ' --config="' . self::get_behat_config_filepath() . '" ' . $filteroption, $steps, $code);
        chdir($currentcwd);

        // Outputing steps.

        $content = '';
        if ($steps) {
            foreach ($steps as $line) {

                // Skipping the step definition context.
                if (strpos($line, '#') == 0) {
                    if (CLI_SCRIPT) {
                        $content .= $line . PHP_EOL;
                    } else {
                        $content .= htmlentities($line) . '<br/>';
                    }

                }
            }
        }

        if ($content === '') {
            $content = get_string('nostepsdefinitions', 'tool_behat');
        }

        if (!CLI_SCRIPT) {
            $html = self::get_header();
            $html .= self::get_steps_definitions_form($filter);
            $html .= html_writer::tag('div', $content, array('id' => 'steps-definitions'));
            $html .= self::get_footer();
            echo $html;
        } else {
            echo $content;
        }
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
     * It starts test mode and runs the built-in php
     * CLI server and stops it all then it's done
     *
     * @param string $tags Restricts the executed tests to the ones that matches the tags
     * @param string $extra Extra CLI behat options
     */
    public static function runtests($tags = false, $extra = false) {
        global $CFG;

        // Checks that the behat reference is properly set up
        self::check_behat_setup();

        // Check that PHPUnit test environment is correctly set up.
        self::test_environment_problem();

        self::update_config_file();

        @set_time_limit(0);

        // Priority to the one specified as argument.
        if (!$tags) {
            $tags = optional_param('tags', false, PARAM_ALPHANUMEXT);
        }

        $tagsoption = '';
        if ($tags) {
            $tagsoption = ' --tags ' . $tags;
        }

        if (!$extra) {
            $extra = '';
        }

        // Starts built-in server and inits test mode
        self::start_test_mode();
        $server = self::start_test_server();

        // Runs the tests switching the current working directory to CFG->behatpath.
        $currentcwd = getcwd();
        chdir($CFG->behatpath);
        ob_start();
        passthru('bin/behat --ansi --config="' . self::get_behat_config_filepath() .'" ' . $tagsoption . ' ' .$extra, $code);
        $output = ob_get_contents();
        ob_end_clean();
        chdir($currentcwd);

        // Stops built-in server and stops test mode
        self::stop_test_server($server[0], $server[1]);
        self::stop_test_mode();

        // Output.
        echo self::get_header();
        echo $output;
        echo self::get_footer();
    }

    /**
     * Updates the config file
     * @throws file_exception
     */
    private static function update_config_file() {
        global $CFG;

        $behatpath = rtrim($CFG->behatpath, '/');

        // Basic behat dependencies.
        $contents = 'default:
  paths:
    features: ' . $behatpath . '/features
    bootstrap: ' . $behatpath . '/features/bootstrap
  extensions:
    Behat\MinkExtension\Extension:
      base_url: ' . $CFG->test_wwwroot . '
      goutte: ~
      selenium2: ~
    Sanpi\Behatch\Extension:
      contexts:
        browser: ~
        system: ~
        json: ~
        table: ~
    ' . $behatpath . '/vendor/moodlehq/behat-extension/init.php:
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
        $components = tests_finder::get_components_with_tests('stepsdefinitions');
        if ($components) {
            $stepsdefinitions = array('');
            foreach ($components as $componentname => $componentpath) {
                $componentpath = self::clean_path($componentpath);
                $diriterator = new DirectoryIterator($componentpath . self::$behat_tests_path);
                $regite = new RegexIterator($diriterator, '|behat_.*\.php$|');

                // All behat_*.php inside self::$behat_tests_path are added as steps definitions files
                foreach ($regite as $file) {
                    $key = $file->getBasename('.php');
                    $stepsdefinitions[$key] = $key . ': ' . $file->getPathname();
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

        // phpunit --diag returns nothing if the test environment is set up correctly.
        $currentcwd = getcwd();
        chdir($CFG->dirroot . '/' . $CFG->admin . '/tool/phpunit/cli');
        exec("php util.php --diag", $output, $code);
        chdir($currentcwd);

        // If something is not ready stop execution and display the CLI command output.
        if ($code != 0) {
            notice(implode(' ', $output));
        }
    }

    /**
     * Checks if behat is set up and working
     *
     * It checks the behatpath setting value and runs the
     * behat help command to ensure it works as expected
     */
    private static function check_behat_setup() {
        global $CFG;

        // Moodle setting.
        if (empty($CFG->behatpath)) {
            $msg = get_string('nobehatpath', 'tool_behat');
            $url = $CFG->wwwroot . '/' . $CFG->admin . '/settings.php?section=systempaths';

            if (!CLI_SCRIPT) {
                $msg .= ' ' . html_writer::tag('a', get_string('systempaths', 'admin'), array('href' => $url));
            }
            notice($msg);
        }

        // Behat test command.
        $currentcwd = getcwd();
        chdir($CFG->behatpath);
        exec('bin/behat --help', $output, $code);
        chdir($currentcwd);

        if ($code != 0) {
            notice(get_string('wrongbehatsetup', 'tool_behat'));
        }
    }

    /**
     * Enables test mode
     *
     * Stores a file in dataroot/behat to
     * allow Moodle to switch to the test
     * database and dataroot before the initial setup
     *
     * @throws file_exception
     * @return array
     */
    private static function start_test_mode() {
        global $CFG;

        if (self::is_test_mode_enabled()) {
            debugging('Test environment was already enabled');
            return;
        }

        $behatdir = self::get_behat_dir();

        $contents = '$CFG->phpunit_prefix and $CFG->phpunit_dataroot are currently used as $CFG->prefix and $CFG->dataroot';
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

        if (!is_resource($process)) {
            print_error('testservercantrun');
        }

        return array($process, $pipes);
    }

    /**
     * Disables test mode
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
     * It does not return if the current script is running
     * in test environment {@see tool_behat::is_test_environment_running()}
     *
     * @return bool
     */
    private static function is_test_mode_enabled() {

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

        if (!empty($CFG->originaldataroot) && php_sapi_name() === 'cli-server') {
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

    /**
     * Returns header output
     * @return string
     */
    private static function get_header() {
        global $OUTPUT;

        $action = optional_param('action', 'info', PARAM_ALPHAEXT);

        if (CLI_SCRIPT) {
            return '';
        }

        $title = get_string('pluginname', 'tool_behat') . ' - ' . get_string('command' . $action, 'tool_behat');
        $html = $OUTPUT->header();
        $html .= $OUTPUT->heading($title);

        return $html;
    }

    /**
     * Returns footer output
     * @return string
     */
    private static function get_footer() {
        global $OUTPUT;

        if (CLI_SCRIPT) {
            return '';
        }

        return $OUTPUT->footer();
    }

    /**
     * Returns a message and a button to continue if web execution
     * @param string $html
     * @param string $url
     * @return string
     */
    private static function output_success($html, $url = false) {
        global $CFG, $OUTPUT;

        if (!$url) {
            $url = $CFG->wwwroot . '/' . $CFG->admin . '/tool/behat/index.php';
        }

        if (!CLI_SCRIPT) {
            $html = $OUTPUT->box($html, 'generalbox', 'notice');
            $html .= $OUTPUT->continue_button($url);
        }

        return $html;
    }

    /**
     * Returns the installation instructions
     *
     * (hardcoded in English)
     *
     * @return string
     */
    private static function get_info() {
        global $OUTPUT;

        $url = 'http://docs.moodle.org/dev/Acceptance_testing';

        $html = $OUTPUT->box_start();
        $html .= html_writer::tag('h1', 'Info');
        $html .= html_writer::tag('div', 'Follow <a href="' . $url . '" target="_blank">' . $url . '</a> instructions for info about installation and tests execution');
        $html .= $OUTPUT->box_end();

        return $html;
    }

    /**
     * Returns the steps definitions form
     * @param string $filter To filter the steps definitions list by keyword
     * @return string
     */
    private static function get_steps_definitions_form($filter = false) {
        global $OUTPUT;

        if ($filter === false) {
            $filter = '';
        } else {
            $filter = s($filter);
        }

        $html = $OUTPUT->box_start();
        $html .= '<form method="get" action="index.php">';
        $html .= '<fieldset class="invisiblefieldset">';
        $html .= '<label for="id_filter">' . get_string('stepsdefinitions', 'tool_behat') . '</label> ';
        $html .= '<input type="text" id="id_filter" value="' . $filter . '" name="filter"/> (' . get_string('stepsdefinitionsemptyfilter', 'tool_behat') . ')';
        $html .= '<p></p>';
        $html .= '<input type="submit" value="' . get_string('viewsteps', 'tool_behat') . '" />';
        $html .= '<input type="hidden" name="action" value="stepsdefinitions" />';
        $html .= '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
        $html .= '</fieldset>';
        $html .= '</form>';
        $html .= $OUTPUT->box_end();

        return $html;
    }

}
