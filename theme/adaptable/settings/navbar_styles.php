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
 * Navbar styles
 *
 * @package    theme_adaptable
 * @copyright  2015 Jeremy Hopkins (Coventry University)
 * @copyright  2015 Fernando Acedo (3-bits.com)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die;

// Header Navbar.
if ($ADMIN->fulltree) {
    $page = new \theme_adaptable\admin_settingspage('theme_adaptable_navbar_styles', get_string('navbarstyles', 'theme_adaptable'));

    $page->add(new admin_setting_heading(
        'theme_adaptable_navbar_styles',
        get_string('navbarstylesheading', 'theme_adaptable'),
        format_text(get_string('navbarstylesdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
    ));

    // Main menu background color.
    $name = 'theme_adaptable/menubkcolor';
    $title = get_string('menubkcolor', 'theme_adaptable');
    $description = get_string('menubkcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Main menu text color.
    $name = 'theme_adaptable/menufontcolor';
    $title = get_string('menufontcolor', 'theme_adaptable');
    $description = get_string('menufontcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#222222', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Main menu background hover color.
    $name = 'theme_adaptable/menubkhovercolor';
    $title = get_string('menubkhovercolor', 'theme_adaptable');
    $description = get_string('menubkhovercolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#00B3A1', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Main menu text color.
    $name = 'theme_adaptable/menufonthovercolor';
    $title = get_string('menufonthovercolor', 'theme_adaptable');
    $description = get_string('menufonthovercolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Main menu bottom border color.
    $name = 'theme_adaptable/menubordercolor';
    $title = get_string('menubordercolor', 'theme_adaptable');
    $description = get_string('menubordercolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#00B3A1', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_adaptable/navbardisplayicons';
    $title = get_string('navbardisplayicons', 'theme_adaptable');
    $description = get_string('navbardisplayiconsdesc', 'theme_adaptable');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $page->add($setting);

    $name = 'theme_adaptable/navbardisplaysubmenuarrow';
    $title = get_string('navbardisplaysubmenuarrow', 'theme_adaptable');
    $description = get_string('navbardisplaysubmenuarrowdesc', 'theme_adaptable');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $page->add($setting);

    // Dropdown border radius.
    $name = 'theme_adaptable/navbardropdownborderradius';
    $title = get_string('navbardropdownborderradius', 'theme_adaptable');
    $description = get_string('navbardropdownborderradiusdesc', 'theme_adaptable');
    $setting = new admin_setting_configselect($name, $title, $description, '0', $from0to20px);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Dropdown Menu Item Link background hover colour.
    $name = 'theme_adaptable/navbardropdownhovercolor';
    $title = get_string('navbardropdownhovercolor', 'theme_adaptable');
    $description = get_string('navbardropdownhovercolordesc', 'theme_adaptable');
    $default = '#EEE';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Dropdown Menu Item Link text colour.
    $name = 'theme_adaptable/navbardropdowntextcolor';
    $title = get_string('navbardropdowntextcolor', 'theme_adaptable');
    $description = get_string('navbardropdowntextcolordesc', 'theme_adaptable');
    $default = '#007';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Dropdown Menu Item Link text hover colour.
    $name = 'theme_adaptable/navbardropdowntexthovercolor';
    $title = get_string('navbardropdowntexthovercolor', 'theme_adaptable');
    $description = get_string('navbardropdowntexthovercolordesc', 'theme_adaptable');
    $default = '#000';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Dropdown transition time.
    $name = 'theme_adaptable/navbardropdowntransitiontime';
    $title = get_string('navbardropdowntransitiontime', 'theme_adaptable');
    $description = get_string('navbardropdowntransitiontimedesc', 'theme_adaptable');
    $setting = new admin_setting_configselect($name, $title, $description, '0.2s', $from0to1second);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $asettings->add($page);
}
