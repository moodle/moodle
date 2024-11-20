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
 * Admin settings configuration for jumbotron section.
 *
 * @package    theme_academi
 * @copyright  2023 onwards LMSACE Dev Team (http://www.lmsace.com)
 * @author    LMSACE Dev Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

// Jumbotron.
$temp = new admin_settingpage('theme_academi_jumbotron', get_string('jumbotronheading', 'theme_academi'));

// Jumbotron heading.
$name = 'theme_academi_jumbotronheading';
$heading = get_string('jumbotronheading', 'theme_academi');
$information = '';
$setting = new admin_setting_heading($name, $heading, $information);
$temp->add($setting);

// Jumbotron Enable or disable option.
$name = 'theme_academi/jumbotronstatus';
$title = get_string('status', 'theme_academi');
$description = get_string('statusdesc', 'theme_academi');
$default = NO;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$temp->add($setting);

// Jumbotron Title.
$name = 'theme_academi/jumbotrontitle';
$title = get_string('title', 'theme_academi');
$description = get_string('titledesc', 'theme_academi');
$default = 'lang:learnanytime';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$temp->add($setting);

// Jumbotron Description.
$name = 'theme_academi/jumbotrondesc';
$title = get_string('description', 'theme_academi');
$description = get_string('description_desc', 'theme_academi');
$default = 'lang:learnanytimedesc';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_TEXT);
$temp->add($setting);

// Jumbotron button text.
$name = 'theme_academi/jumbotronbtntext';
$title = get_string('buttontxt', 'theme_academi');
$description = get_string('jumbotronbtntext_desc', 'theme_academi');
$default = 'lang:viewallcourses';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$temp->add($setting);

// Jumbotron button link.
$name = 'theme_academi/jumbotronbtnlink';
$title = get_string('buttonlink', 'theme_academi');
$description = get_string('jumbotronbtnlink_desc', 'theme_academi');
$default = 'http://www.example.com/';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
$temp->add($setting);

// Jumbotron button target.
$name = 'theme_academi/jumbotronbtntarget';
$title = get_string('buttontarget', 'theme_academi');
$description = get_string('jumbotronbtntarget_desc', 'theme_academi');
$default = NEWWINDOW;
$choices = [
    SAMEWINDOW => get_string('sameWindow', 'theme_academi'),
    NEWWINDOW => get_string('newWindow', 'theme_academi'),
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$temp->add($setting);
$settings->add($temp);
