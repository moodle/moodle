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
 * General
 *
 * @package    theme_adaptable
 * @copyright  2024 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die;

// General settings.
if ($ADMIN->fulltree) {
    $page = new \theme_adaptable\admin_settingspage(
        'theme_adaptable_general',
        get_string('settingspagegeneralsettings', 'theme_adaptable')
    );

    $name = 'theme_adaptable/pageloadingprogress';
    $title = get_string('pageloadingprogress', 'theme_adaptable');
    $description = get_string('pageloadingprogressdesc', 'theme_adaptable');
    $setting = new admin_setting_configcheckbox($name, $title, $description, true);
    $page->add($setting);

    $name = 'theme_adaptable/pageloadingprogresstheme';
    $title = get_string('pageloadingprogresstheme', 'theme_adaptable');
    $description = get_string('pageloadingprogressthemedesc', 'theme_adaptable');
    $choices = [
        'minimal' => get_string('pageloadingprogressthememinimal', 'theme_adaptable'),
        'barber_shop' => get_string('pageloadingprogressthemebarbershop', 'theme_adaptable'),
        'big_counter' => get_string('pageloadingprogressthemebigcounter', 'theme_adaptable'),
        'bounce' => get_string('pageloadingprogressthemebounce', 'theme_adaptable'),
        'center_atom' => get_string('pageloadingprogressthemecenteratom', 'theme_adaptable'),
        'center_circle' => get_string('pageloadingprogressthemecentercircle', 'theme_adaptable'),
        'center_radar' => get_string('pageloadingprogressthemecenterradar', 'theme_adaptable'),
        'center_simple' => get_string('pageloadingprogressthemecentersimple', 'theme_adaptable'),
        'corner_indicator' => get_string('pageloadingprogressthemecornerindicator', 'theme_adaptable'),
        'fill_left' => get_string('pageloadingprogressthemefillleft', 'theme_adaptable'),
        'flash' => get_string('pageloadingprogressthemeflash', 'theme_adaptable'),
        'flat_top' => get_string('pageloadingprogressthemeflattop', 'theme_adaptable'),
        'loading_bar' => get_string('pageloadingprogressthemeloadingbar', 'theme_adaptable'),
        'mac_osx' => get_string('pageloadingprogressthememacosx', 'theme_adaptable'),
    ];
    $setting = new admin_setting_configselect($name, $title, $description, 'minimal', $choices);
    $page->add($setting);

    // Loading bar color.
    $name = 'theme_adaptable/loadingcolor';
    $title = get_string('loadingcolor', 'theme_adaptable');
    $description = get_string('loadingcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#00B3A1', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $asettings->add($page);
}
