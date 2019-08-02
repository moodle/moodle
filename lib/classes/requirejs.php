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
 * RequireJS helper functions.
 *
 * @package    core
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Collection of requirejs related methods.
 *
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_requirejs {

    /**
     * Check a single module exists and return the full path to it.
     *
     * The expected location for amd modules is:
     *  <componentdir>/amd/src/modulename.js
     *
     * @param string $component The component determines the folder the js file should be in.
     * @param string $jsfilename The filename for the module (with the js extension).
     * @param boolean $debug If true, returns the paths to the original (unminified) source files.
     * @return array $files An array of mappings from module names to file paths.
     *                      Empty array if the file does not exist.
     */
    public static function find_one_amd_module($component, $jsfilename, $debug = false) {
        $jsfileroot = core_component::get_component_directory($component);
        if (!$jsfileroot) {
            return array();
        }

        $module = str_replace('.js', '', $jsfilename);

        $srcdir = $jsfileroot . '/amd/build';
        $minpart = '.min';
        if ($debug) {
            $srcdir = $jsfileroot . '/amd/src';
            $minpart = '';
        }

        $filename = $srcdir . '/' . $module . $minpart . '.js';
        if (!file_exists($filename)) {
            return array();
        }

        $fullmodulename = $component . '/' . $module;
        return array($fullmodulename => $filename);
    }

    /**
     * Scan the source for AMD modules and return them all.
     *
     * The expected location for amd modules is:
     *  <componentdir>/amd/src/modulename.js
     *
     * @param boolean $debug If true, returns the paths to the original (unminified) source files.
     * @param boolean $includelazy If true, includes modules with the -lazy suffix.
     * @return array $files An array of mappings from module names to file paths.
     */
    public static function find_all_amd_modules($debug = false, $includelazy = false) {
        global $CFG;

        $jsdirs = array();
        $jsfiles = array();

        $dir = $CFG->libdir . '/amd';
        if (!empty($dir) && is_dir($dir)) {
            $jsdirs['core'] = $dir;
        }
        $subsystems = core_component::get_core_subsystems();
        foreach ($subsystems as $subsystem => $dir) {
            if (!empty($dir) && is_dir($dir . '/amd')) {
                $jsdirs['core_' . $subsystem] = $dir . '/amd';
            }
        }
        $plugintypes = core_component::get_plugin_types();
        foreach ($plugintypes as $type => $dir) {
            $plugins = core_component::get_plugin_list_with_file($type, 'amd', false);
            foreach ($plugins as $plugin => $dir) {
                if (!empty($dir) && is_dir($dir)) {
                    $jsdirs[$type . '_' . $plugin] = $dir;
                }
            }
        }

        foreach ($jsdirs as $component => $dir) {
            $srcdir = $dir . '/build';
            if ($debug) {
                $srcdir = $dir . '/src';
            }
            if (!is_dir($srcdir) || !is_readable($srcdir)) {
                // This is probably an empty amd directory without src or build.
                // Skip it - RecursiveDirectoryIterator fatals if the directory is not readable as an iterator.
                continue;
            }
            $srcdir = realpath($srcdir);
            $directory = new RecursiveDirectoryIterator($srcdir);
            $items = new RecursiveIteratorIterator($directory);
            foreach ($items as $item) {
                $extension = $item->getExtension();
                if ($extension === 'js') {
                    $filename = substr($item->getRealpath(), strlen($srcdir) + 1);
                    $filename = str_replace('.min', '', $filename);
                    // We skip lazy loaded modules unless specifically requested.
                    if ($includelazy || strpos($filename, '-lazy') === false) {
                        $modulename = $component . '/' . $filename;
                        $jsfiles[$modulename] = $item->getRealPath();
                    }
                }
                unset($item);
            }
            unset($items);
        }

        return $jsfiles;
    }

}
