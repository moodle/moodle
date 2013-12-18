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

use Behat\Behat\Event\SuiteEvent as SuiteEvent,
    Behat\Behat\Event\ScenarioEvent as ScenarioEvent,
    Behat\Behat\Event\StepEvent as StepEvent,
    WebDriver\Exception\NoSuchWindow as NoSuchWindow,
    WebDriver\Exception\UnexpectedAlertOpen as UnexpectedAlertOpen,
    WebDriver\Exception\UnknownError as UnknownError,
    WebDriver\Exception\CurlExec as CurlExec,
    WebDriver\Exception\NoAlertOpenError as NoAlertOpenError;

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
     * @var Last browser session start time.
     */
    protected static $lastbrowsersessionstart = 0;

    /**
     * @var For actions that should only run once.
     */
    protected static $initprocessesfinished = false;

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

        // Defined only when the behat CLI command is running, the moodle init setup process will
        // read this value and switch to $CFG->behat_dataroot and $CFG->behat_prefix instead of
        // the normal site.
        define('BEHAT_TEST', 1);

        define('CLI_SCRIPT', 1);

        // With BEHAT_TEST we will be using $CFG->behat_* instead of $CFG->dataroot, $CFG->prefix and $CFG->wwwroot.
        require_once(__DIR__ . '/../../../config.php');

        // Now that we are MOODLE_INTERNAL.
        require_once(__DIR__ . '/../../behat/classes/behat_command.php');
        require_once(__DIR__ . '/../../behat/classes/behat_selectors.php');
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
            throw new Exception($CFG->behat_wwwroot .
                ' is not available, ensure you started your PHP built-in server or your web server is correctly started and set up.' .
                ' More info in ' . behat_command::DOCS_URL . '#Running_tests');
        }

        // Prevents using outdated data, upgrade script would start and tests would fail.
        if (!behat_util::is_test_data_updated()) {
            $commandpath = 'php admin/tool/behat/cli/init.php';
            throw new Exception('Your behat test site is outdated, please run ' . $commandpath . ' from your moodle dirroot to drop and install the behat test site again.');
        }
        // Avoid parallel tests execution, it continues when the previous lock is released.
        test_lock::acquire('behat');

        // Store the browser reset time if reset after N seconds is specified in config.php.
        if (!empty($CFG->behat_restart_browser_after)) {
            // Store the initial browser session opening.
            self::$lastbrowsersessionstart = time();
        }
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
        if (!defined('BEHAT_TEST') ||
               !defined('BEHAT_SITE_RUNNING') ||
               php_sapi_name() != 'cli' ||
               !behat_util::is_test_mode_enabled() ||
               !behat_util::is_test_site()) {
            throw new coding_exception('Behat only can modify the test database and the test dataroot!');
        }

        try {
            $session = $this->getSession();
        } catch (CurlExec $e) {
            // Exception thrown by WebDriver, so only @javascript tests will be caugth; in
            // behat_util::is_server_running() we already checked that the server is running.
            $moreinfo = 'More info in ' . behat_command::DOCS_URL . '#Running_tests';
            $msg = 'Selenium server is not running, you need to start it to run tests that involve Javascript. ' . $moreinfo;
            throw new Exception($msg);
        } catch (UnknownError $e) {
            // Generic 'I have no idea' Selenium error. Custom exception to provide more feedback about possible solutions.
            $this->throw_unknown_exception($e);
        }


        // We need the Mink session to do it and we do it only before the first scenario.
        if (self::is_first_scenario()) {
            behat_selectors::register_moodle_selectors($session);
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

        // Reset the browser if specified in config.php.
        if (!empty($CFG->behat_restart_browser_after) && $this->running_javascript()) {
            $now = time();
            if (self::$lastbrowsersessionstart + $CFG->behat_restart_browser_after < $now) {
                $session->restart();
                self::$lastbrowsersessionstart = $now;
            }
        }

        // Start always in the the homepage.
        try {
            // Let's be conservative as we never know when new upstream issues will affect us.
            $session->visit($this->locate_path('/'));
        } catch (UnknownError $e) {
            $this->throw_unknown_exception($e);
        }


        // Checking that the root path is a Moodle test site.
        if (self::is_first_scenario()) {
            $notestsiteexception = new Exception('The base URL (' . $CFG->wwwroot . ') is not a behat test site, ' .
                'ensure you started the built-in web server in the correct directory or your web server is correctly started and set up');
            $this->find("xpath", "//head/child::title[normalize-space(.)='" . behat_util::BEHATSITENAME . "']", $notestsiteexception);

            self::$initprocessesfinished = true;
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
       // We are already checking that we use a JS browser, this could
       // change in case we use another JS driver.
       try {

            // Safari and Internet Explorer requires time between steps,
            // otherwise Selenium tries to click in the previous page's DOM.
            if ($this->getSession()->getDriver()->getBrowserName() == 'safari' ||
                    $this->getSession()->getDriver()->getBrowserName() == 'internet explorer') {
                $this->getSession()->wait(self::TIMEOUT * 1000, false);

            } else {
                // With other browsers we just wait for the DOM ready.
                $this->getSession()->wait(self::TIMEOUT * 1000, '(document.readyState === "complete")');
            }

        } catch (NoSuchWindow $e) {
            // If we were interacting with a popup window it will not exists after closing it.
        } catch (UnknownError $e) {
            // Custom exception to provide more feedback about possible solutions.
            $this->throw_unknown_exception($e);
        }
    }

    /**
     * Internal step definition to find exceptions, debugging() messages and PHP debug messages.
     *
     * Part of behat_hooks class as is part of the testing framework, is auto-executed
     * after each step so no features will splicitly use it.
     *
     * @Given /^I look for exceptions$/
     * @see Moodle\BehatExtension\Tester\MoodleStepTester
     */
    public function i_look_for_exceptions() {

        // Wrap in try in case we were interacting with a closed window.
        try {

            // Exceptions.
            $exceptionsxpath = "//*[contains(concat(' ', normalize-space(@class), ' '), ' errorbox ')]" .
                "/descendant::p[contains(concat(' ', normalize-space(@class), ' '), ' errormessage ')]";
            // Debugging messages.
            $debuggingxpath = "//*[contains(concat(' ', normalize-space(@class), ' '), ' debuggingmessage ')]";
            // PHP debug messages.
            $phperrorxpath = "//*[contains(concat(' ', normalize-space(@class), ' '), ' phpdebugmessage ')]";
            // Any other backtrace.
            $othersxpath = "(//*[contains(., ': call to ')])[1]";

            $xpaths = array($exceptionsxpath, $debuggingxpath, $phperrorxpath, $othersxpath);
            $joinedxpath = implode(' | ', $xpaths);

            // Joined xpath expression. Most of the time there will be no exceptions, so this pre-check
            // is faster than to send the 4 xpath queries for each step.
            if (!$this->getSession()->getDriver()->find($joinedxpath)) {
                return;
            }

            // Exceptions.
            if ($errormsg = $this->getSession()->getPage()->find('xpath', $exceptionsxpath)) {

                // Getting the debugging info and the backtrace.
                $errorinfoboxes = $this->getSession()->getPage()->findAll('css', 'div.notifytiny');
                $errorinfo = $this->get_debug_text($errorinfoboxes[0]->getHtml()) . "\n" .
                    $this->get_debug_text($errorinfoboxes[1]->getHtml());

                $msg = "Moodle exception: " . $errormsg->getText() . "\n" . $errorinfo;
                throw new \Exception(html_entity_decode($msg));
            }

            // Debugging messages.
            if ($debuggingmessages = $this->getSession()->getPage()->findAll('xpath', $debuggingxpath)) {
                $msgs = array();
                foreach ($debuggingmessages as $debuggingmessage) {
                    $msgs[] = $this->get_debug_text($debuggingmessage->getHtml());
                }
                $msg = "debugging() message/s found:\n" . implode("\n", $msgs);
                throw new \Exception(html_entity_decode($msg));
            }

            // PHP debug messages.
            if ($phpmessages = $this->getSession()->getPage()->findAll('xpath', $phperrorxpath)) {

                $msgs = array();
                foreach ($phpmessages as $phpmessage) {
                    $msgs[] = $this->get_debug_text($phpmessage->getHtml());
                }
                $msg = "PHP debug message/s found:\n" . implode("\n", $msgs);
                throw new \Exception(html_entity_decode($msg));
            }

            // Any other backtrace.
            // First looking through xpath as it is faster than get and parse the whole page contents,
            // we get the contents and look for matches once we found something to suspect that there is a backtrace.
            if ($this->getSession()->getDriver()->find($othersxpath)) {
                $backtracespattern = '/(line [0-9]* of [^:]*: call to [\->&;:a-zA-Z_\x7f-\xff][\->&;:a-zA-Z0-9_\x7f-\xff]*)/';
                if (preg_match_all($backtracespattern, $this->getSession()->getPage()->getContent(), $backtraces)) {
                    $msgs = array();
                    foreach ($backtraces[0] as $backtrace) {
                        $msgs[] = $backtrace . '()';
                    }
                    $msg = "Other backtraces found:\n" . implode("\n", $msgs);
                    throw new \Exception(htmlentities($msg));
                }
            }

        } catch (NoSuchWindow $e) {
            // If we were interacting with a popup window it will not exists after closing it.
        }
    }

    /**
     * Converts HTML tags to line breaks to display the info in CLI
     *
     * @param string $html
     * @return string
     */
    protected function get_debug_text($html) {

        // Replacing HTML tags for new lines and keeping only the text.
        $notags = preg_replace('/<+\s*\/*\s*([A-Z][A-Z0-9]*)\b[^>]*\/*\s*>*/i', "\n", $html);
        return preg_replace("/(\n)+/s", "\n", $notags);
    }

    /**
     * Returns whether the first scenario of the suite is running
     *
     * @return bool
     */
    protected static function is_first_scenario() {
        return !(self::$initprocessesfinished);
    }

    /**
     * Throws an exception after appending an extra info text.
     *
     * @throws Exception
     * @param UnknownError $exception
     * @return void
     */
    protected function throw_unknown_exception(UnknownError $exception) {
        $text = get_string('unknownexceptioninfo', 'tool_behat');
        throw new Exception($text . PHP_EOL . $exception->getMessage());
    }

}
