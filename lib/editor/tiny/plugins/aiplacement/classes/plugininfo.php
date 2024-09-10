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

namespace tiny_aiplacement;

use core\context;
use core_ai\aiactions\generate_image;
use core_ai\aiactions\generate_text;
use core_ai\manager;
use editor_tiny\editor;
use editor_tiny\plugin;
use editor_tiny\plugin_with_buttons;
use editor_tiny\plugin_with_configuration;
use editor_tiny\plugin_with_menuitems;

/**
 * Tiny AI placement plugin.
 *
 * @package    tiny_aiplacement
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plugininfo extends plugin implements plugin_with_buttons, plugin_with_menuitems, plugin_with_configuration {
    /**
     * @var array|string[] The possible actions for the plugin.
     */
    protected static array $possibleactions = [
        'generate_text' => generate_text::class,
        'generate_image' => generate_image::class,
    ];

    #[\Override]
    public static function is_enabled(
        context $context,
        array $options,
        array $fpoptions,
        ?editor $editor = null
    ): bool {
        return in_array(true, self::get_allowed_actions($context, $options));
    }

    #[\Override]
    public static function get_available_buttons(): array {
        return array_map(fn ($action) => "tiny_aiplacement/{$action}", self::$possibleactions);
    }

    #[\Override]
    public static function get_available_menuitems(): array {
        return array_map(fn ($action) => "tiny_aiplacement/{$action}", self::$possibleactions);
    }

    #[\Override]
    public static function get_plugin_configuration_for_context(
        context $context,
        array $options,
        array $fpoptions,
        ?editor $editor = null
    ): array {
        global $USER;

        $userid = (int) $USER->id;
        $allowedactions = self::get_allowed_actions($context, $options);

        return array_merge([
            'contextid' => $context->id,
            'userid' => $userid,
            'policyagreed' => manager::get_user_policy_status($userid),
        ], $allowedactions);
    }

    /**
     * Get the allowed actions for the plugin.
     *
     * @param context $context The context that the editor is used within
     * @param array $options The options passed in when requesting the editor
     * @return array The allowed actions.
     */
    private static function get_allowed_actions(context $context, array $options): array {
        [$plugintype, $pluginname] = explode('_', \core_component::normalize_componentname('aiplacement_editor'), 2);
        $manager = \core_plugin_manager::resolve_plugininfo_class($plugintype);
        $allowedactions = [];
        if ($manager::is_plugin_enabled($pluginname)) {
            $providers = manager::get_providers_for_actions(array_values(self::$possibleactions), true);
            foreach (self::$possibleactions as $action => $providerclass) {
                if (
                    has_capability("aiplacement/editor:{$action}", $context)
                    && manager::is_action_enabled('aiplacement_editor', $action)
                    && !empty($providers[$providerclass])
                ) {
                    if ($action == 'generate_image') {
                        // For generate image, we need to check if the user has the capability to upload files.
                        $canhavefiles = !empty($options['maxfiles']);
                        $canhaveexternalfiles = !empty($options['return_types']) && ($options['return_types'] & FILE_EXTERNAL);
                        $allowedactions[$action] = $canhavefiles || $canhaveexternalfiles;
                    } else {
                        $allowedactions[$action] = true;
                    }
                } else {
                    $allowedactions[$action] = false;
                }
            }
        }
        return $allowedactions;
    }
}
