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

$snapsettings = new admin_settingpage('themesnaphvpcustomcss', get_string('hvpcustomcss', 'theme_snap'));

// H5P Custom CSS.
$name = 'theme_snap/hvpcustomcss';
$title = new lang_string('hvpcustomcss', 'theme_snap');
$description = new lang_string('hvpcustomcssdesc', 'theme_snap');
$default = '';
$setting = new admin_setting_configtextarea($name, $title, $description, $default);

$setting->set_updatedcallback('theme_reset_all_caches');

$snapsettings->add($setting);
$settings->add($snapsettings);
