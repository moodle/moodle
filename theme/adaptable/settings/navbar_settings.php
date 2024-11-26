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
 * Navbar settings
 *
 * @package    theme_adaptable
 * @copyright  2015 Jeremy Hopkins (Coventry University)
 * @copyright  2015 Fernando Acedo (3-bits.com)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die;

// Header Navbar.
if ($ADMIN->fulltree) {
    $page = new \theme_adaptable\admin_settingspage(
        'theme_adaptable_navbar_settings',
        get_string('navbarsettings', 'theme_adaptable')
    );

    $page->add(
        new admin_setting_heading(
            'theme_adaptable_navbar_settings',
            get_string('navbarsettingsheading', 'theme_adaptable'),
            format_text(get_string('navbardesc', 'theme_adaptable'), FORMAT_MARKDOWN)
        )
    );

    // Sticky Navbar at the top. See issue #278.
    $name = 'theme_adaptable/stickynavbar';
    $title = get_string('stickynavbar', 'theme_adaptable');
    $description = get_string('stickynavbardesc', 'theme_adaptable');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $page->add($setting);

    // Enable/Disable menu items.
    $name = 'theme_adaptable/enablehome';
    $title = get_string('home');
    $description = get_string('enablehomedesc', 'theme_adaptable');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $page->add($setting);

    $name = 'theme_adaptable/enablehomeredirect';
    $title = get_string('enablehomeredirect', 'theme_adaptable');
    $description = get_string('enablehomeredirectdesc', 'theme_adaptable');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $page->add($setting);

    $name = 'theme_adaptable/enablemyhome';
    $title = get_string('myhome');
    $description = get_string('enablemydesc', 'theme_adaptable', get_string('myhome'));
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $page->add($setting);

    $name = 'theme_adaptable/enablemycourses';
    $title = get_string('mycourses');
    $description = get_string('enablemydesc', 'theme_adaptable', get_string('mycourses'));
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $page->add($setting);

    $name = 'theme_adaptable/enableevents';
    $title = get_string('events', 'theme_adaptable');
    $description = get_string('enableeventsdesc', 'theme_adaptable');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $page->add($setting);

    $name = 'theme_adaptable/enablethiscourse';
    $title = get_string('thiscourse', 'theme_adaptable');
    $description = get_string('enablethiscoursedesc', 'theme_adaptable');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $page->add($setting);

    $name = 'theme_adaptable/enablecoursesections';
    $title = get_string('coursesections', 'theme_adaptable');
    $description = get_string('enablecoursesectionsdesc', 'theme_adaptable');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $page->add($setting);

    $name = 'theme_adaptable/enablecompetencieslink';
    $title = get_string('enablecompetencieslink', 'theme_adaptable');
    $description = get_string('enablecompetencieslinkdesc', 'theme_adaptable');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $page->add($setting);

    $name = 'theme_adaptable/enablezoom';
    $title = get_string('enablezoom', 'theme_adaptable');
    $description = get_string('enablezoomdesc', 'theme_adaptable');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $page->add($setting);

    $name = 'theme_adaptable/defaultzoom';
    $title = get_string('defaultzoom', 'theme_adaptable');
    $description = get_string('defaultzoomdesc', 'theme_adaptable');
    $choices = [
        'normal' => get_string('normal', 'theme_adaptable'),
        'wide' => get_string('wide', 'theme_adaptable'),
    ];
    $setting = new admin_setting_configselect($name, $title, $description, 'wide', $choices);
    $page->add($setting);

    // Show / hide text for the Full screen button.
    $name = 'theme_adaptable/enablezoomshowtext';
    $title = get_string('enablezoomshowtext', 'theme_adaptable');
    $description = get_string('enablezoomshowtextdesc', 'theme_adaptable');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $page->add($setting);

    $name = 'theme_adaptable/enablenavbarwhenloggedout';
    $title = get_string('enablenavbarwhenloggedout', 'theme_adaptable');
    $description = get_string('enablenavbarwhenloggedoutdesc', 'theme_adaptable');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $page->add($setting);

    // Settings icon and Edit button.
    $name = 'theme_adaptable/editsettingsbutton';
    $title = get_string('editsettingsbutton', 'theme_adaptable');
    $description = get_string('editsettingsbuttondesc', 'theme_adaptable');
    $choices = [
        'cog' => get_string('editsettingsbuttonshowcog', 'theme_adaptable'),
        'button' => get_string('editsettingsbuttonshowbutton', 'theme_adaptable'),
        'cogandbutton' => get_string('editsettingsbuttonshowcogandbutton', 'theme_adaptable'),
    ];
    $setting = new admin_setting_configselect($name, $title, $description, 'cog', $choices);
    $page->add($setting);

    // Show the cog to non-editing teachers.
    $name = 'theme_adaptable/editcognocourseupdate';
    $title = get_string('editcognocourseupdate', 'theme_adaptable');
    $description = get_string('editcognocourseupdatedesc', 'theme_adaptable');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $page->add($setting);

    $name = 'theme_adaptable/displayeditingbuttontext';
    $title = get_string('displayeditingbuttontext', 'theme_adaptable');
    $description = get_string('displayeditingbuttontextdesc', 'theme_adaptable');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $page->add($setting);

    // This course section.
    $page->add(new admin_setting_heading(
        'theme_adaptable_thiscourse_heading',
        get_string('headernavbarthiscourseheading', 'theme_adaptable'),
        format_text(get_string('headernavbarthiscourseheadingdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
    ));

    // Display participants.
    $name = 'theme_adaptable/displayparticipants';
    $title = get_string('displayparticipants', 'theme_adaptable');
    $description = get_string('displayparticipantsdesc', 'theme_adaptable');
    $radchoices = [
        0 => get_string('hide', 'theme_adaptable'),
        1 => get_string('show', 'theme_adaptable'),
    ];
    $setting = new admin_setting_configselect($name, $title, $description, 1, $radchoices);
    $page->add($setting);

    // Display Grades.
    $name = 'theme_adaptable/displaygrades';
    $title = get_string('displaygrades', 'theme_adaptable');
    $description = get_string('displaygradesdesc', 'theme_adaptable');
    $radchoices = [
        0 => get_string('hide', 'theme_adaptable'),
        1 => get_string('show', 'theme_adaptable'),
    ];
    $setting = new admin_setting_configselect($name, $title, $description, 1, $radchoices);
    $page->add($setting);

    $asettings->add($page);
}
