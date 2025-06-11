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
 * This file represents main settings for the plugin.
 *
 * @package    atto_panoptoltibutton
 * @copyright  2024 Panopto
 * @author     Panopto with contributions from David Shepard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

$ADMIN->add('editoratto', new admin_category('atto_panoptoltibutton', new lang_string('pluginname', 'atto_panoptoltibutton')));

$settings = new admin_settingpage('atto_panoptoltibutton_settings', new lang_string('settings', 'atto_panoptoltibutton'));
if ($ADMIN->fulltree) {
    // An option setting.
    $settings->add(
        new admin_setting_configcheckbox(
            'atto_panoptoltibutton/is_responsive',
            get_string('is_responsive', 'atto_panoptoltibutton'),
            get_string('is_responsive_desc', 'atto_panoptoltibutton'),
            0
        )
    );
}
