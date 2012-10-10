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
require_once($CFG->libdir . '/phpunit/classes/util.php');

class tool_behat {

    /**
     * Displays basic info about acceptance tests
     */
    public static function info() {

        $html = tool_behat::get_info();
        $html .= tool_behat::get_steps_definitions_form();
        $html .= tool_behat::get_run_tests_form();

        echo $html;
    }


    /**
     * Lists the available steps definitions
     */
    public static function stepsdefinitions() {
        global $CFG;

        confirm_sesskey();
        tool_behat::check_behat_setup();

        if ($filter = optional_param('filter', false, PARAM_ALPHANUMEXT)) {
            $filteroption = ' -d ' . $filter;
        } else {
            $filteroption = ' -di';
        }

        $currentcwd = getcwd();
        chdir($CFG->behatpath);
        exec('bin/behat' . $filteroption, $steps, $code);
        chdir($currentcwd);

        // Outputing steps.
        $html = tool_behat::get_steps_definitions_form($filter);

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
        echo $html;
    }


    /**
     * Creates a file listing all the moodle with features and steps definitions
     */
    public static function buildconfigfile() {
        // TODO
    }

    /**
     * Runs the acceptance tests
     */
    public static function runtests() {
        global $CFG;

        confirm_sesskey();
        tool_behat::check_behat_setup();

        @set_time_limit(0);

        $tagsoption = '';
        if ($tags = optional_param('tags', false, PARAM_ALPHANUMEXT)) {
            $tagsoption = ' --tags ' . $tags;
        }

        // Switching database and dataroot to test environment.
        tool_behat::enable_test_environment();
        $currentcwd = getcwd();

        // Outputting runner form and tests results.
        echo tool_behat::get_run_tests_form($tags);

        chdir($CFG->behatpath);
        passthru('bin/behat --format html' . $tagsoption, $code);

        // Switching back to regular environment
        tool_behat::disable_test_environment();
        chdir($currentcwd);
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
     * Stores a file in dataroot/behat to allow Moodle swap to
     * test database and dataroot before the initial set up
     *
     * @throws file_exception
     */
    public static function enable_test_environment() {
        global $CFG;

        confirm_sesskey();

        if (tool_behat::is_test_environment_enabled()) {
            debugging('Test environment was already enabled');
            return;
        }

        // Check that PHPUnit test environment is correctly set up.
        tool_behat::test_environment_problem();

        $behatdir = $CFG->dataroot . '/behat';

        if (!is_dir($behatdir)) {
            if (!mkdir($behatdir, $CFG->directorypermissions, true)) {
                throw new file_exception('storedfilecannotcreatefiledirs');
            }
        }

        if (!is_writable($behatdir)) {
            throw new file_exception('storedfilecannotcreatefiledirs');
        }

        $content = '$CFG->phpunit_prefix and $CFG->phpunit_dataroot are currently used as $CFG->prefix and $CFG->dataroot';
        $filepath = $behatdir . '/test_environment_enabled.txt';
        if (!file_put_contents($filepath, $content)) {
            throw new file_exception('cannotcreatefile', $filepath);
        }

    }

    /**
     * Disables test mode
     */
    public static function disable_test_environment() {
        global $CFG;

        confirm_sesskey();

        $testenvfile = $CFG->dataroot . '/behat/test_environment_enabled.txt';

        if (!tool_behat::is_test_environment_enabled()) {
            debugging('Test environment was already disabled');
        } else {
            unlink($testenvfile);
        }
    }

    /**
     * Checks whether test environment is enabled or disabled
     */
    public static function is_test_environment_enabled() {
        global $CFG;

        $testenvfile = $CFG->dataroot . '/behat/test_environment_enabled.txt';
        if (file_exists($testenvfile)) {
            return true;
        }

        return false;
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

        $html = $OUTPUT->box_start();
        $html .= html_writer::tag('h1', 'Info');
        $info = 'This tool makes use of the phpunit test environment, during the automatic acceptance tests execution the site uses the test database and dataroot directories so this tool IS NOT intended to be used in production sites.';
        $html .= html_writer::tag('div', $info);

        $html .= html_writer::tag('h1', 'Installation');
        $installinstructions = '1.- Follow the PHPUnit installation instructions to set up the testing environment $CFG->wwwroot/admin/tool/phpunit/index.php<br/>';
        $installinstructions .= '2.- Follow the moodle-acceptance-test installation instructions https://github.com/dmonllao/behat-moodle<br/>';
        $installinstructions .= '3.- Set up the \'config_file_path\' param in /MOODLE-ACCEPTANCE-TEST/ROOT/PATH/behat.yml pointing to $CFG->dataroot/behat/config.yml (for example /YOUR/MOODLEDATA/PATH/behat/config.yml)<br/>';
        $installinstructions .= '4.- Set up $CFG->behatpath in your config.php with the path of your moodle-acceptance-test installation (for example /MOODLE-ACCEPTANCE-TEST/ROOT/PATH)<br/>';
        $html .= html_writer::tag('div', $installinstructions);
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
        $html .= '<p>';
        $html .= '<fieldset class="invisiblefieldset">';
        $html .= '<label for="id_filter">Steps definitions which contains</label> ';
        $html .= '<input type="text" id="id_filter" value="' . $filter . '" name="filter"/> (all steps definitions if empty)';
        $html .= '</p>';
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
        $html .= '<p>';
        $html .= '<fieldset class="invisiblefieldset">';
        $html .= '<label for="id_tags">Tests tagged as</label> ';
        $html .= '<input type="text" id="id_tags" value="' . $tags . '" name="tags"/> (all available tests if empty)';
        $html .= '</p>';
        $html .= '<input type="submit" value="Run tests" />';
        $html .= '<input type="hidden" name="action" value="runtests" />';
        $html .= '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
        $html .= '</fieldset>';
        $html .= '</form>';
        $html .= $OUTPUT->box_end();

        return $html;
    }

}
