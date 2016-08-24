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

require_once(__DIR__ . '/behat_config_util.php');

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
     * @var bool Keep track of the automatic profile conversion. So we can notify user.
     */
    public static $autoprofileconversion = false;

    /**
     * @var behat_config_util keep object of behat_config_util for use.
     */
    public static $behatconfigutil = null;

    /**
     * Returns behat_config_util.
     *
     * @return behat_config_util
     */
    private static function get_behat_config_util() {
        if (!self::$behatconfigutil) {
            self::$behatconfigutil = new behat_config_util();
        }

        return self::$behatconfigutil;
    }

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
     * @param  string $tags features files including tags.
     * @param  bool   $themesuitewithallfeatures if only theme specific features need to be included in the suite.
     * @param  int    $parallelruns number of parallel runs.
     * @param  int    $run current run for which config needs to be updated.
     * @return void
     */
    public static function update_config_file($component = '', $testsrunner = true, $tags = '',
        $themesuitewithallfeatures = false, $parallelruns = 0, $run = 0) {

        global $CFG;

        // Behat must have a separate behat.yml to have access to the whole set of features and steps definitions.
        if ($testsrunner === true) {
            $configfilepath = behat_command::get_behat_dir() . '/behat.yml';
        } else {
            // Alternative for steps definitions filtering, one for each user.
            $configfilepath = self::get_steps_list_config_filepath();
        }

        $behatconfigutil = self::get_behat_config_util();
        $behatconfigutil->set_theme_suite_to_include_core_features($themesuitewithallfeatures);
        $behatconfigutil->set_tag_for_feature_filter($tags);

        // Gets all the components with features, if running the tests otherwise not required.
        $features = array();
        if ($testsrunner) {
            $features = $behatconfigutil->get_components_features();
        }

        // Gets all the components with steps definitions.
        $stepsdefinitions = $behatconfigutil->get_components_contexts($component);
        // We don't want the deprecated steps definitions here.
        if (!$testsrunner) {
            unset($stepsdefinitions['behat_deprecated']);
        }

        // Get current run.
        if (empty($run) && ($run !== false) && !empty($CFG->behatrunprocess)) {
            $run = $CFG->behatrunprocess;
        }

        // Get number of parallel runs if not passed.
        if (empty($parallelruns) && ($parallelruns !== false)) {
            $parallelruns = self::get_parallel_test_runs();
        }

        // Behat config file specifing the main context class,
        // the required Behat extensions and Moodle test wwwroot.
        $contents = $behatconfigutil->get_config_file_contents($features, $stepsdefinitions, $tags, $parallelruns, $run);

        // Stores the file.
        if (!file_put_contents($configfilepath, $contents)) {
            behat_error(BEHAT_EXITCODE_PERMISSIONS, 'File ' . $configfilepath . ' can not be created');
        }

    }

    /**
     * Search feature files for set of tags.
     *
     * @param array $features set of feature files.
     * @param string $tags list of tags (currently support && only.)
     * @return array filtered list of feature files with tags.
     * @deprecated since 3.2 MDL-55072 - please use behat_config_util.php
     * @todo MDL-55365 This will be deleted in Moodle 3.6.
     */
    public static function get_features_with_tags($features, $tags) {

        debugging('Use of get_features_with_tags is deprecated, please see behat_config_util', DEBUG_DEVELOPER);
        return self::get_behat_config_util()->filtered_features_with_tags($features, $tags);
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
     * @deprecated since 3.2 MDL-55072 - please use behat_config_util.php
     * @todo MDL-55365 This will be deleted in Moodle 3.6.
     */
    public static function get_components_steps_definitions() {

        debugging('Use of get_components_steps_definitions is deprecated, please see behat_config_util::get_components_contexts',
            DEBUG_DEVELOPER);
        return self::get_behat_config_util()->get_components_contexts();
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
     * @param int $runprocess Runprocess.
     * @return string
     */
    public static function get_behat_cli_config_filepath($runprocess = 0) {
        global $CFG;

        if ($runprocess) {
            if (isset($CFG->behat_parallel_run[$runprocess - 1 ]['behat_dataroot'])) {
                $command = $CFG->behat_parallel_run[$runprocess - 1]['behat_dataroot'];
            } else {
                $command = $CFG->behat_dataroot . $runprocess;
            }
        } else {
            $command = $CFG->behat_dataroot;
        }
        $command .= DIRECTORY_SEPARATOR . 'behat' . DIRECTORY_SEPARATOR . 'behat.yml';

        // Cygwin uses linux-style directory separators.
        if (testing_is_cygwin()) {
            $command = str_replace('\\', '/', $command);
        }

        return $command;
    }

    /**
     * Returns the path to the parallel run file which specifies if parallel test environment is enabled
     * and how many parallel runs to execute.
     *
     * @param int $runprocess run process for which behat dir is returned.
     * @return string
     */
    public final static function get_parallel_test_file_path($runprocess = 0) {
        return behat_command::get_behat_dir($runprocess) . '/parallel_environment_enabled.txt';
    }

    /**
     * Returns number of parallel runs for which site is initialised.
     *
     * @param int $runprocess run process for which behat dir is returned.
     * @return int
     */
    public final static function get_parallel_test_runs($runprocess = 0) {

        $parallelrun = 0;
        // Get parallel run info from first file and last file.
        $parallelrunconfigfile = self::get_parallel_test_file_path($runprocess);
        if (file_exists($parallelrunconfigfile)) {
            if ($parallel = file_get_contents($parallelrunconfigfile)) {
                $parallelrun = (int) $parallel;
            }
        }

        return $parallelrun;
    }

    /**
     * Drops parallel site links.
     *
     * @return bool true on success else false.
     */
    public final static function drop_parallel_site_links() {
        global $CFG;

        // Get parallel test runs from first run.
        $parallelrun = self::get_parallel_test_runs(1);

        if (empty($parallelrun)) {
            return false;
        }

        // If parallel run then remove links and original file.
        clearstatcache();
        for ($i = 1; $i <= $parallelrun; $i++) {
            // Don't delete links for specified sites, as they should be accessible.
            if (!empty($CFG->behat_parallel_run['behat_wwwroot'][$i - 1]['behat_wwwroot'])) {
                continue;
            }
            $link = $CFG->dirroot . '/' . BEHAT_PARALLEL_SITE_NAME . $i;
            if (file_exists($link) && is_link($link)) {
                @unlink($link);
            }
        }
        return true;
    }

    /**
     * Create parallel site links.
     *
     * @param int $fromrun first run
     * @param int $torun last run.
     * @return bool true for sucess, else false.
     */
    public final static function create_parallel_site_links($fromrun, $torun) {
        global $CFG;

        // Create site symlink if necessary.
        clearstatcache();
        for ($i = $fromrun; $i <= $torun; $i++) {
            // Don't create links for specified sites, as they should be accessible.
            if (!empty($CFG->behat_parallel_run['behat_wwwroot'][$i - 1]['behat_wwwroot'])) {
                continue;
            }
            $link = $CFG->dirroot.'/'.BEHAT_PARALLEL_SITE_NAME.$i;
            clearstatcache();
            if (file_exists($link)) {
                if (!is_link($link) || !is_dir($link)) {
                    echo "File exists at link location ($link) but is not a link or directory!" . PHP_EOL;
                    return false;
                }
            } else if (!symlink($CFG->dirroot, $link)) {
                // Try create link in case it's not already present.
                echo "Unable to create behat site symlink ($link)" . PHP_EOL;
                return false;
            }
        }
        return true;
    }

    /**
     * Behat config file specifing the main context class,
     * the required Behat extensions and Moodle test wwwroot.
     *
     * @param array $features The system feature files
     * @param array $stepsdefinitions The system steps definitions
     * @return string
     * @deprecated since 3.2 MDL-55072 - please use behat_config_util.php
     * @todo MDL-55365 This will be deleted in Moodle 3.6.
     */
    protected static function get_config_file_contents($features, $stepsdefinitions) {

        debugging('Use of get_config_file_contents is deprecated, please see behat_config_util', DEBUG_DEVELOPER);
        return self::get_behat_config_util()->get_config_file_contents($features, $stepsdefinitions);
    }

    /**
     * Parse $CFG->behat_config and return the array with required config structure for behat.yml
     *
     * @param string $profile profile name
     * @param array $values values for profile
     * @return array
     * @deprecated since 3.2 MDL-55072 - please use behat_config_util.php
     * @todo MDL-55365 This will be deleted in Moodle 3.6.
     */
    protected static function merge_behat_config($profile, $values) {

        debugging('Use of merge_behat_config is deprecated, please see behat_config_util', DEBUG_DEVELOPER);
        self::get_behat_config_util()->get_behat_config_for_profile($profile, $values);
    }

    /**
     * Parse $CFG->behat_profile and return the array with required config structure for behat.yml.
     *
     * $CFG->behat_profiles = array(
     *     'profile' = array(
     *         'browser' => 'firefox',
     *         'tags' => '@javascript',
     *         'wd_host' => 'http://127.0.0.1:4444/wd/hub',
     *         'capabilities' => array(
     *             'platform' => 'Linux',
     *             'version' => 44
     *         )
     *     )
     * );
     *
     * @param string $profile profile name
     * @param array $values values for profile.
     * @return array
     */
    protected static function get_behat_profile($profile, $values) {
        // Values should be an array.
        if (!is_array($values)) {
            return array();
        }

        // Check suite values.
        $behatprofilesuites = array();
        // Fill tags information.
        if (isset($values['tags'])) {
            $behatprofilesuites = array(
                'suites' => array(
                    'default' => array(
                        'filters' => array(
                            'tags' => $values['tags'],
                        )
                    )
                )
            );
        }

        // Selenium2 config values.
        $behatprofileextension = array();
        $seleniumconfig = array();
        if (isset($values['browser'])) {
            $seleniumconfig['browser'] = $values['browser'];
        }
        if (isset($values['wd_host'])) {
            $seleniumconfig['wd_host'] = $values['wd_host'];
        }
        if (isset($values['capabilities'])) {
            $seleniumconfig['capabilities'] = $values['capabilities'];
        }
        if (!empty($seleniumconfig)) {
            $behatprofileextension = array(
                'extensions' => array(
                    'Behat\MinkExtension' => array(
                        'selenium2' => $seleniumconfig,
                    )
                )
            );
        }

        return array($profile => array_merge($behatprofilesuites, $behatprofileextension));
    }

    /**
     * Attempt to split feature list into fairish buckets using timing information, if available.
     * Simply add each one to lightest buckets until all files allocated.
     * PGA = Profile Guided Allocation. I made it up just now.
     * CAUTION: workers must agree on allocation, do not be random anywhere!
     *
     * @param array $features Behat feature files array
     * @param int $nbuckets Number of buckets to divide into
     * @param int $instance Index number of this instance
     * @return array Feature files array, sorted into allocations
     */
    protected static function profile_guided_allocate($features, $nbuckets, $instance) {

        debugging('Use of profile_guided_allocate is deprecated, please see behat_config_util', DEBUG_DEVELOPER);
        return self::get_behat_config_util()->profile_guided_allocate($features, $nbuckets, $instance);
    }

    /**
     * Overrides default config with local config values
     *
     * array_merge does not merge completely the array's values
     *
     * @param mixed $config The node of the default config
     * @param mixed $localconfig The node of the local config
     * @return mixed The merge result
     * @deprecated since 3.2 MDL-55072 - please use behat_config_util.php
     * @todo MDL-55365 This will be deleted in Moodle 3.6.
     */
    protected static function merge_config($config, $localconfig) {

        debugging('Use of merge_config is deprecated, please see behat_config_util', DEBUG_DEVELOPER);
        return self::get_behat_config_util()->merge_config($config, $localconfig);
    }

    /**
     * Cleans the path returned by get_components_with_tests() to standarize it
     *
     * @see tests_finder::get_all_directories_with_tests() it returns the path including /tests/
     * @param string $path
     * @return string The string without the last /tests part
     * @deprecated since 3.2 MDL-55072 - please use behat_config_util.php
     * @todo MDL-55365 This will be deleted in Moodle 3.6.
     */
    protected final static function clean_path($path) {

        debugging('Use of clean_path is deprecated, please see behat_config_util', DEBUG_DEVELOPER);
        return self::get_behat_config_util()->clean_path($path);
    }

    /**
     * The relative path where components stores their behat tests
     *
     * @return string
     * @deprecated since 3.2 MDL-55072 - please use behat_config_util.php
     * @todo MDL-55365 This will be deleted in Moodle 3.6.
     */
    protected final static function get_behat_tests_path() {
        debugging('Use of get_behat_tests_path is deprecated, please see behat_config_util', DEBUG_DEVELOPER);
        return self::get_behat_config_util()->get_behat_tests_path();
    }

}
