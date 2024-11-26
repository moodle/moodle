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
 * Print
 *
 * @package    theme_adaptable
 * @copyright  2020 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die;

// Print settings.
if ($ADMIN->fulltree) {
    $page = new \theme_adaptable\admin_settingspage('theme_adaptable_print', get_string('printsettings', 'theme_adaptable'));

    $page->add(new admin_setting_heading(
        'theme_adaptable_print',
        get_string('printsettingsheading', 'theme_adaptable'),
        format_text(get_string('printsettingsdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
    ));

    $name = 'theme_adaptable/printpageorientation';
    $title = get_string('printpageorientation', 'theme_adaptable');
    $description = get_string('printpageorientationdesc', 'theme_adaptable');
    $choices = [
        'landscape' => get_string('landscape', 'theme_adaptable'),
        'portrait' => get_string('portrait', 'theme_adaptable'),
    ];
    $setting = new admin_setting_configselect($name, $title, $description, 'landscape', $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_adaptable/printbodyfontsize';
    $title = get_string('printbodyfontsize', 'theme_adaptable');
    $description = get_string('printbodyfontsizedesc', 'theme_adaptable');
    $setting = new admin_setting_configtext($name, $title, $description, '11pt', PARAM_TEXT);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_adaptable/printmargin';
    $title = get_string('printmargin', 'theme_adaptable');
    $description = get_string('printmargindesc', 'theme_adaptable');
    $setting = new admin_setting_configtext($name, $title, $description, '2cm 1cm 2cm 2cm', PARAM_TEXT);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_adaptable/printlineheight';
    $title = get_string('printlineheight', 'theme_adaptable');
    $description = get_string('printlineheightdesc', 'theme_adaptable');
    $setting = new admin_setting_configtext($name, $title, $description, '1.2', PARAM_TEXT);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $asettings->add($page);
}
