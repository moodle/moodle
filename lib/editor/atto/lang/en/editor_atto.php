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
 * Strings for component 'editor_atto', language 'en'.
 *
 * @package    editor_atto
 * @copyright  2013 Damyon Wiese  <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['autosavefailed'] = 'Could not connect to the server. If you submit this page now, your changes may be lost.';
$string['autosavefrequency'] = 'Autosave frequency';
$string['autosavefrequency_desc'] = 'This is the number of seconds between auto save attempts. Atto will automatically save the text in the editor according to this setting, so that text can be automatically restored when the same user returns to the same form.';
$string['autosavesucceeded'] = 'Draft saved.';
$string['errorcannotparseline'] = 'The line \'{$a}\' is not in the correct format.';
$string['errorgroupisusedtwice'] = 'The group \'{$a}\' is defined twice; group names must be unique.';
$string['errornopluginsorgroupsfound'] = 'No plugins or groups found; please add some groups and plugins.';
$string['errorpluginnotfound'] = 'The plugin \'{$a}\' cannot be used; it does not appear to be installed.';
$string['errorpluginisusedtwice'] = 'The plugin \'{$a}\' is used twice; plugins can only be defined once.';
$string['errortextrecovery'] = 'Unfortunately the draft version could not be restored.';
$string['pluginname'] = 'Atto HTML editor';
$string['subplugintype_atto'] = 'Atto plugin';
$string['subplugintype_atto_plural'] = 'Atto plugins';
$string['settings'] = 'Atto toolbar settings';
$string['taskautosavecleanup'] = 'Delete expired autosave drafts from the database.';
$string['textrecovered'] = 'A draft version of this text was automatically restored.';
$string['toolbarconfig'] = 'Toolbar config';
$string['toolbarconfig_desc'] = 'The list of plugins and the order they are displayed can be configured here. The configuration consists of groups (one per line) followed by the ordered list of plugins for that group. The group is separated from the plugins with an equals sign and the plugins are separated with commas. The group names must be unique and should indicate what the buttons have in common. Button and group names should not be repeated and may only contain alphanumeric characters.';
$string['editor_command_keycode'] = 'Cmd + {$a}';
$string['editor_control_keycode'] = 'Ctrl + {$a}';
$string['plugin_title_shortcut'] = '{$a->title} [{$a->shortcut}]';
$string['recover'] = 'Recover';
$string['infostatus'] = 'Information';
$string['warningstatus'] = 'Warning';
$string['autosaves'] = 'Editor autosave information';
$string['privacy:metadata:database:atto_autosave'] = 'Automatically saved text editor drafts.';
$string['privacy:metadata:database:atto_autosave:userid'] = 'The ID of the user whose data was saved.';
$string['privacy:metadata:database:atto_autosave:drafttext'] = 'The text which was saved.';
$string['privacy:metadata:database:atto_autosave:timemodified'] = 'The time that content was modified.';
