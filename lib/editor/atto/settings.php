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
 * Atto admin settings
 *
 * @package    editor_atto
 * @copyright  2013 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$ADMIN->add('editorsettings', new admin_category('editoratto', $editor->displayname, $editor->is_enabled() === false));

$settings = new admin_settingpage('editorsettingsatto', new lang_string('settings', 'editor_atto'));
if ($ADMIN->fulltree) {
    $name = new lang_string('toolbarconfig', 'editor_atto');
    $desc = new lang_string('toolbarconfig_desc', 'editor_atto');
    $default = 'collapse = collapse' . "\n" .
               'style1 = title, bold, italic' . "\n" .
               'list = unorderedlist, orderedlist' . "\n" .
               'links = link, unlink' . "\n" .
               'files = image, media, managefiles' . "\n" .
               'style2 = underline, strike, subscript, superscript' . "\n" .
               'indent = indent, outdent' . "\n" .
               'insert = charmap, table, clear' . "\n" .
               'accessibility = accessibilitychecker, accessibilityhelper' . "\n" .
               'other = html';
    $setting = new admin_setting_configtextarea('editor_atto/toolbar',
                                                    $name,
                                                    $desc,
                                                    $default);

    $settings->add($setting);

}
$ADMIN->add('editoratto', $settings);

foreach (core_plugin_manager::instance()->get_plugins_of_type('atto') as $plugin) {
    /** @var \editor_atto\plugininfo\atto $plugin */
    $plugin->load_settings($ADMIN, 'editoratto', $hassiteconfig);
}

// Required or the editor plugininfo will add this section twice.
unset($settings);
$settings = null;
