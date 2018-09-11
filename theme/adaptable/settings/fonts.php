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

    // Fonts Section.
    $temp = new admin_settingpage('theme_adaptable_font', get_string('fontsettings', 'theme_adaptable'));
    $temp->add(new admin_setting_heading('theme_adaptable_font', get_string('fontsettingsheading', 'theme_adaptable'),
                   format_text(get_string('fontdesc', 'theme_adaptable'), FORMAT_MARKDOWN)));

    // Fonts heading.
    $name = 'theme_adaptable/settingsfonts';
    $heading = get_string('settingsfonts', 'theme_adaptable');
    $setting = new admin_setting_heading($name, $heading, '');
    $temp->add($setting);

    // Main Font Name.
    $name = 'theme_adaptable/fontname';
    $title = get_string('fontname', 'theme_adaptable');
    $description = get_string('fontnamedesc', 'theme_adaptable');
    $default = 'Open Sans';
    $choices = $fontlist;
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Main Font Subset.
    $name = 'theme_adaptable/fontsubset';
    $title = get_string('fontsubset', 'theme_adaptable');
    $description = get_string('fontsubsetdesc', 'theme_adaptable');
    $default = '';
    $setting = new admin_setting_configmulticheckbox($name, $title, $description, $default, array(
        'latin-ext' => "latin-ext",
        'cyrillic' => "cyrillic",
        'cyrillic-ext' => "cyrillic-ext",
        'greek' => "greek",
        'greek-ext' => "greek-ext",
        'vietnamese' => "vietnamese",
    ));
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Main Font size.
    $name = 'theme_adaptable/fontsize';
    $title = get_string('fontsize', 'theme_adaptable');
    $description = get_string('fontsizedesc', 'theme_adaptable');
    $setting = new admin_setting_configselect($name, $title, $description, '95%', $from85to110percent);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Main Font weight.
    $name = 'theme_adaptable/fontweight';
    $title = get_string('fontweight', 'theme_adaptable');
    $description = get_string('fontweightdesc', 'theme_adaptable');
    $setting = new admin_setting_configselect($name, $title, $description, 400, $from100to900);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Main Font color.
    $name = 'theme_adaptable/fontcolor';
    $title = get_string('fontcolor', 'theme_adaptable');
    $description = get_string('fontcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#333333', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Top Menu Font Size.
    $name = 'theme_adaptable/topmenufontsize';
    $title = get_string('topmenufontsize', 'theme_adaptable');
    $description = get_string('topmenufontsizedesc', 'theme_adaptable');
    $radchoices = $standardfontsize;
    $setting = new admin_setting_configselect($name, $title, $description, '14px', $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Navber Menu Font Size.
    $name = 'theme_adaptable/menufontsize';
    $title = get_string('menufontsize', 'theme_adaptable');
    $description = get_string('menufontsizedesc', 'theme_adaptable');
    $radchoices = $standardfontsize;
    $setting = new admin_setting_configselect($name, $title, $description, '14px', $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Navbar Menu Padding.
    $name = 'theme_adaptable/menufontpadding';
    $title = get_string('menufontpadding', 'theme_adaptable');
    $description = get_string('menufontpaddingdesc', 'theme_adaptable');
    $radchoices = $from10to30px;
    $setting = new admin_setting_configselect($name, $title, $description, '20px', $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Header Font Name.
    $name = 'theme_adaptable/fontheadername';
    $title = get_string('fontheadername', 'theme_adaptable');
    $description = get_string('fontheadernamedesc', 'theme_adaptable');
    $default = 'Roboto';
    $choices = $fontlist;
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Header Font weight.
    $name = 'theme_adaptable/fontheaderweight';
    $title = get_string('fontheaderweight', 'theme_adaptable');
    $description = get_string('fontheaderweightdesc', 'theme_adaptable');
    $setting = new admin_setting_configselect($name, $title, $description, 400, $from100to900);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Header font color.
    $name = 'theme_adaptable/fontheadercolor';
    $title = get_string('fontheadercolor', 'theme_adaptable');
    $description = get_string('fontheadercolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#333333', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Title Font Name.
    $name = 'theme_adaptable/fonttitlename';
    $title = get_string('fonttitlename', 'theme_adaptable');
    $description = get_string('fonttitlenamedesc', 'theme_adaptable');
    $default = 'Roboto Condensed';
    $choices = $fontlist;
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Title Font size.
    $name = 'theme_adaptable/fonttitlesize';
    $title = get_string('fonttitlesize', 'theme_adaptable');
    $description = get_string('fonttitlesizedesc', 'theme_adaptable');
    $default = '48px';
    $choices = $standardfontsize;
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Title Font weight.
    $name = 'theme_adaptable/fonttitleweight';
    $title = get_string('fonttitleweight', 'theme_adaptable');
    $description = get_string('fonttitleweightdesc', 'theme_adaptable');
    $setting = new admin_setting_configselect($name, $title, $description, 400, $from100to900);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Title font color.
    $name = 'theme_adaptable/fonttitlecolor';
    $title = get_string('fonttitlecolor', 'theme_adaptable');
    $description = get_string('fonttitlecolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Course font color.
    $name = 'theme_adaptable/fonttitlecolorcourse';
    $title = get_string('fonttitlecolorcourse', 'theme_adaptable');
    $description = get_string('fonttitlecolorcoursedesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $ADMIN->add('theme_adaptable', $temp);
