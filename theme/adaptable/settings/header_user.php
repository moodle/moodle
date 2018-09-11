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

    // Navbar.
    $temp = new admin_settingpage('theme_adaptable_usernav', get_string('usernav', 'theme_adaptable'));
    $temp->add(new admin_setting_heading('theme_adaptable_usernav', get_string('usernavheading', 'theme_adaptable'),
           format_text(get_string('usernavdesc', 'theme_adaptable'), FORMAT_MARKDOWN)));

    $name = 'theme_adaptable/hideinforum';
    $title = get_string('hideinforum', 'theme_adaptable');
    $description = get_string('hideinforumdesc', 'theme_adaptable');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Enable My.
    $name = 'theme_adaptable/enablemy';
    $title = get_string('enablemy', 'theme_adaptable');
    $description = get_string('enablemydesc', 'theme_adaptable');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Enable View Profile.
    $name = 'theme_adaptable/enableprofile';
    $title = get_string('enableprofile', 'theme_adaptable');
    $description = get_string('enableprofiledesc', 'theme_adaptable');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Enable Edit Profile.
    $name = 'theme_adaptable/enableeditprofile';
    $title = get_string('enableeditprofile', 'theme_adaptable');
    $description = get_string('enableeditprofiledesc', 'theme_adaptable');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Enable Calendar.
    $name = 'theme_adaptable/enablecalendar';
    $title = get_string('enablecalendar', 'theme_adaptable');
    $description = get_string('enablecalendardesc', 'theme_adaptable');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Enable Private Files.
    $name = 'theme_adaptable/enableprivatefiles';
    $title = get_string('enableprivatefiles', 'theme_adaptable');
    $description = get_string('enableprivatefilesdesc', 'theme_adaptable');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Enable Grades.
    $name = 'theme_adaptable/enablegrades';
    $title = get_string('enablegrades', 'theme_adaptable');
    $description = get_string('enablegradesdesc', 'theme_adaptable');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Enable Badges.
    $name = 'theme_adaptable/enablebadges';
    $title = get_string('enablebadges', 'theme_adaptable');
    $description = get_string('enablebadgesdesc', 'theme_adaptable');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Enable Preferences.
    $name = 'theme_adaptable/enablepref';
    $title = get_string('enablepref', 'theme_adaptable');
    $description = get_string('enableprefdesc', 'theme_adaptable');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Enable Notes.
    $name = 'theme_adaptable/enablenote';
    $title = get_string('enablenote', 'theme_adaptable');
    $description = get_string('enablenotedesc', 'theme_adaptable');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Enable Blog.
    $name = 'theme_adaptable/enableblog';
    $title = get_string('enableblog', 'theme_adaptable');
    $description = get_string('enableblogdesc', 'theme_adaptable');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Enable Forum posts.
    $name = 'theme_adaptable/enableposts';
    $title = get_string('enableposts', 'theme_adaptable');
    $description = get_string('enablepostsdesc', 'theme_adaptable');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Enable Feed.
    $name = 'theme_adaptable/enablefeed';
    $title = get_string('enablefeed', 'theme_adaptable');
    $description = get_string('enablefeeddesc', 'theme_adaptable');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $ADMIN->add('theme_adaptable', $temp);
