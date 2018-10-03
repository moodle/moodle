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
 * Summary page renderable.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Adrian Greeve
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_dataprivacy\output;
defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;


/**
 * Class containing the summary page renderable.
 *
 * @copyright  2018 Adrian Greeve
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class summary_page implements renderable, templatable {

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output) {
        $contextlevels = [
            'contextlevelname10' => CONTEXT_SYSTEM,
            'contextlevelname30' => CONTEXT_USER,
            'contextlevelname40' => CONTEXT_COURSECAT,
            'contextlevelname50' => CONTEXT_COURSE,
            'contextlevelname70' => CONTEXT_MODULE,
            'contextlevelname80' => CONTEXT_BLOCK
        ];

        $data = [];
        $context = \context_system::instance();

        foreach ($contextlevels as $levelname => $level) {
            $classname = \context_helper::get_class_for_level($level);
            list($purposevar, $categoryvar) = \tool_dataprivacy\data_registry::var_names_from_context($classname);
            $purposeid = get_config('tool_dataprivacy', $purposevar);
            $categoryid = get_config('tool_dataprivacy', $categoryvar);

            $section = [];
            $section['contextname'] = get_string($levelname, 'tool_dataprivacy');

            if (empty($purposeid)) {
                list($purposeid, $categoryid) =
                        \tool_dataprivacy\data_registry::get_effective_default_contextlevel_purpose_and_category($level);
            }
            if ($purposeid == -1) {
                $purposeid = 0;
            }
            $purpose = new \tool_dataprivacy\purpose($purposeid);
            $export = new \tool_dataprivacy\external\purpose_exporter($purpose, ['context' => $context]);
            $purposedata = $export->export($output);
            $section['purpose'] = $purposedata;

            if (empty($categoryid)) {
                list($purposeid, $categoryid) =
                        \tool_dataprivacy\data_registry::get_effective_default_contextlevel_purpose_and_category($level);
            }
            if ($categoryid == -1) {
                $categoryid = 0;
            }
            $category = new \tool_dataprivacy\category($categoryid);
            $export = new \tool_dataprivacy\external\category_exporter($category, ['context' => $context]);
            $categorydata = $export->export($output);
            $section['category'] = $categorydata;
            $data['contexts'][] = $section;
        }

        // Get activity module plugin info.
        $pluginmanager = \core_plugin_manager::instance();
        $modplugins = $pluginmanager->get_enabled_plugins('mod');

        foreach ($modplugins as $name) {
            $classname = \context_helper::get_class_for_level($contextlevels['contextlevelname70']);
            list($purposevar, $categoryvar) = \tool_dataprivacy\data_registry::var_names_from_context($classname, $name);
            $categoryid = get_config('tool_dataprivacy', $categoryvar);
            $purposeid = get_config('tool_dataprivacy', $purposevar);
            if ($categoryid === false && $purposeid === false) {
                // If no purpose and category has been set for this plugin, then there's no need to show this on the list.
                continue;
            }

            $section = [];
            $section['contextname'] = $pluginmanager->plugin_name('mod_' . $name);

            if ($purposeid == -1) {
                $purposeid = 0;
            }
            $purpose = new \tool_dataprivacy\purpose($purposeid);
            $export = new \tool_dataprivacy\external\purpose_exporter($purpose, ['context' => $context]);
            $purposedata = $export->export($output);
            $section['purpose'] = $purposedata;

            if ($categoryid == -1) {
                $categoryid = 0;
            }
            $category = new \tool_dataprivacy\category($categoryid);
            $export = new \tool_dataprivacy\external\category_exporter($category, ['context' => $context]);
            $categorydata = $export->export($output);
            $section['category'] = $categorydata;

            $data['contexts'][] = $section;
        }

        return $data;
    }
}
