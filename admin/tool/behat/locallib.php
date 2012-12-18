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

global $CFG;
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
     * @var string Path where each component's tests are stored */
    private static $behat_tests_path = '/tests/behat';

    /** @var array Steps types */
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
        self::update_config_file($component, false);

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
        chdir($CFG->dirroot);
        exec(self::get_behat_command() . ' --config="' . self::get_steps_list_config_filepath() . '" ' . $filteroption, $steps, $code);
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
     * Allows / disables the test environment to be accessed through the built-in server
     *
     * Built-in server must be started separately
     *
     * @param string $testenvironment enable|disable
     */
    public static function switchenvironment($testenvironment) {
        if ($testenvironment == 'enable') {
            self::start_test_mode();
        } else if ($testenvironment == 'disable') {
            self::stop_test_mode();
        }
    }

    /**
     * Updates a config file
     *
     * The tests runner and the steps definitions list uses different
     * config files to avoid problems with concurrent executions.
     *
     * The steps definitions list can be filtered by component so it's
     * behat.yml can be different from the dirroot one.
     *
     * @param string $component Restricts the obtained steps definitions to the specified component
     * @param string $testsrunner If the config file will be used to run tests
     * @throws file_exception
     */
    protected static function update_config_file($component = '', $testsrunner = true) {
        global $CFG;

        // Behat must run with the whole set of features and steps definitions.
        if ($testsrunner === true) {
            $prefix = '';
            $configfilepath = $CFG->dirroot . '/behat.yml';

        // Alternative for steps definitions filtering
        } else {
            $configfilepath = self::get_steps_list_config_filepath();
            $prefix = $CFG->dirroot .'/';
        }

        // Gets all the components with features.
        $features = array();
        $components = tests_finder::get_components_with_tests('features');
        if ($components) {
            foreach ($components as $componentname => $path) {
                $path = self::clean_path($path) . self::$behat_tests_path;
                if (empty($featurespaths[$path]) && file_exists($path)) {
                    $featurespaths[$path] = $path;
                }
            }
            $features = array_values($featurespaths);
        }

        // Gets all the components with steps definitions.
        $stepsdefinitions = array();
        $steps = self::get_components_steps_definitions();
        if ($steps) {
            foreach ($steps as $key => $filepath) {
                if ($component == '' || $component === $key) {
                    $stepsdefinitions[$key] = $filepath;
                }
            }
        }

        // Behat config file specifing the main context class,
        // the required Behat extensions and Moodle test wwwroot.
        $contents = self::get_config_file_contents($prefix, $features, $stepsdefinitions);

        // Stores the file.
        if (!file_put_contents($configfilepath, $contents)) {
            throw new file_exception('cannotcreatefile', $configfilepath);
        }

    }

    /**
     * Behat config file specifing the main context class,
     * the required Behat extensions and Moodle test wwwroot.
     *
     * @param string $prefix The filesystem prefix
     * @param array $features The system feature files
     * @param array $stepsdefinitions The system steps definitions
     * @return string
     */
    protected static function get_config_file_contents($prefix, $features, $stepsdefinitions) {
        global $CFG;

        // We require here when we are sure behat dependencies are available.
        require_once($CFG->dirroot . '/vendor/autoload.php');

        $config = array(
            'default' => array(
                'paths' => array(
                    'features' => $prefix . 'lib/behat/features',
                    'bootstrap' => $prefix . 'lib/behat/features/bootstrap',
                ),
                'context' => array(
                    'class' => 'behat_init_context'
                ),
                'extensions' => array(
                    'Behat\MinkExtension\Extension' => array(
                        'base_url' => $CFG->test_wwwroot,
                        'goutte' => null,
                        'selenium2' => null
                    ),
                    'Moodle\BehatExtension\Extension' => array(
                        'features' => $features,
                        'steps_definitions' => $stepsdefinitions
                    )
                )
            )
        );

        // In case user defined overrides respect them over our default ones.
        if (!empty($CFG->behatconfig)) {
            $config = self::merge_config($config, $CFG->behatconfig);
        }

        return Symfony\Component\Yaml\Yaml::dump($config, 10, 2);
    }

    /**
     * Overrides default config with local config values
     *
     * array_merge does not merge completely the array's values
     *
     * @param mixed $config The node of the default config
     * @param mixed $localconfig The node of the local config
     * @return mixed The merge result
     */
    protected static function merge_config($config, $localconfig) {

        if (!is_array($config) && !is_array($localconfig)) {
            return $localconfig;
        }

        // Local overrides also deeper default values.
        if (is_array($config) && !is_array($localconfig)) {
            return $localconfig;
        }

        foreach ($localconfig as $key => $value) {

            // If defaults are not as deep as local values let locals override.
            if (!is_array($config)) {
                unset($config);
            }

            // Add the param if it doesn't exists.
            if (empty($config[$key])) {
                $config[$key] = $value;

            // Merge branches if the key exists in both branches.
            } else {
                $config[$key] = self::merge_config($config[$key], $localconfig[$key]);
            }
        }

        return $config;
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
     * Checks if $CFG->test_wwwroot is available
     *
     * @return boolean
     */
    public static function is_server_running() {
        global $CFG;

        $request = new curl();
        $request->get($CFG->test_wwwroot);
        return (true && !$request->get_errno());
    }

    /**
     * Cleans the path returned by get_components_with_tests() to standarize it
     *
     * {@see tests_finder::get_all_directories_with_tests()} it returns the path including /tests/
     * @param string $path
     * @return string The string without the last /tests part
     */
    protected static function clean_path($path) {

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
    protected static function test_environment_problem() {
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
    protected static function check_behat_setup($checkphp = false) {
        global $CFG;

        if ($checkphp && version_compare(PHP_VERSION, '5.4.0', '<')) {
            throw new Exception(get_string('wrongphpversion', 'tool_behat'));
        }

        // Moodle setting.
        if (!tool_behat::are_behat_dependencies_installed()) {

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
        chdir($CFG->dirroot);
        exec(self::get_behat_command() . ' --help', $output, $code);
        chdir($currentcwd);

        if ($code != 0) {
            notice(get_string('wrongbehatsetup', 'tool_behat'));
        }
    }

    /**
     * Enables test mode
     *
     * Starts the test mode checking the composer installation and
     * the phpunit test environment and updating the available
     * features and steps definitions.
     *
     * Stores a file in dataroot/behat to allow Moodle to switch
     * to the test environment when using cli-server
     *
     * @throws file_exception
     */
    protected static function start_test_mode() {
        global $CFG;

        // Checks the behat set up and the PHP version.
        self::check_behat_setup(true);

        // Check that PHPUnit test environment is correctly set up.
        self::test_environment_problem();

        // Updates all the Moodle features and steps definitions.
        self::update_config_file();

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
     * Disables test mode
     * @throws file_exception
     */
    protected static function stop_test_mode() {

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
    public static function is_test_environment_running() {
        global $CFG;

        if (!empty($CFG->originaldataroot)) {
            return true;
        }

        return false;
    }

    /**
     * Has the site installed composer with --dev option
     * @return boolean
     */
    public static function are_behat_dependencies_installed() {
        if (!is_dir(__DIR__ . '/../../../vendor/behat')) {
            return false;
        }
        return true;
    }

    /**
     * Returns the path to the file which specifies if test environment is enabled
     *
     * The file is in dataroot/behat/ but we need to
     * know if test mode is running because then we swap
     * it to phpunit_dataroot and we need the original value
     *
     * @return string
     */
    protected static function get_test_filepath() {
        global $CFG;

        if (self::is_test_environment_running()) {
            $prefix = $CFG->originaldataroot;
        } else {
            $prefix = $CFG->dataroot;
        }

        return $prefix . '/behat/test_environment_enabled.txt';
    }

    /**
     * Ensures the behat dir exists in moodledata
     * @throws file_exception
     * @return string Full path
     */
    protected static function get_behat_dir() {
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
     * Returns the executable path
     * @return string
     */
    protected static function get_behat_command() {
        return 'vendor' . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'behat';
    }

    /**
     * Returns the behat config file path used by the steps definition list
     * @return string
     */
    protected static function get_steps_list_config_filepath() {
        return self::get_behat_dir() . '/behat.yml';
    }

}
