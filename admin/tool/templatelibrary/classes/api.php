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

use stdClass;
use core_component;
use coding_exception;
use required_capability_exception;

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
     * @return array[string] Where each template is in the form "component/templatename".
     */
    public static function list_templates($component = '', $search = '') {
        global $CFG;

        $templatedirs = array();
        $results = array();

        if ($component != '') {
            // Just look at one component for templates.
            $dir = core_component::get_component_directory($component);
            if (!$dir) {
                return $templatedirs;
            }

            $templatedirs[$component] = $dir . '/templates';
        } else {

            // Look at all the templates dirs for all installed plugins.
            $dir = $CFG->libdir . '/templates';
            if (!empty($dir) && is_dir($dir)) {
                $templatedirs['core'] = $dir;
            }
            $plugintypes = core_component::get_plugin_types();
            foreach ($plugintypes as $type => $dir) {
                $plugins = core_component::get_plugin_list_with_file($type, 'templates', false);
                foreach ($plugins as $plugin => $dir) {
                    if (!empty($dir) && is_dir($dir)) {
                        $templatedirs[$type . '_' . $plugin] = $dir;
                    }
                }
            }
        }

        foreach ($templatedirs as $templatecomponent => $dir) {
            // List it.
            $files = glob($dir . '/*.mustache');

            foreach ($files as $file) {
                $templatename = basename($file, '.mustache');
                if ($search == '' || strpos($templatename, $search) !== false) {
                    $results[] = $templatecomponent . '/' . $templatename;
                }
            }
        }
        return $results;
    }

}
