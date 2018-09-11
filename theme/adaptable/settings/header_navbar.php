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

// Header Navbar.
$temp = new admin_settingpage('theme_adaptable_navbar', get_string('navbarsettings', 'theme_adaptable'));
$temp->add(new admin_setting_heading('theme_adaptable_navbar', get_string('navbarsettingsheading', 'theme_adaptable'),
format_text(get_string('navbardesc', 'theme_adaptable'), FORMAT_MARKDOWN)));


// Sticky Navbar at the top. See issue #278.
$name = 'theme_adaptable/stickynavbar';
$title = get_string('stickynavbar', 'theme_adaptable');
$description = get_string('stickynavbardesc', 'theme_adaptable');
$default = true;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

// Enable/Disable menu items.
$name = 'theme_adaptable/enablehome';
$title = get_string('home');
$description = get_string('enablehomedesc', 'theme_adaptable');
$default = true;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

$name = 'theme_adaptable/enablehomeredirect';
$title = get_string('enablehomeredirect', 'theme_adaptable');
$description = get_string('enablehomeredirectdesc', 'theme_adaptable');
$default = true;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

$name = 'theme_adaptable/enablemyhome';
$title = get_string('myhome');
$description = get_string('enablemyhomedesc', 'theme_adaptable', get_string('myhome'));
$default = true;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

$name = 'theme_adaptable/enableevents';
$title = get_string('events', 'theme_adaptable');
$description = get_string('enableeventsdesc', 'theme_adaptable');
$default = true;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

$name = 'theme_adaptable/enablethiscourse';
$title = get_string('thiscourse', 'theme_adaptable');
$description = get_string('enablethiscoursedesc', 'theme_adaptable');
$default = true;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

$name = 'theme_adaptable/enablezoom';
$title = get_string('enablezoom', 'theme_adaptable');
$description = get_string('enablezoomdesc', 'theme_adaptable');
$default = true;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

$name = 'theme_adaptable/enableshowhideblocks';
$title = get_string('enableshowhideblocks', 'theme_adaptable');
$description = get_string('enableshowhideblocksdesc', 'theme_adaptable');
$default = true;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

$name = 'theme_adaptable/enablenavbarwhenloggedout';
$title = get_string('enablenavbarwhenloggedout', 'theme_adaptable');
$description = get_string('enablenavbarwhenloggedoutdesc', 'theme_adaptable');
$default = false;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

// Navbar styling.
$temp->add(new admin_setting_heading('theme_adaptable_navbar_styling_heading',
        get_string('headernavbarstylingheading', 'theme_adaptable'),
        format_text(get_string('headernavbarstylingheadingdesc', 'theme_adaptable'), FORMAT_MARKDOWN)));

$name = 'theme_adaptable/navbardisplayicons';
$title = get_string('navbardisplayicons', 'theme_adaptable');
$description = get_string('navbardisplayiconsdesc', 'theme_adaptable');
$default = true;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

$name = 'theme_adaptable/navbardisplaysubmenuarrow';
$title = get_string('navbardisplaysubmenuarrow', 'theme_adaptable');
$description = get_string('navbardisplaysubmenuarrowdesc', 'theme_adaptable');
$default = false;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

// Dropdown border radius.
$name = 'theme_adaptable/navbardropdownborderradius';
$title = get_string('navbardropdownborderradius', 'theme_adaptable');
$description = get_string('navbardropdownborderradiusdesc', 'theme_adaptable');
$setting = new admin_setting_configselect($name, $title, $description, '0px', $from0to20px);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

// Dropdown Menu Item Link hover colour.
$name = 'theme_adaptable/navbardropdownhovercolor';
$title = get_string('navbardropdownhovercolor', 'theme_adaptable');
$description = get_string('navbardropdownhovercolordesc', 'theme_adaptable');
$default = '#EEE';
$previewconfig = null;
$setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

// Dropdown transition time.
$name = 'theme_adaptable/navbardropdowntransitiontime';
$title = get_string('navbardropdowntransitiontime', 'theme_adaptable');
$description = get_string('navbardropdowntransitiontimedesc', 'theme_adaptable');
$setting = new admin_setting_configselect($name, $title, $description, '0.2s', $from0to1second);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

// My courses section.
$temp->add(new admin_setting_heading('theme_adaptable_mycourses_heading',
        get_string('headernavbarmycoursesheading', 'theme_adaptable'),
        format_text(get_string('headernavbarmycoursesheadingdesc', 'theme_adaptable'), FORMAT_MARKDOWN)));

$name = 'theme_adaptable/enablemysites';
$title = get_string('mysites', 'theme_adaptable');
$description = get_string('enablemysitesdesc', 'theme_adaptable');
$choices = array(
    'excludehidden' => get_string('mysitesexclude', 'theme_adaptable'),
    'includehidden' => get_string('mysitesinclude', 'theme_adaptable'),
    'disabled' => get_string('mysitesdisabled', 'theme_adaptable'),
);
$setting->set_updatedcallback('theme_reset_all_caches');
$setting = new admin_setting_configselect($name, $title, $description, 'excludehidden', $choices);
$temp->add($setting);

// Custom profile field value for restricting access to my courses menu.
$name = 'theme_adaptable/enablemysitesrestriction';
$title = get_string('enablemysitesrestriction', 'theme_adaptable');
$description = get_string('enablemysitesrestrictiondesc', 'theme_adaptable');
$setting = new admin_setting_configtext($name, $title, $description, '', PARAM_RAW);
$temp->add($setting);

