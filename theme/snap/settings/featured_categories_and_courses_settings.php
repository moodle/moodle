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

use theme_snap\admin_setting_configcourseid;
use theme_snap\admin_setting_configcategoryid;
$title = get_string('featuredcategoriesandcourses', 'theme_snap');
$snapsettings = new admin_settingpage('themesnapfeaturedcategoriesandcourses', $title);

$name = 'theme_snap/cover_image';
$heading = new lang_string('featuredcategories', 'theme_snap');
$description = get_string('featuredcategorieshelp', 'theme_snap');
$setting = new admin_setting_heading($name, $heading, $description);
$snapsettings->add($setting);

// Featured categories heading.
$name = 'theme_snap/fcat_heading';
$title = new lang_string('featuredcategoriesheading', 'theme_snap');
$description = '';
$default = new lang_string('featuredcategories', 'theme_snap');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_RAW_TRIMMED, 50);
$snapsettings->add($setting);

// Featured categories.
$name = 'theme_snap/fcat_one';
$title = new lang_string('featuredcategoryone', 'theme_snap');
$description = '';
$default = '0';
$setting = new admin_setting_configcategoryid($name, $title, $description, $default, PARAM_RAW_TRIMMED);
$snapsettings->add($setting);

$name = 'theme_snap/fcat_two';
$title = new lang_string('featuredcategorytwo', 'theme_snap');
$setting = new admin_setting_configcategoryid($name, $title, $description, $default, PARAM_RAW_TRIMMED);
$snapsettings->add($setting);

$name = 'theme_snap/fcat_three';
$title = new lang_string('featuredcategorythree', 'theme_snap');
$setting = new admin_setting_configcategoryid($name, $title, $description, $default, PARAM_RAW_TRIMMED);
$snapsettings->add($setting);

$name = 'theme_snap/fcat_four';
$title = new lang_string('featuredcategoryfour', 'theme_snap');
$setting = new admin_setting_configcategoryid($name, $title, $description, $default, PARAM_RAW_TRIMMED);
$snapsettings->add($setting);

$name = 'theme_snap/fcat_five';
$title = new lang_string('featuredcategoryfive', 'theme_snap');
$setting = new admin_setting_configcategoryid($name, $title, $description, $default, PARAM_RAW_TRIMMED);
$snapsettings->add($setting);

$name = 'theme_snap/fcat_six';
$title = new lang_string('featuredcategoriesix', 'theme_snap');
$setting = new admin_setting_configcategoryid($name, $title, $description, $default, PARAM_RAW_TRIMMED);
$snapsettings->add($setting);

$name = 'theme_snap/fcat_seven';
$title = new lang_string('featuredcategorieseven', 'theme_snap');
$setting = new admin_setting_configcategoryid($name, $title, $description, $default, PARAM_RAW_TRIMMED);
$snapsettings->add($setting);

$name = 'theme_snap/fcat_eight';
$title = new lang_string('featuredcategoryeight', 'theme_snap');
$setting = new admin_setting_configcategoryid($name, $title, $description, $default, PARAM_RAW_TRIMMED);
$snapsettings->add($setting);

// Browse all categories link.
$name = 'theme_snap/fcat_browse_all';
$title = new lang_string('featuredcategoriesbrowseall', 'theme_snap');
$description = new lang_string('featuredcategoriesbrowsealldesc', 'theme_snap');
$checked = '1';
$unchecked = '0';
$default = $unchecked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$snapsettings->add($setting);


// Featured courses instructions.
$name = 'theme_snap/fc_instructions';
$heading = get_string('featuredcourses', 'theme_snap');
$description = get_string('featuredcourseshelp', 'theme_snap');
$setting = new admin_setting_heading($name, $heading, $description);
$snapsettings->add($setting);

// Featured courses heading.
$name = 'theme_snap/fc_heading';
$title = new lang_string('featuredcoursesheading', 'theme_snap');
$description = '';
$default = new lang_string('featuredcourses', 'theme_snap');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_RAW_TRIMMED, 50);
$snapsettings->add($setting);

// Featured courses.
$name = 'theme_snap/fc_one';
$title = new lang_string('featuredcourseone', 'theme_snap');
$description = '';
$default = '0';
$setting = new admin_setting_configcourseid($name, $title, $description, $default, PARAM_RAW_TRIMMED);
$snapsettings->add($setting);

$name = 'theme_snap/fc_two';
$title = new lang_string('featuredcoursetwo', 'theme_snap');
$setting = new admin_setting_configcourseid($name, $title, $description, $default, PARAM_RAW_TRIMMED);
$snapsettings->add($setting);

$name = 'theme_snap/fc_three';
$title = new lang_string('featuredcoursethree', 'theme_snap');
$setting = new admin_setting_configcourseid($name, $title, $description, $default, PARAM_RAW_TRIMMED);
$snapsettings->add($setting);

$name = 'theme_snap/fc_four';
$title = new lang_string('featuredcoursefour', 'theme_snap');
$setting = new admin_setting_configcourseid($name, $title, $description, $default, PARAM_RAW_TRIMMED);
$snapsettings->add($setting);

$name = 'theme_snap/fc_five';
$title = new lang_string('featuredcoursefive', 'theme_snap');
$setting = new admin_setting_configcourseid($name, $title, $description, $default, PARAM_RAW_TRIMMED);
$snapsettings->add($setting);

$name = 'theme_snap/fc_six';
$title = new lang_string('featuredcoursesix', 'theme_snap');
$setting = new admin_setting_configcourseid($name, $title, $description, $default, PARAM_RAW_TRIMMED);
$snapsettings->add($setting);

$name = 'theme_snap/fc_seven';
$title = new lang_string('featuredcourseseven', 'theme_snap');
$setting = new admin_setting_configcourseid($name, $title, $description, $default, PARAM_RAW_TRIMMED);
$snapsettings->add($setting);

$name = 'theme_snap/fc_eight';
$title = new lang_string('featuredcourseeight', 'theme_snap');
$setting = new admin_setting_configcourseid($name, $title, $description, $default, PARAM_RAW_TRIMMED);
$snapsettings->add($setting);

// Browse all courses link.
$name = 'theme_snap/fc_browse_all';
$title = new lang_string('featuredcoursesbrowseall', 'theme_snap');
$description = new lang_string('featuredcoursesbrowsealldesc', 'theme_snap');
$checked = '1';
$unchecked = '0';
$default = $unchecked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$snapsettings->add($setting);

$settings->add($snapsettings);
