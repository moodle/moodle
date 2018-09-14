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
 * Version details
 *
 * @package    theme_adaptable
 * @copyright  2015 Jeremy Hopkins (Coventry University)
 * @copyright  2015 Fernando Acedo (3-bits.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die;

    $temp = new admin_settingpage('theme_adaptable_layout', get_string('layoutsettings', 'theme_adaptable'));
    $temp->add(new admin_setting_heading('theme_adaptable_layout', get_string('layoutsettingsheading', 'theme_adaptable'),
        format_text(get_string('layoutdesc', 'theme_adaptable'), FORMAT_MARKDOWN)));

    // Background Image.
    $name = 'theme_adaptable/homebk';
    $title = get_string('homebk', 'theme_adaptable');
    $description = get_string('homebkdesc', 'theme_adaptable');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'homebk');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Display block in the Left/Right side.
    $name = 'theme_adaptable/blockside';
    $title = get_string('blockside', 'theme_adaptable');
    $description = get_string('blocksidedesc', 'theme_adaptable');
    $setting = new admin_setting_configselect($name, $title, $description, 0,
    array(
            0 => get_string('rightblocks', 'theme_adaptable'),
            1 => get_string('leftblocks', 'theme_adaptable'),
        ));
    $temp->add($setting);

    // View default.
    $name = 'theme_adaptable/viewselect';
    $title = get_string('viewselect', 'theme_adaptable');
    $description = get_string('viewselectdesc', 'theme_adaptable');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Fullscreen width.
    $name = 'theme_adaptable/fullscreenwidth';
    $title = get_string('fullscreenwidth', 'theme_adaptable');
    $description = get_string('fullscreenwidthdesc', 'theme_adaptable');
    $setting = new admin_setting_configselect($name, $title, $description, '98%', $from95to100percent);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Emoticons size.
    $name = 'theme_adaptable/emoticonsize';
    $title = get_string('emoticonsize', 'theme_adaptable');
    $description = get_string('emoticonsizedesc', 'theme_adaptable');
    $default = '16px';
    $choices = $standardfontsize;
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);


    $ADMIN->add('theme_adaptable', $temp);
