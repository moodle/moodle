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
 * Settings that allow configuring various tiny Font Color plugin features.
 *
 * @package     tiny_fontcolor
 * @copyright   2023 Luca Bösch <luca.boesch@bfh.ch>
 * @copyright   2023 Stephan Robotta <stephan.robotta@bfh.ch>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use tiny_fontcolor\admin_setting_colorlist;

$ADMIN->add('editortiny', new admin_category('tiny_fontcolor', new lang_string('pluginname', 'tiny_fontcolor')));

$settings = new admin_settingpage('tiny_fontcolor_settings', new lang_string('settings', 'tiny_fontcolor'));
if ($ADMIN->fulltree) {
    $setting = new admin_setting_colorlist(
        'tiny_fontcolor/textcolors',
        new lang_string('textcolors', 'tiny_fontcolor'),
        new lang_string('textcolors_desc', 'tiny_fontcolor'),
        ''
    );
    $settings->add($setting);

    $setting = new admin_setting_colorlist(
        'tiny_fontcolor/backgroundcolors',
        new lang_string('backgroundcolors', 'tiny_fontcolor'),
        new lang_string('backgroundcolors_desc', 'tiny_fontcolor'),
        ''
    );
    $settings->add($setting);

    $offon = [
        0 => get_string('disabled', 'core_adminpresets'),
        1 => get_string('enabled', 'core_adminpresets'),
    ];
    $setting = new admin_setting_configselect(
        'tiny_fontcolor/textcolorpicker',
        new lang_string('textcolorpicker', 'tiny_fontcolor'),
        new lang_string('textcolorpicker_desc', 'tiny_fontcolor'),
        0,
        $offon
    );
    $settings->add($setting);

    $setting = new admin_setting_configselect(
        'tiny_fontcolor/backgroundcolorpicker',
        new lang_string('backgroundcolorpicker', 'tiny_fontcolor'),
        new lang_string('backgroundcolorpicker_desc', 'tiny_fontcolor'),
        0,
        $offon
    );
    $settings->add($setting);

    $setting = new admin_setting_configcheckbox(
        'tiny_fontcolor/usecssclassnames',
        new lang_string('usecssclassnames', 'tiny_fontcolor'),
        new lang_string('usecssclassnames_desc', 'tiny_fontcolor'),
        0
    );
    $settings->add($setting);
}

