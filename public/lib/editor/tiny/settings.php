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
 * Tiny admin settings.
 *
 * @package    editor_tiny
 * @copyright  2022 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use editor_tiny\editor_tiny_admin_setting_package_source;
use editor_tiny\manager;

defined('MOODLE_INTERNAL') || die;

$ADMIN->add('editorsettings', new admin_category('editortiny', $editor->displayname, $editor->is_enabled() === false));

$settings = new admin_settingpage('editorsettingstiny', new lang_string('settings', 'editor_tiny'));
$settings->add(new \core_admin\admin\admin_setting_plugin_manager(
    'tiny',
    \editor_tiny\table\plugin_management_table::class,
    'editor_tiny_settings',
    get_string('editorsettings', 'editor'),
));

if ($ADMIN->fulltree) {
    $setting = new admin_setting_configcheckbox(
        'editor_tiny/branding',
        new lang_string('branding', 'editor_tiny'),
        new lang_string('branding_desc', 'editor_tiny'),
        1
    );

    $settings->add($setting);

    $setting = new admin_setting_configtext(
        'editor_tiny/extended_valid_elements',
        new lang_string('extended_valid_elements', 'editor_tiny'),
        new lang_string('extended_valid_elements_desc', 'editor_tiny'),
        'script[*],p[*],i[*]'
    );

    $settings->add($setting);

    $setting = new admin_setting_configselect(
        'editor_tiny/package_source',
        new lang_string('package_source', 'editor_tiny'),
        new lang_string('package_source_desc', 'editor_tiny'),
        manager::PACKAGE_SOURCE_SHIPPED,
        [
            manager::PACKAGE_SOURCE_SHIPPED => new lang_string('package_source_shipped', 'editor_tiny'),
            manager::PACKAGE_SOURCE_STANDALONE => new lang_string('package_source_standalone', 'editor_tiny'),
        ],
    );
    $settings->add($setting);

    $setting = new editor_tiny_admin_setting_package_source(
        'editor_tiny/package_source_standalone_path',
        new lang_string('package_source_standalone_path', 'editor_tiny'),
        new lang_string('package_source_standalone_path_desc', 'editor_tiny'),
        '',
    );
    $settings->add($setting);
    $settings->hide_if(
        'editor_tiny/package_source_standalone_path',
        'editor_tiny/package_source',
        'eq',
        manager::PACKAGE_SOURCE_SHIPPED,
    );
}

// Note: We add editortiny to the settings page here manually rather than deferring to the plugininfo class.
// This ensures that it shows in the category list too.
$ADMIN->add('editortiny', $settings);

foreach (core_plugin_manager::instance()->get_plugins_of_type('tiny') as $plugin) {
    /** @var \editor_tiny\plugininfo\tiny $plugin */
    $plugin->load_settings($ADMIN, 'editortiny', $hassiteconfig);
}

// Required or the editor plugininfo will add this section twice.
unset($settings);
$settings = null;
