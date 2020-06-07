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
 * Mobile/desktop app steps definitions.
 *
 * @package core
 * @category test
 * @copyright 2018 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../behat/behat_base.php');

use Behat\Mink\Exception\DriverException;
use Behat\Mink\Exception\ExpectationException;

/**
 * Mobile/desktop app steps definitions.
 *
 * @package core
 * @category test
 * @copyright 2018 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_app extends behat_base {
    /** @var stdClass Object with data about launched Ionic instance (if any) */
    protected static $ionicrunning = null;

    /** @var string URL for running Ionic server */
    protected $ionicurl = '';

    /**
     * Checks if the current OS is Windows, from the point of view of task-executing-and-killing.
     *
     * @return bool True if Windows
     */
    protected static function is_windows() : bool {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }

    /**
     * Called from behat_hooks when a new scenario starts, if it has the app tag.
     *
     * This updates Moodle configuration and starts Ionic running, if it isn't already.
     */
    public function start_scenario() {
        $this->check_behat_setup();
        $this->fix_moodle_setup();
        $this->ionicurl = $this->start_or_reuse_ionic();
}

    /**
     * Opens the Moodle app in the browser.
     *
     * Requires JavaScript.
     *
     * @Given /^I enter the app$/
     * @throws DriverException Issue with configuration or feature file
     * @throws dml_exception Problem with Moodle setup
     * @throws ExpectationException Problem with resizing window
     */
    public function i_enter_the_app() {
        // Check the app tag was set.
        if (!$this->has_tag('app')) {
            throw new DriverException('Requires @app tag on scenario or feature.');
        }

        // Restart the browser and set its size.
        $this->getSession()->restart();
        $this->resize_window('360x720', true);

        if (empty($this->ionicurl)) {
            $this->ionicurl = $this->start_or_reuse_ionic();
        }

        // Go to page and prepare browser for app.
        $this->prepare_browser($this->ionicurl);
    }

    /**
     * Checks the Behat setup - tags and configuration.
     *
     * @throws DriverException
     */
    protected function check_behat_setup() {
        global $CFG;

        // Check JavaScript is enabled.
        if (!$this->running_javascript()) {
            throw new DriverException('The app requires JavaScript.');
        }

        // Check the config settings are defined.
        if (empty($CFG->behat_ionic_wwwroot) && empty($CFG->behat_ionic_dirroot)) {
            throw new DriverException('$CFG->behat_ionic_wwwroot or $CFG->behat_ionic_dirroot must be defined.');
        }
    }

    /**
     * Fixes the Moodle admin settings to allow mobile app use (if not already correct).
     *
     * @throws dml_exception If there is any problem changing Moodle settings
     */
    protected function fix_moodle_setup() {
        global $CFG, $DB;

        // Configure Moodle settings to enable app web services.
        if (!$CFG->enablewebservices) {
            set_config('enablewebservices', 1);
        }
        if (!$CFG->enablemobilewebservice) {
            set_config('enablemobilewebservice', 1);
        }

        // Add 'Create token' and 'Use REST webservice' permissions to authenticated user role.
        $userroleid = $DB->get_field('role', 'id', ['shortname' => 'user']);
        $systemcontext = \context_system::instance();
        role_change_permission($userroleid, $systemcontext, 'moodle/webservice:createtoken', CAP_ALLOW);
        role_change_permission($userroleid, $systemcontext, 'webservice/rest:use', CAP_ALLOW);

        // Check the value of the 'webserviceprotocols' config option. Due to weird behaviour
        // in Behat with regard to config variables that aren't defined in a settings.php, the
        // value in $CFG here may reflect a previous run, so get it direct from the database
        // instead.
        $field = $DB->get_field('config', 'value', ['name' => 'webserviceprotocols'], IGNORE_MISSING);
        if (empty($field)) {
            $protocols = [];
        } else {
            $protocols = explode(',', $field);
        }
        if (!in_array('rest', $protocols)) {
            $protocols[] = 'rest';
            set_config('webserviceprotocols', implode(',', $protocols));
        }

        // Enable mobile service.
        require_once($CFG->dirroot . '/webservice/lib.php');
        $webservicemanager = new webservice();
        $service = $webservicemanager->get_external_service_by_shortname(
                MOODLE_OFFICIAL_MOBILE_SERVICE, MUST_EXIST);
        if (!$service->enabled) {
            $service->enabled = 1;
            $webservicemanager->update_external_service($service);
        }

        // If installed, also configure local_mobile plugin to enable additional features service.
        $localplugins = core_component::get_plugin_list('local');
        if (array_key_exists('mobile', $localplugins)) {
            $service = $webservicemanager->get_external_service_by_shortname(
                    'local_mobile', MUST_EXIST);
            if (!$service->enabled) {
                $service->enabled = 1;
                $webservicemanager->update_external_service($service);
            }
        }
    }

    /**
     * Starts an Ionic server if necessary, or uses an existing one.
     *
     * @return string URL to Ionic server
     * @throws DriverException If there's a system error starting Ionic
     */
    protected function start_or_reuse_ionic() {
        global $CFG;

        if (empty($CFG->behat_ionic_dirroot) && !empty($CFG->behat_ionic_wwwroot)) {
            // Use supplied Ionic server which should already be running.
            $url = $CFG->behat_ionic_wwwroot;
        } else if (self::$ionicrunning) {
            // Use existing Ionic instance launched previously.
            $url = self::$ionicrunning->url;
        } else {
            // Open Ionic process in relevant path.
            $path = realpath($CFG->behat_ionic_dirroot);
            $stderrfile = $CFG->dataroot . '/behat/ionic-stderr.log';
            $prefix = '';
            // Except on Windows, use 'exec' so that we get the pid of the actual Node process
            // and not the shell it uses to execute. You can't do exec on Windows; there is a
            // bypass_shell option but it is not the same thing and isn't usable here.
            if (!self::is_windows()) {
                $prefix = 'exec ';
            }
            $process = proc_open($prefix . 'ionic serve --no-interactive --no-open',
                    [['pipe', 'r'], ['pipe', 'w'], ['file', $stderrfile, 'w']], $pipes, $path);
            if ($process === false) {
                throw new DriverException('Error starting Ionic process');
            }
            fclose($pipes[0]);

            // Get pid - we will need this to kill the process.
            $status = proc_get_status($process);
            $pid = $status['pid'];

            // Read data from stdout until the server comes online.
            // Note: On Windows it is impossible to read simultaneously from stderr and stdout
            // because stream_select and non-blocking I/O don't work on process pipes, so that is
            // why stderr was redirected to a file instead. Also, this code is simpler.
            $url = null;
            $stdoutlog = '';
            while (true) {
                $line = fgets($pipes[1], 4096);
                if ($line === false) {
                    break;
                }

                $stdoutlog .= $line;

                if (preg_match('~^\s*Local: (http\S*)~', $line, $matches)) {
                    $url = $matches[1];
                    break;
                }
            }

            // If it failed, close the pipes and the process.
            if (!$url) {
                fclose($pipes[1]);
                proc_close($process);
                $logpath = $CFG->dataroot . '/behat/ionic-start.log';
                $stderrlog = file_get_contents($stderrfile);
                @unlink($stderrfile);
                file_put_contents($logpath,
                        "Ionic startup log from " . date('c') .
                        "\n\n----STDOUT----\n$stdoutlog\n\n----STDERR----\n$stderrlog");
                throw new DriverException('Unable to start Ionic. See ' . $logpath);
            }

            // Remember the URL, so we can reuse it next time, and other details so we can kill
            // the process.
            self::$ionicrunning = (object)['url' => $url, 'process' => $process, 'pipes' => $pipes,
                    'pid' => $pid];
            $url = self::$ionicrunning->url;
        }
        return $url;
    }

    /**
     * Closes Ionic (if it was started) at end of test suite.
     *
     * @AfterSuite
     */
    public static function close_ionic() {
        if (self::$ionicrunning) {
            fclose(self::$ionicrunning->pipes[1]);

            if (self::is_windows()) {
                // Using proc_terminate here does not work. It terminates the process but not any
                // other processes it might have launched. Instead, we need to use an OS-specific
                // mechanism to kill the process and children based on its pid.
                exec('taskkill /F /T /PID ' . self::$ionicrunning->pid);
            } else {
                // On Unix this actually works, although only due to the 'exec' command inserted
                // above.
                proc_terminate(self::$ionicrunning->process);
            }
            self::$ionicrunning = null;
        }
    }

    /**
     * Goes to the app page and then sets up some initial JavaScript so we can use it.
     *
     * @param string $url App URL
     * @throws DriverException If the app fails to load properly
     */
    protected function prepare_browser(string $url) {
        global $CFG;

        // Visit the Ionic URL and wait for it to load.
        $this->getSession()->visit($url);
        $this->spin(
                function($context, $args) {
                    $title = $context->getSession()->getPage()->find('xpath', '//title');
                    if ($title) {
                        $text = $title->getHtml();
                        if ($text === 'Moodle Desktop') {
                            return true;
                        }
                    }
                    throw new DriverException('Moodle app not found in browser');
                }, false, 60);

        // Run the scripts to install Moodle 'pending' checks.
        $this->getSession()->executeScript(
                file_get_contents(__DIR__ . '/app_behat_runtime.js'));

        // Wait until the site login field appears OR the main page.
        $situation = $this->spin(
                function($context, $args) {
                    $page = $context->getSession()->getPage();

                    $element = $page->find('xpath', '//page-core-login-site//input[@name="url"]');
                    if ($element) {
                        // Wait for the onboarding modal to open, if any.
                        $this->wait_for_pending_js();
                        $element = $page->find('xpath', '//page-core-login-site-onboarding');
                        if ($element) {
                            $this->i_press_in_the_app('Skip');
                        }

                        return 'login';
                    }

                    $element = $page->find('xpath', '//page-core-mainmenu');
                    if ($element) {
                        return 'mainpage';
                    }
                    throw new DriverException('Moodle app login URL prompt not found');
                }, behat_base::get_extended_timeout(), 60);

        // If it's the login page, we automatically fill in the URL and leave it on the user/pass
        // page. If it's the main page, we just leave it there.
        if ($situation === 'login') {
            $this->i_set_the_field_in_the_app('campus.example.edu', $CFG->wwwroot);
            $this->i_press_in_the_app('Connect!');
        }

        // Continue only after JS finishes.
        $this->wait_for_pending_js();
    }

    /**
     * Carries out the login steps for the app, assuming the user is on the app login page. Called
     * from behat_auth.php.
     *
     * @param string $username Username (and password)
     * @throws Exception Any error
     */
    public function login(string $username) {
        $this->i_set_the_field_in_the_app('Username', $username);
        $this->i_set_the_field_in_the_app('Password', $username);

        // Note there are two 'Log in' texts visible (the title and the button) so we have to use
        // a 'near' value here.
        $this->i_press_near_in_the_app('Log in', 'Forgotten');

        // Wait until the main page appears.
        $this->spin(
                function($context, $args) {
                    $mainmenu = $context->getSession()->getPage()->find('xpath', '//page-core-mainmenu');
                    if ($mainmenu) {
                        return 'mainpage';
                    }
                    throw new DriverException('Moodle app main page not loaded after login');
                }, false, 30);

        // Wait for JS to finish as well.
        $this->wait_for_pending_js();
    }

    /**
     * Presses standard buttons in the app.
     *
     * @Given /^I press the (?P<button_name>back|main menu|page menu) button in the app$/
     * @param string $button Button type
     * @throws DriverException If the button push doesn't work
     */
    public function i_press_the_standard_button_in_the_app(string $button) {
        $this->spin(function($context, $args) use ($button) {
            $result = $this->getSession()->evaluateScript('return window.behat.pressStandard("' .
                    $button . '");');
            if ($result !== 'OK') {
                throw new DriverException('Error pressing standard button - ' . $result);
            }
            return true;
        });
        $this->wait_for_pending_js();
    }

    /**
     * Closes a popup by clicking on the 'backdrop' behind it.
     *
     * @Given /^I close the popup in the app$/
     * @throws DriverException If there isn't a popup to close
     */
    public function i_close_the_popup_in_the_app() {
        $this->spin(function($context, $args)  {
            $result = $this->getSession()->evaluateScript('return window.behat.closePopup();');
            if ($result !== 'OK') {
                throw new DriverException('Error closing popup - ' . $result);
            }
            return true;
        });
        $this->wait_for_pending_js();
    }

    /**
     * Clicks on / touches something that is visible in the app.
     *
     * Note it is difficult to use the standard 'click on' or 'press' steps because those do not
     * distinguish visible items and the app always has many non-visible items in the DOM.
     *
     * @Given /^I press "(?P<text_string>(?:[^"]|\\")*)" in the app$/
     * @param string $text Text identifying click target
     * @throws DriverException If the press doesn't work
     */
    public function i_press_in_the_app(string $text) {
        $this->press($text);
    }

    /**
     * Clicks on / touches something that is visible in the app, near some other text.
     *
     * This is the same as the other step, but when there are multiple matches, it picks the one
     * nearest (in DOM terms) the second text. The second text should be an exact match, or a partial
     * match that only has one result.
     *
     * @Given /^I press "(?P<text_string>(?:[^"]|\\")*)" near "(?P<nearby_string>(?:[^"]|\\")*)" in the app$/
     * @param string $text Text identifying click target
     * @param string $near Text identifying a nearby unique piece of text
     * @throws DriverException If the press doesn't work
     */
    public function i_press_near_in_the_app(string $text, string $near) {
        $this->press($text, $near);
    }

    /**
     * Clicks on / touches something that is visible in the app, near some other text.
     *
     * If the $near is specified then when there are multiple matches, it picks the one
     * nearest (in DOM terms) $near. $near should be an exact match, or a partial match that only
     * has one result.
     *
     * @param behat_base $base Behat context
     * @param string $text Text identifying click target
     * @param string $near Text identifying a nearby unique piece of text
     * @throws DriverException If the press doesn't work
     */
    protected function press(string $text, string $near = '') {
        $this->spin(function($context, $args) use ($text, $near) {
            if ($near !== '') {
                $nearbit = ', "' . addslashes_js($near) . '"';
            } else {
                $nearbit = '';
            }
            $result = $context->getSession()->evaluateScript('return window.behat.press("' .
                    addslashes_js($text) . '"' . $nearbit .');');
            if ($result !== 'OK') {
                throw new DriverException('Error pressing item - ' . $result);
            }
            return true;
        });
        $this->wait_for_pending_js();
    }

    /**
     * Sets a field to the given text value in the app.
     *
     * Currently this only works for input fields which must be identified using a partial or
     * exact match on the placeholder text.
     *
     * @Given /^I set the field "(?P<field_name>(?:[^"]|\\")*)" to "(?P<text_string>(?:[^"]|\\")*)" in the app$/
     * @param string $field Text identifying field
     * @param string $value Value for field
     * @throws DriverException If the field set doesn't work
     */
    public function i_set_the_field_in_the_app(string $field, string $value) {
        $this->spin(function($context, $args) use ($field, $value) {
            $result = $this->getSession()->evaluateScript('return window.behat.setField("' .
                    addslashes_js($field) . '", "' . addslashes_js($value) . '");');
            if ($result !== 'OK') {
                throw new DriverException('Error setting field - ' . $result);
            }
            return true;
        });
        $this->wait_for_pending_js();
    }

    /**
     * Checks that the current header stripe in the app contains the expected text.
     *
     * This can be used to see if the app went to the expected page.
     *
     * @Then /^the header should be "(?P<text_string>(?:[^"]|\\")*)" in the app$/
     * @param string $text Expected header text
     * @throws DriverException If the header can't be retrieved
     * @throws ExpectationException If the header text is different to the expected value
     */
    public function the_header_should_be_in_the_app(string $text) {
        $result = $this->spin(function($context, $args) {
            $result = $this->getSession()->evaluateScript('return window.behat.getHeader();');
            if (substr($result, 0, 3) !== 'OK:') {
                throw new DriverException('Error getting header - ' . $result);
            }
            return $result;
        });
        $header = substr($result, 3);
        if (trim($header) !== trim($text)) {
            throw new ExpectationException('The header text was not as expected: \'' . $header . '\'',
                    $this->getSession()->getDriver());
        }
    }

    /**
     * Switches to a newly-opened browser tab.
     *
     * This assumes the app opened a new tab.
     *
     * @Given /^I switch to the browser tab opened by the app$/
     * @throws DriverException If there aren't exactly 2 tabs open
     */
    public function i_switch_to_the_browser_tab_opened_by_the_app() {
        $names = $this->getSession()->getWindowNames();
        if (count($names) !== 2) {
            throw new DriverException('Expected to see 2 tabs open, not ' . count($names));
        }
        $this->getSession()->switchToWindow($names[1]);
    }

    /**
     * Closes the current browser tab.
     *
     * This assumes it was opened by the app and you will now get back to the app.
     *
     * @Given /^I close the browser tab opened by the app$/
     * @throws DriverException If there aren't exactly 2 tabs open
     */
    public function i_close_the_browser_tab_opened_by_the_app() {
        $names = $this->getSession()->getWindowNames();
        if (count($names) !== 2) {
            throw new DriverException('Expected to see 2 tabs open, not ' . count($names));
        }
        $this->getSession()->getDriver()->executeScript('window.close()');
        $this->getSession()->switchToWindow($names[0]);
    }

    /**
     * Switch navigator online mode.
     *
     * @Given /^I switch offline mode to "(?P<offline_string>(?:[^"]|\\")*)"$/
     * @param string $offline New value for navigator online mode
     * @throws DriverException If the navigator.online mode is not available
     */
    public function i_switch_offline_mode(string $offline) {
        $this->getSession()->evaluateScript('appProvider.setForceOffline(' . $offline . ');');
    }
}
