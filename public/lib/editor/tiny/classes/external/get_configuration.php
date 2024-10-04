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

namespace editor_tiny\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;
use editor_tiny\manager;

/**
 * External function that returns the TinyMCE configuration for a context.
 *
 * @package     editor_tiny
 * @copyright   2025 Moodle Pty Ltd
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_configuration extends external_api {

    /**
     * Describes the parameters of the external function.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'contextlevel' => new external_value(PARAM_ALPHA, 'Context level: system, user, coursecat, course, module or block'),
            'instanceid' => new external_value(PARAM_INT, 'Instance ID of the context (e.g. course ID)'),
        ]);
    }

    /**
     * Returns the TinyMCE configuration for a context.
     *
     * This function serves a similar purpose as \editor_tiny\editor::use_editor but with the following differences:
     * - It does not receive editor or file picker options.
     * - It only returns information that depends on site settings or permission checks.
     *
     * @param string $contextlevel Context level: system, user, coursecat, course, module or block.
     * @param int    $instanceid   Instance ID of the context (e.g. course ID).
     * @return array
     */
    public static function execute(string $contextlevel, int $instanceid): array {
        global $PAGE;

        $params = self::validate_parameters(self::execute_parameters(), [
            'contextlevel' => $contextlevel,
            'instanceid' => $instanceid,
        ]);

        $context = self::get_context_from_params($params);
        self::validate_context($context);

        $siteconfig = get_config('editor_tiny');
        $branding = !empty($siteconfig->branding ?? true);
        $extendedvalidelements = $siteconfig->extended_valid_elements ?? 'script[*],p[*],i[*]';

        $installedlanguages = [];
        foreach (get_string_manager()->get_list_of_translations(true) as $lang => $name) {
            $installedlanguages[] = ['lang' => $lang, 'name' => $name];
        }

        $manager = new manager();
        $plugins = [];
        foreach ($manager->get_plugin_configuration_for_external($context) as $name => $settings) {
            $plugin = [
                'name' => $name,
                'settings' => [],
            ];
            foreach ($settings as $name => $value) {
                $plugin['settings'][] = [
                    'name' => $name,
                    'value' => $value,
                ];
            }
            $plugins[] = $plugin;
        }

        return [
            'contextid' => $context->id,
            'branding' => $branding,
            'extendedvalidelements' => $extendedvalidelements,
            'installedlanguages' => $installedlanguages,
            'plugins' => $plugins,
            'warnings' => [],
        ];
    }

    /**
     * Describes the return structure of the external function.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'contextid' => new external_value(PARAM_INT, 'Context id'),
            'branding' => new external_value(PARAM_BOOL, 'Display the TinyMCE logo'),
            'extendedvalidelements' => new external_value(PARAM_RAW, 'Extended valid elements'),
            'installedlanguages' => new external_multiple_structure(
                new external_single_structure([
                    'lang' => new external_value(PARAM_LANG, 'Language code'),
                    'name' => new external_value(PARAM_RAW, 'Language name'),
                ]),
                'List of installed languages',
            ),
            'plugins' => new external_multiple_structure(
                new external_single_structure([
                    'name' => new external_value(PARAM_PLUGIN, 'Name of the plugin'),
                    'settings' => new external_multiple_structure(
                        new external_single_structure([
                            'name' => new external_value(PARAM_RAW, 'Name of the setting'),
                            'value' => new external_value(PARAM_RAW, 'Value of the setting'),
                        ]),
                        'Settings of the plugin',
                    ),
                ]),
                'Configuration of enabled plugins for the context'),
            'warnings' => new external_warnings(),
        ]);
    }
}
