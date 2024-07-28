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

use context;
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
     * Whether the plugin is enabled
     *
     * @param context $context The context that the editor is used within
     * @param array $options The options passed in when requesting the editor
     * @param array $fpoptions The filepicker options passed in when requesting the editor
     * @param editor|null $editor $editor The editor instance in which the plugin is initialised
     * @return boolean
     */
    public static function is_enabled(
        context $context,
        array $options,
        array $fpoptions,
        ?editor $editor = null
    ): bool {
        return in_array(true, self::get_allowed_actions($context));
    }

    /**
     * Get the list of buttons that this plugin provides.
     *
     * @return array
     */
    public static function get_available_buttons(): array {
        return [
            'tiny_aiplacement/generate_text',
            'tiny_aiplacement/generate_image',
        ];
    }

    /**
     * Get the list of menu items that this plugin provides.
     *
     * @return array
     */
    public static function get_available_menuitems(): array {
        return [
                'tiny_aiplacement/generate_text',
                'tiny_aiplacement/generate_image',
        ];
    }

    /**
     * Get extra configuration items to be passed to this plugin.
     *
     * @param context $context The context that the editor is used within
     * @param array $options The options passed in when requesting the editor
     * @param array $fpoptions The filepicker options passed in when requesting the editor
     * @param editor|null $editor $editor The editor instance in which the plugin is initialised
     * @return array
     */
    public static function get_plugin_configuration_for_context(
        context $context,
        array $options,
        array $fpoptions,
        ?editor $editor = null
    ): array {
        global $USER;
        $allowedactions = self::get_allowed_actions($context);
        return [
            'contextid' => $context->id,
            'userid' => (int) $USER->id,
            'textallowed' => $allowedactions['textallowed'],
            'imageallowed' => $allowedactions['imageallowed'],
        ];
    }

    /**
     * Get the allowed actions for the plugin.
     *
     * @param context $context The context that the editor is used within
     * @return array The allowed actions.
     */
    private static function get_allowed_actions(context $context): array {
        $allowedactions = [
            'textallowed' => false,
            'imageallowed' => false,
        ];
        [$plugintype, $pluginname] = explode('_', \core_component::normalize_componentname('aiplacement_tinymce'), 2);
        $manager = \core_plugin_manager::resolve_plugininfo_class($plugintype);
        if ($manager::is_plugin_enabled($pluginname)) {
            $providers = manager::get_providers_for_actions([
                generate_text::class,
                generate_image::class,
            ], true);
            if (has_capability('aiplacement/tinymce:generate_text', $context)
                && manager::is_action_enabled('aiplacement_tinymce', 'generate_text')
                && !empty($providers[generate_text::class])) {
                $allowedactions['textallowed'] = true;
            }
            if (has_capability('aiplacement/tinymce:generate_image', $context)
                && manager::is_action_enabled('aiplacement_tinymce', 'generate_image')
                && !empty($providers[generate_image::class])) {
                $allowedactions['imageallowed'] = true;
            }
        }
        return $allowedactions;
    }
}
