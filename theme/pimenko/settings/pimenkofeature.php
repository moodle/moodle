<?php
// This file is part of the Pimenko theme for Moodle
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
 * Theme Pimenko settings pimenko feature.
 *
 * @package    theme_pimenko
 * @copyright  Pimenko 2020
 * @author     Sylvain Revenu - Pimenko 2020 <contact@pimenko.com> <pimenko.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$page = new admin_settingpage('theme_pimenko_pimenkofeature',
    get_string('pimenkofeature', 'theme_pimenko'));

$page->add(new admin_setting_heading('catalogsettings', get_string('catalogsettings', 'theme_pimenko'),
    get_string('catalogsettings_desc', 'theme_pimenko')));

// Activation of catalog view.
$name = 'theme_pimenko/enablecatalog';
$title = get_string(
    'enablecatalog',
    'theme_pimenko'
);
$description = get_string(
    'enablecatalog_desc',
    'theme_pimenko'
);
$setting = new admin_setting_configcheckbox(
    $name,
    $title,
    $description,
    '0'
);
$page->add($setting);

// Activation of tag filter.
$name = 'theme_pimenko/tagfilter';
$title = get_string(
    'tagfilter',
    'theme_pimenko'
);
$description = get_string(
    'tagfilter_desc',
    'theme_pimenko'
);
$setting = new admin_setting_configcheckbox(
    $name,
    $title,
    $description,
    '0'
);
$page->add($setting);

// Activation of custom field filter.
$name = 'theme_pimenko/customfieldfilter';
$title = get_string(
    'customfieldfilter',
    'theme_pimenko'
);
$description = get_string(
    'customfieldfilter_desc',
    'theme_pimenko'
);
$setting = new admin_setting_configcheckbox(
    $name,
    $title,
    $description,
    '0'
);
$page->add($setting);

// Activation of titlecatalog.
$name = 'theme_pimenko/titlecatalog';
$title = get_string(
    'titlecatalog',
    'theme_pimenko'
);
$description = get_string(
    'titlecatalog_desc',
    'theme_pimenko'
);
$setting = new admin_setting_configtext(
    $name,
    $title,
    $description,
    ''
);
$page->add($setting);

$name = 'theme_pimenko/showsubscriberscount';
$title = get_string(
    'showsubscriberscount',
    'theme_pimenko'
);
$description = get_string(
    'showsubscriberscount_desc',
    'theme_pimenko'
);
$setting = new admin_setting_configcheckbox(
    $name,
    $title,
    $description,
    '0'
);
$page->add($setting);

// View all hidden courses.
$name = 'theme_pimenko/viewallhiddencourses';
$title = get_string(
    'viewallhiddencourses',
    'theme_pimenko'
);
$description = get_string(
    'viewallhiddencourses_desc',
    'theme_pimenko'
);
$setting = new admin_setting_configcheckbox(
    $name,
    $title,
    $description,
    '0'
);
$page->add($setting);

// Activation of titlecatalog.
$name = 'theme_pimenko/catalogsummarymodal';
$title = get_string(
    'catalogsummarymodal',
    'theme_pimenko'
);
$description = get_string(
    'catalogsummarymodal_desc',
    'theme_pimenko'
);
$setting = new admin_setting_configcheckbox(
    $name,
    $title,
    $description,
    '0'
);
$page->add($setting);

$page->add(new admin_setting_heading('coursecoversettings', get_string('coursecoversettings', 'theme_pimenko'),
    get_string('coursecoversettings_desc', 'theme_pimenko')));

// Display or not course cover option.
$name = 'theme_pimenko/displaycoverallpage';
$title = get_string(
    'displaycoverallpage',
    'theme_pimenko'
);
$description = get_string(
    'displaycoverallpage_desc',
    'theme_pimenko'
);
$setting = new admin_setting_configcheckbox(
    $name,
    $title,
    $description,
    '0'
);
$page->add($setting);

