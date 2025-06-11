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

defined('MOODLE_INTERNAL') || die;// Main settings.

$snapsettings = new admin_settingpage('snapfootersettings', get_string('snapfootersettings', 'theme_snap'));

$name = 'theme_snap/footerheading';
$title = new lang_string('snapfootercustomization', 'theme_snap');
$description = new lang_string('snapfootercustomizationdesc', 'theme_snap');;
$setting = new admin_setting_heading($name, $title, $description);
$snapsettings->add($setting);

// Custom footer setting.
$name = 'theme_snap/footnote';
$title = new lang_string('footnote', 'theme_snap');
$description = new lang_string('footnotedesc', 'theme_snap');
$default = '';
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$snapsettings->add($setting);

$name = 'theme_snap/footercolorsheading';
$title = new lang_string('snapfootercolors', 'theme_snap');
$description = new lang_string('snapfootercolorsdesc', 'theme_snap');;
$setting = new admin_setting_heading($name, $title, $description);
$snapsettings->add($setting);

// Custom footer background color setting.
$name = 'theme_snap/footerbg';
$title = new lang_string('snapfooterbgcolor', 'theme_snap');
$description = '';
$default = '#474747';
$setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$snapsettings->add($setting);

// Custom footer text color setting.
$name = 'theme_snap/footertxt';
$title = new lang_string('snapfootertxtcolor', 'theme_snap');
$description = '';
$default = '#ffffff';
$setting = new \theme_snap\admin_setting_configcolorwithcontrast(
    \theme_snap\admin_setting_configcolorwithcontrast::FOOTER, $name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$snapsettings->add($setting);

$settings->add($snapsettings);
