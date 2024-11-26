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
 * Custom CSS
 *
 * @package    theme_adaptable
 * @copyright  2015 Jeremy Hopkins (Coventry University)
 * @copyright  2015 Fernando Acedo (3-bits.com)
 * @copyright  2023 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die;

// Custom CSS section.
if ($ADMIN->fulltree) {
    $page = new \theme_adaptable\admin_settingspage('theme_adaptable_customcss',
        get_string('customcsssettings', 'theme_adaptable'));

    $page->add(new admin_setting_heading(
        'theme_adaptable_customcss',
        get_string('customcssjssettingsheading', 'theme_adaptable'),
        format_text(get_string('customcsssettingsdescription', 'theme_adaptable'), FORMAT_MARKDOWN)
    ));

    // Custom CSS.
    $name = 'theme_adaptable/customcss';
    $title = get_string('customcss', 'theme_adaptable');
    $description = get_string('customcssdesc', 'theme_adaptable');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Custom H5P CSS.
    $name = 'theme_adaptable/hvpcustomcss';
    $title = get_string('hvpcustomcss', 'theme_adaptable');
    $description = get_string('hvpcustomcssdesc', 'theme_adaptable');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $asettings->add($page);
}