// Display cover as a thumbnail.
$name = 'theme_pimenko/displayasthumbnail';
$title = get_string(
    'displayasthumbnail',
    'theme_pimenko'
);
$description = get_string(
    'displayasthumbnail_desc',
    'theme_pimenko'
);
$setting = new admin_setting_configcheckbox(
    $name,
    $title,
    $description,
    '0'
);
$page->add($setting);

// Option to control displaying course title under image.
$name = 'theme_pimenko/displaytitlecourseunderimage';
$title = get_string(
    'displaytitlecourseunderimage', // Updated option name
    'theme_pimenko'
);
$description = get_string(
    'displaytitlecourseunderimage_desc', // Corresponding description
    'theme_pimenko'
);
$setting = new admin_setting_configcheckbox(
    $name,
    $title,
    $description,
    '0'
);
$page->add($setting);

// Gradient for course cover.
$name = 'theme_pimenko/gradientcovercolor';
$title = get_string(
    'gradientcovercolor',
    'theme_pimenko'
);
$description = get_string(
    'gradientcovercolor_desc',
    'theme_pimenko'
);
$previewconfig = null;
$setting = new admin_setting_configcolourpicker(
    $name,
    $title,
    $description,
    '',
    $previewconfig
);
$setting->set_updatedcallback('theme_reset_all_caches');

$page->add($setting);

$name = 'theme_pimenko/gradienttextcolor';
$title = get_string(
    'gradienttextcolor',
    'theme_pimenko'
);
$description = get_string(
    'gradienttextcolor_desc',
    'theme_pimenko'
);

$setting = new admin_setting_configcolourpicker(
    $name,
    $title,
    $description,
    '',
    $previewconfig
);
$setting->set_updatedcallback('theme_reset_all_caches');

$page->add($setting);

$page->add(new admin_setting_heading('otherfeature', get_string('otherfeature', 'theme_pimenko'),
    get_string('otherfeature_desc', 'theme_pimenko')));

// Active or not moodle activity completion.
$name = 'theme_pimenko/moodleactivitycompletion';
$title = get_string(
    'moodleactivitycompletion',
    'theme_pimenko'
);
$description = get_string(
    'moodleactivitycompletion_desc',
    'theme_pimenko'
);
$setting = new admin_setting_configcheckbox(
    $name,
    $title,
    $description,
    '0'
);
$page->add($setting);

// Show or not navigation in mod in course.
$name = 'theme_pimenko/showactivitynavigation';
$title = get_string(
    'showactivitynavigation',
    'theme_pimenko'
);
$description = get_string(
    'showactivitynavigation_desc',
    'theme_pimenko'
);
$setting = new admin_setting_configcheckbox(
    $name,
    $title,
    $description,
    true
);
$page->add($setting);

// Show or not participants node in course.
$name = 'theme_pimenko/showparticipantscourse';
$title = get_string(
    'showparticipantscourse',
    'theme_pimenko'
);
$description = get_string(
    'showparticipantscourse_desc',
    'theme_pimenko'
);
$setting = new admin_setting_configcheckbox(
    $name,
    $title,
    $description,
    true
);
$page->add($setting);

// Roles tabs for showparticipantscourse permission list.
global $DB;
$roles = $DB->get_records('role');
if (!$roles) {
    $myrolearray = ['editingteacher' => 'editingteacher', 'teacher' => 'teacher', 'manager' => 'manager'];
} else {
    foreach ($roles as $role) {
        $myrolearray[$role->shortname] = $role->shortname;
    }
}

// Show or not participants node in course.
$name = 'theme_pimenko/listuserrole';
$title = get_string(
    'listuserrole',
    'theme_pimenko'
);
$description = get_string(
    'listuserrole_desc',
    'theme_pimenko'
);
$setting =
    new admin_setting_configmultiselect($name, $title, $description,
        ['editingteacher' => 'editingteacher', 'teacher' => 'teacher', 'manager' => 'manager'], $myrolearray);
$page->add($setting);

$settings->add($page);
