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

namespace MoodleHQ\MoodleCS\moodle\Util;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Exceptions\DeepExitException;
use PHP_CodeSniffer\Files\DummyFile;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Tokenizers\PHP;

// phpcs:disable moodle.NamingConventions

/**
 * Various utility methods specific to Moodle stuff.
 *
 * @package    local_codechecker
 * @copyright  2021 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class MoodleUtil {

    /**
     * @var string Absolute path, cached, containing moodle root detected directory.
     */
    protected static $moodleRoot = false;

    /**
     * @var int Branch, cached, containing moodle detected numeric branch.
     */
    protected static $moodleBranch = false;

    /**
     * @var array Associative array, cached, of components as keys and paths as values.
     */
    protected static $moodleComponents = [];

    /** @var array A list of mocked component mappings for use in unit tests */
    protected static $mockedComponentMappings = [];

    /**
     * Mock component mappings for unit tests.
     *
     * @param array $mappings List of file path => component mappings
     *
     * @throws Exception
     */
    public static function setMockedComponentMappings(array $mappings): void {
        if (!defined('PHPUNIT_TEST') || !PHPUNIT_TEST) {
            throw new \Exception('Not running in a unit test');
        }

        self::$mockedComponentMappings = $mappings;
    }

    /**
     * Load moodle core_component without needing an installed site.
     *
     * @param string $moodleRoot Full path to a valid moodle.root
     * @return bool True if the file has been loaded, false if not.
     */
    protected static function loadCoreComponent(string $moodleRoot): bool {
        global $CFG;

        // Safety check, in case core_component is missing.
        if (!file_exists($moodleRoot . '/lib/classes/component.php')) {
            return false;
        }

        // Some of these (rarely) may be not defined. Ensure they are.
        defined('IGNORE_COMPONENT_CACHE') ?: define('IGNORE_COMPONENT_CACHE', 1);
        defined('MOODLE_INTERNAL') ?: define('MOODLE_INTERNAL', 1);

        if (!isset($CFG->dirroot)) { // No defined, let's start from scratch.
            $CFG = (object) [
                'dirroot' => $moodleRoot,
                'libdir' => "${moodleRoot}/lib",
                'admin' => 'admin',
            ];
        }

        // Save current CFG values.
        $olddirroot = $CFG->dirroot ?? null;
        $oldlibdir = $CFG->libdir ?? null;
        $oldadmin = $CFG->admin ?? null;

        if ($CFG->dirroot !== $moodleRoot) { // Different, set the minimum required.
            $CFG->dirroot = $moodleRoot;
            $CFG->libdir = $CFG->dirroot . '/lib';
            $CFG->admin = 'admin';
        }

        require_once($CFG->dirroot . '/lib/classes/component.php'); // Load the class.

        // Restore original CFG values.
        $CFG->dirroot = $olddirroot ?? null;
        $CFG->libdir = $oldlibdir ?? null;
        $CFG->admin = $oldadmin ?? null;

        return true;
    }

    /**
     * Calculate all the components installed in a site.
     *
     * @param string $moodleRoot Full path to a valid moodle.root
     * @return array Associative array of components as keys and paths as values or null if not found.
     */
    protected static function calculateAllComponents(string $moodleRoot) {
        // If we have calculated the components already, straight return them.
        if (!empty(self::$moodleComponents)) {
            return self::$moodleComponents;
        }

        // We haven't the components yet, let's calculate all them.

        // First, try to get it from configuration/runtime option.
        // This accepts the full path to a file like the one generated
        // by moodle-local_ci/list_valid_components, which format is:
        // [plugin|subsystem],component_name,component_full_path.
        // Useful to load them when not all the code base is available
        // like it happens with CiBoT runs, for example.
        if ($componentsFile = Config::getConfigData('moodleComponentsListPath')) {
            if (!is_readable($componentsFile)) {
                throw new DeepExitException(
                    "ERROR: Incorrect 'moodleComponentsListPath' config/runtime option. File not found: '$componentsFile'", 3);
            }
            // Go processing the file.
            $handle = fopen($componentsFile, "r");
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    $aline = explode(',', trim($line));
                    // Exclude any line not starting by plugin|sybsystem.
                    if ($aline[0] !== 'plugin' && $aline[0] !== 'subsystem') {
                        continue;
                    }
                    // Exclude any component not being valid one.
                    if (!preg_match('/^[a-z][a-z0-9]*(_[a-z][a-z0-9_]*)?[a-z0-9]+$/', $aline[1])) {
                        continue;
                    }
                    // Exclude any path not being under Mooddle dirroot.
                    if (strpos($aline[2], $moodleRoot) !== 0) {
                        continue;
                    }
                    // Arrived here, it's a valid line, annotate the component.
                    self::$moodleComponents[$aline[1]] = $aline[2];
                }
                fclose($handle);
            }
            // Let's sort the array in ascending order, so more specific matches first.
            arsort(self::$moodleComponents);

            return self::$moodleComponents;
        }

        // Let's try to get the components from core.

        // Verify that core_component class is already available.
        // Make an exception for PHPUnit runs, to be able to test everything
        // because within tests it's always available and never invoked.
        if (!class_exists('\core_component') || (defined('PHPUNIT_TEST') && PHPUNIT_TEST)) {
            if (!self::loadCoreComponent($moodleRoot)) {
                return null;
            }
        }

        // Get all the plugins and subplugin types.
        $types = \core_component::get_plugin_types();
        // Sort types in reverse order, so we get subplugins earlier than plugins.
        $types = array_reverse($types);
        // For each type, get their available implementations.
        foreach ($types as $type => $fullpath) {
            $plugins = \core_component::get_plugin_list($type);
            // For each plugin, let's calculate the proper component name and output it.
            foreach ($plugins as $plugin => $pluginpath) {
                $component = $type . '_' . $plugin;
                self::$moodleComponents[$component] = $pluginpath;
            }
        }

        // Get all the subsystems.
        $subsystems = \core_component::get_core_subsystems();
        $subsystems['core'] = $moodleRoot . '/lib'; // To get core for everything under /lib.
        foreach ($subsystems as $subsystem => $subsystempath) {
            if ($subsystem == 'backup') { // Because I want, yes :-P.
                $subsystempath = $moodleRoot . '/backup';
            }
            // All subsystems are core_ prefixed.
            $component = 'core_' . $subsystem;
            if ($subsystem === 'core') { // But core.
                $component = 'core';
            }
            self::$moodleComponents[$component] = $subsystempath;
        }
        // Let's sort the array in ascending order, so more specific matches first.
        arsort(self::$moodleComponents);

        return self::$moodleComponents;
    }

    /**
     * Try to guess the moodle component for a file
     *
     * This method will return, using moodle core_component, the component
     * corresponding to a file, given the file is within a valid moodle tree.
     *
     * @param File $file File that is being checked.
     * @param bool $selfPath Enables the method to also consider self path to search for a valid moodle root.
     *
     * @return string|null a valid moodle component for the file or null if not found.
     */
    public static function getMoodleComponent(File $file, $selfPath = true) {
        if (defined('PHPUNIT_TEST') && PHPUNIT_TEST && !empty(self::$mockedComponentMappings)) {
            $components = self::$mockedComponentMappings;
        } else {
            // Verify that we are able to find a valid moodle root.
            if (!$moodleRoot = self::getMoodleRoot($file, $selfPath)) {
                return null;
            }

            // Load all components, associative array with keys as component and paths as values.
            $components = self::calculateAllComponents($moodleRoot);
            // Have been unable to load components, done.
            if (empty($components)) {
                return null;
            }
        }

        // Let's find the first component that matches the file path.
        foreach ($components as $component => $componentPath) {
            // Only components with path.
            if (empty($componentPath)) {
                continue;
            }
            // Look for component paths matching the file path.
            if (strpos($file->path, $componentPath . '/') === 0) {
                // First match found should be the better one always. We are done.
                return $component;
            }
        }

        // Not found.
        return null;
    }

    /**
     * Try to guess moodle branch (numeric)
     *
     * This method will parse the moodle root version.php file
     * returning the $branch information from it. It will try to:
     * - detect if the moodleBranch configuration/runtime option has been set.
     * - trying to detect moodle root and parsing the version.php file within it.
     *
     * @param File|null $file File that is being checked.
     * @param bool $selfPath Enables the method to also consider self path to search for a valid moodle root.
     *
     * @return int|null the numeric branch in moodle root version.php or null if not found
     */
    public static function getMoodleBranch(File $file = null, bool $selfPath = true) {

        // Return already calculated value if available.
        if (self::$moodleBranch !== false) {
            return self::$moodleBranch;
        }

        // First, try to get it from configuration/runtime option.
        if ($branch = Config::getConfigData('moodleBranch')) {
            // Verify it's integer value and <= 9999 (4 digits max).
            if (filter_var($branch, FILTER_VALIDATE_INT) === false) {
                throw new DeepExitException(
                    "ERROR: Incorrect 'moodleBranch' config/runtime option. Value in not an integer: '$branch'", 3);
            }
            if ($branch > 9999) {
                throw new DeepExitException(
                    "ERROR: Incorrect 'moodleBranch' config/runtime option. Value must be 4 digit max.: '$branch'", 3);
            }
            self::$moodleBranch = (int)$branch;
            return self::$moodleBranch;
        }

        // Now, let's try to find moodle root and then parse the version.php there.
        if ($moodleRoot = self::getMoodleRoot($file, $selfPath)) {
            // Let's use CodeSniffer own facilities to parse the version.php file.
            // Pass the parallel as CLI, disabled. Note
            // this is to avoid some nasty argv notices.
            $config = new Config(['--parallel=1']);
            $ruleset = new Ruleset($config);
            $versionFile = new DummyFile(file_get_contents($moodleRoot . '/version.php'), $ruleset, $config);
            $versionFile->parse();
            // Find the $branch variable declaration.
            if ($varToken = $versionFile->findNext(T_VARIABLE, 0, null, false, '$branch')) {
                // Find the $branch value.
                if ($valueToken = $versionFile->findNext(T_CONSTANT_ENCAPSED_STRING, $varToken)) {
                    $branch = trim($versionFile->getTokens()[$valueToken]['content'], "\"'");
                    self::$moodleBranch = (int)$branch;
                    return self::$moodleBranch;
                }
            }
        }

        // Still not found, bad luck, cannot calculate moodle branch.
        self::$moodleBranch = null;
        return self::$moodleBranch;
    }


    /**
     * Try to guess moodle root full path (needed for other utils).
     *
     * This method will try to guess the full path to moodle root by:
     * - detect if the moodleRoot configuration/runtime option has been set.
     * - looking recursively up from the file being checked.
     * - looking recursively up from this file.
     *
     * @param File|null $file File that is being checked.
     * @param bool $selfPath Enables the method to also consider self path to search for a valid moodle root.
     *
     * @return string|null the full path to moodle root or null if not found.
     */
    public static function getMoodleRoot(File $file = null, bool $selfPath = true) {
        // Return already calculated value if available.
        if (self::$moodleRoot !== false) {
            return self::$moodleRoot;
        }

        // First, try to get it from configuration/runtime option.
        if ($path = Config::getConfigData('moodleRoot')) {
            // Verify the path is exists and is readable.
            if (!is_dir($path) || !is_readable($path)) {
                throw new DeepExitException(
                    "ERROR: Incorrect 'moodleRoot' config/runtime option. Directory does not exist or is not readable: '$path'", 3);
            }
            // Verify the path has version.php and config-dist.php files. Very basic, but effective check.
            if (!is_readable($path . '/version.php') || !is_readable($path . '/config-dist.php')) {
                throw new DeepExitException(
                    "ERROR: Incorrect 'moodleRoot' config/runtime option. Directory is not a valid moodle root: '$path'", 3);
            }
            self::$moodleRoot = $path;
            return self::$moodleRoot;
        }

        // Still not found, let's look upwards for a main version file and config-dist.php file
        // starting from the file path being checked (given it has been passed).
        if ($file instanceof File) {
            $path = $lastPath = $file->path;
            while (($path = pathinfo($path, PATHINFO_DIRNAME)) !== $lastPath) {
                // If we find both a version.php and config-dist.php file then we have arrived to moodle root.
                if (is_readable($path . '/version.php') && is_readable($path . '/config-dist.php')) {
                    self::$moodleRoot = $path;
                    return self::$moodleRoot;
                }
                // Path processed.
                $lastPath = $path;
            }
        }

        // Still not found, let's look upwards for a main version file and config-dist.php file
        // starting from this file path. Only if explicitly allowed by $selfPath.
        if ($selfPath) {
            $path = $lastPath = __FILE__;
            while (($path = pathinfo($path, PATHINFO_DIRNAME)) !== $lastPath) {
                // If we find both a version.php and config-dist.php file then we have arrived to moodle root.
                if (is_readable($path . '/version.php') && is_readable($path . '/config-dist.php')) {
                    self::$moodleRoot = $path;
                    return self::$moodleRoot;
                }
                // Path processed.
                $lastPath = $path;
            }
        }

        // Still not found, bad luck, cannot calculate moodle root.
        self::$moodleRoot = null;
        return self::$moodleRoot;
    }
}
