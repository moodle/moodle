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
 * @package   theme_adaptable
 * @copyright 2015-2016 Jeremy Hopkins (Coventry University)
 * @copyright 2015-2016 Fernando Acedo (3-bits.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die;

// Alert Section.
$temp = new admin_settingpage('theme_adaptable_frontpage_alert', get_string('frontpagealertsettings', 'theme_adaptable'));
$temp->add(new admin_setting_heading('theme_adaptable_alert', get_string('alertsettingsheading', 'theme_adaptable'),
format_text(get_string('alertdesc', 'theme_adaptable'), FORMAT_MARKDOWN)));

// Alert General Settings Heading.
$name = 'theme_adaptable/settingsalertgeneral';
$heading = get_string('alertsettingsgeneral', 'theme_adaptable');
$setting = new admin_setting_heading($name, $heading, '');
$temp->add($setting);

// Enable or disable alerts.
$name = 'theme_adaptable/enablealerts';
$title = get_string('enablealerts', 'theme_adaptable');
$description = get_string('enablealertsdesc', 'theme_adaptable');
$default = false;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

// Disable alert in course pages.
$name = 'theme_adaptable/enablealertcoursepages';
$title = get_string('enablealertcoursepages', 'theme_adaptable');
$description = get_string('enablealertcoursepagesdesc', 'theme_adaptable');
$default = false;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

// Alert hidden course.
$name = 'theme_adaptable/alerthiddencourse';
$title = get_string('alerthiddencourse', 'theme_adaptable');
$description = get_string('alerthiddencoursedesc', 'theme_adaptable');
$default = 'warning';
$choices = array(
'disabled' => get_string('alertdisabled', 'theme_adaptable'),
'info' => get_string('alertinfo', 'theme_adaptable'),
'warning' => get_string('alertwarning', 'theme_adaptable'),
'success' => get_string('alertannounce', 'theme_adaptable'));
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

// Strip Tags.
$name = 'theme_adaptable/enablealertstriptags';
$title = get_string('enablealertstriptags', 'theme_adaptable');
$description = get_string('enablealertstriptagsdesc', 'theme_adaptable');
$default = true;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

// Number of Alerts.
$name = 'theme_adaptable/alertcount';
$title = get_string('alertcount', 'theme_adaptable');
$description = get_string('alertcountdesc', 'theme_adaptable');
$default = THEME_ADAPTABLE_DEFAULT_ALERTCOUNT;
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices1to12);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

$alertcount = get_config('theme_adaptable', 'alertcount');
// If we don't have an an alertcount yet, default to the preset.
if (!$alertcount) {
    $alertcount = THEME_ADAPTABLE_DEFAULT_ALERTCOUNT;
}

for ($alertindex = 1; $alertindex <= $alertcount; $alertindex++) {
    // Alert Box Heading 1.
    $name = 'theme_adaptable/settingsalertbox' . $alertindex;
    $heading = get_string('alertsettings', 'theme_adaptable', $alertindex);
    $setting = new admin_setting_heading($name, $heading, '');
    $temp->add($setting);

    // Enable Alert 1.
    $name = 'theme_adaptable/enablealert' . $alertindex;
    $title = get_string('enablealert', 'theme_adaptable', $alertindex);
    $description = get_string('enablealertdesc', 'theme_adaptable', $alertindex);
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Alert Key.
    $name = 'theme_adaptable/alertkey' . $alertindex;
    $title = get_string('alertkeyvalue', 'theme_adaptable');
    $description = get_string('alertkeyvalue_details', 'theme_adaptable');
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_RAW);
    $temp->add($setting);

    // Alert Text 1.
    $name = 'theme_adaptable/alerttext' . $alertindex;
    $title = get_string('alerttext', 'theme_adaptable');
    $description = get_string('alerttextdesc', 'theme_adaptable');
    $default = '';
    $setting = new adaptable_setting_confightmleditor($name, $title, $description, $default);
    $temp->add($setting);

    // Alert Type 1.
    $name = 'theme_adaptable/alerttype' . $alertindex;
    $title = get_string('alerttype', 'theme_adaptable');
    $description = get_string('alerttypedesc', 'theme_adaptable');
    $default = 'info';
    $choices = array(
    'info' => get_string('alertinfo', 'theme_adaptable'),
    'warning' => get_string('alertwarning', 'theme_adaptable'),
    'success' => get_string('alertannounce', 'theme_adaptable'));
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Alert Access.
    $name = 'theme_adaptable/alertaccess' . $alertindex;
    $title = get_string('alertaccess', 'theme_adaptable');
    $description = get_string('alertaccessdesc', 'theme_adaptable');
    $default = 'global';
    $choices = array(
    'global' => get_string('alertaccessglobal', 'theme_adaptable'),
    'user' => get_string('alertaccessusers', 'theme_adaptable'),
    'admin' => get_string('alertaccessadmins', 'theme_adaptable'),
    'profile' => get_string('alertaccessprofile', 'theme_adaptable'));
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_adaptable/alertprofilefield' . $alertindex;
    $title = get_string('alertprofilefield', 'theme_adaptable');
    $description = get_string('alertprofilefielddesc', 'theme_adaptable');
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_RAW);
    $temp->add($setting);
}

