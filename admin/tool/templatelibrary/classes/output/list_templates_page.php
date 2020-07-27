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
 * Class containing data for list_templates page
 *
 * @package    tool_templatelibrary
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_templatelibrary\output;

use renderable;
use templatable;
use renderer_base;
use stdClass;
use core_collator;
use core_component;
use core_plugin_manager;
use tool_templatelibrary\api;

/**
 * Class containing data for list_templates page
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class list_templates_page implements renderable, templatable {

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $fulltemplatenames = api::list_templates();
        $pluginmanager = core_plugin_manager::instance();
        $components = [];

        foreach ($fulltemplatenames as $templatename) {
            [$component, ] = explode('/', $templatename, 2);
            [$type, ] = core_component::normalize_component($component);

            // Core sub-systems are grouped together and are denoted by a distinct lang string.
            $coresubsystem = (strpos($component, 'core') === 0);

            if (!array_key_exists($type, $components)) {
                $typename = $coresubsystem
                    ? get_string('core', 'tool_templatelibrary')
                    : $pluginmanager->plugintype_name_plural($type);

                $components[$type] = (object) [
                    'type' => $typename,
                    'plugins' => [],
                ];
            }

            $pluginname = $coresubsystem
                ? get_string('coresubsystem', 'tool_templatelibrary', $component)
                : $pluginmanager->plugin_name($component);

            $components[$type]->plugins[$component] = (object) [
                'name' => $pluginname,
                'component' => $component,
            ];
        }

        // Sort returned components according to their type, followed by name.
        core_collator::asort_objects_by_property($components, 'type');
        array_walk($components, function(stdClass $component) {
            core_collator::asort_objects_by_property($component->plugins, 'name');
            $component->plugins = array_values($component->plugins);
        });

        return (object) [
            'allcomponents' => array_values($components),
        ];
    }
}
