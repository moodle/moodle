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
 * Settings that allow configuration of the preview appearance.
 *
 * @package    atto_preview
 * @copyright  2015 Daniel Thies (dthies@ccal.edu)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$ADMIN->add('editoratto', new admin_category('atto_preview', new lang_string('pluginname', 'atto_preview')));

$settings = new admin_settingpage('atto_preview_settings', new lang_string('settings', 'atto_preview'));
if ($ADMIN->fulltree) {
    // Create a list of page layouts to be available.
    $name = new lang_string('layout', 'atto_preview');
    $desc = new lang_string('layout_desc', 'atto_preview');
    $options = array ('embedded' => 'embedded',
                      'popup' => 'popup',
                      'print' => 'print');
    $default = 'print';

    $setting = new admin_setting_configselect('atto_preview/layout',
                                              $name,
                                              $desc,
                                              $default,
                                              $options);
    $settings->add($setting);
}
