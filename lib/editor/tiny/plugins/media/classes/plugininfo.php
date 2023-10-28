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

namespace tiny_media;

use context;
use editor_tiny\editor;
use editor_tiny\plugin;
use editor_tiny\plugin_with_buttons;
use editor_tiny\plugin_with_configuration;
use editor_tiny\plugin_with_menuitems;

/**
 * Tiny media plugin.
 *
 * @package    tiny_media
 * @copyright  2022 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plugininfo extends plugin implements plugin_with_buttons, plugin_with_menuitems, plugin_with_configuration {
    /**
     * Whether the plugin is enabled
     *
     * @param context $context The context that the editor is used within
     * @param array $options The options passed in when requesting the editor
     * @param array $fpoptions The filepicker options passed in when requesting the editor
     * @param editor $editor The editor instance in which the plugin is initialised
     * @return boolean
     */
    public static function is_enabled(
        context $context,
        array $options,
        array $fpoptions,
        ?editor $editor = null
    ): bool {
        // Disabled if:
        // - Not logged in or guest.
        // - Files are not allowed.
        // - Only URL are supported.
        $canhavefiles = !empty($options['maxfiles']);
        $canhaveexternalfiles = !empty($options['return_types']) && ($options['return_types'] & FILE_EXTERNAL);

        return isloggedin() && !isguestuser() && ($canhavefiles || $canhaveexternalfiles);
    }

    public static function get_available_buttons(): array {
        return [
            'tiny_media/tiny_media_image',
        ];
    }

    public static function get_available_menuitems(): array {
        return [
            'tiny_media/tiny_media_image',
        ];
    }

    public static function get_plugin_configuration_for_context(
        context $context,
        array $options,
        array $fpoptions,
        ?editor $editor = null
    ): array {

        // TODO Fetch the actual permissions.
        $permissions = [
            'image' => [
                'filepicker' => true,
            ],
            'embed' => [
                'filepicker' => true,
            ]
        ];

        return array_merge([
            'permissions' => $permissions,
        ], self::get_file_manager_configuration($context, $options, $fpoptions));
    }

    protected static function get_file_manager_configuration(
        context $context,
        array $options,
        array $fpoptions
    ): array {
        global $USER;

        $params = [
            'area' => [],
            'usercontext' => \context_user::instance($USER->id)->id,
        ];

        $keys = [
            'itemid',
            'areamaxbytes',
            'maxbytes',
            'subdirs',
            'return_types',
            'removeorphaneddrafts',
        ];
        if (isset($options['context'])) {
            if (is_object($options['context'])) {
                $params['area']['context'] = $options['context']->id;
            } else {
                $params['area']['context'] = $options['context'];
            }
        }
        foreach ($keys as $key) {
            if (isset($options[$key])) {
                $params['area'][$key] = $options[$key];
            }
        }

        return [
            'storeinrepo' => true,
            'data' => [
                'params' => $params,
                'fpoptions' => $fpoptions,
            ],
        ];
    }
}
