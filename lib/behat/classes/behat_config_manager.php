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
 * Utils to set Behat config
 *
 * @package    core
 * @category   test
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib.php');
require_once(__DIR__ . '/behat_command.php');
require_once(__DIR__ . '/../../testing/classes/tests_finder.php');

/**
 * Behat configuration manager
 *
 * Creates/updates Behat config files getting tests
 * and steps from Moodle codebase
 *
 * @package    core
 * @category   test
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_config_manager {

    /**
     * Updates a config file
     *
     * The tests runner and the steps definitions list uses different
     * config files to avoid problems with concurrent executions.
     *
     * The steps definitions list can be filtered by component so it's
     * behat.yml is different from the $CFG->dirroot one.
     *
     * @param  string $component Restricts the obtained steps definitions to the specified component
     * @param  string $testsrunner If the config file will be used to run tests
     * @return void
     */
    public static function update_config_file($component = '', $testsrunner = true) {
        global $CFG;

        // Behat must have a separate behat.yml to have access to the whole set of features and steps definitions.
        if ($testsrunner === true) {
            $configfilepath = behat_command::get_behat_dir() . '/behat.yml';
        } else {
            // Alternative for steps definitions filtering, one for each user.
            $configfilepath = self::get_steps_list_config_filepath();
        }

        // Gets all the components with features.
        $features = array();
        $components = tests_finder::get_components_with_tests('features');
        if ($components) {
            foreach ($components as $componentname => $path) {
                $path = self::clean_path($path) . self::get_behat_tests_path();
                if (empty($featurespaths[$path]) && file_exists($path)) {

                    // Standarizes separator (some dirs. comes with OS-dependant separator).
                    $uniquekey = str_replace('\\', '/', $path);
                    $featurespaths[$uniquekey] = $path;
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
        $contents = self::get_config_file_contents($features, $stepsdefinitions);

        // Stores the file.
        if (!file_put_contents($configfilepath, $contents)) {
            behat_error(BEHAT_EXITCODE_PERMISSIONS, 'File ' . $configfilepath . ' can not be created');
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

            if (!file_exists($componentpath . self::get_behat_tests_path())) {
                continue;
            }
            $diriterator = new DirectoryIterator($componentpath . self::get_behat_tests_path());
            $regite = new RegexIterator($diriterator, '|behat_.*\.php$|');

            // All behat_*.php inside behat_config_manager::get_behat_tests_path() are added as steps definitions files.
            foreach ($regite as $file) {
                $key = $file->getBasename('.php');
                $stepsdefinitions[$key] = $file->getPathname();
            }
        }

        return $stepsdefinitions;
    }

    /**
     * Returns the behat config file path used by the steps definition list
     *
     * @return string
     */
    public static function get_steps_list_config_filepath() {
        global $USER;

        // We don't cygwin-it as it is called using exec() which uses cmd.exe.
        $userdir = behat_command::get_behat_dir() . '/users/' . $USER->id;
        make_writable_directory($userdir);

        return $userdir . '/behat.yml';
    }

    /**
     * Returns the behat config file path used by the behat cli command.
     *
     * @return string
     */
    public static function get_behat_cli_config_filepath() {
        global $CFG;

        $command = $CFG->behat_dataroot . DIRECTORY_SEPARATOR . 'behat' . DIRECTORY_SEPARATOR . 'behat.yml';

        // Cygwin uses linux-style directory separators.
        if (testing_is_cygwin()) {
            $command = str_replace('\\', '/', $command);
        }

        return $command;
    }

    /**
     * Behat config file specifing the main context class,
     * the required Behat extensions and Moodle test wwwroot.
     *
     * @param array $features The system feature files
     * @param array $stepsdefinitions The system steps definitions
     * @return string
     */
    protected static function get_config_file_contents($features, $stepsdefinitions) {
        global $CFG;

        // We require here when we are sure behat dependencies are available.
        require_once($CFG->dirroot . '/vendor/autoload.php');

        // It is possible that it has no value.
        if (empty($CFG->behat_wwwroot)) {
            $CFG->behat_wwwroot = behat_get_wwwroot();
        }

        $basedir = $CFG->dirroot . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'behat';
        $config = array(
            'default' => array(
                'paths' => array(
                    'features' => $basedir . DIRECTORY_SEPARATOR . 'features',
                    'bootstrap' => $basedir . DIRECTORY_SEPARATOR . 'features' . DIRECTORY_SEPARATOR . 'bootstrap',
                ),
                'context' => array(
                    'class' => 'behat_init_context'
                ),
                'extensions' => array(
                    'Behat\MinkExtension\Extension' => array(
                        'base_url' => $CFG->behat_wwwroot,
                        'goutte' => null,
                        'selenium2' => null
                    ),
                    'Moodle\BehatExtension\Extension' => array(
                        'formatters' => array(
                            'moodle_progress' => 'Moodle\BehatExtension\Formatter\MoodleProgressFormatter'
                        ),
                        'features' => $features,
                        'steps_definitions' => $stepsdefinitions
                    )
                ),
                'formatter' => array(
                    'name' => 'moodle_progress'
                )
            )
        );

        // In case user defined overrides respect them over our default ones.
        if (!empty($CFG->behat_config)) {
            $config = self::merge_config($config, $CFG->behat_config);
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

            // Add the param if it doesn't exists or merge branches.
            if (empty($config[$key])) {
                $config[$key] = $value;
            } else {
                $config[$key] = self::merge_config($config[$key], $localconfig[$key]);
            }
        }

        return $config;
    }

    /**
     * Cleans the path returned by get_components_with_tests() to standarize it
     *
     * @see tests_finder::get_all_directories_with_tests() it returns the path including /tests/
     * @param string $path
     * @return string The string without the last /tests part
     */
    protected final static function clean_path($path) {

        $path = rtrim($path, DIRECTORY_SEPARATOR);

        $parttoremove = DIRECTORY_SEPARATOR . 'tests';

        $substr = substr($path, strlen($path) - strlen($parttoremove));
        if ($substr == $parttoremove) {
            $path = substr($path, 0, strlen($path) - strlen($parttoremove));
        }

        return rtrim($path, DIRECTORY_SEPARATOR);
    }

    /**
     * The relative path where components stores their behat tests
     *
     * @return string
     */
    protected final static function get_behat_tests_path() {
        return DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'behat';
    }

}
