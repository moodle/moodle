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
 * Fonts
 *
 * @package   theme_adaptable
 * @copyright  2015-2018 Jeremy Hopkins (Coventry University)
 * @copyright  2015-2018 Fernando Acedo (3-bits.com)
 * @copyright  2017-2018 Manoj Solanki (Coventry University)
 * @copyright  2020 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die;

// Fonts Section.
if ($ADMIN->fulltree) {
    $page = new \theme_adaptable\admin_settingspage('theme_adaptable_font', get_string('fontsettings', 'theme_adaptable'));

    $page->add(new admin_setting_heading(
        'theme_adaptable_font',
        get_string('fontsettingsheading', 'theme_adaptable'),
        format_text(get_string('fontdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
    ));

    // Font Awesome 6 Free.
    $name = 'theme_adaptable/fav';
    $title = get_string('fav', 'theme_adaptable');
    $description = get_string('favdesc', 'theme_adaptable');
    $default = 0;
    $choices = [
        0 => new \lang_string('favoff', 'theme_adaptable'),
        2 => new \lang_string('fa6name', 'theme_adaptable'),
    ];
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('purge_all_caches');
    $page->add($setting);

    // Font Awesome 6 Free v4 shims.
    $name = 'theme_adaptable/faiv';
    $title = get_string('faiv', 'theme_adaptable');
    $description = get_string('faivdesc', 'theme_adaptable');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('purge_all_caches');
    $page->add($setting);

    // Fonts heading.
    $name = 'theme_adaptable/settingsfonts';
    $heading = get_string('settingsfonts', 'theme_adaptable');
    $setting = new admin_setting_heading($name, $heading, '');
    $page->add($setting);

    // Google fonts.
    $name = 'theme_adaptable/googlefonts';
    $title = get_string('googlefonts', 'theme_adaptable');
    $description = get_string('googlefontsdesc', 'theme_adaptable', 'https://www.google.com/fonts');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $page->add($setting);

    // Main Font Name.
    $name = 'theme_adaptable/fontname';
    $title = get_string('fontname', 'theme_adaptable');
    $description = get_string('fontnamedesc', 'theme_adaptable');
    $default = 'default';
    $setting = new admin_setting_configselect($name, $title, $description, $default, $fontlist);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Main Font Subset.
    $name = 'theme_adaptable/fontsubset';
    $title = get_string('fontsubset', 'theme_adaptable');
    $description = get_string('fontsubsetdesc', 'theme_adaptable');
    $default = '';
    $setting = new admin_setting_configmulticheckbox($name, $title, $description, $default, [
        'latin-ext' => "Latin Extended",
        'cyrillic' => "Cyrillic",
        'cyrillic-ext' => "Cyrillic Extended",
        'greek' => "Greek",
        'greek-ext' => "Greek Extended",
        'vietnamese' => "Vietnamese",
        'arabic' => "Arabic",
        'hebrew' => "Hebrew",
        'japanese' => "Japanese",
        'korean' => "Korean",
        'tamil' => "Tamil",
        'thai' => "Thai",
    ]);
    $page->add($setting);

    // Main Font size.
    $name = 'theme_adaptable/fontsize';
    $title = get_string('fontsize', 'theme_adaptable');
    $description = get_string('fontsizedesc', 'theme_adaptable');
    $setting = new admin_setting_configselect($name, $title, $description, '95%', $from85to110percent);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Main Font weight.
    $name = 'theme_adaptable/fontweight';
    $title = get_string('fontweight', 'theme_adaptable');
    $description = get_string('fontweightdesc', 'theme_adaptable');
    $setting = new admin_setting_configselect($name, $title, $description, 400, $from100to900);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Top Menu Font Size.
    $name = 'theme_adaptable/topmenufontsize';
    $title = get_string('topmenufontsize', 'theme_adaptable');
    $description = get_string('topmenufontsizedesc', 'theme_adaptable');
    $setting = new admin_setting_configselect($name, $title, $description, '14px', $standardfontsize);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Navbar Menu Font Size.
    $name = 'theme_adaptable/menufontsize';
    $title = get_string('menufontsize', 'theme_adaptable');
    $description = get_string('menufontsizedesc', 'theme_adaptable');
    $setting = new admin_setting_configselect($name, $title, $description, '14px', $standardfontsize);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Navbar Menu Padding.
    $name = 'theme_adaptable/menufontpadding';
    $title = get_string('menufontpadding', 'theme_adaptable');
    $description = get_string('menufontpaddingdesc', 'theme_adaptable');
    $setting = new admin_setting_configselect($name, $title, $description, '20px', $from10to30px);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Header Font Name.
    $name = 'theme_adaptable/fontheadername';
    $title = get_string('fontheadername', 'theme_adaptable');
    $description = get_string('fontheadernamedesc', 'theme_adaptable');
    $default = 'default';
    $setting = new admin_setting_configselect($name, $title, $description, $default, $fontlist);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Header Font weight.
    $name = 'theme_adaptable/fontheaderweight';
    $title = get_string('fontheaderweight', 'theme_adaptable');
    $description = get_string('fontheaderweightdesc', 'theme_adaptable');
    $setting = new admin_setting_configselect($name, $title, $description, 400, $from100to900);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Header font color.
    $name = 'theme_adaptable/fontheadercolor';
    $title = get_string('fontheadercolor', 'theme_adaptable');
    $description = get_string('fontheadercolordesc', 'theme_adaptable');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#333333', null);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Title Font Name.
    $name = 'theme_adaptable/fonttitlename';
    $title = get_string('fonttitlename', 'theme_adaptable');
    $description = get_string('fonttitlenamedesc', 'theme_adaptable');
    $default = 'default';
    $setting = new admin_setting_configselect($name, $title, $description, $default, $fontlist);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Title Font size.
    $name = 'theme_adaptable/fonttitlesize';
    $title = get_string('fonttitlesize', 'theme_adaptable');
    $description = get_string('fonttitlesizedesc', 'theme_adaptable');
    $default = '48px';
    $setting = new admin_setting_configselect($name, $title, $description, $default, $standardfontsize);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Title Font weight.
    $name = 'theme_adaptable/fonttitleweight';
    $title = get_string('fonttitleweight', 'theme_adaptable');
    $description = get_string('fonttitleweightdesc', 'theme_adaptable');
    $setting = new admin_setting_configselect($name, $title, $description, 400, $from100to900);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Title font color.
    $name = 'theme_adaptable/fonttitlecolor';
    $title = get_string('fonttitlecolor', 'theme_adaptable');
    $description = get_string('fonttitlecolordesc', 'theme_adaptable');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', null);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Course font color.
    $name = 'theme_adaptable/fonttitlecolorcourse';
    $title = get_string('fonttitlecolorcourse', 'theme_adaptable');
    $description = get_string('fonttitlecolorcoursedesc', 'theme_adaptable');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', null);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $asettings->add($page);
}
