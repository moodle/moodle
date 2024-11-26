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
 * User settings
 *
 * @package    theme_adaptable
 * @copyright  &copy; 2019 - Coventry University
 * @copyright  2019 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die;

// User profile.
if ($ADMIN->fulltree) {
    $page = new \theme_adaptable\admin_settingspage(
        'theme_adaptable_user',
        get_string('usersettings', 'theme_adaptable'),
        true
    );

    $page->add(new admin_setting_heading(
        'theme_adaptable_user',
        get_string('usersettingsheading', 'theme_adaptable'),
        format_text(get_string('usersettingsdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
    ));

    // Custom course title.
    $name = 'theme_adaptable/customcoursetitle';
    $title = get_string('customcoursetitle', 'theme_adaptable');
    $description = get_string('customcoursetitledesc', 'theme_adaptable');
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_TEXT);
    $page->add($setting);

    // Custom course subtitle.
    $name = 'theme_adaptable/customcoursesubtitle';
    $title = get_string('customcoursesubtitle', 'theme_adaptable');
    $description = get_string('customcoursesubtitledesc', 'theme_adaptable');
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_TEXT);
    $page->add($setting);

    // Enable or disable tabbed profile.
    $name = 'theme_adaptable/enabletabbedprofile';
    $title = get_string('enabletabbedprofile', 'theme_adaptable');
    $description = get_string('enabletabbedprofiledesc', 'theme_adaptable');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $page->add($setting);

    // Enable or disable tabbed profile edit profile link.
    $name = 'theme_adaptable/enabledtabbedprofileeditprofilelink';
    $title = get_string('enabledtabbedprofileeditprofilelink', 'theme_adaptable');
    $description = get_string('enabledtabbedprofileeditprofilelinkdesc', 'theme_adaptable');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $page->add($setting);

    // Enable or disable tabbed profile user preferences link.
    $name = 'theme_adaptable/enabledtabbedprofileuserpreferenceslink';
    $title = get_string('enabledtabbedprofileuserpreferenceslink', 'theme_adaptable');
    $description = get_string('enabledtabbedprofileuserpreferenceslinkdesc', 'theme_adaptable');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $page->add($setting);

    $asettings->add($page);
}
