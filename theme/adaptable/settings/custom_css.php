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
 * @copyright 2015 Jeremy Hopkins (Coventry University)
 * @copyright 2015 Fernando Acedo (3-bits.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die;

    // Custom CSS and JS section.
    $temp = new admin_settingpage('theme_adaptable_generic', get_string('customcssjssettings', 'theme_adaptable'));
    $temp->add(new admin_setting_heading('theme_adaptable_generic', get_string('genericsettingsheading', 'theme_adaptable'),
        format_text(get_string('genericsettingsdescription', 'theme_adaptable'), FORMAT_MARKDOWN)));

    // Custom CSS file.
    $name = 'theme_adaptable/customcss';
    $title = get_string('customcss', 'theme_adaptable');
    $description = get_string('customcssdesc', 'theme_adaptable');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Section for javascript to be added e.g. Google Analytics.
    $name = 'theme_adaptable/jssection';
    $title = get_string('jssection', 'theme_adaptable');
    $description = get_string('jssectiondesc', 'theme_adaptable');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $temp->add($setting);

    $ADMIN->add('theme_adaptable', $temp);
