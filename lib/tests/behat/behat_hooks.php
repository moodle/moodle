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
 * Behat hooks steps definitions.
 *
 * This methods are used by Behat CLI command.
 *
 * @package    core
 * @category   test
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../behat/behat_base.php');

use Behat\Behat\Event\SuiteEvent as SuiteEvent;
use Behat\Behat\Event\ScenarioEvent as ScenarioEvent;
use Behat\Behat\Event\StepEvent as StepEvent;

/**
 * Hooks to the behat process.
 *
 * Behat accepts hooks after and before each
 * suite, feature, scenario and step.
 *
 * They can not call other steps as part of their process
 * like regular steps definitions does.
 *
 * Throws generic Exception because they are captured by Behat.
 *
 * @package   core
 * @category  test
 * @copyright 2012 David Monllaó
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_hooks extends behat_base {

    /**
     * Gives access to moodle codebase, ensures all is ready and sets up the test lock.
     *
     * Includes config.php to use moodle codebase with $CFG->behat_*
     * instead of $CFG->prefix and $CFG->dataroot, called once per suite.
     *
     * @static
     * @throws Exception
     * @BeforeSuite
     */
    public static function before_suite($event) {
        global $CFG;

        // To work with behat_dataroot and behat_prefix instead of the regular environment.
        define('BEHAT_RUNNING', 1);
        define('CLI_SCRIPT', 1);

        // With BEHAT_RUNNING we will be using $CFG->behat_* instead of $CFG->dataroot, $CFG->prefix and $CFG->wwwroot.
        require_once(__DIR__ . '/../../../config.php');

        // Now that we are MOODLE_INTERNAL.
        require_once(__DIR__ . '/../../behat/classes/behat_command.php');
        require_once(__DIR__ . '/../../behat/classes/util.php');
        require_once(__DIR__ . '/../../testing/classes/test_lock.php');
        require_once(__DIR__ . '/../../testing/classes/nasty_strings.php');

        // Avoids vendor/bin/behat to be executed directly without test environment enabled
        // to prevent undesired db & dataroot modifications, this is also checked
        // before each scenario (accidental user deletes) in the BeforeScenario hook.

        if (!behat_util::is_test_mode_enabled()) {
            throw new Exception('Behat only can run if test mode is enabled. More info in ' . behat_command::DOCS_URL . '#Running_tests');
        }

        if (!behat_util::is_server_running()) {
            throw new Exception($CFG->behat_wwwroot . ' is not available, ensure you started your PHP built-in server. More info in ' . behat_command::DOCS_URL . '#Running_tests');
        }

        // Prevents using outdated data, upgrade script would start and tests would fail.
        if (!behat_util::is_test_data_updated()) {
            $commandpath = 'php admin/tool/behat/cli/util.php';
            throw new Exception('Your behat test site is outdated, please run ' . $commandpath . ' from your moodle dirroot to drop and install the behat test site again.');
        }
        // Avoid parallel tests execution, it continues when the previous lock is released.
        test_lock::acquire('behat');
    }

    /**
     * Resets the test environment.
     *
     * @throws coding_exception If here we are not using the test database it should be because of a coding error
     * @BeforeScenario
     */
    public function before_scenario($event) {
        global $DB, $SESSION, $CFG;

        // As many checks as we can.
        if (!defined('BEHAT_RUNNING') ||
               php_sapi_name() != 'cli' ||
               !behat_util::is_test_mode_enabled() ||
               !behat_util::is_test_site() ||
               !isset($CFG->originaldataroot)) {
            throw new coding_exception('Behat only can modify the test database and the test dataroot!');
        }

        // Avoid some notices / warnings.
        $SESSION = new stdClass();

        behat_util::reset_database();
        behat_util::reset_dataroot();

        purge_all_caches();
        accesslib_clear_all_caches(true);

        // Reset the nasty strings list used during the last test.
        nasty_strings::reset_used_strings();

        // Assing valid data to admin user (some generator-related code needs a valid user).
        $user = $DB->get_record('user', array('username' => 'admin'));
        session_set_user($user);
    }

    /**
     * Ensures selenium is running.
     *
     * Is only executed in scenarios which requires Javascript to run,
     * it returns a direct error message about what's going on.
     *
     * @throws Exception
     * @BeforeScenario @javascript
     */
    public function before_scenario_javascript($event) {

        // Just trying if server responds.
        try {
            $this->getSession()->executeScript('// empty comment');
        } catch (Exception $e) {
            $moreinfo = 'More info in ' . behat_command::DOCS_URL . '#Running_tests';
            $msg = 'Selenium server is not running, you need to start it to run tests that involves Javascript. ' . $moreinfo;
            throw new Exception($msg);
        }
    }

    /**
     * Checks that all DOM is ready.
     *
     * Executed only when running against a real browser.
     *
     * @AfterStep @javascript
     */
    public function after_step_javascript($event) {

        // If it doesn't have definition or it fails there is no need to check it.
        if ($event->getResult() != StepEvent::PASSED ||
            !$event->hasDefinition()) {
            return;
        }

        // Wait until the page is ready.
        $this->getSession()->wait(self::TIMEOUT, '(document.readyState === "complete")');
    }

}
