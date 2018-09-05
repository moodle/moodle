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
        $data = new stdClass();
        $data->allcomponents = array();
        $fulltemplatenames = api::list_templates();
        $pluginmanager = core_plugin_manager::instance();
        $components = array();

        foreach ($fulltemplatenames as $templatename) {
            list($component, $templatename) = explode('/', $templatename, 2);
            $components[$component] = 1;
        }

        $components = array_keys($components);
        foreach ($components as $component) {
            $info = new stdClass();
            $info->component = $component;
            if (strpos($component, 'core') === 0) {
                $info->name = get_string('coresubsystem', 'tool_templatelibrary', $component);
            } else {
                $info->name = $pluginmanager->plugin_name($component);
            }
            $data->allcomponents[] = $info;
        }

        return $data;
    }
}