// Colours.
// Alert Course Settings Heading.
$name = 'theme_adaptable/settingsalertcolors';
$heading = get_string('settingscolors', 'theme_adaptable');
$setting = new admin_setting_heading($name, $heading, '');
$temp->add($setting);

// Alert info colours.
$name = 'theme_adaptable/alertcolorinfo';
$title = get_string('alertcolorinfo', 'theme_adaptable');
$description = get_string('alertcolorinfodesc', 'theme_adaptable');
$previewconfig = null;
$setting = new admin_setting_configcolourpicker($name, $title, $description, '#3a87ad', $previewconfig);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

$name = 'theme_adaptable/alertbackgroundcolorinfo';
$title = get_string('alertbackgroundcolorinfo', 'theme_adaptable');
$description = get_string('alertbackgroundcolorinfodesc', 'theme_adaptable');
$previewconfig = null;
$setting = new admin_setting_configcolourpicker($name, $title, $description, '#d9edf7', $previewconfig);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

$name = 'theme_adaptable/alertbordercolorinfo';
$title = get_string('alertbordercolorinfo', 'theme_adaptable');
$description = get_string('alertbordercolorinfodesc', 'theme_adaptable');
$previewconfig = null;
$setting = new admin_setting_configcolourpicker($name, $title, $description, '#bce8f1', $previewconfig);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

$name = 'theme_adaptable/alerticoninfo';
$title = get_string('alerticoninfo', 'theme_adaptable');
$description = get_string('alerticoninfodesc', 'theme_adaptable');
$setting = new admin_setting_configtext($name, $title, $description, 'info-circle');
$temp->add($setting);

// Alert success colours.
$name = 'theme_adaptable/alertcolorsuccess';
$title = get_string('alertcolorsuccess', 'theme_adaptable');
$description = get_string('alertcolorsuccessdesc', 'theme_adaptable');
$previewconfig = null;
$setting = new admin_setting_configcolourpicker($name, $title, $description, '#468847', $previewconfig);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

$name = 'theme_adaptable/alertbackgroundcolorsuccess';
$title = get_string('alertbackgroundcolorsuccess', 'theme_adaptable');
$description = get_string('alertbackgroundcolorsuccessdesc', 'theme_adaptable');
$previewconfig = null;
$setting = new admin_setting_configcolourpicker($name, $title, $description, '#dff0d8', $previewconfig);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

$name = 'theme_adaptable/alertbordercolorsuccess';
$title = get_string('alertbordercolorsuccess', 'theme_adaptable');
$description = get_string('alertbordercolorsuccessdesc', 'theme_adaptable');
$previewconfig = null;
$setting = new admin_setting_configcolourpicker($name, $title, $description, '#d6e9c6', $previewconfig);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

$name = 'theme_adaptable/alerticonsuccess';
$title = get_string('alerticonsuccess', 'theme_adaptable');
$description = get_string('alerticonsuccessdesc', 'theme_adaptable');
$setting = new admin_setting_configtext($name, $title, $description, 'bullhorn');
$temp->add($setting);

// Alert warning colours.
$name = 'theme_adaptable/alertcolorwarning';
$title = get_string('alertcolorwarning', 'theme_adaptable');
$description = get_string('alertcolorwarningdesc', 'theme_adaptable');
$previewconfig = null;
$setting = new admin_setting_configcolourpicker($name, $title, $description, '#8a6d3b', $previewconfig);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

$name = 'theme_adaptable/alertbackgroundcolorwarning';
$title = get_string('alertbackgroundcolorwarning', 'theme_adaptable');
$description = get_string('alertbackgroundcolorwarningdesc', 'theme_adaptable');
$previewconfig = null;
$setting = new admin_setting_configcolourpicker($name, $title, $description, '#fcf8e3', $previewconfig);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

$name = 'theme_adaptable/alertbordercolorwarning';
$title = get_string('alertbordercolorwarning', 'theme_adaptable');
$description = get_string('alertbordercolorwarningdesc', 'theme_adaptable');
$previewconfig = null;
$setting = new admin_setting_configcolourpicker($name, $title, $description, '#fbeed5', $previewconfig);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

$name = 'theme_adaptable/alerticonwarning';
$title = get_string('alerticonwarning', 'theme_adaptable');
$description = get_string('alerticonwarningdesc', 'theme_adaptable');
$setting = new admin_setting_configtext($name, $title, $description, 'exclamation-triangle');
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

$ADMIN->add('theme_adaptable', $temp);
