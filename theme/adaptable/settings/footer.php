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

$temp = new admin_settingpage('theme_adaptable_footer', get_string('footersettings', 'theme_adaptable'));
$temp->add(new admin_setting_heading('theme_adaptable_footer', get_string('footersettingsheading', 'theme_adaptable'),
    format_text(get_string('footerdesc', 'theme_adaptable'), FORMAT_MARKDOWN)));

// Show moodle docs link.
$name = 'theme_adaptable/moodledocs';
$title = get_string('moodledocs', 'theme_adaptable');
$description = get_string('moodledocsdesc', 'theme_adaptable');
$default = true;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

$name = 'theme_adaptable/footerblocksplacement';
$title = get_string('footerblocksplacement', 'theme_adaptable');
$description = get_string('footerblocksplacementdesc', 'theme_adaptable');
$choices = array(
    1 => get_string('footerblocksplacement1', 'theme_adaptable'),
    2 => get_string('footerblocksplacement2', 'theme_adaptable'),
    3 => get_string('footerblocksplacement3', 'theme_adaptable'),
);
$setting = new admin_setting_configselect($name, $title, $description, 1, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

// Show Footer blocks.
$name = 'theme_adaptable/showfooterblocks';
$title = get_string('showfooterblocks', 'theme_adaptable');
$description = get_string('showfooterblocksdesc', 'theme_adaptable');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$temp->add($setting);

$totalblocks = 0;
$imgpath = $CFG->wwwroot.'/theme/adaptable/pix/layout-builder/';
$imgblder = '';
for ($i = 1; $i <= 3; $i++) {
    $name = 'theme_adaptable/footerlayoutrow' . $i;
    $title = get_string('footerlayoutrow', 'theme_adaptable');
    $description = get_string('footerlayoutrowdesc', 'theme_adaptable');
    $default = $marketingfooterbuilderdefaults[$i - 1];
    $choices = $bootstrap12;
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $settingname = 'footerlayoutrow' . $i;

    if (!isset($PAGE->theme->settings->$settingname)) {
        $PAGE->theme->settings->$settingname = '0-0-0-0';
    }

    if ($PAGE->theme->settings->$settingname != '0-0-0-0') {
        $imgblder .= '<img src="' . $imgpath . $PAGE->theme->settings->$settingname . '.png' . '" style="padding-top: 5px">';
    }

    $vals = explode('-', $PAGE->theme->settings->$settingname);
    foreach ($vals as $val) {
        if ($val > 0) {
            $totalblocks ++;
        }
    }
}

$temp->add(new admin_setting_heading('theme_adaptable_footerlayoutcheck', get_string('layoutcheck', 'theme_adaptable'),
    format_text(get_string('layoutcheckdesc', 'theme_adaptable'), FORMAT_MARKDOWN)));

$temp->add(new admin_setting_heading('theme_adaptable_footerlayoutbuilder', '', $imgblder));

$blkcontmsg = get_string('layoutaddcontentdesc1', 'theme_adaptable');
$blkcontmsg .= $totalblocks;
$blkcontmsg .= get_string('layoutaddcontentdesc2', 'theme_adaptable');

$temp->add(new admin_setting_heading('theme_adaptable_footerlayoutaddcontent', get_string('layoutaddcontent', 'theme_adaptable'),
    format_text($blkcontmsg, FORMAT_MARKDOWN)));

for ($i = 1; $i <= $totalblocks; $i++) {
    $name = 'theme_adaptable/footer' . $i . 'header';
    $title = get_string('footerheader', 'theme_adaptable') . $i;
    $description = get_string('footerdesc', 'theme_adaptable') . $i;
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    $name = 'theme_adaptable/footer' . $i . 'content';
    $title = get_string('footercontent', 'theme_adaptable') . $i;
    $description = get_string('footercontentdesc', 'theme_adaptable') . $i;
    $default = '';
    $setting = new adaptable_setting_confightmleditor($name, $title, $description, $default);
    $temp->add($setting);
}

// Social icons.
$name = 'theme_adaptable/hidefootersocial';
$title = get_string('hidefootersocial', 'theme_adaptable');
$description = get_string('hidefootersocialdesc', 'theme_adaptable');
$radchoices = array(
    0 => get_string('hide', 'theme_adaptable'),
    1 => get_string('show', 'theme_adaptable'),
);
$setting = new admin_setting_configselect($name, $title, $description, 1, $radchoices);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

// Footnote.
$name = 'theme_adaptable/footnote';
$title = get_string('footnote', 'theme_adaptable');
$description = get_string('footnotedesc', 'theme_adaptable');
$default = '';
$setting = new adaptable_setting_confightmleditor($name, $title, $description, $default);
$temp->add($setting);


$ADMIN->add('theme_adaptable', $temp);
