<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Settings for the Tiny Premium plugin.
 *
 * @package     tiny_premium
 * @category    admin
 * @copyright   2023 David Woloszyn <david.woloszyn@moodle.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tiny_premium\manager;

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedIf
    if ($ADMIN->fulltree) {
        $sourcepathurl = new moodle_url('/admin/settings.php', ['section' => 'editorsettingstiny']);
        $setting = new admin_setting_configselect(
            'tiny_premium/plugin_source',
            new lang_string('pluginsource', 'tiny_premium'),
            new lang_string('pluginsource_desc', 'tiny_premium', $sourcepathurl->out(false)),
            manager::PACKAGE_CLOUD,
            [
                manager::PACKAGE_CLOUD => new lang_string('pluginsource:cloud', 'tiny_premium'),
                manager::PACKAGE_SELF_HOSTED => new lang_string('pluginsource:selfhosted', 'tiny_premium'),
            ],
        );
        $setting->set_validate_function(function(int $value): string {
            if ($value == manager::PACKAGE_SELF_HOSTED) {
                // If the self-hosted package is selected, we need to check if the standalone path is set.
                // If not, we return a warning message.
                $packagesource = get_config('editor_tiny', 'package_source');
                $standalonepath = get_config('editor_tiny', 'package_source_standalone_path');
                if ($packagesource != manager::PACKAGE_SELF_HOSTED || empty($standalonepath)) {
                    return get_string('package_source_standalone_path_invalid', 'editor_tiny');
                }
            }
            return '';
        });
        $settings->add($setting);

        // Set API key.
        $setting = new admin_setting_configpasswordunmask(
            'tiny_premium/apikey',
            get_string('apikey', 'tiny_premium'),
            get_string('apikey_desc', 'tiny_premium'),
            '',
        );
        $settings->add($setting);
        $settings->hide_if(
            'tiny_premium/apikey',
            'tiny_premium/plugin_source',
            'eq',
            manager::PACKAGE_SELF_HOSTED,
        );

        // Set individual Tiny Premium plugins.
        $settings->add(new \tiny_premium\local\admin_setting_tiny_premium_plugins());
    }

    // Extra settings for on-premise plugins (hidden, accessed via direct link from the settings page).
    $page = new admin_externalpage(
        name: 'tiny_premium_plugin_settings',
        visiblename: get_string('premiumplugins_settings', 'tiny_premium'),
        url: new moodle_url('/lib/editor/tiny/plugins/premium/extrasettings.php'),
        req_capability: 'moodle/site:config',
        hidden: true,
    );
    $ADMIN->add('editortiny', $page);
}
