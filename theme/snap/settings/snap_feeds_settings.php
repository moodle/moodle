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

global $PAGE;

$snapsettings = new admin_settingpage('themesnapfeeds', get_string('snapfeeds', 'theme_snap'));

// Snap feeds settings.
$advancedfeedsdependants = [];
$name = 'theme_snap/mycoursessnapfeedsheading';
$title = new lang_string('mycoursessnapfeedsheading', 'theme_snap');
$description = new lang_string('mycoursessnapfeedsdesc', 'theme_snap');
$setting = new admin_setting_heading($name, $title, $description);
$snapsettings->add($setting);

// Deadlines on/off.
$name = 'theme_snap/deadlinestoggle';
$title = new lang_string('deadlinestoggle', 'theme_snap');
$description = new lang_string('deadlinestoggledesc', 'theme_snap');
$default = $checked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$advancedfeedsdependencies[] = $setting->name;
$snapsettings->add($setting);

// Recent feedback & grading on/off.
$name = 'theme_snap/feedbacktoggle';
$title = new lang_string('feedbacktoggle', 'theme_snap');
$description = new lang_string('feedbacktoggledesc', 'theme_snap');
$default = $checked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$advancedfeedsdependencies[] = $setting->name;
$snapsettings->add($setting);

// Messages on/off.
$name = 'theme_snap/messagestoggle';
$title = new lang_string('messagestoggle', 'theme_snap');
$description = new lang_string('messagestoggledesc', 'theme_snap');
$default = $checked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$advancedfeedsdependencies[] = $setting->name;
$snapsettings->add($setting);

// Forum posts on/off.
$name = 'theme_snap/forumpoststoggle';
$title = new lang_string('forumpoststoggle', 'theme_snap');
$description = new lang_string('forumpoststoggledesc', 'theme_snap');
$default = $checked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$advancedfeedsdependencies[] = $setting->name;
$snapsettings->add($setting);

// Enable advanced feeds.
$name = 'theme_snap/advancedfeedsenable';
$title = new lang_string('advancedfeedsenable', 'theme_snap');
$description = new lang_string('advancedfeedsenabledesc', 'theme_snap');
$default = $checked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$snapsettings->add($setting);

$name = 'theme_snap/advancedfeedsperpage';
$title = new lang_string('advancedfeedsperpage', 'theme_snap');
$description = new lang_string('advancedfeedsperpagedesc', 'theme_snap');
$default = '3';
$snapfeedperpagechoices = [
    '3' => '3',
    '4' => '4',
    '5' => '5',
    '6' => '6',
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $snapfeedperpagechoices);
$snapsettings->add($setting);

$name = 'theme_snap/advancedfeedslifetime';
$title = new lang_string('advancedfeedslifetime', 'theme_snap');
$description = new lang_string('advancedfeedslifetimedesc', 'theme_snap');
$default = 30 * MINSECS;
$setting = new admin_setting_configduration($name, $title, $description, $default, MINSECS);
$snapsettings->add($setting);

// Refresh deadlines task settings.
$name = 'theme_snap/refreshdeadlinestasksettingheading';
$title = new lang_string('refreshdeadlinestasksettingheading', 'theme_snap');
$description = '';
$setting = new admin_setting_heading($name, $title, $description);
$snapsettings->add($setting);

$name = 'theme_snap/refreshdeadlines';
$title = new lang_string('refreshdeadlines', 'theme_snap');
$description = new lang_string('refreshdeadlinesdesc', 'theme_snap');
$default = !$checked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$snapsettings->add($setting);

$settings->add($snapsettings);

// advancedfeedsenable depends on $advancedfeedsdependencie.
// advancedfeedsperpage and advancedfeedslifetime depends on advancedfeedsenable.
$PAGE->requires->js_call_amd('theme_snap/hide_settings',
    'hideDependingOnChecked',
    array('advancedfeedsenable',
    $advancedfeedsdependencies,
    array('advancedfeedsperpage', 'advancedfeedslifetime')));
