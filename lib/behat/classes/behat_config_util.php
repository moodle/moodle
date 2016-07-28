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
 * @package    core_behat
 * @copyright  2016 Rajesh Taneja
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
 * @package    core_behat
 * @copyright  2016 Rajesh Taneja
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_config_util {

    /**
     * @var array list of features in core.
     */
    private $features;

    /**
     * @var array list of stepsdefinitions containing in core.
     */
    private $stepsdefinitions;

    /**
     * @var array list of components with tests.
     */
    private $componentswithtests;

    /**
     * @var bool Keep track of the automatic profile conversion. So we can notify user.
     */
    public static $autoprofileconversion = false;

    /**
     * List of components which contain behat context or features.
     *
     * @return array
     */
    private function get_components_with_tests() {
        if (empty($this->componentswithtests)) {
            $this->componentswithtests = tests_finder::get_components_with_tests('behat');
        }

        return $this->componentswithtests;
    }

    /**
     * Return list of features.
     *
     * @return array
     */
    public function get_components_features() {
        global $CFG;

        // If we already have a list created then just return that, as it's up-to-date.
        if (!empty($this->features)) {
            return $this->features;
        }

        // Gets all the components with features.
        $this->features = array();
        $featurespaths = array();
        $components = $this->get_components_with_tests();

        if ($components) {
            foreach ($components as $componentname => $path) {
                $path = $this->clean_path($path) . $this->get_behat_tests_path();
                if (empty($featurespaths[$path]) && file_exists($path)) {

                    // Standarizes separator (some dirs. comes with OS-dependant separator).
                    $uniquekey = str_replace('\\', '/', $path);
                    $featurespaths[$uniquekey] = $path;
                }
            }
            foreach ($featurespaths as $path) {
                $additional = glob("$path/*.feature");
                $this->features = array_merge($this->features, $additional);
            }
        }

        // Optionally include features from additional directories.
        if (!empty($CFG->behat_additionalfeatures)) {
            $this->features = array_merge($this->features, array_map("realpath", $CFG->behat_additionalfeatures));
        }

        return $this->features;
    }

    /**
     * Gets the list of Moodle steps definitions
     *
     * Class name as a key and the filepath as value
     *
     * Externalized from update_config_file() to use
     * it from the steps definitions web interface
     *
     * @param  string $component Restricts the obtained steps definitions to the specified component
     * @return array
     */
    public function get_components_steps_definitions($component = '') {

        // If we already have a list created then just return that, as it's up-to-date.
        if (!empty($this->stepsdefinitions)) {
            return $this->stepsdefinitions;
        }

        $components = $this->get_components_with_tests();

        $this->stepsdefinitions = array();
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
                if ($component == '' || $component === $key) {
                    $this->stepsdefinitions[$key] = $file->getPathname();
                }
            }
        }

        return $this->stepsdefinitions;
    }

    /**
     * Search feature files for set of tags.
     *
     * @param array $features set of feature files.
     * @param string $tags list of tags (currently support && only.)
     * @return array filtered list of feature files with tags.
     */
    public function get_features_with_tags($features, $tags) {
        if (empty($tags)) {
            return $features;
        }
        $newfeaturelist = array();
        // Split tags in and and or.
        $tags = explode('&&', $tags);
        $andtags = array();
        $ortags = array();
        foreach ($tags as $tag) {
            // Explode all tags seperated by , and add it to ortags.
            $ortags = array_merge($ortags, explode(',', $tag));
            // And tags will be the first one before comma(,).
            $andtags[] = preg_replace('/,.*/', '', $tag);
        }

        foreach ($features as $featurefile) {
            $contents = file_get_contents($featurefile);
            $includefeature = true;
            foreach ($andtags as $tag) {
                // If negitive tag, then ensure it don't exist.
                if (strpos($tag, '~') !== false) {
                    $tag = substr($tag, 1);
                    if ($contents && strpos($contents, $tag) !== false) {
                        $includefeature = false;
                        break;
                    }
                } else if ($contents && strpos($contents, $tag) === false) {
                    $includefeature = false;
                    break;
                }
            }

            // If feature not included then check or tags.
            if (!$includefeature && !empty($ortags)) {
                foreach ($ortags as $tag) {
                    if ($contents && (strpos($tag, '~') === false) && (strpos($contents, $tag) !== false)) {
                        $includefeature = true;
                        break;
                    }
                }
            }

            if ($includefeature) {
                $newfeaturelist[] = $featurefile;
            }
        }
        return $newfeaturelist;
    }

    /**
     * Behat config file specifing the main context class,
     * the required Behat extensions and Moodle test wwwroot.
     *
     * @param array $features The system feature files
     * @param array $stepsdefinitions The system steps definitions
     * @return string
     */
    public function get_config_file_contents($features = '', $stepsdefinitions = '', $tags = '') {
        global $CFG;

        // If features not passed then get it.
        if (empty($features)) {
            $features = $this->get_components_features();
            $features = $this->get_features_with_tags($features, $tags);
        }

        // If stepdefinitions not passed then get the list.
        if (empty($stepsdefinitions)) {
            $this->get_components_steps_definitions();
        }

        // We require here when we are sure behat dependencies are available.
        require_once($CFG->dirroot . '/vendor/autoload.php');

        $selenium2wdhost = array('wd_host' => 'http://localhost:4444/wd/hub');

        $parallelruns = behat_config_manager::get_parallel_test_runs();
        // If parallel run, then only divide features.
        if (!empty($CFG->behatrunprocess) && !empty($parallelruns)) {
            // Attempt to split into weighted buckets using timing information, if available.
            if ($alloc = $this->profile_guided_allocate($features, max(1, $parallelruns), $CFG->behatrunprocess)) {
                $features = $alloc;
            } else {
                // Divide the list of feature files amongst the parallel runners.
                srand(crc32(floor(time() / 3600 / 24) . var_export($features, true)));
                shuffle($features);
                // Pull out the features for just this worker.
                if (count($features)) {
                    $features = array_chunk($features, ceil(count($features) / max(1, $parallelruns)));
                    // Check if there is any feature file for this process.
                    if (!empty($features[$CFG->behatrunprocess - 1])) {
                        $features = $features[$CFG->behatrunprocess - 1];
                    } else {
                        $features = null;
                    }
                }
            }
            // Set proper selenium2 wd_host if defined.
            if (!empty($CFG->behat_parallel_run[$CFG->behatrunprocess - 1]['wd_host'])) {
                $selenium2wdhost = array('wd_host' => $CFG->behat_parallel_run[$CFG->behatrunprocess - 1]['wd_host']);
            }
        }

        // It is possible that it has no value as we don't require a full behat setup to list the step definitions.
        if (empty($CFG->behat_wwwroot)) {
            $CFG->behat_wwwroot = 'http://itwillnotbeused.com';
        }

        // Comments use black color, so failure path is not visible. Using color other then black/white is safer.
        // https://github.com/Behat/Behat/pull/628.
        $config = array(
            'default' => array(
                'formatters' => array(
                    'moodle_progress' => array(
                        'output_styles' => array(
                            'comment' => array('magenta'))
                    )
                ),
                'suites' => array(
                    'default' => array(
                        'paths' => $features,
                        'contexts' => array_keys($stepsdefinitions)
                    )
                ),
                'extensions' => array(
                    'Behat\MinkExtension' => array(
                        'base_url' => $CFG->behat_wwwroot,
                        'goutte' => null,
                        'selenium2' => $selenium2wdhost
                    ),
                    'Moodle\BehatExtension' => array(
                        'moodledirroot' => $CFG->dirroot,
                        'steps_definitions' => $stepsdefinitions
                    )
                )
            )
        );

        $config = $this->merge_behat_config($config);

        $config = $this->merge_behat_profiles($config);

        return Symfony\Component\Yaml\Yaml::dump($config, 10, 2);
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
    protected function get_behat_profile($profile, $values) {
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
     * @return array|bool Feature files array, sorted into allocations
     */
    public function profile_guided_allocate($features, $nbuckets, $instance) {

        $behattimingfile = defined('BEHAT_FEATURE_TIMING_FILE') &&
        @filesize(BEHAT_FEATURE_TIMING_FILE) ? BEHAT_FEATURE_TIMING_FILE : false;

        if (!$behattimingfile || !$behattimingdata = @json_decode(file_get_contents($behattimingfile), true)) {
            // No data available, fall back to relying on steps data.
            $stepfile = "";
            if (defined('BEHAT_FEATURE_STEP_FILE') && BEHAT_FEATURE_STEP_FILE) {
                $stepfile = BEHAT_FEATURE_STEP_FILE;
            }
            // We should never get this. But in case we can't do this then fall back on simple splitting.
            if (empty($stepfile) || !$behattimingdata = @json_decode(file_get_contents($stepfile), true)) {
                return false;
            }
        }

        arsort($behattimingdata); // Ensure most expensive is first.

        $realroot = realpath(__DIR__.'/../../../').'/';
        $defaultweight = array_sum($behattimingdata) / count($behattimingdata);
        $weights = array_fill(0, $nbuckets, 0);
        $buckets = array_fill(0, $nbuckets, array());
        $totalweight = 0;

        // Re-key the features list to match timing data.
        foreach ($features as $k => $file) {
            $key = str_replace($realroot, '', $file);
            $features[$key] = $file;
            unset($features[$k]);
            if (!isset($behattimingdata[$key])) {
                $behattimingdata[$key] = $defaultweight;
            }
        }

        // Sort features by known weights; largest ones should be allocated first.
        $behattimingorder = array();
        foreach ($features as $key => $file) {
            $behattimingorder[$key] = $behattimingdata[$key];
        }
        arsort($behattimingorder);

        // Finally, add each feature one by one to the lightest bucket.
        foreach ($behattimingorder as $key => $weight) {
            $file = $features[$key];
            $lightbucket = array_search(min($weights), $weights);
            $weights[$lightbucket] += $weight;
            $buckets[$lightbucket][] = $file;
            $totalweight += $weight;
        }

        if ($totalweight && !defined('BEHAT_DISABLE_HISTOGRAM') && $instance == $nbuckets) {
            echo "Bucket weightings:\n";
            foreach ($weights as $k => $weight) {
                echo $k + 1 . ": " . str_repeat('*', 70 * $nbuckets * $weight / $totalweight) . PHP_EOL;
            }
        }

        // Return the features for this worker.
        return $buckets[$instance - 1];
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
    public function merge_config($config, $localconfig) {

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
                $config[$key] = $this->merge_config($config[$key], $localconfig[$key]);
            }
        }

        return $config;
    }

    /**
     * Merges $CFG->behat_config with the one passed.
     *
     * @param array $config existing config.
     * @return array merged config with $CFG->behat_config
     */
    public function merge_behat_config($config) {
        global $CFG;

        // In case user defined overrides respect them over our default ones.
        if (!empty($CFG->behat_config)) {
            foreach ($CFG->behat_config as $profile => $values) {
                $config = $this->merge_config($config, $this->get_behat_config_for_profile($profile, $values));
            }
        }

        return $config;
    }

    /**
     * Parse $CFG->behat_config and return the array with required config structure for behat.yml
     *
     * @param string $profile profile name
     * @param array $values values for profile
     * @return array
     */
    public function get_behat_config_for_profile($profile, $values) {
        // Only add profile which are compatible with Behat 3.x
        // Just check if any of Bheat 2.5 config is set. Not checking for 3.x as it might have some other configs
        // Like : rerun_cache etc.
        if (!isset($values['filters']['tags']) && !isset($values['extensions']['Behat\MinkExtension\Extension'])) {
            return array($profile => $values);
        }

        // Parse 2.5 format and get related values.
        $oldconfigvalues = array();
        if (isset($values['extensions']['Behat\MinkExtension\Extension'])) {
            $extensionvalues = $values['extensions']['Behat\MinkExtension\Extension'];
            if (isset($extensionvalues['selenium2']['browser'])) {
                $oldconfigvalues['browser'] = $extensionvalues['selenium2']['browser'];
            }
            if (isset($extensionvalues['selenium2']['wd_host'])) {
                $oldconfigvalues['wd_host'] = $extensionvalues['selenium2']['wd_host'];
            }
            if (isset($extensionvalues['capabilities'])) {
                $oldconfigvalues['capabilities'] = $extensionvalues['capabilities'];
            }
        }

        if (isset($values['filters']['tags'])) {
            $oldconfigvalues['tags'] = $values['filters']['tags'];
        }

        if (!empty($oldconfigvalues)) {
            behat_config_manager::$autoprofileconversion = true;
            return $this->get_behat_profile($profile, $oldconfigvalues);
        }

        // If nothing set above then return empty array.
        return array();
    }

    /**
     * Merges $CFG->behat_profiles with the one passed.
     *
     * @param array $config existing config.
     * @return array merged config with $CFG->behat_profiles
     */
    public function merge_behat_profiles($config) {
        global $CFG;

        // Check for Moodle custom ones.
        if (!empty($CFG->behat_profiles) && is_array($CFG->behat_profiles)) {
            foreach ($CFG->behat_profiles as $profile => $values) {
                $config = $this->merge_config($config, $this->get_behat_profile($profile, $values));
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
    public final function clean_path($path) {

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
    public final static function get_behat_tests_path() {
        return DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'behat';
    }
}