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
            $configfilepath = behat_command::get_behat_dir($run) . '/behat.yml';
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
            $parallelruns = self::get_behat_run_config_value('parallel');
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
     * @deprecated since 3.2 - please use behat_config_util.php
     */
    public static function get_features_with_tags() {
        throw new coding_exception('get_features_with_tags() can not be used anymore. ' .
            'Please use behat_config_util instead.');
    }

    /**
     * @deprecated since 3.2 - please use behat_config_util.php
     */
    public static function get_components_steps_definitions() {
        throw new coding_exception('get_components_steps_definitions() can not be used anymore. ' .
            'Please use behat_config_util instead.');
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
     * @return string
     */
    public final static function get_behat_run_config_file_path() {
        return behat_command::get_parent_behat_dir() . '/run_environment.json';
    }

    /**
     * Get config for parallel run.
     *
     * @param string $key Key to store
     * @return string|int|array value which is stored.
     */
    public final static function get_behat_run_config_value($key) {
        $parallelrunconfigfile = self::get_behat_run_config_file_path();

        if (file_exists($parallelrunconfigfile)) {
            if ($parallelrunconfigs = @json_decode(file_get_contents($parallelrunconfigfile), true)) {
                if (isset($parallelrunconfigs[$key])) {
                    return $parallelrunconfigs[$key];
                }
            }
        }

        return false;
    }

    /**
     * Save/update config for parallel run.
     *
     * @param string $key Key to store
     * @param string|int|array $value to store.
     */
    public final static function set_behat_run_config_value($key, $value) {
        $parallelrunconfigs = array();
        $parallelrunconfigfile = self::get_behat_run_config_file_path();

        // Get any existing config first.
        if (file_exists($parallelrunconfigfile)) {
            $parallelrunconfigs = @json_decode(file_get_contents($parallelrunconfigfile), true);
        }
        $parallelrunconfigs[$key] = $value;

        @file_put_contents($parallelrunconfigfile, json_encode($parallelrunconfigs, JSON_PRETTY_PRINT));
    }

    /**
     * Drops parallel site links.
     *
     * @return bool true on success else false.
     */
    public final static function drop_parallel_site_links() {
        global $CFG;

        // Get parallel test runs.
        $parallelrun = self::get_behat_run_config_value('parallel');

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
     * @deprecated since 3.2 - please use behat_config_util.php
     */
    protected static function get_config_file_contents() {
        throw new coding_exception('get_config_file_contents() can not be used anymore. ' .
            'Please use behat_config_util instead.');
    }

    /**
     * @deprecated since 3.2 - please use behat_config_util.php
     */
    protected static function merge_behat_config() {
        throw new coding_exception('merge_behat_config() can not be used anymore. ' .
            'Please use behat_config_util instead.');
    }

    /**
     * @deprecated since 3.2 - please use behat_config_util.php
     */
    protected static function get_behat_profile() {
        throw new coding_exception('get_behat_profile() can not be used anymore. ' .
            'Please use behat_config_util instead.');
    }

    /**
     * @deprecated since 3.2 - please use behat_config_util.php
     */
    protected static function profile_guided_allocate() {
        throw new coding_exception('profile_guided_allocate() can not be used anymore. ' .
            'Please use behat_config_util instead.');
    }

    /**
     * @deprecated since 3.2 - please use behat_config_util.php
     */
    protected static function merge_config() {
        throw new coding_exception('merge_config() can not be used anymore. ' .
            'Please use behat_config_util instead.');
    }

    /**
     * @deprecated since 3.2 - please use behat_config_util.php
     */
    protected final static function clean_path() {
        throw new coding_exception('clean_path() can not be used anymore. ' .
            'Please use behat_config_util instead.');
    }

    /**
     * @deprecated since 3.2 - please use behat_config_util.php
     */
    protected final static function get_behat_tests_path() {
        throw new coding_exception('get_behat_tests_path() can not be used anymore. ' .
            'Please use behat_config_util instead.');
    }

}
