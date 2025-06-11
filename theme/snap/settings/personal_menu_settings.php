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

$snapsettings = new admin_settingpage('themesnappersonalmenu', get_string('personalmenuandsnapfeeds', 'theme_snap'));

// Snap Personal Menu is now deprecated and will be completely removed in Moodle 4.5.
// Only available for Behat tests until it is fully removed.
if (defined('BEHAT_SITE_RUNNING') && BEHAT_SITE_RUNNING) {
    // Personal menu enable settings.
    $name = 'theme_snap/personalmenuenablepersonalmenuheading';
    $title = new lang_string('personalmenuenablepersonalmenuheading', 'theme_snap');
    $description = new lang_string('personalmenuenablepersonalmenuheadingdesc', 'theme_snap');
    $setting = new admin_setting_heading($name, $title, $description);
    $snapsettings->add($setting);

    $name = 'theme_snap/personalmenuenablepersonalmenu';
    $title = new lang_string('personalmenuenablepersonalmenu', 'theme_snap');
    $description = new lang_string('personalmenuenablepersonalmenudesc', 'theme_snap');
    $default = !$checked;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
    $snapsettings->add($setting);

    // Personal menu display on login on/off.
    $name = 'theme_snap/personalmenulogintoggle';
    $title = new lang_string('personalmenulogintoggle', 'theme_snap');
    $description = new lang_string('personalmenulogintoggledesc', 'theme_snap');
    $default = !$checked;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
    $snapsettings->add($setting);

    // Personal menu show course grade in cards.
    $name = 'theme_snap/showcoursegradepersonalmenu';
    $title = new lang_string('showcoursegradepersonalmenu', 'theme_snap');
    $description = new lang_string('showcoursegradepersonalmenudesc', 'theme_snap');
    $default = $checked;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
    $snapsettings->add($setting);

    // Personal menu settings to hide when it is disabled.
    $dependency = 'theme_snap/personalmenuenablepersonalmenu';
    $tohide     = 'theme_snap/personalmenulogintoggle';
    $settings->hide_if($tohide, $dependency, 'notchecked', 0);
    $tohide     = 'theme_snap/showcoursegradepersonalmenu';
    $settings->hide_if($tohide, $dependency, 'notchecked', 0);
}

// Snap feeds settings.
$advancedfeedsdependants = [];
$name = 'theme_snap/mycoursessnapfeedsheading';
$title = new lang_string('mycoursessnapfeedsheading', 'theme_snap');
$description = new lang_string('mycoursessnapfeedsdesc', 'theme_snap');
$setting = new admin_setting_heading($name, $title, $description);
$snapsettings->add($setting);

// BEGIN LSU Course Card Quick Links.
// Personal menu show course quick links in cards.
$name = 'theme_snap/showcoursecardquicklinks';
$title = new lang_string('showcoursecardquicklinks', 'theme_snap');
$description = new lang_string('showcoursecardquicklinksdesc', 'theme_snap');
$default = $checked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$snapsettings->add($setting);
// END LSU Course Card Quick Links.

// Personal menu deadlines on/off.
$name = 'theme_snap/deadlinestoggle';
$title = new lang_string('deadlinestoggle', 'theme_snap');
$description = new lang_string('deadlinestoggledesc', 'theme_snap');
$default = $checked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$advancedfeedsdependencies[] = $setting->name;
$snapsettings->add($setting);

// Personal menu recent feedback & grading on/off.
$name = 'theme_snap/feedbacktoggle';
$title = new lang_string('feedbacktoggle', 'theme_snap');
$description = new lang_string('feedbacktoggledesc', 'theme_snap');
$default = $checked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$advancedfeedsdependencies[] = $setting->name;
$snapsettings->add($setting);

// Personal menu messages on/off.
$name = 'theme_snap/messagestoggle';
$title = new lang_string('messagestoggle', 'theme_snap');
$description = new lang_string('messagestoggledesc', 'theme_snap');
$default = $checked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$advancedfeedsdependencies[] = $setting->name;
$snapsettings->add($setting);

// Personal menu forum posts on/off.
$name = 'theme_snap/forumpoststoggle';
$title = new lang_string('forumpoststoggle', 'theme_snap');
$description = new lang_string('forumpoststoggledesc', 'theme_snap');
$default = $checked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$advancedfeedsdependencies[] = $setting->name;
$snapsettings->add($setting);

// Enable advanced PM feeds.
$name = 'theme_snap/personalmenuadvancedfeedsenable';
$title = new lang_string('personalmenuadvancedfeedsenable', 'theme_snap');
$description = new lang_string('personalmenuadvancedfeedsenabledesc', 'theme_snap');
$default = $checked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$snapsettings->add($setting);

$name = 'theme_snap/personalmenuadvancedfeedsperpage';
$title = new lang_string('personalmenuadvancedfeedsperpage', 'theme_snap');
$description = new lang_string('personalmenuadvancedfeedsperpagedesc', 'theme_snap');
$default = '3';
$pmfeedperpagechoices = [
    '3' => '3',
    '4' => '4',
    '5' => '5',
    '6' => '6',
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $pmfeedperpagechoices);
$snapsettings->add($setting);

$name = 'theme_snap/personalmenuadvancedfeedslifetime';
$title = new lang_string('personalmenuadvancedfeedslifetime', 'theme_snap');
$description = new lang_string('personalmenuadvancedfeedslifetimedesc', 'theme_snap');
$default = 30 * MINSECS;
$setting = new admin_setting_configduration($name, $title, $description, $default, MINSECS);
$snapsettings->add($setting);

// Refresh deadlines task settings.
$name = 'theme_snap/refreshdeadlinestasksettingheading';
$title = new lang_string('refreshdeadlinestasksettingheading', 'theme_snap');
$description = '';
$setting = new admin_setting_heading($name, $title, $description);
$snapsettings->add($setting);

$name = 'theme_snap/personalmenurefreshdeadlines';
$title = new lang_string('personalmenurefreshdeadlines', 'theme_snap');
$description = new lang_string('personalmenurefreshdeadlinesdesc', 'theme_snap');
$default = !$checked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$snapsettings->add($setting);

$settings->add($snapsettings);

// Personalmenuadvancedfeedsenable depends on $advancedfeedsdependencie.
// Personalmenuadvancedfeedsperpage and personalmenuadvancedfeedslifetime depends on personalmenuadvancedfeedsenable.
$PAGE->requires->js_call_amd('theme_snap/hide_settings',
    'hideDependingOnChecked',
    array('personalmenuadvancedfeedsenable',
    $advancedfeedsdependencies,
    array('personalmenuadvancedfeedsperpage', 'personalmenuadvancedfeedslifetime')));
