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

namespace tiny_recordrtc;

use context;
use editor_tiny\editor;
use editor_tiny\plugin;
use editor_tiny\plugin_with_buttons;
use editor_tiny\plugin_with_configuration;
use editor_tiny\plugin_with_configuration_for_external;
use editor_tiny\plugin_with_menuitems;

/**
 * Tiny RecordRTC plugin.
 *
 * @package    tiny_recordrtc
 * @copyright  2022 Stevani Andolo <stevani@hotmail.com.au>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plugininfo extends plugin implements
    plugin_with_buttons,
    plugin_with_menuitems,
    plugin_with_configuration,
    plugin_with_configuration_for_external {

    #[\Override]
    public static function is_enabled(
        context $context,
        array $options,
        array $fpoptions,
        ?editor $editor = null
    ): bool {
        // Disabled if:
        // - Not logged in or guest.
        // - Files are not allowed.
        // - Doesn't have the correct capability.
        $canhavefiles = !empty($options['maxfiles']);
        return isloggedin() && !isguestuser() && $canhavefiles && has_capability('tiny/recordrtc:use', $context);
    }

    #[\Override]
    public static function is_enabled_for_external(context $context, array $options): bool {
        // Assume files are allowed.
        $options['maxfiles'] = 1;
        return self::is_enabled($context, $options, []);
    }

    public static function get_available_buttons(): array {
        return [
            'tiny_recordrtc/tiny_recordrtc_image',
        ];
    }

    public static function get_available_menuitems(): array {
        return [
            'tiny_recordrtc/tiny_recordrtc_image',
        ];
    }

    public static function get_plugin_configuration_for_context(
        context $context,
        array $options,
        array $fpoptions,
        ?editor $editor = null
    ): array {
        $sesskey = sesskey();
        $allowedtypes = explode(',', get_config('tiny_recordrtc', 'allowedtypes'));
        $audiobitrate = get_config('tiny_recordrtc', 'audiobitrate');
        $videobitrate = get_config('tiny_recordrtc', 'videobitrate');
        $screenbitrate = get_config('tiny_recordrtc', 'screenbitrate');
        $audiotimelimit = get_config('tiny_recordrtc', 'audiotimelimit');
        $videotimelimit = get_config('tiny_recordrtc', 'videotimelimit');
        $screentimelimit = get_config('tiny_recordrtc', 'screentimelimit');
        [$videoscreenwidth, $videoscreenheight] = explode(',', get_config('tiny_recordrtc', 'screensize'));
        $audiortcformat = (int) get_config('tiny_recordrtc', 'audiortcformat');

        // Update $allowedtypes to account for capabilities.
        $audioallowed = false;
        $videoallowed = false;
        $screenallowed = false;
        $allowedpausing = (bool) get_config('tiny_recordrtc', 'allowedpausing');
        foreach ($allowedtypes as $value) {
            switch ($value) {
                case constants::TINYRECORDRTC_AUDIO_TYPE:
                    if (has_capability('tiny/recordrtc:recordaudio', $context)) {
                        $audioallowed = true;
                    }
                    break;
                case constants::TINYRECORDRTC_VIDEO_TYPE:
                    if (has_capability('tiny/recordrtc:recordvideo', $context)) {
                        $videoallowed = true;
                    }
                    break;
                case constants::TINYRECORDRTC_SCREEN_TYPE:
                    if (has_capability('tiny/recordrtc:recordscreen', $context)) {
                        $screenallowed = true;
                    }
                    break;
                default:
                    break;
            }
        }

        $maxrecsize = get_max_upload_file_size();
        if (!empty($options['maxbytes'])) {
            $maxrecsize = min($maxrecsize, $options['maxbytes']);
        }
        $params = [
            'contextid' => $context->id,
            'sesskey' => $sesskey,
            'allowedtypes' => $allowedtypes,
            'audiobitrate' => $audiobitrate,
            'videobitrate' => $videobitrate,
            'screenbitrate' => $screenbitrate,
            'audiotimelimit' => $audiotimelimit,
            'videotimelimit' => $videotimelimit,
            'screentimelimit' => $screentimelimit,
            'maxrecsize' => $maxrecsize,
            'videoscreenwidth' => $videoscreenwidth,
            'videoscreenheight' => $videoscreenheight,
            'audiortcformat' => $audiortcformat,
        ];

        $data = [
            'params' => $params,
            'fpoptions' => $fpoptions
        ];

        return [
            'data' => $data,
            'videoAllowed' => $videoallowed,
            'audioAllowed' => $audioallowed,
            'screenAllowed' => $screenallowed,
            'pausingAllowed' => $allowedpausing,
        ];
    }

    #[\Override]
    public static function get_plugin_configuration_for_external(context $context): array {
        $settings = self::get_plugin_configuration_for_context($context, [], []);
        return [
            'videoallowed' => $settings['videoAllowed'] ? '1' : '0',
            'audioallowed' => $settings['audioAllowed'] ? '1' : '0',
            'screenallowed' => $settings['screenAllowed'] ? '1' : '0',
            'pausingallowed' => $settings['pausingAllowed'] ? '1' : '0',
            'allowedtypes' => implode(',', $settings['data']['params']['allowedtypes']),
            'audiobitrate' => $settings['data']['params']['audiobitrate'],
            'videobitrate' => $settings['data']['params']['videobitrate'],
            'screenbitrate' => $settings['data']['params']['screenbitrate'],
            'audiotimelimit' => $settings['data']['params']['audiotimelimit'],
            'videotimelimit' => $settings['data']['params']['videotimelimit'],
            'screentimelimit' => $settings['data']['params']['screentimelimit'],
            'maxrecsize' => (string) $settings['data']['params']['maxrecsize'],
            'videoscreenwidth' => $settings['data']['params']['videoscreenwidth'],
            'videoscreenheight' => $settings['data']['params']['videoscreenheight'],
            'audiortcformat' => (string) $settings['data']['params']['audiortcformat'],
        ];
    }
}
