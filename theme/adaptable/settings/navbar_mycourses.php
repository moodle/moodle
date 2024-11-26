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
 * Navbar my courses
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
        'theme_adaptable_navbar_mycourses',
        get_string('navbarmycourses', 'theme_adaptable'),
        true
    );

    // My courses section.
    $page->add(
        new admin_setting_heading(
            'theme_adaptable_mycourses_heading',
            get_string('headernavbarmycoursesheading', 'theme_adaptable'),
            format_text(get_string('headernavbarmycoursesheadingdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
        )
    );

    $name = 'theme_adaptable/enablemysites';
    $title = get_string('mysites', 'theme_adaptable');
    $description = get_string('enablemysitesdesc', 'theme_adaptable');
    $choices = [
        'excludehidden' => get_string('mysitesexclude', 'theme_adaptable'),
        'includehidden' => get_string('mysitesinclude', 'theme_adaptable'),
        'disabled' => get_string('mysitesdisabled', 'theme_adaptable'),
    ];
    $setting = new admin_setting_configselect($name, $title, $description, 'excludehidden', $choices);
    $page->add($setting);

    // Custom profile field value for restricting access to my courses menu.
    $name = 'theme_adaptable/enablemysitesrestriction';
    $title = get_string('enablemysitesrestriction', 'theme_adaptable');
    $description = get_string('enablemysitesrestrictiondesc', 'theme_adaptable');
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_RAW);
    $page->add($setting);

    $name = 'theme_adaptable/mycoursesmenulimit';
    $title = get_string('mycoursesmenulimit', 'theme_adaptable');
    $description = get_string('mycoursesmenulimitdesc', 'theme_adaptable');
    $setting = new admin_setting_configtext($name, $title, $description, '20', PARAM_INT);
    $page->add($setting);

    $name = 'theme_adaptable/mysitesmaxlength';
    $title = get_string('mysitesmaxlength', 'theme_adaptable');
    $description = get_string('mysitesmaxlengthdesc', 'theme_adaptable');
    $setting = new admin_setting_configselect($name, $title, $description, '30', $from20to40);
    $page->add($setting);

    $name = 'theme_adaptable/mysitessortoverride';
    $title = get_string('mysitessortoverride', 'theme_adaptable');
    $description = get_string('mysitessortoverridedesc', 'theme_adaptable');
    $choices = [
        'off' => get_string('mysitessortoverrideoff', 'theme_adaptable'),
        'strings' => get_string('mysitessortoverridestrings', 'theme_adaptable'),
        'profilefields' => get_string('mysitessortoverrideprofilefields', 'theme_adaptable'),
        'profilefieldscohort' => get_string('mysitessortoverrideprofilefieldscohort', 'theme_adaptable'),
        'myoverview' => get_string('mysitessortoverridemyoverview', 'theme_adaptable'),
        'last' => get_string('mysitessortoverridelast', 'theme_adaptable'),
    ];
    $setting = new admin_setting_configselect($name, $title, $description, 'myoverview', $choices);
    $page->add($setting);

    $name = 'theme_adaptable/mysitessortoverridefield';
    $title = get_string('mysitessortoverridefield', 'theme_adaptable');
    $description = get_string('mysitessortoverridefielddesc', 'theme_adaptable');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_RAW);
    $page->add($setting);

    $name = 'theme_adaptable/mysitesmenudisplay';
    $title = get_string('mysitesmenudisplay', 'theme_adaptable');
    $description = get_string('mysitesmenudisplaydesc', 'theme_adaptable');
    $displaychoices = [
        'shortcodenohover' => get_string('mysitesmenudisplayshortcodenohover', 'theme_adaptable'),
        'shortcodehover' => get_string('mysitesmenudisplayshortcodefullnameonhover', 'theme_adaptable'),
        'fullnamenohover' => get_string('mysitesmenudisplayfullnamenohover', 'theme_adaptable'),
        'fullnamehover' => get_string('mysitesmenudisplayfullnamefullnameonhover', 'theme_adaptable'),
    ];
    $setting = new admin_setting_configselect($name, $title, $description, 'shortcodehover', $displaychoices);
    $page->add($setting);

    $name = 'theme_adaptable/chiddenicon';
    $title = get_string('chiddenicon', 'theme_adaptable');
    $description = get_string('chiddenicondesc', 'theme_adaptable');
    $default = 'eye-slash';
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $page->add($setting);

    $name = 'theme_adaptable/cfrozenicon';
    $title = get_string('cfrozenicon', 'theme_adaptable');
    $description = get_string('cfrozenicondesc', 'theme_adaptable');
    $default = 'snowflake-o';
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $page->add($setting);

    $name = 'theme_adaptable/cneveraccessedicon';
    $title = get_string('cneveraccessedicon', 'theme_adaptable');
    $description = get_string('cneveraccessedicondesc', 'theme_adaptable');
    $default = 'exclamation-circle';
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $page->add($setting);

    $name = 'theme_adaptable/cdefaulticon';
    $title = get_string('cdefaulticon', 'theme_adaptable');
    $description = get_string('cdefaulticondesc', 'theme_adaptable');
    $default = 'graduation-cap';
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $page->add($setting);

    $asettings->add($page);
}
