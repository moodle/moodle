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
 * Header social
 *
 * @package    theme_adaptable
 * @copyright  2015 Jeremy Hopkins (Coventry University)
 * @copyright  2015 Fernando Acedo (3-bits.com)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die;

// Social links.
if ($ADMIN->fulltree) {
    $page = new \theme_adaptable\admin_settingspage('theme_adaptable_social', get_string('socialsettings', 'theme_adaptable'));

    $page->add(new admin_setting_heading(
        'theme_adaptable_social',
        get_string('socialheading', 'theme_adaptable'),
        format_text(get_string('socialtitledesc', 'theme_adaptable'), FORMAT_MARKDOWN)
    ));

    $name = 'theme_adaptable/socialsize';
    $title = get_string('socialsize', 'theme_adaptable');
    $description = get_string('socialsizedesc', 'theme_adaptable');
    $setting = new admin_setting_configselect($name, $title, $description, '37px', $from14to46px);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_adaptable/socialpaddingside';
    $title = get_string('socialpaddingside', 'theme_adaptable');
    $description = get_string('socialpaddingsidedesc', 'theme_adaptable');
    $setting = new admin_setting_configselect($name, $title, $description, 16, $from10to30pxnovalueunit);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_adaptable/socialpaddingtop';
    $title = get_string('socialpaddingtop', 'theme_adaptable');
    $description = get_string('socialpaddingtopdesc', 'theme_adaptable');
    $setting = new admin_setting_configselect($name, $title, $description, '0%', $from0to2point5percent);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_adaptable/socialtarget';
    $title = get_string('socialtarget', 'theme_adaptable');
    $description = get_string('socialtargetdesc', 'theme_adaptable');
    $setting = new admin_setting_configselect($name, $title, $description, '_self', $htmltarget);
    $page->add($setting);

    $name = 'theme_adaptable/socialiconlist';
    $title = get_string('socialiconlist', 'theme_adaptable');
    $default = '';
    $description = get_string('socialiconlistdesc', 'theme_adaptable', 'https://fontawesome.com/search?o=r&m=free');
    $setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '50', '10');
    $page->add($setting);

    $asettings->add($page);
}
