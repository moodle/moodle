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

use Behat\Testwork\Hook\Scope\BeforeSuiteScope,
    Behat\Testwork\Hook\Scope\AfterSuiteScope,
    Behat\Behat\Hook\Scope\BeforeFeatureScope,
    Behat\Behat\Hook\Scope\AfterFeatureScope,
    Behat\Behat\Hook\Scope\BeforeScenarioScope,
    Behat\Behat\Hook\Scope\AfterScenarioScope,
    Behat\Behat\Hook\Scope\BeforeStepScope,
    Behat\Behat\Hook\Scope\AfterStepScope,
    Behat\Mink\Exception\ExpectationException,
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
     * @var For actions that should only run once.
     */
    protected static $initprocessesfinished = false;

    /**
     * @var bool Scenario running
     */
    protected $scenariorunning = false;

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
     * Keeps track of current running suite name.
     *
     * @var string current running suite name
     */
    protected static $runningsuite = '';

    /**
     * @var array Array (with tag names in keys) of all tags in current scenario.
     */
    protected static $scenariotags;

    /**
     * Hook to capture BeforeSuite event so as to give access to moodle codebase.
     * This will try and catch any exception and exists if anything fails.
     *
     * @param BeforeSuiteScope $scope scope passed by event fired before suite.
     * @BeforeSuite
     */
    public static function before_suite_hook(BeforeSuiteScope $scope) {
        // If behat has been initialised then no need to do this again.
        if (self::$initprocessesfinished) {
            return;
        }

        try {
            self::before_suite($scope);
        } catch (behat_stop_exception $e) {
            echo $e->getMessage() . PHP_EOL;
            exit(1);
        }
    }

    /**
     * Gives access to moodle codebase, ensures all is ready and sets up the test lock.
     *
     * Includes config.php to use moodle codebase with $CFG->behat_*
     * instead of $CFG->prefix and $CFG->dataroot, called once per suite.
     *
     * @param BeforeSuiteScope $scope scope passed by event fired before suite.
     * @static
     * @throws behat_stop_exception
     */
    public static function before_suite(BeforeSuiteScope $scope) {
        global $CFG;

        // Defined only when the behat CLI command is running, the moodle init setup process will
        // read this value and switch to $CFG->behat_dataroot and $CFG->behat_prefix instead of
        // the normal site.
        if (!defined('BEHAT_TEST')) {
            define('BEHAT_TEST', 1);
        }

        if (!defined('CLI_SCRIPT')) {
            define('CLI_SCRIPT', 1);
        }

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
            throw new behat_stop_exception('Behat only can run if test mode is enabled. More info in ' .
                behat_command::DOCS_URL);
        }

        // Reset all data, before checking for check_server_status.
        // If not done, then it can return apache error, while running tests.
        behat_util::clean_tables_updated_by_scenario_list();
        behat_util::reset_all_data();

        // Check if server is running and using same version for cli and apache.
        behat_util::check_server_status();

        // Prevents using outdated data, upgrade script would start and tests would fail.
        if (!behat_util::is_test_data_updated()) {
            $commandpath = 'php admin/tool/behat/cli/init.php';
            throw new behat_stop_exception("Your behat test site is outdated, please run\n\n    " .
                    $commandpath . "\n\nfrom your moodle dirroot to drop and install the behat test site again.");
        }
        // Avoid parallel tests execution, it continues when the previous lock is released.
        test_lock::acquire('behat');

        if (!empty($CFG->behat_faildump_path) && !is_writable($CFG->behat_faildump_path)) {
            throw new behat_stop_exception('You set $CFG->behat_faildump_path to a non-writable directory');
        }

        // Handle interrupts on PHP7.
        if (extension_loaded('pcntl')) {
            $disabled = explode(',', ini_get('disable_functions'));
            if (!in_array('pcntl_signal', $disabled)) {
                declare(ticks = 1);
            }
        }
    }

    /**
     * Run final tests before running the suite.
     *
     * @BeforeSuite
     * @param BeforeSuiteScope $scope scope passed by event fired before suite.
     */
    public static function before_suite_final_checks(BeforeSuiteScope $scope) {
        $happy = defined('BEHAT_TEST');
        $happy = $happy && defined('BEHAT_SITE_RUNNING');
        $happy = $happy && php_sapi_name() == 'cli';
        $happy = $happy && behat_util::is_test_mode_enabled();
        $happy = $happy && behat_util::is_test_site();

        if (!$happy) {
            error_log('Behat only can modify the test database and the test dataroot!');
            exit(1);
        }
    }

    /**
     * Gives access to moodle codebase, to keep track of feature start time.
     *
     * @param BeforeFeatureScope $scope scope passed by event fired before feature.
     * @BeforeFeature
     */
    public static function before_feature(BeforeFeatureScope $scope) {
        if (!defined('BEHAT_FEATURE_TIMING_FILE')) {
            return;
        }
        $file = $scope->getFeature()->getFile();
        self::$timings[$file] = microtime(true);
    }

    /**
     * Gives access to moodle codebase, to keep track of feature end time.
     *
     * @param AfterFeatureScope $scope scope passed by event fired after feature.
     * @AfterFeature
     */
    public static function after_feature(AfterFeatureScope $scope) {
        if (!defined('BEHAT_FEATURE_TIMING_FILE')) {
            return;
        }
        $file = $scope->getFeature()->getFile();
        self::$timings[$file] = microtime(true) - self::$timings[$file];
        // Probably didn't actually run this, don't output it.
        if (self::$timings[$file] < 1) {
            unset(self::$timings[$file]);
        }
    }

    /**
     * Gives access to moodle codebase, to keep track of suite timings.
     *
     * @param AfterSuiteScope $scope scope passed by event fired after suite.
     * @AfterSuite
     */
    public static function after_suite(AfterSuiteScope $scope) {
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
     * Hook to capture before scenario event to get scope.
     *
     * @param BeforeScenarioScope $scope scope passed by event fired before scenario.
     * @BeforeScenario
     */
    public function before_scenario_hook(BeforeScenarioScope $scope) {
        try {
            $this->before_scenario($scope);
        } catch (behat_stop_exception $e) {
            echo $e->getMessage() . PHP_EOL;
            exit(1);
        }
    }

    /**
     * Resets the test environment.
     *
     * @param BeforeScenarioScope $scope scope passed by event fired before scenario.
     * @throws behat_stop_exception If here we are not using the test database it should be because of a coding error
     */
    public function before_scenario(BeforeScenarioScope $scope) {
        global $DB, $CFG;

        $moreinfo = 'More info in ' . behat_command::DOCS_URL;
        $driverexceptionmsg = 'Selenium server is not running, you need to start it to run tests that involve Javascript. ' . $moreinfo;
        try {
            $session = $this->getSession();
        } catch (CurlExec $e) {
            // Exception thrown by WebDriver, so only @javascript tests will be caugth; in
            // behat_util::check_server_status() we already checked that the server is running.
            throw new behat_stop_exception(
                $driverexceptionmsg . '. ' .
                $e->getMessage() . "\n\n" .
                format_backtrace($e->getTrace(), true)
            );
        } catch (DriverException $e) {
            throw new behat_stop_exception(
                $driverexceptionmsg . '. ' .
                $e->getMessage() . "\n\n" .
                format_backtrace($e->getTrace(), true)
            );
        } catch (UnknownError $e) {
            // Generic 'I have no idea' Selenium error. Custom exception to provide more feedback about possible solutions.
            throw new behat_stop_exception(
                $e->getMessage() . "\n\n" .
                format_backtrace($e->getTrace(), true)
            );
        }

        $suitename = $scope->getSuite()->getName();

        // Register behat selectors for theme, if suite is changed. We do it for every suite change.
        if ($suitename !== self::$runningsuite) {
            self::$runningsuite = $suitename;
            behat_context_helper::set_environment($scope->getEnvironment());

            // We need the Mink session to do it and we do it only before the first scenario.
            $namedpartialclass = 'behat_partial_named_selector';
            $namedexactclass = 'behat_exact_named_selector';

            // If override selector exist, then set it as default behat selectors class.
            $overrideclass = behat_config_util::get_behat_theme_selector_override_classname($suitename, 'named_partial', true);
            if (class_exists($overrideclass)) {
                $namedpartialclass = $overrideclass;
            }

            // If override selector exist, then set it as default behat selectors class.
            $overrideclass = behat_config_util::get_behat_theme_selector_override_classname($suitename, 'named_exact', true);
            if (class_exists($overrideclass)) {
                $namedexactclass = $overrideclass;
            }

            $this->getSession()->getSelectorsHandler()->registerSelector('named_partial', new $namedpartialclass());
            $this->getSession()->getSelectorsHandler()->registerSelector('named_exact', new $namedexactclass());

            // Register component named selectors.
            foreach (\core_component::get_component_names() as $component) {
                $this->register_component_selectors_for_component($component);
            }

        }

        // Reset mink session between the scenarios.
        $session->reset();

        // Reset $SESSION.
        \core\session\manager::init_empty_session();

        // Ignore E_NOTICE and E_WARNING during reset, as this might be caused because of some existing process
        // running ajax. This will be investigated in another issue.
        $errorlevel = error_reporting();
        error_reporting($errorlevel & ~E_NOTICE & ~E_WARNING);
        behat_util::reset_all_data();
        error_reporting($errorlevel);

        if ($this->running_javascript()) {
            // Fetch the user agent.
            // This isused to choose between the SVG/Non-SVG versions of themes.
            $useragent = $this->getSession()->evaluateScript('return navigator.userAgent;');
            \core_useragent::instance(true, $useragent);

            // Restore the saved themes.
            behat_util::restore_saved_themes();
        }

        // Assign valid data to admin user (some generator-related code needs a valid user).
        $user = $DB->get_record('user', array('username' => 'admin'));
        \core\session\manager::set_user($user);

        // Set the theme if not default.
        if ($suitename !== "default") {
            set_config('theme', $suitename);
        }

        // Reset the scenariorunning variable to ensure that Step 0 occurs.
        $this->scenariorunning = false;

        // Set up the tags for current scenario.
        self::fetch_tags_for_scenario($scope);

        // If scenario requires the Moodle app to be running, set this up.
        if ($this->has_tag('app')) {
            $this->execute('behat_app::start_scenario');

            return;
        }

        // Run all test with medium (1024x768) screen size, to avoid responsive problems.
        $this->resize_window('medium');
    }

    /**
     * Hook to open the site root before the first step in the suite.
     * Yes, this is in a strange location and should be in the BeforeScenario hook, but failures in the test setUp lead
     * to the test being incorrectly marked as skipped with no way to force the test to be failed.
     *
     * @param BeforeStepScope $scope
     * @BeforeStep
     */
    public function before_step(BeforeStepScope $scope) {
        global $CFG;

        if (!$this->scenariorunning) {
            // We need to visit / before the first step in any Scenario.
            // This is our Step 0.
            // Ideally this would be in the BeforeScenario hook, but any exception in there will lead to the test being
            // skipped rather than it being failed.
            //
            // We also need to check that the site returned is a Behat site.
            // Again, this would be better in the BeforeSuite hook, but that does not have access to the selectors in
            // order to perform the necessary searches.
            $session = $this->getSession();
            $session->visit($this->locate_path('/'));

            // Checking that the root path is a Moodle test site.
            if (self::is_first_scenario()) {
                $message = "The base URL ({$CFG->wwwroot}) is not a behat test site. " .
                    'Ensure that you started the built-in web server in the correct directory, ' .
                    'or that your web server is correctly set up and started.';

                $this->find(
                        "xpath", "//head/child::title[normalize-space(.)='" . behat_util::BEHATSITENAME . "']",
                        new ExpectationException($message, $session)
                    );

            }
            $this->scenariorunning = true;
        }
    }

    /**
     * Sets up the tags for the current scenario.
     *
     * @param \Behat\Behat\Hook\Scope\BeforeScenarioScope $scope Scope
     */
    protected static function fetch_tags_for_scenario(\Behat\Behat\Hook\Scope\BeforeScenarioScope $scope) {
        self::$scenariotags = array_flip(array_merge(
            $scope->getScenario()->getTags(),
            $scope->getFeature()->getTags()
        ));
    }

    /**
     * Gets the tags for the current scenario
     *
     * @return array Array where key is tag name and value is an integer
     */
    public static function get_tags_for_scenario() : array {
        return self::$scenariotags;
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
     * @param BeforeStepScope $scope scope passed by event fired before step.
     * @BeforeStep
     */
    public function before_step_javascript(BeforeStepScope $scope) {
        self::$currentstepexception = null;

        // Only run if JS.
        if ($this->running_javascript()) {
            try {
                $this->wait_for_pending_js();
            } catch (Exception $e) {
                self::$currentstepexception = $e;
            }
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
     * @param AfterStepScope $scope scope passed by event fired after step..
     * @AfterStep
     */
    public function after_step_javascript(AfterStepScope $scope) {
        global $CFG, $DB;

        // If step is undefined then throw exception, to get failed exit code.
        if ($scope->getTestResult()->getResultCode() === Behat\Behat\Tester\Result\StepResult::UNDEFINED) {
            throw new coding_exception("Step '" . $scope->getStep()->getText() . "'' is undefined.");
        }

        $isfailed = $scope->getTestResult()->getResultCode() === Behat\Testwork\Tester\Result\TestResult::FAILED;

        // Abort any open transactions to prevent subsequent tests hanging.
        // This does the same as abort_all_db_transactions(), but doesn't call error_log() as we don't
        // want to see a message in the behat output.
        if (($scope->getTestResult() instanceof \Behat\Behat\Tester\Result\ExecutedStepResult) &&
            $scope->getTestResult()->hasException()) {
            if ($DB && $DB->is_transaction_started()) {
                $DB->force_transaction_rollback();
            }
        }

        if ($isfailed && !empty($CFG->behat_faildump_path)) {
            // Save the page content (html).
            $this->take_contentdump($scope);

            if ($this->running_javascript()) {
                // Save a screenshot.
                $this->take_screenshot($scope);
            }
        }

        if ($isfailed && !empty($CFG->behat_pause_on_fail)) {
            $exception = $scope->getTestResult()->getException();
            $message = "<colour:lightRed>Scenario failed. ";
            $message .= "<colour:lightYellow>Paused for inspection. Press <colour:lightRed>Enter/Return<colour:lightYellow> to continue.<newline>";
            $message .= "<colour:lightRed>Exception follows:<newline>";
            $message .= trim($exception->getMessage());
            behat_util::pause($this->getSession(), $message);
        }

        // Only run if JS.
        if (!$this->running_javascript()) {
            return;
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
     * Reset the session between each scenario.
     *
     * @param AfterScenarioScope $scope scope passed by event fired after scenario.
     * @AfterScenario
     */
    public function reset_webdriver_between_scenarios(AfterScenarioScope $scope) {
        $this->getSession()->stop();
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
     * @param AfterStepScope $scope scope passed by event after step.
     */
    protected function take_screenshot(AfterStepScope $scope) {
        // Goutte can't save screenshots.
        if (!$this->running_javascript()) {
            return false;
        }

        // Some drivers (e.g. chromedriver) may throw an exception while trying to take a screenshot.  If this isn't handled,
        // the behat run dies.  We don't want to lose the information about the failure that triggered the screenshot,
        // so let's log the exception message to a file (to explain why there's no screenshot) and allow the run to continue,
        // handling the failure as normal.
        try {
            list ($dir, $filename) = $this->get_faildump_filename($scope, 'png');
            $this->saveScreenshot($filename, $dir);
        } catch (Exception $e) {
            // Catching all exceptions as we don't know what the driver might throw.
            list ($dir, $filename) = $this->get_faildump_filename($scope, 'txt');
            $message = "Could not save screenshot due to an error\n" . $e->getMessage();
            file_put_contents($dir . DIRECTORY_SEPARATOR . $filename, $message);
        }
    }

    /**
     * Take a dump of the page content when a step fails.
     *
     * @throws Exception
     * @param AfterStepScope $scope scope passed by event after step.
     */
    protected function take_contentdump(AfterStepScope $scope) {
        list ($dir, $filename) = $this->get_faildump_filename($scope, 'html');

        try {
            // Driver may throw an exception during getContent(), so do it first to avoid getting an empty file.
            $content = $this->getSession()->getPage()->getContent();
        } catch (Exception $e) {
            // Catching all exceptions as we don't know what the driver might throw.
            $content = "Could not save contentdump due to an error\n" . $e->getMessage();
        }
        file_put_contents($dir . DIRECTORY_SEPARATOR . $filename, $content);
    }

    /**
     * Determine the full pathname to store a failure-related dump.
     *
     * This is used for content such as the DOM, and screenshots.
     *
     * @param AfterStepScope $scope scope passed by event after step.
     * @param String $filetype The file suffix to use. Limited to 4 chars.
     */
    protected function get_faildump_filename(AfterStepScope $scope, $filetype) {
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
        $filename = $scope->getFeature()->getTitle() . '_' . $scope->getStep()->getText();

        // As file name is limited to 255 characters. Leaving 5 chars for line number and 4 chars for the file.
        // extension as we allow .png for images and .html for DOM contents.
        $filenamelen = 245;

        // Suffix suite name to faildump file, if it's not default suite.
        $suitename = $scope->getSuite()->getName();
        if ($suitename != 'default') {
            $suitename = '_' . $suitename;
            $filenamelen = $filenamelen - strlen($suitename);
        } else {
            // No need to append suite name for default.
            $suitename = '';
        }

        $filename = preg_replace('/([^a-zA-Z0-9\_]+)/', '-', $filename);
        $filename = substr($filename, 0, $filenamelen) . $suitename . '_' . $scope->getStep()->getLine() . '.' . $filetype;

        return array($dir, $filename);
    }

    /**
     * Internal step definition to find exceptions, debugging() messages and PHP debug messages.
     *
     * Part of behat_hooks class as is part of the testing framework, is auto-executed
     * after each step so no features will splicitly use it.
     *
     * @Given /^I look for exceptions$/
     * @throw Exception Unknown type, depending on what we caught in the hook or basic \Exception.
     * @see Moodle\BehatExtension\EventDispatcher\Tester\ChainedStepTester
     */
    public function i_look_for_exceptions() {
        // If the step already failed in a hook throw the exception.
        if (!is_null(self::$currentstepexception)) {
            throw self::$currentstepexception;
        }

        $this->look_for_exceptions();
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
     * Register a set of component selectors.
     *
     * @param string $component
     */
    public function register_component_selectors_for_component(string $component): void {
        $context = behat_context_helper::get_component_context($component);

        if ($context === null) {
            return;
        }

        $namedpartial = $this->getSession()->getSelectorsHandler()->getSelector('named_partial');
        $namedexact = $this->getSession()->getSelectorsHandler()->getSelector('named_exact');

        // Replacements must come before selectors as they are used in the selectors.
        foreach ($context->get_named_replacements() as $replacement) {
            $namedpartial->register_replacement($component, $replacement);
            $namedexact->register_replacement($component, $replacement);
        }

        foreach ($context->get_partial_named_selectors() as $selector) {
            $namedpartial->register_component_selector($component, $selector);
        }

        foreach ($context->get_exact_named_selectors() as $selector) {
            $namedexact->register_component_selector($component, $selector);
        }

    }

    /**
     * Mark the first step as having been completed.
     *
     * This must be the last BeforeStep hook in the setup.
     *
     * @param BeforeStepScope $scope
     * @BeforeStep
     */
    public function first_step_setup_complete(BeforeStepScope $scope) {
        self::$initprocessesfinished = true;
    }

}

/**
 * Behat stop exception
 *
 * This exception is thrown from before suite or scenario if any setup problem found.
 *
 * @package    core_test
 * @copyright  2016 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_stop_exception extends \Exception {
}