$name = 'theme_adaptable/mycoursesmenulimit';
$title = get_string('mycoursesmenulimit', 'theme_adaptable');
$description = get_string('mycoursesmenulimitdesc', 'theme_adaptable');
$setting = new admin_setting_configtext($name, $title, $description, '20', PARAM_INT);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

$name = 'theme_adaptable/mysitesmaxlength';
$title = get_string('mysitesmaxlength', 'theme_adaptable');
$description = get_string('mysitesmaxlengthdesc', 'theme_adaptable');
$setting = new admin_setting_configselect($name, $title, $description, '30', $from20to40);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

$name = 'theme_adaptable/mysitessortoverride';
$title = get_string('mysitessortoverride', 'theme_adaptable');
$description = get_string('mysitessortoverridedesc', 'theme_adaptable');
$choices = array(
    'off' => get_string('mysitessortoverrideoff', 'theme_adaptable'),
    'strings' => get_string('mysitessortoverridestrings', 'theme_adaptable'),
    'profilefields' => get_string('mysitessortoverrideprofilefields', 'theme_adaptable'),
    'profilefieldscohort' => get_string('mysitessortoverrideprofilefieldscohort', 'theme_adaptable')
);
$setting = new admin_setting_configselect($name, $title, $description, 'off', $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

$name = 'theme_adaptable/mysitessortoverridefield';
$title = get_string('mysitessortoverridefield', 'theme_adaptable');
$description = get_string('mysitessortoverridefielddesc', 'theme_adaptable');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_RAW);
$temp->add($setting);

$name = 'theme_adaptable/mysitesmenudisplay';
$title = get_string('mysitesmenudisplay', 'theme_adaptable');
$description = get_string('mysitesmenudisplaydesc', 'theme_adaptable');
$displaychoices = array(
        'shortcodenohover' => get_string('mysitesmenudisplayshortcodenohover', 'theme_adaptable'),
        'shortcodehover' => get_string('mysitesmenudisplayshortcodefullnameonhover', 'theme_adaptable'),
        'fullnamenohover' => get_string('mysitesmenudisplayfullnamenohover', 'theme_adaptable'),
        'fullnamehover' => get_string('mysitesmenudisplayfullnamefullnameonhover', 'theme_adaptable')

);
$setting->set_updatedcallback('theme_reset_all_caches');
$setting = new admin_setting_configselect($name, $title, $description, 'shortcodehover', $displaychoices);
$temp->add($setting);

// This course section.
$temp->add(new admin_setting_heading('theme_adaptable_thiscourse_heading',
        get_string('headernavbarthiscourseheading', 'theme_adaptable'),
        format_text(get_string('headernavbarthiscourseheadingdesc', 'theme_adaptable'), FORMAT_MARKDOWN)));

// Display participants.
$name = 'theme_adaptable/displayparticipants';
$title = get_string('displayparticipants', 'theme_adaptable');
$description = get_string('displayparticipantsdesc', 'theme_adaptable');
$radchoices = array(
    0 => get_string('hide', 'theme_adaptable'),
    1 => get_string('show', 'theme_adaptable'),
);
$setting = new admin_setting_configselect($name, $title, $description, 1, $radchoices);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

// Display Grades.
$name = 'theme_adaptable/displaygrades';
$title = get_string('displaygrades', 'theme_adaptable');
$description = get_string('displaygradesdesc', 'theme_adaptable');
$radchoices = array(
    0 => get_string('hide', 'theme_adaptable'),
    1 => get_string('show', 'theme_adaptable'),
);
$setting = new admin_setting_configselect($name, $title, $description, 1, $radchoices);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);


// Help section.
$temp->add(new admin_setting_heading('theme_adaptable_help_heading',
        get_string('headernavbarhelpheading', 'theme_adaptable'),
        format_text(get_string('headernavbarhelpheadingdesc', 'theme_adaptable'), FORMAT_MARKDOWN)));

// Enable help link.
$name = 'theme_adaptable/enablehelp';
$title = get_string('enablehelp', 'theme_adaptable');
$description = get_string('enablehelpdesc', 'theme_adaptable');
$setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
$temp->add($setting);

$name = 'theme_adaptable/helpprofilefield';
$title = get_string('helpprofilefield', 'theme_adaptable');
$description = get_string('helpprofilefielddesc', 'theme_adaptable');
$setting = new admin_setting_configtext($name, $title, $description, '', PARAM_RAW);
$temp->add($setting);

$name = 'theme_adaptable/enablehelp2';
$title = get_string('enablehelp', 'theme_adaptable');
$description = get_string('enablehelpdesc', 'theme_adaptable');
$setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
$temp->add($setting);

$name = 'theme_adaptable/helpprofilefield2';
$title = get_string('helpprofilefield', 'theme_adaptable');
$description = get_string('helpprofilefielddesc', 'theme_adaptable');
$setting = new admin_setting_configtext($name, $title, $description, '', PARAM_RAW);
$temp->add($setting);

$name = 'theme_adaptable/helptarget';
$title = get_string('helptarget', 'theme_adaptable');
$description = get_string('helptargetdesc', 'theme_adaptable');
$choices = array(
    '_blank' => get_string('targetnewwindow', 'theme_adaptable'),
    '_self' => get_string('targetsamewindow', 'theme_adaptable'),
);
$setting = new admin_setting_configselect($name, $title, $description, '_blank', $choices);
$temp->add($setting);


// Create page.
$ADMIN->add('theme_adaptable', $temp);
