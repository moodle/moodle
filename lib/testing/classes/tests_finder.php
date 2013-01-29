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
 * Tests finder
 *
 * @package    core
 * @category   test
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Finds components and plugins with tests
 *
 * @package    core
 * @category   test
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tests_finder {

    /**
     * Returns all the components with tests of the specified type
     * @param string $testtype The kind of test we are looking for
     * @return array
     */
    public static function get_components_with_tests($testtype) {

        // Get all the components
        $components = self::get_all_plugins_with_tests($testtype) + self::get_all_subsystems_with_tests($testtype);

        // Get all the directories having tests
        $directories = self::get_all_directories_with_tests($testtype);

        // Find any directory not covered by proper components
        $remaining = array_diff($directories, $components);

        // Add them to the list of components
        $components += $remaining;

        return $components;
    }

    /**
     * Returns all the plugins having tests
     * @param string $testtype The kind of test we are looking for
     * @return array  all the plugins having tests
     */
    private static function get_all_plugins_with_tests($testtype) {
        $pluginswithtests = array();

        $plugintypes = get_plugin_types();
        ksort($plugintypes);
        foreach ($plugintypes as $type => $unused) {
            $plugs = get_plugin_list($type);
            ksort($plugs);
            foreach ($plugs as $plug => $fullplug) {
                // Look for tests recursively
                if (self::directory_has_tests($fullplug, $testtype)) {
                    $pluginswithtests[$type . '_' . $plug] = $fullplug;
                }
            }
        }
        return $pluginswithtests;
    }

    /**
     * Returns all the subsystems having tests
     *
     * Note we are hacking here the list of subsystems
     * to cover some well-known subsystems that are not properly
     * returned by the {@link get_core_subsystems()} function.
     *
     * @param string $testtype The kind of test we are looking for
     * @return array all the subsystems having tests
     */
    private static function get_all_subsystems_with_tests($testtype) {
        global $CFG;

        $subsystemswithtests = array();

        $subsystems = get_core_subsystems();

        // Hack the list a bit to cover some well-known ones
        $subsystems['backup'] = 'backup';
        $subsystems['db-dml'] = 'lib/dml';
        $subsystems['db-ddl'] = 'lib/ddl';

        ksort($subsystems);
        foreach ($subsystems as $subsys => $relsubsys) {
            if ($relsubsys === null) {
                continue;
            }
            $fullsubsys = $CFG->dirroot . '/' . $relsubsys;
            if (!is_dir($fullsubsys)) {
                continue;
            }
            // Look for tests recursively
            if (self::directory_has_tests($fullsubsys, $testtype)) {
                $subsystemswithtests['core_' . $subsys] = $fullsubsys;
            }
        }
        return $subsystemswithtests;
    }

    /**
     * Returns all the directories having tests
     *
     * @param string $testtype The kind of test we are looking for
     * @return array all directories having tests
     */
    private static function get_all_directories_with_tests($testtype) {
        global $CFG;

        $dirs = array();
        $dirite = new RecursiveDirectoryIterator($CFG->dirroot);
        $iteite = new RecursiveIteratorIterator($dirite);
        $regexp = self::get_regexp($testtype);
        $regite = new RegexIterator($iteite, $regexp);
        foreach ($regite as $path => $element) {
            $key = dirname(dirname($path));
            $value = trim(str_replace('/', '_', str_replace($CFG->dirroot, '', $key)), '_');
            $dirs[$key] = $value;
        }
        ksort($dirs);
        return array_flip($dirs);
    }

    /**
     * Returns if a given directory has tests (recursively)
     *
     * @param string $dir full path to the directory to look for phpunit tests
     * @param string $testtype phpunit|behat
     * @return bool if a given directory has tests (true) or no (false)
     */
    private static function directory_has_tests($dir, $testtype) {
        if (!is_dir($dir)) {
            return false;
        }

        $dirite = new RecursiveDirectoryIterator($dir);
        $iteite = new RecursiveIteratorIterator($dirite);
        $regexp = self::get_regexp($testtype);
        $regite = new RegexIterator($iteite, $regexp);
        $regite->rewind();
        if ($regite->valid()) {
            return true;
        }
        return false;
    }


    /**
     * Returns the regular expression to match by the test files
     * @param string $testtype
     * @return string
     */
    private static function get_regexp($testtype) {

        $sep = preg_quote(DIRECTORY_SEPARATOR, '|');

        switch ($testtype) {
            case 'phpunit':
                $regexp = '|'.$sep.'tests'.$sep.'.*_test\.php$|';
                break;
            case 'features':
                $regexp = '|'.$sep.'tests'.$sep.'behat'.$sep.'.*\.feature$|';
                break;
            case 'stepsdefinitions':
                $regexp = '|'.$sep.'tests'.$sep.'behat'.$sep.'behat_.*\.php$|';
                break;
        }

        return $regexp;
    }
}
