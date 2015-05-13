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
    Behat\Behat\Event\FeatureEvent as FeatureEvent,
    Behat\Behat\Event\OutlineExampleEvent as OutlineExampleEvent,
    Behat\Behat\Event\StepEvent as StepEvent,
    Behat\Mink\Exception\DriverException as DriverException,
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
     * Some exceptions can only be caught in a before or after step hook,
     * they can not be thrown there as they will provoke a framework level
     * failure, but we can store them here to fail the step in i_look_for_exceptions()
     * which result will be parsed by the framework as the last step result.
     *
     * @var Null or the exception last step throw in the before or after hook.
     */
    protected static $currentstepexception = null;

    /**
     * If we are saving any kind of dump on failure we should use the same parent dir during a run.
     *
     * @var The parent dir name
     */
    protected static $faildumpdirname = false;

    /**
     * Keeps track of time taken by feature to execute.
     *
     * @var array list of feature timings
     */
    protected static $timings = array();

    /**
     * Gives access to moodle codebase, ensures all is ready and sets up the test lock.
     *
     * Includes config.php to use moodle codebase with $CFG->behat_*
     * instead of $CFG->prefix and $CFG->dataroot, called once per suite.
     *
     * @param SuiteEvent $event event before suite.
     * @static
     * @throws Exception
     * @BeforeSuite
     */
    public static function before_suite(SuiteEvent $event) {
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
        require_once(__DIR__ . '/../../behat/classes/behat_context_helper.php');
        require_once(__DIR__ . '/../../behat/classes/util.php');
        require_once(__DIR__ . '/../../testing/classes/test_lock.php');
        require_once(__DIR__ . '/../../testing/classes/nasty_strings.php');

        // Avoids vendor/bin/behat to be executed directly without test environment enabled
        // to prevent undesired db & dataroot modifications, this is also checked
        // before each scenario (accidental user deletes) in the BeforeScenario hook.

        if (!behat_util::is_test_mode_enabled()) {
            throw new Exception('Behat only can run if test mode is enabled. More info in ' . behat_command::DOCS_URL . '#Running_tests');
        }

        // Reset all data, before checking for is_server_running.
        // If not done, then it can return apache error, while running tests.
        behat_util::reset_all_data();

        if (!behat_util::is_server_running()) {
            throw new Exception($CFG->behat_wwwroot .
                ' is not available, ensure you specified correct url and that the server is set up and started.' .
                ' More info in ' . behat_command::DOCS_URL . '#Running_tests');
        }

        // Prevents using outdated data, upgrade script would start and tests would fail.
        if (!behat_util::is_test_data_updated()) {
            $commandpath = 'php admin/tool/behat/cli/init.php';
            throw new Exception("Your behat test site is outdated, please run\n\n    " .
                    $commandpath . "\n\nfrom your moodle dirroot to drop and install the behat test site again.");
        }
        // Avoid parallel tests execution, it continues when the previous lock is released.
        test_lock::acquire('behat');

        // Store the browser reset time if reset after N seconds is specified in config.php.
        if (!empty($CFG->behat_restart_browser_after)) {
            // Store the initial browser session opening.
            self::$lastbrowsersessionstart = time();
        }

        if (!empty($CFG->behat_faildump_path) && !is_writable($CFG->behat_faildump_path)) {
            throw new Exception('You set $CFG->behat_faildump_path to a non-writable directory');
        }
    }

    /**
     * Gives access to moodle codebase, to keep track of feature start time.
     *
     * @param FeatureEvent $event event fired before feature.
     * @BeforeFeature
     */
    public static function before_feature(FeatureEvent $event) {
        if (!defined('BEHAT_FEATURE_TIMING_FILE')) {
            return;
        }
        $file = $event->getFeature()->getFile();
        self::$timings[$file] = microtime(true);
    }

    /**
     * Gives access to moodle codebase, to keep track of feature end time.
     *
     * @param FeatureEvent $event event fired after feature.
     * @AfterFeature
     */
    public static function after_feature(FeatureEvent $event) {
        if (!defined('BEHAT_FEATURE_TIMING_FILE')) {
            return;
        }
        $file = $event->getFeature()->getFile();
        self::$timings[$file] = microtime(true) - self::$timings[$file];
        // Probably didn't actually run this, don't output it.
        if (self::$timings[$file] < 1) {
            unset(self::$timings[$file]);
        }
    }

    /**
     * Gives access to moodle codebase, to keep track of suite timings.
     *
     * @param SuiteEvent $event event fired after suite.
     * @AfterSuite
     */
    public static function after_suite(SuiteEvent $event) {
        if (!defined('BEHAT_FEATURE_TIMING_FILE')) {
            return;
        }
        $realroot = realpath(__DIR__.'/../../../').'/';
        foreach (self::$timings as $k => $v) {
            $new = str_replace($realroot, '', $k);
            self::$timings[$new] = round($v, 1);
            unset(self::$timings[$k]);
        }
        if ($existing = @json_decode(file_get_contents(BEHAT_FEATURE_TIMING_FILE), true)) {
            self::$timings = array_merge($existing, self::$timings);
        }
        arsort(self::$timings);
        @file_put_contents(BEHAT_FEATURE_TIMING_FILE, json_encode(self::$timings, JSON_PRETTY_PRINT));
    }

    /**
     * Resets the test environment.
     *
     * @param OutlineExampleEvent|ScenarioEvent $event event fired before scenario.
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

        $moreinfo = 'More info in ' . behat_command::DOCS_URL . '#Running_tests';
        $driverexceptionmsg = 'Selenium server is not running, you need to start it to run tests that involve Javascript. ' . $moreinfo;
        try {
            $session = $this->getSession();
        } catch (CurlExec $e) {
            // Exception thrown by WebDriver, so only @javascript tests will be caugth; in
            // behat_util::is_server_running() we already checked that the server is running.
            throw new Exception($driverexceptionmsg);
        } catch (DriverException $e) {
            throw new Exception($driverexceptionmsg);
        } catch (UnknownError $e) {
            // Generic 'I have no idea' Selenium error. Custom exception to provide more feedback about possible solutions.
            $this->throw_unknown_exception($e);
        }


        // We need the Mink session to do it and we do it only before the first scenario.
        if (self::is_first_scenario()) {
            behat_selectors::register_moodle_selectors($session);
            behat_context_helper::set_session($session);
        }

        // Reset mink session between the scenarios.
        $session->reset();

        // Reset $SESSION.
        \core\session\manager::init_empty_session();

        behat_util::reset_all_data();

        // Assign valid data to admin user (some generator-related code needs a valid user).
        $user = $DB->get_record('user', array('username' => 'admin'));
        \core\session\manager::set_user($user);

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
        // Run all test with medium (1024x768) screen size, to avoid responsive problems.
        $this->resize_window('medium');
    }

    /**
     * Wait for JS to complete before beginning interacting with the DOM.
     *
     * Executed only when running against a real browser. We wrap it
     * all in a try & catch to forward the exception to i_look_for_exceptions
     * so the exception will be at scenario level, which causes a failure, by
     * default would be at framework level, which will stop the execution of
     * the run.
     *
     * @param StepEvent $event event fired before step.
     * @BeforeStep @javascript
     */
    public function before_step_javascript(StepEvent $event) {

        try {
            $this->wait_for_pending_js();
            self::$currentstepexception = null;
        } catch (Exception $e) {
            self::$currentstepexception = $e;
        }
    }

    /**
     * Wait for JS to complete after finishing the step.
     *
     * With this we ensure that there are not AJAX calls
     * still in progress.
     *
     * Executed only when running against a real browser. We wrap it
     * all in a try & catch to forward the exception to i_look_for_exceptions
     * so the exception will be at scenario level, which causes a failure, by
     * default would be at framework level, which will stop the execution of
     * the run.
     *
     * @param StepEvent $event event fired after step.
     * @AfterStep @javascript
     */
    public function after_step_javascript(StepEvent $event) {
        global $CFG;

        // Save a screenshot if the step failed.
        if (!empty($CFG->behat_faildump_path) &&
                $event->getResult() === StepEvent::FAILED) {
            $this->take_screenshot($event);
        }

        try {
            $this->wait_for_pending_js();
            self::$currentstepexception = null;
        } catch (UnexpectedAlertOpen $e) {
            self::$currentstepexception = $e;

            // Accepting the alert so the framework can continue properly running
            // the following scenarios. Some browsers already closes the alert, so
            // wrapping in a try & catch.
            try {
                $this->getSession()->getDriver()->getWebDriverSession()->accept_alert();
            } catch (Exception $e) {
                // Catching the generic one as we never know how drivers reacts here.
            }
        } catch (Exception $e) {
            self::$currentstepexception = $e;
        }
    }

    /**
     * Execute any steps required after the step has finished.
     *
     * This includes creating an HTML dump of the content if there was a failure.
     *
     * @param StepEvent $event event fired after step.
     * @AfterStep
     */
    public function after_step(StepEvent $event) {
        global $CFG;

        // Save the page content if the step failed.
        if (!empty($CFG->behat_faildump_path) &&
                $event->getResult() === StepEvent::FAILED) {
            $this->take_contentdump($event);
        }
    }

    /**
     * Getter for self::$faildumpdirname
     *
     * @return string
     */
    protected function get_run_faildump_dir() {
        return self::$faildumpdirname;
    }

    /**
     * Take screenshot when a step fails.
     *
     * @throws Exception
     * @param StepEvent $event
     */
    protected function take_screenshot(StepEvent $event) {
        // Goutte can't save screenshots.
        if (!$this->running_javascript()) {
            return false;
        }

        list ($dir, $filename) = $this->get_faildump_filename($event, 'png');
        $this->saveScreenshot($filename, $dir);
    }

    /**
     * Take a dump of the page content when a step fails.
     *
     * @throws Exception
     * @param StepEvent $event
     */
    protected function take_contentdump(StepEvent $event) {
        list ($dir, $filename) = $this->get_faildump_filename($event, 'html');

        $fh = fopen($dir . DIRECTORY_SEPARATOR . $filename, 'w');
        fwrite($fh, $this->getSession()->getPage()->getContent());
        fclose($fh);
    }

    /**
     * Determine the full pathname to store a failure-related dump.
     *
     * This is used for content such as the DOM, and screenshots.
     *
     * @param StepEvent $event
     * @param String $filetype The file suffix to use. Limited to 4 chars.
     */
    protected function get_faildump_filename(StepEvent $event, $filetype) {
        global $CFG;

        // All the contentdumps should be in the same parent dir.
        if (!$faildumpdir = self::get_run_faildump_dir()) {
            $faildumpdir = self::$faildumpdirname = date('Ymd_His');

            $dir = $CFG->behat_faildump_path . DIRECTORY_SEPARATOR . $faildumpdir;

            if (!is_dir($dir) && !mkdir($dir, $CFG->directorypermissions, true)) {
                // It shouldn't, we already checked that the directory is writable.
                throw new Exception('No directories can be created inside $CFG->behat_faildump_path, check the directory permissions.');
            }
        } else {
            // We will always need to know the full path.
            $dir = $CFG->behat_faildump_path . DIRECTORY_SEPARATOR . $faildumpdir;
        }

        // The scenario title + the failed step text.
        // We want a i-am-the-scenario-title_i-am-the-failed-step.$filetype format.
        $filename = $event->getStep()->getParent()->getTitle() . '_' . $event->getStep()->getText();
        $filename = preg_replace('/([^a-zA-Z0-9\_]+)/', '-', $filename);

        // File name limited to 255 characters. Leaving 4 chars for the file
        // extension as we allow .png for images and .html for DOM contents.
        $filename = substr($filename, 0, 250) . '.' . $filetype;

        return array($dir, $filename);
    }

    /**
     * Waits for all the JS to be loaded.
     *
     * @throws \Exception
     * @throws NoSuchWindow
     * @throws UnknownError
     * @return bool True or false depending whether all the JS is loaded or not.
     */
    protected function wait_for_pending_js() {

        // We don't use behat_base::spin() here as we don't want to end up with an exception
        // if the page & JSs don't finish loading properly.
        for ($i = 0; $i < self::EXTENDED_TIMEOUT * 10; $i++) {
            $pending = '';
            try {
                $jscode =
                    'if (typeof M === "undefined") {
                        if (document.readyState === "complete") {
                            return "";
                        } else {
                            return "incomplete";
                        }
                    } else if (' . self::PAGE_READY_JS . ') {
                        return "";
                    } else {
                        return M.util.pending_js.join(":");
                    }';
                $pending = $this->getSession()->evaluateScript($jscode);
            } catch (NoSuchWindow $nsw) {
                // We catch an exception here, in case we just closed the window we were interacting with.
                // No javascript is running if there is no window right?
                $pending = '';
            } catch (UnknownError $e) {
                // M is not defined when the window or the frame don't exist anymore.
                if (strstr($e->getMessage(), 'M is not defined') != false) {
                    $pending = '';
                }
            }

            // If there are no pending JS we stop waiting.
            if ($pending === '') {
                return true;
            }

            // 0.1 seconds.
            usleep(100000);
        }

        // Timeout waiting for JS to complete. It will be catched and forwarded to behat_hooks::i_look_for_exceptions().
        // It is unlikely that Javascript code of a page or an AJAX request needs more than self::EXTENDED_TIMEOUT seconds
        // to be loaded, although when pages contains Javascript errors M.util.js_complete() can not be executed, so the
        // number of JS pending code and JS completed code will not match and we will reach this point.
        throw new \Exception('Javascript code and/or AJAX requests are not ready after ' . self::EXTENDED_TIMEOUT .
            ' seconds. There is a Javascript error or the code is extremely slow.');
    }

    /**
     * Internal step definition to find exceptions, debugging() messages and PHP debug messages.
     *
     * Part of behat_hooks class as is part of the testing framework, is auto-executed
     * after each step so no features will splicitly use it.
     *
     * @Given /^I look for exceptions$/
     * @throw Exception Unknown type, depending on what we caught in the hook or basic \Exception.
     * @see Moodle\BehatExtension\Tester\MoodleStepTester
     */
    public function i_look_for_exceptions() {

        // If the step already failed in a hook throw the exception.
        if (!is_null(self::$currentstepexception)) {
            throw self::$currentstepexception;
        }

        // Wrap in try in case we were interacting with a closed window.
        try {

            // Exceptions.
            $exceptionsxpath = "//div[@data-rel='fatalerror']";
            // Debugging messages.
            $debuggingxpath = "//div[@data-rel='debugging']";
            // PHP debug messages.
            $phperrorxpath = "//div[@data-rel='phpdebugmessage']";
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
                $errorinfoboxes = $this->getSession()->getPage()->findAll('css', 'div.alert-error');
                // If errorinfoboxes is empty, try find notifytiny (original) class.
                if (empty($errorinfoboxes)) {
                    $errorinfoboxes = $this->getSession()->getPage()->findAll('css', 'div.notifytiny');
                }
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

