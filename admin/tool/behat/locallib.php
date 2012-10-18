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
 * CLI + web execution
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
        $html .= self::get_switch_environment_form();

        if (!self::is_test_environment_running()) {
            $html .= self::get_steps_definitions_form();
            $html .= self::get_run_tests_form();
        }

        $html .= self::get_footer();

        echo $html;
    }

    /**
     * Lists the available steps definitions
     * @param string $filter Keyword to filter the list of steps definitions availables
     */
    public static function stepsdefinitions($filter = false) {
        global $CFG;

        if (!CLI_SCRIPT) {
            confirm_sesskey();
        }
        self::check_behat_setup();

        // Priority to the one specified as argument.
        if (!$filter) {
            $filter = optional_param('filter', false, PARAM_ALPHANUMEXT);
        }

        if ($filter) {
            $filteroption = ' -d ' . $filter;
        } else {
            $filteroption = ' -di';
        }

        $currentcwd = getcwd();
        chdir($CFG->behatpath);
        exec('bin/behat' . $filteroption, $steps, $code);
        chdir($currentcwd);

        // Outputing steps.
        $html = self::get_header();
        $html .= self::get_steps_definitions_form($filter);

        $content = '';
        if ($steps) {
            foreach ($steps as $line) {

                // Skipping the step definition context.
                if (strpos($line, '#') == 0) {
                    $content .= htmlentities($line) . '<br/>';
                }
            }
        }

        if ($content === '') {
            $content = get_string('nostepsdefinitions', 'tool_behat');
        }

        $html .= html_writer::tag('div', $content, array('id' => 'steps-definitions'));
        $html .= self::get_footer();

        echo $html;
    }

    /**
     * Switches from and to the regular environment to the testing environment
     * @param string $testenvironment enable|disable
     */
    public static function switchenvironment($testenvironment = false) {
        global $CFG;

        if (!CLI_SCRIPT) {
            confirm_sesskey();
        }

        // Priority to the one specified as argument.
        if (!$testenvironment) {
            $testenvironment = optional_param('testenvironment', 'enable', PARAM_ALPHA);
        }

        if ($testenvironment == 'enable') {
            self::enable_test_environment();
        } else if ($testenvironment == 'disable') {
            self::disable_test_environment();
        }

        if (!CLI_SCRIPT) {
            redirect(get_login_url());
        }
    }

    /**
     * Runs the acceptance tests
     * @param string $tags Restricts the executed tests to the ones that matches the tags
     * @param string $extra Extra CLI behat options
     */
    public static function runtests($tags = false, $extra = false) {
        global $CFG;

        if (!CLI_SCRIPT) {
            confirm_sesskey();
        }
        self::check_behat_setup();

        self::update_config_file();

        @set_time_limit(0);

        // Priority to the one specified as argument.
        if (!$tags) {
            $tags = optional_param('tags', false, PARAM_ALPHANUMEXT);
        }
        // $extra only passed as CLI option (to simplify web runner usage).

        $tagsoption = '';
        if ($tags) {
            $tagsoption = ' --tags ' . $tags;
        }

        if (!$extra && !CLI_SCRIPT) {
            $extra = ' --format html';
        } else if(!$extra && CLI_SCRIPT) {
            $extra = '';
        }

        // Switching database and dataroot to test environment.
        self::enable_test_environment();
        $currentcwd = getcwd();

        chdir($CFG->behatpath);
        ob_start();
        passthru('bin/behat --ansi ' . $tagsoption . ' ' .$extra, $code);
        $output = ob_get_contents();
        ob_end_clean();

        // Switching back to regular environment.
        self::disable_test_environment();
        chdir($currentcwd);

        // Output.
        echo self::get_header();
        if (!CLI_SCRIPT) {
            echo self::get_run_tests_form($tags);
        }
        echo $output;

        // Dirty hack to avoid behat bad HTML structure when test execution throws an exception and there are skipped steps.
        if (strstr($output, 'class="skipped"') != false) {
            echo '</ol></div></div></div></body></html>';
        }

        echo self::get_footer();
    }

    /**
     * Updates the config file
     * @throws file_exception
     */
    private static function update_config_file() {

        $contents = '';

        // Gets all the components with features.
        $components = tests_finder::get_components_with_tests('features');
        if ($components) {
            $contents .= 'features:' . PHP_EOL;
            foreach ($components as $componentname => $path) {
                $path = self::clean_path($path) . self::$behat_tests_path;
                $contents .= '  - ' . $path . PHP_EOL;
            }
        }

        // Gets all the components with steps definitions.
        $components = tests_finder::get_components_with_tests('stepsdefinitions');
        if ($components) {
            $contents .= 'steps_definitions:' . PHP_EOL;
            foreach ($components as $componentname => $filepath) {
                $filepath = self::clean_path($path) . self::$behat_tests_path . '/behat_' . $componentname . '.php';
                $contents .= '  ' . $componentname . ': ' . $filepath . PHP_EOL;
            }
        }

        // Stores the file.
        $fullfilepath = self::get_behat_dir() . '/config.yml';
        if (!file_put_contents($fullfilepath, $contents)) {
            throw new file_exception('cannotcreatefile', $fullfilepath);
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
     * Checks the behat setup
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
     * Enables test mode checking the test environment setup
     *
     * Stores a file in dataroot/behat to allow Moodle switch to
     * test database and dataroot before the initial set up
     *
     * @throws file_exception
     */
    private static function enable_test_environment() {
        global $CFG;

        if (self::is_test_environment_enabled()) {
            debugging('Test environment was already enabled');
            return;
        }

        // Check that PHPUnit test environment is correctly set up.
        self::test_environment_problem();

        $behatdir = self::get_behat_dir();

        $contents = '$CFG->phpunit_prefix and $CFG->phpunit_dataroot are currently used as $CFG->prefix and $CFG->dataroot';
        $filepath = $behatdir . '/test_environment_enabled.txt';
        if (!file_put_contents($filepath, $contents)) {
            throw new file_exception('cannotcreatefile', $filepath);
        }
        chmod($filepath, $CFG->directorypermissions);
    }

    /**
     * Disables test mode
     */
    private static function disable_test_environment() {

        $testenvfile = self::get_test_filepath();

        if (!self::is_test_environment_enabled()) {
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
     * It does not return if the current script is running
     * in test environment {@see tool_behat::is_test_environment_running()}
     *
     * @return bool
     */
    private static function is_test_environment_enabled() {

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

        if (!empty($CFG->originaldataroot)) {
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

        $url = 'http://docs.moodle.org/dev/Behat_integration#Installation';

        $html = $OUTPUT->box_start();
        $html .= html_writer::tag('h1', 'Installation info');
        $html .= html_writer::tag('div', 'Follow <a href="' . $url . '" target="_blank">' . $url . '</a> instructions');
        $html .= $OUTPUT->box_end();

        return $html;
    }

    /**
     * Returns a button to switch between environments
     * @return string
     */
    private static function get_switch_environment_form() {
        global $CFG, $OUTPUT;

        if (self::is_test_environment_running()) {
            $perform = 'disable';
        } else {
            $perform = 'enable';
        }
        $params = array('action' => 'switchenvironment', 'testenvironment' => $perform, 'sesskey' => sesskey());
        $url = new moodle_url('/' . $CFG->admin . '/tool/behat/index.php', $params);

        $html = $OUTPUT->box_start();
        $html .= '<p>' . get_string('switchenvironmentinfo', 'tool_behat') . '</p>';
        $html .= $OUTPUT->single_button($url, get_string('switchenvironment' . $perform, 'tool_behat'));
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
        $html .= '<label for="id_filter">Steps definitions which contains</label> ';
        $html .= '<input type="text" id="id_filter" value="' . $filter . '" name="filter"/> (all steps definitions if empty)';
        $html .= '<p></p>';
        $html .= '<input type="submit" value="View available steps definitions" />';
        $html .= '<input type="hidden" name="action" value="stepsdefinitions" />';
        $html .= '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
        $html .= '</fieldset>';
        $html .= '</form>';
        $html .= $OUTPUT->box_end();

        return $html;
    }

    /**
     * Returns the run tests form
     * @param string $tags To execute only the tests that matches the specified tag
     * @return string
     */
    private static function get_run_tests_form($tags = false) {
        global $OUTPUT;

        if ($tags === false) {
            $tags = '';
        } else {
            $tags = s($tags);
        }

        $html = $OUTPUT->box_start();
        $html .= '<form method="get" action="index.php">';
        $html .= '<fieldset class="invisiblefieldset">';
        $html .= '<label for="id_tags">Tests tagged as</label> ';
        $html .= '<input type="text" id="id_tags" value="' . $tags . '" name="tags"/> (all available tests if empty)';
        $html .= '<p></p>';
        $html .= '<input type="submit" value="Run tests" />';
        $html .= '<input type="hidden" name="action" value="runtests" />';
        $html .= '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
        $html .= '</fieldset>';
        $html .= '</form>';
        $html .= $OUTPUT->box_end();

        return $html;
    }
}
