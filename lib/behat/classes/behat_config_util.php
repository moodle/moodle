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
 * @package    core
 * @copyright  2016 Rajesh Taneja
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_config_util {

    /**
     * @var array list of features in core.
     */
    private $features;

    /**
     * @var array list of contexts in core.
     */
    private $contexts;

    /**
     * @var array list of theme specific contexts.
     */
    private $themecontexts;

    /**
     * @var array list of overridden theme contexts.
     */
    private $overriddenthemescontexts;

    /**
     * @var array list of components with tests.
     */
    private $componentswithtests;

    /**
     * @var array|string keep track of theme to return suite with all core features included or not.
     */
    private $themesuitewithallfeatures = array();

    /**
     * @var string filter features which have tags.
     */
    private $tags = '';

    /**
     * @var int number of parallel runs.
     */
    private $parallelruns = 0;

    /**
     * @var int current run.
     */
    private $currentrun = 0;

    /**
     * @var string used to specify if behat should be initialised with all themes.
     */
    const ALL_THEMES_TO_RUN = 'ALL';

    /**
     * Set value for theme suite to include all core features. This should be used if your want all core features to be
     * run with theme.
     *
     * @param bool $themetoset
     */
    public function set_theme_suite_to_include_core_features($themetoset) {
        // If no value passed to --add-core-features-to-theme or ALL is passed, then set core features for all themes.
        if (!empty($themetoset)) {
            if (is_number($themetoset) || is_bool($themetoset) || (self::ALL_THEMES_TO_RUN === strtoupper($themetoset))) {
                $this->themesuitewithallfeatures = self::ALL_THEMES_TO_RUN;
            } else {
                $this->themesuitewithallfeatures = explode(',', $themetoset);
                $this->themesuitewithallfeatures = array_map('trim', $this->themesuitewithallfeatures);
            }
        }
    }

    /**
     * Set the value for tags, so features which are returned will be using filtered by this.
     *
     * @param string $tags
     */
    public function set_tag_for_feature_filter($tags) {
        $this->tags = $tags;
    }

    /**
     * Set parallel run to be used for generating config.
     *
     * @param int $parallelruns number of parallel runs.
     * @param int $currentrun current run
     */
    public function set_parallel_run($parallelruns, $currentrun) {

        if ($parallelruns < $currentrun) {
            behat_error(BEHAT_EXITCODE_REQUIREMENT,
                'Parallel runs('.$parallelruns.') should be more then current run('.$currentrun.')');
        }

        $this->parallelruns = $parallelruns;
        $this->currentrun = $currentrun;
    }

    /**
     * Return parallel runs
     *
     * @return int number of parallel runs.
     */
    public function get_number_of_parallel_run() {
        // Get number of parallel runs if not passed.
        if (empty($this->parallelruns) && ($this->parallelruns !== false)) {
            $this->parallelruns = behat_config_manager::get_behat_run_config_value('parallel');
        }

        return $this->parallelruns;
    }

    /**
     * Return current run
     *
     * @return int current run.
     */
    public function get_current_run() {
        global $CFG;

        // Get number of parallel runs if not passed.
        if (empty($this->currentrun) && ($this->currentrun !== false) && !empty($CFG->behatrunprocess)) {
            $this->currentrun = $CFG->behatrunprocess;
        }

        return $this->currentrun;
    }

    /**
     * Return list of features.
     *
     * @param string $tags tags.
     * @return array
     */
    public function get_components_features($tags = '') {
        global $CFG;

        // If we already have a list created then just return that, as it's up-to-date.
        // If tags are passed then it's a new filter of features we need.
        if (!empty($this->features) && empty($tags)) {
            return $this->features;
        }

        // Gets all the components with features.
        $features = array();
        $featurespaths = array();
        $components = $this->get_components_with_tests();

        if ($components) {
            foreach ($components as $componentname => $path) {
                $path = $this->clean_path($path) . self::get_behat_tests_path();
                if (empty($featurespaths[$path]) && file_exists($path)) {
                    list($key, $featurepath) = $this->get_clean_feature_key_and_path($path);
                    $featurespaths[$key] = $featurepath;
                }
            }
            foreach ($featurespaths as $path) {
                $additional = glob("$path/*.feature");

                $additionalfeatures = array();
                foreach ($additional as $featurepath) {
                    list($key, $path) = $this->get_clean_feature_key_and_path($featurepath);
                    $additionalfeatures[$key] = $path;
                }

                $features = array_merge($features, $additionalfeatures);
            }
        }

        // Optionally include features from additional directories.
        if (!empty($CFG->behat_additionalfeatures)) {
            $additional = array_map("realpath", $CFG->behat_additionalfeatures);
            $additionalfeatures = array();
            foreach ($additional as $featurepath) {
                list($key, $path) = $this->get_clean_feature_key_and_path($featurepath);
                $additionalfeatures[$key] = $path;
            }
            $features = array_merge($features, $additionalfeatures);
        }

        // Sanitize feature key.
        $cleanfeatures = array();
        foreach ($features as $featurepath) {
            list($key, $path) = $this->get_clean_feature_key_and_path($featurepath);
            $cleanfeatures[$key] = $path;
        }

        // Sort feature list.
        ksort($cleanfeatures);

        $this->features = $cleanfeatures;

        // If tags are passed then filter features which has sepecified tags.
        if (!empty($tags)) {
            $cleanfeatures = $this->filtered_features_with_tags($cleanfeatures, $tags);
        }

        return $cleanfeatures;
    }

    /**
     * Return feature key for featurepath
     *
     * @param string $featurepath
     * @return array key and featurepath.
     */
    public function get_clean_feature_key_and_path($featurepath) {
        global $CFG;

        // Fix directory path.
        $featurepath = testing_cli_fix_directory_separator($featurepath);
        $dirroot = testing_cli_fix_directory_separator($CFG->dirroot . DIRECTORY_SEPARATOR);

        $key = basename($featurepath, '.feature');

        // Get relative path.
        $featuredirname = str_replace($dirroot , '', $featurepath);
        // Get 5 levels of feature path to ensure we have a unique key.
        for ($i = 0; $i < 5; $i++) {
            if (($featuredirname = dirname($featuredirname)) && $featuredirname !== '.') {
                if ($basename = basename($featuredirname)) {
                    $key .= '_' . $basename;
                }
            }
        }

        return array($key, $featurepath);
    }

    /**
     * Get component contexts.
     *
     * @param string $component component name.
     * @return array
     */
    private function get_component_contexts($component) {

        if (empty($component)) {
            return $this->contexts;
        }

        $componentcontexts = array();
        foreach ($this->contexts as $key => $path) {
            if ($component == '' || $component === $key) {
                $componentcontexts[$key] = $path;
            }
        }

        return $componentcontexts;
    }

    /**
     * Gets the list of Moodle behat contexts
     *
     * Class name as a key and the filepath as value
     *
     * Externalized from update_config_file() to use
     * it from the steps definitions web interface
     *
     * @param  string $component Restricts the obtained steps definitions to the specified component
     * @return array
     */
    public function get_components_contexts($component = '') {

        // If we already have a list created then just return that, as it's up-to-date.
        if (!empty($this->contexts)) {
            return $this->get_component_contexts($component);
        }

        $components = $this->get_components_with_tests();

        $this->contexts = array();
        foreach ($components as $componentname => $componentpath) {
            if (false !== strpos($componentname, 'theme_')) {
                continue;
            }
            $componentpath = self::clean_path($componentpath);

            if (!file_exists($componentpath . self::get_behat_tests_path())) {
                continue;
            }
            $diriterator = new DirectoryIterator($componentpath . self::get_behat_tests_path());
            $regite = new RegexIterator($diriterator, '|^behat_.*\.php$|');

            // All behat_*.php inside self::get_behat_tests_path() are added as steps definitions files.
            foreach ($regite as $file) {
                $key = $file->getBasename('.php');
                $this->contexts[$key] = $file->getPathname();
            }
        }

        // Sort contexts with there name.
        ksort($this->contexts);

        return $this->get_component_contexts($component);
    }

    /**
     * Behat config file specifing the main context class,
     * the required Behat extensions and Moodle test wwwroot.
     *
     * @param array $features The system feature files
     * @param array $contexts The system steps definitions
     * @param string $tags filter features with specified tags.
     * @param int $parallelruns number of parallel runs.
     * @param int $currentrun current run for which config file is needed.
     * @return string
     */
    public function get_config_file_contents($features = '', $contexts = '', $tags = '', $parallelruns = 0, $currentrun = 0) {
        global $CFG;

        // Set current run and parallel run.
        if (!empty($parallelruns) && !empty($currentrun)) {
            $this->set_parallel_run($parallelruns, $currentrun);
        }

        // If tags defined then use them. This is for BC.
        if (!empty($tags)) {
            $this->set_tag_for_feature_filter($tags);
        }

        // If features not passed then get it. Empty array means we don't need to include features.
        if (empty($features) && !is_array($features)) {
            $features = $this->get_components_features();
        } else {
            $this->features = $features;
        }

        // If stepdefinitions not passed then get the list.
        if (empty($contexts)) {
            $this->get_components_contexts();
        } else {
            $this->contexts = $contexts;
        }

        // We require here when we are sure behat dependencies are available.
        require_once($CFG->dirroot . '/vendor/autoload.php');

        $config = $this->build_config();

        $config = $this->merge_behat_config($config);

        $config = $this->merge_behat_profiles($config);

        // Return config array for phpunit, so it can be tested.
        if (defined('PHPUNIT_TEST') && PHPUNIT_TEST) {
            return $config;
        }

        return Symfony\Component\Yaml\Yaml::dump($config, 10, 2);
    }

    /**
     * Search feature files for set of tags.
     *
     * @param array $features set of feature files.
     * @param string $tags list of tags (currently support && only.)
     * @return array filtered list of feature files with tags.
     */
    public function filtered_features_with_tags($features = '', $tags = '') {

        // This is for BC. Features if not passed then we already have a list in this object.
        if (empty($features)) {
            $features = $this->features;
        }

        // If no tags defined then return full list.
        if (empty($tags) && empty($this->tags)) {
            return $features;
        }

        // If no tags passed by the caller, then it's already set.
        if (empty($tags)) {
            $tags = $this->tags;
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

        foreach ($features as $key => $featurefile) {
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
                $newfeaturelist[$key] = $featurefile;
            }
        }
        return $newfeaturelist;
    }

    /**
     * Build config for behat.yml.
     *
     * @param int $parallelruns how many parallel runs feature needs to be divided.
     * @param int $currentrun current run for which features should be returned.
     * @return array
     */
    protected function build_config($parallelruns = 0, $currentrun = 0) {
        global $CFG;

        if (!empty($parallelruns) && !empty($currentrun)) {
            $this->set_parallel_run($parallelruns, $currentrun);
        } else {
            $currentrun = $this->get_current_run();
            $parallelruns = $this->get_number_of_parallel_run();
        }

        $webdriverwdhost = array('wd_host' => 'http://localhost:4444/wd/hub');
        // If parallel run, then set wd_host if specified.
        if (!empty($currentrun) && !empty($parallelruns)) {
            // Set proper webdriver wd_host if defined.
            if (!empty($CFG->behat_parallel_run[$currentrun - 1]['wd_host'])) {
                $webdriverwdhost = array('wd_host' => $CFG->behat_parallel_run[$currentrun - 1]['wd_host']);
            }
        }

        // It is possible that it has no value as we don't require a full behat setup to list the step definitions.
        if (empty($CFG->behat_wwwroot)) {
            $CFG->behat_wwwroot = 'http://itwillnotbeused.com';
        }

        $suites = $this->get_behat_suites($parallelruns, $currentrun);

        $selectortypes = ['named_partial', 'named_exact'];
        $allpaths = [];
        foreach (array_keys($suites) as $theme) {
            // Remove selectors from step definitions.
            foreach ($selectortypes as $selectortype) {
                // Don't include selector classes.
                $selectorclass = self::get_behat_theme_selector_override_classname($theme, $selectortype);
                if (isset($suites[$theme]['contexts'][$selectorclass])) {
                    unset($suites[$theme]['contexts'][$selectorclass]);
                }
            }

            // Get a list of all step definition paths.
            $allpaths = array_merge($allpaths, $suites[$theme]['contexts']);

            // Convert the contexts array to a list of names only.
            $suites[$theme]['contexts'] = array_keys($suites[$theme]['contexts']);
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
                'suites' => $suites,
                'extensions' => array(
                    'Behat\MinkExtension' => array(
                        'base_url' => $CFG->behat_wwwroot,
                        'goutte' => null,
                        'webdriver' => $webdriverwdhost
                    ),
                    'Moodle\BehatExtension' => array(
                        'moodledirroot' => $CFG->dirroot,
                        'steps_definitions' => $allpaths,
                    )
                )
            )
        );

        return $config;
    }

    /**
     * Divide features between the runs and return list.
     *
     * @param array $features list of features to be divided.
     * @param int $parallelruns how many parallel runs feature needs to be divided.
     * @param int $currentrun current run for which features should be returned.
     * @return array
     */
    protected function get_features_for_the_run($features, $parallelruns, $currentrun) {

        // If no features are passed then just return.
        if (empty($features)) {
            return $features;
        }

        $allocatedfeatures = $features;

        // If parallel run, then only divide features.
        if (!empty($currentrun) && !empty($parallelruns)) {

            $featurestodivide['withtags'] = $features;
            $allocatedfeatures = array();

            // If tags are set then split features with tags first.
            if (!empty($this->tags)) {
                $featurestodivide['withtags'] = $this->filtered_features_with_tags($features);
                $featurestodivide['withouttags'] = $this->remove_blacklisted_features_from_list($features,
                    $featurestodivide['withtags']);
            }

            // Attempt to split into weighted buckets using timing information, if available.
            foreach ($featurestodivide as $tagfeatures) {
                if ($alloc = $this->profile_guided_allocate($tagfeatures, max(1, $parallelruns), $currentrun)) {
                    $allocatedfeatures = array_merge($allocatedfeatures, $alloc);
                } else {
                    // Divide the list of feature files amongst the parallel runners.
                    // Pull out the features for just this worker.
                    if (count($tagfeatures)) {
                        $splitfeatures = array_chunk($tagfeatures, ceil(count($tagfeatures) / max(1, $parallelruns)));

                        // Check if there is any feature file for this process.
                        if (!empty($splitfeatures[$currentrun - 1])) {
                            $allocatedfeatures = array_merge($allocatedfeatures, $splitfeatures[$currentrun - 1]);
                        }
                    }
                }
            }
        }

        return $allocatedfeatures;
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

        // Automatically set tags information to skip app testing if necessary. We skip app testing
        // if the browser is not Chrome. (Note: We also skip if it's not configured, but that is
        // done on the theme/suite level.)
        if (empty($values['browser']) || $values['browser'] !== 'chrome') {
            if (!empty($values['tags'])) {
                $values['tags'] .= ' && ~@app';
            } else {
                $values['tags'] = '~@app';
            }
        }

        // Automatically add Chrome command line option to skip the prompt about allowing file
        // storage - needed for mobile app testing (won't hurt for everything else either).
        // We also need to disable web security, otherwise it can't make CSS requests to the server
        // on localhost due to CORS restrictions.
        if (!empty($values['browser']) && $values['browser'] === 'chrome') {
            $values = array_merge_recursive(
                [
                    'capabilities' => [
                        'extra_capabilities' => [
                            'chromeOptions' => [
                                'args' => [
                                    'unlimited-storage',
                                    'disable-web-security',
                                ],
                            ],
                        ],
                    ],
                ],
                $values
            );

            // If the mobile app is enabled, check its version and add appropriate tags.
            if ($mobiletags = $this->get_mobile_version_tags()) {
                if (!empty($values['tags'])) {
                    $values['tags'] .= ' && ' . $mobiletags;
                } else {
                    $values['tags'] = $mobiletags;
                }
            }

            $values['capabilities']['extra_capabilities']['chromeOptions']['args'] = array_map(function($arg): string {
                if (substr($arg, 0, 2) === '--') {
                    return substr($arg, 2);
                }
                return $arg;
            }, $values['capabilities']['extra_capabilities']['chromeOptions']['args']);
            sort($values['capabilities']['extra_capabilities']['chromeOptions']['args']);
        }

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
                        'webdriver' => $seleniumconfig,
                    )
                )
            );
        }

        return array($profile => array_merge($behatprofilesuites, $behatprofileextension));
    }

    /**
     * Gets version tags to use for the mobile app.
     *
     * This is based on the current mobile app version (from its package.json) and all known
     * mobile app versions (based on the list appversions.json in the lib/behat directory).
     *
     * @param bool $verbose If true, outputs information about installed app version
     * @return string List of tags or '' if not supporting mobile
     */
    protected function get_mobile_version_tags($verbose = true) : string {
        global $CFG;

        if (!empty($CFG->behat_ionic_dirroot)) {
            // Get app version from package.json.
            $jsonpath = $CFG->behat_ionic_dirroot . '/package.json';
            $json = @file_get_contents($jsonpath);
            if (!$json) {
                throw new coding_exception('Unable to load app version from ' . $jsonpath);
            }
            $package = json_decode($json);
            if ($package === null || empty($package->version)) {
                throw new coding_exception('Invalid app package data in ' . $jsonpath);
            }
            $installedversion = $package->version;
        } else if (!empty($CFG->behat_ionic_wwwroot)) {
            // Get app version from env.json inside wwwroot.
            $jsonurl = $CFG->behat_ionic_wwwroot . '/assets/env.json';
            $json = @file_get_contents($jsonurl);
            if (!$json) {
                // Fall back to ionic 3 config file.
                $jsonurl = $CFG->behat_ionic_wwwroot . '/config.json';
                $json = @file_get_contents($jsonurl);
                if (!$json) {
                    throw new coding_exception('Unable to load app version from ' . $jsonurl);
                }
                $config = json_decode($json);
                if ($config === null || empty($config->versionname)) {
                    throw new coding_exception('Invalid app config data in ' . $jsonurl);
                }
                $installedversion = str_replace('-dev', '', $config->versionname);
            } else {
                $env = json_decode($json);
                if (empty($env->build->version ?? null)) {
                    throw new coding_exception('Invalid app config data in ' . $jsonurl);
                }
                $installedversion = $env->build->version;
            }
        } else {
            return '';
        }

        // Read all feature files to check which mobile tags are used. (Note: This could be cached
        // but ideally, it is the sort of thing that really ought to be refreshed by doing a new
        // Behat init. Also, at time of coding it only takes 0.3 seconds and only if app enabled.)
        $usedtags = [];
        foreach ($this->features as $filepath) {
            $feature = file_get_contents($filepath);
            // This may incorrectly detect versions used e.g. in a comment or something, but it
            // doesn't do much harm if we have extra ones.
            if (preg_match_all('~@app_(?:from|upto)(?:[0-9]+(?:\.[0-9]+)*)~', $feature, $matches)) {
                foreach ($matches[0] as $tag) {
                    // Store as key in array so we don't get duplicates.
                    $usedtags[$tag] = true;
                }
            }
        }

        // Set up relevant tags for each version.
        $tags = [];
        foreach ($usedtags as $usedtag => $ignored) {
            if (!preg_match('~^@app_(from|upto)([0-9]+(?:\.[0-9]+)*)$~', $usedtag, $matches)) {
                throw new coding_exception('Unexpected tag format');
            }
            $direction = $matches[1];
            $version = $matches[2];

            switch (version_compare($installedversion, $version)) {
                case -1:
                    // Installed version OLDER than the one being considered, so do not
                    // include any scenarios that only run from the considered version up.
                    if ($direction === 'from') {
                        $tags[] = '~@app_from' . $version;
                    }
                    break;

                case 0:
                    // Installed version EQUAL to the one being considered - no tags need
                    // excluding.
                    break;

                case 1:
                    // Installed version NEWER than the one being considered, so do not
                    // include any scenarios that only run up to that version.
                    if ($direction === 'upto') {
                        $tags[] = '~@app_upto' . $version;
                    }
                    break;
            }
        }

        if ($verbose) {
            mtrace('Configured app tests for version ' . $installedversion);
        }

        return join(' && ', $tags);
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

        // No profile guided allocation is required in phpunit.
        if (defined('PHPUNIT_TEST') && PHPUNIT_TEST) {
            return false;
        }

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

        if ($totalweight && !defined('BEHAT_DISABLE_HISTOGRAM') && $instance == $nbuckets
                && (!defined('PHPUNIT_TEST') || !PHPUNIT_TEST)) {
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
                $values = $this->fix_legacy_profile_data($profile, $values);
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
            if (isset($extensionvalues['webdriver']['browser'])) {
                $oldconfigvalues['browser'] = $extensionvalues['webdriver']['browser'];
            }
            if (isset($extensionvalues['webdriver']['wd_host'])) {
                $oldconfigvalues['wd_host'] = $extensionvalues['webdriver']['wd_host'];
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
     * Check for and attempt to fix legacy profile data.
     *
     * The Mink Driver used for W3C no longer uses the `selenium2` naming but otherwise is backwards compatibly.
     *
     * Emit a warning that users should update their configuration.
     *
     * @param   string $profilename The name of this profile
     * @param   array $data The profile data for this profile
     * @return  array Th eamended profile data
     */
    protected function fix_legacy_profile_data(string $profilename, array $data): array {
        // Check for legacy instaclick profiles.
        if (!array_key_exists('Behat\MinkExtension', $data['extensions'])) {
            return $data;
        }
        if (array_key_exists('selenium2', $data['extensions']['Behat\MinkExtension'])) {
            echo("\n\n");
            echo("=> Warning: Legacy selenium2 profileuration was found for {$profilename} profile.\n");
            echo("=> This has been renamed from 'selenium2' to 'webdriver'.\n");
            echo("=> You should update your Behat configuration.\n");
            echo("\n");
            $data['extensions']['Behat\MinkExtension']['webdriver'] = $data['extensions']['Behat\MinkExtension']['selenium2'];
            unset($data['extensions']['Behat\MinkExtension']['selenium2']);
        }

        return $data;
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
    public static final function get_behat_tests_path() {
        return DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'behat';
    }

    /**
     * Return context name of behat_theme selector to use.
     *
     * @param string $themename name of the theme.
     * @param string $selectortype The type of selector (partial or exact at this stage)
     * @param bool $includeclass if class should be included.
     * @return string
     */
    public static final function get_behat_theme_selector_override_classname($themename, $selectortype, $includeclass = false) {
        global $CFG;

        if ($selectortype !== 'named_partial' && $selectortype !== 'named_exact') {
            throw new coding_exception("Unknown selector override type '{$selectortype}'");
        }

        $overridebehatclassname = "behat_theme_{$themename}_behat_{$selectortype}_selectors";

        if ($includeclass) {
            $themeoverrideselector = $CFG->dirroot . DIRECTORY_SEPARATOR . 'theme' . DIRECTORY_SEPARATOR . $themename .
                self::get_behat_tests_path() . DIRECTORY_SEPARATOR . $overridebehatclassname . '.php';

            if (file_exists($themeoverrideselector)) {
                require_once($themeoverrideselector);
            }
        }

        return $overridebehatclassname;
    }

    /**
     * List of components which contain behat context or features.
     *
     * @return array
     */
    protected function get_components_with_tests() {
        if (empty($this->componentswithtests)) {
            $this->componentswithtests = tests_finder::get_components_with_tests('behat');
        }

        return $this->componentswithtests;
    }

    /**
     * Remove list of blacklisted features from the feature list.
     *
     * @param array $features list of original features.
     * @param array|string $blacklist list of features which needs to be removed.
     * @return array features - blacklisted features.
     */
    protected function remove_blacklisted_features_from_list($features, $blacklist) {

        // If no blacklist passed then return.
        if (empty($blacklist)) {
            return $features;
        }

        // If there is no feature in suite then just return what was passed.
        if (empty($features)) {
            return $features;
        }

        if (!is_array($blacklist)) {
            $blacklist = array($blacklist);
        }

        // Remove blacklisted features.
        foreach ($blacklist as $blacklistpath) {

            list($key, $featurepath) = $this->get_clean_feature_key_and_path($blacklistpath);

            if (isset($features[$key])) {
                $features[$key] = null;
                unset($features[$key]);
            } else {
                $featurestocheck = $this->get_components_features();
                if (!isset($featurestocheck[$key]) && (!defined('PHPUNIT_TEST') || !PHPUNIT_TEST)) {
                    behat_error(BEHAT_EXITCODE_REQUIREMENT, 'Blacklisted feature "' . $blacklistpath . '" not found.');
                }
            }
        }

        return $features;
    }

    /**
     * Return list of behat suites. Multiple suites are returned if theme
     * overrides default step definitions/features.
     *
     * @param int $parallelruns number of parallel runs
     * @param int $currentrun current run.
     * @return array list of suites.
     */
    protected function get_behat_suites($parallelruns = 0, $currentrun = 0) {
        $features = $this->get_components_features();

        // Get number of parallel runs and current run.
        if (!empty($parallelruns) && !empty($currentrun)) {
            $this->set_parallel_run($parallelruns, $currentrun);
        } else {
            $parallelruns = $this->get_number_of_parallel_run();
            $currentrun = $this->get_current_run();;
        }

        $themefeatures = array();
        $themecontexts = array();

        $themes = $this->get_list_of_themes();

        // Create list of theme suite features and contexts.
        foreach ($themes as $theme) {
            // Get theme features and contexts.
            $themefeatures[$theme] = $this->get_behat_features_for_theme($theme);
            $themecontexts[$theme] = $this->get_behat_contexts_for_theme($theme);
        }

        // Remove list of theme features for default suite, as default suite should not run theme specific features.
        foreach ($themefeatures as $themename => $removethemefeatures) {
            if (!empty($removethemefeatures['features'])) {
                $features = $this->remove_blacklisted_features_from_list($features, $removethemefeatures['features']);
            }
        }

        // Set suite for each theme.
        $suites = array();
        foreach ($themes as $theme) {
            // Get list of features which will be included in theme.
            // If theme suite with all features or default theme, then we want all core features to be part of theme suite.
            if ((is_string($this->themesuitewithallfeatures) && ($this->themesuitewithallfeatures === self::ALL_THEMES_TO_RUN)) ||
                in_array($theme, $this->themesuitewithallfeatures) || ($this->get_default_theme() === $theme)) {
                // If there is no theme specific feature. Then it's just core features.
                if (empty($themefeatures[$theme]['features'])) {
                    $themesuitefeatures = $features;
                } else {
                    $themesuitefeatures = array_merge($features, $themefeatures[$theme]['features']);
                }
            } else {
                $themesuitefeatures = $themefeatures[$theme]['features'];
            }

            // Remove blacklisted features.
            $themesuitefeatures = $this->remove_blacklisted_features_from_list($themesuitefeatures,
                $themefeatures[$theme]['blacklistfeatures']);

            // Return sub-set of features if parallel run.
            $themesuitefeatures = $this->get_features_for_the_run($themesuitefeatures, $parallelruns, $currentrun);

            // Default theme is part of default suite.
            if ($this->get_default_theme() === $theme) {
                $suitename = 'default';
            } else {
                $suitename = $theme;
            }

            // Add suite no matter what. If there is no feature in suite then it will just exist successfully with no scenarios.
            // But if we don't set this then the user has to know which run doesn't have suite and which run do.
            $suites = array_merge($suites, array(
                $suitename => array(
                    'paths'    => array_values($themesuitefeatures),
                    'contexts' => $themecontexts[$theme],
                )
            ));
        }

        return $suites;
    }

    /**
     * Return name of default theme.
     *
     * @return string
     */
    protected function get_default_theme() {
        return theme_config::DEFAULT_THEME;
    }

    /**
     * Return list of themes which can be set in moodle.
     *
     * @return array list of themes with tests.
     */
    protected function get_list_of_themes() {
        $selectablethemes = array();

        // Get all themes installed on site.
        $themes = core_component::get_plugin_list('theme');
        ksort($themes);

        foreach ($themes as $themename => $themedir) {
            // Load the theme config.
            try {
                $theme = $this->get_theme_config($themename);
            } catch (Exception $e) {
                // Bad theme, just skip it for now.
                continue;
            }
            if ($themename !== $theme->name) {
                // Obsoleted or broken theme, just skip for now.
                continue;
            }
            if ($theme->hidefromselector) {
                // The theme doesn't want to be shown in the theme selector and as theme
                // designer mode is switched off we will respect that decision.
                continue;
            }
            $selectablethemes[] = $themename;
        }

        return $selectablethemes;
    }

    /**
     * Return the theme config for a given theme name.
     * This is done so we can mock it in PHPUnit.
     *
     * @param string $themename name of theme
     * @return theme_config
     */
    public function get_theme_config($themename) {
        return theme_config::load($themename);
    }

    /**
     * Return theme directory.
     *
     * @param string $themename name of theme
     * @return string theme directory
     */
    protected function get_theme_test_directory($themename) {
        global $CFG;

        $themetestdir = "/theme/" . $themename;

        return $CFG->dirroot . $themetestdir  . self::get_behat_tests_path();
    }

    /**
     * Returns all the directories having overridden tests.
     *
     * @param string $theme name of theme
     * @param string $testtype The kind of test we are looking for
     * @return array all directories having tests
     */
    protected function get_test_directories_overridden_for_theme($theme, $testtype) {
        global $CFG;

        $testtypes = array(
            'contexts' => '|behat_.*\.php$|',
            'features' => '|.*\.feature$|',
        );
        $themetestdirfullpath = $this->get_theme_test_directory($theme);

        // If test directory doesn't exist then return.
        if (!is_dir($themetestdirfullpath)) {
            return array();
        }

        $directoriestosearch = glob($themetestdirfullpath . DIRECTORY_SEPARATOR . '*' , GLOB_ONLYDIR);

        // Include theme directory to find tests.
        $dirs[realpath($themetestdirfullpath)] = trim(str_replace('/', '_', $themetestdirfullpath), '_');

        // Search for tests in valid directories.
        foreach ($directoriestosearch as $dir) {
            $dirite = new RecursiveDirectoryIterator($dir);
            $iteite = new RecursiveIteratorIterator($dirite);
            $regexp = $testtypes[$testtype];
            $regite = new RegexIterator($iteite, $regexp);
            foreach ($regite as $path => $element) {
                $key = dirname($path);
                $value = trim(str_replace(DIRECTORY_SEPARATOR, '_', str_replace($CFG->dirroot, '', $key)), '_');
                $dirs[$key] = $value;
            }
        }
        ksort($dirs);

        return array_flip($dirs);
    }

    /**
     * Return blacklisted contexts or features for a theme, as defined in blacklist.json.
     *
     * @param string $theme themename
     * @param string $testtype test type (contexts|features)
     * @return array list of blacklisted contexts or features
     */
    protected function get_blacklisted_tests_for_theme($theme, $testtype) {

        $themetestpath = $this->get_theme_test_directory($theme);

        if (file_exists($themetestpath . DIRECTORY_SEPARATOR . 'blacklist.json')) {
            // Blacklist file exist. Leave it for last to clear the feature and contexts.
            $blacklisttests = @json_decode(file_get_contents($themetestpath . DIRECTORY_SEPARATOR . 'blacklist.json'), true);
            if (empty($blacklisttests)) {
                behat_error(BEHAT_EXITCODE_REQUIREMENT, $themetestpath . DIRECTORY_SEPARATOR . 'blacklist.json is empty');
            }

            // If features or contexts not defined then no problem.
            if (!isset($blacklisttests[$testtype])) {
                $blacklisttests[$testtype] = array();
            }
            return $blacklisttests[$testtype];
        }

        return array();
    }

    /**
     * Return list of features and step definitions in theme.
     *
     * @param string $theme theme name
     * @param string $testtype test type, either features or contexts
     * @return array list of contexts $contexts or $features
     */
    protected function get_tests_for_theme($theme, $testtype) {

        $tests = array();
        $testtypes = array(
            'contexts' => '|^behat_.*\.php$|',
            'features' => '|.*\.feature$|',
        );

        // Get all the directories having overridden tests.
        $directories = $this->get_test_directories_overridden_for_theme($theme, $testtype);

        // Get overridden test contexts.
        foreach ($directories as $dirpath) {
            // All behat_*.php inside overridden directory.
            $diriterator = new DirectoryIterator($dirpath);
            $regite = new RegexIterator($diriterator, $testtypes[$testtype]);

            // All behat_*.php inside behat_config_manager::get_behat_tests_path() are added as steps definitions files.
            foreach ($regite as $file) {
                $key = $file->getBasename('.php');
                $tests[$key] = $file->getPathname();
            }
        }

        return $tests;
    }

    /**
     * Return list of blacklisted behat features for theme and features defined by theme only.
     *
     * @param string $theme theme name.
     * @return array ($blacklistfeatures, $blacklisttags, $features)
     */
    protected function get_behat_features_for_theme($theme) {
        global $CFG;

        // Get list of features defined by theme.
        $themefeatures = $this->get_tests_for_theme($theme, 'features');
        $themeblacklistfeatures = $this->get_blacklisted_tests_for_theme($theme, 'features');
        $themeblacklisttags = $this->get_blacklisted_tests_for_theme($theme, 'tags');

        // Mobile app tests are not theme-specific, so run only for the default theme (and if
        // configured).
        if ((empty($CFG->behat_ionic_dirroot) && empty($CFG->behat_ionic_wwwroot)) ||
                $theme !== $this->get_default_theme()) {
            $themeblacklisttags[] = '@app';
        }

        // Clean feature key and path.
        $features = array();
        $blacklistfeatures = array();

        foreach ($themefeatures as $themefeature) {
            list($featurekey, $featurepath) = $this->get_clean_feature_key_and_path($themefeature);
            $features[$featurekey] = $featurepath;
        }

        foreach ($themeblacklistfeatures as $themeblacklistfeature) {
            list($blacklistfeaturekey, $blacklistfeaturepath) = $this->get_clean_feature_key_and_path($themeblacklistfeature);
            $blacklistfeatures[$blacklistfeaturekey] = $blacklistfeaturepath;
        }

        // If blacklist tags then add those features to list.
        if (!empty($themeblacklisttags)) {
            // Remove @ if given, so we are sure we have only tag names.
            $themeblacklisttags = array_map(function($v) {
                return ltrim($v, '@');
            }, $themeblacklisttags);

            $themeblacklisttags = '@' . implode(',@', $themeblacklisttags);
            $blacklistedfeatureswithtag = $this->filtered_features_with_tags($this->get_components_features(),
                $themeblacklisttags);

            // Add features with blacklisted tags.
            if (!empty($blacklistedfeatureswithtag)) {
                foreach ($blacklistedfeatureswithtag as $themeblacklistfeature) {
                    list($key, $path) = $this->get_clean_feature_key_and_path($themeblacklistfeature);
                    $blacklistfeatures[$key] = $path;
                }
            }
        }

        ksort($features);

        $retval = array(
            'blacklistfeatures' => $blacklistfeatures,
            'features' => $features
        );

        return $retval;
    }

    /**
     * Return list of behat contexts for theme and update $this->stepdefinitions list.
     *
     * @param string $theme theme name.
     * @return  List of contexts
     */
    protected function get_behat_contexts_for_theme($theme) : array {
        // If we already have this list then just return. This will not change by run.
        if (!empty($this->themecontexts[$theme])) {
            return $this->themecontexts[$theme];
        }

        try {
            $themeconfig = $this->get_theme_config($theme);
        } catch (Exception $e) {
            // This theme has no theme config.
            return [];
        }

        // The theme will use all core contexts, except the one overridden by theme or its parent.
        $parentcontexts = [];
        if (isset($themeconfig->parents)) {
            foreach ($themeconfig->parents as $parent) {
                if ($parentcontexts = $this->get_behat_contexts_for_theme($parent)) {
                    break;
                }
            }
        }

        if (empty($parentcontexts)) {
            $parentcontexts = $this->get_components_contexts();
        }

        // Remove contexts which have been actively blacklisted.
        $blacklistedcontexts = $this->get_blacklisted_tests_for_theme($theme, 'contexts');
        foreach ($blacklistedcontexts as $blacklistpath) {
            $blacklistcontext = basename($blacklistpath, '.php');

            unset($parentcontexts[$blacklistcontext]);
        }

        // Apply overrides.
        $contexts = array_merge($parentcontexts, $this->get_tests_for_theme($theme, 'contexts'));

        // Remove classes which are overridden.
        foreach ($contexts as $contextclass => $path) {
            require_once($path);
            if (!class_exists($contextclass)) {
                // This may be a Poorly named class.
                continue;
            }

            $rc = new \ReflectionClass($contextclass);
            while ($rc = $rc->getParentClass()) {
                if (isset($contexts[$rc->name])) {
                    unset($contexts[$rc->name]);
                }
            }
        }

        // Sort the list of contexts.
        ksort($contexts);

        $this->themecontexts[$theme] = $contexts;

        return $contexts;
    }
}
