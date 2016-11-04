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
 * Class for listing mustache templates.
 *
 * @package    tool_templatelibrary
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_templatelibrary;

use core_component;
use core\output\mustache_template_finder;
use coding_exception;
use moodle_exception;
use required_capability_exception;
use stdClass;

/**
 * API exposed by tool_templatelibrary
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api {

    /**
     * Return a list of details about installed templates.
     *
     * @param string $component Filter the list to a single component.
     * @param string $search Search string to optionally filter the list of templates.
     * @param string $themename The name of the current theme.
     * @return array[string] Where each template is in the form "component/templatename".
     */
    public static function list_templates($component = '', $search = '', $themename = '') {
        global $CFG, $PAGE;

        if (empty($themename)) {
            $themename = $PAGE->theme->name;
        }
        $themeconfig = \theme_config::load($themename);

        $templatedirs = array();
        $results = array();

        if ($component !== '') {
            // Just look at one component for templates.
            $dirs = mustache_template_finder::get_template_directories_for_component($component, $themename);

            $templatedirs[$component] = $dirs;
        } else {

            // Look at all the templates dirs for core.
            $templatedirs['core'] = mustache_template_finder::get_template_directories_for_component('core', $themename);

            // Look at all the templates dirs for subsystems.
            $subsystems = core_component::get_core_subsystems();
            foreach ($subsystems as $subsystem => $dir) {
                $dir .= '/templates';
                if (is_dir($dir)) {
                    $dirs = mustache_template_finder::get_template_directories_for_component('core_' . $subsystem, $themename);
                    $templatedirs['core_' . $subsystem] = $dirs;
                }
            }

            // Look at all the templates dirs for plugins.
            $plugintypes = core_component::get_plugin_types();
            foreach ($plugintypes as $type => $dir) {
                $plugins = core_component::get_plugin_list_with_file($type, 'templates', false);
                foreach ($plugins as $plugin => $dir) {
                    if ($type == 'theme' && $plugin != $themename && !in_array($plugin, $themeconfig->parents)) {
                        continue;
                    }
                    if (!empty($dir) && is_dir($dir)) {
                        $pluginname = $type . '_' . $plugin;
                        $dirs = mustache_template_finder::get_template_directories_for_component($pluginname, $themename);
                        $templatedirs[$pluginname] = $dirs;
                    }
                }
            }
        }

        foreach ($templatedirs as $templatecomponent => $dirs) {
            foreach ($dirs as $dir) {
                // List it.
                $files = glob($dir . '/*.mustache');

                foreach ($files as $file) {
                    $templatename = basename($file, '.mustache');
                    if ($search == '' || strpos($templatename, $search) !== false) {
                        $results[$templatecomponent . '/' . $templatename] = 1;
                    }
                }
            }
        }
        $results = array_keys($results);
        sort($results);
        return $results;
    }

    /**
     * Return a mustache template.
     * Note - this function differs from the function core_output_load_template
     * because it will never return a theme overridden version of a template.
     *
     * @param string $component The component that holds the template.
     * @param string $template The name of the template.
     * @return string the template or false if template doesn't exist.
     */
    public static function load_canonical_template($component, $template) {
        // Get the list of possible template directories.
        $dirs = mustache_template_finder::get_template_directories_for_component($component);
        $filename = false;
        $themedir = core_component::get_plugin_types()['theme'];

        foreach ($dirs as $dir) {
            // Skip theme dirs - we only want the original plugin/core template.
            if (strpos($dir, $themedir) === 0) {
                continue;
            }

            $candidate = $dir . $template . '.mustache';
            if (file_exists($candidate)) {
                $filename = $candidate;
                break;
            }
        }

        if ($filename === false) {
            // There are occasions where we don't have a core template.
            return false;
        }

        $templatestr = file_get_contents($filename);
        return $templatestr;
    }


}
