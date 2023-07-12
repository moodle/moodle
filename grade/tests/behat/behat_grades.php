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

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../lib/behat/behat_base.php');

/**
 * Behat grade related steps definitions.
 *
 * @package    core_grades
 * @copyright  2022 Mathew May <mathew.solutions>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_grades extends behat_base {

    /**
     * Convert page names to URLs for steps like 'When I am on the "[identifier]" "[page type]" page'.
     *
     * Recognised page names are:
     * | pagetype              | name meaning | description                                       |
     * | [report] view         | Course name  | The view page for the specified course and report |
     * | gradebook setup       | Course name  | The gradebook setup page for the specified course |
     * | course grade settings | Course name  | The grade settings page                           |
     * | outcomes              | Course name  | The grade outcomes page                           |
     * | scales                | Course name  | The grade scales page                             |
     *
     * @param string $type identifies which type of page this is - for example "Grader > View"
     * @param string $identifier identifies the particular page - for example "Course name"
     * @return moodle_url the corresponding URL.
     */
    protected function resolve_page_instance_url(string $type, string $identifier): moodle_url {
        $type = strtolower($type);
        if (strpos($type, '>') !== false) {
            [$pluginname, $type] = explode('>', $type);
            $pluginname = strtolower(trim($pluginname));

            // Fetch the list of plugins.
            $plugins = \core_component::get_plugin_list('gradereport');

            if (array_key_exists($pluginname, $plugins)) {
                $plugin = $pluginname;
            } else {
                $plugins = array_combine(
                    array_keys($plugins),
                    array_keys($plugins),
                );

                // This plugin is not in the list of plugins. Check the pluginname string.
                $names = array_map(function($name) {
                    return strtolower(get_string('pluginname', "gradereport_{$name}"));
                }, $plugins);
                $result = array_search($pluginname, $names);
                if ($result === false) {
                    throw new \coding_exception("Unknown plugin '{$pluginname}'");
                }
                $plugin = $result;
            }
        }
        $type = trim($type);

        switch ($type) {
            case 'view':
                return new moodle_url(
                    "/grade/report/{$plugin}/index.php",
                    ['id' => $this->get_course_id($identifier)]
                );
            case 'gradebook setup':
                return new moodle_url(
                    "/grade/edit/tree/index.php",
                    ['id' => $this->get_course_id($identifier)]
                );
            case 'course grade settings':
                return new moodle_url(
                    "/grade/edit/settings/index.php",
                    ['id' => $this->get_course_id($identifier)]
                );
            case 'outcomes':
                return new moodle_url(
                    "/grade/edit/outcome/course.php",
                    ['id' => $this->get_course_id($identifier)]
                );
            case 'scales':
                return new moodle_url(
                    "/grade/edit/scale/index.php",
                    ['id' => $this->get_course_id($identifier)]
                );
            default:
                throw new \coding_exception(
                    "Unknown page type '$type' for page identifier '$identifier'"
                );
        }
    }
}
